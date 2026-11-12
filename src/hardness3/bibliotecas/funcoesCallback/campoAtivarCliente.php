<?php
namespace hardness;

function campoAtivarCliente($array){
	global $g;
	if($g['usuarioAtual'] != $array['todosCampos']['D024_C007_Vendedor_Interno']){ 
		return <<<EOT
			<span><a href="#" onclick="campoAtivarCliente('{$array['todosCampos']['D024_Id']}', '{$g['divId']}'); return false;">Ativar</a></span>
EOT;
	} else {
		return '';
	}
}
