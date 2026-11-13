<?php
namespace hardness;
	/**
	* Weiberlan
	* 12/11/2011
	*/
require_once('bibliotecas/classes/EST003.php');
$EST003 = new EST003();

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) {

	case 'abreXML':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;

		$sql = 'select T007_Numero_Protocolo_Nfe, T007_Data_Emissao from T007 WHERE T007_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);


		$cAno_Mes_NFe=substr($array['T007_Data_Emissao'],0,7);
		$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
		//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
		$cArquivo="$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['T007_Numero_Protocolo_Nfe'].".xml";

		if (file_exists($cArquivo)){
			// We'll be outputting a PDF
			header('Content-type: text/xml');

			// It will be called downloaded.pdf
			header('Content-Disposition: attachment; filename="'.$array['T007_Numero_Protocolo_Nfe'].'"');

			// The PDF source is in original.pdf
			readfile($cArquivo);
		}
		else{
			echo "XML Inexistente";
		}
		die();
	break;

	case 'retornarPedido':
		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
		$verifica = $VEN012->retornarPedido($T005_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		}
	break;
		
	case 'est003FaturarPedido':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$T005_Id = mysql_real_escape_string($T005_Id);
		
		$EST003 = new EST003();
		$retorno = $EST003->est003faturarPedido($T005_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est003ProdutosPedido':
		$T206_Id = isset($r_T206_Id) ? $r_T206_Id : false;
		$T006 = mysql_query("SELECT SUM(T006_Quantidade) as Pedidos FROM T006 WHERE T006_T206_Id = '{$T206_Id}'");
		$retorno = mysql_fetch_array($T006);
		if($retorno['Pedidos'] > 0){
			$resposta['code'] = false;
		} else {	
			$resposta['code'] = true;		
		}
	break;
	
	case 'est003excluirDefeito':
		$T206_Id = isset($r_T206_Id) ? $r_T206_Id : false;
		mysql_query("DELETE FROM T206 WHERE T206_Id = '{$T206_Id}'");
		$resposta['code'] = true;
	break;

	case 'est003RequisitarCompra':
		global $g;
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$C007_Id = $g['usuarioAtual'];

			//Busca dados da T006 para inserir na Tabela de Requisição
	$sql = <<<EOT
		SELECT 
		T006_Descricao_Produto, T006_Quantidade, T006_T005_Id, D024_Nome_Empresa
		FROM T006 	
		LEFT JOIN T005 on T005_Id=T006_T005_Id
		LEFT JOIN D024 on D024_Id=T005_D024_Id		
		WHERE 
			T006_Id = '{$T006_Id}';
EOT;
	$row = mysql_fetch_assoc(mysql_query($sql));
	
	//Insere dados na Tabela de Requisição 
	$sql = <<<EOT
		INSERT INTO 
		T189 
			(T189_T006_Id, T189_D009_Id, T189_C007_Id, T189_Data_Criado, T189_Descricao_Produto, T189_Quantidade, T189_Observacao, T189_Detalhes, T189_Flag_Tipo) 
		VALUES 
			('{$T006_Id}', '{$D009_Id}', '{$C007_Id}', CURRENT_TIMESTAMP(), '{$row['T006_Descricao_Produto']}', '{$row['T006_Quantidade']}', '', 'Pedido Nº: {$row['T006_T005_Id']} - {$row['D024_Nome_Empresa']}', 'P' );
EOT;
	mysql_query($sql);
	break;

	case 'est003Balanco':
		$sql = isset($_REQUEST['sql']) ? $_REQUEST['sql'] : false;
		$retorno = $EST003->est003BalancoCiclico($sql);
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Operação realizada com sucesso.";
		} else {	
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est002CheckboxBalanco':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T066_Id = isset($r_T066_Id) ? $r_T066_Id : false;

		$usuarioAtual = ($selected == 'S') ? $g['usuarioAtual'] : "";
		mysql_query("UPDATE T066 SET T066_Flag_Balanco = '{$selected}', T066_C007_Id_Marcou_Balanco='{$usuarioAtual}' WHERE T066_Id = '{$T066_Id}'");

		$resposta['code'] = true;

	break;

	case 'est003AplicarNovosPrecos':
		$sql = base64_decode($r_sql);
		$sql = str_replace('LIMIT 0,100', '', $sql);
		$sql = str_replace('(1=1)', "(D009_Flag_Preco_Loja = 'S')", $sql);
		$res = mysql_query($sql);

		if (mysql_num_rows($res) == 0) {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum item foi selecionado.';

		} else {
			$uniqid = mysql_real_escape_string(uniqid());
			while ($row = mysql_fetch_assoc($res)) {
				if ($row['D009_Flag_Preco_Loja'] != 'S') { 
					continue; 
				}

				mysql_query("INSERT INTO T239 (T239_D009_Id,
											   T239_C007_Id, 
			  								   T239_Data_Hora, 
			  								   T239_Preco_Loja_Antigo, 
			  								   T239_Preco_Loja_Novo, 
			  								   T239_Processo_Id
									  )VALUES(
											   '{$row['D009_Id']}',
										       '{$g['usuarioAtual']}',
										       NOW(),
										       '{$row['D009_Preco_Loja']}',
										       '{$row['D009_Preco_Loja_Real']}', 
										       '{$uniqid}')");

				mysqli_query("UPDATE D009 SET D009_Preco_Loja               = '{$row['D009_Preco_Loja_Real']}',
									          D009_Preco_Loja_T001_Id       = D009_Preco_Loja_Real_T001_Id, 
										      D009_Preco_Loja_Demonstrativo = D009_Preco_Loja_Real_Demonstrativo,
										      D009_Flag_Preco_Loja          = 'N'
								        WHERE D009_Id = '{$row['D009_Id']}'");
			}

			$resposta['data'] = 'Itens processados com sucesso.';
			$resposta['uniqid'] = $uniqid;
			//mysql_query("UPDATE D009 SET D009_Flag_Preco_Loja = 'N' WHERE D009_C004_Id = '{$g['empresaAtual']}'");
		}
	break;

	case 'est003ReabastecimentoLoja':

		$sql = isset($r_sql) ? $r_sql : false;
		$retorno = $EST003->reabastecimentoLoja($sql);

		if(is_array($retorno)){

			if($retorno['totalErro'] > 0){

				$resposta['data']  = "Itens processados com sucesso (Total {$retorno['total']})";

				if(!empty($retorno['erroLocacaoEstoque']) && !empty($retorno['erroLocacaoLoja'])){
					$mensagemErro = $retorno['erroLocacaoEstoque'] . "<br><br>" . $retorno['erroLocacaoLoja'];
				} elseif(!empty($retorno['erroLocacaoEstoque'])) {
					$mensagemErro = $retorno['erroLocacaoEstoque'];
				} else {
					$mensagemErro = $retorno['erroLocacaoLoja'];
				}
				$resposta['erro']  = "Itens com erro (Total {$retorno['totalErro']}) - Verifique os erros abaixo dos itens que não foram inclusos na D.E:<br><br>{$mensagemErro}";

			} else {
				$resposta['data'] = 'Itens processados com sucesso!<br/>Total de itens: '.$retorno['total'];
			}

			$resposta['code']      = true;
			$resposta['T029_Id']   = $retorno['T029_Id'];
			$resposta['totalErro'] = $retorno['totalErro'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}

	break;

	case 'est003CheckboxGerarPedido':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T206_Id = isset($r_T206_Id) ? $r_T206_Id : false;

		mysql_query("UPDATE T206 SET T206_Flag_Marcado = '{$selected}' WHERE T206_Id = '{$T206_Id}'");
		$resposta['code'] = true;

	break;

	case 'mudarStatusColeta':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$flag_status = isset($r_flag_status) ? $r_flag_status : false;

		$retorno = $EST003->mudarStatusColeta($T224_Id, $flag_status);
		if($retorno === true){
			switch ($flag_status) {
				case '3':
					$status = "Aprovado";
				break;
				case '5':
					$status = "Aguardando Coleta";
				break;
				case '6':
					$status = "Em Coleta";
				break;
				case '7':
					$status = "Coletado";
				break;
				case '8':
					$status = "Aguardando Entrada";
				break;
			}
			$resposta['data'] = 'Ordem de compra modificada para "'.$status.'"';
			$resposta['code'] = true;
		} else {	
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";