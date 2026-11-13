<?php
namespace hardness;

function validaCampoRm($array){
    global $g;  
    if($array['todosCampos']['T002_T002_Id_Agrupado'] > 0){
        return '';
    }
    if ($array['todosCampos']['T002_Data_Recebimento']=='0000-00-00' && $array['todosCampos']['T002_T035_Id']<=0 && $array['todosCampos']['T002_Flag_Cancelada']!='S') {
        $T117 = mysql_query("SELECT T117_Id, T002_T117_Id, T002_Flag_Enviar_Banco, T117_Flag_Cancelado, D027_Flag_Tipo FROM T002 LEFT JOIN T117 ON T117_Id=T002_T117_Id LEFT JOIN D027 ON D027_Id=T002_D027_Id WHERE T002_Id='{$array['todosCampos']['T002_Id']}'");
        $mT117 = mysql_fetch_array($T117);
        if($mT117['T002_T117_Id'] > 0 && $mT117['T117_Id'] > 0 && $mT117['T117_Flag_Cancelado'] != "S"){
            $retorno = "<input type='checkbox' name='T002_Flag_Enviar_Banco' title='Remessa Bancária' onChange=\"fin001CheckboxRm(this, '{$array['todosCampos']['T002_Id']}', '{$g['divId']}')\" ";
            if ($array['todosCampos']['T002_Flag_Enviar_Banco']=='S'){
                $retorno .="checked = 'checked'";
            }
            return $retorno .= " />";
        }
    } elseif($array['todosCampos']['T002_T035_Id']>0) {
        return "<a href=\"/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id={$array['todosCampos']['T002_T035_Id']}&ajax=true\">
            <span title=\"Baixar Remessa\">{$array['todosCampos']['T002_T035_Id']}</span>
        </a>";
    } else {
        return " ";
    }
}
