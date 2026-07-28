# Use PHP-FPM with Caddy (simple, reliable, no Apache issues)
FROM php:8.2-fpm-alpine

# Install Caddy
RUN apk add --no-cache caddy

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy application
COPY backend /var/www/html

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Copy and set up startup script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
