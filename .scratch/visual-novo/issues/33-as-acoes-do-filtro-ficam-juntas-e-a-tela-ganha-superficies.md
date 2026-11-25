Status: ready-for-human
Orbit: ERPFX-62

# 33: As ações do filtro ficam juntas e a tela de grade ganha superfícies

**What to build:** As ações do filtro ficam juntas na última célula da última linha de campos, à direita: histórico, Mais filtros, Limpar e Filtrar. Sem campo à mostra, o filtro vira uma linha só, sem faixa em branco acima das ações. Mais filtros usa seta (para baixo fechado, para cima aberto) no lugar do + e do −. Na tela de grade, barra, filtro e totais ficam numa faixa cinza e os dados numa área branca. A linha entre as células troca o bege por cinza claro, o cabeçalho fica branco e a linha alternada fica visível sem apagar o amarelo do mouse.

Revisa o [ticket 32](32-o-filtro-abre-nos-campos-principais-e-ganha-rodape.md) no rodapé: Mais filtros sai da esquerda e o rodapé deixa de ser uma linha própria.

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] Mais filtros, Limpar e Filtrar ficam juntos na última célula da última linha de campos
- [x] Sem campo à mostra o filtro é uma linha só, sem faixa em branco
- [x] Mais filtros usa seta no lugar do + e do −
- [x] Barra, filtro e totais ficam numa faixa cinza; os dados numa área branca
- [x] A linha da grade troca o bege por cinza claro e o cabeçalho fica branco
- [x] A linha alternada aparece e não apaga o amarelo do mouse
- [x] O percurso cobre o agrupamento das ações e as duas superfícies

## Comments

Tudo em `src/fx/tema.css`, mais os tokens `grid-line` e `zebra` em `src/fx/entrada.css`.

- O agrupamento segue a busca avançada do Ant Design Pro (`QueryFilter`). O grupo ocupa a última coluna (`grid-column: -2 / -1`); se a última linha está cheia ele desce sozinho, e quebra em duas linhas se a coluna for estreita.
- A faixa cinza é o `.ui-tabs-panel` que tem `.divToScroll` como filho direto. A `divToScroll` não ganha margem nem raio porque o `sistema.js` recalcula a largura e a altura dela. Com filtro obrigatório ela vem com `background-color: transparent` inline; o tema vence com `!important`.
- O bege das linhas é o `bgcolor="#EDE7DE"` da `table.grid` com `cellspacing=1`.
- A linha alternada já existia (`tr.color2` em `#f9f9f9`, quase invisível). A regra nova exclui `:hover` e `.hover`, senão venceria o amarelo do mouse.
- Teste: o percurso do desktop.
