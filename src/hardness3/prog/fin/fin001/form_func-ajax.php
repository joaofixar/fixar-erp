<?php
namespace hardness;

use Error;

/**
 * Weiberlan
 * 08/11/2011
 * VEN - VEN001 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'inserirParcela':
		require_once("bibliotecas/classes/FIN001.php");
		
		$form=$r_fin001FormPagamento;		
		$FIN001 = new FIN001();		
		$retorno = $FIN001->fin001inserirParcela($form);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;	
	
	case 'alteraConta':
        $D032_Id = isset($r_D032_Id) ? $r_D032_Id : false;
        $resposta['code'] = true;
        $resposta['data']['T002_D014_Id'] = gProcessaSelect(gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta', 'LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '.$D032_Id.' AND D014_Flag_Ativo="S" ORDER BY D014_SubConta', true));
    break;
    
    case 'calculaJuros':
        $novaData = isset($r_novaData) ? $r_novaData : false;
        $novaData = gCorrigeDataInverte($novaData);
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;
        
        //Calcula dias prorrogados
        $SQLdias = mysql_query("SELECT datediff(if(T002_Data_Recebimento='0000-00-00','{$novaData}',T002_Data_Recebimento),T002_Data_Vencimento_Original) as Atraso, T002_Valor_Duplicata FROM T002 WHERE T002_Id='{$acaoId}'");
        $rowSQLdias = mysql_fetch_array($SQLdias);
        $diasProrrogados = $rowSQLdias['Atraso'];
        
        //calcula valor do juros
        $valor = $rowSQLdias['T002_Valor_Duplicata'];
        $taxa = $g['c004']['C004_Taxa_Juros'];
        $valorJuros = gJurosCompostosTitulos($valor, $taxa, $diasProrrogados);
        $valorJuros = gCorrigeNumero($valorJuros - $valor);
        
        
        $resposta['code'] = true;
        $resposta['data']['diasProrrogados'] = $diasProrrogados;
        $resposta['data']['valorJuros'] = $valorJuros;
    break;

	case 'fin001calcularParcelaSimulacao':
		require_once("bibliotecas/classes/FIN001.php");
		
		$form=$r_fin001FormPagamento;		
		$FIN001 = new FIN001();		
		$retorno = $FIN001->fin001calcularParcelaSimulacao($form);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no calculo da parcela...';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;	
	
	case 'removerRemessa':
		require_once("bibliotecas/classes/FIN001.php");
		
		$T002_Id = (isset($r_T002_Id)) ? $r_T002_Id : false; 
			
		$FIN001 = new FIN001();		
		$retorno = $FIN001->fin001removerRemessa($T002_Id);
		
		if ($retorno === true) {			
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;			
		}
	break;		
	
	// case 'salvarObservacao':
		// require_once("bibliotecas/classes/VEN002.php");
// 		
		// $form=$r_ven002FormPagamento;		
		// $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false; 
		// $VEN002 = new VEN002();		
		// $retorno = $VEN002->gravarObservacao($form, $T005_Id);
// 		
		// if ($retorno === false) {			
			// $resposta['code'] = false;
			// $resposta['data'] = 'Falha no calculo da parcela...';			
		// } else {
			// $resposta['code'] = true;
			// $resposta['data'] = $retorno;
		// }
	// break;

	case 'downloadArquivo':
		$dir = $g['pathDados'] . '/tmp/';
		$arquivo = $r_arquivo;
		if (strpos($arquivo, '..') !== false) { die(); }
		header('Content-type: application/x-msdownload');
		header('Content-Disposition: attachment; filename='.basename($arquivo));
		header('Pragma: no-cache');
		header('Expires: 0');
		echo file_get_contents($dir . $arquivo);
		die();
	break;

	case 'calcularValorTroco':
		require_once("bibliotecas/classes/FIN001.php");

		$extra         = base64_decode($r_extra);
		$valorRecebido = $r_valorRecebido;

		$FIN001 = new FIN001();
		$retorno = $FIN001->calcularValorTroco($valorRecebido, $extra);

		if ($retorno === false) {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no calculo do troco.';
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;

	case 'fin001BaixaLoteStart':
		require_once("bibliotecas/classes/FIN001.php");

		$D007_Id         = isset($r_D007_Id) ? $r_D007_Id : false;
		$dataBaixa       = isset($r_dataBaixa) ? gCorrigeDataInverte($r_dataBaixa) : false;
		$dataRecebimento = isset($r_dataRecebimento) ? gCorrigeDataInverte($r_dataRecebimento) : false;
		$extra           = isset($r_extra) ? $r_extra : false;
		$limit           = isset($r_limit) ? $r_limit : 20;

		$FIN001  = new FIN001();
		$retorno = $FIN001->baixarEmLoteAjaxStart($D007_Id, $dataBaixa, $extra, $dataRecebimento, $limit);

		if ($retorno['ok'] === true) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['data'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno['erro'];
		}
	break;

	case 'fin001BaixaLoteRunBatch':
		require_once("bibliotecas/classes/FIN001.php");

		$processToken = isset($r_processToken) ? $r_processToken : false;
		$limit        = isset($r_limit) ? $r_limit : 20;

		$FIN001  = new FIN001();
		$retorno = $FIN001->baixarEmLoteAjaxBatch($processToken, $limit);

		if ($retorno['ok'] === true) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['data'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = isset($retorno['erro']) ? $retorno['erro'] : 'Erro ao processar lote da baixa.';
		}
	break;

	case 'fin001BaixaLotePreparar':
		require_once("bibliotecas/classes/FIN001.php");

		$D007_Id         = isset($r_D007_Id) ? $r_D007_Id : false;
		$dataBaixa       = isset($r_dataBaixa) ? gCorrigeDataInverte($r_dataBaixa) : false;
		$dataRecebimento = isset($r_dataRecebimento) ? gCorrigeDataInverte($r_dataRecebimento) : false;
		$extra           = isset($r_extra) ? $r_extra : false;

		$FIN001  = new FIN001();
		$retorno = $FIN001->prepararBaixaLoteAjaxDireto($D007_Id, $dataBaixa, $extra, $dataRecebimento);

		if ($retorno['ok'] === true) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['data'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno['erro'];
		}
	break;

	case 'fin001BaixaLoteProcessar':
		require_once("bibliotecas/classes/FIN001.php");

		$D007_Id         = isset($r_D007_Id) ? $r_D007_Id : false;
		$dataBaixa       = isset($r_dataBaixa) ? gCorrigeDataInverte($r_dataBaixa) : false;
		$dataRecebimento = isset($r_dataRecebimento) ? gCorrigeDataInverte($r_dataRecebimento) : false;
		$extra           = isset($r_extra) ? $r_extra : false;
		$T045_Id         = isset($r_T045_Id) ? $r_T045_Id : 0;
		$processadoAtual = isset($r_processadoAtual) ? $r_processadoAtual : 0;
		$snapshotIds     = isset($_REQUEST['snapshotIds']) ? $_REQUEST['snapshotIds'] : array();
		$idsLote         = isset($_REQUEST['idsLote']) ? $_REQUEST['idsLote'] : array();

		$FIN001  = new FIN001();
		$retorno = $FIN001->processarBaixaLoteAjaxDireto($snapshotIds, $idsLote, $D007_Id, $dataBaixa, $extra, $dataRecebimento, $T045_Id, $processadoAtual);

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
