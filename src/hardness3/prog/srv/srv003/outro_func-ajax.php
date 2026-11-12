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
	
	case 'ImprimirServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->imprimirServico($T147_Id, $D109_Id);
		
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
		
		$sql = "SELECT * FROM T147 WHERE T147_D109_Id='{$row['D109_Id']}' AND T147_Status <> 'C'";
		$result = mysql_query($sql);
		$rowT147 = mysql_fetch_assoc($result);
		
		$sqlConf = "SELECT * FROM T147 WHERE (T147_Flag_Conferido<>'S' AND T147_Status='C')";
		$resultConf = mysql_query($sqlConf);
		$rowConf = mysql_fetch_array($resultConf);
		
		if (isset($row['D109_Id']) && !empty($row['D109_Id'])) {
			$resposta['code'] = true;
			if(mysql_num_rows($resultConf) > 0){
				$resposta['data'] = "/srv/srv003/content/srv003ContentOperacaoAprovar/?oneWindow=true&acaoId=" . urlencode($row['D109_Id']) . '&T147_Id=' . urlencode($rowT147['T147_Id']);
			} else if (($rowT147['T147_Status'] != 'C') && ($rowT147['T147_D109_Id']==$row['D109_Id'])) {
				$resposta['data'] = "/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId=" . urlencode($row['D109_Id']) . '&T147_Id=' . urlencode($rowT147['T147_Id']);
			} else {
				$resposta['data'] = "/srv/srv003/content/srv003ContentOperacaoPendentes/?oneWindow=true&acaoId=" . urlencode($row['D109_Id']) . '&T147_Id=' . urlencode($rowT147['T147_Id']);
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = false;
		}
	break;
	
	case 'aprovarServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		$Usuario = isset($r_Usuario) ? $r_Usuario : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->aprovarServico($T147_Id, $D109_Id, $Usuario);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Serviço Aprovado com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'reprovarServico':
		require_once("bibliotecas/classes/SRV003.php");
		
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		$D109_Id = isset($r_D109_Id) ? $r_D109_Id : false;
		$Usuario = isset($r_Usuario) ? $r_Usuario : false;
		
		$SRV003 = new SRV003();
		
		$retorno = $SRV003->reprovarServico($T147_Id, $D109_Id, $Usuario);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Serviço foi reprovado e retornado para fila de pendentes!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
