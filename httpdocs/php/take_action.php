<?php
session_start();
include('../includes/db_connection.php');

// Check if the user is an admin
if (!isset($_SESSION['admin_username'])) {
    echo "Access denied.";
    exit();
}

// Get the reported user ID and action from the POST request
$reported_user_id = $_POST['reported_user_id'];
$action = $_POST['action'];

// Perform the action (e.g., block user for 30 days)
if ($action === 'block') {
    $sql = "UPDATE users SET blocked_until = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reported_user_id);
    $stmt->execute();
    $stmt->close();
    echo "User blocked for 30 days.";
} elseif ($action === 'request_id') {
    // Request identity document (implementation depends on your requirements)
    echo "Identity document requested.";
}

$conn->close();
?>