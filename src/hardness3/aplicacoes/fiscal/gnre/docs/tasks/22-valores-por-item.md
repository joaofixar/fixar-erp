# Task 22 — Seleção e validação de valores GNRE por item

Concluída em 02/11/2026, sem consulta ou chamada ao Portal, transmissão, alteração de estado ou criação de `GNRE004`/`GNRE005`. As guias #8, #9 e #13 não foram alteradas.

## Semântica confirmada

O Manual GNRE v2.17, seção 4.3.2 (`TConfigUf.receitas.receita.valorExigido`), define `P` como principal, `T` como total, `A` como principal **ou** total, `N` como nenhum valor informado pelo contribuinte, e `PO`/`TO`/`AO` como variantes opcionais. A interpretação foi centralizada em `GnreConfigUfRules::valueErrors()` e projetada por `GnreFormDefinitionBuilder`; não há condição por receita, UF, PR ou CE.

- `P`/`PO`: somente 11; `T`/`TO`: somente 21.
- `A`/`AO`: 11 e 21 são alternativas exclusivas; a interface apresenta uma escolha única.
- 12 e 22 são FCP complementares de 11 e 21. `exigeValorFecp=N` proíbe; `S` exige o FCP apenas para a base escolhida; `O` permite sem presumir obrigatoriedade.
- `N` não mostra nem aceita valores, e o XML omite `valorGNRE` para guia integralmente calculada pela UF.

## Alterações e validação pela interface

Alterados `src/ConfigUfRules.php`, `src/FormDefinitionBuilder.php`, `src/Task18ProgressiveController.php` e `public/task18-ui.js`. Ao editar um item, escolha “Valor Principal” ou “Valor Total”; a troca remove do POST o campo incompatível e também restringe FCP ao par correspondente. O servidor reconstrói `GNRE006`, recusa combinações incompatíveis e recalcula `GNRE003_Valor`/`GNRE001_Valor` apenas com os valores persistidos. Uma combinação legada 11+21 permanece visível com aviso; XML e transmissão seguem bloqueados pela validação até a escolha válida ser gravada.

O teste sintético `tests/task22-valores-por-item-synthetic.php` cobre principal/total exclusivos, bloqueio 11+21, FCP, `N`, aviso de legado, contrato de POST e o XML de 100102 com somente `<valor tipo="11">1000.00</valor>` e `<valorGNRE>1000.00</valorGNRE>`.
