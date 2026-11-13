<?php
namespace hardness;
// Pedido, D049_Id
function CAD002checkboxCampoPromocao($array){
    global $g;
    $D009_Id = $array['todosCampos']['D009_Id'];
    
    $retorno = <<<EOT
		<input type='checkbox' name='D009_Flag_Promocao' title='Promoção?' onChange='cad002checkboxCampoPromocao(this, "{$D009_Id}", "{$g['divId']}")'
EOT;
	if ($array['todosCampos']['D009_Flag_Promocao']=='S'){
		$retorno .= <<<EOT
		checked = 'checked'
EOT;
	}
	$retorno .= <<<EOT
	 />
EOT;
	return $retorno;
}