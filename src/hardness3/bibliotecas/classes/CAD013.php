<?php
namespace hardness;
definirClasse('CAD013');
class CAD013_
{
    /**
     * montarEmail
     * @param String $D117_Id
     * @return array
     */
    public function montarEmail($D117_Id)
    {
        $D117_Id = mysql_real_escape_string($D117_Id);
        $sql     = "SELECT * FROM D117 where D117_Id='{$D117_Id}'";
        $D117    = mysql_query($sql);
        $D117    = mysql_fetch_array($D117);
        
        $array['Para']    = $D117['D117_Email'];
        $array['Assunto'] = "";
        $array['Corpo']   = <<<EOT
			Caro {$D117['D117_Nome']},<br />
			<br /><br />
EOT;
        return $array;
    }
}
