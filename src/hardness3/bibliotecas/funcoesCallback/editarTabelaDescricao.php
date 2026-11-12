<?php
namespace hardness;

function editarTabelaDescricao($array) {
    $descricao = mysql_real_escape_string(trim($array['campoAtual']));
    $query = "ALTER TABLE {$array['todosCampos']['tabela']} COMMENT '{$descricao}'";
    var_dump($query);
    mysql_query($query);
    $erro = mysql_error();
    if ($erro) {
        jsAlert($erro);
    }
}
