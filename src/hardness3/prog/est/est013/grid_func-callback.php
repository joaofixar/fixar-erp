<?php
namespace hardness;

function corrigePeso($array) {
	return gCorrigeNumero($array['campoAtual']) . " Kg";
}

function corrigeNumero($array) {
	return gCorrigeNumero($array['campoAtual']);
}

$nrLinha = 0;
function nrLinha($array) {
	global $nrLinha;
	return ++$nrLinha;
}
	
?>