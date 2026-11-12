<?php
namespace hardness;
/**
 * Ações
 */
switch($g['r'][3]){
	// GRID
	case 'pega':
		require_once('/home/flycker/html/hardness/exp_viasoft/funcoes_genericas.inc');
		require_once($g['pathRaiz'] . 'tmp/pegaRelacionamento.php');
		$result = PegaRelacionamento('D116', 1);
	//$Entidade, $Id_Entidade, $Excecao=''
	echo "<br/>";
	break;
}
?>