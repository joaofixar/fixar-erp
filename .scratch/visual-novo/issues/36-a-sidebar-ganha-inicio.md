Status: ready-for-human
Orbit: ERPFX-65

# 36: A sidebar ganha Início

**What to build:** Início é o primeiro item da sidebar. Ele mostra a área de trabalho sem fechar as abas: nenhuma aba fica selecionada, o caminho do topo diz Início e o item fica marcado. Clicar numa aba a traz de volta. Na sidebar recolhida, Início é só o ícone da casa.

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] Início abre a sidebar, com a casa
- [x] Clicar em Início mostra a área de trabalho e as abas continuam na faixa
- [x] Nenhuma aba fica selecionada e o caminho diz Início
- [x] Clicar numa aba a traz de volta
- [x] A janela aberta no Início não nasce escondida
- [x] Os testes da aba e o percurso cobrem Início

## Comments

`src/fx/shell.js` e `src/fx/entrada.css` (Início na sidebar recolhida).

- No Início, `visivel` vale `null` e as abas ficam escondidas. O Hardness abre a janela nova na raiz devolvida por `divIdRootAberto`, que podia ser a de uma aba escondida, e a janela nascia escondida. O shell embrulha o `divIdRootAberto`: sem aba visível, uma raiz de aba vira uma raiz nova `fx-inicio-…`.
- Teste: `testes/tela-do-menu-vira-aba.mjs` e o percurso do desktop.
