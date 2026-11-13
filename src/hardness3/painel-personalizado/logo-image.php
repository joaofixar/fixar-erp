<?php
namespace hardness;

function painelPersonalizadoLogoMime($arquivo)
{
    clearstatcache(true, $arquivo);
    if (!is_file($arquivo) || !is_readable($arquivo)) return '';
    $imagem = @getimagesize($arquivo);
    if (is_array($imagem) && isset($imagem['mime']) && strpos($imagem['mime'], 'image/') === 0) {
        return $imagem['mime'];
    }

    // PHP 7.4 não identifica AVIF. Reconhecer o FileTypeBox pelo conteúdo,
    // sem confiar na extensão nem precisar converter a logo enviada.
    // https://aomediacodec.github.io/av1-avif/#brands
    $cabecalho = @file_get_contents($arquivo, false, null, 0, 4096);
    if ($cabecalho === false || strlen($cabecalho) < 16 || substr($cabecalho, 4, 4) !== 'ftyp') return '';
    $caixa = unpack('Ntamanho', substr($cabecalho, 0, 4));
    $tamanho = $caixa['tamanho'];
    if ($tamanho < 16 || $tamanho % 4 !== 0 || $tamanho > strlen($cabecalho) || $tamanho + 8 > filesize($arquivo)) return '';

    // major_brand e compatible_brands são códigos de quatro bytes;
    // os bytes 12..15 são a versão e não devem ser interpretados como marca.
    $marcas = array(substr($cabecalho, 8, 4));
    for ($posicao = 16; $posicao < $tamanho; $posicao += 4) {
        $marcas[] = substr($cabecalho, $posicao, 4);
    }
    if (in_array('avif', $marcas, true) || in_array('avis', $marcas, true)) return 'image/avif';
    return '';
}
