<?php
namespace hardness;

/********************************************
    Raquel Pazini Cardoso 19/11/2018 
    implementação do MDFe
*********************************************/

//-- comando para converter pfx em pen:
//-- openssl pkcs12 -in 1.pfx -out 1.pem -clcerts
//require_once('funcoes.class.php3');
require_once('mdfe3_ext.class.php');
class classeMDFe extends classeMDFeExt  //extends Funcoes
{
    var $arquivo_assinado='';
    var $rPrivkey = '';//resource com a chave privada
    var $rPubkey = '';//resource com a chave publica
    var $certX509='';//chave publica em string

    // retornos de erros do sistema
    var $errorStatus = false; //estado de erro
    var $mErrorMsg=array(); //mensagem de erro
    var $mErrorCod=array(); //codigo do erro


    //-- Contem os dados para debug do NuSoap
    var $debug_str = '';

    /**
     * Indicadores de schemas utilizados na construÃ§ao das mensagens SOAP
     * e na assinatura digital
     *
     * @var string
    **/
    var $URLxsi = '';
    var $URLxsd = 'http://www.w3.org/2001/XMLSchema';
    var $URLMDFe = 'http://www.portalfiscal.inf.br/MDFe';
    var $URLdsig= 'http://www.w3.org/2000/09/xmldsig#';
    var $response; //resposta do webservice

    var $isSoapNativo = true;
    
    // Metodo Construtor, serve para chamar os metodos assim que a class for instanciada
    public function __construct()
    {
        $this->setVersaoMDFe('3.00');
        $this->setTipoSoap();
        $this->setVersaoSistema('1.0');
        $this->setModeloMDFe('58');
         
        $this->setNumeroAleatorioChaveAcesso();
        $this->setOrigemProcessoEmissao('0'); // 0 - emissao com aplicativo do contribuinte
    }

    public function setTipoSoap()
    {
        if (!extension_loaded('soap') and $this->versao_mdfe=='3.00')
        {
           $this->isSoap='';
           $this->mErrorMsg[] = "Versão 3.0 precisa do Soap Nativo do PHP. Verifique!";
           $this->errorStatus = true;
        }
        elseif ($this->versao_mdfe=='3.00')
        {
           $this->isSoap='NATIVO';
        }
        else
        {
           $this->isSoap='';
           $this->mErrorMsg[] = "Problema! Versao do mdfe nao foi definida na tabela T276 ou nao foi passado para o programa que gera o xml. ".$this->versao_mdfe;
           $this->errorStatus = true;
        }

    }
    
    public function criarArquivoXML()
    {
        global $servidorId;
        $QRCode = '';
        $cClausula  = " SELECT  ";
        $cClausula .= " T276_Id, ";                                           
        $cClausula .= " T276_Emitente_Cidade_IBGE, ";                           
        $cClausula .= " T276_Data_Emissao, ";                                  
        $cClausula .= " T276_Emitente_CNPJ, ";
        $cClausula .= " T276_Serie, ";                                              
        $cClausula .= " T276_Numero_MDFe, ";                                    
        $cClausula .= " T276_Hora_Emissao, ";                                   
        $cClausula .= " T276_C004_Id, ";                                        
        $cClausula .= " T276_Emitente_Nome, ";                                  
        $cClausula .= " T276_Emitente_Nome_Fantasia, ";                         
        $cClausula .= " T276_Emitente_Rua, ";                                  
        $cClausula .= " T276_Emitente_Numero, ";                              
        $cClausula .= " T276_Emitente_Bairro, ";                           
        $cClausula .= " T276_Emitente_Cidade, ";                                     
        $cClausula .= " T276_Emitente_CEP, ";                                    
        $cClausula .= " T276_Emitente_Inscricao_Estadual, ";
        $cClausula .= " T276_Emitente_UF, ";
        $cClausula .= " T276_Emitente_UF_IBGE, ";
        $cClausula .= " T276_Flag_Transportador, ";                                   
        $cClausula .= " T276_Serie, ";
        $cClausula .= " T276_Flag_Emitente, "; 
        $cClausula .= " T276_Fuso_Horario_Emissao, "; 
        $cClausula .= " T276_Flag_Modalidade_Transporte, "; 
        $cClausula .= " descarregamentoD018.D018_UF as descarregamentoUF, "; 
        $cClausula .= " carregamentoD020.D020_Nome_Cidade as carregamentoCidade, "; 
        $cClausula .= " carregamentoD018.D018_UF as carregamentoUF, ";
        $cClausula .= " carregamentoD020.D020_Codigo_IBGE as cidadeCarregamentoIBGE, ";

        $cClausula .= " T276_Emitente_CNPJ, ";
        $cClausula .= " T276_Quantidade_NFe, ";
        $cClausula .= " T276_Valor_Carga, ";
        $cClausula .= " T276_Peso_Bruto_Total_Carga, "; 
        $cClausula .= " T276_Informacoes_Adicionais_Fisco, ";
        $cClausula .= " T276_Informacoes_Adicionais_Contribuinte, "; 
        $cClausula .= " T276_Unidade_Peso_Bruto, "; 
        $cClausula .= " T276_Quantidade_CTe, ";
        $cClausula .= " T276_Tipo_Carga, ";
        $cClausula .= " T276_Descricao_Produto_Predominante, ";
        $cClausula .= " T276_EAN_Produto_Predominante, ";
        $cClausula .= " T276_NCM_Produto_Predominante, ";
        $cClausula .= " T276_CEP_Carregamento, ";
        $cClausula .= " T276_CEP_Descarregamento, ";
        $cClausula .= " C004_RNTRC, ";
        $cClausula .= " T276_UF_Percurso ";
		$cClausula .= " FROM T276 ";  
		$cClausula .= " LEFT JOIN D020 as carregamentoD020 on carregamentoD020.D020_Id=T276_D020_Id_Carregamento ";
        $cClausula .= " LEFT JOIN D018 as descarregamentoD018 on descarregamentoD018.D018_Id=T276_D018_Id_Descarregamento ";
        $cClausula .= " LEFT JOIN D018 as carregamentoD018 on carregamentoD018.D018_Id=T276_D018_Id_Carregamento ";                                      
        $cClausula .= " LEFT JOIN C004 on C004_Id = T276_C004_Id";                                      
        $cClausula .= " WHERE T276_Id='".$this->getT276Id()."' ";
        $cClausula .= " GROUP BY T276_Id ";

        echo "\n\n\n cClausula MDF-e= \n ".$cClausula."\n\n\n\n";
        $cSql_T276 = mysqli_query($cClausula);
        echo mysql_error();
        $mT276 = mysql_fetch_array($cSql_T276);

        //-- CAMPOS NECESSARIOS PARA CRIAR A CHAVE DE ACESSO
        $this->setCodigoUFEmitente($mT276['T276_Emitente_UF_IBGE']);
        $this->setDataEmissao($mT276['T276_Data_Emissao'].'T'.$mT276['T276_Hora_Emissao'].$mT276['T276_Fuso_Horario_Emissao']);
        $this->setCnpjEmitente($mT276['T276_Emitente_CNPJ']);
        $this->setSerieMDFe($mT276['T276_Serie']);
        $this->setNumeroMDFe($mT276['T276_Numero_MDFe']);        
        $this->setChaveAcessoMDFe();
        $this->setQRCodeMDFe();
        //-- Campos do cabeçalho do MDFe e emitente
        $this->setC004Id($mT276['T276_C004_Id']);
        $this->setNomeRazaoEmitente($mT276['T276_Emitente_Nome']);
        $this->setNomeFantasiaEmitente($mT276['T276_Emitente_Nome_Fantasia']);
        $this->setLogradouroEmitente($mT276['T276_Emitente_Rua']);
        $this->setNumeroLogradouroEmitente($mT276['T276_Emitente_Numero']);
        $this->setBairroEmitente($mT276['T276_Emitente_Bairro']);
        $this->setCodigoMunicipioEmitente($mT276['T276_Emitente_Cidade_IBGE']);
        $this->setMunicipioEmitente($mT276['T276_Emitente_Cidade']);
        $this->setCEPEmitente($mT276['T276_Emitente_CEP']);
        $this->setInscricaoEstadualEmitente($mT276['T276_Emitente_Inscricao_Estadual']);
        $mT276['T276_Emitente_Inscricao_Estadual']=str_replace(array('-','/','.','_'),"",$mT276['T276_Emitente_Inscricao_Estadual']);
        $this->setTipoEmitente($mT276['T276_Flag_Emitente']);
        $this->setModalMDFe($mT276['T276_Flag_Modalidade_Transporte']);
        
        $this->setUFCarregamento($mT276['carregamentoUF']);
        $this->setUFDescarregamento($mT276['descarregamentoUF']);
        $this->setMunicipioCarregamento($mT276['carregamentoCidade']);
        $this->setCodigoMunicipioCarregamento($mT276['cidadeCarregamentoIBGE']);
        
        $this->setComplementoEmitente("");
        $this->setUFEmitente($mT276['T276_Emitente_UF']);

        //-- Criando XML
        $oNovo = new \DOMDocument("1.0", "UTF-8");

        //-- Descartar espacos em branco
        $oNovo->preserveWhiteSpace = false;

        //-- Gerar um codigo bem legivel
        $oNovo  ->formatOutput = true;

        //-- Tag Raiz
        $oMDFe = $oNovo->createElement("MDFe");
        $oMDFe->setAttribute("xmlns", "http://www.portalfiscal.inf.br/mdfe");
        $oNovo->appendChild($oMDFe);

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- 0 - TAG de grupo das informações da NF-e
        /////////////////////////////////////////////////////////////////////////////////////////
        $oA01 = $oNovo->createElement("infMDFe");

        //Informar a chave de acesso do MDF-e e precedida do literal "MDFe"
        $oA01->setAttribute("Id", $this->getChaveAcessoMDFe());
        $oMDFe->appendChild($oA01);

        //-- Versão do leiaute
        $oA01->setAttribute("versao", $this->getVersaoMDFe());

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- B - TAG de grupo das informações do MDF-e
        /////////////////////////////////////////////////////////////////////////////////////////

        // 4 ide 1 Identificação do MDF-e G 1 - 1
        $oB01 = $oNovo->createElement("ide");
        $oA01->appendChild($oB01);

        // 5 cUF 2 Código da UF do emitente do MDF-e E N 1 - 1 2 D1 Código da UF do emitente do Documento Fiscal. Utilizar a Tabela do IBGE de código de unidades da federação.
        $oB02 = $oNovo->createElement("cUF", $this->getCodigoUFEmitente());
        $oB01->appendChild($oB02);
        
        // 6 tpAmb 2 Tipo do Ambiente E N 1 - 1 1 D6 1 - Produção 2 - Homologação
        $oB03 = $oNovo->createElement("tpAmb", $this->getIdentAmbiente());
        $oB01->appendChild($oB03);

        // 7 tpEmit 2 Tipo do Emitente E N 1 - 1 1 D6 1 - Prestador de serviço de transporte 2 - Transportador de Carga Própria OBS: Deve ser preenchido com 2 para emitentes de NF-e e pelas transportadoras quando estiverem fazendo transporte de carga própria
        $oB04 = $oNovo->createElement("tpEmit", $this->getTipoEmitente());
        $oB01->appendChild($oB04);

        //tpTransp 2 Tipo do Transportador E N 0 - 1 1 D7 1 - ETC 2 - TAC 3 - CTC
        if($mT276['T276_Flag_Transportador'] < 0){
            $oB05 = $oNovo->createElement("tpTransp", $mT276['T276_Flag_Transportador']);
            $oB01->appendChild($oB05);
        }
        
        //9 mod 2 Modelo do Manifesto Eletrônico E N 1 - 1 2 D4 Utilizar o código 58 para identificação do MDF-e
        $oB06 = $oNovo->createElement("mod", $this->getModeloMDFe());
        $oB01->appendChild($oB06);

        // 10 serie 2 Série do Manifesto E N 1 - 1 1 - 3 ER32 Informar a série do documento fiscal (informar zero se inexistente).
        $oB07 = $oNovo->createElement("serie", $mT276['T276_Serie']);
        $oB01->appendChild($oB07);

        // 11 nMDF 2 Número do Manifesto E N 1 - 1 1 - 9 ER31 Número que identifica o Manifesto. 1 a 999999999.
        $oB08 = $oNovo->createElement("nMDF", $this->getNumeroMDFe());
        $oB01->appendChild($oB08);

        // 12 cMDF 2 Código numérico que compõe a Chave E N 1 - 1 8 ER40 Código aleatório gerado pelo emitente,
        $oB09 = $oNovo->createElement("cMDF", $this->getNumeroAleatorioChaveAcesso());
        $oB01->appendChild($oB09);        

        // 13 cDV 2 Digito verificador da chave de acesso do Manifesto E N 1 - 1 1 ER41 Informar o dígito de controle da chave de acesso do MDF-e, que deve ser calculado com a aplicação do algoritmo módulo 11 (base 2,9) da chave de acesso.
        $oB10 = $oNovo->createElement("cDV", $this->getDVChaveAcesso());
        $oB01->appendChild($oB10);

        // 14 modal 2 Modalidade de transporte E N 1 - 1 1 D9 1 - Rodoviário; 2 - Aéreo; 3 - Aquaviário; 4 - Ferroviário.
        $oB11 = $oNovo->createElement("modal", $this->getModalMDFe());
        $oB01->appendChild($oB11);

        // 15 dhEmi 2 Data e hora de emissão do Manifesto E C 1 - 1 21 ER1 Formato AAAA-MM-DDTHH:MM:DD TZD
        $oB12 = $oNovo->createElement("dhEmi", $this->getDataEmissao());
        $oB01->appendChild($oB12);

        // 16 tpEmis 2 Forma de emissão do Manifesto (Normal ou Contingência) E N 1 - 1 1 D6 1 - Normal; 2 - Contingência
        $oB13 = $oNovo->createElement("tpEmis", $this->getTipoDocAuxiliar());
        $oB01->appendChild($oB13);

        // 17 procEmi 2 Identificação do processo de emissão do Manifesto E N 1 - 1 1 D13 0 - emissão de MDF-e com aplicativo do contribuinte; 3- emissão MDF-e pelo contribuinte com aplicativo fornecido pelo Fisco.
        $oB14 = $oNovo->createElement("procEmi", $this->getOrigemProcessoEmissao());
        $oB01->appendChild($oB14);

        // 18 verProc 2 Versão do processo de emissão E C 1 - 1 1 - 20 ER35 Informar a versão do aplicativo emissor
        $oB15 = $oNovo->createElement("verProc", $this->getVersaoSistema());
        $oB01->appendChild($oB15);

        // 19 UFIni 2 Sigla da UF do Carregamento E C 1 - 1 2 D5 Utilizar a Tabela do IBGE de código de unidades da federação. Informar 'EX' para operações com o exterior.
        $oB16 = $oNovo->createElement("UFIni", $this->getUFCarregamento());
        $oB01->appendChild($oB16);

        // 20 UFFim 2 Sigla da UF do Descarregamento E C 1 - 1 2 D5 Utilizar a Tabela do IBGE de código de unidades da federação. Informar 'EX' para operações com o exterior.
        $oB17 = $oNovo->createElement("UFFim", $this->getUFDescarregamento());
        $oB01->appendChild($oB17);

        // 21 infMunCarrega 2 Informações dos Municípios de Carregamento G 1 - 50
        $oB18 = $oNovo->createElement("infMunCarrega");
        $oB01->appendChild($oB18);

        // 22 cMunCarrega 3 Código do Município de Carregamento E N 1 - 1 7 ER2
        $oB19 = $oNovo->createElement("cMunCarrega", $this->getCodigoMunicipioCarregamento());
        $oB18->appendChild($oB19);

        // 23 xMunCarrega 3 Nome do Município de Carregamento E C 1 - 1 2 - 60 ER35
        $oB20 = $oNovo->createElement("xMunCarrega", $this->getMunicipioCarregamento());
        $oB18->appendChild($oB20);

		if(!empty($mT276['T276_UF_Percurso'])){
            $ufPercurso = explode(",", $mT276['T276_UF_Percurso']);
            for($i=0 ; $i<count($ufPercurso); $i++){
                // 24 infPercurso 2 Informações do Percurso do MDF-e G 0 - 25
                $oB21 = $oNovo->createElement("infPercurso");
                $oB01->appendChild($oB21);        
        
                // 25 UFPer 3 Sigla das Unidades da Federação do E C 1 - 1 2 D5 Não é necessário repetir as UF de Início
                $oB22 = $oNovo->createElement("UFPer", $ufPercurso[$i]);
                $oB21->appendChild($oB22);          
            }    
        }        /////////////////////////////////////////////////////////////////////////////////////////
        //-- C - TAG de grupo das informações do Emitente do MDF-e
        /////////////////////////////////////////////////////////////////////////////////////////

        // 27 emit 1 Identificação do Emitente do Manifesto G 1 - 1
        $oC01 = $oNovo->createElement("emit");
        $oA01->appendChild($oC01);

        // 28 CNPJ 2 CNPJ do emitente E N 1 - 1 14 ER7 Informar zeros não significativos
        $oC02 = $oNovo->createElement("CNPJ", $this->getCnpjEmitente());
        $oC01->appendChild($oC02);

        // 29 IE 2 Inscrição Estadual do emitemte E N 1 - 1 2 - 14 ER30
        $oC03 = $oNovo->createElement("IE", $this->getInscricaoEstadualEmitente());
        $oC01->appendChild($oC03);

        // 30 xNome 2 Razão social ou Nome do emitente E C 1 - 1 2 - 60 ER35
        $oC04 = $oNovo->createElement("xNome", $this->getNomeRazaoEmitente());
        $oC01->appendChild($oC04);

        // 31 xFant 2 Nome fantasia do emitente E C 0 - 1 1 - 60 ER35
        if(!empty($this->nome_fantasia_emitente)){
            $oC05 = $oNovo->createElement("xFant", $this->getNomeFantasiaEmitente());
            $oC01->appendChild($oC05);    
        }

        // 30 xNome 2 Razão social ou Nome do emitente E C 1 - 1 2 - 60 ER35
        $oC06 = $oNovo->createElement("enderEmit");
        $oC01->appendChild($oC06);
        
        // 33 xLgr 3 Logradouro E C 1 - 1 2 - 60 ER35
        $oC07 = $oNovo->createElement("xLgr", $this->getLogradouroEmitente());
        $oC06->appendChild($oC07);

        // 34 nro 3 Número E C 1 - 1 1 - 60 ER35
        $oC08 = $oNovo->createElement("nro", $this->getNumeroLogradouroEmitente());
        $oC06->appendChild($oC08);
        
        // 35 xCpl 3 Complemento E C 0 - 1 1 - 60 ER35
        if(!empty($this->complemento_logradouro_emitente)){
            $oC09 = $oNovo->createElement("xCpl", $this->getComplementoEmitente());
            $oC06->appendChild($oC09);
        }

        // 36 xBairro 3 Bairro E C 1 - 1 2 - 60 ER35
        $oC10 = $oNovo->createElement("xBairro", $this->getBairroEmitente());
        $oC06->appendChild($oC10);

        // 37 cMun 3 Código do município (utilizar a tabela do IBGE), informar 9999999 para operações com o exterior. E N 1 - 1 7 ER2
        $oC11 = $oNovo->createElement("cMun", $this->getCodigoMunicipioEmitente());
        $oC06->appendChild($oC11);

        // 38 xMun 3 Nome do município, , informar EXTERIOR para operações com o exterior. E C 1 - 1 2 - 60 ER35
        $oC12 = $oNovo->createElement("xMun", $this->getMunicipioEmitente());
        $oC06->appendChild($oC12);

        // 39 CEP 3 CEP E N 0 - 1 8 ER40 Informar zeros não significativos
        if(!empty($this->cep_emitente)){
            $oC13 = $oNovo->createElement("CEP", $this->getCEPEmitente());
            $oC06->appendChild($oC13);
        }
        
        // 40 UF 3 Sigla da UF, , informar EX para operações com o exterior. E C 1 - 1 2 D5
        $oC14 = $oNovo->createElement("UF", $this->getUFEmitente());
        $oC06->appendChild($oC14);

        /*$oC15 = $oNovo->createElement("fone", "");
        $oC06->appendChild($oC15);

        $oC16 = $oNovo->createElement("email", "");
        $oC06->appendChild($oC16);*/

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- D - Informação do Modal
        /////////////////////////////////////////////////////////////////////////////////////////

        // 43 infModal 1 Informações do modal G 1 - 1
        $oD01 = $oNovo->createElement("infModal");

        //Informar a chave de acesso do MDF-e e precedida do literal "MDFe"
        $oD01->setAttribute("versaoModal", "3.00");
        $oA01->appendChild($oD01);

        // 1 rodo 0 Informações do modal Rodoviário G 1 - 1
        $oD02 = $oNovo->createElement("rodo");
        $oD01->appendChild($oD02);


        $qTemCIOT = <<<SQL
            SELECT 1
              FROM T318
             WHERE T318_T276_Id = '{$this->getT276Id()}'
               AND COALESCE(T318_CIOT, '') <> ''
             LIMIT 1
        SQL;
        $rTemCIOT = mysql_query($qTemCIOT);
        $temCIOTInformado = ($rTemCIOT && mysql_num_rows($rTemCIOT) > 0);

        if(!empty($mT276['C004_RNTRC']) || $temCIOTInformado){
            $infANTT =  $oNovo->createElement("infANTT");
            $oD02->appendChild($infANTT);
    
            // Add child elements to <infANTT>
            if (!empty($mT276['C004_RNTRC'])) {
                $RNTRC =  $oNovo->createElement("RNTRC",  $mT276['C004_RNTRC']);
                $infANTT->appendChild($RNTRC);
            }
    
            /* 25 - infPag - Informações do Pagamento do Frete
            ======================================================== */
            $qT318 = <<<SQL
                SELECT T318_Id,
                       T318_Valor_Contrato,
                       T318_Indicador_Forma_Pagamento,
                       T318_Chave_Pix,
                       T318_CNPJ_Instituicao_Pagamento_Frete,
                       D024_CNPJ,
                       D024_CPF,
                       D024_Nome_Empresa,
                       T318_Codigo_Banco,
                       T318_Codigo_Agencia,
                       T318_CIOT
                  FROM T318
             LEFT JOIN D024 ON T318_D024_Id = D024_Id
                 WHERE T318_T276_Id = '{$this->getT276Id()}'
            SQL;
            $T318 = mysql_query($qT318);
            $infCIOTJaInserido = false;
            while ($mT318 = mysql_fetch_assoc($T318)) {
                // CIOT (Ajuste SINIEF 03/2026 / NT MDF-e 2025.001):
                // gera no grupo ANTT quando informado no cadastro de pagamento do frete.
                if (!$infCIOTJaInserido && !empty($mT318['T318_CIOT'])) {
                    $ciotLimpo = preg_replace('/\D/', '', $mT318['T318_CIOT']);
                    $infCIOT = $oNovo->createElement("infCIOT");
                    $infANTT->appendChild($infCIOT);

                    $CIOT = $oNovo->createElement("CIOT", $ciotLimpo);
                    $infCIOT->appendChild($CIOT);

                    if (!empty($this->limpaCNPJouCPF($mT318['D024_CNPJ']))) {
                        $CNPJ = $oNovo->createElement("CNPJ", $this->limpaCNPJouCPF($mT318['D024_CNPJ']));
                        $infCIOT->appendChild($CNPJ);
                    } elseif (!empty($this->limpaCNPJouCPF($mT318['D024_CPF']))) {
                        $CPF = $oNovo->createElement("CPF", $this->limpaCNPJouCPF($mT318['D024_CPF']));
                        $infCIOT->appendChild($CPF);
                    }

                    $infCIOTJaInserido = true;
                }

                // Create <infContratante> element
                $infContratante =  $oNovo->createElement("infContratante");
                $infANTT->appendChild($infContratante);
        
                $CNPJ =  $oNovo->createElement("CNPJ", $this->getCnpjEmitente());
                $infContratante->appendChild($CNPJ);

                $infPag =  $oNovo->createElement("infPag");
                $infANTT->appendChild($infPag);

                $xNome =  $oNovo->createElement("xNome", $mT318['D024_Nome_Empresa']);
                $infPag->appendChild($xNome);

                if (!empty($this->limpaCNPJouCPF($mT318['D024_CNPJ']))) {
                    $CNPJ =  $oNovo->createElement("CNPJ", $this->limpaCNPJouCPF($mT318['D024_CNPJ']));
                    $infPag->appendChild($CNPJ);
                } elseif (!empty($this->limpaCNPJouCPF($mT318['D024_CPF']))) {
                    $CPF =  $oNovo->createElement("CPF", $this->limpaCNPJouCPF($mT318['D024_CPF']));
                    $infPag->appendChild($CPF);
                }

                // Grupo Comp (Componentes da informação do Pagamento do Frete)
                $qT319 = <<<SQL
                    SELECT T319_Id,
                           T319_Tipo,
                           T319_Valor,
                           T319_Descricao
                      FROM T319
                     WHERE T319_T318_Id = '{$mT318['T318_Id']}'
                SQL;
                $T319 = mysql_query($qT319);
                while ($mT319 = mysql_fetch_assoc($T319)) {
                    $Comp =  $oNovo->createElement("Comp");
                    $infPag->appendChild($Comp);

                    $tpComp =  $oNovo->createElement("tpComp", $mT319['T319_Tipo']);
                    $Comp->appendChild($tpComp);

                    $vComp =  $oNovo->createElement("vComp", $mT319['T319_Valor']);
                    $Comp->appendChild($vComp);

                    $xComp =  $oNovo->createElement("xComp", $mT319['T319_Descricao']);
                    $Comp->appendChild($xComp);
                }

                $vContrato =  $oNovo->createElement("vContrato", $mT318['T318_Valor_Contrato']);
                $infPag->appendChild($vContrato);

                $indPag =  $oNovo->createElement("indPag", $mT318['T318_Indicador_Forma_Pagamento']);
                $infPag->appendChild($indPag);

                // Grupo infPrazo (Componentes da informação do Pagamento do Frete)
                $qT320 = <<<SQL
                    SELECT T320_Id,
                           T320_Numero_Parcela,
                           T320_Data_Vencimento,
                           T320_Valor_Parcela
                      FROM T320
                     WHERE T320_T318_Id = '{$mT318['T318_Id']}'
                SQL;
                $T320 = mysql_query($qT320);
                while ($mT320 = mysql_fetch_assoc($T320)) {
                    $infPrazo =  $oNovo->createElement("infPrazo");
                    $infPag->appendChild($infPrazo);

                    $nParcela =  $oNovo->createElement("nParcela", str_pad($mT320['T320_Numero_Parcela'], 3, '0', STR_PAD_LEFT));
                    $infPrazo->appendChild($nParcela);

                    $dVenc =  $oNovo->createElement("dVenc", $mT320['T320_Data_Vencimento']);
                    $infPrazo->appendChild($dVenc);

                    $vParcela =  $oNovo->createElement("vParcela", $mT320['T320_Valor_Parcela']);
                    $infPrazo->appendChild($vParcela);
                }

                // Grupo infBanc (Informações bancárias)
                $infBanc =  $oNovo->createElement("infBanc");
                $infPag->appendChild($infBanc);

                if (!empty($mT318['T318_Codigo_Banco']) AND !empty($mT318['T318_Codigo_Agencia'])) {
                    $codBanco =  $oNovo->createElement("codBanco", $mT318['T318_Codigo_Banco']);
                    $infBanc->appendChild($codBanco);
                    $codAgencia =  $oNovo->createElement("codAgencia", $mT318['T318_Codigo_Agencia']);
                    $infBanc->appendChild($codAgencia);
                } elseif (!empty($this->limpaCNPJouCPF($mT318['T318_CNPJ_Instituicao_Pagamento_Frete']))) {
                    $CNPJIPEF =  $oNovo->createElement("CNPJIPEF", $this->limpaCNPJouCPF($mT318['T318_CNPJ_Instituicao_Pagamento_Frete']));
                    $infBanc->appendChild($CNPJIPEF);
                } elseif (!empty($mT318['T318_Chave_Pix'])) {
                    $PIX =  $oNovo->createElement("PIX", $mT318['T318_Chave_Pix']);
                    $infBanc->appendChild($PIX);
                }
            }
            /* ======================================================== */
        }

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- E - Dados do Veículo com a Tração
        /////////////////////////////////////////////////////////////////////////////////////////

        // Veículo Tração 
        $cClausula  = "SELECT ";
        $cClausula .= " T283_Versao_Modal, "; 
        $cClausula .= " T283_Identificacao_Veiculo, ";
        $cClausula .= " T283_Placa, "; 
        $cClausula .= " T283_Renavam, "; 
        $cClausula .= " T283_Tara, "; 
        $cClausula .= " T283_Capacidade_Kg, "; 
        $cClausula .= " T283_Capacidade_M3, "; 
        $cClausula .= " T283_Flag_Rodado, "; 
        $cClausula .= " T283_Flag_Carroceria, "; 
        $cClausula .= " T283_UF_Licenciamento, ";
        $cClausula .= " T283_Id ";
        $cClausula .= " FROM T283 ";
        $cClausula .= " WHERE T283_T276_Id='".$this->getT276Id()."' ";
        $cClausula .= " AND T283_Categoria_Veiculo=2 ";
        $T283_Tracao = mysqli_query($cClausula);

        if(mysql_num_rows($T283_Tracao) > 0) {
            $mT283_Tracao = mysql_fetch_assoc($T283_Tracao);
            
            $this->setPlaca($mT283_Tracao['T283_Placa']);

            // 18 veicTracao 1 Dados do Veículo com a Tração G 1 - 1
            $oE01 = $oNovo->createElement("veicTracao");
            $oD02->appendChild($oE01);

            // 19 cInt 2 Código interno do veículo E C 0 - 1 1 - 10 ER35
            if(!empty($mT283_Tracao['T283_Identificacao_Veiculo'])){
                $this->setCodigoInternoVeiculo($mT283_Tracao['T283_Identificacao_Veiculo']);
                $oE02 = $oNovo->createElement("cInt", $this->getCodigoInternoVeiculo());
                $oE01->appendChild($oE02);
            }
            // 20 placa 2 Placa do veículo E C 1 - 1 7 ER39
            $oE03 = $oNovo->createElement("placa", $this->getPlaca());
            $oE01->appendChild($oE03);

            // 21 RENAVAM 2 RENAVAM do veículo E C 0 - 1 9 - 11 ER35
            if(!empty($mT283_Tracao['T283_Renavam'])){
                $oE04 = $oNovo->createElement("RENAVAM", $mT283_Tracao['T283_Renavam']);
                $oE01->appendChild($oE04);
            }

            // 22 tara 2 Tara em KG E N 1 - 1 1 - 6 ER56
            $oE05 = $oNovo->createElement("tara", $mT283_Tracao['T283_Tara']);
            $oE01->appendChild($oE05);

            // 23 capKG 2 Capacidade em KG E N 0 - 1 1 - 6 ER56
            if(!empty($mT283_Tracao['T283_Capacidade_Kg'])){
                $oE06 = $oNovo->createElement("capKG", $mT283_Tracao['T283_Capacidade_Kg']);
                $oE01->appendChild($oE06);    
            }
            
            if(!empty($mT283_Tracao['T283_Capacidade_M3'])){
                // 24 capM3 2 Capacidade em M3 E N 0 - 1 1 - 3 ER32
                $oE07 = $oNovo->createElement("capM3", $mT283_Tracao['T283_Capacidade_M3']);
                $oE01->appendChild($oE07);
            }

            /////////////////////////////////////////////////////////////////////////////////////////
            //-- F - Dados do Condutor
            /////////////////////////////////////////////////////////////////////////////////////////

            // 33 condutor 2 Informações do(s) Condutor(s) do veículo G 1 - 10
            $cClausula  = " SELECT T278_Nome, ";
            $cClausula .= " T278_CPF ";
            $cClausula .= " FROM T278 ";
            $cClausula .= " WHERE T278_T276_Id='".$this->getT276Id()."' ";
            $cSql_T278 = mysqli_query($cClausula);
            
            // Busca todos os condutores do mdfe
            while($mT278 = mysql_fetch_array($cSql_T278)){
                $this->setCPFCondutor($mT278['T278_CPF']);
                
                // 33 condutor 2 Informações do(s) Condutor(s) do veículo G 1 - 10
                $oF01 = $oNovo->createElement("condutor");
                $oE01->appendChild($oF01);

                // 34 xNome 3 Nome do Condutor E C 1 - 1 2 - 60 ER35
                $mT278['T278_Nome'] = $this->adicionarEspape($mT278['T278_Nome']);
                $oF02 = $oNovo->createElement("xNome", $mT278['T278_Nome']);
                $oF01->appendChild($oF02);

                // 35 CPF 3 CPF do Condutor E N 1 - 1 11 ER10
                $oF03 = $oNovo->createElement("CPF", $this->getCPFCondutor());
                $oF01->appendChild($oF03);    
            }

            // 36 tpRod 2 Tipo de Rodado E N 1 - 1 2 D16 Preencher com: 01 - Truck; 02 - Toco; 03 - Cavalo Mecânico; 04 - VAN; 05 - Utilitário; 06 - Outros.
            $oE09 = $oNovo->createElement("tpRod", "0".$mT283_Tracao['T283_Flag_Rodado']);
            $oE01->appendChild($oE09);

            // 37 tpCar 2 Tipo de Carroceria E N 1 - 1 2 D17 Preencher com: 00 - não aplicável; 01 - Aberta; 02 - Fechada/Baú; 03 - Granelera; 04 - Porta Container; 05 - Sider
            $oE10 = $oNovo->createElement("tpCar", "0".$mT283_Tracao['T283_Flag_Carroceria']);
            $oE01->appendChild($oE10);

            // 38 UF 2 UF em que veículo está licenciado E C 1 - 1 2 D5 Sigla da UF de licenciamento do veículo.
            $oE11 = $oNovo->createElement("UF", $mT283_Tracao['T283_UF_Licenciamento']);
            $oE01->appendChild($oE11);
        
        }
        /////////////////////////////////////////////////////////////////////////////////////////
        //-- E1 - Dados do Veículo com a Reboque
        /////////////////////////////////////////////////////////////////////////////////////////
        $cClausula  = " SELECT ";
        $cClausula .= " T283_Versao_Modal, "; 
        $cClausula .= " T283_Identificacao_Veiculo, ";
        $cClausula .= " T283_Placa, "; 
        $cClausula .= " T283_Renavam, "; 
        $cClausula .= " T283_Tara, "; 
        $cClausula .= " T283_Capacidade_Kg, "; 
        $cClausula .= " T283_Capacidade_M3, "; 
        $cClausula .= " T283_Flag_Rodado, "; 
        $cClausula .= " T283_Flag_Carroceria, "; 
        $cClausula .= " T283_UF_Licenciamento, ";
        $cClausula .= " T283_Id ";
        $cClausula .= " FROM T283 ";
        $cClausula .= " WHERE T283_T276_Id='".$this->getT276Id()."' ";
        $cClausula .= " AND T283_Categoria_Veiculo=1 ";
        $T283_Reboque = mysqli_query($cClausula);
        
        while($mT283_Reboque = mysql_fetch_array($T283_Reboque)){
            $this->setPlaca($mT283_Reboque['T283_Placa']);

            // 18 veicTracao 1 Dados do Veículo com a Tração G 1 - 1
            $oE01 = $oNovo->createElement("veicReboque");
            $oD02->appendChild($oE01);

            // 19 cInt 2 Código interno do veículo E C 0 - 1 1 - 10 ER35
            if(!empty($mT283_Reboque['T283_Identificacao_Veiculo'])){
                $this->setCodigoInternoVeiculo($mT283_Reboque['T283_Identificacao_Veiculo']);
                $oE02 = $oNovo->createElement("cInt", $this->getCodigoInternoVeiculo());
                $oE01->appendChild($oE02);
            }
            // 20 placa 2 Placa do veículo E C 1 - 1 7 ER39
            $oE03 = $oNovo->createElement("placa", $this->getPlaca());
            $oE01->appendChild($oE03);

            // 21 RENAVAM 2 RENAVAM do veículo E C 0 - 1 9 - 11 ER35
            if(!empty($mT283_Reboque['T283_Renavam'])){
                $oE04 = $oNovo->createElement("RENAVAM", $mT283_Reboque['T283_Renavam']);
                $oE01->appendChild($oE04);
            }

            // 22 tara 2 Tara em KG E N 1 - 1 1 - 6 ER56
            $oE05 = $oNovo->createElement("tara", $mT283_Reboque['T283_Tara']);
            $oE01->appendChild($oE05);

            // 23 capKG 2 Capacidade em KG E N 0 - 1 1 - 6 ER56
            $oE06 = $oNovo->createElement("capKG", $mT283_Reboque['T283_Capacidade_Kg']);
            $oE01->appendChild($oE06);    
            
            
            if(!empty($mT283_Reboque['T283_Capacidade_M3'])){
                // 24 capM3 2 Capacidade em M3 E N 0 - 1 1 - 3 ER32
                $oE07 = $oNovo->createElement("capM3", $mT283_Reboque['T283_Capacidade_M3']);
                $oE01->appendChild($oE07);
            }

            // 37 tpCar 2 Tipo de Carroceria E N 1 - 1 2 D17 Preencher com: 00 - não aplicável; 01 - Aberta; 02 - Fechada/Baú; 03 - Granelera; 04 - Porta Container; 05 - Sider
            $oE10 = $oNovo->createElement("tpCar", "0".$mT283_Reboque['T283_Flag_Carroceria']);
            $oE01->appendChild($oE10);

            // 38 UF 2 UF em que veículo está licenciado E C 1 - 1 2 D5 Sigla da UF de licenciamento do veículo.
            $oE11 = $oNovo->createElement("UF", $mT283_Reboque['T283_UF_Licenciamento']);
            $oE01->appendChild($oE11);
        }
        /////////////////////////////////////////////////////////////////////////////////////////
        //-- G - Lacres Modal Rodoviário
        /////////////////////////////////////////////////////////////////////////////////////////

        $cClausula  = " SELECT ";
        $cClausula .= " D171_Numero_Lacre";
        $cClausula .= " FROM T286 ";
        $cClausula .= " LEFT JOIN D171 ON D171_Id=T286_D171_Id ";
        $cClausula .= " WHERE T286_T276_Id = '".$this->getT276Id()."' ";
        $cSql_T286 = mysqli_query($cClausula);

        while($mT286 = mysql_fetch_array($cSql_T286)){
            // 57 lacRodo 1 Lacres G 0 - n
            $oG01 = $oNovo->createElement("lacRodo");
            $oD02->appendChild($oG01);

            // 58 nLacre 2 Número do Lacre E C 1 - 1 1 - 20 ER35
            $oG02 = $oNovo->createElement("nLacre", $mT286['D171_Numero_Lacre']);
            $oG01->appendChild($oG02);

        }

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- H - Informações dos Documentos fiscais vinculados ao manifesto
        /////////////////////////////////////////////////////////////////////////////////////////

        // 27 emit 1 Identificação do Emitente do Manifesto G 1 - 1
        $oH01 = $oNovo->createElement("infDoc");
        $oA01->appendChild($oH01);

        $cClausula  = " SELECT COUNT(T284_Id), ";
        $cClausula .= " T284_Cidade_Descarregamento_Codigo_IBGE, ";
        $cClausula .= " T284_Cidade_Descarregamento ";
        $cClausula .= " FROM T284 ";
        $cClausula .= " WHERE T284_T276_Id = '".$this->getT276Id()."' ";
        $cClausula .= " GROUP BY T284_Cidade_Descarregamento_Codigo_IBGE";
        $cSql_T284 = mysqli_query($cClausula);

        // Procura as cidades de descarregamento
        while($mT284_Carregamento = mysql_fetch_array($cSql_T284)){
            
            // 47 infMunDescarga 2 Informações dos Municípios de descarregamento G 1 - 100
            $oH02 = $oNovo->createElement("infMunDescarga");
            $oH01->appendChild($oH02);

            // 48 cMunDescarga 3 Código do Município de Descarregamento E N 1 - 1 7 ER2
            $oH03 = $oNovo->createElement("cMunDescarga", $mT284_Carregamento['T284_Cidade_Descarregamento_Codigo_IBGE']);
            $oH02->appendChild($oH03);

            // 49 xMunDescarga 3 Nome do Município de Descarregamento E C 1 - 1 2 - 60 ER35
            $oH04 = $oNovo->createElement("xMunDescarga", $mT284_Carregamento['T284_Cidade_Descarregamento']);
            $oH02->appendChild($oH04);

            // Procura as notas contidas naquela cidade
            $cClausula  = " SELECT T284_Cidade_Descarregamento, ";
            $cClausula .= " T284_Cidade_Descarregamento_Codigo_IBGE, ";
            $cClausula .= " T284_Chave_De_Acesso_NFe, ";
            $cClausula .= " T284_Id ";
            $cClausula .= " FROM T284 ";
            $cClausula .= " WHERE T284_Cidade_Descarregamento_Codigo_IBGE = '".$mT284_Carregamento['T284_Cidade_Descarregamento_Codigo_IBGE']. "' ";
            $cClausula .= " AND T284_T276_Id = '".$this->getT276Id()."' AND T284_Flag_Tipo_Documento = '2'";
            $cSql_T284_Notas = mysqli_query($cClausula);

            while($mT284_Notas = mysql_fetch_array($cSql_T284_Notas)){
                // 73 infNFe 3 Nota Fiscal Eletronica G 0 - 4000
                $oH05 = $oNovo->createElement("infNFe");
                $oH02->appendChild($oH05);

                // 74 chNFe 4 Nota Fiscal Eletrônica E N 1 - 1 44 ER3
                $oH06 = $oNovo->createElement("chNFe", $mT284_Notas['T284_Chave_De_Acesso_NFe']);
                $oH05->appendChild($oH06);

                $cClausula  = " SELECT T281_Id, ";
                $cClausula .= " T281_Flag_Unidade_Transporte, ";
                $cClausula .= " T281_Identificacao_Unidade_Transporte, ";
                $cClausula .= " T281_Quantidade_Rateada ";
                $cClausula .= " FROM T281 ";
                $cClausula .= " WHERE T281_T284_Id = '".$mT284_Notas['T284_Id']."' ";
                $cSql_T281 = mysqli_query($cClausula);

                while($mT281 = mysql_fetch_array($cSql_T281)){
                    // 77 infUnidTransp 4 Informações das Unidades de Transporte (Carreta/Reboque/Vagão) G 0 - n Deve ser preenchido com as informações das unidades de transporte utilizadas.
                    $oH07 = $oNovo->createElement("infUnidTransp");
                    $oH05->appendChild($oH07);

                    // 78 tpUnidTransp 5 Tipo da Unidade de Transporte E N 1 - 1 1 D8 1 - Rodoviário Tração; 2 - Rodoviário Reboque; 3 - Navio; 4 - Balsa; 5 - Aeronave; 6 - Vagão; 7 - Outros
                    if(!empty($mT281['T281_Flag_Unidade_Transporte'])){
                        $oH08 = $oNovo->createElement("tpUnidTransp", $mT281['T281_Flag_Unidade_Transporte']);
                        $oH07->appendChild($oH08);
                    }
                    
                    // 79 idUnidTransp 5 Identificação da Unidade de Transporte E C 1 - 1 1 - 20 ER52 Informar a identificação conforme o tipo de unidade de transporte. Por exemplo: para rodoviário tração ou reboque deverá preencher com a placa do veículo.
                    if(!empty($mT281['T281_Identificacao_Unidade_Transporte'])){
                        $oH09 = $oNovo->createElement("idUnidTransp", $mT281['T281_Identificacao_Unidade_Transporte']);
                        $oH07->appendChild($oH09);
                    }
                    
                    $cClausula  = " SELECT ";
                    $cClausula .= " D171_Numero_Lacre ";
                    $cClausula .= " FROM T287 ";
                    $cClausula .= " LEFT JOIN D171 ON D171_Id = T287_D171_Id ";
                    $cClausula .= " LEFT JOIN T281 ON T281_Id = T287_T281_Id ";
                    $cClausula .= " LEFT JOIN T284 ON T284_Id = T281_T284_Id  ";
                    $cClausula .= " WHERE T284_T276_Id = '".$this->getT276Id()."' ";
                    $cClausula .= " AND T287_T281_Id = '".$mT281['T281_Id'] ."' ";
                    $cSql_T287_Transporte = mysqli_query($cClausula);

                    while($mT287_Transporte = mysql_fetch_array($cSql_T287_Transporte)){
                        // 80 lacUnidTransp 5 Lacres das Unidades de Transporte G 0 - n
                        $oH10 = $oNovo->createElement("lacUnidTransp");
                        $oH07->appendChild($oH10);

                        // 81 nLacre 6 Número do lacre E C 1 - 1 1 - 20 ER35
                        $oH11 = $oNovo->createElement("nLacre", $mT287_Transporte['D171_Numero_Lacre']);
                        $oH10->appendChild($oH11);
                    }

                    $cClausula  = " SELECT T282_Id, ";
                    $cClausula .= " T282_Flag_Unidade_Carga, ";
                    $cClausula .= " T282_Identificacao_Unidade_Carga, ";
                    $cClausula .= " T282_Quantidade_Rateada, ";
                    $cClausula .= " T282_T281_Id ";
                    $cClausula .= " FROM T282 ";
                    $cClausula .= " WHERE T282_T281_Id= '".$mT281['T281_Id']."' ";
                    $cSql_T282 = mysqli_query($cClausula);

                    while($mT282 = mysql_fetch_array($cSql_T282)){

                        // 82 infUnidCarga 5 Informações das Unidades de Carga (Containeres/ULD/Outros) G 0 - n Dispositivo de carga utilizada (Unit Load Device - ULD) significa todo tipo de contêiner de carga, vagão, contêiner de avião, palete de aeronave com rede ou palete de aeronave com rede sobre um iglu.
                        $oH12 = $oNovo->createElement("infUnidCarga");
                        $oH07->appendChild($oH12);    
                        
                        // 83 tpUnidCarga 6 Tipo da Unidade de Carga E N 1 - 1 1 D9 1 - Container; 2 - ULD; 3 - Pallet; 4 - Outr
                        if(!empty($mT282['T282_Flag_Unidade_Carga'])){
                            $oH13 = $oNovo->createElement("tpUnidCarga", $mT282['T282_Flag_Unidade_Carga']);
                            $oH12->appendChild($oH13);
                        }
                        
                        // 84 idUnidCarga 6 Identificação da Unidade de Carga E C 1 - 1 1 - 20 ER52 Informar a identificação da unidade de carga, por exemplo: número do container.
                        if(!empty($mT282['T282_Identificacao_Unidade_Carga'])){
                            $oH14 = $oNovo->createElement("idUnidCarga",  $mT282['T282_Identificacao_Unidade_Carga']);
                            $oH12->appendChild($oH14);
                        }
                        
                        $cClausula  = " SELECT ";
                        $cClausula .= " D171_Numero_Lacre ";
                        $cClausula .= " FROM T288 ";
                        $cClausula .= " LEFT JOIN D171 ON D171_Id = T288_D171_Id ";
                        $cClausula .= " LEFT JOIN T282 ON T282_Id = T288_T282_Id ";
                        $cClausula .= " LEFT JOIN T281 ON T281_Id = T282_T281_Id ";
                        $cClausula .= " LEFT JOIN T284 ON T284_Id = T281_T284_Id  ";
                        $cClausula .= " WHERE T284_T276_Id = '".$this->getT276Id()."' ";
                        $cClausula .= " AND T288_T282_Id = '".$mT282['T282_Id'] ."' ";
                        $cSql_T288_Carga = mysqli_query($cClausula);

                        while($mT288_Carga = mysql_fetch_array($cSql_T288_Carga)){
                            // 85 lacUnidCarga 6 Lacres das Unidades de Carga G 0 - n
                            $oH15 = $oNovo->createElement("lacUnidCarga");
                            $oH12->appendChild($oH15); 

                            // 86 nLacre 7 Número do lacre E C 1 - 1 1 - 20 ER35
                            $oH16 = $oNovo->createElement("nLacre", $mT288_Carga['D171_Numero_Lacre']);
                            $oH15->appendChild($oH16); 

                        }

                        // 87 qtdRat 6 Quantidade rateada (Peso,Volume) E N 0 - 1 3, 2 ER15 5 posições, sendo 3 inteiras e 2 decimais.
                        if(!empty($mT282['T282_Quantidade_Rateada'])){
                            $oH17 = $oNovo->createElement("qtdRat",  $mT282['T282_Quantidade_Rateada']);
                            $oH12->appendChild($oH17);
                        }
                    }    

                    // 88 qtdRat 5 Quantidade rateada (Peso,Volume) E N 0 - 1 3, 2 ER15 5 posições, sendo 3 inteiras e 2 decimais.
                    if(!empty($mT281['T281_Quantidade_Rateada'])){
                        $oH18 = $oNovo->createElement("qtdRat",  $mT281['T281_Quantidade_Rateada']);
                        $oH07->appendChild($oH18);  
                    }  
                }
            }

            // Procura os conhecimentos contidas naquela cidade
            $cClausula  = " SELECT T284_Cidade_Descarregamento, ";
            $cClausula .= " T284_Cidade_Descarregamento_Codigo_IBGE, ";
            $cClausula .= " T284_Chave_De_Acesso_CTe, ";
            $cClausula .= " T284_Id ";
            $cClausula .= " FROM T284 ";
            $cClausula .= " WHERE T284_Cidade_Descarregamento_Codigo_IBGE = '".$mT284_Carregamento['T284_Cidade_Descarregamento_Codigo_IBGE']. "' ";
            $cClausula .= " AND T284_T276_Id = '".$this->getT276Id()."' AND T284_Flag_Tipo_Documento = '1'";
            $cSql_T284_Conhecimentos = mysqli_query($cClausula);

            while($mT284_Conhecimentos = mysql_fetch_array($cSql_T284_Conhecimentos)) {
                // 53 infCTe 3 Conhecimentos de Tranporte - usar este grupo quando for prestador de serviço de transporte
                $infCTe = $oNovo->createElement("infCTe");
                $oH02->appendChild($infCTe);

                // 54 chCTe 4  Conhecimento Eletrônico
                $chCTe = $oNovo->createElement("chCTe", $mT284_Conhecimentos['T284_Chave_De_Acesso_CTe']);
                $infCTe->appendChild($chCTe);
            }

        }

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- I - Informações de Seguro da Carga
        /////////////////////////////////////////////////////////////////////////////////////////

        $cClausula  = " SELECT T279_Id, ";
        $cClausula .= " T279_Seguradora_Nome, ";
        $cClausula .= " T279_Seguradora_CNPJ, ";
        $cClausula .= " T279_Numero_Apolice ";
        $cClausula .= " FROM T279 ";
        $cClausula .= " LEFT JOIN D172 ON D172_Id=T279_D172_Id ";
        $cClausula .= " LEFT JOIN D173 ON D173_Id=T279_D172_Id ";
        $cClausula .= " WHERE T279_T276_Id= '".$this->getT276Id()."' ";
        $cSql_T279 = mysqli_query($cClausula);

        while($mT279 = mysql_fetch_array($cSql_T279)){
            $this->setCnpjSeguradora($mT279['T279_Seguradora_CNPJ']);
            // 118 seg 1 Informações de Seguro da Carga G 0 - n
            $oI01 = $oNovo->createElement("seg");
            $oA01->appendChild($oI01);

            // 119 infResp 2 Informações do responsável pelo seguro G 1 - 1
            $oI02 = $oNovo->createElement("infResp");
            $oI01->appendChild($oI02);

            // 120 respSeg 3 Responsável pelo seguro E N 1 - 1 1 - 1 D11 Preencher com: 1- Emitente do MDF-e; 2 - Responsável pela contratação do serviço de transporte (contratante) Dados obrigatórios apenas no modal Rodoviário, depois da lei 11.442/07. Para os demais modais esta informação é opcional.
            $oI03 = $oNovo->createElement("respSeg", "1");
            $oI02->appendChild($oI03);

            // 121 CNPJ 3 Número do CNPJ do responsável pelo seguro CE N 1 - 1 14 ER9 Obrigatório apenas se responsável pelo seguro for (2) responsável pela contratação do transporte - pessoa jurídica
            $oI04 = $oNovo->createElement("CNPJ", $this->getCnpjEmitente());
            $oI02->appendChild($oI04);

            // 123 infSeg 2 Informações da seguradora G 0 - 1
            $oI05 = $oNovo->createElement("infSeg");
            $oI01->appendChild($oI05);

            // 124 xSeg 3 Nome da Seguradora E C 1 - 1 1 - 30 ER35
            $mT279['T279_Seguradora_Nome'] = $this->adicionarEspape($mT279['T279_Seguradora_Nome']);
            $oI06 = $oNovo->createElement("xSeg", $mT279['T279_Seguradora_Nome']);
            $oI05->appendChild($oI06);

            // 125 CNPJ 3 Número do CNPJ da seguradora E N 1 - 1 14 ER9 Obrigatório apenas se responsável pelo seguro for (2) responsável pela contratação do transporte - pessoa jurídica
            $oI07 = $oNovo->createElement("CNPJ", $this->getCnpjSeguradora());
            $oI05->appendChild($oI07);

            if(!empty($mT279['T279_Numero_Apolice'])){
                // 126 nApol 2 Número da Apólice E C 0 - 1 1 - 20 ER35 Obrigatório pela lei 11.442/07 (RCTRC)
                $oI08 = $oNovo->createElement("nApol", $mT279['T279_Numero_Apolice']);
                $oI01->appendChild($oI08); 
            }
 
            $cClausula  = " SELECT D174_Numero_Averbacao ";
            $cClausula .= " FROM D174 ";
            $cClausula .= " WHERE D174_T279_Id = '".$mT279['T279_Id']."' ";
            $cSql_D174  = mysqli_query($cClausula);

            while($mD174 = mysql_fetch_array($cSql_D174)){
                // 127 nAver 2 Número da Averbação E C 0 - n 1 - 40 ER35 Informar as averbações do seguro
                $oI09 = $oNovo->createElement("nAver", $mD174['D174_Numero_Averbacao']);
                $oI01->appendChild($oI09);
            }
        }

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- Produto predominante (prodPred)
        /////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($mT276['T276_Tipo_Carga'])) {
            $prodPred = $oNovo->createElement("prodPred");
            $oA01->appendChild($prodPred);

            $tpCarga = $oNovo->createElement("tpCarga", $mT276['T276_Tipo_Carga']);
            $prodPred->appendChild($tpCarga);

            $xProd = $oNovo->createElement("xProd", $mT276['T276_Descricao_Produto_Predominante']);
            $prodPred->appendChild($xProd);

            if(!empty($mT276['T276_EAN_Produto_Predominante'])){
                $cEAN = $oNovo->createElement("cEAN", $mT276['T276_EAN_Produto_Predominante']);
                $prodPred->appendChild($cEAN);
            }

            if(!empty($mT276['T276_NCM_Produto_Predominante'])) {
                $NCM = $oNovo->createElement("NCM", $mT276['T276_NCM_Produto_Predominante']);
                $prodPred->appendChild($NCM);
            }

            $infLotacao = $oNovo->createElement("infLotacao");
            $prodPred->appendChild($infLotacao);
            
            // infLocalCarrega
            $infLocalCarrega = $oNovo->createElement("infLocalCarrega");
            $infLotacao->appendChild($infLocalCarrega);
    
            $CEPCarregamento = $oNovo->createElement("CEP",  preg_replace('/[^0-9]/', '', $mT276['T276_CEP_Carregamento']));
            $infLocalCarrega->appendChild($CEPCarregamento);
    
            // infLocalDescarrega
            $infLocalDescarrega = $oNovo->createElement("infLocalDescarrega");
            $infLotacao->appendChild($infLocalDescarrega);
    
            $CEPDescarregamento = $oNovo->createElement("CEP", preg_replace('/[^0-9]/', '', $mT276['T276_CEP_Descarregamento']));
            $infLocalDescarrega->appendChild($CEPDescarregamento);
        }
    
        
        /////////////////////////////////////////////////////////////////////////////////////////
        //-- J - Informações de Seguro da Carga
        /////////////////////////////////////////////////////////////////////////////////////////

        // 128 tot 1 Totalizadores da carga transportada e seus documentos fiscais G 1 - 1
        $oJ01 = $oNovo->createElement("tot");
        $oA01->appendChild($oJ01);

        if(!empty($mT276['T276_Quantidade_CTe'])){
            $qCTe = $oNovo->createElement("qCTe", $mT276['T276_Quantidade_CTe']);
            $oJ01->appendChild($qCTe);
        }

        // 130 qNFe 2 Quantidade total de NF-e relacionadas no Manifesto E N 0 - 1 1 - 6 ER44
        if(!empty($mT276['T276_Quantidade_NFe'])){
            $oJ02 = $oNovo->createElement("qNFe", $mT276['T276_Quantidade_NFe']);
            $oJ01->appendChild($oJ02);
        }


        // 132 vCarga 2 Valor total da carga / mercadorias transportadas E N 1 - 1 13, 2 ER27 15 posições, sendo 13 inteiras e 2 decimais.
        $oJ03 = $oNovo->createElement("vCarga", $mT276['T276_Valor_Carga']);
        $oJ01->appendChild($oJ03);

        // 133 cUnid 2 Codigo da unidade de medida do Peso Bruto da Carga / Mercadorias transportadas E N 1 - 1 2 D12 01 – KG; 02 - TON
        $oJ04 = $oNovo->createElement("cUnid", "0".$mT276['T276_Unidade_Peso_Bruto']);
        $oJ01->appendChild($oJ04);

        // 134 qCarga 2 Peso Bruto Total da Carga / Mercadorias transportadas E N 1 - 1 11, 4 ER21 15 posições, sendo 11 inteiras e 4 decimais.
        $oJ05 = $oNovo->createElement("qCarga", $mT276['T276_Peso_Bruto_Total_Carga']);
        $oJ01->appendChild($oJ05);

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- K - Lacres do MDF-e
        /////////////////////////////////////////////////////////////////////////////////////////

        $cClausula  = " SELECT ";
        $cClausula .= " D171_Numero_Lacre ";
        $cClausula .= " FROM T280 ";
        $cClausula .= " LEFT JOIN D171 ON D171_Id = T280_D171_Id ";
        $cClausula .= " WHERE T280_T276_Id = '".$this->getT276Id()."' ";
        $cSql_T280_MDFe = mysqli_query($cClausula);

        while($mT280_MDFe = mysql_fetch_array($cSql_T280_MDFe)){
            // 135 lacres 1 Lacres do MDF-e G 0 - n Preechimento opcional para os modais Rodoviário e Ferroviário
            $oK01 = $oNovo->createElement("lacres");
            $oA01->appendChild($oK01);

            // 136 nLacre 2 número do lacre E C 1 - 1 1 - 60 ER35
            $oK02 = $oNovo->createElement("nLacre", $mT280_MDFe['D171_Numero_Lacre']);
            $oK01->appendChild($oK02);
        }

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- K - Informações Adicionais
        /////////////////////////////////////////////////////////////////////////////////////////    

        if(!empty($mT276['T276_Informacoes_Adicionais_Fisco']) || !empty($mT276['T276_Informacoes_Adicionais_Contribuinte'])){
            // 140 infAdic 1 Informações Adicionais G 0 - 1
            $oK01 = $oNovo->createElement("infAdic");
            $oA01->appendChild($oK01);
            
            // 141 infAdFisco 2 Informações adicionais de interesse do Fisco E C 0 - 1 1 - 2000 ER35 Norma referenciada, informações complementares, etc
            $mT276['T276_Informacoes_Adicionais_Fisco'] = $this->adicionarEspape($mT276['T276_Informacoes_Adicionais_Fisco']);
            if(!empty($mT276['T276_Informacoes_Adicionais_Fisco'])){
                $oK02 = $oNovo->createElement("infAdFisco", $mT276['T276_Informacoes_Adicionais_Fisco']);
                $oK01->appendChild($oK02);
            }

            // 142 infCpl 2 Informações complementares de interesse do Contribuinte E C 0 - 1 1 - 5000 ER35
            $mT276['T276_Informacoes_Adicionais_Contribuinte'] = $this->adicionarEspape($mT276['T276_Informacoes_Adicionais_Contribuinte']);
            if(!empty($mT276['T276_Informacoes_Adicionais_Contribuinte'])){
                $oK02 = $oNovo->createElement("infCpl", $mT276['T276_Informacoes_Adicionais_Contribuinte']);
                $oK01->appendChild($oK02);
            }
        }

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- L - Informações do Responsável Técnico pela emissão do DF-e
        /////////////////////////////////////////////////////////////////////////////////////////  
        $oL01 = $oNovo->createElement("infRespTec");
        $oA01->appendChild($oL01);

        // ZD02 - Informar o CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico.
        $oL02     = $oNovo->createElement("CNPJ", "73718926000190");
        $oL01->appendChild($oL02);

        $oL03     = $oNovo->createElement("xContato", "Marcelo Paula");
        $oL01->appendChild($oL03);

        $oL04     = $oNovo->createElement("email", "suporte@hardness.com.br");
        $oL01->appendChild($oL04);

        $oL05     = $oNovo->createElement("fone", "4126261243");
        $oL01->appendChild($oL05);

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- M - Informações suplementares do MDF-e
        ///////////////////////////////////////////////////////////////////////////////////////// 
        $oM01 = $oNovo->createElement("infMDFeSupl");
        $oMDFe->appendChild($oM01);

        $oM02 = $oNovo->createElement("qrCodMDFe", $this->getQRCodeMDFe());
        $oM01->appendChild($oM02);


        ////////////////////////////////////////////////////////////////////
        //-- MONTA A ESTRUTURA XML PARA A ASSINATURA
        ////////////////////////////////////////////////////////////////////
        
        $oXS01 = $oNovo->createElement('Signature');
        $oXS01->setAttribute("xmlns", "http://www.w3.org/2000/09/xmldsig#");
        $oMDFe->appendChild($oXS01);

        $oXS02 = $oNovo->createElement('SignedInfo');
        $oXS01->appendChild($oXS02);

        $oXS03 = $oNovo->createElement('CanonicalizationMethod');
        $oXS03->setAttribute("Algorithm","http://www.w3.org/TR/2001/REC-xml-c14n-20010315");
        $oXS02->appendChild($oXS03);

        $oXS05  = $oNovo->createElement('SignatureMethod');
        $oXS05->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        $oXS02->appendChild($oXS05);

        $oXS07 = $oNovo->createElement('Reference');
        $oXS07->setAttribute("URI", '#'.$this->getChaveAcessoMDFe());
        $oXS02->appendChild($oXS07);

        $oXS10 = $oNovo->createElement('Transforms');
        $oXS07 -> appendChild($oXS10);

        $oXS12 = $oNovo->createElement('Transform');
        $oXS12  -> setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $oXS10-> appendChild($oXS12);

        $oXS12 = $oNovo->createElement('Transform');
        $oXS12  -> setAttribute('Algorithm','http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $oXS10-> appendChild($oXS12);

        $oXS15  = $oNovo->createElement('DigestMethod');
        $oXS15->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $oXS07->appendChild($oXS15);

        $oXS17 = $oNovo->createElement('DigestValue',"");
        $oXS07->appendChild($oXS17);

        $oXS18 = $oNovo->createElement('SignatureValue',"");
        $oXS01->appendChild($oXS18);

        $oXS19 = $oNovo->createElement('KeyInfo');
        $oXS01->appendChild($oXS19);

        $oXS20 = $oNovo->createElement('X509Data');
        $oXS19->appendChild($oXS20);

        $oXS21 = $oNovo->createElement('X509Certificate',$this->certX509);
        $oXS20->appendChild($oXS21);

        //-- Grava o arquivo com os dados da nota fiscal sem assinatura digital
        $oNovo->normalize();
        $oNovo->save($this->getNomeArquivoXML());
        unset($oNovo);
        
        //-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração
        $conteudo = file_get_contents($this->getNomeArquivoXML());
        $conteudo = str_replace(array(chr(13),chr(10),"\r","\n","\r\n")," ",$conteudo);
        $conteudo = str_replace("  ","",$conteudo);
        $conteudo = str_replace("> <","><",$conteudo);
        $conteudo = str_replace(" <","<",$conteudo);
        $conteudo = str_replace("> ",">",$conteudo);
        // Remover acentos

        $conteudo = $this->removerAcentos($conteudo); 
        file_put_contents($this->getNomeArquivoXML(), $conteudo); 
    }
}

?>
