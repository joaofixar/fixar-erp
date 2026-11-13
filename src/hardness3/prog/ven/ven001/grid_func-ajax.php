<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/VEN001.php');
$VEN001 = new VEN001();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('T003_Id', $T003_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $VEN001->excluir($T003_Id);
			if ($retorno !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			} else {
				$resposta['code'] = true;
				$resposta['data'] = 'T003_Id-' . $T003_Id;
			}
		}
	break;
	
	case 'xatualizarParcelas':
			require_once("bibliotecas/classes/VEN001.php");
			
			$T089_Id 		= isset($r_T089_Id) ? $r_T089_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			
			$VEN001 = new VEN001();
			
			$retorno = $VEN001->ven001AtualizarParcelasGrid($T089_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;

	case 'atualizarParcelas':
			require_once("bibliotecas/classes/VEN001.php");
			$VEN001 = new VEN001();
			
			$T089_Id 		= isset($r_T089_Id) ? $r_T089_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			$dadosTabela = array('tabela' => 'T089', 
								 'tabelaId' => $T089_Id,
								 'tabelaPai' => 'T003',
								 'campos' => 'T003_Data_Emissao as dataEmissao,T089_Prazos_Dias as prazoDias,T089_Numero_Parcela as numeroParcela, T003_Id as tabelaPaiId', 
                                 'where' => "T089_Id='{$T089_Id}'", 
                                 'campoEditado' => $campoEditado, 
                                 'valorAntigo' => $valorAntigo, 
                                 'novoValor' => $novoValor);
			
			if ($campoEditado == 'T089_Prazos_Dias') {
				$retorno = $VEN001->atualizarPrazoGridPagamento($dadosTabela);
		 	} else if ($campoEditado == 'T089_Data_Vencimento') {
				$retorno = $VEN001->atualizarVencimentoGridPagamento($dadosTabela);
	 		} else if ($campoEditado == 'T089_Valor_Parcela') {
 				$retorno = $VEN001->atualizarValorParcelaGridPagamento($dadosTabela);
	 		}
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'gravarMontagemIPV':

		$T003_Id = $r_T003_Id;

		mysql_query("UPDATE T250 SET T250_Aliquota = T250_Aliquota_Editavel WHERE T250_T003_Id = '{$T003_Id}'");
		$erro = mysql_error();

		// Só chama o atualizarPrecoTabela se tiver alguma aliquota IPV/ICF que altere o preço
		$T251 = mysql_query("SELECT T250_Id FROM T250 WHERE T250_T003_Id = '{$T003_Id}' AND T250_Flag_Altera_Preco = 'S'");
		if(mysql_num_rows($T251) > 0){
			$VEN001->atualizarPrecoTabela($T003_Id);
		} 

		if (empty($erro)) {
			$resposta['code'] = true;
		} else{			
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao gravar a alíquota digitada!<br> Informe ao seu suporte técnico o seguinte erro:" . $erro;			
		}

		
	break;

	case 'excluirServico':
		$T145_Id = isset($_REQUEST['T145_Id']) ? $_REQUEST['T145_Id'] : false;
		$result = $VEN001->excluirServico($T145_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
		
	break;	

	case 'ven001CancelarProduto':
		$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
		$result = $VEN001->cancelarProduto($T004_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
		
	break;

	case 'excluirProduto':
		$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
		$result = $VEN001->excluirProduto($T004_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
		
	break;		
	
	case 'aplicarPercentualDesconto':
			require_once("bibliotecas/classes/VEN003.php");
			
			$T004_Id 		= isset($r_T004_Id) ? $r_T004_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			$VEN001 = new VEN001();
			
			$retorno = $VEN001->ven001AplicarPercentualDesconto($T004_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$VEN001->gerarAtividadeItemEdicao($T004_Id, $campoEditado, $valorAntigo, $novoValor);
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'aplicarQuantidade':
		require_once("bibliotecas/classes/VEN003.php");

		$T004_Id      = isset($r_T004_Id) ? $r_T004_Id : false;
		$campoEditado = isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo  = isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor    = isset($r_novoValor) ? $r_novoValor : false;

		$VEN001 = new VEN001();

		$retorno = $VEN001->aplicarQuantidade($T004_Id, $campoEditado, $valorAntigo, $novoValor);
			
		if ($retorno === true) {
			$VEN001->gerarAtividadeItemEdicao($T004_Id, $campoEditado, $valorAntigo, $novoValor);
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T188_T003_Id, T188_Nome_Arquivo from T188 WHERE T188_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T188_Nome_Arquivo']);
		$cArquivo =  "{$g['pathDados']}orcamento/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
			header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
			header('Content-Disposition: attachment; filename="' . $row['T188_Nome_Arquivo'] . '"');
			readfile($cArquivo);
		}
		else
		{
			echo "$cArquivo";
			return false;
		}
		die();
	break;

	case 'ven001calcularValorUnitario':
		$T004_Id 		= isset($r_T004_Id) ? $r_T004_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		$novoValor = gCorrigeNumeroInverte($novoValor);
		
		$resposta['code'] = true;
	break;

	case 'atualizaT004':
		$tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : false;
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
		if($tabela == 'T206'){
			$T004 = "UPDATE T004 SET T004_T206_Id = '{$campoId}', T004_T075_Id = '' WHERE T004_Id = '{$T004_Id}'";
		} else if($tabela == 'T075'){
			$T004 = "UPDATE T004 SET T004_T075_Id = '{$campoId}', T004_T206_Id = '' WHERE T004_Id = '{$T004_Id}'";
		} else if($tabela == false){
			$T004 = "UPDATE T004 SET T004_T075_Id = '', T004_T206_Id = '' WHERE T004_Id = '{$T004_Id}'";
		}
		mysql_query($T004);
		
		$resposta['data'] = true;
	break;

	case 'atualizaT004Loja':
		$tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : false;
		$campoId = isset($_REQUEST['campoId']) ? $_REQUEST['campoId'] : false;
		$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
		$D009_Id_Loja = isset($r_D009_Id_Loja) ? $r_D009_Id_Loja : false;
		if($tabela == 'T206'){
			$T004 = "UPDATE T004 SET T004_T206_Id = '{$campoId}', T004_T075_Id = '', T004_D009_Id_Loja='{$D009_Id_Loja}' WHERE T004_Id = '{$T004_Id}'";
		} else if($tabela == 'T075'){
			$T004 = "UPDATE T004 SET T004_T075_Id = '{$campoId}', T004_T206_Id = '', T004_D009_Id_Loja='{$D009_Id_Loja}' WHERE T004_Id = '{$T004_Id}'";
		} else if($tabela == false){
			$T004 = "UPDATE T004 SET T004_T075_Id = '', T004_T206_Id = '', T004_D009_Id_Loja='{$D009_Id_Loja}' WHERE T004_Id = '{$T004_Id}'";
		}
		mysql_query($T004);
		
		$resposta['data'] = true;
	break;

	case 'ven001CheckboxDividir':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$id = isset($r_id) ? $r_id : false;
		$TXXX = isset($r_TXXX) ? $r_TXXX : false;
		
		$VEN001->updateCheckboxDividir($id, $selected, $TXXX);
	break;

	case 'ven001VerificaItensMudarFIlial':
		$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
		
		$contador = 0;
		
		$sql = mysql_query("SELECT T004_Id FROM T004 WHERE T004_T003_Id='{$T003_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		$sql = mysql_query("SELECT T145_Id FROM T145 WHERE T145_T003_Id='{$T003_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		$sql = mysql_query("SELECT T138_Id FROM T138 WHERE T138_T003_Id='{$T003_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		
		if ($contador == 0) {
			$resposta['code'] = false;
		}
	break;

	case 'ven001VerificaItensDividir':
		$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
		
		$contador = 0;
		
		$sql = mysql_query("SELECT T004_Id FROM T004 WHERE T004_Flag_Dividir='S' AND T004_T003_Id='{$T003_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		$sql = mysql_query("SELECT T145_Id FROM T145 WHERE T145_Flag_Dividir='S' AND T145_T003_Id='{$T003_Id}'");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		$sql = mysql_query("SELECT T138_Id FROM T138 LEFT JOIN T008 ON T008_T138_Id=T138_Id WHERE T138_Flag_Dividir='S' AND T138_T003_Id='{$T003_Id}' AND (T008_T138_Id='' or T008_T138_Id is null or T008_T138_Id=0)");
		if (mysql_num_rows($sql)>0) {
			$contador = 1;
		}
		
		if ($contador == 0) {
			$resposta['code'] = false;
		}
	break;

	case 'gerarOCProduto':
		$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
		$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
		
		$T174 = mysql_query("select T174_Preco_Venda from T174 where T174_Id='{$T174_Id}'");
		$mT174=mysql_fetch_array($T174);
		
		if ($mT174['T174_Preco_Venda']<=0){
			$resposta['data'] = 'Esta cotação ainda está em andamento.';	 
			$resposta['code'] = false;	 
		} else {
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

	case 'ven001CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T004_Id = isset($r_T004_Id) ? $r_T004_Id : false;
		mysql_query("UPDATE T004 SET T004_Flag_Comprar = '{$selected}' WHERE T004_Id = '{$T004_Id}'");

		if($selected == 'S'){
			$dataHora = date("Y-m-d H:i:s");
			mysql_query("UPDATE T004A SET T004A_Data_Hora_Solicitacao_Compra = '{$dataHora}' WHERE T004A_T004_Id = '{$T004_Id}'");
		}else{
			mysql_query("UPDATE T004A SET T004A_Data_Hora_Solicitacao_Compra = '0000-00-00 00:00:00' WHERE T004A_T004_Id = '{$T004_Id}'");
		}

	break;

	case 'ven001CheckboxPrazoCompra':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T004_Id  = isset($r_T004_Id) ? $r_T004_Id : false;
		mysql_query("UPDATE T004A SET T004A_Flag_Solicitar_Prazo_Compras = '{$selected}', T004A_Data_Hora_Solicitacao_Compra = now() WHERE T004A_T004_Id = '{$T004_Id}'");
	break;

	case 'cotarProdutosSelecionados':
		require_once('bibliotecas/classes/GFormWrap.php');  		
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$sqlT004 = mysql_query("SELECT T004_Id, T004_D009_Id FROM T004 
								LEFT JOIN T189 ON T189_T004_Id = T004_Id
								WHERE T004_T003_Id = '{$T003_Id}' 
								AND T004_Flag_Comprar = 'S'
								AND (T189_T004_Id IS NULL OR T189_T004_Id = '' OR T189_T004_Id = 0)");
		if(mysql_num_rows($sqlT004) > 0){
			while($T004 = mysql_fetch_array($sqlT004)){
				$arrayT004 = array(
					'T189_T004_Id' => $T004['T004_Id'],
					'T189_D009_Id' => $T004['T004_D009_Id'],
					'T189_Observacao' => ''
				);
				$form = new GFormWrap($arrayT004);
				$VEN001->cotarProduto($form);
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum produto selecionado';
		}
	break;

	case 'ven001MarcarTodosCotar':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$sqlT004 = mysql_query("SELECT * FROM T004 WHERE T004_T003_Id = '{$T003_Id}' AND (T004_Flag_Comprar = 'N' OR T004_Flag_Comprar IS NULL)");
		if(mysql_num_rows($sqlT004) > 0){
			while($T004 = mysql_fetch_array($sqlT004)){
				mysql_query("UPDATE T004 SET T004_Flag_Comprar = 'S' WHERE T004_Id = '{$T004['T004_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'ven001DesmarcarTodosCotar':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$sqlT004 = mysql_query("SELECT * FROM T004 WHERE T004_T003_Id = '{$T003_Id}' AND (T004_Flag_Comprar = 'S' OR T004_Flag_Comprar IS NULL)");
		if(mysql_num_rows($sqlT004) > 0){
			while($T004 = mysql_fetch_array($sqlT004)){
				mysql_query("UPDATE T004 SET T004_Flag_Comprar = 'N' WHERE T004_Id = '{$T004['T004_Id']}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'checkboxExcluirItemOrcamento':
		$T004_Id = isset($r_T004_Id) ? $r_T004_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T004 SET T004_Flag_Excluir = '{$selected}' WHERE T004_Id = '{$T004_Id}'");
		$resposta['code'] = true;
	break;

	case 'ven001MarcarTodosExcluir':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		mysql_query("UPDATE T004 SET T004_Flag_Excluir = '1' WHERE T004_T003_Id = '{$T003_Id}'");
		$resposta['code'] = true;
	break;

	case 'ven001DesmarcarTodosExcluir':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		mysql_query("UPDATE T004 SET T004_Flag_Excluir = '2' WHERE T004_T003_Id = '{$T003_Id}'");
		$resposta['code'] = true;
	break;

	case 'processarNumeroItemOrcamento':
		$T004_Id = isset($r_T004_Id) ? $r_T004_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
		$retorno = $VEN001->processarNumeroItemOrcamento($T004_Id, $novoValor);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = $retorno;
		}
	break;

	case 'alterarRepresentante':
		if(empty($r_T007_Id)){
	        $resposta['data'] = "Erro ao atualizar o representante, por favor entre em contato com o suporte!";
	        $resposta['code'] = false;
		}

		if(!empty($r_T007_Id) && !empty($r_D021_Id)){
	        $sqlT007 = mysql_query("SELECT * FROM T007 WHERE T007_Id = '{$r_T007_Id}'");
	        $T007 = mysql_fetch_array($sqlT007);

	        mysql_query("UPDATE T007 SET T007_D021_Id = '{$r_D021_Id}' WHERE T007_Id = '{$r_T007_Id}'");

	        mysql_query("UPDATE T005 SET T005_D021_Id = '{$r_D021_Id}' WHERE T005_Id = '{$T007['T007_T005_Id']}'");

	        mysql_query("UPDATE T002 SET T002_D021_Id = '{$r_D021_Id}' WHERE T002_Numero_Documento = '{$T007['T007_Numero_Nota_Fiscal']}'");
	        $resposta['code'] = true;
		}
	break;

	case 'atualizarMontagemIPV':
		if(isset($r_T003_Id) && $r_T003_Id > 0){
			$T003_Id = $r_T003_Id;

			mysql_query("DELETE FROM T250 WHERE T250_T003_Id = '{$T003_Id}'");

			$T003 = mysql_query("SELECT T003_C004_Id FROM T003 WHERE T003_Id='{$T003_Id}'");
			$mT003 = mysql_fetch_array($T003);

			mysql_query("INSERT INTO T250 (T250_T003_Id, T250_Descricao, T250_Aliquota, T250_Aliquota_Original, T250_Tipo, T250_Grupo,T250_Nivel_Acesso,T250_Flag_Campo_Calculado,T250_Flag_Altera_Preco) SELECT '{$T003_Id}', T249_Descricao, T249_Aliquota, T249_Aliquota, T249_Tipo, T249_Grupo,T249_Nivel_Acesso,T249_Flag_Campo_Calculado,T249_Flag_Altera_Preco FROM T249 WHERE T249_C004_Id = '{$mT003['T003_C004_Id']}'");
			$erro = mysql_error();
			log("erro: ".$erro);
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = "Erro ao atualizar as varíaveis do IPV. Por favor, entre em contato com o suporte técnico!";
		}
	break;

	case 'gravarT198':
		$retorno = $VEN001->gravarQuantidadePesquisaGridOrcamento($r_D024_Id, $r_D009_Id, $r_campoEditado, $r_novoValor);
		$resposta['refresh'] = $g['C031']['utilizaQuantidadeConversao'] === 'S';
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'incluirProdutosPesquisaGrid':
		global $g;
		$etapas = $r_etapas ?? false;
		if ($etapas) {
			if ($r_T003_Id > 0) {
				$T003 = mysql_fetch_assoc(mysqli_query("SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$r_T003_Id}'"));
				$sqlEtapa = gComprimeSql("SELECT T198_Id, '{$r_T003_Id}' as T003_Id FROM T198 WHERE T198_D024_Id = '{$T003['T003_D024_Id']}' AND T198_C007_Id = '{$g['usuarioAtual']}' AND T198_Quantidade > 0");

			} else if ($r_T005_Id > 0) {
				$T005 = mysql_fetch_assoc(mysqli_query("SELECT T005_D024_Id FROM T005 WHERE T005_Id = '{$r_T005_Id}'"));
				$sqlEtapa = gComprimeSql("SELECT T266_Id, '{$r_T005_Id}' as T005_Id FROM T266 WHERE T266_D024_Id = '{$T005['T005_D024_Id']}' AND T266_C007_Id = '{$g['usuarioAtual']}' AND T266_Quantidade > 0");

			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Erro ao incluir os produtos. Sem T003_Id e T005_Id. Por favor, entre em contato com o suporte técnico!";
				break;
			}

			$resposta['code'] = true;
			$resposta['data'] = 'jsonEtapas';
			$resposta['json'] = json_encode([
				"titulo" => "Incluir produtos em lote",
				"classe" => (($r_T003_Id > 0) ? "VEN001" : (($r_T005_Id > 0) ? "VEN002" : "")),
				"metodo" => (($r_T003_Id > 0) ? "incluirProdutosPesquisaGridOrcEtapas()" : (($r_T005_Id > 0) ? "incluirProdutosPesquisaGridPedEtapas()" : "")),
				"sql" => $sqlEtapa,
				"idBotao" => false,
				"divId" => $g['divIdParent']
			]);
			break;
		
		} else if(isset($r_T003_Id) && $r_T003_Id > 0){
			$retorno = $VEN001->incluirProdutosPesquisaGridOrcamento($r_T003_Id);
		} else if(isset($r_T005_Id) && $r_T005_Id > 0){
			require_once('bibliotecas/classes/VEN002.php');
			$VEN002 = new VEN002();
			$retorno = $VEN002->incluirProdutosPesquisaGridPedido($r_T005_Id);
		} else {
			$resposta['code'] = false;
			$resposta['code'] = "Erro ao incluir os produtos. Por favor, entre em contato com o suporte técnico!";
		}

		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'limparProdutosPesquisaGrid':
		if(isset($r_T003_Id) && $r_T003_Id > 0){
			$sql = mysql_query("SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$r_T003_Id}'");
			$row = mysql_fetch_array($sql);

			$sqlT198 = mysql_query("select T198_Id from T198 WHERE (T198_D024_Id = '{$row['T003_D024_Id']}' AND T198_C007_Id = '{$g['usuarioAtual']}') OR T198_D009_Id IS NULL");
			$varT198 = mysql_fetch_array($sqlT198);
			mysql_query("DELETE FROM T198 WHERE (T198_D024_Id = '{$row['T003_D024_Id']}' AND T198_C007_Id = '{$g['usuarioAtual']}') OR T198_D009_Id IS NULL");
		} else if(isset($r_T005_Id) && $r_T005_Id > 0){
			$sql = mysql_query("SELECT T005_D024_Id FROM T005 WHERE T005_Id = '{$r_T005_Id}'");
			$row = mysql_fetch_array($sql);
			mysql_query("DELETE FROM T266 WHERE (T266_D024_Id = '{$row['T005_D024_Id']}' AND T266_C007_Id = '{$g['usuarioAtual']}') OR T266_D009_Id IS NULL");
		}
	break;

	case 'atualizarUnidade':

		$T004A_Id = isset($_REQUEST['T004A_Id']) ? $_REQUEST['T004A_Id'] : false;
		$D184_Id  = isset($_REQUEST['D184_Id']) ? $_REQUEST['D184_Id'] : false;

		mysql_query("UPDATE T004A 
				  LEFT JOIN T004 ON T004_Id = T004A_T004_Id 
			            SET T004A_Preco_Unitario_Comercial = if({$D184_Id} <= 0 OR {$D184_Id} != T004A_D184_Id, T004_Valor_Preco_Unitario , T004A_Preco_Unitario_Comercial),
				            T004A_D184_Id                  = '{$D184_Id}'
			          WHERE T004A_Id = '{$T004A_Id}'");	
			
		$resposta['data'] = true;

	break;
	
	case 'confirmarQuantidadeComercial':
		
		$T004_Id   = isset($r_T004_Id) ? $r_T004_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		$D184 = mysql_query("SELECT D184_Tipo_Operacao,
									D184_Quantidade_Conversao
							   FROM D184
						  LEFT JOIN T004A ON T004A_D184_Id = D184_Id
		   				      WHERE T004A_T004_Id = '{$T004_Id}'");


		if(mysql_num_rows($D184) > 0){
			$mD184 = mysql_fetch_array($D184);

			if($mD184['D184_Tipo_Operacao'] == 'D'){
				$quantidadeConfirmada = $novoValor / $mD184['D184_Quantidade_Conversao'];
			} elseif($mD184['D184_Tipo_Operacao'] == 'M'){
				$quantidadeConfirmada = $novoValor * $mD184['D184_Quantidade_Conversao'];
			}
		} else {
			$quantidadeConfirmada = $novoValor;
		}

		mysqli_query("UPDATE T004 SET T004_Quantidade_Confirmada = '{$quantidadeConfirmada}' WHERE T004_Id = '{$T004_Id}'");
		
		$resposta['data'] = true;

	break;

	case 'atualizaT004Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;

		mysql_query("UPDATE T004A SET T004A_T066_Id = '{$T066_Id}' WHERE T004A_T004_Id = '{$T004_Id}'");
		
		$resposta['data'] = true;
	break;
	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
