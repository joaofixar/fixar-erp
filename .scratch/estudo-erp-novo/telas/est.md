# Fichas de tela: `prog/est` — Estoque, almoxarifado, compras e NF recebida

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Compras: o fluxo que vale (respondido em 25/11/2026)

O João confirmou que a Fixar usa o **fluxo novo de compras**. A separação por fluxo vem das tabelas que cada tela lê e grava, segundo a extração deste estudo:

- **Fluxo novo (dentro do porte):**
  - `est/est015`: cotação de compra (T218, T219); também atende "Requisitar Compra" no Menu comentado (`bibliotecas/menu.php:192`);
  - `est/est016`: ordem de compra e Análise de Ordem de compra (T224, T225; item ativo no Menu, acesso 173);
  - `est/est019`: compras de estoque (T218, T219, T225);
  - `est/est009` só lê as tabelas novas.
- **Fluxo antigo (fora do porte):** `est/est006` (Ordem de Compra, T011 e T012), `est/est008` (Cotação de Compra, T010) e `est/est012` (Análise de Cotação, T010). Os itens do Menu já estão comentados (`bibliotecas/menu.php:196–198`).
- **A conferir antes de cortar:**
  - `est/est010` (Compras de Emergência) só lê T012;
  - `est/est014` (Requisições de Compra) lê T010 e T012 e grava a requisição T189, que o fluxo novo também usa;
  - a T010 ainda é lida por `est015`, `est016` e `ven/ven001`. **Hipótese:** leitura de histórico ou de compatibilidade.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `est/est001` | Divergências de Estoque | 19* | 8 | 1461 | 3 | 16 |
| `est/est002` | Expedição Mercadorias | 20 | 14 | 13398 | 12 | 53 |
| `est/est003` | Estoque | 110, 69* | 10 | 5039 | 6 | 42 |
| `est/est004` | Notas Fiscais Recebidas | 21 | 10 | 9468 | 10 | 50 |
| `est/est005` | Conhecimentos | 22 | 9 | 1782 | 3 | 26 |
| `est/est006` | Ordem de Compra | 23* | 10 | 2694 | 3 | 17 |
| `est/est007` | Montagens/Desmontagens | 24* | 8 | 1363 | 3 | 14 |
| `est/est008` | Cotação de Compra | 88* | 13 | 7809 | 10 | 22 |
| `est/est009` | Compras Estoque/Emergência | 94* | 7 | 6389 | 4 | 42 |
| `est/est010` | Compras de Emergência | 95* | 7 | 820 | 2 | 22 |
| `est/est011` | RMA | 98 | 9 | 1926 | 3 | 18 |
| `est/est012` | Análise de Cotação | 100* | 7 | 440 | 1 | 5 |
| `est/est013` | Relatórios | 101*, 153* | 9 | 1110 | 2 | 19 |
| `est/est014` | Requisitar Compra | 145*, 106* | 9 | 3392 | 3 | 21 |
| `est/est015` | Cotação de compra Novo | 170*, 172* | 11 | 16286 | 22 | 47 |
| `est/est016` | Análise de Ordem de compra | 173, 171* | 10 | 16132 | 18 | 53 |
| `est/est017` | P.C.P. | 203 | 10 | 5328 | 10 | 33 |
| `est/est018` | Exportar XML CT-e (Recebido) | 48, 297 | 3 | 343 | 1 | 0 |
| `est/est019` | Compras de Estoque (rótulo da primeira aba) |  | 8 | 8767 | 11 | 47 |
| `est/est020` | CT-e (rótulo da primeira aba) |  | 10 | 4610 | 7 | 20 |

Total: 20 telas. 108.557 linhas. 90 tabelas gravadas. 193 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/est/est001` — Divergências de Estoque

- **Menu:** Estoque › Divergências de Estoque, acesso C029 nº 19 "Divergências de Estoque" (item comentado no Menu) (`bibliotecas/menu.php:189`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 180 "Atualizar o Estoque das Divergências de Estoque (D.E.)" (`prog/est/est001/content.php:51`)
- **Arquivos:** 8 arquivos. 1.461 linhas. `form.php` (515), `grid.php` (419), `grid_func-callback.php` (110), `content.php` (103), `tab.php` (87), `form_func-js.php` (79), `form_func-ajax.php` (78), `outro.php` (70)
- **Abas (tab.php):** Divergências de Estoque (`tab.php:19`), Produtos (`tab.php:55`), Produtos (`tab.php:57`), Cabeçalho (`tab.php:66`), Importar Arq. Coletora (`tab.php:67`), Divergências Estoque (`tab.php:73`)
- **Ações (`case` do roteador, 15):** `est001ContentDivergenciasEstoque`, `est001ContentDivergenciasEstoqueIncluir`, `est001ContentDivergenciasEstoqueIncluirProduto`, `est001ContentDivergenciasEstoqueIncluirProdutoGrid`, `est001FormDivergenciasEstoque`, `est001FormDivergenciasEstoqueIncluirProduto`, `est001FormDivergenciasEstoqueImportar`, `atualizarCamposProdutos`, `atualizarEstoque`, `insereRegistroT001`, `est001GridDivergenciasEstoque`, `est001GridDivergenciasEstoqueProdutos`, `est001CabecalhoDE`, `est001TabDivergenciasEstoque`, `est001TabDivergenciasEstoqueIncluir`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D030` (fotografia:4985): form em `prog/est/est001/form.php:437`
  - `T029` (fotografia:18321): form em `prog/est/est001/form.php:32`
  - `T030` (fotografia:18370): form em `prog/est/est001/form.php:176`
- **Tabelas só lidas na tela (16):** `D004` (4×, `prog/est/est001/form.php:204` e mais 3), `C007` (4×, `prog/est/est001/form.php:206` e mais 3), `D049` (3×, `prog/est/est001/form.php:196` e mais 2), `D009` (3×, `prog/est/est001/form.php:198` e mais 2), `D037` (3×, `prog/est/est001/form.php:200` e mais 2), `T066` (3×, `prog/est/est001/form.php:203` e mais 2), `D082` (2×, `prog/est/est001/form.php:199` e mais 1), `D001` (2×, `prog/est/est001/form_func-ajax.php:26` e mais 1), `T001` (2×, `prog/est/est001/grid.php:160` e mais 1), `D041` (1×, `prog/est/est001/form.php:197`), `D060` (1×, `prog/est/est001/form.php:201`), `C008` (1×, `prog/est/est001/form.php:202`), `D144` (1×, `prog/est/est001/form.php:205`), `D146` (1×, `prog/est/est001/form.php:227`), `T029A` (1×, `prog/est/est001/grid.php:121`), `D175` (1×, `prog/est/est001/grid.php:288`)
- **Classes e métodos chamados:** `EST001` (797 linhas): `incluirTransferenciaEstoque` (`EST001.php:531`), `ImportarColetora` (`EST001.php:412`)
- **Tabelas gravadas pelos métodos chamados:** `T030` (EST001::ImportarColetora, EST001::incluirTransferenciaEstoque)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Custo_Unitario` (`prog/est/est001/form_func-ajax.php:20`)
- **Chaves C031 lidas (3):** `SeparaProdutoPorEmpresa`, `calculadoraQtdNavegaTab`, `impressaoJava`
- **Mensagens e validações visíveis (4):**
  - "Registro Alterado com Sucesso!" (`prog/est/est001/form.php:75`)
  - "Registro Incluido com Sucesso!" (`prog/est/est001/form.php:96`)
  - "Arquivo importado com Sucesso!" (`prog/est/est001/form.php:476`)
  - "Etiqueta enviada para a fila de impresão." (`prog/est/est001/grid.php:367`)
- **Abre as telas:** `cad/cad002`, `ven/ven001`, `est/est003`
- **Grafo (Graphify):** 19 nós, 11 funções definidas: `insereRegistroT001()`, `validaCampoAlteraCusto()`, `validaCampoNovoCusto()`, `validaCampoOperacao()`, `validaCampoQuantidade()`, `verificaExclusao()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `cad/cad002`, `est/est003`

### `prog/est/est002` — Expedição Mercadorias

- **Menu:** Estoque › Expedição Mercadorias, acesso C029 nº 20 "Expedição Mercadorias" (`bibliotecas/menu.php:29`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 38 "Boleto Bancário" (`prog/est/est002/tab.php:62`); 230 "Alterar romaneio entrega do pedido na expedição aguardando coleta" (`prog/est/est002/tab.php:98`); 242 "MDF-e" (`prog/est/est002/grid.php:942` e mais 1)
- **Arquivos:** 14 arquivos. 13.398 linhas. `grid.php` (4964), `form.php` (2142), `grid_func-ajax.php` (1438), `report.php` (1330), `content.php` (916), `grid_func-callback.php` (643), `outro.php` (601), `imprimirPedido.php` (471), `tab.php` (377), `form_func-ajax.php` (200), `grid_func-js.php` (173), `grid_func-js-retornarExpedicao.php` (73) …
- **Abas (tab.php):** Impressão (`tab.php:21`), Separação (`tab.php:23`), Aguardando Separação (`tab.php:26`), Em Separação (`tab.php:27`), Conferência (`tab.php:30`), Separação Comercial (`tab.php:33`), Separação Balcão (`tab.php:34`), Conferência Comercial (`tab.php:35`), Conferência Balcão (`tab.php:36`), Gerar Notas Fiscais (`tab.php:40`), Enviar NFe para Sefaz (`tab.php:41`), Aguardando Coleta (`tab.php:44`) …
- **Ações (`case` do roteador, 160):** `est002ContentPrincipal`, `est002ContentProdutosEmSeparacao`, `est002ContentPedido`, `est002ContentEntregaInterna`, `est002ContentSelecionarFuncionario`, `est002ContentHistoricoEntregaInterna`, `est002ContentEstoque`, `est002ContentEtiquetas`, `est002ImprimirPedido`, `est002SeparacaoPedido`, `est002SeparacaoPedido2Rows`, `est002FaturamentoPedido`, `est002ContentConfirmarEmprestimo`, `est002ContentCadastroEmprestimo`, `est002ContentFaturamentoPedido`, `est002SeparacaoPorLocacao`, `est002SeparacaoPorLocacao2rows`, `est002ContentSeparacaoBalcao`, `est002ContentSeparacaoComercial`, `est002ContentCadastroPacote` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D138` (fotografia:7895): form em `prog/est/est002/form.php:850`
  - `P001` (prefixo P; **hipótese:** Personalização, não descrita): INSERT em `prog/est/est002/grid_func-ajax.php:855`, `prog/est/est002/grid_func-ajax.php:890` e mais 1
  - `T005` (fotografia:15218): UPDATE/form em `prog/est/est002/form.php:207`, `prog/est/est002/form.php:1353` e mais 4
  - `T005A` (fotografia:15535): INSERT/UPDATE em `prog/est/est002/form_func-ajax.php:192`, `prog/est/est002/report.php:670` e mais 1
  - `T006` (fotografia:15620): UPDATE/form em `prog/est/est002/form.php:1418`, `prog/est/est002/form.php:1520` e mais 3
  - `T007` (fotografia:16254): UPDATE em `prog/est/est002/grid_func-ajax.php:360`, `prog/est/est002/grid_func-ajax.php:572` e mais 4
  - `T008` (fotografia:16670): UPDATE em `prog/est/est002/validaColeta.php:35`
  - `T175` (fotografia:23413): form em `prog/est/est002/form.php:599`
  - `T178` (fotografia:23467): INSERT em `prog/est/est002/validaColeta.php:28`, `prog/est/est002/validaColeta.php:39`
  - `T238` (fotografia:26134): INSERT/UPDATE/form em `prog/est/est002/form.php:946`, `prog/est/est002/form_func-ajax.php:130` e mais 5
  - `T268` (fotografia:26977): form em `prog/est/est002/form.php:1121`
  - `T313` — Conferencia Volume (Temporaria) (fotografia:28666): INSERT/UPDATE em `prog/est/est002/grid_func-ajax.php:1427`, `prog/est/est002/grid_func-ajax.php:1429`
- **Tabelas só lidas na tela (53):** `C007` (81×, `prog/est/est002/grid.php:71` e mais 80), `D009` (50×, `prog/est/est002/form.php:624` e mais 49), `D049` (47×, `prog/est/est002/form.php:624` e mais 46), `D001` (47×, `prog/est/est002/form.php:969` e mais 46), `D024` (42×, `prog/est/est002/form.php:22` e mais 41), `D020` (39×, `prog/est/est002/form.php:181` e mais 38), `D018` (38×, `prog/est/est002/form.php:177` e mais 37), `D006` (36×, `prog/est/est002/form.php:185` e mais 35), `D022` (34×, `prog/est/est002/form.php:164` e mais 33), `C004` (32×, `prog/est/est002/grid.php:72` e mais 31), `D021` (26×, `prog/est/est002/grid.php:68` e mais 25), `D036` (26×, `prog/est/est002/grid.php:73` e mais 25), `D082` (17×, `prog/est/est002/form.php:624` e mais 16), `D004` (16×, `prog/est/est002/form.php:958` e mais 15), `C008` (16×, `prog/est/est002/grid.php:184` e mais 15), `D037` (15×, `prog/est/est002/form.php:624` e mais 14), `T066` (14×, `prog/est/est002/form.php:959` e mais 13), `T055` (10×, `prog/est/est002/grid.php:463` e mais 9), `T075` (10×, `prog/est/est002/grid.php:464` e mais 9), `T314` (7×, `prog/est/est002/form.php:2065` e mais 6), `D109` (7×, `prog/est/est002/grid.php:1543` e mais 6), `D002` (6×, `prog/est/est002/grid.php:3537` e mais 5), `D148` (5×, `prog/est/est002/form_func-ajax.php:161` e mais 4), `D005` (5×, `prog/est/est002/grid.php:2348` e mais 4), `D001A` (4×, `prog/est/est002/form.php:970` e mais 3) …
- **Classes e métodos chamados:** `API001` (3648 linhas): `executaProcesso` (`API001.php:43`); `EST002` (2393 linhas): `ImprimirPedido` (`EST002.php:130`), `SeparacaoPedido` (`EST002.php:226`), `FaturamentoPedido` (`EST002.php:334`), `confirmarEmpretismo` (`EST002.php:476`), `validaInclusaoEmprestimo` (`EST002.php:520`), `confirmarEntregaInterna` (`EST002.php:630`), `separarItemPedidoProdutoBox` (`EST002.php:1153`), `isPedidoSeparadoBoxLeitor` (`EST002.php:1420`), `volumesConferenciaFinalizar` (`EST002.php:2022`), `efetivarImpressaoPedido` …; `GFormWrap` (29 linhas) (só incluída); `VEN003` (19831 linhas) (só incluída); `VEN012` (24304 linhas): `retornarPedido` (`VEN012.php:9789`), `retornarPedidoSeparacao` (`VEN012.php:9906`), `confirmarEntrega` (`VEN012.php:9948`), `retornarPedidoColetado` (`VEN012.php:23830`)
- **Tabelas gravadas pelos métodos chamados:** `D009` (EST002::FaturamentoPedido), `T001` (VEN012::confirmarEntrega), `T005` (EST002::FaturamentoPedido, EST002::SeparacaoPedido, EST002::finalizarSeparacaoPedidoBoxLeitor…), `T006` (EST002::SeparacaoPedido, EST002::confirmarEntregaInterna, EST002::retornarPedidoExpedicao…), `T007` (EST002::enviarXMLEmailTransportadora, EST002::prePostagemPacote), `T139` (EST002::enviarXMLEmailTransportadora), `T175` (EST002::confirmarEmpretismo, EST002::validaInclusaoEmprestimo), `T178` (EST002::FaturamentoPedido, EST002::ImprimirPedido, EST002::SeparacaoPedido…), `T179` (EST002::confirmarEntregaInterna), `T238` (EST002::retornarPedidoExpedicao, EST002::separarItemPedidoProdutoBox, EST002::volumesConferenciaCancelar…), `T238A` (EST002::retornarPedidoExpedicao, EST002::volumesConferenciaCancelar, EST002::volumesConferenciaFinalizar…), `T268` (EST002::prePostagemPacote), `T275` (EST002::gerarNumeroRomaneio), `T311` (EST002::separarItemPedidoProdutoBox), `T313` (EST002::gravarQuantidadeConferida, EST002::retornarPedidoExpedicao, EST002::volumesConferenciaFinalizar…) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_D005_Id` (`prog/est/est002/grid.php:2348`), `D009_Quantidade_OC` (`prog/est/est002/grid.php:1587` e mais 4), `D009_Quantidade_Pedido` (`prog/est/est002/grid.php:3699`), `T005_Prazos` (`prog/est/est002/grid.php:53` e mais 9), `T005_Status_Pedido` (`prog/est/est002/grid.php:41` e mais 10)
- **Chaves C031 lidas (17):** `EnviarIntelipostColetar`, `EnviarMagentoColetar`, `ImagemPrincipalB2B`, `IntelipostUrlApi`, `campoNomeEmpresa`, `conferenciaCegaVendas`, `desabilitarConferencia`, `desabilitarSeparacao`, `exibirFotoImpressaoVenda`, `expedicaoDeMercadoria`, `modeloExpedicao`, `permitirFaturarSemEstoque` …
- **Mensagens e validações visíveis (29):**
  - "Usuário não identificado! Por favor, tente novamente." (`prog/est/est002/content.php:106`)
  - "Tempo esgotado! Tente novamente." (`prog/est/est002/content.php:130`)
  - "Este processo irá preencher automaticamente as  quantidades  para separar dos produtos na  primeira locação  encontrada. Deseja continuar!?" (`prog/est/est002/content.php:596`)
  - "Este processo irá reiniciar toda a conferência já realizada para este pedido, removendo todos os volumes já gerados. Deseja continuar!?" (`prog/est/est002/content.php:834`)
  - "Este processo irá reiniciar a separação atual, cancelando apenas as quantidades atuais conferidas. Deseja continuar!?" (`prog/est/est002/content.php:852`)
  - "Quantidade de volumes inválida!" (`prog/est/est002/form.php:1595`)
  - "Informe um código de barras para prosseguir com a separação do box {$idBox}!" (`prog/est/est002/form.php:1927`)
  - "Parcelas cadastradas com sucesso." (`prog/est/est002/form.php:104`)
  - … mais 21
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::gerarNotaFiscal` (`NOTIFICACOES.php:1986`, clique na linha 2006; lê `C004`, `T005`); `NOTIFICACOES::enviarNotaFiscal` (`NOTIFICACOES.php:2019`, clique na linha 2047; lê `C004`, `T007`)
- **Abre as telas:** `fis/fis012`, `cad/cad002`, `est/est003`, `ven/ven003`, `ven/ven012`
- **Grafo (Graphify):** 95 nós, 81 funções definidas: `enviarDadosTray_grid2()`, `gridLinha()`, `impEtiquetaControleInterno_grid2()`, `impEtiquetaDanfe_grid2()`, `impEtiquetaTranspML_grid2()`, `execImpML_grid2()` …. Chama fora da tela: `number_format()` (formulario.php), `.log()` (CAD002.php)
- **Aberta por:** `cad/cad061`, `crm/crm001`, `est/est015`, `ven/ven012`

**Leitura do código (manual):**

- **Na Fixar (25/11/2026):** expedição modelo 1 com a etapa de separação ligada (`desabilitarSeparacao` = N; aba em `prog/est/est002/tab.php:22`). Os ramos de expedição sem separação (`EST002.php:180–183`) e do modelo 2 (`tab.php:32`) saem do porte.

### `prog/est/est003` — Estoque

- **Menu:** Estoque › Estoque, acesso C029 nº 110 "Estoque" (`bibliotecas/menu.php:28`)
- **Menu:** Estoque › Expedição Mercadorias 2, acesso C029 nº 69 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:190`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 19 "Divergências de Estoque" (`prog/est/est003/tab.php:78`); 24 "Montagens/Desmontagens" (`prog/est/est003/tab.php:81`); 206 "Processar balanço" (`prog/est/est003/grid.php:1306`)
- **Arquivos:** 10 arquivos. 5.039 linhas. `grid.php` (2841), `form.php` (756), `content.php` (440), `grid_func-ajax.php` (258), `tab.php` (249), `grid_func-callback.php` (214), `outros.php` (103), `grid_func-js.php` (68), `form_func-js.php` (63), `form_func-ajax.php` (47)
- **Abas (tab.php):** Não Liberado (`tab.php:18`), Aguardando Prod/Compra (`tab.php:19`), Disponível p/ Faturar (`tab.php:20`), Divergências (`tab.php:21`), Lista Box (`tab.php:22`), Estoque (`tab.php:23`), Produtos (`tab.php:39`), Locações (`tab.php:40`), Balanço (`tab.php:46`), Loja (`tab.php:47`), Produtos (`tab.php:66`), Locações (`tab.php:67`) …
- **Ações (`case` do roteador, 74):** `est003ContentPrincipal`, `est003ContentPedido`, `est003Content`, `est003ContentBalancoImportarCSV`, `est003ContentDefeito`, `est003ContentProdutosDefeitos`, `est003ContentEstoqueOE`, `est003contentIncluirDE`, `est003ContentLoja`, `est003ContentPrecoLojaHistoricoItem`, `est003ContentHistoricoLojaPreco`, `est003ContentMovimentacao`, `est003ContentLocacoes`, `est003ContentColetas`, `est003ContentGerarPedido`, `est003ContentParticipante`, `est003FormIncluirDE`, `est003FormImportarCSV`, `est003FormProdutosDefeito`, `est003FormGerarPedido` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D009` (fotografia:3608): UPDATE em `prog/est/est003/grid_func-ajax.php:170`
  - `T001` (fotografia:14264): form em `prog/est/est003/form.php:57`
  - `T005` (fotografia:15218): form em `prog/est/est003/form.php:552`
  - `T066` (fotografia:19874): UPDATE/form em `prog/est/est003/form.php:226`, `prog/est/est003/grid_func-ajax.php:133`
  - `T206` (fotografia:24238): DELETE/UPDATE/form em `prog/est/est003/form.php:331`, `prog/est/est003/grid_func-ajax.php:83` e mais 1
  - `T239` (fotografia:26183): INSERT em `prog/est/est003/grid_func-ajax.php:156`
- **Tabelas só lidas na tela (42):** `D049` (26×, `prog/est/est003/content.php:231` e mais 25), `D001` (26×, `prog/est/est003/content.php:232` e mais 25), `C007` (24×, `prog/est/est003/form.php:566` e mais 23), `D024` (22×, `prog/est/est003/form.php:26` e mais 21), `D004` (15×, `prog/est/est003/form.php:38` e mais 14), `D006` (14×, `prog/est/est003/form.php:27` e mais 13), `D082` (12×, `prog/est/est003/form.php:23` e mais 11), `D036` (10×, `prog/est/est003/form.php:542` e mais 9), `D021` (9×, `prog/est/est003/grid.php:65` e mais 8), `T225` (9×, `prog/est/est003/grid.php:2085` e mais 8), `T006` (8×, `prog/est/est003/form.php:20` e mais 7), `D037` (8×, `prog/est/est003/form.php:28` e mais 7), `D018` (8×, `prog/est/est003/form.php:583` e mais 7), `C004` (7×, `prog/est/est003/grid.php:69` e mais 6), `T013` (7×, `prog/est/est003/grid.php:2130` e mais 6), `D020` (6×, `prog/est/est003/form.php:542` e mais 5), `D116` (6×, `prog/est/est003/grid.php:2122` e mais 5), `T014` (6×, `prog/est/est003/grid.php:2129` e mais 5), `T226` (6×, `prog/est/est003/grid.php:2135` e mais 5), `D031` (6×, `prog/est/est003/grid.php:2136` e mais 5), `D022` (4×, `prog/est/est003/form.php:542` e mais 3), `T007` (4×, `prog/est/est003/grid.php:71` e mais 3), `C008` (4×, `prog/est/est003/grid.php:769` e mais 3), `D060` (4×, `prog/est/est003/grid.php:772` e mais 3), `D005` (3×, `prog/est/est003/grid.php:612` e mais 2) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `D001_reprocessa_historico` (`CAD002.php:1159`); `EST003` (938 linhas): `importarCSVBalanco` (`EST003.php:401`), `gerarPedidoProdutosDefeitos` (`EST003.php:315`), `est003faturarPedido` (`EST003.php:160`), `est003BalancoCiclico` (`EST003.php:189`), `reabastecimentoLoja` (`EST003.php:643`), `mudarStatusColeta` (`EST003.php:290`); `VEN012` (24304 linhas): `retornarPedido` (`VEN012.php:9789`)
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::D001_reprocessa_historico, EST003::reabastecimentoLoja), `D009A` (CAD002::D001_reprocessa_historico), `T001` (CAD002::D001_reprocessa_historico), `T005` (VEN012::retornarPedido), `T006` (EST003::gerarPedidoProdutosDefeitos), `T029` (EST003::reabastecimentoLoja), `T030` (EST003::reabastecimentoLoja), `T066` (CAD002::D001_reprocessa_historico, EST003::importarCSVBalanco), `T178` (EST003::est003faturarPedido, VEN012::retornarPedido), `T206` (EST003::gerarPedidoProdutosDefeitos), `T214` (EST003::est003BalancoCiclico), `T224` (EST003::mudarStatusColeta), `T238` (VEN012::retornarPedido)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_D005_Id` (`prog/est/est003/grid.php:612`), `D009_Quantidade_DF` (`prog/est/est003/grid.php:1066`), `D009_Quantidade_OC` (`prog/est/est003/grid.php:595`), `D009_Quantidade_OE` (`prog/est/est003/grid.php:917` e mais 3), `D009_Quantidade_Pedido` (`prog/est/est003/grid.php:730` e mais 1), `T005_Prazos` (`prog/est/est003/grid.php:50` e mais 2), `T005_Status_Pedido` (`prog/est/est003/grid.php:38` e mais 3), `T006_Quantidade_Pedidos` (`prog/est/est003/grid.php:916`), `T225_Quantidade_Pendente_3` (`prog/est/est003/grid.php:2083` e mais 5)
- **Chaves C031 lidas (5):** `SeparaProdutoPorEmpresa`, `alterarVendedor`, `alterarVendedorAusente`, `expedicaoDeMercadoria`, `impressaoJava`
- **Mensagens e validações visíveis (7):**
  - "Etiqueta enviada para a fila de impresão." (`prog/est/est003/content.php:256`)
  - "Registro Alterado com Sucesso!" (`prog/est/est003/form.php:132`)
  - "Registro Incluido com Sucesso!" (`prog/est/est003/form.php:155`)
  - "' . $retorno . '" (`prog/est/est003/form.php:270`)
  - "CSV Importado com Sucesso!" (`prog/est/est003/form.php:273`)
  - "Você tem certeza que deseja processar o balanço?" (`prog/est/est003/grid.php:1345`)
  - "Pedido retornado!" (`prog/est/est003/grid_func-js.php:9`)
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::estoqueAguardandoColeta` (`NOTIFICACOES.php:2086`, clique na linha 2101; lê `T224`); `NOTIFICACOES::estoqueMontagemPendente` (`NOTIFICACOES.php:2110`, clique na linha 2120; lê `N001`, `T047`)
- **Abre as telas:** `cad/cad002`, `ven/ven002`, `ven/ven001`, `est/est001`, `sistema/etiquetas`
- **Grafo (Graphify):** 31 nós, 21 funções definidas: `est003CheckboxBalanco()`, `est003CheckboxGerarPedido()`, `est003FaturarPedido()`, `est003RetornarPedido()`, `carregaCFOP_Contatos()`, `gridLinha()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `est/est001`, `est/est002`, `est/est007`, `ven/ven001`

### `prog/est/est004` — Notas Fiscais Recebidas

- **Menu:** Estoque › Notas Fiscais Recebidas, acesso C029 nº 21 "Notas Fiscais Recebidas" (`bibliotecas/menu.php:30`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 189 "Alterar/Excluir Nota Fiscal de Entrada" (`prog/est/est004/content.php:126` e mais 5); 220 "Finalizar financeiro nota fiscal entrada" (`prog/est/est004/content.php:120`); 253 "Alterar obrigação de vincúlo NF de compra com OC " (`prog/est/est004/form.php:462`); 265 "Autorizar divergência entre NF e OC" (`prog/est/est004/grid.php:2722`); 275 "CIAP - Crédito ICMS Ativo Imobilizado" (`prog/est/est004/tab.php:102`)
- **Arquivos:** 10 arquivos. 9.468 linhas. `grid.php` (3706), `form.php` (2224), `content.php` (875), `grid_func-ajax.php` (667), `form_func-ajax.php` (637), `grid_func-callback.php` (449), `tab.php` (356), `outro.php` (233), `grid_func-js.php` (165), `form_func-js.php` (156)
- **Abas (tab.php):** Principal (`tab.php:46`), ICMS (`tab.php:47`), IPI (`tab.php:48`), Frete (`tab.php:49`), Serviços (`tab.php:50`), Outros (`tab.php:51`), Produtos (`tab.php:85`), Cabeçalho (`tab.php:86`), ICMS (`tab.php:87`), IPI (`tab.php:88`), Serviços (`tab.php:90`), Outros (`tab.php:91`) …
- **Ações (`case` do roteador, 122):** `est004ContentPrincipal`, `est004ContentCadastroNotaFiscal`, `est004ContentEditarNotaFiscal`, `est004ContentProdutosNota`, `est004ContentPagamentoNota`, `est004ContentAssociarItem`, `est004ContentAssociarItemProduto`, `est004ContentImportarOC`, `est004ContentImportarOCNovo`, `est004ContentImportarOS`, `est004ContentInserirQuantidade`, `est004ContentImportarItensNota`, `est004ContentCancelarNotaEntrada`, `est004ContentImportarXml`, `est004ContentCartaCorrecao`, `est004ContentAssociarItemNovo`, `est004ContentCadastroObservacao`, `est004ContentCadastroObrigacaoTributaria`, `est004ContentCadastroCiap`, `est004FormProdutosNota` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D009A` (fotografia:3791): INSERT/UPDATE em `prog/est/est004/form_func-ajax.php:366`, `prog/est/est004/form_func-ajax.php:369`
  - `T007` (fotografia:16254): form em `prog/est/est004/form.php:291`
  - `T008` (fotografia:16670): UPDATE em `prog/est/est004/grid_func-ajax.php:531`
  - `T013` (fotografia:17309): form em `prog/est/est004/form.php:423`
  - `T013A` (fotografia:17476): INSERT/UPDATE em `prog/est/est004/form.php:864`, `prog/est/est004/form.php:867`
  - `T014` (fotografia:17524): INSERT/UPDATE/form em `prog/est/est004/form.php:36`, `prog/est/est004/form.php:858` e mais 7
  - `T225` (fotografia:25444): UPDATE em `prog/est/est004/grid_func-ajax.php:458`, `prog/est/est004/grid_func-ajax.php:501`
  - `T297` (fotografia:28234): form em `prog/est/est004/form.php:1694`
  - `T298` (fotografia:28257): form em `prog/est/est004/form.php:1837`
  - `T316` (fotografia:28740): UPDATE/form em `prog/est/est004/form.php:1993`, `prog/est/est004/form.php:2079`
- **Tabelas só lidas na tela (50):** `D009` (29×, `prog/est/est004/form.php:48` e mais 28), `D049` (27×, `prog/est/est004/form.php:47` e mais 26), `D001` (21×, `prog/est/est004/form.php:1856` e mais 20), `D006` (20×, `prog/est/est004/content.php:83` e mais 19), `D024` (19×, `prog/est/est004/form.php:442` e mais 18), `C007` (15×, `prog/est/est004/form.php:1292` e mais 14), `D082` (14×, `prog/est/est004/form.php:49` e mais 13), `D037` (7×, `prog/est/est004/form.php:50` e mais 6), `D005` (7×, `prog/est/est004/form_func-ajax.php:61` e mais 6), `T224` (7×, `prog/est/est004/grid.php:610` e mais 6), `D083` (6×, `prog/est/est004/grid.php:614` e mais 5), `D004` (5×, `prog/est/est004/form.php:95` e mais 4), `T001` (5×, `prog/est/est004/form_func-ajax.php:353` e mais 4), `C004` (5×, `prog/est/est004/grid.php:350` e mais 4), `T012` (5×, `prog/est/est004/grid.php:608` e mais 4), `D018` (4×, `prog/est/est004/grid.php:93` e mais 3), `D022` (3×, `prog/est/est004/form.php:563` e mais 2), `D031` (3×, `prog/est/est004/grid.php:928` e mais 2), `T086` (3×, `prog/est/est004/grid.php:1875` e mais 2), `D154` (2×, `prog/est/est004/form.php:626` e mais 1), `D149` (2×, `prog/est/est004/form_func-ajax.php:62` e mais 1), `D184` (2×, `prog/est/est004/grid.php:617` e mais 1), `T154` (2×, `prog/est/est004/grid.php:925` e mais 1), `T011` (2×, `prog/est/est004/grid.php:1297` e mais 1), `D014` (2×, `prog/est/est004/grid.php:2325` e mais 1) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `D001_reprocessa_historico` (`CAD002.php:1159`), `retornaDestinoProdutos` (`CAD002.php:12359`), `definirCodigoFornecedor` (`CAD002.php:5288`); `EST004` (10716 linhas): `incluirProduto` (`EST004.php:10361`), `pagamentoNota` (`EST004.php:233`), `prepararImportarXML` (`EST004.php:313`), `acoesAposAlterarNota` (`EST004.php:66`), `cadastrarNota` (`EST004.php:2044`), `associarProduto` (`EST004.php:4854`), `associarProdutoT008` (`EST004.php:4757`), `est004_alterar_quantidade2` (`EST004.php:5672`), `est004_alterar_quantidade` (`EST004.php:5554`), `EST004CancelarNotaEntrada` (`EST004.php:6196`) …; `EST004FreteNFS` (244 linhas) (só incluída); `GFormWrap` (29 linhas) (só incluída)
- **Tabelas gravadas pelos métodos chamados:** `C004` (EST004::finalizarNotaCompras), `D001` (EST004::EST004IncluirProduto, EST004::finalizarNotaEstoque), `D001A` (EST004::EST004IncluirProduto), `D005` (EST004::EST004IncluirProduto), `D009` (CAD002::D001_reprocessa_historico, EST004::EST004IncluirProduto, EST004::finalizarNotaEstoque), `D009A` (CAD002::D001_reprocessa_historico, EST004::finalizarNotaEstoque), `D037` (EST004::EST004IncluirProduto), `D049` (EST004::EST004IncluirProduto, EST004::finalizarNotaEstoque, EST004::insereRegistroT001), `D083` (CAD002::definirCodigoFornecedor, EST004::finalizarNotaEstoque, EST004::insereRegistroT001), `T001` (CAD002::D001_reprocessa_historico, EST004::EST004CancelarNotaEntrada, EST004::est004ReverterCancelamento…), `T001A` (EST004::finalizarNotaEstoque, EST004::insereRegistroT001), `T002` (EST004::EST004CancelarNotaEntrada, EST004::est004ReverterCancelamento, EST004::finalizarNotaFinanceiro), `T005` (EST004::atualizarPedidosVenda), `T006` (EST004::atualizarPedidosVenda, EST004::finalizarNotaEstoque, EST004::insereRegistroT001), `T012` (EST004::est004_alterar_quantidade, EST004::est004_alterar_quantidade2) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_D005_Id` (`prog/est/est004/form_func-ajax.php:61` e mais 3), `T008_Quantidade_Falta_Lancar` (`prog/est/est004/form.php:1294` e mais 3), `T012_Quantidade_Pendente_3` (`prog/est/est004/grid.php:1279` e mais 1), `T013_Gravar_Totalizacao` (`prog/est/est004/form.php:980` e mais 1), `T014_Gravar_Totalizacao` (`prog/est/est004/form_func-ajax.php:227`), `T129_Quantidade_Pendente_3` (`prog/est/est004/grid.php:2429` e mais 1), `T224_Prazos` (`prog/est/est004/grid.php:3108`), `T225_Gravar_Totalizacao` (`prog/est/est004/grid_func-ajax.php:428` e mais 1), `T225_Quantidade_Pendente_3` (`prog/est/est004/grid.php:1059` e mais 8)
- **Chaves C031 lidas (11):** `SeparaProdutoPorEmpresa`, `associarCfopOcParaNf`, `cyberlogInvoiceMethod`, `descricaoCFOPExibida`, `formaRateioFreteNotaRecebida`, `incluirProdutoNotaFiscalEntrada`, `obrigatorioFiltroTelaTransicao`, `reprocessarHistoricoTodasEmpresas`, `separarClientePorEmpresa`, `vincularProdutoNFDevolucaoSemVinculoNFSaida`, `vincularProdutoNFRecebidaOCQtdDiferentes`
- **Mensagens e validações visíveis (14):**
  - "Número da Nota Fiscal não confere!" (`prog/est/est004/content.php:217`)
  - "Você tem certeza?" (`prog/est/est004/content.php:436`)
  - "Parcelas cadastradas com sucesso." (`prog/est/est004/form.php:241`)
  - "' . $retorno . '" (`prog/est/est004/form.php:336`)
  - "Nota Importada com Sucesso!" (`prog/est/est004/form.php:339`)
  - "Registro Alterado com Sucesso!" (`prog/est/est004/form.php:872`)
  - "Registro Incuido com Sucesso!" (`prog/est/est004/form.php:900`)
  - "Produto associado com sucesso!." (`prog/est/est004/form.php:1189`)
  - … mais 6
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::compraAutorizada` (`NOTIFICACOES.php:887`, clique na linha 912; lê `T013`, `T014`, `T224`, `T225`)
- **Abre as telas:** `est/est016`, `cad/cad002`, `est/est008`, `est/est005`, `ven/ven003`, `est/est006`, `fin/fin002`, `srm/srm001`
- **Grafo (Graphify):** 73 nós, 63 funções definidas: `campoProcuraProdutoAssociar()`, `campoProcuraProdutoAssociarItem()`, `campoProcuraProdutoNota()`, `est004AlternarFreteNFS()`, `est004LimparSelecaoFreteNFS()`, `est004PesquisarFreteNFS()` …. Chama fora da tela: `number_format()` (formulario.php), `.log()` (CAD002.php)
- **Aberta por:** `cad/cad002`, `crm/crm002`, `erp/erp002`, `est/est005`, `est/est006`, `est/est016`, `srm/srm001`, `ven/ven001`, `ven/ven003`

### `prog/est/est005` — Conhecimentos

- **Menu:** Estoque › Conhecimentos, acesso C029 nº 22 "Conhecimentos" (`bibliotecas/menu.php:31`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 257 "CT-e" (`prog/est/est005/tab.php:23`)
- **Arquivos:** 9 arquivos. 1.782 linhas. `form.php` (866), `grid.php` (531), `content.php` (80), `tab.php` (74), `grid_func-callback.php` (71), `grid_func-ajax.php` (70), `form_func-ajax.php` (44), `grid_func-js.php` (32), `form_func-js.php` (14)
- **Abas (tab.php):** Conhecimentos (`tab.php:19`), Importar XML (`tab.php:20`), Transmissão (`tab.php:24`), Conhecimento (`tab.php:41`), Conhecimento (`tab.php:59`), Notas Relacionadas (`tab.php:60`), Contas a Pagar (`tab.php:61`)
- **Ações (`case` do roteador, 23):** `est005ContentPrincipal`, `est005ContentCadastroConhecimento`, `est005ContentEditarConhecimento`, `est005FormCadastroNotasRelacionadas`, `est004FormProdutosNota`, `est005FormImportarXML`, `est004FormPagamentoNota`, `est005FormCadastroConhecimento`, `est005FormCadastroNotasRelacionadas`, `gerarContasAPagar`, `populaD073`, `atualizaAliquotas`, `est005GridConhecimentos`, `est004GridProdutosNota`, `est004GridPagamentoNota`, `est005GridCadastroNotasRelacionadas`, `est005gridCadastroContasPagar`, `abreXML`, `CampoCancelar`, `CampoExcluir` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T004` (fotografia:14902): form em `prog/est/est005/form.php:39`
  - `T084` (fotografia:20479): form em `prog/est/est005/form.php:177`, `prog/est/est005/form.php:399`
  - `T085` (fotografia:20725): UPDATE/form em `prog/est/est005/form.php:716`, `prog/est/est005/form.php:799`
- **Tabelas só lidas na tela (26):** `T015` (4×, `prog/est/est005/form.php:626` e mais 3), `T007` (4×, `prog/est/est005/form.php:786` e mais 3), `T013` (3×, `prog/est/est005/grid.php:81` e mais 2), `D049` (2×, `prog/est/est005/form.php:48` e mais 1), `D009` (2×, `prog/est/est005/form.php:49` e mais 1), `D082` (2×, `prog/est/est005/form.php:50` e mais 1), `D037` (2×, `prog/est/est005/form.php:51` e mais 1), `D154` (2×, `prog/est/est005/form.php:505` e mais 1), `D022` (2×, `prog/est/est005/form.php:564` e mais 1), `T084A` (2×, `prog/est/est005/form.php:572` e mais 1), `D006` (2×, `prog/est/est005/form_func-ajax.php:24` e mais 1), `C007` (2×, `prog/est/est005/grid.php:72` e mais 1), `D024` (2×, `prog/est/est005/grid.php:74` e mais 1), `D032` (1×, `prog/est/est005/form.php:505`), `D020` (1×, `prog/est/est005/grid.php:75`), `D018` (1×, `prog/est/est005/grid.php:76`), `T014` (1×, `prog/est/est005/grid.php:213`), `T012` (1×, `prog/est/est005/grid.php:217`), `D001` (1×, `prog/est/est005/grid.php:220`), `T001` (1×, `prog/est/est005/grid.php:223`), `T154` (1×, `prog/est/est005/grid.php:290`), `D027` (1×, `prog/est/est005/grid.php:293`), `D073` (1×, `prog/est/est005/grid.php:465`), `D031` (1×, `prog/est/est005/grid.php:466`), `D014` (1×, `prog/est/est005/grid.php:467`) …
- **Classes e métodos chamados:** `EST004` (10716 linhas): `pagamentoNota` (`EST004.php:233`); `EST005` (1642 linhas): `prepararImportarXML` (`EST005.php:216`), `gerarContasAPagar` (`EST005.php:96`), `atualizarAliquotasConhecimento` (`EST005.php:1579`), `campoCancelar` (`EST005.php:55`), `campoExcluir` (`EST005.php:13`)
- **Tabelas gravadas pelos métodos chamados:** `T015` (EST005::campoCancelar, EST005::campoExcluir, EST005::gerarContasAPagar), `T084` (EST005::atualizarAliquotasConhecimento, EST005::campoCancelar, EST005::campoExcluir), `T085` (EST005::campoExcluir), `T154` (EST004::pagamentoNota)
- **Chaves C031 lidas (2):** `contasPagarUnificado`, `exibirTitulosPagarAgrupados`
- **Mensagens e validações visíveis (9):**
  - "Deseja gerar os títulos referentes as contas a pagar?" (`prog/est/est005/form.php:668`)
  - "Deseja atualizar alíquotas?" (`prog/est/est005/form.php:689`)
  - "' . $retorno . '" (`prog/est/est005/form.php:217`)
  - "Conhecimento importado com Sucesso!" (`prog/est/est005/form.php:220`)
  - "Parcelas cadastradas com sucesso." (`prog/est/est005/form.php:348`)
  - "Não é possível finalizar o conhecimento, pois existem notas relacionadas que não foram lançadas." (`prog/est/est005/form.php:527`)
  - "Registro Alterado com Sucesso!" (`prog/est/est005/form.php:532`)
  - "Este conhecimento já foi lançado! Favor verificar." (`prog/est/est005/form.php:556`)
  - … mais 1
- **Abre as telas:** `est/est004`, `fin/fin002`, `est/est020`
- **Grafo (Graphify):** 18 nós, 9 funções definidas: `validaCampoCancelar()`, `validaCampoExcluir()`, `validaCampoXMLConhecimento()`, `validaNotasLancadas()`, `verificaTomadora()`, `est005CampoCancelar()` …
- **Aberta por:** `crm/crm002`, `erp/erp002`, `est/est004`, `srm/srm001`

### `prog/est/est006` — Ordem de Compra

- **Menu:** Estoque › Ordem de Compra, acesso C029 nº 23 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:197`)
- **Arquivos:** 10 arquivos. 2.694 linhas. `form.php` (1230), `grid.php` (607), `content.php` (296), `form_func-ajax.php` (169), `tab.php` (117), `outro.php` (86), `grid_func-ajax.php` (63), `grid_func-callback.php` (63), `form_func-js.php` (40), `grid_func-js.php` (23)
- **Abas (tab.php):** Ordens de compra (`tab.php:23`), Cadastro (`tab.php:41`), Produtos (`tab.php:70`), Aprovação (`tab.php:71`), E-mail (`tab.php:72`), Totais (`tab.php:73`), Cabeçalho (`tab.php:74`), Pagamento (`tab.php:75`), Contatos (`tab.php:76`), Agregados (`tab.php:106`)
- **Ações (`case` do roteador, 38):** `est006ContentPrincipal`, `est006ContentIncluirOrdemCompra`, `est006ContentAbreEditarOrdemCompra`, `est006ContentEditarOrdemCompra`, `est006ContentAplicarDesconto`, `est006ContentAplicarICMS`, `est006ContentEmail`, `est006ContentPagamentoCotacao`, `est006AgregadosOrdemCompraTab`, `est006AgregadosOrdemCompra`, `est006FormIncluirOrdemCompra`, `est006FormAprovacao`, `est006FormEditarOrdemCompra`, `est006FormAplicarDesconto`, `est006FormAplicarIcms`, `est006FormTotalOrdemCompra`, `est006FormPagamento`, `est006FormAgregados`, `atualizarCamposProdutos`, `aplicarIPICadastro` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T011` (fotografia:17178): DELETE/form em `prog/est/est006/form.php:37`, `prog/est/est006/form.php:260` e mais 5
  - `T012` (fotografia:17251): DELETE/UPDATE/form em `prog/est/est006/form.php:151`, `prog/est/est006/form.php:404` e mais 1
  - `T210` (fotografia:24327): DELETE/form em `prog/est/est006/form.php:1122`, `prog/est/est006/grid_func-ajax.php:52`
- **Tabelas só lidas na tela (17):** `D024` (5×, `prog/est/est006/form.php:77` e mais 4), `D009` (5×, `prog/est/est006/form.php:396` e mais 4), `D049` (5×, `prog/est/est006/form.php:397` e mais 4), `D082` (5×, `prog/est/est006/form.php:398` e mais 4), `D001` (4×, `prog/est/est006/form.php:399` e mais 3), `C007` (4×, `prog/est/est006/grid.php:47` e mais 3), `D020` (2×, `prog/est/est006/form.php:50` e mais 1), `D018` (2×, `prog/est/est006/form.php:51` e mais 1), `D037` (2×, `prog/est/est006/form.php:420` e mais 1), `C004` (1×, `prog/est/est006/grid.php:48`), `T006` (1×, `prog/est/est006/grid.php:170`), `T059` (1×, `prog/est/est006/grid.php:171`), `T139` (1×, `prog/est/est006/grid.php:241`), `T190` (1×, `prog/est/est006/grid.php:356`), `D031` (1×, `prog/est/est006/grid.php:359`), `D013` (1×, `prog/est/est006/grid.php:481`), `T209` (1×, `prog/est/est006/grid.php:561`)
- **Classes e métodos chamados:** `EST006` (1804 linhas): `insereItemEstoque` (`EST006.php:32`), `insereItemEmergencia` (`EST006.php:194`), `insereItemEstoqueRequisicao` (`EST006.php:85`), `insereItemEstoqueRequisicaoTabCotacao` (`EST006.php:139`), `insereItem` (`EST006.php:13`), `aplicarDesconto` (`EST006.php:255`), `aplicarICMS` (`EST006.php:284`), `calcularParcelaCombo` (`EST006.php:945`), `atualizarParcelas` (`EST006.php:1092`), `aplicarIPICad` (`EST006.php:305`) …
- **Tabelas gravadas pelos métodos chamados:** `T012` (EST006::aplicarDesconto, EST006::aplicarICMS, EST006::aplicarIPICad…), `T013` (EST006::gerarNotaFiscalOC), `T014` (EST006::gerarNotaFiscalOC), `T154` (EST006::gerarNotaFiscalOC), `T174` (EST006::insereItemEstoqueRequisicaoTabCotacao), `T189` (EST006::insereItemEstoqueRequisicao), `T190` (EST006::EST006AtualizarParcelas, EST006::atualizarParcelas, EST006::inserirParcela)
- **Mensagens e validações visíveis (6):**
  - "Registro Alterado com Sucesso!" (`prog/est/est006/form.php:154`)
  - "Registro Incluido com Sucesso!" (`prog/est/est006/form.php:182`)
  - "Status Alterado com Sucesso!" (`prog/est/est006/form.php:312`)
  - "Desconto Aplicado com sucesso!" (`prog/est/est006/form.php:588`)
  - "ICMS Aplicado com sucesso!" (`prog/est/est006/form.php:694`)
  - "Parcelas cadastradas com sucesso." (`prog/est/est006/form.php:1060`)
- **Abre as telas:** `etc/etc006`, `est/est004`, `cad/cad001`
- **Grafo (Graphify):** 18 nós, 8 funções definidas: `campoExclusao()`, `est006_Dia_Semana()`, `T190_D031_Id_Portador()`, `validaCampoPendente()`, `validaCampoStatus()`, `campoProcuraProduto()` …
- **Aberta por:** `crm/crm002`, `erp/erp002`, `est/est004`, `est/est008`, `est/est009`, `est/est010`, `est/est014`, `est/est019`, `srm/srm001`

**Leitura do código (manual):**

- **Fora do porte (25/11/2026):** fluxo antigo de compras. A Fixar usa o fluxo novo (`est015`, `est016`, `est019`).

### `prog/est/est007` — Montagens/Desmontagens

- **Menu:** Estoque › Montagens/Desmontagens, acesso C029 nº 24 "Montagens/Desmontagens" (item comentado no Menu) (`bibliotecas/menu.php:191`)
- **Arquivos:** 8 arquivos. 1.363 linhas. `form.php` (446), `grid.php` (405), `content.php` (149), `grid_func-callback.php` (132), `tab.php` (117), `form_func-ajax.php` (52), `form_func-js.php` (35), `grid_func-ajax.php` (27)
- **Abas (tab.php):** Geral (`tab.php:18`), Operação (`tab.php:39`), Cabeçalho (`tab.php:40`), Geral (`tab.php:61`), Itens (`tab.php:82`), Sub-Itens (`tab.php:103`)
- **Ações (`case` do roteador, 21):** `est007ContentMontagens`, `est007ContentMontagensIncluir`, `est007ContentMontagensEditarNovo`, `est007ContentMontagensEditar`, `est007ContentMontagensIncluirSubItem`, `est007FormCadastroMontagens`, `est007TabMontagensFormIncluirProduto`, `est007TabMontagensFormIncluirSubItem`, `est007atualizarCamposProdutos`, `est007atualizarCamposProdutosSubItens`, `est007ProcessarMontagem`, `est007GridMontagens`, `est007FormEditarMontagens`, `est007FormEditarMontagensSubItens`, `atualizaT048Local`, `atualizaT071Local`, `est007TabMontagens`, `est007TabMontagensEditarNovo`, `est007TabMontagensIncluir`, `est007TabMontagensEditar` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T047` (fotografia:18934): UPDATE/form em `prog/est/est007/form.php:30`, `prog/est/est007/grid.php:15`
  - `T048` (fotografia:18960): UPDATE em `prog/est/est007/grid_func-ajax.php:12`
  - `T071` (fotografia:20022): UPDATE em `prog/est/est007/grid_func-ajax.php:21`
- **Tabelas só lidas na tela (14):** `D004` (6×, `prog/est/est007/form.php:190` e mais 5), `D049` (4×, `prog/est/est007/form.php:182` e mais 3), `D082` (4×, `prog/est/est007/form.php:185` e mais 3), `T066` (4×, `prog/est/est007/form.php:189` e mais 3), `D009` (3×, `prog/est/est007/form.php:184` e mais 2), `D001` (3×, `prog/est/est007/form.php:358` e mais 2), `C007` (2×, `prog/est/est007/form.php:192` e mais 1), `T066A` (2×, `prog/est/est007/grid_func-callback.php:44` e mais 1), `D041` (1×, `prog/est/est007/form.php:183`), `D037` (1×, `prog/est/est007/form.php:186`), `D060` (1×, `prog/est/est007/form.php:187`), `C008` (1×, `prog/est/est007/form.php:188`), `D144` (1×, `prog/est/est007/form.php:191`), `D146` (1×, `prog/est/est007/form.php:212`)
- **Classes e métodos chamados:** `EST007` (1239 linhas): `est007gravarProduto` (`EST007.php:15`), `est007gravarProdutoSubItens` (`EST007.php:162`), `est007ProcessarMontagem` (`EST007.php:220`)
- **Tabelas gravadas pelos métodos chamados:** `T001` (EST007::est007ProcessarMontagem), `T047` (EST007::est007ProcessarMontagem), `T048` (EST007::est007gravarProduto), `T071` (EST007::est007gravarProduto, EST007::est007gravarProdutoSubItens)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Quantidade_Montagem` (`prog/est/est007/grid.php:164`)
- **Mensagens e validações visíveis (2):**
  - "Registro Incluido com Sucesso!" (`prog/est/est007/form.php:90`)
  - "Processo concluído com sucesso!" (`prog/est/est007/form.php:289`)
- **Abre as telas:** `ven/ven001`, `cad/cad002`, `est/est003`
- **Grafo (Graphify):** 18 nós, 10 funções definidas: `atualizaT048Local()`, `atualizaT071Local()`, `campoTipoMontagem()`, `T048_T066_Id_Local()`, `T071_T066_Id_Local()`, `validaCampoExcluirItens()` …
- **Aberta por:** `cad/cad002`, `est/est009`, `est/est019`

### `prog/est/est008` — Cotação de Compra

- **Menu:** Estoque › Cotação de Compra, acesso C029 nº 88 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:196`)
- **Arquivos:** 13 arquivos. 7.809 linhas. `form.php` (1483), `grid.php` (1479), `grid_func-callback.php` (820), `outros.php` (818), `cotacaoViaEmail.php` (702), `cotacaoViaEmail2.php` (680), `grid_func-ajax.php` (546), `content.php` (450), `tab.php` (318), `form_func-ajax.php` (227), `gravar_campo_editado_online.php` (129), `form_func-js.php` (84) …
- **Abas (tab.php):** Cotação de Compra (`tab.php:23`), Produtos (`tab.php:24`), Cabeçalho (`tab.php:43`), Fornecedores (`tab.php:48`), Produtos (`tab.php:49`), Preços (`tab.php:50`), Cabeçalho (`tab.php:51`), E-mail (`tab.php:52`), Gerar OC (`tab.php:70`), Fornecedores OC (`tab.php:87`), Produtos (`tab.php:104`), Contatos (`tab.php:105`) …
- **Ações (`case` do roteador, 76):** `est008ContentPrincipal`, `est008contentCadastroCotacaoCompra`, `est008ContentProdutos`, `est008ContentFornecedores`, `est008ContentGerarOC`, `est008ContentFornecedoresOC`, `est008ContentFornecedoresOCForn`, `est008contentCadastroCotacaoCompraNew`, `est006ContentIncluirOrdemCompra`, `est008ContentCadastroCotacaoCompra`, `est008ContentInserirMarca`, `est008ContentResumoCotacao`, `est008ContentResumo`, `est008ContentRetornarProdForn`, `est008ContentIncluirProdForn`, `est008ContentPreCadastrarProduto`, `est008ContentSelecionarEmpresa`, `est008ContentEditarProdutoFornecedor`, `est008FormCadastroCotacaoCompra`, `est008FormProdutos` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D001` (fotografia:2944): form em `prog/est/est008/form.php:838`
  - `D049` (fotografia:5460): INSERT/UPDATE em `prog/est/est008/form.php:961`, `prog/est/est008/gravar_campo_editado_online.php:111`
  - `D129` (fotografia:7638): form em `prog/est/est008/form.php:1087`
  - `T010` (fotografia:17049): UPDATE/form em `prog/est/est008/form.php:46`, `prog/est/est008/gravar_campo_editado_online.php:81`
  - `T014` (fotografia:17524): form em `prog/est/est008/form.php:677`
  - `T059` (fotografia:19613): INSERT/UPDATE/form em `prog/est/est008/form.php:225`, `prog/est/est008/form.php:967` e mais 4
  - `T173` (fotografia:23333): UPDATE/form em `prog/est/est008/form.php:379`, `prog/est/est008/gravar_campo_editado_online.php:88` e mais 3
  - `T174` (fotografia:23369): INSERT/UPDATE em `prog/est/est008/form.php:970`, `prog/est/est008/gravar_campo_editado_online.php:26` e mais 16
  - `T189` (fotografia:23794): UPDATE em `prog/est/est008/grid_func-ajax.php:513`
  - `T232` (fotografia:25930): INSERT/UPDATE em `prog/est/est008/grid_func-ajax.php:537`, `prog/est/est008/grid_func-ajax.php:540`
- **Tabelas só lidas na tela (22):** `D009` (33×, `prog/est/est008/cotacaoViaEmail.php:211` e mais 32), `D024` (31×, `prog/est/est008/cotacaoViaEmail.php:37` e mais 30), `D082` (29×, `prog/est/est008/cotacaoViaEmail.php:216` e mais 28), `C007` (11×, `prog/est/est008/cotacaoViaEmail.php:40` e mais 10), `D037` (8×, `prog/est/est008/cotacaoViaEmail.php:215` e mais 7), `C008` (5×, `prog/est/est008/cotacaoViaEmail.php:217` e mais 4), `D083` (4×, `prog/est/est008/cotacaoViaEmail.php:213` e mais 3), `D020` (4×, `prog/est/est008/cotacaoViaEmail.php:314` e mais 3), `D018` (4×, `prog/est/est008/cotacaoViaEmail.php:315` e mais 3), `T013` (4×, `prog/est/est008/grid_func-callback.php:223` e mais 3), `T011` (4×, `prog/est/est008/grid_func-callback.php:314` e mais 3), `C004` (3×, `prog/est/est008/cotacaoViaEmail.php:24` e mais 2), `D036` (3×, `prog/est/est008/cotacaoViaEmail.php:39` e mais 2), `T012` (3×, `prog/est/est008/cotacaoViaEmail.php:223` e mais 2), `D030` (3×, `prog/est/est008/cotacaoViaEmail.php:316` e mais 2), `D013` (1×, `prog/est/est008/grid.php:1417`), `T139` (1×, `prog/est/est008/grid_func-callback.php:363`), `T006` (1×, `prog/est/est008/grid_func-callback.php:673`), `D003` (1×, `prog/est/est008/outros.php:759`), `D060` (1×, `prog/est/est008/outros.php:760`), `D002` (1×, `prog/est/est008/outros.php:761`), `D015` (1×, `prog/est/est008/outros.php:762`)
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `preCadastroMarca` (`CAD002.php:1105`); `EST008` (1979 linhas): `insereItemEstoque` (`EST008.php:66`), `insereItemEmergencia` (`EST008.php:222`), `insereItemEstoqueRequisicao` (`EST008.php:122`), `insereItem` (`EST008.php:13`), `verificaFornecedor` (`EST008.php:1770`), `est008_alterar_marca` (`EST008.php:1747`), `alteradoProduto` (`EST008.php:1856`), `EST008montarEmailPdf` (`EST008.php:845`), `EST008montarEmailPdfDemonstrativo` (`EST008.php:1247`), `incluirProdutoCotacaoForn` (`EST008.php:1802`) …
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::preCadastroMarca), `D041` (CAD002::preCadastroMarca), `T010` (EST008::updateT010_Flag_Marca_Individual), `T011` (EST008::geraOCEmpresa, EST008::geraOCMarcados), `T012` (EST008::geraOCEmpresa, EST008::geraOCMarcados), `T059` (EST008::alteradoProduto, EST008::incluirProdutoCotacaoForn, EST008::insereItemEmergencia…), `T173` (EST008::enviarParaAnalise), `T174` (EST008::alteradoProduto, EST008::est008_alterar_marca, EST008::incluirProdutoCotacaoForn…), `T189` (EST008::geraOCEmpresa, EST008::insereItemEstoqueRequisicao, EST008::insereRegistrosT174)
- **Chaves C031 lidas (1):** `precoTabelado`
- **Mensagens e validações visíveis (7):**
  - "A geração desta OC exige maior alçada, pedir liberação?(1)" (`prog/est/est008/form.php:1186`)
  - "A geração desta OC exige maior alçada, pedir liberação?(2)" (`prog/est/est008/form.php:1207`)
  - "Registro Alterado com Sucesso!" (`prog/est/est008/form.php:116`)
  - "Registro Incluido com Sucesso!" (`prog/est/est008/form.php:139`)
  - "Produto associado com sucesso!." (`prog/est/est008/form.php:611`)
  - "{$return}" (`prog/est/est008/form.php:973`)
  - "Erro ao editar produto!" (`prog/est/est008/form.php:1398`)
- **Abre as telas:** `etc/etc006`, `cad/cad002`, `est/est006`, `cad/cad001`
- **Grafo (Graphify):** 80 nós, 67 funções definidas: `buscaMarca()`, `calculaFrete()`, `calculaItens()`, `campoCodFornecedor()`, `campoeMail()`, `campoeMail2()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `crm/crm002`, `erp/erp002`, `est/est004`, `est/est009`, `est/est010`, `est/est012`, `est/est014`, `est/est019`, `imp/imp003`, `ven/ven001`

**Leitura do código (manual):**

- **Fora do porte (25/11/2026):** fluxo antigo de compras (cotação em T010). A Fixar usa o fluxo novo.

### `prog/est/est009` — Compras Estoque/Emergência

- **Menu:** Estoque › Compras Estoque/Emergência, acesso C029 nº 94 "Compras de Estoque" (item comentado no Menu) (`bibliotecas/menu.php:194`)
- **Arquivos:** 7 arquivos. 6.389 linhas. `grid.php` (4387), `grid_func-ajax.php` (1269), `tab.php` (244), `content.php` (203), `form.php` (143), `grid_func-callback.php` (105), `grid_func-js.php` (38)
- **Abas (tab.php):** Compras de Estoque (`tab.php:17`), Cotação de Emergência (`tab.php:18`), Compras de Emergência (`tab.php:19`), Geral (`tab.php:43`), Geral (`tab.php:63`), Cotação de Emergência (`tab.php:83`), Cotar (`tab.php:102`), Comprar (`tab.php:103`), Montagens de Emergência (`tab.php:122`), Serviços de Emergência (`tab.php:141`), Produções de Emergência (`tab.php:160`), Previsão DI (`tab.php:176`) …
- **Ações (`case` do roteador, 77):** `est009ContentComprasDeEstoque`, `est009GridComprasEstoqueLinha`, `cad046ContentMotivosCancelamentoIncluir`, `est009ContentCotacaoEmergencia`, `est009ContentComprasEmergencia`, `est009ContentMontagemEmergencia`, `est009ContentServicosEmergencia`, `est009ContentProducoesEmergencia`, `est009ContentPrevisaoDI`, `est009ContentProdutosEstoqueVazio`, `est009ContentprodutosEstoqueEmergencia`, `est009ContentProdutosHistoricoSemLocacao`, `cad046FormCadastroMotivosCancelamento`, `est009GridComprasEstoque`, `est009GridComprasEstoqueEmpresas`, `est009GridCotacaoEmergencia`, `est009GridComprasEmergencia`, `est009GridComprasEmergenciaComprar`, `est009GridComprasEmergenciaCotando`, `est009GridMontagensEmergencia` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D009` (fotografia:3608): UPDATE em `prog/est/est009/grid_func-ajax.php:303`, `prog/est/est009/grid_func-ajax.php:331` e mais 4
  - `D047` (fotografia:5424): form em `prog/est/est009/form.php:27`
  - `T004` (fotografia:14902): UPDATE em `prog/est/est009/grid_func-ajax.php:514`
  - `T006` (fotografia:15620): UPDATE em `prog/est/est009/grid_func-ajax.php:521`
- **Tabelas só lidas na tela (42):** `C007` (49×, `prog/est/est009/grid.php:144` e mais 48), `D024` (43×, `prog/est/est009/grid.php:143` e mais 42), `D001` (36×, `prog/est/est009/grid.php:133` e mais 35), `D049` (35×, `prog/est/est009/grid.php:132` e mais 34), `D082` (29×, `prog/est/est009/grid.php:134` e mais 28), `D037` (26×, `prog/est/est009/grid.php:136` e mais 25), `T219` (21×, `prog/est/est009/grid.php:1164` e mais 20), `D006` (19×, `prog/est/est009/grid.php:1159` e mais 18), `T218` (18×, `prog/est/est009/grid.php:1572` e mais 17), `D005` (16×, `prog/est/est009/grid.php:1158` e mais 15), `T005` (16×, `prog/est/est009/grid.php:1161` e mais 15), `T241` (14×, `prog/est/est009/grid.php:147` e mais 13), `T225` (14×, `prog/est/est009/grid.php:1574` e mais 13), `T224` (13×, `prog/est/est009/grid.php:1573` e mais 12), `C008` (12×, `prog/est/est009/grid.php:137` e mais 11), `T003` (11×, `prog/est/est009/grid.php:1162` e mais 10), `D003` (10×, `prog/est/est009/grid.php:138` e mais 9), `D060` (10×, `prog/est/est009/grid.php:139` e mais 9), `D002` (10×, `prog/est/est009/grid.php:140` e mais 9), `D015` (10×, `prog/est/est009/grid.php:141` e mais 9), `T001` (10×, `prog/est/est009/grid.php:142` e mais 9), `T187` (8×, `prog/est/est009/grid.php:131` e mais 7), `D083` (8×, `prog/est/est009/grid.php:135` e mais 7), `T005A` (6×, `prog/est/est009/grid.php:2090` e mais 5), `C004` (4×, `prog/est/est009/grid.php:149` e mais 3) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `D001_reprocessa_historico` (`CAD002.php:1159`), `atualizarEstatisticasCompra` (`CAD002.php:7564`); `EST007` (1239 linhas): `est007GerarMontagemEmergencia` (`EST007.php:1030`), `est007GerarMontagemEstoque` (`EST007.php:1128`); `EST009` (337 linhas): `updateCheckboxGerarCotar` (`EST009.php:12`), `updateCheckboxGerarComprar` (`EST009.php:28`), `updateCheckboxGerarNaoComprar` (`EST009.php:44`), `est009RecuperarEstoqueLoja` (`EST009.php:55`); `EST015` (9926 linhas): `est015atualizarCFOP` (`EST015.php:7367`), `atualizarAliquotas` (`EST015.php:2261`), `insereItemCotacaoEmergencia` (`EST015.php:8129`), `insereItemEstoque` (`EST015.php:7846`), `insereItemCotacaoPedidoEmergencia` (`EST015.php:8295`), `gerarCotacaoAutomatica` (`EST015.php:8710`), `gerarCotacaoPedidoAutomatica` (`EST015.php:8916`), `gerarPedido` (`EST015.php:1164`), `gerarCotacaoEstoqueAutomatica` (`EST015.php:9187`), `adicionarProdutosOrdemCompra` (`EST015.php:9535`)
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::D001_reprocessa_historico, CAD002::atualizarEstatisticasCompra, EST007::est007GerarMontagemEstoque…), `D009A` (CAD002::D001_reprocessa_historico, CAD002::atualizarEstatisticasCompra), `D049` (EST009::updateCheckboxGerarNaoComprar), `T001` (CAD002::D001_reprocessa_historico, EST009::est009RecuperarEstoqueLoja), `T004` (EST015::insereItemCotacaoEmergencia), `T006` (EST009::est009RecuperarEstoqueLoja), `T047` (EST007::est007GerarMontagemEmergencia, EST007::est007GerarMontagemEstoque), `T048` (EST007::est007GerarMontagemEmergencia, EST007::est007GerarMontagemEstoque), `T066` (CAD002::D001_reprocessa_historico), `T071` (EST007::est007GerarMontagemEmergencia, EST007::est007GerarMontagemEstoque), `T148` (EST015::gerarPedido), `T149` (EST015::gerarPedido), `T186` (EST009::est009RecuperarEstoqueLoja), `T204` (EST015::gerarPedido), `T218` (EST015::atualizarAliquotas, EST015::est015atualizarCFOP, EST015::gerarCotacaoAutomatica…) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Compra_Emergencia` (`prog/est/est009/grid.php:190` e mais 1), `D009_Quantidade_Estoque_Tabela` (`prog/est/est009/grid.php:1127` e mais 5), `D009_Quantidade_MP` (`prog/est/est009/grid.php:4060`), `T005_Status_Pedido` (`prog/est/est009/grid.php:4328`)
- **Chaves C031 lidas (3):** `SeparaProdutoPorEmpresa`, `avisarQtaCompradaMaiorNecessario`, `calculaEstoqueMinimoMaximoProduto`
- **Mensagens e validações visíveis (2):**
  - "Registro Incluido com Sucesso!" (`prog/est/est009/form.php:83`)
  - "Informe um número de cotação" (`prog/est/est009/grid.php:514`)
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::cotacaoEmergencia` (`NOTIFICACOES.php:1023`, clique na linha 1087; lê `C004`, `C007`, `D001`, `D005`, `D006`, `D009`); `NOTIFICACOES::cotacaoEmergencia` (`NOTIFICACOES.php:1023`, clique na linha 1115; lê `C004`, `C007`, `D001`, `D005`, `D006`, `D009`); `NOTIFICACOES::comprasEmergencia` (`NOTIFICACOES.php:1690`, clique na linha 1759; lê `C007`, `D001`, `D005`, `D006`, `D009`, `D024`); `NOTIFICACOES::comprasEmergencia` (`NOTIFICACOES.php:1690`, clique na linha 1784; lê `C007`, `D001`, `D005`, `D006`, `D009`, `D024`); `NOTIFICACOES::montagensEmergencia` (`NOTIFICACOES.php:1802`, clique na linha 1834; lê `C007`, `D009`, `D049`, `N001`, `T004`, `T005`); `NOTIFICACOES::montagensEmergencia` (`NOTIFICACOES.php:1802`, clique na linha 1860; lê `C007`, `D009`, `D049`, `N001`, `T004`, `T005`); `NOTIFICACOES::servicosEmergencia` (`NOTIFICACOES.php:1878`, clique na linha 1909; lê `C007`, `D009`, `D049`, `N001`, `T004`, `T005`); `NOTIFICACOES::servicosEmergencia` (`NOTIFICACOES.php:1878`, clique na linha 1937; lê `C007`, `D009`, `D049`, `N001`, `T004`, `T005`); `NOTIFICACOES::produtosEstoqueVazio` (`NOTIFICACOES.php:2295`, clique na linha 2319; lê `C004`, `D001`, `D009`, `D049`); `NOTIFICACOES::produtosEstoqueEmergencia` (`NOTIFICACOES.php:2339`, clique na linha 2363; lê `C004`, `D001`, `D009`, `D049`); `NOTIFICACOES::historicoSemLocacao` (`NOTIFICACOES.php:2383`, clique na linha 2413; lê `C004`, `D001`, `D009`, `D049`, `T001`)
- **Abre as telas:** `cad/cad002`, `est/est015`, `ven/ven001`, `est/est016`, `ven/ven002`, `est/est007`, `cad/cad046`, `est/est006`, `est/est008`, `crm/crm001`
- **Grafo (Graphify):** 25 nós, 18 funções definidas: `est009campoConsumido()`, `est009DescricaoProduto()`, `est009VendaPerdida()`, `mediaMMF()`, `validaCampoCLientes()`, `validaCampoComprar()` …
- **Aberta por:** `est/est016`

### `prog/est/est010` — Compras de Emergência

- **Menu:** Estoque › Compras de Emergência, acesso C029 nº 95 "Compras de Emergência" (item comentado no Menu) (`bibliotecas/menu.php:195`)
- **Arquivos:** 7 arquivos. 820 linhas. `grid.php` (283), `grid_func-ajax.php` (159), `form.php` (142), `grid_func-callback.php` (114), `tab.php` (53), `content.php` (43), `grid_func-js.php` (26)
- **Abas (tab.php):** Compras de Emergência (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 12):** `est010ContentComprasDeEmergencia`, `cad046ContentMotivosCancelamentoIncluir`, `cad046FormCadastroMotivosCancelamento`, `est010GridComprasEmergencia`, `est010CheckboxCotar`, `est010CheckboxComprar`, `est010flagCotar`, `est010FlagDesmarcarCotar`, `est010GerarOC`, `est010GerarCotacao`, `est010TabComprasEmergencia`, `cad046TabCadastroMotivosCancelamento`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D047` (fotografia:5424): form em `prog/est/est010/form.php:27`
  - `T006` (fotografia:15620): UPDATE em `prog/est/est010/grid_func-ajax.php:48`, `prog/est/est010/grid_func-ajax.php:76`
- **Tabelas só lidas na tela (22):** `T005` (3×, `prog/est/est010/grid.php:62` e mais 2), `D024` (3×, `prog/est/est010/grid.php:63` e mais 2), `T012` (3×, `prog/est/est010/grid.php:74` e mais 2), `T059` (3×, `prog/est/est010/grid.php:75` e mais 2), `T129` (3×, `prog/est/est010/grid.php:76` e mais 2), `T008` (3×, `prog/est/est010/grid.php:79` e mais 2), `T007` (3×, `prog/est/est010/grid.php:80` e mais 2), `C007` (2×, `prog/est/est010/grid.php:64` e mais 1), `D009` (2×, `prog/est/est010/grid.php:66` e mais 1), `D049` (2×, `prog/est/est010/grid.php:67` e mais 1), `D083` (2×, `prog/est/est010/grid_func-callback.php:32` e mais 1), `D069` (2×, `prog/est/est010/grid_func-callback.php:33` e mais 1), `D070` (2×, `prog/est/est010/grid_func-callback.php:34` e mais 1), `D001` (1×, `prog/est/est010/grid.php:68`), `D082` (1×, `prog/est/est010/grid.php:69`), `D003` (1×, `prog/est/est010/grid.php:70`), `D060` (1×, `prog/est/est010/grid.php:71`), `D002` (1×, `prog/est/est010/grid.php:72`), `D015` (1×, `prog/est/est010/grid.php:73`), `D037` (1×, `prog/est/est010/grid.php:77`), `C008` (1×, `prog/est/est010/grid.php:78`), `T001` (1×, `prog/est/est010/grid_func-callback.php:17`)
- **Classes e métodos chamados:** `EST010` (39 linhas): `updateCheckboxGerarCotar` (`EST010.php:13`), `updateCheckboxGerarComprar` (`EST010.php:29`)
- **Tabelas gravadas pelos métodos chamados:** `T006` (EST010::updateCheckboxGerarComprar, EST010::updateCheckboxGerarCotar)
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/est/est010/form.php:83`)
- **Abre as telas:** `est/est006`, `est/est008`
- **Grafo (Graphify):** 12 nós, 5 funções definidas: `est010CheckboxComprar()`, `est010CheckboxCotar()`, `campoCustoFornecedor()`, `campoPrecoUltimaCompra()`, `validaCampoComprar()`. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `est/est016`

**Leitura do código (manual):**

- **A conferir (25/11/2026):** só lê T012, do fluxo antigo. **Hipótese:** sai do porte junto com ele. Confirmar com compras.

### `prog/est/est011` — RMA

- **Menu:** Estoque › RMA, acesso C029 nº 98 (ausente da C029 na fotografia) (`bibliotecas/menu.php:32`)
- **Arquivos:** 9 arquivos. 1.926 linhas. `form.php` (909), `grid.php` (492), `content.php` (137), `tab.php` (104), `form_func-js.php` (84), `grid_func-callback.php` (74), `form_func-ajax.php` (73), `grid_func-ajax.php` (38), `grid_func-js.php` (15)
- **Abas (tab.php):** Controle (`tab.php:18`), Gerar nota devolução (Origem emitidas) (`tab.php:19`), Gerar nota de devolução (Origem recebidas) (`tab.php:20`), Produto (`tab.php:42`), Atividades (`tab.php:46`), Documentos (`tab.php:47`), Gerar (`tab.php:68`), Cabeçalho (`tab.php:89`), Produtos (`tab.php:90`)
- **Ações (`case` do roteador, 22):** `est011ContentGarantiaConserto`, `est011ContentIncluirProduto`, `est011ContentDocumentos`, `est011ContentGerarRemessa`, `est011ContentEditarRemessa`, `est011FormIncluirProduto`, `est011FormGerarRemessa`, `est011FormCabecalhoRemessa`, `est011FormDocumentos`, `atualizarCamposProdutos`, `carregaContatoGarantiaCliente`, `carregaContatoGarantiaFornecedor`, `est011GridPrincipalProdutos`, `est011GridPrincipalRemessas`, `est011gridItensRemessa`, `est011GridDocumentos`, `est011CheckboxMarcado`, `abreDocumentoRMA`, `est011TabPrincipal`, `est011TabIncluirProduto` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T182` (fotografia:23565): UPDATE/form em `prog/est/est011/form.php:34`, `prog/est/est011/grid_func-ajax.php:12`
  - `T183` (fotografia:23642): form em `prog/est/est011/form.php:499`, `prog/est/est011/form.php:639`
  - `T271` (fotografia:27081): form em `prog/est/est011/form.php:764`
- **Tabelas só lidas na tela (18):** `D024` (8×, `prog/est/est011/form.php:355` e mais 7), `D049` (5×, `prog/est/est011/form.php:46` e mais 4), `D009` (5×, `prog/est/est011/form.php:47` e mais 4), `D001` (4×, `prog/est/est011/form.php:78` e mais 3), `D082` (3×, `prog/est/est011/form.php:48` e mais 2), `D018` (3×, `prog/est/est011/form.php:102` e mais 2), `T007` (3×, `prog/est/est011/form.php:357` e mais 2), `C007` (3×, `prog/est/est011/grid.php:62` e mais 2), `T184` (3×, `prog/est/est011/grid.php:313` e mais 2), `D020` (2×, `prog/est/est011/form.php:102` e mais 1), `D013` (2×, `prog/est/est011/grid.php:70` e mais 1), `D037` (1×, `prog/est/est011/form.php:49`), `D083` (1×, `prog/est/est011/form_func-ajax.php:20`), `D041` (1×, `prog/est/est011/form_func-ajax.php:23`), `D005` (1×, `prog/est/est011/form_func-ajax.php:24`), `D162` (1×, `prog/est/est011/grid.php:63`), `D164` (1×, `prog/est/est011/grid.php:64`), `D022` (1×, `prog/est/est011/grid.php:67`)
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída); `EST011` (119 linhas): `gerarRemessa` (`EST011.php:12`)
- **Tabelas gravadas pelos métodos chamados:** `T182` (EST011::gerarRemessa), `T183` (EST011::gerarRemessa), `T184` (EST011::gerarRemessa)
- **Mensagens e validações visíveis (4):**
  - "Selecione o Cliente!" (`prog/est/est011/form.php:317`)
  - "Registro Alterado com Sucesso!" (`prog/est/est011/form.php:380`)
  - "Remessa Gerada com Sucesso!" (`prog/est/est011/form.php:570`)
  - "Registro Incluido com Sucesso!" (`prog/est/est011/form.php:698`)
- **Abre as telas:** `cad/cad001`, `etc/etc017`
- **Grafo (Graphify):** 22 nós, 13 funções definidas: `campoAbrirDocumentoRMA()`, `retornaFornecedorRemessa()`, `retornaIdRemessa()`, `retornaStatus()`, `retornaTipo()`, `retornaTipoItemRemessa()` …

### `prog/est/est012` — Análise de Cotação

- **Menu:** Estoque › Análise de Cotação, acesso C029 nº 100 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:198`)
- **Arquivos:** 7 arquivos. 440 linhas. `form.php` (129), `grid.php` (112), `tab.php` (54), `content.php` (45), `grid_func-callback.php` (44), `grid_func-ajax.php` (32), `grid_func-js.php` (24)
- **Abas (tab.php):** Análise de Cotação (`tab.php:21`), Motivo (`tab.php:42`)
- **Ações (`case` do roteador, 7):** `est012ContentPrincipal`, `est012ContentNaoLiberar`, `est012FormNaoLiberar`, `est012GridPrincipal`, `est012AprovarCotacao`, `est012TabPrincipal`, `est012TabNaoLiberar`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T010` (fotografia:17049): form em `prog/est/est012/form.php:36`
- **Tabelas só lidas na tela (5):** `T173` (2×, `prog/est/est012/grid.php:41` e mais 1), `D024` (2×, `prog/est/est012/grid.php:44` e mais 1), `T059` (1×, `prog/est/est012/grid.php:33`), `C007` (1×, `prog/est/est012/grid.php:46`), `T174` (1×, `prog/est/est012/grid_func-callback.php:15`)
- **Classes e métodos chamados:** `EST012` (51 linhas): `reprovarCotacao` (`EST012.php:34`), `est012AprovarCotacao` (`EST012.php:12`)
- **Tabelas gravadas pelos métodos chamados:** `T173` (EST012::est012AprovarCotacao, EST012::reprovarCotacao)
- **Mensagens e validações visíveis (2):**
  - "Cotação negada com Sucesso" (`prog/est/est012/form.php:69`)
  - "Cotação aprovada!" (`prog/est/est012/grid_func-js.php:10`)
- **Abre as telas:** `est/est008`
- **Grafo (Graphify):** 12 nós, 5 funções definidas: `campoFornecedor()`, `campoValorCotacao()`, `est012Liberar()`, `est012NaoLiberar()`, `est012AprovarCotacao()`
- **Aberta por:** `est/est016`

**Leitura do código (manual):**

- **Fora do porte (25/11/2026):** análise de cotação do fluxo antigo (T010). No fluxo novo, a análise fica em `est015` e `est016`.

### `prog/est/est013` — Relatórios

- **Menu:** Estoque › Relatórios, acesso C029 nº 101 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:199`)
- **Menu:** Gerencial › Inventário estoque, acesso C029 nº 153 "Inventario estoque" (item comentado no Menu) (`bibliotecas/menu.php:226`)
- **Arquivos:** 9 arquivos. 1.110 linhas. `relatorioInventario.php` (433), `form.php` (272), `grid.php` (250), `content.php` (43), `tab.php` (30), `grid_func-js.php` (26), `help.php` (21), `grid_func-callback.php` (18), `grid_func-ajax.php` (17)
- **Abas (tab.php):** Frequência (`tab.php:17`), Sobras (`tab.php:18`)
- **Ações (`case` do roteador, 9):** `est013ContentRelatorios`, `est013ContentInventario`, `est012FormNaoLiberar`, `est013FormInventario`, `est013GridFrequencia`, `est013GridSobras`, `est013AlterarPeriodo`, `help013RelatorioInventario`, `est013TabPrincipal`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C020` (fotografia:838): UPDATE em `prog/est/est013/grid_func-ajax.php:12`
  - `T010` (fotografia:17049): form em `prog/est/est013/form.php:37`
- **Tabelas só lidas na tela (19):** `T001` (4×, `prog/est/est013/relatorioInventario.php:75` e mais 3), `D009` (3×, `prog/est/est013/grid.php:62` e mais 2), `D049` (3×, `prog/est/est013/grid.php:67` e mais 2), `D001` (3×, `prog/est/est013/grid.php:68` e mais 2), `D024` (3×, `prog/est/est013/grid.php:71` e mais 2), `D082` (2×, `prog/est/est013/grid.php:69` e mais 1), `D037` (2×, `prog/est/est013/grid.php:72` e mais 1), `C008` (2×, `prog/est/est013/grid.php:73` e mais 1), `D003` (2×, `prog/est/est013/grid.php:74` e mais 1), `D060` (2×, `prog/est/est013/grid.php:75` e mais 1), `D002` (2×, `prog/est/est013/grid.php:76` e mais 1), `T066` (2×, `prog/est/est013/grid.php:79` e mais 1), `D004` (2×, `prog/est/est013/grid.php:80` e mais 1), `T187` (1×, `prog/est/est013/grid.php:66`), `D083` (1×, `prog/est/est013/grid.php:70`), `D015` (1×, `prog/est/est013/grid.php:77`), `C004` (1×, `prog/est/est013/relatorioInventario.php:5`), `D005` (1×, `prog/est/est013/relatorioInventario.php:168`), `D041` (1×, `prog/est/est013/relatorioInventario.php:177`)
- **Classes e métodos chamados:** `EST012` (51 linhas): `reprovarCotacao` (`EST012.php:34`)
- **Tabelas gravadas pelos métodos chamados:** `T173` (EST012::reprovarCotacao)
- **Mensagens e validações visíveis (1):**
  - "Cotação negada com Sucesso" (`prog/est/est013/form.php:70`)
- **Abre as telas:** `cad/cad002`
- **Grafo (Graphify):** 16 nós, 7 funções definidas: `agora()`, `Hoje()`, `corrigeNumero()`, `corrigePeso()`, `nrLinha()`, `est009CheckboxComprar()` …. Chamada por: `.finalizarNotaFinanceiro()` (bibliotecas/classes/EST004.php), `.xfinalizarNotaEntrada()` (bibliotecas/classes/EST004.php), `.validacaoNFE3()` (bibliotecas/classes/VEN012.php)

### `prog/est/est014` — Requisitar Compra

- **Menu:** Estoque › Requisitar Compra, acesso C029 nº 145 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:192`)
- **Menu:** Estoque › Requisições de Compra, acesso C029 nº 106 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:193`)
- **Arquivos:** 9 arquivos. 3.392 linhas. `grid.php` (1238), `form.php` (836), `content.php` (298), `grid_func-ajax.php` (256), `grid_func-callback.php` (216), `tab.php` (211), `form_func-ajax.php` (189), `form_func-js.php` (88), `grid_func-js.php` (60)
- **Abas (tab.php):** Requisitar compra (`tab.php:16`), Requisições de Compra (`tab.php:33`), Cotações (`tab.php:34`), E-mail (`tab.php:35`), Ordens compra pendentes (`tab.php:36`), Notas Fiscais Recebidas (`tab.php:37`), Importar XML (`tab.php:38`), Ordem de Serviço (`tab.php:39`), Requisições de Compra (`tab.php:57`), Produto (`tab.php:78`), Gerar OC (`tab.php:99`), Incluir Requisição (`tab.php:120`) …
- **Ações (`case` do roteador, 52):** `est014ContentRequisicoesdeCompraGeral`, `est014ContentRequisicoesdeCompra`, `est014ContentRequisicaoCompraEditarNovo`, `est014ContentAssociarProduto`, `est014contentGerarOC`, `est014RequisicaoCompra`, `est014contentOrdemServico`, `est014FormOrdemServico`, `est014ContentIncluirOS`, `est014ContentOrdemServicoGeral`, `est014FormRequisicoesdeCompraEditarIncluir`, `est014FormAssociarProduto`, `est014FormRequisicaoCompra`, `est014FormProdutosOrdemServico`, `est014FormCabecalhoOS`, `atualizarCamposProdutosAssociar`, `atualizarCamposProdutosRequisitar`, `est014atualizarCamposFornecedores`, `est014OSProcessarEstoque`, `est014OSGerarNF` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T128` (fotografia:22089): form em `prog/est/est014/form.php:699`
  - `T129` (fotografia:22143): form em `prog/est/est014/form.php:536`
  - `T189` (fotografia:23794): UPDATE/form em `prog/est/est014/form.php:38`, `prog/est/est014/form.php:331` e mais 4
- **Tabelas só lidas na tela (21):** `D049` (12×, `prog/est/est014/form.php:197` e mais 11), `D009` (12×, `prog/est/est014/form.php:198` e mais 11), `D001` (8×, `prog/est/est014/form_func-ajax.php:26` e mais 7), `D082` (7×, `prog/est/est014/form.php:199` e mais 6), `C007` (7×, `prog/est/est014/grid.php:53` e mais 6), `D024` (6×, `prog/est/est014/form_func-ajax.php:125` e mais 5), `T174` (6×, `prog/est/est014/grid.php:59` e mais 5), `D037` (3×, `prog/est/est014/form.php:200` e mais 2), `T059` (3×, `prog/est/est014/grid.php:54` e mais 2), `T139` (3×, `prog/est/est014/grid.php:907` e mais 2), `T012` (2×, `prog/est/est014/grid.php:55` e mais 1), `T010` (2×, `prog/est/est014/grid.php:422` e mais 1), `T173` (2×, `prog/est/est014/grid.php:428` e mais 1), `T007` (2×, `prog/est/est014/grid.php:1077` e mais 1), `T014` (2×, `prog/est/est014/grid.php:1079` e mais 1), `T013` (2×, `prog/est/est014/grid.php:1080` e mais 1), `D020` (1×, `prog/est/est014/form.php:361`), `D018` (1×, `prog/est/est014/form.php:362`), `D041` (1×, `prog/est/est014/form_func-ajax.php:30`), `T006` (1×, `prog/est/est014/grid.php:1167`), `T008` (1×, `prog/est/est014/grid.php:1173`)
- **Classes e métodos chamados:** `EST004` (10716 linhas) (só incluída); `EST006` (1804 linhas): `insereOCItensViaRequisicao` (`EST006.php:1462`); `EST008` (1979 linhas): `updateT174_Flag_Indisponivel` (`EST008.php:44`), `updateT174_Flag_Gerar_OC` (`EST008.php:28`), `insereCotacaoItensViaRequisicao` (`EST008.php:185`), `insereOrdemServico` (`EST008.php:1892`); `EST012` (51 linhas) (só incluída); `EST014` (1748 linhas): `est014associarProduto` (`EST014.php:116`), `est014OSProcessarEstoque` (`EST014.php:450`), `est014OSGerarNF` (`EST014.php:564`), `updateCheckboxGerarCotar` (`EST014.php:13`), `updateCheckboxGerarComprar` (`EST014.php:35`), `updateCheckboxGerarProducao` (`EST014.php:57`), `est014AplicarIPV` (`EST014.php:81`)
- **Tabelas gravadas pelos métodos chamados:** `C004` (EST014::est014OSGerarNF), `D024` (EST014::est014OSGerarNF), `T001` (EST014::est014OSGerarNF, EST014::est014OSProcessarEstoque), `T006` (EST014::est014OSGerarNF), `T007` (EST014::est014OSGerarNF), `T008` (EST014::est014OSGerarNF), `T010` (EST008::insereCotacaoItensViaRequisicao), `T011` (EST006::insereOCItensViaRequisicao), `T128` (EST008::insereOrdemServico, EST014::est014OSProcessarEstoque), `T129` (EST008::insereOrdemServico), `T174` (EST008::updateT174_Flag_Gerar_OC, EST008::updateT174_Flag_Indisponivel, EST014::est014AplicarIPV), `T186` (EST014::est014OSProcessarEstoque), `T189` (EST008::insereOrdemServico, EST014::est014associarProduto, EST014::updateCheckboxGerarComprar…)
- **Chaves C031 lidas (1):** `CFOPPadraoOrdemServico`
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/est/est014/form.php:69`)
  - "Registro Incluido com Sucesso!" (`prog/est/est014/form.php:90`)
  - "Produto associado com sucesso!." (`prog/est/est014/form.php:248`)
- **Abre as telas:** `est/est008`, `est/est006`, `cad/cad002`, `est/est016`, `est/est015`
- **Grafo (Graphify):** 30 nós, 21 funções definidas: `validaCampoComprar()`, `validaCampoDtEnvio()`, `validaCampoDtRetorno()`, `validaCampoIconeEmail()`, `validaCampoIconeFinalizado()`, `validaCampoIndisponivel()` …. Chama fora da tela: `str_pad()` (outros.php)
- **Aberta por:** `cad/cad002`

**Leitura do código (manual):**

- **A conferir (25/11/2026):** lê T010 e T012, do fluxo antigo, mas grava a requisição T189, que o fluxo novo também usa (`est015`, `est016`). Decidir se a requisição entra no porte pelo fluxo novo.

### `prog/est/est015` — Cotação de compra Novo

- **Menu:** Estoque › Cotação de compra Novo, acesso C029 nº 170 "Cotação de Compra" (item comentado no Menu) (`bibliotecas/menu.php:200`)
- **Menu:** Estoque › Análise de Cotação de Compra, acesso C029 nº 172 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:202`)
- **Arquivos:** 11 arquivos. 16.286 linhas. `form.php` (4623), `grid.php` (3545), `form_func-ajax.php` (2274), `outro.php` (2219), `content.php` (1394), `grid_func-ajax.php` (623), `grid_func-callback.php` (611), `tab.php` (333), `form_func-js.php` (257), `gravar_campo_editado_online.php` (237), `grid_func-js.php` (170)
- **Abas (tab.php):** Cotação de compra (`tab.php:21`), Cancelados (`tab.php:22`), Produtos (`tab.php:23`), Prod. x Forn. (`tab.php:24`), Cotação de Compra (`tab.php:51`), Produtos (`tab.php:88`), Cabeçalho (`tab.php:89`), Pagamentos (`tab.php:91`), Documentos (`tab.php:92`), E-mail (`tab.php:93`), Configurações (`tab.php:94`), Processos (`tab.php:115`) …
- **Ações (`case` do roteador, 178):** `est015ContentPrincipal`, `est015ContentOrcamento`, `est015ContentEditarOrcamento`, `est015ContentProdutos`, `est015contentform`, `est015contentServicos`, `est015contentTrocas`, `est015ContentEditarServico`, `est015contentProcessos`, `est015contentMateriais`, `est015contentTotais`, `est015ContentEditarProduto`, `est015ContentAplicarIpv`, `est015ContentAplicarDesconto`, `est015ContentImportarOrcamento`, `est015contentPagamentos`, `est015ContentEmail`, `est015ContentCancelamentoOrcamento`, `est015ContentDocumentos`, `est015ContentDividirOrcamento` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D024` (fotografia:4506): UPDATE em `prog/est/est015/gravar_campo_editado_online.php:206`
  - `D047` (fotografia:5424): form em `prog/est/est015/form.php:3319`
  - `D049` (fotografia:5460): UPDATE em `prog/est/est015/gravar_campo_editado_online.php:210`
  - `D069` (fotografia:6119): UPDATE em `prog/est/est015/form_func-ajax.php:1960`, `prog/est/est015/form_func-ajax.php:2021`
  - `T001` (fotografia:14264): INSERT/UPDATE em `prog/est/est015/form_func-ajax.php:1299`, `prog/est/est015/form_func-ajax.php:1317` e mais 2
  - `T004` (fotografia:14902): UPDATE em `prog/est/est015/form_func-ajax.php:1427`, `prog/est/est015/form_func-ajax.php:1676`
  - `T006` (fotografia:15620): UPDATE em `prog/est/est015/form_func-ajax.php:1825`
  - `T139` (fotografia:22467): INSERT/UPDATE em `prog/est/est015/form.php:426`, `prog/est/est015/form.php:428` e mais 2
  - `T146` (fotografia:22715): form em `prog/est/est015/form.php:1385`
  - `T152` (fotografia:22845): form em `prog/est/est015/form.php:1511`
  - `T174` (fotografia:23369): UPDATE em `prog/est/est015/grid_func-ajax.php:425`, `prog/est/est015/grid_func-ajax.php:430`
  - `T189` (fotografia:23794): form em `prog/est/est015/form.php:4089`
  - `T198` (fotografia:24027): DELETE em `prog/est/est015/form.php:641`
  - `T202` (fotografia:24127): form em `prog/est/est015/form.php:3606`
  - `T218` (fotografia:24521): UPDATE/form em `prog/est/est015/content.php:93`, `prog/est/est015/content.php:98` e mais 27
  - `T218A` (fotografia:24729): UPDATE em `prog/est/est015/form_func-ajax.php:1863`, `prog/est/est015/gravar_campo_editado_online.php:26` e mais 3
  - `T219` (fotografia:24768): DELETE/UPDATE/form em `prog/est/est015/form.php:795`, `prog/est/est015/form.php:942` e mais 47
  - `T219A` (fotografia:24969): UPDATE em `prog/est/est015/form_func-ajax.php:1711`, `prog/est/est015/gravar_campo_editado_online.php:202` e mais 1
  - `T221` (fotografia:25062): form em `prog/est/est015/form.php:3439`
  - `T222` (fotografia:25083): form em `prog/est/est015/form.php:1221`
  - `T223` (fotografia:25115): form em `prog/est/est015/form.php:1052`
  - `T225` (fotografia:25444): UPDATE em `prog/est/est015/form_func-ajax.php:1700`, `prog/est/est015/form_func-ajax.php:1848`
- **Tabelas só lidas na tela (47):** `D009` (44×, `prog/est/est015/form.php:943` e mais 43), `C007` (43×, `prog/est/est015/form_func-ajax.php:634` e mais 42), `D001` (28×, `prog/est/est015/form.php:945` e mais 27), `D018` (24×, `prog/est/est015/form.php:36` e mais 23), `D006` (24×, `prog/est/est015/form.php:967` e mais 23), `D082` (21×, `prog/est/est015/form.php:1237` e mais 20), `D037` (18×, `prog/est/est015/form.php:1238` e mais 17), `C004` (18×, `prog/est/est015/form.php:3050` e mais 17), `D020` (13×, `prog/est/est015/form.php:37` e mais 12), `T224` (12×, `prog/est/est015/grid.php:105` e mais 11), `D133` (10×, `prog/est/est015/form.php:157` e mais 9), `D134` (10×, `prog/est/est015/form.php:157` e mais 9), `D013` (10×, `prog/est/est015/form_func-ajax.php:2134` e mais 9), `D005` (8×, `prog/est/est015/form_func-ajax.php:132` e mais 7), `D116` (8×, `prog/est/est015/grid.php:1870` e mais 7), `D021` (7×, `prog/est/est015/form_func-ajax.php:622` e mais 6), `T003` (6×, `prog/est/est015/form_func-ajax.php:1568` e mais 5), `T220` (5×, `prog/est/est015/form.php:1725` e mais 4), `T005` (5×, `prog/est/est015/form_func-ajax.php:1723` e mais 4), `D083` (4×, `prog/est/est015/form.php:2161` e mais 3), `D070` (4×, `prog/est/est015/form_func-ajax.php:201` e mais 3), `T008` (4×, `prog/est/est015/form_func-ajax.php:1617` e mais 3), `T014` (4×, `prog/est/est015/grid.php:106` e mais 3), `T013` (4×, `prog/est/est015/grid.php:107` e mais 3), `T144` (4×, `prog/est/est015/grid_func-callback.php:231` e mais 3) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `retornaDestinoProdutos` (`CAD002.php:12359`), `retornaPrecoVenda` (`CAD002.php:8729`), `D001_reprocessa_historico` (`CAD002.php:1159`), `cad002RetornaPrecoCotacao` (`CAD002.php:6797`); `EST015` (9926 linhas): `atualizarCodigoFornecedorCotacao` (`EST015.php:9869`), `insereItemEstoque` (`EST015.php:7846`), `insereItemCotacaoEmergencia` (`EST015.php:8129`), `insereItemCotacaoPedidoEmergencia` (`EST015.php:8295`), `incluirProduto` (`EST015.php:2630`), `pesquisaGridItemCotacao` (`EST015.php:9442`), `retornaCFOP` (`EST015.php:5049`), `atualizarAliquotas` (`EST015.php:2261`), `insereServico` (`EST015.php:1117`), `incluirProdutoTroca` (`EST015.php:2583`) …; `GFormWrap` (29 linhas) (só incluída); `VEN001` (15360 linhas): `ven001atualizarCFOP` (`VEN001.php:12384`), `atualizarAliquotas` (`VEN001.php:3688`); `VEN002` (10343 linhas): `ven002atualizarCFOP` (`VEN002.php:7371`), `atualizarAliquotasPedidos` (`VEN002.php:2005`)
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::D001_reprocessa_historico, EST015::insereItemEstoque), `D009A` (CAD002::D001_reprocessa_historico), `D083` (EST015::est015AplicarPercentualDesconto, EST015::incluirProduto), `T001` (CAD002::D001_reprocessa_historico), `T003` (VEN001::ven001atualizarCFOP), `T004` (EST015::cancelarOrcamento, EST015::excluirProduto, EST015::insereItemCotacaoEmergencia…), `T004A` (VEN001::atualizarAliquotas, VEN001::ven001atualizarCFOP), `T005` (VEN002::ven002atualizarCFOP), `T006` (EST015::cancelarOrcamento, EST015::excluirProduto, VEN002::ven002atualizarCFOP), `T006A` (VEN002::ven002atualizarCFOP), `T066` (CAD002::D001_reprocessa_historico), `T146` (EST015::excluirServico, EST015::incluirProduto, EST015::insereServico), `T148` (EST015::gerarPedido), `T149` (EST015::gerarPedido), `T152` (EST015::excluirServico, EST015::incluirProduto, EST015::insereServico) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Quantidade_DF` (`prog/est/est015/grid_func-callback.php:261` e mais 3), `D009_Quantidade_DM` (`prog/est/est015/grid_func-callback.php:279` e mais 1), `D009_Quantidade_Estoque_Tabela` (`prog/est/est015/grid.php:426`), `D009_Quantidade_MP` (`prog/est/est015/grid_func-callback.php:300` e mais 3), `D009_Quantidade_Pedido` (`prog/est/est015/form.php:2170`), `T005_Gravar_Totalizacao_4` (`prog/est/est015/form_func-ajax.php:79`), `T218_Gravar_Totalizacao_3` (`prog/est/est015/form.php:324` e mais 6), `T219_Gravar_Totalizacao` (`prog/est/est015/form.php:322` e mais 6)
- **Chaves C031 lidas (17):** `atualizarCustoVendaComCustoMedio`, `campoNomeEmpresa`, `comprasCalcularICMS`, `comprasCalcularIPI`, `comprasCalcularST`, `descricaoCFOPExibida`, `exibirLimitesOrcamentoPedido`, `habilitaEmail`, `nivelAlcadaAtualizarCustoFornecedor`, `nivelAlcadaAtualizarCustoProduto`, `nivelAlcadaAtualizarCustoVenda`, `obrigatorioFiltroTelaTransicao` …
- **Mensagens e validações visíveis (33):**
  - "Deseja excluir os itens marcados?" (`prog/est/est015/content.php:392`)
  - "Qual atualização você deseja?" (`prog/est/est015/content.php:423`)
  - "Deseja atualizar o custo apenas para esta venda?" (`prog/est/est015/content.php:452`)
  - "Deseja atualizar o custo dos produtos selecionados?" (`prog/est/est015/content.php:475`)
  - "Deseja atualizar a tabela do fornecedor dos produtos selecionados?" (`prog/est/est015/content.php:498`)
  - "Esta cotação de compra não existe!" (`prog/est/est015/content.php:69`)
  - "Esta cotação de compra foi  negada ." (`prog/est/est015/content.php:89`)
  - "Esta cotação de compra foi  aprovada ." (`prog/est/est015/content.php:91`)
  - … mais 25
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::cotacaoFinalizada` (`NOTIFICACOES.php:758`, clique na linha 779; lê `T218`); `NOTIFICACOES::cotacaoAnalise` (`NOTIFICACOES.php:792`, clique na linha 812; lê `T218`); `NOTIFICACOES::cotacaoAprovada` (`NOTIFICACOES.php:855`, clique na linha 875; lê `T218`); `NOTIFICACOES::cotacaoNegada` (`NOTIFICACOES.php:957`, clique na linha 979; lê `T218`)
- **Abre as telas:** `cad/cad002`, `cad/cad001`, `est/est016`, `etc/etc006`, `ven/ven001`, `est/est002`, `crm/crm001`, `est/esT218`, `ven/ven002`, `srm/srm001`
- **Grafo (Graphify):** 75 nós, 64 funções definidas: `alteraServico()`, `campoProcuraProduto()`, `campoProcuraProdutoEditar()`, `campoProcuraProdutoMaterial()`, `campoProcuraServicoPedido()`, `carregaCFOP_Contatos()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `cad/cad002`, `cad/cad061`, `crm/crm001`, `est/est009`, `est/est014`, `est/est016`, `est/est017`, `est/est019`, `srm/srm001`, `ven/ven001`, `ven/ven002`, `ven/ven003`

**Leitura do código (manual):**

- **Dentro do porte (25/11/2026):** cotação do fluxo novo de compras (T218, T219), que a Fixar usa.

### `prog/est/est016` — Análise de Ordem de compra

- **Menu:** Estoque › Análise de Ordem de compra, acesso C029 nº 173 "Análise de Ordem de Compra" (`bibliotecas/menu.php:33`)
- **Menu:** Estoque › Ordem de compra Novo, acesso C029 nº 171 "Ordem de Compra" (item comentado no Menu) (`bibliotecas/menu.php:201`)
- **Arquivos:** 10 arquivos. 16.132 linhas. `form.php` (5853), `grid.php` (2969), `form_func-ajax.php` (1801), `outro.php` (1749), `content.php` (1733), `grid_func-ajax.php` (625), `grid_func-callback.php` (551), `tab.php` (433), `form_func-js.php` (247), `grid_func-js.php` (171)
- **Abas (tab.php):** Ordem de Compra (`tab.php:21`), Produtos (`tab.php:22`), Serviços (`tab.php:23`), Troca (`tab.php:24`), Processos (`tab.php:100`), Materiais (`tab.php:101`), Produto (`tab.php:120`), IPI (`tab.php:121`), Icms (`tab.php:122`), Pis/Cofins (`tab.php:123`), Agregados (`tab.php:124`), Pagamento (`tab.php:144`) …
- **Ações (`case` do roteador, 207):** `est016content01`, `est016content02`, `est016ContentFaturarEmergencia`, `est016content03`, `est016content04`, `est016contentform`, `est016contentTrocas`, `est016content05`, `est016content06`, `est016content07`, `est016content08`, `est016ContentEditarProduto`, `est016ContentAplicarIpv`, `est016ContentImportarItens`, `est016ContentAbrePagamento`, `est016ContentPagamento`, `est016ContentEmail`, `est016ContentDocumentos`, `est016ContentDetalhesCheque`, `est016ContentAplicarDesconto` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D009` (fotografia:3608): UPDATE em `prog/est/est016/form_func-ajax.php:62`, `prog/est/est016/grid_func-ajax.php:159`
  - `T005` (fotografia:15218): UPDATE em `prog/est/est016/form_func-ajax.php:26`
  - `T006` (fotografia:15620): UPDATE em `prog/est/est016/form_func-ajax.php:64`, `prog/est/est016/form_func-ajax.php:1564`
  - `T139` (fotografia:22467): INSERT em `prog/est/est016/form_func-ajax.php:1653`, `prog/est/est016/form_func-ajax.php:1766`
  - `T148` (fotografia:22795): form em `prog/est/est016/form.php:1076`
  - `T149` (fotografia:22819): form em `prog/est/est016/form.php:895`
  - `T174` (fotografia:23369): UPDATE em `prog/est/est016/grid_func-ajax.php:198`, `prog/est/est016/grid_func-ajax.php:202`
  - `T189` (fotografia:23794): form em `prog/est/est016/form.php:4004`, `prog/est/est016/form.php:4094`
  - `T204` (fotografia:24194): form em `prog/est/est016/form.php:3355`
  - `T219` (fotografia:24768): UPDATE em `prog/est/est016/form_func-ajax.php:1577`
  - `T222` (fotografia:25083): form em `prog/est/est016/form.php:2749`
  - `T224` (fotografia:25146): UPDATE/form em `prog/est/est016/content.php:60`, `prog/est/est016/form.php:55` e mais 29
  - `T224A` (fotografia:25396): INSERT/form em `prog/est/est016/form.php:361`, `prog/est/est016/form.php:367`
  - `T225` (fotografia:25444): UPDATE/form em `prog/est/est016/form.php:475`, `prog/est/est016/form.php:1224` e mais 28
  - `T226` (fotografia:25756): form em `prog/est/est016/form.php:1546`, `prog/est/est016/form.php:2523`
  - `T227` (fotografia:25788): form em `prog/est/est016/form.php:2346`, `prog/est/est016/form.php:3044`
  - `T228` (fotografia:25809): INSERT em `prog/est/est016/form_func-ajax.php:1124`
  - `T230` (fotografia:25862): form em `prog/est/est016/form.php:712`
- **Tabelas só lidas na tela (53):** `D024` (38×, `prog/est/est016/form.php:45` e mais 37), `D049` (32×, `prog/est/est016/form.php:726` e mais 31), `C007` (25×, `prog/est/est016/form_func-ajax.php:1646` e mais 24), `D001` (24×, `prog/est/est016/form.php:1830` e mais 23), `D018` (23×, `prog/est/est016/form.php:110` e mais 22), `D020` (19×, `prog/est/est016/form.php:45` e mais 18), `D082` (19×, `prog/est/est016/form.php:728` e mais 18), `D006` (18×, `prog/est/est016/form_func-ajax.php:544` e mais 17), `D037` (15×, `prog/est/est016/form.php:729` e mais 14), `C004` (12×, `prog/est/est016/content.php:140` e mais 11), `D133` (11×, `prog/est/est016/form.php:140` e mais 10), `D134` (11×, `prog/est/est016/form.php:140` e mais 10), `D005` (11×, `prog/est/est016/form_func-ajax.php:331` e mais 10), `T014` (9×, `prog/est/est016/grid.php:66` e mais 8), `T013` (9×, `prog/est/est016/grid.php:66` e mais 8), `D022` (8×, `prog/est/est016/form.php:45` e mais 7), `D013` (6×, `prog/est/est016/form_func-ajax.php:1690` e mais 5), `D116` (5×, `prog/est/est016/grid.php:103` e mais 4), `D083` (4×, `prog/est/est016/form.php:1262` e mais 3), `T001` (4×, `prog/est/est016/form_func-ajax.php:1478` e mais 3), `T218` (4×, `prog/est/est016/form_func-ajax.php:1480` e mais 3), `T055` (4×, `prog/est/est016/grid_func-callback.php:239` e mais 3), `T075` (4×, `prog/est/est016/grid_func-callback.php:239` e mais 3), `D036` (3×, `prog/est/est016/form.php:45` e mais 2), `T206` (3×, `prog/est/est016/grid_func-callback.php:199` e mais 2) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `retornaDestinoProdutos` (`CAD002.php:12359`), `D001_reprocessa_historico` (`CAD002.php:1159`), `cad002RetornaPrecoCompra` (`CAD002.php:3707`), `reprocessaOCPedidos` (`CAD002.php:5087`), `cad002RetornaPrecoCotacao` (`CAD002.php:6797`), `retornaPrecoVenda` (`CAD002.php:8729`); `EST004` (10716 linhas): `alterarStatusOCVinculada` (`EST004.php:9011`); `EST015` (9926 linhas): `retornaUltimoPrecoCompra` (`EST015.php:8469`), `retornaCFOP` (`EST015.php:5049`), `retornaAliquotas` (`EST015.php:6349`), `retornaltimoPrecoVenda` (`EST015.php:6030`); `EST016` (6846 linhas): `alteraLista` (`EST016.php:4329`), `pesquisaGridItemOC` (`EST016.php:6699`), `incluirProduto` (`EST016.php:2496`), `insereServico` (`EST016.php:1008`), `calcularParcelaCombo` (`EST016.php:3265`), `atualizarParcelas` (`EST016.php:2145`), `calculaTotalGeral` (`EST016.php:3307`), `calculaPercentualDescontoPedido` (`EST016.php:3416`), `calculaPrazoMedioAlcadaVendedor` (`EST016.php:3488`), `calculaPrazoMedioAlcadaSupervisor` (`EST016.php:3516`) …; `GFormWrap` (29 linhas) (só incluída); `VEN002` (10343 linhas): `ven002atualizarCFOP` (`VEN002.php:7371`), `atualizarAliquotasPedidos` (`VEN002.php:2005`)
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::D001_reprocessa_historico, EST016::gerarOT), `D009A` (CAD002::D001_reprocessa_historico), `D083` (EST016::incluirProduto), `T001` (CAD002::D001_reprocessa_historico), `T005` (VEN002::ven002atualizarCFOP), `T006` (CAD002::reprocessaOCPedidos, EST016::cancelarProduto, EST016::excluirProduto…), `T006A` (VEN002::ven002atualizarCFOP), `T066` (CAD002::D001_reprocessa_historico), `T148` (EST016::excluirServico, EST016::incluirProduto, EST016::insereServico), `T149` (EST016::excluirServico, EST016::incluirProduto, EST016::insereServico), `T185` (EST016::incluirProduto), `T189` (EST016::comprarProduto, EST016::incluirProduto, EST016::modificarProdutosSelecionados), `T222` (EST016::incluirProdutoTroca), `T224` (EST004::alterarStatusOCVinculada, EST016::EST016ProcessarOCVencida, EST016::alteraLista…), `T224A` (EST016::atualizarAliquotasPedidos) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Classificacao_Fiscal` (`prog/est/est016/form_func-ajax.php:344`), `D009_IPI` (`prog/est/est016/form_func-ajax.php:399`), `D009_Quantidade_DF` (`prog/est/est016/grid_func-callback.php:207` e mais 3), `D009_Quantidade_DM` (`prog/est/est016/grid_func-callback.php:223` e mais 1), `D009_Quantidade_Estoque_Tabela` (`prog/est/est016/grid.php:361`), `D009_Quantidade_MP` (`prog/est/est016/grid_func-callback.php:242` e mais 3), `D009_Quantidade_Pedido` (`prog/est/est016/form.php:1271` e mais 4), `T005_Status_Pedido` (`prog/est/est016/content.php:140` e mais 1), `T224_Gravar_Totalizacao_3` (`prog/est/est016/form.php:1840` e mais 4), `T224_Prazos` (`prog/est/est016/form_func-ajax.php:1233` e mais 1), `T224_Status_Pedido` (`prog/est/est016/form_func-ajax.php:1108` e mais 2), `T225_Gravar_Totalizacao` (`prog/est/est016/form.php:1838` e mais 1), `T225_Quantidade_Pendente_3` (`prog/est/est016/grid.php:372` e mais 3)
- **Chaves C031 lidas (20):** `PrazoVencerOrdemCompra`, `atualizarCustoVendaComCustoMedio`, `botaoAplicarDesconto`, `campoNomeEmpresa`, `controleAlcadasOrdemCompra`, `descricaoCFOPExibida`, `exibirLimitesOrcamentoPedido`, `habilitaComprasImportacao`, `habilitarbotaoIncluirOC`, `nivelAlcadaAtualizarCustoVenda`, `obrigatorioFiltroTelaTransicao`, `obrigatorioOCNotaFiscalCompra` …
- **Mensagens e validações visíveis (19):**
  - "Deseja solicitar a coleta dos produtos?" (`prog/est/est016/content.php:360`)
  - "Deseja excluir os itens marcados?" (`prog/est/est016/content.php:624`)
  - "Qual atualização você deseja?" (`prog/est/est016/content.php:654`)
  - "Deseja atualizar o custo apenas para esta venda?" (`prog/est/est016/content.php:703`)
  - "Este pedido não existe!" (`prog/est/est016/content.php:36`)
  - "Esta ordem de compra foi  negada ." (`prog/est/est016/content.php:56`)
  - "Esta ordem de compra foi  aprovada ." (`prog/est/est016/content.php:58`)
  - "$retorno" (`prog/est/est016/form.php:814`)
  - … mais 11
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::ordemCompraAnalise` (`NOTIFICACOES.php:823`, clique na linha 843; lê `T224`); `NOTIFICACOES::ordemCompraAprovada` (`NOTIFICACOES.php:923`, clique na linha 946; lê `T224`); `NOTIFICACOES::ordemCompraNegada` (`NOTIFICACOES.php:990`, clique na linha 1012; lê `T224`); `NOTIFICACOES::produtosDivergencia` (`NOTIFICACOES.php:1186`, clique na linha 1211; lê `C004`, `T013`, `T014`, `T225`); `NOTIFICACOES::ordemTransferencia` (`NOTIFICACOES.php:2062`, clique na linha 2077; lê `T005`, `T224`)
- **Abre as telas:** `cad/cad002`, `cad/cad001`, `etc/etc006`, `est/est009`, `est/est012`, `crm/crm001`, `ven/ven001`, `est/est015`, `est/est010`, `est/est004` …
- **Grafo (Graphify):** 70 nós, 60 funções definidas: `alteraServico()`, `campoProcuraProduto()`, `campoProcuraProdutoEditar()`, `campoProcuraProdutoMaterial()`, `campoProcuraServicoPedido()`, `carregaCFOP_Contatos()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `cad/cad002`, `cad/cad061`, `crm/crm001`, `est/est004`, `est/est009`, `est/est014`, `est/est015`, `est/est017`, `est/est019`, `imp/imp003`, `srm/srm001`, `ven/ven001`

**Leitura do código (manual):**

- **Dentro do porte (25/11/2026):** ordem de compra e análise do fluxo novo (T224, T225). Item ativo no Menu: Análise de Ordem de compra, acesso 173.

### `prog/est/est017` — P.C.P.

- **Menu:** Estoque › P.C.P., acesso C029 nº 203 "PCP" (`bibliotecas/menu.php:27`)
- **Arquivos:** 10 arquivos. 5.328 linhas. `grid.php` (2124), `form.php` (1030), `grid_func-callback.php` (626), `outro.php` (497), `content.php` (421), `tab.php` (226), `grid_func-ajax.php` (164), `form_func-ajax.php` (145), `form_func-js.php` (53), `grid_func-js.php` (42)
- **Abas (tab.php):** PCP (`tab.php:16`), Análise de Custo (`tab.php:36`), Produção Emergência (`tab.php:37`), Ordem Produção (`tab.php:38`), Produção (`tab.php:39`), Produtos (`tab.php:73`), Cabeçalho (`tab.php:74`), Matéria Prima / Consumo (`tab.php:75`), Cabeçalho (`tab.php:77`), Produto (`tab.php:107`), Produção (`tab.php:108`), Composição (`tab.php:109`) …
- **Ações (`case` do roteador, 68):** `est017ContentPrincipal`, `est017ContentPCP`, `est017ContentOrdemProducao`, `est017ContentEntradaProducao`, `est017ContentEntradaProduto`, `est017ContentProcessarProducao`, `est017ContentConsumiveis`, `est017ContentProdutoComposicao`, `est017ContentProcessos`, `est017ContentConsumivel`, `est017ContentProdutoProducao`, `est017ContentOrdensProducao`, `est017ContentProducoes`, `est017ContentProducaoAberto`, `est017ContentProducaoFinalizado`, `est017FormProdutoProducao`, `est017FormEntradaProducao`, `est017FormCabecalhoOrdemProducao`, `est017FormProdutoComposicao`, `est017FormProcessos` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T004` (fotografia:14902): UPDATE em `prog/est/est017/grid_func-ajax.php:130`
  - `T006` (fotografia:15620): UPDATE em `prog/est/est017/grid_func-ajax.php:72`
  - `T255` (fotografia:26575): form em `prog/est/est017/form.php:379`
  - `T256` (fotografia:26629): form em `prog/est/est017/form.php:29`
  - `T256A` (fotografia:26655): UPDATE em `prog/est/est017/form_func-ajax.php:41`, `prog/est/est017/form_func-ajax.php:93`
  - `T257` (fotografia:26677): UPDATE/form em `prog/est/est017/form.php:239`, `prog/est/est017/grid_func-ajax.php:97`
  - `T261` (fotografia:26764): form em `prog/est/est017/form.php:540`
  - `T262` (fotografia:26807): form em `prog/est/est017/form.php:724`
  - `T263` (fotografia:26856): form em `prog/est/est017/form.php:872`
  - `T264` (fotografia:26875): UPDATE em `prog/est/est017/grid_func-ajax.php:120`
- **Tabelas só lidas na tela (33):** `D009` (31×, `prog/est/est017/form.php:42` e mais 30), `D049` (28×, `prog/est/est017/form.php:40` e mais 27), `D001` (27×, `prog/est/est017/form.php:86` e mais 26), `C007` (17×, `prog/est/est017/grid.php:72` e mais 16), `D037` (16×, `prog/est/est017/form.php:44` e mais 15), `D024` (14×, `prog/est/est017/form_func-ajax.php:130` e mais 13), `T005` (13×, `prog/est/est017/form_func-ajax.php:129` e mais 12), `D082` (7×, `prog/est/est017/form.php:43` e mais 6), `T001` (6×, `prog/est/est017/form.php:431` e mais 5), `T066` (5×, `prog/est/est017/form.php:302` e mais 4), `D078` (4×, `prog/est/est017/grid.php:1363` e mais 3), `D079` (4×, `prog/est/est017/grid.php:1364` e mais 3), `T055` (4×, `prog/est/est017/grid_func-callback.php:239` e mais 3), `T075` (4×, `prog/est/est017/grid_func-callback.php:239` e mais 3), `D041` (3×, `prog/est/est017/form.php:41` e mais 2), `D004` (3×, `prog/est/est017/grid.php:1692` e mais 2), `D001A` (3×, `prog/est/est017/grid.php:1986` e mais 2), `T206` (3×, `prog/est/est017/grid_func-callback.php:199` e mais 2), `D005` (2×, `prog/est/est017/grid.php:65` e mais 1), `D006` (2×, `prog/est/est017/grid.php:66` e mais 1), `T003` (2×, `prog/est/est017/grid.php:69` e mais 1), `T219` (2×, `prog/est/est017/grid.php:71` e mais 1), `T255A` (2×, `prog/est/est017/grid.php:1993` e mais 1), `D116` (2×, `prog/est/est017/grid_func-callback.php:85` e mais 1), `D133` (2×, `prog/est/est017/grid_func-callback.php:85` e mais 1) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `D001_reprocessa_historico` (`CAD002.php:1159`); `EST017` (1649 linhas): `processarEngenhariaProduto` (`EST017.php:807`), `gerarOrdemProducaoComposicao` (`EST017.php:227`), `gatilhoValidacaoInsertT257` (`EST017.php:884`), `dadosCertificadosOP` (`EST017.php:1045`), `processarEntrada` (`EST017.php:292`), `validaProcessarEntradaEmEtapas` (`EST017.php:441`), `atualizarComponentes` (`EST017.php:894`), `gerarOrdemProducao` (`EST017.php:15`), `processarProdutosBaixa` (`EST017.php:593`), `baixarEstoqueMateriais` (`EST017.php:686`) …; `SMTEMAIL` (156 linhas): `geraEmail` (`SMTEMAIL.php:21`)
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::D001_reprocessa_historico), `D009A` (CAD002::D001_reprocessa_historico), `D085` (EST017::excluirOrdemProducao, EST017::gerarOrdemProducao), `P001` (EST017::excluirOrdemProducao), `T001` (CAD002::D001_reprocessa_historico, EST017::baixarEstoqueMateriais, EST017::processarEntrada), `T005` (EST017::processarEntrada), `T066` (CAD002::D001_reprocessa_historico), `T255` (EST017::baixarEstoqueMateriais, EST017::cancelarOrdemProducao, EST017::excluirOrdemProducao…), `T255A` (EST017::excluirOrdemProducao, EST017::gerarOrdemProducao), `T256` (EST017::excluirOrdemProducao, EST017::gerarOrdemProducao), `T256A` (EST017::excluirOrdemProducao), `T257` (EST017::excluirOrdemProducao, EST017::processarEntrada), `T261` (EST017::atualizarComponentes, EST017::excluirOrdemProducao, EST017::gerarOrdemProducao…), `T261A` (EST017::atualizarComponentes, EST017::excluirOrdemProducao, EST017::gerarOrdemProducao…), `T262` (EST017::atualizarComponentes, EST017::excluirOrdemProducao, EST017::gerarOrdemProducao…) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Quantidade_DF` (`prog/est/est017/grid_func-callback.php:207` e mais 3), `D009_Quantidade_DM` (`prog/est/est017/grid_func-callback.php:223` e mais 1), `D009_Quantidade_MP` (`prog/est/est017/grid_func-callback.php:242` e mais 3)
- **Chaves C031 lidas (3):** `locacaoPadraoProducao`, `locacaoPadraoSimulacao`, `processarEntradaEmEtapas`
- **Mensagens e validações visíveis (11):**
  - "Selecione o formato da impressão:" (`prog/est/est017/content.php:85`)
  - "Deseja processar as entradas?" (`prog/est/est017/content.php:249`)
  - "Registro Alterado com Sucesso!" (`prog/est/est017/form.php:110`)
  - "Registro Incluido com Sucesso!" (`prog/est/est017/form.php:155`)
  - "Produção inserida com sucesso!" (`prog/est/est017/form.php:315`)
  - "A quantidade produzida deve ser menor ou igual ao saldo a produzir." (`prog/est/est017/form.php:330`)
  - "Não foi possível finalizar a ordem de produção, existem itens produzidos que não deram entrada no estoque!" (`prog/est/est017/form.php:437`)
  - "Registro Incluido com Sucesso! -" (`prog/est/est017/form.php:472`)
  - … mais 3
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::producoesEmergencia` (`NOTIFICACOES.php:1955`, clique na linha 1976; lê `D009`, `D049`, `T005`, `T006`); `NOTIFICACOES::analiseCusto` (`NOTIFICACOES.php:2426`, clique na linha 2449; lê `D009`, `D049`, `T003`, `T004`, `T006`); `NOTIFICACOES::baixaAutomatica` (`NOTIFICACOES.php:3049`, clique na linha 3072; lê `T255A`)
- **Abre as telas:** `cad/cad002`, `est/est016`, `ven/ven001`, `est/est015`, `ven/ven002`
- **Grafo (Graphify):** 52 nós, 42 funções definidas: `est017CampoProcuraConsumivel()`, `est017CampoProcuraProduto()`, `est017CampoProcuraProdutoOP()`, `atualizaT225()`, `buscaUnidade()`, `buscaVeiculo()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `cad/cad061`, `crm/crm001`, `ven/ven002`

### `prog/est/est018` — Exportar XML CT-e (Recebido)

- **Menu:** Fiscal › Exportar XML CT-e (Recebido), acesso C029 nº 48 "Exportar TXT NF-e" (`bibliotecas/menu.php:90`)
- **Menu:** Fiscal › Exportar XML CT-e (Emitido), acesso C029 nº 297 "Exportar XML Conhecimentos Emitidos" (`bibliotecas/menu.php:91`)
- **Arquivos:** 3 arquivos. 343 linhas. `form.php` (257), `tab.php` (47), `content.php` (39)
- **Abas (tab.php):** Exportar XML Conhecimentos (`tab.php:16`), Exportar XML Conhecimentos Emitidos (`tab.php:36`)
- **Ações (`case` do roteador, 6):** `est018ContentExportarXmlConhecimentos`, `est018ContentExportarXmlConhecimentosEmitidos`, `est018FormExportarXmlConhecimentos`, `est018FormExportarXmlConhecimentosEmitidos`, `est018TabExportarXmlConhecimentos`, `est018TabExportarXmlConhecimentosEmitidos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T084` (fotografia:20479): form em `prog/est/est018/form.php:33`, `prog/est/est018/form.php:156`
- **Classes e métodos chamados:** `EST018` (304 linhas): `exportarXMLconhecimentos`, `exportarXMLConhecimentosEmitidos` (`EST018.php:150`)
- **Grafo (Graphify):** 3 nós, 0 funções definidas

### `prog/est/est019` — Compras de Estoque (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 258 "Compras Estoque" (`prog/est/est019/tab.php:246`); 259 "Compras Emergência" (`prog/est/est019/tab.php:249`)
- **Arquivos:** 8 arquivos. 8.767 linhas. `grid.php` (4361), `form_func-ajax.php` (2085), `grid_func-ajax.php` (1365), `tab.php` (308), `content.php` (268), `grid_func-callback.php` (179), `form.php` (143), `grid_func-js.php` (58)
- **Abas (tab.php):** Compras de Estoque (`tab.php:17`), Cotação de Emergência (`tab.php:18`), Compras de Emergência (`tab.php:19`), Geral (`tab.php:43`), Geral (`tab.php:63`), Cotação de Emergência (`tab.php:83`), Montagens de Emergência (`tab.php:102`), Serviços de Emergência (`tab.php:121`), Produções de Emergência (`tab.php:140`), Previsão DI (`tab.php:156`), Compras de Estoque (`tab.php:175`), Compras Emergencia (`tab.php:194`) …
- **Ações (`case` do roteador, 124):** `est019ContentComprasDeEstoque`, `est019GridComprasEstoqueLinha`, `cad046ContentMotivosCancelamentoIncluir`, `est019ContentCotacaoEmergencia`, `est019ContentComprasEmergencia`, `est019ContentMontagemEmergencia`, `est019ContentServicosEmergencia`, `est019ContentProducoesEmergencia`, `est019ContentPrevisaoDI`, `est019ContentProdutosEstoqueVazio`, `est019ContentprodutosEstoqueEmergencia`, `est019ContentProdutosHistoricoSemLocacao`, `est019ContentCotacaoEmergencia`, `est019ContentEstoqueEmergencia`, `cad046FormCadastroMotivosCancelamento`, `est019atualizarCFOP`, `est019TotalizarInserirItemEstoque`, `est019TotalizarInsereItemCotacaoEmergencia`, `atualizarCamposProdutos`, `atualizarCamposServicos` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D009` (fotografia:3608): UPDATE em `prog/est/est019/grid_func-ajax.php:295`, `prog/est/est019/grid_func-ajax.php:323` e mais 6
  - `D047` (fotografia:5424): form em `prog/est/est019/form.php:27`
  - `D069` (fotografia:6119): UPDATE em `prog/est/est019/form_func-ajax.php:1822`, `prog/est/est019/form_func-ajax.php:1861`
  - `T001` (fotografia:14264): INSERT/UPDATE em `prog/est/est019/form_func-ajax.php:1229`, `prog/est/est019/form_func-ajax.php:1247` e mais 2
  - `T004` (fotografia:14902): UPDATE em `prog/est/est019/form_func-ajax.php:1337`, `prog/est/est019/form_func-ajax.php:1584` e mais 2
  - `T006` (fotografia:15620): UPDATE em `prog/est/est019/form_func-ajax.php:1727`, `prog/est/est019/form_func-ajax.php:1873` e mais 1
  - `T139` (fotografia:22467): INSERT em `prog/est/est019/form_func-ajax.php:1939`, `prog/est/est019/form_func-ajax.php:2051`
  - `T218` (fotografia:24521): UPDATE em `prog/est/est019/form_func-ajax.php:497`
  - `T219` (fotografia:24768): DELETE/UPDATE em `prog/est/est019/form_func-ajax.php:504`, `prog/est/est019/form_func-ajax.php:505` e mais 5
  - `T225` (fotografia:25444): UPDATE em `prog/est/est019/form_func-ajax.php:1607`, `prog/est/est019/form_func-ajax.php:1749`
  - `T294` (fotografia:27819): UPDATE em `prog/est/est019/grid_func-ajax.php:1282`, `prog/est/est019/grid_func-ajax.php:1308`
- **Tabelas só lidas na tela (47):** `C007` (51×, `prog/est/est019/form_func-ajax.php:595` e mais 50), `D024` (50×, `prog/est/est019/form_func-ajax.php:582` e mais 49), `D049` (41×, `prog/est/est019/form_func-ajax.php:117` e mais 40), `D001` (39×, `prog/est/est019/form_func-ajax.php:118` e mais 38), `D082` (30×, `prog/est/est019/form_func-ajax.php:120` e mais 29), `D037` (26×, `prog/est/est019/form_func-ajax.php:186` e mais 25), `D006` (21×, `prog/est/est019/form_func-ajax.php:925` e mais 20), `D005` (18×, `prog/est/est019/form_func-ajax.php:119` e mais 17), `T005` (17×, `prog/est/est019/form_func-ajax.php:1625` e mais 16), `T293` (15×, `prog/est/est019/form_func-ajax.php:386` e mais 14), `T003` (15×, `prog/est/est019/form_func-ajax.php:1477` e mais 14), `T241` (14×, `prog/est/est019/grid.php:150` e mais 13), `C008` (12×, `prog/est/est019/grid.php:140` e mais 11), `T296` (11×, `prog/est/est019/form_func-ajax.php:370` e mais 10), `D083` (10×, `prog/est/est019/form_func-ajax.php:121` e mais 9), `D003` (10×, `prog/est/est019/grid.php:141` e mais 9), `D060` (10×, `prog/est/est019/grid.php:142` e mais 9), `D002` (10×, `prog/est/est019/grid.php:143` e mais 9), `D015` (10×, `prog/est/est019/grid.php:144` e mais 9), `T187` (8×, `prog/est/est019/grid.php:134` e mais 7), `T295` (8×, `prog/est/est019/grid.php:1533` e mais 7), `C004` (7×, `prog/est/est019/form_func-ajax.php:628` e mais 6), `T066` (4×, `prog/est/est019/form_func-ajax.php:1346` e mais 3), `T008` (4×, `prog/est/est019/form_func-ajax.php:1525` e mais 3), `T220` (3×, `prog/est/est019/form_func-ajax.php:922` e mais 2) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `retornaPrecoVenda` (`CAD002.php:8729`), `D001_reprocessa_historico` (`CAD002.php:1159`), `cad002RetornaPrecoCotacao` (`CAD002.php:6797`), `atualizarEstatisticasCompra` (`CAD002.php:7564`); `EST007` (1239 linhas): `est007GerarMontagemEmergencia` (`EST007.php:1030`), `est007GerarMontagemEstoque` (`EST007.php:1128`); `EST019` (1734 linhas): `est019atualizarCFOP` (`EST019.php:431`), `retornaUltimoPrecoCompra`, `retornaCFOP`, `gerarPedido` (`EST019.php:1116`), `aplicarIpvMinimo`, `est019aplicarDesconto`, `importarOrcamento`, `atualizarAliquotas` (`EST019.php:205`), `gravarObservacao`, `inserirParcela` …; `VEN001` (15360 linhas): `ven001atualizarCFOP` (`VEN001.php:12384`); `VEN002` (10343 linhas): `ven002atualizarCFOP` (`VEN002.php:7371`), `atualizarAliquotasPedidos` (`VEN002.php:2005`)
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::D001_reprocessa_historico, CAD002::atualizarEstatisticasCompra, EST007::est007GerarMontagemEstoque…), `D009A` (CAD002::D001_reprocessa_historico, CAD002::atualizarEstatisticasCompra), `D049` (EST019::updateCheckboxGerarNaoComprar), `T001` (CAD002::D001_reprocessa_historico), `T003` (VEN001::ven001atualizarCFOP), `T004` (EST019::insereItemCotacaoEmergencia, VEN001::ven001atualizarCFOP), `T004A` (VEN001::ven001atualizarCFOP), `T005` (VEN002::ven002atualizarCFOP), `T006` (VEN002::ven002atualizarCFOP), `T006A` (VEN002::ven002atualizarCFOP), `T047` (EST007::est007GerarMontagemEmergencia, EST007::est007GerarMontagemEstoque), `T048` (EST007::est007GerarMontagemEmergencia, EST007::est007GerarMontagemEstoque), `T066` (CAD002::D001_reprocessa_historico), `T071` (EST007::est007GerarMontagemEmergencia, EST007::est007GerarMontagemEstoque), `T219` (EST019::est019atualizarCFOP) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Compra_Emergencia` (`prog/est/est019/grid.php:199` e mais 1), `D009_Quantidade_Estoque_Tabela` (`prog/est/est019/grid.php:1098` e mais 5), `D009_Quantidade_MP` (`prog/est/est019/grid.php:4029`), `T005_Status_Pedido` (`prog/est/est019/grid.php:4300`), `T294_Gravar_Totalizacao` (`prog/est/est019/form_func-ajax.php:45` e mais 1)
- **Chaves C031 lidas (8):** `SeparaProdutoPorEmpresa`, `atualizarCustoVendaComCustoMedio`, `desabilitarCompraEmergenciaInternacional`, `desabilitarCotacaoEmergenciaInternacional`, `desabilitarMontagemEmergencia`, `listarItensNacionais`, `orcamentoCarregarPrimeiroContato`, `precopadraoVendaProduto`
- **Mensagens e validações visíveis (3):**
  - "Registro Incluido com Sucesso!" (`prog/est/est019/form.php:83`)
  - "Informe um número de Quotation" (`prog/est/est019/grid.php:487`)
  - "Informe um número de cotação" (`prog/est/est019/grid.php:1291`)
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::comprasEmergenciaInternacional` (`NOTIFICACOES.php:2721`, clique na linha 2770; lê `C007`, `D006`, `D009`, `D049`, `N001`, `T003`); `NOTIFICACOES::cotacaoEmergenciaInternacional` (`NOTIFICACOES.php:2787`, clique na linha 2825; lê `C007`, `D006`, `D009`, `D049`, `N001`, `T003`); `NOTIFICACOES::produtosEstoqueEmergenciaInternacional` (`NOTIFICACOES.php:2921`, clique na linha 2953; lê `D001`, `D009`, `D049`)
- **Abre as telas:** `cad/cad002`, `ven/ven001`, `imp/imp004`, `est/est016`, `est/est015`, `ven/ven002`, `est/est007`, `imp/imp005`, `cad/cad046`, `est/est006` …
- **Grafo (Graphify):** 31 nós, 23 funções definidas: `est019campoConsumido()`, `est019checkboxCotarEmergencia()`, `est019checkboxCotarPedidoEmergencia()`, `est019DescricaoProduto()`, `est019validaCampoCotar()`, `est019VendaPerdida()` …
- **Aberta por:** `imp/imp003`

**Leitura do código (manual):**

- **Dentro do porte (25/11/2026):** compras de estoque do fluxo novo.

### `prog/est/est020` — CT-e (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 10 arquivos. 4.610 linhas. `form.php` (2235), `grid.php` (1106), `content.php` (425), `grid_func-ajax.php` (247), `grid_func-callback.php` (193), `form_func-ajax.php` (162), `tab.php` (96), `grid_func-js.php` (56), `outro.php` (51), `form_func-js.php` (39)
- **Abas (tab.php):** CT-e (`tab.php:19`), Transmitir SEFAZ (`tab.php:20`), Cabeçalho (`tab.php:53`), Documentos (`tab.php:58`), Cabeçalho (`tab.php:59`), Informações da Carga (`tab.php:60`), Valores e Impostos (`tab.php:61`), Adicionais (`tab.php:62`), Documentos Transporte Anterior (`tab.php:63`), Técnico (`tab.php:64`), Pagamentos (`tab.php:65`), a Receber (`tab.php:66`) …
- **Ações (`case` do roteador, 67):** `est020ContentPrincipal`, `est020ContentIncluirConhecimento`, `est020ContentTecnico`, `est020ContentIncluirDocumento`, `est020ContentCancelarCte`, `est020ContentInfoCarga`, `est020ContentIncluirQuantidadeCarga`, `est020ContentValores`, `est020ContentIncluirComponente`, `est020ContentAdicionais`, `est020ContentIncluirObservacaoContribuinte`, `est020ContentIncluirObservacaoFisco`, `est020ContentPagamentos`, `est020ContentCTeSefaz`, `est020ContentIncluirDocumentoAnterior`, `est020ContentIncluirNfeCte`, `est020ContentIncluirOutroDocumento`, `est020FormPrincipal`, `est020FormTecnico`, `est020FormIncluirNfeCte` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T084` (fotografia:20479): UPDATE/form em `prog/est/est020/form.php:33`, `prog/est/est020/form.php:250` e mais 11
  - `T085` (fotografia:20725): form em `prog/est/est020/form.php:407`, `prog/est/est020/form.php:589`
  - `T304` (fotografia:28462): form em `prog/est/est020/form.php:1306`
  - `T305` (fotografia:28482): form em `prog/est/est020/form.php:970`
  - `T306` (fotografia:28503): form em `prog/est/est020/form.php:1572`
  - `T307` (fotografia:28523): form em `prog/est/est020/form.php:1710`
  - `T308` (fotografia:28550): form em `prog/est/est020/form.php:1998`
- **Tabelas só lidas na tela (20):** `D018` (6×, `prog/est/est020/grid.php:56` e mais 5), `D020` (5×, `prog/est/est020/grid.php:55` e mais 4), `T007` (4×, `prog/est/est020/form_func-ajax.php:40` e mais 3), `C004` (3×, `prog/est/est020/form.php:1889` e mais 2), `C007` (3×, `prog/est/est020/grid.php:52` e mais 2), `D024` (2×, `prog/est/est020/grid.php:54` e mais 1), `T013` (2×, `prog/est/est020/grid.php:59` e mais 1), `D027` (2×, `prog/est/est020/grid.php:580` e mais 1), `T084A` (1×, `prog/est/est020/form.php:1165`), `D007` (1×, `prog/est/est020/form.php:1889`), `D148` (1×, `prog/est/est020/form_func-ajax.php:13`), `D022` (1×, `prog/est/est020/grid.php:53`), `D006` (1×, `prog/est/est020/grid.php:57`), `T002` (1×, `prog/est/est020/grid.php:671`), `T117` (1×, `prog/est/est020/grid.php:676`), `D014` (1×, `prog/est/est020/grid.php:677`), `D154` (1×, `prog/est/est020/grid.php:678`), `D046` (1×, `prog/est/est020/grid.php:679`), `T035` (1×, `prog/est/est020/grid.php:681`), `T103` (1×, `prog/est/est020/grid.php:683`)
- **Classes e métodos chamados:** `EST020` (1951 linhas): `incluirCTe` (`EST020.php:24`), `cancelarCTe` (`EST020.php:1642`), `finalizarCTe` (`EST020.php:517`), `continuarLiberacao` (`EST020.php:640`), `excluirCTe` (`EST020.php:1758`), `atualizarAliquota` (`EST020.php:1489`), `enviaCTe` (`EST020.php:1363`), `consultarRecibo` (`EST020.php:1397`), `recuperarDuplicidadeCTe` (`EST020.php:1423`), `enviarLoteCTe` (`EST020.php:1895`); `ReformaTributaria` (482 linhas) (só incluída); `VEN001` (15360 linhas): `populaComboPgtoParcelas` (`VEN001.php:4954`), `atualizarParcelas` (`VEN001.php:6054`)
- **Tabelas gravadas pelos métodos chamados:** `C004` (EST020::continuarLiberacao, EST020::excluirCTe), `D007` (EST020::continuarLiberacao), `T002` (EST020::continuarLiberacao, EST020::excluirCTe), `T015` (EST020::continuarLiberacao, EST020::excluirCTe), `T084` (EST020::atualizarAliquota, EST020::cancelarCTe, EST020::continuarLiberacao…), `T084A` (EST020::incluirCTe), `T085` (EST020::excluirCTe), `T117` (EST020::continuarLiberacao, EST020::excluirCTe), `T304` (EST020::excluirCTe), `T305` (EST020::excluirCTe), `T306` (EST020::excluirCTe), `T307` (EST020::continuarLiberacao, EST020::excluirCTe)
- **Mensagens e validações visíveis (11):**
  - "CT-e Finalizado" (`prog/est/est020/content.php:86`)
  - "Deseja excluir o CT-e?   Este processo irá excluir o CT-e e seus itens." (`prog/est/est020/content.php:172`)
  - "Registro Alterado com Sucesso!" (`prog/est/est020/form.php:157`)
  - "Registro Incuido com Sucesso!" (`prog/est/est020/form.php:185`)
  - "Registro Incluido com Sucesso!" (`prog/est/est020/form.php:342`)
  - "Esta chave já está vinculada a outro CT-e" (`prog/est/est020/form.php:480`)
  - "CT-e Cancelado!" (`prog/est/est020/form.php:777`)
  - "Selecione uma das opções NF-e ou Outros" (`prog/est/est020/form.php:2206`)
  - … mais 3
- **Abre as telas:** `fin/fin001`
- **Grafo (Graphify):** 30 nós, 20 funções definidas: `est020CheckboxEnviarCTe()`, `est020excluirCTe()`, `est020imprimirDACTE()`, `alteraEstado()`, `carregaDadosNfeCte()`, `gridLinha()` …
- **Aberta por:** `est/est005`

