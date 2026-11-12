<?php
namespace hardness;

require_once __DIR__ . '/../dashboard-personalizado/custom-indicator-engine.php';
require_once __DIR__ . '/graficos-config.php';
require_once __DIR__ . '/comercial-config.php';

function painelPersonalizadoArquivo($empresaId)
{
    global $g, $confUsuario;
    $path = !empty($g['pathDados']) ? $g['pathDados'] : '';
    if ($path === '' && !empty($confUsuario['dbDatabase'])) {
        $path = 'dados_usuarios/' . $confUsuario['dbDatabase'];
    }
    if ($path === '' || (int) $empresaId < 0) {
        return '';
    }
    if ($path[0] !== '/' && !empty($confUsuario['pathRaiz'])) {
        $path = rtrim($confUsuario['pathRaiz'], '/\\') . '/' . $path;
    }
    return rtrim($path, '/\\') . '/personalizacaoConf/' . (int) $empresaId . 'PAINEL PERSONALIZAVEL-alterarPainel.json';
}

function painelPersonalizadoArquivoEditor($empresaId)
{
    global $g;
    if ((int) $empresaId < 0) return '';
    if (!empty($g['modoDesenvolvedor'])) $path = 'ide/';
    elseif (!empty($g['modoDesenvolvedorApp'])) $path = Apps::getDirEditor();
    else $path = $g['pathDados'];
    return rtrim($path, '/\\') . '/personalizacaoConf/' . (int) $empresaId . 'PAINEL PERSONALIZAVEL-alterarPainel.json';
}

function painelPersonalizadoSql($painel)
{
    $sql = isset($painel['sqlBase']) ? $painel['sqlBase'] : '';
    if (is_array($sql)) {
        foreach ($sql as $linha) {
            if (!is_string($linha)) return '';
        }
        return trim(implode("\n", $sql));
    }
    return is_string($sql) ? trim($sql) : '';
}

function painelPersonalizadoValidarConfiguracao($configuracao, &$erros = array())
{
    $erros = array();
    if (!is_array($configuracao)) {
        $erros[] = 'Informe um objeto JSON.';
        return false;
    }
    if (array_key_exists('habilitado', $configuracao) && !is_bool($configuracao['habilitado'])) {
        $erros[] = 'habilitado deve ser true ou false.';
    }
    if (empty($configuracao['titulo']) || !is_string($configuracao['titulo'])) {
        $erros[] = 'Informe o título do painel.';
    }
    foreach (array('atualizacaoSegundos' => array(30, 3600), 'alternanciaSegundos' => array(5, 300), 'linhasPorPagina' => array(3, 15)) as $campo => $limites) {
        if (isset($configuracao[$campo]) && (!is_int($configuracao[$campo]) || $configuracao[$campo] < $limites[0] || $configuracao[$campo] > $limites[1])) {
            $erros[] = $campo . ' deve ser um inteiro entre ' . $limites[0] . ' e ' . $limites[1] . '.';
        }
    }
    if (!isset($configuracao['paineis']) || !is_array($configuracao['paineis']) || count($configuracao['paineis']) > 20) {
        $erros[] = 'Informe uma lista de até 20 painéis.';
        return false;
    }
    $ids = array();
    $ativos = 0;
    foreach (array_merge(array($configuracao), $configuracao['paineis']) as $item) {
        if (!is_array($item)) continue;
        foreach (array('perfisPermitidos', 'usuariosPermitidos') as $campo) {
            if (!isset($item[$campo])) continue;
            if (!is_array($item[$campo])) {
                $erros[] = $campo . ' deve ser uma lista de IDs numéricos.';
                continue;
            }
            foreach ($item[$campo] as $id) {
                if (!is_int($id) || $id <= 0) $erros[] = $campo . ' aceita apenas IDs inteiros positivos.';
            }
        }
    }
    foreach ($configuracao['paineis'] as $painel) {
        if (!is_array($painel)) {
            $erros[] = 'Cada painel deve ser um objeto.';
            continue;
        }
        $id = isset($painel['id']) && is_string($painel['id']) ? $painel['id'] : '';
        if (!preg_match('/^[a-z][a-z0-9_]{1,49}$/', $id) || isset($ids[$id])) {
            $erros[] = 'Cada painel deve ter um id único, com letras minúsculas, números ou sublinhado (2 a 50 caracteres).';
        }
        $ids[$id] = true;
        if (isset($painel['habilitado']) && !is_bool($painel['habilitado'])) $erros[] = $id . ': habilitado deve ser booleano.';
        if (isset($painel['habilitado']) && $painel['habilitado'] === false) continue;
        $ativos++;
        foreach (array('titulo', 'setor') as $campo) {
            if (empty($painel[$campo]) || !is_string($painel[$campo])) $erros[] = $id . ': informe ' . $campo . '.';
        }
        $tipo = isset($painel['tipo']) ? $painel['tipo'] : 'mensagem';
        if ($tipo === 'mensagem') {
            if (empty($painel['mensagem']) || !is_string($painel['mensagem'])) $erros[] = $id . ': informe a mensagem.';
        } elseif ($tipo === 'tabela' || $tipo === 'grafico' || $tipo === 'comercial') {
            if ($tipo === 'comercial') {
                painelComercialValidar($painel, $erros);
                if (array_key_exists('dados', $painel)) continue;
            }
            if ($tipo === 'grafico') {
                painelPersonalizadoValidarGrafico($painel, $erros);
                if (array_key_exists('dados', $painel)) continue;
            }
            $erroSql = '';
            if (!dashboardIndicadorValidarSql(painelPersonalizadoSql($painel), $erroSql, false, $tipo === 'comercial')) $erros[] = $id . ': ' . $erroSql;
            if ($tipo === 'tabela' && (empty($painel['colunas']) || !is_array($painel['colunas']) || count($painel['colunas']) > 8)) {
                $erros[] = $id . ': informe de 1 a 8 colunas.';
            } elseif ($tipo === 'tabela') {
                foreach ($painel['colunas'] as $coluna) {
                    if (!is_array($coluna) || empty($coluna['campo']) || !is_string($coluna['campo']) || dashboardIndicadorIdentificador($coluna['campo']) === '' || empty($coluna['titulo']) || !is_string($coluna['titulo'])) {
                        $erros[] = $id . ': coluna inválida.';
                        continue;
                    }
                    if (isset($coluna['formato']) && !in_array($coluna['formato'], array('texto', 'numero', 'moeda', 'data', 'status'), true)) $erros[] = $id . ': formato de coluna inválido.';
                }
            }
            $filtros = isset($painel['filtros']) ? $painel['filtros'] : array();
            if (isset($painel['ordenacao'])) {
                $ordenacao = $painel['ordenacao'];
                if (!is_array($ordenacao) || empty($ordenacao['campo']) || !is_string($ordenacao['campo']) || dashboardIndicadorIdentificador($ordenacao['campo']) === '' || !isset($ordenacao['direcao']) || !in_array($ordenacao['direcao'], array('ASC', 'DESC'), true)) {
                    $erros[] = $id . ': ordenacao exige campo e direcao ASC ou DESC.';
                }
            }
            $parametros = array('empresa' => 1);
            if (!is_array($filtros)) {
                $erros[] = $id . ': filtros deve ser uma lista.';
            } else {
                foreach ($filtros as $filtro) {
                    if (!is_array($filtro) || !isset($filtro['id']) || !is_string($filtro['id']) || !preg_match('/^[a-z][a-zA-Z0-9_]{1,39}$/', $filtro['id']) || isset($parametros[$filtro['id']]) || !isset($filtro['padrao']) || !is_scalar($filtro['padrao']) || !in_array(isset($filtro['tipo']) ? $filtro['tipo'] : 'texto', array('texto', 'data', 'numero'), true)) {
                        $erros[] = $id . ': filtro inválido; informe id, tipo e padrao. O parâmetro empresa é reservado.';
                    } else {
                        $parametros[$filtro['id']] = $filtro['padrao'];
                    }
                }
                if (empty($erros)) {
                    $errosFiltros = array();
                    dashboardIndicadorFiltros($painel, array(), $errosFiltros);
                    $erros = array_merge($erros, $errosFiltros);
                }
            }
            $valores = array();
            $tipos = $erroParametros = '';
            dashboardIndicadorPrepararSql(painelPersonalizadoSql($painel), $parametros, $valores, $tipos, $erroParametros);
            if ($erroParametros !== '') $erros[] = $id . ': ' . $erroParametros;
        } else {
            $erros[] = $id . ': tipo deve ser mensagem, tabela, grafico ou comercial.';
        }
    }
    if ((!array_key_exists('habilitado', $configuracao) || $configuracao['habilitado'] === true) && $ativos === 0) $erros[] = 'Habilite ao menos um painel.';
    return empty($erros);
}

function painelPersonalizadoConfiguracao($empresaId)
{
    if ((int) $empresaId <= 0) return false;
    foreach (array((int) $empresaId, 0) as $empresa) {
        $arquivo = painelPersonalizadoArquivo($empresa);
        if ($arquivo === '' || !file_exists($arquivo)) continue;
        if (!is_readable($arquivo)) return false;
        $config = json_decode(file_get_contents($arquivo), true);
        $erros = array();
        // Uma configuração específica inválida ou desabilitada não herda a geral.
        if (!painelPersonalizadoValidarConfiguracao($config, $erros)) return false;
        // Salvar um JSON válido habilita o painel, salvo desativação explícita.
        if (!array_key_exists('habilitado', $config)) $config['habilitado'] = true;
        if (!$config['habilitado']) return false;
        return $config;
    }
    // Sem uma personalização salva para a empresa ou para o grupo, não há painel ativo.
    return false;
}

function painelPersonalizadoPermitidos($config, $contexto)
{
    if (!$config || empty($contexto['usuario']) || empty($contexto['empresa'])) return array();
    return array_values(array_filter($config['paineis'], function ($painel) use ($config, $contexto) {
        if (isset($painel['habilitado']) && !$painel['habilitado']) return false;
        if (!empty($contexto['root']) || !empty($contexto['diretor'])) return true;
        $perfis = isset($painel['perfisPermitidos']) ? $painel['perfisPermitidos'] : (isset($config['perfisPermitidos']) ? $config['perfisPermitidos'] : array());
        $usuarios = isset($painel['usuariosPermitidos']) ? $painel['usuariosPermitidos'] : (isset($config['usuariosPermitidos']) ? $config['usuariosPermitidos'] : array());
        return in_array((int) $contexto['usuario'], $usuarios, true) || in_array((int) $contexto['perfil'], $perfis, true);
    }));
}
