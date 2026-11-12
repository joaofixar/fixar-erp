<?php
namespace hardness;
definirClasse('SRV003');
class SRV003_
{

    /**
     * iniciarServico
     *
     * @param String $T147_Id
     * @param String $D109_Id
     * @param String $Usuario
     * @return String/bool
     */     
    public function iniciarServico($T147_Id, $D109_Id, $Usuario)
    {
        global $g;
        $T147_Id = (isset($T147_Id)) ? $T147_Id : false;
        $D109_Id = (isset($D109_Id)) ? $D109_Id : false;
        $Usuario = (isset($Usuario)) ? $Usuario : false;
        
        if ($D109_Id == "0" || $D109_Id == "") {
            return "É necessário Selecionar o funcionário e salvar as alteraçoes para iniciar o serviço";
        }
        
        $sql    = "SELECT * FROM T147 WHERE T147_D109_Id='{$D109_Id}' AND T147_Status='E'";
        $result = mysql_query($sql);
        
        if (mysql_num_rows($result) >= $g['C031']['numeroServicosFuncionarioExecSimult']) {
            return "<font color='red'><b>Atenção</b></font><br />Já foi atingido o limite máximo de ordens de serviço em execução<br /> 
			simultânea permitido pela empresa.<br />
			Por favor, conclua uma ordem de serviço EM EXECUÇÃO para iniciar outra.";
        }
 
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        $sql    = "SELECT * FROM T147 left join D109 on D109_Id=T147_D109_Id";
        $result = mysql_query($sql);
        
        // while ($todosT147 = mysql_fetch_assoc($result)){
        // if($todosT147['D109_Id'] == $D109_Id && $todosT147['T147_Status']=="E"){
        // return "O funcionário ".$todosT147['D109_Nome'] . " já tem um serviço em execução. Por favor, conclua o serviço atual!";
        // }
        // }
        
        $sql       = "SELECT T147_Status FROM T147 WHERE T147_Id='{$T147_Id}'";
        $result    = mysql_query($sql);
        $linhaT147 = mysql_fetch_assoc($result);
        
        if (empty($T147_Id)) {
            
            return "Não foi possível Iniciar o Serviço. Verifique!";
            
        } elseif ($linhaT147['T147_Status'] == "E") {
            
            return "Este serviço já está em execução. Por favor, selecione outro serviço na fila de pendentes";
            
        } else {
            
            $sql     = "UPDATE T147 SET T147_Status='E', T147_Hora_Inicio=CURRENT_TIMESTAMP(), T147_D109_Id='{$D109_Id}' WHERE T147_Id='{$T147_Id}'";
            $retorno = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
            //$retorno = true;
            if ($retorno) {
                //INSERINDO REGISTRO NO HISTÓRICO
                if ($Usuario == "Gerente") {
                    $sql        = "SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
                    $result     = mysql_query($sql);
                    $aC007      = mysql_fetch_array($result);
                    $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Iniciado (GERENTE)<br />Login: {$aC007['C007_Login']}<br />Nome: {$aC007['C007_Primeiro_Nome']}')
EOT;
                    $sqlRetorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                } else if ($Usuario == "Funcionario") {
                    $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Iniciado')
EOT;
                    $sqlRetorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                //Pegando o registro recém alterado para imprimir a hora gravada
                $query           = "SELECT T147_Hora_Inicio FROM T147 WHERE T147_Id='{$T147_Id}'";
                $lastInsert      = mysql_query($query);
                $linhaLastInsert = mysql_fetch_assoc($lastInsert);
                $hora            = gCorrigeData($linhaLastInsert['T147_Hora_Inicio']);
                
                // Lista Serviço
                $query        = <<<EOT
					SELECT 
						T147_Titulo, D024_Nome_Empresa, T147_Tempo_Estimado
					FROM
						T147
					left join T005 on T005_Id=T147_T005_Id
					left join D024 on T005_D024_Id=D024_Id
					WHERE T147_Id = '{$T147_Id}'
EOT;
                $servico      = mysql_query($query);
                $linhaServico = mysql_fetch_assoc($servico);
                
                //Processos
                $query     = <<<EOT
				SELECT * FROM T148 WHERE T148_T147_Id='{$T147_Id}'
EOT;
                $processos = mysql_query($query);
                
                //Materiais
                $query = <<<EOT
				SELECT * FROM T149
				LEFT JOIN D009 ON D009_Id=T149_D009_Id
				LEFT JOIN D001 ON D001_Id=D009_D001_Id
				WHERE T149_T147_Id='{$T147_Id}'
EOT;
                
                $materiais = mysql_query($query);
                
                //$query = 
                $retorno = <<<EOT
DETALHES DO SERVIÇO
Serviço: {$linhaServico['T147_Titulo']}
Cliente: {$linhaServico['D024_Nome_Empresa']}
Tempo Estimado: {$linhaServico['T147_Tempo_Estimado']}
Hora Início:\n{$hora}


PROCESSOS\n
EOT;
                while ($linhaProcessos = mysql_fetch_assoc($processos)) {
                    $retorno .= <<<EOT
{$linhaProcessos['T148_Titulo']}
 - {$linhaProcessos['T148_Descricao']}\n
EOT;
                }
                $retorno .= <<<EOT


MATERIAIS

EOT;
                while ($linhaMateriais = mysql_fetch_assoc($materiais)) {
                    $retorno .= <<<EOT
{$linhaMateriais['T149_Quantidade']} - {$linhaMateriais['D001_Descricao_Produto']}\n
EOT;
                }
                
                //echo $this->formataPrint($retorno);
                //die();
                
                //****** LINHAS COMENTADA POR ERRO NO SERVIDOR LOCAL. DESCOMENTAR ELAS
                
                // $retorno = $this->formataPrint($retorno);
                // $retorno .= "\n.: HARDNESS SISTEMAS :.\n\n";
                // $h = fopen('/tmp/print.txt', 'w');
                // fwrite($h, $this->formataPrint($retorno));
                // fclose($h);
                // shell_exec('lpr -Pepson /tmp/print.txt');
                mysql_query("COMMIT");
                return true;
            }
        }
    }

    /**
     * imprimirServico
     *
     * @param String $T147_Id
     * @return bool
     */     
    public function imprimirServico($T147_Id)
    {
        $T147_Id = (isset($T147_Id)) ? $T147_Id : false;
        //$D109_Id = (isset($D109_Id)) ? $D109_Id : false;
        
        //Pegando o registro recém alterado para imprimir a hora gravada
        $query           = "SELECT T147_Hora_Inicio FROM T147 WHERE T147_Id='{$T147_Id}'";
        $lastInsert      = mysql_query($query);
        $linhaLastInsert = mysql_fetch_assoc($lastInsert);
        $hora            = gCorrigeData($linhaLastInsert['T147_Hora_Inicio']);
        
        // Lista Serviço
        $query        = <<<EOT
					SELECT 
						T147_Titulo, D024_Nome_Empresa, T147_Tempo_Estimado
					FROM
						T147
					left join T005 on T005_Id=T147_T005_Id
					left join D024 on T005_D024_Id=D024_Id
					WHERE T147_Id = '{$T147_Id}'
EOT;
        $servico      = mysql_query($query);
        $linhaServico = mysql_fetch_assoc($servico);
        
        //Processos
        $query     = <<<EOT
				SELECT * FROM T148 WHERE T148_T147_Id='{$T147_Id}'
EOT;
        $processos = mysql_query($query);
        
        //Materiais
        $query = <<<EOT
				SELECT * FROM T149
				LEFT JOIN D009 ON D009_Id=T149_D009_Id
				LEFT JOIN D049 on D049_Id=D009_D049_Id
				LEFT JOIN D001 on D001_Id=D049_D001_Id	
				WHERE T149_T147_Id='{$T147_Id}'
EOT;
        
        $materiais = mysql_query($query);
        
        //$query = 
        $retorno = <<<EOT
DETALHES DO SERVIÇO
Serviço: {$linhaServico['T147_Titulo']}
Cliente: {$linhaServico['D024_Nome_Empresa']}
Tempo Estimado: {$linhaServico['T147_Tempo_Estimado']}
Hora Início:\n{$hora}


PROCESSOS\n
EOT;
        while ($linhaProcessos = mysql_fetch_assoc($processos)) {
            $retorno .= <<<EOT
{$linhaProcessos['T148_Titulo']}
 - {$linhaProcessos['T148_Descricao']}\n
EOT;
        }
        $retorno .= <<<EOT


MATERIAIS

EOT;
        while ($linhaMateriais = mysql_fetch_assoc($materiais)) {
            $retorno .= <<<EOT
{$linhaMateriais['T149_Quantidade']} - {$linhaMateriais['D001_Descricao_Produto']}\n
EOT;
        }
        
        echo $this->formataPrint($retorno);
        die();
        $retorno = $this->formataPrint($retorno);
        $retorno .= "\n.: HARDNESS SISTEMAS :.\n\n";
        $h = fopen('/tmp/print.txt', 'w');
        fwrite($h, $this->formataPrint($retorno));
        fclose($h);
        shell_exec('lpr -Pepson /tmp/print.txt');
        return true;
    }

    /**
     * formataPrint
     *
     * @param String $txt
     * @return String
     */     
    public function formataPrint($txt)
    {
        $txt     = utf8_decode($txt);
        $retorno = '';
        $txt     = explode("\n", $txt);
        foreach ($txt as $linha) {
            $linha = trim($linha);
            if (strlen($linha) > 30) {
                while (strlen($linha) > 30) {
                    $retorno .= substr($linha, 0, 30) . "\n";
                    $linha = trim(substr($linha, 30));
                }
            }
            $retorno .= $linha . "\n";
        }
        $retorno = iconv("ISO-8859-1", "ascii//TRANSLIT", $retorno);
        return $retorno;
    }
    
    /**
     * pausarServico
     *
     * @param String $T147_Id
     * @param String $D109_Id
     * @param String $Usuario
     * @return String/bool
     */     
    public function pausarServico($T147_Id, $D109_Id, $Usuario)
    {
        global $g;
        $T147_Id = (isset($T147_Id)) ? $T147_Id : false;
        $D109_Id = (isset($D109_Id)) ? $D109_Id : false;
        $Usuario = (isset($Usuario)) ? $Usuario : false;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        $sql    = "UPDATE T147 SET T147_Hora_Pausa=CURRENT_TIMESTAMP(), T147_Pausado='S' WHERE T147_Id='{$T147_Id}'";
        $result = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . mysql_error();
            
        } else {
            
            //INSERINDO REGISTRO NO HISTÓRICO
            if ($Usuario == "Gerente") {
                $sql        = "SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
                $result     = mysql_query($sql);
                $aC007      = mysql_fetch_array($result);
                $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Pausado (GERENTE)<br />Login: {$aC007['C007_Login']}<br />Nome: {$aC007['C007_Primeiro_Nome']}')
EOT;
                $sqlRetorno = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
            } else if ($Usuario == "Funcionario") {
                $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Pausado')
EOT;
                $sqlRetorno = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
            }
            mysql_query("COMMIT");
            return true;
        }   
    }
    
    /**
     * cancelarServicoGeral
     *
     * @param String $T147_Id
     * @return String/bool
     */     
    public function cancelarServicoGeral($T147_Id)
    {
        global $g;
        $T147_Id = (isset($T147_Id)) ? $T147_Id : false;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $sql    = "UPDATE T147 SET T147_Flag_Cancelado='S', T147_Status='N' WHERE T147_Id='{$T147_Id}'";
        $result = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . mysql_error();
        } else {
            $sql        = "SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
            $result     = mysql_query($sql);
            $aC007      = mysql_fetch_array($result);
            $sql        = <<<EOT
					INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
					VALUES
					('', '', $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Caneclado (GERENTE)<br />Login: {$aC007['C007_Login']}<br />Nome: {$aC007['C007_Primeiro_Nome']}')
EOT;
            $sqlRetorno = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
        }
        
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * concluirServico
     *
     * @param String $T147_Id
     * @param String $D109_Id
     * @param String $Usuario
     * @return String/bool
     */     
    public function concluirServico($T147_Id, $D109_Id, $Usuario)
    {
        global $g;
        $T147_Id = (isset($T147_Id)) ? $T147_Id : false;
        $D109_Id = (isset($D109_Id)) ? $D109_Id : false;
        $Usuario = (isset($Usuario)) ? $Usuario : false;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        $sql    = "UPDATE T147 SET T147_Status='C', T147_Hora_Fim=CURRENT_TIMESTAMP(), T147_Pausado='N' WHERE T147_Id='{$T147_Id}'";
        $result = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . mysql_error();
            
        } else {
            
            //INSERINDO REGISTRO NO HISTÓRICO
            if ($Usuario == "Gerente") {
                $sql        = "SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
                $result     = mysql_query($sql);
                $aC007      = mysql_fetch_array($result);
                $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Concluído (GERENTE)<br />Login: {$aC007['C007_Login']}<br />Nome: {$aC007['C007_Primeiro_Nome']}')
EOT;
                $sqlRetorno = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
            } else if ($Usuario == "Funcionario") {
                $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Concluído')
EOT;
                $sqlRetorno = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
            }
            mysql_query("COMMIT");
            return true;
        }
    }
    
    /**
     * cancelarServico
     *
     * @param String $T147_Id
     * @param String $D109_Id
     * @param String $Usuario
     * @return String/bool
     */     
    public function cancelarServico($T147_Id, $D109_Id, $Usuario)
    {
        $T147_Id = (isset($T147_Id)) ? $T147_Id : false;
        $D109_Id = (isset($D109_Id)) ? $D109_Id : false;
        $Usuario = (isset($Usuario)) ? $Usuario : false;
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        $sql    = "UPDATE T147 SET T147_Status='P', T147_Hora_Inicio='0000-00-00 00:00:00', T147_D109_Id=NULL, T147_Pausado='N', T147_Hora_Reinicio=NULL, T147_Hora_Pausa=NULL, T147_Hora_Fim=NULL WHERE T147_Id='{$T147_Id}'";
        $result = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . mysql_error();
        } else {
            //INSERINDO REGISTRO NO HISTÓRICO
            if ($Usuario == "Gerente") {
                $sql        = "SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
                $result     = mysql_query($sql);
                $aC007      = mysql_fetch_array($result);
                $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Execução Cancelada (GERENTE)<br />Login: {$aC007['C007_Login']}<br />Nome: {$aC007['C007_Primeiro_Nome']}')
EOT;
                $sqlRetorno = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
            } else if ($Usuario == "Funcionario") {
                $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Execução Cancelada')
EOT;
                $sqlRetorno = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
            }
            mysql_query("COMMIT");
            return true;
        }
        
    }
    
    /**
     * reiniciarServico
     *
     * @param String $T147_Id
     * @param String $D109_Id
     * @param String $Usuario
     * @return String/bool
     */    
    public function reiniciarServico($T147_Id, $D109_Id, $Usuario)
    {
        global $g;
        $T147_Id = (isset($T147_Id)) ? $T147_Id : false;
        $D109_Id = (isset($T147_Id)) ? $D109_Id : false;
        $Usuario = (isset($Usuario)) ? $Usuario : false;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        $sql    = "UPDATE T147 SET T147_Hora_Reinicio=CURRENT_TIMESTAMP(), T147_Pausado='N' WHERE T147_Id='{$T147_Id}'";
        $result = mysql_query($sql);
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return 'Erro: ' . mysql_error();
        } else {
            //INSERINDO REGISTRO NO HISTÓRICO
            if ($Usuario == "Gerente") {
                $sql        = "SELECT C007_Login, C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
                $result     = mysql_query($sql);
                $aC007      = mysql_fetch_array($result);
                $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Reiniciado (GERENTE)<br />Login: {$aC007['C007_Login']}<br />Nome: {$aC007['C007_Primeiro_Nome']}')
EOT;
                $sqlRetorno = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
            } else if ($Usuario == "Funcionario") {
                $sql        = <<<EOT
							INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao)
							VALUES
							('', $D109_Id, $T147_Id, CURRENT_TIMESTAMP(), 'Serviço Reiniciado')
EOT;
                $sqlRetorno = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
            }
            mysql_query("COMMIT");
            return true;
        }
    }
    
    /**
     * aprovarServico
     *
     * @param String $T147_Id
     * @param String $D109_Id
     * @param String $Usuario
     * @return String/bool
     */     
    public function aprovarServico($T147_Id, $D109_Id, $Usuario)
    {
        global $g;
        $sql = "UPDATE T147 SET T147_Flag_Conferido='S' WHERE T147_Id='{$T147_Id}'";
        mysql_query($sql);
        if (mysql_error())
            return mysql_error();
        
        $retorno      = mysql_query("SELECT * FROM T147 WHERE T147_Id='$T147_Id'");
        $ret          = mysql_fetch_array($retorno);
        $T147_D109_Id = $ret['T147_D109_Id'];
        
        $result   = mysql_query("SELECT * FROM D109 WHERE D109_Id='{$T147_D109_Id}'");
        $aUsuario = mysql_fetch_array($result);
        
        $sql    = <<<EOT
			INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao, T169_Observacao)
			VALUES
			('', '$T147_D109_Id', '$T147_Id', CURRENT_TIMESTAMP(), 'Serviço Aprovado <br />
																	Pelo Funcionário: {$aUsuario['D109_Nome']}', '');
EOT;
        $result = mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        }
        return true;
    }
    
    /**
     * reprovarServico
     *
     * @param String $T147_Id
     * @param String $D109_Id
     * @param String $Usuario
     * @return String/bool
     */     
    public function reprovarServico($T147_Id, $D109_Id, $Usuario)
    {
        $sql = "UPDATE T147 SET T147_Flag_Conferido='R', T147_Hora_Inicio='',  T147_Prioridade='1', T147_Status='P' WHERE T147_Id='{$T147_Id}'";
        mysql_query($sql);
        if (mysql_error())
            return mysql_error();
        
        $retorno      = mysql_query("SELECT * FROM T147 WHERE T147_Id='$T147_Id'");
        $ret          = mysql_fetch_array($retorno);
        $T147_D109_Id = $ret['T147_D109_Id'];
        
        $result   = mysql_query("SELECT * FROM D109 WHERE D109_Id='{$T147_D109_Id}'");
        $aUsuario = mysql_fetch_array($result);
        
        $sql = <<<EOT
			INSERT INTO T169 (T169_Id, T169_D109_Id, T169_T147_Id, T169_Data, T169_Descricao, T169_Observacao)
			VALUES
			('', '$T147_D109_Id', '$T147_Id', CURRENT_TIMESTAMP(), 'Serviço Reprovado <br />
																	Pelo Funcionário: {$aUsuario['D109_Nome']}', '');
EOT;
        
        $result = mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        }
        return true;
    }
}
