<?php
declare(strict_types=1);
require_once __DIR__.'/../src/ConfigUfRules.php';
$rules=['uf'=>'BA','versions'=>['2.00'],'uf_types'=>['0'],'receipts'=>['100102'=>['unknown'=>[],'types'=>['0'],'fields'=>['reference'=>'S','detail'=>'N','product'=>'N','period'=>'N','installment'=>'N','document'=>'N','recipient'=>'N','due_date'=>'N','payment_date'=>'N','agreement'=>'N','fecp'=>'N','emitter'=>'N','extras'=>'N'],'details'=>[],'products'=>[],'periods'=>[],'document_types'=>[],'extras'=>[],'value'=>'PO']]];
$guide=['GNRE001_UF_Favorecida'=>'BA','GNRE001_Tipo_GNRE'=>'0'];$item=['GNRE003_Codigo_Receita'=>'100102','GNRE003_Detalhamento_Receita'=>'','GNRE003_Codigo_Produto'=>'','GNRE003_Referencia_Periodo'=>'','GNRE003_Referencia_Mes'=>'','GNRE003_Referencia_Ano'=>'','GNRE003_Referencia_Parcela'=>'','GNRE003_Data_Vencimento'=>'','GNRE003_Convenio'=>'','GNRE002_Identificacao'=>'','GNRE006'=>[['GNRE006_Tipo_Valor'=>'11','GNRE006_Valor'=>'1.00']]];
$errors=GnreConfigUfRules::validate($rules,$guide,[$item],null);if(!in_array('Item 1: referência é obrigatório pela UF.',$errors,true))throw new RuntimeException('Referência obrigatória não foi bloqueada.');
$item['GNRE003_Referencia_Mes']='11';$item['GNRE003_Referencia_Ano']='2026';$errors=GnreConfigUfRules::validate($rules,$guide,[$item],null);foreach($errors as $error)if(strpos($error,'referência é obrigatória')!==false)throw new RuntimeException('Referência válida foi bloqueada.');
echo "Task 33 required reference checks OK\n";
