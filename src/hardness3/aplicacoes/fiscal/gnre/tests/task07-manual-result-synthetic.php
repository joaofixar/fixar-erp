<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/QueueWorker.php';
require_once __DIR__ . '/../src/ManualConsultFeedback.php';

$called = false;
$worker = new GnreQueueWorker(static function (int $companyId, string $receipt) use (&$called): array {
    if ($companyId !== 7 || $receipt !== '26000000042778') throw new RuntimeException('Guia errada foi encaminhada à consulta.');
    $called = true;
    return ['kind'=>'pending','http'=>200,'code'=>'403','description'=>'Pendente','soap'=>'<soap/>','error'=>'','retry_after'=>0];
});
$call = new ReflectionMethod(GnreQueueWorker::class, 'call');
$call->setAccessible(true);
$result = $call->invoke($worker, ['company_id'=>7,'GNRE005_Recibo'=>'26000000042778']);
/* Outro processo pode manter o lock global no ambiente integrado; nesse caso o
 * worker deve reagendar, sem tocar rede nem outra guia. */
if (!$called && $result['kind'] !== 'retry') throw new RuntimeException('Cliente sintético não foi usado nem a consulta foi reagendada com segurança.');
if ($called && $result['kind'] !== 'pending') throw new RuntimeException('Cliente sintético retornou resultado inesperado.');

$workerSource = (string)file_get_contents(__DIR__ . '/../src/QueueWorker.php');
$uiSource = (string)file_get_contents(__DIR__ . '/../public/index.php');
$feedbackSource = (string)file_get_contents(__DIR__ . '/../src/ManualConsultFeedback.php');
foreach (["g.GNRE001_C004_Id='.(int)\$companyId", "g.GNRE001_Id='.(int)\$guideId", "public function consultGuide", "GNRE001_Status='F'", "marcar a guia como F", "\$changed!==1"] as $needle) if (strpos($workerSource, $needle) === false) throw new RuntimeException('Falta garantia da consulta manual: '.$needle);
foreach (['consult_result', 'gnre_consult_flash_set', 'gnre_consult_flash_take', "gnre_back(\$guideId,'&tab=processar')", 'ManualConsultFeedback'] as $needle) if (strpos($uiSource, $needle) === false) throw new RuntimeException('Falta tratamento de interface: '.$needle);
foreach (['pending','final','retry','processing','rescheduled','no_receipt','blocked','not_found','exception'] as $outcome) if (GnreManualConsultFeedback::message($outcome)==='') throw new RuntimeException('Mensagem ausente: '.$outcome);
$scheduled=GnreManualConsultFeedback::action(['GNRE001_Status'=>'P'],['GNRE005_Situacao'=>'PENDENTE','GNRE005_Proxima_Tentativa'=>'2999-01-01 12:34:56'],time());
if ($scheduled['available']) throw new RuntimeException('Consulta reagendada não pode ser habilitada antes de GNRE005_Proxima_Tentativa.');
if ($scheduled['reason']!=='Próxima consulta permitida em 01/01/2999 às 12:34:56.') throw new RuntimeException('A consulta bloqueada deve informar data/hora pública da próxima tentativa.');
foreach (['Consulta já em andamento','Próxima consulta permitida em ','Resultado final já registrado'] as $needle) if (strpos($feedbackSource, $needle) === false) throw new RuntimeException('Falta mensagem pública: '.$needle);
if (strpos($feedbackSource,'Consulta ainda não permitida') !== false) throw new RuntimeException('Mensagem legada ambígua não deve voltar à interface.');
if (strpos($workerSource,'GnrePendingResultPolicy::nextDelaySeconds') === false) throw new RuntimeException('Retorno pendente deve usar a política conservadora persistida.');
echo "Task 07 synthetic checks OK\n";
