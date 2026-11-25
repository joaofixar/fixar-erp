Status: ready-for-human
Orbit: ERPFX-64

# 35: Apps sai da sidebar para o topo, ao lado do sino

**What to build:** O grupo Apps sai da sidebar. No topo, ao lado do sino, um ícone de grade abre um pop com os apps do perfil e, no fim, separada, a Loja de apps. O app abre como antes (Reforma Tributária abre a página própria, sem aba). Sem o grupo Apps no perfil o ícone some. A paleta continua listando os apps.

Revisa o [ticket 13](13-loja-ide-e-app-em-edicao.md) e o [ticket 15](15-a-area-de-trabalho-no-login.md) no lugar do menu Apps: as portas novas continuam no grupo, que agora mora no topo.

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] A sidebar não mostra o grupo Apps
- [x] O ícone ao lado do sino abre o pop com os apps e a Loja de apps no fim
- [x] O item do pop abre o app como o menu abria
- [x] Sem o grupo Apps o ícone some
- [x] A paleta continua achando os apps
- [x] Os testes do menu e o percurso cobrem o pop

## Comments

Tudo em `src/fx/shell.js`. O pop reaproveita o `htmlItem` do menu e o `colocarPop` do sino, então fecha com o clique fora e com o Esc do mesmo jeito.

- `grupoApps()` lê o grupo pela chave `apps` dos `gruposDoMenu()`. A Loja de apps é a entrada cuja ação chama `lojaApps`.
- Teste: `testes/menu-ganha-a-loja-e-o-grupo-apps.mjs` e o percurso do desktop.
