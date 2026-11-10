Status: ready-for-agent
Orbit: ERPFX-3

# 02: O desenvolvedor entra na empresa de validação

**What to build:** A pessoa entra com o usuário local na empresa que concentra orçamento, pedido e nota fiscal de saída. A estrutura das tabelas é a da produção de hoje, sem linhas de negócio. A leitura não grava na produção e não trava quem está operando. A senha certa entra, a errada permanece deslogada, e um banco ausente falha de forma clara. Os dados locais sobrevivem a um reinício do contêiner. Dá para sair e voltar à tela de login.

**Blocked by:** 01 — A tela de login abre a partir de um clone

**Status:** ready-for-agent

- [x] A estrutura das tabelas locais é a da produção atual, sem clientes, produtos, orçamentos, pedidos, notas nem títulos
- [x] A leitura dessa estrutura não grava na produção e não trava a escrita de quem opera
- [x] O login entra na empresa de validação com a senha definida aqui
- [x] Senha errada permanece deslogado
- [x] Banco ausente falha de forma explícita, sem tela em branco
- [x] Os dados locais continuam depois de reiniciar o contêiner
- [x] Sair da sessão volta à tela de login

## Comments

Concluído em cd387a2.
