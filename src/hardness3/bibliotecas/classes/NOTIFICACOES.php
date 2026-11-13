<?php
namespace hardness;

/**
* Notificações
*/
definirClasse('NOTIFICACOES');
class NOTIFICACOES_
{
    /************************************************************************************************************
    *
    *                                   METODOS DE ATUALIZAÇÃO DE NOTIFICAÇÕES
    *
    *************************************************************************************************************/
    public function atualizarNotificacoesT006()
    {
        // session_write_close();
        // $this->cotacaoEmergencia();
        // $this->comprasEmergencia();
        // $this->montagensEmergencia();
        // $this->servicosEmergencia();
        // $this->producoesEmergencia();
        // return true;
    }

    public function atualizarNotificacoesT005()
    {
        // session_write_close();
        // $this->liberarCredito();
        // $this->liberarCreditoPedido();
        // $this->pedidoCreditoNaoLiberado();
        // $this->pedidoCreditoLiberado();
        // $this->pedidoEntradaEstoque();
        // $this->pedidoNaoLiberado();
        // $this->pedidoLiberado();
        // $this->pedidoRetornoAlmoxarifado();
        // $this->gerarNotaFiscal();
        // $this->cotacaoEmergencia();
        // $this->comprasEmergencia();
        // $this->montagensEmergencia();
        // $this->servicosEmergencia();
        // $this->producoesEmergencia();
        // $this->ordemTransferencia();
        // return true;
    }

    public function atualizarNotificacoesT007()
    {
        // session_write_close();
        // $this->liberarCreditoNota();
        // $this->enviarNotaFiscal();
        // return true;
    }

    public function atualizarNotificacoesT003()
    {
        // session_write_close();
        // $this->cotacaoEmergencia();
        // $this->liberarCreditoOrcamento();
        // $this->retornoCotacaoEmergencia();
        // $this->orcamentoLiberado();
        // $this->orcamentoNaoLiberado();
        // return true;
    }

    public function atualizarNotificacoesT218()
    {
        // session_write_close();
        // $this->cotacaoAnalise();
        // $this->cotacaoAprovada();
        // $this->cotacaoNegada();
        // $this->cotacaoFinalizada();
        // return true;
    }

    public function atualizarNotificacoesT219()
    {
        // session_write_close();
        // $this->cotacaoEmergencia();
        // $this->comprasEmergencia();
        // $this->retornoCotacaoEmergencia();
        // return true;
    }

    public function atualizarNotificacoesT224()
    {
        // session_write_close();
        // $this->ordemCompraAnalise();
        // $this->ordemCompraAprovada();
        // $this->ordemCompraNegada();
        // $this->ordemTransferencia();
        // $this->estoqueAguardandoColeta();
        // return true;
    }

    public function atualizarNotificacoesT225()
    {
        // session_write_close();
        // $this->cotacaoEmergencia();
        // $this->comprasEmergencia();
        // $this->compraAutorizada();
        // return true;
    }

    public function atualizarNotificacoesT013()
    {
        // session_write_close();
        // $this->compraAutorizada();
        // $this->pedidoEntradaEstoque();
        // return true;
    }

    public function atualizarNotificacoesT004()
    {
        // session_write_close();
        // $this->cotacaoEmergencia();
        // $this->retornoCotacaoEmergencia();
        // $this->analiseCusto();
        // $this->retornoAnaliseCusto();
        // return true;
    }

    public function atualizarNotificacoesT014()
    {
        // session_write_close();
        // $this->compraAutorizada();
        // $this->produtosDivergencia();
        // return true;
    }

    public function atualizarNotificacoesT047()
    {
        // session_write_close();
        // $this->estoqueMontagemPendente();
        // return true;
    }

    public function atualizarNotificacoesT245()
    {
        // session_write_close();
        // $this->verificaLogErros();
        // return true;
    }

    public function atualizarNotificacoesT048()
    {
        // session_write_close();
        // $this->montagensEmergencia();
        // return true;
    }

    public function atualizarNotificacoesT256()
    {
        // session_write_close();
        // $this->producoesEmergencia();
        // return true;
    }

    /* Função trigger para chamar notificações personalizadas para o geral */
    public function NotificacoesPersonalizadasGeral(){}

    public function atualizarNotificacoesGeral()
    {
    	global $g;
        session_write_close();

    	$C004 = mysql_query("select C004_Id,C004_Nome_Abreviado from C004");
    	while ($mC004 = mysql_fetch_array($C004)){

    		$g['empresaAtual'] = $mC004['C004_Id'];
            $g['C004']['C004_Nome_Abreviado'] = $mC004['C004_Nome_Abreviado'];
            $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
            $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
            while($rowC031 = mysql_fetch_assoc($sqlC031)){
                $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
                $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            }
            
            echo "\n\n Processando empresa {$g['C004']['C004_Nome_Abreviado']} \n\n";

            $this->NotificacoesPersonalizadasGeral(); // Função que pode chamar notificações por personalização

            $this->ordemTransferencia();
            $this->verificaLogErros();
            $this->analiseCusto();
            $this->estoqueAguardandoColeta();
	        $this->comprasEmergencia();
	        $this->montagensEmergencia();
	        $this->servicosEmergencia();
	        $this->producoesEmergencia();
	        $this->liberarCredito();
	        $this->liberarCreditoPedido();
	        $this->pedidoCreditoNaoLiberado();
	        $this->pedidoCreditoLiberado();
	        $this->pedidoEntradaEstoque();
	        $this->pedidoNaoLiberado();
	        $this->pedidoLiberado();
	        $this->pedidoRetornoAlmoxarifado();
	        $this->retornoAnaliseCusto();
	        $this->compraAutorizada();
	        $this->cotacaoAnalise();
	        $this->cotacaoAprovada();
	        $this->cotacaoNegada();
	        $this->cotacaoFinalizada();
	        $this->ordemCompraAnalise();
	        $this->ordemCompraAprovada();
	        $this->ordemCompraNegada();
            $this->retornoCotacaoEmergencia();
            $this->liberarCreditoOrcamento();
            $this->orcamentoLiberado();
            $this->orcamentoNaoLiberado();
            $this->purchaseOrderAnalise();
            $this->comprasEmergenciaInternacional();
            $this->cotacaoEmergenciaInternacional(); 

            $this->retornoAgenda();
            $this->orcamentoRetornoContato();
            $this->produtosDivergencia();
            
            if($g['C031']['habilitaNotificacoesGenerico'] == 'S'){
                $this->alteracaoProdutoGenerico();
                $this->alteracaoPrecoGenericoOrcamento();
                $this->baixaAutomatica();
            }
                         
            /*
            Criado Crontab na Hardness para atualizar o trello
            if(!empty($g['C031']['TrelloUrlApi'])){
                $this->AtualizarChamadosTrello();
                $this->SincronizarTrello();
            }*/
        }
        $this->cotacaoEmergencia();
        // $this->ordenarNotificacoes();
        return true;
    }

    public function atualizarNotificacoesGeralHora()
    {
        global $g;
        session_write_close();

        $this->produtosEstoqueVazio();
        $this->produtosEstoqueEmergencia();
        $this->documentoProdutoVencido();
        $this->documentoProdutoVencendo();
        $this->produtosVencendo();  
        $this->produtosVencidos();         
        $this->notificacoesUpdate(); 

        if($g['C031']['gerarStContasPagar']=='S'){ 
            $this->contasAPagarStAlerta();
        }

        /* Gabriel Feo - 08/03/2023
            Ajustado as notificações para trabalhar por empresa
            As notificações fora do loop terão que ser analisadas para correção
        */
        $C004 = mysql_query("select C004_Id,C004_Nome_Abreviado from C004");
    	while ($mC004 = mysql_fetch_array($C004)){

            $g['empresaAtual'] = $mC004['C004_Id'];
            $g['C004']['C004_Nome_Abreviado'] = $mC004['C004_Nome_Abreviado'];

            $g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
            $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
            while ($rowC031 = mysql_fetch_assoc($sqlC031)) {
                $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
                $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            }

            $this->historicoSemLocacao();            
            $this->enviarNotaFiscal();
            $this->gerarNotaFiscal();
            $this->financeiroAlerta();                                   
            //$this->processarFeedback(); Criado crontab na Hardness
            $this->tabelaFornecedorVencida();            
            $this->notificacaoAtualizacaoTabelaFornecedor();            
            $this->produtosEstoqueEmergenciaInternacional();                        
            // $this->ordenarNotificacoes();
        }

        return true;
    }

    public function atualizarNotificacoesGeralDia()
    {
        global $g;
        
        session_write_close();

        // Verificando qual dia da semana é hoje (0 = Domingo, 1 = Segunda, 2 = terça, 3 = Quarta, 4 = Quinta, 5 = Sexta, 6 = Sábado)
        $diaSemana = date('w', time());

        //Não rodar nos fins de semana
        if(($diaSemana>0) && ($diaSemana<6)){
            $this->enviarBoletoPadraoAutomatico();
            $this->enviarLembretePagamentoAutomatico();
            $this->enviarBoletoCobrancaAutomatico();
            $this->enviarLembreteEntregaOrdemCompra();
            $this->enviarAvisoAtrasoEntregaOrdemCompra();
        }
        
        $this->notificacoesBoletoSemRemessa();
        $this->verificarEstoqueMinimoProduto();

        return true;
    }

    public function enviarLembreteEntregaOrdemCompra()
    {
        global $g;
        $diasEnvio = (int) $g['C031']['DiasEnviarEmailLembreteOrdemCompra'];
        echo "\nDias para envio do lembrete de entrega de OC: {$diasEnvio}";
        if ($diasEnvio <= 0) {
            echo "\nLembrete de entrega de OC desativado.";
            return true;
        }
        if ($g['usuarioAtual'] != 63) {
            echo "\nLembrete de entrega de OC executado por usuário inválido.";
            return false;
        }

        require_once('bibliotecas/classes/EST016.php');
        $EST016 = new EST016();
        $sql = "SELECT T224.T224_Id, T224.T224_Data_Entrega AS Data_Entrega
                  FROM T224
                 WHERE T224.T224_C004_Id = '{$g['empresaAtual']}'
                   AND T224.T224_Data_Emissao >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                   AND T224.T224_Data_Entrega IS NOT NULL
                   AND T224.T224_Data_Entrega = DATE_ADD(CURRENT_DATE(), INTERVAL {$diasEnvio} DAY)
                   AND T224.T224_Flag_Status = 3";
        $resultado = mysql_query($sql);
        $enviados = 0;
        while ($ordemCompra = mysql_fetch_assoc($resultado)) {
            $retorno = $EST016->enviarEmailLembreteEntrega($ordemCompra['T224_Id'], $ordemCompra['Data_Entrega']);
            if ($retorno === true) {
                $enviados++;
            } else {
                echo "\nOC {$ordemCompra['T224_Id']}: {$retorno}";
            }
        }
        echo "\nLembretes de entrega de OC enviados: {$enviados}.";
        return true;
    }

    public function enviarAvisoAtrasoEntregaOrdemCompra()
    {
        global $g;
        $diasAtraso = (int) $g['C031']['DiasAtrasoEnviarEmailOrdemCompra'];
        if ($diasAtraso <= 0) {
            echo "\nAviso de atraso de entrega de OC desativado.";
            return true;
        }
        if ($g['usuarioAtual'] != 63) {
            echo "\nAviso de atraso de entrega de OC executado por usuário inválido.";
            return false;
        }

        require_once('bibliotecas/classes/EST016.php');
        $EST016 = new EST016();
        $sql = "SELECT T224.T224_Id, T224.T224_Data_Entrega AS Data_Entrega
                  FROM T224
                 WHERE T224.T224_C004_Id = '{$g['empresaAtual']}'
                   AND T224.T224_Data_Emissao >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                   AND T224.T224_Data_Entrega IS NOT NULL
                   AND T224.T224_Data_Entrega = DATE_SUB(CURRENT_DATE(), INTERVAL {$diasAtraso} DAY)
                   AND T224.T224_Flag_Status = 3";
        $resultado = mysql_query($sql);
        $enviados = 0;
        while ($ordemCompra = mysql_fetch_assoc($resultado)) {
            $retorno = $EST016->enviarEmailLembreteEntrega($ordemCompra['T224_Id'], $ordemCompra['Data_Entrega'], true);
            if ($retorno === true) {
                $enviados++;
            } else {
                echo "\nOC {$ordemCompra['T224_Id']}: {$retorno}";
            }
        }
        echo "\nAvisos de atraso de entrega de OC enviados: {$enviados}.";
        return true;
    }

    /************************************************************************************************************
    *
    *                                   METODOS DE VERIFICAÇÃO DE NOTIFICAÇÃO
    *
    *************************************************************************************************************/
    public function AtualizarChamadosTrello(){
        global $g;
        require_once('bibliotecas/webservices/trello/apiTrello.php');
        $Trello = new Trello();

        $retorno = $Trello->AtualizarChamadosTrello();
        echo $retorno;
    }

    public function processarFeedback(){
        global $g;
        // Não executar mais nas notificações, criado cronttab na Hardness

        echo "\n\nProcessando feedback";
        require_once 'prog/outros/registroSuporte/registroSuporte.php';
        $retorno = processarFeedback();
        echo $retorno['data'];
    }

    public function SincronizarTrello(){
        global $g;
        require_once('bibliotecas/webservices/trello/apiTrello.php');
        $Trello = new Trello();

        $timeAtualizar = $g['C031']['TrelloNomeTimeAnalise'];

        $retorno = $Trello->sincronizarTrello($timeAtualizar,false);
        echo "\n\n SINCRONIZANDO TRELLO: \n\n".$retorno[1];
    }

    public function enviarBoletoPadraoAutomatico(){
        global $g;
        require_once("bibliotecas/classes/FIN005.php");
        $FIN005 = new FIN005();
        require_once("bibliotecas/classes/FIN001.php");
        $FIN001 = new FIN001();
        $diasEnvio = $g['C031']['DiasEnviarBoletoPadrao'];

        if($g['usuarioAtual'] != 63){
            // No caso do usuario atualizar as notificações manualmente enviava os boletos com o email do usuario
            // Trava para enviar apenas se vir pelo notificacoesDia (crontab) que é setado o ROOT como usuario atual
            echo "\nEntrou no enviarBoletoPadraoAutomatico mas não é o usuario root, finalizando";
            return false;
        }

        mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Envio dos boletos padrão.')",$g['conexaoBancoAuditoria']);
        $idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);

        if($diasEnvio>0){
            $sqlT117 = mysql_query("SELECT T117_Id 
                                  FROM T117 
                             LEFT JOIN T002 ON T002_T117_Id=T117_Id
                                 WHERE T002_Flag_Status != 2
                                   AND T117_Flag_Cancelado != 'S' 
                                   AND T002_Data_Recebimento = '0000-00-00'
                                   AND (T117_Data_Vencimento>=CURRENT_DATE() AND T117_Data_Vencimento<=DATE_ADD(CURDATE(), INTERVAL {$diasEnvio} DAY))
                                   AND (T117_Data_Envio_Email='0000-00-00 00:00:00' OR T117_Data_Envio_Email='');");

            $dadosQuery = array('where' => "T117_Flag_Cancelado != 'S' 
                                            AND T002_Data_Recebimento = '0000-00-00'
                                            AND (T117_Data_Vencimento>=CURRENT_DATE() AND T117_Data_Vencimento<=DATE_ADD(CURDATE(), INTERVAL {$diasEnvio} DAY))
                                            AND (T117_Data_Envio_Email='0000-00-00 00:00:00' OR T117_Data_Envio_Email='');");

            $retorno = $FIN001->validacoesTravarT002($dadosQuery);
            if($retorno!==false){
            echo "Erro validação T002:".$retorno;
            }else{
                if(mysql_num_rows($sqlT117)==0){
                    echo "\n Não há nenhum boleto para envio.";
                }else{
                    while($T117 = mysql_fetch_array($sqlT117)){
                        $retorno = $FIN005->enviarMultiEmailBoleto($T117['T117_Id']);
                        if($retorno !== true){
                            $erros .= "<b>".$T117['T117_Nosso_Numero']."</b> - Erro: ".$retorno."<br>";
                        }
                    }
                    if($erros != '') { 
                        $erros = substr($erros, 0, -2);
                        echo "\n Erros no envio dos boletos abaixo:\n".$erros;
                    } else {
                        echo "\n Os boletos foram enviados com sucesso!";
                    }
                }
            }
        }else{
            echo "\n Dias de envio está zerado, emails não enviados.";
        }
        mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);

        echo "\n FINAL ENVIO BOLETOS PADRÃO. \n";
    }

    public function enviarLembretePagamentoAutomatico(){
        global $g;
        require_once("bibliotecas/classes/FIN005.php");
        $FIN005 = new FIN005();
        require_once("bibliotecas/classes/FIN001.php");
        $FIN001 = new FIN001();

        if($g['usuarioAtual'] != 63){
            // No caso do usuario atualizar as notificações manualmente enviava os boletos com o email do usuario
            // Trava para enviar apenas se vir pelo notificacoesDia (crontab) que é setado o ROOT como usuario atual
            echo "\nEntrou no enviarLembretePagamentoAutomatico mas não é o usuario root, finalizando";
            return false;
        }

        mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Envio de boleto lembretes.')",$g['conexaoBancoAuditoria']);
        $idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);

        mysql_set_charset('utf8');
        mysql_query("SET lc_time_names = 'pt_BR'");

        //Pegar o dia da semana
        $diaSemana = mysql_fetch_assoc(mysql_query("SELECT DAYNAME(CURRENT_TIMESTAMP()) AS diaSemanaHoje"));
        
        //Não enviar email de lembrete nos fins de semana
        if(($diaSemana['diaSemanaHoje']=="domingo") || ($diaSemana['diaSemanaHoje']=="sábado")){
            echo "<br>Email lembretes não serão enviados nos fins de semana.";
        }else{
            $diasEnvio = $g['C031']['DiasEnviarBoletoLembrete'];
            $enviarLembreteDiaVencimento = $g['C031']['EnviarLembreteDiaVencimento'];
            $qtdBoletos = 0;
            $clausula = "";

            if($diasEnvio>0){
                $clausula = "AND T117_Data_Vencimento=DATE_ADD(CURDATE(), INTERVAL {$diasEnvio} DAY)";
            }

            if($enviarLembreteDiaVencimento=="S"){
                if(!empty($clausula)){
                    $clausula = "AND (T117_Data_Vencimento=DATE_ADD(CURDATE(), INTERVAL {$diasEnvio} DAY) OR T117_Data_Vencimento=CURRENT_DATE())";
                }else{
                    $clausula = "AND T117_Data_Vencimento=CURRENT_DATE()";
                }
            }

            if(!empty($clausula)){
                //Se hoje for segunda enviar o email de lembrete dos boletos que venceram no fim de semana
                if($diaSemana['diaSemanaHoje']=="segunda"){
                    $clausula.=" OR (T117_Data_Vencimento = DATE_ADD(CURDATE(), INTERVAL -2 DAY))";
                }
                $sqlT117 = mysql_query("SELECT T117_Id
                                    FROM T117 
                                LEFT JOIN T002 ON T002_T117_Id=T117_Id
                                    WHERE T117_Flag_Cancelado != 'S' 
                                    AND T002_Data_Recebimento = '0000-00-00' 
                                    AND T002_Flag_Status != 2
                                    {$clausula}");
                $qtdBoletos = mysql_num_rows($sqlT117);                   
            }

            if($qtdBoletos<=0){
                echo "\n Não há nenhum boleto com vencimento proximo para envio de lembrete.";
            }else{
                $dadosQuery = array('where' => "T117_Flag_Cancelado != 'S' 
                                                AND T002_Data_Recebimento = '0000-00-00' {$clausula}");
                $retorno = $FIN001->validacoesTravarT002($dadosQuery);
                if($retorno!==false){
                    echo "Erro validação T002:".$retorno;
                }else{
                    while($T117 = mysql_fetch_assoc($sqlT117)){
                        $retorno = $FIN005->enviarMultiEmailBoletoLembrete($T117['T117_Id']);
                        $retorno = true;
                        if($retorno !== true){
                            $erros .= "<b>".$T117['T117_Nosso_Numero']."</b> - Erro: ".$retorno."<br>";
                        }               
                    }
                    if($erros != '') { 
                        $erros = substr($erros, 0, -2);
                        echo "\n Erros no envio dos lembretes de pagamento dos boletos abaixo:\n".$erros;
                    } else {
                        echo "\n Os lembretes de pagamento dos boletos foram enviados com sucesso!";
                    }
                }
            }
        }

        mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
        echo "\nFINAL ENVIO LEMBRETE\n";
    }

    public function enviarBoletoCobrancaAutomatico(){
        global $g;
        require_once("bibliotecas/classes/FIN005.php");
        $FIN005 = new FIN005();
        require_once("bibliotecas/classes/FIN001.php");
        $FIN001 = new FIN001();
        $DiasEnviarBoletoCobranca = $g['C031']['DiasEnviarBoletoCobranca'];
        $DiasMaximoEnviarEmailBoletoCobranca = $g['C031']['DiasMaximoEnviarEmailBoletoCobranca'];

        if($g['usuarioAtual'] != 63){
            // No caso do usuario atualizar as notificações manualmente enviava os boletos com o email do usuario
            // Trava para enviar apenas se vir pelo notificacoesDia (crontab) que é setado o ROOT como usuario atual
            echo "\nEntrou no enviarBoletoCobrancaAutomatico mas não é o usuario root, finalizando";
            return false;
        }

        mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Envio dos boletos cobrança.')",$g['conexaoBancoAuditoria']);
        $idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);

        mysql_set_charset('utf8');
        mysql_query("SET lc_time_names = 'pt_BR'");
        
        //Pegar o dia da semana
        $diaSemana = mysql_fetch_assoc(mysql_query("SELECT DAYNAME(CURRENT_TIMESTAMP()) AS diaSemanaHoje"));

        if($DiasEnviarBoletoCobranca>0){
            $sqlT117 = mysql_query("SELECT T117_Id,
                                    DAYNAME(T117_Data_Vencimento) AS diaSemanaVencimento
                                    FROM T117 
                                    LEFT JOIN T002 ON T002_T117_Id=T117_Id
                                    WHERE T117_Flag_Cancelado != 'S' 
                                    AND T002_Data_Recebimento = '0000-00-00'
                                    AND T002_Flag_Status != 2
                                    AND ((T117_Data_Vencimento>=DATE_ADD(CURRENT_DATE(), INTERVAL -{$DiasMaximoEnviarEmailBoletoCobranca} DAY)) AND (T117_Data_Vencimento<CURRENT_DATE()))
                                    AND ((T117_Data_Envio_Email='0000-00-00 00:00:00') OR (IFNULL(DATEDIFF(CURRENT_TIMESTAMP(),T117_Data_Envio_Email),0)>={$DiasEnviarBoletoCobranca}));");
            
            $dadosQuery = array('where' => "((T117_Data_Vencimento>=DATE_ADD(CURRENT_DATE(), INTERVAL -{$DiasMaximoEnviarEmailBoletoCobranca} DAY)) AND (T117_Data_Vencimento<CURRENT_DATE()))
                                            AND ((T117_Data_Envio_Email='0000-00-00 00:00:00') OR (IFNULL(DATEDIFF(CURRENT_TIMESTAMP(),T117_Data_Envio_Email),0)>={$DiasEnviarBoletoCobranca}));");
            echo "\nBoletos:".mysql_num_rows($sqlT117);

            $retorno = $FIN001->validacoesTravarT002($dadosQuery);
            if($retorno!==false){
                echo "Erro validação T002:".$retorno;
            }else{
                if(mysql_num_rows($sqlT117)==0){
                    echo "\n Não há nenhum boleto para envio.";
                }else{
                    while($T117 = mysql_fetch_assoc($sqlT117)){
                        if($diaSemana['diaSemanaHoje']=="segunda"){
                            if(($T117['diaSemanaVencimento']=="domingo") || ($T117['diaSemanaVencimento']=="sábado")){
                                continue;
                            }                          
                        }else {
                            $retorno = $FIN005->enviarMultiEmailBoletoCobranca($T117['T117_Id']);
                            if($retorno !== true){
                                $erros .= "<b>".$T117['T117_Nosso_Numero']."</b> - Erro: ".$retorno."<br>";
                            }
                        }
                    }
                    if($erros != '') {
                        $erros = substr($erros, 0, -2);
                        echo "\n Erros no envio dos boletos abaixo:\n".$erros;
                    } else {
                        echo "\n Os boletos foram enviados com sucesso!";
                    }
                }
            }
        }else{
            echo "\n Dias de envio do boletos de cobrança está zerado, emails não enviados.";
        }
        mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);

        echo "\n FINAL ENVIO BOLETOS COBRANÇA. \n";
    }

    public function liberarCredito() {
        global $g;
        $resposta = array('id' => str_pad('1', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T005_Id FROM T005 WHERE (T005_Flag_Status='6' and T005_C004_Id='{$g['empresaAtual']}' and T005_Flag_Aguardar_Liberacao != 1)";
        $res = mysql_query($sql);
        $rows = 0;
        if ($res) {
            $rows = mysql_num_rows($res);
        }
        if ($rows > 0) {
            $rows .= ($rows == 1) ? ' liberação' : ' liberações';
            $mensagem = ($rows == 1) ? ' Existe' : ' Existem';
            $resposta['titulo'] = $mensagem." <b>{$rows}</b> de crédito pendente(s).";
            $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('fin007'), 'Análise de Crédito', '.:Análise de Crédito:.', '/fin/fin007/content/fin007content01/', '', 'auto', false, 'center');";
            $resposta['C029'] = array('39');
            $resposta['C031'] = array('mensagemLiberacaoDeCredito' => 'S');
        }

        mysql_free_result($res);

        return $this->processar($resposta);
    }

    public function liberarCreditoNota() {
        global $g;
        $resposta = array('id' => str_pad('2', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
    
        $sql = "SELECT T007_Id FROM T007 WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Flag_Analise_Credito = 1";
        $res = mysql_query($sql);
        $rows = 0;
        if ($res) {
            $rows = mysql_num_rows($res);
        }
        if ($rows > 0) {
            $rows .= ($rows == 1) ? ' liberação' : ' liberações';
            $mensagem = ($rows == 1) ? 'Existe' : 'Existem';
            $resposta['titulo'] = $mensagem." <b>{$rows}</b> de crédito (nota) pendente(s).";
            $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('fin007Nota'), 'Análise de Crédito (Nota)', '.: Análise de Crédito (Nota) :.', '/fin/fin007/content/fin007content02/', '', 'auto', false, 'center');";
            $resposta['C029'] = array('39');
            $resposta['C031'] = array('mensagemLiberacaoDeCredito' => 'S');
        }

        mysql_free_result($res);

        return $this->processar($resposta);
    }

    public function liberarCreditoOrcamento() {
        global $g;
        $resposta = array('id' => str_pad('3', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sqlOrcamento = "SELECT COUNT(T003_Id) as totalT003, 
                                T003_Flag_Alcada 
                           FROM T003 
                          WHERE (T003_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                            AND T003_C004_Id='{$g['empresaAtual']}' 
                            AND T003_Flag_Perdido!='S' 
                            AND T003_Flag_Status_Liberacao='1') 
                       GROUP BY T003_Flag_Alcada";
        $resOrcamento = mysql_query($sqlOrcamento);
        if (mysql_num_rows($resOrcamento) > 0) {
            while($row = mysql_fetch_assoc($resOrcamento)){
                $row['totalT003'] .= ($row['totalT003'] == 1) ? ' liberação' : ' liberações';
                $mensagem = ($row['totalT003'] == 1) ? 'Existe' : 'Existem';
                $resposta['titulo'] = $mensagem." <b>{$row['totalT003']}</b> de orçamento pendente(s).";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('ven005'), 'Análise de Orçamento', '.:Análise de Orçamento:.', '/ven/ven005/content/ven005ContentPrincipal/', '', 'auto', false, 'center');";
                $resposta['C029'] = array('18');
                $resposta['alcada'] = $row['T003_Flag_Alcada'];
                $resposta['C031'] = array('mensagemLiberacaoDeCredito' => 'S');
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($resOrcamento);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function liberarCreditoPedido() {
        global $g;
        $resposta = array('id' => str_pad('4', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sqlPedido = "SELECT COUNT(T005_Id) as totalT005, 
                             T005_Flag_Alcada 
                        FROM T005 
                       WHERE (T005_C004_Id='{$g['empresaAtual']}' 
                         AND T005_Flag_Status='13') 
                    GROUP BY T005_Flag_Alcada";
        $resPedido = mysql_query($sqlPedido);
        if (mysql_num_rows($resPedido) > 0) {
            while($row = mysql_fetch_assoc($resPedido)){
                $row['totalT005'] .= ($row['totalT005'] == 1) ? ' liberação' : ' liberações';
                $mensagem = ($row['totalT005'] == 1) ? 'Existe' : 'Existem';
                $resposta['titulo'] = $mensagem." <b>{$row['totalT005']}</b> de pedidos pendente(s).";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('ven013'), 'Análise de Pedido', '.:Análise de Pedido:.', '/ven/ven013/content/ven013ContentPrincipal/', '', 'auto', false, false , 'center');";
                $resposta['C029'] = array('99');
                $resposta['alcada'] = $row['T005_Flag_Alcada'];
                $resposta['C031'] = array('mensagemLiberacaoDeCredito' => 'S');
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($resPedido);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function cotacaoFinalizada() {
        global $g;
        $resposta = array('id' => str_pad('5', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = "SELECT COUNT(*) as Total, 
                       T218_C007_Id_Vendedor_Interno 
                  FROM T218 
                 WHERE T218_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                   AND T218_Flag_Notificacao = 1 
                   AND T218_C004_Id='{$g['empresaAtual']}' 
              GROUP BY T218_C007_Id_Vendedor_Interno";
        $res = mysql_query($sql);
        if (mysql_num_rows($sql) > 0) {
            while($row = mysql_fetch_assoc($res)){
                $extra = ($row['Total'] == 1) ? 'cotação de compra finalizada por um fornecedor.' : 'cotações de compra finalizadas por fornecedores.';
                $mensagem = ($row['Total'] == 1) ? 'Existe' : 'Existem';
                $resposta['titulo'] = $mensagem." <b>{$row['Total']}</b> {$extra}";
                $resposta['C007_Id'] = $row['T218_C007_Id_Vendedor_Interno'];
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', menuId('est015CotNovo'), 'Cotação de Compra', '.: Cotação de Compra :.', '/est/est015/content/est015ContentPrincipal/', '', 'auto', false, 'center')) {
                        removerNotificacao('notificacaoCotacaoFinalizada', '');
                    }
EOT;
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function cotacaoAnalise() {
        global $g;
        $resposta = array('id' => str_pad('6', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = "SELECT COUNT(*) as Total, 
                       T218_Flag_Alcada 
                  FROM T218 
                 WHERE T218_C004_Id='{$g['empresaAtual']}' 
                   AND T218_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                   AND T218_Flag_Perdido!='S' 
                   AND T218_Flag_Status_Liberacao='1'
               GROUP BY T218_Flag_Alcada";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            while($row = mysql_fetch_assoc($res)){
                $extra = ($row['Total'] == 1) ? 'análise de cotação pendente.' : 'análises de cotações pendentes.';
                $mensagem = ($row['Total'] == 1) ? 'Existe' : 'Existem';
                $resposta['titulo'] = $mensagem." <b>{$row['Total']}</b> {$extra}";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est015Notificacao'), 'Análise de Cotação de Compra', '.:Análise de Cotação de Compra:.', '/est/est015/content/est015ContentPrincipalAnalise/', '', [950,550], false, false, 'center');";
                $resposta['alcada'] = $row['T218_Flag_Alcada'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function ordemCompraAnalise() {
        global $g;
        $resposta = array('id' => str_pad('7', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = "SELECT COUNT(*) as Total, 
                       T224_Flag_Alcada 
                  FROM T224 
                 WHERE T224_C004_Id='{$g['empresaAtual']}' 
                   AND T224_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                   AND T224_Flag_Status='1' 
              GROUP BY T224_Flag_Alcada";

        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0){
            while($row = mysql_fetch_assoc($res)){
                $extra = ($row['Total'] == 1) ? 'análise de ordem de compra pendente.' : 'análises de ordens de compra pendentes.';
                $mensagem = ($row['Total'] == 1) ? 'Existe' : 'Existem';
                $resposta['titulo'] = $mensagem." <b>{$row['Total']}</b> {$extra}";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), 'Análise de Ordem de Compra', '.:Análise de Ordem de Compra:.', '/est/est016/content/est016ContentPrincipalAnalise/', '', [950,550], false, false , 'center');";
                $resposta['C029'] = array('173');
                $resposta['alcada'] = $row['T224_Flag_Alcada'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function cotacaoAprovada() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('8', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T218_Id, 
                       T218_C007_Id_Vendedor_Interno 
                  FROM T218 
                 WHERE T218_C004_Id='{$g['empresaAtual']}' 
                   AND T218_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                   AND T218_Flag_Perdido!='S' 
                   AND T218_Flag_Visualizou_Liberacao='N' 
                   AND T218_Flag_Status_Liberacao!=3";
        $res = mysql_query($sql);
        // exibir mensagem
        if(mysql_num_rows($res) > 0){
            while($row = mysql_fetch_assoc($res)){
                $resposta['idItem'] = $row['T218_Id'];
                $resposta['titulo'] = "<span style=\"color: green;\">Cotação de compra <b>{$row['T218_Id']}</b> foi aprovada.</span>";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '' ,menuId('est015'), '', 'Editar Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$row['T218_Id']}'), [990,605]);";
                $resposta['C007_Id'] = $row['T218_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    // Verificar se a mensagem continua até a 'Concluir inclusão dos itens' ou já ao abrir a nota
    public function compraAutorizada() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('9', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T013_Numero_Nota_Fiscal, T013_Id, T224_C007_Id_Vendedor_Interno
                  FROM T225
                  LEFT JOIN T014 ON T014_T225_Id=T225_Id
                  LEFT JOIN T013 ON T013_Id=T014_T013_Id
                  LEFT JOIN T224 ON T224_Id=T225_T224_Id
                  WHERE T224_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                    AND T225_C004_Id='{$g['empresaAtual']}'
                    AND T014_Flag_Divergencia='S'
                    AND T013_Flag_Travar_Nota!='S'
                    AND T225_Flag_Autorizado='S'                    
                    GROUP BY T013_Id";

        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $resposta['idItem'] = $row['T013_Id'];
                $resposta['titulo'] = "<span style=\"color: green;\">Divergência da nota de entrada <b>{$row['T013_Numero_Nota_Fiscal']}</b> foi autorizada.</span>";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '' ,menuId('est004'), '', 'Editar Nota Fiscal', '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + encodeURIComponent('{$row['T013_Id']}'), [990,550]);";
                $resposta['C007_Id'] = $row['T224_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function ordemCompraAprovada() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('10', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T224_Id, 
                       T224_C007_Id_Vendedor_Interno 
                  FROM T224 
                 WHERE T224_C004_Id='{$g['empresaAtual']}' 
                   AND T224_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                   AND T224_Flag_Visualizou_Liberacao='N' 
                   AND T224_Flag_Status = '3' 
                   AND T224_C007_Id_Vendedor_Interno != T224_Liberou_Alcada_C007_Id";

        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                if($row['T224_C007_Id_Vendedor_Interno']<=0){ continue;}
                $resposta['idItem'] = $row['T224_Id'];
                $resposta['titulo'] = "<span style=\"color: green;\">Ordem de compra <b>{$row['T224_Id']}</b> foi aprovada.</span>";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent('{$row['T224_Id']}'), [990,605]);";
                $resposta['C007_Id'] = $row['T224_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function cotacaoNegada() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('11', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T218_Id, 
                       T218_C007_Id_Vendedor_Interno 
                  FROM T218 
                 WHERE T218_C004_Id='{$g['empresaAtual']}' 
                   AND T218_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                   AND T218_Flag_Perdido!='S' 
                   AND T218_Flag_Visualizou_Liberacao='N' 
                   AND T218_Flag_Status_Liberacao=3";

        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)) {
                $resposta['idItem'] = $row['T218_Id'];
                $resposta['titulo'] = "<span style=\"color: red;\">Cotação de compra <b>{$row['T218_Id']}</b> foi <b>NEGADA</b>.</span>";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '' ,menuId('est015'), '', 'Editar Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$row['T218_Id']}'), [990,605]);";
                $resposta['C007_Id'] = $row['T218_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function ordemCompraNegada() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('12', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T224_Id, 
                       T224_C007_Id_Vendedor_Interno 
                  FROM T224 
                 WHERE T224_C004_Id='{$g['empresaAtual']}' 
                   AND T224_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                   AND T224_Flag_Visualizou_Liberacao='N' 
                   AND T224_Flag_Status = '2'";

        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                if($row['T224_C007_Id_Vendedor_Interno']<=0){ continue;}
                $resposta['idItem'] = $row['T224_Id'];
                $resposta['titulo'] = "<span style=\"color: red;\">Ordem de compra <b>{$row['T224_Id']}</b> foi <b>NEGADA</b>.</span>";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent('{$row['T224_Id']}'), [990,605]);";
                $resposta['C007_Id'] = $row['T224_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function cotacaoEmergencia() {
        global $g;
        $resposta = array('id' => str_pad('13', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        //Select para os compradores (D009_C007_Id_Comprador)
        $sql = mysql_query("SELECT D009_C007_Id_Comprador, 
                                   IF(IFNULL(D009_C007_Id_Comprador,0) > 0, comprador.C007_Primeiro_Nome, 'Sem comprador') as C007_Primeiro_Nome,
                                   D009_C004_Id,
                                   C004_Nome_Abreviado
                              FROM T004 
                         LEFT JOIN D009 on D009_Id=T004_D009_Id
                         LEFT JOIN D049 on D049_Id=D009_D049_Id
                         LEFT JOIN D001 on D001_Id=D049_D001_Id
                         LEFT JOIN D082 on D082_Id=D049_D082_Id
                         LEFT JOIN D037 on D037_Id=T004_D037_Id
                         LEFT JOIN D005 on D005_Id=T004_D005_Id
                         LEFT JOIN D006 on D006_Id=T004_D006_Id
                         LEFT JOIN T006 on T006_T004_Id=T004_Id
                         LEFT JOIN T005 ON T005_Id=T006_T005_Id
                         LEFT JOIN T003 ON T003_Id=T004_T003_Id
                         LEFT JOIN D024 as Cliente ON T003_D024_Id=Cliente.D024_Id
                         LEFT JOIN T219 ON T004_Id=T219_T004_Id
                         LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
                         LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                         LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
                         LEFT JOIN T241 ON T241_D001_Id=D001_Id
                         LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                         LEFT JOIN C004 on C004_Id=D009_C004_Id
                             WHERE T003_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND T006_Id is null
                               AND T219_Id is null
                               AND T004_Flag_Comprar='S'
                               AND D049_Flag_Tipo='CP'
                               AND T003_Flag_Status_Orcamento = 'P'
                          GROUP BY D009_C004_Id,D009_C007_Id_Comprador");

            //AND T003_Flag_Perdido!='S'

/*        $sql = mysql_query("SELECT COUNT(T004_Id) as totalT004, IFNULL(D009_C007_Id_Comprador,0) as D009_C007_Id_Comprador FROM T004 
                            LEFT JOIN T219 ON T004_Id=T219_T004_Id
                            LEFT JOIN T006 on T006_T004_Id=T004_Id
                            LEFT JOIN T003 ON T003_Id=T004_T003_Id 
                            LEFT JOIN D009 ON D009_Id=T004_D009_Id
                            LEFT JOIN D049 on D049_Id=D009_D049_Id 
                            WHERE IFNULL(T219_Id,0)<=0 
                            AND IFNULL(T004_T219_Id,0) <= 0 
                            AND T004_Flag_Comprar = 'S' 
                            AND D009_C004_Id='{$g['empresaAtual']}'
                            AND T006_Id IS NULL
                            AND T003_Flag_Perdido!='S'
                            AND (IFNULL(D049_Flag_Tipo,'CP') = 'CP' or IFNULL(D049_Flag_Tipo,'CP') = '')
                            GROUP BY D009_C007_Id_Comprador"); */
        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                $g['empresaAtual'] = $row['D009_C004_Id'];
                $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
                if($row['C007_Primeiro_Nome']!="Sem comprador"){
                    $row['C007_Primeiro_Nome']=substr($row['C007_Primeiro_Nome'],0,10);
                }
                $resposta['titulo'] = "Existem <b>{$count}</b> itens para cotação de emergência! ({$row['C007_Primeiro_Nome']})";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Compras de Estoque', '/est/est009/content/est009ContentCotacaoEmergencia/', '&mudarAba=true', [990,605]);";
                $resposta['C029'] = array('170', '88');
                $resposta['C031'] = array('desabilitarCotacaoEmergencia' => 'N');
                $resposta['C007_Id'] = $row['D009_C007_Id_Comprador'];
                $retorno .= $this->processar($resposta);
            }
        }

        //Select para os vendedores (T003_C007_Id_Vendedor_Interno)
/*        $sql = mysql_query("SELECT COUNT(T004_Id) as totalT004, T003_C007_Id_Vendedor_Interno FROM T004 
                            LEFT JOIN T219 ON T004_Id=T219_T004_Id
                            LEFT JOIN T006 on T006_T004_Id=T004_Id
                            LEFT JOIN T003 ON T003_Id=T004_T003_Id 
                            LEFT JOIN D009 ON D009_Id=T004_D009_Id
                            LEFT JOIN D049 on D049_Id=D009_D049_Id 
                            WHERE IFNULL(T219_Id,0)<=0 
                            AND IFNULL(T004_T219_Id,0) <= 0 
                            AND T004_Flag_Comprar = 'S' 
                            AND D009_C004_Id='{$g['empresaAtual']}'
                            AND T006_Id IS NULL
                            AND (IFNULL(D049_Flag_Tipo,'CP') = 'CP' or IFNULL(D049_Flag_Tipo,'CP') = '')
                            AND (D009_C007_Id_Comprador = T003_C007_Id_Vendedor_Interno' OR ifnull(D009_C007_Id_Comprador,0)<=0)
                            GROUP BY T003_C007_Id_Vendedor_Interno");
        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                $resposta['titulo'] = "Existem <b>{$row['totalT004']}</b> itens para cotação de emergência!";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Compras de Estoque', '/est/est009/content/est009ContentCotacaoEmergencia/', '&mudarAba=true', [990,605]);";
                $resposta['C029'] = array('170', '88');
                $resposta['alcada'] = 'V';
                $resposta['C031'] = array('desabilitarCotacaoEmergencia' => 'N');
                $resposta['C007_Id'] = $row['T003_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }*/

        if ($xCount<=0){
            mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Id_Mensagem = '{$resposta['id']}' AND N001_C004_Id = '{$g['empresaAtual']}' AND N001_Flag_Excluido='N'");
        }

        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function retornoCotacaoEmergencia() {
        global $g;

        $retorno = '';
        $resposta = array('id' => str_pad('14', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        //$this->cacheRefresh($resposta['id']);
        
        $removerNotificacao = $this->cacheConsultaNotificacao($resposta['id'], null);

        $sql = "SELECT T003_Id, 
                       T003_C007_Id_Vendedor_Interno 
                  FROM T004 
             LEFT JOIN T219 ON T219_Id = T004_T219_Id 
             LEFT JOIN T003 ON T003_Id = T004_T003_Id 
             LEFT JOIN T006 ON T006_T004_Id=T004_Id 
                 WHERE (T219_Flag_Comprar = 'S' || T219_Valor_Preco_Venda >0) 
                   AND T219_Flag_Lido='N' 
                   AND T004_C004_Id='{$g['empresaAtual']}' 
                   AND T006_Id is null 
                   AND T003_Flag_Perdido!='S' 
                   AND T003_Data_Emissao BETWEEN DATE_SUB(CURDATE(), INTERVAL 20 DAY) AND CURRENT_DATE() GROUP BY T004_T003_Id";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $Uniqid_Mensagem = $resposta['id'].$row['T003_Id'].$g['empresaAtual'].ord($resposta['alcada']).$row['T003_C007_Id_Vendedor_Interno'];
                $resposta['idItem'] = $row['T003_Id'];
                $resposta['titulo'] = "Existe retorno de compras para o orçamento <b>{$row['T003_Id']}</b>.";
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent({$row['T003_Id']}) + '&tabela=T003', [990,605])) {
                        removerNotificacao('removerNotificacaoRetornoCotacaoEmergencia', '{$row['T003_Id']}', '{$Uniqid_Mensagem}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T003_C007_Id_Vendedor_Interno'];
                unset($removerNotificacao[$Uniqid_Mensagem]);
                $retorno .= $this->processar($resposta);
            }
        }

        foreach ($removerNotificacao as $Uniqid_Mensagem => $notificacao) {

            $this->cacheConsultaNotificacao($resposta['id'], $removerNotificacao[$Uniqid_Mensagem]);

        }
        

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    // Verificar: a divegência hoje só sai na análise, e não quando da o OK (liberado)
    public function produtosDivergencia() {
        global $g;
        $resposta = array('id' => str_pad('15', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT count(*) as total,
                                   T014_C004_Id,
                                   C004_Nome_Abreviado 
                              FROM T225
                         LEFT JOIN T014 on T014_T225_Id=T225_Id
                         LEFT JOIN T013 on T013_Id=T014_T013_Id
                         LEFT JOIN C004 on C004_Id=T014_C004_Id
                             WHERE T013_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                               AND T014_Flag_Divergencia='S'
                               AND ifnull(T013_Flag_Travar_Nota,'N')!='S'
                               AND ifnull(T225_Flag_Autorizado,'N') != 'S'
                               AND T225_C004_Id='{$g['empresaAtual']}'
                          GROUP BY T014_C004_Id");

        // exibir mensagem
        while ($row = mysql_fetch_array($sql)){
            $g['empresaAtual'] = $row['T014_C004_Id'];
            $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
            $resposta['titulo'] = "Existem <b>{$row['total']}</b> itens em NF-e de entrada com divergência.";
            $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Recebimento NFe', '/est/est016/content/est016ContentProdutosDivergencia/', '&mudarAba=true', 'full');";
            $resposta['alcada'] = 'D';
            $retorno .= $this->processar($resposta);
        }

        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function retornoAgenda() {
        global $g,$confUsuario;

        echo "\n Processando agenda \n";

        $retorno = '';
        $resposta = array('id' => 'retornoAgenda', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $arrayC004 = array();
        $this->cacheRefresh($resposta['id']);

        $sql = <<<EOT
                    SELECT D085_C007_Id,
                           IF(T003_Id > 0, T003_C004_Id, IF(D085_T182_Id > 0, T182_C004_Id, IF(D085A_C004_Id > 0, D085A_C004_Id ,1))) AS T003_C004_Id, 
                           C004_Nome_Abreviado, 
                           D085_Id, 
                           T003_Id, 
                           D024_Id, 
                           D085_T259_Id, 
                           D024_Nome_Empresa, 
                           D085_Data_Previsao_Retorno, 
                           D085_Hora_Previsao_Retorno, 
                           COUNT(*) AS Total 
                      FROM D085
                 LEFT JOIN D024 ON D085_D024_Id = D024_Id
                 LEFT JOIN T003 ON D085_T003_Id = T003_Id
                 LEFT JOIN C004 ON T003_C004_Id = C004_Id
                 LEFT JOIN T182 ON D085_T182_Id = T182_Id
                 LEFT JOIN D085A ON D085A_D085_Id = D085_Id
                     WHERE D085_Flag_Lido = 'N' 
                       AND D085_C007_Id>0
                       AND D085_Data_Previsao_Retorno != '0000-00-00' 
                       AND D085_Data_Previsao_Retorno <= CURDATE()
                       AND D085_Hora_Previsao_Retorno <= CURTIME()
                       AND (D085_Data_Previsao_Retorno>=DATE_ADD(CURRENT_DATE(), INTERVAL -5 DAY))
                  GROUP BY D085_Id
                  ORDER BY D085_Id DESC
EOT;
        $res = mysql_query($sql);
        $num = mysql_num_rows($res);

        $h = fopen("/tmp/logRetornoAgenda", 'a');
        $sql = "DB: ".$confUsuario['dbDatabase'].' - SQL: '.$sql."\n\n";
        fwrite($h, $sql);
        fwrite($h, "numeros:".$num);
        fclose($h);

        while ($row = mysql_fetch_assoc($res)) {
            if(!empty($row['D085_T259_Id'])){
                $sqlD085 = mysql_query("SELECT * FROM D085 WHERE D085_T259_Id = '{$row['D085_T259_Id']}' AND D085_Data = '{$row['D085_Data_Previsao_Retorno']}' AND D085_Hora = '{$row['D085_Hora_Previsao_Retorno']}'");
                if(mysql_num_rows($sqlD085) <= 0){
                    $dataHoraRetorno = date("d/m H:i", strtotime($row['D085_Data_Previsao_Retorno'].' '.$row['D085_Hora_Previsao_Retorno']));
                    mysqli_query("INSERT INTO D085 (D085_D024_Id, D085_Data, D085_Hora, D085_T259_Id, D085_Mensagem, D085_C007_Id, D085_Entidade) VALUES ('{$row['D024_Id']}', '{$row['D085_Data_Previsao_Retorno']}', '{$row['D085_Hora_Previsao_Retorno']}', '{$row['D085_T259_Id']}', '<span class=\"ui-icon\" style=\"float: left; background-position: -80px -114px; margin-left: -2px; height: 13px;\"></span> {$dataHoraRetorno}', '{$row['D085_C007_Id']}', 'RETORNO')");
                    $D085_Id = mysql_insert_id();
                    mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$row['D085_C007_Id']}', 'I', 'Trigger After Insert D085', '" . date('Y-m-d H:i:s') . "', 'D085', '{$D085_Id}', '', '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);

                    mysql_query("UPDATE T259 SET T259_Data_Hora = '{$row['D085_Data_Previsao_Retorno']} {$row['D085_Hora_Previsao_Retorno']}' WHERE T259_Id = '{$row['D085_T259_Id']}'");
                    mysql_query("UPDATE T260 SET T260_Flag_Lido = 'N' WHERE T260_T259_Id = '{$row['D085_T259_Id']}'");
                }
            }

            echo "Encontrado retorno para {$row['D024_Nome_Empresa']} \n";

            $row['D024_Nome_Empresa'] = substr(gLimpaAcentos($row['D024_Nome_Empresa']), 0, 15);
            $row['D085_Hora_Previsao_Retorno'] = substr($row['D085_Hora_Previsao_Retorno'], 0, 5);
            $row['D085_Data_Previsao_Retorno'] = gCorrigeData($row['D085_Data_Previsao_Retorno']);
            $msg = $row['D085_Id']." - Retornar <b>{$row['D024_Nome_Empresa']}</b> em <b>{$row['D085_Data_Previsao_Retorno']}</b> as <b>{$row['D085_Hora_Previsao_Retorno']}</b>";
            if($row['T003_Id'] > 0){
                $msg .= " (Orç:{$row['T003_Id']})";
            }
            $g['empresaAtual'] = $arrayC004[] = $row['T003_C004_Id'];
            $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];

            $resposta['idItem'] = $row['D085_T259_Id'];
            $resposta['titulo'] = $msg;
            $resposta['C007_Id'] = $row['D085_C007_Id'];
            if(isset($g['c029Ids'][207])){
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Tarefa - {$row['D085_T259_Id']}', '/etc/etc017/content/etc017ContentTarefa/', '&acaoId={$row['D085_T259_Id']}&T003_Id={$row['T013_Id']}&D024_Id={$row['D024_Id']}&flagLido=S&notificacao=true', [900,600]);";
            } else {
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Agenda', '/crm/crm001/content/crm001contentEditarAgendaOrcamento/', '&acaoId={$row['D085_Id']}&T003_Id={$row['T013_Id']}&D024_Id={$row['D024_Id']}&flagLido=S', [700,450]);";
            }
            $retorno .= $this->processar($resposta);
        }

        mysql_free_result($res);

/*        $sql = mysql_query("SELECT C004_Id FROM C004");
        while($row = mysql_fetch_array($sql)){
            if(!in_array($row['C004_Id'], $arrayC004)){
                unset($resposta['titulo']);
                $g['empresaAtual'] = $row['C004_Id'];
                $retorno .= $this->processarJS($resposta);
            }
        }
*/
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function pedidoCreditoNaoLiberado() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('16', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T005_Id, T005_Motivo_Nao_Liberado, T005_C007_Id_Vendedor_Interno FROM T005 WHERE T005_Flag_Notificacao_Financeiro = 'N'  AND (T005_Data_Emissao>=DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY)) AND T005_C004_Id='{$g['empresaAtual']}' AND T005_Flag_Status != '10' AND T005_Flag_Status != '8' AND T005_Flag_Status != '3' AND T005_Flag_Status != '5' AND T005_Flag_Status != '4' GROUP BY T005_Id";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $row['T005_Motivo_Nao_Liberado'] = ltrim($row['T005_Motivo_Nao_Liberado']);
                $resposta['idItem'] = $row['T005_Id'];
                $resposta['titulo'] = "<span style=\"color: red;\">Pedido <b>{$row['T005_Id']}</b> não foi aprovado na análise de crédito.</span><br />{$row['T005_Motivo_Nao_Liberado']}";
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$row['T005_Id']}) + '&tabela=T005', [1000,615])) {
                        removerNotificacao('removerNotificacaoPedidoLiberacaoCredito', '{$row['T005_Id']}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function pedidoCreditoLiberado() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('17', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T005_Id, 
                       T005_C007_Id_Vendedor_Interno 
                  FROM T005 
                 WHERE (T005_Data_Emissao>=DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY)) 
                   AND T005_C004_Id='{$g['empresaAtual']}' 
                   AND T005_Flag_Notificacao_Financeiro = 'S' 
                   AND T005_Flag_Status != '10' 
                   AND T005_Flag_Status != '3' 
                   AND T005_Flag_Status != '8' 
                   AND T005_Flag_Status != '5' 
                   AND T005_Flag_Status != '4' GROUP BY T005_Id";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $resposta['idItem'] = $row['T005_Id'];
                $resposta['titulo'] = "<span style=\"color: green;\">Pedido <b>{$row['T005_Id']}</b> foi aprovado na análise de crédito.</span>";
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$row['T005_Id']}) + '&tabela=T005', [1000,615])) {
                        removerNotificacao('removerNotificacaoPedidoLiberacaoCredito', '{$row['T005_Id']}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function pedidoEntradaEstoque() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('18', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T005_Id, 
                       T005_D024_Id, 
                       T005_Motivo_Nao_Liberado, 
                       T005_C007_Id_Vendedor_Interno 
                  FROM T005 
                 WHERE (T005_Data_Emissao>=DATE_ADD(CURRENT_DATE(), INTERVAL -90 DAY))                   
                   AND T005_C004_Id='{$g['empresaAtual']}' 
                   AND T005_Flag_Notificacao_Estoque = 'S' 
                   AND (T005_Flag_Status = '' OR T005_Flag_Status = '0' OR T005_Flag_Status = '14' OR T005_Flag_Status = '15')";
        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $D085_Mensagem = "Entrou estoque para o pedido {$row['T005_Id']}.";

                //Insere atividade na tarefa que chegou estoque
                $sqlTarefa = mysql_query("SELECT D085_T259_Id FROM D085 WHERE (D085_T005_Id = '{$row['T005_Id']}' AND IFNULL(D085_T005_Id,0) > 0)");
                if(mysql_num_rows($sqlTarefa) > 0){
                    $rowTarefa = mysql_fetch_array($sqlTarefa);
                    $sqlD085 = mysql_query("SELECT * FROM D085 WHERE D085_T259_Id = '{$rowTarefa['D085_T259_Id']}' AND D085_Mensagem = '{$D085_Mensagem}'");
                    if(mysql_num_rows($sqlD085) <= 0){
                        mysqli_query("INSERT INTO D085 (D085_D024_Id, D085_Data, D085_Hora, D085_T259_Id, D085_Mensagem, D085_C007_Id, D085_Entidade) VALUES ('{$row['T005_D024_Id']}', CURRENT_DATE(), CURRENT_TIME(), '{$rowTarefa['D085_T259_Id']}', '{$D085_Mensagem}', '{$row['T005_C007_Id_Vendedor_Interno']}', 'EST_PEDIDO')");
                        $D085_Id = mysql_insert_id();
                        mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', 'I', 'Trigger After Insert D085', '" . date('Y-m-d H:i:s') . "', 'D085', '{$D085_Id}', '', '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);
                        mysql_query("UPDATE T259 SET T259_Data_Hora = CURRENT_TIMESTAMP WHERE T259_Id = '{$rowTarefa['D085_T259_Id']}'");
                    }
                }
                $resposta['titulo'] = "Entrou <b>estoque</b> para o pedido <b>{$row['T005_Id']}</b>.";
                $resposta['idItem'] = $row['T005_Id'];
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$row['T005_Id']}) + '&tabela=T005', [1000,615])) {
                        removerNotificacao('removerNotificacaoPedidoEntradaEstoque', '{$row['T005_Id']}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function orcamentoLiberado() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('19', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T003_Id, 
                       T003_C007_Id_Vendedor_Interno,
                       IFNULL(T005_T003_Id,0) as T005_T003_Id
                  FROM T003 
                  LEFT JOIN T005 ON T005_T003_Id=T003_Id
                 WHERE (T003_Data_Emissao>=DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY)) 
                   AND T003_C004_Id='{$g['empresaAtual']}' 
                   AND T003_Flag_Notificacao_Liberacao = 'S' 
                   AND T003_Flag_Status_Orcamento!='C'            
              GROUP BY T003_Id";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                if($row['T005_T003_Id']!=0){continue;}
                $resposta['idItem'] = $row['T003_Id'];
                $resposta['titulo'] = "<span style=\"color: green;\">Orçamento <b>{$row['T003_Id']}</b> foi liberado.</span>";
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent({$row['T003_Id']}) + '&tabela=T003', [1000,615])) {
                        removerNotificacao('removerNotificacaoOrcamento', '{$row['T003_Id']}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T003_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function orcamentoNaoLiberado() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('20', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T003_Id, 
                       T003_Liberou_Alcada_Observacao_Nao_Liberado, 
                       T003_C007_Id_Vendedor_Interno,
                       IFNULL(T005_T003_Id,0) as T005_T003_Id
                FROM T003
           LEFT JOIN T005 ON T005_T003_Id=T003_Id  
               WHERE (T003_Data_Emissao>=DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY))  
                 AND T003_C004_Id='{$g['empresaAtual']}' 
                 AND T003_Flag_Notificacao_Liberacao = 'N' 
                 AND T003_Flag_Status_Orcamento!='C'                
            GROUP BY T003_Id";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                if($row['T005_T003_Id']!=0){continue;}
                $resposta['idItem'] = $row['T003_Id'];
                $resposta['titulo'] = "<span style=\"color: red;\">Orçamento <b>{$row['T003_Id']}</b> não foi liberado.</span><br />{$row['T003_Liberou_Alcada_Observacao_Nao_Liberado']}";
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent({$row['T003_Id']}) + '&tabela=T003', [1000,615])) {
                        removerNotificacao('removerNotificacaoOrcamento', '{$row['T003_Id']}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T003_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function pedidoNaoLiberado() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('21', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T005_Id, 
                       T005_Liberou_Alcada_Observacao_Nao_Liberado, 
                       T005_C007_Id_Vendedor_Interno 
                  FROM T005 
                 WHERE (T005_Data_Emissao>=DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY)) 
                   AND T005_C004_Id='{$g['empresaAtual']}' 
                   AND T005_Flag_Notificacao_Liberacao = 'N' 
                   AND T005_Flag_Status != '10' 
                   AND T005_Flag_Status != '8' 
                   AND T005_Flag_Status != '3' 
                   AND T005_Flag_Status != '5'
                   AND T005_Flag_Status != '4' GROUP BY T005_Id";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $resposta['idItem'] = $row['T005_Id'];
                $resposta['titulo'] = "<span style=\"color: red;\">Pedido <b>{$row['T005_Id']}</b> não foi liberado pela análise comercial.</span><br />{$row['T005_Liberou_Alcada_Observacao_Nao_Liberado']}";
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$row['T005_Id']}) + '&tabela=T005', [1000,615])) {
                        removerNotificacao('removerNotificacaoPedidoLiberacao', '{$row['T005_Id']}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function pedidoLiberado() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('22', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T005_Id, 
                       T005_C007_Id_Vendedor_Interno 
                  FROM T005 
                 WHERE T005_C004_Id='{$g['empresaAtual']}' 
                   AND (T005_Data_Emissao>=DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY)) 
                   AND T005_Flag_Notificacao_Liberacao = 'S' 
                   AND T005_Flag_Status != '10' 
                   AND T005_Flag_Status != '8' 
                   AND T005_Flag_Status != '3' 
                   AND T005_Flag_Status != '5' 
                   AND T005_Flag_Status != '4' GROUP BY T005_Id";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $resposta['idItem'] = $row['T005_Id'];
                $resposta['titulo'] = "<span style=\"color: green;\">Pedido <b>{$row['T005_Id']}</b> foi liberado pela análise comercial.</span>";
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$row['T005_Id']}) + '&tabela=T005', [1000,615])) {
                        removerNotificacao('removerNotificacaoPedidoLiberacao', '{$row['T005_Id']}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function pedidoRetornoAlmoxarifado() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('23', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);

        $sql = "SELECT T005_Id, 
                       T005_C007_Id_Vendedor_Interno 
                  FROM T005 
                 WHERE T005_Data_Emissao>=DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY) 
                   AND T005_C004_Id='{$g['empresaAtual']}'
                   AND T005_Flag_Reenviado_Almoxarifado = 'S' 
                   AND T005_Flag_Reenviado_Almoxarifado_Lido = 'N'           
                   AND (T005_Flag_Status = '' OR T005_Flag_Status = '0' OR T005_Flag_Status = '14' OR T005_Flag_Status = '15')
                   ";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $resposta['idItem'] = $row['T005_Id'];
                $resposta['titulo'] = "<span style=\"color: red;\">Pedido <b>{$row['T005_Id']}</b> retornou do almoxarifado/financeiro.</span>";
                $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$row['T005_Id']}) + '&tabela=T005', [1000,615])) {
                        removerNotificacao('notificacaoRetornoPedidoAlmoxarifado', '{$row['T005_Id']}');
                    }
EOT;
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function orcamentoRetornoContato() {
        global $g;
        $resposta = array('id' => 'orcamentoRetornoContato', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $arrayC004 = array(); //Array dos C004 que estão na resposta do SELECT
        $this->cacheRefresh($resposta['id']);

        //COUNT(DISTINCT T003_Id) AS TotalT003, 
        $sql = "SELECT T003_C004_Id,
                       C004_Nome_Abreviado 
                  FROM T003
             LEFT JOIN T139 ON T139_T003_Id = T003_Id
             LEFT JOIN D085 ON D085_T003_Id = T003_Id
             LEFT JOIN C004 ON T003_C004_Id = C004_Id
                 WHERE T139_Id > 0
                   AND T139_Data_Hora < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 4 HOUR) AND D085_Id IS NULL
                   AND T003_Flag_Status_Orcamento = 'P' AND T003_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}'
             GROUP BY T003_C004_Id";
        $res = mysql_query($sql);
        $countOrcamento = mysql_num_rows($res);
        if($countOrcamento > 0){
            mysql_query("UPDATE T003
                        LEFT JOIN T139 ON T139_T003_Id = T003_Id
                        LEFT JOIN D085 ON D085_T003_Id = T003_Id
                        SET T003_Status_Retorno_Email = 'RETORNAR'
                        WHERE T139_Id > 0 AND T139_Data_Hora < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 4 HOUR) AND D085_Id IS NULL
                        AND T003_Flag_Status_Orcamento = 'P' AND T003_Status_Retorno_Email != 'RETORNAR");
            while($row = mysql_fetch_assoc($res)){
                $g['empresaAtual'] = $arrayC004[] = $row['T003_C004_Id'];
                $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
                $mensagem = "Existe orçamento com agenda pendente.";
                /*if($row['TotalT003'] == 1){
                    $mensagem = "Existe <b>{$row['TotalT003']}</b> orçamento com agenda pendente.";
                } else if($row['TotalT003'] > 1){
                    $mensagem = "Existem <b>{$row['TotalT003']}</b> orçamentos com agenda pendente.";
                }*/
                $resposta['titulo'] = $mensagem . '<br />Verifique em CRM - Orçamentos.';
                $resposta['clickJS'] = "abrirJanela(false, 'crm001', '', menuId('crm001'), 'Agenda Pendente', '.: Agenda Pendente :.', '/crm/crm001/content/crm001ContentOrcamentoRetornoPendente/', '', [1024,615]);";
                $retorno .= $this->processar($resposta);
            }
        }

/*        $sql = mysql_query("SELECT C004_Id FROM C004");
        while($row = mysql_fetch_array($sql)){
            if(!in_array($row['C004_Id'], $arrayC004)){
                unset($resposta['titulo']);
                $g['empresaAtual'] = $row['C004_Id'];
                $retorno .= $this->processarJS($resposta);
            }
        }
*/
        mysql_free_result($res);
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function comprasEmergencia() {
        global $g;
        $resposta = array('id' => str_pad('24', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '',  'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT COUNT(DISTINCT T006_Id) AS qte, 
                                   D009_C007_Id_Comprador, 
                                   IF(IFNULL(D009_C007_Id_Comprador,0) > 0, comprador.C007_Primeiro_Nome, 'Sem comprador') AS C007_Primeiro_Nome
                              FROM T006
                         LEFT JOIN D009 ON D009_Id=T006_D009_Id
                         LEFT JOIN D049 ON D049_Id=D009_D049_Id
                         LEFT JOIN D001 ON D001_Id=D049_D001_Id
                         LEFT JOIN D082 ON D082_Id=D049_D082_Id
                         LEFT JOIN D037 ON D037_Id=T006_D037_Id
                         LEFT JOIN D005 ON D005_Id=T006_D005_Id
                         LEFT JOIN D006 ON D006_Id=T006_D006_Id
                         LEFT JOIN T005 ON T005_Id=T006_T005_Id
                         LEFT JOIN D024 AS Cliente ON T005_D024_Id=Cliente.D024_Id
                         LEFT JOIN T003 ON T003_Id=T005_T003_Id
                         LEFT JOIN T004 ON T004_Id=T006_T004_Id
                         LEFT JOIN T219 ON T219_Id=T006_T219_Id
                         LEFT JOIN T218 ON T218_Id=T219_T218_Id
                         LEFT JOIN T225 ON T219_Id=T225_T219_Id
                         LEFT JOIN T224 ON T224_Id=T225_T224_Id
                         LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
                         LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                         LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
                         LEFT JOIN T241 ON T241_D001_Id=D001_Id
                         LEFT JOIN D024 AS Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                             WHERE D009_C004_Id='{$g['empresaAtual']}' 
                               AND T005_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                               AND T005_Flag_Status!=4 
                               AND T005_Flag_Status!=5
                               AND T005_Flag_Status!=8
                               AND T005_Flag_Status!=26
                               AND T005_Flag_Status!=13
                               AND T005_Flag_Status!=15
                               AND T005_Flag_Status!=6
                               AND T005_Flag_Status!=9
                               AND T005_Flag_Status!=20
                               AND T219_Id IS NOT NULL
                               AND T225_Id IS NULL
                               AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                               AND (T006_Quantidade-T006_Quantidade_Estoque-T006_Quantidade_OC)>0
                               AND (D049_Flag_Tipo='CP' OR D049_Flag_Tipo='PD')            
                               AND T006_Flag_Nao_Comprar != 'S'
                          GROUP BY D009_C007_Id_Comprador");

        /*$sql = mysql_query("SELECT COUNT(T006_Id) as totalT006, IFNULL(D009_C007_Id_Comprador,0) as D009_C007_Id_Comprador, IF(IFNULL(D009_C007_Id_Comprador,0) > 0, C007_Primeiro_Nome, 'Sem comprador') as C007_Primeiro_Nome FROM T006
            LEFT JOIN D009 ON D009_Id = T006_D009_Id
            LEFT JOIN D049 ON D049_Id = D009_D049_Id 
            LEFT JOIN T005 ON T005_Id = T006_T005_Id
            LEFT JOIN T004 ON T004_Id = T006_T004_Id
            LEFT JOIN C007 ON C007_Id = D009_C007_Id_Comprador
            WHERE (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OC) < T006_Quantidade
            AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
            AND T005_C004_Id='{$g['empresaAtual']}'
            AND D049_Flag_Tipo = 'CP'
            GROUP BY D009_C007_Id_Comprador"); */
        // exibir mensagem
        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                if($row['C007_Primeiro_Nome']!="Sem comprador"){
                    $row['C007_Primeiro_Nome']=substr($row['C007_Primeiro_Nome'],0,10);
                }
                $resposta['titulo'] = "Existem <b>{$count}</b> itens para compras emergência! ({$row['C007_Primeiro_Nome']})";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Compras de Estoque', '/est/est009/content/est009ContentComprasEmergencia/', '&mudarAba=true', [990,605]);";
                $resposta['C029'] = array('170', '88');
                $resposta['C031'] = array('desabilitarCompraEmergencia' => 'N');
                $resposta['C007_Id'] = $row['D009_C007_Id_Comprador'];
                $retorno .= $this->processar($resposta);
            }
        }

        /*$sql = mysql_query("SELECT COUNT(T006_Id) as totalT006, T005_C007_Id_Vendedor_Interno FROM T006
            LEFT JOIN D009 ON D009_Id = T006_D009_Id
            LEFT JOIN D049 ON D049_Id = D009_D049_Id
            LEFT JOIN T005 ON T005_Id = T006_T005_Id
            LEFT JOIN T004 ON T004_Id = T006_T004_Id
            WHERE (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OC) < T006_Quantidade
            AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
            AND T005_C004_Id='{$g['empresaAtual']}'
            AND D049_Flag_Tipo = 'CP'
            AND (D009_C007_Id_Comprador = T005_C007_Id_Vendedor_Interno OR IFNULL(D009_C007_Id_Comprador,0) <=0)
            GROUP BY T005_C007_Id_Vendedor_Interno");
        // exibir mensagem
        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                $resposta['titulo'] = "Existem <b>{$row['totalT006']}</b> itens para compras emergência! (Sem comprador)";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Compras de Estoque', '/est/est009/content/est009ContentComprasEmergencia/', '&mudarAba=true', [990,605]);";
                $resposta['C029'] = array('170', '88');
                $resposta['alcada'] = 'V';
                $resposta['C031'] = array('desabilitarCompraEmergencia' => 'N');
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }*/

        if ($xCount<=0){
            mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Id_Mensagem = '{$resposta['id']}' AND N001_C004_Id = '{$g['empresaAtual']}' AND N001_Flag_Excluido='N'");
        }

        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function montagensEmergencia() {
        global $g;
        $resposta = array('id' => str_pad('25', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Id_Mensagem = '{$resposta['id']}' AND N001_C004_Id = '{$g['empresaAtual']}' AND N001_Flag_Excluido='N'");
        $retorno = '';

        $sql = mysql_query("SELECT COUNT(T006_Id) as totalT006, 
                                   IFNULL(D009_C007_Id_Comprador,0) as D009_C007_Id_Comprador, 
                                   C007_Primeiro_Nome 
                              FROM T006
                         LEFT JOIN D009 ON D009_Id = T006_D009_Id
                         LEFT JOIN D049 ON D049_Id = D009_D049_Id
                         LEFT JOIN T005 ON T005_Id = T006_T005_Id
                         LEFT JOIN T004 ON T004_Id = T006_T004_Id
                         LEFT JOIN C007 ON C007_Id = D009_C007_Id_Comprador
                             WHERE T005_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND T005_C004_Id='{$g['empresaAtual']}'
                               AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0) + T006_Quantidade_OC + IFNULL((SELECT SUM(T048_Quantidade) FROM T048 LEFT JOIN T047 ON T047_Id=T048_T047_Id WHERE T048_D049_Id = D049_Id AND T047_C004_Id = D009_C004_Id AND (T047_Flag_Status != 'A' OR T047_Flag_Status IS NULL)),0)) < T006_Quantidade
                               AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
                               AND D049_Flag_Tipo = 'MT'
                               AND IFNULL(D009_C007_Id_Comprador,0) > 0
                          GROUP BY D009_C007_Id_Comprador");
        // exibir mensagem
        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                if($row['C007_Primeiro_Nome']!="Sem comprador"){
                    $row['C007_Primeiro_Nome']=substr($row['C007_Primeiro_Nome'],0,10);
                }
                $resposta['titulo'] = "Existem <b>{$row['totalT006']}</b> itens para montagem emergência! ({$row['C007_Primeiro_Nome']})";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Montagens de Estoque', '/est/est009/content/est009ContentMontagemEmergencia/', '&mudarAba=true', [990,605]);";
                $resposta['C029'] = array('170', '88');
                $resposta['C031'] = array('desabilitarMontagemEmergencia' => 'N');
                $resposta['C007_Id'] = $row['D009_C007_Id_Comprador'];
                $retorno .= $this->processar($resposta);
            }
        }

        $sql = mysql_query("SELECT COUNT(T006_Id) as totalT006, T005_C007_Id_Vendedor_Interno FROM T006
            LEFT JOIN D009 ON D009_Id = T006_D009_Id
            LEFT JOIN D049 ON D049_Id = D009_D049_Id
            LEFT JOIN T005 ON T005_Id = T006_T005_Id
            LEFT JOIN T004 ON T004_Id = T006_T004_Id
            WHERE (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0) + T006_Quantidade_OC +
            IFNULL((SELECT SUM(T048_Quantidade) FROM T048 LEFT JOIN T047 ON T047_Id=T048_T047_Id WHERE T048_D049_Id = D049_Id AND T047_C004_Id = D009_C004_Id AND (T047_Flag_Status != 'A' OR T047_Flag_Status IS NULL)),0)) < T006_Quantidade
            AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
            AND T005_C004_Id='{$g['empresaAtual']}'
            AND D049_Flag_Tipo = 'MT'
        AND (D009_C007_Id_Comprador = T005_C007_Id_Vendedor_Interno OR ifnull(D009_C007_Id_Comprador,0)<=0)
            GROUP BY T005_C007_Id_Vendedor_Interno");
        // exibir mensagem
        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                $resposta['titulo'] = "Existem <b>{$row['totalT006']}</b> itens para montagem emergência! (Sem comprador)";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Montagens de Estoque', '/est/est009/content/est009ContentMontagemEmergencia/', '&mudarAba=true', [990,605]);";
                $resposta['C029'] = array('170', '88');
                $resposta['alcada'] = 'V';
                $resposta['C031'] = array('desabilitarMontagemEmergencia' => 'N');
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        if ($xCount<=0){
            mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Id_Mensagem = '{$resposta['id']}' AND N001_C004_Id = '{$g['empresaAtual']}' AND N001_Flag_Excluido='N'");
        }

        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function servicosEmergencia() {
        global $g;
        $resposta = array('id' => str_pad('26', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT COUNT(T006_Id) as totalT006, 
                                   IFNULL(D009_C007_Id_Comprador,0) as D009_C007_Id_Comprador, 
                                   C007_Primeiro_Nome 
                              FROM T006 
                         LEFT JOIN D009 ON D009_Id = T006_D009_Id
                         LEFT JOIN D049 ON D049_Id = D009_D049_Id 
                         LEFT JOIN T005 ON T005_Id = T006_T005_Id
                         LEFT JOIN T004 ON T004_Id = T006_T004_Id
                         LEFT JOIN C007 ON C007_Id = D009_C007_Id_Comprador
                             WHERE T005_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND T005_C004_Id='{$g['empresaAtual']}'
                               AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OC) < T006_Quantidade 
                               AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)                               
                               AND D049_Flag_Tipo = 'MD'
                               AND IFNULL(D009_C007_Id_Comprador,0) > 0
                          GROUP BY D009_C007_Id_Comprador");
        // exibir mensagem
        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                if($row['C007_Primeiro_Nome']!="Sem comprador"){
                    $row['C007_Primeiro_Nome']=substr($row['C007_Primeiro_Nome'],0,10);
                }
                $resposta['titulo'] = "Existem <b>{$row['totalT006']}</b> itens para serviço emergência! ({$row['C007_Primeiro_Nome']})";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Serviços de Emergência', '/est/est009/content/est009ContentServicosEmergencia/', '&mudarAba=true', [990,605]);";
                $resposta['C029'] = array('170', '88');
                $resposta['C031'] = array('desabilitarServicoEmergencia' => 'N');
                $resposta['C007_Id'] = $row['D009_C007_Id_Comprador'];
                $retorno .= $this->processar($resposta);
            }
        }

        $sql = mysql_query("SELECT COUNT(T006_Id) as totalT006, 
                                   T005_C007_Id_Vendedor_Interno 
                              FROM T006 
                         LEFT JOIN D009 ON D009_Id = T006_D009_Id
                         LEFT JOIN D049 ON D049_Id = D009_D049_Id 
                         LEFT JOIN T005 ON T005_Id = T006_T005_Id
                         LEFT JOIN T004 ON T004_Id = T006_T004_Id
                             WHERE T005_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND T005_C004_Id='{$g['empresaAtual']}'
                               AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OC) < T006_Quantidade 
                               AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
                               AND D049_Flag_Tipo = 'MD'
                               AND (D009_C007_Id_Comprador = T005_C007_Id_Vendedor_Interno OR ifnull(D009_C007_Id_Comprador,0)<=0)
                          GROUP BY T005_C007_Id_Vendedor_Interno");
        // exibir mensagem
        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                $resposta['titulo'] = "Existem <b>{$row['totalT006']}</b> itens para serviço de emergência! (Sem comprador)";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Serviços de Emergência', '/est/est009/content/est009ContentServicosEmergencia/', '&mudarAba=true', [990,605]);";
                $resposta['C029'] = array('170', '88');
                $resposta['alcada'] = 'V';
                $resposta['C031'] = array('desabilitarServicoEmergencia' => 'N');
                $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                $retorno .= $this->processar($resposta);
            }
        }

        if ($xCount<=0){
            mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Id_Mensagem = '{$resposta['id']}' AND N001_C004_Id = '{$g['empresaAtual']}' AND N001_Flag_Excluido='N'");
        }

        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function producoesEmergencia() {
        global $g;
        $resposta = array('id' => str_pad('27', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT COUNT(T006_Id) as totalT006 
                              FROM T006
                         LEFT JOIN D009 ON D009_Id = T006_D009_Id
                         LEFT JOIN D049 ON D049_Id = D009_D049_Id
                         LEFT JOIN T005 ON T005_Id = T006_T005_Id
                             WHERE T005_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND D009_C004_Id='{$g['empresaAtual']}'
                               AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OP) < T006_Quantidade
                               AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
                               AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
                               AND D049_Flag_Tipo = 'PD'");
        // exibir mensagem
        $row = mysql_fetch_assoc($sql);
        if($row['totalT006'] > 0){
            $resposta['titulo'] = "Existem <b>{$row['totalT006']}</b> itens para produção de emergência!";
            $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Produções de Emergência', '/est/est017/content/est017ContentPrincipal/', '&mudarAba=true', [990,605]);";
            $resposta['C029'] = array('170', '88');
            $resposta['C031'] = array('desabilitarProducaoEmergencia' => 'N');
        }

        mysql_free_result($sql);

        return $this->processar($resposta);
    }

    public function gerarNotaFiscal() {
        global $g;
        $resposta = array('id' => str_pad('28', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT count(*) as total,
                                   T005_C004_Id,
                                   C004_Nome_Abreviado
                              FROM T005 
                         LEFT JOIN C004 ON C004_Id=T005_C004_Id 
                             WHERE T005_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND T005_C004_Id = '{$g['empresaAtual']}'
                               AND T005_Flag_Status = '3' 
                               AND ifnull(T005_T005_Id_Agrupado,0)<=0                               
                          GROUP BY T005_C004_Id");
        while ($row = mysql_fetch_array($sql)) {
            $mensagem = "Existem <b>{$row['total']}</b> pedidos para emitir nota fiscal!";
            $resposta['titulo'] = $mensagem;
            $resposta['clickJS'] = <<<EOT
                abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Expedição de Mercadorias', '/est/est002/content/est002ContentPrincipal/', '&mudarAba=true', 'forcarAuto');
                setTimeout(function(){
                    $('.tabs:last').tabs('select', 3);
                }, 500);
EOT;
            $resposta['C029'] = array('17');
        }

        mysql_free_result($sql);

        return $this->processar($resposta);
    }

    public function enviarNotaFiscal() {
        global $g;
        $resposta = array('id' => str_pad('29', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT count(*) as total,
                                   T007_C004_Id,
                                   C004_Nome_Abreviado
                              FROM T007 
                         LEFT JOIN C004 ON C004_Id=T007_C004_Id
                             WHERE T007_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND T007_C004_Id='{$g['empresaAtual']}' 
                               AND T007_Flag_ACP=6 
                               AND T007_TX_Flag_Finalizada != 1 
                               AND IF('1' = '2', T007_Flag_Expedicao = '1', IFNULL(T007_Flag_Expedicao, 1) != 2) 
                               AND (T007_Numero_Protocolo_Nfe = '' OR T007_Numero_Protocolo_Nfe IS NULL) 
                               AND T007_Flag_Inutilizado != 'S'
                               Group by T007_C004_Id");
        $count = mysql_num_rows($sql);
        if($count>0){
            while ($row = mysql_fetch_array($sql)) {
                $mensagem = ($count == 1) ? "Existe <b>{$row['total']}</b> nota fiscal" : "Existem <b>{$row['total']}</b> notas fiscais";
                $mensagem = $mensagem." na fila de envio!";
                // $g['empresaAtual'] = $row['D009_C004_Id'];
                // $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
                $resposta['titulo'] = $mensagem;
                $resposta['clickJS'] = <<<EOT
                    abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Expedição de Mercadorias', '/est/est002/content/est002ContentPrincipal/', '&mudarAba=true', [990,605]);
                    setTimeout(function(){
                        $('.tabs:last').tabs('select', 4);
                    }, 500);
EOT;
                $resposta['C029'] = array('17');
                $retorno.=$this->processar($resposta);
            }
        }
        
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function ordemTransferencia() {
        global $g;
        $resposta = array('id' => str_pad('30', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = mysql_query("SELECT T224_Id 
                              FROM T224 
                         LEFT JOIN T005 ON T005_T224_Id=T224_Id 
                             WHERE T224_Flag_Tipo = 2 
                               AND T005_Id IS NULL 
                               AND T224_C004_Id='{$g['empresaAtual']}'");
        $count = mysql_num_rows($sql);
        if($count > 0){
            $mensagem = ($count == 1) ? "Existe <b>{$count}</b> ordem transferência aguardando geração pedido!" : "Existem <b>{$count}</b> ordens transferência aguardando geração pedido!";
            $resposta['titulo'] = $mensagem;
            $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Ordens', '/est/est016/content/apenasOCs/', '', [990,605]);";
            $resposta['C029'] = array('181');
        }
        
        mysql_free_result($sql);

        return $this->processar($resposta);
    }

    public function estoqueAguardandoColeta() {
        global $g;
        $resposta = array('id' => str_pad('31', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '1','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = mysql_query("SELECT T224_Id 
                              FROM T224 
                             WHERE T224_C004_Id='{$g['empresaAtual']}' 
                               AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null) 
                               AND T224_Flag_Status = '5' 
                          GROUP BY T224_Id");
        $count = mysql_num_rows($sql);
        if($count > 0){
            $mensagem = ($count == 1) ? "Existe <b>{$count}</b> ordem de compra aguardando coleta" : "Existem <b>{$count}</b> ordens de compra aguardando coleta!";
            $resposta['titulo'] = $mensagem;
            $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est013Estoque'), '', 'Estoque', '/est/est003/content/est003Content/', '&tabColetar=S', 'auto', false, 'center');";
            $resposta['C029'] = array('110');
        }
        
        mysql_free_result($sql);

        return $this->processar($resposta);
    }

    public function estoqueMontagemPendente() {
        global $g;
        $resposta = array('id' => str_pad('32', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = mysql_query("SELECT T047_Id FROM T047 WHERE T047_C004_Id = '{$g['empresaAtual']}' AND (T047_Flag_Status != 'A' OR T047_Flag_Status IS NULL) GROUP BY T047_Id");
        $count = mysql_num_rows($sql);
        if($count > 0){
            $mensagem = ($count == 1) ? "Existe <b>{$count}</b> montagem pendente." : "Existem <b>{$count}</b> montagens pendentes.";
            $resposta['titulo'] = $mensagem;
            $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est013Estoque'), '', 'Estoque', '/est/est003/content/est003Content/', '&tabMontagens=S', 'auto', false, 'center');";
            $resposta['C029'] = array('24');
        } else {
           mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Id_Mensagem = '{$resposta['id']}' AND N001_C004_Id = '{$g['empresaAtual']}' AND N001_Flag_Excluido='N'");
        }
        
        mysql_free_result($sql);

        return $this->processar($resposta);
    }

    public function financeiroAlerta() {
        global $g;
        $resposta = array('id' => 'financeiroAlerta', 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT SUM(T015_Valor_Saldo) as Saldo, 
                                   T015_C004_Id, 
                                   C004_Nome_Abreviado 
                              FROM T015
                         LEFT JOIN C004 ON C004_Id = T015_C004_Id
                             WHERE T015_Data_Vencimento = CURDATE() 
                               AND (T015_Data_Pagamento = '0000-00-00' OR T015_Data_Pagamento IS NULL)
                               AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)
                               AND T015_C004_Id = '{$g['empresaAtual']}'
                          GROUP BY T015_C004_Id");
        while($vencer = mysql_fetch_array($sql)){
            $sqlProjetado = mysql_query("SELECT SUM(T018_Valor_Saldo) as Saldo 
                                           FROM T018
                                      LEFT JOIN D014 ON T018_D014_Id=D014_Id
                                          WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') = CURDATE() 
                                            AND T018_C004_Id = '{$vencer['T015_C004_Id']}'
                                            AND ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0))
                                            AND (T018_Ano > 0 AND T018_Mes > 0 AND D014_Dia_Vencimento > 0)");
            $projetado = mysql_fetch_array($sqlProjetado);

            $Saldo = $vencer['Saldo'] + $projetado['Saldo'];
            if($Saldo > 0){
                // $g['empresaAtual'] = $vencer['T015_C004_Id'];
                // $g['C004']['C004_Nome_Abreviado'] = $vencer['C004_Nome_Abreviado'];
                $Saldo = gCorrigeNumero($Saldo);
                $hora = date("H:i:s");
                $resposta['titulo'] = "Contas a pagar vencendo hoje: <b> R$ {$Saldo}</b> ({$hora})";
                $resposta['clickJS'] = 'dialogConfirm("Notificação", "Verifique em contas a pagar.");';
                $resposta['C029'] = array('36');
                $retorno .= $this->processar($resposta);
            }
        }
/*        $sql = mysql_query("SELECT C004_Id FROM C004");
        while($row = mysql_fetch_array($sql)){
            if(!in_array($row['C004_Id'], $arrayC004)){
                unset($resposta['titulo']);
                $g['empresaAtual'] = $row['C004_Id'];
                $retorno .= $this->processarJS($resposta);
            }
        }
*/        
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function contasAPagarStAlerta() {
        global $g;

        $grupoSTContasPagar = $g['C031']['grupoSTContasPagar'];//D032_Id
        $contaSTContasPagar = $g['C031']['contaSTContasPagar'];//D014_Id

        $resposta = array('id' => 'contasAPagarStAlerta', 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT  count(*) as qtde,
                                    SUM(T015_Valor_Saldo) as Saldo, 
                                    T015_C004_Id, 
                                    C004_Nome_Abreviado,
                                    D014_SubConta 
                               FROM T015
                          LEFT JOIN C004 ON C004_Id = T015_C004_Id
                          LEFT JOIN D014 ON D014_Id = T015_D014_Id
                              WHERE T015_D032_Id={$grupoSTContasPagar}
                                AND T015_D014_Id={$contaSTContasPagar}
                                AND (T015_Data_Pagamento = '0000-00-00' OR T015_Data_Pagamento IS NULL)
                                AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)
                           GROUP BY T015_C004_Id");

        while($vencer = mysql_fetch_array($sql)){
            $Saldo =$vencer['Saldo'];
            $Quantidade = $vencer['qtde'];
            $conta = $vencer['D014_SubConta'];
            if($Saldo > 0){
                $g['empresaAtual'] = $vencer['T015_C004_Id'];
                $g['C004']['C004_Nome_Abreviado'] = $vencer['C004_Nome_Abreviado'];
                $Saldo = gCorrigeNumero($Saldo);
                $hora = date("H:i:s");
                $resposta['titulo'] = "$Quantidade guia(s) GNRE-ST a pagar: <b> R$ {$Saldo}</b> ({$hora})";
                $resposta['clickJS'] = "dialogConfirm('Notificação', 'Verifique em contas a pagar: conta $conta');";
                $resposta['C029'] = array('36');
                $retorno .= $this->processar($resposta);
            }
        }
  
        mysql_free_result($sql);
       
        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function verificaLogErros(){
        global $g;
        $resposta = array('id' => str_pad('33', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';
        $dias = 30;

        $sql = mysql_query("SELECT T245_Id,
                                   T245_Referencia,
                                   T245_Tipo_Log 
                              FROM T245
                             WHERE T245_Flag_Erro = 'S' 
                               AND T245_C004_Id = '{$g['empresaAtual']}' 
                               AND (T245_DataHora>=DATE_ADD(CURRENT_DATE(), INTERVAL -{$dias} DAY))
                          ORDER BY T245_Id 
                        DESC LIMIT 10");
                                                                        
        $numErros = mysql_num_rows($sql);
        if($numErros > 0){
            $mensagem = $numErros == 1 ? "Existe {$numErros} erro" : "Existem {$numErros} erros";
            $resposta['titulo'] = $mensagem." de importação de pedido nos últimos {$dias} dias";
            $resposta['clickJS'] = "abrirJanela(event, divIdRootAberto(), '', menuId('LogErros'), 'Log Erros', '.: Log Erros :.', '/sistema/logSistema/outro/contentErros/', '', [1024,600], false, 'full');";
            $resposta['alcada'] = 'D';
            $retorno .= $this->processar($resposta);
        }
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function verificarEstoqueMinimoProduto(){
        global $g;
        
        $resposta = array('id' => str_pad('34', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT COUNT(*) as totalItens,
                                   D009_C004_Id,
                                   C004_Nome_Abreviado
                              FROM D001 
                         LEFT JOIN D049 ON D049_D001_Id=D001_Id 
                         LEFT JOIN D009 ON D009_D049_Id=D049_Id 
                         LEFT JOIN C004 ON D009_C004_Id=C004_Id
                             WHERE D001_Flag_Ativo='S'
                               AND D009_Quantidade_Comprar_Calculada>0
                          GROUP BY D009_C004_Id");
        //$produtos = mysql_num_rows($sql);
        while($row = mysql_fetch_array($sql)){
            
            if($row['totalItens'] > 0){
                $g['empresaAtual']                = $row['D009_C004_Id'];
                $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
                $mensagem                         = ($row['totalItens'] == 1) ? 'Existe' : 'Existem';

                $resposta['titulo']  = $mensagem." <b>{$row['totalItens']}</b> produtos em quantidade mínima de estoque.";
                $resposta['clickJS'] = 'dialogConfirm("Notificação", "Em <b>Estoque->SRM->Compras Estoque</b> filtre os produtos com sugestão de compra maior que 0.")';
                $resposta['C029']    = array('5','181');
                $retorno .= $this->processar($resposta);
            }
        }
        
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function produtosEstoqueVazio(){
        global $g;
        $resposta = array('id' => 'produtosEstoqueVazio', 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $extraBase = "D009_Quantidade_Estoque <= 0 AND D009_Quantidade_Comprar_Calculada > 0 AND D009_Data_Ultima_Venda >= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -30 DAY) AND D001_Flag_Ativo='S'";
        $sql = mysql_query("SELECT D009_C004_Id, 
                                   C004_Nome_Abreviado 
                              FROM D009
                         LEFT JOIN D049 ON D049_Id=D009_D049_Id
                         LEFT JOIN D001 ON D001_Id=D049_D001_Id
                         LEFT JOIN C004 ON C004_Id=D009_C004_Id
                             WHERE {$extraBase}
                              GROUP BY D009_C004_Id");

        while ($row = mysql_fetch_array($sql)) {
            $g['empresaAtual'] = $row['D009_C004_Id'];
            $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
            $hora = date("H:i:s");
            $extra = base64_encode($extraBase . " AND D009_C004_Id = '{$row['D009_C004_Id']}'");
            $mensagem = "<span style=\"color: red;\">Existem produtos em falta no estoque. ({$hora})</span>";
            $resposta['titulo'] = $mensagem;
            $resposta['clickJS'] = <<<EOT
                abrirJanela(false, divIdRootAberto(), '', menuId('est009Estoque'), '', 'Produtos em falta', '/est/est009/content/est009ContentProdutosEstoqueVazio/', '&ocultarFiltro=true&extraFixo=' + encodeURIComponent('{$extra}'), 'full');
EOT;
            $resposta['C029'] = array('94');
            $retorno .= $this->processar($resposta);
        }

/*      $sql = mysql_query("SELECT C004_Id FROM C004");
        while($row = mysql_fetch_array($sql)){
            if(!in_array($row['C004_Id'], $arrayC004)){
                unset($resposta['titulo']);
                $g['empresaAtual'] = $row['C004_Id'];
                $retorno .= $this->processarJS($resposta);
            }
        }
*/        
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function produtosEstoqueEmergencia(){
        global $g;
        $resposta = array('id' => 'produtosEstoqueEmergencia', 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $extraBase = "D001_Flag_Ativo='S' AND D049_Flag_Nao_Comprar='N' AND D009_Quantidade_Compra_Emergencia > 0";
        $sql = mysql_query("SELECT D009_C004_Id, 
                                   C004_Nome_Abreviado 
                              FROM D009
                         LEFT JOIN D049 ON D049_Id=D009_D049_Id
                         LEFT JOIN D001 ON D001_Id=D049_D001_Id
                         LEFT JOIN C004 ON C004_Id=D009_C004_Id
                             WHERE {$extraBase}
                              GROUP BY D009_C004_Id");

        while ($row = mysql_fetch_array($sql)) {
            $g['empresaAtual'] = $row['D009_C004_Id'];
            $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
            $hora = date("H:i:s");
            $extra = base64_encode("D009_C004_Id = '{$row['D009_C004_Id']}' AND " . $extraBase);
            $mensagem = "<span style=\"color: red;\">Existem produtos em emergencia de compra. ({$hora})</span>";
            $resposta['titulo'] = $mensagem;
            $resposta['clickJS'] = <<<EOT
                abrirJanela(false, divIdRootAberto(), '', menuId('est009Estoque'), '', 'Produtos em Emergencia', '/est/est009/content/est009ContentprodutosEstoqueEmergencia/', '&ocultarFiltro=true&extraFixo=' + encodeURIComponent('{$extra}'), 'full');
EOT;
            $resposta['C029'] = array('94');
            $retorno .= $this->processar($resposta);
        }

/*      $sql = mysql_query("SELECT C004_Id FROM C004");
        while($row = mysql_fetch_array($sql)){
            if(!in_array($row['C004_Id'], $arrayC004)){
                unset($resposta['titulo']);
                $g['empresaAtual'] = $row['C004_Id'];
                $retorno .= $this->processarJS($resposta);
            }
        }
*/        
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function historicoSemLocacao(){
        global $g;
        $resposta = array('id' => 'historicoSemLocacao', 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        //COUNT(DISTINCT T001_Id) as TotalD009
        $sql = mysql_query("SELECT count(*) as total,
                                   C004_Nome_Abreviado, 
                                   D009_C004_Id 
                                 FROM T001
                            LEFT JOIN D009 ON D009_Id = T001_D009_Id
                            LEFT JOIN D049 ON D049_Id = D009_D049_Id
                            LEFT JOIN D001 ON D001_Id = D049_D001_Id
                            LEFT JOIN C004 ON C004_Id = D009_C004_Id
                            WHERE (IFNULL(T001_T066_Id, 0) <= 0 OR IFNULL(T001_D004_Id, 0) <= 0) 
                            AND T001_Data_lancamento > C004_Data_Inicio_Sistema
                            AND T001_Flag_Operacao != 'C'
                            AND T001_Flag_Operacao != 'N'
                            AND T001_Quantidade > 0
                            AND D009_C004_Id = '{$g['empresaAtual']}'
                            GROUP BY D009_C004_Id");

        while($row = mysql_fetch_array($sql)) {
            // $g['empresaAtual'] = $row['D009_C004_Id'];
            // $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];            
            $hora = date("H:i:s");
            $mensagem = "<span style=\"color: red;\">Existem {$row['total']} históricos sem locação. ({$hora})</span>";
            $resposta['titulo'] = $mensagem;
            $resposta['clickJS'] = <<<EOT
            abrirJanela(false, divIdRootAberto(), '', menuId('est009Historico'), '', 'Histórico sem locação', '/est/est009/content/est009ContentProdutosHistoricoSemLocacao/', '', 'auto', false, 'center');
EOT;
            $resposta['C029'] = array('186');
            $resposta['alcada'] = 'DS';
            $retorno .= $this->processar($resposta);
        }

        
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function analiseCusto(){
        global $g;
        $resposta = array('id' => str_pad('35', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '1','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT T004_Id 
                              FROM T004
                         LEFT JOIN T003 ON T003_Id=T004_T003_Id
                         LEFT JOIN T006 ON T004_Id=T006_T004_Id
                         LEFT JOIN D009 on D009_Id=T004_D009_Id
                         LEFT JOIN D049 on D049_Id=D009_D049_Id
                             WHERE T003_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND T003_C004_Id='{$g['empresaAtual']}'
                               AND T004_Flag_Comprar = 'S' 
                               AND T003_Flag_Perdido != 'S' 
                               AND T003_Flag_Status_Orcamento != 'V'
                               AND T006_Id IS NULL AND D049_Flag_Tipo = 'PD'");
        $rows = mysql_num_rows($sql);
        if($rows > 0){
            $resposta['titulo'] = ($rows == 1) ? "Existe <b>1</b> item para análise de custo" : "Existem <b>{$rows}</b> itens para análise de custo";
            $resposta['C029'] = array('203');
            $resposta['clickJS'] = <<<EOT
                abrirJanela(false, divIdRootAberto(), '', menuId('est017'), '', 'P.C.P', '/est/est017/content/est017ContentPrincipal/', '', 'full');
EOT;
            $retorno .= $this->processar($resposta);
        }
        
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function retornoAnaliseCusto(){
        global $g;
        $resposta = array('id' => str_pad('36', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = mysql_query("SELECT T003_Id, 
                                   T003_C007_Id_Vendedor_Interno 
                              FROM T004
                         LEFT JOIN T003 ON T003_Id=T004_T003_Id
                         LEFT JOIN T006 ON T004_Id=T006_T004_Id
                         LEFT JOIN D009 on D009_Id=T004_D009_Id
                         LEFT JOIN D049 on D049_Id=D009_D049_Id
                             WHERE T003_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                               AND T004_Flag_Comprar = 'C' 
                               AND T003_C004_Id='{$g['empresaAtual']}'
                               AND T003_Flag_Perdido != 'S'
                               AND T003_Flag_Status_Orcamento != 'V'
                               AND T006_Id IS NULL AND D049_Flag_Tipo = 'PD'
                          GROUP BY T003_Id");
        if(mysql_num_rows($sql) > 0){
            while($row = mysql_fetch_array($sql)){
                $resposta['titulo'] = "Existe retorno de P.C.P para o orçamento <b>{$row['T003_Id']}</b>";
                $resposta['idItem'] = $row['T003_Id'];
                $resposta['C007_Id'] = $row['T003_C007_Id_Vendedor_Interno'];
                $resposta['clickJS'] = <<<EOT
                    abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent({$row['T003_Id']}) + '&tabela=T003', [990,605]);
EOT;
                $retorno .= $this->processar($resposta);
            }
        }
        
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function notificacaoAtualizacaoTabelaFornecedor() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('37', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => true);
        $this->cacheRefresh($resposta['id']);

        $dataIni = date('Y-m-d', strtotime('-7 days'));

        $sql = "SELECT D070_Id,
                       SUBSTR(D069_Data_Atualizacao_Preco,1,10) Data,
                       D024_Nome_Empresa,D024_Nome_Fantasia, 
                       COUNT(*) AS itens                       
                  FROM D069 
             LEFT JOIN D070 ON D070_Id=D069_D070_Id 
             LEFT JOIN D024 ON D070_D024_Id=D024_Id
                WHERE D069_Data_Atualizacao_Preco >= '$dataIni' AND D070_C004_Id = '{$g['empresaAtual']}'
              GROUP BY D070_Id, SUBSTR(D069_Data_Atualizacao_Preco,1,10)
              ORDER BY D070_Id";
       
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                // $g['empresaAtual'] = $row['C004_Id'];
                // $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
                $D024_Nome_Empresa = substr($row['D024_Nome_Empresa'],0,15);
                $data = gCorrigeData($row['Data']);
                $resposta['idItem'] = $row['D070_Id'].$row['Data'];
                $resposta['titulo']  = "<span><b>" . $row['itens'] . "</b> itens <b>Tab. " . $row['D070_Id'] . '-' . $D024_Nome_Empresa . "</b> atualizado ". $data ."</span>";
                $resposta['clickJS'] = 'dialogConfirm("Notificação", "Filtre em tabela fornecedor pelo campo Data preço.")';
                $resposta['C029']    = array('170','171');
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function tabelaFornecedorVencida() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('38', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'false');
        $this->cacheRefresh($resposta['id']);
        
        $sql = "SELECT D070_Id,
                       D024_Nome_Empresa,
                       D070_Data_Vencimento,
                       D070_C004_Id 
                  FROM D070 
             LEFT JOIN D024 ON D070_D024_Id=D024_Id 
                 WHERE D070_Data_Vencimento < CURDATE() 
                   AND D070_Data_Vencimento BETWEEN DATE_SUB(CURDATE(), INTERVAL 10 DAY) AND CURRENT_DATE() 
                   AND D070_Flag_Ativo!='N'
                   AND D070_C004_Id = '{$g['empresaAtual']}'
              ORDER BY D070_C004_Id,D070_Id";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                // $g['empresaAtual']   = $row['D070_C004_Id'];
                $resposta['idItem']  = $row['D070_Id'];
                $data_vencimento     = gCorrigeData($row["D070_Data_Vencimento"]);
                $resposta['titulo']  = "<span style=\"color: red;\">A tab.forn Nº<b>{$row['D070_Id']}</b> venceu nos últimos 10 dias ({$data_vencimento}).</span>";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '' ,menuId('cad002'), '', 'Alterar Tabela Fornecedor', '/cad/cad002/content/cad002ContentListaFornecedorAlterar/', '&acaoId=' + encodeURIComponent('{$row['D070_Id']}'), [1000,600]);";
                $resposta['C029']    = array('170','171');
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function notificacoesBoletoSemRemessa(){
        global $g;
        $resposta = array('id' => str_pad('39', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $diasNotificar = $g['C031']['DiasEnviarNotificacaoBoletoSemRemessa'];
        if($diasNotificar>0){

            $sqlT117 = "SELECT T117_Id 
                            FROM T117 
                        LEFT JOIN T002 ON T002_T117_Id=T117_Id
                            WHERE T117_Flag_Cancelado != 'S' 
                            AND T117_C004_Id='{$g['empresaAtual']}'
                            AND IFNULL(T002_T035_Id,'')=''
                            AND T002_Data_Recebimento = '0000-00-00'
                            AND (T117_Data_Vencimento>=CURRENT_DATE() AND T117_Data_Vencimento<=DATE_ADD(CURDATE(), INTERVAL {$diasNotificar} DAY));";
            $res = mysql_query($sqlT117);
            $rows = 0;
            if ($res) {
                $rows = mysql_num_rows($res);
            }
            if ($rows > 0) {
                $mensagem = ($rows == 1) ? 'Existe' : 'Existem';
                $resposta['titulo'] = $mensagem." <b>{$rows}</b> boletos vencendo em <b>{$diasNotificar}</b> dias sem remessa.";
                $resposta['clickJS'] = 'dialogConfirm("Notificação", "Filtre <b>contas a receber/boletos</b> em aberto, com vencimento em até 30 dias, que não estão cancelados e que não possuem remessa bancária.")';
                $resposta['C029'] = array('35');
            }

            mysql_free_result($res);
            return $this->processar($resposta);
        }
    }

    public function purchaseOrderAnalise() {
        global $g;
        $resposta = array('id' => str_pad('40', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $sql = "SELECT COUNT(*) AS total,
                       T295_Flag_Alcada 
                  FROM T295 
                 WHERE T295_C004_Id='{$g['empresaAtual']}' 
                   AND T295_Flag_Status = '1' 
              GROUP BY T295_Flag_Alcada";

        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0){
            while($row = mysql_fetch_assoc($res)){
                $extra               = ($row['total'] == 1) ? 'análise de <b>Puchase Order</b> pendente.' : 'análises de <b>Puchase Orders</b> pendentes.';
                $mensagem            = ($row['total'] == 1) ? 'Existe' : 'Existem';
                $resposta['titulo']  = $mensagem." <b>{$row['total']}</b> {$extra}";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('imp003_'), 'Análise PO', '.:Análise PO:.', '/imp/imp005/content/imp005ContentPrincipalAnalise/', '', [950,550], false, false , 'center');";
                $resposta['C029']    = array('255');
                $resposta['alcada']  = $row['T295_Flag_Alcada'];
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function produtosVencendo() {
        global $g;
        $resposta = array('id' => str_pad('41', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        
        $retorno          = '';
        $diasAntecedencia = $g['C031']['diasAntecedenciaNotificarValidadeProduto'];

        
        $extraBase = "D001A_Flag_Validade = 'S'
                      AND D001_Flag_Ativo != 'N'
                      AND D001_Flag_Pre_Cadastro != 'S'                      
                      AND T066A_Data_Validade > '0000-00-00'
                      AND T066_Quantidade_Estoque > 0
                      AND (T066A_Data_Validade <= DATE_ADD(CURRENT_DATE(), INTERVAL {$diasAntecedencia} DAY))
                      AND T066A_Data_Validade > CURRENT_DATE()";

        $sql = "SELECT D009_C004_Id
                  FROM T066A
             LEFT JOIN T066 ON T066_Id  = T066A_T066_Id 
             LEFT JOIN D009 ON D009_Id  = T066_D009_Id
             LEFT JOIN D049 ON D049_Id  = D009_D049_Id  
             LEFT JOIN D001 ON D001_Id  = D049_D001_Id 
             LEFT JOIN D001A ON D001_Id = D001A_D001_Id 
                 WHERE $extraBase
              GROUP BY D009_C004_Id";

        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0){
            while($row = mysql_fetch_assoc($res)){
                $g['empresaAtual']   = $row['D009_C004_Id'];
                $extra               = base64_encode("D009_C004_Id = '{$row['D009_C004_Id']}' AND " .$extraBase);
                $resposta['titulo']  = "Existem produtos vencendo nos próximos <b>{$diasAntecedencia}</b> dias.";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('cad002'), '', 'Itens vencendo nos próximos {$diasAntecedencia} dias', '/cad/cad002/content/contentValidade/', '&ocultarFiltro=true&extraFixo=' + encodeURIComponent('{$extra}') + '&validadeProduto=S', 'full');";        
                $resposta['C029']    = array('186');
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function documentoProdutoVencendo() {
        global $g;
        $resposta = array('id' => str_pad('42', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $retorno          = '';
        $diasAntecedencia = '30';

        $extraBase = "T140_Data_Validade > '0000-00-00'
                      AND (T140_Data_Validade <= DATE_ADD(CURRENT_DATE(), INTERVAL {$diasAntecedencia} DAY))
                      AND T140_Data_Validade > CURRENT_DATE()";
        
        $sql = "SELECT T140_Id,
                       D009_C004_Id
                  FROM T140
            LEFT JOIN T303 ON T140_Id = T303_T140_Id
            LEFT JOIN D001 ON D001_Id = T303_D001_Id
            LEFT JOIN D049 ON D001_Id = D049_D001_Id
            LEFT JOIN D009 ON D049_Id = D009_D049_Id
                 WHERE $extraBase
                 GROUP BY T140_Id";

        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0){
            while($row = mysql_fetch_assoc($res)){
                $extra = base64_encode($extraBase);
                $g['empresaAtual']   = $row['D009_C004_Id'];
                $resposta['titulo']  = "Existem documentos vencendo nos próximos <b>{$diasAntecedencia}</b> dias.";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('cad002'), '', 'Documentos vencendo nos próximos {$diasAntecedencia} dias', '/cad/cad002/content/contentDocumentoValidade/', '&ocultarFiltro=true&extraFixo=' + encodeURIComponent('{$extra}'), 'full');";        
                $resposta['C029']    = array('186');
                $retorno .= $this->processar($resposta);
            }            
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function comprasEmergenciaInternacional() {
        global $g;
        $resposta = array('id' => str_pad('43', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '',  'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';
        $xCount  = 0;
        $sql = mysql_query("SELECT D009_C007_Id_Comprador, 
                                   IF(IFNULL(D009_C007_Id_Comprador,0) > 0, comprador.C007_Primeiro_Nome, 'Sem comprador') as C007_Primeiro_Nome,
                                   COUNT(*) AS total
                              FROM T006
                         LEFT JOIN D009 on D009_Id=T006_D009_Id
			             LEFT JOIN D049 on D049_Id=D009_D049_Id
			             LEFT JOIN D006 on D006_Id=T006_D006_Id
			             LEFT JOIN T005 ON T005_Id=T006_T005_Id
			             LEFT JOIN T003 ON T003_Id=T005_T003_Id
			             LEFT JOIN T004 ON T004_Id=T006_T004_Id
			             LEFT JOIN T294 ON T006_Id=T294_T006_Id OR T004_Id=T294_T004_Id
			             LEFT JOIN T293 ON T293_Id=T294_T293_Id
			             LEFT JOIN T296 ON T294_Id=T296_T294_Id
			             LEFT JOIN T295 ON T295_Id=T296_T295_Id
			             LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			                 WHERE D009_C004_Id='{$g['empresaAtual']}'
                               AND T005_Data_Emissao >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
			                   AND T005_Flag_Status!=4 
			                   AND T005_Flag_Status!=5
			                   AND T005_Flag_Status!=8
			                   AND T005_Flag_Status!=26
			                   AND T005_Flag_Status!=13
			                   AND T005_Flag_Status!=15
			                   AND T005_Flag_Status!=6
			                   AND T005_Flag_Status!=9
			                   AND T005_Flag_Status!=20
			                   AND T294_Id IS NULL
			                   AND T296_Id IS NULL
			                   AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado IS NULL)
			                   AND (T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-if(T006_Quantidade_OC>0,T006_Quantidade_OC,0))>0
			                   AND (D049_Flag_Tipo='CP' OR D049_Flag_Tipo='PD')			
			                   AND T006_Flag_Nao_Comprar != 'S'
			                   AND D006_Flag_Estoque = 'D'
			                   AND (D049_Origem_Mercadoria = 1 OR D049_Origem_Mercadoria = 2 OR D049_Origem_Mercadoria = 6 OR D049_Origem_Mercadoria = 7)
			              GROUP BY D009_C007_Id_Comprador");                          

        $count = mysql_num_rows($sql);
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                if($row['C007_Primeiro_Nome']!="Sem comprador"){
                    $row['C007_Primeiro_Nome']=substr($row['C007_Primeiro_Nome'],0,10);
                }
                $resposta['titulo']  = "Existem <b>{$row['total']}</b> itens para compra emergência <u><b>EX</b></u>. ({$row['C007_Primeiro_Nome']})";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Compras Estoque Internacional', '/est/est019/content/est019ContentComprasEmergencia/', '&mudarAba=true', 'full');";
                $resposta['C029']    = array('49','259');
                $resposta['C031']    = array('desabilitarCompraEmergenciaInternacional' => 'N');
                $resposta['C007_Id'] = $row['D009_C007_Id_Comprador'];
                $retorno .= $this->processar($resposta);
            }
        }

        if ($xCount<=0){
            mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Id_Mensagem = '{$resposta['id']}' AND N001_C004_Id = '{$g['empresaAtual']}' AND N001_Flag_Excluido='N'");
        }

        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function cotacaoEmergenciaInternacional() {
        global $g;
        $resposta = array('id' => str_pad('44', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';
        $xCount  = 0;
        
        $sql = mysql_query("SELECT D009_C007_Id_Comprador, 
                                   IF(IFNULL(D009_C007_Id_Comprador,0) > 0, comprador.C007_Primeiro_Nome, 'Sem comprador') as C007_Primeiro_Nome,
                                   D009_C004_Id,
                                   COUNT(*) AS total
                              FROM T004 
                         LEFT JOIN D009 ON D009_Id=T004_D009_Id
                         LEFT JOIN D049 ON D049_Id=D009_D049_Id
                         LEFT JOIN D006 ON D006_Id=T004_D006_Id
                         LEFT JOIN T006 on T006_T004_Id=T004_Id
                         LEFT JOIN T005 ON T005_Id=T006_T005_Id
                         LEFT JOIN T003 ON T003_Id=T004_T003_Id
                         LEFT JOIN T294 ON T294_T004_Id=T004_Id
                         LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                             WHERE D009_C004_Id='{$g['empresaAtual']}'
                               AND T003_Flag_Status_Orcamento = 'P'
                               AND T006_Id is null
                               AND T294_Id is null
                               AND T004_Flag_Comprar='S'
                               AND (D049_Flag_Tipo='CP' OR D049_Flag_Tipo='PD')
                               AND D006_Flag_Estoque = 'D'
                               AND (D049_Origem_Mercadoria = 1 OR D049_Origem_Mercadoria = 2 OR D049_Origem_Mercadoria = 6 OR D049_Origem_Mercadoria = 7)
                          GROUP BY D009_C004_Id, D009_C007_Id_Comprador");

        $count = mysql_num_rows($sql);
        $xCount += $count;
        if($count > 0){
            while($row = mysql_fetch_assoc($sql)){
                if($row['C007_Primeiro_Nome']!="Sem comprador"){
                    $row['C007_Primeiro_Nome']=substr($row['C007_Primeiro_Nome'],0,10);
                }
                $resposta['titulo']  = "Existem <b>{$row['total']}</b> itens para cotação emergência <u><b>EX</b></u>. ({$row['C007_Primeiro_Nome']})";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('est016'), '', 'Compras de Estoque Internacional', '/est/est019/content/est019ContentCotacaoEmergencia/', '&mudarAba=true', 'full');";
                $resposta['C029']    = array('49','259');
                $resposta['C031']    = array('desabilitarCotacaoEmergenciaInternacional' => 'N');
                $resposta['C007_Id'] = $row['D009_C007_Id_Comprador'];
                $retorno .= $this->processar($resposta);
            }
        }

        if ($xCount<=0){
            mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Id_Mensagem = '{$resposta['id']}' AND N001_C004_Id = '{$g['empresaAtual']}' AND N001_Flag_Excluido='N'");
        }

        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function documentoProdutoVencido() {
        global $g;
        $resposta = array('id' => str_pad('45', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $extraBase = "T140_Data_Validade > '0000-00-00'
                      AND (T140_Data_Validade >= DATE_ADD(CURRENT_DATE(), INTERVAL -15 DAY))
                      AND T140_Data_Validade < CURRENT_DATE()";

        $sql = "SELECT T140_Id,
                       D009_C004_Id
                  FROM T140
             LEFT JOIN T303 ON T140_Id = T303_T140_Id
             LEFT JOIN D001 ON D001_Id = T303_D001_Id
             LEFT JOIN D049 ON D001_Id = D049_D001_Id
             LEFT JOIN D009 ON D049_Id = D009_D049_Id
                 WHERE $extraBase
              GROUP BY T140_Id";                

        $res   = mysql_query($sql);
        $count = mysql_num_rows($res);
        if($count > 0){     
            while($row = mysql_fetch_assoc($res)){   
                $extra = base64_encode($extraBase);
                $g['empresaAtual']   = $row['D009_C004_Id'];
                $resposta['titulo']  = "<span style=\"color: red;\"> Existem documentos vencidos nos últimos <b>15</b> dias.</span>";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('cad002'), '', 'Documentos vencidos nos últimos 15 dias', '/cad/cad002/content/contentDocumentoValidade/', '&ocultarFiltro=true&extraFixo=' + encodeURIComponent('{$extra}'), 'full');";        
                $resposta['C029']    = array('186');
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function produtosVencidos() {
        global $g;
        $resposta = array('id' => str_pad('46', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $extraBase = "D001A_Flag_Validade = 'S'
                      AND D001_Flag_Ativo != 'N'
                      AND D001_Flag_Pre_Cadastro != 'S'                      
                      AND T066A_Data_Validade > '0000-00-00'
                      AND T066_Quantidade_Estoque > 0
                      AND (T066A_Data_Validade >= DATE_ADD(CURRENT_DATE(), INTERVAL -15 DAY))
                      AND T066A_Data_Validade < CURRENT_DATE()";

        $sql = "SELECT D009_C004_Id
                  FROM T066A
             LEFT JOIN T066 ON T066_Id  = T066A_T066_Id 
             LEFT JOIN D009 ON D009_Id  = T066_D009_Id
             LEFT JOIN D049 ON D049_Id  = D009_D049_Id  
             LEFT JOIN D001 ON D001_Id  = D049_D001_Id 
             LEFT JOIN D001A ON D001_Id = D001A_D001_Id 
                 WHERE $extraBase
              GROUP BY D009_C004_Id";
        
        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0){
            while($row = mysql_fetch_assoc($res)){
                $g['empresaAtual']   = $row['D009_C004_Id'];
                $extra               = base64_encode("D009_C004_Id = '{$row['D009_C004_Id']}' AND " .$extraBase);
                $resposta['titulo']  = "<span style=\"color: red;\"> Existem produtos vencidos nos últimos <b>15</b> dias.</span>";
                $resposta['clickJS'] = "abrirJanela(false, divIdRootAberto(), '', menuId('cad002'), '', 'Itens vencidos nos últimos 15 dias', '/cad/cad002/content/contentValidade/', '&ocultarFiltro=true&extraFixo=' + encodeURIComponent('{$extra}') + '&validadeProduto=S', 'full');";        
                $resposta['C029']    = array('186');
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($res);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function produtosEstoqueEmergenciaInternacional(){
        global $g;
        $resposta = array('id' => 'produtosEstoqueEmergenciaInternacional', 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');
        //$resposta = array('id' => '45', 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '2','permiteOcultar' => 'true');

        $this->cacheRefresh($resposta['id']);
        $retorno = '';

        $extraBase = "D001_Flag_Ativo='S' 
                      AND D049_Flag_Nao_Comprar='N' 
                      AND D009_Quantidade_Compra_Emergencia > 0";

        $sql = mysql_query("SELECT D009_C004_Id,                                    
                                   COUNT(*) AS total 
                              FROM D009
                         LEFT JOIN D049 ON D049_Id=D009_D049_Id
                         LEFT JOIN D001 ON D001_Id=D049_D001_Id                         
                             WHERE {$extraBase}
                               AND (D049_Origem_Mercadoria = 1 OR D049_Origem_Mercadoria = 2 OR D049_Origem_Mercadoria = 6 OR D049_Origem_Mercadoria = 7)
                               AND D009_C004_Id = '{$g['empresaAtual']}'
                              GROUP BY D009_C004_Id");

        $count = mysql_num_rows($sql);
        if($count > 0){
            while ($row = mysql_fetch_array($sql)) {
                // $g['empresaAtual'] = $row['D009_C004_Id'];
                // $g['C004']['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];
                //$hora = date("H:i:s");
                $extra = base64_encode("D009_C004_Id = '{$row['D009_C004_Id']}' AND " . $extraBase);
                $mensagem = "<span style=\"color: red;\">Existem <b>{$row['total']}</b> produtos em emergência de compra <u><b>EX</b></u>.</span>";
                $resposta['titulo'] = $mensagem;
                $resposta['clickJS'] = <<<EOT
                    abrirJanela(false, divIdRootAberto(), '', menuId('est009Estoque'), '', 'Produtos em Emergencia', '/est/est019/content/est019ContentEstoqueEmergencia/', '&ocultarFiltro=true&extraFixo=' + encodeURIComponent('{$extra}'), 'full');
    EOT;
                $resposta['C029'] = array('94','49');
                $retorno .= $this->processar($resposta);
            }
        }

        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }


    /******************************************** CARLOS CARVALHO ********************************************/
    public function alteracaoProdutoGenerico() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('48', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = " select 
                    T006_Item,
                    T005_Id,
                    T006_Id,
                    T005_C007_Id_Vendedor_Interno,
                    T006A_Flag_Producao_Emergencia
                from 
                    T006 
                    left join T006A on T006A_T006_Id = T006_Id
                    left join T005  on T006_T005_Id  = T005_Id 
                where 
                    T006A_Flag_Notificacao = 'S'";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                $flag = $row['T006A_Flag_Producao_Emergencia'];
                if($flag == 'N'){
                    $resposta['idItem'] = $row['T006_Id'];
                    $resposta['titulo'] = "<span style=\"color: green;\">O produto generico <b>{$row['T006_Item']}</b> do pedido <b>{$row['T005_Id']}</b> foi alterado.</span>";
                    $resposta['clickJS'] = <<<EOT
                            if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$row['T005_Id']}) + '&tabela=T005', [1000,615])) {
                                removerNotificacao('removerNotificacaoPedido', '{$row['T006_Id']}');
                            }
        EOT;
                    $resposta['C007_Id'] = $row['T005_C007_Id_Vendedor_Interno'];
                    $retorno .= $this->processar($resposta);

                    
                }
            }
        }
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function alteracaoPrecoGenericoOrcamento() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('49', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = " select 
                    T004_Item,
                    T003_Id,
                    T004_Id,
                    T003_C007_Id_Vendedor_Interno
                from 
                    T004 
                    left join T004A on T004A_T004_Id = T004_Id
                    left join T003  on T004_T003_Id  = T003_Id 
                where 
                    T004A_Flag_Notificacao = 'S'";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                    $resposta['idItem'] = $row['T004_Id'];
                    $resposta['titulo'] = "<span style=\"color: green;\">O preço do produto generico <b>{$row['T004_Item']}</b> do orçamento <b>{$row['T003_Id']}</b> foi alterado.</span>";
                    $resposta['clickJS'] = <<<EOT
                    if (abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent({$row['T003_Id']}) + '&tabela=T003', [990,605])) {
                        removerNotificacao('removerNotificacaoPrecoGenerico', '{$row['T004_Id']}');
                    }
        EOT;
                    $resposta['C007_Id'] = $row['T003_C007_Id_Vendedor_Interno'];
                    $retorno .= $this->processar($resposta);
            }
        }
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    }

    public function baixaAutomatica() {
        global $g;
        $retorno = '';
        $resposta = array('id' => str_pad('50', 3, "9", STR_PAD_LEFT), 'C029' => '', 'C031' => '', 'C007_Id' => '', 'alcada' => '', 'idItem' => '', 'cor' => '', 'prioridade' => '0','permiteOcultar' => 'true');
        $this->cacheRefresh($resposta['id']);

        $sql = " select 
                    T255_Id,
                    T255_C007_Id
                from
                    T255 
                left join T255A on T255A_T255_Id = T255_Id 
                where 
                    T255_Data_Hora_Baixa_Materiais = '0000-00-00 00:00:00'
                    and T255A_Flag_Baixa_Automatica      = 'S'";
        $res = mysql_query($sql);

        if(mysql_num_rows($res) > 0){
            // exibir mensagem
            while($row = mysql_fetch_assoc($res)){
                    $resposta['idItem'] = $row['T255_Id'];
                    $resposta['titulo'] = "<span style=\"color: green;\">Não foi possível fazer a baixa automática da ordem de produção <b>{$row['T255_Id']}</b>. Por favor, verifique.</span>";
                    $resposta['clickJS'] = <<<EOT
                    abrirJanela(false, divIdRootAberto(), '', unique(), '', 'Editar Orçamento', '/est/est017/content/est017ContentOrdemProducao/', '&acaoId=' + encodeURIComponent({$row['T255_Id']}) + '&tabela=T255', [990,605]);
                    
        EOT;
                    $resposta['C007_Id'] = $row['T004_C007_Id_Comprador'];
                    $retorno .= $this->processar($resposta);
            }
        }
        mysql_free_result($sql);

        return empty($retorno) ? $this->processar($resposta) : $retorno;
    } 

    /**
    * Processar
    * Executa o insert/update/delete das notificações na tabela N001
    */
    public function processar($resposta) {
        global $g;
        $retorno = '';
        if (empty($resposta)) {
            return '';
        }
        $resposta['titulo'] = $resposta['titulo'];
        $resposta['clickJS'] = trim($resposta['clickJS']);
        $resposta['clickJS'] = str_replace("\n", ' ', $resposta['clickJS']);
        $resposta['clickJS'] = preg_replace('/\s+/', ' ', $resposta['clickJS']);
        $resposta['clickJS'] = $resposta['clickJS'];
        $resposta['C029'] = !empty($resposta['C029']) ? serialize($resposta['C029']) : '';
        $resposta['C031'] = !empty($resposta['C031']) ? serialize($resposta['C031']) : '';
        $resposta['C007_Id'] = empty($resposta['C007_Id']) ? '0' : $resposta['C007_Id'];
        $resposta['uniqId'] = $resposta['id'].$resposta['idItem'].$g['empresaAtual'].ord($resposta['alcada']).$resposta['C007_Id'];

        if(!empty($resposta['titulo'])){
            $notificacao = array('N001_C004_Id' => $g['empresaAtual'],
                                 'N001_C004_Nome_Abreviado' => utf8_encode($g['C004']['C004_Nome_Abreviado']),
                                 'N001_C007_Id' => $resposta['C007_Id'],
                                 'N001_C029' => $resposta['C029'],
                                 'N001_C031' => $resposta['C031'],
                                 'N001_Flag_Alcada' => $resposta['alcada'],
                                 'N001_Id_Mensagem' => $resposta['id'],
                                 'N001_Uniqid_Mensagem' => $resposta['uniqId'],
                                 'N001_Mensagem' => $resposta['titulo'],
                                 'N001_Funcao_JS' => $resposta['clickJS'],
                                 'N001_Cor' => $resposta['cor'],
                                 'N001_Prioridade' => $resposta['prioridade'],
                                 'N001_Permite_Ocultar' => $resposta['permiteOcultar']);                                 
            // $this->gravarNotificacao($notificacao);
            $this->gravarNotificacaoArquivo($notificacao);
        }
        return true;
    }

    /**
    * ProcessarJS
    * Retorna o codigo javascript das notificacoes (USADO APENAS PARAS AS NOTIFICAÇÕES NIVEL 2 E 3)
    */
    public function processarJS($resposta) {
        global $g;
        $config = unserialize($g['C007']['C007_Configuracao_Notificacoes']);
        $retorno = '';
        if (empty($resposta)) {
            return '';
        }
        $resposta['id'] = 'cache2'.$resposta['id'];
        if(!isset($g['C019'][$g['empresaAtual']])) { return ''; } //Verifica se o usuário tem perfil na empresa da notificação

        // Se não tiver mensagens, garante de remover todas
        if (!empty($resposta) && isset($resposta['id']) && !isset($resposta['titulo'])) {
            return "alertRemover('{$resposta['id']}', '{$g['empresaAtual']}');";
        }

        // Se for uma mensagem com um item (idItem), precisa remover todas antes (somente na primeira vez)
        if (!empty($resposta) && isset($resposta['idItem']) && !isset($g['removerMsgUmaVez'][$resposta['id']])) {
            $retorno .= "alertRemover('{$resposta['id']}', '{$g['empresaAtual']}');";
            $g['removerMsgUmaVez'][$resposta['id']] = true;
        }

        $item = 'false';
        if (isset($resposta['idItem'])) {
            $item = "'{$resposta['idItem']}'";
        }

        $exibir = 'false';
        $idNotificacao = "{$resposta['id']}-{$g['empresaAtual']}";
        if(isset($config[$idNotificacao]) && $config[$idNotificacao] == '0'){
            $exibir = 'true';
        }
        $retorno .= <<<EOT
            alertAdicionar('{$g['empresaAtual']}', '{$g['C004']['C004_Nome_Abreviado']}', '{$resposta['id']}', {$item}, '{$resposta['titulo']}', function() {
                {$resposta['clickJS']}
            }, true, '', $exibir);
EOT;
        return $retorno;
    }

    /*
    public function gravarNotificacao($notificacao)
    {
        global $g, $confUsuario;

        //Recupera o cache da memória
        $cacheN001 = apc_fetch('cacheN001-'.$confUsuario['dbDatabase']);
        $cacheN001 = unserialize( $cacheN001 );
        //Insere a nova notificação no cache
        $cacheN001[$notificacao['N001_Uniqid_Mensagem']] = $notificacao;
        $cacheN001['lastUpdate'] = time();
        //Grava o cache
        apc_delete('cacheN001-'.$confUsuario['dbDatabase']);
        apc_store('cacheN001-'.$confUsuario['dbDatabase'], serialize($cacheN001));
    }*/

    public function gravarNotificacaoArquivo($notificacao)
    {
        global $g, $confUsuario;
        //Recupera o cache da memória
        $Dir = $g['pathDados'] . 'tmp/notificacoes/';
		is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}cacheN001Notificacoes_" . $notificacao['N001_Id_Mensagem'] . ".txt";
        $cacheN001 = file_get_contents($arquivoNome);

        echo "\n\n Gravando arquivo " . $arquivoNome ;

        $N001_Uniqid_Mensagem = $notificacao['N001_Uniqid_Mensagem']; 

        if ($cacheN001) {
            $cacheN001 = unserialize($cacheN001);
            $lido = $cacheN001["$N001_Uniqid_Mensagem"]['lido'];
            $this->cacheRefreshUnique($notificacao['id'], $notificacao['N001_Uniqid_Mensagem']);
        } 
            
        //Cria nova notificação
        if (is_array($notificacao)) {
            $cacheN001["$N001_Uniqid_Mensagem"] = $notificacao;
            if (is_array($cacheN001["$N001_Uniqid_Mensagem"])) {
                $cacheN001["$N001_Uniqid_Mensagem"]['lido'] = "N";
            }
            $cacheN001['lastUpdate'] = time();
        }

        //Grava o cache
        $h = fopen($arquivoNome, "w");
        $r = fwrite($h, serialize($cacheN001));
        fclose($h);
    }
    
    public function cacheConsultaNotificacao($Id = null, $Uniqid_Mensagem = null)
    {
        global $g, $confUsuario;
        
        if($Uniqid_Mensagem){
            
            $this->cacheRefreshUnique($Id, $Uniqid_Mensagem);

        }else{

            //Recupera o cache da memória
            $Dir = $g['pathDados'] . 'tmp/notificacoes/';
            is_dir($Dir) or mkdir($Dir, 0777, true);
            $arquivoNome = "{$Dir}cacheN001Notificacoes_" . $Id . ".txt";
            $cacheN001 = unserialize(file_get_contents($arquivoNome));

            $cacheUniqueId = array();
            foreach ($cacheN001 as $uniqId => $notificacao) {
                if($notificacao['N001_Id_Mensagem'] == $Id && $notificacao['N001_C004_Id'] == $g['empresaAtual']){
                    $cacheUniqueId[$notificacao['N001_Uniqid_Mensagem']] = $notificacao['N001_Uniqid_Mensagem'];
                }
            }

            return $cacheUniqueId;

        }
    }

    public function xordenarNotificacoesx()
    {
        global $g, $confUsuario;
        $Dir = $g['pathDados'] . 'tmp/notificacoes/';
        is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}cacheN001Notificacoes.txt";
        $cacheN001 = file_get_contents($arquivoNome);
        if ($cacheN001){
            $cacheN001 = unserialize($cacheN001);
            //Executa loop no cache para atualizar todos as notificações para excluir, posteriormente as novas notificações serão gravadas no cache
            foreach ($cacheN001 as $uniqId => &$notificacao) {
                uasort($cacheN001, function($a,$b){
                    if ($a["N001_Prioridade"]==$b["N001_Prioridade"]) return 0;
                    return ($a["N001_Prioridade"] >= $b["N001_Prioridade"]) ? -1 : 1;
                });
            }

            //Grava o cache
            $h = fopen($arquivoNome, "w");
            $r = fwrite($h, serialize($cacheN001));
            fclose($h);
        }
    }

    public function cacheRefresh($idNotificacao)
    {
        global $g, $confUsuario;
        $Dir = $g['pathDados'] . 'tmp/notificacoes/';
		is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}cacheN001Notificacoes_" . $idNotificacao . ".txt";

        //Recupera o cache da memória
        $cacheN001 = file_get_contents($arquivoNome);
        if ($cacheN001) {
            $cacheN001 = unserialize($cacheN001);
            //Executa loop no cache para atualizar todos as notificações para excluir, posteriormente as novas notificações serão gravadas no cache
            foreach ($cacheN001 as $uniqId => &$notificacao) {
                if($notificacao['N001_Id_Mensagem'] == $idNotificacao && $notificacao['N001_C004_Id'] == $g['empresaAtual']){
                    unset($cacheN001[$uniqId]);
                }
            }
            //Grava o cache
            $h = fopen($arquivoNome, "w");
            $r = fwrite($h, serialize($cacheN001));
            fclose($h);
        }
    }
    
    public function cacheRefreshUnique($Id, $uniqid_Id)
    {
        global $g, $confUsuario;
        $Dir = $g['pathDados'] . 'tmp/notificacoes/';
		is_dir($Dir) or mkdir($Dir, 0777, true);
        $arquivoNome = "{$Dir}cacheN001Notificacoes_" . $Id . ".txt";

        //Recupera o cache da memória
        $cacheN001 = file_get_contents($arquivoNome);
        if ($cacheN001) {
            $cacheN001 = unserialize($cacheN001);
            //Executa loop no cache para atualizar todos as notificações para excluir, posteriormente as novas notificações serão gravadas no cache
        
            if (is_array($cacheN001)) {
                unset($cacheN001[$uniqid_Id]);
            }
                
            //Grava o cache
            $h = fopen($arquivoNome, "w");
            $r = fwrite($h, serialize($cacheN001));
            fclose($h);
        }
    }
    public function cacheRefreshCache($idNotificacao)
    {
        global $g, $confUsuario;
        //Recupera o cache da memória
        $cacheN001 = unserialize(apc_fetch('cacheN001-'.$confUsuario['dbDatabase']));
        //Executa loop no cache para atualizar todos as notificações para excluir, posteriormente as novas notificações serão gravadas no cache
        foreach ($cacheN001 as $uniqId => &$notificacao) {
            if($notificacao['N001_Id_Mensagem'] == $idNotificacao && $notificacao['N001_C004_Id'] == $g['empresaAtual']){
                unset($cacheN001[$uniqId]);
            }
        }
        //Grava o cache
        apc_store('cacheN001-'.$confUsuario['dbDatabase'], serialize($cacheN001));
    }

    public function notificacoesUpdate() {
        global $g;
        
        // Retirar notifiação de orçamentos liberado que já geraram pedido
        $sql = <<<EOT
                    UPDATE T003 
                 LEFT JOIN T005 ON T005_T003_Id=T003_Id AND T005_Flag_Status != 8
                       SET T003_Flag_Notificacao_Liberacao = ''
                     WHERE T003_Flag_Notificacao_Liberacao = 'S' 
                       AND T005_Id IS NOT NULL;
EOT;
        mysqli_query($sql);

    }
}
