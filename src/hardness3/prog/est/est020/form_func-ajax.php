<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'carrega_Recebedor':
		global $g;
		$T084_D024_Id = isset($r_T084_D024_Destinatario_Id) ? $r_T084_D024_Destinatario_Id : false;

		$recebedor = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id='{$T084_D024_Id}'");
		if (mysql_num_rows($recebedor) > 0) {
			$recebedor = mysql_fetch_assoc($recebedor);
			$recebedor = $recebedor['D148_Id'];
		} else {
			$recebedor = false;
		}

		$array = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T084_D024_Id.' ORDER BY D148_Id', true);
		$array[0]['title'] = 'PADRÃO';
		$resposta['data']['T084A_D024_Recebedor_Id'] = gProcessaSelect($array);
		$resposta['data']['T084A_D024_Recebedor_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T084_D024_Id.' ORDER BY D148_Id', true)));
	break;

	case 'carregaDadosNfeCte':
		$T085_T007_Id = isset($r_T085_T007_Id) ? $r_T085_T007_Id : false;
		$T085_Id = isset($r_T085_Id) ? $r_T085_Id : false;
		$C004_Origem = isset($r_C004_Origem) ? $r_C004_Origem : false;
		
		if (empty($T085_T007_Id)) {
			$resposta['code'] = false;
			$resposta['data']['erro'] = "NF-e não existe";
		} else {			
			/*
				Que a nota não esteja vinculada a nenhum conhecimento
			 */
			$sql = "SELECT T007_Chave_Acesso_Nfe					 	
						FROM T007		   		              
		   					LEFT JOIN T085 ON T085_T007_Id = T007_Id            
							LEFT JOIN T084 ON T085_T084_Id = T084_Id
						WHERE T007_Id = '{$T085_T007_Id}'							
                			AND (T007_Chave_Acesso_Nfe > 0) 
							AND T007_C004_Id = '{$C004_Origem}'							
							AND (T084_Flag_Entrada_Saida IS NULL OR T084_Flag_Entrada_Saida = 'S')
							AND (T084_Flag_Cancelado IS NULL OR T084_Flag_Cancelado != 'N')";
			$result = mysql_query($sql);
			$arrayDataT007 = mysql_fetch_assoc($result);
			
			echo mysql_error();			

			$resposta['data']['T085_Chave_Acesso_Nfe'] = $arrayDataT007['T007_Chave_Acesso_Nfe'];											
		}
	break;	

	case 'alteraEstado':
		$D018_Id = isset($r_D018_Id) ? $r_D018_Id : false;
		$resposta['code'] = true;
		$resposta['D018_Id'] = $D018_Id;
		
		if(!$D018_Id){
			$resposta['data']['D020_Id'] = "<option value=''>...</option>";
		} else {
			$resposta['data']['D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = ' . $D018_Id . ' ORDER BY D020_Nome_Cidade'));			
		}
	break;

	case 'finalizarCTe':
		require_once("bibliotecas/classes/EST020.php");

		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;
		$resposta['T084_Id'] = $T084_Id;
		$EST020 = new EST020();
		
		$retorno = $EST020->finalizarCTe($T084_Id);		
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'finalizarCTeContinuarLiberacao':	
		require_once("bibliotecas/classes/EST020.php");

		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;

		$EST020 = new EST020();

		log("Entrou no continuarLiberacao");
		$retorno = $EST020->continuarLiberacao($T084_Id);
		log("Saiu do continuarLiberacao");
		
		if ($retorno === true) {			
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'habilitarEdicaoCTe':
		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;
		$T084=mysql_query("SELECT T084_Chave_Acesso_Cte from T084 WHERE T084_Id='{$T084_Id}'");
		$mT084=mysql_fetch_array($T084);
		$erro='';
		if(!empty($mT084['T084_Chave_Acesso_Cte'])){
			$erro = 'O CT-e já possui chave de acesso, para habilitar a edição favor acessar a aba "técnico" e apagar a chave de acesso preenchida';
		} else {
			mysql_query("UPDATE T084 SET T084_Flag_Travar = 'N' WHERE T084_Id = '{$T084_Id}'");
			if(mysql_error()){
				$erro = 'Erro ao habilitar o CT-e para edição, por favor entre em contato com o suporte técnico. Erro: ' . mysql_error();
			}
		}

		if(!empty($erro)){
			$resposta['data'] = $erro;
			$resposta['code'] = false;
		} else {
			$resposta['code'] = true;
		}
	break;

	case 'excluirCTe':
		require_once("bibliotecas/classes/EST020.php");
		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;

		$EST020 = new EST020();
		$retorno = $EST020->excluirCTe($T084_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'atualizarAliquotas':
		require_once("bibliotecas/classes/EST020.php");

		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;		
		$EST020 = new EST020();
		
		$retorno = $EST020->atualizarAliquota($T084_Id);		
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>