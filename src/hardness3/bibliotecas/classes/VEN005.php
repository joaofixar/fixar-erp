<?php
namespace hardness;

definirClasse('VEN005');
class VEN005_
{
	/**
     * ven005NaoLiberarOrcamento
     *
     * @param Form $form
     */
    public function ven005NaoLiberarOrcamento($form)
    {
        global $g;
        $T003_Id                                     = $form->campoValorEnviado('T003_Id');
        $T003_Liberou_Alcada_Observacao_Nao_Liberado = $form->campoValorEnviado('T003_Liberou_Alcada_Observacao_Nao_Liberado');
        require_once('bibliotecas/classes/NOTIFICACOES.php');
        $NOTIFICACOES = new NOTIFICACOES();
        
        if (empty($T003_Id)) {
            return "T003_Id não existe";
        }
        
        $T003_Liberou_Alcada_Observacao_Nao_Liberado = trim($T003_Liberou_Alcada_Observacao_Nao_Liberado);
        $T003_Liberou_Alcada_Observacao_Nao_Liberado = addslashes($T003_Liberou_Alcada_Observacao_Nao_Liberado);
        
        if (empty($T003_Liberou_Alcada_Observacao_Nao_Liberado)) {
            return "Preencha o campo Motivo.";
        }
        $cSql  = mysql_query("update T003 set T003_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                      T003_Liberou_Alcada_Data=now(),
                                      T003_Flag_PDF_Impresso='N',
                                      T003_Flag_Status_Orcamento='N',
                                      T003_Liberou_Alcada_IPV=0,
                                      T003_Liberou_Alcada_Prazo_Medio=0,
                                      T003_Liberou_Alcada_Desconto=0,
                                      T003_Liberou_Alcada_Observacao_Nao_Liberado='{$T003_Liberou_Alcada_Observacao_Nao_Liberado}',
                                      T003_Flag_Status_Liberacao='3',
                                      T003_Flag_Notificacao_Liberacao='N'
                                where T003_Id='{$T003_Id}'");
        $cErro = mysql_error();

        mysql_query("update T003A 
                  left join T003 on T003_Id = T003A_T003_Id
                        set T003A_Liberou_Alcada_Frete=0
                      where T003A_T003_Id='{$T003_Id}'");

        //$NOTIFICACOES->liberarCreditoOrcamento();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro/p/pErro:$cErro/p/pClausula:$cClausula";
        } else {
            return true;
        }
    }
    
    /**
     * ven005AprovarOrcamento
     *
     * @param int $T003_Id
     * @param boolean $viaTablet
     */
    public function ven005AprovarOrcamento($T003_Id, $viaTablet = false)
    {
        global $g;
        require_once("bibliotecas/classes/VEN001.php");
        $VEN001          = new VEN001();
        require_once('bibliotecas/classes/NOTIFICACOES.php');
        $NOTIFICACOES = new NOTIFICACOES();

        $retorno         = mysql_query("SELECT T003_Valor_Total_Produtos,T003_Valor_Total_Confirmado,T003_Valor_Total,T003_Valor_Desconto,T003_Valor_Desconto_Manual FROM T003 WHERE T003_Id='{$T003_Id}'");
        $T003            = mysql_fetch_array($retorno);
        $totalConfirmado = $T003['T003_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento  = $T003['T003_Valor_Total_Produtos'];
        $total           = "";
        if ($viaTablet == false) {
            $desconto = $VEN001->calculaPercentualDescontoOrcamento($T003_Id);
            if ($totalConfirmado <= 0) {
                $total = $totalOrcamento;
            } else {
                $total = $totalConfirmado;
            }
        } else {
            //$desconto = (($T003['T003_Valor_Desconto']+$T003['T003_Valor_Desconto_Manual'])*100)/($T003['T003_Valor_Desconto_Manual']+$T003['T003_Valor_Desconto']+$T003['T003_Valor_Total']);
            $desconto = ($T003['T003_Valor_Desconto_Manual'] * 100) / ($T003['T003_Valor_Desconto_Manual'] + $T003['T003_Valor_Total']);
            $total    = $T003['T003_Valor_Total'];
        }
        
        $retorno = mysql_query("SELECT AVG(T089_Prazos_Dias) FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
        $T089    = mysql_fetch_array($retorno);
        
        $retorno  = mysql_query("SELECT count(*) as Parcelas FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
        $parcelas = mysql_fetch_array($retorno);
        
        if ($parcelas['Parcelas'] == 1) {
            $retorno                   = mysql_query("SELECT D027_Portador FROM T089 LEFT JOIN D027 ON D027_Id = T089_D027_Id WHERE T089_T003_Id='{$T003_Id}'");
            $portador                  = mysql_fetch_array($retorno);
            $portador['D027_Portador'] = trim($portador['D027_Portador']);
        }
        
        $total = $T003['T003_Valor_Total'];
        
        $cSql  = mysql_query("update T003 set T003_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                      T003_Liberou_Alcada_Data=now(),
                                      T003_Flag_PDF_Impresso='N',
                                      T003_Liberou_Alcada_IPV=T003_IPV,
                                      T003_Flag_Status_Orcamento='A',
                                      T003_Liberou_Alcada_Valor_Total='{$total}',
                                      T003_Liberou_Alcada_Cortesia_Valor_Total='{$total}',
                                      T003_Flag_Liberou_Alcada_Cortesia='S',
                                      T003_Flag_Status_Liberacao='2',
                                      T003_Liberou_Alcada_Prazo_Medio='{$T089['AVG(T089_Prazos_Dias)']}',
                                      T003_Motivo_Pedido_Liberacao='',
                                      T003_Liberou_Alcada_Desconto='{$desconto}',
                                      T003_Liberou_Parcelas_Pagamento='{$parcelas['Parcelas']}',
                                      T003_Liberou_Portador_Pagamento='{$portador['D027_Portador']}',
                                      T003_Flag_Notificacao_Liberacao='S'
                                where T003_Id='{$T003_Id}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro/p/pErro:$cErro/p/pClausula:$cClausula";
        }

        mysql_query("update T003A 
                  left join T003 on T003_Id = T003A_T003_Id
                        set T003A_Liberou_Alcada_Frete=T003_Valor_Frete
                      where T003A_T003_Id='{$T003_Id}'");
        
        //$NOTIFICACOES->liberarCreditoOrcamento();
        $sqlT004 = mysql_query("UPDATE T004 LEFT JOIN D009 on D009_Id=T004_D009_Id SET T004_Liberou_Alcada_Saldo_Futuro=D009_Quantidade_Estoque_Liquido-if(T004_Quantidade_Confirmada>0,T004_Quantidade_Confirmada,T004_Quantidade) WHERE T004_T003_Id='{$T003_Id}'");
        return true;
    }
}
