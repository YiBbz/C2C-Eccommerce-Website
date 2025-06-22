<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'demo');

// Function to get the database connection
function getDB() {
    static $conn = null;

    if ($conn === null) {
        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set default fetch mode to associative array
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // In a real application, you would log this error
            // For now, we'll stop execution and display the error
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    return $conn;
}
?> 