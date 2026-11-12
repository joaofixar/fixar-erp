<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/SRV001.php');

/**
 * Ações
 */
switch($g['r'][3]) {

}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>