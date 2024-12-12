<?php
session_start();
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to like posts.']);
    exit();
}

// Get the post ID from the POST request
$post_id = $_POST['post_id'];

// Increment the like count in the database
$sql = "UPDATE posts SET likes = likes + 1 WHERE post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
if ($stmt->execute()) {
    // Get the updated like count
    $sql = "SELECT likes FROM posts WHERE post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->bind_result($like_count);
    $stmt->fetch();
    echo json_encode(['success' => true, 'like_count' => $like_count]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to like post: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>