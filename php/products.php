<?php
include 'config.php';

$result = $conn->query("SELECT * FROM products");

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<h2>" . $row['name'] . "</h2>";
    echo "<p>" . $row['description'] . "</p>";
    echo "<p>Price: $" . $row['price'] . "</p>";
    echo "<img src='../images/" . $row['image'] . "' alt='" . $row['name'] . "'>";
    echo "</div>";
}
?>