# Fichas de tela: `prog/gerencial` — Gerencial

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `gerencial/mcp` |  |  | 2 | 86 | 0 | 0 |
| `gerencial/webhooks` |  |  | 2 | 32 | 0 | 0 |

Total: 2 telas. 118 linhas. 0 tabelas gravadas. 0 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/gerencial/mcp`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 2 arquivos. 86 linhas. `outro.php` (68), `content.php` (18)
- **Grafo (Graphify):** 2 nós, 0 funções definidas

### `prog/gerencial/webhooks`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 2 arquivos. 32 linhas. `outro.php` (18), `content.php` (14)
- **Grafo (Graphify):** 2 nós, 0 funções definidas

