<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');
require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Task ID is required']);
    exit;
}

$task_id = intval($data['id']);

$stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id AND is_deleted = 0');
$stmt->execute(['id' => $task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    http_response_code(404);
    echo json_encode(['error' => 'Task not found']);
    exit;
}

$hasAccess = false;

if ($task['group_id']) {
    $check = $pdo->prepare('SELECT 1 FROM group_users WHERE group_id = :gid AND user_id = :uid');
    $check->execute(['gid' => $task['group_id'], 'uid' => $user_id]);
    $hasAccess = $check->fetchColumn() !== false;
} else {
    $hasAccess = $task['user_id'] == $user_id;
}

if (!$hasAccess) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$stmt = $pdo->prepare('UPDATE tasks SET is_deleted = 1 WHERE id = :task_id');

try {
    $stmt->execute(['task_id' => $task_id]);
    echo json_encode(['success' => true, 'message' => 'Task deleted']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete task', 'message' => $e->getMessage()]);
}