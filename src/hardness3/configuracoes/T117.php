<?php
$conf['restricaoIncluirUpdate']['T117_D024_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D024_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T117_D007_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D007_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T117_Data_Vencimento'] = <<<'EOT'
	$retorno = rDataMaiorIgualAtual($valor);
EOT;
?>
