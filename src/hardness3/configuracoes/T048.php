<?php
// triggers
$conf['triggers']['T048']['insertAfter'] = <<<'EOT'
	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT048();
EOT;

$conf['triggers']['T048']['updateAfter'] = <<<'EOT'
	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT048();
EOT;
