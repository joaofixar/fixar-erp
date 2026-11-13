<?php
namespace hardness;

function dashboardCotacoesMetaChave($entrada, &$erro)
{
    $mes = isset($entrada['mes']) && is_string($entrada['mes']) ? $entrada['mes'] : '';
    if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/', $mes)) { $erro = 'Mês da meta inválido.'; return false; }
    $partes = array($mes);
    foreach (array('empresa', 'equipe', 'vendedor', 'representante') as $campo) {
        $valor = isset($entrada[$campo]) && is_string($entrada[$campo]) ? $entrada[$campo] : '';
        $padrao = $campo === 'equipe' ? '/^[a-zA-Z0-9_-]{1,80}$/' : ($campo === 'representante' ? '/^(all|0|[1-9][0-9]{0,9})$/' : '/^(all|[1-9][0-9]{0,9})$/');
        if (!preg_match($padrao, $valor)) { $erro = 'Escopo da meta inválido.'; return false; }
        $partes[] = $valor;
    }
    $unidade = isset($entrada['unidade']) ? $entrada['unidade'] : '';
    if (!in_array($unidade, array('reais', 'toneladas'), true)) { $erro = 'Unidade da meta inválida.'; return false; }
    $partes[] = $unidade;
    return implode('|', $partes);
}

function dashboardCotacoesMetaArquivo($empresa, $usuario, $indicador = 'painel_cotacoes')
{
    if ((int) $empresa <= 0 || (int) $usuario <= 0 || !preg_match('/^[a-zA-Z0-9_]{1,80}$/', $indicador)) return '';
    $acessos = dashboardPersonalizadoArquivoAcessos($empresa);
    return $acessos === '' ? '' : dirname($acessos) . '/' . (int) $empresa . 'DASHBOARD PERSONALIZAVEL-metas-' . $indicador . '-u' . (int) $usuario . '.json';
}

function dashboardCotacoesMetaOrigemValida($origem, $host)
{
    $url = parse_url($origem); $destino = parse_url('http://' . $host);
    if (!is_array($url) || !isset($url['host'], $url['scheme']) || !in_array($url['scheme'], array('http', 'https'), true)
        || !is_array($destino) || empty($destino['host'])) return false;
    $padrao = $url['scheme'] === 'https' ? 443 : 80;
    return strcasecmp($url['host'], $destino['host']) === 0
        && (isset($url['port']) ? $url['port'] : $padrao) === (isset($destino['port']) ? $destino['port'] : $padrao);
}

function dashboardCotacoesMetaLer($arquivo, &$erro)
{
    if ($arquivo === '') { $erro = 'Não foi possível localizar as metas individuais.'; return false; }
    if (!is_file($arquivo)) return array();
    $dados = json_decode(@file_get_contents($arquivo), true);
    if (!is_array($dados) || !isset($dados['metas']) || !is_array($dados['metas'])) {
        $erro = 'Não foi possível ler as metas individuais.'; return false;
    }
    return $dados['metas'];
}

function dashboardCotacoesMetaSalvar($arquivo, $chave, $valor, &$erro)
{
    if ($arquivo === '') { $erro = 'Não foi possível localizar as metas individuais.'; return false; }
    if ($valor !== null && (!is_int($valor) && !is_float($valor) || !is_finite((float) $valor) || $valor <= 0 || $valor > 1000000000000)) {
        $erro = 'Informe uma meta maior que zero e até 1 trilhão.'; return false;
    }
    if ($valor !== null) {
        $valor = round($valor, substr($chave, -9) === 'toneladas' ? 3 : 2);
        if ($valor <= 0) { $erro = 'A meta deve ser maior que zero na unidade selecionada.'; return false; }
    }
    $dir = dirname($arquivo);
    if (!is_dir($dir) && !@mkdir($dir, 0770, true)) { $erro = 'Não foi possível salvar a meta.'; return false; }
    $lock = @fopen($arquivo . '.lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX)) { if ($lock) fclose($lock); $erro = 'Não foi possível salvar a meta.'; return false; }
    @chmod($arquivo . '.lock', 0660);
    try {
        $metas = dashboardCotacoesMetaLer($arquivo, $erro);
        if ($metas === false) return false;
        if ($valor === null) unset($metas[$chave]); else $metas[$chave] = $valor;
        if (count($metas) > 1000) { $erro = 'O limite de metas individuais foi atingido.'; return false; }
        $json = json_encode(array('versao' => 1, 'metas' => $metas), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $tmp = $arquivo . '.' . uniqid('tmp-', true);
        if ($json === false || @file_put_contents($tmp, $json . "\n", LOCK_EX) === false) { $erro = 'Não foi possível salvar a meta.'; return false; }
        @chmod($tmp, 0660);
        if (!@rename($tmp, $arquivo)) { @unlink($tmp); $erro = 'Não foi possível salvar a meta.'; return false; }
        return array('chave' => $chave, 'valor' => isset($metas[$chave]) ? $metas[$chave] : null);
    } finally { flock($lock, LOCK_UN); fclose($lock); }
}
