<?php
if(!empty($_POST)){
	$resposta = array('code' => true, 'data' => array());

	$erros = '';
	if(!empty($_REQUEST['email']) && !empty($_REQUEST['mensagem']) && !empty($_REQUEST['assunto'])) {
		$email 		= $_REQUEST['email'];
		$emailConteudo 	= $_REQUEST['mensagem'];
		$emailAssunto 	= $_REQUEST['assunto'];

		if (preg_match('/(?:[a-z0-9\'\.\-_\+\|]++|&amp;)+@[a-z0-9\-]+\.(?:[a-z0-9\-]+\.)*[a-z]+/Uusmi', trim($email)) == 0) {
			$erros .= "Insira um e-mail válido!";
		}
	} else {
		$erros = "Os campos com (*) devem ser preenchidos!";
	}

	if($erros != '') {
		$resposta['data'] = $erros;
		$resposta['code'] = false;
	} else {
		require_once("../../hardness3/bibliotecas/phpmailer/class.phpmailer.php");

		$emailDestino = "suporte@hardness.com.br";
		// Mail
		$mail = new \phpmailer;
		$mail->From     = $email;
		$mail->FromName = $email;
		$mail->Host     = "mail.hardness.com.br";
		$mail->Mailer   = "smtp";
		$mail->SMTPAuth = true;
		$mail->Username = "suporteinterno@hardness.com.br";
		$mail->Password = "hard3871";
		$mail->Subject	= $emailAssunto;
		$mail->Body     = $emailConteudo;
		$mail->AltBody  = "ALT BODY";
		$mail->CharSet  = 'utf-8';
		$mail->SetLanguage("br");
		$mail->IsHTML(false);
		$mail->Port 	= "587";
		$mail->AddReplyTo($email);
		
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
			$resposta['code'] = true;
		} else {
			$resposta['data'] = "Erro ao enviar, favor entrar em contato por telefone!";
			$resposta['code'] = false;
		}

	}
	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	die();
}

// Header
$headerHeadTitle = 'Hardness Sistemas - Suporte';
include('requisicoes/header.php');

echo <<<EOT
<div id="suporte" class="container">	
	<div class="row">
		<div class="col-xs-8">
			<form id="formSuporte" role="form">
				<div class="row">
					<div class="col-md-12" style="text-align: center;">
						<span style="text-align: center; font-weight: bold; font-size: 18px;">Suporte via E-mail</span><br />
						Deixe sua mensagem com todos os detalhes, responderemos dentro de 24 horas.
						<br /><span style="color: red;">* Campos obrigatórios.</span>
					</div>
				</div>
				<div class="row" style="margin-top: 15px;">
					<label style="text-align: right;" class="col-lg-3 control-label" for="assunto">* Assunto</label>
					<div class="col-md-9">
						<input class="form-control" name="assunto" id="assunto" type="text" maxlength="80" />
					</div>
				</div>
				<div class="row" style="margin-top: 15px;">
					<label style="text-align: right;" class="col-lg-3 control-label" for="email">* E-mail</label>
					<div class="col-md-9">
						<input class="form-control" type="text" name="email" id="email" maxlength="100" />
					</div>
				</div>
				<div class="row" style="margin-top: 15px;">
					<label style="text-align: right;" class="col-lg-3 control-label" for="mensagem">* Mensagem</label>
					<div class="col-md-9">
						<textarea class="form-control" type="textarea" name="mensagem" id="mensagem" rows="8" style="text-transform: lowercase;"></textarea>
					</div>
				</div>
				<div class="row" style="text-align: center;">
					<button type="submit" id="submit" class="btn btn-default btn-lg btn-primary pull-right" style="margin: 20px;">Enviar</button><br />
				</div>
			</form>
		</div>
		<div class="col-xs-4">
			<div class="row">
				<div class="col-md-12" style="text-align: center; font-weight: bold; font-size: 18px;">
					Suporte Remoto
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" style="text-align: center; font-weight: bold; font-size: 18px;">
					<a href="http://www.teamviewer.com/link/?url=842558&id=1385032344" style="text-decoration:none;">
						<img src="http://www.teamviewer.com/link/?url=945136&id=1385032344" alt="Baixe a versão completa do TeamViewer" title="Baixe a versão completa do TeamViewer" border="0" width="234" height="60" />
						<span style="position:absolute; top:15px; left:135px; display:block; cursor:pointer; color:White; font-family:Arial; font-size:13px; line-height:1.2em; font-weight:bold; text-align:center; width:174px;">
						Clique aqui para baixar o TeamViewer
						</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<br /><br /><br /><br /><br /><br />
EOT;
// Footer
$footerJS = <<<EOT
	$("#formSuporte").submit(function() {
		$('#submit').attr('disabled', 'disabled').html('Enviando...');
		$.ajax({
			url: '/suporte/',
			dataType: 'jsonp',
			type: 'POST',
			cache: false,
			data: $("#formSuporte").serialize(),
			success: function(request) {
				if (request.code) {
					$('.erroCampo').remove();
					$('#submit').addClass('btn-success').html('Enviado!');
				} else {
					$('.erroCampo').remove();
					$('#submit').removeAttr('disabled').html('Enviar');
					$('#submit').parent().append('<label class="erroCampo" class="control-label" for="inputError">' + request.data + '</label>');
				}
				return false;
			}
		});
		return false;
	});
EOT;
include('requisicoes/footer.php');