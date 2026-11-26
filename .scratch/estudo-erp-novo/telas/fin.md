# Fichas de tela: `prog/fin` — Financeiro e crédito

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `fin/fin001` | Contas a Receber | 35, 157, 35, 178 | 10 | 13293 | 10 | 25 |
| `fin/fin002` | Contas a Pagar | 36 | 10 | 6294 | 6 | 19 |
| `fin/fin003` | Caixa e Bancos | 37 | 9 | 2988 | 6 | 14 |
| `fin/fin005` | Boleto Bancário | 38* | 9 | 2090 | 2 | 9 |
| `fin/fin006` | Comissões de Venda (rótulo da primeira aba) |  | 5 | 606 | 1 | 1 |
| `fin/fin007` | Análise de Crédito | 39, 39 | 8 | 1647 | 3 | 8 |
| `fin/fin008` | Comissões | 163*, 164*, 149*, 198*, 162* | 4 | 1081 | 0 | 9 |
| `fin/fin009` | Projeção Orçamentária | 150, 156* | 7 | 1361 | 2 | 9 |

Total: 8 telas. 29.360 linhas. 24 tabelas gravadas. 49 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/fin/fin001` — Contas a Receber

- **Menu:** Financeiro › Contas a Receber, acesso C029 nº 35 "Contas a Receber" (`bibliotecas/menu.php:70`)
- **Menu:** Financeiro › Cobranças, acesso C029 nº 157 "Cobranças" (`bibliotecas/menu.php:76`)
- **Menu:** Financeiro › Créditos Troca, acesso C029 nº 35 "Contas a Receber" (`bibliotecas/menu.php:77`)
- **Menu:** Financeiro › Serasa - Relato, acesso C029 nº 178 "Serasa - Relato" (`bibliotecas/menu.php:78`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 35 "Contas a Receber" (`prog/fin/fin001/content.php:701` e mais 9); 38 "Boleto Bancário" (`prog/fin/fin001/tab.php:53`); 60 "Pagar - Portadores" (`prog/fin/fin001/tab.php:520`); 62 "Receber - Portadores" (`prog/fin/fin001/tab.php:517`); 111 "Excluir títulos e lançamentos" (`prog/fin/fin001/grid_func-callback.php:12`); 168 "Bancos" (`prog/fin/fin001/tab.php:523` e mais 1); 192 "Alterar valor comissão de contas a receber" (`prog/fin/fin001/form.php:67`); 239 "Desabilitar \"Processar saldo\" na aba Créditos" (`prog/fin/fin001/grid.php:4165` e mais 1); 267 "Desabilitar alteração dos valores no lançamento de baixa de titulos" (`prog/fin/fin001/form.php:1076` e mais 2)
- **Arquivos:** 10 arquivos. 13.293 linhas. `grid.php` (5734), `form.php` (3453), `content.php` (1086), `grid_func-ajax.php` (779), `tab.php` (593), `outro.php` (494), `grid_func-callback.php` (481), `grid_func-js.php` (321), `form_func-ajax.php` (233), `form_func-js.php` (119)
- **Abas (tab.php):** Títulos a Receber (`tab.php:17`), Boleto Bancário/Outros (`tab.php:18`), Reparcelamentos/Agrupamentos (`tab.php:19`), Acréscimos/Descontos (`tab.php:20`), Cobranças (`tab.php:21`), Remessas (`tab.php:22`), Retornos (`tab.php:23`), Cheques (`tab.php:24`), Importar CSV (`tab.php:25`), Boleto Bancário (`tab.php:33`), Boleto Bancário (`tab.php:54`), Outros Portadores (`tab.php:57`) …
- **Ações (`case` do roteador, 182):** `fin001content01`, `fin001content02`, `fin001content03`, `fin001content04`, `fin001content05`, `fin001content052rows`, `fin001content06`, `fin001ContentReparcelar`, `fin001ContentPagamento`, `fin001ContentReparcelamento`, `fin001ContentDetalhesCheque`, `fin001content07`, `fin001ContentRetornoRemessas`, `fin001contentRelatorioRetorno`, `fin001ContentCobrancas`, `fin001ContentAlterarVencimento`, `fin001ContentCarta`, `fin001ContentDuplicarTitulos`, `fin001ContentCancelamento`, `fin001contentAgrupar` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D007` (fotografia:3510): form em `prog/fin/fin001/form.php:765`
  - `T002` (fotografia:14415): UPDATE/form em `prog/fin/fin001/form.php:41`, `prog/fin/fin001/form.php:303` e mais 11
  - `T002A` (fotografia:14567): UPDATE em `prog/fin/fin001/grid_func-ajax.php:689`
  - `T020` (fotografia:18068): INSERT/form em `prog/fin/fin001/form.php:317`, `prog/fin/fin001/form.php:338` e mais 2
  - `T038` (fotografia:18636): UPDATE em `prog/fin/fin001/grid_func-ajax.php:682`
  - `T103` (fotografia:21328): UPDATE em `prog/fin/fin001/grid_func-ajax.php:276`, `prog/fin/fin001/grid_func-ajax.php:287` e mais 4
  - `T166` (fotografia:23144): form em `prog/fin/fin001/form.php:1451`
  - `T180` (fotografia:23510): DELETE/form em `prog/fin/fin001/form.php:1867`, `prog/fin/fin001/grid_func-ajax.php:370`
  - `T242` (fotografia:26254): form em `prog/fin/fin001/form.php:2925`
  - `T246` (fotografia:26344): form em `prog/fin/fin001/form.php:3058`
- **Tabelas só lidas na tela (25):** `D024` (39×, `prog/fin/fin001/form.php:1618` e mais 38), `D027` (32×, `prog/fin/fin001/form.php:1619` e mais 31), `C007` (26×, `prog/fin/fin001/form.php:1078` e mais 25), `C004` (22×, `prog/fin/fin001/form.php:70` e mais 21), `T117` (22×, `prog/fin/fin001/form.php:1620` e mais 21), `D014` (21×, `prog/fin/fin001/form.php:1621` e mais 20), `T007` (21×, `prog/fin/fin001/form.php:1623` e mais 20), `D046` (18×, `prog/fin/fin001/form.php:1622` e mais 17), `D154` (11×, `prog/fin/fin001/form.php:99` e mais 10), `D032` (7×, `prog/fin/fin001/form.php:3347` e mais 6), `T035` (7×, `prog/fin/fin001/grid.php:360` e mais 6), `T037` (6×, `prog/fin/fin001/content.php:435` e mais 5), `D020` (6×, `prog/fin/fin001/form.php:91` e mais 5), `D018` (6×, `prog/fin/fin001/form.php:91` e mais 5), `D021` (6×, `prog/fin/fin001/grid.php:363` e mais 5), `T005` (5×, `prog/fin/fin001/form.php:1625` e mais 4), `D062` (5×, `prog/fin/fin001/form.php:2062` e mais 4), `T019` (3×, `prog/fin/fin001/form.php:648` e mais 2), `D085` (3×, `prog/fin/fin001/grid.php:2865` e mais 1), `T015` (2×, `prog/fin/fin001/form.php:172` e mais 1), `D031` (2×, `prog/fin/fin001/grid.php:4256` e mais 1), `D013` (1×, `prog/fin/fin001/grid.php:2820`), `T244` (1×, `prog/fin/fin001/grid.php:3866`), `T317` (1×, `prog/fin/fin001/grid.php:5632`), `D073` (1×, `prog/fin/fin001/grid.php:5637`)
- **Classes e métodos chamados:** `FIN001` (7368 linhas): `duplicarRegistrosUpdate` (`FIN001.php:4400`), `duplicarRegistros` (`FIN001.php:4330`), `vinculaContaGrupoT020` (`FIN001.php:6801`), `estornar` (`FIN001.php:885`), `gerarRemessaBancaria` (`FIN001.php:1009`), `agrupar` (`FIN001.php:1175`), `trocoSmart`, `baixarSmart` (`FIN001.php:11`), `baixar` (`FIN001.php:28`), `reparcelar` …
- **Tabelas gravadas pelos métodos chamados:** `D007` (FIN001::fin001reparcelar), `T002` (FIN001::FIN001AlterarDataVencimento, FIN001::abaterSaldoCreditoTrocaEmTitulo, FIN001::agrupar…), `T002A` (FIN001::enviarMultiEmailBoleto), `T019` (FIN001::abaterSaldoCreditoTrocaEmTitulo, FIN001::baixar, FIN001::baixarSaldoCredito…), `T020` (FIN001::abaterSaldoCreditoTrocaEmTitulo, FIN001::cancelarTitulo, FIN001::excluirAcrescimoDesconto…), `T035` (FIN001::fin001removerRemessa), `T037` (FIN001::fin001excluirRetorno), `T038` (FIN001::fin001excluirRetorno), `T045` (FIN001::baixar, FIN001::baixarSaldoCredito), `T103` (FIN001::atualizaTitulosParaBaixarT103, FIN001::baixar, FIN001::baixarSaldoCredito), `T117` (FIN001::abaterSaldoCreditoTrocaEmTitulo, FIN001::fin001removerRemessa, FIN001::fin001reparcelar), `T139` (FIN001::enviarMultiEmailBoleto), `T166` (FIN001::agrupar, FIN001::fin001Dividir, FIN001::fin001atualizarParcelas…), `T180` (FIN001::fin001AtualizarParcelas2, FIN001::fin001atualizarParcelas, FIN001::fin001inserirParcela…), `T244` (FIN001::fin001Dividir, FIN001::fin001reparcelar) …
- **Rotinas SQL chamadas (`scripts/sql/`):** `D007_Tipo_Registro` (`prog/fin/fin001/form.php:789`), `D024_Email_Cliente` (`prog/fin/fin001/grid.php:5215` e mais 2), `D024_Gravar_Totalizacao` (`prog/fin/fin001/grid.php:3960` e mais 2), `T002_Baixar_Titulo` (`prog/fin/fin001/form.php:399`), `T002_Gravar_Totalizacao` (`prog/fin/fin001/form.php:398` e mais 1)
- **Chaves C031 lidas (11):** `HabilitarDataAtualEstornoReceber`, `aceitarTitulosReceberAtrasados`, `aceitarVencimentoAnteriorEmissaoPagar`, `agrupamentoDesabilitado`, `baixaTitulos`, `exibirTitulosReceberAgrupados`, `habilitarBaixaEmEtapasContasReceber`, `numeroMaxParcelas`, `obrigatorioFiltroTelaTransicao`, `separarClientePorEmpresa`, `utilizaTrocoBaixa`
- **Mensagens e validações visíveis (22):**
  - "Processo concluído com sucesso" (`prog/fin/fin001/content.php:1066`)
  - "Agrupado com sucesso! Deseja abrir o novo título?" (`prog/fin/fin001/form.php:985`)
  - "Registro Alterado com Sucesso!" (`prog/fin/fin001/form.php:240`)
  - "Registro Incluido com Sucesso!" (`prog/fin/fin001/form.php:382`)
  - "Remessa gerada com sucesso!  Abrir Arquivo" (`prog/fin/fin001/form.php:812`)
  - "Agrupado!" (`prog/fin/fin001/form.php:963`)
  - "Baixa realizada com sucesso!" (`prog/fin/fin001/form.php:1236`)
  - "Reparcelamento efetuado com sucesso!" (`prog/fin/fin001/form.php:1374`)
  - … mais 14
- **Abre as telas:** `etc/etc006`, `fin/fin005`, `crm/crm001`, `fin/fin002`, `cad/cad001`
- **Grafo (Graphify):** 84 nós, 74 funções definidas: `alteraConta()`, `calculaJuros()`, `calcularValorTroco()`, `fin001AdicionarParcela()`, `fin001FixBotaoX()`, `fin001FixParcelas()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `cad/cad001`, `cad/cad034`, `cad/cad061`, `crm/crm001`, `crm/crm002`, `crm/crm003`, `erp/erp003`, `est/est020`, `fin/fin003`, `rel/relatorios`, `ven/ven003`, `ven/ven010`

### `prog/fin/fin002` — Contas a Pagar

- **Menu:** Financeiro › Contas a Pagar, acesso C029 nº 36 "Contas a Pagar" (`bibliotecas/menu.php:71`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 111 "Excluir títulos e lançamentos" (`prog/fin/fin002/grid.php:254` e mais 2)
- **Arquivos:** 10 arquivos. 6.294 linhas. `grid.php` (2190), `form.php` (2166), `grid_func-ajax.php` (415), `content.php` (414), `grid_func-callback.php` (246), `tab.php` (238), `grid_func-js.php` (178), `outro.php` (177), `form_func-ajax.php` (152), `form_func-js.php` (118)
- **Abas (tab.php):** Título a Pagar (`tab.php:21`), Reparcelamentos/Agrupamentos (`tab.php:22`), Importar CSV (`tab.php:23`), Cadastro de Título (`tab.php:55`), Titulos a baixar (`tab.php:89`), Agrupar de Títulos (`tab.php:113`), Detalhes (`tab.php:132`), Títulos Geradores (`tab.php:133`), Títulos Gerados (`tab.php:134`), Projeções (`tab.php:153`), Títulos a dividir (`tab.php:172`), Títulos a gerar (`tab.php:191`) …
- **Ações (`case` do roteador, 83):** `fin002content01`, `fin002content02`, `fin002content03`, `fin002ContentAgrupar`, `fin002ContentReparcelar`, `fin002ContentReparcelamento`, `fin002ContentReparcelarWindow`, `fin002ContentAcrescimosDescontos`, `contentBaixa2rows`, `fin002ContentProjecoes`, `fin002contentAgrupar`, `fin002ContentDocumentos`, `fin002ContentDividir`, `fin002ContentDivisao`, `fin002ImportarCSV`, `fin002form01`, `fin002form02`, `fin002form04`, `fin002form05`, `fin002FormAgrupar` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T015` (fotografia:17763): UPDATE/form em `prog/fin/fin002/form.php:38`, `prog/fin/fin002/form.php:529` e mais 6
  - `T033` (fotografia:18483): INSERT/form em `prog/fin/fin002/form.php:541`, `prog/fin/fin002/form.php:563` e mais 3
  - `T103` (fotografia:21328): UPDATE em `prog/fin/fin002/grid_func-ajax.php:88`, `prog/fin/fin002/grid_func-ajax.php:99` e mais 3
  - `T196` (fotografia:23971): form em `prog/fin/fin002/form.php:1387`
  - `T197` (fotografia:23995): DELETE em `prog/fin/fin002/grid_func-ajax.php:202`
  - `T243` (fotografia:26273): form em `prog/fin/fin002/form.php:1790`
- **Tabelas só lidas na tela (19):** `D024` (20×, `prog/fin/fin002/form.php:63` e mais 19), `D014` (19×, `prog/fin/fin002/form.php:65` e mais 18), `D031` (14×, `prog/fin/fin002/form.php:64` e mais 13), `C007` (13×, `prog/fin/fin002/form.php:1560` e mais 12), `D032` (12×, `prog/fin/fin002/form.php:66` e mais 11), `T007` (12×, `prog/fin/fin002/form.php:1558` e mais 11), `D073` (11×, `prog/fin/fin002/form.php:1557` e mais 10), `D154` (10×, `prog/fin/fin002/form.php:120` e mais 9), `C004` (10×, `prog/fin/fin002/form.php:1559` e mais 9), `D046` (6×, `prog/fin/fin002/form.php:1556` e mais 5), `D020` (4×, `prog/fin/fin002/form.php:111` e mais 3), `D018` (4×, `prog/fin/fin002/form.php:111` e mais 3), `T018` (3×, `prog/fin/fin002/form_func-ajax.php:69` e mais 2), `T019` (3×, `prog/fin/fin002/grid.php:529` e mais 2), `D007` (2×, `prog/fin/fin002/form.php:921` e mais 1), `D027` (1×, `prog/fin/fin002/grid.php:1408`), `T247` (1×, `prog/fin/fin002/grid.php:1984`), `T248` (1×, `prog/fin/fin002/grid.php:2114`), `T013` (1×, `prog/fin/fin002/grid_func-ajax.php:318`)
- **Classes e métodos chamados:** `FIN001` (7368 linhas) (só incluída); `FIN002` (3354 linhas): `duplicarRegistrosUpdate` (`FIN002.php:2272`), `duplicarRegistros` (`FIN002.php:2211`), `vinculaContaGrupoT033` (`FIN002.php:3299`), `estornar` (`FIN002.php:198`), `baixar` (`FIN002.php:291`), `agrupar` (`FIN002.php:950`), `fin002atualizarParcelas` (`FIN002.php:1468`), `gerarParcelasDivisao` (`FIN002.php:2441`), `importarCSV` (`FIN002.php:2771`), `fin002inserirParcela` (`FIN002.php:1967`) …; `FIN009` (471 linhas): `FIN009RecalculaProjecao` (`FIN009.php:13`)
- **Tabelas gravadas pelos métodos chamados:** `T002` (FIN009::FIN009RecalculaProjecao), `T015` (FIN002::agrupar, FIN002::baixar, FIN002::duplicarRegistros…), `T018` (FIN009::FIN009RecalculaProjecao), `T019` (FIN002::baixar, FIN002::estornar, FIN009::FIN009RecalculaProjecao), `T033` (FIN002::excluirAcrescimoDesconto), `T103` (FIN002::atualizaTitulosParaBaixarT103, FIN002::baixar), `T196` (FIN002::agrupar, FIN002::fin002Dividir, FIN002::fin002reparcelar), `T197` (FIN002::fin002AtualizarParcelas2, FIN002::fin002atualizarParcelas, FIN002::fin002inserirParcela…), `T247` (FIN002::atualizarParcelasDivisao, FIN002::fin002Dividir, FIN002::gerarParcelasDivisao), `T248` (FIN002::fin002Dividir, FIN002::fin002reparcelar)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T015_Baixar_Titulo` (`prog/fin/fin002/form.php:603` e mais 1), `T015_Gravar_Totalizacao` (`prog/fin/fin002/form.php:602` e mais 2)
- **Chaves C031 lidas (11):** `HabilitarDataAtualEstornoPagar`, `aceitarTitulosPagarAtrasados`, `aceitarTitulosPagarNumeroValorDuplicataIguais`, `aceitarVencimentoAnteriorEmissaoPagar`, `contasPagarUnificado`, `exibirTitulosPagarAgrupados`, `habilitarBaixaLoteAjaxPagar`, `numeroMaxParcelas`, `obrigatorioFiltroTelaTransicao`, `separarClientePorEmpresa`, `verificarSaldoConta`
- **Mensagens e validações visíveis (13):**
  - "Processo concluído com sucesso" (`prog/fin/fin002/content.php:359`)
  - "Registro Alterado com Sucesso!" (`prog/fin/fin002/form.php:388`)
  - "Registro Incluido com Sucesso!" (`prog/fin/fin002/form.php:586`)
  - "Baixa realizada com sucesso!" (`prog/fin/fin002/form.php:1175`)
  - "Agrupado!" (`prog/fin/fin002/form.php:1313`)
  - "Parcelas cadastradas com sucesso." (`prog/fin/fin002/form.php:1724`)
  - "Parcelas geradas com sucesso." (`prog/fin/fin002/form.php:2020`)
  - "' . $retorno . '" (`prog/fin/fin002/form.php:2124`)
  - … mais 5
- **Abre as telas:** `cad/cad001`, `cad/cad034`, `etc/etc006`
- **Grafo (Graphify):** 51 nós, 41 funções definidas: `atualizaTotaisBaixar()`, `baixarTitulosEditarCampo()`, `carregaInputAgrupar()`, `carregaInputBaixar()`, `carregaInputReparcelar()`, `fin002CampoExclusao()` …. Chama fora da tela: `number_format()` (formulario.php)
- **Aberta por:** `cad/cad034`, `crm/crm003`, `erp/erp003`, `est/est004`, `est/est005`, `fin/fin001`, `fin/fin003`, `imp/imp003`, `rel/relatorios`, `srm/srm001`, `srm/srm002`, `ven/ven003`

### `prog/fin/fin003` — Caixa e Bancos

- **Menu:** Financeiro › Caixa e Bancos, acesso C029 nº 37 "Caixa e Bancos" (`bibliotecas/menu.php:72`)
- **Permissões checadas no código (`$g['c029Ids'][N]`):** 267 "Desabilitar alteração dos valores no lançamento de baixa de titulos" (`prog/fin/fin003/form.php:60`)
- **Arquivos:** 9 arquivos. 2.988 linhas. `grid.php` (1068), `form.php` (1015), `content.php` (291), `grid_func-ajax.php` (228), `tab.php` (138), `form_func-ajax.php` (93), `form_func-js.php` (81), `outro.php` (49), `grid_func-js.php` (25)
- **Abas (tab.php):** Bancos (`tab.php:11`), Lançamentos (`tab.php:27`), Extratos (`tab.php:28`), Lançamento (`tab.php:42`), Lançamentos (`tab.php:56`), Caixas e Bancos (`tab.php:71`), Extrato (`tab.php:84`), Regras (`tab.php:85`), Excluídos (`tab.php:86`), Títulos a Pagar (`tab.php:108`), Títulos a Receber (`tab.php:109`), Transação (`tab.php:128`)
- **Ações (`case` do roteador, 53):** `fin003Content02`, `fin003ContentGeral`, `fin003ContentEditaLancamento`, `fin003ContentIncluirLancamento`, `fin003ContentExtratos`, `fin003ContentTransacoes`, `fin003ContentComposicaoRetorno`, `fin003ContentIncluirTransferencia`, `fin003ContentDesmembrarTransacao`, `fin003ContentEditarTransacao`, `fin003ContentEditarRegra`, `fin003FormLancamentos`, `fin003FormImportarExtrato`, `fin003FormIncluirTransferencia`, `fin003FormEditarTransacao`, `fin003FormEditarRegraExtrato`, `fin003atualizarSaldoConta`, `alteraConta`, `processarExtrato`, `aplicarRegrasExtrato` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D007` (fotografia:3510): UPDATE em `prog/fin/fin003/grid_func-ajax.php:52`
  - `T002` (fotografia:14415): UPDATE em `prog/fin/fin003/grid_func-ajax.php:92`
  - `T015` (fotografia:17763): UPDATE em `prog/fin/fin003/grid_func-ajax.php:85`
  - `T019` (fotografia:17992): UPDATE/form em `prog/fin/fin003/form.php:32`, `prog/fin/fin003/form.php:535` e mais 3
  - `T216` (fotografia:24464): UPDATE/form em `prog/fin/fin003/form.php:682`, `prog/fin/fin003/grid.php:387` e mais 6
  - `T217` (fotografia:24497): UPDATE/form em `prog/fin/fin003/form.php:822`, `prog/fin/fin003/grid_func-ajax.php:164`
- **Tabelas só lidas na tela (14):** `D154` (11×, `prog/fin/fin003/form.php:73` e mais 10), `D014` (10×, `prog/fin/fin003/form.php:162` e mais 9), `D024` (10×, `prog/fin/fin003/form.php:689` e mais 9), `D027` (5×, `prog/fin/fin003/grid.php:137` e mais 4), `D031` (5×, `prog/fin/fin003/grid.php:138` e mais 4), `D020` (4×, `prog/fin/fin003/form.php:121` e mais 3), `D018` (4×, `prog/fin/fin003/form.php:121` e mais 3), `T018` (4×, `prog/fin/fin003/form.php:187` e mais 3), `D032` (4×, `prog/fin/fin003/grid.php:424` e mais 3), `T215` (2×, `prog/fin/fin003/grid.php:325` e mais 1), `D073` (2×, `prog/fin/fin003/grid.php:704` e mais 1), `T007` (2×, `prog/fin/fin003/grid.php:709` e mais 1), `C007` (2×, `prog/fin/fin003/grid.php:710` e mais 1), `C004` (1×, `prog/fin/fin003/grid.php:32`)
- **Classes e métodos chamados:** `ComposicaoRetornoExtrato` (307 linhas) (só incluída); `FIN003` (898 linhas): `importarExtratoBancario` (`FIN003.php:37`), `incluirTransferencia` (`FIN003.php:530`), `processarExtrato` (`FIN003.php:232`), `aplicarRegrasExtrato` (`FIN003.php:422`), `incluirRegraExtrato` (`FIN003.php:410`), `listaTotais` (`FIN003.php:12`), `desmembrarTransacao` (`FIN003.php:697`), `desmembrarTransacaoRecebida` (`FIN003.php:781`); `FIN009` (471 linhas): `FIN009RecalculaProjecao` (`FIN009.php:13`)
- **Tabelas gravadas pelos métodos chamados:** `T002` (FIN003::desmembrarTransacaoRecebida, FIN003::processarExtrato, FIN009::FIN009RecalculaProjecao), `T015` (FIN003::desmembrarTransacao, FIN003::processarExtrato, FIN009::FIN009RecalculaProjecao), `T018` (FIN009::FIN009RecalculaProjecao), `T019` (FIN003::incluirTransferencia, FIN003::processarExtrato, FIN009::FIN009RecalculaProjecao), `T215` (FIN003::importarExtratoBancario, FIN003::processarExtrato), `T216` (FIN003::aplicarRegrasExtrato, FIN003::desmembrarTransacao, FIN003::desmembrarTransacaoRecebida…), `T217` (FIN003::incluirRegraExtrato)
- **Chaves C031 lidas (4):** `agrupamentoDesabilitado`, `obrigatorioFiltroTelaTransicao`, `separarClientePorEmpresa`, `verificarSaldoConta`
- **Mensagens e validações visíveis (9):**
  - "Deseja processar os registros marcados?" (`prog/fin/fin003/content.php:156`)
  - "Deseja aplicar as regras no extrato?" (`prog/fin/fin003/content.php:176`)
  - "Registro Alterado com Sucesso!" (`prog/fin/fin003/form.php:213`)
  - "A conta selecionada não permite a inclusão de lançamentos sem um título a '.$tipoLancamento.'!" (`prog/fin/fin003/form.php:270`)
  - "Registro Incluido com Sucesso!" (`prog/fin/fin003/form.php:314`)
  - "Selecione um arquivo para importar" (`prog/fin/fin003/form.php:464`)
  - "Arquivo importado com Sucesso!" (`prog/fin/fin003/form.php:474`)
  - "Transferência realizada com sucesso!" (`prog/fin/fin003/form.php:597`)
  - … mais 1
- **Abre as telas:** `fin/fin002`, `fin/fin001`
- **Grafo (Graphify):** 17 nós, 8 funções definidas: `alteraConta()`, `alteraContaRegra()`, `alteraContaTransacao()`, `retornaSaldoConta()`, `carregaInputsTotais()`, `fin003MarcarRegistroExtrato()` …
- **Aberta por:** `cad/cad034`, `fin/fin009`

### `prog/fin/fin005` — Boleto Bancário

- **Menu:** Financeiro › Boleto Bancário, acesso C029 nº 38 "Boleto Bancário" (item comentado no Menu) (`bibliotecas/menu.php:218`)
- **Arquivos:** 9 arquivos. 2.090 linhas. `grid.php` (614), `form.php` (455), `grid_func-ajax.php` (422), `content.php` (231), `grid_func-callback.php` (129), `tab.php` (90), `form_func-ajax.php` (69), `grid_func-js.php` (62), `form_func-js.php` (18)
- **Abas (tab.php):** Boleto Bancário (`tab.php:18`), Cadastro de Boleto (`tab.php:39`), Títulos (`tab.php:60`), Histórico (`tab.php:61`), Boletos (`tab.php:78`)
- **Ações (`case` do roteador, 34):** `fin005ContentPrincipal`, `fin005ContentAbreCadastroBoleto`, `fin005ContentAtualizarVencimento`, `fin005ContentAtualizarVencimentoMarcados`, `fin005ContentBoletosParaEnvio`, `fin005FormCadastro`, `fin005FormAtualizarVencimento`, `fin005FormAtualizarVencimentoMarcados`, `preencheCampos`, `fin005GridPrincipal`, `fin005GridTitulos`, `fin005GridHistorico`, `fin005GridBoletosParaEnvio`, `cancelar`, `gerarPDFboleto`, `fin005DeletarPDFBoleto`, `flagGerarBoleto`, `enviarMultiEmailBoleto`, `enviarMultiEmailBoletoACP`, `enviarMultiEmailBoletoLembrete` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T002` (fotografia:14415): UPDATE em `prog/fin/fin005/grid.php:334`
  - `T117` (fotografia:21688): UPDATE/form em `prog/fin/fin005/form.php:47`, `prog/fin/fin005/form.php:196` e mais 6
- **Tabelas só lidas na tela (9):** `D007` (11×, `prog/fin/fin005/form.php:20` e mais 10), `D024` (5×, `prog/fin/fin005/form.php:16` e mais 4), `D018` (3×, `prog/fin/fin005/form.php:17` e mais 2), `D020` (3×, `prog/fin/fin005/form.php:18` e mais 2), `D027` (3×, `prog/fin/fin005/form.php:19` e mais 2), `D062` (2×, `prog/fin/fin005/form.php:87` e mais 1), `T007` (2×, `prog/fin/fin005/grid.php:362` e mais 1), `T035` (1×, `prog/fin/fin005/form.php:203`), `T117A` (1×, `prog/fin/fin005/grid.php:59`)
- **Classes e métodos chamados:** `FIN001` (7368 linhas): `validacoesTravarT002` (`FIN001.php:4090`); `FIN005` (1908 linhas): `gerarBoleto` (`FIN005.php:68`), `atualizarVencimentoBoleto` (`FIN005.php:970`), `atualizarVencimentoBoletosMarcados` (`FIN005.php:1217`), `cancelar` (`FIN005.php:33`), `updateT002_Flag_Gerar_Boleto` (`FIN005.php:51`), `enviarMultiEmailBoleto` (`FIN005.php:511`), `enviarMultiEmailBoletoACP` (`FIN005.php:750`), `enviarMultiEmailBoletoLembrete` (`FIN005.php:1457`), `enviarMultiEmailBoletoCobranca` (`FIN005.php:1253`), `desfazerCancelar` (`FIN005.php:957`)
- **Tabelas gravadas pelos métodos chamados:** `D007` (FIN005::atualizarVencimentoBoleto, FIN005::gerarBoleto), `T002` (FIN005::atualizarVencimentoBoleto, FIN005::cancelar, FIN005::desfazerCancelar…), `T117` (FIN005::atualizarVencimentoBoleto, FIN005::atualizarVencimentoBoletosMarcados, FIN005::cancelar…), `T117A` (FIN005::gerarBoleto), `T139` (FIN005::enviarMultiEmailBoleto, FIN005::enviarMultiEmailBoletoACP, FIN005::enviarMultiEmailBoletoCobranca…)
- **Rotinas SQL chamadas (`scripts/sql/`):** `D024_Email_Cliente` (`prog/fin/fin005/grid.php:43` e mais 1)
- **Chaves C031 lidas (1):** `atualizarNossoNumeroBoleto`
- **Mensagens e validações visíveis (4):**
  - "Boleto gerado com sucesso!" (`prog/fin/fin005/form.php:125`)
  - "Boleto atualizado com sucesso!" (`prog/fin/fin005/form.php:260`)
  - "Boletos atualizados com sucesso!" (`prog/fin/fin005/form.php:393`)
  - "O valor do boleto excede o valor limite para boletos sem registro. Registre-o para continuar a operação" (`prog/fin/fin005/grid_func-callback.php:83`)
- **Abre as telas:** `etc/etc006`
- **Grafo (Graphify):** 18 nós, 9 funções definidas: `campoAtualizarVencimento()`, `campoCancelar()`, `campoeMail()`, `campoGerar()`, `fin005CampoCancelar()`, `fin005CampoDesfazerCancelamento()` …
- **Aberta por:** `crm/crm003`, `erp/erp003`, `fin/fin001`, `ven/ven003`

### `prog/fin/fin006` — Comissões de Venda (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 606 linhas. `form.php` (279), `grid.php` (154), `content.php` (84), `tab.php` (81), `grid_func-callback.php` (8)
- **Abas (tab.php):** Comissões de Venda (`tab.php:21`), Comissões (`tab.php:45`), Totalizar Comissões (`tab.php:69`)
- **Ações (`case` do roteador, 9):** `fin006content01`, `fin006content02`, `fin006content03`, `fin006form01`, `fin006form02`, `fin006grid01`, `fin006tab01`, `fin006tab02`, `fin006tab03`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T137` (fotografia:22393): form em `prog/fin/fin006/form.php:38`, `prog/fin/fin006/form.php:163`
- **Tabelas só lidas na tela (1):** `C007` (2×, `prog/fin/fin006/grid.php:55` e mais 1)
- **Classes e métodos chamados:** `FIN006` (346 linhas): `gravarMeta` (`FIN006.php:14`), `totalizarComissoes` (`FIN006.php:57`)
- **Tabelas gravadas pelos métodos chamados:** `T007` (FIN006::totalizarComissoes), `T013` (FIN006::totalizarComissoes), `T137` (FIN006::gravarMeta, FIN006::totalizarComissoes)
- **Mensagens e validações visíveis (2):**
  - "Informações gravadas com sucesso!" (`prog/fin/fin006/form.php:76`)
  - "Totalização realizada com sucesso!" (`prog/fin/fin006/form.php:230`)
- **Grafo (Graphify):** 5 nós, 0 funções definidas

### `prog/fin/fin007` — Análise de Crédito

- **Menu:** Financeiro › Análise de Crédito, acesso C029 nº 39 "Análise de Crédito" (`bibliotecas/menu.php:73`)
- **Menu:** Financeiro › Análise de Crédito (Nota), acesso C029 nº 39 "Análise de Crédito" (`bibliotecas/menu.php:74`)
- **Arquivos:** 8 arquivos. 1.647 linhas. `grid.php` (543), `grid_func-callback.php` (346), `outro.php` (179), `grid_func-js.php` (138), `tab.php` (127), `form.php` (119), `grid_func-ajax.php` (113), `content.php` (82)
- **Abas (tab.php):** Análise de Crédito (`tab.php:21`), Clientes (`tab.php:22`), Análise de Crédito (Nota) (`tab.php:42`), Valor Limite (`tab.php:70`), Saldo Limite (`tab.php:107`)
- **Ações (`case` do roteador, 24):** `fin007content01`, `fin007content02`, `fin007ContentValorLimite`, `fin007ContentSaldoLimite`, `fin007FormNaoLiberar`, `fin007grid01`, `fin007grid02`, `fin007grid03`, `DemonstrativoValorLimite`, `DemonstrativoSaldoLimite`, `validaCampoLiberar`, `validarCamposNFe`, `fin007ValidaCampoRetornarAlmx`, `fin007CheckboxAguardar`, `fin007CheckboxProtestar`, `validaCampoLiberarNota`, `validaCampoNaoLiberarNota`, `fin007cabecalhoValorLimite`, `fin007cabecalhoSaldoLimite`, `cad001CabecalhoAgenda`, `fin007tab01`, `fin007tab02`, `fin007TabValorLimite`, `fin007TabSaldoLimite`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D024` (fotografia:4506): UPDATE em `prog/fin/fin007/grid_func-ajax.php:84`
  - `T005` (fotografia:15218): form em `prog/fin/fin007/form.php:35`
  - `T007` (fotografia:16254): UPDATE em `prog/fin/fin007/grid_func-ajax.php:92`, `prog/fin/fin007/grid_func-ajax.php:103`
- **Tabelas só lidas na tela (8):** `T002` (10×, `prog/fin/fin007/grid.php:73` e mais 9), `D020` (5×, `prog/fin/fin007/grid.php:222` e mais 4), `D018` (5×, `prog/fin/fin007/grid.php:224` e mais 4), `C007` (2×, `prog/fin/fin007/grid.php:74` e mais 1), `T003` (2×, `prog/fin/fin007/outro.php:126` e mais 1), `D046` (1×, `prog/fin/fin007/grid.php:223`), `D021` (1×, `prog/fin/fin007/grid.php:225`), `D085` (1×, `prog/fin/fin007/outro.php:126`)
- **Classes e métodos chamados:** `FIN007` (442 linhas): `naoLiberar` (`FIN007.php:198`), `validaCampoLiberar` (`FIN007.php:265`), `validaCampoRetornarAlmx` (`FIN007.php:103`), `updateCheckboxAguardar` (`FIN007.php:165`), `calculaOutrosPedidos` (`FIN007.php:13`), `formaPagamento` (`FIN007.php:43`), `formaPagamentoNota` (`FIN007.php:73`)
- **Tabelas gravadas pelos métodos chamados:** `T005` (FIN007::naoLiberar, FIN007::updateCheckboxAguardar, FIN007::validaCampoLiberar…), `T006` (FIN007::validaCampoLiberar), `T178` (FIN007::naoLiberar, FIN007::validaCampoLiberar, FIN007::validaCampoRetornarAlmx), `T238` (FIN007::validaCampoLiberar)
- **Tabelas só lidas pelos métodos chamados (8):** `D009`, `D024`, `D027`, `T005A`, `T007`, `T066`, `T090`, `T118`
- **Rotinas SQL chamadas (`scripts/sql/`):** `T005_Status_Pedido` (`prog/fin/fin007/grid.php:62`)
- **Chaves C031 lidas (2):** `somarLimiteCnpjMatriz`, `validarNFEAnaliseDeCredito`
- **Mensagens e validações visíveis (2):**
  - "Ativado! Você irá receber notificações de análise pendente em todo o sistema." (`prog/fin/fin007/grid.php:93`)
  - "Desativado! Você não irá mais receber notificações de análise pendente em todo o sistema." (`prog/fin/fin007/grid.php:97`)
- **Alertas do sino que abrem esta tela:** `NOTIFICACOES::liberarCredito` (`NOTIFICACOES.php:644`, clique na linha 659; lê `T005`); `NOTIFICACOES::liberarCreditoNota` (`NOTIFICACOES.php:669`, clique na linha 684; lê `T007`)
- **Abre as telas:** `ven/ven002`, `ven/ven003`, `ven/ven001`, `crm/crm001`
- **Grafo (Graphify):** 31 nós, 23 funções definidas: `fin007CheckboxAguardar()`, `fin007ValidaCampoLiberar()`, `fin007ValidaCampoLiberarNota()`, `fin007ValidaCampoNaoLiberar()`, `fin007ValidaCampoNaoLiberarNota()`, `fin007ValidaCampoRetornarAlmx()`, `calculaOutrosPedidos()`, `calculaPedidos()`, `calculaSaldoLimite()`, `calculaValorLimite()`, `calculaValorVencer()`, `calculaValorVencido()` …. Chama fora da tela: `number_format()` (formulario.php)

**Leitura do código (manual):**

- **Fila de crédito:** pedido com situação 6, "Financeiro". Duas entradas no Menu com o mesmo acesso 39: Análise de Crédito e Análise de Crédito (Nota) (`bibliotecas/menu.php:73–74`).
- **Liberar** (`FIN007::validaCampoLiberar`, `bibliotecas/classes/FIN007.php:265–…`): Cliente em pré-cadastro bloqueia (`:292–294`). O pedido precisa estar ainda na situação 6, senão "já foi analisado por outro usuário" (`:296–298`). A próxima situação vem da C031 `statusInicialPedido`: 1 Imprimir, 10 Conferência, 3 Emitir NF ou 21 Aguardando Peças (`:273`, `:304–316`).
- **Negar** (`FIN007::naoLiberar`, `FIN007.php:198–257`): em transação, grava situação 9, trava o pedido, guarda o motivo com data e hora e o histórico T178 (`:202–246`). As chamadas que atualizariam os Alertas de crédito estão comentadas (`:248–254`).
- **Na Fixar:** `statusInicialPedido` = `1`, Impressão (25/11/2026). O ramo que vale é "Autorizou crédito. Enviado para impressão" (`FIN007.php:305–307`); os ramos 10, 3 e 21 (`:308–316`) saem do porte. O ramo `modeloExpedicao == '2'` (`:350`) também sai.
- **Alertas (fora do escopo, dívida):** `NOTIFICACOES::liberarCredito` e `liberarCreditoNota` (`NOTIFICACOES.php:644–693`) abrem esta tela.

### `prog/fin/fin008` — Comissões

- **Menu:** Financeiro › Comissões, acesso C029 nº 163 (ausente da C029 na fotografia) (item comentado no Menu) (`bibliotecas/menu.php:221`)
- **Menu:** Gerencial › Mapa de Vendas, acesso C029 nº 164 "Mapa de Vendas" (item comentado no Menu) (`bibliotecas/menu.php:225`)
- **Menu:** Gerencial › Fluxo de Caixa, acesso C029 nº 149 "Fluxo de Caixa" (item comentado no Menu) (`bibliotecas/menu.php:228`)
- **Menu:** Gerencial › DRE, acesso C029 nº 198 "DRE" (item comentado no Menu) (`bibliotecas/menu.php:229`)
- **Menu:** Gerencial › Balancete, acesso C029 nº 162 "Balancete" (item comentado no Menu) (`bibliotecas/menu.php:230`)
- **Arquivos:** 4 arquivos. 1.081 linhas. `form.php` (620), `grid.php` (253), `tab.php` (114), `content.php` (94)
- **Abas (tab.php):** Fluxo de Caixa (`tab.php:21`), Balancete (`tab.php:41`), Comissões (`tab.php:62`), Mapa de Vendas (`tab.php:82`), DRE (`tab.php:102`)
- **Ações (`case` do roteador, 17):** `fin008content01FluxoCaixa`, `fin008contentBalancete`, `fin008contentComissoes`, `fin008contentMapaVendas`, `fin008contentDRE`, `fin008FormFluxoCaixa`, `fin008FormBalancete`, `fin008FormComissoes`, `fin008FormMapaVendas`, `fin008FormDRE`, `fin007grid01`, `fin007grid02`, `fin008tabPrincipalFluxoCaixa`, `fin008tabBalancete`, `fin008tabComissoes`, `fin008tabMapaVendas`, `fin008tabDRE`
- **Tabelas só lidas na tela (9):** `D024` (2×, `prog/fin/fin008/grid.php:55` e mais 1), `T005` (1×, `prog/fin/fin008/grid.php:52`), `T007` (1×, `prog/fin/fin008/grid.php:56`), `T002` (1×, `prog/fin/fin008/grid.php:57`), `C007` (1×, `prog/fin/fin008/grid.php:58`), `D020` (1×, `prog/fin/fin008/grid.php:182`), `D046` (1×, `prog/fin/fin008/grid.php:183`), `D018` (1×, `prog/fin/fin008/grid.php:184`), `D021` (1×, `prog/fin/fin008/grid.php:185`)
- **Classes e métodos chamados:** `FIN007` (442 linhas) (só incluída)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T005_Status_Pedido` (`prog/fin/fin008/grid.php:49`)
- **Mensagens e validações visíveis (2):**
  - "Ativado! Você irá receber notificações de análise pendente em todo o sistema." (`prog/fin/fin008/grid.php:75`)
  - "Desativado! Você não irá mais receber notificações de análise pendente em todo o sistema." (`prog/fin/fin008/grid.php:79`)
- **Abre as telas:** `ven/ven002`
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/fin/fin009` — Projeção Orçamentária

- **Menu:** Financeiro › Projeção Orçamentária, acesso C029 nº 150 "Projeçao Orçamentária" (`bibliotecas/menu.php:75`)
- **Menu:** Financeiro › Referências Comerciais, acesso C029 nº 156 "Referências Comerciais" (item comentado no Menu) (`bibliotecas/menu.php:220`)
- **Arquivos:** 7 arquivos. 1.361 linhas. `grid.php` (891), `form.php` (234), `grid_func-ajax.php` (97), `grid_func-callback.php` (45), `content.php` (44), `tab.php` (32), `grid_func-js.php` (18)
- **Abas (tab.php):** Projeção Orçamentária (`tab.php:19`), Por grupo (`tab.php:20`), Por mês (`tab.php:21`), Lançamentos (`tab.php:22`)
- **Ações (`case` do roteador, 12):** `fin009content01ProjecaoOrcamentaria`, `fin009ContentReferenciasComerciais`, `fin008FormFluxoCaixa`, `fin009formReferenciasComerciais`, `fin009GridProjecaoOrcamentaria`, `fin009GridProjecaoOrcamentariaGrupo`, `fin009GridProjecaoOrcamentariaMes`, `fin009GridLancamentos`, `fin009RecalcularProjecao`, `CheckboxFlagConfirmado`, `fin009AtualizaFlagConfirmado`, `fin009tabProjecaoOrcamentaria`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D024` (fotografia:4506): form em `prog/fin/fin009/form.php:152`
  - `T018` (fotografia:17951): UPDATE em `prog/fin/fin009/grid_func-ajax.php:74`, `prog/fin/fin009/grid_func-ajax.php:88` e mais 1
- **Tabelas só lidas na tela (9):** `D154` (6×, `prog/fin/fin009/grid.php:62` e mais 5), `D014` (5×, `prog/fin/fin009/grid.php:59` e mais 4), `D032` (5×, `prog/fin/fin009/grid.php:63` e mais 4), `T019` (1×, `prog/fin/fin009/grid.php:791`), `D027` (1×, `prog/fin/fin009/grid.php:794`), `D031` (1×, `prog/fin/fin009/grid.php:795`), `T216` (1×, `prog/fin/fin009/grid.php:799`), `D007` (1×, `prog/fin/fin009/grid.php:800`), `C004` (1×, `prog/fin/fin009/grid.php:801`)
- **Classes e métodos chamados:** `FIN007` (442 linhas) (só incluída); `FIN009` (471 linhas): `fin009RecalculaProjecao`
- **Rotinas SQL chamadas (`scripts/sql/`):** `D032_Tipo` (`prog/fin/fin009/grid.php:51`)
- **Chaves C031 lidas (1):** `obrigatorioFiltroTelaTransicao`
- **Mensagens e validações visíveis (2):**
  - "Processado" (`prog/fin/fin009/grid.php:124`)
  - "Processo concluído com sucesso!" (`prog/fin/fin009/grid.php:84`)
- **Abre as telas:** `cad/cad034`, `fin/fin003`
- **Grafo (Graphify):** 13 nós, 6 funções definidas: `campoConfirmado()`, `validaCampoOperacao()`, `ValidaCampoSaldo()`, `atualizaFlagConfirmado()`, `fin009RecalcularProjecaoLoop()`, `fin009CheckboxFlagConfirmado()`

