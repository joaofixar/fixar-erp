<?php
namespace hardness;

function campoMostraDepartamento ($array){
	return $array['todosCampos']['D001_C008_Id'] . ' - ' . $array['todosCampos']['C008_Tipo'];
}
