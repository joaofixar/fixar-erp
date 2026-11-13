#!/bin/bash
GOON=1
while [ $GOON ]; do
php /var/www/html/hardness3/scripts/notifications/notificacoesHora.php > /tmp/notificacoesHora.log 2>&1
sleep 3600
done

