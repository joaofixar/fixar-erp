<?php
declare(strict_types=1);

session_id('codex-debug');
session_start();

$_SESSION['Usuario_Atual'] = 1;
$_SESSION['Empresa_Atual'] = 1;
$_SESSION['Primeiro_Nome_Usuario'] = 'Teste';
$_SESSION['Nome_Empresa_Atual'] = 'Empresa';
$_GET['codigo'] = '180';

ini_set('display_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/movimentacao_estoque.php';
