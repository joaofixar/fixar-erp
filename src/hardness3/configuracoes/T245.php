<?php
// triggers
$conf['triggers']['T245']['insertAfter'] = <<<'EOT'
	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT245();
EOT;

$conf['triggers']['T245']['updateAfter'] = <<<'EOT'
	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT245();
EOT;
