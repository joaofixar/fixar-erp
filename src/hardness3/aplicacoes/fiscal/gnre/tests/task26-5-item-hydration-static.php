<?php declare(strict_types=1);
$js=(string)file_get_contents(__DIR__.'/../public/guide-item-hydration.js');$flow=(string)file_get_contents(__DIR__.'/../public/guide-draft-flow.php');$controller=(string)file_get_contents(__DIR__.'/../src/GuideDraftController.php');
foreach(['payment_date','source_document_type','source_document_number','source_document_description','GNRE003_Campos_Extras_JSON','gnre_campos_extras','additional_','guideItemHydrated','guideItemHydrationIncompatible','snapshot vinculado','data-guide-draft-save'] as $needle)if(strpos($js,$needle)===false)throw new RuntimeException('Hidratação persistida ausente: '.$needle);
if(strpos($flow,'guide-item-hydration.js')===false)throw new RuntimeException('Script de hidratação não é carregado pelo formulário.');
if(strpos($controller,"'?edit='.\$guideId.'&tab=resumo'")===false)throw new RuntimeException('Salvar item não retorna ao resumo.');
echo "Task 26.5 item hydration static: OK\n";
