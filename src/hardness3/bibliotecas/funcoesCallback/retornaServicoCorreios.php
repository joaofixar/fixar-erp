<?php
namespace hardness;
function retornaServicoCorreios($array){
	switch ($array['campoAtual']){
		case 'PAC':
			return 'PAC';
			break;
		case 'SEDEX':
			return 'SEDEX';
			break;
		case 'ESEDEX':
			return 'E-SEDEX';
			break;
		case 'SEDEX10':
			return 'SEDEX 10';
			break;
	}
}