<?php
namespace hardness;
include('conf.php');
include('inc.conf.php');
$resposta = array('code' => true, 'data' => array());
if(!empty($_REQUEST['D024_D018_Id'])){
	$sqlD020 = mysql_query("SELECT * FROM D020 WHERE D020_D018_Id = '{$_REQUEST['D024_D018_Id']}' ORDER BY D020_Nome_Cidade");
	$selectCidade = "";
	while($D020 = mysql_fetch_array($sqlD020)){						 
		$selectCidade .= "<option value=\"{$D020['D020_Id']}\">{$D020['D020_Nome_Cidade']}</option>";
	}
	$resposta['data'] = $selectCidade;
	$resposta['code'] = true;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>