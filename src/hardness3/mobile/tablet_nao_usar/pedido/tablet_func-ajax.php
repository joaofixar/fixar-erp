<?php
namespace hardness;
include('conf.php');

include('inc.conf.php');
// Classes
require_once("bibliotecas/classes/VEN002.php");
$VEN002 = new VEN002();

$resposta = array('code' => true, 'data' => array());
$opcaoAcao = $_REQUEST['opcaoAcao'];
/**
 * Ações
 */

if($opcaoAcao == "verificarParcelas") {
	$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;

	$T089 = mysql_query("SELECT SUM(T089_Valor_Parcela) as TotalParcelas, T003_Valor_Total FROM T089 LEFT JOIN T003 ON T003_Id = T089_T003_Id WHERE T089_T003_Id='{$T003_Id}'");
	$resT089 = mysql_fetch_array($T089);

	$sql="SELECT D006_Flag_Gera_Contas FROM T003 LEFT JOIN D006 on D006_Id=T003_D006_Id WHERE T003_Id='{$T003_Id}'";
	$resultado=mysql_query($sql);
	$row=mysql_fetch_array($resultado);

	if($row['D006_Flag_Gera_Contas'] == "R" && $g['C031']['permitirOrcamentoEmailImpSemPagamento'] == 'N'){
		if($resT089['TotalParcelas'] == 0){
			$erro = "É necessário preencher a forma de pagamento";
		} else {
			if($resT089['T003_Valor_Total'] != $resT089['TotalParcelas']){
				$erro = "Valor das parcelas não confere com o total do orçamento.";
			}
		}
	}

	if(isset($erro)){
		$resposta['code'] = false;
		$resposta['data'] = $erro;
	} else {
		$resposta['code'] = true;
	}
}
if($opcaoAcao == "verificaAlcadaNovo") {
	global $g;
	$msg="";
	$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
	$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : false;

	//Verifica os dados dos itens do pedido
	require_once("bibliotecas/classes/VEN002.php");
	$VEN002 = new VEN002();
	$retornoRestricao = $VEN002->verificaDadosPedido($T005_Id);
    if($retornoRestricao !== true){
         $resposta['code'] = 'erro';
		$resposta['data'] = $retornoRestricao;
		return;
    }

    $T005=mysql_query("SELECT T005_FLag_Status_Liberacao, T005_D024_Id, T005_Flag_Frete, T005_Valor_Frete, ROUND(SUM(T006_Valor_Total_Frete),2) AS FreteProdutos FROM T005 LEFT JOIN T006 ON T006_T005_Id=T005_Id WHERE T005_Id='{$T005_Id}' GROUP BY T005_Id");
	$mT005=mysql_fetch_array($T005);

	//Verifica o frete dos produtos caso obrigatório rateio do frete quando por conta do emitente
	if($mT005['T005_Valor_Frete'] != $mT005['FreteProdutos'] && $g['C031']['ratearFreteVendaNoCusto'] == 'S' && $mT005['T005_Flag_Frete'] == '0'){
		$resposta['code'] = 'erro';
		$resposta['data'] = "A soma do frete dos produtos é diferente do valor do frete no cabeçalho do pedido. <p>Para corrigir clique no botão \"Atualizar\" em seguida a opção \"Atualizar preço tabela\".";
		return;
	}

	require_once("bibliotecas/classes/VEN001.php");
	$VEN001 = new VEN001();

	$retorno = $VEN001->verificaAlcadaNovo(false, $T005_Id, $acao);

	$T005=mysql_query("SELECT T005_FLag_Status_Liberacao, T005_D024_Id FROM T005 WHERE T005_Id='{$T005_Id}'");
	$mT005=mysql_fetch_array($T005);

	if ($mT005['T005_D024_Id'] == $g['C020']['C020_Cupom_Fiscal_Cliente']) {
		$mT005['T005_FLag_Status_Liberacao'] = 1;
	}

	if($mT005['T005_FLag_Status_Liberacao'] == 4){

		$motivo_Liberacao = mysql_query("SELECT T005_Motivo_Pedido_Liberacao FROM T005 WHERE T005_Id='{$T005_Id}'");
		$mMotivo_Liberacao = mysql_fetch_array($motivo_Liberacao);

		if(!empty($mMotivo_Liberacao['T005_Motivo_Pedido_Liberacao'])){
			$msg ="<br />".$mMotivo_Liberacao['T005_Motivo_Pedido_Liberacao'];
		}
		$msg="A liberação deste pedido exige maior alçada, pedir liberação? {$msg}";
	}

	if ($mT005['T005_FLag_Status_Liberacao'] != 4) {

		$resposta['code'] = true;
		$resposta['data'] = "Você tem certeza que deseja gerar o pedido?";

	} else {
		$resposta['code'] = false;
		$resposta['data'] = $msg;
	}
}

if($opcaoAcao == "ven002ImprimirPedido") {
	global $g;

	$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
	$alcadaUpdate = isset($_REQUEST['alcadaUpdate']) ? $_REQUEST['alcadaUpdate'] : false;
	$moeda = isset($_REQUEST['moeda']) ? $_REQUEST['moeda'] : false;

	if (isset($r_gerarHtml)) {
		echo $VEN002->montarEmailPdf($T005_Id, true);
	} else {
		$resposta = array('code' => true, 'data' => array());

		$T090 = mysql_query("SELECT SUM(T090_Valor_Parcela) as TotalParcelas, T005_Valor_Total FROM T090 LEFT JOIN T005 ON T005_Id = T090_T005_Id WHERE T090_T005_Id='{$T005_Id}'");
		$resT090 = mysql_fetch_array($T090);

		$sql="SELECT D006_Flag_Gera_Contas FROM T005 LEFT JOIN D006 on D006_Id=T005_D006_Id WHERE T005_Id='{$T005_Id}'";
		$resultado=mysql_query($sql);
		$row=mysql_fetch_array($resultado);

		if($row['D006_Flag_Gera_Contas'] == "R"){
			if($resT090['TotalParcelas'] == 0){
				$erro = "É necessário preencher a forma de pagamento";
			} else {
				if($resT090['T005_Valor_Total'] != $resT090['TotalParcelas']){
					$erro = "Valor das parcelas não confere com o total do pedido.";
				}
			}
		}
		if(isset($erro)){
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		} else {
			$retorno = $VEN002->montarEmailPdf($T005_Id, false);

			$resposta['code'] = true;
			$resposta['data'] = isset($retorno['AnexosWeb'][0]) ? $retorno['AnexosWeb'][0] : '';
		}

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	}
	die();
}

if($opcaoAcao == "enviarParaAnalise") {
	$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
	$alcadaUpdate = isset($_REQUEST['alcadaUpdate']) ? $_REQUEST['alcadaUpdate'] : false;
	$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : false;

	$retorno = $VEN002->enviarParaAnalise($T005_Id, $alcadaUpdate, $acao);

	if ($retorno === true) {
		$resposta['code'] = true;
		$resposta['data'] = "Pedido enviado para análise";
	} else {
		$resposta['code'] = false;
		$resposta['data'] = $retorno;
	}
}

if($opcaoAcao == "faturarPedido") {
	$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;

	$mensagem = '';
	$retorno = $VEN002->faturarPedido($T005_Id);

	if ($retorno === true) {
		$resposta['code'] = true;
		$resposta['data'] = $mensagem;
	} else {
		// excluir
		$resposta['code'] = false;
		$resposta['data'] = $retorno;
	}
}


echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>