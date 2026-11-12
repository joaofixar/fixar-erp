<?php
namespace hardness;
include('conf.php');

include('inc.conf.php');
// Classes

$resposta = array('code' => true, 'data' => array());
$opcaoAcao = $_REQUEST['opcaoAcao'];
/**
 * Ações
 */
 if($opcaoAcao == 'updateT003_Flag_PDF_Impresso'){
 	$sql = "UPDATE T003 SET T003_Flag_PDF_Impresso='S' WHERE T003_Id='{$T003_Id}'";
	mysql_query($sql);
	$resposta['code'] = true;	 
 }
 
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>