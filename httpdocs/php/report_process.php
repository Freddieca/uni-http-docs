<?php
session_start();
include '../includes/db_connection.php';

if (isset($_SESSION['user_id'])) {
    $reporter_id = $_SESSION['user_id'];
    $reported_user_id = $_POST['reported_user_id'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO reports (reporter_id, reported_user_id, reason) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $reporter_id, $reported_user_id, $reason);
    $stmt->execute();

    echo "Report submitted successfully.";
}
?>
