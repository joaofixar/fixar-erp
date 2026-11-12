# Testes

Os testes em `webhooks/tests/` são regressões permanentes organizadas por
domínio. Eles preservam os contratos de Publisher/Outbox, catálogo, segredo e
HMAC, claim/lease, retry, Dispatcher/SSRF, retenção, integrações VEN001/VEN002,
administração, multiempresa, Auth=N e Bearer.

Os testes integrados exigem credenciais DEV explícitas por
`WEBHOOKS_TEST_ERP_CONFIG`; os estáticos usam apenas arquivos locais ou
doubles. Cada integrado cria dados sintéticos em tenants reservados e os remove
em `finally`. Nunca execute-os contra produção.

## Execução

```bash
for test in webhooks/tests/*.php; do php -l "$test" || exit 1; done

# Testes estáticos, sem banco
php webhooks/tests/worker_operations_static.php
php webhooks/tests/admin_boundary_security_static.php
php webhooks/tests/admin_ui_static.php
php webhooks/tests/destination_authentication_static.php

# Exemplo de teste integrado: somente DEV, com configuração explícita
WEBHOOKS_TEST_ERP_CONFIG=/caminho/seguro/tenant-confUsuario.php \
  php webhooks/tests/catalog_publisher_outbox_integration.php
```

Execute os demais integrados com a mesma variável:

- `secrets_hmac_integration.php`
- `worker_claim_lease_tenants_integration.php`
- `dispatcher_security_integration.php`
- `retry_retention_integration.php`
- `delivery_end_to_end_integration.php`
- `erp_publication_contracts.php`
- `admin_backend_multi_company_integration.php`
- `destination_authentication_integration.php`

`webhooks/tests/` permanece no repositório para DEV e CI. Não é necessário no
artefato implantado em produção; os testes integrados dependem de configuração
e dados sintéticos exclusivamente de DEV.
