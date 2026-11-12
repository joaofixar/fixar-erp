<?php
/**
 * Tabela T294

 */
/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T294']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T294']['insertAfter'] = <<<'EOT'
	$T293 =mysqli_query("select T294_T293_Id from T294 where T294_Id='$chaveValor'");
	$mT293=mysqli_fetch_array($T293);

	// Vai chamar a funcao antes de gravar totalizacao porque ela calcula a base dos impostos conforme as despesas
	require_once('bibliotecas/classes/IMP004.php');
	$IMP004 = new IMP004();
	$IMP004->calcularDespesas($mT293[T294_T293_Id]);
	$IMP004->calculaCustoProduto($mT293[T294_T293_Id]);

	$T294 = mysqli_query("SELECT T294_Id FROM T294 WHERE T294_T293_Id = '{$mT293[T294_T293_Id]}'");
	while($mT294 = mysql_fetch_array($T294)){
		mysqli_query("call T294_Gravar_Totalizacao('{$mT294[T294_Id]}')");
	}	

	mysqli_query("call T293_Gravar_Totalizacao('$mT293[T294_T293_Id]')");

	mysqli_query("UPDATE T294 SET T294_Quantidade_Saldo = T294_Quantidade WHERE T294_Id = '{$chaveValor}'");
EOT;

$conf['triggers']['T294']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T294']['updateAfter'] = <<<'EOT'
	$T293 =mysqli_query("select T294_T293_Id,T294_D009_Id,T293_D024_Id,T294_Codigo_Produto_Fornecedor FROM T294 LEFT JOIN T293 ON T293_Id = T294_T293_Id WHERE T294_Id='{$chaveValor}'");
	$mT293=mysqli_fetch_array($T293);

	// Vai chamar a funcao antes de gravar totalizacao porque ela calcula a base dos impostos conforme as despesas 
	require_once('bibliotecas/classes/IMP004.php');
	$IMP004 = new IMP004();
	$IMP004->calcularDespesas($mT293[T294_T293_Id]);
	$IMP004->calculaCustoProduto($mT293[T294_T293_Id]);

	$T294 = mysqli_query("SELECT T294_Id FROM T294 WHERE T294_T293_Id = '{$mT293[T294_T293_Id]}'");
	while($mT294 = mysql_fetch_array($T294)){
		mysqli_query("call T294_Gravar_Totalizacao('{$mT294[T294_Id]}')");
	}	

	mysqli_query("call T293_Gravar_Totalizacao('{$mT293[T294_T293_Id]}')");
	mysqli_query("call T294_Gravar_Saldo_Disponivel('$chaveValor')");

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->definirCodigoFornecedor($mT293['T294_D009_Id'], $mT293['T293_D024_Id'], $mT293['T294_Codigo_Produto_Fornecedor'], '', false, true);
EOT;

$conf['triggers']['T294']['deleteBefore'] = <<<'EOT'
	global $T294_T293_Id_Apos_Excluir;

	$T294=mysqli_query("SELECT T294_T293_Id FROM T294 WHERE T294_Id='{$chaveValor}'");
	$mT294 = mysql_fetch_array($T294);

	$T294_T293_Id_Apos_Excluir = $mT294['T294_T293_Id'];
EOT;

$conf['triggers']['T294']['deleteAfter'] = <<<'EOT'
	global $T294_T293_Id_Apos_Excluir;

	// Vai chamar a funcao antes de gravar totalizacao porque ela calcula a base dos impostos conforme as despesas
	require_once('bibliotecas/classes/IMP004.php');
	$IMP004 = new IMP004();
	$IMP004->calcularDespesas($T294_T293_Id_Apos_Excluir);
	$IMP004->calculaCustoProduto($T294_T293_Id_Apos_Excluir);

	$T294 = mysqli_query("SELECT T294_Id FROM T294 WHERE T294_T293_Id = '{$T294_T293_Id_Apos_Excluir}'");
	while($mT294 = mysql_fetch_array($T294)){
		mysqli_query("call T294_Gravar_Totalizacao('{$mT294[T294_Id]}')");
	}
	mysqli_query("call T293_Gravar_Totalizacao('{$T294_T293_Id_Apos_Excluir}')");
EOT;

$conf['restricaoIncluirUpdate']['T294_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;

?>