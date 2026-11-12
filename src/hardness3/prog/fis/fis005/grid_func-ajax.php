<?php
namespace hardness;
	/**
	* Processamento de ações AJAX
	*/
	$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
		case 'excluirCep':
					
			$W004_Id = isset($_REQUEST['W004_Id']) ? $_REQUEST['W004_Id'] : false;
			mysql_query("DELETE FROM global.W004 WHERE W004_Id = '{$W004_Id}'");
			$error = mysql_error();

			if (empty($error)){
				$resposta['code'] = true;
				$resposta['data'] = "Cep excluído com sucesso!";
			} else {
				$resposta['code'] = false;
				$resposta['data'] = 'Erro na exclusão do cep: <br>'.$error;
			}
		break;
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>