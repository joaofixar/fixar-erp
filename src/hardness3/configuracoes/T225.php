<?php
/**
* Tabela T225
*/
/**
* camposTipo
*/
$conf['camposTipo']['T225_Valor_Preco_Sem_Desconto_Unitario'] = array('numero', 4);

/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['T225_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T225_D005_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T225_Descricao_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['Marca'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T225_Valor_Preco_Sem_Desconto_Unitario'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T225_Quantidade'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['camposTipo']['T225_Quantidade'] = array('numero', 3);
$conf['restricaoIncluirUpdate']['T225_D037_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T225_D006_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T225_Flag_Origem_Produto'] = <<<'EOT'
	$retorno = rNumeroValido($valor, array(0,1,2,3,4,5,6,7,8));
EOT;

/**
* triggers
*/
$conf['triggers']['T225']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T225']['insertAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/EST016.php');
	$EST016 = new EST016();

	mysqli_query("update T225 
	                set T225_Valor_Preco_Sem_Desconto_Unitario=T225_Valor_Preco_Unitario,
	                    T225_Valor_Preco_Original=T225_Valor_Preco_Unitario,
	                    T225_Valor_Desconto_Unitario=((T225_Valor_Preco_Original*(T225_Valor_Preco_Unitario/100))),
	                    T225_Percentual_Desconto=((1-(T225_Valor_Preco_Unitario/T225_Valor_Preco_Original))*100)
	              where T225_Id='$chaveValor'");
	mysqli_query("call T225_Gravar_Totalizacao('$chaveValor')");
	$T225=mysqli_query("select T224_T218_Id, T225_D006_Id, T225_T224_Id, T225_D009_Id from T225 left join T224 on T224_Id=T225_T224_Id where T225_Id='$chaveValor'");
	$mT225=mysql_fetch_array($T225);
	mysqli_query("UPDATE T224 SET T224_D006_Id='{$mT225['T225_D006_Id']}' WHERE T224_Id = '{$mT225['T225_T224_Id']}' AND (T224_D006_Id IS NULL OR T224_D006_Id <= 0)");
	mysqli_query("call T218_Gravar_Totalizacao_3('$mT225[T224_T218_Id]')");

	$EST016->aplicarAlcadaOC($mT225['T225_T224_Id']);

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->reprocessaOCPedidos($mT225['T225_D009_Id']);

	require_once('bibliotecas/classes/ETC017.php');
	$ETC017 = new ETC017();
	$ETC017->inserirRegistroTarefa($mT225['T225_T224_Id'], 'T224');

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT225();
EOT;

$conf['triggers']['T225']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T225']['updateAfter'] = <<<'EOT'
	mysqli_query("update T225 
	                set T225_Valor_Preco_Sem_Desconto_Unitario=T225_Valor_Preco_Unitario,
	                    T225_Valor_Preco_Original=T225_Valor_Preco_Unitario,
	                    T225_Valor_Desconto_Unitario=((T225_Valor_Preco_Original*(T225_Valor_Preco_Unitario/100))),
	                    T225_Percentual_Desconto=((1-(T225_Valor_Preco_Unitario/T225_Valor_Preco_Original))*100)
	              where T225_Id='$chaveValor'");                        
	mysqli_query("call T225_Gravar_Totalizacao('$chaveValor')");
	$T225=mysqli_query("select T224_T218_Id,T225_D009_Id,D049_Id,T218_D024_Id,T225_Codigo_Substituto,D049_D001_Id,T225_T224_Id from T225 left join T224 on T224_Id=T225_T224_Id left join D009 on D009_Id=T225_D009_Id left join D049 on D049_Id=D009_D049_Id left join T218 on T218_Id=T224_T218_Id where T225_Id='$chaveValor'");
	$mT225=mysql_fetch_array($T225);
	mysqli_query("call T218_Gravar_Totalizacao_3('$mT225[T224_T218_Id]')");

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->definirCodigoFornecedor($mT225['T225_D009_Id'], $mT225['T218_D024_Id'], $mT225['T225_Codigo_Substituto'], '', true, false);
	$CAD002->reprocessaOCPedidos($mT225['T225_D009_Id']);

	require_once('bibliotecas/classes/EST016.php');
	$EST016 = new EST016();
	$EST016->aplicarAlcadaOC($mT225['T225_T224_Id']);

	require_once('bibliotecas/classes/NOTIFICACOES.php');
	$NOTIFICACOES = new NOTIFICACOES();
	$NOTIFICACOES->atualizarNotificacoesT225();
EOT;

$conf['triggers']['T225']['deleteBefore'] = <<<'EOT'
	$sql = mysql_query("SELECT T225_T224_Id from T225 WHERE T225_Id='{$chaveValor}'");
	$mSql = mysql_fetch_array($sql);
	mysql_query("call T224_Gravar_Totalizacao_3({$mSql['T225_T224_Id']})");

	require_once('bibliotecas/classes/EST016.php');
	$EST016 = new EST016();
	$EST016->aplicarAlcadaOC($mT225['T225_T224_Id']);
EOT;

$conf['triggers']['T225']['deleteAfter'] = <<<'EOT'
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T225'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T224_Id FROM T225 LEFT JOIN T224 ON T225_T224_Id = T224_Id WHERE T225_Id = '{$valorPk}' AND T224_Flag_Status != '0' AND T224_Flag_Status != '' AND T224_Flag_Status != '2'";
        $res = mysql_query($sql);
        $rows = mysql_num_rows($res);
        if ($rows > 0 and $g['nivelAlcada'] != 4 && (!isset($g['c029Ids'][199]))) {
	 		$retorno = "Ordem de Compra já está aprovada!";
		}

    } else if ($operacao == 'incluir'){
    	$sql = "SELECT T224_Id FROM T224 WHERE T224_Id = '{$valorPkParent}' AND T224_Flag_Status != '0' AND T224_Flag_Status != '' AND T224_Flag_Status != '2'";
        $res = mysql_query($sql);
        $rows = mysql_num_rows($res);
        if ($rows > 0 and $g['nivelAlcada']!=4) {
	 		$retorno = "Ordem de Compra já está aprovada!";
        }
    } else {
        $retorno = true;
    }
    if (!empty($valorPkParent)) {
	    $sql = "SELECT T005_Id FROM T005 WHERE T005_T224_Id = '{$valorPkParent}'";
	    $res = mysql_query($sql);
	    if (mysql_num_rows($res) > 0) {
	    	$retorno = 'Já foi gerado um Pedido para esta Ordem de Transferência.';
	    }
	}

	$T013 = mysql_query("SELECT T013_Id,
									T013_Numero_Nota_Fiscal,
									sum(T014_Quantidade) as T014_Quantidade,
									T225_Quantidade
							   FROM T013 
						  LEFT JOIN T014 ON T014_T013_Id=T013_Id
						  LEFT JOIN T225 ON T225_Id=T014_T225_Id
						  LEFT JOIN T224 ON T224_Id=T225_T224_Id
						  	  WHERE T225_Id='{$valorPk}'
								AND T013_Flag_Travar_Nota='S'
						      LIMIT 1");
								
	$mT013 = mysql_fetch_assoc($T013);
	if ($mT013['T014_Quantidade']>0){
		if ($campo == 'T225_Quantidade') {
			if ($valor > $mT013['T014_Quantidade']){
				$retorno = "Já existe a NF-e {$mT013['T013_Numero_Nota_Fiscal']} vinculada a OC, para editar a <font style='font-weight:bold;'>QTDE</font> é necessário ter a mesma quantidade da nota fiscal.";		
			} else {
				$retorno = true;
			}
		}else{
			$retorno = "Não é possível alterar OC vinculada a NF-e {$mT013['T013_Numero_Nota_Fiscal']} já finalizada.";
		}
	}

	if($campo == 'T225_Observacao_Entrega' || $campo == 'T225_Observacao'){
		$retorno = true;
	}
EOT;
