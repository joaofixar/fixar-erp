<?php
namespace hardness;

//funções gerais e funções pertinentes a api do mobile
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();


/**
 * Ações
 */
switch($g['r'][3]) {
	case 'produtos':
		
	break;

	case 'empresasContato':
		

	break;

	default:
		$API001->retorno404();
}