<?php
$conf['restricaoIncluirUpdate']['T025_D005_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T025_D015_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D015_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T025_D002_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D002_Id', $valor);
EOT;
?>