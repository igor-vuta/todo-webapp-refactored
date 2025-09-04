<?php
header('Content-Type: application/json');

require_once('./middlewares/cors.php');

require_once __DIR__ . '/middlewares/db.php';

echo json_encode(['success' => 'DB connection works!']);
