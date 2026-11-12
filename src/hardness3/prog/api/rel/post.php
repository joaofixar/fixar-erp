<?php
namespace hardness;

//funções gerais
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();
//funções pertinentes a api de relatórios
require_once('bibliotecas/classes/API003.php');
$API003 = new API003();

/**
 * Ações
 */
	switch($g['r'][3]) {
	case 'IsetPedido':
		/*
		 * Etapa 2B: o webhook é somente um gatilho. A classe ISET concentra as
		 * decisões, a transação e a importação; cada operação gera um INT001.
		 */
		$inicioWebhook = microtime(true);
		$corpoRequisicao = file_get_contents('php://input');
		$headersWebhook = function_exists('getallheaders') ? getallheaders() : array();
		if (!$headersWebhook) {
			foreach ($_SERVER as $nome => $valor) {
				if (strpos($nome, 'HTTP_') === 0) {
					$headersWebhook[str_replace('_', '-', substr($nome, 5))] = $valor;
				}
			}
		}
		foreach ($headersWebhook as $nome => $valor) {
			if (preg_match('/authorization|token|bearer|key|secret/i', $nome)) {
				$headersWebhook[$nome] = '********';
			}
		}

		$contentTypeWebhook = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
		$jsonWebhook = json_decode($corpoRequisicao, true);
		$jsonWebhookValido = trim($corpoRequisicao) !== '' && json_last_error() === JSON_ERROR_NONE && is_array($jsonWebhook);
		$contarNiveisJson = function ($valor, $nivel = 1) use (&$contarNiveisJson) {
			if (!is_array($valor) || !$valor) {
				return $nivel;
			}
			$maiorNivel = $nivel;
			foreach ($valor as $item) {
				$maiorNivel = max($maiorNivel, $contarNiveisJson($item, $nivel + 1));
			}
			return $maiorNivel;
		};

		require_once('bibliotecas/webservices/iset/iset_logs.php');
		require_once('bibliotecas/webservices/iset/iset.php');

		$empresaWebhook = isset($_GET['empresa']) ? (int) $_GET['empresa'] : 0;
		$extrairWebhook = function ($dados, array $caminhos) {
			foreach ($caminhos as $caminho) {
				$valor = $dados;
				foreach (explode('.', $caminho) as $chave) {
					if (!is_array($valor) || !array_key_exists($chave, $valor)) { $valor = null; break; }
					$valor = $valor[$chave];
				}
				if ($valor !== null && $valor !== '') return $valor;
			}
			return null;
		};
		$dadosExtraidos = array(
			'type' => $jsonWebhookValido ? $extrairWebhook($jsonWebhook, array('type', 'event.type', 'event')) : null,
			'orderId' => $jsonWebhookValido ? $extrairWebhook($jsonWebhook, array('orderId', 'order.id', 'order.orderId', 'data.orderId')) : null,
			'domain' => $jsonWebhookValido ? $extrairWebhook($jsonWebhook, array('domain', 'order.domain', 'data.domain')) : null,
			'statusId' => $jsonWebhookValido ? $extrairWebhook($jsonWebhook, array('statusId', 'order.statusId', 'data.statusId')) : null,
		);
		$etapaWebhook = INT_ETAPA_RECEBIMENTO;
		$statusWebhook = INT_STATUS_INFO;
		$mensagemWebhook = 'Webhook recebido.';
		$detalhesProcessamento = array('extraido' => $dadosExtraidos, 'modo' => 'AUTOMATICO');
		$registrarLogWebhook = true;

		if (strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '') !== 'POST') {
			$statusWebhook = INT_STATUS_ERRO;
			$mensagemWebhook = 'Webhook ignorado: método HTTP diferente de POST.';
		} elseif ($empresaWebhook <= 0 || !($empresaConsulta = mysql_query("SELECT C004_Id FROM C004 WHERE C004_Id='{$empresaWebhook}' LIMIT 1")) || mysql_num_rows($empresaConsulta) !== 1) {
			$statusWebhook = INT_STATUS_ERRO;
			$mensagemWebhook = 'Webhook ignorado: empresa inválida.';
		} elseif (!$jsonWebhookValido) {
			$statusWebhook = INT_STATUS_ERRO;
			$mensagemWebhook = 'Webhook ignorado: corpo JSON inválido.';
		} elseif (!filter_var($dadosExtraidos['orderId'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
			$statusWebhook = INT_STATUS_ERRO;
			$mensagemWebhook = 'Webhook ignorado: identificador do pedido não informado.';
		} else {
			$configuracaoIset = array();
			$configuracaoConsulta = mysql_query("SELECT C031_Campo,C031_Valor FROM C031 WHERE C031_C004_Id='{$empresaWebhook}'");
			if ($configuracaoConsulta) while ($configuracaoLinha = mysql_fetch_assoc($configuracaoConsulta)) $configuracaoIset[$configuracaoLinha['C031_Campo']] = $configuracaoLinha['C031_Valor'];
			$empresaAnterior = isset($g['empresaAtual']) ? $g['empresaAtual'] : null;
			$c031Anterior = isset($g['C031']) ? $g['C031'] : array();
			$c031MinusculoAnterior = isset($g['c031']) ? $g['c031'] : array();
			$g['empresaAtual'] = $empresaWebhook;
			$g['C031'] = $configuracaoIset;
			$g['c031'] = $configuracaoIset;
			try {
				$resultado = (new ISET())->processarWebhook((int) $dadosExtraidos['orderId'], array(
					'log' => array(
						'webhook' => array(
							'extraido' => $dadosExtraidos,
							'headers' => $headersWebhook,
							'body' => $corpoRequisicao,
							'json' => $jsonWebhookValido ? $jsonWebhook : null,
						),
					),
				));

				$etapaWebhook = $resultado['etapa'];
				$statusWebhook = $resultado['status'];
				$mensagemWebhook = $resultado['mensagem'];
				$detalhesProcessamento = array_merge($detalhesProcessamento, isset($resultado['detalhes']) ? $resultado['detalhes'] : array());
				$registrarLogWebhook = array_key_exists('registrarLogWebhook', $resultado)
					? (bool) $resultado['registrarLogWebhook']
					: empty($resultado['importado']);
			} catch (\Throwable $erroWebhook) {
				$etapaWebhook = $etapaWebhook === INT_ETAPA_RECEBIMENTO ? 'CONSULTA_API' : $etapaWebhook;
				$statusWebhook = INT_STATUS_ERRO;
				$mensagemWebhook = 'Webhook interrompido durante o processamento automático.';
				$detalhesProcessamento['erro'] = integracoesDiagnosticoExcecao($erroWebhook);
			}
			$g['empresaAtual'] = $empresaAnterior;
			$g['C031'] = $c031Anterior;
			$g['c031'] = $c031MinusculoAnterior;
		}

		if ($registrarLogWebhook) integracoesRegistrarLog(array(
			'empresaId' => $empresaWebhook,
			'integracao' => 'ISET',
			'operacao' => INT_OPERACAO_WEBHOOK,
			'status' => $statusWebhook,
			'etapa' => $etapaWebhook,
			'idExterno' => isset($dadosExtraidos['orderId']) ? $dadosExtraidos['orderId'] : '',
			'mensagem' => $mensagemWebhook,
			'log' => array(
				'requisicao' => array(
					'metodo' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
					'url' => (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
					'queryString' => isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '',
					'protocolo' => isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '',
					'contentType' => $contentTypeWebhook,
					'contentLength' => isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : strlen($corpoRequisicao),
					'userAgent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
					'ipRemoto' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
					'recebidoEm' => date('c'),
					'headers' => $headersWebhook,
					'body' => $corpoRequisicao,
				),
				'json' => array('contentTypeIndicaJson' => stripos($contentTypeWebhook, 'json') !== false, 'valido' => $jsonWebhookValido, 'decodificado' => $jsonWebhookValido ? $jsonWebhook : null, 'chavesRaiz' => $jsonWebhookValido ? array_keys($jsonWebhook) : array(), 'niveis' => $jsonWebhookValido ? $contarNiveisJson($jsonWebhook) : 0),
				'processamento' => $detalhesProcessamento,
				'tamanhoBytes' => strlen($corpoRequisicao),
				'tempoProcessamentoMs' => (int) round((microtime(true) - $inicioWebhook) * 1000),
				'memoriaBytes' => memory_get_peak_usage(true),
			),
		));

		http_response_code(200);
		header('Content-Type: application/json; charset=utf-8');
		echo '{"received":true}';
	break;

	case 'IntelipostStatus':
		// Recebe e verifica a chave da API enviada pelo link da requisição
		$retorno = $API003->auth($_GET['API_AUTH']);

		/* Se o $retorno foi um array significa que 
		a chave está incorreta*/
		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		// Recebe o JSON enviado pelo POST
		$data = json_decode(file_get_contents('php://input'), true);
		
		//error_log(var_export($data, true)); // Debug para ver como o array chega

		// Extrai Macro/Micro Status Id e data hora que o status foi atribuido
		$macroStatus = strtoupper(gLimpaAcentosPuro(trim($data['history']['shipment_order_volume_state_localized'])));
		$microStatus = strtoupper(gLimpaAcentosPuro(trim($data['history']['shipment_volume_micro_state']['default_name'])));
		$previsaoTransp = trim($data['estimated_delivery_date']['logistic_provider']['current_iso']);
		$datahStatus = trim($data['history']['event_date_iso']);
		$T005_Id_Pedido_Externo = $data['order_number'];

		if(!empty($macroStatus) && !empty($microStatus) && !empty($datahStatus) && !empty($T005_Id_Pedido_Externo))
		{
			//error_log("MACRO: $macroStatus | MICRO: $microStatus | ID: $T005_Id_Pedido_Externo | DATAH: $datahStatus | PREVISÃO TRANSP -> $dataPrevisaoTransp"); // Printa para o arquivo de log as informações;
		
			require_once('bibliotecas/webservices/intelipost/apiIntelipost.php');
			$Intelipost = new Intelipost();
	
			// Chama função que atualiza o status do Intelipost
			$Intelipost->AtualizaStatus($T005_Id_Pedido_Externo, $macroStatus, $microStatus, $datahStatus, $previsaoTransp);
	
			// Responde 200 tudo ok
			http_response_code(200);
			echo http_response_code();
		}
		else
		{
			// Responde 206 falta dados
			http_response_code(206);
			echo http_response_code();
		}

		break;

	// Post da API que executa um processo no sistema
	case 'ExecutarProcessoAPI':
		$retorno = $API003->auth($_GET['API_AUTH']);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$data = file_get_contents('php://input');
		$C037_Id = $_GET['IdProcesso'];

		$API001->executaProcesso($C037_Id, array("data" => $data));
		break;

	default:
		$API001->retorno404();
}
