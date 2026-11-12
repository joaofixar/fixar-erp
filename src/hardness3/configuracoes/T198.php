
<?php
/**
* Tabela T004A
*/

// restricaoTravar
$conf['restricaoTravar']['T198'] = <<<'EOT'
	$retorno = true;
	//RESTRICAO AO EDITAR OS CAMPOS DE QUANTIDADE/QUANTIDADE CONVERSAO CASO O PROCESSO ESTEJA ATIVO
	if ($valorPk > 0) {
		if (($campo === 'T198_Quantidade') && ($operacao == 'alterar')) {
			$utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
			if ($utilizaQuantidadeConversao) {
				$sql  = " SELECT D002A_Utiliza_Conversao ";
				$sql .=   " FROM T198 ";
				$sql .=        " LEFT JOIN D009  ON D009_Id = T198_D009_Id ";
				$sql .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
				$sql .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
				$sql .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
				$sql .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
				$sql .=  " WHERE T198_Id = '$valorPk' ";
				$query  = mysql_query("$sql");
				$dados  = mysql_fetch_array($query);
	
				$usaConversao = $dados['D002A_Utiliza_Conversao'] === 'S';
	
				if ($usaConversao) {
					$retorno = 'Este produto utiliza conversão e não é possivel alterar sua quantidade.<br><br>Edite o campo da quantidade conversão.';
				}
			}
		}
		
		if (($campo === 'T198_Quantidade_Conversao') && ($operacao == 'alterar')) {
			$utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
			if ($utilizaQuantidadeConversao) {
				$sql  = " SELECT D002A_Utiliza_Conversao ";
				$sql .=   " FROM T198 ";
				$sql .=        " LEFT JOIN D009  ON D009_Id = T198_D009_Id ";
				$sql .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
				$sql .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
				$sql .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
				$sql .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
				$sql .=  " WHERE T198_Id = '$valorPk' ";
				$query  = mysql_query("$sql");
				$dados  = mysql_fetch_array($query);
	
				$usaConversao = $dados['D002A_Utiliza_Conversao'] === 'S';
	
				if (!$usaConversao) {
					$retorno = 'Este produto não utiliza conversão e não é possivel alterar sua quantidade de conversão.<br><br>Edite o campo de quantidade.';
				}
			}
		}
	}
	//RESTRICAO AO EDITAR OS CAMPOS DE QUANTIDADE/QUANTIDADE CONVERSAO CASO O PROCESSO ESTEJA ATIVO
EOT;
