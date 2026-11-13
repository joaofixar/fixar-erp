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

function inecel_company_rows(): array
{
    $conn = inecel_db();
    $usuarioId = (int) ($_SESSION['Usuario_Atual'] ?? 0);
    if ($usuarioId <= 0) {
        return [];
    }

    $sql = <<<SQL
SELECT
    C019.C019_C004_Id,
    C019.C019_C021_Id,
    C019.C019_Ordem_Empresa,
    C004.C004_Nome_Empresa,
    C004.C004_Nome_Abreviado,
    C004.C004_Caminho_Sistema,
    C004.C004_Banco_Configuracao,
    C004.C004_Flag_Ativo,
    C004.C004_Ordem_Empresa
FROM C019
INNER JOIN C004 ON C004.C004_Id = C019.C019_C004_Id
WHERE C019.C019_C007_Id = {$usuarioId}
  AND C004.C004_Flag_Ativo = 'S'
ORDER BY
    CASE
        WHEN IFNULL(C019.C019_Ordem_Empresa, 0) > 0 THEN 0
        ELSE 1
    END ASC,
    CASE
        WHEN IFNULL(C019.C019_Ordem_Empresa, 0) > 0 THEN C019.C019_Ordem_Empresa
        ELSE C004.C004_Ordem_Empresa
    END ASC,
    C004.C004_Id ASC
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

function inecel_apply_company_session(array $company): void
{
    $_SESSION['Perfil_Usuario'] = $company['C019_C021_Id'];
    $_SESSION['Empresa_Atual'] = $company['C019_C004_Id'];
    $_SESSION['Empresa_Logada'] = $company['C019_C004_Id'];
    $_SESSION['Nome_Empresa_Atual'] = $company['C004_Nome_Empresa'];
    $_SESSION['Pasta_Empresa'] = $company['C004_Caminho_Sistema'];
    $_SESSION['cBd_Configuracao'] = $company['C004_Banco_Configuracao'];
}

function inecel_switch_company(int $empresaId, array $companies): bool
{
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaId) {
            inecel_apply_company_session($company);
            return true;
        }
    }

    return false;
}

function inecel_company_theme(int $empresaId): array
{
    $themes = [
        ['bg' => '#eef6ff', 'panel' => '#ffffff', 'line' => '#c9dffc', 'primary' => '#155eef', 'accent' => '#dceaff', 'alt' => '#168a4a'],
        ['bg' => '#f3fbf5', 'panel' => '#ffffff', 'line' => '#cfe8d5', 'primary' => '#168a4a', 'accent' => '#dff5e6', 'alt' => '#155eef'],
        ['bg' => '#fff8ef', 'panel' => '#ffffff', 'line' => '#f0dac0', 'primary' => '#b35a00', 'accent' => '#ffe8cf', 'alt' => '#6941c6'],
        ['bg' => '#fff1f3', 'panel' => '#ffffff', 'line' => '#f1ccd4', 'primary' => '#c23d5a', 'accent' => '#ffdfe5', 'alt' => '#0f8b8d'],
        ['bg' => '#f5f3ff', 'panel' => '#ffffff', 'line' => '#ddd7fb', 'primary' => '#6941c6', 'accent' => '#ebe5ff', 'alt' => '#b35a00'],
        ['bg' => '#eefaf9', 'panel' => '#ffffff', 'line' => '#c8e9e4', 'primary' => '#0f8b8d', 'accent' => '#d7f4ef', 'alt' => '#c23d5a'],
    ];

    return $themes[$empresaId % count($themes)];
}

function inecel_selected_company_name(array $companies): string
{
    $empresaAtual = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaAtual) {
            return (string) ($company['C004_Nome_Abreviado'] ?: $company['C004_Nome_Empresa']);
        }
    }

    return (string) ($_SESSION['Nome_Empresa_Atual'] ?? 'Empresa');
}


$companies = inecel_company_rows();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empresa_id'])) {
    $empresaId = (int) $_POST['empresa_id'];
    if (inecel_switch_company($empresaId, $companies)) {
        header('Location: ./menu.php');
        exit;
    }
}

$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$empresaAtualId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$companyTheme = inecel_company_theme($empresaAtualId);
$empresaCurta = inecel_selected_company_name($companies);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Inicio | Inecel</title>
    <style>
        :root {
            --bg:<?php echo inecel_h($companyTheme['bg']); ?>;
            --panel:<?php echo inecel_h($companyTheme['panel']); ?>;
            --text:#17202a;
            --muted:#667085;
            --line:<?php echo inecel_h($companyTheme['line']); ?>;
            --primary:<?php echo inecel_h($companyTheme['primary']); ?>;
            --accent:<?php echo inecel_h($companyTheme['accent']); ?>;
            --alt:<?php echo inecel_h($companyTheme['alt']); ?>;
        }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:var(--bg); color:var(--text); font-family:Arial, Helvetica, sans-serif; }
        .app { width:min(1080px, 100%); margin:0 auto; padding:14px; }
        .topbar { display:flex; gap:12px; align-items:center; justify-content:space-between; padding:12px 0 20px; }
        .title h1 { margin:0; font-size:24px; line-height:1.1; }
        .title p { margin:4px 0 0; color:var(--muted); font-size:13px; }
        .topbar-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        .logout { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:0 14px; border-radius:7px; background:var(--panel); border:1px solid var(--line); color:var(--primary); font-weight:700; text-decoration:none; white-space:nowrap; }
        .company-panel { margin:0 0 14px; padding:14px; border:1px solid var(--line); border-radius:8px; background:var(--panel); }
        .company-panel h2 { margin:0; font-size:16px; }
        .company-panel p { margin:4px 0 0; color:var(--muted); font-size:13px; }
        .company-switch {
            display:grid;
            grid-template-columns:minmax(0, 1fr) auto;
            gap:10px;
            align-items:end;
            margin-top:12px;
        }
        .company-select-wrap { display:flex; flex-direction:column; gap:6px; }
        .company-select-wrap label { color:var(--muted); font-size:12px; font-weight:700; text-transform:uppercase; }
        .company-select {
            width:100%;
            min-height:52px;
            border:1px solid var(--line);
            border-radius:8px;
            padding:0 14px;
            background:#fff;
            color:var(--text);
            font-size:16px;
            font-weight:700;
            box-shadow:0 1px 2px rgba(16,24,40,.04);
        }
        .company-current {
            display:flex;
            flex-direction:column;
            justify-content:center;
            min-height:52px;
            padding:0 14px;
            border-radius:8px;
            background:var(--accent);
            border:1px solid var(--line);
            color:var(--primary);
            white-space:nowrap;
        }
        .company-current strong { font-size:15px; line-height:1.2; }
        .company-current span { margin-top:2px; font-size:11px; font-weight:700; text-transform:uppercase; }
        .grid { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:12px; }
        .card {
            display:grid;
            grid-template-columns:1fr auto;
            gap:12px;
            align-items:end;
            min-height:0;
            padding:16px;
            border-radius:8px;
            border:1px solid var(--line);
            background:var(--panel);
            text-decoration:none;
            color:inherit;
            box-shadow:0 2px 8px rgba(16,24,40,.04);
        }
        .card-main { min-width:0; }
        .card-kicker {
            display:inline-flex;
            align-items:center;
            min-height:24px;
            padding:0 9px;
            border-radius:999px;
            background:var(--accent);
            color:var(--primary);
            font-size:11px;
            font-weight:700;
        }
        .card--primary { border-left:4px solid var(--primary); }
        .card--primary .card-kicker { background:var(--accent); color:var(--primary); }
        .card--alt { border-left:4px solid var(--alt); }
        .card--alt .card-kicker { background:#eefaf9; color:var(--alt); }
        .card--neutral { border-left:4px solid #475467; }
        .card--neutral .card-kicker { background:#eef2f6; color:#475467; }
        .card--disabled { opacity:.72; cursor:not-allowed; }
        .card--disabled:hover { transform:none; }
        .card-note { margin-top:10px; color:#b42318; font-size:12px; font-weight:700; }
        .card-flag { display:inline-flex; align-items:center; justify-content:center; min-height:24px; padding:0 10px; border-radius:999px; background:#b42318; color:#fff; font-size:11px; font-weight:700; text-transform:uppercase; }
        .card h2 { margin:10px 0 0; font-size:17px; line-height:1.25; }
        .card p { margin:8px 0 0; color:var(--muted); font-size:13px; line-height:1.45; }
        .count-box {
            display:flex;
            flex-direction:column;
            align-items:flex-end;
            justify-content:flex-end;
            gap:4px;
            min-width:64px;
            text-align:right;
        }
        .count-label {
            color:var(--muted);
            font-size:10px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.04em;
        }
        .count {
            display:block;
            min-width:0;
            min-height:0;
            padding:0;
            border:0;
            background:transparent;
            font-size:34px;
            font-weight:700;
            line-height:1;
            color:var(--text);
        }
        @media (max-width:720px) {
            .app { padding:10px; }
            .topbar { flex-direction:column; align-items:flex-start; }
            .topbar-actions { width:100%; justify-content:stretch; }
            .logout { width:100%; }
            .grid { grid-template-columns:1fr; }
            .card { grid-template-columns:1fr; align-items:start; }
            .count-box { width:100%; align-items:flex-start; text-align:left; margin-top:2px; }
            .count { font-size:30px; }
            .count { min-width:68px; min-height:68px; font-size:26px; }
            .company-switch { grid-template-columns:1fr; }
            .company-current { white-space:normal; }
        }
    </style>
</head>
<body>
<div class="app">
    <header class="topbar">
        <div class="title">
            <h1>Escolha o processo</h1>
            <p><?php echo inecel_h($empresa); ?> - <?php echo inecel_h($usuario); ?></p>
        </div>
        <div class="topbar-actions">
            <a class="logout" href="./menu.php?logout=1">Sair</a>
        </div>
    </header>

    <?php if ($companies): ?>
        <section class="company-panel">
            <h2>Empresa atual: <?php echo inecel_h($empresaCurta); ?></h2>
            <p>Escolha a empresa aqui antes de entrar no processo.</p>
            <form method="post" class="company-switch">
                <div class="company-select-wrap">
                    <label for="empresa_id">Selecao de empresa</label>
                    <select class="company-select" id="empresa_id" name="empresa_id" onchange="this.form.submit()">
                        <?php foreach ($companies as $company): ?>
                            <?php $companyId = (int) $company['C019_C004_Id']; ?>
                            <option value="<?php echo inecel_h($companyId); ?>" <?php echo $companyId === $empresaAtualId ? 'selected' : ''; ?>>
                                <?php echo inecel_h($company['C004_Nome_Empresa']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="company-current">
                    <strong><?php echo inecel_h($empresaCurta); ?></strong>
                    <span>Selecionada</span>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <section class="grid">
        <a class="card card--primary" href="./pedidos.php">
            <div class="card-main">
                <span class="card-kicker">Conferencia</span>
                <h2>Pedidos para conferencia</h2>
            </div>
        </a>

        <div class="card card--alt card--disabled" role="status" aria-disabled="true">
            <div class="card-main">
                <span class="card-kicker">Separacao</span>
                <h2>Pedidos para separacao</h2>
                <span class="card-flag">Indisponivel</span>
                <div class="card-note">Em desenvolvimento no momento.</div>
            </div>
        </div>

        <a class="card card--neutral" href="./consulta_estoque.php">
            <div class="card-main">
                <span class="card-kicker">Estoque</span>
                <h2>Consulta de estoque</h2>
            </div>
        </a>

        <a class="card card--primary" href="./movimentacao_estoque.php">
            <div class="card-main">
                <span class="card-kicker">Movimentacao</span>
                <h2>Movimentacao de estoque</h2>
            </div>
        </a>
    </section>
</div>
</body>
</html>
