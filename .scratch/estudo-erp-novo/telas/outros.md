# Fichas de tela: `prog/outros` — Outros (integrações e utilitários)

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `outros/(soltos)` |  |  | 2 | 98 | 1 | 3 |
| `outros/apacheErros` |  |  | 1 | 268 | 0 | 0 |
| `outros/atualizarContatosE001` |  |  | 1 | 172 | 0 | 0 |
| `outros/buscarArquivos` |  |  | 1 | 23 | 0 | 0 |
| `outros/contratosClientes` | — | — | 1 | 1234 | não detalhado | — |
| `outros/dadosSistema` | Disco (rótulo da primeira aba) |  | 3 | 191 | 0 | 0 |
| `outros/dadosXML` |  |  | 1 | 181 | 0 | 2 |
| `outros/impressao` |  |  | 1 | 42 | 0 | 0 |
| `outros/integracaoPDV` |  |  | 1 | 1639 | 8 | 21 |
| `outros/integracaoSMT` | — | — | 18 | 1906 | não detalhado | — |
| `outros/layoutExportacao` | Exportação EBS | 193 | 7 | 1597 | 3 | 1 |
| `outros/pedidoMercadoLivre` | — | — | 2 | 1164 | não detalhado | — |
| `outros/processosBancoDados` |  |  | 1 | 66 | 0 | 0 |
| `outros/registroSuporte` | — | — | 4 | 12134 | não detalhado | — |
| `outros/registroSuporteCliente` | — | — | 4 | 12143 | não detalhado | — |
| `outros/testes` | — | — | 1 | 58 | não detalhado | — |

Total: 16 telas. 32.916 linhas. 27 tabelas gravadas. 67 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/outros/(soltos)`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 2 arquivos. 98 linhas. `distribuicaoItemPedidoEstoque.php` (77), `teste.php` (21)
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T185` (fotografia:23685): DELETE/INSERT em `prog/outros/distribuicaoItemPedidoEstoque.php:25`, `prog/outros/distribuicaoItemPedidoEstoque.php:63`
- **Tabelas só lidas na tela (3):** `T006` (2×, `prog/outros/distribuicaoItemPedidoEstoque.php:19` e mais 1), `T066` (1×, `prog/outros/distribuicaoItemPedidoEstoque.php:27`), `D004` (1×, `prog/outros/distribuicaoItemPedidoEstoque.php:27`)
- **Grafo (Graphify):** 3 nós, 1 funções definidas: `soletras()`

### `prog/outros/apacheErros`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 268 linhas. `apacheErros.php` (268)
- **Ações (`case` do roteador, 5):** `content`, `tab`, `outros`, `standalone`, `log`
- **Grafo (Graphify):** 4 nós, 3 funções definidas: `load()`, `render()`, `setStatus()`

### `prog/outros/atualizarContatosE001`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 172 linhas. `scriptConexaoBases.php` (172)
- **Ações (`case` do roteador, 1):** `scriptConexaoBases`
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/outros/buscarArquivos`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 23 linhas. `imagensProdutos.php` (23)
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/outros/contratosClientes`

- Arquivos: 1, 1234 linhas. Não detalhado: integração, suporte ou teste do fornecedor; pode conter arquivo de outro cliente da Publicação.

### `prog/outros/dadosSistema` — Disco (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 3 arquivos. 191 linhas. `outro.php` (134), `tab.php` (33), `content.php` (24)
- **Abas (tab.php):** Disco (`tab.php:16`), Dados Empresas (`tab.php:17`), Empresa Atual (`tab.php:18`), Usuários Empresa (`tab.php:19`), Banco Dados (`tab.php:20`), Banco Dados Atual (`tab.php:21`)
- **Ações (`case` do roteador, 8):** `dadosSistema`, `dadosDisco`, `dadosEmpresasServidor`, `dadosUsuarios`, `dadosEmpresaAtual`, `dadosBancoDados`, `dadosBancoDadosEmpresaAtual`, `dadosSistemaTab`
- **Grafo (Graphify):** 3 nós, 0 funções definidas

### `prog/outros/dadosXML`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 181 linhas. `notaFiscal.php` (181)
- **Ações (`case` do roteador, 2):** `content`, `form`
- **Tabelas só lidas na tela (2):** `T007` (1×, `prog/outros/dadosXML/notaFiscal.php:122`), `D024` (1×, `prog/outros/dadosXML/notaFiscal.php:122`)
- **Grafo (Graphify):** 2 nós, 1 funções definidas: `processarDadosXML()`

### `prog/outros/impressao`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 42 linhas. `impressao.php` (42)
- **Ações (`case` do roteador, 3):** `imprimir`, `retornaArquivos`, `limparArquivosServidor`
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/outros/integracaoPDV`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 1.639 linhas. `integracaoSG.php` (1639)
- **Ações (`case` do roteador, 12):** `contentExportar`, `formExportar`, `exportarProdutos`, `exportarClientes`, `exportarOperadores`, `S`, `D`, `exportarPedidos`, `importarCupomFiscal`, `exportarVendedores`, `V`, `S`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T001` (fotografia:14264): DELETE/INSERT em `prog/outros/integracaoPDV/integracaoSG.php:690`, `prog/outros/integracaoPDV/integracaoSG.php:942`
  - `T002` (fotografia:14415): DELETE/INSERT/UPDATE em `prog/outros/integracaoPDV/integracaoSG.php:689`, `prog/outros/integracaoPDV/integracaoSG.php:1322` e mais 3
  - `T006` (fotografia:15620): UPDATE em `prog/outros/integracaoPDV/integracaoSG.php:693`, `prog/outros/integracaoPDV/integracaoSG.php:828`
  - `T007` (fotografia:16254): INSERT/UPDATE em `prog/outros/integracaoPDV/integracaoSG.php:692`, `prog/outros/integracaoPDV/integracaoSG.php:719` e mais 1
  - `T008` (fotografia:16670): INSERT/UPDATE em `prog/outros/integracaoPDV/integracaoSG.php:865`, `prog/outros/integracaoPDV/integracaoSG.php:1038`
  - `T019` (fotografia:17992): DELETE/INSERT/UPDATE em `prog/outros/integracaoPDV/integracaoSG.php:691`, `prog/outros/integracaoPDV/integracaoSG.php:1387` e mais 2
  - `T020` (fotografia:18068): INSERT em `prog/outros/integracaoPDV/integracaoSG.php:1444`
  - `T245` (fotografia:26312): UPDATE em `prog/outros/integracaoPDV/integracaoSG.php:1535`
- **Tabelas só lidas na tela (21):** `D006` (5×, `prog/outros/integracaoPDV/integracaoSG.php:85` e mais 4), `D001` (5×, `prog/outros/integracaoPDV/integracaoSG.php:117` e mais 4), `D049` (5×, `prog/outros/integracaoPDV/integracaoSG.php:118` e mais 4), `D009` (5×, `prog/outros/integracaoPDV/integracaoSG.php:119` e mais 4), `D037` (4×, `prog/outros/integracaoPDV/integracaoSG.php:122` e mais 3), `D020` (3×, `prog/outros/integracaoPDV/integracaoSG.php:301` e mais 2), `D024` (3×, `prog/outros/integracaoPDV/integracaoSG.php:304` e mais 2), `C007` (3×, `prog/outros/integracaoPDV/integracaoSG.php:370` e mais 2), `D053` (2×, `prog/outros/integracaoPDV/integracaoSG.php:77` e mais 1), `D005` (2×, `prog/outros/integracaoPDV/integracaoSG.php:120` e mais 1), `D149` (2×, `prog/outros/integracaoPDV/integracaoSG.php:121` e mais 1), `T066` (2×, `prog/outros/integracaoPDV/integracaoSG.php:921` e mais 1), `D004` (2×, `prog/outros/integracaoPDV/integracaoSG.php:922` e mais 1), `D027` (2×, `prog/outros/integracaoPDV/integracaoSG.php:1306` e mais 1), `D083` (1×, `prog/outros/integracaoPDV/integracaoSG.php:221`), `C004` (1×, `prog/outros/integracaoPDV/integracaoSG.php:301`), `T005` (1×, `prog/outros/integracaoPDV/integracaoSG.php:471`), `C020` (1×, `prog/outros/integracaoPDV/integracaoSG.php:676`), `T134` (1×, `prog/outros/integracaoPDV/integracaoSG.php:1024`), `D014` (1×, `prog/outros/integracaoPDV/integracaoSG.php:1295`), `D007` (1×, `prog/outros/integracaoPDV/integracaoSG.php:1297`)
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `D001_reprocessa_historico` (`CAD002.php:1159`); `VEN003` (19831 linhas) (só incluída)
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::D001_reprocessa_historico), `D009A` (CAD002::D001_reprocessa_historico), `T001` (CAD002::D001_reprocessa_historico), `T066` (CAD002::D001_reprocessa_historico)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T002_Gravar_Totalizacao` (`prog/outros/integracaoPDV/integracaoSG.php:1426`), `T007_Gravar_Totalizacao_4` (`prog/outros/integracaoPDV/integracaoSG.php:993`), `T007_IPV_Medio` (`prog/outros/integracaoPDV/integracaoSG.php:1182`), `T008_Gravar_Totalizacao_4` (`prog/outros/integracaoPDV/integracaoSG.php:916`)
- **Chaves C031 lidas (8):** `alcadaVendaSupervisor_descontoMinimoSupervisor`, `cfopCupomFiscalComST`, `cfopCupomFiscalSemST`, `somarCOFINSPrecoVenda`, `somarCSLLPrecoVenda`, `somarICMSPrecoVenda`, `somarIRPJPrecoVenda`, `somarPISPrecoVenda`
- **Grafo (Graphify):** 3 nós, 2 funções definidas: `corrigeData()`, `prepararCampo()`. Chama fora da tela: `number_format()` (formulario.php)

### `prog/outros/integracaoSMT`

- Arquivos: 18, 1906 linhas. Não detalhado: integração, suporte ou teste do fornecedor; pode conter arquivo de outro cliente da Publicação.

### `prog/outros/layoutExportacao` — Exportação EBS

- **Menu:** Fiscal › Exportação EBS, acesso C029 nº 193 "Exportação EBS" (`bibliotecas/menu.php:92`)
- **Arquivos:** 7 arquivos. 1.597 linhas. `form.php` (659), `grid.php` (295), `outro.php` (221), `content.php` (174), `tab.php` (151), `grid_func-callback.php` (60), `grid_func-ajax.php` (37)
- **Abas (tab.php):** Layouts (`tab.php:11`), Sub-Layouts (`tab.php:27`), Select (`tab.php:43`), Itens (`tab.php:59`), Upload (`tab.php:75`), Editar (`tab.php:91`), Callbacks (`tab.php:107`), Callback (`tab.php:123`), Exportação de Dados (`tab.php:139`)
- **Ações (`case` do roteador, 32):** `layout`, `sublayout`, `editarSelect`, `itens`, `uploadCSV`, `campoSelect`, `campoCallback`, `adicionarCallback`, `uiUsuario`, `editarSelect`, `uploadCSV`, `campoSelect`, `adicionarCallback`, `uiUsuario`, `layout`, `sublayout`, `itens`, `callbacks`, `callback`, `exportacaoEBSalteraFlagData` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D151` (fotografia:8263): UPDATE/form em `prog/outros/layoutExportacao/form.php:30`, `prog/outros/layoutExportacao/grid_func-ajax.php:31`
  - `D152` (fotografia:8289): DELETE/INSERT/UPDATE/form em `prog/outros/layoutExportacao/form.php:196`, `prog/outros/layoutExportacao/form.php:207` e mais 7
  - `D153` (fotografia:8323): form em `prog/outros/layoutExportacao/form.php:438`
- **Tabelas só lidas na tela (1):** `D150` (3×, `prog/outros/layoutExportacao/grid.php:30` e mais 2)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/outros/layoutExportacao/form.php:69`)
  - "Registro Incluido com Sucesso!" (`prog/outros/layoutExportacao/form.php:89`)
  - "CSV importado com sucesso!" (`prog/outros/layoutExportacao/form.php:239`)
- **Grafo (Graphify):** 19 nós, 12 funções definidas: `callbackOpcoes()`, `checkboxUtilizaDatas()`, `docLink()`, `subLayoutComentario()`, `subLayoutFormato()`, `subLayoutOpcoes()` …

### `prog/outros/pedidoMercadoLivre`

- Arquivos: 2, 1164 linhas. Não detalhado: integração, suporte ou teste do fornecedor; pode conter arquivo de outro cliente da Publicação.

### `prog/outros/processosBancoDados`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 66 linhas. `processosBancoDados.php` (66)
- **Ações (`case` do roteador, 3):** `content`, `tab`, `outros`
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/outros/registroSuporte`

- Arquivos: 4, 12134 linhas. Não detalhado: integração, suporte ou teste do fornecedor; pode conter arquivo de outro cliente da Publicação.

### `prog/outros/registroSuporteCliente`

- Arquivos: 4, 12143 linhas. Não detalhado: integração, suporte ou teste do fornecedor; pode conter arquivo de outro cliente da Publicação.

### `prog/outros/testes`

- Arquivos: 1, 58 linhas. Não detalhado: integração, suporte ou teste do fornecedor; pode conter arquivo de outro cliente da Publicação.

