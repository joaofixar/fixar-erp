<?php
require_once("conf.php");
require_once("funcoesCriacaoConta.php");

if(!empty($_POST)){
	$form = $_POST;
	//Valida os dados do formulário
	$erros = '';
	$D024 = mysql_query("SELECT * FROM D024 WHERE D024_Cnpj = '{$form['D024_Cnpj']}'", $conNOTA);
	if(mysql_num_rows($D024) > 0){
		$erros .= "Este CNPJ já esta cadastrado em nossa base <br />";
	}
	if(!verificarDominioValido($form['Dominio'])){
		$erros .= "Este endereço já esta em uso, por favor informe outro endereço <br />";
	}
	//Insere os dados do cliente na base (Hardness)
	if($erros != ''){

	}
	echo $erros;
	die();
}