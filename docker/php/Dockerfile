FROM php:8.3-apache

# O framework chama apc_*. O APCu é quem guarda de fato; a ponte fica em compatibilidade.php.
RUN apt-get update \
    && apt-get install -y --no-install-recommends $PHPIZE_DEPS \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-install mysqli \
    && a2enmod rewrite \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY compatibilidade.php /usr/local/etc/php/compatibilidade.php
COPY php.ini /usr/local/etc/php/conf.d/fixar.ini
