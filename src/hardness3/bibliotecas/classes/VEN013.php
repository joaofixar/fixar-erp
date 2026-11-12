<?php
namespace hardness;

definirClasse('VEN013');
class VEN013_
{
    /**
     * ven013NaoLiberarPedido
     *
     * @param Form $form
     */
    public function ven013NaoLiberarPedido($form)
    {
        global $g;
        require_once('bibliotecas/classes/NOTIFICACOES.php');
        $NOTIFICACOES = new NOTIFICACOES();
        $T005_Id                                     = $form->campoValorEnviado('T005_Id');
        $T005_Liberou_Alcada_Observacao_Nao_Liberado = $form->campoValorEnviado('T005_Liberou_Alcada_Observacao_Nao_Liberado');
        
        if (empty($T005_Id)) {
            return "T005_Id não existe";
        }
        $T005_Liberou_Alcada_Observacao_Nao_Liberado = trim($T005_Liberou_Alcada_Observacao_Nao_Liberado);
        $T005_Liberou_Alcada_Observacao_Nao_Liberado = addslashes($T005_Liberou_Alcada_Observacao_Nao_Liberado);
        
        if (empty($T005_Liberou_Alcada_Observacao_Nao_Liberado)) {
            return "Preencha o campo Motivo.";
        }
        
        $cSql  = mysql_query("update T005 set T005_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                      T005_Liberou_Alcada_Data=now(),
                                      T005_Flag_Status='15',
                                      T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),
                                      T005_Liberou_Alcada_IPV=0,
                                      T005_Liberou_Alcada_Prazo_Medio=0,
                                      T005_Liberou_Alcada_Desconto=0,
                                      T005_Flag_Status_Liberacao='3',
                                      T005_Liberou_Alcada_Observacao_Nao_Liberado='{$T005_Liberou_Alcada_Observacao_Nao_Liberado}',
                                      T005_Flag_Notificacao_Liberacao='N'                           
                                where T005_Id='{$T005_Id}'");
        $cErro = mysql_error();

        mysql_query("update T005A 
                  left join T005 on T005_Id = T005A_T005_Id
                        set T005A_Liberou_Alcada_Frete=0
                      where T005A_T005_Id='{$T005_Id}'");
                      
        //$NOTIFICACOES->liberarCreditoPedido();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro/p/pErro:$cErro/p/pClausula:$cClausula";
        } else {
            $T178  = "insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Pedido <b>NEGADO</b> na análise comercial. <b>Motivo:</b> {$T005_Liberou_Alcada_Observacao_Nao_Liberado}','')";
            mysql_query($T178);
            return true;
        }
    }
    
    /**
     * ven013AprovarPedido
     *
     * @param int $T005_Id
     * @param boolean $viaTablet
     */
    public function ven013AprovarPedido($T005_Id, $viaTablet = false)
    {
        global $g, $mensagem;
        require_once("bibliotecas/classes/VEN002.php");
        $VEN002 = new VEN002();
        require_once('bibliotecas/classes/NOTIFICACOES.php');
        $NOTIFICACOES = new NOTIFICACOES();
        
        $retorno = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
        $T005    = mysql_fetch_array($retorno);
        $total   = $T005['T005_Valor_Total_Produtos'];
        
        if ($viaTablet == false) {
            $desconto = $VEN002->calculaPercentualDescontoPedido($T005_Id);
        } else {
            // PEGA O VALOR DO DESCONTO MANUAL QUE É APLICADO NO VALOR TOTAL DO ORÇAMENTO
            $total    = $T005['T005_Valor_Total'];
            $desconto = ($T005['T005_Valor_Desconto_Manual'] * 100) / ($T005['T005_Valor_Desconto_Manual'] + $T005['T005_Valor_Total']);
        }
        
        $total = $T005['T005_Valor_Total'];
        
        $retorno = mysql_query("SELECT AVG(T090_Prazos_Dias) FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
        $T090    = mysql_fetch_array($retorno);
        
        $retorno  = mysql_query("SELECT count(*) as Parcelas FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
        $parcelas = mysql_fetch_array($retorno);
        
        if ($parcelas['Parcelas'] == 1) {
            $retorno                   = mysql_query("SELECT D027_Portador FROM T090 LEFT JOIN D027 ON D027_Id = T090_D027_Id WHERE T090_T005_Id='{$T005_Id}'");
            $portador                  = mysql_fetch_array($retorno);
            $portador['D027_Portador'] = trim($portador['D027_Portador']);
        }
        
        $cSql = mysql_query("update T005 set T005_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                      T005_Liberou_Alcada_Data=now(),
                                      T005_Liberou_Alcada_IPV=T005_IPV,
                                      T005_Flag_Status='14',
                                      T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),
                                      T005_Flag_Status_Liberacao='2',
                                      T005_Motivo_Pedido_Liberacao='',
                                      T005_Liberou_Alcada_Valor_Total='{$total}',                                      
                                      T005_Liberou_Alcada_Prazo_Medio='{$T090['AVG(T090_Prazos_Dias)']}',
                                      T005_Liberou_Alcada_Desconto='{$desconto}',
                                      T005_Liberou_Parcelas_Pagamento='{$parcelas['Parcelas']}',
                                      T005_Liberou_Portador_Pagamento='{$portador['D027_Portador']}',
                                      T005_Flag_Notificacao_Liberacao='S'
                                where T005_Id='{$T005_Id}'");
        //$NOTIFICACOES->liberarCreditoPedido();
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro/p/pErro:$cErro/p/pClausula:$cClausula";
        }

        mysql_query("update T005A 
                  left join T005 on T005_Id = T005A_T005_Id
                        set T005A_Liberou_Alcada_Frete=T005_Valor_Frete
                      where T005A_T005_Id='{$T005_Id}'");
        
        $sqlT006 = mysql_query("UPDATE T006 LEFT JOIN D009 on D009_Id=T004_D009_Id SET T006_Liberou_Alcada_Saldo_Futuro=D009_Quantidade_Estoque_Liquido-T006_Quantidade WHERE T006_T005_Id='{$T005_Id}'");
        
        $FaturarPedidoAposAnalisePedido = $g['C031']['FaturarPedidoAposAnalisePedido'];
        
        if ($FaturarPedidoAposAnalisePedido == "S") {
            $mensagem = '';
            $retorno  = $VEN002->faturarPedido($T005_Id);
            $msg      = "<b>Análise de pedido:</b><br />Pedido Aprovado com sucesso!<br /><br /><b>Faturamento automático:</b><br />";
            $msg .= $retorno;
            $T178  = "insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Aprovou na análise comercial (alçadas) - Faturamento automático','')";
            mysql_query($T178);
            if (empty($mensagem)) {
                return $msg;
            } else {
                $msg .= $mensagem;
                return $msg;
            }
            
        } else {
            $T178  = "insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'<b>Aprovou</b> na análise comercial (alçadas)','')";
            mysql_query($T178);
            return "Pedido aprovado com sucesso!";
        }
    }
}
