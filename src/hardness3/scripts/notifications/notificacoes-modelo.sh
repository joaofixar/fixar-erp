#!/bin/bash
GOON=1
while [ $GOON ]; do
php /var/www/html/hardness3/scripts/notifications/notificacoes.php > /tmp/notificacoes.log 2>&1
sleep 20
done
