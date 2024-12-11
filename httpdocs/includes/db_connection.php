<?php
// Database connection settings
$servername = "localhost"; // Typically 'localhost' in Plesk
$username = "fred"; // Replace with your database username
$password = "s4413770!"; // Replace with your database password
$dbname = "s4413770"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
