<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

include('mobile/tablet/inc.header.php');

if (!empty($T005_Id)) {
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();
	$formatopedido = $g['C031']['formatoPedidoEmail'];
	if ($formatopedido == 'html') {
		// html
			$monta = $VEN001->montarEmail($T005_Id);
	} else if ($formatopedido == 'pdf') {
		// pdf
			$monta = $VEN001->montarEmailPdf($T005_Id);
	} else {
		// ambos
			$monta = $VEN001->montarEmail($T005_Id);
			$montaTmp = $VEN001->montarEmailPdf($T005_Id);
			$monta['Anexos'] = $montaTmp['Anexos'];
			$monta['AnexosWeb'] = $montaTmp['AnexosWeb'];
	}
	// (todo) utilizar um editor HTML e utilizar o corpo do vindo do metodo
	$monta['Corpo'] = "Olá,\nSegue Pedido {$T005_Id} em anexo.\n";
}

if (isset($_REQUEST['D024_E_Mail'])) {
	//require("bibliotecas/phpmailer/phpmailer.inc.php");
	require("bibliotecas/phpmailer/class.phpmailer.php");
	$mail = new \phpmailer;
	// Mail
	$r_emailAssunto = $_REQUEST['Assunto'];
	$r_emailMensagem = $_REQUEST['corpoEmail']; 
	$r_D024_Id = $_REQUEST['D024_Id'];
	$r_T005_Id = $_REQUEST['T005_Id'];
	$r_emailPara = $_REQUEST['D024_E_Mail'];
	$r_D013_Id = '';
	$r_T011_Id = '';
	$r_emailParaCc = '';
	$r_emailParaCco = '';
	$r_T075_Id = '';
	$r_T007_Id = '';
	$r_T173_Id = '';
	
	$mail->From     = $C007['C007_Email'];
	$mail->FromName = $C007['C007_Primeiro_Nome'];
	$mail->Host     = $C007['C007_Email_SMTP_Servidor'];
	$mail->Mailer   = "smtp";
	$mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
	$mail->Username = $C007['C007_Email_SMTP_Usuario'];
	$mail->Password = $C007['C007_Email_SMTP_Senha'];
	$mail->Subject	= $r_emailAssunto;
	$mail->Body     = $r_emailMensagem;
	$mail->AltBody  = "ALT BODY";
	//$mail->CharSet  = 'iso-8859-1';
	$mail->CharSet  = 'utf-8';
	$mail->SetLanguage("br");
	$mail->ConfirmReadingTo = $C007['C007_Email'];
	
	
	$mail->IsHTML(true);
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
	{
		$mail->SMTPSecure   = "ssl";
		$mail->Port         = "465"; 
	}
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
	{
		$mail->SMTPSecure   = "ssl";
	}
	if (!empty($C007['C007_Email_SMTP_Porta']))
	{
		$mail->Port         = $C007['C007_Email_SMTP_Porta']; 
	}
	$mPara=preg_split("/[;,]+/",$r_emailPara);
	for($x=0;$x<count($mPara);$x++)
	{
		$email = $mPara[$x];
		if (strpos($email, '<') !== false) {
			$email = explode('<', $email);
			$email = explode('>', $email[1]);
			$email = $email[0];
		}
		$mail->AddAddress($email);
	}
	/*
	$mPara=preg_split("/[;,]+/",$r_emailParaCc);
	for($x=0;$x<count($mPara);$x++)
	{
		$email = $mPara[$x];
		if (strpos($email, '<') !== false) {
			$email = explode('<', $email);
			$email = explode('>', $email[1]);
			$email = $email[0];
		}
		$mail->AddCC($email);
	}
	$mPara=preg_split("/[;,]+/",$r_emailParaCco);
	for($x=0;$x<count($mPara);$x++)
	{
		$email = $mPara[$x];
		if (strpos($email, '<') !== false) {
			$email = explode('<', $email);
			$email = explode('>', $email[1]);
			$email = $email[0];
		}
		$mail->AddBCC($email);
	}*/
	if (isset($monta['Anexos'])) {
		foreach ($monta['Anexos'] as $path) {
			$mail->AddAttachment($path,basename($path));
		}
	}
	
	if($mail->Send())
	{
		/*if(!empty($r_T117_Id)){
			mysql_query("update T117 set T117_Data_Envio_Email=now() where T117_Id='{$r_T117_Id}'");
		}*/
		// Gravar como e-mail enviado
		$T139_De = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
		$sql = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_D013_Id, T139_T005_Id, T139_D117_Id, T139_C007_Id, T139_Valor_Total, T139_Numero_Registros, T139_T117_Id,T139_T005_Id,T139_T011_Id, T139_Cc, T139_Cco,T139_T075_Id,T139_T007_Id,T139_T137_Id) VALUES ('S', 0, '".mysql_real_escape_string($r_emailAssunto)."', '{$T139_De}', '".mysql_real_escape_string($r_emailPara)."', '".date('Y-m-d H:i:s')."', 'b:0;', '".mysql_real_escape_string($r_emailMensagem)."', '1', '".mysql_real_escape_string($r_D024_Id)."', '".mysql_real_escape_string($r_D013_Id)."', '".mysql_real_escape_string($r_T005_Id)."', '".mysql_real_escape_string($r_D117_Id)."', '".mysql_real_escape_string($g['usuarioAtual'])."', '".mysql_real_escape_string($r_T139_Valor_Total)."', '".mysql_real_escape_string($r_T139_Numero_Registros)."', '".mysql_real_escape_string($r_T117_Id)."', '".mysql_real_escape_string($r_T005_Id)."', '".mysql_real_escape_string($r_T011_Id)."', '".mysql_real_escape_string($r_emailParaCc)."', '".mysql_real_escape_string($r_emailParaCco)."', '".mysql_real_escape_string($r_T075_Id)."', '".mysql_real_escape_string($r_T007_Id)."', '".mysql_real_escape_string($r_T173_Id)."');";
		mysql_query($sql);
		// Mensagem
		$script = "";
		$script = <<<EOT
			alert('E-mail enviado com sucesso! De: {$C007['C007_Email']} - Para: {$r_emailPara}');
  			$.mobile.changePage( "./pedido.php?T005_Id={$T005_Id}", { transition: 'fade'} );
EOT;
		/* Callbacks 
		// Via Controle Interno
		if (!empty($r_ETC013OS)) {
			$EST013->montarEmailCallback($r_ETC013OS, array('Para' => $r_emailPara));
		}
		*/
	}
	else
	{
		$cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
		$cErro=str_replace(array("<p>","</p>")," - ",$cErro);
		$script = "alert('Não foi possível enviar o e-mail! - De: {$C007['C007_Email']} Para: {$r_emailPara} SMTP: {$C007['C007_Email_SMTP_Servidor']} - SMTP Secure: {$mail->SMTPSecure} PORT: {$mail->Port} ERRO: {$cErro}')";
	}
}

$nomeAnexo = explode('/', $monta['Anexos'][0]);
$nomeAnexo = $nomeAnexo[count($nomeAnexo)-1];

echo <<<EOT
	<div data-role="page" id="pagepedidoEnviarEmail">
EOT;
//print_r($monta);
$etapa = 2;
include('mobile/tablet/inc.header.bar.php');
include('inc.header.page.php');
echo <<<EOT
		<div data-role="content">
       		<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
				<h3>Enviar e-mail</h3>
				<form action="pedidoEnviarEmail.php?T005_Id={$T005_Id}" method="POST" data-ajax="false">
					<div>
						<div>
							<div data-role="fieldcontain" style="padding: 4px 8px;">
								<label for="C007_Email">De:</label>
								<input placeholder="" type="text" id="C007_Email" style="opacity: 0.5; text-transform:none;" disabled="disabled" name="C007_Email" value="{$C007['C007_Email']}">
							</div>
						</div>
					</div>
					 <div>
						<div>
							<div data-role="fieldcontain" style="padding: 4px 8px;">
								<label for="D024_E_Mail">Para:</label>
								<input placeholder="" type="text" id="D024_E_Mail" name="D024_E_Mail" style="text-transform:none;" value="{$monta['Para']}">
							</div>
						</div>
					</div>
					<div>
						<div>
							<div data-role="fieldcontain" style="padding: 4px 8px;">
								<label for="D024_E_Mail">Assunto:</label>
								<input placeholder="" type="text" id="Assunto" name="Assunto" style="text-transform:none;" value="{$monta['Assunto']}">
							</div>
						</div>
					</div>
					<div>
						<div>
							<div data-role="fieldcontain" style="padding: 4px 8px;">
								<label for="D024_E_Mail">Anexos:</label>
								<input placeholder="" type="text" id="AnexoTxt" name="AnexoTxt" style="opacity: 0.5; text-transform:none;" disabled="disabled" value="{$nomeAnexo}">
								<input placeholder="" type="hidden" id="Anexo" name="Anexo" value="{$monta['Anexos'][0]}">
								<input type="hidden" id="D024_Id" name="D024_Id" value="{$monta['D024_Id']}">
							</div>
						</div>
					</div>
					<div>
						<div style="margin-top: 13px;">
							<label for="corpoEmail"></label>
							<textarea name="corpoEmail" id="corpoEmail">{$monta['Corpo']}</textarea>
						</div>
					</div>
					<div>
						<div>
								<input value="Enviar" type="submit" data-inline="true" data-icon="check">
						        <a data-inline="true" data-role="button" data-transition="fade" data-icon="delete" data-iconpos="left" href="./clientes.php?T005_Id={$T005_Id}" data-ajax="false">
			            			Cancelar
						        </a>
						</div>
					</div>
				</form>
			</div>
		</div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pagepedidoEnviarEmail", "pageinit");
    	$( document ).delegate("#pagepedidoEnviarEmail", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
