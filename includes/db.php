<?php
// db.php

$host = 'localhost';
$dbname = 'almas_clothing';  // The name of the database you created
$username = 'root';          // Default XAMPP MySQL username
$password = '';              // Default XAMPP MySQL password is empty

$conn = null;

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Only display error in development, not in production
    if (php_sapi_name() !== 'cli') {
        // For web requests, log the error but don't display it
        error_log("Database Connection failed: " . $e->getMessage());
    } else {
        // For CLI/direct access, show the error
        echo "Connection failed: " . $e->getMessage();
    }
}
?>
