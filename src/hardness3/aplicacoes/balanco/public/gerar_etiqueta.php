<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$redirect = (string) ($_GET['redirect'] ?? '') === '1';
if (!$redirect) header('Content-Type: application/json; charset=utf-8');

function balanco_label_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

function balanco_label_error(string $message, int $status): void
{
    global $redirect;
    if ($redirect) {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
        exit;
    }
    balanco_label_response(['ok' => false, 'message' => $message], $status);
}

$context = balanco_require_access();
if (!balanco_can_process()) {
    balanco_label_error('Seu perfil não possui permissão para imprimir etiquetas de balanço.', 403);
}

$t066Id = (int) ($_GET['t066_id'] ?? 0);
$modelKey = (string) ($_GET['model'] ?? '');
$models = balanco_label_models();
$model = $models[$modelKey] ?? null;
if (!is_array($model) || $t066Id <= 0) {
    balanco_label_error('Solicitação de etiqueta inválida.', 400);
}

$labelContext = balanco_label_context_for_t066($t066Id, $context);
if (!$labelContext) {
    balanco_label_error('A etiqueta só pode ser gerada para a locação atualmente em contagem.', 403);
}

$contextKey = (string) ($model['context_field'] ?? '');
$contextId = (int) ($labelContext[$contextKey] ?? 0);
$result = balanco_generate_label_pdf($model, $contextId, $context);
if (!$result['ok']) balanco_label_error((string) $result['message'], 500);
if ($redirect) {
    header('Location: ' . $result['url']);
    exit;
}
balanco_label_response($result);
