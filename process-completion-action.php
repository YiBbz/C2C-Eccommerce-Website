<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'config/pusher.php';

if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$conn = getDB();
$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'] ?? 0;
$action = $data['action'] ?? '';

try {
    // Get booking details
    $stmt = $conn->prepare("
        SELECT b.*, s.title as service_title, 
               cust.full_name as customer_name, cust.id as customer_id,
               prov.full_name as provider_name, prov.id as provider_id
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users cust ON b.customer_id = cust.id
        JOIN users prov ON b.provider_id = prov.id
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Booking not found');
    }

    // Verify user has permission to perform action
    $is_provider = $booking['provider_id'] == $_SESSION['user_id'];

    if (!$is_provider) {
        throw new Exception('Only provider can update completion status');
    }

    if ($booking['status'] !== 'accepted') {
        throw new Exception('Can only update completion status for accepted bookings');
    }

    switch ($action) {
        case 'in_progress':
            if ($booking['completion_status'] !== 'not_started') {
                throw new Exception('Can only start services that have not been started');
            }
            
            $stmt = $conn->prepare("UPDATE bookings SET completion_status = 'in_progress' WHERE id = ?");
            $stmt->execute([$booking_id]);
            
            // Send notification message
            $message = "I have started working on your booking for {$booking['service_title']}.";
            $stmt = $conn->prepare("
                INSERT INTO messages (sender_id, receiver_id, message, booking_id)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$booking['provider_id'], $booking['customer_id'], $message, $booking_id]);
            break;

        case 'completed':
            if ($booking['completion_status'] !== 'in_progress') {
                throw new Exception('Can only complete services that are in progress');
            }
            
            $stmt = $conn->prepare("UPDATE bookings SET completion_status = 'completed' WHERE id = ?");
            $stmt->execute([$booking_id]);
            
            // Send notification message
            $message = "I have completed your booking for {$booking['service_title']}.";
            $stmt = $conn->prepare("
                INSERT INTO messages (sender_id, receiver_id, message, booking_id)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$booking['provider_id'], $booking['customer_id'], $message, $booking_id]);
            break;

        default:
            throw new Exception('Invalid action');
    }

    // Trigger Pusher event for real-time updates
    $pusher = new Pusher\Pusher(
        PUSHER_APP_KEY,
        PUSHER_APP_SECRET,
        PUSHER_APP_ID,
        ['cluster' => PUSHER_APP_CLUSTER]
    );

    $pusher->trigger('chat-' . $booking['customer_id'], 'booking-updated', [
        'booking_id' => $booking_id,
        'completion_status' => $action,
        'message' => $message ?? null
    ]);

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 