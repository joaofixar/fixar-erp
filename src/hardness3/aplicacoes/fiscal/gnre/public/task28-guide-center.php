<?php
declare(strict_types=1);

function task28_render_guide_center(array $filters, array $guides, array $rows, array $counts, bool $archiveColumnAvailable): string
{
    $retentionFlash=$GLOBALS['retentionFlash']??null;
    $h = static function ($value): string { return gnre_h((string)$value); };
    $date = static function ($value, bool $time = false): string {
        $value = trim((string)$value);
        if ($value === '') return '—';
        if ($value === '0000-00-00' || strpos($value, '0000-00-00') === 0) return 'Não informado';
        if (!$time && preg_match('/^\d{4}-\d{2}-\d{2}/', $value, $matches)) $value = $matches[0];
        $parsed = DateTime::createFromFormat($time ? 'Y-m-d H:i:s' : 'Y-m-d', $value);
        return $parsed instanceof DateTime ? $parsed->format($time ? 'd/m/Y H:i' : 'd/m/Y') : $value;
    };
    $due = static function (array $row) use ($date): string {
        $header = trim((string)($row['GNRE001_Data_Vencimento'] ?? ''));
        if ($header !== '' && $header !== '0000-00-00') return $date($header);
        $items = array_values(array_unique(array_filter((array)($row['_item_due_dates'] ?? []), static function ($value): bool { return $value !== '' && $value !== '0000-00-00'; })));
        if (count($items) === 1) return $date($items[0]);
        return count($items) > 1 ? 'Múltiplos vencimentos' : 'Não informado';
    };
    $out = <<<'HTML'
<style>
.guide-filters{grid-template-columns:repeat(5,minmax(0,1fr))}.guide-filters label{min-width:0}@media(max-width:900px){.guide-filters{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.guide-filters{grid-template-columns:1fr}}
.guide-center-heading{display:flex;justify-content:space-between;gap:16px}.guide-center-heading h2{margin-top:0}.guide-filters label{min-width:145px}.guide-filter-actions{display:flex;align-items:center;gap:9px}.guide-filter-actions .guide-filter-submit,.guide-filter-actions .guide-filter-clear{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:36px;padding:8px 12px;border-radius:9px;font-size:12px;font-weight:800;line-height:1;text-decoration:none;transition:background .15s,border-color .15s,transform .15s,box-shadow .15s}.guide-filter-actions .guide-filter-submit{border:1px solid #2860dc;background:#2860dc!important;color:#fff!important;box-shadow:0 3px 8px rgba(40,96,220,.2)}.guide-filter-actions .guide-filter-clear{border:1px solid #cbd9ed;background:#fff;color:#36506f!important}.guide-filter-actions .guide-filter-submit:hover,.guide-filter-actions .guide-filter-clear:hover{transform:translateY(-1px);box-shadow:0 5px 12px rgba(40,96,220,.18)}.guide-filter-actions .guide-filter-submit:hover{background:#184784!important}.guide-filter-actions .guide-filter-clear:hover{border-color:#9eb7dc;background:#f4f8ff}.guide-filter-actions svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}.guide-filter-note,.guide-results-count{width:100%;color:var(--muted);font-size:12px}.guide-table{min-width:960px;width:100%}.guide-table th,.guide-table td{vertical-align:top;white-space:normal}.guide-pdf-mark{display:block;margin-top:5px;color:var(--ok);font-weight:700}.badge.RECEPCAO_REJEITADA{background:#fff1d6;color:#9a6200}.guide-row-action{display:inline-flex;align-items:center;justify-content:center;gap:7px;width:136px;min-height:34px;padding:7px 11px;border:1px solid #b8cffd;border-radius:9px;background:#edf3ff;color:#1554d1!important;font-size:12px;font-weight:800;line-height:1;text-decoration:none;box-shadow:0 1px 1px rgba(14,67,155,.06);transition:background .15s,border-color .15s,transform .15s,box-shadow .15s}.guide-row-action:hover{border-color:#2860dc;background:#2860dc;color:#fff!important;box-shadow:0 5px 12px rgba(40,96,220,.22);transform:translateY(-1px)}.guide-row-action:focus-visible{outline:3px solid #f2a93b;outline-offset:2px}.guide-row-action.is-result{border-color:#f8b9b3;background:#fff0ef;color:#b42318!important}.guide-row-action.is-result:hover{border-color:#d92d20;background:#d92d20;color:#fff!important}.guide-row-action svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}@media(max-width:560px){.guide-center-heading{flex-direction:column}}
</style>
HTML;
    $dialog=is_array($retentionFlash)?'<style>.gnre-retention-feedback{border:0!important;background:transparent!important;padding:0!important;max-width:420px;border-radius:14px;box-shadow:0 20px 56px rgba(13,39,77,.25)}.gnre-retention-feedback::backdrop{background:rgba(15,35,61,.38)}.gnre-retention-feedback .gnre-panel{margin:0;border-radius:14px}.gnre-retention-feedback h2{margin-top:0}</style><dialog class="gnre-retention-feedback"><section class="gnre-panel"><h2>'.(!empty($retentionFlash['error'])?'Não foi possível concluir':'Operação concluída').'</h2><p class="'.(!empty($retentionFlash['error'])?'error':'success').'">'.$h($retentionFlash['message']??'').'</p><form method="dialog" class="form-actions"><button>Entendi</button></form></section></dialog><script>document.addEventListener("DOMContentLoaded",function(){var d=document.querySelector(".gnre-retention-feedback");if(d&&d.showModal)d.showModal();});</script>':'';
    $out .= $dialog.'<section class="gnre-panel guide-center"><div class="guide-center-heading"><div><h2>Guias</h2><p>Consulta e operação das guias da empresa GNRE ativa.</p></div></div><form method="get" class="filters guide-filters"><input type="hidden" name="view" value="list">';
    foreach (['created_date'=>['Data de criação','date','',''],'due_from'=>['Vencimento inicial','date','',''],'due_to'=>['Vencimento final','date','','']] as $name=>$field) $out .= '<label>'.$field[0].'<input name="'.$name.'" type="'.$field[1].'" value="'.$h((string)($filters[$name]??'')).'" placeholder="'.$field[2].'" '.$field[3].'></label>';
    $out .= '<label>Situação<select name="status"><option value="">Todas</option>';
    foreach (['RASCUNHO'=>'Rascunho','AGUARDANDO'=>'Aguardando resultado','EMITIDA'=>'Emitida','REJEITADA'=>'Rejeitada','INCONCLUSIVA'=>'Inconclusiva','RECEPCAO_REJEITADA'=>'Recepção rejeitada'] as $key=>$label) $out .= '<option value="'.$key.'"'.($filters['status']===$key?' selected':'').'>'.$h($label).'</option>';
    $out .= '</select></label>';
    foreach (['uf'=>['UF favorecida','text','Ex.: BA','maxlength="2"'],'revenue'=>['Receita principal','text','','inputmode="numeric" maxlength="6"'],'guide'=>['Número interno','text','','inputmode="numeric"'],'receipt'=>['Recibo','text','','inputmode="numeric" maxlength="20"']] as $name=>$field) $out .= '<label>'.$field[0].'<input name="'.$name.'" type="'.$field[1].'" value="'.$h((string)($filters[$name]??'')).'" placeholder="'.$field[2].'" '.$field[3].'></label>';
    $filterIcon='<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 5h16l-6.4 7.2v5.1l-3.2 1.7v-6.8L4 5Z"/></svg>';
    $clearIcon='<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 4v6h6"/><path d="M5.5 15a7 7 0 1 0 1.2-7.3L4 10"/></svg>';
    $archivedFilter=$archiveColumnAvailable?'<label><input type="checkbox" name="include_archived" value="1"'.(!empty($filters['include_archived'])?' checked':'').'> Incluir arquivadas</label>':'';
    $out .= '<label>Ambiente<select name="environment"><option value="">Todos</option><option value="2"'.($filters['environment']==='2'?' selected':'').'>Homologação</option><option value="1"'.($filters['environment']==='1'?' selected':'').'>Produção</option></select></label>'.$archivedFilter.'<div class="guide-filter-actions"><button class="guide-filter-submit" type="submit">'.$filterIcon.'<span>Filtrar</span></button><a class="guide-filter-clear" href="'.$h(gnre_url('?view=list')).'">'.$clearIcon.'<span>Limpar</span></a></div></form><p class="guide-results-count">'.count($rows).' guia(s) encontrada(s).</p><div class="table-wrap"><table class="guide-table"><thead><tr><th>Número</th><th>Situação</th><th>UF</th><th>Receita principal</th><th>Data de criação</th><th>Vencimento</th><th>Valor informado</th><th>Recibo</th><th>Última movimentação</th><th>Ação</th></tr></thead><tbody>';
    if (!$rows) $out .= '<tr><td colspan="10">Nenhuma guia encontrada para os filtros informados.</td></tr>';
    foreach ($rows as $row) {
        $state=$row['_state']; $receipt=(string)($row['_queue']['GNRE005_Recibo']??($row['_attempts'][0]['GNRE004_Recibo']??'')); $moved=(string)($row['GNRE001_Data_Hora_Alteracao']?:$row['GNRE001_Data_Hora_Criacao']);
        $amount=$row['GNRE001_Valor']!==null&&$row['GNRE001_Valor']!==''?'R$ '.number_format((float)$row['GNRE001_Valor'],2,',','.'):'—';
        $result=$state['action']==='Ver resultado';
        $icon=$result?'<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>':'<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M14 3h7v7"/><path d="M21 3 10 14"/><path d="M20 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5"/></svg>';
        $revenue=(string)($row['_projected_revenue']??GnreGuideCenter::projectedRevenue($row,(array)($row['_item_revenues']??[])));
        $out .= '<tr><td>#'.$h($row['GNRE001_Id']).'</td><td><span class="badge '.$h($state['key']).'">'.$h($state['label']).'</span>'.($state['key']==='EMITIDA'&&(int)$row['_pdf_id']>0?'<small class="guide-pdf-mark">PDF disponível</small>':'').'</td><td>'.$h($row['GNRE001_UF_Favorecida']).'</td><td>'.$h($revenue).'</td><td>'.$h($date((string)$row['GNRE001_Data_Hora_Criacao'])).'</td><td>'.$h($due($row)).'</td><td>'.$h($amount).'</td><td>'.$h($receipt?:'—').'</td><td>'.$h($date($moved,true)).'</td><td><a class="guide-row-action'.($result?' is-result':'').'" href="'.$h(gnre_guide_url((int)$row['GNRE001_Id'],(string)$state['action_tab'])).'">'.$icon.'<span>'.$h($state['action']).'</span></a></td></tr>';
    }
    return $out.'</tbody></table></div></section>';
}
