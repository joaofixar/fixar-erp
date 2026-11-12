<?php
namespace hardness;

function campoMostraSubGrupo ($array){
	return $array['todosCampos']['D001_D002_Id'] . ' - ' . $array['todosCampos']['D002_Descricao_Produto'];
}
