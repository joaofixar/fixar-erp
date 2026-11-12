<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
		case 'atualizarEmails':
			global $g;
			set_time_limit(0);

		do {
			$temEmailsProcessando = false;
			$res = mysql_query("SHOW FULL PROCESSLIST");
			while ($row = mysql_fetch_assoc($res)) {
				if (preg_match("/T139/i", $row['Info'])) {
					$temEmailsProcessando = true;
				}
			}
			if (!$temEmailsProcessando) {
				break;
			}
			sleep(10);
		} while(true);

			$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta, C007_Email_Pasta_Entrada, C007_Email_Pasta_Saida FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'";
			$resultado = mysql_query($sql);
			$C007 = mysql_fetch_assoc($resultado);
			$g['C007'] = $C007;

			if (empty($C007['C007_Email_POP_Servidor'])) {
				$resposta['code'] = false;
				$resposta['data'] = "Servidor IMAP não configurado para este usuário. Preencha o campo \"Servidor IMAP\" no cadastro do usuário.";
				$resposta['nEmailNaoLidos'] = "0";
				break;
			}
			if (empty($C007['C007_Email_SMTP_Usuario']) || empty($C007['C007_Email_SMTP_Senha'])) {
				$resposta['code'] = false;
				$resposta['data'] = "Usuário ou senha do e-mail não configurados para este usuário.";
				$resposta['nEmailNaoLidos'] = "0";
				break;
			}

			// Pega infos
			require_once('sistema/comunicacaoEmail.php');
			$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'E', false, $C007['C007_Email_POP_Porta']);
			$totalEntrada = $mail->atualizarEmails();

			if (($totalEntrada === false) || (is_string($totalEntrada) && strpos($totalEntrada, "IMAP") !== false)) {
				$resposta['code'] = false;
				$resposta['data'] = "Erro ao tentar conectar:<br><br>".$totalEntrada;
				$resposta['nEmailNaoLidos'] = "0";
			} else {
				$totalSaida = 0;
				/*
				$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'S', false, $C007['C007_Email_POP_Porta']);
				$totalSaida = $mail->atualizarEmails();
			if ($totalSaida === false) {
				$resposta['code'] = false;
				$resposta['data'] = "Erro ao tentar conectar.<br />Por favor, verifique se o Servidor/Login/Senha estão corretos.";
			} else {
			*/
				// Número de e-mails não lidos
				$sql = "SELECT COUNT(*) as Total FROM T139 WHERE T139_Pasta = 'E' AND T139_Flag_Lido = 0 AND T139_Flag_Excluir = 0 AND T139_C007_Id = '{$g['usuarioAtual']}';";
				$resultado = mysql_query($sql);
				$row = mysql_fetch_assoc($resultado);
				$resposta['nEmailNaoLidos'] = $row['Total'];

				$total = $totalEntrada + $totalSaida;
				$totalEntrada = (int) $totalEntrada;
				$totalSaida = (int) $totalSaida;
				if ($total == 0) {
					$resposta['code'] = false;
					$resposta['data'] = "Atualização completa.<br />Nenhum novo e-mail.";
					$resposta['nEmails'] = 0;
				} else {
					//$resposta['data'] = "Atualização completa.<br />Você tem novo(s) e-mail(s).<br /><br />Entrada ($totalEntrada)<br />Saída ($totalSaida)";
					$resposta['data'] = "Atualização completa.<br />Você tem {$totalEntrada} novo(s) e-mail(s).";
					$resposta['nEmails'] = $totalEntrada;
				}
			/*
			}
			*/
		}
	break;

	case 'excluirMarcados':
		global $g;
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'E', false, $C007['C007_Email_POP_Porta']);
		$exc = $mail->excluirMarcados(base64_decode($r_extra));
		$resposta['data'] = "{$exc} e-mails excluídos!";
	break;

	case 'excluirSpamsMarcados':
		global $g;
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'E', false, $C007['C007_Email_POP_Porta']);
		$exc = $mail->excluirSpamsMarcados(base64_decode($r_extra));
		$resposta['data'] = "{$exc} spams excluídos!";
	break;

	case 'excluirDesmarcados':
		global $g;
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'E', false, $C007['C007_Email_POP_Porta']);
		$exc = $mail->excluirDesmarcados(base64_decode($r_extra));
		$resposta['data'] = "{$exc} e-mails excluídos!";
	break;

	// FUNÇÃO OBSOLETA NECESSITA DA BIBLIOTECA IMAP A QUAL NÃO TEMOS MAIS NO SISTEMA 05/04/2023
	case 'associarEmails':
		global $g;
		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'E', false, $C007['C007_Email_POP_Porta']);
		log("associarEmails");
		$mail->associarEmails(false, $r_select);
		$resposta['data'] = "Finalizado!";
	break;
	
	case 'selecionarEmail':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T139_Id = isset($_REQUEST['T139_Id']) ? $_REQUEST['T139_Id'] : false;
		$T139_Id = mysql_real_escape_string($T139_Id);
		$sql = "UPDATE T139 SET T139_Flag_Selecionado = '{$selected}' WHERE T139_Id = '{$T139_Id}';";
		mysql_query($sql);
	break;

	case 'vincularEmailOrcamentoUpdate':
		$T139_Id = isset($_REQUEST['T139_Id']) ? mysql_real_escape_string($_REQUEST['T139_Id']) : false;
		$T003_Id = isset($_REQUEST['T003_Id']) ? mysql_real_escape_string($_REQUEST['T003_Id']) : false;
		if (empty($T139_Id) || empty($T003_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Parâmetros inválidos.';
			break;
		}

		$sqlT003 = mysqli_query("SELECT T003_Id FROM T003 WHERE T003_Id = '{$T003_Id}'");
		if (mysql_num_rows($sqlT003) <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = 'Orçamento não encontrado.';
			break;
		}

		$sql = "UPDATE T139 SET T139_T003_Id = '{$T003_Id}' WHERE T139_Id = '{$T139_Id}'";
		if (!mysql_query($sql)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Erro ao vincular e-mail ao orçamento. Erro: ' . mysql_error();
			break;
		}
		$resposta['code'] = true;
		$resposta['data'] = 'E-mail vinculado ao orçamento com sucesso.';
	break;
	
	case 'marcaLidos':
		$extra = gPegarParteWhere(base64_decode($r_extra));
		$sql =	"update T139 set T139_Flag_Selecionado='0' WHERE {$extra};";
		mysql_query($sql);
		$extra = gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T139_Flag_Lido='1' and T139_Flag_Excluir='0')"));
		$sql =	"update T139 set T139_Flag_Selecionado='1' WHERE {$extra};";
		mysql_query($sql);
	break;
	
	case 'desmarcarTodos':
		$leftJoin = explode("WHERE", base64_decode($r_extra));
		$leftJoin = $leftJoin[0];
		$extra = gPegarParteWhere(base64_decode($r_extra));
		$sql =	"update T139 {$leftJoin} set T139_Flag_Selecionado='0' WHERE {$extra};";
		mysql_query($sql);
	break;

	case 'marcarTodos':
		$leftJoin = explode("WHERE", base64_decode($r_extra));
		$leftJoin = $leftJoin[0];
		$extra = gPegarParteWhere(base64_decode($r_extra));
		$sql =	"update T139 {$leftJoin} set T139_Flag_Selecionado='1' WHERE {$extra};";
		mysql_query($sql);
	break;
	
	case 'marcaNaoLidos':
		$extra = gPegarParteWhere(base64_decode($r_extra));
		$sql =	"update T139 set T139_Flag_Selecionado='0' WHERE {$extra};";
		mysql_query($sql);
		$extra = gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T139_Flag_Lido='0' and T139_Flag_Excluir='0')"));
		$sql =	"update T139 set T139_Flag_Selecionado='1' WHERE {$extra};";
		mysql_query($sql);
	break;

	case 'marcarComoNaoLido':
		$r_T139_Id = mysql_real_escape_string($r_T139_Id);
		$sql =	"update T139 set T139_Flag_Lido='0' WHERE T139_Id = '{$r_T139_Id}';";
		mysql_query($sql);
	break;

	case 'marcaSelecionadosComoLidos':
		$extra = gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T139_Flag_Selecionado='1')"));
		$sql =	"update T139 set T139_Flag_Lido='1' WHERE {$extra};";
		mysql_query($sql);
	break;

	case 'marcaSelecionadosComoNaoLidos':
		$extra = gPegarParteWhere(gInsertExtraWhere(base64_decode($r_extra), "(T139_Flag_Selecionado='1')"));
		$sql =	"update T139 set T139_Flag_Lido='0' WHERE {$extra};";
		mysql_query($sql);
	break;

	case 'processarWhitelist':
		require_once('bibliotecas/classes/ETC006.php');
        $ETC006 = new ETC006();
        $ETC006->processarWhitelist();
	break;

	case 'processarImagensEmail':
		$T139_Id = isset($_REQUEST['T139_Id']) ? $_REQUEST['T139_Id'] : false;
		$sqlT139 = mysql_query("SELECT T139_C007_Id, T139_Estrutura FROM T139 WHERE T139_Id = '{$T139_Id}'");
		$rowT139 = mysql_fetch_assoc($sqlT139);

		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$rowT139['T139_C007_Id']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $T139_Id, $C007['C007_Email_POP_Porta']);
		$corpo = $mail->processarImagens($T139_Id, false);
		if(strlen($corpo) > 0){
			$corpo = stripslashes($corpo);
			$rowT139['T139_Estrutura'] = unserialize($rowT139['T139_Estrutura']);
			$estrutura = $mail->pegarCodificacao($rowT139['T139_Estrutura']);
			$corpo = ($estrutura == '' && strpos($corpo, 'windows-1252') === false && strpos($corpo, 'iso-8859-1') === false) ? utf8_decode($corpo) : $corpo;
			$resposta['code'] = true;
			$resposta['data'] = $corpo;
		} else {
			$resposta['code'] = false;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";