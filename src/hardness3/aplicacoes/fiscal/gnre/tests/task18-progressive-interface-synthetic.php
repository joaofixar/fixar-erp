<?php
declare(strict_types=1);
if (!class_exists('DOMDocument')) { echo "guide-draft progressive interface: SKIP (DOM ausente)\n"; exit; }
require_once __DIR__.'/../bootstrap/app.php';
require_once __DIR__.'/../src/ConfigUfRules.php';
require_once __DIR__.'/../src/FormDefinitionBuilder.php';
function t18(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
$xml=(string)file_get_contents(__DIR__.'/fixtures/task18-pr-snapshot-sanitized.xml');
t18(hash('sha256',$xml)==='31bec5940f413f3b07df5337a14d8e9e60c43e87f8d7a85b3083305edbc0c84c','fixture sanitizada foi alterada');
$rules=GnreConfigUfRules::fromXml($xml);$builder=new GnreFormDefinitionBuilder();
t18(($rules['uf_types']??[])===['0'],'PR deve oferecer somente GNRE Simples');
$byId=static function(array $definition):array{$out=[];$walk=function($fs)use(&$out,&$walk){foreach($fs as $f){$out[$f['id']]=$f;$walk($f['children']??[]);}};$walk($definition['fields']);return$out;};
$a=$byId($builder->buildItem($rules,'0','100102'));t18($a['source_document']['state']==='S'&&$a['recipient']['state']==='S'&&$a['due_date']['state']==='S','100102 deve exigir documento, destinatário e vencimento');t18($a['detail']['state']==='N'&&$a['product']['state']==='N'&&$a['assessment_period']['state']==='N'&&$a['reference_period']['state']==='N','100102 não deve expor detalhamento, produto, período ou referência');t18(count($a['source_document_type']['options'])===2,'100102 usa somente tipos documentais oficiais');t18($a['additional_66']['state']==='S'&&$a['additional_66']['meta']['length']==='44','extra 66 obrigatório com 44 caracteres');
$guide100102=$byId($builder->buildGuide($rules,'0',['receipt_codes'=>['100102']]));t18($guide100102['payment_date']['state']==='S','100102 deve exigir data de pagamento no nível da guia');
$b=$byId($builder->buildItem($rules,'0','100110'));t18($b['assessment_period']['state']==='S'&&$b['source_document']['state']==='N'&&$b['recipient']['state']==='N','100110 deve exigir período mensal e ocultar documento/destinatário');
$guide100110=$byId($builder->buildGuide($rules,'0',['receipt_codes'=>['100110']]));t18($guide100110['payment_date']['state']==='S','100110 deve respeitar a regra oficial de pagamento');
$bad=$rules;$bad['receipts']['100102']['fields']['document']='S';$bad['receipts']['100102']['document_types']=[];$u=$byId($builder->buildItem($bad,'0','100102'));t18($u['source_document_type']['state']==='unavailable','lista obrigatória ausente deve bloquear');
$flow=(string)file_get_contents(__DIR__.'/../public/guide-draft-flow.php');$controller=(string)file_get_contents(__DIR__.'/../src/GuideDraftController.php');t18(strpos($flow,'GnreFormDefinitionBuilder')!==false&&strpos($flow,'guide-form.js')===false,'a definição deve ser construída no servidor da interface');t18(strpos($controller,'GnreConfigUfSnapshot::forGuide')!==false&&strpos($controller,'guide_draft_save_item')!==false,'reabertura deve usar snapshot vinculado sem sessão/Portal');t18(strpos($flow,'Inclua itens pelo fluxo oficial existente')===false,'fluxo técnico legado não pode ser apresentado');
echo "guide-draft progressive interface: OK\n";
