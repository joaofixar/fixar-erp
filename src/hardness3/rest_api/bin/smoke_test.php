<?php

declare(strict_types=1);

$usage = <<<TXT
Uso:
  php rest_api/bin/smoke_test.php --token=TOKEN [opcoes]

Opcoes:
  --base-url=URL              Base da API. Padrao: http://localhost/api/v1
  --token=TOKEN               Token completo da A001. Obrigatorio.
  --table=TABELA              Tabela para schema/listagem. Opcional.
  --filter-field=CAMPO        Campo para filtro de listagem. Opcional.
  --filter-value=VALOR        Valor do filtro. Opcional.
  --validate-table=TABELA     Tabela para POST /validate. Opcional.
  --validate-json=JSON        Payload JSON para /validate. Opcional.
  --expect-put-405=TABELA:ID  Testa PUT bloqueado (405) para a tabela/id.
  --expect-delete-405=TABELA:ID
                              Testa DELETE bloqueado (405) para a tabela/id.
  --expect-patch-error=TABELA:ID:CODIGO
                              Testa PATCH com erro esperado sem gravar no banco.
  --patch-json=JSON           Payload JSON usado no teste de PATCH esperado.
  --header-mode=api-token     Usa X-API-Token. Padrao.
  --header-mode=bearer        Usa Authorization: Bearer.
  --help                      Exibe esta ajuda.

Exemplo:
  php rest_api/bin/smoke_test.php \\
    --token=hrd_live_xxx \\
    --table=D001 \\
    --filter-field=D001_Id \\
    --filter-value=2264 \\
    --validate-table=T005 \\
    --validate-json='{"T005_D024_Id":123,"T005_Observacao_1":"Teste"}' \\
    --expect-put-405=T005:1 \\
    --expect-delete-405=T005:1 \\
    --expect-patch-error=T005:1:PRIMARY_KEY_CHANGE_NOT_ALLOWED \\
    --patch-json='{"T005_Id":1}'
TXT;

$options = getopt('', [
    'base-url::',
    'token:',
    'table::',
    'filter-field::',
    'filter-value::',
    'validate-table::',
    'validate-json::',
    'expect-put-405::',
    'expect-delete-405::',
    'expect-patch-error::',
    'patch-json::',
    'header-mode::',
    'help',
]);

if (isset($options['help'])) {
    fwrite(STDOUT, $usage . PHP_EOL);
    exit(0);
}

$baseUrl = rtrim((string) ($options['base-url'] ?? 'http://localhost/api/v1'), '/');
$token = trim((string) ($options['token'] ?? ''));
$table = trim((string) ($options['table'] ?? ''));
$filterField = trim((string) ($options['filter-field'] ?? ''));
$filterValue = isset($options['filter-value']) ? (string) $options['filter-value'] : '';
$validateTable = trim((string) ($options['validate-table'] ?? ''));
$validateJson = trim((string) ($options['validate-json'] ?? ''));
$expectPut405 = trim((string) ($options['expect-put-405'] ?? ''));
$expectDelete405 = trim((string) ($options['expect-delete-405'] ?? ''));
$expectPatchError = trim((string) ($options['expect-patch-error'] ?? ''));
$patchJson = trim((string) ($options['patch-json'] ?? ''));
$headerMode = strtolower(trim((string) ($options['header-mode'] ?? 'api-token')));

if ($token === '') {
    fwrite(STDERR, "Erro: informe --token.\n\n" . $usage . PHP_EOL);
    exit(1);
}

if (!in_array($headerMode, ['api-token', 'bearer'], true)) {
    fwrite(STDERR, "Erro: --header-mode deve ser api-token ou bearer.\n");
    exit(1);
}

/**
 * @return array<int, string>
 */
function buildAuthHeaders(string $token, string $headerMode): array
{
    if ($headerMode === 'bearer') {
        return [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
        ];
    }

    return [
        'X-API-Token: ' . $token,
        'Accept: application/json',
    ];
}

/**
 * @param array<int, string> $headers
 * @return array{status:int,body:string,json:?array}
 */
function httpRequest(string $method, string $url, array $headers, ?string $body = null): array
{
    $context = stream_context_create([
        'http' => [
            'method' => strtoupper($method),
            'ignore_errors' => true,
            'header' => implode("\r\n", $headers) . "\r\n",
            'content' => $body ?? '',
            'timeout' => 30,
        ],
    ]);

    $responseBody = @file_get_contents($url, false, $context);
    $responseHeaders = $http_response_header ?? [];
    $status = 0;

    foreach ($responseHeaders as $line) {
        if (preg_match('/HTTP\/\d+(?:\.\d+)?\s+(\d{3})/', $line, $matches)) {
            $status = (int) $matches[1];
            break;
        }
    }

    $bodyText = is_string($responseBody) ? $responseBody : '';
    $json = null;
    if ($bodyText !== '') {
        $decoded = json_decode($bodyText, true);
        if (is_array($decoded)) {
            $json = $decoded;
        }
    }

    return [
        'status' => $status,
        'body' => $bodyText,
        'json' => $json,
    ];
}

function assertSuccess(string $label, array $response, int $expectedStatus = 200): void
{
    $status = (int) ($response['status'] ?? 0);
    $json = $response['json'] ?? null;
    $ok = $status === $expectedStatus
        && is_array($json)
        && (($json['success'] ?? false) === true);

    if ($ok) {
        fwrite(STDOUT, "[OK] {$label}\n");
        return;
    }

    $message = '[FALHA] ' . $label . ' - HTTP ' . $status;
    if (is_array($json) && isset($json['error']['code'])) {
        $message .= ' - ' . $json['error']['code'];
    }
    fwrite(STDERR, $message . PHP_EOL);
    if (!empty($response['body'])) {
        fwrite(STDERR, $response['body'] . PHP_EOL);
    }
    exit(1);
}

/**
 * @return array{table:string,id:string}
 */
function parseTableAndId(string $input, string $optionName): array
{
    $parts = explode(':', $input, 2);
    if (count($parts) !== 2 || trim($parts[0]) === '' || trim($parts[1]) === '') {
        fwrite(STDERR, "Erro: {$optionName} deve usar o formato TABELA:ID.\n");
        exit(1);
    }

    return [
        'table' => trim($parts[0]),
        'id' => trim($parts[1]),
    ];
}

/**
 * @return array{table:string,id:string,code:string}
 */
function parsePatchExpectation(string $input): array
{
    $parts = explode(':', $input, 3);
    if (count($parts) !== 3 || trim($parts[0]) === '' || trim($parts[1]) === '' || trim($parts[2]) === '') {
        fwrite(STDERR, "Erro: --expect-patch-error deve usar o formato TABELA:ID:CODIGO.\n");
        exit(1);
    }

    return [
        'table' => trim($parts[0]),
        'id' => trim($parts[1]),
        'code' => trim($parts[2]),
    ];
}

function assertExpectedError(string $label, array $response, int $expectedStatus, string $expectedCode): void
{
    $status = (int) ($response['status'] ?? 0);
    $json = $response['json'] ?? null;
    $code = is_array($json) ? (string) ($json['error']['code'] ?? '') : '';
    $ok = $status === $expectedStatus
        && is_array($json)
        && (($json['success'] ?? true) === false)
        && $code === $expectedCode;

    if ($ok) {
        fwrite(STDOUT, "[OK] {$label}\n");
        return;
    }

    $message = '[FALHA] ' . $label . ' - HTTP ' . $status;
    if ($code !== '') {
        $message .= ' - ' . $code;
    }
    $message .= ' (esperado ' . $expectedStatus . ' / ' . $expectedCode . ')';
    fwrite(STDERR, $message . PHP_EOL);
    if (!empty($response['body'])) {
        fwrite(STDERR, $response['body'] . PHP_EOL);
    }
    exit(1);
}

$headers = buildAuthHeaders($token, $headerMode);

fwrite(STDOUT, "Smoke test REST API\n");
fwrite(STDOUT, "Base URL: {$baseUrl}\n");
fwrite(STDOUT, "Header mode: {$headerMode}\n\n");

$authMe = httpRequest('GET', $baseUrl . '/auth/me', $headers);
assertSuccess('GET /auth/me', $authMe, 200);

$tables = httpRequest('GET', $baseUrl . '/tables', $headers);
assertSuccess('GET /tables', $tables, 200);

if ($table !== '') {
    $schema = httpRequest('GET', $baseUrl . '/tables/' . rawurlencode($table) . '/schema', $headers);
    assertSuccess('GET /tables/' . $table . '/schema', $schema, 200);

    $recordsUrl = $baseUrl . '/tables/' . rawurlencode($table) . '?limit=1';
    if ($filterField !== '' && $filterValue !== '') {
        $recordsUrl .= '&' . http_build_query([
            'filter' => [
                $filterField => [
                    'eq' => $filterValue,
                ],
            ],
        ]);
    }

    $records = httpRequest('GET', $recordsUrl, $headers);
    assertSuccess('GET /tables/' . $table, $records, 200);
}

if ($validateTable !== '' && $validateJson !== '') {
    $decodedValidate = json_decode($validateJson, true);
    if (!is_array($decodedValidate)) {
        fwrite(STDERR, "Erro: --validate-json precisa ser um JSON válido.\n");
        exit(1);
    }

    $validateHeaders = $headers;
    $validateHeaders[] = 'Content-Type: application/json';
    $validate = httpRequest(
        'POST',
        $baseUrl . '/tables/' . rawurlencode($validateTable) . '/validate',
        $validateHeaders,
        json_encode($decodedValidate, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
    assertSuccess('POST /tables/' . $validateTable . '/validate', $validate, 200);
}

if ($expectPut405 !== '') {
    $putTarget = parseTableAndId($expectPut405, '--expect-put-405');
    $putHeaders = $headers;
    $putHeaders[] = 'Content-Type: application/json';
    $put = httpRequest(
        'PUT',
        $baseUrl . '/tables/' . rawurlencode($putTarget['table']) . '/' . rawurlencode($putTarget['id']),
        $putHeaders,
        '{}'
    );
    assertExpectedError(
        'PUT /tables/' . $putTarget['table'] . '/' . $putTarget['id'],
        $put,
        405,
        'METHOD_NOT_ALLOWED'
    );
}

if ($expectDelete405 !== '') {
    $deleteTarget = parseTableAndId($expectDelete405, '--expect-delete-405');
    $delete = httpRequest(
        'DELETE',
        $baseUrl . '/tables/' . rawurlencode($deleteTarget['table']) . '/' . rawurlencode($deleteTarget['id']),
        $headers
    );
    assertExpectedError(
        'DELETE /tables/' . $deleteTarget['table'] . '/' . $deleteTarget['id'],
        $delete,
        405,
        'METHOD_NOT_ALLOWED'
    );
}

if ($expectPatchError !== '') {
    if ($patchJson === '') {
        fwrite(STDERR, "Erro: --patch-json e obrigatorio quando usar --expect-patch-error.\n");
        exit(1);
    }

    $decodedPatch = json_decode($patchJson, true);
    if (!is_array($decodedPatch)) {
        fwrite(STDERR, "Erro: --patch-json precisa ser um JSON válido.\n");
        exit(1);
    }

    $patchTarget = parsePatchExpectation($expectPatchError);
    $patchHeaders = $headers;
    $patchHeaders[] = 'Content-Type: application/json';
    $patch = httpRequest(
        'PATCH',
        $baseUrl . '/tables/' . rawurlencode($patchTarget['table']) . '/' . rawurlencode($patchTarget['id']),
        $patchHeaders,
        json_encode($decodedPatch, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
    assertExpectedError(
        'PATCH /tables/' . $patchTarget['table'] . '/' . $patchTarget['id'],
        $patch,
        400,
        $patchTarget['code']
    );
}

fwrite(STDOUT, "\nSmoke test concluído com sucesso.\n");
