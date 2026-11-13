<?php
declare(strict_types=1);
require_once __DIR__.'/../src/BatchTransmissionCandidates.php';
require_once __DIR__.'/../src/GnreRealBatchCohortCancellation.php';
function t67(bool $condition,string $message):void{if(!$condition)throw new RuntimeException('Task 67: '.$message);}
$guide=['GNRE001_C004_Id'=>77,'GNRE001_Status'=>'R','GNRE001_Arquivada'=>'N','GNRE001_GNRE007_Id'=>9,'GNRE001_UF_Favorecida'=>'BA'];
$snapshot=['GNRE007_C004_Id'=>77,'GNRE007_Id'=>9,'GNRE007_UF'=>'BA','GNRE007_Ambiente'=>'2'];
t67(GnreBatchTransmissionCandidates::eligible($guide,$snapshot,77,true),'guia comum permanece elegível');
t67(!GnreBatchTransmissionCandidates::eligible($guide,$snapshot,77,true,0,0,1),'participante de coorte PREPARADO não entra na fila individual');
t67(GnreBatchTransmissionCandidates::eligible($guide,$snapshot,77,true,0,0,0),'participante de coorte CANCELADO volta a ser elegível');
t67(GnreRealBatchCohortCancellation::STATUS_PREPARED==='PREPARADO'&&GnreRealBatchCohortCancellation::STATUS_CANCELLED==='CANCELADO','estados locais explícitos');
$source=(string)file_get_contents(__DIR__.'/../src/GnreRealBatchCohortCancellation.php');
foreach(['mysqli_begin_transaction','C004_Flag_Ativo','GNRE010_Id=? AND GNRE010_C004_Id=? FOR UPDATE','GNRE011_GNRE010_Id=? FOR UPDATE','GNRE004','GNRE005','GNRE008',"GNRE010_Situacao='CANCELADO'"] as $needle)t67(strpos($source,$needle)!==false,'serviço deve conter guarda '.$needle);
t67(strpos($source,'if($status===self::STATUS_CANCELLED)')!==false,'cancelamento repetido é idempotente');
t67(strpos($source,'if($status!==self::STATUS_PREPARED)')!==false,'estado diferente de PREPARADO é bloqueado');
t67(strpos($source,'DELETE FROM GNRE010')===false&&strpos($source,'DELETE FROM GNRE011')===false,'cancelamento preserva cabeçalho e participantes');
$candidateSource=(string)file_get_contents(__DIR__.'/../src/BatchTransmissionCandidates.php');
t67(strpos($candidateSource,"cp.GNRE011_Situacao IN ('PREPARADO'")!==false&&strpos($candidateSource,'cp.GNRE011_GNRE001_Id=g.GNRE001_Id')!==false,'consulta da fila exclui somente a coorte preparada da própria guia');
$index=(string)file_get_contents(__DIR__.'/../public/index.php');
t67(strpos($index,'cancel_real_batch_cohort')!==false&&strpos($index,'gnre_require_csrf()')!==false,'POST de cancelamento exige CSRF');
t67(strpos($index,"c.GNRE010_Situacao='PREPARADO'")!==false,'transmissão individual bloqueia coorte preparada mesmo com seleção obsoleta');
echo "task67-real-batch-cohort-lifecycle-synthetic: OK\n";
