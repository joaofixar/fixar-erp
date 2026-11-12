<?php
namespace hardness;
definirClasse('SRV004');
class SRV004_
{
    
    /**
     * aprovarTroca
     *
     * @param String $T138_Id
     * @return String/bool
     */     
    public function aprovarTroca($T138_Id)
    {
        $D048_Id = 1;
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START SESSION");
        
        $cClausula = "SELECT * FROM T138 WHERE T138_Id='{$T138_Id}'";
        $cSql_T138 = mysql_query("$cClausula");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'erro ao buscar o T138';
        }
        
        while ($mT138 = mysql_fetch_array($cSql_T138)) {
            $cClausula = " SELECT *  ";
            $cClausula .= " FROM D009 ";
            $cClausula .= " LEFT JOIN D049 ON D049_Id=D009_D049_Id ";
            $cClausula .= " LEFT JOIN D001 ON D049_D001_Id=D001_Id ";
            $cClausula .= " WHERE D009_Id='{$mT138['T138_D009_Id']}' ";
            $cSql_D009 = mysql_query("$cClausula");
            $cErro     = mysql_error();
            if (mysql_error()) {
                mysql_query("ROLL BACK");
                return "Ocorreu um erro: " . str_replace(array(
                    "'",
                    '"'
                ), "", $cErro) . "/pClausula: " . str_replace(array(
                    "'",
                    '"'
                ), "", $cClausula);
            }
            $mD009 = mysql_fetch_array($cSql_D009);
            
            ////////////////////////////////////////////////////////////////////////////////////////////
            // ENCONTRA O ID DO T066
            ////////////////////////////////////////////////////////////////////////////////////////////
            $cClausula = "SELECT T066_Id FROM T066 WHERE T066_D048_Id='{$D048_Id}' and T066_D009_Id='{$mT138['T138_D009_Id']}'";
            $cSql_T066 = mysql_query("$cClausula");
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLL BACK");
                return "Ocorreu um erro: " . str_replace(array(
                    "'",
                    '"'
                ), "", $cErro) . "/pClausula: " . str_replace(array(
                    "'",
                    '"'
                ), "", $cClausula);
            }
            $mT066 = mysql_fetch_array($cSql_T066);
            
            ////////////////////////////////////////////////////////////////////////////////////////////
            // HISTORICO DO PRODUTO
            ////////////////////////////////////////////////////////////////////////////////////////////
            $Descricao_Operacao = "TROCA PEDIDO-{$T138_Id}";
            
            $tmpD004_Id = getD004($mT066['T066_Id']);
            $cClausula  = " INSERT INTO T001 ( ";
            $cClausula .= " T001_D024_Id_Cliente, ";
            $cClausula .= " T001_D024_Id, ";
            $cClausula .= " T001_T138_Id, ";
            $cClausula .= " T001_D009_Id, ";
            $cClausula .= " T001_T066_Id, ";
            $cClausula .= " T001_C004_Id, ";
            $cClausula .= " T001_Codigo_Produto, ";
            $cClausula .= " T001_Data_Lancamento, ";
            $cClausula .= " T001_Descricao_Operacao, ";
            $cClausula .= " T001_Quantidade, ";
            $cClausula .= " T001_Flag_Complemento, ";
            $cClausula .= " T001_Flag_Operacao, ";
            $cClausula .= " T001_Valor_Preco_Unitario, ";
            $cClausula .= " T001_Observacao, ";
            $cClausula .= " T001_Valor_Custo_Unitario, ";
            $cClausula .= " T001_Flag_Atualiza_Custo, ";
            $cClausula .= " T001_D004_Id ";
            //$cClausula .= " T001_Quantidade_Saldo ";
            $cClausula .= " ) VALUES ( ";
            $cClausula .= " 0, ";
            $cClausula .= " '{$mD009['D049_D024_Id']}', ";
            $cClausula .= " '{$mT138['T138_Id']}', ";
            $cClausula .= " '{$mT138['T138_D009_Id']}', ";
            $cClausula .= " '{$mT066['T066_Id']}', ";
            $cClausula .= " '{$mT138['T138_C004_Id']}', ";
            $cClausula .= " '{$mD009['D001_Codigo_Produto']}', ";
            $cClausula .= " current_date(), ";
            $cClausula .= " '{$Descricao_Operacao}', ";
            $cClausula .= " '{$mT138['T138_Quantidade']}', ";
            $cClausula .= " '', ";
            $cClausula .= " 'E', ";
            $cClausula .= " '{$mT138['T138_Valor_Preco_Unitario']}', ";
            $cClausula .= " '', ";
            $cClausula .= " '{$mT138['T138_Valor_Preco_Unitario']}', ";
            $cClausula .= " 'S', ";
            $cClausula .= " '{$tmpD004_Id}' ";
            //$cClausula .= " '' ";
            $cClausula .= " ) ";
            mysql_query("$cClausula");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLL BACK");
                return "Ocorreu um erro: " . str_replace(array(
                    "'",
                    '"'
                ), "", $cErro) . "/pClausula: " . str_replace(array(
                    "'",
                    '"'
                ), "", $cClausula);
            }
            
            mysql_query("UPDATE T138 SET T138_Data_Inspecao=NOW() WHERE T138_Id ='$T138_Id'");
            if (mysql_error()) {
                mysql_query("ROLL BACK");
                return 'Erro ao confirmar inspeção, entre em contato com o suporte';
            }
            //D001_reprocessa_historico(0,$mT030['T030_D009_Id']);
        }
        
        mysql_query("COMMIT");
        
        return true;
    }
}
