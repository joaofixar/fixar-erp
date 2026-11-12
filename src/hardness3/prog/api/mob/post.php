<?php
namespace hardness;
//funções gerais e funções pertinentes a api do mobile
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'produto':
		global $g;

		$retorno = $API001->auth($_GET['API_AUTH']);

		if (is_array($retorno)) {
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$quantidade = $g['C031']['QuantidadePadraoCarrinho'];
		$D009_Id = $_GET['D009_Id'];
		$preco = $_GET['preco'];
		$retorno = array();

		$T269 = mysql_query("SELECT T269_Id 
							FROM T269 
							WHERE T269_D009_Id='{$D009_Id}' AND T269_D024_Id='{$g['clienteAtual']}' AND T269_D013_Id='{$g['clienteAtualContato']}'");
		if (mysql_num_rows($T269) <= 0) {
			mysql_query("INSERT INTO T269 (T269_D009_Id,T269_Quantidade,T269_D024_Id,T269_C007_Id,T269_D013_Id,T269_Preco) 
						VALUES ('{$D009_Id}','{$quantidade}','{$g['clienteAtual']}','{$g['clienteAtualVendedor']}','{$g['clienteAtualContato']}','{$preco}')");
			$error = mysql_error();

			if(empty($error)){
				header('HTTP/1.0 201 Created');
				$retorno['code'] = 201;
				$retorno['mensagem'] = "Inserido com sucesso!";
			}else{
				header('HTTP/1.0 400 Bad Request');
				$retorno['code'] = 400;
				$retorno['mensagem'] = "Erro ao inserir produto:".$error;
			}
		}else{
			header('HTTP/1.0 400 Bad Request');
			$retorno['code'] = 400;
			$retorno['mensagem'] = "Produto já se encontra no carrinho.";
		}

		echo json_encode($retorno, JSON_UNESCAPED_UNICODE);

		die();
	break;

	case 'pedidoB2B':
		global $g;

		$retorno = $API001->auth($_GET['API_AUTH']);

		if (is_array($retorno)) {
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		require_once 'bibliotecas/classes/B2B001.php';
		$B2B001 = new B2B001();

		//Verifica os pedidos
		$retorno = $B2B001->B2BGerarPedido();

		if($retorno[0] === true){
			mysql_query("DELETE FROM T269 WHERE T269_D024_Id='{$g['clienteAtual']}' AND T269_D013_Id='{$g['clienteAtualContato']}'");
			if($retorno[1]===true){
				$mensagemEmail="Email enviado com sucesso"; 
			}else{
				$mensagemEmail = "Email não foi enviado.";
			}
			header('HTTP/1.0 200 Ok');
			$retorno['code'] = 201;
			$retorno['mensagem'] = "Pedido inserido com sucesso!";
			$retorno['envioEmail'] = true;
			$retorno['mensagemEnvioEmail'] = $mensagemEmail;
		}else{
			header('HTTP/1.0 400 Bad Request');
			$retorno['code'] = 400;
			$retorno['mensagem'] = "Erro ao inserir pedido:".$retorno[1];
		}
		
		echo json_encode($retorno, JSON_UNESCAPED_UNICODE);

		die();
	break;

	case 'orcamentoB2B':
		global $g;

		$retorno = $API001->auth($_GET['API_AUTH']);

		if (is_array($retorno)) {
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		require_once 'bibliotecas/classes/B2B001.php';
		$B2B001 = new B2B001();

		//Verifica os pedidos
		$retorno = $B2B001->B2BGerarOrcamento();

		if ($retorno[0] === true) {
			mysql_query("DELETE FROM T269 WHERE T269_D024_Id='{$g['clienteAtual']}' AND T269_D013_Id='{$g['clienteAtualContato']}'");
			if ($retorno[1] === true) {
				$mensagemEmail = "Email enviado com sucesso";
			} else {
				$mensagemEmail = "Email não foi enviado.";
			}
			header('HTTP/1.0 200 Ok');
			$retorno['code'] = 201;
			$retorno['mensagem'] = "Orçamento inserido com sucesso!";
			$retorno['envioEmail'] = true;
			$retorno['mensagemEnvioEmail'] = $mensagemEmail;
		} else {
			header('HTTP/1.0 400 Bad Request');
			$retorno['code'] = 400;
			$retorno['mensagem'] = "Erro ao inserir orçamento:" . $retorno[1];
		}

		echo json_encode($retorno, JSON_UNESCAPED_UNICODE);

		die();
	break;

	case 'esqueciSenha':
		global $g,$confUsuario,$sys;
		header("Access-Control-Allow-Headers: Content-Type");
		header('Access-Control-Allow-Headers: "Origin, X-Requested-With, Content-Type, Accept"');
		header("Access-Control-Allow-Origin: *", false);
		header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD");
		
		$D013_E_Mail = trim(base64_decode($_GET['API_AUTH']));
		
		$retorno = array();
		$sqlEmailUsuario = mysql_query("SELECT D024_C004_Id,
											   D024_Nome_Empresa,
											   D013_E_Mail,
											   D013_Id,
											   D013_Senha,
											   D179_Senha,
											   D013_Nome_Contato,
											   C007_Email,
											   C007_Primeiro_Nome,
											   C007_Email_SMTP_Servidor,
											   C007_Email_SMTP_Autenticacao,
											   C007_Email_SMTP_Usuario,
											   C007_Email_SMTP_Senha,
											   C004_Nome_Empresa,
											   C004_Nome_Abreviado,
											   C004_Logo_Marca_B2B
											FROM D024
		                          	   	LEFT JOIN D013 ON D013_D024_Id=D024_Id
		                          	   	LEFT JOIN D179 ON D179_Login=D013_E_Mail
										LEFT JOIN C007 on D024_C007_Vendedor_Interno=C007_Id
										LEFT JOIN C004 ON IF(IFNULL(D024_C004_Id,0)>0,D024_C004_Id=C004_Id,1)
		                          		   WHERE D013_E_Mail='{$D013_E_Mail}' AND D013_Flag_Ecommerce='S' limit 1 ");
		$erro = mysql_error();
		if (!empty($erro)) {
			header('HTTP/1.0 400 Bad Request');
			$retorno['code'] = 400;
			$retorno['mensagem'] = "Erro select:" . $erro;
			$array = array();
			$array['retorno'] = $retorno;
			echo json_encode($array, JSON_UNESCAPED_UNICODE);
		}else{
			if (mysql_num_rows($sqlEmailUsuario) <= 0) {
				header('HTTP/1.0 400 Bad Request');
				$retorno['code'] = 400;
				$retorno['mensagem'] = "Email ou senha incorreto.";
				$array = array();
				$array['retorno'] = $retorno;
				echo json_encode($array, JSON_UNESCAPED_UNICODE);
				die();
			}else{
				$rowEmailUsuario = mysql_fetch_assoc($sqlEmailUsuario);
				
				$backgroundColor = $sys['loginBackground'];
				$rodape = 'Hardness Sistemas ' . date('Y');
				$logo = "http://" . $_SERVER['HTTP_HOST'] . "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo".$rowEmailUsuario['C004_Logo_Marca_B2B'];
				$rowEmailUsuario['C004_Nome_Abreviado'] = substr($rowEmailUsuario['C004_Nome_Abreviado'], 0, 30);

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
									SENHA: {$rowEmailUsuario['D179_Senha']}</p>
									<p>Esse e-mail foi enviado por um sistema automático e não poderá ser respondido.</p>
									<br/><br/>
								</div>
							</div>
						</body>
					<html>
EOT;
				require_once $confUsuario['pathRaiz']."bibliotecas/phpmailer/class.phpmailer.php";
				$mail = new \phpmailer;
				// Mail
				$mail->From = $rowEmailUsuario['C007_Email'];
				$mail->FromName = $rowEmailUsuario['C007_Primeiro_Nome'];
				$mail->Host = $rowEmailUsuario['C007_Email_SMTP_Servidor'];
				$mail->Mailer = "smtp";
				$mail->SMTPAuth = $rowEmailUsuario['C007_Email_SMTP_Autenticacao'];
				$mail->Username = $rowEmailUsuario['C007_Email_SMTP_Usuario'];
				$mail->Password = $rowEmailUsuario['C007_Email_SMTP_Senha'];
				$mail->Subject = "Reenvio de Senha {$row['C004_Nome_Abreviado']}";
				$mail->Body = $corpoEmail;
				$mail->AltBody = "ALT BODY";
				$mail->CharSet = 'utf-8';
				$mail->SetLanguage("br");
				$mail->IsHTML(true);
				if ($rowEmailUsuario['C007_Email_SMTP_Servidor'] == "smtp.gmail.com") {
					$mail->SMTPSecure = "ssl";
					$mail->Port = "465";
				}
				if ($rowEmailUsuario['C007_Email_SMTP_Servidor'] == "smtp.live.com") {
					$mail->SMTPSecure = "ssl";
				}
				if (!empty($rowEmailUsuario['C007_Email_SMTP_Porta'])) {
					$mail->Port = $rowEmailUsuario['C007_Email_SMTP_Porta'];
				}

				$monta_['Para'] = $rowEmailUsuario['D013_E_Mail'];
				$monta_['cc'] = '';

				$todosEmailsEnviados = array();
				$mPara = preg_split("/[;,]+/", $monta_['Para']);
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

				$anexosGravar = array();
				$T139_Estrutura = array(
					'anexosManual' => array(),
				);
				if (isset($monta_['Anexos'])) {
					foreach ($monta_['Anexos'] as $path) {
						$anexosGravar[] = $path;
						$T139_Estrutura['anexosManual'][] = basename($path);
						$mail->AddAttachment($path, basename($path));
					}
				}

				if ($retornoMail = $mail->Send()) {
					header('HTTP/1.0 200 Ok');
					$retorno['code'] = 200;
					$retorno['mensagem'] = "Email enviado com sucesso.";
				} else {
					$cErro = str_replace(array("'", '"', "\r", "\n"), "", $mail->ErrorInfo);
					$cErro = str_replace(array("<p>", "</p>"), "<br /><br />", $cErro);
					header('HTTP/1.0 400 Bad Request');
					$retorno['code'] = 400;
					$retorno['mensagem'] = "Email não enviado, erro retornado: {$cErro}.";
				}
				$array = array();
				$array['retorno'] = $retorno;
				echo json_encode($array, JSON_UNESCAPED_UNICODE);
				die();
			}
		}

	break;

	case 'token':
		global $g,$confUsuario;

		$deviceToken = $_GET['DEVICE_TOKEN'];
		$retorno = array();

		$API001->verificaParametroObrigatorio($deviceToken, "device_token");

		$retornoFuncao = $API001->auth($_GET['API_AUTH']);
		if (is_array($retornoFuncao)) {
			echo json_encode($retornoFuncao, JSON_UNESCAPED_UNICODE);
			die();
		}

		$D179_Id=$API001->getD179_Id();
		$API001->verificaParametroObrigatorio($D179_Id,"contato");

		$sqlD180 = mysql_query("SELECT D180_Id 
							   FROM D180 
					      LEFT JOIN D179 ON D180_D179_Id=D179_Id
							  WHERE D180_Token='{$deviceToken}'");
		if (mysql_num_rows($sqlD180)<=0) {

			$retornoTopico = $API001->inscreverTopicoFirebase($confUsuario['topicoFirebase'], $deviceToken);
			// echo "\nRetorno erro topico:".$retornoTopico['code'];
			// echo "\nRetorno erro topico:".$retornoTopico['message'];
			
			if($retornoTopico['code']==200){
				mysql_query("INSERT INTO D180 (D180_D179_Id,D180_Token) VALUES ('{$D179_Id}','{$deviceToken}')");
				$error = mysql_error();

				if(empty($error)){
					header('HTTP/1.0 201 Created');
					$retorno['code'] = 201;
					$retorno['mensagem'] = "Inserido com sucesso!";

				}else{
					header('HTTP/1.0 400 Bad Request');
					$retorno['code'] = 400;
					$retorno['mensagem'] = "Erro ao inserir token:" . $error;
				}
			}else{
				header('HTTP/1.0 400 Bad Request');
				$retorno['code'] = 400;
				$retorno['mensagem'] = "Token não inserido, problema na inscrição do token no tópico";
			}
			$retorno['codeInscreverTopico'] = $retornoTopico['code'];
			$retorno['mensagemInscreverTopico'] = $retornoTopico['message'];
		}else{
			header('HTTP/1.0 400 Bad Request');
			$retorno['code'] = 400;
			$retorno['mensagem'] = "Token já cadastrado.";
		}
		echo json_encode($retorno, JSON_UNESCAPED_UNICODE);

		die();
	break;

	default:
		$API001->retorno404();
}