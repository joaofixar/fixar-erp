<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * VEN - VEN002 - Pedidos
 */

function retornaOperacaoTroca($array){
	if ($array['todosCampos']['T138_Operacao']=='P')
		return 'PARTE PAGAMENTO';
	else 	
		return 'TROCA';	
	
}

function campoMarcarInspecionado($array){
	global $g;
	if ($array['todosCampos']['T138_Data_Inspecao'])
		return '<span style="color:green;font-weight:bold">OK</span>';
		
	$qte=round($array['todosCampos']['T138_Quantidade']);	
	$imagem = <<<EOT
		<span onclick="srv004MarcarInspecionado('{$array['todosCampos']['T138_Id']}','{$array['todosCampos']['D001_Descricao_Produto']}','{$qte}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all" style="margin-left:11px;">
		   		<span class="ui-icon ui-icon-check" title="Aprovar inspeção do produto"></span>
		   </div>
		</span>
EOT;
	return $imagem;
	
}

?>