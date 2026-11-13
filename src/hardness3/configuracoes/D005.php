<?php
$conf['restricaoIncluirUpdate']['D005_Classificacao_Fiscal'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D005_Flag_Aplicacao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D005_D088_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D088_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D005_Flag_ST'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['triggers']['D005']['updateAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/FIS003.php');
	$FIS003 = new FIS003();
	$FIS003->cadastrarNCM("{$chaveValor}");
EOT;
$conf['triggers']['D005']['insertAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/FIS003.php');
	$FIS003 = new FIS003();
	$FIS003->cadastrarNCM("{$chaveValor}");
EOT;

/**
* ignorarRelacionamentoExcluir
*/
$conf['ignorarRelacionamentoExcluir'] = array_merge($conf['ignorarRelacionamentoExcluir'], 
array('D149_D005_Id'));	

$conf['triggers']['D005']['deleteAfter'] = <<<'EOT'
	mysql_query("DELETE FROM D149 WHERE D149_D005_Id = '{$chaveValor}'");	
EOT;
?>