Status: ready-for-agent

# 01: A tela de login abre a partir de um clone

**What to build:** Um comando sobe a base no contêiner e o navegador mostra a tela de login. O interpretador é PHP 8.3. A página abre sem internet e sem consultar o fornecedor do sistema. Não existe entrada de administrador sem senha. Um segundo clone não traz segredo, mídia pesada, cache, arquivo de uso, pasta de outro cliente nem o script de backup.

**Blocked by:** None (can start immediately).

**Status:** ready-for-agent

- [ ] Um comando sobe o contêiner com PHP 8.3 e o navegador mostra a tela de login
- [ ] A tela de login abre sem internet e sem consultar o fornecedor do sistema
- [ ] Nenhuma URL entra como administrador sem senha
- [ ] O que o git guarda não inclui segredo, mídia pesada, cache, arquivo de uso, pasta de outro cliente nem o script de backup
- [ ] A sintaxe que o PHP 8.3 se recusa a carregar está corrigida na cópia que roda, e as funções removidas que o legado ainda chama não impedem o login
- [ ] Aviso de função antiga não derruba a página
