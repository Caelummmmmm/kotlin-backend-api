FROM php:8.2-apache

# Install both standard mysqli AND the missing pdo_mysql extension
RUN docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-enable mysqli pdo_mysql

COPY . /var/www/html/

EXPOSE 80
