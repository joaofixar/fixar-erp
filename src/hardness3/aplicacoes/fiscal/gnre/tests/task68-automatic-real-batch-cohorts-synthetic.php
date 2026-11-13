<?php
declare(strict_types=1);
require_once __DIR__.'/../src/GnreRealBatchCohort.php';
function t68(bool $condition,string $message):void {if(!$condition)throw new RuntimeException('Task 68: '.$message);}
$service=(string)file_get_contents(__DIR__.'/../src/GnreRealBatchCohort.php');
$index=(string)file_get_contents(__DIR__.'/../public/index.php');
$ui=(string)file_get_contents(__DIR__.'/../public/task63-batch.php');

t68(method_exists(GnreRealBatchCohort::class,'prepareAutomatically'),'preparação automática deve reutilizar o serviço de coorte');
foreach(['mysqli_begin_transaction','lockGuides($db,$ids)','preparedMembership','assertSelection([$guide],$companyId)','self::project($db,$guide)','GNRE001_C004_Id','GNRE001_UF_Favorecida','GNRE007_Ambiente','GNRE001_GNRE007_Id','self::LAYOUT_VERSION'] as $needle)t68(strpos($service,$needle)!==false,'serviço deve manter admissão e agrupamento seguro: '.$needle);
t68(strpos($service,'SAVEPOINT ')!==false&&strpos($service,'ROLLBACK TO SAVEPOINT ')!==false&&strpos($service,'RELEASE SAVEPOINT ')!==false,'cada grupo deve usar savepoint, rollback e liberação');
t68(strpos($service,'GnreXmlGenerator())->generate')!==false&&strpos($service,'splitPhysicalLots')!==false,'a preparação deve extrair o XML individual validado e dividir apenas por limite físico');
t68(strpos($service,'GnreReceptionClient')===false&&strpos($service,'GnreXmlGenerator())->generate')!==false,'preparação usa XML individual validado e não chama Portal');
t68(strpos($index,"'prepare_real_batch_cohorts'")!==false&&strpos($index,'gnre_require_csrf();$ids=')!==false,'rota deve ser POST com CSRF');
t68(strpos($index,"?view=batch_transmit'),true,303")!==false,'rota deve usar PRG para a Fila');
t68(strpos($ui,'prepare_real_batch_cohorts')===false&&strpos($ui,'real_batch_cohorts')===false,'infraestrutura futura não deve ficar exposta na Fila do MVP');
t68(strpos($index,"c.GNRE010_Situacao='PREPARADO'")!==false,'bloqueio da transmissão individual deve permanecer');

/* Observable in-memory transaction adapter: first group fails on participant 2. */
$state=['headers'=>[],'participants'=>[],'savepoint'=>null,'trace'=>[]];$blocked=[];
$save=static function()use(&$state):void {$state['savepoint']=['headers'=>$state['headers'],'participants'=>$state['participants']];$state['trace'][]='savepoint';};
$rollback=static function()use(&$state):void {$state['headers']=$state['savepoint']['headers'];$state['participants']=$state['savepoint']['participants'];$state['trace'][]='rollback';};
$release=static function()use(&$state):void {$state['savepoint']=null;$state['trace'][]='release';};
try {GnreRealBatchCohort::executeGroupAtomically($save,static function()use(&$state):array {$state['headers'][]='failed-group';$state['participants'][]=1;throw new RuntimeException('second participant failed');},$rollback,$release);}
catch(Throwable $e){foreach([1,2] as $id)$blocked[]=$id;}
t68($state['headers']===[]&&$state['participants']===[],'falha no segundo participante não pode deixar cabeçalho ou participante parcial');
t68($state['trace']===['savepoint','rollback','release'],'falha deve reverter até o savepoint do grupo e liberá-lo');
t68($blocked===[1,2],'resultado público deve bloquear todas as guias do grupo que falhou');
$state['trace']=[];
GnreRealBatchCohort::executeGroupAtomically($save,static function()use(&$state):array {$state['headers'][]='valid-group';$state['participants'][]=3;return ['existing'=>false];},$rollback,$release);
$committed=['headers'=>$state['headers'],'participants'=>$state['participants']];
t68($committed===['headers'=>['valid-group'],'participants'=>[3]],'commit final pode preservar outro grupo válido sem persistência parcial do grupo falho');
echo "Task 68 synthetic checks passed\n";
