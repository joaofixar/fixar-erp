<?php
namespace hardness;
function validaCampoCotarOrcamento($array){
	global $g;
	if(!isset($array['todosCampos']['T189_Id'])){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotar Produto', '/ven/ven001/content/ven001ContentCotar/', '&T003_Id=' + encodeURIComponent({$array['todosCampos']['T004_T003_Id']}) + '&T004_Id=' + encodeURIComponent({$array['todosCampos']['T004_Id']}) + '&D009_Id=' + encodeURIComponent({$array['todosCampos']['T004_D009_Id']}), [350,250]);\"><b>Cotar</b></a>";
	} else {
		return '';
	}
}