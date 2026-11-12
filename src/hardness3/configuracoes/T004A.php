
<?php
/**
* Tabela T004A
*/

$conf['triggers']['T004A']['updateAfter'] = <<<'EOT'

		$T004  = mysql_query("SELECT T004_Id,
									 T004_T003_Id,
									 T003A_Flag_Conversao_Unidade 
		                        FROM T004A 
						   LEFT JOIN T004 ON T004_Id = T004A_T004_Id
						   LEFT JOIN T003 ON T003_Id = T004_T003_Id
						   LEFT JOIN T003A ON T003_Id = T003A_T003_Id
							   WHERE T004A_Id = '{$chaveValor}'");
		$mT004 = mysql_fetch_array($T004);

		if($mT004['T003A_Flag_Conversao_Unidade'] == 'S'){
			require_once('bibliotecas/classes/CAD002.php');
			$CAD002 = new CAD002();
			
			$CAD002->conversaoUnidadeProduto($mT004['T004_Id'], false, false, false);
	
			mysqli_query("CALL T004_Gravar_Totalizacao_4('{$mT004['T004_Id']}')");
			mysqli_query("CALL T003_Gravar_Totalizacao_4('{$mT004['T004_T003_Id']}')");
		}
EOT;

// restricaoTravar
$conf['restricaoTravar']['T004A'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = "SELECT T006_Id
				  FROM T004A
			 LEFT JOIN T004 ON T004_Id = T004A_T004_Id
			 LEFT JOIN T006 ON T004_Id = T006_T004_Id
				 WHERE T004A_Id = '{$valorPk}'
				   AND T006_Id IS NOT NULL";
		$res = mysql_query($sql);
		$rows = mysql_numrows($res);
		if ($rows > 0) {
			$retorno = 'Este item já gerou pedido.';
		}
	}

	//RESTRICAO AO EDITAR OS CAMPOS DE QUANTIDADE/QUANTIDADE CONVERSAO CASO O PROCESSO ESTEJA ATIVO
	if (($campo === 'T004A_Quantidade_Conversao') && ($operacao == 'alterar')) {
		$utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
		if ($utilizaQuantidadeConversao) {
			$sql  = " SELECT D002A_Utiliza_Conversao ";
			$sql .=   " FROM T004 ";
			$sql .=        " LEFT JOIN T004A ON T004_Id = T004A_T004_Id ";
			$sql .=        " LEFT JOIN D009  ON D009_Id = T004_D009_Id ";
			$sql .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
			$sql .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
			$sql .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
			$sql .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
			$sql .=  " WHERE T004A_Id = '$valorPk' ";
			$query  = mysql_query("$sql");
			$dados  = mysql_fetch_array($query);

			$usaConversao = $dados['D002A_Utiliza_Conversao'] === 'S';

			if (!$usaConversao) {
				$retorno = 'Este produto não utiliza conversão e não é possivel alterar sua quantidade de conversão.<br><br>Edite o campo de quantidade.';
			}
		}
	}
	//RESTRICAO AO EDITAR OS CAMPOS DE QUANTIDADE/QUANTIDADE CONVERSAO CASO O PROCESSO ESTEJA ATIVO
EOT;
