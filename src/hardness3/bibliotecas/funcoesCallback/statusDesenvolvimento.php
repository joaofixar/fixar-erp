<?php
namespace hardness;

function statusDesenvolvimento($array, $flagStatus){
	if($array !== false){
		$flagStatus = $array['campoAtual'];
	}
	switch ($flagStatus) {
		case '0':
			$status = 'Erro/Dúvida/Melhoria';
			break;
		case '1':
			$status = 'Análise';
			break;
		case '2':
			$status = 'Executar';
			break;
		case '3':
			$status = 'Execução';
			break;
		case '4':
			$status = 'Teste';
			break;
		case '5':
			$status = 'Finalizado';
			break;
		default:
			$status = 'Pendente';
			break;
	}
	return $status;
}