# Fichas de tela: `prog/etc` — Diversos (agenda, e-mail, tarefas, relatórios auxiliares)

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `etc/etc001` | Gerenciador de Links | 52* | 8 | 465 | 1 | 1 |
| `etc/etc006` | Emails | 13, 205, 50* | 12 | 4983 | 12 | 19 |
| `etc/etc007` | Auditoria | 272 | 5 | 446 | 0 | 5 |
| `etc/etc008` | Geral (rótulo da primeira aba) |  | 8 | 43130 | 36 | 37 |
| `etc/etc009` | Geral (rótulo da primeira aba) |  | 6 | 414 | 1 | 0 |
| `etc/etc010` | Geral (rótulo da primeira aba) |  | 4 | 369 | 1 | 2 |
| `etc/etc011` | BI Dashboards | 152, 152, 152* | 8 | 6672 | 4 | 26 |
| `etc/etc012` | Resumo Diário (rótulo da primeira aba) |  | 5 | 411 | 0 | 7 |
| `etc/etc013` | Controle Interno | 93* | 5 | 1064 | 2 | 1 |
| `etc/etc014` | Relatórios Salvos (rótulo da primeira aba) |  | 4 | 2247 | 1 | 12 |
| `etc/etc015` | Relatórios Salvos (rótulo da primeira aba) |  | 5 | 554 | 1 | 2 |
| `etc/etc016` | Conferência Cega | 20 | 3 | 652 | 0 | 6 |
| `etc/etc017` | Atividades (rótulo da primeira aba) |  | 6 | 2058 | 3 | 21 |
| `etc/etc018` | Crontab (rótulo da primeira aba) |  | 7 | 726 | 2 | 0 |
| `etc/etc019` |  |  | 1 | 545 | 1 | 0 |
| `etc/etc020` | E-mail (rótulo da primeira aba) |  | 3 | 334 | 2 | 0 |
| `etc/etc021` |  |  | 5 | 1016 | 1 | 3 |
| `etc/etc022` |  |  | 3 | 599 | 0 | 11 |

Total: 18 telas. 66.685 linhas. 63 tabelas gravadas. 130 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/etc/etc001` — Gerenciador de Links

- **Menu:** Outros › Gerenciador de Links, acesso C029 nº 52 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:236`)
- **Arquivos:** 8 arquivos. 465 linhas. `form.php` (176), `grid.php` (102), `outros.php` (59), `grid_func-ajax.php` (33), `tab.php` (32), `content.php` (29), `grid_func-callback.php` (26), `grid_func-js.php` (8)
- **Abas (tab.php):** Links (`tab.php:21`)
- **Ações (`case` do roteador, 7):** `etc001_content_urls`, `etc001_form_urls`, `etc001_grid_urls`, `excluir`, `etc001_outros_iframe`, `etc001_outros_iframe_conteudo`, `etc001_tab_urls`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T144` (fotografia:22649): DELETE/UPDATE/form em `prog/etc/etc001/form.php:39`, `prog/etc/etc001/grid_func-ajax.php:21` e mais 2
- **Tabelas só lidas na tela (1):** `D001` (1×, `prog/etc/etc001/grid.php:41`)
- **Classes e métodos chamados:** `FIN001` (7368 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/etc/etc001/form.php:95`)
  - "Registro Incluido com Sucesso!" (`prog/etc/etc001/form.php:115`)
- **Grafo (Graphify):** 11 nós, 3 funções definidas: `possuiCache()`, `tipoDocumento()`, `abrirVersaoCache()`

### `prog/etc/etc006` — Emails

- **Menu:** Vendas › Emails, acesso C029 nº 13 "Emails" (`bibliotecas/menu.php:19`)
- **Menu:** Outros › Email - Whitelist, acesso C029 nº 205 "Email - Whitelist" (`bibliotecas/menu.php:109`)
- **Menu:** Outros › Emails, acesso C029 nº 50 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:232`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 21 "Notas Fiscais Recebidas" (`prog/etc/etc006/form.php:136`)
- **Arquivos:** 12 arquivos. 4.983 linhas. `form.php` (2215), `grid.php` (744), `content.php` (585), `tab.php` (354), `grid_func-ajax.php` (255), `form_func-ajax.php` (182), `outro.php` (171), `grid_func-callback.php` (144), `gridWhitelist.php` (119), `leituraAnexo.php` (109), `grid_func-js.php` (73), `form_func-js.php` (32)
- **Abas (tab.php):** Emails Enviados (`tab.php:21`), Emails Recebidos (`tab.php:24`), Spam (`tab.php:25`), Lixeira (`tab.php:27`), Email (`tab.php:61`), Email (`tab.php:63`), Associações (`tab.php:65`), Email (`tab.php:88`), Cadastrar Contato (`tab.php:135`), Emails Recebidos (`tab.php:156`), Emails Enviados (`tab.php:158`), Lixeira (`tab.php:159`) …
- **Ações (`case` do roteador, 74):** `etc006ContentLeitura`, `etc006ContentEmail`, `etc006ContentEmailEnviar`, `etc006ContentCadastroContato`, `etc006ContentRetornosPendentesAgenda`, `etc006ContentWhitelist`, `etc006ContentEmailTarefa`, `etc006ContentSpam`, `etc006ContentLeituraAnexo`, `cadastroCliente`, `preCadastroProduto`, `cadastroProduto`, `orcamento`, `etc006ContentOrcamento`, `etc006ContentArquivarAnexo`, `etc006ContentEditarWhitelist`, `etc006FormEmail`, `etc006FormEmailEnviar`, `etc006FormAssoc`, `etc006FormCadastroContato` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D013` (fotografia:3952): INSERT/form em `prog/etc/etc006/form.php:1082`, `prog/etc/etc006/form.php:1102` e mais 1
  - `D085` (fotografia:6827): form em `prog/etc/etc006/form.php:1843`
  - `D087` (fotografia:6917): INSERT em `prog/etc/etc006/form_func-ajax.php:146`
  - `T002A` (fotografia:14567): UPDATE em `prog/etc/etc006/form.php:951`
  - `T003A` (fotografia:14848): UPDATE em `prog/etc/etc006/form.php:946`
  - `T007` (fotografia:16254): UPDATE em `prog/etc/etc006/form.php:919`, `prog/etc/etc006/form.php:922` e mais 1
  - `T117` (fotografia:21688): UPDATE em `prog/etc/etc006/form.php:925`
  - `T139` (fotografia:22467): INSERT/UPDATE/form em `prog/etc/etc006/form.php:957`, `prog/etc/etc006/form.php:1553` e mais 14
  - `T140` (fotografia:22532): INSERT em `prog/etc/etc006/form_func-ajax.php:139`
  - `T174` (fotografia:23369): UPDATE em `prog/etc/etc006/form.php:941`
  - `T199` — CARTA DE CORRECAO ELETRONICA (fotografia:24049): UPDATE em `prog/etc/etc006/form.php:931`
  - `T258` (fotografia:26698): form em `prog/etc/etc006/form.php:2091`
- **Tabelas só lidas na tela (19):** `C007` (20×, `prog/etc/etc006/content.php:69` e mais 19), `D024` (18×, `prog/etc/etc006/form.php:83` e mais 17), `T003` (15×, `prog/etc/etc006/form.php:946` e mais 14), `T075` (12×, `prog/etc/etc006/form.php:1007` e mais 11), `T259` (4×, `prog/etc/etc006/form.php:964` e mais 3), `T173` (3×, `prog/etc/etc006/form.php:934` e mais 2), `D020` (3×, `prog/etc/etc006/form.php:1996` e mais 2), `D018` (3×, `prog/etc/etc006/form.php:1996` e mais 2), `D001` (2×, `prog/etc/etc006/form.php:109` e mais 1), `T011` (1×, `prog/etc/etc006/form.php:1002`), `T166` (1×, `prog/etc/etc006/form.php:1017`), `T196` (1×, `prog/etc/etc006/form.php:1022`), `T010` (1×, `prog/etc/etc006/form.php:1032`), `T218` (1×, `prog/etc/etc006/form.php:1042`), `T224` (1×, `prog/etc/etc006/form.php:1047`), `T005` (1×, `prog/etc/etc006/form.php:1052`), `T002` (1×, `prog/etc/etc006/form.php:1062`), `D049` (1×, `prog/etc/etc006/form.php:2000`), `D082` (1×, `prog/etc/etc006/form.php:2000`)
- **Classes e métodos chamados:** `B2B001` (526 linhas) (só incluída); `CAD001` (1497 linhas): `montarEmail` (`CAD001.php:12`); `CAD013` (27 linhas): `montarEmail` (`CAD013.php:11`); `EST004` (10716 linhas): `ImportarXML` (`EST004.php:414`); `EST006` (1804 linhas): `montarEmailPdf` (`EST006.php:491`); `EST008` (1979 linhas): `montarEmail` (`EST008.php:335`), `EST008montarEmailPdf` (`EST008.php:845`), `montarEmailCallback` (`EST008.php:1790`); `EST013` (139 linhas): `montarEmail` (`EST013.php:12`), `montarEmailCallback` (`EST013.php:103`); `EST015` (9926 linhas): `montarEmailLinkPolux` (`EST015.php:10`), `montarEmailLink` (`EST015.php:7768`), `montarEmail` (`EST015.php:172`); `EST016` (6846 linhas): `montarEmail` (`EST016.php:37`), `montarEmailSMT`; `ETC006` (231 linhas): `montarEmailAgenda` (`ETC006.php:210`), `personalizacaoDe` (`ETC006.php:223`), `processarWhitelist` (`ETC006.php:10`); `FIN001` (7368 linhas): `montarEmailReparcelamento` (`FIN001.php:3258`), `montarEmail` (`FIN001.php:6457`), `montarEmailPdf` (`FIN001.php:4729`), `montarEmailSimulacaoReparcelamento` (`FIN001.php:4416`); `FIN002` (3354 linhas): `montarEmailReparcelamentoPagar` (`FIN002.php:1126`); `FIN005` (1908 linhas): `montarEmail` (`FIN005.php:374`), `montarEmailPdf` (`FIN005.php:464`); `IMP003` (3710 linhas): `montarEmail` (`IMP003.php:82`); `VEN001` (15360 linhas): `montarEmail` (`VEN001.php:391`), `montarEmailPdf` (`VEN001.php:764`); `VEN002` (10343 linhas): `montarEmail` (`VEN002.php:34`), `montarEmailPdf` (`VEN002.php:199`); `VEN003` (19831 linhas): `montarEmailBoletos` (`VEN003.php:13476`), `montarEmailACP` (`VEN003.php:12175`), `montarEmail` (`VEN003.php:11963`), `montarEmailPdfCce` (`VEN003.php:13103`)
- **Tabelas gravadas pelos métodos chamados:** `D024` (EST004::ImportarXML), `I002` (EST013::montarEmailCallback), `T013` (EST004::ImportarXML), `T013A` (EST004::ImportarXML), `T014` (EST004::ImportarXML), `T014A` (EST004::ImportarXML), `T154` (EST004::ImportarXML), `T173` (EST008::montarEmailCallback), `T258` (ETC006::processarWhitelist)
- **Chaves C031 lidas (23):** `Boleto_Email`, `Boleto_Email_SMTP_Autenticacao`, `Boleto_Email_SMTP_Porta`, `Boleto_Email_SMTP_Senha`, `Boleto_Email_SMTP_Servidor`, `Boleto_Email_SMTP_Usuario`, `EmailEnviadosAddContatosEmpresa`, `NFe_Email`, `NFe_Email_SMTP_Autenticacao`, `NFe_Email_SMTP_Porta`, `NFe_Email_SMTP_Senha`, `NFe_Email_SMTP_Servidor` …
- **Mensagens e validações visíveis (12):**
  - "Deseja criar tarefa?" (`prog/etc/etc006/content.php:163`)
  - "É necessários preencher o campo "Para:"" (`prog/etc/etc006/form.php:715`)
  - "É necessários preencher o campo "Assunto:"" (`prog/etc/etc006/form.php:717`)
  - "É necessários preencher uma mensagem no texto do e-mail." (`prog/etc/etc006/form.php:719`)
  - "De: {$C007['C007_Email']} Para: {$r_emailPara} SMTP: {$C007['C007_Email_SMTP_Servidor']}" (`prog/etc/etc006/form.php:1134`)
  - "De: {$C007['C007_Email']} Para: {$r_emailPara} SMTP: {$C007['C007_Email_SMTP_Servidor']} SMTP Secure: {$mail->SMTPSecure} PORT: {$mail->Port} ERRO: {$cErro}" (`prog/etc/etc006/form.php:1181`)
  - "O e-mail foi adicionado ao contato e associado com a empresa." (`prog/etc/etc006/form.php:1743`)
  - "O e-mail foi adicionado ao contato e associado com a empresa!" (`prog/etc/etc006/form.php:1761`)
  - … mais 4
- **Abre as telas:** `ven/ven001`, `cad/cad001`, `etc/etc017`, `cad/cad002`, `ven/ven002`, `crm/crm001`
- **Grafo (Graphify):** 45 nós, 30 funções definidas: `.checkServerSettings()`, `.__construct()`, `.handleUpload()`, `.toBytes()`, `.getName()`, `.getSize()` …
- **Aberta por:** `cad/cad001`, `cad/cad002`, `cad/cad013`, `cad/cad061`, `crm/crm001`, `crm/crm002`, `crm/crm003`, `erp/erp001`, `erp/erp002`, `erp/erp003`, `est/est006`, `est/est008`, `est/est015`, `est/est016`, `etc/etc007` …

### `prog/etc/etc007` — Auditoria

- **Menu:** Outros › Auditoria, acesso C029 nº 272 "Auditoria" (`bibliotecas/menu.php:106`)
- **Arquivos:** 5 arquivos. 446 linhas. `grid.php` (183), `outro.php` (142), `tab.php` (47), `content.php` (39), `grid_func-callback.php` (35)
- **Abas (tab.php):** Registros (`tab.php:16`), Registros (`tab.php:36`)
- **Ações (`case` do roteador, 11):** `etc007ContentAuditoria`, `etc007ContentAuditoriaGeral`, `etc007GridAuditoria`, `etc007GridAuditoriaGeral`, `I`, `U`, `D`, `S`, `etc007OutroRegistro`, `etc007TabAuditoria`, `etc007TabAuditoriaGeral`
- **Tabelas só lidas na tela (5):** `T162` (3×, `prog/etc/etc007/grid.php:37` e mais 2), `C007` (3×, `prog/etc/etc007/grid.php:52` e mais 2), `D001` (1×, `prog/etc/etc007/grid.php:44`), `D049` (1×, `prog/etc/etc007/grid.php:45`), `D009` (1×, `prog/etc/etc007/grid.php:46`)
- **Abre as telas:** `etc/etc006`
- **Grafo (Graphify):** 7 nós, 2 funções definidas: `acaoTipos()`, `acoesRegistro()`
- **Aberta por:** `imp/imp003`, `imp/imp004`, `imp/imp005`, `sistema/funcoes`

### `prog/etc/etc008` — Geral (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 8 arquivos. 43.130 linhas. `ERP.sql` (21296), `NOTA.sql` (20903), `form.php` (658), `grid.php` (121), `tab.php` (53), `grid_func-callback.php` (47), `content.php` (43), `grid_func-js.php` (9)
- **Abas (tab.php):** Geral (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 7):** `etc008ContentBancoDadosClientes`, `etc008ContentFormBancoDadosClientesIncluir`, `etc008ContentFormCadastroDB`, `etc008FormBancoDadosClientes`, `etc008GridBancoDadosClientes`, `etc008TabBancoDadosClientes`, `etc008TabFormBancoDadosClientes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C009` (fotografia:584): INSERT em `prog/etc/etc008/NOTA.sql:488`, `prog/etc/etc008/NOTA.sql:10940`
  - `C010` (fotografia:607): INSERT em `prog/etc/etc008/NOTA.sql:517`
  - `C024` (fotografia:1012): INSERT em `prog/etc/etc008/form.php:264`
  - `C025` (fotografia:1030): INSERT/form em `prog/etc/etc008/form.php:30`, `prog/etc/etc008/form.php:252`
  - `D006` (fotografia:3314): INSERT em `prog/etc/etc008/NOTA.sql:1269`, `prog/etc/etc008/NOTA.sql:11720`
  - `D007` (fotografia:3510): UPDATE em `prog/etc/etc008/ERP.sql:16784`
  - `D009` (fotografia:3608): UPDATE em `prog/etc/etc008/ERP.sql:16662`, `prog/etc/etc008/ERP.sql:16712`
  - `D018` (fotografia:4161): INSERT em `prog/etc/etc008/ERP.sql:1417`, `prog/etc/etc008/ERP.sql:1418` e mais 27
  - `D020` (fotografia:4223): INSERT em `prog/etc/etc008/ERP.sql:1495`, `prog/etc/etc008/ERP.sql:1496` e mais 5590
  - `D024` (fotografia:4506): UPDATE em `prog/etc/etc008/ERP.sql:16647`, `prog/etc/etc008/ERP.sql:16648`
  - `D037` (fotografia:5142): INSERT em `prog/etc/etc008/NOTA.sql:2649`, `prog/etc/etc008/NOTA.sql:13100`
  - `D053` (fotografia:5634): INSERT em `prog/etc/etc008/NOTA.sql:3182`, `prog/etc/etc008/NOTA.sql:13633`
  - `D059` (fotografia:5794): INSERT em `prog/etc/etc008/NOTA.sql:3333`, `prog/etc/etc008/NOTA.sql:13784`
  - `D062` (fotografia:5962): INSERT em `prog/etc/etc008/NOTA.sql:3474`, `prog/etc/etc008/NOTA.sql:13925`
  - `D085` (fotografia:6827): INSERT em `prog/etc/etc008/ERP.sql:16686`, `prog/etc/etc008/ERP.sql:16699` e mais 3
  - `T002` (fotografia:14415): UPDATE em `prog/etc/etc008/ERP.sql:16807`, `prog/etc/etc008/ERP.sql:19183` e mais 3
  - `T003` (fotografia:14592): UPDATE em `prog/etc/etc008/ERP.sql:19317`, `prog/etc/etc008/ERP.sql:19460`
  - `T004` (fotografia:14902): UPDATE em `prog/etc/etc008/ERP.sql:19569`, `prog/etc/etc008/ERP.sql:19603`
  - `T005` (fotografia:15218): UPDATE em `prog/etc/etc008/ERP.sql:19692`, `prog/etc/etc008/ERP.sql:19775` e mais 1
  - `T006` (fotografia:15620): UPDATE em `prog/etc/etc008/ERP.sql:19845`, `prog/etc/etc008/ERP.sql:19895` e mais 3
  - `T007` (fotografia:16254): UPDATE em `prog/etc/etc008/ERP.sql:20044`
  - `T008` (fotografia:16670): UPDATE em `prog/etc/etc008/ERP.sql:20187`, `prog/etc/etc008/ERP.sql:20211`
  - `T010` (fotografia:17049): UPDATE em `prog/etc/etc008/ERP.sql:20260`
  - `T011` (fotografia:17178): UPDATE em `prog/etc/etc008/ERP.sql:20274`
  - `T012` (fotografia:17251): UPDATE em `prog/etc/etc008/ERP.sql:20289`
  - `T013` (fotografia:17309): UPDATE em `prog/etc/etc008/ERP.sql:20309`
  - `T014` (fotografia:17524): UPDATE em `prog/etc/etc008/ERP.sql:20529`, `prog/etc/etc008/ERP.sql:20549` e mais 1
  - `T015` (fotografia:17763): UPDATE em `prog/etc/etc008/ERP.sql:16833`, `prog/etc/etc008/ERP.sql:20789` e mais 3
  - `T054` (fotografia:19212): UPDATE em `prog/etc/etc008/ERP.sql:21001`
  - `T055` (fotografia:19318): UPDATE em `prog/etc/etc008/ERP.sql:21177`
  - `T056` (fotografia:19493): UPDATE em `prog/etc/etc008/ERP.sql:21246`
  - `T059` (fotografia:19613): UPDATE em `prog/etc/etc008/ERP.sql:21261`
  - `T066` (fotografia:19874): INSERT/UPDATE em `prog/etc/etc008/ERP.sql:16619`, `prog/etc/etc008/ERP.sql:16673` e mais 3
  - `T128` (fotografia:22089): UPDATE em `prog/etc/etc008/ERP.sql:21273`
  - `T129` (fotografia:22143): UPDATE em `prog/etc/etc008/ERP.sql:21288`
  - `T185` (fotografia:23685): DELETE em `prog/etc/etc008/ERP.sql:16726`
- **Tabelas só lidas na tela (37):** `D049` (26×, `prog/etc/etc008/ERP.sql:16954` e mais 25), `D001` (14×, `prog/etc/etc008/ERP.sql:17027` e mais 13), `C004` (12×, `prog/etc/etc008/ERP.sql:17000` e mais 11), `D005` (7×, `prog/etc/etc008/ERP.sql:17015` e mais 6), `T019` (7×, `prog/etc/etc008/ERP.sql:18980` e mais 6), `T020` (6×, `prog/etc/etc008/ERP.sql:16799` e mais 5), `T033` (6×, `prog/etc/etc008/ERP.sql:16825` e mais 5), `D019` (5×, `prog/etc/etc008/ERP.sql:17582` e mais 4), `T025` (4×, `prog/etc/etc008/ERP.sql:17088` e mais 3), `D041` (4×, `prog/etc/etc008/ERP.sql:17536` e mais 3), `C007` (3×, `prog/etc/etc008/ERP.sql:18449` e mais 2), `D090` (3×, `prog/etc/etc008/ERP.sql:18872` e mais 2), `T117` (3×, `prog/etc/etc008/grid.php:28` e mais 2), `D002` (2×, `prog/etc/etc008/ERP.sql:16973` e mais 1), `T001` (2×, `prog/etc/etc008/ERP.sql:17183` e mais 1), `T046` (2×, `prog/etc/etc008/ERP.sql:17527` e mais 1), `T016` (2×, `prog/etc/etc008/ERP.sql:17528` e mais 1), `D016` (2×, `prog/etc/etc008/ERP.sql:18098` e mais 1), `C026` (2×, `prog/etc/etc008/form.php:235` e mais 1), `D068` (1×, `prog/etc/etc008/ERP.sql:17251`), `D067` (1×, `prog/etc/etc008/ERP.sql:17252`), `D004` (1×, `prog/etc/etc008/ERP.sql:17687`), `D042` (1×, `prog/etc/etc008/ERP.sql:17890`), `T053` (1×, `prog/etc/etc008/ERP.sql:17921`), `T052` (1×, `prog/etc/etc008/ERP.sql:17922`) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D009_D005_Id` (`prog/etc/etc008/ERP.sql:17340`), `D009_Quantidade_Pedido` (`prog/etc/etc008/ERP.sql:16633` e mais 5), `D009_Quantidade_Pendencia` (`prog/etc/etc008/ERP.sql:16633` e mais 2), `D016_Dolar` (`prog/etc/etc008/ERP.sql:17116`), `T003_IPV_Medio` (`prog/etc/etc008/ERP.sql:19353` e mais 7), `T005_IPV_Medio` (`prog/etc/etc008/ERP.sql:19713` e mais 3), `T007_IPV_Medio` (`prog/etc/etc008/ERP.sql:20058` e mais 1)
- **Mensagens e validações visíveis (1):**
  - "Nova empresa criada com sucesso!" (`prog/etc/etc008/form.php:126`)
- **Grafo (Graphify):** 646 nós, 6 funções definidas: `validaCampoAcessoLocal()`, `validaCampoAcessoRemoto()`, `validaCampoAts()`, `acessar1()`, `acessarr()`, `criarNovaEmpresa()`

### `prog/etc/etc009` — Geral (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 6 arquivos. 414 linhas. `form.php` (149), `grid.php` (111), `tab.php` (53), `grid_func-callback.php` (47), `content.php` (45), `grid_func-js.php` (9)
- **Abas (tab.php):** Geral (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `etc009ContentContadoresClientes`, `etc009ContentFormCotadorClientesIncluir`, `etc009FormContadoresClientes`, `etc009GridContadoresClientes`, `etc009TabContadoresClientes`, `etc009TabFormContadoresClientes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C026` (fotografia:1057): form em `prog/etc/etc009/form.php:28`
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/etc/etc009/form.php:90`)
- **Grafo (Graphify):** 11 nós, 5 funções definidas: `validaCampoAcessoLocal()`, `validaCampoAcessoRemoto()`, `validaCampoAts()`, `acessar1()`, `acessarr()`

### `prog/etc/etc010` — Geral (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 369 linhas. `form.php` (152), `grid.php` (119), `tab.php` (53), `content.php` (45)
- **Abas (tab.php):** Geral (`tab.php:18`), Geral (`tab.php:39`)
- **Ações (`case` do roteador, 6):** `etc010ContentUsuariosClientes`, `etc010ContentFormUsuariosClientesIncluir`, `etc010FormUsuariosClientes`, `etc010GridUsuariosClientes`, `etc010TabUsuariosClientes`, `etc010TabFormUsuariosClientes`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C025` (fotografia:1030): form em `prog/etc/etc010/form.php:28`
- **Tabelas só lidas na tela (2):** `C026` (1×, `prog/etc/etc010/grid.php:40`), `D024` (1×, `prog/etc/etc010/grid.php:41`)
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/etc/etc010/form.php:93`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/etc/etc011` — BI Dashboards

- **Menu:** Gerencial › BI Dashboards, acesso C029 nº 152 "B.I. - Business Intelligence" (`bibliotecas/menu.php:100`)
- **Menu:** Gerencial › BI Hardness, acesso C029 nº 152 "B.I. - Business Intelligence" (`bibliotecas/menu.php:139`)
- **Menu:** Gerencial › BI - Relatórios, acesso C029 nº 152 "B.I. - Business Intelligence" (item comentado no Menu) (`bibliotecas/menu.php:217`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 35 "Contas a Receber" (`prog/etc/etc011/formDash.php:303` e mais 1); 36 "Contas a Pagar" (`prog/etc/etc011/formDash.php:303` e mais 1); 37 "Caixa e Bancos" (`prog/etc/etc011/formDash.php:303` e mais 1); 152 "B.I. - Business Intelligence" (`prog/etc/etc011/formDash.php:204`); 203 "PCP" (`prog/etc/etc011/outros.php:411`); 304 "Acessar painel de integrações" (`prog/etc/etc011/outros.php:400`); 306 "Acessar conexões MCP" (`prog/etc/etc011/outros.php:401`); 307 "Administrar Webhooks" (`prog/etc/etc011/outros.php:402`); 308 "Acessar Apontamento PCP" (`prog/etc/etc011/outros.php:411`); 310 "Acessar GNRE" (`prog/etc/etc011/outros.php:405`)
- **Arquivos:** 8 arquivos. 6.672 linhas. `grid.php` (3433), `outros.php` (1521), `formDash.php` (688), `form.php` (359), `grid_func-ajax.php` (189), `tab.php` (186), `content.php` (156), `ajax.php` (140)
- **Abas (tab.php):** Todos (`tab.php:15`), Sql (`tab.php:17`), Sql salvos (`tab.php:18`), Relatório (`tab.php:102`), Relatórios (`tab.php:117`), Filtro (`tab.php:160`), Indicador (`tab.php:175`)
- **Ações (`case` do roteador, 37):** `adicionarItemDashboard`, `limparFiltroSalvo`, `visualizarIndicador`, `etc011ContentPivot`, `etc011ContentCockpit`, `etc011ContentSalvar`, `etc011ContentPivotRelatorio`, `etc011ContentGrafico`, `etc011ContentNovaVisao`, `etc011FiltroGlobal`, `etc011IncluirIndicador`, `etc011FormSalvar`, `etc011IncluirIndicador`, `etc011FormDashVendas`, `etc011FormDashOrcamento`, `etc011FormDashPainel`, `etc011FormDashFinanceiro`, `etc011GridPivot`, `A`, `B` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C007` (fotografia:396): UPDATE em `prog/etc/etc011/formDash.php:136`, `prog/etc/etc011/outros.php:595`
  - `D132` (fotografia:7694): form em `prog/etc/etc011/form.php:33`, `prog/etc/etc011/form.php:214`
  - `D182` (fotografia:9473): INSERT/UPDATE em `prog/etc/etc011/ajax.php:132`, `prog/etc/etc011/form.php:125` e mais 4
  - `T265` (fotografia:26916): INSERT/UPDATE em `prog/etc/etc011/grid_func-ajax.php:57`, `prog/etc/etc011/grid_func-ajax.php:61`
- **Tabelas só lidas na tela (26):** `FAC001` (16×, `prog/etc/etc011/grid.php:1449` e mais 15), `D024` (10×, `prog/etc/etc011/formDash.php:315` e mais 9), `DIM004` (4×, `prog/etc/etc011/ajax.php:94` e mais 2), `C032` (4×, `prog/etc/etc011/formDash.php:282` e mais 3), `T117` (4×, `prog/etc/etc011/formDash.php:329` e mais 3), `T002` (4×, `prog/etc/etc011/formDash.php:331` e mais 3), `D007` (4×, `prog/etc/etc011/formDash.php:332` e mais 3), `T135` (4×, `prog/etc/etc011/formDash.php:537` e mais 3), `DIM002` (2×, `prog/etc/etc011/ajax.php:92` e mais 1), `DIM003` (2×, `prog/etc/etc011/ajax.php:93` e mais 1), `DIM005` (2×, `prog/etc/etc011/ajax.php:96` e mais 1), `DIM006` (2×, `prog/etc/etc011/ajax.php:97` e mais 1), `DIM007` (2×, `prog/etc/etc011/ajax.php:98` e mais 1), `DIM008` (2×, `prog/etc/etc011/ajax.php:99` e mais 1), `DIM009` (2×, `prog/etc/etc011/ajax.php:100` e mais 1), `DIM010` (2×, `prog/etc/etc011/ajax.php:101` e mais 1), `DIM011` (2×, `prog/etc/etc011/ajax.php:102` e mais 1), `DIM012` (2×, `prog/etc/etc011/ajax.php:103` e mais 1), `DIM013` (2×, `prog/etc/etc011/ajax.php:104` e mais 1), `C004` (2×, `prog/etc/etc011/formDash.php:320` e mais 1), `C036` (2×, `prog/etc/etc011/formDash.php:645` e mais 1), `C001` (2×, `prog/etc/etc011/outros.php:703` e mais 1), `DIM001` (1×, `prog/etc/etc011/grid.php:8`), `D177` (1×, `prog/etc/etc011/tab.php:52`), `D176` (1×, `prog/etc/etc011/tab.php:53`) …
- **Mensagens e validações visíveis (8):**
  - "O relatório/gráfico foi salvo." (`prog/etc/etc011/form.php:138`)
  - "Erro" (`prog/etc/etc011/form.php:276`)
  - "Registro Alterado com Sucesso!" (`prog/etc/etc011/form.php:284`)
  - "Registro Incluido com Sucesso!" (`prog/etc/etc011/form.php:296`)
  - "Deseja remover o filtro salvo para este relatório, retornando ao modelo padrão?" (`prog/etc/etc011/grid.php:1241`)
  - "Não existe uma personalização ativa para a empresa atual." (`prog/etc/etc011/outros.php:9`)
  - "Seu usuário ainda não possui indicadores liberados neste dashboard." (`prog/etc/etc011/outros.php:13`)
  - "Não foi possível autorizar a nova janela. Atualize a página e tente novamente." (`prog/etc/etc011/outros.php:18`)
- **Abre as telas:** `etc/etc015`
- **Grafo (Graphify):** 31 nós, 23 funções definidas: `{closure#1}()`, `{closure#2}()`, `{closure#3}()`, `{closure#4}()`, `{closure#5}()`, `formataDiaMes()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `sistema/personalizacaoLowCode`

### `prog/etc/etc012` — Resumo Diário (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 411 linhas. `grid.php` (143), `outro.php` (122), `graph.php` (98), `tab.php` (27), `content.php` (21)
- **Abas (tab.php):** Resumo Diário (`tab.php:14`)
- **Ações (`case` do roteador, 6):** `etc012ContentDashboard`, `etc012GraphFluxoCaixa`, `etc012Vendas`, `etc012Financeiro`, `etc012OutroDashboard`, `etc012TabDashboard`
- **Tabelas só lidas na tela (7):** `T019` (2×, `prog/etc/etc012/grid.php:89` e mais 1), `T002` (1×, `prog/etc/etc012/graph.php:23`), `T015` (1×, `prog/etc/etc012/graph.php:32`), `C007` (1×, `prog/etc/etc012/grid.php:11`), `T003` (1×, `prog/etc/etc012/grid.php:17`), `T005` (1×, `prog/etc/etc012/grid.php:24`), `T007` (1×, `prog/etc/etc012/grid.php:31`)
- **Grafo (Graphify):** 5 nós, 0 funções definidas

### `prog/etc/etc013` — Controle Interno

- **Menu:** Outros › Controle Interno, acesso C029 nº 93 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:237`)
- **Arquivos:** 5 arquivos. 1.064 linhas. `grid.php` (354), `form.php` (330), `grid_func-callback.php` (241), `tab.php` (75), `content.php` (64)
- **Abas (tab.php):** Análises (`tab.php:18`), OS (`tab.php:19`), Geral (`tab.php:40`), Geral (`tab.php:61`)
- **Ações (`case` do roteador, 10):** `etc013ContentControleInterno`, `etc013FormControleInternoIncluir`, `etc013FormControleInternoIncluirOS`, `etc013FormControleInterno`, `etc013FormControleInternoOS`, `etc013GridAnalises`, `etc013GridOs`, `etc013TabControleInterno`, `etc013TabFormControleInternoIncluir`, `etc013TabFormControleInternoIncluirOS`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `I001` (fotografia:11262): form em `prog/etc/etc013/form.php:28`
  - `I002` (fotografia:11293): form em `prog/etc/etc013/form.php:192`
- **Tabelas só lidas na tela (1):** `C007` (3×, `prog/etc/etc013/grid.php:62` e mais 2)
- **Mensagens e validações visíveis (1):**
  - "Registro Incluido com Sucesso!" (`prog/etc/etc013/form.php:113`)
- **Abre as telas:** `etc/etc006`
- **Grafo (Graphify):** 22 nós, 17 funções definidas: `gridLinha()`, `dateDiff()`, `mostrarNome()`, `temEmail()`, `trimTitulo()`, `validaCampoDias()` …. Chamada por: `.montarEmail()` (bibliotecas/classes/EST013.php)

### `prog/etc/etc014` — Relatórios Salvos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 2.247 linhas. `grid.php` (1951), `form.php` (169), `tab.php` (68), `content.php` (59)
- **Abas (tab.php):** Relatórios Salvos (`tab.php:14`), Visualização (`tab.php:34`), Criação de Relatórios (`tab.php:35`), Relatório (`tab.php:55`)
- **Ações (`case` do roteador, 12):** `etc014ContentPivot`, `etc014ContentSalvar`, `etc014ContentPivotRelatorio`, `etc014FormSalvar`, `etc014GridPivot`, `A`, `B`, `C`, `etc014GridSave`, `etc014TabListar`, `etc010TabPivot`, `etc014TabSalvar`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D132` (fotografia:7694): form em `prog/etc/etc014/form.php:30`
- **Tabelas só lidas na tela (12):** `FAC001` (9×, `prog/etc/etc014/grid.php:861` e mais 8), `DIM004` (2×, `prog/etc/etc014/grid.php:9` e mais 1), `DIM001` (1×, `prog/etc/etc014/grid.php:6`), `DIM002` (1×, `prog/etc/etc014/grid.php:7`), `DIM003` (1×, `prog/etc/etc014/grid.php:8`), `DIM005` (1×, `prog/etc/etc014/grid.php:11`), `DIM006` (1×, `prog/etc/etc014/grid.php:12`), `DIM007` (1×, `prog/etc/etc014/grid.php:13`), `DIM008` (1×, `prog/etc/etc014/grid.php:14`), `DIM009` (1×, `prog/etc/etc014/grid.php:15`), `DIM010` (1×, `prog/etc/etc014/grid.php:16`), `DIM011` (1×, `prog/etc/etc014/grid.php:17`)
- **Grafo (Graphify):** 5 nós, 1 funções definidas: `{closure#1}()`

### `prog/etc/etc015` — Relatórios Salvos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 554 linhas. `outro.php` (362), `grid.php` (77), `content.php` (44), `tab.php` (40), `ajax.php` (31)
- **Abas (tab.php):** Relatórios Salvos (`tab.php:10`), Criar Relatórios (`tab.php:11`), Relatório (`tab.php:27`)
- **Ações (`case` do roteador, 10):** `verificarNome`, `salvarGrid`, `etc015Reports`, `etc015CriarRelatorio`, `etc015VerRelatorio`, `etc015Relatorios`, `etc015CriarViaSql`, `etc015GerarGrid`, `etc015Reports`, `etc015VerRelatorio`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D140` (fotografia:7937): INSERT em `prog/etc/etc015/ajax.php:25`
- **Tabelas só lidas na tela (2):** `T001` (1×, `prog/etc/etc015/outro.php:84`), `T005` (1×, `prog/etc/etc015/outro.php:152`)
- **Grafo (Graphify):** 5 nós, 0 funções definidas
- **Aberta por:** `etc/etc011`

### `prog/etc/etc016` — Conferência Cega

- **Menu:** Estoque › Conferência Cega, acesso C029 nº 20 "Expedição Mercadorias" (`bibliotecas/menu.php:69`)
- **Arquivos:** 3 arquivos. 652 linhas. `outros.php` (362), `ajax.php` (267), `content.php` (23)
- **Ações (`case` do roteador, 6):** `etc016ProdutoNovo`, `etc016ProdutoNovoPDV`, `etc016Verificar`, `etc016SqlCodVar`, `etc016VerificarPorCodbarra`, `etc016VerificarPorCodbarra`
- **Tabelas só lidas na tela (6):** `D001` (3×, `prog/etc/etc016/ajax.php:125` e mais 2), `D083` (1×, `prog/etc/etc016/ajax.php:128`), `T014` (1×, `prog/etc/etc016/outros.php:345`), `D009` (1×, `prog/etc/etc016/outros.php:346`), `D049` (1×, `prog/etc/etc016/outros.php:347`), `D037` (1×, `prog/etc/etc016/outros.php:349`)
- **Grafo (Graphify):** 5 nós, 2 funções definidas: `reproduzirSomErro()`, `verCodBarraAjustesUI()`
- **Aberta por:** `ven/ven001`

### `prog/etc/etc017` — Atividades (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 6 arquivos. 2.058 linhas. `grid.php` (813), `form.php` (664), `content.php` (267), `grid_func-ajax.php` (172), `tab.php` (84), `outro.php` (58)
- **Abas (tab.php):** Atividades (`tab.php:12`), Lixeira (`tab.php:13`), Atividades (`tab.php:29`), Lixeira (`tab.php:30`), Atividades (`tab.php:57`), Registro de contato (`tab.php:73`)
- **Ações (`case` do roteador, 30):** `etc017ContentTarefas`, `etc017ContentTarefasCliente`, `etc017ContentTarefa`, `etc017ContentAtividades`, `etc017ContentAtividadesT003`, `etc017ContentAtividadesT005`, `etc017ContentAtividadesT007`, `etc017ContentAtividadesT182`, `etc017ContentOrcamentoAgenda`, `etc017ContentOrcamento`, `etc017FormInserirAgendaTarefa`, `etc017FormInserirAgendaTarefaT003`, `etc017FormInserirAgendaTarefaT007`, `etc017FormInserirAgendaTarefaT005`, `etc017FormInserirAgendaTarefaT182`, `etc017GridTarefas`, `etc017GridTarefasCliente`, `etc017GridLixeira`, `etc017GridTarefa`, `processarTarefas` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D085` (fotografia:6827): INSERT/UPDATE/form em `prog/etc/etc017/content.php:46`, `prog/etc/etc017/form.php:33` e mais 17
  - `T259` (fotografia:26718): INSERT/UPDATE em `prog/etc/etc017/grid_func-ajax.php:40`, `prog/etc/etc017/grid_func-ajax.php:49` e mais 5
  - `T260` (fotografia:26743): DELETE/INSERT/UPDATE em `prog/etc/etc017/grid.php:529`, `prog/etc/etc017/grid.php:531` e mais 1
- **Tabelas só lidas na tela (21):** `C007` (7×, `prog/etc/etc017/grid.php:34` e mais 6), `T139` (7×, `prog/etc/etc017/grid.php:165` e mais 6), `T219` (5×, `prog/etc/etc017/grid.php:651` e mais 4), `D024` (4×, `prog/etc/etc017/grid.php:37` e mais 3), `T005` (4×, `prog/etc/etc017/grid.php:598` e mais 3), `T006` (4×, `prog/etc/etc017/grid.php:599` e mais 3), `T007` (4×, `prog/etc/etc017/grid.php:601` e mais 3), `T218` (4×, `prog/etc/etc017/grid.php:651` e mais 3), `T003` (3×, `prog/etc/etc017/grid.php:595` e mais 2), `T225` (3×, `prog/etc/etc017/grid.php:653` e mais 2), `T224` (3×, `prog/etc/etc017/grid.php:663` e mais 2), `T004` (2×, `prog/etc/etc017/grid.php:596` e mais 1), `D013` (1×, `prog/etc/etc017/grid.php:537`), `T008` (1×, `prog/etc/etc017/grid.php:602`), `T014` (1×, `prog/etc/etc017/grid.php:653`), `T013` (1×, `prog/etc/etc017/grid.php:653`), `T256` (1×, `prog/etc/etc017/grid.php:655`), `D009` (1×, `prog/etc/etc017/grid.php:656`), `D049` (1×, `prog/etc/etc017/grid.php:656`), `D001` (1×, `prog/etc/etc017/grid.php:656`), `T255` (1×, `prog/etc/etc017/grid.php:665`)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T003_Status_Orcamento` (`prog/etc/etc017/grid.php:595`), `T005_Status_Pedido` (`prog/etc/etc017/grid.php:598`)
- **Mensagens e validações visíveis (1):**
  - "Deseja processar as tarefas sem vínculo?" (`prog/etc/etc017/grid.php:95`)
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::retornoAgenda` (`NOTIFICACOES.php:1221`, clique na linha 1297; lê `C004`, `D024`, `D085`, `D085A`, `T003`, `T182`)
- **Abre as telas:** `crm/crm001`, `etc/etc006`, `ven/ven001`
- **Grafo (Graphify):** 9 nós, 3 funções definidas: `gridLinha()`, `resizeGridTarefa()`, `resizeEditorChamado()`
- **Aberta por:** `crm/crm001`, `est/est011`, `etc/etc006`, `ven/ven001`, `ven/ven002`, `ven/ven003`

### `prog/etc/etc018` — Crontab (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 7 arquivos. 726 linhas. `grid.php` (264), `form.php` (147), `content.php` (100), `tab.php` (87), `grid_func-ajax.php` (86), `grid_func-callback.php` (24), `outro.php` (18)
- **Abas (tab.php):** Crontab (`tab.php:12`), Processos Automáticos (`tab.php:28`), Editar Crontab (`tab.php:44`), Log (`tab.php:60`), Crontab (`tab.php:76`)
- **Ações (`case` do roteador, 17):** `etc018ContentCrontab`, `etc018ContentCrontabUsuarios`, `etc018ContentEditarCrontab`, `etc018ContentLog`, `etc018ContentPersonalizarCrontab`, `etc018FormEditarCrontab`, `etc018GridCrontab`, `etc018ContentPersonalizarCrontab`, `etc018GridCrontabUsuarios`, `personalizarCrontab`, `executarCrontab`, `etc018OutroLog`, `etc018TabCrontab`, `etc018TabCrontabUsuarios`, `etc018TabEditarCrontab`, `etc018TabLog`, `etc018ContentPersonalizarCrontab`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C039` (fotografia:2484): INSERT em `prog/etc/etc018/grid_func-ajax.php:30`
  - `D183` (fotografia:9494): UPDATE em `prog/etc/etc018/grid_func-ajax.php:15`
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/etc/etc018/form.php:84`)
  - "Registro Incluido com Sucesso!" (`prog/etc/etc018/form.php:104`)
- **Grafo (Graphify):** 10 nós, 3 funções definidas: `etc018NomeApp()`, `etc018Opcoes()`, `gridLinha()`

### `prog/etc/etc019`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 545 linhas. `outro.php` (545)
- **Ações (`case` do roteador, 5):** `etc019OutroAjuda`, `etc019OutroRegistro`, `etc019OutroCabecalhoSalvar`, `etc019OutroTopicoSalvar`, `etc019OutroTopicoExcluir`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `W001` (fotografia:28949): INSERT/UPDATE em `prog/etc/etc019/outro.php:43`, `prog/etc/etc019/outro.php:444` e mais 1
- **Grafo (Graphify):** 6 nós, 5 funções definidas: `buscaTopicosRecursive()`, `wikiAbrirRegistro()`, `wikiEditarCabecalho()`, `wikiEditarRegistro()`, `wikiExcluirRegistro()`

### `prog/etc/etc020` — E-mail (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 3 arquivos. 334 linhas. `form.php` (289), `tab.php` (23), `content.php` (22)
- **Abas (tab.php):** E-mail (`tab.php:12`)
- **Ações (`case` do roteador, 3):** `etc020ContentEnviarEmail`, `etc020FormEnviarEmail`, `etc020TabEnviarEmail`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C041` — E-mails enviados (fotografia:2539): INSERT em `prog/etc/etc020/form.php:252`
  - `C042` — E-mails enviados - lista de anexos (fotografia:2563): INSERT em `prog/etc/etc020/form.php:256`
- **Mensagens e validações visíveis (6):**
  - "É necessários preencher o campo "De:"" (`prog/etc/etc020/form.php:171`)
  - "É necessários preencher o campo "Para:"" (`prog/etc/etc020/form.php:173`)
  - "É necessários preencher o campo "Assunto:"" (`prog/etc/etc020/form.php:175`)
  - "É necessários preencher uma mensagem no texto do e-mail." (`prog/etc/etc020/form.php:177`)
  - "De: {$r_emailDe} Para: {$r_emailPara} SMTP: {$options['smtp']['host']}" (`prog/etc/etc020/form.php:261`)
  - "De: {$r_emailDe} Para: {$r_emailPara} SMTP: {$options['smtp']['host']} SMTP Secure: {$mail->SMTPSecure} PORT: {$mail->Port} ERRO: {$cErro}" (`prog/etc/etc020/form.php:284`)
- **Grafo (Graphify):** 3 nós, 0 funções definidas

### `prog/etc/etc021`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 1.016 linhas. `outros.php` (435), `ajax.php` (276), `grid-func-ajax.php` (143), `grid_func-ajax.php` (143), `content.php` (19)
- **Ações (`case` do roteador, 9):** `etc021ProdutoNovo`, `etc021Verificar`, `etc021SqlCodVar`, `etc021VerificarPorCodbarra`, `conferenciaCegaFim`, `CalcConfCegaFim`, `conferenciaCegaFim`, `CalcConfCegaFim`, `etc021VerificarPorCodbarra`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T014A` (fotografia:17682): UPDATE em `prog/etc/etc021/ajax.php:118`, `prog/etc/etc021/ajax.php:127`
- **Tabelas só lidas na tela (3):** `T014` (4×, `prog/etc/etc021/grid-func-ajax.php:27` e mais 3), `D001` (3×, `prog/etc/etc021/ajax.php:26` e mais 2), `D083` (2×, `prog/etc/etc021/ajax.php:26` e mais 1)
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `cad002FotoProduto` (`CAD002.php:12012`)
- **Grafo (Graphify):** 7 nós, 2 funções definidas: `reproduzirSomErro()`, `verCodBarraAjustesUI()`

### `prog/etc/etc022`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 3 arquivos. 599 linhas. `outros.php` (398), `ajax.php` (181), `content.php` (20)
- **Ações (`case` do roteador, 4):** `etc022CodigoOrigem`, `etc022InsertMovimentacao`, `etc022ContentPrincipal`, `codigoBarra`
- **Tabelas só lidas na tela (11):** `D004` (2×, `prog/etc/etc022/ajax.php:20` e mais 1), `D001` (1×, `prog/etc/etc022/ajax.php:41`), `D049` (1×, `prog/etc/etc022/ajax.php:42`), `D009` (1×, `prog/etc/etc022/ajax.php:43`), `T066` (1×, `prog/etc/etc022/ajax.php:44`), `D082` (1×, `prog/etc/etc022/ajax.php:46`), `D083` (1×, `prog/etc/etc022/ajax.php:47`), `D024` (1×, `prog/etc/etc022/ajax.php:48`), `T013` (1×, `prog/etc/etc022/ajax.php:49`), `T014` (1×, `prog/etc/etc022/ajax.php:50`), `C007` (1×, `prog/etc/etc022/ajax.php:120`)
- **Classes e métodos chamados:** `CAD002` (14075 linhas): `cad002FotoProduto` (`CAD002.php:12012`); `EST001` (797 linhas): `movimentaLocacao`
- **Grafo (Graphify):** 10 nós, 7 funções definidas: `agregaQuantidades()`, `atualizaQtdTotal()`, `ErrorMessage()`, `itemColor()`, `listaProd()`, `recalculateTotalInserted()` …

