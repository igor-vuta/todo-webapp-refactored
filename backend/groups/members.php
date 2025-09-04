<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');
require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$ids = $_GET['ids'] ?? '';
$idArray = array_filter(explode(',', $ids), 'is_numeric');

if (empty($idArray)) {
  echo json_encode(['success' => false, 'message' => 'No valid group IDs']);
  exit;
}

$placeholders = implode(',', array_fill(0, count($idArray), '?'));
$stmt = $pdo->prepare("
  SELECT gu.group_id, u.id AS user_id, u.username
  FROM group_users gu
  JOIN users u ON gu.user_id = u.id
  WHERE gu.group_id IN ($placeholders)
");
$stmt->execute($idArray);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupMap = [];
foreach ($rows as $row) {
  $groupMap[$row['group_id']][] = [
    'id' => $row['user_id'],
    'username' => $row['username']
  ];
}

echo json_encode(['success' => true, 'groups' => $groupMap]);