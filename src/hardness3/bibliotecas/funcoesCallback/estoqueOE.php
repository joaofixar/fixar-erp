<?php
namespace hardness;
function estoqueOE($array){
	global $g;
	if ($array['todosCampos']['D009_Quantidade_OE'] == '0') { $array['todosCampos']['D009_Quantidade_OE'] = '0.00'; }
	$array['todosCampos']['D009_Quantidade_OE'] = gCorrigeNumero($array['todosCampos']['D009_Quantidade_OE']);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Produtos com Defeitos', '/est/est003/content/est003ContentEstoqueOE/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [700,400]); return false;\">{$array['todosCampos']['D009_Quantidade_OE']}</a>";
}
