<?php
// backend/middlewares/db.php

$host     = getenv('DB_HOST') ?: 'db';      // <-- container-to-container host
$port     = getenv('DB_PORT') ?: '3306';
$db_name  = getenv('DB_NAME') ?: 'todo_app';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';

$dsn = "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode([
    'error' => 'Database connection failed',
    'message' => $e->getMessage(),
    'host' => $host,
    'port' => $port,
  ]);
  exit;
}