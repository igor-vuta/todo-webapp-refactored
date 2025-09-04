<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');
require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['title'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Task title is required']);
    exit;
}

$title = trim($data['title']);
$due_date = $data['due_date'] ?? null;
$priority = isset($data['priority']) ? intval($data['priority']) : 1;
$list_id = isset($data['list_id']) ? intval($data['list_id']) : null;
$group_id = isset($data['group_id']) ? intval($data['group_id']) : null;
$start_time = $data['start_time'] ?? null;
$end_time = $data['end_time'] ?? null;

$stmt = $pdo->prepare('
    INSERT INTO tasks (title, due_date, priority, user_id, list_id, group_id, start_time, end_time)
    VALUES (:title, :due_date, :priority, :user_id, :list_id, :group_id, :start_time, :end_time)
');

try {
    $stmt->execute([
        'title' => $title,
        'due_date' => $due_date,
        'priority' => $priority,
        'user_id' => $user_id,
        'list_id' => $list_id,
        'group_id' => $group_id,
        'start_time' => $start_time,
        'end_time' => $end_time
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create task', 'message' => $e->getMessage()]);
    exit;
}

echo json_encode(['message' => 'Task created successfully', 'id' => $pdo->lastInsertId()]);