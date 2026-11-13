<?php
namespace hardness;

function biFormatarValor($array) {
    if ($array['campoAtual'] < 0) {
        return '<span style="color: var(--BGVermelho);">' . number_format($array['campoAtual'], 2, ',', '.') . '</span>';
    } else if ($array['campoAtual'] > 0) {
        return '<span style="color: blue;">' . number_format($array['campoAtual'], 2, ',', '.') . '</span>';
    } else {
        return '<span style="color: gray;">' . number_format($array['campoAtual'], 2, ',', '.') . '</span>';
    }
}
