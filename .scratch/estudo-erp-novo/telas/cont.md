# Fichas de tela: `prog/cont` — Contábil

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `cont/cont001` | Contábil | 256 | 4 | 355 | 1 | 0 |
| `cont/cont002` | Lançamentos (rótulo da primeira aba) |  | 5 | 1061 | 3 | 1 |
| `cont/cont003` | Saldo (rótulo da primeira aba) |  | 4 | 349 | 1 | 1 |

Total: 3 telas. 1.765 linhas. 5 tabelas gravadas. 5 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/cont/cont001` — Contábil

- **Menu:** Contábil › Contábil, acesso C029 nº 256 "Contábil" (`bibliotecas/menu.php:98`)
- **Arquivos:** 4 arquivos. 355 linhas. `form.php` (164), `grid.php` (114), `tab.php` (39), `content.php` (38)
- **Abas (tab.php):** Plano contas (`tab.php:9`), Lançamentos (`tab.php:10`), Saldos (`tab.php:11`), Escrituração (`tab.php:12`), Conta (`tab.php:28`)
- **Ações (`case` do roteador, 6):** `cont001ContentPrincipal`, `cont001ContentCadastroConta`, `cont001FormConta`, `cont001GridContas`, `cont001TabPrincipal`, `cont001TabCadastroConta`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D193` (fotografia:9735): form em `prog/cont/cont001/form.php:28`
- **Classes e métodos chamados:** `CONTABIL` (320 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cont/cont001/form.php:81`)
  - "Registro Incluido com Sucesso!" (`prog/cont/cont001/form.php:101`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

### `prog/cont/cont002` — Lançamentos (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 1.061 linhas. `form.php` (578), `grid.php` (330), `tab.php` (76), `content.php` (69), `grid_func-callback.php` (8)
- **Abas (tab.php):** Lançamentos (`tab.php:14`), Processar Lançamento (`tab.php:30`), Escrituracao (`tab.php:46`), Correlações (`tab.php:50`), Correlação (`tab.php:65`)
- **Ações (`case` do roteador, 15):** `cont002ContentCadastroLancamento`, `cont002ContentProcessarLancamento`, `cont002ContentCadastroEscrituracao`, `cont002ContentCadastroCorrelacao`, `cont002FormLancamento`, `cont002FormProcessarLancamento`, `cont002FormCadastroEscrituracao`, `cont002FormCadastroCorrelacao`, `cont002GridLancamentos`, `cont002GridEscrituracao`, `cont002GridCorrelacoes`, `cont002TabCadastroLancamento`, `cont002TabProcessarLancamento`, `cont002TabCadastroEscrituracao`, `cont002TabCadastroCorrelacao`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D194` (fotografia:9755): form em `prog/cont/cont002/form.php:306`
  - `D195` (fotografia:9776): form em `prog/cont/cont002/form.php:449`
  - `T302` (fotografia:28414): form em `prog/cont/cont002/form.php:30`
- **Tabelas só lidas na tela (1):** `D193` (3×, `prog/cont/cont002/grid.php:28` e mais 2)
- **Classes e métodos chamados:** `CONTABIL` (320 linhas): `processaLancamentos` (`CONTABIL.php:18`)
- **Mensagens e validações visíveis (3):**
  - "Registro Alterado com Sucesso!" (`prog/cont/cont002/form.php:81`)
  - "Registro Incluido com Sucesso!" (`prog/cont/cont002/form.php:101`)
  - "Lançamentos processados com sucesso!" (`prog/cont/cont002/form.php:223`)
- **Grafo (Graphify):** 6 nós, 1 funções definidas: `abrirDocumento()`

### `prog/cont/cont003` — Saldo (rótulo da primeira aba)

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 4 arquivos. 349 linhas. `form.php` (155), `grid.php` (151), `content.php` (22), `tab.php` (21)
- **Abas (tab.php):** Saldo (`tab.php:10`)
- **Ações (`case` do roteador, 4):** `cont003ContentCadastroSaldo`, `cont003FormSaldo`, `cont003GridSaldos`, `cont003TabCadastroSaldo`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `T301` (fotografia:28390): form em `prog/cont/cont003/form.php:28`
- **Tabelas só lidas na tela (1):** `D193` (1×, `prog/cont/cont003/grid.php:20`)
- **Classes e métodos chamados:** `CONTABIL` (320 linhas) (só incluída)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/cont/cont003/form.php:72`)
  - "Registro Incluido com Sucesso!" (`prog/cont/cont003/form.php:92`)
- **Grafo (Graphify):** 4 nós, 0 funções definidas

