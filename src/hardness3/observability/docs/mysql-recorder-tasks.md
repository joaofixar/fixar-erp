# Gravador MySQL — diário de implementação

Este arquivo é o ponto de continuidade entre as tasks. A próxima task deve lê-lo integralmente antes de alterar o gravador. Ele pode ser removido somente quando o recurso estiver concluído, documentado no README e validado no piloto.

## Regras permanentes

- Compatibilidade mínima: PHP 7.4 e MySQL 8.x/RDS.
- Todas as consultas do gravador são fixas e somente leitura.
- Não alterar crons `C039`, parâmetros do RDS, painel AWS ou instalar cron por uma task de implementação.
- Não conectar ao ambiente de produção durante desenvolvimento e testes locais.
- Não armazenar credenciais nem valores literais das consultas.
- Não aceitar caminhos ou SQL vindos do navegador.
- A instalação e o piloto em produção serão manuais, comando a comando, e inicialmente restritos ao schema `e229`.
- **Nenhuma task faz commit automaticamente.** O commit é sempre uma ação manual e deliberada do responsável após revisar diff e testes.

## Task 1 — núcleo do coletor

Estado: concluída em 2026-10-13.

Entregas:

- adaptador `mysqli` com timeout de conexão de 2 segundos e leitura de 4 segundos;
- coleta independente de locks InnoDB, processlist, transações abertas, digests, status global e erros relevantes;
- consultas longas filtradas por schema e limite configurável;
- indicação de possíveis bloqueadores MyISAM, explicitamente não confirmados;
- SQL ativo sanitizado, com literais numéricos e textuais substituídos por `?`;
- falha de uma fonte do Performance Schema sem interromper as demais seções;
- CLI manual em `--dry-run`, sem persistência.

## Task 2 — persistência e ciclos controlados

Estado: concluída localmente em 2026-10-13; ainda não instalada nem validada em produção.

### Arquivos criados

- `src/Collectors/Mysql/MysqlRecorderDeltaCalculator.php`: separa contadores absolutos e deltas, cria baselines e identifica resets, reinício e digests desaparecidos.
- `src/Collectors/Mysql/MysqlRecorderStorage.php`: JSONL diário com lock, estado atômico, permissões restritas, limite por arquivo e retenção seletiva.
- `src/Collectors/Mysql/MysqlRecorderRunLock.php`: trava não bloqueante por schema usando `flock`.
- `src/Collectors/Mysql/MysqlRecorderRunner.php`: ciclo, frequências, persistência resiliente e saúde do gravador.

### Arquivos alterados

- `src/Collectors/Mysql/MysqlRecorderCollector.php`: separação em `collectFast()`, `collectStatus()` e `collectDigests()`; `collect()` foi preservado para compatibilidade.
- `bin/mysql-recorder.php`: modos explícitos `--dry-run` e `--record`, execução única e ciclo controlado.
- `tests/run.php`: fixtures sem banco real para todos os comportamentos da Task 2 e preservação dos contratos da Task 1.
- `README.md`: operação local, layout, frequências e limites do gravador.
- `docs/mysql-recorder-tasks.md`: este diário.

`MysqliRecorderQuerySource.php` e `RecorderQuerySource.php` foram revisados, mas não precisaram ser alterados.

### Diretórios, permissões e formato

Diretório padrão, fora de `observability/public`:

```text
dados_usuarios/observability/mysql/
├── events/YYYY-MM-DD.jsonl
├── digests/YYYY-MM-DD.jsonl
├── status/YYYY-MM-DD.jsonl
└── state/<schema>.json
```

- Diretórios são criados com modo `0700`; arquivos de dados, estado e trava recebem `0600`.
- Históricos usam um objeto JSON completo por linha e escrita sob `flock` exclusivo.
- Em escrita curta ou falha de flush, o histórico volta ao tamanho anterior para não deixar uma linha parcial.
- Estado usa arquivo temporário exclusivo no mesmo diretório, `fflush` e `rename` atômico.
- Symlinks são recusados e nomes de schema/datas são validados.
- O caminho padrão não é configurável pela CLI nem pelo navegador.
- Um caminho alternativo existe somente quando `OBSERVABILITY_TEST_MODE=1` e `OBSERVABILITY_TEST_MYSQL_RECORDER_PATH` estão definidos; ele é exclusivo das fixtures.

Registros de `events` guardam locks, queries longas e transações longas, mais duração, sucesso/parcialidade, falhas sanitizadas e contagens. Registros de `status` e `digests` guardam `absolute` e `delta` em campos distintos. Quando não existe base válida, `baseline=true`, `baseline_reason` explica o motivo e `delta=null`; o acumulado nunca é apresentado como atividade do período.

O estado mantém apenas a última base cumulativa e horários internos de agendamento. Estado ausente ou JSON corrompido é tratado como primeira amostra.

### Deltas e resets

- Digests: execuções, tempo total, lock, linhas examinadas/enviadas/afetadas, erros, warnings, temporárias, temporárias em disco e consultas sem índice.
- Status: conexões e contadores globais cumulativos selecionados; `Threads_connected` e `Threads_running` permanecem como valores absolutos, pois são gauges.
- Erros globais: contadores `raised` e `handled` por número de erro.
- Nenhum delta pode ser negativo.
- Uptime menor identifica reinício do MySQL; essa informação permanece no estado até a próxima coleta de digests.
- Contador menor identifica limpeza/reset e abre nova baseline.
- Digest novo abre baseline; digest ausente na amostra seguinte aparece em `disappeared_digests`.

### Ciclo, frequências e limites

- Evento rápido: a cada intervalo do ciclo; padrão de 10 segundos.
- Duração do ciclo: padrão de 50 segundos.
- Status e erros: no máximo uma vez a cada 60 segundos, inclusive entre execuções distintas.
- Ranking de digests: no máximo uma vez a cada 300 segundos, inclusive entre execuções distintas.
- Limites aceitos pelo núcleo: duração de 1 a 300 segundos, intervalo de 1 a 60 segundos e retenção de 1 a 90 dias.
- Retenção padrão: 7 dias, somente para nomes reconhecidos `YYYY-MM-DD.jsonl` nos três diretórios históricos; não há exclusão recursiva.
- Limite padrão por arquivo diário: 16 MiB. Ao atingir o limite, a linha não é anexada e o runner registra `daily_file_limit_reached` na saúde, quando o arquivo de status ainda comporta esse registro.
- Uma trava independente por schema impede duas instâncias simultâneas. A segunda termina controladamente com código `5`; descritores e locks são liberados em encerramento normal ou exceção.
- Falha parcial de coleta ou persistência é registrada e não descarta as categorias que ainda podem ser coletadas.

### CLI

Diagnóstico sem persistência:

```bash
php observability/bin/mysql-recorder.php --database=e229 --dry-run
```

Uma única rodada persistida, apropriada para teste local manual:

```bash
php observability/bin/mysql-recorder.php --database=e229 --record --once
```

Ciclo com padrões de 50/10 segundos:

```bash
php observability/bin/mysql-recorder.php --database=e229 --record
```

Opções adicionais: `--duration=50`, `--interval=10`, `--retention-days=7`, `--long-query-seconds=15` e `--long-transaction-seconds=15`. `--dry-run` e `--record` são mutuamente exclusivos.

### Testes executados

Executados localmente, sem banco real e sem conexão de produção:

```bash
php -l observability/src/Collectors/Mysql/MysqlRecorderCollector.php
php -l observability/src/Collectors/Mysql/MysqlRecorderDeltaCalculator.php
php -l observability/src/Collectors/Mysql/MysqlRecorderStorage.php
php -l observability/src/Collectors/Mysql/MysqlRecorderRunLock.php
php -l observability/src/Collectors/Mysql/MysqlRecorderRunner.php
php -l observability/bin/mysql-recorder.php
php -l observability/tests/run.php
php observability/tests/run.php
```

Resultado: sintaxe válida e todas as suítes passaram, incluindo cálculo normal, primeira baseline, reset, digest novo/desaparecido, estado corrompido, JSONL, estado atômico, symlink, limite, retenção, trava, frequências distintas, sanitização de SQL e ausência de comandos MySQL mutáveis.

### Limitações conhecidas

- O gravador ainda não possui API, leitura histórica pelo painel, agregação ou visualização; isso pertence à Task 3.
- A saúde sobre limite é best effort: se o próprio arquivo diário de status também estiver cheio, a condição continua no resultado do processo, mas não pode ser anexada nesse arquivo.
- O ranking continua limitado aos 100 maiores digests por tempo total; um digest fora desse conjunto pode aparecer como desaparecido por mudança de ranking, não apenas por limpeza do Performance Schema.
- Não houve teste de integração com MySQL/RDS nesta task; as fixtures exercitam o fluxo sem banco real.

### Confirmações de escopo

- Produção não foi acessada nem alterada.
- Parâmetros do RDS não foram alterados.
- Crons `C039` não foram alterados.
- Nenhum cron foi instalado.
- API e tela do painel não foram implementadas.
- Nenhum commit foi criado automaticamente.

### Ajuste operacional após o primeiro teste no servidor

Em 2026-10-13, o dry-run real do `e229` terminou completo: eventos em 5,63 ms, status em 5,12 ms e digests em 142,59 ms, com 4.295 digests observados e 627 selecionados. A primeira rodada `--record --once` foi recusada antes de persistir com `mysql_recorder_symlink_refused`, pois nesse servidor `/var/www/html/hardness3/dados_usuarios` é o link operacional para `/dados/v3/dados_usuarios`.

O armazenamento e o leitor passaram a resolver uma vez o caminho real dos pais definidos pela aplicação e a operar diretamente no volume canônico. A proteção permanece estrita a partir de `mysql`: um link usado como o próprio diretório de armazenamento, categoria, arquivo, estado ou trava continua recusado. Uma fixture cobre tanto o layout legítimo com `dados_usuarios` apontando para outro volume quanto a recusa de um `mysql` simbólico. Nenhum link ou arquivo do servidor foi alterado durante o diagnóstico.

O primeiro relatório real também revelou que os diagnósticos de digest exibiam o fingerprint mesmo quando o ranking autorizado continha `sql` ou `sql_summary`. O resolvedor confundia o campo `sql` dos digests com o campo `query` dos eventos. O mapeamento foi separado, mantendo a ordem segura `sql` autorizado, `sql_summary` restrito e fingerprint como último recurso; o painel agora identifica explicitamente qual formato está exibindo. As fixtures cobrem contextos privilegiado e restrito.

## Task 3 — leitura, agregação, API e tela

Estado: concluída localmente em 2026-10-13; ainda não instalada nem validada em produção.

### Arquivos criados

- `src/Collectors/Mysql/MysqlRecorderHistoryRepository.php`: leitura incremental, read-only, limitada e resistente a corrupção dos JSONL diários.
- `src/Collectors/Mysql/MysqlRecorderHistoryService.php`: agregação, paginação, redaction e contrato `mysql-history.v1`.
- `src/Collectors/Mysql/MysqlRecorderScopeResolver.php`: resolução de schemas autorizados somente pela sessão, host e inventário do backend.
- `tests/mysql-recorder-task3.php`: fixtures específicas da Task 3, sem banco real.

### Arquivos alterados

- `src/Application.php`: injeta serviço e resolvedor de escopo no controller de produção.
- `src/Http/Request.php`: leitura escalar e controlada de query string.
- `src/Http/Router.php`: página `/mysql-history` e endpoint `GET /api/v1/mysql-history`.
- `src/Http/PrototypeController.php`: valida período/paginação e chama a API sem aceitar schema, tenant, caminho, categoria ou SQL.
- `public/assets/js/observability.js`: tela sobre a API autorizada, com timeline, séries, ranking, sinais e paginação.
- `public/assets/css/observability.css`: apresentação responsiva do histórico e de seus sinais.
- `tests/run.php`: inclui a suíte específica e mantém os contratos anteriores.
- `README.md`: contrato, autorização, limites, operação e limitações.
- `docs/mysql-recorder-tasks.md`: este diário.

Os coletores, queries read-only, CLI, formato persistido e cálculo de deltas da Task 2 não precisaram ser alterados.

### Contrato da API

`GET /api/v1/mysql-history` é somente leitura e usa o contrato `mysql-history.v1`.

Entradas aceitas:

- `from` e `to`: ambos obrigatórios quando um deles for informado, em RFC 3339, com janela máxima de 7 dias;
- `page`: inteiro positivo, padrão 1;
- `limit`: inteiro positivo, padrão 100 e máximo efetivo 200.

Sem período explícito, são consultadas as últimas 24 horas. `schema`, `tenant`, `path`, categoria, nome de arquivo e SQL não são entradas da API e são ignorados se enviados.

Campos de resposta:

- `period`: período efetivamente consultado e limite máximo;
- `scope`: quantidade de schemas autorizados, sem expor configuração ou credenciais;
- `pagination`: página, limite, total da timeline e `has_more`;
- `timeline`: locks, consultas longas, transações longas e falhas/saúde do coletor;
- `status_series` e `error_series`: baseline explícita, gauges separados e somente deltas válidos;
- `digest_ranking`: até 100 digests ordenados pelo tempo total do delta do período, com desempate por execuções;
- `signals`: baseline, reset, reinício, digests desaparecidos, coletas parciais e arquivo no limite;
- `metadata`: parcialidade, fontes indisponíveis, categorias sem dados, limites atingidos e contagens limitadas de bytes/linhas — sem caminhos.

Antes de o gravador produzir dados, a API responde `200` com arrays vazios e `no_data_categories`; falha/corrupção de uma categoria não remove dados válidos das demais. Entrada inválida responde `400` com contrato estável. Indisponibilidade inesperada responde `503` sem mensagem interna.

### Autorização, isolamento e redaction

- Cliente: o schema vem exclusivamente do host validado e da sessão corrente via `SiteTenantProvider`; nenhum parâmetro do navegador participa da escolha.
- Root: os schemas vêm do inventário autorizado da Visão Hardness (`SiteTenantProvider::all()`).
- O mesmo usuário/ID em hosts diferentes é resolvido novamente e recebe somente o schema do tenant atual.
- Se um nome de schema for ambíguo entre servidores MySQL diferentes, a leitura do cliente falha fechada. O JSONL da Task 2 identifica schema, mas não servidor, e portanto não permite isolamento seguro nessa condição.
- `full_sql=true` libera apenas o SQL já sanitizado persistido pelo gravador. Sem ela, retornam resumo sanitizado e fingerprint, sem o campo de SQL completo.
- `connection_identity=true` controla usuário, host e banco de conexão. Sem ela, esses campos são removidos.
- Mensagens internas de exceção/coleta são reduzidas a nomes de seção conhecidos. Credenciais, caminhos, arquivos de state, travas, acumulados absolutos e configuração de tenant não são retornados.

### Limites e regras de leitura/agregação

- Categorias permitidas: somente `events`, `digests` e `status`.
- Período máximo: 604.800 segundos (7 dias).
- Por categoria/requisição: 8 MiB, 20.000 linhas e 5.000 registros.
- Linha JSONL: até 256 KiB, lida por streaming; arquivos não são carregados integralmente.
- Timeline: página de até 200 resultados; séries de status/erros: até 1.000 pontos cada; ranking: até 100 digests.
- Arquivos são derivados somente de categoria conhecida e dias do período; symlinks, diretórios inseguros e troca de inode são recusados.
- A última linha inválida/truncada é ignorada sem perder registros anteriores. Linha inválida intermediária é pulada, preserva registros válidos e marca a fonte como corrompida/indisponível.
- Baselines nunca entram em séries de atividade nem no ranking. Deltas negativos não são criados pela Task 2 e nenhum acumulado `absolute` é somado ou exposto pela Task 3.
- Reset, reinício, digest desaparecido, coleta parcial e `daily_file_limit_reached` permanecem sinais explícitos.

### Testes executados

Executados localmente, sem banco real e sem conexão de produção:

```bash
php -l observability/src/Collectors/Mysql/MysqlRecorderHistoryRepository.php
php -l observability/src/Collectors/Mysql/MysqlRecorderHistoryService.php
php -l observability/src/Collectors/Mysql/MysqlRecorderScopeResolver.php
php -l observability/src/Http/Request.php
php -l observability/src/Http/PrototypeController.php
php -l observability/src/Http/Router.php
php -l observability/src/Application.php
php -l observability/tests/mysql-recorder-task3.php
node --check observability/public/assets/js/observability.js
php observability/tests/mysql-recorder-task3.php
php observability/tests/run.php
git diff --check -- observability
```

Resultado: sintaxe PHP e JavaScript válida, `git diff --check` sem erros nos arquivos rastreados e não rastreados do Observability e todas as suítes passaram. As fixtures cobrem leitura normal, múltiplos arquivos diários, última linha truncada, linha intermediária inválida, arquivo grande e limites, symlink, categoria/período inválidos, baseline, reset, reinício, digest desaparecido, ranking por delta, coleta parcial, arquivo no limite, isolamento por tenant/host, tentativa de escolher schema/tenant/caminho, `full_sql`, `connection_identity`, contrato vazio, ausência de state/caminhos/credenciais/comandos mutáveis e contratos das Tasks 1 e 2.

### Limitações conhecidas

- A tela usa por padrão as últimas 24 horas. A API já aceita período RFC 3339 de até 7 dias, mas controles visuais de calendário ainda não foram adicionados.
- O formato da Task 2 não registra identidade do servidor MySQL. Nomes de schema iguais em servidores diferentes são ambíguos; cliente falha fechado nessa situação e root vê agregação por nome de schema.
- O ranking histórico continua condicionado aos 100 digests que o coletor grava em cada amostra; mudança nesse top 100 pode produzir `disappeared_digests`.
- Corrupção intermediária preserva registros válidos, mas marca toda a categoria como indisponível/parcial para impedir aparência de completude.
- Não houve teste de integração com MySQL/RDS nem validação visual em navegador real nesta task; as fixtures exercitam arquivos e contrato sem banco.

### Confirmações de escopo

- Produção não foi acessada nem alterada.
- Parâmetros do RDS não foram alterados.
- Crons `C039` não foram alterados.
- Nenhum cron foi instalado.
- Nenhuma credencial foi armazenada ou exposta.
- Nenhum comando MySQL mutável foi implementado.
- Nenhum commit foi criado automaticamente.

## Task 4 — gravador único por servidor

Estado: concluída localmente em 2026-10-13; ainda não instalada nem validada em produção.

### Objetivo e arquitetura

O gravador persistente foi alinhado à tela ao vivo da Visão Hardness: uma conexão e um processo por servidor MySQL. As fontes globais não são mais multiplicadas pelo número de schemas.

- Processlist, locks e transações são consultados uma vez por amostra e separados em memória por schema.
- Status, erros, uptime, reinício e saúde pertencem ao escopo `server` e são persistidos uma única vez.
- Digests são obtidos por uma única consulta global, limitada a 500 linhas e executada no máximo a cada 300 segundos; depois são particionados por `SCHEMA_NAME`.
- A allowlist de schemas vem somente de `SiteTenantProvider::all()`, limitada aos bancos cujo host/porta correspondem à conexão central do `showprocess`.
- Há no máximo 100 schemas por servidor, uma trava `state/server.lock` e um estado `state/server.json`.
- Eventos sem locks, consultas longas, transações longas ou falha parcial não são persistidos, reduzindo I/O e crescimento diário.
- O modo anterior por `--database` foi mantido para compatibilidade e piloto restrito.

### Arquivos criados

- `src/Collectors/Mysql/MysqlRecorderServerRunner.php`: ciclo único, estado/trava globais, particionamento persistido e orçamento de performance.
- `src/Collectors/Mysql/MysqlRecorderSchemaProvider.php`: allowlist de schemas do mesmo servidor baseada no inventário confiável.
- `tests/mysql-recorder-task4.php`: fixtures com cinco e 35 schemas que contam as consultas executadas, sem banco real.

### Arquivos alterados

- `src/Collectors/Mysql/MysqlRecorderCollector.php`: adiciona coleta global limitada de eventos e digests, preservando as consultas e métodos anteriores.
- `bin/mysql-recorder.php`: adiciona `--all-schemas`, mutuamente exclusivo com `--database`.
- `src/Collectors/Mysql/MysqlRecorderScopeResolver.php`: root passa a receber registros globais `server`; clientes continuam limitados ao próprio schema.
- `src/Collectors/Mysql/MysqlRecorderHistoryService.php`: contrato informa presença de métricas globais e sinais de cobertura/performance.
- `src/Config/SiteTenantProvider.php`: preserva a porta configurada e isola corretamente instâncias no mesmo host com portas diferentes.
- `public/assets/js/observability.js`: apresenta limite global de digests e estouro do orçamento de coleta.
- `tests/run.php`: inclui as fixtures da Task 4.
- `tests/mysql-recorder-task3.php`: atualiza o contrato de escopo root para métricas `server`.
- `README.md` e este diário: operação, limites, testes e continuidade.

### Operação da CLI

Diagnóstico global, sem persistência:

```bash
php observability/bin/mysql-recorder.php --all-schemas --dry-run
```

Uma rodada global persistida:

```bash
php observability/bin/mysql-recorder.php --all-schemas --record --once
```

Ciclo global com os padrões 50/10 segundos:

```bash
php observability/bin/mysql-recorder.php --all-schemas --record
```

Compatibilidade restrita permanece disponível com `--database=e229`. A CLI não aceita simultaneamente `--database` e `--all-schemas`. Nenhum schema, caminho ou SQL vem do navegador.

### Orçamento de performance

Com 1, 5 ou 35 schemas, cada amostra rápida usa exatamente quatro fontes globais: ID da própria conexão, processlist, locks e transações. Status e erros usam duas consultas no máximo a cada minuto. Digests usam uma consulta no máximo a cada cinco minutos. A separação por schema ocorre em memória, portanto o número de consultas não cresce com a quantidade de bases. A coleta registra duração total e por fonte para que o piloto real confirme o orçamento antes de qualquer agendamento.

Controles adicionais:

- máximo de 100 schemas autorizados;
- limites SQL de 100 locks, 500 transações e 500 digests globais;
- timeouts de conexão/leitura existentes preservados;
- ciclo e intervalo continuam limitados a 300/60 segundos;
- limite diário de 16 MiB, retenção e rollback de escrita preservados;
- segunda instância global recusada por lock não bloqueante;
- coleta que excede o intervalo registra `performance_budget_exceeded`;
- digests que atingem 500 linhas registram `digest_coverage_limited`, evitando aparência de cobertura completa;
- falha de uma fonte gera parcialidade sem repetir consultas ou descartar as demais.

### Autorização e apresentação

- Root recebe schemas do inventário autorizado e o pseudoescopo `server` para status/erros globais.
- Cliente recebe somente o schema resolvido por sessão/host; registros `server`, outros schemas e linhas sem atribuição não entram na leitura.
- Timeline identifica o schema responsável por lock, consulta ou transação.
- Métricas globais não são duplicadas nem apresentadas como se pertencessem a um cliente.
- Redaction de `full_sql` e `connection_identity` continua aplicada pela API da Task 3.

### Testes executados

Executados localmente, sem banco real e sem conexão de produção:

```bash
find observability -type f -name '*.php' -exec php -l '{}' ';'
node --check observability/public/assets/js/observability.js
php observability/tests/mysql-recorder-task4.php
php observability/tests/run.php
git diff --check -- observability
```

Resultado: sintaxe válida e todas as suítes passaram. As fixtures de cinco e 35 schemas comprovaram que o número de consultas de processlist, locks, transações, status, erros e digests não cresce com a quantidade de bases; também validaram atribuição correta de eventos, status global único, baselines/deltas separados por schema, estado/trava únicos, allowlist por host e porta inclusive quando o host é igual e a porta difere, limite de 100 schemas, duração por fonte, compatibilidade das Tasks 1–3 e ausência de comandos MySQL mutáveis.

### Limitações conhecidas

- A consulta global de digests prioriza os 500 maiores por tempo acumulado do servidor. Schemas de baixa atividade podem não aparecer; isso é explicitado por `digest_coverage_limited` quando o limite é atingido.
- A correspondência do inventário usa host e porta textuais. Alias diferentes para o mesmo servidor precisam ser normalizados na configuração antes do piloto.
- O modo global cobre somente o servidor definido pela conexão central do `showprocess`; múltiplos servidores exigem identidades e diretórios independentes em uma evolução futura.
- Ainda não houve medição em MySQL/RDS real nem piloto visual com dados persistidos.

### Confirmações de escopo

- Produção não foi acessada nem alterada.
- Parâmetros do RDS não foram alterados.
- Crons `C039` não foram alterados.
- Nenhum cron foi instalado.
- Nenhuma credencial foi armazenada ou exposta.
- Nenhum commit foi criado automaticamente.

## Task 5 — instruções precisas

Antes de alterar qualquer arquivo, ler integralmente este diário, todos os componentes `MysqlRecorder*`, a CLI, `tests/run.php` e o README; conferir o Git e preservar alterações não relacionadas. Não fazer commit automático.

Objetivo: executar um piloto operacional manual, mensurável e reversível do gravador único por servidor, sem instalar cron e sem alterar RDS.

1. Confirmar com o responsável autorização explícita antes de qualquer conexão ao ambiente piloto/produção.
2. Validar somente por comandos read-only o usuário efetivo, diretório `dados_usuarios/observability/mysql`, permissões, espaço livre e correspondência host/porta dos schemas autorizados.
3. Executar `--all-schemas --dry-run`, registrar duração total e por fonte, quantidade de schemas/linhas e qualquer parcialidade; não persistir nessa primeira etapa.
4. Se o custo estiver dentro do orçamento acordado, executar uma única vez `--all-schemas --record --once`; inspecionar permissões, JSONL, `state/server.json`, ausência de estados por schema e limites.
5. Aguardar as frequências necessárias e repetir manualmente para validar delta de status (mínimo 60 s) e digest (mínimo 300 s), sem manter processo contínuo inicialmente.
6. Conferir na API/tela: atribuição do schema, métricas globais apenas para root, isolamento de cliente, redaction, baseline, parcialidade, cobertura limitada e orçamento excedido.
7. Medir impacto no RDS/MySQL antes/durante/depois: CPU, latência, conexões, duração das consultas do gravador e tamanho diário projetado. Definir critérios objetivos de abortar o piloto.
8. Testar rollback interrompendo o processo e movendo somente os arquivos criados pelo piloto para uma pasta de quarentena; não excluir dados automaticamente.
9. Somente após aprovação documentada propor o cron, com usuário, comando exato, frequência, timeout, lock, logs, monitoramento e rollback. Não instalar ou alterar cron nesta task.
10. Atualizar README e este diário com resultados reais, limitações, decisão de continuidade e confirmação explícita de que RDS, C039 e parâmetros administrativos não foram alterados.

## Task 5 — piloto operacional manual

Estado histórico: dry-run local concluído em 2026-10-13; rodada persistida não executada por falta de linha de base externa de cinco minutos. O antigo requisito de mais de 30 schemas foi removido pela Task 7 por não representar um critério válido de aprovação.

### Escopo e ambiente observado

- O Git já continha alterações das Tasks 1–4 e mudanças fora do escopo em `showprocess`; elas foram preservadas.
- A configuração central aponta para `127.0.0.1:3306`, sem acesso a endpoint remoto de produção.
- No host, o inventário não estava disponível e o PHP CLI não tinha `mysqli`; essa primeira tentativa falhou em 0,023 s antes de conectar. O piloto foi então repetido, com autorização, dentro do container local `hardness-php7`, que possui PHP 7.4 e `mysqli`.
- O MySQL local expôs 15 databases, incluindo schemas de sistema. Inicialmente somente `e040` estava autorizado por `/var/www/sites`; após integrar a fonte exclusivamente local `configuracoes/local/ambientes.php`, a allowlist passou a conter sete schemas deduplicados do mesmo `127.0.0.1:3306`: `e013`, `e018`, `e040`, `e043`, `e211`, `e229` e `e998`. Bases apenas visíveis em `SHOW DATABASES` continuam excluídas.
- O usuário MySQL efetivo foi `root@localhost`; nenhuma senha ou configuração de credencial foi impressa ou persistida.
- No container, `dados_usuarios/observability/mysql` ainda não existia, mas seu diretório pai estava em modo `0750`, pertencia a `www-data:www-data` e era gravável. O filesystem tinha 918.446.620 KiB disponíveis (9% utilizados).
- O primeiro dry-run real, com um schema, terminou sem parcialidade: 734 ms de parede; eventos 11,23 ms, status/erros 13,31 ms e digests 521,67 ms. Após ativar o inventário local, o dry-run com sete schemas também terminou sem parcialidade: eventos 2,36 ms, status/erros 3,48 ms e digests 114,49 ms. Por fonte rápida: conexão 0,31 ms, processlist 0,15 ms, locks 0,62 ms e transações 0,99 ms.
- Foram observados 2 processos, 1 conexão em 151 permitidas, 2 threads em execução, zero erros de limite de conexão, zero locks, zero consultas longas, zero transações longas e zero digests atribuídos ao único schema autorizado.
- As amostras instantâneas do container foram 13,91% de CPU antes e 0,56% depois, com memória de 746,2 para 746,3 MiB. Esses pontos isolados não formam uma linha de base e não permitem atribuir impacto ao gravador.
- Com sete schemas, foram encontrados 320 digests e nenhum evento rápido. Repetir todos os acumulados projetava 65,862 MiB/dia de digests. O histórico foi então compactado: valores absolutos permanecem apenas em `state/server.json`; o JSONL conserva baselines, itens com delta positivo, digests novos/resetados, desaparecimentos, indisponibilidade e cobertura limitada. Amostras sem atividade não criam linha.
- Duas amostras reais separadas manualmente por 300 segundos validaram a compactação: baseline inicial de 164.237 bytes, 14 digests ativos, zero desaparecimentos e 6.204 bytes na amostra delta. A projeção passou para 1.944.785 bytes/dia (1,855 MiB) de digests. Com aproximadamente 1,340 MiB/dia de status, o total estimado é 3,195 MiB/dia, dentro do limiar operacional e do limite por arquivo.
- O inventário do Observability passou a reutilizar `configuracoes/local/ambientes.php` somente quando esse arquivo local, ignorado pelo Git, existe. Apenas perfis com banco em loopback são aceitos, o caminho é fixo, symlinks são recusados e a ausência do arquivo preserva integralmente o comportamento de produção baseado em `/var/www/sites`.

Comando executado inicialmente, sem persistência:

```bash
php observability/bin/mysql-recorder.php --all-schemas --dry-run
```

Resultado final do dry-run no container: código `0`, coleta completa e sem persistência. A primeira tentativa no host retornou de forma sanitizada `mysql_recorder_mysqli_extension_unavailable`; a implementação agora detecta essa dependência antes de tentar conexão e não inclui detalhes do driver em falhas de conexão. Nenhum JSONL, estado ou trava operacional foi criado.

### Critérios objetivos de interrupção

O piloto deve ser interrompido, sem executar `--record`, se qualquer condição ocorrer:

- pré-flight: endpoint não aprovado, inventário diferente dos schemas esperados, usuário efetivo inesperado, `mysqli` ausente, destino sem escrita segura ou menos de 1 GiB livre;
- duração: coleta total maior que o intervalo de 10 s, qualquer fonte acima de 4 s, ou três amostras consecutivas da mesma fonte acima de 2 s;
- CPU: aumento de 10 pontos percentuais ou de 20% sobre a linha de base por cinco minutos, usando o maior dos dois limites;
- latência: aumento de 20% no p95 ou de 10 ms por cinco minutos, usando o maior dos dois limites;
- conexões: aumento de 10% de `max_connections`, menos de 20% de folga, ou erro `max_connections` durante a rodada;
- arquivos: projeção de qualquer JSONL diário acima de 12,8 MiB (80% do limite de 16 MiB), `daily_file_limit_reached`, linha acima de 256 KiB ou crescimento sem eventos úteis;
- integridade: fonte parcial indispensável, evento sem schema, evento vazio persistido, mais de um estado/trava de servidor, status global duplicado, exposição de caminho/estado/credencial, falha de isolamento ou SQL não sanitizado.

As comparações de CPU, latência e conexões exigem linha de base externa de cinco minutos antes e depois da amostra. Ausência dessas métricas é motivo para não aprovar continuidade, não evidência de impacto zero.

### Decisão e etapas não executadas

O tempo e a projeção compactada de volume com sete schemas ficaram dentro dos limites, mas faltou a linha de base externa de cinco minutos para CPU e latência. Por esse critério, `--all-schemas --record --once` não foi executado e não houve validação com dados operacionais na API/tela. As fixtures de 35 schemas da Task 4 comprovam custo constante de consultas e a suíte confirma que digests inalterados não crescem o histórico, mas elas não substituem o piloto operacional.

Para aprovação completa ainda será necessário um host piloto explicitamente autorizado e métricas externas por pelo menos cinco minutos. Não se deve incluir automaticamente outras bases apenas porque `SHOW DATABASES` as torna visíveis.

### Validações locais concluídas

Foram executados com sucesso, sem banco real:

```bash
find observability -type f -name '*.php' -print0 | sort -z | xargs -0 -n1 php -l
php observability/tests/mysql-recorder-task3.php
php observability/tests/mysql-recorder-task4.php
php observability/tests/mysql-recorder-task5.php
php observability/tests/run.php
node --check observability/public/assets/js/observability.js
git diff --check -- observability
```

As fixtures específicas preservam os contratos de API/tela, isolamento por tenant, `full_sql`, `connection_identity`, ausência de caminhos/estado/credenciais na API, estado/trava globais, status único, ausência de eventos vazios, baselines/deltas por schema e custo constante com 35 schemas. Também confirmam que acumulados de digest não são duplicados no histórico, amostras inalteradas não criam linha, atividade e baselines permanecem disponíveis, `mysqli` ausente interrompe antes da tentativa de conexão e a CLI não aceita entrada do navegador nem caminho operacional.

### Prévia visual local

Foi adicionado `bin/mysql-recorder-demo.php`, disponível somente em CLI e condicionado à existência do inventário local, conexão central em loopback e ausência de armazenamento anterior. Ele não se conecta ao MySQL nem executa SQL; produz somente JSONL sintético marcado, usando as mesmas proteções de `MysqlRecorderStorage`, e recusa uma segunda execução.

A prévia local criou três eventos não vazios (`long_query` em `e998`, lock em `e229` e transação longa em `e043`), três registros de status/saúde, seis registros de digest com baseline/delta e um único `state/server.json`/`state/server.lock`. Diretórios ficaram em `0700`, arquivos em `0600` e a propriedade foi ajustada para `www-data:www-data`, usuário do painel local.

A agregação root retornou uma query longa, um lock, uma transação longa, um alerta de saúde, dois pontos de status, dois pontos de erro e três digests no ranking. A leitura cliente de `e998` retornou somente um evento e um digest, sem métricas `server`; SQL completo, usuário e host foram removidos sem as capabilities. A resposta não continha acumulados absolutos, caminhos ou marcadores de credencial. A leitura direta como `www-data` passou e `/observability/mysql-history` respondeu com redirecionamento esperado para autenticação.

A tela identifica essa prévia com um aviso amarelo derivado de `synthetic=true` nos arquivos, sem marcar históricos reais. Deltas e gauges de status/erros são apresentados como métricas nomeadas, datas e horários ocupam linhas separadas e as tabelas compactas não exigem rolagem horizontal. O contrato expõe somente o booleano `metadata.synthetic`/`signals.synthetic_data`; não expõe origem, caminho ou conteúdo interno do arquivo.

### Rollback reversível

Se uma futura rodada persistida for aprovada, interromper qualquer processo manual, confirmar que não existe gravador ativo e mover — sem excluir — apenas os arquivos criados pelo piloto (`events`, `digests`, `status`, `state/server.json` e `state/server.lock`) para uma pasta privada de quarentena datada no mesmo filesystem. Registrar proprietário, modos, nomes e tamanhos antes/depois. A restauração consiste em recolocar esses arquivos no diretório original com diretórios `0700` e arquivos `0600`. Não remover dados anteriores, não executar retenção manual e não alterar banco, RDS ou cron como parte do rollback.

### Confirmações de escopo

- Produção não foi acessada nem alterada.
- Parâmetros do RDS não foram alterados.
- Crons `C039` não foram alterados.
- Nenhum cron foi instalado ou proposto para instalação.
- Nenhum comando MySQL mutável foi executado.
- Nenhuma credencial foi armazenada ou exibida.
- Nenhum processo contínuo foi iniciado.
- Nenhum commit foi criado automaticamente.

## Task 6 — diagnóstico assistido do histórico

Estado: concluída localmente em 2026-10-13; sem conexão ou alteração de produção.

### Implementação

- `MysqlRecorderDiagnosticEngine.php` adiciona uma camada determinística, testável e independente da interface, trabalhando somente sobre o contrato histórico autorizado.
- O contrato `mysql-history.v1` ganhou `diagnostics`, sem expor acumulados brutos, caminhos, credenciais ou estado interno.
- Cada diagnóstico inclui nível, categoria, schema autorizado, título, explicação, evidências, recomendação, limitação, horário/período e SQL sanitizado ou fingerprint conforme as capabilities.
- Foram implementadas categorias para lock confirmado, consulta longa, transação longa, crescimento de `Slow_queries`, tempo relevante de digest, linhas examinadas por execução, relação examinadas/retornadas, uso sem índice ou sem bom índice, temporárias em disco, tempo em locks, erros MySQL, pressão de conexões e coleta insuficiente.
- `Slow_queries` permanece global: digests do mesmo período aparecem apenas como candidatos separados e marcados `candidate_only`, nunca como correlação confirmada.
- O ranking de digests expõe execuções, tempo total, média calculada do período, maior tempo identificado como acumulado, locks, linhas, erros, alertas, temporárias, sinais de índice e primeira/última ocorrência.
- A tela recebeu a seção **Diagnóstico assistido** antes da timeline e cartões responsivos com detalhes em janela lateral.
- Os detalhes agora identificam transação/processo, estados, horários, linhas, tabelas e lock quando esses campos existem, com rótulos em português; SQL/fingerprint de consulta e transação foram ligados corretamente ao diagnóstico.
- **Baixar relatório para IA** gera no navegador um JSON sanitizado e autocontido usando somente o contrato autorizado, sem endpoint, caminho ou consulta adicional.
- A tela de histórico passou a atualizar somente sob ação manual, preservando cartões abertos durante a análise; mostra inicialmente cinco diagnósticos e dez digests, com controles para expandir ou recolher as listas.
- Locks confirmados apresentam separadamente o SQL bloqueado e o SQL bloqueador, associados aos respectivos processos; a idade observada do bloqueador não é rotulada como duração do lock.
- Diagnósticos e digests deixaram de expandir dentro da lista: suas ações de detalhes abrem uma janela lateral modal dedicada, fechável pelo botão, fundo ou tecla `Esc`.
- A lista de digests abre em **Para investigar**, exibindo os motivos determinísticos de cada item, e oferece as visões **Mais lentas**, **Maior impacto**, **Mais frequentes** e **Ver todas** sem nova consulta ao MySQL.
- A ordem visual prioriza diagnósticos e consultas investigáveis; timeline e **Contadores e estado do MySQL** aparecem depois como evidências complementares.
- A demonstração local preserva lock confirmado em `e229`, transação longa em `e043`, consulta longa em `e998`, aumento global de três consultas lentas e digests com linhas examinadas, temporárias em disco e uso sem índice. Todos permanecem marcados como sintéticos.

### Conclusões e limitações

- Consulta longa confirma duração, não causa; transação longa não é declarada bloqueadora sem relação de lock.
- Sinal de índice orienta investigação e não recomenda criação automática.
- Pressão de conexões sem `max_connections` e linha de base permanece indício, não confirma saturação.
- Ausência de evento, fonte parcial/indisponível e cobertura limitada geram informação insuficiente; não provam normalidade.
- O maior tempo por digest vem do acumulado observado no Performance Schema e não é exclusivo da janela.
- A continuidade operacional permanece condicionada ao piloto manual autorizado e aos critérios da Task 5; esta task não iniciou processo contínuo.

### Validação

Foi criada `tests/mysql-recorder-task6.php`, cobrindo os quatro níveis, lock confirmado, transação sem atribuição indevida de bloqueio, `Slow_queries` sem atribuição a digest, sinais de índice e temporária em disco, informação insuficiente, sintéticos, redaction, isolamento e ausência de estado/acumulados/comandos mutáveis.

Comandos obrigatórios executados localmente: sintaxe de todos os PHP do Observability, `php observability/tests/mysql-recorder-task6.php`, `php observability/tests/run.php`, `node --check observability/public/assets/js/observability.js` e `git diff --check -- observability`.

### Confirmações de escopo

- Produção e RDS não foram acessados nem alterados.
- Parâmetros MySQL/RDS e crons `C039` não foram alterados.
- Nenhum cron foi instalado e nenhum processo contínuo foi iniciado.
- Nenhuma consulta nova ao MySQL, comando mutável ou análise automática de plano foi adicionada.
- Nenhum commit foi criado automaticamente.

## Task 7 — cobertura, consolidação e preparo operacional

Estado: concluída localmente em 2026-10-13; sem conexão ou alteração de produção.

### Implementação

- A consulta global de digests permanece única e executada no máximo a cada 300 segundos, mas deixou de selecionar apenas por tempo total. Uma CTE com funções de janela combina os 20 principais por schema e os 200 principais por tempo total, execuções, linhas examinadas, lock e sinais de erro/temporária, com deduplicação natural e limite final de 2.000 itens.
- O coletor solicita uma linha sentinela adicional. Quando ela existe, descarta a sentinela e persiste `digest_coverage_limited`, sem apresentar o conjunto como completo.
- `Performance_schema_digest_lost` passou a fazer parte de status e deltas; valor acumulado maior que zero vira sinal explícito `digest_entries_lost` e marca a resposta histórica como parcial.
- Locks, consultas ativas longas e transações agora expõem flags independentes de cobertura quando alcançam seus limites fixos.
- O histórico consolida observações consecutivas do mesmo tipo/schema/processo/fingerprint com intervalo máximo de 30 segundos. O incidente resultante mantém primeira e última observação, `sample_count`, maior duração e os detalhes mais recentes.
- A tela mostra a quantidade de amostras consolidadas e todos os novos sinais de cobertura.
- A exigência de mais de 30 schemas foi retirada do piloto. O ambiente piloto deve representar o servidor real; quantidade de schemas não é medida de segurança nem de qualidade.
- O README determina que o gravador persistente execute com o mesmo usuário do painel (`www-data` no ambiente local validado), preservando `0700`/`0600` sem criar arquivos ilegíveis pelo servidor web.
- Foi documentada a limitação de erros 2006/2013: falhas geradas somente no cliente PHP não são garantidas pelos contadores do servidor e exigirão instrumentação separada da aplicação, fora desta task.

### Validação

Foi criada `tests/mysql-recorder-task7.php`. As fixtures cobrem seleção multidimensional fixa, sentinela e limite de digests, `Performance_schema_digest_lost`, limites rápidos, consolidação de três amostras em um incidente, maior duração e propagação dos sinais até a API.

Foram executados localmente `php -l`, a suíte completa `php observability/tests/run.php`, `node --check observability/public/assets/js/observability.js` e `git diff --check -- observability`. A nova CTE também foi compilada e executada com sucesso em dry-run somente leitura no MySQL 8.0 local: sete schemas, 304 digests no maior grupo retornado e 491,54 ms na fonte de digests. Naquele momento, cobertura era inferida apenas pela linha sentinela; a Task 8 corrigiu essa definição usando a contagem total. É uma amostra funcional, não uma linha de base de desempenho. Produção, RDS e crons não foram acessados ou alterados; nenhum processo contínuo foi iniciado e nenhum commit foi criado automaticamente.

## Task 8 — cobertura real e desaparecimento de digests

Estado: concluída localmente em 2026-10-13; sem acesso a produção.

### Correção

- A CTE global passou a retornar `COUNT(*) OVER()` antes do recorte. `coverage_limited` compara o total do Performance Schema com a quantidade efetivamente selecionada e não depende somente de atingir 2.000 linhas.
- `result_limit_reached` continua indicando apenas o limite físico e a linha sentinela; `total_digest_rows` e `selected_digest_rows` tornam o motivo da amostragem verificável.
- Todos os rankings usam `DIGEST ASC` como desempate, impedindo troca aleatória entre linhas com a mesma métrica.
- Quando a cobertura é amostrada, o runner não produz `disappeared_digests`, pois ausência no ranking não comprova remoção do Performance Schema. Deltas dos itens presentes e baselines de itens novos continuam válidos.
- O modo compatível `--database` também ganhou linha sentinela, desempate determinístico, sinal de cobertura e supressão do falso desaparecimento.
- Com cobertura completa, o cálculo original de digest realmente desaparecido permanece ativo e continua coberto pelas fixtures anteriores.

### Validação

Foi criada `tests/mysql-recorder-task8.php`, cobrindo amostragem abaixo do limite físico, metadados de cobertura, SQL determinístico e troca de um item do ranking sem falso desaparecimento. A suíte completa passou.

O dry-run somente leitura no MySQL 8.0 local confirmou a consulta final: sete schemas, 601 digests totais, 424 selecionados, `coverage_limited=true`, `result_limit_reached=false`, nenhuma falha e 181,92 ms na fonte de digests. Isso confirma que a correção distingue amostragem de limite físico; não constitui linha de base do servidor de produção.

Produção, RDS, parâmetros e crons não foram acessados ou alterados. Nenhum processo persistente foi iniciado e nenhum commit foi criado automaticamente.

## Task 9 — compatibilidade de tamanho entre escrita, estado e leitura

Estado: concluída localmente em 2026-10-13; sem acesso a produção.

### Correção

- `MysqlRecorderStorage::MAX_RECORD_BYTES` passou a definir os mesmos 256 KiB aceitos pelo repositório histórico, removendo a divergência entre escritor e leitor.
- Uma amostra de digests acima desse limite é dividida automaticamente em múltiplos registros JSONL completos. Cada parte recebe `collection.digest_chunk.index/total`; itens aparecem exatamente uma vez e `disappeared_digests` fica somente na primeira parte.
- Registros não divisíveis acima de 256 KiB são recusados antes da abertura/escrita do arquivo, em vez de criar conteúdo que o painel rejeitaria depois.
- O estado de digests conserva somente `digest` e os acumulados necessários ao próximo delta. SQL, datas e métricas derivadas deixam de ser duplicados em `state/server.json`.
- Leitura e escrita do estado agora compartilham limite explícito de 8 MiB; o salvamento recusa excedente antes de substituir atomicamente um estado válido.
- Fragmentação não altera agregação, autorização ou redaction: o repositório lê as partes como registros normais e o serviço histórico agrega os itens pelo digest.

### Validação

Foi criada `tests/mysql-recorder-task9.php`. A fixture persiste 250 digests com SQL longo, força múltiplas linhas, verifica tamanho máximo, sequência, preservação exata dos 250 itens e leitura integral sem corrupção. Também compacta 2.000 digests com SQL de 2.000 caracteres, confirma estado abaixo do antigo limite de 4 MiB, executa save/load atômico e valida a recusa de um evento indivisível grande.

Todas as suítes das Tasks 1–9 passaram, assim como lint PHP, validação JavaScript e `git diff --check`. Produção, RDS, parâmetros e crons não foram acessados ou alterados; nenhum processo persistente foi iniciado e nenhum commit foi criado automaticamente.
