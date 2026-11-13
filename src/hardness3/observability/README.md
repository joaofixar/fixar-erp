# Hardness Observability — primeiros coletores reais

Módulo isolado e somente leitura, compatível com PHP 7.4. A publicação, autenticação, sessão e autorização permanecem externas aos coletores.

## Dados reais já coletados

- Linux: hostname, horário, uptime, load average, CPU por amostra de `/proc/stat`, memória de `/proc/meminfo` e uso do volume raiz.
- Apache/PHP: versão Apache, PHP, SAPI, OPcache, APCu, `memory_limit` e `max_execution_time`.
- MySQL 5.7/8.0/8.4: versão, conexões abertas/em uso, uptime, `Slow_queries`, tamanho estimado do schema e `SHOW FULL PROCESSLIST`.

As consultas MySQL são fixas e individuais: `SELECT VERSION()`, `SHOW GLOBAL STATUS`, leitura de `information_schema.tables`, `SELECT CONNECTION_ID()`, `SHOW FULL PROCESSLIST` e, quando disponível, a correlação read-only de esperas InnoDB no `performance_schema` (ou `information_schema` no MySQL 5.7). O módulo não aceita SQL do navegador e não contém comandos de alteração ou administração.

Para a visão `root` (Visão Hardness), o módulo reutiliza exclusivamente a
configuração local `showprocess/confConnect.php` do mesmo servidor. Com isso,
o painel abre uma única conexão administrativa ao MySQL — como o
`showprocess` — para obter status e `SHOW FULL PROCESSLIST`, em vez de abrir
uma conexão por cliente. A credencial não é exibida, aceita SQL do navegador
nem executa `KILL`; todas as consultas do Observability permanecem somente
leitura. Se esse arquivo não existir ou estiver inválido, o módulo preserva a
coleta legada por tenant como fallback.

## Operação

O Alias Apache aprovado publica somente `observability/public/` em:

`/observability/`

Em servidores com o `DocumentRoot` em `/var/www/html`, a configuração é global
(não por cliente) e pode ser criada em
`/etc/apache2/conf-available/hardness-observability.conf`:

```apache
Alias /observability/ /var/www/html/hardness3/observability/public/

<Directory /var/www/html/hardness3/observability/public>
    Require all granted
    Options -Indexes -MultiViews
    AllowOverride All
</Directory>
```

Habilite e valide antes de recarregar o Apache:

```bash
sudo a2enconf hardness-observability
sudo apachectl configtest
sudo systemctl reload apache2
```

Não exponha `hardness3/observability/` diretamente: o `.htaccess` dessa pasta
deve continuar bloqueando acesso; somente `public/` é publicado pelo Alias.

Em produção, o provider real é usado. O mock continua disponível exclusivamente quando `OBSERVABILITY_DEVELOPMENT_MOCK=1` é definido antes de iniciar o processo.

Os limites de severidade têm valores padrão e podem ser ajustados com um arquivo operacional opcional, fora do DocumentRoot:

`/etc/hardness/observability/collectors.json`

Use [collectors.example.json](config/collectors.example.json) como modelo. Ausência ou formato inválido preserva os limites padrão.

## Permissões de processo

`full_sql` controla o SQL completo; sem ela, a API e o drawer recebem somente uma indicação sanitizada. `connection_identity` controla a exposição de usuário, banco e host da conexão. A aplicação não depende dessas permissões para decidir o tenant ou papel.

## Acesso de usuários

A configuração de autorização de usuários fica em `config/access.json`, fora do DocumentRoot, com a mesma estrutura de [access.example.json](config/access.example.json). O arquivo real não é versionado e deve ser mantido com permissões que impeçam escrita por grupo/outros e leitura por outros.

Sem `config/access.json` — ou quando ele estiver vazio, inválido ou com permissões inseguras — somente `root`, após validar a senha master oficial do Hardness, pode acessar. Usuários comuns do ERP são negados. O arquivo legado em `/etc/hardness/observability/client-access.json` não é utilizado.

Com o arquivo válido e `enabled: true`, `root` continua autorizado pela senha master. Usuários comuns devem ser configurados em `tenants.<tenant_key>.allowed_users`; o `tenant_key` é o host canônico do arquivo `/var/www/sites/<host>-confUsuario.php`, nunca um parâmetro do navegador. `full_sql` somente libera o SQL completo quando também há `view`.

Os clientes são descobertos exclusivamente em `/var/www/sites`: para usuário comum, somente `/var/www/sites/<host>-confUsuario.php` é carregado após validar o host, sem fallback. Para root, somente arquivos `*-confUsuario.php` são considerados; symlinks e arquivos inválidos são ignorados, e bancos duplicados são deduplicados. A sessão do cliente guarda apenas o host validado, `tenant_key`, ID C007 e capabilities; não guarda a configuração nem credenciais do banco. Em toda requisição, host e `tenant_key` são resolvidos e conferidos novamente. Para clientes, processos MySQL são filtrados no backend pelo schema do tenant; linhas sem schema e schemas de outros tenants não entram no JSON.

Para criar a configuração:

```bash
cp observability/config/access.example.json observability/config/access.json
chmod 0640 observability/config/access.json
```

O root usa exclusivamente `$sys['senhaRootMaster']`, carregada pelo mecanismo oficial
`configuracoes/sysConfig.php` → `ide/sysConfig.php`, e compara o MD5 da senha digitada com esse
hash. `senhaRootSistema` não é aceita: ela é uma senha específica por representante/tenant e não
é equivalente ao acesso administrativo global da Observability. Não é necessário provisionar
`/etc/hardness/observability/runtime.json` para autenticação. Quando APCu não estiver ativo, o
usuário do Apache precisa ter escrita somente em `/var/lib/hardness/observability/` (por exemplo,
diretório `0750`) para o rate limit persistente; se esse diretório não puder ser usado, os logins
são bloqueados por segurança.

O mesmo diretório armazena o histórico curto de diagnósticos (alertas ativos e resolvidos nas últimas
24 horas). Sem escrita, o painel continua a coletar dados, mas informa que o histórico não está disponível.

## Capturas de incidente (Visão Hardness)

O usuário `root` pode usar **Salvar captura** quando identificar um gargalo. A captura grava o estado já
autorizado da coleta (servidor, MySQL, processos e diagnósticos), horário e uma observação opcional em
`dados_usuarios/observability/`. A área **Capturas** permite selecionar duas ocorrências e comparar conexões,
queries, locks, diagnósticos repetidos e processos semelhantes. O recurso não é exposto a usuários de clientes.

A retenção é de até 50 capturas e 30 dias. Os arquivos ficam organizados em
`dados_usuarios/observability/captures/` e o histórico de diagnósticos em
`dados_usuarios/observability/diagnostics/`; ambos são limpos pela retenção. O usuário do Apache precisa ter escrita em
`dados_usuarios/observability/`; caso contrário, a coleta continua funcionando e a gravação informa o erro.

## Rede do navegador

A tela Rede mede exclusivamente a duração de uma requisição HTTP autenticada para a própria aplicação. As amostras, perda, oscilação e classificação ficam somente na memória do navegador (até 20 medições); não há ICMP, comandos de sistema, serviço externo ou persistência.

## Gravador MySQL

O gravador de linha de comando reutiliza uma única conexão local do `showprocess`, mantém consultas fixas e somente leitura e não recebe SQL ou caminhos do navegador. O modo recomendado descobre no inventário confiável apenas os schemas do mesmo host/porta MySQL e faz uma única coleta global:

```bash
php observability/bin/mysql-recorder.php --all-schemas --dry-run
```

O modo persistente deve ser acionado explicitamente. Para uma única rodada local:

```bash
php observability/bin/mysql-recorder.php --all-schemas --record --once
```

Processlist, locks e transações são lidos uma vez por amostra e separados em memória pelo schema responsável. Status e erros são globais e gravados uma única vez com escopo `server`. A cada cinco minutos, uma única consulta global seleciona até 2.000 digests com cobertura combinada por schema, tempo total, frequência, linhas examinadas, tempo de lock e sinais de erro/temporária; uma linha sentinela permite indicar quando esse limite foi atingido. O modo `--database=e229` permanece apenas para compatibilidade e piloto restrito; `--database` e `--all-schemas` são mutuamente exclusivos.

Os acumulados completos de digest permanecem somente em `state/server.json`, onde servem de base para a próxima diferença. O JSONL histórico não duplica valores absolutos: grava a baseline inicial e, nas amostras seguintes, somente digests com delta positivo, novos/resetados, desaparecimentos, indisponibilidade ou cobertura limitada. Uma amostra sem atividade atualiza estado e frequência, mas não cria linha de digest.

A consulta também retorna a quantidade total de digests existentes antes do recorte. Assim, o painel distingue cobertura completa de amostragem mesmo quando o limite físico de 2.000 linhas não é atingido. Rankings usam o digest como desempate determinístico. Durante cobertura amostrada, a saída de uma consulta do ranking não é registrada como `disappeared_digests`; esse sinal só é produzido quando a leitura anterior e a atual têm cobertura completa.

Sem `--once`, o padrão é um ciclo de 50 segundos, com eventos rápidos a cada 10 segundos. Status/erros são coletados no máximo a cada minuto e digests no máximo a cada cinco minutos, usando um único estado `state/server.json`. O estado conserva somente IDs e contadores necessários ao próximo delta, sem duplicar textos SQL. Eventos vazios não são persistidos. Os dados ficam em `dados_usuarios/observability/mysql/{events,digests,status,state}`; históricos são JSONL diários, a retenção padrão é de 7 dias e cada arquivo diário tem limite de 16 MiB. Uma trava `flock` no escopo do servidor impede duas instâncias globais simultâneas.

O modo global aceita no máximo 100 schemas autorizados e foi testado sem banco real com 35 schemas no mesmo ciclo. A quantidade de consultas ao MySQL permanece constante: aumentar o número de bases amplia apenas a separação em memória; eventos vazios não são persistidos. A duração total e por fonte fica registrada para o piloto. O histórico sinaliza orçamento excedido, limites de locks/consultas/transações, limite global de digests e `Performance_schema_digest_lost`; qualquer um desses sinais impede que a tela apresente o período como completo. Amostras consecutivas do mesmo processo/fingerprint são consolidadas em um incidente com primeira/última observação, quantidade de amostras e maior duração. Root pode ver métricas `server` e todos os schemas autorizados. Clientes recebem somente o próprio schema; métricas globais e registros sem schema não são entregues.

O diretório alternativo `OBSERVABILITY_TEST_MYSQL_RECORDER_PATH` só é considerado junto de `OBSERVABILITY_TEST_MODE=1` e existe exclusivamente para testes. Não o use como configuração operacional. A instalação de cron e o piloto em produção são etapas manuais posteriores; nenhuma task cria commit automaticamente.

### Piloto manual e critérios de interrupção

O piloto global permanece manual e começa obrigatoriamente por `--all-schemas --dry-run`. Antes dele, confirme endpoint aprovado, usuário MySQL efetivo, schemas autorizados esperados, extensão `mysqli`, destino privado gravável e pelo menos 1 GiB livre. A quantidade de schemas não é critério de aprovação: o piloto deve representar o servidor real que será monitorado. Não execute `--record` quando faltar qualquer evidência do pré-flight ou quando a coleta for parcial.

O processo persistente deve executar como o mesmo usuário do sistema operacional que serve o painel — `www-data` no ambiente local validado. Os diretórios permanecem em `0700` e os arquivos em `0600`; executar primeiro como `root` ou `ubuntu` pode deixar o histórico ilegível para o painel. Antes de agendar, valide uma rodada única com esse usuário e confirme que ele consegue criar, ler e atualizar `dados_usuarios/observability/mysql`.

Instalações em que `dados_usuarios` é um link simbólico para um volume dedicado são suportadas: o gravador fixa o caminho canônico do diretório pai antes de criar `mysql`. O próprio diretório `mysql`, suas categorias e seus arquivos continuam proibidos de ser links simbólicos.

Interrompa o piloto se a coleta total ultrapassar o intervalo de 10 s; se uma fonte ultrapassar 4 s ou ficar acima de 2 s por três amostras; se CPU subir 10 pontos percentuais ou 20% por cinco minutos; se o p95 de latência subir 10 ms ou 20% por cinco minutos; se conexões crescerem 10% de `max_connections` ou restar menos de 20% de folga; ou se algum JSONL projetar mais de 12,8 MiB/dia. Também interrompa diante de limite diário, fonte indispensável indisponível, evento vazio/sem schema, duplicação de estado/status global, falha de isolamento ou exposição de SQL não sanitizado, identidade, caminho, estado ou credencial fora das capabilities.

No piloto local de 2026-10-13, o PHP do host não possuía `mysqli`, mas o container autorizado `hardness-php7` permitiu o dry-run contra `127.0.0.1:3306`. O arquivo local `configuracoes/local/ambientes.php` adicionou sete schemas deduplicados e explicitamente configurados; bases apenas visíveis por `SHOW DATABASES` não foram promovidas automaticamente. O dry-run de sete schemas terminou sem parcialidade: eventos 2,36 ms, status/erros 3,48 ms e digests 114,49 ms. A projeção ingênua dos 320 acumulados repetidos era 65,862 MiB/dia de digests. Após compactar o histórico, duas amostras reais separadas por 300 segundos encontraram 14 digests ativos, baseline inicial de 164.237 bytes e amostra delta de 6.204 bytes. A nova projeção é 1,855 MiB/dia de digests e aproximadamente 3,2 MiB/dia incluindo status, dentro do orçamento. Essa medição antecede a seleção multidimensional da Task 7 e deve ser refeita no próximo dry-run. As fixtures com 35 schemas continuam demonstrando custo constante em simulação; a aprovação operacional ainda exige linha de base externa de CPU e latência.

Os contadores do Performance Schema ajudam a detectar erros e desconexões observados pelo servidor, mas não garantem a captura de `MySQL server has gone away`/`Lost connection` produzidos somente pela biblioteca cliente PHP. Atribuir esses casos ao cron e à consulta exata exige instrumentação separada na aplicação, com horário, processo, schema, `mysqli_errno` e fingerprint sanitizado. O gravador não altera os crons nem promete essa correlação.

O rollback de uma futura rodada aprovada deve ser reversível: pare o processo, confirme a ausência de instância ativa e mova somente os arquivos criados pelo piloto para uma quarentena privada datada no mesmo filesystem. Não exclua automaticamente JSONL ou estado. Restaure-os com diretórios `0700` e arquivos `0600` se necessário. Não instale nem proponha cron antes da aprovação documentada do piloto.

### Histórico autorizado do gravador

A rota somente leitura `GET /api/v1/mysql-history` e a tela **Histórico MySQL** leem os JSONL da Task 2. O navegador pode informar apenas `from`, `to`, `page` e `limit`; `from` e `to` devem ser RFC 3339, o período máximo é de 7 dias, `page` começa em 1 e `limit` é limitado a 200. Parâmetros como `schema`, `tenant`, `path`, categoria ou SQL não são usados. Sem período explícito, a API consulta as últimas 24 horas.

O schema é resolvido exclusivamente no backend:

- para cliente, a sessão e o host validado resolvem exatamente o banco do tenant atual;
- para `root`, o inventário confiável de `/var/www/sites` define os schemas consultáveis;
- somente no ambiente local, quando o arquivo ignorado pelo Git `configuracoes/local/ambientes.php` existe, seus perfis de banco em loopback também compõem o inventário; arquivo ausente mantém o comportamento de produção inalterado;
- o mesmo ID C007 não transfere autorização entre hosts/tenants;
- se o mesmo nome de schema existir em servidores MySQL diferentes, a leitura do cliente falha fechada, porque o formato da Task 2 não contém identidade do servidor suficiente para separar esses registros.

O contrato `mysql-history.v1` retorna `period`, `scope`, `pagination`, `timeline`, `diagnostics`, `status_series`, `error_series`, `digest_ranking`, `signals` e `metadata`. `metadata` explicita baseline, parcialidade, fontes indisponíveis, categorias ainda sem dados, limites atingidos e estatísticas limitadas de leitura — nunca caminhos. A timeline contém locks, consultas longas, transações longas e saúde do coletor. Status e erros usam somente deltas válidos; gauges instantâneos ficam separados. O ranking soma a atividade (`delta`) do período e nunca os acumulados absolutos ou amostras de baseline. Na tela, a visão inicial **Para investigar** aplica os mesmos sinais do diagnóstico e informa o motivo; as visões **Mais lentas**, **Maior impacto**, **Mais frequentes** e **Ver todas** permitem mudar a ordenação sem uma nova consulta ao MySQL. Reset, reinício, digest desaparecido, coleta parcial e arquivo diário no limite permanecem sinalizados.

O ranking resume execuções, tempo total, média derivada do período, lock, linhas examinadas/retornadas/afetadas, erros, alertas, temporárias, temporárias em disco e execuções sem índice ou sem bom índice. Primeira e última ocorrência são preservadas quando disponíveis. O maior tempo observado é identificado como acumulado do Performance Schema, pois não pode ser atribuído exclusivamente à janela consultada.

### Diagnóstico assistido do histórico

A camada determinística `MysqlRecorderDiagnosticEngine` trabalha somente sobre valores já autorizados pela leitura histórica; não consulta o MySQL e não executa análise de plano. Cada resultado informa nível (`confirmed`, `strong_indication`, `attention` ou `insufficient_data`), categoria, schema permitido, explicação, evidências numéricas, recomendação segura, limitação, período e SQL sanitizado ou fingerprint conforme as capabilities.

São avaliados locks confirmados, consultas e transações longas, crescimento global de `Slow_queries`, tempo e volume de digests, linhas examinadas, proporção entre examinadas e retornadas, sinais de índice, temporárias em disco, espera por locks, erros MySQL, pressão de conexões e limitações da coleta. Consulta longa confirma apenas a duração; transação longa não é declarada bloqueadora sem relação de lock; sinal de índice não ordena criação de índice.

O aumento de `Slow_queries` é sempre global. A resposta informa sua variação e pode listar separadamente digests observados no mesmo período como `candidate_only`; nunca atribui o contador a um digest. Coleta parcial, indisponível, limitada ou sem evidência acionável produz `insufficient_data`, porque ausência de evento não prova ausência de problema. Diagnósticos derivados de registros sintéticos mantêm `synthetic=true`.

Na tela, **Diagnóstico assistido** aparece antes da timeline, com cartões compactos que abrem os detalhes em uma janela lateral. IDs de transação/processo, estados, início, objetos e demais evidências disponíveis recebem rótulos legíveis; SQL sanitizado ou fingerprint aparece conforme as capabilities. As consultas também usam cartões compactos em vez de tabela larga, preservando o layout responsivo.

O botão **Baixar relatório para IA** cria no próprio navegador um JSON autocontido a partir da resposta já autorizada. O arquivo inclui diagnósticos, ranking, timeline, séries, sinais e notas de interpretação, sem nova consulta ao MySQL. Redaction de SQL, identidade e isolamento do tenant permanecem exatamente os da sessão que gerou o relatório.

O Histórico MySQL não possui atualização automática: somente o botão **Atualizar** inicia nova leitura, evitando fechar detalhes durante uma análise. A lista inicial mostra os cinco diagnósticos prioritários e os dez primeiros digests; controles explícitos permitem ver todos ou voltar ao resumo.

Em locks confirmados, a interface separa **SQL bloqueado** e **SQL bloqueador**, associa cada um ao processo correspondente e descreve `blocking_seconds` como tempo observado do processo bloqueador — não como duração exata do lock.

As ações **Ver evidências** e **Ver detalhes** não expandem os cartões na lista. Elas abrem uma janela lateral dedicada, com fundo modal, fechamento explícito e tecla `Esc`, preservando a posição e a leitura resumida da tela.

Cada categoria lê no máximo 8 MiB, 20.000 linhas e 5.000 registros por requisição; cada linha é limitada a 256 KiB. O escritor aplica o mesmo limite: amostras grandes de digests são divididas em linhas sequenciadas, sem dividir um item, enquanto registros indivisíveis acima do limite são recusados e sinalizados como falha. A timeline é paginada, cada série retorna no máximo 1.000 pontos e o ranking retorna no máximo 100 digests. A leitura é incremental, aceita somente `events`, `digests` e `status`, recusa symlinks e nunca abre `state`, travas ou caminhos fornecidos pelo navegador. Uma última linha incompleta/corrompida é ignorada preservando as anteriores; corrupção intermediária marca a fonte como indisponível/parcial sem derrubar as outras categorias.

`full_sql` libera apenas o SQL já sanitizado que foi persistido pelo gravador. Sem essa capability, a resposta contém resumo sanitizado e fingerprint. `connection_identity` controla usuário, host e banco das conexões. Mensagens internas de falha, credenciais, estado bruto e caminhos do filesystem nunca entram no contrato.

A tela não possui ações administrativas ou mutáveis: não executa `KILL`, `DELETE`, `FLUSH` nem qualquer alteração no MySQL.

### Prévia sintética exclusivamente local

Quando `configuracoes/local/ambientes.php` existe, a conexão central é loopback e ainda não existe armazenamento do gravador, o comando abaixo cria uma demonstração reversível sem executar SQL no MySQL:

```bash
php observability/bin/mysql-recorder-demo.php
```

O gerador cria eventos fictícios de query longa, lock confirmado e transação longa, além de status, erros, saúde e digests com baseline/delta. Todos os registros recebem `synthetic=true`; o comando recusa ambiente não local e recusa uma segunda execução para não misturar a prévia com dados existentes. Os arquivos mantêm diretórios `0700` e arquivos `0600` e devem pertencer ao usuário que serve o painel.

Após autenticar, a prévia fica em `/observability/mysql-history`. Root vê os schemas configurados e métricas globais; clientes continuam limitados ao próprio tenant, com `full_sql` e `connection_identity` aplicados normalmente. Para rollback, pare qualquer gravador e mova o diretório `dados_usuarios/observability/mysql` inteiro para uma quarentena privada datada no mesmo filesystem; não exclua os arquivos automaticamente.

Enquanto houver registros sintéticos no período consultado, a tela mostra um aviso explícito de **Prévia com dados sintéticos locais**. O status e os erros são exibidos como métricas legíveis, sem JSON bruto, e as tabelas compactas se adaptam à largura disponível. O aviso vem apenas do marcador persistido pelo gerador local e não aparece em coletas reais.

## Logs de erro (Visão Hardness)

A tela **Logs de erro** é exclusiva do `root` e mostra as últimas 200 linhas dos arquivos explicitamente autorizados em `config/error-logs.json`. Use [error-logs.example.json](config/error-logs.example.json) como modelo; o arquivo real é ignorado pelo Git, como `config/access.json`. Caminhos enviados pelo navegador não são aceitos; symlinks e arquivos não legíveis são recusados.

## Ainda não coletado

- métricas da rede do servidor;
- lista de processos Linux e I/O detalhado;
- `server-status` Apache e status PHP-FPM;
- dashboard central entre servidores;
- suporte a MariaDB.
