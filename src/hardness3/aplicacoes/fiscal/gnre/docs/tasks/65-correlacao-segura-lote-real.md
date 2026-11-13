# Task 65 — Correlação segura para lote GNRE real

## Decisão

Esta task define somente contrato puro, testes locais e documentação. Ela não
habilita transmissão GNRE em lote real em Homologação nem em Produção; não cria
schema, migration, SQL, XML operacional, chamada ao Portal, consulta, PDF ou
alteração de guia. A tela/fila de processamento individual da Task 63 permanece
inalterada.

`src/GnreBatchCorrelationFingerprint.php` recebe `TDadosGNRE` já convertido em
array pelo futuro adaptador de XML. Não depende de banco, NF-e, recibo, ambiente
ou posição no lote. Antes de enviar, a futura participante deve congelar:

1. o fragmento canônico JSON de `TDadosGNRE` para auditoria;
2. o SHA-256 desse fragmento; e
3. a impressão de negócio SHA-256 (`GNRE-BATCH-CORRELATION-v1` + vetor
   canônico), usada exclusivamente para encontrar a participante.

O fragmento de auditoria preserva os elementos pertinentes de `TDadosGNRE`,
inclusive `documentoOrigem`, `camposExtras` e `identificadorGuia`, para que a
evidência congelada possa ser revisada. Seu SHA-256 não decide correlação.

O vetor v1 de **negócio** usa somente âncoras GNRE retornáveis que não são
documentais de NF-e: `ufFavorecida`, `tipoGnre`, identificação do emitente,
`itensGNRE/item` (receita, detalhamento, produto, referência, vencimento,
valores/tipos, convênio e identificação do destinatário), `valorGNRE` e
`dataPagamento`. Itens e valores são ordenados pelo conteúdo canônico, portanto
sua ordem não é uma chave.

Texto tem espaços colapsados e caixa uniforme; códigos removem espaços, mas
preservam zeros significativos; valor monetário remove zeros não significativos
e aceita ponto ou vírgula decimal; as datas aceitam `Y-m-d`, `d/m/Y`, `Y/m/d` e
`d-m-Y` e passam para `Y-m-d`; ausência vira valor vazio de forma determinística.

Deliberadamente descartados: ID interno ERP, NF-e e seus dados,
`documentoOrigem` inteiro (tipo e valor), `camposExtras`, `identificadorGuia`,
ordinal/posição de envio ou retorno, recibo, ambiente como chave,
PDF/páginas/barras/PIX, estado técnico, `situacaoGuia`, `numeroControle`,
`numeroControleFecp` e `nossoNumero`. Controle pode ser evidência devolvida,
mas nunca uma premissa da correlação: ele pode não existir no retorno.

O XSD local coloca `identificadorGuia` no grupo `dadosGnre_2.00_3`, que é usado
na composição de envio e de retorno. Porém, o XSD só informa padrão numérico e
limite de dez dígitos; o manual local não oferece semântica que descarte um
identificador interno. Portanto ele fica no fragmento auditável, mas é removido
do fingerprint até existir evidência oficial suficiente.

## Regra pura de decisão

O resultado precisa trazer `TDadosGNRE` com as âncoras mínimas: UF, tipo,
`valorGNRE` e pelo menos um item com receita e pelo menos um valor. Sem
`TDadosGNRE`, ou sem qualquer uma dessas âncoras, a decisão é
`INSUFFICIENT_EVIDENCE`. Com evidência suficiente, a impressão retornada é
comparada a todas as participantes congeladas:

| Participantes compatíveis | Decisão | Efeito futuro |
| --- | --- | --- |
| 1 | `MATCHED` | Único caso que poderá associar o resultado. |
| 0 | `UNMATCHED` | Bloqueia conclusão, emissão/autorização e retransmissão automática. |
| 2 ou mais | `AMBIGUOUS` | Mesmo bloqueio; jamais escolher pela posição. |
| Dados ausentes/parciais | `INSUFFICIENT_EVIDENCE` | Mesmo bloqueio; preservar retorno para investigação. |

`MATCHED` não é autorização. O código individual `situacaoGuia=0` continua
sendo a única confirmação de guia emitida/autorizada. Os códigos `1`, `2`, `3`
e `4` continuam não autorizados, ainda que a participante tenha sido
correlacionada.

## Contrato de ambiente e coorte futura

O mesmo algoritmo, versão de vetor, fragmento, hash, estados de decisão e
auditoria será usado em Homologação e Produção. Para cada lote futuro, ambiente
vem exclusivamente do `GNRE007` imutável de cada participante; não há fallback
nem inferência por endpoint. A admissão futura deve bloquear coorte que misture
empresa, ambiente, UF, versão de layout ou snapshots/regras incompatíveis. A
diferença entre ambientes é apenas endpoint resolvido pelo ambiente da guia e
liberação operacional, nunca correlação, idempotência ou autorização.

## Protocolo futuro de Homologação (não executado)

1. Criar duas ou mais guias completas da mesma empresa, ambiente, UF e snapshot,
   com vetores intencionalmente distintos; congelar XML/fragmento/hash/versão,
   seleção e horário antes do envio.
2. Enviar um único lote somente quando as tasks de schema, persistência e
   recepção segura estiverem prontas. Preservar XML/sha, recibo, SOAP bruto/sha,
   HTTP, resultado individual bruto, decisão e candidatos considerados.
3. Cobrir sucesso, rejeição, pendência, ausência de `TDadosGNRE` e retorno em
   ordem invertida. Confirmar que `MATCHED` ocorre sem usar ordinal e que cada
   `situacaoGuia` mantém a semântica individual.
4. Não liberar Produção se houver `UNMATCHED`, `AMBIGUOUS`,
   `INSUFFICIENT_EVIDENCE`, retorno fora do contrato, coorte mista, ou qualquer
   hipótese de associação por ordem. A prova aprovada em Homologação é aplicada
   em Produção sem mudar algoritmo ou versão do vetor.

`pdfGuias` é evidência de lote: não deve ser atribuído automaticamente a cada
guia. Até que haja comprovação oficial de relação um-para-um, preservar apenas
como PDF do lote e investigar páginas/controles separadamente.

## Validação local

`tests/task65-batch-correlation-fingerprint-synthetic.php` cobre duas guias
distintas, normalização de espaços/zeros/datas/texto, `UNMATCHED`, colisão
ambígua, inclusive duas guias que diferem somente por chave NF-e/campo extra,
ausência e parcialidade de `TDadosGNRE`, retorno invertido, presença/ausência de
`numeroControle` e os cinco valores de `situacaoGuia` com autorização exclusiva
do `0`. A diferença documental altera somente o SHA-256 do fragmento auditável,
nunca o fingerprint de negócio.

## Entrada objetiva para a Task 66

A Task 66 poderá começar quando este contrato v1 e sua evidência sintética
estiverem aceitos: o schema deverá persistir por participante o fragmento,
SHA-256, versão do vetor e decisão/candidatos, e por lote a coorte imutável
(empresa, UF, ambiente, `GNRE007` e compatibilidade), sem duplicar recibo/XML
por guia. Ela não deve iniciar transmissão.
