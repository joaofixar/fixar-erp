<?php
namespace hardness;

definirClasse('EST014');
class EST014_
{
    /**
     * updateCheckboxGerarCotar
     *
     * @param int $T189_Id
     * @param string $selected
     */
    public function updateCheckboxGerarCotar($T189_Id, $selected)
    {
        global $g;
        
        $T189_Id  = mysql_real_escape_string($T189_Id);
        $selected = mysql_real_escape_string($selected);
        
        if ($selected == "S") {
            $id_User = $g['usuarioAtual'];
        } else {
            $id_User = "";
        }
        mysql_query("update T189 set T189_Flag_Cotar='$selected', T189_C007_Id_Cotar='{$id_User}' where T189_Id='$T189_Id'");
        return true;
    }
    
    /**
     * updateCheckboxGerarComprar
     *
     * @param int $T189_Id
     * @param string $selected
     */
    public function updateCheckboxGerarComprar($T189_Id, $selected)
    {
        global $g;
        
        $T189_Id  = mysql_real_escape_string($T189_Id);
        $selected = mysql_real_escape_string($selected);
        
        if ($selected == "S") {
            $id_User = $g['usuarioAtual'];
        } else {
            $id_User = "";
        }
        mysql_query("update T189 set T189_Flag_Comprar='$selected', T189_C007_Id_Comprar='{$id_User}' where T189_Id='$T189_Id'");
        return true;
    }
    
    /**
     * updateCheckboxGerarProducao
     *
     * @param int $T189_Id
     * @param string $selected
     */
    public function updateCheckboxGerarProducao($T189_Id, $selected)
    {
        global $g;
        
        $T189_Id  = mysql_real_escape_string($T189_Id);
        $selected = mysql_real_escape_string($selected);
        
        if ($selected == "S") {
            $id_User = $g['usuarioAtual'];
        } else {
            $id_User = "";
        }
        mysql_query("update T189 set T189_Flag_Produzir='$selected', T189_C007_Id_Comprar='{$id_User}' where T189_Id='$T189_Id'");
        return true;
    }
    
    /**
     * est014AplicarIPV
     *
     * @param int $T174_Id
     * @param string $campoEditado
     * @param string $valorAntigo
     * @param string $novoValor
     */
    public function est014AplicarIPV($T174_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        $sqlT174    = <<<EOT
		SELECT * FROM T174 WHERE T174_Id='{$T174_Id}'
EOT;
        $novoValor  = gCorrigeNumeroInverte($novoValor);
        $resultT174 = mysql_query($sqlT174);
        $linhaT174  = mysql_fetch_array($resultT174);
        if ($campoEditado == "T174_IPV") {
            $valor  = $novoValor * $linhaT174['T174_Valor_Custo_Unitario'];
            $upT174 = "UPDATE T174 SET T174_Preco_Venda='{$valor}' WHERE T174_Id='{$T174_Id}'";
            mysql_query($upT174);
            return true;
        } else if ($campoEditado == "T174_Preco_Venda") {
            $valor  = ($novoValor / $linhaT174['T174_Valor_Custo_Unitario']);
            $upT174 = "UPDATE T174 SET T174_IPV='{$valor}' WHERE T174_Id='{$T174_Id}'";
            mysql_query($upT174);
            return true;
        } else if ($campoEditado == "T174_Valor_Custo_Unitario") {
            if ($linhaT174['T174_IPV'] > 0) {
                $valor  = $novoValor * $linhaT174['T174_IPV'];
                $upT174 = "UPDATE T174 SET T174_Preco_Venda='{$valor}' WHERE T174_Id='{$T174_Id}'";
                mysql_query($upT174);
                return true;
            } else {
                return true;
            }
        }
    }
    
    /**
     * est014associarProduto
     *
     * @param Form $form
     */
    public function est014associarProduto($form)
    {
        $D009_Id                = $form->campoValorEnviado('D009_Id');
        $T189_Id                = $form->campoValorEnviado('T189_Id');
        $D001_Descricao_Produto = $form->campoValorEnviado('D001_Descricao_Produto');
        
        // echo "D009: ".$D009_Id."<br />T189: ".$T189_Id."<br />Desc: ".$D001_Descricao_Produto;
        // die();
        
        $sqlT189 = "UPDATE T189 SET T189_D009_Id='{$D009_Id}', T189_Descricao_Produto='{$D001_Descricao_Produto}' WHERE T189_Id='{$T189_Id}'";
        $result  = mysql_query($sqlT189);
        if ($result) {
            return true;
        } else {
            return mysql_error();
        }
    }
    
    /**
     * EST014montarPdfGridRequisicoes - Gera o PDF do Grid de Requisicoes a partir da sql passada por parametro (Tablet)
     *
     * @param string $sqlT189
     */
    public function EST014montarPdfGridRequisicoes($sqlT189)
    {
        global $g, $confUsuario;
        
        $resultT189 = mysql_query($sqlT189);
        
        
        $sqlC004    = "SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $C004       = mysql_fetch_array($resultC004);
        
        $sqlC007    = "SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
        $resultC007 = mysql_query($sqlC007);
        $C007       = mysql_fetch_array($resultC007);
        
        //$layout = $g['conf']->ini('layoutOrcamentoEmailPdf');
        
        // Array de retorno para criação do e-mail
        $array            = array();
        $array['Para']    = "";
        $array['Assunto'] = "Requisições de Compra";
        
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
        $pdf->setPageOrientation('P');
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
				<td align="right" style="font-size: 230%;"><b>Requisições de Compra</b></td>
			</tr>
		</table>
EOT;
        
        /**
         * PDF - TABELA REQUISIÇÕES
         */
        if ($C007['C007_Flag_Vendedor'] == 'D' OR $C007['C007_Flag_Vendedor'] == 'S') {
            $html .= <<<EOT
			<div style="height:20px; width:100%;"></div>
					<table class="itens" cellspacing="0" cellpadding="2">
						<tr class="titulo">
							<td style="width: 8%">Data</td>
    						<td style="width: 65%" align="left">Descrição Produto</td>
    						<td style="width: 7%;padding:0px 12px 0px 0px;" align="right">Qnt</td>
    						<td style="width: 10%">Tipo</td>
    						<td style="width: 10%">Usuário</td>
    					</tr>
    				</table>
EOT;
        } else {
            $html .= <<<EOT
			<div style="height:20px; width:100%;"></div>
					<table class="itens" cellspacing="0" cellpadding="2">
						<tr class="titulo">
							<td style="width: 8%">Data</td>
    						<td style="width: 75%" align="left">Descrição Produto</td>
    						<td style="width: 7%;padding:0px 12px 0px 0px;" align="right">Qnt</td>
    						<td style="width: 10%">Tipo</td>
    					</tr>
    				</table>
EOT;
        }
        $html .= <<<EOT
		<table class="itens" cellspacing="0" cellpadding="2">
EOT;
        while ($rowT189 = mysql_fetch_array($resultT189)) {
            $rowT189['T189_Data_Criado'] = gCorrigeData($rowT189['T189_Data_Criado']);
            
            if ($rowT189['T189_Flag_Tipo'] == 'C') {
                $rowT189['T189_Flag_Tipo'] = "Cotar";
            } else if ($rowT189['T189_Flag_Tipo'] == 'O') {
                $rowT189['T189_Flag_Tipo'] = "Orçamento";
            } else if ($rowT189['T189_Flag_Tipo'] == 'P') {
                $rowT189['T189_Flag_Tipo'] = "Pedido";
            } else if ($rowT189['T189_Flag_Tipo'] == 'E') {
                $rowT189['T189_Flag_Tipo'] = "Reposição";
            } else if ($rowT189['T189_Flag_Tipo'] == 'R') {
                $rowT189['T189_Flag_Tipo'] = "Cancelado";
            }
            $rowT189['T189_Quantidade'] = number_format($rowT189['T189_Quantidade'], 2, ',', ' ');
            
            //Verificação Se Item já foi cotado
            $sqlT059 = <<<EOT
			SELECT T174_Id,
			       D024_Nome_Empresa,
			       T174_Valor_Custo_Unitario,
			       T174_IPV,
			       T174_Flag_Indisponivel,
			       T174_Prazo_Entrega_Dias,
			       T174_Preco_Venda,
			       T174_Observacao,
			       T189_Descricao_Produto,
			       T059_Descricao_Produto,
			       T011_Id,
			       T011_Prazo_Entrega
			  FROM T174
		 LEFT JOIN T173 ON T173_Id=T174_T173_Id
		 LEFT JOIN D024 ON D024_Id=T173_D024_Id 
		 LEFT JOIN T010 ON T010_Id=T173_T010_Id 
		 LEFT JOIN T059 ON T059_Id=T174_T059_Id
		 LEFT JOIN T189 ON T189_Id=T059_T189_Id
		 LEFT JOIN T012 ON T012_T174_Id=T174_Id or T012_T189_Id=T189_Id 
		 LEFT JOIN T011 ON T011_Id=T012_T011_Id
			 WHERE T059_T189_Id='{$rowT189['T189_Id']}'
			 AND T174_Flag_Excluido='N'
EOT;
            $resT059 = mysql_query($sqlT059);
            echo mysql_error();
            $rowsT059 = mysql_numrows($resT059);
            
            //Verificação Se Item já foi para ordem de compra
            $sqlT012  = <<<EOT
			SELECT T012_Id FROM T012 WHERE T012_T189_Id='{$rowT189['T189_Id']}'
EOT;
            $resT012  = mysql_query($sqlT012);
            $rowsT012 = mysql_numrows($resT012);
            $retorno  = '';
            if ($rowsT059 > 0) {
                while ($mT059 = mysql_fetch_array($resT059)) {
                    $mT059['T059_Descricao_Produto'] = str_replace(trim($mT059['T189_Descricao_Produto']), '', trim($mT059['T059_Descricao_Produto']));
                    $mT059['D024_Nome_Empresa']      = substr($mT059['D024_Nome_Empresa'], 0, 10);
                    if ($mT059[T174_Flag_Indisponivel] == 'S') {
                        $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</i></font><font color='red'><i> INDISPONÍVEL </i></font>";
                    } else if ($mT059[T174_Valor_Custo_Unitario] > 0) {
                        if ($C007['C007_Flag_Vendedor'] == 'D' OR $C007['C007_Flag_Vendedor'] == 'S') {
                            $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</i></font><font color='green'><i> R$ " . number_format($mT059[T174_Valor_Custo_Unitario], 2, ',', '.') . " x " . number_format($mT059[T174_IPV], 2, ',', '.') . " = R$ " . number_format($mT059[T174_Preco_Venda], 2, ',', '.') . "</i></font>";
                        } else {
                            $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</i></font><font color='green'><i> R$ " . number_format($mT059[T174_Preco_Venda], 2, ',', '.') . "</i></font>";
                        }
                        if (!empty($mT059[T174_Prazo_Entrega_Dias])) {
                            $retorno .= " <font color='blue' style='font-weight:normal'>Ent:</font> <font color='green'>{$mT059['T174_Prazo_Entrega_Dias']}</font>";
                        }
                    } else {
                        $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='blue'> COTANDO </font>";
                    }
                    if (!empty($mT059[T174_Observacao])) {
                        $retorno .= " <font color='#D96C00' style='font-weight:normal'><i>{$mT059['T174_Observacao']}</i></font>";
                    }
                    if (!empty($mT059[T011_Id])) {
                        $retorno .= " <font color='#880088' style='font-weight:bold'>OC {$mT059['T011_Id']} {$mT059['T011_Prazo_Entrega']}</font>";
                    }
                }
            } else if ($rowsT012 > 0) {
                $retorno = " - <font color='blue'>(OC)</font>";
            } else {
                $retorno = "<br><font color='#585858' style='font-weight:normal'><i>Aguardando comprador...</i></font>";
            }
            //Busca observacao do cabecalho da cotacao
            $sqlT010 = <<<EOT
				SELECT T010_Observacao
				  FROM T189
			 LEFT JOIN T059 ON T059_T189_Id=T189_Id
			 LEFT JOIN T010 ON T010_Id=T059_T010_Id
				 WHERE T059_T189_Id='{$rowT189['T189_Id']}'
		      GROUP BY T189_Id
EOT;
            $resT010 = mysql_query($sqlT010);
            echo mysql_error();
            $rowsT010 = mysql_fetch_array($resT010);
            if (!empty($rowsT010["T010_Observacao"])) {
                $retorno .= "<br><font color='#D96C00' style='font-weight:normal'><i>{$rowsT010['T010_Observacao']}</i></font>";
            }
            
            if ($C007['C007_Flag_Vendedor'] == 'D' OR $C007['C007_Flag_Vendedor'] == 'S') {
                $html .= <<<EOT
			
				<tr>
    				<td style="width: 8%;white-space:normal"; valign=top>{$rowT189['T189_Data_Criado']}</td>
    				<td style="width: 65%;white-space:normal"; align="left">
						{$rowT189['T189_Descricao_Produto']}{$retorno}
    					<p style="width: 100%;white-space:normal;font-weight:normal;font-size:14px;"; align="right"><i>{$rowT189['T189_Detalhes']}</i></p>
    				</td>
    				<td style="width: 7%;padding:0px 15px 0px 0px;" align="right">{$rowT189['T189_Quantidade']}</td>
    				<td style="width: 10%;white-space:normal";>{$rowT189['T189_Flag_Tipo']}</td>
    				<td style="width: 10%;white-space:normal";>{$rowT189['C007_Nome']}</td>
    			</tr>
				
EOT;
            } else {
                $html .= <<<EOT
			
				<tr>
    				<td style="width: 8%;white-space:normal" valign=top>{$rowT189['T189_Data_Criado']}</td>
    				<td style="width: 75%;white-space:normal" align="left">
						
						{$rowT189['T189_Descricao_Produto']}{$retorno}
    					<p style="width: 100%;white-space:normal;font-weight:normal;font-size:14px;"; align="right"><i>{$rowT189['T189_Detalhes']}</i></p></td>
    				<td style="width: 7%;padding:0px 15px 0px 0px;" align="right">{$rowT189['T189_Quantidade']}</td>
    				<td style="width: 10%;white-space:normal">{$rowT189['T189_Flag_Tipo']}</td>
    			</tr>

				
EOT;
            }
            
        }
        $html .= '</table>';
        
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
        $anexoNome = "{$anexoDir}requisicoesCompra.pdf";
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
        $array['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br /><font style='font-size:16px'>Lista de Requisições de Compra em anexo.</font><br /><br /><br />";
        
        return $array;
    }
    
    /**
     * est014OSProcessarEstoque
     *
     * @param int $T128_Id
     */
    public function est014OSProcessarEstoque($T128_Id)
    {
        global $g;
        $Empresa_Atual = $g['empresaAtual'];
        
        $T128  = mysql_query("SELECT T128_Flag_Estoque_Processado FROM T128 WHERE T128_Id='{$T128_Id}'");
        $mT128 = mysql_fetch_array($T128);
        
        if ($mT128['T128_Flag_Estoque_Processado'] == 'S') {
            return "Este processo já foi executado!";
        }
        
        $cSql_T129 = mysql_query("select *
				from T129
				left join D049 on D049_Id=T129_D049_Id
				left join D009 on D009_D049_Id=D049_Id and D009_C004_Id='$Empresa_Atual'					
				left join D001 on D001_Id=D049_D001_Id
				left join D002 on D002_Id=D001_D002_Id
				left join T128 on T128_Id=T129_T128_Id
				where T129_T128_Id='{$T128_Id}'");
        
        $cSql_D001 = mysql_query("SELECT * FROM T129 
								LEFT JOIN D009 ON D009_Id=T129_D009_Id
								LEFT JOIN D049 ON D049_Id=D009_D049_Id
								LEFT JOIN D001 ON D001_Id=D049_D001_Id
								LEFT JOIN T128 ON T128_Id=T129_T128_Id
								WHERE T129_T128_Id='{$T128_Id}'");
        
        while ($mT129 = mysql_fetch_array($cSql_D001)) {
            
            $D009  = mysql_query("SELECT * FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D001_Id='{$mT129['D001_D001_Id']}' AND D009_C004_Id='{$g['empresaAtual']}'");
            $mD009 = mysql_fetch_array($D009);
            
            $cSql_T066 = mysql_query("select T066_Id from T066 where T066_D048_Id='1' and T066_D009_Id='{$mT129['T129_D009_Id']}'");
            $mT066     = mysql_fetch_array($cSql_T066);
            
            // ATUALIZA O HISTORICO DO PRODUTO
            $tmpD004_Id = getD004($mT066[T066_Id]);
            mysql_query("insert into T001 (
					T001_D024_Id,
					T001_D024_Id_Cliente,
					T001_D009_Id,
					T001_C004_Id,
					T001_T129_Id,
					T001_T066_Id,
					T001_Codigo_Produto,
					T001_Data_Lancamento,
					T001_Descricao_Operacao,
					T001_Quantidade,
					T001_Flag_Operacao,
					T001_Flag_Atualiza_Custo,
					T001_Valor_Preco_Unitario,
					T001_Valor_Custo_Unitario,
					T001_Quantidade_Saldo,
					T001_Flag_MMF,
					T001_D004_Id
			) values (
					'$mT129[T128_D024_Id]',
					'$mT129[T128_D024_Id]',
					'$mD009[D009_Id]',
					'$Empresa_Atual',
					'$mT129[T129_Id]',
					'$mT066[T066_Id]',
					'$mT129[D001_Codigo_Produto]',
					current_date(),
					'OS-$T128_Id',
					'$mT129[T129_Quantidade]',
					'S',
					'N',
					'0',
					'0',
					'0',
					'S',
					'{$tmpD004_Id}')");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("rollback");
                return "Erro: $cErro";
                exit;
            }
            $lastT001_Id = $g['mysqlLastId'];
            
            // Adiciona um registro para cada T066 (locação)
            if ($lastT001_Id > 0) {
                
                $sqlT201 = "SELECT * FROM T201 WHERE T201_T129_Id = '{$mT129['T129_Id']}';";
                $tmpT201 = mysql_query($sqlT201);
                while ($rowT201 = mysql_fetch_assoc($tmpT201)) {
                    if ($rowT201['T201_Qtd_Estoque'] > 0) {
                        $sqlT186Insert = "INSERT INTO T186 (T186_T001_Id, T186_T066_Id, T186_Quantidade, T186_Quantidade_Saldo) VALUES ('{$lastT001_Id}', '{$rowT201['T201_T066_Id']}', '{$rowT201['T201_Qtd_Estoque']}', '0');";
                        mysql_query($sqlT186Insert);
                    }
                }
                require_once("bibliotecas/classes/CAD002.php");
                $CAD002 = new CAD002();
                
                $retorno = $CAD002->D001_reprocessa_historico(false, $mD009['D009_Id']);
                if ($retorno != true) {
                    mysql_query("ROLLBACK");
                    return $retorno;
                }
            }
        }
        mysql_query("UPDATE T128 SET T128_Flag_Estoque_Processado='S' WHERE T128_Id='{$T128_Id}'");
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * est014OSGerarNF
     *
     * @param int $T128_Id
     * @param string $Tipo_Documento
     */
    public function est014OSGerarNF($T128_Id, $Tipo_Documento)
    {
        
        global $g;
        
        $Empresa_Atual = $g['empresaAtual'];
        
        $T128  = mysql_query("SELECT T128_Flag_Estoque_Processado,T128_D006_Id FROM T128 WHERE T128_Id='{$T128_Id}'");
        $mT128 = mysql_fetch_array($T128);
        
        if ($mT128['T128_Flag_Estoque_Processado'] == 'N') {
            return "É necessário primeiro processar o estoque para depois fazer esta ação.";
        }
        
        $cClausula = " select * from C020 where C020_C004_Id='{$Empresa_Atual}";
        $cSql_C020 = mysql_query($cClausula);
        $mConfig   = mysql_fetch_array($cSql_C020);
        
        $D048_Id           = 1;
        $mTipoDocumento[1] = "NOTA FISCAL";
        $mTipoDocumento[3] = "ACP";
        $mTipoDocumento[4] = "CUPOM FISCAL";
        $mTipoDocumento[5] = "NOTA FISCAL D1";
        $mTipoDocumento[6] = "NOTA FISCAL ELETRÔNICA";
        
        if (empty($T128_Id)) {
            return 'Não encontrado o Id da OS (T128_ID)';
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // SO IRA GERAR A NOTA SE A FILA DE IMPRESSAO ESTIVER VAZIA
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sql = mysql_query("select T007_Id from T007 where T007_Flag_Impresso='N' and T007_Flag_Almoxarifado='S' and T007_Flag_ACP!=6 and T007_Numero_Nota_Fiscal!='' and T007_Numero_Nota_Fiscal is not null and T007_C004_Id='$Empresa_Atual'");
        if (mysql_num_rows($sql) > 0) {
            //return 'Existem notas na fila de impressão, Não é possivel emitir as notas enquanto existirem pendencias na fila de impressão, Por Favor imprima as notas pendentes!';
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // VERIFICA SE JÁ EXISTE NF EMITIDA PARA O PEDIDO
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sql = mysql_query("select T007_Numero_Nota_Fiscal,T007_Data_Emissao from T007 where T007_T128_Id='$T128_Id' and T007_Flag_Cancelada!='S'");
        if (mysql_num_rows($sql) > 0) {
            $cMsg_Faturado = "A NF para esta OS já foi gerada: ";
            while ($mT007 = mysql_fetch_array($sql)) {
                $mT007[T007_Data_Emissao] = gCorrigeDataInverte($mT007[T007_Data_Emissao]);
                $cMsg_Faturado .= "NF: $mT007[T007_Numero_Nota_Fiscal] no dia $mT007[T007_Data_Emissao], ";
            }
            return $cMsg_Faturado;
            
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // CONSULTA O PEDIDO
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $cSql_T128 = mysql_query("select *
							from T128
						left join D024 on D024_Id=T128_D024_Id
						LEFT JOIN D022 ON D022_Id=T128_D022_Id
						LEFT JOIN D018 ON D018_Id=D024_D018_Id
						LEFT JOIN D020 ON D020_Id=D024_D020_Id
							where T128_Id='$T128_Id'");
        
        $mT128 = mysql_fetch_array($cSql_T128);
        
        //empresa usuaria
        $C004  = mysql_query("select * from C004 left join D020 on D020_Id=C004_D020_Id left join D018 on D018_Id=C004_D018_Id where C004_Id = '$mT128[T128_C004_Id]'");
        $mC004 = mysql_fetch_array($C004);
        
        //cliente
        $D024  = mysql_query("select * 
							from D024 
						left join D020 on D020_Id=D024_D020_Id 
						left join D018 on D018_Id=D020_D018_Id
						left join D030 on D030_Id=D024_D030_Id 
						where  D024_Id='$mT128[T128_D024_Id]'");
        $mD024 = mysql_fetch_array($D024);
        
        if (($mD024['D030_Sigla_Pais'] == 'BR') and ($mD024['D018_UF'] == 'EX')) {
            return 'UF do cliente esta definido como EX, Verifique o cadastro do cliente!';
        }
        
        //cfop
        $D006  = mysql_query("select * from D006 WHERE D006_Id='{$mT128['T128_D006_Id']}'");
        $mD006 = mysql_fetch_array($D006);
        
        $Observacao_Fiscal = $mD006['D006_Observacao'];
        
        if (empty($Observacao_Fiscal)) {
            return "A CFOP {$mD006['D006_Descricao_Comercial']} está sem Observação Fiscal, verifique";
        }
        
        //tipo de transporte
        $D036  = mysql_query("select * from D036 where D036_Id='$mT128[T128_D036_Id]'");
        $mD036 = mysql_fetch_array($D036);
        
        ////////////////////////////////////////////////////////////////////////
        if ($mD024['D020_Id'] == '0') {
            return 'Fornecedor não tem Cidade cadastrada';
        }
        if ($mD024['D018_Id'] == '0') {
            return 'Fornecedor não tem Estado cadastrado';
        }
        
        if (($mD006['D006_Flag_MMF'] != 'N') and ($mD006['D006_Flag_MMF'] != 'S')) {
            return "O campo: D006_Flag_MMF ({$mD006['D006_Flag_MMF']}) está incorreto,, Informe o codigo abaixo ao suporte tecnico!, D006_Id: {$mD006['D006_Id']}";
        }
        
        if (($mD006['D006_Flag_Atualiza_Custo'] != 'N') and ($mD006['D006_Flag_Atualiza_Custo'] != 'S')) {
            return "O campo: D006_Flag_Atualiza_Custo ({$mD006['D006_Flag_Atualiza_Custo']}) está incorreto,, Informe o codigo abaixo ao suporte tecnico!, D006_Id: {$mD006['D006_Id']}";
        }
        
        
        //-- OBSERVACAO: DESENVOLVER SCRIPT PARA MUDAR O NUMERO DE SERIE CASO O TOTAL DE NOTAS ULTRAPASSE 999.999.999 (nossa!!!)
        if ($mConfig['C020_NFe_SCAN_Ativado'] == 'S') {
            $nNumero_Serie = 900;
        } else {
            $nNumero_Serie = 1;
        }
        
        if (($Tipo_Documento == 1) or ($Tipo_Documento == 4) or ($Tipo_Documento == 5) or ($Tipo_Documento == 6)) {
            
            if ($Tipo_Documento == 1) {
                $notaf = mysql_query("select T007_Numero_Nota_Fiscal
		    							from T007
		     		 		           where T007_Flag_ACP='1'
		    							 and T007_C004_Id='{$mT128['T128_C004_Id']}'
		    						order by year(T007_Data_Emissao) desc,round(T007_Numero_Nota_Fiscal) desc limit 0,1;");
                $mT007 = mysql_fetch_array($notaf);
                
                $mT007['T007_Numero_Nota_Fiscal']++;
                
                if (number_format($mT007['T007_Numero_Nota_Fiscal'], 0) != number_format($mC004['C004_Ultimo_Numero_Nota_Fiscal'], 0) and (empty($mC004['C004_Ultimo_Numero_Nota_Fiscal']) or mysql_num_rows($notaf) > 0)) {
                    return "Nímero proxima nota não confere ({$mC004['C004_Ultimo_Numero_Nota_Fiscal']}<>{$mT007['T007_Numero_Nota_Fiscal']})";
                }
                $Proximo_Numero = $mC004['C004_Ultimo_Numero_Nota_Fiscal'];
                
            } else if ($Tipo_Documento == 6) {
                $notaf = mysql_query("select T007_Numero_Nota_Fiscal
		    							from T007
		     		 		           where T007_Flag_ACP='6'
		    							 and T007_C004_Id='{$mT128['T128_C004_Id']}'
		    							 and T007_Serie='$nNumero_Serie'
		    						order by year(T007_Data_Emissao) desc,round(T007_Numero_Nota_Fiscal) desc limit 0,1;");
                $mT007 = mysql_fetch_array($notaf);
                
                $mT007['T007_Numero_Nota_Fiscal']++;
                
                //return "T007_Numero_Nota_Fiscal: ".number_format($mT007['T007_Numero_Nota_Fiscal'],0)." C004_Ultimo_Numero_Nota_Fiscal: ".number_format($mC004['C004_Ultimo_Numero_Nota_Fiscal_Eletronica'],0);
                
                if (number_format($mT007['T007_Numero_Nota_Fiscal'], 0) != number_format($mC004['C004_Ultimo_Numero_Nota_Fiscal_Eletronica'], 0) and (empty($mC004['C004_Ultimo_Numero_Nota_Fiscal_Eletronica']) or mysql_num_rows($notaf) > 0)) {
                    return "Número próxima nota nao confere ('C004_Ultimo_Numero_Nota_Fiscal_Eletronica: {$mC004['C004_Ultimo_Numero_Nota_Fiscal_Eletronica']}<> T007_Numero_Nota_Fiscal:{$mT007['T007_Numero_Nota_Fiscal']})";
                }
                $Proximo_Numero = $mC004['C004_Ultimo_Numero_Nota_Fiscal_Eletronica'];
                
            } else if ($Tipo_Documento == 4) {
                /*    	$notaf=mysql_query("select T007_Numero_Nota_Fiscal
                from T007
                where T007_Flag_ACP='4'
                and T007_C004_Id='$mT128[T128_C004_Id]'
                order by year(T007_Data_Emissao) desc,round(T007_Numero_Nota_Fiscal) desc limit 0,1;");
                $mT007=mysql_fetch_array($notaf);
                
                if (number_format($mT007[T007_Numero_Nota_Fiscal],0)!=number_format($mC004[C004_Ultimo_Numero_Cupom_Fiscal],0)) {
                echo "Carregar_Formulario_Informe_Nota('$mT128[T128_Id]');";
                
                }
                $Proximo_Numero=$mC004[C004_Ultimo_Numero_Nota_Fiscal]; */
                
            }
            
        } else if ($Tipo_Documento == 3) {
            
            $notaf = mysql_query("select T007_Numero_Nota_Fiscal
									from T007
							where T007_Flag_ACP='3'
								and T007_C004_Id='{$mT128['T128_C004_Id']}'
						order by year(T007_Data_Emissao)desc, round(Substring(T007_Numero_Nota_Fiscal,2,12)) desc limit 0,1;");
            $mT007 = mysql_fetch_array($notaf);
            
            $mT007['T007_Numero_Nota_Fiscal'] = substr($mT007['T007_Numero_Nota_Fiscal'], 1, 12) + 1;
            $mC004['C004_Ultimo_Numero_ACP']  = substr($mC004['C004_Ultimo_Numero_ACP'], 1, 12);
            
            if ($mT007['T007_Numero_Nota_Fiscal'] != $mC004['C004_Ultimo_Numero_ACP'] and (empty($mC004['C004_Ultimo_Numero_ACP']) or mysql_num_rows($notaf) > 0)) {
                
                
                //echo "parent.Carregar_Formulario_Informe_Nota('$mT128[T128_Id]');";
            }
            
            $Proximo_Numero = $mC004['C004_Ultimo_Numero_ACP'];
            
            if (empty($Proximo_Numero)) {
                $Proximo_Numero = 1;
            }
            
        } else {
            return "Erro, documento nao identificado, Tipo_Documento={$Tipo_Documento}.";
        }
        
        // Busca os dados do usuario
        $resultado      = mysql_query("select * from C007 where C007_Id='{$g['usuarioAtual']}'");
        $mUsuario       = mysql_fetch_array($resultado);
        $Perfil_Usuario = $mUsuario['C007_Id_Perfil'];
        //-- VERIFICA SE O USUARIO TEM ACESSO A OPCAO DE EMISSAO DE NOTA DE EXPOSICAO
        $cSql_C010      = mysql_query("select * from C010 where C010_C009_Id=13 and C010_Id_Perfil='{$Perfil_Usuario}'");
        if (mysql_num_rows($cSql_C010) == 0) {
            if ($mC004['C004_D024_Id'] == $mT128['T128_D024_Id']) {
                return "Não é possivel gerar esta " . $mTipoDocumento[$Tipo_Documento] . ", pois o Cliente é o mesmo que a empresa atual!";
            }
        }
        
        
        if ($mT128['T128_Valor_Entrada'] > 0) {
            $Total_Duplicatas++;
        }
        
        if (($mT128['T128_Valor_Total'] > 0) and ($mT128['T128_Valor_Entrada'] != $mT128['T128_Valor_Total'])) {
            $Total_Duplicatas += $mT128['T128_Numero_Parcelas'];
        }
        
        
        if ($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
            $D007_Cliente_CNPJ_CPF = "$mD024[D024_Cnpj]";
        } elseif ($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'I') {
            $D007_Cliente_CNPJ_CPF = "$mC004[C004_Cnpj]";
        } else {
            $D007_Cliente_CNPJ_CPF = "$mD024[D024_Cpf]";
        }
        
        $mSql_T129 = mysql_query("select T129_Id from T129 where T129_T128_Id='$mT128[T128_Id]' and T129_D009_Id=0");
        if (mysql_num_rows($mSql_T129) > 0) {
            
            return "Existem itens sem D009_Id, informe o suporte técnico";
        }
        
        $cSql_T129 = mysql_query("select T129_Id from T129 where T129_T128_Id='$mT128[T128_Id]' and T129_Quantidade_Separacao>0");
        if (mysql_num_rows($cSql_T129) == 0) {
            
            //return "Informe um item com quantidade separada";
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////
        // GERANDO A NF
        /////////////////////////////////////////////////////////////////////////////////////////
        
        
        mysql_query("LOCK T007");
        mysql_query("LOCK T008");
        mysql_query("LOCK T001");
        mysql_query("LOCK T002");
        mysql_query("LOCK T015");
        mysql_query("LOCK C004");
        mysql_query("START TRANSACTION");
        
        ///////////////////////////////////////////////////////////////////////
        // ENCONTRA O NUMERO DA PROXIMA NOTA FISCAL OU ACP
        if ($Tipo_Documento == 3) {
            $mT007['Proximo_Numero'] = "A" . $Proximo_Numero;
        } else {
            $mT007['Proximo_Numero'] = $Proximo_Numero;
        }
        
        if (empty($mT007['Proximo_Numero'])) {
            return "Numero da NF está em branco";
            mysql_query("ROLLBACK");
        }
        
        ///////////////////////////////////////////////////////////////////////
        // INCLUSAO DO CABECALHO DA NOTA
        $mD024['D020_Nome_Cidade']  = addslashes($mD024['D020_Nome_Cidade']);
        $mD024['D024_Bairro']       = addslashes($mD024['D024_Bairro']);
        $mD024['D024_Nome_Empresa'] = addslashes($mD024['D024_Nome_Empresa']);
        $mD024['D024_Endereco']     = addslashes($mD024['D024_Endereco']);
        
        /*
         *	and T007_C004_Id={$g['empresaAtual']}
         and T007_Numero_Nota_Fiscal!=''
         and T007_Numero_Nota_Fiscal is not null
         and T007_Flag_Cancelada='N'
         and T007_Flag_Almoxarifado='S'
         and (T007_Numero_Recibo_Nfe is null or T007_Numero_Protocolo_Nfe is null)
         */
        
        $cClausula = " insert into T007 ";
        $cClausula .= " (T007_T128_Id, ";
        $cClausula .= " T007_D024_Id, ";
        $cClausula .= " T007_D022_Id, ";
        $cClausula .= " T007_D021_Id, ";
        $cClausula .= " T007_D006_Id, ";
        $cClausula .= " T007_C004_Id, ";
        $cClausula .= " T007_D027_Id, ";
        $cClausula .= " T007_D027_Id_Entrada, ";
        $cClausula .= " T007_Versao_NFe, ";
        $cClausula .= " T007_Empresa_Usuaria_CRT, ";
        $cClausula .= " T007_Origem_UF, ";
        $cClausula .= " T007_Origem_Ano_Mes, ";
        $cClausula .= " T007_Origem_Cnpj, ";
        $cClausula .= " T007_Origem_Modelo, ";
        $cClausula .= " T007_Origem_Serie, ";
        $cClausula .= " T007_Origem_Chave_Acesso, ";
        $cClausula .= " T007_Flag_Almoxarifado, ";
        $cClausula .= " T007_Flag_Cancelada, ";
        $cClausula .= " T007_Flag_ACP, ";
        $cClausula .= " T007_Flag_SCAN, ";
        $cClausula .= " T007_Numero_Nota_Fiscal, ";
        $cClausula .= " T007_Ambiente_NFe, ";
        $cClausula .= " T007_Serie, ";
        $cClausula .= " T007_Numero_Nota_Devolvida, ";
        $cClausula .= " T007_Data_Emissao, ";
        $cClausula .= " T007_Data_Saida, ";
        $cClausula .= " T007_Data_Vencimento, ";
        $cClausula .= " T007_Numero_Total_Parcelas, ";
        $cClausula .= " T007_Codigo_CFOP, ";
        $cClausula .= " T007_Descricao_CFOP, ";
        $cClausula .= " T007_Aliquota_ICMS, ";
        $cClausula .= " T007_Nome_Transportadora, ";
        $cClausula .= " T007_Tipo_Transporte, ";
        $cClausula .= " T007_Observacao_1, ";
        $cClausula .= " T007_Flag_Frete, ";
        $cClausula .= " T007_Flag_IPI, ";
        $cClausula .= " T007_Valor_Mao_Obra, ";
        $cClausula .= " T007_Observacao_Nota_Fiscal_2, ";
        $cClausula .= " T007_Obs_Classificacao_Fiscal, ";
        $cClausula .= " T007_Valor_Frete, ";
        $cClausula .= " T007_Prazo_Parcelas, ";
        $cClausula .= " T007_Percentual_Comissao_Representante, ";
        $cClausula .= " T007_Valor_Despesas_Acessorias, ";
        $cClausula .= " T007_Valor_Desconto, ";
        $cClausula .= " T007_Observacao_Nota_Fiscal_1, ";
        $cClausula .= " T007_Codigo_Cliente, ";
        $cClausula .= " T007_Codigo_Natureza, ";
        $cClausula .= " T007_Codigo_Representante, ";
        $cClausula .= " T007_Codigo_Transportadora, ";
        $cClausula .= " T007_Numero_Pedido_Venda, ";
        $cClausula .= " T007_C007_Id_Vendedor_Interno, ";
        $cClausula .= " T007_C007_Id_Vendedor_Externo, ";
        $cClausula .= " T007_C007_Id_Devolucao,	 ";
        $cClausula .= " T007_Percentual_Reducao_ICMS, ";
        $cClausula .= " T007_Percentual_Reducao_IPI, ";
        $cClausula .= " T007_Aliquota_IPI, ";
        $cClausula .= " T007_Cliente_Razao, ";
        $cClausula .= " T007_Cliente_CNPJ_CPF, ";
        $cClausula .= " T007_Cliente_Inscricao_Suframa, ";
        $cClausula .= " T007_Cliente_Inscricao_Estadual, ";
        $cClausula .= " T007_Cliente_Endereco, ";
        $cClausula .= " T007_Cliente_Endereco_Numero, ";
        $cClausula .= " T007_Cliente_Bairro, ";
        $cClausula .= " T007_Cliente_CEP, ";
        $cClausula .= " T007_Cliente_Cidade, ";
        $cClausula .= " T007_Cliente_Codigo_Cidade_IBGE, ";
        $cClausula .= " T007_Cliente_Telefone, ";
        $cClausula .= " T007_Cliente_Estado, ";
        $cClausula .= " T007_Cliente_Codigo_Estado_IBGE, ";
        $cClausula .= " T007_Hora_Saida, ";
        $cClausula .= " T007_Transportadora_CNPJ, ";
        $cClausula .= " T007_Transportadora_Inscricao_Estadual, ";
        $cClausula .= " T007_Transportadora_Endereco, ";
        $cClausula .= " T007_Transportadora_Cidade, ";
        $cClausula .= " T007_Transportadora_Estado, ";
        $cClausula .= " T007_Transportadora_Placa_Veiculo, ";
        $cClausula .= " T007_Estado_Veiculo, ";
        $cClausula .= " T007_Quantidade_Volumes, ";
        $cClausula .= " T007_Especie, ";
        $cClausula .= " T007_Marca, ";
        $cClausula .= " T007_Numero_Caixas, ";
        $cClausula .= " T007_Peso_Bruto, ";
        $cClausula .= " T007_Peso_Liquido, ";
        $cClausula .= " T007_Mao_Obra_1, ";
        $cClausula .= " T007_Mao_Obra_2, ";
        $cClausula .= " T007_Mao_Obra_3, ";
        $cClausula .= " T007_Mao_Obra_4, ";
        $cClausula .= " T007_Inscricao_Municipal, ";
        $cClausula .= " T007_Aliquota_ISS, ";
        $cClausula .= " T007_Empresa_Usuaria_Nome, ";
        $cClausula .= " T007_Empresa_Usuaria_Endereco, ";
        $cClausula .= " T007_Empresa_Usuaria_Numero, ";
        $cClausula .= " T007_Empresa_Usuaria_Bairro, ";
        $cClausula .= " T007_Empresa_Usuaria_CEP, ";
        $cClausula .= " T007_Empresa_Usuaria_Cidade, ";
        $cClausula .= " T007_Empresa_Usuaria_Estado, ";
        $cClausula .= " T007_Empresa_Usuaria_Uf, ";
        $cClausula .= " T007_Empresa_Usuaria_Codigo_Cidade_IBGE, ";
        $cClausula .= " T007_Empresa_Usuaria_Codigo_Estado_IBGE, ";
        $cClausula .= " T007_Empresa_Usuaria_Pais, ";
        $cClausula .= " T007_Empresa_Usuaria_Telefone, ";
        $cClausula .= " T007_Empresa_Usuaria_Fax, ";
        $cClausula .= " T007_Empresa_Usuaria_CNPJ, ";
        $cClausula .= " T007_Empresa_Usuaria_Inscricao_Estadual, ";
        $cClausula .= " T007_Empresa_Usuaria_CNAE_Fiscal, ";
        $cClausula .= " T007_Flag_Impresso, ";
        $cClausula .= " T007_Percentual_Desconto, ";
        $cClausula .= " T007_Valor_Percentual_Desconto, ";
        $cClausula .= " T007_Valor_Total_Base_ICMS, ";
        $cClausula .= " T007_Valor_Total_ICMS, ";
        $cClausula .= " T007_Valor_Total_Base_IPI, ";
        $cClausula .= " T007_Valor_Total_IPI, ";
        $cClausula .= " T007_Valor_ISS, ";
        $cClausula .= " T007_Valor_Total_Custo, ";
        $cClausula .= " T007_Valor_Total_Produtos, ";
        $cClausula .= " T007_Valor_Total, ";
        $cClausula .= " T007_IPV, ";
        $cClausula .= " T007_IPV_Cliente, ";
        $cClausula .= " T007_Flag_Entrada_Saida, ";
        $cClausula .= " T007_Flag_Venda_Compra_Outros, ";
        $cClausula .= " T007_Flag_Expedicao, ";
        $cClausula .= " T007_Valor_Entrada, ";
        $cClausula .= " T007_Numero_Parcelas, ";
        $cClausula .= " T007_Numero_Dias_Entrada, ";
        $cClausula .= " T007_Numero_Dias, ";
        $cClausula .= " T007_Flag_Revenda_Consumidor, ";
        $cClausula .= " T007_Flag_Classificacao, ";
        $cClausula .= " T007_ST_ICMS_Interno, ";
        $cClausula .= " T007_ICMS_Inter_Estadual, ";
        $cClausula .= " T007_ST_VA, ";
        $cClausula .= " T007_Casas_Decimais_Quantidade ";
        $cClausula .= " ) values (";
        $cClausula .= " '{$mT128['T128_Id']}', ";
        $cClausula .= " '{$mT128['T128_D024_Id']}', ";
        $cClausula .= " '{$mT128['T128_D022_Id']}', ";
        $cClausula .= " '{$mT128['T128_D021_Id']}', ";
        $cClausula .= " '{$mD006['D006_Id']}', ";
        $cClausula .= " '{$mT128['T128_C004_Id']}', ";
        $cClausula .= " '{$mT128['T128_D027_Id']}', ";
        $cClausula .= " '{$mT128['T128_D027_Id_Entrada']}', ";
        //$cClausula .= " '{$mConfig['C020_Versao_NFe']}', ";
        $cClausula .= " '{$g['c004']['C004_Versao_NFe']}', ";
        $cClausula .= " '{$mC004['C004_Codigo_Regime_Tributario']}', ";
        $cClausula .= " '{$mT128['T128_Origem_UF']}', ";
        $cClausula .= " '{$mT128['T128_Origem_Ano_Mes']}', ";
        $cClausula .= " '{$mT128['T128_Origem_Cnpj']}', ";
        $cClausula .= " '{$mT128['T128_Origem_Modelo']}', ";
        $cClausula .= " '{$mT128['T128_Origem_Serie']}', ";
        $cClausula .= " '{$mT128['T128_Origem_Chave_Acesso']}', ";
        $cClausula .= " 'S', ";
        $cClausula .= " 'N', ";
        $cClausula .= " '{$Tipo_Documento}', ";
        $cClausula .= " '{$mConfig['C020_NFe_SCAN_Ativado']}', ";
        $cClausula .= " '{$mT007['Proximo_Numero']}', ";
        $cClausula .= " '{$mC004['C004_Ambiente_NFe']}', ";
        $cClausula .= " '{$nNumero_Serie}', ";
        $cClausula .= " '{$mT128['T128_Numero_NF_Devolucao']}', ";
        $cClausula .= " current_date(), ";
        $cClausula .= " current_date(), ";
        $cClausula .= " '', ";
        $cClausula .= " '{$Total_Duplicatas}', ";
        $cClausula .= " '{$mD006['D006_Codigo_CFOP']}', ";
        $cClausula .= " '{$mD006['D006_Descricao']}', ";
        $cClausula .= " '{$mT128['T128_Aliquota_ICMS']}', ";
        $cClausula .= " '{$mT128['D022_Nome_Empresa']}', ";
        $cClausula .= " '{$mD036['D036_Tipo_Transporte']}', ";
        $cClausula .= " '{$mT128['T128_Observacao_1']}', ";
        $cClausula .= " '{$mT128['T128_Flag_Frete']}', ";
        $cClausula .= " '', ";
        $cClausula .= " '{$mT128['T128_Valor_Mao_Obra']}', ";
        $cClausula .= " '{$mT128['T128_Observacao']}', ";
        $cClausula .= " '{$Obs_Classificacao_Fiscal}', ";
        $cClausula .= " '{$mT128['T128_Valor_Frete']}', ";
        $cClausula .= " '{$mT128['T128_Prazos_Pagamentos']}', ";
        $cClausula .= " '{$mT128['T128_Percentual_Comissao']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Despesas_Acessorias']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Desconto']}', ";
        $cClausula .= " '{$Observacao_Fiscal}', ";
        $cClausula .= " '{$mT128['T128_Codigo_Cliente']}', ";
        $cClausula .= " '{$mT128['T128_Codigo_Natureza']}', ";
        $cClausula .= " '{$mT128['T128_Codigo_Representante']}', ";
        $cClausula .= " '{$mT128['T128_Codigo_Transportadora']}', ";
        $cClausula .= " '{$mT128['T128_Numero_Pedido_Venda']}', ";
        $cClausula .= " '{$mT128['T128_C007_Id_Vendedor_Interno']}', ";
        $cClausula .= " '{$mT128['T128_C007_Id_Vendedor_Externo']}', ";
        $cClausula .= " '{$mT128['T128_C007_Id_Devolucao']}', ";
        $cClausula .= " '{$mT128['T128_Percentual_Reducao_ICMS']}', ";
        $cClausula .= " '{$mT128['T128_Percentual_Reducao_IPI']}', ";
        $cClausula .= " '{$mT128['T128_Aliquota_IPI']}', ";
        $cClausula .= " '{$mT128['D024_Nome_Empresa']}', ";
        $cClausula .= " '{$D007_Cliente_CNPJ_CPF}', ";
        $cClausula .= " '{$mT128['D024_Numero_Suframa']}', ";
        $cClausula .= " '{$mT128['D024_Inscricao_Estadual']}', ";
        $cClausula .= " '{$mT128['D024_Endereco']}', ";
        $cClausula .= " '{$mT128['D024_Numero']}', ";
        $cClausula .= " '{$mT128['D024_Bairro']}', ";
        $cClausula .= " '{$mT128['D024_Cep']}', ";
        $cClausula .= " '{$mT128['D020_Nome_Cidade']}', ";
        $cClausula .= " '{$mT128['D020_Codigo_IBGE']}', ";
        $cClausula .= " '{$mT128['D024_DDD_Telefone_1']}{$mT128['D024_Telefone_1']}', ";
        $cClausula .= " '{$mT128['D018_UF']}', ";
        $cClausula .= " '{$mT128['D018_Codigo_IBGE']}', ";
        $cClausula .= " current_time(), ";
        $cClausula .= " '{$mT128['D022_Cnpj']}', ";
        $cClausula .= " '{$mT128['D022_Inscricao_Estadual']}', ";
        $cClausula .= " '{$mT128['D022_Endereco']}', ";
        $cClausula .= " '{$mT128['D020_Nome_Cidade']}', ";
        $cClausula .= " '{$mT128['D018_UF']}', ";
        $cClausula .= " '{$mT128['T128_Transportadora_Placa_Veiculo']}', ";
        $cClausula .= " '{$mT128['T128_Estado_Veiculo']}', ";
        $cClausula .= " '{$mT128['T128_Quantidade_Volumes']}', ";
        $cClausula .= " '{$mT128['T128_Especie']}', ";
        $cClausula .= " '{$mT128['T128_Marca']}', ";
        $cClausula .= " '{$mT128['T128_Numero_Caixas']}', ";
        $cClausula .= " '{$mT128['T128_Peso_Bruto']}', ";
        $cClausula .= " '{$mT128['T128_Peso_Liquido']}', ";
        $cClausula .= " '{$mT128['T128_Mao_Obra_1']}', ";
        $cClausula .= " '{$mT128['T128_Mao_Obra_2']}', ";
        $cClausula .= " '{$mT128['T128_Mao_Obra_3']}', ";
        $cClausula .= " '{$mT128['T128_Mao_Obra_4']}', ";
        $cClausula .= " '{$mC004['C004_Inscricao_Municipal']}', ";
        $cClausula .= " '{$mT128['T128_Aliquota_ISS']}', ";
        $cClausula .= " '{$mC004['C004_Nome_Empresa']}', ";
        $cClausula .= " '{$mC004['C004_Logradouro']}', ";
        $cClausula .= " '{$mC004['C004_Numero']}', ";
        $cClausula .= " '{$mC004['C004_Bairro']}', ";
        $cClausula .= " '{$mC004['C004_Cep']}', ";
        $cClausula .= " '{$mC004['D020_Nome_Cidade']}', ";
        $cClausula .= " '{$mC004['D018_Estado']}', ";
        $cClausula .= " '{$mC004['D018_UF']}', ";
        $cClausula .= " '{$mC004['D020_Codigo_IBGE']}', ";
        $cClausula .= " '{$mC004['D018_Codigo_IBGE']}', ";
        $cClausula .= " 'BR', ";
        $cClausula .= " '{$mC004['C004_Telefone']}', ";
        $cClausula .= " '{$mC004['C004_Fax']}', ";
        $cClausula .= " '{$mC004['C004_Cnpj']}', ";
        $cClausula .= " '{$mC004['C004_Inscricao_Estadual']}', ";
        $cClausula .= " '{$mC004['C004_CNAE_Fiscal']}', ";
        if ($Tipo_Documento == 6) {
            $cClausula .= " 'S', ";
        } else {
            $cClausula .= " 'N', ";
        }
        $cClausula .= " '{$mT128['T128_Percentual_Desconto']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Percentual_Desconto']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Total_Base_ICMS']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Total_ICMS']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Total_Base_IPI']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Total_IPI']}', ";
        $cClausula .= " '{$mT128['T128_Valor_ISS']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Total_Custo']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Total_Produtos']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Total']}', ";
        $cClausula .= " '{$mT128['T128_IPV']}', ";
        $cClausula .= " '{$mT128['T128_IPV_Cliente']}', ";
        $cClausula .= " '{$mD006['D006_Flag_Entrada_Saida']}', ";
        $cClausula .= " '{$mD006['D006_Flag_Venda_Compra_Outros']}', ";
        $cClausula .= " '{$mT128['T128_Flag_Expedicao']}', ";
        $cClausula .= " '{$mT128['T128_Valor_Entrada']}', ";
        $cClausula .= " '{$mT128['T128_Numero_Parcelas']}', ";
        $cClausula .= " '{$mT128['T128_Numero_Dias_Entrada']}', ";
        $cClausula .= " '{$mT128['T128_Numero_Dias']}', ";
        $cClausula .= " '{$mT128['T128_Flag_Revenda_Consumidor']}', ";
        $cClausula .= " '{$Classificacao}', ";
        $cClausula .= " '{$mT128['T128_ST_ICMS_Interno']}', ";
        $cClausula .= " '{$mT128['T128_ICMS_Inter_Estadual']}', ";
        $cClausula .= " '{$mT128['T128_ST_VA']}',";
        $cClausula .= " '{$mConfig['C020_Casas_Decimais_Quantidade']}') ";
        mysql_query($cClausula);
        $T007_Id = $g['mysqlLastId'];
        $cErro   = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            $retorno = "Clausula: $cClausula<br />";
            $retorno .= "ERRO: $cErro";
            return $retorno;
        }
        
        //////////////////////////////////////////////////////////////////////////////////////
        // INCLUSAO DOS ITENS DA NOTA
        //////////////////////////////////////////////////////////////////////////////////////
        
        /*
        $sql=mysql_query("select *
        from T129
        left join D009 on D009_Id=T129_D009_Id
        left join D049 on D049_Id=D009_D049_Id
        left join D001 on D001_Id=D049_D001_Id
        left join D037 on D037_Id=D001_D037_Id
        where T129_T128_Id='{$mT128['T128_Id']}'
        and T129_Quantidade_Separacao>0
        order by T129_Id");
        */
        $sql = mysql_query("select *
							from T129
							left join D009 on D009_Id=T129_D009_Id
							left join D049 on D049_Id=D009_D049_Id
							left join D001 on D001_Id=D049_D001_Id
							left join D037 on D037_Id=D001_D037_Id
								where T129_T128_Id='{$mT128['T128_Id']}'
							order by T129_Id");
        
        while ($mT129 = mysql_fetch_array($sql)) {
            
            if ($mD006['D006_Flag_Estoque'] == 'D') {
                $Flag_Operacao_Historico = "S";
            } elseif ($mD006['D006_Flag_Estoque'] == 'C') {
                $Flag_Operacao_Historico = "E";
            } elseif ($mD006['D006_Flag_Estoque'] == 'N') {
                $Flag_Operacao_Historico = "N";
            }
            
            $mT129[T129_Descricao_Produto] = addslashes($mT129[D001_Descricao_Produto]);
            $mT129[T129_Codigo_Substituto] = addslashes($mT129[T129_Codigo_Substituto]);
            $mT129[T129_Codigo_Produto]    = addslashes($mT129[D001_Codigo_Produto]);
            $Classificacao_Fiscal          = $mT129[T129_D005_Id];
            if (!empty($mT129[T129_Codigo_Substituto])) {
                $Codigo_Produto_Nota = $mT129[T129_Codigo_Substituto];
            } else {
                $Codigo_Produto_Nota = $mT129[D001_Codigo_Produto];
            }
            
            
            /*
            if (($mT129[T129_T006_Id]>0) and ($mT129[T129_Quantidade_Separacao]<$mT129[T129_Quantidade]))
            {
            
            $T006=mysql_query("select * from T006 where T006_Id='$mT129[T129_T006_Id]'");
            $mT006=mysql_fetch_array($T006);
            
            $mT006[T006_Quantidade]=$mT129[T129_Quantidade]-$mT129[T129_Quantidade_Separacao];
            $mT006[T006_Quantidade_Comprar]=$mT129[T129_Quantidade]-$mT129[T129_Quantidade_Separacao];
            
            mysql_query("update T006 set T006_Quantidade='$mT129[T129_Quantidade_Separacao]' where T006_Id='$mT129[T129_T006_Id]'");
            
            $T006_Id=mysql_query("insert into T006 (T006_D024_Id,
            T006_D037_Id,
            T006_D009_Id,
            T006_C004_Id,
            T006_D005_Id,
            T006_T005_Id,
            T006_T004_Id,
            T006_Classificacao_Fiscal,
            T006_Quantidade,
            T006_Valor_Preco_Sem_Desconto_Unitario,
            T006_Valor_Preco_Unitario,
            T006_Codigo_Produto,
            T006_Descricao_Produto,
            T006_Peso_Unitario,
            T006_Aliquota_IPI,
            T006_Percentual_Reducao_IPI,
            T006_Aliquota_ICMS,
            T006_Percentual_Reducao_ICMS,
            T006_Flag_Status,
            T006_Flag_ST,
            T006_ST_VA,
            T006_Flag_Tipo_Produto,
            T006_Quantidade_Comprar,
            T006_D049_Id,
            T006_Quantidade_Estoque)
            values ('$mT006[T006_D024_Id]',
            '$mT006[T006_D037_Id]',
            '$mT006[T006_D009_Id]',
            '$mT006[T006_C004_Id]',
            '$mT006[T006_D005_Id]',
            '$mT006[T006_T005_Id]',
            '$mT006[T006_T004_Id]',
            '$mT006[T006_Classificacao_Fiscal]',
            '$mT006[T006_Quantidade]',
            '$mT006[T006_Valor_Preco_Sem_Desconto_Unitario]',
            '$mT006[T006_Valor_Preco_Unitario]',
            '$mT006[T006_Codigo_Produto]',
            '$mT006[T006_Descricao_Produto]',
            '$mT006[T006_Peso_Unitario]',
            '$mT006[T006_Aliquota_IPI]',
            '$mT006[T006_Percentual_Reducao_IPI]',
            '$mT006[T006_Aliquota_ICMS]',
            '$mT006[T006_Percentual_Reducao_ICMS]',
            '$mT006[T006_Flag_Status]',
            '$mT006[T006_Flag_ST]',
            '$mT006[T006_ST_VA]',
            'CP',
            '$mT006[T006_Quantidade_Comprar]',
            '$mT006[T006_D049_Id]',
            '$mT006[D009_Quantidade_Estoque_Minimo]')");
            
            $cErro = mysql_error();
            if (!empty($cErro))
            {
            echo "ERRO: $cErro\n";
            echo "Clausula: $cClausula\n\n";
            mysql_query("ROLLBACK");
            exit;
            }
            
            }
            */
            
            
            
            $D049 = mysql_query("select D009_Id,
		                              D001_Codigo_Produto
		                         from D001
		                    left join D049 as t1 on t1.D049_D001_Id=D001_Id
		                    left join D049 as t2 on D001_Id=t2.D049_D001_Id and t2.D049_Flag_Tipo='CP'
		                    left join D009 on D009_D049_Id=t2.D049_Id and D009_C004_Id='$Empresa_Atual'
		                        where t1.D049_Id='$mT129[T129_D049_Id]'");
            
            $D001  = mysql_query("SELECT D001_D001_Id FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D009_Id='{$mT129['T129_D009_Id']}' AND D001_D001_Id>0 AND D009_C004_Id='{$g['empresaAtual']}'");
            $mD001 = mysql_fetch_array($D001);
            if (mysql_num_rows($D001) == 0) {
                mysql_query("ROLLBACK");
                return "Não foi definido o que será comprado no cadastro do item $mD049[D001_Codigo_Produto]";
            }
            
            $D009_2  = mysql_query("SELECT * FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D001_Id='{$mD001['D001_D001_Id']}' AND D009_C004_Id='{$g['empresaAtual']}'");
            $mD009_2 = mysql_fetch_array($D009_2);
            
            /////////////////////////////////////////////////////////////
            //-- INSERÇAO DOS ITENS DA NOTA
            $cClausula = " insert into T008 ( ";
            $cClausula .= " T008_T007_Id, ";
            $cClausula .= " T008_D003_Id, ";
            $cClausula .= " T008_D037_Id, ";
            $cClausula .= " T008_T129_Id, ";
            $cClausula .= " T008_T008_Id, ";
            $cClausula .= " T008_T014_Id, ";
            $cClausula .= " T008_D009_Id, ";
            $cClausula .= " T008_D005_Id, ";
            $cClausula .= " T008_D006_Id, ";
            $cClausula .= " T008_D040_Id, ";
            $cClausula .= " T008_C004_Id, ";
            $cClausula .= " T008_Quantidade, ";
            $cClausula .= " T008_Unidade, ";
            $cClausula .= " T008_Valor_Preco_Sem_Desconto_Unitario, ";
            $cClausula .= " T008_Codigo_CFOP, ";
            $cClausula .= " T008_Flag_Entrada_Saida, ";
            $cClausula .= " T008_Data_Emissao, ";
            $cClausula .= " T008_Nome_Transportadora, ";
            $cClausula .= " T008_Aliquota_IPI, ";
            $cClausula .= " T008_Flag_Nacional_Importado, ";
            $cClausula .= " T008_Descricao_Produto, ";
            $cClausula .= " T008_Aliquota_ICMS, ";
            $cClausula .= " T008_Percentual_Reducao_ICMS, ";
            $cClausula .= " T008_Valor_Base_ICMS, ";
            $cClausula .= " T008_Valor_Base_IPI, ";
            $cClausula .= " T008_Valor_ICMS, ";
            $cClausula .= " T008_Valor_IPI, ";
            $cClausula .= " T008_Valor_Custo_Unitario, ";
            $cClausula .= " T008_Numero_Nota_Fiscal, ";
            $cClausula .= " T008_Codigo_Produto, ";
            $cClausula .= " T008_Codigo_Substituto, ";
            $cClausula .= " T008_Codigo_Cliente, ";
            $cClausula .= " T008_Codigo_Fornecedor, ";
            $cClausula .= " T008_Classificacao_Fiscal, ";
            $cClausula .= " T008_Codigo_Natureza, ";
            $cClausula .= " T008_Codigo_Produto_Nota, ";
            $cClausula .= " T008_Valor_Total_Custo, ";
            $cClausula .= " T008_Valor_Total_Preco, ";
            $cClausula .= " T008_Flag_Preco_Especial, ";
            $cClausula .= " T008_Flag_Situacao_Tributaria, ";
            $cClausula .= " T008_Peso_Unitario, ";
            $cClausula .= " T008_Flag_Classificacao, ";
            $cClausula .= " T008_Flag_Classificacao_Fiscal, ";
            
            $cClausula .= " T008_Flag_ST, ";
            $cClausula .= " T008_ST_VA, ";
            
            $cClausula .= " T008_Situacao_Tributaria_PIS, ";
            $cClausula .= " T008_Valor_Base_PIS, ";
            $cClausula .= " T008_Aliquota_PIS, ";
            $cClausula .= " T008_Flag_Isento_PIS, ";
            $cClausula .= " T008_Valor_PIS, ";
            
            $cClausula .= " T008_Situacao_Tributaria_COFINS, ";
            $cClausula .= " T008_Valor_Base_COFINS, ";
            $cClausula .= " T008_Aliquota_COFINS, ";
            $cClausula .= " T008_Flag_Isento_COFINS, ";
            $cClausula .= " T008_Valor_COFINS, ";
            
            $cClausula .= " T008_ST_ICMS_Interno, ";
            $cClausula .= " T008_ICMS_Inter_Estadual ";
            $cClausula .= " ) values ( ";
            $cClausula .= " '$T007_Id', ";
            $cClausula .= " '$mD009_2[D001_D003_Id]', ";
            $cClausula .= " '$mD009_2[D001_D037_Id]', ";
            $cClausula .= " '$mT129[T129_Id]', ";
            $cClausula .= " '$mT129[T129_T008_Id]', ";
            $cClausula .= " '$mT129[T129_T014_Id]', ";
            $cClausula .= " '$mD009_2[D009_Id]', ";
            $cClausula .= " '$mT129[T129_D005_Id]', ";
            $cClausula .= " '$mD006[D006_Id]', ";
            $cClausula .= " '$mT129[T129_D040_Id]', ";
            $cClausula .= " '$mT129[T128_C004_Id]', ";
            $cClausula .= " '$mT129[T129_Quantidade]', ";
            $cClausula .= " '$mT129[D037_Unidade]', ";
            $cClausula .= " '$mT129[D009_Valor_Custo_Unitario]', ";
            $cClausula .= " '$mD006[D006_Codigo_CFOP]', ";
            $cClausula .= " '$Flag_Operacao_Historico', ";
            $cClausula .= " current_date(), ";
            $cClausula .= " '', ";
            $cClausula .= " '$mT129[T129_Aliquota_IPI]', ";
            $cClausula .= " '$mT129[D049_Flag_Nacional_Importado]', ";
            $cClausula .= " '$mD009_2[D001_Descricao_Produto]', ";
            $cClausula .= " '$mT129[T129_Aliquota_ICMS]', ";
            $cClausula .= " '$mT129[T129_Percentual_Reducao_ICMS]', ";
            $cClausula .= " '$mT129[T129_Valor_Base_ICMS]', ";
            $cClausula .= " '$mT129[T129_Valor_Base_IPI]', ";
            $cClausula .= " '$mT129[T129_Valor_ICMS]', ";
            $cClausula .= " '$mT129[T129_Valor_IPI]', ";
            $cClausula .= " '$mT129[D009_Valor_Custo_Unitario]', ";
            $cClausula .= " '', ";
            $cClausula .= " '$mD009_2[D001_Codigo_Produto]', ";
            $cClausula .= " '$mT129[T129_Codigo_Substituto]', ";
            $cClausula .= " '', ";
            $cClausula .= " '$mT129[T129_Codigo_Fornecedor]', ";
            $cClausula .= " '$mT129[T129_Classificacao_Fiscal]', ";
            $cClausula .= " '', ";
            $cClausula .= " '$Codigo_Produto_Nota', ";
            $cClausula .= " '', ";
            $cClausula .= " '', ";
            $cClausula .= " '$mT129[T129_Flag_Preco_Especial]', ";
            $cClausula .= " '$mT129[T129_Flag_Situacao_Tributaria]', ";
            $cClausula .= " '$mT129[T129_Peso_Unitario]', ";
            $cClausula .= " '$Classificacao', ";
            $cClausula .= " '$mClassificacao_Fiscal[$Classificacao_Fiscal]', ";
            
            $cClausula .= " '$mT129[T129_Flag_ST]', ";
            $cClausula .= " '$mT129[T129_ST_VA]', ";
            
            $cClausula .= " '$mT129[T129_Situacao_Tributaria_PIS]', ";
            $cClausula .= " '$mT129[T129_Valor_Base_PIS]', ";
            $cClausula .= " '$mT129[T129_Aliquota_PIS]', ";
            $cClausula .= " '$mT129[T129_Flag_Isento_PIS]', ";
            $cClausula .= " '$mT129[T129_Valor_PIS]', ";
            
            $cClausula .= " '$mT129[T129_Situacao_Tributaria_COFINS]', ";
            $cClausula .= " '$mT129[T129_Valor_Base_COFINS]', ";
            $cClausula .= " '$mT129[T129_Aliquota_COFINS]', ";
            $cClausula .= " '$mT129[T129_Flag_Isento_COFINS]', ";
            $cClausula .= " '$mT129[T129_Valor_COFINS]', ";
            
            $cClausula .= " '$mT129[T129_ST_ICMS_Interno]', ";
            $cClausula .= " '$mT129[T129_ICMS_Inter_Estadual]' ";
            $cClausula .= " ) ";
            mysql_query($cClausula);
            $T008_Id = $g['mysqlLastId'];
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                $retorno = "Clausula: $cClausula<br />";
                $retorno .= "ERRO: $cErro";
                return $retorno;
            }
            
            
            /////////////////////////////////////////////////////////////
            // BUSCA OS DADOS DO D009
            $cClausula = " select * ";
            $cClausula .= " from D009 ";
            $cClausula .= " left join D049 on D009_D049_Id=D049_Id ";
            $cClausula .= " where D009_Id='$mD049[D009_Id]' ";
            $cSql_D009 = mysql_query($cClausula);
            $mD009     = mysql_fetch_array($cSql_D009);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                $retorno = "Clausula: $cClausula<br />";
                $retorno .= "Erro consultando D009: $cErro";
                return $retorno;
            }
            
            /////////////////////////////////////////////////////////////
            // ENCONTRA O ID DO T066
            $cClausula = " select T066_Id ";
            $cClausula .= " from T066 ";
            $cClausula .= " where T066_D048_Id='$D048_Id' ";
            $cClausula .= " and T066_D009_Id='$mD009[D009_Id]' ";
            $cSql_T066 = mysql_query($cClausula);
            $mT066     = mysql_fetch_array($cSql_T066);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                $retorno = "Clausula: $cClausula<br />";
                $retorno .= "Erro gravando o T066: $cErro";
                return $retorno;
            }
            
            
            /////////////////////////////////////////////////////////////
            //-- HISTORICO DO PRODUTO
            $tmpD004_Id = getD004($mT066[T066_Id]);
            $cClausula  = " insert into T001 ( ";
            $cClausula .= " T001_D024_Id_Cliente, ";
            $cClausula .= " T001_D024_Id, ";
            $cClausula .= " T001_T008_Id, ";
            $cClausula .= " T001_T007_Id, ";
            $cClausula .= " T001_D009_Id, ";
            $cClausula .= " T001_C004_Id, ";
            $cClausula .= " T001_D006_Id, ";
            $cClausula .= " T001_T066_Id, ";
            $cClausula .= " T001_Codigo_Produto, ";
            $cClausula .= " T001_Data_Lancamento, ";
            $cClausula .= " T001_Descricao_Operacao, ";
            $cClausula .= " T001_Quantidade, ";
            $cClausula .= " T001_Flag_Operacao, ";
            $cClausula .= " T001_Valor_Preco_Unitario, ";
            $cClausula .= " T001_Observacao, ";
            $cClausula .= " T001_Valor_Custo_Unitario, ";
            $cClausula .= " T001_Quantidade_Saldo, ";
            $cClausula .= " T001_Flag_MMF, ";
            $cClausula .= " T001_Flag_Atualiza_Custo,  ";
            $cClausula .= " T001_D004_Id ";
            $cClausula .= " ) values ( ";
            $cClausula .= " '$mT128[T128_D024_Id]', ";
            $cClausula .= " '$mD009[D049_D024_Id]', ";
            $cClausula .= " '$T008_Id', ";
            $cClausula .= " '$T007_Id', ";
            $cClausula .= " '$mD049[D009_Id]', ";
            $cClausula .= " '$mT129[T129_C004_Id]', ";
            $cClausula .= " '4', ";
            $cClausula .= " '$mT066[T066_Id]', ";
            $cClausula .= " '$mT129[T129_Codigo_Produto]', ";
            $cClausula .= " current_date(), ";
            $cClausula .= " 'NF-$mT007[Proximo_Numero]', ";
            $cClausula .= " '$mT129[T129_Quantidade_Separacao]', ";
            $cClausula .= " '$Flag_Operacao_Historico', ";
            $cClausula .= " '$mT129[T129_Valor_Preco_Unitario]', ";
            $cClausula .= " '', ";
            $cClausula .= " '$mT129[Custo_Unitario]', ";
            $cClausula .= " '0', ";
            $cClausula .= " '$mD006[D006_Flag_MMF]', ";
            $cClausula .= " '$mD006[D006_Flag_Atualiza_Custo]', ";
            $cClausula .= " '{$tmpD004_Id}' ";
            $cClausula .= " )";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $erro = "Clausula: {$cClausula}<br />";
                $erro .= "ERRO: $cErro";
                return 'Erro gravando T001: ' . $erro;
                mysql_query("ROLLBACK");
            }
            
        }
        
        $cSql_T008_Novo = mysql_query("select * from T008 where T008_T007_Id='{$T007_Id}'");
        while ($mT008_Novo = mysql_fetch_array($cSql_T008_Novo)) {
            $cClausula = "call T008_Gravar_Totalizacao_4('{$mT008_Novo['T008_Id']}')";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $erro = "Clausula: {$cClausula}<br />";
                $erro .= "ERRO: $cErro";
                return 'Erro gravando T008_Gravar_Totalizacao_4: ' . $erro;
                mysql_query("ROLLBACK");
            }
        }
        
        
        $cClausula = "call T007_Gravar_Totalizacao($T007_Id)";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            $erro = "Clausula: {$cClausula}<br />";
            $erro .= "ERRO: $cErro";
            return 'Erro gravando T007_Gravar_Totalizacao: ' . $erro;
            mysql_query("ROLLBACK");
        }
        
        $cSql_T007_Novo = mysql_query("select * from T007 where T007_Id=$T007_Id");
        $mT007_Novo     = mysql_fetch_array($cSql_T007_Novo);
        
        
        $Valor_Total_Base_Comissao = $mT007_Novo['T007_Valor_Total_Produtos'] + $mT007_Novo['T007_Valor_Mao_Obra'];
        
        // PROCESSO DE ATUALIZAR DATA DA PRIMEIRA ULTIMA COMPRA SENDO EXECUDATO PELO CRONTAB
        /*if ($mT128['D024_Data_Primeira_Compra'] == '0000-00-00') {
            $cClausula = " UPDATE D024 SET";
            $cClausula .= " D024_Data_Primeira_Compra = T007_Primeira_Ultima_Compra(1,D024_Id)";
            $cClausula .= " WHERE D024_Id='{$mT128['T128_D024_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $erro = "Clausula: {$cClausula}<br />";
                $erro .= "ERRO: $cErro";
                return 'Erro gravando D024_Data_Primeira_Compra: ' . $erro;
                mysql_query("ROLLBACK");
            }
            
        }
        
        $cClausula = " UPDATE D024 SET";
        $cClausula .= " D024_Data_Ultima_Compra = T007_Primeira_Ultima_Compra(2,D024_Id),";
        $cClausula .= " D024_Data_Primeira_Compra = T007_Primeira_Ultima_Compra(1,D024_Id),";
        //Function parametro = 3 retorna a empresa da ultima compra
        $cClausula .= " D024_C004_Id_Ultima_Compra = T007_Primeira_Ultima_Compra(3,D024_Id)";
        $cClausula .= " WHERE D024_Id='{$mT128['T128_D024_Id']}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            $erro = "Clausula: {$cClausula}<br />";
            $erro .= "ERRO: $cErro";
            return 'Erro gravando D024_Data_Ultima_Compra ' . $erro;
            mysql_query("ROLLBACK");
        }*/
        
        //-- VERIFICA SE O NUMERO DA NOTA JA EXISTE
        $vernotaf = mysql_query("select T007_Numero_Nota_Fiscal
							from T007
		  				   where T007_Flag_ACP='{$Tipo_Documento}'
						  	 and T007_C004_Id='{$mT128['T128_C004_Id']}'
						  	 and T007_Id!='{$T007_Id}'
		                     and T007_Numero_Nota_Fiscal='{$mT007['Proximo_Numero']}'");
        
        if (mysql_num_rows($vernotaf) > 0) {
            return $mTipoDocumento[$Tipo_Documento] . " NÃO PODE SER GERADA. POIS JÁ EXISTE O MESMO NUMERO NO BANCO DE DADOS. POR FAVOR, TENTE NOVAMENTE.";
            mysql_query("ROLLBACK");
        }
        
        //grava na C004 o ultimo numero da nota fiscal
        if ($Tipo_Documento == 1) {
            $mT007['Proximo_Numero'] = $mT007['Proximo_Numero'] + 1;
            
            $cClausula = " update C004 set ";
            $cClausula .= " C004_Ultimo_Numero_Nota_Fiscal='{$mT007['Proximo_Numero']}' ";
            $cClausula .= " where C004_Id='{$mT128['T128_C004_Id']}' ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $erro = "Clausula: {$cClausula}<br />";
                $erro .= "ERRO: $cErro";
                return 'Erro gravando C004_Ultimo_Numero_Nota_Fiscal ' . $erro;
                mysql_query("ROLLBACK");
            }
        } elseif ($Tipo_Documento == 6) {
            $mT007['Proximo_Numero'] = $mT007['Proximo_Numero'] + 1;
            
            $cClausula = " update C004 set ";
            $cClausula .= " C004_Ultimo_Numero_Nota_Fiscal_Eletronica='{$mT007['Proximo_Numero']}' ";
            $cClausula .= " where C004_Id='{$mT128['T128_C004_Id']}' ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $erro = "Clausula: {$cClausula}<br />";
                $erro .= "ERRO: $cErro";
                return 'Erro gravando C004_Ultimo_Numero_Nota_Fiscal_Eltronica ' . $erro;
                mysql_query("ROLLBACK");
            }
        } else if ($Tipo_Documento == 4) {
            $mT007['Proximo_Numero'] = $mT007['Proximo_Numero'] + 1;
            
            $cClausula = " update C004 set ";
            $cClausula .= " C004_Ultimo_Numero_Cupom_Fiscal='{$mT007['Proximo_Numero']}' ";
            $cClausula .= " where C004_Id='{$mT128['T128_C004_Id']}' ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $erro = "Clausula: {$cClausula}<br />";
                $erro .= "ERRO: $cErro";
                return 'Erro gravando C004_Ultimo_Numero_Cupom_Fiscal ' . $erro;
                mysql_query("ROLLBACK");
            }
        } else if ($Tipo_Documento == 3) {
            //-- ACP
            $mT007['Proximo_Numero'] = substr($mT007['Proximo_Numero'], 1, 12) + 1;
            
            $cClausula = " update C004 set ";
            $cClausula .= " C004_Ultimo_Numero_ACP='A{$mT007['Proximo_Numero']}' ";
            $cClausula .= " where  C004_Id='{$mT128['T128_C004_Id']}' ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $erro = "Clausula: {$cClausula}<br />";
                $erro .= "ERRO: $cErro";
                return 'Erro gravando C004_Ultimo_Numero_ACP ' . $erro;
                mysql_query("ROLLBACK");
            }
        } else {
            $mT007['Proximo_Numero'] = substr($mT007['Proximo_Numero'], 1, 12) + 1;
            
            $cClausula = " update C004 set ";
            $cClausula .= " C004_Ultimo_Numero_ACP='{$mT007['Proximo_Numero']}' ";
            $cClausula .= " where  C004_Id='{$mT128['T128_C004_Id']}' ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $erro = "Clausula: {$cClausula}<br />";
                $erro .= "ERRO: $cErro";
                return 'Erro gravando C004_Ultimo_Numero_ACP ' . $erro;
                mysql_query("ROLLBACK");
            }
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE OS IMPOSTOS ESTAO CORRETOS
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $cClausula = "select
		D009_IPI(T007_C004_Id,T008_D009_Id,T007_D024_Id,T008_D006_Id,1,T007_Flag_ACP) as T008_Aliquota_IPI_Correto,
		D009_IPI(T007_C004_Id,T008_D009_Id,T007_D024_Id,T008_D006_Id,2,T007_Flag_ACP) as T008_Percentual_Reducao_IPI_Correto,
		D009_IPV_ICMS(T007_C004_Id,T007_D024_Id,T008_D009_Id,T008_D006_Id,D020_D018_Id,T007_Flag_Revenda_Consumidor,0,T007_Flag_ACP,2)  as T008_Aliquota_ICMS_Correto,
		D009_IPV_ICMS(T007_C004_Id,T007_D024_Id,T008_D009_Id,T008_D006_Id,D020_D018_Id,T007_Flag_Revenda_Consumidor,0,T007_Flag_ACP,3) as T008_Percentual_Reducao_ICMS_Correto,
		T008_Id,
		D049_Flag_Substituicao_Tributaria,
		D005_Id,
		D005_Classificacao_Fiscal,
		D005_Flag_ST,
		D005_Valor_VA,
		D053_ST_VA,
		D053_ST_VA_2,
		T007_Flag_Revenda_Consumidor,
		D006_Flag_Substituicao_Tributaria,
		D006_Situacao_Tributaria_PIS,
		D006_Situacao_Tributaria_COFINS,
		T007_Flag_ACP,
		D059_Codigo_ST,
		D059_Id,
		D088_Codigo_ST,
		D006_Flag_ST_VA,
		D006_D059_Id,
		if(T008_Flag_Isento_COFINS='S',0,C004_Percentual_COFINS) as Aliquota_COFINS,
		if(T008_Flag_Isento_PIS='S',0,C004_Percentual_PIS) as Aliquota_PIS
		                                             from T007
		                                         left join T008 on T007_Id=T008_T007_Id
		                                         left join D024 on D024_Id=T007_D024_Id
		                                         left join D020 on D020_Id=D024_D020_Id
		                                         left join D009 on D009_Id=T008_D009_Id
		                                         left join D049 on D049_Id=D009_D049_Id
		                                         left join D001 on D001_Id=D049_D001_Id
		                                         left join D006 on D006_Id=T008_D006_Id
		                                         left join C004 on C004_Id=T007_C004_Id
		                                         left join D005 on D005_Id=D009_D005_Id(D009_Id)
		                                         left join D088 on D088_Id=D005_D088_Id
		                                         left join D059 on D059_Id=D006_D059_Id
		                                         left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id='{$Empresa_Atual}'
		                                            where T007_Id='{$T007_Id}'";
        $cSql_T008 = mysql_query($cClausula);
        $cError    = mysql_error();
        if (!empty($cError)) {
            return "Erro: $cError";
            exit;
        }
        
        while ($mT008 = mysql_fetch_array($cSql_T008)) {
            if ($mT008['D005_Valor_VA'] <= 0) {
                if ($mT008['T007_Flag_Revenda_Consumidor'] == '1') {
                    $mT008['D005_Valor_VA'] = $mT008['D053_ST_VA'];
                } else {
                    $mT008['D005_Valor_VA'] = $mT008['D053_ST_VA_2'];
                }
            }
            
            if ($mT008['D006_Flag_ST_VA'] == "N") {
                $mT008['D005_Valor_VA'] = 0;
            }
            
            if (($mT008['T007_Flag_ACP'] != '1' and $mT008['T007_Flag_ACP'] != '6') or ($mT008['D049_Flag_Substituicao_Tributaria'] != 'S') or ($mT008['D006_Flag_Substituicao_Tributaria'] != 'S')) {
                $mT008['D005_Flag_ST']  = "N";
                $mT008['D005_Valor_VA'] = 0;
            }
            
            $cClausula = "update T008 left join D006 on D006_Id=T008_D006_Id
		                                                   set T008_Aliquota_IPI='$mT008[T008_Aliquota_IPI_Correto]',
		                                                       T008_Situacao_Tributaria_IPI='$mT008[D088_Codigo_ST]',
		                                                       T008_Percentual_Reducao_IPI='$mT008[T008_Percentual_Reducao_IPI_Correto]',
		                                                       T008_Aliquota_ICMS='$mT008[T008_Aliquota_ICMS_Correto]',
		                                                       T008_Percentual_Reducao_ICMS='$mT008[T008_Percentual_Reducao_ICMS_Correto]',
		                                                       T008_Flag_ST='$mT008[D005_Flag_ST]',
		                                                       T008_D005_Id='$mT008[D005_Id]',
		                                                       T008_D059_Id='$mT008[D059_Id]',
		                                                       T008_Flag_Situacao_Tributaria=concat('0','$mT008[D059_Codigo_ST]'),
		                                                       T008_Classificacao_Fiscal='$mT008[D005_Classificacao_Fiscal]',
		                                                       T008_ST_VA='$mT008[D005_Valor_VA]',
		                                                       T008_Situacao_Tributaria_PIS='$mT008[D006_Situacao_Tributaria_PIS]',
		                                                       T008_Situacao_Tributaria_COFINS='$mT008[D006_Situacao_Tributaria_COFINS]',
		                                                       T008_Aliquota_PIS='$mT008[Aliquota_PIS]',
		                                                       T008_Aliquota_COFINS='$mT008[Aliquota_COFINS]'
		                                                       where T008_Id='$mT008[T008_Id]'";
            mysql_query("$cClausula");
            $cError = mysql_error();
            if (!empty($cError)) {
                return "Erro: $cError";
                exit;
            }
            
            mysql_query("call T008_Gravar_Totalizacao_4('$mT008[T008_Id]')");
            $cError = mysql_error();
            if (!empty($cError)) {
                return "Erro: $cError";
                exit;
            }
        }
        
        mysql_query("call T007_Gravar_Totalizacao('{$T007_Id}')");
        
        mysql_query("COMMIT");
        mysql_query("UNLOCK ALL");
        
        return true;
    }
}
