<?php
session_start();
include('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_bio') {
    $bio = trim($_POST['bio']);
    $username = $_SESSION['username'];
    $sql = "UPDATE users SET bio = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $bio, $username);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
?>
