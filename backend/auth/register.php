<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');

require_once __DIR__ . '/../middlewares/db.php';
require_once __DIR__ . '/../utils/jwt.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'], $data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Username, email and password are required']);
    exit;
}

$username = trim($data['username']);
$email = trim($data['email']);
$password = $data['password'];

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username');
$stmt->execute([
    'email' => $email,
    'username' => $username
]);

if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'User already exists']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
try {
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to register user',
        'message' => $e->getMessage() 
    ]);
    exit;
}

$user_id = $pdo->lastInsertId();

$token = JWT::generate(['user_id' => $user_id], 60);

echo json_encode([
    'message' => 'Registration successful',
    'token' => $token
]);
