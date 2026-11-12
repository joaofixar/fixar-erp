<?php
namespace hardness;

function editarTabelaCampoTipo($array) {
    $descricao = mysql_real_escape_string($array['todosCampos']['descricao']);
    $query = "ALTER TABLE {$array['todosCampos']['tabela']} MODIFY {$array['todosCampos']['campo']} {$array['campoAtual']} COMMENT '{$descricao}'";
    mysql_query($query);
    $erro = mysql_error();
    if ($erro) {
        jsAlert($erro);
    }
}
