# Fichas de tela: `prog/fis` — Fiscal

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `fis/fis001` | CFOP | 41 | 6 | 3021 | 3 | 5 |
| `fis/fis002` | ICMS | 43 | 5 | 1449 | 3 | 2 |
| `fis/fis003` | NCM | 42 | 8 | 1266 | 2 | 3 |
| `fis/fis004` | NCM por Grupo | 44 | 4 | 378 | 1 | 3 |
| `fis/fis005` | Cidades | 45 | 6 | 1081 | 2 | 3 |
| `fis/fis006` | Exportar Sintegra | 46 | 4 | 303 | 1 | 0 |
| `fis/fis007` | Exportar XML NF-e | 47, 167 | 3 | 346 | 1 | 1 |
| `fis/fis008` | Exportar TXT NF-e | 48 | 4 | 247 | 1 | 0 |
| `fis/fis009` | Países | 85 | 4 | 394 | 1 | 0 |
| `fis/fis010` | Simples Nacional | 89 | 4 | 413 | 1 | 0 |
| `fis/fis011` | SPED Contribuições | 160, 174 | 6 | 561 | 0 | 1 |
| `fis/fis012` | MDF-e | 242, 242* | 10 | 7818 | 15 | 11 |
| `fis/fis013` | Outros | 254 | 4 | 918 | 3 | 1 |

Total: 13 telas. 18.195 linhas. 31 tabelas gravadas. 52 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/fis/fis001` — CFOP

- **Menu:** Fiscal › CFOP, acesso C029 nº 41 "CFOP" (`bibliotecas/menu.php:79`)
- **Arquivos:** 6 arquivos. 3.021 linhas. `form.php` (1713), `grid_func-callback.php` (430), `grid_cfop.php` (348), `grid.php` (265), `content.php` (134), `tab.php` (131)
- **Abas (tab.php):** Vendas (`tab.php:21`), Compras (`tab.php:22`), Transferências (`tab.php:23`), Outras saídas (`tab.php:24`), Outras entradas (`tab.php:25`), Devolução compras (`tab.php:26`), Devolução vendas (`tab.php:27`), Geral (`tab.php:28`), Inativas (`tab.php:29`), Regras venda (`tab.php:30`), Regras compra (`tab.php:31`), Geral (`tab.php:59`) …
- **Ações (`case` do roteador, 22):** `fis001content01`, `fis001content02`, `fis001content03`, `fis001content03Compras`, `fis001form01`, `fis001form02`, `fis001form02Compras`, `fis001GridRegras`, `fis001GridRegrasCompras`, `fis001grid01`, `fis001grid02`, `fis001grid03`, `fis001grid04`, `fis001grid05`, `fis001grid06`, `fis001grid07`, `fis001GridGeral`, `fis001gridInativas`, `fis001tab01`, `fis001tab02` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D006` (fotografia:3314): form em `prog/fis/fis001/form.php:70`
  - `D137` (fotografia:7846): form em `prog/fis/fis001/form.php:1208`
  - `D145` (fotografia:8053): form em `prog/fis/fis001/form.php:1493`
- **Tabelas só lidas na tela (5):** `D154` (10×, `prog/fis/fis001/form.php:218` e mais 9), `D059` (9×, `prog/fis/fis001/grid_cfop.php:160` e mais 8), `D014` (9×, `prog/fis/fis001/grid_cfop.php:161` e mais 8), `D073` (9×, `prog/fis/fis001/grid_cfop.php:163` e mais 8), `C004` (1×, `prog/fis/fis001/form.php:982`)
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `retornaDestinoProdutos` (`CAD002.php:12359`); `FIS001` (87 linhas): `verificaCampoEstados` (`FIS001.php:13`); `ReformaTributaria` (482 linhas) (só incluída)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D137_Quantidade_Regras` (`prog/fis/fis001/grid.php:16`), `D145_Quantidade_Regras` (`prog/fis/fis001/grid.php:147`)
- **Chaves C031 lidas (2):** `descricaoCFOPExibida`, `venderEstoqueMatriz`
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/fis/fis001/form.php:1112`)
  - "Registro Incluido com Sucesso!" (`prog/fis/fis001/form.php:1132`)
  - "Campo "Estados Específicos" preenchido de forma incorreta Preencha com as siglas dos estados separados por vírgulas, ex: PR,SC,RS" (`prog/fis/fis001/form.php:1413`)
- **Grafo (Graphify):** 49 nós, 43 funções definidas: `{closure#1}()`, `{closure#2}()`, `{closure#3}()`, `{closure#4}()`, `baseCalculoCOFINS()`, `baseCalculoPIS()` …
- **Aberta por:** `ven/ven004`

### `prog/fis/fis002` — ICMS

- **Menu:** Fiscal › ICMS, acesso C029 nº 43 "ICMS" (`bibliotecas/menu.php:81`)
- **Arquivos:** 5 arquivos. 1.449 linhas. `form.php` (580), `grid.php` (547), `grid_func-callback.php` (152), `tab.php` (95), `content.php` (75)
- **Abas (tab.php):** ICMS (`tab.php:19`), ICMS Compras (`tab.php:21`), ICMS Importação (`tab.php:23`), ICMS Transporte (`tab.php:24`), ICMS (`tab.php:44`), Exceção (`tab.php:65`), ICMS Transporte (`tab.php:82`)
- **Ações (`case` do roteador, 16):** `fis002ContentICMS`, `fis002ContentICMSIncluir`, `fis002ContentICMSIncluirExcessao`, `fis002ContentICMSTransporte`, `fis002FormICMS`, `fis002FormICMSExcessao`, `fis002FormICMSTransporte`, `fis002GridIcms`, `fis002GridIcmsCompra`, `fis002GridIcmsImportado`, `fis002GridExcessoesIcms`, `fis002GridIcmsTransporte`, `fis002TabIcms`, `fis002TabIcmsIncluir`, `fis002TabIcmsIncluirExcessao`, `fis002TabIcmsTransporte`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D053` (fotografia:5634): form em `prog/fis/fis002/form.php:32`
  - `D143` (fotografia:7998): form em `prog/fis/fis002/form.php:190`
  - `D196` (fotografia:9799): form em `prog/fis/fis002/form.php:453`
- **Tabelas só lidas na tela (2):** `D018` (5×, `prog/fis/fis002/grid.php:33` e mais 4), `D006` (1×, `prog/fis/fis002/grid.php:355`)
- **Classes e métodos chamados:** `FIS002` (31 linhas): `verificaCampoEstados` (`FIS002.php:13`)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/fis/fis002/form.php:90`)
  - "Registro Incluido com Sucesso!" (`prog/fis/fis002/form.php:110`)
  - "Campo "Estados Específicos" preenchido de forma incorreta Preencha com as siglas dos estados separados por vírgulas, ex: PR,SC,RS" (`prog/fis/fis002/form.php:365`)
- **Abre as telas:** `fis/fis003`
- **Grafo (Graphify):** 20 nós, 15 funções definidas: `campoAtiva()`, `campoCOFINS()`, `campoDentroForaEstado()`, `campoEstoque()`, `campoFinanceiro()`, `campoICMS()` …

### `prog/fis/fis003` — NCM

- **Menu:** Fiscal › NCM, acesso C029 nº 42 "NCM" (`bibliotecas/menu.php:80`)
- **Arquivos:** 8 arquivos. 1.266 linhas. `form.php` (621), `grid.php` (334), `tab.php` (90), `content.php` (85), `outro.php` (45), `form_func-ajax.php` (36), `grid_func-ajax.php` (32), `form_func-js.php` (23)
- **Abas (tab.php):** Regras NCM desta empresa (`tab.php:20`), Cadastro NCM - Vale para todas empresas (`tab.php:21`), NCM (`tab.php:45`), Reforma (`tab.php:48`), NCM (`tab.php:77`)
- **Ações (`case` do roteador, 15):** `fis003ContentNcm`, `fis003ContentD005`, `fis003ContentNCMIncluir`, `fis003ContentNCMAlterar`, `fis003FormNCM`, `fis003FormNCMAlterar`, `fis003FormImportacao`, `atualizarCamposNCM`, `fis003GridNcm`, `fis003GridD005`, `processarNCM`, `fis003CabecalhoNCM`, `fis003TabNcm`, `fis003ContentNCMIncluir`, `fis003ContentNCMAlterar`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D005` (fotografia:3266): UPDATE/form em `prog/fis/fis003/form.php:23`, `prog/fis/fis003/form.php:506` e mais 1
  - `D149` (fotografia:8171): UPDATE/form em `prog/fis/fis003/form.php:246`, `prog/fis/fis003/grid_func-ajax.php:23`
- **Tabelas só lidas na tela (3):** `D088` (2×, `prog/fis/fis003/grid.php:56` e mais 1), `D001` (1×, `prog/fis/fis003/form.php:147`), `T153` (1×, `prog/fis/fis003/form_func-ajax.php:17`)
- **Classes e métodos chamados:** `FIS003` (197 linhas): `verificarNCMDuplicadas` (`FIS003.php:29`); `ReformaTributaria` (482 linhas) (só incluída)
- **Mensagens e validações visíveis (4):**
  - "NCM não pode ser inativada, '.$texto.'. Desvincule e tente novamente" (`prog/fis/fis003/form.php:151`)
  - "Registro Alterado com Sucesso!" (`prog/fis/fis003/form.php:155`)
  - "Registro Incluido com Sucesso!" (`prog/fis/fis003/form.php:182`)
  - "Iniciar processo?" (`prog/fis/fis003/grid.php:103`)
- **Grafo (Graphify):** 12 nós, 4 funções definidas: `{closure#1}()`, `{closure#2}()`, `campoProcuraNCM()`, `gridLinha()`
- **Aberta por:** `fis/fis002`

### `prog/fis/fis004` — NCM por Grupo

- **Menu:** Fiscal › NCM por Grupo, acesso C029 nº 44 "NCM por Grupo" (`bibliotecas/menu.php:82`)
- **Arquivos:** 4 arquivos. 378 linhas. `form.php` (148), `grid.php` (143), `content.php` (44), `tab.php` (43)
- **Abas (tab.php):** Grupo/Aplicação/Classificação (`tab.php:15`), Grupo/Aplicação/Classificação (`tab.php:32`)
- **Ações (`case` do roteador, 6):** `fis004ContentNcmPorGrupo`, `fis004ContentNCMporGrupoIncluir`, `fis004FormGrupo`, `fis004GridGrupo`, `fis004TabNcmPorGrupo`, `fis004TabNcmPorGrupoIncluir`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T025` (fotografia:18207): form em `prog/fis/fis004/form.php:27`
- **Tabelas só lidas na tela (3):** `D005` (1×, `prog/fis/fis004/grid.php:60`), `D002` (1×, `prog/fis/fis004/grid.php:61`), `D015` (1×, `prog/fis/fis004/grid.php:62`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/fis/fis004/form.php:65`)
  - "Registro Incluido com Sucesso!" (`prog/fis/fis004/form.php:85`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/fis/fis005` — Cidades

- **Menu:** Fiscal › Cidades, acesso C029 nº 45 "Cidades" (`bibliotecas/menu.php:84`)
- **Arquivos:** 6 arquivos. 1.081 linhas. `form.php` (455), `grid.php` (341), `tab.php` (125), `content.php` (118), `grid_func-ajax.php` (29), `grid_func-callback.php` (13)
- **Abas (tab.php):** Cidades (`tab.php:23`), CEP (`tab.php:24`), Cidade (`tab.php:42`), Cidade (`tab.php:59`), Bairros (`tab.php:60`), CEP (`tab.php:77`), CEP (`tab.php:94`), Bairro (`tab.php:112`)
- **Ações (`case` do roteador, 19):** `fis005ContentPrincipal`, `fis005contentCadastroCidade`, `fis005contentCadastroCidadeEdicao`, `fis005contentCadastroCep`, `fis005contentCadastroCepEdicao`, `fis005contentCadastroBairro`, `fis005FormCadastroCidade`, `fis005FormCadastroCep`, `fis005FormCadastroBairro`, `fis005GridCidades`, `fis005GridCep`, `fis005GridBairros`, `excluirCep`, `fis005TabPrincipal`, `fis005TabCadastroCidade`, `fis005TabCadastroCidadeEdicao`, `fis005TabCadastroCep`, `fis005TabCadastroCepEdicao`, `fis005TabCadastroBairro`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D020` (fotografia:4223): form em `prog/fis/fis005/form.php:41`
  - `D139` (fotografia:7918): form em `prog/fis/fis005/form.php:337`
- **Tabelas só lidas na tela (3):** `D018` (2×, `prog/fis/fis005/form.php:60` e mais 1), `D022` (1×, `prog/fis/fis005/grid.php:25`), `D084` (1×, `prog/fis/fis005/grid.php:250`)
- **Chaves C031 lidas (1):** `obrigatorioFiltroTelaTransicao`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/fis/fis005/form.php:97`)
  - "Registro Incluido com Sucesso!" (`prog/fis/fis005/form.php:117`)
- **Grafo (Graphify):** 7 nós, 1 funções definidas: `excluirCep()`
- **Aberta por:** `cad/cad001`

### `prog/fis/fis006` — Exportar Sintegra

- **Menu:** Fiscal › Exportar Sintegra, acesso C029 nº 46 "Exportar Sintegra" (`bibliotecas/menu.php:86`)
- **Arquivos:** 4 arquivos. 303 linhas. `form.php` (176), `tab.php` (54), `content.php` (50), `form_func-ajax.php` (23)
- **Abas (tab.php):** Exportar Sintegra (`tab.php:23`), Cidade (`tab.php:41`)
- **Ações (`case` do roteador, 6):** `fis006ContentExportarSintegra`, `fis005contentCadastroCidade`, `fis006FormExportarSintegra`, `fis006abrirSintegra`, `fis006TabExportarSintegra`, `fis005TabCadastroCidade`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D020` (fotografia:4223): form em `prog/fis/fis006/form.php:42`
- **Classes e métodos chamados:** `FIS006` (2531 linhas): `exportarSintegra` (`FIS006.php:15`)
- **Mensagens e validações visíveis (1):**
  - "Arquivo selecionado não é referente ao período informado!" (`prog/fis/fis006/form.php:113`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/fis/fis007` — Exportar XML NF-e

- **Menu:** Fiscal › Exportar XML NF-e, acesso C029 nº 47 "Exportar XML NF-e" (`bibliotecas/menu.php:87`)
- **Menu:** Fiscal › Exportar XML NF-e (Entrada), acesso C029 nº 167 "Exportar XML NF-e (Entrada)" (`bibliotecas/menu.php:89`)
- **Arquivos:** 3 arquivos. 346 linhas. `form.php` (229), `content.php` (63), `tab.php` (54)
- **Abas (tab.php):** Exportar XML NF-e (`tab.php:23`), Cidade (`tab.php:41`)
- **Ações (`case` do roteador, 7):** `fis007ContentExportarXMLNFe`, `fis005contentCadastroCidade`, `fis007ContentExportarXMLNFeEntrada`, `fis007FormExportarXMLNFe`, `fis007FormExportarXMLNFeEntrada`, `fis007TabExportarXMLNFe`, `fis005TabCadastroCidade`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D020` (fotografia:4223): form em `prog/fis/fis007/form.php:37`
- **Tabelas só lidas na tela (1):** `D018` (1×, `prog/fis/fis007/form.php:62`)
- **Classes e métodos chamados:** `FIS007` (423 linhas): `exportarXMLNfe` (`FIS007.php:14`), `exportarXMLNfeEntrada` (`FIS007.php:256`)
- **Grafo (Graphify):** 3 nós, 0 funções definidas

### `prog/fis/fis008` — Exportar TXT NF-e

- **Menu:** Fiscal › Exportar TXT NF-e, acesso C029 nº 48 "Exportar TXT NF-e" (`bibliotecas/menu.php:88`)
- **Arquivos:** 4 arquivos. 247 linhas. `form.php` (120), `tab.php` (54), `content.php` (50), `form_func-ajax.php` (23)
- **Abas (tab.php):** Exportar XML NF-e (`tab.php:23`), Cidade (`tab.php:41`)
- **Ações (`case` do roteador, 6):** `fis008ContentExportarTXTNFe`, `fis005contentCadastroCidade`, `fis008FormExportarTXTNFe`, `fis008abrirTXT`, `fis008TabExportarTXTNFe`, `fis005TabCadastroCidade`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D020` (fotografia:4223): form em `prog/fis/fis008/form.php:42`
- **Classes e métodos chamados:** `FIS008` (1277 linhas): `exportarTXTNfe` (`FIS008.php:14`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/fis/fis009` — Países

- **Menu:** Fiscal › Países, acesso C029 nº 85 "Países" (`bibliotecas/menu.php:85`)
- **Arquivos:** 4 arquivos. 394 linhas. `form.php` (155), `grid.php` (135), `tab.php` (54), `content.php` (50)
- **Abas (tab.php):** Países (`tab.php:23`), País (`tab.php:41`)
- **Ações (`case` do roteador, 6):** `fis009ContentPrincipal`, `fis009contentCadastroPais`, `fis009FormCadastroPais`, `fis009GridPaises`, `fis009TabPrincipal`, `fis009TabCadastroPais`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D030` (fotografia:4985): form em `prog/fis/fis009/form.php:41`
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/fis/fis009/form.php:80`)
  - "Registro Incluido com Sucesso!" (`prog/fis/fis009/form.php:100`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/fis/fis010` — Simples Nacional

- **Menu:** Fiscal › Simples Nacional, acesso C029 nº 89 "Simples Nacional" (`bibliotecas/menu.php:83`)
- **Arquivos:** 4 arquivos. 413 linhas. `form.php` (165), `grid.php` (143), `tab.php` (54), `content.php` (51)
- **Abas (tab.php):** Simples Nacional (`tab.php:23`), Simples Nacional (`tab.php:41`)
- **Ações (`case` do roteador, 6):** `fis010ContentPrincipal`, `fis010contentCadastroSimplesNacional`, `fis010FormCadastroSimplesNacional`, `fis010GridSimplesNacional`, `fis010TabPrincipal`, `fis010TabCadastroSimplesNacional`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T134` (fotografia:22306): form em `prog/fis/fis010/form.php:41`
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/fis/fis010/form.php:89`)
  - "Registro Incluido com Sucesso!" (`prog/fis/fis010/form.php:109`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas
- **Aberta por:** `fis/fis011`

### `prog/fis/fis011` — SPED Contribuições

- **Menu:** Fiscal › SPED Contribuições, acesso C029 nº 160 "SPED Contribuições" (`bibliotecas/menu.php:93`)
- **Menu:** Fiscal › SPED Fiscal, acesso C029 nº 174 "SPED Contribuições" (`bibliotecas/menu.php:94`)
- **Arquivos:** 6 arquivos. 561 linhas. `form.php` (325), `grid.php` (128), `content.php` (46), `tab.php` (25), `download.php` (21), `outro.php` (16)
- **Abas (tab.php):** SPED Contribuições (`tab.php:12`)
- **Ações (`case` do roteador, 7):** `fis011ContentSpedFiscal`, `fis011ContentPrincipal`, `fis011FormSpedContribuicoes`, `fis011FormSpedFiscal`, `fis010GridSimplesNacional`, `fis011LogoSped`, `fis011TabPrincipal`
- **Tabelas só lidas na tela (1):** `T134` (1×, `prog/fis/fis011/grid.php:46`)
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída); `FIS011` (65 linhas) (só incluída)
- **Chaves C031 lidas (1):** `SeparaProdutoPorEmpresa`
- **Mensagens e validações visíveis (3):**
  - "Para uma retificação, o número do recibo deve estar preenchido!" (`prog/fis/fis011/form.php:107`)
  - "Os campos data inicial e data final devem ser preenchidos!" (`prog/fis/fis011/form.php:110`)
  - "O campo contador deve ser preenchido!" (`prog/fis/fis011/form.php:297`)
- **Abre as telas:** `fis/fis010`
- **Grafo (Graphify):** 6 nós, 0 funções definidas

### `prog/fis/fis012` — MDF-e

- **Menu:** Configuração › MDF-e, acesso C029 nº 242 "MDF-e" (`bibliotecas/menu.php:123`)
- **Menu:** Fiscal › MDF-e, acesso C029 nº 242 "MDF-e" (item comentado no Menu) (`bibliotecas/menu.php:223`)
- **Arquivos:** 10 arquivos. 7.818 linhas. `form.php` (3014), `grid.php` (2051), `content.php` (857), `form_func-ajax.php` (467), `tab.php` (460), `grid_func-callback.php` (392), `grid_func-ajax.php` (295), `form_func-js.php` (153), `outro.php` (74), `grid_func-js.php` (55)
- **Abas (tab.php):** MDF-e (`tab.php:14`), Enviar MDF-e para Sefaz (`tab.php:15`), Enviar (`tab.php:34`), Encerrar (`tab.php:35`), Encerrados (`tab.php:36`), Cabeçalho (`tab.php:72`), Documentos (`tab.php:78`), Cabeçalho (`tab.php:79`), Rodoviário (`tab.php:80`), Seguradora (`tab.php:81`), Produto Predominante (`tab.php:82`), Adicionais (`tab.php:83`) …
- **Ações (`case` do roteador, 106):** `fis012ContentPrincipal`, `fis012ContentAlterarDescarregamento`, `fis012contentMDFeSefaz`, `fis012ContentLacres`, `fis012ContentLacresModalRodoviario`, `fis012ContentTecnico`, `fis012MdfeCadastro`, `fis012ContentDocumentos`, `fis012ContentCadastroNfeMdfe`, `fis012ContentAlterarNfeMdfe`, `fis012ContentCancelarMdfe`, `fis012ContentRodoviarioVeiculo`, `fis012ContentIncluirVeiculoModal`, `fis012ContentConfiguracaoMDFe`, `fis012ContentCadastroCTeMdfe`, `fis012ContentAlterarCTeMdfe`, `fis012GridInfoPgtoFrete`, `fis012FormInfoPgtoFrete`, `fis012AlterarInfoPgtoFrete`, `fis012FormInfoPgtoFreteComponente` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D174` (fotografia:9301): form em `prog/fis/fis012/form.php:1872`
  - `T276` (fotografia:27190): UPDATE/form em `prog/fis/fis012/form.php:32`, `prog/fis/fis012/form.php:1201` e mais 5
  - `T278` (fotografia:27302): form em `prog/fis/fis012/form.php:283`
  - `T279` (fotografia:27324): form em `prog/fis/fis012/form.php:1753`
  - `T280` (fotografia:27347): form em `prog/fis/fis012/form.php:1969`
  - `T281` (fotografia:27367): form em `prog/fis/fis012/form.php:735`
  - `T282` (fotografia:27388): form em `prog/fis/fis012/form.php:879`
  - `T283` (fotografia:27409): UPDATE/form em `prog/fis/fis012/form.php:439`, `prog/fis/fis012/form.php:586` e mais 1
  - `T284` (fotografia:27448): form em `prog/fis/fis012/form.php:137`, `prog/fis/fis012/form.php:1033` e mais 1
  - `T286` (fotografia:27510): form em `prog/fis/fis012/form.php:2083`
  - `T287` (fotografia:27529): form em `prog/fis/fis012/form.php:2326`
  - `T288` (fotografia:27548): form em `prog/fis/fis012/form.php:2209`
  - `T318` (fotografia:28796): form em `prog/fis/fis012/form.php:2593`
  - `T319` (fotografia:28823): form em `prog/fis/fis012/form.php:2748`
  - `T320` (fotografia:28844): form em `prog/fis/fis012/form.php:2895`
- **Tabelas só lidas na tela (11):** `D018` (21×, `prog/fis/fis012/form.php:1223` e mais 20), `D020` (14×, `prog/fis/fis012/form.php:1077` e mais 13), `T007` (6×, `prog/fis/fis012/form.php:1013` e mais 5), `D171` (6×, `prog/fis/fis012/grid.php:366` e mais 5), `C004` (4×, `prog/fis/fis012/form.php:1222` e mais 3), `D024` (4×, `prog/fis/fis012/form_func-ajax.php:92` e mais 3), `T285` (4×, `prog/fis/fis012/grid.php:1239` e mais 3), `D170` (3×, `prog/fis/fis012/form.php:1225` e mais 2), `D109` (3×, `prog/fis/fis012/form_func-ajax.php:173` e mais 2), `D148` (2×, `prog/fis/fis012/form_func-ajax.php:93` e mais 1), `D172` (1×, `prog/fis/fis012/form_func-ajax.php:185`)
- **Classes e métodos chamados:** `FIS012` (1599 linhas): `cancelarMdfe` (`FIS012.php:1058`), `alterarLocalDescarregamento` (`FIS012.php:1023`), `incluirMDFe` (`FIS012.php:744`), `finalizarMDFe` (`FIS012.php:1224`), `gerarMDFeCabecalho` (`FIS012.php:8`), `excluirMDFe` (`FIS012.php:1417`)
- **Tabelas gravadas pelos métodos chamados:** `T007` (FIS012::gerarMDFeCabecalho), `T276` (FIS012::cancelarMdfe, FIS012::finalizarMDFe, FIS012::gerarMDFeCabecalho…), `T278` (FIS012::gerarMDFeCabecalho, FIS012::incluirMDFe), `T279` (FIS012::gerarMDFeCabecalho, FIS012::incluirMDFe), `T281` (FIS012::gerarMDFeCabecalho), `T282` (FIS012::gerarMDFeCabecalho), `T283` (FIS012::gerarMDFeCabecalho, FIS012::incluirMDFe), `T284` (FIS012::alterarLocalDescarregamento, FIS012::gerarMDFeCabecalho)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T276_Gravar_Totalizacao` (`prog/fis/fis012/form.php:1131` e mais 1)
- **Chaves C031 lidas (1):** `separarClientePorEmpresa`
- **Mensagens e validações visíveis (23):**
  - "Deseja excluir o MDF-e?   Este processo irá excluir o MDF-e e seus itens." (`prog/fis/fis012/content.php:211`)
  - "MDFe Finalizado" (`prog/fis/fis012/content.php:221`)
  - "MDF-e Cancelado!" (`prog/fis/fis012/form.php:75`)
  - "Não identificado número do MDF-e!" (`prog/fis/fis012/form.php:179`)
  - "Registros Alterados com Sucesso!" (`prog/fis/fis012/form.php:193`)
  - "Motorista já vinculado a este MDF-e" (`prog/fis/fis012/form.php:355`)
  - "Só é possível cadastrar um veículo do tipo tração por MDf-e." (`prog/fis/fis012/form.php:578`)
  - "Só é possível cadastrar até três veículos do tipo reboque por MDf-e." (`prog/fis/fis012/form.php:580`)
  - … mais 15
- **Abre as telas:** `cad/cad064`, `cad/cad066`, `cad/cad010`, `ven/ven003`, `cad/cad065`
- **Grafo (Graphify):** 50 nós, 40 funções definidas: `alteraEstado()`, `alteraEstadoCabecalho()`, `alteraEstadoDescarregamento()`, `carregaDadosMotorista()`, `carregaDadosNfeMdfe()`, `carregaSeguradora()` …. Chama fora da tela: `.log()` (CAD002.php)
- **Aberta por:** `est/est002`, `ven/ven003`

### `prog/fis/fis013` — Outros

- **Menu:** Fiscal › Outros, acesso C029 nº 254 "Outros" (`bibliotecas/menu.php:95`)
- **Arquivos:** 4 arquivos. 918 linhas. `form.php` (428), `grid.php` (310), `tab.php` (98), `content.php` (82)
- **Abas (tab.php):** Tipo Produto (`tab.php:21`), Observações Fiscais (`tab.php:22`), Ajustes (`tab.php:23`), Tipo Produto (`tab.php:44`), Observação Fiscal (`tab.php:65`), Ajustes (`tab.php:86`)
- **Ações (`case` do roteador, 14):** `fis013ContentPrincipal`, `fis013ContentCadastroTipoProduto`, `fis013ContentCadastroObservacaoFiscais`, `fis013ContentCadastroAjuste`, `fis013FormTipoProduto`, `fis013FormObservacaoFiscal`, `fis013FormAjuste`, `fis013GridTipoProduto`, `fis013GridObservacoes`, `fis013GridAjustes`, `fis013TabPrincipal`, `fis013TabCadastroTipoProduto`, `fis013TabCadastroObservacaoFiscal`, `fis013TabCadastroAjuste`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D187` (fotografia:9596): form em `prog/fis/fis013/form.php:28`
  - `D188` (fotografia:9615): form em `prog/fis/fis013/form.php:166`
  - `D189` (fotografia:9635): form em `prog/fis/fis013/form.php:306`
- **Tabelas só lidas na tela (1):** `D018` (1×, `prog/fis/fis013/grid.php:223`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/fis/fis013/form.php:67`)
  - "Registro Incluido com Sucesso!" (`prog/fis/fis013/form.php:87`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

