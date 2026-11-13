# Task 53 — número de controle normalizado da guia autorizada

`GNRE001_Numero_Controle VARCHAR(20) NULL` é a projeção operacional do
`numeroControle` oficial devolvido pelo Portal no resultado individual da guia.
O limite segue o XSD GNRE (`[0-9]{1,20}`). A evidência histórica continua sendo
integralmente o SOAP já persistido em `GNRE005_Ultima_Consulta_SOAP`.

## SQL manual (não executado)

Execute uma única vez no banco local/cliente, em janela apropriada:

```sql
ALTER TABLE GNRE001
  ADD COLUMN GNRE001_Numero_Controle VARCHAR(20) NULL
    COMMENT 'Projeção operacional do numeroControle oficial de resultado individual autorizado',
  ADD UNIQUE KEY UQ_GNRE001_Empresa_Numero_Controle
    (GNRE001_C004_Id, GNRE001_Numero_Controle);
```

O índice é único por empresa GNRE e o MySQL/InnoDB atual aceita várias linhas
com `NULL` em chave única composta; por isso não impede guias sem controle. Não
há backfill: controles eventualmente presentes em SOAP histórico permanecem
somente em `GNRE005` até uma ação futura explicitamente aprovada.

O `CREATE TABLE` distribuído também inclui a coluna e a chave. A aplicação
mantém compatibilidade enquanto o SQL não for aplicado: conclui o resultado
técnico normalmente, mas não tenta acessar a coluna ausente.

## Regra de projeção

No mesmo commit que finaliza a consulta manual ou a fila, o módulo lê apenas o
SOAP retornado naquela consulta e grava a projeção somente quando há exatamente
um resultado individual válido, `situacaoGuia=0`, e `numeroControle` numérico
não vazio de até 20 dígitos. A gravação é limitada à guia e empresa do job e à
transição `P -> F` atual. `COALESCE` preserva um controle já preenchido.

Rejeições (`1`/`2`), pendência (`4`), erro (`3`), timeout, SOAP inválido,
resultado inconclusivo, múltiplas guias ou controle ausente/inválido não
preenchem nem alteram a projeção. O controle não confirma emissão: somente o
resultado individual persistido com `situacaoGuia=0` continua confirmando-a.
GNRE004/GNRE005 não são substituídas, removidas ou reinterpretadas como uma nova
auditoria.

## Interface e limites

A Central não exibe Controle como informação operacional. No XSD do resultado
GNRE, `numeroControle` é opcional; o retorno pode trazer somente `nossoNumero`,
que é identificador interno da UF e não o substitui. O PDF pode exibir um número
de controle mesmo quando o XML estruturado não o retornou. A projeção
`GNRE001_Numero_Controle` permanece disponível somente se o Portal fornecer
`numeroControle` estruturado e válido, nas condições desta task, mas não é lida
pela listagem nem confirma emissão. A Central não extrai valores do SOAP nem
chama o Portal no GET. Não há filtro, busca externa, consulta por controle,
mudança de XML, transmissão, criação de GNRE004, reabertura, PDF, GNRE007,
ambiente ou regra por UF/receita nesta task.

Nenhum `ALTER TABLE`, migration, SQL real, guia real, transmissão, consulta ou
chamada ao Portal foi executado durante a implementação.
