<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_register_error.log');

// Log the start of the script
error_log("process-register.php started");

require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

$conn = getDB();
error_log("Database connection obtained");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    $username = $_POST['username'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'customer'; // Default role to customer

    // Log received data (excluding password)
    error_log("Registration attempt for username: " . $username . ", email: " . $email . ", role: " . $role);

    if (empty($username) || empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        error_log("Validation failed: Missing required fields");
        $_SESSION['error'] = "Please fill in all fields";
        header('Location: register.php');
        exit;
    }

    if ($password !== $confirm_password) {
        error_log("Validation failed: Passwords do not match");
        $_SESSION['error'] = "Passwords do not match";
        header('Location: register.php');
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Validation failed: Invalid email format");
        $_SESSION['error'] = "Invalid email format";
        header('Location: register.php');
        exit;
    }

    // Validate password strength
    if (strlen($password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters long";
        header('Location: register.php');
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Email already registered";
        header('Location: register.php');
        exit;
    }

    // Additional validation for service providers
    // Removed requirement for business name and description

    // Register the user
    $registration_result = registerUser($username, $full_name, $email, $password, $role);

    if ($registration_result['success']) {
        error_log("User registration successful for email: " . $email);
        // Log the user in automatically after successful registration
        $user = loginUser($username, $password);
        
        if ($user) {
            error_log("Automatic login successful for username: " . $username);
            $_SESSION['success_message'] = "Registration successful! You are now logged in.";
            // Redirect based on role, similar to process-login.php
            if ($user['role'] === 'admin') {
                $redirect_url = '/demo2/admin/index.php';
                error_log("Redirecting Admin to: " . $redirect_url);
                header('Location: ' . $redirect_url);
            } else {
                $redirect_url = '/demo2/dashboard.php';
                error_log("Redirecting User to: " . $redirect_url);
                header('Location: ' . $redirect_url);
            }
            exit;
        } else {
            error_log("Automatic login failed after registration for username: " . $username);
            // Registration was successful, but automatic login failed
            $_SESSION['success_message'] = "Registration successful! Please log in.";
            header('Location: login.php');
            exit;
        }
    } else {
        error_log("User registration failed: " . $registration_result['message']);
        $_SESSION['error'] = $registration_result['message'];
        header('Location: register.php');
        exit;
    }
} else {
    error_log("Non-POST request received");
    header('Location: register.php');
    exit;
}

// Log if execution reaches here unexpectedly
error_log("process-register.php reached unexpected end.");

?> 