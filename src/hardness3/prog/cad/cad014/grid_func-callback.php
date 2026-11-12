<?php
namespace hardness;
/**
 * CAD - CAD014 - Cadastro de Bancos/Caixa
 */

function campoTelefone($array) {
	return "({$array['todosCampos']['D007_DDD_Telefone']}) {$array['todosCampos']['D007_Telefone']}";
}
?>