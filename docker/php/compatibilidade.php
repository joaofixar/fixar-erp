<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');

// No PHP 8.1 o mysqli passou a lançar exceção. O legado trata o false da consulta.
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

if (!function_exists('each')) {
    function each(&$array)
    {
        if (!is_array($array)) {
            return false;
        }
        $chave = key($array);
        if ($chave === null) {
            return false;
        }
        $valor = current($array);
        next($array);

        return array(
            1 => $valor,
            'value' => $valor,
            0 => $chave,
            'key' => $chave,
        );
    }
}

if (!function_exists('apc_fetch')) {
    function apc_fetch($chave, &$sucesso = null)
    {
        $sucesso = false;
        return false;
    }
}

if (!function_exists('apc_store')) {
    function apc_store($chave, $valor = null, $ttl = 0)
    {
        return true;
    }
}

if (!function_exists('apc_add')) {
    function apc_add($chave, $valor = null, $ttl = 0)
    {
        return true;
    }
}

if (!function_exists('apc_delete')) {
    function apc_delete($chave)
    {
        return true;
    }
}

if (!function_exists('apc_inc')) {
    function apc_inc($chave, $passo = 1, &$sucesso = null)
    {
        $sucesso = true;
        return $passo;
    }
}

if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc()
    {
        return false;
    }
}

if (!function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime()
    {
        return false;
    }
}

// A base local não abre conexão com a receita, com banco, com e-mail
// nem com o fornecedor do sistema. O registro fica em /tmp/fixar-saidas.log.
function fixar_registrar_saida($destino)
{
    $linha = str_replace(array("\r", "\n"), ' ', (string) $destino);
    @file_put_contents('/tmp/fixar-saidas.log', $linha . "\n", FILE_APPEND);
}

function fixar_fsockopen_recusado($host, $porta = 0, &$errno = null, &$errstr = null, $tempo = 0)
{
    fixar_registrar_saida($host);
    $errno = 0;
    $errstr = 'A base local não abre conexão.';
    return false;
}

class FixarSemSaida
{
    public function stream_open($caminho, $modo, $opcoes, &$aberto)
    {
        fixar_registrar_saida($caminho);
        return false;
    }

    public function stream_stat()
    {
        return false;
    }

    public function url_stat($caminho, $flags)
    {
        fixar_registrar_saida($caminho);
        return false;
    }
}

foreach (array('http', 'https', 'ftp') as $esquema) {
    if (in_array($esquema, stream_get_wrappers(), true)) {
        stream_wrapper_unregister($esquema);
    }
    stream_wrapper_register($esquema, 'FixarSemSaida');
}
