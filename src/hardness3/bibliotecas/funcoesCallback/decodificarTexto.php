<?php
namespace hardness;

function decodificarTexto($array) {
	return strip_tags($array['campoAtual']);
}
