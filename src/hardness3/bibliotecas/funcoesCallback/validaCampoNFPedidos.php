<?php
namespace hardness;
// Pedido, D049_Id
function validaCampoNFPedidos($array){
	global $r_generateCSV;
	if (!empty($r_generateCSV)) {
		return isset($array['campoAtual']) ? $array['campoAtual'] : '';
	}
    $T005_Id = $array['todosCampos']['T005_Id'];
	$status = $array['todosCampos']['T005_Flag_Status'];
	$retorno = '';
	$cor = '';
	if($status == '5') {
		$cor = "color: gray;";
	} else if($status <= 0){
		$cor = "color: blue;";
	}
	$sql = mysql_query("SELECT T007_Id, T007_Numero_Nota_Fiscal, T007_Flag_ACP FROM T005 LEFT JOIN T007 ON T007_T005_Id=T005_Id WHERE T005_Id = '{$T005_Id}' and T007_Flag_Cancelada!='S'");
	while($res = mysql_fetch_array($sql)){
		if($res['T007_Flag_ACP'] == "3"){
			$retorno .= "<a href=\"#\" onclick=\"javascript:abre_doc('/ven/ven003/outro/imprimirACP/?ajax=true&T007_Id={$res['T007_Id']}','NF {$res['T007_Id']}');\" style='cursor:pointer'>{$res['T007_Numero_Nota_Fiscal']}, </a>";
		}else{
			$retorno .= "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$res['T007_Id']}','NF {$res['T007_Id']}');\" style='cursor:pointer; {$cor}'>{$res['T007_Numero_Nota_Fiscal']}, </a>";	
		}
	}
    $retorno = substr($retorno, 0, -6);
    return $retorno . '</a>';
}