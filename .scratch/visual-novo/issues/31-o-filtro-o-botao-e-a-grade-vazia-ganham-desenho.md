Status: ready-for-human
Orbit: ERPFX-60

# 31: O filtro, o botão e a grade vazia ganham desenho

**What to build:** O filtro da grade reparte os campos em colunas, com o rótulo de largura fixa e o campo no tipo do passo, sem negrito. Limpar e busca por vazio aparecem no hover ou no foco do campo. Os botões do filtro têm o mesmo tamanho, e Filtrar é o principal. Os botões do miolo (barra da tela, formulário, grade) ganham borda, raio, hover e foco, e o que inclui ou grava é o principal. O aviso do filtro obrigatório e a grade sem linha viram estado vazio, com ícone, título, texto e Limpar filtro quando a tela tem filtro. Os ícones de ação passam ao traço do Lucide.

Revisa a história 105 da [spec](../spec.md) e o [ADR 0009](../../../docs/adr/0009-tokens-nascem-no-theme.md) no ponto do sprite. Ver o [ADR 0011](../../../docs/adr/0011-icone-de-acao-sai-do-sprite.md).

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] A seleção e o campo do filtro seguem o passo, sem negrito e sem caixa alta
- [x] O filtro reparte os campos em colunas alinhadas
- [x] Limpar e busca por vazio aparecem no hover ou no foco do campo
- [x] Os botões do filtro têm o mesmo tamanho, e Filtrar é o principal
- [x] Os botões do miolo têm borda, raio, hover e foco; incluir, salvar, gravar e o envio do formulário são o principal
- [x] O aviso do filtro vira estado vazio
- [x] A grade sem linha vira estado vazio, com Limpar filtro quando a tela tem filtro
- [x] Os ícones de ação usam o traço do Lucide, e o resto continua no sprite
- [x] O percurso e o teste do shell cobrem o estado vazio e a seleção do filtro

## Comments

O desenho do filtro e dos botões está em `src/fx/tema.css`. O estado vazio está em `src/fx/shell.js` (`pintarVazios`) e `src/fx/entrada.css`.

- O texto gigante vinha do Hardness: `interfaceUsuario.php` grava `font-weight: bold; font-size: 1.1em` inline em cada campo do filtro, e o tema de caixa alta. O tema vence com `!important` só no filtro.
- O JS do Hardness estica o rótulo do filtro até 265px quando todos os campos aparecem. O tema fixa o rótulo em 7.5em e reparte os campos em `repeat(auto-fill, minmax(300px, 1fr))`.
- O campo preenchido (`.amarela`) troca o gradiente amarelo pelo azul suave do tema.
- "Exibir filtros" é o próprio ícone: o botão leva a classe do sprite e o amarelo inline. O traço vai para um `::before`.
- O aviso "Por favor, utilize o filtro." vira "Utilize o filtro". O aviso próprio da tela (`$filtroObrigatorio`) fica com o texto dela. A grade sem linha é a `table.grid` com `thead` e `tbody` vazio.
- Teste: `testes/tela-do-menu-vira-aba.mjs` (jsdom) e o percurso do desktop.
