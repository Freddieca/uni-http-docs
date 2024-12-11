<?php
// Start the session
session_start();

// Include the database connection file
include('../includes/db_connection.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve the admin data from the database
    $sql = "SELECT password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the admin exists
    if ($stmt->num_rows > 0) {
        // Fetch the hashed password from the database
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Hash the input password and compare it to the stored hashed password
        if (hash('sha512', $password) === $hashed_password) {
            // Password is correct, log the admin in
            $_SESSION['admin_username'] = $username; // Store username in session
            header("Location: ../pages/admin.php?success=1"); // Redirect to admin dashboard with success flag
            exit();
        } else {
            // Password is incorrect
            header("Location: ../pages/admin_login.html?error=Invalid+password");
            exit();
        }
    } else {
        // Admin not found
        header("Location: ../pages/admin_login.html?error=Admin+not+found");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to the admin login page if the request method is not POST
    header("Location: ../pages/admin_login.html");
    exit();
}
?>