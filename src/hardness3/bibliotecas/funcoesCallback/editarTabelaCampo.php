<?php
namespace hardness;

function editarTabelaCampo($array) {
    global $g;
    $query = "ALTER TABLE {$array['todosCampos']['tabela']} CHANGE {$array['campoAtualAntigo']} {$array['campoAtual']} {$array['todosCampos']['tipo']}";
    mysql_query($query);
    $erro = mysql_error();
    if ($erro) {
        jsAlert($erro);
    }
}
