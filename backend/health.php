<?php
// backend/health.php
// Health check endpoint for cloud providers

header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'service' => 'todo-webapp-api',
];

// Check database connection
try {
    require_once __DIR__ . '/middlewares/db.php';
    
    $stmt = $pdo->query('SELECT 1');
    $health['database'] = 'connected';
} catch (Exception $e) {
    $health['status'] = 'unhealthy';
    $health['database'] = 'disconnected';
    $health['error'] = $e->getMessage();
    http_response_code(503);
}

// Check environment
$health['environment'] = [
    'php_version' => PHP_VERSION,
    'jwt_configured' => getenv('JWT_SECRET') ? true : false,
    'db_host' => getenv('DB_HOST') ?: 'not set',
];

echo json_encode($health, JSON_PRETTY_PRINT);
