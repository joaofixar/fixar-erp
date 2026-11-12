<?php
namespace hardness;

definirClasse('EST011');
class EST011_
{
    /**
     * gerarRemessa
     *
     * @param int $T183_Id
     */
    public function gerarRemessa($T183_Id)
    {
        
        $sql = mysql_query("SELECT * FROM T182 WHERE T182_Marcado='S'");
        if (mysql_num_rows($sql) <= 0)
            return 'Não foram marcados ítens para gerar remessa...';
        
        $sql   = mysql_query("SELECT * FROM T183 WHERE T183_Id = '$T183_Id'");
        $mT183 = mysql_fetch_array($sql);
        
        // calcular o proximo numero da remessa
        $sql     = mysql_query("SELECT MAX(T183_Numero) FROM T183 WHERE T183_D024_Id = '{$mT183['T183_D024_Id']}'");
        $retorno = mysql_fetch_array($sql);
        if ($retorno['MAX(T183_Numero)'] > 0)
            $proxNum = $retorno['MAX(T183_Numero)'] + 1;
        else
            $proxNum = 1;
        
        mysql_query("UPDATE T183 SET T183_Data=NOW(), T183_Numero='$proxNum' WHERE T183_Id = '$T183_Id'");
        
        if (mysql_error()) {
            mysql_query("delete FROM T183 WHERE T183_Id = '$T183_Id'");
            return 'Falha na geração do cabeçalho da remessa....';
        }
        
        $sql = mysql_query("SELECT * FROM T182 WHERE T182_Marcado='S'");
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($mT182 = mysql_fetch_assoc($sql)) {
            $cClausula = " insert into T184 (";
            $cClausula .= " T184_T183_Id,";
            $cClausula .= " T184_T182_Id,";
            $cClausula .= " T184_C007_Id,";
            $cClausula .= " T184_D009_Id,";
            $cClausula .= " T184_Quantidade,";
            $cClausula .= " T184_Preco,";
            $cClausula .= " T184_Tipo,";
            $cClausula .= " T184_Observacao";
            $cClausula .= " ) values ( ";
            $cClausula .= " '{$T183_Id}', ";
            $cClausula .= " '{$mT182['T182_Id']}', ";
            $cClausula .= " '{$mT182['T182_C007_Id']}', ";
            $cClausula .= " '{$mT182['T182_D009_Id']}', ";
            $cClausula .= " '{$mT182['T182_Quantidade']}',";
            $cClausula .= " '{$mT182['T182_Preco']}', ";
            $cClausula .= " '{$mT182['T182_Tipo']}', ";
            $cClausula .= " '{$mT182['T182_Observacao']}' ";
            $cClausula .= " ) ";
            mysql_query($cClausula);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Falha na inclusão dos ítens da remessa...";
            }
            mysql_query("UPDATE T182 SET T182_Marcado='N', T182_Status='R' WHERE T182_Id = '{$mT182['T182_Id']}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Falha ao desmarcar os produtos...";
            }
        }
        
        mysql_query("COMMIT");
        
        return true;
    }
    
    /**
     * campoCancelar
     *
     * @param int $T084_Id
     */
    public function campoCancelar($T084_Id)
    {
        
        $cSql_T084 = mysql_query("SELECT * FROM T084 WHERE T084_Id = '$T084_Id'");
        $mT084     = mysql_fetch_array($cSql_T084);
        
        if ($mT084['T084_Flag_Cancelado'] == 'S') {
            return "Este conhecimento já foi cancelado!";
        }
        
        // Verifica se ja existem contas a receber lancadas para esse conhecimento
        $cSql_T015 = mysql_query("SELECT * FROM T015 WHERE  T015_T084_Id='$T084_Id' and T015_Flag_Cancelada!='S'");
        if (mysql_num_rows($cSql_T015) > 0) {
            return "Existem contas a pagar referente a este conhecimento. Cancele-os para depois cancelar este conhecimento!";
        }
        
        // Verifica se n�o existem notas fiscais relacionadas aos conhecimento
        $cSql_T013 = mysql_query("SELECT * FROM T084 LEFT JOIN T085 ON T085_T084_Id = T084_Id LEFT JOIN T013 ON T013_Id = T085_T013_Id WHERE T084_Id = '$T084_Id' AND T013_Flag_Cancelada != 'S'");
        if (mysql_num_rows($cSql_T013) > 0) {
            return "Existem notas fiscais referente a este conhecimento. Cancele as notas fiscais para cancelar este conhecimento!";
        }
        
        if ($mT084['T084_Flag_Fechado'] == 'N') {
            return "Este conhecimento não foi finalizado ainda, utilize a opção excluir";
        }
        
        $retorno  = mysql_query("UPDATE T084 SET T084_Flag_Cancelado = 'S' WHERE T084_Id = '$T084_Id' AND T084_C004_Id='{$g['empresaAtual']}'");
        $retorno1 = mysql_query("UPDATE T015 SET T015_Flag_Cancelada='S', T015_Data_Pagamento=current_date(), T015_Valor_Duplicata=0, T015_Valor_Total=0,T015_Valor_Saldo=0  WHERE T015_T084_Id='$T084_Id'");
        if (($retorno && $retorno2) == 1) {
            return true;
        } else {
            return mysql_error();
        }
    }
}
