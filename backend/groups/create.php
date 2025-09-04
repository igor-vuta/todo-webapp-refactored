<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');

require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Group name is required']);
    exit;
}

$name = trim($data['name']);

$stmt = $pdo->prepare('INSERT INTO `groups` (name, owner_id) VALUES (:name, :owner_id)');

try {
    $stmt->execute([
        'name' => $name,
        'owner_id' => $user_id
    ]);

    $group_id = $pdo->lastInsertId();

    $stmtMember = $pdo->prepare('INSERT INTO group_users (group_id, user_id) VALUES (:group_id, :user_id)');
    $stmtMember->execute([
        'group_id' => $group_id,
        'user_id' => $user_id
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create group', 'message' => $e->getMessage()]);
    exit;
}

echo json_encode([
    'message' => 'Group created successfully',
    'id' => $group_id
]);
