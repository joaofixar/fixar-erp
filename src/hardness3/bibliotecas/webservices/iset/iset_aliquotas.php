<?php
namespace hardness;

require_once __DIR__ . '/iset_logs.php';

/**
 * Coordena a atualização fiscal posterior ao COMMIT de pedidos ISET.
 *
 * A regra tributária continua centralizada em VEN002::atualizarAliquotas().
 * Esta classe apenas persiste o cursor e limita cada execução a poucos itens.
 */
class iset_aliquotas_
{
    const TAMANHO_LOTE = 20;

    public function iniciar($empresaId, $pedidoId, $pedidoExterno, $operacao, array $logBase = array())
    {
        $empresaId = (int) $empresaId;
        $pedidoId = (int) $pedidoId;
        if ($empresaId <= 0 || $pedidoId <= 0) {
            throw new \Exception('Não foi possível iniciar a atualização de alíquotas do pedido.');
        }

        $total = mysql_query("SELECT COUNT(*) AS quantidade FROM T006 WHERE T006_T005_Id = '{$pedidoId}'");
        $total = $total ? mysql_fetch_assoc($total) : false;
        if ($total === false) {
            throw new \Exception('Não foi possível preparar os itens para atualização de alíquotas.');
        }

        $token = bin2hex(random_bytes(32));
        $externo = mysql_real_escape_string(substr((string) $pedidoExterno, 0, 100));
        $operacao = mysql_real_escape_string(substr((string) $operacao, 0, 50));
        $log = json_encode(integracoesLimparDadosLog($logBase), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $log = mysql_real_escape_string($log === false ? '' : $log);
        $quantidade = (int) $total['quantidade'];
        $sql = "INSERT INTO INT002 (
                    INT002_C004_Id, INT002_T005_Id, INT002_Id_Externo, INT002_Operacao,
                    INT002_Token, INT002_Total_Itens, INT002_Status, INT002_Data_Hora_Inicio,
                    INT002_Log_Base
                ) VALUES (
                    '{$empresaId}', '{$pedidoId}', '{$externo}', '{$operacao}',
                    '{$token}', '{$quantidade}', 'P', NOW(), '{$log}'
                )";
        if (!mysql_query($sql)) {
            throw new \Exception('Não foi possível registrar a atualização de alíquotas. Erro MySQL: ' . (mysql_error() ?: 'sem detalhe retornado pelo banco.'));
        }

        // mysql_insert_id() não é confiável neste legado: triggers e outras
        // rotinas da mesma conexão podem sobrescrever o último ID retornado.
        $consulta = mysql_query("SELECT INT002_Id FROM INT002 WHERE INT002_T005_Id = '{$pedidoId}' AND INT002_C004_Id = '{$empresaId}' LIMIT 1");
        $registro = $consulta ? mysql_fetch_assoc($consulta) : false;
        $id = $registro ? (int) $registro['INT002_Id'] : 0;
        if ($id <= 0) {
            throw new \Exception('Não foi possível identificar a atualização de alíquotas criada.');
        }

        integracoesRegistrarLog(array(
            'empresaId' => $empresaId,
            'integracao' => 'ISET',
            'operacao' => $operacao,
            'status' => INT_STATUS_INFO,
            'etapa' => INT_ETAPA_ATUALIZACAO_ALIQUOTAS,
            'idExterno' => $pedidoExterno,
            'tabelaPrincipal' => 'T005',
            'idPrincipal' => $pedidoId,
            'mensagem' => 'Atualização de alíquotas iniciada.',
            'log' => array_merge($logBase, array(
                'processamentoAliquotasId' => $id,
                'itensAliquotas' => $quantidade,
            )),
        ));

        return array('id' => $id, 'token' => $token, 'pedidoId' => $pedidoId, 'total' => $quantidade);
    }

    public function processarLote($processamentoId, $empresaId, $token = null)
    {
        $processamentoId = (int) $processamentoId;
        $empresaId = (int) $empresaId;
        $processamento = $this->buscar($processamentoId, $empresaId, $token);
        if (!$processamento) {
            throw new \Exception('Processamento de alíquotas não localizado.');
        }
        if ($processamento['INT002_Status'] === 'C') {
            return $this->resposta($processamento);
        }
        if ($processamento['INT002_Flag_Processando'] === 'S'
            && strtotime($processamento['INT002_Data_Hora_Processando']) > strtotime('-5 minutes')) {
            return array_merge($this->resposta($processamento), array('ocupado' => true));
        }

        $travado = mysql_query("UPDATE INT002
            SET INT002_Flag_Processando = 'S', INT002_Status = 'E', INT002_Data_Hora_Processando = NOW()
            WHERE INT002_Id = '{$processamentoId}'
              AND INT002_C004_Id = '{$empresaId}'
              AND (INT002_Flag_Processando <> 'S' OR INT002_Data_Hora_Processando < DATE_SUB(NOW(), INTERVAL 5 MINUTE))");
        if (!$travado || mysql_affected_rows() !== 1) {
            $atual = $this->buscar($processamentoId, $empresaId, $token);
            return array_merge($this->resposta($atual), array('ocupado' => true));
        }

        $processamento = $this->buscar($processamentoId, $empresaId, $token);
        $cursor = (int) $processamento['INT002_Ultimo_T006_Id'];
        $pedidoId = (int) $processamento['INT002_T005_Id'];
        $itens = $this->itensDoLote($pedidoId, $cursor);
        $sucessos = 0;
        $falhas = 0;
        $mensagens = array();

        require_once 'bibliotecas/classes/VEN002.php';
        $ven002 = new VEN002();
        foreach ($itens as $item) {
            try {
                $retorno = $ven002->atualizarAliquotas($pedidoId, $item);
                $erroBanco = mysql_error();
                if ($retorno !== true) {
                    throw new \Exception('VEN002::atualizarAliquotas retornou ' . $this->descreverRetorno($retorno) . '.');
                }
                if ($erroBanco !== '') {
                    throw new \Exception('VEN002::atualizarAliquotas retornou sucesso, mas o MySQL informou: ' . $erroBanco . '.');
                }
                $sucessos++;
            } catch (\Throwable $erro) {
                $falhas++;
                $mensagens[] = 'Item T006=' . (int) $item['T006_Id'] . ', D009=' . (int) $item['D009_Id'] . ': ' . $erro->getMessage();
            }
            $cursor = (int) $item['T006_Id'];
        }

        $concluido = count($itens) < self::TAMANHO_LOTE;
        $errosAnteriores = trim((string) $processamento['INT002_Erros']);
        $erros = trim($errosAnteriores . ($errosAnteriores !== '' && $mensagens ? "\n" : '') . implode("\n", $mensagens));
        $erros = substr($erros, 0, 16000);
        $sql = "UPDATE INT002 SET
                    INT002_Ultimo_T006_Id = '{$cursor}',
                    INT002_Itens_Processados = INT002_Itens_Processados + " . count($itens) . ",
                    INT002_Itens_Sucesso = INT002_Itens_Sucesso + '{$sucessos}',
                    INT002_Itens_Erro = INT002_Itens_Erro + '{$falhas}',
                    INT002_Erros = '" . mysql_real_escape_string($erros) . "',
                    INT002_Flag_Processando = 'N',
                    INT002_Status = '" . ($concluido ? 'C' : 'P') . "',
                    INT002_Data_Hora_Processando = NULL,
                    INT002_Data_Hora_Fim = " . ($concluido ? 'NOW()' : 'NULL') . "
                WHERE INT002_Id = '{$processamentoId}' AND INT002_C004_Id = '{$empresaId}'";
        if (!mysql_query($sql)) {
            throw new \Exception('Não foi possível salvar o progresso das alíquotas.');
        }

        $atual = $this->buscar($processamentoId, $empresaId, $token);
        if ($atual['INT002_Status'] === 'C') {
            $this->registrarConclusao($atual);
            $atual = $this->buscar($processamentoId, $empresaId, $token);
        }
        return $this->resposta($atual);
    }

    public function concluirTodos($processamentoId, $empresaId)
    {
        do {
            $resposta = $this->processarLote($processamentoId, $empresaId);
            if (!empty($resposta['ocupado'])) {
                throw new \Exception('A atualização de alíquotas já está em andamento.');
            }
        } while (empty($resposta['concluido']));
        return $resposta;
    }

    protected function buscar($id, $empresaId, $token = null)
    {
        $filtroToken = $token === null ? '' : " AND INT002_Token = '" . mysql_real_escape_string((string) $token) . "'";
        $consulta = mysql_query("SELECT * FROM INT002 WHERE INT002_Id = '" . (int) $id . "' AND INT002_C004_Id = '" . (int) $empresaId . "'{$filtroToken} LIMIT 1");
        return $consulta ? mysql_fetch_assoc($consulta) : false;
    }

    protected function itensDoLote($pedidoId, $cursor)
    {
        $consulta = mysql_query("SELECT
                T006.T006_Id,
                T006.T006_D009_Id AS D009_Id,
                IFNULL(D009.D009_Origem_Mercadoria, '') AS D009_Origem_Mercadoria,
                T005.T005_Flag_Lista_Preco,
                T005.T005_D024_Id,
                T006.T006_D006_Id,
                IFNULL(T006.T006_T219_Id, 0) AS T006_T219_Id,
                IFNULL(T006.T006_T225_Id, 0) AS T006_T225_Id,
                IFNULL(D001.D001_Peso_Unitario_Kg, 0) AS D001_Peso_Unitario_Kg,
                IFNULL(D001.D001_Cubagem_Unitaria, 0) AS D001_Cubagem_Unitaria,
                IFNULL(D009.D009_Preco_Pauta_ST, 0) AS D009_Preco_Pauta_ST,
                IFNULL(T006.T006_Flag_Origem_Produto, '') AS T006_Flag_Origem_Produto
            FROM T006
            INNER JOIN T005 ON T005.T005_Id = T006.T006_T005_Id
            LEFT JOIN D009 ON D009.D009_Id = T006.T006_D009_Id
            LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
            LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
            WHERE T006.T006_T005_Id = '" . (int) $pedidoId . "'
              AND T006.T006_Id > '" . (int) $cursor . "'
            ORDER BY T006.T006_Id ASC
            LIMIT " . self::TAMANHO_LOTE);
        if (!$consulta) {
            throw new \Exception('Não foi possível consultar os itens para atualizar alíquotas.');
        }
        $itens = array();
        while ($item = mysql_fetch_assoc($consulta)) {
            $itens[] = $item;
        }
        return $itens;
    }

    protected function registrarConclusao(array $processamento)
    {
        if ($processamento['INT002_Flag_Log_Final'] === 'S') {
            return;
        }
        $dados = json_decode($processamento['INT002_Log_Base'], true);
        $dados = is_array($dados) ? $dados : array();
        $dados = array_merge($dados, array(
            'itensAliquotas' => (int) $processamento['INT002_Total_Itens'],
            'itensAliquotasProcessados' => (int) $processamento['INT002_Itens_Processados'],
            'itensAliquotasSucesso' => (int) $processamento['INT002_Itens_Sucesso'],
            'itensAliquotasErro' => (int) $processamento['INT002_Itens_Erro'],
            'errosAliquotas' => $processamento['INT002_Erros'],
        ));
        $temErro = (int) $processamento['INT002_Itens_Erro'] > 0;
        integracoesRegistrarLog(array(
            'empresaId' => (int) $processamento['INT002_C004_Id'],
            'integracao' => 'ISET',
            'operacao' => $processamento['INT002_Operacao'],
            'status' => $temErro ? INT_STATUS_AVISO : INT_STATUS_SUCESSO,
            'etapa' => 'ATUALIZACAO_ALIQUOTAS',
            'idExterno' => $processamento['INT002_Id_Externo'],
            'tabelaPrincipal' => 'T005',
            'idPrincipal' => (int) $processamento['INT002_T005_Id'],
            'mensagem' => $temErro ? 'Pedido importado com pendências na atualização de alíquotas.' : 'Pedido importado com sucesso.',
            'log' => $dados,
        ));
        mysql_query("UPDATE INT002 SET INT002_Flag_Log_Final = 'S' WHERE INT002_Id = '" . (int) $processamento['INT002_Id'] . "' AND INT002_Flag_Log_Final = 'N'");
    }

    protected function descreverRetorno($retorno)
    {
        if (is_bool($retorno)) {
            return $retorno ? 'booleano true' : 'booleano false';
        }
        if (is_scalar($retorno) || $retorno === null) {
            return var_export($retorno, true);
        }
        $json = json_encode($retorno, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json === false ? gettype($retorno) : $json;
    }

    protected function resposta(array $processamento)
    {
        $total = (int) $processamento['INT002_Total_Itens'];
        $processados = (int) $processamento['INT002_Itens_Processados'];
        return array(
            'id' => (int) $processamento['INT002_Id'],
            'pedidoId' => (int) $processamento['INT002_T005_Id'],
            'total' => $total,
            'processados' => $processados,
            'sucessos' => (int) $processamento['INT002_Itens_Sucesso'],
            'falhas' => (int) $processamento['INT002_Itens_Erro'],
            'erros' => trim((string) $processamento['INT002_Erros']),
            'concluido' => $processamento['INT002_Status'] === 'C',
            'percentual' => $total > 0 ? min(100, (int) ceil(($processados / $total) * 100)) : 100,
        );
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_ALIQUOTAS');
}

if (!class_exists(__NAMESPACE__ . '\\iset_aliquotas', false)) {
    class_alias(__NAMESPACE__ . '\\iset_aliquotas_', __NAMESPACE__ . '\\iset_aliquotas');
}
