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
