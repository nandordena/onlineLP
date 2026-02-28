FROM php:8.3-apache
WORKDIR /var/www/html
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
COPY infrastructure/apache/vhost.cfg /etc/apache2/sites-available/000-default.conf