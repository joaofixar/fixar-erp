#!/bin/bash
GOON=1
while [ $GOON ]; do
/usr/bin/php /var/www/html/hardness3/scripts/queueMDFE/queueMDFE.php >> /tmp/queueMDFE.log 2>&1
sleep 1
done
