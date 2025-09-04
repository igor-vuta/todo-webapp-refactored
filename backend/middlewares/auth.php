<?php
// backend/middlewares/auth.php

// 1) Always set CORS first
require_once __DIR__ . '/../middlewares/cors.php';

// 2) Let preflight through without auth
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

// 3) Normal JSON responses for protected routes
header('Content-Type: application/json');

require_once __DIR__ . '/../utils/jwt.php';

/**
 * Get the Authorization header reliably across servers.
 */
function get_auth_header(): ?string {
  // Standard place
  if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
    return $_SERVER['HTTP_AUTHORIZATION'];
  }
  // apache_request_headers (case-insensitive)
  if (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    foreach ($headers as $k => $v) {
      if (strcasecmp($k, 'Authorization') === 0) return $v;
    }
  }
  // Fallback
  if (!empty($_SERVER['Authorization'])) {
    return $_SERVER['Authorization'];
  }
  return null;
}

$auth = get_auth_header();
if (!$auth || !preg_match('/^\s*Bearer\s+(.+)\s*$/i', $auth, $m)) {
  error_log('Authorization header not found or malformed');
  http_response_code(401);
  echo json_encode(['error' => 'Authorization header not found']);
  exit;
}

$token = trim($m[1]);

try {
  // Validate token (adjust to your utils/jwt.php API if needed)
  $decoded = JWT::validate($token);

  // normalize payload to array
  if (is_object($decoded)) {
    $decoded = (array)$decoded;
  }

  if (!$decoded || !isset($decoded['user_id'])) {
    error_log('Invalid token payload: ' . json_encode($decoded));
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token payload']);
    exit;
  }

  // Expose the authenticated user id to route handlers
  $user_id = (int)$decoded['user_id'];
  if ($user_id <= 0) {
    throw new Exception('user_id not positive');
  }

} catch (Throwable $e) {
  error_log('Token validation failed: ' . $e->getMessage());
  http_response_code(401);
  echo json_encode(['error' => 'Token validation failed']);
  exit;
}