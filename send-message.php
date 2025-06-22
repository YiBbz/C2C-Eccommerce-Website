<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_send_message_error.log');

// Log the start of the script
error_log("send-message.php started");

require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'config/pusher.php';

header('Content-Type: application/json');

// Ensure user is logged in
if (!isLoggedIn()) {
    error_log("User not logged in.");
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure it's a POST request with JSON body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || json_last_error() !== JSON_ERROR_NONE) {
    error_log("Invalid request: Not POST or invalid JSON. Method: " . $_SERVER['REQUEST_METHOD'] . ", JSON Error: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

$receiver_id = $data['receiver_id'] ?? null;
$message_content = $data['message'] ?? null;
$booking_id = $data['booking_id'] ?? null; // Get booking_id

// Log received data
error_log("Received message data: Receiver ID = " . $receiver_id . ", Booking ID = " . $booking_id . ", Message = " . substr($message_content, 0, 50) . "...");

// Validate input
if (empty($receiver_id) || empty($message_content)) {
    error_log("Validation failed: Missing receiver_id or message.");
    echo json_encode(['success' => false, 'message' => 'Missing receiver or message content.']);
    exit();
}

$conn = getDB();
error_log("Database connection obtained.");

try {
    // Verify booking exists and user is part of it
    $stmt = $conn->prepare("
        SELECT * FROM bookings 
        WHERE id = :booking_id 
        AND (customer_id = :user_id OR provider_id = :user_id)
    ");
    $stmt->execute([
        ':booking_id' => $booking_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        throw new Exception('Booking not found or unauthorized');
    }
    
    // Verify receiver is part of the booking
    if ($receiver_id != $booking['customer_id'] && $receiver_id != $booking['provider_id']) {
        throw new Exception('Invalid receiver');
    }
    
    // Insert message
    $stmt = $conn->prepare("
        INSERT INTO messages (booking_id, sender_id, receiver_id, message, created_at)
        VALUES (:booking_id, :sender_id, :receiver_id, :message, NOW())
    ");
    $stmt->execute([
        ':booking_id' => $booking_id,
        ':sender_id' => $_SESSION['user_id'],
        ':receiver_id' => $receiver_id,
        ':message' => $message_content
    ]);
    
    $message_id = $conn->lastInsertId();
    
    // Get sender details for the response
    $stmt = $conn->prepare("
        SELECT full_name, profile_picture 
        FROM users 
        WHERE id = :user_id
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $sender = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Prepare message data for Pusher
    $message_data = [
        'id' => $message_id,
        'booking_id' => $booking_id,
        'sender_id' => $_SESSION['user_id'],
        'receiver_id' => $receiver_id,
        'message' => $message_content,
        'created_at' => date('Y-m-d H:i:s'),
        'sender_name' => $sender['full_name'] ?? null,
        'sender_image' => $sender['profile_picture'] ?? null
    ];
    
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
    
    // Notify receiver
    $pusher->trigger('chat-' . $receiver_id, 'new-message', $message_data);
    
    // Also notify sender (for real-time updates in their own chat)
    $pusher->trigger('chat-' . $_SESSION['user_id'], 'new-message', $message_data);
    
    echo json_encode([
        'success' => true,
        'message' => $message_data
    ]);

} catch (PDOException $e) {
    error_log("Database error in send-message.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
} catch (Exception $e) {
    error_log("General error in send-message.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Log if execution reaches here unexpectedly
error_log("send-message.php reached unexpected end.");

?> 