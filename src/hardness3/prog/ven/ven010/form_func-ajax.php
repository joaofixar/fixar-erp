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
		
		case 'carregaVeicEmb':
			$T141_D024_Id = isset($r_T141_D024_Id) ? $r_T141_D024_Id : false;
			$sql = "SELECT D116_D024_Id FROM D116 WHERE D116_D024_Id=" . $T141_D024_Id;
			$result = mysql_query($sql);
			$retorno = mysql_fetch_assoc($result);
			$D024_Id = $retorno['D116_D024_Id'];
			$resposta['code'] = true;
			
			if(empty($D024_Id)){
				$resposta['data']['T141_D116_Id'] = gProcessaSelect(gGeraSelect('D116', 'D116_Id', 'D116_Modelo',' WHERE 1=2', true));
			}else{
				$array = gGeraSelect('D116', 'D116_Id', 'D116_Modelo', 'WHERE D116_D024_Id= '.$D024_Id,true);
				if(!empty($array)){
					$resposta['data']['T141_D116_Id'] = gProcessaSelect($array);
				}else{
					$resposta['data']['T141_D116_Id'] = gProcessaSelect(gGeraSelect('D116', 'D116_Id', 'D116_Modelo','', true));
				}
			}
		break;
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>