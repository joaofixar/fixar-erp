<?php
namespace hardness;
definirClasse('CAD020');
class CAD020_
{

    /**
     * updateCheckboxImprimir
     *
     * @param String $D009_Id
     * @param String $selected
     * @return true
     */   
    public function updateCheckboxImprimir($D009_Id, $selected)
    {
        global $g;
        
        $D009_Id  = mysql_real_escape_string($D009_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("UPDATE D009 SET D009_Flag_Imprimir_Etiqueta='$selected' WHERE D009_Id='$D009_Id'");
        return true;
    }

    /**
     * atualizarMarca
     *
     * @param Form $form
     * @param String $D001_Id
     * @return true
     */ 
    function atualizarMarca($form, $D001_Id)
    {
        global $g;
        $D049_Id                           = $form->campoValorEnviado('D049_Id');
        $D049_D082_Id                      = $form->campoValorEnviado('D049_D082_Id');
        $D049_Flag_Tipo                    = $form->campoValorEnviado('D049_Flag_Tipo');
        $D049_Origem_Mercadoria            = $form->campoValorEnviado('D049_Origem_Mercadoria');
        $D049_Data_Cadastro                = gCorrigeDataInverte($form->campoValorEnviado('D049_Data_Cadastro'));
        
        $sql = <<<EOT
                    UPDATE D049 SET D049_D082_Id = '{$D049_D082_Id}',
                                D049_Flag_Tipo = '{$D049_Flag_Tipo}',
                                D049_Data_Cadastro = '{$D049_Data_Cadastro}',
                                D049_Origem_Mercadoria='{$D049_Origem_Mercadoria}'
                          WHERE D049_D001_Id = '{$D001_Id}'
EOT;
        mysql_query($sql);
        log($sql);
        
        return true;
        
    }


}
