<?php
namespace hardness;
	/**
	* VEN - VEN005 - Cadastro de Orçamentos
	*
	* Processamento de ações AJAX
	*/
	$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
