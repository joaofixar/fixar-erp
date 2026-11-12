<?php
namespace hardness;

function biCurvaABC($array) {
    switch ($array['campoAtual']) {
        case 'A':
            $cor = 'green';
            break;
        case 'B':
            $cor = 'blue';
            break;
        case 'C':
            $cor = 'red';
            break;
    }
    return "<span style=\"color: {$cor}; font-weight: bold;\">{$array['campoAtual']}</span>";
}
