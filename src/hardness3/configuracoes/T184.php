<?php
/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T184']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T184']['insertAfter'] = <<<'EOT'
EOT;
$conf['triggers']['T184']['updateBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T184']['updateAfter'] = <<<'EOT'
EOT;
$conf['triggers']['T184']['deleteBefore'] = <<<'EOT'
	$resultado = mysqli_query("select * from T184 where T184_Id=$chaveValor");
	$row= \mysql_fetch_array($resultado);
	mysqli_query("update T182 set T182_Status='P' where T182_Id={$row['T184_T182_Id']}");	
EOT;
$conf['triggers']['T184']['deleteAfter'] = <<<'EOT'
EOT;

?>