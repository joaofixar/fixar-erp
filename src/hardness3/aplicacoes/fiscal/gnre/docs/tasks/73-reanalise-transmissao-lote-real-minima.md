# Task 73 — Reanálise técnica: transmissão GNRE em lote real com modelo mínimo

## Escopo e conclusão direta

Esta é uma reanálise documental. Foram lidos exclusivamente o Manual local
GNRE v2.17, os XSDs locais e as Tasks 63 a 68, além do modelo já documentado
no projeto. Não houve SQL, banco, migration, alteração de código, chamada ao
Portal, transmissão, consulta, PDF ou alteração de guia.

**Conclusão:** as estruturas atuais `GNRE001`, `GNRE004`, `GNRE005` e
`GNRE008` não conseguem representar com segurança um lote físico único que
possua várias guias. Persistência adicional é inevitável. O menor desenho que
preserva histórico, idempotência e correlação é **um cabeçalho de lote físico e
uma tabela de participantes imutáveis**. Isso é conceitualmente a opção C;
não significa que as `GNRE010`/`GNRE011` existentes sejam, sem revisão, o
schema definitivo nem autoriza reutilizá-las para enviar ao Portal.

Uma única tabela de cabeçalho sem uma relação histórica por participante não
consegue responder, de modo auditável, quais guias compunham cada tentativa
física passada. Uma coluna mutável em `GNRE001` perde essa história quando a
guia for reaberta, cancelada ou voltar a uma nova intenção de lote.

## 1. Contrato oficial local: fatos e limites da evidência

### Fatos comprovados pelo Manual v2.17 e XSDs

| Assunto | Evidência local | Fato documentado |
| --- | --- | --- |
| XML de envio | Manual 4.1.1; `lote_gnre_v2.00.xsd` | `TLote_GNRE versao="2.00"` contém exatamente `guias`, que contém de 1 a N `TDadosGNRE`; cada ocorrência declara `versao` `1.00` ou `2.00`. É uma chamada física com várias guias. |
| Limites | Manual 4.1.5; `config_uf_v1.00.xsd` | A mensagem não pode exceder 600 KB. A configuração da UF prevê `qtdMaximas/guiasPorLote`, `itensPorGuia` e `itensPorLote`. Não há constante global de quantidade de guias no contrato local. O `maxOccurs=200` de guias no **retorno** não substitui `guiasPorLote`. |
| Recepção | Manual 4.1.2 e 4.1.6; `lote_gnre_recibo_v1.00.xsd` | A recepção é assíncrona e devolve um `TRetLote_GNRE`: ambiente, situação e, no sucesso 100, um único recibo com número, data/hora e tempo estimado. |
| Consulta | Manual 4.2; `lote_gnre_consulta_v1.00.xsd` | `TConsLote_GNRE` recebe ambiente e `numeroRecibo`, podendo solicitar PDF, arquivo de pagamento e notícias. O Manual recomenda aguardar pelo menos 30 segundos antes da consulta. |
| Resultado | Manual 4.2.2/4.2.3; `lote_gnre_result_v2.00.xsd` | O resultado traz estado do lote (400, 401, 402, 403 ou 404) e, quando há resultado, várias `resultado/guia`. Cada guia traz `situacaoGuia`: 0 sucesso, 1 inválida pelo Portal, 2 inválida pela UF, 3 erro de comunicação ou 4 pendência. |
| Emissão | Manual/XSD acima | O estado do lote 402 ou 403 não confirma guia emitida. Para cada participante, somente `situacaoGuia=0` persistida e inequivocamente correlacionada confirma emissão. |
| PDF | Manual 4.2.2; `lote_gnre_result_v2.00.xsd` | `pdfGuias` é opcional e fica em `resultado`, no nível do lote; é base64 das guias processadas com sucesso. Não há PDF aninhado em cada `resultado/guia`, nem identificador de página/guia no XSD. |

Estrutura resumida, mantendo apenas nós relevantes ao lote:

```xml
<TLote_GNRE versao="2.00" xmlns="http://www.gnre.pe.gov.br">
  <guias>
    <TDadosGNRE versao="1.00|2.00">...</TDadosGNRE>
    <TDadosGNRE versao="1.00|2.00">...</TDadosGNRE>
  </guias>
</TLote_GNRE>
```

Há uma diferença documental a respeitar: o Manual 4.1.1 descreve
`opcoesRetorno` na mensagem de envio, mas o `lote_gnre_v2.00.xsd` local
fornecido aceita apenas `guias`. Já a consulta (`TConsLote_GNRE`) possui
explicitamente `incluirPDFGuias`, `incluirArquivoPagamento` e
`incluirNoticias`. Qualquer futura implementação deve validar contra o XSD
local aplicável e tratar essa divergência como questão documental a resolver;
esta task não infere um XML alternativo.

### O que o contrato local não prova

O XSD permite `TDadosGNRE` com UFs, receitas e versões por ocorrência, mas
não há regra textual local que autorize operacionalmente misturar UFs, receitas
ou snapshots/configurações num mesmo envio. `qtdMaximas` é retornada pela
configuração de **uma UF**, o que torna especialmente inseguro usar vários
snapshots/UFs para calcular limites. O ambiente está no cabeçalho da recepção e
na consulta, logo um lote não pode misturar ambientes; empresa/titular do
certificado também não deve ser misturada. Portanto:

- empresa e ambiente mistos são proibidos;
- UF, receita, snapshot e layout mistos **não são admitidos** até prova
  contratual específica; a recomendação mínima é mesma UF, mesmo snapshot
  imutável e mesmo layout;
- não se conclui que a ordem de `resultado/guia` replica a ordem enviada;
- não há ID interno do ERP no recibo ou no resultado para correlacionar cada
  participante. `TDadosGNRE` pode estar ausente em invalidações, conforme a
  estrutura/observação do Manual, e controles retornados não são uma chave
  segura prévia.

O PDF tampouco pode ser repartido ou copiado para guias individuais apenas por
ordem de páginas. Até haver evidência contratual inequívoca, ele é evidência do
lote; `GNRE008`, que exige guia e tentativa individual proprietárias, não deve
recebê-lo.

## 2. Por que o modelo atual não basta

Hoje `GNRE004` é uma tentativa de uma única `GNRE001`: tem FK lógica direta
para a guia, sequência por guia, XML enviado, recibo, SOAP e estado de
recepção. `GNRE005` é única por `GNRE004` e agenda/serializa a consulta daquele
recibo. `GNRE008` pertence a uma guia e uma `GNRE004` individual. Este desenho
é adequado ao MVP sequencial da Task 63, que faz uma chamada física por guia.

Uma única `GNRE004` para várias `GNRE001` seria tecnicamente e semanticamente
incorreta: ela teria somente uma dona (`GNRE004_GNRE001_Id`), embora XML,
recibo, SOAP, timeout e resultado fossem compartilhados. A tentativa ficaria
ora atribuída a uma só guia, ora artificialmente duplicada em N tentativas.

- Duplicar `GNRE004` para cada guia duplica o XML e recibo físico e aparenta N
  envios. Também gera N `GNRE005` concorrentes consultando o mesmo recibo,
  tornando lease, retry, estado 400/401/403/404 e auditoria contraditórios.
- Compartilhar uma `GNRE005` por um `GNRE004` escolhido deixa as demais guias
  sem vínculo de pertencimento histórico e sem bloqueio seguro contra a fila
  individual.
- Uma única coluna em `GNRE001` poderia marcar apenas a associação atual, mas
  não a composição congelada de uma tentativa, seu fragmento/hashes, decisão
  individual, resultado bruto ou nova participação posterior.
- `GNRE008` não pode receber o mesmo `pdfGuias` em todas as guias: isso
  afirma uma relação individual que o contrato local não fornece.

Assim, XML enviado, recibo, estado de recepção, timeout/inconclusão, SOAP e
consulta devem ter **um dono: o lote físico**. Resultado, decisão de correlação
e bloqueio devem ter **um dono por participante**. Não se obtém isso apenas
rearranjando `GNRE004/GNRE005` sem alterar seu significado e introduzir
ambiguidades materiais.

## 3. Alternativas mínimas de persistência

| Opção | Estruturas/colunas | Idempotência, auditoria e correlação | Timeout, retransmissão e PDF | Complexidade/compatibilidade | Veredito |
| --- | --- | --- | --- | --- | --- |
| A. Nenhuma tabela nova | Reinterpretar `GNRE004` como lote e criar/duplicar `GNRE005`; ou usar colunas em `GNRE001`. | Não há relação N:N histórica válida. Uma `GNRE004` tem uma guia dona; N cópias falsificam tentativas físicas. A correlação e o resultado por guia não têm local próprio. | Múltiplas filas para um recibo ou guias sem bloqueio; timeout pode devolver participante à fila sequencial. `pdfGuias` não é associável a `GNRE008`. | Menor DDL aparente, mas alto acoplamento e risco operacional. PHP 7/MySQL legado não é o problema. | Rejeitada: não é segura nem auditável. |
| B. Um cabeçalho novo, participantes por coluna mínima existente | Uma tabela `Lote` para XML/recibo/fila/evidência; mais uma coluna de lote em `GNRE001` ou adaptação de `GNRE004`. | A coluna em `GNRE001` só representa vínculo atual e perde histórico/reparticipação. `GNRE004` não pode ser simultaneamente tentativa de uma guia e relação de N participantes sem duplicar evidência. | Não consegue congelar/consultar por participante de modo histórico; PDF continua sem relação individual. | DDL curto, compatível com PHP 7/MySQL, mas reduz segurança materialmente. | Não recomendada. Só seria aceitável se já houvesse relação de participantes imutável adequada — ela não existe em `GNRE001/004/005/008`. |
| C. Cabeçalho e participantes separados | `Lote`: dono do envio físico; `Lote_Guia`: uma linha imutável por guia participante. | Chave idempotente e hash da seleção/XML no cabeçalho; unicidade lote+guia e bloqueio de participação ativa. Fragmento/hash/fingerprint e decisão de correlação no participante. | Um lease/fila por lote/recibo; qualquer incerteza bloqueia todas as participantes. PDF fica no lote até associação individual comprovada. | Duas tabelas simples InnoDB, `int`, `varchar`, `datetime`, `mediumtext/blob` e índices usuais: compatível com PHP 7/MySQL legado. | Recomendada: é o mínimo que preserva fatos físicos e fatos individuais. |

### Modelo C mínimo, ainda apenas proposto

`GNRE_Lote` (nome futuro, sem decisão nesta task) deve conter somente:
identificador, empresa, UF, ambiente, referência ao snapshot imutável/layout,
chave idempotente, hash da seleção/XML, XML e SHA-256, situação, datas de
criação/envio, solicitante, HTTP/SOAP/erro de recepção, recibo/dados do recibo,
agendamento/lease de consulta e última evidência de consulta. O binário
`pdfGuias`, se retido, pertence a este cabeçalho como PDF de lote, não a
`GNRE008` individual.

`GNRE_Lote_Guia` deve conter somente: identificador, lote, `GNRE001`, ordinal
apenas para auditoria (nunca correlação), fragmento canônico congelado, SHA-256,
versão e fingerprint de correlação, situação/decisão individual, candidatos e
resultado/motivos/controles brutos ou projetados, com datas. São necessárias ao
menos unicidade `(lote, guia)`, índice por guia e regra transacional de uma
participação ativa por guia.

Não é necessário criar uma terceira tabela de eventos para a primeira versão
se o cabeçalho conservar a última evidência e os XML/SOAP de recepção/consulta
necessários à auditoria. Ela pode vir a ser justificada para histórico de todas
as consultas, mas não é pré-requisito estrutural para o modelo mínimo. Essa
economia só é aceitável se a futura task de schema definir claramente quais
evidências imutáveis não podem ser sobrescritas.

As `GNRE010`/`GNRE011` atuais se parecem com a separação C, mas são coortes
locais `PREPARADO`/`CANCELADO`: não possuem ainda XML físico, recibo, fila/lease
de lote, estados de recepção/consulta, resultado individual ou PDF de lote. O
schema futuro pode evoluí-las após revisão, substituí-las ou migrar delas; esta
task não decide isso e não pressupõe aproveitamento automático.

## 4. Regras operacionais não negociáveis

1. `situacaoGuia=0`, persistida e ligada inequivocamente a uma participante, é
   a única confirmação individual de emissão.
2. Antes da chamada externa, persistir e bloquear a composição imutável. A
   chave idempotente deve reencontrar a mesma intenção/XML; não pode criar novo
   envio por clique/retry.
3. Timeout, TLS/HTTP, SOAP inválido, ausência de recibo fora de rejeição formal,
   resultado parcial, `situacaoGuia` 3/4 ou correlação ausente/ambígua deixam o
   lote e todas as integrantes bloqueados como inconclusivos. Não autorizam
   retransmissão individual, sequencial ou em outro lote.
4. Há uma única fila/lease de consulta por recibo de lote. Estados 400/401 são
   pendentes; em 403, a pendência individual 4 permanece bloqueada; 404 não é
   licença para reenviar cegamente, apesar do texto do Manual, pois não resolve
   a incerteza local de recepção.
5. Nunca misturar empresa ou ambiente. UF, receita, snapshot e layout só podem
   ser misturados após comprovação contratual; até lá o lote é homogêneo em UF,
   snapshot e layout, e respeita seus limites e 600 KB.
6. Um PDF só entra em `GNRE008` se existir relação inequívoca com uma guia
   autorizada. `pdfGuias` coletivo fica retido no lote ou não é solicitado;
   nunca é duplicado por conveniência.

## 5. Relação com Tasks 63 a 68

| Task | Reaproveitar | Manter como estudo/infraestrutura, sem tratá-la como decisão final |
| --- | --- | --- |
| 63 | A distinção explícita entre fila sequencial individual e lote real; proteções da tentativa individual. | A interface/Fila atual não é base para envio físico único. |
| 64 | Leitura do contrato, limite por `guiasPorLote`, bloqueio conservador, recibo comum e cautela com PDF. | Sua proposta `GNRE010`–`012` era hipótese arquitetural; esta reanálise reduz a recomendação inicial a cabeçalho+participantes e reabre a decisão de eventos. |
| 65 | Fragmento congelado, fingerprint, regras `MATCHED`/`UNMATCHED`/`AMBIGUOUS`/`INSUFFICIENT_EVIDENCE` e proibição de posição. | O algoritmo continua contrato experimental até evidência futura; não é autorização para transmissão. |
| 66 | Coorte homogênea, transação, locks, idempotência local e congelamento de participantes. | `GNRE010/011` existentes só representam preparação local; não devem ser promovidas implicitamente a lote transmitido. |
| 67 | Cancelamento somente antes do envio e bloqueio da fila individual para participantes preparadas. | Estados reservados não são fluxo de Portal. |
| 68 | Separação por empresa/UF/ambiente/snapshot/layout e rollback por coorte. | Preparação automática não prova contrato de envio, correlação nem PDF. |

Nada deve ser removido ou alterado por esta task. O MVP continua com
processamento sequencial individual.

## Próximas tasks, pequenas e estritamente separadas

1. **Schema mínimo de lote físico (somente DDL/documentação):** decidir se
   evolui ou substitui `GNRE010/011`; criar cabeçalho+participantes, restrições
   e estratégia de preservação das coortes locais, sem XML, Portal ou rotas.
2. **Preparação e idempotência local:** travar seleção homogênea, limites/600
   KB, fragmentos, hashes, bloqueios e ciclo de cancelamento pré-envio, sem
   chamada ao Portal.
3. **Envio/recepção de lote:** gerar/validar XML multi-guia, persistir envio e
   recibo único, tratar timeout/inconclusivo, inicialmente sem interface ampla.
4. **Consulta, resultado e PDF:** fila/lease por lote, correlação conservadora,
   resultado individual, retenção de PDF de lote e só então decisão comprovada
   sobre PDF individual.
5. **Interface:** expor acompanhamento e ações apenas depois de persistência e
   comportamento técnico validados; manter claramente distinta da Fila
   sequencial do MVP.

## Validação desta task

Revisão documental e `git diff --check`. Não há validação contra Portal porque
esta task expressamente não realizou chamada externa.
