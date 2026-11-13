<?php
$headerHeadTitle or ($headerHeadTitle = '');

// Menus
$home = '<li><a href="/home/" title="Conheça o Sistema">Home</a></li>';
$empresa = '<li' . (($g['r'][0] == 'empresa') ? ' class="active"' : '')  . '><a href="/empresa/" title="Empresa">Empresa</a></li>';
$cadastro = '<li' . (($g['r'][0] == 'conheca') ? ' class="active"' : '')  . '><a href="/conheca/" title="ERP">Soluções</a></li>';
$notafiscal = '<li' . (($g['r'][0] == 'notafiscal' || $g['r'][0] == 'cadastro') ? ' class="active"' : '')  . '><a href="/cadastro/" title="NF-e / NFC-e">Básico</a></li>';
$contato = '<li' . (($g['r'][0] == 'contato') ? ' class="active"' : '')  . '><a href="/contato/" title="Contato">Contato</a></li>';

//login
$valueLogin = substr(base64_decode($_COOKIE['registerCode']), 0, -6);
//senha
$valueSenha = substr(base64_decode($_COOKIE['dataCode']), 6, strlen(base64_decode($_COOKIE['dataCode']))-6);
$opcaoSalvarDados = $_COOKIE['opcaoSalvarDados'];

// HTML
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<!-- <link rel="shortcut icon" href="../../assets/ico/favicon.png"> -->
	<title>{$headerHeadTitle}</title>
	<!-- Bootstrap core CSS -->
	<!--<link href="/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="/static/css/site.css" rel="stylesheet">
	<link href="/static/css/lightbox.css" rel="stylesheet" />
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="/static/js/html5shiv.js"></script>
		<script src="/static/js/respond.min.js"></script>
	<![endif]-->
	<link href="/static/video-js/video-js.css" rel="stylesheet">
</head>


<body onload="verificaTipoConta($('.inputLogin'));">
	<div id="wrapper">
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="container" style="max-width: 1020px; margin: auto; padding: 0; padding-left: 18px;">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/"></a>
				</div>
				<div class="collapse navbar-collapse" style="padding: 0;">
					<form id="form-login" class="navbar-form navbar-right" role="form" onkeypress="if (event.keyCode == 13) { loginNOTA(); return false; }">
						<input type="text" class="tipoConta" name="tipoConta" style="display: none;" value="novo">
						<input type="text" id="opcaoSalvarDados" class="opcaoSalvarDados" name="opcaoSalvarDados" style="display: none;" value="senha">
						<div class="form-group campoLogin">
							<input type="text" name="login" class="form-control inputLogin" placeholder="Login" style="width: 138px;" onkeyup="verificaTipoConta(this);" value="{$valueLogin}">
						</div>
						<div class="form-group campoSenha" style="display: none;">
							<input type="password" name="senha" class="form-control" placeholder="Senha" style="width: 138px; margin-top: -2px;" value="{$valueSenha}">
						</div>
						<div class="btn-group">
							<button id="entrar" class="btn btn-success btnEntrar" type="button" onclick="loginNOTA(); return false;" data-placement="bottom">Entrar</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" style="padding-right: 8px; padding-left: 8px;">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="/esqueci-minha-senha/">Esqueci minha senha</a></li>
								<li onclick="limparLoginSenha(); return false;" style="cursor: pointer;"><a>Limpar login e senha</a></li>
							</ul>
						</div>
					</form>
					<ul class="nav navbar-nav pull-left">
						{$home}
						{$empresa}
						{$cadastro}
						{$contato}
					</ul>
				</div>
			</div>
		</div>
EOT;
