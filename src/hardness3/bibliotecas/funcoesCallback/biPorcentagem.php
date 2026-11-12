<?php
namespace hardness;

function biPorcentagem($array) {
    if ($array['campoAtual'] != '-') {
        return "{$array['campoAtual']}%";
    }
}
