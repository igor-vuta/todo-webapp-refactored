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

$stmt = $pdo->prepare('DELETE FROM lists WHERE id = :list_id AND user_id = :user_id');

try {
    $stmt->execute([
        'list_id' => $list_id,
        'user_id' => $user_id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to delete list',
        'message' => $e->getMessage()
    ]);
    exit;
}

echo json_encode(['message' => 'List deleted successfully']);
