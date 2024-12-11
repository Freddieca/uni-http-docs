<header>
    <nav class="navbar">
        <a href="home.php">Home</a> |
        <a href="profile.php">Profile</a> |
        <a href="messages.php">Messages</a> |
        <a href="report.php">Report</a> |
        <?php if (isset($_SESSION['admin_username'])): ?>
            <a href="admin.php">Admin Panel</a> |
        <?php endif; ?>
        <a href="../pages/login.html">Logout</a>
    </nav>
</header>