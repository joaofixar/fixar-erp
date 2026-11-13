<?php
namespace hardness;
global $g;
echo "\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "**\n";
echo "**                     E T A P A  -  4\n";
echo "** \n";
echo "**                     Enviar E-Mail \n";
echo "**\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "\n";
echo "\n Entrou na etapa 4\n";
echo "\nEnvio da Danfe por email como:{$g['C031']['NFeEnviarEmailAutomatico']}\n";
echo "\nEnvio dos Boletos por email como:{$g['C031']['BoletosEnviarEmailAutomatico']}\n";
echo "\nQ001_C007_Id:{$Q001['Q001_C007_Id']}\n";

// Esta opção tem prioridade sobre os dois envios individuais.
$enviarDanfeBoletoMesmoEmail = isset($g['C031']['EnviarDanfeBoletoMesmoEmail'])
    && $g['C031']['EnviarDanfeBoletoMesmoEmail'] == 'S';

$logTxt = "\r\n Etapa 4 - Enviar E-mail.\r\n";
$r = fwrite($h, $logTxt);

$logTxt = "Envio da Danfe por email como:{$g['C031']['NFeEnviarEmailAutomatico']}.\r\n";
$r = fwrite($h, $logTxt);

$logTxt = "Envio dos Boletos por email como:{$g['C031']['BoletosEnviarEmailAutomatico']}.\r\n";
$r = fwrite($h, $logTxt);

$logTxt = "Id C007 - {$Q001['Q001_C007_Id']}.\r\n";
$r = fwrite($h, $logTxt);

if (empty($Q001['Q001_C007_Id'])) {
	echo "\n NÃO ENCONTROU Q001_C007_Id.\n";
	$logTxt = "NÃO ENCONTROU Q001_C007_Id.\r\n";
	$r = fwrite($h, $logTxt);
	return false;
}

$D024 = mysql_query("SELECT D024_E_Mail, 
							D024_E_Mail_Nota,
							D024_E_Mail_Boleto 
					   FROM T007 
				  LEFT JOIN D024 ON D024_Id = T007_D024_Id 
				      WHERE T007_Id = '{$Q001['Q001_T007_Id']}'");
$mD024 = mysql_fetch_assoc($D024);

$clienteComEmail = 'S';
if (empty($mD024['D024_E_Mail']) && empty($mD024['D024_E_Mail_Nota']) && empty($mD024['D024_E_Mail_Boleto'])) {
	$clienteComEmail = 'N';
	$logTxt = "Não tem email cadastrado no cliente\r\n";
	$r = fwrite($h, $logTxt);

	echo " \n\nNão tem email cadastrado no client\n";
	return false;
}

echo "**************************************************************************************\n\n";
if (($enviarDanfeBoletoMesmoEmail || $g['C031']['NFeEnviarEmailAutomatico']=='S') && $clienteComEmail == 'S'){
	msg_erro_nfe(13);
	echo " \n\nEntrou no NFe Enviar email automatico\n";
	$logTxt = "Entrou no NFe Enviar email automatico.\r\n";
	$r = fwrite($h, $logTxt);

	$C007 = mysqli_query("SELECT C007_Email_SMTP_Servidor,
							     C007_Email_Assinatura,
							     C007_Email,
							     C007_Primeiro_Nome,
							     C007_Email_SMTP_Autenticacao,
							     C007_Email_SMTP_Usuario,
							     C007_Email_SMTP_Senha,
							     C007_Email_SMTP_Porta,
								 C007_Email_SMTP_Seguranca
 							FROM C007 
						   WHERE C007_Id = '{$Q001['Q001_C007_Id']}';");
	$C007 = mysql_fetch_assoc($C007);
	if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
		$logTxt = "Não encontrou o C007 ou o C007_Email_SMTP_Servidor\r\n";
		$r = fwrite($h, $logTxt);

		echo " \n\nNão encontrou o C007 ou o C007_Email_SMTP_Servidor\n";
		return false;
	}

	$VEN003 = new \hardness\VEN003();

	$logTxt = "Montando email VEN003->montarEmail\r\n";
	$r = fwrite($h, $logTxt);
	
	$monta_ = $VEN003->montarEmail($Q001['Q001_T007_Id']);
	if(!is_array($monta_)){
		echo " \n\nNão conseguiu montar o email na classe VEN003->montarEmail passando o T007_Id:{$Q001['Q001_T007_Id']}. Retorno função: {$monta_} \n";
		$logTxt = "Não conseguiu montar o email na classe VEN003->montarEmail passando o T007_Id:{$Q001['Q001_T007_Id']}. Retorno função: {$monta_}.\r\n";
		$r = fwrite($h, $logTxt);
		return false;
	}
	$boletosAnexadosNoMesmoEmail = false;
	if ($enviarDanfeBoletoMesmoEmail) {
		$sqlBoletos = mysql_query("SELECT T002_T117_Id FROM T002 WHERE T002_T007_Id = '{$Q001['Q001_T007_Id']}' AND IFNULL(T002_T117_Id, 0) > 0");
		if (mysql_num_rows($sqlBoletos) > 0) {
			chdir($home);
			$montaBoletos = $VEN003->montarEmailBoletos($Q001['Q001_T007_Id']);
			chdir($home);

			if (!is_array($montaBoletos) || empty($montaBoletos['Anexos'])) {
				echo " \n\nNão foi possível montar os boletos para o e-mail único.\n";
				$logTxt = "Não foi possível montar os boletos para o e-mail único.\r\n";
				$r = fwrite($h, $logTxt);
				return false;
			}

			$emailsDestinatarios = array();
			$emailNota = !empty($mD024['D024_E_Mail_Nota']) ? $mD024['D024_E_Mail_Nota'] : $mD024['D024_E_Mail'];
			$emailBoleto = !empty($mD024['D024_E_Mail_Boleto']) ? $mD024['D024_E_Mail_Boleto'] : $mD024['D024_E_Mail'];
			foreach (array($emailNota, $emailBoleto) as $email) {
				if (!empty($email)) {
					$emailsDestinatarios[] = $email;
				}
			}

			$monta_['Para'] = implode('; ', array_unique($emailsDestinatarios));
			$monta_['Assunto'] .= ' - Boletos';
			$monta_['Corpo'] .= '<br /><br /><hr /><br />' . $montaBoletos['Corpo'];
			$monta_['Anexos'] = array_merge($monta_['Anexos'], $montaBoletos['Anexos']);
			$boletosAnexadosNoMesmoEmail = true;
		}
	}

	if (empty($monta_['Para'])) {
		echo " \n\nNão conseguiu identificar o Email do Cliente\n";
		$logTxt = "Não conseguiu identificar o Email do Cliente.\r\n";
		$r = fwrite($h, $logTxt);
		return false;
	}
	if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
		$monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
	}

	echo " \n\nPreechendo os atributos do objeto email com os dados de envio\n";
	$logTxt = "Preechendo os atributos do objeto email com os dados de envio.\r\n";
	$r = fwrite($h, $logTxt);

	//Email padrao para NFe
	if (!empty($g['C031']['NFe_Email'])) {
		$C007['C007_Email'] = $g['C031']['NFe_Email'];
		$C007['C007_Primeiro_Nome'] = "NFe";
		$C007['C007_Email_SMTP_Senha'] = $g['C031']['NFe_Email_SMTP_Senha'];
		$C007['C007_Email_SMTP_Servidor'] = $g['C031']['NFe_Email_SMTP_Servidor'];
		$C007['C007_Email_SMTP_Autenticacao'] = $g['C031']['NFe_Email_SMTP_Autenticacao'];
		$C007['C007_Email_SMTP_Usuario'] = $g['C031']['NFe_Email_SMTP_Usuario'];
		$C007['C007_Email_SMTP_Porta'] = $g['C031']['NFe_Email_SMTP_Porta'];
	}

	$mail = new \phpmailer;
	// Mail
	$mail->From     = $C007['C007_Email'];
	$mail->FromName = $C007['C007_Primeiro_Nome'];
	$mail->Host     = $C007['C007_Email_SMTP_Servidor'];
	$mail->Mailer   = "smtp";
	$mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
	$mail->Username = $C007['C007_Email_SMTP_Usuario'];
	$mail->Password = $C007['C007_Email_SMTP_Senha'];
	$mail->Subject	= '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
	$mail->Body     = $monta_['Corpo'];
	$mail->AltBody  = "ALT BODY";
	//$mail->CharSet  = 'iso-8859-1';
	$mail->CharSet  = 'utf-8';
	$mail->SetLanguage("br");
	//$mail->ConfirmReadingTo = $C007['C007_Email'];
	$mail->IsHTML(true);

	$logTxt = "C007_Email_SMTP_Servidor: {$C007['C007_Email_SMTP_Servidor']} \r\n";
	$r = fwrite($h, $logTxt);

	$logTxt = "C007_Email_SMTP_Porta: {$C007['C007_Email_SMTP_Porta']} \r\n";
	$r = fwrite($h, $logTxt);

	$logTxt = "C007_Email_SMTP_Usuario: {$C007['C007_Email_SMTP_Usuario']} \r\n";
	$r = fwrite($h, $logTxt);
	
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
	{
		$mail->SMTPSecure   = "ssl";
		$mail->Port         = "465"; 
	}
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
	{
		$mail->SMTPSecure   = "ssl";
	}
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
	{
		$mail->SMTPSecure   = "tls";
		$mail->SMTPSecurity = 'tls';
	}	
	if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false)
	{
		$mail->SMTPSecure   = "tls";
	}
	if (strpos($C007['C007_Email_SMTP_Servidor'],"mail.nacomaq.com.br") !== false)
	{
		$mail->SMTPSecure   = "ssl";
		$mail->Port         = "465";
	}
	if (!empty($C007['C007_Email_SMTP_Porta']))
	{
		$mail->Port         = $C007['C007_Email_SMTP_Porta']; 
	}

	if($C007['C007_Email_SMTP_Seguranca'] == '1'){
		$mail->SMTPSecure   = "ssl";
		$mail->SMTPSecurity = "ssl";
	}elseif($C007['C007_Email_SMTP_Seguranca'] == '2'){
		$mail->SMTPSecure   = "tls";
		$mail->SMTPSecurity = "tls";
	}

	if (empty($monta_['Para']) && !empty($monta_['cc'])) {
		$monta_['Para'] = $monta_['cc'];
		$monta_['cc'] = '';
	}
	$todosEmailsEnviados = array();
	$mPara=preg_split("/[;,]+/",$monta_['Para']);
	for($x=0;$x<count($mPara);$x++)
	{
		$email = $mPara[$x];
		if (strpos($email, '<') !== false) {
			$email = explode('<', $email);
			$email = explode('>', $email[1]);
			$email = $email[0];
		}
		$email = trim($email);
		$emailNormalizado = strtolower($email);
		if (empty($email) || in_array($emailNormalizado, $todosEmailsEnviados)) {
			continue;
		}
		$todosEmailsEnviados[] = $emailNormalizado;
		$mail->AddAddress($email);
	}
	if (isset($monta_['cc'])) {
		$mPara=preg_split("/[;,]+/",$monta_['cc']);
		for($x=0;$x<count($mPara);$x++)
		{
			$email = $mPara[$x];
			if (strpos($email, '<') !== false) {
				$email = explode('<', $email);
				$email = explode('>', $email[1]);
				$email = $email[0];
			}
			$email = trim($email);
			$emailNormalizado = strtolower($email);
			if (empty($email) || in_array($emailNormalizado, $todosEmailsEnviados)) {
				continue;
			}
			$todosEmailsEnviados[] = $emailNormalizado;
			$mail->AddCC($email);
		}
	}

	$anexosGravar = array();
	$T139_Estrutura = array('anexosManual' => array());
	if (isset($monta_['Anexos'])) {
		foreach ($monta_['Anexos'] as $path) {
			$anexosGravar[] = $path;
			$T139_Estrutura['anexosManual'][] = basename($path);
			$mail->AddAttachment($path,basename($path));
		}
	}
	echo " \n\nEntrando no envio do email com o Send()\n";
	$logTxt = "Entrando no envio do email com o Send().\r\n";
	$r = fwrite($h, $logTxt);

	if($mail->Send())
	{
		mysqli_query("update T007 set T007_Data_Envio_XML=now() where T007_Id='{$Q001['Q001_T007_Id']}'");
		if ($boletosAnexadosNoMesmoEmail) {
			mysqli_query("update T007 set T007_Data_Envio_Email_Boleto=now() where T007_Id='{$Q001['Q001_T007_Id']}'");
			$retorno = $VEN003->atualizaDataEnvioBoleto($Q001['Q001_T007_Id']);
			echo "atualizaDataEnvioBoleto: \n{$retorno}\n\n";
		}
	     
		// Gravar como e-mail enviado
		$T139_De = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
		$T139_Estrutura = serialize($T139_Estrutura);
		$T139_Para = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
		$sql = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id,T139_T007_Id) VALUES ('S', 0, '".mysql_real_escape_string($monta_['Assunto'])."', '{$T139_De}', '".mysql_real_escape_string($T139_Para)."', '".date('Y-m-d H:i:s')."', '" . mysql_real_escape_string($T139_Estrutura) . "', '".mysql_real_escape_string($monta_['Corpo'])."', '1', '".mysql_real_escape_string($mT007['T007_D024_Id'])."', '".mysql_real_escape_string($g['usuarioAtual'])."', '".mysql_real_escape_string($Q001['Q001_T007_Id'])."');";
		mysqli_query($sql);
		$g['mysqlLastId'] = mysql_insert_id();
		
		// Salvar anexos
		$dir = $g['pathDados'] . 'anexosEmail/';
		is_dir($dir) or mkdir($dir, 0777, true);
		foreach ($anexosGravar as $key => $anexo) {
			$arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
			copy($anexo, $arquivo); 
		}

		if (isset($monta_['Anexos'])) {
			foreach ($monta_['Anexos'] as $path) {
				gDeletarArquivo($path);
			}
		}
		
		// Associar e-mail
		/*
		$sql_ = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
		$resultado_ = mysqli_query($sql_);
		$C007_ = mysql_fetch_assoc($resultado_);
		$comunicacaoEmail_ = new comunicacaoEmail($C007_['C007_Email_POP_Servidor'], $C007_['C007_Email_SMTP_Usuario'], $C007_['C007_Email_SMTP_Senha'], false, false, $C007_['C007_Email_POP_Porta']);
		$comunicacaoEmail_->associarEmails($g['mysqlLastId']);
		*/
		echo "\nEnvio do email com DANFE realizado com sucesso para o T007_Id:{$Q001['Q001_T007_Id']}.\n";
		$logTxt = "Envio do email com DANFE realizado com sucesso para o T007_Id:{$Q001['Q001_T007_Id']}\r\n";
		$r = fwrite($h, $logTxt);
	}
	else
	{
		$cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
		$cErro=str_replace(array("<p>","</p>"),"<br /><br />",$cErro);
		echo "\nENVIO DANFE ETAPA 4 T007_Id:{$Q001['Q001_T007_Id']}\nNão foi possível enviar o e-mail!De: {$C007['C007_Email']}\nPara: {$r_emailPara}\nSMTP: {$C007['C007_Email_SMTP_Servidor']}\nSMTP Secure: {$mail->SMTPSecure}\nPORT: {$mail->Port}\nERRO:<br />{$cErro}\n";
		$logTxt = "ENVIO DANFE ETAPA 4 T007_Id:{$Q001['Q001_T007_Id']}\nNão foi possível enviar o e-mail!De: {$C007['C007_Email']}\nPara: {$r_emailPara}\nSMTP: {$C007['C007_Email_SMTP_Servidor']}\nSMTP Secure: {$mail->SMTPSecure}\nPORT: {$mail->Port}\nERRO:<br />{$cErro}\r\n";
		$r = fwrite($h, $logTxt);
	}
}else{
	echo "\nGlobal NFeEnviarEmailAutomatico não configurada para SIM. Valor Global:{$g['C031']['NFeEnviarEmailAutomatico']}.\n";
	$logTxt = "Global NFeEnviarEmailAutomatico não configurada para SIM. Valor Global:{$g['C031']['NFeEnviarEmailAutomatico']}.\r\n";
	$r = fwrite($h, $logTxt);
}

echo "**************************************************************************************\n\n";
if (!$enviarDanfeBoletoMesmoEmail && $g['C031']['BoletosEnviarEmailAutomatico']=='S' && $clienteComEmail == 'S'){
	echo " \n\nEntrou no Boletos Enviar email automatico\n";
	$logTxt = "Entrou no Boletos Enviar email automatico.\r\n";
	$r = fwrite($h, $logTxt);

	$C007 = mysqli_query("SELECT C007_Email_SMTP_Servidor,
							     C007_Email_Assinatura,
							     C007_Email,
							     C007_Primeiro_Nome,
							     C007_Email_SMTP_Autenticacao,
							     C007_Email_SMTP_Usuario,
							     C007_Email_SMTP_Senha,
							     C007_Email_SMTP_Porta,
								 C007_Email_SMTP_Seguranca
 							FROM C007 
						   WHERE C007_Id = '{$Q001['Q001_C007_Id']}';");
	$C007 = mysql_fetch_assoc($C007);
	if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
		echo " \n\nNão encontrou o C007 ou o C007_Email_SMTP_Servidor T007_Id:".$Q001['Q001_T007_Id']."   C007_Id:".$Q001['Q001_C007_Id']."\n";
		$logTxt = "Não encontrou o C007 ou o C007_Email_SMTP_Servidor T007_Id:".$Q001['Q001_T007_Id']."   C007_Id:".$Q001['Q001_C007_Id']."\r\n";
		$r = fwrite($h, $logTxt);
		return false;
	}

	$VEN003 = new \hardness\VEN003();

	$logTxt = "Montando email boletos VEN003->montaremailboletos - Diretório: {$home}\r\n";
	$r = fwrite($h, $logTxt);

	chdir($home);
	$monta_ = $VEN003->montarEmailBoletos($Q001['Q001_T007_Id']);
	chdir($home);

	$logTxt = "Voltando para diretório: {$home}\r\n";
	$r = fwrite($h, $logTxt);

	if(!is_array($monta_)){
		echo " \n\nNão conseguiu montar o email na classe VEN003->montarEmailBoletos passando o T007_Id:{$Q001['Q001_T007_Id']}. Retorno: {$monta_}\n";
		$logTxt = "Não conseguiu montar o email na classe VEN003->montarEmailBoletos passando o T007_Id:{$Q001['Q001_T007_Id']}. Retorno: {$monta_}\r\n";
		$r = fwrite($h, $logTxt);
		return false;
	}
	if (empty($monta_['Para'])) {
		echo " \n\nNão preencher o email do Cliente.\n";
		$logTxt = "Não preencher o email do Cliente.\r\n";
		$r = fwrite($h, $logTxt);
		return false;
	}
	if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
		$monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
	}

	msg_erro_nfe(14);
	echo " \n\nPreechendo os atributos do objeto email com os dados de envio\n";
	$logTxt = "Preechendo os atributos do objeto email com os dados de envio.\r\n";
	$r = fwrite($h, $logTxt);

	//Email padrao para Boleto
	if (!empty($g['C031']['Boleto_Email'])) {
		$C007['C007_Email'] = $g['C031']['Boleto_Email'];
		$C007['C007_Email_SMTP_Senha'] = $g['C031']['Boleto_Email_SMTP_Senha'];
		$C007['C007_Email_SMTP_Servidor'] = $g['C031']['Boleto_Email_SMTP_Servidor'];
		$C007['C007_Email_SMTP_Autenticacao'] = $g['C031']['Boleto_Email_SMTP_Autenticacao'];
		$C007['C007_Email_SMTP_Usuario'] = $g['C031']['Boleto_Email_SMTP_Usuario'];
		$C007['C007_Email_SMTP_Porta'] = $g['C031']['Boleto_Email_SMTP_Porta'];
	}

	$mail = new \phpmailer;
	// Mail
	$mail->From     = $C007['C007_Email'];
	$mail->FromName = $C007['C007_Primeiro_Nome'];
	$mail->Host     = $C007['C007_Email_SMTP_Servidor'];
	$mail->Mailer   = "smtp";
	$mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
	$mail->Username = $C007['C007_Email_SMTP_Usuario'];
	$mail->Password = $C007['C007_Email_SMTP_Senha'];
	$mail->Subject	= '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
	$mail->Body     = $monta_['Corpo'];
	$mail->AltBody  = "ALT BODY";
	//$mail->CharSet  = 'iso-8859-1';
	$mail->CharSet  = 'utf-8';
	$mail->SetLanguage("br");
	//$mail->ConfirmReadingTo = $C007['C007_Email'];
	$mail->IsHTML(true);

	$logTxt = "C007_Email_SMTP_Servidor: {$C007['C007_Email_SMTP_Servidor']} \r\n";
	$r = fwrite($h, $logTxt);

	$logTxt = "C007_Email_SMTP_Porta: {$C007['C007_Email_SMTP_Porta']} \r\n";
	$r = fwrite($h, $logTxt);

	$logTxt = "C007_Email_SMTP_Usuario: {$C007['C007_Email_SMTP_Usuario']} \r\n";
	$r = fwrite($h, $logTxt);
	
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
	{
		$mail->SMTPSecure   = "ssl";
		$mail->Port         = "465"; 
	}
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
	{
		$mail->SMTPSecure   = "ssl";
	}
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
	{
		$mail->SMTPSecure   = "tls";
		$mail->SMTPSecurity = 'tls';
	}	
	if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false){
		$mail->SMTPSecure   = "tls";
	}
	if (strpos($C007['C007_Email_SMTP_Servidor'],"mail.nacomaq.com.br") !== false)
	{
		$mail->SMTPSecure   = "ssl";
		$mail->Port         = "465";
	}
	if (!empty($C007['C007_Email_SMTP_Porta']))
	{
		$mail->Port         = $C007['C007_Email_SMTP_Porta']; 
	}

	if($C007['C007_Email_SMTP_Seguranca'] == '1'){
		$mail->SMTPSecure   = "ssl";
		$mail->SMTPSecurity = "ssl";
	}elseif($C007['C007_Email_SMTP_Seguranca'] == '2'){
		$mail->SMTPSecure   = "tls";
		$mail->SMTPSecurity = "tls";
	}


	if (empty($monta_['Para']) && !empty($monta_['cc'])) {
		$monta_['Para'] = $monta_['cc'];
		$monta_['cc'] = '';
	}
	$todosEmailsEnviados = array();
	$mPara=preg_split("/[;,]+/",$monta_['Para']);
	for($x=0;$x<count($mPara);$x++)
	{
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
		$mPara=preg_split("/[;,]+/",$monta_['cc']);
		for($x=0;$x<count($mPara);$x++)
		{
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
	$T139_Estrutura = array('anexosManual' => array());
	if (isset($monta_['Anexos'])) {
		foreach ($monta_['Anexos'] as $path) {
			$anexosGravar[] = $path;
			$T139_Estrutura['anexosManual'][] = basename($path);
			$mail->AddAttachment($path,basename($path));
		}
	}

	echo " \n\nEntrando no envio do email com o Send()\n";
	$logTxt = "Entrando no envio do email com o Send()\r\n";
	$r = fwrite($h, $logTxt);

	if($mail->Send())
	{
		mysqli_query("update T007 set T007_Data_Envio_Email_Boleto=now() where T007_Id='{$Q001['Q001_T007_Id']}'");

		$retorno = $VEN003->atualizaDataEnvioBoleto($Q001['Q001_T007_Id']);
		echo "atualizaDataEnvioBoleto: \n{$retorno}\n\n";
	    
		// Gravar como e-mail enviado
		$T139_De = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
		$T139_Estrutura = serialize($T139_Estrutura);
		$T139_Para = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
		$sql = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id,T139_T007_Id) VALUES ('S', 0, '".mysql_real_escape_string($monta_['Assunto'])."', '{$T139_De}', '".mysql_real_escape_string($T139_Para)."', '".date('Y-m-d H:i:s')."', '" . mysql_real_escape_string($T139_Estrutura) . "', '".mysql_real_escape_string($monta_['Corpo'])."', '1', '".mysql_real_escape_string($mT007['T007_D024_Id'])."', '".mysql_real_escape_string($g['usuarioAtual'])."', '".mysql_real_escape_string($Q001['Q001_T007_Id'])."');";
		mysqli_query($sql);
		$g['mysqlLastId'] = mysql_insert_id();
		
		// Salvar anexos
		$dir = $g['pathDados'] . 'anexosEmail/';
		is_dir($dir) or mkdir($dir, 0777, true);
		foreach ($anexosGravar as $key => $anexo) {
			$arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
			copy($anexo, $arquivo);
		}

		if (isset($monta_['Anexos'])) {
			foreach ($monta_['Anexos'] as $path) {
				gDeletarArquivo($path);
			}
		}
		
		// Associar e-mail
		/*
		$sql_ = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
		$resultado_ = mysqli_query($sql_);
		$C007_ = mysql_fetch_assoc($resultado_);
		$comunicacaoEmail_ = new comunicacaoEmail($C007_['C007_Email_POP_Servidor'], $C007_['C007_Email_SMTP_Usuario'], $C007_['C007_Email_SMTP_Senha'], false, false, $C007_['C007_Email_POP_Porta']);
		$comunicacaoEmail_->associarEmails($g['mysqlLastId']);
		*/
		echo "\nEnvio do email com boletos realizado com sucesso para o T007_Id:{$Q001['Q001_T007_Id']}.\n";
		$logTxt = "Envio do email com boletos realizado com sucesso para o T007_Id:{$Q001['Q001_T007_Id']}.\r\n";
		$r = fwrite($h, $logTxt);
	}
	else
	{
		$cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
		$cErro=str_replace(array("<p>","</p>"),"<br /><br />",$cErro);
		echo "\nENVIO BOLETOS ETAPA 4 T007_Id:{$Q001['Q001_T007_Id']}\nNão foi possível enviar o e-mail!De: {$C007['C007_Email']}\nPara: {$r_emailPara}\nSMTP: {$C007['C007_Email_SMTP_Servidor']}\nSMTP Secure: {$mail->SMTPSecure}\nPORT: {$mail->Port}\nERRO:<br />{$cErro}\n";
		$logTxt = "ENVIO BOLETOS ETAPA 4 T007_Id:{$Q001['Q001_T007_Id']}\nNão foi possível enviar o e-mail!De: {$C007['C007_Email']}\nPara: {$r_emailPara}\nSMTP: {$C007['C007_Email_SMTP_Servidor']}\nSMTP Secure: {$mail->SMTPSecure}\nPORT: {$mail->Port}\nERRO:<br />{$cErro}.\r\n";
		$r = fwrite($h, $logTxt);
	}
}else{
	echo "\nGlobal BoletosEnviarEmailAutomatico não configurada para SIM. Valor Global:{$g['C031']['BoletosEnviarEmailAutomatico']}.\n";
	$logTxt = "Global BoletosEnviarEmailAutomatico não configurada para SIM. Valor Global:{$g['C031']['BoletosEnviarEmailAutomatico']}.\r\n";
	$r = fwrite($h, $logTxt);
}
