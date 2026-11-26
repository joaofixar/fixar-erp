# Fichas de tela: `prog/cad` — Cadastros

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `cad/cad001` | Cliente/Fornecedor | 1 | 11 | 8326 | 8 | 24 |
| `cad/cad002` | Produtos | 5, 48* | 11 | 23827 | 24 | 60 |
| `cad/cad003` | Representantes | 3 | 8 | 989 | 2 | 4 |
| `cad/cad004` | Transportadoras | 2 | 6 | 1019 | 2 | 5 |
| `cad/cad006` |  |  | 1 | 53 | 0 | 2 |
| `cad/cad007` | &nbsp;&nbsp;> Certificados | 7* | 7 | 786 | 2 | 2 |
| `cad/cad008` | Bens | 30* | 7 | 1103 | 1 | 14 |
| `cad/cad009` | &nbsp;&nbsp;> Catálogos | 6* | 7 | 731 | 2 | 1 |
| `cad/cad010` | Funcionários | 10, 97* | 11 | 1507 | 3 | 9 |
| `cad/cad011` | Serviços - Competências | 72* | 7 | 459 | 1 | 0 |
| `cad/cad012` | Materiais | 26* | 7 | 454 | 1 | 1 |
| `cad/cad013` | Agenda de Contatos | 4 | 6 | 548 | 1 | 3 |
| `cad/cad014` | Bancos/Caixas | 8 | 5 | 867 | 2 | 1 |
| `cad/cad015` | Centro de Custos | 9 | 4 | 473 | 1 | 0 |
| `cad/cad016` | Supervisores/Vendedores | 11* | 7 | 526 | 1 | 0 |
| `cad/cad017` | Comex | 58 | 6 | 1549 | 4 | 1 |
| `cad/cad018` | Principal (rótulo da primeira aba) |  | 6 | 410 | 1 | 2 |
| `cad/cad019` | Produtos - Locações (rótulo da primeira aba) |  | 7 | 1072 | 2 | 5 |
| `cad/cad020` | Produtos (rótulo da primeira aba) |  | 7 | 4645 | 5 | 28 |
| `cad/cad021` | Portadores a Pagar (rótulo da primeira aba) |  | 4 | 390 | 1 | 0 |
| `cad/cad022` | Portadores a Receber (rótulo da primeira aba) |  | 7 | 682 | 1 | 2 |
| `cad/cad023` | Produtos | 63, 165*, 166* | 5 | 2720 | 10 | 0 |
| `cad/cad024` | Produtos - Unidades (rótulo da primeira aba) |  | 4 | 360 | 1 | 0 |
| `cad/cad025` | Produtos - Marcas (rótulo da primeira aba) |  | 4 | 386 | 1 | 1 |
| `cad/cad026` | Produtos - Grupos (rótulo da primeira aba) |  | 4 | 527 | 1 | 2 |
| `cad/cad027` | Produtos - Setores (rótulo da primeira aba) |  | 4 | 367 | 1 | 1 |
| `cad/cad028` | Produtos - sub-grupos (rótulo da primeira aba) |  | 4 | 388 | 1 | 2 |
| `cad/cad029` | Produtos - Aplicações (rótulo da primeira aba) |  | 4 | 358 | 1 | 0 |
| `cad/cad030` | Usuários/Perfis | 84, 86 | 6 | 3664 | 7 | 6 |
| `cad/cad031` | Empresas | 57 | 8 | 2593 | 3 | 4 |
| `cad/cad032` | Perfis (rótulo da primeira aba) |  | 8 | 1027 | 3 | 3 |
| `cad/cad033` | Cotação Moeda | 77* | 4 | 344 | 1 | 0 |
| `cad/cad034` | Plano de Contas | 12 | 8 | 3447 | 4 | 12 |
| `cad/cad035` | Recibos de Locação | 33* | 6 | 681 | 2 | 3 |
| `cad/cad036` | Bens - Checklist (conformidade) | 73* | 5 | 407 | 1 | 2 |
| `cad/cad037` | Bens - Especificações (inspeção) | 74* | 6 | 424 | 1 | 1 |
| `cad/cad038` | Checklist de Processos (rótulo da primeira aba) |  | 5 | 408 | 1 | 0 |
| `cad/cad039` | Certificados de Inspeção | 56* | 7 | 833 | 2 | 5 |
| `cad/cad040` | Certificados de Conformidade | 34* | 10 | 1096 | 3 | 9 |
| `cad/cad041` | Geral (rótulo da primeira aba) |  | 4 | 334 | 1 | 0 |
| `cad/cad042` | Bens - Tipos | 75* | 4 | 337 | 1 | 0 |
| `cad/cad043` | Bens - Especificações | 76* | 4 | 342 | 1 | 1 |
| `cad/cad044` | Textos de Documento | 80* | 7 | 706 | 1 | 0 |
| `cad/cad045` | Veículos - Especificações | 79* | 4 | 343 | 1 | 0 |
| `cad/cad046` | Geral (rótulo da primeira aba) |  | 4 | 383 | 1 | 0 |
| `cad/cad047` | Geral (rótulo da primeira aba) |  | 4 | 374 | 1 | 0 |
| `cad/cad048` | Veículos - Modelos | 90* | 4 | 373 | 1 | 0 |
| `cad/cad049` | Veículos - Cores | 91* | 4 | 374 | 1 | 0 |
| `cad/cad050` | Local Locação (rótulo da primeira aba) |  | 4 | 388 | 1 | 1 |
| `cad/cad051` | Etiquetas - Modelos | 119* | 7 | 1303 | 1 | 14 |
| `cad/cad052` | Composição (rótulo da primeira aba) |  | 10 | 1792 | 4 | 10 |
| `cad/cad053` | P.C.P. | 120, 120* | 10 | 859 | 2 | 7 |
| `cad/cad054` | Rotas | 158* | 4 | 372 | 1 | 0 |
| `cad/cad055` | Bens - Impressoras de CF | 159* | 5 | 371 | 1 | 1 |
| `cad/cad056` | Bancos (rótulo da primeira aba) |  | 4 | 290 | 1 | 0 |
| `cad/cad057` | Impressoras | 176* | 4 | 278 | 1 | 0 |
| `cad/cad058` | Geral (rótulo da primeira aba) |  | 6 | 7127 | 1 | 8 |
| `cad/cad059` | Moedas (rótulo da primeira aba) |  | 4 | 288 | 1 | 0 |
| `cad/cad060` | Variavéis IPV (rótulo da primeira aba) |  | 6 | 664 | 3 | 0 |
| `cad/cad061` | Pedidos (rótulo da primeira aba) |  | 10 | 17250 | 19 | 80 |
| `cad/cad062` | RMA (rótulo da primeira aba) |  | 4 | 599 | 2 | 0 |
| `cad/cad063` | Cliente - Grupos | 240 | 4 | 364 | 1 | 0 |
| `cad/cad064` | Veículos (rótulo da primeira aba) |  | 5 | 651 | 1 | 2 |
| `cad/cad065` | Lacres (rótulo da primeira aba) |  | 5 | 502 | 1 | 0 |
| `cad/cad066` | Seguradoras (rótulo da primeira aba) |  | 8 | 1249 | 3 | 2 |
| `cad/cad067` | Periféricos | 246 | 5 | 448 | 1 | 0 |
| `cad/cad068` | Nota Fiscal | 266 | 4 | 352 | 1 | 0 |

Total: 67 telas. 109.159 linhas. 143 tabelas gravadas. 226 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/cad/cad001` — Cliente/Fornecedor

- **Menu:** Cadastros › Cliente/Fornecedor, acesso C029 nº 1 "Cliente/Fornecedor" (`bibliotecas/menu.php:8`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 45 "Cidades" (`prog/cad/cad001/form.php:99`); 103 "Incluir/Excluir/Alterar Cliente/Fornecedor" (`prog/cad/cad001/form.php:47` e mais 5); 183 "Incluir/Excluir/Alterar Pré Cadastro Cliente/Fornecedor" (`prog/cad/cad001/form.php:47` e mais 2); 222 "Calcular Limite" (`prog/cad/cad001/grid.php:141`); 231 "Desabilitar Incluir/Excluir/Alterar na aba Cadastros" (`prog/cad/cad001/form.php:47` e mais 1); 234 "Desabilitar Incluir/Excluir/Alterar na aba Fiscal" (`prog/cad/cad001/form.php:84`); 235 "Desabilitar Incluir/Excluir/Alterar na aba Comercial" (`prog/cad/cad001/form.php:89`); 236 "Desabilitar Incluir/Excluir/Alterar na aba Financeiro" (`prog/cad/cad001/form.php:94`)
- **Arquivos:** 11 arquivos. 8.326 linhas. `form.php` (3880), `grid.php` (1670), `outro.php` (1412), `content.php` (574), `tab.php` (390), `grid_func-ajax.php` (152), `grid_func-callback.php` (133), `form_func-js.php` (64), `form_func-ajax.php` (47), `grid_func-js.php` (2), `modeloCSVcliente.csv` (2)
- **Abas (tab.php):** Clientes/Fornecedores (`tab.php:17`), Trabalho (`tab.php:18`), Inativos (`tab.php:19`), Contatos (`tab.php:20`), Importar CSV (`tab.php:21`), Cadastro (`tab.php:53`), Comercial (`tab.php:55`), Financeiro (`tab.php:56`), Fiscal (`tab.php:57`), Documentos (`tab.php:71`), Agenda (`tab.php:73`), Créditos (`tab.php:74`) …
- **Ações (`case` do roteador, 85):** `cad001ContentPrincipal`, `cad001contentCadastro`, `cad001contentClienteCRMVendedor`, `cad001ContentVeiculosIncluir`, `cad001ContentAbreAgenda`, `cad001ContentAgenda`, `cad001ContentAbreDocumentos`, `cad001ContentDocumentos`, `cad001ContentDocumentosVeiculos`, `cad001contentEmail`, `cad001ContentReferencias`, `cad001ContentPreCadastrarCliente`, `cad001ContentAlterarContato`, `cad001ContentImprimirClientes`, `cad001ContentImprimirEtiquetas`, `cad001ContentJuntar`, `cad001OrcamentoIncluirCliente`, `cad001PedidoIncluirCliente`, `cad001contentCadastroEndereco`, `cad001CotacaoIncluirFornecedor` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D013` (fotografia:3952): INSERT/form em `prog/cad/cad001/form.php:961`, `prog/cad/cad001/form.php:1135` e mais 4
  - `D024` (fotografia:4506): UPDATE/form em `prog/cad/cad001/form.php:51`, `prog/cad/cad001/form.php:643` e mais 9
  - `D061` (fotografia:5839): form em `prog/cad/cad001/form.php:1788`
  - `D085` (fotografia:6827): form em `prog/cad/cad001/form.php:1297`
  - `D087` (fotografia:6917): form em `prog/cad/cad001/form.php:1452`
  - `D116` (fotografia:7352): form em `prog/cad/cad001/form.php:749`
  - `D127` (fotografia:7599): form em `prog/cad/cad001/form.php:1618`
  - `D148` (fotografia:8131): form em `prog/cad/cad001/form.php:3418`
- **Tabelas só lidas na tela (24):** `C007` (24×, `prog/cad/cad001/grid.php:100` e mais 23), `D020` (17×, `prog/cad/cad001/form.php:346` e mais 16), `D018` (17×, `prog/cad/cad001/form.php:346` e mais 16), `T007` (14×, `prog/cad/cad001/outro.php:249` e mais 13), `D006` (14×, `prog/cad/cad001/outro.php:253` e mais 13), `T008` (8×, `prog/cad/cad001/outro.php:250` e mais 7), `T002` (5×, `prog/cad/cad001/outro.php:656` e mais 4), `T139` (4×, `prog/cad/cad001/form.php:936` e mais 3), `D084` (4×, `prog/cad/cad001/grid.php:95` e mais 3), `D046` (4×, `prog/cad/cad001/grid.php:97` e mais 3), `D021` (4×, `prog/cad/cad001/grid.php:98` e mais 3), `D009` (4×, `prog/cad/cad001/outro.php:251` e mais 3), `D049` (4×, `prog/cad/cad001/outro.php:252` e mais 3), `C004` (3×, `prog/cad/cad001/form.php:68` e mais 2), `D030` (2×, `prog/cad/cad001/grid.php:1587` e mais 1), `T003` (2×, `prog/cad/cad001/outro.php:84` e mais 1), `D007` (1×, `prog/cad/cad001/form.php:543`), `D024A` (1×, `prog/cad/cad001/form.php:637`), `D126` (1×, `prog/cad/cad001/grid.php:677`), `D013A` (1×, `prog/cad/cad001/grid.php:998`), `T165` (1×, `prog/cad/cad001/grid.php:1359`), `D131` (1×, `prog/cad/cad001/grid.php:1364`), `D001` (1×, `prog/cad/cad001/grid_func-ajax.php:118`), `C031` (1×, `prog/cad/cad001/outro.php:619`)
- **Classes e métodos chamados:** `CAD001` (1497 linhas): `imprimirEtiquetas` (`CAD001.php:59`), `importarCSV` (`CAD001.php:586`), `insereRegistrosT165` (`CAD001.php:34`), `updateCheckboxImprimir` (`CAD001.php:409`), `calcularLimite` (`CAD001.php:1307`)
- **Tabelas gravadas pelos métodos chamados:** `D024` (CAD001::calcularLimite, CAD001::importarCSV, CAD001::updateCheckboxImprimir), `T165` (CAD001::insereRegistrosT165)
- **Chaves C031 lidas (15):** `AbrirEdicaoClienteAposInsercao`, `curvaABCclientes`, `diasAtrasoPgtoClienteClasseA`, `diasAtrasoPgtoClienteClasseB`, `diasAtrasoPgtoClienteClasseC`, `diasAtrasoPgtoClienteClasseD`, `diasConsiderarAtraso`, `exibirRegistroAgendaManual`, `incluirContatoAposInsercao`, `obrigatorioFiltroTelaTransicao`, `qtdTitulosAtrasoValidarMaiorAtrasoClasseA`, `qtdTitulosAtrasoValidarMaiorAtrasoClasseB` …
- **Mensagens e validações visíveis (11):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad001/form.php:614`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad001/form.php:660`)
  - "Agenda alterada com sucesso." (`prog/cad/cad001/form.php:1380`)
  - "Agenda cadastrada com sucesso." (`prog/cad/cad001/form.php:1392`)
  - "Etiquetas enviadas para impressora!  $retorno clientes buscados" (`prog/cad/cad001/form.php:2273`)
  - "Os clientes foram juntados com sucesso!" (`prog/cad/cad001/form.php:2883`)
  - "' . $retorno . '" (`prog/cad/cad001/form.php:3837`)
  - "CSV Importado com Sucesso!" (`prog/cad/cad001/form.php:3840`)
  - … mais 3
- **Abre as telas:** `etc/etc006`, `crm/crm001`, `fis/fis005`, `cad/cad045`, `srm/srm001`, `ven/ven001`, `fin/fin001`
- **Grafo (Graphify):** 29 nós, 19 funções definidas: `abrirDocumento()`, `campoAbrirDocumento()`, `campoeMailSenhaCliente()`, `checkboxMarcarCliente()`, `mostraImagemCadastro()`, `mostraImagemDoc()` …. Chama fora da tela: `.log()` (CAD002.php)
- **Aberta por:** `cad/cad002`, `cad/cad039`, `cad/cad040`, `cad/cad061`, `crm/crm001`, `crm/crm002`, `erp/erp001`, `erp/erp002`, `est/est006`, `est/est008`, `est/est011`, `est/est015`, `est/est016`, `etc/etc006`, `fin/fin001` …

### `prog/cad/cad002` — Produtos

- **Menu:** Cadastros › Produtos, acesso C029 nº 5 "Produtos" (`bibliotecas/menu.php:12`)
- **Menu:** Estoque › Exportar XML produtos, acesso C029 nº 48 "Exportar TXT NF-e" (item comentado no Menu) (`bibliotecas/menu.php:203`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 186 "Incluir/Excluir/Alterar Produto" (`prog/cad/cad002/grid.php:301` e mais 4); 187 "Incluir/Excluir/Alterar Pré Cadastro Produto" (`prog/cad/cad002/grid.php:1260` e mais 3); 209 "Incluir produtos similares" (`prog/cad/cad002/grid.php:7353` e mais 1); 274 "Importar CSV Tabela Geral Fornecedor" (`prog/cad/cad002/tab.php:770`); 275 "CIAP - Crédito ICMS Ativo Imobilizado" (`prog/cad/cad002/tab.php:34`)
- **Arquivos:** 11 arquivos. 23.827 linhas. `grid.php` (8821), `form.php` (8090), `outro.php` (1781), `content.php` (1724), `tab.php` (1071), `grid_func-ajax.php` (976), `form_func-ajax.php` (585), `grid_func-callback.php` (464), `form_func-js.php` (187), `grid_func-js.php` (124), `excessoesIcms.php` (4)
- **Abas (tab.php):** Produtos (`tab.php:17`), Preços (`tab.php:18`), Tabelas (`tab.php:21`), Documentos (`tab.php:22`), Inativos (`tab.php:23`), Histórico (`tab.php:25`), Similares (`tab.php:26`), Importar CSV (`tab.php:28`), Referências (`tab.php:29`), Fornecedores (`tab.php:30`), Pertence à empresa (`tab.php:31`), Ativos Imobilizado (CIAP) (`tab.php:35`) …
- **Ações (`case` do roteador, 274):** `grid1`, `form1`, `formXML`, `form2`, `detalhes`, `cad002ContentEcommerce`, `certificados`, `cadastroMarca`, `cad002ContentCadastroEmpresas`, `cad002ContentCadastroLocacao`, `cad002ContentCadastroLocacaoEditar`, `cad002ContentMontagem`, `cad002ContentMontagemProdutos`, `cad002contentEditarHistorico`, `cad002ContentUploadFotoProduto`, `cad002ContentServicos`, `cad002ContentPreCadastro`, `cad002ContentJuntarProdutos`, `cad002ReferenciasProdutos`, `cad002ContentTrocarCodigos` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D001` (fotografia:2944): UPDATE/form em `prog/cad/cad002/form.php:64`, `prog/cad/cad002/form.php:234` e mais 9
  - `D001A` — Produtos (fotografia:3035): form em `prog/cad/cad002/form.php:6391`
  - `D009` (fotografia:3608): UPDATE/form em `prog/cad/cad002/form.php:1698`, `prog/cad/cad002/form.php:1902` e mais 8
  - `D049` (fotografia:5460): INSERT/UPDATE/form em `prog/cad/cad002/form.php:2042`, `prog/cad/cad002/form.php:2423` e mais 8
  - `D069` (fotografia:6119): DELETE/UPDATE/form em `prog/cad/cad002/form.php:4382`, `prog/cad/cad002/form.php:6006` e mais 10
  - `D070` (fotografia:6408): form em `prog/cad/cad002/form.php:5837`
  - `D083` (fotografia:6752): UPDATE/form em `prog/cad/cad002/form.php:2957`, `prog/cad/cad002/form.php:3119` e mais 1
  - `D135` (fotografia:7754): form em `prog/cad/cad002/form.php:4546`
  - `D136` (fotografia:7772): form em `prog/cad/cad002/form.php:4938`
  - `D144` (fotografia:8031): form em `prog/cad/cad002/form.php:5682`
  - `D156` (fotografia:8377): form em `prog/cad/cad002/form.php:7166`
  - `D184` (fotografia:9526): form em `prog/cad/cad002/form.php:7664`
  - `D185` — Produtos e-commerce tray (fotografia:9546): UPDATE/form em `prog/cad/cad002/form.php:7814`, `prog/cad/cad002/form_func-ajax.php:402` e mais 1
  - `D186` — Atributos de produtos e-commerce tray (fotografia:9572): UPDATE/form em `prog/cad/cad002/form.php:7961`, `prog/cad/cad002/form_func-ajax.php:431` e mais 1
  - `T001` (fotografia:14264): UPDATE/form em `prog/cad/cad002/form.php:3701`, `prog/cad/cad002/outro.php:259` e mais 3
  - `T004` (fotografia:14902): UPDATE em `prog/cad/cad002/grid_func-ajax.php:830`
  - `T006` (fotografia:15620): UPDATE em `prog/cad/cad002/grid_func-ajax.php:841`
  - `T008` (fotografia:16670): UPDATE em `prog/cad/cad002/form.php:3599`
  - `T014` (fotografia:17524): UPDATE em `prog/cad/cad002/form.php:3583`
  - `T066` (fotografia:19874): UPDATE/form em `prog/cad/cad002/form.php:3474`, `prog/cad/cad002/form.php:4671` e mais 2
  - `T140` (fotografia:22532): form em `prog/cad/cad002/form.php:2621`
  - `T172` (fotografia:23312): form em `prog/cad/cad002/form.php:3974`
  - `T241` (fotografia:26229): form em `prog/cad/cad002/form.php:6764`, `prog/cad/cad002/form.php:7556`
  - `T303` (fotografia:28442): INSERT em `prog/cad/cad002/form.php:2731`
- **Tabelas só lidas na tela (60):** `D082` (58×, `prog/cad/cad002/form.php:1301` e mais 57), `D024` (55×, `prog/cad/cad002/form.php:1300` e mais 54), `C007` (33×, `prog/cad/cad002/form.php:3305` e mais 32), `D037` (28×, `prog/cad/cad002/form.php:767` e mais 27), `D005` (25×, `prog/cad/cad002/form.php:22` e mais 24), `D004` (20×, `prog/cad/cad002/form.php:3303` e mais 19), `C008` (17×, `prog/cad/cad002/form.php:3301` e mais 16), `C004` (15×, `prog/cad/cad002/grid.php:163` e mais 14), `D006` (14×, `prog/cad/cad002/form.php:6560` e mais 13), `D060` (11×, `prog/cad/cad002/form.php:3300` e mais 10), `D003` (11×, `prog/cad/cad002/grid.php:151` e mais 10), `D041` (10×, `prog/cad/cad002/form.php:1299` e mais 9), `D002` (10×, `prog/cad/cad002/grid.php:158` e mais 9), `T098` (10×, `prog/cad/cad002/grid.php:4067` e mais 9), `D015` (9×, `prog/cad/cad002/grid.php:159` e mais 8), `D072` (9×, `prog/cad/cad002/grid.php:162` e mais 8), `D149` (8×, `prog/cad/cad002/grid.php:165` e mais 7), `D009A` (7×, `prog/cad/cad002/form.php:1298` e mais 6), `T007` (7×, `prog/cad/cad002/grid.php:1414` e mais 6), `T013` (6×, `prog/cad/cad002/grid.php:1412` e mais 5), `D020` (5×, `prog/cad/cad002/form.php:6784` e mais 4), `D018` (5×, `prog/cad/cad002/form.php:6784` e mais 4), `T055` (5×, `prog/cad/cad002/grid.php:2203` e mais 4), `T219` (5×, `prog/cad/cad002/grid.php:5985` e mais 4), `T005` (4×, `prog/cad/cad002/grid.php:2068` e mais 3) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `obterNovoCodigoProduto` (`CAD002.php:5401`), `obterNovoCodigoEAN` (`CAD002.php:5380`), `verificarCodigoProduto` (`CAD002.php:2038`), `incluirMarca` (`CAD002.php:496`), `importarCSV` (`CAD002.php:10525`), `atualizarFlagProdutoPertenceEmpresa` (`CAD002.php:703`), `obterFlagProdutoPertenceEmpresa` (`CAD002.php:747`), `atualizarMarca` (`CAD002.php:787`), `cad002AtualizarAtributosIntegracaoTray` (`CAD002.php:12411`), `juntarMarcas` (`CAD002.php:37`) …
- **Tabelas gravadas pelos métodos chamados:** `D001` (CAD002::cadastroMarca, CAD002::importarCSV, CAD002::juntarProdutos…), `D009` (CAD002::D001_reprocessa_historico, CAD002::atualizarCustoProduto, CAD002::atualizarMarca…), `D009A` (CAD002::D001_reprocessa_historico, CAD002::atualizarCustoProduto, CAD002::atualizarFlagProdutoPertenceEmpresa…), `D017` (CAD002::gravarSubstituicao, CAD002::juntarMarcas, CAD002::trocarCodigos), `D041` (CAD002::atualizarMarca, CAD002::cadastroMarca, CAD002::incluirMarca…), `D042` (CAD002::cad002gravarProduto, CAD002::juntarMarcas), `D049` (CAD002::atualizarMarca, CAD002::gravarPercentualAcrescimoCusto, CAD002::importarCSV…), `D069` (CAD002::associarProdutoListaFornecedor, CAD002::atualizarAliquotasProdutos, CAD002::atualizarCustoProduto…), `D069A` (CAD002::atualizarAliquotasProdutos), `D083` (CAD002::associarProdutoListaFornecedor, CAD002::definirCodigoFornecedor, CAD002::importarCSV…), `D086` (CAD002::juntarMarcas), `D186` (CAD002::cad002AtualizarAtributosIntegracaoTray), `L001` (CAD002::juntarMarcas), `T001` (CAD002::D001_reprocessa_historico, CAD002::juntarMarcas, CAD002::juntarProdutos…), `T004` (CAD002::juntarMarcas, CAD002::juntarProdutos) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Custo_Unitario` (`prog/cad/cad002/grid.php:903` e mais 1), `D009_Quantidade_MP` (`prog/cad/cad002/grid_func-callback.php:213`), `D009_Quantidade_OC` (`prog/cad/cad002/grid.php:778` e mais 2), `D009_Quantidade_Pedido` (`prog/cad/cad002/form.php:1294` e mais 5), `D009_Quantidade_Pedido_Por_Locacao` (`prog/cad/cad002/grid.php:1743`), `D009_Quantidade_Separado_3` (`prog/cad/cad002/form.php:1295` e mais 4), `D069_Gravar_Totalizacao` (`prog/cad/cad002/form_func-ajax.php:231` e mais 1), `T003_Status_Orcamento` (`prog/cad/cad002/grid.php:6822` e mais 1), `T005_Status_Pedido` (`prog/cad/cad002/grid.php:2058` e mais 2), `T225_Quantidade_Pendente_3` (`prog/cad/cad002/grid.php:5965` e mais 2)
- **Chaves C031 lidas (22):** `HabilitarAtualizacaoEstoque`, `SeparaProdutoPorEmpresa`, `diasMedioPrazo`, `gravarCodProdAoCadastrar`, `habilitarMultiplasLocacoes`, `mascaraCodigoProduto`, `mascaraCodigoProdutoPreCadastro`, `mostrarBotaoIncluirHistoricoProduto`, `multimarcas`, `obrigatorioFiltroTelaTransicao`, `plataformaECommerce`, `plataformaECommerce2` …
- **Mensagens e validações visíveis (45):**
  - "Confirma a atualização do preço?" (`prog/cad/cad002/content.php:521`)
  - "Confirma a atualização do estoque?" (`prog/cad/cad002/content.php:542`)
  - "Já existe um código, é necessário limpar ele para poder preencher." (`prog/cad/cad002/content.php:655`)
  - "Já existe um código EAN para este produto e ele não pode ser modificado." (`prog/cad/cad002/content.php:672`)
  - "Não foi possível realizar o upload, selecione o arquivo e tente novamente" (`prog/cad/cad002/form.php:2747`)
  - "Confirma a atualização dos produtos?" (`prog/cad/cad002/form.php:6694`)
  - "Registro Alterado com Sucesso!" (`prog/cad/cad002/form.php:199`)
  - "' . $retorno . '" (`prog/cad/cad002/form.php:408`)
  - … mais 37
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::tabelaFornecedorVencida` (`NOTIFICACOES.php:2537`, clique na linha 2563; lê `D024`, `D070`); `NOTIFICACOES::produtosVencendo` (`NOTIFICACOES.php:2638`, clique na linha 2671; lê `D001`, `D001A`, `D009`, `D049`, `T066`, `T066A`); `NOTIFICACOES::documentoProdutoVencendo` (`NOTIFICACOES.php:2682`, clique na linha 2710; lê `D001`, `D009`, `D049`, `T140`, `T303`); `NOTIFICACOES::documentoProdutoVencido` (`NOTIFICACOES.php:2842`, clique na linha 2869; lê `D001`, `D009`, `D049`, `T140`, `T303`); `NOTIFICACOES::produtosVencidos` (`NOTIFICACOES.php:2880`, clique na linha 2910; lê `D001`, `D001A`, `D009`, `D049`, `T066`, `T066A`)
- **Abre as telas:** `ven/ven001`, `ven/ven002`, `cad/cad043`, `est/est016`, `etc/etc006`, `est/est015`, `crm/crm001`, `est/est004`, `cad/cad052`, `cad/cad001` …
- **Grafo (Graphify):** 91 nós, 80 funções definidas: `cad002AtualizaProdutosNuvemshop()`, `cad002AtualizarAliquotasLoop()`, `cad002AtualizarEstoqueGeralLoop()`, `cad002AtualizarEstoqueRapidoLoop()`, `cad002AtualizarEstoqueTrayLoop()`, `cad002BuscarEstoqueTrayLoop()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `cad/cad007`, `cad/cad020`, `cad/cad052`, `cad/cad061`, `crm/crm001`, `crm/crm002`, `crm/crm005`, `erp/erp001`, `est/est001`, `est/est002`, `est/est003`, `est/est004`, `est/est007`, `est/est008`, `est/est009` …

### `prog/cad/cad003` — Representantes

- **Menu:** Cadastros › Representantes, acesso C029 nº 3 "Representantes" (`bibliotecas/menu.php:10`)
- **Arquivos:** 8 arquivos. 989 linhas. `grid.php` (340), `form.php` (322), `content.php` (78), `tab.php` (72), `outro.php` (67), `portal-acesso.php` (61), `form_func-ajax.php` (34), `form_func-js.php` (15)
- **Abas (tab.php):** Representantes (`tab.php:18`), Representantes Inativos (`tab.php:19`), Representantes (`tab.php:51`), Representantes (`tab.php:57`), Usuários (`tab.php:58`)
- **Ações (`case` do roteador, 12):** `cad003ContentRepresentantes`, `cad003ContentCadastroRepresentantes`, `cad003ContentRepresentantesUsuarios`, `cad003CadastroRepresentantes`, `cad003FormRepresentantesUsuarios`, `alteraEstadoRepresentantes`, `cad003GridFRepresentantes`, `cad003GridFRepresentantesInativos`, `cad003GridRepresentantesUsuarios`, `cad003CabecalhoRepresentate`, `cad003TabRepresentantes`, `cad003TabCadastroRepresentantes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D021` (fotografia:4260): form em `prog/cad/cad003/form.php:38`
  - `T291` (fotografia:27666): form em `prog/cad/cad003/form.php:222`
- **Tabelas só lidas na tela (4):** `D020` (3×, `prog/cad/cad003/grid.php:42` e mais 2), `D018` (3×, `prog/cad/cad003/grid.php:43` e mais 2), `C007` (3×, `prog/cad/cad003/grid.php:45` e mais 2), `D084` (2×, `prog/cad/cad003/grid.php:44` e mais 1)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad003/form.php:130`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad003/form.php:150`)
  - "Usuário já cadastrado para esse representante" (`prog/cad/cad003/form.php:263`)
- **Grafo (Graphify):** 14 nós, 6 funções definidas: `cad003PortalCsrf()`, `cad003PortalExecutar()`, `cad003PortalPodeGerenciar()`, `cad003PortalProcessar()`, `cad003PortalRepresentanteColuna()`, `alteraEstadoRepresentantes()`
- **Aberta por:** `sistema/funcoes`

### `prog/cad/cad004` — Transportadoras

- **Menu:** Cadastros › Transportadoras, acesso C029 nº 2 "Transportadoras" (`bibliotecas/menu.php:9`)
- **Arquivos:** 6 arquivos. 1.019 linhas. `grid.php` (474), `form.php` (356), `content.php` (85), `tab.php` (72), `form_func-ajax.php` (17), `form_func-js.php` (15)
- **Abas (tab.php):** Transportadoras (`tab.php:17`), Transportadoras ecommerce (`tab.php:18`), Transportadoras Inativas (`tab.php:19`), Geral (`tab.php:38`), Mais (`tab.php:39`), Frete (`tab.php:40`), Geral (`tab.php:60`)
- **Ações (`case` do roteador, 13):** `grid1`, `form1`, `form2`, `form1`, `form2`, `alteraEstadoTransportadoras`, `divPadrao`, `transportadoras`, `transportadorasInativas`, `transportadorasEcommerce`, `tabGrid`, `tabForm1`, `tabform2`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D022` (fotografia:4337): form em `prog/cad/cad004/form.php:33`
  - `D168` (fotografia:9155): form em `prog/cad/cad004/form.php:231`
- **Tabelas só lidas na tela (5):** `D020` (3×, `prog/cad/cad004/form.php:258` e mais 2), `D018` (3×, `prog/cad/cad004/form.php:258` e mais 2), `D084` (2×, `prog/cad/cad004/grid.php:84` e mais 1), `D022A` (2×, `prog/cad/cad004/grid.php:85` e mais 1), `D036` (1×, `prog/cad/cad004/grid.php:368`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad004/form.php:145`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad004/form.php:165`)
- **Grafo (Graphify):** 10 nós, 4 funções definidas: `nomeBairro()`, `nomeCidade()`, `nomeEmpresa()`, `alteraEstadoTransportadoras()`

### `prog/cad/cad006`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 53 linhas. `grid.php` (53)
- **Ações (`case` do roteador, 1):** `lista`
- **Tabelas só lidas na tela (2):** `D070` (1×, `prog/cad/cad006/grid.php:21`), `D024` (1×, `prog/cad/cad006/grid.php:24`)
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/cad/cad007` — &nbsp;&nbsp;> Certificados

- **Menu:** Cadastros › &nbsp;&nbsp;> Certificados, acesso C029 nº 7 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:177`)
- **Arquivos:** 7 arquivos. 786 linhas. `form.php` (333), `grid.php` (212), `content.php` (86), `grid_func-ajax.php` (56), `tab.php` (56), `grid_func-js.php` (23), `grid_func-callback.php` (20)
- **Abas (tab.php):** Certificados (`tab.php:21`), Produtos (`tab.php:43`), Cadastro de Certificado (`tab.php:45`)
- **Ações (`case` do roteador, 12):** `cad007content01`, `cad007content02`, `cad007content02Editar`, `cad007ContentProdutos`, `cad007form01`, `cad007FormItens`, `cad007grid01`, `cad007GridItensDocumento`, `excluir`, `abreCertificado`, `cad007tab01`, `cad007tab02`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T140` (fotografia:22532): form em `prog/cad/cad007/form.php:35`
  - `T303` (fotografia:28442): INSERT em `prog/cad/cad007/grid.php:170`
- **Tabelas só lidas na tela (2):** `D001` (3×, `prog/cad/cad007/grid.php:55` e mais 2), `D024` (1×, `prog/cad/cad007/grid.php:53`)
- **Classes e métodos chamados:** `CAD007` (60 linhas): `incluirItem` (`CAD007.php:30`), `excluir` (`CAD007.php:13`)
- **Tabelas gravadas pelos métodos chamados:** `T140` (CAD007::excluir), `T303` (CAD007::excluir, CAD007::incluirItem)
- **Mensagens e validações visíveis (5):**
  - "Não foi possível realizar o upload, selecione o arquivo e tente novamente" (`prog/cad/cad007/form.php:152`)
  - "Intem incluído com sucesso!" (`prog/cad/cad007/form.php:289`)
  - "Deve ser enviado uma  Foto  ou um  Link ." (`prog/cad/cad007/form.php:88`)
  - "Registro Alterado com Sucesso!" (`prog/cad/cad007/form.php:105`)
  - "Certificado excluído" (`prog/cad/cad007/grid_func-js.php:11`)
- **Abre as telas:** `cad/cad002`
- **Grafo (Graphify):** 10 nós, 3 funções definidas: `abrirCertificado()`, `campoExclusao()`, `cad007CampoExclusao()`

### `prog/cad/cad008` — Bens

- **Menu:** Locação › Bens, acesso C029 nº 30 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:211`)
- **Arquivos:** 7 arquivos. 1.103 linhas. `grid.php` (593), `form.php` (176), `tab.php` (85), `grid_func-js.php` (79), `content.php` (69), `grid_func-callback.php` (59), `grid_func-ajax.php` (42)
- **Abas (tab.php):** Bens (`tab.php:21`), Cadastro de Bem (`tab.php:45`), Cadastro de Bem (`tab.php:50`), Especificações (`tab.php:51`)
- **Ações (`case` do roteador, 15):** `cad008content01`, `cad008content02`, `cad008content03`, `cad008form01`, `cad008grid01`, `cad008grid02`, `cad008grid03`, `cad008grid04`, `cad008GridChecklist`, `cad008GridEspecificacoes`, `excluir`, `gravarCampo`, `cad008tab01`, `cad008tab02`, `cad008tab03`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D106` (fotografia:7080): form em `prog/cad/cad008/form.php:35`
- **Tabelas só lidas na tela (14):** `T141` (2×, `prog/cad/cad008/grid.php:256` e mais 1), `D128` (1×, `prog/cad/cad008/grid.php:48`), `T142` (1×, `prog/cad/cad008/grid.php:156`), `D024` (1×, `prog/cad/cad008/grid.php:259`), `T158` (1×, `prog/cad/cad008/grid.php:260`), `T143` (1×, `prog/cad/cad008/grid.php:336`), `D020` (1×, `prog/cad/cad008/grid.php:341`), `D018` (1×, `prog/cad/cad008/grid.php:342`), `D030` (1×, `prog/cad/cad008/grid.php:343`), `D120` (1×, `prog/cad/cad008/grid.php:442`), `T159` (1×, `prog/cad/cad008/grid.php:444`), `D037` (1×, `prog/cad/cad008/grid.php:445`), `T163` (1×, `prog/cad/cad008/grid.php:522`), `D129` (1×, `prog/cad/cad008/grid.php:525`)
- **Classes e métodos chamados:** `CAD008` (176 linhas): `insereRegistrosT159` (`CAD008.php:51`), `insereRegistrosT163` (`CAD008.php:109`), `excluir` (`CAD008.php:12`), `updateT159_Flag_Ok` (`CAD008.php:29`)
- **Tabelas gravadas pelos métodos chamados:** `D106` (CAD008::excluir), `T159` (CAD008::insereRegistrosT159, CAD008::updateT159_Flag_Ok), `T163` (CAD008::insereRegistrosT163)
- **Mensagens e validações visíveis (5):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad008/form.php:89`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad008/form.php:109`)
  - "Bem excluído" (`prog/cad/cad008/grid_func-js.php:11`)
  - "Evento excluído" (`prog/cad/cad008/grid_func-js.php:32`)
  - "Registro excluído" (`prog/cad/cad008/grid_func-js.php:54`)
- **Abre as telas:** `ven/ven011`, `ven/ven009`, `ven/ven010`, `cad/cad036`
- **Grafo (Graphify):** 17 nós, 10 funções definidas: `campoCheckboxChecklist()`, `campoExcluirLogistica()`, `campoExcluirManutencao()`, `campoExclusao()`, `validaCampoStatus()`, `validaCampoStatusContratos()` …

### `prog/cad/cad009` — &nbsp;&nbsp;> Catálogos

- **Menu:** Cadastros › &nbsp;&nbsp;> Catálogos, acesso C029 nº 6 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:176`)
- **Arquivos:** 7 arquivos. 731 linhas. `form.php` (280), `grid.php` (186), `tab.php` (79), `content.php` (66), `grid_func-ajax.php` (51), `grid_func-js.php` (45), `grid_func-callback.php` (24)
- **Abas (tab.php):** Listas (`tab.php:21`), Cadastro de Lista (`tab.php:44`), Produtos (`tab.php:67`), Cadastro de Lista (`tab.php:68`)
- **Ações (`case` do roteador, 12):** `cad009content01`, `cad009content02`, `cad009content03`, `cad009form01`, `cad009form02`, `cad009grid01`, `cad009grid02`, `excluir`, `excluirItem`, `cad009tab01`, `cad009tab02`, `cad009tab03`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D107` (fotografia:7103): form em `prog/cad/cad009/form.php:35`
  - `D108` (fotografia:7121): form em `prog/cad/cad009/form.php:188`
- **Tabelas só lidas na tela (1):** `D001` (1×, `prog/cad/cad009/grid.php:150`)
- **Classes e métodos chamados:** `CAD009` (33 linhas): `excluir` (`CAD009.php:12`), `excluirItem` (`CAD009.php:26`)
- **Tabelas gravadas pelos métodos chamados:** `D107` (CAD009::excluir), `D108` (CAD009::excluirItem)
- **Mensagens e validações visíveis (4):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad009/form.php:81`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad009/form.php:101`)
  - "Bem excluído" (`prog/cad/cad009/grid_func-js.php:11`)
  - "Ítem excluído" (`prog/cad/cad009/grid_func-js.php:34`)
- **Grafo (Graphify):** 11 nós, 4 funções definidas: `campoExclusao()`, `campoExclusaoItem()`, `cad009CampoExclusao()`, `cad009campoExclusaoItem()`

### `prog/cad/cad010` — Funcionários

- **Menu:** Cadastros › Funcionários, acesso C029 nº 10 "Funcionários" (`bibliotecas/menu.php:15`)
- **Menu:** Outros › Cadastro de Digitais, acesso C029 nº 97 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:234`)
- **Arquivos:** 11 arquivos. 1.507 linhas. `form.php` (474), `grid.php` (456), `content.php` (121), `outros.php` (97), `tab.php` (97), `grid_func-ajax.php` (71), `grid_func-callback.php` (66), `grid_func-js.php` (55), `outro_func-ajax.php` (38), `form_func-ajax.php` (17), `form_func-js.php` (15)
- **Abas (tab.php):** Funcionários (`tab.php:21`), Cadastro de Funcionário (`tab.php:44`), Cadastro de Funcionário (`tab.php:63`), Documentos (`tab.php:65`), Senha (`tab.php:86`)
- **Ações (`case` do roteador, 23):** `cad010content01`, `cad010content02`, `cad010content03`, `cad010ContentDocumentos`, `cad010ContentCompetencias`, `cad010ContentCadastroDigital`, `cad010form01`, `cad010form02`, `cad010FormDocumentos`, `alteraEstado`, `cad010grid01`, `cad010grid02`, `cad010GridDocumentos`, `cad010GridHistorico`, `excluir`, `excluircompetencia`, `abreDocumento`, `verificaLogin`, `cad010OutrosCadastrarDigital`, `cad010tab01` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D109` (fotografia:7147): form em `prog/cad/cad010/form.php:37`
  - `D115` (fotografia:7330): form em `prog/cad/cad010/form.php:227`
  - `D123` (fotografia:7517): form em `prog/cad/cad010/form.php:333`
- **Tabelas só lidas na tela (9):** `D110` (2×, `prog/cad/cad010/grid.php:205` e mais 1), `C007` (2×, `prog/cad/cad010/grid.php:297` e mais 1), `D018` (1×, `prog/cad/cad010/grid.php:56`), `D020` (1×, `prog/cad/cad010/grid.php:57`), `T169` (1×, `prog/cad/cad010/grid.php:382`), `T147` (1×, `prog/cad/cad010/grid.php:385`), `T005` (1×, `prog/cad/cad010/grid.php:386`), `D024` (1×, `prog/cad/cad010/grid.php:387`), `D116` (1×, `prog/cad/cad010/grid.php:388`)
- **Classes e métodos chamados:** `CAD010` (67 linhas): `excluir` (`CAD010.php:12`), `excluircompetencia` (`CAD010.php:26`)
- **Tabelas gravadas pelos métodos chamados:** `D109` (CAD010::excluir), `D115` (CAD010::excluircompetencia)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad010/form.php:122`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad010/form.php:143`)
  - "Registro excluído" (`prog/cad/cad010/grid_func-js.php:11`)
- **Grafo (Graphify):** 23 nós, 12 funções definidas: `campoAbrirDocumento()`, `campoExclusao()`, `campoExclusaoCompetencia()`, `campoTelefone()`, `impDigital()`, `validaCampoCompetencia()` …
- **Aberta por:** `cad/cad064`, `fis/fis012`, `srv/srv002`

### `prog/cad/cad011` — Serviços - Competências

- **Menu:** Configuração › Serviços - Competências, acesso C029 nº 72 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:248`)
- **Arquivos:** 7 arquivos. 459 linhas. `form.php` (163), `grid.php` (120), `tab.php` (55), `content.php` (50), `grid_func-ajax.php` (33), `grid_func-js.php` (23), `grid_func-callback.php` (15)
- **Abas (tab.php):** Competências (`tab.php:21`), Cadastro de Competências (`tab.php:44`)
- **Ações (`case` do roteador, 7):** `cad011content01`, `cad011content02`, `cad011form01`, `cad011grid01`, `excluir`, `cad011tab01`, `cad011tab02`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D110` (fotografia:7217): form em `prog/cad/cad011/form.php:39`
- **Classes e métodos chamados:** `CAD011` (21 linhas): `excluir` (`CAD011.php:12`)
- **Tabelas gravadas pelos métodos chamados:** `D110` (CAD011::excluir)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad011/form.php:80`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad011/form.php:100`)
  - "Competência excluída" (`prog/cad/cad011/grid_func-js.php:11`)
- **Grafo (Graphify):** 9 nós, 2 funções definidas: `campoExclusao()`, `cad011CampoExclusao()`

### `prog/cad/cad012` — Materiais

- **Menu:** Serviços › Materiais, acesso C029 nº 26 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:206`)
- **Arquivos:** 7 arquivos. 454 linhas. `form.php` (157), `grid.php` (122), `tab.php` (55), `content.php` (49), `grid_func-ajax.php` (33), `grid_func-js.php` (23), `grid_func-callback.php` (15)
- **Abas (tab.php):** Materiais (`tab.php:21`), Cadastro de Materiais (`tab.php:44`)
- **Ações (`case` do roteador, 7):** `cad012content01`, `cad012content02`, `cad012form01`, `cad012grid01`, `excluir`, `cad012tab01`, `cad012tab02`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D111` (fotografia:7235): form em `prog/cad/cad012/form.php:35`
- **Tabelas só lidas na tela (1):** `D037` (1×, `prog/cad/cad012/grid.php:44`)
- **Classes e métodos chamados:** `CAD012` (20 linhas): `excluir` (`CAD012.php:12`)
- **Tabelas gravadas pelos métodos chamados:** `D111` (CAD012::excluir)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad012/form.php:75`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad012/form.php:95`)
  - "Registro excluído" (`prog/cad/cad012/grid_func-js.php:11`)
- **Grafo (Graphify):** 9 nós, 2 funções definidas: `campoExclusao()`, `cad012CampoExclusao()`

### `prog/cad/cad013` — Agenda de Contatos

- **Menu:** Cadastros › Agenda de Contatos, acesso C029 nº 4 "Agenda de Contatos" (`bibliotecas/menu.php:11`)
- **Arquivos:** 6 arquivos. 548 linhas. `grid.php` (235), `form.php` (169), `content.php` (59), `tab.php` (53), `form_func-ajax.php` (17), `form_func-js.php` (15)
- **Abas (tab.php):** Agenda de Contatos (`tab.php:18`), Cadastro (`tab.php:39`), E-Mail (`tab.php:41`)
- **Ações (`case` do roteador, 9):** `cad013ContentAgendaContatos`, `cad013content02Cadastro`, `cad013ContentEmail`, `cad013FormEditarContatos`, `alteraEstadoContato`, `cad013GridAgendaContatos`, `cad013GridEmail`, `cad013TabAgendaContatos`, `cad013TabEditarContatos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D117` (fotografia:7379): form em `prog/cad/cad013/form.php:27`
- **Tabelas só lidas na tela (3):** `D020` (1×, `prog/cad/cad013/grid.php:36`), `D018` (1×, `prog/cad/cad013/grid.php:37`), `T139` (1×, `prog/cad/cad013/grid.php:154`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad013/form.php:85`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad013/form.php:106`)
- **Abre as telas:** `etc/etc006`
- **Grafo (Graphify):** 7 nós, 1 funções definidas: `alteraEstadoContato()`

### `prog/cad/cad014` — Bancos/Caixas

- **Menu:** Cadastros › Bancos/Caixas, acesso C029 nº 8 "Bancos/Caixas" (`bibliotecas/menu.php:13`)
- **Arquivos:** 5 arquivos. 867 linhas. `form.php` (368), `grid.php` (346), `content.php` (75), `tab.php` (68), `grid_func-callback.php` (10)
- **Abas (tab.php):** Contas (`tab.php:14`), Inativas (`tab.php:15`), Cadastro (`tab.php:36`), Configuração (`tab.php:37`), Banco (`tab.php:56`)
- **Ações (`case` do roteador, 11):** `cad014ContentPrincipal`, `cad014ContentEditarConta`, `cad014contentCadastroBanco`, `cad014FormPrincipal`, `cad014FormCadastroBanco`, `cad014GridPrincipal`, `cad014GridContasInativas`, `cad014GridCadastroBanco`, `cad014TabPrincipal`, `cad014TabEditarConta`, `cad014contentCadastroBanco`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D007` (fotografia:3510): form em `prog/cad/cad014/form.php:24`
  - `D062` (fotografia:5962): form em `prog/cad/cad014/form.php:261`
- **Tabelas só lidas na tela (1):** `C004` (1×, `prog/cad/cad014/grid.php:31`)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad014/form.php:172`)
  - "Registro Incluido com Sucesso! -" (`prog/cad/cad014/form.php:192`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad014/form.php:323`)
- **Grafo (Graphify):** 6 nós, 1 funções definidas: `campoTelefone()`

### `prog/cad/cad015` — Centro de Custos

- **Menu:** Cadastros › Centro de Custos, acesso C029 nº 9 "Centro de Custos" (`bibliotecas/menu.php:14`)
- **Arquivos:** 4 arquivos. 473 linhas. `grid.php` (228), `form.php` (150), `tab.php` (53), `content.php` (42)
- **Abas (tab.php):** Centro Custos (`tab.php:18`), Centro Custos Inativo (`tab.php:19`), Centro Custos (`tab.php:40`)
- **Ações (`case` do roteador, 7):** `cad015ContentCentroDeCustos`, `cad015ContentCentroCustosIncluir`, `cad015FormCentroDeCustos`, `cad015GridCentroDeCustos`, `cad015GridCentroDeCustosInativo`, `cad015TabCentroDeCustos`, `cad015TabCentroDeCustosIncluir`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D073` — CENTRO DE CUSTOS (fotografia:6501): form em `prog/cad/cad015/form.php:26`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad015/form.php:71`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad015/form.php:91`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad016` — Supervisores/Vendedores

- **Menu:** Cadastros › Supervisores/Vendedores, acesso C029 nº 11 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:178`)
- **Arquivos:** 7 arquivos. 526 linhas. `form.php` (191), `grid.php` (141), `grid_func-callback.php` (67), `tab.php` (52), `content.php` (43), `form_func-ajax.php` (17), `form_func-js.php` (15)
- **Abas (tab.php):** Supervisores/Vendedores (`tab.php:18`), Supervisores/Vendedores (`tab.php:39`)
- **Ações (`case` do roteador, 7):** `cad016ContentVendedores`, `cad016ContentVendedoresIncluir`, `cad016FormVendedores`, `alteraEstado`, `cad016GridVendedores`, `cad016TabVendedores`, `cad016TabVendedoresIncluir`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C007` (fotografia:396): form em `prog/cad/cad016/form.php:27`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad016/form.php:111`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad016/form.php:131`)
- **Grafo (Graphify):** 14 nós, 7 funções definidas: `selecionaSupervisor()`, `trataAtivo()`, `trataEspecial()`, `trataFlagExpedicao()`, `trataFlagSupervisorVendedor()`, `trataUsuarioAncora()` …

### `prog/cad/cad017` — Comex

- **Menu:** Configuração › Comex, acesso C029 nº 58 "DI" (`bibliotecas/menu.php:121`)
- **Arquivos:** 6 arquivos. 1.549 linhas. `form.php` (706), `grid.php` (459), `tab.php` (154), `content.php` (123), `grid_func-callback.php` (74), `grid_func-ajax.php` (33)
- **Abas (tab.php):** Portos (`tab.php:14`), Despachantes (`tab.php:15`), Checklist de processos (`tab.php:16`), Despesas (`tab.php:17`), D.I - Portos (`tab.php:37`), Portos (`tab.php:58`), Pacotes (`tab.php:79`), Despesas (`tab.php:80`), Pacote (`tab.php:101`), Valores (`tab.php:102`), Tipo Despesa (`tab.php:122`), Pacote despesas (`tab.php:142`)
- **Ações (`case` do roteador, 23):** `cad017ContentConfiguracaoDI`, `cad017ContentPortos`, `cad017ContentPortosIncluir`, `cad017ContentDespesasComex`, `cad017ContentTipoDespesasComexIncluir`, `cad017ContentDespesasComexIncluir`, `cad017ContentPacoteDespesasComexIncluir`, `cad017FormPortos`, `cad017FormTipoDespesasComexIncluir`, `cad017FormDespesasComexIncluir`, `cad017FormPacoteDespesasComexIncluir`, `cad017GridPortos`, `cad017GridTipoDespesas`, `cad017GridDespesas`, `cad017GridPacoteDespesas`, `processarOrdemDespesa`, `cad017TabConfiguracaoDI`, `cad017TabPortos`, `cad017TabPortosIncluir`, `cad017TabCadastroDeDespesasComex` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D118` (fotografia:7407): form em `prog/cad/cad017/form.php:27`
  - `D190` (fotografia:9655): form em `prog/cad/cad017/form.php:173`
  - `D191` (fotografia:9672): form em `prog/cad/cad017/form.php:310`
  - `D192` (fotografia:9691): UPDATE/form em `prog/cad/cad017/form.php:467`, `prog/cad/cad017/form.php:643`
- **Tabelas só lidas na tela (1):** `D030` (1×, `prog/cad/cad017/grid.php:33`)
- **Classes e métodos chamados:** `CAD017` (33 linhas): `processarOrdemDespesa` (`CAD017.php:13`)
- **Tabelas gravadas pelos métodos chamados:** `D192` (CAD017::processarOrdemDespesa)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad017/form.php:75`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad017/form.php:95`)
- **Grafo (Graphify):** 12 nós, 6 funções definidas: `baseRateio()`, `tipoComex()`, `tipoDespesa()`, `tipoDespesaComex()`, `tipoRateio()`, `colunaEditarCallbackJS_D192_Ordem()`

### `prog/cad/cad018` — Principal (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 6 arquivos. 410 linhas. `form.php` (152), `grid.php` (131), `tab.php` (52), `content.php` (43), `form_func-ajax.php` (17), `form_func-js.php` (15)
- **Abas (tab.php):** Principal (`tab.php:18`), Despachantes (`tab.php:39`)
- **Ações (`case` do roteador, 7):** `cad018ContentDespachantes`, `cad018ContentDespachantesIncluir`, `cad018FormDespachantes`, `alteraEstado`, `cad018GridDespachantes`, `cad018TabDespachantes`, `cad018TabDespachantesIncluir`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D119` (fotografia:7432): form em `prog/cad/cad018/form.php:29`
- **Tabelas só lidas na tela (2):** `D020` (1×, `prog/cad/cad018/grid.php:35`), `D018` (1×, `prog/cad/cad018/grid.php:36`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad018/form.php:73`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad018/form.php:93`)
- **Grafo (Graphify):** 7 nós, 1 funções definidas: `alteraEstado()`

### `prog/cad/cad019` — Produtos - Locações (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 7 arquivos. 1.072 linhas. `grid.php` (465), `form.php` (320), `content.php` (95), `grid_func-ajax.php` (93), `tab.php` (53), `grid_func-callback.php` (32), `grid_func-js.php` (14)
- **Abas (tab.php):** Produtos - Locações (`tab.php:18`), Armazéns (`tab.php:19`), Locações (`tab.php:40`)
- **Ações (`case` do roteador, 15):** `cad019ContentLocacoes`, `cad019ContentCadastroLocacoes`, `cad019ContentCadastroArmazem`, `cad019ContentImprimirEtiquetas`, `cad019CadastroLocacoes`, `cad019CadastroArmazem`, `cad019GridLocacao`, `cad019GridLocacoesImprimir`, `cad019GridArmazem`, `cad019CheckImpLoc`, `cad019CheckboxImprimir`, `cad019DesmarcarImprimir`, `cad019VerificaProdutosImprimir`, `cad019TabLocacoes`, `cad019TabCadastroLocacoes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D004` (fotografia:3234): UPDATE/form em `prog/cad/cad019/form.php:27`, `prog/cad/cad019/grid_func-ajax.php:79`
  - `D146` (fotografia:8096): form em `prog/cad/cad019/form.php:204`
- **Tabelas só lidas na tela (5):** `T066` (1×, `prog/cad/cad019/grid.php:39`), `D009` (1×, `prog/cad/cad019/grid.php:40`), `D049` (1×, `prog/cad/cad019/grid.php:41`), `D001` (1×, `prog/cad/cad019/grid.php:42`), `D138` (1×, `prog/cad/cad019/grid_func-ajax.php:29`)
- **Classes e métodos chamados:** `CAD019` (24 linhas): `updateCheckboxImprimir` (`CAD019.php:14`)
- **Tabelas gravadas pelos métodos chamados:** `D004` (CAD019::updateCheckboxImprimir)
- **Mensagens e validações visíveis (4):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad019/form.php:91`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad019/form.php:112`)
  - "Não existem locações selecionados para impressão" (`prog/cad/cad019/grid.php:69`)
  - "Não existem produtos selecionados para impressão" (`prog/cad/cad019/grid.php:248`)
- **Abre as telas:** `cad/cad051`
- **Grafo (Graphify):** 11 nós, 4 funções definidas: `campoImpTag()`, `tipoLocacao()`, `validaCampoImprimir()`, `cad019CheckboxImprimir()`
- **Aberta por:** `cad/cad002`, `cad/cad023`

### `prog/cad/cad020` — Produtos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 7 arquivos. 4.645 linhas. `form.php` (1953), `grid.php` (1582), `content.php` (429), `tab.php` (233), `grid_func-callback.php` (214), `grid_func-ajax.php` (176), `grid_func-js.php` (58)
- **Abas (tab.php):** Produtos (`tab.php:17`), Novo Produto (`tab.php:37`), Marcas (`tab.php:59`), Comercial (`tab.php:80`), Estoque (`tab.php:81`), Custos (`tab.php:82`), Histórico (`tab.php:101`), Empresas (`tab.php:103`), Montagem (`tab.php:104`), Locação (`tab.php:106`), Pedidos (`tab.php:108`), OC (`tab.php:109`) …
- **Ações (`case` do roteador, 45):** `cad020ContentPrincipal`, `cad020form1`, `cad020form2`, `cad020detalhes`, `certificados`, `cadastroMarca`, `cad020ContentImprimirEtiquetas`, `cad020form2Comum`, `cad020ContentCabecalhoProdutoComum`, `cad020form1`, `cad020form2`, `cad020tributos`, `juntarMarcas`, `cad020editarMarca`, `cadastroMarca`, `certificados`, `cad020substituicao`, `form2Comum`, `cad020lista`, `cad020comercial` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D001` (fotografia:2944): form em `prog/cad/cad020/form.php:39`, `prog/cad/cad020/form.php:236` e mais 1
  - `D009` (fotografia:3608): UPDATE/form em `prog/cad/cad020/form.php:437`, `prog/cad/cad020/form.php:617` e mais 2
  - `D041` (fotografia:5252): UPDATE em `prog/cad/cad020/grid_func-ajax.php:23`, `prog/cad/cad020/grid_func-ajax.php:36`
  - `D049` (fotografia:5460): form em `prog/cad/cad020/form.php:730`, `prog/cad/cad020/form.php:899`
  - `T140` (fotografia:22532): form em `prog/cad/cad020/form.php:1083`
- **Tabelas só lidas na tela (28):** `D024` (14×, `prog/cad/cad020/form.php:1666` e mais 13), `D082` (13×, `prog/cad/cad020/form.php:625` e mais 12), `C008` (7×, `prog/cad/cad020/grid.php:26` e mais 6), `D037` (4×, `prog/cad/cad020/grid.php:32` e mais 3), `T066` (4×, `prog/cad/cad020/grid.php:33` e mais 3), `D004` (4×, `prog/cad/cad020/grid.php:34` e mais 3), `D003` (3×, `prog/cad/cad020/grid.php:23` e mais 2), `D060` (3×, `prog/cad/cad020/grid.php:29` e mais 2), `D002` (3×, `prog/cad/cad020/grid.php:30` e mais 2), `D015` (3×, `prog/cad/cad020/grid.php:31` e mais 2), `D005` (3×, `prog/cad/cad020/grid.php:35` e mais 2), `D072` (3×, `prog/cad/cad020/grid.php:36` e mais 2), `C004` (3×, `prog/cad/cad020/grid.php:378` e mais 2), `C007` (2×, `prog/cad/cad020/grid.php:699` e mais 1), `T006` (2×, `prog/cad/cad020/grid.php:1192` e mais 1), `D149` (1×, `prog/cad/cad020/form.php:1431`), `D175` (1×, `prog/cad/cad020/grid.php:240`), `D017` (1×, `prog/cad/cad020/grid.php:695`), `D083` (1×, `prog/cad/cad020/grid.php:785`), `T001` (1×, `prog/cad/cad020/grid.php:902`), `D042` (1×, `prog/cad/cad020/grid.php:992`), `D048` (1×, `prog/cad/cad020/grid.php:1094`), `T005` (1×, `prog/cad/cad020/grid.php:1188`), `T008` (1×, `prog/cad/cad020/grid.php:1196`), `T007` (1×, `prog/cad/cad020/grid.php:1197`) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `obterNovoCodigoProduto` (`CAD002.php:5401`), `obterNovoCodigoEAN` (`CAD002.php:5380`), `juntarMarcas` (`CAD002.php:37`), `cadastroMarca` (`CAD002.php:1054`), `verificarCodigoProduto` (`CAD002.php:2038`), `D001_reprocessa_historico` (`CAD002.php:1159`), `incluirMarca` (`CAD002.php:496`), `excluir` (`CAD002.php:471`); `CAD020` (56 linhas): `atualizarMarca` (`CAD020.php:31`), `updateCheckboxImprimir` (`CAD020.php:14`)
- **Tabelas gravadas pelos métodos chamados:** `D001` (CAD002::cadastroMarca), `D009` (CAD002::D001_reprocessa_historico, CAD002::cadastroMarca, CAD002::incluirMarca…), `D009A` (CAD002::D001_reprocessa_historico, CAD002::incluirMarca), `D017` (CAD002::juntarMarcas), `D041` (CAD002::cadastroMarca, CAD002::incluirMarca, CAD002::juntarMarcas), `D042` (CAD002::juntarMarcas), `D049` (CAD002::incluirMarca, CAD002::juntarMarcas, CAD020::atualizarMarca), `D069` (CAD002::incluirMarca, CAD002::juntarMarcas), `D083` (CAD002::juntarMarcas), `D086` (CAD002::juntarMarcas), `L001` (CAD002::juntarMarcas), `T001` (CAD002::D001_reprocessa_historico, CAD002::juntarMarcas), `T004` (CAD002::juntarMarcas), `T006` (CAD002::juntarMarcas), `T008` (CAD002::juntarMarcas) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Custo_Unitario` (`prog/cad/cad020/grid.php:499` e mais 1), `D009_Quantidade_OC` (`prog/cad/cad020/grid.php:395` e mais 2), `D009_Quantidade_Pedido` (`prog/cad/cad020/form.php:1661` e mais 2), `D009_Quantidade_Separado_3` (`prog/cad/cad020/form.php:1662` e mais 2), `T005_Status_Pedido` (`prog/cad/cad020/grid.php:1182`)
- **Chaves C031 lidas (4):** `SeparaProdutoPorEmpresa`, `impressaoEtiquetasPadraoJava`, `multimarcas`, `utilizaEcommerce`
- **Mensagens e validações visíveis (13):**
  - "Confirma a atualização do preço?" (`prog/cad/cad020/content.php:138`)
  - "Confirma a atualização do estoque?" (`prog/cad/cad020/content.php:159`)
  - "Já existe um código, é necessário limpar ele para poder preencher." (`prog/cad/cad020/content.php:329`)
  - "Já existe um código EAN para este produto e ele não pode ser modificado." (`prog/cad/cad020/content.php:346`)
  - "Registro Alterado com Sucesso!" (`prog/cad/cad020/form.php:121`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad020/form.php:137`)
  - "Processo concluído com sucesso!" (`prog/cad/cad020/form.php:528`)
  - "Registro Alterado com Sucesso! Problemas na atualizacao do estoque!" (`prog/cad/cad020/form.php:1598`)
  - … mais 5
- **Abre as telas:** `cad/cad002`, `ven/ven001`, `sistema/etiquetas`, `cad/cad052`
- **Grafo (Graphify):** 42 nós, 35 funções definidas: `{closure#1}()`, `{closure#2}()`, `{closure#3}()`, `cad002CampoExcluir1()`, `cad020CheckboxImprimir()`, `cad020ImpTag()` …. Chama fora da tela: `number_format()` (formulario.php)

### `prog/cad/cad021` — Portadores a Pagar (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 390 linhas. `form.php` (171), `grid.php` (115), `tab.php` (54), `content.php` (50)
- **Abas (tab.php):** Portadores a Pagar (`tab.php:23`), Portador a Pagar (`tab.php:41`)
- **Ações (`case` do roteador, 6):** `cad021ContentPrincipal`, `cad021contentCadastroPortador`, `cad021FormCadastroPortador`, `cad021GridPortadoresPagar`, `cad021TabPrincipal`, `cad021TabCadastroPortador`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D031` (fotografia:5005): form em `prog/cad/cad021/form.php:41`
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad021/form.php:96`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad021/form.php:116`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad022` — Portadores a Receber (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 7 arquivos. 682 linhas. `form.php` (287), `grid_func-callback.php` (127), `grid.php` (118), `tab.php` (54), `content.php` (49), `grid_func-ajax.php` (28), `grid_func-js.php` (19)
- **Abas (tab.php):** Portadores a Receber (`tab.php:18`), Portador a Receber (`tab.php:39`), PDV (`tab.php:40`), Ecommerce (`tab.php:41`)
- **Ações (`case` do roteador, 29):** `cad022ContentPrincipal`, `cad022contentCadastroPortador`, `cad022FormCadastroPortador`, `cad022GridPortadoresReceber`, `checkboxFlagComercial`, `1`, `2`, `3`, `4`, `5`, `01`, `02`, `03`, `04`, `05`, `10`, `11`, `12`, `13`, `14` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D027` (fotografia:4887): UPDATE/form em `prog/cad/cad022/form.php:29`, `prog/cad/cad022/grid_func-ajax.php:22`
- **Tabelas só lidas na tela (2):** `D062` (1×, `prog/cad/cad022/form.php:74`), `D007` (1×, `prog/cad/cad022/grid.php:39`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad022/form.php:222`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad022/form.php:242`)
- **Grafo (Graphify):** 14 nós, 7 funções definidas: `campoBoleto()`, `campoCupom()`, `campoFormaPagamento()`, `campoTipo()`, `campoVendaPrazo()`, `validaCampoComercial()` …

### `prog/cad/cad023` — Produtos

- **Menu:** Configuração › Produtos, acesso C029 nº 63 "Produtos" (`bibliotecas/menu.php:113`)
- **Menu:** Configuração › Agregados - Tipos, acesso C029 nº 165 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:246`)
- **Menu:** Configuração › Serviços - Tipos, acesso C029 nº 166 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:247`)
- **Arquivos:** 5 arquivos. 2.720 linhas. `form.php` (1318), `grid.php` (469), `outro.php` (397), `content.php` (368), `tab.php` (168)
- **Abas (tab.php):** Departamentos (`tab.php:14`), Setores (`tab.php:15`), Grupos (`tab.php:16`), Sub-Grupos (`tab.php:17`), Aplicações (`tab.php:18`), Unidades (`tab.php:19`), Locações (`tab.php:20`), Marcas (`tab.php:21`), Importar CSV (`tab.php:22`), Outros Estoques (`tab.php:23`), Embalagens (`tab.php:24`), Importar Departamentos (`tab.php:38`) …
- **Ações (`case` do roteador, 48):** `cad023ContentConfiguracaoProdutos`, `cad023ContentTipos`, `cad023ContentCadastroTipos`, `cad023ContentTiposAgregados`, `cad023ContentTiposAgregadosGrid`, `cad023ContentTiposAgregadosForm`, `cad023ContentTiposServicos`, `cad023ContentTiposServicosGrid`, `cad023ContentTiposServicosForm`, `cad023ContentPrincipal2rows`, `cad023ImportarCSVDepartamentos`, `cad023ImportarCSVSetores`, `cad023ImportarCSVGrupos`, `cad023ImportarCSVSubGrupo`, `cad023ImportarCSVMarca`, `cad023ImportarCSVLocacao`, `cad023ContentCadastroOutrosEstoques`, `cad023ContentCadastroEmbalagens`, `cad023CadastroTipos`, `cad023FormTiposAgregados` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C008` (fotografia:547): form em `prog/cad/cad023/form.php:39`, `prog/cad/cad023/form.php:432`
  - `D002` (fotografia:3154): form em `prog/cad/cad023/form.php:749`
  - `D003` (fotografia:3205): form em `prog/cad/cad023/form.php:643`
  - `D004` (fotografia:3234): form em `prog/cad/cad023/form.php:961`
  - `D060` — CADASTRO DE GRUPOS (fotografia:5814): form em `prog/cad/cad023/form.php:538`
  - `D082` (fotografia:6724): form em `prog/cad/cad023/form.php:855`
  - `D169` (fotografia:9178): form em `prog/cad/cad023/form.php:1066`
  - `D200` — Cadastro de embalagem (fotografia:9876): form em `prog/cad/cad023/form.php:1202`
  - `T209` (fotografia:24310): form em `prog/cad/cad023/form.php:204`
  - `T212` (fotografia:24376): form em `prog/cad/cad023/form.php:312`
- **Classes e métodos chamados:** `CAD023` (946 linhas): `importarCSVDepartamentos` (`CAD023.php:13`), `importarCSVSetores` (`CAD023.php:177`), `importarCSVGrupos` (`CAD023.php:328`), `importarCSVSubGrupos` (`CAD023.php:482`), `importarCSVMarcas` (`CAD023.php:637`), `importarCSVLocacoes` (`CAD023.php:792`)
- **Tabelas gravadas pelos métodos chamados:** `C008` (CAD023::importarCSVDepartamentos), `D002` (CAD023::importarCSVSubGrupos), `D003` (CAD023::importarCSVGrupos), `D004` (CAD023::importarCSVLocacoes), `D060` (CAD023::importarCSVSetores), `D082` (CAD023::importarCSVMarcas)
- **Mensagens e validações visíveis (4):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad023/form.php:116`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad023/form.php:136`)
  - "' . $retorno . '" (`prog/cad/cad023/form.php:476`)
  - "CSV Importado com Sucesso!" (`prog/cad/cad023/form.php:479`)
- **Abre as telas:** `cad/cad019`
- **Grafo (Graphify):** 5 nós, 0 funções definidas

### `prog/cad/cad024` — Produtos - Unidades (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 360 linhas. `form.php` (145), `grid.php` (120), `tab.php` (52), `content.php` (43)
- **Abas (tab.php):** Produtos - Unidades (`tab.php:18`), Unidades (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad024ContentUnidades`, `cad024ContentCadastroUnidades`, `cad024CadastroUnidades`, `cad024GridUnidades`, `cad024TabUnidades`, `cad024TabCadastroUnidades`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D037` (fotografia:5142): form em `prog/cad/cad024/form.php:27`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad024/form.php:66`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad024/form.php:86`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad025` — Produtos - Marcas (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 386 linhas. `form.php` (167), `grid.php` (115), `tab.php` (54), `content.php` (50)
- **Abas (tab.php):** Produtos - Marcas (`tab.php:23`), Marca (`tab.php:41`)
- **Ações (`case` do roteador, 6):** `cad025ContentPrincipal`, `cad025contentCadastroMarca`, `cad025FormCadastroMarca`, `cad025GridMarcas`, `cad025TabPrincipal`, `cad025TabCadastroMarca`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D082` (fotografia:6724): form em `prog/cad/cad025/form.php:41`
- **Tabelas só lidas na tela (1):** `C008` (1×, `prog/cad/cad025/grid.php:34`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad025/form.php:91`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad025/form.php:111`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad026` — Produtos - Grupos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 527 linhas. `form.php` (311), `grid.php` (111), `tab.php` (55), `content.php` (50)
- **Abas (tab.php):** Produtos - Grupos (`tab.php:23`), Grupos (`tab.php:41`), Imagem (`tab.php:42`)
- **Ações (`case` do roteador, 7):** `cad026ContentPrincipal`, `cad026contentCadastroLinha`, `cad026FormCadastroLinha`, `cad026FormCadastroImagem`, `cad026GridLinhas`, `cad026TabPrincipal`, `cad026TabCadastroLinha`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D003` (fotografia:3205): form em `prog/cad/cad026/form.php:40`, `prog/cad/cad026/form.php:196`
- **Tabelas só lidas na tela (2):** `C008` (1×, `prog/cad/cad026/grid.php:30`), `D082` (1×, `prog/cad/cad026/grid.php:31`)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad026/form.php:100`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad026/form.php:120`)
  - "Imagem enviada com sucesso!" (`prog/cad/cad026/form.php:256`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad027` — Produtos - Setores (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 367 linhas. `form.php` (151), `grid.php` (120), `tab.php` (53), `content.php` (43)
- **Abas (tab.php):** Produtos - Setores (`tab.php:18`), Setores (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad027ContentGrupos`, `cad027ContentCadastroGrupos`, `cad027CadastroGrupos`, `cad027GridGrupos`, `cad027TabGrupos`, `cad027TabCadastroGrupos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D060` — CADASTRO DE GRUPOS (fotografia:5814): form em `prog/cad/cad027/form.php:27`
- **Tabelas só lidas na tela (1):** `C008` (1×, `prog/cad/cad027/grid.php:27`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad027/form.php:72`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad027/form.php:92`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad028` — Produtos - sub-grupos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 388 linhas. `form.php` (169), `grid.php` (124), `tab.php` (52), `content.php` (43)
- **Abas (tab.php):** Produtos - sub-grupos (`tab.php:18`), Sub-Grupos (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad028ContentSubGrupos`, `cad028ContentCadastroSubGrupos`, `cad028FormCadastroSubGrupos`, `cad028GridSubGrupos`, `cad028TabSubGrupos`, `cad028TabCadastroSubGrupos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D002` (fotografia:3154): form em `prog/cad/cad028/form.php:27`
- **Tabelas só lidas na tela (2):** `D003` (1×, `prog/cad/cad028/grid.php:33`), `D002A` (1×, `prog/cad/cad028/grid.php:34`)
- **Chaves C031 lidas (1):** `utilizaQuantidadeConversao`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad028/form.php:90`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad028/form.php:110`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad029` — Produtos - Aplicações (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 358 linhas. `form.php` (145), `grid.php` (118), `tab.php` (52), `content.php` (43)
- **Abas (tab.php):** Produtos - Aplicações (`tab.php:18`), Aplicações (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad029ContentAplicacoes`, `cad029ContentCadastroAplicacoes`, `cad029FormCadastroAplicacoes`, `cad029GridAplicacoes`, `cad029TabAplicacoes`, `cad029TabCadastroAplicacoes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D015` (fotografia:4083): form em `prog/cad/cad029/form.php:27`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad029/form.php:66`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad029/form.php:86`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad030` — Usuários/Perfis

- **Menu:** Configuração › Usuários/Perfis, acesso C029 nº 84 "Usuários/Perfis" (`bibliotecas/menu.php:110`)
- **Menu:** Configuração › Usuário, acesso C029 nº 86 "Usuário" (`bibliotecas/menu.php:111`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 248 "Usuários API" (`prog/cad/cad030/tab.php:31`)
- **Arquivos:** 6 arquivos. 3.664 linhas. `form.php` (2043), `grid.php` (970), `content.php` (282), `tab.php` (195), `form_func-ajax.php` (138), `grid_func-callback.php` (36)
- **Abas (tab.php):** Usuários (`tab.php:23`), Perfis (`tab.php:24`), Usuários Inativos (`tab.php:25`), IP Acesso (`tab.php:26`), Perfil Email (`tab.php:27`), Feriados (`tab.php:28`), Usuários API (`tab.php:32`), Usuário (`tab.php:52`), Usuário (`tab.php:56`), Perfil (`tab.php:57`), Parâmetros (`tab.php:58`), Integração (`tab.php:59`) …
- **Ações (`case` do roteador, 52):** `cad030ContentPrincipal`, `cad030contentCadastroUsuario`, `cad030ContentRecriarSenha`, `cad030ContentUsuariosB2B`, `cad030ContentUsuarioB2B`, `cad030ContentUsuariosApi`, `cad030ContentUsuarioApi`, `cad030ContentPerfilUsuario`, `cad030ContentCadastroUsuarioAtual`, `cad030ContentPastasEmail`, `cad030PerfilEmailContent`, `cad030FeriadosContent`, `cad030FeriadosContentEdicao`, `cad030DataFeriadosContent`, `cad030FormCadastroUsuario`, `cad030FormPerfilUsuario`, `cad030FormUsuariosApi`, `cad030FormUsuariosB2B`, `cad030FormPastasEmail`, `cad030FormCadastroUsuarioSimples` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C007` (fotografia:396): UPDATE/form em `prog/cad/cad030/form.php:36`, `prog/cad/cad030/form.php:915` e mais 5
  - `C019` (fotografia:811): form em `prog/cad/cad030/form.php:418`
  - `D178` (fotografia:9383): form em `prog/cad/cad030/form.php:555`
  - `D179` (fotografia:9410): form em `prog/cad/cad030/form.php:732`
  - `D202` — Acessos ao Perfil de Email (fotografia:9911): form em `prog/cad/cad030/form.php:1676`
  - `D206` (fotografia:9976): form em `prog/cad/cad030/form.php:1818`
  - `D207` (fotografia:9994): form em `prog/cad/cad030/form.php:1943`
- **Tabelas só lidas na tela (6):** `C004` (3×, `prog/cad/cad030/grid.php:291` e mais 2), `C021` (3×, `prog/cad/cad030/grid.php:292` e mais 2), `D021` (1×, `prog/cad/cad030/grid.php:453`), `D180` (1×, `prog/cad/cad030/grid.php:545`), `D013` (1×, `prog/cad/cad030/grid.php:549`), `C044` (1×, `prog/cad/cad030/grid.php:897`)
- **Classes e métodos chamados:** `CAD030` (197 linhas): `verificaDuplicidade` (`CAD030.php:12`), `testarEmail` (`CAD030.php:69`)
- **Chaves C031 lidas (1):** `recebimentoEmail`
- **Mensagens e validações visíveis (10):**
  - "A tela ainda está sendo preparada. Aguarde alguns segundos e tente novamente." (`prog/cad/cad030/form.php:183`)
  - "Informe um e-mail de destino para o teste." (`prog/cad/cad030/form.php:230`)
  - "Processo concluído." (`prog/cad/cad030/form.php:1617`)
  - "Esse login já está sendo utilizado pelos usuários abaixo: {$usuarios}" (`prog/cad/cad030/form.php:289`)
  - "Registro Alterado com Sucesso!" (`prog/cad/cad030/form.php:304`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad030/form.php:325`)
  - "Para cadastrar um login vinculado a um representante é necessário vincular um vendedor interno." (`prog/cad/cad030/form.php:786`)
  - "Só é permitido vincular um tipo de vendedor ao login." (`prog/cad/cad030/form.php:793`)
  - … mais 2
- **Grafo (Graphify):** 9 nós, 3 funções definidas: `campoTipoUsuario()`, `validaCampoPerfil()`, `gridLinha()`
- **Aberta por:** `cad/cad055`, `crm/crm001`

### `prog/cad/cad031` — Empresas

- **Menu:** Configuração › Empresas, acesso C029 nº 57 "Empresas" (`bibliotecas/menu.php:112`)
- **Arquivos:** 8 arquivos. 2.593 linhas. `form.php` (1596), `grid.php` (576), `tab.php` (133), `content.php` (109), `grid_func-callback.php` (86), `grid_func-ajax.php` (40), `form_func-js.php` (29), `form_func-ajax.php` (24)
- **Abas (tab.php):** Empresas (`tab.php:18`), Inativas (`tab.php:19`), Avisos (`tab.php:20`), Geral (`tab.php:43`), Parâmetros (`tab.php:44`), Impostos (`tab.php:45`), Pedido de Venda (`tab.php:46`), Configurações (`tab.php:47`), Juros (`tab.php:48`), Rodapé (`tab.php:49`), Logo (`tab.php:50`), LogoB2B (`tab.php:51`) …
- **Ações (`case` do roteador, 25):** `cad031ContentEmpresas`, `cad031ContentCadastroEmpresas`, `cad031ContentCadastroAvisosIndex`, `cad031ContentFormAvisosPerfil`, `cad031ContentAvisosIndex`, `cad031FormCadastroEmpresas`, `cad031FormCadastroLogomarca`, `cad031FormCadastroLogoB2B`, `cad031FormCadastroObservacoes`, `cad031FormAvisosIndex`, `cad031FormAvisosImagem`, `cad031FormAvisosPerfil`, `alteraEstadoEmpresas`, `carregaSeguradoraApolices`, `cad031GridEmpresas`, `cad031GridEmpresasInativas`, `cad031GridAvisosIndex`, `cad031GridAvisosPerfil`, `cad031GridAvisosIndexInativos`, `campoAbrirAviso` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C004` (fotografia:176): UPDATE/form em `prog/cad/cad031/form.php:44`, `prog/cad/cad031/form.php:486` e mais 4
  - `C032` (fotografia:2316): form em `prog/cad/cad031/form.php:1177`, `prog/cad/cad031/form.php:1356`
  - `C036` (fotografia:2416): form em `prog/cad/cad031/form.php:1468`
- **Tabelas só lidas na tela (4):** `D020` (3×, `prog/cad/cad031/form.php:275` e mais 2), `D018` (3×, `prog/cad/cad031/form.php:275` e mais 2), `C021` (2×, `prog/cad/cad031/grid.php:382` e mais 1), `C007` (2×, `prog/cad/cad031/grid.php:384` e mais 1)
- **Classes e métodos chamados:** `CAD031` (45 linhas): `inserirAposC004` (`CAD031.php:12`)
- **Tabelas gravadas pelos métodos chamados:** `C020` (CAD031::inserirAposC004), `D009` (CAD031::inserirAposC004), `D053` (CAD031::inserirAposC004)
- **Mensagens e validações visíveis (5):**
  - "O arquivo deve ser no formato ".PFX"" (`prog/cad/cad031/form.php:459`)
  - "Registro Alterado com Sucesso!" (`prog/cad/cad031/form.php:492`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad031/form.php:540`)
  - "O arquivo deve ser no formato PFX" (`prog/cad/cad031/form.php:663`)
  - "Para inclusão de novas empresas favor entrar em contato com a Hardness no telefone 41 2626-1243, obrigado!" (`prog/cad/cad031/grid.php:60`)
- **Abre as telas:** `cad/cad064`, `cad/cad066`
- **Grafo (Graphify):** 13 nós, 5 funções definidas: `campoAbrirAviso()`, `campoAviso()`, `campoPerfil()`, `alteraEstadoEmpresas()`, `alteraSeguradora()`

### `prog/cad/cad032` — Perfis (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 8 arquivos. 1.027 linhas. `grid.php` (295), `form.php` (286), `outro.php` (161), `content.php` (104), `tab.php` (84), `grid_func-callback.php` (36), `grid_func-ajax.php` (34), `grid_func-js.php` (27)
- **Abas (tab.php):** Perfis (`tab.php:18`), Perfil (`tab.php:35`), Acessos: Menu (`tab.php:37`), Acessos: Ações (`tab.php:38`), Perfils (`tab.php:57`), Perfis (`tab.php:73`)
- **Ações (`case` do roteador, 19):** `cad032ContentPrincipal`, `cad032contentCadastroPerfil`, `cad032ContentCopiaPerfil`, `cad032ContentPerfisItem`, `cad032ContentIncluirAcesso`, `cad032FormCadastroPerfil`, `cad032FormIncluirAcesso`, `cad032GridPerfis`, `cad032GridResumosRelatorios`, `cad032GridAcessos`, `cad032GridPerfisItem`, `gravarCampo`, `gravarCampoResumoselatorios`, `cad032OutroCopiaPerfil`, `copiar`, `cad032TabPrincipal`, `cad032TabCadastroPerfil`, `cad032TabCopiaPerfil`, `cad032TabPerfisItem`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C021` (fotografia:960): INSERT/form em `prog/cad/cad032/form.php:41`, `prog/cad/cad032/outro.php:149`
  - `C029` (fotografia:1118): form em `prog/cad/cad032/form.php:165`
  - `C030` (fotografia:1353): INSERT em `prog/cad/cad032/outro.php:156`
- **Tabelas só lidas na tela (3):** `C004` (2×, `prog/cad/cad032/grid.php:181` e mais 1), `C027` (1×, `prog/cad/cad032/grid.php:127`), `C028` (1×, `prog/cad/cad032/grid.php:130`)
- **Classes e métodos chamados:** `CAD032` (57 linhas): `updateC030_Acesso_Liberado` (`CAD032.php:15`), `updateC028_Acesso_Liberado` (`CAD032.php:40`)
- **Tabelas gravadas pelos métodos chamados:** `C028` (CAD032::updateC028_Acesso_Liberado), `C030` (CAD032::updateC030_Acesso_Liberado)
- **Chaves C031 lidas (1):** `tipoSistema`
- **Mensagens e validações visíveis (4):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad032/form.php:77`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad032/form.php:98`)
  - "Copiado!" (`prog/cad/cad032/outro.php:59`)
  - "Fazer uma cópia deste perfil para esta empresa?" (`prog/cad/cad032/outro.php:65`)
- **Grafo (Graphify):** 13 nós, 5 funções definidas: `cad032CheckboxAcessos()`, `cad032CheckboxAcessosItem()`, `cad032CheckboxResumosRelatorios()`, `cad032CheckboxAcessos()`, `cad032CheckboxResumosRelatorios()`

### `prog/cad/cad033` — Cotação Moeda

- **Menu:** Configuração › Cotação Moeda, acesso C029 nº 77 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:253`)
- **Arquivos:** 4 arquivos. 344 linhas. `form.php` (145), `grid.php` (103), `tab.php` (53), `content.php` (43)
- **Abas (tab.php):** Cotação Moeda (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad033ContentPrincipal`, `cad033ContentCadastroCotacaoMoeda`, `cad033FormCadastroCotacaoMoeda`, `cad033GridCotacaoMoeda`, `cad033TabCotacaoMoeda`, `cad033TabCadastroCotacaoMoeda`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D016` (fotografia:4108): form em `prog/cad/cad033/form.php:27`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad033/form.php:65`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad033/form.php:85`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad034` — Plano de Contas

- **Menu:** Cadastros › Plano de Contas, acesso C029 nº 12 "Plano de Contas" (`bibliotecas/menu.php:16`)
- **Arquivos:** 8 arquivos. 3.447 linhas. `grid.php` (1412), `form.php` (1018), `outro.php` (373), `tab.php` (207), `content.php` (194), `grid_func-callback.php` (122), `grid_func-ajax.php` (79), `grid_func-js.php` (42)
- **Abas (tab.php):** Contas (`tab.php:18`), Grupos (`tab.php:19`), Sub-Grupos (`tab.php:20`), Ativo (`tab.php:41`), Inativo (`tab.php:42`), Importar CSV (`tab.php:43`), Ativo (`tab.php:63`), Inativo (`tab.php:64`), Importar CSV (`tab.php:65`), Sub-Grupos (`tab.php:85`), Importar CSV (`tab.php:86`), Plano de Contas (`tab.php:120`) …
- **Ações (`case` do roteador, 41):** `cad034ContentPlanoDeContas`, `cad034ContentPlanoContasIncluir`, `cad034ContentGrupoPlanoContasIncluir`, `cad034ContantIncluirProjecao`, `cad034ContentSubGrupoPlanoContasIncluir`, `cad034ImportarCSV`, `cad034ImportarCSVGrupos`, `cad034ImportarCSVSubGrupos`, `cad034FormPlanoDeContas`, `cad034FormPlanoDeContasGrupo`, `cad034FormProjecao`, `cad034FormPlanoDeContasSubGrupo`, `cad034FormImportarCSV`, `cad034FormImportarCSVGrupos`, `cad034FormImportarCSVSubGrupos`, `cad034GridPlanoDeContas`, `cad034GridPlanoDeContasInativo`, `cad034GridGruposPlanoContas`, `cad034GridGruposPlanoContasInativo`, `cad034GridProjecaoOrcamentaria` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D014` (fotografia:4032): UPDATE/form em `prog/cad/cad034/form.php:31`, `prog/cad/cad034/form.php:175` e mais 3
  - `D032` (fotografia:5024): UPDATE/form em `prog/cad/cad034/form.php:275`, `prog/cad/cad034/form.php:326` e mais 4
  - `D154` (fotografia:8342): form em `prog/cad/cad034/form.php:580`, `prog/cad/cad034/form.php:933`
  - `T018` (fotografia:17951): UPDATE/form em `prog/cad/cad034/form.php:427`, `prog/cad/cad034/grid_func-ajax.php:43`
- **Tabelas só lidas na tela (12):** `D024` (5×, `prog/cad/cad034/grid.php:45` e mais 4), `D031` (3×, `prog/cad/cad034/grid.php:46` e mais 2), `D073` (2×, `prog/cad/cad034/grid.php:800` e mais 1), `T007` (2×, `prog/cad/cad034/grid.php:804` e mais 1), `C007` (2×, `prog/cad/cad034/grid.php:805` e mais 1), `D027` (2×, `prog/cad/cad034/grid.php:1035` e mais 1), `D020` (1×, `prog/cad/cad034/form.php:120`), `D018` (1×, `prog/cad/cad034/form.php:120`), `T015` (1×, `prog/cad/cad034/grid.php:796`), `T002` (1×, `prog/cad/cad034/grid.php:1030`), `T019` (1×, `prog/cad/cad034/grid.php:1165`), `D007` (1×, `prog/cad/cad034/grid.php:1170`)
- **Classes e métodos chamados:** `CAD034` (749 linhas): `importarCSV` (`CAD034.php:42`), `importarCSVGrupos` (`CAD034.php:389`), `importarCSVSubGrupos` (`CAD034.php:582`); `FIN001` (7368 linhas) (só incluída); `FIN002` (3354 linhas) (só incluída); `FIN009` (471 linhas): `fin009RecalculaProjecao`
- **Tabelas gravadas pelos métodos chamados:** `D014` (CAD034::importarCSV), `D032` (CAD034::importarCSVGrupos), `D154` (CAD034::importarCSVSubGrupos)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D032_Tipo` (`prog/cad/cad034/grid.php:27` e mais 5)
- **Mensagens e validações visíveis (4):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad034/form.php:176`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad034/form.php:198`)
  - "' . $retorno . '" (`prog/cad/cad034/form.php:764`)
  - "CSV Importado com Sucesso!" (`prog/cad/cad034/form.php:767`)
- **Abre as telas:** `fin/fin002`, `fin/fin003`, `fin/fin001`
- **Grafo (Graphify):** 21 nós, 13 funções definidas: `campoConfirmado()`, `campoDebitoCredito()`, `campoGrupoNegrito()`, `validaCampoMes()`, `ValidaCampoSaldo()`, `validacheckAtivo()` …
- **Aberta por:** `fin/fin002`, `fin/fin009`

### `prog/cad/cad035` — Recibos de Locação

- **Menu:** Locação › Recibos de Locação, acesso C029 nº 33 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:214`)
- **Arquivos:** 6 arquivos. 681 linhas. `form.php` (296), `grid.php` (204), `content.php` (64), `tab.php` (56), `grid_func-callback.php` (33), `outro.php` (28)
- **Abas (tab.php):** Recibos de Locações (`tab.php:18`), Cadastro (`tab.php:39`), Bens (`tab.php:43`), Recibo de Locação (`tab.php:44`)
- **Ações (`case` do roteador, 10):** `cad035ContentReciboDeLocacoes`, `cad035ContentReciboDeLocacoesIncluir`, `cad035ContentBens`, `cad035FormReciboDeLocacoes`, `cad035FormBens`, `cad035GridReciboDeLocacoes`, `cad035GridBens`, `cad035locacaoBensMoveis`, `cad035TabReciboDeLocacoes`, `cad035TabCadastroReciboDeLocacoes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T156` (fotografia:22935): form em `prog/cad/cad035/form.php:26`
  - `T157` (fotografia:22956): form em `prog/cad/cad035/form.php:171`
- **Tabelas só lidas na tela (3):** `D024` (2×, `prog/cad/cad035/form.php:39` e mais 1), `D062` (1×, `prog/cad/cad035/form.php:42`), `T141` (1×, `prog/cad/cad035/grid.php:31`)
- **Classes e métodos chamados:** `CAD035` (369 linhas): `getVar` (`CAD035.php:13`), `gerarDadosBanco` (`CAD035.php:30`), `gerarDadosEmpresaCabecalho` (`CAD035.php:70`), `emissaoANDNum` (`CAD035.php:106`), `docGerarDadosEmpresa` (`CAD035.php:138`), `docGerarCorpoDcto` (`CAD035.php:269`), `docGerarRodape` (`CAD035.php:351`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad035/form.php:68`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad035/form.php:89`)
- **Grafo (Graphify):** 7 nós, 1 funções definidas: `campoImpressao()`

### `prog/cad/cad036` — Bens - Checklist (conformidade)

- **Menu:** Configuração › Bens - Checklist (conformidade), acesso C029 nº 73 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:249`)
- **Arquivos:** 5 arquivos. 407 linhas. `form.php` (150), `grid.php` (120), `tab.php` (49), `content.php` (44), `grid_func-callback.php` (44)
- **Abas (tab.php):** Checklist de Bens (`tab.php:18`), Checklist (`tab.php:38`)
- **Ações (`case` do roteador, 6):** `cad036ContentChecklistDeBens`, `cad036ContentCadastroChecklistDeBens`, `cad036FormChecklistDeBens`, `cad036GridChecklistDeBens`, `cad036TabChecklistDeBens`, `cad036TabCadastroChecklistDeBens`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D120` (fotografia:7460): form em `prog/cad/cad036/form.php:26`
- **Tabelas só lidas na tela (2):** `D037` (1×, `prog/cad/cad036/grid.php:32`), `D128` (1×, `prog/cad/cad036/grid.php:33`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad036/form.php:67`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad036/form.php:88`)
- **Grafo (Graphify):** 7 nós, 2 funções definidas: `campoImpressao()`, `campoTipo()`
- **Aberta por:** `cad/cad008`, `cad/cad040`

### `prog/cad/cad037` — Bens - Especificações (inspeção)

- **Menu:** Configuração › Bens - Especificações (inspeção), acesso C029 nº 74 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:250`)
- **Arquivos:** 6 arquivos. 424 linhas. `form.php` (142), `grid.php` (115), `outro.php` (54), `tab.php` (52), `content.php` (42), `grid_func-callback.php` (19)
- **Abas (tab.php):** Especificações (`tab.php:18`), Especificações (`tab.php:39`)
- **Ações (`case` do roteador, 7):** `cad037ContentEspecificacoes`, `cad037ContentEspecificacoesIncluir`, `cad037FormEspecificacoes`, `cad037GridEspecificacoes`, `cad037CertificadoInspecao`, `cad037TabEspecificacoes`, `cad037TabEspecificacoesIncluir`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D121` (fotografia:7481): form em `prog/cad/cad037/form.php:26`
- **Tabelas só lidas na tela (1):** `D128` (1×, `prog/cad/cad037/grid.php:29`)
- **Classes e métodos chamados:** `CAD008` (176 linhas) (só incluída); `CAD037` (476 linhas): `getVar` (`CAD037.php:13`), `docGerarNumAno` (`CAD037.php:30`), `docGerarTableInspecao` (`CAD037.php:44`), `docGerarEspecificacoes` (`CAD037.php:163`), `docGerarCilindrosGas` (`CAD037.php:88`), `docGerarRodapeDireita` (`CAD037.php:463`); `CAD044` (23 linhas): `getVar` (`CAD044.php:12`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad037/form.php:63`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad037/form.php:83`)
- **Grafo (Graphify):** 7 nós, 1 funções definidas: `validaCampoTipo()`

### `prog/cad/cad038` — Checklist de Processos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 408 linhas. `form.php` (143), `grid.php` (128), `tab.php` (49), `content.php` (44), `grid_func-callback.php` (44)
- **Abas (tab.php):** Checklist de Processos (`tab.php:18`), Checklist (`tab.php:38`)
- **Ações (`case` do roteador, 6):** `cad038ContentChecklistDeProcessos`, `cad038ContentCadastroChecklistDeProcessos`, `cad038FormChecklistDeProcessos`, `cad038GridChecklistDeProcessos`, `cad038TabChecklistDeProcessos`, `cad038TabCadastroChecklistDeProcessos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D122` (fotografia:7500): form em `prog/cad/cad038/form.php:26`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad038/form.php:60`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad038/form.php:81`)
- **Grafo (Graphify):** 7 nós, 2 funções definidas: `campoImpressao()`, `campoTipo()`

### `prog/cad/cad039` — Certificados de Inspeção

- **Menu:** Locação › Certificados de Inspeção, acesso C029 nº 56 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:216`)
- **Arquivos:** 7 arquivos. 833 linhas. `form.php` (318), `grid.php` (302), `content.php` (62), `tab.php` (62), `form_func-ajax.php` (38), `grid_func-callback.php` (29), `form_func-js.php` (22)
- **Abas (tab.php):** Certificados de Inspeção (`tab.php:18`), Cadastro de Certificado (`tab.php:41`), Cadastro de Certificado (`tab.php:48`), Especificações (`tab.php:49`), Acessórios (`tab.php:50`)
- **Ações (`case` do roteador, 10):** `cad039ContentCertificadosDeInspecao`, `cad039ContentCertificadosInspecaoIncluir`, `cad039ContentAcessorios`, `cad039FormCertificadosDeInspecao`, `cad039FormAcessorios`, `atualizarCamposBens`, `cad039GridCertificadosDeInspecao`, `cad039GridAcessorios`, `cad039TabCertificadosDeInspecao`, `cad039TabCertificadosDeInspecaoIncluir`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D124` (fotografia:7539): form em `prog/cad/cad039/form.php:27`
  - `T164` (fotografia:23106): form em `prog/cad/cad039/form.php:208`
- **Tabelas só lidas na tela (5):** `D106` (5×, `prog/cad/cad039/form.php:52` e mais 4), `D128` (2×, `prog/cad/cad039/form.php:37` e mais 1), `D024` (1×, `prog/cad/cad039/form.php:69`), `D121` (1×, `prog/cad/cad039/grid.php:146`), `T160` (1×, `prog/cad/cad039/grid.php:153`)
- **Classes e métodos chamados:** `CAD039` (37 linhas): `insereRegistrosT160` (`CAD039.php:14`)
- **Tabelas gravadas pelos métodos chamados:** `T160` (CAD039::insereRegistrosT160)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad039/form.php:93`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad039/form.php:113`)
- **Abre as telas:** `cad/cad001`
- **Grafo (Graphify):** 10 nós, 3 funções definidas: `campoDebitoCredito()`, `campoImpressao()`, `campoProcuraBem()`

### `prog/cad/cad040` — Certificados de Conformidade

- **Menu:** Locação › Certificados de Conformidade, acesso C029 nº 34 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:215`)
- **Arquivos:** 10 arquivos. 1.096 linhas. `form.php` (303), `grid.php` (294), `form_func-ajax.php` (200), `content.php` (65), `tab.php` (58), `form_func-js.php` (53), `grid_func-ajax.php` (43), `outro.php` (37), `grid_func-callback.php` (29), `grid_func-js.php` (14)
- **Abas (tab.php):** Certificado Conformidade (`tab.php:18`), Certificado de Conformidade (`tab.php:40`), Certificado de Conformidade (`tab.php:44`), CheckList (`tab.php:45`), Acessórios (`tab.php:46`)
- **Ações (`case` do roteador, 21):** `cad040ContentCertificadoConformidade`, `cad040ContentCadastroCertificadoConformidade`, `cad040ContentAcessorios`, `cad040FormCertificadoConformidade`, `cad040FormAcessorios`, `atualizarCamposBens`, `atualizarCamposServicos`, `gerarPedido`, `aplicarIPV`, `ImportarOrcamento`, `atualizarAliquota`, `confirmarTudo`, `zerarTudo`, `cad040CertificadosConformidade`, `cad040GridChecklist`, `cad040GridAcessorios`, `excluir`, `gravarCampo`, `cad040OutroCertificado`, `cad040TabCertificadoConformidade` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D125` (fotografia:7560): form em `prog/cad/cad040/form.php:23`
  - `T004` (fotografia:14902): UPDATE em `prog/cad/cad040/form_func-ajax.php:163`, `prog/cad/cad040/form_func-ajax.php:184`
  - `T164` (fotografia:23106): form em `prog/cad/cad040/form.php:192`
- **Tabelas só lidas na tela (9):** `D106` (5×, `prog/cad/cad040/form.php:48` e mais 4), `D128` (2×, `prog/cad/cad040/form.php:32` e mais 1), `D024` (2×, `prog/cad/cad040/form.php:63` e mais 1), `T158` (1×, `prog/cad/cad040/form_func-ajax.php:37`), `D112` (1×, `prog/cad/cad040/form_func-ajax.php:55`), `D110` (1×, `prog/cad/cad040/form_func-ajax.php:56`), `D120` (1×, `prog/cad/cad040/grid.php:156`), `T159` (1×, `prog/cad/cad040/grid.php:158`), `D037` (1×, `prog/cad/cad040/grid.php:159`)
- **Classes e métodos chamados:** `CAD008` (176 linhas): `excluir` (`CAD008.php:12`), `updateT159_Flag_Ok` (`CAD008.php:29`), `docGerarEspecificacoes` (`CAD008.php:139`); `CAD040` (199 linhas): `insereRegistrosT159` (`CAD040.php:13`), `docGerarChecklist` (`CAD040.php:72`), `docGerarCilindrosGas` (`CAD040.php:135`); `CAD044` (23 linhas): `getVar` (`CAD044.php:12`); `VEN001` (15360 linhas): `gerarPedido` (`VEN001.php:1896`), `aplicarIpvMinimo` (`VEN001.php:3523`), `importarOrcamento` (`VEN001.php:3571`), `atualizarAliquotas` (`VEN001.php:3688`)
- **Tabelas gravadas pelos métodos chamados:** `D106` (CAD008::excluir), `T003` (VEN001::gerarPedido), `T004` (VEN001::aplicarIpvMinimo, VEN001::atualizarAliquotas, VEN001::gerarPedido), `T004A` (VEN001::atualizarAliquotas), `T005` (VEN001::gerarPedido), `T005A` (VEN001::gerarPedido), `T006` (VEN001::gerarPedido), `T006A` (VEN001::gerarPedido), `T090` (VEN001::gerarPedido), `T138` (VEN001::gerarPedido), `T147` (VEN001::gerarPedido), `T148` (VEN001::gerarPedido), `T149` (VEN001::gerarPedido), `T159` (CAD008::updateT159_Flag_Ok, CAD040::insereRegistrosT159), `T167` (VEN001::gerarPedido) …
- **Mensagens e validações visíveis (1):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad040/form.php:88`)
- **Abre as telas:** `cad/cad036`, `cad/cad001`
- **Grafo (Graphify):** 15 nós, 5 funções definidas: `campoProcuraBem()`, `campoProcuraServico()`, `campoCheckboxChecklist()`, `campoImpressao()`, `cad040CheckboxChecklist()`

### `prog/cad/cad041` — Geral (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 334 linhas. `form.php` (139), `grid.php` (99), `tab.php` (53), `content.php` (43)
- **Abas (tab.php):** Geral (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad041ContentTiposVeiculos`, `cad041ContentTiposVeiculo`, `cad041FormCadastroTiposVeiculos`, `cad041GridTiposVeiculos`, `cad041TabTiposVeiculos`, `cad041TabCadastroTiposVeiculos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D126` (fotografia:7582): form em `prog/cad/cad041/form.php:27`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad041/form.php:80`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad042` — Bens - Tipos

- **Menu:** Configuração › Bens - Tipos, acesso C029 nº 75 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:251`)
- **Arquivos:** 4 arquivos. 337 linhas. `form.php` (140), `grid.php` (101), `tab.php` (53), `content.php` (43)
- **Abas (tab.php):** Geral (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad042ContentBensTipos`, `cad042ContentBensTIpos`, `cad042FormCadastroBensTipos`, `cad042GridBensTipos`, `cad042TabBensTipos`, `cad042TabCadastroBensTipos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D128` (fotografia:7620): form em `prog/cad/cad042/form.php:27`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad042/form.php:81`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad043` — Bens - Especificações

- **Menu:** Configuração › Bens - Especificações, acesso C029 nº 76 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:252`)
- **Arquivos:** 4 arquivos. 342 linhas. `form.php` (143), `grid.php` (103), `tab.php` (53), `content.php` (43)
- **Abas (tab.php):** Geral (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad043ContentBensEspecificacoes`, `cad043ContentBensEspecificacoesIncluir`, `cad043FormCadastroBensEspecificacoes`, `cad043GridBensEspecificacoes`, `cad043TabBensEspecificacoes`, `cad043TabCadastroBensEspecificacoes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D129` (fotografia:7638): form em `prog/cad/cad043/form.php:27`
- **Tabelas só lidas na tela (1):** `D128` (1×, `prog/cad/cad043/grid.php:29`)
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad043/form.php:83`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas
- **Aberta por:** `cad/cad002`

### `prog/cad/cad044` — Textos de Documento

- **Menu:** Configuração › Textos de Documento, acesso C029 nº 80 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:259`)
- **Arquivos:** 7 arquivos. 706 linhas. `grid.php` (424), `form.php` (141), `tab.php` (55), `content.php` (43), `form_func-ajax.php` (17), `form_func-js.php` (15), `grid_func-callback.php` (11)
- **Abas (tab.php):** Geral (`tab.php:18`), Recibo de Locação (`tab.php:19`), Certificado de Inspeção (`tab.php:20`), Certificado de Conformidade (`tab.php:21`), Textos De Documentos (`tab.php:42`)
- **Ações (`case` do roteador, 10):** `cad044ContentTextosDeDocumentos`, `cad044ContentCadastroTextosDeDocumentos`, `cad044CadastroTextosDeDocumentos`, `alteraEstadoTextosDeDocumentos`, `cad044GridTextosDeDocumentosGeral`, `cad044GridTextosDeDocumentosReciboLocacao`, `cad044GridTextosDeDocumentosCertificadoInspecao`, `cad044GridTextosDeDocumentosCertificadoConformidade`, `cad044TabTextosDeDocumentos`, `cad044TabCadastroTextosDeDocumentos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D130` (fotografia:7656): form em `prog/cad/cad044/form.php:29`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad044/form.php:64`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad044/form.php:81`)
- **Grafo (Graphify):** 9 nós, 2 funções definidas: `alteraEstadoTextosDeDocumentos()`, `exibirCodigoHTML()`

### `prog/cad/cad045` — Veículos - Especificações

- **Menu:** Configuração › Veículos - Especificações, acesso C029 nº 79 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:256`)
- **Arquivos:** 4 arquivos. 343 linhas. `form.php` (142), `grid.php` (105), `tab.php` (53), `content.php` (43)
- **Abas (tab.php):** Geral (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad045ContentEspecificacoesVeiculos`, `cad045ContentVeiculosEspecificacoesIncluir`, `cad045FormCadastroVeiculosEspecificacoes`, `cad045GridVeiculosEspecificacoes`, `cad043TabVaiculosEspecificacoes`, `cad045TabCadastroVeiculosEspecificacoes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D131` (fotografia:7677): form em `prog/cad/cad045/form.php:27`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad045/form.php:83`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas
- **Aberta por:** `cad/cad001`

### `prog/cad/cad046` — Geral (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 383 linhas. `form.php` (161), `grid.php` (126), `tab.php` (53), `content.php` (43)
- **Abas (tab.php):** Geral (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `cad046ContentMotivosCancelamento`, `cad046ContentMotivosCancelamentoIncluir`, `cad046FormCadastroMotivosCancelamento`, `cad046GridMotivosCancelamento`, `cad046TabMotivosCancelamento`, `cad046TabCadastroMotivosCancelamento`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D047` (fotografia:5424): form em `prog/cad/cad046/form.php:27`
- **Chaves C031 lidas (1):** `conferenciaVolumes`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad046/form.php:101`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas
- **Aberta por:** `est/est009`, `est/est019`

### `prog/cad/cad047` — Geral (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 374 linhas. `form.php` (141), `grid.php` (122), `tab.php` (56), `content.php` (55)
- **Abas (tab.php):** Geral (`tab.php:18`), Geral (`tab.php:41`), Ecommerce (`tab.php:42`)
- **Ações (`case` do roteador, 6):** `cad047ContentTransportesTipos`, `cad047ContentTransportesTiposIncluir`, `cad047FormCadastroTransportesTipos`, `cad047GridTransportesTipos`, `cad047TabTransportesTipos`, `cad047TabCadastroTransportesTipos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D036` (fotografia:5123): form em `prog/cad/cad047/form.php:27`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad047/form.php:95`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad048` — Veículos - Modelos

- **Menu:** Configuração › Veículos - Modelos, acesso C029 nº 90 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:257`)
- **Arquivos:** 4 arquivos. 373 linhas. `form.php` (152), `grid.php` (117), `tab.php` (54), `content.php` (50)
- **Abas (tab.php):** Modelos Veículos (`tab.php:23`), Modelo Veículo (`tab.php:41`)
- **Ações (`case` do roteador, 6):** `cad048ContentPrincipal`, `cad048contentCadastroModelo`, `cad048FormCadastroModelo`, `cad048GridModelosVeiculo`, `cad048TabPrincipal`, `cad048TabCadastroModelo`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D133` (fotografia:7720): form em `prog/cad/cad048/form.php:41`
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad048/form.php:77`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad048/form.php:97`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad049` — Veículos - Cores

- **Menu:** Configuração › Veículos - Cores, acesso C029 nº 91 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:258`)
- **Arquivos:** 4 arquivos. 374 linhas. `form.php` (153), `grid.php` (117), `tab.php` (54), `content.php` (50)
- **Abas (tab.php):** Cores Veículos (`tab.php:23`), Cor Veículo (`tab.php:41`)
- **Ações (`case` do roteador, 6):** `cad049ContentPrincipal`, `cad049contentCadastroCor`, `cad049FormCadastroCor`, `cad049GridCoresVeiculo`, `cad049TabPrincipal`, `cad049TabCadastroCor`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D134` (fotografia:7737): form em `prog/cad/cad049/form.php:41`
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad049/form.php:77`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad049/form.php:97`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas
- **Aberta por:** `cad/cad050`, `srv/srv004`

### `prog/cad/cad050` — Local Locação (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 388 linhas. `form.php` (172), `grid.php` (117), `content.php` (50), `tab.php` (49)
- **Abas (tab.php):** Local Locação (`tab.php:18`)
- **Ações (`case` do roteador, 5):** `cad050contentCadastroLocacaoLocal`, `cad049contentCadastroCor`, `cad050FormLocacaoLocal`, `cad049GridCoresVeiculo`, `cad050TabCadastroLocacaoLocal`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D004` (fotografia:3234): form em `prog/cad/cad050/form.php:41`
- **Tabelas só lidas na tela (1):** `D134` (1×, `prog/cad/cad050/grid.php:36`)
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad050/form.php:95`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad050/form.php:115`)
- **Abre as telas:** `cad/cad049`
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad051` — Etiquetas - Modelos

- **Menu:** Configuração › Etiquetas - Modelos, acesso C029 nº 119 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:260`)
- **Arquivos:** 7 arquivos. 1.303 linhas. `form.php` (557), `form_func-ajax.php` (343), `grid.php` (158), `tab.php` (93), `content.php` (74), `grid_func-callback.php` (41), `form_func-js.php` (37)
- **Abas (tab.php):** Etiquetas - Modelos (`tab.php:18`), Cadastro Modelo (`tab.php:39`), Impressão Genérica (`tab.php:60`), Selecione o Modelo (`tab.php:81`)
- **Ações (`case` do roteador, 13):** `cad051ContentPrincipal`, `cad051ContentCadastroModelo`, `cad051ContentImpressaoGenerica`, `cad051SelecionarModelo`, `cad051FormModeloEtiqueta`, `cad051FormImpressaoGenerica`, `cad051SelecionarModelo`, `imprimirEtiquetas`, `cad051GridPrincipal`, `cad051TabPrincipal`, `cad051TabCadastroModelo`, `cad051TabImpressaGenerica`, `cad051SelecionarModelo`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D138` (fotografia:7895): form em `prog/cad/cad051/form.php:30`, `prog/cad/cad051/form.php:189` e mais 1
- **Tabelas só lidas na tela (14):** `D004` (5×, `prog/cad/cad051/form_func-ajax.php:95` e mais 4), `D024` (4×, `prog/cad/cad051/form_func-ajax.php:127` e mais 3), `D009` (4×, `prog/cad/cad051/form_func-ajax.php:159` e mais 3), `D049` (2×, `prog/cad/cad051/form_func-ajax.php:170` e mais 1), `D037` (2×, `prog/cad/cad051/form_func-ajax.php:172` e mais 1), `D082` (2×, `prog/cad/cad051/form_func-ajax.php:177` e mais 1), `T066` (2×, `prog/cad/cad051/form_func-ajax.php:178` e mais 1), `T006` (2×, `prog/cad/cad051/form_func-ajax.php:241` e mais 1), `D001` (1×, `prog/cad/cad051/form_func-ajax.php:171`), `D003` (1×, `prog/cad/cad051/form_func-ajax.php:173`), `D060` (1×, `prog/cad/cad051/form_func-ajax.php:174`), `D002` (1×, `prog/cad/cad051/form_func-ajax.php:175`), `D015` (1×, `prog/cad/cad051/form_func-ajax.php:176`), `T005` (1×, `prog/cad/cad051/form_func-ajax.php:263`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad051/form.php:81`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad051/form.php:101`)
- **Abre as telas:** `sistema/etiquetas`
- **Grafo (Graphify):** 11 nós, 4 funções definidas: `cad051ImprimirEtiquetas()`, `cad051ImprimirEtiquetasTesteGenerico()`, `cad051CampoTipo()`, `campoImpTag()`
- **Aberta por:** `cad/cad019`

### `prog/cad/cad052` — Composição (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 10 arquivos. 1.792 linhas. `form.php` (746), `grid.php` (368), `outro.php` (196), `content.php` (148), `grid_func-callback.php` (85), `tab.php` (75), `grid_func-ajax.php` (47), `form_func-ajax.php` (45), `grid_func-js.php` (45), `form_func-js.php` (37)
- **Abas (tab.php):** Composição (`tab.php:22`), Processos (`tab.php:23`), Demonstração Custo (`tab.php:24`), Outro (`tab.php:25`), Processos (`tab.php:44`), Consumiveis (`tab.php:63`)
- **Ações (`case` do roteador, 22):** `cad052ContentEngenharia`, `cad052ContentConsumiveis`, `cad052ContentConsumivel`, `cad052contentItensComposicao`, `cad052contentProcessos`, `cad052ContentDemonstracao`, `cad052ContentProduto`, `cad052formItensComposicao`, `cad052formProcessos`, `cad052formConsumiveis`, `cad052formProduto`, `atualizarCamposProdutos`, `cad052gridComposicao`, `cad052gridProcessos`, `cad052gridConsumiveis`, `excluirItem`, `excluirProcesso`, `cad052Cabecalho`, `cad052Demonstracao`, `cad052TabGeral` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D001` (fotografia:2944): form em `prog/cad/cad052/form.php:650`
  - `D074` (fotografia:6519): form em `prog/cad/cad052/form.php:44`
  - `D075` — PROCESSOS (fotografia:6562): form em `prog/cad/cad052/form.php:276`
  - `D076` (fotografia:6612): form em `prog/cad/cad052/form.php:465`
- **Tabelas só lidas na tela (10):** `D009` (16×, `prog/cad/cad052/form.php:60` e mais 15), `D049` (13×, `prog/cad/cad052/form.php:51` e mais 12), `D037` (9×, `prog/cad/cad052/form.php:62` e mais 8), `D082` (5×, `prog/cad/cad052/form.php:53` e mais 4), `D041` (3×, `prog/cad/cad052/form.php:59` e mais 2), `D078` (3×, `prog/cad/cad052/grid.php:166` e mais 2), `D079` (3×, `prog/cad/cad052/grid.php:167` e mais 2), `D001A` (2×, `prog/cad/cad052/form.php:697` e mais 1), `D005` (2×, `prog/cad/cad052/form_func-ajax.php:24` e mais 1), `D083` (1×, `prog/cad/cad052/form_func-ajax.php:20`)
- **Classes e métodos chamados:** `CAD052` (143 linhas): `engenhariaLiberadaProcessoIncluir` (`CAD052.php:17`), `engenhariaLiberada` (`CAD052.php:6`), `engenhariaDuplicada` (`CAD052.php:21`), `incluirEngenharia` (`CAD052.php:26`), `engenhariaLiberadaProcesso` (`CAD052.php:14`), `alterarProduto` (`CAD052.php:71`), `engenhariaLiberadaIncluir` (`CAD052.php:10`), `excluirItem` (`CAD052.php:52`), `excluirProcesso` (`CAD052.php:130`)
- **Tabelas gravadas pelos métodos chamados:** `D001` (CAD052::alterarProduto), `D001A` (CAD052::alterarProduto), `D009` (CAD052::alterarProduto), `D037` (CAD052::alterarProduto), `D074` (CAD052::excluirItem), `D075` (CAD052::excluirProcesso)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad052/form.php:160`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad052/form.php:185`)
- **Abre as telas:** `cad/cad002`
- **Grafo (Graphify):** 21 nós, 11 funções definidas: `campoConsumiveis()`, `campoExclusaoItem()`, `campoExclusaoProcesso()`, `custoTotal()`, `custoTotalMateriais()`, `custoTotalMaterial()` …
- **Aberta por:** `cad/cad002`, `cad/cad020`, `cad/cad053`

### `prog/cad/cad053` — P.C.P.

- **Menu:** Configuração › P.C.P., acesso C029 nº 120 "P.C.P." (`bibliotecas/menu.php:120`)
- **Menu:** Configuração › Engenharia, acesso C029 nº 120 "P.C.P." (item comentado no Menu) (`bibliotecas/menu.php:261`)
- **Arquivos:** 10 arquivos. 859 linhas. `form.php` (318), `grid.php` (188), `outro.php` (72), `content.php` (56), `grid_func-callback.php` (50), `grid_func-ajax.php` (46), `grid_func-js.php` (45), `form_func-ajax.php` (35), `tab.php` (28), `form_func-js.php` (21)
- **Abas (tab.php):** Máquinas (`tab.php:15`), Funções (`tab.php:16`)
- **Ações (`case` do roteador, 12):** `cad053ContentEngenharia`, `cad053contentFuncao`, `cad053contentMaquina`, `cad053formMaquinas`, `cad053formFuncoes`, `atualizarCamposProdutos`, `cad053gridFuncoes`, `cad053gridMaquinas`, `excluirItem`, `excluirProcesso`, `cad052Cabecalho`, `cad053tabEngenharia`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D078` — FUNCAO (fotografia:6651): form em `prog/cad/cad053/form.php:186`
  - `D079` — MAQUINAS (fotografia:6669): form em `prog/cad/cad053/form.php:34`
- **Tabelas só lidas na tela (7):** `D075` (2×, `prog/cad/cad053/grid_func-callback.php:25` e mais 1), `D001` (1×, `prog/cad/cad053/form_func-ajax.php:16`), `D074` (1×, `prog/cad/cad053/grid_func-callback.php:12`), `D005` (1×, `prog/cad/cad053/outro.php:18`), `D037` (1×, `prog/cad/cad053/outro.php:19`), `D049` (1×, `prog/cad/cad053/outro.php:20`), `D009` (1×, `prog/cad/cad053/outro.php:21`)
- **Classes e métodos chamados:** `CAD052` (143 linhas): `excluirItem` (`CAD052.php:52`), `excluirProcesso` (`CAD052.php:130`)
- **Tabelas gravadas pelos métodos chamados:** `D074` (CAD052::excluirItem), `D075` (CAD052::excluirProcesso)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad053/form.php:83`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad053/form.php:103`)
- **Abre as telas:** `cad/cad052`
- **Grafo (Graphify):** 17 nós, 7 funções definidas: `campoConsumiveis()`, `campoExclusaoItem()`, `campoExclusaoProcesso()`, `custoTotal()`, `cad052ExcluirItem()`, `cad052ExcluirProcesso()` …

### `prog/cad/cad054` — Rotas

- **Menu:** Cadastros › Rotas, acesso C029 nº 158 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:179`)
- **Arquivos:** 4 arquivos. 372 linhas. `form.php` (156), `grid.php` (117), `content.php` (50), `tab.php` (49)
- **Abas (tab.php):** Rotas (`tab.php:18`), Rota (`tab.php:36`)
- **Ações (`case` do roteador, 6):** `cad054ContentRotas`, `cad054ContentRota`, `cad054FormCadastroRota`, `cad054GridRotas`, `cad054TabRotas`, `cad054TabCadastroRotas`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T208` (fotografia:24292): form em `prog/cad/cad054/form.php:41`
- **Classes e métodos chamados:** `CAD001` (1497 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad054/form.php:79`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad054/form.php:99`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad055` — Bens - Impressoras de CF

- **Menu:** Configuração › Bens - Impressoras de CF, acesso C029 nº 159 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:254`)
- **Arquivos:** 5 arquivos. 371 linhas. `form.php` (170), `grid.php` (88), `content.php` (50), `tab.php` (49), `ajax.php` (14)
- **Abas (tab.php):** Teclas de Atalho (`tab.php:18`), Tecla de Atalho (`tab.php:36`)
- **Ações (`case` do roteador, 7):** `verificarPermissao`, `principal`, `cadastro`, `cadastro`, `principal`, `principal`, `cadastro`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D142` (fotografia:7977): form em `prog/cad/cad055/form.php:47`
- **Tabelas só lidas na tela (1):** `C029` (1×, `prog/cad/cad055/grid.php:24`)
- **Mensagens e validações visíveis (3):**
  - "Mensagem de alerta?" (`prog/cad/cad055/form.php:24`)
  - "Registro Alterado com Sucesso!" (`prog/cad/cad055/form.php:93`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad055/form.php:113`)
- **Abre as telas:** `cad/cad030`
- **Grafo (Graphify):** 5 nós, 0 funções definidas

### `prog/cad/cad056` — Bancos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 290 linhas. `form.php` (143), `grid.php` (82), `content.php` (40), `tab.php` (25)
- **Abas (tab.php):** Bancos (`tab.php:12`)
- **Ações (`case` do roteador, 5):** `cad056ContentBancos`, `cad056Bancos`, `cad056FormBancos`, `cad056GridBancos`, `cad056TabBancos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D062` (fotografia:5962): form em `prog/cad/cad056/form.php:30`
- **Classes e métodos chamados:** `CAD056` (24 linhas): `verificarCodigoBanco` (`CAD056.php:14`)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad056/form.php:74`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad056/form.php:94`)
  - "Já existe um banco com este número!" (`prog/cad/cad056/form.php:109`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas
- **Aberta por:** `sistema/outros`

### `prog/cad/cad057` — Impressoras

- **Menu:** Configuração › Impressoras, acesso C029 nº 176 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:262`)
- **Arquivos:** 4 arquivos. 278 linhas. `form.php` (133), `grid.php` (81), `content.php` (39), `tab.php` (25)
- **Abas (tab.php):** Impressoras (`tab.php:12`)
- **Ações (`case` do roteador, 5):** `cad057ContentImpressoras`, `cad057Impressoras`, `cad057FormImpressoras`, `cad057GridImpressoras`, `cad057TabImpressoras`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D147` (fotografia:8114): form em `prog/cad/cad057/form.php:27`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad057/form.php:67`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad057/form.php:87`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas
- **Aberta por:** `cad/cad058`

### `prog/cad/cad058` — Geral (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 185 "Opções" (`prog/cad/cad058/tab.php:14` e mais 1)
- **Arquivos:** 6 arquivos. 7.127 linhas. `form.php` (5760), `content.php` (916), `tab.php` (317), `grid.php` (86), `form_func-ajax.php` (25), `form_func-js.php` (23)
- **Abas (tab.php):** Geral (`tab.php:16`), Cadastros (`tab.php:17`), Vendas (`tab.php:18`), Estoque (`tab.php:19`), Financeiro (`tab.php:20`), Comex (`tab.php:22`), Integração (`tab.php:24`), MDF-e (`tab.php:25`), SPED (`tab.php:26`), Jobs (`tab.php:27`), Interface (`tab.php:30`), Geral (`tab.php:48`) …
- **Ações (`case` do roteador, 77):** `cad058ContentConfGlobal`, `cad058ContentConfGeral`, `cad058ContentConfCadastros`, `cad058ContentConfVendas`, `cad058ContentConfEstoque`, `cad058ContentConfFinanceiro`, `xcad058ContentConfImportacao`, `cad058ContentConfMobile`, `cad058ContentConfECommerce`, `cad058ContentConfECommerceMagento1Oauth`, `cad058ContentConfECommerceMagento1Soap`, `cad058ContentConfTrello`, `cad058ContentConfECommerceTray`, `cad058ContentConfMeli`, `openIA`, `cad058ContentConfECommerceNuvemShop`, `cad058ContentConfMagazord`, `cad058ContentConfECommercePluggto`, `cad058ContentConfCyberlog`, `cad058ContentConfDuimp` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C007` (fotografia:396): form em `prog/cad/cad058/form.php:5026`
- **Tabelas só lidas na tela (8):** `C031` (53×, `prog/cad/cad058/form.php:166` e mais 52), `D154` (7×, `prog/cad/cad058/form.php:1491` e mais 6), `C004` (3×, `prog/cad/cad058/form.php:2351` e mais 2), `D166` (3×, `prog/cad/cad058/form.php:4499` e mais 2), `D024` (2×, `prog/cad/cad058/form.php:1728` e mais 1), `D007` (2×, `prog/cad/cad058/form.php:2351` e mais 1), `T003` (2×, `prog/cad/cad058/form.php:4311` e mais 1), `T015` (1×, `prog/cad/cad058/form.php:27`)
- **Classes e métodos chamados:** `CAD058` (35 linhas): `atualizarConfGlobal` (`CAD058.php:12`)
- **Tabelas gravadas pelos métodos chamados:** `C031` (CAD058::atualizarConfGlobal)
- **Chaves C031 lidas (2):** `grupoSTContasPagar`, `grupoXmlContasPagarDi`
- **Mensagens e validações visíveis (3):**
  - "Configurações salvas com sucesso!" (`prog/cad/cad058/form.php:201`)
  - "Número de parcelas padrão não pode ser maior que o número máximo de parcelas." (`prog/cad/cad058/form.php:2620`)
  - "Registro Alterado com Sucesso!" (`prog/cad/cad058/form.php:5069`)
- **Abre as telas:** `cad/cad057`
- **Grafo (Graphify):** 7 nós, 1 funções definidas: `alteraConta()`

### `prog/cad/cad059` — Moedas (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 288 linhas. `form.php` (130), `grid.php` (82), `content.php` (50), `tab.php` (26)
- **Abas (tab.php):** Moedas (`tab.php:15`)
- **Ações (`case` do roteador, 5):** `cad059ContentMoedas`, `cad059ContentCadastroMoeda`, `cad059FormCadastroMoeda`, `cad059GridMoedas`, `cad059TabMoedas`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D044` (fotografia:5358): form em `prog/cad/cad059/form.php:26`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad059/form.php:65`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad059/form.php:85`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad060` — Variavéis IPV (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 6 arquivos. 664 linhas. `form.php` (260), `grid.php` (224), `grid_func-callback.php` (63), `content.php` (49), `tab.php` (44), `form_func-ajax.php` (24)
- **Abas (tab.php):** Variavéis IPV (`tab.php:15`), Tipo agenda (`tab.php:33`)
- **Ações (`case` do roteador, 14):** `cad060ContentMontagemIPV`, `cad060ContentIncluirTipoAgenda`, `cad059FormCadastroMoeda`, `cad060FormIncluirTipoAgenda`, `cad060IncluirVariavelIPV`, `cad060GridMontagemIPV`, `cad060GridTipoAgenda`, `1`, `2`, `3`, `4`, `5`, `cad060TabMontagemIPV`, `cad060TabIncluirTipoAgenda`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D044` (fotografia:5358): form em `prog/cad/cad060/form.php:26`
  - `D155` (fotografia:8359): form em `prog/cad/cad060/form.php:151`
  - `T249` (fotografia:26422): INSERT em `prog/cad/cad060/form_func-ajax.php:11`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad060/form.php:65`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad060/form.php:85`)
- **Grafo (Graphify):** 10 nós, 4 funções definidas: `mostraTipoVariavel()`, `nomeCampoAlteraPreco()`, `nomeCampoCalculado()`, `nomeDoNivel()`

### `prog/cad/cad061` — Pedidos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 201 "Alterar comissão vendedor" (`prog/cad/cad061/grid.php:61`); 202 "Alterar comissão representante" (`prog/cad/cad061/grid.php:63`)
- **Arquivos:** 10 arquivos. 17.250 linhas. `grid.php` (7625), `form.php` (4940), `content.php` (1304), `form_func-ajax.php` (1196), `grid_func-ajax.php` (533), `grid_func-callback.php` (475), `outro.php` (411), `form_func-js.php` (296), `tab.php` (292), `grid_func-js.php` (178)
- **Abas (tab.php):** Pedidos (`tab.php:26`), Produtos (`tab.php:27`), Serviços (`tab.php:28`), Troca (`tab.php:29`), Processos (`tab.php:109`), Materiais (`tab.php:110`), Produto (`tab.php:131`), IPI (`tab.php:132`), Icms (`tab.php:133`), Pis/Cofins (`tab.php:134`), Agregados (`tab.php:135`), Pagamento (`tab.php:158`) …
- **Ações (`case` do roteador, 213):** `ven002content01`, `cad061content02`, `ven002ContentFaturarEmergencia`, `ven002content03`, `cad061content04`, `cad061contentform`, `ven002contentTrocas`, `ven002content05`, `ven002content06`, `ven002content07`, `ven002content08`, `cad061ContentEditarProduto`, `ven002ContentAplicarIpv`, `ven002ContentImportarItens`, `ven002ContentAbrePagamento`, `cad061ContentPagamento`, `ven002ContentEmail`, `ven002ContentDocumentos`, `ven002ContentDetalhesCheque`, `ven002ContentAplicarDesconto` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D009` (fotografia:3608): UPDATE em `prog/cad/cad061/grid_func-ajax.php:221`
  - `D157` (fotografia:8398): UPDATE/form em `prog/cad/cad061/content.php:78`, `prog/cad/cad061/content.php:88` e mais 7
  - `D158` (fotografia:8688): form em `prog/cad/cad061/form.php:434`, `prog/cad/cad061/form.php:1250`
  - `D159` (fotografia:8932): form em `prog/cad/cad061/form.php:1584`
  - `N001` (fotografia:12214): UPDATE em `prog/cad/cad061/content.php:102`
  - `T005` (fotografia:15218): UPDATE/form em `prog/cad/cad061/form.php:1908`, `prog/cad/cad061/form.php:2140` e mais 16
  - `T006` (fotografia:15620): UPDATE em `prog/cad/cad061/form.php:1871`, `prog/cad/cad061/grid_func-ajax.php:198` e mais 19
  - `T090` (fotografia:20866): form em `prog/cad/cad061/form.php:2532`
  - `T138` — Tabela de trocas (fotografia:22429): form em `prog/cad/cad061/form.php:2758`
  - `T147` (fotografia:22739): form em `prog/cad/cad061/form.php:722`
  - `T148` (fotografia:22795): form em `prog/cad/cad061/form.php:1102`
  - `T149` (fotografia:22819): form em `prog/cad/cad061/form.php:921`
  - `T167` (fotografia:23168): form em `prog/cad/cad061/form.php:2357`, `prog/cad/cad061/form.php:3053`
  - `T174` (fotografia:23369): UPDATE em `prog/cad/cad061/grid_func-ajax.php:260`, `prog/cad/cad061/grid_func-ajax.php:264`
  - `T178` (fotografia:23467): INSERT em `prog/cad/cad061/form_func-ajax.php:812`, `prog/cad/cad061/form_func-ajax.php:991`
  - `T189` (fotografia:23794): form em `prog/cad/cad061/form.php:4199`, `prog/cad/cad061/form.php:4289`
  - `T204` (fotografia:24194): form em `prog/cad/cad061/form.php:3375`
  - `T251` (fotografia:26474): DELETE/INSERT em `prog/cad/cad061/grid_func-ajax.php:518`, `prog/cad/cad061/grid_func-ajax.php:523`
  - `T260` (fotografia:26743): INSERT/UPDATE em `prog/cad/cad061/grid.php:4808`, `prog/cad/cad061/grid.php:4810`
- **Tabelas só lidas na tela (80):** `C007` (92×, `prog/cad/cad061/form.php:79` e mais 91), `D024` (50×, `prog/cad/cad061/form.php:50` e mais 49), `D049` (47×, `prog/cad/cad061/form.php:454` e mais 46), `D001` (34×, `prog/cad/cad061/form.php:1875` e mais 33), `D082` (33×, `prog/cad/cad061/form.php:457` e mais 32), `D006` (27×, `prog/cad/cad061/form_func-ajax.php:737` e mais 26), `D018` (22×, `prog/cad/cad061/form.php:109` e mais 21), `D020` (17×, `prog/cad/cad061/form.php:50` e mais 16), `D037` (16×, `prog/cad/cad061/form.php:458` e mais 15), `C004` (15×, `prog/cad/cad061/form.php:1602` e mais 14), `T007` (15×, `prog/cad/cad061/grid.php:522` e mais 14), `D133` (12×, `prog/cad/cad061/form.php:152` e mais 11), `D134` (12×, `prog/cad/cad061/form.php:152` e mais 11), `D021` (12×, `prog/cad/cad061/grid.php:103` e mais 11), `D005` (10×, `prog/cad/cad061/form.php:463` e mais 9), `T075` (9×, `prog/cad/cad061/grid.php:3539` e mais 8), `T055` (9×, `prog/cad/cad061/grid.php:3563` e mais 8), `T066` (8×, `prog/cad/cad061/form.php:464` e mais 7), `D004` (8×, `prog/cad/cad061/form.php:465` e mais 7), `T004` (8×, `prog/cad/cad061/form_func-ajax.php:157` e mais 7), `T008` (8×, `prog/cad/cad061/form_func-ajax.php:1162` e mais 7), `D013` (8×, `prog/cad/cad061/grid.php:92` e mais 7), `C008` (7×, `prog/cad/cad061/form.php:460` e mais 6), `T219` (7×, `prog/cad/cad061/form_func-ajax.php:1167` e mais 6), `R001` (7×, `prog/cad/cad061/grid.php:1558` e mais 6) …
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `retornaPrecoVenda` (`CAD002.php:8729`), `cad002RetornaPrecoTabela` (`CAD002.php:5779`), `D001_reprocessa_historico` (`CAD002.php:1159`), `atualizaPrecoTabelaPedido` (`CAD002.php:8048`); `CAD061` (1139 linhas): `incluirProduto` (`CAD061.php:13`), `calcularParcelaCombo` (`CAD061.php:952`), `atualizarParcelas` (`CAD061.php:636`), `processarPedidosPeriodicos` (`CAD061.php:993`), `excluirProduto` (`CAD061.php:572`); `EST017` (1649 linhas): `atualizarComponentes` (`EST017.php:894`); `GFormWrap` (29 linhas) (só incluída); `VEN001` (15360 linhas): `retornaCFOP` (`VEN001.php:8750`), `retornaAliquotas` (`VEN001.php:10380`), `retornaltimoPrecoVenda` (`VEN001.php:9934`), `verificaAlcadaNovo` (`VEN001.php:11531`); `VEN002` (10343 linhas): `alteraLista` (`VEN002.php:5732`), `insereServico` (`VEN002.php:1777`), `calculaTotalGeral` (`VEN002.php:4460`), `calculaPercentualDescontoPedido` (`VEN002.php:4569`), `calculaPrazoMedioAlcadaVendedor` (`VEN002.php:4673`), `calculaPrazoMedioAlcadaSupervisor` (`VEN002.php:4703`), `verificaFuturoSaldoProduto` (`VEN002.php:5556`), `retornaMaiorDescontoPedido` (`VEN002.php:4732`), `motivoPedidoLiberacao` (`VEN002.php:4933`), `faturarEmergencia` (`VEN002.php:3068`) …
- **Tabelas gravadas pelos métodos chamados:** `D009` (CAD002::D001_reprocessa_historico), `D009A` (CAD002::D001_reprocessa_historico), `D083` (CAD061::incluirProduto), `D085` (VEN002::excluirPedido), `D157` (CAD061::atualizarParcelas), `D158` (CAD061::excluirProduto, CAD061::incluirProduto), `D159` (CAD061::atualizarParcelas), `T001` (CAD002::D001_reprocessa_historico), `T005` (CAD061::processarPedidosPeriodicos, VEN001::verificaAlcadaNovo, VEN002::alteraLista…), `T005A` (VEN002::excluirPedido), `T006` (CAD002::atualizaPrecoTabelaPedido, CAD061::processarPedidosPeriodicos, VEN002::alteraLista…), `T006A` (VEN002::excluirProduto, VEN002::ven002atualizarCFOP), `T066` (CAD002::D001_reprocessa_historico), `T090` (VEN002::excluirPedido, VEN002::inserirParcela, VEN002::ven002AtualizarParcelas), `T138` (VEN002::incluirProdutoTroca) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_Classificacao_Fiscal` (`prog/cad/cad061/form_func-ajax.php:61`), `D009_IPI` (`prog/cad/cad061/form_func-ajax.php:116`), `D009_Quantidade_DF` (`prog/cad/cad061/grid_func-callback.php:217` e mais 3), `D009_Quantidade_DM` (`prog/cad/cad061/grid_func-callback.php:233` e mais 1), `D009_Quantidade_MP` (`prog/cad/cad061/grid_func-callback.php:252` e mais 3), `D009_Quantidade_Pedido` (`prog/cad/cad061/form.php:1281` e mais 2), `D009_Saldo_DF` (`prog/cad/cad061/grid.php:3532`), `T003_Prazos` (`prog/cad/cad061/grid.php:4336`), `T005_Gravar_Totalizacao_4` (`prog/cad/cad061/form.php:1885`), `T005_Prazos` (`prog/cad/cad061/grid.php:4451` e mais 1), `T005_Status_Pedido` (`prog/cad/cad061/form_func-ajax.php:796` e mais 8), `T006_Gravar_Totalizacao_4` (`prog/cad/cad061/form.php:1883`), `T007_Prazos` (`prog/cad/cad061/grid.php:724` e mais 1)
- **Chaves C031 lidas (35):** `C004PrecoTransferenciaGridProdutos`, `alterarVendedor`, `alterarVendedorAusente`, `botaoAplicarDesconto`, `campoNomeEmpresa`, `desabilitarProducaoEmergencia`, `diasMedioPrazo`, `diasVencPedido`, `editarDescricaoProdutoVenda`, `editarUnidadeProdutoVenda`, `exibirLimitesOrcamentoPedido`, `exibirRegistroAgendaManual` …
- **Mensagens e validações visíveis (28):**
  - "Confirmar Impressão?" (`prog/cad/cad061/content.php:179`)
  - "Deseja excluir os itens marcados?" (`prog/cad/cad061/content.php:483`)
  - "Deseja atualizar as aliquotas?" (`prog/cad/cad061/content.php:508`)
  - "Deseja atualizar o preço tabela dos produtos?" (`prog/cad/cad061/content.php:534`)
  - "Deseja atualizar a engenharia dos produtos igual ao cadastro de produtos?" (`prog/cad/cad061/content.php:556`)
  - "Qual atualização você deseja?" (`prog/cad/cad061/content.php:562`)
  - "Este pedido não existe!" (`prog/cad/cad061/content.php:41`)
  - "Este pedido retornou do almoxarifado." (`prog/cad/cad061/content.php:77`)
  - … mais 20
- **Abre as telas:** `ven/ven002`, `cad/cad002`, `crm/crm001`, `ven/ven001`, `cad/cad001`, `ven/ven003`, `est/est017`, `etc/etc006`, `est/est015`, `est/est002` …
- **Grafo (Graphify):** 56 nós, 46 funções definidas: `alteraServico()`, `campoProcuraProduto()`, `campoProcuraProdutoEditar()`, `campoProcuraProdutoMaterial()`, `campoProcuraServicoPedido()`, `carregaCFOP_Contatos()` …. Chama fora da tela: `number_format()` (formulario.php)

### `prog/cad/cad062` — RMA (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 599 linhas. `form.php` (281), `grid.php` (199), `tab.php` (60), `content.php` (59)
- **Abas (tab.php):** RMA (`tab.php:15`), Geral (`tab.php:31`), Geral (`tab.php:47`)
- **Ações (`case` do roteador, 10):** `cad062RMA`, `cad062RMAIncluir`, `cad062TipoRMAIncluir`, `cad062FormCadastroStatusRMA`, `cad062FormCadastroTipoRMA`, `cad062GridRMA`, `cad062GridRMATipo`, `cad062TabRMA`, `cad062TabCadastroStatusRMA`, `cad062TabCadastroTipoRMA`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D162` (fotografia:9027): form em `prog/cad/cad062/form.php:28`
  - `D164` (fotografia:9062): form em `prog/cad/cad062/form.php:166`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad062/form.php:81`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad063` — Cliente - Grupos

- **Menu:** Cadastros › Cliente - Grupos, acesso C029 nº 240 "Grupo Clientes" (`bibliotecas/menu.php:17`)
- **Arquivos:** 4 arquivos. 364 linhas. `form.php` (145), `grid.php` (127), `tab.php` (47), `content.php` (45)
- **Abas (tab.php):** Cliente - Grupos (`tab.php:14`), Cliente - Grupos (`tab.php:35`)
- **Ações (`case` do roteador, 6):** `cad063ContentCadastroClienteGrupo`, `cad063CadastroClienteGrupoCadastro`, `cad063FormCadastroClienteGrupo`, `cad063GridCadastroClienteGrupo`, `cad063TabCadastroClienteGrupo`, `cad063TabCadastroCadastroClienteGrupo`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D046` (fotografia:5405): form em `prog/cad/cad063/form.php:28`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad063/form.php:86`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cad/cad064` — Veículos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 651 linhas. `grid.php` (298), `form.php` (216), `grid_func-callback.php` (56), `tab.php` (44), `content.php` (37)
- **Abas (tab.php):** Veículos (`tab.php:14`), Inativos (`tab.php:15`), Veículos (`tab.php:32`)
- **Ações (`case` do roteador, 7):** `cad064ContentCadastroVeiculos`, `cad064ContentFormCadastrarVeiculos`, `cad064FormCadastrarVeiculos`, `cad064GridCadastroVeiculos`, `cad064GridVeiculosInativos`, `cad064TabCadastroVeiculos`, `cad064TabFormCadastrarVeiculos`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D170` (fotografia:9197): form em `prog/cad/cad064/form.php:28`
- **Tabelas só lidas na tela (2):** `D018` (2×, `prog/cad/cad064/grid.php:40` e mais 1), `D109` (2×, `prog/cad/cad064/grid.php:41` e mais 1)
- **Mensagens e validações visíveis (3):**
  - "Não pode haver motorista para veículo tipo Reboque" (`prog/cad/cad064/form.php:123`)
  - "O número de placa para esta UF já foi cadastrada." (`prog/cad/cad064/form.php:152`)
  - "Registro Incluído com Sucesso!" (`prog/cad/cad064/form.php:156`)
- **Abre as telas:** `cad/cad010`
- **Grafo (Graphify):** 9 nós, 4 funções definidas: `validaCampoCarroceria()`, `validaCampoCategoria()`, `validaCampoStatus()`, `validaCampoTipoRodado()`
- **Aberta por:** `cad/cad031`, `fis/fis012`

### `prog/cad/cad065` — Lacres (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 502 linhas. `grid.php` (223), `form.php` (163), `content.php` (45), `tab.php` (43), `grid_func-callback.php` (28)
- **Abas (tab.php):** Lacres (`tab.php:14`), Inativos (`tab.php:15`), Lacres (`tab.php:31`)
- **Ações (`case` do roteador, 7):** `cad065ContentCadastroLacres`, `cad065LacresCadastro`, `cad065FormLacres`, `cad065GridLacres`, `cad065GridLacresInativos`, `cad065TabLacres`, `cad065TabCadastroLacres`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D171` (fotografia:9229): form em `prog/cad/cad065/form.php:29`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad065/form.php:104`)
- **Grafo (Graphify):** 8 nós, 3 funções definidas: `validaCampoStatus()`, `validaCampoTipo()`, `gridLinha()`
- **Aberta por:** `fis/fis012`

### `prog/cad/cad066` — Seguradoras (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 8 arquivos. 1.249 linhas. `form.php` (465), `grid.php` (455), `content.php` (94), `tab.php` (88), `outro.php` (61), `grid_func-callback.php` (48), `form_func-ajax.php` (22), `form_func-js.php` (16)
- **Abas (tab.php):** Seguradoras (`tab.php:14`), Inativas (`tab.php:15`), Seguradora (`tab.php:48`), Cabeçalho (`tab.php:54`), Cabeçalho (`tab.php:75`), Averbações (`tab.php:76`)
- **Ações (`case` do roteador, 17):** `cad066ContentCadastroSeguradoras`, `cad066ContentFormCadastrarSeguradoras`, `cad066ContentFormCadastrarApolices`, `cad066ContentGridCadastrarAverbacoes`, `cad066ContentFormCadastrarAverbacoes`, `cad066FormCadastrarSeguradoras`, `cad066FormCadastrarApolices`, `cad066FormCadastrarAverbacoes`, `alteraEstado`, `cad066GridCadastroSeguradoras`, `cad066GridSeguradorasInativas`, `gridCadastroApolices`, `cad066GridAverbacoes`, `cad066CabecalhoSeguradora`, `cad066TabCadastroSeguradoras`, `cad066TabFormCadastrarSeguradoras`, `cad066TabFormCadastrarApolices`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D172` (fotografia:9250): form em `prog/cad/cad066/form.php:39`
  - `D173` (fotografia:9277): form em `prog/cad/cad066/form.php:196`
  - `D174` (fotografia:9301): form em `prog/cad/cad066/form.php:342`
- **Tabelas só lidas na tela (2):** `D018` (3×, `prog/cad/cad066/grid.php:37` e mais 2), `D020` (3×, `prog/cad/cad066/grid.php:38` e mais 2)
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/cad/cad066/form.php:115`)
- **Grafo (Graphify):** 13 nós, 5 funções definidas: `campoTelefone()`, `colunaAverbacoes()`, `validaCampoStatus()`, `validaCampoStatusApolice()`, `alteraEstado()`. Chama fora da tela: `.log()` (CAD002.php)
- **Aberta por:** `cad/cad031`, `fis/fis012`

### `prog/cad/cad067` — Periféricos

- **Menu:** Configuração › Periféricos, acesso C029 nº 246 "Periféricos" (`bibliotecas/menu.php:124`)
- **Arquivos:** 5 arquivos. 448 linhas. `form.php` (169), `grid.php` (165), `tab.php` (46), `content.php` (43), `grid_func-callback.php` (25)
- **Abas (tab.php):** Impressora (`tab.php:17`), Cadastro (`tab.php:34`)
- **Ações (`case` do roteador, 6):** `cad067ContentPerifericos`, `cad067ContentCadastroImpressora`, `cad067FormCadastroImpressora`, `cad067GridImpressora`, `cad067TabImpressora`, `cad067TabCadastroImpressora`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D175` (fotografia:9320): form em `prog/cad/cad067/form.php:34`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad067/form.php:85`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad067/form.php:106`)
- **Grafo (Graphify):** 6 nós, 1 funções definidas: `tipoImpressora()`

### `prog/cad/cad068` — Nota Fiscal

- **Menu:** Configuração › Nota Fiscal, acesso C029 nº 266 "Nota Fiscal" (`bibliotecas/menu.php:122`)
- **Arquivos:** 4 arquivos. 352 linhas. `form.php` (162), `grid.php` (101), `tab.php` (46), `content.php` (43)
- **Abas (tab.php):** Participantes autorizados no XML (`tab.php:15`), Cadastro (`tab.php:33`)
- **Ações (`case` do roteador, 6):** `cad068ContentNotaFiscal`, `cad068ContentIncluirParticipante`, `cad068FormIncluirParticipante`, `cad068GridParticipanteXML`, `cad068TabNotaFiscal`, `cad068TabIncluirParticipante`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C043` (fotografia:2585): UPDATE/form em `prog/cad/cad068/form.php:35`, `prog/cad/cad068/form.php:98`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cad/cad068/form.php:78`)
  - "Registro Incluido com Sucesso!" (`prog/cad/cad068/form.php:100`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

