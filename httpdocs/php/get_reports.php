<?php
session_start();
include('../includes/db_connection.php');

// Check if the user is an admin
if (!isset($_SESSION['admin_username'])) {
    echo "Access denied.";
    exit();
}

// Fetch reports from the database
$sql = "SELECT reporter_id, reported_user_id, reason, created_at FROM reports ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p><strong>Reporter ID:</strong> " . htmlspecialchars($row['reporter_id']) . "<br>";
        echo "<strong>Reported User ID:</strong> " . htmlspecialchars($row['reported_user_id']) . "<br>";
        echo "<strong>Reason:</strong> " . htmlspecialchars($row['reason']) . "<br>";
        echo "<strong>Reported on:</strong> " . htmlspecialchars($row['created_at']) . "</p>";
        echo "<button onclick='takeAction(" . htmlspecialchars($row['reported_user_id']) . ")'>Take Action</button>";
    }
} else {
    echo "<p>No reports available.</p>";
}

$conn->close();
?>