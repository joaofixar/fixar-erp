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

function inecel_format_datetime($value): string
{
    if (empty($value) || $value === '0000-00-00 00:00:00') {
        return '';
    }

    $time = strtotime((string) $value);
    return $time ? date('d/m/Y H:i:s', $time) : '';
}

function inecel_pedido_atual(int $pedidoId): ?array
{
    $conn = inecel_db();
    $empresaId = (int) $_SESSION['Empresa_Atual'];

    $sql = <<<SQL
SELECT
    T005.T005_Id,
    T005.T005_Flag_Status,
    T005.T005_Nome_Status,
    D024.D024_Nome_Empresa
FROM T005
LEFT JOIN D024 ON D024.D024_Id = T005.T005_D024_Id
WHERE T005.T005_Id = '{$pedidoId}'
  AND T005.T005_C004_Id = '{$empresaId}'
  AND T005.T005_Flag_Status IN (3, 4, 5)
LIMIT 1
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    return $row ?: null;
}

function inecel_volume_rows(int $pedidoId): array
{
    $conn = inecel_db();
    $empresaId = (int) $_SESSION['Empresa_Atual'];

    $sql = <<<SQL
SELECT
    T314.T314_Id,
    T314.T314_T005_Id,
    T314.T314_Volume,
    T314.T314_Identificador,
    T314.T314_Sequencia,
    T314.T314_Peso_Bruto,
    T314.T314_Peso_Liquido,
    D200.D200_Descricao,
    IFNULL(T314A.T314A_Flag_Coletado, 'N') AS T314A_Flag_Coletado,
    T314A.T314A_C007_Id_Coletado,
    T314A.T314A_Data_Hora_Coletado,
    C007.C007_Nome AS C007_Nome_Coletado,
    D024.D024_Nome_Empresa
FROM T314
LEFT JOIN T314A ON T314A.T314A_T314_Id = T314.T314_Id
LEFT JOIN T005 ON T005.T005_Id = T314.T314_T005_Id
LEFT JOIN D024 ON D024.D024_Id = T005.T005_D024_Id
LEFT JOIN D200 ON D200.D200_Id = T314.T314_D200_Id
LEFT JOIN C007 ON C007.C007_Id = T314A.T314A_C007_Id_Coletado
WHERE T005.T005_C004_Id = '{$empresaId}'
  AND T314.T314_T005_Id = '{$pedidoId}'
ORDER BY IFNULL(T314A.T314A_Flag_Coletado, 'N') ASC, T314.T314_Sequencia ASC, T314.T314_Id DESC
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

function inecel_volume_items_map(int $pedidoId): array
{
    $conn = inecel_db();
    $empresaId = (int) $_SESSION['Empresa_Atual'];

    $sql = <<<SQL
SELECT
    T314.T314_Id AS T314_Id,
    D001.D001_Codigo_Produto,
    D001.D001_Descricao_Produto,
    T315.T315_Quantidade
FROM T315
LEFT JOIN T314 ON T314.T314_Id = T315.T315_T314_Id
LEFT JOIN T005 ON T005.T005_Id = T314.T314_T005_Id
LEFT JOIN D009 ON D009.D009_Id = T315.T315_D009_Id
LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
WHERE T314.T314_T005_Id = '{$pedidoId}'
  AND T005.T005_C004_Id = '{$empresaId}'
ORDER BY T314.T314_Id ASC, D001.D001_Codigo_Produto ASC, T315.T315_Id ASC
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $itemsByVolume = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $volumeId = (int) ($row['T314_Id'] ?? 0);
        unset($row['T314_Id']);
        if (!isset($itemsByVolume[$volumeId])) {
            $itemsByVolume[$volumeId] = [];
        }
        $itemsByVolume[$volumeId][] = $row;
    }

    return $itemsByVolume;
}

$pedidoId = (int) ($_GET['pedido_id'] ?? 0);
$pedido = $pedidoId > 0 ? inecel_pedido_atual($pedidoId) : null;
if (!$pedido) {
    header('Location: ./pedidos.php');
    exit;
}

$volumes = inecel_volume_rows($pedidoId);
$volumeItemsMap = inecel_volume_items_map($pedidoId);
$total = count($volumes);
$coletados = 0;

foreach ($volumes as $volume) {
    if (($volume['T314A_Flag_Coletado'] ?? 'N') === 'S') {
        $coletados++;
    }
}

$pendentes = $total - $coletados;
$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$somenteLeitura = (int) $pedido['T005_Flag_Status'] === 5;
$statusFilter = ($_GET['status'] ?? '') === 'coletados' ? 'coletados' : 'pendentes';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Conferencia de volumes | Inecel</title>
    <style>
        :root { --bg:#eef2f6; --panel:#fff; --text:#17202a; --muted:#667085; --line:#d7dde5; --primary:#155eef; --ok:#168a4a; --warn:#a05a00; --danger:#b42318; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:var(--bg); color:var(--text); font-family:Arial, Helvetica, sans-serif; }
        .app { width:min(1180px, 100%); margin:0 auto; padding:14px; }
        .topbar { display:flex; gap:12px; align-items:center; justify-content:space-between; padding:12px 0 16px; }
        .title h1 { margin:0; font-size:22px; line-height:1.15; }
        .title p { margin:4px 0 0; color:var(--muted); font-size:13px; }
        .topbar-actions { display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }
        .action-button { display:inline-flex; align-items:center; justify-content:center; min-height:38px; padding:0 12px; border-radius:7px; background:var(--primary); color:#fff; font-weight:700; text-decoration:none; white-space:nowrap; }
        .action-button--secondary { background:#475467; }
        .scanner { position:sticky; top:0; z-index:2; background:var(--bg); padding-bottom:12px; }
        .scan-card, .metric, .volume, .empty { background:var(--panel); border:1px solid var(--line); border-radius:8px; }
        .scan-card { padding:12px; box-shadow:0 8px 20px rgba(16,24,40,.08); }
        .scan-form { display:grid; grid-template-columns:1fr auto; gap:8px; }
        input, button { min-height:46px; border-radius:7px; font-size:16px; }
        input { width:100%; border:1px solid #b8c2cf; padding:0 12px; background:#fff; }
        button { border:0; padding:0 16px; background:var(--primary); color:#fff; font-weight:700; cursor:pointer; }
        button:disabled { opacity:.6; cursor:wait; }
        .feedback { display:none; margin-top:10px; padding:10px; border-radius:7px; font-weight:700; }
        .feedback--ok { display:block; color:#fff; background:var(--ok); }
        .feedback--error { display:block; color:#fff; background:var(--danger); }
        .feedback--warn { display:block; color:#fff; background:var(--warn); }
        .volume-filter { margin-bottom:12px; }
        .volume-filter input { width:100%; border:1px solid #b8c2cf; padding:0 12px; background:#fff; }
        .metrics { display:grid; grid-template-columns:repeat(3, 1fr); gap:8px; margin:12px 0; }
        .metric { padding:10px; }
        .metric span { display:block; color:var(--muted); font-size:12px; text-transform:uppercase; }
        .metric strong { display:block; margin-top:3px; font-size:22px; }
        .volume-list { display:grid; gap:9px; }
        .volume { display:grid; grid-template-columns:1fr auto; gap:10px; align-items:center; border-left:6px solid var(--warn); padding:12px; cursor:pointer; }
        .volume[hidden] { display:none; }
        .volume:focus { outline:3px solid rgba(21,94,239,.25); outline-offset:2px; }
        .volume[data-coletado="S"] { border-left-color:var(--ok); background:#f6fef9; }
        .volume h2 { margin:0; font-size:18px; line-height:1.2; }
        .volume p { margin:5px 0 0; color:var(--muted); font-size:13px; }
        .coleta-info { margin-top:8px; padding:8px 10px; border-radius:7px; background:#e9f8ef; color:#125c31; font-size:12px; font-weight:700; }
        .volume-items { display:none; grid-column:1 / -1; margin-top:4px; border-top:1px solid var(--line); padding-top:10px; cursor:default; }
        .volume--open .volume-items { display:block; }
        .volume-item { display:grid; grid-template-columns:90px 1fr 92px; gap:8px; align-items:start; padding:8px 0; border-bottom:1px solid #eef2f6; }
        .volume-item:last-child { border-bottom:0; }
        .volume-item strong { font-size:14px; }
        .volume-item span { color:var(--muted); font-size:13px; }
        .volume-item__qty { text-align:right; font-weight:700; color:var(--text) !important; }
        .badge { display:inline-flex; align-items:center; justify-content:center; min-width:92px; min-height:32px; border-radius:999px; font-weight:700; font-size:12px; color:#fff; background:var(--warn); }
        .volume[data-coletado="S"] .badge { background:var(--ok); }
        .empty { padding:22px; color:var(--muted); text-align:center; }
        @media (max-width:640px) {
            .app { padding:10px; }
            .topbar { align-items:flex-start; }
            .title h1 { font-size:19px; }
            .scan-form { grid-template-columns:1fr; }
            .metrics { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .metric { padding:8px; }
            .metric strong { font-size:19px; }
            .volume { grid-template-columns:1fr; }
            .volume-item { grid-template-columns:1fr; gap:3px; }
            .volume-item__qty { text-align:left; }
            .badge { width:100%; }
        }
    </style>
</head>
<body>
<div class="app">
    <header class="topbar">
        <div class="title">
            <h1>Conferencia de volumes</h1>
            <p>Pedido <?php echo inecel_h($pedido['T005_Id']); ?> - <?php echo inecel_h($pedido['D024_Nome_Empresa'] ?: 'Pessoa nao informada'); ?></p>
            <p><?php echo inecel_h($empresa); ?> - <?php echo inecel_h($usuario); ?></p>
        </div>
        <div>
            <div class="topbar-actions">
                <a class="action-button action-button--secondary" href="./pedidos.php?status=<?php echo inecel_h($statusFilter); ?>">Pedidos</a>
            </div>
        </div>
    </header>

    <section class="scanner">
        <?php if ($somenteLeitura): ?>
            <div class="scan-card">
                <div class="feedback feedback--ok" style="display:block;">Pedido ja coletado (status 5). Somente leitura, nao e possivel ler novos volumes.</div>
            </div>
        <?php else: ?>
            <div class="scan-card">
                <form class="scan-form" id="scanForm" method="post" autocomplete="off">
                    <input type="hidden" id="pedidoId" name="pedido_id" value="<?php echo inecel_h($pedidoId); ?>">
                    <input id="codigo" name="codigo" type="text" inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" autofocus placeholder="Leia ou digite o codigo do volume">
                    <button id="scanButton" type="submit">Confirmar</button>
                </form>
                <div id="feedback" class="feedback"></div>
            </div>
        <?php endif; ?>
    </section>

    <section class="metrics" aria-label="Resumo">
        <div class="metric"><span>Total</span><strong id="totalCount"><?php echo inecel_h((string) $total); ?></strong></div>
        <div class="metric"><span>Coletados</span><strong id="coletadoCount"><?php echo inecel_h((string) $coletados); ?></strong></div>
        <div class="metric"><span>Pendentes</span><strong id="pendenteCount"><?php echo inecel_h((string) $pendentes); ?></strong></div>
    </section>

    <section class="volume-filter">
        <input id="volumeFilterInput" type="text" placeholder="Filtrar por numero do volume">
    </section>

    <section class="volume-list" id="volumeList">
        <?php foreach ($volumes as $volume): ?>
            <?php $coletado = ($volume['T314A_Flag_Coletado'] ?? 'N') === 'S' ? 'S' : 'N'; ?>
            <?php $itensVolume = $volumeItemsMap[(int) $volume['T314_Id']] ?? []; ?>
            <?php $dataColetado = inecel_format_datetime($volume['T314A_Data_Hora_Coletado'] ?? ''); ?>
            <article class="volume" id="volume-<?php echo inecel_h($volume['T314_Id']); ?>" data-id="<?php echo inecel_h($volume['T314_Id']); ?>" data-coletado="<?php echo inecel_h($coletado); ?>" data-volume="<?php echo inecel_h($volume['T314_Volume']); ?>" tabindex="0" role="button" aria-expanded="false">
                <div>
                    <h2>
                        Volume <?php echo inecel_h($volume['T314_Volume']); ?>
                        <?php if (!empty($volume['D200_Descricao'])): ?>
                            - <?php echo inecel_h($volume['D200_Descricao']); ?>
                        <?php endif; ?>
                    </h2>
                    <p>Pedido <?php echo inecel_h($volume['T314_T005_Id']); ?> - <?php echo inecel_h($volume['D024_Nome_Empresa'] ?: 'Pessoa nao informada'); ?></p>
                    <p>
                        ID <?php echo inecel_h($volume['T314_Id']); ?>
                        <?php if (!empty($volume['T314_Identificador'])): ?>
                            - Identificador <?php echo inecel_h($volume['T314_Identificador']); ?>
                        <?php endif; ?>
                        - Seq. <?php echo inecel_h($volume['T314_Sequencia']); ?>
                    </p>
                    <div class="coleta-info" id="coleta-info-<?php echo inecel_h($volume['T314_Id']); ?>" <?php echo $coletado === 'S' ? '' : 'hidden'; ?>>
                        Coletado por
                        <span class="coleta-info__user"><?php echo inecel_h($volume['C007_Nome_Coletado'] ?: 'Usuario nao informado'); ?></span>
                        em
                        <span class="coleta-info__datetime"><?php echo inecel_h($dataColetado ?: '-'); ?></span>
                    </div>
                </div>
                <span class="badge"><?php echo $coletado === 'S' ? 'COLETADO' : 'PENDENTE'; ?></span>
                <div class="volume-items">
                    <?php foreach ($itensVolume as $item): ?>
                        <div class="volume-item">
                            <strong><?php echo inecel_h($item['D001_Codigo_Produto'] ?: '-'); ?></strong>
                            <span><?php echo inecel_h($item['D001_Descricao_Produto'] ?: 'Produto sem descricao'); ?></span>
                            <span class="volume-item__qty"><?php echo inecel_h(number_format((float) $item['T315_Quantidade'], 4, ',', '.')); ?></span>
                        </div>
                    <?php endforeach; ?>

                    <?php if (!$itensVolume): ?>
                        <div class="empty">Nenhum item encontrado neste volume.</div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if (!$volumes): ?>
            <div class="empty">Nenhum volume encontrado para este pedido.</div>
        <?php endif; ?>
    </section>

    <div class="empty" id="volumeFilterEmpty" hidden>Nenhum volume encontrado para o numero informado.</div>
</div>

<script>
(function () {
    var form = document.getElementById('scanForm');

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

    var volumeFilterInput = document.getElementById('volumeFilterInput');
    var volumeFilterEmpty = document.getElementById('volumeFilterEmpty');

    if (volumeFilterInput) {
        volumeFilterInput.addEventListener('input', function () {
            var termo = volumeFilterInput.value.trim().toLowerCase();
            var cards = document.querySelectorAll('.volume');
            var visiveis = 0;

            cards.forEach(function (card) {
                var numeroVolume = (card.dataset.volume || '').toLowerCase();
                var corresponde = termo === '' || numeroVolume.indexOf(termo) !== -1;
                card.hidden = !corresponde;
                if (corresponde) {
                    visiveis++;
                }
            });

            if (volumeFilterEmpty) {
                volumeFilterEmpty.hidden = visiveis !== 0;
            }
        });
    }

    document.querySelectorAll('.volume').forEach(function (card) {
        card.addEventListener('click', function (event) {
            if (event.target.closest('.volume-items')) {
                return;
            }

            var isOpen = card.classList.toggle('volume--open');
            card.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        card.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            var isOpen = card.classList.toggle('volume--open');
            card.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    if (form) {
        var pedidoId = document.getElementById('pedidoId').value;
        var input = document.getElementById('codigo');
        var button = document.getElementById('scanButton');
        var feedback = document.getElementById('feedback');
        var processing = false;

        bindCollectorInput(input);

        function setFeedback(type, message) {
            feedback.className = 'feedback feedback--' + type;
            feedback.textContent = message;
        }

        function updateCounters() {
            var cards = document.querySelectorAll('.volume');
            var total = cards.length;
            var coletados = document.querySelectorAll('.volume[data-coletado="S"]').length;
            document.getElementById('totalCount').textContent = String(total);
            document.getElementById('coletadoCount').textContent = String(coletados);
            document.getElementById('pendenteCount').textContent = String(total - coletados);
        }

        function processScan() {
            var codigo = input.value.trim();
            if (!codigo) {
                input.focus();
                return;
            }

            if (processing) {
                return;
            }

            processing = true;
            button.disabled = true;
            input.disabled = true;
            var body = new URLSearchParams();
            body.set('pedido_id', pedidoId);
            body.set('codigo', codigo);

            fetch('./volumes_ajax.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
                body: body.toString()
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.ok) {
                        setFeedback('error', data.message || 'Nao foi possivel coletar o volume.');
                        return;
                    }

                    var card = document.getElementById('volume-' + data.volume.T314_Id);
                    if (card) {
                        card.dataset.coletado = 'S';
                        var badge = card.querySelector('.badge');
                        if (badge) {
                            badge.textContent = 'COLETADO';
                        }
                        var coletaInfo = document.getElementById('coleta-info-' + data.volume.T314_Id);
                        if (coletaInfo) {
                            var userNode = coletaInfo.querySelector('.coleta-info__user');
                            var dateNode = coletaInfo.querySelector('.coleta-info__datetime');
                            if (userNode) {
                                userNode.textContent = data.volume.C007_Nome_Coletado || 'Usuario nao informado';
                            }
                            if (dateNode) {
                                dateNode.textContent = data.volume.T314A_Data_Hora_Coletado_Formatada || '-';
                            }
                            coletaInfo.hidden = false;
                        }
                        card.scrollIntoView({behavior: 'smooth', block: 'center'});
                    }

                    setFeedback(data.already_collected ? 'warn' : 'ok', data.message);
                    updateCounters();
                })
                .catch(function () {
                    setFeedback('error', 'Falha de comunicacao com o servidor.');
                })
                .finally(function () {
                    processing = false;
                    button.disabled = false;
                    input.disabled = false;
                    input.value = '';
                    input.focus();
                });
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            processScan();
        });

        input.focus();
    }
})();
</script>
</body>
</html>
