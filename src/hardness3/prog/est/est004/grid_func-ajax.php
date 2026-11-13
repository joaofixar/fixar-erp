<?php
namespace hardness;
	/**
	* Weiberlan
	* 29/11/2011
	*/
require_once('bibliotecas/classes/EST004.php');
$EST004 = new EST004();

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) {

	case 'downloadxml':
		$id = isset($r_T013_Id) ? $r_T013_Id : false;

		$sql = 'select T013_Numero_Protocolo_Nfe, T013_Data_Emissao from T013 WHERE T013_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);


		$cAno_Mes_NFe=substr($array['T013_Data_Emissao'],0,7);
		$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
		//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
		//$cArquivo="$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['T013_Numero_Protocolo_Nfe'].".xml";
		$cArquivo="$Diretorio_NFe/xml/recebido/".$array['T013_Numero_Protocolo_Nfe'].".xml";

		if (file_exists($cArquivo)){
			// We'll be outputting a PDF
			header('Content-type: text/xml');

			// It will be called downloaded.pdf
			header('Content-Disposition: attachment; filename="'.$array['T013_Numero_Protocolo_Nfe'].'.xml"');

			// The PDF source is in original.pdf
			readfile($cArquivo);
		}
		else{
			echo "XML Inexistente";
		}
		die();
	break;
	
	case 'atualizarPedidosVenda':
		$retorno = $EST004->atualizarPedidosVenda();

		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			// excluir
			$resposta['data'] = 'Pedidos Venda atualizados com sucesso!';
		}
	break;
	
	case 'atualizarTotal':
		require_once("bibliotecas/classes/EST004.php");
		
		$T014_Id 		= isset($r_T014_Id) ? $r_T014_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004AtualizarTotal($T014_Id, $campoEditado, $valorAntigo, $novoValor);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizaCodigoFornecedor':
		
		$T014_Id = isset($r_T014_Id) ? (int) $r_T014_Id : 0;
		$novoValor = isset($r_novoValor) ? trim($r_novoValor) : '';
		
		if ($T014_Id <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = 'Item inválido para atualizar código de barras.';
			break;
		}

		$T014 = mysql_query("SELECT T014_D009_Id,
									T013_D024_Id,
									T014_Codigo_Produto_Fornecedor
							   FROM T014
						  LEFT JOIN T013 ON T013_Id = T014_T013_Id
						      WHERE T014_Id = '{$T014_Id}'
						      LIMIT 1");
		$mT014 = mysql_fetch_array($T014);

		if (empty($mT014['T014_D009_Id']) || empty($mT014['T013_D024_Id'])) {
			$resposta['code'] = false;
			$resposta['data'] = 'Item ainda não foi vinculado corretamente com produto/fornecedor, favor verificar.';
			break;
		}

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		$retorno = $CAD002->definirCodigoFornecedor(
			$mT014['T014_D009_Id'],
			$mT014['T013_D024_Id'],
			$mT014['T014_Codigo_Produto_Fornecedor'],
			$novoValor,
			false,
			true
		);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}

	break;
	
	case 'excluirNota':
		require_once("bibliotecas/classes/EST004.php");
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;				
		
		$EST004 = new EST004();
		
		$retorno = $EST004->excluirNota($T013_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;	
	
	case 'receberNota':
		require_once("bibliotecas/classes/EST004.php");
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;				
		
		$EST004 = new EST004();
		
		$nT013_Id = 0;
		$retorno = $EST004->receberNota($T007_Id);				

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['T013_Id'] = $nT013_Id;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;			

	case 'atualizarParcelas':
		require_once("bibliotecas/classes/EST004.php");
		
		$T154_Id 		= isset($r_T154_Id) ? $r_T154_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004AtualizarParcelas($T154_Id, $campoEditado, $valorAntigo, $novoValor);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est004ConcluirInclusaoItens':
		require_once("bibliotecas/classes/EST004.php");
		
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004ConcluirInclusaoItens($T013_Id, $extraSql);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est004ConcluirInclusaoItensNovo':
		require_once("bibliotecas/classes/EST004.php");
		
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004ConcluirInclusaoItensNovo($T013_Id, $extraSql);
		
		if (is_array($retorno)) {
			if ($retorno['erro'] > 0) {
				$resposta['code'] = false;
				$resposta['data'] = "Os valores de alguns itens da NF não conferem com a OC.<br />Itens com erros: {$retorno['erro']}<br/>Itens corretos: {$retorno['ok']}";
			} else {
				$resposta['code'] = true;
			}
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est004ConcluirInclusaoItensOS':
		require_once("bibliotecas/classes/EST004.php");
		
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004ConcluirInclusaoItensOS($T013_Id, $extraSql);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
		
	case 'est004VerificaItens':
		$retorno = $EST004->est004VerificaItens($r_extra);
		
		if($retorno === true){
			if($nLinhas == '1'){
				$resposta['data'] = "Deseja gerar NF do item selecionado?";
			}else{
				$resposta['data'] = "Deseja gerar NF dos {$nLinhas} itens selecionados?";
			}
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}        
		
	break;

	case 'est004DefinirQuantidadeRecebidaOC':
		require_once("bibliotecas/classes/EST004.php");
				
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004DefinirQuantidadeRecebidaOC($T013_Id, $extraSql);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}

	break;

	case 'est004DefinirQuantidadeRecebidaOCNovo':
		require_once("bibliotecas/classes/EST004.php");
				
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004DefinirQuantidadeRecebidaOCNovo($T013_Id, $extraSql);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}

	break;

	case 'est004DefinirQuantidadeRecebidaOS':
		require_once("bibliotecas/classes/EST004.php");
				
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004DefinirQuantidadeRecebidaOS($T013_Id, $extraSql);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}

	break;

	case 'est004LimparQuantidadeRecebidaOC':
		require_once("bibliotecas/classes/EST004.php");
				
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004LimparQuantidadeRecebidaOC($T013_Id, $extraSql);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est004LimparQuantidadeRecebidaOCNovo':
		require_once("bibliotecas/classes/EST004.php");
				
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004LimparQuantidadeRecebidaOCNovo($T013_Id, $extraSql);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est004LimparQuantidadeRecebidaOS':
		require_once("bibliotecas/classes/EST004.php");
				
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$extraSql = isset($r_extraSql) ? $r_extraSql : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004LimparQuantidadeRecebidaOS($T013_Id, $extraSql);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est004AlterarQuantidadeRecebidaNF':
		require_once("bibliotecas/classes/EST004.php");
		$EST004 = new EST004();
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$T008_Id = isset($r_T008_Id) ? $r_T008_Id : false;
		$quantidade = isset($r_novoValor) ? gCorrigeNumeroInverte($r_novoValor) : false;
		
		$retorno = $EST004->est004AlterarQuantidadeRecebidaNF($T013_Id, $T008_Id, $quantidade);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est004AlterarQuantidadeRecebidaOC':
		
		require_once("bibliotecas/classes/EST004.php");
		$EST004 = new EST004();
		$T225_Id 	= isset($r_T225_Id) ? $r_T225_Id : false;
		$quantidade = isset($r_novoValor) ? $r_novoValor : false;

		$retorno = $EST004->est004AlterarQuantidadeRecebidaOC($T225_Id, $quantidade);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est004IncluirItensNF':
		require_once("bibliotecas/classes/EST004.php");
		$EST004 = new EST004();
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		
		$retorno = $EST004->est004IncluirItensNF($T013_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'T225_Gravar_Totalizacao':
		preg_match('#T225_Id-([0-9]+)#', $r_T225_Id, $matches);
		mysqli_query("CALL T225_Gravar_Totalizacao('{$matches[1]}');");
	break;

	case 'associarItemNovo':
		global $g;
		require_once('bibliotecas/classes/GFormWrap.php');
		$form = new GFormWrap(array('D009_Id' => $r_D009_Id, 'T014_Id' => $r_T014_Id));
		// Associar o Produto com o novo código do fornecedor
		$retorno = $EST004->associarProduto($form);
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			// Já faz a associação com a OC
			$sql = "SELECT T014_Quantidade FROM T014 WHERE T014_Id = '{$r_T014_Id}'";
			$mT014 = mysql_query($sql);
			$mT014 = mysql_fetch_assoc($mT014);
			$sql = "SELECT T225_Id,T225_Quantidade,T225_Quantidade_Pendente_3(T225_Id) as Pendente, T225_D006_Id FROM T225 WHERE T225_Id = '{$r_T225_Id}'";
			$mT225 = mysql_query($sql);
			$mT225 = mysql_fetch_assoc($mT225);
			if (($mT014['T014_Quantidade']<=$mT225['Pendente']) || ($g['C031']['vincularProdutoNFRecebidaOCQtdDiferentes']=="S")){
				if($g['C031']['associarCfopOcParaNf'] == 'S'){
					mysql_query("UPDATE T014 SET T014_T225_Id='{$mT225['T225_Id']}', T014_Flag_Divergencia='S', T014_D006_Id = '{$mT225['T225_D006_Id']}' WHERE T014_Id='{$r_T014_Id}'");
				} else {
					mysql_query("UPDATE T014 SET T014_T225_Id='{$mT225['T225_Id']}', T014_Flag_Divergencia='S' WHERE T014_Id='{$r_T014_Id}'");
				}
				// Seta a quantidade recebida da OC com o valor do item da NF
				$sql = "SELECT T014_Quantidade FROM T014 WHERE T014_Id = '{$r_T014_Id}'";
				$mT014 = mysql_query($sql);
				$mT014 = mysql_fetch_assoc($mT014);
				mysql_query("UPDATE T225 SET T225_Quantidade_Recebida='{$mT014['T014_Quantidade']}' WHERE T225_Id='{$r_T225_Id}'");
				//
				$resposta['data'] = 'Ok!';
			}else{
				$resposta['code'] = false;
				$resposta['data'] = 'A quantidade da Nota fiscal é maior que o saldo da ordem de compra. NF: '.$mT014['T014_Quantidade'].' OC: '.$mT225['Pendente'];
			}
		}
	break;

	case 'duplicarItemNFEntrada':
		$T014_Id = isset($r_T014_Id) ? $r_T014_Id : false;
		$sql = "SELECT * FROM T014 WHERE T014_Id = '{$T014_Id}'";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$campos = $valores = '';
		$idDuplicado = 0;
		foreach ($row as $key => $value) {
			if ($key == 'T014_T225_Id') { continue; }
			if ($key == 'T014_Id') { $idDuplicado = $value; continue; }
			if ($key == 'T014_Id_Duplicado') { 
				if ($value) {
					$idDuplicado = $value; 
				}
				continue;
			}
			$campos .= "{$key},";
			$value = mysql_real_escape_string($value);
			$valores .= "'{$value}',";
		}
		$campos = trim($campos, ',');
		$valores = trim($valores, ',');
		$insert = "INSERT INTO T014 ($campos, T014_Id_Duplicado) VALUES ($valores, '{$idDuplicado}')";
		mysql_query($insert);
		$resposta['code'] = true;
		$resposta['data'] = $g['mysqlLastId'];
	break;

	case 'est004DesvincularItemNota':
		$T014_Id = isset($r_T014_Id) ? $r_T014_Id : false;
		$T225_Id = isset($r_T225_Id) ? $r_T225_Id : false;
		if($T014_Id){
			mysql_query("UPDATE T014 SET T014_T225_Id = 0 WHERE T014_Id = '{$T014_Id}'");
			mysql_query("UPDATE T225 SET T225_Quantidade_Recebida = 0 WHERE T225_Id = '{$T225_Id}'");
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível desvincular este item da nota fiscal";
		}
	break;

	case 'est004ReverterCancelamento':

		require_once("bibliotecas/classes/EST004.php");
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;				
		
		$EST004 = new EST004();
		
		$retorno = $EST004->est004ReverterCancelamento($T013_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizaT008Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T008_Id = isset($_REQUEST['T008_Id']) ? $_REQUEST['T008_Id'] : false;
		$D004_Id = getD004($T066_Id);
		mysql_query("UPDATE T008 SET T008_T066_Id = '{$T066_Id}', T008_D004_Id = '{$D004_Id}' WHERE T008_Id = '{$T008_Id}'");
		
		$resposta['data'] = true;
	break;

	case 'atualizaT014Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T014_Id = isset($_REQUEST['T014_Id']) ? $_REQUEST['T014_Id'] : false;
		$D004_Id = getD004($T066_Id);
		mysql_query("UPDATE T014 SET T014_T066_Id = '{$T066_Id}', T014_D004_Id = '{$D004_Id}' WHERE T014_Id = '{$T014_Id}'");
		
		$resposta['data'] = true;
	break;

	case 'insereRegistroT001':
		global $g;
		$T014_Id=isset($r_T014_Id) ? $r_T014_Id : false;
		log("Estou no ajax");
		$retorno = $EST004->insereRegistroT001($T014_Id); 
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Inserido registro do T001";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

		
	case 'est004AtualizarObservacoes':
		require_once("bibliotecas/classes/EST004.php");
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;				
		
		$EST004 = new EST004();
		
		$retorno = $EST004->EST004AtualizarObservacoes($T013_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Observações atualizadas com sucesso";
		} else {			
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;	

	case 'est004IncluirProduto':
		require_once("bibliotecas/classes/EST004.php");
		$T014_Id = isset($r_T014_Id) ? $r_T014_Id : false;				
		
		$EST004 = new EST004();
		
		$retorno = $EST004->EST004IncluirProduto($T014_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Produto incluido com sucesso";
		} else {			
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;	

	case 'excluirProduto':
		$T014_Id = isset($_REQUEST['T014_Id']) ? $_REQUEST['T014_Id'] : false;
		$T013_Id = isset($_REQUEST['T014_T013_Id']) ? $_REQUEST['T014_T013_Id'] : false;
		
		$result = $EST004->excluirProduto($T014_Id, $T013_Id);
		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
