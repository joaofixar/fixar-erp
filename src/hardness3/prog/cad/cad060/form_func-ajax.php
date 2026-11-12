<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'cad060IncluirVariavelIPV':
		mysql_query("INSERT INTO T249 (T249_C004_Id) VALUES ('{$g['empresaAtual']}')");
		$erro = mysql_error();
		if(!empty($erro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao incluir variável do IPV";
		} else {
			$resposta['code'] = true;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
