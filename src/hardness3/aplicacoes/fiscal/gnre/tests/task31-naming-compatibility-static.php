<?php
declare(strict_types=1);
function t31(bool $condition,string $message):void { if(!$condition)throw new RuntimeException($message); }
$root=dirname(__DIR__);
foreach(['src/GuideDraftController.php','src/GuideItemTransaction.php','src/GuideItemEditPolicy.php','public/guide-draft-flow.php','public/guide-readonly-items.php','public/guide-form.js','public/guide-item-hydration.js'] as $file)t31(is_file($root.'/'.$file),'componente canônico ausente: '.$file);
$index=(string)file_get_contents($root.'/public/index.php');$controller=(string)file_get_contents($root.'/src/GuideDraftController.php');$adapter=(string)file_get_contents($root.'/src/LegacyTask18Compatibility.php');
t31(strpos($index,'GuideDraftController.php')!==false&&strpos($index,'GuideItemTransaction.php')!==false&&strpos($index,'GuideItemEditPolicy.php')!==false,'entrada não usa componentes canônicos');
t31(strpos($controller,'gnre_guide_draft_handle')!==false&&strpos($controller,'guide_draft_save_item')!==false&&strpos($controller,'task18_')===false,'controlador ativo depende de nomenclatura de task');
t31(strpos($adapter,'Temporary POST/session adapter')!==false&&strpos($adapter,'task18_')!==false&&strpos($adapter,'guide_draft_')!==false,'adaptador transitório sem justificativa explícita');
echo "task31 naming compatibility static: OK\n";
