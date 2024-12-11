<?php
session_start();
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "Please log in to send messages.";
    exit();
}

// Get the logged-in user's username
$username = $_SESSION['username'];

// Get the message from the POST request
$message = trim($_POST['message']);

// Insert the message into the database
$sql = "INSERT INTO messages (sender, message, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $message);
$stmt->execute();
$stmt->close();

$conn->close();
?>