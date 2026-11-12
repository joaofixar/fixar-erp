<?php
namespace hardness;

function validaCampoMelhorFornecedor($array) {
    global $g;
    $T010_Id = $array['todosCampos']['T059_T010_Id'];
    $T059_Id = $array['todosCampos']['T059_Id'];

    $sql = "SELECT *, (IF(T174_Valor_ST > 0, (T174_Valor_Custo_Unitario + (T174_Valor_Custo_Unitario * (T174_IPI/100)) + (T174_Valor_ST/T174_Quantidade)), (T174_Valor_Custo_Unitario + (T174_Valor_Custo_Unitario * (T174_IPI/100))))) as T174_Valor_Total FROM T174 left join T173 on T173_Id=T174_T173_Id left join D024 on D024_Id=T173_D024_Id WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$T059_Id}' AND (T174_Valor_Total<>'0.00') ORDER BY T174_Valor_Total ASC";
    $result = mysql_query($sql);
    $linha = mysql_fetch_array($result);
    $preco = gCorrigeNumero($linha['T174_Valor_Total']);
    if((empty($preco)) || ($preco == null) || ($preco == '0,00')){
        return "N/A";
    }
    return substr($linha['D024_Nome_Empresa'], 0, 20);
}
