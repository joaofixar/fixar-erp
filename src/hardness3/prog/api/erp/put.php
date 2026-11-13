<?php
namespace hardness;

//funções gerais
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();
//funções pertinentes a api de erp
require_once('bibliotecas/classes/API004.php');
$API004 = new API004();

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