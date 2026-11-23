<?php
namespace hardness;

function dashboardTendenciaSeries($grafico)
{
    return !empty($grafico['seriesSelecionaveis']['habilitado']) && isset($grafico['seriesSelecionaveis']['series'])
        && is_array($grafico['seriesSelecionaveis']['series']) ? $grafico['seriesSelecionaveis']['series'] : array();
}

function dashboardTendenciaSeriesValidar($grafico, &$erro)
{
    if (empty($grafico['seriesSelecionaveis']['habilitado'])) return true;
    $series = dashboardTendenciaSeries($grafico);
    if (!$series || count($series) > 8 || !empty($grafico['tipo']) || ($grafico['apresentacao'] ?? '') !== 'padrao'
        || ($grafico['calculo'] ?? 'soma') !== 'soma') {
        $erro = 'Configure de uma a oito séries para a tendência padrão por soma.'; return false;
    }
    $ids = array();
    foreach ($series as $serie) {
        foreach (array('id', 'titulo', 'cor', 'campoValor') as $campo) if (!isset($serie[$campo]) || !is_string($serie[$campo])) {
            $erro = 'Os identificadores, títulos, cores e campos das séries devem ser textos.'; return false;
        }
        if (isset($serie['campoQuantidade']) && !is_string($serie['campoQuantidade'])) { $erro = 'Campo de quantidade inválido na série.'; return false; }
        $id = isset($serie['id']) ? dashboardIndicadorIdentificador($serie['id']) : '';
        if ($id === '' || isset($ids[$id]) || !is_string($serie['titulo'] ?? null) || trim($serie['titulo']) === ''
            || strlen($serie['titulo']) > 120 || !preg_match('/^#[a-f0-9]{6}$/i', $serie['cor'] ?? '')
            || dashboardIndicadorIdentificador($serie['campoValor'] ?? '') === ''
            || (!empty($grafico['campoQuantidade']) && dashboardIndicadorIdentificador($serie['campoQuantidade'] ?? '') === '')) {
            $erro = 'Série da tendência inválida: confira identificador, título, cor e campos.'; return false;
        }
        $ids[$id] = true;
    }
    return true;
}

// Agrega todas as opções na mesma leitura fiscal. Marcar opções não consulta o banco.
function dashboardTendenciaSeriesSql($grafico)
{
    $pares = array();
    foreach (dashboardTendenciaSeries($grafico) as $serie) {
        $id = dashboardIndicadorIdentificador($serie['id'] ?? '');
        $valor = dashboardIndicadorIdentificador($serie['campoValor'] ?? '');
        $quantidade = dashboardIndicadorIdentificador($serie['campoQuantidade'] ?? '');
        if ($id === '' || $valor === '') continue;
        $pares[] = "'{$id}', JSON_OBJECT('valor', COALESCE(SUM(`{$valor}`), 0)"
            . ($quantidade === '' ? '' : ", 'quantidade', COALESCE(SUM(`{$quantidade}`), 0)") . ')';
    }
    return $pares ? ', JSON_OBJECT(' . implode(', ', $pares) . ') AS series' : '';
}

function dashboardTendenciaSeriesCampos($campos, $grafico)
{
    if (dashboardTendenciaSeries($grafico)) $campos[] = 'series';
    return $campos;
}

function dashboardTendenciaSeriesNormalizar($linhas, $grafico, &$erro)
{
    $definicoes = dashboardTendenciaSeries($grafico);
    if (!$definicoes) return $linhas;
    foreach ($linhas as &$linha) {
        $series = isset($linha['series']) && is_string($linha['series']) ? json_decode($linha['series'], true) : ($linha['series'] ?? null);
        if (!is_array($series) || count($series) !== count($definicoes)) { $erro = 'Resposta incompleta das séries da tendência.'; return false; }
        foreach ($definicoes as $definicao) {
            $id = $definicao['id'];
            foreach (!empty($grafico['campoQuantidade']) ? array('valor', 'quantidade') : array('valor') as $campo) {
                if (!isset($series[$id][$campo]) || !is_numeric($series[$id][$campo]) || !is_finite((float) $series[$id][$campo])) {
                    $erro = 'Valor inválido na série da tendência.'; return false;
                }
                $series[$id][$campo] = (float) $series[$id][$campo];
            }
        }
        $linha['series'] = $series;
    }
    unset($linha);
    return $linhas;
}
