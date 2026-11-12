# Operação

O worker é um serviço `systemd` contínuo. A unit de referência fixa
`WEBHOOKS_BATCH_SIZE=20`; cada rodada processa no máximo 20 deliveries por
tenant/banco e visita os tenants sequencialmente. Ao terminar uma rodada, o
serviço aguarda cinco segundos antes da próxima, sem sobreposição.

O worker exige `WEBHOOKS_HTTP_ENABLED=1`, usa lease de 90 segundos por default
e mantém semântica at-least-once. Uma lease expirada é recuperada pelo claim
normal; consumidores devem deduplicar por `Hardness-Event-Id` e
`Hardness-Delivery-Id`. Falhas de um tenant não impedem os demais, mas são
refletidas no exit code do ciclo.

Tenants internos que deliberadamente não participam da operação podem ser
não ter o schema de webhooks; nesses casos o worker os ignora. Ciclos sem
atividade não emitem logs; há registro quando ocorre entrega, recuperação de
lease ou falha.

O transporte aceita HTTPS/443, valida o destino contra SSRF, fixa DNS, não
segue redirects e registra somente metadados seguros. Retry usa os atrasos de
1m, 5m, 15m, 1h e 6h, com jitter; 2xx finaliza, falhas transitórias reagendam
e erros definitivos encerram a delivery.

`php webhooks/cli/diagnostics.php` é somente leitura e informa backlog P/R,
idade da delivery elegível mais antiga, leases expiradas e totais R/X/C por
rótulo seguro de tenant. A retenção é independente em
`webhooks/cli/retention.php`; nunca remove P/L/R.

O procedimento de instalação, validação, permissões, ativação, monitoramento e
rollback está em `PRODUCTION_DEPLOYMENT.md`.
