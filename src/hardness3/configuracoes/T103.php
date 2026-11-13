<?php
/**
 * Tabela T002

 */
 
/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T103']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T103']['insertAfter'] = <<<'EOT'
    // Código PHP
EOT;

$conf['triggers']['T103']['updateBefore'] = <<<'EOT'
    // Código PHP
EOT;

$conf['triggers']['T103']['updateAfter'] = <<<'EOT'

    $T103  = mysql_query("SELECT T103_Valor_Recebido, T103_Valor_Juros, T103_Valor_Taxas, T103_Valor_Desconto FROM T103 WHERE T103_Id = '{$chaveValor}'");
    $mT103 = mysql_fetch_array($T103);
    $total = $mT103['T103_Valor_Recebido'] + $mT103['T103_Valor_Juros'] + $mT103['T103_Valor_Taxas'] - $mT103['T103_Valor_Desconto'];
    mysqli_query("UPDATE T103 SET T103_Valor_Total_Recebido = '{$total}' WHERE T103_Id = '{$chaveValor}'");
    
EOT;

$conf['triggers']['T103']['deleteBefore'] = <<<'EOT'
    // Código PHP
EOT;

$conf['triggers']['T103']['deleteAfter'] = <<<'EOT'
    // Código PHP
EOT;



?>