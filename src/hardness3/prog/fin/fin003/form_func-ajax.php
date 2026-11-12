<?php
namespace hardness;
/**
 * Weiberlan
 * 29/06/2012
 * FIN - FIN002
 *
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	
	case 'fin003atualizarSaldoConta':
		$D014_Id = isset($r_D014_Id) ? $r_D014_Id : false;
		$Data = isset($r_Data) ? $r_Data : false;
		$Data = explode("/", $Data);
		$ano = @$Data[2];
		$mes = @$Data[1];
		$T018 = mysql_query("SELECT T018_Valor_Saldo FROM D014 LEFT JOIN T018 ON T018_D014_Id=D014_Id WHERE D014_Id='{$D014_Id}' AND T018_Mes='{$mes}' AND T018_Ano='{$ano}'");
		if(mysql_num_rows($T018)> 0){
			$mT018 = mysql_fetch_array($T018);
			$resposta['code'] = true;
			$resposta['data'] = gCorrigeNumero($mT018['T018_Valor_Saldo']);
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "";
		}
	break;

	case 'alteraConta':
		$D032_Id = isset($r_D032_Id) ? $r_D032_Id : false;
		$resposta['code'] = true;
		$resposta['data']['D014_Id'] = gProcessaSelect(gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta', 'LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '.$D032_Id.' AND D014_Flag_Ativo="S" ORDER BY D014_SubConta', true));
	break;

	case 'processarExtrato':
		$T215_Id = isset($r_T215_Id) ? $r_T215_Id : false;
		require_once("bibliotecas/classes/FIN003.php");
		$FIN003 = new FIN003();
		$retorno = $FIN003->processarExtrato($T215_Id);
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = 'Extrato processado com sucesso!';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'aplicarRegrasExtrato':
		$T215_Id = isset($r_T215_Id) ? $r_T215_Id : false;
		require_once("bibliotecas/classes/FIN003.php");
		$FIN003 = new FIN003();
		$retorno = $FIN003->aplicarRegrasExtrato($T215_Id);
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = 'Regras aplicadas com sucesso!';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'fin003incluirRegraExtrato':
		$D007_Id = isset($r_D007_Id) ? $r_D007_Id : false;
		require_once("bibliotecas/classes/FIN003.php");
		$FIN003 = new FIN003();
		$retorno = $FIN003->incluirRegraExtrato($D007_Id);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'atualizarCamposCliente':
		$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;
		$sql = "SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$resposta['code'] = true;
		$resposta['data'] = $row;
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
