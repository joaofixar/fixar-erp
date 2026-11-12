<?php
namespace hardness;
definirClasse('CAD058');
class CAD058_
{
    /**
     * atualizarConfGlobal
     * @param form  $form
     * @param string  $C004_Id
     * @return true/erro(string)
     */
    public function atualizarConfGlobal($form, $C004_Id)
    {
        global $g;
        $C031 = array();
        foreach($form->formArray['campos'] as $grupoC031){
            foreach ($grupoC031 as $varC031 => $valor) {
                $C031[] = $varC031;
            }
        }
        foreach($C031 as $global){
            $campoValor = $form->campoValorEnviado($global);
            if(strpos($global, "alcadaVenda_valor") !== false || strpos($global, "alcadaVenda_desconto") !== false || strpos($global, "alcadaVendaSupervisor_valor") !== false || strpos($global, "alcadaVendaSupervisor_desconto") !== false || strpos($global, "ticketMedioClienteClasse") !== false || strpos($global, "mediaVendaMesClienteClasse") !== false){
                $campoValor = gCorrigeNumeroInverte($campoValor); 
            } else {
                $campoValor = mysql_real_escape_string($campoValor);
            }
            $sqlId = mysql_query("SELECT C031_Id FROM C031 WHERE C031_C004_Id = '{$C004_Id}' AND C031_Campo = '{$global}'");
            $rowId = mysql_fetch_array($sqlId);
            mysql_query("UPDATE C031 SET C031_Valor = '{$campoValor}' WHERE C031_Id = '{$rowId['C031_Id']}'");
        }
        return true;
    }     
}
