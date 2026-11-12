<?php
namespace hardness;
definirClasse('FIN007');
class FIN007_
{

    /**
     * calculaOutrosPedidos
     *
     * @param array $array
     * @return String
     */
    public function calculaOutrosPedidos($array)
    {   
        global $g;
        $D024_Id = $array['todosCampos']['D024_Id'];
        $T005_Id = $array['todosCampos']['T005_Id'];
        $dadosCliente = mysql_query("SELECT D024_Valor_Limite, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
        $dadosCliente = mysql_fetch_array($dadosCliente);    
        $filtroSaldo = "D024_Id = '{$D024_Id}'";
        if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){
            $filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";
        }
        $sql     = mysql_query("SELECT sum(T005_Valor_Total)
                                FROM T005
                           LEFT JOIN T007 ON T007_T005_Id=T005_Id
                           LEFT JOIN D024 ON D024_Id=T005_D024_Id
                               WHERE $filtroSaldo
                                 AND T005_Id!='$T005_Id'
                                 AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
                                 AND T005_Flag_Status!=8
                                 AND T007_Id is null");
        $retorno = mysql_fetch_array($sql);
        return $retorno[0];
    }
    
    /**
     * formaPagamento
     *
     * @param array $array
     * @return String
     */
    public function formaPagamento($array)
    {  
        $T005_Id = $array['todosCampos']['T005_Id'];
        $sql     = mysql_query("SELECT T090_Prazos_Dias,
                                 T090_Valor_Parcela,
                                 D027_Portador,
                                 T090_Data_Vencimento
                                 FROM T090
                                 LEFT JOIN D027 ON D027_Id=T090_D027_Id                                
        						 WHERE T090_T005_Id='$T005_Id'");
        $count   = 0;
        $pgts    = array();
        while ($retorno = mysql_fetch_array($sql)) {
            $pgts[$count] = array(
                'prazo' => $retorno['T090_Prazos_Dias'],
                'valorParcela' => $retorno['T090_Valor_Parcela'],
                'portador' => $retorno['D027_Portador'],
                'vencimento' => $retorno['T090_Data_Vencimento']
            );
            $count++;
        }
        return $pgts;
    }
    
    /**
     * formaPagamentoNota
     *
     * @param array $array
     * @return String
     */    
    public function formaPagamentoNota($array)
    {
        $T007_Id = $array['todosCampos']['T007_Id'];
        $sql     = mysql_query("SELECT T118_Prazos_Dias,
                                 T118_Valor_Parcela,
                                 D027_Portador,
                                 T118_Data_Vencimento
                                 FROM T118
                                 LEFT JOIN D027 ON D027_Id=T118_D027_Id                                
        						 WHERE T118_T007_Id='$T007_Id'");
        $count   = 0;
        $pgts    = array();
        while ($retorno = mysql_fetch_array($sql)) {
            $pgts[$count] = array(
                'prazo' => $retorno['T118_Prazos_Dias'],
                'valorParcela' => $retorno['T118_Valor_Parcela'],
                'portador' => $retorno['D027_Portador'],
                'vencimento' => $retorno['T118_Data_Vencimento']
            );
            $count++;
        }
        return $pgts;
    }
    
    /**
     * validaCampoRetornarAlmx
     *
     * @param String $T005_Id
     * @return String/bool
     */    
    public function validaCampoRetornarAlmx($T005_Id)
    {   
        global $g;    
        $cSql_T005 = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $mT005     = mysql_fetch_array($cSql_T005);
        
        if ($mT005['T005_Flag_Status'] == '4') {
            return "Pedido já esta em AGUARDANDO ENTREGA! Não pode ser alterado.";
        }
        
        if ($mT005['T005_Flag_Status'] == '8') {
            return "Pedido esta CANCELADO!, não pode ser alterado";
        }
        
        /**
         * Foi solicitaodo que esse IF fosse comentado, pois o campo T005_Flag_Almoxarifado não é usado no sistema novo
         */
        
        //if (($mT005['T005_Flag_Almoxarifado']=='N') AND
        //			($mT005['T005_Flag_Status']!='9')) 
        //		{
        //			return "Pedido não está no almoxarifado!";
        //		}
        
        if (($mT005['T005_Flag_Almoxarifado'] == 'S') AND ($mT005['T005_Flag_Status'] == '9')) {
            return "O Pedido não foi Liberado pelo Dpto Financeiro, Apenas o vendedor poderá retornar este pedido!";
        }
        mysql_query("UPDATE T005 SET T005_Flag_Almoxarifado='N',
									T005_Flag_Travar_Pedido=2,
									T005_Flag_Status=0,
                                    T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),
									T005_Flag_Reenviado_Almoxarifado='S',
                                    T005_Flag_Reenviado_Almoxarifado_Lido='N',
									T005_Flag_Separacao='N',
									T005_FLag_Separacao_Liberar='N'
								WHERE T005_Id='{$T005_Id}'");
        
        //Insere registros no histórico do pedido (T178)
        mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Retornou para o Vendedor','')");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }
        
        // Atualiza notificação de análise de crédito
        //require_once('bibliotecas/classes/NOTIFICACOES.php');
        //$NOTIFICACOES= new NOTIFICACOES();
        //$NOTIFICACOES->liberarCredito();
        //$NOTIFICACOES->pedidoRetornoAlmoxarifado();
        //$NOTIFICACOES->pedidoCreditoNaoLiberado();
        //$NOTIFICACOES->pedidoCreditoLiberado();
        return true;
    }
    
    /**
     * updateCheckboxAguardar
     *
     * @param String $T005_Id
     * @param String $selected
     * @return String/bool
     */     
    public function updateCheckboxAguardar($T005_Id, $selected)
    {        
        global $g;
        
        $cSql_T005 = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        
        $mT005 = mysql_fetch_array($cSql_T005);
        if ($T005_Id <= 0) {
            return "T005_Id nao existe!";
        }
        
        if ($selected == '1') {
            $cClausula = "UPDATE T005 SET T005_Flag_Aguardar_Liberacao='1' WHERE T005_Id='{$T005_Id}'";
        } else {
            $cClausula = "UPDATE T005 SET T005_Flag_Aguardar_Liberacao='2' WHERE T005_Id='{$T005_Id}'";
        }
        mysql_query($cClausula);

        // Atualiza notificação de análise de crédito
        //require_once('bibliotecas/classes/NOTIFICACOES.php');
        //$NOTIFICACOES= new NOTIFICACOES();
        //$NOTIFICACOES->liberarCredito();

        return true;
    }
    
    /**
     * naoLiberar
     *
     * @param String $T005_Id
     * @param String $observacao
     * @return String/bool
     */     
    public function naoLiberar($T005_Id, $observacao)
    {   
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $sqlT005 = mysql_query("SELECT T005_Flag_Primeira_Liberacao_Credito FROM T005 WHERE T005_Id = '{$T005_Id}'");
        $T005    = mysql_fetch_array($sqlT005);
        //Status Possíveis 
        // 0 - Não passou pela primeira liberação ainda
        // 1 - Não Aprovado na primeira liberação
        // 2 - Aprovado na primeira liberação
        if ($T005['T005_Flag_Primeira_Liberacao_Credito'] == 0) {
            $mensagem = "Não aprovado na primeira liberação";
        } else {
            $mensagem = "Não liberado";
        }
        
        $observacao = date('d/m/Y H:i:s') . ' ' . $observacao;
        $cSql = mysql_query("UPDATE T005 SET T005_Flag_Status='9' , T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),
		                                 T005_Liberacao_Credito_Id='{$g['usuarioAtual']}' ,
		                                 T005_Flag_Travar_Pedido=2,
		                                 T005_Flag_Separacao='N',
		                                 T005_Motivo_Nao_Liberado='{$observacao}',
		                                 T005_Flag_Aguardar_Liberacao='',
		                                 T005_Flag_Notificacao_Financeiro='N',
		                                 T005_Flag_Primeira_Liberacao_Credito='1',
		                                 T005_Liberou_Credito_Valor_Total='',
		                                 T005_Liberou_Credito_Desconto='',
		                                 T005_Liberou_Credito_Prazo_Medio='',
                                         T005_Liberou_D024_Id=''
		                           WHERE T005_Id='{$T005_Id}'", "N", "N", "N");
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }
        
        //Insere registros no histórico do pedido (T178)
        mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$mensagem}','')");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }
        
        mysql_query("COMMIT");

        // Atualiza notificação de análise de crédito
        //require_once('bibliotecas/classes/NOTIFICACOES.php');
        //$NOTIFICACOES= new NOTIFICACOES();
        //$NOTIFICACOES->liberarCredito();
        //$NOTIFICACOES->pedidoRetornoAlmoxarifado();
        //$NOTIFICACOES->pedidoCreditoNaoLiberado();
        //$NOTIFICACOES->pedidoCreditoLiberado();
        
        return true;
    }
    
    /**
     * validaCampoLiberar
     *
     * @param String $T005_Id
     * @return String/bool
     */
    public function validaCampoLiberar($T005_Id)
    {
        global $g;

        require_once("bibliotecas/classes/VEN002.php");
        $VEN002 = new VEN002();
        
        //PARA SETAR A EXPEDIÇÃO PADRÃO DO PEDIDO, EXISTE A VARIÁVEL GLOBAL ABAIXO QUE É CONFIGURADA DENTRO DE _GLOBAL
        $statusPedido = $g['C031']['statusInicialPedido'];
        
        if (empty($T005_Id)) {
            return "T005_Id invalido, contate o suporte técnico";
        }
        
        $sqlT005 = mysql_query("SELECT T005_Flag_Primeira_Liberacao_Credito, 
                                       D024_Flag_Pre_Cadastro, 
                                       T005_Flag_Status, 
                                       T005_D024_Id,
                                       T005_Valor_Total_Produtos,
                                       T005_Flag_Separacao,
                                       T005_D027_Id
                                  FROM T005
                             LEFT JOIN D024 ON D024_Id=T005_D024_Id
                                 WHERE T005_Id = '{$T005_Id}'");

        $T005 = mysql_fetch_array($sqlT005);
        
        if($T005['D024_Flag_Pre_Cadastro'] == 'S'){
            return "Cadastro do cliente incompleto, verifique o cadastro do cliente!";
        }

        if($T005['T005_Flag_Status'] != '6'){
            return "O pedido já foi analisado por outro usuário, atualize a tela de análise de crédito.";
        }

        //Status Possíveis 
        // 0 - Não passou pela primeira liberação ainda
        // 1 - Não Aprovado na primeira liberação
        // 2 - Aprovado na primeira liberação
        if ($T005['T005_Flag_Primeira_Liberacao_Credito'] > 0) {
            if ($statusPedido == "1") {
                $fraseStatus = "Autorizou crédito. Enviado para impressão";
            }
            if ($statusPedido == "10") {
                $fraseStatus = "Autorizou crédito. Enviado para conferência";
            }
            if ($statusPedido == "3") {
                $fraseStatus = "Autorizou crédito. Enviado para emissão de NF";
            }
            if ($statusPedido == "21") {
                $fraseStatus = "Autorizou crédito. Enviado para aguardando Peças";
            }

            /* COMENTADO PROCESSO QUE PREENCHIA A SEPARAÇÃO DA MANEIRA ANTIGA. AGORA A SEPARAÇÃO É PREENCHIDA NO MÉTODO ven012->gerarNotaFiscal
            if ($statusPedido == 10 || $statusPedido == 3) {
                $sql    = "SELECT * FROM T006 WHERE T006_T005_Id='{$T005_Id}'";
                $result = mysql_query($sql);
                while ($linha = mysql_fetch_array($result)) {
                    $quantidade = $linha['T006_Quantidade'];
                    $sql2       = "UPDATE T006 SET T006_Quantidade_Separacao='{$quantidade}' WHERE T006_Id='{$linha['T006_Id']}';";
                    mysql_query($sql2);
                    if (mysql_error()) {
                        return mysql_error();
                    }
                }
            }
            */

            //Verifica se os produtos serão transportados por outra empresa
            $sqlPedidos = mysql_query("SELECT T006_Id 
                                         FROM T006 
                                    LEFT JOIN T005 ON T005_Id = T006_T005_Id 
                                        WHERE IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id 
                                          AND T005_Id = '{$T005_Id}' 
                                     GROUP BY T006_C004_Id_Estoque");
            if(mysql_num_rows($sqlPedidos) <= 0){
                $statusPedido = '3';
            }

            $retorno = $VEN002->gerarPedidoTransporte($T005_Id);

            if($retorno !== true){
                return $retorno;
            }

            if($g['C031']['modeloExpedicao'] == '2'){
                if($T005['T005_Flag_Separacao'] == "S"){
                    $statusPedido = 21;
                    $fraseStatus = "Autorizou crédito. Enviado para aguardando Peças";
                } else {
                    $statusPedido = 3;
                    $fraseStatus = "Autorizou crédito. Enviado para emissão de NF";
                }
            }
        } else {
            $statusPedido = 0;
            $fraseStatus  = "Autorizou crédito";
        }        
        
        $retorno = mysql_query("SELECT AVG(T090_Prazos_Dias) FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
        $T090    = mysql_fetch_array($retorno);
        
        $desconto = $VEN002->calculaPercentualDescontoPedido($T005_Id);

        $cSql = mysqli_query("UPDATE T005 LEFT JOIN T005A ON T005A_T005_Id = T005_Id
                                          SET T005_Flag_Status='{$statusPedido}', 
                                              T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),
	                                          T005_Liberacao_Credito_Id='{$g['usuarioAtual']}',
	                                          T005_Flag_Separacao_Liberar='N',
	                                          T005_Flag_Notificacao_Financeiro='S',
	                                          T005_Flag_Primeira_Liberacao_Credito='2',
	                                          T005_Liberou_Credito_Valor_Total='{$T005['T005_Valor_Total_Produtos']}',
	                                          T005_Liberou_Credito_Desconto='{$desconto}',
	                                          T005_Liberou_Credito_Prazo_Medio='{$T090['AVG(T090_Prazos_Dias)']}',
                                              T005_Liberou_D024_Id='{$T005['T005_D024_Id']}',
                                              T005A_Liberou_Credito_Portador = '{$T005['T005_D027_Id']}',
                                              T005_Data_Hora_Almoxarifado = NOW()
	                                    WHERE T005_Id='{$T005_Id}'");
 
        if($statusPedido == $g['C031']['statusInicialPedido']) {
            require_once('bibliotecas/webservices/cyberlog/integracaoWMS.php');
            $WMS = new WMS();
            
            if($WMS->isEnabled()) {
                $WMS->integrarPedido($T005_Id);
            }
        }

        //Insere registros no histórico do pedido (T178)
        mysql_query("INSERT INTO T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$fraseStatus}','')");

        /*
        * Verifica se há produtos da Matriz para enviar pedido matriz
        */
        $produtosMatriz = false;
        $sqlD009 = mysql_query("SELECT T006_Id 
                                  FROM T006 
                             LEFT JOIN D009 ON D009_Id = T006_D009_Id 
                                 WHERE D009_C004_Id != '{$g['empresaAtual']}' 
                                   AND T006_T005_Id = '{$T005_Id}'");

        if(mysql_num_rows($sqlD009) > 0 && $g['C031']['venderEstoqueMatriz'] == 'S'){
            $produtosMatriz = true;
        }

        $duplicarPedido = $g['conf']->ini('faturamentoMultiEmpresa');
        
        if ($duplicarPedido && $produtosMatriz){
            $duplica = $this->enviarPedidoMatriz($T005_Id);
            if ($duplica !== true) {
                $mensagem = $duplica;
            }

            //Insere os registros da separação do pedido (A SEPARAÇÃO REAL OCORRE APENAS NA MATRIZ)
            $sqlT006 = mysql_query("SELECT T006_Id, T006_T005_Id, T006_Quantidade, T066_Id, T066_D004_Id FROM T006 LEFT JOIN D009 ON D009_Id=T006_D009_Id LEFT JOIN T066 ON T066_D009_Id=D009_Id WHERE T006_T005_Id = '{$T005_Id}' GROUP BY T006_Id");
            while($T006 = mysql_fetch_array($sqlT006)){
                mysql_query("INSERT INTO T238 (T238_T006_Id, T238_T066_Id, T238_Quantidade_Separacao, T238_T005_Id, T238_D004_Id) VALUES ('{$T006['T006_Id']}', '{$T006['T066_Id']}', '{$T006['T006_Quantidade']}', '{$T006['T006_T005_Id']}', '{$T006['T066_D004_Id']}')");
            }
        }

        // Atualiza notificação de análise de crédito
        //require_once('bibliotecas/classes/NOTIFICACOES.php');
        //$NOTIFICACOES= new NOTIFICACOES();
        //$NOTIFICACOES->liberarCredito();
        //$NOTIFICACOES->pedidoRetornoAlmoxarifado();
        //$NOTIFICACOES->pedidoCreditoNaoLiberado();
        //$NOTIFICACOES->pedidoCreditoLiberado();

        $this->triggerAposLiberarAnaliseCredito($T005_Id);

        return true;
    }

    public function triggerAposLiberarAnaliseCredito($T005_Id) {
        return true;
    }
}
