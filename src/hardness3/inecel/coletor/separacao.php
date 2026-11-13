<?php
declare(strict_types=1);

session_start();

const INECEL_ROOT = __DIR__ . '/../..';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./index.php');
    exit;
}

if (empty($_SESSION['Usuario_Atual']) || empty($_SESSION['Empresa_Atual'])) {
    header('Location: ./index.php');
    exit;
}

chdir(INECEL_ROOT);
require_once INECEL_ROOT . '/bibliotecas/confUsuario.php';
include_once INECEL_ROOT . '/configuracoes/sysConfig.php';
require_once INECEL_ROOT . '/bibliotecas/funcoesGerais.php';
require_once INECEL_ROOT . '/bibliotecas/conexaoBanco.php';

if (empty($g['empresaAtual'])) {
    $g['empresaAtual'] = (int) ($_SESSION['Empresa_Atual'] ?? 0);
}

function inecel_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function inecel_db(): mysqli
{
    global $g;
    return $g['conexaoBanco'];
}

function inecel_format_number($value, int $decimals = 2): string
{
    return number_format((float) $value, $decimals, ',', '.');
}

function inecel_company_logo_url(int $empresaId): string
{
    global $g, $confUsuario;

    if ($empresaId <= 0) {
        return '';
    }

    if (empty($g['empresaAtual'])) {
        $g['empresaAtual'] = $empresaId;
    }

    if (empty($g['c004']['C004_Logo_Marca'])) {
        $conn = inecel_db();
        $sqlEmpresa = "SELECT * FROM C004 WHERE C004_Id = '{$empresaId}' LIMIT 1";
        $resultEmpresa = mysqli_query($conn, $sqlEmpresa);
        if ($resultEmpresa && ($rowEmpresa = mysqli_fetch_assoc($resultEmpresa))) {
            $g['c004'] = $rowEmpresa;
        }
    }

    if (!empty($g['c004']['C004_Logo_Marca'])) {
        $pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
        $extension = (string) ($pathinfo['extension'] ?? '');
        if ($extension !== '') {
            $urlRaiz = rtrim((string) ($confUsuario['urlRaiz'] ?? ''), '/');
            $dbDatabase = (string) ($confUsuario['dbDatabase'] ?? '');
            if ($urlRaiz !== '' && $dbDatabase !== '') {
                return "{$urlRaiz}/hardness3/dados_usuarios/{$dbDatabase}/imagens/logo/{$empresaId}.{$extension}";
            }
        }
    }

    return '';
}
function inecel_pedido_atual(int $pedidoId): ?array
{
    $conn = inecel_db();
    $empresaId = (int) $_SESSION['Empresa_Atual'];

    $sql = <<<SQL
SELECT
    T005.T005_Id,
    T005.T005_Nome_Status,
    D024.D024_Nome_Empresa
FROM T005
LEFT JOIN D024 ON D024.D024_Id = T005.T005_D024_Id
WHERE T005.T005_Id = '{$pedidoId}'
  AND T005.T005_C004_Id = '{$empresaId}'
  AND T005.T005_Flag_Status = 2
LIMIT 1
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    return $row ?: null;
}

function inecel_separacao_item_rows(int $pedidoId): array
{
    $conn = inecel_db();
    $empresaId = (int) $_SESSION['Empresa_Atual'];

    $sql = <<<SQL
SELECT
    T006.T006_Id,
    T006.T006_Item,
    T006.T006_D009_Id,
    T006.T006_Quantidade,
    IFNULL(T006A.T006A_Qtde_Separado, 0) AS T006A_Qtde_Separado,
    IFNULL(T006A.T006A_Quantidade_Retornada, 0) AS T006A_Quantidade_Retornada,
    IFNULL(T006A.T006A_SMT006_Id, 0) AS T006A_SMT006_Id,
    SMT006.SMT006_Descricao,
    D001.D001_Id,
    D001.D001_Codigo_Produto,
    D001.D001_Descricao_Produto
FROM T006
LEFT JOIN T005 ON T005.T005_Id = T006.T006_T005_Id
LEFT JOIN T006A ON T006A.T006A_T006_Id = T006.T006_Id
LEFT JOIN SMT006 ON SMT006.SMT006_Id = T006A.T006A_SMT006_Id
LEFT JOIN D009 ON D009.D009_Id = T006.T006_D009_Id
LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
LEFT JOIN C008A ON C008A.C008A_C008_Id = D001.D001_C008_Id
WHERE T006.T006_T005_Id = '{$pedidoId}'
  AND T005.T005_C004_Id = '{$empresaId}'
  AND T005.T005_Flag_Status = 2
  AND C008A.C008A_Flag_Coletor = 'S'
ORDER BY D001.D001_Codigo_Produto ASC, T006.T006_Item ASC, T006.T006_Id ASC
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function inecel_buscar_locacao_separado_empresa(): ?array
{
    $conn = inecel_db();
    $empresaId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    if ($empresaId <= 0) {
        return null;
    }

    $sql = <<<SQL
SELECT
    D004.D004_Id,
    D004.D004_Local
FROM D004
LEFT JOIN D004A ON D004A.D004A_D004_Id = D004.D004_Id
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

function inecel_locacoes_por_produto(array $d009Ids): array
{
    $d009Ids = array_values(array_unique(array_map('intval', $d009Ids)));
    $d009Ids = array_filter($d009Ids, function ($value) {
        return $value > 0;
    });

    if (!$d009Ids) {
        return [];
    }

    $conn = inecel_db();
    $sql = <<<SQL
SELECT
    T066.T066_D009_Id,
    T066.T066_Quantidade_Estoque,
    D004.D004_Local
FROM T066
LEFT JOIN D004 ON D004.D004_Id = T066.T066_D004_Id
LEFT JOIN D004A ON D004A.D004A_D004_Id = D004.D004_Id
WHERE T066.T066_D009_Id IN (%s)
  AND D004.D004_Flag_Somar_Estoque_Fisico = 'S'
  AND (D004A.D004A_Flag_Separado IS NULL OR D004A.D004A_Flag_Separado <> 'S')
  AND T066.T066_Quantidade_Estoque > 0
ORDER BY D004.D004_Local ASC, T066.T066_Id ASC
SQL;

    $sql = sprintf($sql, implode(',', $d009Ids));
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $mapa = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $d009Id = (int) ($row['T066_D009_Id'] ?? 0);
        if (!isset($mapa[$d009Id])) {
            $mapa[$d009Id] = [];
        }
        $mapa[$d009Id][] = [
            'D004_Local' => (string) ($row['D004_Local'] ?? ''),
            'T066_Quantidade_Estoque' => (float) ($row['T066_Quantidade_Estoque'] ?? 0),
        ];
    }

    return $mapa;
}

$pedidoId = (int) ($_GET['pedido_id'] ?? 0);
$pedido = $pedidoId > 0 ? inecel_pedido_atual($pedidoId) : null;
if (!$pedido) {
    header('Location: ./pedidos_separacao.php');
    exit;
}

$locacaoSeparadoEmpresa = inecel_buscar_locacao_separado_empresa();
$semLocacaoSeparadoEmpresa = !$locacaoSeparadoEmpresa;
$itens = inecel_separacao_item_rows($pedidoId);
$locacoesPorProduto = inecel_locacoes_por_produto(array_column($itens, 'T006_D009_Id'));

foreach ($itens as &$item) {
    $quantidade = (float) ($item['T006_Quantidade'] ?? 0);
    $separado = (float) ($item['T006A_Qtde_Separado'] ?? 0);
    $saldo = max($quantidade - $separado, 0);
    $retornada = (float) ($item['T006A_Quantidade_Retornada'] ?? 0);
    $item['saldo'] = $saldo;
    $item['retornada_disponivel'] = max($retornada - $separado, 0);
    $item['locacoes'] = $locacoesPorProduto[(int) ($item['T006_D009_Id'] ?? 0)] ?? [];
}
unset($item);

$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$empresaAtualId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$companyLogoUrl = inecel_company_logo_url($empresaAtualId);
$scannerIndex = [];
foreach ($itens as $item) {
    $codigoProduto = trim((string) ($item['D001_Codigo_Produto'] ?? ''));
    if ($codigoProduto === '') {
        continue;
    }
    if (!isset($scannerIndex[$codigoProduto])) {
        $scannerIndex[$codigoProduto] = [];
    }
    $scannerIndex[$codigoProduto][] = [
        't006_id' => (int) $item['T006_Id'],
        'codigo' => $codigoProduto,
        'descricao' => (string) ($item['D001_Descricao_Produto'] ?? ''),
        'saldo' => (float) ($item['saldo'] ?? 0),
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Separacao de itens | Inecel</title>
    <style>
        :root { --bg:#eef2f6; --panel:#fff; --text:#17202a; --muted:#667085; --line:#d7dde5; --primary:#155eef; --ok:#168a4a; --warn:#a05a00; --danger:#b42318; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:var(--bg); color:var(--text); font-family:Arial, Helvetica, sans-serif; }
        .app { width:min(1180px, 100%); margin:0 auto; padding:10px; }
        .topbar { display:flex; gap:10px; align-items:center; justify-content:space-between; padding:8px 0 12px; }
        .title h1 { margin:0; font-size:20px; line-height:1.1; }
        .title p { margin:3px 0 0; color:var(--muted); font-size:12px; }
        .topbar-actions { display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }
        .action-button { display:inline-flex; align-items:center; justify-content:center; min-height:38px; padding:0 12px; border-radius:7px; background:var(--primary); color:#fff; font-weight:700; text-decoration:none; white-space:nowrap; font-size:14px; }
        .action-button--secondary { background:#475467; }
        .feedback { display:none; margin-bottom:10px; padding:9px 11px; border-radius:7px; font-weight:700; font-size:14px; }
        .feedback--ok, .feedback--error, .feedback--warn { display:block; color:#fff; }
        .feedback--ok { background:var(--ok); }
        .feedback--error { background:var(--danger); }
        .feedback--warn { background:var(--warn); }
        .scanner { margin:0 0 10px; padding:10px; background:var(--panel); border:1px solid var(--line); border-radius:8px; }
        .scanner-form { display:grid; grid-template-columns:1fr auto; gap:8px; }
        .scanner small { display:block; margin-top:7px; color:var(--muted); font-size:11px; }
        .scanner-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:8px; }
        .scanner-actions button { min-height:46px; min-width:140px; border-radius:7px; font-size:15px; }
        .item-list { display:grid; gap:8px; }
        .item-card { padding:10px; background:var(--panel); border:1px solid var(--line); border-left:5px solid var(--warn); border-radius:8px; }
        .item-card--painted { border-left-color:#6d28d9; background:#f3e8ff; box-shadow:0 0 0 2px rgba(109,40,217,.16); }
        .item-card[data-completo="S"] { border-left-color:var(--ok); background:#f6fef9; }
        .item-card--painted[data-completo="S"] { border-left-color:#16a34a; background:#ecfdf3; box-shadow:0 0 0 2px rgba(22,163,74,.14); }
        .item-card.is-scan-match { border-color:var(--primary); box-shadow:0 0 0 3px rgba(21,94,239,.18); }
        .item-card.is-filter-hidden { display:none; }
        .item-head { display:flex; gap:8px; align-items:flex-start; }
        .item-head h2 { margin:0; font-size:16px; line-height:1.2; }
        .item-head p { margin:4px 0 0; color:var(--muted); font-size:12px; }
        .paint-badge { display:inline-flex; align-items:center; margin-top:6px; padding:5px 11px; border-radius:999px; background:#7c3aed; color:#fff; font-size:12px; font-weight:700; box-shadow:0 4px 12px rgba(124,58,237,.28); }
        .item-locations {
            margin-top:8px;
            color:var(--muted);
            font-size:11px;
            line-height:1.35;
        }
        .item-locations strong {
            color:var(--text);
            font-size:11px;
        }
        .item-locations span { display:inline; }
        .item-grid { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:6px; margin-top:10px; }
        .mini { padding:6px 8px; background:#f8fafc; border:1px solid #e7edf4; border-radius:8px; min-height:82px; }
        .mini span { display:block; color:var(--muted); font-size:11px; }
        .mini strong { display:block; margin-top:2px; font-size:16px; line-height:1.1; }
        .mini--input { background:#fff; }
        .qtd-input {
            width:100%;
            min-height:48px;
            border:1px solid #b8c2cf;
            border-radius:8px;
            background:#fff;
            font-size:20px;
            font-weight:700;
            text-align:center;
            color:var(--text);
            cursor:pointer;
        }
        .qtd-input:disabled {
            background:#eef2f6;
            color:#98a2b3;
            cursor:not-allowed;
            border-color:#d0d5dd;
        }
        .empty { padding:18px; background:var(--panel); border:1px solid var(--line); border-radius:8px; color:var(--muted); text-align:center; font-size:14px; }
        .modal[hidden] { display:none; }
        .modal {
            position:fixed;
            inset:0;
            z-index:20;
            background:rgba(15,23,42,.52);
            display:flex;
            align-items:flex-end;
            justify-content:center;
            padding:12px;
        }
        .keypad {
            width:min(560px, 100%);
            background:#fff;
            border-radius:12px;
            padding:16px;
            box-shadow:0 20px 44px rgba(15,23,42,.28);
        }
        .keypad h3 { margin:0 0 10px; font-size:18px; }
        .keypad-display {
            min-height:78px;
            display:flex;
            align-items:center;
            justify-content:flex-end;
            padding:0 16px;
            border:1px solid #b8c2cf;
            border-radius:10px;
            background:#f8fafc;
            font-size:36px;
            font-weight:700;
        }
        .keypad-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; margin-top:12px; }
        .keypad-grid button, .keypad-actions button {
            min-height:78px;
            border:0;
            border-radius:10px;
            font-size:28px;
            font-weight:700;
            cursor:pointer;
            line-height:1;
            overflow:hidden;
        }
        .keypad-grid button { background:#eef2f6; color:#17202a; }
        .keypad-grid button.is-action {
            background:#fcd3d0;
            color:#8e1a10;
            font-size:18px;
            letter-spacing:0;
            padding:0 8px;
            white-space:nowrap;
            text-overflow:ellipsis;
        }
        .keypad-actions { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:12px; }
        .keypad-actions .cancel { background:#475467; color:#fff; }
        .keypad-actions .confirm { background:var(--primary); color:#fff; }
        .location-info {
            margin-top:12px;
            padding:10px 12px;
            border:1px solid #d7dde5;
            border-radius:10px;
            background:#f8fafc;
        }
        .location-info strong, .location-info span { display:block; }
        .location-info strong { font-size:15px; }
        .location-info span { margin-top:4px; color:var(--muted); font-size:12px; }
        .location-form { display:grid; grid-template-columns:1fr auto; gap:10px; margin-top:12px; }
        .location-form input {
            min-height:56px;
            border:1px solid #b8c2cf;
            border-radius:10px;
            padding:0 14px;
            font-size:24px;
            font-weight:700;
        }
        .location-feedback {
            display:none;
            margin-top:10px;
            padding:10px 12px;
            border-radius:10px;
            font-size:14px;
            font-weight:700;
        }
        .location-feedback--error,
        .location-feedback--ok,
        .location-feedback--warn,
        .location-feedback--loading {
            display:block;
            color:#fff;
        }
        .location-feedback--error { background:var(--danger); }
        .location-feedback--ok { background:var(--ok); }
        .location-feedback--warn { background:var(--warn); }
        .location-feedback--loading { background:var(--primary); }
        .location-manual { display:grid; gap:10px; margin-top:12px; }
        .location-manual-button {
            min-height:54px;
            border:1px solid #b8c2cf;
            border-radius:10px;
            background:#eef2f6;
            color:var(--text);
            font-size:18px;
            font-weight:700;
            cursor:pointer;
        }
        .location-manual-list {
            display:grid;
            gap:8px;
            max-height:260px;
            overflow:auto;
            padding-right:2px;
        }
        .location-manual-list[hidden] { display:none; }
        .location-option {
            width:100%;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            padding:12px 14px;
            border:1px solid #d7dde5;
            border-radius:10px;
            background:#f8fafc;
            color:var(--text);
            text-align:left;
            cursor:pointer;
        }
        .location-option strong,
        .location-option span { display:block; }
        .location-option span {
            margin-top:3px;
            color:var(--muted);
            font-size:12px;
        }
        .location-option em {
            font-style:normal;
            font-weight:700;
            color:var(--primary);
            white-space:nowrap;
        }
        .location-manual-empty {
            padding:12px 14px;
            border:1px dashed #d0d5dd;
            border-radius:10px;
            background:#f8fafc;
            color:var(--muted);
            font-size:14px;
            text-align:center;
        }
        .processing-overlay[hidden] { display:none !important; }
        .processing-overlay { position:fixed; inset:0; z-index:90; background:rgba(255,255,255,.94); display:flex; align-items:center; justify-content:center; padding:20px; }
        .processing-overlay__card { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; text-align:center; }
        .processing-overlay__logo-wrap { width:280px; height:200px; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .processing-overlay__logo-wrap img { width:230px; height:auto; max-width:100%; object-fit:contain; object-position:center center; clip-path:none; animation:inecelFade 1.8s ease-in-out infinite; }
        .processing-overlay__fallback { width:150px; height:150px; border-radius:26px; display:flex; align-items:center; justify-content:center; background:var(--primary); color:#fff; font-size:64px; font-weight:700; animation:inecelFade 1.8s ease-in-out infinite; }
        .processing-overlay__title { font-size:13px; font-weight:700; color:var(--muted); letter-spacing:.02em; text-transform:uppercase; }
        .processing-overlay__subtitle { display:none; }
        @keyframes inecelFade {
            0% { opacity:1; }
            50% { opacity:.38; }
            100% { opacity:1; }
        }
        @media (max-width:760px) {
            .app { padding:8px; }
            .topbar { align-items:flex-start; flex-direction:column; }
            .topbar-actions { width:100%; }
            .action-button { flex:1 1 auto; }
            .scanner-form { grid-template-columns:1fr; }
            .scanner-actions { justify-content:stretch; }
            .scanner-actions button { width:100%; }
            .item-head { flex-direction:column; }
            .item-grid { grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .qtd-input { min-height:56px; font-size:24px; }
            .keypad-grid button, .keypad-actions button { min-height:88px; font-size:32px; }
            .keypad-grid button.is-action { font-size:16px; }
            .location-form { grid-template-columns:1fr; }
            .location-form input { font-size:22px; }
            .location-manual-button { min-height:64px; font-size:20px; }
            .location-option { align-items:flex-start; flex-direction:column; }
            .location-option em { white-space:normal; }
        }
    </style>
</head>
<body>
<div class="processing-overlay" id="processingOverlay" hidden>
    <div class="processing-overlay__card" role="status" aria-live="polite" aria-busy="true">
        <div class="processing-overlay__logo-wrap">
            <?php if ($companyLogoUrl !== ''): ?>
                <img src="<?php echo inecel_h($companyLogoUrl); ?>" alt="Logo da empresa">
            <?php else: ?>
                <div class="processing-overlay__fallback"><?php echo inecel_h(function_exists('mb_substr') ? mb_substr((string) $empresa, 0, 1, 'UTF-8') : substr((string) $empresa, 0, 1)); ?></div>
            <?php endif; ?>
        </div>
        <div class="processing-overlay__title">Processando...</div>
        <div class="processing-overlay__subtitle"><?php echo inecel_h($empresa); ?></div>
    </div>
</div>
<div class="app">
    <header class="topbar">
        <div class="title">
            <h1>Separacao de itens</h1>
            <p>Pedido <?php echo inecel_h($pedido['T005_Id']); ?> - <?php echo inecel_h($pedido['D024_Nome_Empresa'] ?: 'Pessoa nao informada'); ?></p>
            <p><?php echo inecel_h($empresa); ?> - <?php echo inecel_h($usuario); ?></p>
        </div>
        <div class="topbar-actions">
            <a class="action-button action-button--secondary" href="./menu.php">Inicio</a>
            <a class="action-button action-button--secondary" href="./pedidos_separacao.php">Pedidos</a>
        </div>
    </header>

    <div id="feedback" class="feedback"></div>

    <?php if ($semLocacaoSeparadoEmpresa): ?>
        <div class="feedback feedback--error">Nao existe locacao marcada para separado para esta empresa.</div>
    <?php endif; ?>

    <section class="scanner">
        <form class="scanner-form" id="scannerForm" autocomplete="off">
            <input type="text" id="scannerCodigo" inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Leia ou digite o codigo de barras do produto" <?php echo $semLocacaoSeparadoEmpresa ? 'disabled' : ''; ?>>
            <button type="submit" <?php echo $semLocacaoSeparadoEmpresa ? 'disabled' : ''; ?>>Localizar</button>
        </form>
        <div class="scanner-actions">
            <button type="button" id="clearFilterButton" <?php echo $semLocacaoSeparadoEmpresa ? 'disabled' : ''; ?>>Limpar filtro</button>
        </div>
        <small><?php echo $semLocacaoSeparadoEmpresa ? 'Configure uma locacao com flag de separado para esta empresa antes de continuar.' : 'O leitor filtra os itens do pedido pelo codigo do produto. Depois da confirmacao, o filtro eh limpo.'; ?></small>
    </section>

    <section class="item-list" id="itemList">
        <?php foreach ($itens as $item): ?>
            <?php
                $quantidade = (float) $item['T006_Quantidade'];
                $separado = (float) $item['T006A_Qtde_Separado'];
                $saldo = (float) $item['saldo'];
                $completo = $saldo <= 0.00001 && $quantidade > 0 ? 'S' : 'N';
                $pintado = (int) ($item['T006A_SMT006_Id'] ?? 0) > 0;
                $retornadaDisponivel = (float) ($item['retornada_disponivel'] ?? 0);
                $bloqueadoRetorno = $pintado && $retornadaDisponivel <= 0.00001;
                $classePintado = $pintado ? ' item-card--painted' : '';
            ?>
            <article
                class="item-card<?php echo $classePintado; ?>"
                id="item-<?php echo inecel_h($item['T006_Id']); ?>"
                data-item-id="<?php echo inecel_h($item['T006_Id']); ?>"
                data-pedido-id="<?php echo inecel_h($pedidoId); ?>"
                data-completo="<?php echo inecel_h($completo); ?>"
                data-saldo="<?php echo inecel_h((string) $saldo); ?>"
                data-codigo-produto="<?php echo inecel_h($item['D001_Codigo_Produto'] ?: ''); ?>"
                data-d009-id="<?php echo inecel_h($item['T006_D009_Id']); ?>"
                data-d001-id="<?php echo inecel_h($item['D001_Id']); ?>"
                data-pintado="<?php echo $pintado ? 'S' : 'N'; ?>"
                data-retornada-disponivel="<?php echo inecel_h((string) $retornadaDisponivel); ?>"
            >
                <div class="item-head">
                    <div>
                        <h2><?php echo inecel_h($item['D001_Codigo_Produto'] ?: '-'); ?> - <?php echo inecel_h($item['D001_Descricao_Produto'] ?: 'Produto sem descricao'); ?></h2>
                        <p>Item <?php echo inecel_h($item['T006_Item']); ?> - Registro <?php echo inecel_h($item['T006_Id']); ?></p>
                        <?php if ($pintado): ?>
                            <div class="paint-badge"><?php echo inecel_h($item['SMT006_Descricao'] ?: 'Pintado'); ?></div>
                            <p>Retornada disponivel: <?php echo inecel_h(inecel_format_number($retornadaDisponivel)); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($item['locacoes'])): ?>
                            <?php
                                $locacoesTexto = [];
                                foreach ($item['locacoes'] as $locacao) {
                                    $locacoesTexto[] = 'Local: ' . ($locacao['D004_Local'] ?: '-') . ' - Qtde: ' . inecel_format_number($locacao['T066_Quantidade_Estoque'] ?? 0);
                                }
                            ?>
                            <div class="item-locations">
                                <strong>Locacoes:</strong>
                                <span><?php echo inecel_h(implode(' / ', $locacoesTexto)); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="item-grid">
                    <div class="mini">
                        <span>Quantidade do pedido</span>
                        <strong class="pedido-qtd"><?php echo inecel_h(inecel_format_number($quantidade)); ?></strong>
                    </div>
                    <div class="mini">
                        <span>Ja separado</span>
                        <strong class="separado-qtd"><?php echo inecel_h(inecel_format_number($separado)); ?></strong>
                    </div>
                    <div class="mini">
                        <span>Saldo</span>
                        <strong class="saldo-qtd"><?php echo inecel_h(inecel_format_number($saldo)); ?></strong>
                    </div>
                    <div class="mini mini--input">
                        <span>Separar agora</span>
                        <input
                            class="qtd-input"
                            type="text"
                            value="0"
                            readonly
                            inputmode="none"
                            data-item-id="<?php echo inecel_h($item['T006_Id']); ?>"
                            data-saldo="<?php echo inecel_h((string) $saldo); ?>"
                            <?php echo ($semLocacaoSeparadoEmpresa || $saldo <= 0.00001 || $bloqueadoRetorno) ? 'disabled' : ''; ?>
                            aria-label="Quantidade para separar"
                        >
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if (!$itens): ?>
            <div class="empty">Nenhum item encontrado neste pedido.</div>
        <?php endif; ?>
    </section>
</div>

<div class="modal" id="keypadModal" hidden>
    <div class="keypad" role="dialog" aria-modal="true" aria-labelledby="keypadTitle">
        <h3 id="keypadTitle">Informe a quantidade separada</h3>
        <div class="keypad-display" id="keypadDisplay">0</div>
        <div class="keypad-grid">
            <button type="button" data-key="1">1</button>
            <button type="button" data-key="2">2</button>
            <button type="button" data-key="3">3</button>
            <button type="button" data-key="4">4</button>
            <button type="button" data-key="5">5</button>
            <button type="button" data-key="6">6</button>
            <button type="button" data-key="7">7</button>
            <button type="button" data-key="8">8</button>
            <button type="button" data-key="9">9</button>
            <button type="button" data-key=",">,</button>
            <button type="button" data-key="0">0</button>
            <button type="button" class="is-action" data-key="back">Apagar</button>
        </div>
        <div class="keypad-actions">
            <button type="button" class="cancel" id="keypadCancel">Cancelar</button>
            <button type="button" class="confirm" id="keypadConfirm">Confirmar</button>
        </div>
    </div>
</div>

<div class="modal" id="locationModal" hidden>
    <div class="keypad" role="dialog" aria-modal="true" aria-labelledby="locationTitle">
        <h3 id="locationTitle">Leia a locacao</h3>
        <div class="location-info">
            <strong id="locationProduct">Produto</strong>
            <span id="locationQuantity">Quantidade</span>
        </div>
        <form class="location-form" id="locationForm" autocomplete="off">
            <input type="text" id="locationCodigo" inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Leia ou digite o codigo da locacao">
            <button type="submit" id="locationSubmitButton">Confirmar</button>
        </form>
        <div id="locationFeedback" class="location-feedback"></div>
        <div class="location-manual">
            <button type="button" class="location-manual-button" id="manualLocationButton">Escolher manual</button>
            <div class="location-manual-list" id="manualLocationList" hidden></div>
        </div>
        <div class="keypad-actions">
            <button type="button" class="cancel" id="locationCancel">Cancelar</button>
            <button type="button" class="confirm" id="locationBack">Voltar</button>
        </div>
    </div>
</div>

<script>
(function () {
    var feedback = document.getElementById('feedback');
    var processingOverlay = document.getElementById('processingOverlay');
    var modal = document.getElementById('keypadModal');
    var locationModal = document.getElementById('locationModal');
    var locationForm = document.getElementById('locationForm');
    var locationCodigo = document.getElementById('locationCodigo');
    var locationCancel = document.getElementById('locationCancel');
    var locationBack = document.getElementById('locationBack');
    var locationSubmitButton = document.getElementById('locationSubmitButton');
    var locationProduct = document.getElementById('locationProduct');
    var locationQuantity = document.getElementById('locationQuantity');
    var locationFeedback = document.getElementById('locationFeedback');
    var manualLocationButton = document.getElementById('manualLocationButton');
    var manualLocationList = document.getElementById('manualLocationList');
    var display = document.getElementById('keypadDisplay');
    var confirmButton = document.getElementById('keypadConfirm');
    var cancelButton = document.getElementById('keypadCancel');
    var scannerForm = document.getElementById('scannerForm');
    var scannerCodigo = document.getElementById('scannerCodigo');
    var clearFilterButton = document.getElementById('clearFilterButton');
    var scannerIndex = <?php echo json_encode($scannerIndex, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var activeInput = null;

    bindCollectorInput(scannerCodigo);
    bindCollectorInput(locationCodigo, false);
    var activeValue = '0';
    var processing = false;
    var currentFilterCode = '';
    var pendingItem = null;
    var pendingQuantidade = 0;
    var loadingLocations = false;

    function setGlobalProcessing(active) {
        if (!processingOverlay) {
            return;
        }
        processingOverlay.hidden = !active;
        document.body.style.overflow = active ? 'hidden' : '';
    }
    function setFeedback(type, message) {
        feedback.className = 'feedback feedback--' + type;
        feedback.textContent = message;
    }

    function setLocationFeedback(type, message) {
        if (!message) {
            locationFeedback.className = 'location-feedback';
            locationFeedback.textContent = '';
            return;
        }
        locationFeedback.className = 'location-feedback location-feedback--' + type;
        locationFeedback.textContent = message;
    }

    function openKeypad(input) {
        if (!input || input.disabled) {
            return;
        }
        activeInput = input;
        activeValue = input.value && input.value !== '0' ? input.value : '0';
        display.textContent = activeValue;
        modal.hidden = false;
    }

    function closeKeypad() {
        modal.hidden = true;
        activeInput = null;
        activeValue = '0';
        display.textContent = '0';
    }

    function openLocationModal(card, quantidade) {
        pendingItem = card;
        pendingQuantidade = quantidade;
        locationProduct.textContent = (card.querySelector('.item-head h2') || {textContent: 'Produto'}).textContent;
        locationQuantity.textContent = 'Quantidade a separar: ' + formatValue(quantidade);
        locationCodigo.value = '';
        setLocationFeedback('', '');
        manualLocationList.innerHTML = '';
        manualLocationList.hidden = true;
        manualLocationButton.disabled = false;
        manualLocationButton.textContent = 'Escolher manual';
        locationModal.hidden = false;
        setTimeout(function () {
            locationCodigo.focus();
        }, 80);
    }

    function closeLocationModal() {
        locationModal.hidden = true;
        locationCodigo.value = '';
        setLocationFeedback('', '');
        manualLocationList.innerHTML = '';
        manualLocationList.hidden = true;
        manualLocationButton.disabled = false;
        manualLocationButton.textContent = 'Escolher manual';
        locationSubmitButton.disabled = false;
        locationSubmitButton.textContent = 'Confirmar';
        pendingItem = null;
        pendingQuantidade = 0;
        loadingLocations = false;
    }

    function setLocationControlsState(disabled) {
        locationCodigo.disabled = disabled;
        locationModal.querySelectorAll('button').forEach(function (button) {
            button.disabled = disabled;
        });
        locationSubmitButton.textContent = disabled ? 'Processando...' : 'Confirmar';
    }

    function submitLocationCode(codigoLocacao) {
        if (!pendingItem || processing) {
            return;
        }

        if (!codigoLocacao) {
            setLocationFeedback('error', 'Informe ou leia uma locacao.');
            locationCodigo.focus();
            return;
        }

        processing = true;
        setGlobalProcessing(true);
        setLocationControlsState(true);
        setLocationFeedback('loading', 'Processando movimentacao e reprocessando o historico...');

        var body = new URLSearchParams();
        body.set('pedido_id', pendingItem.getAttribute('data-pedido-id'));
        body.set('t006_id', pendingItem.getAttribute('data-item-id'));
        body.set('quantidade', String(pendingQuantidade).replace('.', ','));
        body.set('t066_id', codigoLocacao);

        fetch('./separacao_ajax.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
            body: body.toString()
        })
            .then(parseJsonResponse)
            .then(function (data) {
                if (!data.ok) {
                    setLocationFeedback('error', data.message || 'Nao foi possivel atualizar a separacao.');
                    setFeedback('error', data.message || 'Nao foi possivel atualizar a separacao.');
                    return;
                }

                var card = pendingItem;
                card.querySelector('.separado-qtd').textContent = data.item.qtd_separado_formatada;
                card.querySelector('.saldo-qtd').textContent = data.item.saldo_formatado;
                card.setAttribute('data-saldo', data.item.saldo);
                card.setAttribute('data-completo', data.item.completo ? 'S' : 'N');

                var input = card.querySelector('.qtd-input');
                if (input) {
                    input.value = '0';
                    input.setAttribute('data-saldo', data.item.saldo);
                    input.disabled = Number(data.item.saldo) <= 0;
                }

                setFeedback(data.item.completo ? 'ok' : 'warn', data.message + ' Locacao: ' + data.location.display + '.');
                renderCardLocations(card, data.locacoes || []);
                setLocationFeedback('', '');
                closeLocationModal();
                clearFilter();
                scannerCodigo.focus();
            })
            .catch(function (error) {
                var message = error && error.message ? error.message : 'Falha de comunicacao com o servidor.';
                setLocationFeedback('error', message);
                setFeedback('error', message);
            })
            .finally(function () {
                processing = false;
                setGlobalProcessing(false);
                setLocationControlsState(false);
                if (!locationModal.hidden) {
                    locationCodigo.focus();
                }
            });
    }

    function formatLocationsText(locacoes) {
        if (!locacoes || !locacoes.length) {
            return '';
        }

        return locacoes.map(function (locacao) {
            return 'Local: ' + (locacao.D004_Local || '-') + ' - Qtde: ' + (locacao.estoque_formatado || formatValue(locacao.T066_Quantidade_Estoque || 0));
        }).join(' / ');
    }

    function renderCardLocations(card, locacoes) {
        if (!card) {
            return;
        }

        var head = card.querySelector('.item-head > div');
        if (!head) {
            return;
        }

        var box = card.querySelector('.item-locations');
        if (!locacoes || !locacoes.length) {
            if (box) {
                box.remove();
            }
            return;
        }

        if (!box) {
            box = document.createElement('div');
            box.className = 'item-locations';
            head.appendChild(box);
        }

        box.innerHTML = '<strong>Locacoes:</strong><span>' + formatLocationsText(locacoes) + '</span>';
    }

    function carregarLocacoesItem(card, onSuccess, onError) {
        if (!card) {
            if (typeof onError === 'function') {
                onError(new Error('Item nao encontrado.'));
            }
            return;
        }

        var body = new URLSearchParams();
        body.set('acao', 'listar_locacoes');
        body.set('pedido_id', card.getAttribute('data-pedido-id'));
        body.set('t006_id', card.getAttribute('data-item-id'));

        fetch('./separacao_ajax.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
            body: body.toString()
        })
            .then(parseJsonResponse)
            .then(function (data) {
                if (!data.ok) {
                    throw new Error(data.message || 'Nao foi possivel buscar as locacoes.');
                }

                renderCardLocations(card, data.locacoes || []);
                if (typeof onSuccess === 'function') {
                    onSuccess(data.locacoes || []);
                }
            })
            .catch(function (error) {
                if (typeof onError === 'function') {
                    onError(error);
                }
            });
    }

    function renderManualLocations(locacoes) {
        manualLocationList.innerHTML = '';

        if (!locacoes.length) {
            var empty = document.createElement('div');
            empty.className = 'location-manual-empty';
            empty.textContent = 'Nenhuma locacao encontrada para este produto.';
            manualLocationList.appendChild(empty);
            manualLocationList.hidden = false;
            return;
        }

        locacoes.forEach(function (locacao) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'location-option';
            button.disabled = processing;
            button.innerHTML =
                '<div><strong>' + locacao.display + '</strong><span>Estoque: ' + locacao.estoque_formatado + '</span></div>' +
                '<em>Selecionar</em>';
            button.addEventListener('click', function () {
                if (Number(locacao.T066_Quantidade_Estoque || 0) + 0.00001 < Number(pendingQuantidade || 0)) {
                    setLocationFeedback('error', 'Estoque insuficiente na locacao selecionada (' + locacao.estoque_formatado + ').');
                    return;
                }
                locationCodigo.value = String(locacao.T066_Id || '');
                submitLocationCode(locationCodigo.value.trim());
            });
            manualLocationList.appendChild(button);
        });

        manualLocationList.hidden = false;
    }

    function clearMatches() {
        document.querySelectorAll('.item-card.is-scan-match').forEach(function (card) {
            card.classList.remove('is-scan-match');
        });
    }

    function focusCard(itemId, openKeyboard) {
        clearMatches();
        var card = document.getElementById('item-' + itemId);
        if (!card) {
            setFeedback('error', 'Linha do pedido nao encontrada.');
            return;
        }
        card.classList.add('is-scan-match');
        card.scrollIntoView({behavior: 'smooth', block: 'center'});
        var input = card.querySelector('.qtd-input');
        if (openKeyboard && input) {
            setTimeout(function () {
                openKeypad(input);
            }, 150);
        }
    }

    function applyFilter(codigo) {
        currentFilterCode = codigo;
        clearMatches();
        var firstMatchId = null;
        var matchCount = 0;

        document.querySelectorAll('.item-card').forEach(function (card) {
            var isMatch = card.getAttribute('data-codigo-produto') === codigo;
            card.classList.toggle('is-filter-hidden', !isMatch);
            card.classList.toggle('is-scan-match', isMatch);
            if (isMatch) {
                matchCount++;
                if (!firstMatchId) {
                    firstMatchId = card.getAttribute('data-item-id');
                }
            }
        });

        if (firstMatchId) {
            focusCard(firstMatchId, false);
        }

        return matchCount;
    }

    function clearFilter() {
        currentFilterCode = '';
        scannerCodigo.value = '';
        clearMatches();
        document.querySelectorAll('.item-card').forEach(function (card) {
            card.classList.remove('is-filter-hidden');
        });
    }

    function normalizeValue(value) {
        value = String(value || '').replace(/\./g, '').replace(',', '.');
        var number = parseFloat(value);
        return Number.isFinite(number) ? number : 0;
    }

    function formatValue(value) {
        return Number(value).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function parseJsonResponse(response) {
        return response.text().then(function (text) {
            try {
                return JSON.parse(text);
            } catch (error) {
                var rawText = String(text || '').trim();
                throw new Error(rawText || 'Resposta invalida do servidor.');
            }
        });
    }

    document.querySelectorAll('.qtd-input').forEach(function (input) {
        input.addEventListener('click', function () {
            openKeypad(input);
        });
    });

    modal.querySelectorAll('[data-key]').forEach(function (button) {
        button.addEventListener('click', function () {
            var key = button.getAttribute('data-key');
            if (key === 'back') {
                activeValue = activeValue.length > 1 ? activeValue.slice(0, -1) : '0';
            } else if (key === ',') {
                if (activeValue.indexOf(',') === -1) {
                    activeValue += ',';
                }
            } else {
                activeValue = activeValue === '0' ? key : activeValue + key;
            }

            display.textContent = activeValue || '0';
        });
    });

    document.addEventListener('keydown', function (event) {
        if (modal.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            closeKeypad();
            event.preventDefault();
            return;
        }

        if (event.key === 'Enter') {
            confirmButton.click();
            event.preventDefault();
            return;
        }

        if (event.key === 'Backspace') {
            activeValue = activeValue.length > 1 ? activeValue.slice(0, -1) : '0';
            display.textContent = activeValue || '0';
            event.preventDefault();
            return;
        }

        if ((event.key === ',' || event.key === '.') && activeValue.indexOf(',') === -1) {
            activeValue += ',';
            display.textContent = activeValue || '0';
            event.preventDefault();
            return;
        }

        if (/^[0-9]$/.test(event.key)) {
            activeValue = activeValue === '0' ? event.key : activeValue + event.key;
            display.textContent = activeValue || '0';
            event.preventDefault();
        }
    });

    cancelButton.addEventListener('click', closeKeypad);
    clearFilterButton.addEventListener('click', function () {
        clearFilter();
        scannerCodigo.focus();
    });
    locationCancel.addEventListener('click', function () {
        closeLocationModal();
        scannerCodigo.focus();
    });
    locationBack.addEventListener('click', function () {
        locationModal.hidden = true;
        if (pendingItem) {
            var input = pendingItem.querySelector('.qtd-input');
            if (input && !input.disabled) {
                openKeypad(input);
            }
        }
    });
    manualLocationButton.addEventListener('click', function () {
        if (!pendingItem || loadingLocations || processing) {
            return;
        }

        loadingLocations = true;
        manualLocationButton.disabled = true;
        manualLocationButton.textContent = 'Carregando...';

        var finalizarBuscaManual = function () {
            loadingLocations = false;
            manualLocationButton.disabled = false;
            manualLocationButton.textContent = 'Escolher manual';
        };

        carregarLocacoesItem(pendingItem, function (locacoes) {
            setLocationFeedback('', '');
            renderManualLocations(locacoes || []);
            finalizarBuscaManual();
        }, function (error) {
            var message = error && error.message ? error.message : 'Falha de comunicacao ao buscar as locacoes.';
            setLocationFeedback('error', message);
            setFeedback('error', message);
            finalizarBuscaManual();
        });
    });

    scannerForm.addEventListener('submit', function (event) {
        event.preventDefault();
        var codigo = scannerCodigo.value.trim();
        if (!codigo) {
            scannerCodigo.focus();
            return;
        }

        clearMatches();
        var options = scannerIndex[codigo] || [];
        if (!options.length) {
            setFeedback('error', 'Codigo do produto nao encontrado neste pedido.');
            scannerCodigo.focus();
            scannerCodigo.select();
            return;
        }

        var matchCount = applyFilter(codigo);
        if (matchCount === 1) {
            setFeedback('ok', 'Produto localizado. Informe a quantidade na linha filtrada.');
        } else {
            setFeedback('warn', 'Codigo repetido no pedido. Escolha a linha correta pelo T006.');
        }
    });

    confirmButton.addEventListener('click', function () {
        if (!activeInput || processing) {
            return;
        }

        var quantidade = normalizeValue(activeValue);
        if (quantidade <= 0) {
            setFeedback('error', 'Informe uma quantidade maior que zero.');
            return;
        }

        var itemId = activeInput.getAttribute('data-item-id');
        var card = document.getElementById('item-' + itemId);
        if (!card) {
            setFeedback('error', 'Item nao encontrado na tela.');
            return;
        }

        if ((card.getAttribute('data-pintado') || 'N') === 'S') {
            var retornadaDisponivel = Number(card.getAttribute('data-retornada-disponivel') || '0');
            if (quantidade - retornadaDisponivel > 0.00001) {
                setFeedback('error', 'Item pintado libera somente ' + formatValue(retornadaDisponivel) + ' para separar.');
                return;
            }
        }

        closeKeypad();
        openLocationModal(card, quantidade);
    });

    locationForm.addEventListener('submit', function (event) {
        event.preventDefault();
        submitLocationCode(locationCodigo.value.trim());
    });
})();
</script>
</body>
</html>











