<?php
namespace hardness;
  /**
 * Weiberlan
 * 25/11/2011
 * VEN - VEN001 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
		
	case 'iniciarServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		$Usuario = isset($r_Usuario) ? $r_Usuario : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->iniciarServico($T147_Id, $D109_Id, $Usuario);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'iniciarServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		$Usuario = isset($r_Usuario) ? $r_Usuario : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->iniciarServico($T147_Id, $D109_Id, $Usuario);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	
	case 'cancelarServicoGeral':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		//$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->cancelarServicoGeral($T147_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'pausarServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		$Usuario = isset($r_Usuario) ? $r_Usuario : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->pausarServico($T147_Id, $D109_Id, $Usuario);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'concluirServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		$Usuario = isset($r_Usuario) ? $r_Usuario : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->concluirServico($T147_Id, $D109_Id, $Usuario);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'cancelarServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		$Usuario = isset($r_Usuario) ? $r_Usuario : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->cancelarServico($T147_Id, $D109_Id, $Usuario);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'reiniciarServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		$Usuario = isset($r_Usuario) ? $r_Usuario : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->reiniciarServico($T147_Id, $D109_Id, $Usuario);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'verificaLogin':
		$senha = isset($r_senha) ? $r_senha : false;
		
		$sql = "SELECT D109_Id, T147_Id FROM D109 left join T147 on T147_D109_Id=D109_Id WHERE D109_Senha = '".mysql_real_escape_string($senha)."';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		
		$sql = "SELECT * FROM T147 WHERE T147_D109_Id='{$row['D109_Id']}' AND T147_Status='E'";
		$result = mysql_query($sql);
		$rowT147 = mysql_fetch_assoc($result);
		
		if (isset($row['D109_Id']) && !empty($row['D109_Id'])) {
			$resposta['code'] = true;
			if ($rowT147['T147_Status'] == 'E') {
				$resposta['data'] = "/srv/srv003/content/srv003ContentExecucaoServico/?oneWindow=true&acaoId=" . urlencode($row['D109_Id']) . '&T147_Id=' . urlencode($rowT147['T147_Id']); 
			} else {
				$resposta['data'] = "/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId=" . urlencode($row['D109_Id']);
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = false;
		}
	break;
	
	case 'atualizarCamposServicos':
		$D112_Id = isset($_REQUEST['D112_Id']) ? $_REQUEST['D112_Id'] : false;
		$D112_Id = mysql_real_escape_string($D112_Id);

		$sql = <<<EOT
			SELECT 
				D112_Titulo,
	   			D112_Descricao,
	   			D112_Tempo_Estimado,
	   			D112_Valor_Unitario,
	   			D112_Id,
	   			D112_D110_Id
	   				from D112
   				WHERE D112_Id = '{$D112_Id}'
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
	
	case 'carregaVeiculo':
			
			global $g;
			
			$T147_D024_Id = isset($r_T147_D024_Id) ? $r_T147_D024_Id : false;
			if (empty($T147_D024_Id)) {
				$resposta['code'] = false;
			} else {			
				$resposta['data']['T147_D116_Id'] = gProcessaSelect(gGeraSelect('D116', 'D116_Id', 'D116_Placa,D116_Modelo,D116_Cor', "WHERE D116_D024_Id='{$r_T147_D024_Id}'"));
			}
			
		break;
	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
