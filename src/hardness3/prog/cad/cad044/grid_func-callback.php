<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */

function exibirCodigoHTML($array){
		return htmlentities($array['todosCampos']['D130_Conteudo'],ENT_COMPAT,'utf-8');
}
?>