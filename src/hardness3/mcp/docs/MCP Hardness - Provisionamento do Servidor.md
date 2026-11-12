# MCP Hardness — Provisionamento do Servidor

Guia operacional para preparar um servidor Linux com Apache e PHP para publicar
o MCP Hardness. Ele cobre infraestrutura, publicação das rotas e validação HTTP.
Não executa nem substitui o procedimento do ERP para banco de dados ou
`atualizacaoBase`.

> Execute cada etapa de forma assistida e valide o resultado antes da próxima.
> Não grave peppers, tokens, API keys ou segredos no Git, em código, em comandos
> colados em histórico de shell ou em logs.

## 1. Variáveis da instalação e limites de escopo

Substitua os marcadores abaixo pelos valores da instalação:

- `<host-publico>`: domínio HTTPS do cliente, por exemplo
  `cliente.hardness.com.br`;
- `<document-root>`: DocumentRoot real do VirtualHost;
- `<app-root>`: diretório da cópia versionada do Hardness, por exemplo
  `<document-root>/hardness3`;
- `<arquivo-vhost>`: arquivo do VirtualHost do cliente;
- `<pool-fpm>`: arquivo do pool PHP-FPM que atende esse VirtualHost, quando
  aplicável.

O MCP é publicado no domínio normal do cliente, não em um VirtualHost exclusivo:

```text
https://<host-publico>/mcp
  → VirtualHost existente do cliente
  → <document-root>/.htaccess
  → <app-root>/mcp/public/index.php
```

Conexões com identificador público usam o mesmo host, sem VirtualHost adicional:

```text
https://<host-publico>/mcp/c/<public_id>
  → VirtualHost existente do cliente
  → <document-root>/.htaccess
  → <app-root>/mcp/public/index.php
```

`<public_id>` possui exatamente 24 caracteres base64url (`A-Z`, `a-z`, `0-9`, `_` ou `-`). Cadastre essa URL exclusiva para uma nova conexão MCP; mantenha `/mcp` para conectores e credenciais legados.

Não inspecione, altere ou corrija VirtualHosts de outros clientes como parte
deste procedimento. Se uma validação global do Apache falhar por um site alheio,
registre o bloqueio e continue apenas com diagnósticos específicos do host MCP.

## 2. Descobrir a arquitetura antes de alterar

### 2.1 VirtualHost, DocumentRoot e rewrite

Confirme no VirtualHost do cliente:

```bash
grep -nEi '^[[:space:]]*(<VirtualHost|ServerName|ServerAlias|DocumentRoot|<Directory|AllowOverride|RewriteEngine|SSLEngine|SSLCertificate|SetHandler|FilesMatch)' <arquivo-vhost>
ls -l /etc/apache2/mods-enabled/rewrite.load
```

Requisitos:

- o host HTTPS aponta para o DocumentRoot correto;
- o bloco aplicável ao DocumentRoot permite `.htaccess` com `AllowOverride
  FileInfo` ou `AllowOverride All`;
- `mod_rewrite` está habilitado;
- o certificado HTTPS corresponde ao host público;
- se houver PHP-FPM, o handler aponta para o socket/pool esperado.

`apachectl -S` e `apachectl -t` são diagnósticos úteis, mas podem falhar por um
VirtualHost não relacionado. Nunca corrija esse outro site automaticamente e não
reinicie/recarregue o Apache apenas para publicar regras em `.htaccess`.

### 2.2 Identificar mod_php ou PHP-FPM

Não assuma o modelo de PHP. Verifique primeiro:

```bash
systemctl list-units --type=service --all 'php*-fpm.service'
grep -nEi '^[[:space:]]*(SetHandler|ProxyPassMatch|FilesMatch)' <arquivo-vhost>
```

- Se há um serviço `php*-fpm` ativo e o VirtualHost usa `proxy:unix:` ou
  `fcgi://`, siga a seção **5. PHP-FPM**.
- Se o VirtualHost usa `mod_php`, siga a seção **6. Apache com mod_php**.

Não aplique instruções de FPM a um host `mod_php`, nem o inverso.

## 3. Pré-requisitos PHP e HTTPS

O MCP requer PHP `7.4.x`, `apcu`, `openssl`, `PDO`, `pdo_mysql` e `mysqli` no
SAPI HTTP. O CLI é só um pré-diagnóstico:

```bash
php -v
php -m | grep -Ei '^(apcu|openssl|PDO|pdo_mysql|mysqli)$'
php -r 'echo "APCu=" . ((extension_loaded("apcu") && function_exists("apcu_enabled") && apcu_enabled()) ? "ok" : "indisponivel") . PHP_EOL;'
curl -Iv --max-time 15 https://<host-publico>/
```

O certificado deve validar sem erro. Com `MCP_RATE_LIMIT_DRIVER=apcu`, o MCP
falha fechado com `503` se APCu não estiver ativo no PHP que atende HTTP, mesmo
que esteja ativo no CLI.

## 4. Peppers e demais variáveis MCP

### 4.1 Valores obrigatórios

Configure estes nomes exatamente como abaixo:

```text
MCP_ENVIRONMENT=production
MCP_CREDENTIAL_PEPPER_CURRENT_VERSION=v1
MCP_CREDENTIAL_PEPPERS_JSON
MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPER_CURRENT_VERSION=v1
MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPERS_JSON
MCP_OAUTH_AUTHORIZATION_CODE_PEPPER_CURRENT_VERSION=v1
MCP_OAUTH_AUTHORIZATION_CODE_PEPPERS_JSON
MCP_OAUTH_ACCESS_TOKEN_PEPPER_CURRENT_VERSION=v1
MCP_OAUTH_ACCESS_TOKEN_PEPPERS_JSON
MCP_OAUTH_REFRESH_TOKEN_PEPPER_CURRENT_VERSION=v1
MCP_OAUTH_REFRESH_TOKEN_PEPPERS_JSON
MCP_RATE_LIMIT_KEY_PEPPER
MCP_RATE_LIMIT_DRIVER=apcu
```

Cada finalidade usa um valor aleatório e independente. Os cinco valores
`*_PEPPERS_JSON` são JSON versionado, por exemplo `{"v1":"<segredo>"}`; `v1`
é uma versão inicial válida. `MCP_RATE_LIMIT_KEY_PEPPER` é um valor escalar e
não usa JSON/versionamento.

Em uma instalação com vários servidores que atendem o mesmo tenant/banco, todos
devem receber os mesmos peppers e versões. Em instalações isoladas, gere valores
independentes.

### 4.2 Armazenamento seguro

Mantenha os valores fora do repositório, por exemplo em:

```text
/etc/hardness/mcp/mcp.env
```

O diretório deve pertencer a `root:root` e ter modo `0700`; o arquivo deve
pertencer a `root:root` e ter modo `0600`. Gere os valores diretamente no
servidor e valide apenas nomes, formato e permissões — nunca os imprima.

## 5. Apache + PHP-FPM (modelo recomendado em servidor compartilhado)

Este modelo mantém `clear_env=yes` e libera ao pool somente as variáveis MCP.
Não use `clear_env=no`: isso ampliaria a herança de todas as variáveis do serviço
para todas as aplicações atendidas pelo pool.

1. Faça backup de `<pool-fpm>` antes de editar e confirme a cópia com `cmp`.
2. Crie um drop-in do serviço FPM, por exemplo
   `/etc/systemd/system/php7.4-fpm.service.d/mcp.conf`, com:

   ```ini
   [Service]
   EnvironmentFile=/etc/hardness/mcp/mcp.env
   ```

3. No bloco `[www]` de `<pool-fpm>`, mantenha `clear_env` no padrão seguro e
   acrescente somente a allow-list abaixo. Os valores permanecem exclusivamente
   no `mcp.env`:

   ```ini
   env[MCP_ENVIRONMENT] = $MCP_ENVIRONMENT
   env[MCP_CREDENTIAL_PEPPER_CURRENT_VERSION] = $MCP_CREDENTIAL_PEPPER_CURRENT_VERSION
   env[MCP_CREDENTIAL_PEPPERS_JSON] = $MCP_CREDENTIAL_PEPPERS_JSON
   env[MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPER_CURRENT_VERSION] = $MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPER_CURRENT_VERSION
   env[MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPERS_JSON] = $MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPERS_JSON
   env[MCP_OAUTH_AUTHORIZATION_CODE_PEPPER_CURRENT_VERSION] = $MCP_OAUTH_AUTHORIZATION_CODE_PEPPER_CURRENT_VERSION
   env[MCP_OAUTH_AUTHORIZATION_CODE_PEPPERS_JSON] = $MCP_OAUTH_AUTHORIZATION_CODE_PEPPERS_JSON
   env[MCP_OAUTH_ACCESS_TOKEN_PEPPER_CURRENT_VERSION] = $MCP_OAUTH_ACCESS_TOKEN_PEPPER_CURRENT_VERSION
   env[MCP_OAUTH_ACCESS_TOKEN_PEPPERS_JSON] = $MCP_OAUTH_ACCESS_TOKEN_PEPPERS_JSON
   env[MCP_OAUTH_REFRESH_TOKEN_PEPPER_CURRENT_VERSION] = $MCP_OAUTH_REFRESH_TOKEN_PEPPER_CURRENT_VERSION
   env[MCP_OAUTH_REFRESH_TOKEN_PEPPERS_JSON] = $MCP_OAUTH_REFRESH_TOKEN_PEPPERS_JSON
   env[MCP_RATE_LIMIT_KEY_PEPPER] = $MCP_RATE_LIMIT_KEY_PEPPER
   env[MCP_RATE_LIMIT_DRIVER] = $MCP_RATE_LIMIT_DRIVER
   ```

4. Revise o diff, execute `systemctl daemon-reload` e valide a sintaxe FPM
   com o ambiente carregado. Redirecione a saída detalhada do teste para não
   revelar expansões de variáveis.
5. Reinicie somente o serviço FPM após a sintaxe válida. Um `reload` não basta
   para carregar um novo ambiente de serviço systemd.
6. Confirme o estado do FPM, a resposta normal do ERP e os logs antes de seguir.

O drop-in é específico do serviço PHP-FPM, mas as variáveis explicitamente
permitidas ficam visíveis a processos PHP do pool compartilhado. Para isolamento
mais forte, é necessário um pool dedicado e alteração arquitetural do Apache;
isso não deve ser decidido durante um deploy comum.

### Rollback FPM

Se a ativação falhar, restaure o backup de `<pool-fpm>`, remova o drop-in MCP,
execute `systemctl daemon-reload`, valide a sintaxe e reinicie somente o FPM. O
arquivo `mcp.env` pode permanecer no disco, pois não é carregado sem o drop-in e
a allow-list correspondente.

## 6. Apache com mod_php

Não grave os valores dos peppers diretamente no VirtualHost nem no repositório.
Carregue o arquivo protegido de ambiente pelo mecanismo de serviço suportado na
distribuição (por exemplo, drop-in systemd para `apache2`/`httpd`) e valide que
o processo Apache/mod_php recebe os valores via `getenv()`.

Antes de ativar, faça backup de qualquer arquivo de serviço/Apache que será
alterado, valide a sintaxe específica do Apache e só então faça `reload` quando
tecnicamente suficiente. Em caso de dúvida sobre a propagação de ambiente para
mod_php, faça o smoke test MCP sem credencial e a validação OAuth; não exponha
segredos por `phpinfo()` ou `printenv`.

## 7. Publicação do código e do `.htaccess`

### 7.1 Atualizar a cópia versionada

Antes de qualquer `git pull`, inspecione a branch e o worktree:

```bash
git -C <app-root> status --short --branch
```

Se o deploy estiver autorizado e não houver conflito, prefira `git pull
--ff-only`. Não use `reset --hard`, `checkout --`, stash ou limpeza de arquivos
locais como atalho para um worktree sujo.

### 7.2 Identificar o `.htaccess` efetivo

O arquivo relevante para `https://<host-publico>/mcp` é
`<document-root>/.htaccess`, não necessariamente `<app-root>/.htaccess`.
Confirme isso pelo `DocumentRoot` do VirtualHost e compare os arquivos antes de
copiar qualquer conteúdo.

Faça backup do `.htaccess` efetivo, monte uma versão candidata fora do
DocumentRoot e revise o `diff`. Preserve a regra genérica existente do ERP e
qualquer flag operacional, como `UnsafeAllow3F`.

As regras MCP devem vir **antes** do roteador genérico do ERP:

```apache
RewriteRule ^mcp/?$ /hardness3/mcp/public/index.php [END]
RewriteRule ^mcp/c/[A-Za-z0-9_-]{24}$ /hardness3/mcp/public/index.php [END]
RewriteRule ^\.well-known/(oauth-protected-resource(?:/mcp)?|oauth-authorization-server|openid-configuration)/?$ /hardness3/mcp/public/index.php [END]
RewriteRule ^\.well-known/oauth-protected-resource/mcp/c/[A-Za-z0-9_-]{24}$ /hardness3/mcp/public/index.php [END]
RewriteRule ^oauth/register/?$ /hardness3/mcp/public/index.php [END]
RewriteRule ^oauth/authorize/?$ /hardness3/mcp/public/index.php [END]
RewriteRule ^oauth/token/?$ /hardness3/mcp/public/index.php [END]
```

Adapte somente o prefixo `/hardness3/` se `<app-root>` tiver outro caminho
público. Não copie cegamente o `.htaccess` do repositório sobre o arquivo
operacional do DocumentRoot. Regras em `.htaccess` entram em vigor na próxima
requisição; não exigem restart/reload do Apache.

O `.htaccess` em `docker/` serve ao ambiente Apache legado do Docker. Em
ambientes locais Nginx, `.htaccess` é ignorado e as rotas MCP devem ser tratadas
na configuração Nginx correspondente.

### Rollback de rotas

Restaure o backup do `.htaccess` efetivo. Isso despublica somente as rotas MCP/
OAuth; não remove código, banco ou `mcp.env`.

## 8. Smoke tests após publicação

Primeiro valide sem criar dados:

```bash
curl -i --max-time 15 -X POST 'https://<host-publico>/mcp' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' \
  --data '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"smoke-test","version":"1"}}}'

curl -i --max-time 15 'https://<host-publico>/.well-known/oauth-protected-resource/mcp'
curl -i --max-time 15 -X POST 'https://<host-publico>/mcp/c/<public_id>' -H 'Content-Type: application/json' -H 'Accept: application/json, text/event-stream' --data '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"exclusive-smoke","version":"1"}}}'
curl -i --max-time 15 'https://<host-publico>/.well-known/oauth-protected-resource/mcp/c/<public_id>'
curl -i --max-time 15 'https://<host-publico>/.well-known/oauth-authorization-server'
curl -i --max-time 15 'https://<host-publico>/oauth/authorize'
```

Resultados esperados:

- `POST /mcp` sem credencial: `401`, corpo vazio e `WWW-Authenticate: Bearer`;
- `/.well-known/oauth-protected-resource/mcp`: `200` com `resource` e
  `authorization_servers` corretos;
- `/.well-known/oauth-authorization-server`: `200` com `/oauth/authorize`,
  `/oauth/token` e `/oauth/register`;
- `GET /oauth/authorize` sem parâmetros: `400 {"error":"invalid_request"}`.

Depois use as validações autenticadas de `mcp/docs/http-validation.md`, sempre
com credenciais temporárias e sem colá-las em chat, tickets ou logs.

## 9. Pré-requisitos para o primeiro vínculo OAuth

Rotas e discovery corretos não bastam para o cliente concluir a autorização.
Antes de clicar em **Vincular** no Claude ou outro cliente OAuth, configure no
painel MCP do ERP:

- uma conexão MCP para a empresa correta, com status `active`;
- os scopes mínimos necessários (por exemplo, `company:read` para o teste
  inicial);
- elegibilidade para o usuário ERP que fará login (direta, por perfil ou pela
  política permitida);
- usuário ERP ativo.

Se o usuário autenticado não tiver conexão elegível, o servidor recusa o
consentimento e o cliente normalmente mostra uma mensagem genérica de falha de
autorização. Isso não indica, por si só, falha de Apache, FPM, HTTPS ou peppers.

O cadastro de conexões, clientes OAuth, grants e quaisquer migrations é uma
etapa de aplicação/banco separada deste guia. Ao terminar o teste, prefira
revogar a conexão, credenciais, cliente e grant de teste pelo painel MCP. Não
faça `TRUNCATE` indiscriminado nas tabelas `MCP*`, sobretudo em `MCP003`
(auditoria), sem procedimento aprovado, backup e confirmação de escopo.

## 10. Critérios de conclusão e diagnóstico

Considere a infraestrutura aprovada quando:

- HTTPS do host público valida;
- o ERP existente continua respondendo normalmente após a ativação PHP;
- o PHP HTTP possui APCu operacional;
- `POST /mcp` sem credencial retorna o desafio Bearer esperado;
- os dois documentos `/.well-known` retornam `200` com URLs HTTPS canônicas;
- o fluxo OAuth conclui com usuário elegível e uma conexão ativa;
- uma chamada autenticada `initialize` retorna `200`.

Durante diagnóstico, filtre logs por eventos MCP e por período de teste. Não
publique query strings OAuth, cabeçalhos `Authorization`, cookies, códigos,
tokens, API keys ou peppers. Se for necessário voltar atrás, use os backups
criados para FPM e `.htaccess` e valide o ERP após cada rollback.
