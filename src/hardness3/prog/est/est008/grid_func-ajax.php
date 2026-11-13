<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/

// Classes
require_once('bibliotecas/classes/EST008.php');
$EST008 = new EST008();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'checkboxGerarOC':
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
		$EST008->updateT174_Flag_Gerar_OC($T174_Id, $selected);
	break;
	
	case 'CheckboxFlagIndisponivel':
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
		$EST008->updateT174_Flag_Indisponivel($T174_Id, $selected);
	break;
	
	case 'marcarMelhorPreco':
		$T010_Id = isset($_REQUEST['T010_Id']) ? $_REQUEST['T010_Id'] : false;
		$EST008->updateT010_Flag_Marca_Individual($T010_Id);
	break;
	
	case 'est008VerificarGerarOC':
		//$sql = "SELECT * FROM T173 LEFT JOIN T174 ON T174_T173_Id=T173_Id WHERE T174_T010_Id='{$r_T010_Id}' AND T174_Flag_Gerar_OC='1'";
		
		$sqlNomeForn = "SELECT * FROM T173 left join D024 on D024_Id=T173_D024_Id WHERE T173_Id='{$r_T173_Id}'";
		$retornoForn = mysql_query($sqlNomeForn);
		$rowForn = mysql_fetch_assoc($retornoForn);
		// Gerar ordem de compra de X itens, no valor de X para o fornecedor X?
		
		if (false) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para compra.";
		}else{
			$resposta['code'] = true;
			//$resposta['data'] = "Gerar ordem de compra de {$rowSomaProd['Total_Produtos']} itens, no valor de {$rowSomaValorTotal['valorTotal']} para o fornecedor {$rowForn['D024_Nome_Fantasia']}?";
			$resposta['data'] = "Deseja gerar ordem de compra para o fornecedor {$rowForn['D024_Nome_Empresa']} ?";
		}
	break;	
	
	case 'est008geraOCMarcados':
			require_once("bibliotecas/classes/EST008.php");
			$T010_Id 		= isset($r_T010_Id) ? $r_T010_Id : false;
			$retorno = $EST008->geraOCMarcados($T010_Id);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Ordem de compra gerada com sucesso";
			} else {
				// excluir
				$resposta['code'] = $retorno;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'est008geraOCEmpresa':
			require_once("bibliotecas/classes/EST008.php");
			$T010_Id 		= isset($r_T010_Id) ? $r_T010_Id : false;
			$T173_Id 		= isset($r_T173_Id) ? $r_T173_Id : false;
			$C004_Id 		= isset($r_C004_Id) ? $r_C004_Id : false;
			
			$resposta_T011_Id = '';
			$retorno = $EST008->geraOCEmpresa($T010_Id,$T173_Id,$C004_Id);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Ordem de compra gerada com sucesso";
				$resposta['respostaT011'] = $resposta_T011_Id;
			} else {
				// excluir
				$resposta['code'] = $retorno;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'est008liberacaoAlcada':
			require_once("bibliotecas/classes/EST008.php");
			$T010_Id 		= isset($r_T010_Id) ? $r_T010_Id : false;
			$T173_Id 		= isset($r_T173_Id) ? $r_T173_Id : false;
			
			$retorno = $EST008->enviarParaAnalise($T010_Id,$T173_Id);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Cotação enviada para Análise!";
			} else {
				// excluir
				$resposta['code'] = $retorno;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'est008MarcarMelhoresPrecos':
		$T010_Id = isset($r_T010_Id) ? $r_T010_Id : false;
		$sqlT174 = "SELECT * FROM T174 WHERE T174_T010_Id='{$T010_Id}'";
		$resultT174 = mysql_query($sqlT174);
		while($linhaT174 = mysql_fetch_array($resultT174)){
			$sql = <<<EOT
				SELECT *, (IF(T174_Valor_ST > 0, (T174_Valor_Custo_Unitario + (T174_Valor_Custo_Unitario * (T174_IPI/100)) + (T174_Valor_ST/T174_Quantidade)), (T174_Valor_Custo_Unitario + (T174_Valor_Custo_Unitario * (T174_IPI/100))))) as T174_Valor_Total FROM T174 left join T173 on T173_Id=T174_T173_Id left join D024 on D024_Id=T173_D024_Id WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$linhaT174['T174_T059_Id']}' AND (T174_Valor_Total<>'0.00') ORDER BY T174_Valor_Total ASC
EOT;
			$result = mysql_query($sql);
			$linha = mysql_fetch_array($result);
			$preco = gCorrigeNumero($linha['T174_Valor_Total']);
			if((!empty($preco)) || ($preco != null) || ($preco != '0,00')){
				$sqlUP = "UPDATE T174 SET T174_Flag_Gerar_OC='1' WHERE T174_Id='{$linha['T174_Id']}'";
				mysql_query($sqlUP);
			}
		}
		$resposta['code'] = true;
	break;
	
	case 'est008DesmarcarMelhoresPrecos':
		$T010_Id = isset($r_T010_Id) ? $r_T010_Id : false;
		
		$sqlT174 = "SELECT * FROM T174 WHERE T174_T010_Id='{$T010_Id}'";
		$resultT174 = mysql_query($sqlT174);
		while($linhaT174 = mysql_fetch_array($resultT174)){
			$sql =<<<EOT
			SELECT T174_Valor_Total, D024_Nome_Empresa, T174_Id FROM T174 left join T173 on T173_Id=T174_T173_Id left join D024 on D024_Id=T173_D024_Id WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$linhaT174['T174_T059_Id']}' AND (T174_Valor_Total<>'0.00') ORDER BY T174_Valor_Total ASC
EOT;
			$result = mysql_query($sql);
			$linha = mysql_fetch_array($result);
			$preco = gCorrigeNumero($linha['T174_Valor_Total']);
			if((!empty($preco)) || ($preco != null) || ($preco != '0,00')){
				$sqlUP = "UPDATE T174 SET T174_Flag_Gerar_OC='N' WHERE T174_Id='{$linha['T174_Id']}'";
				mysql_query($sqlUP);
			}
		}
	break;
	
	case 'est008DesmarcarTodos':
		$T010_Id = isset($r_T010_Id) ? $r_T010_Id : false;
		
		$sqlT174 = "SELECT * FROM T174 WHERE T174_T010_Id='{$T010_Id}'";
		$resultT174 = mysql_query($sqlT174);
		while($linhaT174 = mysql_fetch_array($resultT174)){
			$sql =<<<EOT
			SELECT T174_Valor_Total, D024_Nome_Empresa, T174_Id FROM T174 left join T173 on T173_Id=T174_T173_Id left join D024 on D024_Id=T173_D024_Id WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$linhaT174['T174_T059_Id']}' AND (T174_Valor_Total<>'0.00') ORDER BY T174_Valor_Total ASC
EOT;
			$result = mysql_query($sql);
			while($linha = mysql_fetch_array($result)){
				$preco = gCorrigeNumero($linha['T174_Valor_Total']);
				if((!empty($preco)) || ($preco != null) || ($preco != '0,00')){
					$sqlUP = "UPDATE T174 SET T174_Flag_Gerar_OC='N' WHERE T174_Id='{$linha['T174_Id']}'";
					mysql_query($sqlUP);
				}
			}
		}
	break;
	
	case 'duplicaProduto':
	global $g;
		$T059_Id = isset($r_T059_Id) ? $r_T059_Id : false;
		
		$sql = "SELECT * FROM T059 WHERE T059_Id='{$T059_Id}'";
		$result = mysql_query($sql);
		$linhaT059 = mysql_fetch_array($result);
		
		$insert = <<<EOT
		INSERT INTO T059
		(T059_T009_Id, 
		T059_T043_Id, 
		T059_T010_Id, 
		T059_D009_Id, 
		T059_D005_Id, 
		T059_C004_Id, 
		T059_Valor_Custo_Unitario, 
		T059_Valor_Total_Custo, 
		T059_Codigo_Produto_Fornecedor,
		T059_Percentual_Reducao_ICMS,
		T059_Valor_Base_ICMS,
		T059_Aliquota_ICMS,
		T059_Valor_ICMS,
		T059_Percentual_Reducao_IPI,
		T059_Valor_Base_IPI,
		T059_Aliquota_IPI,
		T059_Valor_IPI,
		T059_Prazo_Entrega,
		T059_Classificacao_Fiscal,
		T059_Flag_Imprimir,
		T059_Codigo_Produto,
		T059_Quantidade,
		T059_Descricao_Produto,
		T059_D037_Id,
		T059_T006_Id,
		T059_Valor_Custo_Unitario_1,
		T059_Valor_Custo_Unitario_2,
		T059_Flag_Gerar_OC_1,
		T059_Flag_Gerar_OC_2,
		T059_Valor_Custo_Unitario_3,
		T059_Valor_Custo_Unitario_4,
		T059_Flag_Gerar_OC_3,
		T059_Flag_Gerar_OC_4,
		T059_D009_Id_1,
		T059_D009_Id_2,
		T059_D009_Id_3,
		T059_D009_Id_4,
		T059_D009_Id_5,
		T059_Quantidade_1,
		T059_Quantidade_2,
		T059_Quantidade_3,
		T059_Quantidade_4,
		T059_Quantidade_5,
		T059_Valor_Custo_Unitario_Online_1,
		T059_Valor_Custo_Unitario_Online_2,
		T059_Valor_Custo_Unitario_Online_3,
		T059_Valor_Custo_Unitario_Online_4,
		T059_Valor_Custo_Unitario_Online_5,
		T059_Flag_Custo_Online_1,
		T059_Flag_Custo_Online_2,
		T059_Flag_Custo_Online_3,
		T059_Flag_Custo_Online_4,
		T059_Flag_Custo_Online_5,
		T059_Valor_Custo_Unitario_5,
		T059_Flag_Gerar_OC_5,
		T059_Quantidade_6,
		T059_Valor_Custo_Unitario_Online_6,
		T059_Flag_Custo_Online_6,
		T059_D009_Id_6,
		T059_T189_Id) 
							VALUES
		('{$linhaT059['T059_T009_Id']}',
		'{$linhaT059['T059_T043_Id']}', 
		'{$linhaT059['T059_T010_Id']}',
		'{$linhaT059['T059_D009_Id']}',
		'{$linhaT059['T059_D005_Id']}',
		'{$linhaT059['T059_C004_Id']}',
		'{$linhaT059['T059_Valor_Custo_Unitario']}', 
		'{$linhaT059['T059_Valor_Total_Custo']}',
		'{$linhaT059['T059_Codigo_Produto_Fornecedor']}',
		'{$linhaT059['T059_Percentual_Reducao_ICMS']}',
		'{$linhaT059['T059_Valor_Base_ICMS']}',
		'{$linhaT059['T059_Aliquota_ICMS']}',
		'{$linhaT059['T059_Valor_ICMS']}',
		'{$linhaT059['T059_Percentual_Reducao_IPI']}',
		'{$linhaT059['T059_Valor_Base_IPI']}',
		'{$linhaT059['T059_Aliquota_IPI']}',
		'{$linhaT059['T059_Valor_IPI']}',
		'{$linhaT059['T059_Prazo_Entrega']}',
		'{$linhaT059['T059_Classificacao_Fiscal']}',
		'{$linhaT059['T059_Flag_Imprimir']}',
		'{$linhaT059['T059_Codigo_Produto']}',
		'{$linhaT059['T059_Quantidade']}',
		'{$linhaT059['T059_Descricao_Produto']}',
		'{$linhaT059['T059_D037_Id']}',
		'{$linhaT059['T059_T006_Id']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_1']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_2']}',
		'{$linhaT059['T059_Flag_Gerar_OC_1']}',
		'{$linhaT059['T059_Flag_Gerar_OC_2']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_3']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_4']}',
		'{$linhaT059['T059_Flag_Gerar_OC_3']}',
		'{$linhaT059['T059_Flag_Gerar_OC_4']}',
		'{$linhaT059['T059_D009_Id_1']}',
		'{$linhaT059['T059_D009_Id_2']}',
		'{$linhaT059['T059_D009_Id_3']}',
		'{$linhaT059['T059_D009_Id_4']}',
		'{$linhaT059['T059_D009_Id_5']}',
		'{$linhaT059['T059_Quantidade_1']}',
		'{$linhaT059['T059_Quantidade_2']}',
		'{$linhaT059['T059_Quantidade_3']}',
		'{$linhaT059['T059_Quantidade_4']}',
		'{$linhaT059['T059_Quantidade_5']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_1']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_2']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_3']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_4']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_5']}',
		'{$linhaT059['T059_Flag_Custo_Online_1']}',
		'{$linhaT059['T059_Flag_Custo_Online_2']}',
		'{$linhaT059['T059_Flag_Custo_Online_3']}',
		'{$linhaT059['T059_Flag_Custo_Online_4']}',
		'{$linhaT059['T059_Flag_Custo_Online_5']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_5']}',
		'{$linhaT059['T059_Flag_Gerar_OC_5']}',
		'{$linhaT059['T059_Quantidade_6']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_6']}',
		'{$linhaT059['T059_Flag_Custo_Online_6']}',
		'{$linhaT059['T059_D009_Id_6']}',
		'{$linhaT059['T059_T189_Id']}')
EOT;
		$resultInsert = mysql_query($insert);
		$resposta['code'] = true;
	break;
	
	case 'duplicaProdutoForn':
		$T059_Id = isset($r_T059_Id) ? $r_T059_Id : false;
		$T174_Id = isset($r_T174_Id) ? $r_T174_Id : false;
		
		$sql = "SELECT * FROM T059 WHERE T059_Id='{$T059_Id}'";
		$result = mysql_query($sql);
		$linhaT059 = mysql_fetch_array($result);
		
		$T174 = mysql_query("SELECT * FROM T174 WHERE T174_Id='{$T174_Id}'");
		$mT174 = mysql_fetch_array($T174);
		
		$insert = <<<EOT
		INSERT INTO T059
		(T059_T009_Id, 
		T059_T043_Id, 
		T059_T010_Id, 
		T059_D009_Id, 
		T059_D005_Id, 
		T059_C004_Id, 
		T059_Valor_Custo_Unitario, 
		T059_Valor_Total_Custo, 
		T059_Codigo_Produto_Fornecedor,
		T059_Percentual_Reducao_ICMS,
		T059_Valor_Base_ICMS,
		T059_Aliquota_ICMS,
		T059_Valor_ICMS,
		T059_Percentual_Reducao_IPI,
		T059_Valor_Base_IPI,
		T059_Aliquota_IPI,
		T059_Valor_IPI,
		T059_Prazo_Entrega,
		T059_Classificacao_Fiscal,
		T059_Flag_Imprimir,
		T059_Codigo_Produto,
		T059_Quantidade,
		T059_Descricao_Produto,
		T059_D037_Id,
		T059_T006_Id,
		T059_Valor_Custo_Unitario_1,
		T059_Valor_Custo_Unitario_2,
		T059_Flag_Gerar_OC_1,
		T059_Flag_Gerar_OC_2,
		T059_Valor_Custo_Unitario_3,
		T059_Valor_Custo_Unitario_4,
		T059_Flag_Gerar_OC_3,
		T059_Flag_Gerar_OC_4,
		T059_D009_Id_1,
		T059_D009_Id_2,
		T059_D009_Id_3,
		T059_D009_Id_4,
		T059_D009_Id_5,
		T059_Quantidade_1,
		T059_Quantidade_2,
		T059_Quantidade_3,
		T059_Quantidade_4,
		T059_Quantidade_5,
		T059_Valor_Custo_Unitario_Online_1,
		T059_Valor_Custo_Unitario_Online_2,
		T059_Valor_Custo_Unitario_Online_3,
		T059_Valor_Custo_Unitario_Online_4,
		T059_Valor_Custo_Unitario_Online_5,
		T059_Flag_Custo_Online_1,
		T059_Flag_Custo_Online_2,
		T059_Flag_Custo_Online_3,
		T059_Flag_Custo_Online_4,
		T059_Flag_Custo_Online_5,
		T059_Valor_Custo_Unitario_5,
		T059_Flag_Gerar_OC_5,
		T059_Quantidade_6,
		T059_Valor_Custo_Unitario_Online_6,
		T059_Flag_Custo_Online_6,
		T059_D009_Id_6,
		T059_T189_Id,
		T059_T173_Id) 
		   VALUES
		('{$linhaT059['T059_T009_Id']}',
		'{$linhaT059['T059_T043_Id']}', 
		'{$linhaT059['T059_T010_Id']}',
		'{$linhaT059['T059_D009_Id']}',
		'{$linhaT059['T059_D005_Id']}',
		'{$linhaT059['T059_C004_Id']}',
		'{$linhaT059['T059_Valor_Custo_Unitario']}', 
		'{$linhaT059['T059_Valor_Total_Custo']}',
		'{$linhaT059['T059_Codigo_Produto_Fornecedor']}',
		'{$linhaT059['T059_Percentual_Reducao_ICMS']}',
		'{$linhaT059['T059_Valor_Base_ICMS']}',
		'{$linhaT059['T059_Aliquota_ICMS']}',
		'{$linhaT059['T059_Valor_ICMS']}',
		'{$linhaT059['T059_Percentual_Reducao_IPI']}',
		'{$linhaT059['T059_Valor_Base_IPI']}',
		'{$linhaT059['T059_Aliquota_IPI']}',
		'{$linhaT059['T059_Valor_IPI']}',
		'{$linhaT059['T059_Prazo_Entrega']}',
		'{$linhaT059['T059_Classificacao_Fiscal']}',
		'{$linhaT059['T059_Flag_Imprimir']}',
		'{$linhaT059['T059_Codigo_Produto']}',
		'{$linhaT059['T059_Quantidade']}',
		'{$linhaT059['T059_Descricao_Produto']}',
		'{$linhaT059['T059_D037_Id']}',
		'{$linhaT059['T059_T006_Id']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_1']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_2']}',
		'{$linhaT059['T059_Flag_Gerar_OC_1']}',
		'{$linhaT059['T059_Flag_Gerar_OC_2']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_3']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_4']}',
		'{$linhaT059['T059_Flag_Gerar_OC_3']}',
		'{$linhaT059['T059_Flag_Gerar_OC_4']}',
		'{$linhaT059['T059_D009_Id_1']}',
		'{$linhaT059['T059_D009_Id_2']}',
		'{$linhaT059['T059_D009_Id_3']}',
		'{$linhaT059['T059_D009_Id_4']}',
		'{$linhaT059['T059_D009_Id_5']}',
		'{$linhaT059['T059_Quantidade_1']}',
		'{$linhaT059['T059_Quantidade_2']}',
		'{$linhaT059['T059_Quantidade_3']}',
		'{$linhaT059['T059_Quantidade_4']}',
		'{$linhaT059['T059_Quantidade_5']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_1']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_2']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_3']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_4']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_5']}',
		'{$linhaT059['T059_Flag_Custo_Online_1']}',
		'{$linhaT059['T059_Flag_Custo_Online_2']}',
		'{$linhaT059['T059_Flag_Custo_Online_3']}',
		'{$linhaT059['T059_Flag_Custo_Online_4']}',
		'{$linhaT059['T059_Flag_Custo_Online_5']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_5']}',
		'{$linhaT059['T059_Flag_Gerar_OC_5']}',
		'{$linhaT059['T059_Quantidade_6']}',
		'{$linhaT059['T059_Valor_Custo_Unitario_Online_6']}',
		'{$linhaT059['T059_Flag_Custo_Online_6']}',
		'{$linhaT059['T059_D009_Id_6']}',
		'{$linhaT059['T059_T189_Id']}',
		'{$mT174['T174_T173_Id']}'
		)
EOT;
		$resultInsert = mysql_query($insert);
		$T059_Id_Novo= $g['mysqlLastId'];
		
		$inT174 = <<<EOT
		INSERT INTO T174 (
			T174_T010_Id,
			T174_T173_Id,
			T174_T059_Id,
			T174_Valor_Custo_Unitario,
			T174_IPI,
			T174_Quantidade,
			T174_Valor_Total,
			T174_D009_Id,
			T174_Quantidade_Forn,
			T174_Valor_Custo_Unitario_Forn,
			T174_IPI_Forn,
			T174_Hora_Email_Forn,
			T174_Flag_Indisponivel,
			T174_IPV,
			T174_Preco_Venda,
			T174_Prazo_Entrega_Dias,
			T174_Observacao,
			T174_Flag_Excluido,
			T174_Valor_ST,
			T174_T189_Id,
			T174_Descricao_Produto
		) VALUES (
			'{$mT174['T174_T010_Id']}',
			'{$mT174['T174_T173_Id']}',
			'{$T059_Id_Novo}',
			'{$mT174['T174_Valor_Custo_Unitario']}',
			'{$mT174['T174_IPI']}',
			'{$mT174['T174_Quantidade']}',
			'{$mT174['T174_Valor_Total']}',
			'{$mT174['T174_D009_Id']}',
			'{$mT174['T174_Quantidade_Forn']}',
			'{$mT174['T174_Valor_Custo_Unitario_Forn']}',
			'{$mT174['T174_IPI_Forn']}',
			'{$mT174['T174_Hora_Email_Forn']}',
			'{$mT174['T174_Flag_Indisponivel']}',
			'{$mT174['T174_IPV']}',
			'{$mT174['T174_Preco_Venda']}',
			'{$mT174['T174_Prazo_Entrega_Dias']}',
			'{$mT174['T174_Observacao']}',
			'{$mT174['T174_Flag_Excluido']}',
			'{$mT174['T174_Valor_ST']}',
			'{$mT174['T174_T189_Id']}',
			'{$mT174['T174_Descricao_Produto']}'
		)
EOT;
		mysql_query($inT174);
		$resposta['code'] = true;
		
	break;
	
	case 'ajaxUpdateT174_Flag_Excluido':
		$T174_Id = isset($r_T174_Id) ? $r_T174_Id : false;
		
		$sql = "UPDATE T174 SET T174_Flag_Excluido='S' WHERE T174_Id='{$T174_Id}'";
		$result = mysql_query($sql);
		$resposta['code'] = true;
	break;
	
	case 'est008updatecheckboxExcluido':
		$T174_Id = isset($r_T174_Id) ? $r_T174_Id : false;
		
		$sql = "UPDATE T174 SET T174_Flag_Excluido='N' WHERE T174_Id='{$T174_Id}'";
		$result = mysql_query($sql);
		$resposta['code'] = true;
	break;
	
	case 'est008FlagDesmarcar':
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;
		
		$sql = "UPDATE T189 SET T189_Flag_Comprar='N', T189_C007_Id_Comprar='0' where T189_Id='{$T189_Id}'";
		$result = mysql_query($sql);
		$resposta['code'] = true;
	break;

	case 'compraEstoqueProjetarFuturo':
		// Pode ser várias chaves (temos que identificar qual é a correta, que tem a mesma tabela)
		$pks = explode('|', $r_D009_Id);
		$pk = $pk_valor = null;
		foreach ($pks as $pksValor) {
			$pkTmp = explode('-', $pksValor);
			if (preg_replace('/_Id$/', '', $pkTmp[0]) == 'D009') {
				$pk = $pkTmp[0];
				$pk_valor = $pkTmp[1];
			}
		}

		preg_match("#T232_MesAno = '(.*)'#", $r_campoEditado, $mesAno);
		$mesAno = $mesAno[1];
		$r_novoValor = gCorrigeNumeroInverte($r_novoValor);

		$sql = "SELECT T232_Id FROM T232 WHERE T232_D009_Id = '{$pk_valor}' AND T232_MesAno = '{$mesAno}'";
		$res = mysql_query($sql);
		if (mysql_num_rows($res) > 0) {
			$sql = "UPDATE T232 SET T232_Quantidade = '{$r_novoValor}' WHERE T232_D009_Id = '{$pk_valor}' AND T232_MesAno = '{$mesAno}'";
			mysql_query($sql);
		} else {
			$sql = "INSERT INTO T232 (T232_D009_Id, T232_C004_Id, T232_MesAno, T232_Quantidade) VALUES ('{$pk_valor}', '{$g['empresaAtual']}', '{$mesAno}', '{$r_novoValor}')";
			mysql_query($sql);
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>