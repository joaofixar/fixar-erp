<?php

namespace hardness;

use InvalidArgumentException;

require_once('nfe4.class.php');
class NFe4Lote extends classeNFe
{

    public function __construct($configuracoes)
    {
        parent::__construct();
        $this->logNFe("Configurações: ");
        $this->logNfe($configuracoes, true);
        $this->setCnpjEmitente($configuracoes['T007_Empresa_Usuaria_Cnpj']);
        $this->setUFEmitente($configuracoes['T007_Empresa_Usuaria_Uf']);
        $this->setCodigoUFEmitente($configuracoes['T007_Empresa_Usuaria_Codigo_Estado_IBGE']);
        $this->setPastaArquivoXML($configuracoes['pastaArquivoXML']);
        $this->setTipoDocAuxiliar(1);
        $this->setIdentAmbiente($configuracoes['T007_Ambiente_NFe']);

        $this->setModeloCertificado($configuracoes['C020_NFe_Tipo_Certificado']);
        $this->setSenhaCertificado($configuracoes['C004_Senha_Certificados']);
        $this->setCertificadoPEM("{$configuracoes['C004_Localizacao_Certificados']}{$configuracoes['C004_Id']}.pem");
        $this->carregaCertP12("{$configuracoes['C004_Localizacao_Certificados']}{$configuracoes['C004_Id']}.pfx");
    }
    /**    
     * Envia lote NF-e (Nota Fiscal)     
     * @param   string $numLote     
     * @return true|string
     **/
    public function enviarLoteNfe($numLote)
    {
        global $g;
        $this->logNfe("Iniciando envio do lote");
        $this->logNfe("Numero Lote: $numLote");
        if (empty($numLote))
            throw new InvalidArgumentException("numLote: Não foi informado o número do lote");

        $xmls = array();

        $T007 = mysql_query("SELECT T007_Id, 
                                    T007_Numero_Nota_Fiscal,
                                    T007_Serie,
                                    T007_Ambiente_NFe,
                                    C004_Localizacao_Certificados,
                                    C004_Id,
                                    T007_Empresa_Usuaria_Uf,
                                    T007_Empresa_Usuaria_Codigo_Estado_IBGE,
                                    C004_Senha_Certificados,                                    
                                    T007_Data_Emissao,
                                    T007_Empresa_Usuaria_Cnpj,
                                    T007_C004_Id,
                                    T007_D024_Id,
                                    T007_Flag_ACP                                   
                                FROM T007 
                                LEFT JOIN C004 ON C004_Id = T007_C004_Id
                                WHERE T007_Numero_Lote = '{$numLote}' AND T007_C004_Id = '{$g['empresaAtual']}'");
        while ($mT007 = mysql_fetch_array($T007)) {
            $this->logNfe("Nota Atual: {$mT007['T007_Numero_Nota_Fiscal']}");

            $this->setT007Id($mT007['T007_Id']);
            $this->setNumeroNotaFiscal($mT007['T007_Numero_Nota_Fiscal']);
            $this->setSerieNF($mT007['T007_Serie']);
            $this->setDataEmissao($mT007['T007_Data_Emissao']);
            $modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';
            $this->setModeloNF($modelo);

            # Cria XML
            $this->logNfe("Criando Arquivo XML");
            $this->criarArquivoXML();
            $chaveAcesso = str_replace("NFe", "", $this->getChaveAcessoNFe());


            if ($this->errorStatus === true) {

                $mensagem = array_reduce($this->mErrorMsg, function ($carry, $mensagem) {
                    $carry .= $mensagem . " - ";
                    return $carry;
                }, '');


                $this->logNfe("Erro ao gerar arquivo XML");
                $this->atualizarStatus($mT007['T007_Id'], 0, 109, $mensagem, '', '', true);
                continue;
            }

            mysql_query("UPDATE T007 
                            SET T007_Chave_Acesso_Nfe = '{$chaveAcesso}'
                        WHERE  T007_Id='{$mT007['T007_Id']}'");

            $this->gravaTabelaLog(
                $mT007['T007_Id'],
                $mT007['T007_C004_Id'],
                $mT007['T007_D024_Id'],
                $this->getModeloNF(),
                'Gerou Chave Acesso',
                $mT007['T007_Numero_Nota_Fiscal'],
                $chaveAcesso
            );

            $this->logNfe("Assinando Arquivo XML");
            # Assina XML
            $this->assinarXML();

            $pathXMLAssinado = $this->getPastaArquivoXML() . "assinado/NFe{$chaveAcesso}.xml";

            if (!file_exists($pathXMLAssinado)) {
                return array(false,"Não foi possível localizar o XML Assinado");
            }
            $xml = file_get_contents($pathXMLAssinado);
            $xml = str_replace(array(chr(13), chr(10), "\r", "\n", "\r\n"), '', $xml);

            //Validar XML            
            $retorno = $this->validarXML($xml, 'hardness/class/PL/PL_Nfe/nfe_v4.00.xsd');

            if ($retorno !== true) {
                //Modificar status e remover do lote                            
                $this->atualizarStatus($mT007['T007_Id'], 0, 109, $retorno, '', '', true);
                $this->logNfe("XML Inválido\n");
                continue;
            }
            $this->logNfe("XML Valido e incluido no lote\n");
            $xmls[] = $xml;
        }

        if (count($xmls) == 0) {
            return array(false,"Nenhuma nota incluida no lote!");
        }

        if (count($xmls) > 50) {
            return array(false,"Não é possível ter mais que 50 notas no mesmo lote");
        }
        $this->logNfe("Enviando Nota");
        $this->logNfe("Quantidade de Notas no LOTE: " . count($xmls));
        // Faz a chamada ao metodo
        $retorno = $this->enviaNFLote($xmls, $numLote);
        $this->logNfe("Retorno SEFAZ:");
        $this->logNfe($this->retornoEnviaNF, true);

        if ($retorno !== true) {
            $this->atualizarStatus(0, $numLote, 202, $this->mErrorMsg[0]);
            return array(false,$this->mErrorMsg[0]);
        }

        if (empty($this->cStat)) {
            $this->atualizarStatus(0, $numLote, 202, 'ERRO: PROBLEMA NA COMUNICAÇÃO COM O SEFAZ');
            return array(false,'ERRO: PROBLEMA NA COMUNICAÇÃO COM O SEFAZ');
        }

        if ($this->cStat != 103) {
            $this->atualizarStatus(0, $numLote, 202, $this->cStat, $this->xMotivo);
            return array(false,'Rejeição na SEFAZ');
        }

        //Lote recebido com sucesso
        $this->logNfe("Lote recebido com sucesso");
        mysql_query("UPDATE T007 
                        SET T007_Flag_Enviar = 'N',
                            T007_TX_Resposta_Codigo = 6,
                            T007_Numero_Recibo_Nfe  = '{$this->nRec}'
                    WHERE T007_Numero_Lote = '{$numLote}' and T007_C004_Id = '{$g['empresaAtual']}'");

        $numNotas = count($xmls);        
        return array(true, $numNotas);
    }

    /**    
     * Consulta Recibo NF-e
     * @param   string $numLote     
     * @return true|string
     **/
    public function consultarRecibo($numRecibo)
    {
        global $g;
        if (empty($numRecibo)) {
            return false;
        }

        $this->logNfe("Iniciando Consulta RECIBO: $numRecibo");

        // Consulta Recibo
        $retorno = $this->consultaNFReciboLote($numRecibo);

        if ($this->errorStatus) {
            return $this->errorMsg;
        }

        $this->logNfe("Retorno XML");
        $this->logNfe($this->retornoEnviaNF, true);

        $T007 = mysql_query("SELECT T007_Id, 
                                    T007_Chave_Acesso_Nfe,
                                    T007_Data_Emissao,
                                    T007_Numero_Nota_Fiscal,
                                    T007_Numero_Recibo_Nfe,
                                    T007_Numero_Lote,
                                    T007_C004_Id,
                                    T007_D024_Id,
                                    T007_Flag_ACP                                    
                                FROM T007                         
                                WHERE T007_Numero_Recibo_Nfe = '{$numRecibo}' 
                                    AND T007_C004_Id = '{$g['empresaAtual']}'");

        while ($mT007 = mysql_fetch_array($T007)) {
            //Lote em processamento, disponibilizar o consulta recibo novamente
            if ($retorno->cStat  == 105 || $retorno->cStat == 106) {
                $this->logNfe("Lote ainda em processamento");
                $this->atualizarStatus(0, $mT007['T007_Numero_Lote'], 202, $retorno->cStat, $retorno->xMotivo, $this->retornoEnviaNF);
                continue;
            }
            //Rejeição com o lote
            if ($retorno->cStat != 104) {
                $this->logNfe("Lote Rejeitado");
                $this->atualizarStatus(0, $mT007['T007_Numero_Lote'], 202, $retorno->cStat, $retorno->xMotivo, $this->retornoEnviaNF);
                continue;
            }

            $nfeRetorno = $retorno->protNFe["{$mT007['T007_Chave_Acesso_Nfe']}"];
            $this->logNfe("Chave de Acesso: {$mT007['T007_Chave_Acesso_Nfe']}");
            $this->logNfe("protNFe");
            $this->logNfe($nfeRetorno, true);

            //Verificar se nfeRetorno esta vazio, caso esteja não foi localizado o protocolo para chave em questão

            //Rejeição com a nota
            if ($nfeRetorno->cStat != 100 && $nfeRetorno->cStat != 110 && $nfeRetorno->cStat != 205 && $nfeRetorno->cStat != 301 && $nfeRetorno->cStat != 302 && $nfeRetorno->cStat != 303) {
                $this->logNfe("Nota com rejeição");
                $this->atualizarStatus($mT007['T007_Id'], 0, 202, $nfeRetorno->cStat, $nfeRetorno->xMotivo);
                $this->gravaTabelaLog(
                    $mT007['T007_Id'],
                    $mT007['T007_C004_Id'],
                    $mT007['T007_D024_Id'],
                    $this->getModeloNF(),
                    "{$nfeRetorno->cStat} - {$nfeRetorno->xMotivo}",
                    $mT007['T007_Numero_Nota_Fiscal'],
                    $mT007['T007_Chave_Acesso_Nfe'],
                    $mT007['T007_Numero_Recibo_Nfe']
                );

                $T136 = mysql_query("select * from global.T136 where T136_Numero_Erro='{$nfeRetorno->cStat}'");
                $mT136 = mysql_fetch_assoc($T136);
                if ($mT136['T136_Flag_Refazer_Chave_Acesso'] == 'S') {
                    mysql_query("UPDATE T007 SET 
                                        T007_Chave_Acesso_Nfe='',
                                        T007_Numero_Recibo_Nfe=null,
                                        T007_Data_Hora_Recebimento_Nfe='0000-00-00 00:00:00' 
                                    WHERE T007_Id='{$mT007['T007_Id']}'");
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        $this->atualizarStatus($mT007['T007_Id'], 0, 117);
                    }

                    $this->gravaTabelaLog(
                        $mT007['T007_Id'],
                        $mT007['T007_C004_Id'],
                        $mT007['T007_D024_Id'],
                        $this->getModeloNF(),
                        "Limpou chave de acesso tabela T136 (Id: {$mT136['T136_Id']})",
                        $mT007['T007_Numero_Nota_Fiscal'],
                        $mT007['T007_Chave_Acesso_Nfe'],
                        $mT007['T007_Numero_Recibo_Nfe'],
                        $this->getPastaArquivoXML() . "assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml",
                        "{$nfeRetorno->cStat}"
                    );
                }

                continue;
            }

            //Carrega o XML Assinado
            $pathXMLAssinado = $this->getPastaArquivoXML() . "assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml";
            $this->logNfe("Caminho XML Assinado: {$pathXMLAssinado}\n");
            if (!file_exists($pathXMLAssinado)) {
                $this->atualizarStatus($mT007['T007_Id'], 0, 116, "Arquivo não existe: {$pathXMLAssinado}");
                continue;
            }

            $this->logNfe("Gravando Autorizacao");
            // Adiciona Protocolo
            $this->setDataEmissao($mT007['T007_Data_Emissao']);
            $xmlAssinado = file_get_contents($pathXMLAssinado);
            $anoMesNfe = substr($this->getDataEmissao(), 0, 7);
            if (!is_dir($this->getPastaArquivoXML() . 'autorizado/' . $anoMesNfe))
                mkdir($this->getPastaArquivoXML() . 'autorizado/' . $anoMesNfe, 0777);

            $xmlNFeProtocolado = $this->adicionaProtocolo($xmlAssinado, $this->retornoEnviaNF, $mT007['T007_Chave_Acesso_Nfe']);
            $dataProtocolo = str_replace("T", " ", $nfeRetorno->dhRecbto);
            $dataProtocolo = substr($dataProtocolo, 0, 19);

            file_put_contents($this->getPastaArquivoXML() . "autorizado/{$anoMesNfe}/{$nfeRetorno->nProt}.xml", $xmlNFeProtocolado);

            //Atualiza T007
            mysql_query("UPDATE T007
                            SET T007_TX_Resposta_Extra	= '',
                                T007_TX_Resposta_Codigo = '9',
                                T007_TX_Retorno_Sefaz = '',
                                T007_Numero_Protocolo_Nfe = '{$nfeRetorno->nProt}',
                                T007_Arquivo_XML = '{$xmlNFeProtocolado}',
                                T007_Data_Hora_Protocolo_Nfe = '{$dataProtocolo}'   
                        WHERE T007_Id = '{$mT007['T007_Id']}' AND T007_C004_Id = '{$g['empresaAtual']}'");    
                        
            $this->gravaTabelaLog(
                $mT007['T007_Id'],
                $mT007['T007_C004_Id'],
                $mT007['T007_D024_Id'],
                $this->getModeloNF(),
                "{$nfeRetorno->cStat} - {$nfeRetorno->xMotivo}",
                $mT007['T007_Numero_Nota_Fiscal'],
                $mT007['T007_Chave_Acesso_Nfe'],
                $mT007['T007_Numero_Recibo_Nfe'],
                '',
                $nfeRetorno->cStat
            );
            unlink($pathXMLAssinado);   
            
            require_once('bibliotecas/classes/VEN012.php');
            $VEN012 = new VEN012();

            $VEN012->gatilhoAposTransmitirNF($mT007['T007_Id']);
            // Denegar
            if ($nfeRetorno->cStat == '301' || $nfeRetorno->cStat == '302' || $nfeRetorno->cStat == '303' || $nfeRetorno->cStat == '110' || $nfeRetorno->cStat == '205') {
                $this->logNfe("******************* Denegando nota...\n");

                $cSql_T007 = mysqli_query("select T007_Flag_ACP,	
                                                   T007_Id,	
                                                   T007_D024_Id	 
                                              from T007 
                                          left join C004 on C004_Id=T007_C004_Id 
                                              where T007_Id='{$mT007['T007_Id']}'");
                $mT007 = mysql_fetch_assoc($cSql_T007);
                if (mysql_num_rows($cSql_T007) == 1 && $mT007['T007_Flag_ACP'] == '6') {
                    // INICIO DENEGAR
                    mysqli_query("SET AUTOCOMMIT=0");
                    mysqli_query("START TRANSACTION");
                    //-- Cancelando Nota Fiscal
                    mysqli_query("update T007
                                set T007_Flag_Cancelada='S',
                                    T007_Flag_Classificacao=0,
                                    T007_Observacao_Cancelamento='NOTA FISCAL DENEGADA',
                                    T007_Flag_Denegado='S',
                                    T007_Valor_Total=0
                                where T007_Id='{$mT007['T007_Id']}'");
                    //-- Limpa o Flag de classificacao do item
                    mysqli_query("update T008 set T008_Flag_Classificacao=0 where T008_T007_Id='{$mT007['T007_Id']}'");
                    //-- Cancelando Contas a Receber 
                    mysqli_query("update T002 set T002_Flag_Cancelada='S',
                                            T002_Data_Recebimento=current_date(),
                                                T002_Observacao_2='*** NF DENEGADA ***',
                                                T002_Valor_Duplicata=0,
                                                T002_Valor_Total=0
                                    where T002_T007_Id='{$mT007['T007_Id']}'");
                    //-- Cancelando Contas a Pagar
                    mysqli_query("update T015 set T015_Flag_Cancelada='S',
                                                T015_Data_Pagamento=current_date(),
                                                T015_Observacao_2='*** NF DENEGADA ***',
                                                T015_Valor_Duplicata=0,
                                                T015_Valor_Total=0
                                    where T015_T007_Id='{$mT007['T007_Id']}'");
                    mysqli_query("update T001 set T001_Flag_Operacao='C' where T001_T007_Id='{$mT007['T007_Id']}'");
                    $cSql_T008 = mysqli_query("select D006_Flag_Estoque,
                                                T008_D009_Id,
                                                T008_Quantidade,
                                                T007_D024_Id,
                                                T008_Id,
                                                T007_Id,
                                                T008_C004_Id,
                                                T007_D006_Id,
                                                T008_Codigo_Produto,
                                                T007_Numero_Nota_Fiscal,
                                                D049_D001_Id,
                                                T008_D009_Id 
                                            from T008 
                                        left join T007 on T008_T007_Id=T007_Id 
                                        left join D006 on T007_D006_Id=D006_Id 
                                        left join D009 on D009_Id=T008_D009_Id
                                        left join D049 on D049_Id=D009_D049_Id
                                            where T008_T007_Id='{$mT007['T007_Id']}'");
                    while ($mT008 = mysql_fetch_assoc($cSql_T008)) {                                               
                        require 'bibliotecas/classes/CAD002.php';
                        $CAD002 = new \hardness\CAD002();
                        $CAD002->D001_reprocessa_historico(0, $mT008['T008_D009_Id'], 10, false);                        
                    }
                    $sqlUpdD024  = " UPDATE D024 SET";
                    $sqlUpdD024 .= " D024_Data_Primeira_Compra = T007_Primeira_Ultima_Compra(1,D024_Id),";
                    $sqlUpdD024 .= " D024_Data_Ultima_Compra = T007_Primeira_Ultima_Compra(2,D024_Id),";
                    //Function parametro = 3 retorna a empresa da ultima compra
                    $sqlUpdD024 .= " D024_C004_Id_Ultima_Compra = T007_Primeira_Ultima_Compra(3,D024_Id)";
                    $sqlUpdD024 .= " WHERE D024_Id={$mT007['T007_D024_Id']}";
                    mysqli_query($sqlUpdD024);
                    mysqli_query("COMMIT");
                    mysqli_query("SET AUTOCOMMIT=1");
                    // FIM DENEGAR)
                }
            }
            $this->logNfe("Nota Autorizada e Finalizada");
        }

        return true;
    }

    /**    
     * Valida XML contra o Schema (XSD)
     * @param   string $xml   conteudo do MXL
     * @param   string $pathXSD caminho para o schema     
     * @return true|string
     **/
    public function validarXML($xml, $pathXSD = "")
    {

        if (!is_file($pathXSD)) {
            return "Arquivo XSD não encontrado";
        }

        if (empty($xml)) {
            return "Arquivo XSD não encontrado";
        }

        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        libxml_use_internal_errors(true);

        if (!$dom->schemaValidate($pathXSD)) {
            $mensagens = '';
            foreach (libxml_get_errors() as $error) {
                $mensagens .= "Mensagem: $error->message<br>";
            }

            return $mensagens;
        }

        return true;
    }

    /**
     * Adiciona o protocolo retornado pelo SEFAZ ao XML     
     * @param   string $xmlAssinado
     * @param   string $retorno resposta do SEFAZ
     * @return  string XML com o protocolo
     * @access  public
     **/
    public function adicionaProtocolo($xmlAssinado, $retorno, $chave = '')
    {
        $ret = new \DOMDocument('1.0', 'UTF-8');
        $ret->preserveWhiteSpace = false;
        $ret->formatOutput = false;
        $ret->loadXML($retorno);
        $infProt = null;

        //limpa xml
        $xmlAssinado = preg_replace("/<\?xml.*?\?>/", "", $xmlAssinado);

        if (empty($chave)) {
            $retProt = $ret->getElementsByTagName('protNFe')->item(0) ? $ret->getElementsByTagName('protCTe')->item(0) : '';
            $infProt = $ret->getElementsByTagName('infProt')->item(0);
        } else {
            $retProt = $ret->getElementsByTagName('protNFe');
            foreach ($retProt as $prot) {
                $chNFe = $prot->getElementsByTagName('chNFe')->item(0);
                if ($chNFe->nodeValue == $chave) {
                    $infProt = $chNFe->parentNode;
                    $protocolo = $infProt->getElementsByTagName('nProt')->item(0)->nodeValue;
                    $infProt->setAttribute("Id", "ID{$protocolo}");
                    break;
                }
            }
        }

        // Verifica se é nulo 
        if (empty($retProt)) {
            return false;
        }

        $xml  = '';
        $xml .= '<nfeProc versao="'.$this->getVersaoNFe().'" xmlns="http://www.portalfiscal.inf.br/nfe">';
        $xml .= $xmlAssinado;
        $xml .= '<protNFe versao="4.00">';
        $xml .= $ret->saveXML($infProt);
        $xml .= "</protNFe>";
        $xml .= '</nfeProc>';
        return $xml;
    }

    private function atualizarStatus($T007_Id, $numLote, $codigo, $extra = '', $retornoSefaz = '', $xml = '', $removerNFeLote = false)
    {
        global $g;
        $codigo_ = mysql_real_escape_string($codigo);
        $extra_ = mysql_real_escape_string($extra);
        $retornoSefaz_ = mysql_real_escape_string($retornoSefaz);
        $xml_ = mysql_real_escape_string($xml);

        if ($T007_Id > 0) {
            $query = "UPDATE T007 SET 
                            T007_TX_Resposta_Codigo = '{$codigo_}', 
                            T007_TX_Resposta_Extra = '{$extra_}', 
                            T007_TX_Retorno_Sefaz = '{$retornoSefaz_}', 
                            T007_Arquivo_XML_Temp = '{$xml_}'";

            if ($removerNFeLote) {
                $query .= ",T007_Numero_Lote = 1,T007_Flag_Enviar = 'N'";
            }
            $query .= "WHERE T007_Id = '{$T007_Id}' AND T007_C004_Id = '{$g['empresaAtual']}';";
            mysql_query($query);
        }

        if ($numLote > 0) {
            mysql_query("UPDATE T007 SET 
                                T007_TX_Resposta_Codigo = '{$codigo_}', 
                                T007_TX_Resposta_Extra = '{$extra_}', 
                                T007_TX_Retorno_Sefaz = '{$retornoSefaz_}', 
                                T007_Arquivo_XML_Temp = '{$xml_}' 
                            WHERE T007_Numero_Lote = '{$numLote}' AND T007_C004_Id = '{$g['empresaAtual']}'");
        }

        $Erro = mysql_error();
        if (!empty($Erro))
            $this->logNfe("Atualizar Status(SQL ERROR): " . $Erro);
    }

    /**
     * Adiciona o protocolo retornado pelo SEFAZ ao XML     
     * @param   int $T007_Id
     * @param   stdClass $retorno status e mensagem
     * @return  string XML com o protocolo
     * @access  public
     **/
    private function gravaTabelaLog($T007_Id, $C004_Id, $D024_Id, $modelo, $mensagem, $numeroNotaFiscal, $chaveAcesso = '', $recibo = '', $retorno = '', $codigo = '')
    {

        if (empty($T007_Id))
            return "Não foi informado nenhum T007_Id";

        mysql_query("insert into T127 ( T127_C004_Id,
                                        T127_D024_Id,
                                        T127_Chave_Acesso_Nfe,
                                        T127_Numero_Nota_Fiscal,
                                        T127_Codigo_Retorno,
                                        T127_Numero_Recibo_Nfe,
                                        T127_Data_Transmicao,
                                        T127_Retorno_Sefaz,
                                        T127_Xml_Retorno,
                                        T127_Modelo,
                                        T127_T007_Id
                                        ) values (
                                        '{$C004_Id}',
                                        '{$D024_Id}',
                                        '{$chaveAcesso}',
                                        '{$numeroNotaFiscal}',
                                        '$codigo',
                                        '$recibo',
                                        now(),
                                        '{$mensagem}',
                                        '" . mysql_real_escape_string($retorno) . "',
                                        '{$modelo}',
                                        '{$T007_Id}')");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            // $this->atualizarStatus(19,111);            
            return false;
        }
    }

    private function logNfe($message, $printR = false)
    {
        $fileName = '/tmp/nfeLote.log';

        if (!file_exists($fileName)) {
            touch($fileName);
        }

        if ($printR == true) {
            $message = print_r($message, true);
        }

        file_put_contents($fileName, "$message\n", FILE_APPEND);
    }
}
