# Task 40 — Ambiente imutável em novos snapshots e guias GNRE

> **Nota histórica superada pela Task 42.** As referências ao bloqueio de
> Produção abaixo registram o estado desta entrega. No estado vigente,
> Produção não é bloqueada artificialmente pelo módulo. `GNRE009` escolhe o
> ambiente apenas para novas guias, que exigem `GNRE007` compatível com empresa,
> UF e ambiente; transmissão, consulta de resultado, fila e PDF usam somente o
> ambiente imutável desse snapshot, sem fallback. Certificado válido da empresa
> e habilitação prévia do Web Service no Portal GNRE são pré-requisitos externos
> para a operação real em Produção.

`GNRE009` é lida somente no início de uma nova guia. O ambiente pretendido filtra a busca de `GNRE007` e é conferido novamente antes da criação da `GNRE001`. A guia passa então a usar exclusivamente o ambiente do snapshot vinculado; alterar a preferência posteriormente não altera guia, XML, transmissão, resultado ou PDF.

| Preferência | Snapshot aceito | Nova guia | Operação |
| --- | --- | --- | --- |
| `2` Homologação | mesma empresa, UF e ambiente `2` | criada vinculada ao snapshot | Homologação preservada |
| `1` Produção | nenhum nesta task | não criada | bloqueada antes de Portal, snapshot ou registro parcial |

Produção continua bloqueada com `A operação em Produção ainda não foi liberada para este módulo.` Não há fallback para Homologação. Guias legadas sem snapshot seguem sem inferência e bloqueadas pelos fluxos existentes. Não houve migration, alteração de banco, guia real ou chamada ao Portal.

Fila, transmissão e PDF carregam o ambiente exclusivamente do `GNRE007` vinculado à guia. A fila valida empresa, tentativa, guia, snapshot e UF antes do cliente; um snapshot ausente ou incompatível falha sem consulta. A apresentação mostra o rótulo derivado do snapshot ou `Ambiente indisponível`, nunca Homologação presumida.

A resolução de ambiente da fila ocorre dentro do lock global já existente. Falhas de vínculo, ambiente e bloqueio de Produção são convertidas em resultado técnico de nova tentativa; assim a finalização normal limpa token e lease antes do reagendamento. O `finally` preserva a liberação do lock inclusive nessas falhas e callbacks injetados continuam recebendo somente empresa e recibo.

O teste comportamental isolado `task40-queue-behavior-synthetic.php` injeta apenas a resolução de snapshot e a finalização em memória. Ele executa o `process()` privado por reflexão, sem banco ou Portal, e comprova: Homologação chega ao callback; Produção, snapshot ausente e exceção não chegam ao callback; todos retornam `retry` e a finalização simulada limpa estado `PROCESSANDO`, token e lease; nenhuma tentativa GNRE004 é criada e a guia continua pendente. Depois de falha, o teste obtém novamente o lock global, comprovando o `finally`.
