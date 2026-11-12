<?php
namespace hardness;

function campoMostraTitle($array, $param){
	return "<span title=\"".$array['todosCampos'][$param]."\">{$array['campoAtual']}</span>";
}