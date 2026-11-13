<?php
namespace hardness;

require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/layout.php';

function importador_orcamento_pdf_cache_paths_index($baseDirImportacao)
{
    $moduleBase = dirname(dirname((string) $baseDirImportacao));
    $cacheDir = $moduleBase . '/cache';
    return array(
        'dir' => $cacheDir,
        'final' => $cacheDir . '/produtos_cache.json',
        'index' => $cacheDir . '/produtos_index.json',
        'lock' => $cacheDir . '/produtos_cache.lock',
    );
}

function importador_orcamento_pdf_load_produtos_cache_meta_index($cachePath)
{
    if (!is_file($cachePath)) {
        return array();
    }
    $fh = @fopen($cachePath, 'rb');
    if (!$fh) {
        return array();
    }
    $head = @fread($fh, 8192);
    @fclose($fh);
    if (!is_string($head) || $head === '') {
        return array();
    }
    $meta = array();
    if (preg_match('/"gerado_em"\s*:\s*"([^"]+)"/', $head, $m)) {
        $meta['gerado_em'] = $m[1];
    }
    if (preg_match('/"total"\s*:\s*(\d+)/', $head, $m)) {
        $meta['total'] = (int) $m[1];
    }
    if (preg_match('/"parser_version"\s*:\s*"([^"]+)"/', $head, $m)) {
        $meta['parser_version'] = $m[1];
    }
    return $meta;
}

function importador_orcamento_pdf_load_produtos_index_meta_index($indexPath)
{
    if (!is_file($indexPath)) {
        return array();
    }
    $fh = @fopen($indexPath, 'rb');
    if (!$fh) {
        return array();
    }
    $head = @fread($fh, 8192);
    @fclose($fh);
    if (!is_string($head) || $head === '') {
        return array();
    }
    $meta = array();
    if (preg_match('/"total"\s*:\s*(\d+)/', $head, $m)) {
        $meta['total'] = (int) $m[1];
    }
    if (preg_match('/"parser_version"\s*:\s*"([^"]+)"/', $head, $m)) {
        $meta['parser_version'] = $m[1];
    }
    return $meta;
}

function importador_orcamento_pdf_cache_lock_busy_index($lockPath)
{
    $lockPath = trim((string) $lockPath);
    if ($lockPath === '') {
        return false;
    }
    $dir = dirname($lockPath);
    if (!is_dir($dir)) {
        return false;
    }
    $fp = @fopen($lockPath, 'c');
    if (!$fp) {
        return false;
    }
    $locked = !@flock($fp, LOCK_EX | LOCK_NB);
    if (!$locked) {
        @flock($fp, LOCK_UN);
    }
    @fclose($fp);
    return $locked;
}

$T003_Id = isset($_GET['T003_Id']) ? (int) $_GET['T003_Id'] : 0;
list($ok, $message, $orcamento) = importador_orcamento_pdf_validar_orcamento_editavel($T003_Id);
$podeGerenciarBusca = importador_orcamento_pdf_pode_gerenciar_configuracao_catalogo();

$cacheMeta = array();
$cacheBusy = false;
$cacheExpired = false;
$cacheStatusText = '';
$cacheStatusShow = false;
if ($ok && is_array($orcamento) && isset($orcamento['T003_D024_Id'])) {
    $baseDir = importador_orcamento_pdf_fs_orcamento_dir_existing((int) $orcamento['T003_D024_Id'], $T003_Id);
    $cachePaths = importador_orcamento_pdf_cache_paths_index($baseDir);
    $cacheMeta = importador_orcamento_pdf_load_produtos_cache_meta_index($cachePaths['final']);
    $indexMeta = importador_orcamento_pdf_load_produtos_index_meta_index($cachePaths['index']);
    if (!isset($cacheMeta['total']) || (int) $cacheMeta['total'] <= 0) {
        $cacheMeta['total'] = isset($indexMeta['total']) ? (int) $indexMeta['total'] : 0;
    }
    $cacheBusy = importador_orcamento_pdf_cache_lock_busy_index($cachePaths['lock']);

    $cacheTimestamp = isset($cacheMeta['gerado_em']) ? strtotime((string) $cacheMeta['gerado_em']) : false;
    if ($cacheTimestamp === false || $cacheTimestamp <= 0) {
        $cacheTimestamp = is_file($cachePaths['final']) ? @filemtime($cachePaths['final']) : false;
    }
    $cacheExpired = empty($cacheMeta) || $cacheTimestamp === false || $cacheTimestamp <= 0 || ((time() - (int) $cacheTimestamp) >= 43200);

    if (!empty($cacheMeta)) {
        if ($cacheBusy) {
            $cacheStatusText = 'Atualização de produtos em andamento.';
            $cacheStatusShow = true;
        } elseif ($cacheExpired) {
            $cacheStatusText = 'Atualizar produtos?';
            if (!empty($cacheMeta['gerado_em'])) {
                $cacheStatusText .= ' Última atualização: ' . (string) gCorrigeData($cacheMeta['gerado_em']);
            }
            $cacheStatusShow = true;
        }
    } else {
        $cacheStatusText = $cacheBusy
            ? 'Cache de produtos ainda não disponível. Há uma atualização em andamento.'
            : 'Cache de produtos ainda não gerado.';
        $cacheStatusShow = true;
    }
}

importador_orcamento_pdf_header('Importar PDF do cliente', array(
    'subtitle' => $T003_Id > 0 ? 'Orçamento ' . $T003_Id : '',
));
?>
<?php if (!$ok): ?>
    <section class="panel panel--narrow">
        <h2>Acesso indisponivel</h2>
        <p><?php echo importador_orcamento_pdf_h($message); ?></p>
    </section>
<?php else: ?>
    <section class="panel">
        <div class="section-head">
            <div>
                <span class="badge">Importador PDF</span>
                <h2>Importacao de itens do orçamento</h2>
            </div>
            <strong>Cliente <?php echo importador_orcamento_pdf_h($orcamento['T003_D024_Id']); ?></strong>
        </div>
        <p>
            Utilize este processo para importar itens de PDF no orçamento com segurança e rastreabilidade.
            O fluxo realiza upload do arquivo, extração dos itens, revisão assistida e inclusão no orçamento.
        </p>
        <?php if ($cacheStatusShow): ?>
            <div class="review-hint" style="margin-bottom:16px;">
                <span><?php echo importador_orcamento_pdf_h($cacheStatusText); ?></span>
            </div>
        <?php endif; ?>
        <div class="actions">
            <a class="button button--primary" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('upload.php?T003_Id=' . $T003_Id)); ?>">Iniciar upload</a>
            <a class="button" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>">Revisão</a>
            <a class="button" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('configurar_busca.php?T003_Id=' . $T003_Id)); ?>">Configuração de busca</a>
            <?php if ($podeGerenciarBusca): ?>
                <a class="button" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('configurar_base_conhecimento.php?T003_Id=' . $T003_Id)); ?>">Base de Conhecimento</a>
            <?php endif; ?>
            <?php if ($podeGerenciarBusca): ?>
                <form method="post" class="inline-form" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>" onsubmit="return confirm('Confirmar atualização dos produtos? Esta ação irá reconstruir o cache do catálogo para o importador PDF e pode levar alguns segundos.');">
                    <input type="hidden" name="action" value="gerar_cache_produtos">
                    <button class="button" type="submit" <?php echo $cacheBusy ? 'disabled' : ''; ?>>Atualizar produtos</button>
                </form>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>
<?php importador_orcamento_pdf_footer(); ?>
