<?php
namespace hardness;

function dashboardIndicadorListagemPaginadaHabilitada($indicador)
{
    $lista = isset($indicador['listagem']) ? $indicador['listagem'] : array();
    return (!isset($lista['habilitada']) || $lista['habilitada'])
        && !empty($lista['paginacao']['habilitada'])
        && dashboardPersonalizadoSqlTexto(isset($lista['sqlBase']) ? $lista['sqlBase'] : '') !== '';
}

function dashboardIndicadorConsultarListagemPaginada($conexao, $lista, $parametros, $entrada, &$erro, $completa = false)
{
    $erro = '';
    $sql = dashboardPersonalizadoSqlTexto(isset($lista['sqlBase']) ? $lista['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sql, $erro, false, !empty($lista['permitirUnionAll']))) return false;
    preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sql, $nomes);
    foreach (array_unique($nomes[1]) as $nome) {
        if (!array_key_exists($nome, $parametros)) { $erro = 'Filtro não configurado na tabela: ' . $nome . '.'; return false; }
    }
    if (!empty($lista['_barras'])) $sql = dashboardBarrasEmpilhadasFiltrarSql($sql, $lista, $parametros);
    if (isset($parametros['ufMapa']) && $parametros['ufMapa'] !== '') {
        $sql = 'SELECT dashboard_clientes.* FROM (' . $sql . ') AS dashboard_clientes WHERE dashboard_clientes.`uf` = :ufMapa';
    }
    $paginacao = isset($lista['paginacao']) ? $lista['paginacao'] : array();
    $tamanho = isset($paginacao['tamanhoPagina']) ? max(1, min(100, (int) $paginacao['tamanhoPagina'])) : 50;
    $offset = isset($entrada['listaOffset']) ? $entrada['listaOffset'] : '0';
    if (!is_scalar($offset) || !preg_match('/^[0-9]{1,9}$/', (string) $offset)) {
        $erro = 'Página da tabela inválida.';
        return false;
    }
    $offset = $completa ? 0 : (int) $offset;
    $chave = dashboardIndicadorIdentificador(isset($paginacao['campoChave']) ? $paginacao['campoChave'] : 'id', '');
    $ordem = dashboardIndicadorIdentificador(isset($lista['ordenacao']['campo']) ? $lista['ordenacao']['campo'] : 'valor', '');
    if ($chave === '' || $ordem === '') { $erro = 'Ordenação inválida na tabela.'; return false; }
    $campos = array();
    foreach ((isset($lista['rodapeTotais']['campos']) ? $lista['rodapeTotais']['campos'] : array()) as $campo) {
        $valido = dashboardIndicadorIdentificador($campo, '');
        if ($valido === '') { $erro = 'Coluna inválida no total da tabela.'; return false; }
        $campos[$valido] = $valido;
    }
    $resumo = 'COUNT(*) AS totalLinhas';
    $aliases = array();
    foreach (array_values($campos) as $indice => $campo) {
        $aliases['total' . $indice] = $campo;
        $resumo .= ', COALESCE(SUM(`' . $campo . '`), 0) AS total' . $indice;
    }
    $contagem = dashboardIndicadorConsultar($conexao, 'SELECT ' . $resumo . ' FROM (' . $sql . ') AS dashboard_lista_total', $parametros, $erro);
    if ($contagem === false) return false;
    $total = isset($contagem[0]['totalLinhas']) ? (int) $contagem[0]['totalLinhas'] : 0;
    $totais = array();
    foreach ($aliases as $alias => $campo) $totais[$campo] = isset($contagem[0][$alias]) ? (float) $contagem[0][$alias] : 0;
    $direcao = isset($lista['ordenacao']['direcao']) && strtoupper($lista['ordenacao']['direcao']) === 'ASC' ? 'ASC' : 'DESC';
    $ordenacao = ' ORDER BY `' . $ordem . '` ' . $direcao;
    if ($chave !== $ordem) $ordenacao .= ', `' . $chave . '` ASC';
    $consulta = 'SELECT * FROM (' . $sql . ') AS dashboard_lista_pagina' . $ordenacao;
    if (!$completa) $consulta .= ' LIMIT ' . $tamanho . ' OFFSET ' . $offset;
    $linhas = $total > 0 ? dashboardIndicadorConsultar($conexao, $consulta, $parametros, $erro) : array();
    if ($linhas === false) return false;
    return array(
        'linhas' => $linhas,
        'paginacao' => array('total' => $total, 'offset' => $offset, 'tamanhoPagina' => $tamanho,
            'quantidade' => count($linhas), 'temMais' => $offset + count($linhas) < $total, 'totais' => $totais)
    );
}
