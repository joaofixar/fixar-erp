<?php
namespace hardness;
// Pedido, D049_Id
function campoMostrarBoletos($array){
	global $g, $confUsuario;
	$T117_Id = $array['todosCampos']['T117_Id'];
	$T117_Nosso_Numero = $array['todosCampos']['T117_Nosso_Numero'];
	
	return "<a href=\"#\" <input onclick=\"imprimirBoleto('{$T117_Id}', '{$confUsuario['urlRaiz']}')\" style='cursor:pointer'>{$T117_Nosso_Numero}</a>";
	
}