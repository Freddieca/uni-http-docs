<?php
// Start session and include the database connection
session_start();
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "Please log in to see your posts.";
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

// Fetch posts made by this user
$sql = "SELECT posts.post_id, posts.title, posts.description, posts.image, posts.location, posts.created_at, users.username, users.profile_picture FROM posts JOIN users ON posts.user_id = users.user_id WHERE posts.user_id = ? ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($post_id, $title, $description, $image, $location, $created_at, $username, $profile_picture);

if ($stmt->num_rows > 0) {
    while ($stmt->fetch()) {
        echo "<div class='post'>";
        echo "<p>";
        if (!empty($profile_picture)) {
            echo "<img src='../uploads/" . htmlspecialchars($profile_picture) . "' alt='Profile Picture' style='width: 50px; height: 50px; border-radius: 50%;'>";
        } else {
            echo "<img src='../assets/images/default-profile.png' alt='Default Profile Picture' style='width: 50px; height: 50px; border-radius: 50%;'>";
        }
        echo "<strong>" . htmlspecialchars($title) . "</strong> by " . htmlspecialchars($username) . "</p>";
        echo "<p>" . htmlspecialchars($description) . "</p>";
        if ($image) {
            echo "<img src='../uploads/" . htmlspecialchars($image) . "' alt='Post Image' style='width: 100px; height: 100px;'>";
        }
        if ($location) {
            echo "<p><strong>Location:</strong> " . htmlspecialchars($location) . "</p>";
        }
        echo "<p><small>Posted on: " . htmlspecialchars($created_at) . "</small></p>";
        echo "<button onclick='deletePost(" . htmlspecialchars($post_id) . ")'>Delete</button>";
        echo "</div>";
    }
} else {
    echo "No posts available.";
}

$stmt->close();
$conn->close();
?>
<script>
function deletePost(postId) {
    if (confirm("Are you sure you want to delete this post?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../php/delete_post.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert("Post deleted successfully!");
                    loadUserPosts();
                } else {
                    alert("Failed to delete post: " + response.message);
                }
            }
        };
        xhr.send("post_id=" + postId);
    }
}
</script>