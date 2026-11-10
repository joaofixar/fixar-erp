FROM php:8.3-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY compatibilidade.php /usr/local/etc/php/compatibilidade.php
COPY php.ini /usr/local/etc/php/conf.d/fixar.ini
