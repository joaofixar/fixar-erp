<?php

namespace hardness;
/**
* 
* Este script atualiza o histórico de todos os produtos
*/
chdir($home);
$g['r_'] = '';
$_SERVER['REMOTE_ADDR'] = "127.0.0.1";
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";

$sqlEmpresaAtual=mysql_query("select C004_Id from C004");
$g['usuarioAtual'] = 0;

require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();

$count = 0;
while($rowEmpresa=mysql_fetch_array($sqlEmpresaAtual)){
    $g['empresaAtual'] = $rowEmpresa['C004_Id'];
    $g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
    $g['c004'] = $g['C004'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}';"));
	$sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
	while($rowC031 = mysql_fetch_assoc($sqlC031)){
		$g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
		$g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
	}

    $D009 = mysql_query("SELECT D009_Id FROM D009 WHERE D009_C004_Id='{$g['empresaAtual']}'");
    while($mD009 = mysql_fetch_array($D009)){
		$CAD002->D001_reprocessa_historico(null, $mD009['D009_Id']);

		$count++;
		if (($count % 100) == 0) {
			echo "($count) ";
		}
    }
}
$CAD002->reprocessarValidade();

$g['empresaAtual'] = 1;
$g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
$sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
while($rowC031 = mysql_fetch_assoc($sqlC031)){
	$g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
	$g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
}