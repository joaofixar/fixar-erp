<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: retornaPrecoVenda()
*/

function retornaPrecoVendaNota($array) {
	global $g;
	
	// Preço e demonstrativo
	require_once('bibliotecas/classes/CAD002.php');
	$CAD002 = new CAD002();
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();
	$sql = "SELECT T007_D024_Id, T007_C004_Id, T007_Flag_Acp, T007_Flag_Destino_Produto FROM T007 WHERE T007_Id = '{$array['todosCampos']['T007_Id']}'";
	$T007 = mysql_query($sql);
	$mT007 = mysql_fetch_assoc($T007);
	$cfop = $VEN001->retornaCFOP($mT007['T007_D024_Id'], $array['todosCampos']['D009_Id'], $mT007['T007_C004_Id'], $mT007['T007_Flag_Destino_Produto'], false, false, false);
	$aliquotas = $VEN001->retornaAliquotas($array['todosCampos']['D009_Id'], false, false, $array['todosCampos']['T007_Id'], false, false, false, $cfop);	
	$preco = $CAD002->retornaPrecoVenda($array['todosCampos']['D009_Id'], '1', $mT007['T007_D024_Id'], false, $cfop, $aliquotas['Aliquota_ICMS'], false, false, 4, $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
	return $preco[0];
}
