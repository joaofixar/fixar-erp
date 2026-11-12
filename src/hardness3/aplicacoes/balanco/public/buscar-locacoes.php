<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$context = balanco_require_access();
header('Content-Type: application/json; charset=utf-8');

$action = (string) ($_GET['action'] ?? '');
$d009Id = (int) ($_GET['d009_id'] ?? 0);
$ignoreT066Id = (int) ($_GET['ignore_t066_id'] ?? 0);
$search = trim((string) ($_GET['q'] ?? ''));

if (!in_array($action, ['include', 'update'], true) || !balanco_can_manage_locations($action)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Ação de locação não autorizada.']);
    exit;
}
if (!$context || $d009Id <= 0 || mb_strlen($search) < 2) {
    echo json_encode(['ok' => true, 'items' => []]);
    exit;
}

echo json_encode([
    'ok' => true,
    'items' => balanco_available_locations_for_product($d009Id, $ignoreT066Id, $search),
], JSON_UNESCAPED_UNICODE);
