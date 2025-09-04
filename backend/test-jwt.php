<?php
header('Content-Type: application/json');

require_once('./middlewares/cors.php');

require_once __DIR__ . '/utils/jwt.php';

$payload = ['user_id' => 1];
$token = JWT::generate($payload, 1);

echo json_encode([
    'token' => $token,
    'decoded' => JWT::validate($token)
]);
