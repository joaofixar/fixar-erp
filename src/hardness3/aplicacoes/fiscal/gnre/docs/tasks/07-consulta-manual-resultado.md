# Task 07 — Consulta manual do resultado pela interface

**Situação:** corrigida globalmente em 03/11/2026, com retorno visível por POST → redirect → GET.

## Correção global de feedback

- Cada clique em **Consultar resultado** grava um flash isolado por empresa GNRE
  e guia, redireciona com `303` para a mesma guia na aba **Processar** e o
  consome uma única vez. A exceção recebe mensagem pública segura; SOAP, SQL,
  stack trace e detalhes de transporte não são mostrados.
- O GET somente relê `GNRE001`, `GNRE004` e `GNRE005`. Ele não chama o Portal,
  não retransmite, não altera XML/snapshot/itens/dados fiscais e não obtém PDF
  automaticamente.
- A aba informa estado técnico, próxima tentativa quando existir, e interpreta
  exclusivamente o SOAP persistido. `situacaoGuia=0` é a única confirmação de
  emitida/autorizada; `F`, HTTP 200, recibo, lote ou consulta final isolados
  não confirmam emissão.
- A ação é única: formulário real quando a fila `PENDENTE` está elegível;
  controle bloqueado com motivo para sem recibo, lease ativo, reagendamento,
  bloqueio, resultado final ou outro estado indisponível.

## Entrega e decisões

- A tela da guia tem a ação **Consultar resultado** somente para a própria
  guia selecionada, da empresa ativa, quando a guia está em `P`, há recibo
  `RECEBIDA`, a `GNRE005` está `PENDENTE` e sua próxima tentativa já venceu.
- A interface chama `GnreQueueWorker::consultGuide()`; ela não executa o CLI
  `queue-resultados.php` e não contém SOAP. A operação usa o mesmo claim,
  token, lease, lock global por IP, transação, idempotência, gravação em
  `GNRE005` e transição para `F` já usados pela Task 06.
- O claim é restringido simultaneamente por `GNRE001_Id` e `C004_Id`. Assim,
  um clique não pode selecionar qualquer outra guia pendente.
- `GNRE004` continua imutável como auditoria de transmissão/recepção. As
  informações de resultado (HTTP, código, SOAP, erro e data) permanecem em
  `GNRE005`; não houve migration nem nova tabela.
- Não foram criados cron, shell script, daemon, dispatcher ou consulta
  automática. Não há alteração de código NF-e.

## Estados operacionais

| Condição | Ação / mensagem |
| --- | --- |
| `P` + recibo recebido + `GNRE005=PENDENTE` elegível | Exibe botão e consulta esta guia. |
| Sem recibo/fila técnica válida | Bloqueia: guia sem recibo confirmado. |
| `PROCESSANDO` com lease vigente | Bloqueia: consulta já em andamento. |
| `PENDENTE` com próxima tentativa futura | Bloqueia: consulta reagendada/indisponível até o horário. |
| `FINALIZADA` ou guia `F` | Bloqueia: resultado final já registrado. |
| Guia fora de `P` (`R`, `I`, `C`) ou fila `BLOQUEADA` | Bloqueia por estado técnico. |
| Retorno pendente | Reagenda e informa que o Portal ainda não finalizou. |
| Falha técnica | Reagenda e apresenta a falha técnica persistida. |

Depois de cada POST, a página relê a guia e a `GNRE005` do banco antes de
renderizar: o estado exibido é o efetivamente gravado.

## Arquivos alterados

- `src/QueueWorker.php`: expôs a consulta unitária segura e preservou `run()`
  para o recurso administrativo existente. O callback opcional existe apenas
  para teste sintético e não é usado pela interface.
- `public/index.php`: carregou o worker, tratou o POST com CSRF e mostrou o
  estado técnico, mensagens e botão condicional.
- `tests/task07-manual-result-synthetic.php`: valida sem rede que o cliente
  sintético recebe apenas a guia indicada e que o código conserva filtros e
  mensagens de interface.

## Testes executados

`php aplicacoes/fiscal/gnre/tests/task07-manual-result-synthetic.php` retornou
`Task 07 synthetic checks OK`; o teste não instancia `GnreResultClient` nem
faz chamada HTTP. Também passaram `php -l` nos três arquivos e
`git diff --check`.

## Como testar pela interface

1. Abra uma guia da empresa ativa em estado `P`, com recibo recebido e fila
   `GNRE005` pendente já elegível.
2. Confirme que aparece **Consultar resultado** e acione-o uma vez.
3. A tela deve recarregar a mesma guia. Resultado pendente mantém `P` e
   reagenda a fila; resultado final marca a fila `FINALIZADA` e a guia `F`;
   falha técnica mantém a guia `P` e registra/reagenda o erro.
4. Para fila processando, reagendada ou finalizada, confirme que não há botão
   e a mensagem correspondente é exibida.

Uma validação real no Portal permanece pendente de solicitação expressa do
usuário. Não iniciar tarefa posterior sem essa validação/autorização.

## Validação real em homologação

Em 28/10/2026, a guia `#4`, no valor de **R$ 1.000,00**, foi transmitida em
homologação e teve o recebimento confirmado com HTTP `200`, código `100` e
recibo `26000000042859`. A consulta manual foi realizada pela interface, sem
retransmissão. O Portal retornou código `403`, **“Lote Processado com
pendencias”**.

A validação confirmou a transição da guia `GNRE001` de `P` para `F` e da fila
`GNRE005` de `PENDENTE` para `FINALIZADA`. A `GNRE004` da tentativa `#4`
permaneceu `RECEBIDA`, preservando exclusivamente a auditoria de recepção.

O resultado individual da guia continha motivos de rejeição que ainda não são
exibidos pela interface. Portanto, `F` significa apenas **resultado técnico
final consultado**; não representa autorização nem guia emitida.

A Task 07 está validada. A Task 08 deverá interpretar e exibir o resultado
individual antes de qualquer nova homologação. O significado fiscal/detalhamento
das pendências do código `403` e a obtenção de PDF permanecem fora do escopo
atual.

## Correção posterior — estado técnico final

Foi encontrada a guia local `#3` com `GNRE005=FINALIZADA` e retorno `403`, mas
`GNRE001_Status` vazio. A causa foi a consulta final ter sido gravada quando o
enum ainda não aceitava `F`; o MySQL coercionou o valor para vazio. A migration
atual já aceita `F`, mas não altera esse registro histórico.

- `QueueWorker::finish()` agora exige que o `UPDATE` protegido de `GNRE005`
  afete uma linha e, quando o retorno é final, que a transição `P -> F` em
  `GNRE001` também afete exatamente uma linha. Qualquer divergência lança erro
  e faz rollback da transação inteira.
- Foi criado `scripts/reparar-status-resultados-finalizados.php`, reparo CLI
  idempotente e restrito a guias com estado vazio, tentativa `RECEBIDA`, recibo
  correspondente e `GNRE005=FINALIZADA`. Ele somente define `GNRE001_Status`
  como `F`; não modifica `GNRE004` nem `GNRE005` e não faz backfill na
  atualização-base.
- No ambiente local, a correção restrita atualizou uma guia: `#3` passou a
  `F`, preservando `GNRE005=FINALIZADA` e código `403`. Não houve chamada ao
  Portal.
