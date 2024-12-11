<?php
// Start the session
session_start();

// Include the database connection file
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}

// Fetch the logged-in user's details
$username = $_SESSION['username'];

// Initialize variables for bio, profile picture, and update status
$bio = '';
$profile_picture = '';

// Fetch the user's bio and profile picture from the database
$sql = "SELECT bio, profile_picture FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($bio, $profile_picture);
$stmt->fetch();
$stmt->close();

// Check if the form is submitted to update the profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    // Get the updated bio and username from the form
    $new_bio = trim($_POST['bio']);
    $new_username = trim($_POST['username']);

    // Update the user's bio and username in the database
    $sql = "UPDATE users SET bio = ?, username = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("sss", $new_bio, $new_username, $username);
    $stmt->execute();
    $stmt->close();

    // Update the session username
    $_SESSION['username'] = $new_username;

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $profile_picture_name = uniqid("profile_", true) . "." . strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $profile_picture_target = "../uploads/" . $profile_picture_name;

        // Check if the file is an image and is either .jpg or .png
        $file_type = strtolower(pathinfo($profile_picture_target, PATHINFO_EXTENSION));
        $allowed_types = array('jpg', 'jpeg', 'png');
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_target)) {
                // Update the profile picture in the database
                $sql = "UPDATE users SET profile_picture = ? WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $profile_picture_name, $new_username);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Failed to upload the profile picture.";
            }
        } else {
            echo "Only JPG and PNG files are allowed.";
        }
    }

    // Set session variable to show alert
    $_SESSION['profile_updated'] = true;

    // Redirect to avoid form resubmission
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Fred's Free Speech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #profile-edit-form, #new-post-form {
            display: none;
        }
        #edit-profile-btn, #create-post-btn {
            background-color: navy;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        #edit-profile-btn:hover, #create-post-btn:hover {
            background-color: darkblue;
        }
        #map {
            height: 400px;
            width: 100%;
        }
    </style> 
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZlCp0Zt62EittcZsPueFGo-QRwRDQBcE&libraries=maps,marker&v=beta" defer></script>
    <script>
        let map, marker;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: -34.397, lng: 150.644 },
                zoom: 8
            });

            map.addListener('click', function (event) {
                placeMarker(event.latLng);
            });
        }

        function placeMarker(location) {
            if (marker) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    map: map
                });
            }
            document.getElementById('location').value = location.lat() + ',' + location.lng();
        }

        function toggleProfileEditForm() {
            var form = document.getElementById("profile-edit-form");
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }

        function toggleNewPostForm() {
            var form = document.getElementById("new-post-form");
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }

        function loadUserPosts() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../php/get_user_posts.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("user-posts").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function submitPostForm(event) {
            event.preventDefault();
            var form = document.getElementById("new-post-form");
            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", form.action, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Post created successfully!");
                        form.reset();
                        toggleNewPostForm();
                        loadUserPosts();
                    } else {
                        alert("Failed to create post: " + response.message);
                    }
                }
            };
            xhr.send(formData);
        }

        // Load posts when page loads
        document.addEventListener("DOMContentLoaded", loadUserPosts);

        // Display alert if profile was updated
        <?php if (isset($_SESSION['profile_updated']) && $_SESSION['profile_updated'] === true): ?>
            alert("Profile updated successfully!");
            <?php unset($_SESSION['profile_updated']); ?>
        <?php endif; ?>
    </script>
</head>
<body>
<?php include '../includes/header.php'; ?>
    <main>
        <h2>Profile</h2>
        <div class="profile-info">
            <?php if (!empty($profile_picture)) : ?>
                <img src="../uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" style="width: 100px; height: 100px;">
            <?php else : ?>
                <img src="../assets/images/default-profile.png" alt="Default Profile Picture" style="width: 100px; height: 100px;">
            <?php endif; ?>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Bio:</strong> <?php echo htmlspecialchars($bio); ?></p>
        </div>
        <button id="edit-profile-btn" onclick="toggleProfileEditForm()">Edit Profile</button>
        <form id="profile-edit-form" method="POST" action="profile.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_profile">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" rows="4" cols="50"><?php echo htmlspecialchars($bio); ?></textarea>
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" accept=".jpg, .jpeg, .png">
            <button type="submit">Update Profile</button>
        </form>

        <h2>Your Posts</h2>
        <button id="create-post-btn" onclick="toggleNewPostForm()">Create New Post</button>
        <form id="new-post-form" method="POST" action="../php/post_processes.php" enctype="multipart/form-data" onsubmit="return submitPostForm(event)">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" cols="50" required></textarea>
            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" readonly>
            <div id="map"></div>
            <button type="submit">Create Post</button>
        </form>
        <div id="user-posts"></div>
    </main>
    <footer>
        <p>&copy; 2024 Fred's Free Speech Platform</p>
    </footer>
</body>
</html>