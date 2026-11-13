<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
$context = balanco_require_access();
if (!balanco_can_process()) {
    balanco_render_access_denied($context);
}
$scan = trim((string) ($_GET['scan'] ?? ''));
$matches = $scan === '' ? [] : balanco_product_matches($scan);
if (count($matches) === 1) { header('Location: ' . balanco_url('locacoes.php?d009_id=' . (int) $matches[0]['D009_Id'])); exit; }
balanco_header('Balanço', $context, [
    // Mesma biblioteca e abordagem de câmera usadas em mobile/tablet_separacao.
    'extra_scripts' => ['https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'],
    'show_company_change' => true,
]);
?>
<section class="scan-stage"><p class="eyebrow">Próximo item</p><h2>Bipe a etiqueta do produto</h2><p>Use o código do produto ou seu EAN/código de barras.</p><form class="scan-form" method="get"><div class="scan-field"><input id="product-scan" name="scan" value="<?php echo balanco_h($scan); ?>" inputmode="none" autocomplete="off" autofocus placeholder="Aguardando leitura do produto"><button type="button" class="scan-field__camera" data-barcode-camera-open="true" data-barcode-label="produto" data-target-input="product-scan" aria-label="Ler produto pela câmera" title="Ler produto pela câmera"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3 7.2 5H4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3.2L15 3H9Zm3 5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/></svg></button></div><button class="button" type="submit">Localizar</button></form></section>
<?php if ($scan !== '' && !$matches): balanco_feedback_dialog('error', 'Nenhum produto desta empresa foi encontrado para a leitura ' . $scan . '.', '#product-scan', true); endif; ?>
<?php if (count($matches) > 1): ?><section class="choice-panel"><h2>Mais de um produto corresponde à leitura</h2><p>Selecione o produto correto para continuar.</p><?php foreach ($matches as $match): ?><a class="choice-row" href="<?php echo balanco_h(balanco_url('locacoes.php?d009_id=' . (int) $match['D009_Id'])); ?>"><strong><?php echo balanco_h($match['D001_Codigo_Produto']); ?></strong><span><?php echo balanco_h($match['D001_Descricao_Produto']); ?></span><small><?php echo balanco_h($match['D037_Unidade']); ?> · <?php echo balanco_h($match['D082_Marca']); ?></small></a><?php endforeach; ?></section><?php endif; ?>
<?php balanco_footer();
