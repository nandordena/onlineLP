FROM php:8.3-apache
WORKDIR /app
COPY adapters/userManager/public /var/www/html
RUN docker-php-ext-install pdo pdo_mysql