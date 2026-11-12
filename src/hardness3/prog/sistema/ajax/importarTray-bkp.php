<?php
namespace hardness;

require_once('bibliotecas/webservices/tray/comunicacaoTray.php');
global $g;

$resposta = array('code' => true, 'data' => array());

$consumerKey= $g['C031']['ConsumerKeyTray'];
$consumerSecret= $g['C031']['ConsumerSecretTray'];

//Seleciona as lojas e faz loop em todas elas
$sql = mysql_query("SELECT * FROM Z001 WHERE IFNULL(Z001_Code,'') != ''");
while($store = mysql_fetch_array($sql)){
	$Tray = new ComunicacaoTray();
	//Dados de integração da Hardness
	// $Tray->setConsumer_key('4e11b543a70cc2cadb69d7157a4d338fde21fdb9f4a2c3a83311a3e3bfe28957');
	// $Tray->setConsumer_secret('a3ecc6b74767611bfb5666c4dc9a0049eb4ed0e0f24f773bf7bb77f942606887');

	$Tray->setConsumer_key($consumerKey);
	$Tray->setConsumer_secret($consumerSecret);

	//Código de autorização do cliente
	$Tray->setCode($store['Z001_Code']);
	$Tray->setStore($store['Z001_Store']);
	$Tray->setDb($store['Z001_Db']);
	$Tray->setExport_url($store['Z001_Export_URL']);

	//Dados de acesso aos dados do cliente
	$Tray->setAccess_token($store['Z001_Access_Token']);
	$Tray->setRefresh_token($store['Z001_Refresh_Token']);
	$Tray->setAccess_token_expiration($store['Z001_Access_Token_Expiration']);
	$Tray->setRefresh_token_expiration($store['Z001_Refresh_Token_Expiration']);

	//Verifica os pedidos
	$resposta['data'] = $Tray->listarPedidos();
	$resposta['code'] = true;

	if($Tray->getUpdate_data()){
		//Atualiza os dados de acesso ao cliente
		$update = "UPDATE Z001 SET ";
		$update .="Z001_Access_Token = '{$Tray->getAccess_token()}', ";
		$update .="Z001_Refresh_Token = '{$Tray->getRefresh_token()}', ";
		$update .="Z001_Access_Token_Expiration = '{$Tray->getAccess_token_expiration()}', ";
		$update .="Z001_Refresh_Token_Expiration = '{$Tray->getRefresh_token_expiration()}' ";
		$update .="WHERE Z001_Store = '{$store['Z001_Store']}'";
		mysql_query($update);
	}
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
