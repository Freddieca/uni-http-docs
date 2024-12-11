<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Social Media</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <main>
        <h2>User Management</h2>
        <div id="user-list">
            <?php
            include('../includes/db_connection.php');

            // Fetch all users from the database
            $sql = "SELECT user_id, username, email FROM users";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>User ID: " . htmlspecialchars($row['user_id']) . " - Username: " . htmlspecialchars($row['username']) . " - Email: " . htmlspecialchars($row['email']) . " ";
                    echo "<button onclick='removeUser(" . htmlspecialchars($row['user_id']) . ")'>Remove</button> ";
                    echo "<button onclick='changePassword(" . htmlspecialchars($row['user_id']) . ")'>Change Password</button></li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No users found.</p>";
            }

            $conn->close();
            ?>
        </div>
        <h2>Reports</h2>
        <div id="reports"></div>
    </main>
    <footer>
        <p>&copy; 2024 Social Media Platform</p>
    </footer>
    <script>
        function loadReports() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../php/get_reports.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("reports").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function removeUser(userId) {
            if (confirm("Are you sure you want to remove this user?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../php/delete_user.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        alert(xhr.responseText);
                        location.reload();
                    } else {
                        alert("Error: " + xhr.statusText);
                    }
                };
                xhr.onerror = function () {
                    alert("Request failed.");
                };
                xhr.send("user_id=" + userId);
            }
        }

        function changePassword(userId) {
            var newPassword = prompt("Enter the new password:");
            if (newPassword) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../php/change_password.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        alert(xhr.responseText);
                    } else {
                        alert("Error: " + xhr.statusText);
                    }
                };
                xhr.onerror = function () {
                    alert("Request failed.");
                };
                xhr.send("user_id=" + userId + "&new_password=" + encodeURIComponent(newPassword));
            }
        }

        document.addEventListener("DOMContentLoaded", loadReports);
    </script>
</body>

</html>