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
	case 'inserirParcela':
		require_once("bibliotecas/classes/FIN002.php");
		
		$form=$r_fin002FormReparcelamento;		
		$FIN002 = new FIN002();
		$retorno = $FIN002->fin002inserirParcela($form);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;	

	case 'imprimirReparcelamento':
		require_once("bibliotecas/classes/FIN002.php");
		$FIN002 = new FIN002();
		$T196_Id = isset($r_T196_Id) ? $r_T196_Id : false;
		$retorno = $FIN002->montarEmailReparcelamentoPagar($T196_Id);
		echo $retorno['Corpo'];
		die();
	break;

	case 'fin002calcularParcelaSimulacao':
		require_once("bibliotecas/classes/FIN002.php");
		
		$form=$r_fin002FormPagamento;		
		$FIN002 = new FIN002();		
		$retorno = $FIN002->fin002calcularParcelaSimulacao($form);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no cálculo da parcela...';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'alteraConta':
		$D032_Id = isset($r_D032_Id) ? $r_D032_Id : false;
		$resposta['code'] = true;
		$resposta['data']['T015_D014_Id'] = gProcessaSelect(gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta', 'LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '.$D032_Id.' AND D014_Flag_Ativo="S" ORDER BY D014_SubConta', true));
	break;
	
	case 'fin002atualizarSaldoConta':
		$D032_Id = isset($r_D032_Id) ? $r_D032_Id : false;
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

	case 'pesquisaTitulosPagar':
		$T015_Id = isset($r_T015_Id) ? $r_T015_Id : false;
		//Captura o nome cliente
		$T015 = mysql_fetch_array(mysql_query("SELECT * FROM T015 LEFT JOIN D024 ON D024_Id = T015_D024_Id WHERE T015_Id = '{$T015_Id}'"));
		//Pega a data de vencimento mais recente
		$T015_Vencimento = mysql_fetch_array(mysql_query("SELECT * FROM T015 WHERE T015_Nome_Cliente = '{$T015['T015_Nome_Cliente']}' ORDER BY T015_Data_Vencimento DESC LIMIT 1"));
		
		$T015['T015_Data_Emissao'] = date("d/m/Y");
		$T015_Data_Vencimento = explode("-", $T015_Vencimento['T015_Data_Vencimento']);
		$dia = $T015_Data_Vencimento[2];
		$mes = $T015_Data_Vencimento[1];
		$ano = $T015_Data_Vencimento[0];
		if($dia >= date("d")){
			$T015['T015_Data_Vencimento'] = $dia.'/'.date("m").'/'.date("Y");
		} else {
			$dataVencimento = date("Y").'-'.date("m").'-'.$dia;
			$T015['T015_Data_Vencimento'] = date('d/m/Y', strtotime("+1 months", strtotime($dataVencimento)));
		}
		$T015['T015_Valor_Duplicata'] = gCorrigeNumero($T015['T015_Valor_Duplicata']);
		$resposta['code'] = true;
		$resposta['data'] = $T015;
	break;

	case 'fin002BaixaLotePreparar':
		require_once("bibliotecas/classes/FIN002.php");
		$FIN002 = new FIN002();

		$D007_Id   = isset($r_D007_Id) ? $r_D007_Id : false;
		$dataBaixa = isset($r_dataBaixa) ? gCorrigeDataInverte($r_dataBaixa) : false;
		$extra     = isset($r_extra) ? $r_extra : false;

		$retorno = $FIN002->prepararBaixaLoteAjaxDireto($D007_Id, $dataBaixa, $extra);
		if ($retorno['ok'] === true) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['data'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno['erro'];
		}
	break;

	case 'fin002BaixaLoteProcessar':
		require_once("bibliotecas/classes/FIN002.php");
		$FIN002 = new FIN002();

		$D007_Id         = isset($r_D007_Id) ? $r_D007_Id : false;
		$dataBaixa       = isset($r_dataBaixa) ? gCorrigeDataInverte($r_dataBaixa) : false;
		$extra           = isset($r_extra) ? $r_extra : false;
		$T045_Id         = isset($r_T045_Id) ? $r_T045_Id : 0;
		$processadoAtual = isset($r_processadoAtual) ? $r_processadoAtual : 0;
		$snapshotIds     = isset($_REQUEST['snapshotIds']) ? $_REQUEST['snapshotIds'] : array();
		$idsLote         = isset($_REQUEST['idsLote']) ? $_REQUEST['idsLote'] : array();

		$retorno = $FIN002->processarBaixaLoteAjaxDireto($snapshotIds, $idsLote, $D007_Id, $dataBaixa, $extra, $T045_Id, $processadoAtual);
		if ($retorno['ok'] === true) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['data'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = isset($retorno['erro']) ? $retorno['erro'] : 'Erro ao processar lote da baixa.';
		}
	break;
	
}

if (!empty($_REQUEST['callback'])) {
	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
} else {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($resposta);
}
?>
