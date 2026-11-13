# Transição de scopes de parceiros

Não execute atualização automática para `partner:search`. O scope legado
`partner:read` representa apenas a consulta documental anterior e deve ser
substituído por `partner:lookup` após backup e validação administrativa.

Na base de teste e001, a pré-verificação de 2026-08-26 encontrou as conexões
MCP001 8 e 9 com `partner:read`, um grant MCP010, um snapshot MCP011 e quatro
solicitações MCP019 afetados. Antes da alteração, exporte esses registros em
uma transação e preserve o backup fora das tabelas MCP. Em seguida:

1. Altere somente `MCP001_Scopes` de `partner:read` para `partner:lookup`.
2. Revogue/substitua grants OAuth e autorizações pendentes que contenham o
   scope legado; o usuário deverá consentir novamente para o novo conjunto.
3. Não atribua `partner:search` pela migração. Conceda-o explicitamente na
   tela administrativa às conexões internas aprovadas.
4. Após validar `tools/list` com as duas conexões, remova referências restantes
   a `partner:read` dos dados de teste.

O rollback é a restauração da exportação feita no passo de backup. Esta
mudança de dados não é executada pelo deploy do código.
