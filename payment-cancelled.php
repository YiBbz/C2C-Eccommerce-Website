<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$booking_id = $_GET['custom_str1'] ?? '';

if ($booking_id) {
    $_SESSION['error'] = "Payment was cancelled. You can try again later.";
    header('Location: messages.php?booking_id=' . $booking_id);
} else {
    header('Location: messages.php');
}
exit; 