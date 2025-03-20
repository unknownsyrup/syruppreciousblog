<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$apiKey = 'lTKGF8VRXPTQdtHnnazUoMJQc'; 
if (!isset($_SERVER['HTTP_X_DX_API']) || $_SERVER['HTTP_X_DX_API'] !== $apiKey) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid API key']);
    exit;
}

$requestData = file_get_contents('php://input');


$data = json_decode($requestData, true);


if (!isset($data['username']) || !isset($data['password']) || !isset($data['phone']) || !isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing required fields']);
    exit;
}

$username = htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8');
$password = password_hash($data['password'], PASSWORD_DEFAULT); 
$phone = htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8');
$name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');


$dbFile = __DIR__ . '/database.sqlite'; 
$conn = new PDO('sqlite:' . $dbFile);


if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Database connection error']);
    exit;
}


$conn->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    phone TEXT NOT NULL,
    name TEXT NOT NULL
)");

$stmt = $conn->prepare('INSERT INTO users (username, password, phone, name) VALUES (?, ?, ?, ?)');
$stmt->bindParam(1, $username);
$stmt->bindParam(2, $password);
$stmt->bindParam(3, $phone);
$stmt->bindParam(4, $name);


if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['message' => 'Registration successful']);
} else {
    
    $errorInfo = $stmt->errorInfo();
    http_response_code(500);
    echo json_encode(['message' => 'Registration failed: ' . $errorInfo[2]]);
}
?>
