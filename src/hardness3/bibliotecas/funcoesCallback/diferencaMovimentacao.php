<?php
namespace hardness;

function diferencaMovimentacao($array){
    global $g;  
    $dif = $array['todosCampos']['Diferenca'];
    if ($dif > 0) {
        return '<span style="font-weight: bold; color: green;">' . gCorrigeNumero($dif, 0) . '</span>';
    } else if ($dif < 0) {
        return '<span style="font-weight: bold; color: red;">' . gCorrigeNumero($dif, 0) . '</span>';
    } else {
        return '<span style="font-weight: bold; color: blue;">' . gCorrigeNumero($dif, 0) . '</span>';
    }
}
