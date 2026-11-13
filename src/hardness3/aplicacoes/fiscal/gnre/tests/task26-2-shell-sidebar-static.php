<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$presentation = (string) file_get_contents($root . '/public/task10-presentation.php');
$css = (string) file_get_contents($root . '/public/gnre.css');
$js = (string) file_get_contents($root . '/public/gnre-ui.js');
$checks = [
    'sidebar' => strpos($presentation, 'data-gnre-sidebar') !== false,
    'menu-toggle' => strpos($presentation, 'data-gnre-menu-toggle') !== false,
    'breadcrumb' => strpos($presentation, 'gnre-breadcrumb') !== false,
    'active-navigation' => strpos($presentation, "aria-current=\"page\"") !== false,
    'simple-current-title-contract' => strpos($presentation, '$shellTitle=$guide') !== false && strpos($js, 'window.gnreShellTitle') !== false,
    'fixed-main-header' => strpos($presentation, '.gnre-topbar{position:fixed;top:0;left:300px;right:0;z-index:15;') !== false,
    'single-header-height-variable' => strpos($presentation, '--gnre-shell-header-height:88px') !== false && strpos($presentation, 'height:var(--gnre-shell-header-height)!important') !== false,
    'header-content-clearance' => strpos($presentation, 'padding-top:calc(var(--gnre-shell-header-height) + var(--gnre-shell-header-gap))!important') !== false,
    'mobile-header-height-variable' => strpos($presentation, '--gnre-shell-header-height:136px') !== false,
    'summary-cards-have-hierarchy' => strpos($js, "document.querySelectorAll('.gnre-cards .gnre-card')") !== false && strpos($presentation, '.gnre-content .gnre-card strong') !== false,
    'duplicate-company-title-hidden' => strpos($presentation, '.gnre-topbar .eyebrow,.gnre-breadcrumb{display:none}') !== false,
    'company-form-preserved' => strpos($presentation, 'data-gnre-company-form') !== false && strpos($presentation, 'name="csrf"') !== false,
    'mobile-drawer-css' => strpos($css, '.gnre-menu-open .gnre-sidebar') !== false && strpos($css, '@media(max-width:900px)') !== false,
    'visible-focus' => strpos($css, ':focus-visible') !== false,
    'escape-close' => strpos($js, "event.key === 'Escape'") !== false,
    'aria-expanded' => strpos($js, "setAttribute('aria-expanded'") !== false,
    'official-logo-exists' => is_file($root . '/../../../static/img/logo_hardness.png'),
    'official-logo-loaded-by-shell' => strpos($css, "url('/hardness3/static/img/logo_hardness.png')") !== false,
    'white-sidebar-body' => strpos($css, '.gnre-sidebar{position:sticky;top:0;height:100vh;padding:26px 14px;background:#fff;') !== false,
    'blue-brand-strip-only' => strpos($css, '.gnre-brand{min-height:82px;') !== false && strpos($css, 'background:var(--blue-deep) url(') !== false,
    'accessible-svg-icons' => strpos($js, "'<svg viewBox=\"0 0 24 24\" aria-hidden=\"true\">") !== false && strpos($css, '.gnre-nav-icon svg') !== false,
    'source-notes-document-svg' => strpos($presentation, 'data-gnre-nav-icon="source-notes"') !== false && strpos($presentation, 'Gerar guias NFe</a>') !== false && strpos($js, "'source-notes': '<svg") !== false,
    'shared-dashboard-icon-language' => strpos($js, "'guides': '<svg") !== false && strpos($js, "'new-guide': '<svg") !== false && strpos($js, "'batch-transmit': '<svg") !== false && strpos($css, 'stroke-width:1.8') !== false,
    'operational-menu-order' => strrpos($presentation, "?view=batch_transmit" ) < strrpos($presentation, "?view=source_notes"),
    'configuration-gear-svg' => strpos($presentation, 'data-gnre-nav-icon="config"') !== false && strpos($presentation, 'Configuração</a>') !== false && strpos($js, "'config': '<svg") !== false,
    'targeted-icon-hydration' => strpos($js, "document.querySelectorAll('[data-gnre-nav-icon]')") !== false && strpos($js, "document.querySelectorAll('.gnre-nav-icon').forEach") === false,
    'no-external-assets' => strpos($presentation . $css . $js, 'http://') === false && strpos($presentation . $css . $js, 'https://') === false,
];
foreach ($checks as $name => $passed) {
    if (!$passed) {
        fwrite(STDERR, "FAILED: {$name}\n");
        exit(1);
    }
}
echo "Task 26.2 shell static checks passed\n";
