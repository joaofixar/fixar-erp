# Fichas de tela: `prog/srm` — S.R.M. (Fornecedor)

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `srm/srm001` | S.R.M. | 181 | 10 | 4822 | 6 | 44 |
| `srm/srm002` |  |  | 4 | 1025 | 0 | 17 |

Total: 2 telas. 5.847 linhas. 6 tabelas gravadas. 55 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/srm/srm001` — S.R.M.

- **Menu:** Estoque › S.R.M., acesso C029 nº 181 "SRM" (`bibliotecas/menu.php:26`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 13 "Emails" (`prog/srm/srm001/tab.php:63`); 21 "Notas Fiscais Recebidas" (`prog/srm/srm001/tab.php:51` e mais 1); 22 "Conhecimentos" (`prog/srm/srm001/tab.php:54` e mais 1); 94 "Compras de Estoque" (`prog/srm/srm001/tab.php:57` e mais 2); 95 "Compras de Emergência" (`prog/srm/srm001/tab.php:57` e mais 1); 133 (ausente da C029) (`prog/srm/srm001/tab.php:110`); 139 (ausente da C029) (`prog/srm/srm001/tab.php:66` e mais 1); 170 "Cotação de Compra" (`prog/srm/srm001/tab.php:45` e mais 1); 171 "Ordem de Compra" (`prog/srm/srm001/tab.php:48` e mais 1)
- **Arquivos:** 10 arquivos. 4.822 linhas. `grid.php` (2413), `grid_func-ajax.php` (487), `outro.php` (419), `grid_func-callback.php` (381), `content.php` (324), `tab.php` (321), `form.php` (234), `grid_func-js.php` (136), `form_func-ajax.php` (63), `tab_func-ajax.php` (44)
- **Abas (tab.php):** SRM (`tab.php:15`), Cotação Compra (`tab.php:46`), Ordem Compra (`tab.php:49`), Notas Fiscais Recebidas (`tab.php:52`), Conhecimentos (`tab.php:55`), Compras Estoque/Emergência (`tab.php:58`), Fornecedores (`tab.php:61`), Email (`tab.php:64`), Pagar (`tab.php:67`), Cotação Compra (`tab.php:96`), Ordem Compra (`tab.php:99`), Notas Fiscais Recebidas (`tab.php:102`) …
- **Ações (`case` do roteador, 79):** `srm001ContentPrincipal`, `srm001ContentPrincipal2`, `srm001ContentPrincipal2rows`, `srm001ContentPrincipalCliente`, `srm001ContentPrincipalSRM`, `srm001ContentComprasEstoque`, `srm001ContentCotacoes`, `srm001ContentOrdensCompra`, `srm001ContentNotasRecebidas`, `srm001ContentConhecimentos`, `srm001ContentCotacoesFornecedor`, `srm001ContentOrdensComprasFornecedor`, `srm001ContentNotasRecebidasFornecedor`, `srm001ContentFornecedor`, `srm001ContentComprasEstoqueModelos`, `srm001FormSetFornecedor`, `srm001FormImportarXML`, `atualizarCamposFornecedor`, `adicionarFornecedor`, `removerFornecedor` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D024` (fotografia:4506): UPDATE em `prog/srm/srm001/grid_func-ajax.php:482`
  - `T005` (fotografia:15218): UPDATE em `prog/srm/srm001/grid_func-ajax.php:300`, `prog/srm/srm001/grid_func-ajax.php:315` e mais 4
  - `T007` (fotografia:16254): form em `prog/srm/srm001/form.php:153`
  - `T189` (fotografia:23794): UPDATE em `prog/srm/srm001/grid_func-ajax.php:43`, `prog/srm/srm001/grid_func-ajax.php:53` e mais 1
  - `T198` (fotografia:24027): DELETE/INSERT em `prog/srm/srm001/grid_func-ajax.php:182`, `prog/srm/srm001/grid_func-ajax.php:190` e mais 2
  - `T240` (fotografia:26208): INSERT/UPDATE em `prog/srm/srm001/form_func-ajax.php:31`, `prog/srm/srm001/form_func-ajax.php:46` e mais 4
- **Tabelas só lidas na tela (44):** `C007` (35×, `prog/srm/srm001/form.php:41` e mais 34), `D006` (22×, `prog/srm/srm001/grid.php:78` e mais 21), `D018` (16×, `prog/srm/srm001/form.php:46` e mais 15), `D009` (13×, `prog/srm/srm001/grid.php:47` e mais 12), `D049` (13×, `prog/srm/srm001/grid.php:48` e mais 12), `T225` (13×, `prog/srm/srm001/grid.php:71` e mais 12), `T224` (13×, `prog/srm/srm001/grid.php:75` e mais 12), `T013` (10×, `prog/srm/srm001/grid.php:77` e mais 9), `D020` (9×, `prog/srm/srm001/form.php:45` e mais 8), `T014` (9×, `prog/srm/srm001/grid.php:76` e mais 8), `C004` (9×, `prog/srm/srm001/grid.php:377` e mais 8), `D001` (9×, `prog/srm/srm001/grid.php:389` e mais 8), `D082` (8×, `prog/srm/srm001/grid.php:49` e mais 7), `D013` (7×, `prog/srm/srm001/grid.php:68` e mais 6), `T219` (5×, `prog/srm/srm001/grid.php:46` e mais 4), `T218` (5×, `prog/srm/srm001/grid.php:64` e mais 4), `D021` (5×, `prog/srm/srm001/grid.php:379` e mais 4), `D116` (4×, `prog/srm/srm001/grid.php:378` e mais 3), `T012` (4×, `prog/srm/srm001/grid.php:604` e mais 3), `D037` (4×, `prog/srm/srm001/grid.php:1015` e mais 3), `T002` (4×, `prog/srm/srm001/outro.php:172` e mais 3), `D022` (3×, `prog/srm/srm001/grid.php:380` e mais 2), `T015` (2×, `prog/srm/srm001/grid.php:843` e mais 1), `D014` (2×, `prog/srm/srm001/grid.php:2269` e mais 1), `T206` (2×, `prog/srm/srm001/grid_func-callback.php:220` e mais 1) …
- **Classes e métodos chamados:** `EST004` (10716 linhas): `prepararImportarXML` (`EST004.php:313`); `EST008` (1979 linhas): `updateT174_Flag_Indisponivel` (`EST008.php:44`), `updateT174_Flag_Gerar_OC` (`EST008.php:28`); `EST014` (1748 linhas): `updateCheckboxGerarCotar` (`EST014.php:13`), `updateCheckboxGerarComprar` (`EST014.php:35`), `est014AplicarIPV` (`EST014.php:81`); `VEN001` (15360 linhas): `VEN001ClientesProcessar` (`VEN001.php:10289`); `VEN002` (10343 linhas): `VEN002AgruparPedidosSelecionados` (`VEN002.php:5797`); `VEN012` (24304 linhas): `gerarNotaFiscalPedidos` (`VEN012.php:19031`)
- **Tabelas gravadas pelos métodos chamados:** `C004` (VEN012::gerarNotaFiscalPedidos), `D007` (VEN012::gerarNotaFiscalPedidos), `D009` (VEN012::gerarNotaFiscalPedidos), `D024` (VEN001::VEN001ClientesProcessar, VEN012::gerarNotaFiscalPedidos), `D085` (VEN012::gerarNotaFiscalPedidos), `T001` (VEN012::gerarNotaFiscalPedidos), `T002` (VEN012::gerarNotaFiscalPedidos), `T005` (VEN002::VEN002AgruparPedidosSelecionados, VEN012::gerarNotaFiscalPedidos), `T005A` (VEN002::VEN002AgruparPedidosSelecionados), `T006` (VEN002::VEN002AgruparPedidosSelecionados), `T006A` (VEN002::VEN002AgruparPedidosSelecionados), `T007` (VEN012::gerarNotaFiscalPedidos), `T008` (VEN012::gerarNotaFiscalPedidos), `T015` (VEN012::gerarNotaFiscalPedidos), `T019` (VEN012::gerarNotaFiscalPedidos) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Quantidade_DF` (`prog/srm/srm001/grid_func-callback.php:228` e mais 1), `D009_Quantidade_DM` (`prog/srm/srm001/grid_func-callback.php:244` e mais 1), `D009_Quantidade_MP` (`prog/srm/srm001/grid_func-callback.php:263` e mais 1), `T225_Quantidade_Pendente_3` (`prog/srm/srm001/grid.php:338` e mais 1)
- **Chaves C031 lidas (6):** `SRMNomeTab`, `desabilitarCompraEmergencia`, `desabilitarCotacaoEmergencia`, `desabilitarMontagemEmergencia`, `desabilitarProducaoEmergencia`, `desabilitarServicoEmergencia`
- **Mensagens e validações visíveis (4):**
  - "' . $retorno . '" (`prog/srm/srm001/form.php:192`)
  - "Nota Importada com Sucesso!" (`prog/srm/srm001/form.php:195`)
  - "Nota excluída com sucesso!" (`prog/srm/srm001/grid_func-js.php:32`)
  - "Registro excluído" (`prog/srm/srm001/grid_func-js.php:87`)
- **Abre as telas:** `est/est016`, `est/est004`, `est/est015`, `cad/cad002`, `est/est005`, `fin/fin002`, `est/est006`, `cad/cad001`, `crm/crm001`
- **Grafo (Graphify):** 41 nós, 31 funções definidas: `EST004ExcluirNota()`, `EST004ReverterCancelamento()`, `est005CampoCancelar()`, `est005CampoExcluir()`, `est009CheckboxComprar()`, `est009CheckboxCotar()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `cad/cad001`, `est/est004`, `est/est015`, `est/est016`, `imp/imp003`, `imp/imp004`, `imp/imp005`

### `prog/srm/srm002`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 1.025 linhas. `grid_func-callback.php` (674), `grid.php` (193), `grid_func-ajax.php` (155), `grid_func-js.php` (3)
- **Ações (`case` do roteador, 8):** `srm002GridContasPagarFornecedor`, `downloadxml`, `atualizarPedidosVenda`, `atualizarTotal`, `excluirNota`, `receberNota`, `atualizarParcelas`, `est004ConcluirInclusaoItens`
- **Tabelas só lidas na tela (17):** `T173` (4×, `prog/srm/srm002/grid_func-callback.php:164` e mais 3), `T147` (4×, `prog/srm/srm002/grid_func-callback.php:367` e mais 3), `C007` (3×, `prog/srm/srm002/grid.php:53` e mais 2), `T174` (3×, `prog/srm/srm002/grid_func-callback.php:169` e mais 2), `D024` (2×, `prog/srm/srm002/grid.php:47` e mais 1), `D014` (2×, `prog/srm/srm002/grid.php:50` e mais 1), `T005` (2×, `prog/srm/srm002/grid_func-callback.php:22` e mais 1), `T015` (1×, `prog/srm/srm002/grid.php:44`), `D073` (1×, `prog/srm/srm002/grid.php:48`), `D031` (1×, `prog/srm/srm002/grid.php:49`), `D154` (1×, `prog/srm/srm002/grid.php:51`), `T007` (1×, `prog/srm/srm002/grid.php:52`), `T018` (1×, `prog/srm/srm002/grid.php:129`), `T013` (1×, `prog/srm/srm002/grid_func-ajax.php:17`), `T003` (1×, `prog/srm/srm002/grid_func-callback.php:352`), `D047` (1×, `prog/srm/srm002/grid_func-callback.php:354`), `T127` (1×, `prog/srm/srm002/grid_func-callback.php:500`)
- **Classes e métodos chamados:** `EST004` (10716 linhas): `atualizarPedidosVenda` (`EST004.php:2878`), `est004AtualizarTotal` (`EST004.php:4579`), `excluirNota` (`EST004.php:4629`), `receberNota` (`EST004.php:1827`), `est004AtualizarParcelas` (`EST004.php:4916`), `est004ConcluirInclusaoItens` (`EST004.php:5216`)
- **Tabelas gravadas pelos métodos chamados:** `T001` (EST004::excluirNota), `T005` (EST004::atualizarPedidosVenda), `T006` (EST004::atualizarPedidosVenda), `T013` (EST004::est004ConcluirInclusaoItens, EST004::excluirNota, EST004::receberNota), `T013A` (EST004::excluirNota), `T014` (EST004::est004AtualizarTotal, EST004::est004ConcluirInclusaoItens, EST004::excluirNota…), `T014A` (EST004::excluirNota), `T015` (EST004::excluirNota), `T085` (EST004::excluirNota), `T086` (EST004::excluirNota), `T154` (EST004::est004AtualizarParcelas, EST004::excluirNota), `T178` (EST004::atualizarPedidosVenda), `T224` (EST004::excluirNota), `T225` (EST004::est004ConcluirInclusaoItens), `T225A` (EST004::est004ConcluirInclusaoItens)
- **Abre as telas:** `etc/etc006`, `ven/ven002`, `cad/cad001`, `fin/fin002`, `ven/ven001`
- **Grafo (Graphify):** 48 nós, 44 funções definidas: `campoCancelamento()`, `campoCancelamentoOrcamento()`, `campoCancelar()`, `campoeMail()`, `campoExclusao()`, `campoExclusaoFin()` …

