# Segredos MCP no desenvolvimento local

O contrato normal do MCP é `getenv()`. Produção e homologação devem injetar os
peppers no Apache/PHP e não podem depender deste fallback.

Para o MVP local, o único fallback aceito é `/www/.local-secrets/mcp.local.php`
(host: `/root/sistema_hardness/.local-secrets/mcp.local.php`). O arquivo não
pertence ao repositório, deve ter modo `0600`, não pode ser symlink e deve
retornar `environment => 'dev'` junto com os nomes de variáveis MCP necessários.
Variáveis reais do ambiente sempre têm prioridade.

Para concluir OAuth 5.2, configure também `MCP_OAUTH_AUTHORIZATION_CODE_PEPPER_V1`
(ou o par JSON/versionado `MCP_OAUTH_AUTHORIZATION_CODE_PEPPERS_JSON` e
`MCP_OAUTH_AUTHORIZATION_CODE_PEPPER_CURRENT_VERSION`). Ele deve ser diferente
do pepper de transações de autorização e nunca pode ser registrado ou exposto.
Como compatibilidade de implantação, na ausência dele o sistema usa o pepper de
transações já configurado; isso permite ativação sem indisponibilidade, mas deve
ser substituído por um valor próprio antes de produção.

A Etapa 6 adiciona os peppers versionados `MCP_OAUTH_ACCESS_TOKEN_PEPPER_V1` e
`MCP_OAUTH_REFRESH_TOKEN_PEPPER_V1` (ou suas variantes `*_PEPPERS_JSON` e
`*_PEPPER_CURRENT_VERSION`). Em desenvolvimento, ambos têm o mesmo fallback
compatível; fora de `dev`, a ausência do pepper próprio falha fechada. Em
produção, configure valores distintos e próprios.

Valide sem expor valores executando, pelo PHP que atende HTTP, uma checagem de
presença e tamanho do pepper. Nunca registre, faça dump ou envie o conteúdo do
arquivo ao navegador.
