<?php
namespace hardness;

require_once('prog/etc/etc008/grid_func-js.php');

$count = 0;
$countC = 0;
$countE = 0;

function validaCampoDias($array){
	global $count;
	$prev = $array['todosCampos']['I001_Previsao_Analise_Horas'];
	$count = $count+$prev;
	$dias = round($count/6, 1);
	$diasReal = round($dias/7, 2);
	if ($diasReal >= 1) {
		$diasReal = round($dias + ($diasReal * 2), 1);
	} else {
		$diasReal = $dias;
	}
	return "<span style=\"font-weight: bold;\">{$dias} [{$diasReal}]</span> dias";
}
function validaCampoDiasOs($array){
	global $count;
	$prev = $array['todosCampos']['I002_Previsao_OS_Horas'];
	$count = $count+$prev;
	$dias = round($count/6, 1);
	$diasReal = round($dias/7, 2);
	if ($diasReal >= 1) {
		$diasReal = round($dias + ($diasReal * 2), 1);
	} else {
		$diasReal = $dias;
	}
	return "<span style=\"font-weight: bold;\">{$dias} [{$diasReal}]</span> dias";
}

function validaCampoDiasCH($array){
	global $countC;
	if($array['todosCampos']['I001_Status'] == 'C' || $array['todosCampos']['I001_Status'] == 'E'){
		if ($array['todosCampos']['I001_Status'] == 'E') { 
			$array['todosCampos']['I001_Conclusao_Analise_Data'] = date('Y-m-d');
			$array['todosCampos']['I001_Conclusao_Analise_Hora'] = (date('G') < 9 || date('G') > 18) ? date('09:00:00') : date('H:i:00');
		}
		
		$inicioOsData = explode('-', $array['todosCampos']['I001_Inicio_Analise_Data']);
		$inicioOsHora = explode(':', $array['todosCampos']['I001_Inicio_Analise_Hora']);
		$conclusaoOsData = explode('-', $array['todosCampos']['I001_Conclusao_Analise_Data']);
		$conclusaoOsHora = explode(':', $array['todosCampos']['I001_Conclusao_Analise_Hora']);
		
		$timeInicio = mktime($inicioOsHora[0], $inicioOsHora[1], 0, $inicioOsData[1], $inicioOsData[2], $inicioOsData[0]);
		$timeConclusao = mktime($conclusaoOsHora[0], $conclusaoOsHora[1], 0, $conclusaoOsData[1], $conclusaoOsData[2], $conclusaoOsData[0]);
		$time = ($timeConclusao - $timeInicio)/3600;
		if ($time < 0) { 
			$time = 0;
		} else {
			// Ajustar as horas
			$dateDiff = dateDiff($array['todosCampos']['I001_Inicio_Analise_Data'], $array['todosCampos']['I001_Conclusao_Analise_Data']);
			// Hora iniciar é MANHA e final é TARDE
			if ($inicioOsHora[0] < 13 && $conclusaoOsHora[0] >= 13) {
				// Mesmo dia?
				if ($inicioOsData[2] == $conclusaoOsData[2]) {
					$time -= 1;
				} else {
					$time -= (1 * ($dateDiff + 1));
				}
			}
			// Mais dias
			if ($dateDiff != 0) {
				$time -= $dateDiff * 15;
			}
			if ($array['todosCampos']['I001_Status'] == 'C') { 
				$countC += $time/6;
			}
		}
		if ($time > $array['todosCampos']['I001_Previsao_Analise_Horas']) {
			$style = ' color: var(--BGVermelho);';
		} else {
			$style = ' color: green;';
		}
		if($array['todosCampos']['I001_Status'] == 'C'){
			return '<span style="font-weight: bold;'.$style.'">' . round($time,1) . '</span>';
		} else {
			return '<span style="font-weight: bold;'.$style.'">' . round($time,1) . '</span>';
		}
	}
}
function validaCampoDiasOsCH($array){
	global $countC;
	if($array['todosCampos']['I002_Status'] == 'C' || $array['todosCampos']['I002_Status'] == 'E'){
		if ($array['todosCampos']['I002_Status'] == 'E') { 
			$array['todosCampos']['I002_Conclusao_OS_Data'] = date('Y-m-d');
			$array['todosCampos']['I002_Conclusao_OS_Hora'] = (date('G') < 9 || date('G') > 18) ? date('09:00:00') : date('H:i:00');
		}
		
		$inicioOsData = explode('-', $array['todosCampos']['I002_Inicio_OS_Data']);
		$inicioOsHora = explode(':', $array['todosCampos']['I002_Inicio_OS_Hora']);
		$conclusaoOsData = explode('-', $array['todosCampos']['I002_Conclusao_OS_Data']);
		$conclusaoOsHora = explode(':', $array['todosCampos']['I002_Conclusao_OS_Hora']);
		
		$timeInicio = mktime($inicioOsHora[0], $inicioOsHora[1], 0, $inicioOsData[1], $inicioOsData[2], $inicioOsData[0]);
		$timeConclusao = mktime($conclusaoOsHora[0], $conclusaoOsHora[1], 0, $conclusaoOsData[1], $conclusaoOsData[2], $conclusaoOsData[0]);
		$time = ($timeConclusao - $timeInicio)/3600;
		if ($time < 0) { 
			$time = 0; 
		} else {
			// Ajustar as horas
			$dateDiff = dateDiff($array['todosCampos']['I002_Inicio_OS_Data'], $array['todosCampos']['I002_Conclusao_OS_Data']);
			// Hora iniciar é MANHA e final é TARDE
			if ($inicioOsHora[0] < 13 && $conclusaoOsHora[0] >= 13) {
				// Mesmo dia?
				if ($inicioOsData[2] == $conclusaoOsData[2]) {
					$time -= 1;
				} else {
					$time -= (1 * ($dateDiff + 1));
				}
			}
			// Mais dias
			if ($dateDiff != 0) {
				$time -= $dateDiff * 15;
			}
			if ($array['todosCampos']['I002_Status'] == 'C') { 
				$countC += $time/6;
			}
		}
		if ($time > $array['todosCampos']['I002_Previsao_OS_Horas']) {
			$style = ' color: var(--BGVermelho);';
		} else {
			$style = ' color: green;';
		}
		if($array['todosCampos']['I002_Status'] == 'C'){
			return '<span style="font-weight: bold;'.$style.'">' . round($time,1) . '</span>';
		} else {
			return '<span style="font-weight: bold;'.$style.'">' . round($time,1) . '</span>';
		}
	}
}

function validaCampoDiasCD($array){
	global $countC;
	if($array['todosCampos']['I001_Status'] == 'C'){
		return '<span style="font-weight: bold;">' . round($countC, 1) . '</span> dias';
	}
}
function validaCampoDiasOsCD($array){
	global $countC;
	if($array['todosCampos']['I002_Status'] == 'C'){
		return '<span style="font-weight: bold;">' . round($countC, 1) . '</span> dias';
	}
}

function validaCampoStatusOs($array){
	if($array['todosCampos']['I002_Status'] == 'P'){
		return "Pendente";
	}else if($array['todosCampos']['I002_Status'] == 'C'){
		return "Concluído";
	}else if($array['todosCampos']['I002_Status'] == 'E'){
		return "Execução";
	}
}

function validaCampoStatus($array){
	if($array['todosCampos']['I001_Status'] == 'P'){
		return "Pendente";
	}else if($array['todosCampos']['I001_Status'] == 'C'){
		return "Concluído";
	}else if($array['todosCampos']['I001_Status'] == 'E'){
		return "Execução";
	}else if($array['todosCampos']['I001_Status'] == 'A'){
		return "Pré-Análise";
	}else if($array['todosCampos']['I001_Status'] == 'N'){
		return "Pós-Análise";
	}	
}

function validaCampoTipo($array){
	if($array['todosCampos']['I001_Tipo'] == 'I'){
		return "Implementação";
	}elseif($array['todosCampos']['I001_Tipo'] == 'C'){
		return "Correção";
	}
}

function mostrarNome($array){
	return $array['todosCampos']['PROGRAMADOR.C007_Primeiro_Nome'];
}

function temEmail($array){
	if (!empty($array['todosCampos']['I002_Email_Enviado'])) {
		return "<span class='ui-icon ui-icon-check' title='E-mail já enviado para: " . $array['todosCampos']['I002_Email_Enviado'] . "''> </span>";
	}
	return '&nbsp;';
}

function trimTitulo($array) {
	if (mb_strlen($array['campoAtual']) > 60) {
		return mb_substr($array['campoAtual'], 0, 60) . ' ...';
	}
	return $array['campoAtual'];
}

function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
}

function validaCampoOss($array){
	$sql = "SELECT * FROM I002 WHERE I002_I001_Id='{$array['todosCampos']['I001_Id']}'";
	$result = mysql_query($sql);
	$linhas = mysql_num_rows($result);
	return $linhas;
}

function validaCampoHorasOss($array){
	$sql = "SELECT SUM(I002_Previsao_OS_Horas) FROM I002 WHERE I002_I001_Id='{$array['todosCampos']['I001_Id']}'";
	$result = mysql_query($sql);
	$ret=mysql_fetch_array($result);
	$soma=$ret[0];
	if ($soma > 0)
		return "<span style='color:blue'>".$soma."</span>";
	else
		return "<span style='color: var(--BGVermelho)'>0</span>";
}

function validaCampoStatusGeral($array){
	$sql = "SELECT * FROM I002 WHERE I002_I001_Id='{$array['todosCampos']['I001_Id']}' AND I002_Status != 'C'";
	$result = mysql_query($sql);
	
	$sql = "SELECT * FROM I002 WHERE I002_I001_Id='{$array['todosCampos']['I001_Id']}'";
	$resultA = mysql_query($sql);
	
	if(mysql_num_rows($result) > 0){
		return "Pendentes";
	}else if(mysql_num_rows($resultA)==0){
		return "N/A";
	}else{
		return "Concluídos";
	}
}
