<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'notificacao':
		$post = json_decode(file_get_contents('php://input'));
		if(empty($post)){
			die();
		}
	    $post = (array) $post;

	    if($post['id'] == "572b809816112c9704829bf3" || $post['id'] == "572b8094514766260aa23310"){
	    	die();
	    }
		require_once('bibliotecas/webservices/pluggto/integracaoPluggto.php');
		$integracao = new Pluggto();
		$integracao->processarNotificacao($post);
	break;
}
