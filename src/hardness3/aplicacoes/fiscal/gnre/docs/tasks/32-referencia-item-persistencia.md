# Task 32 — Persistência da referência por item

Corrigida em 03/11/2026 a lacuna de mapeamento do formulário progressivo: os campos dinâmicos `reference_month` e `reference_year`, derivados exclusivamente de `TConfigUf/receita/exigePeriodoReferencia`, agora são persistidos em `GNRE003_Referencia_Mes` e `GNRE003_Referencia_Ano` tanto no INSERT quanto no UPDATE.

O contrato auditado para os campos visíveis do `GnreFormDefinitionBuilder` é: campos de item usam GNRE003/GNRE002/GNRE006 ou JSON de extras; dados do emitente são pré-visualização do cadastro da empresa; `payment_date` é GNRE001; e seletores/calculados locais não são dados persistíveis. A auditoria identificou somente mês e ano de referência como campos persistíveis sem mapeamento no fluxo ativo. Não foram criadas regras por BA, UF ou receita.

Na reabertura, `guide-form.js` traduz as duas colunas para os nomes de campo dinâmicos e o POST pendente também as preserva. O `GnreXmlGenerator` já consumia as mesmas colunas e, portanto, passa a emitir `<referencia><mes>` e `<ano>` a partir dos valores efetivamente gravados.

Sem migration: as colunas já existem. Portal, transmissão, consulta, PDF, snapshots e guias reais não foram alterados.

Cobertura: `tests/reference-period-persistence-contract-synthetic.php` verifica INSERT/UPDATE e suas contagens de bind, reabertura/hidratação, preservação em erro e XML com mês/ano. A regressão transacional existente foi atualizada para os 20 placeholders do UPDATE.
