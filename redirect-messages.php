<?php
require_once 'includes/auth.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get the current user's role
$role = $_SESSION['role'];

// Preserve query parameters
$query_string = $_SERVER['QUERY_STRING'];
$redirect_url = '';

// Redirect based on role
switch ($role) {
    case 'customer':
        $redirect_url = 'customer-messages.php';
        break;
    case 'service_provider':
        $redirect_url = 'provider-messages.php';
        break;
    case 'admin':
        // Admins can access both views, default to customer view
        $redirect_url = 'customer-messages.php';
        break;
    default:
        // Unknown role, redirect to home
        header('Location: index.php');
        exit;
}

// Append query string if it exists
if (!empty($query_string)) {
    $redirect_url .= '?' . $query_string;
}

header('Location: ' . $redirect_url);
exit; 