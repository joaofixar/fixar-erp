<?php
namespace hardness;
definirClasse('CAD011');
class CAD011_
{
    /**
     * excluir
     *
     * @param int $D110
     * @return true
     */
    public function excluir($D110_Id)
    {
        $D110_Id = mysql_real_escape_string($D110_Id);
        $sql     = "DELETE FROM D110 WHERE D110_Id='$D110_Id'";
        mysql_query($sql);
        return true;
    }
}

?>