<?php
namespace hardness;

/**
 * Classe para criar e enviar a carta de correÃ§ão para o Sefaz.
 */

class classeCCe
{
    
    /**
     * Indicadores de schemas utilizados na construÃƒÂ§ao das mensagens SOAP
     * e na assinatura digital
     **/
    var $URLxsi = 'http://www.w3.org/2001/XMLSchema-instance';
    var $URLxsd = 'http://www.w3.org/2001/XMLSchema';
    var $URLnfe = 'http://www.portalfiscal.inf.br/nfe';
    var $URLdsig= 'http://www.w3.org/2000/09/xmldsig#';
    var $rPrivkey="";    
    var $rPubkey="";
    var $passKey="";
    var $pathCert="";
    var $config;
    
    public function __construct()
    {
        $this->setDescEvento("Carta de Correcao");
        $this->setCondUso("A Carta de Correcao e disciplinada pelo paragrafo 1o-A do art. 7o do Convenio S/N, de 15 de dezembro de 1970 e pode ser utilizada para regularizacao de erro ocorrido na emissao de documento fiscal, desde que o erro nao esteja relacionado com: I - as variaveis que determinam o valor do imposto tais como: base de calculo, aliquota, diferenca de preco, quantidade, valor da operacao ou da prestacao; II - a correcao de dados cadastrais que implique mudanca do remetente ou do destinatario; III - a data de emissao ou de saida.");
        date_default_timezone_set("America/Fortaleza");
    }
        
    public function setTextoCorrecao($xCorrecao)
    {
        if (strlen($xCorrecao) < 15 || strlen($xCorrecao) > 1000){
            
            $this->errorStatus = true;
            $this->errorMsg = "O texto da correcao deve ter entre 15 e 1000 caracteres!";
            $this->xCorrecao = "";
                        
        } else {
            
            $this->xCorrecao = $this->adicionarEscape($xCorrecao);
            
        }
        
    }
    public function getTextoCorrecao()
    {
        return $this->xCorrecao;
    }
    
    public function setDescEvento($x)
    {
        $this->descEvento=$x;
    }
    
    public function getDescEvento()
    {
        return $this->descEvento;    
    }
    
    /**
     * 2   +    6     +    44         +   2        = 54 digitos
     * ID  + tpEvento + chave da NF-e + nSeqEvento
     */
    public function setId()
    {
        $this->id = "ID".$this->getCodigoEvento().$this->getChaveNFe().str_pad($this->getSeqEvento(), 2, "0", STR_PAD_LEFT);
        if (strlen($this->id)!=54)
        {
            $this->errorStatus = true;
            $this->errorMsg = "Id da carta de correcao esta fora do padrao de 54 caracteres [".$this->id."] = ".strlen($this->id)." caracteres";
            $this->id = "";
        } 
    }
        
    public function setT199Id($x)
    {
        $this->T199_id=$x;
    }

    public function getT199Id()
    {
        return $this->T199_id;
    }

    public function setConfig($x)
    {
        $this->config=$x;
    }
    
    public function setC007Id($x)
    {
        $this->c007_id=$x;
    }
    
    public function getC007Id()
    {
        return $this->c007_id;
    }

    public function setFusoHorario($x)
    {
        $this->fusoHorario=$x;
    }
    
    public function getFusoHorario()
    {
        return $this->fusoHorario;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setChaveNFe($chNFe)
    {
        if ($chNFe == ''){
            $this->errorStatus = true;
            $this->errorMsg = "Chave de Acesso da NFe nao pode ser vazio";
            $this->chNFe = "";            
        }
        
        if (strlen($chNFe) != 44){
            $this->errorStatus = true;
            $this->errorMsg = "Chave de Acesos da NFe nao e valida ".strlen($chNFe)." caracteres";
            $this->chNFe = "";
        }
        
        $this->chNFe = $chNFe;
        
    }
    
    /**
     *  Cnpj do Emitente
     */
    public function setCnpjEmitente($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(array(".","-","/"),"",$x);
        $this->cnpj_emitente=$x;
    }
    public function getCnpjEmitente()
    {
        if (empty($this->cnpj_emitente))
        {
            $this->errorMsg = "Nao foi definido o cnpj do emitente";
            $this->errorStatus = true;
        }
        if (strlen($this->cnpj_emitente)!=14)
        {
            $this->errorMsg = "Cnpj do emitente esta incompleto: ".strlen($this->cnpj_emitente)." caracteres";
            $this->errorStatus = true;
        }
        return $this->cnpj_emitente;
    }
    
    public function getVersaoLayout()
    {
        return '1.00';
    }
    
    
    /**
     *  UNIDADE FEDERATIVA DO EMITENTE
     */
    public function setCodigoUFEmitente($x)
    {
        $x=trim($x);
        if (empty($x))
        {
            $this->errorMsg = "Nao foi definido codigo IBGE para o Estado do emitente";
            $this->errorStatus = true;
        }
        else
        {
            $this->codigo_uf_emitente=$x;
        }
    }
    public function getCodigoUFEmitente()
    {
        return $this->codigo_uf_emitente;
    }
    
    /****************************************************
     *  Documento Auxiliar de Documento Fiscal Eletrônico
    ****************************************************/
    public function setTipoDocAuxiliar($x)
    {
        $this->tipo_documento_auxiliar=$x;
    }
    public function getTipoDocAuxiliar()
    {
        if ($this->tipo_documento_auxiliar!=1 && $this->tipo_documento_auxiliar!=2 && $this->tipo_documento_auxiliar!=3 && $this->tipo_documento_auxiliar!=4 && $this->tipo_documento_auxiliar!=5)
        {
            $this->errorMsg = "Documento Auxiliar de Documento Fiscal Eletrônico é inválido: ".$this->tipo_documento_auxiliar;
            $this->errorStatus = true;
        }
        return $this->tipo_documento_auxiliar;
    }
    
    /**
     *  IdentificaÃ§ão do Ambiente: 1-Produção / 2-Homologação
     */
    public function setIdentAmbiente($x)
    {
        $this->identificacao_ambiente=$x;
    }
    public function getIdentAmbiente()
    {
        if ($this->identificacao_ambiente!=1 && $this->identificacao_ambiente!=2)
        {
            $this->errorMsg = "Identificacao do ambiente e invalida: ".$this->identificacao_ambiente;
            $this->errorStatus = true;
        }
        return $this->identificacao_ambiente;
    }
    
    
    public function getChaveNFe()
    {
        return $this->chNFe;
    }
    
    /**
     * Sigla da UF do emitente
     */
    public function setUFEmitente($x)
    {
        $this->sigla_uf_emitente=$x;
    }
    public function getUFEmitente()
    {
        if (empty($this->sigla_uf_emitente))
        {
            $this->errorMsg = "Sigla da unidade federativa do emitente nao foi definida";
            $this->errorStatus = true;
        }
        if (strlen($this->sigla_uf_emitente)>2)
        {
            $this->errorMsg = "Sigla da unidade federativa do emitente ultrapassou 2 caracteres";
            $this->errorStatus = true;
        }
        return $this->sigla_uf_emitente;
    }
    
    
    /**
     * Seta o orgão ou Unidade Federativa.
     * Se o envio for para Svan mudar o numero no orgão para 90
     * @return string
     */
    public function setOrgao($cCodigo_IBGE)
    {                
        //-- MA = 21
        //-- PI = 22
        //-- RN = 24                        
        if ($cCodigo_IBGE=='21' || 
            $cCodigo_IBGE=='22' || 
            $cCodigo_IBGE=='24') {
            
            $this->cOrgao = '90';
            
        } else {
            
            $this->cOrgao = $cCodigo_IBGE;
            
        }
    }
    
    public function getOrgao()
    {
        return $this->cOrgao;
    }
         
                
    public function getNomeXML($tipo)
    {        
        //-- Cria pasta caso a mesma não exista
        $cPasta=$this->getPastaArquivoXML().$tipo;
        if (!is_dir($cPasta)) mkdir($cPasta, 0777);
        if (substr($cPasta,-1)!='/') $cPasta.='/';
        
        return $cPasta.$this->getId().'_'.$this->getIdentAmbiente().'.xml';
    }

    public function getURLXML()
    {
        return $this->getId().'_'.$this->getIdentAmbiente().'.xml';
    }

    /**
     *  Pasta do Arquivo XML
     */
    public function setPastaArquivoXML($x)
    {
        $this->pasta_arquivo_xml=$x;
    }
    public function getPastaArquivoXML()
    {
        //-- Cria pasta caso a mesma não exista
        $cPasta=$this->pasta_arquivo_xml;
        if (!is_dir($cPasta)) mkdir($cPasta, 0777);
        if (substr($cPasta,-1)!='/') $cPasta.='/';
        return $cPasta;
    }
               
    /**
     *  LOCALIZACAO DO ARQUIVO PEM
     */
    public function setCertificadoPEM($x)
    {
        $this->certificado_pem=$x;
    }
    public function getCertificadoPEM()
    {
        return $this->certificado_pem;
    }
    
    public function carregaCertP12($pathCert='')
    {
        //-- Verifica se foi passa o path completo do certificado p12
        if( $pathCert == '' )
        {
            $this->errorMsg .= "<br/><br/>Um certificado deve ser passado para a classe!!";
            $this->errorStatus = true;
            return FALSE;
        }
        elseif (!file_exists($pathCert))
        {
            $this->errorMsg .= "<br/><br/>Certificado não existe: $pathCert";
            $this->errorStatus = true;
        }
        else
        {
            $this->pathCert = $pathCert;
        }
    
        //-- Carrega o certificado em um string
        $key = @file_get_contents($this->pathCert);
    
    
        //-- Carrega os certificados e chaves para um array
        $resp = openssl_pkcs12_read($key,$x509certdata,$this->passKey);
    
        //-- Carrega os certificados em um resource

        $this->rPrivkey = openssl_pkey_get_private($x509certdata['pkey']);
        $this->rPubkey =  openssl_pkey_get_public($x509certdata['cert']);
    
        $certX509= '';
        $data = '';
        $arCert = explode("\n", $x509certdata['cert']);
        foreach ($arCert AS $curData)
        {
            if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 && strncmp($curData, '-----END CERTIFICATE', 20) != 0 )
            {
                $data .= trim($curData);
            }
        }
        //-- Certificado que sera incluso no xml
        $this->certX509 = $data;
    }    
    
    public function setSenhaCertificado($x)
    {
        $this->passKey=$x;
    }
    
    
    public function setVersaoCCe($x)
    {
        $this->versao_cce=$x;
    }
    public function getVersaoCCe()
    {
        if (empty($this->versao_cce))
        {
            $this->errorMsg = "Nao foi definida a versao da carta de correcao";
            $this->errorStatus = true;
        }
        if (strlen($this->versao_cce)>4)
        {
            $this->errorMsg = "Tamanho do campo da versao do leiaute ultrapassou a 4 caracteres ";
            $this->errorStatus = true;
        }
        if (substr($this->versao_cce,-2)=='.')
        {
            $this->errorMsg = "Campo da versao do leiaute deve ter duas casas decimais separado por ponto";
            $this->errorStatus = true;
        }
        return $this->versao_cce;
    }
    
     /**
     * Data e hora do evento no formato AAAA-MM-DDTHH:MM:SSTZD (UTC)
     */
    public function setDataHoraEvento()
    {
        if(empty($this->fusoHorario)){
            $this->fusoHorario = 'America/Fortaleza';
        }
        date_default_timezone_set($this->fusoHorario);
        $this->dhEvento=date('Y-m-d').'T'.date('H:i:sP');
    }    
    public function getDataHoraEvento()
    {
        return $this->dhEvento;
    }
    
    /**
     * Código do Evento
     */
    public function setCodigoEvento($x)
    {
        $this->tpEvento=$x;
    }
    public function getCodigoEvento()
    {
        return $this->tpEvento;
    }

    /**
     * se o numero sequencial do evento não foi informado ou se for maior que 1 digito
     */
    public function setSeqEvento($nSeqEvento=1)
    {
        if ($nSeqEvento == '' || strlen($nSeqEvento) > 2 || !is_numeric($nSeqEvento)){
            $this->errorStatus = true;
            $this->errorMsg = "Numero sequencial esta vazio ou e maior que 99 ou contem caracteres nao numericos [$nSeqEvento]";
            $this->nSeqEvento='';
        } else {
            $this->nSeqEvento=$nSeqEvento;
        }
    }
    public function getSeqEvento()
    {
        return $this->nSeqEvento;
    }

    public function setCondUso($xCondUso)
    {
        if (empty($xCondUso)) {
            $this->errorStatus = true;
            $this->errorMsg = "Condicao de uso da Carta de Correcao esta vazia";
            $this->xCondUso='';            
        } else {
            $this->xCondUso=$xCondUso;
        }
    }
    public function getCondUso()
    {
        return $this->xCondUso;
    }
    
    public function enviarCCe()
    {
        $wsdl = 'RecepcaoEvento';
        $this->setOrgao($this->getCodigoUFEmitente());
        $this->setDataHoraEvento();
        $this->setVersaoCCe('1.00');
        $this->setCodigoEvento('110110');
        $this->setId();
        
        $cceDadosMsg = $this->gerarArquivoCce();
        
        $cceDadosMsg = str_replace('<?xml version="1.0"'.'>','',$cceDadosMsg);        
        $cceDadosMsg = str_replace('<?xml version="1.0" encoding="utf-8"?'.'>','',$cceDadosMsg);
        $cceDadosMsg = str_replace('<?xml version="1.0" encoding="UTF-8"?'.'>','',$cceDadosMsg);
        
        $dados = '';
        $dados .= "<envEvento xmlns=\"".$this->URLnfe."\" versao=\"".$this->getVersaoCCe()."\">";
        $dados .= "<idLote>1</idLote>";
        $dados .= $cceDadosMsg;
        $dados .= "</envEvento>";

        $retorno_soap = $this->sendSOAP($dados, $wsdl);        

        if (is_object($retorno_soap)) {
            $xmlresp = utf8_encode($retorno_soap->any);
            $isRetorno = true;
        } else if (!empty($retorno_soap)) {
            $xmlresp = utf8_encode($retorno_soap);                
            $isRetorno = true;
        }

        if ($isRetorno===true)
        {
            if ($xmlresp == ''){
                //Houve uma falha na comunicaÃ§ão SOAP
                $this->errorMsg .= '<br/><br/>Falha na comunicacao SOAP.';
                return FALSE;
            }
        }
        
        // tratar dados de retorno
        $doc = new \DOMDocument("1.0", "UTF-8"); //cria objeto DOM
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;

        // if (!simplexml_load_string($xmlresp)){
        //     die($xmlresp);
        // }

        $cClausula  = " insert into T200 ( ";
        $cClausula .= " T200_T199_Id, ";            
        $cClausula .= " T200_C007_Id, ";
        $cClausula .= " T200_Data_Hora_Inclusao, ";                        
        $cClausula .= " T200_Retorno_XML, ";           
        $cClausula .= " T200_Status, ";            
        $cClausula .= " T200_Motivo ";            
        $cClausula .= " ) values ( ";
        $cClausula .= " '".$this->getT199Id()."', ";
        $cClausula .= " '".$this->getC007Id()."', ";
        //$cClausula .= " addtime(NOW(),'".$this->config['C020_Fuso_Horario']."'), ";
        $cClausula .= " NOW(), ";
        $cClausula .= " '".addslashes($xmlresp)."', ";
        $cClausula .= " '$cStat', ";
        $cClausula .= " '".addslashes($xMotivo)."' ";
        $cClausula .= " ) ";            
        mysqli_query($cClausula);

        if (mysql_error()){
            die("Erro: " . mysql_error());
        }

        $nT200_Id=mysql_insert_id();


        if (@$doc->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG))
        {

            $cStatProcessamento = $doc->getElementsByTagName("cStat")->item(0)->nodeValue;

            if ($cStatProcessamento != '128'){
                $cStat = !empty($doc->getElementsByTagName('cStat')->item(0)->nodeValue) ? $doc->getElementsByTagName('cStat')->item(0)->nodeValue : '';
                $xMotivo = !empty($doc->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';
                mysqli_query("update T200 set T200_Status='$cStat', T200_Motivo='".addslashes($xMotivo)."' where T200_Id='$nT200_Id'");
                die('Erro: ' . $xMotivo);
            }
            
            $retornocce = $doc->getElementsByTagName("retEvento")->item(0);
            $cStat = !empty($retornocce->getElementsByTagName('cStat')->item(0)->nodeValue) ? $retornocce->getElementsByTagName('cStat')->item(0)->nodeValue : '';
            $xMotivo = !empty($retornocce->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $retornocce->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';

            mysqli_query("update T200 set T200_Status='$cStat', T200_Motivo='".addslashes($xMotivo)."' where T200_Id='$nT200_Id'");
            
            if ($cStat == ''){
                //houve erro
                $this->errorStatus = true;
                $this->errorMsg .= "<br/><br/>Carregou o XML mas o cStatus esta em branco, houve erro na comunicação Soap verifique a mensagem de erro e o debug!!\n";
                return false;
            }
            
            //erro no processamento cStat <> 128
            if ($cStat != 135 ){
                //se cStat <> 135 houve erro e o lote foi rejeitado
                $this->errorStatus = true;
                $this->errorMsg .= "<br/><br/>$cStat - $xMotivo\n";
                return false;
            }
            
            //a correÃ§ão foi aceita cStat == 135
            //carregar a CCe
            $xmlenvCCe = new \DOMDocument(); //cria objeto DOM
            $xmlenvCCe->formatOutput = false;
            $xmlenvCCe->preserveWhiteSpace = false;
            $xmlenvCCe->loadXML('<?xml version="1.0" encoding="utf-8"?>'.$cceDadosMsg,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            $evento = $xmlenvCCe->getElementsByTagName("evento")->item(0);
            
            //Processo completo solicitaÃ§ão + protocolo
            $xmlprocCCe = new \DOMDocument('1.0', 'utf-8');; //cria objeto DOM
            $xmlprocCCe->formatOutput = false;
            $xmlprocCCe->preserveWhiteSpace = false;
            
            //cria a tag procEventoNFe
            $procEventoNFe = $xmlprocCCe->createElement('procEventoNFe');
            $xmlprocCCe->appendChild($procEventoNFe);
            
            //estabele o atributo de versão
            $eventProc_att1 = $procEventoNFe->appendChild($xmlprocCCe->createAttribute('versao'));
            $eventProc_att1->appendChild($xmlprocCCe->createTextNode($this->getVersaoCCe()));
            
            //estabelece o atributo xmlns
            $eventProc_att2 = $procEventoNFe->appendChild($xmlprocCCe->createAttribute('xmlns'));
            $eventProc_att2->appendChild($xmlprocCCe->createTextNode($this->URLnfe));
            
            //carrega o node evento
            $node1 = $xmlprocCCe->importNode($evento, true);
            $procEventoNFe->appendChild($node1);
            
            //carrega o node retEvento
            $node2 = $xmlprocCCe->importNode($retornocce, true);
            $procEventoNFe->appendChild($node2);
            
            //salva o xml como string em uma variÃ¡vel
            $procXML = $xmlprocCCe->saveXML();
            
            //remove as informaÃ§Ãµes indesejadas
            $procXML = str_replace("xmlns:default=\"http://www.w3.org/2000/09/xmldsig#\"",'',$procXML);
            $procXML = str_replace(array('default:',':default'),'',$procXML);
            $procXML = str_replace(array("\n","\r","\s"),'',$procXML);
            
            $cClausula = "update T199 set T199_Flag_Enviado='S' where T199_Id=".$this->getT199Id();
            mysqli_query($cClausula);

            $cClausula = "update T200 set T200_Retorno_XML='".addslashes($procXML)."' where T200_Id=$nT200_Id";
            mysqli_query($cClausula);                        
            
            //salva o arquivo xml
            if (!file_put_contents($this->getNomeXML('autorizado'), $procXML)){
                $this->errorStatus = true;
                $this->errorMsg .= "<br/><br/>Falha na gravação do xml autorizado da carta de correcao procCCe!!\n";
            }
            return $procXML;            
            
        }
        else
        {
            $this->errorMsg  .= "<br/><br/>Nao foi possivel carregar o retorno do XML (sendSOAP) $xmlresp";
            $this->errorStatus = true;
        }
        return $bRet;
        
    }        
    
    public function gerarArquivoCce()
    {        
        //-- Criando XML
        $oNovo = new \DOMDocument("1.0", "UTF-8");
        
        //-- Descartar espacos em branco
        $oNovo->preserveWhiteSpace = false;
        
        //-- Gerar um codigo bem legivel
        $oNovo  ->formatOutput = true;
                
        //-- Tag Raiz
        $oCCe = $oNovo->createElement("evento");
        $oCCe->setAttribute("xmlns", $this->URLnfe);
        $oNovo->appendChild($oCCe);
        $oCCe->setAttribute("versao", $this->getVersaoCCe());
        
        $oHP06 = $oNovo->createElement("infEvento");                
        $oHP06->setAttribute("Id", $this->getId());
        $oCCe->appendChild($oHP06);
        
        $oHP08 = $oNovo->createElement("cOrgao",$this->getOrgao());
        $oHP06->appendChild($oHP08);
        
        $oHP09 = $oNovo->createElement("tpAmb",$this->getIdentAmbiente());
        $oHP06->appendChild($oHP09);

        $oHP10 = $oNovo->createElement("CNPJ",$this->getCnpjEmitente());
        $oHP06->appendChild($oHP10);

        //$oHP11 = $oNovo->createElement("CPF","");
        //$oHP06->appendChild($oHP11);
        
        $oHP12 = $oNovo->createElement("chNFe",$this->getChaveNFe());
        $oHP06->appendChild($oHP12);

        $oHP13 = $oNovo->createElement("dhEvento",$this->getDataHoraEvento());
        $oHP06->appendChild($oHP13);

        $oHP14 = $oNovo->createElement("tpEvento",$this->getCodigoEvento());
        $oHP06->appendChild($oHP14);

        $oHP15 = $oNovo->createElement("nSeqEvento",$this->getSeqEvento());
        $oHP06->appendChild($oHP15);

        $oHP16 = $oNovo->createElement("verEvento",$this->getVersaoCCe());
        $oHP06->appendChild($oHP16);

        $oHP17 = $oNovo->createElement("detEvento");        
        $oHP17->setAttribute("versao", $this->getVersaoCCe());
        $oHP06->appendChild($oHP17);

        $oHP19 = $oNovo->createElement("descEvento",$this->getDescEvento());
        $oHP17->appendChild($oHP19);

        $xCorrecao = trim($this->getTextoCorrecao());
        $oHP20 = $oNovo->createElement("xCorrecao", $xCorrecao);
        $oHP17->appendChild($oHP20);

        $oHP20a = $oNovo->createElement("xCondUso",$this->getCondUso());
        $oHP17->appendChild($oHP20a);

        //-- Grava o arquivo com os dados da carta de correÃ§ão sem assinatura digital
        $oNovo->normalize();
        
        $oNovo->save($this->getNomeXML('temp'));
        unset($oNovo);
        $conteudo = $this->assinaPHPNF(file_get_contents($this->getNomeXML('temp')), 'infEvento','evento');
        
        //-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual
        $conteudo = str_replace(array(chr(13),chr(10),"\r","\n","\r\n")," ",$conteudo);
        $conteudo = str_replace("  ","",$conteudo);
        $conteudo = str_replace("> <","><",$conteudo);
        $conteudo = str_replace(" <","<",$conteudo);
        $conteudo = str_replace("> ",">",$conteudo);
        file_put_contents($this->getNomeXML('assinado'), $conteudo);
                
        return $conteudo;
    }
    
    
    public function sendSOAP($param,$wsdl)
    {
        $dataVer = $this->getVersaoLayout();
        
        //-- Monta a url do serviÃ§o
        if (!$URL = $this->setWebService($wsdl))
        {
            return $this->errorMsg;
        }
    
        if (substr($URL,-5)!='?WSDL')
        {
            $xURL.=$URL.'?WSDL';
        }
    
        //echo "<div style='position:absolute;top:97%;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>WebService: ".$URL."</font></div>";
    
        $ArquivoPEM_Padrao=basename($this->getCertificadoPEM());
        list($ArquivoPEM,$Extencao)=explode(".",$ArquivoPEM_Padrao);
        $ArquivoKEY_Padrao=str_replace("$ArquivoPEM_Padrao",$ArquivoPEM."_key.$Extencao",$this->getCertificadoPEM());
        $ArquivoCERT_Padrao=str_replace("$ArquivoPEM_Padrao",$ArquivoPEM."_cert.$Extencao",$this->getCertificadoPEM());
        
        //-- Verifica se existe o certificado hardness para enviar o xml
        $ArquivoKEY_Hardness=str_replace("$ArquivoPEM_Padrao",$ArquivoPEM."_key_hardness.$Extencao",$this->getCertificadoPEM());
        $ArquivoCERT_Hardness=str_replace("$ArquivoPEM_Padrao",$ArquivoPEM."_cert_hardness.$Extencao",$this->getCertificadoPEM());
        
        if (file_exists($ArquivoKEY_Hardness) and file_exists($ArquivoCERT_Hardness))
        {
            $this->pubKEY=$ArquivoCERT_Hardness;
            $this->priKEY=$ArquivoKEY_Hardness;
        }
        else
        {
            $this->pubKEY=$ArquivoCERT_Padrao;
            $this->priKEY=$ArquivoKEY_Padrao;
        }
  
        try{
            if (true)
            {

                //$namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/'.$wsdl;
                $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/NFeRecepcaoEvento4';
                $cabec = '<nfeCabecMsg xmlns="'. $namespace . '"><cUF>'.$this->getCodigoUFEmitente().'</cUF><versaoDados>'.$dataVer.'</versaoDados></nfeCabecMsg>';
                $dados = '<nfeDadosMsg xmlns="'. $namespace . '">'.$param.'</nfeDadosMsg>';
                if($this->getUFEmitente() == 'CE' || $this->getUFEmitente() == 'GO'){
                    $wsdl = 'nfeRecepcaoEvento';
                }

//      die("URL: ".$URL."<br /> namespace: ".$namespace." <br />cabec: ".$cabec." <br />dados: ".$dados." <br />wsdl: ".$wsdl." <br />ambiente: ".$this->getIdentAmbiente());
                $retorno = $this->__sendSOAP2($URL, $namespace, $cabec, $dados, $wsdl, $this->getIdentAmbiente());
            }
            else
            {

                $client = new \SoapClient("$xURL",array(
                        'encoding'     => 'UTF-8',
                        'local_cert'   => $this->getCertificadoPEM(),
                        'passphrase'   => $this->passKey,
                        'soap_version' => SOAP_1_2,
                        'trace'        => true,
                        'exceptions'   => false,
                        'style'        => 2,
                        'use'          => 2,
                )
                );
    
    
                //echo $client->soapDebug;
    
                $headerWSDL = rtrim($wsdl, 'NF');
    
                $headerbody = array('cUF' => $this->getCodigoUFEmitente(), 'versaoDados' => $dataVer);
                $header = new \SOAPHeader('http://www.portalfiscal.inf.br/nfe/wsdl/'.$headerWSDL, 'nfeCabecMsg', $headerbody);
                $client->__setSoapHeaders($header);
    
                $nfeDadosMsg = new \stdClass;
                $nfeDadosMsg->any = $param;
    
                //-- CHAMA O METODO
                if ($wsdl=='ConsultaCadastro')
                {
                    $retorno = $client->nfeConsultaCadastro2($nfeDadosMsg);
                }
                elseif( $wsdl=='NfeRecepcao')
                {
                    $retorno = $client->nfeRecepcaoLote2($nfeDadosMsg);
                }
                elseif( $wsdl=='NfeRetRecepcao')
                {
                    $retorno = $client->nfeRetRecepcao2($nfeDadosMsg);
                }
                elseif( $wsdl=='NfeCancelamento')
                {
                    $retorno = $client->nfeCancelamentoNF2($nfeDadosMsg);
                }
                elseif( $wsdl=='NfeInutilizacao')
                {
                    $retorno = $client->nfeInutilizacaoNF2($nfeDadosMsg);
                }
                elseif( $wsdl=='NfeConsultaNF')
                {
                    $retorno = $client->nfeConsultaNF2($nfeDadosMsg);
                }
                elseif( $wsdl=='NfeStatusServico')
                {
                    $retorno = $client->nfeStatusServicoNF2($nfeDadosMsg);
                }
                elseif( $wsdl=='RecepcaoEvento')
                {
                    $retorno = $client->nfeRecepcaoEvento($nfeDadosMsg);
                }
    
            }
    
        }
        catch(\Exception $e)
        {
            $this->errorMsg = $e->getMessage();
            $this->errorStatus = true;
    
        }
    
        if (is_soap_fault($retorno))
        {
            echo "<br><hr><font color=blue>Requisicao<br>".htmlspecialchars($param)."</font>";
    
            if (is_object($retorno))
            {
                $xmlresp = utf8_encode($retorno->any);
                echo "<hr><font color=red>Retorno:<br>$retorno<br>".htmlspecialchars($xmlresp)."</font>";
            }
            else
            {
                echo "<hr><font color=red>Retorno:<br>Nao Retornou NADA</font>";
            }
            echo "<hr><font color=gray>";
            foreach($retorno as $erro)
            {
               // echo htmlspecialchars($erro,ENT_QUOTES)."<br>";
            }
            exit;
        }
        
        $this->retorno_xml_sefaz=$retorno;
        return $retorno;    
    }    
       

    protected function __sendSOAP2($urlsefaz,$namespace,$cabecalho,$dados,$metodo,$ambiente,$UF=''){
        
        if ($urlsefaz == ''){
            //não houve retorno
            $this->errorMsg = 'URL do webservice não disponível.';
            $this->errStatus = true;
        }

        $data = '';
        $data .= '<?xml version="1.0" encoding="utf-8"?>';
        $data .= '<soap12:Envelope ';
        $data .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $data .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
        $data .= 'xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">';
        $data .= '<soap12:Header>';
        $data .= $cabecalho;
        $data .= '</soap12:Header>';
        $data .= '<soap12:Body>';
        $data .= $dados;
        $data .= '</soap12:Body>';
        $data .= '</soap12:Envelope>';

        //[Informational 1xx]
        $cCode['100']="Continue";
        $cCode['101']="Switching Protocols";
        //[Successful 2xx]
        $cCode['200']="OK";
        $cCode['201']="Created";
        $cCode['202']="Accepted";
        $cCode['203']="Non-Authoritative Information";
        $cCode['204']="No Content";
        $cCode['205']="Reset Content";
        $cCode['206']="Partial Content";
        //[Redirection 3xx]
        $cCode['300']="Multiple Choices";
        $cCode['301']="Moved Permanently";
        $cCode['302']="Found";
        $cCode['303']="See Other";
        $cCode['304']="Not Modified";
        $cCode['305']="Use Proxy";
        $cCode['306']="(Unused)";
        $cCode['307']="Temporary Redirect";
        //[Client Error 4xx]
        $cCode['400']="Bad Request";
        $cCode['401']="Unauthorized";
        $cCode['402']="Payment Required";
        $cCode['403']="Forbidden";
        $cCode['404']="Not Found";
        $cCode['405']="Method Not Allowed";
        $cCode['406']="Not Acceptable";
        $cCode['407']="Proxy Authentication Required";
        $cCode['408']="Request Timeout";
        $cCode['409']="Conflict";
        $cCode['410']="Gone";
        $cCode['411']="Length Required";
        $cCode['412']="Precondition Failed";
        $cCode['413']="Request Entity Too Large";
        $cCode['414']="Request-URI Too Long";
        $cCode['415']="Unsupported Media Type";
        $cCode['416']="Requested Range Not Satisfiable";
        $cCode['417']="Expectation Failed";
        //[Server Error 5xx]
        $cCode['500']="Internal Server Error";
        $cCode['501']="Not Implemented";
        $cCode['502']="Bad Gateway";
        $cCode['503']="Service Unavailable";
        $cCode['504']="Gateway Timeout";
        $cCode['505']="HTTP Version Not Supported";

        $tamanho = strlen($data);
        /*
        if($this->enableSCAN){
            //monta a terminação do URL
            switch ($metodo){
                case 'nfeRecepcaoLote2':
                    $servico = "NfeRecepcao";
                    break;
                case 'nfeRetRecepcao2':
                    $servico = "NfeRetRecepcao";
                    break;
                case 'nfeCancelamentoNF2':
                    $servico = "NfeCancelamento";
                    break;
                case 'nfeInutilizacaoNF2':
                    $servico = "NfeInutilizacao";
                    break;
                case 'nfeConsultaNF2':
                    $servico = "NfeConsulta";
                    break;
                case 'nfeStatusServicoNF2':
                    $servico = "NfeStatusServico";
                    break;
                case 'consultaCadastro':
                    $servico = "CadConsultaCadastro";
                    break;
            }
            
            $aURL = $this->loadSEFAZ( $this->raizDir . 'config' . DIRECTORY_SEPARATOR . "def_ws2.xml",$ambiente,'SCAN');
            $urlsefaz = $aURL[$servico]['URL'];
        } 
        */
        $parametros = Array('Content-Type: application/soap+xml;charset=utf-8;action="'.$namespace."/".$metodo.'"','SOAPAction: "'.$metodo.'"',"Content-length: $tamanho");
        
        $_aspa = '"';
        $oCurl = curl_init();

        if(is_array($this->aProxy)){
            curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, "CURLPROXY_HTTP");
            curl_setopt($oCurl, CURLOPT_PROXY, $this->aProxy['IP'].':'.$this->aProxy['PORT']);
            if( $this->aProxy['PASS'] != '' ){
                curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->aProxy['USER'].':'.$this->aProxy['PASS']);
                curl_setopt($oCurl, CURLOPT_PROXYAUTH, "CURLAUTH_BASIC");
            } //fim if senha proxy
        }//fim if aProxy
        curl_setopt($oCurl, CURLOPT_URL, $urlsefaz.'');
        curl_setopt($oCurl, CURLOPT_PORT , 443);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, 1); //retorna o cabeçalho de resposta
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 6);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($oCurl, CURLOPT_SSLCERT, $this->pubKEY);
        curl_setopt($oCurl, CURLOPT_SSLKEY, $this->priKEY);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER,$parametros);
    curl_setopt($oCurl, CURLOPT_TIMEOUT, 25);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 25);
    if (strpos($urlsefaz, '.mg.')){
        curl_setopt($oCurl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
    }

        $__xml = curl_exec($oCurl);

        $info = curl_getinfo($oCurl); //informações da conexão
        
        $txtInfo ="";
        $txtInfo .= "URL=$info[url]\n";
        $txtInfo .= "Content type=$info[content_type]\n";
        $txtInfo .= "Http Code=$info[http_code]\n";
        $txtInfo .= "Header Size=$info[header_size]\n";
        $txtInfo .= "Request Size=$info[request_size]\n";
        $txtInfo .= "Filetime=$info[filetime]\n";
        $txtInfo .= "SSL Verify Result=$info[ssl_verify_result]\n";
        $txtInfo .= "Redirect Count=$info[redirect_count]\n";
        $txtInfo .= "Total Time=$info[total_time]\n";
        $txtInfo .= "Namelookup=$info[namelookup_time]\n";
        $txtInfo .= "Connect Time=$info[connect_time]\n";
        $txtInfo .= "Pretransfer Time=$info[pretransfer_time]\n";
        $txtInfo .= "Size Upload=$info[size_upload]\n";
        $txtInfo .= "Size Download=$info[size_download]\n";
        $txtInfo .= "Speed Download=$info[speed_download]\n";
        $txtInfo .= "Speed Upload=$info[speed_upload]\n";
        $txtInfo .= "Download Content Length=$info[download_content_length]\n";
        $txtInfo .= "Upload Content Length=$info[upload_content_length]\n";
        $txtInfo .= "Start Transfer Time=$info[starttransfer_time]\n";
        $txtInfo .= "Redirect Time=$info[redirect_time]\n";
        $txtInfo .= "Certinfo=$info[certinfo]\n";

        $n = strlen($__xml);
        $x = stripos($__xml, "<");
        $xml = substr($__xml, $x, $n-$x);
        
        $Requisicao = htmlspecialchars($data,ENT_QUOTES);
        $Requisicao = str_replace("&lt;","<br>&lt;",$Requisicao);
        $Requisicao = str_replace("<br>&lt;/","&lt;/",$Requisicao);        
        $Requisicao = $this->quebraString($Requisicao,80);
        
        $Retorno = htmlspecialchars($xml,ENT_QUOTES);
        $Retorno = str_replace("&lt;","<br>&lt;",$Retorno);
        $Retorno = str_replace("<br>&lt;/","&lt;/",$Retorno);
        $Retorno = $this->quebraString($Retorno,80);
                
        $cDebug  = "<table>";
        
            $cDebug .= "<tr>";
                $cDebug .= "<td>";
                    $cDebug .= "<font color=green font=arial size:10px>Requisição:<br>$Requisicao</font>";
                $cDebug .= "</td>";
            $cDebug .= "</tr>";
        
            $cDebug .= "<tr>";
                $cDebug .= "<td>";
                    $cDebug .= "<font color=black font=arial size:10px>Debug Soap:<br><pre>$txtInfo</pre></font>";
                $cDebug .= "</td>";
            $cDebug .= "</tr>";

            $cDebug .= "<tr>";
                $cDebug .= "<td>";
                    $cDebug .= "<font color=red font=arial size:10px>Retorno<br>$Retorno</font>";
                $cDebug .= "</td>";
            $cDebug .= "</tr>";
            
        $cDebug .= "</table>";
        
        $this->soapDebug = $cDebug;
        
        if ($__xml === false){
            //não houve retorno
            $this->errorMsg .= "<br/><br/><b>XML nao retornado (__sendSOAP2), log de erros:</b> <br/>curl_error(oCurl): " .curl_error($oCurl) . "<br/>info[http_code]:  " . $info['http_code'] . "<br/>cCode[info[http_code]] " . $cCode[$info['http_code']];
            $this->errStatus = true;
        } else {
            //houve retorno mas ainda pode ser uma mensagem de erro do webservice
            $this->errorMsg .= "<br/><br/> XML Retornado: <br/>info[http_code]: " . $info['http_code'] . "<br/>cCode[info[http_code]]: " . $cCode[$info['http_code']] . "<br/><br/>";
            $this->errStatus = false;
        }
        curl_close($oCurl);

        return $xml;
    } //fim __sendSOAP2

    public function setWebService($cMetodo='')
    {
        // Versão 3.10

        /*$mListaWS['CE']['RecepcaoEvento']['1.00']['1']="https://nfe.sefaz.ce.gov.br/nfe2/services/RecepcaoEvento?wsdl";
        $mListaWS['CE']['RecepcaoEvento']['1.00']['2']="https://nfeh.sefaz.ce.gov.br/nfe2/services/RecepcaoEvento?wsdl";

        $mListaWS['GO']['RecepcaoEvento']['1.00']['1']="https://nfe.sefaz.go.gov.br/nfe/services/v2/RecepcaoEvento?wsdl";
        $mListaWS['GO']['RecepcaoEvento']['1.00']['2']="https://homolog.sefaz.go.gov.br/nfe/services/v2/RecepcaoEvento?wsdl";

        $mListaWS['PR']['RecepcaoEvento']['1.00']['1']="https://nfe.fazenda.pr.gov.br/nfe/NFeRecepcaoEvento?wsdlteste";
        $mListaWS['PR']['RecepcaoEvento']['1.00']['2']="https://homologacao.nfe.fazenda.pr.gov.br/nfe/NFeRecepcaoEvento?wsdlteste";

        $mListaWS['RS']['RecepcaoEvento']['1.00']['1']="https://nfe.sefazrs.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['RS']['RecepcaoEvento']['1.00']['2']="https://homologacao.nfe.sefaz.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";

//        $mListaWS['SP']['RecepcaoEvento']['1.00']['1']="https://nfe.fazenda.sp.gov.br/eventosWEB/services/RecepcaoEvento.asmx";
        $mListaWS['SP']['RecepcaoEvento']['1.00']['1']="https://nfe.fazenda.sp.gov.br/ws/recepcaoevento.asmx";
        $mListaWS['SP']['RecepcaoEvento']['1.00']['2']="https://homologacao.nfe.fazenda.sp.gov.br/eventosWEB/services/RecepcaoEvento.asmx";

        $mListaWS['MG']['RecepcaoEvento']['1.00']['1']="https://nfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento";
        $mListaWS['MG']['RecepcaoEvento']['1.00']['2']="https://nfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento";

        $mListaWS['SVRS']['RecepcaoEvento']['1.00']['1']="https://nfe.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['1.00']['2']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";

        $mListaWS['SVAN']['RecepcaoEvento']['1.00']['1']="https://www.sefazvirtual.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        $mListaWS['SVAN']['RecepcaoEvento']['1.00']['2']="https://hom.sefazvirtual.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";*/

        //Versão 4.00
        $mListaWS['CE']['RecepcaoEvento']['1.00']['1']="https://nfe.sefaz.ce.gov.br/nfe4/services/NFeRecepcaoEvento4?wsdl";
        $mListaWS['CE']['RecepcaoEvento']['1.00']['2']="https://nfeh.sefaz.ce.gov.br/nfe4/services/NFeRecepcaoEvento4?WSDL";

        $mListaWS['GO']['RecepcaoEvento']['1.00']['1']="https://nfe.sefaz.go.gov.br/nfe/services/NFeRecepcaoEvento4?wsdl";
        $mListaWS['GO']['RecepcaoEvento']['1.00']['2']="https://homolog.sefaz.go.gov.br/nfe/services/NFeRecepcaoEvento4?wsdl";

        $mListaWS['PR']['RecepcaoEvento']['1.00']['1']="https://nfe.sefa.pr.gov.br/nfe/NFeRecepcaoEvento4?wsdl";
        $mListaWS['PR']['RecepcaoEvento']['1.00']['2']="https://homologacao.nfe.sefa.pr.gov.br/nfe/NFeRecepcaoEvento4?wsdl";

        $mListaWS['PE']['RecepcaoEvento']['1.00']['1']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NFeRecepcaoEvento4";
        $mListaWS['PE']['RecepcaoEvento']['1.00']['2']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NFeRecepcaoEvento4";

        $mListaWS['RS']['RecepcaoEvento']['1.00']['1']="https://nfe.sefazrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";
        $mListaWS['RS']['RecepcaoEvento']['1.00']['2']="https://nfe-homologacao.sefazrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";
        
        $mListaWS['SP']['RecepcaoEvento']['1.00']['1']="https://nfe.fazenda.sp.gov.br/ws/nferecepcaoevento4.asmx";
        $mListaWS['SP']['RecepcaoEvento']['1.00']['2']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nferecepcaoevento4.asmx";

        $mListaWS['MG']['RecepcaoEvento']['1.00']['1']="https://nfe.fazenda.mg.gov.br/nfe2/services/NFeRecepcaoEvento4";
        $mListaWS['MG']['RecepcaoEvento']['1.00']['2']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeRecepcaoEvento4";
        
        $mListaWS['SVRS']['RecepcaoEvento']['1.00']['1']="https://nfe.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['1.00']['2']="https://nfe-homologacao.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";

        $mListaWS['SVAN']['RecepcaoEvento']['1.00']['1']="https://www.sefazvirtual.fazenda.gov.br/NFeRecepcaoEvento4/NFeRecepcaoEvento4.asmx";
        $mListaWS['SVAN']['RecepcaoEvento']['1.00']['2']="https://hom.sefazvirtual.fazenda.gov.br/NFeRecepcaoEvento4/NFeRecepcaoEvento4.asmx";
        
        $cAmbiente=$this->getIdentAmbiente();
        $cTipoDocumento=$this->getTipoDocAuxiliar();
        $cEstadoEmitente=$this->getUFEmitente();
        $cVersao=$this->getVersaoCCe();
        
        if ($cTipoDocumento==3)
        {
            $cEstadoEmitente="SCAN";
        }
        elseif ($cEstadoEmitente=='AC' or $cEstadoEmitente=='AL' or $cEstadoEmitente=='AM' or $cEstadoEmitente=='AP' or $cEstadoEmitente=='DF' or $cEstadoEmitente=='MS' or $cEstadoEmitente=='PB' or $cEstadoEmitente=='RJ' or $cEstadoEmitente=='RO' or $cEstadoEmitente=='RR' or $cEstadoEmitente=='SC' or $cEstadoEmitente=='SE' or $cEstadoEmitente=='TO' or $cEstadoEmitente=='ES' or $cEstadoEmitente=='PA') 
        {
            $cEstadoEmitente='SVRS';
        }
        elseif ($cEstadoEmitente=='MA' or $cEstadoEmitente=='PI' or $cEstadoEmitente=='RN' )
        {
            $cEstadoEmitente='SVAN';
        }
        
        if (!empty($cEstadoEmitente) and !empty($cMetodo) and !empty($cVersao) and !empty($cAmbiente))
        {
            $cRetorno = $mListaWS[$cEstadoEmitente][$cMetodo][$cVersao][$cAmbiente];
        }
        $this->ultimo_webservice_utilizado = $cRetorno;
        
        if (empty($cRetorno))
        {
            $this->errorMsg .= "<br/><br/><b>Web Service:</b><br/>Nao foi identificado o ambiente da nota fiscal. Estado Emitente: $cEstadoEmitente / Ambiente: $cAmbiente / Versao: $cVersao / Metodo: $cMetodo";
            $this->errorStatus = true;
            return false;
        }
        else
        {
        
            return $cRetorno;
        }
    }

    public function assinaPHPNF($nfe, $tagid='infCanc',$taginicio='cancNFe', $outDir=''){
        //carrega as chaves do certifciado p12
        $key = file_get_contents($this->pathCert);
        $resp = openssl_pkcs12_read ($key,$x509certdata,$this->passKey);
        $rPrivkey = openssl_pkey_get_private($x509certdata['pkey']);
        $rPubkey =  openssl_pkey_get_public($x509certdata['cert']);

        if (!$key) die("Erro ao assinar XML: file_get_contents($this->pathCert)");
        if (!$resp) die("Erro ao assinar XML: openssl_pkcs12_read ($key,$x509certdata,$this->passKey)");
        if (!$rPrivkey) die("Erro ao assinar XML: openssl_pkey_get_private({$x509certdata['pkey']})");
        if (!$rPrivkey) die("Erro ao assinar XML: openssl_pkey_get_public({$x509certdata['cert']})");
    
        $certX509= '';
        $data = '';
        $arCert = explode("\n", $x509certdata['cert']);
        foreach ($arCert AS $curData) {
            if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 && strncmp($curData, '-----END CERTIFICATE', 20) != 0 ) {
                $data .= trim($curData);
            }
        }
        // certificado que sera incluso no xml
        $certX509 = $data;
    
        // limpeza do xml com a retirada dos CR e LF
        $order = array("\r\n", "\n", "\r");
        $replace = '';
        $nfe = str_replace($order, $replace, $nfe);
        // carrega o documento no DOM
        $xmldoc = new \DOMDocument();
        $xmldoc->preservWhiteSpace = FALSE; //elimina espaÃƒÂ§os em branco
        $xmldoc->formatOutput = FALSE;
        // muito importante deixar ativadas as opÃƒÂ§oes para limpar os espacos em branco
        // e as tags vazias
        $nfe = utf8_decode($nfe);
        $xmldoc->loadXML("$nfe",LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
    
        //extrair a tag com os dados a serem assinados
        $infNFe = $xmldoc->getElementsByTagName($tagid)->item(0);
        $id = trim($infNFe->getAttribute("Id"));

        //extrai os dados da tag para uma string
        $dados = $infNFe->C14N(FALSE,FALSE,NULL,NULL);
        //calcular o hash dos dados
        $hashValue = hash('sha1',$dados,TRUE);
        //converte o valor para base64 para serem colocados no xml
        $digValue = base64_encode($hashValue);
        //monta a tag da assinatura digital
        $templsign = '<Signature xmlns="'.$this->URLdsig.'"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /><SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1" /><Reference URI="#'.$id.'"><Transforms><Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" /><Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /></Transforms><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" /><DigestValue>'.$digValue.'</DigestValue></Reference></SignedInfo><SignatureValue></SignatureValue><KeyInfo><X509Data><X509Certificate>'.$certX509.'</X509Certificate></X509Data></KeyInfo></Signature>';
        //salva o xml normalizado
        $nfe = $xmldoc->saveXML();
        //remove a tag de fechamento final da NFe </NFe>
        $nfe = str_replace("</$taginicio>","",$nfe);
        //acrescenta o template da assinatura
        $nfe = $nfe . $templsign."</$taginicio>";
    
        // limpa algumas variaveis da memoria
        unset($xmldoc);
        unset($dados);
        unset($hashValue);
        unset($digValue);
        unset($id);
        unset($infNFe);
        unset($order);
        unset($replace);
        unset($templsign);
        unset($data);
    
        //carrega novamente o DOM com a NFe
        $xmldoc = new \DOMDocument();
        $xmldoc->preservWhiteSpace = FALSE; //elimina espaÃƒÂ§os em branco
        $xmldoc->formatOutput = FALSE;
    
        $xmldoc->loadXML($nfe,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        //obtem a tag com as informaÃƒÂ§oes da assinatura
        // esta informaÃƒÂ§oes e que serao assinadas
        $SignedInfo = $xmldoc->getElementsByTagName('SignedInfo')->item(0);
        // extrai os dados a serem assinados para uma string
        $dados = $SignedInfo->C14N(FALSE,FALSE,NULL,NULL);
        $signature = '';
        //executa a assinatura digital usando o resource da chave privada
        $resp = openssl_sign($dados,$signature,$rPrivkey);
        //codifica assinatura para o padrao base64
        $signatureValue = base64_encode($signature);
        $SignValue = $xmldoc->getElementsByTagName('SignatureValue')->item(0);
        $SignValue->nodeValue = $signatureValue;
        $nfe = $xmldoc->saveXML();
    
        //se for passado parametro de destino salvar o xml como arquvo
        //$ret = $xmldoc->save(getNomeXML('assinado'));
        return $nfe;
    }   

    public function quebraString($String,$nTamanho=160)
    {
        $cRetorno='';
        $mLinha = explode("<br>",$String);
        for ($i=0;$i<count($mLinha);$i++) {
            if (strlen($mLinha[$i])>=$nTamanho) {
                for ($n = 0;$n<strlen($mLinha[$i]);$n+=$nTamanho) {
                    $c=substr($mLinha[$i],$n,$nTamanho);
                    $cRetorno.=$c."<br>";
                }
            } else {
                $cRetorno.=$mLinha[$i]."<br>";
            }
        }
        return $cRetorno;
    }

    public function adicionarEscape($x)
    {
        $replace = array(
            '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
            '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
            '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
            'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
            'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
            'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
            'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
            'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
            'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
            'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
            'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
            'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
            '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
            'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
            'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
            'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
            'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
            'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
            'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
            'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
            'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
            'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
            'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
            'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
            '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
            'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
            'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
            'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
            'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
            'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
            'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
            'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
            'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
            'ю' => 'yu', 'я' => 'ya', '▒' => 'A',
        );

        //$x = str_replace(array_keys($replace), $replace, $x);

        ////-- ELIMINA CARACTERES ESPECIAIS
        define( "ACENTOS" , "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŒØŠÙÚÛÜÝŽÞàáâãäåæçèéêëìíîïðñòóôõö&œøšùúûüýžþºª&.$" );
        define( "NORMAL"  , "AAAAAAACEEEEIIIIDNOOOOO   UUUUY  aaaaaaaceeeeiiii noooooE   uuuuy  ..E.$" );
        $x=strtr($x, ACENTOS, NORMAL);
        $x = urldecode($x);

        $novoX = '';
        for ($i = 0; $i < strlen($x); $i++) {
            $code = ord($x[$i]);
            if ($code == 192) { $novoX .= 'A'; continue; }
            if ($code == 193) { $novoX .= 'A'; continue; }
            if ($code == 194) { $novoX .= 'A'; continue; }
            if ($code == 195) { $novoX .= 'A'; continue; }
            if ($code == 196) { $novoX .= 'A'; continue; }
            if ($code == 197) { $novoX .= 'A'; continue; }
            if ($code == 198) { $novoX .= 'A'; continue; }
            if ($code == 199) { $novoX .= 'C'; continue; }
            if ($code == 200) { $novoX .= 'E'; continue; }
            if ($code == 201) { $novoX .= 'E'; continue; }
            if ($code == 202) { $novoX .= 'E'; continue; }
            if ($code == 203) { $novoX .= 'E'; continue; }
            if ($code == 204) { $novoX .= 'I'; continue; }
            if ($code == 205) { $novoX .= 'I'; continue; }
            if ($code == 206) { $novoX .= 'I'; continue; }
            if ($code == 207) { $novoX .= 'I'; continue; }
            if ($code == 210) { $novoX .= 'O'; continue; }
            if ($code == 211) { $novoX .= 'O'; continue; }
            if ($code == 212) { $novoX .= 'O'; continue; }
            if ($code == 213) { $novoX .= 'O'; continue; }
            if ($code == 214) { $novoX .= 'O'; continue; }
            if ($code == 217) { $novoX .= 'U'; continue; }
            if ($code == 218) { $novoX .= 'U'; continue; }
            if ($code == 219) { $novoX .= 'U'; continue; }
            if ($code == 220) { $novoX .= 'U'; continue; }
            if ($code == 224) { $novoX .= 'A'; continue; }
            if ($code == 225) { $novoX .= 'A'; continue; }
            if ($code == 226) { $novoX .= 'A'; continue; }
            if ($code == 227) { $novoX .= 'A'; continue; }
            if ($code == 228) { $novoX .= 'A'; continue; }
            if ($code == 229) { $novoX .= 'A'; continue; }
            if ($code == 231) { $novoX .= 'C'; continue; }
            if ($code == 232) { $novoX .= 'E'; continue; }
            if ($code == 233) { $novoX .= 'E'; continue; }
            if ($code == 234) { $novoX .= 'E'; continue; }
            if ($code == 235) { $novoX .= 'E'; continue; }
            if ($code == 236) { $novoX .= 'I'; continue; }
            if ($code == 237) { $novoX .= 'I'; continue; }
            if ($code == 238) { $novoX .= 'I'; continue; }
            if ($code == 239) { $novoX .= 'I'; continue; }
            if ($code == 242) { $novoX .= 'O'; continue; }
            if ($code == 243) { $novoX .= 'O'; continue; }
            if ($code == 244) { $novoX .= 'O'; continue; }
            if ($code == 245) { $novoX .= 'O'; continue; }
            if ($code == 246) { $novoX .= 'O'; continue; }
            if ($code == 249) { $novoX .= 'U'; continue; }
            if ($code == 250) { $novoX .= 'U'; continue; }
            if ($code == 251) { $novoX .= 'U'; continue; }
            if ($code == 252) { $novoX .= 'U'; continue; }
            $novoX .= $x[$i];
        }
        //return $novoX;
        $x = $novoX;

        /*
        $x = str_replace("<","&lt;",$x);
        $x = str_replace(">","&gt;",$x);          
        $x = str_replace("'","&#39;",$x);
        $x = str_replace('"',"&quot;",$x);
        */
        
        $cCharPermitido='&ABCDEFGHIJLKYMNOPQRSTUVXWZ 1234567890()[]{}.,+_-:/=%?;!@|$"';
        for ($n = 0;$n<strlen($x);$n++)
        {
             $c=substr($x,$n,1);
             if (strpos($cCharPermitido,strtoupper($c))!==FALSE)
             {
                $r.=$c;
             }
             else
             {
                $r.="";
             }
        }
        $x=$r;
        $x = str_replace("&","&amp;",$x);        
        return $x;
    }
}

?>
