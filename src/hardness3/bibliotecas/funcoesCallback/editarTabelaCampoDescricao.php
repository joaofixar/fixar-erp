<?php
namespace hardness;

function editarTabelaCampoDescricao($array) {
    $descricao = mysql_real_escape_string(trim($array['campoAtual']));
    $query = "ALTER TABLE {$array['todosCampos']['tabela']} MODIFY {$array['todosCampos']['campo']} {$array['todosCampos']['tipo']} COMMENT '{$descricao}'";
    var_dump($query);
    mysql_query($query);
    $erro = mysql_error();
    if ($erro) {
        jsAlert($erro);
    }
}
