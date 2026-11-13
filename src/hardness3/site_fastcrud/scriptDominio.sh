#!/bin/bash
/usr/bin/php /home/criacaoConta/scriptDominio.php $1 $2 $3 $4
/etc/init.d/apache2 reload