<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * est - est016 - Pedidos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/EST016.php');
$EST016 = new EST016();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'atualizarParcelas':
			require_once("bibliotecas/classes/EST016.php");
			
			$T226_Id 		= isset($r_T226_Id) ? $r_T226_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			
			$EST016 = new EST016();
			
			$retorno = $EST016->est016AtualizarParcelas($T226_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'excluirServico':
		$T230_Id = isset($r_T230_Id) ? $r_T230_Id : false;
		//$T020_T002_Id = isset($_REQUEST['T020_T002_Id']) ? $_REQUEST['T020_T002_Id'] : false;
		if (empty($T230_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro não existe.';
		} else {
			$retorno = $EST016->excluirServico($T230_Id);
			if ($retorno === true) {
				$resposta['data'] = 'T230_Id-' . $T230_Id;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;
	
	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T227_T224_Id, T227_Nome_Arquivo from T227 WHERE T227_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T227_Nome_Arquivo']);
		// Salvava na mesma pasta que salva os documentos do pedido, então não abrir o arquivo correto
		//$cArquivo =  "{$g['pathDados']}pedido/{$id}.{$pathinfo['extension']}";
		$cArquivo =  "{$g['pathDados']}ordemCompra/{$id}.{$pathinfo['extension']}";		
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['T227_Nome_Arquivo'] . '"');
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
		require_once("bibliotecas/classes/EST016.php");
		$EST016 = new EST016();
		
		$T224_Id = isset($_REQUEST['T224_Id']) ? $_REQUEST['T224_Id'] : false;
		$retorno = $EST016->excluirPedido($T224_Id);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedido excluído com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
		
	case 'est016aplicarPercentualDesconto':
			
			$T225_Id 		= isset($r_T225_Id) ? $r_T225_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			// echo "T225: ".$T225_Id."campo: ".$campoEditado."valorAntigo: ".$valorAntigo."novo valor: ".$novoValor;
			// die();
			
			$EST016 = new EST016();
			
			$retorno = $EST016->est016AplicarPercentualDesconto($T225_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'est016calcularValorUnitario':
		$T225_Id 		= isset($r_T225_Id) ? $r_T225_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		$novoValor = gCorrigeNumeroInverte($novoValor);
		
		$resposta['code'] = true;
	break;

	case 'atualizaT225':
		$tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : false;
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T225_Id = isset($_REQUEST['T225_Id']) ? $_REQUEST['T225_Id'] : false;

		if($tabela == 'T206'){
			$T225 = "UPDATE T225 SET T225_T206_Id = '{$campoId}', T225_T075_Id = '' WHERE T225_Id = '{$T225_Id}'";
		} else if($tabela == 'T075'){
			$T225 = "UPDATE T225 SET T225_T075_Id = '{$campoId}', T225_T206_Id = '' WHERE T225_Id = '{$T225_Id}'";
		} else if($tabela == false){
			$T225 = "UPDATE T225 SET T225_T075_Id = '', T225_T206_Id = '' WHERE T225_Id = '{$T225_Id}'";
		}
		mysql_query($T225);
		
		$resposta['data'] = true;
	break;

	case 'atualizaT225Loja':
		$tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : false;
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T225_Id = isset($_REQUEST['T225_Id']) ? $_REQUEST['T225_Id'] : false;
		$D009_Id_Loja = isset($r_D009_Id_Loja) ? $r_D009_Id_Loja : false;

		if($tabela == 'T206'){
			$T225 = "UPDATE T225 SET T225_T206_Id = '{$campoId}', T225_T075_Id = '', T225_D009_Id_Loja='{$D009_Id_Loja}' WHERE T225_Id = '{$T225_Id}'";
		} else if($tabela == 'T075'){
			$T225 = "UPDATE T225 SET T225_T075_Id = '{$campoId}', T225_T206_Id = '', T225_D009_Id_Loja='{$D009_Id_Loja}' WHERE T225_Id = '{$T225_Id}'";
		} else if($tabela == false){
			$T225 = "UPDATE T225 SET T225_T075_Id = '', T225_T206_Id = '', T225_D009_Id_Loja='{$D009_Id_Loja}' WHERE T225_Id = '{$T225_Id}'";
			mysql_query("UPDATE D009 set D009_Quantidade_Estoque_Liquido=0 where D009_Id='{$D009_Id_Loja}'");

			mysql_query("UPDATE T225 left join D009 on D009_Id=if(T225_D009_Id_Loja<=0, T225_D009_Id, T225_D009_Id_Loja) SET T225_Quantidade_Estoque=D009_Quantidade_Estoque_Liquido WHERE T225_Id = '{$T225_Id}'");
		}
		mysql_query($T225);
		
		$resposta['data'] = true;
	break;

	case 'gerarOCProduto':
	    $T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
	  	$T225_Id = isset($_REQUEST['T225_Id']) ? $_REQUEST['T225_Id'] : false;

	  	$T174 = mysql_query("select T174_Preco_estda from T174 where T174_Id='{$T174_Id}'");
	  	$mT174=mysql_fetch_array($T174);
	  	
	  	if ($mT174['T174_Preco_estda']<=0){
			$resposta['data'] = 'Esta cotação ainda está em andamento.';	 
			$resposta['code'] = false;	 
	  	} else {
	  		$sqlT174 = "SELECT * FROM T174 
						LEFT JOIN T189 ON T189_Id = T174_T189_Id
						LEFT JOIN T225 ON T225_Id = T189_T225_Id
						LEFT JOIN T173 ON T173_Id = T174_T173_Id
						LEFT JOIN D024 ON D024_Id = T173_D024_Id
						WHERE T225_Id = '{$T225_Id}'";
			$resT174 = mysql_query($sqlT174);
			
			$verCotacao = mysql_query("SELECT T010_Flag_Cotacao_Finalizada
							 FROM T189
							 LEFT JOIN T059 ON T059_T189_Id=T189_Id
							 LEFT JOIN T174 ON T174_T059_Id=T059_Id
							 LEFT JOIN T173 ON T173_Id=T174_T173_Id
							 LEFT JOIN T010 ON T010_Id=T173_T010_Id 
							 WHERE T189_T225_Id = '{$T225_Id}'");
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
			$T225_Id = isset($_REQUEST['T225_Id']) ? $_REQUEST['T225_Id'] : false;
			$sqlT174 = mysql_query("SELECT * FROM T174 
							LEFT JOIN T189 ON T189_Id = T174_T189_Id
							LEFT JOIN T225 ON T225_Id = T189_T225_Id
							WHERE T225_Id = '{$T225_Id}' AND T174_Flag_Gerar_OC = 1");
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
	
	case 'est016CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T225_Id = isset($r_T225_Id) ? $r_T225_Id : false;
		mysql_query("UPDATE T225 SET T225_Flag_Cotar = '{$selected}' WHERE T225_Id = '{$T225_Id}'");
	break;

	case 'cotarProdutosSelecionados':
		require_once('bibliotecas/classes/GFormWrap.php');  		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$sqlT225 = mysql_query("SELECT T225_Id, T225_D009_Id FROM T225 
								LEFT JOIN T189 ON T189_T225_Id = T225_Id
								WHERE T225_T224_Id = '{$T224_Id}' 
								AND T225_Flag_Cotar = 'S'
								AND (T189_T225_Id IS NULL OR T189_T225_Id = '' OR T189_T225_Id = 0)");
		if(mysql_num_rows($sqlT225) > 0){
			while($T225 = mysql_fetch_array($sqlT225)){
				$arrayT225 = array(
					'T189_T225_Id' => $T225['T225_Id'],
					'T189_D009_Id' => $T225['T225_D009_Id'],
					'T189_Observacao' => ''
				);
				$form = new GFormWrap($arrayT225);
				$EST016->cotarProduto($form);
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum produto selecionado';
		}
	break;

	case 'est016CheckboxComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T225_Id = isset($r_T225_Id) ? $r_T225_Id : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;
		if(!empty($T189_Id)){
			$sqlT174 = mysql_query("SELECT * FROM T174 
							LEFT JOIN T189 ON T189_Id = T174_T189_Id
							LEFT JOIN T225 ON T225_Id = T189_T225_Id
							WHERE T225_Id = '{$T225_Id}' AND T174_Flag_Gerar_OC = 1");
			mysql_query("UPDATE T225 SET T225_Flag_Comprar = '{$selected}' WHERE T225_Id = '{$T225_Id}'");
			$resposta['code'] = true;
		} else {
			mysql_query("UPDATE T225 SET T225_Flag_Comprar = '{$selected}' WHERE T225_Id = '{$T225_Id}'");
			$resposta['code'] = true;
		}
	break;

	case 'est016CheckboxModificar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T225_Id = isset($r_T225_Id) ? $r_T225_Id : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;

		mysql_query("UPDATE T225 SET T225_Flag_Modificar = '{$selected}' WHERE T225_Id = '{$T225_Id}'");
		$resposta['code'] = true;

	break;

    case 'comprarProdutosSelecionados':
		require_once('bibliotecas/classes/GFormWrap.php');  		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$sqlT225 = mysql_query("SELECT T225_Id, T225_D009_Id FROM T225 
								LEFT JOIN T189 ON T189_T225_Id = T225_Id
								WHERE T225_T224_Id = '{$T224_Id}' 
								AND T225_Flag_Comprar = 'S'
								AND (T189_Flag_Tipo = 'C' OR T189_Id IS NULL)");
    	if(mysql_num_rows($sqlT225) > 0){
	    	while($T225 = mysql_fetch_array($sqlT225)){
				$arrayT225 = array(
					'T189_T225_Id' => $T225['T225_Id'],
					'T189_D009_Id' => $T225['T225_D009_Id'],
				);
				$form = new GFormWrap($arrayT225);
				$EST016->comprarProduto($form);
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum produto selecionado';
		}
    break;

    case 'est016modificarProdutosSelecionados':
		require_once('bibliotecas/classes/GFormWrap.php');  		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$sqlT225 = mysql_query("SELECT T225_Id, T225_D009_Id FROM T225 
								WHERE T225_T224_Id = '{$T224_Id}' 
								AND T225_Flag_Modificar = 'S'");

    	if(mysql_num_rows($sqlT225) > 0){
	    	while($T225 = mysql_fetch_array($sqlT225)){
				$arrayT225 = array(
					'T189_T225_Id' => $T225['T225_Id'],
					'T189_D009_Id' => $T225['T225_D009_Id'],
				);
				$form = new GFormWrap($arrayT225);
				$EST016->modificarProdutosSelecionados($form);
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum produto selecionado';
		}
    break;

    case 'est016MarcarTodosCotar':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$sqlT225 = mysql_query("SELECT * FROM T225 
								LEFT JOIN T189 ON T189_T225_Id = T225_Id 
								WHERE T225_T224_Id = '{$T224_Id}' AND T225_Flag_Cotar = 'N' AND (T189_T225_Id < 0 OR T189_Id IS NULL)");
		if(mysql_num_rows($sqlT225) > 0){
			while($T225 = mysql_fetch_array($sqlT225)){
				mysql_query("UPDATE T225 SET T225_Flag_Cotar = 'S' WHERE T225_Id = '{$T225['T225_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'est016DesmarcarTodosCotar':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$sqlT225 = mysql_query("SELECT * FROM T225 
								LEFT JOIN T189 ON T189_T225_Id = T225_Id 
								WHERE T225_T224_Id = '{$T224_Id}' AND T225_Flag_Cotar = 'S' AND (T189_T225_Id < 0 OR T189_Id IS NULL)");
		if(mysql_num_rows($sqlT225) > 0){
			while($T225 = mysql_fetch_array($sqlT225)){
				mysql_query("UPDATE T225 SET T225_Flag_Cotar = 'N' WHERE T225_Id = '{$T225['T225_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'est016MarcarTodosComprar':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$sqlT225 = mysql_query("SELECT * FROM T225 
								LEFT JOIN T189 ON T189_T225_Id = T225_Id
								WHERE T225_T224_Id = '{$T224_Id}' AND (T225_Flag_Comprar = 'N' OR T225_Flag_Comprar IS NULL)");
		if(mysql_num_rows($sqlT225) > 0){
			$resposta['code'] = false;
			while($T225 = mysql_fetch_array($sqlT225)){
				if(empty($T225['T189_Id'])){
					mysql_query("UPDATE T225 SET T225_Flag_Comprar = 'S' WHERE T225_Id = '{$T225['T225_Id']}'");
					$resposta['code'] = true;
				} else {
					$sqlT174 = mysql_query("SELECT * FROM T174 
						LEFT JOIN T189 ON T189_Id = T174_T189_Id
						LEFT JOIN T225 ON T225_Id = T189_T225_Id
						WHERE T225_Id = '{$T225['T225_Id']}' AND T174_Flag_Gerar_OC = 1");
					if(mysql_num_rows($sqlT174) > 0){
						$resposta['code'] = true;
						mysql_query("UPDATE T225 SET T225_Flag_Comprar = 'S' WHERE T225_Id = '{$T225['T225_Id']}'");
					}
				}			
			}
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'est016DesmarcarTodosComprar':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$sqlT225 = mysql_query("SELECT * FROM T225 
								LEFT JOIN T189 ON T189_T225_Id = T225_Id 
								WHERE T225_T224_Id = '{$T224_Id}' AND (T225_Flag_Comprar = 'S' AND T189_Id IS NULL) OR (T225_Flag_Comprar = 'S' AND T189_Flag_Status != 'D')");
		if(mysql_num_rows($sqlT225) > 0){
			while($T225 = mysql_fetch_array($sqlT225)){
				mysql_query("UPDATE T225 SET T225_Flag_Comprar = 'N' WHERE T225_Id = '{$T225['T225_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'checkboxExcluirItemPedido':
		$T225_Id = isset($r_T225_Id) ? $r_T225_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T225 SET T225_Flag_Excluir = '{$selected}' WHERE T225_Id = '{$T225_Id}'");
		$resposta['code'] = true;
	break;

	case 'est016MarcarTodosExcluir':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		mysql_query("UPDATE T225 SET T225_Flag_Excluir = '1' WHERE T225_T224_Id = '{$T224_Id}'");
		$resposta['code'] = true;
	break;

	case 'est016DesmarcarTodosExcluir':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		mysql_query("UPDATE T225 SET T225_Flag_Excluir = '2' WHERE T225_T224_Id = '{$T224_Id}'");
		$resposta['code'] = true;
	break;

	case 'est016OCIncluirCabecalho':
		$retorno = $EST016->insereOCItensViaRequisicao();
		
		if($retorno === true){
			$resposta['code']=true;
			$resposta['data']=$T224_Id;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est016AprovarPedido':
		$T224_Id = isset($_REQUEST['T224_Id']) ? $_REQUEST['T224_Id'] : false;
		
		$retorno = $EST016->est016AprovarPedido($T224_Id);
		$resposta['code'] = false;
		$resposta['data'] = $retorno;
	break;

	case 'est016ProcessarOCVencida':

		$extra = isset($r_sql) ? $r_sql : false;
		$T224_Id = false;
		if($extra !== false){
			$T224_Id = array();
			$extra   = base64_decode($extra);
			$T224    = mysql_query("SELECT T224_Id
									  FROM T224
									       $extra");
			while($mT224 = mysql_fetch_array($T224)){
				$T224_Id[] = $mT224['T224_Id'];
			}	
		}
		
		$retorno = $EST016->EST016ProcessarOCVencida($T224_Id);

		if(is_array($retorno)){
			$msg = ($retorno['totalProcessada'] == 1) ? 'ordem de compra' : 'ordens de compra';
			$resposta['code']=true;
			$resposta['data']='Operação concluída com sucesso, processado '.$retorno['totalProcessada'].' '.$msg;
		}else{
			$resposta['code']=false;
			$resposta['data']=$retorno;
		}
	break;

	case 'cancelarProdutoOC':
		$T225_Id = isset($r_T225_Id) ? $r_T225_Id : false;
		$retorno = $EST016->cancelarProduto($T225_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'verificaExcluirProduto':
		$T225_Id = isset($_REQUEST['T225_Id']) ? $_REQUEST['T225_Id'] : false;
		
		$sqlT006 = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T225_Id = '{$T225_Id}'");
		if(mysql_num_rows($sqlT006)){
			$pedido = true;
		}

		$sqlT006 = mysql_query("SELECT T219_Id, T006_Id FROM T219 LEFT JOIN T225 ON T225_T219_Id=T219_Id LEFT JOIN T006 ON T006_T219_Id=T219_Id WHERE T225_Id = '{$T225_Id}' AND T006_Id > 0");
		if(mysql_num_rows($sqlT006)){
			$pedido = true;
		}

		if ($pedido == false) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = "Este produto está vinculado a um pedido.";
		}
	break;

	case 'excluirProdutoOC':
		$T225_Id = isset($_REQUEST['T225_Id']) ? $_REQUEST['T225_Id'] : false;
		$retorno = $EST016->excluirProduto($T225_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est016ValidaQuantidadeDividirOC':
    	$T225_Id = isset($r_T225_Id) ? $r_T225_Id : false;
    	$r_novoValor = gCorrigeNumeroInverte($r_novoValor);
    	$r_valorAntigo = gCorrigeNumeroInverte($r_valorAntigo);
    	$novoValor = isset($r_novoValor) ? $r_novoValor : false;
    	$valorAntigo = isset($r_valorAntigo) ? $r_valorAntigo : false;

    	$T225 = mysql_query("SELECT T225_Quantidade, IFNULL(T219_T004_Id, 0) AS T219_T004_Id, IFNULL(T219_T006_Id, 0) AS T219_T006_Id FROM T225 LEFT JOIN T219 ON T219_Id=T225_T219_Id WHERE T225_Id = '{$T225_Id}'");
    	$T225 = mysql_fetch_array($T225);

    	if($novoValor < 0){
			mysql_query("UPDATE T225 SET T225_Quantidade_Dividir = '{$valorAntigo}' WHERE T225_Id = '{$T225_Id}'", false);
			$resposta['data'] = "O valor informado deve ser maior que zero";
			$resposta['code'] = false;
		} else if($novoValor > $T225['T225_Quantidade']){
			mysql_query("UPDATE T225 SET T225_Quantidade_Dividir = '{$valorAntigo}' WHERE T225_Id = '{$T225_Id}'", false);
			$resposta['data'] = "O valor informado é maior que a quantidade de produtos na ordem de compra";
			$resposta['code'] = false;
    	} else if(($T225['T219_T004_Id'] > 0 || $T225['T219_T006_Id'] > 0) && $novoValor < $T225['T225_Quantidade'] && $novoValor > 0){
    		mysql_query("UPDATE T225 SET T225_Quantidade_Dividir = '{$valorAntigo}' WHERE T225_Id = '{$T225_Id}'", false);
			$resposta['data'] = "Só é possível dividir a quantidade integral deste item, devido este item está vinculado a uma venda";
			$resposta['code'] = false;
    	} else {
			$resposta['code'] = true;
    	}
    break;

    case 'est016LimparQuantidades':
    	$erro=0;
		mysql_query("START_TRANSACTION");
    	$r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T224 = mysql_query($r_sql);
		while($mT224 = mysql_fetch_array($T224)){
			$upT225 = mysql_query("UPDATE T225 SET T225_Quantidade_Dividir = 0 WHERE T225_Id='{$mT224['T225_Id']}'", false);
			$erro = mysql_error();
			if($erro){
				mysql_query("ROLLBACK");
				$erro=1;
			}
		}
		mysql_query("COMMIT");

		if($erro==0){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Houve algum erro ao atualizar a quantidade. Por favor, contato o suporte técnico.";
		}
    break;

    case 'est016DefinirQuantidadeOrdemCompra':
    	global $g;
		$erro=0;
		mysql_query("START_TRANSACTION");
		$r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T224 = mysql_query($r_sql);
		while($mT224 = mysql_fetch_array($T224)){
			$upT225 = mysql_query("UPDATE T225 SET T225_Quantidade_Dividir=T225_Quantidade WHERE T225_Id='{$mT224['T225_Id']}'", false);
			$erro = mysql_error();
			if($erro){
				mysql_query("ROLLBACK");
				$erro=1;
			}
		}
		mysql_query("COMMIT");
		if($erro==0){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
		}
    break;

    case 'est016VerificaItensDividirOrdemCompra':
        $qtdProdutoParaDividir = 0;
        $r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T225 = mysql_query($r_sql);
		while($mT225 = mysql_fetch_array($T225)) {
			if ($mT225['T225_Quantidade_Dividir'] > 0) {
				$qtdProdutoParaDividir++;
	        	if($mT225['T225_Quantidade_Dividir'] > $mT225['T225_Quantidade']) {
	        		$resposta['code'] = false;
	            	$resposta['data'] = "Quantidade informada para dividir é maior que a quantidade disponível no pedido.";
	        	}
	        }
		}

        if(!$qtdProdutoParaDividir){
        	$resposta['code'] = false;
            $resposta['data'] = "Não há itens selecionados para duplicar";
        }
    break;

	case 'processarNumeroItemOrdemCompra':
		$T225_Id = isset($r_T225_Id) ? $r_T225_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		$retorno = $EST016->processarNumeroItemOrdemCompra($T225_Id, $novoValor);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = $retorno;
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
