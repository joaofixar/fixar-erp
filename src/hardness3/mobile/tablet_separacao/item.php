<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/layout.php';

tablet_separacao_require_auth();

$orderId = (int) ($_GET['pedido'] ?? 0);
$itemId = (int) ($_GET['item'] ?? 0);
$labelSql = (string) ($_GET['sql'] ?? '');
$autoLabelPrint = (string) ($_GET['auto_label_print'] ?? '') === '1';
$order = tablet_separacao_find_order($orderId);
$item = tablet_separacao_find_item($orderId, $itemId);

if (!$order || !$item) {
    tablet_separacao_set_flash('error', 'Item não encontrado.');
    header('Location: ' . app_base_path('pedidos.php'));
    exit;
}

$resolvedLocation = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'separate');

    if ($action === 'delete_separation') {
        $separationKey = trim((string) ($_POST['separation_key'] ?? ''));
        $deleteResult = tablet_separacao_delete_item_separation($orderId, $itemId, $separationKey);
        tablet_separacao_set_flash($deleteResult['ok'] ? 'success' : 'error', $deleteResult['message']);
        header('Location: ' . app_base_path('item.php?pedido=' . $orderId . '&item=' . $itemId));
        exit;
    }

    if ($action === 'separate') {
        $barcode = trim((string) ($_POST['location_barcode'] ?? ''));
        $quantity = (float) str_replace(',', '.', (string) ($_POST['quantity'] ?? '0'));
        $remaining = (float) $item['required_qty'] - (float) $item['separated_qty'];

        $resolvedLocation = tablet_separacao_resolve_location($item, $barcode);

        if ($resolvedLocation === null) {
            $errors[] = 'A locação bipada não corresponde a este item.';
        }

        if ($quantity <= 0) {
            $errors[] = 'Informe uma quantidade maior que zero.';
        }

        if ($quantity > $remaining) {
            $errors[] = 'A quantidade informada ultrapassa o saldo pendente do item.';
        }

        $locationStock = $resolvedLocation['stock_qty'] ?? $item['stock_qty'];
        if ($resolvedLocation !== null && $quantity > (float) $locationStock) {
            $errors[] = 'A locação não possui estoque suficiente para esta quantidade.';
        }

        if (!$errors && $resolvedLocation !== null) {
            $order = tablet_separacao_register_item_separation($order, (int) $item['id'], $resolvedLocation, $quantity);
            tablet_separacao_save_order($order);
            $updatedItem = $order['items'][$item['id']];
            $updatedRemaining = (float) $updatedItem['required_qty'] - (float) $updatedItem['separated_qty'];
            $flashMessage = $updatedRemaining > 0
                ? 'Separação parcial registrada. Item ainda possui saldo pendente.'
                : 'Separação registrada para o item.';
            tablet_separacao_set_flash('success', $flashMessage);
            if ($updatedRemaining > 0) {
                $redirect = app_base_path('item.php?pedido=' . $orderId . '&item=' . $itemId . ($labelSql !== '' ? '&sql=' . urlencode($labelSql) : ''));
            } else {
                $redirect = app_base_path('item.php?pedido=' . $orderId . '&item=' . $itemId . '&auto_label_print=1' . ($labelSql !== '' ? '&sql=' . urlencode($labelSql) : ''));
            }
            header('Location: ' . $redirect);
            exit;
        }
    }
}

$remaining = (float) $item['required_qty'] - (float) $item['separated_qty'];
$itemPercent = tablet_separacao_item_percent($item);
$locationAllowsTyping = (bool) tablet_separacao_config('locacao_permite_digitacao', false);
$locationAllowsCamera = (bool) tablet_separacao_config('locacao_permite_camera', true);
$locationDisplayMode = (string) tablet_separacao_config('locacao_exibicao', 'lista');
$allowDeleteSeparation = (bool) tablet_separacao_config('permitir_excluir_separacao', true);
$suggestedLocations = is_array($item['suggested_locations'] ?? null) ? $item['suggested_locations'] : [];
$displayLocations = $suggestedLocations ?: ($item['locations'] ?? []);
$productMedia = is_array($item['product_media'] ?? null) ? $item['product_media'] : null;
$itemLabelOptions = tablet_separacao_config('etiquetas_item_pedido', []);
$visibleItemLabelOptions = [];
if (is_array($itemLabelOptions)) {
    foreach ($itemLabelOptions as $labelGroup) {
        if (!is_array($labelGroup)) {
            continue;
        }

        $visibleOptions = [];
        foreach (($labelGroup['opcoes'] ?? []) as $labelOption) {
            if (is_array($labelOption) && (bool) ($labelOption['mostrar'] ?? true)) {
                $visibleOptions[] = $labelOption;
            }
        }

        if ($visibleOptions) {
            $labelGroup['opcoes'] = $visibleOptions;
            $visibleItemLabelOptions[] = $labelGroup;
        }
    }
}
$hasItemLabelOptions = !empty($visibleItemLabelOptions);
$labelPrintIcon = '<svg class="button__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V4h9l8.6 8.6a2 2 0 0 1 0 2.8Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M7.5 8.5h.01" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M10 12v4M13 11v6M16 10v4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>';
$itemLabelButtonHtml = '';
if ($hasItemLabelOptions) {
    $returnAfterPrintUrl = app_base_path('pedido.php?id=' . $orderId);
    $itemLabelButtonHtml = sprintf(
        '<button type="button" class="button button--secondary button--icon" data-label-print="true" data-label-scope="item" data-label-id="%s" data-label-sql="%s" data-label-return-url="%s" %s title="Imprimir etiqueta do item" aria-label="Imprimir etiqueta do item">%s</button>',
        h((string) $item['id']),
        h($labelSql),
        h($returnAfterPrintUrl),
        $autoLabelPrint ? 'data-label-auto-open="true"' : '',
        $labelPrintIcon
    );
}
$extraScripts = [];

if ($locationDisplayMode !== 'combo' && $locationAllowsCamera) {
    $extraScripts[] = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
}

tablet_separacao_header('Separar item', [
    'back_href' => app_base_path('pedido.php?id=' . $orderId),
    'show_eyebrow' => false,
    'extra_scripts' => $extraScripts,
    'title_actions_html' => $itemLabelButtonHtml,
]);
?>
<section class="item-page-grid">
    <article class="panel panel--hero">
        <div class="item-product-head">
            <div class="section-heading">
                <p class="section-heading__eyebrow">Pedido <?php echo h((string) $order['id']); ?></p>
                <h2><?php echo h($item['name']); ?></h2>
            </div>
            <?php if ($productMedia): ?>
                <figure class="product-photo-card">
                    <?php if (($productMedia['type'] ?? '') === 'video'): ?>
                        <div class="product-photo-card__video" aria-label="<?php echo h((string) ($productMedia['label'] ?? 'Produto com video')); ?>">
                            <span></span>
                        </div>
                    <?php else: ?>
                        <img src="<?php echo h((string) ($productMedia['url'] ?? '')); ?>" alt="<?php echo h((string) ($productMedia['label'] ?? 'Foto do produto')); ?>">
                    <?php endif; ?>
                </figure>
            <?php endif; ?>
        </div>

        <div class="summary-grid summary-grid--four">
            <article>
                <span class="metric-row__label">Quantidade</span>
                <strong><?php echo h(tablet_separacao_format_qty((float) $item['required_qty'])); ?></strong>
            </article>
            <article>
                <span class="metric-row__label">Saldo pendente</span>
                <strong><?php echo h(tablet_separacao_format_qty($remaining)); ?></strong>
            </article>
            <article>
                <span class="metric-row__label">Código</span>
                <strong><?php echo h($item['code']); ?></strong>
            </article>
            <article>
                <span class="metric-row__label">Unidade</span>
                <strong><?php echo h((string) ($item['unit'] ?? '')); ?></strong>
            </article>
        </div>

        <article class="summary-grid__full location-panel">
            <span class="metric-row__label"><?php echo $suggestedLocations ? 'Locações indicadas' : 'Locações'; ?></span>
            <div class="location-list location-list--stack">
                <?php foreach ($displayLocations as $location): ?>
                    <div class="location-list__row <?php echo !empty($location['suggested']) ? 'location-list__row--suggested' : ''; ?>">
                        <?php if (!empty($location['suggested'])): ?>
                            <strong>
                                <?php echo h((string) ($location['address'] ?: $location['label'])); ?>
                            </strong>
                            <div class="location-list__details">
                                <?php if ((string) ($location['lot'] ?? '') !== ''): ?>
                                    <span>Lote: <b><?php echo h((string) $location['lot']); ?></b></span>
                                <?php endif; ?>
                                <?php if (!empty($location['expiration_date']) && $location['expiration_date'] !== '0000-00-00'): ?>
                                    <span>Validade: <b><?php echo h(date('d/m/Y', strtotime((string) $location['expiration_date']))); ?></b></span>
                                <?php endif; ?>
                                <span>Quantidade indicada: <b><?php echo h(tablet_separacao_format_qty((float) ($location['suggested_qty'] ?? 0))); ?></b></span>
                            </div>
                        <?php else: ?>
                            <strong>
                                <?php echo h(($location['address'] ?: $location['label']) . ' - ' . tablet_separacao_format_qty((float) ($location['stock_qty'] ?? 0))); ?>
                            </strong>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <div class="progress-block">
            <div class="progress-block__head">
                <span><?php echo h($remaining > 0 ? 'Separado' : 'Item concluído'); ?></span>
                <strong><?php echo h(number_format($itemPercent, 1, ',', '.') . '%'); ?></strong>
            </div>
            <div class="progress-bar <?php echo $remaining <= 0 ? 'progress-bar--done' : ''; ?>">
                <div class="progress-bar__fill" style="width: <?php echo h((string) max(0, min(100, $itemPercent))); ?>%;"></div>
            </div>
        </div>
    </article>

    <article class="panel">
        <div class="section-heading">
            <h2>Bipar locação</h2>
        </div>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert--error"><?php echo h($error); ?></div>
        <?php endforeach; ?>

        <form method="post" class="stack-lg">
            <label class="field field--compact">
                <span>Quantidade</span>
                <input
                    type="text"
                    inputmode="decimal"
                    pattern="[0-9]*([,.][0-9]+)?"
                    step="0.0001"
                    min="0.0001"
                    max="<?php echo h((string) $remaining); ?>"
                    name="quantity"
                    placeholder="0,0000"
                    autocomplete="off"
                    value="<?php echo h((string) ($_POST['quantity'] ?? '')); ?>"
                    autofocus
                >
            </label>

            <label class="field">
                <span>Código da locação</span>
                <?php if ($locationDisplayMode === 'combo'): ?>
                    <select name="location_barcode" class="field__select">
                        <option value="">Selecione uma locação</option>
                        <?php foreach (($item['locations'] ?? []) as $location): ?>
                            <?php $locationValue = (string) ($location['barcode'] ?? ''); ?>
                            <option value="<?php echo h($locationValue); ?>" <?php echo ((string) ($_POST['location_barcode'] ?? '')) === $locationValue ? 'selected' : ''; ?>>
                                <?php echo h((string) ($location['selection_label'] ?? (($location['address'] ?: $location['label']) . ' - ' . tablet_separacao_format_qty((float) ($location['stock_qty'] ?? 0))))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <div class="scan-field">
                        <input
                            type="text"
                            name="location_barcode"
                            class="scan-input"
                            placeholder="<?php echo h($locationAllowsTyping ? 'Digite, bipa a locação' : 'Bipe a locação'); ?>"
                            value="<?php echo h((string) ($_POST['location_barcode'] ?? '')); ?>"
                            <?php echo $locationAllowsTyping ? '' : 'readonly'; ?>
                            <?php echo $locationAllowsTyping ? '' : 'inputmode="none"'; ?>
                            <?php echo $locationAllowsTyping ? '' : 'data-scan-capture="true"'; ?>
                            data-allow-typing="<?php echo $locationAllowsTyping ? 'true' : 'false'; ?>"
                        >
                        <?php if ($locationAllowsCamera): ?>
                            <button
                                type="button"
                                class="scan-field__camera"
                                data-barcode-camera-open="true"
                                data-target-input="location_barcode"
                                aria-label="Abrir câmera para leitura da locação"
                                title="Ler pela câmera"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M9 4L7.5 6H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-2.5L15 4H9zm3 4.5A4.5 4.5 0 1 1 7.5 13 4.5 4.5 0 0 1 12 8.5zm0 2A2.5 2.5 0 1 0 14.5 13 2.5 2.5 0 0 0 12 10.5z"/>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </label>

            <?php if (!tablet_separacao_real_mode()): ?>
                <div class="hint-card">
                    <strong>Teste de fluxo atual</strong>
                    <p>Para este protótipo, use a locação <code><?php echo h($item['location_barcode']); ?></code>.</p>
                </div>
            <?php endif; ?>

            <?php if ($resolvedLocation): ?>
                <div class="location-result">
                    <span class="badge badge--success">Locação válida</span>
                    <h3><?php echo h($resolvedLocation['label']); ?></h3>
                    <p>Endereço: <?php echo h($resolvedLocation['address']); ?></p>
                </div>
            <?php endif; ?>

            <div class="button-row">
                <button class="button button--primary" type="submit">Confirmar separação</button>
                <a class="button button--secondary" href="<?php echo h(app_base_path('pedido.php?id=' . $orderId)); ?>">Voltar ao pedido</a>
            </div>
        </form>

        <?php if (!empty($item['separations'])): ?>
            <div class="history-panel">
                <div class="section-heading">
                    <p class="section-heading__eyebrow">Histórico</p>
                    <h2>Locações já usadas neste item</h2>
                </div>
                <div class="stack-md">
                    <?php foreach ($item['separations'] as $separation): ?>
                        <div class="history-row">
                            <div class="history-row__content">
                                <strong><?php echo h($separation['location_label']); ?></strong>
                            </div>
                            <div class="history-row__actions">
                                <span class="badge badge--success">
                                    <?php echo h(tablet_separacao_format_qty((float) $separation['quantity'])); ?>
                                </span>
                                <?php if ($allowDeleteSeparation): ?>
                                    <form method="post" data-confirm="Deseja excluir esta separação desta locação?">
                                        <input type="hidden" name="action" value="delete_separation">
                                        <input type="hidden" name="separation_key" value="<?php echo h((string) ($separation['key'] ?? '')); ?>">
                                        <button class="button button--danger button--small" type="submit">Excluir</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </article>
</section>
<?php if ($hasItemLabelOptions): ?>
    <script type="application/json" id="item-label-options">
        <?php echo json_encode([
            'item' => $visibleItemLabelOptions,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
    </script>
<?php endif; ?>
<?php tablet_separacao_footer(); ?>
