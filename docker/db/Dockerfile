FROM mariadb:11

# A fotografia e as rotinas entram na imagem. No Coolify um volume do
# repositório nasce vazio e o banco sobe sem a empresa.
COPY banco/fotografia.sql /semente/fotografia.sql
COPY banco/aplicar-rotinas-ausentes.sh /usr/local/bin/aplicar-rotinas-ausentes.sh
COPY src/hardness3/scripts/sql /rotinas/sql
COPY src/hardness3/scripts/atualizacaoBase-rotinas-lista.php /rotinas/lista.php
COPY docker/db/subir.sh /usr/local/bin/subir-producao.sh

RUN chmod +x /usr/local/bin/subir-producao.sh /usr/local/bin/aplicar-rotinas-ausentes.sh

ENTRYPOINT ["/usr/local/bin/subir-producao.sh"]
CMD ["mariadbd"]
