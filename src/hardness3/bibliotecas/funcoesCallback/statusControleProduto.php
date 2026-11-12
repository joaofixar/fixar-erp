<?php
namespace hardness;
function statusControleProduto($array){
	global $g;
	$status = $array['todosCampos']['T207_Flag_Status'];   
	
	switch ($status) {
		case '1':
			$status = "NÃO ENTREGUE";
			break;
		
		case '2':
			$status = "ENTREGUE";
			break;

		case '3':
			$status = "AGUARDANDO OS";
			break;

		case '4':
			$status = "DEVOLVIDO";
			break;

		case '5':
			$status = "CANCELADO";
			break;
	}

	return $status; 
}
