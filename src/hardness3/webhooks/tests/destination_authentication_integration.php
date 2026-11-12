<?php

declare(strict_types=1);

use HardnessWebhooks\Admin\Context\WebhookAdminContext;
use HardnessWebhooks\Admin\WebhookAdminCatalogService;
use HardnessWebhooks\Admin\WebhookAdminRepository;
use HardnessWebhooks\Admin\WebhookAdminService;
use HardnessWebhooks\Catalog\Catalog;
use HardnessWebhooks\Delivery\DeliveryRepository;
use HardnessWebhooks\Delivery\DestinationValidator;
use HardnessWebhooks\Delivery\HttpClientInterface;
use HardnessWebhooks\Delivery\HttpResult;
use HardnessWebhooks\Delivery\ValidatedDestination;
use HardnessWebhooks\Delivery\WebhookHttpDispatcher;
use HardnessWebhooks\DestinationAuthentication\DestinationAuthenticationService;
use HardnessWebhooks\Publisher\WebhookPublisher;
use HardnessWebhooks\Secrets\SecretVersionService;
use HardnessWebhooks\Security\HmacVerifier;
use HardnessWebhooks\Security\SecretCipher;

require_once dirname(__DIR__) . '/autoload.php';

$configPath=getenv('WEBHOOKS_TEST_ERP_CONFIG'); if(!is_string($configPath)||$configPath===''||!is_file($configPath)){fwrite(STDERR,"Task 21A requires WEBHOOKS_TEST_ERP_CONFIG.\n");exit(2);} $confUsuario=array(); require $configPath;
$db=new mysqli((string)$confUsuario['dbHost'],(string)$confUsuario['dbUser'],(string)$confUsuario['dbPass'],(string)$confUsuario['dbDatabase']); if($db->connect_errno!==0)throw new RuntimeException('Could not connect to DEV database.'); $db->set_charset('utf8mb4'); $company=987654351;
function t21aI(bool $value,string $message):void{if(!$value)throw new RuntimeException('Assertion failed: '.$message);} function t21aQ(mysqli $db,string $sql,string $types='',array $values=array()):mysqli_stmt{$s=$db->prepare($sql);if($s===false)throw new RuntimeException($db->error);if($types!==''){$args=array($types);foreach($values as $i=>$v)$args[]=&$values[$i];call_user_func_array(array($s,'bind_param'),$args);}if(!$s->execute())throw new RuntimeException($s->error);return $s;} function t21aClean(mysqli $db,int $company):void{t21aQ($db,'DELETE a FROM WHK005 a INNER JOIN WHK004 d ON d.WHK004_Id=a.WHK005_WHK004_Id WHERE d.WHK004_C004_Id=?','i',array($company))->close();t21aQ($db,'DELETE FROM WHK004 WHERE WHK004_C004_Id=?','i',array($company))->close();t21aQ($db,'DELETE FROM WHK003 WHERE WHK003_C004_Id=?','i',array($company))->close();t21aQ($db,'DELETE FROM WHK002 WHERE WHK002_C004_Id=?','i',array($company))->close();t21aQ($db,'DELETE v FROM WHK007 v INNER JOIN WHK001 e ON e.WHK001_Id=v.WHK007_WHK001_Id WHERE e.WHK001_C004_Id=?','i',array($company))->close();t21aQ($db,'DELETE FROM WHK001 WHERE WHK001_C004_Id=?','i',array($company))->close();}
final class Task21aValidator extends DestinationValidator{public function validate(string $url):ValidatedDestination{return new ValidatedDestination($url,parse_url($url,PHP_URL_HOST)?:'task21a.example.test',443,array('8.8.8.8'));}}
final class Task21aClient implements HttpClientInterface{public $calls=array();public function post(ValidatedDestination $destination,string $rawBody,array $headers):HttpResult{$this->calls[$destination->getUrl()]=array('body'=>$rawBody,'headers'=>$headers);return new HttpResult(204,null,1,0);}}

try{
 t21aClean($db,$company);$cipher=new SecretCipher(random_bytes(32));$auth=new DestinationAuthenticationService($cipher);$secrets=new SecretVersionService($cipher);$repository=new WebhookAdminRepository();$catalog=new WebhookAdminCatalogService(new Catalog());$service=new WebhookAdminService($repository,$catalog,new Task21aValidator(),$auth);$context=new WebhookAdminContext(1,1,$company);
 $none=$service->create($db,$context,array('name'=>'Task21A none','url'=>'https://task21a-none.example.test/hook','events'=>array('financial_analysis.requested'),'destination_auth_type'=>'N'));$service->activate($db,$context,$none);
 $token='task21a-bearer.token';$bearer=$service->create($db,$context,array('name'=>'Task21A bearer','url'=>'https://task21a-bearer.example.test/hook','events'=>array('financial_analysis.requested'),'destination_auth_type'=>'B','destination_auth_token'=>$token));$service->activate($db,$context,$bearer);
 $legacy=$service->create($db,$context,array('name'=>'Task21A legacy','url'=>'https://task21a-legacy.example.test/hook','events'=>array('financial_analysis.requested'),'destination_auth_type'=>'N'));$version=$secrets->createVersion($db,$legacy);$service->activate($db,$context,$legacy);
 $publication=(new WebhookPublisher(new Catalog()))->publish($db,$company,'financial_analysis.requested',array('order'=>array('id'=>21,'total'=>1500.90),'customer'=>array('id'=>21,'name'=>'Task 21 customer','document'=>'12345678000190')),array('aggregate_type'=>'order','aggregate_id'=>'task21a'));t21aI($publication->getDeliveryCount()===3,'unsigned, Bearer and legacy HMAC endpoints all receive deliveries');
 $rows=t21aQ($db,'SELECT WHK004_Id,WHK004_WHK001_Id,WHK004_WHK007_Id FROM WHK004 WHERE WHK004_C004_Id=? ORDER BY WHK004_Id','i',array($company))->get_result()->fetch_all(MYSQLI_ASSOC);$byEndpoint=array();foreach($rows as $row)$byEndpoint[(int)$row['WHK004_WHK001_Id']]=$row;t21aI($byEndpoint[$none]['WHK004_WHK007_Id']===null&&$byEndpoint[$bearer]['WHK004_WHK007_Id']===null&&(int)$byEndpoint[$legacy]['WHK004_WHK007_Id']===$version->getId(),'publisher snapshots NULL without HMAC and WHK007 for legacy HMAC');
 $client=new Task21aClient();$deliveries=new DeliveryRepository();$dispatcher=new WebhookHttpDispatcher($deliveries,$secrets,new Task21aValidator(),$client,null,$auth);foreach($rows as $row){$id=(int)$row['WHK004_Id'];t21aI($deliveries->tryClaim($db,$id,'task21a-worker'),'delivery is claimed');$dispatcher->dispatch($db,'task21a-worker',$deliveries->loadClaimed($db,$id,'task21a-worker')?:array());}
 $noneHeaders=$client->calls['https://task21a-none.example.test/hook']['headers'];$bearerHeaders=$client->calls['https://task21a-bearer.example.test/hook']['headers'];$legacyCall=$client->calls['https://task21a-legacy.example.test/hook'];t21aI(!isset($noneHeaders['Authorization'],$noneHeaders['Hardness-Signature'],$noneHeaders['Hardness-Timestamp'])&&isset($noneHeaders['Hardness-Event-Id'],$noneHeaders['Hardness-Delivery-Id'],$noneHeaders['Content-Type']),'Auth N without HMAC sends only independent headers');t21aI(($bearerHeaders['Authorization']??'')==='Bearer '.$token&&!isset($bearerHeaders['Hardness-Signature'],$bearerHeaders['Hardness-Timestamp']),'Bearer works without HMAC');t21aI(isset($legacyCall['headers']['Hardness-Signature'],$legacyCall['headers']['Hardness-Timestamp'])&&HmacVerifier::verify($version->getPlaintextSecret(),(int)$legacyCall['headers']['Hardness-Timestamp'],$legacyCall['body'],$legacyCall['headers']['Hardness-Signature'],(int)$legacyCall['headers']['Hardness-Timestamp']),'legacy HMAC delivery remains signed');
 t21aQ($db,"UPDATE WHK001 SET WHK001_Destination_Auth_Criptografada='{}' WHERE WHK001_Id=?",'i',array($bearer))->close();$corrupt=(new WebhookPublisher(new Catalog()))->publish($db,$company,'financial_analysis.requested',array('order'=>array('id'=>22,'total'=>1500.90),'customer'=>array('id'=>22,'name'=>'Task 21 customer','document'=>'12345678000190')),array('aggregate_type'=>'order','aggregate_id'=>'task21a-corrupt'));$corruptRow=t21aQ($db,'SELECT WHK004_Id FROM WHK004 WHERE WHK004_C004_Id=? AND WHK004_WHK001_Id=? ORDER BY WHK004_Id DESC LIMIT 1','ii',array($company,$bearer))->get_result()->fetch_assoc();$before=count($client->calls);$corruptId=(int)$corruptRow['WHK004_Id'];$deliveries->tryClaim($db,$corruptId,'task21a-corrupt');$dispatcher->dispatch($db,'task21a-corrupt',$deliveries->loadClaimed($db,$corruptId,'task21a-corrupt')?:array());$state=t21aQ($db,'SELECT WHK004_Status FROM WHK004 WHERE WHK004_Id=?','i',array($corruptId))->get_result()->fetch_assoc();t21aI($state['WHK004_Status']==='C'&&count($client->calls)===$before,'corrupt Bearer fails closed before HTTP');
 echo "Task 21A integrated optional HMAC and destination authentication tests passed.\n";
}finally{t21aClean($db,$company);$db->close();}
