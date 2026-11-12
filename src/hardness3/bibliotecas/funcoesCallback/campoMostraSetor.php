<?php
namespace hardness;

function campoMostraSetor ($array){
	return $array['todosCampos']['D001_D060_Id'] . ' - ' . $array['todosCampos']['D060_Descricao_Grupo'];
}
