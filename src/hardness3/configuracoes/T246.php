<?php
/**
 * Tabela T246
 */
 
/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T246']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T246']['insertAfter'] = <<<'EOT'
$T246 = mysql_query("SELECT T246_T002_Id FROM T246 WHERE T246_Id='{$chaveValor}'");
$row = mysql_fetch_array($T246);
mysqli_query("call T002_Gravar_Totalizacao('{$row['T246_T002_Id']}')");
EOT;

$conf['triggers']['T246']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T246']['updateAfter'] = <<<'EOT'
$T246 = mysql_query("SELECT T246_T002_Id FROM T246 WHERE T246_Id='{$chaveValor}'");
$row = mysql_fetch_array($T246);
mysqli_query("call T002_Gravar_Totalizacao('{$row['T246_T002_Id']}')");
EOT;

$conf['triggers']['T246']['deleteBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T246']['deleteAfter'] = <<<'EOT'
EOT;

$conf['restricaoTravar']['T246'] = <<<'EOT'

EOT;

?>