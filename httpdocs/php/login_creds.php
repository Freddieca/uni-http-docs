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

    // Retrieve the user data from the database
    $sql = "SELECT password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        // Fetch the hashed password from the database
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Hash the input password and compare it to the stored hashed password
        if (hash('sha512', $password) === $hashed_password) {
            // Password is correct, log the user in
            $_SESSION['username'] = $username; // Store username in session
            header("Location: ../pages/home.php?success=1"); // Redirect to home with success flag
            exit();
        } else {
            // Password is incorrect
            header("Location: ../pages/login.html?error=Invalid+password");
            exit();
        }
    } else {
        // User not found
        header("Location: ../pages/login.html?error=User+not+found");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to the login page if the request method is not POST
    header("Location: ../pages/login.html");
    exit();
}
?>