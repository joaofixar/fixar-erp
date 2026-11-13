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

function inecel_format_number($value): string
{
    return number_format((float) $value, 4, ',', '.');
}

function inecel_buscar_locacoes_item(mysqli $conn, int $d009Id): array
{
    if ($d009Id <= 0) {
        return [];
    }

    $sqlLocacoes = <<<SQL
SELECT
    T066.T066_Id,
    T066.T066_Quantidade_Estoque,
    D004.D004_Local
FROM T066
LEFT JOIN D004 ON D004.D004_Id = T066.T066_D004_Id
LEFT JOIN D004A ON D004A.D004A_D004_Id = D004.D004_Id
WHERE T066.T066_D009_Id = '{$d009Id}'
  AND D004.D004_Flag_Somar_Estoque_Fisico = 'S'
  AND (D004A.D004A_Flag_Separado IS NULL OR D004A.D004A_Flag_Separado <> 'S')
  AND T066.T066_Quantidade_Estoque > 0
ORDER BY D004.D004_Local ASC, T066.T066_Id ASC
SQL;

    $resultLocacoes = mysqli_query($conn, $sqlLocacoes);
    if (!$resultLocacoes) {
        return [];
    }

    $locacoes = [];
    while ($row = mysqli_fetch_assoc($resultLocacoes)) {
        $locacoes[] = [
            'T066_Id' => (int) ($row['T066_Id'] ?? 0),
            'D004_Local' => (string) ($row['D004_Local'] ?? ''),
            'T066_Quantidade_Estoque' => (float) ($row['T066_Quantidade_Estoque'] ?? 0),
            'estoque_formatado' => inecel_format_number($row['T066_Quantidade_Estoque'] ?? 0),
            'display' => trim((string) (($row['D004_Local'] ?? '') . ' - T066 ' . ($row['T066_Id'] ?? ''))),
        ];
    }

    return $locacoes;
}

function inecel_buscar_locacao_separado(mysqli $conn, int $empresaId): ?array
{
    if ($empresaId <= 0) {
        return null;
    }

    $sql = <<<SQL
SELECT
    D004.D004_Id,
    D004.D004_Local,
    D004.D004_D146_Id,
    D146.D146_Descricao,
    (
        SELECT D146_Id
        FROM D146
        WHERE UPPER(D146_Descricao) = 'ALMOX'
        ORDER BY D146_Id ASC
        LIMIT 1
    ) AS D146_Id_Almox
FROM D004
LEFT JOIN D004A ON D004A.D004A_D004_Id = D004.D004_Id
LEFT JOIN D146 ON D146.D146_Id = D004.D004_D146_Id
WHERE D004.D004_C004_Id = '{$empresaId}'
  AND D004A.D004A_Flag_Separado = 'S'
ORDER BY D004.D004_Id ASC
LIMIT 1
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    return $row ?: null;
}

function inecel_buscar_ou_criar_t066_separado(mysqli $conn, int $d009Id, array $locacaoSeparado, string $codigoProduto): array
{
    $d004Id = (int) ($locacaoSeparado['D004_Id'] ?? 0);
    if ($d009Id <= 0 || $d004Id <= 0) {
        return ['ok' => false, 'message' => 'Locacao de separado invalida.'];
    }

    $d146AlmoxId = (int) ($locacaoSeparado['D146_Id_Almox'] ?? 0);
    $d146AtualId = (int) ($locacaoSeparado['D004_D146_Id'] ?? 0);

    if ($d146AtualId <= 0 && $d146AlmoxId > 0) {
        $sqlUpdateD004 = "UPDATE D004 SET D004_D146_Id = '{$d146AlmoxId}' WHERE D004_Id = '{$d004Id}'";
        if (!mysqli_query($conn, $sqlUpdateD004)) {
            return ['ok' => false, 'message' => 'Erro ao atualizar armazem da locacao de separado: ' . mysqli_error($conn)];
        }
    }

    $sqlBuscar = <<<SQL
SELECT
    T066_Id,
    T066_D004_Id
FROM T066
WHERE T066_D009_Id = '{$d009Id}'
  AND T066_D004_Id = '{$d004Id}'
LIMIT 1
SQL;

    $resultBuscar = mysqli_query($conn, $sqlBuscar);
    if (!$resultBuscar) {
        return ['ok' => false, 'message' => 'Erro ao buscar locacao de estoque para separado: ' . mysqli_error($conn)];
    }

    $rowBuscar = mysqli_fetch_assoc($resultBuscar);
    if ($rowBuscar) {
        return [
            'ok' => true,
            'T066_Id' => (int) ($rowBuscar['T066_Id'] ?? 0),
            'D004_Id' => $d004Id,
            'D004_Local' => (string) ($locacaoSeparado['D004_Local'] ?? ''),
        ];
    }

    $codigoProdutoSql = mysqli_real_escape_string($conn, $codigoProduto);
    $sqlInsertT066 = <<<SQL
INSERT INTO T066 (
    T066_D009_Id,
    T066_D004_Id,
    T066_Codigo_Produto
) VALUES (
    '{$d009Id}',
    '{$d004Id}',
    '{$codigoProdutoSql}'
)
SQL;

    if (!mysqli_query($conn, $sqlInsertT066)) {
        return ['ok' => false, 'message' => 'Erro ao criar locacao T066 para separado: ' . mysqli_error($conn)];
    }

    return [
        'ok' => true,
        'T066_Id' => (int) mysqli_insert_id($conn),
        'D004_Id' => $d004Id,
        'D004_Local' => (string) ($locacaoSeparado['D004_Local'] ?? ''),
    ];
}

function inecel_gravar_movimento_t001(
    mysqli $conn,
    array $campos,
    \hardness\CAD002 $CAD002,
    float $quantidade,
    float $estoqueAtual,
    string $operacao
): array {
    $sql = <<<SQL
INSERT INTO T001 (
    T001_D024_Id_Cliente,
    T001_D024_Id,
    T001_D009_Id,
    T001_C004_Id,
    T001_T006_Id,
    T001_T005_Id,
    T001_D001_Id,
    T001_T066_Id,
    T001_D004_Id,
    T001_Codigo_Produto,
    T001_Data_Lancamento,
    T001_Descricao_Operacao,
    T001_Quantidade,
    T001_Quantidade_Saldo,
    T001_Flag_Operacao,
    T001_Observacao,
    T001_Flag_MMF,
    T001_Flag_Atualiza_Custo,
    T001_Numero_Controle,
    T001_Data_Hora_Insercao
) VALUES (
    '{$campos['T001_D024_Id_Cliente']}',
    '{$campos['T001_D024_Id']}',
    '{$campos['T001_D009_Id']}',
    '{$campos['T001_C004_Id']}',
    '{$campos['T001_T006_Id']}',
    '{$campos['T001_T005_Id']}',
    '{$campos['T001_D001_Id']}',
    '{$campos['T001_T066_Id']}',
    '{$campos['T001_D004_Id']}',
    '{$campos['T001_Codigo_Produto']}',
    CURRENT_DATE(),
    '{$campos['T001_Descricao_Operacao']}',
    '{$campos['T001_Quantidade']}',
    '0',
    '{$campos['T001_Flag_Operacao']}',
    '{$campos['T001_Observacao']}',
    '{$campos['T001_Flag_MMF']}',
    'N',
    '{$campos['T001_Numero_Controle']}',
    NOW()
)
SQL;

    if (!mysqli_query($conn, $sql)) {
        return [
            'ok' => false,
            'message' => 'Erro ao gravar movimento de estoque: ' . mysqli_error($conn),
        ];
    }

    $t001Id = (int) mysqli_insert_id($conn);
    if ($t001Id <= 0) {
        return [
            'ok' => false,
            'message' => 'Nao foi possivel identificar o movimento de estoque gravado.',
        ];
    }

    $CAD002->triggerAposInsercaoT001([
        'T001_Id' => $t001Id,
        'T001_Quantidade' => $quantidade,
        'T066_Quantidade_Estoque' => $estoqueAtual,
        'T001_Flag_Operacao' => $operacao,
    ]);

    return [
        'ok' => true,
        'T001_Id' => $t001Id,
    ];
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
$g['e_'] = $g['e_'] ?? '/inecel/coletor/separacao_ajax.php';

if (empty($g['C007']) && $g['usuarioAtual'] > 0) {
    $sqlUsuarioContexto = "SELECT * FROM C007 WHERE C007_Id = '" . (int) $g['usuarioAtual'] . "' LIMIT 1";
    $resultUsuarioContexto = mysqli_query($g['conexaoBanco'], $sqlUsuarioContexto);
    if ($resultUsuarioContexto) {
        $g['C007'] = mysqli_fetch_assoc($resultUsuarioContexto) ?: [];
    }
}

$conn = $g['conexaoBanco'];
$acao = trim((string) ($_POST['acao'] ?? ''));
$pedidoId = (int) ($_POST['pedido_id'] ?? 0);
$t006Id = (int) ($_POST['t006_id'] ?? 0);
$t066Id = (int) ($_POST['t066_id'] ?? 0);
$quantidadeInformada = inecel_normalize_number((string) ($_POST['quantidade'] ?? ''));
$empresaId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$usuarioId = (int) ($_SESSION['Usuario_Atual'] ?? 0);

$sqlItem = <<<SQL
SELECT
    T006.T006_Id,
    T006.T006_D009_Id,
    T006.T006_Quantidade,
    T005.T005_D024_Id,
    IFNULL(T006A.T006A_Qtde_Separado, 0) AS T006A_Qtde_Separado,
    IFNULL(T006A.T006A_Quantidade_Retornada, 0) AS T006A_Quantidade_Retornada,
    IFNULL(T006A.T006A_SMT006_Id, 0) AS T006A_SMT006_Id,
    D001.D001_Id,
    D001.D001_Codigo_Produto
FROM T006
LEFT JOIN T005 ON T005.T005_Id = T006.T006_T005_Id
LEFT JOIN T006A ON T006A.T006A_T006_Id = T006.T006_Id
LEFT JOIN D009 ON D009.D009_Id = T006.T006_D009_Id
LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
LEFT JOIN C008A ON C008A.C008A_C008_Id = D001.D001_C008_Id
WHERE T006.T006_Id = '{$t006Id}'
  AND T006.T006_T005_Id = '{$pedidoId}'
  AND T005.T005_C004_Id = '{$empresaId}'
  AND T005.T005_Flag_Status = 2
  AND C008A.C008A_Flag_Coletor = 'S'
LIMIT 1
SQL;

$resultItem = mysqli_query($conn, $sqlItem);
if (!$resultItem) {
    inecel_json(['ok' => false, 'message' => 'Erro ao buscar item: ' . mysqli_error($conn)]);
}

$item = mysqli_fetch_assoc($resultItem);
if (!$item) {
    inecel_json(['ok' => false, 'message' => 'Item nao encontrado neste pedido.']);
}

if ($acao === 'listar_locacoes') {
    inecel_json([
        'ok' => true,
        'locacoes' => inecel_buscar_locacoes_item($conn, (int) $item['T006_D009_Id']),
    ]);
}

if ($pedidoId <= 0 || $t006Id <= 0 || $t066Id <= 0) {
    inecel_json(['ok' => false, 'message' => 'Item, pedido ou locacao nao informado.']);
}

if ($quantidadeInformada <= 0) {
    inecel_json(['ok' => false, 'message' => 'Informe uma quantidade maior que zero.']);
}

$quantidadePedido = (float) ($item['T006_Quantidade'] ?? 0);
$quantidadeSeparada = (float) ($item['T006A_Qtde_Separado'] ?? 0);
$quantidadeRetornada = (float) ($item['T006A_Quantidade_Retornada'] ?? 0);
$itemPintado = (int) ($item['T006A_SMT006_Id'] ?? 0) > 0;
$retornadaDisponivel = max($quantidadeRetornada - $quantidadeSeparada, 0);
$novoTotal = $quantidadeSeparada + $quantidadeInformada;

if ($itemPintado && $quantidadeInformada - $retornadaDisponivel > 0.00001) {
    inecel_json([
        'ok' => false,
        'message' => 'Item pintado libera somente ' . inecel_format_number($retornadaDisponivel) . ' para separar.',
    ]);
}

if (max($quantidadePedido - $quantidadeSeparada, 0) <= 0.00001) {
    inecel_json([
        'ok' => false,
        'message' => 'Este item nao possui saldo para separar.',
    ]);
}

if ($novoTotal - $quantidadePedido > 0.00001) {
    $saldoDisponivel = max($quantidadePedido - $quantidadeSeparada, 0);
    inecel_json([
        'ok' => false,
        'message' => 'Quantidade informada maior que o saldo disponivel (' . inecel_format_number($saldoDisponivel) . ').',
    ]);
}

$sqlLocacao = <<<SQL
SELECT
    T066.T066_Id,
    T066.T066_D009_Id,
    T066.T066_D004_Id,
    T066.T066_Quantidade_Estoque,
    D004.D004_Local,
    D004.D004_Flag_Somar_Estoque_Fisico
FROM T066
LEFT JOIN D004 ON D004.D004_Id = T066.T066_D004_Id
LEFT JOIN D004A ON D004A.D004A_D004_Id = D004.D004_Id
WHERE T066.T066_Id = '{$t066Id}'
  AND D004.D004_Flag_Somar_Estoque_Fisico = 'S'
  AND (D004A.D004A_Flag_Separado IS NULL OR D004A.D004A_Flag_Separado <> 'S')
LIMIT 1
SQL;

$resultLocacao = mysqli_query($conn, $sqlLocacao);
if (!$resultLocacao) {
    inecel_json(['ok' => false, 'message' => 'Erro ao buscar locacao: ' . mysqli_error($conn)]);
}

$locacao = mysqli_fetch_assoc($resultLocacao);
if (!$locacao) {
    inecel_json(['ok' => false, 'message' => 'Locacao nao encontrada.']);
}

if ((int) ($locacao['T066_D009_Id'] ?? 0) !== (int) ($item['T006_D009_Id'] ?? 0)) {
    inecel_json(['ok' => false, 'message' => 'O produto desta locacao nao corresponde ao item selecionado.']);
}

$estoqueLocacao = (float) ($locacao['T066_Quantidade_Estoque'] ?? 0);
if ($estoqueLocacao <= 0.00001) {
    inecel_json([
        'ok' => false,
        'message' => 'A locacao informada nao possui saldo em estoque.',
    ]);
}

if ($estoqueLocacao + 0.00001 < $quantidadeInformada) {
    inecel_json([
        'ok' => false,
        'message' => 'Estoque insuficiente na locacao (' . inecel_format_number($estoqueLocacao) . ').',
    ]);
}

$quantidadeSql = number_format($quantidadeInformada, 4, '.', '');
$numeroControle = mysqli_real_escape_string($conn, 'COL-SEP-' . $pedidoId . '-' . $t006Id . '-' . time());
$descricaoOperacao = mysqli_real_escape_string($conn, 'Separacao coletor');
$descricaoOperacaoEntrada = mysqli_real_escape_string($conn, 'Separacao coletor - entrada');
$observacao = mysqli_real_escape_string($conn, 'Separacao pedido ' . $pedidoId);
$codigoProduto = mysqli_real_escape_string($conn, (string) ($item['D001_Codigo_Produto'] ?? ''));

mysqli_begin_transaction($conn);

$sqlUpsert = <<<SQL
INSERT INTO T006A (T006A_T006_Id, T006A_Qtde_Separado)
VALUES ('{$t006Id}', '{$quantidadeSql}')
ON DUPLICATE KEY UPDATE
    T006A_Qtde_Separado = IFNULL(T006A_Qtde_Separado, 0) + VALUES(T006A_Qtde_Separado)
SQL;

if (!mysqli_query($conn, $sqlUpsert)) {
    mysqli_rollback($conn);
    inecel_json(['ok' => false, 'message' => 'Erro ao atualizar separacao: ' . mysqli_error($conn)]);
}

$locacaoSeparado = inecel_buscar_locacao_separado($conn, $empresaId);
if (!$locacaoSeparado) {
    mysqli_rollback($conn);
    inecel_json(['ok' => false, 'message' => 'Nao existe locacao marcada para separado para esta empresa.']);
}

$t066Separado = inecel_buscar_ou_criar_t066_separado($conn, (int) $item['T006_D009_Id'], $locacaoSeparado, (string) ($item['D001_Codigo_Produto'] ?? ''));
if (empty($t066Separado['ok'])) {
    mysqli_rollback($conn);
    inecel_json(['ok' => false, 'message' => $t066Separado['message'] ?? 'Nao foi possivel preparar a locacao de separado.']);
}

$numeroControleEntrada = mysqli_real_escape_string($conn, 'COL-SEP-ENT-' . $pedidoId . '-' . $t006Id . '-' . time());
$t066SeparadoId = (int) ($t066Separado['T066_Id'] ?? 0);
$d004SeparadoId = (int) ($t066Separado['D004_Id'] ?? 0);

require_once INECEL_ROOT . '/sistema/apps.php';
require_once INECEL_ROOT . '/sistema/confSistema.php';
require_once INECEL_ROOT . '/bibliotecas/classes/CAD002.php';

if (empty($g['conf']) || !is_object($g['conf'])) {
    $g['conf'] = new \hardness\confSistema();
}

try {
    $CAD002 = new \hardness\CAD002();

    $retornoSaida = inecel_gravar_movimento_t001($conn, [
        'T001_D024_Id_Cliente' => (int) ($item['T005_D024_Id'] ?? 0),
        'T001_D024_Id' => (int) ($item['T005_D024_Id'] ?? 0),
        'T001_D009_Id' => (int) ($item['T006_D009_Id'] ?? 0),
        'T001_C004_Id' => $empresaId,
        'T001_T006_Id' => $t006Id,
        'T001_T005_Id' => $pedidoId,
        'T001_D001_Id' => (int) ($item['D001_Id'] ?? 0),
        'T001_T066_Id' => $t066Id,
        'T001_D004_Id' => (int) ($locacao['T066_D004_Id'] ?? 0),
        'T001_Codigo_Produto' => $codigoProduto,
        'T001_Descricao_Operacao' => $descricaoOperacao,
        'T001_Quantidade' => $quantidadeSql,
        'T001_Flag_Operacao' => 'S',
        'T001_Observacao' => $observacao,
        'T001_Flag_MMF' => 'S',
        'T001_Numero_Controle' => $numeroControle,
    ], $CAD002, $quantidadeInformada, $estoqueLocacao, 'S');

    if (empty($retornoSaida['ok'])) {
        mysqli_rollback($conn);
        inecel_json(['ok' => false, 'message' => $retornoSaida['message'] ?? 'Erro ao gravar movimento de saida.']);
    }

    $retornoEntrada = inecel_gravar_movimento_t001($conn, [
        'T001_D024_Id_Cliente' => (int) ($item['T005_D024_Id'] ?? 0),
        'T001_D024_Id' => (int) ($item['T005_D024_Id'] ?? 0),
        'T001_D009_Id' => (int) ($item['T006_D009_Id'] ?? 0),
        'T001_C004_Id' => $empresaId,
        'T001_T006_Id' => $t006Id,
        'T001_T005_Id' => $pedidoId,
        'T001_D001_Id' => (int) ($item['D001_Id'] ?? 0),
        'T001_T066_Id' => $t066SeparadoId,
        'T001_D004_Id' => $d004SeparadoId,
        'T001_Codigo_Produto' => $codigoProduto,
        'T001_Descricao_Operacao' => $descricaoOperacaoEntrada,
        'T001_Quantidade' => $quantidadeSql,
        'T001_Flag_Operacao' => 'E',
        'T001_Observacao' => $observacao,
        'T001_Flag_MMF' => 'N',
        'T001_Numero_Controle' => $numeroControleEntrada,
    ], $CAD002, $quantidadeInformada, 0.0, 'E');

    if (empty($retornoEntrada['ok'])) {
        mysqli_rollback($conn);
        inecel_json(['ok' => false, 'message' => $retornoEntrada['message'] ?? 'Erro ao gravar movimento de entrada.']);
    }

    $retornoReprocessamento = $CAD002->D001_reprocessa_historico(false, (int) $item['T006_D009_Id'], 50, true);

    if ($retornoReprocessamento !== true) {
        mysqli_rollback($conn);
        $mensagemErro = $retornoReprocessamento;
        if ($mensagemErro === false || $mensagemErro === null || $mensagemErro === '') {
            $mensagemErro = 'Nao foi possivel reprocessar o historico do produto.';
        }
        inecel_json([
            'ok' => false,
            'message' => 'Movimentacao cancelada: ' . $mensagemErro,
        ]);
    }
} catch (Throwable $throwable) {
    mysqli_rollback($conn);
    inecel_json([
        'ok' => false,
        'message' => 'Movimentacao cancelada ao reprocessar historico: ' . $throwable->getMessage(),
    ]);
}

mysqli_commit($conn);

$locacoesAtualizadas = inecel_buscar_locacoes_item($conn, (int) $item['T006_D009_Id']);
$saldo = max($quantidadePedido - $novoTotal, 0);
$completo = $saldo <= 0.00001 && $quantidadePedido > 0;

inecel_json([
    'ok' => true,
    'message' => $completo ? 'Item separado por completo.' : 'Quantidade separada atualizada com sucesso.',
    'item' => [
        'T006_Id' => $t006Id,
        'qtd_pedido' => $quantidadePedido,
        'qtd_separado' => $novoTotal,
        'saldo' => $saldo,
        'qtd_pedido_formatada' => inecel_format_number($quantidadePedido),
        'qtd_separado_formatada' => inecel_format_number($novoTotal),
        'saldo_formatado' => inecel_format_number($saldo),
        'completo' => $completo,
    ],
    'location' => [
        'T066_Id' => $t066Id,
        'D004_Local' => $locacao['D004_Local'] ?? '',
        'display' => trim((string) (($locacao['D004_Local'] ?? '') . ' - T066 ' . $t066Id)),
    ],
    'location_entry' => [
        'T066_Id' => $t066SeparadoId,
        'D004_Local' => $t066Separado['D004_Local'] ?? '',
        'display' => trim((string) (($t066Separado['D004_Local'] ?? '') . ' - T066 ' . $t066SeparadoId)),
    ],
    'locacoes' => $locacoesAtualizadas,
]);
