<?php
namespace hardness;
definirClasse('CAD039');
class CAD039_
{

    /**
     * insereRegistrosT160
     *
     * @param String $D106_Id
     * @param String $D124_Id
     * @return bool
     */ 	
    public function insereRegistrosT160($D106_Id, $D124_Id)
    {        
        $sql = "SELECT D106_D128_Id FROM D106 WHERE D106_Id='{$D106_Id}'";
        
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        $tipo      = $row['D106_D128_Id'];
        
        $sql = "SELECT D121_Id FROM D121 WHERE D121_D128_Id='{$tipo}'";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) { 
            $T160  = mysql_query("SELECT T160_Id FROM T160 WHERE T160_D121_Id='{$row['D121_Id']}' AND T160_D106_Id='$D106_Id' AND T160_D124_Id='$D124_Id'");
            $mT160 = mysql_fetch_array($T160);
            
            if (mysql_num_rows($T160) <= 0) {
                mysql_query("INSERT INTO T160 (T160_D121_Id,T160_D106_Id, T160_D124_Id)
			                           VALUES ('{$row['D121_Id']}','$D106_Id','$D124_Id')");
            }
        }
    }
}
