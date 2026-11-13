<?php
namespace hardness;

ini_set('memory_limit', '512M'); 
set_time_limit(0); // remove limite de tempo

require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/enrichment/ImportEnrichmentService.php';
require_once __DIR__ . '/inc/enrichment/ImportEnrichmentStepService.php';

$T003_Id = isset($_GET['T003_Id']) ? (int) $_GET['T003_Id'] : 0;
list($ok, $message, $orcamento) = importador_orcamento_pdf_validar_orcamento_editavel($T003_Id);

$rows = array();
$summary = array(
    'total_items' => 0,
    'lidos' => 0,
    'incompletos_ou_erro' => 0,
    'sem_vinculo' => 0,
    'confirmados' => 0,
);
$loadMessage = '';
$flashMessage = '';
$flashType = 'info';
$baseKnowledgeSnapshot = array();
$baseKnowledgeError = '';
$baseKnowledgeActive = false;
$enrichmentUi = array('status' => 'not_applied', 'attention_items' => 0, 'processed_items' => 0, 'duration_ms' => 0, 'message' => '', 'error_type' => '');

function importador_orcamento_pdf_revisao_flash_set($message, $type)
{
    if (!isset($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]) || !is_array($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION])) {
        $_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION] = array();
    }
    $_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['revisao_flash'] = array(
        'message' => (string) $message,
        'type' => (string) $type,
    );
}

function importador_orcamento_pdf_revisao_flash_pull()
{
    if (!isset($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['revisao_flash']) || !is_array($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['revisao_flash'])) {
        return array('', 'info');
    }

    $flash = $_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['revisao_flash'];
    unset($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['revisao_flash']);

    return array(
        isset($flash['message']) ? (string) $flash['message'] : '',
        isset($flash['type']) ? (string) $flash['type'] : 'info',
    );
}

function importador_orcamento_pdf_load_json_file($path)
{
    if (!is_file($path)) {
        return array();
    }
    $json = json_decode((string) @file_get_contents($path), true);
    return is_array($json) ? $json : array();
}

function importador_orcamento_pdf_normalize_debug_snapshot($value)
{
    $value = (string) $value;
    return array(
        'text' => $value,
        'hex' => bin2hex($value),
        'mb_check_encoding_utf8' => function_exists('mb_check_encoding') ? mb_check_encoding($value, 'UTF-8') : null,
    );
}

function importador_orcamento_pdf_normalize_debug_targets()
{
    return array(
        'divis',
        'redu',
        'uni',
        'conex',
        'pré-z',
        'pre-z',
        'pr zinc',
    );
}

function importador_orcamento_pdf_normalize_debug_should_trace($value)
{
    if (!importador_orcamento_pdf_debug_normalize_enabled(false)) {
        return false;
    }

    $value = (string) $value;
    if ($value === '') {
        return false;
    }

    foreach (importador_orcamento_pdf_normalize_debug_targets() as $needle) {
        if (mb_stripos($value, $needle, 0, 'UTF-8') !== false) {
            return true;
        }
    }

    return false;
}

function importador_orcamento_pdf_normalize_debug_init($path)
{
    $path = trim((string) $path);
    $GLOBALS['importador_orcamento_pdf_normalize_debug'] = array(
        'path' => $path,
        'events' => array(),
        'counts' => array(),
        'enabled' => importador_orcamento_pdf_debug_normalize_enabled(false) && $path !== '',
        'max_per_key' => 12,
    );

    if (!empty($GLOBALS['importador_orcamento_pdf_normalize_debug']['enabled']) && is_file($path)) {
        @unlink($path);
    }
}

function importador_orcamento_pdf_normalize_debug_append($type, array $payload)
{
    $state = isset($GLOBALS['importador_orcamento_pdf_normalize_debug']) && is_array($GLOBALS['importador_orcamento_pdf_normalize_debug'])
        ? $GLOBALS['importador_orcamento_pdf_normalize_debug']
        : array();
    if (empty($state['enabled'])) {
        return;
    }

    $inputText = '';
    if (isset($payload['input']['text'])) {
        $inputText = (string) $payload['input']['text'];
    } elseif (isset($payload['descricao'])) {
        $inputText = (string) $payload['descricao'];
    } elseif (isset($payload['texto'])) {
        $inputText = (string) $payload['texto'];
    }

    if ($inputText !== '' && !importador_orcamento_pdf_normalize_debug_should_trace($inputText)) {
        return;
    }

    $key = $type . '|' . md5($inputText);
    $count = isset($state['counts'][$key]) ? (int) $state['counts'][$key] : 0;
    if ($count >= (int) $state['max_per_key']) {
        return;
    }

    $payload['type'] = (string) $type;
    $payload['ts'] = date('c');
    $payload['caller'] = '';
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
    if (isset($trace[1]['function'])) {
        $payload['caller'] = (string) $trace[1]['function'];
    }

    $state['counts'][$key] = $count + 1;
    $state['events'][] = $payload;
    $GLOBALS['importador_orcamento_pdf_normalize_debug'] = $state;
}

function importador_orcamento_pdf_normalize_debug_flush()
{
    $state = isset($GLOBALS['importador_orcamento_pdf_normalize_debug']) && is_array($GLOBALS['importador_orcamento_pdf_normalize_debug'])
        ? $GLOBALS['importador_orcamento_pdf_normalize_debug']
        : array();
    if (empty($state['enabled']) || empty($state['path'])) {
        return;
    }

    $payload = array(
        'gerado_em' => date('c'),
        'total_eventos' => isset($state['events']) ? count($state['events']) : 0,
        'targets' => importador_orcamento_pdf_normalize_debug_targets(),
        'events' => isset($state['events']) ? $state['events'] : array(),
    );

    @file_put_contents($state['path'], json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}

function importador_orcamento_pdf_load_produtos_cache_meta($cachePath)
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
    $meta['tem_produtos'] = (strpos($head, '"produtos"') !== false);

    return $meta;
}

function importador_orcamento_pdf_iterar_produtos_cache($cachePath, $callback)
{
    if (!is_file($cachePath)) {
        return false;
    }

    $fh = @fopen($cachePath, 'rb');
    if (!$fh) {
        return false;
    }

    $inProdutos = false;
    while (($line = fgets($fh)) !== false) {
        if (!$inProdutos) {
            if (strpos($line, '"produtos"') !== false) {
                $inProdutos = true;
            }
            continue;
        }

        $trim = trim($line);
        if ($trim === '' || $trim === '[' || $trim === ']' || $trim === '],') {
            continue;
        }
        if ($trim === '}' || $trim === ']}') {
            break;
        }

        $jsonLine = trim(ltrim($trim, ','));
        $jsonLine = rtrim($jsonLine, ',');
        if ($jsonLine === '') {
            continue;
        }

        $produto = json_decode($jsonLine, true);
        if (!is_array($produto)) {
            continue;
        }

        $keepGoing = call_user_func($callback, $produto);
        if ($keepGoing === false) {
            break;
        }
    }

    @fclose($fh);
    return true;
}

function importador_orcamento_pdf_extract_catalog_ngrams(array $tokens, $minGram = 2, $maxGram = 2)
{
    $out = array();
    $ordered = array();
    foreach ($tokens as $token) {
        $token = trim((string) $token);
        if ($token === '' || strlen($token) < 2) {
            continue;
        }
        $ordered[] = $token;
    }

    $count = count($ordered);
    if ($count === 0) {
        return array();
    }

    $minGram = max(2, (int) $minGram);
    $maxGram = max($minGram, (int) $maxGram);
    for ($size = $minGram; $size <= $maxGram; $size++) {
        if ($count < $size) {
            continue;
        }
        for ($i = 0; $i <= ($count - $size); $i++) {
            $slice = array_slice($ordered, $i, $size);
            $ngram = trim(implode('-', $slice));
            if ($ngram !== '') {
                $out[$ngram] = true;
            }
        }
    }

    return array_keys($out);
}

function importador_orcamento_pdf_detect_code_pattern($codigo)
{
    $codigo = trim((string) $codigo);
    if ($codigo === '') {
        return 'empty';
    }

    $norm = importador_orcamento_pdf_normalize_text_loose($codigo);
    $norm = preg_replace('/\s+/u', '', (string) $norm);
    if ($norm === '') {
        return 'empty';
    }
    if (preg_match('/^\d+$/u', $norm)) {
        return 'numeric_only';
    }
    if (preg_match('/^[a-z]+$/u', $norm)) {
        return 'alpha_only';
    }
    if (preg_match('/^[a-z]+[-\/]\d+[a-z0-9\-\/]*$/u', $norm)) {
        return 'alpha_num_delimited';
    }
    if (preg_match('/^[a-z]+\d+[a-z0-9]*$/u', $norm)) {
        return 'alpha_num_compact';
    }
    if (preg_match('/^\d+[a-z]+[a-z0-9]*$/u', $norm)) {
        return 'num_alpha_compact';
    }
    if (preg_match('/^[a-z0-9\-\/]+$/u', $norm)) {
        return 'mixed_simple';
    }

    return 'mixed_other';
}

function importador_orcamento_pdf_collect_products_by_ids($cachePath, array $targetIds)
{
    $out = array();
    if (empty($targetIds)) {
        return $out;
    }
    $targetMap = array();
    foreach ($targetIds as $id) {
        $id = (int) $id;
        if ($id > 0) {
            $targetMap[(string) $id] = true;
        }
    }
    if (empty($targetMap)) {
        return $out;
    }

    importador_orcamento_pdf_iterar_produtos_cache($cachePath, function ($p) use (&$out, $targetMap) {
        $id = isset($p['D001_Id']) ? (int) $p['D001_Id'] : 0;
        if ($id <= 0) {
            return true;
        }
        $key = (string) $id;
        if (!isset($targetMap[$key])) {
            return true;
        }
        $out[$key] = $p;
        return true;
    });

    return $out;
}

function importador_orcamento_pdf_fmt_qtd($value)
{
    if ($value === null || $value === '') {
        return '';
    }
    return number_format((float) $value, 3, ',', '');
}

function importador_orcamento_pdf_status_normalized($status)
{
    $status = strtolower(trim((string) $status));
    if ($status === 'lido' || $status === 'incompleto' || $status === 'erro') {
        return $status;
    }
    return 'incompleto';
}

function importador_orcamento_pdf_status_label($status)
{
    $status = importador_orcamento_pdf_status_normalized($status);
    if ($status === 'lido') {
        return 'Lido';
    }
    if ($status === 'erro') {
        return 'Erro';
    }
    return 'Incompleto';
}

function importador_orcamento_pdf_parse_qtd_input($value, &$normalized = '')
{
    $value = trim((string) $value);
    if ($value === '') {
        return false;
    }

    $raw = str_replace(' ', '', $value);
    if (strpos($raw, ',') !== false && strpos($raw, '.') !== false) {
        $raw = str_replace('.', '', $raw);
        $raw = str_replace(',', '.', $raw);
    } elseif (strpos($raw, ',') !== false) {
        $raw = str_replace(',', '.', $raw);
    }

    if (!is_numeric($raw)) {
        return false;
    }

    $number = (float) $raw;
    $normalized = number_format($number, 3, ',', '');
    return true;
}

function importador_orcamento_pdf_load_revisao($path)
{
    if (!is_file($path)) {
        return array();
    }

    $json = json_decode((string) @file_get_contents($path), true);
    if (!is_array($json) || !isset($json['itens']) || !is_array($json['itens'])) {
        return array();
    }

    return $json['itens'];
}

function importador_orcamento_pdf_save_revisao($path, array $itens)
{
    $payload = array(
        'updated_at' => date('c'),
        'itens' => $itens,
    );
    return @file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) !== false;
}

function importador_orcamento_pdf_load_selecao_produtos($path)
{
    $json = importador_orcamento_pdf_load_json_file($path);
    if (!isset($json['itens']) || !is_array($json['itens'])) {
        return array();
    }
    return $json['itens'];
}

function importador_orcamento_pdf_save_selecao_produtos($path, array $itens)
{
    $payload = array(
        'updated_at' => date('c'),
        'itens' => $itens,
    );
    return @file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) !== false;
}

function importador_orcamento_pdf_sincronizar_selecoes_com_sugestoes($selecaoPath, array $sugestoes)
{
    $selecoes = importador_orcamento_pdf_load_selecao_produtos($selecaoPath);
    $itensSug = isset($sugestoes['itens']) && is_array($sugestoes['itens']) ? $sugestoes['itens'] : array();

    foreach ($itensSug as $s) {
        $linha = isset($s['linha']) ? (int) $s['linha'] : 0;
        if ($linha <= 0) {
            continue;
        }
        $key = (string) $linha;
        $produtoIa = isset($s['produto_sugerido']) && is_array($s['produto_sugerido']) ? $s['produto_sugerido'] : null;
        $preSelecionarIa = !empty($s['pre_selecionar']) && $produtoIa !== null;

        $atual = isset($selecoes[$key]) && is_array($selecoes[$key]) ? $selecoes[$key] : array();
        $temProdutoManual = isset($atual['produto']) && is_array($atual['produto']) && (
            !empty($atual['produto']['D001_Id']) ||
            !empty($atual['produto']['codigo']) ||
            !empty($atual['produto']['descricao'])
        );

        // Preserva o que usuário já definiu manualmente.
        if ($temProdutoManual) {
            if (!isset($atual['confirmado'])) {
                $atual['confirmado'] = false;
            }
            if (!isset($atual['origem']) || $atual['origem'] === '') {
                $atual['origem'] = 'manual';
            }
            $selecoes[$key] = $atual;
            continue;
        }

        $selecoes[$key] = array(
            'confirmado' => $preSelecionarIa ? true : (isset($atual['confirmado']) ? (bool) $atual['confirmado'] : false),
            'produto' => $produtoIa,
            'origem' => $produtoIa !== null ? 'ia' : 'manual',
        );
    }

    return importador_orcamento_pdf_save_selecao_produtos($selecaoPath, $selecoes);
}

function importador_orcamento_pdf_load_candidatos_map($path)
{
    $json = importador_orcamento_pdf_load_json_file($path);
    $map = array();
    if (!isset($json['itens']) || !is_array($json['itens'])) {
        return $map;
    }
    foreach ($json['itens'] as $it) {
        $linha = isset($it['linha']) ? (int) $it['linha'] : 0;
        if ($linha <= 0) {
            continue;
        }
        $map[(string) $linha] = isset($it['candidatos']) && is_array($it['candidatos']) ? $it['candidatos'] : array();
    }
    return $map;
}

function importador_orcamento_pdf_load_candidatos_diagnosticos_map($path)
{
    $json = importador_orcamento_pdf_load_json_file($path);
    $map = array();
    $itens = isset($json['itens']) && is_array($json['itens']) ? $json['itens'] : array();
    foreach ($itens as $it) {
        $linha = isset($it['linha']) ? (int) $it['linha'] : 0;
        if ($linha > 0) {
            $map[(string) $linha] = array(
                'diagnostico' => isset($it['diagnostico_local']) && is_array($it['diagnostico_local']) ? $it['diagnostico_local'] : array(),
                'codigo' => isset($it['codigo']) ? (string) $it['codigo'] : '',
                'descricao' => isset($it['descricao']) ? (string) $it['descricao'] : '',
                'quantidade' => isset($it['quantidade']) ? (string) $it['quantidade'] : '',
            );
        }
    }
    return $map;
}

function importador_orcamento_pdf_load_candidatos_item_ui($path, $linha)
{
    $linha = (int) $linha;
    if ($linha <= 0) {
        return array();
    }
    $map = importador_orcamento_pdf_load_candidatos_map($path);
    $candidatos = isset($map[(string) $linha]) && is_array($map[(string) $linha]) ? $map[(string) $linha] : array();
    return importador_orcamento_pdf_compact_candidatos_para_ui($candidatos);
}

function importador_orcamento_pdf_compact_candidatos_para_ui(array $candidatos)
{
    $out = array();
    foreach ($candidatos as $c) {
        if (!is_array($c)) {
            continue;
        }
        $detalhe = isset($c['detalhe_score']) && is_array($c['detalhe_score']) ? $c['detalhe_score'] : array();
        $out[] = array(
            'D001_Id' => isset($c['D001_Id']) ? (int) $c['D001_Id'] : 0,
            'codigo' => isset($c['codigo']) ? (string) $c['codigo'] : '',
            'descricao' => isset($c['descricao']) ? (string) $c['descricao'] : '',
            'score_local' => isset($c['score_local']) ? (int) $c['score_local'] : 0,
            'motivo_score' => isset($c['motivo_score']) ? (string) $c['motivo_score'] : '',
            'detalhe_score' => array(
                'score_final' => isset($detalhe['score_final']) ? (int) $detalhe['score_final'] : 0,
                'classificacao' => isset($detalhe['classificacao']) ? (string) $detalhe['classificacao'] : '',
                'observacoes' => isset($detalhe['observacoes']) && is_array($detalhe['observacoes']) ? array_slice(array_values($detalhe['observacoes']), 0, 6) : array(),
            ),
        );
    }
    return $out;
}

function importador_orcamento_pdf_filtrar_payload_por_linha(array $payload, $linha)
{
    $linha = (int) $linha;
    $itens = isset($payload['itens']) && is_array($payload['itens']) ? $payload['itens'] : array();
    $filtrados = array();

    foreach ($itens as $item) {
        $itemLinha = isset($item['linha']) ? (int) $item['linha'] : 0;
        if ($itemLinha === $linha) {
            $filtrados[] = $item;
        }
    }

    $payload['itens'] = $filtrados;
    if (isset($payload['total_itens'])) {
        $payload['total_itens'] = count($filtrados);
    }

    return $payload;
}

function importador_orcamento_pdf_mesclar_payload_por_linha(array $base, array $novo)
{
    $baseItens = isset($base['itens']) && is_array($base['itens']) ? $base['itens'] : array();
    $novosItens = isset($novo['itens']) && is_array($novo['itens']) ? $novo['itens'] : array();

    $map = array();
    foreach ($baseItens as $item) {
        $linha = isset($item['linha']) ? (int) $item['linha'] : 0;
        if ($linha > 0) {
            $map[(string) $linha] = $item;
        }
    }
    foreach ($novosItens as $item) {
        $linha = isset($item['linha']) ? (int) $item['linha'] : 0;
        if ($linha > 0) {
            $map[(string) $linha] = $item;
        }
    }

    uasort($map, function ($a, $b) {
        $linhaA = isset($a['linha']) ? (int) $a['linha'] : 0;
        $linhaB = isset($b['linha']) ? (int) $b['linha'] : 0;
        if ($linhaA === $linhaB) {
            return 0;
        }
        return ($linhaA < $linhaB) ? -1 : 1;
    });

    $merged = empty($base) ? $novo : $base;
    foreach ($novo as $key => $value) {
        if ($key === 'itens') {
            continue;
        }
        $merged[$key] = $value;
    }
    $merged['itens'] = array_values($map);
    if (isset($merged['total_itens'])) {
        $merged['total_itens'] = count($merged['itens']);
    }

    return $merged;
}

function importador_orcamento_pdf_buscar_produtos_cache($cachePath, $termo, $limit = 20)
{
    $termo = importador_orcamento_pdf_normalize_text($termo);
    if ($termo === '') {
        return array();
    }
    $parts = array_values(array_filter(preg_split('/\s+/u', $termo, -1, PREG_SPLIT_NO_EMPTY)));
    $out = array();
    importador_orcamento_pdf_iterar_produtos_cache($cachePath, function ($p) use ($parts, &$out) {
        $busca = isset($p['busca']) ? (string) $p['busca'] : '';
        if ($busca === '') {
            return true;
        }
        $score = 0;
        foreach ($parts as $tk) {
            if ($tk !== '' && strpos($busca, $tk) !== false) {
                $score++;
            }
        }
        if ($score <= 0) {
            return true;
        }
        $out[] = array(
            'D001_Id' => isset($p['D001_Id']) ? (int) $p['D001_Id'] : 0,
            'codigo' => isset($p['codigo']) ? (string) $p['codigo'] : '',
            'descricao' => isset($p['descricao']) ? (string) $p['descricao'] : '',
            '_score' => $score,
        );
        return true;
    });
    usort($out, function ($a, $b) {
        if ($a['_score'] === $b['_score']) {
            return strcmp((string) $a['codigo'], (string) $b['codigo']);
        }
        return $b['_score'] - $a['_score'];
    });
    $out = array_slice($out, 0, $limit);
    foreach ($out as &$r) {
        unset($r['_score']);
    }
    return $out;
}

function importador_orcamento_pdf_resolver_d009_por_d001($D001_Id, $C004_Id = 0)
{
    $D001_Id = (int) $D001_Id;
    $C004_Id = (int) $C004_Id;
    if ($D001_Id <= 0) {
        return array(false, 'Produto inválido.', null);
    }
    if ($C004_Id <= 0) {
        return array(false, 'Empresa inválida para localizar D009.', null);
    }
    $sql = "SELECT D009_Id, D001_Id, D001_Codigo_Produto, D001_Descricao_Produto, D001_D037_Id, D001_D005_Id
              FROM D001
         LEFT JOIN D049 ON D049_D001_Id = D001_Id
         LEFT JOIN D009 ON D009_D049_Id = D049_Id
             WHERE D001_Id = {$D001_Id}
               AND D009_C004_Id = {$C004_Id}
          ORDER BY D009_Id ASC
             LIMIT 1";
    $res = @mysql_query($sql);
    if (!$res) {
        $err = function_exists('mysql_error') ? (string) @mysql_error() : 'Erro SQL';
        return array(false, 'Falha ao consultar produto para inclusão.', $err);
    }
    $row = mysql_fetch_assoc($res);
    if (!$row || (int) $row['D009_Id'] <= 0) {
        return array(false, 'Produto sem configuração de venda (D009).', null);
    }
    return array(true, '', $row);
}

function importador_orcamento_pdf_fmt_preco($value)
{
    if ($value === null || $value === '') {
        return '0,00';
    }
    $num = (float) $value;
    return number_format($num, 2, ',', '');
}

function importador_orcamento_pdf_build_form_incluir_produto($T003_Id, array $itemConsolidado, array $produtoRow, $precoUnitario = null)
{
    $qtd = isset($itemConsolidado['quantidade']) ? (string) $itemConsolidado['quantidade'] : '0,000';
    // Na inclusão final, usa sempre a descrição oficial do cadastro do produto.
    $desc = isset($produtoRow['D001_Descricao_Produto']) ? (string) $produtoRow['D001_Descricao_Produto'] : '';
    $codigo = isset($produtoRow['D001_Codigo_Produto']) ? (string) $produtoRow['D001_Codigo_Produto'] : (isset($itemConsolidado['codigo']) ? (string) $itemConsolidado['codigo'] : '');

    return array(
        'T004_D009_Id' => (int) $produtoRow['D009_Id'],
        'T004_Quantidade' => $qtd,
        'T004_Valor_Preco_Sem_Desconto_Unitario' => importador_orcamento_pdf_fmt_preco($precoUnitario),
        'T004_D037_Id' => isset($produtoRow['D001_D037_Id']) ? (int) $produtoRow['D001_D037_Id'] : '',
        'T004_Descricao_Produto' => $desc,
        'T004_Codigo_Produto' => $codigo,
        'T004_T003_Id' => (int) $T003_Id,
        'T004_D005_Id' => isset($produtoRow['D001_D005_Id']) ? (int) $produtoRow['D001_D005_Id'] : '',
        'T004_D006_Id' => '',
        'T004_Aliquota_IPI' => '0',
        'T004_Flag_Destino_Produto' => '',
        'T004A_Quantidade_Conversao' => $qtd,
    );
}

function importador_orcamento_pdf_linhas_ja_inseridas($auditPath)
{
    $audit = importador_orcamento_pdf_load_json_file($auditPath);
    $linhas = array();
    $resultados = isset($audit['resultados']) && is_array($audit['resultados']) ? $audit['resultados'] : array();
    foreach ($resultados as $resultado) {
        if (!is_array($resultado)) {
            continue;
        }
        $linha = isset($resultado['linha']) ? (int) $resultado['linha'] : 0;
        $status = isset($resultado['status']) ? (string) $resultado['status'] : '';
        if ($linha > 0 && $status === 'ok') {
            $linhas[(string) $linha] = true;
        }
    }
    return $linhas;
}

function importador_orcamento_pdf_resultados_ok_historico($auditPath)
{
    $audit = importador_orcamento_pdf_load_json_file($auditPath);
    $historico = array();
    $resultados = isset($audit['resultados']) && is_array($audit['resultados']) ? $audit['resultados'] : array();
    foreach ($resultados as $resultado) {
        if (!is_array($resultado)) {
            continue;
        }
        $linha = isset($resultado['linha']) ? (int) $resultado['linha'] : 0;
        $status = isset($resultado['status']) ? (string) $resultado['status'] : '';
        if ($linha <= 0 || $status !== 'ok') {
            continue;
        }
        $historico[(string) $linha] = array(
            'linha' => $linha,
            'status' => 'ok',
            'D001_Id' => isset($resultado['D001_Id']) ? (int) $resultado['D001_Id'] : 0,
        );
    }
    return array_values($historico);
}

function importador_orcamento_pdf_montar_itens_para_insercao($previewPath, array $revisoes, array $selecoes, array $linhasJaInseridas = array())
{
    if (!is_file($previewPath)) {
        return array(false, 'Import preview não encontrado para inclusão.', array());
    }
    $previewJson = json_decode((string) @file_get_contents($previewPath), true);
    $previewItems = isset($previewJson['items']) && is_array($previewJson['items']) ? $previewJson['items'] : array();
    $itensConsolidados = importador_orcamento_pdf_build_itens_para_ia($previewItems, $revisoes);
    $itensConsolidados = isset($itensConsolidados['itens']) && is_array($itensConsolidados['itens']) ? $itensConsolidados['itens'] : array();

    $mapPrecoLinha = array();
    foreach ($previewItems as $idx => $itPrev) {
        $linhaPrev = $idx + 1;
        $mapPrecoLinha[(string) $linhaPrev] = isset($itPrev['unitPrice']) ? $itPrev['unitPrice'] : null;
    }
    $mapItens = array();
    foreach ($itensConsolidados as $it) {
        $linha = isset($it['linha']) ? (int) $it['linha'] : 0;
        if ($linha > 0) {
            $mapItens[(string) $linha] = $it;
        }
    }

    $itensParaInserir = array();
    $itensElegiveis = 0;
    foreach ($selecoes as $linhaSel => $s) {
        $linha = (int) $linhaSel;
        $okConf = isset($s['confirmado']) && $s['confirmado'] && isset($s['produto']) && is_array($s['produto']);
        if (!$okConf || !isset($mapItens[(string) $linha])) {
            continue;
        }
        $itensElegiveis++;
        if (isset($linhasJaInseridas[(string) $linha])) {
            continue;
        }
        $produto = $s['produto'];
        if (!(isset($produto['D001_Id']) && (int) $produto['D001_Id'] > 0)) {
            continue;
        }
        $itensParaInserir[] = array(
            'linha' => $linha,
            'item' => $mapItens[(string) $linha],
            'produto' => $produto,
            'preco_unitario' => isset($mapPrecoLinha[(string) $linha]) ? $mapPrecoLinha[(string) $linha] : null,
        );
    }

    if (empty($itensParaInserir)) {
        if ($itensElegiveis > 0 && !empty($linhasJaInseridas)) {
            return array(false, 'Todos os itens confirmados deste lote já foram inseridos no orçamento.', array());
        }
        return array(false, 'Nenhum item confirmado com produto selecionado para inserir.', array());
    }
    return array(true, '', $itensParaInserir);
}

function importador_orcamento_pdf_processar_lote_insercao($T003_Id, $auditPath, $statePath, $chunkSize = 20)
{
    $chunkSize = (int) $chunkSize > 0 ? (int) $chunkSize : 20;
    $state = importador_orcamento_pdf_load_json_file($statePath);
    $itens = isset($state['itens']) && is_array($state['itens']) ? $state['itens'] : array();
    $cursor = isset($state['cursor']) ? (int) $state['cursor'] : 0;
    $total = count($itens);
    if ($total <= 0) {
        return array('ok' => false, 'erro' => 'Estado da inclusão inválido.', 'done' => false);
    }

    require_once __DIR__ . '/../../bibliotecas/classes/VEN001.php';
    require_once __DIR__ . '/../../bibliotecas/classes/GFormWrap.php';
    $VEN001 = new VEN001();
    list($okCtx, $erroCtx, $ctxT003) = importador_orcamento_pdf_contexto_orcamento($T003_Id);
    if (!$okCtx) {
        return array('ok' => false, 'erro' => $erroCtx, 'done' => false);
    }

    $audit = importador_orcamento_pdf_load_json_file($auditPath);
    if (!isset($audit['falhas']) || !is_array($audit['falhas'])) {
        $audit['falhas'] = array();
    }
    if (!isset($audit['resultados']) || !is_array($audit['resultados'])) {
        $audit['resultados'] = array();
    }
    if (!isset($audit['itens_inseridos'])) {
        $audit['itens_inseridos'] = 0;
    }

    $fim = min($total, $cursor + $chunkSize);
    for ($i = $cursor; $i < $fim; $i++) {
        $entry = $itens[$i];
        $linha = isset($entry['linha']) ? (int) $entry['linha'] : 0;
        $item = isset($entry['item']) && is_array($entry['item']) ? $entry['item'] : array();
        $produto = isset($entry['produto']) && is_array($entry['produto']) ? $entry['produto'] : array();

        list($okD009, $erroD009, $produtoRow) = importador_orcamento_pdf_resolver_d009_por_d001(
            isset($produto['D001_Id']) ? (int) $produto['D001_Id'] : 0,
            isset($ctxT003['T003_C004_Id']) ? (int) $ctxT003['T003_C004_Id'] : 0
        );
        if (!$okD009) {
            $audit['falhas'][] = array('linha' => $linha, 'erro' => $erroD009);
            $audit['resultados'][] = array('linha' => $linha, 'status' => 'erro', 'erro' => $erroD009);
            continue;
        }

        $formData = importador_orcamento_pdf_build_form_incluir_produto($T003_Id, $item, $produtoRow, isset($entry['preco_unitario']) ? $entry['preco_unitario'] : null);
        $Destino = isset($ctxT003['T003_Flag_Destino_Produto']) ? (string) $ctxT003['T003_Flag_Destino_Produto'] : '';
        $formData['T004_D006_Id'] = '';
        $formData['T004_Flag_Destino_Produto'] = $Destino;
        $formData['T004_Aliquota_IPI'] = '0';
        $formData['T004A_Quantidade_Conversao'] = isset($formData['T004_Quantidade']) ? $formData['T004_Quantidade'] : '0,000';

        $form = new GFormWrap($formData);
        $retorno = $VEN001->incluirProduto($form);
        if ($retorno === true) {
            $audit['itens_inseridos']++;
            $audit['resultados'][] = array('linha' => $linha, 'status' => 'ok', 'D001_Id' => (int) (isset($produto['D001_Id']) ? $produto['D001_Id'] : 0));
        } else {
            $errMsg = is_string($retorno) ? $retorno : 'Falha desconhecida ao incluir item.';
            $audit['falhas'][] = array('linha' => $linha, 'erro' => $errMsg);
            $audit['resultados'][] = array('linha' => $linha, 'status' => 'erro', 'erro' => $errMsg);
        }
    }

    $state['cursor'] = $fim;
    $done = $fim >= $total;
    $audit['status'] = $done ? (empty($audit['falhas']) ? 'sucesso' : 'parcial') : 'processando';
    @file_put_contents($auditPath, json_encode($audit, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    if ($done) {
        @unlink($statePath);
    } else {
        @file_put_contents($statePath, json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    return array(
        'ok' => true,
        'done' => $done,
        'total' => $total,
        'processados' => $fim,
        'itens_inseridos' => (int) $audit['itens_inseridos'],
        'falhas' => isset($audit['falhas']) && is_array($audit['falhas']) ? count($audit['falhas']) : 0,
        'status' => isset($audit['status']) ? $audit['status'] : 'processando',
    );
}

function importador_orcamento_pdf_contexto_orcamento($T003_Id)
{
    $T003_Id = (int) $T003_Id;
    $sql = "SELECT *
              FROM T003
             WHERE T003_Id = {$T003_Id}
             LIMIT 1";
    $res = @mysql_query($sql);
    if (!$res) {
        return array(false, 'Falha ao consultar contexto do orçamento.', null);
    }
    $row = mysql_fetch_assoc($res);
    if (!$row) {
        return array(false, 'Orçamento não encontrado para inclusão.', null);
    }
    return array(true, '', $row);
}

function importador_orcamento_pdf_build_itens_para_ia(array $items, array $revisoes)
{
    $out = array();

    foreach ($items as $idx => $item) {
        $linha = $idx + 1;
        $codigoOriginal = trim((string) (isset($item['codeClient']) ? $item['codeClient'] : ''));
        $descricaoOriginal = trim((string) (isset($item['description']) ? $item['description'] : ''));
        $quantidadeOriginal = importador_orcamento_pdf_fmt_qtd(isset($item['quantity']) ? $item['quantity'] : null);
        $unidadeOriginal = trim((string) (isset($item['unit']) ? $item['unit'] : ''));

        $revisao = isset($revisoes[(string) $linha]) && is_array($revisoes[(string) $linha]) ? $revisoes[(string) $linha] : array();
        $codigoRevisado = trim((string) (isset($revisao['codigo']) ? $revisao['codigo'] : ''));
        $descricaoRevisada = trim((string) (isset($revisao['descricao']) ? $revisao['descricao'] : ''));
        $quantidadeRevisada = trim((string) (isset($revisao['quantidade']) ? $revisao['quantidade'] : ''));

        $codigoFinal = $codigoRevisado !== '' ? $codigoRevisado : $codigoOriginal;
        $descricaoFinal = $descricaoRevisada !== '' ? $descricaoRevisada : $descricaoOriginal;
        $quantidadeFinal = $quantidadeRevisada !== '' ? $quantidadeRevisada : $quantidadeOriginal;

        $out[] = array(
            'linha' => $linha,
            'codigo' => $codigoFinal,
            'descricao' => $descricaoFinal,
            'quantidade' => $quantidadeFinal,
            'unidade' => $unidadeOriginal,
        );
    }

    return array('itens' => $out);
}

function importador_orcamento_pdf_prepare_enrichment_input($previewPath, array $revisoes, $inputPath, &$error = '')
{
    $error = '';
    $rawPreview = @file_get_contents($previewPath);
    $preview = is_string($rawPreview) ? json_decode($rawPreview, true) : null;
    if (!is_array($preview) || !isset($preview['items']) || !is_array($preview['items'])) {
        $error = 'Preview de importação inválido para aplicar a Base de Conhecimento.';
        return false;
    }

    foreach ($preview['items'] as $index => &$item) {
        if (!is_array($item)) {
            continue;
        }
        $line = (string) ((int) $index + 1);
        $revision = isset($revisoes[$line]) && is_array($revisoes[$line]) ? $revisoes[$line] : array();
        $code = isset($revision['codigo']) ? trim((string) $revision['codigo']) : '';
        $description = isset($revision['descricao']) ? trim((string) $revision['descricao']) : '';
        $quantity = isset($revision['quantidade']) ? trim((string) $revision['quantidade']) : '';

        if ($code !== '') {
            $item['codeClient'] = $code;
        }
        if ($description !== '') {
            $item['description'] = $description;
        }
        if ($quantity !== '') {
            $item['quantity'] = $quantity;
        }
    }
    unset($item);

    $preview['enrichment_input'] = array(
        'source_preview_sha256' => hash('sha256', (string) $rawPreview),
        'revisions_applied' => !empty($revisoes),
        'generated_at' => date('c'),
    );

    $dir = dirname((string) $inputPath);
    $tmp = @tempnam($dir, '.tmp-enrichment-input-');
    $json = json_encode($preview, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($tmp === false || !is_string($json) || @file_put_contents($tmp, $json, LOCK_EX) === false || !@rename($tmp, $inputPath)) {
        if ($tmp !== false) {
            @unlink($tmp);
        }
        $error = 'Não foi possível preparar as alterações do item para a Base de Conhecimento.';
        return false;
    }
    return true;
}

function importador_orcamento_pdf_extract_json_object($text)
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }
    if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/is', $text, $m)) {
        return trim((string) $m[1]);
    }
    $start = strpos($text, '{');
    $end = strrpos($text, '}');
    if ($start === false || $end === false || $end <= $start) {
        return '';
    }
    return trim((string) substr($text, $start, $end - $start + 1));
}

function importador_orcamento_pdf_load_sugestoes_ia($path)
{
    $out = array(
        'map' => array(),
        'erro' => '',
    );
    if (!is_file($path)) {
        return $out;
    }
    $json = json_decode((string) @file_get_contents($path), true);
    if (!is_array($json)) {
        $out['erro'] = 'Arquivo de sugestões da IA inválido.';
        return $out;
    }
    if (isset($json['erro']) && trim((string) $json['erro']) !== '') {
        $out['erro'] = trim((string) $json['erro']);
    }
    if (!isset($json['itens']) || !is_array($json['itens'])) {
        return $out;
    }
    foreach ($json['itens'] as $item) {
        $linha = isset($item['linha']) ? (int) $item['linha'] : 0;
        if ($linha <= 0) {
            continue;
        }
        $out['map'][(string) $linha] = $item;
    }
    return $out;
}

function importador_orcamento_pdf_load_sugestoes_ia_payload($path)
{
    $json = importador_orcamento_pdf_load_json_file($path);
    if (!isset($json['itens']) || !is_array($json['itens'])) {
        $json['itens'] = array();
    }
    if (!isset($json['erro'])) {
        $json['erro'] = '';
    }
    if (!isset($json['gerado_em']) || trim((string) $json['gerado_em']) === '') {
        $json['gerado_em'] = date('Y-m-d H:i:s');
    }
    return $json;
}

function importador_orcamento_pdf_save_json_pretty($path, array $payload)
{
    return @file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) !== false;
}

function importador_orcamento_pdf_save_sugestoes_ia_payload($path, array $payload)
{
    if (!isset($payload['itens']) || !is_array($payload['itens'])) {
        $payload['itens'] = array();
    }
    if (!isset($payload['erro'])) {
        $payload['erro'] = '';
    }
    if (!isset($payload['gerado_em']) || trim((string) $payload['gerado_em']) === '') {
        $payload['gerado_em'] = date('Y-m-d H:i:s');
    }
    return importador_orcamento_pdf_save_json_pretty($path, $payload);
}

function importador_orcamento_pdf_upsert_sugestao_ia_payload(array &$payload, array $itemSug)
{
    if (!isset($payload['itens']) || !is_array($payload['itens'])) {
        $payload['itens'] = array();
    }

    $linha = isset($itemSug['linha']) ? (int) $itemSug['linha'] : 0;
    if ($linha <= 0) {
        return;
    }

    foreach ($payload['itens'] as $idx => $itemAtual) {
        $linhaAtual = isset($itemAtual['linha']) ? (int) $itemAtual['linha'] : 0;
        if ($linhaAtual === $linha) {
            $payload['itens'][$idx] = $itemSug;
            return;
        }
    }

    $payload['itens'][] = $itemSug;
}

function importador_orcamento_pdf_sincronizar_selecao_com_sugestao($selecaoPath, array $sugestao)
{
    $linha = isset($sugestao['linha']) ? (int) $sugestao['linha'] : 0;
    if ($linha <= 0) {
        return false;
    }

    $selecoes = importador_orcamento_pdf_load_selecao_produtos($selecaoPath);
    $key = (string) $linha;
    $produtoIa = isset($sugestao['produto_sugerido']) && is_array($sugestao['produto_sugerido']) ? $sugestao['produto_sugerido'] : null;
    $preSelecionarIa = !empty($sugestao['pre_selecionar']) && $produtoIa !== null;

    $atual = isset($selecoes[$key]) && is_array($selecoes[$key]) ? $selecoes[$key] : array();
    $temProdutoManual = isset($atual['produto']) && is_array($atual['produto']) && (
        !empty($atual['produto']['D001_Id']) ||
        !empty($atual['produto']['codigo']) ||
        !empty($atual['produto']['descricao'])
    ) && (!isset($atual['origem']) || $atual['origem'] !== 'ia');

    if ($temProdutoManual) {
        if (!isset($atual['confirmado'])) {
            $atual['confirmado'] = false;
        }
        if (!isset($atual['origem']) || $atual['origem'] === '') {
            $atual['origem'] = 'manual';
        }
        $selecoes[$key] = $atual;
        return importador_orcamento_pdf_save_selecao_produtos($selecaoPath, $selecoes);
    }

    $selecoes[$key] = array(
        'confirmado' => $preSelecionarIa ? true : (isset($atual['confirmado']) ? (bool) $atual['confirmado'] : false),
        'produto' => $produtoIa,
        'origem' => $produtoIa !== null ? 'ia' : 'manual',
    );

    return importador_orcamento_pdf_save_selecao_produtos($selecaoPath, $selecoes);
}

function importador_orcamento_pdf_build_sugestoes_ia_state(array $payloadCandidatos, $model)
{
    $itens = isset($payloadCandidatos['itens']) && is_array($payloadCandidatos['itens']) ? $payloadCandidatos['itens'] : array();
    return array(
        'gerado_em' => date('Y-m-d H:i:s'),
        'atualizado_em' => date('Y-m-d H:i:s'),
        'status' => 'processando',
        'model' => (string) $model,
        'cursor' => 0,
        'candidate_offset' => 0,
        'best_suggestion' => null,
        'total_itens' => count($itens),
        'processados_itens' => 0,
        'itens_com_sugestao' => 0,
        'chamadas_ia' => 0,
        'chamadas_com_erro' => 0,
        'ultimo_erro' => '',
    );
}

function importador_orcamento_pdf_processar_sugestoes_ia_etapa(array $payloadCandidatos, $sugestoesIaPath, $selecaoProdutosPath, $statePath, $apiKey, $model, &$error = '', $maxCalls = 1, $candidateChunkSize = 20)
{
    $error = '';
    $state = importador_orcamento_pdf_load_json_file($statePath);
    if (!is_array($state) || !isset($state['cursor'])) {
        $error = 'Estado do processamento de sugestões da IA inválido.';
        return array('ok' => false, 'erro' => $error, 'done' => false);
    }

    $itensEntrada = isset($payloadCandidatos['itens']) && is_array($payloadCandidatos['itens']) ? $payloadCandidatos['itens'] : array();
    $total = count($itensEntrada);
    if ($total <= 0) {
        $error = 'Arquivo itens_com_candidatos.json sem itens para análise.';
        return array('ok' => false, 'erro' => $error, 'done' => false);
    }

    $cursor = isset($state['cursor']) ? (int) $state['cursor'] : 0;
    $candidateOffset = isset($state['candidate_offset']) ? (int) $state['candidate_offset'] : 0;
    $candidateChunkSize = (int) $candidateChunkSize > 0 ? (int) $candidateChunkSize : 20;
    $candidateChunkSize = min(20, $candidateChunkSize);
    $maxCalls = (int) $maxCalls > 0 ? (int) $maxCalls : 1;
    $maxCalls = min(3, $maxCalls);
    $state['model'] = (string) $model;
    $state['status'] = 'processando';
    $state['total_itens'] = $total;

    $payloadSugestoes = importador_orcamento_pdf_load_sugestoes_ia_payload($sugestoesIaPath);
    $apiCallsUsed = 0;
    $linhaAtual = 0;

    while ($cursor < $total) {
        $item = $itensEntrada[$cursor];
        $linha = isset($item['linha']) ? (int) $item['linha'] : 0;
        $linhaAtual = $linha;

        $candOut = array();
        $candList = isset($item['candidatos']) && is_array($item['candidatos']) ? $item['candidatos'] : array();
        foreach ($candList as $c) {
            $candCodigo = trim((string) (isset($c['codigo']) ? $c['codigo'] : ''));
            if ($candCodigo === '') {
                continue;
            }
            $candOut[] = array(
                'D001_Id' => isset($c['D001_Id']) ? (int) $c['D001_Id'] : 0,
                'codigo' => $candCodigo,
                'descricao' => isset($c['descricao']) ? (string) $c['descricao'] : '',
                'score_local' => isset($c['score_local']) ? (int) $c['score_local'] : 0,
            );
        }

        if ($linha <= 0) {
            $cursor++;
            $candidateOffset = 0;
            $state['best_suggestion'] = null;
            $state['processados_itens'] = min($total, isset($state['processados_itens']) ? max((int) $state['processados_itens'], $cursor) : $cursor);
            continue;
        }

        if (empty($candOut)) {
            $finalSugestao = importador_orcamento_pdf_sugestao_nula($linha, 'Item sem candidatos para análise da IA');
            importador_orcamento_pdf_upsert_sugestao_ia_payload($payloadSugestoes, $finalSugestao);
            importador_orcamento_pdf_sincronizar_selecao_com_sugestao($selecaoProdutosPath, $finalSugestao);
            $cursor++;
            $candidateOffset = 0;
            $state['best_suggestion'] = null;
            $state['processados_itens'] = min($total, isset($state['processados_itens']) ? max((int) $state['processados_itens'], $cursor) : $cursor);
            continue;
        }

        if ($candidateOffset >= count($candOut)) {
            $bestSugestao = isset($state['best_suggestion']) && is_array($state['best_suggestion'])
                ? $state['best_suggestion']
                : importador_orcamento_pdf_sugestao_nula($linha, 'A IA não encontrou sugestão confiável.');
            importador_orcamento_pdf_upsert_sugestao_ia_payload($payloadSugestoes, $bestSugestao);
            importador_orcamento_pdf_sincronizar_selecao_com_sugestao($selecaoProdutosPath, $bestSugestao);
            if (!empty($bestSugestao['produto_sugerido'])) {
                $state['itens_com_sugestao'] = isset($state['itens_com_sugestao']) ? ((int) $state['itens_com_sugestao'] + 1) : 1;
            }
            $cursor++;
            $candidateOffset = 0;
            $state['best_suggestion'] = null;
            $state['processados_itens'] = min($total, isset($state['processados_itens']) ? max((int) $state['processados_itens'], $cursor) : $cursor);
            continue;
        }

        if ($apiCallsUsed >= $maxCalls) {
            break;
        }

        $chunk = array_slice($candOut, $candidateOffset, $candidateChunkSize);
        $chunkIndex = (int) floor($candidateOffset / $candidateChunkSize) + 1;
        $basePayloadItem = array(
            'item_id' => $linha,
            'codigo' => isset($item['codigo']) ? (string) $item['codigo'] : '',
            'descricao' => isset($item['descricao']) ? (string) $item['descricao'] : '',
            'quantidade' => isset($item['quantidade']) ? (string) $item['quantidade'] : '',
            'unidade' => isset($item['unidade']) ? (string) $item['unidade'] : '',
            'candidatos' => $chunk,
        );

        $errBatch = '';
        $resultados = importador_orcamento_pdf_call_openai_chat($apiKey, $model, array($basePayloadItem), $errBatch);
        $state['chamadas_ia'] = isset($state['chamadas_ia']) ? ((int) $state['chamadas_ia'] + 1) : 1;
        $apiCallsUsed++;

        if ($resultados === null) {
            $state['chamadas_com_erro'] = isset($state['chamadas_com_erro']) ? ((int) $state['chamadas_com_erro'] + 1) : 1;
            $state['ultimo_erro'] = $errBatch !== '' ? $errBatch : 'Falha ao processar lote com IA.';
            $sugestaoLote = importador_orcamento_pdf_sugestao_nula($linha, 'Erro ao processar lote ' . $chunkIndex . ' com IA');
            if ($error === '') {
                $error = $state['ultimo_erro'];
            }
        } else {
            $respostaLinha = null;
            foreach ($resultados as $resultado) {
                if (isset($resultado['item_id']) && (int) $resultado['item_id'] === $linha) {
                    $respostaLinha = $resultado;
                    break;
                }
            }
            $sugestaoLote = importador_orcamento_pdf_sanitizar_sugestao_ia(
                $linha,
                $chunk,
                $respostaLinha,
                'Item sem retorno da IA neste lote'
            );
        }

        $melhorAtual = isset($state['best_suggestion']) && is_array($state['best_suggestion'])
            ? $state['best_suggestion']
            : importador_orcamento_pdf_sugestao_nula($linha, 'A IA não encontrou sugestão confiável.');
        $melhorAtual = importador_orcamento_pdf_escolher_melhor_sugestao_ia($melhorAtual, $sugestaoLote);
        $candidateOffset += count($chunk);

        $itemFinalizado = importador_orcamento_pdf_sugestao_ia_eh_forte($melhorAtual) || $candidateOffset >= count($candOut);
        if ($itemFinalizado) {
            importador_orcamento_pdf_upsert_sugestao_ia_payload($payloadSugestoes, $melhorAtual);
            importador_orcamento_pdf_sincronizar_selecao_com_sugestao($selecaoProdutosPath, $melhorAtual);
            if (!empty($melhorAtual['produto_sugerido'])) {
                $state['itens_com_sugestao'] = isset($state['itens_com_sugestao']) ? ((int) $state['itens_com_sugestao'] + 1) : 1;
            }
            $cursor++;
            $candidateOffset = 0;
            $state['best_suggestion'] = null;
            $state['processados_itens'] = min($total, isset($state['processados_itens']) ? max((int) $state['processados_itens'], $cursor) : $cursor);
        } else {
            $state['best_suggestion'] = $melhorAtual;
        }

        break;
    }

    $state['cursor'] = $cursor;
    $state['candidate_offset'] = $candidateOffset;
    $done = $cursor >= $total;
    $state['status'] = $done ? 'concluido' : 'processando';
    $state['atualizado_em'] = date('Y-m-d H:i:s');
    $payloadSugestoes['model'] = (string) $model;
    $payloadSugestoes['erro'] = $done && !empty($state['ultimo_erro']) ? (string) $state['ultimo_erro'] : '';

    importador_orcamento_pdf_save_sugestoes_ia_payload($sugestoesIaPath, $payloadSugestoes);

    if ($done) {
        @unlink($statePath);
    } else {
        importador_orcamento_pdf_save_json_pretty($statePath, $state);
    }

    return array(
        'ok' => true,
        'done' => $done,
        'status' => $done ? 'concluido' : 'processando',
        'total' => $total,
        'processados' => isset($state['processados_itens']) ? (int) $state['processados_itens'] : $cursor,
        'linha_atual' => $done ? 0 : $linhaAtual,
        'candidate_offset' => $candidateOffset,
        'chamadas_ia' => isset($state['chamadas_ia']) ? (int) $state['chamadas_ia'] : 0,
        'chamadas_com_erro' => isset($state['chamadas_com_erro']) ? (int) $state['chamadas_com_erro'] : 0,
        'itens_com_sugestao' => isset($state['itens_com_sugestao']) ? (int) $state['itens_com_sugestao'] : 0,
        'ultimo_erro' => isset($state['ultimo_erro']) ? (string) $state['ultimo_erro'] : '',
    );
}

function importador_orcamento_pdf_sugestao_nula($linha, $motivo)
{
    return array(
        'linha' => (int) $linha,
        'produto_sugerido' => null,
        'percentual_confianca' => 0,
        'motivo' => (string) $motivo,
        'pre_selecionar' => false,
        'exigir_revisao_manual' => true,
    );
}

function importador_orcamento_pdf_sugestao_ia_eh_forte(array $item)
{
    return !empty($item['produto_sugerido'])
        && isset($item['percentual_confianca'])
        && (int) $item['percentual_confianca'] >= 90
        && empty($item['exigir_revisao_manual']);
}

function importador_orcamento_pdf_escolher_melhor_sugestao_ia(array $atual, array $nova)
{
    $atualTemProduto = !empty($atual['produto_sugerido']);
    $novaTemProduto = !empty($nova['produto_sugerido']);

    if ($novaTemProduto && !$atualTemProduto) {
        return $nova;
    }

    if (!$novaTemProduto && $atualTemProduto) {
        return $atual;
    }

    $confAtual = isset($atual['percentual_confianca']) ? (int) $atual['percentual_confianca'] : 0;
    $confNova = isset($nova['percentual_confianca']) ? (int) $nova['percentual_confianca'] : 0;
    if ($confNova > $confAtual) {
        return $nova;
    }
    if ($confAtual > $confNova) {
        return $atual;
    }

    $preAtual = !empty($atual['pre_selecionar']);
    $preNova = !empty($nova['pre_selecionar']);
    if ($preNova && !$preAtual) {
        return $nova;
    }
    if ($preAtual && !$preNova) {
        return $atual;
    }

    $motivoAtual = isset($atual['motivo']) ? trim((string) $atual['motivo']) : '';
    $motivoNova = isset($nova['motivo']) ? trim((string) $nova['motivo']) : '';
    if ($motivoNova !== '' && $motivoAtual === '') {
        return $nova;
    }

    return $atual;
}

function importador_orcamento_pdf_sanitizar_sugestao_ia($linha, array $candLinha, $respostaRaw = null, $motivoFallback = 'Item sem retorno da IA')
{
    if (!is_array($respostaRaw)) {
        return importador_orcamento_pdf_sugestao_nula($linha, $motivoFallback);
    }

    $codigoSug = isset($respostaRaw['produto_sugerido']) ? trim((string) $respostaRaw['produto_sugerido']) : '';
    $descSug = isset($respostaRaw['descricao_sugerida']) ? trim((string) $respostaRaw['descricao_sugerida']) : '';
    $motivo = isset($respostaRaw['motivo']) ? trim((string) $respostaRaw['motivo']) : '';
    $conf = isset($respostaRaw['percentual_confianca']) ? (int) $respostaRaw['percentual_confianca'] : 0;
    if ($conf < 0) {
        $conf = 0;
    }
    if ($conf > 100) {
        $conf = 100;
    }

    $candMatch = null;
    foreach ($candLinha as $c) {
        if (isset($c['codigo']) && trim((string) $c['codigo']) === $codigoSug) {
            $candMatch = $c;
            break;
        }
    }

    if ($codigoSug === '' || $codigoSug === 'null') {
        return importador_orcamento_pdf_sugestao_nula($linha, $motivo !== '' ? $motivo : 'Sem confianca suficiente');
    }

    if ($candMatch === null) {
        return importador_orcamento_pdf_sugestao_nula($linha, 'Produto retornado não existe nos candidatos');
    }

    $produto = array(
        'D001_Id' => isset($candMatch['D001_Id']) ? (int) $candMatch['D001_Id'] : 0,
        'codigo' => isset($candMatch['codigo']) ? (string) $candMatch['codigo'] : '',
        'descricao' => $descSug !== '' ? $descSug : (isset($candMatch['descricao']) ? (string) $candMatch['descricao'] : ''),
    );

    $preSelecionar = ($conf >= 90) && empty($respostaRaw['exigir_revisao_manual']);
    $exigirRevisao = !$preSelecionar;
    if ($conf < 90) {
        $preSelecionar = false;
        $exigirRevisao = true;
    }

    return array(
        'linha' => (int) $linha,
        'produto_sugerido' => $produto,
        'percentual_confianca' => $conf,
        'motivo' => $motivo,
        'pre_selecionar' => $preSelecionar,
        'exigir_revisao_manual' => $exigirRevisao,
    );
}

function importador_orcamento_pdf_call_openai_chat($apiKey, $model, array $batchItens, &$error = '')
{
    $error = '';
    $timeoutSeconds = (int) getenv('IMPORTADOR_IA_TIMEOUT');
    if ($timeoutSeconds <= 0) {
        $timeoutSeconds = 45;
    }
    $timeoutSeconds = max(15, min(55, $timeoutSeconds));
    $systemPrompt = 'Voce e um assistente de matching de itens de orcamento. Retorne SOMENTE JSON valido, sem markdown e sem texto extra.';
    $userPrompt = "Tarefa: para cada item, escolher no maximo 1 produto entre os candidatos do proprio item.\n"
        . "REGRAS OBRIGATORIAS:\n"
        . "1) NUNCA invente produto.\n"
        . "2) Somente escolha codigo que exista na lista de candidatos do item.\n"
        . "3) score_local e apenas apoio, NAO decide sozinho.\n"
        . "4) Compare descricao original x descricao dos candidatos considerando tipo, medida, modelo, material, aplicacao e termos equivalentes.\n"
        . "5) Em ambiguidade ou baixa confianca, retorne produto_sugerido = null.\n"
        . "6) Nao escolha candidato apenas por score alto quando descricao nao bater.\n"
        . "7) percentual_confianca deve ser inteiro de 0 a 100.\n"
        . "8) pre_selecionar = true somente quando percentual_confianca >= 90 e sem ambiguidade.\n"
        . "9) exigir_revisao_manual = true quando percentual_confianca < 90 ou produto_sugerido = null.\n"
        . "10) Se produto_sugerido = null, use percentual_confianca=0, pre_selecionar=false, exigir_revisao_manual=true.\n\n"
        . "FORMATO OBRIGATORIO DE SAIDA:\n"
        . "{\"resultados\":[{\"item_id\":1,\"produto_sugerido\":\"115244\" ou null,\"descricao_sugerida\":\"...\" ou null,\"percentual_confianca\":92,\"motivo\":\"...\",\"pre_selecionar\":true,\"exigir_revisao_manual\":false}]}\n\n"
        . "DADOS:\n"
        . json_encode(array('itens' => $batchItens), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $request = array(
        'model' => $model,
        'temperature' => 0,
        'messages' => array(
            array('role' => 'system', 'content' => $systemPrompt),
            array('role' => 'user', 'content' => $userPrompt),
        ),
        'response_format' => array('type' => 'json_object'),
    );

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutSeconds);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));

    $respRaw = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($respRaw === false || $curlErr !== '') {
        $error = 'Falha ao comunicar com a IA.';
        error_log('[importadorOrcamentoPdf] erro curl IA: ' . $curlErr);
        return null;
    }

    $resp = json_decode((string) $respRaw, true);
    if (!is_array($resp) || $httpCode >= 400) {
        $error = 'A IA retornou erro na requisicao.';
        error_log('[importadorOrcamentoPdf] erro HTTP IA: ' . $httpCode . ' body=' . substr((string) $respRaw, 0, 2000));
        return null;
    }

    $content = isset($resp['choices'][0]['message']['content']) ? (string) $resp['choices'][0]['message']['content'] : '';
    $jsonText = importador_orcamento_pdf_extract_json_object($content);
    if ($jsonText === '') {
        $error = 'Resposta da IA em formato inválido.';
        error_log('[importadorOrcamentoPdf] IA sem JSON valido: ' . substr($content, 0, 1500));
        return null;
    }

    $out = json_decode($jsonText, true);
    if (!is_array($out) || !isset($out['resultados']) || !is_array($out['resultados'])) {
        $error = 'Resposta da IA não contém o campo resultados.';
        error_log('[importadorOrcamentoPdf] IA JSON invalido: ' . substr($jsonText, 0, 1500));
        return null;
    }
    return $out['resultados'];
}

function importador_orcamento_pdf_gerar_sugestoes_ia(array $itensComCandidatos, &$error = '')
{
    $error = '';
    global $g;
    //$apiKey = isset($g['C031']['openAIApiKey']) ? trim((string) $g['C031']['openAIApiKey']) : '';
    $apiKey = isset($g['C031']['openAIApiKey']) ? trim((string) $g['C031']['openAIApiKey']) : '';
    if ($apiKey === '') {
        $error = 'Chave da IA não configurada. Defina openAIApiKey nas configurações da empresa.';
        return null;
    }

    $model = getenv('IMPORTADOR_IA_MODEL');
    if (!$model) {
        $model = 'gpt-4.1-mini';
    }

    $itensEntrada = isset($itensComCandidatos['itens']) && is_array($itensComCandidatos['itens']) ? $itensComCandidatos['itens'] : array();
    if (empty($itensEntrada)) {
        $error = 'Arquivo itens_com_candidatos.json sem itens para análise.';
        return array(
            'gerado_em' => date('Y-m-d H:i:s'),
            'model' => $model,
            'erro' => $error,
            'itens' => array(),
        );
    }

    $san = array();
    foreach ($itensEntrada as $item) {
        $candOut = array();
        $candList = isset($item['candidatos']) && is_array($item['candidatos']) ? $item['candidatos'] : array();
        foreach ($candList as $c) {
            $candCodigo = trim((string) (isset($c['codigo']) ? $c['codigo'] : ''));
            if ($candCodigo === '') {
                continue;
            }
            $candOut[] = array(
                'D001_Id' => isset($c['D001_Id']) ? (int) $c['D001_Id'] : 0,
                'codigo' => $candCodigo,
                'descricao' => isset($c['descricao']) ? (string) $c['descricao'] : '',
                'score_local' => isset($c['score_local']) ? (int) $c['score_local'] : 0,
            );
        }
        $linha = isset($item['linha']) ? (int) $item['linha'] : 0;
        if ($linha <= 0) {
            continue;
        }

        if (empty($candOut)) {
            $san[] = importador_orcamento_pdf_sugestao_nula($linha, 'Item sem candidatos para análise da IA');
            continue;
        }

        $basePayloadItem = array(
            'item_id' => $linha,
            'codigo' => isset($item['codigo']) ? (string) $item['codigo'] : '',
            'descricao' => isset($item['descricao']) ? (string) $item['descricao'] : '',
            'quantidade' => isset($item['quantidade']) ? (string) $item['quantidade'] : '',
            'unidade' => isset($item['unidade']) ? (string) $item['unidade'] : '',
            'candidatos' => array(),
        );

        $melhorSugestao = importador_orcamento_pdf_sugestao_nula($linha, 'A IA não encontrou sugestão confiável.');
        $lotesCandidatos = array_chunk($candOut, 30);

        foreach ($lotesCandidatos as $indiceLote => $candChunk) {
            $payloadItem = $basePayloadItem;
            $payloadItem['candidatos'] = $candChunk;

            $errBatch = '';
            $resultados = importador_orcamento_pdf_call_openai_chat($apiKey, $model, array($payloadItem), $errBatch);
            if ($resultados === null) {
                if ($error === '') {
                    $error = $errBatch !== '' ? $errBatch : 'Falha ao processar lote com IA.';
                }
                $sugestaoLote = importador_orcamento_pdf_sugestao_nula($linha, 'Erro ao processar lote ' . ($indiceLote + 1) . ' com IA');
                $melhorSugestao = importador_orcamento_pdf_escolher_melhor_sugestao_ia($melhorSugestao, $sugestaoLote);
                continue;
            }

            $respostaLinha = null;
            foreach ($resultados as $resultado) {
                if (isset($resultado['item_id']) && (int) $resultado['item_id'] === $linha) {
                    $respostaLinha = $resultado;
                    break;
                }
            }

            $sugestaoLote = importador_orcamento_pdf_sanitizar_sugestao_ia(
                $linha,
                $candChunk,
                $respostaLinha,
                'Item sem retorno da IA neste lote'
            );

            $melhorSugestao = importador_orcamento_pdf_escolher_melhor_sugestao_ia($melhorSugestao, $sugestaoLote);
            if (importador_orcamento_pdf_sugestao_ia_eh_forte($sugestaoLote)) {
                break;
            }
        }

        $san[] = $melhorSugestao;
    }

    return array(
        'gerado_em' => date('Y-m-d H:i:s'),
        'model' => $model,
        'erro' => $error,
        'itens' => $san,
    );
}

function importador_orcamento_pdf_normalize_text($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    $inputValue = $value;
    $stages = array(
        'input' => importador_orcamento_pdf_normalize_debug_snapshot($value),
    );
    if (preg_match('/^[\x00-\x7F]+$/', $value)) {
        $value = strtolower($value);
        $stages['after_ascii_strtolower'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    } else {
        $isUtf8 = function_exists('mb_check_encoding') ? mb_check_encoding($value, 'UTF-8') : true;
        $stages['mb_check_encoding_utf8'] = $isUtf8;
        if (!$isUtf8 && function_exists('mb_convert_encoding')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        }
        $stages['after_mb_convert_encoding'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
        $value = mb_strtolower($value, 'UTF-8');
        $stages['after_mb_strtolower'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($ascii !== false) {
            $value = $ascii;
        }
        $stages['after_iconv'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    }
    $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);
    $stages['after_preg_replace_non_alnum'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    $value = preg_replace('/\s+/u', ' ', trim((string) $value));
    $stages['after_preg_replace_spaces'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    importador_orcamento_pdf_normalize_debug_append('normalize_text', array(
        'input' => $stages['input'],
        'stages' => $stages,
        'output' => importador_orcamento_pdf_normalize_debug_snapshot($value),
        'input_text' => $inputValue,
    ));
    return $value;
}

function importador_orcamento_pdf_normalize_text_loose($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    $inputValue = $value;
    $stages = array(
        'input' => importador_orcamento_pdf_normalize_debug_snapshot($value),
    );
    if (preg_match('/^[\x00-\x7F]+$/', $value)) {
        $value = strtolower($value);
        $stages['after_ascii_strtolower'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    } else {
        $isUtf8 = function_exists('mb_check_encoding') ? mb_check_encoding($value, 'UTF-8') : true;
        $stages['mb_check_encoding_utf8'] = $isUtf8;
        if (!$isUtf8 && function_exists('mb_convert_encoding')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        }
        $stages['after_mb_convert_encoding'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
        $value = mb_strtolower($value, 'UTF-8');
        $stages['after_mb_strtolower'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($ascii !== false) {
            $value = $ascii;
        }
        $stages['after_iconv'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    }
    $value = str_replace(array('×', '*'), 'x', $value);
    $stages['after_replace_times'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    $value = preg_replace('/#\s+(\d+)/u', '#$1', $value);
    $stages['after_hash_compact'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    $value = preg_replace('/\b([a-z]{1,4})\s+(\d{2,8})\b/u', '$1-$2', $value);
    $stages['after_model_join'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    $value = preg_replace('/[;,]+/u', ' ', $value);
    $stages['after_punct_cleanup'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    $value = preg_replace('/\s*([\/\\\\\-\:\(\)\[\]])\s*/u', ' $1 ', $value);
    $stages['after_separator_spacing'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    $value = preg_replace('/\s+/u', ' ', trim((string) $value));
    $stages['after_preg_replace_spaces'] = importador_orcamento_pdf_normalize_debug_snapshot($value);
    importador_orcamento_pdf_normalize_debug_append('normalize_text_loose', array(
        'input' => $stages['input'],
        'stages' => $stages,
        'output' => importador_orcamento_pdf_normalize_debug_snapshot($value),
        'input_text' => $inputValue,
    ));
    return $value;
}

function importador_orcamento_pdf_feature_lexicon()
{
    static $lex = null;
    if ($lex !== null) {
        return $lex;
    }

    $lex = array(
        'stop_tokens' => array(
            'de', 'da', 'do', 'das', 'dos', 'para', 'com', 'sem', 'tipo', 'em', 'a', 'o', 'e',
            'mm', 'cm', 'm', 'un', 'und', 'pc', 'pca', 'peca', 'pecas', 'linha', 'modelo'
        ),
        'weak_tokens' => array(
            'de', 'da', 'do', 'das', 'dos', 'para', 'com', 'sem', 'tipo', 'em', 'a', 'o', 'e',
            'un', 'und', 'linha', 'modelo', 'item', 'cod', 'codigo', 'ref', 'referencia'
        ),
        'subtipos' => array(
            'tipo u', 'tipo c', 'tipo l', 'interna', 'externa', 'reto', 'reta', 'curva',
            'simples', 'duplo', 'dupla', 'macho', 'femea', 'esquerda', 'direita'
        ),
        'materiais' => array(
            'aco', 'inox', 'in-304', 'in-316', 'aluminio', 'al', 'plastico', 'pet', 'nylon',
            'latao', 'cobre', 'ferro', 'borracha', 'pvc', 'cpvc', 'pp', 'abs'
        ),
        'acabamentos' => array(
            'pz', 'gf', 'galv', 'galvanizado', 'zincado', 'polido', 'pintado', 'natural',
            'fosfatizado', 'bicromatizado', 'niquelado'
        ),
        'componentes_criticos' => array(
            'tampa', 'virola', 'fecho', 'pressao', 'fecho pressao', 'trava', 'rosca', 'valvula',
            'conector', 'adaptador', 'porca', 'arruela'
        ),
        'atributos_tecnicos' => array(
            'lisa', 'liso', 'perfurada', 'perfurado', 'encaixe', 'soldavel', 'rosqueavel',
            'reforcado', 'reforcada', '2 aguas', 'duas aguas', 'base perf', 'lat perf'
        ),
        'tipo_ignorados' => array(
            'com', 'para', 'tipo', 'simples', 'duplo', 'dupla', 'horizontal', 'vertical',
            'dimensao', 'linha', 'modelo'
        ),
    );

    return $lex;
}

function importador_orcamento_pdf_unique_values($values)
{
    if (!is_array($values)) {
        return array();
    }
    $out = array();
    foreach ($values as $value) {
        $value = trim((string) $value);
        if ($value === '') {
            continue;
        }
        $out[$value] = true;
    }
    return array_keys($out);
}

function importador_orcamento_pdf_array_intersection_values(array $a, array $b)
{
    return array_values(array_intersect(
        importador_orcamento_pdf_unique_values($a),
        importador_orcamento_pdf_unique_values($b)
    ));
}

function importador_orcamento_pdf_tokens_from_loose_text($value)
{
    $out = array();
    $value = importador_orcamento_pdf_normalize_text_loose($value);
    if (preg_match_all('/[a-z0-9#]+(?:[-\/x][a-z0-9#]+)*/u', $value, $m)) {
        foreach ($m[0] as $token) {
            $token = trim((string) $token);
            if ($token !== '') {
                $out[$token] = true;
            }
        }
    }
    return array_keys($out);
}

function importador_orcamento_pdf_normalize_model_token($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    if (function_exists('mb_convert_encoding')) {
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
    }
    $value = mb_strtolower($value, 'UTF-8');
    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($ascii !== false) {
        $value = $ascii;
    }
    $value = str_replace(array('/', '_', ' '), '-', $value);
    $value = preg_replace('/[^a-z0-9\-]+/u', '-', $value);
    $value = preg_replace('/-+/u', '-', $value);
    $value = trim((string) $value, '-');
    return $value;
}

function importador_orcamento_pdf_model_token_ruidoso($value, array $bloqueadosPrefixo = array())
{
    $value = trim((string) $value);
    if ($value === '') {
        return true;
    }

    $parts = explode('-', $value, 2);
    $prefixo = isset($parts[0]) ? trim((string) $parts[0]) : '';
    $sufixo = isset($parts[1]) ? trim((string) $parts[1]) : '';

    if ($prefixo === '' || $sufixo === '') {
        return true;
    }
    if (isset($bloqueadosPrefixo[$prefixo])) {
        return true;
    }
    if (preg_match('/^\d+(?:x\d+){1,3}$/u', $sufixo)) {
        return true;
    }
    if (preg_match('/^[ucl]$/u', $prefixo) && preg_match('/^\d+(?:x\d+){1,3}$/u', $sufixo)) {
        return true;
    }

    return false;
}

function importador_orcamento_pdf_model_prefixos_bloqueados()
{
    static $prefixos = null;
    if ($prefixos !== null) {
        return $prefixos;
    }

    $lex = importador_orcamento_pdf_feature_lexicon();
    $prefixos = array();
    foreach (array_merge($lex['materiais'], $lex['acabamentos'], $lex['stop_tokens'], $lex['weak_tokens']) as $token) {
        $prefixo = importador_orcamento_pdf_normalize_text($token);
        if ($prefixo !== '' && preg_match('/^[a-z]{1,8}$/u', $prefixo)) {
            $prefixos[$prefixo] = true;
        }
    }

    return $prefixos;
}

function importador_orcamento_pdf_extract_model_tokens($value)
{
    static $memo = array();
    $value = trim((string) $value);
    if ($value === '') {
        return array();
    }
    if (isset($memo[$value])) {
        return $memo[$value];
    }

    $lex = importador_orcamento_pdf_feature_lexicon();
    $bloqueados = array();
    foreach (array_merge($lex['materiais'], $lex['acabamentos'], $lex['stop_tokens'], $lex['weak_tokens']) as $token) {
        $canon = importador_orcamento_pdf_normalize_model_token($token);
        if ($canon !== '') {
            $bloqueados[$canon] = true;
        }
    }

    $bloqueadosPrefixo = array();
    foreach (array_merge($lex['materiais'], $lex['acabamentos'], $lex['stop_tokens'], $lex['weak_tokens']) as $token) {
        $prefixo = importador_orcamento_pdf_normalize_text($token);
        if ($prefixo !== '' && preg_match('/^[a-z]{1,8}$/u', $prefixo)) {
            $bloqueadosPrefixo[$prefixo] = true;
        }
    }

    $out = array();
    $textoLoose = importador_orcamento_pdf_normalize_text_loose($value);
    $textoBruto = importador_orcamento_pdf_normalize_text($value);

    if (preg_match_all('/\b([a-z]{1,8})\s*[-\/]\s*(\d{2,8}[a-z0-9]*)\b/u', $textoBruto, $mRawHifen, PREG_SET_ORDER)) {
        foreach ($mRawHifen as $match) {
            $prefixo = trim((string) $match[1]);
            $sufixo = trim((string) $match[2]);
            if ($prefixo === '' || $sufixo === '' || isset($bloqueadosPrefixo[$prefixo])) {
                continue;
            }
            $canon = importador_orcamento_pdf_normalize_model_token($prefixo . '-' . $sufixo);
            if ($canon === '' || isset($bloqueados[$canon]) || importador_orcamento_pdf_model_token_ruidoso($canon, $bloqueadosPrefixo)) {
                continue;
            }
            $out[$canon] = true;
        }
    }

    if (preg_match_all('/\b([a-z]{1,8})\s+(\d{3,8}[a-z0-9]*)\b/u', $textoBruto, $mRawEspaco, PREG_SET_ORDER)) {
        foreach ($mRawEspaco as $match) {
            $prefixo = trim((string) $match[1]);
            $sufixo = trim((string) $match[2]);
            if ($prefixo === '' || $sufixo === '' || isset($bloqueadosPrefixo[$prefixo])) {
                continue;
            }
            $canon = importador_orcamento_pdf_normalize_model_token($prefixo . '-' . $sufixo);
            if ($canon === '' || isset($bloqueados[$canon]) || importador_orcamento_pdf_model_token_ruidoso($canon, $bloqueadosPrefixo)) {
                continue;
            }
            $out[$canon] = true;
        }
    }

    if (preg_match_all('/\b[a-z]{1,8}\s*(?:[-\/]\s*)?\d{2,8}[a-z0-9\-\/]*\b/u', $textoLoose, $mCode)) {
        foreach ($mCode[0] as $candidate) {
            $canon = importador_orcamento_pdf_normalize_model_token($candidate);
            if ($canon === '' || isset($bloqueados[$canon]) || importador_orcamento_pdf_model_token_ruidoso($canon, $bloqueadosPrefixo)) {
                continue;
            }
            $out[$canon] = true;
        }
    }

    $tokens = importador_orcamento_pdf_tokens_from_loose_text($textoLoose);
    $total = count($tokens);
    for ($i = 0; $i < $total - 1; $i++) {
        $left = trim((string) $tokens[$i]);
        $right = trim((string) $tokens[$i + 1]);
        if (!preg_match('/^[a-z]{1,8}$/u', $left)) {
            continue;
        }
        if (isset($bloqueados[$left]) || isset($bloqueadosPrefixo[$left])) {
            continue;
        }
        if (!preg_match('/^\d{2,8}[a-z0-9]*$/u', $right)) {
            continue;
        }

        $canon = importador_orcamento_pdf_normalize_model_token($left . '-' . $right);
        if ($canon === '' || isset($bloqueados[$canon]) || importador_orcamento_pdf_model_token_ruidoso($canon, $bloqueadosPrefixo)) {
            continue;
        }
        $out[$canon] = true;
    }

    $memo[$value] = array_keys($out);
    return $memo[$value];
}

function importador_orcamento_pdf_tokens_relevantes($value)
{
    $stop = array(
        'de', 'da', 'do', 'das', 'dos', 'para', 'com', 'sem', 'tipo', 'em', 'a', 'o', 'e',
        'mm', 'un'
    );
    $parts = preg_split('/\s+/u', importador_orcamento_pdf_normalize_text($value), -1, PREG_SPLIT_NO_EMPTY);
    $out = array();
    foreach ($parts as $p) {
        if (importador_orcamento_pdf_token_parece_codigo($p)) {
            continue;
        }
        if (strlen($p) <= 2) {
            continue;
        }
        if (in_array($p, $stop, true)) {
            continue;
        }
        $out[$p] = true;
    }
    return array_keys($out);
}

function importador_orcamento_pdf_token_canonico($token)
{
    return importador_orcamento_pdf_normalize_text($token);
}

function importador_orcamento_pdf_token_parece_codigo($rawToken)
{
    $raw = trim((string) $rawToken);
    if ($raw === '') {
        return true;
    }

    $raw = preg_replace('/^[^\p{L}\p{N}]+|[^\p{L}\p{N}]+$/u', '', $raw);
    if ($raw === '') {
        return true;
    }

    if (preg_match('/^[A-Za-z]{1,8}[-\/]?\d+[A-Za-z0-9\-\/]*$/u', $raw)) {
        return true;
    }

    if (preg_match('/^\d{3,}$/u', $raw)) {
        return true;
    }

    $letters = preg_match_all('/[A-Za-zÀ-ÿ]/u', $raw, $mL);
    $digits = preg_match_all('/\d/u', $raw, $mD);
    $len = strlen(preg_replace('/\s+/u', '', $raw));

    if ($digits > 0 && $letters > 0) {
        return true;
    }

    if ($digits > 0 && $letters === 0) {
        return true;
    }

    if ($len <= 3 && preg_match('/^[A-Z]{1,3}$/u', $raw)) {
        return true;
    }

    return false;
}

function importador_orcamento_pdf_canon_medida($value)
{
    $v = mb_strtolower(trim((string) $value), 'UTF-8');
    if ($v === '') {
        return '';
    }

    $v = str_replace('×', 'x', $v);
    $v = preg_replace('/\s+/u', '', $v);
    $v = str_replace(',', '.', $v);

    if (preg_match('/^(\d+(?:\.\d+)?)m$/u', $v, $mm)) {
        $meters = (float) $mm[1];
        if ($meters > 0) {
            return (string) (int) round($meters * 1000);
        }
    }

    $v = preg_replace('/(mm|cm|m|kg|g|pol|")$/u', '', $v);
    $v = preg_replace('/\.0+(?=x|$)/u', '', $v);

    return trim((string) $v);
}

function importador_orcamento_pdf_medidas_split_principais_secundarias(array $medidas)
{
    $principais = array();
    $secundarias = array();

    foreach ($medidas as $medida) {
        $canon = importador_orcamento_pdf_canon_medida($medida);
        if ($canon === '') {
            continue;
        }

        if (strpos($canon, 'x') !== false) {
            $principais[$canon] = true;
        } else {
            $secundarias[$canon] = true;
        }
    }

    return array(
        'principais' => array_keys($principais),
        'secundarias' => array_keys($secundarias),
    );
}

function importador_orcamento_pdf_features_precisam_backfill_modelo(array $features, $codigo = '', $descricao = '')
{
    $modeloTokens = isset($features['modelo_tokens']) && is_array($features['modelo_tokens']) ? $features['modelo_tokens'] : array();
    $codigosModelos = isset($features['codigos_modelos']) && is_array($features['codigos_modelos']) ? $features['codigos_modelos'] : array();

    $modeloTokens = importador_orcamento_pdf_unique_values(array_values(array_filter(array_map('importador_orcamento_pdf_normalize_model_token', $modeloTokens))));
    $codigosModelos = importador_orcamento_pdf_unique_values(array_values(array_filter(array_map('importador_orcamento_pdf_normalize_model_token', $codigosModelos))));

    if (!empty($modeloTokens) || !empty($codigosModelos)) {
        return false;
    }

    $detectados = importador_orcamento_pdf_extract_model_tokens(trim((string) $codigo . ' ' . (string) $descricao));
    return !empty($detectados);
}

function importador_orcamento_pdf_parser_version()
{
    return '2026-05-17-modelo-v9';
}

function importador_orcamento_pdf_features_tem_schema_novo(array $features)
{
    return isset($features['medidas']) && is_array($features['medidas']) && isset($features['medidas']['principais']);
}

function importador_orcamento_pdf_normalize_features_schema(array $features)
{
    if (isset($features['medidas']) && is_array($features['medidas']) && isset($features['medidas']['principais'])) {
        $features['texto_normalizado'] = isset($features['texto_normalizado']) ? (string) $features['texto_normalizado'] : '';
        $features['tipo_principal'] = isset($features['tipo_principal']) ? trim((string) $features['tipo_principal']) : '';
        $features['subtipos'] = isset($features['subtipos']) && is_array($features['subtipos']) ? importador_orcamento_pdf_unique_values($features['subtipos']) : array();
        $features['medidas']['principais'] = isset($features['medidas']['principais']) && is_array($features['medidas']['principais']) ? importador_orcamento_pdf_unique_values($features['medidas']['principais']) : array();
        $features['medidas']['secundarias'] = isset($features['medidas']['secundarias']) && is_array($features['medidas']['secundarias']) ? importador_orcamento_pdf_unique_values($features['medidas']['secundarias']) : array();
        $modeloTokensRaw = isset($features['modelo_tokens']) && is_array($features['modelo_tokens']) ? $features['modelo_tokens'] : array();
        $modeloTokens = array();
        foreach ($modeloTokensRaw as $token) {
            $canon = importador_orcamento_pdf_normalize_model_token($token);
            if ($canon !== '') {
                $modeloTokens[] = $canon;
            }
        }
        $features['modelo_tokens'] = importador_orcamento_pdf_unique_values($modeloTokens);
        $features['materiais'] = isset($features['materiais']) && is_array($features['materiais']) ? importador_orcamento_pdf_unique_values($features['materiais']) : array();
        $features['acabamentos'] = isset($features['acabamentos']) && is_array($features['acabamentos']) ? importador_orcamento_pdf_unique_values($features['acabamentos']) : array();
        $features['espessuras'] = isset($features['espessuras']) && is_array($features['espessuras']) ? importador_orcamento_pdf_unique_values($features['espessuras']) : array();
        $features['componentes_criticos'] = isset($features['componentes_criticos']) && is_array($features['componentes_criticos']) ? importador_orcamento_pdf_unique_values($features['componentes_criticos']) : array();
        $features['atributos_tecnicos'] = isset($features['atributos_tecnicos']) && is_array($features['atributos_tecnicos']) ? importador_orcamento_pdf_unique_values($features['atributos_tecnicos']) : array();
        $features['tokens_relevantes'] = isset($features['tokens_relevantes']) && is_array($features['tokens_relevantes']) ? importador_orcamento_pdf_unique_values($features['tokens_relevantes']) : array();
        $features['tokens_fracos'] = isset($features['tokens_fracos']) && is_array($features['tokens_fracos']) ? importador_orcamento_pdf_unique_values($features['tokens_fracos']) : array();
        $features['numeros_soltos'] = isset($features['numeros_soltos']) && is_array($features['numeros_soltos']) ? importador_orcamento_pdf_unique_values($features['numeros_soltos']) : array();
        $codigosModelosRaw = isset($features['codigos_modelos']) && is_array($features['codigos_modelos'])
            ? $features['codigos_modelos']
            : (isset($features['modelo_tokens']) && is_array($features['modelo_tokens']) ? $features['modelo_tokens'] : array());
        $codigosModelos = array();
        foreach ($codigosModelosRaw as $token) {
            $canon = importador_orcamento_pdf_normalize_model_token($token);
            if ($canon !== '') {
                $codigosModelos[] = $canon;
            }
        }
        $features['codigos_modelos'] = importador_orcamento_pdf_unique_values($codigosModelos);
        $features['numeros'] = isset($features['numeros']) && is_array($features['numeros']) ? importador_orcamento_pdf_unique_values($features['numeros']) : $features['numeros_soltos'];
        $features['atributos'] = isset($features['atributos']) && is_array($features['atributos']) ? importador_orcamento_pdf_unique_values($features['atributos']) : $features['atributos_tecnicos'];
        return $features;
    }

    $textoNormalizado = isset($features['texto_normalizado']) ? (string) $features['texto_normalizado'] : '';
    $tipoPrincipal = isset($features['tipo_principal']) ? trim((string) $features['tipo_principal']) : '';
    $tokensRelevantes = isset($features['tokens_relevantes']) && is_array($features['tokens_relevantes']) ? $features['tokens_relevantes'] : array();
    $tokensFracos = isset($features['tokens_fracos']) && is_array($features['tokens_fracos']) ? $features['tokens_fracos'] : array();
    $modeloTokens = array();
    if (isset($features['modelo_tokens']) && is_array($features['modelo_tokens'])) {
        $modeloTokens = $features['modelo_tokens'];
    } elseif (isset($features['codigos_modelos']) && is_array($features['codigos_modelos'])) {
        foreach ($features['codigos_modelos'] as $token) {
            $canon = importador_orcamento_pdf_normalize_model_token((string) $token);
            if ($canon !== '') {
                $modeloTokens[] = $canon;
            }
        }
    }

    $medidasOrig = isset($features['medidas']) && is_array($features['medidas']) ? $features['medidas'] : array();
    $medidas = importador_orcamento_pdf_medidas_split_principais_secundarias($medidasOrig);
    $textoBusca = trim($textoNormalizado . ' ' . implode(' ', $tokensRelevantes) . ' ' . implode(' ', $modeloTokens) . ' ' . implode(' ', $medidasOrig));
    $textoBusca = importador_orcamento_pdf_normalize_text_loose($textoBusca);
    $lex = importador_orcamento_pdf_feature_lexicon();

    $subtipos = array();
    if (preg_match_all('/\btipo\s+([a-z0-9]+)\b/u', $textoBusca, $mTipoSub)) {
        foreach ($mTipoSub[1] as $v) {
            $subtipos['tipo ' . trim((string) $v)] = true;
        }
    }
    foreach ($lex['subtipos'] as $s) {
        if (strpos($textoBusca, $s) !== false) {
            $subtipos[$s] = true;
        }
    }

    $materiais = array();
    foreach ($lex['materiais'] as $m) {
        if (preg_match('/(^|[^a-z0-9])' . preg_quote($m, '/') . '([^a-z0-9]|$)/u', $textoBusca)) {
            $materiais[$m] = true;
        }
    }

    $acabamentos = array();
    foreach ($lex['acabamentos'] as $a) {
        if (preg_match('/(^|[^a-z0-9])' . preg_quote($a, '/') . '([^a-z0-9]|$)/u', $textoBusca)) {
            $acabamentos[$a] = true;
        }
    }

    $espessuras = array();
    if (preg_match_all('/#\d+\b/u', $textoBusca, $mEspHash)) {
        foreach ($mEspHash[0] as $v) {
            $espessuras[$v] = true;
        }
    }
    foreach ($medidas['secundarias'] as $sec) {
        if (preg_match('/^\d+(?:\.\d+)?mm$/u', $sec)) {
            $espessuras[$sec] = true;
        }
    }

    $componentesCriticos = array();
    foreach ($lex['componentes_criticos'] as $c) {
        if (strpos($textoBusca, $c) !== false) {
            $componentesCriticos[$c] = true;
        }
    }

    $atributosTecnicos = array();
    if (isset($features['atributos_tecnicos']) && is_array($features['atributos_tecnicos'])) {
        foreach ($features['atributos_tecnicos'] as $a) {
            $atributosTecnicos[(string) $a] = true;
        }
    } elseif (isset($features['atributos']) && is_array($features['atributos'])) {
        foreach ($features['atributos'] as $a) {
            $atributosTecnicos[(string) $a] = true;
        }
    }
    foreach ($lex['atributos_tecnicos'] as $a) {
        if (strpos($textoBusca, $a) !== false) {
            $atributosTecnicos[$a] = true;
        }
    }

    $numerosSoltos = array();
    if (isset($features['numeros_soltos']) && is_array($features['numeros_soltos'])) {
        $numerosSoltos = $features['numeros_soltos'];
    } elseif (isset($features['numeros']) && is_array($features['numeros'])) {
        $numerosSoltos = array_map('strval', $features['numeros']);
    }

    return array(
        'texto_normalizado' => $textoNormalizado,
        'tipo_principal' => $tipoPrincipal,
        'subtipos' => array_keys($subtipos),
        'medidas' => array(
            'principais' => importador_orcamento_pdf_unique_values($medidas['principais']),
            'secundarias' => importador_orcamento_pdf_unique_values($medidas['secundarias']),
        ),
        'modelo_tokens' => importador_orcamento_pdf_unique_values($modeloTokens),
        'materiais' => array_keys($materiais),
        'acabamentos' => array_keys($acabamentos),
        'espessuras' => array_keys($espessuras),
        'componentes_criticos' => array_keys($componentesCriticos),
        'atributos_tecnicos' => importador_orcamento_pdf_unique_values(array_keys($atributosTecnicos)),
        'tokens_relevantes' => importador_orcamento_pdf_unique_values($tokensRelevantes),
        'tokens_fracos' => importador_orcamento_pdf_unique_values($tokensFracos),
        'numeros_soltos' => importador_orcamento_pdf_unique_values($numerosSoltos),
        'codigos_modelos' => importador_orcamento_pdf_unique_values($modeloTokens),
        'numeros' => importador_orcamento_pdf_unique_values($numerosSoltos),
        'atributos' => importador_orcamento_pdf_unique_values(array_keys($atributosTecnicos)),
    );
}

function importador_orcamento_pdf_extract_features($codigo, $descricao)
{
    $codigo = trim((string) $codigo);
    $descricao = trim((string) $descricao);
    $texto = trim($codigo . ' ' . $descricao);
    $lex = importador_orcamento_pdf_feature_lexicon();
    $textoLoose = importador_orcamento_pdf_normalize_text_loose($texto);
    $textoNorm = importador_orcamento_pdf_normalize_text($texto);
    $tokensOriginais = importador_orcamento_pdf_tokens_from_loose_text($textoLoose);
    $tokensRelevantesBase = importador_orcamento_pdf_tokens_relevantes($texto);
    $tokensRelevantes = array();
    $tokensFracos = array();
    foreach ($tokensOriginais as $token) {
        if ($token === '' || importador_orcamento_pdf_token_parece_codigo($token)) {
            continue;
        }
        if (preg_match('/^\d+(?:[.,]\d+)?$/u', $token)) {
            continue;
        }
        if (in_array($token, $lex['weak_tokens'], true)) {
            $tokensFracos[$token] = true;
            continue;
        }
        if (strlen($token) <= 1) {
            continue;
        }
        $tokensRelevantes[$token] = true;
    }
    foreach ($tokensRelevantesBase as $token) {
        $tokensRelevantes[$token] = true;
    }

    $descricaoSemPrefixo = $descricao;
    if (preg_match('/^\s*([^\s:–-]+)\s*[:–-]\s*(.+)$/u', $descricao, $mSep)) {
        $prefixo = trim((string) $mSep[1]);
        if (importador_orcamento_pdf_token_parece_codigo($prefixo) || strlen(importador_orcamento_pdf_normalize_text($prefixo)) <= 3) {
            $descricaoSemPrefixo = trim((string) $mSep[2]);
        }
    }

    $tipoPrincipal = '';
    foreach (importador_orcamento_pdf_tokens_from_loose_text($descricaoSemPrefixo) as $token) {
        if ($token === '' || importador_orcamento_pdf_token_parece_codigo($token)) {
            continue;
        }
        if (preg_match('/^\d+$/', $token)) {
            continue;
        }
        if (in_array($token, $lex['tipo_ignorados'], true) || in_array($token, $lex['weak_tokens'], true)) {
            continue;
        }
        if (strlen($token) < 2) {
            continue;
        }
        $tipoPrincipal = $token;
        break;
    }

    $modeloTokens = array_fill_keys(importador_orcamento_pdf_extract_model_tokens($texto), true);
    if (empty($modeloTokens)) {
        $bloqueadosPrefixo = importador_orcamento_pdf_model_prefixos_bloqueados();
        if (preg_match_all('/\b([a-z]{1,8})\s+(\d{3,8}[a-z0-9]*)\b/u', $textoNorm, $mModeloFallback, PREG_SET_ORDER)) {
            foreach ($mModeloFallback as $match) {
                $prefixo = trim((string) $match[1]);
                $sufixo = trim((string) $match[2]);
                if ($prefixo === '' || $sufixo === '' || isset($bloqueadosPrefixo[$prefixo])) {
                    continue;
                }
                $canon = importador_orcamento_pdf_normalize_model_token($prefixo . '-' . $sufixo);
                if ($canon === '' || importador_orcamento_pdf_model_token_ruidoso($canon, $bloqueadosPrefixo)) {
                    continue;
                }
                $modeloTokens[$canon] = true;
            }
        }
    }

    $medidas = array();
    if (preg_match_all('/\b\d+(?:[.,]\d+)?(?:\s*x\s*\d+(?:[.,]\d+)?){1,2}\s*(?:mm|cm|m|pol|")?\b/iu', $textoLoose, $mMedComp)) {
        foreach ($mMedComp[0] as $v) {
            $n = importador_orcamento_pdf_canon_medida($v);
            if ($n !== '') {
                $medidas[$n] = true;
            }
        }
    }
    if (preg_match_all('/\b\d+(?:[.,]\d+)?\s*(?:mm|cm|m|pol|")\b/iu', $textoLoose, $mMedLin)) {
        foreach ($mMedLin[0] as $v) {
            $n = importador_orcamento_pdf_canon_medida($v);
            if ($n !== '') {
                $medidas[$n] = true;
            }
        }
    }
    if (preg_match_all('/\b\d+\s*(?:[.\-]|\s)\s*\d+\s*\/\s*\d+\s*(?:pol|")?\b/iu', $textoLoose, $mMedMista)) {
        foreach ($mMedMista[0] as $v) {
            $n = importador_orcamento_pdf_canon_medida($v);
            if ($n !== '') {
                $medidas[$n] = true;
            }
        }
    }
    if (preg_match_all('/\b\d+\s*\/\s*\d+\b/u', $textoLoose, $mFrac)) {
        foreach ($mFrac[0] as $v) {
            $n = importador_orcamento_pdf_canon_medida($v);
            if ($n !== '') {
                $medidas[$n] = true;
            }
        }
    }
    $splitMedidas = importador_orcamento_pdf_medidas_split_principais_secundarias(array_keys($medidas));

    $subtipos = array();
    if (preg_match_all('/\btipo\s+([a-z0-9]+)\b/u', $textoLoose, $mTipoSub)) {
        foreach ($mTipoSub[1] as $v) {
            $subtipos['tipo ' . trim((string) $v)] = true;
        }
    }
    foreach ($lex['subtipos'] as $s) {
        if (strpos($textoLoose, $s) !== false) {
            $subtipos[$s] = true;
        }
    }

    $materiais = array();
    foreach ($lex['materiais'] as $m) {
        if (preg_match('/(^|[^a-z0-9])' . preg_quote($m, '/') . '([^a-z0-9]|$)/u', $textoLoose)) {
            $materiais[$m] = true;
        }
    }

    $acabamentos = array();
    foreach ($lex['acabamentos'] as $a) {
        if (preg_match('/(^|[^a-z0-9])' . preg_quote($a, '/') . '([^a-z0-9]|$)/u', $textoLoose)) {
            $acabamentos[$a] = true;
        }
    }

    $espessuras = array();
    if (preg_match_all('/#\d+\b/u', $textoLoose, $mEspHash)) {
        foreach ($mEspHash[0] as $v) {
            $espessuras[$v] = true;
        }
    }
    if (preg_match_all('/\b\d+(?:[.,]\d+)?mm\b/u', $textoLoose, $mEspMm)) {
        foreach ($mEspMm[0] as $v) {
            $espessuras[preg_replace('/\s+/u', '', $v)] = true;
        }
    }

    $componentesCriticos = array();
    foreach ($lex['componentes_criticos'] as $c) {
        if (strpos($textoLoose, $c) !== false) {
            $componentesCriticos[$c] = true;
        }
    }

    $atributosTecnicos = array();
    foreach ($lex['atributos_tecnicos'] as $a) {
        if (strpos($textoLoose, $a) !== false) {
            $atributosTecnicos[$a] = true;
        }
    }
    foreach (array_keys($tokensRelevantes) as $token) {
        if ($token === $tipoPrincipal) {
            continue;
        }
        if (isset($materiais[$token]) || isset($acabamentos[$token]) || isset($componentesCriticos[$token]) || isset($subtipos[$token])) {
            continue;
        }
        if (strlen($token) >= 5) {
            $atributosTecnicos[$token] = true;
        }
    }

    $numerosSoltos = array();
    if (preg_match_all('/\b\d+(?:[.,]\d+)?\b/u', $textoLoose, $mNum)) {
        foreach ($mNum[0] as $n) {
            $nn = trim((string) str_replace(',', '.', $n));
            if ($nn !== '') {
                $numerosSoltos[$nn] = true;
            }
        }
    }

    $features = importador_orcamento_pdf_normalize_features_schema(array(
        'texto_normalizado' => $textoNorm,
        'tipo_principal' => $tipoPrincipal,
        'subtipos' => array_keys($subtipos),
        'medidas' => array(
            'principais' => importador_orcamento_pdf_unique_values($splitMedidas['principais']),
            'secundarias' => importador_orcamento_pdf_unique_values($splitMedidas['secundarias']),
        ),
        'modelo_tokens' => array_keys($modeloTokens),
        'materiais' => array_keys($materiais),
        'acabamentos' => array_keys($acabamentos),
        'espessuras' => array_keys($espessuras),
        'componentes_criticos' => array_keys($componentesCriticos),
        'atributos_tecnicos' => array_keys($atributosTecnicos),
        'tokens_relevantes' => array_keys($tokensRelevantes),
        'tokens_fracos' => array_keys($tokensFracos),
        'numeros_soltos' => array_keys($numerosSoltos),
        // Compatibilidade com a estrutura antiga
        'codigos_modelos' => array_keys($modeloTokens),
        'numeros' => array_keys($numerosSoltos),
        'atributos' => array_keys($atributosTecnicos),
    ));

    importador_orcamento_pdf_normalize_debug_append('extract_features_legacy', array(
        'codigo' => $codigo,
        'descricao' => $descricao,
        'texto' => $texto,
        'input' => importador_orcamento_pdf_normalize_debug_snapshot($texto),
        'texto_normalizado' => $textoNorm,
        'texto_loose' => $textoLoose,
        'tokens_originais' => array_values($tokensOriginais),
        'tokens_finais' => isset($features['tokens_relevantes']) ? $features['tokens_relevantes'] : array(),
        'tipo_principal' => isset($features['tipo_principal']) ? $features['tipo_principal'] : '',
    ));

    return $features;
}

function importador_orcamento_pdf_medidas_para_comparacao(array $medidas)
{
    if (isset($medidas['principais']) || isset($medidas['secundarias'])) {
        $merged = array();
        if (isset($medidas['principais']) && is_array($medidas['principais'])) {
            $merged = array_merge($merged, $medidas['principais']);
        }
        if (isset($medidas['secundarias']) && is_array($medidas['secundarias'])) {
            $merged = array_merge($merged, $medidas['secundarias']);
        }
        $medidas = $merged;
    }
    $out = array_values(array_unique(array_filter(array_map('strval', $medidas))));
    $hasComposta = false;
    foreach ($out as $m) {
        if (strpos($m, 'x') !== false) {
            $hasComposta = true;
            break;
        }
    }

    // Se houver medida composta, ignora medidas isoladas muito curtas (ex.: "20"),
    // mas preserva medidas longas relevantes (ex.: "3000" equivalente a "3m").
    if ($hasComposta) {
        $out = array_values(array_filter($out, function ($m) {
            if (strpos($m, 'x') !== false) {
                return true;
            }
            if (preg_match('/^\d+$/', $m)) {
                return strlen($m) >= 4;
            }
            return preg_match('/^\d+(?:\.\d+)?m$/u', $m) === 1;
        }));
    }

    return $out;
}

function importador_orcamento_pdf_medida_num_norm($v)
{
    $n = trim((string) $v);
    if ($n === '') {
        return '';
    }
    $n = str_replace(',', '.', $n);
    if (!preg_match('/^\d+(?:\.\d+)?$/', $n)) {
        return '';
    }
    $n = preg_replace('/\.0+$/', '', $n);
    return $n;
}

function importador_orcamento_pdf_medidas_match_info(array $itemMedidas, array $prodMedidas)
{
    $item = importador_orcamento_pdf_medidas_para_comparacao($itemMedidas);
    $prod = importador_orcamento_pdf_medidas_para_comparacao($prodMedidas);
    $diretas = array_values(array_intersect($item, $prod));

    $item2d = array();
    $item3d = array();
    $itemLen = array();
    foreach ($item as $m) {
        if (preg_match('/^(\d+(?:\.\d+)?)m$/u', $m, $mm)) {
            $itemLen[] = (string) (int) round(((float) $mm[1]) * 1000);
            continue;
        }
        if (preg_match('/^\d+$/', $m)) {
            $itemLen[] = (string) (int) $m;
            continue;
        }
        if (strpos($m, 'x') !== false) {
            $rawParts = explode('x', (string) $m);
            if (!is_array($rawParts)) {
                $rawParts = array();
            }
            $parts = array_values(array_filter(array_map('trim', $rawParts), function ($p) { return $p !== ''; }));
            if (!is_array($parts)) {
                $parts = array();
            }
            $parts = array_values(array_filter(array_map(function ($v) {
                return importador_orcamento_pdf_medida_num_norm($v);
            }, $parts), function ($p) { return $p !== ''; }));
            if (!is_array($parts)) {
                $parts = array();
            }
            if (count($parts) === 2) {
                $item2d[] = $parts;
            } elseif (count($parts) === 3) {
                $item3d[] = $parts;
            }
        }
    }

    $prod2d = array();
    $prod3d = array();
    $prodLen = array();
    foreach ($prod as $m) {
        if (preg_match('/^(\d+(?:\.\d+)?)m$/u', $m, $mm)) {
            $prodLen[] = (string) (int) round(((float) $mm[1]) * 1000);
            continue;
        }
        if (preg_match('/^\d+$/', $m)) {
            $prodLen[] = (string) (int) $m;
            continue;
        }
        if (strpos($m, 'x') !== false) {
            $rawParts = explode('x', (string) $m);
            if (!is_array($rawParts)) {
                $rawParts = array();
            }
            $parts = array_values(array_filter(array_map('trim', $rawParts), function ($p) { return $p !== ''; }));
            if (!is_array($parts)) {
                $parts = array();
            }
            $parts = array_values(array_filter(array_map(function ($v) {
                return importador_orcamento_pdf_medida_num_norm($v);
            }, $parts), function ($p) { return $p !== ''; }));
            if (!is_array($parts)) {
                $parts = array();
            }
            if (count($parts) === 2) {
                $prod2d[] = $parts;
            } elseif (count($parts) === 3) {
                $prod3d[] = $parts;
            }
        }
    }

    $equiv = array();
    $pairEqual2 = function ($a, $b) {
        return ($a[0] === $b[0] && $a[1] === $b[1]) || ($a[0] === $b[1] && $a[1] === $b[0]);
    };

    foreach ($item2d as $i2) {
        foreach ($itemLen as $len) {
            foreach ($prod3d as $p3) {
                if ($pairEqual2($i2, array($p3[0], $p3[1])) && $len === $p3[2]) {
                    $equiv[] = implode('x', $i2) . ' + ' . $len . 'mm ≈ ' . implode('x', $p3);
                }
            }
        }
    }
    foreach ($prod2d as $p2) {
        foreach ($prodLen as $len) {
            foreach ($item3d as $i3) {
                if ($pairEqual2($p2, array($i3[0], $i3[1])) && $len === $i3[2]) {
                    $equiv[] = implode('x', $p2) . ' + ' . $len . 'mm ≈ ' . implode('x', $i3);
                }
            }
        }
    }
    foreach ($item3d as $i3) {
        foreach ($prod3d as $p3) {
            if ($pairEqual2(array($i3[0], $i3[1]), array($p3[0], $p3[1])) && $i3[2] === $p3[2]) {
                $equiv[] = implode('x', $i3) . ' ≈ ' . implode('x', $p3);
            }
        }
    }
    $equiv = array_values(array_unique($equiv));

    return array(
        'item' => $item,
        'produto' => $prod,
        'diretas' => array_values(array_unique($diretas)),
        'equivalentes' => $equiv,
        'tem_match' => !empty($diretas) || !empty($equiv),
    );
}

function importador_orcamento_pdf_cache_paths($baseDirImportacao)
{
    $moduleBase = dirname(dirname((string) $baseDirImportacao));
    $cacheDir = $moduleBase . '/cache';
    return array(
        'dir' => $cacheDir,
        'tmp' => $cacheDir . '/produtos_cache.tmp.json',
        'final' => $cacheDir . '/produtos_cache.json',
        'index' => $cacheDir . '/produtos_index.json',
        'lock' => $cacheDir . '/produtos_cache.lock',
    );
}

function importador_orcamento_pdf_cache_lock_acquire($lockPath)
{
    $lockPath = trim((string) $lockPath);
    if ($lockPath === '') {
        return array(false, null);
    }
    $lockDir = dirname($lockPath);
    if (!importador_orcamento_pdf_ensure_directory($lockDir)) {
        return array(false, null);
    }
    $fp = @fopen($lockPath, 'c');
    if (!$fp) {
        return array(false, null);
    }
    if (!@flock($fp, LOCK_EX | LOCK_NB)) {
        @fclose($fp);
        return array(false, null);
    }
    return array(true, $fp);
}

function importador_orcamento_pdf_cache_lock_release($lockFp)
{
    if (!is_resource($lockFp)) {
        return;
    }
    @flock($lockFp, LOCK_UN);
    @fclose($lockFp);
}

function importador_orcamento_pdf_cache_produtos_expirado($cachePath, $ttlSeconds = 21600, $indexPath = '')
{
    $ttlSeconds = (int) $ttlSeconds > 0 ? (int) $ttlSeconds : 21600;
    if (!is_file($cachePath)) {
        return true;
    }
    if ($indexPath !== '' && !is_file($indexPath)) {
        return true;
    }

    $meta = importador_orcamento_pdf_load_produtos_cache_meta($cachePath);
    if (empty($meta['tem_produtos'])) {
        return true;
    }
    if (!isset($meta['parser_version']) || (string) $meta['parser_version'] !== importador_orcamento_pdf_parser_version()) {
        return true;
    }
    $geradoEm = isset($meta['gerado_em']) ? strtotime((string) $meta['gerado_em']) : false;
    if ($geradoEm === false || $geradoEm <= 0) {
        $geradoEm = @filemtime($cachePath);
    }
    if ($geradoEm === false || $geradoEm <= 0) {
        return true;
    }
    if ($indexPath !== '') {
        $indexMeta = importador_orcamento_pdf_load_json_file($indexPath);
        $indexVersion = isset($indexMeta['parser_version']) ? (string) $indexMeta['parser_version'] : '';
        if ($indexVersion !== importador_orcamento_pdf_parser_version()) {
            return true;
        }
    }

    return (time() - (int) $geradoEm) >= $ttlSeconds;
}

function importador_orcamento_pdf_gerar_cache_produtos($cacheFinal, $cacheTmp, $indexPath = '')
{
    if (function_exists('set_time_limit')) {
        @set_time_limit(0);
    }

    $conn = importador_orcamento_pdf_db();
    $connExists = $conn ? 'S' : 'N';
    $connIsMysqli = (is_object($conn) && ($conn instanceof \mysqli)) ? 'S' : 'N';
    error_log('[importadorOrcamentoPdf] cache produtos: conn_existe=' . $connExists . ' conn_mysqli=' . $connIsMysqli);

    if (!$conn || !$connIsMysqli) {
        error_log('[importadorOrcamentoPdf] cache produtos: conexao invalida para consulta.');
        return array(false, 'Conexao com banco indisponivel.');
    }

    $sql = "SELECT D001_Id, D001_Codigo_Produto, D001_Descricao_Produto
              FROM D001
             WHERE (D001_Flag_Ativo != 'N' OR D001_Flag_Ativo IS NULL)
             ORDER BY D001_Id ASC";
    error_log('[importadorOrcamentoPdf] cache produtos SQL FINAL: ' . $sql);

    $tmpPath = $cacheTmp . '.stream';
    @unlink($tmpPath);
    @unlink($cacheTmp);

    $fh = @fopen($tmpPath, 'wb');
    if (!$fh) {
        return array(false, 'Não foi possível criar o cache temporário de produtos.');
    }

    $res = @$conn->query($sql, MYSQLI_USE_RESULT);
    if (!$res) {
        error_log('[importadorOrcamentoPdf] erro SQL cache produtos: mysqli_errno=' . mysqli_errno($conn) . ' mysqli_error=' . mysqli_error($conn));
        @fclose($fh);
        @unlink($tmpPath);
        return array(false, 'Não foi possível consultar produtos ativos.');
    }

    $geradoEm = date('Y-m-d H:i:s');
    $total = 0;
    $first = true;
    $lastId = null;
    $header = "{\n"
        . '    "gerado_em": ' . json_encode($geradoEm, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ",\n"
        . '    "parser_version": ' . json_encode(importador_orcamento_pdf_parser_version(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ",\n"
        . '    "total": %20d,' . "\n"
        . '    "produtos": [' . "\n";

    if (@fwrite($fh, sprintf($header, 0)) === false) {
        @$res->close();
        @fclose($fh);
        @unlink($tmpPath);
        return array(false, 'Não foi possível iniciar a gravação do cache de produtos.');
    }

    while ($row = $res->fetch_assoc()) {
        $id = isset($row['D001_Id']) ? (int) $row['D001_Id'] : 0;
        if ($id <= 0) {
            continue;
        }
        if ($lastId !== null && $id === $lastId) {
            continue;
        }
        $lastId = $id;

        $codigo = isset($row['D001_Codigo_Produto']) ? trim((string) $row['D001_Codigo_Produto']) : '';
        $descricao = isset($row['D001_Descricao_Produto']) ? trim((string) $row['D001_Descricao_Produto']) : '';
        $busca = importador_orcamento_pdf_normalize_text(trim($codigo . ' ' . $descricao));
        $produto = array(
            'D001_Id' => $id,
            'codigo' => $codigo,
            'descricao' => $descricao,
            'busca' => is_string($busca) ? $busca : '',
        );

        $json = json_encode($produto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            continue;
        }

        $prefix = $first ? '        ' : ",\n        ";
        if (@fwrite($fh, $prefix . $json) === false) {
            @$res->close();
            @fclose($fh);
            @unlink($tmpPath);
            return array(false, 'Não foi possível gravar o cache de produtos.');
        }

        $first = false;
        $total++;

        if (($total % 500) === 0) {
            @fflush($fh);
        }
    }

    @$res->close();

    if (@fwrite($fh, "\n    ]\n}\n") === false) {
        @fclose($fh);
        @unlink($tmpPath);
        return array(false, 'Não foi possível finalizar o cache de produtos.');
    }

    @fflush($fh);

    $totalFieldOffset = strlen("{\n")
        + strlen('    "gerado_em": ' . json_encode($geradoEm, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ",\n")
        + strlen('    "parser_version": ' . json_encode(importador_orcamento_pdf_parser_version(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ",\n")
        + strlen('    "total": ');
    if (@fseek($fh, $totalFieldOffset) === 0) {
        @fwrite($fh, sprintf('%20d', $total));
    }

    @fclose($fh);

    if (!@rename($tmpPath, $cacheFinal)) {
        @unlink($tmpPath);
        return array(false, 'Não foi possível finalizar o cache de produtos.');
    }
    @unlink($tmpPath);

    error_log('[importadorOrcamentoPdf] cache produtos finalizado: total=' . (int) $total);

    return array(true, '');
}

function importador_orcamento_pdf_busca_runtime_texto_item(array $item)
{
    $codigo = isset($item['codigo']) ? trim((string) $item['codigo']) : '';
    $descricao = isset($item['descricao']) ? trim((string) $item['descricao']) : '';
    return trim($codigo . ' ' . $descricao);
}

function importador_orcamento_pdf_enrichment_min_confidence()
{
    return 0.85;
}

function importador_orcamento_pdf_load_enrichment_for_candidates($previewPath)
{
    $result = array(
        'available' => false,
        'reason' => 'arquivo_ausente',
        'items_by_source_index' => array(),
    );
    $baseDir = dirname((string) $previewPath);
    $enrichmentInputPath = $baseDir . '/import_preview_enrichment_input.json';
    $enrichmentPreviewPath = is_file($enrichmentInputPath) ? $enrichmentInputPath : $previewPath;
    $enrichedPath = $baseDir . '/import_preview_enriched.json';
    if (!is_file($enrichedPath)) {
        return $result;
    }

    $previewRaw = @file_get_contents($enrichmentPreviewPath);
    $enrichedRaw = @file_get_contents($enrichedPath);
    $enriched = is_string($enrichedRaw) ? json_decode($enrichedRaw, true) : null;
    if (!is_string($previewRaw) || !is_array($enriched)) {
        $result['reason'] = 'json_invalido';
        return $result;
    }
    if (!isset($enriched['preview_sha256']) || !is_string($enriched['preview_sha256'])
        || !hash_equals(hash('sha256', $previewRaw), $enriched['preview_sha256'])) {
        $result['reason'] = 'preview_sha256_divergente';
        return $result;
    }
    if (!isset($enriched['status']) || !in_array($enriched['status'], array('success', 'partial'), true)) {
        $result['reason'] = 'status_raiz_nao_utilizavel';
        return $result;
    }
    if (!isset($enriched['items']) || !is_array($enriched['items'])) {
        $result['reason'] = 'items_invalidos';
        return $result;
    }

    foreach ($enriched['items'] as $enrichedItem) {
        if (!is_array($enrichedItem) || !isset($enrichedItem['source_index']) || !is_int($enrichedItem['source_index'])) {
            continue;
        }
        $result['items_by_source_index'][(string) $enrichedItem['source_index']] = $enrichedItem;
    }
    $result['available'] = true;
    $result['reason'] = '';
    return $result;
}

function importador_orcamento_pdf_enrichment_candidate_input(array $enrichmentContext, $linha, $descricaoOriginalOuRevisada)
{
    $out = array(
        'available' => !empty($enrichmentContext['available']),
        'eligible' => false,
        'reason' => '',
        'text' => '',
        'aliases_received' => 0,
        'aliases_discarded' => 0,
        'aliases_discard_reasons' => array('identical_to_original' => 0, 'empty' => 0, 'duplicate' => 0),
        'calculated_thickness' => '',
        'has_pending_confirmations' => false,
    );
    if (empty($enrichmentContext['available'])) {
        $out['reason'] = isset($enrichmentContext['reason']) ? (string) $enrichmentContext['reason'] : 'indisponivel';
        return $out;
    }

    $sourceIndex = (int) $linha - 1;
    $items = isset($enrichmentContext['items_by_source_index']) && is_array($enrichmentContext['items_by_source_index'])
        ? $enrichmentContext['items_by_source_index'] : array();
    if (!isset($items[(string) $sourceIndex]) || !is_array($items[(string) $sourceIndex])) {
        $out['reason'] = 'item_nao_encontrado';
        return $out;
    }
    $item = $items[(string) $sourceIndex];
    $out['aliases_received'] = isset($item['aliases']) && is_array($item['aliases']) ? count($item['aliases']) : 0;
    if (!in_array(($item['status'] ?? ''), array('enriched', 'attention'), true)) {
        $out['reason'] = 'item_status_nao_utilizavel';
        return $out;
    }
    if (!isset($item['confidence']) || !is_numeric($item['confidence']) || (float) $item['confidence'] < importador_orcamento_pdf_enrichment_min_confidence()) {
        $out['reason'] = 'confidence_abaixo_do_minimo';
        return $out;
    }
    if (!isset($item['pending_confirmations']) || !is_array($item['pending_confirmations'])) {
        $out['reason'] = 'pending_confirmations';
        return $out;
    }
    $out['has_pending_confirmations'] = !empty($item['pending_confirmations']);
    if (!isset($item['contradictions']) || !is_array($item['contradictions']) || !empty($item['contradictions'])) {
        $out['reason'] = 'contradictions';
        return $out;
    }
    if (isset($item['enrichment_error']) && trim((string) $item['enrichment_error']) !== '') {
        $out['reason'] = 'enrichment_error';
        return $out;
    }
    $thickness = isset($item['attributes']['thickness']) && is_array($item['attributes']['thickness'])
        ? $item['attributes']['thickness'] : array();
    $thicknessValue = isset($thickness['value']) ? trim((string) $thickness['value']) : '';
    if (($thickness['origin'] ?? '') === 'calculated'
        && empty($thickness['requires_confirmation'])
        && preg_match('/^#\d+(?:\/\d+)?$/', $thicknessValue)) {
        $out['calculated_thickness'] = $thicknessValue;
    }

    $parts = array();
    if (isset($item['search_description']) && is_string($item['search_description']) && trim($item['search_description']) !== '') {
        $parts[] = trim($item['search_description']);
    }
    $descricaoNormalizada = importador_orcamento_pdf_busca_normalize_alias($descricaoOriginalOuRevisada);
    $aliasesVistos = array();
    if (isset($item['aliases']) && is_array($item['aliases'])) {
        foreach ($item['aliases'] as $alias) {
            $aliasNormalizado = is_string($alias) ? importador_orcamento_pdf_busca_normalize_alias($alias) : '';
            if ($aliasNormalizado === '') {
                $out['aliases_discarded']++;
                $out['aliases_discard_reasons']['empty']++;
                continue;
            }
            if ($descricaoNormalizada !== '' && $aliasNormalizado === $descricaoNormalizada) {
                $out['aliases_discarded']++;
                $out['aliases_discard_reasons']['identical_to_original']++;
                continue;
            }
            if (isset($aliasesVistos[$aliasNormalizado])) {
                $out['aliases_discarded']++;
                $out['aliases_discard_reasons']['duplicate']++;
                continue;
            }
            $aliasesVistos[$aliasNormalizado] = true;
            $parts[] = trim($alias);
        }
    }
    $out['text'] = trim(implode(' ', $parts));
    if ($out['text'] === '') {
        $out['reason'] = 'texto_enriquecido_vazio';
        return $out;
    }
    $out['eligible'] = true;
    return $out;
}

function importador_orcamento_pdf_candidate_ids(array $runtime, $fetchLimit)
{
    $ids = isset($runtime['final_ids']) && is_array($runtime['final_ids']) ? $runtime['final_ids'] : array();
    $out = array();
    $seen = array();
    foreach ($ids as $id) {
        $id = (int) $id;
        if ($id <= 0 || isset($seen[(string) $id])) {
            continue;
        }
        $seen[(string) $id] = true;
        $out[] = $id;
        if (count($out) >= $fetchLimit) {
            break;
        }
    }
    return $out;
}

function importador_orcamento_pdf_canonicos_por_informacao(array $busca)
{
    $selecionados = isset($busca['canonicos_encontrados']) && is_array($busca['canonicos_encontrados'])
        ? array_fill_keys(array_map('strval', $busca['canonicos_encontrados']), true) : array();
    $out = array();
    $aliases = isset($busca['aliases_encontrados']) && is_array($busca['aliases_encontrados'])
        ? $busca['aliases_encontrados'] : array();
    foreach ($aliases as $alias) {
        $informacao = isset($alias['informacao']) ? trim((string) $alias['informacao']) : '';
        $canonico = isset($alias['canonico']) ? trim((string) $alias['canonico']) : '';
        if ($informacao === '' || $canonico === '' || !isset($selecionados[$canonico]) || isset($out[$informacao])) {
            continue;
        }
        $out[$informacao] = $canonico;
    }
    return $out;
}

function importador_orcamento_pdf_combine_enrichment_candidate_ids(array $originalIds, array $enrichedIds, $enrichmentAvailable, $enrichmentEligible)
{
    if (!$enrichmentAvailable) {
        return array('ids' => $originalIds, 'strategy' => 'original_only');
    }
    if (!$enrichmentEligible) {
        return array('ids' => $originalIds, 'strategy' => 'enrichment_ineligible');
    }
    if (empty($originalIds) && empty($enrichedIds)) {
        return array('ids' => array(), 'strategy' => 'enrichment_no_result');
    }
    if (empty($originalIds)) {
        return array('ids' => $enrichedIds, 'strategy' => 'enrichment_recovered_empty_original');
    }
    if (empty($enrichedIds)) {
        return array('ids' => $originalIds, 'strategy' => 'enrichment_no_result');
    }

    // A Base transforma uma descrição curta em atributos de busca mais
    // específicos. Quando ela está elegível, sua seleção é a fonte principal;
    // a descrição original continua sendo fallback e proteção de conflito.
    return array('ids' => $enrichedIds, 'strategy' => 'enrichment_primary');
}

function importador_orcamento_pdf_enrichment_conflict_diagnostic(array $buscaOriginal, array $enrichmentInput, $strategy)
{
    if (strpos((string) $strategy, 'enrichment_') !== 0) {
        return array();
    }
    $enrichedValue = isset($enrichmentInput['calculated_thickness']) ? trim((string) $enrichmentInput['calculated_thickness']) : '';
    if ($enrichedValue === '') {
        return array();
    }

    $originalThicknesses = array();
    $canonicos = isset($buscaOriginal['canonicos_encontrados']) && is_array($buscaOriginal['canonicos_encontrados'])
        ? $buscaOriginal['canonicos_encontrados'] : array();
    foreach ($canonicos as $canonico) {
        $canonico = trim((string) $canonico);
        if (preg_match('/^#\d+(?:\/\d+)?$/', $canonico)) {
            $originalThicknesses[$canonico] = true;
        }
    }
    $originalValues = array_keys($originalThicknesses);
    if (count($originalValues) !== 1 || $originalValues[0] === $enrichedValue) {
        return array();
    }

    return array(
        'reason' => 'explicit_original_thickness_conflicts_with_calculated_thickness',
        'original_value' => $originalValues[0],
        'enriched_value' => $enrichedValue,
        'original_source' => 'pdf',
        'enriched_origin' => 'calculated',
        'resolution' => 'original_candidates_preserved',
    );
}

function importador_orcamento_pdf_gerar_candidatos_busca_dict_do_cache(array $itensIa, $cachePath, $limit = 80, array $enrichmentContext = array())
{
    $limit = (int) $limit > 0 ? (int) $limit : 80;
    $fetchLimit = max($limit, 200);
    $estados = array();
    $allTargetIds = array();

    foreach ($itensIa as $item) {
        $textoItem = importador_orcamento_pdf_busca_runtime_texto_item($item);
        $buscaOriginalError = '';
        $buscaOriginal = importador_orcamento_pdf_busca_preselecao_runtime($textoItem, $buscaOriginalError);
        $originalIds = importador_orcamento_pdf_candidate_ids($buscaOriginal, $fetchLimit);
        $enrichmentInput = importador_orcamento_pdf_enrichment_candidate_input(
            $enrichmentContext,
            isset($item['linha']) ? (int) $item['linha'] : 0,
            isset($item['descricao']) ? (string) $item['descricao'] : ''
        );
        $buscaEnriquecida = array();
        $buscaEnriquecidaError = '';
        $enrichedIds = array();
        if (!empty($enrichmentInput['eligible'])) {
            $canonicosOriginaisPorInformacao = importador_orcamento_pdf_canonicos_por_informacao($buscaOriginal);
            $buscaEnriquecida = importador_orcamento_pdf_busca_preselecao_runtime($enrichmentInput['text'], $buscaEnriquecidaError, $canonicosOriginaisPorInformacao);
            $enrichedIds = importador_orcamento_pdf_candidate_ids($buscaEnriquecida, $fetchLimit);
        }
        $combined = importador_orcamento_pdf_combine_enrichment_candidate_ids($originalIds, $enrichedIds, !empty($enrichmentInput['available']), !empty($enrichmentInput['eligible']));
        $conflictDiagnostic = importador_orcamento_pdf_enrichment_conflict_diagnostic($buscaOriginal, $enrichmentInput, isset($combined['strategy']) ? $combined['strategy'] : '');
        if (!empty($conflictDiagnostic)) {
            $combined['ids'] = $originalIds;
            $combined['strategy'] = 'enrichment_conflict_kept_original';
        }
        $finalIds = isset($combined['ids']) && is_array($combined['ids']) ? $combined['ids'] : array();
        if (!empty($finalIds)) {
            foreach ($finalIds as $id) {
                if ($id > 0) {
                    $allTargetIds[(string) $id] = true;
                }
            }
        }

        $estados[] = array(
            'item' => $item,
            'busca_original' => $buscaOriginal,
            'busca_original_error' => $buscaOriginalError,
            'busca_enriquecida' => $buscaEnriquecida,
            'busca_enriquecida_error' => $buscaEnriquecidaError,
            'enrichment_input' => $enrichmentInput,
            'conflict_diagnostic' => $conflictDiagnostic,
            'strategy' => isset($combined['strategy']) ? (string) $combined['strategy'] : 'original_only',
            'final_ids' => $finalIds,
        );
    }

    $productMap = importador_orcamento_pdf_collect_products_by_ids_shared($cachePath, array_keys($allTargetIds));
    $itensOut = array();

    foreach ($estados as $estado) {
        $item = isset($estado['item']) && is_array($estado['item']) ? $estado['item'] : array();
        $buscaOriginal = isset($estado['busca_original']) && is_array($estado['busca_original']) ? $estado['busca_original'] : array();
        $buscaOriginalError = isset($estado['busca_original_error']) ? (string) $estado['busca_original_error'] : '';
        $buscaEnriquecida = isset($estado['busca_enriquecida']) && is_array($estado['busca_enriquecida']) ? $estado['busca_enriquecida'] : array();
        $enrichmentInput = isset($estado['enrichment_input']) && is_array($estado['enrichment_input']) ? $estado['enrichment_input'] : array();
        $conflictDiagnostic = isset($estado['conflict_diagnostic']) && is_array($estado['conflict_diagnostic']) ? $estado['conflict_diagnostic'] : array();
        $finalIds = isset($estado['final_ids']) && is_array($estado['final_ids']) ? $estado['final_ids'] : array();

        $candidatos = array();
        $rank = 0;
        foreach ($finalIds as $id) {
            $key = (string) (int) $id;
            if (!isset($productMap[$key]) || !is_array($productMap[$key])) {
                continue;
            }
            $produto = $productMap[$key];
            $scoreLocal = max(1, 100 - $rank);
            $candidatos[] = array(
                'D001_Id' => isset($produto['D001_Id']) ? (int) $produto['D001_Id'] : 0,
                'codigo' => isset($produto['codigo']) ? (string) $produto['codigo'] : '',
                'descricao' => isset($produto['descricao']) ? (string) $produto['descricao'] : '',
                'score_local' => $scoreLocal,
                'motivo_score' => 'Pré-seleção por dicionário',
                'detalhe_score' => array(
                    'score_final' => $scoreLocal,
                    'classificacao' => 'preselecionado_dicionario',
                    'observacoes' => array_values(array_filter(array(
                        'Valores encontrados no cadastro: ' . implode(', ', isset($buscaOriginal['canonicos_encontrados']) && is_array($buscaOriginal['canonicos_encontrados']) ? $buscaOriginal['canonicos_encontrados'] : array()),
                        isset($buscaOriginal['motivo']) ? ('Motivo: ' . (string) $buscaOriginal['motivo']) : '',
                    ))),
                ),
            );
            $rank++;
            if (count($candidatos) >= $limit) {
                break;
            }
        }

        $diagItem = array(
            'total_produtos_avaliados' => count($finalIds),
            'preselecionados' => count($candidatos),
            'busca_dict_aplicada' => !empty($candidatos),
            'busca_dict_motivo' => isset($buscaOriginal['motivo']) ? (string) $buscaOriginal['motivo'] : '',
            'busca_dict_erro' => $buscaOriginalError,
            'busca_dict_aliases' => isset($buscaOriginal['aliases_encontrados']) && is_array($buscaOriginal['aliases_encontrados']) ? count($buscaOriginal['aliases_encontrados']) : 0,
            'busca_dict_canonicos' => isset($buscaOriginal['canonicos_encontrados']) && is_array($buscaOriginal['canonicos_encontrados']) ? array_values($buscaOriginal['canonicos_encontrados']) : array(),
            'busca_dict_total_candidatos' => isset($buscaOriginal['total_final_candidatos']) ? (int) $buscaOriginal['total_final_candidatos'] : 0,
            'busca_dict_zero_at_step' => isset($buscaOriginal['zero_at_step']) ? $buscaOriginal['zero_at_step'] : null,
            'enrichment_available' => !empty($enrichmentInput['available']),
            'enrichment_eligible' => !empty($enrichmentInput['eligible']),
            'enrichment_ineligible_reason' => !empty($enrichmentInput['eligible']) ? '' : (isset($enrichmentInput['reason']) ? (string) $enrichmentInput['reason'] : ''),
            'enrichment_has_pending_confirmations' => !empty($enrichmentInput['has_pending_confirmations']),
            'enrichment_aliases_received' => isset($enrichmentInput['aliases_received']) ? (int) $enrichmentInput['aliases_received'] : 0,
            'enrichment_aliases_discarded' => isset($enrichmentInput['aliases_discarded']) ? (int) $enrichmentInput['aliases_discarded'] : 0,
            'enrichment_aliases_discard_reasons' => isset($enrichmentInput['aliases_discard_reasons']) && is_array($enrichmentInput['aliases_discard_reasons']) ? $enrichmentInput['aliases_discard_reasons'] : array(),
            'busca_dict_canonicos_original' => isset($buscaOriginal['canonicos_encontrados']) && is_array($buscaOriginal['canonicos_encontrados']) ? array_values($buscaOriginal['canonicos_encontrados']) : array(),
            'busca_dict_canonicos_enriquecimento' => isset($buscaEnriquecida['canonicos_encontrados']) && is_array($buscaEnriquecida['canonicos_encontrados']) ? array_values($buscaEnriquecida['canonicos_encontrados']) : array(),
            'busca_dict_total_candidatos_original' => isset($buscaOriginal['total_final_candidatos']) ? (int) $buscaOriginal['total_final_candidatos'] : 0,
            'busca_dict_total_candidatos_enriquecimento' => isset($buscaEnriquecida['total_final_candidatos']) ? (int) $buscaEnriquecida['total_final_candidatos'] : 0,
            'busca_dict_total_candidatos_final' => count($candidatos),
            'busca_dict_estrategia_final' => isset($estado['strategy']) ? (string) $estado['strategy'] : 'original_only',
            'enrichment_conflict' => $conflictDiagnostic,
        );

        $itensOut[] = array(
            'linha' => isset($item['linha']) ? (int) $item['linha'] : 0,
            'codigo' => isset($item['codigo']) ? (string) $item['codigo'] : '',
            'descricao' => isset($item['descricao']) ? (string) $item['descricao'] : '',
            'quantidade' => isset($item['quantidade']) ? (string) $item['quantidade'] : '',
            'unidade' => isset($item['unidade']) ? (string) $item['unidade'] : '',
            'candidatos' => $candidatos,
            'diagnostico_local' => $diagItem,
        );
    }

    return array(
        'gerado_em' => date('Y-m-d H:i:s'),
        'parser_version' => importador_orcamento_pdf_parser_version(),
        'matching_mode' => 'busca_dict',
        'total_itens' => count($itensOut),
        'limite_candidatos_por_item' => $limit,
        'itens' => $itensOut,
    );
}

function importador_orcamento_pdf_validar_estrutura_busca_dict($produtosCacheDir, $produtosCachePath, $produtosCacheTmp, $produtosCacheLockPath, &$erro)
{
    $erro = '';

    if (!importador_orcamento_pdf_ensure_directory($produtosCacheDir)) {
        $erro = 'Não foi possível preparar o diretório de cache de produtos.';
        return false;
    }

    $pathsBusca = importador_orcamento_pdf_busca_global_paths();
    if (!is_file($pathsBusca['compiled']) || !is_file($pathsBusca['index'])) {
        $erro = 'Configuração de busca incompleta. Antes de gerar candidatos, execute: Compilar configuração e Gerar índice do catálogo.';
        return false;
    }
    $buscaFreshError = '';
    if (!importador_orcamento_pdf_busca_compiled_is_fresh($buscaFreshError)) {
        $erro = 'A configuração de busca precisa ser recompilada. Execute: Compilar configuração e Gerar índice do catálogo.';
        return false;
    }
    if (!importador_orcamento_pdf_busca_index_is_fresh($buscaFreshError)) {
        $erro = 'O índice de busca está desatualizado. Execute: Gerar índice do catálogo.';
        return false;
    }

    if (!importador_orcamento_pdf_cache_produtos_expirado($produtosCachePath, 21600)) {
        return true;
    }

    list($lockOk, $lockFp) = importador_orcamento_pdf_cache_lock_acquire($produtosCacheLockPath);
    if (!$lockOk) {
        if (!is_file($produtosCachePath)) {
            $erro = 'O cache de produtos está sendo gerado por outro usuário. Aguarde alguns instantes e tente novamente.';
            return false;
        }
        return true;
    }

    if (importador_orcamento_pdf_cache_produtos_expirado($produtosCachePath, 21600)) {
        list($okCache, $cacheErr) = importador_orcamento_pdf_gerar_cache_produtos($produtosCachePath, $produtosCacheTmp);
        importador_orcamento_pdf_cache_lock_release($lockFp);
        if (!$okCache) {
            error_log('[importadorOrcamentoPdf] erro ao gerar produtos_cache.json: ' . $cacheErr);
            $erro = 'Não foi possível gerar o cache de produtos.';
            return false;
        }
        return true;
    }

    importador_orcamento_pdf_cache_lock_release($lockFp);
    return true;
}

function importador_orcamento_pdf_preparar_candidatos_locais($previewPath, $revisoes, $itensIaPath, $produtosCacheDir, $produtosCachePath, $produtosCacheTmp, $produtosIndexPath, $produtosCacheLockPath, $itensComCandidatosPath, $matchingComparePath, $normalizeDebugPath, &$flashMessage, &$flashType)
{
    importador_orcamento_pdf_normalize_debug_init($normalizeDebugPath);

    if (!is_file($previewPath)) {
        $flashMessage = 'Não foi possível gerar: import_preview.json não encontrado.';
        $flashType = 'error';
        return null;
    }

    $previewJson = json_decode((string) @file_get_contents($previewPath), true);
    $previewItems = isset($previewJson['items']) && is_array($previewJson['items']) ? $previewJson['items'] : array();
    if (empty($previewItems)) {
        $flashMessage = 'Não foi possível gerar: import_preview.json sem itens.';
        $flashType = 'error';
        return null;
    }

    $payloadIa = importador_orcamento_pdf_build_itens_para_ia($previewItems, $revisoes);
    $okSaveIa = @file_put_contents($itensIaPath, json_encode($payloadIa, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) !== false;
    if (!$okSaveIa) {
        $flashMessage = 'Não foi possível gerar o arquivo consolidado para IA.';
        $flashType = 'error';
        return null;
    }

    $erroBuscaDict = '';
    if (!importador_orcamento_pdf_validar_estrutura_busca_dict($produtosCacheDir, $produtosCachePath, $produtosCacheTmp, $produtosCacheLockPath, $erroBuscaDict)) {
        $flashMessage = $erroBuscaDict !== '' ? $erroBuscaDict : 'Não foi possível preparar a estrutura de busca.';
        $flashType = 'error';
        return null;
    }

    $itensIaJson = json_decode((string) @file_get_contents($itensIaPath), true);
    $itensIa = isset($itensIaJson['itens']) && is_array($itensIaJson['itens']) ? $itensIaJson['itens'] : array();
    $enrichmentContext = importador_orcamento_pdf_load_enrichment_for_candidates($previewPath);
    $payloadCandidatos = importador_orcamento_pdf_gerar_candidatos_busca_dict_do_cache($itensIa, $produtosCachePath, 80, $enrichmentContext);
    $okSaveCand = @file_put_contents($itensComCandidatosPath, json_encode($payloadCandidatos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) !== false;
    if (!$okSaveCand) {
        $flashMessage = 'Não foi possível salvar itens_com_candidatos.json.';
        $flashType = 'error';
        return null;
    }

    importador_orcamento_pdf_normalize_debug_flush();

    return $payloadCandidatos;
}

function importador_orcamento_pdf_gerar_candidatos_linha($linha, $previewPath, $revisoes, $itensIaPath, $produtosCacheDir, $produtosCachePath, $produtosCacheTmp, $produtosCacheLockPath, $itensComCandidatosPath, $normalizeDebugPath, &$erro)
{
    $erro = '';
    $linha = (int) $linha;
    if ($linha <= 0) {
        $erro = 'Linha inválida.';
        return null;
    }

    importador_orcamento_pdf_normalize_debug_init($normalizeDebugPath);

    if (!is_file($previewPath)) {
        $erro = 'import_preview.json não encontrado.';
        return null;
    }

    $previewJson = json_decode((string) @file_get_contents($previewPath), true);
    $previewItems = isset($previewJson['items']) && is_array($previewJson['items']) ? $previewJson['items'] : array();
    if (empty($previewItems)) {
        $erro = 'import_preview.json sem itens.';
        return null;
    }

    $payloadIa = importador_orcamento_pdf_build_itens_para_ia($previewItems, $revisoes);
    if (@file_put_contents($itensIaPath, json_encode($payloadIa, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) === false) {
        $erro = 'Não foi possível atualizar itens_para_ia.json.';
        return null;
    }

    $payloadLinhaIa = importador_orcamento_pdf_filtrar_payload_por_linha($payloadIa, $linha);
    $itensLinha = isset($payloadLinhaIa['itens']) && is_array($payloadLinhaIa['itens']) ? $payloadLinhaIa['itens'] : array();
    if (empty($itensLinha)) {
        $erro = 'Linha não encontrada no payload de importação.';
        return null;
    }

    $erroBuscaDict = '';
    if (!importador_orcamento_pdf_validar_estrutura_busca_dict($produtosCacheDir, $produtosCachePath, $produtosCacheTmp, $produtosCacheLockPath, $erroBuscaDict)) {
        $erro = $erroBuscaDict !== '' ? $erroBuscaDict : 'Não foi possível preparar a estrutura de busca.';
        return null;
    }

    $enrichmentContext = importador_orcamento_pdf_load_enrichment_for_candidates($previewPath);
    $payloadLinha = importador_orcamento_pdf_gerar_candidatos_busca_dict_do_cache($itensLinha, $produtosCachePath, 80, $enrichmentContext);
    $payloadExistente = importador_orcamento_pdf_load_json_file($itensComCandidatosPath);
    $payloadMerged = importador_orcamento_pdf_mesclar_payload_por_linha($payloadExistente, $payloadLinha);

    if (@file_put_contents($itensComCandidatosPath, json_encode($payloadMerged, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) === false) {
        $erro = 'Não foi possível salvar itens_com_candidatos.json.';
        return null;
    }

    importador_orcamento_pdf_normalize_debug_flush();

    return $payloadLinha;
}

function importador_orcamento_pdf_gerar_sugestao_ia_linha($linha, $itensComCandidatosPath, $sugestoesIaPath, &$erro)
{
    $erro = '';
    $linha = (int) $linha;
    if ($linha <= 0) {
        $erro = 'Linha inválida.';
        return null;
    }

    $payloadCandidatos = importador_orcamento_pdf_load_json_file($itensComCandidatosPath);
    $payloadLinha = importador_orcamento_pdf_filtrar_payload_por_linha($payloadCandidatos, $linha);
    $itensLinha = isset($payloadLinha['itens']) && is_array($payloadLinha['itens']) ? $payloadLinha['itens'] : array();
    if (empty($itensLinha)) {
        $erro = 'Nenhum candidato local disponível para esta linha.';
        return null;
    }

    $payloadSugestaoLinha = importador_orcamento_pdf_gerar_sugestoes_ia($payloadLinha, $erro);
    if ($payloadSugestaoLinha === null) {
        return null;
    }

    $payloadExistente = importador_orcamento_pdf_load_json_file($sugestoesIaPath);
    $payloadMerged = importador_orcamento_pdf_mesclar_payload_por_linha($payloadExistente, $payloadSugestaoLinha);
    if (@file_put_contents($sugestoesIaPath, json_encode($payloadMerged, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) === false) {
        $erro = 'Não foi possível salvar sugestoes_ia.json.';
        return null;
    }

    return $payloadSugestaoLinha;
}

function importador_orcamento_pdf_regenerar_cache_produtos($produtosCacheDir, $produtosCachePath, $produtosCacheTmp, $produtosIndexPath, $produtosCacheLockPath, &$flashMessage, &$flashType)
{
    if (!importador_orcamento_pdf_ensure_directory($produtosCacheDir)) {
        $flashMessage = 'Não foi possível preparar o diretório de cache de produtos.';
        $flashType = 'error';
        return false;
    }

    list($lockOk, $lockFp) = importador_orcamento_pdf_cache_lock_acquire($produtosCacheLockPath);
    if (!$lockOk) {
        $flashMessage = 'Já existe uma geração de cache de produtos em andamento por outro usuário.';
        $flashType = 'error';
        return false;
    }

    $inicio = microtime(true);
    list($okCache, $cacheErr) = importador_orcamento_pdf_gerar_cache_produtos($produtosCachePath, $produtosCacheTmp);
    importador_orcamento_pdf_cache_lock_release($lockFp);
    if (!$okCache) {
        error_log('[importadorOrcamentoPdf] erro ao gerar produtos_cache.json: ' . $cacheErr);
        $flashMessage = 'Não foi possível gerar o cache de produtos.';
        if (trim((string) $cacheErr) !== '') {
            $flashMessage .= ' ' . trim((string) $cacheErr);
        }
        $flashType = 'error';
        return false;
    }

    $cacheMeta = importador_orcamento_pdf_load_produtos_cache_meta($produtosCachePath);
    $total = isset($cacheMeta['total']) ? (int) $cacheMeta['total'] : 0;
    $segundos = number_format(microtime(true) - $inicio, 2, ',', '');
    $flashMessage = 'Cache de produtos gerado com sucesso';
    if ($total > 0) {
        $flashMessage .= ': ' . $total . ' produtos';
    }
    $flashMessage .= ' em ' . $segundos . 's.';
    $flashType = 'success';
    return true;
}

function importador_orcamento_pdf_resumo_candidatos_locais(array $payloadCandidatos)
{
    $itens = isset($payloadCandidatos['itens']) && is_array($payloadCandidatos['itens']) ? $payloadCandidatos['itens'] : array();
    $itensComCandidatos = 0;
    $totalCandidatos = 0;

    foreach ($itens as $item) {
        $candidatos = isset($item['candidatos']) && is_array($item['candidatos']) ? $item['candidatos'] : array();
        $qt = count($candidatos);
        $totalCandidatos += $qt;
        if ($qt > 0) {
            $itensComCandidatos++;
        }
    }

    return array(
        'itens_total' => count($itens),
        'itens_com_candidatos' => $itensComCandidatos,
        'total_candidatos' => $totalCandidatos,
    );
}

if ($ok && is_array($orcamento) && isset($orcamento['T003_D024_Id'])) {
    $D024_Id = (int) $orcamento['T003_D024_Id'];
    list($flashSessionMessage, $flashSessionType) = importador_orcamento_pdf_revisao_flash_pull();
    if ($flashSessionMessage !== '') {
        $flashMessage = $flashSessionMessage;
        $flashType = $flashSessionType !== '' ? $flashSessionType : 'info';
    }
    $baseDir = importador_orcamento_pdf_fs_orcamento_dir_existing($D024_Id, $T003_Id);
    $previewPath = $baseDir . '/import_preview.json';
    $enrichmentInputPath = $baseDir . '/import_preview_enrichment_input.json';
    $enrichedPath = $baseDir . '/import_preview_enriched.json';
    $enrichmentDiagPath = $baseDir . '/enrichment_diag.json';
    $enrichmentStatePath = $baseDir . '/enrichment_state.json';
    $enrichmentLockPath = $baseDir . '/enrichment.lock';
    $revisaoPath = $baseDir . '/revisao_itens.json';
    $itensIaPath = $baseDir . '/itens_para_ia.json';
    $itensComCandidatosPath = $baseDir . '/itens_com_candidatos.json';
    $matchingComparePath = $baseDir . '/matching_compare.json';
    $normalizeDebugPath = $baseDir . '/normalize_debug.json';
    $sugestoesIaPath = $baseDir . '/sugestoes_ia.json';
    $sugestoesIaStatePath = $baseDir . '/sugestoes_ia_state.json';
    $sugestoesIaLockPath = $baseDir . '/sugestoes_ia.lock';
    $selecaoProdutosPath = $baseDir . '/selecao_produtos.json';
    $insercaoAuditPath = $baseDir . '/insercao_orcamento.json';
    $insercaoStatePath = $baseDir . '/insercao_orcamento_state.json';
    $insercaoLockPath = $baseDir . '/insercao_orcamento.lock';
    $cachePaths = importador_orcamento_pdf_cache_paths($baseDir);
    $produtosCacheDir = $cachePaths['dir'];
    $produtosCacheTmp = $cachePaths['tmp'];
    $produtosCachePath = $cachePaths['final'];
    $produtosIndexPath = $cachePaths['index'];
    $produtosCacheLockPath = $cachePaths['lock'];
    $baseKnowledgeRepository = new BaseConhecimentoRepository();
    $baseKnowledgeSnapshot = $baseKnowledgeRepository->getActiveSnapshot($baseKnowledgeError);
    $baseKnowledgeActive = !empty($baseKnowledgeSnapshot);
    $enrichmentCsrfScope = 'enrichment_steps_' . (int) $T003_Id;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'processar_enriquecimento') {
        header('Content-Type: application/json; charset=utf-8');
        $csrfToken = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
        if (!importador_orcamento_pdf_csrf_valido($enrichmentCsrfScope, $csrfToken) || !$baseKnowledgeActive || !is_file($previewPath)) {
            echo json_encode(array('ok'=>false,'erro'=>'Não foi possível iniciar o enriquecimento.')); exit;
        }
        $lockFp = @fopen($enrichmentLockPath, 'c');
        if (!$lockFp || !@flock($lockFp, LOCK_EX | LOCK_NB)) { if($lockFp)@fclose($lockFp); echo json_encode(array('ok'=>false,'erro'=>'Já existe um enriquecimento em andamento.','busy'=>true)); exit; }
        $init = isset($_POST['init']) && (string)$_POST['init']==='1';
        $state = importador_orcamento_pdf_load_json_file($enrichmentStatePath);
        if ($init && isset($state['status']) && $state['status']==='processing') {
            @flock($lockFp,LOCK_UN);@fclose($lockFp); echo json_encode(array('ok'=>false,'erro'=>'Já existe um enriquecimento em andamento.','busy'=>true)); exit;
        }
        $stepService = new ImportEnrichmentStepService($baseKnowledgeRepository);
        $stepResult = array();
        if ($init) {
            $enrichmentInputError = '';
            $revisoesEnrichment = importador_orcamento_pdf_load_revisao($revisaoPath);
            if (!importador_orcamento_pdf_prepare_enrichment_input($previewPath, $revisoesEnrichment, $enrichmentInputPath, $enrichmentInputError)) {
                @flock($lockFp,LOCK_UN);@fclose($lockFp); echo json_encode(array('ok'=>false,'erro'=>$enrichmentInputError)); exit;
            }
        }
        if (!is_file($enrichmentInputPath)) {
            @flock($lockFp,LOCK_UN);@fclose($lockFp); echo json_encode(array('ok'=>false,'erro'=>'Entrada da Base de Conhecimento não encontrada. Reaplique a Base.')); exit;
        }
        if ($init && !$stepService->start($enrichmentInputPath,$baseDir,$stepResult)) {
            @flock($lockFp,LOCK_UN);@fclose($lockFp); echo json_encode(array('ok'=>false,'erro'=>isset($stepResult['erro'])?$stepResult['erro']:'Não foi possível iniciar o enriquecimento.')); exit;
        }
        global $g; $apiKey=isset($g['C031']['openAIApiKey'])?trim((string)$g['C031']['openAIApiKey']):'';
        $stepService->process($enrichmentInputPath,$baseDir,$apiKey,$stepResult);
        @flock($lockFp,LOCK_UN);@fclose($lockFp);
        echo json_encode($stepResult,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'buscar_produtos') {
        header('Content-Type: application/json; charset=utf-8');
        if (!$ok) {
            echo json_encode(array('ok' => false, 'erro' => 'Acesso inválido', 'itens' => array()));
            exit;
        }
        $q = isset($_GET['q']) ? (string) $_GET['q'] : '';
        $found = importador_orcamento_pdf_buscar_produtos_cache($produtosCachePath, $q, 20);
        echo json_encode(array('ok' => true, 'itens' => $found), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'carregar_candidatos') {
        header('Content-Type: application/json; charset=utf-8');
        if (!$ok) {
            echo json_encode(array('ok' => false, 'erro' => 'Acesso inválido', 'itens' => array()));
            exit;
        }
        $linhaAjax = isset($_GET['linha']) ? (int) $_GET['linha'] : 0;
        $itens = importador_orcamento_pdf_load_candidatos_item_ui($itensComCandidatosPath, $linhaAjax);
        echo json_encode(array('ok' => true, 'itens' => $itens), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'gerar_candidatos_linha') {
        header('Content-Type: application/json; charset=utf-8');
        if (!$ok) {
            echo json_encode(array('ok' => false, 'erro' => 'Acesso inválido'));
            exit;
        }
        $linhaAjax = isset($_POST['linha']) ? (int) $_POST['linha'] : 0;
        $erroAjax = '';
        $revisoesAjax = importador_orcamento_pdf_load_revisao($revisaoPath);
        $payloadLinha = importador_orcamento_pdf_gerar_candidatos_linha(
            $linhaAjax,
            $previewPath,
            $revisoesAjax,
            $itensIaPath,
            $produtosCacheDir,
            $produtosCachePath,
            $produtosCacheTmp,
            $produtosCacheLockPath,
            $itensComCandidatosPath,
            $normalizeDebugPath,
            $erroAjax
        );
        if ($payloadLinha === null) {
            echo json_encode(array('ok' => false, 'erro' => $erroAjax !== '' ? $erroAjax : 'Não foi possível gerar os candidatos desta linha.'));
            exit;
        }
        $itens = importador_orcamento_pdf_load_candidatos_item_ui($itensComCandidatosPath, $linhaAjax);
        echo json_encode(array(
            'ok' => true,
            'linha' => $linhaAjax,
            'itens' => $itens,
            'total_candidatos' => count($itens),
            'mensagem' => count($itens) > 0
                ? ('Candidatos do item gerados com sucesso: ' . count($itens) . ' opção(ões).')
                : 'Candidatos do item gerados, mas nenhuma opção foi encontrada.',
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'sugerir_ia_linha') {
        header('Content-Type: application/json; charset=utf-8');
        if (!$ok) {
            echo json_encode(array('ok' => false, 'erro' => 'Acesso inválido'));
            exit;
        }
        $linhaAjax = isset($_POST['linha']) ? (int) $_POST['linha'] : 0;
        $erroAjax = '';
        $payloadSugestao = importador_orcamento_pdf_gerar_sugestao_ia_linha($linhaAjax, $itensComCandidatosPath, $sugestoesIaPath, $erroAjax);
        if ($payloadSugestao === null) {
            echo json_encode(array('ok' => false, 'erro' => $erroAjax !== '' ? $erroAjax : 'Não foi possível gerar a sugestão da IA para esta linha.'));
            exit;
        }
        $itensSug = isset($payloadSugestao['itens']) && is_array($payloadSugestao['itens']) ? $payloadSugestao['itens'] : array();
        $itemSug = !empty($itensSug[0]) && is_array($itensSug[0]) ? $itensSug[0] : null;
        echo json_encode(array(
            'ok' => true,
            'linha' => $linhaAjax,
            'item' => $itemSug,
            'mensagem' => $itemSug && !empty($itemSug['produto_sugerido'])
                ? 'Sugestão da IA gerada com sucesso para este item.'
                : 'A IA analisou este item, mas não encontrou sugestão confiável.',
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'processar_sugestoes_ia') {
        header('Content-Type: application/json; charset=utf-8');
        if (!$ok) {
            echo json_encode(array('ok' => false, 'erro' => 'Acesso inválido'));
            exit;
        }

        $lockFp = @fopen($sugestoesIaLockPath, 'c');
        if (!$lockFp || !@flock($lockFp, LOCK_EX | LOCK_NB)) {
            echo json_encode(array('ok' => false, 'erro' => 'Já existe uma geração de sugestões da IA em andamento.', 'busy' => true));
            if ($lockFp) {
                @fclose($lockFp);
            }
            exit;
        }

        global $g;
        $apiKey = isset($g['C031']['openAIApiKey']) ? trim((string) $g['C031']['openAIApiKey']) : '';
        if ($apiKey === '') {
            @flock($lockFp, LOCK_UN);
            @fclose($lockFp);
            echo json_encode(array('ok' => false, 'erro' => 'Chave da IA não configurada. Defina openAIApiKey nas configurações da empresa.'));
            exit;
        }

        $model = getenv('IMPORTADOR_IA_MODEL');
        if (!$model) {
            $model = 'gpt-4.1-mini';
        }

        $init = isset($_POST['init']) && (string) $_POST['init'] === '1';
        if ($init || !is_file($sugestoesIaStatePath)) {
            $revisoesAtual = importador_orcamento_pdf_load_revisao($revisaoPath);
            $payloadCandidatos = importador_orcamento_pdf_preparar_candidatos_locais(
                $previewPath,
                $revisoesAtual,
                $itensIaPath,
                $produtosCacheDir,
                $produtosCachePath,
                $produtosCacheTmp,
                $produtosIndexPath,
                $produtosCacheLockPath,
                $itensComCandidatosPath,
                $matchingComparePath,
                $normalizeDebugPath,
                $flashMessage,
                $flashType
            );

            if ($payloadCandidatos === null) {
                @flock($lockFp, LOCK_UN);
                @fclose($lockFp);
                echo json_encode(array('ok' => false, 'erro' => $flashMessage !== '' ? $flashMessage : 'Não foi possível preparar os candidatos locais para a IA.'));
                exit;
            }

            $payloadSugestoesInicial = array(
                'gerado_em' => date('Y-m-d H:i:s'),
                'model' => $model,
                'erro' => '',
                'itens' => array(),
            );
            if (!importador_orcamento_pdf_save_sugestoes_ia_payload($sugestoesIaPath, $payloadSugestoesInicial)
                || !importador_orcamento_pdf_save_json_pretty($sugestoesIaStatePath, importador_orcamento_pdf_build_sugestoes_ia_state($payloadCandidatos, $model))
            ) {
                @flock($lockFp, LOCK_UN);
                @fclose($lockFp);
                echo json_encode(array('ok' => false, 'erro' => 'Não foi possível iniciar o processamento em etapas da IA.'));
                exit;
            }
        }

        $payloadCandidatosAtual = importador_orcamento_pdf_load_json_file($itensComCandidatosPath);
        $erroIa = '';
        $respIa = importador_orcamento_pdf_processar_sugestoes_ia_etapa(
            $payloadCandidatosAtual,
            $sugestoesIaPath,
            $selecaoProdutosPath,
            $sugestoesIaStatePath,
            $apiKey,
            $model,
            $erroIa,
            1,
            20
        );

        @flock($lockFp, LOCK_UN);
        @fclose($lockFp);

        if (!$respIa['ok']) {
            echo json_encode($respIa, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $respIa['mensagem'] = !empty($respIa['done'])
            ? 'Sugestões da IA concluídas.'
            : 'Processando sugestões da IA em etapas.';
        echo json_encode($respIa, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'salvar_selecao_item') {
        header('Content-Type: application/json; charset=utf-8');
        if (!$ok) {
            echo json_encode(array('ok' => false, 'erro' => 'Acesso inválido'));
            exit;
        }
        $linhaAjax = isset($_POST['linha']) ? (int) $_POST['linha'] : 0;
        $confirmadoAjax = isset($_POST['confirmado']) && (string) $_POST['confirmado'] === '1';
        $produtoIdAjax = isset($_POST['produto_id']) ? (int) $_POST['produto_id'] : 0;
        $produtoCodigoAjax = trim((string) (isset($_POST['produto_codigo']) ? $_POST['produto_codigo'] : ''));
        $produtoDescricaoAjax = trim((string) (isset($_POST['produto_descricao']) ? $_POST['produto_descricao'] : ''));
        $origemAjax = trim((string) (isset($_POST['origem']) ? $_POST['origem'] : ''));
        if ($linhaAjax <= 0) {
            echo json_encode(array('ok' => false, 'erro' => 'Linha inválida'));
            exit;
        }
        $selecoesAjax = importador_orcamento_pdf_load_selecao_produtos($selecaoProdutosPath);
        $selecoesAjax[(string) $linhaAjax] = array(
            'confirmado' => $confirmadoAjax,
            'produto' => ($produtoIdAjax > 0 || $produtoCodigoAjax !== '' || $produtoDescricaoAjax !== '') ? array(
                'D001_Id' => $produtoIdAjax,
                'codigo' => $produtoCodigoAjax,
                'descricao' => $produtoDescricaoAjax,
            ) : null,
            'origem' => $origemAjax !== '' ? $origemAjax : 'manual',
        );
        $okSaveAjax = importador_orcamento_pdf_save_selecao_produtos($selecaoProdutosPath, $selecoesAjax);
        echo json_encode(array('ok' => $okSaveAjax), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'processar_insercao_orcamento') {
        header('Content-Type: application/json; charset=utf-8');
        if (!$ok) {
            echo json_encode(array('ok' => false, 'erro' => 'Acesso inválido'));
            exit;
        }
        $lockFp = @fopen($insercaoLockPath, 'c');
        if (!$lockFp || !@flock($lockFp, LOCK_EX | LOCK_NB)) {
            echo json_encode(array('ok' => false, 'erro' => 'Já existe uma inclusão em andamento.', 'busy' => true));
            if ($lockFp) {
                @fclose($lockFp);
            }
            exit;
        }

        $chunkSize = isset($_POST['chunk_size']) ? (int) $_POST['chunk_size'] : 20;
        if ($chunkSize <= 0) {
            $chunkSize = 20;
        }
        if ($chunkSize > 100) {
            $chunkSize = 100;
        }
        $init = isset($_POST['init']) && (string) $_POST['init'] === '1';

        $stateExistente = importador_orcamento_pdf_load_json_file($insercaoStatePath);
        $stateTemItens = isset($stateExistente['itens']) && is_array($stateExistente['itens']) && !empty($stateExistente['itens']);
        $stateCursor = isset($stateExistente['cursor']) ? (int) $stateExistente['cursor'] : 0;
        $stateTotal = $stateTemItens ? count($stateExistente['itens']) : 0;
        $retomarEmAndamento = $stateTemItens && $stateCursor > 0 && $stateCursor < $stateTotal;

        if (!$retomarEmAndamento && ($init || !is_file($insercaoStatePath))) {
            $revisoesAtual = importador_orcamento_pdf_load_revisao($revisaoPath);
            $selecoesAtual = importador_orcamento_pdf_load_selecao_produtos($selecaoProdutosPath);
            $linhasJaInseridas = importador_orcamento_pdf_linhas_ja_inseridas($insercaoAuditPath);
            list($okItens, $erroItens, $itensParaInserir) = importador_orcamento_pdf_montar_itens_para_insercao($previewPath, $revisoesAtual, $selecoesAtual, $linhasJaInseridas);
            if (!$okItens) {
                @flock($lockFp, LOCK_UN);
                @fclose($lockFp);
                echo json_encode(array('ok' => false, 'erro' => $erroItens));
                exit;
            }

            $hashBase = array_map(function ($x) {
                return array(
                    'l' => isset($x['linha']) ? (int) $x['linha'] : 0,
                    'd001' => isset($x['produto']['D001_Id']) ? (int) $x['produto']['D001_Id'] : 0,
                    'q' => isset($x['item']['quantidade']) ? (string) $x['item']['quantidade'] : '',
                    'd' => isset($x['item']['descricao']) ? (string) $x['item']['descricao'] : '',
                );
            }, $itensParaInserir);
            $executionHash = sha1(json_encode($hashBase, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $lastAudit = importador_orcamento_pdf_load_json_file($insercaoAuditPath);
            if (isset($lastAudit['status']) && $lastAudit['status'] === 'sucesso' && isset($lastAudit['execution_hash']) && $lastAudit['execution_hash'] === $executionHash) {
                @flock($lockFp, LOCK_UN);
                @fclose($lockFp);
                echo json_encode(array(
                    'ok' => true,
                    'done' => true,
                    'status' => 'sucesso',
                    'total' => count($itensParaInserir),
                    'processados' => count($itensParaInserir),
                    'itens_inseridos' => isset($lastAudit['itens_inseridos']) ? (int) $lastAudit['itens_inseridos'] : 0,
                    'falhas' => isset($lastAudit['falhas']) && is_array($lastAudit['falhas']) ? count($lastAudit['falhas']) : 0,
                    'mensagem' => 'Este mesmo conjunto já foi inserido recentemente. Operação ignorada para evitar duplicidade.',
                ));
                exit;
            }

            $historicoResultadosOk = importador_orcamento_pdf_resultados_ok_historico($insercaoAuditPath);

            $state = array(
                'gerado_em' => date('Y-m-d H:i:s'),
                'T003_Id' => (int) $T003_Id,
                'execution_hash' => $executionHash,
                'cursor' => 0,
                'itens' => $itensParaInserir,
            );
            $audit = array(
                'gerado_em' => date('Y-m-d H:i:s'),
                'status' => 'processando',
                'T003_Id' => (int) $T003_Id,
                'execution_hash' => $executionHash,
                'itens_total_confirmados' => count($itensParaInserir),
                'itens_inseridos' => 0,
                'falhas' => array(),
                'resultados' => $historicoResultadosOk,
            );
            @file_put_contents($insercaoStatePath, json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            @file_put_contents($insercaoAuditPath, json_encode($audit, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }

        $respLote = importador_orcamento_pdf_processar_lote_insercao($T003_Id, $insercaoAuditPath, $insercaoStatePath, $chunkSize);
        @flock($lockFp, LOCK_UN);
        @fclose($lockFp);

        if (!$respLote['ok']) {
            echo json_encode($respLote, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $msg = 'Inclusão em andamento.';
        if (!empty($respLote['done'])) {
            if (isset($respLote['falhas']) && (int) $respLote['falhas'] > 0) {
                $msg = 'Inclusão parcial concluída.';
            } else {
                $msg = 'Inclusão concluída com sucesso.';
            }
        }
        $respLote['mensagem'] = $msg;
        echo json_encode($respLote, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? (string) $_POST['action'] : '';
        $linhaPost = isset($_POST['linha']) ? (int) $_POST['linha'] : 0;
        $revisoes = importador_orcamento_pdf_load_revisao($revisaoPath);
        $selecoes = importador_orcamento_pdf_load_selecao_produtos($selecaoProdutosPath);

        if ($action === 'aplicar_base_conhecimento') {
            $flashMessage = 'A aplicação da Base de Conhecimento requer JavaScript habilitado para processar os itens em etapas.';
            $flashType = 'error';
        } elseif ($action === 'salvar_linha') {
            $codigo = trim((string) (isset($_POST['codigo']) ? $_POST['codigo'] : ''));
            $descricao = trim((string) (isset($_POST['descricao']) ? $_POST['descricao'] : ''));
            $quantidadeInput = isset($_POST['quantidade']) ? $_POST['quantidade'] : '';
            $quantidadeNormalizada = '';

            if ($linhaPost <= 0) {
                $flashMessage = 'Linha inválida para salvar.';
                $flashType = 'error';
            } elseif ($descricao === '') {
                $flashMessage = 'Descrição não pode ficar vazia.';
                $flashType = 'error';
            } elseif (!importador_orcamento_pdf_parse_qtd_input($quantidadeInput, $quantidadeNormalizada)) {
                $flashMessage = 'Quantidade inválida. Informe um número válido.';
                $flashType = 'error';
            } else {
                $revisoes[(string) $linhaPost] = array(
                    'codigo' => $codigo,
                    'descricao' => $descricao,
                    'quantidade' => $quantidadeNormalizada,
                );
                if (importador_orcamento_pdf_save_revisao($revisaoPath, $revisoes)) {
                    $flashMessage = 'Linha ' . $linhaPost . ' salva com sucesso.';
                    $flashType = 'success';
                } else {
                    $flashMessage = 'Não foi possível salvar a revisão da linha.';
                    $flashType = 'error';
                }
            }
        } elseif ($action === 'restaurar_linha') {
            if ($linhaPost > 0 && isset($revisoes[(string) $linhaPost])) {
                unset($revisoes[(string) $linhaPost]);
                if (importador_orcamento_pdf_save_revisao($revisaoPath, $revisoes)) {
                    $flashMessage = 'Linha ' . $linhaPost . ' restaurada para o original.';
                    $flashType = 'success';
                } else {
                    $flashMessage = 'Não foi possível restaurar a linha.';
                    $flashType = 'error';
                }
            }
        } elseif ($action === 'gerar_cache_produtos') {
            importador_orcamento_pdf_regenerar_cache_produtos(
                $produtosCacheDir,
                $produtosCachePath,
                $produtosCacheTmp,
                $produtosIndexPath,
                $produtosCacheLockPath,
                $flashMessage,
                $flashType
            );
        } elseif ($action === 'gerar_candidatos_local' || $action === 'gerar_itens_ia') {
            $payloadCandidatos = importador_orcamento_pdf_preparar_candidatos_locais(
                $previewPath,
                $revisoes,
                $itensIaPath,
                $produtosCacheDir,
                $produtosCachePath,
                $produtosCacheTmp,
                $produtosIndexPath,
                $produtosCacheLockPath,
                $itensComCandidatosPath,
                $matchingComparePath,
                $normalizeDebugPath,
                $flashMessage,
                $flashType
            );

            if ($payloadCandidatos !== null) {
                $resumoCandidatos = importador_orcamento_pdf_resumo_candidatos_locais($payloadCandidatos);
                if ($action === 'gerar_candidatos_local') {
                    $payloadSemIa = array(
                        'gerado_em' => date('Y-m-d H:i:s'),
                        'erro' => '',
                        'itens' => array(),
                    );
                    importador_orcamento_pdf_save_sugestoes_ia_payload($sugestoesIaPath, $payloadSemIa);
                    @unlink($sugestoesIaStatePath);
                    if ((int) $resumoCandidatos['total_candidatos'] > 0) {
                        $flashMessage = 'Candidatos locais gerados: '
                            . (int) $resumoCandidatos['itens_com_candidatos'] . ' de ' . (int) $resumoCandidatos['itens_total']
                            . ' itens com candidatos, total ' . (int) $resumoCandidatos['total_candidatos'] . '.';
                        $flashType = 'success';
                    } else {
                        $flashMessage = 'Geração local concluída, mas nenhum candidato foi encontrado para os '
                            . (int) $resumoCandidatos['itens_total'] . ' itens.';
                        $flashType = 'error';
                    }
                } else {
                    global $g;
                    $apiKey = isset($g['C031']['openAIApiKey']) ? trim((string) $g['C031']['openAIApiKey']) : '';
                    $model = getenv('IMPORTADOR_IA_MODEL');
                    if (!$model) {
                        $model = 'gpt-4.1-mini';
                    }
                    if ($apiKey === '') {
                        $flashMessage = 'Chave da IA não configurada. Defina openAIApiKey nas configurações da empresa.';
                        $flashType = 'error';
                    } else {
                        $payloadSugestoesInicial = array(
                            'gerado_em' => date('Y-m-d H:i:s'),
                            'model' => $model,
                            'erro' => '',
                            'itens' => array(),
                        );
                        $okStart = importador_orcamento_pdf_save_sugestoes_ia_payload($sugestoesIaPath, $payloadSugestoesInicial)
                            && importador_orcamento_pdf_save_json_pretty($sugestoesIaStatePath, importador_orcamento_pdf_build_sugestoes_ia_state($payloadCandidatos, $model));
                        if ($okStart) {
                            $flashMessage = 'Processamento da IA iniciado em etapas. Aguarde a conclusão na tela.';
                            $flashType = 'success';
                        } else {
                            $flashMessage = 'Candidatos locais gerados, mas não foi possível iniciar o processamento em etapas da IA.';
                            $flashType = 'error';
                        }
                    }
                }
            }

            if ($flashType === 'error' && $flashMessage === '') {
                $flashMessage = 'Não foi possível gerar o arquivo consolidado para IA.';
                $flashType = 'error';
            }
        } elseif ($action === 'salvar_selecao_item') {
            $confirmado = isset($_POST['confirmado']) && (string) $_POST['confirmado'] === '1';
            $produtoId = isset($_POST['produto_id']) ? (int) $_POST['produto_id'] : 0;
            $produtoCodigo = trim((string) (isset($_POST['produto_codigo']) ? $_POST['produto_codigo'] : ''));
            $produtoDescricao = trim((string) (isset($_POST['produto_descricao']) ? $_POST['produto_descricao'] : ''));
            $origem = trim((string) (isset($_POST['origem']) ? $_POST['origem'] : ''));
            if ($linhaPost <= 0) {
                $flashMessage = 'Linha inválida para salvar seleção.';
                $flashType = 'error';
            } else {
                $selecoes[(string) $linhaPost] = array(
                    'confirmado' => $confirmado,
                    'produto' => ($produtoId > 0 || $produtoCodigo !== '' || $produtoDescricao !== '') ? array(
                        'D001_Id' => $produtoId,
                        'codigo' => $produtoCodigo,
                        'descricao' => $produtoDescricao,
                    ) : null,
                    'origem' => $origem !== '' ? $origem : 'manual',
                );
                if (importador_orcamento_pdf_save_selecao_produtos($selecaoProdutosPath, $selecoes)) {
                    $flashMessage = 'Selecao da linha ' . $linhaPost . ' atualizada.';
                    $flashType = 'success';
                } else {
                    $flashMessage = 'Não foi possível salvar a seleção do produto.';
                    $flashType = 'error';
                }
            }
        } elseif ($action === 'inserir_orcamento') {
            $auditPath = $baseDir . '/insercao_orcamento.json';
            $lockPath = $baseDir . '/insercao_orcamento.lock';
            $lockFp = @fopen($lockPath, 'c');
            if (!$lockFp || !@flock($lockFp, LOCK_EX | LOCK_NB)) {
                $flashMessage = 'Já existe uma inclusão em andamento. Aguarde alguns segundos e tente novamente.';
                $flashType = 'error';
            } elseif (!is_file($previewPath)) {
                $flashMessage = 'Import preview não encontrado para inclusão.';
                $flashType = 'error';
            } else {
                require_once __DIR__ . '/../../bibliotecas/classes/VEN001.php';
                require_once __DIR__ . '/../../bibliotecas/classes/GFormWrap.php';

                $linhasJaInseridas = importador_orcamento_pdf_linhas_ja_inseridas($auditPath);
                list($okItens, $erroItens, $itensParaInserir) = importador_orcamento_pdf_montar_itens_para_insercao($previewPath, $revisoes, $selecoes, $linhasJaInseridas);

                if (!$okItens) {
                    $flashMessage = $erroItens !== '' ? $erroItens : 'Nenhum item confirmado com produto selecionado para inserir.';
                    $flashType = 'error';
                } else {
                    $hashBase = array_map(function ($x) {
                        return array(
                            'l' => $x['linha'],
                            'd001' => isset($x['produto']['D001_Id']) ? (int) $x['produto']['D001_Id'] : 0,
                            'q' => isset($x['item']['quantidade']) ? (string) $x['item']['quantidade'] : '',
                            'd' => isset($x['item']['descricao']) ? (string) $x['item']['descricao'] : '',
                        );
                    }, $itensParaInserir);
                    $executionHash = sha1(json_encode($hashBase, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    $lastAudit = importador_orcamento_pdf_load_json_file($auditPath);
                    if (isset($lastAudit['status']) && $lastAudit['status'] === 'sucesso' && isset($lastAudit['execution_hash']) && $lastAudit['execution_hash'] === $executionHash) {
                        $flashMessage = 'Este mesmo conjunto já foi inserido recentemente. Operação ignorada para evitar duplicidade.';
                        $flashType = 'error';
                    } else {
                        $VEN001 = new VEN001();
                        list($okCtx, $erroCtx, $ctxT003) = importador_orcamento_pdf_contexto_orcamento($T003_Id);
                        if (!$okCtx) {
                            $flashMessage = $erroCtx;
                            $flashType = 'error';
                            if ($lockFp) {
                                @flock($lockFp, LOCK_UN);
                                @fclose($lockFp);
                            }
                            return;
                        }
                        $historicoResultadosOk = importador_orcamento_pdf_resultados_ok_historico($auditPath);
                        $audit = array(
                            'gerado_em' => date('Y-m-d H:i:s'),
                            'status' => 'processando',
                            'T003_Id' => (int) $T003_Id,
                            'execution_hash' => $executionHash,
                            'itens_total_confirmados' => count($itensParaInserir),
                            'itens_inseridos' => 0,
                            'falhas' => array(),
                            'resultados' => $historicoResultadosOk,
                        );

                        foreach ($itensParaInserir as $entry) {
                            $linha = (int) $entry['linha'];
                            $item = $entry['item'];
                            $produto = $entry['produto'];

                            list($okD009, $erroD009, $produtoRow) = importador_orcamento_pdf_resolver_d009_por_d001(
                                isset($produto['D001_Id']) ? (int) $produto['D001_Id'] : 0,
                                isset($ctxT003['T003_C004_Id']) ? (int) $ctxT003['T003_C004_Id'] : 0
                            );
                            if (!$okD009) {
                                $audit['falhas'][] = array('linha' => $linha, 'erro' => $erroD009);
                                $audit['resultados'][] = array('linha' => $linha, 'status' => 'erro', 'erro' => $erroD009);
                                continue;
                            }

                            $formData = importador_orcamento_pdf_build_form_incluir_produto($T003_Id, $item, $produtoRow, $entry['preco_unitario']);
                            $Destino = isset($ctxT003['T003_Flag_Destino_Produto']) ? (string) $ctxT003['T003_Flag_Destino_Produto'] : '';
                            $formData['T004_D006_Id'] = '';
                            $formData['T004_Flag_Destino_Produto'] = $Destino;
                            $formData['T004_Aliquota_IPI'] = '0';
                            $formData['T004A_Quantidade_Conversao'] = isset($formData['T004_Quantidade']) ? $formData['T004_Quantidade'] : '0,000';

                            $form = new GFormWrap($formData);
                            $retorno = $VEN001->incluirProduto($form);
                            if ($retorno === true) {
                                $audit['itens_inseridos']++;
                                $audit['resultados'][] = array('linha' => $linha, 'status' => 'ok', 'D001_Id' => (int) $produto['D001_Id']);
                            } else {
                                $errMsg = is_string($retorno) ? $retorno : 'Falha desconhecida ao incluir item.';
                                $audit['falhas'][] = array('linha' => $linha, 'erro' => $errMsg);
                                $audit['resultados'][] = array('linha' => $linha, 'status' => 'erro', 'erro' => $errMsg);
                            }
                        }

                        $audit['status'] = empty($audit['falhas']) ? 'sucesso' : 'parcial';
                        @file_put_contents($auditPath, json_encode($audit, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

                        if (empty($audit['falhas'])) {
                            $flashMessage = 'Inclusão concluída com sucesso. Itens inseridos: ' . (int) $audit['itens_inseridos'] . '.';
                            $flashType = 'success';
                        } else {
                            $primeiraFalha = $audit['falhas'][0];
                            $flashMessage = 'Inclusão parcial. Inseridos: ' . (int) $audit['itens_inseridos'] . '. Primeira falha na linha ' . (int) $primeiraFalha['linha'] . ': ' . $primeiraFalha['erro'];
                            $flashType = 'error';
                        }
                    }
                }
            }
            if ($lockFp) {
                @flock($lockFp, LOCK_UN);
                @fclose($lockFp);
            }
        }

        if ($action !== '') {
            if ($flashMessage !== '') {
                importador_orcamento_pdf_revisao_flash_set($flashMessage, $flashType);
            }
            header('Location: ' . importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id));
            exit;
        }
    }

    if (!is_file($previewPath)) {
        $loadMessage = 'Nenhum JSON de importação encontrado para este orçamento. Importe um PDF antes de revisar.';
    } else {
        $json = json_decode((string) @file_get_contents($previewPath), true);
        $items = isset($json['items']) && is_array($json['items']) ? $json['items'] : array();
        if ($baseKnowledgeActive) {
            $enrichedData = importador_orcamento_pdf_load_json_file($enrichedPath);
            $enrichmentDiag = importador_orcamento_pdf_load_json_file($enrichmentDiagPath);
            $enrichmentState = importador_orcamento_pdf_load_json_file($enrichmentStatePath);
            if (isset($enrichmentState['status']) && $enrichmentState['status'] === 'processing') {
                $enrichmentUi['status'] = 'processing';
                $enrichmentUi['processed_items'] = isset($enrichmentState['next_index']) ? (int) $enrichmentState['next_index'] : 0;
                $enrichmentUi['attention_items'] = isset($enrichmentState['attention_count']) ? (int) $enrichmentState['attention_count'] : 0;
                $enrichmentUi['duration_ms'] = isset($enrichmentState['duration_ms']) ? (int) $enrichmentState['duration_ms'] : 0;
                $enrichmentUi['calls'] = isset($enrichmentState['calls']) ? (int) $enrichmentState['calls'] : 0;
                $enrichmentUi['total_items'] = isset($enrichmentState['total_items']) ? (int) $enrichmentState['total_items'] : count($items);
            }
            if ($enrichmentUi['status'] !== 'processing') {
                $enrichmentUi['duration_ms'] = isset($enrichmentDiag['duration_ms']) ? (int) $enrichmentDiag['duration_ms'] : 0;
                $enrichmentUi['message'] = isset($enrichmentDiag['message']) ? (string) $enrichmentDiag['message'] : '';
                $enrichmentUi['error_type'] = isset($enrichmentDiag['error_type']) ? (string) $enrichmentDiag['error_type'] : '';
                $enrichmentUi['processed_items'] = isset($enrichedData['items']) && is_array($enrichedData['items']) ? count($enrichedData['items']) : 0;
            }
            if ($enrichmentUi['status'] === 'processing') {
            } elseif (isset($enrichedData['status']) && $enrichedData['status'] === 'failed') {
                $enrichmentUi['status'] = 'failed';
            } elseif (isset($enrichedData['status']) && in_array($enrichedData['status'], array('success', 'partial'), true)) {
                $enrichmentUi['status'] = $enrichedData['status'];
                foreach ($enrichedData['items'] as $enrichedItem) {
                    if (is_array($enrichedItem) && isset($enrichedItem['status']) && $enrichedItem['status'] === 'attention') {
                        $enrichmentUi['attention_items']++;
                    }
                }
            }
        }
        $revisoes = importador_orcamento_pdf_load_revisao($revisaoPath);
        $selecoes = importador_orcamento_pdf_load_selecao_produtos($selecaoProdutosPath);
        $candidatosDiagnosticos = importador_orcamento_pdf_load_candidatos_diagnosticos_map($itensComCandidatosPath);
        $sugestoesIaData = importador_orcamento_pdf_load_sugestoes_ia($sugestoesIaPath);
        $sugestoesIaMap = isset($sugestoesIaData['map']) && is_array($sugestoesIaData['map']) ? $sugestoesIaData['map'] : array();
        $insercaoAudit = importador_orcamento_pdf_load_json_file($insercaoAuditPath);
        $linhasInseridas = array();
        $auditResultados = isset($insercaoAudit['resultados']) && is_array($insercaoAudit['resultados']) ? $insercaoAudit['resultados'] : array();
        foreach ($auditResultados as $auditResultado) {
            if (!is_array($auditResultado)) {
                continue;
            }
            $linhaAudit = isset($auditResultado['linha']) ? (int) $auditResultado['linha'] : 0;
            $statusAudit = isset($auditResultado['status']) ? (string) $auditResultado['status'] : '';
            if ($linhaAudit > 0 && $statusAudit === 'ok') {
                $linhasInseridas[(string) $linhaAudit] = true;
            }
        }
        if ($flashMessage === '' && isset($sugestoesIaData['erro']) && trim((string) $sugestoesIaData['erro']) !== '') {
            $flashMessage = 'Aviso IA: ' . trim((string) $sugestoesIaData['erro']);
            $flashType = 'error';
        }
        $enrichedItemsBySourceIndex = array();
        if (isset($enrichedData['items']) && is_array($enrichedData['items'])) {
            foreach ($enrichedData['items'] as $enrichedItem) {
                if (!is_array($enrichedItem) || !isset($enrichedItem['source_index']) || !is_int($enrichedItem['source_index'])) {
                    continue;
                }
                $enrichedItemsBySourceIndex[(string) $enrichedItem['source_index']] = $enrichedItem;
            }
        }

        foreach ($items as $idx => $item) {
            $linha = $idx + 1;
            $codigoOriginal = trim((string) (isset($item['codeClient']) ? $item['codeClient'] : ''));
            $descricaoOriginal = trim((string) (isset($item['description']) ? $item['description'] : ''));
            $quantidadeOriginal = importador_orcamento_pdf_fmt_qtd(isset($item['quantity']) ? $item['quantity'] : null);
            $unidade = trim((string) (isset($item['unit']) ? $item['unit'] : ''));

            $status = 'lido';
            $isError = ($descricaoOriginal === '' && $quantidadeOriginal === '');
            if ($isError) {
                $status = 'erro';
            } elseif ($descricaoOriginal === '' || $quantidadeOriginal === '') {
                $status = 'incompleto';
            }

            $revisado = null;
            if (isset($revisoes[(string) $linha]) && is_array($revisoes[(string) $linha])) {
                $revisado = array(
                    'codigo' => trim((string) (isset($revisoes[(string) $linha]['codigo']) ? $revisoes[(string) $linha]['codigo'] : '')),
                    'descricao' => trim((string) (isset($revisoes[(string) $linha]['descricao']) ? $revisoes[(string) $linha]['descricao'] : '')),
                    'quantidade' => trim((string) (isset($revisoes[(string) $linha]['quantidade']) ? $revisoes[(string) $linha]['quantidade'] : '')),
                );
            }

            $codigoExibido = $revisado ? $revisado['codigo'] : $codigoOriginal;
            $descricaoExibida = $revisado ? $revisado['descricao'] : $descricaoOriginal;
            $quantidadeExibida = $revisado ? $revisado['quantidade'] : $quantidadeOriginal;
            $ajustado = $revisado !== null;

            if ($status === 'lido') {
                $summary['lidos']++;
            } else {
                $summary['incompletos_ou_erro']++;
            }

            $sugestaoIa = isset($sugestoesIaMap[(string) $linha]) ? $sugestoesIaMap[(string) $linha] : null;
            $produtoSugerido = $sugestaoIa && isset($sugestaoIa['produto_sugerido']) && is_array($sugestaoIa['produto_sugerido']) ? $sugestaoIa['produto_sugerido'] : null;
            $confiancaIa = $sugestaoIa && isset($sugestaoIa['percentual_confianca']) ? (float) $sugestaoIa['percentual_confianca'] : null;
            $motivoIa = $sugestaoIa && isset($sugestaoIa['motivo']) ? trim((string) $sugestaoIa['motivo']) : '';
            $preSelecionarIa = $sugestaoIa && !empty($sugestaoIa['pre_selecionar']);

            $selecao = isset($selecoes[(string) $linha]) && is_array($selecoes[(string) $linha]) ? $selecoes[(string) $linha] : array();
            $produtoSelecionado = isset($selecao['produto']) && is_array($selecao['produto']) ? $selecao['produto'] : $produtoSugerido;
            $confirmado = isset($selecao['confirmado']) ? (bool) $selecao['confirmado'] : ($preSelecionarIa && is_array($produtoSugerido));
            $jaInserido = isset($linhasInseridas[(string) $linha]);
            if (!is_array($produtoSelecionado)) {
                $summary['sem_vinculo']++;
            }
            if ($confirmado && is_array($produtoSelecionado)) {
                $summary['confirmados']++;
            }
            $candidatosEstado = isset($candidatosDiagnosticos[(string) $linha]) && is_array($candidatosDiagnosticos[(string) $linha]) ? $candidatosDiagnosticos[(string) $linha] : array();
            $diagnosticoCandidatos = isset($candidatosEstado['diagnostico']) && is_array($candidatosEstado['diagnostico']) ? $candidatosEstado['diagnostico'] : array();
            $candidatosDesatualizados = $revisado !== null && (
                !isset($candidatosDiagnosticos[(string) $linha])
                || trim((string) ($candidatosEstado['codigo'] ?? '')) !== $codigoExibido
                || trim((string) ($candidatosEstado['descricao'] ?? '')) !== $descricaoExibida
                || trim((string) ($candidatosEstado['quantidade'] ?? '')) !== $quantidadeExibida
            );
            $enrichmentConflict = isset($diagnosticoCandidatos['enrichment_conflict']) && is_array($diagnosticoCandidatos['enrichment_conflict']) ? $diagnosticoCandidatos['enrichment_conflict'] : array();
            $hasEnrichmentConflict = isset($diagnosticoCandidatos['busca_dict_estrategia_final'])
                && $diagnosticoCandidatos['busca_dict_estrategia_final'] === 'enrichment_conflict_kept_original'
                && !empty($enrichmentConflict)
                && !$candidatosDesatualizados;
            $enrichmentItem = isset($enrichedItemsBySourceIndex[(string) $idx]) ? $enrichedItemsBySourceIndex[(string) $idx] : array();
            $enrichmentForUi = empty($enrichmentItem) ? array() : array(
                'status' => isset($enrichmentItem['status']) ? (string) $enrichmentItem['status'] : '',
                'search_description' => isset($enrichmentItem['search_description']) ? (string) $enrichmentItem['search_description'] : '',
                'confidence' => isset($enrichmentItem['confidence']) && is_numeric($enrichmentItem['confidence']) ? (float) $enrichmentItem['confidence'] : null,
                'attributes' => isset($enrichmentItem['attributes']) && is_array($enrichmentItem['attributes']) ? $enrichmentItem['attributes'] : array(),
                'pending_confirmations' => isset($enrichmentItem['pending_confirmations']) && is_array($enrichmentItem['pending_confirmations']) ? array_values($enrichmentItem['pending_confirmations']) : array(),
                'contradictions' => isset($enrichmentItem['contradictions']) && is_array($enrichmentItem['contradictions']) ? array_values($enrichmentItem['contradictions']) : array(),
                'evidence' => isset($enrichmentItem['evidence']) && is_array($enrichmentItem['evidence']) ? array_values($enrichmentItem['evidence']) : array(),
                'enrichment_error' => isset($enrichmentItem['enrichment_error']) ? (string) $enrichmentItem['enrichment_error'] : '',
            );
            $descricaoBase = trim((string) ($enrichmentForUi['search_description'] ?? ''));
            $mostrarDescricaoBase = $descricaoBase !== ''
                && trim((string) ($enrichmentForUi['enrichment_error'] ?? '')) === ''
                && importador_orcamento_pdf_busca_normalize_alias($descricaoBase) !== importador_orcamento_pdf_busca_normalize_alias($descricaoExibida);
            $baseAlertStatus = 'not_applied';
            $baseAlertCount = 0;
            $baseAlertTitle = 'A Base de Conhecimento ainda não foi aplicada a este item.';
            if (!empty($enrichmentForUi)) {
                $baseAlertCount = count($enrichmentForUi['pending_confirmations']) + count($enrichmentForUi['contradictions']);
                if (trim((string) $enrichmentForUi['enrichment_error']) !== '') {
                    $baseAlertStatus = 'error';
                    $baseAlertTitle = 'O enriquecimento deste item falhou. Abra Revisar para ver o motivo.';
                } elseif ($baseAlertCount > 0 || $enrichmentForUi['status'] === 'attention') {
                    $baseAlertStatus = 'attention';
                    $baseAlertTitle = 'A Base de Conhecimento encontrou informações para conferência. Abra Revisar para ver os detalhes.';
                } else {
                    $baseAlertStatus = 'ok';
                    $baseAlertTitle = 'Base de Conhecimento aplicada sem alertas.';
                }
            }
            if ($hasEnrichmentConflict) {
                $baseAlertCount++;
                if ($baseAlertStatus !== 'error') {
                    $baseAlertStatus = 'attention';
                    $baseAlertTitle = 'A Base de Conhecimento encontrou uma diferença técnica para conferência. Abra Revisar para ver os detalhes.';
                }
            }

            $rows[] = array(
                'linha' => $linha,
                'codigo' => $codigoExibido,
                'descricao' => $descricaoExibida,
                'quantidade' => $quantidadeExibida,
                'unidade' => $unidade,
                'status' => $status,
                'status_label' => importador_orcamento_pdf_status_label($status),
                'ajustado_manual' => $ajustado,
                'produto_sugerido' => $produtoSugerido,
                'produto_selecionado' => $produtoSelecionado,
                'confianca_ia' => $confiancaIa,
                'motivo_ia' => $motivoIa,
                'confirmado' => $confirmado,
                'pre_selecionar_ia' => $preSelecionarIa,
                'ja_inserido' => $jaInserido,
                'candidatos_desatualizados' => $candidatosDesatualizados,
                'enrichment_conflict' => $hasEnrichmentConflict ? $enrichmentConflict : array(),
                'enrichment' => $enrichmentForUi,
                'descricao_base' => $mostrarDescricaoBase ? $descricaoBase : '',
                'base_alert' => array(
                    'status' => $baseAlertStatus,
                    'count' => $baseAlertCount,
                    'title' => $baseAlertTitle,
                ),
                'produto_escolhido' => null,
                'original' => array(
                    'codigo' => $codigoOriginal,
                    'descricao' => $descricaoOriginal,
                    'quantidade' => $quantidadeOriginal,
                    'unidade' => $unidade,
                ),
                'revisado' => $revisado,
            );
        }

        $summary['total_items'] = count($rows);
    }
}

importador_orcamento_pdf_header('Revisão da importação', array('subtitle' => 'Orçamento ' . $T003_Id));
?>
<section class="panel">
    <?php if (!$ok): ?>
        <h2>Acesso indisponível</h2>
        <p><?php echo importador_orcamento_pdf_h($message); ?></p>
    <?php else: ?>
        <div class="section-head">
            <div>
                <span class="badge">Conferência</span>
                <h2>Itens extraídos do PDF</h2>
            </div>
            <div class="actions actions--tight">
                <a class="button" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('index.php?T003_Id=' . $T003_Id)); ?>">Tela inicial</a>
                <a class="button" href="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('upload.php?T003_Id=' . $T003_Id)); ?>">Importar novo PDF</a>
                <form method="post" class="inline-form js-form-gerar-cache-produtos" style="display:none;" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>" data-processing-form="1" data-processing-text="Gerando cache de produtos...">
                    <input type="hidden" name="action" value="gerar_cache_produtos">
                    <button class="button js-btn-gerar-cache-produtos" type="submit" data-processing-text="Gerando cache de produtos...">Gerar Cache</button>
                </form>
                <?php if ($baseKnowledgeActive): ?>
                    <form method="post" class="inline-form js-form-enriquecer" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>" data-processing-form="1" data-processing-text="Aplicando Base de Conhecimento aos <?php echo (int) $summary['total_items']; ?> itens. Este processo pode levar alguns segundos." data-processing-active="<?php echo $enrichmentUi['status'] === 'processing' ? '1' : '0'; ?>">
                        <input type="hidden" name="action" value="aplicar_base_conhecimento">
                        <input type="hidden" name="csrf_token" value="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_csrf_token($enrichmentCsrfScope)); ?>">
                        <button class="button button--primary js-btn-enriquecer" type="submit" data-processing-text="Aplicando Base de Conhecimento aos <?php echo (int) $summary['total_items']; ?> itens. Este processo pode levar alguns segundos." <?php echo $enrichmentUi['status'] === 'processing' ? 'disabled' : ''; ?>><?php echo $enrichmentUi['status'] === 'not_applied' ? 'Aplicar Base de Conhecimento' : ($enrichmentUi['status'] === 'processing' ? 'Aplicando Base de Conhecimento...' : 'Reaplicar Base de Conhecimento'); ?></button>
                    </form>
                <?php endif; ?>
                <form method="post" class="inline-form js-form-gerar-candidatos-local" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>" data-processing-form="1" data-processing-text="Gerando candidatos locais...">
                    <input type="hidden" name="action" value="gerar_candidatos_local">
                    <button class="button js-btn-gerar-candidatos-local" type="submit" data-processing-text="Gerando candidatos locais...">Gerar Candidatos</button>
                </form>
                <form method="post" class="inline-form js-form-sugerir-ia" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>" data-processing-form="1" data-processing-text="Processando sugestões com IA...">
                    <input type="hidden" name="action" value="gerar_itens_ia">
                    <button class="button button--primary js-btn-sugerir-ia" type="submit" data-processing-text="Processando sugestões com IA...">Sugerir Produtos (IA)</button>
                </form>
                <form method="post" class="inline-form js-form-inserir-orcamento" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>" data-processing-form="1" data-processing-text="Inserindo itens no orçamento..." onsubmit="return confirm('Confirmar inserção dos itens selecionados no orçamento? Esta ação irá incluir os itens marcados e pode levar alguns segundos.');">
                    <input type="hidden" name="action" value="inserir_orcamento">
                    <button class="button js-btn-inserir-orcamento" type="submit" data-processing-text="Inserindo itens no orçamento..." <?php echo (int) $summary['confirmados'] > 0 ? '' : 'disabled'; ?>>Inserir no Orçamento</button>
                </form>
            </div>
        </div>

        <?php if ($flashMessage !== ''): ?>
            <p class="flash flash--<?php echo importador_orcamento_pdf_h($flashType); ?>"><?php echo importador_orcamento_pdf_h($flashMessage); ?></p>
        <?php endif; ?>
        <p class="flash flash--info js-loading-progresso" style="display:none;"></p>

        <?php if ($loadMessage !== ''): ?>
            <p><?php echo importador_orcamento_pdf_h($loadMessage); ?></p>
        <?php else: ?>
            <div class="review-hint">
                <span>Revise os itens e confirme os corretos antes de inserir no orçamento.</span>
                <button type="button" class="button button--sm js-open-help">? Como funciona</button>
            </div>
            <div class="review-toolbar">
                <div class="review-toolbar__filters">
                    <label>Confirmar
                        <select class="input-inline js-filter-confirmar">
                            <option value="todos">Todos</option>
                            <option value="sim">Confirmados</option>
                            <option value="nao">Não confirmados</option>
                        </select>
                    </label>
                    <label>Produto selecionado
                        <select class="input-inline js-filter-produto">
                            <option value="todos">Todos</option>
                            <option value="com">Com produto</option>
                            <option value="sem">Sem produto</option>
                        </select>
                    </label>
                    <label>Confiança IA
                        <select class="input-inline js-filter-confianca">
                            <option value="todos">Todos</option>
                            <option value="alta">Alta (&gt;= 90)</option>
                            <option value="media">Média (70 a 89)</option>
                            <option value="baixa">Baixa (&lt; 70)</option>
                            <option value="sem">Sem IA</option>
                        </select>
                    </label>
                    <label>Status da leitura
                        <select class="input-inline js-filter-status">
                            <option value="todos">Todos</option>
                            <option value="lido">Lidos</option>
                            <option value="incompleto_erro">Incompletos ou erro</option>
                        </select>
                    </label>
                    <label>Alertas da Base
                        <select class="input-inline js-filter-alerta-base">
                            <option value="todos">Todos</option>
                            <option value="com_alertas">Com alertas</option>
                            <option value="falha">Falha no enriquecimento</option>
                            <option value="sem_alertas">Aplicada sem alertas</option>
                        </select>
                    </label>
                    <label>Código
                        <input type="text" class="input-inline js-filter-codigo" placeholder="Filtrar código">
                    </label>
                    <label>Descrição
                        <input type="text" class="input-inline js-filter-descricao" placeholder="Filtrar descrição">
                    </label>
                    <button type="button" class="button button--sm js-limpar-filtros">Limpar filtros</button>
                </div>
                <div class="review-toolbar__mass">
                    <button type="button" class="button button--sm js-marcar-validos">Marcar todos válidos</button>
                    <button type="button" class="button button--sm js-desmarcar-todos">Desmarcar todos</button>
                </div>
            </div>
            <div class="table-wrap">
                <table class="grid">
                    <thead>
                    <tr>
                        <th>Linha</th>
                        <th>Código</th>
                        <th>Descrição</th>
                        <th>Quantidade</th>
                        <th>Unidade</th>
                        <th>Status</th>
                        <th>Produto Selecionado</th>
                        <th>Confiança IA</th>
                        <th>Alertas da Base</th>
                        <th>Confirmar</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr class="<?php echo !empty($row['ja_inserido']) ? 'row--inserido' : ''; ?>" data-linha="<?php echo (int) $row['linha']; ?>"
                            data-confirmado="<?php echo !empty($row['confirmado']) ? '1' : '0'; ?>"
                            data-tem-produto="<?php echo is_array($row['produto_selecionado']) ? '1' : '0'; ?>"
                            data-status="<?php echo importador_orcamento_pdf_h($row['status']); ?>"
                            data-enrichment-conflict="<?php echo !empty($row['enrichment_conflict']) ? '1' : '0'; ?>"
                            data-base-alert-status="<?php echo importador_orcamento_pdf_h($row['base_alert']['status']); ?>"
                            data-candidatos-desatualizados="<?php echo !empty($row['candidatos_desatualizados']) ? '1' : '0'; ?>"
                            data-confianca="<?php echo $row['confianca_ia'] !== null ? (float) $row['confianca_ia'] : ''; ?>">
                            <td><?php echo (int) $row['linha']; ?></td>
                            <td>
                                <span class="view-mode"><?php echo importador_orcamento_pdf_h($row['codigo']); ?></span>
                                <input class="edit-mode input-inline" type="text" name="codigo" value="<?php echo importador_orcamento_pdf_h($row['codigo']); ?>" style="display:none;">
                            </td>
                            <td>
                                <span class="view-mode"><?php echo importador_orcamento_pdf_h($row['descricao']); ?></span>
                                <?php if ($row['descricao_base'] !== ''): ?>
                                    <span class="base-description-inline" title="<?php echo importador_orcamento_pdf_h($row['descricao_base']); ?>">
                                        <strong>Base:</strong> <?php echo importador_orcamento_pdf_h($row['descricao_base']); ?>
                                    </span>
                                <?php endif; ?>
                                <input class="edit-mode input-inline" type="text" name="descricao" value="<?php echo importador_orcamento_pdf_h($row['descricao']); ?>" style="display:none;">
                            </td>
                            <td>
                                <span class="view-mode"><?php echo importador_orcamento_pdf_h($row['quantidade']); ?></span>
                                <input class="edit-mode input-inline" type="text" name="quantidade" value="<?php echo importador_orcamento_pdf_h($row['quantidade']); ?>" style="display:none;">
                            </td>
                            <td><?php echo importador_orcamento_pdf_h($row['unidade']); ?></td>
                            <td>
                                <span class="status status--<?php echo importador_orcamento_pdf_h($row['status']); ?>">
                                    <?php echo importador_orcamento_pdf_h($row['status_label']); ?>
                                </span>
                                <?php if (!empty($row['ja_inserido'])): ?>
                                    <span class="badge badge--inserted">Inserido</span>
                                <?php endif; ?>
                                <?php if ($row['ajustado_manual']): ?>
                                    <span class="badge badge--adjusted">Ajustado</span>
                                <?php endif; ?>
                            </td>
                            <td class="js-produto-selecionado-cell">
                                <?php if (is_array($row['produto_selecionado'])): ?>
                                    <?php
                                    $ps = $row['produto_selecionado'];
                                    $txtPs = trim((string) (isset($ps['codigo']) ? $ps['codigo'] : '') . ' - ' . (string) (isset($ps['descricao']) ? $ps['descricao'] : ''));
                                    ?>
                                    <?php echo importador_orcamento_pdf_h($txtPs); ?>
                                <?php else: ?>
                                    Não vinculado
                                <?php endif; ?>
                            </td>
                            <td class="js-confianca-cell">
                                <?php if ($row['confianca_ia'] !== null): ?>
                                    <?php echo importador_orcamento_pdf_h(number_format((float) $row['confianca_ia'], 0, ',', '') . '%'); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['base_alert']['status'] === 'error'): ?>
                                    <span class="badge badge--base-error" title="<?php echo importador_orcamento_pdf_h($row['base_alert']['title']); ?>">⛔ Falha na base</span>
                                <?php elseif ($row['base_alert']['status'] === 'attention'): ?>
                                    <span class="badge badge--base-attention" title="<?php echo importador_orcamento_pdf_h($row['base_alert']['title']); ?>">⚠ Conferir<?php echo $row['base_alert']['count'] > 0 ? ' (' . (int) $row['base_alert']['count'] . ')' : ''; ?></span>
                                <?php elseif ($row['base_alert']['status'] === 'ok'): ?>
                                    <span class="badge badge--base-ok" title="<?php echo importador_orcamento_pdf_h($row['base_alert']['title']); ?>">✓ Base usada</span>
                                <?php else: ?>
                                    <span class="muted" title="<?php echo importador_orcamento_pdf_h($row['base_alert']['title']); ?>">— Não aplicada</span>
                                <?php endif; ?>
                                <?php if (!empty($row['candidatos_desatualizados'])): ?>
                                    <span class="badge" title="O item foi alterado após a geração dos candidatos">↻ Atualizar</span>
                                <?php endif; ?>
                            </td>
                            <td class="confirm-cell">
                                <input type="checkbox" class="js-confirmar-item" title="Confirmar item para inserção no orçamento" <?php echo (!empty($row['confirmado']) ? 'checked' : ''); ?>>
                            </td>
                            <td class="actions-cell">
                                <div class="row-actions acoes-container">
                                    <button type="button" class="button button--sm js-revisar-produto" title="Abrir revisão completa do item">Revisar</button>

                                    <form method="post" class="inline-form js-save-form" style="display:none;" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>">
                                        <input type="hidden" name="action" value="salvar_linha">
                                        <input type="hidden" name="linha" value="<?php echo (int) $row['linha']; ?>">
                                        <input type="hidden" name="codigo" value="">
                                        <input type="hidden" name="descricao" value="">
                                        <input type="hidden" name="quantidade" value="">
                                    </form>

                                    <form method="post" class="inline-form js-selecao-form" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>">
                                        <input type="hidden" name="action" value="salvar_selecao_item">
                                        <input type="hidden" name="linha" value="<?php echo (int) $row['linha']; ?>">
                                        <input type="hidden" name="confirmado" value="<?php echo !empty($row['confirmado']) ? '1' : '0'; ?>">
                                        <input type="hidden" name="produto_id" value="<?php echo is_array($row['produto_selecionado']) && isset($row['produto_selecionado']['D001_Id']) ? (int) $row['produto_selecionado']['D001_Id'] : 0; ?>">
                                        <input type="hidden" name="produto_codigo" value="<?php echo is_array($row['produto_selecionado']) && isset($row['produto_selecionado']['codigo']) ? importador_orcamento_pdf_h($row['produto_selecionado']['codigo']) : ''; ?>">
                                        <input type="hidden" name="produto_descricao" value="<?php echo is_array($row['produto_selecionado']) && isset($row['produto_selecionado']['descricao']) ? importador_orcamento_pdf_h($row['produto_selecionado']['descricao']) : ''; ?>">
                                        <input type="hidden" name="origem" value="<?php echo is_array($row['produto_sugerido']) ? 'ia' : 'manual'; ?>">
                                    </form>

                                    <form method="post" class="inline-form" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('revisar.php?T003_Id=' . $T003_Id)); ?>">
                                        <input type="hidden" class="js-restore-form" name="js_restore_marker" value="1">
                                        <input type="hidden" name="action" value="restaurar_linha">
                                        <input type="hidden" name="linha" value="<?php echo (int) $row['linha']; ?>">
                                        <button type="submit" class="button button--sm button--danger-soft" style="display:none;" title="Voltar ao estado original do item">Desfazer alterações</button>
                                    </form>
                                </div>
                                <div class="js-row-meta"
                                     data-linha="<?php echo (int) $row['linha']; ?>"
                                     data-descricao-original="<?php echo importador_orcamento_pdf_h($row['original']['descricao']); ?>"
                                     data-candidatos-desatualizados="<?php echo !empty($row['candidatos_desatualizados']) ? '1' : '0'; ?>"
                                     data-enrichment-conflict-source="<?php echo $row['ajustado_manual'] ? 'revisao' : 'pdf'; ?>"
                                     data-confianca-ia="<?php echo $row['confianca_ia'] !== null ? importador_orcamento_pdf_h((string) $row['confianca_ia']) : ''; ?>"
                                     data-motivo-ia="<?php echo importador_orcamento_pdf_h($row['motivo_ia']); ?>"
                                     data-enrichment-conflict="<?php echo importador_orcamento_pdf_h(json_encode($row['enrichment_conflict'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>"
                                     data-enrichment="<?php echo importador_orcamento_pdf_h(json_encode($row['enrichment'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>"
                                     data-produto-sugerido="<?php echo importador_orcamento_pdf_h(json_encode($row['produto_sugerido'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>">
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div id="modal-revisar-produto" class="modal" style="display:none;">
                <div class="modal__backdrop js-modal-close"></div>
                <div class="modal__panel">
                    <div class="modal__head">
                        <strong>Revisar produto</strong>
                        <button type="button" class="button button--sm js-modal-close">Fechar</button>
                    </div>
                    <div class="modal__body">
                        <div style="margin-bottom:10px;">
                            <button type="button" class="button button--sm js-modal-help-toggle">Como escolher o produto</button>
                            <div class="js-modal-help-box" style="display:none; margin-top:8px; padding:10px 12px; border:1px solid #d8e2ee; border-radius:8px; background:#f7fafc; font-size:13px; line-height:1.45;">
                                <div>1. Use <strong>Usar sugestão da IA</strong> quando a confiança estiver alta.</div>
                                <div>2. Revise os <strong>Candidatos locais</strong> para comparar opções semelhantes.</div>
                                <div>3. Se necessário, faça <strong>Busca manual no cadastro de produtos</strong>.</div>
                                <div>4. Para ajustar dados do item, use <strong>Salvar alterações do item</strong> ou <strong>Desfazer alterações do item</strong>.</div>
                            </div>
                        </div>
                        <div><strong>Item original:</strong> <span class="js-modal-item-desc"></span></div>
                        <div class="flash flash--info js-modal-enrichment" style="display:none; margin-top:12px;">
                            <strong>Base de conhecimento</strong>
                            <div class="js-modal-enrichment-empty" style="display:none; margin-top:6px;">A base ainda não foi aplicada a este item.</div>
                            <div class="js-modal-enrichment-content" style="display:none; margin-top:6px;">
                                <div><strong>Descrição proposta:</strong> <span class="js-modal-enrichment-description"></span></div>
                                <div><strong>Status:</strong> <span class="js-modal-enrichment-status"></span> · <strong>Confiança:</strong> <span class="js-modal-enrichment-confidence"></span></div>
                                <div class="js-modal-enrichment-attributes" style="margin-top:8px;"></div>
                                <div class="js-modal-enrichment-pending" style="display:none; margin-top:8px;"></div>
                                <div class="js-modal-enrichment-contradictions" style="display:none; margin-top:8px;"></div>
                                <div class="js-modal-enrichment-evidence" style="display:none; margin-top:8px;"></div>
                                <div class="js-modal-enrichment-error flash flash--error" style="display:none; margin-top:8px;"></div>
                            </div>
                        </div>
                        <div class="flash flash--info js-modal-candidatos-desatualizados" style="display:none; margin-top:12px;">
                            Este item foi alterado após a geração dos candidatos. Use <strong>Gerar candidatos deste item</strong> para atualizar a análise da Base de Conhecimento.
                        </div>
                        <div class="flash flash--info js-modal-enrichment-conflict" style="display:none; margin-top:12px;">
                            <strong>Divergência encontrada pela Base de Conhecimento</strong>
                            <div style="margin-top:6px;"><strong class="js-modal-conflict-original-label">Valor informado no PDF:</strong> <span class="js-modal-conflict-original-value"></span></div>
                            <div><strong>Valor calculado pela Base:</strong> <span class="js-modal-conflict-enriched-value"></span></div>
                            <div><strong>Motivo:</strong> <span class="js-modal-conflict-reason"></span></div>
                            <div><strong>Decisão atual:</strong> <span class="js-modal-conflict-resolution"></span></div>
                        </div>
                        <hr>
                        <div><strong>Editar item</strong></div>
                        <div class="modal-edit-grid">
                            <label>Código extraído
                                <input type="text" class="input-inline js-modal-edit-codigo" placeholder="Código">
                            </label>
                            <label>Descrição extraída
                                <input type="text" class="input-inline js-modal-edit-descricao" placeholder="Descrição">
                            </label>
                            <label>Quantidade
                                <input type="text" class="input-inline js-modal-edit-quantidade" placeholder="Quantidade">
                            </label>
                            <div class="modal-edit-actions">
                                <button type="button" class="button button--sm js-modal-salvar-linha" title="Alterar descrição ou quantidade do item">Salvar alterações do item</button>
                                <button type="button" class="button button--sm button--danger-soft js-modal-desfazer-linha" title="Voltar código, descrição e quantidade para o que veio originalmente da importação">Desfazer alterações do item</button>
                                <button type="button" class="button button--sm button--primary js-modal-gerar-candidatos-item">Gerar candidatos deste item</button>
                                <button type="button" class="button button--sm button--primary js-modal-sugerir-ia-item">Sugerir IA deste item</button>
                                <button type="button" class="button button--sm js-modal-open-busca-manual">Busca manual no cadastro</button>
                            </div>
                        </div>
                        <div class="js-modal-status" style="display:none; margin-top:10px;"></div>
                        <hr>
                        <div class="ia-card">
                            <div class="ia-card__title">Sugestão da IA</div>
                            <div class="js-modal-ia-empty" style="display:none;">Nenhuma sugestão da IA gerada para este item.</div>
                            <div class="js-modal-ia-content">
                                <div><strong>Produto:</strong> <span class="js-modal-ia-prod"></span></div>
                                <div><strong>Confiança:</strong> <span class="js-modal-ia-conf"></span></div>
                                <div><strong>Motivo:</strong> <span class="js-modal-ia-motivo"></span></div>
                                <button type="button" class="button button--sm button--primary js-modal-usar-ia">Usar sugestão da IA</button>
                            </div>
                        </div>
                        <hr>
                        <div><strong>Candidatos locais</strong></div>
                        <div class="js-modal-candidatos-locais"><div class="muted">Carregando candidatos...</div></div>
                    </div>
                </div>
            </div>

            <div id="modal-busca-manual" class="modal" style="display:none; align-items:center; justify-content:center; padding:24px;">
                <div class="modal__backdrop js-busca-manual-close"></div>
                <div class="modal__panel modal__panel--small" style="margin:0;">
                    <div class="modal__head">
                        <strong>Busca manual no cadastro</strong>
                        <button type="button" class="button button--sm js-busca-manual-close">Fechar</button>
                    </div>
                    <div class="modal__body">
                        <div class="actions actions--tight">
                            <input type="text" class="input-inline js-modal-busca" placeholder="Buscar por código ou descrição">
                            <button type="button" class="button button--sm js-modal-buscar">Buscar</button>
                        </div>
                        <div class="js-modal-resultados-busca"></div>
                    </div>
                </div>
            </div>

            <div id="modal-ajuda-revisao" class="modal" style="display:none;">
                <div class="modal__backdrop js-help-close"></div>
                <div class="modal__panel modal__panel--small">
                    <div class="modal__head">
                        <strong>Como funciona</strong>
                        <button type="button" class="button button--sm js-help-close">Fechar</button>
                    </div>
                    <div class="modal__body">
                        <div>Como revisar os itens:</div>
                        <div>1. Marque os itens confirmados para inserir no orçamento.</div>
                        <div>2. Clique em <strong>Revisar</strong> na linha para abrir os detalhes do item.</div>
                        <div>3. Dentro de <strong>Revisar</strong>, use <strong>Salvar alterações do item</strong> para ajustar código, descrição ou quantidade.</div>
                        <div>4. Dentro de <strong>Revisar</strong>, use <strong>Desfazer alterações do item</strong> para voltar ao conteúdo original da importação.</div>
                        <div>5. Para definir produto, use <strong>Usar sugestão da IA</strong>, selecione um <strong>Candidato local</strong> ou faça <strong>Busca manual no cadastro de produtos</strong>.</div>
                    </div>
                </div>
            </div>

            <div class="summary">
                <strong>Resumo</strong>
                <div>Total de itens encontrados: <?php echo (int) $summary['total_items']; ?></div>
                <div>Quantidade de itens lidos: <?php echo (int) $summary['lidos']; ?></div>
                <div>Quantidade de itens incompletos/erro: <?php echo (int) $summary['incompletos_ou_erro']; ?></div>
                <div>Quantidade de itens sem produto vinculado: <?php echo (int) $summary['sem_vinculo']; ?></div>
                <div>Quantidade de itens confirmados: <?php echo (int) $summary['confirmados']; ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<script>
(function () {
    'use strict';

    function qsa(scope, sel) { return Array.prototype.slice.call(scope.querySelectorAll(sel)); }
    function qs(scope, sel) { return scope.querySelector(sel); }
    function safeJsonParse(value, fallback) {
        try { return JSON.parse(value || ''); } catch (e) { return fallback; }
    }
    function produtoLabel(prod) {
        if (!prod || !prod.codigo) { return 'Não vinculado'; }
        return String(prod.codigo) + ' - ' + String(prod.descricao || '');
    }

    var modal = document.getElementById('modal-revisar-produto');
    var modalBuscaManual = document.getElementById('modal-busca-manual');
    var modalAjuda = document.getElementById('modal-ajuda-revisao');
    var modalItemDesc = qs(modal, '.js-modal-item-desc');
    var modalEnrichment = qs(modal, '.js-modal-enrichment');
    var modalEnrichmentEmpty = qs(modal, '.js-modal-enrichment-empty');
    var modalEnrichmentContent = qs(modal, '.js-modal-enrichment-content');
    var modalEnrichmentDescription = qs(modal, '.js-modal-enrichment-description');
    var modalEnrichmentStatus = qs(modal, '.js-modal-enrichment-status');
    var modalEnrichmentConfidence = qs(modal, '.js-modal-enrichment-confidence');
    var modalEnrichmentAttributes = qs(modal, '.js-modal-enrichment-attributes');
    var modalEnrichmentPending = qs(modal, '.js-modal-enrichment-pending');
    var modalEnrichmentContradictions = qs(modal, '.js-modal-enrichment-contradictions');
    var modalEnrichmentEvidence = qs(modal, '.js-modal-enrichment-evidence');
    var modalEnrichmentError = qs(modal, '.js-modal-enrichment-error');
    var modalIaProd = qs(modal, '.js-modal-ia-prod');
    var modalIaConf = qs(modal, '.js-modal-ia-conf');
    var modalIaMotivo = qs(modal, '.js-modal-ia-motivo');
    var modalIaEmpty = qs(modal, '.js-modal-ia-empty');
    var modalIaContent = qs(modal, '.js-modal-ia-content');
    var modalUsarIaBtn = qs(modal, '.js-modal-usar-ia');
    var modalBuscaManualOpenBtn = qs(modal, '.js-modal-open-busca-manual');
    var modalBusca = qs(modalBuscaManual, '.js-modal-busca');
    var modalBuscarBtn = qs(modalBuscaManual, '.js-modal-buscar');
    var modalResultadosBusca = qs(modalBuscaManual, '.js-modal-resultados-busca');
    var modalCandidatosLocais = qs(modal, '.js-modal-candidatos-locais');
    var modalHelpToggle = qs(modal, '.js-modal-help-toggle');
    var modalHelpBox = qs(modal, '.js-modal-help-box');
    var modalEditCodigo = qs(modal, '.js-modal-edit-codigo');
    var modalEditDescricao = qs(modal, '.js-modal-edit-descricao');
    var modalEditQuantidade = qs(modal, '.js-modal-edit-quantidade');
    var modalSalvarLinhaBtn = qs(modal, '.js-modal-salvar-linha');
    var modalDesfazerLinhaBtn = qs(modal, '.js-modal-desfazer-linha');
    var modalGerarCandidatosItemBtn = qs(modal, '.js-modal-gerar-candidatos-item');
    var modalSugerirIaItemBtn = qs(modal, '.js-modal-sugerir-ia-item');
    var modalStatus = qs(modal, '.js-modal-status');
    var modalCandidatosDesatualizados = qs(modal, '.js-modal-candidatos-desatualizados');
    var modalEnrichmentConflict = qs(modal, '.js-modal-enrichment-conflict');
    var modalConflictOriginalLabel = qs(modal, '.js-modal-conflict-original-label');
    var modalConflictOriginalValue = qs(modal, '.js-modal-conflict-original-value');
    var modalConflictEnrichedValue = qs(modal, '.js-modal-conflict-enriched-value');
    var modalConflictReason = qs(modal, '.js-modal-conflict-reason');
    var modalConflictResolution = qs(modal, '.js-modal-conflict-resolution');
    var filterConfirmar = qs(document, '.js-filter-confirmar');
    var filterProduto = qs(document, '.js-filter-produto');
    var filterConfianca = qs(document, '.js-filter-confianca');
    var filterStatus = qs(document, '.js-filter-status');
    var filterAlertaBase = qs(document, '.js-filter-alerta-base');
    var filterCodigo = qs(document, '.js-filter-codigo');
    var filterDescricao = qs(document, '.js-filter-descricao');
    var btnLimparFiltros = qs(document, '.js-limpar-filtros');
    var btnMarcarValidos = qs(document, '.js-marcar-validos');
    var btnDesmarcarTodos = qs(document, '.js-desmarcar-todos');
    var currentRow = null;
    var currentIaProduto = null;
    var currentCandidatosRequestId = 0;

    function setModalStatus(type, message) {
        if (!modalStatus) { return; }
        if (!message) {
            modalStatus.style.display = 'none';
            modalStatus.className = 'js-modal-status';
            modalStatus.innerHTML = '';
            return;
        }
        modalStatus.style.display = 'block';
        modalStatus.className = 'js-modal-status flash flash--' + (type || 'info');
        modalStatus.textContent = String(message);
    }

    function enrichmentConflictReasonLabel(reason) {
        var labels = {
            'explicit_original_thickness_conflicts_with_calculated_thickness': 'A espessura informada no PDF é diferente da espessura calculada pela Base de Conhecimento.'
        };
        return labels[reason] || 'A Base de Conhecimento encontrou uma diferença técnica que precisa ser conferida.';
    }

    function enrichmentConflictResolutionLabel(resolution) {
        var labels = {
            'original_candidates_preserved': 'Os candidatos gerados a partir do PDF foram mantidos por segurança.'
        };
        return labels[resolution] || 'Os candidatos originais foram preservados por segurança.';
    }

    function renderEnrichmentConflict(conflict, source) {
        if (!modalEnrichmentConflict) { return; }
        if (!conflict || !conflict.reason) {
            modalEnrichmentConflict.style.display = 'none';
            return;
        }
        modalConflictOriginalLabel.textContent = source === 'revisao' ? 'Valor revisado pelo vendedor:' : 'Valor informado no PDF:';
        modalConflictOriginalValue.textContent = conflict.original_value || '-';
        modalConflictEnrichedValue.textContent = conflict.enriched_value || '-';
        modalConflictReason.textContent = enrichmentConflictReasonLabel(conflict.reason);
        modalConflictResolution.textContent = enrichmentConflictResolutionLabel(conflict.resolution);
        modalEnrichmentConflict.style.display = 'block';
    }

    function enrichmentStatusLabel(status) {
        var labels = { enriched: 'Enriquecido', attention: 'Requer conferência' };
        return labels[status] || (status || 'Não informado');
    }

    function enrichmentAttributeLabel(name) {
        var labels = {
            family: 'Família', type: 'Tipo', dimensions: 'Medidas', finish: 'Acabamento',
            thickness: 'Espessura', perforation: 'Perfuração', unit: 'Unidade'
        };
        return labels[name] || String(name || 'Atributo');
    }

    function enrichmentOriginLabel(origin) {
        var labels = {
            markdown: 'Definido na Base',
            calculated: 'Calculado pela Base',
            inherited: 'Herdado de outro item',
            assumed: 'Assumido pela Base',
            ai_inferred: 'Identificado pela IA'
        };
        return labels[origin] || String(origin || 'Não informada');
    }

    function enrichmentEvidenceLabel(value) {
        return String(value || '')
            .replace(/\(\s*source_index\s*[:=]?\s*(\d+)\s*\)/gi, function (_, index) {
                return '(item ' + (parseInt(index, 10) + 1) + ')';
            })
            .replace(/\bsource_item_index\s*[:=]?\s*(\d+)\b/gi, function (_, index) {
                return 'item ' + (parseInt(index, 10) + 1);
            });
    }

    function renderEnrichmentList(target, title, values, formatValue) {
        var list = Array.isArray(values) ? values.filter(function (value) { return String(value || '').trim() !== ''; }) : [];
        if (!target) { return; }
        target.innerHTML = '';
        target.style.display = list.length ? 'block' : 'none';
        if (!list.length) { return; }
        var heading = document.createElement('strong');
        heading.textContent = title;
        var ul = document.createElement('ul');
        ul.className = 'detail-list';
        list.forEach(function (value) {
            var li = document.createElement('li');
            li.textContent = formatValue ? formatValue(value) : String(value);
            ul.appendChild(li);
        });
        target.appendChild(heading);
        target.appendChild(ul);
    }

    function renderEnrichment(enrichment) {
        if (!modalEnrichment) { return; }
        modalEnrichment.style.display = 'block';
        var hasData = enrichment && typeof enrichment === 'object' && Object.keys(enrichment).length > 0;
        if (!hasData) {
            modalEnrichmentEmpty.style.display = 'block';
            modalEnrichmentContent.style.display = 'none';
            return;
        }

        modalEnrichmentEmpty.style.display = 'none';
        modalEnrichmentContent.style.display = 'block';
        modalEnrichmentDescription.textContent = enrichment.search_description || '-';
        modalEnrichmentStatus.textContent = enrichmentStatusLabel(enrichment.status);
        modalEnrichmentConfidence.textContent = typeof enrichment.confidence === 'number'
            ? Math.round(enrichment.confidence * 100) + '%' : '-';

        modalEnrichmentAttributes.innerHTML = '';
        var attributes = enrichment.attributes && typeof enrichment.attributes === 'object' ? enrichment.attributes : {};
        var attributeNames = Object.keys(attributes);
        if (attributeNames.length) {
            var heading = document.createElement('strong');
            heading.textContent = 'Atributos identificados';
            var ul = document.createElement('ul');
            ul.className = 'detail-list';
            var inheritedByItem = {};
            attributeNames.forEach(function (name) {
                var attribute = attributes[name] && typeof attributes[name] === 'object' ? attributes[name] : {};
                var details = [];
                var label = enrichmentAttributeLabel(name);
                if (attribute.origin === 'inherited') {
                    var sourceItem = typeof attribute.source_item_index === 'number' ? attribute.source_item_index + 1 : 0;
                    var sourceKey = sourceItem > 0 ? String(sourceItem) : 'outro';
                    if (!inheritedByItem[sourceKey]) { inheritedByItem[sourceKey] = []; }
                    inheritedByItem[sourceKey].push(label);
                } else {
                    if (attribute.origin) { details.push(enrichmentOriginLabel(attribute.origin)); }
                    if (typeof attribute.confidence === 'number' && attribute.confidence < 0.995) {
                        details.push('confiança: ' + Math.round(attribute.confidence * 100) + '%');
                    }
                }
                if (attribute.requires_confirmation) { details.push('requer confirmação'); }
                var li = document.createElement('li');
                li.textContent = label + ': ' + String(attribute.value || '-') + (details.length ? ' (' + details.join('; ') + ')' : '');
                ul.appendChild(li);
            });
            Object.keys(inheritedByItem).forEach(function (sourceKey) {
                var inherited = document.createElement('li');
                var sourceLabel = sourceKey === 'outro' ? 'outro item' : 'item ' + sourceKey;
                inherited.textContent = 'Herdado do ' + sourceLabel + ': ' + inheritedByItem[sourceKey].join(', ') + '.';
                ul.appendChild(inherited);
            });
            modalEnrichmentAttributes.appendChild(heading);
            modalEnrichmentAttributes.appendChild(ul);
        }
        renderEnrichmentList(modalEnrichmentPending, 'Pendências', enrichment.pending_confirmations);
        renderEnrichmentList(modalEnrichmentContradictions, 'Divergências', enrichment.contradictions);
        renderEnrichmentList(modalEnrichmentEvidence, 'Evidências', enrichment.evidence, enrichmentEvidenceLabel);
        var error = String(enrichment.enrichment_error || '').trim();
        modalEnrichmentError.textContent = error ? 'Base não concluiu a identificação. Revise manualmente.' : '';
        modalEnrichmentError.style.display = error ? 'block' : 'none';
    }

    function currentLinha() {
        if (!currentRow) { return 0; }
        var rowMeta = currentRow.querySelector('.js-row-meta');
        return rowMeta ? parseInt(rowMeta.getAttribute('data-linha') || '0', 10) : 0;
    }

    function setModalBusy(isBusy) {
        var buttons = [
            modalSalvarLinhaBtn,
            modalDesfazerLinhaBtn,
            modalGerarCandidatosItemBtn,
            modalSugerirIaItemBtn,
            modalUsarIaBtn,
            modalBuscarBtn
        ];
        buttons.forEach(function (btn) {
            if (btn) { btn.disabled = !!isBusy; }
        });
    }

    function updateIaStateOnRow(row, itemSug) {
        if (!row) { return; }
        var rowMeta = qs(row, '.js-row-meta');
        var confCell = qs(row, '.js-confianca-cell');
        if (!rowMeta || !itemSug) { return; }

        var produto = itemSug.produto_sugerido || null;
        var confianca = typeof itemSug.percentual_confianca !== 'undefined' ? String(itemSug.percentual_confianca) : '';
        var motivo = itemSug.motivo || '';

        rowMeta.setAttribute('data-produto-sugerido', JSON.stringify(produto));
        rowMeta.setAttribute('data-confianca-ia', confianca);
        rowMeta.setAttribute('data-motivo-ia', motivo);
        row.setAttribute('data-confianca', confianca !== '' ? confianca : '');

        if (confCell) {
            confCell.textContent = (produto && produto.codigo && confianca !== '') ? (confianca + '%') : '';
        }
    }

    function updateRowSelectionFromIa(row, itemSug) {
        if (!row) { return; }
        var form = qs(row, '.js-selecao-form');
        if (!form || !itemSug) { return; }

        var origemInput = form.querySelector('input[name="origem"]');
        var origemAtual = origemInput ? String(origemInput.value || '').trim() : '';
        var produtoAtualId = rowSelectedProductId(row);
        var temSelecaoManual = produtoAtualId !== '' && produtoAtualId !== '0' && origemAtual !== 'ia';
        if (temSelecaoManual) {
            return;
        }

        var produto = itemSug.produto_sugerido || null;
        var confirmado = !!itemSug.pre_selecionar;

        if (origemInput) {
            origemInput.value = (produto && produto.codigo) ? 'ia' : '';
        }

        refreshRowSelectionUi(row, produto, confirmado);
        updateInsertButtonState();
        applyFilters();
    }

    function normalizeText(v) {
        return String(v || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/\s+/g, ' ')
            .trim();
    }

    function rowHasProduto(row) {
        var form = qs(row, '.js-selecao-form');
        var codigo = form ? (form.querySelector('input[name="produto_codigo"]').value || '') : '';
        var desc = form ? (form.querySelector('input[name="produto_descricao"]').value || '') : '';
        return (codigo.trim() !== '' || desc.trim() !== '');
    }

    function rowSelectedProductId(row) {
        var form = qs(row, '.js-selecao-form');
        if (!form) { return ''; }
        return String((form.querySelector('input[name="produto_id"]').value || '')).trim();
    }

    function refreshRowSelectionUi(row, produto, confirmado) {
        if (!row) { return; }
        var form = qs(row, '.js-selecao-form');
        var cell = qs(row, '.js-produto-selecionado-cell');
        var chk = qs(row, '.js-confirmar-item');
        var temProduto = !!(produto && (produto.codigo || produto.descricao || produto.D001_Id));

        if (cell) {
            cell.textContent = temProduto ? produtoLabel(produto) : 'Não vinculado';
        }

        row.setAttribute('data-tem-produto', temProduto ? '1' : '0');
        row.setAttribute('data-confirmado', confirmado ? '1' : '0');

        if (chk) {
            chk.checked = !!confirmado;
        }

        if (form) {
            var produtoIdInput = form.querySelector('input[name="produto_id"]');
            var produtoCodigoInput = form.querySelector('input[name="produto_codigo"]');
            var produtoDescricaoInput = form.querySelector('input[name="produto_descricao"]');
            var confirmadoInput = form.querySelector('input[name="confirmado"]');

            if (produtoIdInput) {
                produtoIdInput.value = temProduto && produto.D001_Id ? String(produto.D001_Id) : '0';
            }
            if (produtoCodigoInput) {
                produtoCodigoInput.value = temProduto && produto.codigo ? String(produto.codigo) : '';
            }
            if (produtoDescricaoInput) {
                produtoDescricaoInput.value = temProduto && produto.descricao ? String(produto.descricao) : '';
            }
            if (confirmadoInput) {
                confirmadoInput.value = confirmado ? '1' : '0';
            }
        }
    }

    function updateInsertButtonState() {
        var hasConfirmed = false;
        qsa(document, '.grid tbody tr').forEach(function (row) {
            var chk = qs(row, '.js-confirmar-item');
            if (chk && chk.checked && rowHasProduto(row)) {
                hasConfirmed = true;
            }
        });
        qsa(document, 'form[action*="revisar.php"] input[name="action"][value="inserir_orcamento"]').forEach(function (inp) {
            if (inp.form && inp.form.querySelector('button[type="submit"]')) {
                inp.form.querySelector('button[type="submit"]').disabled = !hasConfirmed;
            }
        });
    }

    function persistSelecao(row) {
        var form = qs(row, '.js-selecao-form');
        if (!form) { return Promise.resolve(false); }
        var fd = new FormData(form);
        fd.set('ajax', 'salvar_selecao_item');
        return fetch(window.location.href, {
            method: 'POST',
            credentials: 'same-origin',
            body: fd
        }).then(function (r) { return r.json(); })
          .then(function (data) { return !!(data && data.ok); })
          .catch(function () { return false; });
    }

    function applyFilters() {
        var fConfirmar = filterConfirmar ? filterConfirmar.value : 'todos';
        var fProduto = filterProduto ? filterProduto.value : 'todos';
        var fConfianca = filterConfianca ? filterConfianca.value : 'todos';
        var fStatus = filterStatus ? filterStatus.value : 'todos';
        var fAlertaBase = filterAlertaBase ? filterAlertaBase.value : 'todos';
        var fCodigo = normalizeText(filterCodigo ? filterCodigo.value : '');
        var fDesc = normalizeText(filterDescricao ? filterDescricao.value : '');

        qsa(document, '.grid tbody tr').forEach(function (row) {
            var chk = qs(row, '.js-confirmar-item');
            var confirmado = chk ? chk.checked : false;
            var temProduto = rowHasProduto(row);
            var status = row.getAttribute('data-status') || '';
            var baseAlertStatus = row.getAttribute('data-base-alert-status') || 'not_applied';
            var confRaw = row.getAttribute('data-confianca');
            var conf = confRaw === '' || confRaw === null ? null : parseFloat(confRaw);
            var txtCodigo = normalizeText((qs(row, 'td:nth-child(2) .view-mode') || {textContent:''}).textContent + ' ' + (qs(row, '.js-selecao-form input[name="produto_codigo"]') || {value:''}).value);
            var txtDesc = normalizeText((qs(row, 'td:nth-child(3) .view-mode') || {textContent:''}).textContent + ' ' + (qs(row, '.js-selecao-form input[name="produto_descricao"]') || {value:''}).value);

            var ok = true;
            if (fConfirmar === 'sim' && !confirmado) { ok = false; }
            if (fConfirmar === 'nao' && confirmado) { ok = false; }
            if (fProduto === 'com' && !temProduto) { ok = false; }
            if (fProduto === 'sem' && temProduto) { ok = false; }
            if (fStatus === 'lido' && status !== 'lido') { ok = false; }
            if (fStatus === 'incompleto_erro' && !(status === 'incompleto' || status === 'erro')) { ok = false; }
            if (fAlertaBase === 'com_alertas' && !(baseAlertStatus === 'attention' || baseAlertStatus === 'error')) { ok = false; }
            if (fAlertaBase === 'falha' && baseAlertStatus !== 'error') { ok = false; }
            if (fAlertaBase === 'sem_alertas' && baseAlertStatus !== 'ok') { ok = false; }
            if (fConfianca === 'sem' && conf !== null) { ok = false; }
            if (fConfianca === 'alta' && (conf === null || conf < 90)) { ok = false; }
            if (fConfianca === 'media' && (conf === null || conf < 70 || conf > 89)) { ok = false; }
            if (fConfianca === 'baixa' && (conf === null || conf >= 70)) { ok = false; }
            if (fCodigo !== '' && txtCodigo.indexOf(fCodigo) === -1) { ok = false; }
            if (fDesc !== '' && txtDesc.indexOf(fDesc) === -1) { ok = false; }

            row.style.display = ok ? '' : 'none';
        });
    }

    function closeModal() {
        modal.style.display = 'none';
        if (modalBuscaManual) {
            modalBuscaManual.style.display = 'none';
        }
        currentRow = null;
        currentIaProduto = null;
        currentCandidatosRequestId += 1;
        setModalStatus('', '');
        setModalBusy(false);
        if (modalHelpBox) {
            modalHelpBox.style.display = 'none';
        }
        if (modalCandidatosLocais) {
            modalCandidatosLocais.innerHTML = '';
        }
        modalResultadosBusca.innerHTML = '';
    }
    function openBuscaManualModal() {
        if (!modalBuscaManual) { return; }
        modalBuscaManual.style.display = 'flex';
        if (modalBusca) {
            window.setTimeout(function () { modalBusca.focus(); }, 10);
        }
    }
    function closeBuscaManualModal() {
        if (!modalBuscaManual) { return; }
        modalBuscaManual.style.display = 'none';
    }
    qsa(modal, '.js-modal-close').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });
    if (modalBuscaManual) {
        qsa(modalBuscaManual, '.js-busca-manual-close').forEach(function (el) {
            el.addEventListener('click', closeBuscaManualModal);
        });
    }
    qsa(document, '.js-open-help').forEach(function (el) {
        el.addEventListener('click', function () { modalAjuda.style.display = 'block'; });
    });
    qsa(modalAjuda, '.js-help-close').forEach(function (el) {
        el.addEventListener('click', function () { modalAjuda.style.display = 'none'; });
    });
    if (modalHelpToggle && modalHelpBox) {
        modalHelpToggle.addEventListener('click', function () {
            var aberto = modalHelpBox.style.display !== 'none';
            modalHelpBox.style.display = aberto ? 'none' : 'block';
            modalHelpToggle.textContent = aberto ? 'Como escolher o produto' : 'Ocultar ajuda';
        });
    }

    function selecionarProdutoNaLinha(row, produto, origem, autoConfirmar) {
        var form = qs(row, '.js-selecao-form');
        var chk = qs(row, '.js-confirmar-item');
        form.querySelector('input[name="produto_id"]').value = produto && produto.D001_Id ? String(produto.D001_Id) : '0';
        form.querySelector('input[name="produto_codigo"]').value = produto && produto.codigo ? String(produto.codigo) : '';
        form.querySelector('input[name="produto_descricao"]').value = produto && produto.descricao ? String(produto.descricao) : '';
        form.querySelector('input[name="origem"]').value = origem || 'manual';
        if (autoConfirmar) {
            chk.checked = true;
            form.querySelector('input[name="confirmado"]').value = '1';
        }
        refreshRowSelectionUi(row, produto, chk && chk.checked);
        return persistSelecao(row).then(function (okSave) {
            if (!okSave) {
                form.submit();
                return false;
            }
            refreshRowSelectionUi(row, produto, chk && chk.checked);
            updateInsertButtonState();
            applyFilters();
            return true;
        });
    }

    function renderProdutoList(target, produtos, row, origem, showDetails) {
        if (!produtos || !produtos.length) {
            target.innerHTML = '<div class="muted">Nenhuma opcao encontrada.</div>';
            return;
        }
        var selectedId = rowSelectedProductId(row);
        var orderedProdutos = produtos.slice();
        if (selectedId !== '') {
            orderedProdutos.sort(function (a, b) {
                var aId = String(a && a.D001_Id ? a.D001_Id : '');
                var bId = String(b && b.D001_Id ? b.D001_Id : '');
                var aSelected = aId === selectedId ? 1 : 0;
                var bSelected = bId === selectedId ? 1 : 0;
                return bSelected - aSelected;
            });
        }
        var html = orderedProdutos.map(function (p, idx) {
            var label = produtoLabel(p);
            var isSelected = selectedId !== '' && String(p && p.D001_Id ? p.D001_Id : '') === selectedId;
            var detailsHtml = '';
            if (showDetails) {
                var score = (p && typeof p.score_local !== 'undefined') ? String(p.score_local) : '-';
                var motivo = (p && p.motivo_score) ? String(p.motivo_score) : '-';
                var detalhe = (p && p.detalhe_score && typeof p.detalhe_score === 'object') ? p.detalhe_score : {};
                var obs = Array.isArray(detalhe.observacoes) ? detalhe.observacoes : [];
                function arrHtml(arr) {
                    if (!arr.length) { return '<span class="muted">-</span>'; }
                    return '<ul class="detail-list">' + arr.map(function (v) {
                        return '<li>' + String(v).replace(/</g, '&lt;') + '</li>';
                    }).join('') + '</ul>';
                }
                detailsHtml = ''
                    + '<button type="button" class="link-button js-toggle-details" data-idx="' + idx + '">Ver detalhes</button>'
                    + '<div class="candidate-details" data-idx="' + idx + '" style="display:none;">'
                    + '<div><strong>Compatibilidade local:</strong> ' + score.replace(/</g, '&lt;') + '</div>'
                    + '<div><strong>Motivo:</strong> ' + motivo.replace(/</g, '&lt;') + '</div>'
                    + '<div class="candidate-details__title">Por que apareceu</div>'
                    + '<div><strong>Observações:</strong>' + arrHtml(obs) + '</div>'
                    + '</div>';
            }
            return '<div class="pick-row' + (isSelected ? ' pick-row--selected' : '') + '"><span>' + label.replace(/</g, '&lt;') + '</span><div class="pick-row__actions"><button type="button" class="button button--sm' + (isSelected ? ' button--selected' : '') + ' js-pick" data-idx="' + idx + '">' + (isSelected ? 'Selecionado' : 'Selecionar') + '</button>' + detailsHtml + '</div></div>';
        }).join('');
        target.innerHTML = html;
        qsa(target, '.js-pick').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var p = orderedProdutos[parseInt(btn.getAttribute('data-idx'), 10)];
                setModalStatus('info', 'Salvando produto selecionado...');
                setModalBusy(true);
                selecionarProdutoNaLinha(row, p, origem, true).then(function () {
                    setModalBusy(false);
                    renderProdutoList(target, orderedProdutos, row, origem, showDetails);
                    if (origem === 'busca_manual_cache') {
                        closeBuscaManualModal();
                    }
                    setModalStatus('', '');
                });
            });
        });
        qsa(target, '.js-toggle-details').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var idx = btn.getAttribute('data-idx');
                var details = target.querySelector('.candidate-details[data-idx="' + idx + '"]');
                if (!details) { return; }
                var isOpen = details.style.display !== 'none';
                details.style.display = isOpen ? 'none' : 'block';
                btn.textContent = isOpen ? 'Ver detalhes' : 'Ocultar detalhes';
            });
        });
    }

    function carregarCandidatosLinha(row) {
        if (!row || !modalCandidatosLocais) { return; }
        var rowMeta = row.querySelector('.js-row-meta');
        var linha = rowMeta ? parseInt(rowMeta.getAttribute('data-linha') || '0', 10) : 0;
        currentCandidatosRequestId += 1;
        var requestId = currentCandidatosRequestId;
        modalCandidatosLocais.innerHTML = '<div class="muted">Carregando candidatos...</div>';
        if (!linha) {
            modalCandidatosLocais.innerHTML = '<div class="muted">Nenhum candidato local disponível.</div>';
            return;
        }
        var url = new URL(window.location.href);
        url.searchParams.set('ajax', 'carregar_candidatos');
        url.searchParams.set('linha', String(linha));
        fetch(url.toString(), { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (requestId !== currentCandidatosRequestId) { return; }
                var itens = data && data.ok && Array.isArray(data.itens) ? data.itens : [];
                renderProdutoList(modalCandidatosLocais, itens, row, 'candidato_local', true);
            })
            .catch(function () {
                if (requestId !== currentCandidatosRequestId) { return; }
                modalCandidatosLocais.innerHTML = '<div class="flash flash--error">Não foi possível carregar os candidatos locais.</div>';
            });
    }

    qsa(document, '.grid tbody tr').forEach(function (row) {
        var saveForm = row.querySelector('.js-save-form');
        var selecaoForm = row.querySelector('.js-selecao-form');
        var confirmarChk = row.querySelector('.js-confirmar-item');
        var revisarProdutoBtn = row.querySelector('.js-revisar-produto');
        var rowMeta = row.querySelector('.js-row-meta');
        var viewEls = qsa(row, '.view-mode');
        var editEls = qsa(row, '.edit-mode');

        var originals = editEls.map(function (el) { return el.value; });

        function setEditMode(isEdit) {
            viewEls.forEach(function (el) { el.style.display = isEdit ? 'none' : ''; });
            editEls.forEach(function (el) { el.style.display = isEdit ? '' : 'none'; });
        }

        confirmarChk.addEventListener('change', function () {
            selecaoForm.querySelector('input[name="confirmado"]').value = confirmarChk.checked ? '1' : '0';
            persistSelecao(row).then(function (okSave) {
                if (!okSave) {
                    selecaoForm.submit();
                    return;
                }
                row.setAttribute('data-confirmado', confirmarChk.checked ? '1' : '0');
                updateInsertButtonState();
                applyFilters();
            });
        });

        revisarProdutoBtn.addEventListener('click', function () {
            currentRow = row;
            var descOriginal = rowMeta.getAttribute('data-descricao-original') || '';
            var conf = rowMeta.getAttribute('data-confianca-ia') || '';
            var motivo = rowMeta.getAttribute('data-motivo-ia') || '';
            var prodSugerido = safeJsonParse(rowMeta.getAttribute('data-produto-sugerido'), null);
            var enrichmentConflict = safeJsonParse(rowMeta.getAttribute('data-enrichment-conflict'), null);
            var enrichment = safeJsonParse(rowMeta.getAttribute('data-enrichment'), null);
            var candidatosDesatualizados = rowMeta.getAttribute('data-candidatos-desatualizados') === '1';
            var enrichmentConflictSource = rowMeta.getAttribute('data-enrichment-conflict-source') || 'pdf';

            modalItemDesc.textContent = descOriginal;
            renderEnrichment(enrichment);
            if (modalCandidatosDesatualizados) {
                modalCandidatosDesatualizados.style.display = candidatosDesatualizados ? 'block' : 'none';
            }
            renderEnrichmentConflict(candidatosDesatualizados ? null : enrichmentConflict, enrichmentConflictSource);
            modalIaProd.textContent = produtoLabel(prodSugerido);
            modalIaConf.textContent = conf !== '' ? (conf + '%') : '-';
            modalIaMotivo.textContent = motivo || '-';
            currentIaProduto = prodSugerido;
            if (prodSugerido && prodSugerido.codigo) {
                modalIaEmpty.style.display = 'none';
                modalIaContent.style.display = 'block';
            } else {
                modalIaEmpty.style.display = 'block';
                modalIaContent.style.display = 'none';
            }
            modalEditCodigo.value = row.querySelector('input[name="codigo"]').value || '';
            modalEditDescricao.value = row.querySelector('input[name="descricao"]').value || '';
            modalEditQuantidade.value = row.querySelector('input[name="quantidade"]').value || '';
            modalResultadosBusca.innerHTML = '';
            modalBusca.value = '';
            setModalStatus('', '');
            setModalBusy(false);
            carregarCandidatosLinha(row);
            modal.style.display = 'block';
        });
    });

    if (modalBuscaManualOpenBtn) {
        modalBuscaManualOpenBtn.addEventListener('click', function () {
            if (!currentRow) { return; }
            modalResultadosBusca.innerHTML = '';
            if (modalBusca) {
                modalBusca.value = '';
            }
            openBuscaManualModal();
        });
    }

    if (modalGerarCandidatosItemBtn) {
        modalGerarCandidatosItemBtn.addEventListener('click', function () {
            var linha = currentLinha();
            if (!linha || !currentRow) { return; }

            setModalBusy(true);
            setModalStatus('info', 'Gerando candidatos deste item...');
            if (window.ImportadorOrcamentoPdfProcessing) {
                window.ImportadorOrcamentoPdfProcessing.show('Gerando candidatos deste item...');
            }

            var fd = new FormData();
            fd.set('ajax', 'gerar_candidatos_linha');
            fd.set('linha', String(linha));

            fetch(window.location.href, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            }).then(function (r) {
                return r.json();
            }).then(function (data) {
                if (!data || !data.ok) {
                    throw new Error((data && data.erro) ? data.erro : 'Falha ao gerar candidatos deste item.');
                }
                var itens = Array.isArray(data.itens) ? data.itens : [];
                renderProdutoList(modalCandidatosLocais, itens, currentRow, 'candidato_local', true);
                setModalStatus('success', (data.mensagem || 'Candidatos deste item gerados com sucesso.') + ' Atualizando a análise...');
                window.setTimeout(function () { window.location.reload(); }, 500);
            }).catch(function (err) {
                setModalStatus('error', err && err.message ? err.message : 'Não foi possível gerar os candidatos deste item.');
            }).finally(function () {
                setModalBusy(false);
                if (window.ImportadorOrcamentoPdfProcessing) {
                    window.ImportadorOrcamentoPdfProcessing.hide();
                }
            });
        });
    }

    if (modalSugerirIaItemBtn) {
        modalSugerirIaItemBtn.addEventListener('click', function () {
            var linha = currentLinha();
            if (!linha || !currentRow) { return; }

            setModalBusy(true);
            setModalStatus('info', 'Gerando sugestão da IA para este item...');
            if (window.ImportadorOrcamentoPdfProcessing) {
                window.ImportadorOrcamentoPdfProcessing.show('Gerando sugestão da IA para este item...');
            }

            var fd = new FormData();
            fd.set('ajax', 'sugerir_ia_linha');
            fd.set('linha', String(linha));

            fetch(window.location.href, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            }).then(function (r) {
                return r.json();
            }).then(function (data) {
                if (!data || !data.ok) {
                    throw new Error((data && data.erro) ? data.erro : 'Falha ao gerar sugestão da IA deste item.');
                }

                var itemSug = data.item || null;
                var prodSugerido = itemSug && itemSug.produto_sugerido ? itemSug.produto_sugerido : null;
                var conf = itemSug && typeof itemSug.percentual_confianca !== 'undefined' ? String(itemSug.percentual_confianca) : '';
                var motivo = itemSug && itemSug.motivo ? String(itemSug.motivo) : '';

                currentIaProduto = prodSugerido;
                modalIaProd.textContent = produtoLabel(prodSugerido);
                modalIaConf.textContent = conf !== '' ? (conf + '%') : '-';
                modalIaMotivo.textContent = motivo || '-';
                if (prodSugerido && prodSugerido.codigo) {
                    modalIaEmpty.style.display = 'none';
                    modalIaContent.style.display = 'block';
                } else {
                    modalIaEmpty.style.display = 'block';
                    modalIaContent.style.display = 'none';
                }

                updateIaStateOnRow(currentRow, itemSug || {});
                updateRowSelectionFromIa(currentRow, itemSug || {});
                setModalStatus('success', data.mensagem || 'Sugestão da IA gerada com sucesso.');
            }).catch(function (err) {
                setModalStatus('error', err && err.message ? err.message : 'Não foi possível gerar a sugestão da IA deste item.');
            }).finally(function () {
                setModalBusy(false);
                if (window.ImportadorOrcamentoPdfProcessing) {
                    window.ImportadorOrcamentoPdfProcessing.hide();
                }
            });
        });
    }

    modalSalvarLinhaBtn.addEventListener('click', function () {
        if (!currentRow) { return; }
        var saveForm = qs(currentRow, '.js-save-form');
        saveForm.querySelector('input[name="codigo"]').value = modalEditCodigo.value || '';
        saveForm.querySelector('input[name="descricao"]').value = modalEditDescricao.value || '';
        saveForm.querySelector('input[name="quantidade"]').value = modalEditQuantidade.value || '';
        saveForm.submit();
    });

    modalDesfazerLinhaBtn.addEventListener('click', function () {
        if (!currentRow) { return; }
        var form = qs(currentRow, 'form.inline-form input[name="action"][value="restaurar_linha"]');
        if (form && form.form) {
            form.form.submit();
        }
    });

    modalUsarIaBtn.addEventListener('click', function () {
        if (!currentRow || !currentIaProduto || !currentIaProduto.codigo) { return; }
        selecionarProdutoNaLinha(currentRow, currentIaProduto, 'ia', true);
    });

    modalBuscarBtn.addEventListener('click', function () {
        if (!currentRow) { return; }
        var q = (modalBusca.value || '').trim();
        if (!q) { return; }
        var url = new URL(window.location.href);
        url.searchParams.set('ajax', 'buscar_produtos');
        url.searchParams.set('q', q);
        fetch(url.toString(), { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var itens = data && data.ok && Array.isArray(data.itens) ? data.itens : [];
                renderProdutoList(modalResultadosBusca, itens, currentRow, 'busca_manual_cache', false);
            })
            .catch(function () {
                modalResultadosBusca.innerHTML = '<div class="flash flash--error">Não foi possível buscar produtos no cadastro.</div>';
            });
    });

    [filterConfirmar, filterProduto, filterConfianca, filterStatus, filterAlertaBase, filterCodigo, filterDescricao].forEach(function (el) {
        if (!el) { return; }
        el.addEventListener('change', applyFilters);
        el.addEventListener('input', applyFilters);
    });
    if (btnLimparFiltros) {
        btnLimparFiltros.addEventListener('click', function () {
            if (filterConfirmar) { filterConfirmar.value = 'todos'; }
            if (filterProduto) { filterProduto.value = 'todos'; }
            if (filterConfianca) { filterConfianca.value = 'todos'; }
            if (filterStatus) { filterStatus.value = 'todos'; }
            if (filterAlertaBase) { filterAlertaBase.value = 'todos'; }
            if (filterCodigo) { filterCodigo.value = ''; }
            if (filterDescricao) { filterDescricao.value = ''; }
            applyFilters();
        });
    }
    if (btnMarcarValidos) {
        btnMarcarValidos.addEventListener('click', function () {
            var promises = [];
            qsa(document, '.grid tbody tr').forEach(function (row) {
                if (row.style.display === 'none') { return; }
                if (!rowHasProduto(row)) { return; }
                var chk = qs(row, '.js-confirmar-item');
                var form = qs(row, '.js-selecao-form');
                if (!chk || !form || chk.checked) { return; }
                chk.checked = true;
                form.querySelector('input[name="confirmado"]').value = '1';
                promises.push(persistSelecao(row));
            });
            Promise.all(promises).then(function () {
                updateInsertButtonState();
                applyFilters();
            });
        });
    }
    if (btnDesmarcarTodos) {
        btnDesmarcarTodos.addEventListener('click', function () {
            var promises = [];
            qsa(document, '.grid tbody tr').forEach(function (row) {
                if (row.style.display === 'none') { return; }
                var chk = qs(row, '.js-confirmar-item');
                var form = qs(row, '.js-selecao-form');
                if (!chk || !form || !chk.checked) { return; }
                chk.checked = false;
                form.querySelector('input[name="confirmado"]').value = '0';
                promises.push(persistSelecao(row));
            });
            Promise.all(promises).then(function () {
                updateInsertButtonState();
                applyFilters();
            });
        });
    }

    function processarSugestoesIaAuto(form, btn) {
        var progressoEl = qs(document, '.js-loading-progresso');
        var originalText = btn.textContent;
        var first = true;
        var processingUi = window.ImportadorOrcamentoPdfProcessing || null;

        function setProgress(msg, isError) {
            if (!progressoEl) { return; }
            progressoEl.style.display = '';
            progressoEl.className = 'flash ' + (isError ? 'flash--error' : 'flash--info') + ' js-loading-progresso';
            progressoEl.textContent = msg;
            if (processingUi && typeof processingUi.update === 'function') {
                processingUi.update(msg);
            }
        }

        function nextStep() {
            var fd = new FormData();
            fd.set('ajax', 'processar_sugestoes_ia');
            if (first) {
                fd.set('init', '1');
                first = false;
            }
            return fetch(window.location.href, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            }).then(function (r) { return r.json(); })
              .then(function (data) {
                  if (!data || !data.ok) {
                      throw new Error((data && data.erro) ? data.erro : 'Falha ao processar sugestões da IA.');
                  }
                  var total = typeof data.total !== 'undefined' ? parseInt(data.total, 10) : 0;
                  var proc = typeof data.processados !== 'undefined' ? parseInt(data.processados, 10) : 0;
                  var linha = typeof data.linha_atual !== 'undefined' ? parseInt(data.linha_atual, 10) : 0;
                  var chamadas = typeof data.chamadas_ia !== 'undefined' ? parseInt(data.chamadas_ia, 10) : 0;
                  var falhas = typeof data.chamadas_com_erro !== 'undefined' ? parseInt(data.chamadas_com_erro, 10) : 0;
                  var comSugestao = typeof data.itens_com_sugestao !== 'undefined' ? parseInt(data.itens_com_sugestao, 10) : 0;
                  if (!data.done) {
                      var detalheLinha = linha > 0 ? (' Linha atual: ' + linha + '.') : '';
                      setProgress('Processando sugestões da IA... ' + proc + '/' + total + ' itens concluídos, ' + comSugestao + ' com sugestão, ' + chamadas + ' chamadas, ' + falhas + ' falhas.' + detalheLinha, false);
                      return nextStep();
                  }
                  if (falhas > 0) {
                      setProgress('Sugestões da IA concluídas com alertas. Itens concluídos: ' + proc + '/' + total + '. Sugestões encontradas: ' + comSugestao + '. Falhas de chamada: ' + falhas + '.', true);
                  } else {
                      setProgress('Sugestões da IA concluídas com sucesso. Itens concluídos: ' + proc + '/' + total + '. Sugestões encontradas: ' + comSugestao + '.', false);
                  }
                  setTimeout(function () { window.location.reload(); }, 1200);
                  return true;
              });
        }

        btn.disabled = true;
        btn.textContent = 'Sugerindo em etapas...';
        setProgress('Preparando sugestões da IA em etapas...', false);

        return nextStep().catch(function (err) {
            setProgress((err && err.message) ? err.message : 'Falha ao processar sugestões da IA.', true);
            if (processingUi && typeof processingUi.hide === 'function') {
                processingUi.hide();
            }
            btn.disabled = false;
            btn.textContent = originalText;
            return false;
        });
    }

    function processarInsercaoOrcamentoAuto(form, btn) {
        var progressoEl = qs(document, '.js-loading-progresso');
        var originalText = btn.textContent;
        var chunkSize = 20;
        var first = true;
        var processingUi = window.ImportadorOrcamentoPdfProcessing || null;

        function setProgress(msg, isError) {
            if (!progressoEl) { return; }
            progressoEl.style.display = '';
            progressoEl.className = 'flash ' + (isError ? 'flash--error' : 'flash--info') + ' js-loading-progresso';
            progressoEl.textContent = msg;
            if (processingUi && typeof processingUi.update === 'function') {
                processingUi.update(msg);
            }
        }

        function nextStep() {
            var fd = new FormData();
            fd.set('ajax', 'processar_insercao_orcamento');
            fd.set('chunk_size', String(chunkSize));
            if (first) {
                fd.set('init', '1');
                first = false;
            }
            return fetch(window.location.href, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            }).then(function (r) { return r.json(); })
              .then(function (data) {
                  if (!data || !data.ok) {
                      throw new Error((data && data.erro) ? data.erro : 'Falha ao processar inclusão.');
                  }
                  var total = typeof data.total !== 'undefined' ? parseInt(data.total, 10) : 0;
                  var proc = typeof data.processados !== 'undefined' ? parseInt(data.processados, 10) : 0;
                  var ins = typeof data.itens_inseridos !== 'undefined' ? parseInt(data.itens_inseridos, 10) : 0;
                  var fal = typeof data.falhas !== 'undefined' ? parseInt(data.falhas, 10) : 0;
                  if (!data.done) {
                      setProgress('Inserindo itens no orçamento... ' + proc + '/' + total + ' processados, ' + ins + ' inseridos, ' + fal + ' falhas.', false);
                      return nextStep();
                  }
                  if (fal > 0) {
                      setProgress('Inclusão finalizada com falhas. Processados: ' + proc + '/' + total + '. Inseridos: ' + ins + '. Falhas: ' + fal + '.', true);
                  } else {
                      setProgress('Inclusão concluída com sucesso. Processados: ' + proc + '/' + total + '. Inseridos: ' + ins + '.', false);
                  }
                  setTimeout(function () { window.location.reload(); }, 1200);
                  return true;
              });
        }

        btn.disabled = true;
        btn.textContent = 'Inserindo em etapas...';
        setProgress('Preparando inclusão em etapas...', false);

        return nextStep().catch(function (err) {
            setProgress((err && err.message) ? err.message : 'Falha ao inserir no orçamento.', true);
            if (processingUi && typeof processingUi.hide === 'function') {
                processingUi.hide();
            }
            btn.disabled = false;
            btn.textContent = originalText;
            return false;
        });
    }

    function processarEnriquecimentoAuto(form, btn, resume) {
        var progressoEl = qs(document, '.js-loading-progresso');
        var originalText = btn.textContent;
        var first = !resume;
        function progress(msg, error) {
            if (progressoEl) { progressoEl.style.display=''; progressoEl.className='flash '+(error?'flash--error':'flash--info')+' js-loading-progresso'; progressoEl.textContent=msg; }
            if (window.ImportadorOrcamentoPdfProcessing) { window.ImportadorOrcamentoPdfProcessing.update(msg); }
        }
        function next() {
            var fd = new FormData(form); fd.set('ajax','processar_enriquecimento'); if(first){fd.set('init','1');first=false;}
            return fetch(window.location.href,{method:'POST',credentials:'same-origin',body:fd}).then(function(r){return r.json();}).then(function(data){
                if(!data||data.ok!==true) throw new Error((data&&(data.message||data.erro))?(data.message||data.erro):'Falha ao aplicar Base de Conhecimento.');
                if(data.done===false){ progress('Aplicando Base de Conhecimento: '+data.processados+' de '+data.total+' itens processados. '+data.calls+' chamada(s), '+(data.duration_ms/1000).toFixed(1)+'s.',false); return next(); }
                if(data.done===true){ progress('Base de Conhecimento aplicada: '+data.processados+' itens processados em '+(data.duration_ms/1000).toFixed(1)+'s.',false); setTimeout(function(){window.location.reload();},800); return true; }
                throw new Error('Resposta inválida do processamento da Base de Conhecimento.');
            });
        }
        btn.disabled=true; btn.textContent='Aplicando Base de Conhecimento...'; progress('Aplicando Base de Conhecimento. Este processo pode levar alguns segundos.',false);
        return next().catch(function(err){progress(err&&err.message?err.message:'Falha ao aplicar Base de Conhecimento.',true);if(window.ImportadorOrcamentoPdfProcessing)window.ImportadorOrcamentoPdfProcessing.hide();btn.disabled=false;btn.textContent=originalText;return false;});
    }
    qsa(document, 'form.js-form-enriquecer').forEach(function (f) {
        var btn=f.querySelector('.js-btn-enriquecer'); if(!btn)return;
        if(f.getAttribute('data-processing-active')==='1'){ if(window.ImportadorOrcamentoPdfProcessing)window.ImportadorOrcamentoPdfProcessing.show('Retomando aplicação da Base de Conhecimento...'); processarEnriquecimentoAuto(f,btn,true); return; }
        f.addEventListener('submit',function(ev){ev.preventDefault();if(window.ImportadorOrcamentoPdfProcessing)window.ImportadorOrcamentoPdfProcessing.show('Aplicando Base de Conhecimento...');processarEnriquecimentoAuto(f,btn,false);});
    });


    qsa(document, 'form.js-form-sugerir-ia').forEach(function (f) {
        var btn = f.querySelector('.js-btn-sugerir-ia');
        if (!btn) { return; }
        f.addEventListener('submit', function (ev) {
            ev.preventDefault();
            if (window.ImportadorOrcamentoPdfProcessing) {
                window.ImportadorOrcamentoPdfProcessing.show('Processando sugestões com IA em etapas...');
            }
            processarSugestoesIaAuto(f, btn);
        });
    });

    qsa(document, 'form.js-form-gerar-candidatos-local').forEach(function (f) {
        var btn = f.querySelector('.js-btn-gerar-candidatos-local');
        var progressoEl = qs(document, '.js-loading-progresso');
        if (!btn) { return; }
        f.addEventListener('submit', function () {
            btn.disabled = true;
            btn.textContent = 'Gerando candidatos...';
            if (progressoEl) {
                progressoEl.style.display = '';
                progressoEl.className = 'flash flash--info js-loading-progresso';
                progressoEl.textContent = 'Gerando candidatos locais. Aguarde...';
            }
        });
    });

    qsa(document, 'form.js-form-gerar-cache-produtos').forEach(function (f) {
        var btn = f.querySelector('.js-btn-gerar-cache-produtos');
        var progressoEl = qs(document, '.js-loading-progresso');
        if (!btn) { return; }
        f.addEventListener('submit', function () {
            btn.disabled = true;
            btn.textContent = 'Gerando cache...';
            if (progressoEl) {
                progressoEl.style.display = '';
                progressoEl.className = 'flash flash--info js-loading-progresso';
                progressoEl.textContent = 'Gerando cache de produtos. Aguarde...';
            }
        });
    });

    qsa(document, 'form').forEach(function (f) {
        var actionInput = f.querySelector('input[name="action"][value="inserir_orcamento"]');
        var btn = f.querySelector('button[type="submit"]');
        if (!actionInput || !btn) { return; }
        f.addEventListener('submit', function (ev) {
            ev.preventDefault();
            processarInsercaoOrcamentoAuto(f, btn);
        });
    });
    updateInsertButtonState();
    applyFilters();
}());
</script>
<?php importador_orcamento_pdf_footer(); ?>
