<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/CAD019.php');
$CAD019 = new CAD019();
/**
 * Ações
 */
switch($g['r'][3]) {
	/*case 'impTag':
		// uma ou mais
		if (isset($r_extra)) {
			$sql = base64_decode($r_extra);
			$sql = stripslashes($sql);
			$sql = explode("LIMIT", $sql);
			$sql = $sql[0];
			$res = mysql_query($sql);
		} else {
			$D004_Id = isset($_REQUEST['D004_Id']) ? $_REQUEST['D004_Id'] : false;
			$sql = "SELECT D004_Id, D004_Local FROM D004 WHERE D004_Id = '{$D004_Id}';";
			$res = mysql_query($sql);
		}
		
		$etiquetasLocacaoModelos = $g['conf']->ini('etiquetasLocacaoModelos');
		$D138_Id = $etiquetasLocacaoModelos[0];
		if ($D138_Id == 1 || $D138_Id == 2) {
			// D138_Id = 1
			$sqlModelo = "SELECT * FROM D138 WHERE D138_Id = '{$D138_Id}';";
			$resModelo = mysql_query($sqlModelo);
			$rowModelo = mysql_fetch_assoc($resModelo);
		}
		
		$toPrint = '';
		while ($row = mysql_fetch_assoc($res)) {
			// print
			$print = $rowModelo['D138_Codigo'];
			$print = str_replace('[[stx]]', chr(2), $print);
			$print = str_replace('[[D004_Local]]', $row['D004_Local'], $print);
			$print = str_replace('[[D004_Id]]', $row['D004_Id'], $print);
			$toPrint .= $print . "\n";
		}
		
		// send to printer
		$file = '/tmp/cad019ImpTag'.$confUsuario['dbDatabase'].'.txt';
		file_put_contents($file, $toPrint);
		$etiquetasLocacaoMetodo = $g['conf']->ini('etiquetasLocacaoMetodo');
		if ($etiquetasLocacaoMetodo[0] == 'windows') {
			$resposta['data'] = 'impressaoArquivo-' . base64_encode($file);
		} else {
			$resposta['code'] = false;
			eval("echo `lpr -P{$etiquetasLocacaoMetodo[1]} {$file}`;");
		}
	break;
	
	case 'cad019CheckImpLoc':
		$sql = base64_decode($r_extra);
		$sql = stripslashes($sql);
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$result = mysql_query($sql);
		if(mysql_error()){
			$resposta['code'] = false;
			$resposta['data'] = 'Erro: ' . mysql_error();
		} else {
			$resposta['code'] = true;
			$resposta['data'] = 'Serão impressas ' . mysql_numrows($result) . ' etiquetas. Confirmar!?';
		}
	break;*/
    
    case 'cad019CheckboxImprimir':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $D004_Id = isset($r_D004_Id) ? $r_D004_Id : false;
        
        $CAD019->updateCheckboxImprimir($D004_Id, $selected);
    break;
    
    case 'cad019DesmarcarImprimir':
        $sql = "update D004 set D004_Flag_Imprimir_Etiqueta = 'N'";
         mysql_query($sql);    
    break;
    
    case 'cad019VerificaProdutosImprimir':
        $sql = "SELECT D004_Id FROM D004 WHERE D004_Flag_Imprimir_Etiqueta='S'";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)<=0) {
            $resposta['code'] = false;
        }        
    break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
