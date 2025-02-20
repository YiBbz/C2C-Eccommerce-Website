<?php
session_start();

define('BASE_URL', 'http://localhost/ikhwezi');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ikhwezi/uploads/');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to redirect with message
function redirect($location, $message = '', $type = 'info') {
    if($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: " . BASE_URL . "/$location");
    exit();
}