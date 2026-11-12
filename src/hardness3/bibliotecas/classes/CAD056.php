<?php
namespace hardness;
definirClasse('CAD056');
class CAD056_
{

    /**
     * verificarCodigoBanco
     *
     * @param String $D062_Numero_Banco
     * @param String $D062_Id
     * @return bool
     */ 	
    public function verificarCodigoBanco($D062_Numero_Banco, $D062_Id)
    {
        $D062 = mysql_query("SELECT * FROM D062 WHERE D062_Numero_Banco = '{$D062_Numero_Banco}' AND D062_Id != '{$D062_Id}'");
        if (mysql_num_rows($D062) > 0) {
            return false;
        } else {
            return true;
        }
    }
}
