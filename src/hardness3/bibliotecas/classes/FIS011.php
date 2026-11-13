<?php
namespace hardness;

definirClasse('FIS011');
class FIS011_{
	public function gerarSpedContribuicoes($dataInicial, $dataFinal, $tipoEFD, $numeroRecibo, $situacaoEspecial){
		global $g;
		DEFINE("_SALTO_LINHA",CHR(13).CHR(10));
		$layout = '';
		$mes = date("m",strtotime($dataInicial));
		$ano = date("Y",strtotime($dataFinal));
		$NUM_REC_ANTERIOR = ($tipoEFD == '1') ? $numeroRecibo : '';
		$IND_SIT_ESP = isset($IND_SIT_ESP) ? $situacaoEspecial : '';

		include_once('/bibliotecas/SPED/EFD/bloco_0.php');

		include_once('/bibliotecas/SPED/EFD/bloco_C.php');

		include_once('/bibliotecas/SPED/EFD/bloco_M.php');

		preg_match_all('/((^|\n)\|[^\|]+)/mi', $layout, $array);
		$totais = array();
		foreach ($array[0] as $value) {
			$value = trim($value);
			$value = str_replace('|', '', $value);
			if (isset($totais[$value])) {
				$totais[$value]++;
			} else {
				$totais[$value] = 1;
			}
		}

		foreach ($totais as $key => $value) {
			$layout .= '|9900|'. $key . '|' . $value . '|' . _SALTO_LINHA;
		}
		$layout .= '|9900|9990|1|' . _SALTO_LINHA;
		$layout .= '|9900|9999|1|' . _SALTO_LINHA;
		$layout .= '|9900|9900|' . count($totais) . '|' . _SALTO_LINHA;
		$layout .= '|9990|';
		$layout .= count($totais)+6 . '|' . _SALTO_LINHA;

		$linhas = substr_count($layout, CHR(13).CHR(10));
		$layout .= '|9999|';
		$layout .= $linhas+1 . '|' . _SALTO_LINHA;

		return $layout;
	}

	function formataData($data){
		$data = explode('-', $data);	
		$dia = @$data[2];
		$mes = @$data[1];
		$ano = $data[0];
		return $dia . $mes . $ano;
	}

	function formataDecimal($numero){
		return number_format($numero, 2, ',', '');
	}

	function removeChars($string){
		return preg_replace('/[^a-zA-Z0-9]/','', $string);
	}
}
?>