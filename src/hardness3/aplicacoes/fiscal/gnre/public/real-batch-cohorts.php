<?php
declare(strict_types=1);
/** @var list<array<string,mixed>> $realBatchCohorts */
$h='gnre_h';
$date=static function(string $value):string{$d=DateTime::createFromFormat('Y-m-d H:i:s',$value);return $d instanceof DateTime?$d->format('d/m/Y H:i'):'—';};
$environment=static function(string $value):string{return $value==='1'?'Produção':($value==='2'?'Homologação':'—');};
?>
<section class="gnre-panel real-batch-cohorts">
  <header><h2>Coortes de lote real</h2><p>Estes lotes estão preparados somente localmente. Nenhum lote desta tela foi enviado ao Portal GNRE.</p><p><a class="guide-row-action" href="<?=$h(gnre_url('?view=batch_transmit'))?>">Voltar à Fila de transmissão</a></p></header>
  <?php if($realBatchCohortFlash): ?><p class="<?=$realBatchCohortFlash['error']?'error':'success'?>"><?=$h((string)$realBatchCohortFlash['message'])?></p><?php endif; ?>
  <?php if(!$realBatchCohorts): ?><p>Nenhuma coorte local foi preparada para esta empresa.</p>
  <?php else: ?><div class="table-wrap"><table class="guide-table"><thead><tr><th>Coorte</th><th>UF favorecida</th><th>Ambiente</th><th>Situação</th><th>Guias participantes</th><th>Criação</th><th>Ação</th></tr></thead><tbody>
  <?php foreach($realBatchCohorts as $cohort): ?><tr><td>#<?=(int)$cohort['id']?></td><td><?=$h((string)$cohort['uf'])?></td><td><?=$h($environment((string)$cohort['environment']))?></td><td><span class="badge <?=((string)$cohort['status']==='PREPARADO'?'RASCUNHO':'CANCELADO')?>"><?=$h((string)$cohort['status'])?></span></td><td><ul class="real-batch-guides"><?php foreach((array)$cohort['guides'] as $guide): ?><li>#<?=(int)$guide['id']?> — <?=$h((string)$guide['status'])?></li><?php endforeach; ?></ul></td><td><?=$h($date((string)$cohort['created_at']))?></td><td><?php if((string)$cohort['status']==='PREPARADO'): ?><form method="post" action="<?=$h(gnre_url('?view=real_batch_cohorts'))?>" onsubmit="return confirm('Cancelar este lote preparado localmente? Nenhum envio ao Portal será feito.');"><input type="hidden" name="csrf" value="<?=$h($csrf)?>"><input type="hidden" name="action" value="cancel_real_batch_cohort"><input type="hidden" name="cohort_id" value="<?=(int)$cohort['id']?>"><button type="submit">Cancelar lote preparado</button></form><?php else: ?>—<?php endif; ?></td></tr><?php endforeach; ?>
  </tbody></table></div><?php endif; ?>
</section>
<style>.real-batch-cohorts header{margin-bottom:16px}.real-batch-guides{margin:0;padding-left:18px}.badge.CANCELADO{background:#f2f4f7;color:#475467}.success{color:#087443}</style>
