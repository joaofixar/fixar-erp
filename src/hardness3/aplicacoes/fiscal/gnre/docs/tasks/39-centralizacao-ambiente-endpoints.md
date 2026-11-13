# Task 39 — Centralização de ambiente e endpoints GNRE, com Produção bloqueada

> **Nota histórica superada pelas Tasks 40 e 42.** O bloqueio indicado no
> título e nas seções desta task era o estado de sua entrega. Atualmente,
> Produção não é bloqueada artificialmente pelo módulo: `GNRE009` orienta
> somente novas guias, que exigem `GNRE007` compatível com empresa, UF e
> ambiente; as operações da guia usam exclusivamente o ambiente imutável do
> snapshot, sem fallback. Certificado válido da empresa e habilitação prévia do
> Web Service no Portal GNRE são pré-requisitos externos da operação real.

## Entrega e limite

`GnreEnvironmentResolver` passa a ser a fonte única, fechada e local dos
ambientes aceitos (`1` e `2`), seus rótulos e contratos de endpoint GNRE. Não
aceita URL de banco, POST, query string, sessão ou usuário. Valores inválidos
e serviços desconhecidos falham explicitamente.

Os clientes `ConfigUfClient`, `ReceptionClient` e `ResultClient` obtêm o
endpoint pelo resolvedor. Resultado/PDF compartilham `ResultClient`, portanto
não há endpoint adicional de PDF. Os fluxos existentes continuam chamando os
clientes no padrão explícito de Homologação (`2`), preservando o comportamento.

`GNRE009` continua sendo apenas preferência administrativa por empresa. Esta
task não a lê para selecionar ambiente operacional e não altera `GNRE007`,
`GNRE001`–`GNRE009`, migrations, guias, snapshots, XML, fila, locks, recibos,
PDF, idempotência, auditoria ou política de aprovação.

## Matriz de endpoints e situação

| Ambiente | Configuração UF | Recepção de lote | Resultado/PDF | Situação nesta task |
| --- | --- | --- | --- | --- |
| `2` — Homologação | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF` | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote` | Mantido como comportamento operacional existente. |
| `1` — Produção | `https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote` | Apenas allowlist em código; toda operação é bloqueada antes de certificado, XML ou HTTP/SOAP. |

O bloqueio público é: `A operação em Produção ainda não foi liberada para este módulo.` Não houve chamada ao Portal nesta task. A confirmação de emissão permanece exclusivamente no resultado individual com `situacaoGuia=0`.

## Diagnóstico substituído

Antes desta task, URLs de Homologação e o ambiente fixo estavam espalhados em
`ConfigUfClient.php`, `ReceptionClient.php` e `ResultClient.php`; configuração
UF e resultado/PDF também montavam `<ambiente>2</ambiente>` diretamente. As
URLs agora aparecem somente no resolvedor. As actions/namespaces SOAP legados
dos clientes não são configuráveis por entrada externa; a allowlist do
resolvedor registra também as actions, namespaces e versões por serviço.

## Validação e roteiro manual sem Portal

1. Em `?view=config`, salve Produção e confirme apenas a preferência `GNRE009`.
2. Abra uma guia existente e confirme que continua exibindo/operando em Homologação.
3. Gere e valide XML localmente; não transmita nem consulte o Portal.
4. Execute o teste sintético Task 39: ele resolve ambos os ambientes, recusa
   valores/serviços externos e chama os quatro caminhos produtivos somente até
   o bloqueio, sem chegar a cliente HTTP/SOAP.

## Próxima task

Uma task posterior deve selecionar o ambiente somente para **novos** snapshots
e guias, derivando-o do snapshot imutável `GNRE007`, sem modificar guias ou
snapshots existentes. A liberação de Produção permanece fora deste escopo e
exige decisão, evidências e autorização próprias.
