<?php
namespace hardness;

definirClasse('VEN009');
class VEN009_
{    
    /**
     * excluir - Excluir um item do grid de logistica de aluguel
     * 
     * @param int $T143_Id
     * return true/string(erro)
     */
    public function excluir($T143_Id)
    {
        global $g;
        
        $T143_Id = mysql_real_escape_string($T143_Id);
        mysql_query("delete from T143 where T143_Id='$T143_Id'");
        return true;
    }
}
