# REST API Interna - Operacao

Este guia e interno da Hardness.

Objetivo:

- lembrar o que precisa ser feito ao subir a REST API em um servidor;
- lembrar onde ficam `app_key`, AccessPolicy e tokens;
- padronizar o processo para nao depender de memoria.

## Visao geral

A REST API hoje depende de 3 frentes principais:

1. configuracao local da instancia:
   - `rest_api/config/app.local.php`
2. policy por cliente:
   - `dados_usuarios/{dbCliente}/rest_api/access_policy.php`
3. tokens da API:
   - tabela `A001`

## 1. O que precisa existir no servidor

### 1.1. `app.local.php`

Criar:

```text
rest_api/config/app.local.php
```

Exemplo:

```php
<?php

return [
    'app_key' => 'DEFINA_AQUI_UMA_CHAVE_SECRETA_LONGA_E_UNICA',
];
```

Observacoes:

- este arquivo nao deve ser versionado;
- a `app_key` e por servidor/instancia;
- nao e uma `app_key` por cliente;
- clientes do mesmo servidor compartilham a mesma `app_key`;
- servidores diferentes devem ter `app_key` diferente.

### 1.2. Como gerar a `app_key`

No servidor:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

Ou:

```bash
openssl rand -hex 32
```

Usar o valor gerado no `app.local.php`.

### 1.3. Nao depende mais de `.htaccess`

Com o modelo atual:

- nao precisa `SetEnv REST_API_APP_KEY`;
- nao precisa repassar `HTTP_AUTHORIZATION` via `.htaccess`;
- o header recomendado para integracao e:

```http
X-API-Token: TOKEN_COMPLETO
```

## 2. Onde fica a AccessPolicy

### 2.1. Caminho principal em producao

```text
dados_usuarios/{dbCliente}/rest_api/access_policy.php
```

Exemplo:

```text
dados_usuarios/e998/rest_api/access_policy.php
```

### 2.2. Fallback de desenvolvimento

Se a policy principal nao existir, a API ainda pode usar o fallback de desenvolvimento:

```text
rest_api/config/policies/clients/{host_normalizado}__{dbCliente}.php
```

Isso nao e o caminho recomendado para producao.

### 2.3. Formato da policy

```php
<?php

return [
    'T005' => true,
    'T006' => [
        'block' => [
            'T006_Custo',
        ],
    ],
    'T007' => [
        'allow' => [
            'T007_Id',
            'T007_Descricao',
        ],
    ],
];
```

### 2.4. Regras da policy

- `true`:
  - libera a tabela inteira, respeitando protecoes globais
- `block`:
  - libera tudo, exceto os campos listados
- `allow`:
  - libera somente os campos listados

### 2.5. Regras gerais importantes

- policy ausente bloqueia tudo;
- policy invalida bloqueia tudo;
- tabela ausente da policy bloqueia tudo;
- nao misturar `block` e `allow` na mesma tabela;
- `A001` e `A002` continuam bloqueadas estruturalmente;
- campo inexistente nao e liberado mesmo que esteja listado.

### 2.6. PK real da tabela

A PK real detectada no schema entra automaticamente para:

- schema
- `GET`
- `fields`
- `filter`
- `sort`

Isso vale mesmo em `allow`, se a PK nao estiver listada.

Importante:

- isso nao libera a PK para escrita;
- `PATCH` com PK no payload continua bloqueado;
- `POST` com PK auto increment continua protegido.

## 3. Como administrar a policy no sistema

A tela interna fica em:

- `API001`
- aba `API REST`
- subaba `AccessPolicy`

Regras operacionais atuais:

- somente usuario `63`;
- ao abrir a tela, exige senha master;
- ao recarregar/reabrir a tela, exige senha novamente.

### 3.1. Fluxo para liberar tabela

1. abrir `API REST`
2. informar a senha master
3. em `AccessPolicy`, digitar a tabela
4. clicar em `Carregar tabela`
5. escolher:
   - `Liberar tabela inteira`
   - `Liberar tudo e bloquear campos`
   - `Liberar somente campos selecionados`
6. marcar/desmarcar os campos
7. conferir em `Ver PHP gerado`
8. salvar

### 3.2. O que a tela faz ao salvar

- valida tabela;
- valida campos;
- valida modo;
- impede `A001` e `A002`;
- cria diretório se necessario;
- cria backup;
- grava de forma segura.

### 3.3. Backup da policy

Ao salvar pela tela, o backup vai para:

```text
dados_usuarios/{dbCliente}/rest_api/history/access_policy_YYYYMMDD_HHMMSS.php
```

## 4. Como funcionam os tokens

### 4.1. Fonte da verdade

Os tokens da REST API ficam na tabela:

```text
A001
```

Cada cliente/integrador deve ter:

- token proprio;
- prefixo proprio;
- hash proprio.

O que e compartilhado no servidor e somente a:

- `app_key`

### 4.2. O que vai para a `A001`

Nao salvar token puro na tabela.

Salvar:

- `A001_Token_Hash`
- `A001_Token_Prefixo`
- `A001_Status`
- datas e observacoes

### 4.3. Como a autenticacao funciona

1. a API recebe o token completo no header
2. aplica `hash_hmac('sha256', token, app_key)`
3. compara com `A001_Token_Hash`
4. se bater e estiver `active`, autentica

### 4.4. Header recomendado para integracao

Usar:

```http
X-API-Token: TOKEN_COMPLETO
```

`Authorization: Bearer ...` continua suportado, mas `X-API-Token` e o caminho operacional recomendado.

## 5. Como gerar token

### 5.1. Pela tela interna

A tela fica em:

- `API001`
- aba `API REST`

## 6. Smoke test da REST API

Existe um script simples para validar autenticacao, leitura, policy e alguns bloqueios de escrita sem gravar no banco:

```bash
php rest_api/bin/smoke_test.php --token=TOKEN
```

### 6.1. Teste basico

Valida:

- `GET /auth/me`
- `GET /tables`

Exemplo:

```bash
php rest_api/bin/smoke_test.php --token=hrd_live_xxx
```

### 6.2. Teste de tabela liberada

Valida:

- `GET /tables/{table}/schema`
- `GET /tables/{table}`

Exemplo:

```bash
php rest_api/bin/smoke_test.php \
  --token=hrd_live_xxx \
  --table=T005
```

### 6.3. Teste com filtro

Exemplo:

```bash
php rest_api/bin/smoke_test.php \
  --token=hrd_live_xxx \
  --table=T005 \
  --filter-field=T005_Id \
  --filter-value=2264
```

### 6.4. Teste de `/validate`

Este teste nao grava no banco.

Exemplo:

```bash
php rest_api/bin/smoke_test.php \
  --token=hrd_live_xxx \
  --validate-table=T005 \
  --validate-json='{"T005_D024_Id":123}'
```

### 6.5. Testes seguros de bloqueio de escrita

Esses testes podem ser usados em producao porque validam bloqueios ou falhas antes de qualquer gravacao:

- `PUT` bloqueado com `405`
- `DELETE` bloqueado com `405`
- `PATCH` com erro esperado, por exemplo tentativa de alterar PK

#### PUT bloqueado

```bash
php rest_api/bin/smoke_test.php \
  --token=hrd_live_xxx \
  --expect-put-405=T005:1
```

#### DELETE bloqueado

```bash
php rest_api/bin/smoke_test.php \
  --token=hrd_live_xxx \
  --expect-delete-405=T005:1
```

#### PATCH com erro esperado

Exemplo seguro para testar a protecao de PK:

```bash
php rest_api/bin/smoke_test.php \
  --token=hrd_live_xxx \
  --expect-patch-error=T005:1:PRIMARY_KEY_CHANGE_NOT_ALLOWED \
  --patch-json='{"T005_Id":1}'
```

### 6.6. Teste com `Authorization: Bearer`

Embora o header recomendado seja `X-API-Token`, o runtime continua aceitando `Bearer`.

Exemplo:

```bash
php rest_api/bin/smoke_test.php \
  --token=hrd_live_xxx \
  --table=T005 \
  --header-mode=bearer
```

### 6.7. O que nao rodar em producao sem controle

Evitar smoke automatizado de escrita real em producao sem um caso controlado:

- `POST /tables/{table}` real
- `PATCH /tables/{table}/{id}` com sucesso real

Esses cenarios sao melhores para homologacao ou tabela de teste dedicada.
- subaba `Tokens`

Fluxo:

1. abrir `Tokens`
2. informar senha master
3. clicar em `Gerar novo token`
4. copiar o token no painel exibido

Regras atuais:

- o token completo aparece uma unica vez;
- a tela permite copiar nessa hora;
- depois de fechar, o valor nao volta a aparecer;
- ao gerar um novo token, os anteriores ativos sao inativados automaticamente;
- a listagem mostra primeiro o mais novo.

### 5.2. Pelo script

Tambem existe:

```bash
php rest_api/bin/generate_token.php
```

Esse script gera:

- token completo
- prefixo
- hash HMAC

Uso recomendado:

- preferir a tela interna para operacao normal;
- usar o script para suporte tecnico, emergencia ou bootstrap.

## 6. Como testar a autenticacao

### 6.1. Endpoint simples

```http
GET /api/v1/auth/me
X-API-Token: TOKEN_COMPLETO
```

Se estiver certo:

- a API autentica;
- encontra o registro na `A001`;
- retorna o contexto autenticado.

### 6.1.1. Smoke test automatizado

Existe um smoke test simples em:

```bash
php rest_api/bin/smoke_test.php --token=SEU_TOKEN
```

Exemplo mais completo:

```bash
php rest_api/bin/smoke_test.php \
  --token=hrd_live_xxx \
  --table=D001 \
  --filter-field=D001_Id \
  --filter-value=2264 \
  --validate-table=T005 \
  --validate-json='{"T005_D024_Id":123,"T005_Observacao_1":"Teste"}'
```

Esse smoke test valida:

- `GET /auth/me`
- `GET /tables`
- `GET /tables/{table}/schema`
- `GET /tables/{table}` com ou sem filtro
- `POST /tables/{table}/validate`

### 6.2. Teste de leitura

Exemplo:

```http
GET /api/v1/tables/D001?filter[D001_Id][eq]=1
X-API-Token: TOKEN_COMPLETO
```

Observacoes operacionais do endpoint `GET /tables/{table}`:

- consultas antigas de tabela unica continuam compativeis;
- a leitura agora aceita campos, filtros e ordenacao usando tabelas relacionadas, desde que a policy libere as tabelas e campos envolvidos;
- o JOIN automatico e inferido apenas para relacionamentos no formato `SOURCE_TARGET_Id`, por exemplo `T005_T003_Id -> T003.T003_Id`;
- o JOIN automatico usa sempre `LEFT JOIN`;
- quando `fields` nao e informado, a resposta continua retornando apenas campos visiveis da tabela base, mesmo que filtros, ordenacao ou joins internos usem outras tabelas;
- quando o relacionamento automatico nao existir ou precisar ser sobrescrito, a leitura pode usar o parametro `joins` com condicoes estruturadas;
- a documentacao funcional completa, com exemplos de requests e responses, fica em `rest_api/docs/openapi.yaml` e em `/api/docs`.

## 7. Checklist para subir em servidor novo

### Obrigatorio

1. garantir que a REST API esta publicada
2. criar `rest_api/config/app.local.php`
3. gerar e definir `app_key`
4. garantir que exista `dados_usuarios/{dbCliente}/rest_api/`
5. criar policy do cliente, se necessario
6. gerar token
7. gravar/confirmar o registro na `A001`
8. testar com `X-API-Token`

### Recomendado

1. abrir a tela `API REST`
2. validar senha master
3. revisar policy
4. gerar token pela propria tela
5. testar um `GET /auth/me`
6. testar uma tabela liberada

## 8. Itens que costumam causar erro

- `app.local.php` inexistente
- `app_key` diferente da usada para gerar o hash salvo na `A001`
- policy ausente ou invalida
- tabela fora da policy
- campo fora da policy
- esquecer que `A001` e `A002` continuam bloqueadas
- tentar reutilizar token antigo depois de gerar um novo

## 9. Resumo operacional curto

Para um servidor novo:

1. criar `app.local.php`
2. definir `app_key`
3. configurar policy em `dados_usuarios/{db}/rest_api/access_policy.php`
4. gerar token
5. validar `A001`
6. testar com `X-API-Token`

Para um cliente novo:

1. liberar tabelas/campos na `AccessPolicy`
2. gerar token na aba `Tokens`
3. entregar o token completo uma unica vez
4. validar no Postman
