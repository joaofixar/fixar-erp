# Task 05 — Transmissão e persistência do recibo

**Situação:** concluída e validada em 28/10/2026. Task 06 não iniciada.

## Entrega

- `ReceptionClient.php` transmite exclusivamente em homologação com SOAP 1.2,
  Document/Literal, certificado A1 e pin TLS.
- `GNRE004` audita cada tentativa, sem senha, chave privada ou certificado.
- O XML é validado antes de qualquer chamada e a tentativa é gravada antes do
  envio. Há uma única chamada, sem reenvio automático.
- Recibo confirmado marca `P`; rejeição mantém `R`; timeout/TLS/HTTP ambíguo
  marca tentativa inconclusiva e guia `I`.

## Correção pós-homologação

Arquivos corrigidos: `src/ReceptionClient.php`, `public/index.php` e este
handoff. O WSDL capturado define a ação exata
`http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao/processar`; o cliente
usava incorretamente `GnreRecepcaoLote` e foi alinhado ao contrato, sem alterar
a tentativa ou o recibo já preservados da guia `#3`.

Critério objetivo de classificação: somente um `TRetLote_GNRE` bem-formado,
HTTP 2xx, com `situacaoRecepcao/codigo` de três dígitos diferente de `100`,
descrição não vazia e **sem** recibo confirma uma rejeição (`REJEITADA`, guia
permanece `R`). Sucesso exige simultaneamente código `100` e recibo. SOAP
Fault, HTTP não-2xx (em especial 5xx), TLS/timeout, XML SOAP inválido, ausência
de `TRetLote_GNRE`, código ausente/inconsistente ou resposta sem recibo que não
atenda à regra anterior são ambíguos (`INCONCLUSIVA`, guia `I`).

A tentativa e seu XML são gravados antes da chamada externa. Após a resposta,
`GNRE004` e o estado final da guia são atualizados na mesma transação. Em falha
da transação, ocorre rollback do retorno/estado como uma unidade: não fica
recibo persistido com guia `R`, e o registro inicial imutável da tentativa/XML
não é apagado nem sobrescrito. A interface recarrega a guia da base após a
transmissão; em `P` ou `I` mostra o histórico, mas não o botão de transmissão.

Proteção de concorrência: a primeira transação bloqueia `GNRE001` com `FOR
UPDATE`, exige estado `R`, impede nova tentativa `CRIADA`, grava XML/hash e
muda a guia para `I` antes de liberar o lock e chamar o Portal. Portanto um
clique concorrente encontra a guia bloqueada. Se o processo morrer entre essa
confirmação e a resposta externa, a tentativa continua `CRIADA` e a guia fica
em `I`, sem retorno automático a `R`; a auditoria é preservada para tratamento
posterior, sem reenvio nesta task.

## Banco e aplicação

Aplicar `scripts/atualizacaoBase.php` pelo procedimento usual. A alteração
amplia `GNRE001_Status` para `R`, `C`, `P`, `I` e cria somente `GNRE004`.
Não há produção, fila, consulta de resultado, PDF, pagamento ou NF-e.

## Testes e resultados

- Lint dos arquivos PHP no host e no PHP 7.4 do ERP; `git diff --check` sem
  saída.
- Após a correção: testes locais de classificação para sucesso com recibo,
  rejeição confirmada e SOAP Fault; sem chamada ao Portal.
- Correção final de concorrência: revisão estática confirmou o lock `FOR
  UPDATE`, a transição antecipada para `I`, a vedação de tentativa `CRIADA` e
  a remoção da função antiga `gnre_transmit()`; lint PHP no host/PHP 7 e
  `git diff --check` aprovados, sem nova chamada ao Portal.
- XML inválido: geração local recusou guia sem item antes de chegar ao cliente;
  nenhuma chamada ao Portal ocorreu.
- Falha técnica: a primeira tentativa da guia `#2` retornou HTTP 500/Fault sem
  recibo. Foi gravada como `INCONCLUSIVA` e a guia passou a `I`; não houve
  reenvio. A causa (namespace WSDL incorreto) foi corrigida antes de qualquer
  nova transmissão.
- Recepção real: a nova guia `GNRE001_Id=3`, criada em rascunho sem copiar a
  tentativa anterior, foi recebida em homologação em 28/10/2026. Tentativa 1:
  HTTP 200, código `100`, “Lote recebido com sucesso!”, recibo
  `26000000042778`, XML SHA-256
  `709e8e0621bb34c6e123b01ddc69ee7e99974bfe8ca2855532b3325ba90deac6`.
  A guia ficou em `P`; nenhum resultado foi consultado.

Não foi provocada rejeição fiscal deliberada no Portal, para não gerar tráfego
de teste desnecessário; o tratamento de rejeição é registrado pelo cliente e
mantém a guia em `R`.

## Roteiro operacional homologado

1. Criar ou abrir uma guia `R` com XML local válido.
2. Acionar **Transmitir em homologação** e confirmar o aviso.
3. Com código 100 e recibo, conferir o histórico e o estado `P`.
4. Não editar, cancelar ou reenviar uma guia `P` ou `I` nesta etapa.

## Condição para iniciar a Task 06

A Task 05 foi validada pelo usuário com recibo real. A Task 06 poderá ser
iniciada apenas mediante nova autorização expressa e deverá implementar a fila
persistente de consultas (`GNRE005`), sem alterar a auditoria já registrada.

## Registro de homologação

Em 28/10/2026, a guia manual `GNRE001_Id=2` recebeu HTTP 500 sem recibo e foi
corretamente preservada como tentativa `GNRE004` inconclusiva, deixando a guia
em `I` sem reenvio. A resposta SOAP indicou erro de desserialização. A causa
foi corrigida antes de qualquer nova transmissão: o corpo/header deve usar o
namespace do WSDL capturado, `http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao`,
e não o namespace ilustrativo `.../wsdl/...` do manual. A guia `#2` permanece
bloqueada; o reteste requer uma nova guia de homologação válida.
