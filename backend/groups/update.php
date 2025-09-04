<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');

require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Group ID and name are required']);
    exit;
}

$group_id = intval($data['id']);
$name = trim($data['name']);

$stmtCheck = $pdo->prepare('SELECT * FROM `groups` WHERE id = :group_id AND owner_id = :owner_id');
$stmtCheck->execute([
    'group_id' => $group_id,
    'owner_id' => $user_id
]);

if (!$stmtCheck->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'Only the owner can update the group']);
    exit;
}

$stmt = $pdo->prepare('UPDATE `groups` SET name = :name WHERE id = :group_id');

try {
    $stmt->execute([
        'name' => $name,
        'group_id' => $group_id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update group', 'message' => $e->getMessage()]);
    exit;
}

echo json_encode(['message' => 'Group updated successfully']);
