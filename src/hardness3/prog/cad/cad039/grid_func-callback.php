<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */

function campoDebitoCredito($array){
		if ($array['todosCampos']['D014_Flag_Tipo']=='RC'){
			return "(+)";
		}elseif($array['todosCampos']['D014_Flag_Tipo']=='PG'){
		return "(-)";
		}
}
function campoImpressao($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	//onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Recibo Locação', '/cad/cad035/outro/cad035locacaoBensMoveis/','&T156_Id={$array['todosCampos']['T156_Id']}', [1000,700]);	
	$imagem = <<<EOT
		<span onclick="">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
?>