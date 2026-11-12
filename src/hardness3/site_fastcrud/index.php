<?php
/**
* index.php
*/
/**
* Inicialização
*/
require('conf.php');
require("funcoesCriacaoConta.php");
$g = array();

/**
* Tratando a requisição
*/
$g['r_'] = isset($_REQUEST['r']) ? $_REQUEST['r'] : '';
if (!empty($g['r_']) && substr($g['r_'], -1) != '/') { header('HTTP/1.1 301 Moved Permanently'); header('Location: ' . $g['r_'] . '/'); die(); }
// Forçar 'ending slash'
$g['r'] = explode('/', trim($g['r_'], '/'));
$requisicao = 'home';
// Requisições
$requisicao = !empty($g['r'][0]) ? $g['r'][0] : $requisicao;

require 'requisicoes/' . $requisicao . '.php';