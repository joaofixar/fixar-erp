<?php
namespace hardness;

function dashboardAgendaSessaoValida($autorizacao, $sessao)
{
    return !empty($autorizacao['usuario']) && !empty($autorizacao['empresa'])
        && isset($sessao['Usuario_Atual'], $sessao['Empresa_Atual'])
        && (int) $sessao['Usuario_Atual'] === (int) $autorizacao['usuario']
        && (int) $sessao['Empresa_Atual'] === (int) $autorizacao['empresa'];
}

// Somente leitura: autoriza a abertura do cadastro nativo; não grava D085.
function dashboardAgendaContexto($conexao, $indicador, $autorizacao, $sessao, $entrada, &$erro, $edicao = false)
{
    $erro = '';
    if (!dashboardAgendaSessaoValida($autorizacao, $sessao)) {
        $erro = 'A sessão do ERP mudou ou expirou. Reabra o dashboard pela empresa atual do sistema.';
        return false;
    }
    $detalhe = isset($indicador['listagem']['detalhamento']) ? $indicador['listagem']['detalhamento'] : array();
    if (empty($detalhe['modal']) || (isset($detalhe['habilitado']) && !$detalhe['habilitado'])
        || ($edicao ? empty($detalhe['edicaoTitulo']['habilitado'])
            : (!isset($detalhe['agenda']['habilitarInclusao']) || $detalhe['agenda']['habilitarInclusao'] !== true))) {
        $erro = 'Esta ação não está habilitada neste indicador.';
        return false;
    }
    $campoId = $edicao ? 'tituloId' : 'agendaTituloId';
    $titulo = isset($entrada[$campoId]) && !is_array($entrada[$campoId]) ? (string) $entrada[$campoId] : '';
    $grupo = isset($entrada['detalheGrupo']) && !is_array($entrada['detalheGrupo']) ? trim((string) $entrada['detalheGrupo']) : '';
    if (!preg_match('/^[1-9][0-9]{0,9}$/', $titulo) || $grupo === '' || strlen($grupo) > 800) {
        $erro = 'Selecione um título válido no detalhamento do cliente.';
        return false;
    }
    $filtrosConfigurados = dashboardIndicadorCarregarOpcoesFiltros($conexao, isset($indicador['filtros']) ? $indicador['filtros'] : array(), (int) $autorizacao['empresa'], $erro);
    if ($filtrosConfigurados === false) return false;
    $indicador['filtros'] = $filtrosConfigurados;
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $errosFiltros);
    if (!empty($errosFiltros)) {
        $erro = implode(' ', $errosFiltros);
        return false;
    }
    $sqlBase = dashboardPersonalizadoSqlTexto(isset($detalhe['sqlBase']) ? $detalhe['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sqlBase, $erro)) return false;
    $campoGrupo = dashboardIndicadorIdentificador(isset($detalhe['campoGrupo']) ? $detalhe['campoGrupo'] : 'descricao', 'descricao');
    $parametros = array_merge($filtros['valores'], array(
        'empresa' => (int) $autorizacao['empresa'], 'detalheGrupo' => $grupo, 'agendaTituloId' => (int) $titulo,
    ));
    $sql = 'SELECT tituloId, clienteId, empresaTituloId FROM (' . $sqlBase . ') AS agenda_base'
        . " WHERE tituloId = :agendaTituloId AND COALESCE(NULLIF(`{$campoGrupo}`, ''), 'Não informado') = :detalheGrupo LIMIT 1";
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($linhas === false) return false;
    if (count($linhas) !== 1 || empty($linhas[0]['clienteId']) || empty($linhas[0]['empresaTituloId'])) {
        $erro = 'O título não está disponível para este cliente e os filtros selecionados.';
        return false;
    }
    $parametros = array(
        'titulo' => (int) $titulo, 'cliente' => (int) $linhas[0]['clienteId'],
        'empresaTitulo' => (int) $linhas[0]['empresaTituloId'],
        'empresaSessao' => (int) $autorizacao['empresa'], 'usuario' => (int) $autorizacao['usuario'],
        'rootSessao' => !empty($sessao['usuarioLogadoComPermissao']) ? 1 : 0,
    );
    // Permissões atuais do ERP: usuário ativo, empresa e Contas a Receber (35).
    $camposEdicao = $edicao ? 'T002_Data_Vencimento AS dataVencimento, T002_Flag_Status AS statusTitulo,' : '';
    $sql = "SELECT T002_Id AS tituloId, T002_D024_Id AS clienteId, T002_C004_Id AS empresaTituloId, {$camposEdicao}
        (SELECT MAX(D085_Id) FROM D085 WHERE D085_T002_Id = T002_Id) AS agendaId
        FROM T002
        JOIN C007 AS autor ON autor.C007_Id = :usuario AND autor.C007_Ativo = 'S'
        JOIN C004 ON C004_Id = T002_C004_Id AND C004_Flag_Ativo = 'S'
        WHERE T002_Id = :titulo AND T002_D024_Id = :cliente AND T002_C004_Id = :empresaTitulo
        AND IFNULL(T002_Flag_Cancelada, 'N') <> 'S'
        AND (T002_Data_Recebimento IS NULL OR T002_Data_Recebimento = '0000-00-00')
        AND IFNULL(T002_Valor_Saldo, 0) > 0 AND IFNULL(T002_T002_Id_Agrupado, 0) <= 0
        AND (autor.C007_Id = 63 OR autor.C007_Usuario_Suporte = 'S' OR :rootSessao = 1 OR (
            EXISTS (SELECT 1 FROM C019 JOIN C030 ON C030_C021_Id = C019_C021_Id
                WHERE C019_C007_Id = :usuario AND C019_C004_Id = :empresaTitulo
                AND C030_C029_Id = 35 AND C030_Acesso_Liberado = 'S')
            AND EXISTS (SELECT 1 FROM C019 JOIN C030 ON C030_C021_Id = C019_C021_Id
                WHERE C019_C007_Id = :usuario AND C019_C004_Id = :empresaSessao
                AND C030_C029_Id = 35 AND C030_Acesso_Liberado = 'S')
        )) LIMIT 1";
    $permitidos = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($permitidos === false) return false;
    if (count($permitidos) !== 1) {
        $erro = 'O título foi baixado, cancelado, agrupado, está sem saldo ou seu usuário não tem acesso ao Contas a Receber.';
        return false;
    }
    $registro = $permitidos[0];
    if ($edicao) return $registro;
    return array(
        'tituloId' => (int) $registro['tituloId'], 'clienteId' => (int) $registro['clienteId'],
        'empresaTituloId' => (int) $registro['empresaTituloId'], 'agendaId' => (int) $registro['agendaId'],
        'usuario' => (int) $autorizacao['usuario'], 'empresaSessao' => (int) $autorizacao['empresa'],
        'url' => '/crm/crm001/content/crm001contentEditarAgenda/?' . http_build_query(array(
            'oneWindow' => 'true', 'D024_Id' => (int) $registro['clienteId'], 'T002_Id' => (int) $registro['tituloId'], 'tab' => 'geral',
        )),
    );
}
