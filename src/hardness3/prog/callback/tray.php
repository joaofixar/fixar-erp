<?php

if ($_SERVER['HTTP_HOST'] == "integracao.smartness.com.br") {
	include 'prog/callback/traySMT.php';
	die();
} 

if(!empty($_REQUEST['store'])) {
	$store = $_REQUEST['store'];

	// Precisa colocar no array o número da loja do cliente na Tray e a URL de acesso no sistema
	// Na plataforma, precisa ir em aplicativos e buscar pela Hardness, vai direcionar para este script para fazer a integração da loja
	// A URL precisa ser  http://integracao.hardness.com.br/hardness3/prog/callback/tray.php?...(parametros que vem da própria plataforma)
	$stores = array(
		'391250' => 'http://bug_fabio',
		'106943' => 'http://pelegrin.premiersistemas.com.br',
		//'710069' => 'http://eurosul.hardness.com.br',
		'1296650' => 'http://eurosul.hardness.com.br',
		'732137' => 'http://ceigon.hardness.com.br',
		'815717' => 'http://geralfix.unityerp.online',
		'1231853' => 'http://fixwell.unityerp.online',
		'1375911' => 'http://fixar.chasistemas.com.br',
		'1132927' => 'http://amm.chatechs.com.br',
	);
	if(isset($stores[$store])){
		if(isset($_GET['url'])){
			$url = $_GET['url'];
		}else if(isset($_GET['store_host'])){
			$url = $_GET['store_host'];
		}else{
			die(imprimeMensagem("Necessário passar a url da sua loja como parâmetro. Entre em contato com seu suporte Tray."));
		}

		$configuracaoEmpresa = "/var/www/sites/".str_replace('http://','',$stores[$store]."-confUsuario.php");
		if (file_exists($configuracaoEmpresa)) {
			include($configuracaoEmpresa);
		} 

		$con = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
		$error = mysqli_connect_error($con);
		if(!empty($error)){
			die(imprimeMensagem("Erro ao carregar dados. Por favor entre em contato com o suporte técnico da Hardness Sistemas e informe o seguinte erro:{$error}. (Código 1)"));
		}
		// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die(imprimeMensagem('Erro carregar dados. Por favor entre em contato com o suporte técnico da Hardness Sistemas. (Código 2)'));
		mysqli_set_charset($con,'utf8');
		mysqli_query($con,'SET NAMES utf8;');
		$store_ = mysqli_real_escape_string($con,$store);
		$sql = mysqli_query($con,"SELECT * FROM Z001 WHERE Z001_Store = '{$store_}'");
		$error = mysqli_error($con);
		if(!empty($error)){
			die(imprimeMensagem("Erro ao carregar dados. Por favor entre em contato com o suporte técnico da Hardness Sistemas e informe o seguinte erro:{$error}. (Código 3)"));
		}

		if(mysqli_num_rows($sql) > 0){
			die(imprimeMensagem("Esta loja já está autorizada."));
		} else {
			if(!empty($_REQUEST['code'])) {
				$code = $_REQUEST['code'];
				$api_address = $_REQUEST['api_address'];

				mysqli_query($con,"INSERT INTO Z001 (Z001_Export_URL, Z001_Store, Z001_Code,Z001_API_Address) VALUES ('{$stores[$store]}', '{$store}', '{$code}','{$api_address}')");
				$error = mysqli_error($con);
				if(!empty($error)){
					die(imprimeMensagem('Erro ao gravar código de autorização. Por favor entre em contato com o suporte técnico da Hardness Sistemas. (Código 4)'));
				}
				die(imprimeMensagem('Loja autorizada com sucesso!<br>Code:'.$code.'<br>Api_Address:'.$api_address));
			} else {
				die(imprimeMensagem("Deseja integrar sua loja com a Hardness?<br /><br /><div style=\"text-align: center;\"><button type=\"button\" class=\"btn btn-success\" onclick=\"window.location.href = '{$url}/auth.php?response_type=code&consumer_key=4e11b543a70cc2cadb69d7157a4d338fde21fdb9f4a2c3a83311a3e3bfe28957&callback=http://integracao.hardness.com.br/hardness3/prog/callback/tray.php';\">Integrar</button></div>"));
			}
		}
	} else {
		die(imprimeMensagem("Não foi possível localizar sua loja no banco de dados da Hardness, por favor entre em contato com o suporte técnico da Hardness Sistemas e informe o número de sua loja."));
	}
}

header("location: http://hardness.com.br");

function imprimeMensagem($mensagem) {
	echo <<<EOT
		<html>
		<head>
			<title>Hardness Sistemas - Integração Tray</title>
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		</head>
		<body style="background-color: #f7f7f7;">
		<div style="margin: auto; margin-top: 50px; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px; border-radius: 8px; width: 400px; height: auto; padding: 20px; text-align: justify; font-family: Helvetica; background-color: #d6dee0; color: #000;">
			<div style="text-align: center;"><img src="/hardness3/static/img/logo-hardness-full.png" style='margin-left: -40px;' /></div><br />
			{$mensagem}
		</div>
		</body>
		</html>
EOT;
}
