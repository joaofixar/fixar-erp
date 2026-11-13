chmod -R 777 static/css/temas
mkdir dados_usuarios
chmod -R 777 dados_usuarios
chmod -R 777 smarty/templates_c
chmod -R 777 smarty/cache
chmod -R 777 bibliotecas/tcpdf/cache
chmod -R 777 personalizacao
chmod +x scripts/erp/*.sh
chmod +x scripts/backup/*.sh
mkdir apps
chmod -R 777 apps
chmod 0600 outros/apps/apps.key
chown www-data:www-data outros/apps/apps.key
sudo apt install -f dos2unix
dos2unix outros/apps/apps.key
