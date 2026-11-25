Status: ready-for-agent
Orbit: ERPFX-55

# 26: O menu ganha a loja e o grupo Apps

**What to build:** A Loja de apps fica em Configuração, só para o root no critério de hoje, e abre o catálogo que a Hardness publica, com Obter, Instalar e Atualizar. O item de criar app sai do menu. O desktop não desenha Modo IDE nem Editando App. O grupo Apps entra depois de Gerencial e antes de Outros, na ordem combinada. Cada item abre o endereço de hoje numa aba nova do navegador, com a mesma permissão, e não vira aba do desktop. Quem está logado vê Reforma Tributária e, por isso, o grupo. A paleta alcança o grupo e a loja.

**Blocked by:** 19 — A janela acompanha a aba. 20 — A paleta pesquisa o menu.

**Status:** ready-for-agent

- [x] Usuário root, no critério de hoje (usuário 63 ou usuário de suporte), vê a Loja de apps no grupo Configuração
- [x] Perfil Diretor não vê a Loja de apps
- [x] A janela da loja lista os apps de versão de produção e oferece Obter, Instalar e Atualizar
- [x] A nuvem fica fora do topo
- [x] O item de criar app fica fora do menu do shell
- [x] O desktop não mostra o selo Modo IDE nem o selo Editando App
- [x] O grupo Apps fica depois de Gerencial e antes de Outros
- [x] A ordem do grupo é Integrações, Conexões MCP, Webhooks, Apontamento PCP, GNRE, Reforma Tributária, Dashboard e Painel TV
- [x] Cada item abre o mesmo endereço de hoje, numa aba nova do navegador, com a mesma permissão, e não vira aba do desktop
- [x] Quem está logado vê Reforma Tributária e, por isso, o grupo Apps
- [x] A paleta alcança o grupo Apps e a Loja de apps

## Comments

Implementado em `cd87dfc4`. A Loja de apps entra em Configuração para o usuário 63 ou o usuário de suporte e abre o catálogo `appsListarCliente`. O Diretor não herda o flag de sessão do root. O item de criar app sai do menu que o shell desenha; o editor de permissão continua com o menu de origem. A nuvem e os selos Modo IDE e Editando App saem do desktop. O grupo Apps fica depois de Gerencial e antes de Outros. Cada atalho abre o endereço de hoje numa aba nova do navegador.
