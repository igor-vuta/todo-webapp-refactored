#!/bin/sh
set -e

# Initialize database schema on first boot (uses PDO, same as the app)
php /docker-init/init-db.php || echo "WARNING: DB init script failed"

# Seed public demo data (idempotent - skips if already seeded)
php /docker-init/seed-demo.php || echo "WARNING: demo seed failed"

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Caddy..."
exec caddy run --config /etc/caddy/Caddyfile
