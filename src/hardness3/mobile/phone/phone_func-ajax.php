<?php
namespace hardness;
include('conf.php');

include('inc.conf.php');
// Classes
require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

$resposta = array('code' => true, 'data' => array());
$opcaoAcao = $_REQUEST['opcaoAcao'];
/**
 * Ações
 */
 if($opcaoAcao == 'updateT003_Flag_PDF_Impresso'){
 	$sql = "UPDATE T003 SET T003_Flag_PDF_Impresso='S' WHERE T003_Id='{$T003_Id}'";
	mysql_query($sql);
	$resposta['code'] = true;	 
 }
if($opcaoAcao == 'updateT174'){
  	$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
  	$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
  	$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
  	
  	$T174 = mysql_query("select T174_Preco_Venda from T174 where T174_Id='{$T174_Id}'");
  	$mT174=mysql_fetch_array($T174);
  	
  	if ($mT174['T174_Preco_Venda']<=0){
		$resposta['data'] = 'Esta cotação ainda está em andamento.';	 
		$resposta['code'] = false;	 
  	}
  	else
  	{
  	
  		if(!empty($T004_Id)) {
	  		$sqlT174 = "SELECT * FROM T174 
						LEFT JOIN T189 ON T189_Id = T174_T189_Id
						LEFT JOIN T004 ON T004_Id = T189_T004_Id
						LEFT JOIN T173 ON T173_Id = T174_T173_Id
						LEFT JOIN D024 ON D024_Id = T173_D024_Id
						WHERE T004_Id = '{$T004_Id}'";
			$resT174 = mysql_query($sqlT174);
			
			$verCotacao = mysql_query("SELECT T010_Flag_Cotacao_Finalizada
							 FROM T189
							 LEFT JOIN T059 ON T059_T189_Id=T189_Id
							 LEFT JOIN T174 ON T174_T059_Id=T059_Id
							 LEFT JOIN T173 ON T173_Id=T174_T173_Id
							 LEFT JOIN T010 ON T010_Id=T173_T010_Id 
							 WHERE T189_T004_Id = '{$T004_Id}'");
			$resCotacao = mysql_fetch_array($verCotacao);
  		} else if(!empty($T006_Id)) {
	  		$sqlT174 = "SELECT * FROM T174 
						LEFT JOIN T189 ON T189_Id = T174_T189_Id
						LEFT JOIN T006 ON T006_Id = T189_T006_Id
						LEFT JOIN T173 ON T173_Id = T174_T173_Id
						LEFT JOIN D024 ON D024_Id = T173_D024_Id
						WHERE T006_Id = '{$T006_Id}'";
			$resT174 = mysql_query($sqlT174);
			
			$verCotacao = mysql_query("SELECT T010_Flag_Cotacao_Finalizada
							 FROM T189
							 LEFT JOIN T059 ON T059_T189_Id=T189_Id
							 LEFT JOIN T174 ON T174_T059_Id=T059_Id
							 LEFT JOIN T173 ON T173_Id=T174_T173_Id
							 LEFT JOIN T010 ON T010_Id=T173_T010_Id 
							 WHERE T189_T006_Id = '{$T006_Id}'");
			$resCotacao = mysql_fetch_array($verCotacao);
  		}

		if(mysql_num_rows($resT174) > 0) {
			while($res = mysql_fetch_array($resT174)){
				mysql_query("UPDATE T174 SET T174_Flag_Gerar_OC = 'N' WHERE T174_Id = '{$res['T174_Id']}'");
			}
		}
  		
		
		if($resCotacao['T010_Flag_Cotacao_Finalizada'] == 'S'){
  			mysql_query("UPDATE T174 SET T174_Flag_Gerar_OC = 1 WHERE T174_Id = '{$T174_Id}'");
		}
  		
  		$sql = mysql_query("SELECT * FROM T174 LEFT JOIN T059 ON T059_id = T174_T059_Id WHERE T174_Id = '{$T174_Id}'");
  		$res = mysql_fetch_array($sql);
		$resposta['code'] = true;
		$resposta['data'] = $res;
	}
}
if($opcaoAcao == "validaClienteExistente"){
	$cpfCnpj = (isset($_REQUEST['cpfCnpj'])) ? $_REQUEST['cpfCnpj'] : false;
	$valor 	 = (isset($_REQUEST['valor'])) ? $_REQUEST['valor'] : false;
	if($cpfCnpj == "cpf"){
		$D024=mysql_query("SELECT D024_Id, D024_Nome_Empresa from D024 WHERE D024_Cpf='{$valor}'");
		if(mysql_num_rows($D024) > 0){
			$mD024=mysql_fetch_array($D024);
			$resposta['code']=true;
			$resposta['data']=$mD024['D024_Id'];
			$resposta['D024_Nome_Empresa']=$mD024['D024_Nome_Empresa'];
		}else{
			$resposta['code']=false;
		}
	} else if($cpfCnpj == "cnpj"){
		$D024=mysql_query("SELECT D024_Id, D024_Nome_Empresa from D024 WHERE D024_Cnpj='{$valor}'");
		if(mysql_num_rows($D024) > 0){
			$mD024=mysql_fetch_array($D024);
			$resposta['code']=true;
			$resposta['data']=$mD024['D024_Id'];
			$resposta['D024_Nome_Empresa']=$mD024['D024_Nome_Empresa'];
		}else{
			$resposta['code']=false;
		}
	} else {
		$resposta['code']=false;
	}
}

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
	$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
	$gerarPedido = isset($_REQUEST['gerarPedido']) ? $_REQUEST['gerarPedido'] : false;

	// Não efetua verificação se já estiver em análise
	$T003=mysql_query("SELECT T003_Flag_Status_Orcamento, T003_Flag_Frete, T003_Valor_Frete, ROUND(SUM(T004_Valor_Frete_Total),2) as FreteProdutos FROM T003 LEFT JOIN T004 ON T004_T003_Id=T003_Id WHERE T003_Id='{$T003_Id}' GROUP BY T003_Id");
	$mT003=mysql_fetch_array($T003);

	//Verifica o frete dos produtos caso obrigatório rateio do frete quando por conta do emitente
	if($mT003['T003_Valor_Frete'] != $mT003['FreteProdutos'] && $g['C031']['ratearFreteVendaNoCusto'] == 'S' && $mT003['T003_Flag_Frete'] == '0'){
		$resposta['code'] = 'erro';
		$resposta['data'] = "A soma do frete dos produtos é diferente do valor do frete no cabeçalho do orçamento. <p>Para corrigir clique no botão \"Atualizar\" em seguida a opção \"Atualizar preço tabela\".";
	} else {
		//Verifica se é obrigatorio forma de pagamento ao gerar pedido
		if ($mT003['T003_Flag_Status_Orcamento'] == 'L') {
			$resposta['code'] = 'analise';
			$resposta['data'] = "Este orçamento está em análise.";
		} else if($gerarPedido == true && $g['C031']['obrigatorioGerarParcelaOrcamento'] == 'N'){
			$resposta['code'] = true;
			$resposta['data'] = "Você tem certeza que deseja gerar o pedido?";
			if(!empty($g['C031']['horarioLimiteGeracaoPedido']) && $g['C031']['horarioLimiteGeracaoPedido'] != '00:00' && strtotime(date("H:i")) > strtotime($g['C031']['horarioLimiteGeracaoPedido'])){
				$resposta['code'] = 'erro';
				$resposta['data'] = "O horário para geração de novos pedidos ultrapassou o horário permitido ({$g['C031']['horarioLimiteGeracaoPedido']}).";
			}
		} else if(!$gerarPedido && $g['C031']['verificaAlcadaOrcamentoEmailImpressao'] == 'N'){
			$resposta['code'] = true;
		} else {
			$retorno = $VEN001->verificaAlcadaNovo($T003_Id, false);

			$T003=mysql_query("SELECT T003_FLag_Status_Liberacao, T003_D024_Id FROM T003 WHERE T003_Id='{$T003_Id}'");
			$mT003=mysql_fetch_array($T003);

			if ($mT003['T003_D024_Id'] == $g['C020']['C020_Cupom_Fiscal_Cliente']) {
				$mT003['T003_FLag_Status_Liberacao'] = 1;
			}

			if($mT003['T003_FLag_Status_Liberacao'] == 4){

				$motivo_Liberacao = mysql_query("SELECT T003_Motivo_Pedido_Liberacao FROM T003 WHERE T003_Id='{$T003_Id}'");
				$mMotivo_Liberacao = mysql_fetch_array($motivo_Liberacao);
				if(!empty($mMotivo_Liberacao['T003_Motivo_Pedido_Liberacao'])){
					//Redução do tamanho da fonte das regras
					$T003_Motivo_Pedido_Liberacao = $mMotivo_Liberacao['T003_Motivo_Pedido_Liberacao'];
					$T003_Motivo_Pedido_Liberacao = explode("<br> REGRAS:<br><br>", $T003_Motivo_Pedido_Liberacao);
					$T003_Motivo_Pedido_Liberacao = $T003_Motivo_Pedido_Liberacao[0];

					$msg ="<br />".$T003_Motivo_Pedido_Liberacao;

				}
				$msg="A liberação deste orçamento exige maior alçada, pedir liberação? {$msg}";
			}

			if ($mT003['T003_FLag_Status_Liberacao'] != 4) {

				$resposta['code'] = true;
				$resposta['data'] = "Você tem certeza que deseja gerar o pedido?";

			} else {
				$resposta['code'] = false;
				$resposta['data'] = $msg;
			}
		}
	}
}
if($opcaoAcao == "ven001ImprimirOrcamento") {
	global $g;

	$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
	$alcadaUpdate = isset($_REQUEST['alcadaUpdate']) ? $_REQUEST['alcadaUpdate'] : false;
	$moeda = isset($_REQUEST['moeda']) ? $_REQUEST['moeda'] : false;

	if (isset($_REQUEST['gerarHtml'])) {
		echo $VEN001->montarEmailPdf($T003_Id, true);
		die();
	} else {
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
					$erro = "Valor das parcelas não confere com o total do pedido.";
				}
			}
		}
		if(isset($erro)){
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		} else {
			$retorno = $VEN001->montarEmailPdf($T003_Id, false);

			$resposta['code'] = true;
			$resposta['data'] = isset($retorno['AnexosWeb'][0]) ? $retorno['AnexosWeb'][0] : '';
		}
	}
}
if($opcaoAcao == "enviarParaAnalise") {
	$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
	$alcadaUpdate = isset($_REQUEST['alcadaUpdate']) ? $_REQUEST['alcadaUpdate'] : false;

	$retorno = $VEN001->enviarParaAnalise($T003_Id, $alcadaUpdate);

	if ($retorno === true) {
		$resposta['code'] = true;
		$resposta['data'] = "Orçamento enviado para análise";
	} else {
		$resposta['code'] = false;
		$resposta['data'] = $retorno;
	}
}

if($opcaoAcao == "gerarPedido") {
	$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;

	$T005_Id = 0;

	$sqlT003 = mysql_query("SELECT D024_Id, IFNULL(D024_Valor_Limite,0) AS D024_Valor_Limite, T003_Valor_Total FROM T003 LEFT JOIN D024 ON D024_Id=T003_D024_Id WHERE T003_Id = '{$T003_Id}'");
	$rowT003 = mysql_fetch_assoc($sqlT003);

	//Calcula saldo do cliente
	$sqlSaldo = "SELECT ifnull(sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) as Vencido, 
	                    ifnull(sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) as Vencer
	                FROM T002
	                WHERE T002_D024_Id='{$rowT003['D024_Id']}'
	                and T002_Data_Recebimento='0000-00-00'
	                and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";
	$resultSaldo = mysql_query($sqlSaldo);
	$linhaSaldo= mysql_fetch_array($resultSaldo);

	$sqlPedidos   = mysql_query("SELECT IFNULL(SUM(T005_Valor_Total),0) as Pedidos FROM T005 LEFT JOIN T007 ON T007_T005_Id=T005_Id WHERE T005_D024_Id='{$rowT003['D024_Id']}' AND T005_Flag_Status != '8' AND T007_Id IS NULL");
	$linhaPedidos = mysql_fetch_array($sqlPedidos);

	$saldo = $rowT003['D024_Valor_Limite'] - ($linhaSaldo['Vencido'] + $linhaSaldo['Vencer'] + $linhaPedidos['Pedidos']);

	if($rowT003['D024_Valor_Limite'] > 0 && $rowT003['T003_Valor_Total'] > $saldo && $g['nivelAlcada'] < 4){
		$resposta['code'] = false;
		$resposta['data'] = "O valor do pedido ultrapassou o limite do cliente!";
	} else {
		//Confirmar tudo
		mysql_query("UPDATE T004 SET T004_Quantidade_Confirmada=T004_Quantidade WHERE T004_T003_Id = '{$T003_Id}'");
		$retorno = $VEN001->gerarPedido($T003_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedido Gerado com sucesso!";
			$resposta['T005_Id'] = $T005_Id;

			$sqlT005 = mysql_query("SELECT T005_Flag_Status FROM T005 WHERE T005_Id = '{$T005_Id}'");
			$T005 = mysql_fetch_array($sqlT005);
			if($T005['T005_Flag_Status'] == 6){
				$resposta['notificacao'] = "ATENÇÂO!!!<br />O cliente possui restrições! Pedido foi enviado para análise de crédito.";
			}
		} elseif (is_array($retorno)) {
			$resposta['code'] = true;
			$resposta['alerta'] = $retorno;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	}
}


echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>