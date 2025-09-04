<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');

require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'List ID is required']);
    exit;
}

$list_id = intval($data['id']);
$fields = [];
$params = ['list_id' => $list_id, 'user_id' => $user_id];

if (isset($data['name'])) {
    $fields[] = 'name = :name';
    $params['name'] = $data['name'];
}

if (isset($data['group_id'])) {
    $fields[] = 'group_id = :group_id';
    $params['group_id'] = intval($data['group_id']);
}

if (empty($fields)) {
    http_response_code(400);
    echo json_encode(['error' => 'No fields to update']);
    exit;
}

$query = 'UPDATE lists SET ' . implode(', ', $fields) . ' WHERE id = :list_id AND user_id = :user_id';

$stmt = $pdo->prepare($query);

try {
    $stmt->execute($params);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to update list',
        'message' => $e->getMessage()
    ]);
    exit;
}

echo json_encode(['message' => 'List updated successfully']);
