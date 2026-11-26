# Fichas de tela: `prog/v2b2b` — B2B v2

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `v2b2b/b2b001` |  |  | 6 | 1935 | 2 | 19 |

Total: 1 telas. 1.935 linhas. 2 tabelas gravadas. 21 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/v2b2b/b2b001`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 6 arquivos. 1.935 linhas. `grid.php` (1293), `form.php` (246), `grid_func-ajax.php` (185), `grid_cabecalho.php` (175), `grid_func-callback.php` (31), `grid_func-js.php` (5)
- **Ações (`case` do roteador, 13):** `b2b001FormSenha`, `gridB2BPrincipal`, `gridB2BCarrinho`, `gridB2BPedidos`, `gridB2BOrcamentos`, `gridB2BNotasFiscais`, `gridB2BBoletos`, `incluir`, `excluir`, `gravarQuantidade`, `alterarPreco`, `gerarPedido`, `gerarOrcamento`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D179` (fotografia:9410): UPDATE/form em `prog/v2b2b/b2b001/form.php:65`, `prog/v2b2b/b2b001/form.php:147`
  - `T269` (fotografia:27018): DELETE/INSERT/UPDATE em `prog/v2b2b/b2b001/grid_func-ajax.php:28`, `prog/v2b2b/b2b001/grid_func-ajax.php:38` e mais 4
- **Tabelas só lidas na tela (19):** `C007` (12×, `prog/v2b2b/b2b001/grid.php:625` e mais 11), `D024` (10×, `prog/v2b2b/b2b001/form.php:14` e mais 9), `D013` (9×, `prog/v2b2b/b2b001/form.php:15` e mais 8), `D001` (7×, `prog/v2b2b/b2b001/form.php:30` e mais 6), `D003` (7×, `prog/v2b2b/b2b001/form.php:30` e mais 6), `D009` (7×, `prog/v2b2b/b2b001/grid.php:81` e mais 6), `T005` (6×, `prog/v2b2b/b2b001/grid.php:621` e mais 5), `T007` (6×, `prog/v2b2b/b2b001/grid.php:626` e mais 5), `D049` (4×, `prog/v2b2b/b2b001/grid.php:82` e mais 3), `T003` (4×, `prog/v2b2b/b2b001/grid.php:624` e mais 3), `D022` (3×, `prog/v2b2b/b2b001/grid.php:660` e mais 2), `D082` (2×, `prog/v2b2b/b2b001/grid.php:116` e mais 1), `T002` (2×, `prog/v2b2b/b2b001/grid.php:1149` e mais 1), `T117` (2×, `prog/v2b2b/b2b001/grid.php:1151` e mais 1), `D007` (2×, `prog/v2b2b/b2b001/grid.php:1152` e mais 1), `D021` (1×, `prog/v2b2b/b2b001/form.php:16`), `C004` (1×, `prog/v2b2b/b2b001/grid.php:23`), `D006` (1×, `prog/v2b2b/b2b001/grid.php:980`), `D027` (1×, `prog/v2b2b/b2b001/grid.php:1183`)
- **Classes e métodos chamados:** `B2B001` (526 linhas) (só incluída)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T005_Status_Pedido` (`prog/v2b2b/b2b001/grid.php:647` e mais 3)
- **Chaves C031 lidas (11):** `FaturarPedidoAutomaticamenteB2B`, `HabilitarPedidoOrcamento`, `HabilitarPreco`, `MostrarBoletos`, `MostrarNotasFiscais`, `MostrarOrcamentos`, `MostrarPedidos`, `PermitirRepresentanteEditarPrecoB2B`, `PermitirVendedorEditarPrecoB2B`, `PermitirVendedorExternoEditarPrecoB2B`, `QuantidadePadraoCarrinho`
- **Grafo (Graphify):** 13 nós, 7 funções definidas: `formataPrecoB2B()`, `precoProdutoB2B()`, `valorTotalBoletosB2B()`, `valorTotalNotasB2B()`, `valorTotalOrcamentoB2B()`, `valorTotalPedidoB2B()` …

