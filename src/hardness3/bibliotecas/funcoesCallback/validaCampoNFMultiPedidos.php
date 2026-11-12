<?php
namespace hardness;
// Pedido, D049_Id
function validaCampoNFMultiPedidos($array){
    $T005_Id = $array['todosCampos']['T005_Id'];
    $T007_Id = $array['todosCampos']['T007_Id'];
	$status = $array['todosCampos']['T005_Flag_Status'];
	$T007_Flag_ACP = $array['todosCampos']['T007_Flag_ACP'];
	$T007_Numero_Nota_Fiscal = $array['todosCampos']['T007_Numero_Nota_Fiscal'];
	$retorno = '';
	$cor = '';
	if($status == '5') {
		$cor = "color: gray;";
	} else if($status <= 0){
		$cor = "color: blue;";
	}
	if($T007_Flag_ACP == "3"){
		$retorno .= "<a href=\"#\" onclick=\"javascript:abre_doc('/ven/ven003/outro/imprimirACP/?ajax=true&T007_Id={$T007_Id}','NF {$T007_Id}');\" style='cursor:pointer'>{$T007_Numero_Nota_Fiscal}</a>";
	}else{
		$retorno .= "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$T007_Id}','NF {$T007_Id}');\" style='cursor:pointer; {$cor}'>{$T007_Numero_Nota_Fiscal}</a>";	
	}
    return $retorno;
}