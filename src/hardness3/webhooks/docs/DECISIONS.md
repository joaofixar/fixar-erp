# Decisões arquiteturais

## DEC-001 — Catálogo controlado pela Hardness
**Decisão:** eventos são definidos e versionados pela plataforma.  
**Motivo:** preservar contratos, segurança e evolução previsível.

## DEC-002 — PUBLIC e RESTRICTED
**Decisão:** o catálogo classifica eventos e `WHK006` concede acesso aos RESTRICTED.  
**Motivo:** separar disponibilidade geral de autorização explícita.

## DEC-003 — Transactional Outbox
**Decisão:** gravar evento e entrega na transação de domínio.  
**Motivo:** não publicar fato que sofreu rollback nem perder fato confirmado.

## DEC-004 — Fan-out transacional
**Decisão:** criar uma delivery por destinatário elegível no commit.  
**Motivo:** isolamento, rastreabilidade e retry individual.

## DEC-005 — Timer systemd e worker curto independente
**Decisão:** o executor inicial é um `systemd timer` que aciona um serviço
`oneshot` de worker PHP curto, com uma única instância ativa por servidor e
sem sobreposição. A próxima rodada ocorre aproximadamente cinco segundos após
o término da anterior.  
**Motivo:** reduz a latência normal esperada para aproximadamente 5–10 segundos
sem introduzir daemon PHP, pool, fila externa ou HTTP síncrono no ERP. O worker
continua desacoplado do agendador e pode evoluir futuramente.

## DEC-006 — Não usar D183/C039
**Decisão:** esses mecanismos não executam o worker.  
**Motivo:** evitar acoplamento inadequado ao processamento de entregas.

## DEC-007 — At-least-once
**Decisão:** entregas podem ocorrer mais de uma vez.  
**Motivo:** priorizar entrega confiável; consumidores usam identificadores para idempotência.

## DEC-008 — Endpoint inicialmente inativo
**Decisão:** novo endpoint inicia inativo até validação/ativação.  
**Motivo:** reduzir envio acidental a destino não confirmado.

## DEC-009 — Sem WHK003 sem destinatário
**Decisão:** não gravar evento/outbox sem subscription elegível.  
**Motivo:** evitar acúmulo sem valor operacional.

## DEC-010 — Versionamento de secret
**Decisão:** segredos têm versões e são cifrados.  
**Motivo:** permitir rotação segura e rastreável.

## DEC-011 — Retenção controlada
**Decisão:** histórico tem retenção operacional configurável.  
**Motivo:** equilibrar diagnóstico, auditoria e custo.

## DEC-012 — Administração após a base técnica
**Decisão:** a UI administrativa é mantida sobre a base técnica já validada.  
**Motivo:** preservar o desacoplamento entre configuração administrativa e entrega.

## DEC-013 — Auditoria FIN007 é evolução futura, não gate do MVP atual
**Decisão:** a auditoria de FIN007 permanece como conhecimento técnico para um
futuro `financial_analysis.approved`; sua refatoração ampla não é requisito da
integração inicial de `financial_analysis.requested`.
**Motivo:** o primeiro fato real é a entrada do pedido na Análise de Crédito e
será integrado somente no método/momento explicitamente fornecidos. A análise
e a alteração serão locais, salvo consistência tecnicamente indispensável.

## DEC-014 — Ponto funcional do evento é fornecido, análise é localizada
**Decisão:** para cada evento do legado, o responsável informa o método e o momento funcional esperados para `WebhookPublisher::publish(...)`. O projeto Webhooks analisa apenas esse ponto para confirmar o fato de domínio e os riscos de consistência indispensáveis à chamada.  
**Motivo:** evitar descoberta automática ampla e refatorações de fluxos relacionados que não sejam necessárias para integrar o evento informado com segurança.

## DEC-015 — Baseline MySQL e collation explícita
**Decisão:** Webhooks suporta MySQL 5.6.19+ até 8.4 e fixa `utf8mb4` com `utf8mb4_unicode_ci` nos sete `CREATE TABLE` do schema.  
**Motivo:** `utf8mb4_0900_ai_ci` é exclusiva do MySQL 8.0 e foi herdada do DEV, não definida pelo schema WHK. Como WHK ainda não foi publicado, não há migration de conversão permanente: o DEV foi ajustado pontualmente e o schema definitivo já nasce correto. `utf8mb4_unicode_ci` preserva Unicode de quatro bytes e é compatível com a faixa operacional confirmada. O baseline 5.6.19 também comporta os defaults `DATETIME DEFAULT CURRENT_TIMESTAMP` usados pelo schema.

## DEC-016 — Endpoint sem secret atual ativo é excluído do fan-out
**Decisão:** o Publisher ignora fail-closed um endpoint elegível cuja referência
atual de WHK007 esteja ausente, pertença a outro endpoint ou não esteja ativa.
Se nenhum destinatário restar, não cria WHK003/WHK004 e retorna normalmente.
**Motivo:** a ausência de secret é uma configuração administrativa incompleta;
não pode resultar em delivery não assinável nem impedir a transação de domínio.
O suporte interno a WHK007 é preservado sem expor gestão administrativa de HMAC.

## DEC-017 — Chave mestra exclusiva via ambiente
**Decisão:** usar `WEBHOOKS_MASTER_KEY`, Base64URL de exatamente 32 bytes,
injetada de configuração operacional fora do repositório; DEV pode usar somente
arquivo local 0600 validado.  
**Motivo:** AES-256 exige material independente, com rotação operacional futura
e sem chave em PHP, banco, endpoint ou Git.

## DEC-018 — Envelope AES-256-GCM JSON v1
**Decisão:** persistir em `WHK007_Secret_Criptografado` JSON versionado com
`v`, `iv`, `tag` e `ciphertext`, todos binários codificados Base64URL.  
**Motivo:** GCM autentica o segredo e o formato pode evoluir sem serialização
PHP insegura; dados alterados, chave errada ou formato inválido falham fechados.

## DEC-019 — Secret WHK007 imutável e revogação fail-closed
**Decisão:** criar sempre nova versão ativa em transação e mover o ponteiro do
endpoint; nunca sobrescrever versão. Revogar a versão atual remove o ponteiro.  
**Motivo:** preserva deliveries já associadas a versões antigas e impede novo
fan-out quando não há versão atual assinável.

## DEC-020 — Contrato HMAC v1 sobre bytes brutos
**Decisão:** `HMAC-SHA256(secret, timestamp + "." + raw_body)` em header
`Hardness-Signature: v1=<hex>`, verificado com `hash_equals()` e replay de 300s.  
**Motivo:** contrato simples, testável, resistente a timing e sem ambiguidades
de reserialização JSON.

## DEC-021 — Claim condicional, lease de 90s e rodada justa por tenant
**Decisão:** o worker usa seleção limitada seguida de `UPDATE` condicional para
claim; lease configurável de 90 segundos e Worker ID
`hostname:pid:random`. Na operação de produção, o batch é configurado para no
máximo **20 deliveries por tenant por rodada**, embora o componente permaneça
parametrizável.
`Attempt_Count` aumenta somente no claim ganho. Tenant é deduplicado por
host+base.  
**Motivo:** compatibilidade MySQL 5.6.19+ sem `SKIP LOCKED`, exclusividade no
banco e fairness simples: backlog ou endpoint lento de um tenant não consome um
batch inteiro antes dos demais. Timeout HTTP de 15s permanece abaixo da lease;
não há heartbeat, concorrência interna ou regra extra por endpoint no MVP.

## DEC-022 — HTTPS 443, DNS pinning e deferimento mínimo
**Decisão:** transporte aceita somente HTTPS/443, sem credentials ou redirects;
resolve A/AAAA antes, bloqueia IP não público e fixa cURL nos IPs aprovados. Só
2xx é `D`; demais resultados liberam `L` para `R` em 300s com WHK005 mínima.
**Motivo:** impedir SSRF/DNS rebinding e leases presas, mantendo o timeout total
de 15s abaixo da lease de 90s.

## DEC-023 — Retry limitado e classificação HTTP explícita
**Decisão:** seis tentativas totais; 2xx=D, 408/425/429/5xx/transporte=R até X,
3xx e demais 4xx=X. Atrasos 1m/5m/15m/1h/6h e jitter até 10%; Retry-After em
segundos positivos é limitado a 6h.
**Motivo:** comportamento operacional seguro e testável.

## DEC-024 — Cancelamento e retenção operacional
**Decisão:** C é administrativo/de segurança e exige owner para lease; retenção
manual limitada remove WHK005→WHK004→WHK003 órfã, preservando P/L/R.
**Motivo:** não reenviar configuração não autorizada nem apagar trabalho ativo.

## DEC-025 — Entrega exige HTTP explicitamente habilitado
**Decisão:** o entrypoint operacional de delivery falha antes de descobrir
tenants ou adquirir claims quando `WEBHOOKS_HTTP_ENABLED` não for exatamente
`1`.
**Motivo:** impedir que uma execução sem transporte consuma
`Attempt_Count` ou deixe deliveries em lease sem tentativa HTTP.

## DEC-026 — Evolução de capacidade guiada por métricas
**Decisão:** o MVP usa um único serviço residente que executa ciclos PHP
sequenciais, com pausa de cinco segundos, sem shards, múltiplos workers, pool,
`pcntl`, Redis, RabbitMQ nem controle persistido por endpoint. Se métricas reais
mostrarem backlog ou latência insuficiente, a evolução pode avaliar
particionamento determinístico entre poucas instâncias, paralelismo controlado,
entrega híbrida para caso específico ou fila externa. Nenhuma dessas evoluções
autoriza HTTP síncrono na transação de domínio.
**Motivo:** preservar simplicidade em PHP legado e manter Publisher, outbox,
retry, HMAC e histórico independentes do executor operacional.

## DEC-027 — Referências systemd e retenção independente
**Decisão:** versionar somente referências de `systemd` para o serviço contínuo
de delivery e para a retenção diária, sem as instalar. O delivery aguarda cinco
segundos entre ciclos; a retenção mantém comando e timer diário próprios,
ambos limitados.
**Motivo:** permitir operação reproduzível sem sobreposição, sem logs de ciclo
do gerenciador de serviços e sem misturar uma remoção histórica ao caminho de
entrega.

## DEC-028 — Disponibilidade explícita para novas subscriptions
**Decisão:** cada contrato do catálogo declara `subscription_available`. A
projeção segura `Catalog::subscriptionEventsForCompany()` retorna apenas os
contratos marcados e autorizados pela visibilidade/grant, sem expor validadores
ou detalhes internos. A marca não é consultada pelo Publisher.
**Motivo:** separar contrato tecnicamente definido, emissão real pelo core e
oferta na administração, mantendo o catálogo como fonte única de verdade e
preservando a compatibilidade das subscriptions existentes.

## DEC-029 — Payload oficial resolvido pelo módulo no momento da publicação
**Decisão:** o produtor ERP informa somente empresa, tipo de evento e ID do
agregado para `Webhook::publishForCompany()`. Um builder registrado por evento
consulta o agregado pela mesma conexão `mysqli` do chamador, produz o payload
oficial e o submete ao Catalog antes de WHK003/WHK004 serem inseridos.
`financial_analysis.requested` usa T005/D024 filtrado também por empresa.
**Motivo:** um evento tem um único contrato público, comum a todos os
consumidores e protegido de personalizações em VEN001/VEN002. O JSON continua
sendo um snapshot transacional no instante da publicação; worker e Dispatcher
não reconstroem dados do ERP. Novos campos compatíveis serão adicionados apenas
ao contrato oficial do evento.

## DEC-030 — Falha pré-Outbox não bloqueia a operação ERP
**Decisão:** falhas de resolução, validação ou persistência que ocorram antes
de haver uma Outbox válida são isoladas em VEN001/VEN002 e o fluxo de domínio
continua. WHK005 não é usado, pois ele referencia uma WHK004 já existente. Não
é emitido `error_log` por tentativa.
**Motivo:** Webhook é integração auxiliar e algumas bases podem ainda não ter
o schema WHK; registrar cada falha no log operacional criaria ruído contínuo.
Falhas HTTP posteriores à Outbox continuam sob worker/retry, com estado em
WHK004/WHK005.
