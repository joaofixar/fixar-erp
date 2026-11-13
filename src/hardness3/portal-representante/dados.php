<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

require_once __DIR__ . '/config.php';

function portalRepresentanteConectar($conf)
{
    if (!extension_loaded('mysqli')) throw new RepresentantePortalErro('Consulta ao sistema indisponível.', 503);
    $conexao = @\mysqli_connect($conf['dbHost'], $conf['dbUser'], $conf['dbPass'], $conf['dbDatabase']);
    if (!$conexao) throw new RepresentantePortalErro('Consulta ao sistema indisponível.', 503);
    try {
        if (!\mysqli_set_charset($conexao, 'utf8')) throw new RepresentantePortalErro('Consulta ao sistema indisponível.', 503);
        portalRepresentantePrepararLeitura($conexao);
    } catch (\Throwable $erro) { portalRepresentanteFecharConexao($conexao); throw $erro; }
    return $conexao;
}

function portalRepresentantePrepararLeitura($conexao)
{
    // O ERP define hardness\mysqli_query($sql, $conexao), com assinatura diferente.
    // Esta conexão pertence ao portal e usa exclusivamente a API nativa do PHP.
    $limite = stripos(\mysqli_get_server_info($conexao), 'mariadb') !== false ? 'SET SESSION MAX_STATEMENT_TIME=5' : 'SET SESSION MAX_EXECUTION_TIME=5000';
    if (!\mysqli_query($conexao, $limite) || !\mysqli_query($conexao, 'START TRANSACTION READ ONLY')) throw new RepresentantePortalErro('Consulta somente leitura indisponível.', 503);
}

function portalRepresentanteFecharConexao($conexao)
{
    \mysqli_close($conexao);
}

function portalRepresentanteConsultar($conexao, $sql, $parametros = array())
{
    $valores = array();
    $preparado = preg_replace_callback('/:([a-zA-Z][a-zA-Z0-9_]*)/', function ($p) use ($parametros, &$valores) {
        if (!array_key_exists($p[1], $parametros)) throw new RepresentantePortalErro('Consulta indisponível.', 503);
        $valores[] = $parametros[$p[1]]; return '?';
    }, $sql);
    $stmt = \mysqli_prepare($conexao, $preparado);
    if (!$stmt) throw new RepresentantePortalErro('Consulta indisponível.', 503);
    try {
        if ($valores) {
            $tipos = ''; $args = array(&$tipos);
            foreach ($valores as $i => $valor) { $tipos .= is_int($valor) ? 'i' : 's'; $args[] = &$valores[$i]; }
            if (!call_user_func_array(array($stmt, 'bind_param'), $args)) throw new RepresentantePortalErro('Consulta indisponível.', 503);
        }
        if (!\mysqli_stmt_execute($stmt)) throw new RepresentantePortalErro('Não foi possível consultar. Reduza o período e tente novamente.', 503);
        $resultado = \mysqli_stmt_get_result($stmt);
        if (!$resultado) throw new RepresentantePortalErro('Consulta indisponível.', 503);
        return \mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    } finally { \mysqli_stmt_close($stmt); }
}

function portalRepresentanteLerCadastro($conexao, $id)
{
    $linhas = portalRepresentanteConsultar($conexao, 'SELECT D021_Id,D021_Nome_Empresa,D021_Ativo,D021_Flag_Pessoa_Fisica_Juridica,
        D021_Cpf,D021_Cnpj,D021_E_Mail FROM D021 WHERE D021_Id=:id LIMIT 1', array('id' => (int) $id));
    return $linhas ? $linhas[0] : false;
}

function portalRepresentanteLerRemetente($conexao, $id)
{
    $linhas = portalRepresentanteConsultar($conexao, 'SELECT C007_Email,C007_Primeiro_Nome,C007_Email_SMTP_Servidor,
        C007_Email_SMTP_Usuario,C007_Email_SMTP_Senha,C007_Email_SMTP_Porta,C007_Email_SMTP_Autenticacao,C007_Email_SMTP_Seguranca
        FROM C007 WHERE C007_Id=:id LIMIT 1', array('id' => (int) $id));
    return $linhas ? $linhas[0] : false;
}

function portalRepresentanteFiltros($entrada)
{
    $inicio = isset($entrada['inicio']) ? $entrada['inicio'] : date('Y-01-01');
    $fim = isset($entrada['fim']) ? $entrada['fim'] : date('Y-m-d');
    foreach (array($inicio, $fim) as $data) {
        if (!is_string($data) || !preg_match('/^([1-9][0-9]{3})-([0-9]{2})-([0-9]{2})$/D', $data, $p) || !checkdate((int) $p[2], (int) $p[3], (int) $p[1])) throw new RepresentantePortalErro('Informe datas válidas.');
    }
    $de = new \DateTimeImmutable($inicio); $ate = new \DateTimeImmutable($fim);
    if ($fim < $inicio || $de->diff($ate)->days > 365) throw new RepresentantePortalErro('Selecione até 366 dias por consulta.');
    $cliente = isset($entrada['cliente']) ? $entrada['cliente'] : ''; $status = isset($entrada['status']) ? $entrada['status'] : '';
    if (!is_string($cliente) || strlen($cliente) > 100 || !is_string($status) || !in_array($status, array('', 'pago', 'aberto', 'parcial', 'vencido'), true)) throw new RepresentantePortalErro('Filtro inválido.');
    $composicao = isset($entrada['composicao']) ? $entrada['composicao'] : '';
    if (!is_string($composicao) || !in_array($composicao, array('', 'previsto', 'recebido', 'saldo', 'vencido', 'media', 'proximos7'), true)) throw new RepresentantePortalErro('Composição inválida.');
    $segmento = isset($entrada['segmento']) ? $entrada['segmento'] : '';
    if (!is_string($segmento) || !in_array($segmento, array('', 'Revenda', 'Construção', 'Obra'), true)) throw new RepresentantePortalErro('Distrito inválido.');
    $numero = isset($entrada['numero']) ? $entrada['numero'] : '';
    $etapa = isset($entrada['etapa']) ? $entrada['etapa'] : '';
    if (!is_string($numero) || ($numero !== '' && !preg_match('/^[A-Za-z0-9.\/\-]{1,40}$/D', $numero)) || !is_string($etapa)
        || !in_array($etapa, array('', 'convertido', 'pendente', 'analise', 'finalizado', 'aprovado', 'vencido', 'negado', 'sem_conversao', 'cancelado', 'com_nota', 'sem_nota', 'atrasado', 'pendente_direto', 'expedicao', 'outras_etapas', 'em_aberto', 'quitada', 'sem_cobranca'), true)) throw new RepresentantePortalErro('Filtro inválido.');
    $clienteId = isset($entrada['clienteId']) ? $entrada['clienteId'] : '';
    if (!is_scalar($clienteId) || ($clienteId !== '' && (!preg_match('/^[1-9][0-9]{0,9}$/D', (string) $clienteId) || (float) $clienteId > 2147483647))) throw new RepresentantePortalErro('Cliente inválido.');
    $grupoClienteId = isset($entrada['grupoClienteId']) ? $entrada['grupoClienteId'] : '';
    if (!is_scalar($grupoClienteId) || ($grupoClienteId !== '' && (!preg_match('/^[1-9][0-9]{0,9}$/D', (string) $grupoClienteId) || (float) $grupoClienteId > 2147483647))) throw new RepresentantePortalErro('Grupo de clientes inválido.');
    $pagina = isset($entrada['pagina']) && is_scalar($entrada['pagina']) ? (int) $entrada['pagina'] : 1;
    return array('inicio' => $inicio, 'fim' => $fim, 'limiteFim' => $ate->modify('+1 day')->format('Y-m-d'), 'cliente' => trim($cliente), 'clienteId' => $clienteId === '' ? 0 : (int) $clienteId,
        'grupoClienteId' => $grupoClienteId === '' ? 0 : (int) $grupoClienteId, 'numero' => $numero, 'etapa' => $etapa, 'status' => $status, 'pagina' => max(1, min(1000, $pagina)), 'composicao' => $composicao, 'segmento' => $segmento);
}

function portalRepresentanteNotaValida($alias = 'd')
{
    return str_replace('d.', $alias . '.', "d.T007_Flag_Entrada_Saida='S' AND COALESCE(d.T007_Flag_Emitida_Recebida,'')<>'R'
        AND COALESCE(d.T007_Flag_Cancelada,'N')<>'S' AND COALESCE(d.T007_Flag_Inutilizado,'N')<>'S'
        AND COALESCE(d.T007_Flag_Denegado,'N')<>'S' AND TRIM(COALESCE(d.T007_Numero_Protocolo_Nfe,''))<>''");
}

function portalRepresentanteBaseConfigurada($tipo, $conta, $filtros, $hoje = null, $projecao = null)
{
    if (empty($conta['empresa']) || empty($conta['representante'])) throw new RepresentantePortalErro('Acesso sem vínculo.', 403);
    if (!portalRepresentanteCamposFonte($tipo)) throw new RepresentantePortalErro('Visão inválida.');
    $config = isset($conta['painel']) ? $conta['painel'] : portalRepresentanteLerConfiguracao($conta['empresa']);
    $fonte = $config['fontes'][$tipo]; $campos = array();
    foreach (portalRepresentanteCamposOpcionaisFonte($tipo) as $nome => $padrao) {
        if (!array_key_exists($nome, $fonte['campos'])) $fonte['campos'][$nome] = isset($fonte['campos'][$padrao]) ? $fonte['campos'][$padrao] : $padrao;
    }
    foreach ($projecao === null ? portalRepresentanteCamposFonte($tipo, true) : $projecao as $nome) $campos[] = '(' . portalRepresentanteSqlTexto($fonte['campos'][$nome]) . ') AS ' . $nome;
    $empresa = $fonte['escopo']['empresa']; $representante = $fonte['escopo']['representante'];
    $escopo = $tipo === 'clientes' ? "($empresa=:empresa OR COALESCE($empresa,0)=0)" : "$empresa=:empresa";
    $sql = 'SELECT ' . implode(',', $campos) . ' FROM ' . portalRepresentanteSqlTexto($fonte['de'])
        . " WHERE $escopo AND $representante=:representante AND (" . portalRepresentanteSqlTexto($fonte['condicao']) . ')';
    $campoPeriodo = portalRepresentanteCampoPeriodo($config, $tipo);
    // Cobranças sem personalização preservam a evolução histórica por emissão.
    $campoEvolucao = $tipo === 'cobrancas' && !array_key_exists('campoPeriodo', $fonte) ? 'data' : $campoPeriodo;
    return array('sql' => $sql, 'campoPeriodo' => $campoPeriodo, 'campoEvolucao' => $campoEvolucao, 'params' => array('empresa' => (int) $conta['empresa'], 'representante' => (int) $conta['representante'],
        'inicio' => $filtros['inicio'], 'fim' => $filtros['limiteFim'], 'hoje' => $hoje === null ? date('Y-m-d') : $hoje));
}

function portalRepresentanteFonte($tipo, $conta, $filtros, $nota = 0, $hoje = null, $projecao = null)
{
    if ($tipo === 'clientes') throw new RepresentantePortalErro('Visão inválida.');
    // Os detalhes de separação/carga são calculados só para a página de pedidos.
    // Não executa funções por pedido ao consolidar indicadores do ano inteiro.
    if ($projecao === null) $projecao = portalRepresentanteCamposFonte($tipo, true);
    if ($tipo === 'pedidos') $projecao = array_values(array_diff($projecao, array('percentualSeparado','numeroCarga')));
    $base = portalRepresentanteBaseConfigurada($tipo, $conta, $filtros, $hoje, $projecao);
    $params = $base['params']; $where = array('1=1');
    $data = $base['campoPeriodo'];
    if ($tipo === 'cobrancas' && $nota > 0) { $where[] = 'notaId=:nota'; $params['nota'] = (int) $nota; }
    else $where[] = "$data>=:inicio AND $data<:fim";
    if ($filtros['numero'] !== '') { $where[] = 'numero=:numero'; $params['numero'] = $filtros['numero']; }
    if ($tipo === 'orcamentos' && !empty($filtros['segmento'])) {
        $where[] = 'segmento=:segmento AND excluido=0'; $params['segmento'] = $filtros['segmento'];
    }
    if ($filtros['clienteId']) { $where[] = 'clienteId=:clienteId'; $params['clienteId'] = $filtros['clienteId']; }
    if (!empty($filtros['grupoClienteId'])) { $where[] = 'grupoClienteId=:grupoClienteId'; $params['grupoClienteId'] = $filtros['grupoClienteId']; }
    if ($filtros['cliente'] !== '') {
        $where[] = $tipo === 'orcamentos' ? '(LOCATE(:cliente,cliente)>0 OR LOCATE(:cliente,clienteNormal)>0 OR LOCATE(:cliente,clienteProposta)>0 OR LOCATE(:cliente,grupoCliente)>0)'
            : '(LOCATE(:cliente,cliente)>0 OR LOCATE(:cliente,grupoCliente)>0)';
        $params['cliente'] = $filtros['cliente'];
    }
    if ($filtros['etapa'] !== '') {
        $config = isset($conta['painel']) ? $conta['painel'] : portalRepresentanteLerConfiguracao($conta['empresa']);
        if ($tipo === 'orcamentos' && $filtros['etapa'] === 'cancelado' && isset($config['fontes'][$tipo]['campos']['cancelamentoReal'])) $where[] = 'cancelamentoReal=1';
        $especiais = array('pendente_direto' => 'pendenteDireto=1', 'atrasado' => 'prazoVencido=1', 'sem_nota' => "excluido=0 AND categoria<>'negado' AND comNota=0");
        if ($tipo === 'pedidos' && isset($especiais[$filtros['etapa']])) $where[] = '(' . $especiais[$filtros['etapa']] . ')';
        else { $where[] = 'categoria=:etapa'; $params['etapa'] = $filtros['etapa']; }
    }
    if (in_array($tipo, array('boletos','cobrancas'), true) && $filtros['status'] !== '') { $where[] = 'status=:status'; $params['status'] = $filtros['status']; }
    $ordem = "$data DESC,id DESC";
    if ($tipo === 'boletos' && !empty($filtros['composicao'])) {
        $composicao = $filtros['composicao'];
        $where[] = 'excluido=0';
        if ($composicao === 'recebido') $where[] = 'recebido>0';
        elseif ($composicao === 'saldo') $where[] = 'saldo>0';
        elseif ($composicao === 'vencido') $where[] = "status='vencido' AND saldo>0";
        elseif ($composicao === 'proximos7') {
            $where[] = 'saldo>0 AND vencimento>=:composicaoHoje AND vencimento<=:composicaoFim';
            $params['composicaoHoje'] = $hoje === null ? date('Y-m-d') : $hoje;
            $params['composicaoFim'] = (new \DateTimeImmutable($params['composicaoHoje']))->modify('+7 days')->format('Y-m-d');
        }
        $campo = in_array($composicao, array('saldo','vencido','proximos7'), true) ? 'saldo' : ($composicao === 'recebido' ? 'recebido' : 'valor');
        $ordem = "$campo DESC,$data DESC,id DESC";
    }
    return array('select' => implode(',', $projecao), 'from' => '(' . $base['sql'] . ') fonte_portal', 'campoPeriodo' => $data, 'campoEvolucao' => $base['campoEvolucao'],
        'where' => implode(' AND ', $where), 'ordem' => $ordem, 'params' => $params);
}

function portalRepresentantePedidosDetalhar($conexao, $conta, $linhas, $filtros = null)
{
    if (!$linhas) return $linhas;
    $config = isset($conta['painel']) ? $conta['painel'] : portalRepresentanteLerConfiguracao($conta['empresa']);
    $campos = $config['fontes']['pedidos']['campos'];
    $opcionais = array_intersect_key(portalRepresentanteCamposOpcionaisFonte('pedidos'), array_flip(array('percentualSeparado','numeroCarga')));
    $detalhes = array();
    if (array_intersect_key($campos, $opcionais)) {
        $base = portalRepresentanteBaseConfigurada('pedidos', $conta, $filtros === null ? portalRepresentanteFiltros(array()) : $filtros, null, array_merge(array('id'), array_keys($opcionais)));
        $ids = array();
        foreach ($linhas as $i => $linha) { $nome = 'pedidoPagina' . $i; $ids[] = ':' . $nome; $base['params'][$nome] = (int) $linha['id']; }
        $sql = $base['sql'] . ' AND (' . portalRepresentanteSqlTexto($campos['id']) . ') IN (' . implode(',', $ids) . ')';
        foreach (portalRepresentanteConsultar($conexao, $sql, $base['params']) as $detalhe) $detalhes[$detalhe['id']] = $detalhe;
    }
    foreach ($linhas as &$linha) {
        foreach ($opcionais as $campo => $padrao) $linha[$campo] = isset($detalhes[$linha['id']][$campo]) ? $detalhes[$linha['id']][$campo] : null;
    }
    unset($linha);
    return $linhas;
}

function portalRepresentanteSql($fonte, $resumo = false, $financeiro = false, $pagina = 1, $limite = 50)
{
    $base = 'SELECT ' . $fonte['select'] . ' FROM ' . $fonte['from'] . ' WHERE ' . $fonte['where'];
    if ($resumo) return 'SELECT COALESCE(SUM(CASE WHEN excluido=0 THEN 1 ELSE 0 END),0) AS quantidade,
        COALESCE(SUM(CASE WHEN excluido=0 THEN valor ELSE 0 END),0) AS valor' . ($financeiro ? ",COALESCE(SUM(saldo),0) AS saldo,COALESCE(SUM(recebido),0) AS recebido,COALESCE(SUM(CASE WHEN status='vencido' THEN saldo ELSE 0 END),0) AS vencido" : '') . ' FROM (' . $base . ') resumo_portal';
    return $base . ' ORDER BY ' . $fonte['ordem'] . ' LIMIT ' . (int) ($limite + 1) . ' OFFSET ' . ((max(1, (int) $pagina) - 1) * $limite);
}
