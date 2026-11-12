<?php
namespace hardness;
	/*
	* Processamento de ações AJAX
	*/
	$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
		case 'atualizarCamposProdutos':
			$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;
						
			$sql = <<<EOT
			SELECT * from D001 WHERE D001_Id = '{$D001_Id}'
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
