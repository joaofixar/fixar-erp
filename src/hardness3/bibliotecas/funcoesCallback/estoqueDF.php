<?php
namespace hardness;
function estoqueDF($array){
	global $g;
	if ($array['todosCampos']['D009_Quantidade_DF'] == '0') { $array['todosCampos']['D009_Quantidade_DF'] = '0.00'; }
	$array['todosCampos']['D009_Quantidade_DF'] = gCorrigeNumero($array['todosCampos']['D009_Quantidade_DF']);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Produtos com Defeitos', '/est/est003/content/est003ContentProdutosDefeitos/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [700,400]); return false;\">{$array['todosCampos']['D009_Quantidade_DF']}</a>";
}
