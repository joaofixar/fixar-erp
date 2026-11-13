<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../src/FiscalConfiguration.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

try {
    reforma_tributaria_require_access();
    $db = reforma_tributaria_connect();
    if (!$db) throw new RuntimeException();
    try {
        $type = (string) ($_GET['tipo'] ?? '');
        $data = $type === '' ? ReformaTributariaFiscalConfiguration::summary($db) : ReformaTributariaFiscalConfiguration::records($db, $type, (string) ($_GET['filtro'] ?? 'all'), (string) ($_GET['busca'] ?? ''), (int) ($_GET['pagina'] ?? 1), (int) ($_GET['por_pagina'] ?? 50));
        if ($type !== '' && ($_GET['exportar'] ?? '') === 'csv' && !empty($data['available'])) {
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="reforma-tributaria-' . $type . '.csv"');
            echo "\xEF\xBB\xBF";
            $csv = fopen('php://output', 'wb');
            fputcsv($csv, ['ID', 'Código', 'Descrição', 'Situação', 'CST', 'Classificação', 'CST regular', 'Classificação regular'], ';');
            foreach ($data['records'] as $record) {
                $details = [];
                foreach ($record['details'] as $detail) $details[$detail['label']] = $detail['value'];
                fputcsv($csv, [$record['id'], $record['code'], $record['description'], $record['status'], $record['cst'] ?? '', $record['classification'] ?? '', $record['regular_cst'] ?? '', $record['regular_classification'] ?? ''], ';');
            }
            fclose($csv);
            exit;
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } finally {
        mysqli_close($db);
    }
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['error' => 'Não foi possível consultar as configurações fiscais agora.'], JSON_UNESCAPED_UNICODE);
}
