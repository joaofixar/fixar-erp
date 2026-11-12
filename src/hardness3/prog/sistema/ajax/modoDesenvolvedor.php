<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

session_start();

if ($_SESSION['modoDesenvolvedorApp']) {
    $resposta['code'] = false;
    $resposta['data'] = 'Não foi possível entrar no modo Desenvolvedor do IDE porque o mode Desenvolvedor App já está ativo.';
} else {
    $_SESSION['modoDesenvolvedor'] = !$_SESSION['modoDesenvolvedor'];
    $resposta['data'] = $_SESSION['modoDesenvolvedor'];
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
