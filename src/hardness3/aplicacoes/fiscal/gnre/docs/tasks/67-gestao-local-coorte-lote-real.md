# Task 67 — gestão local da coorte de lote GNRE real

Implementa somente o ciclo local da coorte persistida pela Task 66. O schema já
possuía `GNRE010_Situacao enum('PREPARADO','CANCELADO')`; por isso nenhuma
migration, tabela, coluna ou SQL de alteração foi criado ou executado.

`PREPARADO` é uma coorte local ainda não enviada. `CANCELADO` preserva o
cabeçalho e as participantes para auditoria, mas deixa suas guias novamente
elegíveis às regras normais da fila individual e de uma futura preparação.
`ENVIANDO`, `RECEBIDO` e `CONCLUIDO` ficam apenas como constantes reservadas:
não há operação associada a elas nesta task.

O cancelamento exige empresa GNRE ativa, escopo da empresa e CSRF na rota. Em
uma transação, bloqueia empresa, coorte e participantes com `FOR UPDATE` e
recusa a operação se houver qualquer `GNRE004`, `GNRE005` ou `GNRE008` das
participantes. Uma coorte já cancelada responde de forma idempotente; qualquer
estado que não seja `PREPARADO` é recusado. A operação nunca altera `GNRE001`.

A Fila de transmissão exclui por `NOT EXISTS` apenas participantes de coorte
`PREPARADO`; o mesmo bloqueio é repetido dentro da transação de transmissão
individual para impedir uso de seleção obsoleta. Coortes canceladas não entram
nesse filtro.

Em **Fila de transmissão → Ver coortes locais**, a tela mostra somente a
empresa GNRE ativa, sem fragmentos, hashes, fingerprints, XML ou SOAP. Ela
deixa explícito que o lote não foi enviado e oferece somente **Cancelar lote
preparado** para coortes `PREPARADO`. Não há ação de transmitir lote real.

Roteiro manual local:

1. Abra **Fila de transmissão** e confirme que uma guia de coorte preparada
   não aparece na lista.
2. Abra **Ver coortes locais** e confirme UF, ambiente, situação, participantes
   e a mensagem de que o lote ainda é local.
3. Cancele uma coorte `PREPARADO`; recarregue a tela e confirme `CANCELADO`.
4. Volte à fila e confirme que a guia volta a ser candidata caso satisfaça os
   demais filtros. Reenvie o POST de cancelamento e confirme a mensagem segura
   de coorte já cancelada.

A próxima task só pode iniciar após definir e aprovar o contrato operacional de
envio real, sua auditoria persistida e o tratamento individual do retorno; esta
task não chama Portal, não gera XML operacional e não altera guias reais.
