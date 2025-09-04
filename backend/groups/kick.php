<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');

require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['group_id'], $data['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Group ID and User ID are required']);
    exit;
}

$group_id = intval($data['group_id']);
$kick_user_id = intval($data['user_id']);

$stmt = $pdo->prepare('SELECT * FROM `groups` WHERE id = :group_id AND owner_id = :owner_id');
$stmt->execute([
    'group_id' => $group_id,
    'owner_id' => $user_id
]);

if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'Only the owner can kick users']);
    exit;
}

if ($kick_user_id === $user_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Owner cannot remove themselves']);
    exit;
}

$stmtDelete = $pdo->prepare('DELETE FROM group_users WHERE group_id = :group_id AND user_id = :user_id');

try {
    $stmtDelete->execute([
        'group_id' => $group_id,
        'user_id' => $kick_user_id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to kick user', 'message' => $e->getMessage()]);
    exit;
}

echo json_encode(['message' => 'User kicked successfully']);
