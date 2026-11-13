<?php
declare(strict_types=1);
require_once __DIR__.'/../src/Municipality.php';
require_once __DIR__.'/../src/ConfigUfRules.php';
require_once __DIR__.'/../src/SemanticValidator.php';

$options=GnreMunicipality::optionsFromRows([
 ['D018_UF'=>'CE','D020_UF'=>'','D020_Codigo_IBGE'=>'2304400','D020_Nome_Cidade'=>'Fortaleza'],
 ['D018_UF'=>'CE','D020_UF'=>'','D020_Codigo_IBGE'=>'2304459','D020_Nome_Cidade'=>'Fortim'],
 ['D018_UF'=>'PR','D020_UF'=>'','D020_Codigo_IBGE'=>'4106902','D020_Nome_Cidade'=>'Curitiba'],
]);
if(GnreMunicipality::gnreCode('4106902')!=='06902'||GnreMunicipality::gnreCode('2304400')!=='04400'||GnreMunicipality::gnreCode('2304459')!=='04459')throw new RuntimeException('Derivação GNRE pelos últimos cinco dígitos incorreta.');
if(count($options['cities']['CE'])!==2||$options['cities']['CE'][0]['code']===$options['cities']['CE'][1]['code']||GnreMunicipality::cityUfForCode($options,'2304400')!=='CE'||GnreMunicipality::cityUfForCode($options,'4106902')!=='PR')throw new RuntimeException('Resolução D020 → D018 incorreta.');
if(GnreMunicipality::codeForSearch($options,'Fortaleza','CE')!=='2304400'||GnreMunicipality::codeForSearch($options,'4106902 — Curitiba (PR)','PR')!=='4106902')throw new RuntimeException('Busca textual de cidade incorreta.');
$rules=['receipts'=>['100102'=>['fields'=>['emitter'=>'S','recipient'=>'S']]]];
$validator=new GnreSemanticValidator();
$base=['GNRE003_Codigo_Receita'=>'100102','GNRE003_Destinatario_Identificacao'=>'12345678000199','GNRE003_Destinatario_Razao_Social'=>'','GNRE003_Destinatario_Codigo_Municipio'=>''];
$errors=$validator->validate($rules,['city'=>'06902'],[$base]);
if(count($errors)!==2)throw new RuntimeException('Destinatário sem razão social/município deveria ser apontado.');
$base['GNRE003_Destinatario_Razao_Social']='Destinatário';$base['GNRE003_Destinatario_Codigo_Municipio']='06902';$base['GNRE003_Campos_Extras_JSON']=GnreMunicipality::encodeExtras([],'4106902');
$errors=$validator->validate($rules,['city'=>'06902'],[$base]);
if($errors)throw new RuntimeException('IBGE persistido deveria ser aceito.');
$legacy=$base;$legacy['GNRE003_Campos_Extras_JSON']='';$errors=$validator->validate($rules,['city'=>'06902'],[$legacy]);
if(count($errors)!==1||strpos($errors[0],'compatibilidade')===false)throw new RuntimeException('Legado sem IBGE-7 deveria bloquear.');
$name=trim('  DERENICE  NOVO APARICIO  ');if($name!=='DERENICE  NOVO APARICIO'||trim('   ')!=='')throw new RuntimeException('Normalização de razão social incorreta.');
echo "task19 cadastro semântico: OK\n";
