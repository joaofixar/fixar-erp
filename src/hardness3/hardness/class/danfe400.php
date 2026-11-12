<?php
namespace hardness;

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
    
    // Função que redimensiona a altura e a largura da imagem
    function redimencionaImagem($imagem){
        // Verifica se a imagem existe para redimensionar    
        if (!empty($this->logomarca) && is_file('/tmp/'.$this->logomarca)){
            $Img_nfe = imagecreatefromjpeg('/tmp/'.$imagem);
            if(empty($Img_nfe)){
                $Img_nfe = imagecreatefrompng('/tmp/'.$imagem);
            }
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
            if ($Largura_Imagem < 0 || $Largura_Imagem > 240) {
                $porcentagem = (round((240 * 100) / $Largura_Imagem) - 100) / 100;
                if ($porcentagem <= 0) {
                    $porcentagem = $porcentagem + 1;
                }
                $Largura_Imagem = 240;
                $Altura_Imagem = $Altura_Imagem * $porcentagem;
            }
            
        }

        return array($Largura_Imagem, $Altura_Imagem);

    }

    function montaBarcode($chave_acesso, $caminhoSistema, $id){
        include_once('php-barcode.php');
        $im     = imagecreatetruecolor(900, 130);  
        $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
        $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
        imagefilledrectangle($im, 0, 0, 900, 130, $white);  
        $data = \Barcode::gd($im, $black, 450, 65, 0, "code128", $chave_acesso, 3, 120);  
        if($caminhoSistema!=null){
            imagepng($im, "{$caminhoSistema}.barcode.png");
            $caminhoImg = "{$caminhoSistema}.barcode.png";
        }else{
            imagepng($im, "/tmp/{$id}.barcode.png");
            $caminhoImg = "/tmp/{$id}.barcode.png";
        }    
        return $caminhoImg;
    }

    function geraHtml($caminhoSistema=null)
    {
        global $Banco_Dados, $g, $id, $sys;
        // Linhas / Páginas
        // Máximo de caracteres qeu a observação pode ocupar em uma linha
        $maxChars = 53;
        // Quantidade total de linhas que os produtos podem ocupar na primeira página
        $this->linhas_primeira_pagina = 40;

        // Quantidade total de linhas que o produto pode ocupar nas páginas diferentes de 1
        if($this->casas_decimais_preco == 8){
            $this->linhas_segunda_pagina = 76;    
        } elseif($this->casas_decimais_preco > 8){
            $this->linhas_segunda_pagina = 65;
        } else { 
            $this->linhas_segunda_pagina = 79;    
        } 

        // Define a quantidade total de linhas que deverão ser impressas no DANFE
        $nTotal_Linhas = 0;
        // Define a quantidade de itens que serão impressos no DANFE
        $nTotal_Itens_Contados = 0;
        // Define a altura do campo de informações adicionais do produto
        $lineHeightInfo = 13;
        //Define a altura do campo
        $lineHeightPadrao = 25;        
        // Define a altura do campo que irá conter as informações dos produtos no DANFE.
        $lineHeightGeral = 11;
        // Somar altura linha quando descrição for mais que 2 linhas
        $somaLinhaHeight = 13;
        // Define a quantidade total de páginas.
        $nTotal_Paginas = 1;
        // Vai definindo quantos itens estão sendo impressos
        $nTotal_Itens_Impressos=0;
        // Define a quantidade de itens que estão sendo Impressos por página
        $nTotal_Itens_Impressos_Tmp=0;
        $empresaP23 = '';
        // Define o texto das faturas e duplicatas
        $dups = "";
        // Máximo de caracteres que a observacao do produto pode ocupar em uma linha
        $maxCharcInfAdProd = 165;

        if (empty($this->xml))
        {
            return false;
        }
        
        
        // Verifica se a coluna C020_Flag_Codigo_CEST deve ser inserida no select do T007
        $vefificaC020 = mysqli_query("SHOW COLUMNS FROM C020 WHERE FIELD='C020_Flag_Codigo_CEST'");
        if(mysql_num_rows($vefificaC020) > 0){
            $camposC020 = " C020_Flag_Codigo_CEST, ";
        }

        
        // Busca os dados da nota, emitente e configuração para fazer validações
        $cClausula  = " select ";
        $cClausula .= " C004_Id, ";
        $cClausula .= " C004_CNPJ, ";
        $cClausula .= " C004_E_Mail, ";
        $cClausula .= " C004_Site, ";
        $cClausula .= " C020_NFe_Treinamento, ";
        $cClausula .= " T007_Id, ";
        $cClausula .= " C004_Codigo_Regime_Tributario, ";
        $cClausula .= " T007_Flag_Cancelada, ";
        $cClausula .= " D024_Nome_Fantasia, ";
        $cClausula .= " C007_Nome, ";
        $cClausula .= " T007_Flag_Trib_Transparencia, ";
        $cClausula .= " T007_Config_Vars_Danfe, ";
        $cClausula .= "  $camposC020 ";
        $cClausula .= " T007_T005_Id ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " left join C020 on C020_C004_Id=T007_C004_Id ";
        $cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " left join D024 on D024_Id=T007_D024_Id ";
        $cClausula .= " where T007_Id='".$this->T007_Id."' ";
        $cSql_T007 = mysqli_query($cClausula);
        $mT007 = mysql_fetch_array($cSql_T007);

        eval($mT007['T007_Config_Vars_Danfe']);

        //Variaveis de configuração global
        $sqlC031 = mysqli_query("SELECT C031_Valor, 
                                       C031_Campo 
                                  FROM C031 
                                 WHERE C031_C004_Id = '{$mT007['C004_Id']}'
                                   AND (C031_Campo='imprimirDuplicatasPortadorTipoECommerce' ||
                                        C031_Campo='imprimirNoDanfeEanNoLugarDoCodigo'	 ||
                                        C031_Campo='base64FonteDANFE' ||
                                        C031_Campo='adicionarNomeFantasiaClienteCanhotoDanfe' ||
                                        C031_Campo='imprimirCanhotoDanfe' || 
                                        C031_Campo='exibirValorNFDANFE' ||
                                        C031_Campo='adicionarPreenchimentoCPFRGCanhoto' || 
                                        C031_Campo='exibirObservacaoOCPorItemnaNF'
                                        );");
                                        
        while($rowC031 = mysql_fetch_assoc($sqlC031)){
            $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }


        // Verifica pela CFOP quais dados dos produtos serão impressos 
        // Ao encontrar a primeira CFOP preenchido o campo D006_Impressao_Dados_Produtos_DANFE, vai valer para todos os produtos
        $imprimeCodigo              = 'S';
        $imprimeDescricao           = 'S';
        $imprimeNCM                 = 'S';
        $imprimeCEST                = 'S';
        $imprimeCST                 = 'S';
        $imprimeCFOP                = 'S';
        $imprimeUnidade             = 'S';
        $imprimeQuantidade          = 'S';
        $imprimeValorUnitario       = 'S';
        $imprimeIPI                 = 'S';
        $imprimeST                  = 'S';
        $imprimeValorUnitarioIpiST  = 'S';
        $imprimeValorTotal          = 'S';
        $imprimeBaseICMS            = 'S';
        $imprimeValorICMS           = 'S';
        $imprimePercentualICMS      = 'S';
        $imprimePercentualIPI       = 'S';

        $D006 = mysql_query("SELECT D006_Impressao_Dados_Produtos_DANFE
                               FROM D006
                          LEFT JOIN T008 ON T008_D006_Id = D006_Id
                              WHERE T008_T007_Id = '".$this->T007_Id."'
                                AND D006_Impressao_Dados_Produtos_DANFE != ''
                              LIMIT 1");

        $mD006 = mysql_fetch_array($D006);     
        
        if (!empty($mD006['D006_Impressao_Dados_Produtos_DANFE'])) {
            
            $mFiltro = explode(',',$mD006['D006_Impressao_Dados_Produtos_DANFE']);  

            foreach ($mFiltro as $filtro) {

                if(strpos($filtro,'CODIGO') !== false){
                    $imprimeCodigo = 'N';
                }  

                if(strpos($filtro,'DESCRICAO') !== false){
                    $imprimeDescricao = 'N';
                }  

                if(strpos($filtro,'NCM') !== false){
                    $imprimeNCM = 'N';
                } 

                if(strpos($filtro,'CEST') !== false){
                    $imprimeCEST = 'N';
                } 

                if(strpos($filtro,'CST') !== false){
                    $imprimeCST = 'N';
                } 

                if(strpos($filtro,'CFOP') !== false){
                    $imprimeCFOP = 'N';
                } 

                if(strpos($filtro,'UNIDADE') !== false){
                    $imprimeUnidade = 'N';
                } 

                if(strpos($filtro,'QUANTIDADE') !== false){
                    $imprimeQuantidade = 'N';
                } 

                if(strpos($filtro,'VALOR_UNITARIO') !== false){
                    $imprimeValorUnitario = 'N';
                } 

                if(strpos($filtro,'VALOR_IPI') !== false){
                    $imprimeIPI = 'N';
                } 

                if(strpos($filtro,'VALOR_ST') !== false){
                    $imprimeST = 'N';
                } 

                if(strpos($filtro,'V.UNIT+IPI+ST') !== false){
                    $imprimeValorUnitarioIpiST = 'N';
                }         
                
                if(strpos($filtro,'VALOR_TOTAL') !== false){
                    $imprimeValorTotal = 'N';
                }         
                
                if(strpos($filtro,'BASE_ICMS') !== false){
                    $imprimeBaseICMS = 'N';
                }    

                if(strpos($filtro,'VALOR_ICMS') !== false){
                    $imprimeValorICMS = 'N';
                }  

                if(strpos($filtro,'%_ICMS') !== false){
                    $imprimePercentualICMS = 'N';
                }  

                if(strpos($filtro,'%_IPI') !== false){
                    $imprimePercentualIPI = 'N';
                }  
            }	
        }  

        /**********************************************************************
        ****************** ACESSANDO OS ELEMENTOS DO XML **********************
        **********************************************************************/
        
        // Acessa as tags mãe do XML
        $dom = new \DomDocument;
        $dom->loadXML($this->xml);
        $infNFe     = $dom->getElementsByTagName("infNFe")->item(0);
        $ide        = $dom->getElementsByTagName("ide")->item(0);
        $emit       = $dom->getElementsByTagName("emit")->item(0);
        $dest       = $dom->getElementsByTagName("dest")->item(0);
        $enderEmit  = $dom->getElementsByTagName("enderEmit")->item(0);
        $enderDest  = $dom->getElementsByTagName("enderDest")->item(0);
        $det        = $dom->getElementsByTagName("det");
        $detItem    = $dom->getElementsByTagName("prod");
        $cobr       = $dom->getElementsByTagName("cobr")->item(0);
        $dup        = $dom->getElementsByTagName('dup');
        $pag        = $dom->getElementsByTagName('pag')->item(0);
        $detPag     = $dom->getElementsByTagName('detPag');
        $ICMSTot    = $dom->getElementsByTagName("ICMSTot")->item(0);
        $transp     = $dom->getElementsByTagName("transp")->item(0);
        $transporta = $dom->getElementsByTagName("transporta")->item(0);
        $veicTransp = $dom->getElementsByTagName("veicTransp")->item(0);
        $vols       = $dom->getElementsByTagName("vol");
        $infAdic    = $dom->getElementsByTagName("infAdic")->item(0);
        $protNFe    = $dom->getElementsByTagName("protNFe")->item(0);
        $entrega    = $dom->getElementsByTagName("entrega")->item(0);

        // Dados do endereço do Emitente
        $xNome = utf8_decode($emit->getElementsByTagName("xNome")->item(0)->nodeValue);
        $nome_empresa = $emit->getElementsByTagName("xNome")->item(0)->nodeValue;
        $endereco_emitente = substr($enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue,0,60);
        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".$enderEmit->getElementsByTagName("nro")->item(0)->nodeValue : null;
        $endereco_emitente.= (!empty($enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
        $endereco_emitente .= "<br/>" . utf8_decode($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $endereco_emitente .= "<br/>" . utf8_decode($enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue).'/'.utf8_decode($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue);
        $endereco_emitente .= "<br/>" . "CEP: ".utf8_decode($emit->getElementsByTagName("CEP")->item(0)->nodeValue)." - FONE: ".utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue);

        // Dados do Cabeçalho da nota
        $nrNf = $this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue);
        $serieNf = $ide->getElementsByTagName('serie')->item(0)->nodeValue;
        $protocolo = '';
        $chave_acesso = str_replace('NFe', '', $infNFe->getAttribute("Id"));
        $chave_acesso1=$this->formata_chave_acesso($chave_acesso);
        $this->pdf->SetFillColor(0,0,0); 
        $this->pdf->Code128(145, 23.8-$extra, $chave_acesso, 75, 12);  
        
        // Informações sobre a operação
        $extraNatureza = utf8_decode($ide->getElementsByTagName("natOp")->item(0)->nodeValue);
        $extraIE = utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue);
        $extraIESubst = utf8_decode($emit->getElementsByTagName("IEST")->item(0)->nodeValue);
        $extraCNPJ = $this->mask_cnpj($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue);

        // Acessa os dados do cliente no XML
        $clienteNome = utf8_decode($dest->getElementsByTagName("xNome")->item(0)->nodeValue);
        $clienteCnpj = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($dest->getElementsByTagName("CPF")->item(0)->nodeValue);
        $clienteDataEmissao = gCorrigeData(substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 0, 10));
        $clienteEndereco = utf8_decode($enderDest->getElementsByTagName("xLgr")->item(0)->nodeValue);
        $clienteEndereco .= (!empty($enderDest->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($enderDest->getElementsByTagName("nro")->item(0)->nodeValue) : null;
        $clienteEndereco .= (!empty($enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
        $clienteBairro = utf8_decode($enderDest->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $clienteDataSaida = gCorrigeData(substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 0, 10));
        $clienteMunicipio = utf8_decode($enderDest->getElementsByTagName("xMun")->item(0)->nodeValue);
        $clienteFone = utf8_decode($enderDest->getElementsByTagName("fone")->item(0)->nodeValue);
        $clienteUF = utf8_decode($enderDest->getElementsByTagName("UF")->item(0)->nodeValue);
        $clienteCEP = utf8_decode($enderDest->getElementsByTagName("CEP")->item(0)->nodeValue);
        $clienteIE = utf8_decode($dest->getElementsByTagName("IE")->item(0)->nodeValue);
        $clienteHoraSaira=substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 11, 8);

        // Acessa os dados de entrega no XML
        if(!empty($entrega)){
            $entregaCnpj = (!empty($entrega->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($entrega->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($entrega->getElementsByTagName("CPF")->item(0)->nodeValue);
            $entregaNome = utf8_decode($entrega->getElementsByTagName("xNome")->item(0)->nodeValue);
            $entregaEndereco = utf8_decode($entrega->getElementsByTagName("xLgr")->item(0)->nodeValue);
            $entregaEndereco .= (!empty($entrega->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($entrega->getElementsByTagName("nro")->item(0)->nodeValue) : null;
            $entregaEndereco .= (!empty($entrega->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$entrega->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
            $entregaBairro = utf8_decode($entrega->getElementsByTagName("xBairro")->item(0)->nodeValue);
            $entregaMunicipio = utf8_decode($entrega->getElementsByTagName("xMun")->item(0)->nodeValue);
            $entregaFone = utf8_decode($entrega->getElementsByTagName("fone")->item(0)->nodeValue);
            $entregaUF = utf8_decode($entrega->getElementsByTagName("UF")->item(0)->nodeValue);
            $entregaCEP = utf8_decode($entrega->getElementsByTagName("CEP")->item(0)->nodeValue);
            $entregaIE = utf8_decode($entrega->getElementsByTagName("IE")->item(0)->nodeValue);
        }
        
        // Acessa os totais da nota
        $vBC = number_format($ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue, 2, ",", ".");
        $vICMS = number_format($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue, 2, ",", ".");
        $vBCST = number_format($ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue, 2, ",", ".");
        $vST = number_format($ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue, 2, ",", ".");
        $vICMSDeson = number_format($ICMSTot->getElementsByTagName("vICMSDeson")->item(0)->nodeValue, 2, ",", ".");
        $vTotTrib = number_format($ICMSTot->getElementsByTagName("vTotTrib")->item(0)->nodeValue, 2, ",", ".");
        $vProd = number_format($ICMSTot->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", ".");
        $vFrete = number_format($ICMSTot->getElementsByTagName("vFrete")->item(0)->nodeValue, 2, ",", ".");
        $vSeg = number_format($ICMSTot->getElementsByTagName("vSeg")->item(0)->nodeValue, 2, ",", ".");
        $vDesc = number_format($ICMSTot->getElementsByTagName("vDesc")->item(0)->nodeValue, 2, ",", ".");
        $vOutro = number_format($ICMSTot->getElementsByTagName("vOutro")->item(0)->nodeValue, 2, ",", ".");
        $vIPITotal = number_format($ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue, 2, ",", ".");
        $vNF = number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", ".");
        
        // Acessa os dados da transportadora no XML
        if (is_object($transporta)){
            $transp_doc     = (!empty($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($transporta->getElementsByTagName("CPF")->item(0)->nodeValue);
            $transp_xNome   = utf8_decode($transporta->getElementsByTagName("xNome")->item(0)->nodeValue);
            $transp_xEnder  = utf8_decode($transporta->getElementsByTagName("xEnder")->item(0)->nodeValue);
            $transp_xMun    = utf8_decode($transporta->getElementsByTagName("xMun")->item(0)->nodeValue);
            $transp_UF      = utf8_decode($transporta->getElementsByTagName("UF")->item(0)->nodeValue);
            $transp_IE      = utf8_decode($transporta->getElementsByTagName("IE")->item(0)->nodeValue);
        } 

        if (is_object($veicTransp)){
            $veic_RNTC      = utf8_decode($veicTransp->getElementsByTagName("RNTC")->item(0)->nodeValue);
            $veic_placa     = utf8_decode($veicTransp->getElementsByTagName("placa")->item(0)->nodeValue);
            $veic_UF        = utf8_decode($veicTransp->getElementsByTagName("UF")->item(0)->nodeValue);
        }
        
        $Modalidade_Frete=$transp->getElementsByTagName("modFrete")->item(0)->nodeValue;

        /**********************************************************************
        ****** VERIFICAÇÕES SOBRE COMO OS DADOS SERÃO IMPRESSOS NO DANFE ******
        **********************************************************************/      
        // Conta a quantidade de produtos
        $nTotal_Itens_Contados = $detItem->length;
        $nTotal_Linhas = $nTotal_Itens_Contados*2;
        
        // Conta a quantidade de páginas
        $nTotal_Paginas = $this->getTotalPaginas($nTotal_Linhas);        
        
        // Formata a hora de saída caso não esteja preenchida no DANFE
        if (empty($clienteHoraSaira)){
            $clienteHoraSaira=date("H:i:s");
        }

        // Verifica se será informado o e-mail ou o site
        if (!empty($mT007[C004_E_Mail]))
        {
            $endereco_emitente .= "<br/>" . "$mT007[C004_E_Mail]";
        }
        if (!empty($mT007[C004_Site]))
        {
            $endereco_emitente .= "<br/>" . "$mT007[C004_Site]";
        }

        // Verifica o tipo de emissão da nota, para imprimir mensagem da chave de acesso
        if ($ide->getElementsByTagName("tpEmis")->item(0)->nodeValue != 1 and $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue != 3){
            // contingencia
            $empresaP23 .= "DANFE em Contingência Impresso em decorrência de problemas têcnicos<br />";
            
            $dados_nfe = $this->array_uf[$enderDest->getElementsByTagName("UF")->item(0)->nodeValue];
            $dados_nfe.= $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue;
            $dados_nfe.= $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
            $dados_nfe.= sprintf("%014s", str_replace(',', '', number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", "")));
            $dados_nfe.= 1; // ICMSp
            $dados_nfe.= 2; // ICMSs
            $data_emissao = gCorrigeData($ide->getElementsByTagName("dEmi")->item(0)->nodeValue);
            $dados_nfe.= $data_emissao[0].$data_emissao[1];
            $dados_nfe.= $this->calcula_dv($dados_nfe);

            $empresaP23 .= "DADOS DA NF-e<br />";
            $empresaP23 .= $dados_nfe;

        } else {
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
                $protocolo = $nNumero_Protocolo." ".gCorrigeData($cData_Protocolo)." às ".$cHora_Protocolo;
            }
        }
        
        // Verifica se o tipo é E-commerce e se o cliente habilitou para imprimir as duplicatas desse tipo
        $sqlD027 = mysqli_query("SELECT D027_Flag_Tipo 
                                  FROM T007 
                             LEFT JOIN T118 ON T118_T007_Id = T007_Id 
                             LEFT JOIN D027 ON D027_Id = T118_D027_Id
                                 WHERE T007_Id = '{$mT007['T007_Id']}' AND IFNULL(D027_Flag_Tipo,'0') = 4 ");
        
        // Imprime no DANFE as informações da Faturas e Duplicatas, essas informações só irão aparecer na página 1
        // Imprime as faturas e duplicatas no DANFE
        if ((count($dup) > 0 || count($detPag) > 0) && (mysql_num_rows($sqlD027) <= 0 || (mysql_num_rows($sqlD027) > 0 && $g['C031']['imprimirDuplicatasPortadorTipoECommerce'] == 'S'))){
            // Verifica a quantidade de duplicatas
            $countDup = $dup->length;
            $countDetPag = $detPag->length;
            $tpag = "";

            $referencia = ($dup->length > 0) ? $dup : $detPag;
            
            foreach($referencia as $k => $d){
                // Só imprime 9 duplicatas
                if($k==9){ 
                    break;
                }
                // Obtém a informação do tipo de pagamento através do código informado no XML
                if($k<$countDetPag){
                    $tpag = $detPag->item($k)->getElementsByTagName('tPag')->item(0)->nodeValue;
                }else{
                    $tpag = $tpag;
                }

                if(!empty($tpag)){
                    if($countDup >=7){
                        $portador = array('01' => 'DINH ', 
                                          '02' => 'CHEQ ', 
                                          '03' => 'CART CRÉD ', 
                                          '04' => 'CART DÉB ', 
                                          '05' => 'CRÉD LOJ ',
                                          '10' => 'VAL ALIM ', 
                                          '11' => 'VAL REF ', 
                                          '12' => 'VAL PRES ',
                                          '13' => 'VAL COMB ', 
                                          '14' => 'DUPL MERC ',
                                          '15' => 'BOL BANC ', 
                                          '16' => 'DEP BANC ',
                                          '17' => 'PIX ',
                                          '18' => 'TRA BANC ',
                                          '19' => 'PROG FID ',
                                          '90' => 'SEM PAG ', 
                                          '99' => 'OUTROS ');
                    }else{
                        $portador = array('01' => 'DINHEIRO ',
                                          '02' => 'CHEQUE ',
                                          '03' => 'CARTÃO DE CRÉDITO ', 
                                          '04' => 'CARTÃO DE DÉBITO ', 
                                          '05' => 'CRÉDITO LOJA ', 
                                          '10' => 'VALE ALIMENTAÇÃO ', 
                                          '11' => 'VALE REFEIÇÃO ', 
                                          '12' => 'VALE PRESENTE ', 
                                          '13' => 'VALE COMBUSTÍVEL ',
                                          '14' => 'DUPLICATA MERCANTIL ', 
                                          '15' => 'BOLETO BANCÁRIO ', 
                                          '16' => 'DEPÓSITO BANCÁRIO ',
                                          '17' => 'PIX ',
                                          '18' => 'TRANSFERÊNCIA BANCÁRIA ',
                                          '19' => 'PROGRAMA FIDELIDADE ',
                                          '90' => 'SEM PAGAMENTO ', 
                                          '99' => 'OUTROS ');
                    } 
                }
                
                // Monta o texto que será impresso em faturas/duplicatas
                $dups.= ($dup->length > 0) ? $dup->item($k)->getElementsByTagName('nDup')->item(0)->nodeValue." " : '';
                $dups.= $portador[$tpag];
                $dups.= ($dup->length > 0) ? "<b>".date('d/m/y', strtotime($dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue))."</b>" : 'À VISTA ';
                $dups.= "<b> R$".number_format(($dup->length > 0) ? $dup->item($k)->getElementsByTagName('vDup')->item(0)->nodeValue : $detPag->item($k)->getElementsByTagName('vPag')->item(0)->nodeValue, 2, ",", ".")."&nbsp;&nbsp;</b>";
            }
        }
        
        // Redimensiona a logo no DANFE
        $alturaLargura = $this->redimencionaImagem($this->logomarca);
        $Largura_Imagem = $alturaLargura[0];
        $Altura_Imagem = $alturaLargura[1];
        
        // Monta as páginas do DANFE
        $totalPaginasReal = 0;
        $html = '';
        for ($pagina = 1; $pagina <= 999; $pagina++) {
            // Declaração das variavéis que serão utilizadas nesse loop
            $nTotal_Itens_Impressos_Tmp = 0;
            $tabelaProdutos = '';
            $Descricao_Frete = "";

            // Verifica se todos os produtos foram impressos no DANFE
            if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)
            {
                break;
            }

            // Define o tamanho da margem superior
            $marginTop = ($pagina == 1) ? 0 : 85;
            
            // Monta a descrição do frete que será impresso no DANFE
            if($Modalidade_Frete == 0){
                $Descricao_Frete = "0 - REMETENTE";
            }else if($Modalidade_Frete == 1){
                $Descricao_Frete = "1 - DESTINATÁRIO";
            }else if($Modalidade_Frete == 2){
                $Descricao_Frete = "2 - TERCEIROS";
            }else if($Modalidade_Frete == 3){
                $Descricao_Frete = "3 - PRÓPRIO REMETENTE";
            }else if($Modalidade_Frete == 4){
                $Descricao_Frete = "4 - PRÓPRIO DESTINATÁRIO";
            }else if($Modalidade_Frete == 9){
                $Descricao_Frete = " 9 - SEM FRETE";
            }

            // faz somatorios/concatenacoes dos volumes e coloca nos campos
            foreach ($vols as $key => $v){
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

            // Verifica se a coluna adicionada nas informaçõe dos produtos será CST ou CSOSN de acordo com o regime tributário da empresa
            if ($mT007[C004_Codigo_Regime_Tributario]=='3'){
                $Titulo_CST = "CST";
            } else {
                $Titulo_CST = "CSOSN";
            }

            if(!empty($entrega)){
                $this->linhas_primeira_pagina = 30;
            }
            
            // Monta o html com os dados dos produtos no DANFE
            //for ($i = 0; $i < $nTotal_Itens_Contados; $i++) {
            foreach ($detItem as $nItem => $tipoProduto){
                // Define a quantidade aproximada de tributos
                $valorAproxTrib = '';
                // Variavel que define a quantidade de linhas que um produto irá ocupar
                $linhasAdicionadas = 0;

                $lineHeight = $lineHeightPadrao;
                
                if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados){
                    break;
                }

                /***********************************************************************************
                *************** Acessa as informações dos produtos que estão no xml ****************
                ***********************************************************************************/
                
                $prod = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("prod")->item(0);
                $imposto = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("imposto")->item(0);
                $ICMS = $imposto->getElementsByTagName("ICMS")->item(0);
                $IPI  = $imposto->getElementsByTagName("IPI")->item(0);
                $cDescricao = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
                $cDescricao = trim($cDescricao);
                //$cDescricao = utf8_encode($cDescricao);
                $infAdProd = $det->item($nTotal_Itens_Impressos)->getElementsByTagName("infAdProd")->item(0);
                $cInfAdProd = (!empty($infAdProd)) ? $infAdProd->nodeValue : '';
                $cInfAdProd = trim($cInfAdProd);
                $cInfAdProd = utf8_encode($cInfAdProd);
                $xPed = $prod->getElementsByTagName("xPed")->item(0);
                $cOrdem_Compra = (!empty($xPed)) ? $xPed->nodeValue : '';
                $cOrdem_Compra = trim($cOrdem_Compra);
                $nItemPed = $prod->getElementsByTagName("nItemPed")->item(0);
                $cItem_Ordem_Compra = (!empty($nItemPed)) ? $nItemPed->nodeValue : '';
                $cItem_Ordem_Compra = trim($cItem_Ordem_Compra);
                $cReferencia=$prod->getElementsByTagName("cProd")->item(0)->nodeValue;
                $cReferencia = utf8_encode($cReferencia);
                $nCodigo_Barras=$prod->getElementsByTagName("cEAN")->item(0)->nodeValue;
                $nQuantidade=$prod->getElementsByTagName("qCom")->item(0)->nodeValue;
                $NCM = (!empty($prod->getElementsByTagName("NCM")->item(0)->nodeValue)) ? $prod->getElementsByTagName("NCM")->item(0)->nodeValue : '';
                $CEST = (!empty($prod->getElementsByTagName("CEST")->item(0)->nodeValue)) ? $prod->getElementsByTagName("CEST")->item(0)->nodeValue : '';
                $CFOP = $prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
                $unidade = utf8_decode($prod->getElementsByTagName("uCom")->item(0)->nodeValue);
                $quantidade = number_format($nQuantidade, $this->casas_decimais_quantidade, ",", ".");
                $valor_unitario = $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue;
                $valor_total = number_format($prod->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", ".");
                $vTotTribItem = $imposto->getElementsByTagName("vTotTrib")->item(0);
                $valor_total_tributos = number_format((!empty($vTotTribItem) ? $vTotTribItem->nodeValue : 0), $this->casas_decimais_preco, ",", ".");
                $vBCIcmsItem = (!empty($ICMS)) ? $ICMS->getElementsByTagName("vBC")->item(0) : null;
                $vIcmsItem = (!empty($ICMS)) ? $ICMS->getElementsByTagName("vICMS")->item(0) : null;
                $vICMSSTItem = (!empty($ICMS)) ? $ICMS->getElementsByTagName("vICMSST")->item(0) : null;
                $pIcmsItem = (!empty($ICMS)) ? $ICMS->getElementsByTagName("pICMS")->item(0) : null;
                $bc_icms = number_format((!empty($vBCIcmsItem) ? $vBCIcmsItem->nodeValue : 0), $this->casas_decimais_preco, ",", ".");
                $v_icms = number_format((!empty($vIcmsItem) ? $vIcmsItem->nodeValue : 0), $this->casas_decimais_preco, ",", "");
                $vIPI = (!empty($IPI) && !empty($IPI->getElementsByTagName("vIPI")->item(0))) ? $IPI->getElementsByTagName("vIPI")->item(0)->nodeValue : 0;
                $vICMSST = (!empty($vICMSSTItem)) ? $vICMSSTItem->nodeValue : 0;
                $p_icms = number_format((!empty($pIcmsItem) ? $pIcmsItem->nodeValue : 0), 2 , ",", ".");
                $pIPI = (!empty($IPI) && !empty($IPI->getElementsByTagName("pIPI")->item(0))) ? $IPI->getElementsByTagName("pIPI")->item(0)->nodeValue : 0;
                $pIPI = number_format($pIPI, 2, ",", ".");
                $CEST = $prod->getElementsByTagName("CEST")->item(0)->nodeValue;
                $STUnit = $vICMSST / $nQuantidade;
                $IPIUnit = $vIPI / $nQuantidade;
                $valor_total = number_format(($valor_unitario + $IPIUnit + $STUnit) * $nQuantidade,2, ",",".");
                $valor_bruto = number_format($valor_unitario + $IPIUnit + $STUnit, $this->casas_decimais_preco, ",",".");
                $valor_unitario = number_format($prod->getElementsByTagName("vUnCom")->item(0)->nodeValue, $this->casas_decimais_preco, ",", ".");
                $STUnit = number_format( $STUnit , $this->casas_decimais_preco, ",", ".");
                $IPIUnit = number_format( $IPIUnit, $this->casas_decimais_preco, ",", ".");
                $vICMSST = number_format($vICMSST, $this->casas_decimais_preco, ",", "");
                $vIPI = number_format($vIPI, $this->casas_decimais_preco, ",", "");
                
                /***********************************************************************************
                ******* VERIFICAÇÕES QUANTO A INFORMAÇÃO PARA SEREM IMPRESSAS DOS PRODUTOS *********
                ***********************************************************************************/

                // Verifica se vai imprimir o EAN no lugar do código do produto
                if (!empty($nCodigo_Barras) && $g['C031']['imprimirNoDanfeEanNoLugarDoCodigo']=='S' && $nCodigo_Barras != 'SEM GTIN'){
                    $cReferencia=$nCodigo_Barras;
                }

                if (!empty($ICMS)) {
                    $orig = $ICMS->getElementsByTagName("orig")->item(0);
                    $csosn = $ICMS->getElementsByTagName("CSOSN")->item(0);
                    $cst = $ICMS->getElementsByTagName("CST")->item(0);
                    if (!empty($csosn)) {
                        $CST = $orig->nodeValue.$csosn->nodeValue;
                    } else {
                        $CST = $orig->nodeValue.$cst->nodeValue;
                    }
                } else {
                    $CST = '';
                }
                
                // De acordo com a descrição do produto, verifica quantas linhas o produto irá ocupar no DANFE
                if (strlen($cDescricao) > ($maxChars * 2)) {
                    $nTotal_Itens_Impressos_Tmp++;
                    $nTotal_Itens_Impressos_Tmp++;
                    $linhasAdicionadas++;
                    $linhasAdicionadas++;
                    $lineHeight += $somaLinhaHeight;
                } else if (strlen($cDescricao) > $maxChars) {
                    $nTotal_Itens_Impressos_Tmp++;
                    $linhasAdicionadas++;
                } else {
                    $nTotal_Itens_Impressos_Tmp++;
                    $linhasAdicionadas++;
                }

                $nTotal_Itens_Impressos++;
                $nTotal_Itens_Impressos_Tmp++;
                
                // Adiciona as informações da Ordem de Compra no item
                if (!empty($cOrdem_Compra) && $g['C031']['exibirObservacaoOCPorItemnaNF'] != 'N')
                {
                    $cOrdem_Compra=' > Ped.Compra: '.$cOrdem_Compra;
                    if (!empty($cItem_Ordem_Compra))
                    {
                        $cOrdem_Compra.=' - Item: '.$cItem_Ordem_Compra;
                    }
                    $cDescricao .= '<br />' . utf8_encode($cOrdem_Compra);
                    $nTotal_Itens_Impressos_Tmp++;
                    $linhasAdicionadas++;
                    $lineHeight += $somaLinhaHeight;
                }
                
                // Verifica se será impresso o Código CEST no DANFE
                // Somente o sistema de nota utiliza essa tabela de configuração C020 (Configuração -> Parametrização -> Geral)
                $mT007['C020_Flag_Codigo_CEST'] = empty($mT007['C020_Flag_Codigo_CEST']) ? 'N' : $mT007['C020_Flag_Codigo_CEST'];           
                if(@$mT007['C020_Flag_Codigo_CEST']=='S' && (!empty($CEST))){
                    $cCEST = " CEST: ".$CEST;
                } else {
                    $cCEST="";
                }

                // Adiciona na descrição o código CEST
                $cDescricao .= utf8_encode($cCEST);

                // Define a altura do campo da tabela  
                $lineHeightTmp = $lineHeight;
                // Define a altura da linha do campo da tabela
                $lineHeightGeralTmp = $lineHeightGeral;
                $border = '';

                // Se houver informações adicionais de produtos diminui a altura da linha e do campo que irá conter os produtos
                if (!empty($cInfAdProd)) {
                    $lineHeightTmp = $lineHeight - 1;
                    $lineHeightGeralTmp = $lineHeightGeral - 1;
                }

                // Verifica se será adicionado o valor total de tributos
                if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S' && $vTotTrib > 0) {
                    $valorAproxTrib = <<<EOT
                        <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; text-align: right;{$border}">{$valor_total_tributos}</td>
EOT;
                }
 
                // Apresenta as informações dos produtos no DANFE
                $tabelaProdutos .= <<<EOT
                    <tr style="font-weight: bolder;">
EOT;
                        if($imprimeCodigo == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; text-align: center;">{$cReferencia}</td>
EOT;
                        }

                        if($imprimeDescricao == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; font-family: Courier New">{$cDescricao}</td>
EOT;
                        }

                        if($imprimeNCM == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;font-size:10px;">{$NCM}</td>
EOT;
                        }

                        if($imprimeCEST == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;font-size:10px;">{$CEST}</td>
EOT;
                        }

                        if($imprimeCST == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;font-size:10px;">{$CST}</td>
EOT;
                        }

                        if($imprimeCFOP == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;">{$CFOP}</td>
EOT;
                        }

                        if($imprimeUnidade == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;">{$unidade}</td>
EOT;
                        }

                        if($imprimeQuantidade == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$quantidade}</td>
EOT;
                        }

                        if($imprimeValorUnitario == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$valor_unitario}</td>
EOT;
                        }

                        if($imprimeIPI == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right; width:50px">{$vIPI}</td>
EOT;
                        }

                        if($imprimeST == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right; width:50px">{$STUnit}</td>
EOT;
                        }                   

                        if($imprimeValorUnitarioIpiST == 'S'){
                            $tabelaProdutos .= <<<EOT
                                <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right;">{$valor_bruto}</td>
EOT;
                        }

                        if($imprimeValorTotal == 'S'){
                            $tabelaProdutos .= <<<EOT
                            <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right; width: 70px">{$valor_total}</td>
EOT;
                        }

                        $tabelaProdutos .= <<<EOT
                            {$valorAproxTrib}
EOT;

                        if($imprimeBaseICMS == 'S'){
                            $tabelaProdutos .= <<<EOT
                            <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right; width: 70px">{$bc_icms}</td>
EOT;
                        }

                        if($imprimeValorICMS == 'S'){
                            $tabelaProdutos .= <<<EOT
                            <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: right; width: 45px">{$v_icms}</td>
EOT;
                        }

                        if($imprimePercentualICMS == 'S'){
                            $tabelaProdutos .= <<<EOT
                            <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black; {$border} text-align: center;">{$p_icms}</td>
EOT;
                        }

                        if($imprimePercentualIPI == 'S'){
                            $tabelaProdutos .= <<<EOT
                            <td style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; {$border} text-align: center;">{$pIPI}</td>
EOT;
                        }

                $tabelaProdutos .= <<<EOT
                    </tr>
EOT;
                // Imprime no danfe as informações adicionais do produto, caso exista e incrementa uma linha
                if (!empty($cInfAdProd)) {
                    $lineHeightTmp = $lineHeightInfo - 1;
                    $lineHeightGeralTmp = $lineHeightGeral - 1;
                    $tabelaProdutos .= <<<EOT
                        <tr style="font-weight: bolder;">
                            <td style="vertical-align: top; height: {$lineHeightInfo}px; line-height: {$lineHeightGeralTmp}px; border-right: 1px solid black;">&nbsp;</td>
                            <td colspan=100 style="vertical-align: top; height: {$lineHeightTmp}px; line-height: {$lineHeightGeralTmp}px; border-bottom: 0px solid black;">{$cInfAdProd}</td>
                        </tr>
EOT;
                    if(strlen($cInfAdProd) > $maxCharcInfAdProd){
                        // Se a informação adicional for maior que é permitido em 1 linha (165 caracteres) adiciona duas linhas
                        $nTotal_Itens_Impressos_Tmp++;
                        $nTotal_Itens_Impressos_Tmp++;
                        $linhasAdicionadas++;
                        $linhasAdicionadas++;
                    } else {
                        $nTotal_Itens_Impressos_Tmp++;
                        $linhasAdicionadas++;
                    }    
                }
            
                // Verifica se será necessário inserir uma nova página
                // 1° verifica se a página é igual a 1
                // 2° Verifica se a quantidade total de itens que já foram impressos naquela página é maior que capacidade de linhas da primeira página
                // 3° Verifica se a quantidade total de itens impressos na página 1 é maior que a capacidade de itens impressos nessa página
                if ($pagina == 1 && ( (($nTotal_Itens_Impressos_Tmp - $linhasAdicionadas) > ($this->linhas_primeira_pagina+1)) || (($nTotal_Itens_Impressos_Tmp) > ($this->linhas_primeira_pagina+1)) ) ) {
                    break;
                } 
                
                // Verifica se será necessário abrir uma nova página
                // 1° Verifica se não está na página 1
                // 2° Verifica se a quantidade total de itens impressos na página atual é maior que a capacidade de linhas suportada pela segunda página
                if ($pagina != 1 && (($nTotal_Itens_Impressos_Tmp - $linhasAdicionadas) > ($this->linhas_segunda_pagina+1)) ) {
                    break;
                }
            }

            // Quando o último produto é impresso, será adicionada uma linha preta no final da última página.
            if ((($nTotal_Itens_Impressos_Tmp+1)< (($pagina == 1) ? $this->linhas_primeira_pagina : $this->linhas_segunda_pagina)) AND ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)) {
                $tabelaProdutos .= "<tr><td colspan=20 style='border-top:1px solid black;'>&nbsp;</td></tr>";
            }
            
            // Monta o barcode para ser impresso no DANFE
            $caminhoImg = $this->montaBarcode($chave_acesso, $caminhoSistema, $id);

            if ($mT007['C004_CNPJ'] != $extraCNPJ) {
                $this->logomarca = '';
            }
            
           
            // HTML
            $html .= <<<EOT
                <html>
                <head>
EOT;
            // Define  a fonte que será utilizada
            $g['C031']['base64FonteDANFE'] = trim($g['C031']['base64FonteDANFE']);
            include_once('fonts.php');
            
            if(!empty($g['C031']['base64FonteDANFE']) && $g['C031']['base64FonteDANFE'] != ''){
                $html .= <<<EOT
                    <style>
                        @font-face {
                            font-family: '{$g['C031']['base64FonteDANFE']}';
                            src: url(data:font/truetype;charset=utf-8;base64,{$fonts[$g['C031']['base64FonteDANFE']]});
                        }
                    </style>
EOT;
            }

            $html .= <<<EOT
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <body style="width: 1100px; border: 0; padding: 0; margin: 5px 0px; font-size: 11px; font-family: Helvetica;">
                        <div style="width: 0; height: 0; border: 0; margin: 0; padding: 0; position: relative;">
EOT;

            // Como será a impressão do DANFE: Se é pré-visualização, se a nota foi emitida em homologação, se a nota foi cancelada.
            if ($this->visualizacao=='S'){
                // transform:rotate(-45deg); -ms-transform:rotate(-45deg); -webkit-transform:rotate(-45deg);
                $html .= <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
                        PRÉ VISUALIZAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
                    </div>
EOT;
            }elseif ($mT007[C020_NFe_Treinamento]=='S'){
                
                $html .= <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
                        DEMOSTRAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
                    </div>
EOT;
            }elseif ($mT007[T007_Flag_Cancelada]=='S'){
                $html .= <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 120px; z-index: 100; top: 554px; left: 155px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial; color: #f42e2c;">
                        CANCELADA
                    </div>
EOT;
            } else if ($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == 2) {
                $html .= <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 100px; z-index: 100; top: 554px; left: 135px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
                        SEM VALOR FISCAL
                    </div>
EOT;
            }
            
            $html .= <<<EOT
                </div>
                <div style="width: 0; height: {$marginTop}px; border: 0; margin: 0; padding: 0; "></div>
EOT;
                    
            if($g['C031']['adicionarNomeFantasiaClienteCanhotoDanfe']=='S'){ 
                $nomeFantasia=utf8_encode($mT007['D024_Nome_Fantasia']); 
            }else{ 
                $nomeFantasia='';
            }     
            
            $paddingbottomCanhoto =  '6px';
            $fontSizeCanhoto = '';
            if($g['C031']['exibirValorNFDANFE'] == 'S'){
                $paddingbottomCanhoto = '1px';
                $fontSizeCanhoto = 'font-size: 14px;';
            }
           
            $assinatura = <<<EOT
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
                                <td colspan="2" style="text-align: center; padding-bottom: {$paddingbottomCanhoto};">NF-E</td>
                            </tr>
                            <tr style="font-weight: bolder; margin: 3px 0 0 6px; {$fontSizeCanhoto}">
                                <td>No.</td>
                                <td style="background-color: e6e6e6; width: 100%; text-align: center;">{$this->mask_nnf($ide->getElementsByTagName('nNF')->item(0)->nodeValue)}</td>
                            </tr>
                            <tr style="font-weight: bolder; margin: 3px 0 0 6px; {$fontSizeCanhoto}">
                                <td>SÉRIE</td>
                                <td style="width: 100%; text-align: center;">{$ide->getElementsByTagName('serie')->item(0)->nodeValue}</td>
                            </tr>
EOT;
                if($g['C031']['exibirValorNFDANFE'] == 'S'){
                        $assinatura .= <<<EOT
                            <tr style="font-weight: bolder; margin: 3px 0 0 6px; {$fontSizeCanhoto}">
                                <td>VALOR</td>
                                <td style="width: 100%; text-align: center;">{$vNF}</td>
                            </tr>
EOT;
                }

                $widthDataRecebimento = ($g['C031']['adicionarPreenchimentoCPFRGCanhoto'] == 'S') ? '140' : '193';
                $widthIdentificacao   = ($g['C031']['adicionarPreenchimentoCPFRGCanhoto'] == 'S') ? '550' : '610' ;

                $assinatura .= <<<EOT
                        </table>
                    </div>
                    <div style="width: 900px; height: 38px; float: left;">
                        <div style="width: {$widthDataRecebimento}px; height: 38px; float: left; padding: 1px 0 0 3px;">
                            DATA DE RECEBIMENTO
                        </div>
                        <div style="width: {$widthIdentificacao}px; height: 38px; float: left; border-left: 1px solid black; padding: 1px 0 0 3px;">
                            IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR: {$nomeFantasia}
                        </div>
EOT;
                if($g['C031']['adicionarPreenchimentoCPFRGCanhoto'] == 'S'){
                    $assinatura .= <<<EOT
                        <div style="width: 140px; height: 38px; float: left; border-left: 1px solid black; padding: 1px 0 0 3px;">
                            CPF/RG RECEBEDOR:
                        </div>
EOT;
                }

                $assinatura .= <<<EOT
                    </div>
                </div>
                <!-- Fim Cabeçalho 1 -->
EOT;
            $pontilhado = '<div style="width: 100%; height: 0; border-top: 2px dashed black;"></div>';
            
            // Monta o DANFE
            if($g['C031']['imprimirCanhotoDanfe'] == '1' && $pagina == 1){
                $html .= $assinatura;
                $html .= $pontilhado;
            }

            $html .= <<<EOT
                <!-- Empresa -->
                <div style="margin: 5px 5px 0 5px; width: 1090px; height: 180px;">
                    <!-- Empresa 1 -->
                    <div style="float: left; width: 520px; height: 175px; margin-top: 5px;">
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
                                <td style="width: 230px;">
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
                    <div style="float: left; width: 437px; height: 180px; border: 1px solid black; border-bottom: 0;">
                        <div style="width: 437px; height: 85px; border-bottom: 1px solid black; text-align: center;">
                            <img src="{$caminhoImg}" style="width: 433px; height: 80px;" />
                        </div>
                        <div style="width: 437px; height: 48px; border-bottom: 1px solid black;">
                            <table style="width: 437px;">
                                <tr>
                                    <td style="font-size: 11px;">CHAVE DE ACESSO</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; font-weight: bolder; text-align: center;">{$chave_acesso1}</td>
                                </tr>
                            </table>
                        </div>
                        <div style="width: 437px; height: 25px; text-align: center;">
                            {$empresaP23}
                        </div>
                    </div>
                </div>
                <!-- Fim Empresa -->
                <!-- Extra -->
                
                <div style="margin: 0 5px 5px 5px; width: 1090px; height: 74px; border: 1px solid black; border-bottom: 0;">
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
                </span>
                <!-- Fim Cliente -->
EOT;
                if (!empty($entrega) && $pagina == 1){            
                    $html .= <<<EOT
                        <!-- Endereço entrega -->
                        <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">ENDEREÇO DE ENTREGA</span>
                            <div style="margin: 0 5px 5px 5px; width: 1088px; height: 111px; border: 1px solid black; border-bottom: 0;">
                                <div style="float: left; width: 704px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                    NOME/RAZÃO SOCIAL<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaNome}</span>
                                </div>
                                <div style="float: left; width: 186px; height: 30px; border-right: 1px solid black; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
                                    C.N.P.J./C.P.F.<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaCnpj}</span>
                                </div>
                                <div style="float: left; width: 177px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
                                    INSCRIÇÃO ESTADUAL<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaIE}</span>
                                </div>
                                
                                <!-- linha -->
                                <div style="float: left; width: 704px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                    ENDEREÇO<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaEndereco}</span>
                                </div>
                                <div style="float: left; width: 270px; height: 30px; border-right: 1px solid black; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
                                    BAIRRO/DISTRITO<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaBairro}</span>
                                </div>
                                <div style="float: left; width: 94px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
                                    CEP<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaCEP}</span>
                                </div>
                                
                                <!-- linha -->
                                <div style="float: left; width: 934px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                    MUNICÍPIO<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaMunicipio}</span>
                                </div>
                                <div style="float: left; width: 40px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                    UF<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaUF}</span>
                                </div>
                                <div style="float: left; width: 93px; height: 30px; border-bottom: 1px solid black;  padding: 1px 3px 5px 3px;">
                                    FONE/FAX<br />
                                    <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$entregaFone}</span>
                                </div>
                            </div>
                        </span>
                        <!-- Fim Endereço entrega -->
EOT;
                }                
        
            if ($pagina == 1) {
                $html .= <<<EOT
                    <!-- Faturar -->
                    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">FATURAR/DUPLICATAS</span>
                        <div style="margin: 0 5px 5px 5px; width: 1079px; height: 30px; border: 1px solid black; padding: 5px; font-size: 11px;">
                            {$dups}
                        </div>
                    <!-- Fim Faturar -->

                    <!-- Imposto -->
                    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">CÁLCULO DO IMPOSTO</span>
                        <div style="margin: 0 5px 5px 5px; width: 1088px; height: 74px; border: 1px solid black; border-bottom: 0;">
EOT;
                $D006 = mysql_query("SELECT D006_Flag_ICMS_Deson_DANFE FROM T007 LEFT JOIN D006 ON T007_D006_Id = D006_Id WHERE T007_Id = '".$this->T007_Id."' LIMIT 1");
                $mD006 = (mysql_num_rows($D006)>0) ? mysql_fetch_assoc($D006) : array();
                if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S' && $vTotTrib > 0) {
                    if ($mD006['D006_Flag_ICMS_Deson_DANFE'] == 'S') {
                        $html .= <<<EOT
                            <div style="float: left; width: 168px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                BASE DE CÁLCULO DO ICMS<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBC}</span>
                            </div>
                            <div style="float: left; width: 102px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                VALOR DO ICMS<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vICMS}</span>
                            </div>
                            <div style="float: left; width: 184px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                BASE DE CÁLCULO DO ICMS ST<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vBCST}</span>
                            </div>
                            <div style="float: left; width: 119px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                VALOR DO ICMS ST<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vST}</span>
                            </div>
                            <div style="float: left; width: 127px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                VALOR ICMS DESON.<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vICMSDeson}</span>
                            </div>
                            <div style="float: left; width: 156px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                VALOR APROX. TRIBUTOS<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vTotTrib}</span>
                            </div>
                            <div style="float: left; width: 183px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
                                VALOR TOTAL DOS PRODUTOS<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vProd}</span>
                            </div>
EOT;
                    } else {
                        $html .= <<<EOT
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
                    }
                } else {
                    if ($mD006['D006_Flag_ICMS_Deson_DANFE'] == 'S') {
                        $html .= <<<EOT
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
                                VALOR ICMS DESON.<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vICMSDeson}</span>
                            </div>
                            <div style="float: left; width: 177px; height: 30px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
                                VALOR TOTAL DOS PRODUTOS<br/>
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$vProd}</span>
                            </div>
EOT;
                    } else {
                        $html .= <<<EOT
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
                }

                $html .= <<<EOT
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
                        <div style="float: left; width: 452px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                            RAZÃO SOCIAL<br />
                            <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$transp_xNome}</span>
                        </div>
                        <div style="float: left; width: 176px; height: 30px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                            FRETE POR CONTA DE<br />
                            <span style="line-height: 9px; font-size: 10px; padding: 0;">
                                <!--<div style="padding: 3 1px 5px 2px; width: 6px; height: 5px; float: left; border: 1px solid black; font-size: 10px; font-weight: bolder; margin-top: 2px;">
                                    {$Modalidade_Frete}
                                </div>-->
                                <span style="line-height: 20px; font-size: 11px; font-weight: bolder; padding: 0px;">&nbsp{$Descricao_Frete}</span>
                                <!--0-Emitente 2-Terceiros<br />
                                1-dest/Rem 9-Sem Frete -->
                            </span> 
                            <!---->
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

            if(!empty($entrega)){
                $produtosHeight = ($pagina == 1) ? 445 : 1073;
            }else{
                $produtosHeight = ($pagina == 1) ? 560 : 1073;
            }
            $valorAproxTrib = '';
            
            if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S' && $vTotTrib > 0) {
                $valorAproxTrib = '<td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">V.APX<br />TRIB</td>';
            }

            $html .= <<<EOT
                <!-- Produtos -->
                <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">DADOS DOS PRODUTOS/SERVIÇOS</span>
                    <div style="margin: 0 5px 5px 5px; width: 1088px; height: {$produtosHeight}px; border: 1px solid black;"> <!-- 625px -->
                        <table style="font-size: 11px; width: 100%;" cellspacing="0">
                            <tr style="text-align: center;">
EOT;
                            if($imprimeCodigo == 'S'){
                                $html .= <<<EOT
                                 <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">CÓDIGO</td>
EOT;
                            }

                            if($imprimeDescricao == 'S'){
                                $html .= <<<EOT
                                    <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">DESCRIÇÃO</td>
EOT;
                            }

                            if($imprimeNCM == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">NCM/SH</td>
EOT;
                            }

                            if($imprimeCEST == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">CEST</td>
EOT;
                            }

                            if($imprimeCST == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">{$Titulo_CST}</td>
EOT;
                            }

                            if($imprimeCFOP == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">CFOP</td>
EOT;
                            }

                            if($imprimeUnidade == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">UN</td>
EOT;
                            }

                            if($imprimeQuantidade == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">QTE</td>
EOT;
                            }

                            if($imprimeValorUnitario == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">VALOR<BR />UNIT</td>
EOT;
                            }

                            if($imprimeIPI == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">VALOR<BR>IPI</td>
EOT;
                            }

                            if($imprimeST == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">V.ST.<BR>UNIT</td>
EOT;
                            }

                            if($imprimeValorUnitarioIpiST == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">V.UNIT<br> +IPI+ST</td>
EOT;
                            }

                            if($imprimeValorTotal == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">VALOR<BR />TOTAL</td>
EOT;
                            }

                            $html .= <<<EOT
                                {$valorAproxTrib}  
EOT;

                            if($imprimeBaseICMS  == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">BASE<BR />ICMS</td>
EOT;
                            }

                            if($imprimeValorICMS == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">VALOR<BR />ICMS</td>
EOT;
                            }

                            if($imprimePercentualICMS == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black; border-right: 1px solid black;font-size:10px;">%<BR />ICMS</td>
EOT;
                            }

                            if($imprimePercentualIPI == 'S'){
                                $html .= <<<EOT
                                <td style="border-bottom: 1px solid black;">%<BR />IPI</td>

EOT;
                            }                       
                $html .= <<<EOT
                            </tr>
                            {$tabelaProdutos}
                        </table>
                    </div>
                <!-- Fim Produtos -->

EOT;

            // Rodapé
            // Informações Adicionais
            $infCpl = $infAdFisco = '';
            if (is_object($infAdic)){
                $infCpl = $infAdic->getElementsByTagName("infCpl")->item(0)->nodeValue;
                $infCpl = str_replace(';', '<br/>', $infCpl);
                $infCpl = str_replace('|', '<br/>', $infCpl);
                $infAdFisco = $infAdic->getElementsByTagName("infAdFisco")->item(0)->nodeValue;
                $infAdFisco = str_replace('&quot;', '"', $infAdFisco);
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
                
                $html .= <<<EOT
                    <!-- Rodapé -->
                    <span style="margin-left: 10px; font-weight: bolder; font-size: 10px;">DADOS ADICIONAIS</span>
                        <div style="margin: 0 5px 1px; width: 1088px; height: 167px; border: 1px solid black; border-bottom: 0;">
                            <div style="float: left; width: 642px; height: 160px; border-bottom: 1px solid black; border-right: 1px solid black; padding: 1px 3px 5px 3px;">
                                INFORMAÇÕES COMPLEMENTARES<br />
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;">{$infCpl}{$infAdFisco}{$extra}</span>
                            </div>
                            <div style="float: left; width: 433px; height: 160px; border-bottom: 1px solid black; padding: 1px 3px 5px 3px;">
                                RESERVADO AO FISCO<br />
                                <span style="line-height: 17px; font-size: 13px; font-weight: bolder; padding: 5px;"></span>
                            </div>
                        </div>
                    <!-- Fim Rodapé -->
EOT;
            }
           

            //$footer = 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';
            $footer = (!empty($sys['nomeEmpresa']) && !empty($sys['siteEmpresa'])) ? strToUpper($sys['nomeEmpresa']) .' - '. strToUpper($sys['siteEmpresa']) : 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';

            if ($Banco_Dados == 'e276') {
                $footer = '&nbsp;';
            }
        
            $html .= <<<EOT
                <span style="margin-left: 5px; font-size: 13px;">{$footer}</span>
                </body>
                </html>
EOT;
            
            if($g['C031']['imprimirCanhotoDanfe'] == '2' && $pagina == 1){
                $html .= '<br /><br /><br />';
                $html .= $pontilhado;
                $html .= '<br />';
                $html .= $assinatura;
            }

            // Vai setando sempre a ultima pagina e vai ser usado mais a baixo para conferir se a quantidade bate
            $totalPaginasReal = $pagina;       
        }  

        // Valida se a quantidade de paginas definida no inico bateu com a quantidade final
        // Caso nao vai corringido pagina a pagina
        if($nTotal_Paginas != $totalPaginasReal){
            for($i = 1; $i <= $totalPaginasReal; $i++){
                $html = str_replace("<td>{$i}/{$nTotal_Paginas}</td>", "<td>{$i}/{$totalPaginasReal}</td>", $html);
            }
        }    
        echo $html;  
    }
}

?>
