<?php
namespace hardness;

require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/response.php';
require_once __DIR__ . '/../inc/upload.php';
require_once __DIR__ . '/../inc/pdf/PdfPreviewService.php';

importador_orcamento_pdf_require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    importador_orcamento_pdf_json(array(
        'code' => false,
        'data' => 'Metodo nao permitido.',
    ), 405);
}

$T003_Id = isset($_POST['T003_Id']) ? (int) $_POST['T003_Id'] : 0;
list($orcamentoOk, $orcamentoMessage, $orcamento) = importador_orcamento_pdf_validar_orcamento_editavel($T003_Id);

if (!$orcamentoOk) {
    importador_orcamento_pdf_json(array(
        'code' => false,
        'data' => $orcamentoMessage,
    ), 403);
}

list($ok, $message, $data) = importador_orcamento_pdf_salvar_upload_pdf(isset($_FILES['arquivo_pdf']) ? $_FILES['arquivo_pdf'] : null, $orcamento);

if (!$ok) {
    importador_orcamento_pdf_json(array(
        'code' => false,
        'data' => $message,
    ), 400);
}

$preferAiParser = importador_orcamento_pdf_ai_parser_enabled(true);
$forceAi = isset($_REQUEST['force_ai_parser']) ? (string) $_REQUEST['force_ai_parser'] : '';
if ($forceAi !== '') {
    $preferAiParser = importador_orcamento_pdf_truthy_flag($forceAi, $preferAiParser);
}
$aiInputMode = importador_orcamento_pdf_ai_input_mode('pdf');
if (isset($_REQUEST['ai_input_mode']) && $_REQUEST['ai_input_mode'] !== '') {
    $requestedAiInputMode = (string) $_REQUEST['ai_input_mode'];
    if (in_array($requestedAiInputMode, array('pdf', 'raw_text'), true)) {
        $aiInputMode = $requestedAiInputMode;
    }
}

$previewService = new PdfPreviewService();
$preview = $previewService->preview($data['stored_path'], array(
    'work_hash' => $data['sha256'],
    'work_dir' => isset($data['work_dir']) ? $data['work_dir'] : '',
    'D024_Id' => isset($data['D024_Id']) ? (int) $data['D024_Id'] : 0,
    'T003_Id' => isset($data['T003_Id']) ? (int) $data['T003_Id'] : 0,
    'timeout_seconds' => 60,
    'prefer_ai_parser' => $preferAiParser,
    'ai_pdf_model' => importador_orcamento_pdf_ai_parser_model(''),
    'ai_timeout_seconds' => importador_orcamento_pdf_ai_parser_timeout_seconds(180),
    'ai_input_mode' => $aiInputMode,
));

$shouldRedirect = isset($_REQUEST['redirect']) && (string) $_REQUEST['redirect'] === '1';
if ($shouldRedirect) {
    $target = importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id);
    header('Location: ' . $target);
    exit;
}

$publicFileData = $data;
unset($publicFileData['stored_path']);

$preview['extraction'] = array_intersect_key($preview['extraction'], array(
    'success' => true,
    'status' => true,
    'message' => true,
    'stdout' => true,
    'stderr' => true,
    'exit_code' => true,
    'elapsed_ms' => true,
    'metadata' => true,
));

unset($preview['diag']['raw_text_path'], $preview['diag']['work_dir']);

importador_orcamento_pdf_json(array(
    'code' => true,
    'data' => array(
        'message' => 'PDF recebido com sucesso.',
        'file' => $publicFileData,
        'preview' => $preview,
    ),
));
