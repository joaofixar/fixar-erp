<?php
namespace hardness;

definirClasse('EST010');
class EST010_
{
    /**
     * updateCheckboxGerarCotar
     *
     * @param int $T006_Id
     * @param string $selected
     */
    public function updateCheckboxGerarCotar($T006_Id, $selected)
    {
        global $g;
        
        $T006_Id  = mysql_real_escape_string($T006_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("update T006 set T006_Flag_Cotar='$selected' where T006_Id='$T006_Id'");
        return true;
    }
    
    /**
     * updateCheckboxGerarComprar
     *
     * @param int $T006_Id
     * @param string $selected
     */
    public function updateCheckboxGerarComprar($T006_Id, $selected)
    {
        global $g;
        
        $T006_Id  = mysql_real_escape_string($T006_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("update T006 set T006_Flag_Comprar='$selected' where T006_Id='$T006_Id'");
        return true;
    }
}
