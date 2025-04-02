<?php
// Database Configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'servicehub');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8");

// Session start
session_start();

// Initialize cart if not exists
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Website settings
$settings = array(
    'site_name' => 'ServiceHub',
    'site_tagline' => 'Find Professional Services Online',
    'contact_email' => 'info@servicehub.com',
    'contact_phone' => '+1 (555) 123-4567',
    'address' => '123 Main Street, Suite 100, New York, NY 10001',
    'currency' => '$',
    'tax_rate' => 0.08 // 8% tax rate
);

// Function to sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate random string
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $random_string;
}

// Function to format price
function format_price($price) {
    global $settings;
    return $settings['currency'] . number_format($price, 2);
}

// Function to calculate total cart price
function calculate_cart_total() {
    $total = 0;
    foreach($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Function to get cart count
function get_cart_count() {
    $count = 0;
    if(isset($_SESSION['cart'])) {
        foreach($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is a service provider
function is_provider() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'provider';
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Function to display alert messages
function display_message() {
    if(isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'];
        
        echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Function to set message
function set_message($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}
?>