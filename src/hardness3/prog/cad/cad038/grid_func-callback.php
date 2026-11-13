<?php
namespace hardness;
/**
 * Weiberlan
 * 07/12/2011
 */
/**
* 
*  UTILIZADO EM OUTRO MÓDULO
* 
function campoDebitoCredito($array){
		if ($array['todosCampos']['D014_Flag_Tipo']=='RC'){
			return "(+)";
		}elseif($array['todosCampos']['D014_Flag_Tipo']=='PG'){
		return "(-)";
		}
}
*/

function campoTipo($array){
	if($array['todosCampos']['D122_Tipo'] == '1'){
		return "Balsa";
	}elseif($array['todosCampos']['D122_Tipo'] == '2'){
		return "Imóvel";
	}elseif($array['todosCampos']['D122_Tipo'] == '3'){
		return "Veículo";
	}
}

function campoImpressao($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	//exemplo de janela de impressao: onclick="nova_janela('/hardness/almoxarifado/imprimir_pedido_almoxarifado.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');"
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