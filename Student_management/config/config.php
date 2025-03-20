<?php
// Database configuration
$host = 'localhost';  // XAMPP runs MySQL on localhost
$dbname = 'student_management'; // Your database name
$username = 'root'; // Default XAMPP MySQL username
$password = ''; // Default XAMPP has no password

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Display error message and stop execution if connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>
