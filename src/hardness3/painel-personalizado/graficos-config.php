<?php
namespace hardness;

function painelPersonalizadoValidarGrafico($painel, &$erros)
{
    $id = isset($painel['id']) && is_string($painel['id']) ? $painel['id'] : 'Gráfico';
    $grafico = isset($painel['grafico']) ? $painel['grafico'] : null;
    if (!is_array($grafico)) {
        $erros[] = $id . ': informe a definição grafico.';
        return;
    }
    if (!isset($grafico['tipo']) || !in_array($grafico['tipo'], array('barras', 'barras_horizontais', 'linhas', 'rosca'), true)) $erros[] = $id . ': grafico.tipo deve ser barras, barras_horizontais, linhas ou rosca.';
    foreach (array('subtitulo' => 220, 'rotuloTotal' => 30) as $campo => $limite) {
        if (isset($grafico[$campo]) && (!is_string($grafico[$campo]) || strlen($grafico[$campo]) > $limite * 4)) $erros[] = $id . ': ' . $campo . ' deve ser um texto curto.';
    }
    if (isset($grafico['coresCategorias'])) {
        if (!is_array($grafico['coresCategorias']) || count($grafico['coresCategorias']) > 12) $erros[] = $id . ': coresCategorias deve mapear até 12 categorias para cores.';
        else foreach ($grafico['coresCategorias'] as $cor) {
            if (!is_string($cor) || !preg_match('/^#[a-fA-F0-9]{6}$/', $cor)) $erros[] = $id . ': coresCategorias aceita apenas cores #RRGGBB.';
        }
    }
    if (empty($grafico['campoCategoria']) || !is_string($grafico['campoCategoria']) || dashboardIndicadorIdentificador($grafico['campoCategoria']) === '') $erros[] = $id . ': informe um campoCategoria válido.';
    if (isset($grafico['formato']) && !in_array($grafico['formato'], array('numero', 'moeda', 'percentual'), true)) $erros[] = $id . ': formato deve ser numero, moeda ou percentual.';
    if (isset($grafico['categoriasPorPagina']) && (!is_int($grafico['categoriasPorPagina']) || $grafico['categoriasPorPagina'] < 3 || $grafico['categoriasPorPagina'] > 12)) $erros[] = $id . ': categoriasPorPagina deve ser um inteiro entre 3 e 12.';
    if (empty($grafico['series']) || !is_array($grafico['series']) || count($grafico['series']) > 4) {
        $erros[] = $id . ': informe de 1 a 4 séries para o gráfico.';
        return;
    }
    if (isset($grafico['tipo']) && $grafico['tipo'] === 'rosca' && count($grafico['series']) !== 1) $erros[] = $id . ': o gráfico de rosca aceita uma única série.';
    $campos = array();
    foreach ($grafico['series'] as $serie) {
        if (!is_array($serie) || empty($serie['campo']) || !is_string($serie['campo']) || dashboardIndicadorIdentificador($serie['campo']) === '' || empty($serie['titulo']) || !is_string($serie['titulo'])) {
            $erros[] = $id . ': cada série exige campo válido e titulo.';
            continue;
        }
        if (isset($campos[$serie['campo']]) || (isset($grafico['campoCategoria']) && $serie['campo'] === $grafico['campoCategoria'])) $erros[] = $id . ': os campos das séries e da categoria devem ser distintos.';
        $campos[$serie['campo']] = true;
        if (isset($serie['cor']) && (!is_string($serie['cor']) || !preg_match('/^#[a-fA-F0-9]{6}$/', $serie['cor']))) $erros[] = $id . ': informe a cor no formato #RRGGBB.';
    }
    $camposSeries = $campos;
    if (isset($grafico['camposComplementares'])) {
        if (!is_array($grafico['camposComplementares']) || count($grafico['camposComplementares']) > 8 || array_values($grafico['camposComplementares']) !== $grafico['camposComplementares']) $erros[] = $id . ': camposComplementares deve ser uma lista de até oito campos numéricos.';
        else foreach ($grafico['camposComplementares'] as $campo) {
            if (!is_string($campo) || dashboardIndicadorIdentificador($campo) === '' || isset($campos[$campo]) || (isset($grafico['campoCategoria']) && $campo === $grafico['campoCategoria'])) $erros[] = $id . ': campo complementar inválido ou repetido.';
            elseif (is_string($campo)) $campos[$campo] = true;
        }
    }
    if (isset($grafico['detalhesCategoria'])) {
        if (!isset($grafico['tipo']) || $grafico['tipo'] !== 'barras_horizontais' || !is_array($grafico['detalhesCategoria']) || count($grafico['detalhesCategoria']) > 2 || array_values($grafico['detalhesCategoria']) !== $grafico['detalhesCategoria']) $erros[] = $id . ': detalhesCategoria aceita até dois detalhes em barras_horizontais.';
        else foreach ($grafico['detalhesCategoria'] as $detalhe) {
            if (!is_array($detalhe) || empty($detalhe['campo']) || !is_string($detalhe['campo']) || !isset($campos[$detalhe['campo']]) || empty($detalhe['titulo']) || !is_string($detalhe['titulo']) || strlen($detalhe['titulo']) > 160) $erros[] = $id . ': detalhe exige campo numérico declarado e titulo curto.';
            if (isset($detalhe['formato']) && !in_array($detalhe['formato'], array('numero', 'moeda', 'percentual', 'duracao'), true)) $erros[] = $id . ': formato de detalhe inválido.';
        }
    }
    if (isset($grafico['detalhesEmColunas'])) {
        if (!is_bool($grafico['detalhesEmColunas']) || !isset($grafico['tipo']) || $grafico['tipo'] !== 'barras_horizontais') $erros[] = $id . ': detalhesEmColunas deve ser booleano e usado em barras_horizontais.';
        elseif ($grafico['detalhesEmColunas'] && empty($grafico['detalhesCategoria'])) $erros[] = $id . ': informe detalhesCategoria para exibir colunas.';
    }
    painelPersonalizadoValidarResumos($grafico, $campos, $id, $erros);
    if (!array_key_exists('dados', $painel)) return;
    if (array_key_exists('sqlBase', $painel)) $erros[] = $id . ': escolha dados no JSON ou sqlBase, sem combinar as duas fontes.';
    if (isset($painel['filtros']) || isset($painel['ordenacao'])) $erros[] = $id . ': filtros e ordenacao são usados apenas com sqlBase; ordene os dados na própria lista JSON.';
    if (!is_array($painel['dados']) || count($painel['dados']) > 200 || array_values($painel['dados']) !== $painel['dados']) {
        $erros[] = $id . ': dados deve ser uma lista de até 200 registros.';
        return;
    }
    foreach ($painel['dados'] as $linha) {
        if (!is_array($linha) || !isset($grafico['campoCategoria']) || !is_string($grafico['campoCategoria'])) {
            $erros[] = $id . ': registro de gráfico inválido.';
            break;
        }
        $categoria = isset($linha[$grafico['campoCategoria']]) ? $linha[$grafico['campoCategoria']] : null;
        if (!is_string($categoria) && !is_numeric($categoria)) $erros[] = $id . ': cada registro deve informar a categoria.';
        foreach ($campos as $campo => $usado) {
            $valor = isset($linha[$campo]) ? $linha[$campo] : null;
            if (!is_numeric($valor) || !is_finite((float) $valor)) $erros[] = $id . ': cada série exige valores numéricos finitos nos dados.';
            elseif (isset($camposSeries[$campo]) && isset($grafico['tipo']) && in_array($grafico['tipo'], array('rosca', 'barras_horizontais'), true) && (float) $valor < 0) $erros[] = $id . ': rosca e barras horizontais empilhadas não aceitam valores negativos.';
        }
    }
}

function painelPersonalizadoValidarResumos($grafico, $campos, $id, &$erros)
{
    if (!isset($grafico['resumos'])) return;
    if (!is_array($grafico['resumos']) || count($grafico['resumos']) > 4 || array_values($grafico['resumos']) !== $grafico['resumos']) {
        $erros[] = $id . ': resumos deve ser uma lista de até quatro indicadores.';
        return;
    }
    foreach ($grafico['resumos'] as $resumo) {
        if (!is_array($resumo) || empty($resumo['titulo']) || !is_string($resumo['titulo']) || strlen($resumo['titulo']) > 240) {
            $erros[] = $id . ': cada resumo precisa de um titulo curto.';
            continue;
        }
        $calculo = isset($resumo['calculo']) ? $resumo['calculo'] : 'soma';
        if (!in_array($calculo, array('soma', 'media', 'percentual', 'pico'), true)) $erros[] = $id . ': calculo do resumo deve ser soma, media, percentual ou pico.';
        if (isset($resumo['formato']) && !in_array($resumo['formato'], array('numero', 'moeda', 'percentual', 'duracao'), true)) $erros[] = $id . ': formato do resumo inválido.';
        foreach (array_merge(array('campos'), $calculo === 'percentual' ? array('camposTotal') : array()) as $campoLista) {
            if (empty($resumo[$campoLista]) || !is_array($resumo[$campoLista]) || count($resumo[$campoLista]) > 4 || array_values($resumo[$campoLista]) !== $resumo[$campoLista]) {
                $erros[] = $id . ': informe uma lista de séries em ' . $campoLista . '.';
                continue;
            }
            $vistos = array();
            foreach ($resumo[$campoLista] as $campo) {
                if (!is_string($campo) || !isset($campos[$campo]) || isset($vistos[$campo])) $erros[] = $id . ': o resumo deve usar campos numéricos declarados, sem repetições.';
                if (is_string($campo)) $vistos[$campo] = true;
            }
        }
        if (isset($resumo['categorias'])) {
            if (!is_array($resumo['categorias']) || !$resumo['categorias'] || count($resumo['categorias']) > 12 || array_values($resumo['categorias']) !== $resumo['categorias']) $erros[] = $id . ': categorias do resumo deve ser uma lista de 1 a 12 rótulos.';
            else foreach ($resumo['categorias'] as $categoria) {
                if (!is_string($categoria)) $erros[] = $id . ': categorias do resumo aceita apenas textos.';
            }
        }
        if (isset($resumo['descricao']) && (!is_string($resumo['descricao']) || strlen($resumo['descricao']) > 480)) $erros[] = $id . ': descricao do resumo deve ser um texto curto.';
        if (isset($resumo['cor']) && (!is_string($resumo['cor']) || !preg_match('/^#[a-fA-F0-9]{6}$/', $resumo['cor']))) $erros[] = $id . ': cor do resumo deve usar #RRGGBB.';
        if (isset($resumo['complemento'])) {
            if (!is_array($resumo['complemento']) || isset($resumo['complemento']['complemento'])) $erros[] = $id . ': complemento deve ser um indicador, sem outros complementos.';
            else painelPersonalizadoValidarResumos(array('resumos' => array($resumo['complemento'])), $campos, $id, $erros);
        }
    }
}

function painelPersonalizadoCamposDados($painel)
{
    if (isset($painel['tipo']) && $painel['tipo'] === 'comercial') return painelComercialCamposDados();
    if (isset($painel['tipo']) && $painel['tipo'] === 'grafico') {
        return array_merge(array($painel['grafico']['campoCategoria']), array_column($painel['grafico']['series'], 'campo'), isset($painel['grafico']['camposComplementares']) ? $painel['grafico']['camposComplementares'] : array());
    }
    return array_column($painel['colunas'], 'campo');
}

function painelPersonalizadoResumoPublico($resumo)
{
    $campos = array_flip(array('titulo', 'descricao', 'cor', 'campos', 'calculo', 'camposTotal', 'categorias', 'formato'));
    $publico = array_intersect_key($resumo, $campos);
    if (isset($resumo['complemento'])) $publico['complemento'] = array_intersect_key($resumo['complemento'], $campos);
    return $publico;
}

function painelPersonalizadoDefinicaoPublica($painel)
{
    $publico = array('id' => $painel['id'], 'titulo' => $painel['titulo'], 'setor' => $painel['setor'], 'tipo' => isset($painel['tipo']) ? $painel['tipo'] : 'mensagem');
    if ($publico['tipo'] === 'mensagem') {
        $publico['mensagem'] = $painel['mensagem'];
    } elseif ($publico['tipo'] === 'comercial') {
        foreach (array('referencia', 'demonstracao') as $campo) {
            if (isset($painel[$campo])) $publico[$campo] = $painel[$campo];
        }
    } elseif ($publico['tipo'] === 'grafico') {
        $grafico = $painel['grafico'];
        $publico['grafico'] = array('tipo' => $grafico['tipo'], 'campoCategoria' => $grafico['campoCategoria'], 'formato' => isset($grafico['formato']) ? $grafico['formato'] : 'numero', 'categoriasPorPagina' => isset($grafico['categoriasPorPagina']) ? $grafico['categoriasPorPagina'] : 8, 'series' => array());
        foreach ($grafico['series'] as $serie) {
            $publico['grafico']['series'][] = array_intersect_key($serie, array_flip(array('campo', 'titulo', 'cor')));
        }
        foreach (array('subtitulo', 'rotuloTotal', 'coresCategorias', 'detalhesEmColunas') as $campo) {
            if (isset($grafico[$campo])) $publico['grafico'][$campo] = $grafico[$campo];
        }
        if (isset($grafico['resumos'])) {
            $publico['grafico']['resumos'] = array_map(__NAMESPACE__ . '\\painelPersonalizadoResumoPublico', $grafico['resumos']);
        }
        if (isset($grafico['detalhesCategoria'])) {
            $publico['grafico']['detalhesCategoria'] = array_map(function ($detalhe) {
                return array_intersect_key($detalhe, array_flip(array('campo', 'titulo', 'formato')));
            }, $grafico['detalhesCategoria']);
        }
    } else {
        $publico['colunas'] = array();
        foreach ($painel['colunas'] as $coluna) {
            $publico['colunas'][] = array('campo' => $coluna['campo'], 'titulo' => $coluna['titulo'], 'formato' => isset($coluna['formato']) ? $coluna['formato'] : 'texto');
        }
    }
    return $publico;
}
