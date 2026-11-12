
<?php
/**
* Tabela T004
*/
/*
* camposLabelForm
*/
$conf['camposLabelForm']['T004_Observacao_Entrega'] = 'Entrega dias';

// camposNaoUsar
if($g['C031']['usarDescontoProdutos'] == 'N'){
	$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
		'T004_Valor_Preco_Original',
		'T004_Percentual_Desconto'
	));
}

// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T004_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T004_D005_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T004_Descricao_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['Marca'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T004_Valor_Preco_Sem_Desconto_Unitario'] = <<<'EOT'
	if($g['C031']['permitirPrecoZeradoOrcamento'] == 'S'){
		$retorno = true;
	} else {
		$retorno = rNumeroMaiorQueZero($valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['T004_Quantidade'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T004_D037_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T004_D006_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T004_Flag_Destino_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

// camposTipo
//$conf['camposTipo']['T004_Quantidade'] = array('numero', 2);
//$conf['camposTipo']['T004_Valor_Preco_Sem_Desconto_Unitario'] = array('numero', 4);
//$conf['camposTipo']['T004_Valor_Preco_Unitario'] = array('numero', 4);

// triggers
$conf['triggers']['T004']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T004']['insertAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	mysql_query("CALL T004_Gravar_Totalizacao_4('$chaveValor')");

	$T004=mysqli_query("SELECT * FROM T004 LEFT JOIN T003 ON T003_Id = T004_T003_Id WHERE T004_Id='{$chaveValor}'");
	$mT004 = mysql_fetch_array($T004);

	$C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$mT004['T003_C007_Id_Vendedor_Interno']}'");
	$mC007=mysql_fetch_array($C007);

	$retorno = $VEN001->calcularComissao($chaveValor, 'T004');
	$upT004 = mysqli_query("UPDATE T004 SET T004_Valor_Comissao='{$retorno['valorComissao']}', T004_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T004_Id='{$chaveValor}'");

	$retorno = $VEN001->calcularComissaoTotal($mT004['T004_T003_Id'], 'T003');	
	$upT003 = mysqli_query("UPDATE T003 SET T003_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T003_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T003_Percentual_Comissao_Representante = '{$retorno['percentualComissaoRepresentante']}',
											T003_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T003_Percentual_Comissao_Externo = '{$retorno['percentualComissaoExterno']}',
											T003_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T003_Percentual_Comissao_Usuario = '{$retorno['percentualComissaoUsuario']}',
											T003_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}'
							WHERE T003_Id = '{$mT004['T004_T003_Id']}'");

	$upT003 = mysqli_query("UPDATE T003 SET T003_D006_Id='{$mT004['T004_D006_Id']}' WHERE T003_Id = '{$mT004['T004_T003_Id']}' AND (T003_D006_Id IS NULL OR T003_D006_Id <= 0)");
	if ($mT004['T004_T219_Id']>0){
		mysqli_query("update T219 set T219_Valor_Preco_Venda_Negociado='{$mT004['T004_Valor_Preco_Venda_Unitario']}' where T219_Id='{$mT004['T004_T219_Id']}'");
	}

	// Ao incluir um item no orçamento insere um registro na tabela estendida T004A (valor default do campo é 'N')
	mysqli_query("INSERT INTO T004A (T004A_T004_Id) VALUES ('{$chaveValor}')");

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT004();

	if($g['C031']['orcamentoAtualizaTabelaPrecoCliente'] == 'S'){
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		$CAD002->atualizaTabelaPrecoCliente($mT004['T003_D024_Id'], $mT004['T004_D009_Id'], $mT004['T004_Valor_Preco_Unitario']);
	}
EOT;

$conf['triggers']['T004']['updateBefore'] = <<<'EOT'
	$T004=mysqli_query("SELECT T004_Valor_Preco_Unitario,
							   D006_Flag_Destaca_Desconto_NF,
							   T003_Valor_Total
						  FROM T004 
					 LEFT JOIN D006 ON D006_Id = T004_D006_Id 
					 LEFT JOIN T003 ON T003_Id = T004_T003_Id
						 WHERE T004_Id='{$chaveValor}'");
	$mT004 = mysql_fetch_array($T004);

	$g['updateBefore-T004_Valor_Preco_Unitario'] = $mT004['T004_Valor_Preco_Unitario'];
	$g['updateBefore-D006_Flag_Destaca_Desconto_NF'] = $mT004['D006_Flag_Destaca_Desconto_NF'];
	$g['updateBefore-T003_Valor_Total'] = $mT004['T003_Valor_Total'];
EOT;

$conf['triggers']['T004']['updateAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	mysql_query("CALL T004_Gravar_Totalizacao_4('$chaveValor')");

	$T004=mysqli_query("SELECT * 
						  FROM T004 
					 LEFT JOIN T003 ON T003_Id = T004_T003_Id 
					 left join D009 on D009_Id = T004_D009_Id 
					 left join D049 on D049_Id = D009_D049_Id 
					 left join D006 on D006_Id = T004_D006_Id 
					 LEFT JOIN T004A ON T004_Id = T004A_T004_Id 
					    WHERE T004_Id='{$chaveValor}'");
	$mT004 = mysql_fetch_array($T004);

	if($mT004['T004A_Percentual_Desconto_CFOP'] > 0 && $mT004['D006_Percentual_Desconto_CFOP'] <= 0){
		// Foi alterada a CFOP, agora nao aplica mais desconto, precisa retirar o desconto do total
		$VEN001->aplicaDescontoCFOP('T004', 'T003', $chaveValor, $mT004['T004_T003_Id'], 'S');	
	}elseif($mT004['T004A_Percentual_Desconto_CFOP'] > 0 || $mT004['D006_Percentual_Desconto_CFOP'] > 0){
		$VEN001->aplicaDescontoCFOP('T004', 'T003', $chaveValor, $mT004['T004_T003_Id'], $mT004['D006_Flag_Destaca_Desconto_NF']);	
	} 

	$upT003 = mysqli_query("UPDATE T003 SET T003_D006_Id='{$mT004['T004_D006_Id']}' WHERE T003_Id = '{$mT004['T004_T003_Id']}' AND (T003_D006_Id != '{$mT004['T004_D006_Id']}' OR (T003_D006_Id IS NULL OR T003_D006_Id <= 0))");
	mysql_query("CALL T003_Gravar_Totalizacao_4('{$mT004['T004_T003_Id']}')");

	$C007=mysql_query("SELECT * FROM C007 LEFT JOIN C019 ON C019_C007_Id=C007_Id LEFT JOIN C021 on C021_Id=C019_C021_Id WHERE C007_Id='{$mT004['T003_C007_Id_Vendedor_Interno']}'");
	$mC007=mysql_fetch_array($C007);

	//calculo frete
	$dadosTabela = array('tabela' => 'T003', 
						 'tabelaId' => $mT004['T004_T003_Id'],
						 'campos' => 'T003_Valor_Total as valorTotal,
                                     T003_Peso_Total as pesoTotal,
                                     T003_Peso_Bruto_Total as valorBrutoTotal,
                                     T003_Total_Cubagem as totalCubagem');
	$valor_frete_calculado = $VEN001->calcularFrete($dadosTabela);

	$retorno = $VEN001->calcularComissao($chaveValor, 'T004');
	$upT004 = mysqli_query("UPDATE T004 SET T004_Valor_Comissao='{$retorno['valorComissao']}', T004_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T004_Id='{$chaveValor}'");

	$retorno = $VEN001->calcularComissaoTotal($mT004['T004_T003_Id'], 'T003');
	$upT003 = mysqli_query("UPDATE T003 SET T003_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T003_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T003_Percentual_Comissao_Representante = '{$retorno['percentualComissaoRepresentante']}',
											T003_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T003_Percentual_Comissao_Externo = '{$retorno['percentualComissaoExterno']}',
											T003_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T003_Percentual_Comissao_Usuario = '{$retorno['percentualComissaoUsuario']}',
											T003_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}',
											T003_Valor_Frete_Calculado = '{$valor_frete_calculado}'
							WHERE T003_Id = '{$mT004['T004_T003_Id']}'");

	/*
	$D083=mysql_query("select D083_Id,D083_Flag_Fornecedor from D083 where D083_D001_Id='{$mT004['D049_D001_Id']}' and D083_Flag_Cliente='S' and D083_D049_Id='{$mT004['D049_Id']}' and D083_D024_Id='{$mT004['T003_D024_Id']}'");
	if (mysql_num_rows($D083)<=0){
		mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) values ('{$mT004['D049_Id']}','{$mT004['D049_D001_Id']}','{$mT004['T003_D024_Id']}',curdate(),'S','{$mT004['T004_Codigo_Substituto']}')");
	} else {
		$mD083=mysql_fetch_array($D083);
		mysql_query("update D083 set D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='{$mT004['T004_Codigo_Substituto']}' where D083_Id='{$mD083['D083_Id']}'");
	}
	*/

	if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
		mysql_query("CALL T004_Gravar_Totalizacao_4('$chaveValor')");
		mysqli_query("UPDATE T004 SET T004_Valor_Preco_Unitario = T004_Valor_Preco_Bruto+ifnull((T004_Valor_Total_Agregados/T004_Quantidade),0),
		                                                          T004_Valor_Desconto_Unitario=((T004_Valor_Preco_Original*(T004_Valor_Preco_Unitario/100))),
		                          T004_Percentual_Desconto=((1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100) WHERE T004_Id = '$chaveValor'");
		mysql_query("CALL T004_Gravar_Totalizacao_4('$chaveValor')");
		mysqli_query("UPDATE T004 SET T004_Valor_Preco_Unitario = round(T004_Valor_Preco_Unitario/((T004_Valor_Preco_Unitario+(T004_Valor_IPI/T004_Quantidade)+(T004_Valor_ST/T004_Quantidade))/T004_Valor_Preco_Unitario),4),
		                              T004_Valor_Desconto_Unitario=((T004_Valor_Preco_Original*(T004_Valor_Preco_Unitario/100))),
		                          T004_Percentual_Desconto=((1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100) WHERE T004_Id = '$chaveValor'");
		mysql_query("CALL T004_Gravar_Totalizacao_4('$chaveValor')");
	} else {
		mysql_query("call T004_Gravar_Totalizacao_4('$chaveValor')");
	}


	require_once("bibliotecas/classes/CAD002.php");
    $CAD002 = new CAD002();
    $CAD002->definirCodigoFornecedor($mT004['D009_Id'], $mT004['T003_D024_Id'], $mT004['T004_Codigo_Substituto'], '', true, false);

	if ($mT004['T004_T219_Id']>0){
		mysqli_query("update T219 set T219_Valor_Preco_Venda_Negociado='{$mT004['T004_Valor_Preco_Unitario']}' where T219_Id='{$mT004['T004_T219_Id']}'");
		mysqli_query("update T225 left join T219 on T219_Id=T225_T219_Id set T225_Valor_Preco_Venda_Negociado='{$mT004['T004_Valor_Preco_Unitario']}',T225_Valor_Preco_Venda='{$mT004['T004_Valor_Preco_Original']}' where T219_Id='{$mT004['T004_T219_Id']}'");
		//mysql_query("update T219 set T219_Valor_Preco_Venda_Negociado='{$mT004['T004_Valor_Preco_Unitario']}' where T219_Id='{$mT004['T004_T219_Id']}'");
		//log("update T219 set T219_Valor_Preco_Venda_Negociado='{$mT004['T004_Valor_Preco_Unitario']}' where T219_Id='{$mT004['T004_T219_Id']}'");
	}

	require_once('bibliotecas/classes/NOTIFICACOES.php');
	$NOTIFICACOES = new NOTIFICACOES();
	$NOTIFICACOES->atualizarNotificacoesT004();

	if($g['updateBefore-T004_Valor_Preco_Unitario'] != $mT004['T004_Valor_Preco_Unitario'] && $g['C031']['orcamentoAtualizaTabelaPrecoCliente'] == 'S'){
		log('UPDATE TABELA PREÇO CLIENTE');
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		$CAD002->atualizaTabelaPrecoCliente($mT004['T003_D024_Id'], $mT004['T004_D009_Id'], $mT004['T004_Valor_Preco_Unitario']);
	}

	if($mT004['T003_Valor_Total'] != $g['updateBefore-T003_Valor_Total']){
		mysqli_query("UPDATE T003A SET T003A_Data_Ultima_Alteracao = NOW() WHERE T003A_T003_Id = '{$mT004['T004_T003_Id']}'");
	}

	if($mT004['D006_Flag_Tipo_Desconto'] == 'I' && $mT004['T003_Valor_Desconto_Manual'] > 0){
		// Se usar desconto incondicional na CFOP, vai descontar no valor dos impostos, precisa recalcular todos os produtos, chama trigger do T003
		mysql_query("UPDATE T003 SET T003_Valor_Desconto_Manual = T003_Valor_Desconto_Manual WHERE T003_Id = '{$mT004[T004_T003_Id]}'");
	}
EOT;

$conf['triggers']['T004']['deleteBefore'] = <<<'EOT'
	global $T004_T003_Id_Apos_Excluir;

	$T004=mysqli_query("SELECT * FROM T004 WHERE T004_Id='{$chaveValor}'");
	$mT004 = mysql_fetch_array($T004);

	$T004_T003_Id_Apos_Excluir = $mT004['T004_T003_Id'];

	require_once("bibliotecas/classes/VEN001.php");
    $VEN001 = new VEN001();
	$VEN001->processarNumeroItemOrcamento($chaveValor, '0');
EOT;

$conf['triggers']['T004']['deleteAfter'] = <<<'EOT'
	global $T004_T003_Id_Apos_Excluir;

	$T004=mysqli_query("SELECT * FROM T004 WHERE T004_Id='{$chaveValor}'");
	$mT004 = mysql_fetch_array($T004);

	$upT003 = mysqli_query("UPDATE T003 SET T003_Valor_Total_Comissao=(SELECT SUM(T004_Valor_Comissao) FROM T004 WHERE T004_T003_Id=T003_Id),T003_Percentual_Comissao=(T003_Valor_Total_Comissao/T003_Valor_Total_Produtos)*100 WHERE T003_Id = '{$mT004['T004_T003_Id']}'");

	log("SQL", "UPDATE T003 SET T003_Valor_Total_Comissao=(SELECT SUM(T004_Valor_Comissao) FROM T004 WHERE T004_T003_Id=T003_Id) WHERE T003_Id = '{$T004_T003_Id_Apos_Excluir}'");

	unset($T004_T003_Id_Apos_Excluir);
EOT;

// restricaoTravar
$conf['restricaoTravar']['T004'] = <<<'EOT'
	global $g;
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = "SELECT T006_Id FROM T006 WHERE T006_T004_Id = '{$valorPk}'";
		$res = mysql_query($sql);
		$rows = mysql_numrows($res);
		if ($rows > 0) {
			$retorno = 'Este item já gerou pedido.';
		}
	} else if ($operacao == 'incluir') {		
		if($g["C031"]["bloqueioOrcamentoConfirmado"] == 'S') {
			$sql = "SELECT T005_Id FROM T005 WHERE T005_T003_Id = '{$valorPkParent}'";
			$res = mysql_query($sql);
			$rows = mysql_numrows($res);
			if ($rows > 0) {
				$retorno = 'Este orçamento já gerou pedido.';
			}
		}
	}
	//Travas para os itens que possuem cotação relacionada
    /*if(($operacao == 'alterar' || $operacao == 'excluir') && $retorno == true) {
        $sql = mysql_query("SELECT T219_Id FROM T004 LEFT JOIN T219 ON T219_Id=T004_T219_Id WHERE T004_Id = '{$valorPk}'");
        $res = mysql_fetch_array($sql);
        if ($res['T219_Id'] > 0) {
            $retorno = 'Este item está vinculado a uma cotação.';
        }
    }*/

	//RESTRICAO AO EDITAR OS CAMPOS DE QUANTIDADE/QUANTIDADE CONVERSAO CASO O PROCESSO ESTEJA ATIVO
	if (($campo === 'T004_Quantidade') && ($operacao == 'alterar')) {
		$utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
		if ($utilizaQuantidadeConversao) {
			$sql  = " SELECT D002A_Utiliza_Conversao ";
			$sql .=   " FROM T004 ";
			$sql .=        " LEFT JOIN D009  ON D009_Id = T004_D009_Id ";
			$sql .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
			$sql .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
			$sql .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
			$sql .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
			$sql .=  " WHERE T004_Id = '$valorPk' ";
			$query  = mysql_query("$sql");
			$dados  = mysql_fetch_array($query);

			$usaConversao = $dados['D002A_Utiliza_Conversao'] === 'S';

			if ($usaConversao) {
				$retorno = 'Este produto utiliza conversão e não é possivel alterar sua quantidade.<br><br>Edite o campo da quantidade conversão.';
			}
		}
	}
	//RESTRICAO AO EDITAR OS CAMPOS DE QUANTIDADE/QUANTIDADE CONVERSAO CASO O PROCESSO ESTEJA ATIVO
EOT;
