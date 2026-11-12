<?php
namespace hardness;
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');

$js = '';
$email=$_REQUEST['emailqrcode'];
if (isset($_REQUEST['login'])) {
	if ($_SERVER['HTTP_HOST'] == 'hardness.hardness.com.br' || $_SERVER['HTTP_HOST'] == 'cha.chasistemas.com.br' || $_SERVER['HTTP_HOST'] == 'premier.premiersistemas.com.br') {
		$usarSenhaRootPadrao = true;
	} else {
		$usarSenhaRootPadrao = true;
	}
	// Login!
	if(!isset($_SESSION['bancoDados'])){
		@session_destroy();
	}
	session_start();
	$e='1';
	$l=$_REQUEST['login'];
	$s=$_REQUEST['senha'];
	$rodape = 'Hardness Sistemas ' . date('Y');
	$D013_Id=$_REQUEST['D013_Id'];
	$D024_Id=$_REQUEST['D024_Id'];
	$buscaCliente=$_REQUEST['buscaCliente'];
	$Banco_Dados = $w = $sb = $us = $uc = $Empresa_Usuaria = $PaginaQueIniciou = $nWidth_Padrao = $nHeight_Padrao = '';
	$user = explode('/', $confUsuario['pathRaiz']);
	$user = $user[2];
	$tipoUsuarioLogin = "";  

	if (empty($l)) {
	   $js .= "alert('Digite seu e-mail.');";
	   $js .= "location.replace('{$confUsuario['urlRaiz']}b2b');";
	} else if (empty($s)) {
		$js .= "alert('Digite a senha.');";
		$js .= "location.replace('{$confUsuario['urlRaiz']}b2b');";
	} else if (empty($e)) {
		$js .= "alert('Selecione a empresa.');";
		$js .= "location.replace('{$confUsuario['urlRaiz']}b2b');";
	} else {
		$sqlEmpresaUsuario = mysqli_query("SELECT D179_Login,D179_Senha,
												ifnull(D179_C007_Id,0) as D179_C007_Id,
												ifnull(D179_C007_Vendedor_Externo_Id,0) as D179_C007_Vendedor_Externo_Id,
												ifnull(D179_D021_Id,0) as D179_D021_Id,
												ifnull(D013_Id,0) as D013_Id
											FROM D179
									   LEFT JOIN D013 ON D179_Login=D013_E_Mail
										   WHERE D179_Login='{$l}' LIMIT 1");
		$D179 = mysql_fetch_assoc($sqlEmpresaUsuario);
		
		if ((mysql_num_rows($sqlEmpresaUsuario)==0) || ($s!=$D179['D179_Senha'])) {
			$js .= "alert('E-Mail ou Senha inválida.');";
			$js .= "location.replace('{$confUsuario['urlRaiz']}b2b');";
			$redirectJS = <<<EOT
				<!DOCTYPE html>
				<html>
					<head>
						<meta charset="UTF-8">
					</head>
					<body>
					<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
					<script>
						$(document).ready(function() {
							{$js}
							$('input:first').focus();
						});
					</script>
					</body>
				</html>
EOT;
				die($redirectJS);
		}else{
			$query = "";
			$queryD013 = "";		

			//Login vinculado ao contato do cliente, trazer todos clientes que tenham o email de login vinculado ao contato
			if($D179['D013_Id']>0){
				$query = " D013_E_Mail='{$l}'";

				if(isset($D013_Id)){
					$queryD013 = "AND D013_Id='{$D013_Id}'";
				}
				$tipoUsuarioLogin = "C";
			}

			//Login vinculado com vendedor interno, trazer todos os clientes desse vendedor
			if($D179['D179_C007_Id']>0){
				$query = " D024_C007_Vendedor_Interno=".$D179['D179_C007_Id'];
				$tipoUsuarioLogin = "VI";
			}

			//Login vinculado com vendedor externo, trazer todos os clientes desse vendedor
			if($D179['D179_C007_Vendedor_Externo_Id']>0){
				$query = " D024_C007_Vendedor_Externo=".$D179['D179_C007_Vendedor_Externo_Id'];
				$tipoUsuarioLogin = "VE";
			}

			//Login vinculado com representante, trazer todos os clientes desse representante
			if($D179['D179_D021_Id']>0){
				$query = " D024_D021_Id=".$D179['D179_D021_Id'];
				$tipoUsuarioLogin = "R";
			}

			//Verifica se foi recebido o parametro GET com o nome da empresa na busca
			if(isset($buscaCliente)){
				$query .= " AND (D024_Nome_Empresa LIKE '%{$buscaCliente}%' OR D024_Nome_Fantasia LIKE '%{$buscaCliente}%' OR D024_Cnpj LIKE '%{$buscaCliente}%')";
			}

			if(isset($D024_Id)){
				$query = " D024_Id=".$D024_Id;
			}

			$sqlD024=mysqli_query("SELECT D024_Id,D024_C004_Id, D013_E_Mail, D013_Id, D024_Nome_Empresa, D024_Cnpj, D024_D021_Id, D024_C007_Vendedor_Interno,D024_C007_Vendedor_Externo,D018_UF
									FROM D024
	                           LEFT JOIN D013 ON D024_Id=D013_D024_Id {$queryD013}
							   LEFT JOIN D018 ON D024_D018_Id=D018_Id
								   WHERE {$query} AND D024_Flag_Ativo='S' AND D024_Flag_Pre_Cadastro!='S' GROUP BY D024_Id ORDER BY D024_Nome_Empresa");

			$numD024 = mysql_num_rows($sqlD024);
		
			if(($numD024<=0) && !(isset($buscaCliente))){
				$js .= "alert('Esse login não está vinculado a nenhum cliente ou usuário, verifique as configurações.');";
				$js .= "location.replace('{$confUsuario['urlRaiz']}b2b');";
				$redirectJS = <<<EOT
					<!DOCTYPE html>
					<html>
						<head>
							<meta charset="UTF-8">
						</head>
						<body>
						<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
						<script>
							$(document).ready(function() {
								{$js}
								$('input:first').focus();
							});
						</script>
						</body>
					</html>
EOT;
				die($redirectJS);
			}else{
				if(($numD024>1) || (isset($buscaCliente))){
					if($confUsuario['dbDatabase']=='e212bkp'){
						$sql = "SELECT C004_Nome_Abreviado,C004_Logo_Marca_B2B,C004_Imagem_Celular_B2B FROM C004 WHERE C004_Id=4";
					}else{
						$sql = "SELECT C004_Nome_Abreviado,C004_Logo_Marca_B2B,C004_Imagem_Celular_B2B FROM C004 ORDER BY C004_Id ASC LIMIT 1";
					}
						$res = mysqli_query($sql);
						$row = mysql_fetch_assoc($res);
						$logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/".$row['C004_Logo_Marca_B2B'];
						$row['C004_Nome_Abreviado'] = substr($row['C004_Nome_Abreviado'], 0 , 30);
						echo <<<EOT
							<!DOCTYPE html>
								<html>
								<head>
									<meta name="viewport" content="width=device-width" />
									<meta charset="UTF-8">
									<title></title>
									<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
									<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
									<script type="text/javascript" src="./bootstrap/js/bootstrap.js"></script>
									<link rel="stylesheet" href="/hardness3/smarty/B2B/css/responsive.css">
									<link rel="stylesheet" href="/hardness3/smarty/B2B/css/jquery-ui.css">
									<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
									<link href="./smarty/B2B/style.css" rel="stylesheet" type="text/css">
									<link href="/hardness3/smarty/B2B/css/v2/sb-admin-2.css" rel="stylesheet">
		
									<style>
										html, body {
											height: 100%;
										}
										body {
											#000;
										}
										.caixinha {
											max-width: 700px;
											padding: 19px 29px 29px;
											margin: 0 auto 20px;
											border: 1px solid #e5e5e5;
											-webkit-border-radius: 5px;
											-moz-border-radius: 5px;
											border-radius: 5px;
											-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
											-moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
											box-shadow: 0 1px 2px rgba(0,0,0,.05);
											background-color: #F4F4F4;
										}
										.container {
											margin: 0 auto;
											max-width: 700px;
											text-align: center;
											padding: 30px 0 20px 0;
										}
										#buscaCliente{
											width:86%;
											height:48px;
											float:left;
											margin-right:5px;
											font-size:16px;
										}
										.btn-buscar{
											font-size: 1rem;
											border-radius: 2px !important;
											padding: 0.75rem 1rem;
											display: block;
											width: 100%;
											color: #fff;
											background-color: #4e73df !important;
											border-color: #4e73df !important;
										  }
									</style>
									<script>
										$(document).ready(function() {
											
										});
									</script>
								</head>
								<body class="login-bg">
									<div style="text-align: center;">
										<img src="{$logo}" style="margin: 50px 10px 25px 0;" />
									</div>
									<div class="container-empresa">
										<div class="content-search">
										<form id="form-login" action="./index.php" method="post">
											<input type="text" name='buscaCliente' id='buscaCliente' placeholder="Pesquisar" value=''>
											<input type="hidden" name="login" id='login' value='{$l}'>
											<input type="hidden" name="senha" id='senha' value='{$s}'>
											<button type="submit" id="botaoBuscarCliente" style="width:90px;" class="btn-primary-p btn-buscar">Buscar</button>
										</form>
										</div>
EOT;
									if($numD024>0){
										echo <<<EOT
											<div class="card-body-empresa">
												<p class="mt-4">Escolha a empresa que deseja se logar:</p>
EOT;
										while ($empresas = mysql_fetch_array($sqlD024)){
											echo <<<EOT
												<div class="border-s" onclick='javascript:location.replace("./index.php?login={$l}&senha={$s}&D013_Id={$empresas['D013_Id']}&D024_Id={$empresas['D024_Id']}")'>
													<div class="font-weight-bold txt-align-l">
														{$empresas['D024_Nome_Empresa']}
													</div>
													<div class="d-flex hover-gray card-header py-3">
														<span>
															{$empresas['D024_Cnpj']}
														</span>
														<span class="color-st">
															<b>&nbsp;&nbsp;{$empresas['D018_UF']}</b>
														</span>
													</div>
												</div>
EOT;
										}
									}else{
										echo <<<EOT
											<div class="border-s">
												<div class="font-weight-bold txt-align-l">
													Nenhuma empresa encontrada com esse nome.
												</div>
											</div>
EOT;
									}
									echo <<<EOT
										<br>
										<a class="btn-primary-p btn-login" href="/hardness3/outros/b2b/">
											Voltar
										</a>
										<div class="footer">
											<p style="text-align: center; color: black; font-size:12px;">&copy; {$rodape}</p>
										</div>
									</div>
								</div> <!-- /container -->
							</body>
							</html>
EOT;
					die();
				} else {
					$rowEmpresaUsuario = mysql_fetch_array($sqlD024);
				}
			}
		}

		//Validações
		$Cnpj = $rowEmpresaUsuario['D024_Cnpj'];
		$Nome_Empresa = $rowEmpresaUsuario['D024_Nome_Empresa'];
		$erros = "";

		//Caso o contato do cliente esteja em branco a variavel de sessao ClienteAtualContato(D013) vai ser 99999
		if ($rowEmpresaUsuario['D013_Id']<=0) {
			$rowEmpresaUsuario['D013_Id']="99999";
		}

		//Login de representante/vendedor a variavel de sessao ClienteAtualContato(D013) vai ser 99999
		if($tipoUsuarioLogin!="C"){
			$rowEmpresaUsuario['D013_Id']="99999";
		}
		
		//Obrigatorio ter C004 vinculado ao cliente
		if ($rowEmpresaUsuario['D024_C004_Id']<=0){
			$erros .= "\nEmpresa não definida no seu cadastro de cliente.";
		}else{
			$e = $rowEmpresaUsuario['D024_C004_Id'];
		}

		$C007_Id=0;
		$D021_Id="";
		$C007_Vendedor_Interno = 0;
		$C007_Vendedor_Externo = 0;
		//Obrigatorio ter um C007 vinculado ao D024 ou ao D179(login B2B) para identificar vendedor
		//Se usuario for Representante/Vendedor interno/Vendedor externo C007 vai ser preenchido com valor vinculado ao D179_C007_Id ou D179_C007_Vendedor_Externo_Id
		if($D179['D179_C007_Id']>0){
			$C007_Id=$D179['D179_C007_Id'];
			$C007_Vendedor_Interno = $D179['D179_C007_Id'];
		}

		if($D179['D179_C007_Vendedor_Externo_Id']>0){
			$C007_Id=$D179['D179_C007_Vendedor_Externo_Id'];
			$C007_Vendedor_Externo = $D179['D179_C007_Vendedor_Externo_Id'];
			$D021_Id = $D179['D021_Id'];
		}

		if($D179['D179_D021_Id']>0){
			$D021_Id=$D179['D179_D021_Id'];
		}

		//Se usuario for Cliente C007 vai ser preenchido com valor vinculado D024_C007_Vendedor_Interno
		if($tipoUsuarioLogin=="C"){
			$C007_Id=$rowEmpresaUsuario['D024_C007_Vendedor_Interno'];
			$C007_Vendedor_Interno = $rowEmpresaUsuario['D024_C007_Vendedor_Interno'];
		}

		if($C007_Id==0){
			$erros .= "\nVendedor interno não definido no cadastro do cliente.";
		}
	
		if (!empty($erros)) {
			$mensagem = "Estão faltando informações em seu cadastro, por favor contate seu vendedor e informe a seguinte mensagem:\n{$erros}\n\n {$Nome_Empresa} \n{$Cnpj}";
			$redirectJS = <<<EOT
				<!DOCTYPE html>
				<html>
					<head>
						<meta charset="UTF-8">
					</head>
					<body>
					<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
					<script>
						$(document).ready(function() {
							alert($('#mensagem').text());
							location.replace('{$confUsuario['urlRaiz']}b2b');
							$('input:first').focus();
						});
					</script>
					<div id="mensagem" style="display:none;">{$mensagem}</div>
					</body>
				</html>
EOT;
			die($redirectJS);	
		}

		//Validando configurações da empresa vinculada ao cliente
		$cC004=mysqli_query("SELECT C004_Id,C004_Banco_Configuracao,C004_Nome_Empresa,C004_Caminho_Sistema FROM C004 WHERE C004_Id='{$e}'");
		$mC004=mysql_fetch_array($cC004);

		if (empty($mC004['C004_Banco_Configuracao'])) {
			$js .= "alert('Banco de configuração nao definido.');";
			$js .= "location.replace('{$confUsuario['urlRaiz']}b2b');";
		} else if (empty($mC004['C004_Caminho_Sistema'])) {
			$js .= "alert('Pasta da empresa nao definida.');";
			$js .= "location.replace('{$confUsuario['urlRaiz']}b2b');";
		}else {
			// Consulta vendedor C007 que sera logado
			$Sql=mysqli_query("SELECT C007_Id,
									 C007_Nome,
									 C007_Primeiro_Nome,
									 C007_Flag_Vendedor,
									 C007_Cargo,
									 C007_Usuario_Suporte
							    FROM C007 
							   WHERE C007_Id='{$C007_Id}'");
							$mSenha=mysql_fetch_array($Sql);

			$cBd_Configuracao=$mC004['C004_Banco_Configuracao'];

			$cC020=mysqli_query("SELECT C020_Modelo_Sistema_Nfe FROM C020 WHERE C020_C004_Id='{$mC004['C004_Id']}'");
			$mC020=mysql_fetch_array($cC020);

			// Habilita mensagens de liberação de crédito
			setcookie('liberacaoCreditoFlag', 2, 0, '/');

			// Atribui a Variavel de Sessao o Nome e o Codigo do Usuario
			$_SESSION['Modulo_NFe']='S';
			$_SESSION['Usuario_Atual']=$mSenha['C007_Id'];
			$_SESSION['Nome_Usuario']=$mSenha['C007_Nome'];
			$_SESSION['Primeiro_Nome_Usuario']=$mSenha['C007_Primeiro_Nome'];
			$_SESSION['Alcada_Usuario']=$mSenha['C007_Flag_Vendedor'];
			$_SESSION['Cargo_Usuario']=$mSenha['C007_Cargo'];
			$_SESSION['Perfil_Usuario']=$mSenha['C021_Id'];
			$_SESSION['Empresa_Atual']="$e";
			$_SESSION['Empresa_Logada']="$e";
			$_SESSION['Nome_Empresa_Atual']=$mC004['C004_Nome_Empresa'];
			$_SESSION['Banco_Dados']=$confUsuario['dbDatabase'];
			$_SESSION['sb']="$sb";
			$_SESSION['nWidth_Padrao']="$nWidth_Padrao";
			$_SESSION['nHeight_Padrao']="$nHeight_Padrao";
			$_SESSION['Pasta_Empresa']=$mC004['C004_Caminho_Sistema']; //-- pasta onde estao localizados os dados da empresa
			$_SESSION['cBd_Configuracao']=$cBd_Configuracao;
			$_SESSION['Usuario_Suporte']=$mSenha['C007_Usuario_Suporte'];
			$_SESSION['User_Suport']="$us";
			$_SESSION['Usuario_Contador']="$uc";
			$_SESSION['Empresa_Usuaria']="$Empresa_Usuaria";
			$_SESSION['Data_Hora_Login']=time();
			$_SESSION['Cliente_Atual']=$rowEmpresaUsuario['D024_Id'];
			$_SESSION['Cliente_Atual_Contato']=$rowEmpresaUsuario['D013_Id'];
			$_SESSION['Versao_B2B']='2';
			$_SESSION['Cliente_Atual_Vendedor']=$C007_Vendedor_Interno;
			$_SESSION['Cliente_Atual_Vendedor_Externo']=$C007_Vendedor_Externo;
			$_SESSION['Cliente_Atual_Representante']=$D021_Id;
			$_SESSION['Tipo_Login_Usuario_B2B']=$tipoUsuarioLogin;

			// -> Registra um historico de Enderecos
			$_SESSION['URL_Historico']="";

			// -> Registra um historico de Tipos de Telas
			$_SESSION['URL_Tipo_Historico']="";

			// -> Registra um historico de nomes l�gicos das p�ginas acessadas
			$_SESSION['URL_Descricao_Historico']="";

			// -> Registra um historico com as Entidades Principais
			$_SESSION['URL_Entidade_Principal']="";

			// Guarda o numero pagina acessada no monta_consultas_fme
			// Utiliza como chave da matriz a Entidade+cTela+Entidade_Principal+Id_Entidade_Principal
			$_SESSION['Pagina_Monta_Consulta']="";
			$_SESSION['Chave_Pagina_Monta_Consulta']="";

			$_SESSION['PaginaQueIniciou']=$PaginaQueIniciou;

			if (isset($confUsuario['urlRaiz'])) {
				$js .= "location.replace('{$confUsuario['urlRaiz']}erp/');";
			} else {
				$js .= "location.replace('http://{$user}/erp/');";
			}	   
		}

		$redirectJS = <<<EOT
			<!DOCTYPE html>
			<html>
				<head>
					<meta charset="UTF-8">
				</head>
				<body>
				<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
				<script>
					$(document).ready(function() {
						{$js}
						$('input:first').focus();
					});
				</script>
				</body>
			</html>
EOT;
			die($redirectJS);	
	}
	$redirectJS = <<<EOT
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="UTF-8">
			</head>
			<body>
			<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
			<script>
				$(document).ready(function() {
					{$js}
					$('input:first').focus();
				});
			</script>
			</body>
		</html>
EOT;
	die($redirectJS);
}

if (isset($_REQUEST['email'])) {
	
	$email = $_REQUEST['email'];
	$user = explode('/', $confUsuario['pathRaiz']);
	
	if (empty($email)) {
	   	$js .= "alert('Digite seu e-mail.');";
	  	$js .= "location.replace('/hardness3/outros/b2b/?ajax=true&enviarSenha=true');";
		$redirectJS = <<<EOT
				<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
				<script>
				  	$(document).ready(function() {
				  		{$js}
				  		$('input:first').focus();
				  	});
				</script>
EOT;
		die($redirectJS);
	} else {
		$sqlEmailUsuario = mysqli_query("SELECT D024_C004_Id,
											   D024_Nome_Empresa,
											   D013_E_Mail, 
											   D013_Id,
											   D013_Senha,
											   D013_Nome_Contato,
											   C007_Email,
											   C007_Primeiro_Nome,
											   C007_Email_SMTP_Servidor,
											   C007_Email_SMTP_Autenticacao,
											   C007_Email_SMTP_Usuario,
											   C007_Email_SMTP_Senha,
											   D179_Login,
											   D179_Senha
											FROM D024
		                          	   LEFT JOIN D013 ON D013_D024_Id=D024_Id
		                          	   LEFT JOIN D179 ON D179_Login=D013_E_Mail
									   LEFT JOIN C007 ON C007_Id = D024_C007_Vendedor_Interno	 
		                          		   WHERE D179_Login='{$email}' limit 1");
		
		$rowEmailUsuario = mysql_fetch_array($sqlEmailUsuario);
		
		$erro = mysql_error();
		if(!empty($erro)){
			die($erro);
		}

		if ($rowEmailUsuario['D013_Id']<=0) {
			$js = "alert('E-Mail ou senha incorretos.');";
			$js .= "location.replace('/hardness3/outros/b2b/?ajax=true&enviarSenha=true');";
			$redirectJS = <<<EOT
				<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
				<script>
				  	$(document).ready(function() {
				  		{$js}
				  		$('input:first').focus();
				  	});
				</script>
EOT;
			die($redirectJS);
		}

		if($confUsuario['dbDatabase']=='e212bkp'){
			$sql = "SELECT C004_Nome_Abreviado,C004_Logo_Marca_B2B,C004_Imagem_Celular_B2B FROM C004 WHERE C004_Id=4";
		}else{
			$sql = "SELECT C004_Nome_Abreviado,C004_Logo_Marca_B2B,C004_Imagem_Celular_B2B FROM C004 ORDER BY C004_Id ASC LIMIT 1";
		}

		$res = mysqli_query($sql);
		$row = mysql_fetch_assoc($res);

		$backgroundColor = $sys['loginBackground'];
		$rodape = 'Hardness Sistemas ' . date('Y');
		$logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/".$row['C004_Logo_Marca_B2B'];
		$row['C004_Nome_Abreviado'] = substr($row['C004_Nome_Abreviado'], 0 , 30);
		
		$corpoEmail = <<<EOT
			<!DOCTYPE html>
				<head>
					<meta charset="UTF-8">
					<title></title>			
					
					<style>								
						.container{						
							width: 600px;
							text-align: center;
							padding:20px 40px;				 
							height:300px;
							align-content: center;
							background-color:#696969;
						}
						p, h2 {
							font-family:arial;
							color:white;
						}
					</style>
				</head>
				<body>
					<div class="container">
						<div style="text-align:left;margin-left:50px;margin-top:20px">
							<img src="{$logo}" style="width: 150px; height: 50px;" />
						</div>
						<div style="width:600px; margin-left:50px;text-align:left;">
							<h2>REENVIO DE SENHA</h2>
							<p>Conforme sua solicitação, segue abaixo suas informações de acesso:</p>
							<p>
							EMPRESA: {$row['C004_Nome_Abreviado']}<br/>
							CONTATO: {$rowEmailUsuario['D013_Nome_Contato']}<br/>
							LOGIN: {$rowEmailUsuario['D179_Login']}<br/>
							SENHA: {$rowEmailUsuario['D179_Senha']}</p>
							<p>Esse e-mail foi enviado por um sistema automático e não poderá ser respondido.</p>	
							<br/><br/>			
						</div>			
					</div>
				</body>
			<html>	
EOT;
     	require_once("../../bibliotecas/phpmailer/class.phpmailer.php");
	 	$mail 			= new \phpmailer;
        // Mail
        $mail->From     = $rowEmailUsuario['C007_Email'];
        $mail->FromName = $rowEmailUsuario['C007_Primeiro_Nome'];
        $mail->Host     = $rowEmailUsuario['C007_Email_SMTP_Servidor'];
        $mail->Mailer   = "smtp";
        $mail->SMTPAuth = $rowEmailUsuario['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $rowEmailUsuario['C007_Email_SMTP_Usuario'];
        $mail->Password = $rowEmailUsuario['C007_Email_SMTP_Senha'];
        $mail->Subject  = "Reenvio de Senha {$row['C004_Nome_Abreviado']}";
        $mail->Body     =  $corpoEmail;
        $mail->AltBody  = "ALT BODY";
        $mail->CharSet  = 'utf-8';
        $mail->SetLanguage("br");
        $mail->IsHTML(true);
        if ($rowEmailUsuario['C007_Email_SMTP_Servidor'] == "smtp.gmail.com") {
            $mail->SMTPSecure = "ssl";
            $mail->Port       = "465";
        }
        if ($rowEmailUsuario['C007_Email_SMTP_Servidor'] == "smtp.live.com") {
            $mail->SMTPSecure = "ssl";
        }
        if (!empty($rowEmailUsuario['C007_Email_SMTP_Porta'])) {
            $mail->Port = $rowEmailUsuario['C007_Email_SMTP_Porta'];
        }
       
        $monta_['Para'] = $rowEmailUsuario['D013_E_Mail'];
        $monta_['cc']   = '';
        
        $todosEmailsEnviados = array();
        $mPara               = preg_split("/[;,]+/", $monta_['Para']);
        for ($x = 0; $x < count($mPara); $x++) {
            $email = $mPara[$x];
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $todosEmailsEnviados[] = strtolower(trim($email));
            $mail->AddAddress($email);
        }
        if (isset($monta_['cc'])) {
            $mPara = preg_split("/[;,]+/", $monta_['cc']);
            for ($x = 0; $x < count($mPara); $x++) {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddCC($email);
            }
        }
        
        $anexosGravar   = array();
        $T139_Estrutura = array(
            'anexosManual' => array()
        );
        if (isset($monta_['Anexos'])) {
            foreach ($monta_['Anexos'] as $path) {
                $anexosGravar[]                   = $path;
                $T139_Estrutura['anexosManual'][] = basename($path);
                $mail->AddAttachment($path, basename($path));
            }
        }
        
        if ($retornoMail = $mail->Send()) {
           	echo <<<EOT
			<script>
			alert("Email enviado com sucesso");
			location.replace('/hardness3/outros/b2b/?ajax=true');
			</script>
EOT;
		}else{
			$cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
			$cErro=str_replace(array("<p>","</p>"),"<br /><br />",$cErro);
			echo <<<EOT
			<script>
			alert("Email não enviado {$cErro}");
			location.replace('/hardness3/outros/b2b/?ajax=true&enviarSenha=true');
			</script>
EOT;
		}
        die();
	}
}		
// Caso for a sigma irá logar com a empresa 4 como padrão. Analisar ua maneira disso ser configurável por empresa.
		if($confUsuario['dbDatabase']=='e212bkp'){
			$sql = "SELECT C004_Nome_Abreviado,C004_Logo_Marca_B2B,C004_Imagem_Celular_B2B FROM C004 WHERE C004_Id=4";
		}else{
			$sql = "SELECT C004_Nome_Abreviado,C004_Logo_Marca_B2B,C004_Imagem_Celular_B2B FROM C004 ORDER BY C004_Id ASC LIMIT 1";
		}

		$res = mysqli_query($sql);
		$row = mysql_fetch_assoc($res);
		$email=$_REQUEST['emailqrcode'];
		if(!empty($email)){
			$opacity="opacity:1;";
		}else{
			$opacity="opacity:0.06;";
		}

		$backgroundColor = $sys['loginBackground'];
		$rodape = 'Hardness Sistemas ' . date('Y');
		$logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/".$row['C004_Logo_Marca_B2B'];
		$path = $confUsuario['pathRaiz']."dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/".$row['C004_Imagem_Celular_B2B'];
		if(file_exists($path)){
			$imgPhone = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/".$row['C004_Imagem_Celular_B2B'];
		}else{
			$imgPhone = "/hardness3/smarty/B2B/images/celular.png";
		}
		$row['C004_Nome_Abreviado'] = substr($row['C004_Nome_Abreviado'], 0 , 30);
		echo <<<EOT
			<!DOCTYPE html>
			<html lang="pt-br">
			<head>
			<meta name="viewport" content="width=device-width" />
			<meta charset="UTF-8">
			<title></title>
			<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
			<script type="text/javascript" src="./bootstrap/js/bootstrap.js"></script>
			<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
			<link rel="stylesheet" href="/hardness3/smarty/B2B/css/responsive.css">
			<link rel="stylesheet" href="/hardness3/smarty/B2B/css/jquery-ui.css">
			<link href="./smarty/B2B/style.css" rel="stylesheet" type="text/css">
			<link href="/hardness3/smarty/B2B/css/v2/sb-admin-2.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    		<link href="/hardness3/smarty/B2B/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
		    <!-- QRCode -->
		    <script src="/hardness3/smarty/B2B/js/v2/qrcode.js"></script>
		    <script src="/hardness3/smarty/B2B/js/v2/qrcode.min.js"></script>
		    <script src="/hardness3/smarty/B2B/js/v2/jquery.min.js"></script>

			<script>
			  	$(document).ready(function() {
			  		{$js}
			  		$('input:first').focus();
			  		new QRCode(document.getElementById('qrcode'),{
			        	text:'http://{$_SERVER['HTTP_HOST']}/hardness3/outros/b2b/index.php?emailqrcode={$email}',
			        	width:100,
			        	height:100,
			        	correctLevel: QRCode.CorrectLevel.M	
			    	})	  	
			  	});
			</script>
			</head>
EOT;

if(isset($_REQUEST['enviarSenha'])){
	echo <<<EOT
	<body class="login-bg">
		<div class="container col-md-4">
			<div style="text-align: center;">
				<img src="{$logo}" style="margin: 40px 0 25px 0;"/>
			</div>
			<p class="title-rec-senha">Recuperação de senha</p>
			<form id="form-login" class="user card-body" action="./index.php" method="post">
				<div class="alert alert-error hide">
				</div>
					<input type="text" class="form-control form-control-user" placeholder="Email" name="email">
					<button class="btn-primary-p btn-login marg" type="submit">Enviar</button>
					<a class="link" id="link_voltar" href="/hardness3/outros/b2b/?ajax=true">Voltar</a>
				<div class="clearfix"></div>
			</form>
			<div class="footer">
				<p style="text-align: center; color: white;">&copy; {$rodape}</p>
			</div>
		</div> <!-- /container -->
	</body>
	</html>
EOT;

} else {
	echo <<<EOT
	<body class="login-bg">
		<div class="container grid-web">
    		<div class="row justify-content-center">
		      	<div class="col-md-12 row">
		        	<div class="col-md-3">
		        		<img src="{$imgPhone}" class="img-cell">
	        		</div>
		        	<div class="col-md-2 text-qrcode">
		        		<div class="title-qrcode">ACESSE TAMBÉM PELO CELULAR</div>
		        		<div class="p1-qrcode">
			        		<p class="p-qrcode">No navegador do seu celular, acesse pela url:</p> 
			        		<div class="qrcode-link">http://{$_SERVER['HTTP_HOST']}/b2b</div>
		        		</div>
		        		<div class="p2-qrcode">
			        		<p class="p-qrcode space">Ou utilize o QR code abaixo:</p>
			        		<p class="p-qrcode">Informe seu email para gerar o QR code de acesso</p>   			
		        			<div class="qrcode-email">
			        			<div class="card-qrcode shadow">
			        				<div id="qrcode" style="{$opacity}"></div>
				        		</div>
								<form class="user" action="./index.php" method="get">
			        				<input type="text" class="form-control form-control-user qrcode-input" placeholder="Email" name="emailqrcode" value="$email">
		        					<button class="btn-primary-p btn-gerar" type="submit" id="gerarQr">Gerar QR code</button>
	        					</form>
	    					</div>
    					</div>
	        		</div>
		        	<div class="col-md-6 div-form-login">
		        		<div class="login-box">
			        		<div class="col-lg-6 center">
								<img src="{$logo}" class="logo-lgn"/>
							</div>
				                <div class="text-center">
				                    <h1 class="h4 text-gray-900 mb-5"></h1>
				                </div>
				                <div class="alert alert-error hide">
									<strong>Erro!</strong><span></span>
								</div>
				                <form class="user" action="./index.php" method="post">
				                    <div class="form-group">
				                    	<input type="text" class="form-control form-control-user" placeholder="Email" name="login" value="$email">
				                    </div>
				                    <div class="form-group">
				                    	<input type="password" class="form-control form-control-user" placeholder="Senha" name="senha">
				                    </div>
				                    <hr>  
				                    <button class="btn-primary-p btn-login" type="submit">
				                        Entrar
				                    </button>
				                	<div class="forgot-password">
				                    	<a class="f-pass" href="/hardness3/outros/b2b/?ajax=true&enviarSenha=true">Esqueci a senha</a>
				                	</div>  
				                	<div class="clearfix"></div>
				                </form>   					
							</div>	
						</div>	   
	        		</div>
		  		</div>
	    	</div>
		</div>
		<div class="container grid-mobile">
			<div class="col-lg-6 center">
				<div style="text-align: center;">
				<img src="{$logo}" style="margin-top:9%;" />
			</div>
                <div class="text-center">
					<!--<h1 class="h4 text-gray-900 mb-5">{$row['C004_Nome_Abreviado']}</h1>-->
					<h1 class="h4 text-gray-900 mb-5">&nbsp;</h1>
                </div>
                <div class="alert alert-error hide">
					<strong>Erro!</strong> <span></span>
				</div>
                <form class="user" action="./index.php" method="post">
                    <div class="form-group">
                    	<input type="text" class="form-control form-control-user" placeholder="Email" name="login" value="$email">
                    </div>
                    <div class="form-group">
                    	<input type="password" class="form-control form-control-user" placeholder="Senha" name="senha">
                    </div>
                    <hr>  
                    <button class="btn-primary-p btn-login" type="submit">
                        Entrar
                    </button>
                	<div class="forgot-password">
                    	<a class="f-pass" href="/hardness3/outros/b2b/?ajax=true&enviarSenha=true">Esqueci a senha</a>
                	</div>  
                	<div class="clearfix"></div>
                </form>   					
				</div> <!-- /container -->
			</div>
		</div>
	</body>
	</html>
EOT;
}

echo<<<EOT
<script>
    $("#gerarQr").click(function(){
    	$("#qrcode").empty();
        new QRCode(document.getElementById('qrcode'),{
        	text:'http://{$_SERVER['HTTP_HOST']}/hardness3/outros/b2b/index.php?emailqrcode={$email}',
        	width:100,
        	height:100,
        	correctLevel: QRCode.CorrectLevel.M	
    	})
    });  
</script>
EOT;
?>

</html>

