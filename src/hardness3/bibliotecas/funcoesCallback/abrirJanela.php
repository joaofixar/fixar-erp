<?php
namespace hardness;

function abrirJanela($array, $param = NULL){
	global $g;
	$titulo = isset($param[0]) ? $param[0] : '';
	$url = isset($param[1]) ? $param[1] : '';
	if($param[2] == "campoAtual"){
		$acaoId = $array['campoAtual'];
	} else {
		$acaoId = isset($param[2]) ? $param[2] : '';
		$acaoId = $array['todosCampos'][$acaoId];
	}
	$tamanho = isset($param[3]) ? $param[3] : '';
	$array['campoAtual'] = isset($param[5]) && $param[5] ? $param[5] : $array['campoAtual'];
	
	//Caso seja enviado um array com os extras, separamos e verificamos se utiliza dados do grid ou não
	//Ex: Quando enviado 'var' => 'valor', abrimos a janela com a variavel 'var' e passamos como valor a coluna 'valor' do grid
	// Quando enviado 'var=123', abrimos a janela com a variavel 'var' e passamos como valor '123' e não pegamos dados do grid	
	if(is_array($param[6])){
		$extra = '';
		if(!empty($param[6])){
			foreach ($param[6] as $parametro => $valorParametro) {
				$extra .= "&";
				if(!is_numeric($parametro)){
					$extra .= "{$parametro}=".$array['todosCampos'][$valorParametro];
				} else if(!empty($valorParametro)){
					$extra .= $valorParametro;
				}
			}
		}
	} else {
		$extra = isset($param[6]) ? $param[6] : '';
	}
	if (isset($param[7]) && $param[7] && empty($array['campoAtual'])) {
		$array['campoAtual'] = 'abrir';
	}
	//$titulo = str_replace("'", "\\'", $titulo);
	if (empty($tamanho)) {
		$tamanho = "'auto'";
	} else {
		$tamanho = "[{$tamanho}]";
	}
	//if (preg_match('/^([0-9]|\.)+$/', $array['campoAtual'])) {
	if (isset($param[4]) && $param[4]) {
		$array['campoAtual'] = gCorrigeNumero($array['campoAtual']);
	}
	return "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', '{$titulo}', '{$url}', '&acaoId=' + encodeURIComponent('{$acaoId}') + '{$extra}', {$tamanho}); return false;\">{$array['campoAtual']}</a>";
}
