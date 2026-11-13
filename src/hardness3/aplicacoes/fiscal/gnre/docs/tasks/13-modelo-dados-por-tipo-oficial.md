# Task 13 — Modelo de dados por tipo oficial de GNRE

## Resultado e escopo

Concluída em 29/10/2026, esta task prepara somente o DDL completo para o
modelo analisado e aprovado na Task 12. Não altera interface, fluxo guiado,
cadastro funcional, XML, validação funcional, transmissão, consulta, fila,
SOAP nem chama o Portal GNRE.

## Contrato persistido

### Guia (`GNRE001`)

- `GNRE001_Tipo_GNRE char(1) DEFAULT NULL` guarda o `tipoGnre` oficial da
  guia. O `dados_gnre_v2.00.xsd` enumera somente `0` (Simples), `1`
  (Múltiplos Documentos de Origem) e `2` (Múltiplas Receitas). Não foi usado
  `ENUM`: a validação PHP futura deve aceitar apenas a lista confirmada pelo
  XSD e a habilitada por `TConfigUf`. O campo é nulo para não invalidar
  rascunhos de teste existentes.
- `GNRE001_Data_Pagamento date DEFAULT NULL` representa `TDadosGNRE/dataPagamento`.
  O elemento ocorre uma vez no nível da guia, após o total, e
  `TConfigUf/exigeDataPagamento` decide a obrigatoriedade futura. Também é
  nulo inicialmente para preservar rascunhos.

Os campos legados de cabeçalho `GNRE001_Codigo_Receita`,
`GNRE001_Detalhamento_Receita`, `GNRE001_Data_Vencimento`, `GNRE001_Valor` e
`GNRE001_Referencia` foram preservados sem renomear, remover ou migrar. Eles
continuam disponíveis para compatibilidade/histórico, mas não passam a ser a
fonte futura de verdade dos dados que o XML coloca em item.

### Item (`GNRE003`)

- `GNRE003_Data_Vencimento date DEFAULT NULL` corresponde a
  `item/dataVencimento` no `dados_gnre_v2.00.xsd`; portanto pertence à linha
  de recolhimento, e não ao documento de origem ou ao cabeçalho.
- `GNRE003_Convenio varchar(30) DEFAULT NULL` corresponde a `item/convenio`.
  O XSD documenta “Número do convênio” e o restringe a `TString` com
  `maxLength=30`; por isso o tamanho não foi arbitrado. A exigência será
  validada futuramente conforme `TConfigUf/exigeConvenio`.

### Valores tributários (`GNRE006`)

`GNRE006` é filha de `GNRE003`: `GNRE006_GNRE003_Id` é obrigatório e possui
índice de consulta por item. Ela guarda `GNRE006_Tipo_Valor char(2)` e
`GNRE006_Valor decimal(15,2)`, além da auditoria de criação/alteração que já é
o padrão de `GNRE003`. Não há chave única fiscal nem enumeração no banco.

Essa cardinalidade é necessária porque o XSD aceita até dez elementos
`item/valor`, cada um com tipo `11`, `12`, `21`, `22`, `31`, `32`, `41`, `42`,
`51` ou `52`; os tipos pares contemplam FCP. O Manual relaciona as regras de
FCP aos retornos 279--287. A tarefa não materializa essas regras fiscais como
restrições de banco: elas dependem da validação futura da configuração e do
tipo da guia.

O modelo segue o padrão GNRE existente, que usa IDs e índices de vínculo sem
foreign keys físicas. Assim, o `NOT NULL` torna a associação obrigatória sem
introduzir uma convenção de integridade referencial que as tabelas GNRE atuais
não usam.

## Níveis XML e compatibilidade

`dataPagamento` é global porque está em `TDadosGNRE`; `convenio` e
`dataVencimento` estão dentro de `itensGNRE/item`; e cada `valor` repete dentro
do mesmo item. Logo, uma guia tem uma data de pagamento, cada recolhimento
pode ter vencimento/convênio próprios, e os valores — inclusive FCP — são
filhos do recolhimento. Isso viabiliza os três tipos oficiais sem reinterpretar
os campos legados.

Não há backfill, cópia automática, `INSERT`, `ALTER TABLE` nem alteração de
dados nesta task. A leitura dos campos legados e a migração assistida de
rascunhos serão decididas pela task funcional posterior.

## Recriação local antes de testar o DDL

`CREATE TABLE IF NOT EXISTS` não altera tabelas já existentes. Para testar um
schema limpo no ambiente de desenvolvimento, recrie manualmente **GNRE001,
GNRE002, GNRE003, GNRE004, GNRE005 e GNRE006** como conjunto: embora as novas
colunas estejam em `GNRE001`/`GNRE003` e `GNRE006` seja nova, as demais tabelas
guardam vínculos lógicos com a guia e devem acompanhar a recriação local.

Procedimento seguro, a ser executado manualmente somente em uma base local
descartável (não foi executado por esta task):

1. Confirme a base-alvo local e faça uma cópia recuperável, por exemplo:
   `mysqldump -u USUARIO -p BASE_LOCAL GNRE001 GNRE002 GNRE003 GNRE004 GNRE005 > gnre-local-antes-task13.sql`.
2. Após conferir o backup e a base, remova na ordem de dependência:
   `DROP TABLE IF EXISTS GNRE006, GNRE005, GNRE004, GNRE003, GNRE002, GNRE001;`.
3. Execute o mecanismo usual da instalação que aplica `scripts/atualizacaoBase.php` na
   **mesma BASE_LOCAL**; ele recriará o conjunto pelos `CREATE TABLE` completos.
4. Confirme com `SHOW CREATE TABLE GNRE001`, `SHOW CREATE TABLE GNRE003` e
   `SHOW CREATE TABLE GNRE006`. Não use esse procedimento em produção nem em
   uma base que contenha guias a preservar.

## Validação entregue

`tests/task13-schema-static.php` lê o DDL sem conectar ao banco e confirma os
novos campos, o vínculo/tipo/valor de `GNRE006`, a preservação dos campos
legados e a ausência, no bloco GNRE, de `Criar_Campo`, `ALTER TABLE`, `INSERT`
ou backfill.

## Limites até a Task 14

Ainda não é possível escolher/persistir o tipo pela interface, preencher os
novos dados no cadastro, ler `GNRE006` no XML, totalizar seus valores, emitir
convênio/data de pagamento, validar `TConfigUf`, validar as regras dos tipos
0/1/2 ou migrar rascunhos. A Task 14 poderá tratar gerador e validador; ela não
foi iniciada aqui.

## Handoff

Arquivos alterados: `scripts/atualizacaoBase.php`, este contrato, a validação
estática `tests/task13-schema-static.php` e o status do projeto. A alteração é
somente DDL fonte/documentação/teste, sem execução de banco.
