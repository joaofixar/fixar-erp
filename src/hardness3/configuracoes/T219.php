<?php
/**
* Tabela T219
*/

$conf['camposTipo']['T219_Valor_Preco_Unitario'] = array('numero', 4);

/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['T219_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T219_D005_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T219_Descricao_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['Marca'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T219_Valor_Preco_Sem_Desconto_Unitario'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T219_Quantidade'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['camposTipo']['T219_Quantidade'] = array('numero', 3);
$conf['restricaoIncluirUpdate']['T219_D037_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T219_D006_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T219_Flag_Origem_Produto'] = <<<'EOT'
	$retorno = rNumeroValido($valor, array(0,1,2,3,4,5,6,7));
EOT;

/**
 * triggers
 */
$conf['triggers']['T219']['insertBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T219']['insertAfter'] = <<<'EOT'
	mysqli_query("update T219 
	                set T219_Valor_Preco_Sem_Desconto_Unitario=T219_Valor_Preco_Unitario,
	                    T219_Valor_Preco_Original=T219_Valor_Preco_Unitario,
	                    T219_Valor_Desconto_Unitario=((T219_Valor_Preco_Original*(T219_Valor_Preco_Unitario/100))),
	                    T219_Percentual_Desconto=((1-(T219_Valor_Preco_Unitario/T219_Valor_Preco_Original))*100)
	              where T219_Id='$chaveValor'");
	mysqli_query("call T219_Gravar_Totalizacao('$chaveValor')");

	$T219=mysqli_query("select T219_T218_Id, T219_D006_Id, T219_T004_Id, T219_T006_Id from T219 where T219_Id='$chaveValor'");
	$mT219=mysql_fetch_array($T219);
	mysqli_query("UPDATE T218 SET T218_D006_Id='{$mT219['T219_D006_Id']}' WHERE T218_Id = '{$mT219['T219_T218_Id']}' AND (T218_D006_Id IS NULL OR T218_D006_Id <= 0)");

	mysqli_query("call T218_Gravar_Totalizacao_3('$mT219[T219_T218_Id]')");

	require_once('bibliotecas/classes/EST015.php');
	$EST015 = new EST015();
	$EST015->atualizarPrecoNegociado($chaveValor, $mT219['T219_T004_Id'], $mT219['T219_T006_Id']);

	require_once('bibliotecas/classes/ETC017.php');
	$ETC017 = new ETC017();
	$ETC017->inserirRegistroTarefa($mT219[T219_T218_Id], 'T218');

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT219();
EOT;

$conf['triggers']['T219']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T219']['updateAfter'] = <<<'EOT'
	mysqli_query("update T219 
	                set T219_Valor_Preco_Sem_Desconto_Unitario=T219_Valor_Preco_Unitario,
	                    T219_Valor_Preco_Original=T219_Valor_Preco_Unitario,
	                    T219_Valor_Desconto_Unitario=((T219_Valor_Preco_Original*(T219_Valor_Preco_Unitario/100))),
	                    T219_Percentual_Desconto=((1-(T219_Valor_Preco_Unitario/T219_Valor_Preco_Original))*100)
	              where T219_Id='$chaveValor'");                        
	mysqli_query("call T219_Gravar_Totalizacao('$chaveValor')");

	$T219=mysqli_query("select T219_T218_Id,T219_D009_Id,D049_Id,T218_D024_Id,T219_Codigo_Substituto,D049_D001_Id, T219_T004_Id, T219_T006_Id, T219_Valor_Preco_Unitario from T219 left join D009 on D009_Id=T219_D009_Id left join D049 on D049_Id=D009_D049_Id left join T218 on T218_Id=T219_T218_Id where T219_Id='$chaveValor'");
	$mT219=mysql_fetch_array($T219);
	mysqli_query("call T218_Gravar_Totalizacao_3('$mT219[T219_T218_Id]')");

	if($mT219['T219_Valor_Preco_Unitario'] > 0){
		$sql = mysql_query("SELECT T241_Id FROM T241 WHERE T241_D024_Id = '{$mT219['T218_D024_Id']}' AND T241_D001_Id = '{$mT219['D049_D001_Id']}'");
		if(mysql_num_rows($sql) <= 0){
			mysqli_query("INSERT INTO T241 (T241_D024_Id, T241_D001_Id, T241_Data, T241_Flag_Homologado) VALUES ('{$mT219['T218_D024_Id']}', '{$mT219['D049_D001_Id']}', CURDATE(), 'S')");
		} else {
			mysqli_query("UPDATE T241 SET T241_Flag_Homologado = 'S' WHERE T241_D024_Id = '{$mT219['T218_D024_Id']}' AND T241_D001_Id = '{$mT219['D049_D001_Id']}'");
		}
	}
	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->definirCodigoFornecedor($mT219['T219_D009_Id'], $mT219['T218_D024_Id'], $mT219['T219_Codigo_Substituto'], '', true, false);

	require_once('bibliotecas/classes/EST015.php');
	$EST015 = new EST015();
	$EST015->atualizarPrecoNegociado($chaveValor, $mT219['T219_T004_Id'], $mT219['T219_T006_Id']);

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT219();
EOT;

$conf['triggers']['T219']['deleteBefore'] = <<<'EOT'
	$sql = mysql_query("SELECT T219_T218_Id from T219 WHERE T219_Id='{$chaveValor}'");
	$mSql = mysql_fetch_array($sql);
	mysql_query("call T218_Gravar_Totalizacao_3({$mSql['T219_T218_Id']})");
EOT;

$conf['triggers']['T219']['deleteAfter'] = <<<'EOT'
EOT;
