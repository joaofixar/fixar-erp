<?php
declare(strict_types=1);

function t50(bool $condition, string $message): void { if (!$condition) throw new RuntimeException('Task 50: '.$message); }

$root=dirname(__DIR__);
$flow=(string)file_get_contents($root.'/public/guide-draft-flow.php');
$css=(string)file_get_contents($root.'/public/gnre.css');
$layout=(string)file_get_contents($root.'/public/task10-layout.css');
$presentation=(string)file_get_contents($root.'/public/task10-presentation.php');

$multipleStart=strpos($flow,"<?php if(\$type!=='0'&&\$mode==='list'):");
$multiple=(string)substr($flow,$multipleStart);
$single=(string)substr($flow,strpos($flow,'<?php if($singleItemEmpty):'),$multipleStart-strpos($flow,'<?php if($singleItemEmpty):'));

t50(strpos($multiple,'class="form-actions guide-draft-add-item-action"><a class="button" data-guide-item-modal style="display:inline-flex!important;align-items:center!important;justify-content:center!important;min-height:42px!important;padding:0 16px!important;border:0!important;border-radius:9px!important;background:#2860dc!important;color:#fff!important;font-weight:700!important;line-height:1!important;text-decoration:none!important" href="<?=gnre_h(gnre_url(\'?edit=\'.(int)$guide[\'GNRE001_Id\'].\'&new_item=1\'))?>"')!==false,'Inclusão múltipla deve preservar link/modal e aplicar visual primário diretamente na ação.');
t50(strpos($multiple,'task10-layout.css?v=50.2')===false,'A lista não deve depender de link de CSS inserido no corpo para exibir a ação.');
t50(strpos($multiple,"\$type==='1'?'Adicionar documento/item':'Adicionar receita'")!==false,'Tipos 1 e 2 devem manter os respectivos rótulos de inclusão.');
t50(strpos($css,'.guide-draft-item-list .form-actions .button')!==false&&strpos($layout,'.guide-draft-item-list .form-actions>a[data-guide-item-modal][href*="new_item=1"]')!==false&&strpos($layout,'.guide-draft-item-list .form-actions{margin:16px 0 0}')!==false&&strpos($layout,'display:inline-flex!important;align-items:center;justify-content:center;min-height:42px;padding:0 16px!important;border:0!important;border-radius:9px!important;background:var(--blue)!important;color:#fff!important')!==false,'Ação múltipla deve ter aparência de botão primário e espaçamento abaixo da lista.');
t50(strpos($presentation,"str_replace('href=\"task10-layout.css\"")===false,'A apresentação não deve reescrever a URL do CSS por ob_start.');
t50(strpos($single,'<p class="form-actions"><a class="button" data-guide-item-modal')!==false&&strpos($single,'guide-draft-add-item-action')===false,'Ação do tipo 0 deve permanecer inalterada.');
t50(strpos($multiple,"'&new_item=1'")!==false&&strpos($multiple,'data-guide-item-modal')!==false,'Href de fallback e data-guide-item-modal devem permanecer.');
t50(strpos($multiple,'<td><a data-guide-item-modal style="display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:6px!important;min-height:32px!important;padding:0 12px!important;border:1px solid #a9c6ff!important;border-radius:8px!important;background:#edf3ff!important;color:#1554d1!important;font-weight:700!important;line-height:1!important;text-decoration:none!important" href="<?=gnre_h(gnre_url(\'?edit=\'.(int)$guide[\'GNRE001_Id\'].\'&item=\'.(int)$row[\'GNRE003_Id\']))?>"><svg')!==false,'Editar da lista deve seguir o padrão visual compacto sem perder o link/modal.');

echo "Task 50 multiple item add action: OK\n";
