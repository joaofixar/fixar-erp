<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

require_once('bibliotecas/classes/VEN002.php');
$VEN002 = new VEN002();

$resposta = $VEN002->processarVencidos();

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
