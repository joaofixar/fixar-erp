# Fichas de tela: `prog/sistema` — Núcleo de telas (funções do sistema, sino, chat, personalização low-code)

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `sistema/ajax` |  |  | 7 | 360 | 2 | 5 |
| `sistema/builder` |  |  | 4 | 418 | 0 | 0 |
| `sistema/calculadoraQuantidade` | Quantidade (rótulo da primeira aba) |  | 3 | 153 | 0 | 0 |
| `sistema/etiquetas` | Modelos de Etiqueta (rótulo da primeira aba) |  | 5 | 574 | 1 | 0 |
| `sistema/funcoes` |  |  | 43 | 11671 | 15 | 38 |
| `sistema/importacaoCSV` | Tabelas (rótulo da primeira aba) |  | 5 | 1411 | 0 | 0 |
| `sistema/impressao` |  |  | 5 | 3196 | 1 | 22 |
| `sistema/info` |  |  | 2 | 65 | 1 | 0 |
| `sistema/logSistema` |  |  | 1 | 201 | 0 | 2 |
| `sistema/outros` | Retornos pendentes (rótulo da primeira aba) |  | 8 | 602 | 1 | 2 |
| `sistema/personalizacaoConf` | Configurações (rótulo da primeira aba) |  | 4 | 383 | 0 | 1 |
| `sistema/personalizacaoContent` |  |  | 3 | 36 | 0 | 0 |
| `sistema/personalizacaoForm` | Preencher Valor (rótulo da primeira aba) |  | 4 | 269 | 0 | 0 |
| `sistema/personalizacaoGrid` | HTML - Colunas (rótulo da primeira aba) |  | 5 | 938 | 1 | 0 |
| `sistema/personalizacaoLowCode` | Alterar Parâmetros do Callback (rótulo da primeira aba) |  | 12 | 3283 | 2 | 0 |
| `sistema/personalizacaoTab` |  |  | 4 | 46 | 0 | 0 |
| `sistema/progs` |  |  | 5 | 1404 | 15 | 6 |
| `sistema/scripts` |  |  | 7 | 141 | 2 | 4 |
| `sistema/tema` | Add. Tema (rótulo da primeira aba) |  | 3 | 162 | 1 | 0 |
| `sistema/webservice` |  |  | 5 | 139 | 0 | 0 |

Total: 20 telas. 25.452 linhas. 32 tabelas gravadas. 74 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/sistema/ajax`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 41 "CFOP" (`prog/sistema/ajax/campoInfo.php:31`)
- **Arquivos:** 7 arquivos. 360 linhas. `processarCarteiraClientes.php` (206), `importarTray-bkp.php` (51), `campoInfo.php` (34), `modoDesenvolvedorApp.php` (22), `verificaRoot.php` (18), `modoDesenvolvedor.php` (17), `processarPedidosVencidos.php` (12)
- **Ações (`case` do roteador, 5):** `dadosInfo`, `carteiraClienteDiasMaxContato`, `carteiraClienteDiasMaxOrcamento`, `carteiraClienteDiasMaxPedido`, `carteiraClienteDiasMaxNotaFiscal`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D024` (fotografia:4506): UPDATE em `prog/sistema/ajax/processarCarteiraClientes.php:176`, `prog/sistema/ajax/processarCarteiraClientes.php:183`
  - `Z001` (fotografia:29216): UPDATE em `prog/sistema/ajax/importarTray-bkp.php:41`
- **Tabelas só lidas na tela (5):** `C031` (1×, `prog/sistema/ajax/processarCarteiraClientes.php:10`), `T007` (1×, `prog/sistema/ajax/processarCarteiraClientes.php:128`), `T005` (1×, `prog/sistema/ajax/processarCarteiraClientes.php:141`), `T003` (1×, `prog/sistema/ajax/processarCarteiraClientes.php:153`), `D085` (1×, `prog/sistema/ajax/processarCarteiraClientes.php:165`)
- **Classes e métodos chamados:** `CAD001` (1497 linhas): `processarCarteiraCliente` (`CAD001.php:1355`); `VEN002` (10343 linhas): `processarVencidos`
- **Tabelas gravadas pelos métodos chamados:** `D024` (CAD001::processarCarteiraCliente)
- **Chaves C031 lidas (2):** `ConsumerKeyTray`, `ConsumerSecretTray`
- **Grafo (Graphify):** 7 nós, 0 funções definidas

### `prog/sistema/builder`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 418 linhas. `form.php` (170), `grid.php` (156), `tab.php` (53), `content.php` (39)
- **Ações (`case` do roteador, 6):** `builder`, `builderForm`, `builder`, `builder`, `builder`, `builderForm`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/sistema/builder/form.php:113`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/sistema/calculadoraQuantidade` — Quantidade (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 3 arquivos. 153 linhas. `form.php` (117), `content.php` (18), `tab.php` (18)
- **Abas (tab.php):** Quantidade (`tab.php:13`)
- **Ações (`case` do roteador, 3):** `calculadoraQuantidade`, `calculadoraQuantidade`, `calculadoraQuantidade`
- **Mensagens e validações visíveis (1):**
  - "Informe pelo menos duas medidas numéricas maiores que zero." (`prog/sistema/calculadoraQuantidade/form.php:106`)
- **Grafo (Graphify):** 8 nós, 5 funções definidas: `calcularResultado()`, `formatar()`, `numero()`, `obterMedidas()`, `recalcular()`

### `prog/sistema/etiquetas` — Modelos de Etiqueta (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 574 linhas. `form.php` (290), `content.php` (130), `tab.php` (90), `form_func-ajax.php` (45), `form_func-js.php` (19)
- **Abas (tab.php):** Modelos de Etiqueta (`tab.php:17`), Quantidades (`tab.php:37`), Impressão de Etiquetas (`tab.php:57`), Selecione o Modelo (`tab.php:77`)
- **Ações (`case` do roteador, 12):** `sistemaEtiquetasSelecao`, `sistemaEtiquetasQtdProduto`, `etiquetasGenericaXX000`, `sistemaSelecionarModelo`, `sistemaEtiquetasQtdProduto`, `etiquetasGenericaXX000`, `formSistemaSelecionarModelo`, `etiquetasGenericaXX000`, `sistemaEtiquetasSelecao`, `sistemaEtiquetasQtdProduto`, `etiquetasGenericaXX000`, `tabSistemaSelecionarModelo`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D138` (fotografia:7895): form em `prog/sistema/etiquetas/form.php:192`
- **Grafo (Graphify):** 6 nós, 1 funções definidas: `etiquetasGenericaXX000()`
- **Aberta por:** `cad/cad020`, `cad/cad051`, `est/est003`

### `prog/sistema/funcoes`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 43 arquivos. 11.671 linhas. `editarColuna.php` (1584), `importacaoDeDados.php` (1350), `queueNF.php` (1105), `inclusaoMultiProdutos.php` (840), `pesquisaGrid.php` (794), `informacoes.php` (543), `svnPersonalizacoes.php` (538), `mensagens.php` (430), `autoriza.php` (379), `gridFiltro.php` (318), `pesquisaCep.php` (264), `login.php` (263) …
- **Ações (`case` do roteador, 179):** `content`, `listaDimensoes`, `mostraGrid`, `grid`, `validaAutorizacao`, `content`, `tab`, `form`, `contentAbrirGaveta`, `tabAbrirGaveta`, `formAbrirGaveta`, `enviarMensagem`, `verificarMensagem`, `novaNotificacaoSuporte`, `verificarAgenda`, `marcarLido`, `removerMensagem`, `contentPesquisaFormulario`, `idAnterior`, `idProximo` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C007` (fotografia:396): UPDATE em `prog/sistema/funcoes/login.php:155`, `prog/sistema/funcoes/mensagens.php:28` e mais 5
  - `C034` (fotografia:2361): INSERT/UPDATE em `prog/sistema/funcoes/gridFiltro.php:198`, `prog/sistema/funcoes/gridFiltro.php:220`
  - `C035` (fotografia:2390): form em `prog/sistema/funcoes/autoriza.php:85`
  - `D009` (fotografia:3608): UPDATE em `prog/sistema/funcoes/inclusaoMultiProdutos.php:309`
  - `D024` (fotografia:4506): UPDATE em `prog/sistema/funcoes/pesquisaCNPJ.php:100`
  - `D083` (fotografia:6752): INSERT/UPDATE em `prog/sistema/funcoes/inclusaoMultiProdutos.php:688`, `prog/sistema/funcoes/inclusaoMultiProdutos.php:691`
  - `T004` (fotografia:14902): INSERT/UPDATE em `prog/sistema/funcoes/inclusaoMultiProdutos.php:537`, `prog/sistema/funcoes/inclusaoMultiProdutos.php:704`
  - `T008` (fotografia:16670): INSERT em `prog/sistema/funcoes/inclusaoMultiProdutos.php:779`
  - `T181` (fotografia:23545): DELETE/INSERT em `prog/sistema/funcoes/integracaoLeitorDigital.php:20`, `prog/sistema/funcoes/integracaoLeitorDigital.php:29` e mais 1
  - `T218` (fotografia:24521): UPDATE em `prog/sistema/funcoes/removerNotificacao.php:104`
  - `T219` (fotografia:24768): UPDATE em `prog/sistema/funcoes/removerNotificacao.php:13`
  - `T233` (fotografia:25951): DELETE em `prog/sistema/funcoes/impWin.php:22`
  - `T237` (fotografia:26108): DELETE/INSERT/UPDATE em `prog/sistema/funcoes/chat.php:19`, `prog/sistema/funcoes/chat.php:236` e mais 2
  - `T245` (fotografia:26312): UPDATE em `prog/sistema/funcoes/removerNotificacao.php:110`
  - `T252` (fotografia:26501): UPDATE em `prog/sistema/funcoes/mensagens.php:360`
- **Tabelas só lidas na tela (38):** `Q002` (20×, `prog/sistema/funcoes/queueNF.php:28` e mais 19), `D001` (11×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:115` e mais 10), `D049` (8×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:118` e mais 7), `D018` (5×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:380` e mais 4), `D020` (5×, `prog/sistema/funcoes/login.php:53` e mais 4), `Q003` (5×, `prog/sistema/funcoes/queueNF.php:105` e mais 4), `C021` (4×, `prog/sistema/funcoes/copiarTabelas.php:60` e mais 3), `T066` (4×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:120` e mais 3), `D082` (4×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:123` e mais 3), `D005` (4×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:127` e mais 3), `D030` (4×, `prog/sistema/funcoes/pesquisaCNPJ.php:241` e mais 3), `T234` (3×, `prog/sistema/funcoes/chat.php:158` e mais 2), `D004` (3×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:121` e mais 2), `T003` (3×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:359` e mais 2), `T001` (3×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:367` e mais 2), `C004` (3×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:382` e mais 2), `D037` (2×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:126` e mais 1), `D069` (2×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:129` e mais 1), `D149` (2×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:130` e mais 1), `D146` (2×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:297` e mais 1), `D041` (2×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:369` e mais 1), `D006` (2×, `prog/sistema/funcoes/inclusaoMultiProdutos.php:499` e mais 1), `C019` (2×, `prog/sistema/funcoes/login.php:112` e mais 1), `T144` (2×, `prog/sistema/funcoes/pesquisaGridCallback.php:32` e mais 1), `T055` (2×, `prog/sistema/funcoes/pesquisaGridCallback.php:54` e mais 1) …
- **Classes e métodos chamados:** `CAD001` (1497 linhas): `triggerAlteracaoConsultaApi` (`CAD001.php:1493`), `pesquisaTerceirosByjg` (`CAD001.php:505`), `consultaCep` (`CAD001.php:561`); `CAD002` (14075 linhas): `retornaPrecoVenda` (`CAD002.php:8729`), `definirCodigoFornecedor` (`CAD002.php:5288`); `FRAMEWORK` (49 linhas): `montaPesquisaGrid` (`FRAMEWORK.php:17`); `NOTIFICACOES` (3346 linhas): `atualizarNotificacoesGeral` (`NOTIFICACOES.php:162`), `atualizarNotificacoesGeralHora` (`NOTIFICACOES.php:238`), `atualizarNotificacoesGeralDia` (`NOTIFICACOES.php:286`), `financeiroAlerta` (`NOTIFICACOES.php:2131`), `historicoSemLocacao` (`NOTIFICACOES.php:2383`), `produtosEstoqueVazio` (`NOTIFICACOES.php:2295`); `VEN001` (15360 linhas): `retornaCFOP` (`VEN001.php:8750`), `retornaAliquotas` (`VEN001.php:10380`), `atualizarAliquotas` (`VEN001.php:3688`), `triggerWherePesquisaGrid` (`VEN001.php:15025`); `VEN002` (10343 linhas): `VEN002MudarEmpresaPedido` (`VEN002.php:1525`); `VEN003` (19831 linhas): `ven003atualizarCFOP` (`VEN003.php:16644`)
- **Tabelas gravadas pelos métodos chamados:** `D083` (CAD002::definirCodigoFornecedor), `T004` (VEN001::atualizarAliquotas), `T004A` (VEN001::atualizarAliquotas), `T005` (VEN002::VEN002MudarEmpresaPedido), `T006` (VEN002::VEN002MudarEmpresaPedido), `T007` (VEN003::ven003atualizarCFOP), `T008` (VEN003::ven003atualizarCFOP), `T008A` (VEN003::ven003atualizarCFOP), `T178` (VEN002::VEN002MudarEmpresaPedido)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T003_Gravar_Totalizacao_4` (`prog/sistema/funcoes/inclusaoMultiProdutos.php:701`), `T004_Gravar_Totalizacao_4` (`prog/sistema/funcoes/inclusaoMultiProdutos.php:700`), `T007_Gravar_Totalizacao_4` (`prog/sistema/funcoes/inclusaoMultiProdutos.php:838`), `T008_Gravar_Totalizacao_4` (`prog/sistema/funcoes/inclusaoMultiProdutos.php:837`)
- **Chaves C031 lidas (9):** `alcadaVendaSupervisor_descontoMaximoSupervisorAVista`, `destaqueTermosPesquisaGrid`, `diasMedioPrazo`, `habilitarInclusaoProdutosPesquisaGrid`, `incluirProdutosPesquisaGridEmEtapas`, `modeloBuscaPesquisaGridProduto`, `precopadraoVendaProduto`, `usarSenhaRootPadrao`, `usarSuporteInterno`
- **Mensagens e validações visíveis (21):**
  - "Copiado!" (`prog/sistema/funcoes/copiarTabelas.php:92`)
  - "Atenção!!!  Esta ação irá sobreescrever a tabela selecionada nesta empresa! Continuar?" (`prog/sistema/funcoes/copiarTabelas.php:98`)
  - "Valor não pode ser vazio!" (`prog/sistema/funcoes/editarColuna.php:445`)
  - "Fórmula não pode ser vazia!" (`prog/sistema/funcoes/editarColuna.php:769`)
  - "Nenhum campo do formulário foi habilitado para edição!" (`prog/sistema/funcoes/editarRegistro.php:85`)
  - "Registro Alterado com Sucesso!" (`prog/sistema/funcoes/editarRegistro.php:100`)
  - "Registro Inserido com Sucesso!" (`prog/sistema/funcoes/editarRegistro.php:109`)
  - "{$log}" (`prog/sistema/funcoes/expImpPersonalizacao.php:132`)
  - … mais 13
- **Abre as telas:** `etc/etc007`, `cad/cad002`, `cad/cad001`, `cad/cad003`
- **Grafo (Graphify):** 89 nós, 46 funções definidas: `acaoImportar()`, `campoColuna()`, `campoColunaLike()`, `campoColunaPK()`, `campoLinkProdutoPesquisaGrid()`, `validaCampoEstoque()` …. Chama fora da tela: `number_format()` (formulario.php), `str_pad()` (outros.php)

### `prog/sistema/importacaoCSV` — Tabelas (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 1.411 linhas. `grid.php` (494), `grid_func-ajax.php` (493), `form.php` (282), `content.php` (78), `tab.php` (64)
- **Abas (tab.php):** Tabelas (`tab.php:13`), Configurações Salvas (`tab.php:28`), Importação CSV (`tab.php:45`)
- **Ações (`case` do roteador, 23):** `sistemaPImpCSVPrincipal`, `sistemaPImpCSVEnviar`, `sistemaPImpCSVRecuperar`, `sistemaPImpCSVEnviarZIP`, `sistemaPImpCSVEnviar`, `sistemaPImpCSVEnviarZIP`, `sistemaPImpCSVTabelas`, `sistemaPImpCSVRecuperar`, `sistemaPImpCSVAcoes`, `sistemaPImpCSVListarColunas`, `campoColuna`, `campoColunaPK`, `campoColunaFKSelect`, `campoColunaFK`, `truncate`, `importar`, `salvar`, `pularLinha`, `alterarOrdem`, `excluir` …
- **Mensagens e validações visíveis (2):**
  - "Arquivo enviado com sucesso!" (`prog/sistema/importacaoCSV/form.php:69`)
  - "Erro abrindo arquivo .ZIP" (`prog/sistema/importacaoCSV/form.php:241`)
- **Grafo (Graphify):** 13 nós, 8 funções definidas: `acaoImportar()`, `campoColuna()`, `campoColunaFK()`, `campoColunaFKSelect()`, `campoColunaPK()`, `{closure#1}()` …

### `prog/sistema/impressao`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 3.196 linhas. `etiquetasbkp.php20190513` (1370), `etiquetasJava.php` (1180), `etiquetasApplet.php` (482), `cupom.php` (109), `etiquetas.php` (55)
- **Ações (`case` do roteador, 12):** `romaneioestoque`, `notafiscal`, `precoloja`, `produto`, `locacao`, `pedido`, `notafiscal`, `precoloja`, `precolojaNovoPreco`, `produto`, `locacao`, `pedido`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D009` (fotografia:3608): UPDATE em `prog/sistema/impressao/etiquetasJava.php:418`
- **Tabelas só lidas na tela (22):** `D001` (20×, `prog/sistema/impressao/cupom.php:54` e mais 19), `D175` (15×, `prog/sistema/impressao/etiquetasJava.php:27` e mais 14), `D049` (11×, `prog/sistema/impressao/cupom.php:52` e mais 10), `D018` (9×, `prog/sistema/impressao/etiquetasApplet.php:35` e mais 8), `D020` (9×, `prog/sistema/impressao/etiquetasApplet.php:36` e mais 8), `D004` (8×, `prog/sistema/impressao/etiquetasApplet.php:200` e mais 7), `D024` (7×, `prog/sistema/impressao/cupom.php:17` e mais 6), `T014` (6×, `prog/sistema/impressao/etiquetasApplet.php:195` e mais 5), `T066` (6×, `prog/sistema/impressao/etiquetasApplet.php:199` e mais 5), `T005` (5×, `prog/sistema/impressao/cupom.php:16` e mais 4), `C007` (5×, `prog/sistema/impressao/etiquetas.php:40` e mais 4), `T006` (4×, `prog/sistema/impressao/cupom.php:50` e mais 3), `T007` (4×, `prog/sistema/impressao/etiquetasApplet.php:33` e mais 3), `D006` (4×, `prog/sistema/impressao/etiquetasApplet.php:37` e mais 3), `D021` (4×, `prog/sistema/impressao/etiquetasApplet.php:39` e mais 3), `D022` (4×, `prog/sistema/impressao/etiquetasApplet.php:40` e mais 3), `C004` (3×, `prog/sistema/impressao/etiquetasApplet.php:456` e mais 2), `C008` (2×, `prog/sistema/impressao/etiquetas.php:20` e mais 1), `D037` (2×, `prog/sistema/impressao/etiquetasApplet.php:458` e mais 1), `D082` (1×, `prog/sistema/impressao/cupom.php:53`), `D036` (1×, `prog/sistema/impressao/etiquetasJava.php:119`), `D148` (1×, `prog/sistema/impressao/etiquetasJava.php:120`)
- **Chaves C031 lidas (3):** `impressaoJava`, `precoEtiquetaGondola`, `quantidadeDigitosEAN`
- **Mensagens e validações visíveis (1):**
  - "Etiqueta enviada para a fila de impresão." (`prog/sistema/impressao/etiquetasJava.php:56`)
- **Grafo (Graphify):** 8 nós, 4 funções definidas: `geraArquivoTXT()`, `quantidadeArquviosServidor()`, `retornoWhere()`, `retornaImpressora()`

### `prog/sistema/info`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 2 arquivos. 65 linhas. `outros.php` (39), `content.php` (26)
- **Ações (`case` do roteador, 2):** `notifcaMelhoria`, `notifcaMelhoria`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C007` (fotografia:396): UPDATE em `prog/sistema/info/outros.php:15`
- **Grafo (Graphify):** 2 nós, 0 funções definidas

### `prog/sistema/logSistema`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 201 linhas. `outro.php` (201)
- **Ações (`case` do roteador, 7):** `contentErros`, `tabErros`, `gridErros`, `log`, `contentGeral`, `tabGeral`, `gridGeral`
- **Tabelas só lidas na tela (2):** `T245` (2×, `prog/sistema/logSistema/outro.php:59` e mais 1), `T265` (1×, `prog/sistema/logSistema/outro.php:162`)
- **Grafo (Graphify):** 2 nós, 1 funções definidas: `gridLinha()`

### `prog/sistema/outros` — Retornos pendentes (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 8 arquivos. 602 linhas. `grid_func-ajax.php` (294), `grid.php` (106), `grid_func-js.php` (60), `sobre.php` (53), `tab.php` (33), `content.php` (30), `grid_func-callback.php` (17), `fluxo.php` (9)
- **Abas (tab.php):** Retornos pendentes (`tab.php:22`)
- **Ações (`case` do roteador, 9):** `etc006ContentRetornosPendentesAgenda`, `imagem`, `etc006GridRetornosPendenteAgenda`, `ajaxEtapas`, `ajaxEtapaUnica`, `ajaxEtapasGrid`, `content`, `sobre`, `etc006TabRetornosPendentesAgenda`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C007` (fotografia:396): UPDATE em `prog/sistema/outros/sobre.php:23`
- **Tabelas só lidas na tela (2):** `D085` (2×, `prog/sistema/outros/grid.php:15` e mais 1), `D024` (2×, `prog/sistema/outros/grid.php:15` e mais 1)
- **Abre as telas:** `cad/cad056`
- **Grafo (Graphify):** 14 nós, 6 funções definidas: `est014CheckboxComprar()`, `est014CheckboxCotar()`, `est014CheckboxFlagIndisponivel()`, `est014CheckboxGerarOC()`, `est014CheckboxProduzir()`, `validaCampoProduzir()`

### `prog/sistema/personalizacaoConf` — Configurações (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 383 linhas. `form.php` (325), `grid.php` (54), `content.php` (2), `tab.php` (2)
- **Abas (tab.php):** Configurações (`tab.php:2`), R Inserir/Alterar (`tab.php:2`), R Remover (`tab.php:2`), R Travar (`tab.php:2`), Menu (`tab.php:2`), Campos - Tipo (`tab.php:2`), Campos - Label Form (`tab.php:2`), Campos - Valor Padrão (`tab.php:2`), Campos - Não Usar (`tab.php:2`), Variáveis (ini) (`tab.php:2`), Tema/CSS (`tab.php:2`), Auditoria (`tab.php:2`) …
- **Ações (`case` do roteador, 2):** `alterarSQL`, `alterarFlag`
- **Tabelas só lidas na tela (1):** `C004` (2×, `prog/sistema/personalizacaoConf/tab.php:2`)
- **Mensagens e validações visíveis (3):**
  - "Selecione a empresa antes de personalizar o dashboard." (`prog/sistema/personalizacaoConf/form.php:15`)
  - "Configuração removida!" (`prog/sistema/personalizacaoConf/form.php:15`)
  - "Corrija a configuração antes de salvar: ' . json_last_error_msg()); $Code = false; eKjum: DJxkf: if (!($Code === false)) { goto AYD32; } goto dashboardPersonali" (`prog/sistema/personalizacaoConf/form.php:26`)
- **Grafo (Graphify):** 6 nós, 2 funções definidas: `{closure#1}()`, `getCodeDefault()`

### `prog/sistema/personalizacaoContent`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 3 arquivos. 36 linhas. `form.php` (32), `content.php` (2), `tab.php` (2)
- **Mensagens e validações visíveis (1):**
  - "Content personalizado com sucesso!" (`prog/sistema/personalizacaoContent/form.php:16`)
- **Grafo (Graphify):** 3 nós, 0 funções definidas

### `prog/sistema/personalizacaoForm` — Preencher Valor (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 269 linhas. `form.php` (263), `content.php` (2), `form_func-ajax.php` (2), `tab.php` (2)
- **Abas (tab.php):** Preencher Valor (`tab.php:2`), Callback JS (`tab.php:2`), Ao Alterar (`tab.php:2`), Restrição (`tab.php:2`), Preencher Select (`tab.php:2`), Adicionar Novo Campo (`tab.php:2`)
- **Ações (`case` do roteador, 1):** `novoCampo`
- **Mensagens e validações visíveis (3):**
  - "Form personalizado com sucesso!" (`prog/sistema/personalizacaoForm/form.php:38`)
  - "Configuração removida!" (`prog/sistema/personalizacaoForm/form.php:38`)
  - "Registro Alterado com Sucesso!" (`prog/sistema/personalizacaoForm/form.php:38`)
- **Grafo (Graphify):** 5 nós, 1 funções definidas: `getCodeDefault()`

### `prog/sistema/personalizacaoGrid` — HTML - Colunas (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 938 linhas. `form.php` (919), `salvarModelo.php` (13), `content.php` (2), `form_func-ajax.php` (2), `tab.php` (2)
- **Abas (tab.php):** HTML - Colunas (`tab.php:2`), Botões (`tab.php:2`), Configurações (`tab.php:2`), Colunas (`tab.php:2`), Ordem (`tab.php:2`), JS (`tab.php:2`), gridLinha() (`tab.php:2`), Extra (`tab.php:2`), Totais (`tab.php:2`), Filtros (`tab.php:2`), Totais (`tab.php:2`), HTML - Cabeçalho (`tab.php:2`)
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D140` (fotografia:7937): INSERT em `prog/sistema/personalizacaoGrid/form.php:313`
- **Mensagens e validações visíveis (3):**
  - "A personalização será feita apenas para este usuário, deseja continuar?" (`prog/sistema/personalizacaoGrid/form.php:226`)
  - "Registro Alterado com Sucesso! ." (`prog/sistema/personalizacaoGrid/form.php:212`)
  - "Registro Alterado com Sucesso!" (`prog/sistema/personalizacaoGrid/form.php:381`)
- **Abre as telas:** `cad/cad001`
- **Grafo (Graphify):** 8 nós, 3 funções definidas: `carregarEditor()`, `pegarFiltros()`, `t()`

### `prog/sistema/personalizacaoLowCode` — Alterar Parâmetros do Callback (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 12 arquivos. 3.283 linhas. `form.php` (866), `grid.php` (759), `outro.php` (613), `bi.php` (529), `content.php` (220), `tela.php` (162), `dashboard.php` (106), `indicador.php` (20), `codigo.php` (2), `excel.php` (2), `grid_func-ajax.php` (2), `tab.php` (2)
- **Abas (tab.php):** Alterar Parâmetros do Callback (`tab.php:2`), Colunas Ocultas (`tab.php:2`), Totais Ocultos (`tab.php:2`), Total (`tab.php:2`), Módulos (`tab.php:2`), Opções (`tab.php:2`), Content (`tab.php:2`), Tab (`tab.php:2`), Grid (`tab.php:2`), Form (`tab.php:2`), Outro (`tab.php:2`), Relatórios (`tab.php:2`) …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D182` (fotografia:9473): INSERT em `prog/sistema/personalizacaoLowCode/bi.php:453`
  - `D183` (fotografia:9494): DELETE em `prog/sistema/personalizacaoLowCode/form.php:743`
- **Classes e métodos chamados:** `ResultadoGeracaoPdf` (198 linhas) (só incluída)
- **Mensagens e validações visíveis (83):**
  - "Remover fórmula deste campo?" (`prog/sistema/personalizacaoLowCode/content.php:213`)
  - "Tela salva com sucesso!" (`prog/sistema/personalizacaoLowCode/content.php:2`)
  - "A cláusula deve ser preenchida." (`prog/sistema/personalizacaoLowCode/form.php:54`)
  - "Coluna ' . ($acaoId ? 'alterada' : 'incluída" (`prog/sistema/personalizacaoLowCode/form.php:54`)
  - "Coluna alterada com sucesso!" (`prog/sistema/personalizacaoLowCode/form.php:65`)
  - "Callback ' . ($acaoId ? 'alterado' : 'incluído" (`prog/sistema/personalizacaoLowCode/form.php:147`)
  - "Callback removido!" (`prog/sistema/personalizacaoLowCode/form.php:160`)
  - "O callback deve ser preenchido." (`prog/sistema/personalizacaoLowCode/form.php:160`)
  - … mais 75
- **Abre as telas:** `etc/etc011`
- **Grafo (Graphify):** 47 nós, 35 funções definidas: `{closure#1}()`, `{closure#2}()`, `alterarModulo()`, `{closure#1}()`, `onChangeTipoBotao()`, `appsDownloadCliente()` …. Chama fora da tela: `str_pad()` (outros.php)

### `prog/sistema/personalizacaoTab`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 46 linhas. `form.php` (32), `form_func-ajax.php` (10), `content.php` (2), `tab.php` (2)
- **Mensagens e validações visíveis (1):**
  - "Tabs personalizada com sucesso!" (`prog/sistema/personalizacaoTab/form.php:27`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/sistema/progs`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 1.404 linhas. `db.php` (1216), `db.sigmacar.update.php` (185), `db.azpr.serialized` (1), `db.fixar.serialized` (1), `db.koisas.serialized` (1)
- **Ações (`case` do roteador, 24):** `grid_atual`, `table_fields`, `filtro`, `grid_atual_filtro`, `grid_outro_filtro_tabela`, `grid_outro_filtro_campo`, `changebaseAtual`, `grid_outro_tabelas`, `grid_outro`, `grid_outro_campos`, `relacionamento`, `criarRelacionamento`, `dbRemoverRel`, `iniciarUpdate`, `iniciarConversao`, `datetime`, `date`, `time`, `datetime`, `TIMESTAMP` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C004` (fotografia:176): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:20`
  - `C007` (fotografia:396): INSERT em `prog/sistema/progs/db.sigmacar.update.php:42`
  - `C019` (fotografia:811): INSERT em `prog/sistema/progs/db.sigmacar.update.php:45`, `prog/sistema/progs/db.sigmacar.update.php:46` e mais 2
  - `D001` (fotografia:2944): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:94`, `prog/sistema/progs/db.sigmacar.update.php:98` e mais 2
  - `D006` (fotografia:3314): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:108`, `prog/sistema/progs/db.sigmacar.update.php:140`
  - `D009` (fotografia:3608): INSERT em `prog/sistema/progs/db.sigmacar.update.php:77`
  - `D024` (fotografia:4506): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:35`, `prog/sistema/progs/db.sigmacar.update.php:96` e mais 6
  - `D041` (fotografia:5252): INSERT em `prog/sistema/progs/db.sigmacar.update.php:66`
  - `D049` (fotografia:5460): INSERT/UPDATE em `prog/sistema/progs/db.sigmacar.update.php:60`, `prog/sistema/progs/db.sigmacar.update.php:95`
  - `D116` (fotografia:7352): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:104`
  - `T001` (fotografia:14264): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:88`, `prog/sistema/progs/db.sigmacar.update.php:90` e mais 2
  - `T002` (fotografia:14415): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:29`
  - `T007` (fotografia:16254): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:23`
  - `T008` (fotografia:16670): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:26`
  - `T015` (fotografia:17763): UPDATE em `prog/sistema/progs/db.sigmacar.update.php:32`
- **Tabelas só lidas na tela (6):** `D020` (1×, `prog/sistema/progs/db.sigmacar.update.php:36`), `D018` (1×, `prog/sistema/progs/db.sigmacar.update.php:37`), `D030` (1×, `prog/sistema/progs/db.sigmacar.update.php:38`), `D037` (1×, `prog/sistema/progs/db.sigmacar.update.php:98`), `D133` (1×, `prog/sistema/progs/db.sigmacar.update.php:104`), `D134` (1×, `prog/sistema/progs/db.sigmacar.update.php:104`)
- **Grafo (Graphify):** 8 nós, 7 funções definidas: `changeBaseAtual()`, `dbRemoverRel()`, `funOrdTableOrigem()`, `funOrdTableOrigemComment()`, `mySortFunc()`, `quebraString()` …

### `prog/sistema/scripts`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 7 arquivos. 141 linhas. `atualizacaoTitulos.php` (102), `atualizacaoBase-rotinas.php` (8), `atualizacaoBase.php` (7), `atualizacaoBaseAuditoria.php` (7), `atualizacaoSSD.php` (7), `atualizacaoLeiTransparencia.php` (6), `tabelasR.php` (4)
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T002` (fotografia:14415): UPDATE em `prog/sistema/scripts/atualizacaoTitulos.php:19`, `prog/sistema/scripts/atualizacaoTitulos.php:37`
  - `T015` (fotografia:17763): UPDATE em `prog/sistema/scripts/atualizacaoTitulos.php:53`, `prog/sistema/scripts/atualizacaoTitulos.php:69` e mais 2
- **Tabelas só lidas na tela (4):** `D006` (6×, `prog/sistema/scripts/atualizacaoTitulos.php:12` e mais 5), `D014` (4×, `prog/sistema/scripts/atualizacaoTitulos.php:13` e mais 3), `T013` (4×, `prog/sistema/scripts/atualizacaoTitulos.php:45` e mais 3), `T007` (2×, `prog/sistema/scripts/atualizacaoTitulos.php:11` e mais 1)
- **Grafo (Graphify):** 7 nós, 0 funções definidas

### `prog/sistema/tema` — Add. Tema (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 3 arquivos. 162 linhas. `form.php` (108), `tab.php` (29), `content.php` (25)
- **Abas (tab.php):** Add. Tema (`tab.php:17`)
- **Ações (`case` do roteador, 3):** `temaContentAdd`, `temaFormAdd`, `temaTabAdd`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D087` (fotografia:6917): form em `prog/sistema/tema/form.php:27`
- **Mensagens e validações visíveis (2):**
  - "Nome deve ser preenchido." (`prog/sistema/tema/form.php:60`)
  - "Ok" (`prog/sistema/tema/form.php:85`)
- **Grafo (Graphify):** 3 nós, 0 funções definidas

### `prog/sistema/webservice`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 139 linhas. `impNota.php` (30), `magento.php` (30), `sis.php` (30), `tray.php` (29), `pluggto.php` (20)
- **Ações (`case` do roteador, 6):** `send`, `send`, `notificacao`, `send`, `notificacao`, `teste`
- **Grafo (Graphify):** 5 nós, 0 funções definidas

