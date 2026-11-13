<?php
declare(strict_types=1);

define('GNRE_MODULE', dirname(__DIR__));
require_once __DIR__.'/../src/ConfigUfRules.php';
require_once __DIR__.'/../src/XmlGenerator.php';
if(!class_exists('DOMDocument')){echo "reference period persistence contract: SKIP (DOM ausente)\n";exit;}

function reference_contract(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }

$controller=(string)file_get_contents(__DIR__.'/../src/GuideDraftController.php');
$form=(string)file_get_contents(__DIR__.'/../public/guide-form.js');
$flow=(string)file_get_contents(__DIR__.'/../public/guide-draft-flow.php');
foreach(['GNRE003_Referencia_Mes=NULLIF(?,\'\')','GNRE003_Referencia_Ano=NULLIF(?,\'\')','GNRE003_Referencia_Mes,GNRE003_Referencia_Ano','$month=(string)($_POST[\'reference_month\']??\'\')','$year=(string)($_POST[\'reference_year\']??\'\')',"\$updateTypes='i'.str_repeat('s',17).'ii'","\$insertTypes='ii'.str_repeat('s',17).'i'"] as $needle) reference_contract(strpos($controller,$needle)!==false,'Contrato de persistência ausente: '.$needle);
foreach(["reference_month:'GNRE003_Referencia_Mes'","reference_year:'GNRE003_Referencia_Ano'"] as $needle) reference_contract(strpos($form,$needle)!==false,'Contrato de reabertura ausente: '.$needle);
foreach(["'GNRE003_Referencia_Mes'=>\$pending['reference_month']??''","'GNRE003_Referencia_Ano'=>\$pending['reference_year']??''"] as $needle) reference_contract(strpos($flow,$needle)!==false,'POST pendente não preserva: '.$needle);

$rules=['uf'=>'RJ','versions'=>['2.00'],'uf_types'=>['0'],'max'=>['itensPorGuia'=>'10'],'address_required'=>['cnpj'=>'N'],'receipts'=>['100001'=>['code'=>'100001','fields'=>['emitter'=>'N','detail'=>'N','product'=>'N','reference'=>'S','period'=>'N','installment'=>'N','document'=>'N','recipient'=>'N','due_date'=>'N','payment_date'=>'N','agreement'=>'N','fecp'=>'N','extras'=>'N'],'types'=>['0'],'unknown'=>[],'details'=>[],'products'=>[],'periods'=>[],'document_types'=>[],'extras'=>[],'value'=>'P']]];
$item=['GNRE003_Codigo_Receita'=>'100001','GNRE003_Detalhamento_Receita'=>'','GNRE002_Identificacao'=>'','GNRE003_Tipo_Documento_Origem'=>'','GNRE003_Codigo_Produto'=>'','GNRE003_Referencia_Periodo'=>'','GNRE003_Referencia_Mes'=>'09','GNRE003_Referencia_Ano'=>'2026','GNRE003_Referencia_Parcela'=>'','GNRE003_Data_Vencimento'=>'','GNRE003_Convenio'=>'','GNRE003_Destinatario_Identificacao'=>'','GNRE003_Destinatario_Tipo_Identificacao'=>'','GNRE003_Campos_Extras_JSON'=>'','GNRE006'=>[['GNRE006_Tipo_Valor'=>'11','GNRE006_Valor'=>'10.00']]];
$guide=['GNRE001_UF_Favorecida'=>'RJ','GNRE001_Tipo_GNRE'=>'0','GNRE001_Valor'=>'10.00','GNRE001_Data_Pagamento'=>''];
$result=(new GnreXmlGenerator())->generate($guide,[$item],$rules,null);
reference_contract(!$result['errors'],'XML com referência persistida deve ser válido: '.implode('; ',$result['errors']));
reference_contract(strpos($result['xml'],'<referencia>')!==false&&strpos($result['xml'],'<mes>09</mes>')!==false&&strpos($result['xml'],'<ano>2026</ano>')!==false,'XML não contém mês e ano persistidos.');
echo "reference period persistence contract: OK\n";
