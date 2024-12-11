<?php
session_start();
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "Please log in to see your messages.";
    exit();
}

// Fetch messages from the database
$sql = "SELECT sender, message, created_at FROM messages ORDER BY created_at ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p><strong>" . htmlspecialchars($row['sender']) . ":</strong> " . htmlspecialchars($row['message']) . " <small>(" . htmlspecialchars($row['created_at']) . ")</small></p>";
    }
} else {
    echo "<p>No messages available.</p>";
}

$conn->close();
?>