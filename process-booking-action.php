<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'config/pusher.php';

if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$booking_id = isset($data['booking_id']) ? intval($data['booking_id']) : 0;
$action = isset($data['action']) ? $data['action'] : '';

if (!$booking_id || !in_array($action, ['accepted', 'rejected'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $conn = getDB();
    
    // Start transaction
    $conn->beginTransaction();
    
    // Get booking details
    $stmt = $conn->prepare("
        SELECT b.*, s.title as service_title, s.price as service_price,
               cust.full_name as customer_name, cust.id as customer_id,
               prov.full_name as provider_name, prov.id as provider_id
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users cust ON b.customer_id = cust.id
        JOIN users prov ON b.provider_id = prov.id
        WHERE b.id = :booking_id AND b.provider_id = :user_id
    ");
    $stmt->execute([':booking_id' => $booking_id, ':user_id' => $_SESSION['user_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        throw new Exception('Booking not found or unauthorized');
    }
    
    if ($booking['status'] !== 'pending') {
        throw new Exception('Booking is not in pending status');
    }
    
    // Update booking status
    $stmt = $conn->prepare("
        UPDATE bookings 
        SET status = :status,
            updated_at = NOW()
        WHERE id = :booking_id AND provider_id = :user_id
    ");
    $stmt->execute([
        ':status' => $action === 'accepted' ? 'accepted' : 'cancelled',
        ':booking_id' => $booking_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    
    // Add system message
    $message = $action === 'accepted' 
        ? "Service provider has accepted your booking request. The booking is now accepted."
        : "Service provider has declined your booking request. The booking has been cancelled.";
    
    $stmt = $conn->prepare("
        INSERT INTO messages (booking_id, sender_id, receiver_id, message)
        VALUES (:booking_id, :sender_id, :receiver_id, :message)
    ");
    $stmt->execute([
        ':booking_id' => $booking_id,
        ':sender_id' => $_SESSION['user_id'],
        ':receiver_id' => $booking['customer_id'],
        ':message' => $message
    ]);
    
    // Commit transaction
    $conn->commit();
    
    // Send Pusher notification
    $pusher = new Pusher\Pusher(
        PUSHER_APP_KEY,
        PUSHER_APP_SECRET,
        PUSHER_APP_ID,
        [
            'cluster' => PUSHER_APP_CLUSTER,
            'useTLS' => true
        ]
    );
    
    $notification = [
        'type' => 'booking_update',
        'booking_id' => $booking_id,
        'status' => $action === 'accepted' ? 'accepted' : 'cancelled',
        'message' => $message
    ];
    
    // Notify provider
    $pusher->trigger('chat-' . $booking['provider_id'], 'booking-update', $notification);
    
    // Notify customer
    $pusher->trigger('chat-' . $_SESSION['user_id'], 'booking-update', $notification);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 