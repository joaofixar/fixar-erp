# Hardness MCP

Módulo MCP isolado do Framework e da REST API. O endpoint é `POST /mcp` e o
tenant é resolvido pela configuração existente da instalação.

### Perfil de instalação MCP

As instalações exclusivas são cadastradas no inventário privado
`mcp/config/installations.php`, por exemplo:

```php
'inecel-production' => [
    'hosts' => ['inecel.hardness.com.br'],
    'expected_database' => 'e018',
    'profile' => 'inecel-process-analysis-v1',
],
```

O perfil é um identificador estável, em minúsculas e separado por hífens. O
registro MCP exige o host canônico e o banco esperado, mas esses valores não
ativam a feature sozinhos: ela também passa pela assinatura de
schema/configuração definida em código. A tool de rastreio da Inecel exige o valor
`inecel-process-analysis-v1`, as tabelas/colunas de rastreabilidade e o scope
`inecel.certificate:read`; assim ela não é publicada nem autorizável nas demais
instalações.

### Certificados da Inecel

`hardness.inecel.certificate.trace` parte da NF de saída da empresa da conexão,
resolve a OP pelo vínculo do lote ou pelo prefixo numérico legado e identifica
os lotes de matéria-prima consumidos. Anexos na entrada usada pela OP mantêm
prioridade. Se essa entrada não tiver anexos, a busca também segue uma
transferência entre empresas até a entrada original: chave da NF-e recebida
igual à emitida, `T008_T014_Id` do item emitido apontando ao item original e
mesmo lote no recebimento da OP, na saída da transferência e na entrada original.
Cada item e histórico deve pertencer à empresa e ao produto de sua etapa.
Esse caminho funciona mesmo quando `T014_T008_Id` da nota recebida está zerado.

O resultado mantém `inbound_*` como a origem do certificado e informa a etapa
intermediária em `transfer`, quando houver. Notas, empresas, lotes e documentos
são resolvidos pelos vínculos, sem IDs fixos. A extensão percorre uma
transferência de matéria-prima; não faz uma busca global por lote nem presume
que lotes diferentes compartilhem a mesma origem. A tool de leitura usa os
mesmos vínculos para autorizar o documento. Links de PDF identificam a empresa
da entrada original e mantêm a validade de 15 minutos.

Validação local, sem conexão com bases de clientes:

```bash
python3 mcp/tests/InecelCertificate/repository-sql.py
php -r 'require "mcp/autoload.php"; function mcpAssert($ok, $message) { if (!$ok) throw new RuntimeException($message); } require "mcp/tests/Capabilities/foundation.php";'
```

O teste SQL captura os SELECTs gerados pelo repositório e os executa sobre
fixtures em memória no SQLite, adaptando somente as funções específicas do
MySQL. Ele cobre duas origens distintas, lotes adicionais no mesmo item,
documentos com o mesmo nome, vínculos incompletos, isolamento por empresa e
autorização da leitura. Não substitui a homologação do plano de execução no MySQL.

## Namespace de tabelas

`MCP` é reservado exclusivamente a este módulo. As tabelas por tenant são
`MCP001` (conexões), `MCP002` (credenciais) e `MCP003` (auditoria). A fundação
OAuth 2.1 reserva `MCP004` a `MCP012` para clients, grants, códigos, tokens,
redirect URIs e snapshots relacionais de scopes. Nesta etapa essas tabelas não
ativam OAuth, discovery nem alteram o endpoint. APPs e outras funcionalidades não
devem reutilizar esse prefixo.

`MCP003` é uma auditoria própria, sem integração com a auditoria do Framework.
Ela possui índice temporal e retenção configurável por `MCP_AUDIT_RETENTION_DAYS`
(365 dias por padrão). Esta etapa não executa exclusão automática: arquivamento ou
remoção deve ocorrer em lotes por procedimento operacional aprovado, preservando
registros ligados a mutações administrativas críticas até o fim da retenção.

## Credenciais e integração com Claude

O suporte a credenciais próprias do MCP permanece implementado no módulo para uso
futuro e outros clientes compatíveis. Ele está temporariamente oculto da interface
administrativa para evitar ambiguidade: a integração atualmente homologada com o
Claude utiliza o fluxo OAuth, não essas credenciais. Essa ocultação é somente
visual; não remove, altera ou revoga credenciais já existentes.

## Configuração de produção

São obrigatórios `MCP_CREDENTIAL_PEPPER_CURRENT_VERSION`,
`MCP_CREDENTIAL_PEPPERS_JSON`, `MCP_RATE_LIMIT_KEY_PEPPER` e
`MCP_RATE_LIMIT_DRIVER=apcu`. `MCP_CREDENTIAL_PEPPERS_JSON` é um objeto JSON de
peppers externos por versão, por exemplo `{"v1":"<segredo antigo>","v2":"<segredo
atual>"}` com `MCP_CREDENTIAL_PEPPER_CURRENT_VERSION=v2`. Durante a migração,
as versões antigas devem permanecer presentes para validar credenciais existentes.
`MCP_CREDENTIAL_PEPPER_V1` é aceito somente como compatibilidade do primeiro
deploy. Peppers são distintos e nunca armazenados no banco. Se APCu não estiver
operacional no contexto HTTP, o endpoint falha com `503`; não há fallback sem
rate limit.

## Convenção temporal

O módulo usa UTC. Ao abrir o PDO, ele executa `SET time_zone = '+00:00'`; datas
de expiração recebidas pelo serviço administrativo devem estar no formato exato
`Y-m-d H:i:s`, em UTC, e no futuro. O grace period de rotação é calculado pelo
MySQL com `UTC_TIMESTAMP()` e dura 24 horas por padrão
(`MCP_CREDENTIAL_ROTATION_GRACE_SECONDS`).

O virtual host deve encaminhar `/mcp` diretamente para `mcp/public/index.php`,
preservando um `Host` canônico e confiável.

## Contrato HTTP

Todo `POST` precisa enviar `Accept: application/json, text/event-stream` e um
`Content-Type` cujo tipo de mídia seja `application/json` (parâmetros, como
`charset=utf-8`, são aceitos). Ausência ou tipo incompatível recebe `415` antes
da resolução do tenant e da autenticação. O endpoint sempre envia
`Cache-Control: no-store`; respostas com corpo são JSON-RPC em JSON e respostas
sem corpo também declaram `Content-Type: application/json; charset=utf-8` para
impedir que PHP/Apache aplique o tipo padrão `text/html`.

Exemplo de inicialização:

```bash
curl -i https://cliente.fastcrud.com.br/mcp \
  -X POST \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' \
  -H 'Authorization: Bearer hrd_mcp_v1_<public_id>_<secret>' \
  --data '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"manual-test","version":"1.0"}}}'
```

Depois de `initialize`, cada chamada deve incluir
`MCP-Protocol-Version: 2025-11-25`. A ausência desse cabeçalho é rejeitada: o
servidor adota política estrita e suporta exclusivamente essa versão do MCP.

## Tools

Tools são indexadas em `src/Tools/ToolRegistry.php`, compostas por módulos
explícitos através de `src/Tools/Catalog/ToolCatalogFactory.php`; o dispatcher
não contém condicionais por Tool. Cada Tool declara nome público, descrição,
`inputSchema` JSON e scopes obrigatórios. Os nomes seguem
`hardness.<dominio>[.<subdominio>].<acao>` e scopes seguem
`<dominio>[.<subdominio>]:<capacidade>`, sempre com o menor privilégio
necessário. A arquitetura, convenções e checklist para extensões estão em
[`docs/modular-tools.md`](docs/modular-tools.md).

`hardness.company.get_current` exige
`company:read`, aceita exclusivamente o objeto vazio:

```json
{"type":"object","properties":{},"additionalProperties":false}
```

O `C004_Id`, tenant, host, banco, tabela, SQL e filtros nunca são recebidos da
chamada MCP. A Tool usa somente `ExecutionContext::getCompanyId()` e a conexão
do tenant já resolvida pelo endpoint. `tools/list` omite Tools sem os scopes
necessários; `tools/call` valida nome, argumentos, schema e scope antes da
execução.

Relatórios financeiros: `hardness.finance.receivables_summary` exige
`finance:receivables:read` e `hardness.finance.payables_summary` exige
`finance:payables:read`. Ambos consolidam títulos pelo vencimento (dia, mês,
parceiro, portador, origem ou situação) e retornam total, liquidado, em aberto
e vencido. Contas a receber excluem títulos cancelados e agrupados; os vínculos
com NF de saída e boleto são apresentados apenas como origem. Contas a pagar
identificam NF de entrada e CT-e como origem. Nenhuma Tool expõe dados bancários
sensíveis, como linha digitável ou código de barras.
`hardness.finance.cash_flow_summary` exige ambos os escopos e combina os saldos
em aberto a receber e a pagar por semana ou mês, apresentando o saldo líquido.

As consultas públicas adicionais são `hardness.partner.search`, `hardness.partner.get_by_document`,
`hardness.product.search`, `hardness.quote.search` e
`hardness.quote.get_by_id`; elas exigem, respectivamente, `partner:search`, `partner:lookup`,
`product:read` e `quote:read`. A busca de cadastros localiza clientes e
fornecedores ativos por nome, fantasia ou documento; a consulta exata por documento
não permite descoberta textual. A lista de candidatos não expõe o código interno:
após selecionar um candidato, `hardness.partner.get` retorna `code` (apresentado como
“Código”), que pode ser usado como `customer_id` em `hardness.quote.create`. A consulta
exata por documento retorna esse Código apenas quando houver um único cadastro; em caso
de duplicidade, exige a seleção de um `candidate_ref` com o mesmo documento. O argumento
`document` aceita a máscara padrão, mas deve conter 11 dígitos (CPF) ou 14 dígitos (CNPJ);
não há busca por razão social ou nome fantasia. A tool `hardness.quote.generate_pdf_link` exige `quote:write`, gera o PDF com o
layout configurado para a empresa, cria um link público aleatório na área já utilizada pelo WhatsApp, `/hardness3/whatsapp/` (sem expor o diretório temporário interno), e não envia mensagens ao WhatsApp. Quando o host público do MCP não for resolvível pelo container PHP, a configuração do tenant pode definir `mcpPdfRenderUrl` (por exemplo, `http://localhost/`): ela é usada apenas pelo renderizador interno; o link retornado continua usando `urlRaiz`. Todas obtêm `C004_Id` exclusivamente de
`ExecutionContext`; argumentos de empresa são rejeitados pelo schema. Produto
não retorna preço, custo ou estoque. Orçamentos retornam apenas o resumo
aprovado e, no detalhe, os itens aprovados.

Para registrar uma Tool futura, adicione um vertical slice ao módulo de domínio
explícito, mantendo a Tool fina e a consulta em um serviço/repositório próprio
(Tools não acessam PDO diretamente). Não registre argumentos, respostas empresariais,
Authorization, segredos ou SQL em `MCP003`; a auditoria registra apenas eventos
seguros e o nome público da Tool.

### Prévia de atualização de preços

`hardness_product_prices_prepare` exige `product:prices:prepare` e um usuário ERP
responsável válido. Essa permissão permite consultar preços e gravar a prévia,
sem atualizar `D009` ou `D009A`. Não é concedida automaticamente às conexões.
As tabelas `MCP020` e `MCP021` devem existir pela atualização de base.

Exemplo de argumentos:

```json
{
  "request_key": "precos-planilha-20261001-01",
  "origin": "spreadsheet",
  "items": [
    {"line": 2, "code": "00123", "prices": {"price_1": "1250.50", "price_5": "1300.00"}}
  ]
}
```

Aceita de 1 a 1.000 linhas por padrão, também com `origin: "manual"`. Códigos são texto,
comparados exatamente na empresa da conexão. O cliente deve converter valores
da planilha para strings decimais com ponto, sem separador de milhar, e omitir
células vazias. Não há escolha implícita de preço: para atualizar todos, informe
explicitamente `price_1` a `price_5`. Cada linha precisa de pelo menos um preço.

O limite é configurado por `MCP_PRODUCT_PRICE_MAX_ITEMS` (padrão `1000`, faixa
`1` a `10000`). Por exemplo, defina `MCP_PRODUCT_PRICE_MAX_ITEMS=2000` no ambiente
do processo PHP para permitir 2.000 linhas. Schema, validação e orçamento de bytes
acompanham a configuração; reinicie/recarregue os processos e atualize o catálogo
no cliente MCP após alterar a variável. Os limites de argumentos das outras tools
continuam em 100 elementos/64 KiB. O teto HTTP compartilhado acompanha o orçamento
para receber o envelope; limites externos do proxy/webserver também precisam
comportar esse tamanho. A busca agrupa até 250 códigos por consulta ao banco.

A resposta contém `batch_id`, validade de 30 minutos em UTC, totais por situação
e todas as linhas com preços atuais/propostos. Linhas com códigos duplicados,
ambíguos, ausentes, valores inválidos ou cálculo automático ativo ficam bloqueadas.
Selecionar preço 5 sem registro `D009A` também bloqueia a linha. Zero é destacado
para aprovação explícita posterior; valores iguais ficam como `unchanged`.
O texto resume até dez linhas; `structuredContent.items` contém todas.

Repetir a mesma chave e entrada devolve a prévia persistida, sem reler os preços.
Outra entrada ou responsável usando a mesma chave é rejeitado; uma prévia expirada
exige nova chave. A gravação do lote e de todos os itens é transacional.
A resposta inclui `preview_hash`, que vincula os produtos, seleções e valores.
`prices_updated` permanece `false` na preparação; `apply_available` indica que
existe uma etapa separada de aplicação. Prévias anteriores à introdução desse
contrato de hash precisam ser preparadas novamente.

### Aplicação do lote aprovado

`hardness_product_prices_apply` exige `product:prices:apply` e responsável ERP
válido. Essa permissão é separada de preparar/consultar preços e não é concedida
automaticamente. A aplicação usa a conexão primária de escrita do tenant,
inclusive para preparar os snapshots; as buscas comuns podem usar a conexão de leitura.

Argumentos: `batch_id` e `preview_hash` retornados pela prévia, `confirmed: true`,
`allow_partial` e `allow_zero` (ambos obrigatórios). A IA deve obter aprovação
explícita do usuário após apresentar a prévia. `allow_partial: true` autoriza
somente as linhas válidas quando a prévia tem bloqueios; `allow_zero: true`
registra a aprovação explícita dos preços zerados. Enviar uma planilha por si
só não autoriza a aplicação. O usuário responsável, empresa e conexão devem
corresponder aos da preparação.

A aprovação é persistida e vinculada ao hash. Cada produto é processado em uma
transação independente, com bloqueio do lote, item e registros de produto.
Os cinco preços atuais são comparados com o snapshot completo; se algum divergir,
o item fica em conflito. Cadastro ativo, código, vínculo de empresa, existência
de D009A e IPT dos preços selecionados são revalidados. Somente os preços
selecionados e a data de alteração de preço são escritos. Os triggers SQL
continuam ativos e os cinco preços são relidos antes do commit; valores
inesperados desfazem a transação. Os hooks PHP gerais de estoque/custo não são
invocados. Esta operação não altera preços já registrados em orçamentos/pedidos,
nem dispara sincronização externa de lojas ou preenche o histórico legado T098;
o histórico obrigatório desta operação é MCP020/MCP021.

Preço e status/histórico do item são confirmados juntos. Falhas desfazem aquele
produto; itens já concluídos permanecem aplicados. Reenvie os mesmos argumentos
para retomar: itens `applied`, `unchanged`, `blocked` e `conflict` não são escritos
novamente. Uma chamada trabalha por até aproximadamente 20 segundos entre itens;
`can_resume` indica trabalho restante dentro da validade da prévia. Um comando SQL
em andamento pode ultrapassar esse intervalo. Expiração interrompe novos itens;
conflitos e itens restantes após expiração exigem nova prévia e nova aprovação.

A resposta traz contagens cumulativas, `applied_this_call`, status por linha e
indicação de retomada. `partial` não significa que nada foi aplicado. Se a conexão
cair, repetir o mesmo lote recupera o estado persistido, inclusive quando o cliente
não recebeu a confirmação de um commit. A reversão terá uma tool própria na etapa 4.

A atualização de base inclui `MCP020_Aprovar_Parcial` e `MCP020_Aprovar_Zero`,
também para instalações que já receberam as tabelas da etapa 1.

### Histórico e reversão de preços

`hardness_product_prices_history` exige `product:prices:history`. Sem argumentos,
lista até 50 lotes da empresa; `batch_id` mostra as linhas daquele lote e, em
`structuredContent.items`, os preços selecionados antes/depois. Essa permissão
é separada porque o histórico revela preços comerciais.

`hardness_product_prices_reversal_prepare` e
`hardness_product_prices_reversal_apply` exigem `product:prices:reverse` e um
responsável ERP válido. A primeira Tool recebe o lote original e uma nova
`request_key`; opcionalmente recebe `item_lines` para escolher linhas específicas.
Ela cria um lote de reversão independente, com referência auditável ao original,
e devolve uma prévia e seu hash. Não altera preços.

A prévia de reversão só considera itens originalmente aplicados. Antes de criar
cada linha, confere produto, empresa, preços selecionados, D009A e regra IPT.
Se qualquer preço selecionado tiver mudado desde o lote original, a linha fica
bloqueada com `price_changed_since_original`; nenhuma Tool escolhe ou sobrescreve
esses valores. Para aplicar, envie o novo `batch_id`, `preview_hash`,
`confirmed: true` e `allow_partial`. Uma reversão que restaura preço zero não
precisa de uma segunda confirmação de zero: a confirmação explícita da própria
reversão autoriza exatamente o valor exibido na prévia.

Reversões usam a mesma transação por produto, bloqueio, validação pós-trigger e
retomada da aplicação comum. O lote original nunca é apagado ou reescrito. Para
desfazer uma reversão, prepare uma nova reversão a partir do lote de reversão
concluído, após conferir a prévia resultante.

Testes de integração: `tests/ProductPrices/prepare.php` usa `PRICE_TEST_DSN`,
`PRICE_TEST_USER` e `PRICE_TEST_PASSWORD` definidos no ambiente. Execute contra a
base local migrada; produtos e lotes de teste ficam exclusivamente em tabelas
temporárias da conexão. `tests/ProductPrices/apply.php` cobre a aplicação e falhas
transacionais com tabelas temporárias. `tests/ProductPrices/concurrency.php` cria
e remove uma base de teste isolada no mesmo servidor local para validar duas
chamadas simultâneas e efeitos de triggers SQL; exige permissão de criar bases.
`tests/ProductPrices/schema.py` valida a estrutura com um
comando de cliente MySQL informado nos argumentos, também com tabelas temporárias.

### Validação de argumentos das Tools

`tools/call` valida os argumentos antes de verificar scopes e antes de executar a
Tool. O módulo implementa deliberadamente um subconjunto fechado de JSON Schema;
schemas com palavras-chave não suportadas falham fechados e a chamada recebe o
mesmo erro de parâmetros inválidos.

Recursos suportados:

- `object`: `properties`, `required` e `additionalProperties` booleano;
- `array`: `items`, `minItems` e `maxItems`;
- `string`: `minLength`, `maxLength` e `enum`;
- `integer` e `number`: `minimum`, `maximum`, `exclusiveMinimum`,
  `exclusiveMaximum` e `enum`;
- `boolean` e `null`, com `enum` quando necessário;
- objetos e arrays aninhados, dentro dos limites abaixo.

Não são suportados: `pattern`, `format`, `oneOf`, `anyOf`, `allOf`, `not`,
`$ref`, `const`, `default`, `uniqueItems`, schemas booleanos e qualquer
palavra-chave não listada. Essa escolha evita interpretações parciais ou regexes
controladas por schema que poderiam introduzir comportamento inesperado. Uma
Tool deve usar somente o subconjunto documentado.

Limites padrão de defesa em profundidade: argumentos serializados até 64 KiB,
profundidade máxima 8, até 100 propriedades por objeto, até 100 elementos por
array e até 8.192 bytes por string. A prévia de preços possui limites específicos conforme a configuração acima;
o transporte dimensiona o corpo HTTP para seu orçamento (mínimo de 1 MiB).
os limites menores dos argumentos evitam que uma chamada válida consuma recursos
desproporcionais antes da execução de uma Tool. Paginação e filtros devem ser
modelados dentro desses limites.

Exemplo de schema válido para uma Tool futura:

```php
[
    'type' => 'object',
    'properties' => [
        'term' => ['type' => 'string', 'minLength' => 2, 'maxLength' => 100],
        'page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 1000],
    ],
    'required' => ['term'],
    'additionalProperties' => false,
]
```

Argumentos ausentes, não-objeto, fora do schema ou acima dos limites retornam
JSON-RPC `-32602` com a mensagem segura `Invalid tool call parameters.`. Não são
retornados caminhos de campos, schema, stack trace, SQL ou valores enviados. A
auditoria registra somente `tools_call_invalid_params`; argumentos não são
persistidos. O schema vazio da Tool atual permanece válido e exige `{}`.

O roteiro de validação HTTP com credenciais temporárias, grace period e
revogação está em [docs/http-validation.md](docs/http-validation.md). Ele deve
ser executado somente após a publicação correta do vhost `/mcp`.

Exemplos, após `initialize` e com uma credencial que possua `company:read`:

```bash
curl -i https://cliente.fastcrud.com.br/mcp \
  -X POST \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' \
  -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer hrd_mcp_v1_<public_id>_<secret>' \
  --data '{"jsonrpc":"2.0","id":2,"method":"tools/list"}'
```

```bash
curl -i https://cliente.fastcrud.com.br/mcp \
  -X POST \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' \
  -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer hrd_mcp_v1_<public_id>_<secret>' \
  --data '{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":{}}}'
```
