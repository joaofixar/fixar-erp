<?php
namespace hardness;

if (PHP_SAPI !== 'cli' && session_id() === '') {
    session_start();
}

define('IMPORTADOR_ORCAMENTO_PDF_SESSION', 'importador_orcamento_pdf');
define('IMPORTADOR_ORCAMENTO_PDF_ROOT', dirname(dirname(dirname(__DIR__))));
define('IMPORTADOR_ORCAMENTO_PDF_MODULE_DIR', dirname(__DIR__));

function importador_orcamento_pdf_route_base()
{
    return '/importadores/importador_orcamento_pdf';
}

function importador_orcamento_pdf_base_path($path = '')
{
    $base = importador_orcamento_pdf_url_base() . importador_orcamento_pdf_route_base();
    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function importador_orcamento_pdf_url_base()
{
    global $g;

    $urlRaiz = importador_orcamento_pdf_url_raiz();
    $pathWeb = isset($g['pathWeb']) && $g['pathWeb'] !== '' ? $g['pathWeb'] : importador_orcamento_pdf_detect_path_web();

    return rtrim($urlRaiz, '/') . ($pathWeb !== '' ? '/' . trim($pathWeb, '/') : '');
}

function importador_orcamento_pdf_detect_path_web()
{
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $modulePath = importador_orcamento_pdf_route_base();
    $pos = strpos($scriptName, $modulePath);

    if ($pos === false) {
        return '';
    }

    return substr($scriptName, 0, $pos);
}

function importador_orcamento_pdf_url_raiz()
{
    global $g, $confUsuario;

    if (isset($g['urlRaiz']) && $g['urlRaiz'] !== '') {
        return rtrim($g['urlRaiz'], '/');
    }

    if (isset($confUsuario['urlRaiz']) && $confUsuario['urlRaiz'] !== '') {
        return rtrim($confUsuario['urlRaiz'], '/');
    }

    return '';
}

function importador_orcamento_pdf_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function importador_orcamento_pdf_fs_root_dir()
{
    importador_orcamento_pdf_bootstrap();

    global $g;
    return isset($g['pathRaiz']) ? rtrim($g['pathRaiz'], '/') : '';
}

function importador_orcamento_pdf_fs_data_dir()
{
    importador_orcamento_pdf_bootstrap();

    global $g;
    $root = importador_orcamento_pdf_fs_root_dir();
    $data = isset($g['pathDados']) ? trim($g['pathDados'], '/') : '';

    return $root . ($data !== '' ? '/' . $data : '');
}

function importador_orcamento_pdf_fs_module_dir()
{
    $dataDir = importador_orcamento_pdf_fs_data_dir();
    $newDir = $dataDir . '/importadorOrcamentoPdf';
    $legacyDir = $dataDir . '/importador_orcamento_pdf';

    if (is_dir($newDir)) {
        return $newDir;
    }

    // Prioriza o novo padrao. Se o ambiente nao permitir criar, usa legado para nao quebrar upload.
    if (@mkdir($newDir, 0770, true) || is_dir($newDir)) {
        return $newDir;
    }

    return $legacyDir;
}

function importador_orcamento_pdf_catalog_id()
{
    importador_orcamento_pdf_bootstrap();

    global $g, $confUsuario;

    if (isset($g['dbDatabase']) && trim((string) $g['dbDatabase']) !== '') {
        return preg_replace('/[^a-zA-Z0-9_]/', '', (string) $g['dbDatabase']);
    }

    if (isset($confUsuario['dbDatabase']) && trim((string) $confUsuario['dbDatabase']) !== '') {
        return preg_replace('/[^a-zA-Z0-9_]/', '', (string) $confUsuario['dbDatabase']);
    }

    $pathDados = isset($g['pathDados']) ? trim((string) $g['pathDados'], '/') : '';
    if ($pathDados !== '') {
        $parts = explode('/', $pathDados);
        $last = end($parts);
        if (is_string($last) && trim($last) !== '') {
            return preg_replace('/[^a-zA-Z0-9_]/', '', $last);
        }
    }

    return '';
}

function importador_orcamento_pdf_fs_cliente_root_dir()
{
    return importador_orcamento_pdf_fs_module_dir();
}

function importador_orcamento_pdf_fs_config_dir()
{
    return importador_orcamento_pdf_fs_cliente_root_dir() . '/config';
}

function importador_orcamento_pdf_fs_config_global_dir()
{
    return importador_orcamento_pdf_fs_config_dir() . '/global';
}

function importador_orcamento_pdf_fs_base_conhecimento_dir()
{
    return importador_orcamento_pdf_fs_config_dir() . '/base_conhecimento';
}

function importador_orcamento_pdf_pode_gerenciar_configuracao_catalogo()
{
    importador_orcamento_pdf_bootstrap();

    global $g;
    $usuarioAlcada = isset($g['usuarioAlcada']) ? strtoupper(trim((string) $g['usuarioAlcada'])) : '';
    $nivelAlcadaTela = isset($g['nivelAlcada']) ? (int) $g['nivelAlcada'] : 0;
    $nivelAlcadaSistema = isset($g['nivelAlcadaUsuarioSistema']) ? (int) $g['nivelAlcadaUsuarioSistema'] : 0;

    return in_array($usuarioAlcada, array('D', 'S'), true)
        || $nivelAlcadaTela >= 2
        || $nivelAlcadaSistema >= 2;
}

function importador_orcamento_pdf_csrf_token($scope)
{
    importador_orcamento_pdf_bootstrap();
    $scope = preg_replace('/[^a-zA-Z0-9_.-]/', '_', (string) $scope);
    if (!isset($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['csrf']) || !is_array($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['csrf'])) {
        $_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['csrf'] = array();
    }
    if (empty($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['csrf'][$scope])) {
        try {
            $_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['csrf'][$scope] = bin2hex(random_bytes(32));
        } catch (\Exception $e) {
            return '';
        }
    }
    return (string) $_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION]['csrf'][$scope];
}

function importador_orcamento_pdf_csrf_valido($scope, $token)
{
    $expected = importador_orcamento_pdf_csrf_token($scope);
    return $expected !== '' && is_string($token) && $token !== '' && hash_equals($expected, $token);
}

function importador_orcamento_pdf_fs_config_cliente_dir($D024_Id)
{
    $D024_Id = (int) $D024_Id;
    return importador_orcamento_pdf_fs_config_dir() . '/clientes/D024_' . $D024_Id;
}

function importador_orcamento_pdf_busca_global_paths()
{
    $dir = importador_orcamento_pdf_fs_config_global_dir();
    return array(
        'dir' => $dir,
        'source' => $dir . '/busca_source.json',
        'backup' => $dir . '/busca_source.backup.json',
        'compiled' => $dir . '/busca_compiled.json',
        'index' => $dir . '/busca_index.json',
    );
}

function importador_orcamento_pdf_busca_source_default_payload()
{
    return array(
        'version' => 1,
        'catalog_id' => importador_orcamento_pdf_catalog_id(),
        'updated_at' => date('c'),
        'informacoes_busca' => array(),
    );
}

function importador_orcamento_pdf_busca_source_validate(array $payload, &$error = '')
{
    $error = '';

    if (!isset($payload['version'])) {
        $error = 'Campo version ausente em busca_source.json.';
        return false;
    }

    if (!isset($payload['informacoes_busca']) || !is_array($payload['informacoes_busca'])) {
        $error = 'Campo informacoes_busca inválido em busca_source.json.';
        return false;
    }

    $nomes = array();
    foreach ($payload['informacoes_busca'] as $infoIndex => $info) {
        $line = $infoIndex + 1;
        if (!is_array($info)) {
            $error = 'Informação de busca #' . $line . ' inválida.';
            return false;
        }

        $nome = isset($info['nome']) ? trim((string) $info['nome']) : '';
        $prioridade = isset($info['prioridade']) ? (string) $info['prioridade'] : '';
        $valores = isset($info['valores']) ? $info['valores'] : array();

        if ($nome === '') {
            $error = 'Campo nome obrigatório na informação de busca #' . $line . '.';
            return false;
        }
        if ($prioridade === '' || !is_numeric($prioridade)) {
            $error = 'Campo prioridade inválido na informação de busca "' . $nome . '".';
            return false;
        }
        if (isset($nomes[$nome])) {
            $error = 'Nome duplicado em informacoes_busca: "' . $nome . '".';
            return false;
        }
        $nomes[$nome] = true;

        if (!is_array($valores)) {
            $error = 'Campo valores inválido na informação de busca "' . $nome . '".';
            return false;
        }

        foreach ($valores as $valorIndex => $valor) {
            $valorLine = $valorIndex + 1;
            if (!is_array($valor)) {
                $error = 'Valor canônico #' . $valorLine . ' inválido na informação de busca "' . $nome . '".';
                return false;
            }

            $canonico = isset($valor['canonico']) ? trim((string) $valor['canonico']) : '';
            $aliases = isset($valor['aliases_pdf']) ? $valor['aliases_pdf'] : array();

            if ($canonico === '') {
                $error = 'Campo canonico obrigatório na informação de busca "' . $nome . '".';
                return false;
            }
            if (!is_array($aliases)) {
                $error = 'Campo aliases_pdf inválido na informação de busca "' . $nome . '" para o valor "' . $canonico . '".';
                return false;
            }
        }
    }

    return true;
}

function importador_orcamento_pdf_busca_source_save(array $payload, &$error = '')
{
    $error = '';
    $paths = importador_orcamento_pdf_busca_global_paths();

    if (!importador_orcamento_pdf_ensure_directory($paths['dir'])) {
        $error = 'Não foi possível criar o diretório global de configuração de busca.';
        return false;
    }

    $payload['version'] = isset($payload['version']) ? $payload['version'] : 1;
    $payload['catalog_id'] = isset($payload['catalog_id']) && trim((string) $payload['catalog_id']) !== ''
        ? trim((string) $payload['catalog_id'])
        : importador_orcamento_pdf_catalog_id();
    $payload['updated_at'] = date('c');
    if (!isset($payload['informacoes_busca']) || !is_array($payload['informacoes_busca'])) {
        $payload['informacoes_busca'] = array();
    }

    if (!importador_orcamento_pdf_busca_source_validate($payload, $error)) {
        return false;
    }

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if (!is_string($json) || $json === '') {
        $error = 'Não foi possível serializar busca_source.json.';
        return false;
    }

    if (is_file($paths['source'])) {
        $currentRaw = @file_get_contents($paths['source']);
        if (!is_string($currentRaw) || trim($currentRaw) === '') {
            $error = 'Não foi possível ler busca_source.json atual para gerar o backup.';
            return false;
        }

        if (@file_put_contents($paths['backup'], $currentRaw) === false) {
            $error = 'Não foi possível gravar busca_source.backup.json antes de salvar a nova configuração.';
            return false;
        }
    }

    if (@file_put_contents($paths['source'], $json) === false) {
        $error = 'Não foi possível gravar busca_source.json.';
        return false;
    }

    return true;
}

function importador_orcamento_pdf_busca_source_load(&$error = '')
{
    $error = '';
    $paths = importador_orcamento_pdf_busca_global_paths();

    if (!importador_orcamento_pdf_ensure_directory($paths['dir'])) {
        $error = 'Não foi possível criar o diretório global de configuração de busca.';
        return array();
    }

    if (!is_file($paths['source'])) {
        $payload = importador_orcamento_pdf_busca_source_default_payload();
        if (!importador_orcamento_pdf_busca_source_save($payload, $error)) {
            return array();
        }
        return $payload;
    }

    $raw = @file_get_contents($paths['source']);
    if (!is_string($raw) || trim($raw) === '') {
        $error = 'Arquivo busca_source.json vazio ou ilegível.';
        return array();
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        $error = 'Arquivo busca_source.json inválido.';
        return array();
    }

    if (!importador_orcamento_pdf_busca_source_validate($payload, $error)) {
        return array();
    }

    return $payload;
}

function importador_orcamento_pdf_busca_source_file_hash(&$error = '')
{
    $error = '';
    $paths = importador_orcamento_pdf_busca_global_paths();
    if (!is_file($paths['source'])) {
        $error = 'Arquivo busca_source.json não encontrado.';
        return '';
    }
    $raw = @file_get_contents($paths['source']);
    if (!is_string($raw) || trim($raw) === '') {
        $error = 'Não foi possível ler busca_source.json para calcular o hash.';
        return '';
    }
    return hash('sha256', $raw);
}

function importador_orcamento_pdf_busca_normalize_alias($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    if (function_exists('mb_check_encoding') && function_exists('mb_convert_encoding') && !mb_check_encoding($value, 'UTF-8')) {
        $converted = @mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252, ASCII');
        if (is_string($converted) && $converted !== '') {
            $value = $converted;
        }
    }

    if (function_exists('mb_strtolower')) {
        $value = mb_strtolower($value, 'UTF-8');
    } else {
        $value = strtolower($value);
    }

    $value = strtr($value, array(
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c',
        'ª' => ' ', 'º' => ' ', '°' => ' ',
    ));

    $ascii = function_exists('iconv') ? @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) : false;
    if (is_string($ascii) && $ascii !== '') {
        $value = $ascii;
    }

    $value = str_replace('?', ' ', $value);
    $value = preg_replace('/\s+/u', ' ', $value);
    return trim((string) $value);
}

function importador_orcamento_pdf_busca_compiled_load(&$error = '')
{
    $error = '';
    $paths = importador_orcamento_pdf_busca_global_paths();
    if (!is_file($paths['compiled'])) {
        $error = 'Arquivo busca_compiled.json não encontrado.';
        return array();
    }

    $raw = @file_get_contents($paths['compiled']);
    if (!is_string($raw) || trim($raw) === '') {
        $error = 'Arquivo busca_compiled.json vazio ou ilegível.';
        return array();
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        $error = 'Arquivo busca_compiled.json inválido.';
        return array();
    }

    if (!isset($payload['alias_to_canonico']) || !is_array($payload['alias_to_canonico'])) {
        $error = 'Campo alias_to_canonico inválido em busca_compiled.json.';
        return array();
    }

    return $payload;
}

function importador_orcamento_pdf_busca_compiled_file_hash(&$error = '')
{
    $error = '';
    $paths = importador_orcamento_pdf_busca_global_paths();
    if (!is_file($paths['compiled'])) {
        $error = 'Arquivo busca_compiled.json não encontrado.';
        return '';
    }
    $raw = @file_get_contents($paths['compiled']);
    if (!is_string($raw) || trim($raw) === '') {
        $error = 'Não foi possível ler busca_compiled.json para calcular o hash.';
        return '';
    }
    return hash('sha256', $raw);
}

function importador_orcamento_pdf_busca_compiled_payload_from_source(array $sourcePayload, $sourceHash, &$error = '')
{
    $error = '';

    if (!importador_orcamento_pdf_busca_source_validate($sourcePayload, $error)) {
        return array();
    }

    $compiled = array(
        'format_version' => 2,
        'version' => isset($sourcePayload['version']) ? (int) $sourcePayload['version'] : 1,
        'catalog_id' => isset($sourcePayload['catalog_id']) ? (string) $sourcePayload['catalog_id'] : importador_orcamento_pdf_catalog_id(),
        'compiled_at' => date('c'),
        'source_hash' => (string) $sourceHash,
        'alias_to_canonico' => array(),
        'alias_to_informacoes' => array(),
    );

    foreach ($sourcePayload['informacoes_busca'] as $infoIndex => $info) {
        if (empty($info['ativo'])) {
            continue;
        }

        $infoNome = isset($info['nome']) ? trim((string) $info['nome']) : ('#' . ($infoIndex + 1));
        $valores = isset($info['valores']) && is_array($info['valores']) ? $info['valores'] : array();

        foreach ($valores as $valorIndex => $valor) {
            $canonico = isset($valor['canonico']) ? trim((string) $valor['canonico']) : '';
            $aliases = isset($valor['aliases_pdf']) && is_array($valor['aliases_pdf']) ? $valor['aliases_pdf'] : array();

            foreach ($aliases as $alias) {
                $aliasOriginal = trim((string) $alias);
                if ($aliasOriginal === '') {
                    continue;
                }

                $aliasNormalizado = importador_orcamento_pdf_busca_normalize_alias($aliasOriginal);
                if ($aliasNormalizado === '') {
                    continue;
                }

                if (isset($compiled['alias_to_canonico'][$aliasNormalizado]) && $compiled['alias_to_canonico'][$aliasNormalizado] !== $canonico) {
                    $error = 'Conflito de alias na informação "' . $infoNome . '" para "' . $aliasOriginal . '" (normalizado como "' . $aliasNormalizado . '"): "' . $compiled['alias_to_canonico'][$aliasNormalizado] . '" vs "' . $canonico . '".';
                    return array();
                }

                $compiled['alias_to_canonico'][$aliasNormalizado] = $canonico;
                if (!isset($compiled['alias_to_informacoes'][$aliasNormalizado])) {
                    $compiled['alias_to_informacoes'][$aliasNormalizado] = array();
                }
                $compiled['alias_to_informacoes'][$aliasNormalizado][$infoNome] = true;
            }
        }
    }

    ksort($compiled['alias_to_canonico']);
    foreach ($compiled['alias_to_informacoes'] as $aliasNormalizado => $informacoes) {
        $compiled['alias_to_informacoes'][$aliasNormalizado] = array_values(array_keys($informacoes));
    }
    ksort($compiled['alias_to_informacoes']);
    return $compiled;
}

function importador_orcamento_pdf_busca_compile(&$error = '')
{
    $error = '';
    $paths = importador_orcamento_pdf_busca_global_paths();

    if (!importador_orcamento_pdf_ensure_directory($paths['dir'])) {
        $error = 'Não foi possível criar o diretório global de configuração de busca.';
        return false;
    }

    $sourcePayload = importador_orcamento_pdf_busca_source_load($error);
    if (!is_array($sourcePayload) || !empty($error)) {
        return false;
    }

    $rawSource = @file_get_contents($paths['source']);
    if (!is_string($rawSource) || trim($rawSource) === '') {
        $error = 'Não foi possível ler busca_source.json para gerar o source_hash.';
        return false;
    }

    $compiledPayload = importador_orcamento_pdf_busca_compiled_payload_from_source($sourcePayload, hash('sha256', $rawSource), $error);
    if (!is_array($compiledPayload) || !empty($error)) {
        return false;
    }

    $json = json_encode($compiledPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if (!is_string($json) || $json === '') {
        $error = 'Não foi possível serializar busca_compiled.json.';
        return false;
    }

    if (@file_put_contents($paths['compiled'], $json) === false) {
        $error = 'Não foi possível gravar busca_compiled.json.';
        return false;
    }

    return true;
}

function importador_orcamento_pdf_busca_compiled_is_fresh(&$error = '')
{
    $error = '';
    $sourceHash = importador_orcamento_pdf_busca_source_file_hash($error);
    if ($sourceHash === '') {
        return false;
    }

    $compiled = importador_orcamento_pdf_busca_compiled_load($error);
    if (empty($compiled)) {
        return false;
    }

    return isset($compiled['format_version']) && (int) $compiled['format_version'] >= 2
        && isset($compiled['alias_to_informacoes']) && is_array($compiled['alias_to_informacoes'])
        && isset($compiled['source_hash']) && (string) $compiled['source_hash'] === $sourceHash;
}

function importador_orcamento_pdf_busca_collect_active_canonicos(array $sourcePayload)
{
    $canonicos = array();
    $seen = array();

    $infos = isset($sourcePayload['informacoes_busca']) && is_array($sourcePayload['informacoes_busca'])
        ? $sourcePayload['informacoes_busca']
        : array();

    foreach ($infos as $info) {
        if (empty($info['ativo'])) {
            continue;
        }

        $valores = isset($info['valores']) && is_array($info['valores']) ? $info['valores'] : array();
        foreach ($valores as $valor) {
            $canonico = isset($valor['canonico']) ? trim((string) $valor['canonico']) : '';
            if ($canonico === '' || isset($seen[$canonico])) {
                continue;
            }

            $normalizado = importador_orcamento_pdf_busca_normalize_alias($canonico);
            if ($normalizado === '') {
                continue;
            }

            $seen[$canonico] = true;
            $canonicos[] = array(
                'canonico' => $canonico,
                'normalizado' => $normalizado,
            );
        }
    }

    usort($canonicos, function ($a, $b) {
        return strlen((string) $b['normalizado']) - strlen((string) $a['normalizado']);
    });

    return $canonicos;
}

function importador_orcamento_pdf_global_produtos_cache_paths()
{
    $cacheDir = importador_orcamento_pdf_fs_cliente_root_dir() . '/cache';
    return array(
        'dir' => $cacheDir,
        'final' => $cacheDir . '/produtos_cache.json',
        'index' => $cacheDir . '/produtos_index.json',
        'lock' => $cacheDir . '/produtos_cache.lock',
    );
}

function importador_orcamento_pdf_load_produtos_cache_meta_shared($cachePath)
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

function importador_orcamento_pdf_iterar_produtos_cache_shared($cachePath, $callback)
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

function importador_orcamento_pdf_collect_products_by_ids_shared($cachePath, array $targetIds)
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

    importador_orcamento_pdf_iterar_produtos_cache_shared($cachePath, function ($produto) use (&$out, $targetMap) {
        $id = isset($produto['D001_Id']) ? (int) $produto['D001_Id'] : 0;
        if ($id <= 0) {
            return true;
        }

        $key = (string) $id;
        if (!isset($targetMap[$key])) {
            return true;
        }

        $out[$key] = $produto;
        return true;
    });

    return $out;
}

function importador_orcamento_pdf_busca_index_build_payload(array $sourcePayload, array $compiledPayload, $sourceHash, $compiledHash, $cachePath, &$error = '')
{
    $error = '';

    if (!importador_orcamento_pdf_busca_source_validate($sourcePayload, $error)) {
        return array();
    }
    if (!isset($compiledPayload['alias_to_canonico']) || !is_array($compiledPayload['alias_to_canonico'])) {
        $error = 'busca_compiled.json inválido para geração do índice.';
        return array();
    }
    if (!is_file($cachePath)) {
        $error = 'Arquivo produtos_cache.json não encontrado.';
        return array();
    }

    $canonicos = importador_orcamento_pdf_busca_collect_active_canonicos($sourcePayload);
    $payload = array(
        'version' => isset($sourcePayload['version']) ? (int) $sourcePayload['version'] : 1,
        'catalog_id' => isset($sourcePayload['catalog_id']) ? (string) $sourcePayload['catalog_id'] : importador_orcamento_pdf_catalog_id(),
        'generated_at' => date('c'),
        'source_hash' => (string) $sourceHash,
        'compiled_hash' => (string) $compiledHash,
        'total_produtos' => 0,
        'total_valores' => count($canonicos),
        'total_vinculos' => 0,
        'valores' => array(),
    );

    foreach ($canonicos as $item) {
        $payload['valores'][$item['canonico']] = array();
    }

    $ok = importador_orcamento_pdf_iterar_produtos_cache_shared($cachePath, function ($produto) use (&$payload, $canonicos) {
        $id = isset($produto['D001_Id']) ? (int) $produto['D001_Id'] : 0;
        if ($id <= 0) {
            return true;
        }

        $payload['total_produtos']++;
        $descricao = isset($produto['descricao']) ? (string) $produto['descricao'] : '';
        $descricaoNormalizada = importador_orcamento_pdf_busca_normalize_alias($descricao);
        if ($descricaoNormalizada === '') {
            return true;
        }

        foreach ($canonicos as $canonico) {
            if (strpos($descricaoNormalizada, $canonico['normalizado']) !== false) {
                $payload['valores'][$canonico['canonico']][] = $id;
                $payload['total_vinculos']++;
            }
        }

        return true;
    });

    if (!$ok) {
        $error = 'Não foi possível iterar produtos_cache.json.';
        return array();
    }

    foreach ($payload['valores'] as $canonico => $ids) {
        if (empty($ids)) {
            $payload['valores'][$canonico] = array();
            continue;
        }
        $payload['valores'][$canonico] = array_values(array_map('intval', array_keys(array_fill_keys(array_map('intval', $ids), true))));
    }

    ksort($payload['valores']);
    return $payload;
}

function importador_orcamento_pdf_busca_index_generate(&$error = '')
{
    $error = '';
    $paths = importador_orcamento_pdf_busca_global_paths();
    $cachePaths = importador_orcamento_pdf_global_produtos_cache_paths();

    if (!importador_orcamento_pdf_ensure_directory($paths['dir'])) {
        $error = 'Não foi possível criar o diretório global de configuração de busca.';
        return array();
    }
    if (!is_file($cachePaths['final'])) {
        $error = 'Arquivo produtos_cache.json não encontrado. Gere o cache de produtos do importador antes de criar o índice do catálogo.';
        return array();
    }

    $compiledFreshError = '';
    $compiledFresh = importador_orcamento_pdf_busca_compiled_is_fresh($compiledFreshError);
    if (!$compiledFresh) {
        if (!importador_orcamento_pdf_busca_compile($error)) {
            if ($error === '' && $compiledFreshError !== '') {
                $error = $compiledFreshError;
            }
            return array();
        }
    }

    $sourcePayload = importador_orcamento_pdf_busca_source_load($error);
    if (empty($sourcePayload)) {
        return array();
    }
    $compiledPayload = importador_orcamento_pdf_busca_compiled_load($error);
    if (empty($compiledPayload)) {
        return array();
    }

    $sourceHash = importador_orcamento_pdf_busca_source_file_hash($error);
    if ($sourceHash === '') {
        return array();
    }
    $compiledHash = importador_orcamento_pdf_busca_compiled_file_hash($error);
    if ($compiledHash === '') {
        return array();
    }

    $payload = importador_orcamento_pdf_busca_index_build_payload($sourcePayload, $compiledPayload, $sourceHash, $compiledHash, $cachePaths['final'], $error);
    if (empty($payload) && $error !== '') {
        return array();
    }

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if (!is_string($json) || $json === '') {
        $error = 'Não foi possível serializar busca_index.json.';
        return array();
    }

    if (@file_put_contents($paths['index'], $json) === false) {
        $error = 'Não foi possível gravar busca_index.json.';
        return array();
    }

    return $payload;
}

function importador_orcamento_pdf_busca_index_load(&$error = '')
{
    $error = '';
    $paths = importador_orcamento_pdf_busca_global_paths();
    if (!is_file($paths['index'])) {
        $error = 'Arquivo busca_index.json não encontrado.';
        return array();
    }

    $raw = @file_get_contents($paths['index']);
    if (!is_string($raw) || trim($raw) === '') {
        $error = 'Arquivo busca_index.json vazio ou ilegível.';
        return array();
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        $error = 'Arquivo busca_index.json inválido.';
        return array();
    }

    if (!isset($payload['valores']) || !is_array($payload['valores'])) {
        $error = 'Campo valores inválido em busca_index.json.';
        return array();
    }

    return $payload;
}

function importador_orcamento_pdf_busca_index_is_fresh(&$error = '')
{
    $error = '';

    $sourceHash = importador_orcamento_pdf_busca_source_file_hash($error);
    if ($sourceHash === '') {
        return false;
    }

    $compiledHash = importador_orcamento_pdf_busca_compiled_file_hash($error);
    if ($compiledHash === '') {
        return false;
    }

    $index = importador_orcamento_pdf_busca_index_load($error);
    if (empty($index)) {
        return false;
    }

    if (!isset($index['source_hash']) || (string) $index['source_hash'] !== $sourceHash) {
        $error = 'O índice está desatualizado em relação ao busca_source.json.';
        return false;
    }

    if (!isset($index['compiled_hash']) || (string) $index['compiled_hash'] !== $compiledHash) {
        $error = 'O índice está desatualizado em relação ao busca_compiled.json.';
        return false;
    }

    return true;
}

function importador_orcamento_pdf_busca_dimension_alias_has_standalone_occurrence($descricaoNormalizada, $aliasNormalizado)
{
    $descricaoNormalizada = (string) $descricaoNormalizada;
    $aliasNormalizado = (string) $aliasNormalizado;
    $aliasLength = strlen($aliasNormalizado);
    $offset = 0;

    while (($position = strpos($descricaoNormalizada, $aliasNormalizado, $offset)) !== false) {
        $previous = $position > 0 ? $descricaoNormalizada[$position - 1] : '';
        $nextPosition = $position + $aliasLength;
        $next = $nextPosition < strlen($descricaoNormalizada) ? $descricaoNormalizada[$nextPosition] : '';

        if (($previous === '' || !ctype_digit($previous)) && ($next === '' || !ctype_digit($next))) {
            return true;
        }

        $offset = $position + 1;
    }

    return false;
}

function importador_orcamento_pdf_busca_alias_has_standalone_occurrence($descricaoNormalizada, $aliasNormalizado)
{
    $descricaoNormalizada = (string) $descricaoNormalizada;
    $aliasNormalizado = trim((string) $aliasNormalizado);
    if ($descricaoNormalizada === '' || $aliasNormalizado === '') {
        return false;
    }

    $aliasLength = strlen($aliasNormalizado);
    $first = $aliasNormalizado[0];
    $last = $aliasNormalizado[$aliasLength - 1];
    $offset = 0;

    while (($position = strpos($descricaoNormalizada, $aliasNormalizado, $offset)) !== false) {
        $previous = $position > 0 ? $descricaoNormalizada[$position - 1] : '';
        $nextPosition = $position + $aliasLength;
        $next = $nextPosition < strlen($descricaoNormalizada) ? $descricaoNormalizada[$nextPosition] : '';

        // Para alias numérico, separadores como "x" e "#" continuam válidos
        // (100 em 200x100 e #20), mas o alias não pode fazer parte de outro número (20 em 200).
        $previousInvalid = $previous !== '' && (ctype_digit($first) ? ctype_digit($previous) : ctype_alnum($previous));
        $nextInvalid = $next !== '' && (ctype_digit($last) ? ctype_digit($next) : ctype_alnum($next));
        if (!$previousInvalid && !$nextInvalid) {
            return true;
        }

        $offset = $position + 1;
    }

    return false;
}

function importador_orcamento_pdf_busca_alias_text_tokens($value)
{
    $value = importador_orcamento_pdf_busca_normalize_alias($value);
    if ($value === '') {
        return array();
    }

    $tokens = preg_split('/\s+/', $value);
    $out = array();
    foreach ($tokens as $token) {
        $token = trim((string) $token);
        if ($token === '') {
            continue;
        }
        if (ctype_digit($token)) {
            continue;
        }
        $out[$token] = true;
    }
    return array_keys($out);
}

function importador_orcamento_pdf_busca_alias_has_same_text_terms($descricaoNormalizada, $aliasNormalizado)
{
    $aliasTokens = importador_orcamento_pdf_busca_alias_text_tokens($aliasNormalizado);
    if (count($aliasTokens) < 2) {
        return false;
    }

    $descriptionTokens = importador_orcamento_pdf_busca_alias_text_tokens($descricaoNormalizada);
    if (empty($descriptionTokens)) {
        return false;
    }
    $descriptionMap = array_fill_keys($descriptionTokens, true);
    foreach ($aliasTokens as $token) {
        if (!isset($descriptionMap[$token])) {
            return false;
        }
    }
    return true;
}

function importador_orcamento_pdf_busca_detect_aliases($descricaoNormalizada, array $compiledPayload)
{
    $descricaoNormalizada = trim((string) $descricaoNormalizada);
    if ($descricaoNormalizada === '') {
        return array();
    }

    $aliasMap = isset($compiledPayload['alias_to_canonico']) && is_array($compiledPayload['alias_to_canonico'])
        ? $compiledPayload['alias_to_canonico']
        : array();
    $aliasGroups = isset($compiledPayload['alias_to_informacoes']) && is_array($compiledPayload['alias_to_informacoes'])
        ? $compiledPayload['alias_to_informacoes'] : array();

    $aliases = array();
    $reorderedMatchesByCanonico = array();
    foreach ($aliasMap as $aliasNormalizado => $canonico) {
        $aliasNormalizado = trim((string) $aliasNormalizado);
        if ($aliasNormalizado === '') {
            continue;
        }
        $informacoes = isset($aliasGroups[$aliasNormalizado]) && is_array($aliasGroups[$aliasNormalizado])
            ? array_values(array_filter($aliasGroups[$aliasNormalizado], function ($nome) { return trim((string) $nome) !== ''; }))
            : array();
        // Um alias reaproveitado em mais de uma informação é ambíguo: mantém o
        // comportamento anterior, sem forçar a exclusão de nenhum grupo.
        $informacao = count($informacoes) === 1 ? (string) $informacoes[0] : '';
        if (importador_orcamento_pdf_busca_alias_has_standalone_occurrence($descricaoNormalizada, $aliasNormalizado)) {
            $aliases[] = array(
                'alias_normalizado' => $aliasNormalizado,
                'canonico' => (string) $canonico,
                'informacao' => $informacao,
                'length' => strlen($aliasNormalizado),
                'match_mode' => 'exact_terms',
            );
            continue;
        }

        if (importador_orcamento_pdf_busca_alias_has_same_text_terms($descricaoNormalizada, $aliasNormalizado)) {
            $canonico = (string) $canonico;
            if (!isset($reorderedMatchesByCanonico[$canonico])) {
                $reorderedMatchesByCanonico[$canonico] = array();
            }
            $reorderedMatchesByCanonico[$canonico][] = $aliasNormalizado;
        }
    }

    // A ordem de termos textuais não muda o significado somente quando há uma
    // única categoria possível. Qualquer ambiguidade continua exigindo regra
    // explícita no configurador.
    if (count($reorderedMatchesByCanonico) === 1) {
        foreach ($reorderedMatchesByCanonico as $canonico => $matchedAliases) {
            foreach ($matchedAliases as $aliasNormalizado) {
                $aliases[] = array(
                    'alias_normalizado' => $aliasNormalizado,
                    'canonico' => (string) $canonico,
                    'informacao' => isset($aliasGroups[$aliasNormalizado]) && is_array($aliasGroups[$aliasNormalizado]) && count($aliasGroups[$aliasNormalizado]) === 1 ? (string) $aliasGroups[$aliasNormalizado][0] : '',
                    'length' => strlen($aliasNormalizado),
                    'match_mode' => 'unordered_text_terms',
                );
            }
        }
    }

    usort($aliases, function ($a, $b) {
        if ($a['length'] === $b['length']) {
            return strcmp((string) $a['alias_normalizado'], (string) $b['alias_normalizado']);
        }
        return $b['length'] - $a['length'];
    });

    return $aliases;
}

function importador_orcamento_pdf_busca_deduplicate_canonicos(array $aliasesEncontrados)
{
    $out = array();
    $seen = array();
    $selectedByInformation = array();

    foreach ($aliasesEncontrados as $alias) {
        $canonico = isset($alias['canonico']) ? trim((string) $alias['canonico']) : '';
        if ($canonico === '' || isset($seen[$canonico])) {
            continue;
        }
        $informacao = isset($alias['informacao']) ? trim((string) $alias['informacao']) : '';
        if ($informacao !== '' && isset($selectedByInformation[$informacao])) {
            continue;
        }
        $seen[$canonico] = true;
        if ($informacao !== '') {
            $selectedByInformation[$informacao] = $canonico;
        }
        $out[] = $canonico;
    }

    return $out;
}

function importador_orcamento_pdf_busca_build_bucket_debug(array $canonicosDetectados, array $indexPayload)
{
    $out = array();
    $bucketMap = isset($indexPayload['valores']) && is_array($indexPayload['valores']) ? $indexPayload['valores'] : array();

    foreach ($canonicosDetectados as $canonico) {
        $ids = isset($bucketMap[$canonico]) && is_array($bucketMap[$canonico]) ? array_values(array_map('intval', $bucketMap[$canonico])) : array();
        $out[] = array(
            'canonico' => $canonico,
            'total' => count($ids),
            'sample_ids' => array_slice($ids, 0, 20),
            'ids' => $ids,
        );
    }

    return $out;
}

function importador_orcamento_pdf_busca_intersect_progressive(array $bucketDebug, $totalInicial)
{
    $result = array(
        'ordem_original' => array(),
        'ordem_efetiva' => array(),
        'passos' => array(),
        'final_ids' => array(),
        'zero_at_step' => null,
        'total_inicial' => (int) $totalInicial,
    );

    foreach ($bucketDebug as $bucket) {
        $result['ordem_original'][] = isset($bucket['canonico']) ? (string) $bucket['canonico'] : '';
    }

    if (empty($bucketDebug)) {
        return $result;
    }

    usort($bucketDebug, function ($a, $b) {
        $countA = isset($a['total']) ? (int) $a['total'] : 0;
        $countB = isset($b['total']) ? (int) $b['total'] : 0;
        if ($countA === $countB) {
            return strcmp((string) $a['canonico'], (string) $b['canonico']);
        }
        return $countA - $countB;
    });

    $corrente = null;
    foreach ($bucketDebug as $idx => $bucket) {
        $canonico = isset($bucket['canonico']) ? (string) $bucket['canonico'] : '';
        $ids = isset($bucket['ids']) && is_array($bucket['ids']) ? array_values(array_map('intval', $bucket['ids'])) : array();
        $antes = ($idx === 0) ? (int) $totalInicial : count($corrente);

        $result['ordem_efetiva'][] = $canonico;
        if ($idx === 0) {
            $corrente = $ids;
        } else {
            $corrente = array_values(array_map('intval', array_intersect($corrente, $ids)));
        }

        $depois = count($corrente);
        $result['passos'][] = array(
            'passo' => $idx + 1,
            'canonico' => $canonico,
            'antes' => $antes,
            'bucket_total' => count($ids),
            'depois' => $depois,
        );

        if ($depois === 0 && $result['zero_at_step'] === null) {
            $result['zero_at_step'] = $idx + 1;
        }
    }

    $result['final_ids'] = is_array($corrente) ? $corrente : array();
    return $result;
}

function importador_orcamento_pdf_busca_preselecao_test($descricao, &$error = '')
{
    $error = '';
    $descricao = trim((string) $descricao);

    $compiled = importador_orcamento_pdf_busca_compiled_load($error);
    if (empty($compiled)) {
        return array();
    }
    $index = importador_orcamento_pdf_busca_index_load($error);
    if (empty($index)) {
        return array();
    }

    $cachePaths = importador_orcamento_pdf_global_produtos_cache_paths();
    if (!is_file($cachePaths['final'])) {
        $error = 'Arquivo produtos_cache.json não encontrado. Gere o cache de produtos do importador antes de testar a pré-seleção.';
        return array();
    }

    $descricaoNormalizada = importador_orcamento_pdf_busca_normalize_alias($descricao);
    $aliasesEncontrados = importador_orcamento_pdf_busca_detect_aliases($descricaoNormalizada, $compiled);
    $canonicosDetectados = importador_orcamento_pdf_busca_deduplicate_canonicos($aliasesEncontrados);
    $totalInicial = isset($index['total_produtos']) ? (int) $index['total_produtos'] : 0;
    $bucketDebug = importador_orcamento_pdf_busca_build_bucket_debug($canonicosDetectados, $index);

    $resultado = array(
        'descricao_original' => $descricao,
        'descricao_normalizada' => $descricaoNormalizada,
        'aliases_encontrados' => $aliasesEncontrados,
        'canonicos_encontrados' => $canonicosDetectados,
        'total_inicial_catalogo' => $totalInicial,
        'buckets' => $bucketDebug,
        'ordem_original' => array(),
        'ordem_efetiva' => array(),
        'passos_intersecao' => array(),
        'zero_at_step' => null,
        'total_final_candidatos' => 0,
        'candidatos' => array(),
    );

    if (empty($canonicosDetectados)) {
        return $resultado;
    }

    if (count($canonicosDetectados) === 1) {
        $bucket = reset($bucketDebug);
        $resultado['ordem_original'] = array(isset($bucket['canonico']) ? (string) $bucket['canonico'] : '');
        $resultado['ordem_efetiva'] = $resultado['ordem_original'];
        $resultado['passos_intersecao'][] = array(
            'passo' => 1,
            'canonico' => isset($bucket['canonico']) ? (string) $bucket['canonico'] : '',
            'antes' => $totalInicial,
            'bucket_total' => isset($bucket['total']) ? (int) $bucket['total'] : 0,
            'depois' => isset($bucket['total']) ? (int) $bucket['total'] : 0,
        );
        $finalIds = isset($bucket['ids']) && is_array($bucket['ids']) ? $bucket['ids'] : array();
    } else {
        $intersecao = importador_orcamento_pdf_busca_intersect_progressive($bucketDebug, $totalInicial);
        $resultado['ordem_original'] = $intersecao['ordem_original'];
        $resultado['ordem_efetiva'] = $intersecao['ordem_efetiva'];
        $resultado['passos_intersecao'] = $intersecao['passos'];
        $resultado['zero_at_step'] = $intersecao['zero_at_step'];
        $finalIds = $intersecao['final_ids'];
    }

    $resultado['total_final_candidatos'] = count($finalIds);
    if (empty($finalIds)) {
        return $resultado;
    }

    $produtos = importador_orcamento_pdf_collect_products_by_ids_shared($cachePaths['final'], $finalIds);
    $candidatos = array();
    foreach ($finalIds as $id) {
        $key = (string) (int) $id;
        if (!isset($produtos[$key]) || !is_array($produtos[$key])) {
            continue;
        }
        $produto = $produtos[$key];
        $candidatos[] = array(
            'D001_Id' => isset($produto['D001_Id']) ? (int) $produto['D001_Id'] : 0,
            'codigo' => isset($produto['codigo']) ? (string) $produto['codigo'] : '',
            'descricao' => isset($produto['descricao']) ? (string) $produto['descricao'] : '',
        );
        if (count($candidatos) >= 100) {
            break;
        }
    }

    $resultado['candidatos'] = $candidatos;
    return $resultado;
}

function importador_orcamento_pdf_busca_priorizar_canonicos_por_informacao(array $aliasesEncontrados, array $canonicosPreferidosPorInformacao)
{
    if (empty($aliasesEncontrados) || empty($canonicosPreferidosPorInformacao)) {
        return $aliasesEncontrados;
    }

    usort($aliasesEncontrados, function ($a, $b) use ($canonicosPreferidosPorInformacao) {
        $informacaoA = isset($a['informacao']) ? trim((string) $a['informacao']) : '';
        $informacaoB = isset($b['informacao']) ? trim((string) $b['informacao']) : '';
        $prioridadeA = $informacaoA !== ''
            && isset($canonicosPreferidosPorInformacao[$informacaoA])
            && (string) ($a['canonico'] ?? '') === (string) $canonicosPreferidosPorInformacao[$informacaoA] ? 0 : 1;
        $prioridadeB = $informacaoB !== ''
            && isset($canonicosPreferidosPorInformacao[$informacaoB])
            && (string) ($b['canonico'] ?? '') === (string) $canonicosPreferidosPorInformacao[$informacaoB] ? 0 : 1;
        if ($prioridadeA !== $prioridadeB) {
            return $prioridadeA - $prioridadeB;
        }
        $lengthA = isset($a['length']) ? (int) $a['length'] : 0;
        $lengthB = isset($b['length']) ? (int) $b['length'] : 0;
        if ($lengthA !== $lengthB) {
            return $lengthB - $lengthA;
        }
        return strcmp((string) ($a['alias_normalizado'] ?? ''), (string) ($b['alias_normalizado'] ?? ''));
    });

    return $aliasesEncontrados;
}

function importador_orcamento_pdf_busca_preselecao_runtime($descricao, &$error = '', array $canonicosPreferidosPorInformacao = array())
{
    $error = '';
    $descricao = trim((string) $descricao);
    $descricaoNormalizada = importador_orcamento_pdf_busca_normalize_alias($descricao);

    if ($descricao === '') {
        return array(
            'enabled' => false,
            'descricao_normalizada' => $descricaoNormalizada,
            'aliases_encontrados' => array(),
            'canonicos_encontrados' => array(),
            'buckets' => array(),
            'ordem_original' => array(),
            'ordem_efetiva' => array(),
            'passos_intersecao' => array(),
            'final_ids' => array(),
            'total_final_candidatos' => 0,
            'total_inicial_catalogo' => 0,
            'motivo' => 'descricao_vazia',
        );
    }

    $compiledError = '';
    $compiled = importador_orcamento_pdf_busca_compiled_load($compiledError);
    $indexError = '';
    $index = importador_orcamento_pdf_busca_index_load($indexError);
    if (empty($compiled) || empty($index)) {
        return array(
            'enabled' => false,
            'descricao_normalizada' => $descricaoNormalizada,
            'aliases_encontrados' => array(),
            'canonicos_encontrados' => array(),
            'buckets' => array(),
            'ordem_original' => array(),
            'ordem_efetiva' => array(),
            'passos_intersecao' => array(),
            'final_ids' => array(),
            'total_final_candidatos' => 0,
            'total_inicial_catalogo' => 0,
            'motivo' => 'artefatos_busca_indisponiveis',
            'compiled_error' => $compiledError,
            'index_error' => $indexError,
        );
    }

    $aliasesEncontrados = importador_orcamento_pdf_busca_detect_aliases($descricaoNormalizada, $compiled);
    // Uso opcional e localizado pelo gerador de candidatos: se o PDF já
    // identificou um valor explícito para a mesma informação, ele vence uma
    // expressão genérica adicionada no texto enriquecido.
    $aliasesEncontrados = importador_orcamento_pdf_busca_priorizar_canonicos_por_informacao($aliasesEncontrados, $canonicosPreferidosPorInformacao);
    $canonicosDetectados = importador_orcamento_pdf_busca_deduplicate_canonicos($aliasesEncontrados);
    $totalInicial = isset($index['total_produtos']) ? (int) $index['total_produtos'] : 0;
    $bucketDebug = importador_orcamento_pdf_busca_build_bucket_debug($canonicosDetectados, $index);

    $resultado = array(
        'enabled' => true,
        'descricao_original' => $descricao,
        'descricao_normalizada' => $descricaoNormalizada,
        'aliases_encontrados' => $aliasesEncontrados,
        'canonicos_encontrados' => $canonicosDetectados,
        'total_inicial_catalogo' => $totalInicial,
        'buckets' => $bucketDebug,
        'ordem_original' => array(),
        'ordem_efetiva' => array(),
        'passos_intersecao' => array(),
        'zero_at_step' => null,
        'total_final_candidatos' => 0,
        'final_ids' => array(),
        'motivo' => 'nenhum_alias_encontrado',
    );

    if (empty($canonicosDetectados)) {
        return $resultado;
    }

    if (count($canonicosDetectados) === 1) {
        $bucket = reset($bucketDebug);
        $resultado['ordem_original'] = array(isset($bucket['canonico']) ? (string) $bucket['canonico'] : '');
        $resultado['ordem_efetiva'] = $resultado['ordem_original'];
        $resultado['passos_intersecao'][] = array(
            'passo' => 1,
            'canonico' => isset($bucket['canonico']) ? (string) $bucket['canonico'] : '',
            'antes' => $totalInicial,
            'bucket_total' => isset($bucket['total']) ? (int) $bucket['total'] : 0,
            'depois' => isset($bucket['total']) ? (int) $bucket['total'] : 0,
        );
        $resultado['final_ids'] = isset($bucket['ids']) && is_array($bucket['ids']) ? array_values(array_map('intval', $bucket['ids'])) : array();
        $resultado['total_final_candidatos'] = count($resultado['final_ids']);
        $resultado['motivo'] = 'bucket_unico';
        return $resultado;
    }

    $intersecao = importador_orcamento_pdf_busca_intersect_progressive($bucketDebug, $totalInicial);
    $resultado['ordem_original'] = isset($intersecao['ordem_original']) && is_array($intersecao['ordem_original']) ? $intersecao['ordem_original'] : array();
    $resultado['ordem_efetiva'] = isset($intersecao['ordem_efetiva']) && is_array($intersecao['ordem_efetiva']) ? $intersecao['ordem_efetiva'] : array();
    $resultado['passos_intersecao'] = isset($intersecao['passos']) && is_array($intersecao['passos']) ? $intersecao['passos'] : array();
    $resultado['zero_at_step'] = isset($intersecao['zero_at_step']) ? $intersecao['zero_at_step'] : null;
    $resultado['final_ids'] = isset($intersecao['final_ids']) && is_array($intersecao['final_ids']) ? array_values(array_map('intval', $intersecao['final_ids'])) : array();
    $resultado['total_final_candidatos'] = count($resultado['final_ids']);
    $resultado['motivo'] = !empty($resultado['final_ids']) ? 'intersecao' : 'intersecao_zerou';

    return $resultado;
}

function importador_orcamento_pdf_fs_work_base_dir()
{
    return importador_orcamento_pdf_fs_module_dir() . '/_work';
}

function importador_orcamento_pdf_fs_orcamento_dir($D024_Id, $T003_Id)
{
    $D024_Id = (int) $D024_Id;
    $T003_Id = (int) $T003_Id;

    return importador_orcamento_pdf_fs_module_dir()
        . '/D024_' . $D024_Id
        . '/T003_' . $T003_Id;
}

function importador_orcamento_pdf_fs_orcamento_dir_candidates($D024_Id, $T003_Id)
{
    $D024_Id = (int) $D024_Id;
    $T003_Id = (int) $T003_Id;
    $dataDir = importador_orcamento_pdf_fs_data_dir();

    $newBase = $dataDir . '/importadorOrcamentoPdf';
    $legacyBase = $dataDir . '/importador_orcamento_pdf';

    return array(
        $newBase . '/D024_' . $D024_Id . '/T003_' . $T003_Id,
        $legacyBase . '/D024_' . $D024_Id . '/T003_' . $T003_Id,
    );
}

function importador_orcamento_pdf_fs_orcamento_dir_existing($D024_Id, $T003_Id)
{
    $candidates = importador_orcamento_pdf_fs_orcamento_dir_candidates($D024_Id, $T003_Id);
    foreach ($candidates as $dir) {
        if (is_dir($dir)) {
            return $dir;
        }
    }

    return $candidates[0];
}

function importador_orcamento_pdf_fs_work_dir($hash)
{
    $hash = preg_replace('/[^a-f0-9]/i', '', (string) $hash);
    return importador_orcamento_pdf_fs_work_base_dir() . '/' . $hash;
}

function importador_orcamento_pdf_ensure_directory($dir)
{
    if (is_dir($dir)) {
        return true;
    }

    return mkdir($dir, 0770, true);
}

function importador_orcamento_pdf_bootstrap()
{
    if (!isset($_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION])) {
        $_SESSION[IMPORTADOR_ORCAMENTO_PDF_SESSION] = array(
            'created_at' => date('Y-m-d H:i:s'),
        );
    }

    return importador_orcamento_pdf_bootstrap_legacy_env();
}

function importador_orcamento_pdf_bootstrap_legacy_env()
{
    static $loaded = false;
    static $available = false;

    if ($loaded) {
        return $available;
    }

    $loaded = true;

    if (empty($_SESSION['Usuario_Atual']) || empty($_SESSION['Empresa_Atual'])) {
        return false;
    }

    global $g, $confUsuario;

    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/bibliotecas/confUsuario.php';

    $g = isset($g) && is_array($g) ? $g : array();
    $g['urlRaiz'] = $confUsuario['urlRaiz'];
    $g['pathWeb'] = isset($g['pathWeb']) && $g['pathWeb'] !== '' ? $g['pathWeb'] : importador_orcamento_pdf_detect_path_web();
    $g['pathRaiz'] = $confUsuario['pathRaiz'];
    $g['tabelasCache'] = array();

    if (!isset($g['pathDados']) || $g['pathDados'] === '') {
        $databaseName = isset($g['dbDatabase']) && $g['dbDatabase'] !== '' ? $g['dbDatabase'] : $confUsuario['dbDatabase'];
        $databaseName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $databaseName);
        $g['pathDados'] = "dados_usuarios/{$databaseName}/";
    }

    chdir($g['pathRaiz']);

    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/bibliotecas/sessao.php';
    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/bibliotecas/conexaoBanco.php';
    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/sistema/interfaceUsuario.php';
    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/sistema/automacaoBanco.php';
    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/sistema/automacaoFormulario.php';
    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/sistema/confSistema.php';
    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/bibliotecas/restricoes.php';
    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/bibliotecas/funcoesGerais.php';
    require_once IMPORTADOR_ORCAMENTO_PDF_ROOT . '/sistema/apps.php';

    $empresaAtual = (int) (isset($_SESSION['Empresa_Atual']) ? $_SESSION['Empresa_Atual'] : 0);
    $usuarioAtual = (int) (isset($g['usuarioAtual']) && $g['usuarioAtual'] ? $g['usuarioAtual'] : $_SESSION['Usuario_Atual']);

    if ($empresaAtual <= 0 || $usuarioAtual <= 0) {
        return false;
    }

    $g['empresaAtual'] = $empresaAtual;
    $g['usuarioAtual'] = $usuarioAtual;
    $g['conf'] = new \hardness\confSistema();

    $conn = isset($g['conexaoBanco']) ? $g['conexaoBanco'] : null;
    if (!$conn) {
        return false;
    }

    $resC031 = \mysqli_query($conn, "SELECT * FROM C031 WHERE C031_C004_Id = '{$empresaAtual}'");
    while ($resC031 && $row = \mysqli_fetch_assoc($resC031)) {
        $g['c031'][$row['C031_Campo']] = $row['C031_Valor'];
        $g['C031'][$row['C031_Campo']] = $row['C031_Valor'];
    }

    $resC020 = \mysqli_query($conn, "SELECT * FROM C020 WHERE C020_C004_Id = '{$empresaAtual}'");
    $g['c020'] = $g['C020'] = $resC020 ? \mysqli_fetch_assoc($resC020) : array();

    $resC004 = \mysqli_query($conn, "SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 ON D018_Id=C004_D018_Id WHERE C004_Id='{$empresaAtual}'");
    $g['c004'] = $g['C004'] = $resC004 ? \mysqli_fetch_assoc($resC004) : array();

    $resC007 = \mysqli_query($conn, "SELECT * FROM C007 WHERE C007_Id='{$usuarioAtual}'");
    $g['c007'] = $g['C007'] = $resC007 ? \mysqli_fetch_assoc($resC007) : array();

    $perfilUsuario = (int) (isset($_SESSION['Perfil_Usuario']) ? $_SESSION['Perfil_Usuario'] : (isset($g['perfilUsuario']) ? $g['perfilUsuario'] : 0));
    $g['perfilUsuario'] = $perfilUsuario;

    if ($perfilUsuario > 0) {
        $resC021 = \mysqli_query($conn, "SELECT * FROM C021 WHERE C021_Id='{$perfilUsuario}'");
        $g['c021'] = $g['C021'] = $resC021 ? \mysqli_fetch_assoc($resC021) : array();
    }

    $available = true;
    return true;
}

function importador_orcamento_pdf_db()
{
    if (!importador_orcamento_pdf_bootstrap()) {
        return null;
    }

    global $g;
    return isset($g['conexaoBanco']) ? $g['conexaoBanco'] : null;
}

function importador_orcamento_pdf_user_id()
{
    global $g;
    importador_orcamento_pdf_bootstrap();
    return (int) (isset($g['usuarioAtual']) ? $g['usuarioAtual'] : 0);
}

function importador_orcamento_pdf_company_id()
{
    global $g;
    importador_orcamento_pdf_bootstrap();
    return (int) (isset($g['empresaAtual']) ? $g['empresaAtual'] : 0);
}

function importador_orcamento_pdf_truthy_flag($value, $default = false)
{
    if ($value === null || $value === '') {
        return (bool) $default;
    }

    $normalized = strtolower(trim((string) $value));
    if (in_array($normalized, array('1', 'true', 'yes', 'sim', 'on'), true)) {
        return true;
    }
    if (in_array($normalized, array('0', 'false', 'no', 'nao', 'não', 'off'), true)) {
        return false;
    }

    return (bool) $default;
}

function importador_orcamento_pdf_ai_parser_enabled($default = false)
{
    importador_orcamento_pdf_bootstrap();

    $envValue = getenv('IMPORTADOR_PDF_AI_ENABLED');
    if ($envValue !== false && $envValue !== '') {
        return importador_orcamento_pdf_truthy_flag($envValue, $default);
    }

    global $g;
    $configKeys = array(
        'importadorPdfOpenAiAtivo',
        'openAIPdfParserEnabled',
        'importadorPdfAiEnabled',
    );

    foreach ($configKeys as $key) {
        if (isset($g['C031'][$key]) && trim((string) $g['C031'][$key]) !== '') {
            return importador_orcamento_pdf_truthy_flag($g['C031'][$key], $default);
        }
    }

    return (bool) $default;
}

function importador_orcamento_pdf_ai_parser_model($default = '')
{
    importador_orcamento_pdf_bootstrap();

    $envValue = trim((string) getenv('IMPORTADOR_PDF_AI_MODEL'));
    if ($envValue !== '') {
        return $envValue;
    }

    global $g;
    $configKeys = array(
        'importadorPdfOpenAiModel',
        'openAIPdfParserModel',
        'importadorPdfAiModel',
    );

    foreach ($configKeys as $key) {
        if (isset($g['C031'][$key]) && trim((string) $g['C031'][$key]) !== '') {
            return trim((string) $g['C031'][$key]);
        }
    }

    return trim((string) $default);
}

function importador_orcamento_pdf_ai_parser_timeout_seconds($default = 180)
{
    importador_orcamento_pdf_bootstrap();

    $envValue = getenv('IMPORTADOR_PDF_AI_TIMEOUT');
    if ($envValue !== false && trim((string) $envValue) !== '') {
        return max(30, (int) $envValue);
    }

    global $g;
    $configKeys = array(
        'importadorPdfOpenAiTimeout',
        'openAIPdfParserTimeout',
        'importadorPdfAiTimeout',
    );

    foreach ($configKeys as $key) {
        if (isset($g['C031'][$key]) && trim((string) $g['C031'][$key]) !== '') {
            return max(30, (int) $g['C031'][$key]);
        }
    }

    return max(30, (int) $default);
}

function importador_orcamento_pdf_ai_input_mode($default = 'pdf')
{
    importador_orcamento_pdf_bootstrap();

    $allowed = array('pdf', 'raw_text');

    $envValue = trim((string) getenv('IMPORTADOR_PDF_AI_INPUT_MODE'));
    if ($envValue !== '' && in_array($envValue, $allowed, true)) {
        return $envValue;
    }

    global $g;
    $configKeys = array(
        'importadorPdfOpenAiInputMode',
        'openAIPdfParserInputMode',
        'importadorPdfAiInputMode',
    );

    foreach ($configKeys as $key) {
        $value = isset($g['C031'][$key]) ? trim((string) $g['C031'][$key]) : '';
        if ($value !== '' && in_array($value, $allowed, true)) {
            return $value;
        }
    }

    return in_array($default, $allowed, true) ? $default : 'pdf';
}

function importador_orcamento_pdf_matching_mode($default = 'legacy')
{
    importador_orcamento_pdf_bootstrap();

    $allowed = array('legacy', 'generic');
    $fallback = in_array((string) $default, $allowed, true) ? (string) $default : 'legacy';

    $envValue = strtolower(trim((string) getenv('IMPORTADOR_PDF_MATCHING_MODE')));
    if ($envValue !== '' && in_array($envValue, $allowed, true)) {
        return $envValue;
    }

    global $g;
    $configKeys = array(
        'importadorPdfMatchingMode',
        'pdfMatchingMode',
        'importadorPdfModoMatching',
    );

    foreach ($configKeys as $key) {
        $value = strtolower(trim((string) (isset($g['C031'][$key]) ? $g['C031'][$key] : '')));
        if ($value !== '' && in_array($value, $allowed, true)) {
            return $value;
        }
    }

    return $fallback;
}

function importador_orcamento_pdf_matching_shadow_enabled($default = false)
{
    importador_orcamento_pdf_bootstrap();

    $envValue = getenv('IMPORTADOR_PDF_MATCHING_SHADOW');
    if ($envValue !== false && $envValue !== '') {
        return importador_orcamento_pdf_truthy_flag($envValue, $default);
    }

    global $g;
    $configKeys = array(
        'importadorPdfMatchingShadow',
        'pdfMatchingShadow',
        'importadorPdfModoMatchingShadow',
    );

    foreach ($configKeys as $key) {
        if (isset($g['C031'][$key]) && trim((string) $g['C031'][$key]) !== '') {
            return importador_orcamento_pdf_truthy_flag($g['C031'][$key], $default);
        }
    }

    return true;
}

function importador_orcamento_pdf_debug_normalize_enabled($default = false)
{
    importador_orcamento_pdf_bootstrap();

    $envValue = getenv('IMPORTADOR_PDF_DEBUG_NORMALIZE');
    if ($envValue !== false && $envValue !== '') {
        return importador_orcamento_pdf_truthy_flag($envValue, $default);
    }

    global $g;
    $configKeys = array(
        'importadorPdfDebugNormalize',
        'pdfDebugNormalize',
        'importadorPdfNormalizeDebug',
    );

    foreach ($configKeys as $key) {
        if (isset($g['C031'][$key]) && trim((string) $g['C031'][$key]) !== '') {
            return importador_orcamento_pdf_truthy_flag($g['C031'][$key], $default);
        }
    }

    return $default;
}
