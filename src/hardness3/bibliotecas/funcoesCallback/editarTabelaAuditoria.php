<?php
namespace hardness;

function editarTabelaAuditoria($array) {
    $tabelasAuditoria = Personalizacoes::tabelasAuditoria();
    $tabelasAuditoria[strtoupper($array['todosCampos']['tabela'])] = $array['campoAtual'];
    Personalizacoes::tabelasAuditoria($tabelasAuditoria);
}
