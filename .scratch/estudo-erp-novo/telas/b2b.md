# Fichas de tela: `prog/b2b` — B2B

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `b2b/b2b001` |  |  | 7 | 2892 | 2 | 14 |

Total: 1 telas. 2.892 linhas. 2 tabelas gravadas. 16 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/b2b/b2b001`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 7 arquivos. 2.892 linhas. `v2grid.php` (1155), `grid.php` (1147), `form.php` (244), `grid_cabecalho.php` (175), `grid_func-ajax.php` (135), `grid_func-callback.php` (31), `grid_func-js.php` (5)
- **Ações (`case` do roteador, 18):** `b2b001FormSenha`, `gridB2BPrincipal`, `gridB2BCarrinho`, `gridB2BPedidos`, `gridB2BOrcamentos`, `gridB2BNotasFiscais`, `gridB2BBoletos`, `incluir`, `excluir`, `gravarQuantidade`, `gerarPedido`, `gerarOrcamento`, `gridB2BPrincipal`, `gridB2BCarrinho`, `gridB2BPedidos`, `gridB2BOrcamentos`, `gridB2BNotasFiscais`, `gridB2BBoletos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D013` (fotografia:3952): UPDATE/form em `prog/b2b/b2b001/form.php:63`, `prog/b2b/b2b001/form.php:139`
  - `T269` (fotografia:27018): DELETE/INSERT/UPDATE em `prog/b2b/b2b001/grid_func-ajax.php:27`, `prog/b2b/b2b001/grid_func-ajax.php:37` e mais 3
- **Tabelas só lidas na tela (14):** `D024` (29×, `prog/b2b/b2b001/form.php:28` e mais 28), `C007` (24×, `prog/b2b/b2b001/grid.php:552` e mais 23), `D001` (21×, `prog/b2b/b2b001/form.php:22` e mais 20), `D003` (21×, `prog/b2b/b2b001/form.php:22` e mais 20), `T005` (12×, `prog/b2b/b2b001/grid.php:546` e mais 11), `T007` (12×, `prog/b2b/b2b001/grid.php:559` e mais 11), `D009` (10×, `prog/b2b/b2b001/grid.php:62` e mais 9), `T003` (8×, `prog/b2b/b2b001/grid.php:550` e mais 7), `D049` (7×, `prog/b2b/b2b001/grid.php:64` e mais 6), `D022` (6×, `prog/b2b/b2b001/grid.php:551` e mais 5), `T002` (4×, `prog/b2b/b2b001/grid.php:1024` e mais 3), `T117` (4×, `prog/b2b/b2b001/grid.php:1028` e mais 3), `D007` (4×, `prog/b2b/b2b001/grid.php:1029` e mais 3), `D006` (2×, `prog/b2b/b2b001/grid.php:885` e mais 1)
- **Classes e métodos chamados:** `B2B001` (526 linhas) (só incluída)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T005_Status_Pedido` (`prog/b2b/b2b001/grid.php:536` e mais 7)
- **Chaves C031 lidas (7):** `HabilitarPedidoOrcamento`, `HabilitarPreco`, `MostrarBoletos`, `MostrarNotasFiscais`, `MostrarOrcamentos`, `MostrarPedidos`, `QuantidadePadraoCarrinho`
- **Grafo (Graphify):** 13 nós, 6 funções definidas: `formataPrecoB2B()`, `precoProdutoB2B()`, `valorTotalBoletosB2B()`, `valorTotalNotasB2B()`, `valorTotalOrcamentoB2B()`, `valorTotalPedidoB2B()`

