<?php
if(!empty($_POST)){
	$resposta = array('code' => true, 'data' => array());

	$erros = '';
	if(!empty($_REQUEST['login'])) {
		//Verifica os clientes
		$sqlC025 = mysql_query("SELECT * FROM C025 WHERE C025_Login = '{$_REQUEST['login']}'", $conNOTA);
		if(mysql_num_rows($sqlC025) == 0){
			//Se nao encontrar um usuario, verifica as contabilidades
			$sqlC026 = mysql_query("SELECT * FROM C026 WHERE C026_Login = '{$_REQUEST['login']}'", $conNOTA);
			if(mysql_num_rows($sqlC026) == 1){
				$rowC026 = mysql_fetch_array($sqlC026);
				if(!empty($rowC026['C026_Email'])){
					$emailDestino = $rowC026['C026_Email'];
					$login = $rowC026['C026_Login'];
					$senha = $rowC026['C026_Senha'];
				} else {
					$erros = true;
				}
			} else {
				$erros = true;
			}

		} else if(mysql_num_rows($sqlC025) == 1){
			$rowC025 = mysql_fetch_array($sqlC025);
			if(empty($rowC025['C025_Email'])){
				$erros = true;
			} else {
				$emailDestino = $rowC025['C025_Email'];
				$login = $rowC025['C025_Login'];
				$senha = $rowC025['C025_Senha'];
			}
		} else {
			$erros = true;
		}
		if($erros === true){
			$erros = 'Não foi possível encontrar seu cadastro, verifique o login informado.<br> Caso o problema persista, por favor entre em contato com o suporte técnico.';
		}
	} else {
		$erros = "Informe o login para enviar a solicitação de recuperação da senha!";
	}

	if($erros != '') {
		$resposta['data'] = $erros;
		$resposta['code'] = false;
	} else {
		require_once("../../hardness3/bibliotecas/phpmailer/class.phpmailer.php");

		$emailConteudo = <<<EOT
		<div style="width: 600px; color:black; font-family: Lato,Helvetica,Arial; color: #444; font-size: 14px; margin-left: auto; margin-right: auto;">
			<div style="padding: 7px; background-color: #0C4F72; background-image: -webkit-linear-gradient(bottom, #0C4F72, #324A77); background-image: -moz-linear-gradient(bottom, #0C4F72, #324A77); background-image: -ms-linear-gradient(bottom, #0C4F72, #324A77); background-image: -o-linear-gradient(bottom, #0C4F72, #324A77); background-image: linear-gradient(to top, #0C4F72, #324A77); color: white;">
				<img src="http://hardness.hardness.com.br/hardness3/static/img/logo_hardness.png"><br />
			</div>
			<div style="padding: 12px; border: 1px solid #999; border-top: 0;">
				<div style="font-size: 18px; font-weight: bold; margin-top: 10px;">Recuperação de senha</div>
				<p>Dados de acesso ao sistema:<br>
	                Login: <b>{$login}</b><br>
	                Senha: <b>{$senha}</b>
	            </p>
				<div style="text-align: justify; margin-top: 15px;">Caso necessite de auxílio com a autenticação no sistema entre em contato conosco através 
				do email suporte@hardness.com.br ou por telefone <br />41 3344-2938, estamos a sua disposição.</div>
				<p><div style="text-align: center; font-size: 11px; margin-bottom: -15px;">© Hardness Sistemas. Todos os direitos reservados.</div></p>
			</div>
		</div>
EOT;

		// Mail
		$mail = new \phpmailer;
		$mail->From     = "suporte@hardness.com.br";
		$mail->FromName = "Hardness Sistemas - Recuperação de senha";
		$mail->Host     = "mail.hardness.com.br";
		$mail->Mailer   = "smtp";
		$mail->SMTPAuth = true;
		$mail->Username = "suporteinterno@hardness.com.br";
		$mail->Password = "hard3871";
		$mail->Subject	= "Hardness Sistemas - Recuperação de senha";
		$mail->Body     = $emailConteudo;
		$mail->AltBody  = "ALT BODY";
		$mail->CharSet  = 'utf-8';
		$mail->SetLanguage("br");
		$mail->IsHTML(false);
		$mail->Port 	= "587";
		
		$todosEmailsEnviados = array();
		$mPara=preg_split("/[;,]+/", $emailDestino);
		for($x=0; $x<count($mPara); $x++){
			$email = $mPara[$x];
			if (strpos($email, '<') !== false) {
				$email = explode('<', $email);
				$email = explode('>', $email[1]);
				$email = $email[0];
			}
			$todosEmailsEnviados[] = strtolower(trim($email));
			$mail->AddAddress($email);
		}

		if($mail->Send()){
			preg_match('#([A-Za-z0-9.-/_^@]+)@([A-Za-z0-9-/_]+)(.*+)#', $email, $partes);

			$padding = (strlen($partes[1]) < 8) ? 3 : 5;
			$parte1 = substr($partes[1], 0, $padding) . str_pad($input, strlen($partes[1])-$padding, "*", STR_PAD_RIGHT);
			$parte2 = substr($partes[2], 0, $padding) . str_pad($input, strlen($partes[2])-$padding, "*", STR_PAD_RIGHT);			
			$emailOculto = $parte1.'@'.$parte2.$partes[3];
			$emailOculto = strtolower($emailOculto);

			$resposta['code'] = true;
			$resposta['data'] = "Sua senha foi enviada para o email {$emailOculto}.";
		} else {
			$resposta['data'] = "Erro ao enviar, favor entrar em contato por telefone!";
			$resposta['code'] = false;
		}

	}
	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	die();
}

// Header
$headerHeadTitle = 'Hardness Sistemas - Recuperação de senha';
include('requisicoes/header.php');

echo <<<EOT
<div id="suporte" class="container conteudo">	
	<div class="row">
		<div class="col-xs-12">
			<form id="formRecuperacaoSenha" role="form">
				<div class="row">
					<div class="col-md-12" style="text-align: center;">
						<span style="text-align: center; font-weight: bold; font-size: 18px;">Esqueci minha senha</span><br />
						Informe seu login, a senha será enviada para o e-mail cadastrado.
					</div>
				</div>
				<div class="row" style="margin-top: 5px;">
					<div class="col-md-2"></div>
					<div class="col-md-2" style="text-align: right;">
						<label class="control-label" for="login">Login</label>
					</div>
					<div class="col-md-3">
						<input class="form-control" name="login" id="login" type="text" maxlength="80" />
					</div>
					<div class="col-md-5">
						<button type="submit" id="submit" class="btn btn-default btn-lg btn-primary" style="margin-left: -20px; font-size: 14px;">Enviar</button>
					</div>
				</div>				
				<div class="row" style="text-align: center;">
				</div>
			</form>
		</div>
	</div>
</div>
EOT;
// Footer
$footerJS = <<<EOT
	$("#formRecuperacaoSenha").submit(function() {
		$('#submit').attr('disabled', 'disabled').html('Enviando...');
		$.ajax({
			url: '/esqueci-minha-senha/',
			dataType: 'jsonp',
			type: 'POST',
			cache: false,
			data: $("#formRecuperacaoSenha").serialize(),
			success: function(request) {
				if (request.code) {
					$('.erroCampo').remove();
					$('body').append('<div class="alert alert-success alertaLogin" role="alert" style="position: fixed; top:0; bottom: 0; left: 0; right: 0; margin: auto; width: 500px; overflow: auto; display: table; z-index: 10000;"><button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>' + request.data + '</div>');
					$('#submit').addClass('btn-success').html('Enviado!');
				} else {
					$('.erroCampo').remove();
					$('#submit').removeAttr('disabled').html('Enviar');
					$('body').append('<div class="alert alert-danger alertaLogin" role="alert" style="position: fixed; top:0; bottom: 0; left: 0; right: 0; margin: auto; width: 500px; overflow: auto; display: table; z-index: 10000;"><button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>' + request.data + '</div>');
				}
				return false;
			}
		});
		return false;
	});
EOT;
include('requisicoes/footer.php');