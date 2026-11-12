<?php
namespace hardness;
/**
 * est - est015 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/EST015.php');
$EST015 = new EST015();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$T218_Id = isset($_REQUEST['T218_Id']) ? $_REQUEST['T218_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('T218_Id', $T218_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $EST015->excluir($T218_Id);
			$resposta['data'] = 'T218_Id-' . $T218_Id;
		}
	break;

	case 'est015CheckboxComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T219_Id = isset($r_T219_Id) ? $r_T219_Id : false;
		$sqlT218 = mysql_query("SELECT T218_Grupo, T219_D009_Id FROM T218 LEFT JOIN T219 ON T218_Id=T219_T218_Id WHERE T219_Id = '{$T219_Id}'");
		$rowT218 = mysql_fetch_array($sqlT218);
		//mysql_query("UPDATE T219 LEFT JOIN T218 ON T218_Id = T219_T218_Id SET T219_Flag_Comprar = 'N' WHERE T219_D009_Id = '{$rowT218['T219_D009_Id']}' AND T218_Grupo = '{$rowT218['T218_Grupo']}'");
        mysqli_query("update T219 set T219_Flag_Comprar='{$selected}' where T219_Id='{$T219_Id}'");
        $retorno['code'] = true;
 	break;

 	case 'est015VerificaGerarOC':
		if(!empty($r_extra)){
			$extraOriginal = base64_decode(urldecode($r_extra));
			if($g['C031']['permiteOrdemCompraProdutoPreCadastro'] == 'N') {
				$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T219_Flag_Comprar='S' and T225_Id is null and D009_C004_Id='{$g['empresaAtual']}' AND D001_Flag_Pre_Cadastro = 'S'"));

				$sql = "SELECT COUNT(DISTINCT T218_Id) as contadorOC, 
					GROUP_CONCAT(DISTINCT C007_Primeiro_Nome SEPARATOR '<br />') as compradores, 
					COUNT(DISTINCT T219_Id) as contadorItems FROM T219 
					left join D009 on D009_Id=T219_D009_Id 
					left join D049 on D049_Id=D009_D049_Id 
					left join D001 on D001_Id=D049_D001_Id
					left join D082 on D082_Id=D049_D082_Id 
					left join D006 on D006_Id=T219_D006_Id 
					left join D037 on D037_Id=T219_D037_Id 
					left join T218 on T218_Id=T219_T218_Id 
					left join T225 on T225_T219_Id=T219_Id 
					left join D024 on D024_Id=T218_D024_Id 
					left join D018 on D018_Id=D024_D018_Id 
					left join C007 on C007_Id=T218_C007_Id_Vendedor_Interno 
					left join D021 on D021_Id=D024_D021_Id 
					left join C004 on C004_Id=T218_C004_Id 
					left join D116 on D116_Id=T218_D116_Id 
					WHERE {$extra}";
				$retorno = mysql_query($sql);
				$row = mysql_fetch_assoc($retorno);
				if ($row['contadorItems'] > 0){
					$resposta['code'] = false;
					$resposta['data'] = "Não é possível gerar OC para itens em pré-cadastro";
					break;
				}
			}

			$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T219_Flag_Comprar='S' and T225_Id is null and D009_C004_Id={$g['empresaAtual']}"));
			$sql = "SELECT COUNT(DISTINCT T218_Id) as contadorOC, 
					GROUP_CONCAT(DISTINCT C007_Primeiro_Nome SEPARATOR '<br />') as compradores, 
					COUNT(DISTINCT T219_Id) as contadorItems FROM T219 
					left join D009 on D009_Id=T219_D009_Id 
					left join D049 on D049_Id=D009_D049_Id 
					left join D001 on D001_Id=D049_D001_Id
					left join D082 on D082_Id=D049_D082_Id 
					left join D006 on D006_Id=T219_D006_Id 
					left join D037 on D037_Id=T219_D037_Id 
					left join T218 on T218_Id=T219_T218_Id 
					left join T225 on T225_T219_Id=T219_Id 
					left join D024 on D024_Id=T218_D024_Id 
					left join D018 on D018_Id=D024_D018_Id 
					left join C007 on C007_Id=T218_C007_Id_Vendedor_Interno 
					left join D021 on D021_Id=D024_D021_Id 
					left join C004 on C004_Id=T218_C004_Id 
					left join D116 on D116_Id=T218_D116_Id 
					WHERE {$extra}";


			$retorno = mysql_query($sql);
			$row = mysql_fetch_array($retorno);

			// Conferido
							//Ok conferido
			$rTriggerPhp = $EST015->PersonalizacaoTriggerVerificarItemGerarOC($extra);
			if($rTriggerPhp !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $rTriggerPhp;
				break;
			}
		
			if (mysql_num_rows($retorno) <= 0 || $row['contadorOC'] <= 0) {
				$resposta['code'] = false;
				$resposta['data'] = "Não foi selecionado nenhum item para comprar.";
			}else{
				$resposta['code'] = true;
				$resposta['data'] = <<<EOT
					<span style="font-size: 14px;">
						<center><b><font style='color: var(--BGVermelho)'>LEIA COM ATENÇÃO!!!</font></b></center><br/>Serão geradas <b>{$row['contadorOC']} ordens de compra</b> para <b>{$row['contadorItems']} itens</b>, dos compradores:<br /><br /> {$row['compradores']}.<br /><br />Deseja gerar as ordens de compra?
					</span>
EOT;
			}
		} else {

			$resposta['code'] = false;
			$resposta['data'] = "Utilize o filtro.";
		}
	break;

 	case 'est015GerarOC':
		if(!empty($r_extra)){
			$extraOriginal = base64_decode(urldecode($r_extra));
			$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T219_Flag_Comprar='S' and T225_Id is null and D009_C004_Id={$g['empresaAtual']}"));

			$sql = "SELECT T218_Id FROM T219 
					left join D009 on D009_Id=T219_D009_Id 
					left join D049 on D049_Id=D009_D049_Id 
					left join D082 on D082_Id=D049_D082_Id 
					left join D006 on D006_Id=T219_D006_Id 
					left join D037 on D037_Id=T219_D037_Id 
					left join T218 on T218_Id=T219_T218_Id 
					left join T225 on T225_T219_Id=T219_Id 
					left join D024 on D024_Id=T218_D024_Id 
					left join D018 on D018_Id=D024_D018_Id 
					left join C007 on C007_Id=T218_C007_Id_Vendedor_Interno 
					left join D021 on D021_Id=D024_D021_Id 
					left join C004 on C004_Id=T218_C004_Id 
					left join D116 on D116_Id=T218_D116_Id 
					WHERE {$extra} 
					group by T218_Id";

			$res = mysql_query($sql);
			$quantidadeOC=0;
			$erro = '';
			while ($row = mysql_fetch_array($res)){
				$retorno = $EST015->gerarPedido($row['T218_Id']);
				if($retorno !== true){
					$erro .= "COTAÇÃO " . $row['T218_Id'] . "<br>" . $retorno . "<br><br>";
				} else {
					$quantidadeOC++;
				}
			}
			
			if (mysql_num_rows($res) <= 0) {
				$resposta['code'] = false;
				$resposta['data'] = "Não foi selecionado nenhum item para comprar.";
			}else{
				$resposta['code'] = true;
				$resposta['data'] = "Foram gerada(s) ".$quantidadeOC." OC(s)";
				if($erro != ''){
					$resposta['data'] = $resposta['data']."<br><br>ERROS:<br><br>".$erro;
				}
			}

		} else {

			$resposta['code'] = false;
			$resposta['data'] = "Utilize o filtro.";
		}

	break;

	case 'atualizarParcelas':
			require_once("bibliotecas/classes/EST015.php");
			
			$T220_Id 		= isset($r_T220_Id) ? $r_T220_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			
			$EST015 = new EST015();
			
			$retorno = $EST015->est015AtualizarParcelasGrid($T220_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'excluirServico':
		$T223_Id = isset($_REQUEST['T223_Id']) ? $_REQUEST['T223_Id'] : false;
		$result = $EST015->excluirServico($T223_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
		
	break;	

	case 'excluirProduto':
		$T219_Id = isset($_REQUEST['T219_Id']) ? $_REQUEST['T219_Id'] : false;
		$result = $EST015->excluirProduto($T219_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
	break;		

	case 'verificaExcluirProduto':
		$T219_Id = isset($_REQUEST['T219_Id']) ? $_REQUEST['T219_Id'] : false;
		$msg = "";
		$sqlT004 = mysql_query("SELECT T004_Id FROM T004 WHERE T004_T219_Id = '{$T219_Id}'");
		if(mysql_num_rows($sqlT004)){
			$orcamento = true;
			$msg .= "orçamento";
		}

		$sqlT006 = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T219_Id = '{$T219_Id}'");
		if(mysql_num_rows($sqlT006)){
			$pedido = true;	
			$msg .= ($orcamento) ? " e um pedido":"pedido";
		}

		$sqlT225 = mysql_query("SELECT T225_Id FROM T225 WHERE T225_T219_Id = '{$T219_Id}'");
		if(mysql_num_rows($sqlT225)){
			$ordemCompra = true;	
			$msg .= "OC";
		}

		if ($orcamento == false && $pedido == false && $ordemCompra == false) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = "Este produto está vinculado a um {$msg}.";
		}
	break;
	
	case 'aplicarPercentualDesconto':
			require_once("bibliotecas/classes/EST015.php");
			
			$T219_Id 		= isset($r_T219_Id) ? $r_T219_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			$EST015 = new EST015();
			
			$retorno = $EST015->est015AplicarPercentualDesconto($T219_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T221_T218_Id, T221_Nome_Arquivo from T221 WHERE T221_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T221_Nome_Arquivo']);
		$cArquivo =  "{$g['pathDados']}cotacaoNovo/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
			header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
			header('Content-Disposition: attachment; filename="' . $row['T221_Nome_Arquivo'] . '"');
			readfile($cArquivo);
		}
		else
		{
			echo "$cArquivo";
			return false;
		}
		die();
	break;

	case 'est015calcularValorUnitario':
		$T219_Id 		= isset($r_T219_Id) ? $r_T219_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		$novoValor = gCorrigeNumeroInverte($novoValor);
		
		$resposta['code'] = true;
	break;

	case 'atualizaT219':
		$tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : false;
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T219_Id = isset($_REQUEST['T219_Id']) ? $_REQUEST['T219_Id'] : false;
		if($tabela == 'T206'){
			$T219 = "UPDATE T219 SET T219_T206_Id = '{$campoId}', T219_T075_Id = '' WHERE T219_Id = '{$T219_Id}'";
		} else if($tabela == 'T075'){
			$T219 = "UPDATE T219 SET T219_T075_Id = '{$campoId}', T219_T206_Id = '' WHERE T219_Id = '{$T219_Id}'";
		} else if($tabela == false){
			$T219 = "UPDATE T219 SET T219_T075_Id = '', T219_T206_Id = '' WHERE T219_Id = '{$T219_Id}'";
		}
		mysql_query($T219);
		
		$resposta['data'] = true;
	break;

	case 'atualizaT219Loja':
		$tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : false;
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T219_Id = isset($_REQUEST['T219_Id']) ? $_REQUEST['T219_Id'] : false;
		$D009_Id_Loja = isset($r_D009_Id_Loja) ? $r_D009_Id_Loja : false;
		if($tabela == 'T206'){
			$T219 = "UPDATE T219 SET T219_T206_Id = '{$campoId}', T219_T075_Id = '', T219_D009_Id_Loja='{$D009_Id_Loja}' WHERE T219_Id = '{$T219_Id}'";
		} else if($tabela == 'T075'){
			$T219 = "UPDATE T219 SET T219_T075_Id = '{$campoId}', T219_T206_Id = '', T219_D009_Id_Loja='{$D009_Id_Loja}' WHERE T219_Id = '{$T219_Id}'";
		} else if($tabela == false){
			$T219 = "UPDATE T219 SET T219_T075_Id = '', T219_T206_Id = '', T219_D009_Id_Loja='{$D009_Id_Loja}' WHERE T219_Id = '{$T219_Id}'";
		}
		mysql_query($T219);
		
		$resposta['data'] = true;
	break;

	case 'est015CheckboxDividir':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$id = isset($r_id) ? $r_id : false;
		$TXXX = isset($r_TXXX) ? $r_TXXX : false;
		
		$EST015->updateCheckboxDividir($id, $selected, $TXXX);
	break;

	case 'est015VerificaItensMudarFIlial':
		$T218_Id = isset($_REQUEST['T218_Id']) ? $_REQUEST['T218_Id'] : false;
		
		$contador = 0;
		
		$sql = mysql_query("SELECT T219_Id FROM T219 WHERE T219_T218_Id='{$T218_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		$sql = mysql_query("SELECT T223_Id FROM T223 WHERE T223_T218_Id='{$T218_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		$sql = mysql_query("SELECT T222_Id FROM T222 WHERE T222_T218_Id='{$T218_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		
		if ($contador == 0) {
			$resposta['code'] = false;
		}
	break;

	case 'est015VerificaItensDividir':
		$T218_Id = isset($_REQUEST['T218_Id']) ? $_REQUEST['T218_Id'] : false;
		
		$contador = 0;
		
		$sql = mysql_query("SELECT T219_Id FROM T219 WHERE T219_Flag_Dividir='S' AND T219_T218_Id='{$T218_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		$sql = mysql_query("SELECT T223_Id FROM T223 WHERE T223_Flag_Dividir='S' AND T223_T218_Id='{$T218_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		$sql = mysql_query("SELECT T222_Id FROM T222 LEFT JOIN T008 ON T008_T222_Id=T222_Id WHERE T222_Flag_Dividir='S' AND T222_T218_Id='{$T218_Id}' AND (T008_T222_Id='' or T008_T222_Id is null or T008_T222_Id=0)");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		
		if ($contador == 0) {
			$resposta['code'] = false;
		}
	break;

	case 'gerarOCProduto':
		$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
		$T219_Id = isset($_REQUEST['T219_Id']) ? $_REQUEST['T219_Id'] : false;
		
		$T174 = mysql_query("select T174_Preco_estda from T174 where T174_Id='{$T174_Id}'");
		$mT174=mysql_fetch_array($T174);
		
		if ($mT174['T174_Preco_estda']<=0){
			$resposta['data'] = 'Esta cotação ainda está em andamento.';	 
			$resposta['code'] = false;	 
		} else {
			$sqlT174 = "SELECT * FROM T174 
						LEFT JOIN T189 ON T189_Id = T174_T189_Id
						LEFT JOIN T219 ON T219_Id = T189_T219_Id
						LEFT JOIN T173 ON T173_Id = T174_T173_Id
						LEFT JOIN D024 ON D024_Id = T173_D024_Id
						WHERE T219_Id = '{$T219_Id}'";
			$resT174 = mysql_query($sqlT174);
			
			$verCotacao = mysql_query("SELECT T010_Flag_Cotacao_Finalizada
							 FROM T189
							 LEFT JOIN T059 ON T059_T189_Id=T189_Id
							 LEFT JOIN T174 ON T174_T059_Id=T059_Id
							 LEFT JOIN T173 ON T173_Id=T174_T173_Id
							 LEFT JOIN T010 ON T010_Id=T173_T010_Id 
							 WHERE T189_T219_Id = '{$T219_Id}'");
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

	case 'est015CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T219_Id = isset($r_T219_Id) ? $r_T219_Id : false;
		mysql_query("UPDATE T219 SET T219_Flag_Comprar = '{$selected}' WHERE T219_Id = '{$T219_Id}'");
	break;

	case 'cotarProdutosSelecionados':
		require_once('bibliotecas/classes/GFormWrap.php');  		
		$T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;
		$sqlT219 = mysql_query("SELECT T219_Id, T219_D009_Id FROM T219 
								LEFT JOIN T189 ON T189_T219_Id = T219_Id
								WHERE T219_T218_Id = '{$T218_Id}' 
								AND T219_Flag_Comprar = 'S'
								AND (T189_T219_Id IS NULL OR T189_T219_Id = '' OR T189_T219_Id = 0)");
		if(mysql_num_rows($sqlT219) > 0){
			while($T219 = mysql_fetch_array($sqlT219)){
				$arrayT219 = array(
					'T189_T219_Id' => $T219['T219_Id'],
					'T189_D009_Id' => $T219['T219_D009_Id'],
					'T189_Observacao' => ''
				);
				$form = new GFormWrap($arrayT219);
				$EST015->cotarProduto($form);
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum produto selecionado';
		}
	break;

	case 'est015MarcarTodosCotar':
		$T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;
		$sqlT219 = mysql_query("SELECT * FROM T219 
								LEFT JOIN T189 ON T189_T219_Id = T219_Id 
								WHERE T219_T218_Id = '{$T218_Id}' AND T219_Flag_Comprar = 'N' AND (T189_T219_Id < 0 OR T189_Id IS NULL)");
		if(mysql_num_rows($sqlT219) > 0){
			while($T219 = mysql_fetch_array($sqlT219)){
				mysql_query("UPDATE T219 SET T219_Flag_Comprar = 'S' WHERE T219_Id = '{$T219['T219_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'est015DesmarcarTodosCotar':
		$T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;
		$sqlT219 = mysql_query("SELECT * FROM T219 
								LEFT JOIN T189 ON T189_T219_Id = T219_Id 
								WHERE T219_T218_Id = '{$T218_Id}' AND T219_Flag_Comprar = 'S' AND (T189_T219_Id < 0 OR T189_Id IS NULL)");
		if(mysql_num_rows($sqlT219) > 0){
			while($T219 = mysql_fetch_array($sqlT219)){
				mysql_query("UPDATE T219 SET T219_Flag_Comprar = 'N' WHERE T219_Id = '{$T219['T219_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'checkboxExcluirItemOrcamento':
		$T219_Id = isset($r_T219_Id) ? $r_T219_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T219 SET T219_Flag_Excluir = '{$selected}' WHERE T219_Id = '{$T219_Id}'");
		$resposta['code'] = true;
	break;

	case 'est015MarcarTodosExcluir':
		$T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;
		mysql_query("UPDATE T219 SET T219_Flag_Excluir = '1' WHERE T219_T218_Id = '{$T218_Id}'");
		$resposta['code'] = true;
	break;

	case 'est015DesmarcarTodosExcluir':
		$T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;
		mysql_query("UPDATE T219 SET T219_Flag_Excluir = '2' WHERE T219_T218_Id = '{$T218_Id}'");
		$resposta['code'] = true;
	break;

	case 'est015CotacaoIncluirCabecalho':
	$retorno = $EST015->insereCotacaoItensViaRequisicao();
		
		if($retorno === true){
			$resposta['code']=true;
			$resposta['data']=$T218_Id;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est015AprovarOrcamento':
		$T218_Id = isset($_REQUEST['T218_Id']) ? $_REQUEST['T218_Id'] : false;
		$verifica = $EST015->est015AprovarOrcamento($T218_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		}
	break;

	case 'est015ProcessarCotacaoVencida':

		$retorno = $EST015->EST015ProcessarCotacaoVencida();

		if($retorno===true){
			$resposta['code']=true;
			$resposta['data']='Processo concluído com sucesso';
		}else{
			$resposta['code']=false;
			$resposta['data']='Houve algum erro ao proceessar as cotações vencidas. Erro: '.$retorno;
		}
	break;

	case 'processarNumeroItemCotacao':
		$T219_Id = isset($r_T219_Id) ? $r_T219_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		require_once("bibliotecas/classes/EST015.php");
		$EST015 = new EST015();
		$retorno = $EST015->processarNumeroItemCotacao($T219_Id, $novoValor);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = $retorno;
		}
	break;

	case 'est015MarcarMelhorPreco':
		$T218_Grupo = isset($r_T218_Grupo) ? $r_T218_Grupo : false;

		mysql_query("UPDATE T219 
		          LEFT JOIN T218 ON T218_Id=T219_T218_Id 
				        SET T219_Flag_Comprar = 'N' 
					  WHERE T218_Grupo = '{$T218_Grupo}'");

		mysql_query("UPDATE T219 
		          LEFT JOIN T218 ON T218_Id=T219_T218_Id 
				  LEFT JOIN T225 ON T219_Id=T225_T219_Id
				        SET T219_Flag_Comprar = 'S'
					  WHERE T218_Grupo = '{$T218_Grupo}'
					    AND T219_Flag_Melhor_Preco = 'S'
						AND T225_Id IS NULL");

		$erro = mysql_error();
		if(!empty($erro)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Erro: ' . $erro;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = 'Processo concluído com sucesso';
		}
	break;

	case 'est015MarcarMelhorPrazo':
		$T218_Grupo = isset($r_T218_Grupo) ? $r_T218_Grupo : false;
		mysql_query("UPDATE T219 
		          LEFT JOIN T218 ON T218_Id=T219_T218_Id 
				        SET T219_Flag_Comprar = 'N' 
					  WHERE T218_Grupo = '{$T218_Grupo}'");

		mysql_query("UPDATE T219 
		          LEFT JOIN T218 ON T218_Id=T219_T218_Id 
				  LEFT JOIN T225 ON T219_Id=T225_T219_Id
				        SET T219_Flag_Comprar = 'S'
					  WHERE T218_Grupo = '{$T218_Grupo}'
					    AND T219_Flag_Melhor_Prazo = 'S'
						AND T225_Id IS NULL");

		$erro = mysql_error();
		if(!empty($erro)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Erro: ' . $erro;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = 'Processo concluído com sucesso';
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
