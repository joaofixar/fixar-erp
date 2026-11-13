<?php
namespace hardness;

/* INTEGRAÇÃO E-COMMERCE TRAY
 *
 * URL's da API utilizadas
 * https://api.commerce.tray.com.br/products - POST - Adicionar produtos
 * https://api.commerce.tray.com.br/products/{id} - PUT - Atualizar produtos
 * https://api.commerce.tray.com.br/products/variants - POST - Adicionar variações de produtos
 * https://api.commerce.tray.com.br/products/variants/{id} - PUT - Atualizar variações de produtos
 * 
 */

definirClasse('ENVIODADOSNFTRAY');
class EnvioDadosNFTray_ {

    public function __construct() {
    }

    public function enviarDados($T005_Id = 0, $T007_Id = 0) {

        $reposta = array();

        if ($T005_Id = 0 && $T007_Id = 0) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível enviar dados para tray. T005_Id e T007_Id estão zeradas.');
        }

        require_once('bibliotecas/webservices/tray/conexaoTray.php');
        $conexaoTray = new ConexaoTray();
        if (!$conexaoTray->isTudoOk()) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível enviar dados para tray. Falha na conexão com tray: ' . $conexaoTray->getMsgErro());
        }

        $dadosNF = $this->getDadosNF($T005_Id, $T007_Id);

        if (empty($dadosNF)) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível enviar dados para tray. Dados para envio não encontrados!');
        }

        $retornoTray['NF'] = $this->enviarDadosNF($conexaoTray, $dadosNF);
        $retornoTray['Transp'] = $this->enviarDadosTransporte($conexaoTray, $dadosNF['T007_Id'], $dadosNF['T005_Id_Pedido_Externo']);

        return array('sucesso' => true, 'mensagem' => 'Retorno Tray: ' . \json_encode($retornoTray));
    }

    private function getDadosNF($T005_Id, $T007_Id) {

        $sql = "SELECT T005_Id_Pedido_Externo,
                       T005_Id,
                       T007_Id,
                       T007_TX_Resposta_Codigo,
                       T007_Data_Emissao,
                       T007_Numero_Nota_Fiscal,
                       T007_Serie,
                       T007_Valor_Total,
                       T007_Chave_Acesso_Nfe,
                       T007_Arquivo_XML
                FROM T007
                JOIN T005 ON T005_Id = T007_T005_Id ";

        if ($T007_Id > 0) {
            $sql .= " WHERE T007_Id = '{$T007_Id}'";
        } else {
            $sql .= " WHERE T007_T005_Id = '{$T005_Id}'";
        }

        return mysql_fetch_assoc(mysql_query($sql));
    }

    private function getDadosItensNF($dadosNF) {

        $dadosItensNF = array();
        $T007_Id = $dadosNF['T007_Id'];

        $sql = "select
                    distinct
                    D185_Integracao_Id,
                    coalesce(D186_Integracao_Id, 0) as D186_Integracao_Id,
                    D006_Codigo_CFOP
                from
                    T007
                join T008 on T008_T007_Id = T007_Id
                join D006 on T008_D006_Id = D006_Id
                join D009 on T008_D009_Id = D009_Id
                join D186 on D186_D001_Id = D009_D001_Id
                join D185 on D186_D185_Id = D185_Id
                where T007_Id = '{$T007_Id}' ";
  
        $result = mysql_query($sql);
  
        while($row = mysql_fetch_assoc($result)) {

            $itemNF = array("product_id" => $row['D185_Integracao_Id'],
                            "cfop" => $row['D006_Codigo_CFOP']);

            if ($row['D186_Integracao_Id'] > 0) {
                $itemNF["variation_id"] = $row['D186_Integracao_Id'];
            }

            $dadosItensNF[] = $itemNF;
        }
  
        return $dadosItensNF;
    }

    private function enviarDadosNF($conexaoTray, $dadosNF) {

        $dadosItensNF = $this->getDadosItensNF($dadosNF);

        $paraTray = array('issue_date'  => $dadosNF['T007_Data_Emissao'],
                          'number'      => $dadosNF['T007_Numero_Nota_Fiscal'],
                          'serie'       => $dadosNF['T007_Serie'],
                          'value'       => $dadosNF['T007_Valor_Total'],
                          'key'         => $dadosNF['T007_Chave_Acesso_Nfe'],
                          'xml_danfe'   => $dadosNF['T007_Arquivo_XML'],
                          'ProductCfop' => $dadosItensNF
                         );

        $idOrder = $dadosNF['T005_Id_Pedido_Externo'];
        return $conexaoTray->execPost("orders/{$idOrder}/invoices", $paraTray, true);
    }

    private function enviarDadosTransporte($conexaoTray, $T007_Id, $T005_Id_Pedido_Externo) {

        $deTray = $conexaoTray->execGet("orders/{$T005_Id_Pedido_Externo}", array(), true);
		$order = (array) $deTray['Order'];

        if ($order['sending_date'] <> "0000-00-00" && $order['sending_date'] <> "") {
            return "Informação de data de envio já existente na tray, nenhuma informação de transporte foi enviada!";
        }

        $dadosTransporte = $this->getDadosTransporte($T007_Id);

        if (empty($dadosTransporte)) {
            return "Dados de transporte não encontrados para enviar para tray!";
        }

        $hoje = new \DateTime();
        $paraTray = array("Order" => array('sending_date' => $hoje->format('Y-m-d')));

        if (empty($order['shipment'])) {
            $paraTray["Order"]['shipment'] = $dadosTransporte["D022_Descricao_Ecommerce"];
        }

        if (empty($order['sending_code'])) {
            $paraTray["Order"]['sending_code'] = $dadosTransporte["T007_Numero_Nota_Fiscal"];
        }

        if (empty($order['tracking_url']) && !empty($dadosTransporte["D022_Url_Rastreio"])) {
            $paraTray["Order"]['tracking_url'] = $dadosTransporte["D022_Url_Rastreio"];
        }

        return $conexaoTray->execPut("orders/{$T005_Id_Pedido_Externo}", $paraTray, true);
    }

    private function getDadosTransporte($T007_Id) {

        $sql = "SELECT T007_Numero_Nota_Fiscal,
                       D022_Descricao_Ecommerce, 
                       D022_Url_Rastreio
                FROM T007
                LEFT JOIN D022 ON D022_Id = T007_D022_Id
                WHERE T007_Id = '{$T007_Id}'";

        return mysql_fetch_assoc(mysql_query($sql));
    }
}