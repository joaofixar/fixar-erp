# Arquitetura

```text
ERP (transação de negócio) → Facade + Payload Builder → WebhookPublisher → WHK003 + WHK004 → COMMIT
                                                                  ↓
worker curto → claim/lease → validação de destino → HTTP → WHK005 + estado final/retry
```

`Webhook::publishForCompany(int $companyId, string $eventType, int $aggregateId)` usa a conexão `mysqli` atual. O builder registrado para o evento consulta o agregado nessa mesma conexão, monta o payload oficial, o Catalog o valida e o Publisher o persiste. Nenhum componente de Webhooks abre, confirma ou desfaz a transação de domínio.

Cada evento possui um único contrato público de payload, compartilhado por todos os consumidores. Os produtores ERP informam somente a empresa, o evento e a identidade do agregado; não fornecem payload público arbitrário. Campos compatíveis podem ser acrescentados futuramente ao contrato oficial.

Uma falha antes de uma Outbox válida não interrompe a operação ERP. Para não gerar ruído em produção — especialmente em bases que ainda não possuem as tabelas WHK — VEN001/VEN002 não emitem `error_log` por tentativa. `WHK005` permanece exclusivo de tentativas de delivery que já possuem `WHK004`.

Em `VEN002::faturarPedido()`, a chamada de `financial_analysis.requested` permanece após o `UPDATE T005` para status 6 e o histórico T178 já existentes. O método preserva sua propriedade transacional legada; Webhook/Publisher não iniciam, confirmam nem desfazem transações.

## RISCO CONHECIDO / DÍVIDA TÉCNICA DO FLUXO LEGADO VEN002

O método grava o status e T178 antes de chamar o Publisher, sem uma transação comum explicitamente controlada nesse trecho. Se a publicação falhar — por exemplo, erro de conexão, schema WHK ausente ou falha de insert em WHK003/WHK004 — o pedido pode permanecer confirmado em análise de crédito sem evento/outbox correspondente. A correção transacional completa depende de uma refatoração do fluxo legado aprovada separadamente.

O worker é **at-least-once**. Após HTTP bem-sucedido, uma falha antes de persistir o resultado pode causar nova entrega. Consumidores devem deduplicar por `Hardness-Event-Id` e `Hardness-Delivery-Id`.

Uma delivery sem `WHK007` é enviada sem assinatura. Quando `WHK007` está presente, o Dispatcher assina o corpo literal. Bearer, se configurado, é independente da assinatura.
