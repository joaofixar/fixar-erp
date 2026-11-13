<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');
ob_start();

const INECEL_ROOT = __DIR__ . '/../..';

$inecelResponseSent = false;

register_shutdown_function(function () use (&$inecelResponseSent): void {
    if ($inecelResponseSent) {
        return;
    }

    $error = error_get_last();
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    $buffer = trim((string) ob_get_contents());

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    if ($error && in_array($error['type'], $fatalTypes, true)) {
        echo json_encode([
            'ok' => false,
            'message' => 'Erro fatal no servidor: ' . $error['message'] . ' em ' . basename((string) $error['file']) . ':' . (int) $error['line'],
        ]);
        return;
    }

    if ($buffer !== '') {
        echo json_encode([
            'ok' => false,
            'message' => $buffer,
        ]);
        return;
    }

    echo json_encode([
        'ok' => false,
        'message' => 'Resposta vazia do servidor.',
    ]);
});

function inecel_json(array $payload): void
{
    global $inecelResponseSent;
    $inecelResponseSent = true;
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    echo json_encode($payload);
    exit;
}

function inecel_normalize_number(string $value): float
{
    $value = trim($value);
    if ($value === '') {
        return 0.0;
    }
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);
    return (float) $value;
}

function inecel_format_number($value, int $decimals = 2): string
{
    return number_format((float) $value, $decimals, ',', '.');
}

function inecel_fetch_row(mysqli $conn, int $t066Id, int $empresaId): ?array
{
    if ($t066Id <= 0 || $empresaId <= 0) {
        return null;
    }

    $sql = <<<SQL
SELECT
    T066.T066_Id,
    IFNULL(T066.T066_Quantidade_Estoque, 0) AS T066_Quantidade_Estoque,
    IFNULL(D009.D009_Quantidade_Estoque, 0) AS D009_Quantidade_Estoque,
    IFNULL(D009.D009_Quantidade_Estoque_Liquido, 0) AS D009_Quantidade_Estoque_Liquido,
    D009_Quantidade_Pedido(D009.D009_Id,1) AS reserva,
    D004.D004_Local,
    D009.D009_Id,
    D001.D001_Codigo_Produto,
    D001.D001_Descricao_Produto
FROM T066
LEFT JOIN D009 ON D009.D009_Id = T066.T066_D009_Id
LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
LEFT JOIN D004 ON D004.D004_Id = T066.T066_D004_Id
WHERE T066.T066_Id = '{$t066Id}'
  AND D009.D009_C004_Id = '{$empresaId}'
  AND D004.D004_Flag_Somar_Estoque_Fisico = 'S'
LIMIT 1
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    return $row ?: null;
}

function inecel_fetch_summary(mysqli $conn, int $d009Id, int $empresaId): ?array
{
    if ($d009Id <= 0 || $empresaId <= 0) {
        return null;
    }

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
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        return null;
    }

    $row['total_fisico_formatado'] = inecel_format_number($row['total_fisico'] ?? 0, 2);
    $row['total_liquido_formatado'] = inecel_format_number($row['total_liquido'] ?? 0, 2);
    $row['reserva_formatado'] = inecel_format_number($row['reserva'] ?? 0, 2);
    $row['quantidade_sem_pintura_formatado'] = inecel_format_number($row['quantidade_sem_pintura'] ?? 0, 2);
    $row['quantidade_pintado_formatado'] = inecel_format_number($row['quantidade_pintado'] ?? 0, 2);

    return $row;
}

if (empty($_SESSION['Usuario_Atual']) || empty($_SESSION['Empresa_Atual'])) {
    inecel_json(['ok' => false, 'message' => 'Sessao expirada. Faca login novamente.']);
}

chdir(INECEL_ROOT);
require_once INECEL_ROOT . '/bibliotecas/confUsuario.php';
include_once INECEL_ROOT . '/configuracoes/sysConfig.php';
require_once INECEL_ROOT . '/bibliotecas/funcoesGerais.php';
require_once INECEL_ROOT . '/bibliotecas/conexaoBanco.php';

global $g;
$g['pathRaiz'] = $g['pathRaiz'] ?? $confUsuario['pathRaiz'];
$g['urlRaiz'] = $g['urlRaiz'] ?? $confUsuario['urlRaiz'];
$g['empresaAtual'] = $g['empresaAtual'] ?? (int) ($_SESSION['Empresa_Atual'] ?? 0);
$g['usuarioAtual'] = $g['usuarioAtual'] ?? (int) ($_SESSION['Usuario_Atual'] ?? 0);
$g['modoDesenvolvedor'] = $g['modoDesenvolvedor'] ?? (!empty($_SESSION['modoDesenvolvedor']));
$g['modoDesenvolvedorApp'] = $g['modoDesenvolvedorApp'] ?? ($_SESSION['modoDesenvolvedorApp'] ?? false);
$g['pathDados'] = $g['pathDados'] ?? ('dados_usuarios/' . $confUsuario['dbDatabase'] . '/');
$g['tabelasAuditoria'] = $g['tabelasAuditoria'] ?? [];

if (empty($g['C007']) && $g['usuarioAtual'] > 0) {
    $sqlUsuario = "SELECT * FROM C007 WHERE C007_Id = '" . (int) $g['usuarioAtual'] . "' LIMIT 1";
    $resultUsuario = mysqli_query($g['conexaoBanco'], $sqlUsuario);
    if ($resultUsuario) {
        $g['C007'] = mysqli_fetch_assoc($resultUsuario) ?: [];
    }
}

$conn = $g['conexaoBanco'];
$empresaId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$usuarioId = (int) ($_SESSION['Usuario_Atual'] ?? 0);
$t066Id = (int) ($_POST['t066_id'] ?? 0);
$d009Id = (int) ($_POST['d009_id'] ?? 0);
$quantidade = inecel_normalize_number((string) ($_POST['quantidade'] ?? ''));
$operacao = strtoupper(trim((string) ($_POST['operacao'] ?? '')));
$quantidadeDigitada = $quantidade;

if ($t066Id <= 0 || $d009Id <= 0) {
    inecel_json(['ok' => false, 'message' => 'Produto ou locacao nao informado.']);
}

if ($quantidade <= 0) {
    inecel_json(['ok' => false, 'message' => 'Informe uma quantidade maior que zero.']);
}

if (!in_array($operacao, ['E', 'S'], true)) {
    inecel_json(['ok' => false, 'message' => 'Operacao invalida.']);
}

$sqlProduto = <<<SQL
SELECT
    D004.D004_Id,
    D004.D004_Local,
    D001.D001_Codigo_Produto,
    D001.D001_Descricao_Produto,
    IFNULL(D001.D001_Qte_Embalagem, 0) AS D001_Qte_Embalagem,
    T066.T066_Id,
    T066.T066_Quantidade_Estoque,
    D009.D009_Id,
    D049.D049_Flag_Tipo
FROM D009
LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
LEFT JOIN T066 ON T066.T066_Id = '{$t066Id}' AND T066.T066_D009_Id = D009.D009_Id
LEFT JOIN D004 ON D004.D004_Id = T066.T066_D004_Id
WHERE D009.D009_Id = '{$d009Id}'
  AND D009.D009_C004_Id = '{$empresaId}'
  AND IFNULL(D049.D049_Flag_Tipo, '') NOT IN ('CP', 'MT')
  AND D004.D004_Flag_Somar_Estoque_Fisico = 'S'
LIMIT 1
SQL;

$resultProduto = mysqli_query($conn, $sqlProduto);
if (!$resultProduto) {
    inecel_json(['ok' => false, 'message' => 'Erro ao buscar produto: ' . mysqli_error($conn)]);
}

$produto = mysqli_fetch_assoc($resultProduto);
if (!$produto || empty($produto['T066_Id'])) {
    inecel_json(['ok' => false, 'message' => 'Locacao do produto nao encontrada para esta empresa.']);
}

$quantidadeEmbalagem = (float) ($produto['D001_Qte_Embalagem'] ?? 0);
if ($quantidadeEmbalagem > 0) {
    if (abs($quantidadeDigitada - floor($quantidadeDigitada)) > 0.00001) {
        inecel_json(['ok' => false, 'message' => 'Para este produto informe somente quantidade inteira de embalagens.']);
    }
    $quantidade = $quantidadeDigitada * $quantidadeEmbalagem;
}

$estoqueAtual = (float) ($produto['T066_Quantidade_Estoque'] ?? 0);
if ($operacao === 'S' && ($estoqueAtual - $quantidade) < -0.00001) {
    inecel_json(['ok' => false, 'message' => 'Nao e permitido deixar estoque negativo na locacao ' . ($produto['D004_Local'] ?? '-') . '.']);
}

$descricaoOperacao = [
    'E' => 'ENT-COLETOR',
    'S' => 'SAI-COLETOR',
][$operacao];

$primeiroNome = trim((string) ($g['C007']['C007_Primeiro_Nome'] ?? 'Usuario'));
$primeiroNome = substr($primeiroNome, 0, 10);
if ($primeiroNome !== '') {
    $descricaoOperacao .= ' (' . $primeiroNome . ')';
}

$descricaoOperacaoSql = mysqli_real_escape_string($conn, $descricaoOperacao);
$quantidadeSql = mysqli_real_escape_string($conn, (string) $quantidade);
$codigoProdutoSql = mysqli_real_escape_string($conn, (string) ($produto['D001_Codigo_Produto'] ?? ''));
$delta = $operacao === 'E' ? $quantidade : ($quantidade * -1);

mysqli_begin_transaction($conn);

$sqlT001 = <<<SQL
INSERT INTO T001 (
    T001_D024_Id_Cliente,
    T001_D024_Id,
    T001_D009_Id,
    T001_C004_Id,
    T001_Codigo_Produto,
    T001_Data_Lancamento,
    T001_Descricao_Operacao,
    T001_Quantidade,
    T001_Flag_Complemento,
    T001_Flag_Operacao,
    T001_Valor_Preco_Unitario,
    T001_Observacao,
    T001_Valor_Custo_Unitario,
    T001_Flag_Atualiza_Custo,
    T001_Quantidade_Saldo,
    T001_T066_Id,
    T001_D004_Id
) VALUES (
    '0',
    '0',
    '{$d009Id}',
    '{$empresaId}',
    '{$codigoProdutoSql}',
    CURRENT_DATE(),
    '{$descricaoOperacaoSql}',
    '{$quantidadeSql}',
    '',
    '{$operacao}',
    '0.00000',
    '',
    '0.00000',
    'N',
    '0',
    '{$t066Id}',
    '{$produto['D004_Id']}'
)
SQL;

if (!mysqli_query($conn, $sqlT001)) {
    mysqli_rollback($conn);
    inecel_json(['ok' => false, 'message' => 'Erro ao gravar movimento de estoque: ' . mysqli_error($conn)]);
}

$t001Id = (int) mysqli_insert_id($conn);
if ($t001Id <= 0) {
    mysqli_rollback($conn);
    inecel_json(['ok' => false, 'message' => 'Nao foi possivel identificar o movimento gravado.']);
}

try {
    require_once INECEL_ROOT . '/sistema/apps.php';
    require_once INECEL_ROOT . '/sistema/confSistema.php';
    require_once INECEL_ROOT . '/bibliotecas/classes/CAD002.php';

    if (empty($g['conf']) || !is_object($g['conf'])) {
        $g['conf'] = new \hardness\confSistema();
    }

    $CAD002 = new \hardness\CAD002();
    $dados = [
        'T001_Id' => $t001Id,
        'T001_Quantidade' => $quantidade,
        'T066_Quantidade_Estoque' => $produto['T066_Quantidade_Estoque'] ?? 0,
        'T001_Flag_Operacao' => $operacao,
    ];

    $CAD002->triggerAposInsercaoT001($dados);
    $CAD002->D001_reprocessa_historico(false, $d009Id, 50, true);
} catch (Throwable $throwable) {
    mysqli_rollback($conn);
    inecel_json([
        'ok' => false,
        'message' => 'Movimentacao cancelada ao reprocessar historico: ' . $throwable->getMessage(),
    ]);
}

$sqlResetBalanco = "
UPDATE T066
   SET T066_Quantidade_Balanco      = '0.00',
       T066_Data_Balanco            = CURDATE(),
       T066_Frequencia_Apos_Balanco = 0,
       T066_Flag_Balanco            = 'N',
       T066_C007_Id_Marcou_Balanco  = ''
 WHERE T066_Id = '{$t066Id}'";

if (!mysqli_query($conn, $sqlResetBalanco)) {
    mysqli_rollback($conn);
    inecel_json(['ok' => false, 'message' => 'Erro ao atualizar dados de balanco da locacao: ' . mysqli_error($conn)]);
}

mysqli_commit($conn);

$rowAtualizada = inecel_fetch_row($conn, $t066Id, $empresaId);
if (!$rowAtualizada) {
    inecel_json(['ok' => true, 'message' => 'Movimentacao registrada com sucesso.']);
}

$rowAtualizada['T066_Quantidade_Estoque_formatado'] = inecel_format_number($rowAtualizada['T066_Quantidade_Estoque'] ?? 0, 2);
$rowAtualizada['D009_Quantidade_Estoque_formatado'] = inecel_format_number($rowAtualizada['D009_Quantidade_Estoque'] ?? 0, 2);
$rowAtualizada['D009_Quantidade_Estoque_Liquido_formatado'] = inecel_format_number($rowAtualizada['D009_Quantidade_Estoque_Liquido'] ?? 0, 2);
$rowAtualizada['reserva_formatado'] = inecel_format_number($rowAtualizada['reserva'] ?? 0, 2);
$summaryAtualizado = [
    'total_fisico' => $rowAtualizada['D009_Quantidade_Estoque'] ?? 0,
    'total_liquido' => $rowAtualizada['D009_Quantidade_Estoque_Liquido'] ?? 0,
    'reserva' => $rowAtualizada['reserva'] ?? 0,
    'total_fisico_formatado' => $rowAtualizada['D009_Quantidade_Estoque_formatado'] ?? inecel_format_number(0, 2),
    'total_liquido_formatado' => $rowAtualizada['D009_Quantidade_Estoque_Liquido_formatado'] ?? inecel_format_number(0, 2),
    'reserva_formatado' => $rowAtualizada['reserva_formatado'] ?? inecel_format_number(0, 2),
];

inecel_json([
    'ok' => true,
    'message' => 'Movimentacao registrada com sucesso.',
    'row' => $rowAtualizada,
    'summary' => $summaryAtualizado,
    't066_id' => $t066Id,
    'd009_id' => $d009Id,
    'usuario_id' => $usuarioId,
]);
