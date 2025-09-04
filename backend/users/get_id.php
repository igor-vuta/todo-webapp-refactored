<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');
require_once(__DIR__ . '/../middlewares/auth.php');
require_once(__DIR__ . '/../middlewares/db.php');

if (!isset($_GET['username'])) {
    echo json_encode(['success' => false, 'message' => 'Username required']);
    exit;
}

$username = trim($_GET['username']);

$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
$stmt->execute(['username' => $username]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['success' => true, 'user_id' => $user['id']]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}