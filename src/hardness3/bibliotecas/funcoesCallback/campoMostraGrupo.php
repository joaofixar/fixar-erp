<?php
namespace hardness;

function campoMostraGrupo ($array){
	return $array['todosCampos']['D001_D003_Id'] . ' - ' . $array['todosCampos']['D003_Nome_Linha'];
}
