# Fichas de tela: `prog/callback` — Callbacks

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `callback/(soltos)` |  |  | 2 | 236 | 1 | 0 |

Total: 1 telas. 236 linhas. 1 tabelas gravadas. 1 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/callback/(soltos)`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 2 arquivos. 236 linhas. `traySMT.php` (142), `tray.php` (94)
- **Tabelas gravadas na tela** (`form` = `$form_tabela`, gravação pelo núcleo):
  - `Z001` (fotografia:29216): INSERT em `prog/callback/tray.php:61`, `prog/callback/traySMT.php:115`
- **Grafo (Graphify):** 9 nós, 7 funções definidas: `cadastrarLoja()`, `getConexaoBD()`, `getStore()`, `processarMensagemRetorno()`, `retornarFormConfirmacao()`, `validarLojaJaCadastrada()` …

