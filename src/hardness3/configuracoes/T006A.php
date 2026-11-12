
<?php
/**
* Tabela T006A
*/

$conf['triggers']['T006A']['updateAfter'] = <<<'EOT'
		
		$T006  = mysql_query("SELECT T006_Id,
									 T006_T005_Id,
									 T005A_Flag_Conversao_Unidade 
		                        FROM T006A 
						   LEFT JOIN T006 ON T006_Id = T006A_T006_Id
						   LEFT JOIN T005 ON T005_Id = T006_T005_Id
						   LEFT JOIN T005A ON T005_Id = T005A_T005_Id
							   WHERE T006A_Id = '{$chaveValor}'");
		$mT006 = mysql_fetch_array($T006);

		if($mT006['T005A_Flag_Conversao_Unidade'] == 'S'){ // teste para conversao
			
			require_once('bibliotecas/classes/CAD002.php');
			$CAD002 = new CAD002();
			
			$CAD002->conversaoUnidadeProduto(false, $mT006['T006_Id'], false, false);
	
			mysqli_query("CALL T006_Gravar_Totalizacao_4('{$mT006['T006_Id']}')");
			mysqli_query("CALL T005_Gravar_Totalizacao_4('{$mT006['T006_T005_Id']}')");
		}
EOT;

// restricaoTravar
$conf['restricaoTravar']['T006A'] = <<<'EOT'
	$retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T006_Id
				  FROM T006A 
		     LEFT JOIN T006 ON T006_Id=T006A_T006_Id
			 LEFT JOIN T005 ON T005_Id=T006_T005_Id 
			     WHERE T006A_Id = '{$valorPk}' 
				   AND (T005_Flag_Status>0 
				   AND T005_Flag_Status !='' 
				   AND T005_Flag_Status IS NOT NULL 
				   AND T005_Flag_Status!=14 
				   AND T005_Flag_Status!=15 
				   AND T005_Flag_Status!=9 
				   AND T005_Flag_Status!=26 
				   AND T005_Flag_Status!=22)";

        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0) {
        	$row = mysql_fetch_array($res);
            $retorno = 'Este pedido já foi faturado.';
        }
    } 

	//RESTRICAO AO EDITAR OS CAMPOS DE QUANTIDADE/QUANTIDADE CONVERSAO CASO O PROCESSO ESTEJA ATIVO
	if (($campo === 'T006A_Quantidade_Conversao') && ($operacao == 'alterar')) {
		$utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
		if ($utilizaQuantidadeConversao) {
			$sql  = " SELECT D002A_Utiliza_Conversao ";
			$sql .=   " FROM T006 ";
			$sql .=        " LEFT JOIN T006A ON T006_Id = T006A_T006_Id ";
			$sql .=        " LEFT JOIN D009  ON D009_Id = T006_D009_Id ";
			$sql .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
			$sql .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
			$sql .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
			$sql .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
			$sql .=  " WHERE T006A_Id = '$valorPk' ";
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
