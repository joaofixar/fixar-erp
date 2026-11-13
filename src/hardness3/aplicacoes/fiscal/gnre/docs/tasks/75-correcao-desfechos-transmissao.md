# Task 75 — Piloto de transmissão GNRE em lote real

## Piloto físico isolado do MVP

Implementado localmente em 06/11/2026, aguardando revisão técnica antes de
qualquer uso manual em Homologação. A rota `real_batch_pilot` é separada da
Fila sequencial (`batch_transmit`) e usa somente coortes já persistidas em
`GNRE010`/`GNRE011`. O envio exige POST, CSRF, empresa ativa, confirmação na
interface e ambiente imutável de Homologação; Produção é recusada pelo serviço.

Antes da chamada única, o lote é bloqueado transacionalmente, suas participantes
evidências individuais são revalidadas e o XML físico, hash, chave idempotente,
solicitante e estado `ENVIANDO` são persistidos. A recepção única persiste
recibo/SOAP sanitizado e transita para `RECEBIDO`, `REJEITADO` ou
`INCONCLUSIVO`; os dois últimos não são retransmitidos automaticamente.

A consulta é explícita e única por recibo. Resultados só podem finalizar uma
participante por correlação do contrato Task 65 e `situacaoGuia=0`; `1` e `2`
são rejeições, e ausência, ambiguidade, `4` ou erro mantêm bloqueio. Não há
`GNRE004`, `GNRE005`, `GNRE008`, alteração de `GNRE001` ou download/associação
de PDF nesse fluxo. Enquanto o extrator de `TDadosGNRE` do retorno não trouxer
uma evidência correlacionável, o serviço mantém conservadoramente o lote como
inconclusivo.

---

# Histórico: desfecho público persistente da transmissão individual

Correção global da apresentação de transmissão individual e da Fila sequencial.
`TransmissionOutcomePresentation` classifica a tentativa persistida sem depender da UF: recepção rejeitada mantém código/descrição públicos; uma tentativa `INCONCLUSIVA`, ou `CRIADA` sem recibo, é falha técnica segura e não reabre a confirmação de transmissão. SOAP, cURL, HTTP, TLS, URL, certificado e credenciais não são exibidos.

A transmissão converte exceção inesperada da recepção em tentativa `INCONCLUSIVA` auditável. Se a finalização não chegar a persistir uma tentativa, o PRG conserva somente o marcador público por empresa+guia na sessão; o modal o lê após reload e encerra o ciclo sem retransmissão automática. A invalidação do XML remove esse marcador ao corrigir a guia.

A Fila usa o mesmo estado lido de `task34-modal-state.php`; rejeição de recepção vai para **Rejeitadas**, e falha técnica/inconclusão não é convertida em bloqueio nem seguida de nova tentativa. `situacaoGuia=0` permanece a única autorização individual.

Validação local continua sem `GNRE004`; não foram alterados snapshots, ambientes, XML, Portal, PDF, `GNRE004`/`GNRE005` ou regras fiscais.
