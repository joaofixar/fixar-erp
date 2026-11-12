<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/layout.php';

tablet_separacao_require_auth();

$search = trim((string) ($_GET['q'] ?? ''));
$tab = (string) ($_GET['tab'] ?? 'start');
$deadlineFilter = (string) ($_GET['deadline'] ?? 'all');
$issuedFilter = (string) ($_GET['issued'] ?? 'all');
$separatorFilter = trim((string) ($_GET['separator'] ?? ''));
$tab = in_array($tab, ['start', 'progress'], true) ? $tab : 'start';
$deadlineFilter = in_array($deadlineFilter, ['all', 'late', 'soon', 'ok'], true) ? $deadlineFilter : 'all';
$issuedFilter = in_array($issuedFilter, ['all', 'today', 'recent', 'older'], true) ? $issuedFilter : 'all';
$orders = tablet_separacao_orders($tab);
$currentUser = tablet_separacao_user();
$currentSeparator = trim((string) ($currentUser['name'] ?? ''));
$showSearchFilter = (bool) tablet_separacao_config('mostrar_busca_pedidos', true);
$showIssuedFilter = (bool) tablet_separacao_config('mostrar_filtro_emissao', true);
$showDeadlineFilter = (bool) tablet_separacao_config('mostrar_filtro_entrega', true);
$showSeparatorFilter = (bool) tablet_separacao_config('mostrar_filtro_separador', true);
$showVendor = (bool) tablet_separacao_config('mostrar_vendedor', true);
$showCarrier = (bool) tablet_separacao_config('mostrar_transportadora', true);
$showObservation = (bool) tablet_separacao_config('mostrar_observacao', true);
$showSeparatedPercent = (bool) tablet_separacao_config('mostrar_percentual_separado', true);
$showCurrentSeparator = (bool) tablet_separacao_config('mostrar_quem_esta_separando', true);

if (!$showSearchFilter) {
    $search = '';
}

$separatorOptions = [];
foreach ($orders as $order) {
    $separatorName = trim((string) ($order['separator'] ?? ''));
    if ($separatorName !== '') {
        $separatorOptions[$separatorName] = $separatorName;
    }
}
ksort($separatorOptions);

$separatorChoices = ['' => 'Todos'];
if ($tab === 'progress' && $currentSeparator !== '') {
    $separatorChoices[$currentSeparator] = 'Eu';
    unset($separatorOptions[$currentSeparator]);
}
foreach ($separatorOptions as $separatorName) {
    $separatorChoices[$separatorName] = $separatorName;
}

if ($search !== '') {
    $orders = array_filter($orders, static function (array $order) use ($search): bool {
        $needle = strtoupper($search);
        return strpos((string) $order['id'], $search) !== false
            || strpos(strtoupper($order['client_name']), $needle) !== false
            || strpos(strtoupper((string) ($order['carrier'] ?? '')), $needle) !== false;
    });
}

if ($deadlineFilter !== 'all') {
    $orders = array_filter($orders, static function (array $order) use ($deadlineFilter): bool {
        $status = tablet_separacao_delivery_status($order['delivery_at'] ?? '');
        return (string) ($status['filter'] ?? 'all') === $deadlineFilter;
    });
}

if ($issuedFilter !== 'all') {
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $weekAgo = date('Y-m-d', strtotime('-7 days'));
    $orders = array_filter($orders, static function (array $order) use ($issuedFilter, $today, $yesterday, $weekAgo): bool {
        $issuedAt = substr((string) ($order['issued_at'] ?? ''), 0, 10);
        if ($issuedAt === '' || $issuedAt === '0000-00-00') {
            return false;
        }

        switch ($issuedFilter) {
            case 'today':
                return $issuedAt === $today;
            case 'recent':
                return $issuedAt >= $yesterday;
            case 'older':
                return $issuedAt < $weekAgo;
            default:
                return true;
        }
    });
}

if ($tab === 'progress' && $separatorFilter !== '') {
    $orders = array_filter($orders, static function (array $order) use ($separatorFilter): bool {
        return trim((string) ($order['separator'] ?? '')) === $separatorFilter;
    });
}

tablet_separacao_header('Pedidos para separação', [
    'auto_refresh' => 30,
    'show_eyebrow' => false,
]);
?>
<?php if ($showSearchFilter || ($showSeparatorFilter && $tab === 'progress')): ?>
    <section class="page-head">
        <div class="search-row">
            <?php if ($showSearchFilter): ?>
                <form method="get" class="search-bar">
                    <input type="hidden" name="tab" value="<?php echo h($tab); ?>">
                    <input type="hidden" name="deadline" value="<?php echo h($deadlineFilter); ?>">
                    <input type="hidden" name="issued" value="<?php echo h($issuedFilter); ?>">
                    <?php if ($separatorFilter !== ''): ?>
                        <input type="hidden" name="separator" value="<?php echo h($separatorFilter); ?>">
                    <?php endif; ?>
                    <input
                        type="search"
                        name="q"
                        value="<?php echo h($search); ?>"
                        placeholder="Buscar por pedido, cliente ou transportadora"
                    >
                    <button class="button button--primary" type="submit">Buscar</button>
                </form>
            <?php endif; ?>

            <div class="search-filter-slot">
                <?php if ($showSeparatorFilter && $tab === 'progress'): ?>
                    <form method="get" class="search-filter">
                        <input type="hidden" name="tab" value="<?php echo h($tab); ?>">
                        <input type="hidden" name="deadline" value="<?php echo h($deadlineFilter); ?>">
                        <input type="hidden" name="issued" value="<?php echo h($issuedFilter); ?>">
                        <?php if ($showSearchFilter): ?>
                            <input type="hidden" name="q" value="<?php echo h($search); ?>">
                        <?php endif; ?>
                        <label class="search-bar__select">
                            <span class="search-bar__label">Separador</span>
                            <select name="separator" onchange="this.form.submit()">
                                <?php foreach ($separatorChoices as $separatorValue => $separatorLabel): ?>
                                    <option value="<?php echo h($separatorValue); ?>" <?php echo $separatorFilter === $separatorValue ? 'selected' : ''; ?>>
                                        <?php echo h($separatorLabel); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($showIssuedFilter || $showDeadlineFilter): ?>
    <section class="filter-stack">
        <div class="filter-row filter-row--inline">
            <?php if ($showIssuedFilter): ?>
                <div class="filter-group">
                    <span class="section-heading__eyebrow filter-group__label">Emissão</span>
                    <div class="filter-chips filter-chips--compact">
                        <a class="filter-chip <?php echo $issuedFilter === 'all' ? 'filter-chip--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=' . $tab . '&deadline=' . $deadlineFilter . '&issued=all&separator=' . urlencode($separatorFilter) . '&q=' . urlencode($search))); ?>">
                            Todas
                        </a>
                        <a class="filter-chip <?php echo $issuedFilter === 'today' ? 'filter-chip--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=' . $tab . '&deadline=' . $deadlineFilter . '&issued=today&separator=' . urlencode($separatorFilter) . '&q=' . urlencode($search))); ?>">
                            Hoje
                        </a>
                        <a class="filter-chip <?php echo $issuedFilter === 'recent' ? 'filter-chip--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=' . $tab . '&deadline=' . $deadlineFilter . '&issued=recent&separator=' . urlencode($separatorFilter) . '&q=' . urlencode($search))); ?>">
                            Ontem e hoje
                        </a>
                        <a class="filter-chip <?php echo $issuedFilter === 'older' ? 'filter-chip--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=' . $tab . '&deadline=' . $deadlineFilter . '&issued=older&separator=' . urlencode($separatorFilter) . '&q=' . urlencode($search))); ?>">
                            Mais antigos
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showDeadlineFilter): ?>
                <div class="filter-group">
                    <span class="section-heading__eyebrow filter-group__label">Entrega</span>
                    <div class="filter-chips filter-chips--compact">
                        <a class="filter-chip <?php echo $deadlineFilter === 'all' ? 'filter-chip--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=' . $tab . '&deadline=all&issued=' . $issuedFilter . '&separator=' . urlencode($separatorFilter) . '&q=' . urlencode($search))); ?>">
                            Todos os prazos
                        </a>
                        <a class="filter-chip <?php echo $deadlineFilter === 'late' ? 'filter-chip--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=' . $tab . '&deadline=late&issued=' . $issuedFilter . '&separator=' . urlencode($separatorFilter) . '&q=' . urlencode($search))); ?>">
                            Atrasados
                        </a>
                        <a class="filter-chip <?php echo $deadlineFilter === 'soon' ? 'filter-chip--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=' . $tab . '&deadline=soon&issued=' . $issuedFilter . '&separator=' . urlencode($separatorFilter) . '&q=' . urlencode($search))); ?>">
                            Hoje e amanhã
                        </a>
                        <a class="filter-chip <?php echo $deadlineFilter === 'ok' ? 'filter-chip--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=' . $tab . '&deadline=ok&issued=' . $issuedFilter . '&separator=' . urlencode($separatorFilter) . '&q=' . urlencode($search))); ?>">
                            Dentro do prazo
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<section class="tab-switch">
    <a class="tab-switch__item <?php echo $tab === 'start' ? 'tab-switch__item--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=start')); ?>">
        Iniciar separação
    </a>
    <a class="tab-switch__item <?php echo $tab === 'progress' ? 'tab-switch__item--active' : ''; ?>" href="<?php echo h(app_base_path('pedidos.php?tab=progress')); ?>">
        Separando
    </a>
</section>

<section class="order-grid">
    <?php foreach ($orders as $order): ?>
        <?php $deliveryStatus = tablet_separacao_delivery_status($order['delivery_at'] ?? ''); ?>
        <?php $progressPercent = tablet_separacao_order_progress_percent($order); ?>
        <a class="order-card" href="<?php echo h(app_base_path('pedido.php?id=' . $order['id'])); ?>">
            <div class="order-card__top">
                <span class="badge"><?php echo h($tab === 'start' ? 'INICIAR' : $order['status']); ?></span>
                <span class="order-card__id">Pedido <?php echo h((string) $order['id']); ?></span>
            </div>
            <h2><?php echo h($order['client_name']); ?></h2>
            <p><?php echo h(tablet_separacao_format_city_uf($order)); ?></p>
            <div class="order-card__meta-grid">
                <div>
                    <span class="metric-row__label">Emissão</span>
                    <strong><?php echo h(!empty($order['issued_at']) ? date('d/m/Y', strtotime($order['issued_at'])) : '-'); ?></strong>
                </div>
                <div>
                    <span class="metric-row__label">Prazo de entrega</span>
                    <div class="deadline-block <?php echo h($deliveryStatus['class']); ?>" title="<?php echo h($deliveryStatus['title']); ?>">
                        <strong><?php echo h($deliveryStatus['label']); ?></strong>
                        <span><?php echo h($deliveryStatus['title']); ?></span>
                    </div>
                </div>
                <?php if ($showCarrier): ?>
                    <div>
                        <span class="metric-row__label">Transportadora</span>
                        <strong><?php echo h($order['carrier'] !== '' ? $order['carrier'] : '-'); ?></strong>
                    </div>
                <?php endif; ?>
                <?php if ($showVendor): ?>
                    <div>
                        <span class="metric-row__label">Vendedor</span>
                        <strong><?php echo h($order['seller'] !== '' ? $order['seller'] : '-'); ?></strong>
                    </div>
                <?php endif; ?>
                <?php if ($tab === 'progress'): ?>
                    <?php if ($showCurrentSeparator): ?>
                        <div>
                            <span class="metric-row__label">Quem está separando</span>
                            <strong><?php echo h($order['separator'] !== '' ? $order['separator'] : '-'); ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($showSeparatedPercent): ?>
                        <div class="<?php echo $progressPercent > 0 ? 'order-card__progress-highlight' : ''; ?>">
                            <span class="metric-row__label">Percentual separado</span>
                            <strong><?php echo h(number_format($progressPercent, 1, ',', '.') . '%'); ?></strong>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php if ($showObservation && !empty($order['observation'])): ?>
                <div class="order-card__observation order-card__observation--note">
                    <span class="metric-row__label">OBS</span>
                    <strong><?php echo h($order['observation']); ?></strong>
                </div>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>

    <?php if (!$orders): ?>
        <article class="empty-state">
            <h3>Nenhum pedido encontrado nesta aba.</h3>
            <p>Assim que houver pedidos nesta fila, eles aparecerão aqui.</p>
        </article>
    <?php endif; ?>
</section>
<?php tablet_separacao_footer(); ?>
