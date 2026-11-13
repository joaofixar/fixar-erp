<?php
/*
 DANFE EM PDF
 Classe PDF_Code128 obtida em: http://www.fpdf.org/en/script/script88.php
*/
//error_reporting(0);

include_once('../fpdf/fpdf.php');
include_once('../fpdf/code128.php');

if (!defined('FPDF_FONTPATH'))
{
    define('FPDF_FONTPATH','../fpdf/font/');
}

class danfe
{
    var $canhoto;   // se true, imprime campos do canhoto
    var $issqn;     // se true, imprime campos do issqn
    var $pdf;       // objeto fpdf()
    var $xml;       // string XML NFe
    var $array_uf;  // usado para obter código do estado do destinatario qdo em contingencia
    var $protocolo; // protocolo de autorizacao deve sair em infCpl
    var $data_hora; // data e hora de autorizacao devem sair em infCpl

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
    
    function setT007_Id($x)
    {
       $this->T007_Id=$x;
    }
    
    function mask_cnpj($c)
	{
        return sprintf("%s.%s.%s/%s-%s", $c[0].$c[1], $c[2].$c[3].$c[4], $c[5].$c[6].$c[7], $c[8].$c[9].$c[10].$c[11], $c[12].$c[13]);
    }


    function mask_cpf($c)
	{
        return sprintf("%s.%s.%s-%s", $c[0].$c[1].$c[2], $c[3].$c[4].$c[5], $c[6].$c[7].$c[8], $c[9].$c[10]);
    }


    function mask_nnf($n)
	{
        $n = sprintf("%09s", $n);
        return sprintf("%s.%s.%s", $n[0].$n[1].$n[2], $n[3].$n[4].$n[5], $n[6].$n[7].$n[8]);
    }

    function calcula_dv($chave35)
	{
        $multiplicadores = array(2,3,4,5,6,7,8,9);
        $i = 34;
        while ($i >= 0)
		{
            for ($m=0; $m<count($multiplicadores) && $i>=0; $m++)
			{
                $soma_ponderada+= $chave35[$i] * $multiplicadores[$m];
                $i--;
            }
        }
        $resto = $soma_ponderada % 11;
        if ($resto == '0' || $resto == '1')
		{
            $dv = 0;
        }
		else
		{
            $dv = 11 - $resto;
        }
        return $dv;
    }

    function formata_chave_acesso($input)
    {
       while (strlen($input)>0)
       {
          $lenght=substr($input,4,strlen($input));
          $formatted_input .= " ".substr($input,0,4);
          $input=$lenght;
       }
          
       return $formatted_input;
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
		global $Banco_Dados;

        if (empty($this->xml))
		{
            return false;
        }
        
        $cClausula  = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " left join C020 on C020_C004_Id=T007_C004_Id ";
        $cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " where T007_Id='".$this->T007_Id."' ";
        $cSql_T007 = mysql_query($cClausula);
        $mT007 = mysql_fetch_array($cSql_T007);

        $dom = new DomDocument;
        $dom->loadXML($this->xml);
        $infNFe     = $dom->getElementsByTagName("infNFe")->item(0);
        $ide        = $dom->getElementsByTagName("ide")->item(0);
        $emit       = $dom->getElementsByTagName("emit")->item(0);
        $dest       = $dom->getElementsByTagName("dest")->item(0);
        $enderEmit  = $dom->getElementsByTagName("enderEmit")->item(0);
        $enderDest  = $dom->getElementsByTagName("enderDest")->item(0);
        $det        = $dom->getElementsByTagName("det");
        $cobr       = $dom->getElementsByTagName("cobr")->item(0);
        $dup        = $dom->getElementsByTagName('dup');
        $ICMSTot    = $dom->getElementsByTagName("ICMSTot")->item(0);
        $transp     = $dom->getElementsByTagName("transp")->item(0);
        $transporta = $dom->getElementsByTagName("transporta")->item(0);
        $veicTransp = $dom->getElementsByTagName("veicTransp")->item(0);
        $vols       = $dom->getElementsByTagName("vol");
        $infAdic    = $dom->getElementsByTagName("infAdic")->item(0);
        $protNFe    = $dom->getElementsByTagName("protNFe")->item(0);
        
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

            if ($this->visualizacao=='S')
            {
                $this->pdf->Image("../imagens/jpg/danfe_pre_visualizacao.jpg",0,20,230,237);
            }
            elseif ($mT007[C020_NFe_Treinamento]=='S')
            {
                $this->pdf->Image("../imagens/jpg/danfe_demonstracao.jpg",0,20,230,237);
            }
            elseif ($mT007[T007_Flag_Cancelada]=='S')
            {
               $this->pdf->Image("../imagens/jpg/danfe_cancelado.jpg",0,20,230,237);
            }

		    if ($nPagina_Atual>1)
		    {
		       $nItensPorPagina=$this->linhas_segunda_pagina;
		    }

	        if ($this->canhoto and $nPagina_Atual==1)
			{
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
                if($_REQUEST['vlrNF'] == 'S'){
                    $this->linha(8.5, 40, 175, 11.9, "", "", "", "R$ " . number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", "."), "Arial,B,9,L" );
                }
	            $this->linha(8.5,    40, 10.0, 13.3, "TBRL", "DATA DE RECEBIMENTO");
	            $this->linha(8.5,   110, 50.0, 13.3, "TBRL", "IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR");

                $this->pdf->Image("../imagens/jpg/pontilhado.jpg",0,21,232,0.15);


	            $extra = 0;

	        }
			else
			{
	            $extra = 21;
	        }

	        $chave_acesso = str_replace('NFe', '', $infNFe->getAttribute("Id"));
	        $this->pdf->SetFillColor(0,0,0);
	        $this->pdf->Code128(145, 23.8-$extra, $chave_acesso, 75, 12);

	        // dados da nfe
	        $this->linha(39.2,  84.6,  10.0, 25.4-$extra, "B");    // identificacao emitente/logo
	        if (!empty($this->logomarca) && is_file($this->logomarca))
			{
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
	        }
	        $endereco_emitente = substr($enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue,0,60);
	        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".$enderEmit->getElementsByTagName("nro")->item(0)->nodeValue : null;
	        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
            $nome_empresa=$emit->getElementsByTagName("xNome")->item(0)->nodeValue;
/*          if (strlen($nome_empresa)<=20)
            {
    	        $this->linha( 0,  0,  45.5, 28.0-$extra, "TBRL", "", "", utf8_decode(substr($nome_empresa,0,46)), "Arial,B,10,L");
            } */
            if (strlen($nome_empresa)<=40)
            {
    	        $this->linha( 0,  0,  9.0, 22.4-$extra, "TBRL", "", "", utf8_decode(substr($nome_empresa,0,46)), "Arial,B,11,L");
            }
            else
            {
    	        $this->linha( 0,  0,  9.0, 22.4-$extra, "TBRL", "", "", utf8_decode($nome_empresa), "Arial,B,9,L");
            }
            if (strlen($endereco_emitente)>31)
            {
    	        $this->linha( 0,  0,  45.5, 32.0-$extra, "", "", "", utf8_decode(substr($endereco_emitente,0,31)));
    	        $this->linha( 0,  0,  45.5, 36.0-$extra, "", "", "", utf8_decode(substr($endereco_emitente,31,30)));
            }
            else
            {
    	        $this->linha( 0,  0,  45.5, 36.0-$extra, "", "", "", utf8_decode($endereco_emitente));
            }
	        $this->linha( 0,  0,  45.5, 40.0-$extra, "", "", "", utf8_decode($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue));
	        $this->linha( 0,  0,  45.5, 44.0-$extra, "", "", "", utf8_decode($enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue).'/'.utf8_decode($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue));
	        $this->linha( 0,  0,  45.5, 48.0-$extra, "", "", "", "CEP: ".utf8_decode($emit->getElementsByTagName("CEP")->item(0)->nodeValue)." - FONE: ".utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue));

            if (!empty($mT007[C004_E_Mail]))
            {
     	        $this->linha( 0,  0,  45.5, 52.0-$extra, "", "", "", "$mT007[C004_E_Mail]");
            }
            if (!empty($mT007[C004_Site]))
            {
     	        $this->linha( 0,  0,  45.5, 56.0-$extra, "", "", "", "$mT007[C004_Site]");
            }
            
	        if ($ide->getElementsByTagName("tpEmis")->item(0)->nodeValue != 1 and $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue != 3)
			{
	            // contingencia
	            $this->pdf->SetTextColor(170,170,170);
	            $this->linha( 5.0, 190.0,  10.0, 120.0-$extra, "", "", "", "DANFE em Contingência",   "Arial,B,30,C");
	            $this->linha( 5.0, 190.0,  10.0, 140.0-$extra, "", "", "", "Impresso em decorrência", "Arial,B,30,C");
	            $this->linha( 5.0, 190.0,  10.0, 160.0-$extra, "", "", "", "de problemas têcnicos",   "Arial,B,30,C");
	            $this->pdf->SetTextColor(0,0,0);

	            $dados_nfe = $this->array_uf[$enderDest->getElementsByTagName("UF")->item(0)->nodeValue];
	            $dados_nfe.= $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue;
	            $dados_nfe.= $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
	            $dados_nfe.= sprintf("%014s", str_replace(',', '', number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", "")));
	            $dados_nfe.= 1; // ICMSp
	            $dados_nfe.= 2; // ICMSs
	            $data_emissao = troca_data_dma($ide->getElementsByTagName("dEmi")->item(0)->nodeValue);
	            $dados_nfe.= $data_emissao[0].$data_emissao[1];
	            $dados_nfe.= $this->calcula_dv($dados_nfe);

	            $this->pdf->Code128(125, 51.2-$extra, $dados_nfe, 70, 12); // codigo de barras

	            $this->linha(14.8,    80, 120.0,  49.8-$extra,  "BTRL", ""); // codigo de barras dos dados
	            $this->linha( 8.5,    80, 120.0,  64.6-$extra,  "BTRL",  "DADOS DA NF-e", "", $dados_nfe, "Arial,B,6.5,L");

	        }
			else
			{

                if ($mT007[C020_NFe_Treinamento]=='S' or $this->visualizacao=='S')
                {
    	            $this->linha(14.8,    80, 120.0,  49.8-$extra,  "BTRL", "", "", "");
    	            $this->linha(14.8,    80, 120.0,  52.8-$extra,      "", "", "", "");
    	            $this->linha(14.8,    80, 120.0,  55.8-$extra,      "", "", "", "");
    	            $this->linha( 8.5,    80, 120.0,  64.6-$extra,  "BTRL",  "PROTOCOLO DE AUTORIZAÇÃO DE USO", "", "", "Arial,B,8,L");
                }
                else
                {
    	            // normal
    		        $nNumero_Protocolo=utf8_decode($protNFe->getElementsByTagName("nProt")->item(0)->nodeValue);
    		        $cData_Protocolo=utf8_decode($protNFe->getElementsByTagName("dhRecbto")->item(0)->nodeValue);
    		        list($cData_Protocolo,$cHora_Protocolo)=explode("T",$cData_Protocolo);
    	            $this->linha(14.8,    80, 120.0,  49.8-$extra,  "BTRL", "", "", "Consulta de autenticidade no portal nacional");
    	            $this->linha(14.8,    80, 120.0,  52.8-$extra,      "", "", "", "da NF-e www.nfe.fazenda.gov.br/portal ou");
    	            $this->linha(14.8,    80, 120.0,  55.8-$extra,      "", "", "", "no site da Sefaz Autorizadora");
    	            $this->linha( 8.5,    80, 120.0,  64.6-$extra,  "BTRL",  "PROTOCOLO DE AUTORIZAÇÃO DE USO", "", $nNumero_Protocolo." ".troca_data_dma($cData_Protocolo)." às ".$cHora_Protocolo, "Arial,B,8,L");
                }
	        }


         // se for ambiente de homologação, escreve SEM VALOR FISCAL
	        if ($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == 2  or $this->visualizacao=='S')
			{
	            $this->pdf->SetTextColor(170,170,170);
	            $this->linha( 5.0, 190.0,  10.0, 210.0-$extra, "", "", "", "SEM VALOR FISCAL", "Arial,B,50,C");
	            $this->pdf->SetTextColor(0,0,0);
	        }


	        $this->linha(39.2,  25.4,  94.6,  25.4-$extra,  "BTRL", "DANFE",              "Arial,B,12,C"); // DANFE
	        $this->linha(39.2,  25.4,  94.6,    31-$extra,      "", "DOCUMENTO AUXILIAR", "Arial,B,6,C");
	        $this->linha(39.2,  25.4,  94.6,    33-$extra,      "", "DA NOTA FISCAL",     "Arial,B,6,C");
	        $this->linha(39.2,  25.4,  94.6,    35-$extra,      "", "ELETRÔNICA",         "Arial,B,6,C");

	        $this->linha(39.2,  25.4,  95.6,    42-$extra,      "", "0 - ENTRADA",        "Arial,B,6,L");
	        $this->linha(39.2,  25.4,  95.6,    45-$extra,      "", "1 - SAÍDA",          "Arial,B,6,L");
	        $Entrada_Saida = $ide->getElementsByTagName('tpNF')->item(0)->nodeValue;
	        $Entrada_Saida = " $Entrada_Saida ";
	        $this->linha(   7,     5,   110,    41-$extra,  "BTRL", "", "", $Entrada_Saida, "Arial,B,10,L");

	        $this->linha(39.2,  25.4,  95.6,    52-$extra,      "", "No.",   "Arial,B,6,L");
	        $this->linha(39.2,  25.4,  95.6,    56-$extra,      "", "SÉRIE", "Arial,B,5,L");
	        $this->linha(39.2,  25.4,  95.6,    60-$extra,      "", "FOLHA", "Arial,B,5,L");
	        $this->linha(39.2,  25.4,   101,  48.5-$extra,      "", "", "", $this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue), "Arial,B,10,L");
	        $this->linha(39.2,  25.4,   102,  52.2-$extra,      "", "", "", $ide->getElementsByTagName('serie')->item(0)->nodeValue);
	        $this->linha(39.2,  25.4,   102,  56.0-$extra,      "", "", "", "$nPagina_Atual/$nTotal_Paginas");

            $chave_acesso1=$this->formata_chave_acesso($chave_acesso);

	        $this->linha(14.8,    80, 120.0,  25.4-$extra,  "BTRL"); // codigo de barras da chave
	        $this->linha_itens( 9.6,    80, 120.0,  40.0-$extra, "", "CHAVE DE ACESSO" , "Arial,,7,L");
	        $this->linha( 9.6,    80, 120.0,  40.2-$extra,  "BTRL",  "", "", $chave_acesso1 , "Arial,B,9,L");
	        $this->linha( 8.5, 110.0,  10.0,  64.6-$extra,  "BTRL",  "NATUREZA DA OPERAÇÃO", "", utf8_decode($ide->getElementsByTagName("natOp")->item(0)->nodeValue));

	        $this->linha( 8.5,  63.4,  10.0,  73.1-$extra,  "BTRL", "INSCRIÇÃO ESTADUAL", "", utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue));
	        $this->linha( 8.5,  63.3,  73.4,  73.1-$extra,  "BTRL", "INSCRIÇÂO ESTADUAL DO SUBST. TRIB.", "", utf8_decode($emit->getElementsByTagName("IEST")->item(0)->nodeValue));
	        $this->linha( 8.5,  63.3, 136.7,  73.1-$extra,  "BTRL", "C.N.P.J.", "", $this->mask_cnpj($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue));

	        // destinatario/remetente
	        $this->linha( 4.2,    33,  10.0,  82.6-$extra,      "", "DESTINATÁRIO/REMETENTE", "Arial,B,6,L");
	        $this->linha( 8.5, 126.0,  10.0,  85.8-$extra,  "BTRL", "NOME/RAZÃO SOCIAL","", utf8_decode($dest->getElementsByTagName("xNome")->item(0)->nodeValue), "Arial,B,9,L");
	        $dest_doc = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($dest->getElementsByTagName("CPF")->item(0)->nodeValue);
	        $this->linha( 8.5,  45.0, 136.0,  85.8-$extra, "BTRLN", "C.N.P.J./C.P.F.","",   $dest_doc);
	        $this->linha( 8.5,  19.0,   181,  85.8-$extra,  "BTRL", "DATA EMISSÃO","",   troca_data_dma(substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue,0,10)));
	        $dest_ender = utf8_decode($enderDest->getElementsByTagName("xLgr")->item(0)->nodeValue);
	        $dest_ender.= (!empty($enderDest->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($enderDest->getElementsByTagName("nro")->item(0)->nodeValue) : null;
	        $dest_ender.= (!empty($enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
	        $this->linha( 8.5,  126.0,  10.0,  94.3-$extra,  "BTRL", "ENDEREÇO","",          $dest_ender);
            $this->linha( 8.5,  45.0, 136.0,  94.3-$extra,  "BTRL", "BAIRRO/DISTRITO","",   utf8_decode($enderDest->getElementsByTagName("xBairro")->item(0)->nodeValue));
	        $this->linha( 8.5,  19,   181,  94.3-$extra, "BTRLN", "DATA SAÍDA/ENT","",troca_data_dma(substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue,0,10)));
	        $this->linha( 8.5,  74.0,  10.0, 102.8-$extra,  "BTRL", "MUNICÍPIO","",         utf8_decode($enderDest->getElementsByTagName("xMun")->item(0)->nodeValue));
	        $this->linha( 8.5,  35.0,  84.0, 102.8-$extra,  "BTRL", "FONE/FAX","",          utf8_decode($enderDest->getElementsByTagName("fone")->item(0)->nodeValue));
	        $this->linha( 8.5,  11.0, 119.0, 102.8-$extra,  "BTRL", "UF","",                utf8_decode($enderDest->getElementsByTagName("UF")->item(0)->nodeValue));
            $this->linha( 8.5,  13.0, 130.0,  102.8-$extra,  "BTRL", "CEP","",               utf8_decode($enderDest->getElementsByTagName("CEP")->item(0)->nodeValue));
	        $this->linha( 8.5,  38.0, 143.0, 102.8-$extra,  "BTRL", "INSCRIÇÃO ESTADUAL","",utf8_decode($dest->getElementsByTagName("IE")->item(0)->nodeValue));
	        
            //$Hora=$ide->getElementsByTagName("hSaiEnt")->item(0)->nodeValue;
            $Hora=substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 11, 8);
            if (empty($Hora))
            {
                $Hora=date("H:i:s");
            }
	        $this->linha( 8.5,  19.0,   181, 102.8-$extra, "BTRLN", "HORA DA SAÍDA","",     $Hora);

	        if ($nPagina_Atual==1)
			{
		        // faturas/duplicatas
		        if (count($dup)>0)
		        {
		           $this->linha( 4.2,    10,  10.0, 111.9,      "", "FATURA/DUPLICATAS", "Arial,B,6,L");
                   $this->linha( 8.5, 190.0,  10.0, 115.1,  "BTRL", "", "","");
  		           $dups = "";

                   $nValorLinhaDpl=112.1;
                   $nValorColunaDpl=10.0;
                   $nColunaDpl=1;
		           foreach ($dup as $k => $d)
				   {
				      $dups='';
		              $dups.= $dup->item($k)->getElementsByTagName('nDup')->item(0)->nodeValue." ";
		              $dups.= troca_data_dma($dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue);
		              $dups.= " R$".number_format($dup->item($k)->getElementsByTagName('vDup')->item(0)->nodeValue, 2, ",", ".")."    ";
                      $this->linha( 2.3, 47.0, $nValorColunaDpl, $nValorLinhaDpl,  "", "", "","$dups");
                      $nColunaDpl++;
                      $nValorColunaDpl+=47;
                      if ($nColunaDpl>4)
                      {
                         $nColunaDpl=1;
                         $nValorColunaDpl=10.0;
                         $nValorLinhaDpl+=2.5;
                      }
		           }
                }


		        // calculo do imposto
		        $this->linha( 4.2,    56,  10.0, 124.6,      "", "CÁLCULO DO IMPOSTO", "Arial,B,6,L");
                if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
                    $this->linha( 8.5,  30,  10.0, 127.8,  "BTRL", "BASE DE CÁLCULO DO ICMS",     "", number_format($ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R");
                    $this->linha( 8.5,  25,  40.0, 127.8,  "BTRL", "VALOR DO ICMS",               "", number_format($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
                    $this->linha( 8.5,  35,  65.0, 127.8,  "BTRL", "BASE DE CÁLCULO DO ICMS ST",  "", number_format($ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
                    $this->linha( 8.5,  30, 100.0, 127.8,  "BTRL", "VALOR DO ICMS ST",            "", number_format($ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
                    $this->linha( 8.5,  30, 130.0, 127.8,  "BTRL", "VALOR APROX. TRIBUTOS",            "", number_format($ICMSTot->getElementsByTagName("vTotTrib")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
                } else {
                    $this->linha( 8.5,  37.5,  10.0, 127.8,  "BTRL", "BASE DE CÁLCULO DO ICMS",     "", number_format($ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R");
                    $this->linha( 8.5,  37.5,  47.5, 127.8,  "BTRL", "VALOR DO ICMS",               "", number_format($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
                    $this->linha( 8.5,  37.5,  85.0, 127.8,  "BTRL", "BASE DE CÁLCULO DO ICMS ST",  "", number_format($ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
                    $this->linha( 8.5,  37.5, 122.5, 127.8,  "BTRL", "VALOR DO ICMS ST",            "", number_format($ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
                }
		        $this->linha( 8.5,  40.0, 160.0, 127.8,  "BTRL", "VALOR TOTAL DOS PRODUTOS",    "", number_format($ICMSTot->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0,  10.0, 136.3,  "BTRL", "VALOR DO FRETE",              "", number_format($ICMSTot->getElementsByTagName("vFrete")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0,  40.0, 136.3,  "BTRL", "VALOR DO SEGURO",             "", number_format($ICMSTot->getElementsByTagName("vSeg")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0,  70.0, 136.3,  "BTRL", "DESCONTO",                    "", number_format($ICMSTot->getElementsByTagName("vDesc")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0, 100.0, 136.3,  "BTRL", "DESPESAS ACESSÓRIAS",  "", number_format($ICMSTot->getElementsByTagName("vOutro")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0, 130.0, 136.3,  "BTRL", "VALOR DO IPI",                "", number_format($ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  40.0, 160.0, 136.3, "BTRLN", "VALOR TOTAL DA NOTA",         "", number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", "."), "Arial,B,10,R" );

		        // transportador/volumes transportados
		        $this->linha( 4.2,    52,  10.0, 145.8,      "", "TRANSPORTADOR/VOLUMES TRANSPORTADOS", "Arial,B,6,L");
		        if (is_object($transporta))
				{
		            $transp_doc     = (!empty($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($transporta->getElementsByTagName("CPF")->item(0)->nodeValue);
		            $transp_xNome   = utf8_decode($transporta->getElementsByTagName("xNome")->item(0)->nodeValue);
		            $transp_xEnder  = utf8_decode($transporta->getElementsByTagName("xEnder")->item(0)->nodeValue);
		            $transp_xMun    = utf8_decode($transporta->getElementsByTagName("xMun")->item(0)->nodeValue);
		            $transp_UF      = utf8_decode($transporta->getElementsByTagName("UF")->item(0)->nodeValue);
		            $transp_IE      = utf8_decode($transporta->getElementsByTagName("IE")->item(0)->nodeValue);
		        }
		        if (is_object($veicTransp))
				{
		            $veic_RNTC      = utf8_decode($veicTransp->getElementsByTagName("RNTC")->item(0)->nodeValue);
		            $veic_placa     = utf8_decode($veicTransp->getElementsByTagName("placa")->item(0)->nodeValue);
		            $veic_UF        = utf8_decode($veicTransp->getElementsByTagName("UF")->item(0)->nodeValue);
		        }
		        $this->linha( 8.5,  75.0,  10.0,   149,  "BTRL", "RAZÃO SOCIAL",        "", $transp_xNome);
		        $this->linha( 8.5,  31.0,  85.0,   149,  "BTRL", "FRETE POR CONTA DE",  "");
		        // Alumifix: na alumifix, versão antiga, 1 era emitente, 2 era destinatário
		        if ($Banco_Dados == 'e004') {
			        $this->linha( 8.5,  31.0,  85.0,   152,      "", "1 - Emitente",  "");
			        $this->linha( 8.5,  31.0,  85.0,   154,      "", "2 - Destinatário",  "");
				} else { 
					$this->linha( 8.5,  31.0,  85.0,   152,      "", "0-Emitente 2-Terceiros",  "");
			        $this->linha( 8.5,  31.0,  85.0,   154,      "", "1-Dest/Rem 9-Sem Frete",  "");
				}
		        $this->linha( 3.5,   4.0, 110.0, 152.5,  "BTRL", "");
		        $Modalidade_Frete=$transp->getElementsByTagName("modFrete")->item(0)->nodeValue;
		        if ($Modalidade_Frete=='0')
		        {
		        	$Modalidade_Frete='O';
		        }
		        $this->linha( 3.5,   4.0, 110.2, 149.2,      "", "",                    "", "$Modalidade_Frete");
		        $this->linha( 8.5,  16.0, 116.0,   149,  "BTRL", "CÓDIGO ANTT",         "", $veic_RNTC);
		        $this->linha( 8.5,  21.0, 132.0,   149,  "BTRL", "PLACA DO VEÍCULO",    "", $veic_placa);
		        $this->linha( 8.5,   8.0, 153.0,   149,  "BTRL", "UF",                  "", $veic_UF);
		        $this->linha( 8.5,  39.0, 161.0,   149,  "BTRL", "C.N.P.J./C.P.F.",     "", $transp_doc);
		        $this->linha( 8.5,  80.0,  10.0, 157.5,  "BTRL", "ENDEREÇO",            "", $transp_xEnder);
		        $this->linha( 8.5,  63.0,  90.0, 157.5,  "BTRL", "MUNICÍPIO",           "", $transp_xMun);
		        $this->linha( 8.5,   8.0, 153.0, 157.5,  "BTRL", "UF",                  "", $transp_UF);
		        $this->linha( 8.5,  39.0, 161.0, 157.5,  "BTRL", "INSCRIÇÃO ESTADUAL",  "", $transp_IE);

		        // faz somatorios/concatenacoes dos volumes e coloca nos campos
		        foreach ($vols as $key => $v)
				{
		            $barra   = ($key != 0) ? "/" : "";
		            $qVol   += $v->getElementsByTagName("qVol")->item(0)->nodeValue;
		            $esp    .= $barra.$v->getElementsByTagName("esp")->item(0)->nodeValue;
		            $marca  .= $barra.$v->getElementsByTagName("marca")->item(0)->nodeValue;
		            $nVol   .= $barra.$v->getElementsByTagName("nVol")->item(0)->nodeValue;
		            $pesoB  += $v->getElementsByTagName("pesoB")->item(0)->nodeValue;
		            $pesoL  += $v->getElementsByTagName("pesoL")->item(0)->nodeValue;
		        }

		        $this->linha( 8.5,  26.0,  10.0,   166,  "BTRL", "QNTDE",          "", number_format($qVol, 2, ",", "."), "Arial,B,8,R");
		        $this->linha( 8.5,  27.0,  36.0,   166,  "BTRL", "ESPÉCIE",             "", utf8_decode($esp), "Arial,B,8,L");
		        $this->linha( 8.5,  27.0,  63.0,   166,  "BTRL", "MARCA",               "", utf8_decode($marca), "Arial,B,8,L");
		        $this->linha( 8.5,  46.0,  90.0,   166,  "BTRL", "NUMERAÇÃO",           "", utf8_decode($nVol), "Arial,B,8,L");
		        $this->linha( 8.5,  32.0,   136,   166,  "BTRL", "PESO BRUTO",          "", number_format($pesoB, 3, ",", "."), "Arial,B,8,R");
		        $this->linha( 8.5,  32.0, 168.0,   166,  "BTRL", "PESO LÍQUIDO",        "", number_format($pesoL, 3, ",", "."), "Arial,B,8,R");
		        
	            $extra_altura_itens=0;
   			}
   			else
   			{
	            $extra += 50;
	            $extra_altura_itens=115;
   			}

	        if (!$this->issqn and $nPagina_Atual>1)
			{
	            $extra += 12.7;
	        }

            if ($mT007[C004_Codigo_Regime_Tributario]=='3'){
                $Titulo_CST = "CST";
                $Fonte_CST = 6;
            } else {
                $Titulo_CST = "CSOSN";
                $Fonte_CST = 5;
            }

	        // dados dos produtos/servicos
	        $this->linha( 4.2,    40,  10.0, 175.5-$extra,     "", "DADOS DOS PRODUTOS/SERVIÇOS", "Arial,B,6,L");
	        //$this->linha(78.0, 190.0,  10.0, 178.7-$extra, "BTRL", "");
	        $this->linha(2.6, 190.0, 10.0, 178.7-$extra, "BTRL",  "");
            if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
    	        $this->linha(77.0+$extra_altura_itens,  15.0,  10.0, 178.7-$extra, "BTRL", "CÓDIGO",    "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,  52.0,  25.0, 178.7-$extra, "BTRL", "DESCRIÇÃO", "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,   9.0,  77.0, 178.7-$extra, "BTRL", "NCM/SH",    "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,   5.0,  86.0, 178.7-$extra, "BTRL", "$Titulo_CST",       "Arial,,$Fonte_CST,C");
    	        $this->linha(77.0+$extra_altura_itens,   5.3, 91.0, 178.7-$extra, "BTRL", "CFOP",      "Arial,,6,L");
    	        $this->linha(77.0+$extra_altura_itens,   4.7, 96.3, 178.7-$extra, "BTRL", " UN.",      "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,  13.5, 101.0, 178.7-$extra, "BTRL", "QUANT.",    "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,  13.0, 114.5, 178.7-$extra, "BTRL", "V.UNIT.",   "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,  13.0, 127.5, 178.7-$extra, "BTRL", "V.TOTAL",   "Arial,,6,C");
                // add tributo
                $this->linha(77.0+$extra_altura_itens,  12.0, 140.5, 178.7-$extra, "BTRL", " V.APR.TRIB.",   "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,  11.5, 152.5, 178.7-$extra, "BTRL", "BC.ICMS",   "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,  11.0, 164.0, 178.7-$extra, "BTRL", "V.ICMS",    "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,  11.0, 175.0, 178.7-$extra, "BTRL", "V.IPI",     "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,   7.0, 186.0, 178.7-$extra, "BTRL", "%ICMS",     "Arial,,6,C");
    	        $this->linha(77.0+$extra_altura_itens,   7.0, 193.0, 178.7-$extra, "BTRL", "%IPI",      "Arial,,6,C");
            } else {
                $this->linha(77.0+$extra_altura_itens,  15.0,  10.0, 178.7-$extra, "BTRL", "CÓDIGO",    "Arial,,6,C");
                $this->linha(77.0+$extra_altura_itens,  62.0,  25.0, 178.7-$extra, "BTRL", "DESCRIÇÃO", "Arial,,6,C");
                $this->linha(77.0+$extra_altura_itens,   9.0,  87.0, 178.7-$extra, "BTRL", "NCM/SH",    "Arial,,6,C");
                $this->linha(77.0+$extra_altura_itens,   5.0,  96.0, 178.7-$extra, "BTRL", "$Titulo_CST",       "Arial,,$Fonte_CST,C");

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
            }
			$i=0;
	        for ($x=0;$nTotal_Linhas_Impressas<$nItensPorPagina;$x++)
			{
				if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)
				{
					break;
				}

	            $prod = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("prod")->item(0);
	            $imposto = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("imposto")->item(0);
	            $ICMS = $imposto->getElementsByTagName("ICMS")->item(0);
	            $IPI  = $imposto->getElementsByTagName("IPI")->item(0);
	            $cDescricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
                $cDescricao = trim($cDescricao);
	            $cOrdem_Compra = $prod->getElementsByTagName("xPed")->item(0)->nodeValue;
                $cOrdem_Compra = trim($cOrdem_Compra);
	            $cItem_Ordem_Compra = $prod->getElementsByTagName("nItemPed")->item(0)->nodeValue;
                $cItem_Ordem_Compra = trim($cItem_Ordem_Compra);

				$cReferencia=$prod->getElementsByTagName("cProd")->item(0)->nodeValue;
				$nCodigo_Barras=$prod->getElementsByTagName("cEAN")->item(0)->nodeValue;
				$nQuantidade=$prod->getElementsByTagName("qCom")->item(0)->nodeValue;
				
				if (!empty($nCodigo_Barras))
				{
				   $cReferencia=$nCodigo_Barras;
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
                if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
	               $this->linha_itens(65.0,   9.0,  77.0, 178.9+($i*2.3)-$extra, "", $NCM,                    "Arial,B,6,C");
               } else {
                    $this->linha_itens(65.0,   9.0,  87.0, 178.9+($i*2.3)-$extra, "", $NCM,                    "Arial,B,6,C");
               }
                if (!empty($ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue))
                {
                    if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
	                   $this->linha_itens(65.0,   5.0,  86.0, 178.9+($i*2.3)-$extra, "", $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue, "Arial,B,7,L");
                    } else {
                        $this->linha_itens(65.0,   5.0,  96.0, 178.9+($i*2.3)-$extra, "", $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue, "Arial,B,7,L");
                    }
                }
                else
                {
                    if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
                        $this->linha_itens(65.0,   5.0,  86.0, 178.9+($i*2.3)-$extra, "", $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CST")->item(0)->nodeValue, "Arial,B,7,L");
                    } else {
	                   $this->linha_itens(65.0,   5.0,  96.0, 178.9+($i*2.3)-$extra, "", $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CST")->item(0)->nodeValue, "Arial,B,7,L");
                   }
                }
                if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
                    $this->linha_itens(65.0,   5.3, 91.0, 178.9+($i*2.3)-$extra, "", $prod->getElementsByTagName("CFOP")->item(0)->nodeValue,                               "Arial,B,7,C");
                    $this->linha_itens(65.0,   4.7, 96.3, 178.9+($i*2.3)-$extra, "", utf8_decode($prod->getElementsByTagName("uCom")->item(0)->nodeValue),                  "Arial,B,7,C");
                    $this->linha_itens(65.0,  13.5, 101.0, 178.9+($i*2.3)-$extra, "", number_format($nQuantidade, $this->casas_decimais_quantidade, ",", "."),   "Arial,B,7,R");
                    $this->linha_itens(65.0,  13.5, 114.5, 178.9+($i*2.3)-$extra, "", number_format($prod->getElementsByTagName("vUnCom")->item(0)->nodeValue, $this->casas_decimais_preco, ",", "."), "Arial,B,7,R");
                    $this->linha_itens(65.0,  13.5, 127.5, 178.9+($i*2.3)-$extra, "", number_format($prod->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", "."),  "Arial,B,7,R");

                    $this->linha_itens(65.0,  12.5, 140.5, 178.9+($i*2.3)-$extra, "", number_format($imposto->getElementsByTagName("vTotTrib")->item(0)->nodeValue, 2, ",", "."),  "Arial,B,7,R");

                    $this->linha_itens(65.0,  12.0, 152.5, 178.9+($i*2.3)-$extra, "", number_format($ICMS->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", "."),    "Arial,B,7,R");
                    $this->linha_itens(65.0,  11.5, 164.0, 178.9+($i*2.3)-$extra, "", number_format($ICMS->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", ""),  "Arial,B,7,R");
                    $vIPI = (!empty($IPI)) ? $IPI->getElementsByTagName("vIPI")->item(0)->nodeValue : 0;
                    $this->linha_itens(65.0,  11.5, 175.0, 178.9+($i*2.3)-$extra, "", number_format($vIPI, 2, ",", ""),    "Arial,B,7,R");
                    $this->linha_itens(65.0,   7.5, 186.0, 178.9+($i*2.3)-$extra, "", number_format($ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue, 2, ",", "."),  "Arial,B,7,R");
                    $pIPI = (!empty($IPI)) ? $IPI->getElementsByTagName("pIPI")->item(0)->nodeValue : 0;
                    $this->linha_itens(65.0,   7.5, 193.0, 178.9+($i*2.3)-$extra, "", number_format($pIPI, 2, ",", "."),    "Arial,B,7,R");
                } else {
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
                }
                $i=$linha_descricao;
	        }

			for ($x=$nTotal_Linhas_Impressas;$x<$nItensPorPagina;$x++)
			{
	            //$this->linha(65.0,  15.0,  10.0, 178.9+($i*2.3)-$extra, "", "",     "Arial,,5,L");
			}

	        if ($this->issqn and $nPagina_Atual==1)
			{
	            // calculo do issqn
	            $this->linha(4.2, 23.0,  10.0, 244.3+$extra,     "", "CÁLCULO DO ISSQN", "Arial,B,6,L");
	            $this->linha(8.5, 46.0,  10.0, 247.9+$extra, "BTRL", "INSCRIÇÃO MUNICIPAL");
	            $this->linha(8.5, 47.0,  56.0, 247.9+$extra, "BTRL", "VALOR TOTAL DOS SERVIÇOS");
	            $this->linha(8.5, 47.0, 103.0, 247.9+$extra, "BTRL", "BASE DE CÁLCULO DO ISSQN");
	            $this->linha(8.5, 50.0, 150.0, 247.9+$extra, "BTRL", "VALOR DO ISSQN");
	        }

            if ($nPagina_Atual==1)
            {
		        // dados adicionais
		        $this->linha( 4.2,  23.0,  10.0, 257.4+$extra,      "", "DADOS ADICIONAIS", "Arial,B,6,L");

		        $this->linha(30.7, 113.0,  10.0, 260.6+$extra,  "BTRL", "INFORMAÇÕES COMPLEMENTARES");
		        $this->linha(30.7,  77.0, 123.0, 260.6+$extra,  "BTRL", "RESERVADO AO FISCO");


		        $this->pdf->setFont("Arial", "B", "8");
		        $this->pdf->SetAutopagebreak(false);

                $DadosCompl="";
                /*
                if (!empty($mT007[C007_Primeiro_Nome]))
                {
                    $DadosCompl="Vendedor: ".$mT007[C007_Primeiro_Nome];
                }
                if ($mT007[T007_T005_Id]>0)
                {
                    if (!empty($DadosCompl))
                    {
                       $DadosCompl.=" - ";
                    }
                    $DadosCompl.="Pedido: ".$mT007[T007_T005_Id];
                } */

                if (!empty($DadosCompl))
                {
	                $this->pdf->setXY(2, 263+$extra);
	                $this->pdf->Cell(113, 0, $DadosCompl, 0, 0, 'L');
	                $extra+=3;
                }
                
		        if (is_object($infAdic))
				{
		            $infCpl     = $infAdic->getElementsByTagName("infCpl")->item(0)->nodeValue;
		            $infAdFisco = $infAdic->getElementsByTagName("infAdFisco")->item(0)->nodeValue;
		        }

		        // IMPRIME infCpl
		        for ($i=0; $i<=strlen($infCpl); $i++)
				{
		            $txt.= $infCpl[$i];
		            if ($this->pdf->GetStringWidth($txt) >= 132 || $i >= strlen($infCpl))
					{
		                $this->pdf->setXY(2, 263+$extra+($l*3));
		                $this->pdf->Cell(113, 0, $txt, 0, 0, 'L');
		                $txt = '';
		                $l++;
		            }
		        }
		        $LinhaAdicional=263+($l*3);

		        // IMPRIME infAdFisco
		        $l = 0;
                $quebras = 0;
		        for ($i=0; $i<=strlen($infAdFisco); $i++)
				{
		            $txt.= $infAdFisco[$i];
                    if(substr($txt, -1) == ';' && $quebras < 6){
                        $this->pdf->setXY(2, $LinhaAdicional+$extra+($l*3));
                        $this->pdf->Cell(75, 0, substr($txt, 0, -1), 0, 0, 'L');
                        $txt = '';
                        $l++;
                        $quebras++;
                    } else if ($this->pdf->GetStringWidth($txt) >= 132 || $i >= strlen($infAdFisco))
					{                    
		                $this->pdf->setXY(2, $LinhaAdicional+$extra+($l*3));
		                $this->pdf->Cell(77, 0, $txt, 0, 0, 'L');
		                $txt = '';
		                $l++;
		            }
		        }

			}

            $this->linha( 4.2,    40,  10.0, 292,     "", "HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR", "Arial,,7,L");

			//-- Faz a verificação pra fim de pagina
		    if ((($nTotal_Linhas_Impressas)>=$nItensPorPagina) and (($nImpressao+$nItensPorPagina)<$nTotal_Linhas))
		    {
		       $nTotal_Linhas_Impressas=0;
		       $extra=0;
			   $issqn_extra=0;
		       $nInicio+=$nItensPorPagina;
		       if ($nInicio<=$nTotal_Linhas)
		       {
		         // $this->pdf->AddPage();
			   }
		    }

		}
		
        $this->pdf->SetDisplayMode(90);
        $this->pdf->Output($this->nomearquivopdf, $this->tiposaidaarquivo);
        //return $this->pdf;
    }




    function geraHtml()
    {
        global $Banco_Dados;

        if (empty($this->xml))
        {
            return false;
        }
        
        $cClausula  = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " left join C020 on C020_C004_Id=T007_C004_Id ";
        $cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " left join D024 on D024_Id=T007_D024_Id ";
        $cClausula .= " where T007_Id='".$this->T007_Id."' ";
        $cSql_T007 = mysql_query($cClausula);
        $mT007 = mysql_fetch_array($cSql_T007);

        //Variaveis de configuração global
        $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$mT007['C004_Id']}';");
        while($rowC031 = mysql_fetch_assoc($sqlC031)){
            $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }

        $dom = new DomDocument;
        $dom->loadXML($this->xml);
        $infNFe     = $dom->getElementsByTagName("infNFe")->item(0);
        $ide        = $dom->getElementsByTagName("ide")->item(0);
        $emit       = $dom->getElementsByTagName("emit")->item(0);
        $dest       = $dom->getElementsByTagName("dest")->item(0);
        $enderEmit  = $dom->getElementsByTagName("enderEmit")->item(0);
        $enderDest  = $dom->getElementsByTagName("enderDest")->item(0);
        $det        = $dom->getElementsByTagName("det");
        $cobr       = $dom->getElementsByTagName("cobr")->item(0);
        $dup        = $dom->getElementsByTagName('dup');
        $ICMSTot    = $dom->getElementsByTagName("ICMSTot")->item(0);
        $transp     = $dom->getElementsByTagName("transp")->item(0);
        $transporta = $dom->getElementsByTagName("transporta")->item(0);
        $veicTransp = $dom->getElementsByTagName("veicTransp")->item(0);
        $vols       = $dom->getElementsByTagName("vol");
        $infAdic    = $dom->getElementsByTagName("infAdic")->item(0);
        $protNFe    = $dom->getElementsByTagName("protNFe")->item(0);

        // Linhas / Páginas
        $maxChars = 51;
        $this->linhas_primeira_pagina = 32;
        $this->linhas_segunda_pagina = 63;
        $nTotal_Linhas = 0;
        $nTotal_Itens_Contados = 0;
        $lineHeightGeral = 11;
        foreach ($det as $d)
        {
            $prod = $det->item($nTotal_Itens_Contados)->getElementsByTagName("prod")->item(0);
            $cDescricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
            $cDescricao = trim($cDescricao);
            $cOrdem_Compra = $prod->getElementsByTagName("xPed")->item(0)->nodeValue;
            $cOrdem_Compra = trim($cOrdem_Compra);
            $cInfAdProd = $det->item($nTotal_Itens_Contados)->getElementsByTagName("infAdProd")->item(0)->nodeValue;
            $cInfAdProd = trim($cInfAdProd);
            
            $nTotal_Linhas++;
            if (strlen($cDescricao) > ($maxChars * 2)) {
                $nTotal_Linhas++;
                $nTotal_Linhas++;
            }
            else if (strlen($cDescricao) > $maxChars)
            {
               $nTotal_Linhas++;
            }
            if (!empty($cOrdem_Compra))
            {
               $nTotal_Linhas++;
            }
            if (!empty($cInfAdProd)) {
                $nTotal_Linhas++;
            }
            $nTotal_Itens_Contados++;
        }
        $nInicio=0;
        $nPagina_Atual=0;
        $nItensPorPagina = $this->linhas_primeira_pagina;

        $nTotal_Paginas = 1;
        //echo $nTotal_Linhas;
        //die();
        if ($nTotal_Linhas > $this->linhas_primeira_pagina) {
            $nTotal_Paginas += ceil(($nTotal_Linhas - 32) / $this->linhas_segunda_pagina);
        }
        
        //-- Total de Linhas Impressas por página
        $nTotal_Linhas_Impressas=0;
        //-- Total de Linhas Impressas no Total
        $nTotal_Itens_Impressos=0;

for ($pagina = 1; $pagina <= $nTotal_Paginas; $pagina++) {

        // Cabeçalho 1
        $xNome = utf8_decode($emit->getElementsByTagName("xNome")->item(0)->nodeValue);
        $vlNF = '';
        if($_REQUEST['vlrNF'] == 'S'){
            //$vlNF = "R$ " . number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", ".");
        }

        // Empresa P1
        if (!empty($this->logomarca) && is_file('/tmp/'.$this->logomarca))
        {
            $Img_nfe = imagecreatefromjpeg('/tmp/'.$this->logomarca);
            $Largura_Imagem=imagesx($Img_nfe);
            $Altura_Imagem=imagesy($Img_nfe);

            if ($Altura_Imagem > 130) {
                $porcentagem = (round((130 * 100) / $Altura_Imagem) - 100) / 100;
                if ($porcentagem < 0) {
                    $porcentagem = $porcentagem + 1;
                }
                $Largura_Imagem = $Largura_Imagem * $porcentagem;
                $Altura_Imagem = 130;
            }
            if ($Largura_Imagem < 0 || $Largura_Imagem > 260) {
                //$Largura_Imagem=imagesx($Img_nfe);
                //$Altura_Imagem=imagesy($Img_nfe);
                $porcentagem = (round((260 * 100) / $Largura_Imagem) - 100) / 100;
                if ($porcentagem <= 0) {
                    $porcentagem = $porcentagem + 1;
                }
                $Largura_Imagem = 260;
                $Altura_Imagem = $Altura_Imagem * $porcentagem;;
            }
        }
        $endereco_emitente = substr($enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue,0,60);
        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".$enderEmit->getElementsByTagName("nro")->item(0)->nodeValue : null;
        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
        $nome_empresa=$emit->getElementsByTagName("xNome")->item(0)->nodeValue;
        if (strlen($nome_empresa)<=40)
        {
            $nome_empresa = utf8_decode(substr($nome_empresa,0,46));
        }
        else
        {
            $nome_empresa = utf8_decode($nome_empresa);
        }
        $endereco_emitente .= "<br/>" . utf8_decode($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $endereco_emitente .= "<br/>" . utf8_decode($enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue).'/'.utf8_decode($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue);
        $endereco_emitente .= "<br/>" . "CEP: ".utf8_decode($emit->getElementsByTagName("CEP")->item(0)->nodeValue)." - FONE: ".utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue);
        if (!empty($mT007[C004_E_Mail]))
        {
            $endereco_emitente .= "<br/>" . "$mT007[C004_E_Mail]";
        }
        if (!empty($mT007[C004_Site]))
        {
            $endereco_emitente .= "<br/>" . "$mT007[C004_Site]";
        }

        // Empresa P2
        $nrNf = $this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue);
        $serieNf = $ide->getElementsByTagName('serie')->item(0)->nodeValue;

        // Empresa P3
        $protocolo = '';
        $chave_acesso = str_replace('NFe', '', $infNFe->getAttribute("Id"));
        $chave_acesso1=$this->formata_chave_acesso($chave_acesso);
        $this->pdf->SetFillColor(0,0,0);
        $this->pdf->Code128(145, 23.8-$extra, $chave_acesso, 75, 12);

        $empresaP23 = '';
        if ($ide->getElementsByTagName("tpEmis")->item(0)->nodeValue != 1 and $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue != 3)
        {
            // contingencia
            $empresaP23 .= "DANFE em Contingência Impresso em decorrência de problemas têcnicos<br />";
            
            $dados_nfe = $this->array_uf[$enderDest->getElementsByTagName("UF")->item(0)->nodeValue];
            $dados_nfe.= $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue;
            $dados_nfe.= $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
            $dados_nfe.= sprintf("%014s", str_replace(',', '', number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", "")));
            $dados_nfe.= 1; // ICMSp
            $dados_nfe.= 2; // ICMSs
            $data_emissao = troca_data_dma($ide->getElementsByTagName("dEmi")->item(0)->nodeValue);
            $dados_nfe.= $data_emissao[0].$data_emissao[1];
            $dados_nfe.= $this->calcula_dv($dados_nfe);

            $empresaP23 .= "DADOS DA NF-e<br />";
            $empresaP23 .= $dados_nfe;

        }
        else
        {
            if ($mT007[C020_NFe_Treinamento]=='S' or $this->visualizacao=='S')
            {
                //
            }
            else
            {
                // normal
                $empresaP23 .= "Consulta de autenticidade no portal nacional da NF-e www.nfe.fazenda.gov.br/portal ou no site da Sefaz Autorizadora<br />";
                $nNumero_Protocolo=utf8_decode($protNFe->getElementsByTagName("nProt")->item(0)->nodeValue);
                $cData_Protocolo=utf8_decode($protNFe->getElementsByTagName("dhRecbto")->item(0)->nodeValue);
                list($cData_Protocolo,$cHora_Protocolo)=explode("T",$cData_Protocolo);
                $protocolo = $nNumero_Protocolo." ".troca_data_dma($cData_Protocolo)." às ".$cHora_Protocolo;
            }
        }

        // Extra
        $extraNatureza = utf8_decode($ide->getElementsByTagName("natOp")->item(0)->nodeValue);
        $extraIE = utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue);
        $extraIESubst = utf8_decode($emit->getElementsByTagName("IEST")->item(0)->nodeValue);
        $extraCNPJ = $this->mask_cnpj($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue);

        // Cliente
        $clienteNome = utf8_decode($dest->getElementsByTagName("xNome")->item(0)->nodeValue);
        $clienteCnpj = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($dest->getElementsByTagName("CPF")->item(0)->nodeValue);
        $clienteDataEmissao = troca_data_dma(substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 0, 10));
        $clienteEndereco = utf8_decode($enderDest->getElementsByTagName("xLgr")->item(0)->nodeValue);
        $clienteEndereco .= (!empty($enderDest->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($enderDest->getElementsByTagName("nro")->item(0)->nodeValue) : null;
        $clienteEndereco .= (!empty($enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
        $clienteBairro = utf8_decode($enderDest->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $clienteDataSaida = troca_data_dma(substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 0, 10));
        $clienteMunicipio = utf8_decode($enderDest->getElementsByTagName("xMun")->item(0)->nodeValue);
        $clienteFone = utf8_decode($enderDest->getElementsByTagName("fone")->item(0)->nodeValue);
        $clienteUF = utf8_decode($enderDest->getElementsByTagName("UF")->item(0)->nodeValue);
        $clienteCEP = utf8_decode($enderDest->getElementsByTagName("CEP")->item(0)->nodeValue);
        $clienteIE = utf8_decode($dest->getElementsByTagName("IE")->item(0)->nodeValue);
        //$Hora=$ide->getElementsByTagName("hSaiEnt")->item(0)->nodeValue;
        $Hora=substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 11, 8);
        if (empty($Hora))
        {
            $Hora=date("H:i:s");
        }
        $clienteHoraSaira = $Hora;
        $nPagina_Atual = 1;
        if ($nPagina_Atual==1)
        {
             // faturas/duplicatas
            $dups = "";
            if (count($dup)>0)
            {
               $dups = "";

               $nValorLinhaDpl=112.1;
               $nValorColunaDpl=10.0;
               $nColunaDpl=1;
               foreach ($dup as $k => $d)
               {
                  //$dups.= $dup->item($k)->getElementsByTagName('nDup')->item(0)->nodeValue." ";
                  $dups.= troca_data_dma($dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue);
                  $dups.= "&nbsp;R$".number_format($dup->item($k)->getElementsByTagName('vDup')->item(0)->nodeValue, 2, ",", ".")."&nbsp;&nbsp; ";
               }
            }
        }

        // Impostos
        //if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
        $vBC = number_format($ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", ".");
        $vICMS = number_format($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", ".");
        $vBCST = number_format($ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue, 2, ",", ".");
        $vST = number_format($ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue, 2, ",", ".");
        $vTotTrib = number_format($ICMSTot->getElementsByTagName("vTotTrib")->item(0)->nodeValue, 2, ",", ".");
        $vProd = number_format($ICMSTot->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", ".");
        $vFrete = number_format($ICMSTot->getElementsByTagName("vFrete")->item(0)->nodeValue, 2, ",", ".");
        $vSeg = number_format($ICMSTot->getElementsByTagName("vSeg")->item(0)->nodeValue, 2, ",", ".");
        $vDesc = number_format($ICMSTot->getElementsByTagName("vDesc")->item(0)->nodeValue, 2, ",", ".");
        $vOutro = number_format($ICMSTot->getElementsByTagName("vOutro")->item(0)->nodeValue, 2, ",", ".");
        $vIPITotal = number_format($ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue, 2, ",", ".");
        $vNF = number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", ".");

        // Transportadora
        if (is_object($transporta))
        {
            $transp_doc     = (!empty($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($transporta->getElementsByTagName("CPF")->item(0)->nodeValue);
            $transp_xNome   = utf8_decode($transporta->getElementsByTagName("xNome")->item(0)->nodeValue);
            $transp_xEnder  = utf8_decode($transporta->getElementsByTagName("xEnder")->item(0)->nodeValue);
            $transp_xMun    = utf8_decode($transporta->getElementsByTagName("xMun")->item(0)->nodeValue);
            $transp_UF      = utf8_decode($transporta->getElementsByTagName("UF")->item(0)->nodeValue);
            $transp_IE      = utf8_decode($transporta->getElementsByTagName("IE")->item(0)->nodeValue);
        }
        if (is_object($veicTransp))
        {
            $veic_RNTC      = utf8_decode($veicTransp->getElementsByTagName("RNTC")->item(0)->nodeValue);
            $veic_placa     = utf8_decode($veicTransp->getElementsByTagName("placa")->item(0)->nodeValue);
            $veic_UF        = utf8_decode($veicTransp->getElementsByTagName("UF")->item(0)->nodeValue);
        }
        // Alumifix: na alumifix, versão antiga, 1 era emitente, 2 era destinatário
        /*if ($Banco_Dados == 'e004') {
            $this->linha( 8.5,  31.0,  85.0,   152,      "", "1 - Emitente",  "");
            $this->linha( 8.5,  31.0,  85.0,   154,      "", "2 - Destinatário",  "");
        } else { 
            $this->linha( 8.5,  31.0,  85.0,   152,      "", "0-Emitente 2-Terceiros",  "");
            $this->linha( 8.5,  31.0,  85.0,   154,      "", "1-Dest/Rem 9-Sem Frete",  "");
        }*/
        $Modalidade_Frete=$transp->getElementsByTagName("modFrete")->item(0)->nodeValue;
        /*if ($Modalidade_Frete=='0')
        {
            $Modalidade_Frete='O';
        }*/
        // faz somatorios/concatenacoes dos volumes e coloca nos campos
        foreach ($vols as $key => $v)
        {
            $barra   = ($key != 0) ? "/" : "";
            $qVol   += $v->getElementsByTagName("qVol")->item(0)->nodeValue;
            $esp    .= $barra.$v->getElementsByTagName("esp")->item(0)->nodeValue;
            $marca  .= $barra.$v->getElementsByTagName("marca")->item(0)->nodeValue;
            $nVol   .= $barra.$v->getElementsByTagName("nVol")->item(0)->nodeValue;
            $pesoB  += $v->getElementsByTagName("pesoB")->item(0)->nodeValue;
            $pesoL  += $v->getElementsByTagName("pesoL")->item(0)->nodeValue;
        }
        $qVol = number_format($qVol, 2, ",", ".");
        $esp = utf8_decode($esp);
        $marca = utf8_decode($marca);
        $nVol = utf8_decode($nVol);
        $pesoB = number_format($pesoB, 3, ",", ".");
        $pesoL = number_format($pesoL, 3, ",", ".");

        /*
        * LOOP PRODUTOS
        */
        if ($mT007[C004_Codigo_Regime_Tributario]=='3'){
            $Titulo_CST = "CST";
        } else {
            $Titulo_CST = "CSOSN";
        }

        $tabelaProdutos = '';
        $valorAproxTrib = '';
        if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
            $valorAproxTrib = "<td style=\"height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;\">&nbsp;</td>";
        }
        $vazio = <<<EOT
<tr>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    {$valorAproxTrib}
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px;">&nbsp;</td>
</tr>
EOT;

        $nTotal_Itens_Impressos_Tmp = 0;
        for ($i = 0; $i < $nTotal_Itens_Contados; $i++) {
            if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)
            {
                break;
            }
            $prod = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("prod")->item(0);
            $imposto = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("imposto")->item(0);
            $ICMS = $imposto->getElementsByTagName("ICMS")->item(0);
            $IPI  = $imposto->getElementsByTagName("IPI")->item(0);
            $cDescricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
            $cDescricao = trim($cDescricao);
            $cInfAdProd = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("infAdProd")->item(0)->nodeValue;
            $cInfAdProd = trim($cInfAdProd);
            $cOrdem_Compra = $prod->getElementsByTagName("xPed")->item(0)->nodeValue;
            $cOrdem_Compra = trim($cOrdem_Compra);
            $cItem_Ordem_Compra = $prod->getElementsByTagName("nItemPed")->item(0)->nodeValue;
            $cItem_Ordem_Compra = trim($cItem_Ordem_Compra);

            $cReferencia=$prod->getElementsByTagName("cProd")->item(0)->nodeValue;
            $nCodigo_Barras=$prod->getElementsByTagName("cEAN")->item(0)->nodeValue;
            $nQuantidade=$prod->getElementsByTagName("qCom")->item(0)->nodeValue;

            if (!empty($nCodigo_Barras) && $g['C031']['imprimirNoDanfeEanNoLugarDoCodigo']=='S')
            {
               $cReferencia=$nCodigo_Barras;
            }

            $cReferencia = utf8_encode($cReferencia);
            $cDescricao = utf8_encode($cDescricao);
            $cInfAdProd = utf8_encode($cInfAdProd);
            $NCM = (!empty($prod->getElementsByTagName("NCM")->item(0)->nodeValue)) ? $prod->getElementsByTagName("NCM")->item(0)->nodeValue : '';
	    $CEST = (!empty($prod->getElementsByTagName("CEST")->item(0)->nodeValue)) ? $prod->getElementsByTagName("CEST")->item(0)->nodeValue : '';
            if (!empty($ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue))
            {
                $CST = $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue;
            }
            else
            {
                $CST = $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CST")->item(0)->nodeValue;
            }
            $CFOP = $prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
            $unidade = utf8_decode($prod->getElementsByTagName("uCom")->item(0)->nodeValue);
            $valor_unitario = $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue;
            $valor_total = number_format($prod->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", ".");
            $valor_total_tributos = number_format($imposto->getElementsByTagName("vTotTrib")->item(0)->nodeValue, 2, ",", ".");
            $bc_icms = number_format($ICMS->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", ".");
            $v_icms = number_format($ICMS->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", "");
            $vIPI = (!empty($IPI)) ? $IPI->getElementsByTagName("vIPI")->item(0)->nodeValue : 0;
            $vICMSST = $ICMS->getElementsByTagName("vICMSST")->item(0)->nodeValue;
            $p_icms = number_format($ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue,0);
            $pIPI = (!empty($IPI)) ? $IPI->getElementsByTagName("pIPI")->item(0)->nodeValue : 0;
            $pIPI = number_format($pIPI, 0);
            $CEST = $prod->getElementsByTagName("CEST")->item(0)->nodeValue;
            $STUnit = round($vICMSST / $nQuantidade,2);
	    $IPIUnit = round($vIPI / $nQuantidade,2);
            $valor_total = number_format(($valor_unitario + $IPIUnit + $STUnit) * $nQuantidade,2, ",",".");
	    $valor_bruto = number_format($valor_unitario + $IPIUnit + $STUnit, 2, ",", ".");
	    $STUnit = number_format( $STUnit , 2, ",", ".");
	    $IPIUnit = number_format( $IPIUnit, 2, ",", ".");
            $quantidade = number_format($nQuantidade,0);
            $vICMSST = number_format($vICMSST, 2, ",", "");
            $vIPI = number_format($vIPI, 2, ",", "");
            $valor_unitario = number_format($prod->getElementsByTagName("vUnCom")->item(0)->nodeValue, 2, ",", ".");
	    //$valor_total = number_format(($valor_unitario + $IPIUnit + $STUnit) * $nQuantidade,$this->casas_decimais_preco, ",",".");

            $maxChars = 51;
            $linhasAdicionadas = 0;
            if (strlen($cDescricao) > ($maxChars * 2)) {
                $lineHeight = (16 * 3) - 4;
                $cDescricao = substr($cDescricao, 0, $maxChars) . '<br />' . substr($cDescricao, $maxChars, $maxChars) . '<br />' . substr($cDescricao, ($maxChars * 2), $maxChars);
                $nTotal_Linhas_Contados += 3;
                $nTotal_Itens_Impressos_Tmp++;
                $nTotal_Itens_Impressos_Tmp++;
                $linhasAdicionadas++;
                $linhasAdicionadas++;
            } else if (strlen($cDescricao) > $maxChars) {
                $lineHeight = (16 * 2) - 2;
                $cDescricao = substr($cDescricao, 0, $maxChars) . '<br />' . substr($cDescricao, $maxChars, $maxChars);
                $nTotal_Linhas_Contados += 2;
                $nTotal_Itens_Impressos_Tmp++;
                $linhasAdicionadas++;
            } else {
                $lineHeight = 16;
                $nTotal_Linhas_Contados += 1;
            }

            // Ordem de Compra
            if (!empty($cOrdem_Compra))
            {
                $cOrdem_Compra=' > Ped.Compra: '.$cOrdem_Compra;
                if (!empty($cItem_Ordem_Compra))
                {
                    $cOrdem_Compra.=' - Item: '.$cItem_Ordem_Compra;
                }
                $cDescricao .= '<br />' . utf8_encode($cOrdem_Compra);
                $nTotal_Linhas_Contados += 1;
                $nTotal_Itens_Impressos_Tmp++;
                $linhasAdicionadas++;
                $lineHeight += 16;
            }
            $mT007['C020_Flag_Codigo_CEST'] = empty($mT007['C020_Flag_Codigo_CEST']) ? 'N' : $mT007['C020_Flag_Codigo_CEST'];
            // Código CEST
            if(@$mT007['C020_Flag_Codigo_CEST']=='S' && (!empty($CEST))){
                $cCEST = " CEST: ".$CEST;
            } else {
                $cCEST="";
            }
            $cDescricao .= utf8_encode($cCEST);

            $lineHeightTmp = $lineHeight;
            $lineHeightGeralTmp = $lineHeightGeral;
            $border = '';
            if (!empty($cInfAdProd)) {
                $lineHeightTmp = $lineHeight - 1;
                $lineHeightGeralTmp = $lineHeightGeral - 1;
                $border = 'border-bottom: 1px solid black;';
            }

            $valorAproxTrib = '';
            if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
                $valorAproxTrib = <<<EOT
<td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; text-align: right;{$border}">{$valor_total_tributos}</td>
EOT;
            }
// Personalizacao GP
            $tabelaProdutos .= <<<EOT
<tr style="font-weight: bolder;">
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; text-align: center;">{$cReferencia}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black;">{$cDescricao}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;font-size:10px;">{$NCM}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;font-size:10px;">{$CEST}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;font-size:10px;">{$CST}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;">{$CFOP}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;">{$unidade}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$quantidade}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$valor_unitario}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$vIPI}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$STUnit}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$valor_bruto}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$valor_total}</td>
    {$valorAproxTrib}
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$bc_icms}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$v_icms}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;">{$p_icms}</td>
    <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; {$border} text-align: center;">{$pIPI}</td>
</tr>
EOT;
            if (!empty($cInfAdProd)) {
                $lineHeightTmp = $lineHeight - 1;
                $lineHeightGeralTmp = $lineHeightGeral - 1;
                $tabelaProdutos .= <<<EOT
<tr style="font-weight: bolder;">
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black;">&nbsp;</td>
    <td colspan=100 style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-bottom: 1px solid black;">{$cInfAdProd}</td>
</tr>
EOT;
                $nTotal_Linhas_Contados += 1;
                $nTotal_Itens_Impressos_Tmp++;
                $linhasAdicionadas++;
            }
            
            $nTotal_Itens_Impressos++;
            $nTotal_Itens_Impressos_Tmp++;

            if ($pagina == 1 && ( (($nTotal_Itens_Impressos_Tmp - $linhasAdicionadas) % ($this->linhas_primeira_pagina+1)) == 0 || (($nTotal_Itens_Impressos_Tmp) % ($this->linhas_primeira_pagina+1)) == 0) ) {
                break;
            }
            if ($pagina != 1 && (($nTotal_Itens_Impressos_Tmp - $linhasAdicionadas) % ($this->linhas_segunda_pagina+1)) == 0) {
                break;
            }
        }

        // Completas as linhas até o final
        for ($i = $nTotal_Itens_Impressos_Tmp++; $i <= (($pagina == 1) ? $this->linhas_primeira_pagina : $this->linhas_segunda_pagina); $i++) {
            $tabelaProdutos .= $vazio;
        }

        // Barcode
        global $id;
        $im     = imagecreatetruecolor(900, 130);  
        $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
        $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
        imagefilledrectangle($im, 0, 0, 900, 130, $white);  
        $data = Barcode::gd($im, $black, 450, 65, 0, "code128", $chave_acesso, 3, 120);  
        imagepng($im, "/tmp/{$id}.barcode.png");

        // HTML
        echo <<<EOT
<html>
<head>
EOT;
include_once('fonts.php');
if(!empty($g['C031']['base64FonteDANFE']) && $g['C031']['base64FonteDANFE'] != ''){
echo <<<EOT
<style>
@font-face {
    font-family: '{$g['C031']['base64FonteDANFE']}';
    src: url(data:font/truetype;charset=utf-8;base64,{$fonts[$g['C031']['base64FonteDANFE']]});
}
</style>
EOT;
}
echo <<<EOT
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body style="width: 1100px; border: 0; padding: 0; margin: 5px 0px; font-size: 11px; font-family: Helvetica;">
    <div style="width: 0; height: 0; border: 0; margin: 0; padding: 0; position: relative;">
EOT;

        // Overlays
        if ($this->visualizacao=='S')
        {
            // transform:rotate(-45deg); -ms-transform:rotate(-45deg); -webkit-transform:rotate(-45deg);
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    PRÉ VISUALIZAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
</div>
EOT;
        }
        elseif ($mT007[C020_NFe_Treinamento]=='S')
        {
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    DEMOSTRAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
</div>
EOT;
        }
        elseif ($mT007[T007_Flag_Cancelada]=='S')
        {
           echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 120px; z-index: 100; top: 554px; left: 155px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial; color: #f42e2c;">
    CANCELADA
</div>
EOT;
        } else if ($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == 2) {
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 100px; z-index: 100; top: 554px; left: 135px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    SEM VALOR FISCAL
</div>
EOT;
        }

        // HTML
        $marginTop = ($pagina == 1) ? 0 : 85;
        echo <<<EOT
    </div>
    <div style="width: 0; height: {$marginTop}px; border: 0; margin: 0; padding: 0;"></div>
EOT;
        if ($pagina == 1) {
            if($g['C031']['adicionarNomeFantasiaClienteCanhotoDanfe']=='S'){$nomeFantasia=$mT007['D024_Nome_Fantasia'];}else{$nomeFantasia='';}
        echo <<<EOT
    <!-- Cabeçalho 1 -->
    <div style="margin: 5px; width: 1090px; height: 82px; border: 1px solid black;">
        <div style="width: 900px; height: 36px; float: left; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            RECEBI(EMOS) DE <span style="font-weight: bolder;">{$xNome}</span><br />
            OS PRODUTOS CONSTANTE DA NOTA FISCAL ELETRÔNICA INDICADA AO LADO, BEM COMO ATESTAMOS QUE OS MESMOS FORAM EXAMINADOS<br />
            SERVINDO O ACEITE DA PRESENTE PARA TODOS OS EFEITOS LEGAIS.
        </div>
        <div style="width: 183px; height: 82px; float: right; border-left: 1px solid black; text-align: center; font-size: 15px;">
            <table>
                <tr>
                    <td colspan="2" style="text-align: center; padding-bottom: 6px;">NF-E</td>
                </tr>
                <tr style="font-weight: bolder; margin: 3px 0 0 6px;">
                    <td>No.</td>
                    <td style="background-color: e6e6e6; width: 100%; text-align: center;">{$this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue)}</td>
                </tr>
                <tr style="font-weight: bolder; margin: 3px 0 0 6px;">
                    <td>SÉRIE</td>
                    <td style="width: 100%; text-align: center;">{$ide->getElementsByTagName('serie')->item(0)->nodeValue}</td>
                </tr>
                <tr>
                    <td>{$vlNF}</td>
                </tr>
            </table>
        </div>
        <div style="width: 900px; height: 38px; float: left;">
            <div style="width: 193px; height: 38px; float: left; padding: 1px 0 0 3px;">
                DATA DE RECEBIMENTO
            </div>
            <div style="width: 600px; height: 38px; float: left; border-left: 1px solid black; padding: 1px 0 0 3px;">
                IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR: {$nomeFantasia}
            </div>
        </div>
    </div>
    <div style="width: 100%; height: 0; border-top: 2px dashed black;"></div>
    <!-- Fim Cabeçalho 1 -->
EOT;
        }
        echo <<<EOT
    <!-- Empresa -->
    <div style="margin: 5px 5px 0 5px; width: 1090px; height: 180px;">
        <!-- Empresa 1 -->
        <div style="float: left; width: 580px; height: 175px; margin-top: 5px;">
            <table style="font-size: 13px;">
                <tr style="height: 20px;">
                    <td colspan="2" style="font-weight: bolder; white-space: nowrap; font-size: 14px;">
                        {$nome_empresa}
                    </td>
                </tr>
                <tr style="height: 149px;">
                    <td style="width: 280px; vertical-align: middle; text-align: center;">
                        <img src="{$this->logomarca}" style="width: {$Largura_Imagem}px; height: {$Altura_Imagem}px;" />
                    </td>
                    <td style="width: 300px;">
                        {$endereco_emitente}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Empresa 2 -->
        <div style="float: left; width: 120px; height: 170px; border-top: 1px solid black; border-left: 1px solid black; padding: 5px;">
            <table style="font-size: 9px; text-align: center;">
                <tr>
                    <td colspan="2" style="font-size: 21px; font-weight: bolder;">
                        DANFE
                    </td>
                </tr>
                <tr>
                    <td colspan="2" >
                        DOCUMENTO AUXILIAR DA NOTA FISCAL ELETRÔNICA
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;">
                        0 - ENTRADA<br/>
                        1 - SAÍDA
                    </td>
                    <td style="text-align: center;">
                        <div style="border: 1px solid black; padding: 5px 4px; position: relative; right: 8px; font-size: 13px; width: 18px; float: right;">
                            {$ide->getElementsByTagName('tpNF')->item(0)->nodeValue}
                        </div>
                    </td>
                </tr>
            </table>
            <table style="font-size: 9px; text-align: center; margin-top: 7px;">
                <tr>
                    <td style="text-align: left;">No.</td>
                    <td style="font-size: 12px; font-weight: bolder;">{$nrNf}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">SÉRIE</td>
                    <td>{$serieNf}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">FOLHA</td>
                    <td>{$pagina}/{$nTotal_Paginas}</td>
                </tr>
            </table>
        </div>

        <!-- Empresa 3 -->
        <div style="float: left; width: 377px; height: 180px; border: 1px solid black; border-bottom: 0;">
            <div style="width: 377px; height: 65px; border-bottom: 1px solid black; text-align: center;">
                <img src="{$id}.barcode.png" style="width: 373px; height: 63px;" />
            </div>
            <div style="width: 377px; height: 68px; border-bottom: 1px solid black;">
                <table style="width: 377px;">
                    <tr>
                        <td style="font-size: 11px;">CHAVE DE ACESSO</td>
                    </tr>
                    <tr>
                        <td style="font-size: 13px; font-weight: bolder; text-align: center;">{$chave_acesso1}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 377px; height: 45px; text-align: center;">
                {$empresaP23}
            </div>
        </div>
    </div>
    <!-- Fim Empresa -->

    <!-- Extra -->
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: 74px; border: 1px solid black; border-bottom: 0;">
        <div style="float: left; width: 704px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            NATUREZA DA OPERAÇÃO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$extraNatureza}</span>
        </div>
        <div style="float: left; width: 371px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            PROTOCOLO DE AUTORIZAÇÃO DE USO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$protocolo}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 356px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            INSCRIÇÃO ESTADUAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$extraIE}</span>
        </div>
        <div style="float: left; width: 356px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            INSCRIÇÃO ESTADUAL DO SUBST. TRIB.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$extraIESubst}</span>
        </div>
        <div style="float: left; width: 356px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            C.N.P.J.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">$extraCNPJ</span>
        </div>
    </div>
    <!-- Fim Extra -->

    <!-- Cliente -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">DESTINATÁRIO/REMETENTE</span>
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: 111px; border: 1px solid black; border-bottom: 0;">
        <div style="float: left; width: 704px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            NOME/RAZÃO SOCIAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteNome}</span>
        </div>
        <div style="float: left; width: 270px; height: 30px; border-right: 1px solid black; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            C.N.P.J./C.P.F.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteCnpj}</span>
        </div>
        <div style="float: left; width: 94px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            DATA EMISSÃO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteDataEmissao}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 704px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            ENDEREÇO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteEndereco}</span>
        </div>
        <div style="float: left; width: 270px; height: 30px; border-right: 1px solid black; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            BAIRRO/DISTRITO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteBairro}</span>
        </div>
        <div style="float: left; width: 94px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            DATA SAÍDA/ENT<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteDataSaida}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 414px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            MUNICÍPIO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteMunicipio}</span>
        </div>
        <div style="float: left; width: 169px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            FONE/FAX<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteFone}</span>
        </div>
        <div style="float: left; width: 39px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            UF<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteUF}</span>
        </div>
        <div style="float: left; width: 102px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            CEP<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteCEP}</span>
        </div>
        <div style="float: left; width: 229px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            INSCRIÇÃO ESTADUAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteIE}</span>
        </div>
        <div style="float: left; width: 94px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            HORA DA SAÍDA<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteHoraSaira}</span>
        </div>
    </div>
    <!-- Fim Cliente -->
EOT;
        
if ($pagina == 1) {
        echo <<<EOT
    <!-- Faturar -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">FATURAR/DUPLICATAS</span>
    <div style="margin: 0 5px 5px 5px; width: 1079px; height: 30px; border: 1px solid black; padding: 5px; font-size: 12px; font-weight: bold;">
        {$dups}
    </div>
    <!-- Fim Faturar -->

    <!-- Imposto -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">CÁLCULO DO IMPOSTO</span>
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: 74px; border: 1px solid black; border-bottom: 0;">
EOT;
        if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
            echo <<<EOT
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            BASE DE CÁLCULO DO ICMS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBC}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO ICMS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vICMS}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            BASE DE CÁLCULO DO ICMS ST<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBCST}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO ICMS ST<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vST}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR APROX. TRIBUTOS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vTotTrib}</span>
        </div>
        <div style="float: left; width: 177px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR TOTAL DOS PRODUTOS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vProd}</span>
        </div>
EOT;
        } else {
            echo <<<EOT
        <div style="float: left; width: 210px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            BASE DE CÁLCULO DO ICMS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBC}</span>
        </div>
        <div style="float: left; width: 210px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO ICMS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vICMS}</span>
        </div>
        <div style="float: left; width: 210px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            BASE DE CÁLCULO DO ICMS ST<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBCST}</span>
        </div>
        <div style="float: left; width: 210px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO ICMS ST<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vST}</span>
        </div>
        <div style="float: left; width: 214px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR TOTAL DOS PRODUTOS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vProd}</span>
        </div>
EOT;
        }
            echo <<<EOT
        <!-- linha -->
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO FRETE<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vFrete}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO SEGURO<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vSeg}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            DESCONTO<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vDesc}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            DESPESAS ACESSÓRIAS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vOutro}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO IPI<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vIPITotal}</span>
        </div>
        <div style="float: left; width: 177px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR TOTAL DA NOTA<br/>
            <span style="line-height: 17px; font-size: 15px; font-weight: bolder; padding: 5px;">{$vNF}</span>
        </div>
    </div>
    <!-- Fim Imposto -->

    <!-- Transportadora -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">TRANSPORTADOR/VOLUMES TRANSPORTADOS</span>
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: 111px; border: 1px solid black; border-bottom: 0;">
        <div style="float: left; width: 494px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            RAZÃO SOCIAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_xNome}</span>
        </div>
        <div style="float: left; width: 142px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            FRETE POR CONTA DE<br />
            <span style="line-height: 9px; font-size: 10px; padding: 0;">
                0-Emitente 2-Terceiros<br />
                1-Dest/Rem 9-Sem Frete
            </span>
            <div style="padding: 0 1px 7px 4px; width: 10px; height: 10px; float: right; border: 1px solid black; font-size: 13px; font-weight: bolder; margin-top: -10px;">
                {$Modalidade_Frete}
            </div>
        </div>
        <div style="float: left; width: 80px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            CÓDIGO ANTT<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$veic_RNTC}</span>
        </div>
        <div style="float: left; width: 109px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            PLACA DO VEÍCULO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$veic_placa}</span>
        </div>
        <div style="float: left; width: 30px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            UF<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$veic_UF}</span>
        </div>
        <div style="float: left; width: 192px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            C.N.P.J./C.P.F.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_doc}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 623px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            ENDEREÇO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_xEnder}</span>
        </div>
        <div style="float: left; width: 232px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            MUNICÍPIO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_xMun}</span>
        </div>
        <div style="float: left; width: 32px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            UF<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_UF}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            INSCRIÇÃO ESTADUAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_IE}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            QUANT.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$qVol}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            ESPÉCIE<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$esp}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            MARCA<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$marca}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            NUMERAÇÃO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$nVol}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            PESO BRUTO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$pesoB}</span>
        </div>
        <div style="float: left; width: 177px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            PESO LÍQUIDO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$pesoL}</span>
        </div>
    </div>
    <!-- Fim Transportadora -->
EOT;
        }

        $produtosHeight = ($pagina == 1) ? 560 : 1073;
        $valorAproxTrib = '';
        if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
            $valorAproxTrib = '<td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">V.APX<BR>TRIB</td>';
        }
        echo <<<EOT
    <!-- Produtos -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">DADOS DOS PRODUTOS/SERVIÇOS</span>
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: {$produtosHeight}px; border: 1px solid black;"> <!-- 625px -->
        <table style="font-size: 11px; width: 100%;" cellspacing="0">
            <tr style="text-align: center;">
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">CÓDIGO</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">DESCRIÇÃO</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">NCM/SH</td>
		<td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">CEST</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">{$Titulo_CST}</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">CFOP</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">UN.</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">QTE</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">VALOR<BR>UNIT</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">VALOR<BR>IPI</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">V.ST.<BR>UNIT</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">V.TOTAL<br>UNIT</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">VALOR<BR>TOTAL</td>
                {$valorAproxTrib}
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">BASE<BR>ICMS</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">VALOR<BR>ICMS</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">%<BR>ICMS</td>
                <td style="border-bottom: 1px solid black;">%<BR>IPI</td>
            </tr>
            {$tabelaProdutos}
        </table>
    </div>
    <!-- Fim Produtos -->

EOT;

        // Rodapé
        $infCpl = $infAdFisco = '';
        if (is_object($infAdic))
        {
            $infCpl = $infAdic->getElementsByTagName("infCpl")->item(0)->nodeValue;
            $infCpl = str_replace(';', '<br/>', $infCpl);
            $infCpl = str_replace('|', '<br/>', $infCpl);
            $infAdFisco = $infAdic->getElementsByTagName("infAdFisco")->item(0)->nodeValue;
            $infAdFisco = str_replace(';', '<br/>', $infAdFisco);
            $infAdFisco = str_replace('|', '<br/>', $infAdFisco);
        }
if ($pagina == 1) {
        $infCpl = trim($infCpl);
        $infAdFisco = trim($infAdFisco);
        $extra = '';
        if ($Banco_Dados == 'e234') {
            if (!empty($infCpl) || !empty($infAdFisco)) {
                $extra .= '<br />';
            }
            $extra .= "Vendedor: {$mT007['C007_Nome']}<br />Pedido: {$mT007['T007_T005_Id']}";
        }
        // Acrescenta endereço de entrega (caso seja diferente do padrão)
        if (!empty($mT007['T007_D148_Id_Entrega'])) {
            if (!empty($infCpl) || !empty($infAdFisco) || !empty($extra)) {
                $extra .= '<br />';
            }
            $sql = "SELECT * FROM D148 left join D020 on D020_Id=D148_D020_Id left join D018 on D018_Id=D148_D018_Id WHERE D148_Id = '{$mT007['T007_D148_Id_Entrega']}'";
            $mD148 = mysql_query($sql);
            $mD148 = mysql_fetch_assoc($mD148);
            $endereco = !empty($mD148['D148_Numero']) ? $mD148['D148_Rua'].", ".$mD148['D148_Numero'] : $mD148['D148_Rua'];
            $endereco .= !empty($mD148['D148_Complemento']) ? " - ".$mD148['D148_Complemento'] : '';
            $extra = trim($extra, '<br/>');
            $extra .= "ENDEREÇO DE ENTREGA: {$endereco} - {$mD148['D020_Nome_Cidade']} - {$mD148['D018_UF']} - Bairro: {$mD148['D148_Bairro']} - CEP: {$mD148['D148_Cep']}";
        }

        //
        echo <<<EOT
    <!-- Rodapé -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">DADOS ADICIONAIS</span>
    <div style="margin: 0 5px 1px; width: 1088px; height: 140px; border: 1px solid black; border-bottom: 0;">
        <div style="float: left; width: 642px; height: 133px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            INFORMAÇÕES COMPLEMENTARES<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$infCpl}{$infAdFisco}{$extra}</span>
        </div>
         <div style="float: left; width: 433px; height: 133px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            RESERVADO AO FISCO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;"></span>
        </div>
    </div>
    <!-- Fim Rodapé -->
EOT;
}

        global $Banco_Dados;
        $footer = 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';
        if ($Banco_Dados == 'e276') {
            $footer = '&nbsp;';
        }
            echo <<<EOT
    <span style="margin-left: 5px; font-size: 13px;">{$footer}</span>
</body>
</html>
EOT;

}
        
    }


    function geraHtmlCabecalhoEmBaixo()
    {
        global $Banco_Dados;

        if (empty($this->xml))
        {
            return false;
        }
        
        $cClausula  = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " left join C020 on C020_C004_Id=T007_C004_Id ";
        $cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " where T007_Id='".$this->T007_Id."' ";
        $cSql_T007 = mysql_query($cClausula);
        $mT007 = mysql_fetch_array($cSql_T007);

        $dom = new DomDocument;
        $dom->loadXML($this->xml);
        $infNFe     = $dom->getElementsByTagName("infNFe")->item(0);
        $ide        = $dom->getElementsByTagName("ide")->item(0);
        $emit       = $dom->getElementsByTagName("emit")->item(0);
        $dest       = $dom->getElementsByTagName("dest")->item(0);
        $enderEmit  = $dom->getElementsByTagName("enderEmit")->item(0);
        $enderDest  = $dom->getElementsByTagName("enderDest")->item(0);
        $det        = $dom->getElementsByTagName("det");
        $cobr       = $dom->getElementsByTagName("cobr")->item(0);
        $dup        = $dom->getElementsByTagName('dup');
        $ICMSTot    = $dom->getElementsByTagName("ICMSTot")->item(0);
        $transp     = $dom->getElementsByTagName("transp")->item(0);
        $transporta = $dom->getElementsByTagName("transporta")->item(0);
        $veicTransp = $dom->getElementsByTagName("veicTransp")->item(0);
        $vols       = $dom->getElementsByTagName("vol");
        $infAdic    = $dom->getElementsByTagName("infAdic")->item(0);
        $protNFe    = $dom->getElementsByTagName("protNFe")->item(0);

        // Linhas / Páginas
        $maxChars = 62;
        $this->linhas_primeira_pagina = 32;
        $this->linhas_segunda_pagina = 58;
        $nTotal_Linhas = 0;
        $nTotal_Itens_Contados = 0;
        $lineHeightGeral = 11;
        foreach ($det as $d)
        {
            $prod = $det->item($nTotal_Itens_Contados)->getElementsByTagName("prod")->item(0);
            $cDescricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
            $cDescricao = trim($cDescricao);
            $cOrdem_Compra = $prod->getElementsByTagName("xPed")->item(0)->nodeValue;
            $cOrdem_Compra = trim($cOrdem_Compra);
            
            $nTotal_Linhas++;
            if (strlen($cDescricao) > ($maxChars * 2)) {
                $nTotal_Linhas++;
                $nTotal_Linhas++;
            }
            else if (strlen($cDescricao) > $maxChars)
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
        $nItensPorPagina = $this->linhas_primeira_pagina;

        $nTotal_Paginas = 1;
        //echo $nTotal_Linhas;
        //die();
        if ($nTotal_Linhas > $this->linhas_primeira_pagina) {
            $nTotal_Paginas += ceil(($nTotal_Linhas - 32) / $this->linhas_segunda_pagina);
        }
        
        //-- Total de Linhas Impressas por página
        $nTotal_Linhas_Impressas=0;
        //-- Total de Linhas Impressas no Total
        $nTotal_Itens_Impressos=0;

for ($pagina = 1; $pagina <= $nTotal_Paginas; $pagina++) {

        // Cabeçalho 1
        $xNome = utf8_decode($emit->getElementsByTagName("xNome")->item(0)->nodeValue);
        $vlNF = '';
        if($_REQUEST['vlrNF'] == 'S'){
            $vlNF = "R$ " . number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", ".");
        }

        // Empresa P1
        if (!empty($this->logomarca) && is_file('/tmp/'.$this->logomarca))
        {
            $Img_nfe = imagecreatefromjpeg('/tmp/'.$this->logomarca);
            $Largura_Imagem=imagesx($Img_nfe);
            $Altura_Imagem=imagesy($Img_nfe);

            if ($Altura_Imagem > 130) {
                $porcentagem = (round((130 * 100) / $Altura_Imagem) - 100) / 100;
                if ($porcentagem < 0) {
                    $porcentagem = $porcentagem + 1;
                }
                $Largura_Imagem = $Largura_Imagem * $porcentagem;
                $Altura_Imagem = 130;
            }
            if ($Largura_Imagem < 0 || $Largura_Imagem > 260) {
                //$Largura_Imagem=imagesx($Img_nfe);
                //$Altura_Imagem=imagesy($Img_nfe);
                $porcentagem = (round((260 * 100) / $Largura_Imagem) - 100) / 100;
                if ($porcentagem < 0) {
                    $porcentagem = $porcentagem + 1;
                }
                $Largura_Imagem = 260;
                $Altura_Imagem = $Altura_Imagem * $porcentagem;;
            }
        }
        $endereco_emitente = substr($enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue,0,60);
        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".$enderEmit->getElementsByTagName("nro")->item(0)->nodeValue : null;
        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
        $nome_empresa=$emit->getElementsByTagName("xNome")->item(0)->nodeValue;
        if (strlen($nome_empresa)<=40)
        {
            $nome_empresa = utf8_decode(substr($nome_empresa,0,46));
        }
        else
        {
            $nome_empresa = utf8_decode($nome_empresa);
        }
        $endereco_emitente .= "<br/>" . utf8_decode($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $endereco_emitente .= "<br/>" . utf8_decode($enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue).'/'.utf8_decode($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue);
        $endereco_emitente .= "<br/>" . "CEP: ".utf8_decode($emit->getElementsByTagName("CEP")->item(0)->nodeValue)." - FONE: ".utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue);
        if (!empty($mT007[C004_E_Mail]))
        {
            $endereco_emitente .= "<br/>" . "$mT007[C004_E_Mail]";
        }
        if (!empty($mT007[C004_Site]))
        {
            $endereco_emitente .= "<br/>" . "$mT007[C004_Site]";
        }

        // Empresa P2
        $nrNf = $this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue);
        $serieNf = $ide->getElementsByTagName('serie')->item(0)->nodeValue;

        // Empresa P3
        $protocolo = '';
        $chave_acesso = str_replace('NFe', '', $infNFe->getAttribute("Id"));
        $chave_acesso1=$this->formata_chave_acesso($chave_acesso);
        $this->pdf->SetFillColor(0,0,0);
        $this->pdf->Code128(145, 23.8-$extra, $chave_acesso, 75, 12);

        $empresaP23 = '';
        if ($ide->getElementsByTagName("tpEmis")->item(0)->nodeValue != 1 and $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue != 3)
        {
            // contingencia
            $empresaP23 .= "DANFE em Contingência Impresso em decorrência de problemas têcnicos<br />";
            
            $dados_nfe = $this->array_uf[$enderDest->getElementsByTagName("UF")->item(0)->nodeValue];
            $dados_nfe.= $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue;
            $dados_nfe.= $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
            $dados_nfe.= sprintf("%014s", str_replace(',', '', number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", "")));
            $dados_nfe.= 1; // ICMSp
            $dados_nfe.= 2; // ICMSs
            $data_emissao = troca_data_dma($ide->getElementsByTagName("dEmi")->item(0)->nodeValue);
            $dados_nfe.= $data_emissao[0].$data_emissao[1];
            $dados_nfe.= $this->calcula_dv($dados_nfe);

            $empresaP23 .= "DADOS DA NF-e<br />";
            $empresaP23 .= $dados_nfe;

        }
        else
        {
            if ($mT007[C020_NFe_Treinamento]=='S' or $this->visualizacao=='S')
            {
                //
            }
            else
            {
                // normal
                $empresaP23 .= "Consulta de autenticidade no portal nacional da NF-e www.nfe.fazenda.gov.br/portal ou no site da Sefaz Autorizadora<br />";
                $nNumero_Protocolo=utf8_decode($protNFe->getElementsByTagName("nProt")->item(0)->nodeValue);
                $cData_Protocolo=utf8_decode($protNFe->getElementsByTagName("dhRecbto")->item(0)->nodeValue);
                list($cData_Protocolo,$cHora_Protocolo)=explode("T",$cData_Protocolo);
                $protocolo = $nNumero_Protocolo." ".troca_data_dma($cData_Protocolo)." às ".$cHora_Protocolo;
            }
        }

        // Extra
        $extraNatureza = utf8_decode($ide->getElementsByTagName("natOp")->item(0)->nodeValue);
        $extraIE = utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue);
        $extraIESubst = utf8_decode($emit->getElementsByTagName("IEST")->item(0)->nodeValue);
        $extraCNPJ = $this->mask_cnpj($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue);

        // Cliente
        $clienteNome = utf8_decode($dest->getElementsByTagName("xNome")->item(0)->nodeValue);
        $clienteCnpj = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($dest->getElementsByTagName("CPF")->item(0)->nodeValue);
        $clienteDataEmissao = troca_data_dma(substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 0, 10));
        $clienteEndereco = utf8_decode($enderDest->getElementsByTagName("xLgr")->item(0)->nodeValue);
        $clienteEndereco .= (!empty($enderDest->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($enderDest->getElementsByTagName("nro")->item(0)->nodeValue) : null;
        $clienteEndereco .= (!empty($enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
        $clienteBairro = utf8_decode($enderDest->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $clienteDataSaida = troca_data_dma(substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 0, 10));
        $clienteMunicipio = utf8_decode($enderDest->getElementsByTagName("xMun")->item(0)->nodeValue);
        $clienteFone = utf8_decode($enderDest->getElementsByTagName("fone")->item(0)->nodeValue);
        $clienteUF = utf8_decode($enderDest->getElementsByTagName("UF")->item(0)->nodeValue);
        $clienteCEP = utf8_decode($enderDest->getElementsByTagName("CEP")->item(0)->nodeValue);
        $clienteIE = utf8_decode($dest->getElementsByTagName("IE")->item(0)->nodeValue);
        //$Hora=$ide->getElementsByTagName("hSaiEnt")->item(0)->nodeValue;
        $Hora=substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 11, 8);
        if (empty($Hora))
        {
            $Hora=date("H:i:s");
        }
        $clienteHoraSaira = $Hora;
        $nPagina_Atual = 1;
        if ($nPagina_Atual==1)
        {
             // faturas/duplicatas
            $dups = "";
            if (count($dup)>0)
            {
               $dups = "";

               $nValorLinhaDpl=112.1;
               $nValorColunaDpl=10.0;
               $nColunaDpl=1;
               foreach ($dup as $k => $d)
               {
                  $dups.= $dup->item($k)->getElementsByTagName('nDup')->item(0)->nodeValue." ";
                  $dups.= troca_data_dma($dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue);
                  $dups.= " R$".number_format($dup->item($k)->getElementsByTagName('vDup')->item(0)->nodeValue, 2, ",", ".")."&nbsp;&nbsp;&nbsp;&nbsp;";
               }
            }
        }

        // Impostos
        //if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
        $vBC = number_format($ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", ".");
        $vICMS = number_format($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", ".");
        $vBCST = number_format($ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue, 2, ",", ".");
        $vST = number_format($ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue, 2, ",", ".");
        $vTotTrib = number_format($ICMSTot->getElementsByTagName("vTotTrib")->item(0)->nodeValue, 2, ",", ".");
        $vProd = number_format($ICMSTot->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", ".");
        $vFrete = number_format($ICMSTot->getElementsByTagName("vFrete")->item(0)->nodeValue, 2, ",", ".");
        $vSeg = number_format($ICMSTot->getElementsByTagName("vSeg")->item(0)->nodeValue, 2, ",", ".");
        $vDesc = number_format($ICMSTot->getElementsByTagName("vDesc")->item(0)->nodeValue, 2, ",", ".");
        $vOutro = number_format($ICMSTot->getElementsByTagName("vOutro")->item(0)->nodeValue, 2, ",", ".");
        $vIPITotal = number_format($ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue, 2, ",", ".");
        $vNF = number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", ".");

        // Transportadora
        if (is_object($transporta))
        {
            $transp_doc     = (!empty($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($transporta->getElementsByTagName("CPF")->item(0)->nodeValue);
            $transp_xNome   = utf8_decode($transporta->getElementsByTagName("xNome")->item(0)->nodeValue);
            $transp_xEnder  = utf8_decode($transporta->getElementsByTagName("xEnder")->item(0)->nodeValue);
            $transp_xMun    = utf8_decode($transporta->getElementsByTagName("xMun")->item(0)->nodeValue);
            $transp_UF      = utf8_decode($transporta->getElementsByTagName("UF")->item(0)->nodeValue);
            $transp_IE      = utf8_decode($transporta->getElementsByTagName("IE")->item(0)->nodeValue);
        }
        if (is_object($veicTransp))
        {
            $veic_RNTC      = utf8_decode($veicTransp->getElementsByTagName("RNTC")->item(0)->nodeValue);
            $veic_placa     = utf8_decode($veicTransp->getElementsByTagName("placa")->item(0)->nodeValue);
            $veic_UF        = utf8_decode($veicTransp->getElementsByTagName("UF")->item(0)->nodeValue);
        }
        // Alumifix: na alumifix, versão antiga, 1 era emitente, 2 era destinatário
        /*if ($Banco_Dados == 'e004') {
            $this->linha( 8.5,  31.0,  85.0,   152,      "", "1 - Emitente",  "");
            $this->linha( 8.5,  31.0,  85.0,   154,      "", "2 - Destinatário",  "");
        } else { 
            $this->linha( 8.5,  31.0,  85.0,   152,      "", "0-Emitente 2-Terceiros",  "");
            $this->linha( 8.5,  31.0,  85.0,   154,      "", "1-Dest/Rem 9-Sem Frete",  "");
        }*/
        $Modalidade_Frete=$transp->getElementsByTagName("modFrete")->item(0)->nodeValue;
        /*if ($Modalidade_Frete=='0')
        {
            $Modalidade_Frete='O';
        }*/
        // faz somatorios/concatenacoes dos volumes e coloca nos campos
        foreach ($vols as $key => $v)
        {
            $barra   = ($key != 0) ? "/" : "";
            $qVol   += $v->getElementsByTagName("qVol")->item(0)->nodeValue;
            $esp    .= $barra.$v->getElementsByTagName("esp")->item(0)->nodeValue;
            $marca  .= $barra.$v->getElementsByTagName("marca")->item(0)->nodeValue;
            $nVol   .= $barra.$v->getElementsByTagName("nVol")->item(0)->nodeValue;
            $pesoB  += $v->getElementsByTagName("pesoB")->item(0)->nodeValue;
            $pesoL  += $v->getElementsByTagName("pesoL")->item(0)->nodeValue;
        }
        $qVol = number_format($qVol, 2, ",", ".");
        $esp = utf8_decode($esp);
        $marca = utf8_decode($marca);
        $nVol = utf8_decode($nVol);
        $pesoB = number_format($pesoB, 3, ",", ".");
        $pesoL = number_format($pesoL, 3, ",", ".");

        /*
        * LOOP PRODUTOS
        */
        if ($mT007[C004_Codigo_Regime_Tributario]=='3'){
            $Titulo_CST = "CST";
        } else {
            $Titulo_CST = "CSOSN";
        }

        $tabelaProdutos = '';
        $valorAproxTrib = '';
        if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
            $valorAproxTrib = "<td style=\"height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;\">&nbsp;</td>";
        }
        $vazio = <<<EOT
<tr>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    {$valorAproxTrib}
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">&nbsp;</td>
    <td style="height: 16px; line-height: {$lineHeightGeral}px;">&nbsp;</td>
</tr>
EOT;

        $nTotal_Itens_Impressos_Tmp = 0;
        for ($i = 0; $i < $nTotal_Itens_Contados; $i++) {
            if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)
            {
                break;
            }
            $prod = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("prod")->item(0);
            $imposto = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("imposto")->item(0);
            $ICMS = $imposto->getElementsByTagName("ICMS")->item(0);
            $IPI  = $imposto->getElementsByTagName("IPI")->item(0);
            $cDescricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
            $cDescricao = trim($cDescricao);
            $cOrdem_Compra = $prod->getElementsByTagName("xPed")->item(0)->nodeValue;
            $cOrdem_Compra = trim($cOrdem_Compra);
            $cItem_Ordem_Compra = $prod->getElementsByTagName("nItemPed")->item(0)->nodeValue;
            $cItem_Ordem_Compra = trim($cItem_Ordem_Compra);

            $cReferencia=$prod->getElementsByTagName("cProd")->item(0)->nodeValue;
            $nCodigo_Barras=$prod->getElementsByTagName("cEAN")->item(0)->nodeValue;
            $nQuantidade=$prod->getElementsByTagName("qCom")->item(0)->nodeValue;

            if (!empty($nCodigo_Barras))
            {
               $cReferencia=$nCodigo_Barras;
            }

            $cReferencia = utf8_encode($cReferencia);
            $cDescricao = utf8_encode($cDescricao);
            $NCM = (!empty($prod->getElementsByTagName("NCM")->item(0)->nodeValue)) ? $prod->getElementsByTagName("NCM")->item(0)->nodeValue : '';
            if (!empty($ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue))
            {
                $CST = $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue;
            }
            else
            {
                $CST = $ICMS->getElementsByTagName("orig")->item(0)->nodeValue.$ICMS->getElementsByTagName("CST")->item(0)->nodeValue;
            }
            $CFOP = $prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
            $unidade = utf8_decode($prod->getElementsByTagName("uCom")->item(0)->nodeValue);
            $quantidade = number_format($nQuantidade, $this->casas_decimais_quantidade, ",", ".");
            $valor_unitario = number_format($prod->getElementsByTagName("vUnCom")->item(0)->nodeValue, $this->casas_decimais_preco, ",", ".");
            $valor_total = number_format($prod->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", ".");
            $valor_total_tributos = number_format($imposto->getElementsByTagName("vTotTrib")->item(0)->nodeValue, 2, ",", ".");
            $bc_icms = number_format($ICMS->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", ".");
            $v_icms = number_format($ICMS->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", "");
            $vIPI = (!empty($IPI)) ? $IPI->getElementsByTagName("vIPI")->item(0)->nodeValue : 0;
            $vIPI = number_format($vIPI, 2, ",", "");
            $p_icms = number_format($ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue, 2, ",", ".");
            $pIPI = (!empty($IPI)) ? $IPI->getElementsByTagName("pIPI")->item(0)->nodeValue : 0;
            $pIPI = number_format($pIPI, 2, ",", ".");

            $maxChars = 62;
            $linhasAdicionadas = 0;
            if (strlen($cDescricao) > ($maxChars * 2)) {
                $lineHeight = (16 * 3) - 4;
                $cDescricao = substr($cDescricao, 0, $maxChars) . '<br />' . substr($cDescricao, $maxChars, $maxChars) . '<br />' . substr($cDescricao, ($maxChars * 2), $maxChars);
                $nTotal_Linhas_Contados += 3;
                $nTotal_Itens_Impressos_Tmp++;
                $nTotal_Itens_Impressos_Tmp++;
                $linhasAdicionadas++;
                $linhasAdicionadas++;
            } else if (strlen($cDescricao) > $maxChars) {
                $lineHeight = (16 * 2) - 2;
                $cDescricao = substr($cDescricao, 0, $maxChars) . '<br />' . substr($cDescricao, $maxChars, $maxChars);
                $nTotal_Linhas_Contados += 2;
                $nTotal_Itens_Impressos_Tmp++;
                $linhasAdicionadas++;
            } else {
                $lineHeight = 16;
                $nTotal_Linhas_Contados += 1;
            }

            // Ordem de Compra
            if (!empty($cOrdem_Compra))
            {
                $cOrdem_Compra=' > Ped.Compra: '.$cOrdem_Compra;
                if (!empty($cItem_Ordem_Compra))
                {
                    $cOrdem_Compra.=' - Item: '.$cItem_Ordem_Compra;
                }
                $cDescricao .= '<br />' . utf8_encode($cOrdem_Compra);
                $nTotal_Linhas_Contados += 1;
                $nTotal_Itens_Impressos_Tmp++;
                $linhasAdicionadas++;
                $lineHeight += 16;
            }

            $valorAproxTrib = '';
            if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
                $valorAproxTrib = <<<EOT
<td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: right;">{$valor_total_tributos}</td>
EOT;
            }

            $tabelaProdutos .= <<<EOT
<tr style="font-weight: bolder;">
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: center;">{$cReferencia}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black;">{$cDescricao}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: center;">{$NCM}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: center;">{$CST}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: center;">{$CFOP}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: center;">{$unidade}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: right;">{$quantidade}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: right;">{$valor_unitario}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: right;">{$valor_total}</td>
    {$valorAproxTrib}
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: right;">{$bc_icms}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: right;">{$v_icms}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: right;">{$vIPI}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; border-right: 1px solid black; text-align: center;">{$p_icms}</td>
    <td style="vertical-align: top; height: {$lineHeight}px; line-height: {$lineHeightGeral}px; text-align: center;">{$pIPI}</td>
</tr>
EOT;
            
            $nTotal_Itens_Impressos++;
            $nTotal_Itens_Impressos_Tmp++;

            if ($pagina == 1 && ( (($nTotal_Itens_Impressos_Tmp - $linhasAdicionadas) % ($this->linhas_primeira_pagina+1)) == 0 || (($nTotal_Itens_Impressos_Tmp) % ($this->linhas_primeira_pagina+1)) == 0) ) {
                break;
            }
            if ($pagina != 1 && (($nTotal_Itens_Impressos_Tmp - $linhasAdicionadas) % ($this->linhas_segunda_pagina+1)) == 0) {
                break;
            }
        }

        // Completas as linhas até o final
        for ($i = $nTotal_Itens_Impressos_Tmp++; $i <= (($pagina == 1) ? $this->linhas_primeira_pagina : $this->linhas_segunda_pagina); $i++) {
            $tabelaProdutos .= $vazio;
        }

        // Barcode
        global $id;
        $im     = imagecreatetruecolor(900, 130);  
        $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
        $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
        imagefilledrectangle($im, 0, 0, 900, 130, $white);  
        $data = Barcode::gd($im, $black, 450, 65, 0, "code128", $chave_acesso, 3, 120);  
        imagepng($im, "/tmp/{$id}.barcode.png");

        // HTML
        echo <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body style="width: 1100px; border: 0; padding: 0; margin: 5px 0px; font-size: 11px; font-family: Helvetica;">
    <div style="width: 0; height: 0; border: 0; margin: 0; padding: 0; position: relative;">
EOT;

        // Overlays
        if ($this->visualizacao=='S')
        {
            // transform:rotate(-45deg); -ms-transform:rotate(-45deg); -webkit-transform:rotate(-45deg);
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    PRÉ VISUALIZAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
</div>
EOT;
        }
        elseif ($mT007[C020_NFe_Treinamento]=='S')
        {
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    DEMOSTRAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
</div>
EOT;
        }
        elseif ($mT007[T007_Flag_Cancelada]=='S')
        {
           echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 120px; z-index: 100; top: 554px; left: 155px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial; color: #f42e2c;">
    CANCELADA
</div>
EOT;
        } else if ($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == 2) {
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 100px; z-index: 100; top: 554px; left: 135px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    SEM VALOR FISCAL
</div>
EOT;
        }

        // HTML
        $marginTop = ($pagina == 1) ? 0 : 105;
        echo <<<EOT
    </div>
    <div style="width: 0; height: {$marginTop}px; border: 0; margin: 0; padding: 0;"></div>
EOT;

        echo <<<EOT
    <!-- Empresa -->
    <div style="margin: 5px 5px 0 5px; width: 1090px; height: 180px;">
        <!-- Empresa 1 -->
        <div style="float: left; width: 580px; height: 175px; margin-top: 5px;">
            <table style="font-size: 13px;">
                <tr style="height: 20px;">
                    <td colspan="2" style="font-weight: bolder; white-space: nowrap; font-size: 16px;">
                        {$nome_empresa}
                    </td>
                </tr>
                <tr style="height: 149px;">
                    <td style="width: 280px; vertical-align: middle; text-align: center;">
                        <img src="{$this->logomarca}" style="width: {$Largura_Imagem}px; height: {$Altura_Imagem}px;" />
                    </td>
                    <td style="width: 300px;">
                        {$endereco_emitente}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Empresa 2 -->
        <div style="float: left; width: 120px; height: 170px; border-top: 1px solid black; border-left: 1px solid black; padding: 5px;">
            <table style="font-size: 9px; text-align: center;">
                <tr>
                    <td colspan="2" style="font-size: 21px; font-weight: bolder;">
                        DANFE
                    </td>
                </tr>
                <tr>
                    <td colspan="2" >
                        DOCUMENTO AUXILIAR DA NOTA FISCAL ELETRÔNICA
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;">
                        0 - ENTRADA<br/>
                        1 - SAÍDA
                    </td>
                    <td style="text-align: center;">
                        <div style="border: 1px solid black; padding: 5px 4px; position: relative; right: 8px; font-size: 13px; width: 18px; float: right;">
                            {$ide->getElementsByTagName('tpNF')->item(0)->nodeValue}
                        </div>
                    </td>
                </tr>
            </table>
            <table style="font-size: 9px; text-align: center; margin-top: 7px;">
                <tr>
                    <td style="text-align: left;">No.</td>
                    <td style="font-size: 15px; font-weight: bolder;">{$nrNf}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">SÉRIE</td>
                    <td>{$serieNf}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">FOLHA</td>
                    <td>{$pagina}/{$nTotal_Paginas}</td>
                </tr>
            </table>
        </div>

        <!-- Empresa 3 -->
        <div style="float: left; width: 377px; height: 180px; border: 1px solid black; border-bottom: 0;">
            <div style="width: 377px; height: 65px; border-bottom: 1px solid black; text-align: center;">
                <img src="{$id}.barcode.png" style="width: 373px; height: 62px;" />
            </div>
            <div style="width: 377px; height: 48px; border-bottom: 1px solid black;">
                <table style="width: 377px;">
                    <tr>
                        <td style="font-size: 11px;">CHAVE DE ACESSO</td>
                    </tr>
                    <tr>
                        <td style="font-size: 13px; font-weight: bolder; text-align: center;">{$chave_acesso1}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 377px; height: 65px; text-align: center;">
                {$empresaP23}
            </div>
        </div>
    </div>
    <!-- Fim Empresa -->

    <!-- Extra -->
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: 74px; border: 1px solid black; border-bottom: 0;">
        <div style="float: left; width: 704px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            NATUREZA DA OPERAÇÃO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$extraNatureza}</span>
        </div>
        <div style="float: left; width: 371px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            PROTOCOLO DE AUTORIZAÇÃO DE USO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$protocolo}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 356px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            INSCRIÇÃO ESTADUAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$extraIE}</span>
        </div>
        <div style="float: left; width: 356px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            INSCRIÇÃO ESTADUAL DO SUBST. TRIB.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$extraIESubst}</span>
        </div>
        <div style="float: left; width: 356px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            C.N.P.J.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">$extraCNPJ</span>
        </div>
    </div>
    <!-- Fim Extra -->

    <!-- Cliente -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">DESTINATÁRIO/REMETENTE</span>
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: 111px; border: 1px solid black; border-bottom: 0;">
        <div style="float: left; width: 704px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            NOME/RAZÃO SOCIAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteNome}</span>
        </div>
        <div style="float: left; width: 270px; height: 30px; border-right: 1px solid black; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            C.N.P.J./C.P.F.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteCnpj}</span>
        </div>
        <div style="float: left; width: 94px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            DATA EMISSÃO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteDataEmissao}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 704px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            ENDEREÇO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteEndereco}</span>
        </div>
        <div style="float: left; width: 270px; height: 30px; border-right: 1px solid black; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            BAIRRO/DISTRITO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteBairro}</span>
        </div>
        <div style="float: left; width: 94px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            DATA SAÍDA/ENT<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteDataSaida}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 414px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            MUNICÍPIO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteMunicipio}</span>
        </div>
        <div style="float: left; width: 169px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            FONE/FAX<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteFone}</span>
        </div>
        <div style="float: left; width: 39px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            UF<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteUF}</span>
        </div>
        <div style="float: left; width: 102px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            CEP<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteCEP}</span>
        </div>
        <div style="float: left; width: 229px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            INSCRIÇÃO ESTADUAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteIE}</span>
        </div>
        <div style="float: left; width: 94px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            HORA DA SAÍDA<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$clienteHoraSaira}</span>
        </div>
    </div>
    <!-- Fim Cliente -->
EOT;
        
if ($pagina == 1) {
        echo <<<EOT
    <!-- Faturar -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">FATURAR/DUPLICATAS</span>
    <div style="margin: 0 5px 5px 5px; width: 1079px; height: 30px; border: 1px solid black; padding: 5px; font-size: 13px; font-weight: bolder;">
        {$dups}
    </div>
    <!-- Fim Faturar -->

    <!-- Imposto -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">CÁLCULO DO IMPOSTO</span>
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: 74px; border: 1px solid black; border-bottom: 0;">
EOT;
        if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
            echo <<<EOT
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            BASE DE CÁLCULO DO ICMS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBC}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO ICMS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vICMS}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            BASE DE CÁLCULO DO ICMS ST<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBCST}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO ICMS ST<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vST}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR APROX. TRIBUTOS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vTotTrib}</span>
        </div>
        <div style="float: left; width: 177px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR TOTAL DOS PRODUTOS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vProd}</span>
        </div>
EOT;
        } else {
            echo <<<EOT
        <div style="float: left; width: 210px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            BASE DE CÁLCULO DO ICMS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBC}</span>
        </div>
        <div style="float: left; width: 210px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO ICMS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vICMS}</span>
        </div>
        <div style="float: left; width: 210px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            BASE DE CÁLCULO DO ICMS ST<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBCST}</span>
        </div>
        <div style="float: left; width: 210px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO ICMS ST<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vST}</span>
        </div>
        <div style="float: left; width: 214px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR TOTAL DOS PRODUTOS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vProd}</span>
        </div>
EOT;
        }
            echo <<<EOT
        <!-- linha -->
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO FRETE<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vFrete}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO SEGURO<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vSeg}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            DESCONTO<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vDesc}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            DESPESAS ACESSÓRIAS<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vOutro}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR DO IPI<br/>
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vIPITotal}</span>
        </div>
        <div style="float: left; width: 177px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            VALOR TOTAL DA NOTA<br/>
            <span style="line-height: 17px; font-size: 15px; font-weight: bolder; padding: 5px;">{$vNF}</span>
        </div>
    </div>
    <!-- Fim Imposto -->

    <!-- Transportadora -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">TRANSPORTADOR/VOLUMES TRANSPORTADOS</span>
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: 111px; border: 1px solid black; border-bottom: 0;">
        <div style="float: left; width: 494px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            RAZÃO SOCIAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_xNome}</span>
        </div>
        <div style="float: left; width: 142px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            FRETE POR CONTA DE<br />
            <span style="line-height: 9px; font-size: 10px; padding: 0;">
                0-Emitente 2-Terceiros<br />
                1-Dest/Rem 9-Sem Frete
            </span>
            <div style="padding: 0 1px 7px 4px; width: 10px; height: 10px; float: right; border: 1px solid black; font-size: 13px; font-weight: bolder; margin-top: -10px;">
                {$Modalidade_Frete}
            </div>
        </div>
        <div style="float: left; width: 80px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            CÓDIGO ANTT<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$veic_RNTC}</span>
        </div>
        <div style="float: left; width: 109px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            PLACA DO VEÍCULO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$veic_placa}</span>
        </div>
        <div style="float: left; width: 30px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            UF<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$veic_UF}</span>
        </div>
        <div style="float: left; width: 192px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            C.N.P.J./C.P.F.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_doc}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 623px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            ENDEREÇO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_xEnder}</span>
        </div>
        <div style="float: left; width: 232px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            MUNICÍPIO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_xMun}</span>
        </div>
        <div style="float: left; width: 32px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            UF<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_UF}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            INSCRIÇÃO ESTADUAL<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_IE}</span>
        </div>
        <!-- linha -->
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            QUANT.<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$qVol}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            ESPÉCIE<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$esp}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            MARCA<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$marca}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            NUMERAÇÃO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$nVol}</span>
        </div>
        <div style="float: left; width: 174px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            PESO BRUTO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$pesoB}</span>
        </div>
        <div style="float: left; width: 177px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            PESO LÍQUIDO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$pesoL}</span>
        </div>
    </div>
    <!-- Fim Transportadora -->
EOT;
        }

        $produtosHeight = ($pagina == 1) ? 560 : 970; //1073;
        $valorAproxTrib = '';
        if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
            $valorAproxTrib = '<td style="border-bottom: 1px solid black; border-right: 1px solid black;">V.AP.TRIB.</td>';
        }
        echo <<<EOT
    <!-- Produtos -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">DADOS DOS PRODUTOS/SERVIÇOS</span>
    <div style="margin: 0 5px 5px 5px; width: 1088px; height: {$produtosHeight}px; border: 1px solid black;"> <!-- 625px -->
        <table style="font-size: 11px; width: 100%;" cellspacing="0">
            <tr style="text-align: center;">
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">CÓDIGO</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">DESCRIÇÃO</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">NCM/SH</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">{$Titulo_CST}</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">CFOP</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">UN.</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">QTDE</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">V.UNIT.</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">V.TOTAL</td>
                {$valorAproxTrib}
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">BC.ICMS</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">V.ICMS</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">V.IPI</td>
                <td style="border-bottom: 1px solid black; border-right: 1px solid black;">%ICMS</td>
                <td style="border-bottom: 1px solid black;">%IPI</td>
            </tr>
            {$tabelaProdutos}
        </table>
    </div>
    <!-- Fim Produtos -->

EOT;

        // Rodapé
        $infCpl = $infAdFisco = '';
        if (is_object($infAdic))
        {
            $infCpl = $infAdic->getElementsByTagName("infCpl")->item(0)->nodeValue;
            $infCpl = str_replace(';', '<br/>', $infCpl);
            $infCpl = str_replace('|', '<br/>', $infCpl);
            $infAdFisco = $infAdic->getElementsByTagName("infAdFisco")->item(0)->nodeValue;
            $infAdFisco = str_replace(';', '<br/>', $infAdFisco);
            $infAdFisco = str_replace('|', '<br/>', $infAdFisco);
        }
if ($pagina == 1) {
        $infCpl = trim($infCpl);
        $infAdFisco = trim($infAdFisco);
        $extra = '';
        if ($Banco_Dados == 'e234') {
            if (!empty($infCpl) || !empty($infAdFisco)) {
                $extra .= '<br />';
            }
            $extra .= "Vendedor: {$mT007['C007_Nome']}<br />Pedido: {$mT007['T007_T005_Id']}";
        }
        // Acrescenta endereço de entrega (caso seja diferente do padrão)
        if (!empty($mT007['T007_D148_Id_Entrega'])) {
            if (!empty($infCpl) || !empty($infAdFisco) || !empty($extra)) {
                $extra .= '<br />';
            }
            $sql = "SELECT * FROM D148 left join D020 on D020_Id=D148_D020_Id left join D018 on D018_Id=D148_D018_Id WHERE D148_Id = '{$mT007['T007_D148_Id_Entrega']}'";
            $mD148 = mysql_query($sql);
            $mD148 = mysql_fetch_assoc($mD148);
            $endereco = !empty($mD148['D148_Numero']) ? $mD148['D148_Rua'].", ".$mD148['D148_Numero'] : $mD148['D148_Rua'];
            $endereco .= !empty($mD148['D148_Complemento']) ? " - ".$mD148['D148_Complemento'] : '';
            $extra = trim($extra, '<br/>');
            $extra .= "ENDEREÇO DE ENTREGA: {$endereco} - {$mD148['D020_Nome_Cidade']} - {$mD148['D018_UF']} - Bairro: {$mD148['D148_Bairro']} - CEP: {$mD148['D148_Cep']}";
        }

        //
        echo <<<EOT
    <!-- Rodapé -->
    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">DADOS ADICIONAIS</span>
    <div style="margin: 0 5px 1px; width: 1088px; height: 120px; border: 1px solid black; border-bottom: 0;">
        <div style="float: left; width: 642px; height: 113px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
            INFORMAÇÕES COMPLEMENTARES<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$infCpl}{$infAdFisco}{$extra}</span>
        </div>
         <div style="float: left; width: 433px; height: 113px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            RESERVADO AO FISCO<br />
            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;"></span>
        </div>
    </div>
    <!-- Fim Rodapé -->
EOT;
}

        global $Banco_Dados;
        $footer = 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';
        if ($Banco_Dados == 'e276') {
            $footer = '&nbsp;';
        }
            echo <<<EOT
    <span style="margin-left: 5px; font-size: 13px;">{$footer}</span>
</body>
</html>
EOT;

        echo <<<EOT
    <!-- Cabeçalho 1 -->
    <!--<div style="width: 100%; height: 0; border-top: 2px dashed black; margin: 4px 0;"></div>-->
    <div style="margin: 100px 5px 5px 5px; width: 1090px; height: 82px; border: 1px solid black;">
        <div style="width: 900px; height: 36px; float: left; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
            RECEBI(EMOS) DE <span style="font-weight: bolder;">{$xNome}</span><br />
            OS PRODUTOS CONSTANTE DA NOTA FISCAL ELETRÔNICA INDICADA AO LADO, BEM COMO ATESTAMOS QUE OS MESMOS FORAM EXAMINADOS<br />
            SERVINDO O ACEITE DA PRESENTE PARA TODOS OS EFEITOS LEGAIS.
        </div>
        <div style="width: 183px; height: 82px; float: right; border-left: 1px solid black; text-align: center; font-size: 15px;">
            <table>
                <tr>
                    <td colspan="2" style="text-align: center; padding-bottom: 6px;">NF-E</td>
                </tr>
                <tr style="font-weight: bolder; margin: 3px 0 0 6px;">
                    <td>No.</td>
                    <td style="background-color: e6e6e6; width: 100%; text-align: center;">{$this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue)}</td>
                </tr>
                <tr style="font-weight: bolder; margin: 3px 0 0 6px;">
                    <td>SÉRIE</td>
                    <td style="width: 100%; text-align: center;">{$ide->getElementsByTagName('serie')->item(0)->nodeValue}</td>
                </tr>
                <tr>
                    <td>{$vlNF}</td>
                </tr>
            </table>
        </div>
        <div style="width: 900px; height: 38px; float: left;">
            <div style="width: 193px; height: 38px; float: left; padding: 1px 0 0 3px;">
                DATA DE RECEBIMENTO
            </div>
            <div style="width: 600px; height: 38px; float: left; border-left: 1px solid black; padding: 1px 0 0 3px;">
                IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR
            </div>
        </div>
    </div>
    <!-- Fim Cabeçalho 1 -->
EOT;

}
        
    }



}

?>
