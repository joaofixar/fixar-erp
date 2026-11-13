<?php
namespace hardness;
definirClasse('CAD032');
class CAD032_
{
    
    /**
     * updateC030_Acesso_Liberado
     *
     * @param String $C029_Id
     * @param String $C021_Id
     * @param String $selected
     * @return true
     */     
    public function updateC030_Acesso_Liberado($C029_Id, $C021_Id, $selected)
    {
        global $g;
        
        $C030  = mysql_query("SELECT C030_Id FROM C030 WHERE C030_C029_Id='$C029_Id' AND C030_C021_Id='$C021_Id'");
        $mC030 = mysql_fetch_array($C030);
        
        if ($mC030['C030_Id'] <= 0) {
            mysql_query("INSERT INTO C030 (C030_C029_Id,C030_C021_Id,C030_Id_Perfil,C030_Acesso_Liberado)
		                           VALUES ('$C029_Id','$C021_Id','$C021_Id','S')");
        } else {
            mysql_query("UPDATE C030 SET C030_Acesso_Liberado='$selected'
		                  WHERE C030_Id='{$mC030['C030_Id']}'");
        }
        return true;        
    }
    
    /**
     * updateC028_Acesso_Liberado
     *
     * @param String $C027_Id
     * @param String $C021_Id
     * @param String $selected
     * @return bool
     */     
    public function updateC028_Acesso_Liberado($C027_Id, $C021_Id, $selected)
    {
        global $g;
        
        $C028  = mysql_query("SELECT C028_Id FROM C028 WHERE C028_C027_Id='$C027_Id' AND C028_C021_Id='$C021_Id'");
        $mC028 = mysql_fetch_array($C028);
        
        if ($mC028['C028_Id'] <= 0) {
            mysql_query("INSERT INTO C028 (C028_C027_Id,C028_C021_Id,C028_Id_Perfil,C028_Acesso_Liberado)
		                           VALUES ('$C027_Id','$C021_Id','$C021_Id','S')");
        } else {
            mysql_query("UPDATE C028 SET C028_Acesso_Liberado='$selected'
		                  WHERE C028_Id='{$mC028['C028_Id']}'");
        }
        return true;
    }
}
