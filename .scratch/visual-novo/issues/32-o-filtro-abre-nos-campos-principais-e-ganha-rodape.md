Status: ready-for-human
Orbit: ERPFX-61

# 32: O filtro abre nos campos principais e ganha rodapé

**What to build:** O filtro abre só com os campos principais, e o resto fica atrás de "Mais filtros (N)", também quando o filtro é obrigatório. As ações vão para um rodapé: Mais filtros e o histórico à esquerda, Limpar e Filtrar com texto à direita. O rótulo tem a largura do maior rótulo do filtro, até 11em, com o nome no title quando corta. O intervalo de datas termina na mesma linha dos outros campos, e limpar e busca por vazio ficam dentro do campo. O botão de menos filtros deixa de ficar preto, e o botão de ajuda do filtro sai.

Revisa o [ticket 31](31-o-filtro-o-botao-e-a-grade-vazia-ganham-desenho.md) no rótulo fixo de 7.5em e nos botões quadrados do filtro.

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] O botão de menos filtros mostra o traço, sem ficar preto
- [x] O intervalo de datas alinha com os outros campos da coluna
- [x] Limpar e busca por vazio ficam dentro do campo, à direita
- [x] O rótulo tem a largura do maior do filtro, até 11em, com title quando corta
- [x] O filtro abre nos campos principais, e "Mais filtros (N)" abre o resto
- [x] O filtro obrigatório também abre nos campos principais
- [x] O rodapé tem Mais filtros à esquerda e Limpar e Filtrar com texto à direita
- [x] O botão de ajuda do filtro sai
- [x] O envio do formulário é botão principal
- [x] O percurso e o teste do shell cobrem o rodapé e os campos principais

## Comments

O desenho está em `src/fx/tema.css`. A contagem, a medida do rótulo e o recolher estão em `src/fx/shell.js` (`arrumarFiltros`).

- O traço preto vinha da falta de `--fx-glifo` para `ui-icon-minusthick`: a máscara do `::before` ficava inválida e o fundo cobria o botão.
- Com filtro obrigatório e nada filtrado, `interfaceUsuario.php` marca todo campo como filtrado, e nenhum vem com `filtroOculto`. Os campos que o Hardness mostra fora desse caso são os que o usuário já usou (`getGridFiltroCampoCount`), e essa conta não chega à página. O shell mantém os 6 primeiros e os preenchidos e devolve `filtroOculto` aos outros; o botão do Hardness (`button:eq(2)`) volta a abrir e fechar esses campos.
- O mesmo filtro obrigatório abre com `display: inline` os campos que já vinham escondidos e deixa o botão no "+". O shell fecha de novo.
- Os botões continuam os do Hardness, que os liga pela posição; o rodapé só troca a ordem visual (`order`). Limpar, Filtrar e Mais filtros ganham o texto por `::after`, e a contagem vem de `data-fx-ocultos`.
- O rótulo é medido em canvas, uma vez por filtro, e vai para `--fx-rotulo`, que também define a largura mínima da coluna.
- O envio do formulário não tem `botaoid`; o tema o pega pelo id `…Form…_btn` (ex.: `cad001FormCadastro_btn`).
- Teste: `testes/tela-do-menu-vira-aba.mjs` (jsdom) e o percurso do desktop.
