<?php
namespace hardness;
echo<<<EOT
	<script>
		window.location.replace("http://{$_SERVER['HTTP_HOST']}/b2b");
	</script>
EOT;
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');

$js = '';
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
	$D013_Id=$_REQUEST['D013_Id'];
	$Banco_Dados = $w = $sb = $us = $uc = $Empresa_Usuaria = $PaginaQueIniciou = $nWidth_Padrao = $nHeight_Padrao = '';
	$user = explode('/', $confUsuario['pathRaiz']);
  	$user = $user[2];
  	if (empty($l)) {
	   $js .= "alert('Digite seu e-mail.');";
	} else if (empty($s)) {
		$js .= "alert('Digite a senha.');";
	} else if (empty($e)) {
		$js .= "alert('Selecione a empresa.');";
	} else {

		if (isset($D013_Id)){
			$contato = "AND D013_Id={$D013_Id}";
		}

		$sqlEmpresaUsuario = mysqli_query("SELECT D024_C004_Id, D013_E_Mail, D013_Id, D024_Nome_Empresa, D024_Cnpj, D018_UF
											FROM D024
		                          	   LEFT JOIN D013 ON D013_D024_Id=D024_Id
		                          	   LEFT JOIN D018 ON D024_D018_Id=D018_Id
		                          		   WHERE D013_E_Mail='{$l}' and ifnull(D013_Senha,'')!='' $contato");

		if(mysql_num_rows($sqlEmpresaUsuario)>1){

			if($confUsuario['dbDatabase']=='e212bkp'){
				$sql = "SELECT C004_Nome_Abreviado FROM C004 WHERE C004_Id=4";
			}else{
				$sql = "SELECT C004_Nome_Abreviado FROM C004 ORDER BY C004_Id ASC LIMIT 1";
			}

				$Sql=mysqli_query("select * from D013
	                           left join D024 on D024_Id=D013_D024_Id
	                           where D013_E_Mail='{$l}' and D013_Senha='{$s}'");

			    $mSenha=mysql_fetch_array($Sql);
			    if (mysql_num_rows($Sql)==0 or $s != $mSenha['D013_Senha']) {
			    	$js .= "alert('E-Mail ou Senha inválida.');";
				}else{
					$res = mysqli_query($sql);
					$row = mysql_fetch_assoc($res);
					$logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/B2B_1.jpg";
					$row['C004_Nome_Abreviado'] = substr($row['C004_Nome_Abreviado'], 0 , 30);
					echo <<<EOT
					<!DOCTYPE html>
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

								.link{
									margin-left:110px;
								}
								#link_voltar{
									margin-left:170px;margin-top:3px;
								}
								
								.link_voltar {
									background: #5a88ca;
									border: medium none;
									color: #fff;
									padding: 6px 25px;
									align:right;
								}
								.link_voltar:hover {
									background: #000;
									border: medium none;
									color: #fff;
									padding: 6px 25px;
									align:right;
									text-decoration:none;
								}

							</style>
							<script>
							  	$(document).ready(function() {
							  		{$js}
							  		$('input:first').focus();
							  	});
							</script>
						</head>
						<body>
							<div class="container">
								<div style="text-align: center;">
									<img src="{$logo}" style="margin: 40px 0 25px 0;" />
								</div>
								<div style="text-align: center;" class="caixinha">
									<h4 style="margin-bottom: 20px; text-align:left;text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);">{$row['C004_Nome_Abreviado']}</h4>
									<h4 style="margin-bottom: 20px; text-align:left;text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);">Escolha a empresa que deseja se logar:</h4>
									<table class="table table-striped table-hover ">
										<thead>
											<tr>
												<th>Empresa</th>
												<th>CNPJ</th>
												<th>UF</th>
											<tr>
										</thead>
EOT;
								while ($empresas = mysql_fetch_array($sqlEmpresaUsuario)){
									echo <<<EOT
										

										<tr class="active" style="cursor:pointer;background-color:#fff;" onclick='javascript:location.replace("./index.php?login=$l&senha=$s&D013_Id={$empresas['D013_Id']}")'>
											<td>
												{$empresas['D024_Nome_Empresa']}
											</td>
											<td>
												{$empresas['D024_Cnpj']}
											</td>
											<td>
												{$empresas['D018_UF']}
											</td>
										</tr>
EOT;
								}
			echo <<<EOT
						</table>
						<a class="link_voltar" href="/hardness3/outros/loginb2b/">Voltar</a>
					</div>
						<div class="footer">
							<p style="text-align: center; color: white;">&copy; {$rodape}</p>
						</div>
					</div> <!-- /container -->
				</body>
				<html>

EOT;
		die();
				}

				
		} else {
			$rowEmpresaUsuario = mysql_fetch_array($sqlEmpresaUsuario);
		}

		if ($rowEmpresaUsuario['D013_Id']<=0) {
			$js = "alert('{$confUsuario['urlRaiz']}b2b');";
			$js .= "location.replace('{$confUsuario['urlRaiz']}b2b');";
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
		} else if (mysql_num_rows($sqlEmpresaUsuario) == 1){
			$e = $rowEmpresaUsuario['D024_C004_Id'];
		}

		if (isset($D013_Id)){
			$contato = "AND D013_Id={$D013_Id}";
		}

		$sqlEmpresaUsuario = mysqli_query("SELECT D024_C004_Id, D013_E_Mail, D013_Id, D024_Nome_Empresa, D024_Cnpj, D018_UF
											FROM D024
		                          	   LEFT JOIN D013 ON D013_D024_Id=D024_Id
		                          	   LEFT JOIN D018 ON D024_D018_Id=D018_Id
		                          		   WHERE D013_E_Mail='{$l}' and ifnull(D013_Senha,'')!='' $contato");

		$empresas = mysql_fetch_array($sqlEmpresaUsuario);
		$Cnpj = $empresas['D024_Cnpj'];
		$Nome_Empresa = $empresas['D024_Nome_Empresa'];

		$cC004=mysqli_query("select * from C004 where C004_Id='$e'");
		$mC004=mysql_fetch_array($cC004);
		if (empty($e)) {
			$js .= "$('#Mensagem').dialog({ 
						resizable: false,
					    height: 'auto',
					    width: 320,
					    modal: true,
						buttons: {
					        Ok: function() {
					          $( this ).dialog('close');
					        }
			  			}
			  		});";
			  	echo<<<EOT
				  <div id="Mensagem" title="Mensagem" style="display:none;">
					
						<p>
							Está faltando informações em seu cadastro.<br>
							<br>Por favor contate seu vendedor e informe a seguinte mensagem:<br>
							<br>Empresa não definida no seu cadastro de cliente.<br>
							<br>{$Nome_Empresa}<br>
							<br>{$Cnpj}<br>
						</p>
					
				  </div>
EOT;
			  	
		} else if (empty($mC004['C004_Banco_Configuracao'])) {
			$js .= "alert('Banco de configuração nao definido.');";
		} else {
			$cBd_Configuracao=$mC004['C004_Banco_Configuracao'];

			$cC020=mysqli_query("select C020_Modelo_Sistema_Nfe from C020 where C020_C004_Id='$mC004[C004_Id]'");
			$mC020=mysql_fetch_array($cC020);

		    // Consulta a Senha Digitada do Usuario Selecionado
    		$Sql=mysqli_query("select * from D013
                           left join D024 on D024_Id=D013_D024_Id
                           left join C007 on C007_Id=D024_C007_Vendedor_Interno
                           where D013_Id='{$rowEmpresaUsuario['D013_Id']}'
                           and D024_Flag_Ativo='S'");

		    $mSenha=mysql_fetch_array($Sql);
		    if (mysql_num_rows($Sql)==0 or $s != $mSenha['D013_Senha']) {
		    	$js .= "alert('E-Mail ou Senha inválida (1).');";
		    } else {
		  		if (empty($mSenha['C007_Id'])) {
		  			$js .= "alert('Não foi definido vendedor no seu cadastro de cliente.');";
		  		} else if (empty($mC004['C004_Caminho_Sistema'])) {
		  			$js .= "alert('Pasta da empresa não definida.');";
				} else {
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
					$_SESSION['Cliente_Atual']=$mSenha['D024_Id'];
					$_SESSION['Cliente_Atual_Contato']=$mSenha['D013_Id'];
					$_SESSION['Versao_B2B']='1';
					$_SESSION['Cliente_Atual_Vendedor']=$mSenha['D024_C007_Vendedor_Interno'];


					// -> Registra um historico de Enderecos
					$_SESSION['URL_Historico']="";

					// -> Registra um historico de Tipos de Telas
					$_SESSION['URL_Tipo_Historico']="";

					// -> Registra um historico de nomes l�gicos das p�ginas acessadas
					$_SESSION['URL_Descricao_Historico']="";

					// -> Registra um historico com as Entidades Principais
					$_SESSION['URL_Entidade_Principal']="";

					//
					// Guarda o numero pagina acessada no monta_consultas_fme
					// Utiliza como chave da matriz a Entidade+cTela+Entidade_Principal+Id_Entidade_Principal
					//
					$_SESSION['Pagina_Monta_Consulta']="";
					$_SESSION['Chave_Pagina_Monta_Consulta']="";

					$_SESSION['PaginaQueIniciou']=$PaginaQueIniciou;

					if (isset($confUsuario['urlRaiz'])) {
						$js .= "location.replace('{$confUsuario['urlRaiz']}erp/');";
					} else {
						$js .= "location.replace('http://{$user}/erp/');";
					}
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
					//echo "<div class='divHidden'><script type='text/javascript'> if (typeof jQuery != 'undefined') { fecharJanela('{$g['divId']}'); } </script></div>";
				}
		   }
		}
	}
}

if (isset($_REQUEST['email'])) {
	
	$email = $_REQUEST['email'];
	$user = explode('/', $confUsuario['pathRaiz']);
	
	if (empty($email)) {
	   	$js .= "alert('Digite seu e-mail.');";
	  	$js .= "location.replace('/hardness3/outros/loginb2b/?ajax=true&enviarSenha=true');";
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
											   C007_Email_SMTP_Senha
											FROM D024
		                          	   LEFT JOIN D013 ON D013_D024_Id=D024_Id
										LEFT JOIN C007 on C007_Id = D024_C007_Vendedor_Interno	 
		                          		   WHERE D013_E_Mail='{$email}' limit 1");
		
		$rowEmailUsuario = mysql_fetch_array($sqlEmailUsuario);
		
		$erro = mysql_error();
		if(!empty($erro)){
			die($erro);
		}

		if ($rowEmailUsuario['D013_Id']<=0) {
			$js = "alert('E-Mail ou senha incorretos.');";
			$js .= "location.replace('/hardness3/outros/loginb2b/?ajax=true&enviarSenha=true');";
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
			$sql = "SELECT C004_Nome_Abreviado FROM C004 WHERE C004_Id=4";
		}else{
			$sql = "SELECT C004_Nome_Abreviado FROM C004 ORDER BY C004_Id ASC LIMIT 1";
		}

		$res = mysqli_query($sql);
		$row = mysql_fetch_assoc($res);

		$backgroundColor = $sys['loginBackground'];
		$rodape = 'Hardness Sistemas ' . date('Y');
		$logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/B2B_1.jpg";
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
							LOGIN: {$rowEmailUsuario['D013_E_Mail']}<br/>
							SENHA: {$rowEmailUsuario['D013_Senha']}</p>
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
			location.replace('/hardness3/outros/loginb2b/?ajax=true');
			</script>
EOT;
		}else{
			$cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
			$cErro=str_replace(array("<p>","</p>"),"<br /><br />",$cErro);
			echo <<<EOT
			<script>
			alert("Email não enviado {$cErro}");
			location.replace('/hardness3/outros/loginb2b/?ajax=true&enviarSenha=true');
			</script>
EOT;
		}
        die();
	}
}		
// Caso for a sigma irá logar com a empresa 4 como padrão. Analisar ua maneira disso ser configurável por empresa.
		if($confUsuario['dbDatabase']=='e212bkp'){
			$sql = "SELECT C004_Nome_Abreviado FROM C004 WHERE C004_Id=4";
		}else{
			$sql = "SELECT C004_Nome_Abreviado FROM C004 ORDER BY C004_Id ASC LIMIT 1";
		}

		$res = mysqli_query($sql);
		$row = mysql_fetch_assoc($res);

		$backgroundColor = $sys['loginBackground'];
		$rodape = 'Hardness Sistemas ' . date('Y');
		$logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/B2B_1.jpg";
		$row['C004_Nome_Abreviado'] = substr($row['C004_Nome_Abreviado'], 0 , 30);
		echo <<<EOT
			<!DOCTYPE html>
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

			<style>
			  	html, body {
				    height: 100%;
				}
				body {
					#fff;
				}

				.container-narrow {
					margin: 0 auto;
					max-width: 300px;
					text-align: center;
					padding: 30px 0 20px 0;
				}

				.form-signin {
				  	max-width: 300px;
				  	padding: 19px 29px 29px;
				  	margin: 0 auto 20px;
				  	background-color: #fff;
				  	border: 1px solid #e5e5e5;
				  	-webkit-border-radius: 5px;
				    -moz-border-radius: 5px;
				    border-radius: 5px;
				  	-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
				    -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
				    box-shadow: 0 1px 2px rgba(0,0,0,.05);
				}
			 	.form-signin .form-signin-heading, .form-signin .checkbox {
			    	margin-bottom: 10px;
			  	}
			  	.form-signin input[type="text"], .form-signin input[type="password"] {
				    font-size: 16px;
				    height: auto;
				    margin-bottom: 15px;
				    padding: 7px 9px;
			  	}
			  	#form-login {
				   /*background: url("nfe_transp.png") no-repeat scroll -2px -13px white;*/
				   background-color: #F4F4F4;
			  	}
			  	.button_form_submit {
					background: #5a88ca;
					border: medium none;
					color: #fff;
					padding: 6px 25px;
					align:right;
				}
				.button_form_submit:hover {
					background: #000;
					border: medium none;
					color: #fff;
					padding: 6px 25px;
					align:right;
				}

				.link{
					margin-left:90px;
				}
				#link_voltar{
					margin-left:170px;margin-top:3px;
				}

			</style>
			<script>
			  	$(document).ready(function() {
			  		{$js}
			  		$('input:first').focus();
			  	});
			</script>
			</head>
EOT;

if(isset($_REQUEST['enviarSenha'])){	

	echo <<<EOT
		<body>
			<div class="container">
				<div style="text-align: center;">
					<img src="{$logo}" style="margin: 40px 0 25px 0;"/>
				</div>
				<form id="form-login" class="form-signin" action="./index.php" method="post">
					<h4 style="margin-bottom: 20px; text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);">{$row['C004_Nome_Abreviado']}</h4>
					<!--<h2 class="form-signin-heading">Login</h2>-->
					<div class="alert alert-error hide">
					</div>
						<input type="text" class="input-block-level" placeholder="E-Mail" name="email">
						<button class="button_form_submit" type="submit">Enviar</button>
						<a class="link" id="link_voltar" href="/hardness3/outros/loginb2b/?ajax=true">Voltar</a>
					<div class="clearfix"></div>
				</form>
				<div class="footer">
					<p style="text-align: center; color: white;">&copy; {$rodape}</p>
				</div>
			</div> <!-- /container -->
		</body>

EOT;


} else {

	echo <<<EOT
		<body>
			<div class="container">
				<div style="text-align: center;">
					<img src="{$logo}" style="margin: 40px 0 25px 0;" />
				</div>
				<form id="form-login" class="form-signin" action="./index.php" method="post">
					<h4 style="margin-bottom: 20px; text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);">{$row['C004_Nome_Abreviado']}</h4>
					<!--<h2 class="form-signin-heading">Login</h2>-->
					<div class="alert alert-error hide">
						<strong>Erro!</strong> <span></span>
					</div>
					<input type="text" class="input-block-level" placeholder="E-Mail" name="login">
					<input type="password" class="input-block-level" placeholder="Senha" name="senha">
					<button class="button_form_submit" type="submit">Entrar</button>
					<a class="link" href="/hardness3/outros/loginb2b/?ajax=true&enviarSenha=true">Esqueci a senha</a>
					<div class="clearfix"></div>
				</form>
				<div class="footer">
					<p style="text-align: center; color: white;">&copy; {$rodape}</p>
				</div>
			</div> <!-- /container -->
		</body>

EOT;

}


?>

</html>

