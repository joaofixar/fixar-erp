# Fichas de tela: `prog/bi` — BI

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `bi/bi002` | BI | 247, 152 | 5 | 777 | 2 | 3 |

Total: 1 telas. 777 linhas. 2 tabelas gravadas. 5 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/bi/bi002` — BI

- **Menu:** Configuração › BI, acesso C029 nº 247 "B.I. - Configuração" (`bibliotecas/menu.php:125`)
- **Menu:** Gerencial › BI Link, acesso C029 nº 152 "B.I. - Business Intelligence" (`bibliotecas/menu.php:140`)
- **Arquivos:** 5 arquivos. 777 linhas. `form.php` (306), `grid.php` (253), `tab.php` (90), `content.php` (79), `outro.php` (49)
- **Abas (tab.php):** Links (`tab.php:23`), Perfil (`tab.php:24`), Links (`tab.php:42`), Perfil (`tab.php:59`), Links (`tab.php:76`)
- **Ações (`case` do roteador, 14):** `bi002ContentConfBI`, `bi002ContentCadastroRelatorio`, `bi002ContentCadastroPerfil`, `bi002ContentRelatorios`, `bi002FormCadastroRelatorio`, `bi002FormCadastroPerfil`, `bi002GridConfBI`, `bi002GridConfPerfil`, `bi002GridRelatorios`, `bi002OutroTelaRelatorios`, `bi002TabConfBI`, `bi002TabCadastroRelatorio`, `bi002TabCadastroPerfil`, `bi002TabRelatorios`
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `D176` (fotografia:9339): form em `prog/bi/bi002/form.php:38`
  - `D177` (fotografia:9359): form em `prog/bi/bi002/form.php:178`
- **Tabelas só lidas na tela (3):** `C021` (2×, `prog/bi/bi002/grid.php:116` e mais 1), `C004` (1×, `prog/bi/bi002/grid.php:117`), `C007` (1×, `prog/bi/bi002/grid.php:118`)
- **Mensagens e validações visíveis (2):**
  - "Registro Alterado com Sucesso!" (`prog/bi/bi002/form.php:79`)
  - "Registro Incluido com Sucesso!" (`prog/bi/bi002/form.php:99`)
- **Grafo (Graphify):** 5 nós, 0 funções definidas

