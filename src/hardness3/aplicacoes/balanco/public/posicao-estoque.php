<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function balanco_stock_position_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

$context = balanco_require_access();
if (!balanco_can_process()) {
    balanco_stock_position_response(['ok' => false, 'message' => 'Seu perfil não possui permissão para consultar a posição de estoque.'], 403);
}

$t066Id = (int) ($_GET['t066_id'] ?? 0);
if ($t066Id <= 0) {
    balanco_stock_position_response(['ok' => false, 'message' => 'Locação inválida.'], 400);
}

global $g;
$company = (int) $g['empresaAtual'];
$sql = "SELECT T066_Id, T066_Flag_Balanco, T066_C007_Id_Marcou_Balanco, T066_Quantidade_Estoque, T066_Quantidade_Estoque_Liquido, D009_D049_Id, D009_Quantidade_Estoque, D009_Quantidade_Estoque_Liquido, D009_Quantidade_Pedido(D009_Id, 1) AS D009_Quantidade_Pedido, D009_Quantidade_Pedido_Por_Locacao(D009_Id, T066_Id) AS T066_Quantidade_Pedido, COALESCE((SELECT SUM(T006_Empresa.T006_Quantidade_Separacao) FROM T006 AS T006_Empresa INNER JOIN T005 AS T005_Empresa ON T005_Empresa.T005_Id=T006_Empresa.T006_T005_Id LEFT JOIN T008 AS T008_Empresa ON T008_Empresa.T008_T006_Id=T006_Empresa.T006_Id WHERE T006_Empresa.T006_D009_Id=D009_Id AND T005_Empresa.T005_Flag_Status IN ('2', '10', '3') AND IFNULL(T005_Empresa.T005_T005_Id_Agrupado, 0)=0 AND T006_Empresa.T006_Flag_Status!='3' AND T008_Empresa.T008_Id IS NULL), 0) AS D009_Quantidade_Separado, COALESCE((SELECT SUM(T238_Locacao.T238_Quantidade_Separacao) FROM T238 AS T238_Locacao INNER JOIN T006 AS T006_Locacao ON T006_Locacao.T006_Id=T238_Locacao.T238_T006_Id INNER JOIN T005 AS T005_Locacao ON T005_Locacao.T005_Id=T238_Locacao.T238_T005_Id WHERE T006_Locacao.T006_D009_Id=D009_Id AND T238_Locacao.T238_T066_Id=T066_Id AND T005_Locacao.T005_Flag_Status IN ('2', '10', '3') AND IFNULL(T005_Locacao.T005_T005_Id_Agrupado, 0)=0 AND T006_Locacao.T006_Flag_Status!='3'), 0) AS T066_Quantidade_Separado FROM T066 LEFT JOIN D009 ON D009_Id=T066_D009_Id LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_Id='{$t066Id}' AND D009_C004_Id='{$company}' AND D004_C004_Id='{$company}' LIMIT 1";
$query = mysqli_query($g['conexaoBanco'], $sql);
$item = $query ? mysqli_fetch_assoc($query) : null;
if (!$item || $item['T066_Flag_Balanco'] !== 'S' || (int) $item['T066_C007_Id_Marcou_Balanco'] !== (int) $context['user_id']) {
    balanco_stock_position_response(['ok' => false, 'message' => 'A locação não está mais disponível para esta contagem.'], 403);
}

$productSupplierId = (int) $item['D009_D049_Id'];
$allCompanies = [
    'physical' => 0,
    'orders' => 0,
    'separated' => 0,
    'liquid' => 0,
];

if ($productSupplierId > 0) {
    $allCompaniesSql = "SELECT
            COALESCE(SUM(D009_Todas.D009_Quantidade_Estoque), 0) AS physical,
            COALESCE(SUM(D009_Quantidade_Pedido(D009_Todas.D009_Id, 1)), 0) AS orders,
            COALESCE(SUM(D009_Todas.D009_Quantidade_Estoque_Liquido), 0) AS liquid,
            COALESCE(SUM(COALESCE((SELECT SUM(T006_Todas.T006_Quantidade_Separacao)
                FROM T006 AS T006_Todas
                INNER JOIN T005 AS T005_Todas ON T005_Todas.T005_Id=T006_Todas.T006_T005_Id
                LEFT JOIN T008 AS T008_Todas ON T008_Todas.T008_T006_Id=T006_Todas.T006_Id
                WHERE T006_Todas.T006_D009_Id=D009_Todas.D009_Id
                  AND T005_Todas.T005_Flag_Status IN ('2', '10', '3')
                  AND IFNULL(T005_Todas.T005_T005_Id_Agrupado, 0)=0
                  AND T006_Todas.T006_Flag_Status!='3'
                  AND T008_Todas.T008_Id IS NULL), 0)), 0) AS separated
        FROM D009 AS D009_Todas
        WHERE D009_Todas.D009_D049_Id='{$productSupplierId}'";
    $allCompaniesQuery = mysqli_query($g['conexaoBanco'], $allCompaniesSql);
    if (!$allCompaniesQuery) {
        balanco_stock_position_response(['ok' => false, 'message' => 'Não foi possível consultar a posição de estoque de todas as empresas.'], 500);
    }
    $allCompanies = mysqli_fetch_assoc($allCompaniesQuery) ?: $allCompanies;
}

function balanco_stock_position_format($value): string
{
    return balanco_format_quantity($value, 2);
}

balanco_stock_position_response([
    'ok' => true,
    'updated_at' => date('H:i:s'),
    'company' => [
        'physical' => balanco_stock_position_format($item['D009_Quantidade_Estoque']),
        'orders' => balanco_stock_position_format($item['D009_Quantidade_Pedido']),
        'separated' => balanco_stock_position_format($item['D009_Quantidade_Separado']),
        'liquid' => balanco_stock_position_format($item['D009_Quantidade_Estoque_Liquido']),
    ],
    'location' => [
        'physical' => balanco_stock_position_format($item['T066_Quantidade_Estoque']),
        'orders' => balanco_stock_position_format($item['T066_Quantidade_Pedido']),
        'separated' => balanco_stock_position_format($item['T066_Quantidade_Separado']),
        'liquid' => balanco_stock_position_format($item['T066_Quantidade_Estoque_Liquido']),
    ],
    'all_companies' => [
        'physical' => balanco_stock_position_format($allCompanies['physical']),
        'orders' => balanco_stock_position_format($allCompanies['orders']),
        'separated' => balanco_stock_position_format($allCompanies['separated']),
        'liquid' => balanco_stock_position_format($allCompanies['liquid']),
    ],
]);
