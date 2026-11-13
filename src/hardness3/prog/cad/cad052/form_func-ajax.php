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
			$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
			$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;	
			
			$sql = <<<EOT
			SELECT  *
	   				from D001
	   			left join D049 on D049_D001_Id=D001_Id
	   			left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T003_D024_Id']}'
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   				WHERE D009_Id = '{$D009_Id}'
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
