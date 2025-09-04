# Simple PHP + Apache image with mysqli/pdo_mysql
FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# Apache doc root is /var/www/html; we mount backend there via volumes
# Enable Apache mod_rewrite if needed later
RUN a2enmod rewrite

EXPOSE 80
