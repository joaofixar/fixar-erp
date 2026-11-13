<?php
namespace hardness;

require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();
require_once('bibliotecas/classes/VEN001.php');
$VEN001 = new VEN001();
/*
$sql = "SELECT C004_Id FROM C004";
$resC004 = mysql_query($sql);
while ($rowC004 = mysql_fetch_assoc($resC004)) {
	$g['empresaAtual'] = $rowC004['C004_Id'];
	reprocessarEmpresaAtual($g['empresaAtual']);
	echo "Preço Loja para C004_Id: {$g['empresaAtual']} \n";

	$sql = "SELECT D009_Id, D009_Preco_Loja_Real, D009_T001_Id, D005_Flag_ST FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id LEFT JOIN D005 ON D005_Id=D001_D005_Id WHERE D009_C004_Id = '{$g['empresaAtual']}'";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$D006_Id = ($row['D005_Flag_ST'] == 'S') ? $g['C031']['cfopCupomFiscalComST'] : $g['C031']['cfopCupomFiscalSemST'];
		$D024_Id = $g['C031']['codigoClienteCupomFiscal'];

		$aliquotas = $VEN001->retornaAliquotas($row['D009_Id'], false, false, false, false, false, false, $D006_Id, $D024_Id);
		$preco = $CAD002->retornaPrecoVenda($row['D009_Id'], 1, false, false, $D006_Id, $aliquotas['Aliquota_ICMS'], false, false, 4, $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
		$demonstrativo = mysql_real_escape_string(serialize($preco[1]));
		$preco = gCorrigeNumeroInverte($preco[0]);
		if (number_format($preco, 2, '.', '') != $row['D009_Preco_Loja_Real']) {
			//echo number_format($preco, 2, '.', '')  . " != {$row['D009_Preco_Loja_Real']} \n";
			// TRUNCATE = arredondar para baixo
			$sql = "UPDATE D009 SET D009_Preco_Loja_Real = TRUNCATE('{$preco}', 2), D009_Preco_Loja_Real_T001_Id = '{$row['D009_T001_Id']}', D009_Preco_Loja_Real_Demonstrativo = '{$demonstrativo}' WHERE D009_Id = '{$row['D009_Id']}'";
			mysql_query($sql);
		}
		echo '.';
	}
	echo "\n";
}
reprocessarEmpresaAtual('1');
*/

// ESSE SCRIPT ESTA SENDO CALCULADO AGORA NO REPROCESSA_HISTORICO