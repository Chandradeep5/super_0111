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
$conn->query("UPDATE users SET last_activity = NOW() WHERE id = $user_id");

$users_result = $conn->query("SELECT id, username, last_activity FROM users WHERE id != $user_id");

$users = [];
while ($row = $users_result->fetch_assoc()) {
    $users[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            text-align: center;
        }
        .user-list {
            list-style-type: none;
            padding: 0;
        }
        .user-list li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .user-list li a {
            text-decoration: none;
            color: #333;
        }
        .user-list li a:hover {
            text-decoration: underline;
        }
        .online {
            color: green;
        }
        .offline {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard</h2>
        <ul class="user-list">
            <?php foreach ($users as $user) { ?>
                <li>
                    <a href="chat.php?receiver_id=<?php echo $user['id']; ?>">
                        <?php echo $user['username']; ?>
                    </a>
                    <span class="<?php echo (time() - strtotime($user['last_activity']) < 300) ? 'online' : 'offline'; ?>">
                        <?php echo (time() - strtotime($user['last_activity']) < 300) ? 'Online' : 'Offline'; ?>
                    </span>
                </li>
            <?php } ?>
        </ul>
    </div>
</body>
</html>
