<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("http://php-database.cxeppltorunv.us-east-1.rds.amazonaws.com/", "admin", "cloudcomputer", "php-database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch all users
$result = $conn->query("SELECT id, username FROM users WHERE id != $user_id");

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #333;
            color: #fff;
            padding: 20px;
        }
        .user-list ul {
            list-style-type: none;
            padding: 0;
        }
        .user-list li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .chat-window {
            display: none;
        }
        .chat-messages {
            border: 1px solid #ccc;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            margin-bottom: 10px;
        }
        .chat-messages div {
            margin-bottom: 10px;
        }
        .back-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #d32f2f;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px;
        }
        .header img {
            height: 40px;
            margin: 0 10px;
            cursor: pointer;
        }

    </style>
</head>

<body>
<div class="header">
        <img src="photo/logo1.jpeg" alt="Logo 1" onclick="location.href='https://e45d51g56.s3.amazonaws.com/games.html'">
        <img src="photo/logo2.png" alt="Logo 2" onclick="location.href='https://e45d51g56.s3.amazonaws.com/bot.html'">
        <img src="photo/logo3.jpeg" alt="Logo 3" onclick="location.href='https://e45d51g56.s3.amazonaws.com/horoscopes.html'">
        <img src="photo/logo4.png" alt="Logo 4" onclick="location.href='https://open.spotify.com/'">
        <img src="photo/logo5.jpeg" alt="Logo 5" onclick="location.href='https://www.microsoft.com/en-us/microsoft-365/outlook/log-in'">
        <img src="photo/logo6.jpeg" alt="Logo 6" onclick="location.href='https://www.youtube.com/'">
    </div>
    <div class="container">
        <div class="sidebar">
            <h2>Dashboard</h2>
            <div class="user-list">
                <ul>
                    <?php foreach ($users as $user) { ?>
                        <li onclick="startChat(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>')"><?php echo $user['username']; ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="main-content">
            <div class="chat-window">
                <button class="back-button" onclick="showDashboard()">Back to Dashboard</button>
                <h2 id="chat-with">Chat with</h2>
                <div class="chat-messages" id="chat-messages"></div>
                <form id="chat-form" onsubmit="sendMessage(); return false;">
                    <input type="hidden" id="receiver_id">
                    <input type="text" id="message" required>
                    <input type="submit" value="Send">
                </form>
            </div>
        </div>
    </div>
    <script>
        function startChat(receiverId, receiverUsername) {
            document.querySelector('.main-content').style.display = 'block';
            document.querySelector('.chat-window').style.display = 'block';
            document.getElementById('chat-with').innerText = 'Chat with ' + receiverUsername;
            document.getElementById('receiver_id').value = receiverId;
            fetchMessages(receiverId);
        }

        function showDashboard() {
            document.querySelector('.main-content').style.display = 'none';
            document.querySelector('.chat-window').style.display = 'none';
        }

        function fetchMessages(receiverId) {
            fetch('fetch_messages.php?receiver_id=' + receiverId)
                .then(response => response.json())
                .then(data => {
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.innerHTML = '';
                    data.messages.forEach(message => {
                        const div = document.createElement('div');
                        div.textContent = `${message.sender}: ${message.message}`;
                        chatMessages.appendChild(div);
                    });
                });
        }

        function sendMessage() {
            const message = document.getElementById('message').value;
            const receiverId = document.getElementById('receiver_id').value;
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `message=${message}&receiver_id=${receiverId}`
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      document.getElementById('message').value = '';
                      fetchMessages(receiverId);
                  } else {
                      alert('Error sending message');
                  }
              });
        }

        setInterval(() => {
            const receiverId = document.getElementById('receiver_id').value;
            if (receiverId) {
                fetchMessages(receiverId);
            }
        }, 3000);
    </script>
</body>
</html>
