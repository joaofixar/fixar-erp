<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: retornaPrecoVenda()
*/

function retornaPrecoVendaB2B ($array) {
	global $g;
	
	// Preço e demonstrativo
	require_once('bibliotecas/classes/CAD002.php');
	$CAD002 = new CAD002();
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();
	$sql = "SELECT D024_Id, D024_C004_Id, D024_Flag_Lista_Preco, 6 as Flag_ACP, 1 as Flag_Destino_Produto FROM D024 WHERE D024_Id = '{$g['clienteAtual']}'";
	$D024 = mysql_query($sql);
	$mD024 = mysql_fetch_assoc($D024);
	$cfop = $VEN001->retornaCFOP($mD024['D024_Id'], $array['todosCampos']['D009_Id'], $mD024['D024_C004_Id'], $mD024['Flag_Destino_Produto'], false, false, false, $mD024['D024_C004_Id']);
	$aliquotas = $VEN001->retornaAliquotas($array['todosCampos']['D009_Id'], $g['clienteAtual'], false, false, false, false, false, $cfop);
	$preco = $CAD002->retornaPrecoVenda($array['todosCampos']['D009_Id'], $mD024['D024_Flag_Lista_Preco'], $mD024['D024_Id'], false, $cfop, $aliquotas['Aliquota_ICMS'], false, false, 2, $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
	return str_replace(',','.',str_replace('.', '', $preco[0])); 
}
