<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/app.php';

tablet_separacao_require_auth();

$model = preg_replace('/[^a-zA-Z0-9_]/', '', (string) ($_GET['model'] ?? ''));
$idType = preg_replace('/[^a-zA-Z0-9_]/', '', (string) ($_GET['id_type'] ?? ''));
$id = trim((string) ($_GET['id'] ?? ''));
$sql = (string) ($_GET['sql'] ?? '');
$redirect = (string) ($_GET['redirect'] ?? '') === '1';

if (!$redirect) {
    header('Content-Type: application/json; charset=utf-8');
}

if ($model === '' || $idType === '' || $id === '') {
    http_response_code(400);
    if ($redirect) {
        echo 'Parametros da etiqueta incompletos.';
    } else {
        echo json_encode(['ok' => false, 'message' => 'Parametros da etiqueta incompletos.']);
    }
    exit;
}

set_time_limit(180);

$callbackName = 'tabletSeparacaoServerLabelCallback';
$sessionCookie = session_id();
$companyId = (int) ($_SESSION['Empresa_Atual'] ?? tablet_separacao_company_id());
$userId = (int) ($_SESSION['Usuario_Atual'] ?? tablet_separacao_user_id());
$database = (string) ($_SESSION['Banco_Dados'] ?? $_SESSION['bancoDados'] ?? $_SESSION['cBd_Configuracao'] ?? '');
$labelSourceLink = '/sistema/personalizacao/tela/' . $model . '/?ajax=true&' . $idType . '=' . rawurlencode($id) . '&sql=' . rawurlencode($sql);
$generatorParams = [
    'ajax' => 'true',
    'acaoId' => $model,
    'link' => $labelSourceLink,
    'callback' => $callbackName,
    'sessionCookie' => $sessionCookie,
    'p' => (string) $companyId,
    'u' => (string) $userId,
    'db' => $database,
    'noEmp' => 'true',
];
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
$generatorPath = '/hardness3/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/?' . http_build_query($generatorParams);
$generatorCandidates = array_values(array_unique(array_filter([
    $scheme . '://' . $host . $generatorPath,
    'http://127.0.0.1' . $generatorPath,
    'http://localhost' . $generatorPath,
])));
$cookiePairs = [];

foreach ($_COOKIE as $cookieName => $cookieValue) {
    $cookiePairs[] = rawurlencode((string) $cookieName) . '=' . rawurlencode((string) $cookieValue);
}

// Libera a sessão antes de chamar outra rota do mesmo sistema. Sem isso,
// a segunda requisição pode ficar aguardando o lock da sessão até timeout.
session_write_close();

$context = stream_context_create([
    'http' => [
        'header' => implode("\r\n", array_filter([
            $cookiePairs ? 'Cookie: ' . implode('; ', $cookiePairs) : '',
            'ngrok-skip-browser-warning: true',
        ])),
        'timeout' => 150,
    ],
]);
$responseBody = false;
$usedGeneratorUrl = '';
$lastResponseHeader = [];

foreach ($generatorCandidates as $generatorUrl) {
    $http_response_header = [];
    $responseBody = @file_get_contents($generatorUrl, false, $context);
    $lastResponseHeader = $http_response_header ?? [];
    $usedGeneratorUrl = $generatorUrl;

    if (is_string($responseBody) && trim($responseBody) !== '') {
        break;
    }
}
$pdfUrl = '';

if (is_string($responseBody)) {
    $responseText = trim($responseBody);
    $pattern = '/^' . preg_quote($callbackName, '/') . '\((.*)\);?\s*$/s';
    if (preg_match($pattern, $responseText, $matches)) {
        $response = json_decode($matches[1], true);
        if (is_array($response) && !empty($response['data'])) {
            $pdfUrl = (string) $response['data'];
        }
    }
}

if ($pdfUrl === '') {
    http_response_code(500);
    $status = '';
    if (!empty($lastResponseHeader[0])) {
        $status = (string) $lastResponseHeader[0];
    }
    $responseStart = is_string($responseBody) ? trim(substr(strip_tags($responseBody), 0, 180)) : '';
    $message = trim('Nao foi possivel gerar o PDF da etiqueta. ' . ($status !== '' ? $status . '. ' : '') . ($responseStart !== '' ? 'Retorno: ' . $responseStart : ''));
    if ($redirect) {
        echo $message;
        exit;
    }

    echo json_encode([
        'ok' => false,
        'message' => $message,
        'debug' => [
            'url' => $usedGeneratorUrl,
            'status' => $status,
            'response_start' => $responseStart,
        ],
    ]);
    exit;
}

if (strpos($pdfUrl, '/') === 0) {
    $pdfUrl = $scheme . '://' . $host . $pdfUrl;
}

if ($redirect) {
    header('Location: ' . $pdfUrl);
    exit;
}

echo json_encode(['ok' => true, 'url' => $pdfUrl]);
