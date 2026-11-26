# Fichas de tela: `prog/api` — API

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `api/api001` | API | 228 | 6 | 1363 | 4 | 3 |
| `api/cli` |  |  | 5 | 251 | 0 | 5 |
| `api/erp` |  |  | 5 | 105 | 0 | 0 |
| `api/mob` |  |  | 5 | 983 | 3 | 11 |
| `api/red` |  |  | 1 | 12 | 0 | 0 |
| `api/rel` |  |  | 5 | 2927 | 0 | 60 |
| `api/stone` |  |  | 1 | 335 | 0 | 2 |

Total: 7 telas. 5.976 linhas. 7 tabelas gravadas. 67 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/api/api001` — API

- **Menu:** Gerencial › API, acesso C029 nº 228 "Gerencial - Exportação Power BI" (`bibliotecas/menu.php:101`)
- **Arquivos:** 6 arquivos. 1.363 linhas. `form.php` (590), `outro.php` (306), `grid.php` (258), `content.php` (95), `tab.php` (89), `exportar.php` (25)
- **Abas (tab.php):** API GET (`tab.php:18`), Integrador (`tab.php:19`), Exportar CSV (`tab.php:20`), API REST (`tab.php:21`), API REST (`tab.php:25`), Cadastro (`tab.php:41`), Processo (`tab.php:42`), Callback (`tab.php:43`), Processo (`tab.php:59`), Callback (`tab.php:75`)
- **Ações (`case` do roteador, 30):** `API001ContentAPI`, `API001ContentConfApi`, `API001ContentConfApiForm`, `API001ContentConfApiProcessoForm`, `API001ContentCallbackForm`, `API001exportarBI`, `API001FormPolicyAdmin`, `API001FormAPI`, `API001FormConfApi`, `API001FormApiProcessos`, `API001FormApiCallback`, `API001GridConfApi`, `API001GridAPIProcessos`, `API001GridAPICallback`, `API001OutroLinksApi`, `API001PolicyStatus`, `API001PolicyAccessStatus`, `API001PolicyUnlock`, `API001PolicyLoadTable`, `API001PolicyPreview` …
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `C033` (fotografia:2338): form em `prog/api/api001/form.php:174`
  - `C037` (fotografia:2444): form em `prog/api/api001/form.php:317`
  - `C038` (fotografia:2464): form em `prog/api/api001/form.php:461`
  - `D024` (fotografia:4506): form em `prog/api/api001/form.php:83`
- **Tabelas só lidas na tela (3):** `D020` (1×, `prog/api/api001/form.php:192`), `D018` (1×, `prog/api/api001/form.php:192`), `C007` (1×, `prog/api/api001/outro.php:33`)
- **Classes e métodos chamados:** `API001` (3648 linhas): `exportarAPI001` (`API001.php:802`); `API001PolicyAdmin` (319 linhas): `assertAuthorized` (`API001PolicyAdmin.php:32`), `resetRestApiUnlock` (`API001PolicyAdmin.php:51`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/api/api001/form.php:219`)
  - "Registro Incluido com Sucesso!" (`prog/api/api001/form.php:239`)
- **Grafo (Graphify):** 8 nós, 2 funções definidas: `API001PolicyJsonResponse()`, `copyToClipboard()`

### `prog/api/cli`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 251 linhas. `get.php` (167), `delete.php` (21), `patch.php` (21), `post.php` (21), `put.php` (21)
- **Ações (`case` do roteador, 7):** `clientes`, `produtos`, `estoqueQuantidade`, `estoqueDisponibilidade`, `clientes`, `clientes`, `clientes`
- **Tabelas só lidas na tela (5):** `D001` (3×, `prog/api/cli/get.php:41` e mais 2), `D049` (2×, `prog/api/cli/get.php:93` e mais 1), `D009` (2×, `prog/api/cli/get.php:94` e mais 1), `C004` (2×, `prog/api/cli/get.php:95` e mais 1), `D003` (1×, `prog/api/cli/get.php:44`)
- **Classes e métodos chamados:** `API001` (3648 linhas): `retorno404` (`API001.php:30`), `montarWhere`, `tratarRetornoVazio`, `getC004`; `API002` (110 linhas): `auth` (`API002.php:27`)
- **Rotinas SQL chamadas (`scripts/sql/`):** `API_estoque_disponibilidade` (`prog/api/cli/get.php:128`)
- **Grafo (Graphify):** 5 nós, 0 funções definidas

### `prog/api/erp`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 105 linhas. `delete.php` (21), `get.php` (21), `patch.php` (21), `post.php` (21), `put.php` (21)
- **Ações (`case` do roteador, 5):** `clientes`, `clientes`, `clientes`, `clientes`, `clientes`
- **Classes e métodos chamados:** `API001` (3648 linhas): `retorno404` (`API001.php:30`); `API004` (13 linhas) (só incluída)
- **Grafo (Graphify):** 5 nós, 0 funções definidas

### `prog/api/mob`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 983 linhas. `get.php` (421), `post.php` (378), `put.php` (108), `delete.php` (52), `patch.php` (24)
- **Ações (`case` do roteador, 18):** `produto`, `produtos`, `produtoEmpresa`, `configuracoesC031`, `empresasContato`, `carrinho`, `validaLogin`, `quantidadeCarrinho`, `valorTotalCarrinho`, `produtos`, `empresasContato`, `produto`, `pedidoB2B`, `orcamentoB2B`, `esqueciSenha`, `token`, `produto`, `contatoSenha`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D179` (fotografia:9410): UPDATE em `prog/api/mob/put.php:80`
  - `D180` (fotografia:9432): INSERT em `prog/api/mob/post.php:346`
  - `T269` (fotografia:27018): DELETE/INSERT/UPDATE em `prog/api/mob/delete.php:28`, `prog/api/mob/post.php:30` e mais 3
- **Tabelas só lidas na tela (11):** `D001` (5×, `prog/api/mob/get.php:41` e mais 4), `D003` (5×, `prog/api/mob/get.php:44` e mais 4), `D013` (3×, `prog/api/mob/get.php:217` e mais 2), `D049` (2×, `prog/api/mob/get.php:112` e mais 1), `D009` (2×, `prog/api/mob/get.php:114` e mais 1), `D024` (2×, `prog/api/mob/get.php:214` e mais 1), `D082` (1×, `prog/api/mob/get.php:113`), `C031` (1×, `prog/api/mob/get.php:164`), `D018` (1×, `prog/api/mob/get.php:218`), `C007` (1×, `prog/api/mob/post.php:161`), `C004` (1×, `prog/api/mob/post.php:162`)
- **Classes e métodos chamados:** `API001` (3648 linhas): `auth`, `retorno404` (`API001.php:30`), `montarWhere`, `tratarRetornoVazio`, `getC004`, `validaEmpresa`, `getEcommerce`, `getEmailApp`, `getD024_Id`, `getD013_Id` …; `B2B001` (526 linhas) (só incluída)
- **Chaves C031 lidas (1):** `QuantidadePadraoCarrinho`
- **Grafo (Graphify):** 5 nós, 0 funções definidas

### `prog/api/red`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 12 linhas. `ide.php` (12)
- **Grafo (Graphify):** 1 nós, 0 funções definidas

### `prog/api/rel`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 2.927 linhas. `get.php` (2636), `post.php` (228), `delete.php` (21), `patch.php` (21), `put.php` (21)
- **Ações (`case` do roteador, 56):** `clientes`, `estoque_terceiros`, `clientes`, `empresas`, `transportadoras`, `produtos`, `portadoresReceber`, `contas`, `centroDeCustos`, `portadoresPagar`, `notasSaida`, `notasSaidaDetalhes`, `notasSaidaProdutos`, `orcamentosProdutos`, `orcamentos`, `pedidos`, `pedidosDetalhes`, `pedidosProdutos`, `pedidosExpedicao`, `cotacoes` …
- **Tabelas só lidas na tela (60):** `C007` (58×, `prog/api/rel/get.php:499` e mais 57), `D049` (14×, `prog/api/rel/get.php:83` e mais 13), `D001` (14×, `prog/api/rel/get.php:84` e mais 13), `T007` (14×, `prog/api/rel/get.php:496` e mais 13), `D009` (13×, `prog/api/rel/get.php:82` e mais 12), `D006` (13×, `prog/api/rel/get.php:619` e mais 12), `T003` (9×, `prog/api/rel/get.php:671` e mais 8), `T004` (8×, `prog/api/rel/get.php:668` e mais 7), `T219` (7×, `prog/api/rel/get.php:1020` e mais 6), `T008` (5×, `prog/api/rel/get.php:612` e mais 4), `T005` (5×, `prog/api/rel/get.php:794` e mais 4), `T225` (5×, `prog/api/rel/get.php:1138` e mais 4), `T013` (4×, `prog/api/rel/get.php:1258` e mais 3), `D018` (3×, `prog/api/rel/get.php:144` e mais 2), `D020` (3×, `prog/api/rel/get.php:145` e mais 2), `C004` (3×, `prog/api/rel/get.php:181` e mais 2), `D014` (3×, `prog/api/rel/get.php:362` e mais 2), `T014` (3×, `prog/api/rel/get.php:1317` e mais 2), `D085` (3×, `prog/api/rel/get.php:1638` e mais 1), `T312` (3×, `prog/api/rel/get.php:2094` e mais 2), `T001` (2×, `prog/api/rel/get.php:81` e mais 1), `D024` (2×, `prog/api/rel/get.php:141` e mais 1), `D022` (2×, `prog/api/rel/get.php:224` e mais 1), `D036` (2×, `prog/api/rel/get.php:797` e mais 1), `T218` (2×, `prog/api/rel/get.php:1013` e mais 1) …
- **Classes e métodos chamados:** `API001` (3648 linhas): `retorno404` (`API001.php:30`), `tratarRetornoVazio`, `montarWhere`, `executaProcesso` (`API001.php:43`); `API003` (173 linhas): `auth` (`API003.php:28`); `APIMobile` (305 linhas) (só incluída); `SMTOrcamento` (780 linhas): `OrcamentoPolux` (`SMTOrcamento.php:6`)
- **Rotinas SQL chamadas (`scripts/sql/`):** `T007_Prazos` (`prog/api/rel/get.php:538`)
- **Grafo (Graphify):** 12 nós, 6 funções definidas: `.__call()`, `.__construct()`, `.gerarSQLGrid()`, `extraFiltro()`, `{closure#1}()`, `{closure#2}()`

### `prog/api/stone`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 1 arquivos. 335 linhas. `post.php` (335)
- **Ações (`case` do roteador, 1):** `StonePayment`
- **Tabelas só lidas na tela (2):** `T007` (2×, `prog/api/stone/post.php:80` e mais 1), `D027` (2×, `prog/api/stone/post.php:178` e mais 1)
- **Classes e métodos chamados:** `API001` (3648 linhas) (só incluída); `API003` (173 linhas): `auth` (`API003.php:28`)
- **Grafo (Graphify):** 1 nós, 0 funções definidas

