<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este script deve ser executado via CLI.\n");
    exit(1);
}

$options = getopt('', array('file::', 'top::', 'request::', 'json'));
$baseDir = dirname(__FILE__) . '/../../tmp';
$defaultFile = $baseDir . '/apontamento_sql_tempo.log';
$file = isset($options['file']) && $options['file'] !== false && $options['file'] !== ''
    ? $options['file']
    : $defaultFile;
$top = isset($options['top']) ? max(1, (int) $options['top']) : 10;
$requestFilter = isset($options['request']) ? trim((string) $options['request']) : '';
$jsonOnly = array_key_exists('json', $options);

if (!is_file($file)) {
    fwrite(STDERR, "Arquivo de log nao encontrado: {$file}\n");
    exit(1);
}

$handle = fopen($file, 'r');
if (!$handle) {
    fwrite(STDERR, "Nao foi possivel abrir o arquivo: {$file}\n");
    exit(1);
}

$stats = array(
    'file' => $file,
    'lines_total' => 0,
    'lines_invalid' => 0,
    'events_total' => 0,
    'sql_total' => 0,
    'sql_time_ms' => 0.0,
    'by_operation' => array(),
    'slowest_sql' => array(),
    'requests' => array(),
    'slowest_blocks' => array(),
);

while (($line = fgets($handle)) !== false) {
    $stats['lines_total']++;
    $line = trim($line);
    if ($line === '') {
        continue;
    }

    $row = json_decode($line, true);
    if (!is_array($row)) {
        $stats['lines_invalid']++;
        continue;
    }

    $requestId = isset($row['request_id']) ? (string) $row['request_id'] : 'sem_request_id';
    if ($requestFilter !== '' && $requestId !== $requestFilter) {
        continue;
    }

    $stats['events_total']++;

    if (!isset($stats['requests'][$requestId])) {
        $stats['requests'][$requestId] = array(
            'request_id' => $requestId,
            'uri' => isset($row['uri']) ? (string) $row['uri'] : '',
            'metodo' => isset($row['metodo']) ? (string) $row['metodo'] : '',
            'acao' => isset($row['acao']) ? (string) $row['acao'] : '',
            'usuario' => isset($row['usuario']) ? (int) $row['usuario'] : 0,
            'empresa' => isset($row['empresa']) ? (int) $row['empresa'] : 0,
            'op_id' => isset($row['op_id']) ? (int) $row['op_id'] : 0,
            'item_id' => isset($row['item_id']) ? (int) $row['item_id'] : 0,
            'sql_count' => 0,
            'sql_time_ms' => 0.0,
            'max_sql_ms' => 0.0,
            'request_duration_ms' => null,
            'blocks' => array(),
        );
    }

    if (($row['evento'] ?? '') === 'sql') {
        $operation = trim((string) (($row['operacao'] ?? '') . ' ' . ($row['tabela'] ?? '')));
        $duration = isset($row['duracao_ms']) ? (float) $row['duracao_ms'] : 0.0;
        $sql = isset($row['sql']) ? (string) $row['sql'] : '';

        $stats['sql_total']++;
        $stats['sql_time_ms'] += $duration;
        $stats['requests'][$requestId]['sql_count']++;
        $stats['requests'][$requestId]['sql_time_ms'] += $duration;
        if ($duration > $stats['requests'][$requestId]['max_sql_ms']) {
            $stats['requests'][$requestId]['max_sql_ms'] = $duration;
        }

        if (!isset($stats['by_operation'][$operation])) {
            $stats['by_operation'][$operation] = array(
                'operation' => $operation,
                'count' => 0,
                'time_ms' => 0.0,
                'max_ms' => 0.0,
            );
        }

        $stats['by_operation'][$operation]['count']++;
        $stats['by_operation'][$operation]['time_ms'] += $duration;
        if ($duration > $stats['by_operation'][$operation]['max_ms']) {
            $stats['by_operation'][$operation]['max_ms'] = $duration;
        }

        $stats['slowest_sql'][] = array(
            'request_id' => $requestId,
            'operation' => $operation,
            'duracao_ms' => $duration,
            'sql' => $sql,
            'ok' => isset($row['ok']) ? (int) $row['ok'] : null,
            'erro' => isset($row['erro']) ? (string) $row['erro'] : '',
            'item_id' => isset($row['item_id']) ? (int) $row['item_id'] : 0,
            'op_id' => isset($row['op_id']) ? (int) $row['op_id'] : 0,
        );
    }

    if (($row['evento'] ?? '') === 'bloco') {
        $stats['requests'][$requestId]['blocks'][] = array(
            'bloco' => isset($row['bloco']) ? (string) $row['bloco'] : '',
            'duracao_ms' => isset($row['duracao_ms']) ? (float) $row['duracao_ms'] : 0.0,
            'ok' => isset($row['ok']) ? (int) $row['ok'] : null,
            'erro' => isset($row['erro']) ? (string) $row['erro'] : '',
        );

        $stats['slowest_blocks'][] = array(
            'request_id' => $requestId,
            'bloco' => isset($row['bloco']) ? (string) $row['bloco'] : '',
            'duracao_ms' => isset($row['duracao_ms']) ? (float) $row['duracao_ms'] : 0.0,
            'ok' => isset($row['ok']) ? (int) $row['ok'] : null,
            'erro' => isset($row['erro']) ? (string) $row['erro'] : '',
            'item_id' => isset($row['item_id']) ? (int) $row['item_id'] : 0,
            'op_id' => isset($row['op_id']) ? (int) $row['op_id'] : 0,
        );
    }

    if (($row['evento'] ?? '') === 'resumo_request') {
        $stats['requests'][$requestId]['request_duration_ms'] = isset($row['duracao_request_ms']) ? (float) $row['duracao_request_ms'] : null;
    }
}

fclose($handle);

foreach ($stats['by_operation'] as $key => $operation) {
    $count = max(1, (int) $operation['count']);
    $stats['by_operation'][$key]['avg_ms'] = $operation['time_ms'] / $count;
}

usort($stats['slowest_sql'], function ($a, $b) {
    if ($a['duracao_ms'] == $b['duracao_ms']) {
        return 0;
    }
    return ($a['duracao_ms'] < $b['duracao_ms']) ? 1 : -1;
});

usort($stats['slowest_blocks'], function ($a, $b) {
    if ($a['duracao_ms'] == $b['duracao_ms']) {
        return 0;
    }
    return ($a['duracao_ms'] < $b['duracao_ms']) ? 1 : -1;
});

$requests = array_values($stats['requests']);
usort($requests, function ($a, $b) {
    $aValue = $a['request_duration_ms'] !== null ? $a['request_duration_ms'] : $a['sql_time_ms'];
    $bValue = $b['request_duration_ms'] !== null ? $b['request_duration_ms'] : $b['sql_time_ms'];
    if ($aValue == $bValue) {
        return 0;
    }
    return ($aValue < $bValue) ? 1 : -1;
});

$operations = array_values($stats['by_operation']);
usort($operations, function ($a, $b) {
    if ($a['time_ms'] == $b['time_ms']) {
        return 0;
    }
    return ($a['time_ms'] < $b['time_ms']) ? 1 : -1;
});

$result = array(
    'arquivo' => $stats['file'],
    'linhas_total' => $stats['lines_total'],
    'linhas_invalidas' => $stats['lines_invalid'],
    'eventos_total' => $stats['events_total'],
    'sql_total' => $stats['sql_total'],
    'sql_time_ms' => round($stats['sql_time_ms'], 3),
    'operacoes' => $operations,
    'queries_lentas' => array_slice($stats['slowest_sql'], 0, $top),
    'blocos_lentos' => array_slice($stats['slowest_blocks'], 0, $top),
    'requests_lentas' => array_slice($requests, 0, $top),
);

if ($jsonOnly) {
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit(0);
}

function formatMs($value)
{
    return number_format((float) $value, 3, ',', '.');
}

function printLine($text = '')
{
    echo $text . PHP_EOL;
}

printLine('Arquivo: ' . $result['arquivo']);
printLine('Linhas lidas: ' . $result['linhas_total'] . ' | invalidas: ' . $result['linhas_invalidas'] . ' | eventos considerados: ' . $result['eventos_total']);
printLine('SQLs medidos: ' . $result['sql_total'] . ' | tempo SQL acumulado: ' . formatMs($result['sql_time_ms']) . ' ms');
printLine('');
printLine('Operacoes por tempo acumulado:');
foreach ($result['operacoes'] as $operation) {
    printLine(sprintf(
        '- %s | qtd=%d | total=%s ms | media=%s ms | max=%s ms',
        $operation['operation'] !== '' ? $operation['operation'] : '(sem nome)',
        $operation['count'],
        formatMs($operation['time_ms']),
        formatMs($operation['avg_ms']),
        formatMs($operation['max_ms'])
    ));
}

printLine('');
printLine('Top ' . $top . ' queries mais lentas:');
foreach ($result['queries_lentas'] as $query) {
    printLine(sprintf(
        '- %s ms | %s | req=%s | op_id=%d | item_id=%d',
        formatMs($query['duracao_ms']),
        $query['operation'],
        $query['request_id'],
        $query['op_id'],
        $query['item_id']
    ));
    printLine('  SQL: ' . $query['sql']);
    if ($query['erro'] !== '') {
        printLine('  Erro: ' . $query['erro']);
    }
}

printLine('');
printLine('Top ' . $top . ' blocos mais lentos:');
foreach ($result['blocos_lentos'] as $block) {
    printLine(sprintf(
        '- %s ms | %s | req=%s | op_id=%d | item_id=%d',
        formatMs($block['duracao_ms']),
        $block['bloco'],
        $block['request_id'],
        $block['op_id'],
        $block['item_id']
    ));
    if ($block['erro'] !== '') {
        printLine('  Erro: ' . $block['erro']);
    }
}

printLine('');
printLine('Top ' . $top . ' requests mais lentas:');
foreach ($result['requests_lentas'] as $request) {
    $requestDuration = $request['request_duration_ms'] !== null
        ? formatMs($request['request_duration_ms']) . ' ms'
        : 'n/d';
    printLine(sprintf(
        '- req=%s | request=%s | sql=%s ms | qtd_sql=%d | max_sql=%s ms | op_id=%d | item_id=%d | acao=%s',
        $request['request_id'],
        $requestDuration,
        formatMs($request['sql_time_ms']),
        $request['sql_count'],
        formatMs($request['max_sql_ms']),
        $request['op_id'],
        $request['item_id'],
        $request['acao']
    ));
    if ($request['uri'] !== '') {
        printLine('  URI: ' . $request['uri']);
    }
}
