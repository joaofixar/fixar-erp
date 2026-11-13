<?php
namespace hardness;
include('conf.php');
include('inc.conf.php');
$resposta = array('code' => true, 'data' => array());
 //$resposta['code'] = false;
//$resposta['data'] = "Flag: ".$_REQUEST['D024_Flag_Pessoa_Fisica_Juridica']."<br />cpf: ".$_REQUEST['D024_Cpf']."<br />CNPJ: ".$_REQUEST['D024_Cnpj'];

$valores = array();
foreach ($_REQUEST as $key => $value) {
	$valores[$key] = $value;
}

$erros = '';
foreach ($_REQUEST as $key => $value) {
	$erro = $g['conf']->restricaoIncluirUpdate($key, $value, $valores, false);
	if ($erro !== true) {
		$erros .= $key . ': ' . $erro . "\n"; 
	}
}
if(!empty($erros)){
	$resposta['code'] = false;
	$resposta['data'] = $erros;
} else {
	$resposta['code'] = true;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>