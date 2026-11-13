<?php
namespace hardness;

function dashboardCarteiraBancosHabilitado($indicador)
{
    return !empty($indicador['controleCarteiraBancos']['habilitado']);
}

function dashboardCarteiraBancosPortador($nome)
{
    $nome = preg_replace('/\s+/u', ' ', trim((string) $nome));
    return function_exists('mb_strtoupper') ? mb_strtoupper($nome, 'UTF-8')
        : strtr(strtoupper($nome), array('á' => 'Á', 'à' => 'À', 'ã' => 'Ã', 'â' => 'Â', 'é' => 'É', 'ê' => 'Ê', 'í' => 'Í', 'ó' => 'Ó', 'ô' => 'Ô', 'õ' => 'Õ', 'ú' => 'Ú', 'ç' => 'Ç'));
}

function dashboardCarteiraBancosConfig($indicador, &$erro)
{
    $erro = '';
    $bancos = isset($indicador['controleCarteiraBancos']['bancos']) ? $indicador['controleCarteiraBancos']['bancos'] : null;
    if (!is_array($bancos) || !$bancos || count($bancos) > 24) {
        $erro = 'Configure entre 1 e 24 bancos para o controle de carteira.'; return false;
    }
    $ids = array(); $portadores = array();
    foreach ($bancos as &$banco) {
        if (!is_array($banco) || !isset($banco['id'], $banco['nome'], $banco['garantia'], $banco['portadores'])
            || !is_string($banco['id']) || !preg_match('/^[a-z][a-z0-9_]{1,39}$/D', $banco['id']) || isset($ids[$banco['id']])
            || !is_string($banco['nome']) || trim($banco['nome']) === '' || strlen($banco['nome']) > 400
            || !dashboardCarteiraBancosValorValido($banco['garantia'])
            || !is_array($banco['portadores']) || !$banco['portadores'] || count($banco['portadores']) > 30) {
            $erro = 'Há um banco inválido no controle de carteira.'; return false;
        }
        $ids[$banco['id']] = true;
        foreach ($banco['portadores'] as $nome) {
            if (!is_string($nome) || trim($nome) === '' || strlen($nome) > 800) {
                $erro = 'Informe os portadores de cada banco.'; return false;
            }
            $chave = dashboardCarteiraBancosPortador($nome);
            if (isset($portadores[$chave])) { $erro = 'Um portador não pode pertencer a mais de um banco.'; return false; }
            $portadores[$chave] = true;
        }
        $banco['cor'] = isset($banco['cor']) && is_string($banco['cor']) && preg_match('/^#[a-f0-9]{6}$/iD', $banco['cor']) ? $banco['cor'] : '#2563eb';
    }
    unset($banco);
    return array_values($bancos);
}

function dashboardCarteiraBancosValorValido($valor)
{
    return (is_int($valor) || is_float($valor)) && is_finite((float) $valor) && $valor >= 0 && $valor <= 1000000000000
        && round($valor, 2) == $valor;
}

function dashboardCarteiraBancosArquivo($empresa, $indicador)
{
    if ((int) $empresa <= 0 || !is_string($indicador) || !preg_match('/^[a-z][a-zA-Z0-9_]{1,49}$/D', $indicador)) return '';
    $base = dashboardPersonalizadoArquivoAcessos($empresa);
    return $base === '' ? '' : dirname($base) . '/' . (int) $empresa . 'DASHBOARD PERSONALIZAVEL-garantias-' . $indicador . '.json';
}

function dashboardCarteiraBancosLer($arquivo, $bancos, &$erro)
{
    $erro = '';
    if ($arquivo === '') { $erro = 'Não foi possível localizar as garantias dos bancos.'; return false; }
    $garantias = array();
    foreach ($bancos as $banco) $garantias[$banco['id']] = (float) $banco['garantia'];
    $revisao = 'inicial';
    if (is_file($arquivo)) {
        $texto = @file_get_contents($arquivo);
        $dados = $texto === false ? null : json_decode($texto, true);
        if (!is_array($dados) || !isset($dados['garantias']) || !is_array($dados['garantias'])) {
            $erro = 'Não foi possível ler as garantias dos bancos.'; return false;
        }
        foreach ($dados['garantias'] as $id => $valor) {
            if (!dashboardCarteiraBancosValorValido($valor)) { $erro = 'Há uma garantia salva inválida.'; return false; }
            if (array_key_exists($id, $garantias)) $garantias[$id] = (float) $valor;
        }
        $revisao = hash('sha256', $texto);
    }
    return array('garantias' => $garantias, 'revisao' => $revisao);
}

function dashboardCarteiraBancosSalvar($arquivo, $bancos, $entrada, $usuario, &$erro)
{
    $erro = '';
    if (!is_array($entrada) || !isset($entrada['garantias'], $entrada['revisao']) || !is_array($entrada['garantias'])
        || !is_string($entrada['revisao']) || count($entrada['garantias']) !== count($bancos)) {
        $erro = 'Informe as garantias e a revisão carregada.'; return false;
    }
    $garantias = array();
    foreach ($bancos as $banco) {
        $id = $banco['id'];
        if (!array_key_exists($id, $entrada['garantias']) || !dashboardCarteiraBancosValorValido($entrada['garantias'][$id])) {
            $erro = 'Informe valores de garantia entre zero e 1 trilhão, com até duas casas decimais.'; return false;
        }
        $garantias[$id] = round($entrada['garantias'][$id], 2);
    }
    if ($arquivo === '') { $erro = 'Não foi possível localizar as garantias dos bancos.'; return false; }
    $dir = dirname($arquivo);
    if (!is_dir($dir) && !@mkdir($dir, 0770, true) && !is_dir($dir)) { $erro = 'Não foi possível salvar as garantias.'; return false; }
    $lock = @fopen($arquivo . '.lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX)) { if ($lock) fclose($lock); $erro = 'Não foi possível acessar as garantias.'; return false; }
    @chmod($arquivo . '.lock', 0660);
    try {
        $atual = dashboardCarteiraBancosLer($arquivo, $bancos, $erro);
        if ($atual === false) return false;
        if (!hash_equals($atual['revisao'], $entrada['revisao'])) {
            $erro = 'Outro usuário alterou as garantias. Atualize o indicador antes de salvar novamente.'; return false;
        }
        $texto = json_encode(array('versao' => 1, 'garantias' => $garantias, 'usuario' => (int) $usuario,
            'atualizadoEm' => gmdate('c')), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        $tmp = tempnam($dir, '.garantias-');
        if ($tmp === false || @file_put_contents($tmp, $texto, LOCK_EX) !== strlen($texto)) {
            if ($tmp !== false) @unlink($tmp); $erro = 'Não foi possível salvar as garantias.'; return false;
        }
        @chmod($tmp, 0660);
        if (!@rename($tmp, $arquivo)) { @unlink($tmp); $erro = 'Não foi possível salvar as garantias.'; return false; }
        return array('ok' => true, 'garantias' => $garantias, 'revisao' => hash('sha256', $texto));
    } finally { flock($lock, LOCK_UN); fclose($lock); }
}

function dashboardCarteiraBancosExecutar($conexao, $indicador, $empresa, $entrada, &$erro)
{
    $bancos = dashboardCarteiraBancosConfig($indicador, $erro);
    if ($bancos === false) return false;
    // Valida datas e prazo antes de buscar os nomes das empresas do grupo.
    $validacao = $indicador;
    $validacao['filtros'] = array_values(array_filter(isset($indicador['filtros']) ? $indicador['filtros'] : array(), function ($filtro) {
        return empty($filtro['opcoesCadastro']) && empty($filtro['opcoesSql']);
    }));
    $filtros = dashboardIndicadorFiltros($validacao, $entrada, $errosFiltros);
    if ($errosFiltros) { $erro = implode(' ', $errosFiltros); return false; }
    $valores = $filtros['valores'];
    $inicio = isset($valores['dataInicio']) ? $valores['dataInicio'] : '';
    $data = \DateTimeImmutable::createFromFormat('!Y-m-d', $inicio);
    $dias = isset($valores['diasProjecao']) ? $valores['diasProjecao'] : 45;
    if (!$data || $data->format('Y-m-d') !== $inicio || !is_numeric($dias) || floor($dias) != $dias || $dias < 7 || $dias > 366) {
        $erro = 'Informe a data inicial e uma projeção entre 7 e 366 dias.'; return false;
    }
    $fim = $data->modify('+' . ((int) $dias - 1) . ' days')->format('Y-m-d');
    $salvo = dashboardCarteiraBancosLer(dashboardCarteiraBancosArquivo($empresa, $indicador['id']), $bancos, $erro);
    if ($salvo === false) return false;
    $sqlBase = dashboardPersonalizadoSqlTexto(isset($indicador['sqlBase']) ? $indicador['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sqlBase, $erro)) return false;
    $opcoesFiltros = dashboardIndicadorCarregarOpcoesFiltros($conexao, isset($indicador['filtros']) ? $indicador['filtros'] : array(), $empresa, $erro);
    if ($opcoesFiltros === false) return false;
    $indicador['filtros'] = $opcoesFiltros;
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $errosFiltros);
    if ($errosFiltros) { $erro = implode(' ', $errosFiltros); return false; }
    $valores = $filtros['valores'];
    $empresasTitulo = 'Empresa atual';
    foreach ($filtros['publicos'] as $filtro) {
        if ($filtro['id'] !== 'abrangenciaEmpresa') continue;
        foreach ($filtro['opcoes'] as $opcao) {
            if ((string) $opcao['valor'] === (string) $filtro['valor']) $empresasTitulo = $opcao['titulo'];
        }
    }
    $parametros = array_merge($valores, array('empresa' => (int) $empresa, 'dataFim' => $fim));
    // Uma consulta limitada de agregados. O bucket após o horizonte conserva toda
    // a carteira futura na abertura, sem enviar cada título ao navegador.
    $sql = "SELECT portador, boleto, CASE WHEN data > :dataFim THEN DATE_ADD(:dataFim, INTERVAL 1 DAY) ELSE DATE(data) END AS dataCarteira,"
        . " SUM(valor) AS valor, COUNT(*) AS quantidade FROM ({$sqlBase}) AS carteira_base"
        . ' GROUP BY portador, boleto, dataCarteira ORDER BY portador, dataCarteira LIMIT 20001';
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($linhas === false) return false;
    if (count($linhas) > 20000) { $erro = 'A carteira excedeu o limite de grupos. Reduza o período de projeção.'; return false; }
    $mapa = array(); $porId = array();
    foreach ($bancos as $banco) {
        $id = $banco['id'];
        $porId[$id] = array('id' => $id, 'nome' => $banco['nome'], 'cor' => $banco['cor'], 'garantia' => $salvo['garantias'][$id],
            'carteiraCentavos' => 0, 'vencimentos' => array());
        foreach ($banco['portadores'] as $nome) $mapa[dashboardCarteiraBancosPortador($nome)] = $id;
    }
    $composicao = array(); $total = 0; $boletos = 0;
    foreach ($linhas as $linha) {
        $valor = (int) round((float) $linha['valor'] * 100);
        $nome = (string) $linha['portador']; $chave = dashboardCarteiraBancosPortador($nome);
        $total += $valor;
        if ($linha['boleto'] === 'S') $boletos += $valor;
        if (!isset($composicao[$chave])) $composicao[$chave] = array('portador' => $nome, 'valorCentavos' => 0, 'quantidade' => 0, 'banco' => isset($mapa[$chave]) ? $mapa[$chave] : '');
        $composicao[$chave]['valorCentavos'] += $valor;
        $composicao[$chave]['quantidade'] += (int) $linha['quantidade'];
        if (!isset($mapa[$chave])) continue;
        $id = $mapa[$chave];
        $porId[$id]['carteiraCentavos'] += $valor;
        if ($linha['dataCarteira'] >= $inicio && $linha['dataCarteira'] <= $fim) {
            if (!isset($porId[$id]['vencimentos'][$linha['dataCarteira']])) $porId[$id]['vencimentos'][$linha['dataCarteira']] = 0;
            $porId[$id]['vencimentos'][$linha['dataCarteira']] += $valor;
        }
    }
    $dados = array('dataInicio' => $inicio, 'dataFim' => $fim, 'diasProjecao' => (int) $dias, 'empresasTitulo' => $empresasTitulo,
        'bancos' => array_values($porId), 'composicao' => array_values($composicao), 'totalCentavos' => $total,
        'boletosCentavos' => $boletos, 'revisao' => $salvo['revisao']);
    return array('indicador' => dashboardIndicadorDefinicaoPublica($indicador, $filtros['publicos']),
        'controleCarteiraBancos' => $dados, 'card' => array('valor' => array_sum(array_column($porId, 'carteiraCentavos')) / 100,
            'quantidade' => array_sum(array_column($composicao, 'quantidade')), 'formato' => 'moeda'),
        'grafico' => array(), 'listagem' => array(),
        'updatedAt' => (new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')))->format('d/m/Y H:i'));
}
