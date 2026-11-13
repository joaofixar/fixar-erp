<?php
namespace hardness;
definirClasse('SRV002');
class SRV002_
{

    /**
     * controleServico
     *
     * @param Form $form
     * @return String/bool
     */ 	
    public function controleServico($form)
    {
        global $g;
        $T147_Id             = $form->campoValorEnviado('acaoId');
        $T147_D109_Id        = $form->campoValorEnviado('T147_D109_Id');
        $T147_Tempo_Estimado = $form->campoValorEnviado('T147_Tempo_Estimado');
        $T147_Tempo_Estimado = $T147_Tempo_Estimado . ":00";
        
        if ($T147_Id == '') {
            return "Não foi recebido o T147_Id. Por favor, contate o suporte técnico";
        } elseif ($T147_D109_Id == '') {
            return "Não foi recebido o T147_D109_Id. Por favor, contate o suporte técnico";
        } elseif ($T147_Tempo_Estimado == '') {
            return "Não foi recebido o T147_Tempo_Estimado. Por favor, contate o suporte técnico";
        }
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        $sql    = <<<EOT
			SELECT * FROM T147 left join D109 on D109_Id=T147_D109_Id WHERE T147_Id='$T147_Id'
EOT;
        $result = mysql_query($sql);
        $aT147  = mysql_fetch_array($result);
        
        $sql    = <<<EOT
			SELECT * FROM D109 WHERE D109_Id='$T147_D109_Id'
EOT;
        $result = mysql_query($sql);
        $aD109  = mysql_fetch_array($result);
        
        $sql    = <<<EOT
			UPDATE T147 SET T147_D109_Id='{$T147_D109_Id}', T147_Tempo_Estimado='{$T147_Tempo_Estimado}' WHERE T147_Id='{$T147_Id}'
EOT;
        $result = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return mysql_error();
        } else {
            $sql      = <<<EOT
				SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'
EOT;
            $result   = mysql_query($sql);
            $aUsuario = mysql_fetch_array($result);
            
            if ($aT147['T147_Tempo_Estimado'] != $T147_Tempo_Estimado) {
                
                $sql    = <<<EOT
					INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao, T169_Observacao)
					VALUES
					('', '$T147_D109_Id', '$T147_Id', CURRENT_TIMESTAMP(), 'Alterado Tempo Estimado <br />
																			De: {$aT147['T147_Tempo_Estimado']}<br />
																			Para: {$T147_Tempo_Estimado}<br />
																			Pelo Usuário: {$aUsuario['C007_Primeiro_Nome']}<br />
																			Login: {$aUsuario['C007_Login']}', '')
EOT;
                $result = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return mysql_error();
                }
            }
            
            if ($aT147['T147_D109_Id'] != $T147_D109_Id) {
                // $sql = <<<EOT
                // INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao, T169_Observacao)
                // VALUES
                // ('', '{$aT147['T147_D109_Id']}', '$T147_Id', CURRENT_TIMESTAMP(), 'Serviço Retirado do funcionário <br />
                // Para: {$aD109['D109_Nome']}<br />
                // Pelo Usuário: {$aUsuario['C007_Primeiro_Nome']}<br />
                // Login: {$aUsuario['C007_Login']}', '')
                // EOT;
                // $result = mysql_query($sql);
                // if(mysql_error()){
                // mysql_query("ROLLBACK");
                // return mysql_error();
                
                
                $sql    = <<<EOT
					INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao, T169_Observacao)
					VALUES
					('', '$T147_D109_Id', '$T147_Id', CURRENT_TIMESTAMP(), 'Alterado Funcionário Responsável <br />
																			De: {$aT147['D109_Nome']}<br />
																			Para: {$aD109['D109_Nome']}<br />
																			Pelo Usuário: {$aUsuario['C007_Primeiro_Nome']}<br />
																			Login: {$aUsuario['C007_Login']}', '')
EOT;
                $result = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return mysql_error();
                }
            }
            mysql_query("COMMIT");
            return true;
        }
    }
    
    /**
     * srv002atualizarPrioridade
     *
     * @param String $T147_Id
     * @param String $campoEditado
     * @param String $valorAntigo
     * @param String $novoValor
     * @return String/bool
     */     
    public function srv002atualizarPrioridade($T147_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        //return $T147_Id."<br />Campo Editado: ".$campoEditado."<br /> Valor Antigo: ".$valorAntigo."<br />Novo Valor: ".$novoValor;
        
        if (($novoValor > 0) && ($novoValor < 10000)) {
            $novoValor = $novoValor;
        } else {
            $novoValor = "99999";
        }
        
        $sql    = <<<EOT
			UPDATE T147 SET T147_Prioridade='{$novoValor}' WHERE T147_Id='{$T147_Id}'
EOT;
        $result = mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        } else {
            return true;
        }
    }
    
    /**
     * insereServico
     *
     * @param Form $form
     * @param String $T147_Id
     * @return String/bool
     */     
    public function insereServico($form, $T147_Id)
    {
        $D112_Id = $form->campoValorEnviado('T147_D112_Id');
        $sql     = "SELECT * FROM D113 WHERE D113_D112_Id='{$D112_Id}'";
        $array   = mysql_query($sql);
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($row = mysql_fetch_assoc($array)) {
            // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
            $sql = "INSERT INTO T148 (T148_T147_Id, T148_Titulo, T148_Descricao) VALUES ('" . mysql_real_escape_string($T147_Id) . "', '"
            //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return mysql_error();
            }
        }
        
        $sql   = "SELECT * FROM D114 WHERE D114_D112_Id='{$D112_Id}'";
        $array = mysql_query($sql);
        
        while ($row = mysql_fetch_assoc($array)) {
            $sql = "INSERT INTO T149 (T149_D037_Id, T149_D009_Id, T149_T147_Id, T149_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T147_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
            mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return mysql_error();
            }
        }
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * verificaServicoNovo
     *
     * @param String $T147_Id
     */     
    public function verificaServicoNovo($T147_Id)
    {
        if (!empty($T147_Id)) {
            $sql = "UPDATE T147 SET T147_Flag_Novo='N' WHERE T147_Id='{$T147_Id}'";
            mysql_query($sql);
        }   
    }
    
    /**
     * marcarConferido
     *
     * @param String $T147_Id
     * @return String/bool
     */     
    public function marcarConferido($T147_Id)
    {
        global $g;
        $sql = "UPDATE T147 SET T147_Flag_Conferido='S' WHERE T147_Id='{$T147_Id}'";
        mysql_query($sql);
        if (mysql_error())
            return mysql_error();
        
        $retorno      = mysql_query("SELECT * FROM T147 WHERE T147_Id='$T147_Id'");
        $ret          = mysql_fetch_array($retorno);
        $T147_D109_Id = $ret['T147_D109_Id'];
        
        $result   = mysql_query("SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $aUsuario = mysql_fetch_array($result);
        
        $sql    = <<<EOT
			INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao, T169_Observacao)
			VALUES
			('', '$T147_D109_Id', '$T147_Id', CURRENT_TIMESTAMP(), 'Serviço Aprovado <br />
																	Pelo Usuário: {$aUsuario['C007_Primeiro_Nome']}<br />
																	Login: {$aUsuario['C007_Login']}', '')
EOT;
        $result = mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        }
        return true;
    }
    
    /**
     * marcarReprovado
     *
     * @param String $T147_Id
     * @return bool
     */     
    public function marcarReprovado($T147_Id)
    {
        global $g;
        $sql = "UPDATE T147 SET T147_Flag_Conferido='R', T147_Hora_Inicio='', T147_Prioridade='1', T147_Status='P' WHERE T147_Id='{$T147_Id}'";
        mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        }
        
        $retorno      = mysql_query("SELECT * FROM T147 WHERE T147_Id='$T147_Id'");
        $ret          = mysql_fetch_array($retorno);
        $T147_D109_Id = $ret['T147_D109_Id'];
        
        $result   = mysql_query("SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $aUsuario = mysql_fetch_array($result);
        
        $sql    = <<<EOT
			INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao, T169_Observacao)
			VALUES
			('', '$T147_D109_Id', '$T147_Id', CURRENT_TIMESTAMP(), 'Serviço Reprovado <br />
																	Pelo Usuário: {$aUsuario['C007_Primeiro_Nome']}<br />
																	Login: {$aUsuario['C007_Login']}', '')
EOT;
        $result = mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        } 
        return true;
    }
}
