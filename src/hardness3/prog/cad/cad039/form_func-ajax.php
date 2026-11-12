<?php
namespace hardness;
	/**
	* VEN - VEN001 - Cadastro de Orçamentos
	*
	* Processamento de ações AJAX
	*/
	$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
		case 'atualizarCamposBens':
			$D106_Id = isset($_REQUEST['D106_Id']) ? $_REQUEST['D106_Id'] : false;
			
			$sql = <<<EOT
			SELECT  * from D106	WHERE D106_Id = '{$D106_Id}'
EOT;

			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);

			if (!empty($row)) {
				$resposta['code'] = true;
				$resposta['data'] = $row;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
   			
			break;
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
