<?php
namespace hardness;

//funções gerais
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();
//funções pertinentes a api de clientes
require_once('bibliotecas/classes/API002.php');
$API002 = new API002();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'clientes':
		die("entrou no patch");
	break;

	default:
		$API001->retorno404();
}