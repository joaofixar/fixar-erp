#!/bin/bash
GOON=1
while [ $GOON ]; do
php /var/www/html/hardness3/scripts/notifications/notificacoesDia.php > /tmp/notificacoesDia.log 2>&1
sleep 86400
done
