# Task 06 — Fila de consultas e recuperação

**Situação:** concluída e validada em 28/10/2026. Task 07 não iniciada.

## Entrega

- Criada somente `GNRE005`, vinculada de forma única a `GNRE004`. Ela registra
  recibo, situação, próxima tentativa, contador, token/lease temporário,
  processamento e erro técnico.
- A migration inclui idempotentemente os recibos de tentativas `RECEBIDA` das
  guias `P`; não inclui `I`, `CRIADA` ou tentativa sem recibo. A guia #3 entrou
  uma única vez pelo recibo `26000000042778`. Recebimentos futuros são
  enfileirados na mesma transação que persiste o recibo, antes de a guia virar
  `P`.
- `scripts/queue-resultados.php [limite]` é recurso técnico/manual
  administrativo, limitado a 100 itens por execução (padrão 10). Por decisão
  de escopo do MVP, não há cron, `.sh`, daemon, dispatcher multi-banco ou
  execução recorrente configurada; isto não é pendência técnica.
- O claim usa transação, `FOR UPDATE`, token aleatório e lease de cinco minutos.
  Worker concorrente não consulta item leased; lease vencido é recuperável.
- O cliente `ResultClient.php` é exclusivo da GNRE, usa A1/TLS pin próprios,
  somente homologação, SOAP 1.2 e consulta `TConsLote_GNRE` sem PDF ou arquivo
  de pagamento. Há 120 ms mínimos entre chamadas; HTTP 429 respeita
  `Retry-After` (ou 60 s) e demais falhas usam backoff limitado.
- Resultado pendente apenas reagenda. Resultado final fica exclusivamente em
  `GNRE005`; `GNRE004` preserva definitivamente os dados da recepção. O worker fecha a fila e move a guia de
  `P` para `F` (resultado final consultado). Não há PDF, pagamento ou reenvio.

## WSDL homologação

`docs/referencias/wsdl/GnreResultadoLote-homologacao.wsdl`, capturado em
28/10/2026 com A1 homologado: SHA-256
`f01a85af07f807eaa1c6757208f5779b9d87d8e69868a6360ea47f1c2aba1e0c`.
Contrato confirmado: `consultar`, SOAP action
`http://www.gnre.pe.gov.br/webservice/GnreResultadoLote/consultar` e versão
`2.00`.

## Migration e validação

Aplicar `scripts/atualizacaoBase.php`. A alteração é `GNRE001_Status` com o
novo estado técnico `F` e criação idempotente de `GNRE005`; não altera NF-e.

Antes da liberação da GNRE, a atualização-base contém o schema completo de
`GNRE005`, inclusive HTTP, código, descrição, SOAP e data/hora da última
consulta; no ambiente local, recrie `GNRE005` quando necessário. Após a migration, execute uma vez
`scripts/reparar-auditoria-consultas.php`: é idempotente, separa blocos com o
marcador legado e não depende de IDs nem remove recibos. Não há backfill de
dados operacionais na atualização-base: se encontrar marcador sem fila, a
rotina cria diretamente uma `GNRE005` `FINALIZADA` com o recibo e resultado já
preservados, sem enfileirar ou consultar o Portal.

- Lint PHP e `git diff --check` aprovados.
- Consulta real homologada da guia #3, sem nova transmissão: HTTP 200, código
  403, “Lote Processado com pendencias”. O worker encerrou `GNRE005 #1` como
  `FINALIZADA`, armazenou o resultado da consulta em `GNRE005` e deixou a guia #3
  em `F`.
- Lock concorrente validado: com o lease ativo da fila #1, outra execução do
  worker processou zero itens; o estado final foi restaurado sem consulta. O
  lease é condicionado a token e expiração e é recuperável depois do vencimento.
  Falhas técnicas são reagendadas com backoff; teste unitário do cabeçalho 429
  confirmou `Retry-After: 17` como reagendamento de 17 s.
- Não houve retransmissão: a guia #2 continua `I` com tentativa inconclusiva
  sem recibo; a #3 foi apenas consultada.

## Pendências para Task 07

Implementar pela interface GNRE a consulta de resultado acionada pelo usuário,
apresentação/classificação operacional, PDF/dados de emissão e demais telas.
Não implementar produção, pagamento, NF-e, reenvio automático ou automação
recorrente sem nova autorização.
