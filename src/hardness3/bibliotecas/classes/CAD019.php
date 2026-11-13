<?php
namespace hardness;
definirClasse('CAD019');
class CAD019_
{

    /**
     * updateCheckboxImprimir
     *
     * @param String $D004_Id
     * @param String $selected
     * @return true
     */ 
    public function updateCheckboxImprimir($D004_Id, $selected)
    {
        global $g;
        
        $D004_Id  = mysql_real_escape_string($D004_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("UPDATE D004 SET D004_Flag_Imprimir_Etiqueta='$selected' WHERE D004_Id='$D004_Id'");
        return true;
    }
}
