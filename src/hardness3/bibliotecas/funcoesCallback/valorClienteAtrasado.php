<?php
namespace hardness;

function valorClienteAtrasado($D024_Id, $param){
	global $g;
	
	if(is_array($D024_Id) && isset($param)){
		$D024_Id = $D024_Id['todosCampos'][$param[0]];
	}
	
	$valorVencido = 0;
    $sqlT002 = mysql_query("SELECT T002_Id, T002_Valor_Saldo, T002_Data_Vencimento 
    						  FROM T002 
    						  LEFT JOIN T117 ON T117_Id = T002_T117_Id 
    						  WHERE T002_D024_Id = '{$D024_Id}' 
    						  AND T002_Data_Recebimento = '0000-00-00' 
    						  AND (T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL)");

    while ($T002 = mysql_fetch_array($sqlT002)) {
        if ($T002['T002_Data_Vencimento'] < date("Y-m-d")) {
            $valorVencido += $T002['T002_Valor_Saldo'];
        }
    }

	$valorVencido = gCorrigeNumero($valorVencido);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Títulos a receber vencidos', '/fin/fin001/content/fin001ContentTitulosVencidos/', '&D024_Id=' + encodeURIComponent('{$D024_Id}'), [800,400]); return false;\">{$valorVencido}</a>";
}
