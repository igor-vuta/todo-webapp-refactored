<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');

require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'List name is required']);
    exit;
}

$name = trim($data['name']);
$group_id = isset($data['group_id']) ? intval($data['group_id']) : null;

$stmt = $pdo->prepare('
    INSERT INTO lists (name, user_id, group_id)
    VALUES (:name, :user_id, :group_id)
');

try {
    $stmt->execute([
        'name' => $name,
        'user_id' => $user_id,
        'group_id' => $group_id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to create list',
        'message' => $e->getMessage()
    ]);
    exit;
}

echo json_encode([
    'message' => 'List created successfully',
    'id' => $pdo->lastInsertId()
]);
