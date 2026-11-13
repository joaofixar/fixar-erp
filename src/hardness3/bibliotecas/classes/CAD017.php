<?php
namespace hardness;
definirClasse('CAD017');
class CAD017_
{

    /**
     * processarOrdemDespesa
     *
     * @param String $D192_Id
     * @return Bool
     */
    public function processarOrdemDespesa($D192_Id, $novoNumeroItem)
    {
        global $g;
        $contadorItem = 1;
        
        $sql = mysql_query("SELECT D192_Ordem, 
                                   D192_Id 
                              FROM D192 
                             WHERE D192_D190_Id = (SELECT D192_D190_Id FROM D192 WHERE D192_Id = '{$D192_Id}') 
                               AND D192_Id != '{$D192_Id}' 
                          ORDER BY D192_Ordem");
        while($row = mysql_fetch_array($sql)){
            if($contadorItem == $novoNumeroItem){ $contadorItem++; }
            mysqli_query("UPDATE D192 SET D192_Ordem = '{$contadorItem}' WHERE D192_Id = '{$row['D192_Id']}'");
            $contadorItem++;
        }
        return true;
    }

}
