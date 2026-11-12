<?php
namespace hardness;
function totalDescontoPedidosCliente($array){
	global $g;
	$T005_D024_Id = $array['todosCampos']['T005_D024_Id'];
	$sqlT005 = mysql_query("SELECT SUM(T005_Valor_Desconto) as Descontos
							FROM T005 
							WHERE T005_D024_Id = '{$T005_D024_Id}'
							AND (T005_Flag_Status = '' OR T005_Flag_Status = 0)");
	$rowT005 = mysql_fetch_array($sqlT005);

	$tamanho = isset($param[0]) ? $param[0] : 15;
	$explode = isset($param[1]) ? $param[1] : '';
	
	$array['todosCampos']['campoOriginal'] = $T005_D024_Id;
	$array['campoAtual'] = gCorrigeNumero($rowT005['Descontos']);
	require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
	return abrirNotificacao(
		$array,
		array(
			'<color=black>Descontos<color>',
			'/cad/cad002/outro/descontosPedidosCliente/',
			'campoOriginal',
			'right',
			'',
			$explode,
			'',
			'355'
		)
	);
}