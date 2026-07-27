<?php
// backend/middlewares/cors.php

// Get allowed origins from environment or use defaults
$envOrigins = getenv('CORS_ORIGINS') ?: '';
$allowedOrigins = array_filter(array_map('trim', explode(',', $envOrigins)));

// Add default local origins for development
if (empty($allowedOrigins)) {
  $allowedOrigins = [
    'http://localhost:5173',
    'http://localhost:3000',
    'http://127.0.0.1:5173',
  ];
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
  header("Access-Control-Allow-Origin: $origin");
  header('Vary: Origin');
}

header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Preflight must NOT be authenticated
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}