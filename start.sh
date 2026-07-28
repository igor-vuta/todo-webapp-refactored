#!/bin/sh
set -e

echo "Starting PHP-FPM..."
php-fpm -D

echo "Waiting for PHP-FPM to be ready..."
sleep 2

echo "Checking if PHP-FPM is listening..."
netstat -an | grep 9000 || echo "PHP-FPM not found on port 9000"

echo "Starting Caddy..."
exec caddy run --config /etc/caddy/Caddyfile
