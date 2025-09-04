<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');
require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$list_id = isset($_GET['list_id']) ? intval($_GET['list_id']) : null;
$group_id = isset($_GET['group_id']) && $_GET['group_id'] !== '' ? intval($_GET['group_id']) : null;
$date_from = $_GET['date_from'] ?? null;
$date_to = $_GET['date_to'] ?? null;
$due_date = $_GET['due_date'] ?? null;

try {
    $params = [];
    $query = 'SELECT * FROM tasks WHERE is_deleted = 0';

    if ($group_id) {
        $check = $pdo->prepare('SELECT 1 FROM group_users WHERE group_id = ? AND user_id = ?');
        $check->execute([$group_id, $user_id]);

        if (!$check->fetch()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied to this group']);
            exit;
        }

        $query .= ' AND group_id = ?';
        $params[] = $group_id;
    } elseif ($list_id !== null) {
        $query .= ' AND user_id = ? AND list_id = ?';
        $params[] = $user_id;
        $params[] = $list_id;
    } else {
        $groupStmt = $pdo->prepare('SELECT group_id FROM group_users WHERE user_id = ?');
        $groupStmt->execute([$user_id]);
        $groupIds = $groupStmt->fetchAll(PDO::FETCH_COLUMN);

        $query .= ' AND (user_id = ?';
        $params[] = $user_id;

        if (!empty($groupIds)) {
            $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
            $query .= " OR group_id IN ($placeholders)";
            $params = array_merge($params, $groupIds);
        }

        $query .= ')';
    }

    if ($due_date) {
        $query .= ' AND due_date = ?';
        $params[] = $due_date;
    }

    if ($date_from && $date_to) {
        $query .= ' AND due_date BETWEEN ? AND ?';
        $params[] = $date_from;
        $params[] = $date_to;
    }

    $query .= ' ORDER BY due_date ASC';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'tasks' => $tasks]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}