<?php
namespace hardness;

function statusOS($array, $param=false){
	global $g;
	switch ($array['todosCampos']['T234_Flag_Finalizado']) {
		case 'S':
			$status = "<div style=\"background-color: #A9DEFD; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">Finalizado</div>";
			break;
		case 'N':
			$status = "<div style=\"background-color: #fa8585; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">Pendente</div>";
			break;
		case 'O':
			$status = "<div style=\"background-color: #A9DEFD; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">Concluído</div>";
			break;
		case 'A':
			$status = "<div style=\"background-color: #ffff82; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">Atendimento</div>";
			break;
	}

	switch ($array['todosCampos']['T234_Flag_Tipo_OS']) {
		case 'E':
			return "<div>ERRO</div>{$status}";
		break;
		case 'D':
			return "<div>DÚVIDA</div>{$status}";
		break;
		case 'M':
			return "<div>MELHORIA</div>{$status}";
		break;
		case 'B':
			return "<div>MELHORIA*</div>{$status}";
		break;
		default:
			return "<div>ERRO</div>{$status}";
		break;
	}
}
