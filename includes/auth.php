<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

// Initialize database connection
$conn = getDB();

function registerUser($username, $full_name, $email, $password, $role) {
    global $conn;
    
    try {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $full_name, $email, $hashedPassword, $role]);
        
        return ['success' => true, 'message' => 'Registration successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

function loginUser($username, $password) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['profile_image'] = $user['profile_picture'] ?? null;
            
            return $user; // Return the user data directly
        }
        
        return false; // Return false if login fails
    } catch (PDOException $e) {
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logoutUser() {
    session_destroy();
    return ['success' => true, 'message' => 'Logged out successfully'];
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT id, username, email, full_name, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isServiceProvider() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'service_provider';
}

function isCustomer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'customer';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /demo/login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: /demo/index.php");
        exit();
    }
}

function requireServiceProvider() {
    requireLogin();
    if (!isServiceProvider()) {
        header("Location: /demo/index.php");
        exit();
    }
}

function requireCustomer() {
    requireLogin();
    if (!isCustomer()) {
        header("Location: /demo/index.php");
        exit();
    }
} 