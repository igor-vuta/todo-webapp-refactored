<?php
// One-time database initializer: applies database/schema.sql when the DB is empty.
// Runs at container startup (see railway-start.sh).

$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'todo_app';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

$pdo = null;
for ($i = 0; $i < 30; $i++) {
    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        break;
    } catch (PDOException $e) {
        echo "Waiting for database... ({$e->getMessage()})\n";
        sleep(2);
    }
}

if (!$pdo) {
    echo "WARNING: could not reach database, skipping init.\n";
    exit(0);
}

$count = (int) $pdo->query(
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()"
)->fetchColumn();

if ($count > 0) {
    echo "Database already has {$count} tables - skipping init.\n";
    exit(0);
}

echo "Empty database detected - applying schema...\n";
$sql = file_get_contents('/docker-init/database/schema.sql');

// Drop "--" comment lines, then split the mysqldump into individual statements
// (no procedures/triggers in this dump).
$sql = preg_replace('/^--.*$/m', '', $sql);
$statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
foreach ($statements as $stmt) {
    $pdo->exec($stmt);
}

echo "Database initialized (" . count($statements) . " statements applied).\n";
