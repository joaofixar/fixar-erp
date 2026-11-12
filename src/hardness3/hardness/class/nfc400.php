<?php
namespace hardness;
    
    /*
    DANFE EM PDF
    Classe PDF_Code128 obtida em: http://www.fpdf.org/en/script/script88.php
    */
    //error_reporting(0);
    include_once('../fpdf/fpdf.php');
    include_once('../fpdf/code128.php'); 
    include_once('../phpqrcode/qrlib.php');
    include('php-barcode.php');
    

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
    var $array_uf;  // usado para obter c�digo do estado do destinatario qdo em contingencia
    var $protocolo; // protocolo de autorizacao deve sair em infCpl
    var $data_hora; // data e hora de autorizacao devem sair em infCpl

    function strtohex($string){
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }


    function geraQRCode2(){
        global $confUsuario;
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

        $cClausula  = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " left join C020 on C020_C004_Id=T007_C004_Id ";
        $cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " where T007_Id='".$this->T007_Id."' ";
        $cSql_T007 = mysqli_query($cClausula);
        $mT007 = mysql_fetch_array($cSql_T007);

        if($this->visualizacao=='S'){
            $digValHex = '2UKW/C+aObsAPOjMZIF8Awx+sHQ=';
            $protocolo = '111110029199999';
            $chaveAcesso = '99999999996336009999999990000000401888888888';
        }else{
            $digValHex = $this->strtohex($protNFe->getElementsByTagName("digVal")->item(0)->nodeValue);
            $protocolo = $protNFe->getElementsByTagName("nProt")->item(0)->nodeValue;
            $chaveAcesso = $protNFe->getElementsByTagName("chNFe")->item(0)->nodeValue;
        }
        // GERANDO QR CODE-
        // $dhHex = $this->strtohex($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue);
        $tpEmis = $ide->getElementsByTagName('tpEmis')->item(0)->nodeValue;
        $vNF    = $ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue;
        $dhEmi = $ide->getElementsByTagName("dhEmi")->item(0)->nodeValue;
        $diaEmissao = (new \DateTimeImmutable($dhEmi))->format('d');

        if(empty($dest)) {
            $clienteCnpj = '';
        } else {
            $clienteCnpj = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue : $dest->getElementsByTagName("CPF")->item(0)->nodeValue;            
        }
        $emitCNPJBase = substr($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue, 0,8);

        $mT007['C004_Token_NFC'] = empty($mT007['C004_Token_NFC']) ? '000001' : $mT007['C004_Token_NFC'];

        /*$parameters = array(
            'URL'           =>"http://www.dfeportal.fazenda.pr.gov.br/dfe-portal/rest/servico/consultaNFCe",
            'chNFe'         =>$chaveAcesso,
            'nVersao'       =>100, // NOS EXEMPLOS ESTAVA FIXADO A VERS�O 100
            'tpAmb'         =>$ide->getElementsByTagName("tpAmb")->item(0)->nodeValue,
            'cDest'         =>$clienteCnpj,
            'dhEmi'         =>$dhHex,
            'vNF'           =>$ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue,
            'vICMS'         =>$ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue,
            'digVal'        =>$digValHex,
            'cIdToken'      =>$mT007['C004_Token_NFC'],
            'nProt'         =>$protocolo,
            );

        $strHash = "chNFe={$parameters['chNFe']}&nVersao={$parameters['nVersao']}&tpAmb={$parameters['tpAmb']}&cDest={$parameters['cDest']}&dhEmi={$dhEmi}&vNF={$parameters['vNF']}&vICMS={$parameters['vICMS']}&digVal={$parameters['digVal']}&cIdToken={$parameters['cIdToken']}";
        $hashSHA1 = strtolower(sha1($strHash));
        $URL = <<<EOT
        {$parameters['URL']}?{$strHash}&cHashQRCode={$hashSHA1}
EOT;*/
        
        //******** Raquel 12/09/2018 ****************/
        // Altera��es de acordo com manual NFC-e Manual de Padr�es, Padr�es t�cnicos DANFE NFC-E E QR CODE VERS�O 2, Vers�o 5.0 ? fevereiro de 2018
        // Remo��o dos 0 significativos do cIDToken
        $mT007['C004_Identificacao_CSC'] = intval($mT007['C004_Identificacao_CSC']);


        $linkQRCode      = "http://www.fazenda.pr.gov.br/nfce/qrcode?";
        if($mT007['T007_Empresa_Usuaria_Uf'] == 'SC'){
            $linkQRCode = "https://sat.sef.sc.gov.br/nfce/consulta?";
        }
        
        // Cria��o dos par�metros para emiss�o online
        $parameters = array(
            'URL'           =>$linkQRCode,
            'chNFe'         =>$chaveAcesso,
            'nVersao'       =>2, // NOS EXEMPLOS ESTAVA FIXADO A VERS�O 100
            'tpAmb'         =>$ide->getElementsByTagName("tpAmb")->item(0)->nodeValue,
            'dhEmi'         =>$diaEmissao,
            'vNF'           =>number_format((float)$vNF, 2, '.', ''),
            //'vICMS'         =>$ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue,
            'digVal'        =>$digValHex,
            'cIdToken'      =>$mT007['C004_Identificacao_CSC'],
            'Token'         =>$mT007['C004_Codigo_CSC'],
            'nProt'         =>$protocolo
        );

        // Retirado o nome das tags e concatenado com '|'
        if($tpEmis != 9) {
            $strHashURL = "{$parameters['chNFe']}|{$parameters['nVersao']}|{$parameters['tpAmb']}|{$parameters['cIdToken']}";
            $strHash = "{$parameters['chNFe']}|{$parameters['nVersao']}|{$parameters['tpAmb']}|{$parameters['cIdToken']}{$parameters['Token']}";
        } else {
            //contigência offline
            $strHashURL = "{$parameters['chNFe']}|{$parameters['nVersao']}|{$parameters['tpAmb']}|{$parameters['dhEmi']}|{$parameters['vNF']}|{$parameters['digVal']}|{$parameters['cIdToken']}";
            $strHash = "{$strHashURL}{$parameters['Token']}";
        }

        $hashSHA1 = strtoupper(sha1($strHash));
        $URL = <<<EOT
        {$parameters['URL']}p={$strHashURL}|{$hashSHA1}
EOT;
        $URL = str_replace(' ', '', $URL);
        $URL = trim($URL);
        //die($URL);

        if(!file_exists("../../../hardness/dados_usuarios/{$confUsuario['dbDatabase']}/nfce/qrcode/")){
            $pathName = "../../../hardness/dados_usuarios/{$confUsuario['dbDatabase']}/nfce/qrcode/";
            $retorno = mkdir($pathName, 0777, true);
            
        }

        \QRcode::png($URL, "../../../hardness/dados_usuarios/{$confUsuario['dbDatabase']}/nfce/qrcode/{$parameters['nProt']}.jpg");
    }

    //http://www.dfeportal.fazenda.pr.gov.br/dfe-portal/rest/servico/consultaNFCe?chNFe=NFe41150614213283000164650010000106491603470032&nVersao=100&tpAmb=2&cDest=12256518400&dhEmi=323031352d30362d32325431303a35313a30302d30333a3030&vNF=58.54&vICMS=0.00&digVal=5a31475064365a7343466a50324d4a494a682b587a384d447537553d&cIdToken=000001&cHashQRCode=7078E16623632712610E3AB1D31D93DD507DBB0D



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
           //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS �? DEFINIDO PARA 1,
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

    function geraPDF(){
        $pdf = new \FPDF("P", "mm", "A4");
        //ini_set('display_errors', 1);
        //error_reporting(E_ALL);
        global $confUsuario;

        if (empty($this->xml))
        {
            return false;
        }
        //header("location: ../class/impressao_nfc.php");
        $cClausula  = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " left join C020 on C020_C004_Id=T007_C004_Id ";
        $cClausula .= " left join D027 on D027_Id=T007_D027_Id ";
        $cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " where T007_Id='".$this->T007_Id."' ";
        $cSql_T007 = mysqli_query($cClausula);
        $mT007 = mysql_fetch_array($cSql_T007);
        $Empresa_Atual = $mT007['C004_Id'];

        $C004 = mysqli_query("SELECT * FROM C004 WHERE C004_Id='{$Empresa_Atual}'");
        $mC004 = mysql_fetch_array($C004);

        //Variaveis de configuração global
        $sqlC031 = mysqli_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$mT007['C004_Id']}';");
        while($rowC031 = mysql_fetch_assoc($sqlC031)){
            $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }

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
        $pag        = $dom->getElementsByTagName("pag")->item(0);
        $infNFeSupl = $dom->getElementsByTagName("infNFeSupl")->item(0);

        // Linhas / Páginas
        $maxChars = 62;
        $this->linhas_primeira_pagina = 32;
        $this->linhas_segunda_pagina = 64;
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
        if (!empty($this->qrcode) && is_file('/tmp/'.$this->qrcode))
        {
            $Img_nfe = imagecreatefromjpeg('/tmp/'.$this->qrcode);
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
        $endereco_emitente .= " - " . utf8_decode($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $endereco_emitente .= " - " . utf8_decode($enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue).'/'.utf8_decode($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue);
        $endereco_emitente .= " - " . "CEP: ".utf8_decode($emit->getElementsByTagName("CEP")->item(0)->nodeValue)." - FONE: ".utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue);
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
            // // contingencia
            // $empresaP23 .= "EMITIDA EM CONTIGÊNCIA";
                        
            // // $dados_nfe = !empty($enderDest) ? $this->array_uf[$enderDest->getElementsByTagName("UF")->item(0)->nodeValue] : '';
            // $dados_nfe.= $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue;
            // $dados_nfe.= $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
            // $dados_nfe.= sprintf("%014s", str_replace(',', '', number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", "")));
            // $dados_nfe.= 1; // ICMSp
            // $dados_nfe.= 2; // ICMSs
            // $data_emissao = gCorrigeData($ide->getElementsByTagName("dEmi")->item(0)->nodeValue);
            // $dados_nfe.= $data_emissao[0].$data_emissao[1];
            // $dados_nfe.= $this->calcula_dv($dados_nfe);

            // $empresaP23 .= "DADOS DA NF-e<br />";
            // $empresaP23 .= $dados_nfe;

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
                $protocolo = $nNumero_Protocolo." ".gCorrigeData($cData_Protocolo)." às ".$cHora_Protocolo;
            }
        }

        // NFC-E
        /*
            tpEmis = 1 normal
            tpEmis = <> 1 contigência

            tpAmb = 1 produção
            tpAmb = 2 homologação
        */
        $tpEmis = $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue;
        $tpAmb = $ide->getElementsByTagName("tpAmb")->item(0)->nodeValue;
        $tipoEmissao='';
        $tipoEmissao2='';

        if($tpEmis==1 && $tpAmb==1){
            $tipoEmissao='EMISSÃO NORMAL';
            $tipoEmissao2='Ambiente de Produção';
        }

        if($tpEmis==1 && $tpAmb==2){
            $tipoEmissao='EMITIDA EM AMBIENTE DE HOMOLOGAÇÃO SEM VALOR FISCAL EMISSÃO NORMAL';
            $tipoEmissao2='Ambiente de Homologação';
        }

        if($tpEmis!=1 && $tpAmb==1){
            $tipoEmissao='EMITIDA EM CONTINGÊNCIA - EMISSÃO NORMAL';
            $tipoEmissao2='Ambiente de Produção';
        }

        if($tpEmis!=1 && $tpAmb==2){
            $tipoEmissao='EMITIDA EM AMBIENTE DE HOLOGAÇÃO SEM VALOR FISCAL EMITIDA EM CONTINGÊNCIA';
            $tipoEmissao2='Ambiente de Homologação';
        }

        // Extra
        $extraNatureza = utf8_decode($ide->getElementsByTagName("natOp")->item(0)->nodeValue);
        $extraIE = utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue);
        $extraIESubst = utf8_decode($emit->getElementsByTagName("IEST")->item(0)->nodeValue);
        $extraCNPJ = $this->mask_cnpj($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue);

        // Cliente
        $clienteDataEmissao = gCorrigeData(substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 0, 10));
        $clienteDataSaida = gCorrigeData(substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 0, 10));
        if(!empty($dest)) {
            $clienteNome = utf8_decode($dest->getElementsByTagName("xNome")->item(0)->nodeValue);
            $clienteCnpj = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($dest->getElementsByTagName("CPF")->item(0)->nodeValue);
            if(!empty($enderDest)){
                $clienteEndereco = utf8_decode($enderDest->getElementsByTagName("xLgr")->item(0)->nodeValue);
                $clienteEndereco .= (!empty($enderDest->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($enderDest->getElementsByTagName("nro")->item(0)->nodeValue) : null;
                $clienteEndereco .= (!empty($enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
                $clienteBairro = utf8_decode($enderDest->getElementsByTagName("xBairro")->item(0)->nodeValue);
                $clienteMunicipio = utf8_decode($enderDest->getElementsByTagName("xMun")->item(0)->nodeValue);
                $clienteFone = utf8_decode($enderDest->getElementsByTagName("fone")->item(0)->nodeValue);
                $clienteUF = utf8_decode($enderDest->getElementsByTagName("UF")->item(0)->nodeValue);
                $clienteCEP = utf8_decode($enderDest->getElementsByTagName("CEP")->item(0)->nodeValue);
            }
            $clienteIE = utf8_decode($dest->getElementsByTagName("IE")->item(0)->nodeValue);
        }
        //$Hora=$ide->getElementsByTagName("hSaiEnt")->item(0)->nodeValue;
        $Hora=substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 11, 8);
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
                  $dups.= gCorrigeData($dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue);
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
        $vIPITotal = $ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue;
        $valorPago = number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue + $pag->getElementsByTagName("vTroco")->item(0)->nodeValue,2,",",".");
        $vNF = number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", ".");
        $vTroco = number_format($pag->getElementsByTagName("vTroco")->item(0)->nodeValue,2,",",".");

        //Forma de pagamento
        if($this->visualizacao=='S'){
            $sql = mysqli_query("SELECT D027_Tipo_Forma_Pagamento_NFCE FROM T118 
                left join D027 on D027_Id=T118_D027_Id
                WHERE T118_T007_Id='".$this->T007_Id."'");
            $mSql = mysql_fetch_array($sql);
            $tPag = $mSql['D027_Tipo_Forma_Pagamento_NFCE'];
        } else {
            $tPag = $pag->getElementsByTagName("tPag")->item(0)->nodeValue;
        }
        switch ($tPag) {
            case '01':
                $formaPagamento = 'Dinheiro';
                break;
            case '02':
                $formaPagamento = 'Cheque';
                break;
            case '03':
                $formaPagamento = 'Cartão de Crédito';
                break;
            case '04':
                $formaPagamento = 'Cartão de Débito';
                break;
            case '05':
                $formaPagamento = 'Crédito Loja';
                break;
            case '10':
                $formaPagamento = 'Vale Alimentação';
                break;
            case '11':
                $formaPagamento = 'Vale Refeição';
                break;
            case '12':
                $formaPagamento = 'Vale Presente';
                break;
            case '13':
                $formaPagamento = 'Vale Combustível';
                break;
            default:
                $formaPagamento = 'Outros';
                break;
        }

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
        /*if ($confUsuario['dbDatabase'] == 'e004') {
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
            <td></td>
            <td></td>
            <td align="right"></td>
            <td align="right"></td>
            <td align="right"></td>
            <td align="right"></td>
        </tr>
EOT;

        foreach ($det as $d)
        {
    
            if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)
            {
                continue;
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

            if (!empty($nCodigo_Barras) && $g['C031']['imprimirNoDanfeEanNoLugarDoCodigo']=='S' && $nCodigo_Barras != 'SEM GTIN')
            {
               $cReferencia=$nCodigo_Barras;
            }

            $cReferencia = utf8_encode($cReferencia);
            $cDescricao = utf8_encode($cDescricao);
            $cInfAdProd = utf8_encode($cInfAdProd);
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

            $tabelaProdutos .= <<<EOT
<tr>
    <td>{$cReferencia}</td>
    <td>{$cDescricao}</td>
    <td align="right">{$quantidade}</td>
    <td align="right">{$unidade}</td>
    <td align="right">{$valor_unitario}</td>
    <td align="right">{$valor_total}</td>
</tr>
EOT;
           
            $nTotal_Itens_Impressos++;
            $nTotal_Itens_Impressos_Tmp++;

        }

        // Completas as linhas até o final
        for ($i = $nTotal_Itens_Impressos_Tmp++; $i <= (($pagina == 1) ? $this->linhas_primeira_pagina : $this->linhas_segunda_pagina); $i++) {
            //$tabelaProdutos .= $vazio;
        }

        // Barcode
        /*global $id;
         $im     = imagecreatetruecolor(900, 130);  
        $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
        $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
        imagefilledrectangle($im, 0, 0, 900, 130, $white);  
        $data = \Barcode::gd($im, $black, 450, 65, 0, "code128", $chave_acesso, 3, 120);  
        imagepng($im, "/tmp/{$id}.barcode.png"); */
        
        
        /*** Raquel  18/09/2018 IMPLEMENTAO NFC-e***/
        $urlChave = trim($infNFeSupl->getElementsByTagName("urlChave")->item(0)->nodeValue); 
        $tagProtocolo=utf8_decode($protNFe->getElementsByTagName("nProt")->item(0)->nodeValue);
        $dataAutorizacao = gCorrigeData(substr($protNFe->getElementsByTagName("dhRecbto")->item(0)->nodeValue, 0, 10));
        $horaAutorizacao =substr($protNFe->getElementsByTagName("dhRecbto")->item(0)->nodeValue, 11, 8);

        // HTML
        echo <<<EOT
<html>
<head>
EOT;
$g['C031']['base64FonteDANFE'] = trim($g['C031']['base64FonteDANFE']);
if(!empty($g['C031']['base64FonteDANFE']) && $g['C031']['base64FonteDANFE'] != ''){
echo <<<EOT
<style>
@font-face {
    font-family: 'Helvetica';    
    src: url(data:font/truetype;charset=utf-8;base64,{$g['C031']['base64FonteDANFE']});
}
*{
    font-size:14px;
}
b{
    margin-top:5px;
    font-size:16px;
}
</style>
EOT;
}
echo <<<EOT
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body style="width: 1100px; border: 0; padding: 0; margin: 5px 0px;font-family: Helvetica;">
    <div style="width:1000px;min-height:400px;margin:0 auto;border:2px solid #000;background:#FDF5E6;">
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
    DEMONSTRAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
</div>
EOT;
        }
        elseif ($mT007[T007_Flag_Cancelada]=='S')
        {
           echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 120px; z-index: 100; top: 300px; left: 245px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial; color: #f42e2c;">
    CANCELADA
</div>
EOT;
        } else if ($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == 2) {
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 75px; z-index: 100; top: 300px; left: 135px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    SEM VALOR FISCAL
</div>
EOT;
        }

        // HTML
        $marginTop = ($pagina == 1) ? 0 : 85;
        echo <<<EOT
    <div style="width: 0; height: {$marginTop}px; border: 0; margin: 0; padding: 0;"></div>
EOT;
        if ($pagina == 1) {
        echo <<<EOT

            <!-- SESSO 1 -->
            <div style="border-bottom:1px dotted #000;min-height:80px;">
                <table style="width:100%;">
                    <tr>
                        <td rowspan="3">
                            <img style="width:85px;height:50px;" src="{$this->logomarca}" />
                        </td>
                        <tr>
                            <td>{$nome_empresa}</td>
                        </tr>
                        <tr>
                            <td>CNPJ: {$extraCNPJ} inscrição Estadual: {$extraIE}</td>
                        </tr>
                    </tr>
                    <tr>
                        <td colspan='2'>{$endereco_emitente}</td>
                    </tr>
                </table>
            </div>

            <!-- SESSÃO 2 -->
            <div style="min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><strong>DANFE NFC-e - Documento Auxiliar da Nota Fiscal Eletrônica para Consumidor Final</strong></td>
                    </tr>                       
                    <tr>
                        <td>NFC-e não permite aproveitamente de crédito de ICMS</td>
                    </tr>
                </table>
            </div>
EOT;

        $produtosHeight = ($pagina == 1) ? 560 : 1073;
        echo <<<EOT
    <!-- SESSÃO 3 PRODUTOS -->
        <div style="border-bottom:1px dotted #000;min-height:50px;text-align:center;">
            <table style="width:100%;">
                <tr>
                    <td style="border:1px dotted #000;">Código</td>
                    <td style="border:1px dotted #000;width:50%;">Descrição</td>
                    <td style="border:1px dotted #000;">Qtde</td>
                    <td style="border:1px dotted #000;">Un</td>
                    <td style="border:1px dotted #000;">Vl Unit</td>
                    <td style="border:1px dotted #000;">Vl Total</td>
                </tr>
                {$tabelaProdutos}
            </table>
        </div>
    <!-- FIM SESSÃO 3 PRODUTOS->

    <!-- SESSÃO 4 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;">
                    <tr>
                        <td>Qtd Total de Itens</td>
                        <td align='right'>{$nTotal_Itens_Impressos}</td>
                    </tr>
                    <tr>
                        <td>Valor Total R$</td>
                        <td align='right'>{$vProd}</td>
                    </tr>
                    <tr>
                        <td>Valor Descontos R$</td>
                        <td align='right'>{$vDesc}</td>
                    </tr>
                    <tr>
                        <td>Valor a Pagar R$</td>
                        <td align='right'>{$vNF}</td>
                    </tr>
                    <tr>
                        <td>FORMA DE PAGAMENTO</td>
                        <td align='right'>VALOR PAGO</td>
                    </tr>
                    <tr>
                        <td>{$formaPagamento}</td>
                        <td align='right'>{$valorPago}</td>
                    </tr> 
EOT;
            if($tPag == '01'){
                    echo <<<EOT
                    <tr>
                        <td>Troco</td>
                        <td align='right'>{$vTroco}</td>
                    </tr>
EOT;
                }
            echo <<<EOT
                </table>
            </div>
EOT;
            if (isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S') {
                echo <<<EOT
                <!-- SESSÃO 5 -->
                <div style="border-bottom:1px dotted #000;min-height:40px;text-align:center;">
                    <table style="width:100%;text-align:center;">
EOT;
                if($g['C031']['imprimirValorIPInaNFCe'] == 'S' && $vIPITotal > 0){
                    $vIPITotal = number_format($vIPITotal, 2, ",", ".");

                        echo <<<EOT
                        <tr>
                            <td><b>Valor IPI:</b></td>
                            <td align='right'>{$vIPITotal}</td>
                        </tr> 
EOT;
                }

                echo <<<EOT
                        <tr>
                            <td><b>Tributos totais incidentes (Lei Federal 12.741/2012):</b></td>
                            <td align='right'>{$vTotTrib}</td>
                        </tr>  
                    </table>
                </div>
EOT;
            }

            echo <<<EOT
            <!-- SESSÃO 5 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><b>CONSULTA PELA CHAVE DE ACESSO EM</b></td>
                    </tr>                       
                    <tr>
                        <td>{$urlChave}</td>
                    </tr>
                    <tr>
                        <td>{$chave_acesso1}</td>
                    </tr>
                </table>
            </div>

            <!-- SESSÃO 6 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><b>CONSUMIDOR</b></td>
                    </tr>
EOT;
            if(empty($clienteCnpj)){
                echo <<<EOT
                    <tr>
                        <td>Consumidor não identificado</td>
                    </tr>
EOT;
            } else {
                echo <<<EOT
                    <tr>
                        <td>CPF/CNPJ: {$clienteCnpj} - {$clienteNome}</td>
                    </tr>
                    <tr>
                        <td>$clienteEndereco, $clienteMunicipio, $clienteUF</td>
                    </tr>
EOT;
            }

            echo <<<EOT
                </table>
            </div>

            <!-- SESSÃO 7 --> 
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><b>{$tipoEmissao}</b></td>
                    </tr>                       
                    <tr>
                        <td>NFC-e: {$nrNf} Série: {$serieNf} Emissão: {$clienteDataEmissao} {$clienteHoraSaira} </td>
                    </tr>
                    <tr>
                        <td>Protocolo de autorização: {$tagProtocolo} </td>
                    </tr>
                    <tr>
                        <td>Data de autorização: {$dataAutorizacao} {$horaAutorizacao}</td>
                    </tr>
                </table>
            </div>

            <!-- SESSÃO 7 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td>Consulta via leitor QR Code</td>
                    </tr>   
                    <tr>
                        <td><img style="width:250px;height:250px;" src="{$this->qrcode}" /></td>
                    </tr>                   
                </table>
            </div>

            <!-- SESSÃO 8 -->
            <div>
                <table style="text-align:center;width:100%;">
                    <tr>
                        <td style="text-align:left;"><strong>{$tipoEmissao2}</strong></td>
                        <td style="text-align:center;"><strong>Versão XML: 4.00</strong></td>
                        <td style="text-align:right;"><strong>Versão XSLT: 1.14</strong></td>
                    </tr>
                </table>
            </div>

EOT;
        }
       
        global $confUsuario;
        $footer = 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';
        if ($confUsuario['dbDatabase'] == 'e276') {
            $footer = '&nbsp;';
        }
            echo <<<EOT
    </div>
    <span style="margin-left: 350px; font-size: 13px;">{$footer}</span>
</body>
</html>
EOT;

}
        
    }

    //GERAR html PARA IMPRESSÃO
    function geraHtml($_imagemLocal = false){
        global $confUsuario, $Banco_Dados;

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
        $cSql_T007 = mysqli_query($cClausula);
        $mT007 = mysql_fetch_array($cSql_T007);
        $Empresa_Atual = $mT007['C004_Id'];

        $C004 = mysqli_query("SELECT * FROM C004 WHERE C004_Id='{$Empresa_Atual}'");
        $mC004 = mysql_fetch_array($C004);
        //Variaveis de configuração global
        $sqlC031 = mysqli_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$mT007['C004_Id']}';");
        while($rowC031 = mysql_fetch_assoc($sqlC031)){
            $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }
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
        $pag        = $dom->getElementsByTagName("pag")->item(0);
        $infNFeSupl = $dom->getElementsByTagName("infNFeSupl")->item(0);

        // Linhas / Páginas
        $maxChars = 62;
        $this->linhas_primeira_pagina = 32;
        $this->linhas_segunda_pagina = 64;
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

        // Rodapé
        // Informações Adicionais
        if (is_object($infAdic)){
            $infAdFisco = $infAdic->getElementsByTagName("infAdFisco")->item(0)->nodeValue;
            if (substr($infAdFisco, -1) === ';') {
                // Se for uma vírgula, removê-la
                $infAdFisco = substr($infAdFisco, 0, -1);
            }
        }

for ($pagina = 1; $pagina <= $nTotal_Paginas; $pagina++) {

        // Cabeçalho 1
        $xNome = utf8_decode($emit->getElementsByTagName("xNome")->item(0)->nodeValue);
        $vlNF = '';
        if($_REQUEST['vlrNF'] == 'S'){
            //$vlNF = "R$ " . number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", ".");
        }

        // Empresa P1
        if (!empty($this->qrcode) && is_file('/tmp/'.$this->qrcode))
        {
            $Img_nfe = imagecreatefromjpeg('/tmp/'.$this->qrcode);
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
        $endereco_emitente .= " - " . utf8_decode($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $endereco_emitente .= " - " . utf8_decode($enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue).'/'.utf8_decode($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue);
        $endereco_emitente .= " - " . "CEP: ".utf8_decode($emit->getElementsByTagName("CEP")->item(0)->nodeValue)." - FONE: ".utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue);
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
            // $empresaP23 .= "EMITIDA EM CONTIGÊNCIA";
            
            // // $dados_nfe = $this->array_uf[$enderDest->getElementsByTagName("UF")->item(0)->nodeValue];
            // $dados_nfe.= $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue;
            // // $dados_nfe.= $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
            // $dados_nfe.= sprintf("%014s", str_replace(',', '', number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", "")));
            // $dados_nfe.= 1; // ICMSp
            // $dados_nfe.= 2; // ICMSs
            // $data_emissao = gCorrigeData($ide->getElementsByTagName("dEmi")->item(0)->nodeValue);
            // $dados_nfe.= $data_emissao[0].$data_emissao[1];
            // $dados_nfe.= $this->calcula_dv($dados_nfe);

            // $empresaP23 .= "DADOS DA NF-e<br />";
            // $empresaP23 .= $dados_nfe;

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
                $protocolo = $nNumero_Protocolo." ".gCorrigeData($cData_Protocolo)." às ".$cHora_Protocolo;
            }
        }

        // NFC-E
        /*
            tpEmis = 1 normal
            tpEmis = <> 1 contigência

            tpAmb = 1 produção
            tpAmb = 2 homologação
        */
        $tpEmis = $ide->getElementsByTagName("tpEmis")->item(0)->nodeValue;
        $tpAmb = $ide->getElementsByTagName("tpAmb")->item(0)->nodeValue;
        $tipoEmissao='';
        $tipoEmissao2='';

        if($tpEmis==1 && $tpAmb==1){
            $tipoEmissao='EMISSÃO NORMAL';
            $tipoEmissao2='Ambiente de Produção';
        }

        if($tpEmis==1 && $tpAmb==2){
            $tipoEmissao='EMITIDA EM AMBIENTE DE HOMOLOGAÇÃO SEM VALOR FISCAL EMISSÃO NORMAL';
            $tipoEmissao2='Ambiente de Homologação';
        }

        if($tpEmis!=1 && $tpAmb==1){
            $tipoEmissao='EMITIDA EM CONTINGÊNCIA - EMISSÃO NORMAL';
            $tipoEmissao2='Ambiente de Produção';
        }

        if($tpEmis!=1 && $tpAmb==2){
            $tipoEmissao='EMITIDA EM AMBIENTE DE HOLOGAÇÃO SEM VALOR FISCAL EMITIDA EM CONTINGÊNCIA';
            $tipoEmissao2='Ambiente de Homologação';
        }

        // Extra
        $extraNatureza = utf8_decode($ide->getElementsByTagName("natOp")->item(0)->nodeValue);
        $extraIE = utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue);
        $extraIESubst = utf8_decode($emit->getElementsByTagName("IEST")->item(0)->nodeValue);
        $extraCNPJ = $this->mask_cnpj($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue);

        // Cliente
        $clienteDataEmissao = gCorrigeData(substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 0, 10));
        $clienteDataSaida = gCorrigeData(substr($ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue, 0, 10));
        if(!empty($dest)){
            $clienteNome = utf8_decode($dest->getElementsByTagName("xNome")->item(0)->nodeValue);
            $clienteCnpj = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($dest->getElementsByTagName("CPF")->item(0)->nodeValue);
            if(!empty($enderDest)){
                $clienteEndereco = utf8_decode($enderDest->getElementsByTagName("xLgr")->item(0)->nodeValue);
                $clienteEndereco .= (!empty($enderDest->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($enderDest->getElementsByTagName("nro")->item(0)->nodeValue) : null;
                $clienteEndereco .= (!empty($enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$enderDest->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
                $clienteBairro = utf8_decode($enderDest->getElementsByTagName("xBairro")->item(0)->nodeValue);
                $clienteMunicipio = utf8_decode($enderDest->getElementsByTagName("xMun")->item(0)->nodeValue);
                $clienteFone = utf8_decode($enderDest->getElementsByTagName("fone")->item(0)->nodeValue);
                $clienteUF = utf8_decode($enderDest->getElementsByTagName("UF")->item(0)->nodeValue);
                $clienteCEP = utf8_decode($enderDest->getElementsByTagName("CEP")->item(0)->nodeValue);
            }
            $clienteIE = utf8_decode($dest->getElementsByTagName("IE")->item(0)->nodeValue);
        }
        //$Hora=$ide->getElementsByTagName("hSaiEnt")->item(0)->nodeValue;
        $Hora=substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 11, 8);
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
                  $dups.= gCorrigeData($dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue);
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
        $vIPITotal = $ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue;
        $valorPago = number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue + $pag->getElementsByTagName("vTroco")->item(0)->nodeValue,2,",",".");
        $vNF = number_format($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue, 2, ",", ".");
        $vTroco = number_format($pag->getElementsByTagName("vTroco")->item(0)->nodeValue,2,",",".");

        //Forma de pagamento
        $tPag = $pag->getElementsByTagName("tPag")->item(0)->nodeValue;
        switch ($tPag) {
            case '01':
                $formaPagamento = 'Dinheiro';
                break;
            case '02':
                $formaPagamento = 'Cheque';
                break;
            case '03':
                $formaPagamento = 'Cartão de Crédito';
                break;
            case '04':
                $formaPagamento = 'Cartão de Débito';
                break;
            case '05':
                $formaPagamento = 'Crédito Loja';
                break;
            case '10':
                $formaPagamento = 'Vale Alimentação';
                break;
            case '11':
                $formaPagamento = 'Vale Refeição';
                break;
            case '12':
                $formaPagamento = 'Vale Presente';
                break;
            case '13':
                $formaPagamento = 'Vale Combustível';
                break;
            case '14':
                $formaPagamento = 'Duplicata Mercantil';
                break;
            case '15':
                $formaPagamento = 'Boleto Bancário';
                break;
            case '90':
                $formaPagamento = 'Sem Pagamento';
                break;
            default:
                $formaPagamento = 'Outros';
                break;
        }

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
        /*if ($confUsuario['dbDatabase'] == 'e004') {
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
            <td></td>
            <td></td>
            <td align="right"></td>
            <td align="right"></td>
            <td align="right"></td>
            <td align="right"></td>
        </tr>
EOT;

        $nTotal_Itens_Impressos_Tmp = 0;
        foreach ($det as $d)
        {

            if ($nTotal_Itens_Impressos>=$nTotal_Itens_Contados)
            {
                continue;
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

            if (!empty($nCodigo_Barras) && $g['C031']['imprimirNoDanfeEanNoLugarDoCodigo']=='S' && $nCodigo_Barras != 'SEM GTIN')
            {
               $cReferencia=$nCodigo_Barras;
            }

            $cReferencia = utf8_encode($cReferencia);
            $cDescricao = utf8_encode($cDescricao);
            $cInfAdProd = utf8_encode($cInfAdProd);
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


            $tabelaProdutos .= <<<EOT
<tr>
    <td>{$cReferencia}</td>
    <td>{$cDescricao}</td>
    <td align="right">{$quantidade}</td>
    <td align="right">{$unidade}</td>
    <td align="right">{$valor_unitario}</td>
    <td align="right">{$valor_total}</td>
</tr>
EOT;
            $nTotal_Itens_Impressos++;
        }

        // Completas as linhas até o final
        for ($i = $nTotal_Itens_Impressos_Tmp++; $i <= (($pagina == 1) ? $this->linhas_primeira_pagina : $this->linhas_segunda_pagina); $i++) {
            //$tabelaProdutos .= $vazio;
        }

        // Barcode
        // global $id;
        // $im     = imagecreatetruecolor(900, 130);
        // $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
        // $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
        // imagefilledrectangle($im, 0, 0, 900, 130, $white);  
        // $data = \Barcode::gd($im, $black, 450, 65, 0, "code128", $chave_acesso, 3, 120);  
        // imagepng($im, "/tmp/{$id}.barcode.png");
        //FIM GERANDO QR CODE

        //GERANDO QR CODE

        if($this->visualizacao=='S'){
            $digValHex = '2UKW/C+aObsAPOjMZIF8Awx+sHQ=';
            $protocolo = '111110029199999';
            $chaveAcesso = '99999999996336009999999990000000401888888888';
        }else{
            $digValHex = $this->strtohex($protNFe->getElementsByTagName("digVal")->item(0)->nodeValue);
            $protocolo = $protNFe->getElementsByTagName("nProt")->item(0)->nodeValue;
            $chaveAcesso = $protNFe->getElementsByTagName("chNFe")->item(0)->nodeValue;
        }

        $dhHex = $this->strtohex($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue);
        if(!empty($dest)){
            $clienteCnpj = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue : $dest->getElementsByTagName("CPF")->item(0)->nodeValue;
        } else {
            $clienteCnpj = '';
        }
        $emitCNPJBase = substr($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue, 0,8);
        //$cIdToken = "000001".$emitCNPJBase.date("Y")."0001";
        $cIdToken = "000001";
 
        $qrCodeParameters = array(
            //'URL'           =>"http://www.dfeportal.fazenda.pr.gov.br/dfe-portal/rest/servico/consultaNFCe",
            //'chNFe'         =>$protNFe->getElementsByTagName("chNFe")->item(0)->nodeValue,
            //'nVersao'       =>100, // NOS EXEMPLOS ESTAVA FIXADO A VERS�O 100
            //'tpAmb'         =>$ide->getElementsByTagName("tpAmb")->item(0)->nodeValue,
            //'cDest'         =>$clienteCnpj,
            //'dhEmi'         =>$dhHex,
            //'vNF'           =>$ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue,
            //'vICMS'         =>$ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue,
            //'digVal'        =>$digValHex,
            //'cIdToken'      =>$mC004['C004_Token_NFC'],
            //'nProt'         =>$protNFe->getElementsByTagName("nProt")->item(0)->nodeValue,
            );
        //$this->geraQRCode($qrCodeParameters);
        
        /*** Raquel 18/09/2018 ***/
        /*** Implementa��o de informa��es NFC-e 4.00 ***/
        $urlChave = trim($infNFeSupl->getElementsByTagName("urlChave")->item(0)->nodeValue);
        $dataAutorizacao = gCorrigeData(substr($protNFe->getElementsByTagName("dhRecbto")->item(0)->nodeValue, 0, 10));
        $horaAutorizacao =substr($protNFe->getElementsByTagName("dhRecbto")->item(0)->nodeValue, 11, 8);

        if ($_imagemLocal) {
            $imagemNFe    = "<img style='width:85px;height:50px;' src='{$this->logomarca}' />";
            $imagemQrCode = "<td><img style='width:250px;height:250px;' src='{$this->qrcode}' /></td>";
        } else {
            $imagemNFe    = "<img style='width:85px;height:50px;' src='../../../../hardness/imagens/png/nfce.png' />";
            $imagemQrCode = "<td><img style='width:250px;height:250px;' src='../../../../hardness/dados_usuarios/{$Banco_Dados}/nfce/qrcode/{$protocolo}.jpg' /></td>";
        }

        // HTML
        echo <<<EOT
<html>
<head>
EOT;
header ('Content-type: text/html; charset=UTF-8');
$g['C031']['base64FonteDANFE'] = trim($g['C031']['base64FonteDANFE']);
if(!empty($g['C031']['base64FonteDANFE']) && $g['C031']['base64FonteDANFE'] != ''){
echo <<<EOT
<style>
@font-face {
    font-family: 'Helvetica';    
    src: url(data:font/truetype;charset=utf-8;base64,{$g['C031']['base64FonteDANFE']});
}
*{
    font-size:14px;
}
b{
    margin-top:5px;
    font-size:16px;
}
</style>
EOT;
}
echo <<<EOT
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body onload="self.print();" style="border: 0; padding: 0; margin: 5px 0px;font-family: Helvetica;">
    <div style="width:400px;min-height:400px;">
EOT;

        // Overlays
        if ($this->visualizacao=='S')
        {
            // transform:rotate(-45deg); -ms-transform:rotate(-45deg); -webkit-transform:rotate(-45deg);
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 35px; z-index: 100; top: 420px; left: 20px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    PRÉ VISUALIZAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
</div>
<div style="text-align: center; position: absolute; font-size: 35px; z-index: 100; top: 800px; left: 20px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    PRÉ VISUALIZAÇÃO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
</div>
EOT;
        }
        /*
        elseif ($mT007[C020_NFe_Treinamento]=='S')
        {
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    DEMNSTRAO<BR /><span style="font-size: 0.8em;">SEM VALOR FISCAL</span>
</div>
EOT;
        }
        elseif ($mT007[T007_Flag_Cancelada]=='S')
        {
           echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 120px; z-index: 100; top: 300px; left: 245px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial; color: #f42e2c;">
    CANCELADA
</div>
EOT;
        } else if ($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == 2) {
            echo <<<EOT
<div style="text-align: center; position: absolute; font-size: 100px; z-index: 100; top: 300px; left: 135px; opacity: 0.4; font-weight: bold; white-space: nowrap; font-family: Arial;">
    SEM VALOR FISCAL
</div>
EOT;
        }
        */
        

        // HTML
        $marginTop = ($pagina == 1) ? 0 : 85;
        echo <<<EOT
    <div style="width: 0; height: {$marginTop}px; border: 0; margin: 0; padding: 0;"></div>
EOT;

        if ($pagina == 1) {
        echo <<<EOT
            <!-- SESSÃO 1 -->
            <div style="border-bottom:1px dotted #000;min-height:80px;">
                <table style="width:100%;">
                    <tr>
                        <td rowspan="3">
                            {$imagemNFe}
                        </td>
                        <tr>
                            <td>{$nome_empresa}</td>
                        </tr>
                        <tr>
                            <td>CNPJ: {$extraCNPJ} inscrição Estadual: {$extraIE}</td>
                        </tr>
                    </tr>
                    <tr>
                        <td colspan='2'>{$endereco_emitente}</td>
                    </tr>
                </table>
            </div>

            <!-- SESSÃO 2 -->
            <div style="min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><strong>DANFE NFC-e - Documento Auxiliar da Nota Fiscal Eletrônica para Consumidor Final</strong></td>
                    </tr>                       
                    <tr>
                        <td>NFC-e não permite aproveitamente de crédito de ICMS</td>
                    </tr>
                </table>
            </div>
EOT;

        $produtosHeight = ($pagina == 1) ? 560 : 1073;
        echo <<<EOT
    <!-- SESSÃO 3 PRODUTOS -->
        <div style="border-bottom:1px dotted #000;min-height:50px;text-align:center;">
            <table style="width:100%;">
                <tr>
                    <td style="border:1px dotted #000;">Código</td>
                    <td style="border:1px dotted #000;width:50%;">Descrição</td>
                    <td style="border:1px dotted #000;">Qtde</td>
                    <td style="border:1px dotted #000;">Un</td>
                    <td style="border:1px dotted #000;">Vl Unit</td>
                    <td style="border:1px dotted #000;">Vl Total</td>
                </tr>
                {$tabelaProdutos}
            </table>
        </div>
    <!-- FIM SESSÃO 3 PRODUTOS->

    <!-- SESSÃO 4 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;">
                    <tr>
                        <td>Qtd Total de Itens</td>
                        <td align='right'>{$nTotal_Itens_Impressos}</td>
                    </tr>
                    <tr>
                        <td>Valor Total R$</td>
                        <td align='right'>{$vProd}</td>
                    </tr>
                    <tr>
                        <td>Valor Descontos R$</td>
                        <td align='right'>{$vDesc}</td>
                    </tr>
                    <tr>
                        <td>Valor a Pagar R$</td>
                        <td align='right'>{$vNF}</td>
                    </tr>
                    <tr>
                        <td>FORMA DE PAGAMENTO</td>
                        <td align='right'>VALOR PAGO</td>
                    </tr>
                    <tr>
                        <td>{$formaPagamento}</td>
                        <td align='right'>{$valorPago}</td>
                    </tr>
EOT;
            if($tPag == '01'){
                echo <<<EOT
                    <tr>
                        <td>Troco</td>
                        <td align='right'>{$vTroco}</td>
                    </tr>
EOT;
            }
            echo <<<EOT
                </table>
            </div>
EOT;
    if ((isset($mT007['T007_Flag_Trib_Transparencia']) && $mT007['T007_Flag_Trib_Transparencia'] == 'S')) {

        echo <<<EOT
            <!-- SESSÃO 5 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">   
EOT;

        if($g['C031']['imprimirValorIPInaNFCe'] == 'S' && $vIPITotal > 0){
            $vIPITotal = number_format($vIPITotal, 2, ",", ".");
            echo <<<EOT
                    <tr>
                        <td><b>Valor IPI:</b></td>
                        <td><b>R\${$vIPITotal}</b></td>
                    </tr> 
EOT;
        }
        echo <<<EOT
                <tr>
                    <td><b>Tributos totais incidentes (Lei Federal 12.741/2012):</b></td>
                    <td><b>R\${$vTotTrib}</b></td>
                </tr> 
           </table>
        </div>
EOT;
    }

echo <<<EOT
            <!-- SESSÃO 5 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><b>CONSULTA PELA CHAVE DE ACESSO EM</b></td>
                    </tr>                       
                    <tr>
                        <td>{$urlChave}</td>
                    </tr>
                    <tr>
                        <td>{$chave_acesso1}</td>
                    </tr>
                </table>
            </div>

            <!-- SESSÃO 6 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><b>CONSUMIDOR</b></td>
                    </tr>
EOT;
            if(empty($clienteCnpj)){
                echo <<<EOT
                    <tr>
                        <td>Consumidor não identificado</td>
                    </tr>
EOT;
            } else {
                echo <<<EOT
                    <tr>
                        <td>CPF/CNPJ: {$clienteCnpj} - {$clienteNome}</td>
                    </tr>
                    <tr>
                        <td>$clienteEndereco, $clienteMunicipio, $clienteUF</td>
                    </tr>
EOT;
            }

            echo <<<EOT
                </table>
            </div>
            <!-- SESSÃO 7 --> 
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><b>{$tipoEmissao}</b></td>
                    </tr>                       
                    <tr>
                        <td>NFC-e: {$nrNf} Série: {$serieNf} Emissão: {$clienteDataEmissao} {$clienteHoraSaira} </td>
                    </tr>
                    <tr>
                        <td>Protocolo de autorização: {$protocolo}</td>
                    </tr>
                    <tr>
                        <td>Data de autorização: {$dataAutorizacao} {$horaAutorizacao}</td>
                    </tr>
                </table>
            </div>

            <!-- SESSÃO 8 -->
            <div style="border-bottom:1px dotted #000;min-height:70px;text-align:center;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td>Consulta via leitor QR Code</td>
                    </tr>   
                    <tr>
                        {$imagemQrCode}
                    </tr>                   
                </table>
            </div>
EOT;
    if($g['C031']['imprimeInformacoesAdicionaisNFCe'] === 'S'){
        echo <<<EOT
            <!-- SESSÃO 9 --> 
            <div style="border-bottom:1px dotted #000;min-height:70px;">
                <table style="width:100%;text-align:center;">
                    <tr>
                        <td><b>INFORMAÇÕES COMPLEMENTARES</b></td>
                    </tr>                       
                    <tr>
                        <td font-size:13px">{$infAdFisco}</td>
                    </tr>
                </table>
            </div>            
EOT;    
    }
    
    echo <<<EOT
            <!-- SESSÃO 10 -->
            <div>
                <table style="text-align:center;width:100%;">
                    <tr>
                        <td style="text-align:left;"><strong>{$tipoEmissao2}</strong></td>
                        <td style="text-align:center;"><strong>Versão XML: 4.00</strong></td>
                        <td style="text-align:right;"><strong>Versão XSLT: 1.14</strong></td>
                    </tr>
                </table>
            </div>

EOT;
        }
       
        global $confUsuario;
        $footer = 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';
        if ($confUsuario['dbDatabase'] == 'e276') {
            $footer = '&nbsp;';
        }
            echo <<<EOT
    </div>
    <span style="font-size: 13px;">{$footer}</span>
</body>
</html>
EOT;

}

    
    }
}

?>
