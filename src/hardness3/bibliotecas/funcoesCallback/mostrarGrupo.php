<?php
namespace hardness;

function mostrarGrupo($array) {
	return substr($array['todosCampos']['D032_Descricao'],0,20);
}
