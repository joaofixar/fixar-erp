<?php
$conf['triggers']['T274']['updateAfter'] = <<<'EOT'
	
	$T274=mysqli_query("SELECT T274_T234_Id,T234_Chamado_Origem_Id FROM T274 LEFT JOIN T234 ON T274_T234_Id=T234_Id WHERE T274_Id='{$chaveValor}'");
	$mT274 = mysql_fetch_array($T274);

	mysqli_query("CALL T234_Gravar_Totalizacao('{$mT274['T274_T234_Id']}')");
	mysqli_query("CALL T234_Gravar_Totalizacao('{$mT274['T234_Chamado_Origem_Id']}')");

EOT;

$conf['triggers']['T274']['insertAfter'] = <<<'EOT'
	
	$T274=mysqli_query("SELECT T274_T234_Id,T234_Chamado_Origem_Id FROM T274 LEFT JOIN T234 ON T274_T234_Id=T234_Id WHERE T274_Id='{$chaveValor}'");
	$mT274 = mysql_fetch_array($T274);

	mysqli_query("CALL T234_Gravar_Totalizacao('{$mT274['T274_T234_Id']}')");
	mysqli_query("CALL T234_Gravar_Totalizacao('{$mT274['T234_Chamado_Origem_Id']}')");

EOT;
?>