# Fichas de tela: `prog/rel` — Relatórios

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `rel/est` |  |  | 1 | 508 | 0 | 17 |
| `rel/fin` | Relatorio (rótulo da primeira aba) |  | 5 | 1758 | 0 | 18 |
| `rel/menu` |  |  | 1 | 150 | 0 | 0 |
| `rel/pedidos` |  |  | 1 | 265 | 0 | 12 |
| `rel/relatorios` |  |  | 9 | 6360 | 0 | 33 |
| `rel/ven` | PMV - Recebimento (rótulo da primeira aba) |  | 2 | 3312 | 0 | 22 |

Total: 6 telas. 12.353 linhas. 0 tabelas gravadas. 44 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/rel/est`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 508 linhas. `grid.php` (508)
- **Ações (`case` do roteador, 2):** `inventario`, `estoque`
- **Tabelas só lidas na tela (17):** `T001` (13×, `prog/rel/est/grid.php:126` e mais 12), `D001` (4×, `prog/rel/est/grid.php:11` e mais 3), `C004` (4×, `prog/rel/est/grid.php:277` e mais 3), `D005` (3×, `prog/rel/est/grid.php:13` e mais 2), `D049` (3×, `prog/rel/est/grid.php:14` e mais 2), `D009` (3×, `prog/rel/est/grid.php:15` e mais 2), `D002` (3×, `prog/rel/est/grid.php:18` e mais 2), `D060` (3×, `prog/rel/est/grid.php:19` e mais 2), `D037` (3×, `prog/rel/est/grid.php:20` e mais 2), `D024` (3×, `prog/rel/est/grid.php:21` e mais 2), `C008` (1×, `prog/rel/est/grid.php:16`), `D003` (1×, `prog/rel/est/grid.php:17`), `D041` (1×, `prog/rel/est/grid.php:22`), `D082` (1×, `prog/rel/est/grid.php:23`), `D149` (1×, `prog/rel/est/grid.php:24`), `T066` (1×, `prog/rel/est/grid.php:112`), `D006` (1×, `prog/rel/est/grid.php:127`)
- **Abre as telas:** `cad/cad002`
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/rel/fin` — Relatorio (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 1.758 linhas. `grid.php` (1429), `outro.php` (235), `tab.php` (43), `grid_func-callback.php` (26), `content.php` (25)
- **Abas (tab.php):** Relatorio (`tab.php:13`), DRE Financeiro Caixas e Bancos (`tab.php:30`)
- **Ações (`case` do roteador, 16):** `demonstrativoDRE`, `dre`, `dreFinanceiro`, `fluxoCaixa`, `balancete`, `inadimplencia`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `demonstrativoDRE`, `demonstrativoDRE`, `dre`
- **Tabelas só lidas na tela (18):** `D014` (34×, `prog/rel/fin/grid.php:118` e mais 33), `T019` (30×, `prog/rel/fin/grid.php:118` e mais 29), `C004` (13×, `prog/rel/fin/grid.php:16` e mais 12), `D024` (11×, `prog/rel/fin/grid.php:17` e mais 10), `T007` (7×, `prog/rel/fin/grid.php:14` e mais 6), `T018` (7×, `prog/rel/fin/grid.php:880` e mais 6), `D032` (7×, `prog/rel/fin/grid.php:996` e mais 6), `T002` (6×, `prog/rel/fin/grid.php:653` e mais 5), `D006` (5×, `prog/rel/fin/grid.php:90` e mais 4), `T015` (4×, `prog/rel/fin/grid.php:846` e mais 3), `D018` (2×, `prog/rel/fin/grid.php:1329` e mais 1), `D020` (2×, `prog/rel/fin/grid.php:1330` e mais 1), `C007` (2×, `prog/rel/fin/grid.php:1331` e mais 1), `T013` (1×, `prog/rel/fin/grid.php:146`), `T008` (1×, `prog/rel/fin/grid.php:176`), `D007` (1×, `prog/rel/fin/grid.php:733`), `T117` (1×, `prog/rel/fin/grid.php:795`), `T005` (1×, `prog/rel/fin/grid.php:829`)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D032_Tipo` (`prog/rel/fin/grid.php:1080` e mais 1)
- **Grafo (Graphify):** 8 nós, 3 funções definidas: `diaSemana()`, `gridLinha()`, `demoItemDRE()`. Chama fora da tela: `number_format()` (formulario.php), `.log()` (CAD002.php)

### `prog/rel/menu`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 150 linhas. `menu.php` (150)
- **Ações (`case` do roteador, 4):** `content`, `listaRelatorios`, `mostraGrid`, `mostraListaRelatorios`
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/rel/pedidos`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 265 linhas. `entregador.php` (265)
- **Ações (`case` do roteador, 4):** `content`, `tab`, `filtro`, `relatorio`
- **Tabelas só lidas na tela (12):** `T006` (5×, `prog/rel/pedidos/entregador.php:178` e mais 4), `D022` (2×, `prog/rel/pedidos/entregador.php:141` e mais 1), `D006` (1×, `prog/rel/pedidos/entregador.php:165`), `T005` (1×, `prog/rel/pedidos/entregador.php:166`), `D024` (1×, `prog/rel/pedidos/entregador.php:167`), `D020` (1×, `prog/rel/pedidos/entregador.php:168`), `D009` (1×, `prog/rel/pedidos/entregador.php:169`), `D049` (1×, `prog/rel/pedidos/entregador.php:170`), `D001` (1×, `prog/rel/pedidos/entregador.php:171`), `T208` (1×, `prog/rel/pedidos/entregador.php:172`), `C004` (1×, `prog/rel/pedidos/entregador.php:173`), `D037` (1×, `prog/rel/pedidos/entregador.php:174`)
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/rel/relatorios`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 9 arquivos. 6.360 linhas. `mapaVenda.php` (1449), `extratoCliente.php` (1427), `vendas_mensais.php` (1032), `dre.php` (702), `balancete.php` (442), `analitico_vendas.php` (437), `relatorio_comissao_representante.php` (377), `balancete1.php` (351), `relatorios.php` (143)
- **Ações (`case` do roteador, 13):** `contentVendasPeriodo`, `formVendasPeriodo`, `relatorio`, `imprimir`, `contentRelatorio`, `relatorio`, `content`, `listaRelatorios`, `mostraGrid`, `mostraListaRelatorios`, `contentVendasMensais`, `formVendasMensais`, `vendasMensais`
- **Tabelas só lidas na tela (33):** `T007` (91×, `prog/rel/relatorios/analitico_vendas.php:196` e mais 82), `D006` (80×, `prog/rel/relatorios/analitico_vendas.php:197` e mais 79), `C007` (61×, `prog/rel/relatorios/analitico_vendas.php:325` e mais 60), `T008` (46×, `prog/rel/relatorios/dre.php:327` e mais 37), `D014` (27×, `prog/rel/relatorios/balancete.php:137` e mais 26), `C004` (24×, `prog/rel/relatorios/analitico_vendas.php:148` e mais 23), `D024` (24×, `prog/rel/relatorios/analitico_vendas.php:322` e mais 23), `T019` (18×, `prog/rel/relatorios/balancete.php:134` e mais 17), `D009` (13×, `prog/rel/relatorios/mapaVenda.php:963` e mais 12), `D049` (13×, `prog/rel/relatorios/mapaVenda.php:964` e mais 12), `D001` (13×, `prog/rel/relatorios/mapaVenda.php:965` e mais 12), `T002` (12×, `prog/rel/relatorios/balancete.php:151` e mais 11), `T013` (11×, `prog/rel/relatorios/dre.php:309` e mais 10), `T014` (9×, `prog/rel/relatorios/vendas_mensais.php:391` e mais 8), `T018` (7×, `prog/rel/relatorios/balancete.php:138` e mais 6), `D032` (7×, `prog/rel/relatorios/balancete.php:139` e mais 6), `T015` (7×, `prog/rel/relatorios/balancete.php:152` e mais 6), `D003` (7×, `prog/rel/relatorios/mapaVenda.php:967` e mais 6), `D060` (7×, `prog/rel/relatorios/mapaVenda.php:968` e mais 6), `D018` (6×, `prog/rel/relatorios/analitico_vendas.php:324` e mais 5), `D002` (5×, `prog/rel/relatorios/mapaVenda.php:966` e mais 4), `D027` (4×, `prog/rel/relatorios/extratoCliente.php:296` e mais 3), `T117` (4×, `prog/rel/relatorios/extratoCliente.php:297` e mais 3), `T005` (4×, `prog/rel/relatorios/vendas_mensais.php:479` e mais 3), `T090` (4×, `prog/rel/relatorios/vendas_mensais.php:479` e mais 3) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D024_Gravar_Totalizacao` (`prog/rel/relatorios/extratoCliente.php:92`), `D032_Tipo` (`prog/rel/relatorios/balancete.php:133` e mais 1)
- **Chaves C031 lidas (5):** `somarCOFINSPrecoVenda`, `somarCSLLPrecoVenda`, `somarICMSPrecoVenda`, `somarIRPJPrecoVenda`, `somarPISPrecoVenda`
- **Abre as telas:** `fin/fin001`, `fin/fin002`, `cad/cad001`
- **Grafo (Graphify):** 11 nós, 2 funções definidas: `calcularDiasUteis()`, `formataDataMes()`

### `prog/rel/ven` — PMV - Recebimento (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 2 arquivos. 3.312 linhas. `grid.php` (3285), `tab.php` (27)
- **Abas (tab.php):** PMV - Recebimento (`tab.php:13`), PMV - Vencimento (`tab.php:14`)
- **Ações (`case` do roteador, 18):** `faturamentoData`, `faturamentoExpedicao`, `faturamentoVendedor`, `faturamentoPerfil`, `faturamentoUf`, `faturamentoDepartamento`, `analiticoPrazoMedioRecebimento`, `analiticoPrazoMedioVencimento`, `analiticoVendasPeriodo`, `analiticoComprasPeriodo`, `analiticoVendasPeriodoQte`, `analiticoComprasPeriodoQte`, `analiticoVendasPeriodoQteProduto`, `analiticoComprasPeriodoQteProduto`, `analiticoVendasPeriodoValorProduto`, `analiticoComprasPeriodoValorProduto`, `resumoVendedor`, `prazoMedio`
- **Tabelas só lidas na tela (22):** `T007` (84×, `prog/rel/ven/grid.php:8` e mais 78), `D006` (83×, `prog/rel/ven/grid.php:10` e mais 82), `C007` (59×, `prog/rel/ven/grid.php:27` e mais 58), `T008` (43×, `prog/rel/ven/grid.php:66` e mais 36), `T013` (25×, `prog/rel/ven/grid.php:1302` e mais 24), `T014` (20×, `prog/rel/ven/grid.php:1644` e mais 19), `D024` (10×, `prog/rel/ven/grid.php:414` e mais 9), `D018` (10×, `prog/rel/ven/grid.php:415` e mais 9), `C004` (9×, `prog/rel/ven/grid.php:967` e mais 8), `D001` (7×, `prog/rel/ven/grid.php:521` e mais 5), `D020` (6×, `prog/rel/ven/grid.php:724` e mais 5), `D009` (5×, `prog/rel/ven/grid.php:519` e mais 4), `D049` (5×, `prog/rel/ven/grid.php:520` e mais 4), `D003` (5×, `prog/rel/ven/grid.php:523` e mais 4), `T002` (4×, `prog/rel/ven/grid.php:798` e mais 3), `T005` (4×, `prog/rel/ven/grid.php:2735` e mais 3), `T090` (4×, `prog/rel/ven/grid.php:2735` e mais 3), `C019` (1×, `prog/rel/ven/grid.php:304`), `C021` (1×, `prog/rel/ven/grid.php:305`), `D002` (1×, `prog/rel/ven/grid.php:522`), `D060` (1×, `prog/rel/ven/grid.php:524`), `C008` (1×, `prog/rel/ven/grid.php:525`)
- **Chaves C031 lidas (5):** `somarCOFINSPrecoVenda`, `somarCSLLPrecoVenda`, `somarICMSPrecoVenda`, `somarIRPJPrecoVenda`, `somarPISPrecoVenda`
- **Abre as telas:** `crm/crm001`
- **Grafo (Graphify):** 6 nós, 4 funções definidas: `calcularDiasUteis()`, `formataDataMes()`, `gridLinha()`, `imprimirResumoVendedor()`

