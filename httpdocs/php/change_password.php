<?php
session_start();
include('../includes/db_connection.php');

// Check if the user is an admin
if (!isset($_SESSION['admin_username'])) {
    echo "Access denied.";
    exit();
}

// Get the user ID and new password from the POST request
$user_id = $_POST['user_id'];
$new_password = hash('sha512', $_POST['new_password']);

// Update the user's password in the database
$sql = "UPDATE users SET password = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_password, $user_id);
if ($stmt->execute()) {
    echo "Password changed successfully.";
} else {
    echo "Failed to change password: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>