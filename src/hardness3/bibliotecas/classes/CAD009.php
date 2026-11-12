<?php
namespace hardness;
definirClasse('CAD009');
class CAD009_
{
    /**
     * excluir
     *
     * @param int $D107
     * @return true
     */
    public function excluir($D107_Id)
    {
        $D107_Id = mysql_real_escape_string($D107_Id);
        $sql     = "DELETE FROM D107 WHERE D107_Id='$D107_Id'";
        mysql_query($sql);
        return true;
    }
    
    /**
     * exclurItem
     *
     * @param int $D107
     * @return true
     */
    public function excluirItem($D108_Id, $D108_D107_Id)
    {
        $sql = "DELETE FROM D108 WHERE D108_Id='$D108_Id' AND D108_D107_Id='$D108_D107_Id'";
        mysql_query($sql);
        return true;
    }
}
