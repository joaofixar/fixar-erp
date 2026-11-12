<?php
namespace hardness;

function mostrarConta($array) {
	return substr($array['todosCampos']['D014_SubConta'],0,20);
}
