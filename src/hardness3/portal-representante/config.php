<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

require_once __DIR__ . '/core.php';

function portalRepresentanteArquivoPadrao()
{
    return dirname(__DIR__) . '/configuracoes/portal-representante.json';
}

function portalRepresentanteArquivoConfiguracao($empresa, $conf = null)
{
    global $confUsuario, $g;
    if ($conf === null) $conf = isset($confUsuario) ? $confUsuario : array();
    if ((int) $empresa < 0 || empty($conf['dbDatabase'])) return '';
    $dados = !empty($g['pathDados']) ? $g['pathDados'] : 'dados_usuarios/' . $conf['dbDatabase'];
    if ($dados[0] !== '/') $dados = rtrim(isset($conf['pathRaiz']) ? $conf['pathRaiz'] : dirname(__DIR__), '/\\') . '/' . $dados;
    return rtrim($dados, '/\\') . '/personalizacaoConf/' . (int) $empresa . 'PORTAL DO REPRESENTANTE-alterarPortalRepresentante.json';
}

function portalRepresentanteSqlTexto($valor)
{
    if (is_array($valor)) {
        foreach ($valor as $linha) if (!is_string($linha)) return '';
        return trim(implode("\n", $valor));
    }
    return is_string($valor) ? trim($valor) : '';
}

function portalRepresentanteCamposPeriodo($tipo)
{
    if (in_array($tipo, array('boletos','cobrancas'), true)) return array('data','vencimento','pagamento');
    return in_array($tipo, array('orcamentos','pedidos','notas'), true) ? array('data') : array();
}

function portalRepresentanteCampoPeriodo($config, $tipo)
{
    $padrao = in_array($tipo, array('boletos','cobrancas'), true) ? 'vencimento' : 'data';
    $fonte = isset($config['fontes'][$tipo]) ? $config['fontes'][$tipo] : array();
    if (!array_key_exists('campoPeriodo', $fonte)) return $padrao;
    if (!is_string($fonte['campoPeriodo']) || !in_array($fonte['campoPeriodo'], portalRepresentanteCamposPeriodo($tipo), true))
        throw new RepresentantePortalErro('Campo do período inválido na configuração do portal.', 503);
    return $fonte['campoPeriodo'];
}

// A apresentação por NF não determina a data do filtro. Essa escolha pertence
// exclusivamente a fontes.boletos.campoPeriodo e mantém vencimento por padrão.
function portalRepresentanteComissaoPorNota($config)
{
    return isset($config['layout']['visoes']['boletos']['naturezaFinanceira'])
        && $config['layout']['visoes']['boletos']['naturezaFinanceira'] === 'comissoes'
        && !empty($config['layout']['visoes']['boletos']['comissaoPorNota']);
}

function portalRepresentanteIdentidadePortal($config = array())
{
    $portal = isset($config['layout']['portal']) ? $config['layout']['portal'] : array();
    $prefixo = isset($portal['prefixo']) ? trim($portal['prefixo']) : 'Portal do';
    $titulo = isset($portal['titulo']) ? trim($portal['titulo']) : 'Representante';
    return array('prefixo' => $prefixo, 'titulo' => $titulo, 'nome' => trim($prefixo . ' ' . $titulo),
        'personalizado' => isset($config['layout']['portal']));
}

// Fragmentos delimitados: não podem fechar a expressão e substituir os filtros
// do motor. A transação de consulta permanece READ ONLY com limite de duração.
function portalRepresentanteValidarFragmento($valor, $de = false)
{
    $sql = portalRepresentanteSqlTexto($valor);
    if ($sql === '' || strlen($sql) > 100000 || preg_match('/[;#@\\\\]|--|\/\*|\*\//', $sql)) return false;
    $nivel = 0; $aspas = ''; $tokens = ''; $superficie = '';
    for ($i = 0, $n = strlen($sql); $i < $n; $i++) {
        $c = $sql[$i];
        if ($aspas !== '') {
            if ($c === $aspas) {
                if ($i + 1 < $n && $sql[$i + 1] === $aspas) { $i++; continue; }
                $aspas = '';
            }
            continue;
        }
        if ($c === "'" || $c === '"') { $aspas = $c; $tokens .= ' '; continue; }
        if ($c === '`') return false;
        if ($c === '(') { $nivel++; $superficie .= ' '; }
        elseif ($c === ')') { if (--$nivel < 0) return false; }
        elseif ($nivel === 0) $superficie .= $c;
        $tokens .= $c;
    }
    if ($aspas !== '' || $nivel !== 0) return false;
    if (preg_match('/\b(INSERT|UPDATE|DELETE|REPLACE|DROP|ALTER|CREATE|TRUNCATE|RENAME|GRANT|REVOKE|CALL|EXECUTE|PREPARE|HANDLER|LOAD|OUTFILE|DUMPFILE|INTO|LOCK|UNLOCK|SLEEP|BENCHMARK|GET_LOCK|RELEASE_LOCK|LOAD_FILE|INFORMATION_SCHEMA|PERFORMANCE_SCHEMA|MYSQL|SYS|UNION)\b/i', $tokens)) return false;
    if (preg_match('/\b\w*(senha|password|secret|token|smtp)\w*\b/i', $tokens)) return false;
    if (preg_match('/\b(SELECT|FROM|WHERE|GROUP|HAVING|ORDER|LIMIT|OFFSET)\b/i', $superficie)
        || (!$de && (strpos($superficie, ',') !== false || preg_match('/\bAS\b/i', $superficie)))) return false;
    preg_match_all('/:([a-zA-Z][a-zA-Z0-9_]*)/', $sql, $parametros);
    return !array_diff($parametros[1], array('empresa', 'representante', 'inicio', 'fim', 'hoje'));
}

function portalRepresentanteCamposOpcionaisFonte($tipo)
{
    // Campos adicionais ficam no JSON da empresa; configurações existentes
    // continuam válidas quando não utilizam esses detalhes de apresentação.
    $campos = array('grupoClienteId' => '0', 'grupoCliente' => 'NULL');
    if (in_array($tipo, array('orcamentos','pedidos','notas'), true)) $campos['segmento'] = 'NULL';
    if ($tipo === 'orcamentos') return array_merge($campos, array('clienteNormal' => 'cliente', 'clienteNormalId' => 'clienteId',
        'clienteProposta' => 'NULL', 'clientePropostaId' => '0', 'cancelamentoReal' => '0', 'motivoCancelamento' => 'NULL'));
    if ($tipo === 'pedidos') return array_merge($campos, array('percentualSeparado' => 'NULL', 'numeroCarga' => 'NULL'));
    return in_array($tipo, array('notas','boletos','cobrancas','clientes'), true) ? $campos : array();
}

function portalRepresentanteCamposFonte($tipo, $incluirOpcionais = false)
{
    $comuns = array('id','numero','data','cliente','valor','status','excluido','clienteId','categoria');
    $extras = array(
        'orcamentos' => array('origem','convertido'),
        'pedidos' => array('origem','comNota','entrega','pendenteDireto','prazoVencido'),
        'notas' => array('origem','recebido','saldo','cobrancas'),
        'boletos' => array('saldo','recebido','vencimento','pagamento','nota','nossoNumero','tipoCobranca','notaId'),
        'cobrancas' => array('saldo','recebido','vencimento','pagamento','nota','nossoNumero','tipoCobranca','notaId'),
    );
    if ($tipo === 'clientes') return array_merge(array('id','cliente','ativo','uf','ufMunicipio','ibge','cidade','endereco','numero','bairro','cep'),
        $incluirOpcionais ? array_keys(portalRepresentanteCamposOpcionaisFonte($tipo)) : array());
    return isset($extras[$tipo]) ? array_merge($comuns, $extras[$tipo], $incluirOpcionais ? array_keys(portalRepresentanteCamposOpcionaisFonte($tipo)) : array()) : array();
}

function portalRepresentanteListaConfig($valor, $permitidos, $completa = false)
{
    return is_array($valor) && array_values($valor) === $valor && count($valor) > 0
        && count(array_filter($valor, 'is_string')) === count($valor)
        && count(array_unique($valor)) === count($valor) && !array_diff($valor, $permitidos)
        && (!$completa || count($valor) === count($permitidos));
}

function portalRepresentanteValidarConfiguracao($config, &$erros = array())
{
    $erros = array();
    if (!is_array($config)) { $erros[] = 'Informe um objeto JSON.'; return false; }
    if (!isset($config['versao']) || $config['versao'] !== 1) $erros[] = 'A versão deve ser 1.';
    if (!isset($config['habilitado']) || !is_bool($config['habilitado'])) $erros[] = 'habilitado deve ser true ou false.';
    if (isset($config['impressoes']) && (!is_array($config['impressoes'])
        || array_diff(array_keys($config['impressoes']), array('disponibilidadePedido'))
        || (isset($config['impressoes']['disponibilidadePedido']) && (!is_string($config['impressoes']['disponibilidadePedido'])
            || !preg_match('/^[a-f0-9]{32}$/D', $config['impressoes']['disponibilidadePedido'])))))
        $erros[] = 'impressoes.disponibilidadePedido deve conter o identificador da tela de impressão (32 caracteres hexadecimais).';
    $tipos = array('orcamentos','pedidos','notas','boletos','cobrancas','clientes');
    if (!isset($config['fontes']) || !is_array($config['fontes']) || array_diff(array_keys($config['fontes']), $tipos)) $erros[] = 'Fontes inválidas.';
    foreach ($tipos as $tipo) {
        $f = isset($config['fontes'][$tipo]) ? $config['fontes'][$tipo] : array();
        if (!is_array($f)) { $erros[] = "Fonte $tipo inválida."; continue; }
        if (array_key_exists('campoPeriodo', $f) && (!is_string($f['campoPeriodo'])
            || !in_array($f['campoPeriodo'], portalRepresentanteCamposPeriodo($tipo), true)))
            $erros[] = "Fonte $tipo: campoPeriodo deve indicar um campo de data permitido da fonte.";
        foreach (array('empresa', 'representante') as $chave) {
            $campo = isset($f['escopo'][$chave]) ? $f['escopo'][$chave] : null;
            if (!is_string($campo) || !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*\.[a-zA-Z][a-zA-Z0-9_]*$/D', $campo)) $erros[] = "Fonte $tipo: escopo.$chave exige alias.campo.";
        }
        $campos = portalRepresentanteCamposFonte($tipo);
        $permitidos = portalRepresentanteCamposFonte($tipo, true);
        if (!isset($f['campos']) || !is_array($f['campos']) || array_diff($campos, array_keys($f['campos'])) || array_diff(array_keys($f['campos']), $permitidos)) $erros[] = "Fonte $tipo: mantenha os campos " . implode(', ', $campos) . '.';
        foreach (isset($f['campos']) && is_array($f['campos']) ? $f['campos'] : array() as $nome => $valor) {
            if (!portalRepresentanteValidarFragmento($valor)) $erros[] = "Fonte $tipo, campo $nome: expressão SQL inválida ou não permitida.";
        }
        foreach (array('de', 'condicao') as $chave) if (!isset($f[$chave]) || !portalRepresentanteValidarFragmento($f[$chave], $chave === 'de')) $erros[] = "Fonte $tipo: $chave inválida.";
    }
    $layout = isset($config['layout']) && is_array($config['layout']) ? $config['layout'] : array();
    if (array_key_exists('portal', $layout)) {
        $portal = $layout['portal'];
        if (!is_array($portal) || array_diff(array_keys($portal), array('prefixo','titulo'))) $erros[] = 'layout.portal deve conter prefixo e titulo.';
        foreach (array('prefixo','titulo') as $campo) {
            $valor = is_array($portal) && isset($portal[$campo]) ? $portal[$campo] : null;
            if (!is_string($valor) || strlen($valor) > 160 || preg_match('//u', $valor) !== 1
                || preg_match('/[\x00-\x1F\x7F]/u', $valor) || ($campo === 'titulo' && trim($valor) === ''))
                $erros[] = "layout.portal.$campo deve ser um texto válido de até 160 caracteres.";
        }
    }
    $visoes = array('inicio','orcamentos','pedidos','notas','boletos','regioes');
    if (!isset($layout['menu']) || !portalRepresentanteListaConfig($layout['menu'], array_merge($visoes, array('comparativo')))
        || array_diff($visoes, $layout['menu'])) $erros[] = 'layout.menu deve conter as seis visões e, opcionalmente, comparativo.';
    if (isset($layout['menu']) && is_array($layout['menu']) && in_array('comparativo', $layout['menu'], true)) $visoes[] = 'comparativo';
    if (!isset($layout['titulo']) || !is_string($layout['titulo']) || trim($layout['titulo']) === '' || strlen($layout['titulo']) > 160) $erros[] = 'Informe layout.titulo com até 160 caracteres.';
    foreach (array('destaque','destaqueFinal','fundo','primaria') as $cor) if (!isset($layout['tema'][$cor]) || !is_string($layout['tema'][$cor]) || !preg_match('/^#[a-fA-F0-9]{6}$/D', $layout['tema'][$cor])) $erros[] = "layout.tema.$cor deve usar #RRGGBB.";
    foreach ($visoes as $tipo) {
        $v = isset($layout['visoes'][$tipo]) && is_array($layout['visoes'][$tipo]) ? $layout['visoes'][$tipo] : array();
        $textos = in_array($tipo, array('inicio','regioes','comparativo'), true) ? array('titulo','descricao') : array('titulo','descricao','evolucao','distribuicao');
        foreach ($textos as $texto) if (!isset($v[$texto]) || !is_string($v[$texto]) || strlen($v[$texto]) > 300 || ($texto === 'titulo' && trim($v[$texto]) === '')) $erros[] = "layout.visoes.$tipo.$texto inválido.";
        if (isset($v['naturezaFinanceira']) && ($tipo !== 'boletos' || !in_array($v['naturezaFinanceira'], array('recebimentos','comissoes'), true))) $erros[] = 'naturezaFinanceira aceita recebimentos ou comissoes somente em boletos.';
        if (isset($v['comissaoPorNota']) && ($tipo !== 'boletos' || !is_bool($v['comissaoPorNota'])
            || ($v['comissaoPorNota'] && (!isset($v['naturezaFinanceira']) || $v['naturezaFinanceira'] !== 'comissoes'))))
            $erros[] = 'comissaoPorNota exige naturezaFinanceira comissoes em boletos.';
        if (isset($v['exibirClienteProposta']) && ($tipo !== 'orcamentos' || !is_bool($v['exibirClienteProposta'])))
            $erros[] = 'exibirClienteProposta deve ser booleano em orcamentos.';
        if (in_array($tipo, array('inicio','regioes','comparativo'), true)) continue;
        if (isset($v['tipoGrafico']) && !in_array($v['tipoGrafico'], array('linha','barra'), true)) $erros[] = "layout.visoes.$tipo.tipoGrafico inválido.";
        if (!isset($v['blocos']) || !portalRepresentanteListaConfig($v['blocos'], array('evolucao','distribuicao','clientes','acompanhamento','segmentos','cancelamentos'))) $erros[] = "layout.visoes.$tipo.blocos inválido.";
        if (!isset($v['proporcaoGraficos']) || !is_int($v['proporcaoGraficos']) || $v['proporcaoGraficos'] < 35 || $v['proporcaoGraficos'] > 75) $erros[] = "layout.visoes.$tipo.proporcaoGraficos deve ser um inteiro de 35 a 75.";
    }
    $inicio = isset($layout['inicio']) && is_array($layout['inicio']) ? $layout['inicio'] : array();
    if (isset($inicio['series']) && (!portalRepresentanteListaConfig($inicio['series'], array('orcamentos','pedidos','notas')) || count($inicio['series']) !== 2)) $erros[] = 'layout.inicio.series deve conter duas visões diferentes: orcamentos, pedidos ou notas.';
    if (!isset($inicio['blocos']) || !portalRepresentanteListaConfig($inicio['blocos'], array('indicadores','evolucao','desempenho','pendencias','clientes','mapa','segmentos','cancelamentos'))) $erros[] = 'layout.inicio.blocos inválido.';
    if (isset($layout['coresSeries'])) {
        if (!is_array($layout['coresSeries']) || array_diff(array_keys($layout['coresSeries']), array('orcamentos','pedidos','notas','boletos','recebido'))) $erros[] = 'layout.coresSeries inválido.';
        else foreach ($layout['coresSeries'] as $cor) if (!is_string($cor) || !preg_match('/^#[a-fA-F0-9]{6}$/D', $cor)) $erros[] = 'layout.coresSeries deve usar #RRGGBB.';
    }
    if (!isset($inicio['proporcaoGraficos']) || !is_int($inicio['proporcaoGraficos']) || $inicio['proporcaoGraficos'] < 35 || $inicio['proporcaoGraficos'] > 75) $erros[] = 'layout.inicio.proporcaoGraficos deve ser um inteiro de 35 a 75.';
    foreach (array('evolucao','desempenho','pendencias','clientes') as $nome) if (!isset($inicio['titulos'][$nome]) || !is_string($inicio['titulos'][$nome]) || strlen($inicio['titulos'][$nome]) > 300) $erros[] = "layout.inicio.titulos.$nome inválido.";
    return !$erros;
}

function portalRepresentanteLerConfiguracao($empresa, $conf = null, $exigirHabilitado = true)
{
    $arquivos = array(portalRepresentanteArquivoConfiguracao($empresa, $conf));
    if ((int) $empresa !== 0) $arquivos[] = portalRepresentanteArquivoConfiguracao(0, $conf);
    $arquivos[] = portalRepresentanteArquivoPadrao();
    foreach (array_unique($arquivos) as $arquivo) {
        if ($arquivo === '' || !file_exists($arquivo)) continue;
        $texto = is_readable($arquivo) && filesize($arquivo) <= 1048576 ? file_get_contents($arquivo) : '';
        $config = json_decode($texto, true); $erros = array();
        if (!portalRepresentanteValidarConfiguracao($config, $erros)) throw new RepresentantePortalErro('A configuração do Portal do Representante está inválida. Solicite a revisão em Configuração > Personalização.', 503);
        if ($exigirHabilitado && !$config['habilitado']) throw new RepresentantePortalErro('O painel do representante está desabilitado para esta empresa.', 403);
        return $config;
    }
    throw new RepresentantePortalErro('Configuração do Portal do Representante não encontrada.', 503);
}

function portalRepresentanteSalvarConfiguracao($empresa, $texto, $conf = null)
{
    $config = is_string($texto) && strlen($texto) <= 1048576 ? json_decode($texto, true) : null;
    if (!portalRepresentanteValidarConfiguracao($config, $erros)) throw new RepresentantePortalErro(implode(' ', $erros));
    $arquivo = portalRepresentanteArquivoConfiguracao($empresa, $conf);
    if ($arquivo === '') throw new RepresentantePortalErro('Empresa ou instalação não informada.');
    $dir = dirname($arquivo);
    if (!is_dir($dir) && !@mkdir($dir, 0750, true)) throw new RepresentantePortalErro('Não foi possível criar o diretório de personalização.', 503);
    $temporario = tempnam($dir, '.portal-');
    if ($temporario === false) throw new RepresentantePortalErro('Não foi possível salvar a personalização.', 503);
    try {
        if (file_put_contents($temporario, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", LOCK_EX) === false || !chmod($temporario, 0640) || !rename($temporario, $arquivo)) throw new RepresentantePortalErro('Não foi possível salvar a personalização.', 503);
    } finally { if (is_file($temporario)) unlink($temporario); }
}

// Somente dados de apresentação chegam ao navegador; as fontes ficam no servidor.
function portalRepresentanteLayoutPublico($config)
{
    $l = $config['layout'];
    $publico = array('titulo' => $l['titulo'], 'menu' => $l['menu'], 'tema' => array_intersect_key($l['tema'], array_flip(array('destaque','destaqueFinal','fundo','primaria'))), 'visoes' => array());
    foreach ($l['menu'] as $tipo) {
        $publico['visoes'][$tipo] = array_intersect_key($l['visoes'][$tipo], array_flip(array('titulo','descricao','evolucao','distribuicao','blocos','proporcaoGraficos','naturezaFinanceira','comissaoPorNota','exibirClienteProposta','tipoGrafico')));
        if ($tipo === 'boletos' && array_key_exists('campoPeriodo', $config['fontes'][$tipo]))
            $publico['visoes'][$tipo]['campoPeriodo'] = portalRepresentanteCampoPeriodo($config, $tipo);
    }
    $publico['inicio'] = array('blocos' => $l['inicio']['blocos'], 'titulos' => array_intersect_key($l['inicio']['titulos'], array_flip(array('evolucao','desempenho','pendencias','clientes'))), 'proporcaoGraficos' => $l['inicio']['proporcaoGraficos']);
    $publico['inicio']['series'] = isset($l['inicio']['series']) ? $l['inicio']['series'] : array('pedidos','notas');
    $publico['coresSeries'] = isset($l['coresSeries']) ? $l['coresSeries'] : array();
    $publico['impressaoDisponibilidadePedido'] = !empty($config['impressoes']['disponibilidadePedido']);
    return $publico;
}
