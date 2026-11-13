<?php
namespace hardness;

require_once __DIR__ . '/agenda-action.php';
require_once __DIR__ . '/session.php';

function dashboardTituloOpcoes($indicador)
{
    $config = isset($indicador['listagem']['detalhamento']['edicaoTitulo']) ? $indicador['listagem']['detalhamento']['edicaoTitulo'] : array();
    if (!isset($config['habilitado']) || $config['habilitado'] !== true) return array();
    $opcoes = array();
    foreach (isset($config['opcoesStatus']) && is_array($config['opcoesStatus']) ? $config['opcoesStatus'] : array() as $opcao) {
        if (!isset($opcao['valor'], $opcao['titulo']) || !is_scalar($opcao['valor']) || !is_string($opcao['titulo'])
            || !preg_match('/^[0-9]{1,3}$/', (string) $opcao['valor']) || trim($opcao['titulo']) === '') return array();
        $opcoes[(string) $opcao['valor']] = array('valor' => (string) $opcao['valor'], 'titulo' => $opcao['titulo']);
    }
    return array_values($opcoes);
}

function dashboardTituloContexto($conexao, $indicador, $autorizacao, $sessao, $entrada, $confUsuario, &$erro)
{
    $opcoes = dashboardTituloOpcoes($indicador);
    if (!$opcoes) { $erro = 'Configure os status permitidos para editar títulos neste indicador.'; return false; }
    $titulo = dashboardAgendaContexto($conexao, $indicador, $autorizacao, $sessao, $entrada, $erro, true);
    if ($titulo === false) return false;
    // O merge da aba substitui indicador.id pelo id da aba; mantém o id pai
    // já autorizado em data.php para resolver novamente o indicador no POST.
    $escopo = array('id' => isset($entrada['id']) && is_string($entrada['id']) ? $entrada['id'] : $indicador['id'],
        'tituloId' => (string) $titulo['tituloId']);
    foreach ($entrada as $campo => $valor) {
        if (($campo === 'tab' || $campo === 'detalheGrupo' || strpos($campo, 'f_') === 0) && is_scalar($valor)) $escopo[$campo] = (string) $valor;
    }
    $dados = array('entrada' => $escopo, 'titulo' => $titulo, 'usuario' => (int) $autorizacao['usuario'],
        'empresa' => (int) $autorizacao['empresa'], 'sessao' => hash('sha256', session_id()), 'expira' => time() + 900);
    $chave = dashboardPersonalizadoChaveAutorizacao($confUsuario);
    if ($chave === false) { $erro = 'Não foi possível autorizar a edição.'; return false; }
    $conteudo = dashboardPersonalizadoBase64UrlCodificar(json_encode($dados));
    return array('titulo' => $titulo, 'opcoesStatus' => $opcoes,
        'ticket' => $conteudo . '.' . hash_hmac('sha256', 'titulo-v1:' . $conteudo, $chave));
}

function dashboardTituloValidarTicket($ticket, $autorizacao, $confUsuario)
{
    $chave = dashboardPersonalizadoChaveAutorizacao($confUsuario);
    if (!$chave || !is_string($ticket) || strlen($ticket) > 20000
        || !preg_match('/^([a-zA-Z0-9_-]+)\.([a-f0-9]{64})$/', $ticket, $partes)
        || !hash_equals(hash_hmac('sha256', 'titulo-v1:' . $partes[1], $chave), $partes[2])) return false;
    $dados = json_decode(dashboardPersonalizadoBase64UrlDecodificar($partes[1]), true);
    if (!is_array($dados) || empty($dados['entrada']['id']) || empty($dados['titulo']['tituloId'])
        || !isset($dados['usuario'], $dados['empresa'], $dados['sessao'], $dados['expira'])
        || $dados['expira'] < time() || $dados['usuario'] !== (int) $autorizacao['usuario']
        || $dados['empresa'] !== (int) $autorizacao['empresa']
        || !hash_equals(hash('sha256', session_id()), $dados['sessao'])) return false;
    return $dados;
}

function dashboardTituloAberto($titulo)
{
    return array_key_exists('T002_Data_Recebimento', $titulo)
        && ($titulo['T002_Data_Recebimento'] === null || $titulo['T002_Data_Recebimento'] === '0000-00-00')
        && isset($titulo['T002_Valor_Saldo']) && (float) $titulo['T002_Valor_Saldo'] > 0
        && (!isset($titulo['T002_Flag_Cancelada']) || $titulo['T002_Flag_Cancelada'] !== 'S')
        && empty($titulo['T002_T002_Id_Agrupado']);
}

function dashboardTituloValidarAlteracao($entrada, $opcoes)
{
    $campo = isset($entrada['campo']) && is_string($entrada['campo']) ? $entrada['campo'] : '';
    $valor = isset($entrada['valor']) && is_string($entrada['valor']) ? $entrada['valor'] : '';
    if ($campo === 'statusTitulo') {
        if (!in_array($valor, array_column($opcoes, 'valor'), true)) throw new \RuntimeException('Selecione um status permitido.');
        return array('T002_Flag_Status', $valor);
    }
    if ($campo === 'dataVencimento') {
        if (!preg_match('/^([1-9][0-9]{3})-([0-9]{2})-([0-9]{2})$/', $valor, $data)
            || !checkdate((int) $data[2], (int) $data[3], (int) $data[1])) throw new \RuntimeException('Informe uma data de vencimento válida.');
        return array('T002_Data_Vencimento', $valor);
    }
    throw new \RuntimeException('Somente status e vencimento podem ser alterados.');
}

function dashboardTituloEscrever($conexao, $sql, $parametros)
{
    $preparado = dashboardIndicadorPrepararSql($sql, $parametros, $valores, $tipos, $erro);
    $stmt = $preparado === false || $erro !== '' ? false : mysqli_prepare($conexao, $preparado);
    if (!$stmt || !dashboardIndicadorBind($stmt, $tipos, $valores) || !mysqli_stmt_execute($stmt)) {
        if ($stmt) mysqli_stmt_close($stmt);
        throw new \RuntimeException('Não foi possível gravar a alteração do título.');
    }
    $afetados = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $afetados;
}

// Confere que os tickets pertencem ao mesmo detalhamento e não repetem títulos.
function dashboardTitulosValidarLote($tickets)
{
    if (!is_array($tickets) || count($tickets) < 1 || count($tickets) > 100) {
        throw new \RuntimeException('Selecione de 1 a 100 títulos por atualização.');
    }
    $ids = array();
    $escopoEsperado = null;
    foreach ($tickets as $ticket) {
        if (!is_array($ticket) || empty($ticket['titulo']['tituloId']) || empty($ticket['entrada']['id'])) {
            throw new \RuntimeException('A seleção de títulos é inválida.');
        }
        $id = (int) $ticket['titulo']['tituloId'];
        $escopo = $ticket['entrada'];
        unset($escopo['tituloId']);
        ksort($escopo);
        if (isset($ids[$id]) || ($escopoEsperado !== null && $escopo !== $escopoEsperado)) {
            throw new \RuntimeException('Selecione títulos distintos do mesmo detalhamento.');
        }
        $ids[$id] = true;
        $escopoEsperado = $escopo;
    }
    // Ordem fixa para reduzir disputas de bloqueio entre lotes simultâneos.
    usort($tickets, function ($a, $b) { return (int) $a['titulo']['tituloId'] <=> (int) $b['titulo']['tituloId']; });
    return $tickets;
}

function dashboardTitulosValidarAlteracoes($entrada, $opcoes)
{
    $recebidas = isset($entrada['alteracoes']) && is_array($entrada['alteracoes']) ? $entrada['alteracoes'] : array();
    if (!$recebidas || count($recebidas) > 2) throw new \RuntimeException('Informe o novo status, o vencimento ou ambos.');
    $alteracoes = array();
    foreach ($recebidas as $campo => $valor) {
        list($coluna, $novo) = dashboardTituloValidarAlteracao(array('campo' => $campo, 'valor' => $valor), $opcoes);
        $alteracoes[$coluna] = $novo;
    }
    return $alteracoes;
}

function dashboardTituloAtualizar($conexao, $indicador, $autorizacao, $sessao, $ticket, $entrada)
{
    list($campo, $valor) = dashboardTituloValidarAlteracao($entrada, dashboardTituloOpcoes($indicador));
    $resultado = dashboardTitulosGravar($conexao, $indicador, $autorizacao, $sessao, array($ticket), array($campo => $valor));
    $resultado['mensagem'] = $resultado['atualizados'] === 0 ? 'O título já possui esse valor.'
        : ($campo === 'T002_Flag_Status' ? 'Status do título atualizado.' : 'Vencimento do título atualizado.');
    return $resultado;
}

function dashboardTitulosAtualizar($conexao, $indicador, $autorizacao, $sessao, $tickets, $entrada)
{
    $opcoes = dashboardTituloOpcoes($indicador);
    if (!$opcoes) throw new \RuntimeException('A edição de títulos não está habilitada.');
    return dashboardTitulosGravar($conexao, $indicador, $autorizacao, $sessao, $tickets, dashboardTitulosValidarAlteracoes($entrada, $opcoes));
}

// O lote inteiro é validado antes da primeira escrita; qualquer falha reverte todos os títulos.
function dashboardTitulosGravar($conexao, $indicador, $autorizacao, $sessao, $tickets, $alteracoes)
{
    if (!dashboardAgendaSessaoValida($autorizacao, $sessao)) throw new \RuntimeException('A sessão do ERP mudou. Reabra o dashboard.');
    if (!dashboardTituloOpcoes($indicador)) throw new \RuntimeException('A edição de títulos não está habilitada.');
    $tickets = dashboardTitulosValidarLote($tickets);
    foreach (array('T002', 'T162') as $tabela) {
        $estrutura = dashboardIndicadorConsultar($conexao, "SHOW TABLE STATUS LIKE '" . $tabela . "'", array(), $erro);
        if (!$estrutura || strcasecmp($estrutura[0]['Engine'], 'InnoDB') !== 0) {
            throw new \RuntimeException('A edição exige T002 e T162 em InnoDB para salvar o título e seu histórico juntos.');
        }
    }
    if (!mysqli_begin_transaction($conexao)) throw new \RuntimeException('Não foi possível iniciar a atualização.');
    try {
        $registros = array();
        // Bloqueia todos antes das leituras de escopo e permissões.
        foreach ($tickets as $ticket) {
            $esperado = $ticket['titulo'];
            $parametros = array('titulo' => (int) $esperado['tituloId'], 'cliente' => (int) $esperado['clienteId'],
                'empresaTitulo' => (int) $esperado['empresaTituloId'], 'usuario' => (int) $autorizacao['usuario']);
            $linhas = dashboardIndicadorConsultar($conexao, 'SELECT * FROM T002 WHERE T002_Id = :titulo
                AND T002_D024_Id = :cliente AND T002_C004_Id = :empresaTitulo LIMIT 1 FOR UPDATE', $parametros, $erro);
            if (!$linhas || !dashboardTituloAberto($linhas[0])) {
                throw new \RuntimeException('O título ' . $parametros['titulo'] . ' não está mais em aberto. Atualize o detalhamento.');
            }
            $antes = $linhas[0];
            if ((string) $antes['T002_Data_Vencimento'] !== (string) $esperado['dataVencimento']
                || (string) $antes['T002_Flag_Status'] !== (string) $esperado['statusTitulo']) {
                throw new \RuntimeException('O título ' . $parametros['titulo'] . ' foi alterado por outro usuário. Atualize o detalhamento antes de editar.');
            }
            $registros[] = array('antes' => $antes, 'parametros' => $parametros, 'ticket' => $ticket);
        }
        foreach ($registros as $registro) {
            $parametros = $registro['parametros'];
            if (dashboardAgendaContexto($conexao, $indicador, $autorizacao, $sessao, $registro['ticket']['entrada'], $erro, true) === false) {
                throw new \RuntimeException('Título ' . $parametros['titulo'] . ': ' . $erro);
            }
            if (isset($alteracoes['T002_Data_Vencimento'])) {
                $config = dashboardIndicadorConsultar($conexao, "SELECT C031_Valor FROM C031
                    WHERE C031_C004_Id = :empresaTitulo AND C031_Campo = 'aceitarVencimentoAnteriorEmissaoPagar' LIMIT 1", $parametros, $erro);
                if ($config === false) throw new \RuntimeException('Não foi possível validar a regra de vencimento do ERP.');
                if ($config && $config[0]['C031_Valor'] === 'N' && $alteracoes['T002_Data_Vencimento'] < $registro['antes']['T002_Data_Emissao']) {
                    throw new \RuntimeException('Título ' . $parametros['titulo'] . ': a data de vencimento não pode ser anterior à emissão.');
                }
            }
        }
        $atualizados = 0;
        foreach ($registros as $registro) {
            $antes = $registro['antes'];
            $parametros = $registro['parametros'];
            $sets = array();
            foreach ($alteracoes as $campo => $valor) {
                if ((string) $antes[$campo] === $valor) continue;
                $parametro = count($alteracoes) === 1 ? 'valor' : ($campo === 'T002_Flag_Status' ? 'novoStatus' : 'novoVencimento');
                $parametros[$parametro] = $valor;
                $sets[] = $campo . ' = :' . $parametro;
            }
            if (!$sets) continue;
            $afetados = dashboardTituloEscrever($conexao, 'UPDATE T002 SET ' . implode(', ', $sets) . "
                WHERE T002_Id = :titulo AND T002_D024_Id = :cliente AND T002_C004_Id = :empresaTitulo
                AND (T002_Data_Recebimento IS NULL OR T002_Data_Recebimento = '0000-00-00')
                AND IFNULL(T002_Flag_Cancelada, 'N') <> 'S' AND IFNULL(T002_Valor_Saldo, 0) > 0
                AND IFNULL(T002_T002_Id_Agrupado, 0) <= 0", $parametros);
            if ($afetados !== 1) throw new \RuntimeException('O título mudou. Nenhuma alteração foi confirmada.');
            $depois = dashboardIndicadorConsultar($conexao, 'SELECT * FROM T002 WHERE T002_Id = :titulo LIMIT 1', $parametros, $erro);
            if (!$depois || !dashboardTituloAberto($depois[0])) throw new \RuntimeException('Não foi possível confirmar o valor gravado.');
            foreach ($alteracoes as $campo => $valor) {
                if ((string) $depois[0][$campo] !== $valor) throw new \RuntimeException('Não foi possível confirmar o valor gravado.');
            }
            $historico = array();
            foreach ($antes as $nome => $antigo) {
                if (array_key_exists($nome, $depois[0]) && $antigo !== $depois[0][$nome]) $historico[$nome] = array($antigo, $depois[0][$nome]);
            }
            $parametros['registro'] = serialize($historico);
            $parametros['origem'] = 'dashboard-personalizado/atualizar-titulo.php ' . implode(', ', array_keys($alteracoes));
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $parametros['ip'] = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : '';
            if (dashboardTituloEscrever($conexao, "INSERT INTO T162
                (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_TempoExecucao,
                 T162_Registro_Id, T162_Registro, T162_IP)
                VALUES (:usuario, 'U', :origem, NOW(), 'T002', 0, :titulo, COMPRESS(:registro), :ip)", $parametros) !== 1) {
                throw new \RuntimeException('Não foi possível registrar a alteração no histórico.');
            }
            $atualizados++;
        }
        if ($atualizados === 0) mysqli_rollback($conexao);
        elseif (!mysqli_commit($conexao)) throw new \RuntimeException('Não foi possível confirmar a atualização.');
        return array('ok' => true, 'atualizados' => $atualizados, 'selecionados' => count($tickets),
            'mensagem' => $atualizados === 0 ? 'Os títulos selecionados já possuem os valores informados.'
                : $atualizados . ($atualizados === 1 ? ' título atualizado.' : ' títulos atualizados.'));
    } catch (\Throwable $e) {
        mysqli_rollback($conexao);
        throw $e;
    }
}
