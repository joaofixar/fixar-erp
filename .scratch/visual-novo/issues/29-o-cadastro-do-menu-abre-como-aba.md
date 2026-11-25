Status: ready-for-human
Orbit: ERPFX-58

# 29: O cadastro do menu abre como aba

**What to build:** O item do menu que hoje abre um cadastro sobre a aba visível (`divIdRootAberto()`: Cliente/Fornecedor, Produtos, Empresas, CFOP e os demais) abre como aba própria que ocupa a área, com o código do `menuId` como raiz. A caixa pequena do menu, com menos de 900px de largura (exportações, SPED, Usuário), continua janela sobre a aba visível. A inclusão que o cadastro abre continua janela sobre a aba de onde saiu.

Revisa as histórias 51 a 53 da [spec](../spec.md) e o [ticket 09](09-a-aba-alem-da-visivel.md): Cliente/Fornecedor deixa de ser janela com a faixa vazia. A história 32 continua como está.

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] O cadastro do menu e da paleta abre como aba que ocupa a área, com o caminho e o item marcado
- [x] Abrir o mesmo cadastro de novo volta para a aba aberta
- [x] A caixa pequena do menu continua janela sobre a aba visível
- [x] A inclusão que o cadastro abre continua janela sobre a aba dele
- [x] O tamanho que a pessoa guardou para a URL não encolhe a aba
- [x] O percurso e o teste do shell cobrem a aba do cadastro

## Comments

Em `src/fx/shell.js`. O botão da sidebar e da paleta troca `divIdRootAberto()` pelo código do `menuId` na ação do item. No `abrirJanela`, a chamada do menu é a que chega sem pai e com o id de `menuUniques[raiz]`; a inclusão herda a mesma raiz, mas chega com pai e outro id.

- O `abrirJanela` do Hardness troca o tamanho pedido pelo de `g.tamanhosJanelas[url]`. Na aba, o shell tira esse tamanho só durante a chamada e devolve em seguida.
- A inclusão de Cliente/Fornecedor abre ocupando a área porque o usuário local tem `full` guardado para a URL dela. É o comportamento do Hardness, sem mudança aqui.
- Teste: `testes/tela-do-menu-vira-aba.mjs` (jsdom) e o percurso do desktop.
