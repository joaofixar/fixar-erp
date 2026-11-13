<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$candidates=(string)file_get_contents($root.'/src/BatchTransmissionCandidates.php');
$ui=(string)file_get_contents($root.'/public/task63-batch.php');
foreach(["r.GNRE004_Estado='REJEITADA'","COALESCE(r.GNRE004_Recibo,'')=''",'AS _needs_correction'] as $needle)if(strpos($candidates,$needle)===false)throw new RuntimeException('Candidata rejeitada não foi limitada com segurança: '.$needle);
foreach(["g.GNRE001_Status='R' AND (NOT EXISTS",'OR $reopenedFinalHistory))',"g.GNRE001_Status='F' AND \$finalRejection","rh.GNRE005_Situacao='FINALIZADA'"] as $needle)if(strpos($candidates,$needle)===false)throw new RuntimeException('Rascunho reaberto após rejeição final não entra novamente na fila.');
foreach(["cp.GNRE011_GNRE001_Id=g.GNRE001_Id","ap.GNRE011_GNRE001_Id=g.GNRE001_Id"] as $needle)if(strpos($candidates,$needle)===false)throw new RuntimeException('Auditoria de outro lote não pode esconder uma guia individual da fila.');
foreach(['REGEXP', '([[:alnum:]_.-]+:)?situacaoGuia', '[[:space:]]*[12]'] as $needle)if(strpos($candidates,$needle)===false)throw new RuntimeException('Reconhecimento da rejeição final não cobre o SOAP persistido com namespace ou espaços.');
if(strpos($ui,"\$needsCorrection=!empty(\$row['_needs_correction'])")===false)throw new RuntimeException('Fila não diferencia correção de transmissão.');
if(strpos($ui,'if(!$needsReopen): ?><input type="checkbox" name="guide_ids[]"')===false)throw new RuntimeException('Rejeitada elegível precisa poder ser selecionada para nova tentativa.');
if(strpos($ui,'<?=$needsCorrection?\'Corrigir\':\'Abrir\'?>')===false)throw new RuntimeException('Fila não expõe ação Corrigir.');
$index=(string)file_get_contents($root.'/public/index.php');
if(strpos($index,"\$rejectedReception=!empty(\$row['_retransmission_allowed'])")===false)throw new RuntimeException('Servidor não revalida a exceção de rejeição sem recibo.');
echo "Task 63 rejected correction queue checks passed\n";
