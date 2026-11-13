#!/bin/bash
GOON=1
while [ $GOON ]; do
/usr/local/bin/php /home/fabio/html/hardness3/scripts/email/atualizaEmail.php fabio >> /tmp/atualizarEmails 2>&1
sleep 1
done


