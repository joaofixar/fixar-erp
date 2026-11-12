<?php
/**
 * Tabela T055

 */
/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T055']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T055']['insertAfter'] = <<<'EOT'
	$T055 =mysqli_query("select T055_D009_Id from T055 WHERE T055_Id='{$chaveValor}'");
	$mT055=mysqli_fetch_array($T055);

	mysqli_query("call T055_Gravar_Totalizacao('$chaveValor')");
	mysqli_query("call T055_Gravar_Saldo_Disponivel('$chaveValor', 'I')");

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->reprocessaOCPedidos($mT055['T055_D009_Id']);
EOT;

$conf['triggers']['T055']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T055']['updateAfter'] = <<<'EOT'
	$T055 =mysqli_query("select T055_D009_Id from T055 WHERE T055_Id='{$chaveValor}'");
	$mT055=mysqli_fetch_array($T055);

	mysqli_query("call T055_Gravar_Totalizacao('$chaveValor')");
	mysqli_query("call T055_Gravar_Saldo_Disponivel('$chaveValor', 'U')");

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->reprocessaOCPedidos($mT055['T055_D009_Id']);
EOT;

$conf['triggers']['T055']['deleteBefore'] = <<<'EOT'
	global $T055_D009_Id_Apos_Excluir;

	$T055=mysqli_query("SELECT T055_D009_Id FROM T055 WHERE T055_Id='{$chaveValor}'");
	$mT055=mysql_fetch_array($T055);

	$T055_D009_Id_Apos_Excluir = $mT055['T055_D009_Id'];

	mysqli_query("call T055_Gravar_Saldo_Disponivel('$chaveValor', 'D')");
EOT;

$conf['triggers']['T055']['deleteAfter'] = <<<'EOT'
	global $T055_D009_Id_Apos_Excluir;

	mysqli_query("update D009 set D009_Quantidade_OC=D009_Quantidade_OC_3($T055_D009_Id_Apos_Excluir,0) where D009_Id = '{$T055_D009_Id_Apos_Excluir}'");
	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->reprocessaOCPedidos($T055_D009_Id_Apos_Excluir);
EOT;

$conf['restricaoIncluirUpdate']['T055_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;

 
?>