<?php
namespace hardness;

require_once __DIR__ . '/app.php';

function importador_orcamento_pdf_header($title, $options = array())
{
    importador_orcamento_pdf_bootstrap();
    $pageClass = isset($options['page_class']) ? $options['page_class'] : '';
    $subtitle = isset($options['subtitle']) ? $options['subtitle'] : '';
    $cssVersion = @filemtime(IMPORTADOR_ORCAMENTO_PDF_MODULE_DIR . '/assets/css/app.css');
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo importador_orcamento_pdf_h($title); ?> | Importador de Orcamento</title>
    <link rel="stylesheet" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('assets/css/app.css') . '?v=' . $cssVersion); ?>">
</head>
<body class="<?php echo importador_orcamento_pdf_h($pageClass); ?>">
    <div class="app-shell">
        <header class="topbar">
            <div>
                <p class="topbar__eyebrow">Orcamento</p>
                <h1><?php echo importador_orcamento_pdf_h($title); ?></h1>
                <?php if ($subtitle !== ''): ?>
                    <p><?php echo importador_orcamento_pdf_h($subtitle); ?></p>
                <?php endif; ?>
            </div>
        </header>
        <main class="page">
    <?php
}

function importador_orcamento_pdf_footer()
{
    $jsVersion = @filemtime(IMPORTADOR_ORCAMENTO_PDF_MODULE_DIR . '/assets/js/app.js');
    ?>
        </main>
    </div>
    <div class="processing-overlay" hidden data-processing-overlay>
        <div class="processing-overlay__panel">
            <div class="processing-overlay__spinner" aria-hidden="true"></div>
            <div class="processing-overlay__text" data-processing-overlay-text>Processando...</div>
        </div>
    </div>
    <script src="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('assets/js/app.js') . '?v=' . $jsVersion); ?>"></script>
</body>
</html>
    <?php
}
