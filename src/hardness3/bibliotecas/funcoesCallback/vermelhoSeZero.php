<?php
namespace hardness;

function vermelhoSeZero($array){
    if ($array['campoAtual'] == 0) {
        return '<span style="color: red;">' . gCorrigeNumero($array['campoAtual']) . '</span>';
    } else {
        return gCorrigeNumero($array['campoAtual']);
    }
}
