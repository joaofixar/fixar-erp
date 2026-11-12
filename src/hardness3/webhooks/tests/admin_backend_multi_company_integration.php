<?php

declare(strict_types=1);

use HardnessWebhooks\Admin\Context\WebhookAdminContext;
use HardnessWebhooks\Admin\FrameworkEntry;
use HardnessWebhooks\Admin\WebhookAdminCatalogService;
use HardnessWebhooks\Admin\WebhookAdminQueryService;
use HardnessWebhooks\Admin\WebhookAdminRepository;
use HardnessWebhooks\Admin\WebhookAdminService;
use HardnessWebhooks\Catalog\Catalog;
use HardnessWebhooks\Delivery\DestinationValidator;
use HardnessWebhooks\Delivery\DeliveryRepository;
use HardnessWebhooks\Delivery\ValidatedDestination;

require_once dirname(__DIR__) . '/autoload.php';

$configPath = getenv('WEBHOOKS_TEST_ERP_CONFIG');
if (!is_string($configPath) || $configPath === '' || !is_file($configPath)) { fwrite(STDERR, "Task 17 requires WEBHOOKS_TEST_ERP_CONFIG pointing to DEV credentials.\n"); exit(2); }
$confUsuario = array(); require $configPath;
$connection = new mysqli((string)$confUsuario['dbHost'], (string)$confUsuario['dbUser'], (string)$confUsuario['dbPass'], (string)$confUsuario['dbDatabase']);
if ($connection->connect_errno !== 0) throw new RuntimeException('Could not connect to DEV database.');
$connection->set_charset('utf8mb4'); $companyA=987654335; $companyB=987654336;
function t17(bool $value,string $message):void{if(!$value)throw new RuntimeException('Assertion failed: '.$message);}
function t17q(mysqli $db,string $sql,string $types='',array $values=array()):mysqli_stmt{$s=$db->prepare($sql);if($s===false)throw new RuntimeException($db->error);if($types!==''){$arguments=array($types);foreach($values as $i=>$value)$arguments[]=&$values[$i];call_user_func_array(array($s,'bind_param'),$arguments);}if(!$s->execute())throw new RuntimeException($s->error);return $s;}
function t17clean(mysqli $db,int $company):void{t17q($db,'DELETE a FROM WHK005 a INNER JOIN WHK004 d ON d.WHK004_Id=a.WHK005_WHK004_Id WHERE d.WHK004_C004_Id=?','i',array($company))->close();t17q($db,'DELETE FROM WHK004 WHERE WHK004_C004_Id=?','i',array($company))->close();t17q($db,'DELETE FROM WHK003 WHERE WHK003_C004_Id=?','i',array($company))->close();t17q($db,'DELETE FROM WHK002 WHERE WHK002_C004_Id=?','i',array($company))->close();t17q($db,'DELETE v FROM WHK007 v INNER JOIN WHK001 e ON e.WHK001_Id=v.WHK007_WHK001_Id WHERE e.WHK001_C004_Id=?','i',array($company))->close();t17q($db,'DELETE FROM WHK001 WHERE WHK001_C004_Id=?','i',array($company))->close();t17q($db,'DELETE FROM WHK006 WHERE WHK006_C004_Id=?','i',array($company))->close();}
final class Task17DestinationValidator extends DestinationValidator { public function validate(string $url): ValidatedDestination { if(strpos($url,'https://')!==0) throw new \HardnessWebhooks\Delivery\DestinationValidationException('Webhook destination URL is invalid.'); return new ValidatedDestination($url,'task17.example.test',443,array('8.8.8.8')); } }
function t17expect(callable $callable,string $message):void{try{$callable();t17(false,$message);}catch(InvalidArgumentException $exception){}catch(\HardnessWebhooks\Delivery\DestinationValidationException $exception){}}

try {
    t17clean($connection,$companyA); t17clean($connection,$companyB);
    $definitions=array(
        'financial_analysis.requested'=>(new Catalog())->get('financial_analysis.requested'),
        'task17.restricted'=>array('event_type'=>'task17.restricted','label'=>'Restrito','group'=>'Teste','visibility'=>Catalog::VISIBILITY_RESTRICTED,'subscription_available'=>true,'schema_version'=>'1','aggregate_type'=>'order','allowed_filters'=>array(),'payload_validator'=>static function(array $payload):void{}),
        'task17.hidden'=>array('event_type'=>'task17.hidden','label'=>'Oculto','group'=>'Teste','visibility'=>Catalog::VISIBILITY_PUBLIC,'subscription_available'=>false,'schema_version'=>'1','aggregate_type'=>'order','allowed_filters'=>array(),'payload_validator'=>static function(array $payload):void{}),
    );
    $catalog=new WebhookAdminCatalogService(new Catalog($definitions)); $repository=new WebhookAdminRepository(); $service=new WebhookAdminService($repository,$catalog,new Task17DestinationValidator()); $query=new WebhookAdminQueryService($repository,$catalog);
    $a=new WebhookAdminContext(1,1,$companyA); $b=new WebhookAdminContext(2,2,$companyB);
    $id=$service->create($connection,$a,array('name'=>'Task 17 endpoint','url'=>'https://task17.example.test/hook','events'=>array('financial_analysis.requested')));
    $detail=$query->detail($connection,$a,$id); t17($detail!==null && $detail['status']==='I' && !isset($detail['secret']),'creation is inactive and does not expose HMAC state'); t17(count($detail['subscriptions'])===1 && $detail['subscriptions'][0]['status']==='I','creation subscriptions are inactive');
    t17($query->detail($connection,$b,$id)===null && $query->list($connection,$b)===array(),'cross-company detail and list reveal nothing');
    foreach(array(array('name'=>'','url'=>'https://task17.example.test/x','events'=>array()),array('name'=>str_repeat('a',151),'url'=>'https://task17.example.test/x','events'=>array()),array('name'=>'x','url'=>'','events'=>array()),array('name'=>'x','url'=>'http://unsafe.test/x','events'=>array()),array('name'=>'x','url'=>'https://task17.example.test/x','events'=>array('unknown')),array('name'=>'x','url'=>'https://task17.example.test/x','events'=>array('task17.hidden')),array('name'=>'x','url'=>'https://task17.example.test/x','events'=>array('financial_analysis.requested','financial_analysis.requested'))) as $invalid) t17expect(static function()use($service,$connection,$a,$invalid):void{$service->create($connection,$a,$invalid);},'invalid create is rejected');
    t17expect(static function()use($service,$connection,$a):void{$service->create($connection,$a,array('name'=>'x','url'=>'https://task17.example.test/x','events'=>array('task17.restricted')));},'restricted without grant is rejected');
    t17q($connection,"INSERT INTO WHK006 (WHK006_C004_Id,WHK006_Event_Type,WHK006_Status) VALUES (?, 'task17.restricted','A')",'i',array($companyA))->close();
    $restricted=$service->create($connection,$a,array('name'=>'Restricted','url'=>'https://task17.example.test/restricted','events'=>array('task17.restricted')));t17($restricted>0 && count($query->catalog($connection,$a))===2,'restricted event is accepted only with active grant');
    $service->update($connection,$a,$id,array('name'=>'Updated','url'=>'https://task17.example.test/updated','events'=>array('task17.restricted')));$detail=$query->detail($connection,$a,$id);t17($detail['name']==='Updated' && $detail['subscriptions'][0]['selected']===false && $detail['subscriptions'][0]['status']==='I','update changes endpoint and inactivates removed event');
    try{$service->update($connection,$b,$id,array('name'=>'x','url'=>'https://task17.example.test/x','events'=>array()));t17(false,'cross-company update denied');}catch(OutOfBoundsException $exception){}
    $service->activate($connection,$a,$id);t17($query->detail($connection,$a,$id)['status']==='A','activation without HMAC is allowed');$service->deactivate($connection,$a,$id);
    t17q($connection,"INSERT INTO WHK007 (WHK007_WHK001_Id,WHK007_Versao,WHK007_Secret_Criptografado,WHK007_Status) VALUES (?,1,'synthetic-only','A')",'i',array($id))->close();$secret=(int)$connection->insert_id;t17q($connection,'UPDATE WHK001 SET WHK001_WHK007_Id_Atual=? WHERE WHK001_Id=?','ii',array($secret,$id))->close();
    $service->activate($connection,$a,$id);$detail=$query->detail($connection,$a,$id);t17($detail['status']==='A' && !isset($detail['secret']),'activation remains compatible with HMAC without exposing it');
    t17q($connection,"INSERT INTO WHK003 (WHK003_Event_Id,WHK003_C004_Id,WHK003_Type,WHK003_Schema_Version,WHK003_Aggregate_Type,WHK003_Aggregate_Id,WHK003_Payload,WHK003_Occurred_At) VALUES ('00000000-0000-4000-8000-000000001717',?,'task17.restricted','1','order','17','{}',UTC_TIMESTAMP())",'i',array($companyA))->close();$event=(int)$connection->insert_id;t17q($connection,"INSERT INTO WHK004 (WHK004_Delivery_Id,WHK004_C004_Id,WHK004_WHK003_Id,WHK004_WHK001_Id,WHK004_WHK002_Id,WHK004_WHK007_Id,WHK004_Destination_URL,WHK004_Status,WHK004_Next_Attempt_At) SELECT '00000000-0000-4000-8000-000000001718',?,?,?,WHK002_Id,?,'https://task17.example.test/updated','P',UTC_TIMESTAMP() FROM WHK002 WHERE WHK002_WHK001_Id=? AND WHK002_Event_Type='task17.restricted'",'iiiii',array($companyA,$event,$id,$secret,$id))->close();
    t17q($connection,"INSERT INTO WHK004 (WHK004_Delivery_Id,WHK004_C004_Id,WHK004_WHK003_Id,WHK004_WHK001_Id,WHK004_WHK002_Id,WHK004_WHK007_Id,WHK004_Destination_URL,WHK004_Status,WHK004_Next_Attempt_At) SELECT '00000000-0000-4000-8000-000000001720',?,?,?,WHK002_Id,?,'https://task17.example.test/updated','R',UTC_TIMESTAMP() FROM WHK002 WHERE WHK002_WHK001_Id=? AND WHK002_Event_Type='task17.restricted'",'iiiii',array($companyA,$event,$id,$secret,$id))->close();
    $service->deactivate($connection,$a,$id);$statuses=t17q($connection,'SELECT WHK004_Status FROM WHK004 WHERE WHK004_C004_Id=? ORDER BY WHK004_Id','i',array($companyA))->get_result()->fetch_all(MYSQLI_ASSOC);t17(count($statuses)===2 && $statuses[0]['WHK004_Status']==='C' && $statuses[1]['WHK004_Status']==='C' && $query->detail($connection,$a,$id)['status']==='I','deactivation inactivates endpoint and cancels P/R deliveries');
    t17q($connection,"INSERT INTO WHK004 (WHK004_Delivery_Id,WHK004_C004_Id,WHK004_WHK003_Id,WHK004_WHK001_Id,WHK004_WHK002_Id,WHK004_WHK007_Id,WHK004_Destination_URL,WHK004_Status,WHK004_Worker_Id,WHK004_Lease_Until) SELECT '00000000-0000-4000-8000-000000001719',?,?,?,WHK002_Id,?,'https://task17.example.test/updated','L','task17-worker',DATE_ADD(UTC_TIMESTAMP(),INTERVAL 1 MINUTE) FROM WHK002 WHERE WHK002_WHK001_Id=? AND WHK002_Event_Type='task17.restricted'",'iiiii',array($companyA,$event,$id,$secret,$id))->close();$leased=(int)$connection->insert_id;$deliveries=new DeliveryRepository();t17(!$deliveries->isDispatchEligible($connection,$leased,'task17-worker') && $deliveries->cancelClaimed($connection,$leased,'task17-worker','endpoint_disabled'),'leased delivery is revalidated and cancelled before HTTP');
    $_SERVER['REQUEST_METHOD']='GET';$_GET=array('action'=>'detail','id'=>$id,'company_id'=>$companyB);ob_start();FrameworkEntry::read(array('usuarioAtual'=>1,'perfilUsuario'=>1,'empresaAtual'=>$companyA,'c029Ids'=>array(307=>true),'C007'=>array('C007_Id'=>1),'C021'=>array('C021_Id'=>1),'C004'=>array('C004_Id'=>$companyA),'webhooks_mysqli'=>$connection));$response=json_decode((string)ob_get_clean(),true);t17(($response['ok']??true)===false && ($response['message']??'')==='Webhook não encontrado.','selected company cannot access endpoint from another company');
    $_SERVER['REQUEST_METHOD']='GET';$_GET=array('action'=>'list','company_id'=>$companyB);ob_start();FrameworkEntry::read(array('usuarioAtual'=>1,'perfilUsuario'=>1,'empresaAtual'=>$companyA,'c029Ids'=>array(307=>true),'C007'=>array('C007_Id'=>1),'C021'=>array('C021_Id'=>1),'C004'=>array('C004_Id'=>$companyA),'webhooks_mysqli'=>$connection));$response=json_decode((string)ob_get_clean(),true);t17(($response['ok']??true)===false && ($response['message']??'')==='Webhook não encontrado.','inactive company cannot be selected through the administrative boundary');
    echo "Task 17 administrative backend tests passed.\n";
} finally { t17clean($connection,$companyA);t17clean($connection,$companyB);$connection->close(); }

