<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT cart.id, products.name, products.price, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = $user_id");

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<h2>" . $row['name'] . "</h2>";
    echo "<p>Price: $" . $row['price'] . "</p>";
    echo "<p>Quantity: " . $row['quantity'] . "</p>";
    echo "</div>";
}
?>