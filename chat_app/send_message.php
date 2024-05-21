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
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];

$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $receiver_id, $message);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
