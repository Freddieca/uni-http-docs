<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report - Social Media</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <main>
        <h2>Report or Block User</h2>
        <form>
            <label for="username">Username to Report:</label>
            <input type="text" id="username" required>
            <button type="submit">Submit Report</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Social Media Platform</p>
    </footer>
</body>

</html>