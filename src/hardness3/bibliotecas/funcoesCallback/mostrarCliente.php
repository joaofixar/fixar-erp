<?php
namespace hardness;

function mostrarCliente($array) {
	return substr($array['todosCampos']['D024_Nome_Empresa'],0,20);
}
