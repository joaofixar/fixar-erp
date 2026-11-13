<?php
namespace hardness;
// Pedido, D049_Id
function CAD002checkboxCampoPrecoTabelado($array){
   global $g;
    $D009_Id = $array['todosCampos']['D009_Id'];
    
    $retorno = <<<EOT
		<input type='checkbox' name='D009_Flag_Preco_Tabelado' title='Preço tabelado?' onChange='cad002checkboxCampoPrecoTabelado(this, "{$D009_Id}", "{$g['divId']}")'
EOT;
	if ($array['todosCampos']['D009_Flag_Preco_Tabelado']=='S'){
		$retorno .= <<<EOT
		checked = 'checked'
EOT;
	}
	$retorno .= <<<EOT
	 />
EOT;
	return $retorno;
}