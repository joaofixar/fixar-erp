<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

function portalRepresentanteEmpresa($conexao, $conf, $empresa)
{
    $empresa = (int) $empresa;
    $resultado = array('nome' => 'Empresa', 'logo' => '');
    if ($empresa < 1) return $resultado;
    $linhas = portalRepresentanteConsultar($conexao, "SELECT COALESCE(NULLIF(TRIM(C004_Nome_Abreviado),''),C004_Nome_Empresa) AS nome,
        C004_Logo_Marca AS logo FROM C004 WHERE C004_Id=:empresa LIMIT 1", array('empresa' => $empresa));
    if (!$linhas) return $resultado;
    if (!empty($linhas[0]['nome'])) $resultado['nome'] = $linhas[0]['nome'];
    $extensao = strtolower(pathinfo(basename((string) $linhas[0]['logo']), PATHINFO_EXTENSION));
    if (!in_array($extensao, array('png','jpg','jpeg','gif','webp','bmp'), true)) return $resultado;

    // CAD031 preserva o nome original no cadastro e grava o arquivo como <C004_Id>.<extensão>.
    clearstatcache(true);
    $diretorio = realpath(dirname(__DIR__) . '/dados_usuarios/' . $conf['dbDatabase'] . '/imagens/logo');
    $arquivo = $diretorio ? realpath($diretorio . DIRECTORY_SEPARATOR . $empresa . '.' . $extensao) : false;
    if (!$arquivo || strpos($arquivo, $diretorio . DIRECTORY_SEPARATOR) !== 0 || !is_file($arquivo)
        || !is_readable($arquivo) || filesize($arquivo) > 4 * 1024 * 1024) return $resultado;
    $imagem = @getimagesize($arquivo);
    if (!$imagem || !isset($imagem['mime']) || !in_array($imagem['mime'], array('image/png','image/jpeg','image/gif','image/webp','image/bmp','image/x-ms-bmp'), true)) return $resultado;
    $conteudo = @file_get_contents($arquivo);
    if ($conteudo !== false) $resultado['logo'] = 'data:' . $imagem['mime'] . ';base64,' . base64_encode($conteudo);
    return $resultado;
}
