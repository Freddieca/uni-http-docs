<?php
session_start();
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to comment on posts.']);
    exit();
}

// Get the post ID and comment from the POST request
$post_id = $_POST['post_id'];
$comment = trim($_POST['comment']);
$username = $_SESSION['username'];

// Fetch existing comments
$sql = "SELECT comments FROM posts WHERE post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($comments_json);
$stmt->fetch();
$stmt->close();

$comments = json_decode($comments_json, true);
if (!$comments) {
    $comments = [];
}

// Add new comment
$comments[] = [
    'username' => $username,
    'comment' => $comment,
    'created_at' => date('Y-m-d H:i:s')
];

// Update comments in the database
$comments_json = json_encode($comments);
$sql = "UPDATE posts SET comments = ? WHERE post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $comments_json, $post_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'comment' => htmlspecialchars($comment)]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add comment: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>