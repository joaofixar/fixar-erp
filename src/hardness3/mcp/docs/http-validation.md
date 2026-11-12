# Roteiro de validação HTTP pós-publicação

Execute este roteiro somente depois de o virtual host encaminhar `POST /mcp` e
`POST /mcp/c/<public_id>` diretamente para `mcp/public/index.php`. Não altere o
roteamento para executar este documento. Use uma empresa de teste e credenciais
temporárias; revogue as credenciais criadas ao final.

Substitua `<base_url>`, `<api_key>`, `<api_key_sem_scope>`,
`<api_key_grace>` e `<api_key_revogada>`. Todos os comandos, exceto o primeiro,
usam a versão negociada `2025-11-25`.

## Sem credencial

```bash
curl -i -X POST '<base_url>/mcp' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' \
  --data '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"http-validation","version":"1"}}}'
```

Esperado: `401`, corpo vazio e `WWW-Authenticate: Bearer`.

## Duas conexões com URLs exclusivas

Use duas conexões ativas do mesmo usuário, A e B, cada uma com seu `<public_id>` de 24 caracteres. Antes de obter credenciais, valide a chegada ao endpoint e a descoberta específica:

```bash
curl -i -X POST '<base_url>/mcp/c/<public_id_a>' -H 'Content-Type: application/json' -H 'Accept: application/json, text/event-stream' --data '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"connection-a-smoke","version":"1"}}}'
curl -i '<base_url>/.well-known/oauth-protected-resource/mcp/c/<public_id_a>'
curl -i '<base_url>/.well-known/oauth-protected-resource/mcp/c/<public_id_b>'
```

Cada POST sem credencial deve retornar `401`; cada metadata deve retornar `200` e declarar exatamente seu próprio `resource`. Autorize A com o recurso A e B com o recurso B. Token OAuth e API Key de A devem retornar `401` em B, e vice-versa; código de A solicitado com recurso B deve falhar com `invalid_grant`. Credenciais legadas continuam em `<base_url>/mcp`.

## Inicialização, listagem e Tool atual

```bash
curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'Authorization: Bearer <api_key>' \
  --data '{"jsonrpc":"2.0","id":2,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"http-validation","version":"1"}}}'

curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer <api_key>' --data '{"jsonrpc":"2.0","id":3,"method":"tools/list"}'

curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer <api_key>' --data '{"jsonrpc":"2.0","id":4,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":{}}}'
```

Esperado: `200`; `tools/list` contém a Tool, e a chamada retorna somente os
campos aprovados do perfil da empresa vinculada.

## Argumento desconhecido da Tool atual

```bash
curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer <api_key>' --data '{"jsonrpc":"2.0","id":5,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":{"unexpected":true}}}'
```

Esperado: HTTP `200`, erro JSON-RPC `-32602` e mensagem `Invalid tool call parameters.`.

## Fixture temporária de schema com argumentos

Em ambiente de teste, registre temporariamente uma fixture que exija
`term` string com mínimo 2 caracteres e aceite `page` inteiro entre 1 e 50.
Ela não deve ser uma Tool de negócio nem permanecer registrada depois do teste.

```bash
curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer <api_key>' --data '{"jsonrpc":"2.0","id":6,"method":"tools/call","params":{"name":"hardness.test.schema_fixture","arguments":{"term":"cliente","page":2}}}'

curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer <api_key>' --data '{"jsonrpc":"2.0","id":7,"method":"tools/call","params":{"name":"hardness.test.schema_fixture","arguments":{"term":"x","page":2}}}'
```

Esperado: a primeira chamada é bem-sucedida; a segunda retorna `-32602` e não
executa a fixture. Remova o registro da fixture ao terminar.

## Scope, grace period e revogação

```bash
# Credencial ativa sem company:read: Tool não é listada; chamada retorna isError=true.
curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer <api_key_sem_scope>' --data '{"jsonrpc":"2.0","id":8,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":{}}}'

# Credencial anterior dentro do grace period: ainda autentica e obedece seus scopes.
curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'Authorization: Bearer <api_key_grace>' \
  --data '{"jsonrpc":"2.0","id":9,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"http-validation","version":"1"}}}'

# Credencial revogada: nunca autentica.
curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'Authorization: Bearer <api_key_revogada>' \
  --data '{"jsonrpc":"2.0","id":10,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"http-validation","version":"1"}}}'
```

Esperado: sem scope, `isError=true` sem executar a Tool; grace period, `200`;
revogada, `401` e corpo vazio. Ao final, revogue todas as credenciais temporárias
e confirme que a fixture não permanece no registry.

## OAuth Access Token

Depois de concluir uma autorização OAuth e trocar o código em `/oauth/token`, use
o `access_token` retornado no mesmo endpoint MCP; não há rota MCP paralela.

```bash
curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' \
  -H 'Authorization: Bearer <access_token>' \
  --data '{"jsonrpc":"2.0","id":20,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"oauth-validation","version":"1"}}}'

curl -i -X POST '<base_url>/mcp' -H 'Content-Type: application/json' \
  -H 'Accept: application/json, text/event-stream' -H 'MCP-Protocol-Version: 2025-11-25' \
  -H 'Authorization: Bearer <access_token>' \
  --data '{"jsonrpc":"2.0","id":21,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":{}}}'
```

Esperado: `200` para token ativo, cliente/grant/conexão/usuário ainda elegíveis e
scope efetivo suficiente. Enviar o `refresh_token`, token expirado/revogado ou
recurso diferente retorna `401` com o desafio `resource_metadata`; o token e seus
segredos não são incluídos na auditoria.
