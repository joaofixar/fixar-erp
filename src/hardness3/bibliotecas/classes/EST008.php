<?php
namespace hardness;

definirClasse('EST008');
class EST008_
{
    /**
     * insereItem
     *
     * @param Form $form
     * @param int $T059_Id
     */
    public function insereItem($form, $T059_Id)
    {
        $T059_T010_Id = $form->campoValorEnviado('T059_T010_Id');
        
        $cSql = mysql_query("call T059_Gravar_Totalizacao('$T059_Id');");
        $cSql = mysql_query("call T010_Gravar_Totalizacao('$T059_T010_Id');");
        
    }

    /**
     * updateT174_Flag_Gerar_OC
     *
     * @param int $T174_Id
     * @param string $selected
     */
    public function updateT174_Flag_Gerar_OC($T174_Id, $selected)
    {
        global $g;
        
        $T174_Id  = mysql_real_escape_string($T174_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("update T174 set T174_Flag_Gerar_OC='$selected' where T174_Id='$T174_Id'");
        return true;
    }

    /**
     * updateT174_Flag_Indisponivel
     *
     * @param int $T174_Id
     * @param string $selected
     */
    public function updateT174_Flag_Indisponivel($T174_Id, $selected)
    {
        global $g;
        // $retorno = $g['conf']->restricaoTravar('alterar', 'T174_Id', $T174_Id);
        // if($retorno !== true){
        // //return $retorno;
        // echo gGeraAlertMsg('Erro!', $retorno, false);
        // return true;
        // }
        
        $T174_Id  = mysql_real_escape_string($T174_Id);
        $selected = mysql_real_escape_string($selected);
        $sql      = "update T174 set T174_Flag_Indisponivel='$selected' where T174_Id='$T174_Id'";
        mysql_query($sql);
        return true;
    }
    
    /**
     * insereItemEstoque
     *
     * @param int $T010_Id
     */
    public function insereItemEstoque($T010_Id)
    {
        global $g;
        $sql     = <<<EOT
  				select 
  				D001_Codigo_Produto,
  				D001_Descricao_Produto,
  				D009_Quantidade_Comprar_Calculada,
  				D009_Quantidade_Comprar,
  				D009_Id,
  				D041_Valor_Base 
  				from D001 
  				left join D049 on D049_D001_Id=D001_Id
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   			where D009_Flag_Cotar='S'
	   			AND D009_C004_Id={$g['empresaAtual']}
EOT;
        $retorno = mysql_query($sql);
        
        while ($row = mysql_fetch_array($retorno)) {
            
            $QuantidadeComprar = ($row['D009_Quantidade_Comprar'] > 0) ? $row['D009_Quantidade_Comprar'] : $row['D009_Quantidade_Comprar_Calculada'];
            
            $sqlInsert = <<<EOT
				insert into T059
				(T059_T010_Id,
				T059_Codigo_Produto,
				T059_Descricao_Produto,
				T059_Quantidade,
				T059_Valor_Custo_Unitario, 
				T059_D009_Id)
				values 
				('{$T010_Id}',
				'{$row['D001_Codigo_Produto']}',
				'{$row['D001_Descricao_Produto']}',
				'{$QuantidadeComprar}',
				'{$row['D041_Valor_Base']}',
				'{$row['D009_Id']}'
				);
EOT;
            
            mysql_query($sqlInsert);
            
            $cSql = mysql_query("call T059_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T010_Id}');");
        }
    }
    
    /**
     * insereItemEstoqueRequisicao
     *
     * @param int $T010_Id
     */
    public function insereItemEstoqueRequisicao($T010_Id)
    {
        global $g;
        $sql     = <<<EOT
  				select 
  				D001_Codigo_Produto,
  				D001_Descricao_Produto,
  				D009_Quantidade_Comprar_Calculada,
  				D009_Id,
  				T189_Quantidade,
  				T189_Flag_Cotar,
  				T189_Id,
  				D041_Valor_Base,
  				T189_Descricao_Produto,
  				T189_D024_Id
  				from T189
	   			left join D009 on D009_Id=T189_D009_Id
  				left join D049 on D049_Id=D009_D049_Id
  				left join D001 on D001_Id=D049_D001_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   			where T189_Flag_Cotar='S' AND 
	   			T189_C007_Id_Cotar = '{$g['usuarioAtual']}'
EOT;
        $retorno = mysql_query($sql);
        
        while ($row = mysql_fetch_array($retorno)) {
            $sqlInsert = <<<EOT
				insert into T059
				(T059_T010_Id,
				T059_Codigo_Produto,
				T059_Descricao_Produto,
				T059_Quantidade,
				T059_Valor_Custo_Unitario, 
				T059_D009_Id,
				T059_T189_Id,
				T059_C004_Id)
				values 
				('{$T010_Id}',
				'{$row['D001_Codigo_Produto']}',
				'{$row['T189_Descricao_Produto']}',
				'{$row['T189_Quantidade']}',
				'{$row['D041_Valor_Base']}',
				'{$row['D009_Id']}',
				'{$row['T189_Id']}',
				'{$g['empresaAtual']}')
EOT;
            //if(!empty($row['T189_D024_Id'])){
            //				$T173 = mysql_query("INSERT INTO T173 (T173_D024_Id, T173_T010_Id) VALUES ('{$row['T189_D024_Id']}', '{$T010_Id}')");
            //			}
            
            mysql_query($sqlInsert);
            mysql_query("UPDATE T189 SET T189_Flag_Cotar = 'N' WHERE T189_C007_Id_Cotar='{$g['usuarioAtual']}';");
            
            $cSql = mysql_query("call T059_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T010_Id}');");
        }
    }
    
    /**
     * insereCotacaoItensViaRequisicao
     */
    public function insereCotacaoItensViaRequisicao()
    {
        global $g, $T010_Id;
        
        $T010       = <<<EOT
		INSERT INTO T010 (
			T010_C007_Id,
			T010_Flag_Marca_Individual,
			T010_Flag_Quantidade_Individual,
			T010_C004_Id,
			T010_Data
		) VALUES (
			'{$g['usuarioAtual']}',
			'N',
			'N',
			'{$g['empresaAtual']}',
			CURDATE()
		);
EOT;
        $resultT010 = mysql_query($T010);
        if (!$resultT010) {
            $erro = mysql_error();
            return "Erro: " . $erro;
        }
        $T010_Id = $g['mysqlLastId'];
        
        $this->insereItemEstoqueRequisicao($T010_Id);
        
        return true;
        
    }
    
    /**
     * insereItemEmergencia
     *
     * @param int $T010_Id
     */
    public function insereItemEmergencia($T010_Id)
    {
        global $g;
        $sql     = <<<EOT
  				SELECT
					T006_Codigo_Produto,T006_Id,
					T006_Descricao_Produto,
					T006_Quantidade_Comprar,T006_D009_Id
				FROM T006 
					LEFT JOIN T005 ON T005_Id=T006_T005_Id
					LEFT JOIN T012 ON T012_T006_Id=T006_Id
					LEFT JOIN T059 ON T059_T006_Id=T006_Id
					LEFT JOIN T129 ON T129_T006_Id=T006_Id
					LEFT JOIN T008 ON T008_T006_Id=T006_Id
					LEFT JOIN T007 ON T007_Id=T008_T007_Id
				WHERE T006_Flag_Cotar='S' 
				AND T005_C004_Id={$g['empresaAtual']}
				AND T005_Flag_Status!=8 
				AND T005_Flag_Status>0 
				AND T005_Flag_Status!=6 
				AND T005_Flag_Status!=9 
				AND T006_Flag_Tipo_Produto='CP' 
				AND T006_Flag_Status!='3' 
				AND T012_Id IS NULL 
				AND T059_Id IS NULL 
				AND T129_Id IS NULL 
				AND T007_Id IS NULL
EOT;
        $retorno = mysql_query($sql);
        
        while ($row = mysql_fetch_array($retorno)) {
            $sqlInsert = <<<EOT
				insert into T059
				(T059_T010_Id,
				T059_T006_Id,
				T059_Codigo_Produto,
				T059_Descricao_Produto,
				T059_Quantidade, 
				T059_D009_Id)
				values 
				('{$T010_Id}',
				'{$row['T006_Id']}',
				'{$row['T006_Codigo_Produto']}',
				'{$row['T006_Descricao_Produto']}',
				'{$row['T006_Quantidade_Comprar']}',
				'{$row['T006_D009_Id']}')
EOT;
            mysql_query($sqlInsert);
            
            $cSql = mysql_query("call T059_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T010_Id}');");
        }
    }
    
    /**
     * insereRegistrosT174
     *
     * @param int $T010_Id
     */
    public function insereRegistrosT174($T010_Id)
    {
        $sql        = "SELECT T173_Id FROM T173 WHERE T173_T010_Id='{$T010_Id}'";
        $resultado1 = mysql_query($sql);
        //$row=mysql_fetch_assoc($resultado1);
        //$T173_Id=$row['T173_Id'];
        
        $sql        = "SELECT T059_Id,T059_Quantidade,T059_Descricao_Produto,T059_D009_Id,T059_T189_Id FROM T059 WHERE T059_T010_Id='{$T010_Id}' AND (T059_T173_Id IS NULL OR T059_T173_Id = '' OR T059_T173_Id=0)";
        $resultado2 = mysql_query($sql);
        //$row=mysql_fetch_array($resultado2);
        //$T059_Id=$row['T059_Id'];
        
        while ($row1 = mysql_fetch_array($resultado1)) {
            @mysql_data_seek($resultado2, 0);
            while ($row2 = mysql_fetch_array($resultado2)) {
                $T059_Id                        = $row2['T059_Id'];
                $T189_Id                        = $row2['T059_T189_Id'];
                $T173_Id                        = $row1['T173_Id'];
                $T174                           = mysql_query("select T174_Id from T174 where T174_T010_Id='{$T010_Id}' and T174_T173_Id='{$T173_Id}' and T174_T059_Id='{$T059_Id}'");
                //$mT174=mysql_fetch_array($T174);
                $row2['T059_Descricao_Produto'] = mysql_real_escape_string($row2['T059_Descricao_Produto']);
                
                if (mysql_num_rows($T174) <= 0) {
                    mysql_query("insert into T174 (T174_T010_Id,T174_T173_Id,T174_T059_Id,T174_T189_Id,T174_Flag_Gerar_OC,T174_IPI,T174_Quantidade,T174_D009_Id,T174_Descricao_Produto)
						                   values ('{$T010_Id}','{$T173_Id}','{$T059_Id}','{$T189_Id}','N','0.00','{$row2['T059_Quantidade']}','{$row2['T059_D009_Id']}','{$row2['T059_Descricao_Produto']}')");
                    mysql_query("update T189 set T189_Flag_Status = 'C' WHERE T189_Id = '{$T189_Id}'");
                }
            }
        }
    }

    /**
     * updateT010_Flag_Marca_Individual
     *
     * @param int $T010_Id
     */
    public function updateT010_Flag_Marca_Individual($T010_Id)
    {
        $T010_Id = mysql_real_escape_string($T010_Id);
        $sql     = mysql_query("select T010_Flag_Marca_Individual from T010 where T010_Id='{$T010_Id}'");
        $row     = mysql_fetch_array($sql);
        if ($row['T010_Flag_Marca_Individual'] == 'N') {
            mysql_query("update T010 set T010_Flag_Marca_Individual='S' where T010_Id='{$T010_Id}'");
        } else {
            mysql_query("update T010 set T010_Flag_Marca_Individual='N' where T010_Id='{$T010_Id}'");
        }
        return true;
    }

    /**
     * montarEmail
     *
     * @param int $T173_Id
     */
    public function montarEmail($T173_Id)
    {
        global $g, $confUsuario;
        
        $T173_Id  = mysql_real_escape_string($T173_Id);
        $sql      = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id LEFT JOIN D013 ON D013_Id = T173_D013_Id where T173_Id='{$T173_Id}'";
        $Consulta = mysql_query($sql);
        $Consulta = mysql_fetch_array($Consulta);
        //echo "CPF: ".$Consulta['D024_Cpf']."<br />CNPJ: ".$Consulta['D024_Cnpj'];
        
        //retira os caracteres especiais para verificar se o campo está preenchido
        $cpfVerificar  = preg_replace("/[.-]/i", "", $Consulta['D024_Cpf']);
        $cnpjVerificar = preg_replace("/[.\/-]/i", "", $Consulta['D024_Cnpj']);
        
        $cpfCnpj = '';
        if (!empty($cpfVerificar)) {
            $cpfCnpj = "CPF: " . $Consulta['D024_Cpf'];
        } elseif (!empty($cnpjVerificar)) {
            $cpfCnpj = "CNPJ: " . $Consulta['D024_Cnpj'];
        }
        // echo "<br /><br />".$cpfCnpj;
        // die();
        
        $D024_Id   = $Consulta['T173_D024_Id'];
        $T010_Id   = $Consulta['T173_T010_Id'];
        $parametro = base64_encode($g['empresaAtual'] . '|' . $T010_Id . '|' . $D024_Id);
        
        $sql          = "select C004_Nome_Empresa,C004_Site,C004_Logo_Marca,C004_Nome_Abreviado from C004 where C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        }
        
        $array['Para']    = (!empty($Consulta['D013_E_Mail'])) ? $Consulta['D013_E_Mail'] : $Consulta['D024_E_Mail'];
        $array['Assunto'] = "Cotação de Compra Nº[{$Consulta['T173_T010_Id']}]";
        $array['Corpo']   = '<div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px;">';
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        $array['Corpo'] .= '<img src=' . $caminhoImagem . ' style="margin-left:25px"><br /><br />';
        /*
         * <span style="font-family:arial,verdana;font-size:16px;font-weight:bold">
         Olá {$Consulta['D024_Nome_Empresa']},<br /><br />
         </span>
         * <span style="font-family:arial,verdana;font-size:14px;">{$empresaAtual['C004_Nome_Abreviado']}</span><br /><br />
         * 
         * <div style="font-size:10px; line-height:16px;">
         E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a />
         </div>
         */
        $array['Corpo'] .= <<<EOT
			<div style="background-color:white; width:500px; color:black; padding:8px;">
				<div style="clear:both; padding-top:16px;"> 
					<span style="font-family:arial,verdana;font-size:14px;">
						Segue link para acessar nossa cotação de compra número {$Consulta['T173_T010_Id']}<br /><br />
						Empresa: {$Consulta['D024_Nome_Empresa']}<br />
						{$cpfCnpj}<br /><br />
						<a href="{$confUsuario['urlRaiz']}est/est008/outros/est008OutrosCotacaoCompraOnline/?p={$parametro}&oneWindow=true">Link Cotação {$Consulta['T173_T010_Id']}</a><br /><br />
					</span>
				</div>
			</div>
			
EOT;
        //Link para imagem relativa a empresa logada
        //<img style="float:left; margin-right:10px;" src="{$caminhoImagem}" /> E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a />
        return $array;
    }
    
    /**
     * enviarParaAnalise
     *
     * @param int $T010_Id
     * @param int $T173_Id
     */
    public function enviarParaAnalise($T010_Id, $T173_Id = null)
    {
        global $g;
        $valorPretendido = "0";
        if ($T173_Id == null) {
            $valorPretendido = $this->calculaValItens($T010_Id, true);
        } else {
            $valorPretendido = $this->calculaValItensEmpresa($T010_Id, $T173_Id, true);
        }
        
        $alcadaSupervisor = str_replace(",", ".", $g['conf']->ini('alcadaCompraSupervisor'));
        $nivel;
        if ($valorPretendido <= $alcadaSupervisor) {
            $nivel = 'S';
        } else {
            $nivel = 'D';
        }
        
        mysql_query("UPDATE T173 set T173_Flag_Alcada_Status='L', T173_Flag_Alcada_Nivel='{$nivel}',T173_Valor_Pretendido_Alcada='{$valorPretendido}' WHERE T173_Id='{$T173_Id}'");
        if (mysql_error())
            return mysql_error();
        else
            return true;
    }
    
    /**
     * verificaAlcadaCotacao
     *
     * @param int $T173_Id
     * @param decimal $valorPretendido
     */
    public function verificaAlcadaCotacao($T173_Id, $valorPretendido)
    {
        
        global $g;
        
        $retorno = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007    = mysql_fetch_array($retorno);
        
        $alcadaUsuario = 'D';
        if ($C007['C007_Flag_Vendedor'] == 'C') {
            $alcadaUsuario = str_replace(",", ".", $g['conf']->ini('alcadaCompraComprador'));
        } else if ($C007['C007_Flag_Vendedor'] == 'S') {
            $alcadaUsuario = str_replace(",", ".", $g['conf']->ini('alcadaCompraSupervisor'));
        } else if ($C007['C007_Flag_Vendedor'] != 'D') {
            return '4';
        }
        
        // se o usuario for diretor, nao passa por liberação
        if ($alcadaUsuario == 'D')
            return true;
        
        $retorno = mysql_query("SELECT * FROM T173 WHERE T173_Id='{$T173_Id}'");
        $T173    = mysql_fetch_array($retorno);
        if ($valorPretendido > $alcadaUsuario) {
            if ($T173['T173_C007_Id_Liberou_Alcada'] > 0 && $T173['T173_Data_Liberou_Alcada'] > 0 && $T173['T173_Valor_Liberou_Alcada'] > 0) {
                if ($T173['T173_Valor_Liberou_Alcada'] == $valorPretendido) {
                    return true;
                } else {
                    return '5';
                }
            } else {
                return '6';
            }
        }
        return true;
    }
    
    /**
     * geraOCEmpresa
     *
     * @param int $T010_Id
     * @param int $T173_Id
     * @param int $C004_Id
     */
    public function geraOCEmpresa($T010_Id, $T173_Id, $C004_Id)
    {
        global $g, $resposta_T011_Id;
        $retorno = mysql_query("SELECT T173_D024_Id,T173_ICMS,T173_Desconto FROM T173 WHERE T173_Id='{$T173_Id}'");
        $T173    = mysql_fetch_array($retorno);
        
        $sqlT011    = "SELECT * FROM T011 WHERE T011_T010_Id='{$T010_Id}' AND T011_D024_Id='{$T173['T173_D024_Id']}'";
        $resultT011 = mysql_query($sqlT011);
        $linhaT011  = mysql_fetch_array($resultT011);
        $rows       = mysql_num_rows($resultT011);
        if ($rows > 0) {
            return '7';
        }
        
        if ($g['conf']->ini('usarAlcadaCompra')) {
            $valorPretendido = $this->calculaValItensEmpresa($T010_Id, $T173_Id, true);
            $alcada          = $this->verificaAlcadaCotacao($T173_Id, $valorPretendido);
            if ($alcada !== true)
                return $alcada;
        }
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $sql     = "INSERT INTO T011 (T011_D024_Id,
								  T011_C007_Id, 
		                          T011_Observacao, 
		                          T011_C004_Id, 
		                          T011_Data_Emissao, 
		                          T011_Flag_Tipo,
		                          T011_T010_Id
		                          ) VALUES (			                           
		                           '{$T173['T173_D024_Id']}',
		                           '{$g['usuarioAtual']}',
								   '',
								   '{$C004_Id}',
								   CURDATE(),
								   'Cotação (empresa)',
								   '{$T010_Id}'
								   );";
        $retorno = mysql_query($sql);
        
        $T011_Id = $g['mysqlLastId'];
        
        $sqlT189 = "SELECT T189_Id FROM T174 
						LEFT JOIN T189 ON T174_T189_Id = T189_Id
						LEFT JOIN T173 ON T174_T173_Id = T173_Id
						WHERE T173_Id = '{$T173_Id}'";
        $T189    = mysql_query($sqlT189);
        $T189    = mysql_fetch_array($T189);
        $T189_Id = $T189[0];
        mysql_query("update T189 set T189_Flag_Status = 'D' WHERE T189_Id = '{$T189_Id}'");
        
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '2';
        }
        
        $sql       = "SELECT T010_Flag_Quantidade_Individual,T010_Flag_Marca_Individual from T010 WHERE T010_Id='{$T010_Id}'";
        $result    = mysql_query($sql);
        $linhaT010 = mysql_fetch_array($result);
        
        $sql = "SELECT T174_Valor_ST,T174_Id, T174_Quantidade,T059_Quantidade,T174_IPI, T174_Valor_Custo_Unitario,T174_T010_Id,T174_T173_Id,T174_T059_Id,D001_Codigo_Produto,T174_Descricao_Produto,T174_D009_Id,T174_Id,T174_Valor_ST
					FROM T174 
				LEFT JOIN T059 ON T059_Id=T174_T059_Id";
        
        if ($linhaT010['T010_Flag_Marca_Individual'] == "S") {
            $sql .= " LEFT JOIN D009 ON D009_Id=T174_D009_Id";
        } else {
            $sql .= " LEFT JOIN D009 ON D009_Id=T059_D009_Id";
        }
        
        $sql .= " LEFT JOIN D049 ON D049_Id=D009_D049_Id
					LEFT JOIN D001 ON D001_Id=D049_D001_Id
					WHERE T174_T173_Id='{$T173_Id}' AND T174_T010_Id='{$T010_Id}' AND T174_Valor_Custo_Unitario>0 AND T174_Flag_Gerar_OC='1'";
        
        if ($linhaT010['T010_Flag_Quantidade_Individual'] == "S") {
            $sql .= ' AND T174_Quantidade>0';
        }
        
        $retorno = mysql_query($sql);
        
        while ($T174 = mysql_fetch_assoc($retorno)) {
            
            if ($linhaT010['T010_Flag_Quantidade_Individual'] == "S") {
                $qnt = $T174['T174_Quantidade'];
            } else {
                $qnt = $T174['T059_Quantidade'];
            }
            
            // Verifica se é obrigatório usar o D009 para gerar OC na empresa
            $D009_Obrigatorio = $g['conf']->ini('D009ObrigatorioGerarOrdemCompra');
            
            /**
             * CASO NÃO TENHA QUANTIDADE, NÃO SERÁ POSSÍVEL GERAR OC
             */
            
            if ($qnt <= 0) {
                mysql_query("ROLLBACK");
                return "10";
            }
            
            if ($D009_Obrigatorio == true || $C004_Id != $g['empresaAtual']) {
                /**
                 * CASO NÃO EXISTA D009 NA T059 NÃO SERÁ POSSÍVEL GERAR OC
                 */
                if (empty($T174['T174_D009_Id']) || $T174['T174_D009_Id'] == "0" || $T174['T174_D009_Id'] == "") {
                    mysql_query("ROLLBACK");
                    return "8";
                }
                
                
                /**
                 * Caso seja obrigatório D009, é feio os selects para pegar o D009 da empresa selecionada
                 * 
                 * @var resource
                 */
                $D049  = mysql_query("SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$T174['T174_D009_Id']}'");
                $mD049 = mysql_fetch_array($D049);
                
                $newD009  = mysql_query("SELECT D009_Id FROM D009 WHERE D009_C004_Id='{$C004_Id}' AND D009_D049_Id='{$mD049['D009_D049_Id']}'");
                $mNewD009 = mysql_fetch_array($newD009);
                if (mysql_num_rows($newD009) == 0 || mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "9";
                }
                $D009_Inerir = $mNewD009['D009_Id'];
            } else {
                $D009_Inerir = $T174['T174_D009_Id'];
            }
            $valorIPI = $T174['T174_Valor_Custo_Unitario'] * $T174['T174_IPI'] / 100;
            $sql      = "INSERT INTO T012 (T012_T011_Id,
									  T012_Codigo_Produto, 
			                          T012_Descricao_Produto, 
			                          T012_Quantidade, 
			                          T012_Valor_Custo_Unitario,
			                          T012_Desconto_Tabela,
			                          T012_Aliquota_ICMS,
			                          T012_Aliquota_IPI,
									  T012_Valor_IPI,
			                          T012_D009_Id,
			                          T012_T059_Id,
			                          T012_T174_Id,
			                          T012_C004_Id,
									  T012_ST
			                          ) VALUES (			                           
			                           '{$T011_Id}',
			                           '{$T174['D001_Codigo_Produto']}',
									   '{$T174['T174_Descricao_Produto']}',
									   '{$qnt}',
									   '{$T174['T174_Valor_Custo_Unitario']}',
									   '{$T173['T173_Desconto']}',
									   '{$T173['T173_ICMS']}',
									   '{$T174['T174_IPI']}',
									   '{$valorIPI}',
									   '{$D009_Inerir}',
									   '{$T174['T174_T059_Id']}',
									   '{$T174['T174_Id']}',
									   '{$C004_Id}',
									   '{$T174['T174_Valor_ST']}'
									   );";
            mysql_query($sql);
            
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '3';
            }
            
            $cSql = mysql_query("call T012_Gravar_Totalizacao('{$g['mysqlLastId']}');");
        }
        
        $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T011_Id}');");
        mysql_query("COMMIT");
        $resposta_T011_Id = $T011_Id;
        return true;
    }
    
    /**
     * geraOCMarcados
     *
     * @param int $T010_Id
     */
    public function geraOCMarcados($T010_Id)
    {
        
        global $g;
        
        if ($g['conf']->ini('usarAlcadaCompra')) {
            $valorPretendido = $this->calculaValItens($T010_Id, true);
            $alcada          = $this->verificaAlcadaCotacao($T010_Id, $valorPretendido);
            if ($alcada !== true)
                return $alcada;
        }
        
        // pega todas as empresas
        $sql     = <<<EOT
	  			SELECT T174_Id, T173_D024_Id,T173_ICMS,T173_Desconto FROM T174 
				LEFT JOIN T059 ON T059_Id=T174_T059_Id
				LEFT JOIN D009 ON D009_Id=T059_D009_Id
				LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				LEFT JOIN T173 ON T173_Id=T174_T173_Id
				WHERE T174_T010_Id='{$T010_Id}' AND T174_Valor_Custo_Unitario>0 AND T174_Flag_Gerar_OC='1'
				GROUP BY T173_D024_Id;
EOT;
        $retorno = mysql_query($sql);
        // para cada empresa
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($D024 = mysql_fetch_array($retorno)) {
            
            
            $D024_Id = $D024['T173_D024_Id'];
            
            $sql      = "INSERT INTO T011 (T011_D024_Id,
									  T011_C007_Id, 
			                          T011_Observacao, 
			                          T011_C004_Id, 
			                          T011_Data_Emissao, 
			                          T011_Flag_Tipo 
			                          ) VALUES (			                           
			                           '{$D024_Id}',
			                           '{$g['usuarioAtual']}',
									   '',
									   '{$g['empresaAtual']}',
									   CURDATE(),
									   'Cotação (marcados)'
									   );";
            $retorno1 = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '2';
            }
            
            $T011_Id = $g['mysqlLastId'];
            
            $sql       = "SELECT T010_Flag_Quantidade_Individual,T010_Flag_Marca_Individual from T010 WHERE T010_Id='{$T010_Id}'";
            $result    = mysql_query($sql);
            $linhaT010 = mysql_fetch_array($result);
            
            // pega todos os T174 desta empresa nesta cotação
            $sql = "SELECT T174_Id, T174_Flag_Gerar_OC, T174_Valor_Custo_Unitario, T174_Quantidade, T174_IPI, T059_Quantidade, T174_T010_Id, T174_T173_Id, T174_T059_Id ,D001_Codigo_Produto,D001_Descricao_Produto, T174_D009_Id FROM T174 
					LEFT JOIN T059 ON T059_Id=T174_T059_Id";
            
            if ($linhaT010['T010_Flag_Marca_Individual'] == "S") {
                $sql .= " LEFT JOIN D009 ON D009_Id=T174_D009_Id";
            } else {
                $sql .= " LEFT JOIN D009 ON D009_Id=T059_D009_Id";
            }
            
            $sql .= " LEFT JOIN D049 ON D049_Id=D009_D049_Id
					LEFT JOIN D001 ON D001_Id=D049_D001_Id
					LEFT JOIN T173 ON T173_Id=T174_T173_Id
					WHERE T174_T010_Id='{$T010_Id}' AND T174_Valor_Custo_Unitario>0 AND T174_Flag_Gerar_OC='1' AND T173_D024_Id='{$D024_Id}'";
            
            if ($linhaT010['T010_Flag_Quantidade_Individual'] == "S") {
                $sql .= ' AND T174_Quantidade>0';
            }
            
            $ret = mysql_query($sql);
            
            while ($T174 = mysql_fetch_assoc($ret)) {
                
                if ($linhaT010['T010_Flag_Quantidade_Individual'] == "S") {
                    $qnt = $T174['T174_Quantidade'];
                } else {
                    $qnt = $T174['T059_Quantidade'];
                }
                mysql_query("ROLLBACK");
                $valorIPI = $T174['T174_Valor_Custo_Unitario'] * $T174['T174_IPI'] / 100;
                $sql      = "INSERT INTO T012 (T012_T011_Id,
										  T012_Codigo_Produto, 
				                          T012_Descricao_Produto, 
				                          T012_Quantidade, 
				                          T012_Valor_Custo_Unitario, 
			                          	  T012_Desconto_Tabela,
			                          	  T012_Aliquota_ICMS,
			                          	  T012_Aliquota_IPI,
										  T012_Valor_IPI,
			                          	  T012_D009_Id,
										  T012_ST,
										  T012_T174_Id,
										  T012_T059_Id
				                          ) VALUES (			                           
				                           '{$T011_Id}',
				                           '{$T174['D001_Codigo_Produto']}',
										   '{$T174['D001_Descricao_Produto']}',
										   '{$qnt}',
										   '{$T174['T174_Valor_Custo_Unitario']}',
									   	   '{$D024['T173_Desconto']}',
									       '{$D024['T173_ICMS']}',
									       '{$T174['T174_IPI']}',
										   '{$valorIPI}',
									       '{$T174['T174_D009_Id']}',
										   '{$T174['T174_Valor_ST']}',
										   '{$T174['T174_Id']}',
										   '{$T174['T174_T059_Id']}'
										   );";
                
                mysql_query($sql);
                
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '3';
                }
                
                $cSql = mysql_query("call T012_Gravar_Totalizacao('{$g['mysqlLastId']}');");
                
            }
            
            $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T011_Id}');");
            
        }
        
        mysql_query("COMMIT");
        
        return true;
    }
    
    /**
     * calculaNumItens
     *
     * @param int $T010_Id
     */
    public function calculaNumItens($T010_Id)
    {
        $sql    = "SELECT COUNT(*) as itens FROM T174 WHERE T174_Flag_Gerar_OC='1' AND T174_T010_Id='$T010_Id'";
        $result = mysql_query($sql);
        $res    = mysql_fetch_array($result);
        return $res['itens'];
    }
    
    /**
     * calculaValItens
     *
     * @param int $T010_Id
     * @param boolean $inverte
     */
    public function calculaValItens($T010_Id, $inverte = null)
    {
        $sql    = "SELECT SUM(T174_Valor_total) FROM T174 WHERE T174_Flag_Gerar_OC='1' AND T174_T010_Id='$T010_Id'";
        $result = mysql_query($sql);
        $res    = mysql_fetch_array($result);
        if ($inverte == true) {
            return $res['SUM(T174_Valor_total)'];
        }
        return gCorrigeNumero($res['SUM(T174_Valor_total)']);
    }
    
    /**
     * EST008montarEmailPdf
     *
     * @param int $T010_Id
     * @param int $D024_Id
     */
    public function EST008montarEmailPdf($T010_Id, $D024_Id)
    {
        global $g, $confUsuario;
        
        $sqlT010 = <<<EOT
		SELECT * FROM T010
		LEFT JOIN C007 on C007_Id=T010_C007_Id
		LEFT JOIN C004 on C004_Id=T010_C004_Id
		LEFT JOIN D020 ON D020_Id=C004_D020_Id
		LEFT JOIN D018 ON D018_Id=C004_D018_Id
		LEFT JOIN T173 ON T173_T010_Id=T010_Id
		WHERE T010_Id='{$T010_Id}' AND T173_D024_Id='{$D024_Id}'
EOT;
        
        $resultT010 = mysql_query($sqlT010);
        $T010       = mysql_fetch_assoc($resultT010);
        
        $T010_Data = gCorrigeData($T010['T010_Data']);
        
        
        if (!empty($T010['C007_Telefone'])) {
            $tel = $T010['C007_Telefone'];
        } else {
            $tel = $T010['C004_Telefone'];
        }
        
        $sqlT174    = <<<EOT
		SELECT *, (SELECT SUM((T059_Quantidade * T174_Valor_Custo_Unitario) + (T174_Valor_Custo_Unitario * (IFNULL(T174_IPI,0)/100)*T059_Quantidade) + IFNULL(T174_Valor_ST,0)) FROM T174 LEFT JOIN T059 ON T059_Id=T174_T059_Id WHERE T174_T173_Id=T173_Id ) AS totalGeral
			FROM T174 
		LEFT JOIN T010 ON T010_Id=T174_T010_Id
		LEFT JOIN T173 ON T173_Id=T174_T173_Id
		LEFT JOIN T059 ON T059_Id=T174_T059_Id
		LEFT JOIN D009 ON D009_Id=T174_D009_Id
		LEFT JOIN D049 ON D049_Id=D009_D049_Id
		LEFT JOIN D082 ON D082_Id=D049_D082_Id
		LEFT JOIN D024 ON D024_Id=T173_D024_Id
		LEFT JOIN D001 ON D001_Id=D049_D001_Id
		LEFT JOIN D037 ON D037_Id=D001_D037_Id
		WHERE T174_T010_Id='{$T010_Id}' AND T173_D024_Id='{$D024_Id}'
		AND T174_Flag_Excluido='N'
EOT;
        $restulT174 = mysql_query($sqlT174);
        
        
        $sqlC004    = "SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $C004       = mysql_fetch_array($resultC004);
        
        $sqlD024    = <<<EOT
		SELECT * FROM D024 
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		WHERE D024_Id='{$D024_Id}'
EOT;
        $resultD024 = mysql_query($sqlD024);
        $linhaD024  = mysql_fetch_array($resultD024);
        if (!empty($linhaD024['D024_Cpf'])) {
            $cpfCnpj = $linhaD024['D024_Cpf'];
            $cpfCnpj = "<b>CPF:</b> $cpfCnpj";
        } else {
            $cpfCnpj = $linhaD024['D024_Cnpj'];
            $cpfCnpj = "<b>CNPJ:</b> $cpfCnpj";
        }
        //$layout = $g['conf']->ini('layoutOrcamentoEmailPdf');
        
        // Array de retorno para criação do e-mail
        $array            = array();
        $array['Para']    = $linhaD024['D024_E_Mail'];
        //(isset($T003['T003_D013_Id'])) ? $T003['D013_E_Mail'] : $D024['D024_E_Mail'];
        $array['Assunto'] = "Cotação de Compra Nº[{$T010_Id}]";
        
        /**
         * PDF
         */
        require_once('bibliotecas/tcpdf/config/lang/eng.php');
        require_once('bibliotecas/tcpdf/tcpdf.php');
        
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hardness Sistemas');
        // remove default header/footer
        $pdf->setPrintHeader(false);
        //$pdf->setPrintFooter(true);
        // set font
        $pdf->SetFont('helvetica', '', 7);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // add a page
        $pdf->AddPage();
        
        $html = '';
        /**
         * PDF - STYLE
         */
        $html .= <<<EOT
				<style>
					.header .text {
						font-size: 25px;
					}
					.header .text2 {
						font-size: 35px;
						font-weight: bold;
					}
					.titulo {
						background-color: #E1ECF1;
					}
					.titulo_superior {
						font-size: 30px;
					}
					.itens td {
						border: 1px solid black;
					}
					.textIncluso {
						font-size: 18px;
						color: red;
						background-color: #E1ECF1;
					}
					
				</style>
EOT;
        
        /**
         * PDF - HEADER
         */
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        $html .= <<<EOT
		<table>
			<tr>
				<td align="left">{$caminhoImagem}</td>
			</tr>
		</table>
		<br />
		<table class="header" width="100%">
			<tr>
				<td>
					<table align="left" style="font-size:24px;"  cellpadding="0">
						<tr>
							<td class="titulo_superior"><b><u>Fornecedor</u></b></td>
						</tr>
						<tr>
							<td class="tdGeral">{$linhaD024['D024_Nome_Empresa']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$linhaD024['D024_Endereco']}, {$linhaD024['D024_Numero']} - {$linhaD024['D024_Bairro']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>CEP:</b> {$linhaD024['D024_Cep']} {$linhaD024['D020_Nome_Cidade']} - {$linhaD024['D018_UF']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$cpfCnpj}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Incr. Estadual:</b> {$linhaD024['D024_Inscricao_Estadual']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$linhaD024['D024_Web_Site']}</td>
						</tr>
					</table>
				</td>
				<td>
					<table align="left" style="font-size:24px;"  cellpadding="0">
						<tr>
							<td class="titulo_superior"><b><u>Cliente</u></b></td>
						</tr>
						<tr>
							<td class="tdGeral">{$T010['C004_Nome_Empresa']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$T010['C004_Logradouro']}, {$T010['C004_Numero']} - {$T010['C004_Bairro']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>CEP:</b> {$T010['C004_Cep']} {$T010['D020_Nome_Cidade']} - {$T010['D018_UF']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>CNPJ: </b>{$T010['C004_Cnpj']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Incr. Estadual:</b> {$T010['C004_Inscricao_Estadual']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$T010['C004_Site']}</td>
						</tr>
					</table>
				</td>
				<td width=100px>
					<table align="left" style="font-size:24px;" cellpadding="0">
						<tr>
							<td class="titulo_superior"><b><u>Comprador</u></b></td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Nº cotação:</b> $T010_Id</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Data:</b> {$T010_Data}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Comprador:</b> {$T010['C007_Nome']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Dpto:</b> {$T010['C007_Cargo']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Tel.</b> {$tel}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>E-mail:</b> {$T010['C007_Email']}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        $html .= <<<EOT
				<div style="height:20px; width:100%;"></div>
					<table class="itens" cellspacing="0" cellpadding="2">
						<tr class="titulo">
							<td width="5%">Código</td>
							<td align="center" width="38%">Produto</td>
							<td align="left" width="12%">Marca</td>
							<td align="left" width="3%">Un</td>
							<td align="right" width="7%">Qtd</td>
							<td align="right" width="7%">Unit R\$</td>
							<td align="right" width="7%">Total R\$</td>
							<td align="right" width="7%">ST R\$</td>
							<td align="right" width="7%">% ICMS</td>
							<td align="center" width="7%">% IPI</td>
						</tr>
EOT;
        $totalGeral    = 0;
        $totalUnitario = 0;
        $totalST       = 0;
        while ($T174 = mysql_fetch_array($restulT174)) {
            $qtd      = gCorrigeNumero($T174['T059_Quantidade']);
            $unitario = "0,00";
            $total    = "0,00";
            $IPI      = "0,00";
            $ST       = "0,00";
            $ICMS     = "0,00";
            
            $totalUnitario += $T174['T174_Valor_Custo_Unitario'];
            if ($T174['T174_Valor_Custo_Unitario'] > 0) {
                $unitario = gCorrigeNumero($T174['T174_Valor_Custo_Unitario']);
                // SUM((T059_Quantidade * T174_Valor_Custo_Unitario) + (T174_Valor_Custo_Unitario * (IFNULL(T174_IPI,0)/100)*T059_Quantidade) + IFNULL(T174_Valor_ST,0))
                
                $SomaTotal = ($T174['T059_Quantidade'] * $T174['T174_Valor_Custo_Unitario']) + ($T174['T174_Valor_Custo_Unitario'] * ($T174['T174_IPI'] / 100 * $T174['T059_Quantidade']) + $T174['T174_Valor_ST']);
                
                $totalGeral += $SomaTotal;
                $total = gCorrigeNumero($SomaTotal);
                $totalST += $T174['T174_Valor_ST'];
            }
            if ($T174['T174_IPI'] > 0) {
                $IPI = gCorrigeNumero($T174['T174_IPI']);
            }
            if ($T174['T174_Valor_ST'] > 0) {
                $ST = gCorrigeNumero($T174['T174_Valor_ST']);
            }
            if ($T174['T174_ICMS'] > 0) {
                $ICMS = gCorrigeNumero($T174['T174_ICMS']);
            }
            $html .= <<<EOT
					<tr>
						<td align="center">
							{$T174['T059_Codigo_Produto']}
						</td>
						<td align="left">
							{$T174['T059_Descricao_Produto']}
						</td>
						<td align="left">
							{$T174['D082_Marca']}
						</td>
						<td align="left">
							{$T174['D037_Unidade']}
						</td>
						<td align="right">
							{$qtd}
						</td>
						<td align="right">
						    {$unitario}	
						</td>
						<td align="right">
							{$total}
						</td>
						<td align="right">
							{$ST}
						</td>
						<td align="right">
							{$ICMS}
						</td>
						<td align="center">
							{$IPI}
						</td>
					</tr>
EOT;
            //$totalGeral = $T174['totalGeral'];
        }
        $totalGeral    = gCorrigeNumero($totalGeral);
        $totalST       = gCorrigeNumero($totalST);
        $totalUnitario = gCorrigeNumero($totalUnitario);
        
        $html .= <<<EOT
				<tr>
					<td colspan="6" align="right"><b>TOTAL</b></td>
					<td align="right"><b>$totalGeral</b></td>
					<td align="right">$totalST</td>
					<td align="right"></td>
					<td align="right"></td>
				</tr>
EOT;
        $html .= '</table>';
        
        /**
         * PDF - TOTAIS
         */
        // $html .= <<<EOT
        // <br ></EOT><br />
        // <table width=>
        // <tr>
        // <td align="left" width="105">Valor total da Cotação: </td><td  align="left" width="200"><b>R\$ {$totalGeral}</b></td>
        // </tr>
        // </table>
        // <br />
        // EOT;
        if (!empty($T010["T010_Observacao"])) {
            $html .= '<br><span align="left">Observação: <br><b>' . str_replace(chr(10), '<br>', $T010["T010_Observacao"]) . '</b></span>';
        }
        // Assinatura
        if (!empty($g['c007']['C007_Email_Assinatura']) && trim($g['c007']['C007_Email_Assinatura']) != '<br>') {
            $assinatura = gCorrigeAssinatura($g['c007']['C007_Email_Assinatura']);
            $html .= "<br /><br /><span align=\"left\" style=\"font-size: 25px;\">{$assinatura}</span>";
        }
        
        
        /**
         * PDF - GERA!
         */
        $pdf->writeHTML($html, true, false, true, false, 'C');
        // reset pointer to the last page
        $pdf->lastPage();
        // get data
        $data = $pdf->getPDFData();
        
        // Gravar arquivo no disco
        $anexoDir = $g['pathDados'] . 'tmp/';
        is_dir($anexoDir) or mkdir($anexoDir, 0777, true);
        $anexoNome = "{$anexoDir}cotacaoCompra-{$T010_Id}.pdf";
        @unlink($anexoNome);
        $h = fopen($anexoNome, 'w');
        fwrite($h, $data);
        fclose($h);
        // Adiciona como anexo
        $array['Anexos']    = array(
            $anexoNome
        );
        $array['AnexosWeb'] = array(
            '/hardness3/' . $anexoNome
        );
        // Logo 		
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        
        // (TODO) passar para o arq. conf
        $caminhoImagem  = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem  = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        // Variaveis enviadas para o modulo de e-mails (que serão gravadas na tabela T139)
        $array['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br /><font style='font-size:16px'>Segue Cotação de Compra Nº<b>$T010_Id</b> em anexo.</font><br /><br /><br />";
        //$array['D024_Id'] = $T003['T003_D024_Id'];
        // $array['T139_Valor_Total'] = $T003['T003_Valor_Total'];
        // $sql = "SELECT COUNT(*) FROM T004 where T004_T003_Id='{$T003_Id}'";
        // $registros = mysql_query($sql);
        // $registros = mysql_fetch_array($registros);
        // $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
    }
    
    /**
     * EST008montarEmailPdfDemonstrativo
     *
     * @param int $T010_Id
     * @param int $D024_Id
     */
    public function EST008montarEmailPdfDemonstrativo($T010_Id, $D024_Id)
    {
        global $g, $confUsuario;
        
        $sqlT010 = <<<EOT
		SELECT * FROM T010
		LEFT JOIN C007 on C007_Id=T010_C007_Id
		LEFT JOIN C004 on C004_Id=T010_C004_Id
		LEFT JOIN D020 ON D020_Id=C004_D020_Id
		LEFT JOIN D018 ON D018_Id=C004_D018_Id
		LEFT JOIN T173 ON T173_T010_Id=T010_Id
		LEFT JOIN D013 ON D013_Id=T173_D013_Id
		WHERE T010_Id='{$T010_Id}'
EOT;
        
        $resultT010 = mysql_query($sqlT010);
        $T010       = mysql_fetch_assoc($resultT010);
        
        $T010_Data = gCorrigeData($T010['T010_Data']);
        
        if (!empty($T010['C007_Telefone'])) {
            $tel = $T010['C007_Telefone'];
        } else {
            $tel = $T010['C004_Telefone'];
        }
        
        // $sqlT174 = <<<EOT
        // SELECT * FROM T174 
        // LEFT JOIN T010 ON T010_Id=T174_T010_Id
        // LEFT JOIN T173 ON T173_Id=T174_T173_Id
        // LEFT JOIN T059 ON T059_Id=T174_T059_Id
        // LEFT JOIN D009 ON D009_Id=T174_D009_Id
        // LEFT JOIN D049 ON D049_Id=D009_D049_Id
        // LEFT JOIN D082 ON D082_Id=D049_D082_Id
        // LEFT JOIN D024 ON D024_Id=T173_D024_Id
        // WHERE T174_T010_Id='{$T010_Id}' 
        // AND T174_Flag_Excluido='N' ORDER BY T173_Id
        // EOT;
        // $restultT174 = mysql_query($sqlT174);
        
        
        $sqlC004    = "SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $C004       = mysql_fetch_array($resultC004);
        
        $sqlD024    = <<<EOT
		SELECT * FROM D024 
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		WHERE D024_Id='{$D024_Id}'
EOT;
        $resultD024 = mysql_query($sqlD024);
        $linhaD024  = mysql_fetch_array($resultD024);
        if (!empty($linhaD024['D024_Cpf'])) {
            $cpfCnpj = $linhaD024['D024_Cpf'];
            $cpfCnpj = "<b>CPF:</b> $cpfCnpj";
        } else {
            $cpfCnpj = $linhaD024['D024_Cnpj'];
            $cpfCnpj = "<b>CNPJ:</b> $cpfCnpj";
        }
        //$layout = $g['conf']->ini('layoutOrcamentoEmailPdf');
        
        // Array de retorno para criação do e-mail
        $array            = array();
        //$array['Para'] = "";
        $array['Para']    = (!empty($T010['D013_E_Mail'])) ? $T010['D013_E_Mail'] : $linhaD024['D024_E_Mail'];
        $array['Assunto'] = "Cotação de Compra Nº[{$T010_Id}]";
        
        /**
         * PDF
         */
        require_once('bibliotecas/tcpdf/config/lang/eng.php');
        require_once('bibliotecas/tcpdf/tcpdf.php');
        
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hardness Sistemas');
        // remove default header/footer
        $pdf->setPrintHeader(false);
        //$pdf->setPrintFooter(true);
        $pdf->SetMargins(8, 8, 8, 8);
        // set font
        $pdf->SetFont('helvetica', '', 7);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Orientação da página
        $pdf->setPageOrientation('L');
        // add a page
        $pdf->AddPage();
        
        $html = '';
        /**
         * PDF - STYLE
         */
        $html .= <<<EOT
				<style>
					.header .text {
						font-size: 25px;
					}
					.header .text2 {
						font-size: 35px;
						font-weight: bold;
					}
					.titulo {
						background-color: #E1ECF1;
					}
					.titulo_superior {
						font-size: 30px;
					}
					.itens td {
						border: 1px solid black;
					}
					.textIncluso {
						font-size: 18px;
						color: red;
						background-color: #E1ECF1;
					}
					
				</style>
EOT;
        
        /**
         * PDF - HEADER
         */
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        $html .= <<<EOT
		<table style='height:10px;border:1px solid black;'>
			<tr>
				<td align="left">{$g['c004']['C004_Nome_Empresa']}</td>
				<td align="right" style="font-size: 230%;"><b>Cotação de compra</b> Nº {$T010_Id}&nbsp;&nbsp;</td>
			</tr>
		</table>
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        
        $html .= <<<EOT
				<div style="height:20px; width:100%;"></div>
					<table class="itens" cellspacing="0" cellpadding="2">
						<tr class="titulo">
							<td align="left" width="25%" rowspan="2">Produto</td>
							<td align="center" width="3%" rowspan="2">Qnt</td>
							
EOT;
        $sqlT173    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6";
        $resultT173 = mysql_query($sqlT173);
        $count3     = 0;
        while ($rowT173 = mysql_fetch_array($resultT173)) {
            $D024_Nome_Empresa = substr($rowT173['D024_Nome_Empresa'], 0, 20);
            $html .= <<<EOT
							<td align="right" width="12%" colspan="3" align="center">{$D024_Nome_Empresa}</td>
EOT;
            $count3++;
        }
        if ($count3 < 6) {
            $total = 6 - $count3;
            for ($i = 1; $i <= $total; $i++) {
                $html .= <<<EOT
					<td align="right" width="12%" colspan="3" align="center"></td>
EOT;
            }
        }
        $html .= <<<EOT
			</tr>
			<tr class="titulo">
EOT;
        $sqlT173    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6";
        $resultT173 = mysql_query($sqlT173);
        $count      = 0;
        while ($rowT173 = mysql_fetch_array($resultT173)) {
            $html .= <<<EOT
							<td align="right" width="5%">R\$</td>
							<td align="right" width="3%">IPI</td>
							<td align="right" width="4%">ST</td>
							
EOT;
            $count++;
        }
        if ($count < 6) {
            $total = 6 - $count;
            for ($i = 1; $i <= $total; $i++) {
                $html .= <<<EOT
					<td align="right" width="5%">R\$</td>
					<td align="right" width="3%">IPI</td>
					<td align="right" width="4%">ST</td>
EOT;
            }
        }
        $html .= <<<EOT
			</tr>
EOT;
        $sqlT059    = "SELECT * FROM T059 WHERE T059_T010_Id='{$T010_Id}'";
        $resultT059 = mysql_query($sqlT059);
        while ($T059 = mysql_fetch_array($resultT059)) {
            $T059['T059_Quantidade'] = gCorrigeNumero($T059['T059_Quantidade']);
            $html .= <<<EOT
			<tr>
				<td align="left" rowspan="2">{$T059['T059_Codigo_Produto']} - {$T059['T059_Descricao_Produto']}</td>
				<td rowspan="2">{$T059['T059_Quantidade']}</td>
EOT;
            $sqlT173_2    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6";
            $resultT173_2 = mysql_query($sqlT173_2);
            $count2       = 0;
            while ($T173 = mysql_fetch_array($resultT173_2)) {
                $sqlT174                             = "SELECT * FROM T174 WHERE T174_T059_Id='{$T059['T059_Id']}' AND T174_T173_Id='{$T173['T173_Id']}' LIMIT 6";
                $resultT174                          = mysql_query($sqlT174);
                $rwT174                              = mysql_fetch_array($resultT174);
                $rwT174['T174_Valor_Custo_Unitario'] = gCorrigeNumero($rwT174['T174_Valor_Custo_Unitario']);
                @$rwT174['T174_IPI'] = gCorrigeNumero($rwT174['T174_IPI']);
                @$rwT174['T174_Valor_ST'] = gCorrigeNumero($rwT174['T174_Valor_ST']);
                $html .= <<<EOT
				<td align="right">{$rwT174['T174_Valor_Custo_Unitario']}</td>
				<td align="right">{$rwT174['T174_IPI']}</td>
				<td align="right">{$rwT174['T174_Valor_ST']}</td>
EOT;
                $count2++;
            }
            if ($count2 < 6) {
                $total = 6 - $count2;
                for ($i = 1; $i <= $total; $i++) {
                    $html .= <<<EOT
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
EOT;
                }
            }
            $html .= <<<EOT
			</tr>
EOT;
            $html .= <<<EOT
			<tr>
EOT;
            $sqlT173_2    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6";
            $resultT173_2 = mysql_query($sqlT173_2);
            $count2       = 0;
            $ret          = "";
            while ($T173 = mysql_fetch_array($resultT173_2)) {
                $sqlT174 = "SELECT * FROM T174 WHERE T174_T059_Id='{$T059['T059_Id']}' AND T174_T173_Id='{$T173['T173_Id']}' LIMIT 6";
                $ret .= $sqlT174 . "  ";
                $resultT174                          = mysql_query($sqlT174);
                $rwT174                              = mysql_fetch_array($resultT174);
                $rwT174['T174_Valor_Custo_Unitario'] = gCorrigeNumero($rwT174['T174_Valor_Custo_Unitario']);
                @$rwT174['T174_IPI'] = gCorrigeNumero($rwT174['T174_IPI']);
                @$rwT174['T174_Valor_ST'] = gCorrigeNumero($rwT174['T174_Valor_ST']);
                @$rwT174['T174_Observacao'] = $rwT174['T174_Observacao'];
                $html .= <<<EOT
				
				<td align="left" colspan="3">{$rwT174['T174_Observacao']}</td>
EOT;
                $count2++;
            }
            //die($ret);
            if ($count2 < 6) {
                $total = 6 - $count2;
                for ($i = 1; $i <= $total; $i++) {
                    $html .= <<<EOT
					<td align="right" colspan="3"></td>
EOT;
                }
            }
            $html .= <<<EOT
			</tr>
EOT;
        }
        
        $html .= '</table>';
        
        // tabela 2
        $sqlT173Teste    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6,6";
        $resultT173Teste = mysql_query($sqlT173Teste);
        
        if (mysql_num_rows($resultT173Teste) > 0) {
            $html .= <<<EOT
				<div style="height:20px; width:100%;"></div>
					<table class="itens" cellspacing="0" cellpadding="2">
						<tr class="titulo">
							<td align="left" width="25%" rowspan="2">Produto</td>
							<td align="center" width="3%" rowspan="2">Qnt</td>
							
EOT;
            $sqlT173    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6,6";
            $resultT173 = mysql_query($sqlT173);
            $count3     = 0;
            while ($rowT173 = mysql_fetch_array($resultT173)) {
                $D024_Nome_Empresa = substr($rowT173['D024_Nome_Empresa'], 0, 20);
                $html .= <<<EOT
								<td align="right" width="12%" colspan="3" align="center">{$D024_Nome_Empresa}</td>
EOT;
                $count3++;
            }
            if ($count3 < 6) {
                $total = 6 - $count3;
                for ($i = 1; $i <= $total; $i++) {
                    $html .= <<<EOT
						<td align="right" width="12%" colspan="3" align="center"></td>
EOT;
                }
            }
            $html .= <<<EOT
				</tr>
				<tr class="titulo">
EOT;
            $sqlT173    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6,6";
            $resultT173 = mysql_query($sqlT173);
            $count      = 0;
            while ($rowT173 = mysql_fetch_array($resultT173)) {
                $html .= <<<EOT
								<td align="right" width="5%">R\$</td>
								<td align="right" width="3%">IPI</td>
								<td align="right" width="4%">ST</td>
								
EOT;
                $count++;
            }
            if ($count < 6) {
                $total = 6 - $count;
                for ($i = 1; $i <= $total; $i++) {
                    $html .= <<<EOT
						<td align="right" width="5%">R\$</td>
						<td align="right" width="3%">IPI</td>
						<td align="right" width="4%">ST</td>
EOT;
                }
            }
            $html .= <<<EOT
				</tr>
EOT;
            $sqlT059    = "SELECT * FROM T059 WHERE T059_T010_Id='{$T010_Id}'";
            $resultT059 = mysql_query($sqlT059);
            while ($T059 = mysql_fetch_array($resultT059)) {
                $T059['T059_Quantidade'] = gCorrigeNumero($T059['T059_Quantidade']);
                $html .= <<<EOT
				<tr>
					<td align="left" rowspan="2">{$T059['T059_Codigo_Produto']} - {$T059['T059_Descricao_Produto']}</td>
					<td rowspan="2">{$T059['T059_Quantidade']}</td>
EOT;
                $sqlT173_2    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6,6";
                $resultT173_2 = mysql_query($sqlT173_2);
                $count2       = 0;
                while ($T173 = mysql_fetch_array($resultT173_2)) {
                    $sqlT174                             = "SELECT * FROM T174 WHERE T174_T059_Id='{$T059['T059_Id']}' AND T174_T173_Id='{$T173['T173_Id']}'";
                    $resultT174                          = mysql_query($sqlT174);
                    $rwT174                              = mysql_fetch_array($resultT174);
                    $rwT174['T174_Valor_Custo_Unitario'] = gCorrigeNumero($rwT174['T174_Valor_Custo_Unitario']);
                    $rwT174['T174_IPI']                  = gCorrigeNumero($rwT174['T174_IPI']);
                    $rwT174['T174_Valor_ST']             = gCorrigeNumero($rwT174['T174_Valor_ST']);
                    $html .= <<<EOT
					<td align="right">{$rwT174['T174_Valor_Custo_Unitario']}</td>
					<td align="right">{$rwT174['T174_IPI']}</td>
					<td align="right">{$rwT174['T174_Valor_ST']}</td>
EOT;
                    $count2++;
                }
                if ($count2 < 6) {
                    $total = 6 - $count2;
                    for ($i = 1; $i <= $total; $i++) {
                        $html .= <<<EOT
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
EOT;
                    }
                }
                $html .= <<<EOT
				</tr>
EOT;
                $html .= <<<EOT
				<tr>
EOT;
                $sqlT173_2    = "SELECT * FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id LIMIT 6,6";
                $resultT173_2 = mysql_query($sqlT173_2);
                $count2       = 0;
                while ($T173 = mysql_fetch_array($resultT173_2)) {
                    $sqlT174                             = "SELECT * FROM T174 WHERE T174_T059_Id='{$T059['T059_Id']}' AND T174_T173_Id='{$T173['T173_Id']}'";
                    $resultT174                          = mysql_query($sqlT174);
                    $rwT174                              = mysql_fetch_array($resultT174);
                    $rwT174['T174_Valor_Custo_Unitario'] = gCorrigeNumero($rwT174['T174_Valor_Custo_Unitario']);
                    $rwT174['T174_IPI']                  = gCorrigeNumero($rwT174['T174_IPI']);
                    $rwT174['T174_Valor_ST']             = gCorrigeNumero($rwT174['T174_Valor_ST']);
                    $html .= <<<EOT
					
					<td align="right" colspan="3">{$rwT174['T174_Observacao']}</td>
EOT;
                    $count2++;
                }
                if ($count2 < 6) {
                    $total = 6 - $count2;
                    for ($i = 1; $i <= $total; $i++) {
                        $html .= <<<EOT
						<td align="right" colspan="3"></td>
EOT;
                    }
                }
                $html .= <<<EOT
				</tr>
EOT;
            }
            
            $html .= '</table>';
        }
        
        
        
        if (!empty($T010["T010_Observacao"])) {
            $html .= '<br><span align="left">Observação: <br><b>' . str_replace(chr(10), '<br>', $T010["T010_Observacao"]) . '</b></span>';
        }
        // Assinatura
        if (!empty($g['c007']['C007_Email_Assinatura']) && trim($g['c007']['C007_Email_Assinatura']) != '<br>') {
            $assinatura = gCorrigeAssinatura($g['c007']['C007_Email_Assinatura']);
            $html .= "<br /><br /><span align=\"left\" style=\"font-size: 25px;\">{$assinatura}</span>";
        }
        
        
        /**
         * PDF - GERA!
         */
        $pdf->writeHTML($html, true, false, true, false, 'C');
        // reset pointer to the last page
        $pdf->lastPage();
        // get data
        $data = $pdf->getPDFData();
        
        // Gravar arquivo no disco
        $anexoDir = $g['pathDados'] . 'tmp/';
        is_dir($anexoDir) or mkdir($anexoDir, 0777, true);
        $anexoNome = "{$anexoDir}cotacaoCompra-{$T010_Id}.pdf";
        @unlink($anexoNome);
        $h = fopen($anexoNome, 'w');
        fwrite($h, $data);
        fclose($h);
        // Adiciona como anexo
        $array['Anexos']    = array(
            $anexoNome
        );
        $array['AnexosWeb'] = array(
            '/hardness3/' . $anexoNome
        );
        // Logo 		
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem  = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem  = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        // Variaveis enviadas para o modulo de e-mails (que serão gravadas na tabela T139)
        $array['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br /><font style='font-size:16px'>Segue Cotação de Compra Nº<b>$T010_Id</b> em anexo.</font><br /><br /><br />";
        //$array['D024_Id'] = $T003['T003_D024_Id'];
        // $array['T139_Valor_Total'] = $T003['T003_Valor_Total'];
        // $sql = "SELECT COUNT(*) FROM T004 where T004_T003_Id='{$T003_Id}'";
        // $registros = mysql_query($sql);
        // $registros = mysql_fetch_array($registros);
        // $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
    }
    
    /**
     * EST008montarEmailPdfDemonstrativo
     *
     * @param int $T010_Id
     * @param int $D024_Id
     */
    public function calculaValItensEmpresa($T010_Id, $T173_Id, $inverte = null)
    {
        $sql    = "SELECT SUM(T174_Valor_total) FROM T174 WHERE T174_Flag_Gerar_OC='1' AND T174_T010_Id='$T010_Id' AND T174_T173_Id='$T173_Id'";
        $result = mysql_query($sql);
        $res    = mysql_fetch_array($result);
        if ($inverte == true) {
            return $res['SUM(T174_Valor_total)'];
        }
        return gCorrigeNumero($res['SUM(T174_Valor_total)']);
    }
    
    /**
     * est008_alterar_marca
     *
     * @param Form $form
     * @param int $T059_Id
     * @param int $T010_Id
     * @param int $T173_Id
     */
    public function est008_alterar_marca($form, $T059_Id, $T010_Id, $T173_Id)
    {
        $D009_Id = $form->campoValorEnviado('Marca');
        //echo "D009: ".$D009_Id." T059: ". $T059_Id." T010: ".$T010_Id;
        
        //$sql = "SELECT * FROM T174 WHERE T174_D009_Id='{$D009_Id}' AND T174_T059_Id='{$T059_Id}' AND T174_T010_Id='{$T010_Id}' AND T174_T173_Id='{$T173_Id}'";
        
        $sql = "UPDATE T174 SET T174_D009_Id='{$D009_Id}' WHERE T174_T059_Id='{$T059_Id}' AND T174_T010_Id='{$T010_Id}' AND T174_T173_Id='{$T173_Id}'";
        
        mysql_query($sql);
        if (mysql_error()) {
            return mysql_error();
        } else {
            return true;
        }
        
    }
    
    /**
     * verificaFornecedor
     *
     * @param Form $form
     */
    public function verificaFornecedor($form)
    {
        $T173_D024_Id = $form->campoValorEnviado('T173_D024_Id');
        $T173_T010_Id = $form->campoValorEnviado('T173_T010_Id');
        
        $sql    = "SELECT * FROM T173 WHERE T173_D024_Id='{$T173_D024_Id}' AND T173_T010_Id='$T173_T010_Id'";
        $result = mysql_query($sql);
        $row    = mysql_num_rows($result);
        if ($row > 0) {
            return "Já existe esse fornecedor nessa cotação";
        } else {
            return true;
        }
    }
    
    /**
     * montarEmailCallback
     *
     * @param int $T173_Id
     */
    public function montarEmailCallback($T173_Id)
    {
        mysql_query("UPDATE T173 SET T173_Flag_Email_Enviado='S' WHERE T173_Id={$T173_Id}");
    }
    
    /**
     * incluirProdutoCotacaoForn
     *
     * @param int $D009_Id
     * @param int $T010_Id
     * @param int $T173_Id
     */
    public function incluirProdutoCotacaoForn($D009_Id, $T010_Id, $T173_Id)
    {
        global $g;
        
        $D009  = mysql_query("SELECT * FROM D009 LEFT JOIN D049 on D049_Id=D009_D049_Id LEFT JOIN D001 on D001_Id=D049_D001_Id WHERE D009_Id='{$D009_Id}'");
        $mD009 = mysql_fetch_array($D009);
        
        $sqlT059 = <<<EOT
		INSERT INTO T059 (
			T059_T010_Id,
			T059_D009_Id,
			T059_T173_Id,
			T059_Codigo_Produto,
			T059_Descricao_Produto
		) VALUES (
			'{$T010_Id}',
			'{$D009_Id}',
			'{$T173_Id}',
			'{$mD009['D001_Codigo_Produto']}',
			'{$mD009['D001_Descricao_Produto']}'
		);
EOT;
        
        $resultT059 = mysql_query($sqlT059);
        
        $T059_Id = $g['mysqlLastId'];
        
        $sqlT174    = <<<EOT
		INSERT INTO T174 (
			T174_T010_Id,
			T174_T173_Id,
			T174_T059_Id,
			T174_D009_Id,
			T174_Descricao_Produto
		) VALUES (
			'{$T010_Id}',
			'{$T173_Id}',
			'{$T059_Id}',
			'{$D009_Id}',
			'{$mD009['D001_Descricao_Produto']}'
		);
EOT;
        $resultT074 = mysql_query($sqlT174);
        
        return true;
    }

    /**
     * alteradoProduto
     *
     * @param Form $form
     * @param Form $T059_Id
     * @param Form $T173_Id
     */
    public function alteradoProduto($form, $T059_Id, $T173_Id)
    {
        global $g;
        $D082_Marca             = $form->campoValorEnviado('D082_Marca');
        $T059_Descricao_Produto = $form->campoValorEnviado('T059_Descricao_Produto');
        $T059_Quantidade        = $form->campoValorEnviado('T059_Quantidade');
        $T059_T010_Id           = $form->campoValorEnviado('T059_T010_Id');
        $T059_Codigo_Produto    = $form->campoValorEnviado('T059_Codigo_Produto');
        $T059_D009_Id           = $form->campoValorEnviado('T059_D009_Id');
        
        $T059   = <<<EOT
			UPDATE T059 SET T059_Quantidade='{$T059_Quantidade}', 
						    T059_Codigo_Produto='{$T059_Codigo_Produto}',
						    T059_D009_Id='{$T059_D009_Id}'
			WHERE T059_Id='{$T059_Id}'
EOT;
        $result = mysql_query($T059);
        $erro   = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        
        $T174   = <<<EOT
		UPDATE T174 SET T174_Descricao_Produto='{$T059_Descricao_Produto}', T174_D009_Id='{$T059_D009_Id}' WHERE T174_T059_Id='{$T059_Id}' AND T174_T173_Id='{$T173_Id}';
EOT;
        $result = mysql_query($T174);
        $erro   = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        return true;
    }
    
    /**
     * insereOrdemServico
     */
    public function insereOrdemServico()
    {
        global $g, $T128_Id;
        
        $C007_Id = $g['usuarioAtual'];
        
        mysql_query("set autocommit=0");
        mysql_query("start transaction");
        
        $cClausula = "insert into T128 (T128_C004_Id,
		                               T128_C007_Id,
		                               T128_Data_Emissao,
		                               T128_D006_Id)
		                             values
		                              ('{$g['empresaAtual']}',
		                               '$C007_Id',
		                               current_date(),
		                               '{$g['C031']['CFOPPadraoOrdemServico']}'
		                               )";
        
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("rollback");
            return "Erro: {$cErro} Clausula: {$cClausula}";
            exit;
        }
        
        $T128_Id = $g['mysqlLastId'];
        
        $cSql_D001 = mysql_query("SELECT *
        FROM T189
        	LEFT JOIN D009 ON D009_Id=T189_D009_Id
        	LEFT JOIN D049 ON D049_Id=D009_D049_Id
        	LEFT JOIN D001 ON D049_D001_Id=D001_Id
        WHERE T189_Flag_Produzir='S'");
        
        while ($mD001 = mysql_fetch_array($cSql_D001)) {
            $cClausula = " insert into T129 (";
            $cClausula .= " T129_T128_Id,";
            $cClausula .= " T129_Quantidade,";
            $cClausula .= " T129_Codigo_Produto,";
            $cClausula .= " T129_D009_Id,";
            $cClausula .= " T129_Descricao_Produto,";
            $cClausula .= " T129_Valor_Custo_Unitario,";
            $cClausula .= " T129_T006_Id, ";
            $cClausula .= " T129_Quantidade_Estoque, ";
            $cClausula .= " T129_C004_Id, ";
            $cClausula .= " T129_T189_Id ";
            $cClausula .= " ) values (";
            $cClausula .= " '$T128_Id',";
            $cClausula .= " '$mD001[T189_Quantidade]',";
            $cClausula .= " '$mD001[D001_Codigo_Produto]',";
            $cClausula .= " '$mD001[D009_Id]',";
            $cClausula .= " '$mD001[D001_Descricao_Produto]',";
            $cClausula .= " '0',";
            $cClausula .= " '{$mD001['T189_T006_Id']}',";
            $cClausula .= " '{$mD001['D009_Quantidade_Estoque_Liquido']}',";
            $cClausula .= " '{$g['empresaAtual']}',";
            $cClausula .= " '{$mD001['T189_Id']}' ";
            $cClausula .= " )";
            mysql_query($cClausula);
            
            mysql_query("CALL T129_Gravar_Totalizacao('{$g['mysqlLastId']}')");
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: {$cErro} Clausula: {$cClausula}";
                exit;
            }
            
            $cClausula = " update T189 set T189_Flag_Produzir='N' where T189_Id='$mD001[T189_Id]'";
            mysql_query($cClausula);
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: {$cErro} Clausula: {$cClausula}";
                exit;
            }
        }
        mysql_query("CALL T128_Gravar_Totalizacao('{$T128_Id}')");
        mysql_query("COMMIT");
        return true;
    }
}
