
<?php
/**
* Tabela T006A
*/

// restricaoTravar
$conf['restricaoTravar']['T313'] = <<<'EOT'
	$retorno = true;
	//RESTRICAO AO EDITAR A QUANTIDADE SE FOR MAIOR QUE SALDO DISPONIVEL DO PEDIDO
	if (($campo === 'T313_Quantidade') && ($operacao == 'alterar')) {
		$sql  = " SELECT T313_T006_Id as T006_Id ";
		$sql .=   " FROM T313 ";
		$sql .=  " WHERE T313_Id = '$valorPk' ";
		$query = mysql_query($sql);
		$dados = mysql_fetch_array($query);

		if (!is_null($dados)) {
			$dados['Quantidade'] = $valor;

			require_once('bibliotecas/classes/EST002.php');
			$EST002 = new EST002();
	
			$validacao = $EST002->validarSaldoPedido($dados);
			if (is_string($validacao)) {
				$retorno = $validacao;
			}
		}
	}
	//RESTRICAO AO EDITAR A QUANTIDADE SE FOR MAIOR QUE SALDO DISPONIVEL DO PEDIDO
EOT;
