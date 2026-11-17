<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'removerNotificacaoRetornoCotacaoEmergencia':	
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		mysql_query("UPDATE T219 LEFT JOIN T004 ON T004_T219_Id = T219_Id SET T219_Flag_Comprar = 'N', T219_Flag_Lido = 'S' WHERE T004_T003_Id = '{$acaoId}'");
		marcarNotificacaoLido($r_Uniqid_Mensagem);
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'removerNotificacaoPedidoLiberacaoCredito':	
		$resposta = false;
		$acaoId = isset($r_acaoId) && is_numeric($r_acaoId) ? (int) $r_acaoId : false;
		$Uniqid_Mensagem = isset($r_Uniqid_Mensagem) ? $r_Uniqid_Mensagem : false;

		if ($acaoId > 0) {
			$uniqId = '917' . $acaoId . $g['empresaAtual'] . '0' . $g['usuarioAtual'];
			if (empty($Uniqid_Mensagem) || $Uniqid_Mensagem == 'undefined') {
				$resposta = marcarNotificacaoLido($uniqId, '917', $acaoId);
			} else if ($Uniqid_Mensagem == $uniqId) {
				$resposta = marcarNotificacaoLido($Uniqid_Mensagem, '917', $acaoId);
			}
		}

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'removerNotificacaoPedidoEntradaEstoque':	
		$resposta = false;
		$acaoId = isset($r_acaoId) && is_numeric($r_acaoId) ? (int) $r_acaoId : false;
		$Uniqid_Mensagem = isset($r_Uniqid_Mensagem) ? $r_Uniqid_Mensagem : false;

		if ($acaoId > 0) {
			$uniqId918 = '918' . $acaoId . $g['empresaAtual'] . '0' . $g['usuarioAtual'];
			$uniqId985 = '985' . $acaoId . $g['empresaAtual'] . $g['usuarioAtual'];
			if (empty($Uniqid_Mensagem) || $Uniqid_Mensagem == 'undefined') {
				$resposta918 = marcarNotificacaoLido($uniqId918, '918', $acaoId);
				$resposta985 = marcarNotificacaoLido($uniqId985, '985', $acaoId);
				$resposta = $resposta918 && $resposta985;
			} else if ($Uniqid_Mensagem == $uniqId918) {
				$resposta = marcarNotificacaoLido($Uniqid_Mensagem, '918', $acaoId);
			} else if ($Uniqid_Mensagem == $uniqId985) {
				$resposta = marcarNotificacaoLido($Uniqid_Mensagem, '985', $acaoId);
			}
		}

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'removerNotificacaoOrcamento':	
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		//mysql_query("UPDATE T003 SET T003_Flag_Notificacao_Liberacao = '' WHERE T003_Id = '{$acaoId}'");
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'removerNotificacaoPedido':	
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		mysql_query("update 
						T006A
					set
						T006A_Flag_Notificacao = 'N'
					where
						T006A_T006_Id = '{$acaoId}'");
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'removerNotificacaoPrecoGenerico':	
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		mysql_query("update 
						T004A
					set
						T004A_Flag_Notificacao = 'N'
					where
						T004A_T004_Id = '{$acaoId}'");
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
	
	case 'removerNotificacaoPedidoLiberacao':	
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		//mysql_query("UPDATE T005 SET T005_Flag_Notificacao_Liberacao = '' WHERE T005_Id = '{$acaoId}'");
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'notificacaoRetornoPedidoAlmoxarifado':	
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		//mysql_query("UPDATE T005 SET T005_Flag_Reenviado_Almoxarifado_Lido = 'S' WHERE T005_Id = '{$acaoId}'");
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'notificacaoCotacaoFinalizada':
		mysql_query("UPDATE T218 SET T218_Flag_Notificacao = 2 WHERE T218_Flag_Notificacao = 1 AND T218_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}'");
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'verificaLogErros':
		mysql_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Id = '{$r_acaoId}'");
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}


function marcarNotificacaoLido($Uniqid_Mensagem, $Id = false, $acaoId = false)
{
	global $g, $confUsuario;

	if (empty($Uniqid_Mensagem) || $Uniqid_Mensagem == 'undefined') {
		error_log("marcarNotificacaoLido: identificador inválido | Uniqid_Mensagem={$Uniqid_Mensagem}");
		return false;
	}

	//Recupera o cache da memória
	$Dir = $g['pathDados'] . 'tmp/notificacoes/';
	if (!is_dir($Dir)) {
		return true;
	}

	if ($Id) {
		$notificacoes = array("cacheN001Notificacoes_{$Id}.txt");
	} else {
		$notificacoes = scandir("{$Dir}");
		if ($notificacoes === false) {
			error_log("marcarNotificacaoLido: erro ao listar diretório | diretorio={$Dir} | Uniqid_Mensagem={$Uniqid_Mensagem}");
			return false;
		}
	}

	$cacheCorrompido = false;
	foreach ($notificacoes as $arquivo) {
		if (strpos($arquivo, 'cacheN001Notificacoes_') !== 0 || substr($arquivo, -4) != '.txt') {
			continue;
		}

		$arquivoNome = $Dir . $arquivo;
		if (!is_file($arquivoNome)) {
			continue;
		}

		$cacheN001 = file_get_contents($arquivoNome);
		if ($cacheN001 === false) {
			error_log("marcarNotificacaoLido: erro ao ler cache | arquivo={$arquivoNome} | Uniqid_Mensagem={$Uniqid_Mensagem}");
			return false;
		}

		$cacheN001 = @unserialize($cacheN001);
		if (!is_array($cacheN001)) {
			error_log("marcarNotificacaoLido: cache inválido | arquivo={$arquivoNome} | Uniqid_Mensagem={$Uniqid_Mensagem}");
			$cacheCorrompido = true;
			continue;
		}

		if (!isset($cacheN001[$Uniqid_Mensagem])) {
			continue;
		}

		$N001 = $cacheN001[$Uniqid_Mensagem];
		if (!is_array($N001)
			|| ($Id && (!isset($N001['N001_Id_Mensagem']) || $N001['N001_Id_Mensagem'] != $Id))
			|| ($Id && (!isset($N001['N001_C004_Id']) || $N001['N001_C004_Id'] != $g['empresaAtual']))
			|| ($Id && (!isset($N001['N001_C007_Id']) || $N001['N001_C007_Id'] != $g['usuarioAtual']))
			|| ($acaoId && strpos($Uniqid_Mensagem, (string) $acaoId) === false)) {
			error_log("marcarNotificacaoLido: notificação não pertence ao usuário atual | arquivo={$arquivoNome} | Uniqid_Mensagem={$Uniqid_Mensagem}");
			return false;
		}

		$cacheN001[$Uniqid_Mensagem]['lido'] = 'S';
		$cacheN001['lastUpdate'] = time();
		$cacheN001 = serialize($cacheN001);
		$arquivoTmp = $arquivoNome . '.tmp';
		$h = fopen($arquivoTmp, "w");
		$r = $h ? fwrite($h, $cacheN001) : false;
		if ($h) {
			fclose($h);
		}

		if ($r === false || $r != strlen($cacheN001)) {
			is_file($arquivoTmp) and unlink($arquivoTmp);
			error_log("marcarNotificacaoLido: erro ao gravar cache | arquivo={$arquivoNome} | Uniqid_Mensagem={$Uniqid_Mensagem}");
			return false;
		}

		if (!rename($arquivoTmp, $arquivoNome)) {
			is_file($arquivoTmp) and unlink($arquivoTmp);
			error_log("marcarNotificacaoLido: erro ao substituir cache | arquivo={$arquivoNome} | Uniqid_Mensagem={$Uniqid_Mensagem}");
			return false;
		}

		return true;
	}

	if ($cacheCorrompido) {
		return false;
	}

	return true;
}
