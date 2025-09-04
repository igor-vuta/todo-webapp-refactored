<?php
header('Content-Type: application/json');

require_once('./middlewares/cors.php');

require_once('./middlewares/cors.php'); 
require_once __DIR__ . '/middlewares/auth.php';

echo json_encode([
    'message' => 'Authorized!',
    'user_id' => $user_id
]);
