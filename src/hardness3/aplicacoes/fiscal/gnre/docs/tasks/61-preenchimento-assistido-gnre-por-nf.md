# Task 61 — Preenchimento assistido de GNRE por NF

Para cada componente pendente de uma NF elegível, o lote cria um rascunho atômico com cabeçalho, vínculo de origem, um item e seu valor. ST usa 100099/tipo 11, DIFAL usa 100102/tipo 11 e FCP fora do RJ usa guia própria 100129/tipo 11.

Exceção explícita: para RJ com DIFAL e FCP positivos e elegíveis, o lote cria uma só guia 100102, com DIFAL tipo 11 e FCP tipo 12 no mesmo item. Ela persiste ambos os vínculos (`DIFAL` e `FCP`) em `GNRE002`; o snapshot precisa permitir a receita e FCP. A idempotência permanece por NF + tributo.

Se os dois vínculos RJ existirem na mesma guia, a combinação é exibida como existente. Se só um vínculo existir, ou se os dois apontarem para guias distintas, o lote a exibe bloqueada para revisão manual; não cria nem anexa vínculos automaticamente.

A criação RJ bloqueia a NF da empresa ativa antes de reler a elegibilidade. Em corrida de unicidade, faz rollback e só retorna a guia existente após reler e confirmar que os dois vínculos passaram a apontar para a mesma guia.

A receita precisa constar no snapshot `GNRE007` da UF favorecida e do ambiente operacional. O fluxo assistido aceita somente Guia simples/tipo `0`; tipos `1` e `2` são rejeitados no servidor. Snapshot ausente/ambíguo, receita não permitida ou composição RJ incompatível bloqueia somente a combinação afetada. A data de vencimento recebe a data de São Paulo na criação; outros campos não determináveis ficam para revisão manual.

A listagem consulta somente NFs que ainda tenham ST, DIFAL ou FCP com flag CFOP, total final positivo em `T007` e sem vínculo persistido `GNRE002_T007_Id + GNRE002_Tributo_Origem` na empresa GNRE ativa. Não houve Portal, XML, transmissão, consulta, PDF, financeiro, SQL ou alteração de guia real neste fluxo.

Preenchimento de pagamento, documento/chave, referência e emissão permanece em escopo posterior.
