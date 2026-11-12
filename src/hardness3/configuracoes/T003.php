<?php
/**
 * Tabela T003
 */
/*
* camposLabelForm
*/
$conf['camposLabelForm']['T003_Forma_Pagamento'] = 'Forma Pagamento';
$conf['camposLabelForm']['T003_Frete'] = 'Informações Frete';
$conf['camposLabelForm']['T003_Observacao'] = 'Observação cliente';
$conf['camposLabelForm']['T003_Prazo_Entrega'] = 'Entrega dias';

// camposNaoUsar
if($g['C031']['usarDescontoProdutos'] == 'N'){
	$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
		'T003_Valor_Desconto', 
		'T003_Valor_Desconto_Confirmado',
		'T003_Valor_Total_Produtos_Sem_Desconto',
		'T003_Valor_Total_Produtos_Sem_Desconto_Confirmado',
		'T003_D116_Id'
	));
}

// camposValorPadrao
$conf['camposValorPadrao']['T003_Forma_Pagamento'] = '28 DIAS';
$conf['camposValorPadrao']['T003_Frete'] = 'A COMBINAR';
$conf['camposValorPadrao']['T003_Prazo_Entrega'] = '';
$conf['camposValorPadrao']['T003_Validade_Proposta'] = '5 DIAS';

// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T003_Data_Emissao'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T003_D027_Id']  = <<<'EOT'
	$retorno = rVerificaFK('D027_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T003_D024_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D024_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_Codigo_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T003_C007_Id_Vendedor_Interno'] = <<<'EOT'
	$retorno = rVerificaFK('C007_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T003_D006_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D006_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T003_Flag_Lista_Preco'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T003_Flag_Acp'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T003_D013_Id'] = <<<'EOT'
    $retorno = rRegx('naoSelecionado', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T003_D024_Id'] = <<<'EOT'
	$retorno = '';
	if (empty($valor)) {
		$retorno = 'Selecione um Cliente';
	} else {
		$result = mysql_query("SELECT D024_Id, D024_Flag_Contribuinte, D024_Flag_Lista_Preco from D024 WHERE D024_Id='{$valor}'");
		$D024 = mysql_fetch_assoc($result);
		$D024['D024_Flag_Lista_Preco'] = trim($D024['D024_Flag_Lista_Preco']);
		if ($D024['D024_Flag_Contribuinte']<-1) {
			$retorno .= "Favor preencher o campo TIPO no cadastro do cliente<br />";
		}
		if (empty($D024['D024_Flag_Lista_Preco']))	{
			$retorno .= "Favor preencher o campo LISTA PREÇO no cadastro do cliente";
		}
		if (empty($retorno)) {
			$retorno = true;
		}
	}
EOT;

$conf['restricaoIncluirUpdate']['T003_Flag_Destino_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T003_Flag_Expedicao'] = <<<'EOT'
	if($g['C031']['modeloExpedicao'] == '2'){
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;

// triggers
$conf['triggers']['T003']['insertAfter'] = <<<'EOT'
	$T003  = mysql_query("SELECT T003_C004_Id, T003_D024_Id FROM T003 WHERE T003_Id='$chaveValor'");
	$mT003 = mysql_fetch_array($T003);

	mysqli_query("INSERT INTO T250 (T250_T003_Id, T250_Descricao, T250_Aliquota, T250_Aliquota_Original, T250_Tipo, T250_Grupo, T250_Nivel_Acesso, T250_Flag_Campo_Calculado, T250_Flag_Altera_Preco) SELECT '{$chaveValor}', T249_Descricao, T249_Aliquota, T249_Aliquota, T249_Tipo, T249_Grupo, T249_Nivel_Acesso, T249_Flag_Campo_Calculado, T249_Flag_Altera_Preco FROM T249 WHERE T249_C004_Id = '{$mT003['T003_C004_Id']}'");
	mysqli_query("CALL D024_Gravar_Totalizacao('{$mT003['T003_D024_Id']}')");

	require_once('bibliotecas/classes/ETC017.php');
	$ETC017 = new ETC017();
	$ETC017->inserirRegistroTarefa($chaveValor, 'T003');

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT003();
EOT;

$conf['triggers']['T003']['updateBefore'] = <<<'EOT'
	$sql = mysql_query("SELECT T003_D021_Id, T003_D024_Id FROM T003 WHERE T003_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);
	$g['updateBefore-T003_D021_Id'] = $row['T003_D021_Id'];
	$g['updateBefore-T003_D024_Id'] = $row['T003_D024_Id'];
EOT;

$conf['triggers']['T003']['updateAfter'] = <<<'EOT'
	$T004=mysql_query("select T004_Id from T004 where T004_T003_Id='$chaveValor'");
	while ($mT004=mysql_fetch_array($T004)){
		mysql_query("CALL T004_Gravar_Totalizacao_4('$mT004[T004_Id]')");
	}
	$sql = "SELECT T003_Flag_Perdido, T003_Flag_Status_Orcamento, T005_Id, T003_D021_Id, T003_D024_Id FROM T003 left join T005 on T005_T003_Id=T003_Id WHERE T003_Id = '{$chaveValor}';";
	$resultado = mysqli_query($sql);
	$row = mysql_fetch_assoc($resultado);
	if (($row['T003_Flag_Status_Orcamento'] != 'N') && ($row['T003_Flag_Status_Orcamento'] != 'A') && ($row['T003_Flag_Status_Orcamento'] != 'L')) {
		if (empty($row['T005_Id'])) {
			$sql = "UPDATE T003 SET T003_Flag_Status_Orcamento = 'P' WHERE T003_Id = '{$chaveValor}'";
			mysqli_query($sql);
			if($row['T003_Flag_Perdido'] == 'S'){
				$sql = "UPDATE T003 SET T003_Flag_Status_Orcamento = 'C' WHERE T003_Id = '{$chaveValor}'";
				mysqli_query($sql);
			}
		} else { 
			if ($row['T003_Flag_Perdido'] == 'S') {
				$sql = "UPDATE T003 SET T003_Flag_Status_Orcamento = 'C' WHERE T003_Id = '{$chaveValor}'";
				mysqli_query($sql);
			} else {
				$sql = "UPDATE T003 SET T003_Flag_Status_Orcamento = 'F' WHERE T003_Id = '{$chaveValor}'";
				mysqli_query($sql);
			}
		}
	}elseif($row['T003_Flag_Status_Orcamento'] == 'A' && $row['T003_Flag_Perdido'] == 'F'){	
		$sql = "UPDATE T003 SET T003_Flag_Status_Orcamento = 'F' WHERE T003_Id = '{$chaveValor}'";
		mysqli_query($sql);		
	}

	if($g['updateBefore-T003_D021_Id'] != $row['T003_D021_Id']){
		$sqlD021 = mysql_query("SELECT D021_Percentual_Comissao FROM D021 WHERE D021_Id = '{$row['T003_D021_Id']}'");
		$rowD021 = mysql_fetch_array($sqlD021);
		mysqli_query("UPDATE T003 SET T003_Percentual_Comissao_Representante = '{$rowD021['D021_Percentual_Comissao']}' WHERE T003_Id = '{$chaveValor}'");
	}

	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();
	//calculo Frete
	$dadosTabela = array('tabela' => 'T003', 
						 'tabelaId' => 	$chaveValor,
						 'campos' => 'T003_Valor_Total as valorTotal,
                                     T003_Peso_Total as pesoTotal,
                                     T003_Peso_Bruto_Total as valorBrutoTotal,
                                     T003_Total_Cubagem as totalCubagem');
	$valor_frete_calculado = $VEN001->calcularFrete($dadosTabela);

	$retorno = $VEN001->calcularComissaoTotal($chaveValor, 'T003');
	$upT003 = mysqli_query("UPDATE T003 SET T003_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T003_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T003_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T003_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T003_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}',
											T003_Valor_Frete_Calculado = '{$valor_frete_calculado}'
							WHERE T003_Id = '{$chaveValor}'");

	mysql_query("call T003_Gravar_Totalizacao_4('$chaveValor')");

	if($g['updateBefore-T003_D024_Id'] != $row['T003_D024_Id']){
		// Se alterar o cliente, ajusta os registros da tarefa
		$sqlD085 = mysql_query("SELECT D085_Id,
									   D085_T259_Id,
									   D085_D024_Id 
								  FROM D085
								 WHERE D085_T003_Id = '{$chaveValor}' ");

		while($rowD085 = mysql_fetch_array($sqlD085)){

			if($rowD085['D085_D024_Id'] != $row['T003_D024_Id']){
				mysqli_query("UPDATE D085 SET D085_D024_Id = '{$row['T003_D024_Id']}'
										WHERE D085_Id = '{$rowD085['D085_Id']}'");

				mysqli_query("UPDATE T259 SET T259_D024_Id = '{$row['T003_D024_Id']}'
										WHERE T259_Id = '{$rowD085['D085_T259_Id']}'");
			}
		}
		
		// Atualiza o crédito do cliente, teve casos que o valor estava desatualizado
		mysqli_query("CALL D024_Gravar_Totalizacao('{$row['T003_D024_Id']}')");
	}

	mysqli_query("UPDATE T003A SET T003A_Data_Ultima_Alteracao = NOW() WHERE T003A_T003_Id = '{$chaveValor}'");

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT003();
EOT;

// restricaoTravar
$conf['restricaoTravar']['T003'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = "SELECT T005_Id FROM T005 WHERE T005_T003_Id = '{$valorPk}' AND T005_Flag_Status != '8'";
		$res = mysql_query($sql);
		$rows = mysql_numrows($res);
		if ($rows > 0) {
			$retorno = 'Este orçamento já gerou pedido.';
		}
	} else {
		$retorno = true;
	}
EOT;

/**
* trocarEmpresa
*/
$conf['trocarEmpresa']['exibir'] = <<<'EOT'
	// Variáveis disponíveis: $r_tabela, $r_acaoId
	// Retornar 'true' irá ativar o ícone para trocar a empresa

	//Verifica se existem itens do orçamento vinculados a uma cotação
	$sql = mysql_query("SELECT T004_Id FROM T004 LEFT JOIN T219 ON T219_T004_Id=T004_Id WHERE T004_T003_Id='{$r_acaoId}' AND IFNULL(T219_Id,0) > 0");
	if(mysql_num_rows($sql) > 0){
		return false;
	}

	$sql = <<<SQL
		SELECT
			T005_Id
		FROM T003
		left join T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
		WHERE
			T003_Id = '{$r_acaoId}'
		group by T003_Id
SQL;
log($sql);
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if (empty($row['T005_Id'])) {
		return true;
	}
EOT;
$conf['trocarEmpresa']['tabelaFilho'] = 'T004';
$conf['trocarEmpresa']['executarNoFinal'] = <<<'EOT'
	// Variáveis disponíveis: $C004_Id_Novo, $Tabela, $Id
	mysql_query("UPDATE T004 SET T004_Flag_Transferencia = 'N', T004_C004_Id_Transferencia = 0 WHERE T004_T003_Id = '{$Id}'");

	// require_once('bibliotecas/classes/CAD002.php');
	// $CAD002 = new CAD002();
	// $T004 = mysql_query("SELECT * FROM T004 
 //                          	 LEFT JOIN T006 ON T006_T004_Id=T004_Id 
 //                          	 LEFT JOIN D009 ON D009_Id=T004_D009_Id
 //                          	 LEFT JOIN D049 ON D049_Id=D009_D049_Id
 //                          	 LEFT JOIN T003 ON T003_Id=T004_T003_Id
 //                          	 LEFT JOIN T001 ON T001_Id=D009_T001_Id
 //                          	 LEFT JOIN T219 ON T219_Id=T004_T219_Id
 //                          	     WHERE T004_T003_Id = '{$Id}'");
 //    while ($mT004 = mysql_fetch_array($T004)) {
 //        //Método de atualização do preço tabela dos produtos
 //        $CAD002->atualizaPrecoTabelaOrcamento($mT004);
 //    }
EOT;
