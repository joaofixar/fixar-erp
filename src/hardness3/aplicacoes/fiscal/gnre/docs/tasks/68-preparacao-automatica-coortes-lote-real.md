# Task 68 — preparação automática de coortes reais pela Fila de transmissão

Implementada localmente em 05/11/2026. A **Fila de transmissão** passou a ter a
ação `Preparar lote real` para as guias selecionadas. A ação é `POST`, protegida
por CSRF e usa PRG; seleção vazia informa o problema sem fazer escrita.

`GnreRealBatchCohort::prepareAutomatically()` abre uma transação, bloqueia a
empresa e cada guia selecionada e reutiliza o contrato de admissão, projeção
`TDadosGNRE`, fingerprint e persistência da Task 66. Ela separa automaticamente
as guias compatíveis por empresa, UF favorecida, ambiente imutável do snapshot,
snapshot `GNRE007` e versão de layout. Não há fallback de UF, ambiente ou
snapshot e não há coorte mista.

Cada grupo compatível possui um `SAVEPOINT` próprio. Depois de criado o
savepoint, cabeçalho e todas as participantes são persistidos como uma unidade:
qualquer falha faz `ROLLBACK TO SAVEPOINT`, libera o savepoint e bloqueia todas
as guias daquele grupo no resumo público, sem impedir os demais grupos válidos.
Assim, uma coorte `PREPARADO` nunca fica parcialmente persistida; a transação
externa somente faz commit após a conclusão de todos os grupos.

Uma guia inválida, arquivada, fora de rascunho, com histórico individual, sem
evidência mínima ou já participante de `PREPARADO` não impede as outras. O
resultado público mostra coortes/guias preparadas, participantes já preparados
e bloqueios por guia com motivo público. Repetições não criam participantes ou
coortes duplicadas; a proteção da Task 67 contra transmissão individual de uma
participante `PREPARADO` permanece ativa. Cancelar uma coorte `PREPARADO`
continua devolvendo somente guias elegíveis à Fila.

Esta task **prepara somente coortes locais**. Ela não gera XML operacional, não
envia nada ao Portal GNRE, não cria `GNRE004`/`GNRE005`, não consulta resultados
e não obtém PDF.

Validação automatizada cobre seleção vazia/rota, coorte homogênea, separação por
chave de compatibilidade, bloqueios isolados, idempotência, isolamento por
empresa, rollback observável do segundo participante de uma coorte, permanência
do cancelamento e bloqueio da transmissão individual.
Validação manual pendente: selecionar guias compatíveis e incompatíveis na Fila,
conferir o resumo e confirmar em **Coortes de lote real** que nada foi enviado.
