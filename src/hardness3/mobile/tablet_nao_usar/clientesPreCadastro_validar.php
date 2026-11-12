<?php
namespace hardness;
include('conf.php');
include('inc.conf.php');
$resposta = array('code' => true, 'data' => array());
 //$resposta['code'] = false;
//$resposta['data'] = "Flag: ".$_REQUEST['D024_Flag_Pessoa_Fisica_Juridica']."<br />cpf: ".$_REQUEST['D024_Cpf']."<br />CNPJ: ".$_REQUEST['D024_Cnpj'];

if($_REQUEST['D024_Flag_Pessoa_Fisica_Juridica']=="F"){
	if($_REQUEST['D024_Cpf']!=""){
		$validarCPF = mysql_query("SELECT * FROM D024 WHERE D024_Cpf = '{$_REQUEST['D024_Cpf']}'");
		if(mysql_num_rows($validarCPF) > 0){
			$resposta['code'] = false;
			$resposta['data'] = "Este CPF ja esta cadastrado";
		} else {
			$resposta['code'] = true;
		}
	
		/*
		$sql = "SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$_REQUEST['T003_Id']}'";
		$result = mysql_query($sql);
		if (mysql_numrows($result)) {
			$linha = mysql_fetch_array($result);
		} else {
			$linha['T003_D024_Id'] = 0;
		}
		$erro_D024_Cpf = $g['conf']->restricaoIncluirUpdate('D024_Cpf', $_REQUEST['D024_Cpf'], 'N', $linha['T003_D024_Id']);
		if ($erro_D024_Cpf !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $erro_D024_Cpf;
		}
		*/
	}
}
if($_REQUEST['D024_Flag_Pessoa_Fisica_Juridica']=="J"){

	if($_REQUEST['D024_Cnpj']!=""){
		$validarCNPJ = mysql_query("SELECT * FROM D024 WHERE D024_Cnpj = '{$_REQUEST['D024_Cnpj']}'");
		if(mysql_num_rows($validarCNPJ) > 0){
			$resposta['code'] = false;
			$resposta['data'] = "Este CNPJ ja esta cadastrado";
		} else {
			$resposta['code'] = true;
		}
		/*
		$erro_D024_Cnpj = $g['conf']->restricaoIncluirUpdate('D024_Cnpj', $_REQUEST['D024_Cnpj']);
		if ($erro_D024_Cnpj !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $erro_D024_Cnpj;
		}*/
	}
		
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>