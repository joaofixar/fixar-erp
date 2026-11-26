# Fichas de tela: `prog/srv` — Serviços

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `srv/srv001` | Cadastro | 25* | 9 | 1069 | 3 | 15 |
| `srv/srv002` | Gerenciamento | 27* | 9 | 4183 | 5 | 14 |
| `srv/srv003` | Operação | 28*, 28* | 6 | 2061 | 0 | 15 |
| `srv/srv004` | Inspeção de Trocas | 96* | 6 | 286 | 0 | 5 |

Total: 4 telas. 7.599 linhas. 8 tabelas gravadas. 31 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/srv/srv001` — Cadastro

- **Menu:** Serviços › Cadastro, acesso C029 nº 25 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:204`)
- **Arquivos:** 9 arquivos. 1.069 linhas. `form.php` (479), `grid.php` (265), `content.php` (105), `form_func-ajax.php` (81), `tab.php` (70), `form_func-js.php` (36), `grid_func-callback.php` (18), `grid_func-ajax.php` (14), `grid_func-js.php` (1)
- **Abas (tab.php):** Cadastro de Serviços (`tab.php:14`), Serviço (`tab.php:34`), Processos (`tab.php:35`), Materiais (`tab.php:36`), Serviço (`tab.php:56`)
- **Ações (`case` do roteador, 15):** `srv001content001`, `form1`, `form2`, `srv001contentProcessos`, `srv001ContentMateriais`, `form1`, `srv001form002Processos`, `srv001form003Materiais`, `atualizarCamposProdutos`, `srv001grid001`, `srv001gridProcessos`, `srv001gridMateriais`, `srv001tab001`, `tabForm1`, `tabForm2`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D112` (fotografia:7256): form em `prog/srv/srv001/form.php:26`
  - `D113` (fotografia:7280): form em `prog/srv/srv001/form.php:178`
  - `D114` (fotografia:7304): form em `prog/srv/srv001/form.php:319`
- **Tabelas só lidas na tela (15):** `D049` (3×, `prog/srv/srv001/form.php:328` e mais 2), `D009` (3×, `prog/srv/srv001/form.php:329` e mais 2), `D082` (3×, `prog/srv/srv001/form.php:330` e mais 2), `D037` (2×, `prog/srv/srv001/form.php:331` e mais 1), `D001` (2×, `prog/srv/srv001/form_func-ajax.php:19` e mais 1), `D110` (2×, `prog/srv/srv001/grid.php:23` e mais 1), `D083` (1×, `prog/srv/srv001/form_func-ajax.php:21`), `D041` (1×, `prog/srv/srv001/form_func-ajax.php:24`), `D005` (1×, `prog/srv/srv001/form_func-ajax.php:25`), `T003` (1×, `prog/srv/srv001/form_func-ajax.php:43`), `D024` (1×, `prog/srv/srv001/form_func-ajax.php:44`), `D018` (1×, `prog/srv/srv001/form_func-ajax.php:45`), `D053` (1×, `prog/srv/srv001/form_func-ajax.php:46`), `C004` (1×, `prog/srv/srv001/form_func-ajax.php:47`), `T212` (1×, `prog/srv/srv001/grid.php:24`)
- **Classes e métodos chamados:** `SRV001` (578 linhas) (só incluída)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_IPI` (`prog/srv/srv001/form_func-ajax.php:60`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/srv/srv001/form.php:76`)
  - "Registro Incluido com Sucesso!" (`prog/srv/srv001/form.php:94`)
- **Grafo (Graphify):** 12 nós, 3 funções definidas: `buscaUnidade()`, `validaCompetencia()`, `campoProcuraProduto()`

### `prog/srv/srv002` — Gerenciamento

- **Menu:** Serviços › Gerenciamento, acesso C029 nº 27 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:207`)
- **Arquivos:** 9 arquivos. 4.183 linhas. `grid.php` (1711), `form.php` (1299), `grid_func-callback.php` (365), `content.php` (258), `form_func-ajax.php` (227), `tab.php` (161), `grid_func-ajax.php` (67), `form_func-js.php` (48), `grid_func-js.php` (47)
- **Abas (tab.php):** Pendentes (`tab.php:26`), Em Execução (`tab.php:27`), Concluídos (`tab.php:28`), Cancelados (`tab.php:29`), Cronograma (`tab.php:30`), Funcionários (`tab.php:31`), Veículos (`tab.php:32`), Competências (`tab.php:91`), Materiais (`tab.php:92`), Incluir (`tab.php:113`)
- **Ações (`case` do roteador, 53):** `srv002content01`, `srv002content08`, `srv002content02`, `srv002content03`, `srv002content04`, `srv002content05`, `srv002content06`, `srv002content07`, `srv002ContentHistVeiculos`, `srv002contentPendentes`, `srv002contentExecucao`, `srv002contentPausados`, `srv002ContentIncluir`, `srv002form01`, `srv002form02`, `srv002form03`, `srv002form04`, `srv002form05`, `srv002FormControle`, `srv002FormularioInclusao` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T005` (fotografia:15218): form em `prog/srv/srv002/form.php:43`
  - `T006` (fotografia:15620): form em `prog/srv/srv002/form.php:192`
  - `T147` (fotografia:22739): form em `prog/srv/srv002/form.php:344`, `prog/srv/srv002/form.php:788` e mais 1
  - `T148` (fotografia:22795): form em `prog/srv/srv002/form.php:648`
  - `T149` (fotografia:22819): form em `prog/srv/srv002/form.php:510`
- **Tabelas só lidas na tela (14):** `D116` (10×, `prog/srv/srv002/form.php:799` e mais 9), `D024` (9×, `prog/srv/srv002/form.php:798` e mais 8), `D049` (8×, `prog/srv/srv002/form.php:201` e mais 7), `D009` (8×, `prog/srv/srv002/form.php:202` e mais 7), `D109` (8×, `prog/srv/srv002/form_func-ajax.php:160` e mais 7), `C007` (8×, `prog/srv/srv002/grid.php:79` e mais 7), `D110` (7×, `prog/srv/srv002/grid.php:77` e mais 6), `D037` (6×, `prog/srv/srv002/form.php:204` e mais 5), `D082` (4×, `prog/srv/srv002/form.php:203` e mais 3), `D001` (4×, `prog/srv/srv002/form.php:803` e mais 3), `D115` (4×, `prog/srv/srv002/form.php:859` e mais 3), `T169` (2×, `prog/srv/srv002/grid.php:1028` e mais 1), `D112` (1×, `prog/srv/srv002/form_func-ajax.php:193`), `T171` (1×, `prog/srv/srv002/grid_func-callback.php:320`)
- **Classes e métodos chamados:** `SRV002` (266 linhas): `verificaServicoNovo` (`SRV002.php:187`), `controleServico` (`SRV002.php:13`), `insereServico` (`SRV002.php:146`); `SRV003` (625 linhas): `iniciarServico` (`SRV003.php:15`), `cancelarServicoGeral` (`SRV003.php:352`), `pausarServico` (`SRV003.php:297`), `concluirServico` (`SRV003.php:393`), `cancelarServico` (`SRV003.php:450`), `reiniciarServico` (`SRV003.php:506`); `VEN002` (10343 linhas) (só incluída)
- **Tabelas gravadas pelos métodos chamados:** `T147` (SRV002::controleServico, SRV002::verificaServicoNovo, SRV003::cancelarServico…), `T148` (SRV002::insereServico), `T149` (SRV002::insereServico), `T169` (SRV002::controleServico, SRV003::cancelarServico, SRV003::cancelarServicoGeral…)
- **Mensagens e validações visíveis (4):**
  - "Registro Alterado com Sucesso!" (`prog/srv/srv002/form.php:87`)
  - "Registro Incluido com Sucesso!" (`prog/srv/srv002/form.php:107`)
  - "Alterações Salvas com sucesso" (`prog/srv/srv002/form.php:891`)
  - "{$retorno}" (`prog/srv/srv002/form.php:1216`)
- **Abre as telas:** `cad/cad010`
- **Grafo (Graphify):** 42 nós, 33 funções definidas: `campoProcuraServico()`, `carregaVeiculo()`, `colunaEditarCallbackJS_T147_Prioridade()`, `gridLinha()`, `srv002MarcarConferido()`, `srv002MarcarReprovado()` …

### `prog/srv/srv003` — Operação

- **Menu:** Serviços › Operação, acesso C029 nº 28 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:208`)
- **Menu:** Serviços › Operação, acesso C029 nº 28 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:209`)
- **Arquivos:** 6 arquivos. 2.061 linhas. `outros.php` (769), `grid.php` (624), `tab.php` (245), `outro_func-ajax.php` (210), `content.php` (174), `grid_func-callback.php` (39)
- **Abas (tab.php):** Meus Serviços (`tab.php:25`), Fila de Serviços (`tab.php:53`), Processos (`tab.php:73`), Materiais (`tab.php:93`), Senha (`tab.php:121`), Detalhes do Serviço (`tab.php:149`), Serviço em Execução (`tab.php:177`), Aprovar (`tab.php:205`), Detalhes do Serviço (`tab.php:233`)
- **Ações (`case` do roteador, 39):** `srv003ContentOperacao`, `srv003ContentOperacaoPendentes`, `contentProcessosServicos`, `contentMateriaisServicos`, `srv003ContentLogin`, `srv003ContentIniciarServico`, `srv003ContentExecucaoServico`, `srv003ContentOperacaoAprovar`, `srv003ContentAprovarServico`, `srv003GridOperacao`, `srv003GridOperacaoPendentes`, `srv003GridProcessosServicos`, `srv003GridMateriaisServicos`, `srv003GridOperacaoAprovar`, `iniciarServico`, `ImprimirServico`, `pausarServico`, `concluirServico`, `cancelarServico`, `reiniciarServico` …
- **Tabelas só lidas na tela (15):** `T147` (12×, `prog/srv/srv003/content.php:113` e mais 11), `T005` (7×, `prog/srv/srv003/grid.php:48` e mais 6), `D109` (7×, `prog/srv/srv003/grid.php:162` e mais 6), `D024` (6×, `prog/srv/srv003/grid.php:49` e mais 5), `D116` (6×, `prog/srv/srv003/grid.php:50` e mais 5), `C007` (6×, `prog/srv/srv003/grid.php:51` e mais 5), `D110` (4×, `prog/srv/srv003/grid.php:160` e mais 3), `T148` (4×, `prog/srv/srv003/grid.php:314` e mais 3), `T149` (4×, `prog/srv/srv003/grid.php:404` e mais 3), `T006` (4×, `prog/srv/srv003/grid_func-callback.php:30` e mais 3), `D009` (4×, `prog/srv/srv003/grid_func-callback.php:31` e mais 3), `D049` (4×, `prog/srv/srv003/grid_func-callback.php:32` e mais 3), `D001` (4×, `prog/srv/srv003/grid_func-callback.php:33` e mais 3), `D115` (3×, `prog/srv/srv003/grid.php:161` e mais 2), `D111` (1×, `prog/srv/srv003/grid.php:407`)
- **Classes e métodos chamados:** `SRV003` (625 linhas): `iniciarServico` (`SRV003.php:15`), `imprimirServico` (`SRV003.php:183`), `pausarServico` (`SRV003.php:297`), `concluirServico` (`SRV003.php:393`), `cancelarServico` (`SRV003.php:450`), `reiniciarServico` (`SRV003.php:506`), `aprovarServico` (`SRV003.php:561`), `reprovarServico` (`SRV003.php:597`)
- **Tabelas gravadas pelos métodos chamados:** `T147` (SRV003::aprovarServico, SRV003::cancelarServico, SRV003::concluirServico…), `T169` (SRV003::aprovarServico, SRV003::cancelarServico, SRV003::concluirServico…)
- **Grafo (Graphify):** 13 nós, 7 funções definidas: `validaCampoCliente()`, `validaCampoPrioridade()`, `validaCampoProduto()`, `validaCampoStatus()`, `validaCampoVeiculoSrv003()`, `str_pad()` …. Chamada por: `.exportarAPI001()` (bibliotecas/classes/API001.php), `.importarCSV()` (bibliotecas/classes/CAD001.php), `.importarCSV()` (bibliotecas/classes/CAD002.php), `.importarCSVFornecedorProduto()` (bibliotecas/classes/CAD002.php), `.obterNovoCodigoEAN()` (bibliotecas/classes/CAD002.php)

### `prog/srv/srv004` — Inspeção de Trocas

- **Menu:** Serviços › Inspeção de Trocas, acesso C029 nº 96 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:205`)
- **Arquivos:** 6 arquivos. 286 linhas. `grid.php` (136), `grid_func-callback.php` (34), `tab.php` (33), `grid_func-ajax.php` (29), `content.php` (28), `grid_func-js.php` (26)
- **Abas (tab.php):** Inspeção de Tocas (`tab.php:18`)
- **Ações (`case` do roteador, 4):** `srv004contentInspecao`, `srv004GridInspecao`, `marcarInspecionado`, `srv004TabInspecao`
- **Tabelas só lidas na tela (5):** `T138` (1×, `prog/srv/srv004/grid.php:38`), `D009` (1×, `prog/srv/srv004/grid.php:41`), `D049` (1×, `prog/srv/srv004/grid.php:42`), `D001` (1×, `prog/srv/srv004/grid.php:43`), `D082` (1×, `prog/srv/srv004/grid.php:44`)
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída); `SRV004` (135 linhas): `aprovarTroca` (`SRV004.php:13`)
- **Tabelas gravadas pelos métodos chamados:** `T001` (SRV004::aprovarTroca), `T138` (SRV004::aprovarTroca)
- **Abre as telas:** `cad/cad049`
- **Grafo (Graphify):** 9 nós, 3 funções definidas: `campoMarcarInspecionado()`, `retornaOperacaoTroca()`, `srv004MarcarInspecionado()`

