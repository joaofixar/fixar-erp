<?php
namespace hardness;
	/**
	* VEN - VEN013 - Cadastro de Pedidos
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
