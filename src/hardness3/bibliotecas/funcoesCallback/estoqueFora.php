<?php
namespace hardness;

function estoqueFora($array, $param=false){
	global $g;
	$D049_Id = $array['todosCampos']['D049_Id'];
	$estoqueTotalExterno = gCorrigeNumero($array['todosCampos']['D009_Quantidade_Estoque_Fora']);

	$T003_Id = $T005_Id = $T007_Id = false;
	if(isset($param[0]) && $param[0] > 0){
		$T003_Id = $param[0];
		$idItemVenda = $array['todosCampos']['T004_Id'];
	} else if(isset($param[1]) && $param[1] > 0) {
		$T005_Id = $param[1];
		$idItemVenda = $array['todosCampos']['T006_Id'];
	} else if(isset($param[2]) && $param[2] > 0) {
		$T007_Id = $param[2];
		$idItemVenda = $array['todosCampos']['T008_Id'];
	}

	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D001_Id']}') + '&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&T007_Id=' + encodeURIComponent('{$T007_Id}') + '&idItemVenda=' + encodeURIComponent('{$idItemVenda}'), [700,400]); return false;\">{$estoqueTotalExterno}</a>";
}
