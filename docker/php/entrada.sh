#!/bin/sh
set -e

mkdir -p \
    /var/lib/php/sessions \
    /var/www/html/hardness3/dados_usuarios \
    /var/www/html/hardness3/smarty/templates_c \
    /var/www/html/hardness3/smarty/cache \
    /var/www/html/hardness3/ide \
    /var/www/html/hardness3/bibliotecas

php /usr/local/bin/gravar-conf.php

chown -R www-data:www-data \
    /var/lib/php/sessions \
    /var/www/html/hardness3/dados_usuarios \
    /var/www/html/hardness3/smarty/templates_c \
    /var/www/html/hardness3/smarty/cache \
    /var/www/html/hardness3/bibliotecas/confUsuario.php \
    /var/www/html/hardness3/ide/sysConfig.php

exec docker-php-entrypoint "$@"
