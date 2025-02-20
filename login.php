<?php
session_start();
require_once "config/database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $user->login($username, $password);
    
    if($result) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['user_type'] = $result['user_type'];
        
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
