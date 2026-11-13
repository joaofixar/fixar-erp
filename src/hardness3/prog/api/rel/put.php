<?php
namespace hardness;

//funções gerais
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();
//funções pertinentes a api de relatórios
require_once('bibliotecas/classes/API003.php');
$API003 = new API003();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'clientes':
		die("entrou no put");
	break;

	default:
		$API001->retorno404();
}