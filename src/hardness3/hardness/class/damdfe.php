<?php
namespace hardness;
/*
 programador: Lígia Nakazato 21/12/2018
 DAMDFE EM PDF
 Classe PDF_Code128 obtida em: http://www.fpdf.org/en/script/script88.php
*/
//error_reporting(0);
include_once('../fpdf/fpdf.php');
include_once('../fpdf/code128.php');
include_once('../phpqrcode/qrlib.php');

if (!defined('FPDF_FONTPATH'))
{
    define('FPDF_FONTPATH','../fpdf/font/');
}

class damdfe
{
    var $issqn;     // se true, imprime campos do issqn
    var $pdf;       // objeto fpdf()
    var $xml;       // string XML NFe
    var $array_uf;  // usado para obter código do estado do destinatario qdo em contingencia
    var $protocolo; // protocolo de autorizacao deve sair em infCpl
    var $data_hora; // data e hora de autorizacao devem sair em infCpl

    function setNomeArquivoPDF($x){
        $this->nomearquivopdf=$x;
    }

    function setSaidaArquivo($x){
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
    
    function setT276_Id($x){
       $this->T276_Id=$x;
    }

    function setT276_C004_Id($x){
       $this->T276_C004_Id=$x;
    }
    
    function mask_cnpj($c){
        return sprintf("%s.%s.%s/%s-%s", $c[0].$c[1], $c[2].$c[3].$c[4], $c[5].$c[6].$c[7], $c[8].$c[9].$c[10].$c[11], $c[12].$c[13]);
    }


    function mask_cpf($c){
        return sprintf("%s.%s.%s-%s", $c[0].$c[1].$c[2], $c[3].$c[4].$c[5], $c[6].$c[7].$c[8], $c[9].$c[10]);
    }


    function mask_nnf($n){
        $n = sprintf("%09s", $n);
        return sprintf("%s.%s.%s", $n[0].$n[1].$n[2], $n[3].$n[4].$n[5], $n[6].$n[7].$n[8]);
    }

    function calcula_dv($chave35){
        $multiplicadores = array(2,3,4,5,6,7,8,9);
        $i = 34;
        while ($i >= 0){
            for ($m=0; $m<count($multiplicadores) && $i>=0; $m++){
                $soma_ponderada+= $chave35[$i] * $multiplicadores[$m];
                $i--;
            }
        }
        $resto = $soma_ponderada % 11;
        if ($resto == '0' || $resto == '1'){
            $dv = 0;
        }
        else{
            $dv = 11 - $resto;
        }
        return $dv;
    }

    function formata_chave_acesso($input){
        $formatted_input = "";
       while (strlen($input)>0){
          $lenght=substr($input,4,strlen($input));
          $formatted_input .= " ".substr($input,0,4);
          $input=$lenght;
       }
          
       return $formatted_input;
    }


    function contaQuebraDeLinha($obs){
        $qtde = substr_count($obs, '\n');
        if($qtde == 0){
            $qtde = 1;
        }
        return $qtde;
    }

    function gerarQRCode(){
        global $Banco_Dados, $confUsuario,$Diretorio_NFe;

        $dom = new \DomDocument;
        $dom->loadXML($this->xml);
    
        $infMDFeSupl  = $dom->getElementsByTagName("infMDFeSupl");  
        if($infMDFeSupl->length > 0){
            $infMDFeSupl  = $dom->getElementsByTagName("infMDFeSupl")->item(0);
            $qrCodMDFe    = $infMDFeSupl->getElementsByTagName("qrCodMDFe")->item(0)->nodeValue;
            if($this->visualizacao=='S'){
                $nNumero_Protocolo = '999999999999999';
            }else{
                $infProt        = $dom->getElementsByTagName("infProt")->item(0);
                $nNumero_Protocolo=utf8_decode($infProt->getElementsByTagName("nProt")->item(0)->nodeValue);
            }
            
            if(!file_exists($Diretorio_NFe."/qrcode/")){
                $pathName = $Diretorio_NFe."/qrcode/";
                if(!mkdir($pathName, 0777, true)){
                    return ("Não foi possível criar a pasta mdfe/qrcode/. Por favor, abra um chamado informando essa tela.");
                }
            }

            \QRcode::png($qrCodMDFe, $Diretorio_NFe."/qrcode/{$nNumero_Protocolo}.jpg");     
            
        }else{
            return false;
        }
        
    }

    function geraPDF(){
        global $Banco_Dados, $g, $qrcode;

        if (empty($this->xml)){
            return false;
        }
        
        $cClausula  = " SELECT ";
        $cClausula .= " T276_C004_Id, ";
        $cClausula .= " T276_Informacoes_Adicionais_Fisco, ";
        $cClausula .= " T276_Informacoes_Adicionais_Contribuinte, ";
        $cClausula .= " T276_Valor_Carga, ";
        $cClausula .= " T279_T276_Id, ";
        $cClausula .= " T276_Emitente_Nome, ";
        $cClausula .= " T276_Emitente_CNPJ, ";
        $cClausula .= " T276_Emitente_Rua, ";
        $cClausula .= " T276_Emitente_Numero, ";
        $cClausula .= " C004_Complemento, ";
        $cClausula .= " C004_Bairro, ";
        $cClausula .= " T276_Emitente_Cidade, ";
        $cClausula .= " T284_Cidade_Descarregamento, ";
        $cClausula .= " C004_Telefone, ";
        $cClausula .= " C004_Uf, ";
        $cClausula .= " T276_Emitente_CEP, ";
        $cClausula .= " T276_Emitente_Inscricao_Estadual, ";
        $cClausula .= " C004_Telefone, ";
        $cClausula .= " C004_E_Mail, ";
        $cClausula .= " T276_Numero_Protocolo_MDFe, ";
        $cClausula .= " T276_Serie, ";
        $cClausula .= " T276_Numero_MDFe, ";
        $cClausula .= " T276_Data_Emissao, ";
        $cClausula .= " T276_Hora_Emissao, ";
        $cClausula .= " T276_D018_Id_Descarregamento, ";
        $cClausula .= " T276_Quantidade_NFe, ";
        $cClausula .= " T276_Peso_Bruto_Total_Carga, ";
        $cClausula .= " T276_Flag_Cancelada, ";
        $cClausula .= " T276_Ambiente_MDFe ";
        $cClausula .= " FROM T276 ";
        $cClausula .= " LEFT JOIN C004 ON C004_Id=T276_C004_Id ";
        $cClausula .= " LEFT JOIN T283 ON T276_Id=T283_T276_Id ";
        $cClausula .= " LEFT JOIN T284 ON T276_Id=T284_T276_Id ";
        $cClausula .= " LEFT JOIN T278 ON T276_Id=T278_T276_Id ";
        $cClausula .= " LEFT JOIN T279 ON T276_Id=T279_T276_Id ";
        $cClausula .= " WHERE T276_Id='".$this->T276_Id."' ";
        $cClausula .= " AND T276_C004_Id='".$this->T276_C004_Id."'";
        $cClausula .= " GROUP BY T276_Id";
        $cSql_T276 = mysql_query($cClausula);
        $mT276 = mysql_fetch_array($cSql_T276);

        //Variaveis de configuração global
        $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '".$mT276['T276_C004_Id']."'");
        while($rowC031 = mysql_fetch_array($sqlC031)){
            $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }

        $dom = new \DomDocument;
        $dom->loadXML($this->xml);
        $infMDFe        = $dom->getElementsByTagName("infMDFe")->item(0);
        $ide            = $dom->getElementsByTagName("ide")->item(0);
        $infProt        = $dom->getElementsByTagName("infProt")->item(0);
        $emit           = $dom->getElementsByTagName("emit")->item(0);
        $enderEmit      = $dom->getElementsByTagName("enderEmit")->item(0);
        $infMunCarrega  = $dom->getElementsByTagName("infMunCarrega")->item(0);
        $infMunDescarga = $dom->getElementsByTagName("infMunDescarga");
        $protMDFe       = $dom->getElementsByTagName("protMDFe")->item(0);
        $tot            = $dom->getElementsByTagName("tot")->item(0);
        $veicTracao     = $dom->getElementsByTagName("veicTracao")->item(0);
        $veicReboque    = $dom->getElementsByTagName("veicReboque");
        $prop           = $dom->getElementsByTagName("prop")->item(0);
        $condutor       = $dom->getElementsByTagName("condutor");
        $seg            = $dom->getElementsByTagName("seg");
        $infSeg         = $dom->getElementsByTagName("infSeg");
        $infResp        = $dom->getElementsByTagName("infResp")->item(0);
        $infAdic        = $dom->getElementsByTagName("infAdic")->item(0);
        $infNFe         = $dom->getElementsByTagName("infNFe");
        $infCTe         = $dom->getElementsByTagName("infCTe");
        $disp           = $dom->getElementsByTagName("disp")->item(0);
        $tot            = $dom->getElementsByTagName("tot")->item(0);

        // Linhas / Páginas
        $maxChars = 62;
        $this->linhas_primeira_pagina = 15; //qtde de linhas das observações que cabe na primeira página
        $this->linhas_segunda_pagina = 55; //qtde de linhas das observações que cabe na segunda página
        $nTotal_Linhas = 0;
        $nTotal_Notas_Contadas = 0;

        //Valor da altura das linhas das obervações
        $lineHeight = 30; //altura mínima de uma linha da obs nota(página 1)
        $lineHeightContinuacao = 20;//altura mínima de uma linha da obs nota(página 2)
        $lineHeightPrincipal = 60;//altura mínima da div principal da obs nota  (página 1)
        $lineHeightContinuacaoPrincipal = 60;//altura mínima da div principal da obs nota  (página 2)
        $lineHeightGeral = 30;//altura mínima de uma linha da obs fiscal geral
        $lineHeightFiscal = 20; //altura mínima de uma linha da obs fiscal fisco/contribuinte
        $lineHeightFiscalPrincipal = 70; //altura mínima da div principal da obs fisco/contribuinte
        $lineHeightSeguradora = 20;//altura mínima de uma linha da obs seguradora
        $lineHeightSeguradoraPrincipal = 30;//altura mínima da div principal da obs seguradora
        $MostrarDetalhesSeguradora = true; //essa variável controla se a observação será apresentada ou não na primeira página ; se houver quebra de página ela aparece na segunda página
        $MostrarDetalhesContinuacaoNotas = true;
        $maxLinhas = 22; // máximo de linhas de observação que a primeira página pode conter 

        $nInicio=0;
        $nPagina_Atual=0;
        $nLinhasPorPagina = $this->linhas_primeira_pagina; 
        $nTotal_Paginas = 1;
        //Observações

        //Observações Fisco
        if(!empty($mT276['T276_Informacoes_Adicionais_Fisco'])){
            $fisco = utf8_decode($infAdic->getElementsByTagName("infAdFisco")->item(0)->nodeValue);
        }
        if(!empty($mT276['T276_Informacoes_Adicionais_Contribuinte'])){
            $contribuinte = utf8_decode($infAdic->getElementsByTagName("infCpl")->item(0)->nodeValue);
        }

        if(!empty($fisco) && empty($contribuinte)){
            $obsFisco = utf8_encode($fisco);
            $tamanhoLinha = strlen($obsFisco);
            $qtdeQuebraLinha = floor($tamanhoLinha / 80); //calcula quantas linhas terão na observação ; 80 é a qtde de caracteres que cabe dentro da div
            $nTotal_Linhas += $qtdeQuebraLinha;
            $lineHeightFiscal += ($lineHeightFiscal * $qtdeQuebraLinha);
        }else if(!empty($contribuinte) && empty($fisco)){
            $obsContribuinte = utf8_encode($contribuinte);
            $tamanhoLinha = strlen($obsContribuinte);
            $qtdeQuebraLinha .= floor($tamanhoLinha / 80);
            $nTotal_Linhas += $qtdeQuebraLinha;
            $lineHeightFiscal += ($lineHeightFiscal * $qtdeQuebraLinha);
        }else if(!empty($contribuinte) && !empty($fisco)){
            $obsFisco = utf8_encode($fisco);
            $obsContribuinte = utf8_encode($contribuinte);
            //verifica qual a maior observação
            $tamanhoLinha = strlen($obsFisco) > strlen($obsContribuinte) ? strlen($obsFisco) : strlen($obsContribuinte);
            $qtdeQuebraLinha = floor($tamanhoLinha / 80);
            $nTotal_Linhas += $qtdeQuebraLinha;
            $lineHeightFiscal += ($lineHeightFiscal * $qtdeQuebraLinha);
        }else{
            $lineHeightFiscal += $lineHeightFiscal;
        }
        $lineHeightFiscalPrincipal = $lineHeightFiscal + 70;

        //Observações da nota
        $observacaoFinal="";
        $observacaoFinalContinuacao="";
        $valorCarga = number_format(utf8_decode($tot->getElementsByTagName("vCarga")->item(0)->nodeValue),2,',','.');
        $valorTotal = "<b>VALOR TOTAL:</b> R$".$valorCarga."</br>";
        if($g['C031']['habilitarInfoNotasObs'] == 'S' && $MostrarDetalhesSeguradora){
            $nTotal_Linhas_Notas = $nTotal_Linhas; 
            // $sqlNotas = mysql_query("SELECT T284_Id, T284_Flag_Tipo_Documento, T284_Chave_De_Acesso_NFe,T284_Chave_De_Acesso_CTe, T284_Numero_Nfe,T284_Cidade_Descarregamento FROM T284 WHERE T284_T276_Id='".$this->T276_Id."'ORDER BY T284_Cidade_Descarregamento, T284_Numero_Nfe");
            // $seq = 1;
            $nTotal_Linhas += 3;
            $nTotal_Linhas_Notas += 3;
            $lineHeight += 16; //adiciona uma linha para o valor total
            
            for($i = 0; $i < $infNFe->count(); $i++) {
                $chaveAcesso = $infNFe->item($i)->getElementsByTagName("chNFe")->item(0)->nodeValue;
                // echo "Chave Acesso:$chaveAcesso" . PHP_EOL. PHP_EOL;
                // throw new \Exception('Teste');
                
                // Verifica se o indice é impar para pular linha
                if($i % 2 != 0){
                    $pulaLinha = "<br>";
                    $nTotal_Linhas++;
                    $nTotal_Linhas_Notas++;  
                }else{
                    $pulaLinha = "⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀";
                }

                $obsNota = "NFe - " .str_pad(substr($chaveAcesso,25,9), 10, '0', STR_PAD_LEFT) . " - ".$chaveAcesso.$pulaLinha;
                if($i < 40){
                    $lineHeight += 7.5;
                    $observacaoFinal .= $obsNota;
                }else{
                    $observacaoFinalContinuacao .= $obsNota;
                }
                // $seq++;
            }
            
            for($i = 0; $i < $infCTe->count(); $i++) {
                $chaveAcesso = $infCTe->item($i)->getElementsByTagName("chCTe")->item(0)->nodeValue;
                $obsNota = "CTe - " .str_pad(substr($chaveAcesso,25,9), 10, '0', STR_PAD_LEFT) . " - ".$chaveAcesso."<br>";
                if($nTotal_Linhas <= $maxLinhas){
                    $lineHeight += 16;
                    $observacaoFinal .= $obsNota;
                }else{
                    $observacaoFinalContinuacao .= $obsNota;
                }
                // $seq++;
                $nTotal_Linhas++;
                $nTotal_Linhas_Notas++;
            }

            // while($mNotas = mysql_fetch_array($sqlNotas)){
            //     if(!empty($mNotas['T284_Chave_De_Acesso_NFe']) || !empty($mNotas['T284_Chave_De_Acesso_CTe'])){
            //         $chaveAcesso = utf8_decode($infNFe->item($cont)->getElementsByTagName("chNFe")->item(0)->nodeValue ?: $infCTe->item($cont)->getElementsByTagName("chCTe")->item(0)->nodeValue);
            //         $obsNota = str_pad($seq, 2, '0', STR_PAD_LEFT)." - " .str_pad(substr($chaveAcesso,25,9), 10, '0', STR_PAD_LEFT) . " - ".$chaveAcesso."<br>";
            //         if($nTotal_Linhas <= $maxLinhas){
            //             $lineHeight += 16;
            //             $observacaoFinal .= $obsNota;
            //         }else{
            //             $observacaoFinalContinuacao .= $obsNota;
            //         }
            //         $seq++;
            //         $nTotal_Linhas++;
            //         $nTotal_Linhas_Notas++;
            //     }
            //     $cont++;   
            // }    

            $lineHeightPrincipal = $lineHeight + 48; 
        }
        //coloca o valor do total de linhas na variável temporária para verificar se o box de observação de notas aparecerá ou não na segunda página
        $nTotal_Linhas_Fiscal_Mais_Notas = $nTotal_Linhas;

        //Observações da seguradora
        if($g['C031']['habilitarInfoSeguradoraObs'] == 'S' && !empty($mT276['T279_T276_Id'])){
            $nTotal_Linhas++;
            $sqlSeguro = mysql_query("SELECT T279_Id, count(D174_Numero_Averbacao) as qtdeAverbacao, T279_Seguradora_Nome,T279_Seguradora_CNPJ FROM T279 LEFT JOIN D174 ON T279_Id=D174_T279_Id WHERE T279_T276_Id= '".$this->T276_Id."' GROUP BY T279_Id");

            if(!empty($infResp)){
                $responsavelSeguro = $infResp->getElementsByTagName("respSeg")->item(0)->nodeValue;
            }

            //Seguradora
            $cont=0;
            if($responsavelSeguro == 1){
                if(!empty($mT276['T279_T276_Id'])){
                    while($msqlSeguro = mysql_fetch_array($sqlSeguro)){
                        if(!empty($infSeg)){
                            $NomeSeguradora = utf8_decode($infSeg->item($cont)->getElementsByTagName("xSeg")->item(0)->nodeValue);
                            $CNPJseguradora = $this->mask_cnpj(utf8_decode($infSeg->item($cont)->getElementsByTagName("CNPJ")->item(0)->nodeValue));
                        }
                        $InfoSeguradora .= "<b>SEGURADORA:</b> ". $NomeSeguradora ." - ".$CNPJseguradora."<br>";
                        $nTotal_Linhas++;
                        $lineHeightSeguradora += 10;

                        //Apolice
                        if(!empty($seg)){
                            $NomeApolice = utf8_decode($seg->item($cont)->getElementsByTagName("nApol")->item(0)->nodeValue);
                            if(!empty($NomeApolice)){
                                $NomeApolice = utf8_decode($seg->item($cont)->getElementsByTagName("nApol")->item(0)->nodeValue);
                                $InfoSeguradora .= "APÓLICE: ".$NomeApolice."<br>";
                                $nTotal_Linhas++;
                                $lineHeightSeguradora += 10;
                            }
                
                            //Averbacoes
                            $qtdeAverbacoes = $msqlSeguro['qtdeAverbacao'];
                            $averbacao = $seg->item($cont)->getElementsByTagName("nAver")->item(0)->nodeValue; 
                            if(!empty($averbacao)){
                                $InfoSeguradora .= "AVERBAÇÕES: ";
                                $nTotal_Linhas++;
                                //loop averbações
                                for($i = 0 ; $i < $qtdeAverbacoes ; $i++){
                                    $InfoSeguradora .= utf8_decode($seg->item($cont)->getElementsByTagName("nAver")->item($i)->nodeValue)." ; "; 
                                    $lineHeightSeguradora += 5;
                                }
                            }else{
                                $InfoSeguradora .="";
                            } 
                        }
                        $InfoSeguradora .= "<br>";
                        $cont++;   
                    }        
                }else{     
                    $InfoSeguradora = "";
                }
            }else{
                $nomeResponsavelSeguro = "<b>CNPJ RESPONSÁVEL PELO SEGURO:<b> ".(!empty($infResp->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($infResp->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($infResp->getElementsByTagName("CPF")->item(0)->nodeValue) . "<br>";
                $nTotal_Linhas++;
                $lineHeightSeguradora += 10;
            }

            $observacoesSeguradora  = $nomeResponsavelSeguro;
            $observacoesSeguradora .= $InfoSeguradora;  
            $lineHeightSeguradoraPrincipal += $lineHeightSeguradora + 40;    
        }

        //verifica se as observações terão que ser cortadas para a segunda página
        if(round($nTotal_Linhas) > $maxLinhas){
            if($nTotal_Linhas_Fiscal_Mais_Notas < 40){
                $MostrarDetalhesContinuacaoNotas = false;
            }else{
                $MostrarDetalhesContinuacaoNotas = true;
                $resultado = round($nTotal_Linhas_Notas - $maxLinhas);
                $lineHeightContinuacao += (15*$resultado);
                $lineHeightContinuacaoPrincipal = $lineHeightContinuacao + 60;
            }
            $nTotal_Paginas++;
            $MostrarDetalhesSeguradora = false;
            $continua= "<b>CONTINUA NO VERSO</b><br>";
        }  

        if($nTotal_Linhas > 55){
            die("O DAMDFE PODE OCUPAR APENAS 50% DO VERSO");
        }

        for ($pagina = 1; $pagina <= $nTotal_Paginas; $pagina++) {
            // Cabeçalho
            // Logo
            if (!empty($this->logomarca) && is_file('/tmp/'.$this->logomarca)){
                $Img_mdfe = imagecreatefromjpeg('/tmp/'.$this->logomarca);
                $Largura_Imagem=imagesx($Img_mdfe);
                $Altura_Imagem=imagesy($Img_mdfe);

                if ($Altura_Imagem > 130) {
                    $porcentagem = (round((130 * 100) / $Altura_Imagem) - 100) / 100;
                    if ($porcentagem < 0) {
                        $porcentagem = $porcentagem + 1;
                    }
                    $Largura_Imagem = $Largura_Imagem * $porcentagem;
                    $Altura_Imagem = 130;
                }
                if ($Largura_Imagem < 0 || $Largura_Imagem > 260) {
                    $porcentagem = (round((260 * 100) / $Largura_Imagem) - 100) / 100;
                    if ($porcentagem <= 0) {
                        $porcentagem = $porcentagem + 1;
                    }
                    $Largura_Imagem = 260;
                    $Altura_Imagem = $Altura_Imagem * $porcentagem;;
                }
            }

            if(!empty($emit)){
                // Empresa
                $EmpresaNome = utf8_decode($emit->getElementsByTagName("xNome")->item(0)->nodeValue);
                $EmpresaCnpj = (!empty($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($emit->getElementsByTagName("CPF")->item(0)->nodeValue);
                $EmpresaEndereco = utf8_decode($emit->getElementsByTagName("xLgr")->item(0)->nodeValue);
                $EmpresaEndereco .= (!empty($emit->getElementsByTagName("nro")->item(0)->nodeValue)) ? ", ".utf8_decode($emit->getElementsByTagName("nro")->item(0)->nodeValue) : null;
                $EmpresaEndereco .= (!empty($emit->getElementsByTagName("xCpl")->item(0)->nodeValue)) ? " - ".$emit->getElementsByTagName("xCpl")->item(0)->nodeValue : null;
                $EmpresaBairro = utf8_decode($emit->getElementsByTagName("xBairro")->item(0)->nodeValue);
                $EmpresaMunicipio = utf8_decode($emit->getElementsByTagName("xMun")->item(0)->nodeValue);
                $EmpresaFone = utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue);
                $EmpresaUF = utf8_decode($emit->getElementsByTagName("UF")->item(0)->nodeValue);
                $EmpresaCEP = utf8_decode($emit->getElementsByTagName("CEP")->item(0)->nodeValue);
                $EmpresaIE = utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue);
                $EmpresaFone = utf8_decode($emit->getElementsByTagName("fone")->item(0)->nodeValue);
                $EmpresaEmail = (!empty($emit->getElementsByTagName("email")->item(0)->nodeValue)) ? utf8_decode($emit->getElementsByTagName("email")->item(0)->nodeValue): $mT276['C004_Email'];
                $EmpresaCpl = utf8_decode($emit->getElementsByTagName("xCpl")->item(0)->nodeValue);
                $EmpresanNro = utf8_decode($emit->getElementsByTagName("nro")->item(0)->nodeValue);
            }

            if(!empty($infMDFe)){
                // Chave de Acesso
                $chave_acesso = str_replace('MDFe', '', $infMDFe->getAttribute("Id"));
                $chave_acesso1=$this->formata_chave_acesso($chave_acesso);
            }

            // Barcode
            global $id;
            $im     = imagecreatetruecolor(900, 130);  
            $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
            $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
            imagefilledrectangle($im, 0, 0, 900, 130, $white);  
            $data = \Barcode::gd($im, $black, 450, 65, 0, "code128", $chave_acesso, 3, 120);  
            imagepng($im, "/tmp/{$id}.barcode.png");


            //protocolo
            $extra = 0;
            $this->pdf->SetFillColor(0,0,0);
            $this->pdf->Code128(145, 23.8-$extra, $chave_acesso, 75, 12);

            if(!empty($infProt)){
                $nNumero_Protocolo=utf8_decode($infProt->getElementsByTagName("nProt")->item(0)->nodeValue);
                $cData_Protocolo=utf8_decode($infProt->getElementsByTagName("dhRecbto")->item(0)->nodeValue);
                list($cData_Protocolo,$cHora_Protocolo)=explode("T",$cData_Protocolo);
                $protocolo = $nNumero_Protocolo." ".gCorrigeData($cData_Protocolo)." às ".$cHora_Protocolo;   
            }    
     
            //Modelo
            if(!empty($ide)){
                $modelo = $ide->getElementsByTagName("mod")->item(0)->nodeValue;

                //Série
                $serie = $ide->getElementsByTagName("serie")->item(0)->nodeValue;

                //Número MDFe
                $numeroMDFE = $ide->getElementsByTagName("nMDF")->item(0)->nodeValue;

                //Data e Hora de Emissão
                $EmpresaDataEmissao = gCorrigeData(substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 0, 10));
                $Hora=substr($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue, 11, 8);
                if (empty($Hora)){
                    $Hora=date("H:i:s");
                }
                $EmpresaHoraEmissao = $Hora;

                //UF Carregamento        
                $ufCarregamento = $ide->getElementsByTagName("UFIni")->item(0)->nodeValue;

                //UF Descarregamento        
                $ufDescarregamento = $ide->getElementsByTagName("UFFim")->item(0)->nodeValue;
            }

            if(!empty($tot)){
                //Qtde NF-e
                $qtdeNfe = $tot->getElementsByTagName("qNFe")->item(0)->nodeValue ?? 0;

                //Qtde CT-e
                $qtdeCTe = $tot->getElementsByTagName("qCTe")->item(0)->nodeValue ?? 0;

                //Peso Total        
                $pesoTotal = number_format(utf8_decode($tot->getElementsByTagName("qCarga")->item(0)->nodeValue),2,',','.');
            }

            if(!empty($veicTracao)){
                //Veículo Tracao - Placa
                $placaVeiculo = $veicTracao->getElementsByTagName("placa")->item(0)->nodeValue. "<br>";
            }

            if(!empty($veicReboque) && $veicReboque->count() > 0){
                //Veículo Reboque - Placa
                // $sqlVeiculo = mysql_query("SELECT count(T283_Id) as qtdeVeiculos 
                //                              FROM T283 
                //                         LEFT JOIN T276 ON T276_Id= T283_T276_Id
                //                         LEFT JOIN D170 ON D170_Id= T283_D170_Id 
                //                             WHERE T276_Id='".$this->T276_Id."' AND D170_Categoria = 1 AND T276_C004_Id = '".$this->T276_C004_Id ."'");
                // $mSqlVeiculo = mysql_fetch_array($sqlVeiculo);

                    for($i = 0, $count = $veicReboque->count(); $i < $count; $i++){
                        $placaVeiculo .= utf8_decode($veicReboque->item($i)->getElementsByTagName("placa")->item(0)->nodeValue)."<br>";
                    }
            }

            //Foram comentados pq não são usados ainda
            // //Veículo - tara
            // $tara = $veicTracao->getElementsByTagName("tara")->item(0)->nodeValue;
            // //Veículo - capKG
            // $capKG = $veicTracao->getElementsByTagName("capKG")->item(0)->nodeValue;
            // //Veículo - capM3
            // $capM3 = $veicTracao->getElementsByTagName("capM3")->item(0)->nodeValue;
            // //Tipo rodado 
            // $tipoRodado = $veicTracao->getElementsByTagName("tpRod")->item(0)->nodeValue;
            // //Tipo carroceria 
            // $tipoCarroceria = $veicTracao->getElementsByTagName("tpCar")->item(0)->nodeValue;
            // //UF licenciamento
            // $UFLicenciamento = $veicTracao->getElementsByTagName("UF")->item(0)->nodeValue;

            if(!empty($condutor)){
                //Condutores
                $sqlCond = mysql_query("SELECT count(T278_Id) as qtdeCondutores FROM T276 LEFT JOIN T278 ON T276_Id= T278_T276_Id WHERE T276_Id='".$this->T276_Id."'AND T276_C004_Id='".$this->T276_C004_Id ."'GROUP BY T276_Id");
                $mSqlCond = mysql_fetch_array($sqlCond);
                if($mSqlCond['qtdeCondutores'] > 0){
                    $nomeCondutores = "";
                    $cpfCondutores = "";
                    for($i = 0 ; $i < $mSqlCond['qtdeCondutores'] ; $i++){
                        $nomeCondutores .= utf8_decode($condutor->item($i)->getElementsByTagName("xNome")->item(0)->nodeValue)."<br>";
                        $cpfCondutores .= $this->mask_cpf($condutor->item($i)->getElementsByTagName("CPF")->item(0)->nodeValue)."<br>";      
                    }   
                }   
            }      

            $g['C031']['base64FonteDANFE'] = trim($g['C031']['base64FonteDANFE']);
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
                    <div style="width: 0; height: 0; border: 0; margin: 0; padding: 0; position: relatifdieve;">
EOT;
            // Overlays
            if ($this->visualizacao =='S'){
                echo <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; font-weight: bold; white-space: nowrap; font-family: Arial;">
                        <span style="opacity: 0.2; font-size: 1.0em;"><br>PRÉ VISUALIZAÇÃO</span>
                        <span style="opacity: 0.2; font-size: 0.8em;"><br>SEM VALOR FISCAL</span>
                    </div>
EOT;
            } else if ($mT276['T276_Flag_Cancelada']=='S'){
                echo <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 120px; z-index: 100; top: 554px; left: 155px; opacity: 0.2; font-weight: bold; white-space: nowrap; font-family: Arial; color: #f42e2c;">
                        CANCELADA
                    </div>
EOT;
            } else if ($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == 2) { 
                echo <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; font-weight: bold; white-space: nowrap; font-family: Arial;">
                        <span style="opacity: 0.3; font-size: 1.0em;"><br>EM HOMOLOGAÇÃO <br>SEM VALOR FISCAL</span>
                        <span style="font-size: 0.8em;"><br></span>
                    </div>
EOT;
            }
            // HTML
            $marginTop = ($pagina == 1) ? 20 : 85;
            echo <<<EOT
                </div>
                <div style="width: 0; height:{$marginTop}px; border: 0; margin: 0 auto; padding: 0; font-family: Helvetica;"></div>
EOT;
            
            $infMDFeSupl  = $dom->getElementsByTagName("infMDFeSupl");
            if($infMDFeSupl->length > 0 ){
                $tagQRCode = "<img src='qrCode{$id}.jpg' style='width: 175px; height: 175px; margin-bottom:-1px;'/>";
            }else{
                $tagQRCode = "";
            }
            if($pagina == 1){    
                echo <<<EOT
                        <!-- Empresa -->
                        <div style="margin: 0 auto; width: 1150px; height: 210px; border: 1px solid black; border-radius:10px;">
                            <!-- Empresa 1 -->
                            <span>
                                <img src="{$this->logomarca}" style="width: {$Largura_Imagem}px; height:{$Altura_Imagem}px; position:relative; top:30%; left:5%"/>
                            </span>
                            <table style="float: right; width:70%; height: 190px; padding: 20px;">
                                 <tr>
                                    <td colspan="6" style="font-size: 20px; padding-bottom:20px; font-weight: bolder;">{$EmpresaNome}</td>
                                    <td rowspan=8>
                                        {$tagQRCode}
                                    </td> 
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">CNPJ: <b>{$EmpresaCnpj}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">IE: <b>{$EmpresaIE}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">RAZÃO SOCIAL: <b>{$EmpresaNome}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">LOGRADOURO: <b>{$EmpresaEndereco}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">BAIRRO: <b>{$EmpresaBairro}</b> </td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">CEP: <b>{$EmpresaCEP}</b></td>
                                </tr> 
                                <tr style="border: 1px solid pink";>
                                    <td colspan="5" style="font-size: 14px;">MUNICÍPIO/UF: <b>{$EmpresaMunicipio}/{$EmpresaUF} </b></b></td>                                   
                                </tr>
                            </table>
                            
                        </div>
                        <!--  Título Damdfe -->
                        <div style="margin: 0 auto; margin-top:5px; width: 1150px; height: 30px; border: 1px solid black; border-radius:10px; padding:20px 0px 10px 0px; text-align:center;">
                            <span style="font-size: 26px; font-weight: bolder;padding-left:30px;">DAMDFE</span>
                            <span style="font-size: 17px; padding-left:10px;">DOCUMENTO AUXILIAR DE MANIFESTO ELETRÔNICO DE DOCUMENTOS FISCAIS</span>
                        </div>
                        <!--  Fim Título Damdfe -->
                        <!--  Barcode -->
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: 310px; border: 1px solid black; border-radius:10px;">
                            <!-- Barcode -->
                            <div style="width: 1150px; height: 150px; border-bottom: 1px solid black; text-align: center;">
                                <table style="width: 1150px; height: 150px;text-align: center;">
                                    <tr style="width: 1150px;">
                                        <td>
                                            <p style="font-size:14px; margin-bottom:0px; text-align: center;">CONTROLE DO FISCO</p>
                                            <img src="{$id}.barcode.png" style="width: 650px; height: 115px; " />
                                        </td>
                                    </tr>    
                                </table>
                            </div>    
                            <table style="width: 1150px; border-bottom: 1px solid black; ">
                                <tr>
                                    <td style="font-size: 14px; padding: 10px 10px 0px 10px; ">CHAVE DE ACESSO</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 20px; font-weight: bold; text-align: center;padding: 10px 10px 10px 10px;">{$chave_acesso1}</td>
                                </tr>
                            </table>
                            <table style="width: 1150px;">
                                <tr>
                                    <td style="font-size: 14px; padding: 10px 10px 0px 10px; ">PROTOCOLO DE AUTORIZAÇÃO DE USO</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 20px; font-weight: bold; text-align: center;padding: 5px 10px 15px 10px;">{$protocolo}</td>
                                </tr>
                            </table>                   
                        </div>

                        <!-- modelo e série -->
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: 60px; border: 1px solid black; border-radius:10px;">
                            <div style="float: left; width: 85px; height: 41px; border-right: 1px solid black;  padding: 10px; font-size:14px;">MODELO<br />
                                <p style="font-size: 20px; font-weight: bold; text-align:center; margin-top:5px;">{$modelo}</p>
                            </div>
                            <div style="float: left; width: 85px; height: 41px; border-right: 1px solid black;  padding:10px; font-size:14px;">SÉRIE<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$serie}</p>
                            </div>  
                            <div style="float: left; width: 150px; height: 41px; border-right: 1px solid black;  padding:10px; font-size:14px;">NÚMERO<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$numeroMDFE}</p>
                            </div>
                            <div style="float: left; width: 65px; height: 41px; border-right: 1px solid black;  padding:10px; font-size:14px;">FL<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;"></p>
                            </div>
                            <div style="float: left; width: 280px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">DATA E HORA DE EMISSÃO<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$EmpresaDataEmissao} {$EmpresaHoraEmissao}</p>
                            </div> 
                            <div style="float: left; width: 105px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">UF CARREG.<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$ufCarregamento}</p>
                            </div> 
                            <div style="float: left; width: 85px; height: 41px; padding:10px; font-size:14px;">UF DESCAR.<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$ufDescarregamento}</p>
                            </div>   
                        </div>
                        <!-- Fim modelo e série -->

                        <!-- modal rodoviário -->
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: 400px; border: 1px solid black; border-radius:10px;">
                            <!-- linha 1-->
                            <div style="width: 1150px; height: 88px; border-bottom: 1px solid black;">
                                <div style="width: 1150px; height: 25px; border-bottom: 1px solid black; text-align: center;">
                                    <p style="margin-bottom:5px; font-size:17px;">MODAL RODOVIÁRIO DE CARGA</p>
                                </div>    
                                <div style="float: left; width: 105px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">QTDE CT-e<br />
                                    <p style="font-size: 20px; font-weight: bold; text-align:center; margin-top:5px;">{$qtdeCTe}</p>
                                </div>
                                <div style="float: left; width: 110px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">QTDE NF-e<br />
                                    <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$qtdeNfe}</p>
                                </div>  
                                <div style="float: left; width: 190px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">PESO TOTAL(Kg)<br />
                                    <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$pesoTotal}</p>
                                </div>   
                            </div>
                            <!-- fim linha 1-->
                            <!-- linha 2-->
                            <div style="width: 1150px; height: 35px; border-bottom: 1px solid black; text-align: center;">
                                <div style="width: 530px; height: 35px; text-align: center; float:left; border-right:1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">VEÍCULO</p>
                                </div>  
                                <div style="width: 608px; height: 35px; text-align: left;float:right;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left;">CONDUTOR</p>
                                </div>  
                            </div>
                            <!-- fim linha 2-->    
                            <!-- linha 3-->
                            <div style="width: 1150px; height: 30px; border-bottom: 1px solid black; text-align: center;">
                                <div style="width: 264px; height: 30px; float:left; border-right:1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">PLACA</p>
                                </div>  
                                <div style="width: 265px; height: 30px;float:left; border-right: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left;padding-left:10px;">RNTRC</p>
                                </div>  
                                <div style="width: 175px; height: 30px;float:left; border-right: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left;padding-left:10px;">CPF</p>
                                </div>  
                                <div style="width: 306px; height: 30px;float:left;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left;padding-left:10px;">NOME</p>
                                </div>  
                            </div>
                            <!-- fim linha 3-->   
                            <!-- linha 4-->
                            <div style="width: 530px; height: 230px; float:left;">
                                <div style="width: 264px; height: 105px; float:left; border-right:1px solid black; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:15px; text-align:left; padding-left:10px;font-weight:bold;">{$placaVeiculo}</p>
                                </div>  
                                <div style="width: 265px; height: 105px;float:left; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:15px; text-align:left;padding-left:10px;font-weight:bold;">{$RNTRCProprietarioVeiculo}</p>
                                </div>  
                                <div style="width: 530px; height: 30px;float:left; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">VALE PEDÁGIO</p>
                                </div> 
                                <div style="width: 175px; height: 30px; float:left; border-right:1px solid black; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">RESPONSÁVEL CNPJ</p>
                                </div>  
                                <div style="width: 175px; height: 30px;float:left; border-right: 1px solid black; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">FORNECEDORA CNPJ</p>
                                </div> 
                                <div style="width: 178px; height: 30px;float:left;border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">Nº COMPROVANTE</p>
                                </div> 
                                <div style="width: 175px; height: 60px; float:left; border-right:1px solid black; ">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;font-weight:bold;"></p>
                                </div>  
                                <div style="width: 175px; height: 60px;float:left; border-right: 1px solid black; ">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;font-weight:bold;"></p>
                                </div> 
                                <div style="width: 175px; height: 60px;float:left;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;font-weight:bold;"></p>
                                </div> 
                            </div>
                            <div style="width: 616px; height: 228.5px;float:left;border-left: 1px solid black;">
                                <div style="width: 175px; height: 22px;float:left;">
                                    <p style="margin-bottom:5px; font-size:12px; text-align:left;padding-left:10px;font-weight:bold;">{$cpfCondutores}</p>
                                </div>  
                                <div style="width: 438px; height: 226px;float:left; border-left: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:12px; text-align:left;padding-left:10px;font-weight:bold;">{$nomeCondutores}</p>
                                </div>  
                            </div>
                            <!-- fim linha 4-->         
                        </div>
                        <!-- fim modal rodoviário -->
                        <!-- div observacoes gerais/fisco/contribuinte -->
                        <div style="margin: 0 auto;margin-top:5px; width: 1140px; font-size:14px; height:{$lineHeightFiscalPrincipal}; border: 1px solid black;padding:10px 0px 0px 10px; border-radius:10px;">
                            <p style="float:left;width: 1140px; margin-top:0px; margin-bottom:0px;">OBSERVAÇÕES</p>
                            <div style="margin-top:0px;float:left;width: 1140px; height: {$lineHeightGeral}; font-size:12px;">
                                <p><b></b></p> <!-- observações gerais que podem ser adicionadas -->
                            </div> 
EOT;
                if(!empty($mT276['T276_Informacoes_Adicionais_Fisco'])){
                        echo<<<EOT
                            <div style="margin-top:0px;float:left;width: 560px; height: {$lineHeightFiscal}; font-size:12px; padding-right:10px;border-top: 1px solid black;">
                                <p><b>FISCO:</b> {$obsFisco}</p>
                            </div> 
EOT;
                }
                if(!empty($mT276['T276_Informacoes_Adicionais_Contribuinte'])){                    
                        echo<<<EOT
                            <div style="margin-top:0px;float:left;width: 560px; height: {$lineHeightFiscal}; font-size:12px;border-top: 1px solid black;">
                                <p><b>CONTRIBUINTE:</b> {$obsContribuinte}</p>
                            </div>  
EOT;
                }
                echo "</div>"; //fim div observações gerais/fisco/contribuinte
                if($g['C031']['habilitarInfoNotasObs'] == 'S'){
                    $titulo = "TIPO - NÚMERO - CHAVE DE ACESSO<br>";
                    echo<<<EOT
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: {$lineHeightPrincipal}; border: 1px solid black; border-radius:10px;">  
                            <p style="float:left;width: 1140px; font-size:14px; padding:5px 0px 0px 10px; margin-top:5px; margin-bottom:0px;">DOCUMENTOS FISCAIS VINCULADOS AO MDF-E</p>
                            <p style="padding:10px;float:left;margin-top:0px;font-size: 14px;width: 1150px; text-align:left;height: {$lineHeight};">{$titulo}{$valorTotal}{$observacaoFinal}</b></p>

                        </div>
EOT;
                }
                if($g['C031']['habilitarInfoSeguradoraObs'] == 'S' && $MostrarDetalhesSeguradora){
                    echo<<<EOT
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: {$lineHeightSeguradoraPrincipal}; border: 1px solid black; border-radius:10px;">  
                            <p style="float:left;width: 1140px; font-size:14px; padding:0px 0px 0px 10px; margin-top:5px; margin-bottom:0px;">INFORMAÇÕES SEGURADORA</p>
                            <p style="padding:10px;float:left;margin-top:0px;font-size: 14px;width: 1150px; text-align:left;height: {$lineHeightSeguradora};">{$observacoesSeguradora}</p>

                        </div>
EOT;
                }
                $footer = $continua. "HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR";
            }else{
                $MostrarDetalhesSeguradora = true;
                if($g['C031']['habilitarInfoNotasObs'] == 'S' && $MostrarDetalhesContinuacaoNotas){
                    echo<<<EOT
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: {$lineHeightContinuacaoPrincipal}; border: 1px solid black; border-radius:10px;">  
                            <p style="float:left;width: 1140px; font-size:14px; padding:5px 0px 0px 10px; margin-top:5px; margin-bottom:0px;">DOCUMENTOS FISCAIS VINCULADOS AO MDF-E</p>
                            <p style="padding:10px;float:left;margin-top:0px;font-size: 14px;width: 1150px; text-align:left;height: {$lineHeightContinuacao};">TIPO - NÚMERO - CHAVE DE ACESSO<br><b>{$observacaoFinalContinuacao}</b></p>

                        </div>
EOT;
                }
                if($g['C031']['habilitarInfoSeguradoraObs'] == 'S' && $MostrarDetalhesSeguradora){
                    echo<<<EOT
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: {$lineHeightSeguradoraPrincipal}; border: 1px solid black; border-radius:10px;">  
                            <p style="float:left;width: 1140px; font-size:14px; padding:0px 0px 0px 10px; margin-top:5px; margin-bottom:0px;">INFORMAÇÕES SEGURADORA</p>
                            <p style="padding:10px;float:left;margin-top:0px;font-size: 14px;width: 1150px; text-align:left;height: {$lineHeightSeguradora};">{$observacoesSeguradora}</p>

                        </div>
EOT;
                }
                $footer = 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';
            }
                echo <<<EOT
                    <div style="margin-top:10px;">
                        <span style="margin-left: 5px; font-size: 13px;">{$footer}</span>
                    </div>
            </body>
            </html>
EOT;
        }               
    }   

    function geraHtml(){
        global $Banco_Dados, $g;

        if (empty($this->xml)){
            return false;
        }
        
        $cClausula  = " select ";
        $cClausula .= " *, carregamentoD018.D018_Uf as carregamentoD018 ";
        $cClausula .= " from T276 ";
        $cClausula .= " left join C004 on C004_Id=T276_C004_Id ";
        $cClausula .= " left join T283 on T276_Id=T283_T276_Id ";
        $cClausula .= " left join T284 on T276_Id=T284_T276_Id ";
        $cClausula .= " left join T278 on T276_Id=T278_T276_Id ";
        $cClausula .= " left join T279 on T276_Id=T279_T276_Id ";
        $cClausula .= " left join D018 as carregamentoD018 on D018_Id = T276_D018_Id_Carregamento ";
        $cClausula .= " where T276_Id='".$this->T276_Id."' ";
        $cClausula .= " and T276_C004_Id='".$this->T276_C004_Id."'";
        $cClausula .= " group by T276_Id";
        $cSql_T276 = mysql_query($cClausula);
        $mT276 = mysql_fetch_array($cSql_T276);

        //Variaveis de configuração global
        $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '".$mT276['T276_C004_Id']."'");
        while($rowC031 = mysql_fetch_array($sqlC031)){
            $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }

        $dom = new \DomDocument;
        $dom->loadXML($this->xml);
        $infMDFe        = $dom->getElementsByTagName("infMDFe")->item(0);
        $infProt        = $dom->getElementsByTagName("infProt")->item(0);

        // Linhas / Páginas
        $maxChars = 62;
        $this->linhas_primeira_pagina = 15; //qtde de linhas das observações que cabe na primeira página
        $this->linhas_segunda_pagina = 55; //qtde de linhas das observações que cabe na segunda página
        $nTotal_Linhas = 0;
        $nTotal_Notas_Contadas = 0;

        //Valor da altura das linhas das obervações
        $lineHeight = 30; //altura mínima de uma linha da obs nota(página 1)
        $lineHeightContinuacao = 20;//altura mínima de uma linha da obs nota(página 2)
        $lineHeightPrincipal = 60;//altura mínima da div principal da obs nota  (página 1)
        $lineHeightContinuacaoPrincipal = 60;//altura mínima da div principal da obs nota  (página 2)
        $lineHeightGeral = 30;//altura mínima de uma linha da obs fiscal geral
        $lineHeightFiscal = 20; //altura mínima de uma linha da obs fiscal fisco/contribuinte
        $lineHeightFiscalPrincipal = 70; //altura mínima da div principal da obs fisco/contribuinte
        $lineHeightSeguradora = 20;//altura mínima de uma linha da obs seguradora
        $lineHeightSeguradoraPrincipal = 30;//altura mínima da div principal da obs seguradora
        $MostrarDetalhesSeguradora = true; //essa variável controla se a observação será apresentada ou não na primeira página ; se houver quebra de página ela aparece na segunda página
        $MostrarDetalhesContinuacaoNotas = true;
        $maxLinhas = 22; // máximo de linhas de observação que a primeira página pode conter 

        $nInicio=0;
        $nPagina_Atual=0;
        $nLinhasPorPagina = $this->linhas_primeira_pagina; 
        $nTotal_Paginas = 1;
        //Observações

        //Observações Fisco
        if(!empty($mT276['T276_Informacoes_Adicionais_Fisco']) && empty($mT276['T276_Informacoes_Adicionais_Contribuinte'])){
            $obsFisco = utf8_encode($mT276['T276_Informacoes_Adicionais_Fisco']);
            $tamanhoLinha = strlen($obsFisco);
            $qtdeQuebraLinha = floor($tamanhoLinha / 80); //calcula quantas linhas terão na observação ; 80 é a qtde de caracteres que cabe dentro da div
            $nTotal_Linhas += $qtdeQuebraLinha;
            $lineHeightFiscal += ($lineHeightFiscal * $qtdeQuebraLinha);
        }else if(!empty($mT276['T276_Informacoes_Adicionais_Contribuinte']) && empty($mT276['T276_Informacoes_Adicionais_Fisco'])){
            $obsContribuinte = utf8_encode($mT276['T276_Informacoes_Adicionais_Contribuinte']);
            $tamanhoLinha = strlen($obsContribuinte);
            $qtdeQuebraLinha .= floor($tamanhoLinha / 80);
            $nTotal_Linhas += $qtdeQuebraLinha;
            $lineHeightFiscal += ($lineHeightFiscal * $qtdeQuebraLinha);
        }else if(!empty($mT276['T276_Informacoes_Adicionais_Contribuinte']) && !empty($mT276['T276_Informacoes_Adicionais_Fisco'])){
            $obsFisco = utf8_encode($mT276['T276_Informacoes_Adicionais_Fisco']);
            $obsContribuinte = utf8_encode($mT276['T276_Informacoes_Adicionais_Contribuinte']);
            //verifica qual a maior observação
            $tamanhoLinha = strlen($obsFisco) > strlen($obsContribuinte) ? strlen($obsFisco) : strlen($obsContribuinte);
            $qtdeQuebraLinha = floor($tamanhoLinha / 80);
            $nTotal_Linhas += $qtdeQuebraLinha;
            $lineHeightFiscal += ($lineHeightFiscal * $qtdeQuebraLinha);
        }else{
            $lineHeightFiscal += $lineHeightFiscal;
        }
        $lineHeightFiscalPrincipal = $lineHeightFiscal + 70;

        //Observações da nota
        $observacaoFinal="";
        $observacaoFinalContinuacao="";
        $valorTotal = "<b>VALOR TOTAL:</b> R$".number_format($mT276['T276_Valor_Carga'],2,',','.')."</br>";
        if($g['C031']['habilitarInfoNotasObs'] == 'S' && $MostrarDetalhesSeguradora){
            $nTotal_Linhas_Notas = $nTotal_Linhas; 
            $sqlNotas = mysql_query("SELECT T284_Id, T284_Chave_De_Acesso_NFe, T284_Numero_Nfe,T284_Cidade_Descarregamento FROM T284 WHERE T284_T276_Id='".$this->T276_Id."'ORDER BY T284_Cidade_Descarregamento, T284_Numero_Nfe");
            $seq = 1;
            $nTotal_Linhas += 3;
            $nTotal_Linhas_Notas += 3;
            $lineHeight += 16; //adiciona uma linha para o valor total

            $cont=0;
            while($mNotas = mysql_fetch_array($sqlNotas)){
                if(!empty($mNotas['T284_Chave_De_Acesso_NFe'])){
                    $obsNota = str_pad($seq, 2, '0', STR_PAD_LEFT)." - " .str_pad($mNotas['T284_Numero_Nfe'], 10, '0', STR_PAD_LEFT) . " - ".$mNotas['T284_Chave_De_Acesso_NFe']."<br>";
                    if($nTotal_Linhas <= $maxLinhas){
                        $lineHeight += 16;
                        $observacaoFinal .= $obsNota;
                    }else{
                        $observacaoFinalContinuacao .= $obsNota;
                    }
                    $seq++;
                    $nTotal_Linhas++;
                    $nTotal_Linhas_Notas++;
                }
                $cont++;   
            }          
            $lineHeightPrincipal = $lineHeight + 48; 
        }
        //coloca o valor do total de linhas na variável temporária para verificar se o box de observação de notas aparecerá ou não na segunda página
        $nTotal_Linhas_Fiscal_Mais_Notas = $nTotal_Linhas;

        //Observações da seguradora
        if($g['C031']['habilitarInfoSeguradoraObs'] == 'S' && !empty($mT276['T279_T276_Id'])){
            $nTotal_Linhas++;
            $sqlSeguro = mysql_query("SELECT T279_Id,T279_Numero_Apolice,D174_Numero_Averbacao, count(D174_Numero_Averbacao) as qtdeAverbacao, T279_Seguradora_Nome,T279_Seguradora_CNPJ FROM T279 LEFT JOIN D174 ON T279_Id=D174_T279_Id WHERE T279_T276_Id= '".$this->T276_Id."' GROUP BY T279_Id");

            $responsavelSeguro = 1; //sempre vai ser 1 pq os nossos clientes não irão contratar transportadora por enquanto;

            //Seguradora
            $cont=0;
            if($responsavelSeguro == 1){
                if(!empty($mT276['T279_T276_Id'])){
                    while($msqlSeguro = mysql_fetch_array($sqlSeguro)){
                        $InfoSeguradora .= "<b>SEGURADORA:</b> ". $msqlSeguro['T279_Seguradora_Nome'] ." - ".$msqlSeguro['T279_Seguradora_CNPJ']."<br>";
                        $nTotal_Linhas++;
                        $lineHeightSeguradora += 10;

                        //Apolice
                        $NomeApolice = $msqlSeguro['T279_Numero_Apolice'];
                        if(!empty($NomeApolice)){
                            $InfoSeguradora .= "APÓLICE: ".$NomeApolice."<br>";
                            $nTotal_Linhas++;
                            $lineHeightSeguradora += 10;
                        }
            
                        //Averbacoes
                        $qtdeAverbacoes = $msqlSeguro['qtdeAverbacao'];
                        if(!empty($msqlSeguro['D174_Numero_Averbacao'])){
                            //loop averbações
                            $InfoSeguradora .= "AVERBAÇÕES: ";
                            for($i = 0 ; $i < $qtdeAverbacoes ; $i++){
                                $InfoSeguradora .= utf8_encode($msqlSeguro['D174_Numero_Averbacao'])." ; "; 
                                $nTotal_Linhas++;
                                $lineHeightSeguradora += 5;
                            }
                        }else{
                            $InfoSeguradora .="";
                        } 
                        $InfoSeguradora .= "<br>";
                        $cont++;   
                    }        
                }else{     
                    $InfoSeguradora = "";
                }
            }else{
                //comentado pois a princípio nunca cairá nesse if, pois o responsável pelo seguro sempre será a empresa emitente e não uma transportadora contratada
                // $nomeResponsavelSeguro = "<b>CNPJ RESPONSÁVEL PELO SEGURO:<b> ".(!empty($infResp->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $this->mask_cnpj($infResp->getElementsByTagName("CNPJ")->item(0)->nodeValue) : $this->mask_cpf($infResp->getElementsByTagName("CPF")->item(0)->nodeValue) . "<br>";
                // $nTotal_Linhas++;
                // $lineHeightSeguradora += 10;
            }

            $observacoesSeguradora  = $nomeResponsavelSeguro;
            $observacoesSeguradora .= $InfoSeguradora;  
            $lineHeightSeguradoraPrincipal += $lineHeightSeguradora + 40;    
        }

        //verifica se as observações terão que ser cortadas para a segunda página
        if(round($nTotal_Linhas) > $maxLinhas){
            if($nTotal_Linhas_Fiscal_Mais_Notas < 25){
                $MostrarDetalhesContinuacaoNotas = false;
            }else{
                $MostrarDetalhesContinuacaoNotas = true;
                $resultado = round($nTotal_Linhas_Notas - $maxLinhas);
                $lineHeightContinuacao += (17*$resultado);
                $lineHeightContinuacaoPrincipal = $lineHeightContinuacao + 60;
            }
            $nTotal_Paginas++;
            $MostrarDetalhesSeguradora = false;
            $continua= "<b>CONTINUA NO VERSO</b><br>";
        }  

        if($nTotal_Linhas > 55){
            die("O DAMDFE PODE OCUPAR APENAS 50% DO VERSO");
        }

        for ($pagina = 1; $pagina <= $nTotal_Paginas; $pagina++) {
            // Cabeçalho
            // Logo
            if (!empty($this->logomarca) && is_file('/tmp/'.$this->logomarca)){
                $Img_mdfe = imagecreatefromjpeg('/tmp/'.$this->logomarca);
                $Largura_Imagem=imagesx($Img_mdfe);
                $Altura_Imagem=imagesy($Img_mdfe);

                if ($Altura_Imagem > 130) {
                    $porcentagem = (round((130 * 100) / $Altura_Imagem) - 100) / 100;
                    if ($porcentagem < 0) {
                        $porcentagem = $porcentagem + 1;
                    }
                    $Largura_Imagem = $Largura_Imagem * $porcentagem;
                    $Altura_Imagem = 130;
                }
                if ($Largura_Imagem < 0 || $Largura_Imagem > 260) {
                    $porcentagem = (round((260 * 100) / $Largura_Imagem) - 100) / 100;
                    if ($porcentagem <= 0) {
                        $porcentagem = $porcentagem + 1;
                    }
                    $Largura_Imagem = 260;
                    $Altura_Imagem = $Altura_Imagem * $porcentagem;;
                }
            }

            // Empresa
            $EmpresaNome = utf8_encode($mT276['T276_Emitente_Nome']);
            $EmpresaCnpj = utf8_encode($mT276['T276_Emitente_CNPJ']);
            $EmpresaEndereco = utf8_encode($mT276['T276_Emitente_Rua']);
            $EmpresaEndereco .= (!empty($mT276['T276_Emitente_Numero'])) ? ", ".utf8_encode($mT276['T276_Emitente_Numero']) : null;
            $EmpresaEndereco .= (!empty($mT276['C004_Complemento'])) ? " - ".utf8_encode($mT276['C004_Complemento']) : null;
            $EmpresaBairro = utf8_encode($mT276['C004_Bairro']);
            $EmpresaMunicipio = utf8_encode($mT276['T276_Emitente_Cidade']);
            $EmpresaFone = utf8_encode($mT276['C004_Telefone']);
            $EmpresaUF = utf8_encode($mT276['C004_Uf']);
            $EmpresaCEP = utf8_encode($mT276['T276_Emitente_CEP']);
            $EmpresaIE = utf8_encode($mT276['T276_Emitente_Inscricao_Estadual']);
            $EmpresaFone = utf8_encode($mT276['C004_Telefone']);
            $EmpresaEmail = (!empty($mT276['C004_E_Mail'])) ? utf8_encode($mT276['C004_E_Mail']): null;


            // Chave de Acesso
            $chave_acesso = str_replace('MDFe', '', $infMDFe->getAttribute("Id"));
            $chave_acesso1=$this->formata_chave_acesso($chave_acesso);

            // Barcode
            global $id;
            $im     = imagecreatetruecolor(900, 130);  
            $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
            $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
            imagefilledrectangle($im, 0, 0, 900, 130, $white);  
            $data = \Barcode::gd($im, $black, 450, 65, 0, "code128", $chave_acesso, 3, 120);  
            imagepng($im, "/tmp/{$id}.barcode.png");


            //protocolo
            $protocolo = $mT276['T276_Numero_Protocolo_MDFe'];
            $extra = 0;
            $this->pdf->SetFillColor(0,0,0);
            $this->pdf->Code128(145, 23.8-$extra, $chave_acesso, 75, 12);

            if(!empty($protocolo)){
                $nNumero_Protocolo=utf8_decode($infProt->getElementsByTagName("nProt")->item(0)->nodeValue);
                $cData_Protocolo=utf8_decode($infProt->getElementsByTagName("dhRecbto")->item(0)->nodeValue);
                list($cData_Protocolo,$cHora_Protocolo)=explode("T",$cData_Protocolo);
                $protocolo = $nNumero_Protocolo." ".gCorrigeData($cData_Protocolo)." às ".$cHora_Protocolo;   
            }    
     
            //Modelo
            $modelo = 58; //para Mdf-e sempre será 58

            //Série
            $serie = $mT276['T276_Serie'];

            //Número MDFe
            $numeroMDFE = $mT276['T276_Numero_MDFe'];

            //Data e Hora de Emissão
            $EmpresaDataEmissao = gCorrigeData(substr($mT276['T276_Data_Emissao'], 0, 10));
            $Hora=substr($mT276['T276_Hora_Emissao'], 11, 8);
            if (empty($Hora)){
                $Hora=date("H:i:s");
            }
            $EmpresaHoraEmissao = $Hora;

            //UF Carregamento        
            $ufCarregamento = $mT276['carregamentoD018'];

            //UF Descarregamento
            $D018 = mysql_query("SELECT D018_UF FROM D018 WHERE D018_Id='{$mT276['T276_D018_Id_Descarregamento']}'");
            $mD018 = mysql_fetch_array($D018);        
            $ufDescarregamento = $mD018['D018_UF'];

            //Qtde NF-e        
            $qtdeNfe = $mT276['T276_Quantidade_NFe'];

            //Peso Total       
            $pesoTotal = number_format($mT276['T276_Peso_Bruto_Total_Carga'],2,',','.');

            //Veículo - Placa
            $D170 = mysql_query("SELECT D170_Placa,
                                        D170_Tara, 
                                        D170_Capacidade_Kg, 
                                        D170_Capacidade_M3, 
                                        D170_Flag_Rodado, 
                                        D170_Flag_Carroceria, 
                                        D170_D018_Id,
                                        D018_UF 
                                   FROM D170 
                              LEFT JOIN D018 ON D018_Id=D170_D018_Id
                              LEFT JOIN T283 ON D170_Id=T283_D170_Id
                                  WHERE T283_T276_Id='".$this->T276_Id."'");
            while($mD170 = mysql_fetch_array($D170)){
                $placaVeiculo .= $mD170['D170_Placa']."<br>";
            }           

            //Condutores
            $T278 = mysql_query("SELECT count(T278_Id) as qtdeCondutores, 
                                           T278_Nome, 
                                           T278_CPF 
                                      FROM T276 
                                 LEFT JOIN T278 ON T276_Id= T278_T276_Id 
                                     WHERE T276_Id='".$this->T276_Id."'AND T276_C004_Id='".$this->T276_C004_Id ."'GROUP BY T276_Id");
            $mT278 = mysql_fetch_array($T278);
            $qtdeCondutores = $mT278['qtdeCondutores'];
            if($qtdeCondutores > 0){
                $nomeCondutores = "";
                $cpfCondutores = "";
                for($i = 0 ; $i < $mT278['qtdeCondutores'] ; $i++){
                    $nomeCondutores .= utf8_encode($mT278['T278_Nome'])."<br>";
                    $cpfCondutores .= utf8_encode($mT278['T278_CPF'])."<br>";      
                }   
            }         

            $g['C031']['base64FonteDANFE'] = trim($g['C031']['base64FonteDANFE']);
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
                    <div style="width: 0; height: 0; border: 0; margin: 0; padding: 0; position: relatifdieve;">
EOT;
            // Overlays
            if ($this->visualizacao =='S'){
                echo <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; font-weight: bold; white-space: nowrap; font-family: Arial;">
                        <span style="opacity: 0.2; font-size: 1.0em;"><br>PRÉ VISUALIZAÇÃO</span>
                        <span style="opacity: 0.2; font-size: 0.8em;"><br>SEM VALOR FISCAL</span>
                    </div>
EOT;
            } else if ($mT276['T276_Flag_Cancelada']=='S'){
                echo <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 120px; z-index: 100; top: 554px; left: 155px; opacity: 0.2; font-weight: bold; white-space: nowrap; font-family: Arial; color: #f42e2c;">
                        CANCELADA
                    </div>
EOT;
            } else if ($mT276['T276_Ambiente_MDFe']){ 
                echo <<<EOT
                    <div style="text-align: center; position: absolute; font-size: 85px; z-index: 100; top: 520px; left: 120px; font-weight: bold; white-space: nowrap; font-family: Arial;">
                        <span style="opacity: 0.3; font-size: 1.0em;"><br>SEM VALOR FISCAL</span>
                        <span style="font-size: 0.8em;"><br></span>
                    </div>
EOT;
            }
            // HTML
            $marginTop = ($pagina == 1) ? 20 : 85;
            echo <<<EOT
                </div>
                <div style="width: 0; height:{$marginTop}px; border: 0; margin: 0 auto; padding: 0; font-family: Helvetica;"></div>
EOT;
            if($pagina == 1){    
                echo <<<EOT
                        <!-- Empresa -->
                        <div style="margin: 0 auto; width: 1150px; height: 210px; border: 1px solid black; border-radius:10px;">
                            <!-- Empresa 1 -->
                            <span>
                                <img src="{$this->logomarca}" style="width: {$Largura_Imagem}px; height:{$Altura_Imagem}px; position:relative; top:30%; left:5%"/>
                            </span>
                            <table style="float: right; width:70%; height: 190px; padding: 20px;">
                                <tr>
                                    <td colspan="6" style="font-size: 20px; padding-bottom:20px; font-weight: bolder;">{$EmpresaNome}</td>
                                    <td rowspan=8>
                                        <img src="qrCode{$id}.jpg" style="width: 175px; height: 175px; margin-bottom:-1px;"/>
                                    </td> 
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">CNPJ: <b>{$EmpresaCnpj}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">IE: <b>{$EmpresaIE}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">RAZÃO SOCIAL: <b>{$EmpresaNome}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">LOGRADOURO: <b>{$EmpresaEndereco}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">BAIRRO: <b>{$EmpresaBairro}</b> </td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="font-size: 14px;">CEP: <b>{$EmpresaCEP}</b></td>
                                </tr> 
                                <tr style="border: 1px solid pink";>
                                    <td colspan="5" style="font-size: 14px;">MUNICÍPIO/UF: <b>{$EmpresaMunicipio}/{$EmpresaUF} </b></b></td>                                   
                                </tr>
                            </table>
                        </div>
                        <!--  Título Damdfe -->
                        <div style="margin: 0 auto; margin-top:5px; width: 1150px; height: 30px; border: 1px solid black; border-radius:10px; padding:20px 0px 10px 0px; text-align:center;">
                            <span style="font-size: 26px; font-weight: bolder;padding-left:30px;">DAMDFE</span>
                            <span style="font-size: 17px; padding-left:10px;">DOCUMENTO AUXILIAR DE MANIFESTO ELETRÔNICO DE DOCUMENTOS FISCAIS</span>
                        </div>
                        <!--  Fim Título Damdfe -->
                        <!--  Barcode -->
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: 310px; border: 1px solid black; border-radius:10px;">
                            <!-- Barcode -->
                            <div style="width: 1150px; height: 150px; border-bottom: 1px solid black; text-align: center;">
                                <table style="width: 1150px; height: 150px;text-align: center;">
                                    <tr style="width: 1150px;">
                                        <td>
                                            <p style="font-size:14px; margin-bottom:0px; text-align: center;">CONTROLE DO FISCO</p>
                                            <img src="{$id}.barcode.png" style="width: 650px; height: 115px; " />
                                        </td>
                                    </tr>    
                                </table>
                            </div>    
                            <table style="width: 1150px; border-bottom: 1px solid black; ">
                                <tr>
                                    <td style="font-size: 14px; padding: 10px 10px 0px 10px; ">CHAVE DE ACESSO</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 20px; font-weight: bold; text-align: center;padding: 10px 10px 10px 10px;">{$chave_acesso1}</td>
                                </tr>
                            </table>
                            <table style="width: 1150px;">
                                <tr>
                                    <td style="font-size: 14px; padding: 10px 10px 0px 10px; ">PROTOCOLO DE AUTORIZAÇÃO DE USO</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 20px; font-weight: bold; text-align: center;padding: 5px 10px 15px 10px;">{$protocolo}</td>
                                </tr>
                            </table>                   
                        </div>

                        <!-- modelo e série -->
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: 60px; border: 1px solid black; border-radius:10px;">
                            <div style="float: left; width: 85px; height: 41px; border-right: 1px solid black;  padding: 10px; font-size:14px;">MODELO<br />
                                <p style="font-size: 20px; font-weight: bold; text-align:center; margin-top:5px;">{$modelo}</p>
                            </div>
                            <div style="float: left; width: 85px; height: 41px; border-right: 1px solid black;  padding:10px; font-size:14px;">SÉRIE<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$serie}</p>
                            </div>  
                            <div style="float: left; width: 150px; height: 41px; border-right: 1px solid black;  padding:10px; font-size:14px;">NÚMERO<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$numeroMDFE}</p>
                            </div>
                            <div style="float: left; width: 65px; height: 41px; border-right: 1px solid black;  padding:10px; font-size:14px;">FL<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;"></p>
                            </div>
                            <div style="float: left; width: 280px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">DATA E HORA DE EMISSÃO<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$EmpresaDataEmissao} {$EmpresaHoraEmissao}</p>
                            </div> 
                            <div style="float: left; width: 105px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">UF CARREG.<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$ufCarregamento}</p>
                            </div> 
                            <div style="float: left; width: 85px; height: 41px; padding:10px; font-size:14px;">UF DESCAR.<br />
                                <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$ufDescarregamento}</p>
                            </div>   
                        </div>
                        <!-- Fim modelo e série -->

                        <!-- modal rodoviário -->
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: 400px; border: 1px solid black; border-radius:10px;">
                            <!-- linha 1-->
                            <div style="width: 1150px; height: 88px; border-bottom: 1px solid black;">
                                <div style="width: 1150px; height: 25px; border-bottom: 1px solid black; text-align: center;">
                                    <p style="margin-bottom:5px; font-size:17px;">MODAL RODOVIÁRIO DE CARGA</p>
                                </div>    
                                <div style="float: left; width: 105px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">QTDE CT-e<br />
                                    <p style="font-size: 20px; font-weight: bold; text-align:center; margin-top:5px;"></p>
                                </div>
                                <div style="float: left; width: 110px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">QTDE NF-e<br />
                                    <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$qtdeNfe}</p>
                                </div>  
                                <div style="float: left; width: 190px; height: 41px; border-right: 1px solid black; padding:10px; font-size:14px;">PESO TOTAL(Kg)<br />
                                    <p style="font-size: 20px; font-weight: bold;text-align:center;margin-top:5px;">{$pesoTotal}</p>
                                </div>   
                            </div>
                            <!-- fim linha 1-->
                            <!-- linha 2-->
                            <div style="width: 1150px; height: 35px; border-bottom: 1px solid black; text-align: center;">
                                <div style="width: 530px; height: 35px; text-align: center; float:left; border-right:1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">VEÍCULO</p>
                                </div>  
                                <div style="width: 608px; height: 35px; text-align: left;float:right;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left;">CONDUTOR</p>
                                </div>  
                            </div>
                            <!-- fim linha 2-->    
                            <!-- linha 3-->
                            <div style="width: 1150px; height: 30px; border-bottom: 1px solid black; text-align: center;">
                                <div style="width: 264px; height: 30px; float:left; border-right:1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">PLACA</p>
                                </div>  
                                <div style="width: 265px; height: 30px;float:left; border-right: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left;padding-left:10px;">RNTRC</p>
                                </div>  
                                <div style="width: 175px; height: 30px;float:left; border-right: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left;padding-left:10px;">CPF</p>
                                </div>  
                                <div style="width: 306px; height: 30px;float:left;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left;padding-left:10px;">NOME</p>
                                </div>  
                            </div>
                            <!-- fim linha 3-->   
                            <!-- linha 4-->
                            <div style="width: 530px; height: 230px; float:left;">
                                <div style="width: 264px; height: 105px; float:left; border-right:1px solid black; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:15px; text-align:left; padding-left:10px;font-weight:bold;">{$placaVeiculo}</p>
                                </div>  
                                <div style="width: 265px; height: 105px;float:left; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:15px; text-align:left;padding-left:10px;font-weight:bold;">{$RNTRCProprietarioVeiculo}</p>
                                </div>  
                                <div style="width: 530px; height: 30px;float:left; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">VALE PEDÁGIO</p>
                                </div> 
                                <div style="width: 175px; height: 30px; float:left; border-right:1px solid black; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">RESPONSÁVEL CNPJ</p>
                                </div>  
                                <div style="width: 175px; height: 30px;float:left; border-right: 1px solid black; border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">FORNECEDORA CNPJ</p>
                                </div> 
                                <div style="width: 178px; height: 30px;float:left;border-bottom: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;">Nº COMPROVANTE</p>
                                </div> 
                                <div style="width: 175px; height: 60px; float:left; border-right:1px solid black; ">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;font-weight:bold;"></p>
                                </div>  
                                <div style="width: 175px; height: 60px;float:left; border-right: 1px solid black; ">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;font-weight:bold;"></p>
                                </div> 
                                <div style="width: 175px; height: 60px;float:left;">
                                    <p style="margin-bottom:5px; font-size:14px; text-align:left; padding-left:10px;font-weight:bold;"></p>
                                </div> 
                            </div>
                            <div style="width: 616px; height: 228.5px;float:left;border-left: 1px solid black;">
                                <div style="width: 175px; height: 22px;float:left;">
                                    <p style="margin-bottom:5px; font-size:12px; text-align:left;padding-left:10px;font-weight:bold;">{$cpfCondutores}</p>
                                </div>  
                                <div style="width: 438px; height: 226px;float:left; border-left: 1px solid black;">
                                    <p style="margin-bottom:5px; font-size:12px; text-align:left;padding-left:10px;font-weight:bold;">{$nomeCondutores}</p>
                                </div>  
                            </div>
                            <!-- fim linha 4-->         
                        </div>
                        <!-- fim modal rodoviário -->
                        <!-- div observacoes gerais/fisco/contribuinte -->
                        <div style="margin: 0 auto;margin-top:5px; width: 1140px; font-size:14px; height:{$lineHeightFiscalPrincipal}; border: 1px solid black;padding:10px 0px 0px 10px; border-radius:10px;">
                            <p style="float:left;width: 1140px; margin-top:0px; margin-bottom:0px;">OBSERVAÇÕES</p>
                            <div style="margin-top:0px;float:left;width: 1140px; height: {$lineHeightGeral}; font-size:12px;">
                                <p><b></b></p> <!-- observações gerais que podem ser adicionadas -->
                            </div> 
EOT;
                if(!empty($mT276['T276_Informacoes_Adicionais_Fisco'])){
                        echo<<<EOT
                            <div style="margin-top:0px;float:left;width: 560px; height: {$lineHeightFiscal}; font-size:12px; padding-right:10px;border-top: 1px solid black;">
                                <p><b>FISCO:</b> {$obsFisco}</p>
                            </div> 
EOT;
                }
                if(!empty($mT276['T276_Informacoes_Adicionais_Contribuinte'])){                    
                        echo<<<EOT
                            <div style="margin-top:0px;float:left;width: 560px; height: {$lineHeightFiscal}; font-size:12px;border-top: 1px solid black;">
                                <p><b>CONTRIBUINTE:</b> {$obsContribuinte}</p>
                            </div>  
EOT;
                }
                echo "</div>"; //fim div observações gerais/fisco/contribuinte
                if($g['C031']['habilitarInfoNotasObs'] == 'S'){
                    $titulo = "SEQUÊNCIA - NÚMERO NFE - CHAVE DE ACESSO<br>";
                    echo<<<EOT
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: {$lineHeightPrincipal}; border: 1px solid black; border-radius:10px;">  
                            <p style="float:left;width: 1140px; font-size:14px; padding:5px 0px 0px 10px; margin-top:5px; margin-bottom:0px;">DOCUMENTOS FISCAIS VINCULADOS AO MDF-E</p>
                            <p style="padding:10px;float:left;margin-top:0px;font-size: 14px;width: 1150px; text-align:left;height: {$lineHeight};">{$titulo}<b>{$valorTotal}{$observacaoFinal}</b></p>

                        </div>
EOT;
                }
                if($g['C031']['habilitarInfoSeguradoraObs'] == 'S' && $MostrarDetalhesSeguradora){
                    echo<<<EOT
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: {$lineHeightSeguradoraPrincipal}; border: 1px solid black; border-radius:10px;">  
                            <p style="float:left;width: 1140px; font-size:14px; padding:0px 0px 0px 10px; margin-top:5px; margin-bottom:0px;">INFORMAÇÕES SEGURADORA</p>
                            <p style="padding:10px;float:left;margin-top:0px;font-size: 14px;width: 1150px; text-align:left;height: {$lineHeightSeguradora};">{$observacoesSeguradora}</p>

                        </div>
EOT;
                }
                $footer = $continua. "HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR";
            }else{
                $MostrarDetalhesSeguradora = true;
                if($g['C031']['habilitarInfoNotasObs'] == 'S' && $MostrarDetalhesContinuacaoNotas){
                    echo<<<EOT
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: {$lineHeightContinuacaoPrincipal}; border: 1px solid black; border-radius:10px;">  
                            <p style="float:left;width: 1140px; font-size:14px; padding:5px 0px 0px 10px; margin-top:5px; margin-bottom:0px;">DOCUMENTOS FISCAIS VINCULADOS AO MDF-E</p>
                            <p style="padding:10px;float:left;margin-top:0px;font-size: 14px;width: 1150px; text-align:left;height: {$lineHeightContinuacao};">SEQUÊNCIA - NÚMERO NFE - CHAVE DE ACESSO<br><b>{$observacaoFinalContinuacao}</b></p>

                        </div>
EOT;
                }
                if($g['C031']['habilitarInfoSeguradoraObs'] == 'S' && $MostrarDetalhesSeguradora){
                    echo<<<EOT
                        <div style="margin: 0 auto;margin-top:5px;  width: 1150px; height: {$lineHeightSeguradoraPrincipal}; border: 1px solid black; border-radius:10px;">  
                            <p style="float:left;width: 1140px; font-size:14px; padding:0px 0px 0px 10px; margin-top:5px; margin-bottom:0px;">INFORMAÇÕES SEGURADORA</p>
                            <p style="padding:10px;float:left;margin-top:0px;font-size: 14px;width: 1150px; text-align:left;height: {$lineHeightSeguradora};">{$observacoesSeguradora}</p>

                        </div>
EOT;
                }
                $footer = 'HARDNESS SISTEMAS - WWW.HARDNESS.COM.BR';
            }
                echo <<<EOT
                    <div style="margin-top:10px;">
                        <span style="margin-left: 5px; font-size: 13px;">{$footer}</span>
                    </div>
            </body>
            </html>
EOT;
        }               
    } 
}

?>
