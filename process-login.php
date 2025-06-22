<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_login_error.log');

// Log the start of the script
error_log("process-login.php started");

require_once 'includes/auth.php';
require_once 'config/database.php';

$conn = getDB();
error_log("Database connection obtained");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Log received data (excluding password)
    error_log("Login attempt for email: " . $email . ", Remember Me: " . ($remember ? 'Yes' : 'No'));

    if (empty($email) || empty($password)) {
        error_log("Validation failed: Email or password empty");
        $_SESSION['error'] = "Please fill in all fields";
        header('Location: login.php');
        exit;
    }

    $user = loginUser($email, $password);
    
    if ($user) {
        error_log("User authenticated successfully. User ID: " . $user['id'] . ", Role: " . $user['role']);
        // Set remember me cookie if requested
        if ($remember) {
            error_log("Remember Me option selected");
            try {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (30 * 24 * 60 * 60); // 30 days
                
                // Store token in database
                $stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))");
                $stmt->execute([$user['id'], $token, $expires]);
                error_log("Remember token stored for user ID: " . $user['id']);
                
                // Set cookie
                setcookie('remember_token', $token, $expires, '/', '', true, true);
                error_log("Remember Me cookie set");

            } catch (PDOException $e) {
                // Log the error but don't stop the login process
                error_log("Remember token database error: " . $e->getMessage());
            }
        }

        // Redirect based on user role
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
        error_log("Login failed: Invalid credentials");
        $_SESSION['error'] = "Invalid email or password";
        header('Location: login.php');
        exit;
    }
} else {
    error_log("Non-POST request received");
    header('Location: login.php');
    exit;
}

// Log if execution reaches here unexpectedly
error_log("process-login.php reached unexpected end.");

?> 