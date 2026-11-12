<?php
/*
 PDF
 Classe PDF_Code128 obtida em: http://www.fpdf.org/en/script/script88.php
*/

include_once('../fpdf/fpdf.php');
include_once('../fpdf/code128.php');

if (!defined('FPDF_FONTPATH'))
{
    define('FPDF_FONTPATH','../fpdf/font/');
}

class danfe
{

    function setNomeArquivoPDF($x)
    {
        $this->nomearquivopdf=$x;
    }

    function setSaidaArquivo($x)
    {
        // Destino de envio do arquivo:
        // I envia o arquivo para o navegador com a opção de 'salvar como'
        // D envia o arquivo ao navegador para download
        // F salvar o arquivo em uma pasta local.
        if($x!='I' and $x!='D' and $x!='F')
        {
           echo "Erro: Saida do arquivo $x definido incorretamente.";
        }
        $this->tiposaidaarquivo=$x;
    }
    
    function mask_cnpj($c)
	{
        return sprintf("%s.%s.%s/%s-%s", $c[0].$c[1], $c[2].$c[3].$c[4], $c[5].$c[6].$c[7], $c[8].$c[9].$c[10].$c[11], $c[12].$c[13]);
    }


    function mask_cpf($c)
	{
        return sprintf("%s.%s.%s-%s", $c[0].$c[1].$c[2], $c[3].$c[4].$c[5], $c[6].$c[7].$c[8], $c[9].$c[10]);
    }


    function linha($altura, $largura, $esquerda, $superior, $sbordas, $label="", $fonte="", $texto="", $tfonte="")
	{

        $esquerda=($esquerda-9)*1.20;
        $largura=$largura*1.20;
        $superior-=3;
        
        $bordas = array();
        for ($i=0; $i<strlen($sbordas); $i++)
		{
            $bordas[] = $sbordas[$i];
        }

        if (!$this->canhoto)
		{
            $superior-= 21;
        }

        // configura largura da linha N=negrito
        if (in_array("N", $bordas))
		{
            $this->pdf->SetLineWidth(0.15);
        }
		else
		{
            $this->pdf->SetLineWidth(0.15);
        }

        if (in_array("T", $bordas))
		{
            $this->pdf->line($esquerda, $superior, ($esquerda+$largura), $superior);
        }
        if (in_array("B", $bordas))
		{
            $this->pdf->line($esquerda, ($superior+$altura), ($esquerda+$largura), ($superior+$altura));
        }
        if (in_array("L", $bordas))
		{
            $this->pdf->line($esquerda, $superior, $esquerda, ($superior+$altura));
        }
        if (in_array("R", $bordas))
		{
            $this->pdf->line(($esquerda+$largura), $superior, ($esquerda+$largura), ($superior+$altura));
        }

        // impressao das labels
        if (!empty($label))
		{
            if (empty($fonte))
			{
                $fonte = "Arial,,7,L";
            }
            $f = explode(",",$fonte);
            $this->pdf->SetFont($f[0], $f[1], $f[2]);
            $this->pdf->setXY($esquerda-($f[2]/10), $superior+($f[2]/4));
            $this->pdf->Cell($largura, 0, $label, 0, 0, $f[3]);
        }

        // impressao do texto
        if (!empty($texto))
		{
            if (empty($tfonte))
			{
                $tfonte = "Arial,B,8,L";
            }
            $f = explode(",",$tfonte);
            $this->pdf->SetFont($f[0], $f[1], $f[2]);
            $this->pdf->setXY($esquerda, $superior+5);
            $this->pdf->Cell($largura, 0, $texto, 0, 0, $f[3]);
        }
    }


    function linha_itens($altura, $largura, $esquerda, $superior, $sbordas, $label="", $fonte="", $texto="", $tfonte="")
	{

        $esquerda=($esquerda-9)*1.20;
        $largura=$largura*1.20;
        $superior-=3;
        
        $bordas = array();
        for ($i=0; $i<strlen($sbordas); $i++)
		{
            $bordas[] = $sbordas[$i];
        }

        if (!$this->canhoto)
		{
            $superior-= 21;
        }

        // configura largura da linha N=negrito
        if (in_array("N", $bordas))
		{
            $this->pdf->SetLineWidth(0.25);
        }
		else
		{
            $this->pdf->SetLineWidth(0.05);
        }

        if (in_array("T", $bordas))
		{
            $this->pdf->line($esquerda, $superior, ($esquerda+$largura), $superior);
        }
        if (in_array("B", $bordas))
		{
            $this->pdf->line($esquerda, ($superior+$altura), ($esquerda+$largura), ($superior+$altura));
        }
        if (in_array("L", $bordas))
		{
            $this->pdf->line($esquerda, $superior, $esquerda, ($superior+$altura));
        }
        if (in_array("R", $bordas))
		{
            $this->pdf->line(($esquerda+$largura), $superior, ($esquerda+$largura), ($superior+$altura));
        }

        // impressao das labels
        if (!empty($label))
		{
            if (empty($fonte))
			{
                $fonte = "Arial,,7,L";
            }
            $f = explode(",",$fonte);
            $this->pdf->SetFont($f[0], $f[1], $f[2]);
            $this->pdf->setXY($esquerda-($f[2]/10), $superior+($f[2]/4));
            $this->pdf->Cell($largura+1, 0, $label, 0, 0, $f[3]);
        }

        // impressao do texto
        if (!empty($texto))
		{
            if (empty($tfonte))
			{
                $tfonte = "Arial,B,8,L";
            }
            $f = explode(",",$tfonte);
            $this->pdf->SetFont($f[0], $f[1], $f[2]);
            $this->pdf->setXY($esquerda, $superior+5);
            $this->pdf->Cell($largura, 0, $texto, 0, 0, $f[3]);
        }
    }

	function setLinhasPrimeiraPagina($x=12)
	{
		$this->linhas_primeira_pagina=31;
	}

	function setLinhasSegundaPagina($x=30)
	{
		$this->linhas_segunda_pagina=82;
	}

    function getTotalPaginas($nTotal_Linhas)
    {

		if ($nTotal_Linhas==0)
		{
		   //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS É DEFINIDO PARA 1,
		   //-- PARA QUE SEJA IMPRESSO O CABECALHO.
		   $nTotal_Linhas=1;
		}

		if ($nTotal_Linhas>=1)
		{
			$nTotal_Paginas = 1; 
	        // Encontra o numero de paginas cheias
	        $nResto  = intval($nTotal_Linhas - $this->linhas_primeira_pagina);
	        $nResto1 = $nResto / $this->linhas_segunda_pagina;
	        $nTotal_Paginas += intval($nResto1);
	        if (($nResto1-intval($nResto1))>0)
	        {
	        	$nTotal_Paginas++;
	        }
		}
		return $nTotal_Paginas;
	}


    function gera()
	{

        $cClausula  = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " left join C020 on C020_C004_Id=T007_C004_Id ";
        $cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " where T007_Id='".$this->T007_Id."' ";
        $cSql_T007 = mysql_query($cClausula);
        $mT007 = mysql_fetch_array($cSql_T007);

		$this->pdf->setFont("Arial", "B", "6");
        $this->linha(0,   0, 0, 0, "BTRL", "",      "Arial,,6,C");
        
        $nTotal_Linhas=0;
        $nTotal_Itens_Contados=0;
        foreach ($det as $d)
		{
                $prod = $det->item($nTotal_Itens_Contados)->getElementsByTagName("prod")->item(0);
	            $cDescricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
                $cDescricao = trim($cDescricao);
	            $cOrdem_Compra = $prod->getElementsByTagName("xPed")->item(0)->nodeValue;
                $cOrdem_Compra = trim($cOrdem_Compra);
                
                $nTotal_Linhas++;
//              if (strlen($cDescricao) > 50)
                if ($this->pdf->GetStringWidth($cDescricao) >= 62)
                {
                   $nTotal_Linhas++;
		        }
                if (!empty($cOrdem_Compra))
                {
                   $nTotal_Linhas++;
                }
                $nTotal_Itens_Contados++;
		}
		$nInicio=0;
		$nPagina_Atual=0;
		$nItensPorPagina=$this->linhas_primeira_pagina;

		$nTotal_Paginas=$this->getTotalPaginas($nTotal_Linhas);
		
		//-- Total de Linhas Impressas por página
        $nTotal_Linhas_Impressas=0;
        //-- Total de Linhas Impressas no Total
        $nTotal_Itens_Impressos=0;

        for ($nImpressao=0;$nImpressao<$nTotal_Linhas;$nImpressao+=$nItensPorPagina)
		{

			if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)
			{
				break;
			}
				
			$this->pdf->AddPage();

			$nPagina_Atual++;

            //Imagem como marca d´agua
            //$this->pdf->Image("../imagens/jpg/danfe_pre_visualizacao.jpg",0,20,230,237);

		    if ($nPagina_Atual>1)
		    {
		       $nItensPorPagina=$this->linhas_segunda_pagina;
		    }

	        // canhoto
	        $this->linha(8.5, 150.0,  10.0,  4.8, "TBRL", "RECEBI(EMOS) DE ");
	        $this->linha(8.5, 150.0,  30.0,  4.8,     "", utf8_decode($emit->getElementsByTagName("xNome")->item(0)->nodeValue).",", "Arial,B,8,L");
	        $this->linha(5.5, 150.0,  10.0,  7.6,     "", "OS PRODUTOS CONSTANTE DA NOTA FISCAL ELETRÔNICA INDICADA AO LADO, BEM COMO ATESTAMOS QUE OS MESMOS FORAM EXAMINADOS,");
	        $this->linha(2.5, 150.0,  10.0, 10.20,     "", "SERVINDO O ACEITE DA PRESENTE PARA TODOS OS EFEITOS LEGAIS.");
	        $this->linha( 17,  40.0, 160.0,  4.8, "TBRL", "NF-e", "Arial,B,9,C");
	        $this->pdf->setXY(181, 8);
	        $this->pdf->Cell(15, 2, "No.");
	        $this->pdf->setXY(191, 6.8);
	        $this->pdf->SetFillColor(230,230,230);
	        $this->pdf->Cell(32, 3.8, "", 0, 0, "", true);
	        $this->pdf->setXY(181, 14);
	        $this->pdf->Cell(15, 0, "SÉRIE");

	        $this->linha(8.5, 20, 172,  6.7, "", "", "", $this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue),   "Arial,B,10,L");
	        $this->linha(8.5, 20, 172, 11.9, "", "", "", $ide->getElementsByTagName('serie')->item(0)->nodeValue, "Arial,B,9,L");

	        $this->linha(8.5,    40, 10.0, 13.3, "TBRL", "DATA DE RECEBIMENTO");
	        $this->linha(8.5,   110, 50.0, 13.3, "TBRL", "IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR");

            $this->pdf->Image("../imagens/jpg/pontilhado.jpg",0,21,232,0.15);

            // Codigo de Barras
	        //$this->pdf->SetFillColor(0,0,0);
	        //$this->pdf->Code128(145, 23.8-$extra, $chave_acesso, 75, 12);

	        // logo
	        //$this->linha(39.2,  84.6,  10.0, 25.4-$extra, "B");    // identificacao emitente/logo
			$Img_nfe = imagecreatefromjpeg($this->logomarca);
			$Largura_Imagem=imagesx($Img_nfe);
			$Altura_Imagem=imagesy($Img_nfe);			    
			$x = (42*$Altura_Imagem)/$Largura_Imagem;
			$y = (37*$Largura_Imagem)/$Altura_Imagem;
			
	        //$this->pdf->Image($this->logomarca, 2, 23-$extra, 0, 0, 'jpeg');
	        if ($x>37)
	        {	            	
	            $this->pdf->Image($this->logomarca, 2+((42-$y)/2), 25-$extra, 0, 37, 'jpeg');
	        }
	        else
	        {	
	            $this->pdf->Image($this->logomarca, 2, 25-$extra+((37-$x)/2), 42, 0, 'jpeg');
			}	            	

	        $endereco_emitente = substr($enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue,0,60);
	        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".$enderEmit->getElementsByTagName("nro")->item(0)->nodeValue : null;
	        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
            $nome_empresa=$emit->getElementsByTagName("xNome")->item(0)->nodeValue;
	        $this->linha( 0,  0,  45.5, 40.0-$extra, "", "", "", utf8_decode($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue));
	        $this->linha( 0,  0,  45.5, 44.0-$extra, "", "", "", utf8_decode($enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue).'/'.utf8_decode($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue));
	        $this->linha( 0,  0,  45.5, 48.0-$extra, "", "", "", "CEP: ".utf8_decode($emit->getElementsByTagName("CEP")->item(0)->nodeValue)." - FONE: ".utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue));

	        // dados dos produtos/servicos
	        $this->linha( 4.2,    40,  10.0, 175.5-$extra,     "", "DADOS DOS PRODUTOS/SERVIÇOS", "Arial,B,6,L");
	        //$this->linha(78.0, 190.0,  10.0, 178.7-$extra, "BTRL", "");
	        $this->linha(2.6, 190.0, 10.0, 178.7-$extra, "BTRL",  "");
	        $this->linha(77.0+$extra_altura_itens,  15.0,  10.0, 178.7-$extra, "BTRL", "CÓDIGO",    "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,  62.0,  25.0, 178.7-$extra, "BTRL", "DESCRIÇÃO", "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,   9.0,  87.0, 178.7-$extra, "BTRL", "NCM/SH",    "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,   5.0,  96.0, 178.7-$extra, "BTRL", "CST",       "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,   5.3, 101.0, 178.7-$extra, "BTRL", "CFOP",      "Arial,,6,L");
	        $this->linha(77.0+$extra_altura_itens,   4.7, 106.3, 178.7-$extra, "BTRL", " UN.",      "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,  13.5, 111.0, 178.7-$extra, "BTRL", "QUANT.",    "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,  13.0, 124.5, 178.7-$extra, "BTRL", "V.UNIT.",   "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,  14.0, 137.5, 178.7-$extra, "BTRL", "V.TOTAL",   "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,  12.5, 151.5, 178.7-$extra, "BTRL", "BC.ICMS",   "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,  11.0, 164.0, 178.7-$extra, "BTRL", "V.ICMS",    "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,  11.0, 175.0, 178.7-$extra, "BTRL", "V.IPI",     "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,   7.0, 186.0, 178.7-$extra, "BTRL", "%ICMS",     "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,   7.0, 193.0, 178.7-$extra, "BTRL", "%IPI",      "Arial,,6,C");
			$i=0;
	        for ($x=0;$nTotal_Linhas_Impressas<$nItensPorPagina;$x++)
			{
				if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)
				{
					break;
				}

                $nTotal_Itens_Impressos++;
				$i++;
				
	            $this->linha_itens(65.0,  15.0,  10.0, 178.9+($i*2.3)-$extra, "", utf8_decode($cReferencia),     "Arial,B,6,L");

                $linha_descricao=$i;
		        // IMPRIME cDescricao
		        for ($d=0; $d<=strlen($cDescricao); $d++)
				{
		            $txt.= $cDescricao[$d];
		            if ($this->pdf->GetStringWidth($txt) >= 62 || $d >= strlen($cDescricao))
					{
                        if (!empty($txt))
                        {
                            $this->linha_itens(65.0,  62.0,  25.0, 178.9+($linha_descricao*2.3)-$extra, "", utf8_decode($txt),     "Arial,B,7,L");
	   	                    $txt = '';
		                    $linha_descricao++;
                            $nTotal_Linhas_Impressas++;
                        }
		            }
		        }
    			if (!empty($cOrdem_Compra))
    			{
                    $cOrdem_Compra=' > Ped.Compra: '.$cOrdem_Compra;
                    if (!empty($cItem_Ordem_Compra))
                    {
                        $cOrdem_Compra.=' - Item: '.$cItem_Ordem_Compra;
                    }
    		        // IMPRIME c
    		        for ($d=0; $d<=strlen($cOrdem_Compra); $d++)
    				{
    		            $txt.= $cOrdem_Compra[$d];
    		            if ($this->pdf->GetStringWidth($txt) >= 62 || $d >= strlen($cOrdem_Compra))
    					{
                            if (!empty($txt))
                            {
                                $this->linha_itens(65.0,  62.0,  25.0, 178.9+($linha_descricao*2.3)-$extra, "", utf8_decode($txt),     "Arial,B,7,L");
    	   	                    $txt = '';
    		                    $linha_descricao++;
                                $nTotal_Linhas_Impressas++;
                            }
    		            }
    		        }
                }
      			$linha_descricao--;
	            $NCM = (!empty($prod->getElementsByTagName("NCM")->item(0)->nodeValue)) ? $prod->getElementsByTagName("NCM")->item(0)->nodeValue : '';
	            $this->linha_itens(65.0,   9.0,  87.0, 178.9+($i*2.3)-$extra, "", $NCM,                    "Arial,B,6,C");
                if (!empty($ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue))
                {
	                $this->linha_itens(65.0,   5.0,  96.0, 178.9+($i*2.3)-$extra, "", $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue, "Arial,B,7,L");
                }
                else
                {
	                $this->linha_itens(65.0,   5.0,  96.0, 178.9+($i*2.3)-$extra, "", $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CST")->item(0)->nodeValue, "Arial,B,7,L");
                }
	            $this->linha_itens(65.0,   5.3, 101.0, 178.9+($i*2.3)-$extra, "", $prod->getElementsByTagName("CFOP")->item(0)->nodeValue,                               "Arial,B,7,C");
	            $this->linha_itens(65.0,   4.7, 106.3, 178.9+($i*2.3)-$extra, "", utf8_decode($prod->getElementsByTagName("uCom")->item(0)->nodeValue),                  "Arial,B,7,C");
	            $this->linha_itens(65.0,  13.5, 111.0, 178.9+($i*2.3)-$extra, "", number_format($nQuantidade, $this->casas_decimais_quantidade, ",", "."),   "Arial,B,7,R");
	            $this->linha_itens(65.0,  13.5, 124.5, 178.9+($i*2.3)-$extra, "", number_format($prod->getElementsByTagName("vUnCom")->item(0)->nodeValue, $this->casas_decimais_preco, ",", "."), "Arial,B,7,R");
	            $this->linha_itens(65.0,  14.5, 137.5, 178.9+($i*2.3)-$extra, "", number_format($prod->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", "."),  "Arial,B,7,R");
	            $this->linha_itens(65.0,  13.0, 151.5, 178.9+($i*2.3)-$extra, "", number_format($ICMS->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", "."),    "Arial,B,7,R");
	            $this->linha_itens(65.0,  11.5, 164.0, 178.9+($i*2.3)-$extra, "", number_format($ICMS->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", ""),  "Arial,B,7,R");
	            $vIPI = (!empty($IPI)) ? $IPI->getElementsByTagName("vIPI")->item(0)->nodeValue : 0;
	            $this->linha_itens(65.0,  11.5, 175.0, 178.9+($i*2.3)-$extra, "", number_format($vIPI, 2, ",", ""),    "Arial,B,7,R");
	            $this->linha_itens(65.0,   7.5, 186.0, 178.9+($i*2.3)-$extra, "", number_format($ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue, 2, ",", "."),  "Arial,B,7,R");
	            $pIPI = (!empty($IPI)) ? $IPI->getElementsByTagName("pIPI")->item(0)->nodeValue : 0;
	            $this->linha_itens(65.0,   7.5, 193.0, 178.9+($i*2.3)-$extra, "", number_format($pIPI, 2, ",", "."),    "Arial,B,7,R");
                $i=$linha_descricao;
	        }


		}
		
        $this->pdf->SetDisplayMode(90);
        $this->pdf->Output($this->nomearquivopdf, $this->tiposaidaarquivo);
    }

}

?>
