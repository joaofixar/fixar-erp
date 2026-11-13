<?php
$conf['restricaoIncluirUpdate']['T075_C007_Id'] = <<<'EOT'
	$retorno = rVerificaFK('C007_Id', $valor);
EOT;

$conf['camposValorPadrao']['T075_Somar_PIS_Custo'] = 'S';
$conf['camposValorPadrao']['T075_Somar_COFINS_Custo'] = 'S';
$conf['camposValorPadrao']['T075_Somar_Seguro_Custo'] = 'S';
$conf['camposValorPadrao']['T075_Somar_AFRMM_Custo'] = 'S';

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T075']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T075']['insertAfter'] = <<<'EOT'
mysqli_query("call T055_Gravar_Totalizacao('$chaveValor')");
EOT;
$conf['triggers']['T075']['updateBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T075']['updateAfter'] = <<<'EOT'
$T055=mysqli_query("select T055_Id from T055 where T055_T075_Id='$chaveValor'");
while ($mT055=mysql_fetch_array($T055))
{
       mysqli_query("call T055_Gravar_Totalizacao('$mT055[T055_Id]')");
}
EOT;
$conf['triggers']['T075']['deleteBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T075']['deleteAfter'] = <<<'EOT'
EOT;
 
?>