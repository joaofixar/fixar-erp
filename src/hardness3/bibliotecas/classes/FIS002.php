<?php
namespace hardness;
definirClasse('FIS002');
class FIS002_
{

    /**
     * verificaCampoEstados
     *
     * @param Form $form
     * @return bool
     */	
    public function verificaCampoEstados($form)
    {   
        $campoEstados = trim($form->campoValorEnviado('D143_Estados_Especificos'));
        if ($campoEstados == "") {
            return true;
        }
        
        $estados = explode(",", $campoEstados);
        foreach ($estados as $estado) {
            $ret     = mysql_query("SELECT COUNT(*) FROM D018 WHERE D018_UF IN (SELECT D018_UF FROM D018 WHERE D018_UF='{$estado}')");
            $retorno = mysql_fetch_array($ret);
            if ($retorno['COUNT(*)'] == 0) {
                return false;
            }
        }
        return true;   
    }  
}
