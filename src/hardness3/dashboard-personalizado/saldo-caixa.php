<?php
namespace hardness;

function dashboardSaldoCaixaHabilitado($indicador)
{
    return !empty($indicador['saldoCaixa']['habilitado']);
}

function dashboardSaldoCaixaPreparar($conexao, &$indicador, $empresa, &$filtros, &$erro)
{
    $valores = &$filtros['valores'];
    $empresaSelecionada = dashboardCaixaEmpresaSelecionada(isset($indicador['previsaoCaixa']) ? $indicador['previsaoCaixa'] : array(), isset($valores['abrangenciaEmpresa']) ? $valores['abrangenciaEmpresa'] : 'atual', $erro);
    if ($empresaSelecionada === false) return false;
    $empresas = array((int) $empresa);
    if (isset($valores['abrangenciaEmpresa']) && $valores['abrangenciaEmpresa'] === 'todas') {
        $empresas = isset($indicador['previsaoCaixa']['empresas']) ? array_values(array_unique(array_filter(array_map('intval', $indicador['previsaoCaixa']['empresas']), function ($id) { return $id > 0; }))) : array();
        if (!$empresas) { $erro = 'As empresas do fluxo de caixa não estão configuradas.'; return false; }
    }
    if ($empresaSelecionada !== null) $empresas = array($empresaSelecionada);
    $inicio = isset($valores['dataInicio']) ? $valores['dataInicio'] : '';
    $data = \DateTime::createFromFormat('!Y-m-d', $inicio);
    if (!$data || $data->format('Y-m-d') !== $inicio) { $erro = 'Informe a data inicial do fluxo.'; return false; }
    $saldoAtual = !empty($indicador['saldoCaixa']['usarSaldoAtual']) && $inicio === date('Y-m-d');
    $dataCorte = $saldoAtual ? (clone $data)->modify('+1 day')->format('Y-m-d') : $inicio;
    // No fluxo de hoje, as baixas já registradas compõem o saldo da consulta.
    // Para outros períodos, mantém o saldo anterior à data inicial.
    $sql = "SELECT D007_Id AS contaId, D007_C004_Id AS empresa, D007_Nome_Banco AS conta,
        COALESCE(C004_Nome_Abreviado, CAST(D007_C004_Id AS CHAR)) AS nomeEmpresa,
        COALESCE((SELECT SUM(T019_Valor_Lancamento) FROM T019
            WHERE T019_D007_Id = D007_Id AND IFNULL(T019_Flag_Excluido, 'N') <> 'S'
            AND T019_Data_Lancamento < :dataInicio), 0) AS saldo
        FROM D007 LEFT JOIN C004 ON C004_Id = D007_C004_Id
        WHERE D007_Flag_Ativo = 'S' AND D007_Flag_Fluxo_Caixa = 'S'
        AND D007_C004_Id IN (" . implode(',', $empresas) . ") ORDER BY D007_C004_Id, D007_Nome_Banco, D007_Id LIMIT 501";
    $contas = dashboardIndicadorConsultar($conexao, $sql, array('dataInicio' => $dataCorte), $erro);
    if ($contas === false) return false;
    if (count($contas) > 500) { $erro = 'O fluxo excedeu 500 contas habilitadas.'; return false; }
    $porId = array(); $opcoes = array();
    foreach ($contas as $conta) {
        $id = (string) (int) $conta['contaId'];
        $porId[$id] = $conta;
        $opcoes[] = array('valor' => $id, 'titulo' => $conta['nomeEmpresa'] . ' · ' . $conta['conta']);
    }
    $opcoes[] = array('valor' => 'sem_conta', 'titulo' => 'Sem conta definida (previsões)');
    $selecao = isset($valores['contasCaixa']) ? (string) $valores['contasCaixa'] : 'todas';
    $ids = $selecao === 'todas' ? array_merge(array_keys($porId), array('sem_conta')) : ($selecao === 'nenhuma' ? array() : explode(',', $selecao));
    if (count($ids) > 501 || count(array_unique($ids)) !== count($ids)) { $erro = 'Seleção de contas inválida.'; return false; }
    $selecionadas = array(); $saldoCentavos = 0;
    foreach ($ids as $id) {
        if ($id === 'sem_conta') continue;
        if (!preg_match('/^[1-9][0-9]*$/', (string) $id) || !isset($porId[$id])) {
            $erro = 'Uma conta selecionada não está habilitada nas empresas aplicadas. Selecione novamente as contas.'; return false;
        }
        $conta = $porId[$id];
        $conta['saldo'] = round((float) $conta['saldo'], 2);
        $selecionadas[] = $conta;
        $saldoCentavos += (int) round($conta['saldo'] * 100);
    }
    foreach ($filtros['publicos'] as &$filtro) {
        if ($filtro['id'] === 'contasCaixa') {
            $filtro['componente'] = 'contas_caixa'; $filtro['opcoes'] = $opcoes; $filtro['valor'] = $selecao;
        }
    }
    unset($filtro);
    $valores['saldoInicial'] = $saldoCentavos / 100;
    $indicador['grafico']['saldoAcumulado']['saldoInicial'] = $valores['saldoInicial'];
    $indicador['grafico']['saldoAcumulado']['saldoBancario'] = array('dataInicio' => $inicio, 'contas' => $selecionadas,
        'semContasHabilitadas' => !$contas, 'semContaIncluida' => in_array('sem_conta', $ids, true),
        'saldoAtual' => $saldoAtual, 'consultadoEm' => date('d/m/Y H:i:s'));
    $indicador['_saldoCaixaIds'] = $ids;
    $indicador['_saldoCaixaEmpresas'] = $empresas;
    $indicador['_saldoCaixaTodasContas'] = $selecao === 'todas';
    $indicador['_saldoCaixaDataCorte'] = $dataCorte;
    return true;
}

function dashboardSaldoCaixaAceita($indicador, $linha)
{
    $id = isset($linha['contaId']) ? (string) (int) $linha['contaId'] : '0';
    if (in_array($id === '0' ? 'sem_conta' : $id, array_map('strval', $indicador['_saldoCaixaIds']), true)) return true;

    // A opção é exclusiva dos recebíveis em aberto na seleção Todas.
    // O saldo inicial, os movimentos registrados e as saídas mantêm o filtro bancário.
    if (empty($indicador['saldoCaixa']['incluirRecebiveisContasForaFluxo'])
        || empty($indicador['_saldoCaixaTodasContas']) || !empty($linha['_dataBancaria'])
        || $id === '0' || !isset($linha['serie'], $linha['situacao'], $linha['contaAtiva'], $linha['contaFluxoCaixa'], $linha['contaEmpresa'], $linha['empresa'])
        || $linha['serie'] !== 'receber' || $linha['situacao'] !== 'Em aberto'
        || $linha['contaAtiva'] !== 'S' || $linha['contaFluxoCaixa'] === 'S') return false;

    return in_array((int) $linha['empresa'], $indicador['_saldoCaixaEmpresas'], true)
        && in_array((int) $linha['contaEmpresa'], $indicador['_saldoCaixaEmpresas'], true);
}

function dashboardSaldoCaixaMovimentos($conexao, $indicador, $parametros, &$erro)
{
    $ids = array_values(array_filter($indicador['_saldoCaixaIds'], function ($id) { return ctype_digit((string) $id); }));
    if (!$ids) return array();
    // Movimentos já incorporados ao saldo não voltam às barras nem à linha projetada.
    if (!empty($indicador['_saldoCaixaDataCorte'])) {
        $parametros['dataInicio'] = max($parametros['dataInicio'], $indicador['_saldoCaixaDataCorte']);
        if ($parametros['dataInicio'] > $parametros['dataFim']) return array();
    }
    $sql = "SELECT T019_Id AS documento, T019_D007_Id AS contaId, D007_C004_Id AS empresa,
        D007_Nome_Banco AS descricao, T019_Data_Lancamento AS data, T019_Data_Lancamento AS dataVencimento,
        'Movimento bancário' AS situacao, 'Registrado no ERP' AS statusTituloDescricao,
        CASE WHEN T019_Valor_Lancamento >= 0 THEN 'receber' ELSE 'pagar' END AS serie,
        ABS(T019_Valor_Lancamento) AS valor, 0 AS diasUteisAdicionais
        FROM T019 INNER JOIN D007 ON D007_Id = T019_D007_Id
        WHERE T019_D007_Id IN (" . implode(',', array_map('intval', $ids)) . ")
        AND D007_C004_Id IN (" . implode(',', $indicador['_saldoCaixaEmpresas']) . ")
        AND D007_Flag_Ativo = 'S' AND D007_Flag_Fluxo_Caixa = 'S'
        AND IFNULL(T019_Flag_Excluido, 'N') <> 'S'
        AND T019_Data_Lancamento BETWEEN :dataInicio AND :dataFim
        AND ABS(T019_Valor_Lancamento) >= :valorMinimo
        ORDER BY T019_Data_Lancamento, T019_Id LIMIT 5001";
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($linhas === false) return false;
    if (count($linhas) > 5000) { $erro = 'Mais de 5.000 movimentos bancários. Reduza o período ou selecione menos contas.'; return false; }
    foreach ($linhas as &$linha) { $linha['_dataBancaria'] = true; $linha['documento'] = 'Movimento ' . $linha['documento']; }
    unset($linha);
    return $linhas;
}
