<?php
require_once 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

$response = ['success' => false];

if ($username && $password) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $response['success'] = true;
    }

    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>
