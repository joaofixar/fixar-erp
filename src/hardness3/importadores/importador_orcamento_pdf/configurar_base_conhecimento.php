<?php
namespace hardness;

require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/base_conhecimento/BaseConhecimentoRepository.php';

importador_orcamento_pdf_require_auth();

if (!importador_orcamento_pdf_pode_gerenciar_configuracao_catalogo()) {
    header('HTTP/1.1 403 Forbidden');
    importador_orcamento_pdf_header('Base de Conhecimento');
    ?>
    <section class="panel panel--narrow">
        <h2>Acesso indisponível</h2>
        <p>Seu usuário não possui permissão para gerenciar a Base de Conhecimento.</p>
    </section>
    <?php
    importador_orcamento_pdf_footer();
    exit;
}

$T003_Id = isset($_GET['T003_Id']) ? (int) $_GET['T003_Id'] : 0;
$repository = new BaseConhecimentoRepository();

$downloadHash = isset($_GET['download']) ? (string) $_GET['download'] : '';
if ($downloadHash !== '') {
    $downloadError = '';
    $downloadContent = $repository->getVersionContent($downloadHash, $downloadError);
    if ($downloadError !== '') {
        header('HTTP/1.1 404 Not Found');
        exit('Versão da Base de Conhecimento não encontrada.');
    }

    $downloadFilename = 'base-conhecimento-' . substr($downloadHash, 0, 12) . '.md';
    header('Content-Type: text/markdown; charset=UTF-8');
    header('Content-Length: ' . strlen($downloadContent));
    header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
    header('X-Content-Type-Options: nosniff');
    echo $downloadContent;
    exit;
}

$flashMessage = '';
$flashType = 'info';
$csrfToken = importador_orcamento_pdf_csrf_token('base_conhecimento');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string) $_POST['action'] : '';
    $error = '';

    if (!importador_orcamento_pdf_csrf_valido('base_conhecimento', isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
        $flashType = 'error';
        $flashMessage = 'Não foi possível validar a solicitação. Atualize a página e tente novamente.';
    } elseif ($action === 'upload') {
        $metadata = $repository->saveUploadedVersion(isset($_FILES['arquivo_markdown']) ? $_FILES['arquivo_markdown'] : null, $error);
        if (!empty($metadata)) {
            $flashType = 'success';
            $flashMessage = !empty($metadata['already_exists'])
                ? 'Esta versão já estava cadastrada. Nenhum arquivo foi sobrescrito.'
                : 'Nova versão da Base de Conhecimento salva. Ative-a quando estiver pronta para uso.';
        } else {
            $flashType = 'error';
            $flashMessage = $error !== '' ? $error : 'Não foi possível salvar a versão.';
        }
    } elseif ($action === 'activate') {
        $hash = isset($_POST['hash']) ? (string) $_POST['hash'] : '';
        if ($repository->activateVersion($hash, $error)) {
            $flashType = 'success';
            $flashMessage = 'Versão ativada com sucesso.';
        } else {
            $flashType = 'error';
            $flashMessage = $error !== '' ? $error : 'Não foi possível ativar a versão.';
        }
    } elseif ($action === 'consolidate_activate') {
        $selected = isset($_POST['hashes']) && is_array($_POST['hashes']) ? $_POST['hashes'] : array();
        $priorities = isset($_POST['priority']) && is_array($_POST['priority']) ? $_POST['priority'] : array();
        $selectedClean = array();
        foreach ($selected as $selectedHash) {
            if (is_string($selectedHash) && trim($selectedHash) !== '') {
                $selectedClean[] = strtolower(trim($selectedHash));
            }
        }
        $selected = array_values(array_unique($selectedClean));
        usort($selected, function ($a, $b) use ($priorities) {
            $pa = isset($priorities[$a]) ? (int) $priorities[$a] : PHP_INT_MAX;
            $pb = isset($priorities[$b]) ? (int) $priorities[$b] : PHP_INT_MAX;
            return $pa === $pb ? strcmp($a, $b) : ($pa < $pb ? -1 : 1);
        });
        if ($repository->activateVersions($selected, $error)) {
            $flashType = 'success';
            $flashMessage = 'Base consolidada e ativada com ' . count($selected) . ' arquivo(s).';
        } else {
            $flashType = 'error';
            $flashMessage = $error !== '' ? $error : 'Não foi possível consolidar a Base de Conhecimento.';
        }
    } elseif ($action === 'replace_active') {
        $oldHash = isset($_POST['hash']) ? (string) $_POST['hash'] : '';
        if ($repository->replaceActiveVersion($oldHash, isset($_FILES['arquivo_markdown']) ? $_FILES['arquivo_markdown'] : null, $error)) {
            $flashType = 'success';
            $flashMessage = 'Arquivo substituído e Base consolidada novamente, preservando a prioridade.';
        } else {
            $flashType = 'error';
            $flashMessage = $error !== '' ? $error : 'Não foi possível substituir o arquivo ativo.';
        }
    } elseif ($action === 'deactivate') {
        if ($repository->deactivate($error)) {
            $flashType = 'success';
            $flashMessage = 'Base de Conhecimento desativada. Nenhuma versão foi excluída.';
        } else {
            $flashType = 'error';
            $flashMessage = $error !== '' ? $error : 'Não foi possível desativar a Base de Conhecimento.';
        }
    }
}

$activeError = '';
$active = $repository->getActiveSnapshot($activeError);
$versionsError = '';
$versions = $repository->listVersions($versionsError);
$activeSources = !empty($active) && isset($active['sources']) && is_array($active['sources']) ? $active['sources'] : array();
$activePriorityByHash = array();
foreach ($activeSources as $source) {
    if (isset($source['hash'])) {
        $activePriorityByHash[(string) $source['hash']] = isset($source['priority']) ? (int) $source['priority'] : count($activePriorityByHash) + 1;
    }
}
// Deixa visível a mesma ordem que será enviada para a IA. Versões ainda fora
// da Base ficam depois das selecionadas, mantendo a listagem previsível.
usort($versions, function ($a, $b) use ($activePriorityByHash) {
    $hashA = isset($a['sha256']) ? (string) $a['sha256'] : '';
    $hashB = isset($b['sha256']) ? (string) $b['sha256'] : '';
    $priorityA = isset($activePriorityByHash[$hashA]) ? (int) $activePriorityByHash[$hashA] : PHP_INT_MAX;
    $priorityB = isset($activePriorityByHash[$hashB]) ? (int) $activePriorityByHash[$hashB] : PHP_INT_MAX;
    if ($priorityA !== $priorityB) {
        return $priorityA < $priorityB ? -1 : 1;
    }
    $createdA = isset($a['created_at']) ? (string) $a['created_at'] : '';
    $createdB = isset($b['created_at']) ? (string) $b['created_at'] : '';
    if ($createdA !== $createdB) {
        return strcmp($createdB, $createdA);
    }
    return strcmp((string) ($a['original_name'] ?? ''), (string) ($b['original_name'] ?? ''));
});

$backHref = importador_orcamento_pdf_base_path($T003_Id > 0 ? 'index.php?T003_Id=' . $T003_Id : 'index.php');
importador_orcamento_pdf_header('Base de Conhecimento', array('subtitle' => 'Catálogo ' . importador_orcamento_pdf_catalog_id()));
?>
<section class="panel page-wide">
    <div class="section-head">
        <div>
            <span class="badge">Configuração do catálogo</span>
            <h2>Base de Conhecimento</h2>
        </div>
        <a class="button" href="<?php echo importador_orcamento_pdf_h($backHref); ?>">Voltar</a>
    </div>
    <p class="muted">Uma Base por catálogo, compartilhada entre as empresas desta base.</p>
    <p class="muted" style="margin-top:12px;"><strong>Fase opcional — não é obrigatória nem precisa ser usada em todos os orçamentos.</strong> Selecione e ordene os arquivos Markdown (<code>.md</code>) e depois consolide a Base. A IA recebe somente o conteúdo consolidado; em caso de conflito, o arquivo de menor prioridade numérica vem primeiro.</p>

    <?php if ($flashMessage !== ''): ?>
        <p class="flash flash--<?php echo importador_orcamento_pdf_h($flashType); ?>"><?php echo importador_orcamento_pdf_h($flashMessage); ?></p>
    <?php endif; ?>
    <?php if ($versionsError !== ''): ?>
        <p class="flash flash--error"><?php echo importador_orcamento_pdf_h($versionsError); ?></p>
    <?php endif; ?>
    <?php if ($activeError !== ''): ?>
        <p class="flash flash--error"><?php echo importador_orcamento_pdf_h($activeError); ?></p>
    <?php endif; ?>

    <div class="review-hint" style="margin-top:16px;">
        <?php if (!empty($active)): ?>
            <span><strong>Base ativa:</strong> <?php echo importador_orcamento_pdf_h(isset($active['original_name']) ? $active['original_name'] : ''); ?><?php echo !empty($active['content_size_bytes']) ? ' (' . (int) $active['content_size_bytes'] . ' bytes)' : ''; ?></span>
            <form method="post" class="inline-form" onsubmit="return confirm('Desativar a Base de Conhecimento? As versões cadastradas serão mantidas.');">
                <input type="hidden" name="action" value="deactivate">
                <input type="hidden" name="csrf_token" value="<?php echo importador_orcamento_pdf_h($csrfToken); ?>">
                <button class="button button--sm button--danger-soft" type="submit">Desativar</button>
            </form>
        <?php else: ?>
            <span>Nenhuma Base ativa.</span>
        <?php endif; ?>
    </div>

    <?php if (!empty($activeSources)): ?>
        <h3 style="margin-top:20px;">Arquivos da Base ativa</h3>
        <div class="table-wrap"><table class="grid"><thead><tr><th>Prioridade</th><th>Arquivo</th><th>Substituir</th></tr></thead><tbody>
        <?php foreach ($activeSources as $source): ?>
            <tr><td><?php echo (int) $source['priority']; ?></td><td><?php echo importador_orcamento_pdf_h($source['original_name']); ?></td><td>
                <form method="post" enctype="multipart/form-data" class="inline-form" data-processing-form="1" data-processing-text="Substituindo e consolidando Base...">
                    <input type="hidden" name="action" value="replace_active"><input type="hidden" name="csrf_token" value="<?php echo importador_orcamento_pdf_h($csrfToken); ?>"><input type="hidden" name="hash" value="<?php echo importador_orcamento_pdf_h($source['hash']); ?>">
                    <input type="file" name="arquivo_markdown" accept="text/markdown,.md" required><button class="button button--sm" type="submit">Substituir</button>
                </form>
            </td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>

    <form class="upload-box" method="post" enctype="multipart/form-data" style="margin-top:16px;" data-processing-form="1" data-processing-text="Salvando versão da Base de Conhecimento...">
        <input type="hidden" name="action" value="upload">
        <input type="hidden" name="csrf_token" value="<?php echo importador_orcamento_pdf_h($csrfToken); ?>">
        <label for="arquivo_markdown"><strong>Nova versão Markdown</strong></label>
        <input id="arquivo_markdown" type="file" name="arquivo_markdown" accept="text/markdown,.md" required>
        <p class="muted">Envie um arquivo .md. O envio não ativa a versão automaticamente.</p>
        <button class="button button--primary" type="submit">Enviar nova versão</button>
    </form>

    <h2 style="margin-top:24px;">Arquivos disponíveis para consolidação</h2>
    <?php if (empty($versions)): ?>
        <p class="muted">Nenhuma versão cadastrada nesta base.</p>
    <?php else: ?>
        <form method="post" data-processing-form="1" data-processing-text="Consolidando Base de Conhecimento...">
            <input type="hidden" name="action" value="consolidate_activate"><input type="hidden" name="csrf_token" value="<?php echo importador_orcamento_pdf_h($csrfToken); ?>">
            <p class="muted">Marque os arquivos que compõem a Base e informe a prioridade. Prioridade 1 tem precedência em conflitos.</p>
            <div class="table-wrap"><table class="grid">
                <thead>
                    <tr>
                        <th>Usar</th><th>Prioridade</th>
                        <th>Arquivo</th>
                        <th>Enviada em</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($versions as $version): ?>
                    <?php
                    $hash = isset($version['sha256']) ? (string) $version['sha256'] : '';
                    $isActive = !empty($active) && $hash !== '' && isset($active['document_sha256']) && hash_equals((string) $active['document_sha256'], $hash);
                    $isInActiveCollection = isset($activePriorityByHash[$hash]);
                    ?>
                    <tr>
                        <td><?php if (!empty($version['valid']) && $hash !== ''): ?><input type="checkbox" name="hashes[]" value="<?php echo importador_orcamento_pdf_h($hash); ?>" <?php echo $isInActiveCollection || $isActive ? 'checked' : ''; ?>><?php endif; ?></td>
                        <td><?php if (!empty($version['valid']) && $hash !== ''): ?><input type="number" min="1" name="priority[<?php echo importador_orcamento_pdf_h($hash); ?>]" value="<?php echo $isInActiveCollection ? (int) $activePriorityByHash[$hash] : ''; ?>" style="width:70px;"><?php endif; ?></td>
                        <td><?php echo importador_orcamento_pdf_h(isset($version['original_name']) ? $version['original_name'] : 'Versão inválida'); ?></td>
                        <td><?php echo importador_orcamento_pdf_h(isset($version['created_at']) ? date('d/m/Y H:i', strtotime((string) $version['created_at'])) : ''); ?></td>
                        <td>
                            <?php if (empty($version['valid'])): ?>
                                <span class="status status--erro">Inválida</span>
                            <?php elseif ($isActive): ?>
                                <span class="status status--lido">Ativa</span>
                            <?php else: ?>
                                <span class="status">Disponível</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions-cell">
                            <div class="row-actions">
                                <?php if (!empty($version['valid']) && $hash !== ''): ?>
                                    <a class="button button--sm" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('configurar_base_conhecimento.php' . ($T003_Id > 0 ? '?T003_Id=' . $T003_Id . '&download=' . rawurlencode($hash) : '?download=' . rawurlencode($hash)))); ?>">Baixar</a>
                                <?php else: ?>
                                    <span class="muted"><?php echo importador_orcamento_pdf_h(isset($version['validation_error']) ? $version['validation_error'] : 'Versão inválida'); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </table></div>
            <button class="button button--primary" type="submit" style="margin-top:12px;">Consolidar e ativar Base</button>
        </form>
    <?php endif; ?>

</section>
<?php importador_orcamento_pdf_footer(); ?>
