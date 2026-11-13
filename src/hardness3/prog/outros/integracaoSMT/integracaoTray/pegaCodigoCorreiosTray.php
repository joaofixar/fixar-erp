<?php
namespace hardness;

set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class pegaCodigoCorreiosTray extends BaseIntegracao
{

    public function integrar()
    {
        return $response = json_encode($this->getCodRastreio($this->paramFromGet("T005_Id_Pedido_Externo")));
    }

    public function getCodRastreio($T005_Id_Pedido_Externo)
    {
        require_once('bibliotecas/webservices/tray/conexaoTray.php');
        $conexaoTray = new ConexaoTray();

        if ($conexaoTray->isTudoOk()) {
            $dadosPedido = $conexaoTray->execGet('orders', array('id' => $T005_Id_Pedido_Externo), true);
        }
        log('dados pedido' . $dadosPedido);
        $orders = $dadosPedido['Orders'];
        log('dados pedido orders' . $dadosPedido);

        $orders = $orders[0];
        log('dados order' . $dadosPedido);

        $order = (array) $orders['Order'];
        log('dados dentro order' . $dadosPedido);

        log('Retorno do codigo de rastreio do pedido na Tray: ' . $order);

        $codigoRastreio = $order['sending_code'];

        if($codigoRastreio != '' || $codigoRastreio != null){
            return $codigoRastreio;
        } else {
            $codigoRastreio = null;
            return $codigoRastreio;
        }
    }
}
