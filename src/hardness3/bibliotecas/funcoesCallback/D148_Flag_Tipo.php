<?php
namespace hardness;
function D148_Flag_Tipo($array) {
	switch ($array['campoAtual']) {
		case 'E':
			return 'ENTREGA';
		break;
		case 'C':
			return 'COBRANÇA';
		break;
		default:
			return '-';
	}
}