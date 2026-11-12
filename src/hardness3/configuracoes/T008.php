<?php
$conf['restricaoIncluirUpdate']['T008_Codigo_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T008_Descricao_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T008_Quantidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T008_Valor_Preco_Sem_Desconto_Unitario'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T008_D037_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D037_Id', $valor);
EOT;
/*
$conf['restricaoIncluirUpdate']['T008_D006_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D006_Id', $valor);
EOT;
*/
$conf['restricaoIncluirUpdate']['T008_D005_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T008_Situacao_Tributaria_IPI'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
// $conf['restricaoIncluirUpdate']['Codigo_Origem_Mercadoria'] = <<<'EOT'
	// $retorno = rRegx('naoVazio', $valor);
// EOT;

$conf['restricaoIncluirUpdate']['T008_D059_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D059_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T008_Situacao_Tributaria_PIS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T008_Situacao_Tributaria_COFINS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T008_D006_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;



/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T008']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T008']['insertAfter'] = <<<'EOT'
	global $g;
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();

	/*$T290= mysql_query("SELECT T008_Quantidade, T008_Flag_Entrada_Saida, T008_Data_Emissao, T008_D009_Id
						  FROM T008
				         WHERE T008_Id='{$chaveValor}'");
	$mT290 = mysql_fetch_array($T290);

	$CAD002->populaT290($mT290['T008_D009_Id'], $chaveValor, $mT290['T008_Quantidade'], $mT290['T008_Flag_Entrada_Saida'], $mT290['T008_Data_Emissao']);
	*/

	//mysql_query("CALL T008_Gravar_Totalizacao_4('{$chaveValor}')");

	$T008=mysqli_query("SELECT T007_C007_Id_Vendedor_Interno, T008_Valor_Total_Preco, T008_Percentual_Desconto, D149_Percentual_Trib_Transparencia, T008_T007_Id, T008_D006_Id, T008_Valor_Preco_Sem_Desconto_Unitario, T008_D009_Id, T007_D024_Id
		FROM T008 
		LEFT JOIN D009 ON D009_Id=T008_D009_Id 
		LEFT JOIN D049 ON D049_Id=D009_D049_Id 
		LEFT JOIN D001 ON D001_Id=D049_D001_Id 
		LEFT JOIN D005 ON D005_ID=D001_D005_Id 
		LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id=D009_C004_Id 
		LEFT JOIN T007 ON T007_Id=T008_T007_Id 
		WHERE T008_Id='{$chaveValor}'");
	$mT008 = mysql_fetch_array($T008);

	//Seleciona as observações cfop      
    $sqlT008 = mysql_query("SELECT T008_D006_Id, D006_Observacao FROM T007 LEFT JOIN T008 ON T008_T007_Id = T007_Id LEFT JOIN D006 ON T008_D006_Id = D006_Id WHERE T007_Id = '{$mT008['T008_T007_Id']}' AND D006_Observacao != '' GROUP BY T008_D006_Id");

    $observacao = '';
    
    if (mysql_num_rows($sqlT008) > 0) {
        while ($mT008Observacao = mysql_fetch_array($sqlT008)) {
            $observacao .= $mT008Observacao['D006_Observacao'] . ';';
        }
    }
    //T007_Observacao_Nota_Fiscal_1='{$observacao}'

	$C007=mysql_query("SELECT * FROM C007 LEFT JOIN C019 ON C019_C007_Id=C007_Id LEFT JOIN C021 on C021_Id=C019_C021_Id WHERE C007_Id='{$mT008['T007_C007_Id_Vendedor_Interno']}'");
	$mC007=mysql_fetch_array($C007);
	log("Vendedor: ".$mC007['C007_Flag_Vendedor']);

	$retorno = $VEN001->calcularComissao($chaveValor, 'T008');
	$upT008 = mysqli_query("UPDATE T008 SET T008_Valor_Comissao='{$retorno['valorComissao']}', T008_Percentual_Comissao='{$retorno['percentualComissao']}', T008_Percentual_Trib_Transparencia = '{$mT008['D149_Percentual_Trib_Transparencia']}' WHERE T008_Id='{$chaveValor}'");

	$retorno = $VEN001->calcularComissaoTotal($mT008['T008_T007_Id'], 'T007');
	$upT007 = mysqli_query("UPDATE T007 SET T007_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T007_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T007_Percentual_Comissao_Representante = '{$retorno['percentualComissaoRepresentante']}',
											T007_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T007_Percentual_Comissao_Externo = '{$retorno['percentualComissaoExterno']}',
											T007_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T007_Percentual_Comissao_Usuario = '{$retorno['percentualComissaoUsuario']}',
											T007_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}'
											
							WHERE T007_Id = '{$mT008['T008_T007_Id']}'");

	$upT007 = mysqli_query("UPDATE T007 SET T007_D006_Id='{$mT008['T008_D006_Id']}' WHERE T007_Id = '{$mT008['T008_T007_Id']}' AND (T007_D006_Id IS NULL OR T007_D006_Id <= 0)");

	if($g['C031']['notaAtualizaTabelaPrecoCliente'] == 'S'){
		$CAD002->atualizaTabelaPrecoCliente($mT008['T007_D024_Id'], $mT008['T008_D009_Id'], $mT008['T008_Valor_Preco_Sem_Desconto_Unitario']);
	}
EOT;

$conf['triggers']['T008']['updateBefore'] = <<<'EOT'
	$T008=mysqli_query("SELECT T008_Valor_Preco_Sem_Desconto_Unitario FROM T008 WHERE T008_Id='{$chaveValor}'");
	$mT008 = mysql_fetch_array($T008);
	$g['updateBefore-T008_Valor_Preco_Sem_Desconto_Unitario'] = $mT004['T008_Valor_Preco_Sem_Desconto_Unitario'];
EOT;

$conf['triggers']['T008']['updateAfter'] = <<<'EOT'
	global $g;
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();

	$T290= mysql_query("SELECT T008_Quantidade, T008_Flag_Entrada_Saida, T008_Data_Emissao, T008_D009_Id
						  FROM T008
				         WHERE T008_Id='{$chaveValor}'");
	$mT290 = mysql_fetch_array($T290);

	$CAD002->populaT290($mT290['T008_D009_Id'], $chaveValor, $mT290['T008_Quantidade'], $mT290['T008_Flag_Entrada_Saida'], $mT290['T008_Data_Emissao']);

	mysql_query("CALL T008_Gravar_Totalizacao_4('{$chaveValor}')");	
	
	$T008=mysqli_query("SELECT T007_C007_Id_Vendedor_Interno, 
							   T008_Valor_Total_Preco, 
							   T008_Percentual_Desconto, 
							   D149_Percentual_Trib_Transparencia, 
							   T008_T007_Id, 
							   T008_D006_Id, 
							   T008_Flag_Calculo_Manual, 
							   T008_Valor_Preco_Sem_Desconto_Unitario, 
							   T008_D009_Id, 
							   T007_D024_Id,
							   D006_Flag_Destaca_Desconto_NF,
							   D006_Percentual_Desconto_CFOP,
							   T008A_Percentual_Desconto_CFOP 
						  FROM T008 
					 LEFT JOIN D009 ON D009_Id=T008_D009_Id 
					 LEFT JOIN D049 ON D049_Id=D009_D049_Id 
					 LEFT JOIN D001 ON D001_Id=D049_D001_Id 
					 LEFT JOIN D005 ON D005_ID=D001_D005_Id 
					 LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id=D009_C004_Id
					 LEFT JOIN T007 ON T007_Id=T008_T007_Id
					 LEFT JOIN D006 ON D006_Id=T008_D006_Id
					 LEFT JOIN T008A ON T008_Id=T008A_T008_Id 
						 WHERE T008_Id='{$chaveValor}'");
	$mT008 = mysql_fetch_array($T008);

	if($mT008['T008A_Percentual_Desconto_CFOP'] > 0 && $mT008['D006_Percentual_Desconto_CFOP'] <= 0){
		// Foi alterada a CFOP, agora nao aplica mais desconto, precisa retirar o desconto do total
		$VEN001->aplicaDescontoCFOP('T008', 'T007', $chaveValor, $mT008[T008_T007_Id], 'S');	
	} elseif($mT008['T008A_Percentual_Desconto_CFOP'] > 0 || $mT008['D006_Percentual_Desconto_CFOP'] > 0){
		$VEN001->aplicaDescontoCFOP('T008', 'T007', $chaveValor, $mT008[T008_T007_Id], $mT008['D006_Flag_Destaca_Desconto_NF']);
	} 

	//Seleciona as observações cfop      
    $sqlT008 = mysql_query("SELECT T008_D006_Id, D006_Observacao FROM T007 LEFT JOIN T008 ON T008_T007_Id = T007_Id LEFT JOIN D006 ON T008_D006_Id = D006_Id WHERE T007_Id = '{$mT008['T008_T007_Id']}' AND D006_Observacao != '' GROUP BY T008_D006_Id");

    $observacao = '';
    
    if (mysql_num_rows($sqlT008) > 0) {
        while ($mT008Observacao = mysql_fetch_array($sqlT008)) {
            $observacao .= $mT008Observacao['D006_Observacao'] . ';';
        }
    }

	$upT007 = mysqli_query("UPDATE T007 SET T007_D006_Id='{$mT008['T008_D006_Id']}' WHERE T007_Id = '{$mT008['T008_T007_Id']}' AND (T007_D006_Id != '{$mT008['T008_D006_Id']}' OR (T007_D006_Id IS NULL OR T007_D006_Id <= 0))");
	mysql_query("CALL T007_Gravar_Totalizacao_4('{$mT008['T008_T007_Id']}')");	

	$C007=mysql_query("SELECT * FROM C007 LEFT JOIN C019 ON C019_C007_Id=C007_Id LEFT JOIN C021 on C021_Id=C019_C021_Id WHERE C007_Id='{$mT008['T007_C007_Id_Vendedor_Interno']}'");
	$mC007=mysql_fetch_array($C007);
	log("Vendedor: ".$mC007['C007_Flag_Vendedor']);

	//calculo Frete
	$dadosTabela = array('tabela' => 'T007', 
						 'tabelaId' => $mT008['T008_T007_Id'],
						 'campos' => 'T007_Valor_Total as valorTotal,
                                     T007_Total_Peso as pesoTotal,
                                     T007_Peso_Bruto as valorBrutoTotal,
                                     T007_Total_Cubagem as totalCubagem');
	$valor_frete_calculado = $VEN001->calcularFrete($dadosTabela);

	$retorno = $VEN001->calcularComissao($chaveValor, 'T008');
	$upT008 = mysqli_query("UPDATE T008 SET T008_Valor_Comissao='{$retorno['valorComissao']}', T008_Percentual_Comissao='{$retorno['percentualComissao']}', T008_Percentual_Trib_Transparencia = '{$mT008['D149_Percentual_Trib_Transparencia']}' WHERE T008_Id='{$chaveValor}'");
	//T007_Observacao_Nota_Fiscal_1='{$observacao}'
	$retorno = $VEN001->calcularComissaoTotal($mT008['T008_T007_Id'], 'T007');
	$upT007 = mysqli_query("UPDATE T007 SET T007_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T007_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T007_Percentual_Comissao_Representante = '{$retorno['percentualComissaoRepresentante']}',
											T007_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T007_Percentual_Comissao_Externo = '{$retorno['percentualComissaoExterno']}',
											T007_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T007_Percentual_Comissao_Usuario = '{$retorno['percentualComissaoUsuario']}',
											T007_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}',
											T007_Valor_Frete_Calculado = '{$valor_frete_calculado}'
							WHERE T007_Id = '{$mT008['T008_T007_Id']}'");

	if($g['updateBefore-T008_Valor_Preco_Sem_Desconto_Unitario'] != $mT008['T008_Valor_Preco_Sem_Desconto_Unitario'] && $g['C031']['notaAtualizaTabelaPrecoCliente'] == 'S'){
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		$CAD002->atualizaTabelaPrecoCliente($mT008['T007_D024_Id'], $mT008['T008_D009_Id'], $mT008['T008_Valor_Preco_Sem_Desconto_Unitario']);
	}
EOT;

$conf['triggers']['T008']['deleteBefore'] = <<<'EOT'

	$T008=mysqli_query("SELECT * FROM T008 LEFT JOIN T007 ON T007_Id=T008_T007_Id WHERE T008_Id='{$chaveValor}'");
	$mT008 = mysql_fetch_array($T008);

/*
	//Seleciona as observações cfop      
    $sqlT008 = mysqli_query("SELECT T008_D006_Id, D006_Observacao FROM T007 LEFT JOIN T008 ON T008_T007_Id = T007_Id LEFT JOIN D006 ON T008_D006_Id = D006_Id WHERE T007_Id = '{$mT008['T008_T007_Id']}' AND T008_D006_Id != T007_D006_Id AND D006_Observacao != '' GROUP BY T008_D006_Id");

    $observacao = '';

    if (mysql_num_rows($sqlT008) > 0) {
        while ($nT008 = mysql_fetch_array($sqlT008)) {
            $observacao .= $nT008['D006_Observacao'] . ';';
        }
    }

	$upT007 = mysqli_query("UPDATE T007 SET T007_Observacao_Nota_Fiscal_1='{$observacao}', 
						   T007_Flag_Travar_Nota = 'N'
						   WHERE T007_Id = '{$mT008['T008_T007_Id']}'");
						   */
EOT;

$conf['triggers']['T008']['deleteAfter'] = <<<'EOT'

	$T008=mysqli_query("SELECT * FROM T008 WHERE T008_Id='{$chaveValor}'");
	$mT008 = mysql_fetch_array($T008);

	$upT007 = mysqli_query("UPDATE T007 SET T007_Valor_Total_Comissao=(SELECT SUM(T008_Valor_Comissao)
									   FROM T008 
									  WHERE T007_Id = '{$mT008['T008_T007_Id']}'");
EOT;

$conf['restricaoTravar']['T008'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar') {
		$sqlT007 = "SELECT T007_Numero_Protocolo_Nfe,
						   T007_Flag_Travar_Nota,
						   T007_Numero_Nota_Fiscal, 
						   T007_Flag_ACP
					  FROM T008 
				 LEFT JOIN T007 on T007_Id=T008_T007_Id 
				     WHERE T008_Id='{$valorPk}'";

		$resultT007 = mysql_query($sqlT007);
		$linhaT007 = mysql_fetch_array($resultT007);

		if (!empty($linhaT007['T007_Numero_Protocolo_Nfe'])) {
			$retorno = 'Nota já transmitida. Não é possível alterar.';

		} else if ($linhaT007['T007_Flag_Travar_Nota'] == 'S') {
			$retorno = 'Nota já finalizada, para altera-lá utilize o botão "Habilitar edição"';

		} else if($linhaT007['T007_Flag_ACP'] == 6 || $linhaT007['T007_Flag_ACP'] == 8){
			$T127 = mysql_query("SELECT T127_Retorno_Sefaz 
							       FROM T127 
								  WHERE T127_Numero_Nota_Fiscal='{$linhaT007['T007_Numero_Nota_Fiscal']}' 
								    AND T127_C004_Id='{$g['empresaAtual']}' 
									AND T127_Modelo=IF('{$linhaT007['T007_Flag_ACP']}' = '6', '55', '65') 
									AND (T127_Codigo_Retorno = '539' OR T127_Codigo_Retorno = '204' OR T127_Codigo_Retorno = '635')
								  LIMIT 1");
			if(mysql_num_rows($T127) > 0){
				$mT127 = mysql_fetch_array($T127);
				$retorno = "Nota fiscal já está autorizada no portal, não pode mais ser alterada, tente transmitir novamente.<br>Retorno Sefaz: {$mT127['T127_Retorno_Sefaz']}";
			}
		}
	} else {
		$sqlT007 = "SELECT T007_Numero_Protocolo_Nfe, 
						   T007_Flag_Travar_Nota, 
						   T007_Numero_Nota_Fiscal, 
						   T007_Flag_ACP
					  FROM T007 
					 WHERE T007_Id='{$valorPkParent}'";
					 
		$resultT007 = mysql_query($sqlT007);
		$linhaT007 = mysql_fetch_array($resultT007);

		if($linhaT007['T007_Flag_Travar_Nota'] == 'S'){
			$retorno = 'Nota já finalizada, para altera-lá utilize o botão "Habilitar edição"';
		}else{
			if (!empty($linhaT007['T007_Numero_Protocolo_Nfe'])) {
				$retorno = 'Nota já transmitida. Não é possível alterar.';
			} else {
				$T127 = mysql_query("SELECT T127_Retorno_Sefaz 
									   FROM T127 
									  WHERE T127_Numero_Nota_Fiscal='{$linhaT007['T007_Numero_Nota_Fiscal']}' 
										AND T127_C004_Id='{$g['empresaAtual']}' 
										AND T127_Modelo=IF('{$linhaT007['T007_Flag_ACP']}' = '6', '55', '65') 
										AND (T127_Codigo_Retorno = '539' OR T127_Codigo_Retorno = '204' OR T127_Codigo_Retorno = '635')
									  LIMIT 1");
				if(mysql_num_rows($T127) > 0){
					$mT127 = mysql_fetch_array($T127);
					$retorno = "Nota fiscal já está autorizada no portal, não pode mais ser alterada, tente transmitir novamente.<br>Retorno Sefaz: {$mT127['T127_Retorno_Sefaz']}";
				}
			}
		}	
	}

	if ($campo == 'T008_Quantidade_Devolucao' or $campo == 'T008_Percentual_Comissao') {
		$retorno = true;	
	}
EOT;
