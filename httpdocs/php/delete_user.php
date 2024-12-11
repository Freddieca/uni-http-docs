<?php
session_start();
include('../includes/db_connection.php');

// Check if the user is an admin
if (!isset($_SESSION['admin_username'])) {
    echo "Access denied.";
    exit();
}

// Get the user ID from the POST request
$user_id = $_POST['user_id'];

// Delete the user from the database
$sql = "DELETE FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    echo "User removed successfully.";
} else {
    echo "Failed to remove user: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>