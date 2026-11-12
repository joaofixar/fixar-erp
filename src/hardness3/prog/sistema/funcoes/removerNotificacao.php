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
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		//mysql_query("UPDATE T005 SET T005_Flag_Notificacao_Financeiro = '' WHERE T005_Id = '{$acaoId}'");
		$resposta = true;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'removerNotificacaoPedidoEntradaEstoque':	
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		//mysql_query("UPDATE T005 SET T005_Flag_Notificacao_Estoque = 'N' WHERE T005_Id = '{$acaoId}'");
		$resposta = true;
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


function marcarNotificacaoLido($Uniqid_Mensagem)
{
	global $g, $confUsuario;

	//Recupera o cache da memória
	$Dir = $g['pathDados'] . 'tmp/notificacoes/';
	is_dir($Dir) or mkdir($Dir, 0777, true);
	$notificacoes = scandir("{$Dir}");
	foreach ($notificacoes as $key => $arquivo)
		$arquivoNome = $Dir . $arquivo;
		$cacheN001 = file_get_contents($arquivoNome);
		if ($cacheN001){
			$cacheN001 = unserialize($cacheN001);

			//Cria nova notificação
			$cacheN001[$Uniqid_Mensagem]['lido'] = 'S';
			$cacheN001['lastUpdate'] = time();

			//Grava o cache
			$h = fopen($arquivoNome, "w");
			$r = fwrite($h, serialize($cacheN001));
			fclose($h);
		}
}
