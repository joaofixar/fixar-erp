Status: ready-for-human
Orbit: ERPFX-57

# 28: A sidebar recolhe e a aba ganha desenho

**What to build:** A sidebar reparte os grupos do menu em seções (Operação, Financeiro e fiscal, Gestão, Sistema), na ordem do menu. Cada grupo tem ícone e abre e fecha os itens. A sidebar recolhe até os ícones, e o clique no grupo abre os itens num pop ao lado. A marca leva o logo, FX ERP e a empresa; com mais de uma empresa, o bloco inteiro abre a lista. O topo mostra o caminho da aba visível (grupo e item) ou a área de trabalho. A aba da faixa é pílula com o ícone do grupo e o fechar no hover. As abas internas do miolo ficam sublinhadas no primeiro nível e em pílula nos de dentro.

Revisa a história 18 da [spec](../spec.md) (o grupo deixa de ser só título) e o trilho que o [ticket 06](06-shell-para-reagir.md) deixou de fora: agora é um modo da mesma sidebar, não um shell à parte. O caminho no topo não substitui a faixa; a faixa continua guardando as telas abertas.

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] A sidebar agrupa o menu em seções, na ordem do menu
- [x] Cada grupo tem ícone e abre e fecha os itens; os grupos abertos ficam para o próximo login
- [x] O item da aba visível fica marcado, e abrir uma aba abre o grupo dela
- [x] A sidebar recolhe até os ícones e o grupo abre os itens num pop ao lado
- [x] A marca leva o logo, FX ERP e a empresa; com mais de uma empresa o bloco abre a lista
- [x] O topo mostra o caminho da aba visível ou a área de trabalho
- [x] A aba da faixa é pílula com ícone do grupo e fechar no hover
- [x] As abas internas do miolo são sublinhadas no primeiro nível e pílulas nos de dentro
- [x] Recolher a sidebar ou redimensionar a janela não mostra a aba escondida
- [x] O percurso do desktop cobre o caminho, a sanfona e a sidebar recolhida

## Comments

Implementado em `fec9c1fe`, em `src/fx/shell.js`, `src/fx/entrada.css` (gera `shell.css`) e `src/fx/tema.css`. Validado no navegador por quem usa.

- O `@theme` passou a `@theme static`: o `tema.css` lê tokens (`hover`, `header`, `error`…) que o `shell.js` não usa, e sem o `static` o CLI deixaria de emitir essas variáveis.
- O primeiro nível de abas do C.R.M., do S.R.M. e do ERP (`tabCRM.tpl`) não é título repetido: guarda as instâncias abertas e o "+" de `newTab()`. Ele continua, redesenhado.
- O resize do `sistema.js` põe `display:flex` em toda janela de módulo. O resize do shell roda depois e devolve só a aba visível; antes, redimensionar o navegador trazia de volta a aba escondida.
- Sem o Preflight, `tw:border-solid` liga a borda nos quatro lados. Borda de um lado só usa `tw:border-b`/`tw:border-l` sozinho.
- Ícones no desenho do Lucide (ISC), em SVG inline no shell. O miolo continua no sprite.
