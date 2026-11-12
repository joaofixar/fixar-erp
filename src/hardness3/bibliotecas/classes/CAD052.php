<?php
namespace hardness;
definirClasse('CAD052');
class CAD052_
{
    public function engenhariaLiberada($D074_Id){
        return true;
    }

    public function engenhariaLiberadaIncluir($D001_Id){
        return true;
    }

    public function engenhariaLiberadaProcesso($D075_Id){
        return true;
    }
    public function engenhariaLiberadaProcessoIncluir($D001_Id){
        return true;
    }

    public function engenhariaDuplicada($form_tabela, $form){
        return true;
    }

  
    public function incluirEngenharia($form_tabela, $form){
        global $g;
        if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
            // Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
            $alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
            echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    // Fecha a janela, recarrega o grid e abre a alteração
                    $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                        setTimeout(function() {
                            {$alert}
                        }, 150);
                    });
                    $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                    divRefresh('{$g['divIdParent']}', true);
                } </script></div>
EOT;
        }
    }
    
    /**
     * excluirItem
     *
     * @param String $D074_Id
     * @return true
     */     
    public function excluirItem($D074_Id)
    {
       $validacao = $this->engenhariaLiberada($D074_Id);
       if ($validacao === true) {
            $D074_Id = mysql_real_escape_string($D074_Id);
            $sql     = "DELETE FROM D074 WHERE D074_Id='$D074_Id'";
            mysql_query($sql);
            return true;
       } else {
            return $validacao;
       }
    }
    
    /**
     * alterarProduto
     *
     * @param Form $form
     * @return true
     */     
    public function alterarProduto($form)
    {
        global $g;
        
        $D001_Id                        = $form->campoValorEnviado('D001_Id');
        $D001_Lote_Economico            = $form->campoValorEnviado('D001_Lote_Economico');
        $D009_Valor_Custo_Unitario      = $form->campoValorEnviado('D009_Valor_Custo_Unitario');
        $D037_Unidade                   = $form->campoValorEnviado('D037_Unidade');
        $D001A_Flag_Engenharia_Liberada = $form->campoValorEnviado('D001A_Flag_Engenharia_Liberada');
        
        
        $D009_Id = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D001_Id = '{$D001_Id}'");
        $D009_Id = mysql_fetch_array($D009_Id);
        $D009_Id = $D009_Id[0];
        
        $D037_Id = mysql_query("SELECT D001_D037_Id FROM D001 WHERE D001_Id = '{$D001_Id}'");
        $D037_Id = mysql_fetch_array($D037_Id);
        $D037_Id = $D037_Id[0];

        $D001A_Id = mysql_query("SELECT D001A_Id FROM D001A WHERE D001A_D001_Id = '{$D001_Id}'");
        $D001A_Id = mysql_fetch_array($D001A_Id);
        $D001A_Id = $D001A_Id[0];
        
        $sql = <<<EOT
			UPDATE D001 
				SET D001_Lote_Economico = '{$D001_Lote_Economico}'
				WHERE D001_Id = '{$D001_Id}'
EOT;
        mysql_query($sql);
        
        $sql = <<<EOT
			UPDATE D037 
				SET D037_Unidade = '{$D037_Unidade}'
				WHERE D037_Id = '{$D037_Id}'
EOT;
        mysql_query($sql);
        
        $sql = <<<EOT
			UPDATE D009 
				SET D009_Valor_Custo_Unitario = '{$D009_Valor_Custo_Unitario}'
				WHERE D009_Id = '{$D009_Id}'
EOT;
        mysql_query($sql);

        $sql = <<<EOT
			UPDATE D001A 
				SET D001A_Flag_Engenharia_Liberada = '{$D001A_Flag_Engenharia_Liberada}'
				WHERE D001A_Id = '{$D001A_Id}'
EOT;
        mysql_query($sql);        
        return true;
    }
    
    /**
     * excluirProcesso
     *
     * @param String $D075_Id
     * @return true
     */     
    public function excluirProcesso($D075_Id)
    {
        $validacao = $this->engenhariaLiberadaProcesso($D075_Id);
        if ($validacao === true) {
            $D075_Id = mysql_real_escape_string($D075_Id);
            $sql     = "DELETE FROM D075 WHERE D075_Id='$D075_Id'";
            mysql_query($sql);
            return true;
        } else {
             return $validacao;
        }
    }
}
