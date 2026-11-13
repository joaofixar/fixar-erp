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

function inecel_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function inecel_db(): mysqli
{
    global $g;
    return $g['conexaoBanco'];
}

function inecel_company_theme(int $empresaId): array
{
    $themes = [
        ['bg' => '#eef6ff', 'panel' => '#ffffff', 'line' => '#c9dffc', 'primary' => '#155eef', 'accent' => '#dceaff'],
        ['bg' => '#f3fbf5', 'panel' => '#ffffff', 'line' => '#cfe8d5', 'primary' => '#168a4a', 'accent' => '#dff5e6'],
        ['bg' => '#fff8ef', 'panel' => '#ffffff', 'line' => '#f0dac0', 'primary' => '#b35a00', 'accent' => '#ffe8cf'],
        ['bg' => '#fff1f3', 'panel' => '#ffffff', 'line' => '#f1ccd4', 'primary' => '#c23d5a', 'accent' => '#ffdfe5'],
        ['bg' => '#f5f3ff', 'panel' => '#ffffff', 'line' => '#ddd7fb', 'primary' => '#6941c6', 'accent' => '#ebe5ff'],
        ['bg' => '#eefaf9', 'panel' => '#ffffff', 'line' => '#c8e9e4', 'primary' => '#0f8b8d', 'accent' => '#d7f4ef'],
    ];

    return $themes[$empresaId % count($themes)];
}

function inecel_pedido_rows(string $search, string $statusFilter): array
{
    $conn = inecel_db();
    $empresaId = (int) $_SESSION['Empresa_Atual'];
    $statusList = $statusFilter === 'coletados' ? '5' : '3, 4';
    $where = "WHERE T005.T005_C004_Id = '{$empresaId}' AND T005.T005_Flag_Status IN ({$statusList}) AND (T005.T005_T005_Id_Agrupado <= 0 OR T005.T005_T005_Id_Agrupado IS NULL)";

    if ($search !== '') {
        $searchSql = mysqli_real_escape_string($conn, $search);
        $where .= " AND (T005.T005_Id LIKE '%{$searchSql}%' OR D024.D024_Nome_Empresa LIKE '%{$searchSql}%' OR T005.T005_Nome_Status LIKE '%{$searchSql}%')";
    }

    if ($statusFilter === 'coletados') {
        $orderLimit = 'ORDER BY T005.T005_Data_Emissao DESC, T005.T005_Id DESC';
        if ($search === '') {
            $orderLimit .= ' LIMIT 10';
        }
    } else {
        $orderLimit = 'ORDER BY T005.T005_Data_Emissao ASC, T005.T005_Id ASC';
    }

    $sql = <<<SQL
SELECT
    T005.T005_Id,
    T005.T005_Flag_Status,
    T005.T005_Nome_Status,
    T005.T005_Data_Emissao,
    T005.T005_Data_Entrega,
    T005.T005_Valor_Total,
    T005.T005_Peso_Total,
    D024.D024_Nome_Empresa,
    COUNT(T314.T314_Id) AS Total_Volumes,
    SUM(IF(IFNULL(T314A.T314A_Flag_Coletado, 'N') = 'S', 1, 0)) AS Volumes_Coletados
FROM T005
LEFT JOIN D024 ON D024.D024_Id = T005.T005_D024_Id
LEFT JOIN T314 ON T314.T314_T005_Id = T005.T005_Id
LEFT JOIN T314A ON T314A.T314A_T314_Id = T314.T314_Id
{$where}
GROUP BY T005.T005_Id, T005.T005_Flag_Status, T005.T005_Nome_Status, T005.T005_Data_Emissao, T005.T005_Data_Entrega, T005.T005_Valor_Total, T005.T005_Peso_Total, D024.D024_Nome_Empresa
{$orderLimit}
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

function inecel_format_date($value): string
{
    if (empty($value) || $value === '0000-00-00') {
        return '';
    }

    $time = strtotime((string) $value);
    return $time ? date('d/m/Y', $time) : '';
}

$search = trim((string) ($_GET['q'] ?? ''));
$statusFilter = ($_GET['status'] ?? '') === 'coletados' ? 'coletados' : 'pendentes';
$pedidos = inecel_pedido_rows($search, $statusFilter);
$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$empresaAtualId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$companyTheme = inecel_company_theme($empresaAtualId);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, iuitial-scale=1">
    <meta name="robots" content="uoindex, nofollow">
    <title>Pedidos | Inecel</title>
    <style>
        :root {
            --bg:<?php echo inecel_h($companyTheme['bg']); ?>;
            --panel:<?php echo inecel_h($companyTheme['panel']); ?>;
            --text:#17202a;
            --muted:#667085;
            --line:<?php echo inecel_h($companyTheme['line']); ?>;
            --primary:<?php echo inecel_h($companyTheme['primary']); ?>;
            --accent:<?php echo inecel_h($companyTheme['accent']); ?>;
            --ok:#168a4a;
            --warn:#a05a00;
        }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; background:var(--bg); color:var(--text); font-family:Arial, Helvetica, sans-serif; }
        .app { width:min(1180px, 100%); margin:0 auto; padding:14px; }
        .topbar { display:flex; gap:12px; align-items:center; justify-content:space-between; padding:12px 0 16px; }
        .title h1 { margin:0; font-size:22px; line-height:1.15; }
        .title p { margin:4px 0 0; color:var(--muted); font-size:13px; }
        .topbar-actious { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        .quick-nav { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; width:min(520px, 100%); }
        .quick-nav a { display:inline-flex; align-items:center; justify-content:center; min-height:52px; padding:0 14px; border-radius:9px; background:var(--panel); border:1px solid var(--line); color:var(--primary); font-weight:700; text-decoration:none; white-space:nowrap; font-size:15px; }
        .quick-nav a.is-active { background:var(--primary); color:#fff; }
        .quick-nav .is-disabled { display:inline-flex; align-items:center; justify-content:center; min-height:52px; padding:0 14px; border-radius:9px; background:var(--panel); border:1px solid var(--line); color:#98a2b3; font-weight:700; white-space:nowrap; font-size:15px; pointer-events:none; cursor:not-allowed; opacity:.7; }
        .status-filter { display:flex; gap:8px; padding-bottom:12px; flex-wrap:wrap; }
        .status-filter a { display:inline-flex; align-items:center; justify-content:center; min-height:40px; padding:0 16px; border-radius:999px; background:var(--panel); border:1px solid var(--line); color:var(--text); font-weight:700; text-decoration:none; font-size:14px; }
        .status-filter a.is-active { background:var(--primary); border-color:var(--primary); color:#fff; }
        .filter-hint { margin:-4px 0 12px; color:var(--muted); font-size:13px; }
        .search { position:sticky; top:0; z-index:2; display:grid; grid-template-columns:1fr auto; gap:8px; padding-bottom:12px; background:var(--bg); }
        input, button, select { min-height:46px; border-radius:7px; font-size:16px; }
        input, select { width:100%; border:1px solid #b8c2cf; padding:0 12px; background:#fff; }
        button { border:0; padding:0 16px; background:var(--primary); color:#fff; font-weight:700; cursor:pointer; }
        .pedido-list { display:grid; gap:9px; }
        .pedido { display:grid; grid-template-columns:1fr auto; gap:10px; align-items:center; background:var(--panel); border:1px solid var(--line); border-left:6px solid var(--primary); border-radius:8px; padding:12px; color:inherit; text-decoration:none; }
        .pedido h2 { margin:0; font-size:20px; line-height:1.2; }
        .pedido-title { display:flex; flex-wrap:wrap; gap:6px; align-items:baseline; font-weight:700; }
        .pedido-title__uumero { color:var(--primary); }
        .pedido-title__pessoa { color:var(--text); }
        .pedido p { margin:5px 0 0; color:var(--muted); font-size:13px; }
        .meta-line { display:flex; flex-wrap:wrap; gap:8px 12px; }
        .badge { display:inline-flex; align-items:center; justify-content:center; min-width:94px; min-height:32px; border-radius:999px; padding:0 10px; font-weight:700; font-size:12px; color:#fff; background:var(--warn); }
        .badge--ok { background:var(--ok); }
        .empty { padding:22px; background:var(--panel); border:1px solid var(--line); border-radius:8px; color:var(--muted); text-align:center; }
        @media (max-width:640px) {
            .app { padding:10px; }
            .topbar { align-items:flex-start; flex-direction:column; }
            .topbar-actious { width:100%; justify-content:stretch; }
            .quick-nav { width:100%; }
            .title h1 { font-size:19px; }
            .search { grid-template-columns:1fr; }
            .pedido { grid-template-columns:1fr; }
            .badge { width:100%; }
        }
    </style>
</head>
<body>
<div class="app">
    <header class="topbar">
        <div class="title">
            <h1>Pedidos para conferencia</h1>
            <p><?php echo inecel_h($empresa); ?> - <?php echo inecel_h($usuario); ?></p>
        </div>
        <div class="topbar-actious">
            <nav class="quick-nav" aria-label="Navegacao">
                <a href="./menu.php">Inicio</a>
                <a class="is-active" href="./pedidos.php">Conferencia</a>
                <span class="is-disabled" aria-disabled="true">Separacao</span>
            </nav>
        </div>
    </header>

    <nav class="status-filter" aria-label="Filtro por status">
        <a class="<?php echo $statusFilter === 'pendentes' ? 'is-active' : ''; ?>" href="?status=pendentes&amp;q=<?php echo urlencode($search); ?>">Pendentes</a>
        <a class="<?php echo $statusFilter === 'coletados' ? 'is-active' : ''; ?>" href="?status=coletados&amp;q=<?php echo urlencode($search); ?>">Coletados</a>
    </nav>

    <form class="search" method="get">
        <input type="hidden" name="status" value="<?php echo inecel_h($statusFilter); ?>">
        <input name="q" value="<?php echo inecel_h($search); ?>" autofocus inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Buscar por pedido, pessoa ou status">
        <button type="submit">Buscar</button>
            </form>

    <?php if ($statusFilter === 'coletados' && $search === ''): ?>
        <p class="filter-hint">Mostrando os ultimos 10 pedidos coletados. Use a busca para encontrar um pedido especifico.</p>
    <?php endif; ?>

    <section class="pedido-list">
        <?php foreach ($pedidos as $pedido): ?>
            <?php
                $totalVolumes = (int) ($pedido['Total_Volumes'] ?? 0);
                $volumesColetados = (int) ($pedido['Volumes_Coletados'] ?? 0);
                $todosColetados = $totalVolumes > 0 && $volumesColetados >= $totalVolumes;
            ?>
            <a class="pedido" href="./volumes.php?pedido_id=<?php echo inecel_h($pedido['T005_Id']); ?>&amp;status=<?php echo inecel_h($statusFilter); ?>">
                <div>
                    <h2 class="pedido-title">
                        <span class="pedido-title__uumero">Pedido <?php echo inecel_h($pedido['T005_Id']); ?></span>
                        <span>-</span>
                        <span class="pedido-title__pessoa"><?php echo inecel_h($pedido['D024_Nome_Empresa'] ?: 'Pessoa nao informada'); ?></span>
                    </h2>
                    <p class="meta-line">
                        <?php echo inecel_h($pedido['T005_Nome_Status']); ?>
                        <?php $dataEmissao = inecel_format_date($pedido['T005_Data_Emissao'] ?? ''); ?>
                        <?php if ($dataEmissao !== ''): ?>
                            <span>Emissao <?php echo inecel_h($dataEmissao); ?></span>
                        <?php endif; ?>
                        <?php $dataEntrega = inecel_format_date($pedido['T005_Data_Entrega'] ?? ''); ?>
                        <?php if ($dataEntrega !== ''): ?>
                            <span>Entrega <?php echo inecel_h($dataEntrega); ?></span>
                        <?php endif; ?>
                        <span>Valor R$ <?php echo inecel_h(number_format((float) $pedido['T005_Valor_Total'], 2, ',', '.')); ?></span>
                        <span>Peso <?php echo inecel_h(number_format((float) $pedido['T005_Peso_Total'], 3, ',', '.')); ?> kg</span>
                    </p>
                </div>
                <span class="badge <?php echo $todosColetados ? 'badge--ok' : ''; ?>"><?php echo inecel_h($volumesColetados . '/' . $totalVolumes); ?></span>
            </a>
        <?php endforeach; ?>

        <?php if (!$pedidos): ?>
            <div class="empty"><?php echo $statusFilter === 'coletados' ? 'Nenhum pedido coletado (status 5) para a empresa logada.' : 'Nenhum pedido nos status 3 ou 4 para a empresa logada.'; ?></div>
        <?php endif; ?>
    </section>
</div>
<script>
(function () {
        function bindCollectorInput(input, autoFocus) {
        var shouldAutoFocus = autoFocus !== false;
        var form = input ? input.closest('form') : null;
        var manualMode = false;

        if (!input || input.disabled || !form) {
            return;
        }

        function focusInput() {
            input.focus({ preventScroll: true });
            var valueLength = input.value.length;
            if (typeof input.setSelectionRange === 'function') {
                input.setSelectionRange(valueLength, valueLength);
            }
        }

        function lockScannerMode() {
            manualMode = false;
            input.readOnly = false;
            input.setAttribute('inputmode', 'none');
            input.setAttribute('virtualkeyboardpolicy', 'manual');
        }

        function unlockKeyboardMode() {
            manualMode = true;
            input.setAttribute('inputmode', 'text');
            input.setAttribute('virtualkeyboardpolicy', 'auto');
            focusInput();
        }

        lockScannerMode();

        if (shouldAutoFocus) {
            setTimeout(function () {
                focusInput();
            }, 120);
        }

        input.addEventListener('dblclick', function (event) {
            event.preventDefault();
            unlockKeyboardMode();
        });

        input.addEventListener('blur', function () {
            if (manualMode) {
                lockScannerMode();
            }
        });

        input.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        });
    }
    bindCollectorInput(document.querySelector('input[name="q"][data-unlock-dblclick="true"]'));
})();
</script></body>
</html>

