<?php
// db.php - Database connection file

$host = '127.0.0.1';      // Changed from 'localhost' to fix the slow loading bug
$dbname = 'maintenance_db'; // The database we just created
$username = 'root';       // Default XAMPP/WAMP username
$password = '';           // Default XAMPP/WAMP password (usually blank)

// Create the connection using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection failed
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Optional: Set character set to utf8 for proper text encoding
$conn->set_charset("utf8");
?>