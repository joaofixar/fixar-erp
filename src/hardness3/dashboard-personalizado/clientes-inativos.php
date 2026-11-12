<?php
namespace hardness;

// Regras fixas da ação. O JSON não pode fornecer SQL de escrita.
function dashboardClientesInativosHabilitado($indicador)
{
    return isset($indicador['clientesInativos']['habilitado'])
        && $indicador['clientesInativos']['habilitado'] === true;
}

function dashboardClientesInativosCamposCliente()
{
    return "c.D024_Id AS id, c.D024_Nome_Empresa AS descricao, c.D024_Flag_Contribuinte AS tipoId,
        c.D024_C007_Vendedor_Interno AS vendedorAtualId, c.D024_Flag_Ativo AS flagAtivo,
        c.D024_Flag_Comercial AS flagComercial, c.D024_Data_Atualizacao AS dataAtualizacao,
        c.D024_Data_Ativo AS dataAtivoCadastro, c.D024_Data_Inativo AS dataInativoCadastro,
        NULLIF(c.D024_Data_Inativo, '0000-00-00') AS dataInativacao,
        NULL AS vendedorAnteriorId, 'Histórico pendente' AS vendedorAnterior,
        NULL AS dataAtivacao, NULL AS novoVendedor, NULL AS auditoriaId,
        'N' AS historicoConfirmado, 'Inativo' AS situacao, 1 AS valor";
}

function dashboardClientesInativosConfigurar($indicador, $entrada)
{
    // O sqlBase do JSON governa listagem e contagem, com a validação SELECT do
    // motor. Consultas e regras próprias da ação não substituem essa configuração.
    $indicador['card']['compacta'] = true;
    $indicador['card']['ocultarTitulo'] = true;
    $indicador['card']['descricao'] = '';
    $indicador['listagem']['ocultarTitulo'] = true;
    unset($indicador['listagem']['aviso'], $indicador['listagem']['somenteConsulta']);
    $indicador['listagem']['colunas'] = array_values(array_filter($indicador['listagem']['colunas'], function ($coluna) {
        return $coluna['campo'] !== 'origemHistorico';
    }));
    $indicador['filtros'] = array_values(array_filter($indicador['filtros'], function ($filtro) {
        return !in_array($filtro['id'], array('dataInicio', 'dataFim'), true);
    }));
    if (isset($entrada['f_situacao']) && $entrada['f_situacao'] === 'reativados') {
        $indicador['filtros'][] = array('id' => 'dataInicio', 'titulo' => 'Reativação desde', 'tipo' => 'data',
            'padrao' => date('Y-m-d', strtotime('-29 days')), 'obrigatorio' => true);
        $indicador['filtros'][] = array('id' => 'dataFim', 'titulo' => 'Reativação até', 'tipo' => 'data',
            'padrao' => date('Y-m-d'), 'obrigatorio' => true);
    }
    return $indicador;
}

function dashboardClientesInativosPodeAtualizar($autorizacao)
{
    return dashboardPersonalizadoEhRoot($autorizacao) || !empty($autorizacao['diretor']);
}

function dashboardClientesInativosTipo($tipo)
{
    $tipos = array('0' => 'Não contribuinte', '1' => 'Consumidor final', '2' => 'Revenda',
        '3' => 'Produtor rural', '4' => 'Simples', '5' => 'Indústria', '8' => 'Indústria triangular');
    $tipo = trim((string) $tipo);
    return isset($tipos[$tipo]) ? $tipos[$tipo] : ($tipo === '' ? 'Não informado' : 'Tipo ' . $tipo);
}

function dashboardClientesInativosCarteiras($conexao, $empresa, &$erro)
{
    // Inclui os cadastros compartilhados (C004 nulo/zero), como a tela cad001.
    // Não usa o filtro/período da listagem: o perfil considera toda a carteira ativa.
    // O vendedor interno do cad001 admite Vendedor, Supervisor e Diretor ativos.
    // Carteira ativa segue a coluna Ativo do cadastro, inclusive situação comercial I.
    // C007_Id 63 é o ROOT do sistema, não um vendedor da empresa.
    $sql = "SELECT v.C007_Id AS vendedorId,
        COALESCE(NULLIF(v.C007_Primeiro_Nome, ''), v.C007_Nome) AS vendedor,
        TRIM(COALESCE(c.D024_Flag_Contribuinte, '')) AS tipoId, COUNT(*) AS quantidade
        FROM D024 c INNER JOIN C007 v ON v.C007_Id = c.D024_C007_Vendedor_Interno
        WHERE (c.D024_C004_Id = :empresa OR COALESCE(c.D024_C004_Id, 0) = 0)
          AND c.D024_Flag_Cliente = 'S' AND c.D024_Flag_Ativo = 'S'
          AND v.C007_Ativo = 'S' AND v.C007_Flag_Vendedor IN ('V', 'S', 'D')
          AND v.C007_Id <> 63
        GROUP BY v.C007_Id, v.C007_Primeiro_Nome, v.C007_Nome, TRIM(COALESCE(c.D024_Flag_Contribuinte, ''))";
    return dashboardIndicadorConsultar($conexao, $sql, array('empresa' => (int) $empresa), $erro);
}

function dashboardClientesInativosPerfis($carteiras)
{
    $perfis = array();
    foreach ($carteiras as $linha) {
        $id = (int) $linha['vendedorId'];
        if ($id === 63) continue;
        if (!isset($perfis[$id])) {
            $perfis[$id] = array('id' => $id, 'nome' => $linha['vendedor'], 'total' => 0, 'tipos' => array());
        }
        $tipo = trim((string) $linha['tipoId']);
        $quantidade = (int) $linha['quantidade'];
        $perfis[$id]['total'] += $quantidade;
        $perfis[$id]['tipos'][$tipo] = (isset($perfis[$id]['tipos'][$tipo]) ? $perfis[$id]['tipos'][$tipo] : 0) + $quantidade;
    }
    return $perfis;
}

function dashboardClientesInativosSugerir($tipo, $vendedorAnterior, $perfis)
{
    $tipo = trim((string) $tipo);
    if ($tipo === '') return null; // O código zero é um tipo válido.
    $melhor = null;
    foreach ($perfis as $perfil) {
        if ($perfil['id'] === (int) $vendedorAnterior || $perfil['total'] <= 0) continue;
        $quantidade = isset($perfil['tipos'][$tipo]) ? $perfil['tipos'][$tipo] : 0;
        if ($quantidade <= 0) continue;
        $perfil['quantidadeTipo'] = $quantidade;
        // Compara as frações sem arredondar o percentual mostrado na tela.
        $comparacao = $melhor === null ? 1 : $quantidade * $melhor['total'] - $melhor['quantidadeTipo'] * $perfil['total'];
        if ($melhor === null || $comparacao > 0
            || ($comparacao === 0 && ($quantidade > $melhor['quantidadeTipo']
                || ($quantidade === $melhor['quantidadeTipo'] && $perfil['id'] < $melhor['id'])))) {
            $melhor = $perfil;
        }
    }
    if ($melhor !== null) {
        $melhor['percentual'] = 100 * $melhor['quantidadeTipo'] / $melhor['total'];
        $maximo = max($melhor['tipos']);
        $predominantes = array_keys(array_filter($melhor['tipos'], function ($n) use ($maximo) { return $n === $maximo; }));
        $melhor['perfilPredominante'] = implode(', ', array_map(__NAMESPACE__ . '\\dashboardClientesInativosTipo', $predominantes));
    }
    return $melhor;
}

function dashboardClientesInativosDataValida($valor)
{
    if (!is_string($valor) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) return false;
    $data = \DateTime::createFromFormat('!Y-m-d', $valor);
    return $data && $data->format('Y-m-d') === $valor && substr($valor, 0, 4) !== '0000';
}

function dashboardClientesInativosDecodificar($registro)
{
    if (!is_string($registro) || strlen($registro) > 262144) return null;
    $campos = @unserialize($registro, array('allowed_classes' => false));
    if (!is_array($campos)) return null;
    $permitidos = array('D024_C007_VENDEDOR_INTERNO', 'D024_FLAG_ATIVO', 'D024_FLAG_COMERCIAL',
        'D024_DATA_INATIVO', 'D024_DATA_ATIVO');
    $retorno = array();
    foreach ($campos as $campo => $par) {
        $campo = strtoupper((string) $campo);
        if (!in_array($campo, $permitidos, true)) continue;
        if (!is_array($par) || count($par) !== 2 || !array_key_exists(0, $par) || !array_key_exists(1, $par)
            || (!is_scalar($par[0]) && $par[0] !== null) || (!is_scalar($par[1]) && $par[1] !== null)) return null;
        $retorno[$campo] = $par;
    }
    return $retorno;
}

function dashboardClientesInativosEpisodios($cliente, $registros)
{
    // T162_DataHora pode vir do PHP do cadastro ou do relógio do MySQL.
    // Os IDs preservam a sequência dos eventos mesmo com fusos diferentes.
    usort($registros, function ($a, $b) {
        return (int) $b['auditoriaId'] <=> (int) $a['auditoriaId'];
    });
    $campos = array('D024_C007_VENDEDOR_INTERNO' => 'vendedorAtualId', 'D024_FLAG_ATIVO' => 'flagAtivo');
    $estado = array();
    foreach ($campos as $campo => $alias) $estado[$campo] = (string) $cliente[$alias];
    // Mesma regra da aba Inativos do cad001. Situação comercial I não inativa o cadastro.
    $inativo = function ($e) { return $e['D024_FLAG_ATIVO'] === 'N'; };
    $resultado = array('aberto' => null, 'encerrados' => array(), 'incompleto' => count($registros) >= 30);
    $abertoAtual = $inativo($estado);
    $ativacao = null;
    foreach ($registros as $registro) {
        $alteracoes = dashboardClientesInativosDecodificar($registro['registro']);
        $dataEvento = substr((string) $registro['dataHora'], 0, 10);
        if ($alteracoes === null || !dashboardClientesInativosDataValida($dataEvento)
            || (int) $registro['auditoriaId'] <= 0) { $resultado['incompleto'] = true; break; }
        $antes = $estado;
        foreach ($campos as $campo => $alias) {
            if (!isset($alteracoes[$campo])) continue;
            if ((string) $alteracoes[$campo][1] !== $estado[$campo]) {
                $resultado['incompleto'] = true;
                break 2; // Cadeia contraditória: não supor vínculos antigos.
            }
            $antes[$campo] = (string) $alteracoes[$campo][0];
        }
        if ($inativo($estado) !== $inativo($antes)) {
            if (!ctype_digit($estado['D024_C007_VENDEDOR_INTERNO'])) { $resultado['incompleto'] = true; break; }
            $campoData = $inativo($estado) ? 'D024_DATA_INATIVO' : 'D024_DATA_ATIVO';
            $data = isset($alteracoes[$campoData]) && $alteracoes[$campoData][0] !== $alteracoes[$campoData][1]
                && dashboardClientesInativosDataValida($alteracoes[$campoData][1]) ? $alteracoes[$campoData][1] : $dataEvento;
            if (!$inativo($estado)) {
                $ativacao = array('dataAtivacao' => $data, 'dataHoraAtivacao' => $registro['dataHora'],
                    'novoVendedorId' => (int) $estado['D024_C007_VENDEDOR_INTERNO'],
                    'auditoriaAtivacaoId' => (int) $registro['auditoriaId'],
                    'origemAtivacao' => isset($registro['origemRegistro']) ? $registro['origemRegistro'] : '');
                if (preg_match('~^dashboard-personalizado/atualizar-vendedor\.php agenda=([1-9][0-9]*)$~D', $ativacao['origemAtivacao'], $vinculo)) {
                    $ativacao['agendaId'] = (int) $vinculo[1];
                }
            } else {
                // NEW: vínculo gravado ao concluir a inativação, inclusive troca simultânea.
                $episodio = array('vendedorAnteriorId' => (int) $estado['D024_C007_VENDEDOR_INTERNO'],
                    'dataInativacao' => $data, 'auditoriaId' => (int) $registro['auditoriaId'], 'historicoConfirmado' => 'S');
                if ($ativacao !== null) {
                    $resultado['encerrados'][] = array_merge($episodio, $ativacao);
                    $ativacao = null;
                } elseif ($abertoAtual && $resultado['aberto'] === null) {
                    $resultado['aberto'] = $episodio;
                    $abertoAtual = false;
                }
            }
        }
        $estado = $antes;
    }
    if ($ativacao !== null) {
        // A reativação está comprovada, mas a inativação anterior pode ter saído
        // da amostra. Exibe a data comprovada sem inventar vendedor anterior.
        $resultado['encerrados'][] = array_merge(array('vendedorAnteriorId' => null,
            'dataInativacao' => null, 'auditoriaId' => null, 'historicoConfirmado' => 'N'), $ativacao);
    }
    return $resultado;
}

function dashboardClientesInativosReconstruir($cliente, $registros)
{
    $episodios = dashboardClientesInativosEpisodios($cliente, $registros);
    return $episodios['aberto'];
}

function dashboardClientesInativosAuditoria($conexao, $clientes, &$erro)
{
    $porCliente = array();
    // Limites fixos, usando o índice de registro e somente IDs já autorizados.
    foreach (array_chunk(array_slice($clientes, 0, 500, true), 25, true) as $lote) {
        $consultas = array();
        $parametros = array();
        foreach ($lote as $id => $linha) {
            $chave = 'cliente' . (int) $id;
            $parametros[$chave] = (int) $id;
            $consultas[] = 'SELECT T162_Registro_Id AS clienteId, T162_Id AS auditoriaId, T162_DataHora AS dataHora,
                LEFT(T162_Query, 128) AS origemRegistro,
                T162_Registro AS registroCompactado FROM T162 FORCE INDEX (IDX_T162_Registro_Id)
                WHERE T162_Registro_Id = :' . $chave . " AND T162_Tabela = 'D024' AND T162_Acao = 'U'
                ORDER BY T162_Id DESC LIMIT 30";
        }
        $sqlLote = count($consultas) === 1 ? $consultas[0] : '(' . implode(') UNION ALL (', $consultas) . ')';
        try {
            $registros = dashboardIndicadorConsultar($conexao, 'SELECT a.clienteId, a.auditoriaId, a.dataHora, a.origemRegistro,
                COALESCE(UNCOMPRESS(a.registroCompactado), a.registroCompactado) AS registro
                FROM (' . $sqlLote . ') AS a ORDER BY a.clienteId, a.auditoriaId DESC', $parametros, $erro);
        } catch (\Throwable $e) { $registros = false; }
        if ($registros === false) { $erro = 'Não foi possível consultar o histórico existente dos clientes.'; return false; }
        foreach ($registros as $registro) $porCliente[(int) $registro['clienteId']][] = $registro;
    }
    return $porCliente;
}

function dashboardClientesInativosNomes($conexao, $linhas, &$erro)
{
    $ids = array();
    foreach ($linhas as $linha) foreach (array('vendedorAnteriorId', 'novoVendedorId', 'vendedorAtualId') as $campo) {
        if (!empty($linha[$campo])) $ids['vendedor' . (int) $linha[$campo]] = (int) $linha[$campo];
    }
    $nomes = array();
    if ($ids) {
        $registros = dashboardIndicadorConsultar($conexao, "SELECT C007_Id AS id,
            COALESCE(NULLIF(C007_Primeiro_Nome, ''), C007_Nome) AS nome FROM C007 WHERE C007_Id IN ("
            . implode(', ', array_map(function ($id) { return ':' . $id; }, array_keys($ids))) . ')', $ids, $erro);
        if ($registros === false) return false;
        foreach ($registros as $registro) $nomes[(int) $registro['id']] = $registro['nome'];
    }
    foreach ($linhas as &$linha) {
        foreach (array('vendedorAnteriorId' => 'vendedorAnterior', 'novoVendedorId' => 'novoVendedor', 'vendedorAtualId' => 'vendedorAtual') as $campo => $destino) {
            if (isset($linha[$destino . 'Registrado']) && $linha[$destino . 'Registrado'] !== '') {
                $linha[$destino] = $linha[$destino . 'Registrado'];
                continue;
            }
            if (!isset($linha[$campo])) continue;
            $id = (int) $linha[$campo];
            $linha[$destino] = $id === 0 ? 'Sem vendedor' : (isset($nomes[$id]) ? $nomes[$id] : 'Vendedor #' . $id);
        }
    }
    unset($linha);
    return $linhas;
}

function dashboardClientesInativosRecuperar($conexao, $linhas)
{
    $clientes = array();
    foreach ($linhas as $linha) {
        if ($linha['flagAtivo'] === 'N') $clientes[(int) $linha['id']] = $linha;
    }
    if (!$clientes) return $linhas;
    $auditoria = dashboardClientesInativosAuditoria($conexao, $clientes, $erro);
    if ($auditoria === false) $auditoria = array();
    foreach ($linhas as &$linha) {
        $id = (int) $linha['id'];
        if (!isset($clientes[$id])) continue;
        $recuperado = dashboardClientesInativosReconstruir($linha, isset($auditoria[$id]) ? $auditoria[$id] : array());
        if ($recuperado !== null) $linha = array_merge($linha, $recuperado);
    }
    unset($linha);
    $comNomes = dashboardClientesInativosNomes($conexao, $linhas, $erro);
    return $comNomes === false ? $linhas : $comNomes;
}

function dashboardClientesInativosReferencia($linha)
{
    if ($linha['situacao'] !== 'Inativo' || $linha['flagAtivo'] !== 'N') return null;
    if (!empty($linha['auditoriaId']) && $linha['historicoConfirmado'] === 'S'
        && isset($linha['vendedorAnteriorId']) && dashboardClientesInativosDataValida($linha['dataInativacao'])) {
        return array('origem' => 'auditoria', 'vendedorId' => (int) $linha['vendedorAnteriorId']);
    }
    // Cadastros antigos podem não ter mais o evento na auditoria. O vínculo
    // atual permite calcular uma sugestão, sem afirmar que comprova o passado.
    $atual = isset($linha['vendedorAtualId']) ? (string) $linha['vendedorAtualId'] : '0';
    if (!ctype_digit($atual)) return null;
    return array('origem' => 'cadastro', 'vendedorId' => (int) $atual);
}

function dashboardClientesInativosEnriquecer($conexao, $empresa, $linhas, &$erro)
{
    if (!$linhas) return $linhas;
    $obrigatorios = array('id', 'descricao', 'tipoId', 'vendedorAtualId', 'flagAtivo', 'flagComercial',
        'dataAtualizacao', 'dataInativacao', 'vendedorAnteriorId', 'vendedorAnterior',
        'auditoriaId', 'historicoConfirmado', 'situacao');
    foreach ($linhas as $linha) {
        $ausentes = array_diff($obrigatorios, array_keys($linha));
        if ($ausentes) {
            $erro = 'A consulta de clientes inativos não retornou os campos necessários: ' . implode(', ', $ausentes) . '.';
            return false;
        }
    }
    $linhas = dashboardClientesInativosRecuperar($conexao, $linhas);
    $carteiras = dashboardClientesInativosCarteiras($conexao, $empresa, $erro);
    if ($carteiras === false) return false;
    $perfis = dashboardClientesInativosPerfis($carteiras);
    foreach ($linhas as &$linha) {
        $linha['tipo'] = dashboardClientesInativosTipo($linha['tipoId']);
        $linha['vendedorSugeridoId'] = 0;
        $linha['vendedorSugerido'] = 'Sem sugestão';
        $linha['aderencia'] = null;
        $linha['aderenciaTexto'] = '';
        $linha['perfilPredominante'] = '';
        $linha['motivoSugestao'] = '';
        $referencia = dashboardClientesInativosReferencia($linha);
        $linha['referenciaVendedor'] = $referencia;
        if ($linha['situacao'] !== 'Inativo') {
            $linha['motivoSugestao'] = 'Cliente já reativado.';
        } elseif ($referencia === null) {
            $linha['motivoSugestao'] = 'Não foi possível identificar o vendedor de referência no cadastro.';
        } else {
            $prefixo = '';
            if ($referencia['origem'] === 'cadastro') {
                $linha['vendedorAnterior'] = 'Histórico pendente';
                $nomeAtual = isset($linha['vendedorAtual']) ? $linha['vendedorAtual'] : 'Vendedor #' . $referencia['vendedorId'];
                if ($referencia['vendedorId'] === 0) $nomeAtual = 'Sem vendedor';
                $linha['referenciaVendedor']['nome'] = $nomeAtual;
                $prefixo = 'Histórico da inativação não confirmado. '
                    . ($referencia['vendedorId'] > 0 ? 'Vendedor atual do cadastro: ' . $nomeAtual . ' (excluído da sugestão). ' : 'Cadastro atual sem vendedor. ');
            }
            $sugestao = dashboardClientesInativosSugerir($linha['tipoId'], $referencia['vendedorId'], $perfis);
            if ($sugestao === null) {
                $linha['motivoSugestao'] = trim((string) $linha['tipoId']) === ''
                    ? 'Cliente sem tipo de contribuinte informado.' : 'Nenhum outro vendedor ativo possui clientes ativos desse tipo.';
            } else {
                $linha['vendedorSugeridoId'] = $sugestao['id'];
                $linha['vendedorSugerido'] = $sugestao['nome'];
                $linha['aderencia'] = $sugestao['percentual'];
                $linha['aderenciaTexto'] = number_format($sugestao['percentual'], 2, ',', '.') . '%';
                $linha['perfilPredominante'] = $sugestao['perfilPredominante'];
                $linha['motivoSugestao'] = $sugestao['quantidadeTipo'] . ' de ' . $sugestao['total'] . ' clientes ativos são desse tipo.';
            }
            $linha['motivoSugestao'] = $prefixo . $linha['motivoSugestao'];
        }
    }
    unset($linha);
    return $linhas;
}

function dashboardClientesInativosMensagemAgenda($cliente, $sugestao, $referencia)
{
    $confirmado = $referencia['origem'] === 'auditoria';
    $nome = trim((string) $sugestao['nome']);
    if ($nome === '') $nome = 'Vendedor #' . $sugestao['id'];
    $episodio = array('versao' => 1, 'cliente' => (int) $cliente['id'],
        'dataInativacao' => dashboardClientesInativosDataValida($cliente['dataInativacao']) ? $cliente['dataInativacao'] : null,
        'historicoConfirmado' => $confirmado ? 'S' : 'N',
        'vendedorAnteriorId' => $confirmado ? $referencia['vendedorId'] : null,
        'vendedorAnteriorRegistrado' => $confirmado ? (string) $cliente['vendedorAnterior'] : '',
        'novoVendedorId' => (int) $sugestao['id'], 'novoVendedorRegistrado' => $nome);
    $mensagem = 'Cliente reativado via dashboard. Vendedor interno atualizado para '
        . $nome . ' (código ' . $sugestao['id'] . ').';
    if ($confirmado) {
        $mensagem .= ' Inativação em ' . $episodio['dataInativacao'] . ', vendedor '
            . $episodio['vendedorAnteriorRegistrado'] . ' (código ' . $referencia['vendedorId'] . ').';
    } else {
        $mensagem .= ' Referência utilizada: vendedor interno atual do cadastro (código '
            . $referencia['vendedorId'] . '), excluído da sugestão. Histórico da inativação não confirmado.';
        if ($episodio['dataInativacao'] !== null) $mensagem .= ' Data de inativação informada no cadastro: ' . $episodio['dataInativacao'] . '.';
    }
    $registro = json_encode($episodio);
    if ($registro === false) throw new \RuntimeException('Não foi possível preparar o histórico da reativação.');
    // A mesma mensagem preserva os dados para leitura futura, sem campo/tabela novos.
    // Nomes são texto na Agenda HTML; o comentário não aparece na tela do cadastro.
    return htmlspecialchars($mensagem, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        . '<!--dashboard-clientes-inativos:v1:' . base64_encode($registro) . '-->';
}

function dashboardClientesInativosLerAgenda($registro)
{
    $mensagem = $registro['agendaMensagem'];
    if (!is_string($mensagem) || strlen($mensagem) > 65535 || (int) $registro['agendaId'] <= 0
        || !dashboardClientesInativosDataValida($registro['agendaData'])
        || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/D', (string) $registro['agendaHora'])) return null;
    $texto = html_entity_decode($mensagem, ENT_QUOTES, 'UTF-8');
    if (!preg_match('/^Cliente reativado via dashboard\. Vendedor interno atualizado para (.+?) \(código ([1-9][0-9]*)\)\./us', $texto, $legado)) return null;
    $episodio = array('dataInativacao' => null, 'vendedorAnteriorId' => null,
        'vendedorAnteriorRegistrado' => '', 'historicoConfirmado' => 'N',
        'novoVendedorId' => (int) $legado[2], 'novoVendedorRegistrado' => $legado[1]);
    if (strpos($mensagem, '<!--dashboard-clientes-inativos:') !== false) {
        if (!preg_match('/<!--dashboard-clientes-inativos:v1:([A-Za-z0-9+\/=]{1,16384})-->$/D', $mensagem, $partes)) return null;
        $dados = json_decode(base64_decode($partes[1], true), true);
        if (!is_array($dados) || !isset($dados['versao'], $dados['cliente'], $dados['historicoConfirmado'],
            $dados['novoVendedorId'], $dados['novoVendedorRegistrado'], $dados['vendedorAnteriorRegistrado'])
            || $dados['versao'] !== 1 || $dados['cliente'] !== (int) $registro['id']
            || !array_key_exists('vendedorAnteriorId', $dados) || !array_key_exists('dataInativacao', $dados)
            || !in_array($dados['historicoConfirmado'], array('S', 'N'), true)
            || !is_int($dados['novoVendedorId']) || $dados['novoVendedorId'] !== (int) $legado[2]
            || !is_string($dados['novoVendedorRegistrado']) || $dados['novoVendedorRegistrado'] !== $legado[1]
            || !is_string($dados['vendedorAnteriorRegistrado'])
            || ($dados['dataInativacao'] !== null && !dashboardClientesInativosDataValida($dados['dataInativacao']))
            || ($dados['historicoConfirmado'] === 'S' && (!is_int($dados['vendedorAnteriorId'])
                || $dados['vendedorAnteriorId'] < 0 || $dados['dataInativacao'] === null))
            || ($dados['historicoConfirmado'] === 'N' && ($dados['vendedorAnteriorId'] !== null
                || $dados['vendedorAnteriorRegistrado'] !== ''))) return null;
        foreach (array_keys($episodio) as $campo) $episodio[$campo] = $dados[$campo];
    }
    return array_merge($episodio, array('agendaId' => (int) $registro['agendaId'],
        'dataAtivacao' => $registro['agendaData'], 'dataHoraAtivacao' => $registro['agendaData'] . ' ' . $registro['agendaHora'],
        'auditoriaId' => null, 'origemAtivacao' => 'agenda'));
}

function dashboardClientesInativosAgendaHistorico($conexao, $parametros, &$erro)
{
    return dashboardIndicadorConsultar($conexao, 'SELECT ' . dashboardClientesInativosCamposCliente() . ",
        a.D085_Id AS agendaId, a.D085_Data AS agendaData, a.D085_Hora AS agendaHora, a.D085_Mensagem AS agendaMensagem
        FROM D085 a INNER JOIN D024 c ON c.D024_Id = a.D085_D024_Id
        WHERE a.D085_Data >= :dataInicio AND a.D085_Data < :fimExclusivo
          AND a.D085_Flag_Manual = 'N' AND a.D085_Entidade = 'D024' AND a.D085_Id_Entidade = c.D024_Id
          AND a.D085_Mensagem LIKE 'Cliente reativado via dashboard. Vendedor interno atualizado para %'
          AND (c.D024_C004_Id = :empresa OR COALESCE(c.D024_C004_Id, 0) = 0) AND c.D024_Flag_Cliente = 'S'
          AND (:cliente = '' OR c.D024_Nome_Empresa LIKE CONCAT('%', :cliente, '%') OR CAST(c.D024_Id AS CHAR) = :cliente)
          AND (:tipoCliente = '' OR c.D024_Flag_Contribuinte = :tipoCliente)
        ORDER BY a.D085_Data DESC, a.D085_Id DESC LIMIT 1001", $parametros, $erro);
}

function dashboardClientesInativosCombinarHistorico($auditoria, $agenda)
{
    $usados = array();
    foreach ($auditoria as &$linha) {
        $candidatos = array();
        foreach ($agenda as $i => $registro) {
            if (isset($usados[$i]) || (int) $linha['id'] !== (int) $registro['id']
                || $linha['novoVendedorId'] !== $registro['novoVendedorId']) continue;
            if (isset($linha['agendaId'])) {
                if ($linha['agendaId'] === $registro['agendaId']) $candidatos[] = $i;
            } elseif ($linha['origemAtivacao'] === 'dashboard-personalizado/atualizar-vendedor.php'
                && abs(strtotime($linha['dataHoraAtivacao']) - strtotime($registro['dataHoraAtivacao'])) <= 2) {
                // Versões antigas não gravavam o vínculo. Só une uma ocorrência
                // inequívoca do dashboard. Os INSERTs podem cruzar o segundo.
                $iguais = array_filter($auditoria, function ($outra) use ($linha, $registro) {
                    return (int) $outra['id'] === (int) $linha['id'] && $outra['novoVendedorId'] === $linha['novoVendedorId']
                        && abs(strtotime($outra['dataHoraAtivacao']) - strtotime($registro['dataHoraAtivacao'])) <= 2;
                });
                if (count($iguais) === 1) $candidatos[] = $i;
            }
        }
        if (count($candidatos) !== 1) continue;
        $i = $candidatos[0]; $usados[$i] = true;
        $registro = $agenda[$i];
        // Mensagens antigas não continham os dados da inativação. Enquanto
        // houver auditoria, conserva a evidência recuperada por ela.
        if ($registro['historicoConfirmado'] !== 'S' && $linha['historicoConfirmado'] === 'S') {
            foreach (array('dataInativacao', 'vendedorAnteriorId', 'vendedorAnteriorRegistrado', 'historicoConfirmado') as $campo) {
                if (isset($linha[$campo])) $registro[$campo] = $linha[$campo];
            }
        }
        $registro['auditoriaId'] = $linha['auditoriaId'];
        $linha = array_merge($linha, $registro);
    }
    unset($linha);
    foreach ($agenda as $i => $registro) if (!isset($usados[$i])) $auditoria[] = $registro;
    return $auditoria;
}

function dashboardClientesInativosHistorico($conexao, $indicador, $empresa, $filtros, &$erro)
{
    $valores = $filtros['valores'];
    $inicio = isset($valores['dataInicio']) ? $valores['dataInicio'] : '';
    $fim = isset($valores['dataFim']) ? $valores['dataFim'] : '';
    if (!dashboardClientesInativosDataValida($inicio) || !dashboardClientesInativosDataValida($fim)
        || $inicio > $fim || (new \DateTime($inicio))->diff(new \DateTime($fim))->days > 30) {
        $erro = 'Selecione um período de até 31 dias para consultar as reativações.';
        return false;
    }
    $parametros = array_merge($valores, array('empresa' => (int) $empresa,
        'fimExclusivo' => (new \DateTime($fim))->modify('+1 day')->format('Y-m-d')));
    $registrosAgenda = dashboardClientesInativosAgendaHistorico($conexao, $parametros, $erro);
    if ($registrosAgenda === false) return false;
    $limitado = count($registrosAgenda) > 1000;
    $agenda = array();
    foreach (array_slice($registrosAgenda, 0, 1000) as $registro) {
        $episodio = dashboardClientesInativosLerAgenda($registro);
        if ($episodio === null) { $limitado = true; continue; }
        unset($registro['agendaMensagem']);
        $agenda[] = array_merge($registro, $episodio);
    }
    // A busca no histórico tem período e volume limitados. O conteúdo serializado
    // é interpretado em PHP, sem LIKE/UNCOMPRESS sobre toda a auditoria.
    $eventos = dashboardIndicadorConsultar($conexao, 'SELECT ' . dashboardClientesInativosCamposCliente() . ",
        a.T162_Id AS eventoId, COALESCE(UNCOMPRESS(a.T162_Registro), a.T162_Registro) AS registro
        FROM T162 a FORCE INDEX (IDX_T162_DataHora) INNER JOIN D024 c ON c.D024_Id = a.T162_Registro_Id
        WHERE a.T162_Tabela = 'D024' AND a.T162_Acao = 'U'
          AND a.T162_DataHora >= :dataInicio AND a.T162_DataHora < :fimExclusivo
          AND (c.D024_C004_Id = :empresa OR COALESCE(c.D024_C004_Id, 0) = 0) AND c.D024_Flag_Cliente = 'S'
          AND (:cliente = '' OR c.D024_Nome_Empresa LIKE CONCAT('%', :cliente, '%') OR CAST(c.D024_Id AS CHAR) = :cliente)
          AND (:tipoCliente = '' OR c.D024_Flag_Contribuinte = :tipoCliente)
        ORDER BY a.T162_DataHora DESC, a.T162_Id DESC LIMIT 1001", $parametros, $erro);
    if ($eventos === false) return false;
    $limitado = $limitado || count($eventos) > 1000;
    $clientes = array();
    $eventosSelecionados = array();
    foreach (array_slice($eventos, 0, 1000) as $evento) {
        $pares = dashboardClientesInativosDecodificar($evento['registro']);
        // Confere a transição cadastral na cadeia de alterações, não apenas a
        // situação comercial ou o preenchimento isolado da data de ativação.
        if ($pares === null) { $limitado = true; continue; }
        $possivel = isset($pares['D024_FLAG_ATIVO']) && $pares['D024_FLAG_ATIVO'][0] === 'N' && $pares['D024_FLAG_ATIVO'][1] === 'S';
        if (!$possivel) continue;
        $id = (int) $evento['id'];
        if (!isset($clientes[$id]) && count($clientes) >= 500) { $limitado = true; continue; }
        unset($evento['registro']);
        $clientes[$id] = $evento;
        $eventosSelecionados[(int) $evento['eventoId']] = true;
    }
    $auditoria = dashboardClientesInativosAuditoria($conexao, $clientes, $erro);
    if ($auditoria === false) return false;
    $linhas = array();
    foreach ($clientes as $id => $cliente) {
        $episodios = dashboardClientesInativosEpisodios($cliente, isset($auditoria[$id]) ? $auditoria[$id] : array());
        if ($episodios['incompleto']) $limitado = true;
        foreach ($episodios['encerrados'] as $episodio) {
            if (!isset($eventosSelecionados[$episodio['auditoriaAtivacaoId']])) continue;
            $linhas[] = array_merge($cliente, $episodio);
        }
    }
    $linhas = dashboardClientesInativosCombinarHistorico($linhas, $agenda);
    foreach ($linhas as &$linha) {
        $linha = array_merge($linha, array('situacao' => 'Reativado', 'vendedorSugeridoId' => 0,
            'vendedorSugerido' => '', 'aderenciaTexto' => '', 'perfilPredominante' => '', 'motivoSugestao' => '',
            'tipo' => dashboardClientesInativosTipo($linha['tipoId'])));
        if ($linha['historicoConfirmado'] !== 'S') $linha['vendedorAnterior'] = 'Histórico pendente';
    }
    unset($linha);
    usort($linhas, function ($a, $b) {
        // Dentro do dia, a auditoria remanescente precede os registros arquivados
        // na Agenda. IDs mantêm a sequência mesmo com relógios PHP/MySQL distintos.
        return strcmp($b['dataAtivacao'], $a['dataAtivacao'])
            ?: ((int) (isset($b['auditoriaAtivacaoId']) ? $b['auditoriaAtivacaoId'] : 0)
                <=> (int) (isset($a['auditoriaAtivacaoId']) ? $a['auditoriaAtivacaoId'] : 0))
            ?: ((int) (isset($b['agendaId']) ? $b['agendaId'] : 0) <=> (int) (isset($a['agendaId']) ? $a['agendaId'] : 0));
    });
    $quantidade = count($linhas);
    $limite = isset($indicador['listagem']['limite']) ? max(1, min(500, (int) $indicador['listagem']['limite'])) : 200;
    $linhas = dashboardClientesInativosNomes($conexao, array_slice($linhas, 0, $limite), $erro);
    if ($linhas === false) return false;
    $publico = dashboardIndicadorDefinicaoPublica($indicador, $filtros['publicos']);
    if ($limitado || $quantidade > $limite) {
        $publico['listagem']['observacaoRodape'] = 'Histórico parcial: refine o período ou informe o cliente para consultar mais registros.';
    }
    return array('indicador' => $publico, 'card' => array('valor' => $quantidade, 'quantidade' => $quantidade, 'formato' => 'numero'),
        'listagem' => $linhas, 'geografia' => array(), 'composicao' => array(), 'grafico' => array(),
        'graficoDetalhes' => array(), 'graficosPorGrupo' => array(), 'calendarios' => array(),
        'calendarioDetalhes' => array(), 'listagemDetalhes' => array(), 'comparativoRepresentantes' => array());
}

function dashboardClientesInativosEstado($linha)
{
    // Alterações entre a consulta e o clique invalidam a operação.
    $estado = array();
    foreach (array('id', 'vendedorAnteriorId', 'vendedorAtualId', 'tipoId',
        'dataInativacao', 'flagAtivo', 'flagComercial', 'dataAtualizacao', 'auditoriaId', 'historicoConfirmado') as $campo) {
        $estado[$campo] = isset($linha[$campo]) ? (string) $linha[$campo] : '';
    }
    return hash('sha256', json_encode($estado));
}

function dashboardClientesInativosAssinar($linha, $indicadorId, $autorizacao, $confUsuario)
{
    $referencia = dashboardClientesInativosReferencia($linha);
    if ($referencia === null || empty($linha['vendedorSugeridoId'])
        || (int) $linha['vendedorSugeridoId'] === 63
        || (int) $linha['vendedorSugeridoId'] === $referencia['vendedorId']) return '';
    $chave = dashboardPersonalizadoChaveAutorizacao($confUsuario);
    if ($chave === false) return '';
    $dados = array('cliente' => (int) $linha['id'],
        'auditoria' => $referencia['origem'] === 'auditoria' ? (int) $linha['auditoriaId'] : 0,
        'origemReferencia' => $referencia['origem'], 'vendedorReferencia' => $referencia['vendedorId'],
        'vendedor' => (int) $linha['vendedorSugeridoId'], 'estado' => dashboardClientesInativosEstado($linha),
        'indicador' => $indicadorId, 'usuario' => (int) $autorizacao['usuario'],
        'empresa' => (int) $autorizacao['empresa'], 'expira' => time() + 900);
    $conteudo = dashboardPersonalizadoBase64UrlCodificar(json_encode($dados));
    return $conteudo . '.' . hash_hmac('sha256', 'clientes-inativos-v3.' . $conteudo, $chave);
}

function dashboardClientesInativosValidarAssinatura($ticket, $autorizacao, $confUsuario)
{
    $chave = dashboardPersonalizadoChaveAutorizacao($confUsuario);
    if ($chave === false || !is_string($ticket) || strlen($ticket) > 4096
        || !preg_match('/^([a-zA-Z0-9_-]+)\.([a-f0-9]{64})$/', $ticket, $partes)
        || !hash_equals(hash_hmac('sha256', 'clientes-inativos-v3.' . $partes[1], $chave), $partes[2])) return false;
    $dados = json_decode(dashboardPersonalizadoBase64UrlDecodificar($partes[1]), true);
    if (!is_array($dados) || empty($dados['cliente']) || empty($dados['vendedor'])
        || (int) $dados['vendedor'] === 63
        || !isset($dados['origemReferencia'], $dados['vendedorReferencia'], $dados['auditoria'])
        || !in_array($dados['origemReferencia'], array('auditoria', 'cadastro'), true)
        || ($dados['origemReferencia'] === 'auditoria' && (int) $dados['auditoria'] <= 0)
        || ($dados['origemReferencia'] === 'cadastro' && (int) $dados['auditoria'] !== 0)
        || empty($dados['estado']) || empty($dados['indicador']) || empty($dados['expira']) || $dados['expira'] < time()
        || (int) $dados['usuario'] !== (int) $autorizacao['usuario']
        || (int) $dados['empresa'] !== (int) $autorizacao['empresa']) return false;
    return $dados;
}

function dashboardClientesInativosExecutarEscrita($conexao, $sql, $parametros)
{
    $sql = dashboardIndicadorPrepararSql($sql, $parametros, $valores, $tipos, $erro);
    if ($erro !== '') throw new \RuntimeException('Dados inválidos para atualizar o cliente.');
    $stmt = mysqli_prepare($conexao, $sql);
    if (!$stmt || !dashboardIndicadorBind($stmt, $tipos, $valores) || !mysqli_stmt_execute($stmt)) {
        if ($stmt) mysqli_stmt_close($stmt);
        throw new \RuntimeException('Não foi possível atualizar o cliente. Nenhuma alteração foi confirmada.');
    }
    $afetados = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $afetados;
}

function dashboardClientesInativosAtualizar($conexao, $dados, $autorizacao)
{
    if (!dashboardClientesInativosPodeAtualizar($autorizacao)) throw new \RuntimeException('A atualização exige acesso de diretor ou ROOT.');
    foreach (array('D024', 'D085', 'T162') as $tabela) {
        $estrutura = dashboardIndicadorConsultar($conexao, "SHOW TABLE STATUS LIKE '" . $tabela . "'", array(), $erro);
        if (!$estrutura || strcasecmp($estrutura[0]['Engine'], 'InnoDB') !== 0) {
            throw new \RuntimeException('A atualização exige cadastro, Agenda e auditoria em InnoDB para garantir a gravação conjunta.');
        }
    }
    if (!mysqli_begin_transaction($conexao)) throw new \RuntimeException('Não foi possível iniciar a atualização.');
    try {
        $parametros = array('empresa' => (int) $autorizacao['empresa'], 'cliente' => (int) $dados['cliente'],
            'vendedor' => (int) $dados['vendedor'], 'usuario' => (int) $autorizacao['usuario']);
        $sqlCliente = 'SELECT ' . dashboardClientesInativosCamposCliente() . " FROM D024 c
            WHERE c.D024_Id = :cliente AND c.D024_Flag_Cliente = 'S'
              AND (c.D024_C004_Id = :empresa OR COALESCE(c.D024_C004_Id, 0) = 0)";
        $linhas = dashboardIndicadorConsultar($conexao, $sqlCliente . "
            AND c.D024_Flag_Ativo = 'N' LIMIT 1 FOR UPDATE", $parametros, $erro);
        if ($linhas === false || count($linhas) !== 1) {
            throw new \RuntimeException('O cadastro mudou. Atualize o indicador antes de tentar novamente.');
        }
        $antes = $linhas[0];
        $recuperados = dashboardClientesInativosRecuperar($conexao, $linhas);
        $cliente = $recuperados[0];
        $referencia = dashboardClientesInativosReferencia($cliente);
        if ($referencia === null || $referencia['origem'] !== $dados['origemReferencia']
            || $referencia['vendedorId'] !== (int) $dados['vendedorReferencia']
            || ($referencia['origem'] === 'auditoria' ? (int) $cliente['auditoriaId'] : 0) !== (int) $dados['auditoria']
            || !hash_equals($dados['estado'], dashboardClientesInativosEstado($cliente))) {
            throw new \RuntimeException('O cadastro ou o histórico mudou. Atualize o indicador antes de tentar novamente.');
        }
        $carteiras = dashboardClientesInativosCarteiras($conexao, $parametros['empresa'], $erro);
        if ($carteiras === false) throw new \RuntimeException('Não foi possível recalcular as carteiras dos vendedores.');
        $sugestao = dashboardClientesInativosSugerir($cliente['tipoId'], $referencia['vendedorId'], dashboardClientesInativosPerfis($carteiras));
        if ($sugestao === null || $sugestao['id'] !== $parametros['vendedor']) {
            throw new \RuntimeException('A sugestão mudou. Atualize o indicador para conferir o novo vendedor.');
        }
        // C007 pode ser MyISAM; a condição decisiva é repetida no próprio UPDATE.
        $afetados = dashboardClientesInativosExecutarEscrita($conexao, "UPDATE D024 c
            INNER JOIN C007 v ON v.C007_Id = :vendedor AND v.C007_Ativo = 'S' AND v.C007_Flag_Vendedor IN ('V', 'S', 'D')
                AND v.C007_Id <> 63
            SET c.D024_C007_Vendedor_Interno = v.C007_Id, c.D024_Flag_Ativo = 'S', c.D024_Flag_Comercial = 'A',
                c.D024_Data_Ativo = CURDATE(), c.D024_Data_Atualizacao = NOW()
            WHERE c.D024_Id = :cliente AND (c.D024_C004_Id = :empresa OR COALESCE(c.D024_C004_Id, 0) = 0)
              AND c.D024_Flag_Ativo = 'N'", $parametros);
        if ($afetados !== 1) throw new \RuntimeException('O cliente ou a situação do vendedor mudou. Atualize o indicador.');
        // Agenda do cad001: N identifica o registro automático e o mantém visível
        // mesmo quando exibirRegistroAgendaManual está desabilitado.
        // Insere após reativar: o trigger nativo D085 pode chamar D024_Ativar_Cliente,
        // que atribui o usuário da Agenda ao vendedor se o cliente ainda estiver I.
        $parametros['mensagemAgenda'] = dashboardClientesInativosMensagemAgenda($cliente, $sugestao, $referencia);
        $agendados = dashboardClientesInativosExecutarEscrita($conexao, "INSERT INTO D085
            (D085_D024_Id, D085_C007_Id, D085_C007_Id_Inclusao, D085_Data, D085_Hora,
             D085_Mensagem, D085_Flag_Manual, D085_D021_Id, D085_Entidade, D085_Id_Entidade)
            VALUES (:cliente, :usuario, :usuario, CURDATE(), CURTIME(), :mensagemAgenda, 'N', 0, 'D024', :cliente)", $parametros);
        if ($agendados !== 1) throw new \RuntimeException('Não foi possível registrar a reativação na Agenda do cliente. Nenhuma alteração foi confirmada.');
        $agendaId = (int) mysqli_insert_id($conexao);
        if ($agendaId <= 0) throw new \RuntimeException('Não foi possível identificar o registro da Agenda. Nenhuma alteração foi confirmada.');
        $parametros['origemRegistro'] = 'dashboard-personalizado/atualizar-vendedor.php agenda=' . $agendaId;
        // Confere novamente depois dos triggers da Agenda, antes de confirmar.
        $depois = dashboardIndicadorConsultar($conexao, $sqlCliente . ' LIMIT 1', $parametros, $erro);
        if (!$depois || (int) $depois[0]['vendedorAtualId'] !== $parametros['vendedor']
            || $depois[0]['flagAtivo'] !== 'S' || $depois[0]['flagComercial'] !== 'A') {
            throw new \RuntimeException('Não foi possível conferir a reativação do cliente.');
        }
        // Mesmo formato do mysql_query do framework: pares antes/depois,
        // serialize e COMPRESS. Usa somente a T162 já existente na mesma base.
        $campos = array('D024_C007_Vendedor_Interno' => 'vendedorAtualId', 'D024_Flag_Ativo' => 'flagAtivo',
            'D024_Flag_Comercial' => 'flagComercial', 'D024_Data_Ativo' => 'dataAtivoCadastro',
            'D024_Data_Inativo' => 'dataInativoCadastro', 'D024_Data_Atualizacao' => 'dataAtualizacao');
        $alteracoes = array();
        foreach ($campos as $campo => $alias) {
            if ((string) $antes[$alias] !== (string) $depois[0][$alias]) $alteracoes[$campo] = array($antes[$alias], $depois[0][$alias]);
        }
        $parametros['registro'] = serialize($alteracoes);
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $parametros['ip'] = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : '';
        $auditados = dashboardClientesInativosExecutarEscrita($conexao, "INSERT INTO T162
            (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_TempoExecucao,
             T162_Registro_Id, T162_Registro, T162_IP)
            VALUES (:usuario, 'U', :origemRegistro, NOW(), 'D024', 0,
                :cliente, COMPRESS(:registro), :ip)", $parametros);
        if ($auditados !== 1) throw new \RuntimeException('Não foi possível registrar a alteração no histórico existente.');
        if (!mysqli_commit($conexao)) throw new \RuntimeException('Não foi possível confirmar a atualização.');
        return array('ok' => true, 'mensagem' => 'Vendedor interno atualizado e cliente reativado. A alteração foi registrada na Agenda e no histórico do cadastro.');
    } catch (\Throwable $e) {
        mysqli_rollback($conexao);
        throw $e;
    }
}
