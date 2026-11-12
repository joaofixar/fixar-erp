<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) {
	case 'etiquetasGenericaXX000':
		$r_impLetras = strtoupper($r_impLetras);
	
		$sqlModelo = "SELECT * FROM D138 WHERE D138_Id = '10';";
		$resModelo = mysql_query($sqlModelo);
		$rowModelo = mysql_fetch_assoc($resModelo);
		$print = $rowModelo['D138_Codigo'];
		
		$printAll = '';
		for ($i = $r_impInicio; $i <= $r_impFim; $i++) {
			if ($r_addZeros == 1) {
				$val = ($i < 10) ? '0' . $i : $i;
				$val = ($i < 100) ? '0' . $val : $val;
			} else {
				$val = $i;
			}
			$print_ = $print;
			$print_ = str_replace('[[COD]]', $r_impLetras . $val, $print_);
			$print_ = str_replace('[[CODBARRA]]', $r_impLetras . $i, $print_);
			$printAll .= $print_ . "\n";
		}
		
		// send to printer
		$file = '/tmp/etiquetasGenericaXX000'.$confUsuario['dbDatabase'].'.txt';
		file_put_contents($file, $printAll);
		$etiquetasProdutoMetodo = $g['conf']->ini('etiquetasMetodoDeImpressao');
		if ($etiquetasProdutoMetodo[0] == 'windows') {
			$resposta['data'] = 'impressaoArquivo-' . base64_encode($file);
		} else {
			$resposta['code'] = false;
			eval("echo `lpr -P{$etiquetasProdutoMetodo[1]} {$file}`;");
		}
	break;
	
}
	
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");"
?>
