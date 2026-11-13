<?php
namespace hardness;

definirClasse('VEN011');
class VEN011_
{
    /**
     * excluir - Excluir uma Manutenção de equipamento
     *
     * @param int $T142
     * return true/string(erro)
     */
    public function excluir($T142_Id)
    {
        $T142_Id = mysql_real_escape_string($T142_Id);
        $sql     = "delete from T142 where T142_Id='$T142_Id'";
        mysql_query($sql);
        return true;
    }
}
