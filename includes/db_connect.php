<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "valley_view_uni"; // Database name

try {
    // Create connection using PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Uncomment the line below for debugging purposes
    // echo "Connected successfully";
} catch(PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}

// Function to get database connection
function getDB() {
    global $pdo;
    return $pdo;
}
?>