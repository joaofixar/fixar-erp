<?php
$conf['triggers']['T210']['updateAfter'] = <<<'EOT'
	$sqlT012 = mysqli_query("SELECT * FROM T012 LEFT JOIN T210 ON T210_T012_Id = T012_Id WHERE T210_Id='{$chaveValor}'");
	$T012 = mysql_fetch_array($sqlT012);
	mysqli_query("CALL T011_Gravar_Totalizacao_3('$T012[T012_T011_Id]')");
EOT;

$conf['triggers']['T210']['insertAfter'] = <<<'EOT'
	$sqlT012 = mysqli_query("SELECT * FROM T012 LEFT JOIN T210 ON T210_T012_Id = T012_Id WHERE T210_Id='{$chaveValor}'");
	$T012 = mysql_fetch_array($sqlT012);
	mysqli_query("CALL T011_Gravar_Totalizacao_3('$T012[T012_T011_Id]')");
EOT;

$conf['triggers']['T210']['deleteAfter'] = <<<'EOT'
	$sqlT012 = mysqli_query("SELECT * FROM T012 LEFT JOIN T210 ON T210_T012_Id = T012_Id WHERE T210_Id='{$chaveValor}'");
	$T012 = mysql_fetch_array($sqlT012);
	mysqli_query("CALL T011_Gravar_Totalizacao_3('$T012[T012_T011_Id]')");	
EOT;
