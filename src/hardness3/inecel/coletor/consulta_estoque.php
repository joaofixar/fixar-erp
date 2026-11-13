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

function inecel_format_number($value, int $decimals = 2): string
{
    return number_format((float) $value, $decimals, ',', '.');
}

function inecel_codigo_busca(string $value): string
{
    return trim($value);
}

function inecel_estoque_produtos(string $busca): array
{
    if ($busca === '') {
        return [];
    }

    $conn = inecel_db();
    $empresaId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    $buscaSql = mysqli_real_escape_string($conn, $busca);
    $termos = preg_split('/\s+/', $busca, -1, PREG_SPLIT_NO_EMPTY);
    $descricaoFiltros = [];

    foreach ($termos as $termo) {
        $termoSql = mysqli_real_escape_string($conn, $termo);
        $descricaoFiltros[] = "D001.D001_Descricao_Produto LIKE '%{$termoSql}%'";
    }

    $descricaoWhere = implode("\n        AND ", $descricaoFiltros);

    $sql = <<<SQL
SELECT
    D001.D001_Id,
    D001.D001_Codigo_Produto,
    D001.D001_Descricao_Produto,
    D082.D082_Marca,
    D009.D009_Id,
    IFNULL(D009.D009_Quantidade_Estoque, 0) AS Estoque_Total,
    IFNULL(D009.D009_Quantidade_Estoque_Liquido, 0) AS Estoque_Liquido,
    GREATEST(IFNULL(D009.D009_Quantidade_Estoque, 0) - IFNULL(D009.D009_Quantidade_Estoque_Liquido, 0), 0) AS Reserva,
    0 AS Quantidade_Pintado,
    0 AS Quantidade_Sem_Pintura
FROM D001
INNER JOIN D049 ON D049.D049_D001_Id = D001.D001_Id
INNER JOIN D009 ON D009.D009_D049_Id = D049.D049_Id
LEFT JOIN D082 ON D082.D082_Id = D049.D049_D082_Id
WHERE (
    D001.D001_Codigo_Produto = '{$buscaSql}'
    OR (
        {$descricaoWhere}
    )
)
  AND D009.D009_C004_Id = '{$empresaId}'
  AND D001.D001_Flag_Ativo = 'S'
ORDER BY
    CASE WHEN D001.D001_Codigo_Produto = '{$buscaSql}' THEN 0 ELSE 1 END,
    D001.D001_Codigo_Produto ASC
LIMIT 50
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $produtos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $produtos[] = $row;
    }

    return $produtos;
}

function inecel_estoque_locacoes(int $d009Id): array
{
    if ($d009Id <= 0) {
        return [];
    }

    $conn = inecel_db();

    $sql = <<<SQL
SELECT
    D004.D004_Local,
    IFNULL(SUM(IFNULL(T066.T066_Quantidade_Estoque, 0)), 0) AS Estoque_Local
FROM T066
INNER JOIN D004 ON D004.D004_Id = T066.T066_D004_Id
WHERE T066.T066_D009_Id = '{$d009Id}'
  AND D004.D004_Flag_Somar_Estoque_Fisico = 'S'
GROUP BY D004.D004_Local
ORDER BY D004.D004_Local ASC
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $locacoes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $locacoes[] = [
            'D004_Local' => (string) ($row['D004_Local'] ?? ''),
            'Estoque_Local' => (float) ($row['Estoque_Local'] ?? 0),
        ];
    }

    return $locacoes;
}

$codigo = inecel_codigo_busca((string) ($_GET['codigo'] ?? ''));
$produtos = inecel_estoque_produtos($codigo);
$locacoesPorProduto = [];
foreach ($produtos as $produto) {
    $locacoesPorProduto[(int) ($produto['D001_Id'] ?? 0)] = inecel_estoque_locacoes((int) ($produto['D009_Id'] ?? 0));
}
$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$empresaAtualId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$companyTheme = inecel_company_theme($empresaAtualId);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Consulta de estoque | Inecel</title>
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
        }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:var(--bg); color:var(--text); font-family:Arial, Helvetica, sans-serif; }
        .app { width:min(1080px, 100%); margin:0 auto; padding:14px; }
        .topbar { display:flex; gap:12px; align-items:center; justify-content:space-between; padding:12px 0 16px; }
        .title h1 { margin:0; font-size:22px; line-height:1.15; }
        .title p { margin:4px 0 0; color:var(--muted); font-size:13px; }
        .topbar-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        .quick-nav { display:flex; gap:8px; flex-wrap:wrap; }
        .quick-nav a, .clear-link { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:0 14px; border-radius:7px; background:var(--panel); border:1px solid var(--line); color:var(--primary); font-weight:700; text-decoration:none; white-space:nowrap; }
        .quick-nav a.is-active { background:var(--primary); color:#fff; }
        .quick-nav .is-disabled { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:0 14px; border-radius:7px; background:var(--panel); border:1px solid var(--line); color:#98a2b3; font-weight:700; white-space:nowrap; font-size:16px; pointer-events:none; cursor:not-allowed; opacity:.7; }
        .scanner { position:sticky; top:0; z-index:2; margin:0 0 12px; padding:10px; background:var(--panel); border:1px solid var(--line); border-radius:8px; }
        .scanner-form { display:grid; grid-template-columns:1fr auto auto; gap:8px; }
        input, button { min-height:48px; border-radius:7px; font-size:16px; }
        input { width:100%; border:1px solid #b8c2cf; padding:0 12px; background:#fff; }
        button, .clear-link { display:inline-flex; align-items:center; justify-content:center; border:0; padding:0 16px; background:var(--primary); color:#fff; font-weight:700; text-decoration:none; cursor:pointer; }
        .clear-link { background:#475467; }
        .scanner small { display:block; margin-top:8px; color:var(--muted); font-size:11px; }
        .feedback { margin:0 0 12px; padding:10px 12px; border-radius:7px; font-weight:700; }
        .feedback--warn { color:#fff; background:var(--warn); }
        .feedback--error { color:#fff; background:var(--danger); }
        .result-list { display:grid; gap:10px; }
        .produto { padding:14px; background:var(--panel); border:1px solid var(--line); border-left:6px solid var(--primary); border-radius:8px; }
        .produto h2 { margin:0; font-size:24px; line-height:1.15; }
        .produto p { margin:5px 0 0; color:var(--muted); font-size:13px; }
        .summary-grid { display:grid; grid-template-columns:repeat(5, minmax(0, 1fr)); gap:8px; margin-top:12px; }
        .mini { padding:10px; background:#f8fafc; border:1px solid #e7edf4; border-radius:8px; min-height:72px; }
        .mini span { display:block; color:var(--muted); font-size:11px; line-height:1.2; }
        .mini strong { display:block; margin-top:4px; font-size:17px; line-height:1.15; color:var(--danger); }
        .locacoes { margin-top:12px; }
        .locacoes strong { display:block; margin-bottom:8px; font-size:13px; }
        .locacoes-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:8px; }
        .locacao { padding:10px; border:1px solid var(--line); border-radius:8px; background:#fff; }
        .locacao span { display:block; color:var(--muted); font-size:12px; }
        .locacao b { display:block; margin-top:4px; font-size:18px; }
        .empty { padding:22px; background:var(--panel); border:1px solid var(--line); border-radius:8px; color:var(--muted); text-align:center; }
        @media (max-width:720px) {
            .app { padding:10px; }
            .topbar { flex-direction:column; align-items:flex-start; }
            .topbar-actions { width:100%; justify-content:stretch; }
            .quick-nav { width:100%; }
            .quick-nav a { flex:1 1 auto; }
            .scanner-form { grid-template-columns:1fr; }
            .summary-grid { grid-template-columns:repeat(5, minmax(0, 1fr)); gap:4px; }
            .mini { min-height:58px; padding:6px 5px; border-radius:6px; }
            .mini span { font-size:9px; line-height:1.1; }
            .mini strong { margin-top:3px; font-size:10px; line-height:1.1; word-break:break-word; }
            .produto h2 { font-size:20px; }
        }
    </style>
</head>
<body>
<div class="app">
    <header class="topbar">
        <div class="title">
            <h1>Consulta de estoque</h1>
            <p><?php echo inecel_h($empresa); ?> - <?php echo inecel_h($usuario); ?></p>
        </div>
        <div class="topbar-actions">
            <nav class="quick-nav" aria-label="Navegacao">
                <a href="./menu.php">Inicio</a>
                <a href="./pedidos.php">Conferencia</a>
                <span class="is-disabled" aria-disabled="true">Separacao</span>
                <a class="is-active" href="./consulta_estoque.php">Estoque</a>
            </nav>
        </div>
    </header>

    <section class="scanner">
        <form class="scanner-form" method="get" autocomplete="off">
            <input type="text" name="codigo" value="<?php echo inecel_h($codigo); ?>" autofocus inputmode="none" virtualkeyboardpolicy="manual" placeholder="Leia ou digite o codigo ou descricao do produto">
            <button type="submit">Localizar</button>
                        <a class="clear-link" href="./consulta_estoque.php">Limpar</a>
        </form>
        <small>Use leitor de codigo de barras, QR Code ou digite o codigo ou a descricao do produto.</small>
    </section>

    <?php if ($codigo !== '' && !$produtos): ?>
        <div class="feedback feedback--warn">Nenhum estoque encontrado para este produto nas regras do coletor.</div>
    <?php endif; ?>

    <section class="result-list">
        <?php if ($produtos): ?>
            <?php foreach ($produtos as $produto): ?>
                <?php $produtoId = (int) ($produto['D001_Id'] ?? 0); ?>
                <?php $locacoes = $locacoesPorProduto[$produtoId] ?? []; ?>
                <article class="produto" data-d009-id="<?php echo inecel_h((string) ($produto['D009_Id'] ?? 0)); ?>">
                    <h2><?php echo inecel_h($produto['D001_Codigo_Produto'] ?: '-'); ?> - <?php echo inecel_h($produto['D001_Descricao_Produto'] ?: 'Produto sem descricao'); ?></h2>
                    <p><?php echo inecel_h($produto['D082_Marca'] ?: 'Marca nao informada'); ?></p>
                    <div class="summary-grid">
                        <div class="mini">
                            <span>Total fisico</span>
                            <strong><?php echo inecel_h(inecel_format_number($produto['Estoque_Total'] ?? 0)); ?></strong>
                        </div>
                        <div class="mini">
                            <span>Reserva</span>
                            <strong><?php echo inecel_h(inecel_format_number($produto['Reserva'] ?? 0)); ?></strong>
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
                            <strong><?php echo inecel_h(inecel_format_number($produto['Estoque_Liquido'] ?? 0)); ?></strong>
                        </div>
                    </div>
                    <div class="locacoes">
                        <strong>Locacoes</strong>
                        <?php if ($locacoes): ?>
                            <div class="locacoes-grid">
                                <?php foreach ($locacoes as $locacao): ?>
                                    <div class="locacao">
                                        <span><?php echo inecel_h($locacao['D004_Local'] ?: '-'); ?></span>
                                        <b><?php echo inecel_h(inecel_format_number($locacao['Estoque_Local'] ?? 0)); ?></b>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty">Nenhuma locacao disponivel para este produto.</div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php elseif ($codigo === ''): ?>
            <div class="empty">Leia ou digite o codigo do produto para consultar o estoque e as locacoes.</div>
        <?php endif; ?>
    </section>
</div>
<script>
(function () {
    var input = document.querySelector('input[name="codigo"]');
    var form = input ? input.closest('form') : null;
    var manualMode = false;

    if (!input || !form) {
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

    setTimeout(function () {
        focusInput();
    }, 120);

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
})();
</script>
</body>
</html>