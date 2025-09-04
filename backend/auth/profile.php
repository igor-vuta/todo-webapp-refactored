<?php
require_once '../middlewares/db.php';
require_once '../middlewares/auth.php'; 
require_once __DIR__ . '/../middlewares/cors.php';

try {
    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        exit;
    }

    echo json_encode([
        "id" => $user['id'],
        "username" => $user['username'],
        "email" => $user['email'],
        "created_at" => $user['created_at']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
    exit;
}
