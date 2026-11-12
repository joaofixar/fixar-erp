<?php
namespace hardness;
function estoqueDM($array){
	global $g;
	if ($array['todosCampos']['D009_Quantidade_DM'] == '0') { $array['todosCampos']['D009_Quantidade_DM'] = '0.00'; }
	$array['todosCampos']['D009_Quantidade_DM'] = gCorrigeNumero($array['todosCampos']['D009_Quantidade_DM']);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Produtos em Demonstração', '/est/est003/content/est003ContentProdutosDefeitos/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [700,400]); return false;\">{$array['todosCampos']['D009_Quantidade_DM']}</a>";
}
