<?php
namespace hardness;

definirClasse('EST006');
class EST006_
{
	/**
     * insereItem
     *
     * @param Form $form
     * @param int $T012_Id
     */
    public function insereItem($form, $T012_Id)
    {
        $T012_T011_Id = $form->campoValorEnviado('T012_T011_Id');
        
        $sqlC004 = mysql_query("select T011_C004_Id from T012 left join T011 on T011_Id=T012_T011_Id where T012_Id='{$T012_Id}'");
        $rowC004 = mysql_fetch_array($sqlC004);
        
        mysql_query("update T012 set T012_C004_Id='{$rowC004['T011_C004_Id']}' where T012_Id='{$T012_Id}'");
        
        $cSql = mysql_query("call T012_Gravar_Totalizacao('$T012_Id');");
        $cSql = mysql_query("call T011_Gravar_Totalizacao_3('$T012_T011_Id');");
        
    }

    /**
     * insereItemEstoque
     *
     * @param int $T011_Id
     */
    public function insereItemEstoque($T011_Id)
    {
        global $g;
        $sql = <<<EOT
  				select 
  				D001_Codigo_Produto,
  				D001_Descricao_Produto,
  				D009_Quantidade_Comprar_Calculada,
  				D009_Quantidade_Comprar,
  				D041_Valor_Base 
  				from D001 
  				left join D049 on D049_D001_Id=D001_Id
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   			where D009_Flag_Comprar='S'
	   			AND D009_C004_Id={$g['empresaAtual']}
EOT;
        $retorno = mysql_query($sql);
        
        while ($row = mysql_fetch_array($retorno)) {
            
            // Caso tenha sido preenchido a quantidade de compra manualmente será usado esse valor, se não, 
            // será usado o valor que o sistema calculou automaticamente no reprocessa_historico();
            $quantidadeComprar = ($row['D009_Quantidade_Comprar'] > 0) ? $row['D009_Quantidade_Comprar'] : $row['D009_Quantidade_Comprar_Calculada'];
            
            $sqlInsert = <<<EOT
				insert into T012
				(T012_T011_Id,
				T012_Codigo_Produto,
				T012_Descricao_Produto,
				T012_Quantidade,
				T012_Valor_Custo_Unitario) 
				values 
				('{$T011_Id}',
				'{$row['D001_Codigo_Produto']}',
				'{$row['D001_Descricao_Produto']}',
				'{$quantidadeComprar}',
				'{$row['D041_Valor_Base']}')
EOT;
            mysql_query($sqlInsert);
            
            $cSql = mysql_query("call T012_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T011_Id}');");
        }
    }
    
    /**
     * insereItemEstoqueRequisicao
     *
     * @param int $T011_Id
     */
    public function insereItemEstoqueRequisicao($T011_Id)
    {
        global $g;
        $sql = <<<EOT
  				select 
  				D001_Codigo_Produto,
  				D001_Descricao_Produto,
  				D009_Quantidade_Comprar_Calculada,D009_Id,T189_Quantidade,T189_Flag_Cotar,T189_Id,D041_Valor_Base,T189_Descricao_Produto,T189_D009_Id 
  				from D001 
  				left join D049 on D049_D001_Id=D001_Id
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   			LEFT JOIN T189 on T189_D009_Id=D009_Id
	   			where T189_Flag_Comprar='S'
	   			AND D009_C004_Id={$g['empresaAtual']}
	   			AND T189_C007_Id_Comprar='{$g['usuarioAtual']}'
EOT;
        $retorno = mysql_query($sql);
        
        while ($row = mysql_fetch_array($retorno)) {
            $sqlInsert = <<<EOT
				insert into T012
				(T012_T011_Id,
				T012_Codigo_Produto,
				T012_Descricao_Produto,
				T012_Quantidade,
				T012_Valor_Custo_Unitario,
                T012_D009_Id,
				T012_T189_Id)
				values 
				('{$T011_Id}',
				'{$row['D001_Codigo_Produto']}',
				'{$row['T189_Descricao_Produto']}',
				'{$row['T189_Quantidade']}',
				'{$row['D041_Valor_Base']}',
                '{$row['T189_D009_Id']}',
				'{$row['T189_Id']}')
EOT;
            mysql_query($sqlInsert);
            mysql_query("UPDATE T189 SET T189_Flag_Comprar = 'N' WHERE T189_C007_Id_Comprar='{$g['usuarioAtual']}';");
            
            $cSql = mysql_query("call T012_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T011_Id}');");
        }
    }
    
    /**
     * insereItemEstoqueRequisicaoTabCotacao
     *
     * @param int $T011_Id
     * @param string $x
     */
    public function insereItemEstoqueRequisicaoTabCotacao($T011_Id, $x)
    {
        global $g;
        $x       = urldecode($x);
        $sql2    = base64_decode($x);
        $sql2    = str_replace("\'", "'", $sql2);
        $retorno = mysql_query($sql2);
        
        $sql = <<<EOT
  				select 
  				D001_Codigo_Produto,
  				D001_Descricao_Produto,
  				D009_Quantidade_Comprar_Calculada,D009_Id,T189_Quantidade,T189_Flag_Cotar,T189_Id,D041_Valor_Base,T189_Descricao_Produto 
  				from D001 
  				left join D049 on D049_D001_Id=D001_Id
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   			LEFT JOIN T189 on T189_D009_Id=D009_Id
	   			where T189_Flag_Comprar='S'
	   			AND D009_C004_Id={$g['empresaAtual']}
EOT;
        //$retorno=mysql_query($sql);
        
        while ($row = mysql_fetch_array($retorno)) {
            $sqlInsert = <<<EOT
				insert into T012
				(T012_T011_Id,
				T012_Codigo_Produto,
				T012_Descricao_Produto,
				T012_Quantidade,
				T012_Valor_Custo_Unitario,
				T012_T174_Id)
				values 
				('{$T011_Id}',
				'{$row['T059_Codigo_Produto']}',
				'{$row['T059_Descricao_Produto']}',
				'{$row['T189_Quantidade']}',
				'{$row['T174_Valor_Custo_Unitario']}',
				'{$row['T174_Id']}')
EOT;
            mysql_query($sqlInsert);
            mysql_query("UPDATE T174 SET T174_Flag_Gerar_OC = 'N' WHERE T174_Id='{$row['T174_Id']}';");
            
            $cSql = mysql_query("call T012_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T011_Id}');");
        }
    }

    /**
     * insereItemEmergencia
     *
     * @param int $T011_Id
     */
    public function insereItemEmergencia($T011_Id)
    {
        global $g;
        $sql     = <<<EOT
  				SELECT
					T006_Codigo_Produto,T006_D009_Id,T006_Id,
					T006_Descricao_Produto,
					T006_Quantidade_Comprar
				FROM T006 
					LEFT JOIN T005 ON T005_Id=T006_T005_Id
					LEFT JOIN T012 ON T012_T006_Id=T006_Id
					LEFT JOIN T059 ON T059_T006_Id=T006_Id
					LEFT JOIN T129 ON T129_T006_Id=T006_Id
					LEFT JOIN T008 ON T008_T006_Id=T006_Id
					LEFT JOIN T007 ON T007_Id=T008_T007_Id
				WHERE T006_Flag_Comprar='S' 
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
				insert into T012
				(T012_T011_Id,
				T012_Codigo_Produto,
				T012_D009_Id,
				T012_T006_Id,
				T012_Descricao_Produto,								
				T012_Valor_Custo_Unitario,
				T012_Quantidade) 
				values 
				('{$T011_Id}',
				'{$row['T006_Codigo_Produto']}',
				'{$row['T006_D009_Id']}',
				'{$row['T006_Id']}',
				'{$row['T006_Descricao_Produto']}',
				'0',
				'{$row['T006_Quantidade_Comprar']}')
EOT;
            mysql_query($sqlInsert);
            
            $cSql = mysql_query("call T012_Gravar_Totalizacao('{$g['mysqlLastId']}');");
            $cSql = mysql_query("call T011_Gravar_Totalizacao_3('{$T011_Id}');");
        }
    }
    
    /**
     * aplicarDesconto
     *
     * @param Form $form
     */
    public function aplicarDesconto($form)
    {
        require_once('old/lib/funcoes.php');
        $T012_Desconto_Tabela = $form->campoValorEnviado('Desconto');
        $T011_Id              = $form->campoValorEnviado('T011_Id');
        
        $T012_Desconto_Tabela = Formata_Valor_Gravar($T012_Desconto_Tabela, 3);
        
        /**
         * O desconto não é calculado na sigma porque o campo D049_Valor_Preco_Tabela é igual a zero
         */
        
        $sql    = "update T012 
					left join D009 on D009_Id=T012_D009_Id 
					left join D049 on D049_Id=D009_D049_Id 
					set T012_Desconto_Tabela='{$T012_Desconto_Tabela}', 
					T012_Valor_Custo_Unitario=D049_Valor_Preco_Tabela*(1-(T012_Desconto_Tabela/100)) 
					where T012_T011_Id='{$T011_Id}'";
        //echo $sql;
        $result = mysql_query($sql);
        
        return true;
    }
    
    /**
     * aplicarICMS
     *
     * @param Form $form
     */
    public function aplicarICMS($form)
    {
        require_once('old/lib/funcoes.php');
        $T012_Aliquota_ICMS = $form->campoValorEnviado('ICMS');
        $T011_Id            = $form->campoValorEnviado('T011_Id');
        
        $T012_Aliquota_ICMS = Formata_Valor_Gravar($T012_Aliquota_ICMS, 3);
        
        mysql_query("update T012
		                set T012_Aliquota_ICMS='{$T012_Aliquota_ICMS}'
		              where T012_T011_Id='{$T011_Id}'");
        
        return true;
    }
    
    /**
     * aplicarIPICad
     *
     * @param int $T011_Id
     * @return true/string
     */
    public function aplicarIPICad($T011_Id)
    {
        if ($T011_Id <= 0) {
            return 'Não recebi o T011_Id, informe o suporte técnico';
        }
        
        mysql_query("update T012
		          left join D009 on D009_Id=T012_D009_Id
		          left join D049 on D049_Id=D009_D049_Id
		          left join D001 on D001_Id=D049_D001_Id
		          left join D005 on D005_Id=D001_D005_Id
		                set T012_Aliquota_IPI=D005_Aliquota_IPI
		              where T012_T011_Id='{$T011_Id}'");
        
        return true;
    }
    
    /**
     * Retorna os dados para ser enviado o e-mail
     * A resposta é um array:
     * 	array(
     * 		'Para' => '',
     * 		'Assunto' => '',
     *		'Corpo' => '',
     * 		'D024_Id' => 0
     * 		'T139_Valor_Total' => 0.00
     * 		'T139_Numero_Registros' => 0
     * 	)
     * 
     * @param string $T003_Id
     */
    public function montarEmail($T011_Id)
    {
        global $g, $confUsuario;
        $T011_Id = mysql_real_escape_string($T011_Id);
        $sql     = "SELECT * FROM T012 where T012_T011_Id='{$T011_Id}'";
        $mT012   = mysql_query($sql);
        
        $query = "SELECT * FROM T011 left join D024 on D024_Id=T011_D024_Id WHERE T011_Id='$T011_Id'";
        $mD024 = mysql_query($query);
        $D024  = mysql_fetch_assoc($mD024);
        
        $array = array();
        
        $array['Para']    = $D024['D024_E_Mail'];
        $array['Assunto'] = "Ordem de Compra [{$T011_Id}]";
        
        $tabela = 'font-size: 12px;';
        $tabela .= 'margin: 10px 25px 10px 0;';
        $tabela .= 'width: 800px;';
        $tabela .= 'text-align: left;';
        $tabela .= 'border:0px;';
        
        $th = 'font-size: 13px;';
        $th . '=font-weight: normal;';
        $th .= 'color: black;';
        $th .= 'background: #E1ECF1;';
        $th .= 'padding: 4px 4px;';
        $th .= 'border-bottom: 0px solid #6678b1;';
        
        $td = 'border-bottom: 0px solid #ccc;';
        $td .= 'color: #000000;';
        $td .= 'background: #fff;';
        $td .= 'padding: 4px 2px 4px 2px;';
        
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem     = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem     = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        $T011_Data_Emissao = gCorrigeData($D024['T011_Data_Emissao']);
        // TABELA DO CABEÇALHO DO LAYOUT
        $corpo             = <<<EOT
			<div style="margin-left:25px;margin-top:10px;font-family:Sans-Serif;font-weight:bold;font-size:14px;color:black">
				<table width=800px border=0>
					<tr>
						<td width=10px><img src="{$caminhoImagem}" style="margin-left:10px"></td>
						<td width=130px align=center>
							Ordem de Compra<br>Nº {$T011_Id} <br> {$T011_Data_Emissao}
						</td>
					</tr>
				</table>
				<table style="{$tabela}" bgcolor=gray cellspacing=1px>
					<tbody>
						<tr>
							<td style="{$th}">Item</td>
							<td style="{$th}">Código</td>
							<td style="{$th}">Descrição</td>
							<td style="{$th}" align="right" width=30>Qte</td>
							<td style="{$th}" align="right" width=80>Preço R\$</td>
							<td style="{$th}" align="center" width=30>IPI</td>
							<td style="{$th}" align="center" width=30>ICMS</td>
						</tr>
EOT;
        // LOOPING PARA MONTAR A TABELA DOS PRODUTOS
        $count             = 1;
        $valorTotal        = 0;
        while ($T011 = mysql_fetch_assoc($mT012)) {
            $T012_Quantidade           = gCorrigeNumero($T011['T012_Quantidade']);
            $T012_Valor_Custo_Unitario = gCorrigeNumero($T011['T012_Valor_Custo_Unitario']);
            // $T004_Aliquota_IPIFormatado 	= number_format($T004['T004_Aliquota_IPI'],0);
            // $T004_Aliquota_ICMSFormatado 	= number_format($T004['T004_Aliquota_ICMS'],0);
            $corpo .= <<<EOT
						<tr>
							<td style="{$td}" align="center">{$count}</td>
							<td style="{$td}" align="right">{$T011['T012_Codigo_Produto']}</td>
							<td style="{$td};font-weight:bold" width=350px>
								{$T011['T012_Descricao_Produto']}<br />
							</td>
							<td style="{$td} text-align:center;">
								{$T012_Quantidade}
							</td>
							<td style="{$td} text-align:right;font-weight:bold">
								{$T012_Valor_Custo_Unitario}
							</td>
							<td style="{$td} text-align:right;font-weight:bold">
								{$T011['T012_Aliquota_IPI']}
							</td>
							<td style="{$td} text-align:right;">
								{$T011['T012_Aliquota_ICMS']}
							</td>
					</tr>
EOT;
            $count           = $count + 1;
            $valorTotalLinha = ($T011['T012_Quantidade'] * $T011['T012_Valor_Custo_Unitario']);
            $valorTotal      = ($valorTotal + $valorTotalLinha);
        }
        $corpo .= <<<EOT
					<tbody>
				</table>
			</div>
EOT;
        $result     = mysql_query($sql);
        $valorTotal = gCorrigeNumero($valorTotal);
        //MONTA A TABELA COM OS TOTALIZADORES DO ORÇAMENTO
        $corpo .= <<<EOT
				<div style="margin-left:25px">
					<table border=0 style="font-family:Sans-Serif;color:black;font-size:12px">
						<tr>
							<td>
								Valor total da Ordem de Compra: 
							</td>
							<td style="font-weight:bold" colspan=2 width=290px>
								R\$ {$valorTotal}
							</td>
						</tr>
EOT;
        if (!empty($D024["T011_Observacao"])) {
            $T011_Observacao = str_replace(chr(13), '<br>', $D024["T011_Observacao"]);
            $corpo .= <<<EOT
    					<tr>
    						<td valign=top>
    							Observação: 
    						</td>
    						<td style="font-weight:bold" colspan=5>
    							{$T011_Observacao}
    						</td>
    					</tr>
EOT;
        }
        $corpo .= <<<EOT
					</table>
				</div>
EOT;
        
        $array['Corpo'] = $corpo;
        // $array['D024_Id'] = $T003['T003_D024_Id'];
        // $array['T139_Valor_Total'] = $T003['T003_Valor_Total'];
        // $sql = "SELECT COUNT(*) FROM T004 where T004_T003_Id='{$T003_Id}'";
        // $registros = mysql_query($sql);
        // $registros = mysql_fetch_array($registros);
        // $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
    }
    
    /**
     * montarEmailPdf
     *
     * @param int $T011_Id
     * @return array
     */
    public function montarEmailPdf($T011_Id)
    {
        global $g, $confUsuario;
        
        //$layout = $g['conf']->ini('layoutOrcamentoEmailPdf');
        /**
         * SELECT's
         */
        $T011_Id = mysql_real_escape_string($T011_Id);
        $sql     = "SELECT * FROM T012 
				LEFT JOIN D009 ON D009_Id = T012_D009_Id
				LEFT JOIN D049 ON D049_Id = D009_D049_Id
				LEFT JOIN D082 ON D082_Id = D049_D082_Id
				LEFT JOIN D001 ON D001_Id = D049_D001_Id
				LEFT JOIN D037 ON D037_Id = D001_D037_Id
				WHERE T012_T011_Id='{$T011_Id}'";
        $mT012   = mysql_query($sql);
        
        $query = "SELECT *,
			(SELECT SUM(T012_Valor_IPI * T012_Quantidade) FROM T012 WHERE T012_T011_Id=T011_Id) as Total_IPI,
			(SELECT SUM(T012_ST) FROM T012 WHERE T012_T011_Id=T011_Id) as Total_ST,
			(SELECT SUM(T012_Valor_Custo_Unitario * T012_Quantidade) FROM T012 WHERE T012_T011_Id=T011_Id) as Total_Produtos,
			(SELECT SUM(((T012_Valor_Custo_Unitario + T012_Valor_IPI) * T012_Quantidade)+ T012_ST) FROM T012 WHERE T012_T011_Id=T011_Id) as Valor_Total
			FROM T011 
		left join D024 on D024_Id=T011_D024_Id
		left join C007 on C007_Id=T011_C007_Id
		left join C004 on C004_Id=T011_C004_Id
		LEFT JOIN D020 ON D020_Id=C004_D020_Id
		LEFT JOIN D018 ON D018_Id=C004_D018_Id
		WHERE T011_Id='$T011_Id'";
        log($query);
        $mD024 = mysql_query($query);
        $D024  = mysql_fetch_assoc($mD024);
        
        if (!empty($D024['C007_Telefone'])) {
            $tel = $D024['C007_Telefone'];
        } else {
            $tel = $D024['C004_Telefone'];
        }
        
        $Emissao = gCorrigeData($D024['T011_Data_Emissao']);
        
        if (!empty($D024['D024_Cpf'])) {
            $cpfCnpj = $D024['D024_Cpf'];
            $cpfCnpj = "<b>CPF:</b> $cpfCnpj";
        } else {
            $cpfCnpj = $D024['D024_Cnpj'];
            $cpfCnpj = "<b>CNPJ:</b> $cpfCnpj";
        }
        
        $array = array();
        
        $array['Para']    = $D024['D024_E_Mail'];
        $array['Assunto'] = "Ordem de Compra [{$T011_Id}]";
        
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
					.itens .titulo {
						background-color: #E1ECF1;
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
        $caminhoImagem     = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem     = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        $T011_Data_Emissao = gCorrigeData($D024['T011_Data_Emissao']);
        $html .= <<<EOT
		<table>
			<tr>
				<td align="left">{$caminhoImagem}</td>            
    			<td align="right" style="font-size: 230%;"><br /><br /><br /><b>Ordem compra</b> Nº {$T011_Id}          </td>
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
							<td class="tdGeral">{$D024['D024_Nome_Empresa']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$D024['D024_Endereco']}, {$D024['D024_Numero']} - {$D024['D024_Bairro']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>CEP:</b> {$D024['D024_Cep']} {$D024['D020_Nome_Cidade']} - {$D024['D018_UF']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$cpfCnpj}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Incr. Estadual:</b> {$D024['D024_Inscricao_Estadual']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$D024['D024_Web_Site']}</td>
						</tr>
					</table>
				</td>
				<td>
					<table align="left" style="font-size:24px;"  cellpadding="0">
						<tr>
							<td class="titulo_superior"><b><u>Cliente</u></b></td>
						</tr>
						<tr>
							<td class="tdGeral">{$D024['C004_Nome_Empresa']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$D024['C004_Logradouro']}, {$D024['C004_Numero']} - {$D024['C004_Bairro']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>CEP:</b> {$D024['C004_Cep']} {$D024['D020_Nome_Cidade']} - {$D024['D018_UF']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>CNPJ: </b>{$D024['C004_Cnpj']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Incr. Estadual:</b> {$D024['C004_Inscricao_Estadual']}</td>
						</tr>
						<tr>
							<td class="tdGeral">{$D024['C004_Site']}</td>
						</tr>
					</table>
				</td>
				<td width=100px>
					<table align="left" style="font-size:24px;" cellpadding="0">
						<tr>
							<td class="titulo_superior"><b><u>Comprador</u></b></td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Nº OC:</b> $T011_Id</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Data:</b> {$Emissao}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Comprador:</b> {$D024['C007_Nome']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Dpto:</b> {$D024['C007_Cargo']}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>Tel.</b> {$tel}</td>
						</tr>
						<tr>
							<td class="tdGeral"><b>E-mail:</b> {$D024['C007_Email']}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br />
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        $html .= <<<EOT
					<table class="itens" cellspacing="0" cellpadding="2">
						<tr class="titulo">
							<!--<td width="3%">It</td>-->
							<td width="7%">Código</td>
							<td width="30%">Descrição</td>
							<td width="3%">Un</td>
							<td align="center" width="8%">Marca</td>
							<td align="center" width="7%">Qtd</td>
							<td align="right" width="9%">Unitário R\$</td>
							<td align="center" width="6%">% IPI</td>
							<td align="center" width="6%">IPI R\$</td>
							<td align="center" width="6%">ST R\$</td>
							<td align="right" width="10%">Total R\$</td>
							<td align="center" width="6%">% ICMS</td>
						</tr>
EOT;
        $count      = 1;
        $valorTotal = 0;
        while ($T011 = mysql_fetch_assoc($mT012)) {
            $T012_Quantidade           = gCorrigeNumero($T011['T012_Quantidade']);
            $T012_Valor_Custo_Unitario = gCorrigeNumero($T011['T012_Valor_Custo_Unitario']);
            $T012_Valor_Total_Custo    = gCorrigeNumero($T011['T012_Valor_Total_Custo']);
            $T012_Aliquota_IPI         = gCorrigeNumero($T011['T012_Aliquota_IPI']);
            $T012_Reais_IPI            = gCorrigeNumero($T011['T012_Valor_IPI'] * $T011['T012_Quantidade'], 2);
            $T012_Reais_ST             = gCorrigeNumero($T011['T012_ST']);
            $T012_Aliquota_ICMS        = gCorrigeNumero($T011['T012_Aliquota_ICMS']);
            //if (!empty($T004["T144_Url"]))
            //	$corpo.='<a href="'.$T004["T144_Url"].'" target=_blank style="text-decoration:none;font-weight:bold;color:#000">Ver Produto</a>';
            $html .= <<<EOT
					<tr>
						<!--<td align="center">
							{$T011['T012_Id']}
						</td>-->
						<td align="center">
							{$T011['T012_Codigo_Produto']}
						</td>
						<td align="left">
							<b>{$T011['T012_Descricao_Produto']}</b>
						</td>
						<td align="left">
							{$T011['D037_Unidade']}
						</td>
						<td align="left">
							{$T011['D082_Marca']}
						</td>
						<td>
							{$T012_Quantidade}
						</td>
						<td align="right">
							<b>{$T012_Valor_Custo_Unitario}</b>
						</td>
						<td align="right">
							<b>{$T012_Aliquota_IPI}</b>
						</td>
						<td align="right">
							<b>{$T012_Reais_IPI}</b>
						</td>
						<td align="right">
							<b>{$T012_Reais_ST}</b>
						</td>
						<td align="right">
							<b>{$T012_Valor_Total_Custo}</b>
						</td>
						<td align="right">
							{$T012_Aliquota_ICMS}
						</td>
					</tr>
EOT;
            $count           = $count + 1;
            $valorTotalLinha = ($T011['T012_Quantidade'] * $T011['T012_Valor_Custo_Unitario']);
            $valorTotal      = ($valorTotal + $valorTotalLinha);
        }
        $html .= '</table>';
        
        /**
         * TABLE Parcelas pagamento
         */
        $html .= "<br />";
        $sqlT190    = "SELECT * FROM T190 LEFT JOIN D027 on D027_Id=T190_D027_Id WHERE T190_T011_Id='{$T011_Id}'";
        $resultT190 = mysql_query($sqlT190);
        if (mysql_num_rows($resultT190) > 0) {
            $html .= <<<EOT
					<table class="itens" cellspacing="0" cellpadding="2" width=50%>
						<tr class="titulo">
							<td width="8%">Parcela</td>
							<td width="10%">Dt. Vencto</td>
							<td width="10%" align="right">Valor R\$</td>
							<td align="center" width="10%">Portador</td>
						</tr>
EOT;
            while ($T190 = mysql_fetch_assoc($resultT190)) {
                $T190['T190_Valor_Parcela']   = gCorrigeNumero($T190['T190_Valor_Parcela']);
                $T190['T190_Data_Vencimento'] = gCorrigeData($T190['T190_Data_Vencimento']);
                
                $html .= <<<EOT
						<tr>
							<td align="center">
								{$T190['T190_Numero_Parcela']}
							</td>
							<td align="center">
								{$T190['T190_Data_Vencimento']}
							</td>
							<td align="right">
								<b>{$T190['T190_Valor_Parcela']}</b>
							</td>
							<td>
								{$T190['D027_Portador']}
							</td>
						</tr>
EOT;
            }
            $html .= <<<EOT
				</table>    
EOT;
            $html .= "<br />";
        } else {
            //$html .="<b>Não foi definido nenhuma forma de pagamento</b>";
        }
        
        /**
         * PDF - TOTAIS
         */
        
        $valorTotal                     = gCorrigeNumero($valorTotal);
        $D024['T011_Valor_Total_Custo'] = gCorrigeNumero($D024['T011_Valor_Total_Custo']);
        $D024['T011_Valor_Total_IPI']   = gCorrigeNumero($D024['T011_Valor_Total_IPI']);
        $D024['T011_Valor_Frete']       = gCorrigeNumero($D024['T011_Valor_Frete']);
        $D024['T011_Valor_Total_ICMS']  = gCorrigeNumero($D024['T011_Valor_Total_ICMS']);
        $D024["T011_Observacao"]        = str_replace(chr(13), '<br>', $D024["T011_Observacao"]);
        $Total_IPI                      = gCorrigeNumero($D024['Total_IPI'], 2);
        $Total_ST                       = gCorrigeNumero($D024['Total_ST']);
        $Total_Produtos                 = gCorrigeNumero($D024['Total_Produtos']);
        $Valor_Total                    = gCorrigeNumero($D024['Valor_Total'], 2);
        $html .= <<<EOT
				<br ></EOT>
					<table width=99%>
						<tr>
							<td width="60%">
								<table cellpadding="1px">
									<tr>
										<td align="left" width="100px">Forma Pagamento: </td><td align="left"><b>{$D024['T011_Forma_Pagamento']}</b></td>
									</tr>
									<tr>
										<td align="left" width="100px">Prazo entrega: </td><td align="left"><b>{$D024['T011_Prazo_Entrega']}</b></td>
									</tr>
									<tr>
										<td align="left" width="100px">Observação: </td><td align="left"><b>{$D024['T011_Observacao']}</b></td>
									</tr>
								</table>
							</td>
							<td width="30%">
								<table cellpadding="1px">
									<tr>
										<td align="right" width="150px">Total Produtos : </td><td align="right" width="100px"><b>R\$ {$Total_Produtos}</b></td>
									</tr>
									<tr>
										<td align="right" width="150px">Valor frete : </td><td align="right" width="100px"><b>R\$ {$D024['T011_Valor_Frete']}</b></td>
									</tr>
									<tr>
										<td align="right" width="150px"><b>Total IPI :</b></td><td align="right" width="100px"><b>R\$ {$Total_IPI}</b></td>
									</tr>
									<tr>
										<td align="right" width="150px"><b>Total ST :</b></td><td align="right" width="100px"><b>R\$ {$Total_ST}</b></td>
									</tr>
									<tr>
										<td align="right" width="150px"><b>Total :</b></td><td align="right" width="100px"><b>R\$ {$Valor_Total}</b></td>
									</tr>
								</table>
							</td>
							<td width="5%">
                                  
							</td>
						</tr>
EOT;
        $html .= <<<EOT
					</table>
EOT;
        // if (!empty($D024["T011_Observacao"])) {
        // $html .= '<br><span align="left">Observação: <br><b>'.str_replace(chr(10),'<br>',$D024["T011_Observacao"]).'</b></span>';
        // }
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
        $anexoNome = "{$anexoDir}OrdemCompra-{$T011_Id}.pdf";
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
        $array['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br /><font style='font-size:16px'>Segue ordem de compra número <b>$T011_Id</b> em anexo.</font><br /><br /><br />";
        // $array['D024_Id'] = $T003['T003_D024_Id'];
        // $array['T139_Valor_Total'] = $T003['T003_Valor_Total'];
        // $sql = "SELECT COUNT(*) FROM T004 where T004_T003_Id='{$T003_Id}'";
        // $registros = mysql_query($sql);
        // $registros = mysql_fetch_array($registros);
        // $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
    }
    
    /**
     * calcularParcelaCombo
     *
     * @param int $parcela
     * @param decimal $valorTotal
     * @return decimal
     */
    public function calcularParcelaCombo($parcela, $valorTotal)
    {
        return gCorrigeNumero($valorTotal / $parcela);
    }
    
    /**
     * inserirParcela
     *
     * @param Form form
     * @return boolean
     */
    public function inserirParcela($form)
    {
        $T011_Id        = $form['T011_Id'];
        $intervaloDias  = $form['IntervalorDias'];
        $portador       = $form['D027_Id'];
        $Total_Parcelas = $form['Total_Parcelas'];
        
        $diaFixo                = $form['diaFixo'];
        $primeiroVencimentoNum  = $form['primeiroVencimentoNum'];
        $primeiroVencimentoData = $form['primeiroVencimentoData'];
        
        // se foi definido um intervalo de dias
        if ($intervaloDias > 0) {
            $valorNovo  = 0;
            $query      = "SELECT * FROM T011 WHERE T011_Id='{$T011_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T011_Valor_Total_Custo'];
            
            $query            = "SELECT * FROM T190 WHERE T190_T011_Id='{$T011_Id}' ORDER BY T190_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T190_Prazos_Dias'];
            $numeroUltima     = $linha['T190_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T190_Data_Vencimento'];
            // se a parcela a ser inserida é a primeira, pega a data informada no campo "Primeiro Vencimento"
            // se não, a data é a ultima + intervalo de dias informado.
            if (mysql_num_rows($retorno) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $prazoNovo = $primeiroVencimentoNum;
                } else if (trim($primeiroVencimentoData) != "") {
                    $date1     = new \DateTime(date("Y-m-d"));
                    $date2     = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                    $interval  = $date1->diff($date2);
                    $prazoNovo = $interval->days;
                }
                $valorNovo = $valorTotal;
            } else {
                $prazoNovo = $prazoUltima + $intervaloDias;
                $valorNovo = 0;
            }
            
            $cClausula = "insert into T190 (";
            $cClausula .= "T190_T011_Id,";
            $cClausula .= "T190_D027_Id,";
            $cClausula .= "T190_Numero_Parcela,";
            $cClausula .= "T190_Prazos_Dias,";
            $cClausula .= "T190_Data_Vencimento,";
            $cClausula .= "T190_Valor_Parcela";
            $cClausula .= ") values (";
            $cClausula .= "'$T011_Id',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$prazoNovo',";
            $cClausula .= "DATE_ADD(current_date(),INTERVAL $prazoNovo DAY),";
            $cClausula .= "'$valorNovo'";
            $cClausula .= ")";
            mysql_query($cClausula);
            $erro = mysql_error();
            // ou se foi colocado um dia fixo	
        } else if ($diaFixo > 0) {
            
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            
            $valorNovo  = 0;
            $query      = "SELECT * FROM T011 WHERE T011_Id='{$T011_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T005_Valor_Total'];
            
            $query            = "SELECT * FROM T190 WHERE T190_T011_Id='{$T011_Id}' ORDER BY T190_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T190_Prazos_Dias'];
            $numeroUltima     = $linha['T190_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T190_Data_Vencimento'];
            if (mysql_num_rows($retorno) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $date     = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                    $dataNova = date("Y-m-d", $date);
                } else if (trim($primeiroVencimentoData) != "") {
                    $dataNova = gCorrigeDataInverte($primeiroVencimentoData);
                }
                $valorNovo = $valorTotal;
            } else {
                $ano = date("Y", strtotime($vencimentoUltima));
                $mes = date("m", strtotime($vencimentoUltima));
                $mes++;
                if ($mes == 13) {
                    $mes = 1;
                    $ano++;
                }
                $dataSomar = "$ano-$mes-$diaFixo";
                $validou   = checkdate($mes, $diaFixo, $ano);
                while (!$validou) {
                    $diaFixo--;
                    $validou = checkdate($mes, $diaFixo, $ano);
                }
                $dataNova = "$ano-$mes-$diaFixo";
                
                $valorNovo = 0;
            }
            
            $cClausula = "insert into T190 (";
            $cClausula .= "T190_T011_Id,";
            $cClausula .= "T190_D027_Id,";
            $cClausula .= "T190_Numero_Parcela,";
            $cClausula .= "T190_Data_Vencimento,";
            $cClausula .= "T190_Prazos_Dias,";
            $cClausula .= "T190_Valor_Parcela";
            $cClausula .= ") values (";
            $cClausula .= "'$T011_Id',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$dataNova',";
            $cClausula .= "DATEDIFF('$dataNova',current_date()),";
            $cClausula .= "'$valorNovo'";
            $cClausula .= ")";
            mysql_query($cClausula);
            $erro = mysql_error();
        }
        
        if (empty($erro))
            return true;
        else
            return false;
    }
    
    /**
     * atualizarParcelas
     *
     * @param Form form
     */
    public function atualizarParcelas($form)
    {
        global $g;
        
        $T011_Id                = $form->campoValorEnviado('T011_Id');
        $Total_Parcelas         = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias         = $form->campoValorEnviado('IntervalorDias');
        $diaFixo                = $form->campoValorEnviado('diaFixo');
        $primeiroVencimentoNum  = $form->campoValorEnviado('primeiroVencimentoNum');
        $primeiroVencimentoData = $form->campoValorEnviado('primeiroVencimentoData');
        $numeroParcelas         = $form->campoValorEnviado('Total_Parcelas');
        $D031_Id                = $form->campoValorEnviado('Portador_D031_Id');
        $primeiroVencimentoTipo = $form->campoValorEnviado('selecionaPrimeiroVencimento');
        $diasVctoTipo           = $form->campoValorEnviado('selecionaDias');
        
        if (($primeiroVencimentoData != "") || (!empty($primeiroVencimentoData))) {
            $primeiroVencmentoValor = $primeiroVencimentoData;
        } else if (($primeiroVencimentoNum != "") || (!empty($primeiroVencimentoNum))) {
            $primeiroVencmentoValor = $primeiroVencimentoNum;
        }
        
        if (($IntervalorDias != "") || (!empty($IntervalorDias))) {
            $diasVctoValor = $IntervalorDias;
        } else if (($diaFixo != "") || (!empty($diaFixo))) {
            $diasVctoValor = $diaFixo;
        }
        if ($diasVctoTipo == "I") {
            $diasVctoTipo = "Intervalo";
        } else if ($diasVctoTipo == "F") {
            $diasVctoTipo = "Dia Fixo";
        }
        
        
        $cSql_T011 = mysql_query("select * from T011 where T011_Id='{$T011_Id}'");
        $mT011     = mysql_fetch_array($cSql_T011);
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("delete T190 from T190 where T190_T011_Id='{$T011_Id}'");
            if (mysql_error())
                return mysql_error();
            else
                return true;
        }
        
        /* campo T007_T011_Id não existe na tabela T007 (copiado do sistema antigo
         * 
         * ) 
         $sql=mysql_query("select T007_Id from T007 where T007_T011_Id='{$T011_Id}' and T007_Flag_Cancelada!='S'");
         if (mysql_num_rows($sql)>0) {
         return 'Nota Fiscal já foi impressa, pedido não pode ser alterado';
         }
         */
        
        //-- PEGA O VALOR TOTAL A VISTA
        $cSql_T190           = mysql_query("select SUM(T190_Valor_Parcela) from T190 left join D031 on D031_Id=T190_D031_Id where T190_T011_Id='{$T011_Id}'");
        $mT190_Total_A_Vista = mysql_fetch_array($cSql_T190);
        
        
        $cSql_T190 = mysql_query("select * from T190 left join D031 on D031_Id=T190_D031_Id where T190_T011_Id='{$T011_Id}'");
        while ($mT190 = mysql_fetch_array($cSql_T190)) {
            $mPrazoAntigo[$mT190['T190_Numero_Parcela']]    = $mT190['T190_Prazos_Dias'];
            $mPortadorAntigo[$mT190['T190_Numero_Parcela']] = $mT190['T190_D031_Id'];
        }
        
        mysql_query("delete T190 from T190 left join D031 on D031_Id=T190_D031_Id where T190_T011_Id='{$T011_Id}'");
        
        
        $Valor_Total_IPI       = $mT011['T011_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT011['T011_Valor_Total_Custo'] - $mT190_Total_A_Vista[0];
        
        if ($Total_Parcelas > 0) {
            //$Valor_Titulo_Parcela = ( $Valor_Total_Orcamento - $Valor_Total_IPI ) / $Total_Parcelas;		
            //$Valor_Total_Titulo = ( ( round($Valor_Titulo_Parcela,2) * $Total_Parcelas ) + $Valor_Total_IPI );
            
            // verificar se deve adicionar juro na parcela 
            $C004      = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
            $mC004     = mysql_fetch_array($C004);
            $tipoJuros = $mC004['C004_Tipo_Juros'];
            
            // VALORES ADICIONADOS PARA IGNORAR O CÁLCULO DE JUROS
            $taxaJuros         = 0.00;
            $taxaJurosParcela  = 0.00;
            $valorJurosParcela = 0.00;
            $parcelasIsentas   = 9999;
            if ($Total_Parcelas > $parcelasIsentas) {
                $taxaJurosParcela = $taxaJuros;
                if ($taxaJuros > 0.00) {
                    if ($tipoJuros == 'S')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosSimples($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    else if ($tipoJuros == 'C')
                        $Valor_Titulo_Parcela = gCorrigeNumeroInverte(gJurosCompostos($Valor_Total_Orcamento, $taxaJuros, $Total_Parcelas));
                    $valorJurosParcela = $Valor_Titulo_Parcela - ($Valor_Total_Orcamento / $Total_Parcelas);
                } else {
                    $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
                }
            } else {
                $Valor_Titulo_Parcela = ($Valor_Total_Orcamento) / $Total_Parcelas;
            }
            
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas));
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if (($Valor_Total_Titulo != $Valor_Total_Orcamento) && ($Total_Parcelas <= $parcelasIsentas)) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        // se foi definido um intervalo de dias
        if ($IntervalorDias > 0) {
            // variavel para guardar a diferença de dias entre o intervalo de dias e o dia da primeira parcela	
            $acrescimo = 0;
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D031_Id > 0) {
                    $mPortadorAntigo[$x] = $D031_Id;
                }
                
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, atribui ele
                    if (trim($primeiroVencimentoNum) != "") {
                        $mPrazoAntigo[$x] = $primeiroVencimentoNum;
                        $acrescimo        = $primeiroVencimentoNum;
                        // se o primeiro vencimento foi preenchido com uma data, transforma em intervalo e atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1            = new \DateTime(date("Y-m-d"));
                        $date2            = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                        $interval         = $date1->diff($date2);
                        $mPrazoAntigo[$x] = $interval->days;
                        $acrescimo        = $interval->days;
                    }
                } else {
                    $mPrazoAntigo[$x] = (($IntervalorDias * ($x - 1)) + $acrescimo);
                }
                
                $cClausula = "insert into T190 (";
                $cClausula .= "T190_T011_Id,";
                $cClausula .= "T190_D031_Id,";
                $cClausula .= "T190_Numero_Parcela,";
                $cClausula .= "T190_Prazos_Dias,";
                $cClausula .= "T190_Data_Vencimento,";
                // $cClausula.="T190_Taxa_Juros,";
                // $cClausula.="T190_Valor_Juros,";
                $cClausula .= "T190_Valor_Parcela";
                $cClausula .= ") values (";
                $cClausula .= "'$T011_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$mPrazoAntigo[$x]',";
                $cClausula .= "DATE_ADD(current_date(),INTERVAL $mPrazoAntigo[$x] DAY),";
                // $cClausula.="'$taxaJurosParcela',";
                // $cClausula.="'$valorJurosParcela',";				
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
            }
            // ou se foi colocado um dia fixo	
        } else if ($diaFixo > 0) {
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            $mes = date("m");
            $ano = date("Y");
            
            $validou = checkdate($mes, $diaFixo, $ano);
            while (!$validou) {
                $diaFixo--;
                $validou = checkdate($mes, $diaFixo, $ano);
            }
            $primeiraData = "$ano-$mes-$diaFixo";
            
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D031_Id > 0) {
                    $mPortadorAntigo[$x] = $D031_Id;
                }
                
                $dataVencimento = "0";
                $dataPrazoDias  = date("Y-m-d");
                $numMeses       = $x;
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, soma esses dias a data atual
                    if (trim($primeiroVencimentoNum) != "") {
                        $date           = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                        $dataVencimento = date("Y-m-d", $date);
                        // se o primeiro vencimento foi preenchido com uma data, atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $dataVencimento = gCorrigeDataInverte($primeiroVencimentoData);
                    }
                    $dataPrazoDias = $dataVencimento;
                    // looping feito para quando o dia fixo é menor do que o intervalo dias da primeira parcela e fica no mesmo mes
                    $dataAux       = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    $mes1          = date("m", strtotime($dataAux));
                    $mes2          = date("m", strtotime($dataPrazoDias));
                    while ((strtotime($dataPrazoDias) > strtotime($dataAux)) || ($mes1 == $mes2)) {
                        $dataAux      = date("Y-m-d", strtotime("+1 month", strtotime($dataAux)));
                        $mes1         = date("m", strtotime($dataAux));
                        $primeiraData = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    }
                    $numMeses = "0";
                } else {
                    $dataVencimento = $primeiraData;
                    $numMeses       = $x - 1;
                    // calcula a data de vencimento para depois
                    // poder calcular os dias até o vencimento da parcela (T190_Prazos_Dias)			    		
                    $date           = strtotime("+$numMeses month", strtotime($dataVencimento));
                    $dataPrazoDias  = date("Y-m-d", $date);
                    $numMeses       = $x - 1;
                }
                
                
                $cClausula = "insert into T190 (";
                $cClausula .= "T190_T011_Id,";
                $cClausula .= "T190_D031_Id,";
                $cClausula .= "T190_Numero_Parcela,";
                $cClausula .= "T190_Data_Vencimento,";
                $cClausula .= "T190_Prazos_Dias,";
                // $cClausula.="T190_Taxa_Juros,";
                // $cClausula.="T190_Valor_Juros,";
                $cClausula .= "T190_Valor_Parcela";
                $cClausula .= ") values (";
                $cClausula .= "'$T011_Id',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "DATE_ADD('$dataVencimento',INTERVAL $numMeses MONTH),";
                $cClausula .= "DATEDIFF('$dataPrazoDias',current_date()),";
                // $cClausula.="'$taxaJurosParcela',";
                // $cClausula.="'$valorJurosParcela',";
                $cClausula .= "'$Valor_Titulo'";
                $cClausula .= ")";
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
            }
        }
        // $sqlT011 = <<<EOT
        // UPDATE T011 SET
        // T011_Pgto_Parcelas='{$Total_Parcelas}',
        // T011_Pgto_Primeiro_Vcto_Tipo='{$primeiroVencimentoTipo}',
        // T011_Pgto_Primeiro_Vcto_Valor='{$primeiroVencmentoValor}',
        // T011_Pgto_Dias_Vcto_Tipo='{$diasVctoTipo}',
        // T011_Pgto_Dias_Vcto_Valor='{$diasVctoValor}',
        // T011_Pgto_Portador='{$D031_Id}',
        // T011_Pgto_Data=CURRENT_TIMESTAMP(),
        // T011_Pgto_C007_Id='{$g['usuarioAtual']}'
        // WHERE T011_Id='{$T011_Id}'
        // EOT;
        // $resultT011 = mysql_query($sqlT011);
        // if (mysql_error()) {				
        // return mysql_error();
        // }
        return true;
    }
    
    /**
     * EST006AtualizarParcelas
     *
     * @param int $T190_Id
     * @param string $campoEditado
     * @param string $valorAntigo
     * @param string $novoValor
     */
    public function EST006AtualizarParcelas($T190_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        $query            = "SELECT * FROM T190 left join T011 on T190_T011_Id='{$T190_Id}' WHERE T190_Id='{$T190_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelhaT011    = $linha['T011_Data_Emissao'];
        $prazosDiasAntigo = $linha['T190_Prazos_Dias'];
        
        if ($campoEditado == 'T190_Prazos_Dias') {
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $date = new \DateTime($dataVelhaT011);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T190 SET T190_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T190_Id='{$T190_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T190_Data_Vencimento') {
            
            $dataAntiga = gCorrigeDataInverte($valorAntigo);
            $dataNova   = gCorrigeDataInverte($novoValor);
            
            $timeStampDataNova   = strtotime($dataNova);
            $timeStampDataAntiga = strtotime($dataAntiga);
            
            
            if ($timeStampDataAntiga == $timeStampDataNova) {
                return 'Datas Iguais';
            } else if ($timeStampDataAntiga < $timeStampDataNova) {
                $novaData = ($timeStampDataNova - $timeStampDataAntiga);
                $dias     = date('d', $novaData);
                
                $total = ($prazosDiasAntigo + $dias);
                $query = "UPDATE T190 SET T190_Prazos_Dias='{$total}' WHERE T190_Id='{$T190_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T190 SET T190_Prazos_Dias='{$total}' WHERE T190_Id='{$T190_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T190_Valor_Parcela') {
            
            $query      = "SELECT * FROM T190 WHERE T190_Id='{$T190_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $T011_Id    = $linha['T190_T011_Id'];
            $numParcela = $linha['T190_Numero_Parcela'];
            
            $query      = "SELECT T011_Valor_Total_Custo FROM T011 WHERE T011_Id='{$T011_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T011_Valor_Total_Custo'];
            
            $query                  = "SELECT SUM(T190_Valor_Parcela) FROM T190 WHERE T190_T011_Id='{$T011_Id}' AND T190_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelasRestantes = $linha['SUM(T190_Valor_Parcela)'];
            $valorRestante          = $valorTotal - $valorParcelasRestantes;
            
            $query             = "SELECT * FROM T190 WHERE T190_T011_Id='{$T011_Id}' AND T190_Numero_Parcela>'{$numParcela}'";
            $retorno           = mysql_query($query);
            $parcelasRestantes = mysql_num_rows($retorno);
            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T190 SET T190_Valor_Parcela='{$valorAntigo}' WHERE T190_Id='{$T190_Id}'";
                    mysql_query($query);
                    return 'O valor inserido irá gerar parcelas negativas...<br>Favor alterar novamente';
                }
                
                while ($linha = mysql_fetch_assoc($retorno)) {
                    $query = "UPDATE T190 SET T190_Valor_Parcela='{$valorParcelasRestantes}' WHERE T190_Id='{$linha['T190_Id']}'";
                    mysql_query($query);
                }
            }
            return true;
            
        }
        
        return true;
    }
    
    /**
     * insereOCItensViaRequisicao
     */
    public function insereOCItensViaRequisicao()
    {
        global $g, $T011_Id;
        
        $T011       = <<<EOT
		INSERT INTO T011 (
			T011_C007_Id,
			T011_C004_Id,
			T011_Data_Emissao,
			T011_Flag_Tipo
		) VALUES (
			'{$g['usuarioAtual']}',
			'{$g['empresaAtual']}',
			CURDATE(),
			'ESTOQUE'
		);
EOT;
        $resultT011 = mysql_query($T011);
        if (!$resultT011) {
            $erro = mysql_error();
            return "Erro: " . $erro;
        }
        $T011_Id = $g['mysqlLastId'];
        
        $this->insereItemEstoqueRequisicao($T011_Id);
        
        return true;
    }
    
    /**
     * gerarNotaFiscalOC
     *
     * @param int $T011_Id
     */
    public function gerarNotaFiscalOC($T011_Id)
    {
        global $g, $T013_Id;
        
        if (empty($T011_Id)) {
            return "T011_Id não recebido";
        }
        
        $T011  = mysql_query("SELECT * FROM T011 WHERE T011_Id='{$T011_Id}'");
        $mT011 = mysql_fetch_array($T011);
        
        if ($mT011['T011_Status_Aprovacao'] != '2') {
            return "A Ordem de compra não está finalizada. Por favor, é necessário finalizá-la.";
        }
        
        $T013 = mysql_query("SELECT T013_Id from T013 WHERE T013_T011_Id='{$T011_Id}' AND T013_Flag_Cancelada='N' or T013_Flag_Cancelada='' or T013_Flag_Cancelada IS NULL;");
        if (mysql_num_rows($T013) > 0) {
            return "Já foi gerada um nota fiscal para esta ordem de compra.";
        }
        
        
        $T190           = mysql_query("SELECT COUNT(*) as Total_Parcelas FROM T190 WHERE T190_T011_Id='{$T011_Id}'");
        $mT190          = mysql_fetch_array($T190);
        $Total_Parcelas = $mT190['Total_Parcelas'];
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        //-- FAZ A INSER��O DO CABECALHO DA NOTA FISCAL
        $cClausula = " insert into T013 (";
        $cClausula .= " T013_C008_Id, "; //
        $cClausula .= " T013_T011_Id, "; //
        $cClausula .= " T013_D006_Id, "; //
        $cClausula .= " T013_Tipo, "; //
        $cClausula .= " T013_D024_Id, "; //
        $cClausula .= " T013_C004_Id, "; //
        $cClausula .= " T013_Numero_Nota_Fiscal, ";
        $cClausula .= " T013_Data_Emissao, ";
        $cClausula .= " T013_Data_Entrada, ";
        $cClausula .= " T013_Codigo_CFOP, ";
        $cClausula .= " T013_Numero_Controle, ";
        $cClausula .= " T013_Parcelas, ";
        $cClausula .= " T013_Valor_Total_Produtos, ";
        $cClausula .= " T013_Valor_Desconto_Produtos, ";
        $cClausula .= " T013_Valor_Total, ";
        $cClausula .= " T013_Valor_Desconto, ";
        $cClausula .= " T013_Numero_Ordem_Compra, ";
        $cClausula .= " T013_Observacao_1, ";
        $cClausula .= " T013_Valor_Total_Base_ICMS, ";
        $cClausula .= " T013_Valor_Total_Base_ICMS_Diferido, ";
        $cClausula .= " T013_Valor_Total_ICMS, ";
        $cClausula .= " T013_Aliquota_ICMS, ";
        $cClausula .= " T013_Casas_Decimais_Valor_ICMS, ";
        $cClausula .= " T013_Valor_Total_Base_ICMS_Substituicao, ";
        $cClausula .= " T013_Valor_ICMS_Substituicao, ";
        $cClausula .= " T013_Valor_Total_Base_IPI, ";
        $cClausula .= " T013_Valor_Total_IPI, ";
        $cClausula .= " T013_Flag_Calcular_ICMS_IPI, ";
        $cClausula .= " T013_Flag_Arredondar_Valor_IPI, ";
        $cClausula .= " T013_Numero_Conhecimento, ";
        $cClausula .= " T013_Flag_Frete, ";
        $cClausula .= " T013_Tipo_Transporte, ";
        $cClausula .= " T013_Valor_Frete, ";
        $cClausula .= " T013_Flag_Calcular_ICMS_Frete, ";
        $cClausula .= " T013_Flag_Calcular_IPI_Frete, ";
        $cClausula .= " T013_Nome_Transportadora, ";
        $cClausula .= " T013_D022_Id, ";
        $cClausula .= " T013_D031_Id, ";
        $cClausula .= " T013_Numero_Parcelas, ";
        $cClausula .= " T013_Numero_Dias, ";
        $cClausula .= " T013_Valor_Juros, ";
        $cClausula .= " T013_T085_Id, ";
        $cClausula .= " T013_Valor_Despesas_Acessorias, ";
        $cClausula .= " T013_Flag_Calcular_ICMS_Despesas_Acessorias, ";
        $cClausula .= " T013_Flag_Calcular_IPI_Despesas_Acessorias, ";
        $cClausula .= " T013_Mao_Obra_1, ";
        $cClausula .= " T013_Mao_Obra_2, ";
        $cClausula .= " T013_Valor_Mao_Obra, ";
        $cClausula .= " T013_Aliquota_ISS, ";
        $cClausula .= " T013_Valor_ISS, ";
        $cClausula .= " T013_Valor_Seguro, ";
        $cClausula .= " T013_Flag_Calcular_ICMS_Seguro, ";
        $cClausula .= " T013_Flag_Calcular_IPI_Seguro, ";
        $cClausula .= " T013_D014_Id, ";
        $cClausula .= " T013_Valor_Dolar,  ";
        $cClausula .= " T013_Chave_Acesso_Nfe,  ";
        $cClausula .= " T013_Numero_Recibo_Nfe,  ";
        $cClausula .= " T013_Numero_Protocolo_Nfe,  ";
        $cClausula .= " T013_Data_Hora_Protocolo_Nfe,  ";
        $cClausula .= " T013_Retorno_Nfe,  ";
        $cClausula .= " T013_Ambiente_Nfe,  ";
        $cClausula .= " T013_Flag_Calcular,  ";
        $cClausula .= " T013_Flag_Validacoes,  ";
        $cClausula .= " T013_Valor_Total_PIS,  ";
        $cClausula .= " T013_Valor_Total_COFINS,  ";
        $cClausula .= " T013_Peso_Liquido,  ";
        $cClausula .= " T013_Peso_Bruto,  ";
        $cClausula .= " T013_Arquivo_XML  ";
        $cClausula .= " ) values ( ";
        $cClausula .= " '{$mT011['T011_C008_Id']}', ";
        $cClausula .= " '{$T011_Id}', ";
        $cClausula .= " '', ";
        $cClausula .= " '1', ";
        $cClausula .= " '{$mT011['T011_D024_Id']}', ";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '', ";
        $cClausula .= " current_date(), ";
        $cClausula .= " current_date(), ";
        $cClausula .= " '', ";
        $cClausula .= " '0', ";
        $cClausula .= " '{$Total_Parcelas}', ";
        $cClausula .= " '{$mT011['T011_Valor_Total_Custo']}', ";
        $cClausula .= " '0', ";
        $cClausula .= " '{$mT011['T011_Valor_Total_Custo']}', ";
        $cClausula .= " '', ";
        $cClausula .= " '', ";
        $cClausula .= " '{$mT011['T011_Observacao']}', ";
        $cClausula .= " '{$mT011['T011_Valor_Total_Base_ICMS']}', ";
        $cClausula .= " '0', ";
        $cClausula .= " '{$mT011['T011_Valor_Total_ICMS']}', ";
        $cClausula .= " '{$mT011['T011_Aliquota_ICMS']}', ";
        $cClausula .= " '5', ";
        $cClausula .= " '', ";
        $cClausula .= " '', ";
        $cClausula .= " '{$mT011['T011_Valor_Total_Base_IPI']}', ";
        $cClausula .= " '{$mT011['T011_Valor_Total_IPI']}', ";
        $cClausula .= " 'N', ";
        $cClausula .= " 'S', ";
        $cClausula .= " '', ";
        $cClausula .= " '', ";
        $cClausula .= " '', ";
        $cClausula .= " '{$mT011['T011_Valor_Frete']}', ";
        $cClausula .= " 'S', ";
        $cClausula .= " 'S', ";
        $cClausula .= " '', ";
        $cClausula .= " '{$mT011['T011_D022_Id']}', ";
        $cClausula .= " '0', ";
        $cClausula .= " '$Total_Parcelas', ";
        $cClausula .= " '30', ";
        $cClausula .= " '0', ";
        $cClausula .= " '0', ";
        $cClausula .= " '', ";
        $cClausula .= " 'S', ";
        $cClausula .= " 'S', ";
        $cClausula .= " '', ";
        $cClausula .= " '', ";
        $cClausula .= " '0', ";
        $cClausula .= " '0', ";
        $cClausula .= " '0', ";
        $cClausula .= " '', ";
        $cClausula .= " 'S', ";
        $cClausula .= " 'S', ";
        $cClausula .= " '0', ";
        $cClausula .= " '0', ";
        $cClausula .= " '',  ";
        $cClausula .= " '',  ";
        $cClausula .= " '',  ";
        $cClausula .= " '',  ";
        $cClausula .= " '', ";
        $cClausula .= " '', ";
        $cClausula .= " 'N', "; //T013_Flag_Calcular
        $cClausula .= " 'N', "; //T013_Flag_Calcular
        $cClausula .= " '',  ";
        $cClausula .= " '',  ";
        $cClausula .= " '',  ";
        $cClausula .= " '',  ";
        $cClausula .= " ''  ";
        $cClausula .= " )";
        $cSql  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro . "<br />" . $cClausula;
        } else {
            $T013_Id = $g['mysqlLastId'];
        }
        
        // Pagamento T154
        $T190 = mysql_query("SELECT * FROM T190 WHERE T190_T011_Id='{$T011_Id}'");
        while ($mT190 = mysql_fetch_array($T190)) {
            $sql     = <<<EOT
			INSERT INTO T154 (
					T154_T013_Id,
					T154_D027_Id,
					T154_Numero_Parcela,
					T154_Prazos_Dias,
					T154_Data_Vencimento,
					T154_Valor_Parcela,
					T154_D031_Id
				) VALUES (
					'{$T013_Id}',
					'{$mT190['T190_D027_Id']}',
					'{$mT190['T190_Numero_Parcela']}',
					'{$mT190['T190_Prazos_Dias']}',
					'{$mT190['T190_Data_Vencimento']}',
					'{$mT190['T190_Valor_Parcela']}',
					'{$mT190['T190_D031_Id']}'
				);
EOT;
            $retorno = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro ao inserir as parcelas. Erro:" . mysql_error();
            }
        }
        
        
        /// T014
        $T012 = mysql_query("SELECT * FROM T012 
		LEFT JOIN D009 ON D009_Id=T012_D009_Id
		LEFT JOIN D049 ON D049_Id=D009_D049_Id
		LEFT JOIN D001 ON D001_Id=D049_D001_Id
		LEFT JOIN D005 ON D005_Id=D001_D005_Id
		WHERE T012_T011_Id='{$T011_Id}'");
        
        while ($mT012 = mysql_fetch_array($T012)) {
            
            $cClausula = " insert into T014 (";
            $cClausula .= " T014_T013_Id, ";
            $cClausula .= " T014_D009_Id, ";
            $cClausula .= " T014_D037_Id, ";
            $cClausula .= " T014_C004_Id, ";
            $cClausula .= " T014_Codigo_Produto, ";
            $cClausula .= " T014_Descricao_Produto, ";
            $cClausula .= " T014_Quantidade, ";
            $cClausula .= " T014_Valor_Custo_Unitario, ";
            $cClausula .= " T014_Valor_Total_Custo, ";
            $cClausula .= " T014_Aliquota_IPI, ";
            $cClausula .= " T014_Valor_Base_IPI, ";
            $cClausula .= " T014_Valor_IPI, ";
            $cClausula .= " T014_Classificacao_Fiscal, ";
            $cClausula .= " T014_Aliquota_ICMS, ";
            $cClausula .= " T014_Percentual_Reducao_ICMS, ";
            $cClausula .= " T014_Valor_Base_ICMS, ";
            $cClausula .= " T014_Valor_ICMS, ";
            $cClausula .= " T014_Valor_Total_Desconto, ";
            $cClausula .= " T014_Valor_Desconto_Unitario, ";
            $cClausula .= " T014_T012_Id ";
            // $cClausula .= " T014_Valor_ICMS_Substituicao ";
            // $cClausula .= " T014_Flag_Situacao_Tributaria, ";
            // $cClausula .= " T014_Modalidade_ICMS, ";
            // $cClausula .= " T014_Modalidade_ICMS_Substituicao, ";
            // $cClausula .= " T014_ICMS_Inter_Estadual, ";
            // $cClausula .= " T014_Aliquota_ICMS_Interno, ";
            // $cClausula .= " T014_Valor_Base_ICMS_Substituicao, ";
            // $cClausula .= " T014_Situacao_Tributaria_IPI, ";
            // $cClausula .= " T014_Situacao_Tributaria_PIS, ";
            // $cClausula .= " T014_Valor_Base_PIS, ";
            // $cClausula .= " T014_Aliquota_PIS, ";
            // $cClausula .= " T014_Valor_PIS, ";
            // $cClausula .= " T014_Situacao_Tributaria_COFINS, ";
            // $cClausula .= " T014_Valor_Base_COFINS, ";
            // $cClausula .= " T014_Aliquota_COFINS, ";
            // $cClausula .= " T014_Valor_COFINS, ";
            // $cClausula .= " T014_MVA_Ajustada ";
            $cClausula .= " ) values ( ";
            $cClausula .= " '$T013_Id', ";
            $cClausula .= " '{$mT012['D009_Id']}', ";
            $cClausula .= " '0', ";
            $cClausula .= " '{$g['empresaAtual']}', ";
            $cClausula .= " '{$mT012['T012_Codigo_Produto']}', ";
            $cClausula .= " '{$mT012['T012_Descricao_Produto']}', ";
            $cClausula .= " '{$mT012['T012_Quantidade']}', ";
            $cClausula .= " '{$mT012['T012_Valor_Custo_Unitario']}', ";
            $cClausula .= " '{$mT012['T012_Valor_Total_Custo']}', ";
            $cClausula .= " '{$mT012['T012_Aliquota_IPI']}', ";
            $cClausula .= " '{$mT012['T012_Valor_Base_IPI']}', ";
            $cClausula .= " '{$mT012['T012_Valor_IPI']}', ";
            $cClausula .= " '{$mT012['D005_Classificacao_Fiscal']}', ";
            $cClausula .= " '{$mT012['T012_Aliquota_ICMS']}', ";
            $cClausula .= " '{$mT012['T012_Percentual_Reducao_ICMS']}', ";
            $cClausula .= " '{$mT012['T012_Valor_Base_ICMS']}', ";
            $cClausula .= " '{$mT012['T012_Valor_ICMS']}', ";
            $cClausula .= " '{$mT012['T012_Desconto_Tabela']}', ";
            $cClausula .= " T014_Valor_Total_Desconto / T014_Quantidade, ";
            $cClausula .= " '{$mT012['T012_Id']}' ";
            // $cClausula .= " '{$T014_Valor_ICMS_Substituicao}', ";
            // $cClausula .= " '{$T014_Flag_Situacao_Tributaria}', ";
            // $cClausula .= " '{$T014_Modalidade_ICMS}', ";
            // $cClausula .= " '{$T014_Modalidade_ICMS_Substituicao}', ";
            // $cClausula .= " '{$T014_ICMS_Inter_Estadual}', ";
            // $cClausula .= " '{$T014_Aliquota_ICMS_Interno}', ";
            // $cClausula .= " '{$T014_Valor_Base_ICMS_Substituicao}', ";
            // $cClausula .= " '{$T014_Situacao_Tributaria_IPI}', ";
            // $cClausula .= " '{$T014_Situacao_Tributaria_PIS}', ";
            // $cClausula .= " '{$T014_Valor_Base_PIS}', ";
            // $cClausula .= " '{$T014_Aliquota_PIS}', ";
            // $cClausula .= " '{$T014_Valor_PIS}', ";
            // $cClausula .= " '{$T014_Situacao_Tributaria_COFINS}', ";
            // $cClausula .= " '{$T014_Valor_Base_COFINS}', ";
            // $cClausula .= " '{$T014_Aliquota_COFINS}', ";
            // $cClausula .= " '{$T014_Valor_COFINS}', ";
            // $cClausula .= " '{$T014_MVA_Ajustada}' ";
            $cClausula .= " ) ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            //echo $cClausula;
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br />" . $cClausula;
            }
        }
        
        mysql_query("COMMIT");
        return true;
        
    }
}
