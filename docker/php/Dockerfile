FROM php:8.3-apache

RUN docker-php-ext-install mysqli \
    && pecl install apcu-5.1.24 \
    && docker-php-ext-enable apcu \
    && a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY compatibilidade.php /usr/local/etc/php/compatibilidade.php
COPY php.ini /usr/local/etc/php/conf.d/fixar.ini
