<?php

if(!empty($_REQUEST['store'])) {

	$store = getStore($_REQUEST['store']);
	$conexaoBD = getConexaoBD($store);
	validarLojaJaCadastrada($conexaoBD, $store);

	if(!empty($_REQUEST['code'])) {
		cadastrarLoja($conexaoBD, $store);
	} else {
		retornarFormConfirmacao($store);
	}
} else {
	header("location: https://smartness.com.br");
}

function processarMensagemRetorno($mensagem, $acionarSuporte) {

	if ($acionarSuporte) {
		$mensagem .= "<BR> Por favor acione o suporte técnico da Smartness Sistemas.";
	}

	return <<<EOT
		<html>
		<head>
			<title>Smartness Sistemas - Integração Tray</title>
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		</head>
		<body style="background-color: #fff;">
		<div style="margin: auto; margin-top: 50px; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px; border-radius: 8px; width: auto; height: auto; padding: 20px; text-align: center; font-family: Helvetica; color: #000;">
			<div style="text-align: center;"><img src="https://ypvpehrwgf.us-east-1.awsapprunner.com/assets/smartness.png" /></div><br />
			{$mensagem}
		</div>
		</body>
		</html>
EOT;
}

function getStore($storeParam) {

	$stores = array(
		// '639010' => array('cod' => '639010', 'url' => 'https://www.graferloja.com.br', 'confUsuario' => 'grafer.smartness.com.br-confUsuario.php'),
		'639010'  => array('cod' => '639010',  'confUsuario' => '/var/www/sites/grafer.smartness.com.br-confUsuario.php'),
		'391250'  => array('cod' => '391250',  'confUsuario' => '/var/www/sites/grafer.smartness.com.br-confUsuario.php'),
		'1147672' => array('cod' => '1147672', 'confUsuario' => '/var/www/sites/milla.smartness.com.br-confUsuario.php'),
		'1396261' => array('cod' => '1396261', 'confUsuario' => '/var/www/sites/hmautomacao.smartness.com.br-confUsuario.php'),
		'SMTTST'  => array('cod' => 'SMTTST',  'confUsuario' => 'confUsuario.php'),
	);

	if (array_key_exists($storeParam, $stores)) {
		$store = $stores[$storeParam];
	}

	if (!isset($store) || empty($store)) {
		die(processarMensagemRetorno("Não foi possível encontrar as configurações para o código de loja recebido! ({$storeParam})", true));
	}

	if (isset($_GET['url'])) {
		$store['url'] = $_GET['url'];
	} else if(isset($_GET['store_host'])) {
		$store['url'] = $_GET['store_host'];
	} else {
		die(processarMensagemRetorno("Não foram encontrados os parâmetros necessários para a operação de configuração com a tray!", true));
	}

	return $store;
}

function getConexaoBD($store) {

	if ($store['cod'] == "SMTTST") {
		$conf = "bibliotecas/confUsuario.php";
	} else {
		$conf = "{$store['confUsuario']}"; 
	}

	include($conf);

	$conexaoBD = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
	$error = mysqli_connect_error($conexaoBD);

	if (!empty($error)) {
		die(processarMensagemRetorno("Falha ao conectar com o banco de dados. {$error}", true));
	}

	mysqli_set_charset($conexaoBD,'utf8');
	mysqli_query($conexaoBD,'SET NAMES utf8;');

	return $conexaoBD;
}

function validarLojaJaCadastrada($conexaoBD, $store) {

	$Z001_Store = mysqli_real_escape_string($conexaoBD, $store['cod']);
	$query = mysqli_query($conexaoBD,"SELECT * FROM Z001 WHERE Z001_Store = '{$Z001_Store}'");
	$error = mysqli_error($conexaoBD);

	if(!empty($error)){
		die(processarMensagemRetorno("Falha ao carregar dados para validar loja.", true));
	}

	if (mysqli_num_rows($query) > 0) {
		die(processarMensagemRetorno("Esta loja já está autorizada.", false));
	}
}

function cadastrarLoja($conexaoBD, $store) {

	$Z001_Export_URL = $store['url'];
	$Z001_Store = $store['cod'];
	$Z001_Code = $_REQUEST['code'];
	$Z001_API_Address = $_REQUEST['api_address'];

	mysqli_query($conexaoBD, "INSERT INTO Z001 (Z001_Export_URL, Z001_Store, Z001_Code, Z001_API_Address) VALUES ('{$Z001_Export_URL}', '{$Z001_Store}', '{$Z001_Code}','{$Z001_API_Address}')");
	$error = mysqli_error($conexaoBD);

	if (!empty($error)) {
		die(processarMensagemRetorno('Erro ao gravar autorização.', true));
	}

	die(processarMensagemRetorno("Loja cadastrada com sucesso!<br>Code: {$Z001_Code} <br>Api_Address: {$Z001_API_Address}", false));

}

function retornarFormConfirmacao($store) {

	$urlAuth     = $store['url'] . "/auth.php";
	$urlCallback = "http://integracao.smartness.com.br/callback/tray";
	$consumerKey = "1c2f6a7db6b8103cc3be7b92d5e057b9ea6940511abd0727506075243ff5ed87";
	$urlBotao    = "{$urlAuth}?response_type=code&consumer_key={$consumerKey}&callback={$urlCallback}";

	$htmlFormConf = <<<EOT
		Deseja integrar sua loja com o ERP Smartness?
		<br /><br />
		<div style="text-align: center">
			<button type="button" class="btn btn-success" onclick="window.location.href='{$urlBotao}';">Integrar</button>
		</div>
EOT;

	die(processarMensagemRetorno($htmlFormConf, false));
}