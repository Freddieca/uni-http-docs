<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Social Media</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <main>
        <h2>Direct Messages</h2>
        <div class="chat-window" id="chat-window">
            <!-- Messages will be loaded here -->
        </div>
        <form id="message-form">
            <input type="text" id="message-input" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Social Media Platform</p>
    </footer>
    <script>
        function loadMessages() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../php/get_messages.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("chat-window").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        document.getElementById("message-form").addEventListener("submit", function (e) {
            e.preventDefault();
            var message = document.getElementById("message-input").value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../php/send_message.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    loadMessages();
                    document.getElementById("message-input").value = "";
                }
            };
            xhr.send("message=" + encodeURIComponent(message));
        });

        // Load messages when page loads
        document.addEventListener("DOMContentLoaded", loadMessages);
    </script>
</body>

</html>