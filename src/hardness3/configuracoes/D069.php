<?php
/*
* camposNaoUsar
*/
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D069_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D069_D005_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D069_Descricao_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['Marca'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D069_Valor_Preco_Sem_Desconto_Unitario'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['D069_Quantidade'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['D069_D037_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D069_D006_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D069_Codigo_Barras'] = <<<'EOT'
	$retorno = rDuplicidade($campo, $valor, $acaoId, 'Código de Barras', true, 'D069_D070_Id', $valores['D069_D070_Id']);
EOT;

/**
* camposTipo
*/
$conf['camposTipo']['D069_Quantidade'] = array('numero', 3);

/**
* ignorarRelacionamentoExcluir
*/
$conf['ignorarRelacionamentoExcluir'] = array_merge($conf['ignorarRelacionamentoExcluir'], 
	array('D009_D069_Id')
);


/**
* triggers
*/
$conf['triggers']['D069']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['D069']['insertAfter'] = <<<'EOT'
/*
	mysqli_query("call D069_Gravar_Totalizacao('$chaveValor')");
	mysqli_query("UPDATE D069 SET D069_Data_Atualizacao_Custo = CURDATE() WHERE D069_Id = '{$chaveValor}'");
	*/
EOT;

$conf['triggers']['D069']['updateBefore'] = <<<'EOT'
/*
	$sql = "SELECT D069_Valor_Custo_Unitario, D069_Data_Atualizacao_Custo FROM D069 WHERE D069_Id = '{$chaveValor}'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$g['updateBefore-D069_Valor_Custo_Unitario'] = $row['D069_Valor_Custo_Unitario'];
	$g['updateBefore-D069_Data_Atualizacao_Custo'] = $row['D069_Data_Atualizacao_Custo'];
	*/
EOT;

$conf['triggers']['D069']['updateAfter'] = <<<'EOT'
	//mysqli_query("call D069_Gravar_Totalizacao('$chaveValor')");
	$D069=mysqli_query("select D069_Id,
							   D069_D009_Id,
							   D049_Id,
							   D070_D024_Id,
							   D069_Codigo_Substituto,
							   D049_D001_Id,
							   D069_Flag_Tabela_Principal,
							   D069_D070_Id,
							   D009_C004_Id,
							   D009_Id,
							   D069_Data_Atualizacao_Custo,
							   D009_D069_Id,
							   D069_Valor_Custo_Unitario 
						  from D069 
					 left join D009 on D009_Id=D069_D009_Id 
					 left join D049 on D049_Id=D009_D049_Id 
					 left join D070 on D070_Id=D069_D070_Id 
					     where D069_Id='$chaveValor'");

	$mD069=mysql_fetch_array($D069);
    // Deixa apenas uma tabela marcada para este D009
    if ($mD069['D069_Flag_Tabela_Principal']=='S' and $mD069['D009_Id']>0){
        mysqli_query("UPDATE D069 SET D069_Flag_Tabela_Principal='' WHERE D069_D009_Id='{$mD069['D009_Id']}' and D069_Id!='{$mD069['D069_Id']}'");
    }

	/*
	é a mesma query utilizada em cima
	$D069Principal=mysqli_query("select D069_Id, 
										D069_Flag_Tabela_Principal, 
										D069_D070_Id, 
										D009_Id, 
										D069_Data_Atualizacao_Custo,
										D069_Valor_Custo_Unitario
								   from D069 
							  left join D009 on D009_Id=D069_D009_Id 
							  left join D049 on D049_Id=D009_D049_Id 
							  left join D070 on D070_Id=D069_D070_Id 
							      where D069_Id='$chaveValor'");

	$mD069Principal=mysql_fetch_array($D069Principal);
	*/

    // Atualiza D009 conforme tabela principal

	if(($mD069['D009_Id']<=0) && ($mD069['D009_Id']<=0)){
		$D009=mysqli_query("select D009_Id from D009 where D009_D069_Id='$chaveValor'");
		$mD009=mysql_fetch_array($D009);

	    mysqli_query("UPDATE D009 SET D009_D069_Id='', D009_D070_Id='', D009_Quantidade_Estoque_Tabela=D009_Quantidade_Estoque_Tabela(D009_Id) WHERE D009_Id='{$mD009['D009_Id']}' and D009_D069_Id='{$mD069['D069_Id']}'");
	}else{
		if ($mD069['D069_Flag_Tabela_Principal']=='S' and $mD069['D009_Id']>0){
	        mysqli_query("UPDATE D009 SET D009_D069_Id='{$mD069['D069_Id']}', 
			                              D009_D070_Id='{$mD069['D069_D070_Id']}', 
										  D009_Data_Atualizacao_Custo_Fornecedor = '{$mD069['D069_Data_Atualizacao_Custo']}', 
										  D009_Quantidade_Estoque_Tabela=D009_Quantidade_Estoque_Tabela(D009_Id)
								    WHERE D009_Id='{$mD069['D009_Id']}'");

			require_once("bibliotecas/classes/CAD002.php");
			$CAD002 = new CAD002();								
			$CAD002->atualizarCustoProduto($mD069['D009_Id']);

	    } else {
		    mysqli_query("UPDATE D009 SET D009_D069_Id='', D009_D070_Id='', D009_Quantidade_Estoque_Tabela=D009_Quantidade_Estoque_Tabela(D009_Id) WHERE D009_Id='{$mD069['D009_Id']}' and D009_D069_Id='{$mD069['D069_Id']}'");
	    }
	}


/*
	require_once("bibliotecas/classes/CAD002.php");
    $CAD002 = new CAD002();
    $CAD002->definirCodigoFornecedor($mD069['D069_D009_Id'], $mD069['D070_D024_Id'], $mD069['D069_Codigo_Substituto'], '', false, true);

    // Verifica se o campo D069_Valor_Custo_Unitario foi alterado
	$sql = "SELECT D069_Valor_Custo_Unitario, D069_D009_Id, D069_Data_Atualizacao_Custo, D069_C007_Id_Atualizacao_Custo FROM D069 WHERE D069_Id = '{$chaveValor}'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if ($g['updateBefore-D069_Valor_Custo_Unitario'] != $row['D069_Valor_Custo_Unitario']) {
		$T162_Extra = "Trigger PHP - Tabela D069 (1)";
		$camposAlterados = array();
		$camposAlterados['D069_Data_Atualizacao_Custo'] = array($row['D069_Data_Atualizacao_Custo'], date('Y-m-d'));
		$camposAlterados['D069_C007_Id_Atualizacao_Custo'] = array($row['D069_C007_Id_Atualizacao_Custo'], $g['usuarioAtual']);
		$camposAlterados = mysql_real_escape_string(serialize($camposAlterados));

		mysqli_query("UPDATE D069 SET D069_Data_Atualizacao_Custo = CURDATE(), D069_C007_Id_Atualizacao_Custo = '{$g['usuarioAtual']}' WHERE D069_Id = '{$chaveValor}'");
		mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', 'U', '{$T162_Extra}', '" . date('Y-m-d H:i:s') . "', 'D069', '{$chaveValor}', COMPRESS('{$camposAlterados}'), '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);
	}
	mysqli_query("UPDATE D069 SET D069_Data_Hora_Ultima_Alteracao = CURRENT_TIMESTAMP(), D069_C007_Id_Ultima_Alteracao = '{$g['usuarioAtual']}' WHERE D069_Id = '{$chaveValor}'");

	$sqlD009 = mysql_query("SELECT D009_Data_Atualizacao_Custo_Fornecedor, D009_D069_Id FROM D009 WHERE D009_Id = '{$row['D069_D009_Id']}'");
	$rowD009 = mysql_fetch_array($sqlD009);
	if($mD069['D009_D069_Id'] != $chaveValor && $mD069['D009_Id']>0){
		$T162_Extra = "Trigger PHP - Tabela D069 (2)";
		$camposAlterados = array();
		$camposAlterados['D009_Data_Atualizacao_Custo_Fornecedor'] = array($rowD009['D009_Data_Atualizacao_Custo_Fornecedor'], date('Y-m-d'));
		$camposAlterados = mysql_real_escape_string(serialize($camposAlterados));

		mysqli_query("UPDATE D009 SET D009_Data_Atualizacao_Custo_Fornecedor = CURDATE() WHERE D009_Id = '{$row['D069_D009_Id']}' AND D009_D069_Id = '{$chaveValor}'");
		mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', 'U', '{$T162_Extra}', '" . date('Y-m-d H:i:s') . "', 'D009', '{$row['D069_D009_Id']}', COMPRESS('{$camposAlterados}'), '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);
	}
	$CAD002->atualizarCustoProduto($row['D069_D009_Id']);

	*/
EOT;

$conf['triggers']['D069']['deleteBefore'] = <<<'EOT'
	$sql = mysql_query("SELECT D069_D070_Id from D069 WHERE D069_Id='{$chaveValor}'");
	$mSql = mysql_fetch_array($sql);
EOT;
$conf['triggers']['D069']['deleteAfter'] = <<<'EOT'
EOT;

/**
* restricaoTravar
*/
/*$conf['restricaoTravar']['D069'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = "SELECT D069_Id FROM D069 WHERE D069_D069_Id = '{$valorPk}'";
		$res = mysql_query($sql);
		$rows = mysql_numrows($res);
		if ($rows > 0) {
			$retorno = 'Este item já gerou pedido.';
		}
	} else if ($operacao == 'incluir') {
		$sql = "SELECT D070_Id FROM D070 WHERE D070_T218_Id = '{$valorPkParent}'";
		$res = mysql_query($sql);
		$rows = mysql_numrows($res);
		if ($rows > 0) {
			$retorno = 'Este orçamento já gerou pedido.';
		}
	}
EOT;
*/
/**
* restricaoTravar
*/
$conf['restricaoTravar']['D069'] = <<<'EOT'
	$retorno = true;
EOT;
