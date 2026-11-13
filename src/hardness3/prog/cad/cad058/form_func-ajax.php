<?php
namespace hardness;
/**
 * Weiberlan
 * 29/06/2012
 * FIN - FIN002
 *
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'alteraConta':
		$D032_Id = isset($r_D032_Id) ? $r_D032_Id : false;
		$resposta['code'] = true;
		$resposta['data']['contaSTContasPagar'] = gProcessaSelect(gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta', 'LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '.$D032_Id.' AND D014_Flag_Ativo="S" ORDER BY D014_SubConta', true));
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
