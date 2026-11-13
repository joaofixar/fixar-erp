<?php
namespace hardness;

function dashboardRespostaAceitaGzip($cabecalho)
{
    $qualidades = array();
    foreach (explode(',', strtolower($cabecalho)) as $item) {
        $partes = explode(';', trim($item));
        $nome = trim(array_shift($partes));
        $qualidade = 1.0;
        foreach ($partes as $parte) {
            if (preg_match('/^\s*q\s*=\s*(0(?:\.\d+)?|1(?:\.0+)?)\s*$/', $parte, $m)) $qualidade = (float) $m[1];
            elseif (preg_match('/^\s*q\s*=/',$parte)) $qualidade = 0;
        }
        $qualidades[$nome] = $qualidade;
    }
    return isset($qualidades['gzip']) ? $qualidades['gzip'] > 0 : !empty($qualidades['*']);
}

function dashboardRespostaJson($dados)
{
    $json = json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    // Deixa a negociação com o servidor quando ele já comprime a resposta.
    $zlib = strtolower((string) ini_get('zlib.output_compression'));
    if (headers_sent() || ($zlib !== '' && $zlib !== '0' && $zlib !== 'off')) return $json;
    foreach (ob_list_handlers() as $handler) if (preg_match('/gz|zlib/i', $handler)) return $json;
    foreach (headers_list() as $header) if (stripos($header, 'Content-Encoding:') === 0) return $json;
    header('Vary: Accept-Encoding', false);
    if (getenv('HARDNESS_DASHBOARD_JSON_GZIP') === '0' || !function_exists('gzencode') || strlen($json) < 4096
        || !dashboardRespostaAceitaGzip(isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '')) return $json;
    $comprimido = gzencode($json, 3);
    if ($comprimido === false || strlen($comprimido) >= strlen($json)) return $json;
    header('Content-Encoding: gzip');
    return $comprimido;
}
