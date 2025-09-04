<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');

require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$query = '
    SELECT g.*,
    (
      SELECT COUNT(*) FROM group_users gu
      WHERE gu.group_id = g.id
    ) AS members_count
    FROM `groups` g
    JOIN group_users gu ON g.id = gu.group_id
    WHERE gu.user_id = :user_id
';

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);

$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'groups' => $groups
  ]);
  
