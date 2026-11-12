<?php
namespace hardness;
definirClasse('FIS001');
class FIS001_
{

    /**
     * verificaCampoEstados
     *
     * @param Form $form
     * @return bool
     */	
    public function verificaCampoEstados($form)
    {
        $campoEstados = trim($form->campoValorEnviado('D137_Estados_Especificos'));
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

    /**
     * duplicar_fis001form01
     *
     * @param Form $form
     * @return bool
     */	
    public function duplicar_fis001form01($divId, $divIdRoot, $tabela, $D006_Id_Antigo)
    {
        global $g;
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // Selecionando o pedido
        $D006  = mysql_query("SELECT * FROM D006 WHERE D006_Id='{$D006_Id_Antigo}'");
        $mD006 = mysql_fetch_assoc($D006);

        if (mysql_num_rows($D006) == 0) {
            $retorno = <<<EOT
            var \$buttons = {
              "Fechar": function() { $( this).dialog("destroy").remove(); }
            };
            dialogConfirm("Erro", "Erro interno, CFOP não encontrada", \$buttons);
EOT;
            return $retorno;
        }
        
        $campos  = "";
        $valores = "";
        foreach ($mD006 as $key => $value) {
            if ($key == 'D006_Id') {
                continue;
            }else{
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }

        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insD006 = "INSERT INTO D006 ({$campos}) VALUES ({$valores})";
        $resD006 = mysql_query($insD006);
        $D006_Id = $g['mysqlLastId'];
         
        if (!$resD006) {
            mysql_query("ROLLBACK");
            $erro =  mysql_error();
             return "alert('Erro: {$erro}');";
        }

        mysql_query("COMMIT");
        $novoId = uniqid();

        return "$('#{$divId}').closest('.ui-dialog-content').dialog('close'); divRefresh('{$g['divId']}'); abrirJanela(false, '{$divIdRoot}', '{$divId}' ,'{$novoId}', '', 'Nova CFOP', '/fis/fis001/content/fis001content02/', '&acaoId=' + encodeURIComponent('{$D006_Id}') + '&tabela=D006', [890,605]);";
        
    }
}
