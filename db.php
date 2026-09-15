<?php
// db.php - Database connection using MySQLi (object-oriented)
$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'admission_db';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
