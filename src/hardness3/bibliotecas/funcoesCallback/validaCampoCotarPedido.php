<?php
namespace hardness;
function validaCampoCotarPedido($array){
	global $g;
	if(!isset($array['todosCampos']['T189_Id'])){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotar Produto', '/ven/ven002/content/ven002ContentCotar/', '&T005_Id=' + encodeURIComponent({$array['todosCampos']['T006_T005_Id']}) + '&T006_Id=' + encodeURIComponent({$array['todosCampos']['T006_Id']}) + '&D009_Id=' + encodeURIComponent({$array['todosCampos']['T006_D009_Id']}), [350,250]);\"><b>Cotar</b></a>";
	} else {
		return '';
	}
}