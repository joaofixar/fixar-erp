<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) {

	case 'carregarLocacoes':
		$T006_Id = mysql_real_escape_string($r_T006_Id);
		$sql = "SELECT T006_D009_Id, T006_T066_Id FROM T006 WHERE T006_Id = '{$T006_Id}'";
		$resT006 = mysql_query($sql);
		$rowT006 = mysql_fetch_assoc($resT006);

		$extraD004 = "LEFT JOIN D004 ON D004_Id  = T066_D004_Id 
					  LEFT JOIN D009 ON D009_Id  = T066_D009_Id
					  LEFT JOIN D049 ON D049_Id  = D009_D049_Id
					  LEFT JOIN D001 ON D001_Id  = D049_D001_Id
					  LEFT JOIN D001A ON D001_Id = D001A_D001_Id
					  LEFT JOIN T066A ON T066_Id = T066A_T066_Id
					      WHERE T066_D009_Id = '{$rowT006['T006_D009_Id']}' 
					   ORDER BY IF(T066_Id='{$rowT006['T006_T066_Id']}',1,0) DESC, T066_Id ASC";

		$resposta['data']['T238_T066_Id'] 			 = gProcessaSelect(gGeraSelect('T066','T066_Id','D004_Local,FORMAT(T066_Quantidade_Estoque[[virgula]]2),T066_Id, if(T066A_Data_Validade = "0000-00-00","", concat("- VAL: ",DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y")))', $extraD004, false, false, "%1 (%2) - %3 %4"));
		$resposta['data']['T238_T066_Id_refreshBtn'] = base64_encode(serialize(array('T066','T066_Id','D004_Local,T066_Quantidade_Estoque', $extraD004, false, false, "%1 (%2)")));
		$resposta['data']['T006_D009_Id'] 			 = $rowT006['T006_D009_Id'];
	break;

	case 'est002SeparacaoPorLocacaoContinuar':
		require_once('bibliotecas/classes/GFormWrap.php');
		$form = new GFormWrap(array(
			'T005_Id' => $r_acaoId,
			'T005_Estoque_Box' => $r_T005_Estoque_Box
		));
		require_once("bibliotecas/classes/EST002.php");
		$EST002 = new EST002();
		$retorno = $EST002->SeparacaoPedido($form);
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est002SeparacaoPorLocacaoCPSeparados':
		$sql = <<<EOT
SELECT 
	*,
	GROUP_CONCAT(DISTINCT T066_Id SEPARATOR '|') AS T066_Id_Mais
FROM
	T006
LEFT JOIN T066 ON T066_D009_Id=T006_D009_Id
left join D009 on D009_Id=T006_D009_Id
left join D049 on D049_Id=D009_D049_Id
left join D001 on D001_Id=D049_D001_Id
left join T005 on T005_Id=T006_T005_Id
left join D006 on D006_Id=T006_D006_Id
WHERE
	T006_T005_Id='{$r_acaoId}'
	AND IFNULL(T006_Quantidade_Separacao, 0) < T006_Quantidade
	AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
GROUP BY T006_Id
EOT;
		$resT006 = mysql_query($sql);
		if (mysql_num_rows($resT006) == 0) {
			$resposta['code'] = false;
			$resposta['data'] = 'Todos os itens deste pedido já estão separados.';
		} else {
			$produtosSemLocal = 0;
			$locacaoSemEstoque = 0;
			while ($rowT006 = mysql_fetch_assoc($resT006)) {
				$T066_Id = $rowT006['T006_T066_Id'];
				if ($T066_Id) {
					$D004_Id = getD004($T066_Id);
					//echo "{$rowT006['T006_Id']} - {$rowT006['D001_Codigo_Produto']} - $T066_Id - $D004_Id <br/>";
					// Separa
					$rowT006['T006_Quantidade_Separacao'] = empty($rowT006['T006_Quantidade_Separacao']) ? 0 : $rowT006['T006_Quantidade_Separacao'];
					$rowT006['T006_Quantidade'] = empty($rowT006['T006_Quantidade']) ? 0 : $rowT006['T006_Quantidade'];
					if($g['C031']['permitirFaturarSemEstoque'] == 'S' || $rowT006['D006_Flag_Estoque']=='N'){
						$sqlQtdeSeparacao = mysql_query("SELECT SUM(T238_Quantidade_Separacao) as Quantidade_Separacao FROM T238 WHERE T238_T006_Id = '{$rowT006['T006_Id']}'");
						$rowQtdeSeparacao = mysql_fetch_array($sqlQtdeSeparacao);
						$rowT006['T006_Quantidade_Separacao'] = $rowQtdeSeparacao['Quantidade_Separacao'];
					} else {
						$sqlT066 = mysql_query("SELECT * FROM T066 WHERE T066_Id = '{$T066_Id}'");
						$rowT066 = mysql_fetch_array($sqlT066);
					}
					$qtd = $rowT006['T006_Quantidade'] - $rowT006['T006_Quantidade_Separacao'];

					//Verifica se a primeira locação possui estoque suficiente
					if($rowT066['T066_Quantidade_Estoque'] < $qtd && $g['C031']['permitirFaturarSemEstoque'] == 'N' && $rowT006['D006_Flag_Estoque']=='D'){
						$locacaoSemEstoque++;
					} else {
						$sql = "INSERT INTO T238 (T238_T005_Id, T238_T006_Id, T238_T066_Id, T238_D004_Id, T238_Quantidade_Separacao) VALUES ('{$rowT006['T006_T005_Id']}', '{$rowT006['T006_Id']}', '{$T066_Id}', '{$D004_Id}', '$qtd')";
						if (!empty($D004_Id)) {
							mysql_query("UPDATE T006 SET T006_Quantidade_Separacao='{$qtd}' WHERE T006_Id='{$rowT006['T006_Id']}'");
							mysql_query($sql);
						} else {
							$produtosSemLocal++;
						}
					}
				}
			}
			if($produtosSemLocal > 0){
				$resposta['code'] = false;
				if($produtosSemLocal == 1){
					$resposta['data'] = $produtosSemLocal . " produto não pode ser preenchido pois não possui locação.";
				} else {
					$resposta['data'] = $produtosSemLocal . " produtos não puderam ser preenchidos por não possuirem locação.";
				}
			}
			if($locacaoSemEstoque > 0){
				$resposta['code'] = false;
				if($locacaoSemEstoque == 1){
					$resposta['data'] = $locacaoSemEstoque . " produto não possui estoque suficiente na primeira locação.";
				} else {
					$resposta['data'] = $locacaoSemEstoque . " produtos não pussuem estoque suficiente na primeira locação.";
				}
			}
		}
	break;

	case 'carregaListaEnderecos':
		if(!empty($r_T268_D024_Id)){
			$D148_Id = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id='{$r_T268_D024_Id}' AND D148_Flag_Tipo = 'E'");
			if (mysql_num_rows($D148_Id) > 0) {
				$D148_Id = mysql_fetch_assoc($D148_Id);
				$D148_Id = $D148_Id['D148_Id'];
			} else {
				$D148_Id = false;
			}
			$array = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$r_T268_D024_Id.' ORDER BY D148_Id', true, $D148_Id);
			$array[0]['title'] = 'PADRÃO';
			$resposta['data']['T268_D148_Id'] = gProcessaSelect($array);
			$resposta['data']['T268_D148_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$r_T268_D024_Id.' ORDER BY D148_Id', true, $D148_Id)));
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível carregar a lista de endereços, destinatário não informado";
		}
	break;

	case 'fatPedidoSeparacaoBoxLeitor':

		$T005_Id = $r_T005_Id;
		$T005_Quantidade_Volumes = $r_T005_Quantidade_Volumes;

		require_once("bibliotecas/classes/EST002.php");
		$EST002 = new EST002();
		$retorno = $EST002->finalizarSeparacaoPedidoBoxLeitor($T005_Id, $T005_Quantidade_Volumes);
		$resposta = array('sucesso' => $retorno);
	break;

	case 'setPedidoFlagEtiquetaTransporteImpressa':

		$T005_Id = $r_T005_Id;
		mysql_query("UPDATE T005A SET T005A_Flag_Etiqueta_Transp_Impressa = 'S' WHERE T005A_T005_Id = '{$T005_Id}'");

		$resposta = array('sucesso' => true);
	break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
