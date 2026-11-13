# Webhooks

O módulo entrega eventos de negócio por Transactional Outbox. O único evento de produção atual é `financial_analysis.requested`, emitido quando um pedido entra em análise de crédito.

Quando o fluxo de domínio já possui uma transação compartilhada, o ERP grava a mudança de negócio, histórico e `WHK003`/`WHK004` nela. Depois do commit, o worker entrega HTTP de forma assíncrona. `VEN002::faturarPedido()` preserva sua fronteira transacional legada: o status/histórico podem ser confirmados antes de uma falha da Outbox, risco conhecido documentado em `ARCHITECTURE.md`. Não há HTTP síncrono no fluxo do ERP.

Consulte `ARCHITECTURE.md`, `EVENTS.md`, `DATABASE.md`, `SECURITY.md`, `OPERATIONS.md`, `TESTING.md` e `PRODUCTION_DEPLOYMENT.md` para o contrato, a manutenção e a implantação operacional.

A tela administrativa de cada Webhook também exibe as 30 deliveries mais recentes e, quando existentes, um resumo expansível de suas tentativas. A origem administrativa vem do Catalog (por exemplo, `Pedido #12345`) e usa somente o tipo e ID de agregado persistidos; a lista pode ser filtrada por evento ou origem dentro desse limite. Ela é somente leitura e apresenta estados, datas e resultados HTTP/erros operacionais sanitizados; não expõe payloads, headers, respostas, URLs de entrega ou credenciais.
