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

$fields = [];
$params = ['task_id' => $task_id];

if (isset($data['title'])) {
    $fields[] = 'title = :title';
    $params['title'] = $data['title'];
}
if (isset($data['due_date'])) {
    $fields[] = 'due_date = :due_date';
    $params['due_date'] = $data['due_date'];
}
if (isset($data['status'])) {
    $fields[] = 'status = :status';
    $params['status'] = $data['status'];
}
if (isset($data['is_deleted'])) {
    $fields[] = 'is_deleted = :is_deleted';
    $params['is_deleted'] = intval($data['is_deleted']);
}
if (isset($data['priority'])) {
    $fields[] = 'priority = :priority';
    $params['priority'] = intval($data['priority']);
}
if (isset($data['start_time'])) {
    $fields[] = 'start_time = :start_time';
    $params['start_time'] = $data['start_time'];
}
if (isset($data['end_time'])) {
    $fields[] = 'end_time = :end_time';
    $params['end_time'] = $data['end_time'];
}
if (array_key_exists('group_id', $data)) {
    $fields[] = 'group_id = :group_id';
    $params['group_id'] = $data['group_id']; 
}

if (array_key_exists('list_id', $data)) {
    $fields[] = 'list_id = :list_id';
    $params['list_id'] = $data['list_id'];
}

if (empty($fields)) {
    http_response_code(400);
    echo json_encode(['error' => 'No fields to update']);
    exit;
}

$query = 'UPDATE tasks SET ' . implode(', ', $fields) . ' WHERE id = :task_id';

$stmt = $pdo->prepare($query);

try {
    $stmt->execute($params);
    echo json_encode(['success' => true, 'message' => 'Task updated']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update task', 'message' => $e->getMessage()]);
}