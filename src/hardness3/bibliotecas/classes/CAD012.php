<?php
namespace hardness;
definirClasse('CAD012');
class CAD012_
{
    /**
     * excluir
     *
     * @param int $D111
     * @return true
     */
    public function excluir($D111_Id)
    {
        $D111_Id = mysql_real_escape_string($D111_Id);
        $sql     = "DELETE FROM D111 WHERE D111_Id='$D111_Id'";
        mysql_query($sql);
        return true;
    }
}
