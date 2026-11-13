<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/layout.php';

tablet_separacao_require_auth();

$orderId = (int) ($_GET['id'] ?? 0);
$order = tablet_separacao_find_order($orderId);

if (!$order) {
    tablet_separacao_set_flash('error', 'Pedido não encontrado.');
    header('Location: ' . app_base_path('pedidos.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'finalize');
    if ($action === 'start') {
        $start = tablet_separacao_start_order($orderId);
        tablet_separacao_set_flash($start['ok'] ? 'success' : 'error', $start['message']);
        header('Location: ' . app_base_path('pedido.php?id=' . $orderId));
        exit;
    } elseif ($action === 'return_to_seller') {
        $return = tablet_separacao_return_order_to_seller($orderId);
        tablet_separacao_set_flash($return['ok'] ? 'success' : 'error', $return['message']);
        header('Location: ' . app_base_path($return['ok'] ? 'pedidos.php?tab=start' : 'pedido.php?id=' . $orderId));
        exit;
    } else {
        $box = trim((string) ($_POST['box'] ?? ''));
        $finalize = tablet_separacao_finalize_order($orderId, $box);
        if ($finalize['ok']) {
            header('Location: ' . app_base_path('pedidos.php?tab=progress'));
            exit;
        }
        tablet_separacao_set_flash('error', $finalize['message']);
        header('Location: ' . app_base_path('pedido.php?id=' . $orderId));
        exit;
    }
}

$pendingItems = tablet_separacao_pending_items($order);
$allItems = $order['items'];
$canOperateItems = ((string) ($order['status_flag'] ?? '') !== '1');
$backTab = $canOperateItems ? 'progress' : 'start';
[$done, $total] = tablet_separacao_order_progress($order);
$progressPercent = tablet_separacao_order_progress_percent($order);
$deliveryStatus = tablet_separacao_delivery_status($order['delivery_at'] ?? '');
$labelSql = (string) ($_GET['sql'] ?? '');
$orderLabelOptions = tablet_separacao_config('etiquetas_pedido', []);
$itemLabelOptions = tablet_separacao_config('etiquetas_item_pedido', []);
$visibleOrderLabelOptions = [];
if (is_array($orderLabelOptions)) {
    foreach ($orderLabelOptions as $labelGroup) {
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
            $visibleOrderLabelOptions[] = $labelGroup;
        }
    }
}
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
$showItemLabelButtonOnOrder = (bool) tablet_separacao_config('mostrar_etiqueta_item_no_pedido', true);
$hasOrderLabelOptions = !empty($visibleOrderLabelOptions);
$hasItemLabelOptions = !empty($visibleItemLabelOptions) && $showItemLabelButtonOnOrder;
$labelPrintIcon = '<svg class="button__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V4h9l8.6 8.6a2 2 0 0 1 0 2.8Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M7.5 8.5h.01" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M10 12v4M13 11v6M16 10v4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>';
$returnOrderIcon = '<svg class="button__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10 7 5 12l5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 12H6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M15 7h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
if ($hasOrderLabelOptions) {
    $titleActionsHtml = sprintf(
        '<button type="button" class="button button--secondary button--icon" data-label-print="true" data-label-scope="order" data-label-id="%s" data-label-sql="%s" title="Imprimir etiquetas do pedido" aria-label="Imprimir etiquetas do pedido">%s</button>',
        h((string) $order['id']),
        h($labelSql),
        $labelPrintIcon
    );
} else {
    $titleActionsHtml = '';
}
$titleActionsHtml .= sprintf(
    '<form method="post" data-confirm="Confirma retornar este pedido para o vendedor?" data-confirm-modal="true"><input type="hidden" name="action" value="return_to_seller"><button type="submit" class="button button--danger button--compact" title="Retornar pedido para o vendedor" aria-label="Retornar pedido para o vendedor">%s<span>Retornar Vend.</span></button></form>',
    $returnOrderIcon
);

tablet_separacao_header('Pedido ' . (string) $order['id'], [
    'back_href' => app_base_path('pedidos.php?tab=' . $backTab),
    'auto_refresh' => 30,
    'show_eyebrow' => false,
    'title_actions_html' => $titleActionsHtml,
]);
?>
<section class="panel panel--hero">
    <div class="summary-grid">
        <article>
            <span class="metric-row__label">Cliente</span>
            <strong><?php echo h($order['client_name']); ?></strong>
        </article>
        <article>
            <span class="metric-row__label">Cidade</span>
            <strong><?php echo h(tablet_separacao_format_city_uf($order)); ?></strong>
        </article>
        <article>
            <span class="metric-row__label">Data de emissão</span>
            <strong><?php echo h(!empty($order['issued_at']) ? date('d/m/Y', strtotime($order['issued_at'])) : '-'); ?></strong>
        </article>
        <article>
            <span class="metric-row__label">Data de entrega</span>
            <div class="deadline-block <?php echo h($deliveryStatus['class']); ?>" title="<?php echo h($deliveryStatus['title']); ?>">
                <strong><?php echo h($deliveryStatus['label']); ?></strong>
                <span><?php echo h($deliveryStatus['title']); ?></span>
            </div>
        </article>
        <article>
            <span class="metric-row__label">Transportadora</span>
            <strong><?php echo h($order['carrier'] !== '' ? $order['carrier'] : '-'); ?></strong>
        </article>
        <?php if ($order['observation'] !== ''): ?>
            <article class="order-card__observation order-card__observation--note">
                <span class="metric-row__label">OBS</span>
                <strong><?php echo h($order['observation']); ?></strong>
            </article>
        <?php endif; ?>
    </div>
</section>

<?php if (!$canOperateItems): ?>
    <section class="panel panel--start-action">
        <div class="section-heading">
            <p class="section-heading__eyebrow">Início do pedido</p>
            <h2>Confirmar início da separação</h2>
        </div>
        <form method="post" class="button-row" data-confirm="Confirmar o início da separação deste pedido?">
            <input type="hidden" name="action" value="start">
            <button class="button button--start" type="submit">Iniciar separação</button>
            <a class="button button--secondary" href="<?php echo h(app_base_path('pedidos.php?tab=start')); ?>">Voltar para fila</a>
        </form>
    </section>
<?php endif; ?>

<section class="section-heading section-heading--space">
    <p class="section-heading__eyebrow">Itens do pedido</p>
    <h2><?php echo h($canOperateItems ? 'Selecionar item para separar' : 'Itens liberados após iniciar a separação'); ?></h2>
</section>

<section class="stack-md">
    <?php foreach ($allItems as $item): ?>
        <?php $remaining = (float) $item['required_qty'] - (float) $item['separated_qty']; ?>
        <?php $completed = $remaining <= 0; ?>
        <?php $itemPercent = tablet_separacao_item_percent($item); ?>
        <article class="item-row <?php echo $completed ? 'item-row--done' : ''; ?>">
            <div class="item-row__main">
                <div class="item-row__meta">
                    <span class="badge"><?php echo h($item['code']); ?></span>
                    <span class="badge <?php echo $completed ? 'badge--success' : ''; ?>">
                        <?php echo h($completed ? 'Separado' : 'Pendente'); ?>
                    </span>
                </div>
                <h3><?php echo h($item['name']); ?></h3>
                <div class="metric-row">
                    <div>
                        <span class="metric-row__label">Saldo</span>
                        <strong><?php echo h(tablet_separacao_format_qty(max(0, $remaining))); ?></strong>
                    </div>
                    <div>
                        <span class="metric-row__label">Separado</span>
                        <strong><?php echo h(tablet_separacao_format_qty((float) $item['separated_qty'])); ?></strong>
                    </div>
                </div>
                <?php if (!empty($item['locations'])): ?>
                    <div class="location-list">
                        <span class="metric-row__label">Locações disponíveis</span>
                        <div class="location-list__chips">
                            <?php foreach ($item['locations'] as $location): ?>
                                <span class="location-chip">
                                    <?php echo h(($location['address'] ?: $location['label']) . ' (' . tablet_separacao_format_qty((float) ($location['stock_qty'] ?? 0)) . ')'); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="progress-block progress-block--item">
                    <div class="progress-block__head">
                        <span><?php echo h($completed ? 'Item concluído' : 'Avanço do item'); ?></span>
                        <strong><?php echo h(number_format($itemPercent, 1, ',', '.') . '%'); ?></strong>
                    </div>
                    <div class="progress-bar <?php echo $completed ? 'progress-bar--done' : ''; ?>">
                        <div class="progress-bar__fill" style="width: <?php echo h((string) max(0, min(100, $itemPercent))); ?>%;"></div>
                    </div>
                </div>
                <?php if (!empty($item['separations'])): ?>
                    <div class="separation-history">
                        <?php foreach ($item['separations'] as $separation): ?>
                            <div class="separation-chip">
                                <strong><?php echo h($separation['address']); ?></strong>
                                <span><?php echo h(tablet_separacao_format_qty((float) $separation['quantity'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="item-row__actions">
                <?php if ($hasItemLabelOptions): ?>
                    <button
                        type="button"
                        class="button button--secondary"
                        data-label-print="true"
                        data-label-scope="item"
                        data-label-id="<?php echo h((string) $item['id']); ?>"
                        data-label-sql="<?php echo h($labelSql); ?>"
                        title="Imprimir etiqueta"
                        aria-label="Imprimir etiqueta"
                    >
                        <?php echo $labelPrintIcon; ?>
                    </button>
                <?php endif; ?>
                <?php if ($completed): ?>
                    <?php $itemUrl = 'item.php?pedido=' . $order['id'] . '&item=' . $item['id'] . ($labelSql !== '' ? '&sql=' . urlencode($labelSql) : ''); ?>
                    <a class="button button--success" href="<?php echo h(app_base_path($itemUrl)); ?>">
                        Item concluído
                    </a>
                <?php elseif (!$canOperateItems): ?>
                    <span class="button button--disabled">Inicie o pedido</span>
                <?php else: ?>
                    <?php $itemUrl = 'item.php?pedido=' . $order['id'] . '&item=' . $item['id'] . ($labelSql !== '' ? '&sql=' . urlencode($labelSql) : ''); ?>
                    <a class="button button--primary" href="<?php echo h(app_base_path($itemUrl)); ?>">
                        Separar item
                    </a>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if (!$pendingItems): ?>
        <article class="empty-state">
            <h3>Todos os itens foram separados.</h3>
            <p>Você já pode seguir para a finalização do pedido.</p>
        </article>
    <?php endif; ?>
</section>

<?php if ($hasOrderLabelOptions || $hasItemLabelOptions): ?>
    <script type="application/json" id="label-print-options">
        <?php echo json_encode([
            'order' => $visibleOrderLabelOptions,
            'item' => $visibleItemLabelOptions,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
    </script>
<?php endif; ?>

<?php if ($canOperateItems): ?>
    <section class="panel panel--footer">
        <div class="section-heading">
            <p class="section-heading__eyebrow">Finalizacao</p>
            <h2>Continuar pedido</h2>
        </div>
        <form method="post" class="finalize-form">
            <input type="hidden" name="action" value="finalize">
            <label class="field">
                <span>BOX</span>
                <input type="text" name="box" value="<?php echo h($order['box']); ?>" placeholder="Informe o BOX">
            </label>
            <button class="button button--primary" type="submit">Continuar</button>
        </form>
    </section>
<?php endif; ?>
<?php tablet_separacao_footer(); ?>
