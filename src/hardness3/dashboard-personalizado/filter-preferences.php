<?php
namespace hardness;

function dashboardPersonalizadoArquivoPreferenciasFiltros($empresaId, $usuarioId)
{
    $arquivoAcessos = dashboardPersonalizadoArquivoAcessos($empresaId);
    $usuarioId = (int) $usuarioId;
    if ($arquivoAcessos === '' || $usuarioId <= 0) {
        return '';
    }

    return dirname($arquivoAcessos) . '/' . (int) $empresaId . 'DASHBOARD PERSONALIZAVEL-filtros-u' . $usuarioId . '.json';
}

function dashboardPersonalizadoLerPreferenciasFiltros($empresaId, $usuarioId)
{
    $arquivo = dashboardPersonalizadoArquivoPreferenciasFiltros($empresaId, $usuarioId);
    if ($arquivo === '' || !is_readable($arquivo)) {
        return array();
    }

    $conteudo = json_decode(file_get_contents($arquivo), true);
    return is_array($conteudo) ? $conteudo : array();
}

function dashboardPersonalizadoSalvarPreferenciasFiltros($empresaId, $usuarioId, $preferencias)
{
    $arquivo = dashboardPersonalizadoArquivoPreferenciasFiltros($empresaId, $usuarioId);
    if ($arquivo === '') {
        return false;
    }

    $diretorio = dirname($arquivo);
    if (!is_dir($diretorio) && !@mkdir($diretorio, 0770, true)) {
        return false;
    }

    $json = json_encode($preferencias, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    $temporario = $arquivo . '.' . uniqid('tmp-', true);
    if (@file_put_contents($temporario, $json . "\n", LOCK_EX) === false) {
        return false;
    }
    @chmod($temporario, 0660);
    return @rename($temporario, $arquivo);
}

function dashboardPersonalizadoChaveAbaPreferencias($abaId)
{
    $abaId = trim((string) $abaId);
    return $abaId === '' ? '_principal' : $abaId;
}

function dashboardPersonalizadoAtualizarPreferenciasFiltros($empresaId, $usuarioId, $indicadorId, $abaId, $filtros)
{
    $preferencias = dashboardPersonalizadoLerPreferenciasFiltros($empresaId, $usuarioId);
    $chaveAba = dashboardPersonalizadoChaveAbaPreferencias($abaId);
    if (!isset($preferencias[$indicadorId]) || !is_array($preferencias[$indicadorId])) {
        $preferencias[$indicadorId] = array();
    }

    if (empty($filtros)) {
        unset($preferencias[$indicadorId][$chaveAba]);
        if (empty($preferencias[$indicadorId])) {
            unset($preferencias[$indicadorId]);
        }
    } else {
        $preferencias[$indicadorId][$chaveAba] = $filtros;
    }

    return dashboardPersonalizadoSalvarPreferenciasFiltros($empresaId, $usuarioId, $preferencias);
}
