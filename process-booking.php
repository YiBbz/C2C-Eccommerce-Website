<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Log the start of the script
error_log("process-booking.php started");

// Ensure the user is logged in
requireLogin();

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    header('Location: index.php');
    exit();
}

// Log POST data
error_log("POST data received: " . print_r($_POST, true));

$user_id = $_SESSION['user_id'];
$service_id = $_POST['service_id'] ?? null;
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;
$payment_method = $_POST['payment_method'] ?? null;
$notes = $_POST['notes'] ?? '';

// Basic validation
if (empty($service_id) || empty($date) || empty($time) || empty($payment_method)) {
    error_log("Validation failed: Missing required fields");
    $_SESSION['error_message'] = 'Missing required booking information.';
    header('Location: service-details.php?id=' . $service_id);
    exit();
}

try {
    $conn = getDB();
    error_log("Database connection obtained");
    
    // Get service details including price and provider_id
    $stmt_service = $conn->prepare("SELECT s.*, u.full_name as provider_name 
                                   FROM services s 
                                   JOIN users u ON s.provider_id = u.id 
                                   WHERE s.id = ?");
    $stmt_service->execute([$service_id]);
    $service = $stmt_service->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        error_log("Service not found for ID: " . $service_id);
        $_SESSION['error_message'] = 'Service not found.';
        header('Location: index.php');
        exit();
    }
    
    $provider_id = $service['provider_id'];
    $total_amount = $service['price'];
    
    // Combine date and time into a single datetime
    $booking_date = date('Y-m-d H:i:s', strtotime("$date $time"));
    error_log("Booking date formatted: " . $booking_date);

    // Insert the booking into the database
    $stmt = $conn->prepare("INSERT INTO bookings (customer_id, provider_id, service_id, booking_date, total_amount, payment_method, status, notes) 
                           VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)");
    
    if ($stmt->execute([$user_id, $provider_id, $service_id, $booking_date, $total_amount, $payment_method, $notes])) {
        $booking_id = $conn->lastInsertId();
        error_log("Booking created successfully with ID: " . $booking_id);
        
        // Create initial message about the booking
        $message = "New booking request for {$service['title']}:\n";
        $message .= "Date: " . date('F j, Y', strtotime($date)) . "\n";
        $message .= "Time: " . date('g:i A', strtotime($time)) . "\n";
        $message .= "Payment Method: " . ucfirst($payment_method) . "\n";
        if (!empty($notes)) {
            $message .= "Additional Notes: " . $notes;
        }
        
        // Insert the message
        $stmt_msg = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, booking_id, message) VALUES (?, ?, ?, ?)");
        $stmt_msg->execute([$user_id, $provider_id, $booking_id, $message]);
        error_log("Initial message created for booking ID: " . $booking_id);
        
        $_SESSION['success_message'] = 'Booking request sent successfully!';
        error_log("Redirecting to messages page with booking_id: " . $booking_id);
        
        // Determine the correct messages page based on user role
        $messages_page = $_SESSION['role'] === 'service_provider' ? 'provider-messages.php' : 'customer-messages.php';
        $redirect_url = '/demo2/' . $messages_page . '?booking_id=' . $booking_id;
        error_log("Redirect URL: " . $redirect_url);
        header('Location: ' . $redirect_url);
        exit();
    } else {
        error_log("Failed to create booking");
        $_SESSION['error_message'] = 'Failed to place booking. Please try again.';
        header('Location: service-details.php?id=' . $service_id);
        exit();
    }

} catch (PDOException $e) {
    error_log("Database error in process-booking.php: " . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while processing your booking. Please try again later.';
    header('Location: service-details.php?id=' . $service_id);
    exit();
}

// Redirect if somehow execution reaches here
error_log("Unexpected end of script reached");
header('Location: index.php');
exit();
?> 