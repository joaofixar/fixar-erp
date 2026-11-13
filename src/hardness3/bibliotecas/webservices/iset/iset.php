<?php
namespace hardness;

require_once __DIR__ . '/iset_cliente.php';
require_once __DIR__ . '/iset_sku.php';
require_once __DIR__ . '/iset_configuracoes.php';
require_once __DIR__ . '/iset_historico.php';
require_once __DIR__ . '/iset_idempotencia.php';
require_once __DIR__ . '/iset_importador.php';
require_once __DIR__ . '/iset_produtos.php';
require_once __DIR__ . '/iset_status_pedidos.php';
require_once __DIR__ . '/iset_consultas_produtos.php';
require_once __DIR__ . '/iset_aliquotas.php';
require_once __DIR__ . '/iset_logs.php';
require_once __DIR__ . '/iset_normalizador.php';
require_once __DIR__ . '/iset_validador.php';

class iset_
{
    public function consultarPedidoParaImportacao($pedidoId)
    {
        $resposta = (new ISET_CLIENTE())->buscarPedido($pedidoId);
        if (empty($resposta['ok'])) {
            return $resposta;
        }

        $pedido = (new ISET_NORMALIZADOR())->normalizar($resposta['dados']);
        return array(
            'ok'        => true,
            'status'    => isset($resposta['status']) ? (int) $resposta['status'] : 200,
            'mensagem'  => 'Pedido consultado com sucesso.',
            'pedido'    => $pedido,
            'validacao' => (new ISET_VALIDADOR())->validar($pedido),
        );
    }

    public function processarPedido($pedidoId, array $opcoes = array())
    {
        $consulta = $this->consultarPedidoParaImportacao($pedidoId);
        if (empty($consulta['ok'])) {
            throw new \Exception(isset($consulta['mensagem']) ? $consulta['mensagem'] : 'Não foi possível consultar o pedido na ISET.');
        }

        $opcoes['idempotencia'] = new ISET_IDEMPOTENCIA();
        $importador = new ISET_IMPORTADOR();
        return $importador->importarPedido($consulta['pedido'], $opcoes);
    }

    public function processarWebhook($pedidoId, array $opcoes = array())
    {
        global $g;
        $consulta = $this->consultarPedidoParaImportacao($pedidoId);
        if (empty($consulta['ok'])) {
            return array(
                'importado' => false,
                'status' => INT_STATUS_ERRO,
                'etapa' => 'CONSULTA_API',
                'mensagem' => 'Não foi possível consultar o pedido na ISET.',
                'detalhes' => array(
                    'consultaIset' => array(
                        'pedidoConsultado' => false,
                        'statusHttp' => isset($consulta['status']) ? (int) $consulta['status'] : 0,
                        'mensagem' => isset($consulta['mensagem'])
                            ? integracoesMascararTextoSensivel($consulta['mensagem'])
                            : 'Sem mensagem retornada pela ISET.',
                    ),
                ),
            );
        }

        $importador = new ISET_IMPORTADOR();
        $pagamento = $importador->pedidoPago($consulta['pedido']['raw']);
        if (empty($pagamento['pago'])) {
            return array('importado' => false, 'status' => INT_STATUS_AVISO, 'etapa' => 'PAGAMENTO_PENDENTE', 'mensagem' => $pagamento['motivo'], 'detalhes' => array('pagamento' => $pagamento));
        }

        $existente = $importador->pedidoJaImportado($consulta['pedido']);
        if ($existente > 0) {
            return array('importado' => false, 'status' => INT_STATUS_AVISO, 'etapa' => 'PEDIDO_JA_IMPORTADO', 'mensagem' => 'Pedido já importado anteriormente.', 'detalhes' => array('pedidoExistente' => $existente, 'pagamento' => $pagamento));
        }

        $opcoes['operacao'] = INT_OPERACAO_IMPORTACAO_AUTOMATICA;
        $opcoes['log'] = array_merge(
            isset($opcoes['log']) && is_array($opcoes['log']) ? $opcoes['log'] : array(),
            array(
                'origem' => 'WEBHOOK',
                'consultaIset' => array(
                    'pedidoConsultado' => true,
                    'statusHttp' => isset($consulta['status']) ? (int) $consulta['status'] : 200,
                ),
                'pagamento' => $pagamento,
            )
        );
        $opcoes['idempotencia'] = new ISET_IDEMPOTENCIA();

        try {
            $resultado = $importador->importarPedido($consulta['pedido'], $opcoes);
        } catch (iset_idempotencia_conflito $conflito) {
            $detalhes = $conflito->detalhes();

            return array(
                'importado' => false,
                'status' => INT_STATUS_AVISO,
                'etapa' => 'PEDIDO_JA_IMPORTADO',
                'mensagem' => $conflito->getMessage(),
                'detalhes' => array_merge(array('pagamento' => $pagamento), $detalhes),
            );
        } catch (\Throwable $erroImportacao) {
            /*
             * O importador conhece a etapa e já registrou a falha em
             * IMPORTACAO_AUTOMATICA. O endpoint não deve registrar o mesmo
             * erro novamente como WEBHOOK.
             */
            return array(
                'importado' => false,
                'registrarLogWebhook' => false,
                'status' => INT_STATUS_ERRO,
                'etapa' => INT_ETAPA_VALIDACAO,
                'mensagem' => 'Falha já registrada durante a importação automática.',
                'detalhes' => array(
                    'importacaoRegistrada' => true,
                    'erro' => integracoesDiagnosticoExcecao($erroImportacao),
                ),
            );
        }

        try {
            $aliquotas = (new ISET_ALIQUOTAS())->concluirTodos($resultado['processamento']['id'], (int) $g['empresaAtual']);
        } catch (\Throwable $erroAliquotas) {
            return array(
                'importado' => true,
                'registrarLogWebhook' => true,
                'status' => INT_STATUS_ERRO,
                'etapa' => INT_ETAPA_ATUALIZACAO_ALIQUOTAS,
                'mensagem' => 'Pedido importado, mas a atualização de alíquotas foi interrompida.',
                'pedidoId' => (int) $resultado['pedidoId'],
                'detalhes' => array(
                    'pagamento' => $pagamento,
                    'pedidoErpId' => (int) $resultado['pedidoId'],
                    'erro' => integracoesDiagnosticoExcecao($erroAliquotas),
                ),
            );
        }
        $temFalhas = !empty($aliquotas['falhas']);

        return array(
            'importado' => true,
            'status' => $temFalhas ? INT_STATUS_AVISO : INT_STATUS_SUCESSO,
            'etapa' => INT_ETAPA_ATUALIZACAO_ALIQUOTAS,
            'mensagem' => $temFalhas ? 'Pedido importado com pendências na atualização de alíquotas.' : 'Pedido importado com sucesso.',
            'pedidoId' => (int) $resultado['pedidoId'],
            'detalhes' => array('pagamento' => $pagamento, 'aliquotas' => $aliquotas),
        );
    }

}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET');
}

if (!class_exists(__NAMESPACE__ . '\\iset', false)) {
    class_alias(__NAMESPACE__ . '\\iset_', __NAMESPACE__ . '\\iset');
}
