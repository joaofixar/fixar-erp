#!/bin/bash
GOON=1
while [ $GOON ]; do
php /var/www/html/hardness3/showprocess/showProcess.php > /tmp/showBash.log 2>&1
sleep 5s
done
