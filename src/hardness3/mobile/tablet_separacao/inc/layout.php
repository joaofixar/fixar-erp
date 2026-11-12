<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

$tabletSeparacaoLayoutExtraScripts = [];

function tablet_separacao_header(string $title, array $options = []): void
{
    global $tabletSeparacaoLayoutExtraScripts;
    $user = tablet_separacao_user();
    $activeCompany = tablet_separacao_active_company();
    $companies = tablet_separacao_companies();
    $pageClass = $options['page_class'] ?? '';
    $subtitle = $options['subtitle'] ?? '';
    $backHref = $options['back_href'] ?? null;
    $companySwitcher = $options['company_switcher'] ?? true;
    $autoRefresh = (int) ($options['auto_refresh'] ?? 0);
    $showEyebrow = $options['show_eyebrow'] ?? true;
    $brandHtml = $options['brand_html'] ?? null;
    $titleActionsHtml = (string) ($options['title_actions_html'] ?? '');
    $extraScripts = $options['extra_scripts'] ?? [];
    $tabletSeparacaoLayoutExtraScripts = (array) $extraScripts;
    $flash = tablet_separacao_pull_flash();
    $appStyleVersion = (string) @filemtime(__DIR__ . '/../assets/css/app.css');
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h($title); ?> | Tablet Separacao</title>
    <link rel="stylesheet" href="<?php echo h(app_base_path('assets/css/app.css') . '?v=' . $appStyleVersion); ?>">
</head>
<body class="<?php echo h($pageClass); ?>"<?php echo $autoRefresh > 0 ? ' data-auto-refresh="' . h((string) $autoRefresh) . '"' : ''; ?>>
    <div class="app-shell">
        <header class="topbar">
            <div class="topbar__brand">
                <?php if ($backHref): ?>
                    <a class="topbar__back" href="<?php echo h($backHref); ?>">Voltar</a>
                <?php endif; ?>
                <?php if ($brandHtml !== null): ?>
                    <div class="topbar__custom-brand"><?php echo $brandHtml; ?></div>
                <?php else: ?>
                    <div>
                        <?php if ($showEyebrow): ?>
                            <p class="topbar__eyebrow">Hardness Systems</p>
                        <?php endif; ?>
                        <div class="topbar__title-row">
                            <h1 class="topbar__title"><?php echo h($title); ?></h1>
                            <?php if ($titleActionsHtml !== ''): ?>
                                <div class="topbar__title-actions"><?php echo $titleActionsHtml; ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($subtitle !== ''): ?>
                            <p class="topbar__subtitle"><?php echo h($subtitle); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($user): ?>
                <div class="topbar__user">
                    <?php if ($companySwitcher && count($companies) > 1): ?>
                        <form method="post" action="<?php echo h(app_base_path('change-company.php')); ?>" class="company-switcher">
                            <input type="hidden" name="redirect" value="<?php echo h($_SERVER['REQUEST_URI'] ?? app_base_path('pedidos.php')); ?>">
                            <label class="company-switcher__label" for="company_id">Empresa</label>
                            <select id="company_id" name="company_id" class="company-switcher__select" data-auto-submit="true">
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?php echo h((string) $company['id']); ?>" <?php echo (int) $company['id'] === (int) ($activeCompany['id'] ?? 0) ? 'selected' : ''; ?>>
                                        <?php echo h($company['short_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    <?php elseif ($activeCompany): ?>
                        <span class="badge"><?php echo h($activeCompany['short_name']); ?></span>
                    <?php endif; ?>
                    <span class="badge badge--solid"><?php echo h($user['name']); ?></span>
                    <a class="ghost-link" href="<?php echo h(app_base_path('logout.php')); ?>">Sair</a>
                </div>
            <?php endif; ?>
        </header>

        <main class="page">
            <?php if ($flash): ?>
                <section class="alert alert--<?php echo h($flash['type']); ?>">
                    <?php echo h($flash['message']); ?>
                </section>
            <?php endif; ?>
    <?php
}

function tablet_separacao_footer(): void
{
    global $tabletSeparacaoLayoutExtraScripts;
    $appScriptVersion = (string) @filemtime(__DIR__ . '/../assets/js/app.js');
    ?>
        </main>
    </div>
    <?php foreach ($tabletSeparacaoLayoutExtraScripts as $scriptSrc): ?>
        <script src="<?php echo h((string) $scriptSrc); ?>"></script>
    <?php endforeach; ?>
    <script src="<?php echo h(app_base_path('assets/js/app.js') . '?v=' . $appScriptVersion); ?>"></script>
</body>
</html>
    <?php
}
