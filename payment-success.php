<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'config/payfast.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$conn = getDB();
$payment_id = $_GET['m_payment_id'] ?? '';
$booking_id = $_GET['custom_str1'] ?? '';

try {
    // Verify the payment with PayFast
    $pfData = $_POST;
    $pfData['passphrase'] = PAYFAST_PASSPHRASE;
    
    // Generate signature
    $signature = md5(implode('', $pfData));
    
    if ($signature === $_POST['signature']) {
        // Payment is valid
        $stmt = $conn->prepare("
            UPDATE bookings 
            SET payment_status = 'paid', 
                payment_date = NOW(),
                payment_id = ?
            WHERE id = ? AND customer_id = ?
        ");
        $stmt->execute([$payment_id, $booking_id, $_SESSION['user_id']]);
        
        $_SESSION['success'] = "Payment successful! Your booking has been confirmed.";
    } else {
        throw new Exception("Invalid payment signature");
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Payment verification failed: " . $e->getMessage();
}

// Redirect back to messages
header('Location: messages.php?booking_id=' . $booking_id);
exit; 