# Task 28 — Central de Guias: listagem operacional, filtros e ações contextuais

**Situação:** concluída em 02/11/2026; aguarda validação visual do usuário. A Visão geral e a Task 29 não foram iniciadas.

## Entrega

Arquivos alterados/criados: `public/index.php`, `public/task10-presentation.php`, `public/task28-guide-center.php`, `src/GuideCenter.php` e `tests/task28-guide-center-synthetic.php`.

`?view=list` passa a ser a central de Guias da empresa GNRE ativa. A consulta é somente leitura e sempre parte de `GNRE001_C004_Id=?`; tentativas, fila e PDFs são carregados apenas para os IDs já autorizados dessa empresa. Não há chamada ao Portal, POST de filtro, nem alteração de registros.

## Classificação e ações

- Emitida exige resultado individual persistido em `GNRE005` com `situacaoGuia=0`. `GNRE001_Status=F` isolado é inconclusivo.
- `situacaoGuia=1/2` é Rejeitada; `3`, ausência/desconhecimento após finalização e `I` são Inconclusiva.
- `P` ou recepção persistida é Aguardando resultado. Uma tentativa `REJEITADA` sem recibo é Recepção rejeitada, separada da rejeição individual.
- As ações são somente links existentes: Abrir para rascunho/emitida/inconclusiva/recepção rejeitada, Consultar resultado para aguardando e Ver resultado para rejeitada. Emitida indica PDF quando `GNRE008` oficial existe.

## Filtros e limites

Filtros GET validados no servidor: situação, UF, receita principal de cabeçalho, vencimento inicial/final, número interno, prefixo de recibo e ambiente do snapshot `GNRE007`. Chips preservam os demais filtros; há Limpar filtros. A Central não exibe coluna Controle: `numeroControle` é opcional no XSD do resultado GNRE, o retorno pode trazer somente `nossoNumero` (identificador interno da UF, que não o substitui) e o PDF pode mostrar um controle que não veio no XML estruturado. Por isso não é informação operacional sempre disponível na listagem e não há busca no SOAP. Faixa de valor foi intencionalmente omitida para não afirmar total fiscal em tipos múltiplos.

Receita e valor exibidos são dados do cabeçalho. Vencimento usa o cabeçalho quando válido; se estiver zerado, usa a única data válida comum aos itens `GNRE003`. Havendo duas ou mais datas de item, a tabela mostra “Múltiplos vencimentos”, sem escolher uma arbitrariamente. “Última movimentação” usa criação/alteração de `GNRE001`, única fonte confiável disponível nesta entrega.

## Testes e validação manual

Executados: lint PHP local e no `hardness-php7`; `task28-guide-center-synthetic.php`; regressão `task26-1-guide-experience-policy-synthetic.php`; `git diff --check`.

Validar manualmente em `?view=list`: alternar empresa GNRE; combinar UF e receita; usar chip mantendo os filtros; testar F sem SOAP de sucesso, resultado 0/1/3 e recepção rejeitada; confirmar PDF disponível somente para emitida com `GNRE008`; conferir rolagem horizontal em celular.

## Pendências para Task 29

A futura Visão geral deve consumir esta mesma classificação, mas ainda precisa definir pendências e atividade consolidada. Número de controle pesquisável, agregação de itens, vencimento por item, total fiscal e uma movimentação cronológica exigem persistência/read model decidido separadamente.
