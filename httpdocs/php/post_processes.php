<?php
// Start session
session_start();
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to post.']);
    exit();
}

// Get the logged-in user's username
$username = $_SESSION['username'];

// Fetch the user_id from the database
$sql = "SELECT user_id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Process the uploaded file
$image = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_name = basename($_FILES['image']['name']);
    $image_target = "../uploads/" . $image_name;

    // Check if the file is an image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        echo json_encode(['success' => false, 'message' => 'Uploaded file is not an image.']);
        exit();
    }

    // Move the file to the uploads directory
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_target)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
        exit();
    }
    $image = $image_name;
}

// Get post title, description, and location
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';

// Insert the post into the database
if (empty($title) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Title and description are required.']);
    exit();
}

$sql = "INSERT INTO posts (user_id, title, description, image, location, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $user_id, $title, $description, $image, $location);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create post: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>