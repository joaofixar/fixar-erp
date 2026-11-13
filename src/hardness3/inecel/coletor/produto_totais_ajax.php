<?php
declare(strict_types=1);

session_start();

const INECEL_ROOT = __DIR__ . '/../..';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['Usuario_Atual']) || empty($_SESSION['Empresa_Atual'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Sessao expirada.']);
    exit;
}

chdir(INECEL_ROOT);
require_once INECEL_ROOT . '/bibliotecas/confUsuario.php';
include_once INECEL_ROOT . '/configuracoes/sysConfig.php';
require_once INECEL_ROOT . '/bibliotecas/funcoesGerais.php';
require_once INECEL_ROOT . '/bibliotecas/conexaoBanco.php';

function inecel_totais_db(): mysqli
{
    global $g;
    return $g['conexaoBanco'];
}

function inecel_totais_format_number($value, int $decimals = 2): string
{
    return number_format((float) $value, $decimals, ',', '.');
}

$d009Id = (int) ($_GET['d009_id'] ?? $_POST['d009_id'] ?? 0);
$empresaId = (int) ($_SESSION['Empresa_Atual'] ?? 0);

if ($d009Id <= 0 || $empresaId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Produto invalido.']);
    exit;
}

$conn = inecel_totais_db();

$sql = <<<SQL
select 
total_fisico,
total_liquido,
reserva,
quantidade_pintado,
IFNULL((reserva - quantidade_pintado),0) as quantidade_sem_pintura 
from 
(
SELECT
    IFNULL(D009.D009_Quantidade_Estoque, 0) AS total_fisico,
    IFNULL(D009.D009_Quantidade_Estoque_Liquido, 0) AS total_liquido,
    D009_Quantidade_Pedido(D009.D009_Id,1) AS reserva,
    (
        SELECT IFNULL(SUM(IFNULL(T006.T006_Quantidade, 0)), 0)
        FROM T006
        LEFT JOIN T005 ON T005.T005_Id = T006.T006_T005_Id
        left join T006A on T006A_T006_Id=T006_Id
        WHERE T006.T006_D009_Id = D009.D009_Id
          AND T005.T005_C004_Id = '{$empresaId}'
          and T006A.T006A_SMT006_Id > 0
          AND T005.T005_Flag_Status IN (1, 2)
    ) AS quantidade_pintado
FROM D009
WHERE D009.D009_Id = '{$d009Id}'
  AND D009.D009_C004_Id = '{$empresaId}'
LIMIT 1)as Estoque
SQL;

$result = mysqli_query($conn, $sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Falha ao buscar totais.']);
    exit;
}

$row = mysqli_fetch_assoc($result) ?: [];
$quantidadePintado = (float) ($row['quantidade_pintado'] ?? 0);
$quantidadeSemPintura = (float) ($row['quantidade_sem_pintura'] ?? 0);

echo json_encode([
    'ok' => true,
    'totais' => [
        'quantidade_pintado' => $quantidadePintado,
        'quantidade_sem_pintura' => $quantidadeSemPintura,
        'quantidade_pintado_formatado' => inecel_totais_format_number($quantidadePintado, 2),
        'quantidade_sem_pintura_formatado' => inecel_totais_format_number($quantidadeSemPintura, 2),
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
