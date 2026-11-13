<?php
/*
* camposNaoUsar
*/
if($g['C031']['usarDescontoProdutos'] == 'N'){
	$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
		'T006_Valor_Preco_Original',
		'T006_Percentual_Desconto'
	));
}

/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['T006_T005_Id'] = <<<'EOT'
	$retorno = rVerificaFK('T005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T006_Descricao_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T006_Valor_Preco_Unitario'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T006_D037_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D037_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T006_D005_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T006_Valor_Preco_Sem_Desconto_Unitario'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T006_Quantidade'] = <<<'EOT'
	$sql = "SELECT T006_Quantidade_Separacao FROM T006 WHERE T006_Id = {$acaoId}";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if ($row['T006_Quantidade_Separacao'] > 0 && gCorrigeNumeroInverte($valor) < $row['T006_Quantidade_Separacao']) {
		$row['T006_Quantidade_Separacao'] = gCorrigeNumero($row['T006_Quantidade_Separacao']);
		$retorno = "Não foi possível alterar! Este produto já está separado no estoque ({$row['T006_Quantidade_Separacao']} itens separados).<br/>Por favor, verifique com o setor de expedição.";
	} else {
		$retorno = rNumeroMaiorQueZero($valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['T006_D006_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T006_Flag_Destino_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

/**
* camposTipo
*/

//$conf['camposTipo']['T006_Quantidade'] = array('numero', 2);
$conf['camposTipo']['T006_Quantidade_Entregar_Temp'] = array('numero', 0);

/**
* triggers
*/
$conf['triggers']['T006']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T006']['insertAfter'] = <<<'EOT'
	global $g;
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	mysql_query("CALL T006_Gravar_Totalizacao_4('{$chaveValor}')");
	
	$T006=mysqli_query("SELECT * FROM T006 LEFT JOIN T005 ON T005_Id = T006_T005_Id LEFT JOIN D009 ON D009_Id=T006_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id WHERE T006_Id='{$chaveValor}'");
	$mT006 = mysql_fetch_array($T006);

	if($mT006['D049_Flag_Tipo'] == 'PD'){
		require_once('bibliotecas/classes/VEN002.php');
		$VEN002 = new VEN002();
		$VEN002->copiarEngenhariaProduto($chaveValor);
	}

	$C007=mysql_query("SELECT * FROM C007 LEFT JOIN C019 ON C019_C007_Id=C007_Id LEFT JOIN C021 on C021_Id=C019_C021_Id WHERE C007_Id='{$mT006['T005_C007_Id_Vendedor_Interno']}'");
	$mC007=mysql_fetch_array($C007);

	if($g['C031']['calcularVolumePedido'] == 'S'){
		$sqlT006 = mysql_query("SELECT SUM(T006_Quantidade/D001_Qte_Embalagem) as Volumes FROM T006 
								LEFT JOIN D009 ON D009_Id = T006_D009_Id
								LEFT JOIN D049 ON D049_Id = D009_D049_Id
								LEFT JOIN D001 ON D001_Id = D049_D001_Id 
								WHERE T006_T005_Id = '{$mT006['T006_T005_Id']}'");
		$resT006 = mysql_fetch_array($sqlT006);

		mysqli_query("UPDATE T005 SET T005_Quantidade_Volumes = '{$resT006['Volumes']}' WHERE T005_Id = '{$mT006['T006_T005_Id']}'");
	}

	$retorno = $VEN001->calcularComissao($chaveValor, 'T006');
	$upT006 = mysqli_query("UPDATE T006 SET T006_Valor_Comissao='{$retorno['valorComissao']}', T006_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T006_Id='{$chaveValor}'");
	
	$retorno = $VEN001->calcularComissaoTotal($mT006['T006_T005_Id'], 'T005');
	$upT005 = mysqli_query("UPDATE T005 SET T005_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T005_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T005_Percentual_Comissao_Representante = '{$retorno['percentualComissaoRepresentante']}',
											T005_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T005_Percentual_Comissao_Externo = '{$retorno['percentualComissaoExterno']}',
											T005_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T005_Percentual_Comissao_Usuario = '{$retorno['percentualComissaoUsuario']}',
											T005_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}'
							WHERE T005_Id = '{$mT006['T006_T005_Id']}'");
	

	$upT005 = mysqli_query("UPDATE T005 SET T005_D006_Id='{$mT006['T006_D006_Id']}' WHERE T005_Id = '{$mT006['T006_T005_Id']}' AND (T005_D006_Id IS NULL OR T005_D006_Id <= 0)");

	mysqli_query("call T006_Gravar_Totalizacao_4('$chaveValor')");
	$T006=mysqli_query("select T006_T005_Id from T006 where T006_Id='$chaveValor'");
	$mT006=mysql_fetch_array($T006);
	mysqli_query("call T005_Gravar_Totalizacao_4('$mT006[T006_T005_Id]')");
	
	if($g['C031']['precopadraoVendaProduto'] == 'liquido'){
	    mysqli_query("UPDATE T006 SET T006_Valor_Preco_Bruto='0.00',T006_Valor_Preco_Original_Bruto='0.00',T006_Valor_Desconto_Unitario_Bruto='0.00',T006_Percentual_Desconto_Bruto='0.00' WHERE T006_Id={$chaveValor}");
	}

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT006();

	if($g['C031']['pedidoAtualizaTabelaPrecoCliente'] == 'S'){
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		$CAD002->atualizaTabelaPrecoCliente($mT006['T005_D024_Id'], $mT006['T006_D009_Id'], $mT006['T006_Valor_Preco_Unitario']);
	}
EOT;

$conf['triggers']['T006']['updateBefore'] = <<<'EOT'
	$sql = "SELECT T006_Flag_Reservar_Estoque, T006_Quantidade, T006_Prioridade FROM T006 WHERE T006_Id = '{$chaveValor}'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$g['updateBefore-T006_Flag_Reservar_Estoque'] = $row['T006_Flag_Reservar_Estoque'];
	$g['updateBefore-T006_Quantidade'] = $row['T006_Quantidade'];
	$g['updateBefore-T006_Prioridade'] = $row['T006_Prioridade'];
	$g['updateBefore-T006_Valor_Preco_Unitario'] = $row['T006_Valor_Preco_Unitario'];
EOT;

$conf['triggers']['T006']['updateAfter'] = <<<'EOT'
	global $g;	
	// Verifica se o campo T006_Flag_Reservar_Estoque foi alterado
	$sql = "SELECT T006_D009_Id, 
				   T006_Flag_Reservar_Estoque, 
				   T006_Quantidade, 
				   T006_Prioridade, 
				   D006_Flag_Tipo_Desconto,
				   T006_T005_Id,
				   D006_Percentual_Desconto_CFOP,
				   D006_Flag_Destaca_Desconto_NF,
				   T006A_Percentual_Desconto_CFOP,
				   T005_Flag_Status 
			  FROM T006 
		 LEFT JOIN D006 ON D006_Id = T006_D006_Id 
		 LEFT JOIN T006A ON T006_Id = T006A_T006_Id
		 LEFT JOIN T005 ON T005_Id = T006_T005_Id
		     WHERE T006_Id = '{$chaveValor}'";

	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if ($g['updateBefore-T006_Flag_Reservar_Estoque'] != $row['T006_Flag_Reservar_Estoque'] || $g['updateBefore-T006_Quantidade'] != $row['T006_Quantidade'] || $g['updateBefore-T006_Prioridade'] != $row['T006_Prioridade']) {
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		$CAD002->D001_reprocessa_historico(0, $row['T006_D009_Id'], 1, true);
	}
	
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	if($row['T006A_Percentual_Desconto_CFOP'] > 0 && $row['D006_Percentual_Desconto_CFOP'] <= 0){
		// Foi alterada a CFOP, agora nao aplica mais desconto, precisa retirar o desconto do total
		$VEN001->aplicaDescontoCFOP('T006', 'T005', $chaveValor, $row[T006_T005_Id], 'S');	
	}elseif($row['T006A_Percentual_Desconto_CFOP'] > 0 || $row['D006_Percentual_Desconto_CFOP'] > 0){
		$VEN001->aplicaDescontoCFOP('T006', 'T005', $chaveValor, $row[T006_T005_Id], $row['D006_Flag_Destaca_Desconto_NF']);	
	} 

	mysql_query("CALL T006_Gravar_Totalizacao_4('{$chaveValor}')");	
	
	$T006=mysqli_query("SELECT * FROM T006 LEFT JOIN T005 ON T005_Id = T006_T005_Id WHERE T006_Id='{$chaveValor}'");
	$mT006 = mysql_fetch_array($T006);

	$upT005 = mysqli_query("UPDATE T005 SET T005_D006_Id='{$mT006['T006_D006_Id']}' WHERE T005_Id = '{$mT006['T006_T005_Id']}' AND (T005_D006_Id != '{$mT006['T006_D006_Id']}' OR (T005_D006_Id IS NULL OR T005_D006_Id <= 0))");

	$C007=mysql_query("SELECT * FROM C007 LEFT JOIN C019 ON C019_C007_Id=C007_Id LEFT JOIN C021 on C021_Id=C019_C021_Id WHERE C007_Id='{$mT006['T005_C007_Id_Vendedor_Interno']}'");
	$mC007=mysql_fetch_array($C007);

	if($g['C031']['calcularVolumePedido'] == 'S'){
		$sqlT006 = mysql_query("SELECT SUM(T006_Quantidade/D001_Qte_Embalagem) as Volumes FROM T006 
								LEFT JOIN D009 ON D009_Id = T006_D009_Id
								LEFT JOIN D049 ON D049_Id = D009_D049_Id
								LEFT JOIN D001 ON D001_Id = D049_D001_Id 
								WHERE T006_T005_Id = '{$mT006['T006_T005_Id']}'");
		$resT006 = mysql_fetch_array($sqlT006);

		mysqli_query("UPDATE T005 SET T005_Quantidade_Volumes = '{$resT006['Volumes']}' WHERE T005_Id = '{$mT006['T006_T005_Id']}'");
	}
	mysql_query("CALL T005_Gravar_Totalizacao_4('{$mT006['T006_T005_Id']}')");

	//calculo Frete
	$dadosTabela = array('tabela' => 'T005', 
						 'tabelaId' => 	$mT006['T006_T005_Id'],
						 'campos' => 'T005_Valor_Total as valorTotal,
                                     T005_Peso_Total as pesoTotal,
                                     T005_Peso_Bruto as valorBrutoTotal,
                                     T005_Total_Cubagem as totalCubagem');
	$valor_frete_calculado = $VEN001->calcularFrete($dadosTabela);

	$retorno = $VEN001->calcularComissao($chaveValor, 'T006');
	$upT006 = mysqli_query("UPDATE T006 SET T006_Valor_Comissao='{$retorno['valorComissao']}', T006_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T006_Id='{$chaveValor}'");
	
	$retorno = $VEN001->calcularComissaoTotal($mT006['T006_T005_Id'], 'T005');
	$upT005 = mysqli_query("UPDATE T005 SET T005_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T005_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T005_Percentual_Comissao_Representante = '{$retorno['percentualComissaoRepresentante']}',
											T005_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T005_Percentual_Comissao_Externo = '{$retorno['percentualComissaoExterno']}',
											T005_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T005_Percentual_Comissao_Usuario = '{$retorno['percentualComissaoUsuario']}',
											T005_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}',
											T005_Valor_Frete_Calculado = '{$valor_frete_calculado}'
							WHERE T005_Id = '{$mT006['T006_T005_Id']}'");

    if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
		mysql_query("CALL T006_Gravar_Totalizacao_4('$chaveValor')");
		mysqli_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario = T006_Valor_Preco_Bruto+ifnull(T006_Valor_Frete_Unitario,0)+ifnull((T006_Valor_Total_Agregados/T006_Quantidade),0), 
									  T006_Valor_Desconto_Unitario=((T006_Valor_Preco_Original*(T006_Valor_Preco_Sem_Desconto_Unitario/100))),
	                                  T006_Percentual_Desconto=((1-(T006_Valor_Preco_Sem_Desconto_Unitario/T006_Valor_Preco_Original))*100)
                                WHERE T006_Id = '$chaveValor'");
		mysql_query("CALL T006_Gravar_Totalizacao_4('$chaveValor')");
		mysqli_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario = round(T006_Valor_Preco_Sem_Desconto_Unitario/((T006_Valor_Preco_Sem_Desconto_Unitario+(T006_Valor_IPI/T006_Quantidade)+(T006_Valor_ICMS_Substituicao_Retencao/T006_Quantidade))/T006_Valor_Preco_Sem_Desconto_Unitario),4),
									  T006_Valor_Desconto_Unitario=((T006_Valor_Preco_Original*(T006_Valor_Preco_Sem_Desconto_Unitario/100))),
	                                  T006_Percentual_Desconto=((1-(T006_Valor_Preco_Sem_Desconto_Unitario/T006_Valor_Preco_Original))*100)
		                        WHERE T006_Id = '$chaveValor'");
		mysql_query("CALL T006_Gravar_Totalizacao_4('$chaveValor')");
	} else {
		mysqli_query("call T006_Gravar_Totalizacao_4('$chaveValor')");
        mysqli_query("UPDATE T006 SET T006_Valor_Preco_Bruto='0.00',T006_Valor_Preco_Original_Bruto='0.00',T006_Valor_Desconto_Unitario_Bruto='0.00',T006_Percentual_Desconto_Bruto='0.00' WHERE T006_Id={$chaveValor}");
	}
	$T006=mysql_query("select * from T006 left join D009 on D009_Id=T006_D009_Id left join D049 on D049_Id=D009_D049_Id left join T005 on T005_Id=T006_T005_Id where T006_Id='$chaveValor'");
	$mT006=mysql_fetch_array($T006);
	mysql_query("call T005_Gravar_Totalizacao_4('$mT006[T006_T005_Id]')");

	/*
	$D083=mysql_query("select D083_Id,D083_Flag_Fornecedor from D083 where D083_D001_Id='{$mT006['D049_D001_Id']}' and D083_Flag_Cliente='S' and D083_D049_Id='{$mT006['D049_Id']}' and D083_D024_Id='{$mT006['T005_D024_Id']}'");
	if (mysql_num_rows($D083)<=0){
		mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) values ('{$mT006['D049_Id']}','{$mT006['D049_D001_Id']}','{$mT006['T005_D024_Id']}',curdate(),'S','{$mT006['T006_Codigo_Substituto']}')");
	} else {
		$mD083=mysql_fetch_array($D083);
		mysql_query("update D083 set D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='{$mT006['T006_Codigo_Substituto']}' where D083_Id='{$mD083['D083_Id']}'");
	}
	*/
	require_once("bibliotecas/classes/CAD002.php");
    $CAD002 = new CAD002();
    $CAD002->definirCodigoFornecedor($mT006['D009_Id'], $mT006['T005_D024_Id'], $mT006['T006_Codigo_Substituto'], '', true, false);

    require_once('bibliotecas/classes/NOTIFICACOES.php');
	$NOTIFICACOES = new NOTIFICACOES();
	$NOTIFICACOES->atualizarNotificacoesT006();

	if($g['updateBefore-T006_Valor_Preco_Unitario'] != $mT006['T006_Valor_Preco_Unitario'] && $g['C031']['pedidoAtualizaTabelaPrecoCliente'] == 'S'){
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		$CAD002->atualizaTabelaPrecoCliente($mT006['T005_D024_Id'], $mT006['T006_D009_Id'], $mT006['T006_Valor_Preco_Unitario']);
	}

	if($row['D006_Flag_Tipo_Desconto'] == 'I' && $mT006['T005_Valor_Desconto_Manual'] > 0){
		// Se usar desconto incondicional na CFOP, vai descontar no valor dos impostos, precisa recalcular todos os produtos, chama a trigger T005
		mysql_query("UPDATE T005 SET T005_Valor_Desconto_Manual = T005_Valor_Desconto_Manual WHERE T005_Id = '{$mT006[T006_T005_Id]}'");
	}
EOT;

$conf['triggers']['T006']['deleteBefore'] = <<<'EOT'
	global $T006_T005_Id_Apos_Excluir, $g;
	$sql = mysql_query("SELECT T006_T005_Id, T006_Quantidade, D001_Qte_Embalagem from T006 
						LEFT JOIN D009 ON D009_Id = T006_D009_Id
						LEFT JOIN D049 ON D049_Id = D009_D049_Id
						LEFT JOIN D001 ON D001_Id = D049_D001_Id 
						WHERE T006_Id='{$chaveValor}'");
	$mSql = mysql_fetch_array($sql);

	if($g['C031']['calcularVolumePedido'] == 'S'){
		$sqlT006 = mysql_query("SELECT SUM(T006_Quantidade/D001_Qte_Embalagem) as Volumes FROM T006 
								LEFT JOIN D009 ON D009_Id = T006_D009_Id
								LEFT JOIN D049 ON D049_Id = D009_D049_Id
								LEFT JOIN D001 ON D001_Id = D049_D001_Id 
								WHERE T006_T005_Id = '{$mSql['T006_T005_Id']}'");
		$resT006 = mysql_fetch_array($sqlT006);
		$resT006['Volumes'] = $resT006['Volumes'] - ($mSql['T006_Quantidade']/$mSql['D001_Qte_Embalagem']);
		mysqli_query("UPDATE T005 SET T005_Quantidade_Volumes = '{$resT006['Volumes']}' WHERE T005_Id = '{$mSql['T006_T005_Id']}'");
	}
	
	require_once("bibliotecas/classes/VEN002.php");
    $VEN002 = new VEN002();
	$VEN002->processarNumeroItemPedido($chaveValor, '0');

	$T006_T005_Id_Apos_Excluir = $mSql['T006_T005_Id'];
EOT;

$conf['triggers']['T006']['deleteAfter'] = <<<'EOT'
	$T006=mysqli_query("SELECT * FROM T006 WHERE T006_Id='{$chaveValor}'");
	$mT006 = mysql_fetch_array($T006);

	$upT005 = mysqli_query("UPDATE T005 SET T005_Valor_Total_Comissao=(SELECT SUM(T006_Valor_Comissao) FROM T006 WHERE T006_T005_Id=T005_Id) WHERE T005_Id = '{$mT006['T006_T005_Id']}'");
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T006'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T006_Id, T005_Flag_Status FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE T006_Id = '{$valorPk}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL AND T005_Flag_Status!=14 AND T005_Flag_Status!=15 AND T005_Flag_Status!=9 AND T005_Flag_Status!=26 AND T005_Flag_Status!=22)";
        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0) {
        	$row = mysql_fetch_array($res);
            $retorno = 'Este pedido já foi faturado.';
        }
        if($g['nivelAlcada'] == '4' && $row['T005_Flag_Status'] == '3'){
        	$retorno = true;
        }
    } elseif ($operacao == 'incluir') {
        $sql = "SELECT T005_Id, T005_Flag_Status FROM T005 WHERE T005_Id = '{$valorPkParent}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL AND T005_Flag_Status!=14 AND T005_Flag_Status!=15 AND T005_Flag_Status!=9 AND T005_Flag_Status!=26 AND T005_Flag_Status!=22)";
        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0) {
        	$row = mysql_fetch_array($res);
            $retorno = 'Este pedido já foi faturado.';
        }
        if($g['nivelAlcada'] == '4' && $row['T005_Flag_Status'] == '3'){
        	$retorno = true;
        }
    } else {
        $retorno = true;
    }

    $sql = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id = '{$valorPkParent}' AND T005_T005_Id_Origem > 0");
    if(mysql_num_rows($sql) > 0){
        $retorno = 'Este item está foi gerado a partir de um pedido externo';
    }
    //Travar para os itens que possuem ordem de compra relacionada
    if(($operacao == 'alterar' || $operacao == 'excluir') && $retorno == true) {
        $sql = mysql_query("SELECT T219_Id 
							  FROM T006 
						 LEFT JOIN T004 ON T004_Id = T006_T004_Id 
						 LEFT JOIN T219 ON T219_Id = IF(T006_T219_Id > 0, T006_T219_Id, T004_T219_Id) 
						 LEFT JOIN T225 ON T219_Id = T225_T219_Id
						     WHERE T006_Id = '{$valorPk}' 
							   AND T225_Id > 0");
        $res = mysql_fetch_array($sql);
        if ($res['T219_Id'] > 0) {
            $retorno = 'Este item está vinculado a uma cotação.';
        }
        if($res['T219_Id'] > 0 && $campo == 'T006_Prioridade'){
        	$retorno = true;
        }
    }
    if($campo == 'T006_Quantidade_Separacao' || $campo == 'T006_Quantidade_Entregar_Temp' || $campo == 'T006_D006_Id' || $campo == 'T006_Numero_Pedido_Venda' || $campo == 'T006_Numero_Pedido_Compra' || $campo == 'T006_Item_Pedido_Compra' || $campo == 'T006_Codigo_Substituto' || $campo == 'T006_Valor_Preco_Sem_Desconto_Unitario' || $campo == 'T006_Percentual_Desconto' || $campo == 'T006_Observacao_Compra' || $campo == 'T006_Quantidade_Dividir'){
        $retorno = true;
    }

	//RESTRICAO AO EDITAR OS CAMPOS DE QUANTIDADE/QUANTIDADE CONVERSAO CASO O PROCESSO ESTEJA ATIVO
	if (($campo === 'T006_Quantidade') && ($operacao == 'alterar')) {
		$utilizaQuantidadeConversao = $g['C031']['utilizaQuantidadeConversao'] === 'S';
		if ($utilizaQuantidadeConversao) {
			$sql  = " SELECT D002A_Utiliza_Conversao ";
			$sql .=   " FROM T006 ";
			$sql .=        " LEFT JOIN D009  ON D009_Id = T006_D009_Id ";
			$sql .=        " LEFT JOIN D049  ON D049_Id = D009_D049_Id ";
			$sql .=        " LEFT JOIN D001  ON D001_Id = D049_D001_Id ";
			$sql .=        " LEFT JOIN D002  ON D002_Id = D001_D002_Id ";
			$sql .=        " LEFT JOIN D002A ON D002_Id = D002A_D002_Id ";
			$sql .=  " WHERE T006_Id = '$valorPk' ";
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
