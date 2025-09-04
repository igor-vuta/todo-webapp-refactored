<?php
// backend/lists/read.php
require_once __DIR__ . '/../middlewares/cors.php';
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

header('Content-Type: application/json');
ini_set('display_errors', '0');

require_once __DIR__ . '/../middlewares/auth.php'; // sets $user_id
require_once __DIR__ . '/../middlewares/db.php';   // provides $pdo

try {
  // Return lists owned by the authenticated user, with task counts
  $sql = "
    SELECT 
      l.id,
      l.name,
      COALESCE((
        SELECT COUNT(*)
        FROM tasks t
        WHERE t.list_id = l.id AND t.is_deleted = 0
      ), 0) AS task_count
    FROM lists l
    WHERE l.user_id = :uid
    ORDER BY l.id DESC
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['uid' => $user_id]);
  $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Optional overall total for the “Home” row
  $stmt2 = $pdo->prepare("
    SELECT COUNT(*) AS c
    FROM tasks t
    WHERE t.user_id = :uid AND t.is_deleted = 0
  ");
  $stmt2->execute(['uid' => $user_id]);
  $totalRow = $stmt2->fetch(PDO::FETCH_ASSOC);
  $total = (int)($totalRow['c'] ?? 0);

  echo json_encode([
    'success'     => true,
    'lists'       => $lists,
    'total_tasks' => $total,
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'error'   => 'Server error',
    'detail'  => $e->getMessage(), // leave during dev
  ]);
}