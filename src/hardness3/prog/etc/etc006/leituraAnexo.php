<?php
namespace hardness;

switch($g['r'][3]){
	case 'leitor':
		// background: center no-repeat url(/hardness3/static/img/loading.gif)
		if(isset($r_extensao) && ($r_extensao == '.xls' || $r_extensao == 'xlsx' || $r_extensao == '.doc' || $r_extensao == 'docx')){
			$r_nomeArquivo = str_replace(array(' '), '_', $r_nomeArquivo);
			$dir = $g['pathDados'] . "anexosEmail/{$r_acaoId}/";
			is_dir($dir) or mkdir($dir, 0777, true);
			$arquivo = $dir . $r_nomeArquivo;

			//Caso o arquivo não tenha sido baixado, realizamos o download pro servidor
			if(!file_exists($arquivo)){
				ob_start();
				$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$r_C007_Id}';";
				$resultado = mysql_query($sql);
				$C007 = mysql_fetch_assoc($resultado);
				// Pega infos
				require_once('sistema/comunicacaoEmail.php');
				$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
				$mail->downloadAnexo($r_acaoId, $r_file);
				$conteudoArquivo = ob_get_contents();
				ob_end_clean();

				file_put_contents($arquivo, $conteudoArquivo);
			}
			$urlArquivo = $confUsuario['urlRaiz'].'/hardness3/'.$arquivo;

			//echo "<iframe align=\"middle\" src=\"https://sheet.zoho.com/sheet/view.do?url=&name={$r_nomeArquivo}\" style=\"border: 0px; width: 100%; height: 100%;\">";
			echo "<iframe src=\"http://docs.google.com/viewer?embedded=true&url={$urlArquivo}\" style=\"width:100%; height:100%;\" frameborder=\"0\"></iframe>";
		} else if(isset($r_extensao) && $r_extensao == '.txt'){
			echo "<iframe align=\"middle\" src=\"/etc/etc006/leituraAnexo/iframeLeitor/?ajax=true&acaoId={$r_acaoId}&file={$r_file}&C007_Id={$r_C007_Id}\" style=\"border: 0px; width: 100%; height: 100%; background-color: #FFF;\">";
		} else {
			echo "<iframe align=\"middle\" src=\"/etc/etc006/leituraAnexo/iframeLeitor/?ajax=true&acaoId={$r_acaoId}&file={$r_file}&C007_Id={$r_C007_Id}\" style=\"border: 0px; width: 100%; height: 100%;\">";
		}
	break;

	case 'iframeLeitor':
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$r_C007_Id}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta'], true);

		$struct = $mail->pegarEstrutura($r_acaoId);
		if (!is_object($struct) && isset($struct['anexosManual'])) {
			$arquivo = $g['pathDados'] . 'anexosEmail/' . $r_acaoId . '-' . $r_file;
			$nomeArquivo = str_replace(' ', '-', $struct['anexosManual'][$r_file]);

			$strFileType = strtolower(strrev(substr(strrev($nomeArquivo),0,4)));
			$ContentType = "application/octet-stream";
	   		if ($strFileType == ".asf") 
	   			$ContentType = "video/x-ms-asf";
	   		if ($strFileType == ".avi")
	   			$ContentType = "video/avi";
	   		if ($strFileType == ".doc")
	   			$ContentType = "application/msword";
	   		if ($strFileType == ".zip")
	   			$ContentType = "application/zip";
	   		if ($strFileType == ".xls")
	   			$ContentType = "application/vnd.ms-excel";
	   		if ($strFileType == ".gif")
	   			$ContentType = "image/gif";
	   		if ($strFileType == ".jpg" || $strFileType == "jpeg")
	   			$ContentType = "image/jpeg";
	   		if ($strFileType == ".png")
	   			$ContentType = "image/png";
	   		if ($strFileType == ".wav")
	   			$ContentType = "audio/wav";
	   		if ($strFileType == ".mp3")
	   			$ContentType = "audio/mpeg3";
	   		if ($strFileType == ".mpg" || $strFileType == "mpeg")
	   			$ContentType = "video/mpeg";
	   		if ($strFileType == ".rtf")
	   			$ContentType = "application/rtf";
	   		if ($strFileType == ".htm" || $strFileType == "html")
	   			$ContentType = "text/html";
	   		if ($strFileType == ".xml") 
	   			$ContentType = "text/xml";
	   		if ($strFileType == ".xsl") 
	   			$ContentType = "text/xsl";
	   		if ($strFileType == ".css") 
	   			$ContentType = "text/css";
	   		if ($strFileType == ".php") 
	   			$ContentType = "text/php";
	   		if ($strFileType == ".asp") 
	   			$ContentType = "text/asp";
	   		if ($strFileType == ".pdf")
	   			$ContentType = "application/pdf";
	   		if ($strFileType == ".txt")
	   			$ContentType = "text/plain";

			header ("Content-Type: $ContentType");
			header ("Content-Disposition: inline; filename=$nomeArquivo;");
			$handle = fopen($arquivo, "rb");
			if ($handle) {
				while (@!feof($handle)) {
					echo fread($handle, 8192);
				}
				fclose($handle);
			}
		} else {
			$mail->downloadAnexo($r_acaoId, $r_file, false, true);
		}
		die();
	break;
}