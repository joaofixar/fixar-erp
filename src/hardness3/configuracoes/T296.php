<?php
/**
 * Tabela T296

 */
/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T296']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T296']['insertAfter'] = <<<'EOT'
	$T295 =mysqli_query("select T296_T295_Id,T296_D009_Id from T296 where T296_Id='$chaveValor'");
	$mT295=mysqli_fetch_array($T295);

	// Vai chamar a funcao antes de gravar totalizacao porque ela calcula a base dos impostos conforme as despesas
	require_once('bibliotecas/classes/IMP005.php');
	$IMP005 = new IMP005();
	$IMP005->calcularDespesas($mT295[T296_T295_Id]);
	$IMP005->calculaCustoProduto($mT295[T296_T295_Id]);

	$T296 = mysqli_query("SELECT T296_Id FROM T296 WHERE T296_T295_Id = '{$mT295[T296_T295_Id]}'");
	while($mT296 = mysql_fetch_array($T296)){
		mysqli_query("call T296_Gravar_Totalizacao('{$mT296[T296_Id]}')");
	}	

	mysqli_query("call T295_Gravar_Totalizacao('$mT295[T296_T295_Id]')");
	mysqli_query("call T296_Gravar_Saldo_Disponivel('$chaveValor', 'U')");

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->reprocessaOCPedidos($mT295['T296_D009_Id']);
EOT;

$conf['triggers']['T296']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T296']['updateAfter'] = <<<'EOT'
	$T295 =mysqli_query("select T296_T295_Id,T296_D009_Id,T295_D024_Id,T296_Codigo_Produto_Fornecedor from T296 left join T295 on T295_Id=T296_T295_Id where T296_Id='{$chaveValor}'");
	$mT295=mysqli_fetch_array($T295);

	log("qual é o id agora ".$mT296[T296_T295_Id]);
	// Vai chamar a funcao antes de gravar totalizacao porque ela calcula a base dos impostos conforme as despesas 
	require_once('bibliotecas/classes/IMP005.php');
	$IMP005 = new IMP005();
	$IMP005->calcularDespesas($mT295[T296_T295_Id]);
	$IMP005->calculaCustoProduto($mT295[T296_T295_Id]);

	$T296 = mysqli_query("SELECT T296_Id FROM T296 WHERE T296_T295_Id = '{$mT295[T296_T295_Id]}'");
	while($mT296 = mysql_fetch_array($T296)){
		mysqli_query("call T296_Gravar_Totalizacao('{$mT296[T296_Id]}')");
	}	

	mysqli_query("call T295_Gravar_Totalizacao('{$mT295[T296_T295_Id]}')");
	mysqli_query("call T296_Gravar_Saldo_Disponivel('$chaveValor', 'U')");

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->definirCodigoFornecedor($mT295['T296_D009_Id'], $mT295['T295_D024_Id'], $mT295['T296_Codigo_Produto_Fornecedor'], '', false, true);
	$CAD002->reprocessaOCPedidos($mT295['T296_D009_Id']);
EOT;

$conf['triggers']['T296']['deleteBefore'] = <<<'EOT'
	global $T296_T295_Id_Apos_Excluir,$T296_D009_Id_Apos_Excluir;

	$T296=mysqli_query("SELECT T296_T295_Id,T296_D009_Id FROM T296 WHERE T296_Id='{$chaveValor}'");
	$mT296 = mysql_fetch_array($T296);

	$T296_T295_Id_Apos_Excluir = $mT296['T296_T295_Id'];
	$T296_D009_Id_Apos_Excluir = $mT296['T296_D009_Id'];

	mysqli_query("call T296_Gravar_Saldo_Disponivel('$chaveValor', 'D')");
EOT;

$conf['triggers']['T296']['deleteAfter'] = <<<'EOT'
	global $T296_T295_Id_Apos_Excluir,$T296_D009_Id_Apos_Excluir;

	// Vai chamar a funcao antes de gravar totalizacao porque ela calcula a base dos impostos conforme as despesas
	require_once('bibliotecas/classes/IMP005.php');
	$IMP005 = new IMP005();
	$IMP005->calcularDespesas($T296_T295_Id_Apos_Excluir);
	$IMP005->calculaCustoProduto($T296_T295_Id_Apos_Excluir);

	$T296 = mysqli_query("SELECT T296_Id FROM T296 WHERE T296_T295_Id = '{$T296_T295_Id_Apos_Excluir}'");
	while($mT296 = mysql_fetch_array($T296)){
		mysqli_query("call T296_Gravar_Totalizacao('{$mT296[T296_Id]}')");
	}
	mysqli_query("call T295_Gravar_Totalizacao('{$T296_T295_Id_Apos_Excluir}')");

	mysqli_query("update D009 set D009_Quantidade_OC=D009_Quantidade_OC_3($T296_D009_Id_Apos_Excluir,0) where D009_Id = '{$T296_D009_Id_Apos_Excluir}'");
	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->reprocessaOCPedidos($T296_D009_Id_Apos_Excluir);
EOT;

$conf['restricaoIncluirUpdate']['T296_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;

 
?>