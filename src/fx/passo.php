<?php
namespace hardness;

// O passo do miolo. Quem não escolheu abre em 12. Quem gravou 13 ou 14 continua.
// O padrão da coluna, para a linha nova, é 12.

function fxPasso($gravado): int
{
    $passo = (int) $gravado;
    if ($passo === 13 || $passo === 14) {
        return $passo;
    }
    return 12;
}

function fxPassoDoAcesso($g): int
{
    $linha = array();
    if (isset($g['C007']) && is_array($g['C007'])) {
        $linha = $g['C007'];
    } elseif (isset($g['c007']) && is_array($g['c007'])) {
        $linha = $g['c007'];
    }
    if (!array_key_exists('C007_Tamanho_Fonte_Sistema', $linha)) {
        return fxPasso(null);
    }
    return fxPasso($linha['C007_Tamanho_Fonte_Sistema']);
}

function fxGarantirPadraoDoPasso(): void
{
    if (function_exists('apc_fetch') && apc_fetch('fx-passo-padrao-12') === true) {
        return;
    }
    if (!function_exists('mysql_query')) {
        return;
    }
    $res = mysql_query(
        "SELECT COLUMN_DEFAULT FROM information_schema.COLUMNS"
        . " WHERE TABLE_SCHEMA = DATABASE()"
        . " AND TABLE_NAME = 'C007'"
        . " AND COLUMN_NAME = 'C007_Tamanho_Fonte_Sistema'"
    );
    if (!$res) {
        return;
    }
    $row = mysql_fetch_assoc($res);
    if (!is_array($row)) {
        return;
    }
    $padrao = isset($row['COLUMN_DEFAULT']) ? (string) $row['COLUMN_DEFAULT'] : '';
    $padrao = trim($padrao, "'\"");
    if ($padrao !== '12') {
        $ok = mysql_query(
            'ALTER TABLE `C007` ALTER `C007_Tamanho_Fonte_Sistema` SET DEFAULT 12'
        );
        if (!$ok) {
            return;
        }
    }
    if (function_exists('apc_store')) {
        apc_store('fx-passo-padrao-12', true);
    }
}
