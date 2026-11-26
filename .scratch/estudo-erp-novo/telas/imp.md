# Fichas de tela: `prog/imp` — Importação

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `imp/imp002` |  |  | 1 | 4 | 0 | 0 |
| `imp/imp003` | Importação | 49 | 10 | 8547 | 10 | 40 |
| `imp/imp004` | Quotation (rótulo da primeira aba) |  | 10 | 7055 | 6 | 47 |
| `imp/imp005` | Análise PO | 255 | 10 | 7561 | 6 | 45 |

Total: 4 telas. 23.167 linhas. 16 tabelas gravadas. 62 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/imp/imp002`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 4 linhas. `grid.php` (4)
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/imp/imp003` — Importação

- **Menu:** Comex › Importação, acesso C029 nº 49 "DI - Declaração Importação" (`bibliotecas/menu.php:96`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 258 "Compras Estoque" (`prog/imp/imp003/tab.php:17`); 259 "Compras Emergência" (`prog/imp/imp003/tab.php:17`); 260 "Quotation" (`prog/imp/imp003/tab.php:20`); 261 "Purchase Order" (`prog/imp/imp003/tab.php:23`)
- **Arquivos:** 10 arquivos. 8.547 linhas. `form.php` (3156), `grid.php` (2216), `content.php` (879), `form_func-ajax.php` (549), `outro.php` (442), `grid_func-callback.php` (381), `tab.php` (366), `grid_func-ajax.php` (278), `grid_func-js.php` (158), `form_func-js.php` (122)
- **Abas (tab.php):** Compras (`tab.php:18`), Quotation (`tab.php:21`), Purchase Order (`tab.php:24`), D.I. (`tab.php:27`), Declaração de Importação (`tab.php:58`), Produtos (`tab.php:59`), Produtos (`tab.php:96`), D.I. (`tab.php:98`), D.I. (`tab.php:100`), Adicoes (`tab.php:102`), Total produtos (`tab.php:103`), Documentos (`tab.php:104`) …
- **Ações (`case` do roteador, 107):** `imp003ContentDi`, `imp003ContentDiDeclaracaoImportacao`, `imp003ContentDiIncluir`, `imp003ContentAtualizarLocacao`, `imp003ContentProdutos`, `imp003ContentDocumentos`, `imp003ContentDocumentosDespesa`, `imp003ContentEmail`, `imp003ContentDespesasIncluir`, `imp003ContentLIIncluir`, `imp003contentIncluirContasPagar`, `imp003contentIncluirContasReceber`, `imp003ContentPedidosProduto`, `imp003contentImportarItensOC`, `imp003contentImportarCSV`, `imp003ContentImportarXML`, `imp003ContentAssociarItemProduto`, `imp003ContentPacoteDespesas`, `imp003ContentItensImportadosDI`, `imp003FormDi` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D190` (fotografia:9655): form em `prog/imp/imp003/form.php:3063`
  - `T002` (fotografia:14415): form em `prog/imp/imp003/form.php:2192`
  - `T015` (fotografia:17763): form em `prog/imp/imp003/form.php:1984`
  - `T055` (fotografia:19318): DELETE/UPDATE/form em `prog/imp/imp003/form.php:861`, `prog/imp/imp003/form.php:1092` e mais 7
  - `T055A` (fotografia:19460): DELETE/UPDATE em `prog/imp/imp003/form_func-ajax.php:282`, `prog/imp/imp003/grid_func-ajax.php:129`
  - `T075` (fotografia:20171): form em `prog/imp/imp003/form.php:38`, `prog/imp/imp003/form.php:275` e mais 3
  - `T155` (fotografia:22913): form em `prog/imp/imp003/form.php:1224`, `prog/imp/imp003/form.php:1391`
  - `T168` (fotografia:23209): UPDATE/form em `prog/imp/imp003/form.php:1555`, `prog/imp/imp003/form.php:1704` e mais 2
  - `T270` (fotografia:27046): form em `prog/imp/imp003/form.php:2531`
  - `T310` (fotografia:28591): UPDATE em `prog/imp/imp003/grid_func-ajax.php:219`
- **Tabelas só lidas na tela (40):** `D009` (14×, `prog/imp/imp003/form.php:876` e mais 13), `D024` (12×, `prog/imp/imp003/form.php:1991` e mais 11), `T006` (10×, `prog/imp/imp003/form.php:651` e mais 9), `D049` (10×, `prog/imp/imp003/form.php:875` e mais 9), `C007` (8×, `prog/imp/imp003/grid.php:74` e mais 7), `T005` (7×, `prog/imp/imp003/form.php:652` e mais 6), `D001` (7×, `prog/imp/imp003/form_func-ajax.php:21` e mais 6), `D020` (6×, `prog/imp/imp003/form.php:56` e mais 5), `D018` (6×, `prog/imp/imp003/form.php:56` e mais 5), `D082` (6×, `prog/imp/imp003/form.php:877` e mais 5), `D005` (5×, `prog/imp/imp003/form_func-ajax.php:27` e mais 4), `T007` (5×, `prog/imp/imp003/grid.php:75` e mais 4), `D037` (4×, `prog/imp/imp003/form.php:878` e mais 3), `D154` (4×, `prog/imp/imp003/form.php:2021` e mais 3), `D004` (4×, `prog/imp/imp003/form_func-ajax.php:143` e mais 3), `T066` (3×, `prog/imp/imp003/form_func-ajax.php:142` e mais 2), `T296` (3×, `prog/imp/imp003/form_func-ajax.php:275` e mais 2), `D119` (3×, `prog/imp/imp003/grid.php:70` e mais 2), `D118` (3×, `prog/imp/imp003/grid.php:71` e mais 2), `D044` (3×, `prog/imp/imp003/grid.php:72` e mais 2), `D001A` (2×, `prog/imp/imp003/form_func-ajax.php:29` e mais 1), `D146` (2×, `prog/imp/imp003/form_func-ajax.php:144` e mais 1), `T225` (2×, `prog/imp/imp003/grid.php:547` e mais 1), `D122` (2×, `prog/imp/imp003/grid.php:1038` e mais 1), `T161` (2×, `prog/imp/imp003/grid.php:1040` e mais 1) …
- **Classes e métodos chamados:** `IMP003` (3710 linhas): `atualizaOC` (`IMP003.php:135`), `importarCSV` (`IMP003.php:1870`), `prepararImportarXML` (`IMP003.php:2515`), `associarProduto` (`IMP003.php:3189`), `incluirPacoteDespesas` (`IMP003.php:3264`), `retornaAliquotas` (`IMP003.php:2331`), `gerarNotaFiscal` (`IMP003.php:159`), `calcularDespesas` (`IMP003.php:3384`), `Calcular` (`IMP003.php:113`), `GerarXMLLI` (`IMP003.php:1549`) …; `IMP005` (3484 linhas): `alteraStatusPO` (`IMP005.php:3176`); `IMP006` (1672 linhas): `prepararCabecalhoConsulta` (`IMP006.php:129`), `carregarItensConsulta` (`IMP006.php:176`), `carregarCatalogoConsulta` (`IMP006.php:203`), `gravarCabecalho` (`IMP006.php:236`), `processarItensLote` (`IMP006.php:258`), `gravarItensLote` (`IMP006.php:351`), `finalizarImportacao` (`IMP006.php:387`), `limparImportacao` (`IMP006.php:416`)
- **Tabelas gravadas pelos métodos chamados:** `C004` (IMP003::gerarNotaFiscal), `D001` (IMP003::imp003AtualizarDescricaoProdutos, IMP003::imp003AtualizarPesoProdutos, IMP003::imp007Finalizar), `D001A` (IMP003::imp007Finalizar), `D009` (IMP003::atualizaOC, IMP003::imp003AtualizarCustoME), `D144` (IMP003::imp007Finalizar), `T001` (IMP003::imp003AtualizarCusto, IMP003::imp007Finalizar), `T002` (IMP003::excluirDI), `T006` (IMP003::imp007Finalizar), `T007` (IMP003::gerarNotaFiscal), `T007A` (IMP003::gerarNotaFiscal), `T008` (IMP003::gerarNotaFiscal), `T008A` (IMP003::gerarNotaFiscal), `T015` (IMP003::excluir, IMP003::excluirDI), `T055` (IMP003::associarItemXML, IMP003::associarProduto, IMP003::excluirDI…), `T055A` (IMP003::associarProduto, IMP003::excluirDI) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Classificacao_Fiscal` (`prog/imp/imp003/form_func-ajax.php:27`), `T002_Baixar_Titulo` (`prog/imp/imp003/form.php:2308`), `T002_Gravar_Totalizacao` (`prog/imp/imp003/form.php:2307`), `T005_Status_Pedido` (`prog/imp/imp003/grid.php:678`), `T015_Baixar_Titulo` (`prog/imp/imp003/form.php:2125`), `T015_Gravar_Totalizacao` (`prog/imp/imp003/form.php:2124`)
- **Chaves C031 lidas (14):** `DIUsarBotaoEntradaEstoque`, `MostrarDocDispesaNaDI`, `SeparaProdutoPorEmpresa`, `duimpAmbiente`, `duimpClientIdHomologacao`, `duimpClientIdProducao`, `duimpClientSecretHomologacao`, `duimpClientSecretProducao`, `duimpPerfil`, `habilitaBotaoAtualizarCadastroProdutoViaDI`, `modeloCalculosDI`, `permitirProdutosDuplicadosImportacao` …
- **Mensagens e validações visíveis (19):**
  - "Você tem certeza?" (`prog/imp/imp003/content.php:327`)
  - "Deseja excluir os itens marcados?" (`prog/imp/imp003/content.php:424`)
  - "Deseja atualizar o peso  liquído  e  bruto  no cadastro dos produtos com os valores preenchidos na DI?   Ação não terá como ser revertida" (`prog/imp/imp003/content.php:473`)
  - "Deseja atualizar a  descrição em inglês  no cadastro dos produtos com os valores preenchidos na DI?   Ação não terá como ser revertida" (`prog/imp/imp003/content.php:519`)
  - "Deseja atualizar o  custo ME  no cadastro dos produtos com os valores preenchidos na DI?" (`prog/imp/imp003/content.php:541`)
  - "Registro Alterado com Sucesso!" (`prog/imp/imp003/form.php:193`)
  - "Registro Incluido com Sucesso!" (`prog/imp/imp003/form.php:210`)
  - "Já existe o item com o código ".$T055_Codigo_Produto." nesta DI. Por favor, selecione outro produto." (`prog/imp/imp003/form.php:1025`)
  - … mais 11
- **Abre as telas:** `ven/ven003`, `est/est008`, `cad/cad002`, `est/est016`, `ven/ven002`, `fin/fin002`, `etc/etc007`, `srm/srm001`, `est/est019`, `imp/imp004` …
- **Grafo (Graphify):** 60 nós, 50 funções definidas: `imp003ExecutarConsultaDuimp()`, `atualizarCabecalho()`, `atualizarEtapa()`, `atualizarProgressoGeral()`, `erroProcesso()`, `finalizar()` …
- **Aberta por:** `imp/imp005`, `ven/ven001`

### `prog/imp/imp004` — Quotation (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 10 arquivos. 7.055 linhas. `form.php` (2369), `grid.php` (1806), `outro.php` (1119), `content.php` (543), `form_func-ajax.php` (303), `tab.php` (270), `grid_func-callback.php` (269), `grid_func-ajax.php` (196), `form_func-js.php` (104), `grid_func-js.php` (76)
- **Abas (tab.php):** Quotation (`tab.php:18`), Produtos (`tab.php:19`), Produtos (`tab.php:52`), Quotation (`tab.php:53`), Adicoes (`tab.php:54`), Despesas (`tab.php:57`), Quotation (`tab.php:66`), Geral (`tab.php:87`), Geral (`tab.php:108`), Geral (`tab.php:129`), Pedidos (`tab.php:147`), Cadastro (`tab.php:170`) …
- **Ações (`case` do roteador, 78):** `imp004ContentDi`, `imp004ContentDiIncluir`, `imp004ContentAtualizarLocacao`, `imp004ContentProdutos`, `imp004ContentDocumentos`, `imp004ContentDocumentosDespesa`, `imp004ContentEmail`, `imp004ContentDespesasIncluir`, `imp004ContentLIIncluir`, `imp004contentIncluirContasPagar`, `imp004contentIncluirContasReceber`, `imp004ContentPedidosProduto`, `imp004contentImportarItensOC`, `imp004contentImportarCSV`, `imp004contentGerarPO`, `imp004ContentCotacoesGrupo`, `imp004FormDi`, `imp004FormTotal`, `imp004FormProdutoIncluir`, `imp004FormAtualizarLocacao` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T002` (fotografia:14415): form em `prog/imp/imp004/form.php:1786`
  - `T155` (fotografia:22913): form em `prog/imp/imp004/form.php:1198`, `prog/imp/imp004/form.php:1363`
  - `T270` (fotografia:27046): form em `prog/imp/imp004/form.php:2117`
  - `T293` (fotografia:27706): UPDATE/form em `prog/imp/imp004/form.php:39`, `prog/imp/imp004/form.php:239` e mais 3
  - `T294` (fotografia:27819): DELETE/UPDATE/form em `prog/imp/imp004/form.php:848`, `prog/imp/imp004/form.php:1068` e mais 7
  - `T299` (fotografia:28294): UPDATE/form em `prog/imp/imp004/form.php:1527`, `prog/imp/imp004/form.php:1703`
- **Tabelas só lidas na tela (47):** `D009` (14×, `prog/imp/imp004/form.php:863` e mais 13), `D024` (14×, `prog/imp/imp004/grid.php:67` e mais 13), `T006` (10×, `prog/imp/imp004/form.php:586` e mais 9), `D049` (9×, `prog/imp/imp004/form.php:862` e mais 8), `C007` (8×, `prog/imp/imp004/grid.php:76` e mais 7), `D020` (7×, `prog/imp/imp004/form.php:65` e mais 6), `D018` (7×, `prog/imp/imp004/form.php:65` e mais 6), `T005` (7×, `prog/imp/imp004/form.php:587` e mais 6), `D001` (7×, `prog/imp/imp004/form_func-ajax.php:27` e mais 6), `D005` (6×, `prog/imp/imp004/form_func-ajax.php:33` e mais 5), `D082` (5×, `prog/imp/imp004/form.php:864` e mais 4), `D037` (5×, `prog/imp/imp004/form.php:865` e mais 4), `D004` (5×, `prog/imp/imp004/form_func-ajax.php:152` e mais 4), `T066` (4×, `prog/imp/imp004/form_func-ajax.php:151` e mais 3), `D146` (3×, `prog/imp/imp004/form_func-ajax.php:153` e mais 2), `D191` (3×, `prog/imp/imp004/grid.php:44` e mais 2), `D030` (3×, `prog/imp/imp004/grid.php:68` e mais 2), `D119` (3×, `prog/imp/imp004/grid.php:70` e mais 2), `D118` (3×, `prog/imp/imp004/grid.php:71` e mais 2), `D044` (3×, `prog/imp/imp004/grid.php:72` e mais 2), `C004` (2×, `prog/imp/imp004/form.php:35` e mais 1), `D154` (2×, `prog/imp/imp004/form.php:1804` e mais 1), `T295` (2×, `prog/imp/imp004/grid.php:75` e mais 1), `T225` (2×, `prog/imp/imp004/grid.php:506` e mais 1), `D122` (2×, `prog/imp/imp004/grid.php:963` e mais 1) …
- **Classes e métodos chamados:** `EST019` (1734 linhas): `insereItemEstoque` (`EST019.php:1454`), `insereItemCotacaoEmergencia` (`EST019.php:59`), `insereItemCotacaoPedidoEmergencia` (`EST019.php:481`); `IMP003` (3710 linhas): `retornaAliquotas` (`IMP003.php:2331`); `IMP004` (2737 linhas): `atualizaOC` (`IMP004.php:131`), `incluirPacoteDespesaQuotation` (`IMP004.php:2305`), `importarCSV` (`IMP004.php:1487`), `gerarNotaFiscal` (`IMP004.php:149`), `calcularDespesas` (`IMP004.php:2414`), `calculaCustoProduto` (`IMP004.php:2501`), `Calcular` (`IMP004.php:113`), `GerarXMLLI` (`IMP004.php:1166`), `geraDownloadXMLLI` (`IMP004.php:1463`), `imp007Finalizar` (`IMP004.php:853`) …
- **Tabelas gravadas pelos métodos chamados:** `D009` (EST019::insereItemEstoque, IMP004::atualizaOC), `D144` (IMP004::imp007Finalizar), `T001` (IMP004::imp007Finalizar), `T004` (EST019::insereItemCotacaoEmergencia), `T006` (IMP004::imp007Finalizar), `T007` (IMP004::gerarNotaFiscal), `T008` (IMP004::gerarNotaFiscal), `T161` (IMP004::insereRegistrosT161, IMP004::updateT161_Flag_Ok), `T225` (IMP004::importarItensOC, IMP004::preencherQuantidadeItensOC), `T293` (IMP004::calculaCustoProduto, IMP004::imp007Finalizar), `T294` (EST019::insereItemCotacaoEmergencia, EST019::insereItemCotacaoPedidoEmergencia, EST019::insereItemEstoque…), `T295` (IMP004::gerarPO), `T296` (IMP004::gerarPO), `T299` (IMP004::calcularDespesas, IMP004::incluirPacoteDespesaQuotation, IMP004::processarOrdemDespesa), `T300` (IMP004::gerarPO)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Classificacao_Fiscal` (`prog/imp/imp004/form_func-ajax.php:33`), `T002_Baixar_Titulo` (`prog/imp/imp004/form.php:1902`), `T002_Gravar_Totalizacao` (`prog/imp/imp004/form.php:1901`), `T005_Status_Pedido` (`prog/imp/imp004/grid.php:618`)
- **Chaves C031 lidas (3):** `MostrarDocDispesaNaDI`, `permitirProdutosDuplicadosImportacao`, `separarClientePorEmpresa`
- **Mensagens e validações visíveis (10):**
  - "Você tem certeza?" (`prog/imp/imp004/content.php:141`)
  - "Deseja excluir os itens marcados?" (`prog/imp/imp004/content.php:241`)
  - "Erro ao incluir produtos na cotation: {$retorno}" (`prog/imp/imp004/form.php:328`)
  - "Registro Incluido com Sucesso!" (`prog/imp/imp004/form.php:449`)
  - "Registro Alterado com Sucesso!" (`prog/imp/imp004/form.php:746`)
  - "Já existe o item com o código ".$T294_Codigo_Produto." nesta DI. Por favor, selecione outro produto." (`prog/imp/imp004/form.php:1004`)
  - "Você deverá selecionar um arquivo!" (`prog/imp/imp004/form.php:1247`)
  - "' . $retorno . '" (`prog/imp/imp004/form.php:2327`)
  - … mais 2
- **Abre as telas:** `cad/cad002`, `imp/imp005`, `etc/etc007`, `srm/srm001`
- **Grafo (Graphify):** 36 nós, 26 funções definidas: `campoProcuraLocacao()`, `campoProcuraProduto()`, `campoProcuraServico()`, `imp004CheckboxChecklist()`, `colunaEditarCallbackJS_T294_Quantidade_PO()`, `colunaEditarCallbackJS_T299_Ordem()` …
- **Aberta por:** `est/est019`, `imp/imp003`

### `prog/imp/imp005` — Análise PO

- **Menu:** Comex › Análise PO, acesso C029 nº 255 "Análise PO" (`bibliotecas/menu.php:97`)
- **Arquivos:** 10 arquivos. 7.561 linhas. `form.php` (2259), `grid.php` (1981), `outro.php` (1277), `content.php` (642), `form_func-ajax.php` (359), `tab.php` (310), `grid_func-callback.php` (278), `grid_func-ajax.php` (255), `form_func-js.php` (104), `grid_func-js.php` (96)
- **Abas (tab.php):** Purchase Order (`tab.php:18`), Produtos (`tab.php:19`), Produtos (`tab.php:52`), Purchase Order (`tab.php:53`), Adicoes (`tab.php:54`), Despesas (`tab.php:57`), Purchase Order (`tab.php:66`), Geral (`tab.php:87`), Geral (`tab.php:108`), Geral (`tab.php:129`), Pedidos (`tab.php:147`), Cadastro (`tab.php:170`) …
- **Ações (`case` do roteador, 89):** `imp005ContentDi`, `imp005ContentDiIncluir`, `imp005ContentAtualizarLocacao`, `imp005ContentProdutos`, `imp005ContentDocumentos`, `imp005ContentDocumentosDespesa`, `imp005ContentEmail`, `imp005ContentDespesasIncluir`, `imp005ContentLIIncluir`, `imp005contentIncluirContasPagar`, `imp005contentIncluirContasReceber`, `imp005ContentPedidosProduto`, `imp005contentImportarItensOC`, `imp005contentImportarCSV`, `imp005contentGerarDI`, `imp005ContentOrdemCompraGrupo`, `imp005ContentNaoLiberarTelaAnalise`, `imp005ContentPrincipalAnalise`, `imp005FormDi`, `imp005FormTotal` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T002` (fotografia:14415): form em `prog/imp/imp005/form.php:1559`
  - `T155` (fotografia:22913): form em `prog/imp/imp005/form.php:984`, `prog/imp/imp005/form.php:1149`
  - `T270` (fotografia:27046): form em `prog/imp/imp005/form.php:1891`
  - `T295` (fotografia:27962): UPDATE/form em `prog/imp/imp005/form.php:32`, `prog/imp/imp005/form.php:235` e mais 3
  - `T296` (fotografia:28086): DELETE/UPDATE/form em `prog/imp/imp005/form.php:638`, `prog/imp/imp005/form.php:854` e mais 7
  - `T300` (fotografia:28342): form em `prog/imp/imp005/form.php:1313`
- **Tabelas só lidas na tela (45):** `D024` (15×, `prog/imp/imp005/grid.php:73` e mais 14), `D009` (14×, `prog/imp/imp005/form.php:653` e mais 13), `C007` (13×, `prog/imp/imp005/grid.php:82` e mais 12), `T006` (10×, `prog/imp/imp005/form.php:375` e mais 9), `D049` (9×, `prog/imp/imp005/form.php:652` e mais 8), `D018` (8×, `prog/imp/imp005/form.php:60` e mais 7), `D020` (7×, `prog/imp/imp005/form.php:60` e mais 6), `T005` (7×, `prog/imp/imp005/form.php:376` e mais 6), `D001` (7×, `prog/imp/imp005/form_func-ajax.php:27` e mais 6), `D005` (6×, `prog/imp/imp005/form_func-ajax.php:33` e mais 5), `D082` (5×, `prog/imp/imp005/form.php:654` e mais 4), `D037` (5×, `prog/imp/imp005/form.php:655` e mais 4), `D004` (5×, `prog/imp/imp005/form_func-ajax.php:151` e mais 4), `T066` (4×, `prog/imp/imp005/form_func-ajax.php:150` e mais 3), `T075` (4×, `prog/imp/imp005/grid.php:81` e mais 3), `D146` (3×, `prog/imp/imp005/form_func-ajax.php:152` e mais 2), `D191` (3×, `prog/imp/imp005/grid.php:44` e mais 2), `D030` (3×, `prog/imp/imp005/grid.php:74` e mais 2), `D119` (3×, `prog/imp/imp005/grid.php:76` e mais 2), `D118` (3×, `prog/imp/imp005/grid.php:77` e mais 2), `D044` (3×, `prog/imp/imp005/grid.php:78` e mais 2), `T055` (3×, `prog/imp/imp005/grid.php:80` e mais 2), `C004` (2×, `prog/imp/imp005/form.php:233` e mais 1), `D154` (2×, `prog/imp/imp005/form.php:1577` e mais 1), `T225` (2×, `prog/imp/imp005/grid.php:530` e mais 1) …
- **Classes e métodos chamados:** `IMP003` (3710 linhas): `retornaAliquotas` (`IMP003.php:2331`); `IMP004` (2737 linhas): `alteraStatusQuotation` (`IMP004.php:2698`); `IMP005` (3484 linhas): `atualizaOC` (`IMP005.php:166`), `incluirPacoteDespesaPO` (`IMP005.php:2779`), `importarCSV` (`IMP005.php:1528`), `imp005NaoLiberarPedido` (`IMP005.php:2686`), `gerarNotaFiscal` (`IMP005.php:190`), `calcularDespesas` (`IMP005.php:2888`), `calculaCustoProduto` (`IMP005.php:2976`), `Calcular` (`IMP005.php:148`), `GerarXMLLI` (`IMP005.php:1207`), `geraDownloadXMLLI` (`IMP005.php:1504`) …
- **Tabelas gravadas pelos métodos chamados:** `D009` (IMP005::atualizaOC), `D144` (IMP005::imp007Finalizar), `T001` (IMP005::imp007Finalizar), `T006` (IMP005::imp007Finalizar), `T007` (IMP005::gerarNotaFiscal), `T008` (IMP005::gerarNotaFiscal), `T055` (IMP005::adicionarProdutosDI, IMP005::gerarDI), `T055A` (IMP005::adicionarProdutosDI, IMP005::gerarDI), `T075` (IMP005::gerarDI), `T161` (IMP005::insereRegistrosT161, IMP005::updateT161_Flag_Ok), `T168` (IMP005::gerarDI), `T225` (IMP005::importarItensOC, IMP005::preencherQuantidadeItensOC), `T293` (IMP004::alteraStatusQuotation), `T295` (IMP005::aplicarAlcadaPO, IMP005::calculaCustoProduto, IMP005::cancelarPO…), `T296` (IMP005::calculaCustoProduto, IMP005::importarCSV, IMP005::importarItensOC…) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Classificacao_Fiscal` (`prog/imp/imp005/form_func-ajax.php:33`), `T002_Baixar_Titulo` (`prog/imp/imp005/form.php:1675`), `T002_Gravar_Totalizacao` (`prog/imp/imp005/form.php:1674`), `T005_Status_Pedido` (`prog/imp/imp005/grid.php:643`)
- **Chaves C031 lidas (3):** `MostrarDocDispesaNaDI`, `permitirProdutosDuplicadosImportacao`, `separarClientePorEmpresa`
- **Mensagens e validações visíveis (10):**
  - "Você tem certeza?" (`prog/imp/imp005/content.php:208`)
  - "Deseja excluir os itens marcados?" (`prog/imp/imp005/content.php:308`)
  - "Registro Incluido com Sucesso!" (`prog/imp/imp005/form.php:240`)
  - "Registro Alterado com Sucesso!" (`prog/imp/imp005/form.php:535`)
  - "Já existe o item com o código ".$T296_Codigo_Produto." nesta DI. Por favor, selecione outro produto." (`prog/imp/imp005/form.php:790`)
  - "Você deverá selecionar um arquivo!" (`prog/imp/imp005/form.php:1033`)
  - "' . $retorno . '" (`prog/imp/imp005/form.php:2100`)
  - "CSV Importado com Sucesso!" (`prog/imp/imp005/form.php:2103`)
  - … mais 2
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::purchaseOrderAnalise` (`NOTIFICACOES.php:2607`, clique na linha 2626; lê `T295`)
- **Abre as telas:** `cad/cad002`, `imp/imp003`, `etc/etc007`, `srm/srm001`
- **Grafo (Graphify):** 37 nós, 27 funções definidas: `campoProcuraLocacao()`, `campoProcuraProduto()`, `campoProcuraServico()`, `imp005CheckboxChecklist()`, `colunaEditarCallbackJS_T296_Quantidade_DI()`, `colunaEditarCallbackJS_T300_Ordem()` …
- **Aberta por:** `est/est019`, `imp/imp003`, `imp/imp004`, `ven/ven001`

