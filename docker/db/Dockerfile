FROM mariadb:11

# A fotografia, as rotinas e o script de colunas entram na imagem. No Coolify um volume do
# repositório nasce vazio e o banco sobe sem a empresa.
COPY banco/fotografia.sql /semente/fotografia.sql
COPY banco/aplicar-rotinas-ausentes.sh /usr/local/bin/aplicar-rotinas-ausentes.sh
COPY banco/aplicar-estrutura-ausente.sh /usr/local/bin/aplicar-estrutura-ausente.sh
COPY banco/ler-estrutura.awk /usr/local/bin/ler-estrutura.awk
COPY src/hardness3/scripts/sql /rotinas/sql
COPY src/hardness3/scripts/atualizacaoBase-rotinas-lista.php /rotinas/lista.php
COPY src/hardness3/scripts/atualizacaoBase.php /rotinas/atualizacaoBase.php
COPY docker/db/subir.sh /usr/local/bin/subir-producao.sh

RUN chmod +x /usr/local/bin/subir-producao.sh /usr/local/bin/aplicar-rotinas-ausentes.sh /usr/local/bin/aplicar-estrutura-ausente.sh

ENTRYPOINT ["/usr/local/bin/subir-producao.sh"]
CMD ["mariadbd"]
