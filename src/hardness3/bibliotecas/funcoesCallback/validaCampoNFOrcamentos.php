<?php
namespace hardness;
// Pedido, D049_Id
function validaCampoNFOrcamentos($array){
	global $g, $r_generateCSV;
	if (!empty($r_generateCSV)) {
		return isset($array['campoAtual']) ? $array['campoAtual'] : '';
	}
	$T005_Id = $array['todosCampos']['T005_Id'];
	$valorNaDanfe = $g['C031']['exibirValorNFDANFE'];
	$retorno = '';
	$sql = mysql_query("SELECT T007_Id, T007_Numero_Nota_Fiscal, T007_Flag_ACP, T007_D024_Id FROM T005 LEFT JOIN T007 ON T007_T005_Id=T005_Id WHERE T005_Id = '{$T005_Id}' and T007_Flag_Cancelada!='S' and T007_Flag_Inutilizado!='S'");
	while($res = mysql_fetch_array($sql)){
		$T007_Id = $res['T007_Id'];
		$Query = md5($res['T007_D024_Id'].'|'.$T007_Id);
		if($res['T007_Flag_ACP'] == "3"){
			$retorno .= "<a href=\"#\" onclick=\"javascript:abre_doc('/ven/ven003/outro/imprimirACP/?ajax=true&T007_Id={$res['T007_Id']}','NF {$res['T007_Id']}');\" style='cursor:pointer'>{$res['T007_Numero_Nota_Fiscal']}, </a>";
		} else if($res['T007_Flag_ACP'] == "4"){
			$retorno .= "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$res['T007_Id']}','CF {$res['T007_Id']}');\" style='cursor:pointer'>{$res['T007_Numero_Nota_Fiscal']}, </a>";
		}else{
			$retorno .= "<a href=\"#\" onclick=\"window.open('/hardness3/hardness/danfe/imprimir_danfe.php?Query={$Query}&vlrNF={$valorNaDanfe}&T007_Id={$T007_Id}','DANFE{$Query}','toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');\" style='cursor:pointer'>{$res['T007_Numero_Nota_Fiscal']}, </a>";
			//$retorno .= "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$res['T007_Id']}','NF {$res['T007_Id']}');\" style='cursor:pointer'>{$res['T007_Numero_Nota_Fiscal']}, </a>";	
		}
	}
    $retorno = substr($retorno, 0, -6);
    return $retorno . '</a>';
}