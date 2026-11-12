<?php
namespace hardness;

require_once __DIR__ . '/iset_cliente.php';
require_once __DIR__ . '/iset_configuracoes.php';
require_once __DIR__ . '/iset_logs.php';
require_once __DIR__ . '/iset_status_pedidos_controle.php';

/** Sincroniza em segundo plano o último status Hardness de pedidos importados da ISET. */
class iset_status_pedidos_
{
    const POR_EXECUCAO = 50;

    public function marcarPedidoPendenteAutomatico($empresaId, $T005_Id)
    {
        return $this->controle()->marcarPendente($empresaId, $T005_Id);
    }

    public function sincronizarPendentesAutomaticos($empresaId, $limite = self::POR_EXECUCAO)
    {
        $empresaId = (int) $empresaId;
        $limite = max(1, min(200, (int) $limite));
        $resumo = array('processados' => 0, 'atualizados' => 0, 'ignorados' => 0, 'falhos' => 0);
        if ($empresaId <= 0) return $resumo;

        try {
            $this->controle()->verificarDisponibilidade();
            $sqlINT008 = <<<SQL
                SELECT INT008_T005_Id
                FROM INT008
                WHERE INT008_C004_Id = '{$empresaId}'
                    AND INT008_Status IN ('P', 'E')
                    AND INT008_Proxima_Tentativa <= NOW()
                    AND (INT008_Flag_Processando <> 'S' OR INT008_Data_Hora_Processando < DATE_SUB(NOW(), INTERVAL 10 MINUTE))
                ORDER BY INT008_Proxima_Tentativa,
                    INT008_Id
                LIMIT {$limite}
SQL;
            $consulta = mysql_query($sqlINT008);
            if (!$consulta) throw new \Exception('Erro ao carregar pendências de status ISET: ' . mysql_error());

            $T005_Ids = array();
            while ($INT008 = mysql_fetch_assoc($consulta)) $T005_Ids[] = (int) $INT008['INT008_T005_Id'];
            foreach ($T005_Ids as $T005_Id) {
                $resultado = $this->sincronizarPendente($empresaId, $T005_Id);
                if ($resultado === null) continue;
                $resumo['processados']++;
                $resumo[$resultado['resultado']]++;
            }
        } catch (\Throwable $erro) {
            $resumo['erro'] = $erro->getMessage();
            error_log('ISET: atualização automática de status bloqueada: ' . $erro->getMessage());
        }

        return $resumo;
    }

    private function sincronizarPendente($empresaId, $T005_Id)
    {
        $INT008 = $this->controle()->adquirir($empresaId, $T005_Id, true);
        if (!$INT008) return null;

        try {
            $resultado = $this->atualizarPendente($INT008);
        } catch (\Throwable $erro) {
            $resultado = $this->falha('FALHA_INTERNA', 'Falha inesperada ao atualizar status do pedido na ISET: ' . integracoesMascararTextoSensivel($erro->getMessage()));
            $this->registrarEvento($empresaId, $T005_Id, '', '', '', INT_STATUS_ERRO, $resultado['mensagem'], array(
                'tentativa' => $INT008['INT008_Token_Processamento'],
                'erro' => integracoesDiagnosticoExcecao($erro),
            ));
        }

        try {
            $this->controle()->finalizar($INT008, $resultado);
        } catch (\Throwable $erro) {
            error_log('ISET: falha finalizando status do pedido T005 #' . $T005_Id . ': ' . $erro->getMessage());
            $resultado = $this->falha('FALHA_FILA', 'Falha ao finalizar pendência de status ISET: ' . integracoesMascararTextoSensivel($erro->getMessage()));
        }

        return $resultado;
    }

    private function atualizarPendente(array $INT008)
    {
        $empresaId = (int) $INT008['INT008_C004_Id'];
        $T005_Id = (int) $INT008['INT008_T005_Id'];
        $T005 = $this->obterPedido($empresaId, $T005_Id);
        if (!$T005) return $this->ignorado('PEDIDO_INEXISTENTE', 'Pedido não existe mais na empresa da pendência ISET.');

        if ((string) $T005['T005_Flag_Plataforma_Ecommerce'] !== '7' || trim((string) $T005['T005_Id_Pedido_Externo']) === '') {
            return $this->ignorado('PEDIDO_NAO_ISET', 'Pedido não pertence mais à integração ISET ou não possui identificador externo.');
        }

        $statusHardness = (string) $T005['T005_Flag_Status'];
        $mapeamento = $this->statusIset($statusHardness);
        if (!$mapeamento['ok']) {
            $mensagem = 'A configuração do mapeamento de status ISET é inválida: ' . implode(' ', $mapeamento['erros']);
            $this->registrarEvento($empresaId, $T005_Id, $T005['T005_Id_Pedido_Externo'], $statusHardness, '', INT_STATUS_ERRO, $mensagem, array(
                'tentativa' => $INT008['INT008_Token_Processamento'],
                'mapeamentoStatus' => 'C031.IsetStatusPedidosMapeamentoJson',
                'errosMapeamento' => $mapeamento['erros'],
            ));
            return $this->falha('FALHA_CONFIGURACAO', $mensagem);
        }

        $statusIset = $mapeamento['statusIset'];
        if ($statusIset === 0) {
            return $this->ignorado('STATUS_NAO_MAPEADO', 'Status Hardness ' . $statusHardness . ' não possui atualização correspondente na ISET.');
        }

        $pedidoIset = filter_var($T005['T005_Id_Pedido_Externo'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$pedidoIset) {
            $mensagem = 'Pedido ISET inválido no T005 #' . $T005_Id . '.';
            $this->registrarEvento($empresaId, $T005_Id, $T005['T005_Id_Pedido_Externo'], $statusHardness, $statusIset, INT_STATUS_ERRO, $mensagem, array(
                'tentativa' => $INT008['INT008_Token_Processamento'],
            ));
            return $this->falha('PEDIDO_ISET_INVALIDO', $mensagem);
        }

        // Se o worker perdeu a posse, não pode fazer uma chamada remota em nome da fila atual.
        if (!$this->controle()->possui($INT008)) {
            return $this->ignorado('POSSE_PERDIDA', 'A pendência de status foi assumida por outra execução.');
        }

        $resposta = $this->cliente()->atualizarStatusPedido($pedidoIset, $statusIset);
        $statusLog = !empty($resposta['ok']) ? INT_STATUS_SUCESSO : INT_STATUS_ERRO;
        if (!$this->registrarEvento($empresaId, $T005_Id, $pedidoIset, $statusHardness, $statusIset, $statusLog, $resposta['mensagem'], array(
            'tentativa' => $INT008['INT008_Token_Processamento'],
            'statusHttp' => isset($resposta['status']) ? (int) $resposta['status'] : 0,
            'requisicaoExecutada' => !empty($resposta['requisicaoExecutada']),
            'chamadas' => isset($resposta['chamadas']) ? $resposta['chamadas'] : array(),
            'resposta' => isset($resposta['dados']) ? $resposta['dados'] : array(),
        ))) {
            return $this->falha('FALHA_LOG', 'Falha ao registrar a atualização de status ISET.');
        }

        if (empty($resposta['ok'])) {
            return $this->falha('FALHA_API', $resposta['mensagem']);
        }

        return array('resultado' => 'atualizados', 'motivo' => 'ATUALIZADO', 'mensagem' => 'Status do pedido atualizado na ISET.');
    }

    private function obterPedido($empresaId, $T005_Id)
    {
        $empresaId = (int) $empresaId;
        $T005_Id = (int) $T005_Id;
        $sqlT005 = <<<SQL
            SELECT T005_Id,
                T005_C004_Id,
                T005_Id_Pedido_Externo,
                T005_Flag_Plataforma_Ecommerce,
                T005_Flag_Status
            FROM T005
            WHERE T005_Id = '{$T005_Id}'
                AND T005_C004_Id = '{$empresaId}'
SQL;
        $consulta = mysql_query($sqlT005);
        if (!$consulta) throw new \Exception('Não foi possível consultar o pedido para atualizar status ISET: ' . mysql_error());
        return mysql_fetch_assoc($consulta);
    }

    private function statusIset($statusHardness)
    {
        $mapeamento = $this->configuracoes()->obterStatusPedidosMapeamento();
        if (!$mapeamento['ok']) {
            return array('ok' => false, 'erros' => $mapeamento['erros'], 'statusIset' => 0);
        }

        $mapa = $mapeamento['mapa'];
        return array(
            'ok' => true,
            'erros' => array(),
            'statusIset' => isset($mapa[(string) $statusHardness]) ? (int) $mapa[(string) $statusHardness] : 0,
        );
    }

    private function registrarEvento($empresaId, $T005_Id, $pedidoIset, $statusHardness, $statusIset, $status, $mensagem, array $detalhes)
    {
        return integracoesRegistrarLog(array(
            'empresaId' => $empresaId,
            'integracao' => 'ISET',
            'operacao' => INT_OPERACAO_ATUALIZAR_STATUS_PEDIDO,
            'status' => $status,
            'etapa' => INT_ETAPA_ATUALIZACAO_STATUS_PEDIDO,
            'tabelaPrincipal' => 'T005',
            'idPrincipal' => $T005_Id,
            'idExterno' => $pedidoIset,
            'mensagem' => $mensagem,
            'log' => array_merge(array(
                'pedidoHardness' => array('T005_Id' => (int) $T005_Id),
                'pedidoIset' => (string) $pedidoIset,
                'statusHardness' => (string) $statusHardness,
                'statusIset' => (int) $statusIset,
            ), $detalhes),
        ));
    }

    private function falha($motivo, $mensagem)
    {
        return array('resultado' => 'falhos', 'motivo' => $motivo, 'mensagem' => $mensagem);
    }

    private function ignorado($motivo, $mensagem)
    {
        return array('resultado' => 'ignorados', 'motivo' => $motivo, 'mensagem' => $mensagem);
    }

    protected function cliente()
    {
        return new ISET_CLIENTE();
    }

    protected function configuracoes()
    {
        return new ISET_CONFIGURACOES();
    }

    protected function controle()
    {
        return new ISET_STATUS_PEDIDOS_CONTROLE();
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_STATUS_PEDIDOS');
}

if (!class_exists(__NAMESPACE__ . '\\iset_status_pedidos', false)) {
    class_alias(__NAMESPACE__ . '\\iset_status_pedidos_', __NAMESPACE__ . '\\iset_status_pedidos');
}
