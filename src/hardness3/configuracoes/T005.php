<?php
/*
* camposLabelForm
*/
$conf['camposLabelForm']['T005_Observacao_1'] = 'Almoxarifado';

// camposNaoUsar
if($g['C031']['usarDescontoProdutos'] == 'N'){
	$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
		'T005_Valor_Desconto',
		'T005_D116_Id'
	));
}

// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T005_Data_Saida'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T005_Observacao_Fatura_Emergencia'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T005_Quantidade_Volumes'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T005_D022_Id'] = <<<'EOT'
	if($valores['T005_Flag_Frete'] != 9) {
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['T005_Data_Emissao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T005_D027_Id']  = <<<'EOT'
	$retorno = rVerificaFK('D027_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T005_Flag_ACP'] = <<<'EOT'
    $retorno = rRegx('naoSelecionado', $valor);
EOT;

//print_r($g['C031']);
$conf['restricaoIncluirUpdate']['T005_Data_Entrega'] = <<<'EOT'
	if($g['C031']['obrigatorioDataEntregaPedido'] == 'S'){
		$retorno = rRegx('naoVazio', $valor);
	}
	else{
		$retorno = true;
	}
EOT;

$conf['restricaoIncluirUpdate']['T005_Flag_Destino_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T005_D013_Id'] = <<<'EOT'
    $retorno = rRegx('naoSelecionado', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T005_Flag_Expedicao'] = <<<'EOT'
	if($g['C031']['modeloExpedicao'] == '2'){
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;

// triggers
$conf['triggers']['T005']['insertBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T005']['insertAfter'] = <<<'EOT'
	mysqli_query("call T005_Gravar_Totalizacao_4('$chaveValor')");

	$T005 = mysqli_query("SELECT T005_C004_Id, D021_Percentual_Comissao,T005_T003_Id FROM T005 LEFT JOIN D021 ON D021_Id=T005_D021_Id WHERE T005_Id='$chaveValor'");
	$mT005 = mysql_fetch_array($T005);

	$T250 = mysqli_query("SELECT T250_Id FROM T250 WHERE T250_T003_Id='{$mT005['T005_T003_Id']}'");

	if(mysql_num_rows($T250) > 0){
		mysqli_query("INSERT INTO T251 (T251_T005_Id, T251_Descricao, T251_Aliquota, T251_Aliquota_Editavel, T251_Aliquota_Original, T251_Tipo, T251_Grupo, T251_Nivel_Acesso, T251_Flag_Campo_Calculado, T251_Flag_Altera_Preco) SELECT '{$chaveValor}', T250_Descricao, T250_Aliquota, T250_Aliquota, T250_Aliquota_Original, T250_Tipo, T250_Grupo, T250_Nivel_Acesso, T250_Flag_Campo_Calculado, T250_Flag_Altera_Preco FROM T250 WHERE T250_T003_Id = '{$mT005['T005_T003_Id']}'");
	} else {
		mysqli_query("INSERT INTO T251 (T251_T005_Id, T251_Descricao, T251_Aliquota, T251_Aliquota_Editavel, T251_Aliquota_Original, T251_Tipo, T251_Grupo, T251_Nivel_Acesso, T251_Flag_Campo_Calculado, T251_Flag_Altera_Preco) SELECT '{$chaveValor}', T249_Descricao, T249_Aliquota, T249_Aliquota, T249_Aliquota, T249_Tipo, T249_Grupo, T249_Nivel_Acesso, T249_Flag_Campo_Calculado, T249_Flag_Altera_Preco FROM T249 WHERE T249_C004_Id = '{$mT005['T005_C004_Id']}'");
	}

	mysqli_query("UPDATE T005 SET T005_Percentual_Comissao_Representante = '{$mT005['D021_Percentual_Comissao']}' WHERE T005_Id = '{$chaveValor}'");
	mysqli_query("UPDATE T003 SET T003_Flag_Notificacao_Liberacao = '' WHERE T003_Id = '{$mT005['T005_T003_Id']}'");

	// Ao incluir um pedido insere um registro na tabela estendida T005A (valor default do campo é 1)
	//mysqli_query("INSERT INTO T005A (T005A_T005_Id) VALUES ('{$chaveValor}')");
	

	require_once('bibliotecas/classes/ETC017.php');
	$ETC017 = new ETC017();
	$ETC017->inserirRegistroTarefa($chaveValor, 'T005');

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT005();
EOT;

$conf['triggers']['T005']['updateBefore'] = <<<'EOT'
	$sql = mysql_query("SELECT T005_D021_Id,T005_D024_Id, T005_Flag_Status FROM T005 WHERE T005_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);
	$g['updateBefore-T005_D021_Id'] = $row['T005_D021_Id'];
	$g['updateBefore-T005_D024_Id'] = $row['T005_D024_Id'];
	$g['updateBefore-T005_Flag_Status'] = $row['T005_Flag_Status'];
EOT;

$conf['triggers']['T005']['updateAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();
	
	$sql = mysql_query("SELECT T005_D021_Id, T005_D024_Id, T005_Flag_Status FROM T005 WHERE T005_Id = '{$chaveValor}'");
	$row = mysql_fetch_array($sql);

	if($g['updateBefore-T005_D021_Id'] != $row['T005_D021_Id']){
		$sqlD021 = mysql_query("SELECT D021_Percentual_Comissao FROM D021 WHERE D021_Id = '{$row['T005_D021_Id']}'");
		$rowD021 = mysql_fetch_array($sqlD021);
		mysqli_query("UPDATE T005 SET T005_Percentual_Comissao_Representante = '{$rowD021['D021_Percentual_Comissao']}' WHERE T005_Id = '{$chaveValor}'");
	}

	//calculo Frete
	$dadosTabela = array('tabela' => 'T005', 
						 'tabelaId' => 	$chaveValor,
						 'campos' => 'T005_Valor_Total as valorTotal,
                                     T005_Peso_Total as pesoTotal,
                                     T005_Peso_Bruto as valorBrutoTotal,
                                     T005_Total_Cubagem as totalCubagem');
	$valor_frete_calculado = $VEN001->calcularFrete($dadosTabela);

	$retorno = $VEN001->calcularComissaoTotal($chaveValor, 'T005');
	$upT005 = mysqli_query("UPDATE T005 SET T005_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T005_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T005_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T005_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T005_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}',
											T005_Valor_Frete_Calculado = '{$valor_frete_calculado}'
							WHERE T005_Id = '{$chaveValor}'");

	$T006=mysql_query("select T006_Id from T006 where T006_T005_Id='$chaveValor'");
	while ($mT006=mysql_fetch_array($T006)){
	    if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
			mysql_query("CALL T006_Gravar_Totalizacao_4('$mT006[T006_Id]')");
			mysqli_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario = T006_Valor_Preco_Bruto+ifnull(T006_Valor_Frete_Unitario,0)+ifnull((T006_Valor_Total_Agregados/T006_Quantidade),0), 
										  T006_Valor_Desconto_Unitario=((T006_Valor_Preco_Original*(T006_Valor_Preco_Sem_Desconto_Unitario/100))),
		                                  T006_Percentual_Desconto=((1-(T006_Valor_Preco_Sem_Desconto_Unitario/T006_Valor_Preco_Original))*100)
	                                WHERE T006_Id = '$mT006[T006_Id]'");
			mysql_query("CALL T006_Gravar_Totalizacao_4('$mT006[T006_Id]')");
			mysqli_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario = round(T006_Valor_Preco_Sem_Desconto_Unitario/((T006_Valor_Preco_Sem_Desconto_Unitario+(T006_Valor_IPI/T006_Quantidade)+(T006_Valor_ICMS_Substituicao_Retencao/T006_Quantidade))/T006_Valor_Preco_Sem_Desconto_Unitario),4),
										  T006_Valor_Desconto_Unitario=((T006_Valor_Preco_Original*(T006_Valor_Preco_Sem_Desconto_Unitario/100))),
		                                  T006_Percentual_Desconto=((1-(T006_Valor_Preco_Sem_Desconto_Unitario/T006_Valor_Preco_Original))*100)
			                        WHERE T006_Id = '$mT006[T006_Id]'");
			mysql_query("CALL T006_Gravar_Totalizacao_4('$mT006[T006_Id]')");
		} else {
			mysql_query("CALL T006_Gravar_Totalizacao_4('$mT006[T006_Id]')");
            mysqli_query("UPDATE T006 SET T006_Valor_Preco_Bruto='0.00',T006_Valor_Preco_Original_Bruto='0.00',T006_Valor_Desconto_Unitario_Bruto='0.00',T006_Percentual_Desconto_Bruto='0.00' WHERE T006_Id={$mT006['T006_Id']}");
		}
	}
	mysql_query("call T005_Gravar_Totalizacao_4('$chaveValor')");

	if($g['updateBefore-T005_D024_Id'] != $row['T005_D024_Id']){
		// Se alterar o cliente, ajusta os registros da tarefa
		
		$sqlD085 = mysql_query("SELECT D085_Id,
									   D085_T259_Id,
									   D085_D024_Id 
								  FROM D085
								 WHERE D085_T005_Id = '{$chaveValor}' ");

		while($rowD085 = mysql_fetch_array($sqlD085)){

			if($rowD085['D085_D024_Id'] != $row['T005_D024_Id']){
				mysqli_query("UPDATE D085 SET D085_D024_Id = '{$row['T005_D024_Id']}'
										WHERE D085_Id = '{$rowD085['D085_Id']}'");
				

				mysqli_query("UPDATE T259 SET T259_D024_Id = '{$row['T005_D024_Id']}'
										WHERE T259_Id = '{$rowD085['D085_T259_Id']}'");
			}
		}
	}

	require_once('bibliotecas/classes/NOTIFICACOES.php');
	$NOTIFICACOES = new NOTIFICACOES();
	$NOTIFICACOES->atualizarNotificacoesT005();

	if($g['updateBefore-T005_Flag_Status'] != $row['T005_Flag_Status'] && $row['T005_Flag_Status'] == $g['C031']['statusInicialPedido']) {
		require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
		$WMS = new WMS();

		if($WMS->isEnabled()) {
			$sqlT005A = mysql_query("SELECT T005A_Data_Envio_WMS FROM T005A WHERE T005A_T005_Id = '{$chaveValor}'");
			$rowT005A = mysql_fetch_array($sqlT005A);
			
			if(empty($rowT005A['T005A_Data_Envio_WMS']) || $rowT005A['T005A_Data_Envio_WMS'] == '0000-00-00 00:00:00') {
				$WMS->integrarPedido('INSERT', $chaveValor);
				mysqli_query("UPDATE T005A SET T005A_Data_Envio_WMS = NOW() WHERE T005A_T005_Id = '{$chaveValor}'");
			}
		}
	}
EOT;
$conf['triggers']['T005']['deleteBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T005']['deleteAfter'] = <<<'EOT'
EOT;

// camposValorPadrao
$conf['camposValorPadrao']['T005_Quantidade_Volumes'] = ''; 
$conf['camposValorPadrao']['T005_D036_Id'] = 3;
$conf['camposValorPadrao']['T005_Flag_Frete'] = 1;

// restricaoTravar
$conf['restricaoTravar']['T005'] = <<<'EOT'
	$retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T005_Id, T005_Flag_Status FROM T005 WHERE T005_Id = '{$valorPk}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL AND T005_Flag_Status!=14 AND T005_Flag_Status!=15 AND T005_Flag_Status!=9 AND T005_Flag_Status!=6 AND T005_Flag_Status!=22 AND ifnull(T005_Numero_NF_Devolucao,'')='')";
        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0){
        	$row = mysql_fetch_assoc($res);
            $retorno = 'Este pedido já foi faturado ou está cancelado.';
        }
        if($row['T005_Flag_Status'] == '3'){
        	$retorno = true;
        }
		if ($row['T005_Flag_Status'] == '2' && $campo == 'T005_D022_Id') {
			$retorno = true;
		}
    } else {
        $retorno = true;
    }
    if(($campo == 'T005_C007_Id_Vendedor_Interno' && $g['nivelAlcada'] == '4')|| $campo == 'T005_Estoque_BOX' || $campo == 'T005_Percentual_Comissao_Representante' || $campo == 'T005_Observacao_3' || $campo == 'T005_Percentual_Comissao_Manual' || $campo == 'T005_D021_Id' || $campo == 'T005_Numero_NF_Devolucao' || $campo == 'T005_T275_Id' || $campo == 'T005_Observacao_1'){
        $retorno = true;
    }
EOT;


/**
* trocarEmpresa
*/
$conf['trocarEmpresa']['exibir'] = <<<'EOT'
	// Variáveis disponíveis: $r_tabela, $r_acaoId
	// Retornar 'true' irá ativar o ícone para trocar a empresa

	//Verifica se existem itens do pedido vinculados a uma cotação
	$sql = mysql_query("SELECT T006_Id FROM T006 LEFT JOIN T219 ON T219_T006_Id=T006_Id WHERE T006_T005_Id='{$r_acaoId}' AND IFNULL(T219_Id,0) > 0");
	if(mysql_num_rows($sql) > 0){
		return false;
	}

	$sql = "SELECT T005_Flag_Status FROM T005 WHERE T005_Id = '{$r_acaoId}'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if ($row['T005_Flag_Status'] == 0) {
		return true;
	}
EOT;
$conf['trocarEmpresa']['tabelaFilho'] = 'T006';
$conf['trocarEmpresa']['executarNoFinal'] = <<<'EOT'
	// Variáveis disponíveis: $C004_Id_Novo, $Tabela, $Id
EOT;
