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
$conf['triggers']['T293']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T293']['insertAfter'] = <<<'EOT'
EOT;

$conf['triggers']['T293']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T293']['updateAfter'] = <<<'EOT'

    // Vai chamar a funcao antes de gravar totalizacao porque ela calcula a base dos impostos conforme as despesas 
    require_once('bibliotecas/classes/IMP004.php');
    $IMP004 = new IMP004();
    $IMP004->calcularDespesas($chaveValor);
    $IMP004->calculaCustoProduto($chaveValor);

	$T294=mysqli_query("select T294_Id from T294 where T294_T293_Id='{$chaveValor}'");
	while($mT294=mysqli_fetch_array($T294)){    
        mysqli_query("call T294_Gravar_Totalizacao('{$mT294[T294_Id]}')");
    }

    mysqli_query("call T293_Gravar_Totalizacao('{$chaveValor}')");
EOT;

$conf['triggers']['T293']['deleteBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T293']['deleteAfter'] = <<<'EOT'
EOT;

?>