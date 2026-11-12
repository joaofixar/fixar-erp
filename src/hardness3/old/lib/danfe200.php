<?php
namespace hardness;
/*
 DANFE EM PDF
 Programador: Evantoni Fuchs
 Classe PDF_Code128 obtida em: http://www.fpdf.org/en/script/script88.php
*/

include_once('old/fpdf/fpdf.php');
include_once('old/fpdf/code128.php');

if (!defined('FPDF_FONTPATH'))
{
    define('FPDF_FONTPATH','old/fpdf/font/');
}

class danfe
{

    var $canhoto;   // se true, imprime campos do canhoto
    var $issqn;     // se true, imprime campos do issqn
    var $pdf;       // objeto fpdf()
    var $xml;       // string XML NFe
    var $array_uf;  // usado para obter c�digo do estado do destinatario qdo em contingencia
    var $protocolo; // protocolo de autorizacao deve sair em infCpl
    var $data_hora; // data e hora de autorizacao devem sair em infCpl

    function setNomeArquivoPDF($x)
    {
        $this->nomearquivopdf=$x;
    }

    function setSaidaArquivo($x)
    {
        // Destino de envio do arquivo:
        // I envia o arquivo para o navegador com a op��o de 'salvar como'
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
		   //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS � DEFINIDO PARA 1,
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

        $dom = new \DomDocument;
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
		
		//-- Total de Linhas Impressas por p�gina
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
	            $this->linha(5.5, 150.0,  10.0,  7.6,     "", "OS PRODUTOS CONSTANTE DA NOTA FISCAL ELETR�NICA INDICADA AO LADO, BEM COMO ATESTAMOS QUE OS MESMOS FORAM EXAMINADOS,");
	            $this->linha(2.5, 150.0,  10.0, 10.20,     "", "SERVINDO O ACEITE DA PRESENTE PARA TODOS OS EFEITOS LEGAIS.");
	            $this->linha( 17,  40.0, 160.0,  4.8, "TBRL", "NF-e", "Arial,B,9,C");
	            $this->pdf->setXY(181, 8);
	            $this->pdf->Cell(15, 2, "No.");
	            $this->pdf->setXY(191, 6.8);
	            $this->pdf->SetFillColor(230,230,230);
	            $this->pdf->Cell(32, 3.8, "", 0, 0, "", true);
	            $this->pdf->setXY(181, 14);
	            $this->pdf->Cell(15, 0, "S�RIE");

	            $this->linha(8.5, 20, 172,  6.7, "", "", "", $this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue),   "Arial,B,10,L");
	            $this->linha(8.5, 20, 172, 11.9, "", "", "", $ide->getElementsByTagName('serie')->item(0)->nodeValue, "Arial,B,9,L");

	            $this->linha(8.5,    40, 10.0, 13.3, "TBRL", "DATA DE RECEBIMENTO");
	            $this->linha(8.5,   110, 50.0, 13.3, "TBRL", "IDENTIFICA��O E ASSINATURA DO RECEBEDOR");

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
	            $this->linha( 5.0, 190.0,  10.0, 120.0-$extra, "", "", "", "DANFE em Conting�ncia",   "Arial,B,30,C");
	            $this->linha( 5.0, 190.0,  10.0, 140.0-$extra, "", "", "", "Impresso em decorr�ncia", "Arial,B,30,C");
	            $this->linha( 5.0, 190.0,  10.0, 160.0-$extra, "", "", "", "de problemas t�cnicos",   "Arial,B,30,C");
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
    	            $this->linha( 8.5,    80, 120.0,  64.6-$extra,  "BTRL",  "PROTOCOLO DE AUTORIZA��O DE USO", "", "", "Arial,B,8,L");
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
    	            $this->linha( 8.5,    80, 120.0,  64.6-$extra,  "BTRL",  "PROTOCOLO DE AUTORIZA��O DE USO", "", $nNumero_Protocolo." ".troca_data_dma($cData_Protocolo)." �s ".$cHora_Protocolo, "Arial,B,8,L");
                }
	        }


         // se for ambiente de homologa��o, escreve SEM VALOR FISCAL
	        if ($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == 2  or $this->visualizacao=='S')
			{
	            $this->pdf->SetTextColor(170,170,170);
	            $this->linha( 5.0, 190.0,  10.0, 210.0-$extra, "", "", "", "SEM VALOR FISCAL", "Arial,B,50,C");
	            $this->pdf->SetTextColor(0,0,0);
	        }


	        $this->linha(39.2,  25.4,  94.6,  25.4-$extra,  "BTRL", "DANFE",              "Arial,B,12,C"); // DANFE
	        $this->linha(39.2,  25.4,  94.6,    31-$extra,      "", "DOCUMENTO AUXILIAR", "Arial,B,6,C");
	        $this->linha(39.2,  25.4,  94.6,    33-$extra,      "", "DA NOTA FISCAL",     "Arial,B,6,C");
	        $this->linha(39.2,  25.4,  94.6,    35-$extra,      "", "ELETR�NICA",         "Arial,B,6,C");

	        $this->linha(39.2,  25.4,  95.6,    42-$extra,      "", "0 - ENTRADA",        "Arial,B,6,L");
	        $this->linha(39.2,  25.4,  95.6,    45-$extra,      "", "1 - SA�DA",          "Arial,B,6,L");
	        $Entrada_Saida = $ide->getElementsByTagName('tpNF')->item(0)->nodeValue;
	        $Entrada_Saida = " $Entrada_Saida ";
	        $this->linha(   7,     5,   110,    41-$extra,  "BTRL", "", "", $Entrada_Saida, "Arial,B,10,L");

	        $this->linha(39.2,  25.4,  95.6,    52-$extra,      "", "No.",   "Arial,B,6,L");
	        $this->linha(39.2,  25.4,  95.6,    56-$extra,      "", "S�RIE", "Arial,B,5,L");
	        $this->linha(39.2,  25.4,  95.6,    60-$extra,      "", "FOLHA", "Arial,B,5,L");
	        $this->linha(39.2,  25.4,   101,  48.5-$extra,      "", "", "", $this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue), "Arial,B,10,L");
	        $this->linha(39.2,  25.4,   102,  52.2-$extra,      "", "", "", $ide->getElementsByTagName('serie')->item(0)->nodeValue);
	        $this->linha(39.2,  25.4,   102,  56.0-$extra,      "", "", "", "$nPagina_Atual/$nTotal_Paginas");

            $chave_acesso1=$this->formata_chave_acesso($chave_acesso);

	        $this->linha(14.8,    80, 120.0,  25.4-$extra,  "BTRL"); // codigo de barras da chave
	        $this->linha_itens( 9.6,    80, 120.0,  40.0-$extra, "", "CHAVE DE ACESSO" , "Arial,,7,L");
	        $this->linha( 9.6,    80, 120.0,  40.2-$extra,  "BTRL",  "", "", $chave_acesso1 , "Arial,B,9,L");
	        $this->linha( 8.5, 110.0,  10.0,  64.6-$extra,  "BTRL",  "NATUREZA DA OPERA��O", "", utf8_decode($ide->getElementsByTagName("natOp")->item(0)->nodeValue));

	        $this->linha( 8.5,  63.4,  10.0,  73.1-$extra,  "BTRL", "INSCRI��O ESTADUAL", "", utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue));
	        $this->linha( 8.5,  63.3,  73.4,  73.1-$extra,  "BTRL", "INSCRI��O ESTADUAL DO SUBST. TRIB.", "", utf8_decode($emit->getElementsByTagName("IEST")->item(0)->nodeValue));
	        $this->linha( 8.5,  63.3, 136.7,  73.1-$extra,  "BTRL", "C.N.P.J.", "", $this->mask_cnpj($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue));

	        // destinatario/remetente
	        $this->linha( 4.2,    33,  10.0,  82.6-$extra,      "", "DESTINAT�RIO/REMETENTE", "Arial,B,6,L");
	        $this->linha( 8.5, 110.0,  10.0,  85.8-$extra,  "BTRL", "NOME/RAZ�O SOCIAL","", utf8_decode($dest->getElementsByTagName("xNome")->item(0)->nodeValue), "Arial,B,9,L");
	        $dest_doc = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($dest->getElementsByTagName("CPF")->item(0)->nodeValue);
	        $this->linha( 8.5,  51.0, 120.0,  85.8-$extra, "BTRLN", "C.N.P.J./C.P.F.","",   $dest_doc);
	        $this->linha( 8.5,  29.0,   171,  85.8-$extra,  "BTRL", "DATA DA EMISS�O","",   troca_data_dma($ide->getElementsByTagName("dEmi")->item(0)->nodeValue));
	        $dest_ender = utf8_decode($enderDest->getElementsByTagName("xLgr")->item(0)->nodeValue);
	        $dest_ender.= (!empty($enderDest->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($enderDest->getElementsByTagName("nro")->item(0)->nodeValue) : null;
	        $this->linha( 8.5,  91.0,  10.0,  94.3-$extra,  "BTRL", "ENDERE�O","",          $dest_ender);
	        $this->linha( 8.5,  45.0, 101.0,  94.3-$extra,  "BTRL", "BAIRRO/DISTRITO","",   utf8_decode($enderDest->getElementsByTagName("xBairro")->item(0)->nodeValue));
	        $this->linha( 8.5,  25.0, 146.0,  94.3-$extra,  "BTRL", "CEP","",               utf8_decode($enderDest->getElementsByTagName("CEP")->item(0)->nodeValue));
	        $this->linha( 8.5,  29.0,   171,  94.3-$extra, "BTRLN", "DATA DA SA�DA/ENTRADA","",troca_data_dma($ide->getElementsByTagName("dSaiEnt")->item(0)->nodeValue));
	        $this->linha( 8.5,  64.0,  10.0, 102.8-$extra,  "BTRL", "MUNIC�PIO","",         utf8_decode($enderDest->getElementsByTagName("xMun")->item(0)->nodeValue));
	        $this->linha( 8.5,  35.0,  74.0, 102.8-$extra,  "BTRL", "FONE/FAX","",          utf8_decode($enderDest->getElementsByTagName("fone")->item(0)->nodeValue));
	        $this->linha( 8.5,  11.0, 109.0, 102.8-$extra,  "BTRL", "UF","",                utf8_decode($enderDest->getElementsByTagName("UF")->item(0)->nodeValue));
	        $this->linha( 8.5,  51.0, 120.0, 102.8-$extra,  "BTRL", "INSCRI��O ESTADUAL","",utf8_decode($dest->getElementsByTagName("IE")->item(0)->nodeValue));
	        
            $Hora=$ide->getElementsByTagName("hSaiEnt")->item(0)->nodeValue;
            if (empty($Hora))
            {
                $Hora=date("H:i:s");
            }
	        $this->linha( 8.5,  29.0,   171, 102.8-$extra, "BTRLN", "HORA DA SA�DA","",     $Hora);

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
		              $dups.= $dup->item($k)->getElementsByTagName('nDup')->item(0)->nodeValue." - ";
		              $dups.= troca_data_dma($dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue);
		              $dups.= " - R$ ".number_format($dup->item($k)->getElementsByTagName('vDup')->item(0)->nodeValue, 2, ",", ".")."    ";
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
		        $this->linha( 4.2,    56,  10.0, 124.6,      "", "C�LCULO DO IMPOSTO", "Arial,B,6,L");
		        $this->linha( 8.5,  37.5,  10.0, 127.8,  "BTRL", "BASE DE C�LCULO DO ICMS",     "", number_format($ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R");
		        $this->linha( 8.5,  37.5,  47.5, 127.8,  "BTRL", "VALOR DO ICMS",               "", number_format($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  37.5,  85.0, 127.8,  "BTRL", "BASE DE C�LCULO DO ICMS ST",  "", number_format($ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  37.5, 122.5, 127.8,  "BTRL", "VALOR DO ICMS ST",            "", number_format($ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  40.0, 160.0, 127.8,  "BTRL", "VALOR TOTAL DOS PRODUTOS",    "", number_format($ICMSTot->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0,  10.0, 136.3,  "BTRL", "VALOR DO FRETE",              "", number_format($ICMSTot->getElementsByTagName("vFrete")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0,  40.0, 136.3,  "BTRL", "VALOR DO SEGURO",             "", number_format($ICMSTot->getElementsByTagName("vSeg")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0,  70.0, 136.3,  "BTRL", "DESCONTO",                    "", number_format($ICMSTot->getElementsByTagName("vDesc")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
		        $this->linha( 8.5,  30.0, 100.0, 136.3,  "BTRL", "DESPESAS ACESS�RIAS",  "", number_format($ICMSTot->getElementsByTagName("vOutro")->item(0)->nodeValue, 2, ",", "."), "Arial,B,8,R" );
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
		        $this->linha( 8.5,  75.0,  10.0,   149,  "BTRL", "RAZ�O SOCIAL",        "", $transp_xNome);
		        $this->linha( 8.5,  31.0,  85.0,   149,  "BTRL", "FRETE POR CONTA DE",  "");
		        $this->linha( 8.5,  31.0,  85.0,   152,      "", "0-Emitente 2-Terceiros",  "");
		        $this->linha( 8.5,  31.0,  85.0,   154,      "", "1-Dest/Rem 9-Sem Frete",  "");
		        $this->linha( 3.5,   4.0, 110.0, 152.5,  "BTRL", "");
		        $Modalidade_Frete=$transp->getElementsByTagName("modFrete")->item(0)->nodeValue;
		        if ($Modalidade_Frete=='0')
		        {
		        	$Modalidade_Frete='O';
		        }
		        $this->linha( 3.5,   4.0, 110.2, 149.2,      "", "",                    "", "$Modalidade_Frete");
		        $this->linha( 8.5,  16.0, 116.0,   149,  "BTRL", "C�DIGO ANTT",         "", $veic_RNTC);
		        $this->linha( 8.5,  21.0, 132.0,   149,  "BTRL", "PLACA DO VE�CULO",    "", $veic_placa);
		        $this->linha( 8.5,   8.0, 153.0,   149,  "BTRL", "UF",                  "", $veic_UF);
		        $this->linha( 8.5,  39.0, 161.0,   149,  "BTRL", "C.N.P.J./C.P.F.",     "", $transp_doc);
		        $this->linha( 8.5,  80.0,  10.0, 157.5,  "BTRL", "ENDERE�O",            "", $transp_xEnder);
		        $this->linha( 8.5,  63.0,  90.0, 157.5,  "BTRL", "MUNIC�PIO",           "", $transp_xMun);
		        $this->linha( 8.5,   8.0, 153.0, 157.5,  "BTRL", "UF",                  "", $transp_UF);
		        $this->linha( 8.5,  39.0, 161.0, 157.5,  "BTRL", "INSCRI��O ESTADUAL",  "", $transp_IE);

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

		        $this->linha( 8.5,  26.0,  10.0,   166,  "BTRL", "QUANTIDADE",          "", number_format($qVol, 2, ",", "."), "Arial,B,8,R");
		        $this->linha( 8.5,  27.0,  36.0,   166,  "BTRL", "ESP�CIE",             "", utf8_decode($esp), "Arial,B,8,L");
		        $this->linha( 8.5,  27.0,  63.0,   166,  "BTRL", "MARCA",               "", utf8_decode($marca), "Arial,B,8,L");
		        $this->linha( 8.5,  46.0,  90.0,   166,  "BTRL", "NUMERA��O",           "", utf8_decode($nVol), "Arial,B,8,L");
		        $this->linha( 8.5,  32.0,   136,   166,  "BTRL", "PESO BRUTO",          "", number_format($pesoB, 3, ",", "."), "Arial,B,8,R");
		        $this->linha( 8.5,  32.0, 168.0,   166,  "BTRL", "PESO L�QUIDO",        "", number_format($pesoL, 3, ",", "."), "Arial,B,8,R");
		        
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

	        // dados dos produtos/servicos
	        $this->linha( 4.2,    40,  10.0, 175.5-$extra,     "", "DADOS DOS PRODUTOS/SERVI�OS", "Arial,B,6,L");
	        //$this->linha(78.0, 190.0,  10.0, 178.7-$extra, "BTRL", "");
	        $this->linha(2.6, 190.0, 10.0, 178.7-$extra, "BTRL",  "");
	        $this->linha(77.0+$extra_altura_itens,  15.0,  10.0, 178.7-$extra, "BTRL", "C�DIGO",    "Arial,,6,C");
	        $this->linha(77.0+$extra_altura_itens,  62.0,  25.0, 178.7-$extra, "BTRL", "DESCRI��O", "Arial,,6,C");
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

			for ($x=$nTotal_Linhas_Impressas;$x<$nItensPorPagina;$x++)
			{
	            //$this->linha(65.0,  15.0,  10.0, 178.9+($i*2.3)-$extra, "", "",     "Arial,,5,L");
			}

	        if ($this->issqn and $nPagina_Atual==1)
			{
	            // calculo do issqn
	            $this->linha(4.2, 23.0,  10.0, 244.3+$extra,     "", "C�LCULO DO ISSQN", "Arial,B,6,L");
	            $this->linha(8.5, 46.0,  10.0, 247.9+$extra, "BTRL", "INSCRI��O MUNICIPAL");
	            $this->linha(8.5, 47.0,  56.0, 247.9+$extra, "BTRL", "VALOR TOTAL DOS SERVI�OS");
	            $this->linha(8.5, 47.0, 103.0, 247.9+$extra, "BTRL", "BASE DE C�LCULO DO ISSQN");
	            $this->linha(8.5, 50.0, 150.0, 247.9+$extra, "BTRL", "VALOR DO ISSQN");
	        }

            if ($nPagina_Atual==1)
            {
		        // dados adicionais
		        $this->linha( 4.2,  23.0,  10.0, 257.4+$extra,      "", "DADOS ADICIONAIS", "Arial,B,6,L");

		        $this->linha(30.7, 113.0,  10.0, 260.6+$extra,  "BTRL", "INFORMA��ES COMPLEMENTARES");
		        $this->linha(30.7,  77.0, 123.0, 260.6+$extra,  "BTRL", "RESERVADO AO FISCO");


		        $this->pdf->setFont("Arial", "B", "8");
		        $this->pdf->SetAutopagebreak(false);

                $DadosCompl="";
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
                }

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
		        for ($i=0; $i<=strlen($infAdFisco); $i++)
				{
		            $txt.= $infAdFisco[$i];
		            if ($this->pdf->GetStringWidth($txt) >= 132 || $i >= strlen($infAdFisco))
					{
		                $this->pdf->setXY(2, $LinhaAdicional+$extra+($l*3));
		                $this->pdf->Cell(77, 0, $txt, 0, 0, 'L');
		                $txt = '';
		                $l++;
		            }
		        }

			}

            $this->linha( 4.2,    40,  10.0, 292,     "", "HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR", "Arial,,7,L");

			//-- Faz a verifica��o pra fim de pagina
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

}

?>
