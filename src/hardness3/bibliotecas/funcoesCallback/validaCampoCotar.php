<?php
namespace hardness;

function validaCampoCotar($array) {
    global $g;
    $retorno = "<input onclick=\"est009CheckboxCotar(this, '{$array['todosCampos']['D009_Id']}', '{$g['divId']}')\" type='checkbox' id='D009_Flag_Cotar' title='Cotar'";
    if ($array['todosCampos']['D009_Flag_Cotar']=='S'){
        if($array['todosCampos']['D009_C007_Id_Comprar'] != $g['usuarioAtual']){
            $C007 = mysql_query("SELECT C007_Nome FROM C007 WHERE C007_Id='{$array['todosCampos']['D009_C007_Id_Comprar']}'");
            $mC007 = mysql_fetch_array($C007);
            $disabled = "disabled='disabled'";
            $nome = substr($mC007['C007_Nome'], 0,8);
            $C007_Nome = <<<EOT
                <font color='gray' style='font-size:8px;'>{$nome}</font>
EOT;
        }
        $retorno .="checked='checked'";
        return $retorno .= " {$disabled}/><br />{$C007_Nome}";
    } else {
        return $retorno .= " />";
    }
}
