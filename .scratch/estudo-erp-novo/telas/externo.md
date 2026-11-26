# Fichas de tela: `prog/externo` — Externo (cobrança)

Parte do estudo de viabilidade do FX ERP novo (`.scratch/estudo-erp-novo/README.md`). Gerado por leitura do código no commit `64eac937`, sem executar nada. Caminhos relativos a `src/hardness3/`; "fotografia" é `banco/fotografia.sql`.

Como foi feito e limites: as tabelas saem de expressões regulares sobre o PHP (`INSERT`/`UPDATE`/`DELETE`/`REPLACE` = gravada; `FROM`/`JOIN` e `$from` da grade = lida; `$form_tabela` = gravada pelo núcleo do Hardness), filtradas pelas 775 tabelas da fotografia. Linha comentada com `//`, `#` ou `*` no início fica de fora; bloco `/* */` no meio da linha e SQL montada por variável (nome de tabela em `$tabela`) escapam. Classes e métodos vêm de `$CLASSE->metodo(` e `CLASSE::metodo(`; o grafo do Graphify completa com funções e chamadas. Permissão vem do Menu (`bibliotecas/menu.php`) e de `$g['c029Ids'][N]`, com a descrição da C029 na fotografia. Regras e validações automáticas são as mensagens literais (`gGeraAlertMsg`, `dialogConfirm`, `$msg .=`, `alert`). Personalização da Fixar não está aqui.

## Inventário

| Tela | Rótulo | Acesso C029 | Arquivos | Linhas | Gravadas | Lidas |
|---|---|---|---|---|---|---|
| `externo/cobranca` |  |  | 5 | 892 | 0 | 6 |

Total: 1 telas. 892 linhas. 0 tabelas gravadas. 6 tabelas tocadas. `*` = item comentado no Menu.

## Fichas

### `prog/externo/cobranca`

- **Menu:** sem item próprio. Abre a partir de outra tela ou aba (ver "Aberta por").
- **Arquivos:** 5 arquivos. 892 linhas. `verificaBoleto.php` (394), `formulario.php` (268), `atualizarBoleto.php` (90), `gerarBoletoEmpresaProtegido.php` (81), `gerarBoletoEmpresa.php` (59)
- **Ações (`case` do roteador, 3):** `form`, `xevent`, `event`
- **Tabelas só lidas na tela (6):** `T117` (9×, `prog/externo/cobranca/formulario.php:13` e mais 8), `D024` (7×, `prog/externo/cobranca/atualizarBoleto.php:50` e mais 6), `D007` (4×, `prog/externo/cobranca/formulario.php:13` e mais 3), `T002` (4×, `prog/externo/cobranca/formulario.php:13` e mais 3), `C004` (3×, `prog/externo/cobranca/gerarBoletoEmpresa.php:23` e mais 2), `T135` (2×, `prog/externo/cobranca/verificaBoleto.php:300` e mais 1)
- **Classes e métodos chamados:** `FIN005` (1908 linhas): `atualizarVencimentoBoleto` (`FIN005.php:970`); `GFormWrap` (29 linhas) (só incluída)
- **Tabelas gravadas pelos métodos chamados:** `D007` (FIN005::atualizarVencimentoBoleto), `T002` (FIN005::atualizarVencimentoBoleto), `T117` (FIN005::atualizarVencimentoBoleto)
- **Grafo (Graphify):** 12 nós, 7 funções definidas: `dialogConfirm()`, `formataData()`, `formataValor()`, `quantidadeDias()`, `unique()`, `Troca_Data_dma()` …. Chamada por: `.B2BGerarParcelas()` (bibliotecas/classes/B2B001.php), `.cad002demonstrativoCustoTabela()` (bibliotecas/classes/CAD002.php), `.cad002RetornaPrecoCompra()` (bibliotecas/classes/CAD002.php), `.cad002RetornaPrecoCotacao()` (bibliotecas/classes/CAD002.php), `.cad002RetornaPrecoTabela()` (bibliotecas/classes/CAD002.php)

