<?php
session_start(); // Start the session to manage user login state

// Include database connection file
include '../includes/db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: pages/login.html");
    exit();
}

// User is logged in; check user role from the database
$userId = $_SESSION['user_id'];

// Function to get user role
function getUserRole($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();
    return $role;
}

// Get user role
$userRole = getUserRole($userId);

// Redirect based on user role
if ($userRole === 'admin') {
    header("Location: pages/admin.php");
} else {
    header("Location: pages/home.php");
}
exit();
?>