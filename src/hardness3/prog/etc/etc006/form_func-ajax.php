<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'downloadAnexo':
		// Email
		$sql = "SELECT * FROM T139 WHERE T139_Id = '{$r_acaoId}';";
		$T139 = mysql_query($sql);
		$T139 = mysql_fetch_assoc($T139);
		// Pega infos
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$T139['T139_C007_Id']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
		
		// O anexo é manual (foi enviado local e gravado aqui) ou está no email?
		$struct = $mail->pegarEstrutura($r_acaoId);
		if (!is_object($struct) && isset($struct['anexosManual'])) {
			$arquivo = $g['pathDados'] . 'anexosEmail/' . $r_acaoId . '-' . $r_file;
			$nomeArquivo = str_replace(' ', '-', $struct['anexosManual'][$r_file]);
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename={$nomeArquivo};"); // size=$fileSize;
			$handle = fopen($arquivo, "rb");
			if ($handle) {
				while (@!feof($handle)) {
					echo fread($handle, 8192);
				}
				fclose($handle);
			}
		} else {
			$mail->downloadAnexo($r_acaoId, $r_file);
		}
		die(); // não retorna JSON
	break;
	
	case 'importarXML':
		// Email
		$sql = "SELECT * FROM T139 WHERE T139_Id = '{$r_acaoId}';";
		$T139 = mysql_query($sql);
		$T139 = mysql_fetch_assoc($T139);
		// Pega infos
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$T139['T139_C007_Id']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
		
		ob_start();
		// O anexo é manual (foi enviado local e gravado aqui) ou está no email?
		$struct = $mail->pegarEstrutura($r_acaoId);
		if (!is_object($struct) && isset($struct['anexosManual'])) {
			$arquivo = $g['pathDados'] . 'anexosEmail/' . $r_acaoId . '-' . $r_file;
			$nomeArquivo = str_replace(' ', '-', $struct['anexosManual'][$r_file]);
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename={$nomeArquivo};"); // size=$fileSize;
			$handle = fopen($arquivo, "rb");
			if ($handle) {
				while (!feof($handle)) {
					echo fread($handle, 8192);
				}
				fclose($handle);
			}
		} else {
			$mail->downloadAnexo($r_acaoId, $r_file);
		}
		$xml = ob_get_contents();
		ob_end_clean();
		
		$arqXml = '/tmp/' . uniqid() . '.xml';
		file_put_contents($arqXml, $xml);
		
		require_once('bibliotecas/classes/EST004.php');
		$EST004 = new EST004();
		
		$retorno = $EST004->ImportarXML(array('name' => basename($arqXml), 'tmp_name' => $arqXml), 'on');
		
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$sql = "UPDATE T139 SET T139_Flag_Xml_Importado = 'S' WHERE T139_Id = '{$r_acaoId}';";
			mysql_query($sql);
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
		
	break;

	case 'imprimirSimulacao':
		require_once("bibliotecas/classes/FIN001.php");
		$FIN001 = new FIN001();
		$D024_Id = isset($r_T180_D024_Id) ? $r_T180_D024_Id : false;
		$retorno = $FIN001->montarEmailSimulacaoReparcelamento($D024_Id);
		echo $retorno['Corpo'];
		die();
	break;

	case 'imprimirReparcelamento':
		require_once("bibliotecas/classes/FIN001.php");
		$FIN001 = new FIN001();
		$T166_Id = isset($r_T166_Id) ? $r_T166_Id : false;
		$retorno = $FIN001->montarEmailReparcelamento($T166_Id);
		echo $retorno['Corpo'];
		die();
	break;

	case 'arquivarAnexo':
		//Caso o arquivo não tenha sido baixado, realizamos o download pro servidor
		$resultado = mysql_query("SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$r_C007_Id}';");
		$C007 = mysql_fetch_assoc($resultado);
		ob_start();
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_T139_Id, $C007['C007_Email_POP_Porta']);
		$mail->downloadAnexo($r_T139_Id, $r_file);
		if(empty($r_nomeArquivo)){
			$anexos = $mail->listaAnexos($r_T139_Id);
			$r_nomeArquivo = $anexos[$r_file];
		}
		$conteudoArquivo = ob_get_contents();
		ob_end_clean();

		$nomeArquivo = $r_nomeArquivo;
		$nomeArquivo = str_replace(' ', '_', $nomeArquivo);
		$arquivo = strrev($nomeArquivo);
		$arquivo = explode('.', $arquivo);
		$extensao = strrev($arquivo[0]);
		$nomeArquivo = mysql_real_escape_string($nomeArquivo);

		//Grava os arquivos no servidor
		if($r_tipo == 'produto'){
			//Produto
			mysql_query("INSERT INTO T140 (T140_D001_Id, T140_Nome_Arquivo, T140_T139_Id, T140_Id_Anexo) VALUES ('{$r_Id}', '{$nomeArquivo}', '{$r_T139_Id}', '{$r_file}')");
			$dir = "{$g['pathDados']}certificados";
			is_dir($dir) or mkdir($dir, 0777, true);
			$arquivo = $dir . '/' . $g['mysqlLastId'] . '.' . $extensao;
			if (file_exists($arquivo)) { unlink($arquivo); }
		} else if($r_tipo == 'cliente'){
			//Cliente
			mysql_query("INSERT INTO D087 (D087_D024_Id, D087_Data_Hora_UpLoad, D087_Descricao, D087_Nome_Arquivo, D087_C007_Id) VALUES ('{$r_Id}', CURRENT_TIMESTAMP(), 'Anexado através do e-mail', '{$nomeArquivo}', '{$g['usuarioAtual']}')");
			$dir = "{$g['pathDados']}documentos/D024_".str_repeat('0',5-strlen($r_Id)).$r_Id;
			is_dir($dir) or mkdir($dir, 0777, true);
			$arquivo = $dir . '/' . $g['mysqlLastId'] . '.' . $extensao;
		}
		file_put_contents($arquivo, $conteudoArquivo);
	break;

	case 'carregaContatos':
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$resposta['data']['D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true));
		$resposta['data']['D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true)));
	break;

	case 'criarInteracaoEmail':
		require_once('sistema/comunicacaoEmail.php');

		$sqlT139 = mysql_query("SELECT *.C007 FROM T139 LEFT JOIN C007 ON C007_Id=T139_C007_Id WHERE T139_Id = '{$r_T139_Id}'");
		$rowT139 = mysql_fetch_array($sqlT139);

		$mail = new comunicacaoEmail($rowT139['C007_Email_POP_Servidor'], $rowT139['C007_Email_SMTP_Usuario'], $rowT139['C007_Email_SMTP_Senha'], false, $r_T139_Id, $rowT139['C007_Email_POP_Porta']);
		$D085_Id = $mail->associarEmails($r_T139_Id);
		$sqlD085 = mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_Id = '{$D085_Id}'");
		$rowD085 = mysql_fetch_array($sqlD085);
		if($rowD085['D085_T259_Id'] > 0){
			$resposta['code'] = true;
			$resposta['data'] = $rowD085['D085_T259_Id'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Erro ao gerar tarefa';
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
