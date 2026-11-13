<?php
declare(strict_types=1);

$dateLabel = static function (string $value): string {
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date instanceof DateTime ? $date->format('d/m/Y') : 'Não informada';
};
$taxValue = static function (bool $eligible, $value): string {
    return $eligible && is_numeric($value) && (float) $value > 0
        ? number_format((float) $value, 2, ',', '.')
        : '—';
};
$recipientLabel = static function ($value): string {
    $value = trim((string) $value);
    return $value === '' ? 'Não informado' : mb_strimwidth($value, 0, 36, '…', 'UTF-8');
};
$typeNames = ['0' => 'Guia simples', '1' => 'Múltiplos documentos de origem', '2' => 'Múltiplas receitas'];
?>
<section class="gnre-panel gnre-note-source">
    <header>
        <h2>Gerar guia a partir de notas</h2>
        <p>Selecione NFs elegíveis para conferir e preparar um rascunho separado por ST, DIFAL ou FCP.</p>
    </header>

    <?php if (is_array($task60Flash)): ?>
        <p class="<?=$task60Flash['error'] ? 'error' : 'success'?>" role="alert"><?=gnre_h((string) $task60Flash['message'])?></p>
    <?php endif; ?>

    <form method="get" class="filters">
        <input type="hidden" name="view" value="source_notes">
        <label>Emissão inicial<input type="date" name="issued_from" required value="<?=gnre_h($noteFilters['issued_from'])?>"></label>
        <label>Emissão final<input type="date" name="issued_to" required value="<?=gnre_h($noteFilters['issued_to'])?>"></label>
        <label>Número da NF<input type="text" name="note_number" value="<?=gnre_h($noteFilters['note_number'])?>"></label>
        <label>UF destinatário<input type="text" name="recipient_uf" maxlength="2" value="<?=gnre_h($noteFilters['recipient_uf'])?>"></label>
        <label>Cliente<input type="text" name="recipient_name" value="<?=gnre_h($noteFilters['recipient_name'])?>"></label>
        <div class="form-actions"><button type="submit">Buscar notas</button></div>
    </form>

    <?php if ((string) ($_GET['batch_plan'] ?? '') === '1' && $task60Batch): ?>
        <dialog class="guide-item-modal task60-batch-modal" data-task60-batch-modal open aria-labelledby="task60-batch-title">
            <div class="guide-item-modal-body">
                <section class="gnre-panel">
                <form method="post">
                    <input type="hidden" name="csrf" value="<?=gnre_h($csrf)?>">
                    <input type="hidden" name="action" value="task60_create_batch">
                    <input type="hidden" name="batch_token" value="<?=gnre_h((string) $task60Batch['token'])?>">
                    <div class="guide-item-modal-heading">
                        <h2 id="task60-batch-title">Conferir rascunhos</h2>
                        <button type="submit" class="guide-item-modal-close" name="action" value="task60_cancel_batch" aria-label="Fechar">×</button>
                    </div>
                        <p>A receita, o item, os valores e o vencimento de hoje serão preparados pelo rascunho. Revise os demais dados exigidos pela configuração oficial antes de transmitir.</p>
                    <div class="table-wrap">
                        <table>
                            <tr><th>NF</th><th>UF favorecida</th><th>Tributo</th><th>Valor referência (R$)</th><th>Tipo GNRE</th><th>Situação</th></tr>
                            <?php foreach ((array) $task60Batch['entries'] as $entry): $key = (int) $entry['note_id'].'_'.(string) $entry['tax']; ?>
                                <tr>
                                    <td><?=gnre_h('NF '.($entry['number'] ?? '—').' / Série '.($entry['series'] ?? '—'))?></td>
                                    <td><?=gnre_h((string) ($entry['favored_uf'] ?? '—'))?></td>
                                    <td><?=gnre_h((string) $entry['tax'])?></td>
                                    <td><?=gnre_h($taxValue(true, $entry['value'] ?? 0))?></td>
                                    <td><?php if (($entry['status'] ?? '') === 'ready'): ?><input type="hidden" name="gnre_types[<?=gnre_h($key)?>]" value="0">Guia simples<?php else: ?>—<?php endif; ?></td>
                                    <td><?=gnre_h((string) ($entry['message'] ?: 'Pronto para criar.'))?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <p class="form-actions">
                        <button type="submit">Criar rascunhos conferidos</button>
                        <button type="submit" name="action" value="task60_cancel_batch">Cancelar</button>
                    </p>
                </form>
                </section>
            </div>
        </dialog>
    <?php endif; ?>

    <?php if ((string) ($_GET['batch_result'] ?? '') === '1' && $task60BatchResults): ?>
        <dialog class="guide-item-modal task60-batch-modal" data-task60-result-modal open aria-labelledby="task60-result-title"><div class="guide-item-modal-body"><section class="gnre-panel">
            <div class="guide-item-modal-heading"><h2 id="task60-result-title">Conclusão do lote</h2><a class="guide-item-modal-close" href="<?=gnre_h(gnre_url('?view=source_notes'))?>" aria-label="Fechar">×</a></div>
            <p>As guias são rascunhos: revise os dados exigidos pela configuração oficial antes de transmitir.</p>
            <?php $summary=['created'=>0,'existing'=>0,'blocked'=>0]; foreach($task60BatchResults as $result){$outcome=(string)($result['outcome']??'blocked');$summary[array_key_exists($outcome,$summary)?$outcome:'blocked']++;} ?>
            <p>Criadas: <?=$summary['created']?> · Existentes: <?=$summary['existing']?> · Bloqueadas: <?=$summary['blocked']?></p>
            <div class="table-wrap"><table><tr><th>NF</th><th>Tributo</th><th>Resultado</th><th>Ação</th></tr>
                <?php foreach ($task60BatchResults as $result): $outcome=(string)($result['outcome']??'failure');$labels=['created'=>'Criada','existing'=>'Já existente','blocked'=>'Bloqueada por regra','failure'=>'Falha segura de criação'];$label=$labels[$outcome]??$labels['failure']; ?><tr>
                    <td><?=gnre_h((string) ($result['number'] ?? $result['note_id'] ?? '—'))?></td>
                    <td><?=gnre_h((string) ($result['tax'] ?? ''))?></td>
                    <td><?=gnre_h($outcome==='created'?'Criada com sucesso':$label.'. '.((string)($result['message']??'Sem detalhe adicional.')))?></td>
                    <td><?php if (!empty($result['guide_id'])): ?><a href="<?=gnre_h(gnre_guide_url((int) $result['guide_id'], 'dados'))?>" target="_blank" rel="noopener">Abrir guia</a><?php else: ?>—<?php endif; ?></td>
                </tr><?php endforeach; ?>
            </table></div><p class="form-actions"><a class="button guide-item-modal-cancel" href="<?=gnre_h(gnre_url('?view=source_notes'))?>">Fechar</a></p>
        </section></div></dialog>
    <?php endif; ?>

    <?php if ($noteSearchRequested): ?>
        <p class="optional-mark"><?=count($noteRows)?> nota(s) elegível(is) encontrada(s).</p>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=gnre_h($csrf)?>"><input type="hidden" name="action" value="task60_prepare_batch">
            <p class="form-actions"><button type="button" id="toggle-all-notes">Marcar todas as listadas</button><button>Preparar guias selecionadas</button></p>
            <div class="table-wrap"><table><thead><tr><th>Selecionar</th><th>Número</th><th>Série</th><th>Emissão</th><th>Destinatário</th><th>UF</th><th>Chave de acesso</th><th>DIFAL (R$)</th><th>FCP partilha (R$)</th><th>ST (R$)</th><th>FCP-ST (R$)</th></tr></thead><tbody>
                <?php if (!$noteRows): ?><tr><td colspan="11">Nenhuma NF elegível no período informado.</td></tr><?php endif; ?>
                <?php foreach ($noteRows as $note): $recipientName=(string) ($note['T007_Cliente_Razao'] ?? ''); $recipientUf=strtoupper(trim((string) ($note['T007_Cliente_Estado'] ?? ''))); ?><tr>
                    <td><input type="checkbox" class="note-selection" name="note_ids[]" value="<?=((int) $note['T007_Id'])?>"></td>
                    <td><?=gnre_h((string) ($note['T007_Numero_Nota_Fiscal'] ?: '—'))?></td><td><?=gnre_h((string) ($note['T007_Serie'] ?: '—'))?></td><td><?=gnre_h($dateLabel((string) $note['T007_Data_Emissao']))?></td>
                    <td title="<?=gnre_h($recipientName ?: 'Não informado')?>"><?=gnre_h($recipientLabel($recipientName))?></td><td><?=gnre_h($recipientUf ?: '—')?></td><td><?=gnre_h((string) ($note['T007_Chave_Acesso_Nfe'] ?: 'Não informada'))?></td>
                    <td><?=gnre_h($taxValue((bool) $note['indicator_difal'], $note['T007_Valor_Total_Partilha_ICMS_Destino'] ?? 0))?></td><td><?=gnre_h($taxValue((bool) $note['indicator_fcp'], $note['T007_Valor_Total_FCP_Partilha'] ?? 0))?></td><td><?=gnre_h($taxValue((bool) $note['indicator_st'], $note['T007_Valor_ICMS_Substituicao_Retencao'] ?? 0))?></td><td><?=gnre_h($taxValue((bool) $note['indicator_st'], $note['T007_Valor_FCP_ST'] ?? 0))?></td>
                </tr><?php endforeach; ?>
            </tbody></table></div>
        </form>
    <?php elseif ($noteFilters['issued_from'] !== '' || $noteFilters['issued_to'] !== ''): ?>
        <p class="error">Informe as duas datas de emissão em formato válido.</p>
    <?php else: ?>
        <p class="optional-mark">Informe o período de emissão para consultar as notas elegíveis.</p>
    <?php endif; ?>
</section>
<script defer src="task60-batch-modal.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var button = document.getElementById('toggle-all-notes');
    if (!button) return;
    button.addEventListener('click', function () {
        var notes = [].slice.call(document.querySelectorAll('.note-selection'));
        var selectAll = notes.some(function (note) { return !note.checked; });
        notes.forEach(function (note) { note.checked = selectAll; });
        button.textContent = selectAll ? 'Desmarcar todas as listadas' : 'Marcar todas as listadas';
    });
});
</script>
