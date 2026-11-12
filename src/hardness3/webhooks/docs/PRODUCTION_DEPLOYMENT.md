# Implantação em produção

Este runbook instala o worker de Webhooks em um servidor Hardness já existente.
Ele não cria endpoints nem subscriptions; essas configurações continuam sendo
administradas no ERP. Execute com uma conta administrativa do servidor e não
copie credenciais de banco para comandos, tickets ou logs.

## 1. Pré-requisitos e caminhos

Os arquivos systemd versionados usam `/opt/hardness`. Se a instalação real
usar outro caminho, ajuste `WorkingDirectory` e `ExecStart` na cópia da unit
antes de instalá-la.

```bash
php -v
php -m | grep -xE 'mysqli|curl|openssl'
mysql --version
systemctl --version
test -d /var/www/sites && find /var/www/sites -maxdepth 1 -type f -name '*-confUsuario.php' -printf '%f\n'
test -f /opt/hardness/webhooks/cli/worker.php
```

O runtime requer PHP 7.4 com `mysqli`, `curl` e OpenSSL; MySQL 5.6.19 ou
superior com InnoDB; systemd; e ao menos um arquivo
`/var/www/sites/*-confUsuario.php` legível pelo usuário de serviço.

## 2. Criar e validar o schema WHK

O schema é criado exclusivamente pelo atualizador-base normal do ERP,
`scripts/atualizacaoBase.php`. Ele executa `CREATE TABLE IF NOT EXISTS` para
`WHK001` até `WHK007`; não use migrations ad hoc nem DDL manual. Execute o
procedimento de atualização-base já aprovado para cada base/tenant, respeitando
o bloqueio do próprio ERP para horário comercial.

Depois da atualização, valide em cada banco com uma conexão administrativa
autorizada, sem alterar dados:

```sql
SELECT TABLE_NAME, ENGINE
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME REGEXP '^WHK00[1-7]$'
ORDER BY TABLE_NAME;

SELECT COLUMN_NAME, IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'WHK004'
  AND COLUMN_NAME = 'WHK004_WHK007_Id';
```

Devem existir sete tabelas InnoDB e `WHK004_WHK007_Id` deve aceitar `NULL`.

## 3. Chave mestra e EnvironmentFile

`WEBHOOKS_MASTER_KEY` cifra Bearer e secrets internos. É Base64URL de 32 bytes.
Gere-a uma única vez em estação administrativa segura:

```bash
php -r 'echo rtrim(strtr(base64_encode(random_bytes(32)), "+/", "-_"), "="), PHP_EOL;'
```

Guarde-a somente no cofre de segredos e no EnvironmentFile. Mantenha a mesma
chave enquanto houver dados cifrados em WHK001/WHK007: perda ou troca incorreta
faz a descriptografia falhar fechada e pode cancelar ou impedir entregas.

```bash
sudo install -d -o root -g root -m 0700 /etc/hardness/webhooks
sudo install -o root -g root -m 0600 /dev/null /etc/hardness/webhooks/webhooks.env
sudoedit /etc/hardness/webhooks/webhooks.env
```

Conteúdo mínimo, com o placeholder substituído somente no servidor:

```ini
WEBHOOKS_HTTP_ENABLED=1
WEBHOOKS_SITES_DIRECTORY=/var/www/sites
WEBHOOKS_LEASE_SECONDS=90
WEBHOOKS_MASTER_KEY=<BASE64URL_DE_32_BYTES>
WEBHOOKS_TIMEZONE=America/Sao_Paulo

# Opcionais: valores atuais padrão.
WEBHOOKS_HTTP_CONNECT_TIMEOUT_MS=5000
WEBHOOKS_HTTP_TIMEOUT_MS=15000
WEBHOOKS_HTTP_MAX_RESPONSE_BYTES=65536
WEBHOOKS_RETRY_JITTER_FRACTION=0.10
WEBHOOKS_RETRY_AFTER_CAP_SECONDS=21600
```

Não defina `WEBHOOKS_BATCH_SIZE` no arquivo: a unit fixa o valor de produção
em 20 por tenant/banco por rodada.

## 4. Usuário e permissões de tenants

Crie o usuário sem login nem home:

```bash
sudo useradd --system --user-group --no-create-home --shell /usr/sbin/nologin hardness-webhooks
```

O worker lê literalmente os arquivos `*-confUsuario.php`, deduplica por
`dbHost + dbDatabase`, abre cada banco e o processa sequencialmente. No DEV
inspecionado, `/var/www/sites` era `root:root 0755` e os arquivos eram
`root:root 0644`, enquanto Apache usava `www-data`. Portanto, uma mudança em
massa de grupo ou modo pode afetar consumidores existentes.

**Não execute `chgrp` ou `chmod` em massa.** Primeiro valide a leitura sem
mudar nada:

```bash
sudo -u hardness-webhooks test -x /var/www/sites
sudo -u hardness-webhooks find /var/www/sites -maxdepth 1 -type f -name '*-confUsuario.php' -readable -printf '%f\n'
```

Se hardening for necessário, defina antes com a equipe que provisiona o ERP um
grupo dedicado de leitura de configurações, valide um único tenant e teste
Apache/PHP e o worker. A solução deve preservar todos os consumidores
existentes; não há alteração genérica de permissões segura sem essa validação.

## 5. Instalar e validar systemd

As units versionadas são `hardness-webhooks-delivery.service`,
`hardness-webhooks-retention.service` e `hardness-webhooks-retention.timer`.
A service de delivery é contínua, usa `WEBHOOKS_BATCH_SIZE=20` e aguarda cinco
segundos entre rodadas. O lease de WHK004 também protege execuções manuais
concorrentes.

```bash
sudo install -o root -g root -m 0644 /opt/hardness/webhooks/systemd/hardness-webhooks-delivery.service /etc/systemd/system/hardness-webhooks-delivery.service
sudo install -o root -g root -m 0644 /opt/hardness/webhooks/systemd/hardness-webhooks-retention.service /etc/systemd/system/hardness-webhooks-retention.service
sudo install -o root -g root -m 0644 /opt/hardness/webhooks/systemd/hardness-webhooks-retention.timer /etc/systemd/system/hardness-webhooks-retention.timer
sudo systemctl daemon-reload
sudo systemd-analyze verify /etc/systemd/system/hardness-webhooks-delivery.service /etc/systemd/system/hardness-webhooks-retention.service /etc/systemd/system/hardness-webhooks-retention.timer
sudo systemctl cat hardness-webhooks-delivery.service
sudo systemctl cat hardness-webhooks-retention.service
sudo stat -c '%a %U:%G %n' /etc/hardness/webhooks/webhooks.env
sudo -u hardness-webhooks test -r /opt/hardness/webhooks/cli/worker.php
```

### Migrar uma instalação anterior

Versões anteriores usavam `hardness-webhooks-delivery.timer`, que iniciava uma
unit curta a cada cinco segundos e registrava mensagens do systemd em cada
rodada. Depois de instalar a nova service contínua e executar
`daemon-reload`, desabilite o timer antigo antes de habilitar a service:

```bash
sudo systemctl disable --now hardness-webhooks-delivery.timer
sudo systemctl enable --now hardness-webhooks-delivery.service
```

O worker contínuo aguarda cinco segundos entre rodadas. Quando não há
deliveries nem falhas, não registra logs; bancos que ainda não possuem todas
as tabelas `WHK001` a `WHK007` são ignorados silenciosamente.

## 6. Validação antes de ativar

`diagnostics.php` é somente leitura. O worker manual abaixo, porém, pode enviar
webhooks reais pendentes porque `WEBHOOKS_HTTP_ENABLED=1` está ativo.

```bash
sudo systemctl start hardness-webhooks-delivery.service
sudo systemctl status hardness-webhooks-delivery.service --no-pager
sudo journalctl -u hardness-webhooks-delivery.service -n 100 --no-pager
```

`webhooks/tests/` é exclusivo de DEV/CI e não integra o artefato de produção.
Para o smoke test de produção, crie previamente um endpoint controlado no ERP e
confirme, com consulta autorizada, WHK003, WHK004 e WHK005. Teste Auth=N e
Bearer em destinos controlados e confirme que uma empresa não recebe delivery de
outra.

## 7. Ativar e monitorar

```bash
sudo systemctl enable --now hardness-webhooks-delivery.service
sudo systemctl enable --now hardness-webhooks-retention.timer
sudo systemctl status hardness-webhooks-delivery.service --no-pager
sudo systemctl status hardness-webhooks-retention.timer --no-pager
sudo systemctl list-timers hardness-webhooks-retention.timer
sudo journalctl -fu hardness-webhooks-delivery.service
```

Use `journalctl -u hardness-webhooks-delivery.service -n 200 --no-pager` para
falhas por tenant e `journalctl -u hardness-webhooks-retention.service -n 100
--no-pager` para a retenção. O resumo do worker informa candidatos, claims,
despachos e leases recuperadas sem registrar URL, payload ou segredo. Para
backlog, retry, leases expiradas e estados terminais, execute `diagnostics.php`
sob a mesma configuração da unit ou consulte as tabelas WHK com acesso
administrativo.

## 8. Rollback seguro

Para pausar delivery, pare e desabilite o serviço; não mate o processo para
liberar lease. Para pausar toda a operação agendada, desabilite também o timer
de retenção.

```bash
sudo systemctl disable --now hardness-webhooks-delivery.service
sudo systemctl disable --now hardness-webhooks-retention.timer
sudo systemctl status hardness-webhooks-delivery.service --no-pager
sudo systemctl status hardness-webhooks-retention.service --no-pager
sudo journalctl -u hardness-webhooks-delivery.service -n 100 --no-pager
```

Para bloquear também invocações manuais, altere `WEBHOOKS_HTTP_ENABLED=0` no
EnvironmentFile e execute `sudo systemctl daemon-reload`. Não apague WHK003,
WHK004 ou WHK005: deliveries pendentes e leases expiradas devem ser preservadas
para retomada posterior, que continua at-least-once.
