<?php
namespace hardness;

function estoqueDisponivelMontagem($array, $param = array()){
	global $g;

	$sql = mysql_query("SELECT MIN(floor(D009_Quantidade_Estoque_Real/D042_Quantidade)) as estoqueDisponivel FROM D042 
		left join D049 on D049_Id=D042_D049_Id_Itens 
		left join D082 on D049_D082_Id=D082_Id 
		left join D001 on D001_Id=D049_D001_Id 
		left join D009 on D009_D049_Id=D042_D049_Id_Itens
		WHERE D042_D049_Id='{$array['todosCampos']['D049_Id']}' AND D009_C004_Id = '{$g['empresaAtual']}'");
	$row = mysql_fetch_array($sql);
	$row['estoqueDisponivel'] = gCorrigeNumero($row['estoqueDisponivel']);
	return "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Estoque disponível para montagem', '/cad/cad002/content/cad002ContentItensMontagem/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [900,550]); return false;\">{$row['estoqueDisponivel']}</a>";
}
