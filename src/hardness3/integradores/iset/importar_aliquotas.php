<?php
namespace hardness;

require_once dirname(__DIR__) . '/layout.php';
require_once dirname(dirname(__DIR__)) . '/importadores/importador_orcamento_pdf/inc/app.php';

importador_orcamento_pdf_bootstrap();
require_once dirname(dirname(__DIR__)) . '/bibliotecas/webservices/iset/iset.php';
integradores_exigir_permissao(INTEGRADORES_C029_ACESSAR_PAINEL, 'Seu perfil não possui permissão para importar pedidos pela integração ISET.');

header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('ok' => false, 'mensagem' => 'Método não permitido.'));
    exit;
}

$csrf = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
if (empty($_SESSION['integradores_iset_importar_csrf']) || !hash_equals($_SESSION['integradores_iset_importar_csrf'], $csrf)) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'mensagem' => 'Sua sessão expirou. Atualize a página e tente novamente.'));
    exit;
}

try {
    global $g;
    $empresaId = (int) $g['empresaAtual'];
    $processamentoId = isset($_POST['processamento_id']) ? (int) $_POST['processamento_id'] : 0;
    $pedidoId = isset($_POST['pedido_id']) ? (int) $_POST['pedido_id'] : 0;
    $resultado = (new ISET_ALIQUOTAS())->processarLote(
        $processamentoId,
        $empresaId
    );
    echo json_encode(array('ok' => true, 'resultado' => $resultado), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Throwable $erro) {
    integracoesRegistrarLog(array(
        'empresaId' => isset($empresaId) ? $empresaId : 0,
        'integracao' => 'ISET',
        'operacao' => INT_OPERACAO_IMPORTAR_PEDIDO,
        'status' => INT_STATUS_AVISO,
        'etapa' => INT_ETAPA_ATUALIZACAO_ALIQUOTAS,
        'tabelaPrincipal' => 'T005',
        'idPrincipal' => isset($pedidoId) ? $pedidoId : 0,
        'mensagem' => 'Falha ao processar alíquotas: ' . $erro->getMessage(),
        'log' => array(
            'processamentoAliquotasId' => isset($processamentoId) ? $processamentoId : 0,
            'pedidoErpId' => isset($pedidoId) ? $pedidoId : 0,
            'erroDetalhado' => $erro->getMessage(),
        ),
    ));
    http_response_code(422);
    echo json_encode(array('ok' => false, 'mensagem' => $erro->getMessage()), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
