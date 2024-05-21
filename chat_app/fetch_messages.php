<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = new mysqli("http://php-database.cxeppltorunv.us-east-1.rds.amazonaws.com/", "admin", "cloudcomputer", "php-database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];

$result = $conn->query("SELECT m.message, u1.username AS sender, u2.username AS receiver
                        FROM messages m
                        JOIN users u1 ON m.sender_id = u1.id
                        JOIN users u2 ON m.receiver_id = u2.id
                        WHERE (m.sender_id = $user_id AND m.receiver_id = $receiver_id)
                        OR (m.sender_id = $receiver_id AND m.receiver_id = $user_id)
                        ORDER BY m.timestamp ASC");

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$conn->close();

echo json_encode(['messages' => $messages]);
?>
