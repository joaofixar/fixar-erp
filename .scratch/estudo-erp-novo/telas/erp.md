# Fichas de tela: `prog/erp` — ERP (cadastro de bases e licenças)

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `erp/erp001` | ERP (rótulo da primeira aba) |  | 11 | 5461 | 5 | 41 |
| `erp/erp002` |  |  | 4 | 3234 | 0 | 37 |
| `erp/erp003` |  |  | 4 | 1189 | 3 | 13 |
| `erp/erp004` |  |  | 2 | 483 | 0 | 1 |

Total: 4 telas. 10.367 linhas. 8 tabelas gravadas. 67 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/erp/erp001` — ERP (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 103 "Incluir/Excluir/Alterar Cliente/Fornecedor" (`prog/erp/erp001/grid.php:774`); 120 "P.C.P." (`prog/erp/erp001/tab.php:68` e mais 1); 121 "Clientes ativos/inativos" (`prog/erp/erp001/tab.php:62` e mais 1); 122 (ausente da C029) (`prog/erp/erp001/tab.php:71`); 123 (ausente da C029) (`prog/erp/erp001/tab.php:74`); 124 (ausente da C029) (`prog/erp/erp001/tab.php:77`); 125 (ausente da C029) (`prog/erp/erp001/tab.php:80`); 126 (ausente da C029) (`prog/erp/erp001/tab.php:83`); 127 (ausente da C029) (`prog/erp/erp001/tab.php:44` e mais 1); 128 "Catálogo" (`prog/erp/erp001/tab.php:53` e mais 1); 130 "Orçamentos" (`prog/erp/erp001/tab.php:47` e mais 1); 131 "Pedidos" (`prog/erp/erp001/tab.php:50` e mais 1); …
- **Arquivos:** 11 arquivos. 5.461 linhas. `grid.php` (3234), `form.php` (441), `outro.php` (432), `grid_func-callback.php` (399), `tab.php` (276), `grid_func-ajax.php` (248), `content.php` (237), `grid_func-js.php` (84), `form_func-ajax.php` (56), `tab_func-ajax.php` (34), `form_func-js.php` (20)
- **Abas (tab.php):** ERP (`tab.php:15`), Pré Pedido (`tab.php:45`), Orçamentos (`tab.php:48`), Pedidos (`tab.php:51`), Catálogo (`tab.php:54`), Notas Fiscais (`tab.php:57`), Produtos NF (`tab.php:60`), Clientes Ativos (`tab.php:63`), Clientes Inativos (`tab.php:66`), Resumo (`tab.php:69`), ABC Clientes (`tab.php:72`), ABC Produtos (`tab.php:75`) …
- **Ações (`case` do roteador, 69):** `erp001ContentPrincipal`, `erp001ContentPrincipal2`, `erp001ContentPrincipal2rows`, `erp001ContentPrincipalCliente`, `erp001ContentPrincipalerp`, `erp001ContentSaldoGeralPedidos`, `erp001contentEditarAgenda`, `erp001contentEditarContato`, `erp001ContentPedidosProduto`, `erp001ContentDiMP`, `erp001ContentDiLP`, `erp001ContentCliente`, `erp001FormSetCliente`, `erp001FormEditarAgenda`, `erp001FormEditarContato`, `atualizarCamposClientes`, `adicionarCliente`, `removerCliente`, `erp001GridPrincipalOrcamentos`, `erp001GridPrincipalPedidos` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D013` (fotografia:3952): form em `prog/erp/erp001/form.php:310`
  - `D085` (fotografia:6827): form em `prog/erp/erp001/form.php:154`
  - `T189` (fotografia:23794): UPDATE em `prog/erp/erp001/grid_func-ajax.php:43`, `prog/erp/erp001/grid_func-ajax.php:53` e mais 1
  - `T194` (fotografia:23924): INSERT/UPDATE em `prog/erp/erp001/form_func-ajax.php:31`, `prog/erp/erp001/form_func-ajax.php:39` e mais 2
  - `T198` (fotografia:24027): DELETE/INSERT em `prog/erp/erp001/grid_func-ajax.php:182`, `prog/erp/erp001/grid_func-ajax.php:190` e mais 2
- **Tabelas só lidas na tela (41):** `C007` (42×, `prog/erp/erp001/grid.php:65` e mais 41), `D024` (22×, `prog/erp/erp001/form_func-ajax.php:13` e mais 21), `D009` (13×, `prog/erp/erp001/grid.php:50` e mais 12), `D049` (13×, `prog/erp/erp001/grid.php:51` e mais 12), `D006` (12×, `prog/erp/erp001/grid.php:70` e mais 11), `D082` (11×, `prog/erp/erp001/grid.php:52` e mais 10), `T007` (11×, `prog/erp/erp001/grid.php:69` e mais 10), `R001` (11×, `prog/erp/erp001/grid.php:1059` e mais 10), `T005` (9×, `prog/erp/erp001/grid.php:68` e mais 8), `D001` (9×, `prog/erp/erp001/grid.php:436` e mais 8), `D018` (7×, `prog/erp/erp001/form.php:54` e mais 6), `D020` (6×, `prog/erp/erp001/form.php:53` e mais 5), `T006` (6×, `prog/erp/erp001/grid.php:64` e mais 5), `T008` (6×, `prog/erp/erp001/grid.php:433` e mais 5), `C008` (5×, `prog/erp/erp001/grid.php:437` e mais 4), `T004` (4×, `prog/erp/erp001/grid.php:49` e mais 3), `T147` (4×, `prog/erp/erp001/grid_func-callback.php:81` e mais 3), `T002` (4×, `prog/erp/erp001/outro.php:204` e mais 3), `D021` (3×, `prog/erp/erp001/grid.php:490` e mais 2), `D037` (3×, `prog/erp/erp001/grid.php:649` e mais 2), `R002` (3×, `prog/erp/erp001/grid.php:2232` e mais 2), `C004` (3×, `prog/erp/erp001/grid.php:2235` e mais 2), `T003` (2×, `prog/erp/erp001/grid.php:59` e mais 1), `T127` (2×, `prog/erp/erp001/grid.php:462` e mais 1), `D087` (2×, `prog/erp/erp001/grid.php:747` e mais 1) …
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída); `EST008` (1979 linhas): `updateT174_Flag_Indisponivel` (`EST008.php:44`), `updateT174_Flag_Gerar_OC` (`EST008.php:28`); `EST014` (1748 linhas): `updateCheckboxGerarCotar` (`EST014.php:13`), `updateCheckboxGerarComprar` (`EST014.php:35`), `est014AplicarIPV` (`EST014.php:81`)
- **Tabelas gravadas pelos métodos chamados:** `T174` (EST008::updateT174_Flag_Gerar_OC, EST008::updateT174_Flag_Indisponivel, EST014::est014AplicarIPV), `T189` (EST014::updateCheckboxGerarComprar, EST014::updateCheckboxGerarCotar)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Quantidade_Pedido` (`prog/erp/erp001/grid.php:2600` e mais 1), `T005_Status_Pedido` (`prog/erp/erp001/grid.php:36` e mais 3)
- **Chaves C031 lidas (2):** `CRMNomeTab`, `diasMedioPrazo`
- **Mensagens e validações visíveis (1):**
  - "Registro excluído" (`prog/erp/erp001/grid_func-js.php:29`)
- **Abre as telas:** `cad/cad001`, `ven/ven001`, `ven/ven002`, `cad/cad002`, `etc/etc006`, `ven/ven003`, `erp/erp005`
- **Grafo (Graphify):** 45 nós, 34 funções definidas: `abre_doc()`, `ven001CampoExclusao()`, `ven001ExcluirProduto()`, `ven001ExcluirServico()`, `campoCancelamento()`, `campoExclusao()` …
- **Aberta por:** `erp/erp002`

### `prog/erp/erp002`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 3.234 linhas. `grid.php` (2364), `grid_func-callback.php` (659), `grid_func-ajax.php` (155), `grid_func-js.php` (56)
- **Ações (`case` do roteador, 23):** `erp002GridAgenda`, `erp002GridContatos`, `erp002GridPrincipalOrcamentosClienteAbertos`, `erp002GridPrincipalOrcamentosClienteFechados`, `erp002GridPrincipalOrcamentosClienteCancelados`, `erp002GridPrincipalPedidosClienteAbertos`, `erp002GridPrincipalPedidosClienteFaturado`, `erp002GridPrincipalPedidosClienteCancelados`, `erp002gridProdutosCliente`, `erp002GridNotasEmitidasCliente`, `erp002GridRequisicao`, `erp002GridCotacao`, `erp002GridOrdem`, `erp002GridNFRecebida`, `erp002GridConhecimentos`, `erp002GridEmail`, `downloadxml`, `atualizarPedidosVenda`, `atualizarTotal`, `excluirNota` …
- **Tabelas só lidas na tela (37):** `C007` (39×, `prog/erp/erp002/grid.php:45` e mais 38), `D024` (21×, `prog/erp/erp002/grid.php:274` e mais 20), `D009` (20×, `prog/erp/erp002/grid.php:262` e mais 19), `D049` (20×, `prog/erp/erp002/grid.php:263` e mais 19), `D006` (18×, `prog/erp/erp002/grid.php:283` e mais 17), `T006` (17×, `prog/erp/erp002/grid.php:277` e mais 16), `D082` (15×, `prog/erp/erp002/grid.php:264` e mais 14), `T005` (13×, `prog/erp/erp002/grid.php:281` e mais 12), `T007` (13×, `prog/erp/erp002/grid.php:282` e mais 12), `T004` (11×, `prog/erp/erp002/grid.php:261` e mais 10), `D013` (7×, `prog/erp/erp002/grid.php:133` e mais 6), `D001` (7×, `prog/erp/erp002/grid.php:1390` e mais 6), `T008` (6×, `prog/erp/erp002/grid.php:1289` e mais 5), `D018` (5×, `prog/erp/erp002/grid.php:1440` e mais 4), `T003` (4×, `prog/erp/erp002/grid.php:271` e mais 3), `T012` (4×, `prog/erp/erp002/grid.php:1577` e mais 3), `T173` (4×, `prog/erp/erp002/grid_func-callback.php:147` e mais 3), `T147` (4×, `prog/erp/erp002/grid_func-callback.php:350` e mais 3), `T178` (3×, `prog/erp/erp002/grid.php:824` e mais 2), `T174` (3×, `prog/erp/erp002/grid_func-callback.php:152` e mais 2), `C008` (2×, `prog/erp/erp002/grid.php:1391` e mais 1), `T127` (2×, `prog/erp/erp002/grid.php:1415` e mais 1), `T059` (2×, `prog/erp/erp002/grid.php:1578` e mais 1), `T013` (2×, `prog/erp/erp002/grid.php:2012` e mais 1), `T014` (2×, `prog/erp/erp002/grid.php:2015` e mais 1) …
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída); `EST004` (10716 linhas): `atualizarPedidosVenda` (`EST004.php:2878`), `est004AtualizarTotal` (`EST004.php:4579`), `excluirNota` (`EST004.php:4629`), `receberNota` (`EST004.php:1827`), `est004AtualizarParcelas` (`EST004.php:4916`), `est004ConcluirInclusaoItens` (`EST004.php:5216`)
- **Tabelas gravadas pelos métodos chamados:** `T001` (EST004::excluirNota), `T005` (EST004::atualizarPedidosVenda), `T006` (EST004::atualizarPedidosVenda), `T013` (EST004::est004ConcluirInclusaoItens, EST004::excluirNota, EST004::receberNota), `T013A` (EST004::excluirNota), `T014` (EST004::est004AtualizarTotal, EST004::est004ConcluirInclusaoItens, EST004::excluirNota…), `T014A` (EST004::excluirNota), `T015` (EST004::excluirNota), `T085` (EST004::excluirNota), `T086` (EST004::excluirNota), `T154` (EST004::est004AtualizarParcelas, EST004::excluirNota), `T178` (EST004::atualizarPedidosVenda), `T224` (EST004::excluirNota), `T225` (EST004::est004ConcluirInclusaoItens), `T225A` (EST004::est004ConcluirInclusaoItens)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T005_Status_Pedido` (`prog/erp/erp002/grid.php:249` e mais 9)
- **Mensagens e validações visíveis (2):**
  - "Nota excluída com sucesso!" (`prog/erp/erp002/grid_func-js.php:9`)
  - "Nota de entrada cadastrada com sucesso! Favor conferir e finalizar a nota" (`prog/erp/erp002/grid_func-js.php:38`)
- **Abre as telas:** `ven/ven001`, `ven/ven002`, `etc/etc006`, `est/est006`, `est/est008`, `est/est004`, `cad/cad001`, `erp/erp001`, `est/erp002`, `est/est005` …
- **Grafo (Graphify):** 50 nós, 46 funções definidas: `campoCancelamento()`, `campoCancelar()`, `campoeMail()`, `campoExclusao()`, `campoExclusaoFin()`, `campoExclusaoPedido()` …

### `prog/erp/erp003`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 1.189 linhas. `grid.php` (548), `grid_func-ajax.php` (249), `grid_func-js.php` (217), `grid_func-callback.php` (175)
- **Ações (`case` do roteador, 22):** `erp003GridReceber`, `erp003GridPagar`, `erp003GridBoletos`, `excluir`, `excluirAd`, `verificaBaixar`, `verificaAgrupar`, `verificaRemessa`, `verificaReparcelar`, `checkboxRm`, `checkboxRp`, `checkboxBx`, `downloadRemessa`, `carregaInputBaixar`, `carregaInputReparcelar`, `carregaInputAgrupar`, `carregaInputRemessa`, `baixarTitulosEditarCampo`, `atualizaTotaisBaixar`, `fin001atualizarParcelas` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T002` (fotografia:14415): UPDATE em `prog/erp/erp003/grid_func-ajax.php:165`
  - `T103` (fotografia:21328): UPDATE em `prog/erp/erp003/grid_func-ajax.php:161`, `prog/erp/erp003/grid_func-ajax.php:172` e mais 1
  - `T180` (fotografia:23510): DELETE em `prog/erp/erp003/grid_func-ajax.php:237`
- **Tabelas só lidas na tela (13):** `D024` (3×, `prog/erp/erp003/grid.php:73` e mais 2), `T117` (2×, `prog/erp/erp003/grid.php:75` e mais 1), `D014` (2×, `prog/erp/erp003/grid.php:76` e mais 1), `T007` (2×, `prog/erp/erp003/grid.php:78` e mais 1), `C007` (2×, `prog/erp/erp003/grid.php:80` e mais 1), `D027` (1×, `prog/erp/erp003/grid.php:74`), `D046` (1×, `prog/erp/erp003/grid.php:77`), `T035` (1×, `prog/erp/erp003/grid.php:79`), `T015` (1×, `prog/erp/erp003/grid.php:297`), `D073` (1×, `prog/erp/erp003/grid.php:301`), `D031` (1×, `prog/erp/erp003/grid.php:302`), `D154` (1×, `prog/erp/erp003/grid.php:304`), `D007` (1×, `prog/erp/erp003/grid.php:449`)
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída); `FIN001` (7368 linhas): `excluir` (`FIN001.php:1440`), `excluirAcrescimoDesconto` (`FIN001.php:1513`), `listaItensParaBaixar` (`FIN001.php:1532`), `listaItensParaAgrupar` (`FIN001.php:2246`), `listaItensParaRemessa` (`FIN001.php:2194`), `listaItensParaReparcelar` (`FIN001.php:2295`), `updateT002_Flag_Enviar_Banco` (`FIN001.php:2348`), `updateT002_Flag_Reparcelar` (`FIN001.php:2382`), `updateT002_Flag_Baixar_Lote` (`FIN001.php:2399`), `geraDownloadRemessa` (`FIN001.php:3172`) …
- **Tabelas gravadas pelos métodos chamados:** `D007` (FIN001::fin001reparcelar), `T002` (FIN001::excluir, FIN001::fin001reparcelar, FIN001::updateT002_Flag_Baixar_Lote…), `T020` (FIN001::excluirAcrescimoDesconto, FIN001::fin001reparcelar), `T117` (FIN001::fin001reparcelar), `T166` (FIN001::fin001reparcelar), `T180` (FIN001::fin001AtualizarParcelas2, FIN001::fin001reparcelar), `T244` (FIN001::fin001reparcelar)
- **Mensagens e validações visíveis (1):**
  - "Remessa removida com sucesso!" (`prog/erp/erp003/grid_func-js.php:95`)
- **Abre as telas:** `fin/fin001`, `fin/fin002`, `etc/etc006`, `fin/fin005`
- **Grafo (Graphify):** 30 nós, 26 funções definidas: `atualizaTotaisBaixar()`, `baixarTitulosEditarCampo()`, `carregaInputAgrupar()`, `carregaInputBaixar()`, `carregaInputRemessa()`, `carregaInputReparcelar()` …. Chama fora da tela: `number_format()` (formulario.php)

### `prog/erp/erp004`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 2 arquivos. 483 linhas. `grid.php` (452), `grid_func-callback.php` (31)
- **Ações (`case` do roteador, 3):** `erp004GridResumo`, `erp004GridResumoMensal`, `erp004GridResumoAnual`
- **Tabelas só lidas na tela (1):** `R001` (3×, `prog/erp/erp004/grid.php:71` e mais 2)
- **Grafo (Graphify):** 6 nós, 4 funções definidas: `erp004_Dia_Semana()`, `retornaAno()`, `retornaMes()`, `gridLinha()`

