<?php
namespace hardness;
function D001_Flag_Ecommerce($array) {
	switch ($array['campoAtual']) {
		case 'S':
			return 'Sim';
		break;
		case 'N':
			return 'Não';
		break;
		default:
			return '-';
	}
}