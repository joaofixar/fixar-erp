<?php
namespace hardness;

require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/layout.php';

function importador_orcamento_pdf_busca_editor_blank_info()
{
    return array(
        'nome' => '',
        'ativo' => true,
        'prioridade' => 1,
        'valores' => array(),
        '__editor_new' => false,
        '__editor_uid' => uniqid('busca_info_', true),
    );
}

function importador_orcamento_pdf_busca_editor_blank_valor()
{
    return array(
        'canonico' => '',
        'aliases_pdf' => array(),
    );
}

function importador_orcamento_pdf_busca_editor_normalize_aliases($raw)
{
    $lines = preg_split('/\r\n|\r|\n/', (string) $raw);
    if (!is_array($lines)) {
        return array();
    }

    $aliases = array();
    foreach ($lines as $line) {
        $line = trim((string) $line);
        if ($line === '') {
            continue;
        }
        $aliases[] = $line;
    }

    return array_values(array_unique($aliases));
}

function importador_orcamento_pdf_busca_editor_normalize_payload($posted)
{
    $payload = importador_orcamento_pdf_busca_source_default_payload();
    $payload['version'] = 1;
    $payload['catalog_id'] = importador_orcamento_pdf_catalog_id();
    $payload['informacoes_busca'] = array();

    $items = isset($posted['informacoes_busca']) && is_array($posted['informacoes_busca'])
        ? $posted['informacoes_busca']
        : array();

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $info = array(
            'nome' => isset($item['nome']) ? trim((string) $item['nome']) : '',
            'ativo' => !empty($item['ativo']),
            'prioridade' => isset($item['prioridade']) && trim((string) $item['prioridade']) !== ''
                ? (int) $item['prioridade']
                : '',
            'valores' => array(),
            '__editor_uid' => isset($item['__editor_uid']) ? trim((string) $item['__editor_uid']) : uniqid('busca_info_', true),
        );

        $valores = isset($item['valores']) && is_array($item['valores']) ? $item['valores'] : array();
        foreach ($valores as $valor) {
            if (!is_array($valor)) {
                continue;
            }

            $info['valores'][] = array(
                'canonico' => isset($valor['canonico']) ? trim((string) $valor['canonico']) : '',
                'aliases_pdf' => importador_orcamento_pdf_busca_editor_normalize_aliases(isset($valor['aliases_pdf_text']) ? $valor['aliases_pdf_text'] : ''),
            );
        }

        $payload['informacoes_busca'][] = $info;
    }

    return $payload;
}

function importador_orcamento_pdf_busca_editor_info_from_payload($payload)
{
    $infos = array();
    $source = isset($payload['informacoes_busca']) && is_array($payload['informacoes_busca'])
        ? $payload['informacoes_busca']
        : array();

    foreach ($source as $info) {
        if (!is_array($info)) {
            continue;
        }

        $normalized = importador_orcamento_pdf_busca_editor_blank_info();
        $normalized['nome'] = isset($info['nome']) ? trim((string) $info['nome']) : '';
        $normalized['ativo'] = !empty($info['ativo']);
        $normalized['prioridade'] = isset($info['prioridade']) && is_numeric($info['prioridade'])
            ? (int) $info['prioridade']
            : 1;
        $normalized['__editor_new'] = !empty($info['__editor_new']);
        $normalized['__editor_uid'] = isset($info['__editor_uid']) && trim((string) $info['__editor_uid']) !== ''
            ? trim((string) $info['__editor_uid'])
            : uniqid('busca_info_', true);
        $normalized['valores'] = array();

        $valores = isset($info['valores']) && is_array($info['valores']) ? $info['valores'] : array();
        foreach ($valores as $valor) {
            if (!is_array($valor)) {
                continue;
            }

            $normalized['valores'][] = array(
                'canonico' => isset($valor['canonico']) ? trim((string) $valor['canonico']) : '',
                'aliases_pdf' => isset($valor['aliases_pdf']) && is_array($valor['aliases_pdf'])
                    ? array_values(array_filter(array_map('trim', $valor['aliases_pdf']), 'strlen'))
                    : array(),
            );
        }

        $infos[] = $normalized;
    }

    return $infos;
}

function importador_orcamento_pdf_busca_editor_sort_infos(array $infos)
{
    usort($infos, function ($a, $b) {
        $ordemA = isset($a['prioridade']) ? (int) $a['prioridade'] : 999999;
        $ordemB = isset($b['prioridade']) ? (int) $b['prioridade'] : 999999;
        if ($ordemA === $ordemB) {
            $nomeA = isset($a['nome']) ? (string) $a['nome'] : '';
            $nomeB = isset($b['nome']) ? (string) $b['nome'] : '';
            return strcmp($nomeA, $nomeB);
        }
        return $ordemA - $ordemB;
    });

    return array_values($infos);
}

function importador_orcamento_pdf_busca_editor_apply_action($payload)
{
    $infos = importador_orcamento_pdf_busca_editor_info_from_payload($payload);

    if (isset($_POST['add_info'])) {
        $novo = importador_orcamento_pdf_busca_editor_blank_info();
        $novo['__editor_new'] = true;
        $infos[] = $novo;
    }

    if (isset($_POST['remove_info'])) {
        $infoIndex = (int) $_POST['remove_info'];
        if (isset($infos[$infoIndex])) {
            unset($infos[$infoIndex]);
            $infos = array_values($infos);
        }
    }

    if (isset($_POST['add_valor'])) {
        $infoIndex = (int) $_POST['add_valor'];
        if (isset($infos[$infoIndex])) {
            $infos[$infoIndex]['valores'][] = importador_orcamento_pdf_busca_editor_blank_valor();
        }
    }

    if (isset($_POST['remove_valor'])) {
        $parts = explode(':', (string) $_POST['remove_valor']);
        $infoIndex = isset($parts[0]) ? (int) $parts[0] : -1;
        $valorIndex = isset($parts[1]) ? (int) $parts[1] : -1;
        if (isset($infos[$infoIndex]['valores'][$valorIndex])) {
            unset($infos[$infoIndex]['valores'][$valorIndex]);
            $infos[$infoIndex]['valores'] = array_values($infos[$infoIndex]['valores']);
        }
    }

    $payload['informacoes_busca'] = $infos;
    return $payload;
}

function importador_orcamento_pdf_busca_editor_modal_index_from_post($payload, $savedWithSuccess)
{
    $infos = importador_orcamento_pdf_busca_editor_sort_infos(importador_orcamento_pdf_busca_editor_info_from_payload($payload));

    if (isset($_POST['add_info'])) {
        foreach ($infos as $infoIndex => $info) {
            if (!empty($info['__editor_new'])) {
                return (int) $infoIndex;
            }
        }
        return -1;
    }

    if (isset($_POST['current_info_uid']) && trim((string) $_POST['current_info_uid']) !== '') {
        $currentUid = trim((string) $_POST['current_info_uid']);
        foreach ($infos as $infoIndex => $info) {
            if (isset($info['__editor_uid']) && (string) $info['__editor_uid'] === $currentUid) {
                return (int) $infoIndex;
            }
        }
    }

    if (isset($_POST['add_valor'])) {
        return (int) $_POST['add_valor'];
    }

    if (isset($_POST['remove_valor'])) {
        $parts = explode(':', (string) $_POST['remove_valor']);
        return isset($parts[0]) ? (int) $parts[0] : -1;
    }

    return -1;
}

function importador_orcamento_pdf_busca_editor_initial_valor_expand_from_post($payload)
{
    $infos = isset($payload['informacoes_busca']) && is_array($payload['informacoes_busca'])
        ? $payload['informacoes_busca']
        : array();

    if (isset($_POST['add_valor'])) {
        $infoIndex = (int) $_POST['add_valor'];
        if (isset($infos[$infoIndex]['valores']) && is_array($infos[$infoIndex]['valores'])) {
            $lastIndex = count($infos[$infoIndex]['valores']) - 1;
            return $lastIndex >= 0 ? array($infoIndex, $lastIndex) : array(-1, -1);
        }
    }

    return array(-1, -1);
}

importador_orcamento_pdf_require_auth();

$T003_Id = isset($_GET['T003_Id']) ? (int) $_GET['T003_Id'] : 0;
$flashType = '';
$flashMessage = '';
$loadError = '';
$modalInfoIndex = -1;
$initialExpandInfoIndex = -1;
$initialExpandValorIndex = -1;
$paths = importador_orcamento_pdf_busca_global_paths();
$payload = importador_orcamento_pdf_busca_source_load($loadError);

if ($loadError !== '') {
    $payload = importador_orcamento_pdf_busca_source_default_payload();
    $flashType = 'error';
    $flashMessage = $loadError;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = importador_orcamento_pdf_busca_editor_normalize_payload($_POST);
    $payload = importador_orcamento_pdf_busca_editor_apply_action($payload);
    $savedWithSuccess = false;

    if (isset($_POST['save']) || isset($_POST['compile']) || isset($_POST['generate_index'])) {
        $saveError = '';
        if (importador_orcamento_pdf_busca_source_save($payload, $saveError)) {
            $payload = importador_orcamento_pdf_busca_source_load($saveError);
            $savedWithSuccess = ($saveError === '');
            if (isset($_POST['generate_index']) && $saveError === '') {
                $indexError = '';
                $indexPayload = importador_orcamento_pdf_busca_index_generate($indexError);
                if (!empty($indexPayload)) {
                    $flashType = 'success';
                    $flashMessage = 'Produtos do catálogo atualizados com sucesso. '
                        . 'Produtos analisados: ' . (int) $indexPayload['total_produtos']
                        . ' | Valores indexados: ' . (int) $indexPayload['total_valores']
                        . ' | Vínculos gerados: ' . (int) $indexPayload['total_vinculos'];
                } else {
                    $flashType = 'error';
                    $flashMessage = $indexError !== '' ? $indexError : 'Não foi possível atualizar os produtos do catálogo.';
                }
            } elseif (isset($_POST['compile']) && $saveError === '') {
                $compileError = '';
                if (importador_orcamento_pdf_busca_compile($compileError)) {
                    $flashType = 'success';
                    $flashMessage = 'Busca preparada com sucesso.';
                } else {
                    $flashType = 'error';
                    $flashMessage = $compileError !== '' ? $compileError : 'Não foi possível preparar a busca.';
                }
            } else {
                $flashType = $saveError === '' ? 'success' : 'error';
                $flashMessage = $saveError === ''
                    ? 'Configuração global do dicionário de busca salva com sucesso.'
                    : $saveError;
            }
        } else {
            $flashType = 'error';
            $flashMessage = $saveError !== '' ? $saveError : 'Não foi possível salvar a configuração.';
        }
    }

    $modalInfoIndex = importador_orcamento_pdf_busca_editor_modal_index_from_post($payload, $savedWithSuccess);
    list($initialExpandInfoIndex, $initialExpandValorIndex) = importador_orcamento_pdf_busca_editor_initial_valor_expand_from_post($payload);
}

$infos = importador_orcamento_pdf_busca_editor_sort_infos(importador_orcamento_pdf_busca_editor_info_from_payload($payload));
$compiledStatusError = '';
$compiledIsFresh = importador_orcamento_pdf_busca_compiled_is_fresh($compiledStatusError);
$indexStatusError = '';
$indexIsFresh = importador_orcamento_pdf_busca_index_is_fresh($indexStatusError);
$pendingActions = array();
if (!$compiledIsFresh) {
    $pendingActions[] = 'Preparar busca';
}
if (!$indexIsFresh) {
    $pendingActions[] = 'Atualizar produtos do catálogo';
}
$backHref = importador_orcamento_pdf_base_path($T003_Id > 0 ? 'index.php?T003_Id=' . $T003_Id : 'index.php');

importador_orcamento_pdf_header('Configuração Global de Busca', array(
    'subtitle' => 'Dicionário de busca do catálogo',
));
?>
<section class="panel page-wide">
    <div class="section-head">
        <div>
            <h2>Dicionário de Busca Global</h2>
        </div>
        <a class="button" href="<?php echo importador_orcamento_pdf_h($backHref); ?>">Voltar</a>
    </div>
    <p>
        Esta tela edita a configuração global do catálogo usada pelo importador PDF.
        Nesta fase, a busca usa apenas a descrição do produto no catálogo.
    </p>
    <div class="summary" style="margin-top:0; margin-bottom:16px;">
        <div><strong>Como funciona:</strong> cada informação de busca agrupa vários valores do cadastro do catálogo.</div>
        <div><strong>Exemplo:</strong> um bloco pode conter vários valores do cadastro relacionados, cada um com suas variações no PDF.</div>
        <div><strong>Ordem de execução:</strong> menor número executa antes na busca.</div>
    </div>

    <?php if ($flashMessage !== ''): ?>
        <div class="flash <?php echo $flashType === 'success' ? 'flash--success' : 'flash--error'; ?>">
            <?php echo importador_orcamento_pdf_h($flashMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($pendingActions)): ?>
        <div class="summary" style="border-color:#f3d38a; background:#fff8e6; margin-top:16px;">
            <div><strong>Pendências para aplicar as alterações na busca:</strong></div>
            <?php if (!$compiledIsFresh): ?>
                <div>1. <strong>Preparar busca</strong></div>
            <?php endif; ?>
            <?php if (!$indexIsFresh): ?>
                <div><?php echo !$compiledIsFresh ? '2.' : '1.'; ?> <strong>Atualizar produtos do catálogo</strong></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post" data-processing-form="1" data-processing-text="Salvando configuração...">
        <input type="hidden" name="current_info_uid" value="<?php echo ($modalInfoIndex >= 0 && isset($infos[$modalInfoIndex]['__editor_uid'])) ? importador_orcamento_pdf_h($infos[$modalInfoIndex]['__editor_uid']) : ''; ?>" class="js-current-info-uid">
        <div class="actions">
            <button class="button" type="submit" name="add_info" value="1" data-processing-text="Abrindo nova informação de busca...">Adicionar informação de busca</button>
            <button class="button" type="submit" name="compile" value="1" data-processing-text="Preparando busca...">Preparar busca</button>
            <button class="button" type="submit" name="generate_index" value="1" data-processing-text="Atualizando produtos do catálogo...">Atualizar produtos do catálogo</button>
            <a class="button" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path($T003_Id > 0 ? 'testar_preselecao.php?T003_Id=' . $T003_Id : 'testar_preselecao.php')); ?>">Testar busca</a>
        </div>

        <?php if (empty($infos)): ?>
            <div class="panel" style="margin-top:16px; padding:16px;">
                <p style="margin:0;">Nenhuma informação de busca cadastrada.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="grid" style="min-width:0;">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Ativo</th>
                            <th>Ordem</th>
                            <th>Valores no Cadastro</th>
                            <th>Exemplos</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($infos as $infoIndex => $info): ?>
                            <tr>
                                <td><?php echo importador_orcamento_pdf_h($info['nome']); ?></td>
                                <td>
                                    <span class="status <?php echo !empty($info['ativo']) ? 'status--lido' : 'status--erro'; ?>">
                                        <?php echo !empty($info['ativo']) ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </td>
                                <td><?php echo (int) $info['prioridade']; ?></td>
                                <td><?php echo (int) count($info['valores']); ?></td>
                                <td>
                                    <?php
                                    $exemplos = array();
                                    foreach ((array) $info['valores'] as $valorExemplo) {
                                        $canonicoExemplo = isset($valorExemplo['canonico']) ? trim((string) $valorExemplo['canonico']) : '';
                                        if ($canonicoExemplo !== '') {
                                            $exemplos[] = $canonicoExemplo;
                                        }
                                        if (count($exemplos) >= 3) {
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if (empty($exemplos)): ?>
                                        <span class="muted">Nenhum valor configurado</span>
                                    <?php else: ?>
                                        <?php echo importador_orcamento_pdf_h(implode(', ', $exemplos)); ?>
                                        <?php if (count($info['valores']) > count($exemplos)): ?>
                                            <div class="muted">+<?php echo (int) (count($info['valores']) - count($exemplos)); ?> valor(es)</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <div class="row-actions">
                                        <button
                                            class="button button--sm js-open-busca-info-modal"
                                            type="button"
                                            data-modal-index="<?php echo $infoIndex; ?>"
                                        >Editar</button>
                                        <button
                                            class="button button--sm"
                                            type="submit"
                                            name="remove_info"
                                            value="<?php echo $infoIndex; ?>"
                                            onclick="return confirm('Remover esta informação de busca?');"
                                        >Remover</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php foreach ($infos as $infoIndex => $info): ?>
            <div
                id="busca-info-modal-<?php echo $infoIndex; ?>"
                class="modal js-busca-info-modal"
                data-modal-index="<?php echo $infoIndex; ?>"
                style="display:none;"
            >
                <div class="modal__backdrop js-busca-info-modal-close"></div>
                <div class="modal__panel">
                    <div class="modal__head">
                        <div>
                            <strong>Informação de busca</strong>
                            <div class="muted" style="margin-top:4px;">Este bloco agrupa vários valores no cadastro e suas variações no PDF dentro da mesma categoria.</div>
                        </div>
                    </div>
                    <div class="modal__body">
                        <?php if ($flashMessage !== '' && $flashType === 'success' && $modalInfoIndex === $infoIndex && isset($_POST['save'])): ?>
                            <div class="flash flash--success" style="margin-bottom:16px;">
                                <?php echo importador_orcamento_pdf_h($flashMessage); ?>
                            </div>
                        <?php endif; ?>

                        <table class="grid" style="min-width:0;">
                            <tbody>
                                <tr style="display:none;">
                                    <td colspan="4">
                                        <input type="hidden" name="informacoes_busca[<?php echo $infoIndex; ?>][__editor_uid]" value="<?php echo importador_orcamento_pdf_h($info['__editor_uid']); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:18%;"><strong>Nome</strong></td>
                                    <td colspan="3"><input class="input-inline" type="text" name="informacoes_busca[<?php echo $infoIndex; ?>][nome]" value="<?php echo importador_orcamento_pdf_h($info['nome']); ?>"></td>
                                </tr>
                                <tr>
                                    <td><strong>Ativo</strong></td>
                                    <td>
                                        <input type="hidden" name="informacoes_busca[<?php echo $infoIndex; ?>][ativo]" value="0">
                                        <label>
                                            <input type="checkbox" name="informacoes_busca[<?php echo $infoIndex; ?>][ativo]" value="1" <?php echo !empty($info['ativo']) ? 'checked' : ''; ?>>
                                            Habilitado
                                        </label>
                                    </td>
                                    <td style="width:18%;"><strong>Ordem</strong></td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                            <input class="input-inline" style="max-width:120px;" type="number" min="1" step="1" name="informacoes_busca[<?php echo $infoIndex; ?>][prioridade]" value="<?php echo importador_orcamento_pdf_h($info['prioridade']); ?>">
                                            <span class="muted">Menor numero executa antes.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="summary">
                            <div><strong>Categoria:</strong> use este bloco para organizar uma familia de valores do catalogo.</div>
                            <div><strong>Exemplo:</strong> um bloco pode conter varios valores no cadastro, cada um com suas formas de aparecer no PDF.</div>
                        </div>

                        <div class="modal-edit-actions">
                            <button class="button button--primary" type="submit" name="save" value="1" data-processing-text="Salvando configuração global...">Salvar configuração</button>
                            <button class="button js-busca-info-modal-close" type="button">Fechar</button>
                        </div>

                        <div class="actions actions--tight" style="margin-top:8px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <button class="button button--sm" type="submit" name="add_valor" value="<?php echo $infoIndex; ?>" data-processing-text="Adicionando valor no cadastro...">Adicionar valor no cadastro</button>
                            <input
                                type="text"
                                class="input-inline js-busca-valor-filter"
                                data-info-index="<?php echo $infoIndex; ?>"
                                placeholder="Filtrar por valor no cadastro ou texto do PDF"
                                style="flex:1 1 320px;"
                            >
                        </div>

                        <?php if (empty($info['valores'])): ?>
                            <div class="muted">Nenhum valor no cadastro cadastrado nesta informação.</div>
                        <?php else: ?>
                            <?php foreach ($info['valores'] as $valorIndex => $valor): ?>
                                <?php
                                $aliasesPreview = array_slice(isset($valor['aliases_pdf']) && is_array($valor['aliases_pdf']) ? $valor['aliases_pdf'] : array(), 0, 2);
                                $aliasesCount = isset($valor['aliases_pdf']) && is_array($valor['aliases_pdf']) ? count($valor['aliases_pdf']) : 0;
                                $searchBlob = trim(
                                    (isset($valor['canonico']) ? (string) $valor['canonico'] : '')
                                    . ' '
                                    . implode(' ', isset($valor['aliases_pdf']) && is_array($valor['aliases_pdf']) ? $valor['aliases_pdf'] : array())
                                );
                                $initialExpand = ($initialExpandInfoIndex === $infoIndex && $initialExpandValorIndex === $valorIndex);
                                ?>
                                <div
                                    class="panel js-valor-card"
                                    data-search="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_busca_normalize_alias($searchBlob)); ?>"
                                    style="padding:16px;"
                                >
                                    <div class="section-head">
                                        <div>
                                            <h2 style="margin:0 0 6px; font-size:16px;"><?php echo importador_orcamento_pdf_h(isset($valor['canonico']) ? $valor['canonico'] : ('Valor no cadastro ' . ($valorIndex + 1))); ?></h2>
                                            <div class="muted">
                                                Como pode vir no PDF: <?php echo (int) $aliasesCount; ?>
                                                <?php if (!empty($aliasesPreview)): ?>
                                                    · <?php echo importador_orcamento_pdf_h(implode(', ', $aliasesPreview)); ?>
                                                    <?php if ($aliasesCount > count($aliasesPreview)): ?>
                                                        ...
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="row-actions">
                                            <button
                                                class="button button--sm js-open-valor-modal"
                                                type="button"
                                                data-info-index="<?php echo $infoIndex; ?>"
                                                data-valor-index="<?php echo $valorIndex; ?>"
                                            >Editar</button>
                                            <button class="button button--sm" type="submit" name="remove_valor" value="<?php echo $infoIndex . ':' . $valorIndex; ?>" onclick="return confirm('Remover este valor no cadastro e suas variações do PDF?');" data-processing-text="Removendo valor no cadastro...">Remover valor</button>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    id="busca-valor-modal-<?php echo $infoIndex; ?>-<?php echo $valorIndex; ?>"
                                    class="modal js-busca-valor-modal"
                                    style="display:<?php echo $initialExpand ? 'flex' : 'none'; ?>; align-items:center; justify-content:center; padding:24px;"
                                >
                                    <div class="modal__backdrop js-busca-valor-modal-close"></div>
                                    <div class="modal__panel" style="width:min(980px, 94vw); max-height:94vh; margin:0;">
                                        <div class="modal__head">
                                            <div>
                                                <strong>Editar valor no cadastro</strong>
                                                <div class="muted" style="margin-top:4px;">
                                                    Ajuste o valor no cadastro e como ele pode aparecer no PDF dentro de <?php echo importador_orcamento_pdf_h($info['nome']); ?>.
                                                </div>
                                            </div>
                                            <button type="button" class="button button--sm js-busca-valor-modal-close">Fechar</button>
                                        </div>
                                        <div class="modal__body">
                                            <table class="grid" style="min-width:0;">
                                                <tbody>
                                                    <tr>
                                                        <td style="width:22%;"><strong>Valor no cadastro</strong></td>
                                                        <td>
                                                            <input class="input-inline" type="text" name="informacoes_busca[<?php echo $infoIndex; ?>][valores][<?php echo $valorIndex; ?>][canonico]" value="<?php echo importador_orcamento_pdf_h($valor['canonico']); ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Como pode vir no PDF</strong></td>
                                                        <td>
                                                            <textarea class="input-inline" style="min-height:320px;" name="informacoes_busca[<?php echo $infoIndex; ?>][valores][<?php echo $valorIndex; ?>][aliases_pdf_text]"><?php echo importador_orcamento_pdf_h(implode("\n", $valor['aliases_pdf'])); ?></textarea>
                                                            <div class="muted">Uma variação por linha.</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="modal-edit-actions">
                                                <button class="button button--primary" type="submit" name="save" value="1" data-processing-text="Salvando configuração global...">Salvar configuração</button>
                                                <button type="button" class="button js-busca-valor-modal-close">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</section>
<script>
(function () {
    'use strict';

    function qsa(scope, sel) { return Array.prototype.slice.call(scope.querySelectorAll(sel)); }
    function qs(scope, sel) { return scope.querySelector(sel); }

    var currentModalInput = qs(document, '.js-current-info-uid');
    var currentModal = null;
    var initialModalIndex = <?php echo json_encode($modalInfoIndex); ?>;

    function closeModal() {
        if (!currentModal) { return; }
        currentModal.style.display = 'none';
        currentModal = null;
        if (currentModalInput) {
            currentModalInput.value = '';
        }
    }

    function openModal(index) {
        closeModal();
        currentModal = document.getElementById('busca-info-modal-' + index);
        if (!currentModal) { return; }
        currentModal.style.display = 'block';
        if (currentModalInput) {
            var uidField = qs(currentModal, 'input[name$="[__editor_uid]"]');
            currentModalInput.value = uidField ? uidField.value : '';
        }
    }

    qsa(document, '.js-open-busca-info-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            openModal(button.getAttribute('data-modal-index'));
        });
    });

    qsa(document, '.js-busca-info-modal-close').forEach(function (button) {
        button.addEventListener('click', closeModal);
    });

    function closeValorModal() {
        qsa(document, '.js-busca-valor-modal').forEach(function (modal) {
            modal.style.display = 'none';
        });
    }

    function openValorModal(infoIndex, valorIndex) {
        closeValorModal();
        var modal = document.getElementById('busca-valor-modal-' + infoIndex + '-' + valorIndex);
        if (!modal) { return; }
        modal.style.display = 'flex';
    }

    qsa(document, '.js-open-valor-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            openValorModal(button.getAttribute('data-info-index'), button.getAttribute('data-valor-index'));
        });
    });

    qsa(document, '.js-busca-valor-modal-close').forEach(function (button) {
        button.addEventListener('click', function () {
            closeValorModal();
        });
    });

    qsa(document, '.js-busca-valor-filter').forEach(function (input) {
        input.addEventListener('input', function () {
            var modal = input.closest('.js-busca-info-modal');
            if (!modal) { return; }
            var query = (input.value || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/\s+/g, ' ')
                .trim();
            qsa(modal, '.js-valor-card').forEach(function (card) {
                var haystack = card.getAttribute('data-search') || '';
                card.style.display = query === '' || haystack.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    });

    if (typeof initialModalIndex === 'number' && initialModalIndex >= 0) {
        openModal(initialModalIndex);
    }

    if (typeof <?php echo json_encode($initialExpandInfoIndex); ?> === 'number'
        && typeof <?php echo json_encode($initialExpandValorIndex); ?> === 'number'
        && <?php echo json_encode($initialExpandInfoIndex); ?> >= 0
        && <?php echo json_encode($initialExpandValorIndex); ?> >= 0) {
        openValorModal(<?php echo json_encode($initialExpandInfoIndex); ?>, <?php echo json_encode($initialExpandValorIndex); ?>);
    }
}());
</script>
<?php importador_orcamento_pdf_footer(); ?>
