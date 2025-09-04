<?php
header('Content-Type: application/json');

require_once('../middlewares/cors.php');
require_once __DIR__ . '/../middlewares/auth.php';
require_once __DIR__ . '/../middlewares/db.php';

$ids = $_GET['ids'] ?? '';
$idArray = array_filter(explode(',', $ids), 'is_numeric');

if (empty($idArray)) {
    echo json_encode(['success' => false, 'message' => 'No valid IDs']);
    exit;
}

$placeholders = implode(',', array_fill(0, count($idArray), '?'));
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id IN ($placeholders)");
$stmt->execute($idArray);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'users' => $users]);