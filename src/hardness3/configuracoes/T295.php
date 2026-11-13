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
$conf['triggers']['T295']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T295']['insertAfter'] = <<<'EOT'
EOT;

$conf['triggers']['T295']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T295']['updateAfter'] = <<<'EOT'

    // Vai chamar a funcao antes de gravar totalizacao porque ela calcula a base dos impostos conforme as despesas 
    require_once('bibliotecas/classes/IMP005.php');
    $IMP005 = new IMP005();
    $IMP005->calcularDespesas($chaveValor);  
    $IMP005->calculaCustoProduto($chaveValor);    

	$T296=mysqli_query("select T296_Id from T296 where T296_T295_Id='{$chaveValor}'");
	while($mT296=mysqli_fetch_array($T296)){
        mysqli_query("call T296_Gravar_Totalizacao('{$mT296[T296_Id]}')");
    }
    mysqli_query("call T295_Gravar_Totalizacao('{$chaveValor}')");
EOT;

$conf['triggers']['T295']['deleteBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T295']['deleteAfter'] = <<<'EOT'
EOT;

?>