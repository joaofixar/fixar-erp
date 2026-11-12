<?php
$conf['restricaoIncluirUpdate']['T047_Data'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

// triggers
$conf['triggers']['T047']['insertAfter'] = <<<'EOT'
	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT047();
EOT;

$conf['triggers']['T047']['updateAfter'] = <<<'EOT'
	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT047();
EOT;
