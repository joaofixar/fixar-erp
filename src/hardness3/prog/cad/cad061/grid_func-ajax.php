<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * VEN - VEN002 - Pedidos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD061.php');
$CAD061 = new CAD061();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {

	case 'processarPedidosPeriodicos':
			require_once("bibliotecas/classes/CAD061.php");

			$CAD061 = new CAD061();
			
			$retorno = $CAD061->processarPedidosPeriodicos();
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;

	case 'atualizarParcelas':
			require_once("bibliotecas/classes/VEN002.php");
			
			$T090_Id 		= isset($r_T090_Id) ? $r_T090_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			
			$VEN002 = new VEN002();
			
			$retorno = $VEN002->ven002AtualizarParcelas($T090_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'excluirServico':
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		//$T020_T002_Id = isset($_REQUEST['T020_T002_Id']) ? $_REQUEST['T020_T002_Id'] : false;
		if (empty($T147_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro não existe.';
		} else {
			$retorno = $VEN002->excluirServico($T147_Id);
			if ($retorno === true) {
				$resposta['data'] = 'T147_Id-' . $T147_Id;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;

	case 'cancelarServico':
		$T147_Id = isset($r_T147_Id) ? $r_T147_Id : false;
		//$T020_T002_Id = isset($_REQUEST['T020_T002_Id']) ? $_REQUEST['T020_T002_Id'] : false;
		if (empty($T147_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro não existe.';
		} else {
			$retorno = $VEN002->cancelarServico($T147_Id);
			if ($retorno === true) {
				$resposta['data'] = 'T147_Id-' . $T147_Id;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;
	
	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T167_T005_Id, T167_Nome_Arquivo from T167 WHERE T167_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T167_Nome_Arquivo']);
		$cArquivo =  "{$g['pathDados']}pedido/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['T167_Nome_Arquivo'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
			return false;
		}
		die();
	break;
	
	case 'excluirPedido':
		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();
		
		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
		$retorno = $VEN002->excluirPedido($T005_Id);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedido excluído com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'excluirProduto':
		$D158_Id = isset($_REQUEST['D158_Id']) ? $_REQUEST['D158_Id'] : false;
		$D157_Id = isset($_REQUEST['D158_D157_Id']) ? $_REQUEST['D158_D157_Id'] : false;
		
		$result = $CAD061->excluirProduto($D158_Id, $D157_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;
		}
		
	break;

	case 'cancelarProduto':
		$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
		$D157_Id = isset($_REQUEST['T006_T005_Id']) ? $_REQUEST['T006_T005_Id'] : false;
		
		$result = $VEN002->cancelarProduto($T006_Id, $T005_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
		
	break;		
	
	case 'ven002aplicarPercentualDesconto':
			
			$T006_Id 		= isset($r_T006_Id) ? $r_T006_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			// echo "T006: ".$T006_Id."campo: ".$campoEditado."valorAntigo: ".$valorAntigo."novo valor: ".$novoValor;
			// die();
			
			$VEN002 = new VEN002();
			
			$retorno = $VEN002->ven002AplicarPercentualDesconto($T006_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'ven002calcularValorUnitario':
		$T006_Id 		= isset($r_T006_Id) ? $r_T006_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		$novoValor = gCorrigeNumeroInverte($novoValor);
		
		$resposta['code'] = true;
	break;

	case 'atualizaT006':
		$tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : false;
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;

		if($tabela == 'T206'){
			$T006 = "UPDATE T006 SET T006_T206_Id = '{$campoId}', T006_T075_Id = '' WHERE T006_Id = '{$T006_Id}'";
		} else if($tabela == 'T075'){
			$T006 = "UPDATE T006 SET T006_T075_Id = '{$campoId}', T006_T206_Id = '' WHERE T006_Id = '{$T006_Id}'";
		} else if($tabela == false){
			$T006 = "UPDATE T006 SET T006_T075_Id = '', T006_T206_Id = '' WHERE T006_Id = '{$T006_Id}'";
		}
		mysql_query($T006);
		
		$resposta['data'] = true;
	break;

	case 'atualizaT006Loja':
		$tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : false;
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
		$D009_Id_Loja = isset($r_D009_Id_Loja) ? $r_D009_Id_Loja : false;

		if($tabela == 'T206'){
			$T006 = "UPDATE T006 SET T006_T206_Id = '{$campoId}', T006_T075_Id = '', T006_D009_Id_Loja='{$D009_Id_Loja}' WHERE T006_Id = '{$T006_Id}'";
		} else if($tabela == 'T075'){
			$T006 = "UPDATE T006 SET T006_T075_Id = '{$campoId}', T006_T206_Id = '', T006_D009_Id_Loja='{$D009_Id_Loja}' WHERE T006_Id = '{$T006_Id}'";
		} else if($tabela == false){
			$T006 = "UPDATE T006 SET T006_T075_Id = '', T006_T206_Id = '', T006_D009_Id_Loja='{$D009_Id_Loja}' WHERE T006_Id = '{$T006_Id}'";
			mysql_query("UPDATE D009 set D009_Quantidade_Estoque_Liquido=0 where D009_Id='{$D009_Id_Loja}'");

			mysql_query("UPDATE T006 left join D009 on D009_Id=if(T006_D009_Id_Loja<=0, T006_D009_Id, T006_D009_Id_Loja) SET T006_Quantidade_Estoque=D009_Quantidade_Estoque_Liquido WHERE T006_Id = '{$T006_Id}'");
		}
		mysql_query($T006);
		
		$resposta['data'] = true;
	break;

	case 'gerarOCProduto':
	    $T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
	  	$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;

	  	$T174 = mysql_query("select T174_Preco_Venda from T174 where T174_Id='{$T174_Id}'");
	  	$mT174=mysql_fetch_array($T174);
	  	
	  	if ($mT174['T174_Preco_Venda']<=0){
			$resposta['data'] = 'Esta cotação ainda está em andamento.';	 
			$resposta['code'] = false;	 
	  	} else {
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
	break;

	case 'validaCampoComprar':
		//Se existir cotação, deve ser selecionado um fornecedor
		if(!empty($_REQUEST['T189_Id'])){
			$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
			$sqlT174 = mysql_query("SELECT * FROM T174 
							LEFT JOIN T189 ON T189_Id = T174_T189_Id
							LEFT JOIN T006 ON T006_Id = T189_T006_Id
							WHERE T006_Id = '{$T006_Id}' AND T174_Flag_Gerar_OC = 1");
			if(mysql_num_rows($sqlT174) > 0){
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = 'Nenhum fornecedor selecionado';
			}
		} else {
			$resposta['code'] = true;
		}
	break;
	
	case 'ven002CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		mysql_query("UPDATE T006 SET T006_Flag_Cotar = '{$selected}' WHERE T006_Id = '{$T006_Id}'");
	break;

	case 'cotarProdutosSelecionados':
		require_once('bibliotecas/classes/GFormWrap.php');  		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sqlT006 = mysql_query("SELECT T006_Id, T006_D009_Id FROM T006 
								LEFT JOIN T189 ON T189_T006_Id = T006_Id
								WHERE T006_T005_Id = '{$T005_Id}' 
								AND T006_Flag_Cotar = 'S'
								AND (T189_T006_Id IS NULL OR T189_T006_Id = '' OR T189_T006_Id = 0)");
		if(mysql_num_rows($sqlT006) > 0){
			while($T006 = mysql_fetch_array($sqlT006)){
				$arrayT006 = array(
					'T189_T006_Id' => $T006['T006_Id'],
					'T189_D009_Id' => $T006['T006_D009_Id'],
					'T189_Observacao' => ''
				);
				$form = new GFormWrap($arrayT006);
				$VEN002->cotarProduto($form);
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum produto selecionado';
		}
	break;

	case 'ven002CheckboxComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;
		if(!empty($T189_Id)){
			$sqlT174 = mysql_query("SELECT * FROM T174 
							LEFT JOIN T189 ON T189_Id = T174_T189_Id
							LEFT JOIN T006 ON T006_Id = T189_T006_Id
							WHERE T006_Id = '{$T006_Id}' AND T174_Flag_Gerar_OC = 1");
			mysql_query("UPDATE T006 SET T006_Flag_Comprar = '{$selected}' WHERE T006_Id = '{$T006_Id}'");
			$resposta['code'] = true;
		} else {
			mysql_query("UPDATE T006 SET T006_Flag_Comprar = '{$selected}' WHERE T006_Id = '{$T006_Id}'");
			$resposta['code'] = true;
		}
	break;

	case 'ven002CheckboxModificar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;

		mysql_query("UPDATE T006 SET T006_Flag_Modificar = '{$selected}' WHERE T006_Id = '{$T006_Id}'");
		$resposta['code'] = true;

	break;

    case 'comprarProdutosSelecionados':
		require_once('bibliotecas/classes/GFormWrap.php');  		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sqlT006 = mysql_query("SELECT T006_Id, T006_D009_Id FROM T006 
								LEFT JOIN T189 ON T189_T006_Id = T006_Id
								WHERE T006_T005_Id = '{$T005_Id}' 
								AND T006_Flag_Comprar = 'S'
								AND (T189_Flag_Tipo = 'C' OR T189_Id IS NULL)");
    	if(mysql_num_rows($sqlT006) > 0){
	    	while($T006 = mysql_fetch_array($sqlT006)){
				$arrayT006 = array(
					'T189_T006_Id' => $T006['T006_Id'],
					'T189_D009_Id' => $T006['T006_D009_Id'],
				);
				$form = new GFormWrap($arrayT006);
				$VEN002->comprarProduto($form);
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum produto selecionado';
		}
    break;

    case 'ven002modificarProdutosSelecionados':
		require_once('bibliotecas/classes/GFormWrap.php');  		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sqlT006 = mysql_query("SELECT T006_Id, T006_D009_Id FROM T006 
								WHERE T006_T005_Id = '{$T005_Id}' 
								AND T006_Flag_Modificar = 'S'");

    	if(mysql_num_rows($sqlT006) > 0){
	    	while($T006 = mysql_fetch_array($sqlT006)){
				$arrayT006 = array(
					'T189_T006_Id' => $T006['T006_Id'],
					'T189_D009_Id' => $T006['T006_D009_Id'],
				);
				$form = new GFormWrap($arrayT006);
				$VEN002->modificarProdutosSelecionados($form);
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum produto selecionado';
		}
    break;

    case 'ven002MarcarTodosCotar':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sqlT006 = mysql_query("SELECT * FROM T006 
								LEFT JOIN T189 ON T189_T006_Id = T006_Id 
								WHERE T006_T005_Id = '{$T005_Id}' AND T006_Flag_Cotar = 'N' AND (T189_T006_Id < 0 OR T189_Id IS NULL)");
		if(mysql_num_rows($sqlT006) > 0){
			while($T006 = mysql_fetch_array($sqlT006)){
				mysql_query("UPDATE T006 SET T006_Flag_Cotar = 'S' WHERE T006_Id = '{$T006['T006_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'ven002DesmarcarTodosCotar':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sqlT006 = mysql_query("SELECT * FROM T006 
								LEFT JOIN T189 ON T189_T006_Id = T006_Id 
								WHERE T006_T005_Id = '{$T005_Id}' AND T006_Flag_Cotar = 'S' AND (T189_T006_Id < 0 OR T189_Id IS NULL)");
		if(mysql_num_rows($sqlT006) > 0){
			while($T006 = mysql_fetch_array($sqlT006)){
				mysql_query("UPDATE T006 SET T006_Flag_Cotar = 'N' WHERE T006_Id = '{$T006['T006_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'ven002MarcarTodosComprar':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sqlT006 = mysql_query("SELECT * FROM T006 
								LEFT JOIN T189 ON T189_T006_Id = T006_Id
								WHERE T006_T005_Id = '{$T005_Id}' AND (T006_Flag_Comprar = 'N' OR T006_Flag_Comprar IS NULL)");
		if(mysql_num_rows($sqlT006) > 0){
			$resposta['code'] = false;
			while($T006 = mysql_fetch_array($sqlT006)){
				if(empty($T006['T189_Id'])){
					mysql_query("UPDATE T006 SET T006_Flag_Comprar = 'S' WHERE T006_Id = '{$T006['T006_Id']}'");
					$resposta['code'] = true;
				} else {
					$sqlT174 = mysql_query("SELECT * FROM T174 
						LEFT JOIN T189 ON T189_Id = T174_T189_Id
						LEFT JOIN T006 ON T006_Id = T189_T006_Id
						WHERE T006_Id = '{$T006['T006_Id']}' AND T174_Flag_Gerar_OC = 1");
					if(mysql_num_rows($sqlT174) > 0){
						$resposta['code'] = true;
						mysql_query("UPDATE T006 SET T006_Flag_Comprar = 'S' WHERE T006_Id = '{$T006['T006_Id']}'");
					}
				}			
			}
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'ven002DesmarcarTodosComprar':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sqlT006 = mysql_query("SELECT * FROM T006 
								LEFT JOIN T189 ON T189_T006_Id = T006_Id 
								WHERE T006_T005_Id = '{$T005_Id}' AND (T006_Flag_Comprar = 'S' AND T189_Id IS NULL) OR (T006_Flag_Comprar = 'S' AND T189_Flag_Status != 'D')");
		if(mysql_num_rows($sqlT006) > 0){
			while($T006 = mysql_fetch_array($sqlT006)){
				mysql_query("UPDATE T006 SET T006_Flag_Comprar = 'N' WHERE T006_Id = '{$T006['T006_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'checkboxExcluirItemPedido':
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T006 SET T006_Flag_Excluir = '{$selected}' WHERE T006_Id = '{$T006_Id}'");
		$resposta['code'] = true;
	break;

	case 'ven002MarcarTodosExcluir':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		mysql_query("UPDATE T006 SET T006_Flag_Excluir = '1' WHERE T006_T005_Id = '{$T005_Id}'");
		$resposta['code'] = true;
	break;

	case 'ven002DesmarcarTodosExcluir':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		mysql_query("UPDATE T006 SET T006_Flag_Excluir = '2' WHERE T006_T005_Id = '{$T005_Id}'");
		$resposta['code'] = true;
	break;

	case 'atualizaEstoqueT006':
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;

		$T006 = "UPDATE T006 SET T006_C004_Id_Estoque = '{$campoId}' WHERE T006_Id = '{$T006_Id}'";
		mysql_query($T006);
		
		$resposta['data'] = true;
	break;

	case 'processarNumeroItemPedido':
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();
		$retorno = $VEN002->processarNumeroItemPedido($T006_Id, $novoValor);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = $retorno;
		}
	break;

	case 'atualizarMontagemIPV':
		if(isset($r_T005_Id) && $r_T005_Id > 0){
			$T005_Id = $r_T005_Id;
		
			mysql_query("DELETE FROM T251 WHERE T251_T005_Id = '{$T005_Id}'");

			$T005 = mysql_query("SELECT T005_C004_Id FROM T005 WHERE T005_Id='{$T005_Id}'");
			$mT005 = mysql_fetch_array($T005);

			mysql_query("INSERT INTO T251 (T251_T005_Id, T251_Descricao, T251_Aliquota, T251_Aliquota_Original, T251_Tipo, T251_Grupo) SELECT '{$T005_Id}', T249_Descricao, T249_Aliquota, T249_Aliquota, T249_Tipo, T249_Grupo FROM T249 WHERE T249_C004_Id = '{$mT005['T005_C004_Id']}'");
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = "Erro ao atualizar as varíaveis do IPV. Por favor, entre em contato com o suporte técnico!";
		}
	break;
	
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
