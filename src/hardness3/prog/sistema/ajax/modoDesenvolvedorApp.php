<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

session_start();

if ($_SESSION['modoDesenvolvedor']) {
    $resposta['code'] = false;
    $resposta['data'] = 'Não foi possível entrar no modo Desenvolvedor do App porque o mode Desenvolvedor IDE já está ativo.';
} else if ($r_appId) {
    $app = Apps::getApp($r_appId);
    $_SESSION['modoDesenvolvedorApp'] = $app['id'];
    Apps::modoDesenvolvedor($app);
    $resposta['data'] = $_SESSION['modoDesenvolvedorApp'];
} else {
    $_SESSION['modoDesenvolvedorApp'] = false;
    $resposta['data'] = $_SESSION['modoDesenvolvedorApp'];
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
