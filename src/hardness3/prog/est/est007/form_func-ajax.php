<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/EST007.php');
$EST007 = new EST007;

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'est007atualizarCamposProdutos':
		$D049_Id = isset($r_D049_Id) ? $r_D049_Id : false;
		$T047_Id = isset($r_T047_Id) ? $r_T047_Id : false;
		
		$retorno = $EST007->est007gravarProduto($D049_Id, $T047_Id);
		
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est007atualizarCamposProdutosSubItens':
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T048_Id = isset($r_T048_Id) ? $r_T048_Id : false;
		
		$retorno = $EST007->est007gravarProdutoSubItens($D009_Id, $T048_Id);
		
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est007ProcessarMontagem':
		$T047_Id = isset($r_T047_Id) ? $r_T047_Id : false;
		
		$retorno = $EST007->est007ProcessarMontagem($T047_Id);
		
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Processo Concluído com sucesso";
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>