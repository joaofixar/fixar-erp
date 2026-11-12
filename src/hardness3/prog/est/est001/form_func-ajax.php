<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/EST001.php');
$est = new EST001;

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'atualizarCamposProdutos':
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T029_Id = isset($r_T029_Id) ? $r_T029_Id : false;
		
		global $g;
		
		$cClausula  = " select ";
		$cClausula .= " D009_Id, ";
		$cClausula .= " D001_Id, ";
		$cClausula .= " D009_Custo_Unitario(D009_Id) as Custo_Original,";
		$cClausula .= " D001_Flag_Ativo, ";
		$cClausula .= " D001_Descricao_Produto, ";
		$cClausula .= " D001_Codigo_Produto ";
		$cClausula .= " from D009 ";
		$cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
		$cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
		$cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
		$cClausula .= " where D009_Id='$D009_Id' ";
		$T030 = mysql_query("$cClausula");
		$mT030 = mysql_fetch_array($T030);

		//$retorno = $est->gravarProduto($D009_Id, $T029_Id);
		$mT030['T030_T066_Id'] 		   = gProcessaSelect(gGeraSelect('T066', 'T066_Id', "D004_Local,REPLACE(FORMAT(T066_Quantidade_Estoque[[virgula]] 2)[[virgula]] '.'[[virgula]] '[[virgula]]'),T066_Id", "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$D009_Id}' ORDER BY D004_Local, T066_Id ASC", true, false, '%1 (%2) - %3'));
		//$mT030['T030_T066_Id_Destino'] = gProcessaSelect(gGeraSelect('T066', 'T066_Id', "D004_Local,REPLACE(FORMAT(T066_Quantidade_Estoque[[virgula]] 2)[[virgula]] '.'[[virgula]] '[[virgula]]'),T066_Id", "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$D009_Id}' ORDER BY D004_Local, T066_Id ASC", true, false, '%1 (%2) - %3'));
		$mT030['T030_T066_Id_Destino'] = $est->populaT030_T066_Id($mT030['D009_Id'],$mT030['D001_Id']);

		
		if (!empty($mT030)) {
			$resposta['code'] = true;
			$resposta['data'] = $mT030;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;
	case 'atualizarEstoque':
			$T029_Id = isset($_REQUEST['T029_Id']) ? $_REQUEST['T029_Id'] : false;
			$retorno = $est->atualizarEstoque($T029_Id);

			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Estoque Atualizado!";
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
			break;

	case 'insereRegistroT001':
		global $g;
		$T030_Id = isset($r_T030_Id) ? $r_T030_Id : false;
		$retorno = $est->insereRegistroT001($T030_Id); 
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Inserido registro do T001";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>