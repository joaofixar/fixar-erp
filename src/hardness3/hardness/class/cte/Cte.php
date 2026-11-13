<?php

namespace hardness;

use InvalidArgumentException;
use stdClass;

require_once('logCTE.php');
class CTe 
{
    public $portal = 'http://www.portalfiscal.inf.br/cte';
    public Certificate $certificado;
    public $ambiente = 2; //homologação
    public $versao = '3.00';
    public $erroStatus = false; //estado de erro   
    public $erro = ""; //mensagem de erro 
    public $digestValue;
    public $xml;
    public $UF; // SC 
    public $urlServico;
    public $metodo;
    public $namespace;
    public $timeZone;
    public $codUF;
    public $cnpj;
    public $modelo = '57';
    public $retornoSEFAZ;    

    public function __construct(Certificate $certificado, $configuracoes = null)
    {
        $this->certificado = $certificado;

        if ($configuracoes != null) {
            $this->versao = $configuracoes['versao'];
            $this->ambiente = $configuracoes['ambiente'];
            $this->UF  = $configuracoes['UF'];
            $this->codUF = $configuracoes['codUF'];
            $this->cnpj = $configuracoes['cnpj'];
        }
    }

    /**
     * Assina o XML     
     * @param   string   $xml xml enviado para assinar
     * @return  string   xml assinado     
     **/
    public function assinarXML($xml)
    {
        // Pesquisar        
        $oAssinatura = new \DOMDocument();
        $oAssinatura->preservWhiteSpace = false;
        $oAssinatura->formatOutput = false;

        if (!$oAssinatura->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {            
            $this->adicionaErro("Não foi possivel abrir o arquivo carregar o arquivo xml");
        }

        //-- Carrega o node que sera assinado
        $infCte = $oAssinatura->getElementsByTagName('infCte')->item(0);
        $SignedInfo = $oAssinatura->getElementsByTagName('SignedInfo')->item(0);

        //-- Extrai o id do MDFe
        // $Id = trim(str_replace('CTe', '',$infCte->getAttribute("Id")));


        //////////////////////////////////////////////////////////////
        //-- DIGEST VALUE
        //////////////////////////////////////////////////////////////
        //-- Pega todos os dados contidos na TAG infNFe
        $cString = $infCte->C14N(false, false, null, null);

        //-- Codifica os dados extraidos da TAG infNFe
        $cDigestValue = base64_encode(hash('sha1', $cString, true));
        $this->digestValue = $cDigestValue;

        //-- Localiza o Node da tag Digest Value
        $DigestValue = $oAssinatura->getElementsByTagName('DigestValue')->item(0);

        //-- Salva o valor do digest value dentro da tag DigestValue
        $DigestValue->nodeValue = $cDigestValue;


        //////////////////////////////////////////////////////////////
        //-- ASSINATURA  (SIGNATURE VALUE)
        //////////////////////////////////////////////////////////////

        // extrai os dados a serem assinados para uma string
        $dados = $SignedInfo->C14N(FALSE, FALSE, NULL, NULL);
        $signature = '';

        //executa a assinatura digital usando o resource da chave privada    
        openssl_sign($dados, $signature, $this->certificado->privateKey);

        //codifica assinatura para o padrao base64
        $cSignatureValue = base64_encode($signature);

        $SignatureValue = $oAssinatura->getElementsByTagName('SignatureValue')->item(0);
        $SignatureValue->nodeValue = $cSignatureValue;

        //seta o valor do certificado
        $x509Certificate = $oAssinatura->getElementsByTagName('X509Certificate')->item(0);
        $x509Certificate->nodeValue = $this->certificado->certX509;
        
        $oAssinatura->normalize();
        $conteudo = $oAssinatura->saveXML();


        //-- Elimina os lixos do meio das tags            
        $conteudo = str_replace(array(chr(13), chr(10)), "", $conteudo);
        $this->xml = $conteudo;        
        return $this->xml;
    }

    /**    
     * Envia lote de CT-e (Conhecimento de Transporte)
     * @param   array $xmls XMLs a ser transmitido
     * @param   string $idLote
     * @return  mixed retorna um stdClass ou seta o errorStatus
     * @access  public
     **/
    public function enviaCTeLote($xmls, $idLote = '1')
    {
        
        // Verifica se é um array
        if (!is_array($xmls)) {
            throw new InvalidArgumentException('Argumento xmls: Deve ser informado um array');            
        }

        //O lote tem um limite de até 50 CT-e
        if (count($xmls) > 50) {            
            return $this->adicionaErro('Existe um limite de até 50 (cinquenta) CT-e por lote.');            
        }

        $this->servico = 'CteRecepcao';
        $this->metodo = 'cteRecepcaoLote';

        // remove a primeira linha (<?xml version="1.0")         
        $xmls = array_reduce($xmls, function ($carry, $xml) {
            $carry .= preg_replace("/<\?xml.*?\?>/", "", $xml);
            return $carry;
        }, '');


        if(strlen($xmls) > 482000) {
            return $this->adicionaErro("214 Rejeição: Tamanho da mensagem excedeu o limite estabelecido");            
        }                

        //Configura as informações do serviço                
        $this->setFunctionWDSL($this->servico, $this->UF, $this->ambiente);

        //Monta a mensagem
        $mensagem = '<enviCTe xmlns="' . $this->portal . '" versao="' . $this->versao . '"><idLote>' . $idLote . '</idLote>' . $xmls . '</enviCTe>';
        $header = '<cteCabecMsg xmlns="' . $this->namespace . '"><cUF>' . $this->codUF . '</cUF><versaoDados>' . $this->versao . '</versaoDados></cteCabecMsg>';
        $body = '<cteDadosMsg xmlns="' . $this->namespace . '">' . $mensagem .  '</cteDadosMsg>';

        //Monta a url do serviço que sera consumido
        $urlAction = "{$this->namespace}/{$this->servico}";

        $this->retornoSEFAZ = $this->__sendSOAP2($this->urlServico, $this->namespace, $header, $body, $urlAction, $this->ambiente);

        //Converte resposta
        if (!empty($this->retornoSEFAZ)) {
            //tratar dados de retorno
            $doc = new \DOMDocument();
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($this->retornoSEFAZ, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            $retorno = new stdClass;
            // status do recebimento ou mensagem de erro
            $retorno->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            //Versão do Aplicativo que recebeu o Lote
            $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            //Descrição literal do status da resposta
            $retorno->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;

            // em caso de sucesso  cStat = 103
            if ($retorno->cStat == '103') {
                // tempo medio de processamento
                $retorno->tMed = $doc->getElementsByTagName('tMed')->item(0)->nodeValue;
                // data e hora da mensagem
                $retorno->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                // numero do recibo, se o lote foi aceito
                // guardar o numero do recibo na base de dados pois devera ser usado
                // para verificar o status dos Lotes enviados
                $retorno->nRec = $doc->getElementsByTagName('nRec')->item(0)->nodeValue;                
            }
        
            return $retorno;
        } else {
            $this->adicionaErro("EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS");
        }
    }

    /**    
     *  IGNORAR ESTE METODO SERA UTILIZADO APENAS O ENVIO EM LOTE
     * Envia o CT-e (Conhecimento de Transporte) sincrono
     * @param   array $xml XML a ser transmitido     
     * @return  mixed retorna um stdClass ou seta o errorStatus
     * @access  public
     **/
    public function enviaCTe($xml)
    {

        if($this->versao == '3.00'){
            //serviço
            $this->servico = 'CteRecepcao';
            //metodo
            $this->metodo = 'cteRecepcao';

        }elseif($this->versao == '4.00'){
            if($this->UF == 'PR'){
                //serviço
                // 08/03/2024 - PR ainda está usando o serviço da versão 3.00 mesmo autorizando na versao 4.00
                $this->servico = 'CTeRecepcao';
            } else {
                //serviço
                $this->servico = 'CTeRecepcaoSincV4';
            }

            //metodo
            $this->metodo = 'cteRecepcao';
        }            

        $xml = str_replace('<?xml version="1.0" encoding="UTF-8"?'.'>','',$xml);

        if(strlen($xml) > 482000) {
            return $this->adicionaErro("214 Rejeição: Tamanho da mensagem excedeu o limite estabelecido");            
        }   

        $this->setFunctionWDSL($this->servico, $this->UF, $this->ambiente);

        //Compacta a mensagem        
        // $zip = base64_encode(gzencode($mensagem, 9, FORCE_GZIP));

        //Monta a mensagem
        if($this->versao == '3.00'){

            $mensagem = '<enviCTe xmlns="' . $this->portal . '" versao="' . $this->versao . '"><idLote>1</idLote>' . $xml . '</enviCTe>';
            $header = '<cteCabecMsg xmlns="' . $this->namespace . '"><cUF>' . $this->codUF . '</cUF><versaoDados>' . $this->versao . '</versaoDados></cteCabecMsg>';
            $body = '<cteDadosMsg xmlns="' . $this->namespace . '">' . $mensagem .  '</cteDadosMsg>';

        }elseif($this->versao == '4.00'){

            if($this->UF == 'PR'){
                $zip      = gzencode($xml, 9);
            } else {
                $zip      = gzencode($xml, 9, FORCE_DEFLATE);

            }
            $xmlBase64zip = base64_encode($zip);

            //versao 4.00 não envia mais header para comunicação com o soap  
            $header = '';
            $body         = '<cteDadosMsg xmlns="' . $this->namespace . '">' . $xmlBase64zip .  '</cteDadosMsg>';
        }

        //$urlAction = "{$this->namespace}/{$this->servico}";
        $urlAction = "{$this->namespace}/{$this->metodo}";
        
        $this->retornoSEFAZ = $this->__sendSOAP2($this->urlServico, $this->namespace, $header, $body, $urlAction, $this->ambiente);

        //Converte resposta
        if (!empty($this->retornoSEFAZ)) {            
            //tratar dados de retorno
            $doc = new \DOMDocument();
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($this->retornoSEFAZ, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);


            $retorno = new stdClass;
            // status do recebimento ou mensagem de erro
            $retorno->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $retorno->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            
            // em caso de sucesso  cStat = 103
            if ($retorno->cStat == '103') {
                // tempo medio de processamento
                $retorno->tMed = $doc->getElementsByTagName('tMed')->item(0)->nodeValue;
                // data e hora da mensagem
                $retorno->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                // numero do recibo, se o lote foi aceito
                // guardar o numero do recibo na base de dados pois devera ser usado
                // para verificar o estatus dos Lotes enviados
                $retorno->nRec = $doc->getElementsByTagName('nRec')->item(0)->nodeValue;
            }
          
            return $retorno;
        } else {
            $this->adicionaErro("EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _");            
        }
    }

    /*Observação : O aplicativo do Contribuinte deve ser construído de forma a aguardar um tempo mínimo de 15 segundos entre o envio do lote de CT-e para processamento e a consulta do resultado deste
        processamento, evitando a obtenção desnecessária do status de erro 105 – “Lote em Processamento”.  */

    /**    
     * consultaRecibo
     * Consulta o Recibo do Lote que foi enviado para processamento
     * @param   string $recibo Recibo a ser consultado
     * @param   string $ambiente Ambiente que esta sendo consultado
     * @return  mixed retorna um stdClass ou seta o errorStatus
     * @access  public
     **/
    public function consultaRecibo($recibo, $ambiente = 2)
    {
        //Configura informações do serviço 
        $this->servico = 'CteRetRecepcao';
        $this->metodo = 'cteRetRecepcao';

        $this->setFunctionWDSL($this->servico, $this->UF, $this->ambiente);

        //Monta a mensagem
        $mensagem = "<consReciCTe xmlns=\"$this->portal\" versao=\"$this->versao\"><tpAmb>$ambiente</tpAmb><nRec>{$recibo}</nRec></consReciCTe>";
        $header = '<cteCabecMsg xmlns="' . $this->namespace . '"><cUF>' . $this->codUF . '</cUF><versaoDados>' . $this->versao . '</versaoDados></cteCabecMsg>';
        $body = '<cteDadosMsg xmlns="' . $this->namespace . '">' . $mensagem .  '</cteDadosMsg>';

        $urlAction = "{$this->namespace}/{$this->servico}";

        $this->retornoSEFAZ = $this->__sendSOAP2($this->urlServico, $this->namespace, $header, $body, $urlAction, $this->ambiente);
        

        //Converte resposta
        if (!empty($this->retornoSEFAZ)) {
            //tratar dados de retorno
            $doc = new \DOMDocument();
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($this->retornoSEFAZ, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            $retorno = new stdClass;
            // status do recebimento ou mensagem de erro
            $retorno->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $retorno->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            $retorno->nRec = $doc->getElementsByTagName('nRec')->item(0)->nodeValue;

            // Lote processado cStat = 104
            if ($retorno->cStat == '104') {
                $retorno->protCTe = array();
                foreach ($doc->getElementsByTagName('protCTe') as $node) {
                    $protCTe = new stdClass;
                    $protCTe->tpAmb =       $node->getElementsByTagName('tpAmb')->item(0)->nodeValue;
                    $protCTe->verAplic =    $node->getElementsByTagName('verAplic')->item(0)->nodeValue;
                    $protCTe->chCTe =       $node->getElementsByTagName('chCTe')->item(0)->nodeValue;
                    $protCTe->dhRecbto =    $node->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                    $protCTe->nProt =       $node->getElementsByTagName('nProt')->item(0)->nodeValue;
                    $protCTe->digVal =      $node->getElementsByTagName('digVal')->item(0)->nodeValue;
                    $protCTe->cStat =       $node->getElementsByTagName('cStat')->item(0)->nodeValue;
                    $protCTe->xMotivo =     $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
                    
                    $retorno->protCTe[$protCTe->chCTe] = $protCTe;
                }
            }
            
            return $retorno;
        } else {
            $this->adicionaErro("EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _");
        }
    }

    /**    
     * consultaStatusSEFAZ
     * Consulta o status atual da SEFAZ informada
     * @param   string $recibo Recibo a ser consultado
     * @param   string $ambiente Ambiente que esta sendo consultado
     * @return  mixed retorna um stdClass ou seta o errorStatus
     * @access  public
     **/
    public function consultaStatusSEFAZ($uf, $codUF)
    {
        $this->codUF = $codUF;

        //Configura informações do serviço            
        if($this->versao == '3.00'){
            $this->servico = 'CteStatusServico';
            $this->metodo  = 'cteStatusServicoCT';
        } elseif ($this->versao == '4.00'){
            $this->servico = 'CTeStatusServicoV4';
            $this->metodo  = 'cteStatusServicoCT';
        }

        $this->setFunctionWDSL($this->servico, $uf, $this->ambiente);

        if($this->versao == '3.00'){
            //Monta a mensagem
            $mensagem = "<consStatServCte xmlns=\"{$this->portal}\" versao=\"{$this->versao}\"><tpAmb>{$this->ambiente}</tpAmb><xServ>STATUS</xServ></consStatServCte>";
            $header = '<cteCabecMsg xmlns="' . $this->namespace . '"><cUF>' . $this->codUF . '</cUF><versaoDados>' . $this->versao . '</versaoDados></cteCabecMsg>';
            $body = "<cteDadosMsg xmlns=\"{$this->namespace}\">$mensagem</cteDadosMsg>";
            
        } elseif($this->versao == '4.00'){

            $header = '';
            $mensagem = "<consStatServCTe xmlns=\"{$this->portal}\" versao=\"{$this->versao}\"><tpAmb>{$this->ambiente}</tpAmb><cUF>{$this->codUF}</cUF><xServ>STATUS</xServ></consStatServCTe>";
            $body = "<cteDadosMsg xmlns=\"{$this->namespace}\">$mensagem</cteDadosMsg>";
        }

        $urlAction = "{$this->namespace}/{$this->metodo}";

        $this->retornoSEFAZ = $this->__sendSOAP2($this->urlServico, $this->namespace, $header, $body, $urlAction, $this->ambiente);

        //Converte resposta
        if (!empty($this->retornoSEFAZ)) {
            //tratar dados de retorno
            $doc = new \DOMDocument();
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($this->retornoSEFAZ, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            $retorno = new stdClass;
            // status do recebimento ou mensagem de erro
            $retorno->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $retorno->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            $retorno->tMed = $doc->getElementsByTagName('tMed')->item(0)->nodeValue;
            $retorno->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
            $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $retorno->cUF = $doc->getElementsByTagName('cUF')->item(0)->nodeValue;
                                  
            return $retorno;
        } else {

            $this->adicionaErro("EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _");
        }
    }

    /**    
     * consultaChave
     * Consulta a chave de acesso
     * @param   string $chave chave a ser consultada     
     * @return  mixed retorna um stdClass ou seta o errorStatus
     * @access  public
     **/
    public function consultaChave($chave)
    {
        // A consulta é a fonte de verdade para recuperar uma autorização que
        // chegou à SEFAZ, mas cuja resposta não foi persistida localmente.
        // No leiaute 4.00 o serviço mudou de nome e não usa SOAP Header.
        if ($this->versao == '4.00') {
            $this->servico = 'CTeConsultaV4';
            $this->metodo = 'cteConsultaCT';
        } else {
            $this->servico = 'CteConsulta';
            $this->metodo = 'cteConsultaCT';
        }

        $this->setFunctionWDSL($this->servico, $this->UF, $this->ambiente);
        // Monta a mensagem
        $mensagem = "<consSitCTe xmlns=\"{$this->portal}\" versao=\"{$this->versao}\"><tpAmb>$this->ambiente</tpAmb><xServ>CONSULTAR</xServ><chCTe>$chave</chCTe></consSitCTe>";
        $header = $this->versao == '4.00'
            ? ''
            : '<cteCabecMsg xmlns="' . $this->namespace . '"><cUF>' . $this->codUF . '</cUF><versaoDados>' . $this->versao . '</versaoDados></cteCabecMsg>';
        $body = "<cteDadosMsg xmlns=\"{$this->namespace}\">$mensagem</cteDadosMsg>";

        $urlAction = "{$this->namespace}/{$this->metodo}";

        $this->retornoSEFAZ = $this->__sendSOAP2($this->urlServico, $this->namespace, $header, $body, $urlAction, $this->ambiente);

        //Converte resposta
        if (!empty($this->retornoSEFAZ)) {
            //tratar dados de retorno
            $doc = new \DOMDocument();
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($this->retornoSEFAZ, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);


            $retorno = new stdClass;
            $retorno->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $retorno->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;

            // A situação do protocolo deve ser validada separadamente da
            // situação da consulta. Só ela confirma a autorização (100).
            $infProt = $doc->getElementsByTagName('infProt')->item(0);
            if ($infProt) {
                $retorno->chCTe = $infProt->getElementsByTagName('chCTe')->item(0)->nodeValue;
                $retorno->dhRecbto = $infProt->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                $retorno->nProt = $infProt->getElementsByTagName('nProt')->item(0)->nodeValue;
                $retorno->protCStat = $infProt->getElementsByTagName('cStat')->item(0)->nodeValue;
            }
        
            return $retorno;
        } else {
            $this->adicionaErro("EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _");
        }
    }

     /**    
     * cancelarCTe
     * Realiza o evento de cancelamento 
     * @param   string $chave chave do conhecimento     
     * @param   string $protocolo protocolo do conhecimento     
     * @param   string $justificativa a justificativa do cancelamento
     * @return  mixed retorna um stdClass ou seta o errorStatus
     * @access  public
     **/
    public function cancelarCTe($chave, $protocolo, $justificativa)
    {
        $tpEvento   = '110111';

        $parteEspecifica  = "<evCancCTe>";
        $parteEspecifica .= "<descEvento>Cancelamento</descEvento>";
        $parteEspecifica .= "<nProt>$protocolo</nProt>";
        $parteEspecifica .= "<xJust>$justificativa</xJust>";
        $parteEspecifica .= "</evCancCTe>";


        return $this->recepcaoEvento($tpEvento, $parteEspecifica, $chave);
    }

    public function manifestarDesacordo($chave, $justificativa) 
    {
        $tpEvento = '610110';

        $justificativa = $this->removeCaracteresEspeciais($justificativa);

        $parteEspecifica = "<evPrestDesacordo>". "<descEvento>Prestacao do Servico em Desacordo</descEvento>"
                        . "<indDesacordoOper>1</indDesacordoOper>"
                        . "<xObs>{$justificativa}</xObs>"
                        . "</evPrestDesacordo>";

        return $this->recepcaoEvento($tpEvento, $parteEspecifica, $chave);
    }

    /**    
     * inutilizarCTe
     * Inutiliza a numeração especificada
     * @param   int     $numIni Numero CT-e Inicio
     * @param   int     $numFim Numero CT-e Final
     * @param   string  $justificativa Justificativa da inutilizacao
     * @param   string  $Cteserie serie do CT-e
     * @param   string  $modelo  CT-e ou CT-e OS  
     * @return  mixed retorna um stdClass ou seta o errorStatus
     * @access  public
     **/
    public function inutilizarCTe($numIni, $numFim, $justificativa, $Cteserie, $modelo = '')
    {
        if ($modelo == '') {
            $modelo = $this->modelo;
        }

        //Configura informações do serviço   
        $this->servico = 'CteInutilizacao';
        $this->metodo = 'cteInutilizacaoCT';
        $this->setFunctionWDSL($this->servico, $this->UF, $this->ambiente);


        $serie = str_pad($Cteserie, 3, '0', STR_PAD_LEFT);
        $ano = date('y');
        $inicio = str_pad($numIni, 9, '0', STR_PAD_LEFT);
        $final = str_pad($numFim, 9, '0', STR_PAD_LEFT);

        $id_inutilizacao = "ID" . $this->codUF . $this->cnpj . $modelo . $serie . $inicio . $final;

        $mensagem = "<inutCTe xmlns=\"{$this->portal}\" versao=\"{$this->versao}\">" .
            "<infInut Id=\"$id_inutilizacao\">" .
            "<tpAmb>{$this->ambiente}</tpAmb>" .
            "<xServ>INUTILIZAR</xServ>" .
            "<cUF>{$this->codUF}</cUF>" .
            "<ano>{$ano}</ano>" .
            "<CNPJ>{$this->cnpj}</CNPJ>" .
            "<mod>{$modelo}</mod>" .
            "<serie>{$Cteserie}</serie>" .
            "<nCTIni>{$numIni}</nCTIni>" .
            "<nCTFin>{$numFim}</nCTFin>" .
            "<xJust>{$justificativa}</xJust>" .
            "</infInut></inutCTe>";

        //assina a mensagem
        $mensagemAssinada = $this->assinaPHPCTe($mensagem, 'infInut', 'inutCTe');
        //remove declaracao xml
        $mensagemAssinada = preg_replace("/<\?xml.*?\?>/", "", $mensagemAssinada);

        $header = '<cteCabecMsg xmlns="' . $this->namespace . '"><cUF>' . $this->codUF . '</cUF><versaoDados>' . $this->versao . '</versaoDados></cteCabecMsg>';
        $body = "<cteDadosMsg xmlns=\"{$this->namespace}\">$mensagemAssinada</cteDadosMsg>";

        $urlAction = "{$this->namespace}/{$this->servico}";
        $this->retornoSEFAZ = $this->__sendSOAP2($this->urlServico, $this->namespace, $header, $body, $urlAction, $this->ambiente);

        //Converte resposta
        if (!empty($this->retornoSEFAZ)) {
            //tratar dados de retorno
            $doc = new \DOMDocument();
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($this->retornoSEFAZ, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);


            $retorno = new stdClass;
            $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $retorno->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $retorno->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;

            // em caso de sucesso  cStat = 102
            if ($retorno->cStat == '102') {
                //data e hora da mensagem
                $retorno->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                //Número do Protocolo de Inutilização
                $retorno->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;
            }
            return $retorno;
        } else {
            $this->adicionaErro("EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _");
        }
    }

    /**    
     * cartaCorrecaoCTe
     * Carta de Correção
     * @param   string  $chave Chave de acesso do CT-e
     * @param   array   $correcoes
     * @param   int     $nSeqEvento caso haja mais de uma ocorrencia para o mesmo evento vinculado ao CT-e
     * @return  mixed retorna um stdClass ou seta o errorStatus
     * @access  public
     **/
    public function cartaCorrecaoCTe($chave, $correcoes,$nSeqEvento = '1')
    {
        $tpEvento   = '110110';

        $scorrecoes = '';
        foreach ($correcoes as $correcao) {
            $scorrecoes .= "<infCorrecao><grupoAlterado>{$correcao['grupoAlterado']}</grupoAlterado>"
                . "<campoAlterado>{$correcao['campoAlterado']}</campoAlterado>"
                . "<valorAlterado>{$correcao['valorAlterado']}</valorAlterado></infCorrecao>";            
        }

        $parteEspecifica  = "<evCCeCTe>";
        $parteEspecifica  .= "<descEvento>Carta de Correcao</descEvento>";
        $parteEspecifica  .= $scorrecoes;
        $parteEspecifica  .= "<xCondUso>A Carta de Correcao e disciplinada pelo Art. 58-B do CONVENIO/SINIEF 06/89: Fica permitida a utilizacao de carta de correcao, para regularizacao de erro ocorrido na emissao de documentos fiscais relativos a prestacao de servico de transporte, desde que o erro nao esteja relacionado com: I - as variaveis que determinam o valor do imposto tais como: base de calculo, aliquota, diferenca de preco, quantidade, valor da prestacao;II - a correcao de dados cadastrais que implique mudanca do emitente, tomador, remetente ou do destinatario;III - a data de emissao ou de saida.</xCondUso>";
        $parteEspecifica  .= "</evCCeCTe>";


        return $this->recepcaoEvento($tpEvento, $parteEspecifica, $chave, $nSeqEvento);
    }

    protected function recepcaoEvento($tpEvento, $parteEspecifica, $chave, $nSeqEvento = '1')
    {
        //Configura informações so serviço        
        if($this->versao == '3.00'){
            $this->servico = 'CteRecepcaoEvento';        
            $this->metodo = 'cteRecepcaoEvento';

            // Identificador da TAG a ser assinada, a regra de formação do Id é:“ID” + tpEvento+ chave do CT-e+ nSeqEvento 
            // Versao 3.00 é sequencial 2
            $zenSeqEvento = str_pad($nSeqEvento, 2, "0", STR_PAD_LEFT);

        } elseif($this->versao == '4.00'){
            
            if($this->UF == 'PR'){
                // 08/03/2024 - PR ainda está usando o serviço da versão 3.00 mesmo autorizando na versao 4.00
                 $this->servico = 'CteRecepcaoEvento';      
            } else {
                $this->servico = 'CTeRecepcaoEventoV4';        
            }
            $this->metodo = 'cteRecepcaoEvento';

            // Identificador da TAG a ser assinada, a regra de formação do Id é:“ID” + tpEvento+ chave do CT-e+ nSeqEvento 
            // Versao 4.00 é sequencial 3
            $zenSeqEvento = str_pad($nSeqEvento, 3, "0", STR_PAD_LEFT);
        }

        $this->setFunctionWDSL($this->servico, $this->UF, $this->ambiente);
        //$urlAction = "{$this->namespace}/{$this->servico}";
        $urlAction = "{$this->namespace}/{$this->metodo}";

        // Data e hora do evento no formato AAAA-MM-DDTHH:MM:SSTZD (UTC)        
        $dhEvento   = date('Y-m-d') . 'T' . date('H:i:s') . $this->getTimeZone();

        // Código da UF que registrou o Evento.
        $cOrgao     = $this->codUF;   

        $id_evento = "ID" . $tpEvento . $chave . $zenSeqEvento;

        $Ev  = '';
        $Ev .= "<eventoCTe xmlns=\"{$this->portal}\" versao=\"$this->versao\" >";
        $Ev .= "<infEvento Id=\"$id_evento\">";
        $Ev .= "<cOrgao>$cOrgao</cOrgao>";
        $Ev .= "<tpAmb>" . $this->ambiente . "</tpAmb>";
        $Ev .= "<CNPJ>" . $this->cnpj . "</CNPJ>";
        $Ev .= "<chCTe>$chave</chCTe>";
        $Ev .= "<dhEvento>$dhEvento</dhEvento>";
        $Ev .= "<tpEvento>$tpEvento</tpEvento>";
        $Ev .= "<nSeqEvento>$nSeqEvento</nSeqEvento>";
        $Ev .= "<detEvento versaoEvento=\"{$this->versao}\">";
        $Ev .= $parteEspecifica;
        $Ev .= "</detEvento></infEvento></eventoCTe>";

        // Assina o XML do evento
        $mensagem = $this->assinaPHPCTe($Ev, 'infEvento', 'eventoCTe');
        $mensagem =  preg_replace("/<\?xml.*?\?>/", "", $mensagem);
        $mensagem = str_replace(array("\r", "\n", "\s"), "", $mensagem);

        $header = '<cteCabecMsg xmlns="' . $this->namespace . '"><cUF>' . $this->codUF . '</cUF><versaoDados>' . $this->versao . '</versaoDados></cteCabecMsg>';
        $body = "<cteDadosMsg xmlns=\"{$this->namespace}\">$mensagem</cteDadosMsg>";

        // // Envia o arquivo....
        $this->retornoSEFAZ = $this->__sendSOAP2($this->urlServico, $this->namespace, $header, $body, $urlAction, $this->ambiente);
        
        //Converte resposta
        if (!empty($this->retornoSEFAZ)) {
            //tratar dados de retorno
            $doc = new \DOMDocument();
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($this->retornoSEFAZ, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);


            $retorno = new stdClass;            
            $retorno->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $retorno->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;            
            $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $retorno->cOrgao = $doc->getElementsByTagName('cOrgao')->item(0)->nodeValue;

            // em caso de sucesso  cStat = 135
            if ($retorno->cStat == '135') {
                                
                $retorno->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;
                $retorno->dhRegEvento = $doc->getElementsByTagName('dhRegEvento')->item(0)->nodeValue;
                $retorno->nSeqEvento = $doc->getElementsByTagName('nSeqEvento')->item(0)->nodeValue;
            }

            return $retorno;
        } else {
            $this->adicionaErro("EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _");
        }
    }


    /**
     * Assinador TOTALMENTE baseado em PHP das NFe
     * este assinador somente utiliza comandos nativos do PHP para assinar
     * FUNCIONAL !!!! resulta em arquvo identico ao assinadorRS
     *
     * @param   string $Cte
     * @param   string $tagid TAG que devera ser assinada
     * @return  string XML assinada
     * @access  public
     **/
    protected function assinaPHPCTe($Cte, $tagid, $taginicio)
    {

        // certificado que sera incluso no xml
        $certX509 = $this->certificado->certX509;
        // limpeza do xml com a retirada dos CR e LF
        $order = array("\r\n", "\n", "\r");
        $replace = '';
        $Cte = str_replace($order, $replace, $Cte);
        // carrega o documento no DOM
        $xmldoc = new \DOMDocument();
        $xmldoc->preservWhiteSpace = FALSE; //elimina espaÃ§os em branco
        $xmldoc->formatOutput = FALSE;
        // muito importante deixar ativadas as opÃ§oes para limpar os espacos em branco
        // e as tags vazias
        $xmldoc->loadXML("$Cte", LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        //extrair a tag com os dados a serem assinados
        $infCTe = $xmldoc->getElementsByTagName($tagid)->item(0);
        $id = trim($infCTe->getAttribute("Id"));

        //extrai os dados da tag para uma string
        $dados = $infCTe->C14N(FALSE, FALSE, NULL, NULL);
        //calcular o hash dos dados
        $hashValue = hash('sha1', $dados, TRUE);
        //converte o valor para base64 para serem colocados no xml
        $digValue = base64_encode($hashValue);
        //monta a tag da assinatura digital
        $templsign = '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /><SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1" /><Reference URI="#' . $id . '"><Transforms><Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" /><Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /></Transforms><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" /><DigestValue>' . $digValue . '</DigestValue></Reference></SignedInfo><SignatureValue></SignatureValue><KeyInfo><X509Data><X509Certificate>' . $certX509 . '</X509Certificate></X509Data></KeyInfo></Signature>';
        //salva o xml normalizado
        $Cte = $xmldoc->saveXML();
        //remove a tag de fechamento final da MDFe </MDFe>
        $Cte = str_replace("</$taginicio>", "", $Cte);
        //acrescenta o template da assinatura
        $Cte = $Cte . $templsign . "</$taginicio>";
        // limpa algumas variaveis da memoria
        unset($xmldoc);
        unset($dados);
        unset($hashValue);
        unset($digValue);
        unset($id);
        unset($infCTe);
        unset($order);
        unset($replace);
        unset($templsign);
        unset($data);

        //carrega novamente o DOM com a MDFe
        $xmldoc = new \DOMDocument();
        $xmldoc->preservWhiteSpace = FALSE; //elimina espaÃ§os em branco
        $xmldoc->formatOutput = FALSE;

        $xmldoc->loadXML($Cte, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        //obtem a tag com as informaÃ§oes da assinatura
        // esta informaÃ§oes e que serao assinadas
        $SignedInfo = $xmldoc->getElementsByTagName('SignedInfo')->item(0);
        // extrai os dados a serem assinados para uma string
        $dados = $SignedInfo->C14N(FALSE, FALSE, NULL, NULL);
        $signature = '';
        //executa a assinatura digital usando o resource da chave privada
        $resp = openssl_sign($dados, $signature, $this->certificado->privateKey);
        //codifica assinatura para o padrao base64
        $signatureValue = base64_encode($signature);
        $SignValue = $xmldoc->getElementsByTagName('SignatureValue')->item(0);
        $SignValue->nodeValue = $signatureValue;
        $Cte = $xmldoc->saveXML();

        return $Cte;
    }

    /**
     * Adiciona o protocolo retornado pelo SEFAZ ao XML     
     * @param   string $xmlAssinado
     * @param   string $retorno resposta do SEFAZ
     * @return  string XML com o protocolo
     * @access  public
     **/
    public function adicionaProtocolo($xmlAssinado,$retorno,$chave = '') {
        $ret = new \DOMDocument('1.0', 'UTF-8');
        $ret->preserveWhiteSpace = false;
        $ret->formatOutput = false;
        $ret->loadXML($retorno);
        $infProt = null;
        
        //limpa xml
        $xmlAssinado = preg_replace("/<\?xml.*?\?>/", "", $xmlAssinado);

        if(empty($chave)) {
            $retProt = $ret->getElementsByTagName('protCTe')->item(0) ? $ret->getElementsByTagName('protCTe')->item(0) : '';
            $infProt = $ret->getElementsByTagName('infProt')->item(0);
        } else {
            $retProt = $ret->getElementsByTagName('protCTe');        
            foreach($retProt as $prot) {
                $chCTe = $prot->getElementsByTagName('chCTe')->item(0);
                if($chCTe->nodeValue == $chave) {
                    $infProt = $chCTe->parentNode;
                    break;
                }
            }
        }
        
        // Verifica se é nulo 
        if(empty($retProt))  {            
            $this->adicionaErro("Erro: Não ha nenhuma tag protCTe para vincular ao XML");                  
            return;
        }        
                         
        $xml  = '';
        $xml .= "<cteProc  xmlns=\"{$this->portal}\" versao=\"{$this->versao}\" >";
        $xml .= $xmlAssinado;
        $xml .= "<protCTe xmlns=\"{$this->portal}\" versao=\"{$this->versao}\" >";
        $xml .= $ret->saveXML($infProt);
        $xml .= "</protCTe>";
        $xml .= "</cteProc>";
        return $xml;
    }


    public function distribuicaoDFe($ultimoNSU,$numNSU = 0,$fonte = "AN") {
        //serviço
        $this->servico = 'CTeDistribuicaoDFe';
        //metodo
        $this->metodo = 'cteDistDFeInteresse';
        
        $this->setFunctionWDSL($this->servico, $fonte, $this->ambiente);

        $ultimoNSU = str_pad($ultimoNSU, 15, '0', STR_PAD_LEFT);
        $tagNSU = "<distNSU><ultNSU>$ultimoNSU</ultNSU></distNSU>";
        if ($numNSU != 0) {
            $numNSU = str_pad($numNSU, 15, '0', STR_PAD_LEFT);
            $tagNSU = "<consNSU><NSU>$numNSU</NSU></consNSU>";
        }

        $mensagem = "<distDFeInt xmlns=\"{$this->portal}\" versao=\"1.00\">"
                    . "<tpAmb>".$this->ambiente."</tpAmb>"
                    . "<cUFAutor>".$this->codUF."</cUFAutor>"        
                    . "<CNPJ>".$this->cnpj."</CNPJ>" 
                    . $tagNSU
                    ."</distDFeInt>";
                
        $header = "";
        $consulta = '<cteDadosMsg>' . $mensagem .  '</cteDadosMsg>';
        $body =  "<cteDistDFeInteresse xmlns=\"{$this->namespace}\">" . $consulta . "</cteDistDFeInteresse>";
        $urlAction = "{$this->namespace}/{$this->servico}";
 
        $this->retornoSEFAZ =  $this->__sendSOAP2($this->urlServico, $this->namespace, $header, $body, $urlAction, $this->ambiente);                                
        return $this->retornoSEFAZ;
    }

    /**
     * __sendSOAP2
     * Função alternativa para estabelecer comunicaçao com servidor SOAP 1.2 da SEFAZ,
     * usando as chaves publica e privada parametrizadas na contrução da classe.
     * Conforme Manual de Integração Versão 4.0.1 Utilizando cURL e não o SOAP nativo
     *
     * @name __sendSOAP2
     * @version 2.14
     * @package NFePHP
     * @author Roberto L. Machado <linux.rlm at gmail dot com>
     * @author Jorge Luiz Rodrigues Tomé <jlrodriguestome at hotmail dot com>
     * @param string $urlsefaz
     * @param string $namespace
     * @param string $cabecalho
     * @param string $dados
     * @param string $metodo
     * @param numeric $ambiente
     * @param string $UF sem uso mantido apenas para compatibilidade com __sendSOAP
     * @return mixed false se houve falha ou o retorno em xml do SEFAZ
     */
    protected function __sendSOAP2($urlsefaz, $namespace, $cabecalho, $dados, $metodo, $ambiente, $UF = '')
    {
        if ($urlsefaz == '') {
            //não houve retorno
            $this->errMsg = 'URL do webservice não disponível.';
            $this->errStatus = true;
        }
        logCTE::logArquivo("\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("* __sendSOAP2 - Inicio\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("\n");

        // inicio da ?requisição ou retorno? padrão SOAP
        if($this->versao == '3.00'){

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
        
        } elseif($this->versao == '4.00'){

            $data = '';
            $data .= '<?xml version="1.0" encoding="utf-8"?>';
            $data .= '<soap12:Envelope ';
            $data .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
            $data .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
            $data .= 'xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">';
            /**$data .= '<soap12:Header>'; Versão 4.00 não envia mais o header
            $data .= $cabecalho;
            $data .= '</soap12:Header>';**/
            $data .= '<soap12:Body>';
            $data .= $dados;
            $data .= '</soap12:Body>';
            $data .= '</soap12:Envelope>'; 
        }
       

        logCTE::logArquivo("Mensagem SOAP");
        logCTE::logArquivo($data,true);
        logCTE::logArquivo("------------------------------\n\n");        
        //[Informational 1xx]
        $cCode['100'] = "Continue";
        $cCode['101'] = "Switching Protocols";
        //[Successful 2xx]
        $cCode['200'] = "OK";
        $cCode['201'] = "Created";
        $cCode['202'] = "Accepted";
        $cCode['203'] = "Non-Authoritative Information";
        $cCode['204'] = "No Content";
        $cCode['205'] = "Reset Content";
        $cCode['206'] = "Partial Content";
        //[Redirection 3xx]
        $cCode['300'] = "Multiple Choices";
        $cCode['301'] = "Moved Permanently";
        $cCode['302'] = "Found";
        $cCode['303'] = "See Other";
        $cCode['304'] = "Not Modified";
        $cCode['305'] = "Use Proxy";
        $cCode['306'] = "(Unused)";
        $cCode['307'] = "Temporary Redirect";
        //[Client Error 4xx]
        $cCode['400'] = "Bad Request";
        $cCode['401'] = "Unauthorized";
        $cCode['402'] = "Payment Required";
        $cCode['403'] = "Forbidden";
        $cCode['404'] = "Not Found";
        $cCode['405'] = "Method Not Allowed";
        $cCode['406'] = "Not Acceptable";
        $cCode['407'] = "Proxy Authentication Required";
        $cCode['408'] = "Request Timeout";
        $cCode['409'] = "Conflict";
        $cCode['410'] = "Gone";
        $cCode['411'] = "Length Required";
        $cCode['412'] = "Precondition Failed";
        $cCode['413'] = "Request Entity Too Large";
        $cCode['414'] = "Request-URI Too Long";
        $cCode['415'] = "Unsupported Media Type";
        $cCode['416'] = "Requested Range Not Satisfiable";
        $cCode['417'] = "Expectation Failed";
        //[Server Error 5xx]
        $cCode['500'] = "Internal Server Error";
        $cCode['501'] = "Not Implemented";
        $cCode['502'] = "Bad Gateway";
        $cCode['503'] = "Service Unavailable";
        $cCode['504'] = "Gateway Timeout";
        $cCode['505'] = "HTTP Version Not Supported";
        $tamanho = strlen($data);

        // cria um cabeçalho personalizado
        // Em SOAP 1.2 a ação informada no Content-Type deve identificar a
        // operação. A SEFAZ/PR rejeita a consulta CTeConsultaV4 quando recebe
        // apenas o namespace, respondendo HTTP 500.
        $contentTypeAction = $namespace;
        if ($this->versao == '4.00' && $this->servico == 'CTeConsultaV4') {
            $contentTypeAction = $metodo;
        }
        $parametros = array('Content-Type: application/soap+xml;charset=utf-8;action="' . $contentTypeAction . '"', 'SOAPAction: "' . $metodo . '"', "Content-length: $tamanho");
        //die("\n\n\n-------------- PARAMETROS ----------- \n\n\n ".print_r($parametros));
        $_aspa = '"';
        logCTE::logArquivo("\n\n\n*****************");
        logCTE::logArquivo($parametros,true);
        logCTE::logArquivo("*************\n\n\n");

        logCTE::logArquivo("\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("* __sendSOAP2 - Curl_init\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("\n");

        $oCurl = curl_init();
        if (is_array($this->aProxy)) {
            // curl_setopt - define parâmetros da requisição
            // torna o canal libcurl todas as operações do proxy http.   
            curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
            // passa um valor para definir um tipo de proxy: HTTP Proxy. Default., HTTPS Proxy. (Added in 7.52.0 for OpenSSL, GnuTLS and NSS)
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, "CURLPROXY_HTTP");
            // configura o proxy para usar
            curl_setopt($oCurl, CURLOPT_PROXY, $this->aProxy['IP'] . ':' . $this->aProxy['PORT']);
            if ($this->aProxy['PASS'] != '') {
                // nome de usuário e senha para autenticaçãop de proxy
                curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->aProxy['USER'] . ':' . $this->aProxy['PASS']);
                // configuração dos métodos de proxy http para tentar
                curl_setopt($oCurl, CURLOPT_PROXYAUTH, "CURLAUTH_BASIC");
            } //fim if senha proxy
        } //fim if aProxy
        // é fornecida a URL para ser usada.
        curl_setopt($oCurl, CURLOPT_URL, $urlsefaz . '');
        // é configurada o número da porta
        curl_setopt($oCurl, CURLOPT_PORT, 443);
        // para ativar o modo detalhado 
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        // para passar cabeçalho no fluxo de dados
        curl_setopt($oCurl, CURLOPT_HEADER, 1); //retorna o cabeçalho de resposta
        // define a versão SSL preferida
        // 5 (for CURL_SSLVERSION_TLSv1_1) ou 6 (for CURL_SSLVERSION_TLSv1_2) .
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 6);
        // verifica o nome do certificado em relação ao host e também se o servidor é o certo, ??????o parâmetro ultilizado é o melhor???????
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
        //  verifique o certificado SSL do peer, verifica sua autenticidade, ????? o parâmetro utilizado é o melhor?????
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
        // define o certificado do cliente SSL
        // curl_setopt($oCurl, CURLOPT_SSLCERT, './certs/1_cert.pem');
        curl_setopt($oCurl, CURLOPT_SSLCERT, $this->certificado->getPathCert());
        // especifica o arquivo de chaves privado para o certificado do cliente TLS e SSL
        // curl_setopt($oCurl, CURLOPT_SSLKEY, './certs/1_key.pem');
        curl_setopt($oCurl, CURLOPT_SSLKEY, $this->certificado->getPathPublicKey());
        //curl_setopt($oCurl, CURLOPT_SSLCERT, $Arquivo_PublKey);
        //curl_setopt($oCurl, CURLOPT_SSLKEY, $Arquivo_PrivKey);
        // Solicita um HTTP POST
        curl_setopt($oCurl, CURLOPT_POST, 1);
        // especifica dados para POST no servidor
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        // forçará o Curl a não imprimir os resultados de sua consulta. Em vez disso, ele retornará os resultados como um valor de retorno de seqüência de curl_exec () ao invés do usual true / false. Observe que se houver um erro, falso ainda será o valor de retorno de curl_exec ().
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        // configura o cabeçalho http personalizado
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $parametros);
        // tempo máximo que o pedido pode levar
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 15);
        // tempo limite para a fase de conexão
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 15);
        //-- Carrega os certificados em um resource
        logCTE::logArquivo("\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("* __sendSOAP2 - Carrega certificados em um resource - Curl_exec\n");
        logCTE::logArquivo("*               url: $urlsefaz\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("\n");

        if (is_array($this->aProxy)) {
            logCTE::logArquivo("CURLOPT_HTTPPROXYTUNNEL, 1\n");
            logCTE::logArquivo("CURLOPT_PROXYTYPE, CURLPROXY_HTTP\n");
            logCTE::logArquivo("CURLOPT_PROXY" . $this->aProxy['IP'] . ':' . $this->aProxy['PORT'] . '\n');
            if ($this->aProxy['PASS'] != '') {
                logCTE::logArquivo("CURLOPT_PROXYUSERPWD" . $this->aProxy['USER'] . ':' . $this->aProxy['PASS'] . '\n');
                logCTE::logArquivo("CURLOPT_PROXYAUTH CURLAUTH_BASIC \n");
            } //fim if senha proxy
        } //fim if aProxy
        logCTE::logArquivo("CURLOPT_URL : " . $urlsefaz . '\n');
        logCTE::logArquivo("CURLOPT_PORT 443 \n");
        logCTE::logArquivo("CURLOPT_VERBOSE  1 \n");
        logCTE::logArquivo("CURLOPT_HEADER  1 \n"); //retorna o cabeçalho de resposta
        logCTE::logArquivo("CURLOPT_SSLVERSION, 6 \n");
        logCTE::logArquivo("CURLOPT_SSL_VERIFYHOST, 0 \n");
        logCTE::logArquivo("CURLOPT_SSL_VERIFYPEER, 0 \n");
        logCTE::logArquivo("CURLOPT_SSLCERT" . $this->certificado->getPathCert() . "\n");
        logCTE::logArquivo("CURLOPT_SSLKEY" . $this->certificado->getPathPublicKey() . "\n");
        //logCTE::logArquivo("CURLOPT_SSLCERT, $Arquivo_PublKey));
        //logCTE::logArquivo("CURLOPT_SSLKEY, $Arquivo_PrivKey));
        logCTE::logArquivo("CURLOPT_POST, 1 \n");
        logCTE::logArquivo("CURLOPT_POSTFIELDS" . $data . "\n");
        logCTE::logArquivo("CURLOPT_RETURNTRANSFER, 1 \n");
        logCTE::logArquivo("CURLOPT_HTTPHEADER" . $parametros . '\n');
        logCTE::logArquivo("CURLOPT_TIMEOUT 15 \n");
        logCTE::logArquivo("CURLOPT_CONNECTTIMEOUT, 15 \n");

        $__xml = curl_exec($oCurl);

        logCTE::logArquivo("\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("* __sendSOAP2 - Carrega certificados em um resource - Curl_getinfo ($__xml)\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("\n");

        $info = curl_getinfo($oCurl); //informações da conexão
        logCTE::logArquivo("\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("* __sendSOAP2 - Continua\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("\n");

        $txtInfo = "";
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
        $xml = substr($__xml, $x, $n - $x);


        $Requisicao = htmlspecialchars($data, ENT_QUOTES);
        $Requisicao = str_replace("&lt;", "<br>&lt;", $Requisicao);
        $Requisicao = str_replace("<br>&lt;/", "&lt;/", $Requisicao);
        $Requisicao = $this->quebraString($Requisicao);

        $Retorno = htmlspecialchars($xml, ENT_QUOTES);
        $Retorno = str_replace("&lt;", "<br>&lt;", $Retorno);
        $Retorno = str_replace("<br>&lt;/", "&lt;/", $Retorno);
        $Retorno = $this->quebraString($Retorno);

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

        logCTE::logArquivo("\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("* __sendSOAP2 - Seta soapDebug\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("\n");

        $this->soapDebug = $cDebug;

        logCTE::logArquivo("\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("* soapDebug\n");
        logCTE::logArquivo($this->soapDebug . "\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("\n");  

        if ($__xml === false) {
            //não houve retorno
            $this->errMsg = curl_error($oCurl) . $info['http_code'] . $cCode[$info['http_code']];
            $this->errStatus = true;
        } else {
            //houve retorno mas ainda pode ser uma mensagem de erro do webservice
            $this->errMsg = $info['http_code'] . $cCode[$info['http_code']];
            $this->errStatus = false;
        }
        curl_close($oCurl);
        logCTE::logArquivo("\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("* __sendSOAP2 - Fecha Curl - Fim\n");
        logCTE::logArquivo("**************************************************************************************\n");
        logCTE::logArquivo("\n");  
        return $xml;
    }

    /**
     * setFunctionWDSL
     * Configura aa url e namespace para consumir o serviço    
     *    
     * @param string $servico
     * @param string $uf
     * @param string $ambiente            
     */
    public function setFunctionWDSL($servico, $uf, $ambiente)
    {
        $autorizador = $this->getAutorizador($uf);
        $this->urlServico = $this->getWDSL($autorizador, $servico, $ambiente);
        $this->namespace = "{$this->portal}/wsdl/{$servico}";
    }

    /**
     * getAutorizador
     * Retorna o autorizador com base na UF    
     * @param string $uf
     * @param string $ambiente   
     * @return string ambiente autorizador para UF informada
     */
    private function getAutorizador($uf)
    {
        #   Autorizadores Produção
        // MG MS MT PR
        // SVSP (AP, PE, RR, SP)
        // SVRS (AC, AL, AM, BA, CE, DF, ES, GO, MA, PA, PB, PI, RJ, RN, RO, RS, SC, SE, TO)   

        # Autorizadores Homologação
        // MG MS MT PR
        // SVSP (AP, PE, RR, SP)
        // SVRS (AC, AL, AM, BA, CE, DF, ES, GO, MA, PA, PB, PI, RJ, RN, RO, RS, SC, SE, TO)

        $autorizadores['SC'] = 'SVRS';
        $autorizadores['RS'] = 'SVRS';
        $autorizadores['AC'] = 'SVRS';
        $autorizadores['AL'] = 'SVRS';
        $autorizadores['AM'] = 'SVRS';
        $autorizadores['BA'] = 'SVRS';
        $autorizadores['CE'] = 'SVRS';
        $autorizadores['DF'] = 'SVRS';
        $autorizadores['ES'] = 'SVRS';
        $autorizadores['GO'] = 'SVRS';
        $autorizadores['MA'] = 'SVRS';
        $autorizadores['PA'] = 'SVRS';
        $autorizadores['PB'] = 'SVRS';
        $autorizadores['PI'] = 'SVRS';
        $autorizadores['RJ'] = 'SVRS';
        $autorizadores['RN'] = 'SVRS';
        $autorizadores['RO'] = 'SVRS';
        $autorizadores['RS'] = 'SVRS';
        $autorizadores['SC'] = 'SVRS';
        $autorizadores['SE'] = 'SVRS';
        $autorizadores['TO'] = 'SVRS';
        $autorizadores['RS'] = 'SVRS';
        $autorizadores['RS'] = 'SVRS';
        $autorizadores['AP'] = 'SVSP';
        $autorizadores['PE'] = 'SVSP';
        $autorizadores['RR'] = 'SVSP';
        $autorizadores['SP'] = 'SVSP';
        $autorizadores['MG'] = 'MG';
        $autorizadores['MS'] = 'MS';
        $autorizadores['MT'] = 'MT';
        $autorizadores['PR'] = 'PR';
        $autorizadores['AN'] = 'AN';

        return $autorizadores[$uf];
    }


    /**
     * getWDSL
     * Retorna a url do serviço
     * @param string $autorizador
     * @param string $servico 
     * @param string $ambiente   
     * @return string a url do serviço
     */
    private function getWDSL($autorizador, $servico, $ambiente = 2)
    {

        //SVRS - Homolacao
        //versão 3.00
        $relacaoServicos['SVRS']['CteRecepcao']['2']['3.00']          = "https://cte-homologacao.svrs.rs.gov.br/ws/cterecepcao/CteRecepcao.asmx";
        $relacaoServicos['SVRS']['CteRetRecepcao']['2']['3.00']       = "https://cte-homologacao.svrs.rs.gov.br/ws/cteretrecepcao/cteRetRecepcao.asmx";
        $relacaoServicos['SVRS']['CteInutilizacao']['2']['3.00']      = "https://cte-homologacao.svrs.rs.gov.br/ws/cteinutilizacao/cteinutilizacao.asmx";
        $relacaoServicos['SVRS']['CteConsulta']['2']['3.00']          = "https://cte-homologacao.svrs.rs.gov.br/ws/cteconsulta/CteConsulta.asmx";
        $relacaoServicos['SVRS']['CteStatusServico']['2']['3.00']     = "https://cte-homologacao.svrs.rs.gov.br/ws/ctestatusservico/CteStatusServico.asmx";
        $relacaoServicos['SVRS']['CteRecepcaoEvento']['2']['3.00']    = "https://cte-homologacao.svrs.rs.gov.br/ws/cterecepcaoevento/cterecepcaoevento.asmx";
        $relacaoServicos['SVRS']['CteRecepcaoOS']['2']['3.00']        = "https://cte-homologacao.svrs.rs.gov.br/ws/cterecepcaoos/cterecepcaoos.asmx";
        $relacaoServicos['SVRS']['CteRecepcaoSinc']['2']['3.00']      = "https://cte-homologacao.svrs.rs.gov.br/ws/CTeRecepcaoSinc/CTeRecepcaosinc.asmx";
        $relacaoServicos['SVRS']['CteRecepcaoGTVe']['2']['3.00']      = "https://cte-homologacao.svrs.rs.gov.br/ws/CTeRecepcaoGTVe/CTeRecepcaoGTVe.asmx";
        //versão 4.00
        $relacaoServicos['SVRS']['CTeStatusServicoV4']['2']['4.00']   = "https://cte-homologacao.svrs.rs.gov.br/ws/CTeStatusServicoV4/CTeStatusServicoV4.asmx";
        $relacaoServicos['SVRS']['CTeConsultaV4']['2']['4.00']        = "https://cte-homologacao.svrs.rs.gov.br/ws/CTeConsultaV4/CTeConsultaV4.asmx"; 
        $relacaoServicos['SVRS']['CTeRecepcaoSincV4']['2']['4.00']    = "https://cte-homologacao.svrs.rs.gov.br/ws/CTeRecepcaoSincV4/CTeRecepcaoSincV4.asmx"; 
        $relacaoServicos['SVRS']['CTeRecepcaoOSV4']['2']['4.00']      = "https://cte-homologacao.svrs.rs.gov.br/ws/CTeRecepcaoOSV4/CTeRecepcaoOSV4.asmx"; 
        $relacaoServicos['SVRS']['CTeRecepcaoGTVeV4']['2']['4.00']    = "https://cte-homologacao.svrs.rs.gov.br/ws/CTeRecepcaoGTVeV4/CTeRecepcaoGTVeV4.asmx"; 
        $relacaoServicos['SVRS']['CTeRecepcaoEventoV4']['2']['4.00']  = "https://cte-homologacao.svrs.rs.gov.br/ws/CTeRecepcaoEventoV4/CTeRecepcaoEventoV4.asmx"; 

        //SVRS - Producao
        // Versao 3.00
        $relacaoServicos['SVRS']['CteRecepcao']['1']['3.00']          = "https://cte.svrs.rs.gov.br/ws/cterecepcao/CteRecepcao.asmx";
        $relacaoServicos['SVRS']['CteRetRecepcao']['1']['3.00']       = "https://cte.svrs.rs.gov.br/ws/cteretrecepcao/cteRetRecepcao.asmx";
        $relacaoServicos['SVRS']['CteInutilizacao']['1']['3.00']      = "https://cte.svrs.rs.gov.br/ws/cteinutilizacao/cteinutilizacao.asmx";
        $relacaoServicos['SVRS']['CteConsulta']['1']['3.00']          = "https://cte.svrs.rs.gov.br/ws/cteconsulta/CteConsulta.asmx";
        $relacaoServicos['SVRS']['CteStatusServico']['1']['3.00']     = "https://cte.svrs.rs.gov.br/ws/ctestatusservico/CteStatusServico.asmx";
        $relacaoServicos['SVRS']['CteRecepcaoEvento']['1']['3.00']    = "https://cte.svrs.rs.gov.br/ws/cterecepcaoevento/cterecepcaoevento.asmx";
        $relacaoServicos['SVRS']['CteRecepcaoOS']['1']['3.00']        = "https://cte.svrs.rs.gov.br/ws/cterecepcaoos/cterecepcaoos.asmx";
        $relacaoServicos['SVRS']['CteRecepcaoSinc']['1']['3.00']      = "https://cte.svrs.rs.gov.br/ws/CTeRecepcaoSinc/CTeRecepcaosinc.asmx";
        $relacaoServicos['SVRS']['CteRecepcaoGTVe']['1']['3.00']      = "https://cte.svrs.rs.gov.br/ws/CTeRecepcaoGTVe/CTeRecepcaoGTVe.asmx";
        // Versao 4.00
        $relacaoServicos['SVRS']['CTeStatusServicoV4']['1']['4.00']   = "https://cte.svrs.rs.gov.br/ws/CTeStatusServicoV4/CTeStatusServicoV4.asmx";
        $relacaoServicos['SVRS']['CTeConsultaV4']['1']['4.00']        = "https://cte.svrs.rs.gov.br/ws/CTeConsultaV4/CTeConsultaV4.asmx";
        $relacaoServicos['SVRS']['CTeRecepcaoSincV4']['1']['4.00']    = "https://cte.svrs.rs.gov.br/ws/CTeRecepcaoSincV4/CTeRecepcaoSincV4.asmx";
        $relacaoServicos['SVRS']['CTeRecepcaoOSV4']['1']['4.00']      = "https://cte.svrs.rs.gov.br/ws/CTeRecepcaoOSV4/CTeRecepcaoOSV4.asmx";
        $relacaoServicos['SVRS']['CTeRecepcaoGTVeV4']['1']['4.00']    = "https://cte.svrs.rs.gov.br/ws/CTeRecepcaoGTVeV4/CTeRecepcaoGTVeV4.asmx";
        $relacaoServicos['SVRS']['CTeRecepcaoEventoV4']['1']['4.00']  = "https://cte.svrs.rs.gov.br/ws/CTeRecepcaoEventoV4/CTeRecepcaoEventoV4.asmx";

        //SVSP - Homologacao
        // Versao 3.00
        $relacaoServicos['SVSP']['CteRecepcao']['2']['3.00']          = "https://homologacao.nfe.fazenda.sp.gov.br/cteWEB/services/cteRecepcao.asmx";
        $relacaoServicos['SVSP']['CteRetRecepcao']['2']['3.00']       = "https://homologacao.nfe.fazenda.sp.gov.br/cteWEB/services/cteRetRecepcao.asmx";
        $relacaoServicos['SVSP']['CteInutilizacao']['2']['3.00']      = "https://homologacao.nfe.fazenda.sp.gov.br/cteWEB/services/cteInutilizacao.asmx";
        $relacaoServicos['SVSP']['CteConsulta']['2']['3.00']          = "https://homologacao.nfe.fazenda.sp.gov.br/cteWEB/services/cteConsulta.asmx";
        $relacaoServicos['SVSP']['CteStatusServico']['2']['3.00']     = "https://homologacao.nfe.fazenda.sp.gov.br/cteWEB/services/cteStatusServico.asmx";
        $relacaoServicos['SVSP']['CteRecepcaoEvento']['2']['3.00']    = "https://homologacao.nfe.fazenda.sp.gov.br/cteweb/services/cteRecepcaoEvento.asmx";
        $relacaoServicos['SVSP']['CteRecepcaoOS']['2']['3.00']        = "https://homologacao.nfe.fazenda.sp.gov.br/cteWEB/services/cteRecepcaoOS.asmx";
        $relacaoServicos['SVSP']['CteRecepcaoGTVe']['2']['3.00']      = "https://homologacao.nfe.fazenda.sp.gov.br/cteWEB/services/cteRecepcaoGTVe.asmx";
        //Versao 4.00
        $relacaoServicos['SVSP']['CTeConsultaV4']['2']['4.00']        = "https://homologacao.nfe.fazenda.sp.gov.br/CTeWS/WS/CTeConsultaV4.asmx";
        $relacaoServicos['SVSP']['CTeRecepcaoEventoV4']['2']['4.00']  = "https://homologacao.nfe.fazenda.sp.gov.br/CTeWS/WS/CTeRecepcaoEventoV4.asmx";
        $relacaoServicos['SVSP']['CTeRecepcaoGTVeV4']['2']['4.00']    = "https://homologacao.nfe.fazenda.sp.gov.br/CTeWS/WS/CTeRecepcaoGTVeV4.asmx";
        $relacaoServicos['SVSP']['CTeRecepcaoOSV4']['2']['4.00']      = "https://homologacao.nfe.fazenda.sp.gov.br/CTeWS/WS/CTeRecepcaoOSV4.asmx";
        $relacaoServicos['SVSP']['CTeRecepcaoSincV4']['2']['4.00']    = "https://homologacao.nfe.fazenda.sp.gov.br/CTeWS/WS/CTeRecepcaoSincV4.asmx";
        $relacaoServicos['SVSP']['CTeStatusServicoV4']['2']['4.00']   = "https://homologacao.nfe.fazenda.sp.gov.br/CTeWS/WS/CTeStatusServicoV4.asmx";

        //SVSP - Producao
        // Versao 3.00
        $relacaoServicos['SVSP']['CteRecepcao']['1']['3.00']          = "https://nfe.fazenda.sp.gov.br/cteWEB/services/cteRecepcao.asmx";
        $relacaoServicos['SVSP']['CteRetRecepcao']['1']['3.00']       = "https://nfe.fazenda.sp.gov.br/cteWEB/services/cteRetRecepcao.asmx";
        $relacaoServicos['SVSP']['CteInutilizacao']['1']['3.00']      = "https://nfe.fazenda.sp.gov.br/cteWEB/services/cteInutilizacao.asmx";
        $relacaoServicos['SVSP']['CteConsulta']['1']['3.00']          = "https://nfe.fazenda.sp.gov.br/cteWEB/services/cteConsulta.asmx";
        $relacaoServicos['SVSP']['CteStatusServico']['1']['3.00']     = "https://nfe.fazenda.sp.gov.br/cteWEB/services/cteStatusServico.asmx";
        $relacaoServicos['SVSP']['CteRecepcaoEvento']['1']['3.00']    = "https://nfe.fazenda.sp.gov.br/cteweb/services/cteRecepcaoEvento.asmx";
        $relacaoServicos['SVSP']['CteRecepcaoOS']['1']['3.00']        = "https://nfe.fazenda.sp.gov.br/cteWEB/services/cteRecepcaoOS.asmx";
        $relacaoServicos['SVSP']['CteRecepcaoGTVe']['1']['3.00']      = "https://nfe.fazenda.sp.gov.br/cteWEB/services/cteRecepcaoGTVe.asmx";
        //Versao 4.00
        $relacaoServicos['SVSP']['CTeConsultaV4']['1']['4.00']        = "https://nfe.fazenda.sp.gov.br/CTeWS/WS/CTeConsultaV4.asmx";
        $relacaoServicos['SVSP']['CTeRecepcaoEventoV4']['1']['4.00']  = "https://nfe.fazenda.sp.gov.br/CTeWS/WS/CTeRecepcaoEventoV4.asmx";
        $relacaoServicos['SVSP']['CTeRecepcaoGTVeV4']['1']['4.00']    = "https://nfe.fazenda.sp.gov.br/CTeWS/WS/CTeRecepcaoGTVeV4.asmx";
        $relacaoServicos['SVSP']['CTeRecepcaoOSV4']['1']['4.00']      = "https://nfe.fazenda.sp.gov.br/CTeWS/WS/CTeRecepcaoOSV4.asmx";
        $relacaoServicos['SVSP']['CTeRecepcaoSincV4']['1']['4.00']    = "https://nfe.fazenda.sp.gov.br/CTeWS/WS/CTeRecepcaoSincV4.asmx";
        $relacaoServicos['SVSP']['CTeStatusServicoV4']['1']['4.00']   = "https://nfe.fazenda.sp.gov.br/CTeWS/WS/CTeStatusServicoV4.asmx";

        //PR - Homologação
        // Versao 3.00
        $relacaoServicos['PR']['CteRecepcao']['2']['3.00']            = "https://homologacao.cte.fazenda.pr.gov.br/cte/CteRecepcao";
        $relacaoServicos['PR']['CteRetRecepcao']['2']['3.00']         = "https://homologacao.cte.fazenda.pr.gov.br/cte/CteRetRecepcao";
        $relacaoServicos['PR']['CteInutilizacao']['2']['3.00']        = "https://homologacao.cte.fazenda.pr.gov.br/cte/CteInutilizacao";
        $relacaoServicos['PR']['CteConsulta']['2']['3.00']            = "https://homologacao.cte.fazenda.pr.gov.br/cte/CteConsulta";
        $relacaoServicos['PR']['CteStatusServico']['2']['3.00']       = "https://homologacao.cte.fazenda.pr.gov.br/cte/CteStatusServico";
        $relacaoServicos['PR']['CteRecepcaoEvento']['2']['3.00']      = "https://homologacao.cte.fazenda.pr.gov.br/cte/CteRecepcaoEvento";
        $relacaoServicos['PR']['CteRecepcaoOS']['2']['3.00']          = "https://homologacao.cte.fazenda.pr.gov.br/cte/CteRecepcaoOS";
        $relacaoServicos['PR']['CteRecepcaoSinc']['2']['3.00']        = "https://homologação.cte.fazenda.pr.gov.br/cte/CteRecepcaoSinc";
        $relacaoServicos['PR']['CteRecepcaoGTVe']['2']['3.00']        = "https://homologação.cte.fazenda.pr.gov.br/cte/CteRecepcaoGTVe";
        // Versao 4.00
        $relacaoServicos['PR']['CTeRecepcaoSincV4']['2']['4.00']      = "https://homologacao.cte.fazenda.pr.gov.br/cte4/CTeRecepcaoSincV4";
        $relacaoServicos['PR']['CTeRecepcao']['2']['4.00']            = "https://homologacao.cte.fazenda.pr.gov.br/cte4/CTeRecepcaoSincV4";
        $relacaoServicos['PR']['CTeConsultaV4']['2']['4.00']          = "https://homologacao.cte.fazenda.pr.gov.br/cte4/CTeConsultaV4";
        $relacaoServicos['PR']['CTeStatusServicoV4']['2']['4.00']     = "https://homologacao.cte.fazenda.pr.gov.br/cte4/CTeStatusServicoV4";
        $relacaoServicos['PR']['CTeRecepcaoEventoV4']['2']['4.00']    = "https://homologacao.cte.fazenda.pr.gov.br/cte4/CTeRecepcaoEventoV4";
        $relacaoServicos['PR']['CteRecepcaoEvento']['2']['4.00']      = "https://homologacao.cte.fazenda.pr.gov.br/cte4/CTeRecepcaoEventoV4";
        $relacaoServicos['PR']['CTeRecepcaoGTVeV4']['2']['4.00']      = "https://homologacao.cte.fazenda.pr.gov.br/cte4/CTeRecepcaoGTVeV4";
        $relacaoServicos['PR']['CTeRecepcaoOSV4']['2']['4.00']        = "https://homologacao.cte.fazenda.pr.gov.br/cte4/CTeRecepcaoOSV4";

        //PR - Produção
        // Versao 3.00
        $relacaoServicos['PR']['CteRecepcao']['1']['3.00']            = "https://cte.fazenda.pr.gov.br/cte/CteRecepcao";
        $relacaoServicos['PR']['CteRetRecepcao']['1']['3.00']         = "https://cte.fazenda.pr.gov.br/cte/CteRetRecepcao";
        $relacaoServicos['PR']['CteInutilizacao']['1']['3.00']        = "https://cte.fazenda.pr.gov.br/cte/CteInutilizacao";
        $relacaoServicos['PR']['CteConsulta']['1']['3.00']            = "https://cte.fazenda.pr.gov.br/cte/CteConsulta";
        $relacaoServicos['PR']['CteStatusServico']['1']['3.00']       = "https://cte.fazenda.pr.gov.br/cte/CteStatusServico";
        $relacaoServicos['PR']['CteRecepcaoEvento']['1']['3.00']      = "https://cte.fazenda.pr.gov.br/cte/CteRecepcaoEvento";
        $relacaoServicos['PR']['CteRecepcaoOS']['1']['3.00']          = "https://cte.fazenda.pr.gov.br/cte/CteRecepcaoOS";
        $relacaoServicos['PR']['CteRecepcaoSinc']['1']['3.00']        = "https://cte.fazenda.pr.gov.br/cte/CteRecepcaoSinc";
        $relacaoServicos['PR']['CteRecepcaoGTVe']['1']['3.00']        = "https://cte.fazenda.pr.gov.br/cte/CteRecepcaoGTVe";
        // Versao 4.00
        $relacaoServicos['PR']['CTeRecepcaoSincV4']['1']['4.00']      = "https://cte.fazenda.pr.gov.br/cte4/CTeRecepcaoSincV4";
        $relacaoServicos['PR']['CTeRecepcao']['1']['4.00']            = "https://cte.fazenda.pr.gov.br/cte4/CTeRecepcaoSincV4";
        $relacaoServicos['PR']['CTeRecepcaoGTVeV4']['1']['4.00']      = "https://cte.fazenda.pr.gov.br/cte4/CTeRecepcaoGTVeV4";
        $relacaoServicos['PR']['CTeRecepcaoOSV4']['1']['4.00']        = "https://cte.fazenda.pr.gov.br/cte4/CTeRecepcaoOSV4";
        $relacaoServicos['PR']['CTeConsultaV4']['1']['4.00']          = "https://cte.fazenda.pr.gov.br/cte4/CTeConsultaV4";
        $relacaoServicos['PR']['CTeStatusServicoV4']['1']['4.00']     = "https://cte.fazenda.pr.gov.br/cte4/CTeStatusServicoV4";
        $relacaoServicos['PR']['CTeRecepcaoEventoV4']['1']['4.00']    = "https://cte.fazenda.pr.gov.br/cte4/CTeRecepcaoEventoV4";
        $relacaoServicos['PR']['CteRecepcaoEvento']['1']['4.00']      = "https://cte.fazenda.pr.gov.br/cte4/CTeRecepcaoEventoV4";

        //MT - Homologação
        // Versao 3.00
        $relacaoServicos['MT']['CteRecepcao']['2']['3.00']            = "https://homologacao.sefaz.mt.gov.br/ctews/services/CteRecepcao";
        $relacaoServicos['MT']['CteRetRecepcao']['2']['3.00']         = "https://homologacao.sefaz.mt.gov.br/ctews/services/CteRetRecepcao";
        $relacaoServicos['MT']['CteInutilizacao']['2']['3.00']        = "https://homologacao.sefaz.mt.gov.br/ctews/services/CteInutilizacao";
        $relacaoServicos['MT']['CteConsulta']['2']['3.00']            = "https://homologacao.sefaz.mt.gov.br/ctews/services/CteConsulta";
        $relacaoServicos['MT']['CteStatusServico']['2']['3.00']       = "https://homologacao.sefaz.mt.gov.br/ctews/services/CteStatusServico";
        $relacaoServicos['MT']['CteRecepcaoEvento']['2']['3.00']      = "https://homologacao.sefaz.mt.gov.br/ctews2/services/CteRecepcaoEvento";
        $relacaoServicos['MT']['CteRecepcaoOS']['2']['3.00']          = "https://homologacao.sefaz.mt.gov.br/ctews/services/CteRecepcaoOS      ";
        $relacaoServicos['MT']['CteRecepcaoSinc']['2']['3.00']        = "https://homologacao.sefaz.mt.gov.br/ctews2/services/CteRecepcaoSinc";
        $relacaoServicos['MT']['CteRecepcaoGTVe']['2']['3.00']        = "https://homologacao.sefaz.mt.gov.br/ctews2/services/cteRecepcaoGTVe";
        // Versao 4.00
        $relacaoServicos['MT']['CTeStatusServicoV4']['2']['4.00']     = "https://homologacao.sefaz.mt.gov.br/ctews2/services/CTeStatusServicoV4";
        $relacaoServicos['MT']['CTeConsultaV4']['2']['4.00']          = "https://homologacao.sefaz.mt.gov.br/ctews2/services/CTeConsultaV4";
        $relacaoServicos['MT']['CTeRecepcaoSincV4']['2']['4.00']      = "https://homologacao.sefaz.mt.gov.br/ctews2/services/CTeRecepcaoSincV4";
        $relacaoServicos['MT']['CTeRecepcaoGTVeV4']['2']['4.00']      = "https://homologacao.sefaz.mt.gov.br/ctews2/services/CTeRecepcaoGTVeV4";
        $relacaoServicos['MT']['CTeRecepcaoEventoV4']['2']['4.00']    = "https://homologacao.sefaz.mt.gov.br/ctews2/services/CTeRecepcaoEventoV4";
        $relacaoServicos['MT']['CTeRecepcaoOSV4']['2']['4.00']        = "https://homologacao.sefaz.mt.gov.br/ctews/services/CTeRecepcaoOSV4";

        //MT - Produção
        // Versao 3.00
        $relacaoServicos['MT']['CteRecepcao']['1']['3.00']            = "https://cte.sefaz.mt.gov.br/ctews/services/CteRecepcao";
        $relacaoServicos['MT']['CteRetRecepcao']['1']['3.00']         = "https://cte.sefaz.mt.gov.br/ctews/services/CteRetRecepcao";
        $relacaoServicos['MT']['CteInutilizacao']['1']['3.00']        = "https://cte.sefaz.mt.gov.br/ctews/services/CteInutilizacao";
        $relacaoServicos['MT']['CteConsulta']['1']['3.00']            = "https://cte.sefaz.mt.gov.br/ctews/services/CteConsulta";
        $relacaoServicos['MT']['CteStatusServico']['1']['3.00']       = "https://cte.sefaz.mt.gov.br/ctews/services/CteStatusServico";
        $relacaoServicos['MT']['CteRecepcaoEvento']['1']['3.00']      = "https://cte.sefaz.mt.gov.br/ctews2/services/CteRecepcaoEvento";
        $relacaoServicos['MT']['CteRecepcaoOS']['1']['3.00']          = "https://cte.sefaz.mt.gov.br/ctews/services/CteRecepcaoOS";
        $relacaoServicos['MT']['CteRecepcaoSinc']['1']['3.00']        = "https://cte.sefaz.mt.gov.br/ctews2/services/CteRecepcaoSinc";
        $relacaoServicos['MT']['CteRecepcaoGTVe']['1']['3.00']        = "https://cte.sefaz.mt.gov.br/ctews2/services/cteRecepcaoGTVe";
        // Versao 4.00
        $relacaoServicos['MT']['CTeConsultaV4']['1']['4.00']          = "https://cte.sefaz.mt.gov.br/ctews2/services/CTeConsultaV4";
        $relacaoServicos['MT']['CTeRecepcaoEventoV4']['1']['4.00']    = "https://cte.sefaz.mt.gov.br/ctews2/services/CTeRecepcaoEventoV4";
        $relacaoServicos['MT']['CTeStatusServicoV4']['1']['4.00']     = "https://cte.sefaz.mt.gov.br/ctews2/services/CTeStatusServicoV4";
        $relacaoServicos['MT']['CTeRecepcaoSincV4']['1']['4.00']      = "https://cte.sefaz.mt.gov.br/ctews2/services/CTeRecepcaoSincV4";
        $relacaoServicos['MT']['CTeRecepcaoGTVeV4']['1']['4.00']      = "https://cte.sefaz.mt.gov.br/ctews2/services/CTeRecepcaoGTVeV4";
        $relacaoServicos['MT']['CTeRecepcaoOSV4']['1']['4.00']        = "https://cte.sefaz.mt.gov.br/ctews/services/CTeRecepcaoOSV4";

        //MS - Homologação
        // Versao 3.00
        $relacaoServicos['MS']['CteRecepcao']['2']['3.00']            = "https://homologacao.cte.ms.gov.br/ws/CteRecepcao";
        $relacaoServicos['MS']['CteRetRecepcao']['2']['3.00']         = "https://homologacao.cte.ms.gov.br/ws/CteRetRecepcao";
        $relacaoServicos['MS']['CteInutilizacao']['2']['3.00']        = "https://homologacao.cte.ms.gov.br/ws/CteInutilizacao";
        $relacaoServicos['MS']['CteConsulta']['2']['3.00']            = "https://homologacao.cte.ms.gov.br/ws/CteConsulta";
        $relacaoServicos['MS']['CteStatusServico']['2']['3.00']       = "https://homologacao.cte.ms.gov.br/ws/CteStatusServico";
        $relacaoServicos['MS']['CteRecepcaoEvento']['2']['3.00']      = "https://homologacao.cte.ms.gov.br/ws/CteRecepcaoEvento";
        $relacaoServicos['MS']['CteRecepcaoOS']['2']['3.00']          = "https://homologacao.cte.ms.gov.br/ws/CteRecepcaoOS";
        $relacaoServicos['MS']['CteRecepcaoSinc']['2']['3.00']        = "https://homologacao.cte.ms.gov.br/ws/CteRecepcaoSinc";
        // Versao 4.00
        $relacaoServicos['MS']['CTeStatusServicoV4']['2']['4.00']     = "https://homologacao.cte.ms.gov.br/ws/CTeStatusServicoV4";
        $relacaoServicos['MS']['CTeRecepcaoEventoV4']['2']['4.00']    = "https://homologacao.cte.ms.gov.br/ws/CTeRecepcaoEventoV4";
        $relacaoServicos['MS']['CTeConsultaV4']['2']['4.00']          = "https://homologacao.cte.ms.gov.br/ws/CTeConsultaV4";
        $relacaoServicos['MS']['CTeRecepcaoSincV4']['2']['4.00']      = "https://homologacao.cte.ms.gov.br/ws/CTeRecepcaoSincV4";

        //MS - Produção
        // Versao 3.00
        $relacaoServicos['MS']['CteRecepcao']['1']['3.00']            = "https://producao.cte.ms.gov.br/ws/CteRecepcao";
        $relacaoServicos['MS']['CteRetRecepcao']['1']['3.00']         = "https://producao.cte.ms.gov.br/ws/CteRetRecepcao";
        $relacaoServicos['MS']['CteInutilizacao']['1']['3.00']        = "https://producao.cte.ms.gov.br/ws/CteInutilizacao";
        $relacaoServicos['MS']['CteConsulta']['1']['3.00']            = "https://producao.cte.ms.gov.br/ws/CteConsulta";
        $relacaoServicos['MS']['CteStatusServico']['1']['3.00']       = "https://producao.cte.ms.gov.br/ws/CteStatusServico";
        $relacaoServicos['MS']['CteRecepcaoEvento']['1']['3.00']      = "https://producao.cte.ms.gov.br/ws/CteRecepcaoEvento";
        $relacaoServicos['MS']['CteRecepcaoOS']['1']['3.00']          = "https://producao.cte.ms.gov.br/ws/CteRecepcaoOS";
        $relacaoServicos['MS']['CteRecepcaoSinc']['1']['3.00']        = "https://producao.cte.ms.gov.br/ws/CteRecepcaoSinc";
        // Versao 4.00
        $relacaoServicos['MS']['CTeRecepcaoSincV4']['1']['4.00']      = "https://producao.cte.ms.gov.br/ws/CTeRecepcaoSincV4";
        $relacaoServicos['MS']['CTeStatusServicoV4']['1']['4.00']     = "https://producao.cte.ms.gov.br/ws/CTeStatusServicoV4";
        $relacaoServicos['MS']['CTeConsultaV4']['1']['4.00']          = "https://producao.cte.ms.gov.br/ws/CTeConsultaV4";
        $relacaoServicos['MS']['CTeRecepcaoEventoV4']['1']['4.00']    = "https://producao.cte.ms.gov.br/ws/CTeRecepcaoEventoV4";
        $relacaoServicos['MS']['CTeRecepcaoOSV4']['1']['4.00']        = "https://producao.cte.ms.gov.br/ws/CTeRecepcaoOSV4";
        $relacaoServicos['MS']['CTeRecepcaoGTVeV4']['1']['4.00']      = "https://producao.cte.ms.gov.br/ws/CTeRecepcaoGTVeV4";

        //MG - Homogação
        // Versao 3.00
        $relacaoServicos['MG']['CteRecepcao']['2']['3.00']            = "https://hcte.fazenda.mg.gov.br/cte/services/CteRecepcao";
        $relacaoServicos['MG']['CteRetRecepcao']['2']['3.00']         = "https://hcte.fazenda.mg.gov.br/cte/services/CteRetRecepcao";
        $relacaoServicos['MG']['CteInutilizacao']['2']['3.00']        = "https://hcte.fazenda.mg.gov.br/cte/services/CteInutilizacao";
        $relacaoServicos['MG']['CteConsulta']['2']['3.00']            = "https://hcte.fazenda.mg.gov.br/cte/services/CteConsulta";
        $relacaoServicos['MG']['CteStatusServico']['2']['3.00']       = "https://hcte.fazenda.mg.gov.br/cte/services/CteStatusServico";
        $relacaoServicos['MG']['CteRecepcaoEvento']['2']['3.00']      = "https://hcte.fazenda.mg.gov.br/cte/services/RecepcaoEvento";
        $relacaoServicos['MG']['CteRecepcaoOS']['2']['3.00']          = "https://hcte.fazenda.mg.gov.br/cte/services/CteRecepcaoOS";   
        // Versao 4.00    
        $relacaoServicos['MG']['CTeRecepcaoSincV4']['2']['4.00']      = "https://hcte.fazenda.mg.gov.br/cte/services/CTeRecepcaoSincV4";
        $relacaoServicos['MG']['CTeRecepcaoOSV4']['2']['4.00']        = "https://hcte.fazenda.mg.gov.br/cte/services/CTeRecepcaoOSV4";   
        $relacaoServicos['MG']['CTeRecepcaoGTVeV4']['2']['4.00']      = "https://hcte.fazenda.mg.gov.br/cte/services/CTeRecepcaoGTVeV4";
        $relacaoServicos['MG']['CTeConsultaV4']['2']['4.00']          = "https://hcte.fazenda.mg.gov.br/cte/services/CTeConsultaV4";
        $relacaoServicos['MG']['CTeStatusServicoV4']['2']['4.00']     = "https://hcte.fazenda.mg.gov.br/cte/services/CTeStatusServicoV4";
        $relacaoServicos['MG']['CTeRecepcaoEventoV4']['2']['4.00']    = "https://hcte.fazenda.mg.gov.br/cte/services/CTeRecepcaoEventoV4";

        //MG - Produção
        // Versao 3.00
        $relacaoServicos['MG']['CteRecepcao']['1']['3.00']            = "https://cte.fazenda.mg.gov.b/cte/services/CteRecepcao";
        $relacaoServicos['MG']['CteRetRecepcao']['1']['3.00']         = "https://cte.fazenda.mg.gov.b/cte/services/CteRetRecepcao";
        $relacaoServicos['MG']['CteInutilizacao']['1']['3.00']        = "https://cte.fazenda.mg.gov.b/cte/services/CteInutilizacao";
        $relacaoServicos['MG']['CteConsulta']['1']['3.00']            = "https://cte.fazenda.mg.gov.b/cte/services/CteConsulta";
        $relacaoServicos['MG']['CteStatusServico']['1']['3.00']       = "https://cte.fazenda.mg.gov.b/cte/services/CteStatusServico";
        $relacaoServicos['MG']['CteRecepcaoEvento']['1']['3.00']      = "https://cte.fazenda.mg.gov.b/cte/services/RecepcaoEvento";
        $relacaoServicos['MG']['CteRecepcaoOS']['1']['3.00']          = "https://cte.fazenda.mg.gov.b/cte/services/CteRecepcaoOS";    
        // Versao 4.00
        $relacaoServicos['MG']['CTeRecepcaoSincV4']['1']['4.00']      = "https://cte.fazenda.mg.gov.br/cte/services/CTeRecepcaoSincV4";
        $relacaoServicos['MG']['CTeRecepcaoGTVeV4']['1']['4.00']      = "https://cte.fazenda.mg.gov.br/cte/services/CTeRecepcaoGTVeV4";
        $relacaoServicos['MG']['CTeRecepcaoOSV4']['1']['4.00']        = "https://cte.fazenda.mg.gov.br/cte/services/CTeRecepcaoOSV4";    
        $relacaoServicos['MG']['CTeConsultaV4']['1']['4.00']          = "https://cte.fazenda.mg.gov.br/cte/services/CTeConsultaV4";
        $relacaoServicos['MG']['CTeStatusServicoV4']['1']['4.00']     = "https://cte.fazenda.mg.gov.br/cte/services/CTeStatusServicoV4";
        $relacaoServicos['MG']['CTeRecepcaoEventoV4']['1']['4.00']    = "https://cte.fazenda.mg.gov.br/cte/services/CTeRecepcaoEventoV4";
        
        if($servico == 'CTeDistribuicaoDFe'){
            $relacaoServicos['AN']['CTeDistribuicaoDFe']['1']         =   "https://www1.cte.fazenda.gov.br/CTeDistribuicaoDFe/CTeDistribuicaoDFe.asmx";
            $relacaoServicos['AN']['CTeDistribuicaoDFe']['2']         =   "https://hom1.cte.fazenda.gov.br/CTeDistribuicaoDFe/CTeDistribuicaoDFe.asmx";
            return $relacaoServicos[$autorizador][$servico][$ambiente]; // SC -> SVRS | PR -> PR

        } else {
            $versao = $this->versao;
            return $relacaoServicos[$autorizador][$servico][$ambiente][$versao]; // SC -> SVRS | PR -> PR
        }

    }

    protected function quebraString($String, $nTamanho = 80)
    {
        $cRetorno = '';
        $mLinha = explode("<br>", $String);
        for ($i = 0; $i < count($mLinha); $i++) {
            if (strlen($mLinha[$i]) >= $nTamanho) {
                for ($n = 0; $n < strlen($mLinha[$i]); $n += $nTamanho) {
                    $c = substr($mLinha[$i], $n, $nTamanho);
                    $cRetorno .= $c . "<br>";
                }
            } else {
                $cRetorno .= $mLinha[$i] . "<br>";
            }
        }
        return $cRetorno;
    }

    protected function getTimeZone()
    {

        $cEstadoEmitente = $this->UF;

        //estados que participam do horario de ver�o
        $aUFhv = array('BA', 'ES', 'GO', 'MG', 'MS', 'PR', 'RJ', 'RS', 'SP', 'SC', 'TO');
        $this->timeZone = '-03:00';

        // corrigir o timeZone
        if (
            $cEstadoEmitente == 'AC' ||
            $cEstadoEmitente == 'AM' ||
            $cEstadoEmitente == 'MT' ||
            $cEstadoEmitente == 'MS' ||
            $cEstadoEmitente == 'RO' ||
            $cEstadoEmitente == 'RR'
        ) {

            $this->timeZone = '-04:00';
        }
        // verificar se estamos no hor�rio de ver�o *** depende da configura��o do servidor ***
        if (date('I') == 1) {

            //estamos no horario de ver�o verificar se o estado est� incluso
            if (in_array($cEstadoEmitente, $aUFhv)) {
                $tz = (int) $this->timeZone;
                $tz++;
                $this->timeZone = '-' . sprintf("%02d", abs($tz)) . ':00'; //poderia ser obtido com date('P')
            }
        } //fim check horario verao

        return date("P");
    }

    private function adicionaErro($mensagem) {
        $this->erro = $mensagem;
        $this->erroStatus = true; 
    }

    protected function removeAcentos($texto) {
        $search = array('á', 'à', 'ã', 'é', 'è', 'ê', 'í', 'ì', 'î', 'ó', 'ò', 'ô', 'õ', 'ú', 'ù', 'û', 'ç', 'Á', 'À', 'Ã', 'É', 'È', 'Ê', 'Í', 'Ì', 'Î', 'Ó', 'Ò', 'Ô', 'Õ', 'Ú', 'Ù', 'Û', 'Ç');
        $replace = array('a', 'a', 'a', 'e', 'e', 'e', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'c', 'A', 'A', 'A', 'E', 'E', 'E', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'C');
      
        return str_replace($search, $replace, $texto);
    }

    protected function removeCaracteresEspeciais($texto) {
        $texto = $this->removeAcentos($texto);
        return preg_replace("/[^\w @#,-_.;:$%\/<>]/", "", $texto);
    }
}
