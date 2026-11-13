# Task 69 — liberação do MVP com Fila de transmissão sequencial

Implementada em 05/11/2026, aguardando validação manual. A Fila de transmissão
é o fluxo operacional do MVP definido na Task 63: permite selecionar uma ou
várias guias, validar e transmitir cada uma sequencialmente. Cada guia preserva
seu XML, tentativa `GNRE004`, recibo, fila `GNRE005`, resultado individual e
PDF do fluxo individual já existente.

A interface mantém filtros, seleção, marcar/desmarcar todas as listadas,
abertura da guia, confirmação e acompanhamento individual. A confirmação deixa
explícito que as guias são processadas uma por vez, com recibo e resultado
próprios. A interrupção impede apenas o início das próximas guias; a guia em
andamento é concluída. Autorização continua condicionada ao resultado
individual persistido com `situacaoGuia=0`.

As estruturas das Tasks 64 a 68 — inclusive `GNRE010`/`GNRE011`, serviços,
rotas e testes — permanecem como infraestrutura para evolução futura. Elas não
são expostas pela interface normal do MVP. Uma participante de `PREPARADO`
continua bloqueada no fluxo individual como proteção técnica.

Não houve alteração de schema, migration, SQL, `gnre_batch_step()`,
`gnre_transmit_atomic()`, GNRE004, GNRE005, GNRE008, XML, consulta, PDF ou do
fluxo individual. Esta implementação não chamou Portal, transmissão, consulta
nem geração de PDF. O lote GNRE real só poderá ser retomado em task própria,
após a liberação do MVP.
