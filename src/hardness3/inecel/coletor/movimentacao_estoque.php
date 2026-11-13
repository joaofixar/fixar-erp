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

function inecel_format_number($value, int $decimals = 2): string
{
    return number_format((float) $value, $decimals, ',', '.');
}

function inecel_codigo_busca(string $value): string
{
    return trim($value);
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

function inecel_movimento_summary(string $search): ?array
{
    if ($search === '') {
        return null;
    }

    $conn = inecel_db();
    $empresaId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    $searchSql = mysqli_real_escape_string($conn, $search);

    $sql = <<<SQL
SELECT
    D001.D001_Id,
    D009.D009_Id,
    D001.D001_Flag_Pre_Cadastro,
    IFNULL(D001.D001_Qte_Embalagem, 0) AS D001_Qte_Embalagem,
    D001.D001_Codigo_Produto,
    D001.D001_Descricao_Produto,
    D082.D082_Marca,
    IFNULL(D009.D009_Quantidade_Estoque, 0) AS D009_Quantidade_Estoque,
    IFNULL(D009.D009_Quantidade_Estoque_Liquido, 0) AS D009_Quantidade_Estoque_Liquido,
    GREATEST(IFNULL(D009.D009_Quantidade_Estoque, 0) - IFNULL(D009.D009_Quantidade_Estoque_Liquido, 0), 0) AS reserva,
    0 AS quantidade_pintado,
    0 AS quantidade_sem_pintura
FROM D001
INNER JOIN D049 ON D049.D049_D001_Id = D001.D001_Id
INNER JOIN D009 ON D009.D009_D049_Id = D049.D049_Id
LEFT JOIN D082 ON D082.D082_Id = D049.D049_D082_Id
WHERE D009.D009_C004_Id = '{$empresaId}'
  AND D001.D001_Flag_Ativo = 'S'
  AND IFNULL(D049.D049_Flag_Tipo, '') NOT IN ('CP', 'MT')
  AND D001.D001_Codigo_Produto = '{$searchSql}'
LIMIT 1
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        return null;
    }

    $descricao = (string) ($row['D001_Descricao_Produto'] ?? '');
    if (($row['D001_Flag_Pre_Cadastro'] ?? '') === 'S') {
        $descricao .= ' (PRE)';
    }

    return [
        'produto_id' => (int) ($row['D001_Id'] ?? 0),
        'codigo' => (string) ($row['D001_Codigo_Produto'] ?? ''),
        'descricao' => $descricao,
        'marca' => (string) ($row['D082_Marca'] ?? ''),
        'd009_id' => (int) ($row['D009_Id'] ?? 0),
        'qte_embalagem' => (float) ($row['D001_Qte_Embalagem'] ?? 0),
        'total_fisico' => (float) ($row['D009_Quantidade_Estoque'] ?? 0),
        'reserva' => (float) ($row['reserva'] ?? 0),
        'quantidade_sem_pintura' => (float) ($row['quantidade_sem_pintura'] ?? 0),
        'quantidade_pintado' => (float) ($row['quantidade_pintado'] ?? 0),
        'total_liquido' => (float) ($row['D009_Quantidade_Estoque_Liquido'] ?? 0),
    ];
}

function inecel_movimento_locacoes(int $d009Id): array
{
    if ($d009Id <= 0) {
        return [];
    }

    $conn = inecel_db();

    $sql = <<<SQL
SELECT
    T066.T066_Id,
    T066.T066_D009_Id AS D009_Id,
    IFNULL(T066.T066_Quantidade_Estoque, 0) AS T066_Quantidade_Estoque,
    D004.D004_Local
FROM T066
INNER JOIN D004 ON D004.D004_Id = T066.T066_D004_Id
WHERE T066.T066_D009_Id = '{$d009Id}'
  AND D004.D004_Flag_Somar_Estoque_Fisico = 'S'
ORDER BY D004.D004_Local ASC, T066.T066_Id ASC
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $locacoes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $locacoes[] = [
            't066_id' => (int) ($row['T066_Id'] ?? 0),
            'd009_id' => (int) ($row['D009_Id'] ?? 0),
            'local' => (string) ($row['D004_Local'] ?? ''),
            't066_fisico' => (float) ($row['T066_Quantidade_Estoque'] ?? 0),
        ];
    }

    return $locacoes;
}

$search = inecel_codigo_busca((string) ($_GET['codigo'] ?? ''));
$groups = [];
$summary = inecel_movimento_summary($search);
if ($summary) {
    $summary['locacoes'] = inecel_movimento_locacoes((int) $summary['d009_id']);
    $groups[] = $summary;
}
$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$empresaAtualId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$companyTheme = inecel_company_theme($empresaAtualId);
$companyLogoUrl = inecel_company_logo_url($empresaAtualId);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Movimentacao de estoque | Inecel</title>
    <style>
        :root {
            --bg:<?php echo inecel_h($companyTheme['bg']); ?>;
            --panel:<?php echo inecel_h($companyTheme['panel']); ?>;
            --text:#17202a;
            --muted:#667085;
            --line:<?php echo inecel_h($companyTheme['line']); ?>;
            --primary:<?php echo inecel_h($companyTheme['primary']); ?>;
            --accent:<?php echo inecel_h($companyTheme['accent']); ?>;
            --warn:#a05a00;
            --danger:#b42318;
            --overlay:rgba(15,23,42,.52);
        }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:var(--bg); color:var(--text); font-family:Arial, Helvetica, sans-serif; }
        .app { width:min(1180px, 100%); margin:0 auto; padding:14px; }
        .topbar { display:flex; gap:12px; align-items:center; justify-content:space-between; padding:12px 0 16px; }
        .title h1 { margin:0; font-size:22px; line-height:1.15; }
        .title p { margin:4px 0 0; color:var(--muted); font-size:13px; }
        .topbar-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        .quick-nav { display:flex; gap:8px; flex-wrap:wrap; }
        .quick-nav a, .clear-link, .op-btn, .qty-button { display:inline-flex; align-items:center; justify-content:center; text-decoration:none; }
        .quick-nav a, .clear-link { min-height:42px; padding:0 14px; border-radius:7px; background:var(--panel); border:1px solid var(--line); color:var(--primary); font-weight:700; white-space:nowrap; }
        .quick-nav a.is-active { background:var(--primary); color:#fff; }
        .scanner { position:sticky; top:0; z-index:2; margin:0 0 12px; padding:10px; background:var(--panel); border:1px solid var(--line); border-radius:8px; }
        .scanner-form { display:grid; grid-template-columns:1fr auto auto; gap:8px; }
        input, button { min-height:48px; border-radius:7px; font-size:16px; }
        input { width:100%; border:1px solid #b8c2cf; padding:0 12px; background:#fff; }
        button, .clear-link { border:0; padding:0 16px; background:var(--primary); color:#fff; font-weight:700; cursor:pointer; }
        .clear-link { background:#475467; }
        .scanner small { display:block; margin-top:8px; color:var(--muted); font-size:11px; }
        .feedback { margin:0 0 12px; padding:10px 12px; border-radius:7px; font-weight:700; }
        .feedback--warn { color:#fff; background:var(--warn); }
        .feedback--error { color:#fff; background:var(--danger); }
        .feedback--success { color:#fff; background:var(--primary); }
        .result-list { display:grid; gap:10px; }
        .product-card { padding:14px; background:var(--panel); border:1px solid var(--line); border-left:5px solid var(--primary); border-radius:8px; }
        .product-head { display:flex; gap:12px; align-items:flex-start; justify-content:space-between; }
        .product-head h2 { margin:0; font-size:18px; line-height:1.2; }
        .product-head p { margin:5px 0 0; color:var(--muted); font-size:13px; }
        .product-packaging { display:inline-flex; align-items:center; gap:6px; margin-top:8px; padding:6px 10px; border-radius:999px; background:#fff3cd; border:1px solid #f1d48a; color:#8a4b00; font-size:13px; font-weight:700; }
        .summary-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:8px; margin-top:12px; }
        .mini { padding:10px; background:#f8fafc; border:1px solid #e7edf4; border-radius:8px; min-height:72px; display:flex; flex-direction:column; justify-content:center; }
        .mini span { display:block; color:var(--muted); font-size:12px; line-height:1.2; }
        .mini strong { display:block; margin-top:4px; font-size:17px; line-height:1.15; font-weight:700; color:var(--text); }
        .summary-grid .mini strong { color:var(--danger); }
        .locations { display:grid; gap:8px; margin-top:12px; }
        .location-row { padding:10px; border:1px solid var(--line); border-radius:8px; background:#fff; }
        .location-head { display:flex; align-items:center; justify-content:space-between; gap:8px; }
        .location-head strong { font-size:22px; line-height:1.1; }
        .location-head span { display:none; }
        .location-stock .mini span { font-size:12px; }
        .location-stock .mini strong { font-size:24px; line-height:1.05; }
        .location-stock { display:grid; grid-template-columns:minmax(0, 1fr); gap:8px; margin-top:10px; }
        .location-actions { display:grid; grid-template-columns:1.2fr .8fr; gap:8px; margin-top:10px; align-items:end; }
        .operation-group { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:6px; }
        .op-btn { min-height:44px; border:1px solid var(--line); border-radius:8px; background:#fff; color:var(--text); font-weight:700; cursor:pointer; }
        .op-btn.is-active { background:var(--primary); color:#fff; border-color:var(--primary); }
        .qty-button { width:100%; min-height:46px; border:1px solid #b8c2cf; border-radius:8px; background:#fff; font-size:24px; font-weight:700; color:var(--text); cursor:pointer; }
        .processing-chip { display:none !important; }
        .processing-chip[hidden] { display:none !important; }
        .processing-overlay[hidden] { display:none !important; }
        .processing-overlay { position:fixed; inset:0; z-index:90; background:rgba(255,255,255,.94); display:flex; align-items:center; justify-content:center; padding:20px; }
        .processing-overlay__card { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; text-align:center; }
        .processing-overlay__logo-wrap { width:280px; height:200px; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .processing-overlay__logo-wrap img { width:230px; height:auto; max-width:100%; object-fit:contain; object-position:center center; clip-path:none; animation:inecelFade 1.8s ease-in-out infinite; }
        .processing-overlay__fallback { width:150px; height:150px; border-radius:26px; display:flex; align-items:center; justify-content:center; background:var(--primary); color:#fff; font-size:64px; font-weight:700; animation:inecelFade 1.8s ease-in-out infinite; }
        .processing-overlay__title { font-size:13px; font-weight:700; color:var(--muted); letter-spacing:.02em; text-transform:uppercase; }
        .processing-overlay__subtitle { display:none; }
        @keyframes inecelFade { 0% { opacity: 1; } 50% { opacity: .38; } 100% { opacity: 1; } }
        .empty { padding:22px; background:var(--panel); border:1px solid var(--line); border-radius:8px; color:var(--muted); text-align:center; }
        .modal[hidden] { display:none; }
        .modal { position:fixed; inset:0; z-index:20; background:var(--overlay); display:flex; align-items:flex-end; justify-content:center; padding:12px; }
        .keypad { width:min(560px, 100%); background:#fff; border-radius:12px; padding:16px; box-shadow:0 20px 44px rgba(15,23,42,.28); }
        .keypad h3 { margin:0 0 10px; font-size:18px; }
        .keypad-display { min-height:78px; display:flex; align-items:center; justify-content:flex-end; padding:0 16px; border:1px solid #b8c2cf; border-radius:10px; background:#f8fafc; font-size:36px; font-weight:700; }
        .keypad-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; margin-top:12px; }
        .keypad-grid button, .keypad-actions button { min-height:78px; border:0; border-radius:10px; font-size:28px; font-weight:700; cursor:pointer; line-height:1; overflow:hidden; }
        .keypad-grid button { background:#eef2f6; color:#17202a; }
        .keypad-grid button.is-action { background:#fcd3d0; color:#8e1a10; font-size:18px; padding:0 8px; white-space:nowrap; }
        .keypad-actions { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:12px; }
        .keypad-actions .cancel { background:#475467; color:#fff; }
        .keypad-actions .confirm { background:var(--primary); color:#fff; }
        @media (max-width:720px) {
            .app { padding:10px; }
            .topbar { flex-direction:column; align-items:flex-start; }
            .topbar-actions { width:100%; justify-content:stretch; }
            .quick-nav { width:100%; }
            .quick-nav a { flex:1 1 auto; }
            .scanner-form, .location-stock, .location-actions { grid-template-columns:1fr; }
            .summary-grid { grid-template-columns:repeat(5, minmax(0, 1fr)); gap:4px; }
            .mini { min-height:58px; padding:6px 5px; border-radius:6px; }
            .mini span { font-size:9px; line-height:1.1; }
            .mini strong { margin-top:3px; font-size:10px; line-height:1.1; word-break:break-word; }
            .product-head, .location-head { flex-direction:column; align-items:flex-start; }
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
            <h1>Movimentacao de estoque</h1>
            <p><?php echo inecel_h($empresa); ?> - <?php echo inecel_h($usuario); ?></p>
        </div>
        <div class="topbar-actions">
            <nav class="quick-nav" aria-label="Navegacao">
                <a href="./menu.php">Inicio</a>
                <a href="./consulta_estoque.php">Estoque</a>
                <a class="is-active" href="./movimentacao_estoque.php">Movimentacao</a>
            </nav>
        </div>
    </header>

    <section class="scanner">
        <form class="scanner-form" method="get" autocomplete="off">
            <input type="text" name="codigo" value="<?php echo inecel_h($search); ?>" autofocus inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Leia ou digite o codigo do produto">
            <button type="submit">Localizar</button>
                        <a class="clear-link" href="./movimentacao_estoque.php">Limpar</a>
        </form>
        <small>Busque o produto uma vez e movimente direto nas locacoes encontradas.</small>
    </section>

    <div id="feedbackArea">
        <?php if ($search !== '' && !$groups): ?>
            <div class="feedback feedback--warn">Nenhum produto encontrado para movimentacao nas regras do coletor.</div>
        <?php endif; ?>
    </div>

    <section class="result-list" id="resultList">
        <?php if ($groups): ?>
            <?php foreach ($groups as $group): ?>
                <article class="product-card" data-d009-id="<?php echo inecel_h((string) $group['d009_id']); ?>">
                    <div class="product-head">
                        <div>
                            <h2><?php echo inecel_h($group['codigo'] ?: '-'); ?> - <?php echo inecel_h($group['descricao'] ?: 'Produto sem descricao'); ?></h2>
                            <p><?php echo inecel_h($group['marca'] ?: 'Marca nao informada'); ?></p>
                            <?php if ((float) ($group['qte_embalagem'] ?? 0) > 0): ?>
                                <div class="product-packaging">Embalagem: <?php echo inecel_h(inecel_format_number($group['qte_embalagem'], 2)); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="summary-grid">
                        <div class="mini">
                            <span>Total fisico</span>
                            <strong class="js-total-fisico"><?php echo inecel_h(inecel_format_number($group['total_fisico'], 2)); ?></strong>
                        </div>
                        <div class="mini">
                            <span>Reserva</span>
                            <strong class="js-reserva"><?php echo inecel_h(inecel_format_number($group['reserva'], 2)); ?></strong>
                        </div>
                        <div class="mini">
                            <span>Sem Pintura</span>
                            <strong class="js-sem-pintura">...</strong>
                        </div>
                        <div class="mini">
                            <span>Pintado</span>
                            <strong class="js-pintado">...</strong>
                        </div>
                        <div class="mini">
                            <span>Liquido</span>
                            <strong class="js-total-liquido"><?php echo inecel_h(inecel_format_number($group['total_liquido'], 2)); ?></strong>
                        </div>
                    </div>

                    <div class="locations">
                        <?php foreach ($group['locacoes'] as $locacao): ?>
                            <section class="location-row" data-t066-id="<?php echo inecel_h((string) $locacao['t066_id']); ?>" data-d009-id="<?php echo inecel_h((string) $locacao['d009_id']); ?>" data-operacao="E">
                                <div class="location-head">
                                    <strong><?php echo inecel_h($locacao['local'] ?: '-'); ?></strong>
                                </div>
                                <div class="location-stock">
                                    <div class="mini">
                                        <span>Local fisico</span>
                                        <strong class="js-stock"><?php echo inecel_h(inecel_format_number($locacao['t066_fisico'], 2)); ?></strong>
                                    </div>
                                </div>
                                <div class="location-actions">
                                    <div>
                                        <span style="display:block;color:var(--muted);font-size:12px;margin-bottom:6px;">Operacao</span>
                                        <div class="operation-group">
                                            <button type="button" class="op-btn is-active" data-operacao="E">Entrada</button>
                                            <button type="button" class="op-btn" data-operacao="S">Saida</button>
                                        </div>
                                    </div>
                                    <div>
                                        <span style="display:block;color:var(--muted);font-size:12px;margin-bottom:6px;">Quantidade</span>
                                        <button type="button" class="qty-button" data-local="<?php echo inecel_h($locacao['local'] ?: '-'); ?>" data-qte-embalagem="<?php echo inecel_h((string) $group['qte_embalagem']); ?>">0</button>
                                    </div>
                                </div>
                                <div class="processing-chip" hidden>Processando movimentacao...</div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php elseif ($search === ''): ?>
            <div class="empty">Leia ou digite o codigo do produto para consultar as locacoes e movimentar o estoque.</div>
        <?php endif; ?>
    </section>
</div>

<div class="modal" id="keypadModal" hidden>
    <div class="keypad">
        <h3>Informe a quantidade da movimentacao</h3>
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
            <button type="button" class="is-action" data-key="backspace">Apagar</button>
        </div>
        <div class="keypad-actions">
            <button type="button" class="cancel" id="keypadCancel">Cancelar</button>
            <button type="button" class="confirm" id="keypadConfirm">Confirmar</button>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('keypadModal');
    var display = document.getElementById('keypadDisplay');
    var keypadCancel = document.getElementById('keypadCancel');
    var keypadConfirm = document.getElementById('keypadConfirm');
    var feedbackArea = document.getElementById('feedbackArea');
    var processingOverlay = document.getElementById('processingOverlay');
    var currentButton = null;
    var currentPackagingQty = 0;

    function setFeedback(type, message) {
        if (!feedbackArea) {
            return;
        }
        feedbackArea.innerHTML = '<div class="feedback feedback--' + type + '">' + message + '</div>';
    }

    function normalizeValue(value) {
        var cleaned = String(value || '').replace(/\./g, '').replace(',', '.');
        var parsed = Number(cleaned);
        if (!isFinite(parsed) || parsed < 0) {
            return 0;
        }
        return parsed;
    }

    function openKeypad(button) {
        currentButton = button;
        currentPackagingQty = Number(button.dataset.qteEmbalagem || '0');
        display.textContent = button.textContent.trim() || '0';
        modal.hidden = false;
    }

    function closeKeypad() {
        modal.hidden = true;
        currentButton = null;
        currentPackagingQty = 0;
    }

    function setGlobalProcessing(active) {
        if (processingOverlay) {
            processingOverlay.hidden = !active;
        }
        document.body.style.overflow = active ? 'hidden' : '';
    }

    function setProcessing(row, active) {
        var buttons = row.querySelectorAll('button');
        var chip = row.querySelector('.processing-chip');
        buttons.forEach(function (button) {
            button.disabled = active;
        });
        if (chip) {
            chip.hidden = !active;
        }
        setGlobalProcessing(active);
    }

    function updateSummaryAfterMove(row, payload) {
        if (!row || !payload || !payload.summary) {
            return;
        }

        var card = row.closest('.product-card');
        if (!card) {
            return;
        }

        var summary = payload.summary;
        var totalFisico = card.querySelector('.js-total-fisico');
        var reserva = card.querySelector('.js-reserva');
        var semPintura = card.querySelector('.js-sem-pintura');
        var pintado = card.querySelector('.js-pintado');
        var totalLiquido = card.querySelector('.js-total-liquido');

        if (totalFisico && summary.total_fisico_formatado) {
            totalFisico.textContent = summary.total_fisico_formatado;
        }
        if (reserva && summary.reserva_formatado) {
            reserva.textContent = summary.reserva_formatado;
        }
        if (semPintura && summary.quantidade_sem_pintura_formatado) {
            semPintura.textContent = summary.quantidade_sem_pintura_formatado;
        }
        if (pintado && summary.quantidade_pintado_formatado) {
            pintado.textContent = summary.quantidade_pintado_formatado;
        }
        if (totalLiquido && summary.total_liquido_formatado) {
            totalLiquido.textContent = summary.total_liquido_formatado;
        }
    }

    function updateRowAfterMove(row, payload) {
        if (!row || !payload || !payload.row) {
            return;
        }

        var stockNode = row.querySelector('.js-stock');
        if (stockNode && payload.row.T066_Quantidade_Estoque_formatado) {
            stockNode.textContent = payload.row.T066_Quantidade_Estoque_formatado;
        }

        var qtyButton = row.querySelector('.qty-button');
        if (qtyButton) {
            qtyButton.textContent = '0';
        }
    }

    document.querySelectorAll('.op-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var row = button.closest('.location-row');
            if (!row) {
                return;
            }
            row.dataset.operacao = button.dataset.operacao || 'E';
            row.querySelectorAll('.op-btn').forEach(function (item) {
                item.classList.toggle('is-active', item === button);
            });
        });
    });

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
    bindCollectorInput(document.querySelector('input[name="codigo"][data-unlock-dblclick="true"]'));

    document.querySelectorAll('.product-card[data-d009-id]').forEach(function (card) {
        var d009Id = card.getAttribute('data-d009-id');
        if (!d009Id || d009Id === '0') {
            return;
        }

        fetch('./produto_totais_ajax.php?d009_id=' + encodeURIComponent(d009Id))
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data || !data.ok || !data.totais) {
                    return;
                }

                var semPintura = card.querySelector('.js-sem-pintura');
                var pintado = card.querySelector('.js-pintado');

                if (semPintura) {
                    semPintura.textContent = data.totais.quantidade_sem_pintura_formatado || '0,00';
                }
                if (pintado) {
                    pintado.textContent = data.totais.quantidade_pintado_formatado || '0,00';
                }
            })
            .catch(function () {
                var semPintura = card.querySelector('.js-sem-pintura');
                var pintado = card.querySelector('.js-pintado');
                if (semPintura) { semPintura.textContent = '0,00'; }
                if (pintado) { pintado.textContent = '0,00'; }
            });
    });

    document.querySelectorAll('.qty-button').forEach(function (button) {
        button.addEventListener('click', function () {
            openKeypad(button);
        });
    });

    document.querySelectorAll('#keypadModal [data-key]').forEach(function (button) {
        button.addEventListener('click', function () {
            var key = button.getAttribute('data-key');
            var value = display.textContent.trim();

            if (key === 'backspace') {
                value = value.slice(0, -1);
                display.textContent = value === '' ? '0' : value;
                return;
            }

            if (key === ',') {
                if (currentPackagingQty > 0) {
                    return;
                }
                if (value.indexOf(',') === -1) {
                    display.textContent = value + ',';
                }
                return;
            }

            if (value === '0') {
                display.textContent = key;
                return;
            }

            display.textContent = value + key;
        });
    });

    document.addEventListener('keydown', function (event) {
        if (modal.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            closeKeypad();
            return;
        }

        if (event.key === 'Enter') {
            keypadConfirm.click();
            event.preventDefault();
            return;
        }

        if (event.key === 'Backspace') {
            var value = display.textContent.trim();
            value = value.slice(0, -1);
            display.textContent = value === '' ? '0' : value;
            event.preventDefault();
            return;
        }

        if (/^[0-9]$/.test(event.key)) {
            if (display.textContent.trim() === '0') {
                display.textContent = event.key;
            } else {
                display.textContent += event.key;
            }
            event.preventDefault();
            return;
        }

        if (currentPackagingQty <= 0 && (event.key === ',' || event.key === '.') && display.textContent.indexOf(',') === -1) {
            display.textContent += ',';
            event.preventDefault();
        }
    });

    keypadCancel.addEventListener('click', closeKeypad);

    keypadConfirm.addEventListener('click', function () {
        if (!currentButton) {
            closeKeypad();
            return;
        }

        var row = currentButton.closest('.location-row');
        if (!row) {
            closeKeypad();
            return;
        }

        var valueText = display.textContent.trim() || '0';
        currentButton.textContent = valueText;
        closeKeypad();

        var quantidade = normalizeValue(valueText);
        if (quantidade <= 0) {
            setFeedback('error', 'Informe uma quantidade maior que zero.');
            currentButton.textContent = '0';
            return;
        }

        if (currentPackagingQty > 0 && Math.floor(quantidade) !== quantidade) {
            setFeedback('error', 'Para este produto informe somente quantidade inteira de embalagens.');
            currentButton.textContent = '0';
            return;
        }

        setProcessing(row, true);
        setFeedback('success', 'Processando movimentacao...');

        var data = new FormData();
        data.append('t066_id', row.dataset.t066Id || '0');
        data.append('d009_id', row.dataset.d009Id || '0');
        data.append('operacao', row.dataset.operacao || 'E');
        data.append('quantidade', valueText);

        fetch('./movimentacao_estoque_ajax.php', {
            method: 'POST',
            body: data,
            credentials: 'same-origin'
        })
        .then(function (response) {
            return response.text().then(function (text) {
                var payload = null;
                try {
                    payload = JSON.parse(text);
                } catch (error) {
                    throw new Error('Resposta invalida do servidor.');
                }
                return payload;
            });
        })
        .then(function (payload) {
            if (!payload || !payload.ok) {
                throw new Error(payload && payload.message ? payload.message : 'Falha ao movimentar o estoque.');
            }
            updateRowAfterMove(row, payload);
            updateSummaryAfterMove(row, payload);
            setFeedback('success', payload.message || 'Movimentacao registrada com sucesso.');
            setProcessing(row, false);
            var qtyButton = row.querySelector('.qty-button');
            if (qtyButton) {
                qtyButton.focus();
            }
        })
        .catch(function (error) {
            setFeedback('error', error.message || 'Falha de comunicacao com o servidor.');
            setProcessing(row, false);
        });
    });
})();
</script>
</body>
</html>
