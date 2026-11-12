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
<div id="suporte" class="container conteudo">	
	<div class="row">
		<div class="col-xs-7">
			<form id="formSuporte" role="form">
				<div class="row">
					<div class="col-md-12">
						<div class="page-header">
							<h3 style="color:#1F4C74">Contato</h1>
						</div>
						<div class="col-md-6">
							<p>
								<span style="text-align: left; float: left; margin-left: 115px; color: #000000; font-size: 20px;">
									<small>41</small> <b>3344-2938</b><br>
									<small>suporte@hardness.com.br</small>
								</span>
							</p>
						</div>
						<div class="col-md-6">					
							<div style="position:relative; width:234px; height:60px;margin-left: 80px;">
							  <a href="http://get.teamviewer.com/9u92jsk" style="text-decoration:none;" target="_blank">
							    <img src="http://www.teamviewer.com/link/?url=232691&id=1250729433" alt="Baixe o TeamViewer QuickSupport" title="Baixe o TeamViewer QuickSupport" border="0" width="234" height="60" />
							    <span style="position:absolute; top:30.5px; left:60px; display:block; cursor:pointer; color:White; font-family:Arial; font-size:12px; line-height:1.2em; font-weight:bold; text-align:center; width:169px;">
							      Baixe o TeamViewer
							    </span>
							  </a>
							</div>
						</div>						    
					</div>
				</div>
				<br>
				<div class="row">
					<span style="text-align: left; float: left; margin-left: 130px; color: #000000; font-size: 20px;">
						<span style="font-size: 18px;">Envie sua mensagem</span>
					</span>
				</div>
				<div class="row" style="margin-top: 5px;">
					<div class="col-md-2" style="text-align: right;">
						<label class="control-label" for="assunto">*Assunto</label>
					</div>
					<div class="col-md-10">
						<input class="form-control" name="assunto" id="assunto" type="text" maxlength="80" />
					</div>
				</div>
				<div class="row" style="margin-top: 5px;">
					<div class="col-md-2" style="text-align: right;">
						<label class="control-label" for="email">*E-mail</label>
					</div>
					<div class="col-md-10">
						<input class="form-control" type="text" name="email" id="email" maxlength="100" />
					</div>
				</div>
				<div class="row" style="margin-top: 5px;">
					<div class="col-md-2" style="text-align: right;">
						<label class="control-label" for="mensagem">*Mensagem</label>
					</div>
					<div class="col-md-10">
						<textarea class="form-control" type="textarea" name="mensagem" id="mensagem" rows="8" style="text-transform: lowercase;"></textarea>
						<span style="color: red;">* Campos obrigatórios.</span>
					</div>
				</div>
				<div class="row" style="text-align: center;">
					<button type="submit" id="submit" class="btn btn-default btn-lg btn-primary pull-right" style="margin: 5px 20px;">Enviar</button><br />
				</div>
			</form>
		</div>
		<div class="col-xs-5">
			<div class="row">
				<div class="col-md-12" font-size: 18px;">
					<div class="page-header">
						<h3 style="color:#1F4C74">Localização</h1>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" style="text-align: center; font-weight: bold;">
					<p>
						<span style="font-size: 14px; color: #000000; font-weight: normal; text-align: left; margin: auto; width: 350px; display: block; margin-bottom: 5px;">
							Rua Cel. Dulcídio, 588 - Batel - OrbitCity Coworking - Sala 3<br>
							Curitiba - PR - 80420-170
						</span>
						<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3602.9802659300663!2d-49.28587408449964!3d-25.438923283784302!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94dce475ee27164d%3A0x27be4d1a832013f5!2sOrbitCity+Coworking!5e0!3m2!1sen!2sbr!4v1494609250896" width="350" height="350" frameborder="0" style="border:0" allowfullscreen></iframe>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
EOT;
// Footer
$footerJS = <<<EOT
	$("#formSuporte").submit(function() {
		$('#submit').attr('disabled', 'disabled').html('Enviando...');
		$.ajax({
			url: '/contato/',
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