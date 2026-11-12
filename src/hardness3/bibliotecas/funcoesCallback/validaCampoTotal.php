<?php
namespace hardness;

function validaCampoTotal($array, $T010_Id){
    $sql    = "SELECT * FROM T174 WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$array['todosCampos']['T059_Id']}' AND (T174_Valor_Total<>'0.00' OR T174_Valor_Total IS NOT NULL) ORDER BY T174_Valor_Total ASC;";
    $result = mysql_query($sql);
    $T174   = mysql_fetch_array($result);
    // echo "T174_Valor_Total: ".$linha['T174_Valor_Total']."<br />grid T174_Valor_Total: ".$array['todosCampos']['T174_Valor_Total'];
    // die();

    /*if(($linha['T174_Valor_Total'] == $array['todosCampos']['T174_Valor_Total']) && ($array['todosCampos']['T174_Valor_Total'] != 0)){
        $linha['T174_Valor_Total'] = gCorrigeNumero($linha['T174_Valor_Total']);    
        return "<div style='background:#00FF00;'>".$linha['T174_Valor_Total']."</div>";
    } else {
        $sql = "SELECT T174_Valor_Total FROM T174 WHERE T174_Id='{$array['todosCampos']['T174_Id']}'";
        $result = mysql_query($sql);
        $linhaT174 = mysql_fetch_array($result);
        $linhaT174['T174_Valor_Total'] = gCorrigeNumero($linhaT174['T174_Valor_Total']);
        return $linhaT174['T174_Valor_Total'];
    }*/

    if((empty($T174['T174_Valor_ST']) || $T174['T174_Valor_ST'] == '0.00') && $T174['T174_Valor_Total'] != 0){
        $T174_Valor_Total = ($T174['T174_Valor_Custo_Unitario'] + ($T174['T174_Valor_Custo_Unitario']*($T174['T174_IPI']/100)))*$T174['T174_IPV'];
        return "<div style='background:#00FF00;'>".gCorrigeNumero($T174_Valor_Total)."</div>";
    } else {
        $T174_Valor_Total = ($T174['T174_Valor_Custo_Unitario'] + ($T174['T174_Valor_Custo_Unitario']*($T174['T174_IPI']/100)) + ($T174['T174_Valor_ST']/$T174['T174_Quantidade']))*$T174['T174_IPV'];
        return "<div style='background:#00FF00;'>".gCorrigeNumero($T174_Valor_Total)."</div>";
    }
}
