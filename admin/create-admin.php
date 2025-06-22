<?php
require_once '../config/database.php';

$conn = getDB();

// Admin user details
$username = 'admin';
$email = 'admin@example.com';
$password = password_hash('admin123', PASSWORD_DEFAULT); // Default password: admin123
$role = 'admin';
$full_name = 'System Administrator';

try {
    // Check if admin user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $stmt->execute([':username' => $username, ':email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        echo "Admin user already exists!";
    } else {
        // Insert admin user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, full_name) 
                               VALUES (:username, :email, :password, :role, :full_name)");
        
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role,
            ':full_name' => $full_name
        ]);
        
        echo "Admin user created successfully!<br>";
        echo "Username: " . $username . "<br>";
        echo "Password: admin123<br>";
        echo "Please change the password after first login.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 