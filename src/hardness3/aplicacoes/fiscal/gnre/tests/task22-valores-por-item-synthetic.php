<?php
declare(strict_types=1);
define('GNRE_MODULE', dirname(__DIR__));
require_once __DIR__.'/../src/ConfigUfRules.php';
require_once __DIR__.'/../src/FormDefinitionBuilder.php';
require_once __DIR__.'/../src/XmlGenerator.php';
function t22(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }
function t22item(array $values):array { return ['GNRE003_Codigo_Receita'=>'100102','GNRE003_Detalhamento_Receita'=>'','GNRE002_Identificacao'=>'','GNRE003_Tipo_Documento_Origem'=>'','GNRE003_Codigo_Produto'=>'','GNRE003_Referencia_Periodo'=>'','GNRE003_Referencia_Mes'=>'','GNRE003_Referencia_Ano'=>'','GNRE003_Referencia_Parcela'=>'','GNRE003_Data_Vencimento'=>'','GNRE003_Convenio'=>'','GNRE003_Destinatario_Identificacao'=>'','GNRE003_Destinatario_Tipo_Identificacao'=>'','GNRE003_Campos_Extras_JSON'=>'','GNRE006'=>$values]; }
$fields=['emitter'=>'N','detail'=>'N','product'=>'N','reference'=>'N','period'=>'N','installment'=>'N','document'=>'N','recipient'=>'N','due_date'=>'N','payment_date'=>'N','agreement'=>'N','fecp'=>'N','extras'=>'N'];
$receipt=['code'=>'100102','description'=>'Sem regra estadual fixa','fields'=>$fields,'types'=>['0'],'unknown'=>[],'details'=>[],'products'=>[],'periods'=>[],'document_types'=>[],'extras'=>[],'value'=>'A'];
$rules=['uf'=>'PR','versions'=>['2.00'],'uf_types'=>['0'],'max'=>[],'address_required'=>['cnpj'=>'N'],'receipts'=>['100102'=>$receipt]];
$builder=new GnreFormDefinitionBuilder();$flat=[];$walk=function($fs)use(&$flat,&$walk){foreach($fs as $f){$flat[$f['id']]=$f;$walk($f['children']??[]);}};$walk($builder->buildItem($rules,'0','100102')['fields']);
t22($flat['value_choice']['state']==='S'&&count($flat['value_choice']['options'])===2,'A expõe escolha única Principal/Total.');
t22(!GnreConfigUfRules::valueErrors('A',[['type'=>'11','value'=>'1000.00']],'N'),'principal exclusivo é válido.');
t22(!GnreConfigUfRules::valueErrors('A',[['type'=>'21','value'=>'1000.00']],'N'),'total exclusivo é válido.');
t22((bool)GnreConfigUfRules::valueErrors('A',[['type'=>'11','value'=>'1'],['type'=>'21','value'=>'1']],'N'),'11 + 21 é bloqueado no servidor.');
t22(!GnreConfigUfRules::valueErrors('A',[['type'=>'11','value'=>'1'],['type'=>'12','value'=>'0.10']],'O'),'FCP correspondente é permitido.');
t22((bool)GnreConfigUfRules::valueErrors('A',[['type'=>'11','value'=>'1'],['type'=>'22','value'=>'0.10']],'O'),'FCP incompatível é bloqueado.');
t22(!GnreConfigUfRules::valueErrors('N',[],'N')&&(bool)GnreConfigUfRules::valueErrors('N',[['type'=>'11','value'=>'1']],'N'),'N não aceita valores.');
$xml=(new GnreXmlGenerator())->generate(['GNRE001_UF_Favorecida'=>'PR','GNRE001_Tipo_GNRE'=>'0','GNRE001_Valor'=>'1000.00','GNRE001_Data_Pagamento'=>''],[t22item([['GNRE006_Tipo_Valor'=>'11','GNRE006_Valor'=>'1000.00']])],$rules,null);
t22(!$xml['errors']&&strpos($xml['xml'],'<valor tipo="11">1000.00</valor>')!==false&&strpos($xml['xml'],'<valor tipo="21">')===false&&strpos($xml['xml'],'<valorGNRE>1000.00</valorGNRE>')!==false,'100102 gera apenas 11 e total 1000.00: '.implode('; ',$xml['errors']));
$ui=(string)file_get_contents(__DIR__.'/../public/guide-form.js');$controller=(string)file_get_contents(__DIR__.'/../src/GuideDraftController.php');t22(strpos($ui,'value_choice')!==false&&strpos($ui,'valores incompatíveis foram preservados para revisão')!==false&&strpos($ui,'guide_draft_incompatible')!==false,'interface preserva e sinaliza valores incompatíveis.');t22(strpos($controller,'GnreConfigUfRules::valueErrors')!==false,'POST reaplica a regra central.');
echo "task22 valores por item: OK\n";
