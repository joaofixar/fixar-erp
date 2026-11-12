<?php
namespace hardness;
definirClasse('CAD010');
class CAD010_
{
    /**
     * excluir
     *
     * @param int $D109
     * @return true
     */
    public function excluir($D109_Id)
    {
        $D109_Id = mysql_real_escape_string($D109_Id);
        $sql     = "DELETE FROM D109 WHERE D109_Id='$D109_Id'";
        mysql_query($sql);
        return true;
    }
    
    /**
     * excluircompetencia
     *
     * @param String $D115_Id
     * @return true
     */     
    public function excluircompetencia($D115_Id)
    {
        $D115_Id = mysql_real_escape_string($D115_Id);
        $sql     = "DELETE FROM D115 WHERE D115_Id='$D115_Id'";
        mysql_query($sql);
        return true;
    }

    /**
     * atualizaTotalMensal
     *
     * @param Form $form
     */     
    public function atualizaTotalMensal($form)
    {
        $T141_Id      = $form->campoValorEnviado('T158_T141_Id');
        $sql          = "SELECT SUM(T158_Valor_Mensal) FROM T158 WHERE T158_T141_Id='{$T141_Id}'";
        $array        = mysql_query($sql);
        $row          = mysql_fetch_assoc($array);
        $Valor_Mensal = $row['SUM(T158_Valor_Mensal)'];
        
        $sql = "UPDATE T141 SET T141_Valor_Mensal='{$Valor_Mensal}' WHERE T141_Id='{$T141_Id}'";
        mysql_query($sql);
    }

    /**
     * atualizaTotalMensalGrid
     *
     * @param String $acaoId
     */     
    public function atualizaTotalMensalGrid($acaoId)
    {
        $sql          = "SELECT SUM(T158_Valor_Mensal) FROM T158 WHERE T158_T141_Id='{$acaoId}'";
        $array        = mysql_query($sql);
        $row          = mysql_fetch_assoc($array);
        $Valor_Mensal = $row['SUM(T158_Valor_Mensal)'];
        
        $sql = "UPDATE T141 SET T141_Valor_Mensal='{$Valor_Mensal}' WHERE T141_Id='{$acaoId}'";
        mysql_query($sql);
    }
}
