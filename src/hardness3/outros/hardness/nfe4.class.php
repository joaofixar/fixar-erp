<?php
//-- comando para converter pfx em pen:
//-- openssl pkcs12 -in 1.pfx -out 1.pem -clcerts
//require_once('funcoes.class.php3');
require_once('nfe4_ext.class.php');
class classeNFe extends classeNFeExt  //extends Funcoes
{
     var $arquivo_assinado='';
     var $rPrivkey = '';//resource com a chave privada
     var $rPubkey = '';//resource com a chave publica
     var $certX509='';//chave publica em string
     
     // retornos de erros do sistema
     var $errorStatus = false; //estado de erro
     var $mErrorMsg=array(); //mensagem de erro
     var $mErrorCod=array(); //codigo do erro
     var $vTotTrib = 0;


     //-- Contem os dados para debug do NuSoap
     var $debug_str = '';

        /**
         * Indicadores de schemas utilizados na construÃ§ao das mensagens SOAP
         * e na assinatura digital
         *
         * @var string
        **/
     var $URLxsi = '';
     //var $URLxsi = 'http://www.w3.org/2001/XMLSchema-instance';
     var $URLxsd = 'http://www.w3.org/2001/XMLSchema';
     var $URLnfe = 'http://www.portalfiscal.inf.br/nfe';
     var $URLdsig= 'http://www.w3.org/2000/09/xmldsig#';
     var $response; //resposta do webservice
     
     var $isSoapNativo = true;
    
	// Metodo Construtor, serve para chamar os metodos assim que a class for instanciada
	public function __construct($T007_Flag_ACP=false)
	{
         $this->setVersaoNFe('4.00');
         $this->setTipoSoap();
         $this->setVersaoSistema('3.0');
         
         if($T007_Flag_ACP=='8'){
            $this->setModeloNF('65');
         }else{
            $this->setModeloNF('55');   
         }
         $this->setNumeroAleatorioChaveAcesso();
         $this->setOrigemProcessoEmissao('0'); // 0 - emissao com aplicativo do contribuinte
	}

    public function setTipoSoap()
    {
        if (!extension_loaded('soap') and ($this->versao_nfe=='2.00' || $this->versao_nfe=='3.10' || $this->versao_nfe=='4.00'))
        {
           $this->isSoap='';
           $this->mErrorMsg[] = "Versão 2.0 precisa do Soap Nativo do PHP. Verifique!";
           $this->errorStatus = true;
        }
        elseif ($this->versao_nfe=='1.10')
        {
           $this->mErrorMsg[] = "Versão 1.10 nao funciona com do Soap Nativo do PHP. Verifique!";
           $this->errorStatus = true;
        }
        elseif ($this->versao_nfe=='2.00' || $this->versao_nfe=='3.10' || $this->versao_nfe=='4.00')
        {
           $this->isSoap='NATIVO';
        }
        else
        {
           $this->isSoap='';
           $this->mErrorMsg[] = "Problema! Versao da nota fiscal nao foi definida na tabela T007 ou nao foi passado para o programa que gera o xml. ".$this->versao_nfe;
           $this->errorStatus = true;
        }

    }
    
    public function criarArquivoXML()
    {
        global $servidorId;
        $cClausula  = " select ";
        $cClausula .= " *, ";
        $cClausula .= " T007_Empresa_Usuaria_CRT, ";
        $cClausula .= " T007_Numero_Recibo_Nfe, ";
        $cClausula .= " T007_Flag_Entrada_Saida, ";
        $cClausula .= " T007_Numero_Nota_Fiscal, ";
        $cClausula .= " T007_Numero_Nota_Devolvida, ";
        $cClausula .= " T007_D024_Id, ";
        $cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
        $cClausula .= " T007_Serie, ";
        $cClausula .= " T007_Data_Emissao, ";
        $cClausula .= " T007_Data_Saida, ";
        $cClausula .= " T007_Hora_Saida, ";
        $cClausula .= " T007_Flag_ACP, ";
        $cClausula .= " T007_Empresa_Usuaria_Nome, ";
        $cClausula .= " T007_Empresa_Usuaria_Endereco, ";
        $cClausula .= " T007_Empresa_Usuaria_Numero, ";
        $cClausula .= " T007_Empresa_Usuaria_Bairro, ";
        $cClausula .= " T007_Empresa_Usuaria_Cep, ";
        $cClausula .= " T007_Empresa_Usuaria_Cidade, ";
        $cClausula .= " T007_Empresa_Usuaria_Uf, ";
        $cClausula .= " T007_Empresa_Usuaria_Codigo_Estado_IBGE, ";
        $cClausula .= " T007_Empresa_Usuaria_Codigo_Cidade_IBGE, ";
        $cClausula .= " T007_Empresa_Usuaria_Telefone, ";
        $cClausula .= " T007_Empresa_Usuaria_Cnpj, ";
        $cClausula .= " T007_Empresa_Usuaria_Inscricao_Estadual, ";
        $cClausula .= " T007_Empresa_Usuaria_CNAE_Fiscal, ";
        $cClausula .= " T007_Inscricao_Municipal, ";
        $cClausula .= " T007_Cliente_CNPJ_CPF, ";
        $cClausula .= " T007_Cliente_Inscricao_Estadual, ";
        $cClausula .= " T007_Cliente_Inscricao_Suframa, ";
        $cClausula .= " T007_Cliente_Razao, ";
        $cClausula .= " T007_Cliente_Endereco, ";
        $cClausula .= " T007_Cliente_Endereco_Numero, ";
        $cClausula .= " T007_Cliente_Bairro, ";
        $cClausula .= " T007_Cliente_CEP, ";
        $cClausula .= " T007_Cliente_Cidade, ";
        $cClausula .= " T007_Cliente_Codigo_Cidade_IBGE, ";
        $cClausula .= " T007_Cliente_Telefone, ";
        $cClausula .= " T007_Cliente_Estado, ";
        $cClausula .= " T007_Cliente_Codigo_Estado_IBGE, ";
        $cClausula .= " T007_Cliente_Complemento, ";
        $cClausula .= " T007_Cliente_Pais, ";
        $cClausula .= " T007_Cliente_Codigo_Pais, ";
        $cClausula .= " D024_E_Mail, ";
        $cClausula .= " T007_Valor_Total_Base_ICMS_Impresso, ";
        //$cClausula .= " round(sum(if(T008_D059_Id!=8,T008_Valor_Base_ICMS_Impresso,0)),2) T007_Valor_Total_Base_ICMS_Impresso, ";
        //$cClausula .= " round(sum(if(T008_D059_Id!=8,T008_Valor_ICMS,0)),2) T007_Valor_Total_ICMS, ";
        $cClausula .= " T007_Valor_Total_ICMS, ";
        $cClausula .= " T007_Valor_Base_ICMS_Substituicao, ";
        $cClausula .= " T007_Valor_ICMS_Substituicao_Retencao, ";
        $cClausula .= " T007_Valor_Total_Produtos, ";
        $cClausula .= " T007_Valor_Frete, ";
        $cClausula .= " T007_Valor_Seguro, ";
        $cClausula .= " T007_Valor_Desconto, ";
        $cClausula .= " T007_Valor_Total_IPI, ";
        $cClausula .= " T007_Valor_Total_Imposto_Importacao, ";
        $cClausula .= " T007_Valor_Total_PIS, ";
        $cClausula .= " T007_Valor_Total_COFINS, ";
        $cClausula .= " T007_Valor_Despesas_Acessorias, ";
        $cClausula .= " T007_Valor_Total, ";
        $cClausula .= " T007_Flag_Frete, ";
        $cClausula .= " T007_Nome_Transportadora, ";
        $cClausula .= " T007_Transportadora_CNPJ, ";
        $cClausula .= " T007_Transportadora_Inscricao_Estadual, ";
        $cClausula .= " T007_Transportadora_Cidade, ";
        $cClausula .= " T007_Transportadora_Estado, ";
        $cClausula .= " T007_Transportadora_Endereco, ";
        $cClausula .= " T007_Transportadora_Placa_Veiculo, ";
        $cClausula .= " T007_Estado_Veiculo, ";
        $cClausula .= " T007_Quantidade_Volumes, ";
        $cClausula .= " T007_Especie, ";
        $cClausula .= " T007_Marca, ";
        $cClausula .= " T007_Numero_Caixas, ";
        $cClausula .= " T007_Peso_Bruto, ";
        $cClausula .= " T007_Peso_Liquido, ";
        $cClausula .= " T007_Observacao_Nota_Fiscal_1, ";
        $cClausula .= " T007_Observacao_Nota_Fiscal_2, ";
        $cClausula .= " T007_Flag_Contingencia, ";
        $cClausula .= " T007_Flag_Finalidade_NFe, ";
        $cClausula .= " T007_Ambiente_NFe, ";
        $cClausula .= " T007_Flag_Inclusao_Manual, ";
        $cClausula .= " T007_Origem_Chave_Acesso, ";
        $cClausula .= " T007_Origem_UF, ";
        $cClausula .= " T007_Origem_Ano_Mes, ";
        $cClausula .= " T007_Origem_Cnpj, ";
        $cClausula .= " T007_Origem_Modelo, ";
        $cClausula .= " T007_Origem_Serie, ";
        $cClausula .= " T007_Exportacao_UF_Embarque, ";
        $cClausula .= " T007_Exportacao_Local_Embarque, ";
        $cClausula .= " T007_Valor_Total_FCP, ";
        $cClausula .= " T007_Valor_Total_Partilha_ICMS_Destino, ";
        $cClausula .= " T007_Valor_Total_Partilha_ICMS_Origem, ";
        $cClausula .= " T007_Id, ";
        $cClausula .= " C004_Ambiente_NFe, ";
        $cClausula .= " C020_Imprimir_Marca_NF, ";
        $cClausula .= " C004_Id, ";
        $cClausula .= " D024_Flag_Contribuinte, ";
        $cClausula .= " T007_Valor_Total_IPI_Devolucao ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " left join C020 on C020_C004_Id=C004_Id ";
        $cClausula .= " left join D024 on D024_Id=T007_D024_Id ";
        $cClausula .= " left join D053 on D053_D018_Id=D024_D018_Id AND D053_C004_Id=T007_C004_Id ";
        $cClausula .= " left join T008 on T008_T007_Id=T007_Id ";
        $cClausula .= " where T007_Id='".$this->getT007Id()."' ";
        $cClausula .= " group by T007_Id ";

        $cSql_T007 = mysql_query($cClausula);
        echo mysql_error();
        $mT007 = mysql_fetch_array($cSql_T007);

        if($servidorId == 'NF'){
	       $indFinal = ($mT007['D024_Flag_Revenda_Consumidor'] == '1') ? '0' : '1';
        } else {
            $indFinal = ($mT007['D024_Flag_Contribuinte'] == '0' || $mT007['D024_Flag_Contribuinte'] == '1') ? '1' : '0';
        }

        //Variaveis da configuração global por empresa
        $mC031 = array();
        $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$mT007['C004_Id']}';");
        while($rowC031 = mysql_fetch_assoc($sqlC031)){
            $mC031[$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $mC031[$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }

        if (!empty($mT007[T007_Numero_Recibo_Nfe]))
        {
           //$this->mErrorMsg[] = "Esta nota já foi transmitida, não pode ser transmitida novamente.";
           //$this->errorStatus = true;
        }
        if (empty($mT007[T007_Cliente_Pais]))
        {
           $this->mErrorMsg[] = "Necessário preenchimento do campo País";
           $this->errorStatus = true;
        }
        if (empty($mT007[T007_Cliente_Codigo_Pais]))
        {
           $this->mErrorMsg[] = "Necessário preenchimento do campo CÓDIGO do País";
           $this->errorStatus = true;
        }

        if ($this->versao_nfe!='1.10' and $mT007[T007_Empresa_Usuaria_CRT]!=1 and $mT007[T007_Empresa_Usuaria_CRT]!=2 and $mT007[T007_Empresa_Usuaria_CRT]!=3)
        {
           $this->mErrorMsg[] = "CRT Empresa Usuaria não definido";
           $this->errorStatus = true;
        }

        //-- CAMPOS NECESSARIOS PARA CRIAR A CHAVE DE ACESSO
        if($mC031['emitirNFCDataHoraAtual'] == 'S' && $this->getModeloNF()=='65'){
            $this->setDataEmissao(date("Y-m-d").'T'.date("H:i:sP"));
            mysql_query("UPDATE T007 SET T007_Data_Emissao = CURRENT_DATE(), T007_Hora_Emissao = CURRENT_TIME() WHERE T007_Id = '".$this->getT007Id()."'");
        } else {
            $this->setDataEmissao($mT007['T007_Data_Emissao'].'T'.$mT007['T007_Hora_Emissao'].$mT007['T007_Fuso_Horario_Emissao']);
        }
        $this->setDataSaida($mT007['T007_Data_Saida'].'T'.$mT007['T007_Hora_Saida'].$mT007['T007_Fuso_Horario_Saida']);
        $this->setCodigoUFEmitente($mT007[T007_Empresa_Usuaria_Codigo_Estado_IBGE]);
        $this->setCnpjEmitente($mT007[T007_Empresa_Usuaria_Cnpj]);
        $this->setSerieNF($mT007[T007_Serie]);
        $this->setNumeroNotaFiscal($mT007[T007_Numero_Nota_Fiscal]);
        $this->D024_Flag_Pessoa_Fisica_Juridica = $mT007[D024_Flag_Pessoa_Fisica_Juridica];

        //-- Dados do cabecalho da nota
        
        $cClausula  = " select ";
        $cClausula .= " D006_Flag_Estado, ";
        $cClausula .= " D006_Descricao ";
        $cClausula .= " from T008 ";
        $cClausula .= " left join D006 on D006_Id=T008_D006_Id ";
        $cClausula .= " where T008_T007_Id='".$this->getT007Id()."' ";
        $cClausula .= " group by T008_D006_Id ";
        $cSql_T008 = mysql_query($cClausula);
        echo mysql_error();
        
        unset($mDescricao_CFOP);
        while ($mT008 = mysql_fetch_array($cSql_T008))
        {
               $mDescricao_CFOP[] = $mT008[D006_Descricao];
               $cD006_Flag_Estado = $mT008[D006_Flag_Estado];
        }
        $cDescricao_CFOP=implode(" - ",$mDescricao_CFOP);
        $cDescricao_CFOP=substr($cDescricao_CFOP,0,60);

        $this->setDescNatOperacao($cDescricao_CFOP);
        $this->setFormaPagamento('1');

        if($this->getModeloNF()=='55'){
            $this->setTipoDANFE('1'); //-- 1-Retrato/ 2-Paisagem
        } else {
            $this->setTipoDANFE('4'); //-- 1-Retrato/ 2-Paisagem
        }

        $this->setC004Id($mT007['T007_C004_Id']); 

        //-- 1 - NF-e normal 2 - NF-e complementar 3 – NF-e de ajuste
        $this->setFinalidadeNFe($mT007['T007_Flag_Finalidade_NFe']);        
        
        $mT007[T007_Empresa_Usuaria_Inscricao_Estadual]=str_replace(array('-','/','.','_'),"",$mT007[T007_Empresa_Usuaria_Inscricao_Estadual]);
        $mT007[T007_Cliente_Inscricao_Estadual]=str_replace(array('-','/','.','_'),"",$mT007[T007_Cliente_Inscricao_Estadual]);
        $mT007[T007_Transportadora_Inscricao_Estadual]=str_replace(array('-','/','.','_'),"",$mT007[T007_Transportadora_Inscricao_Estadual]);
        $mT007[T007_Cliente_Telefone]=str_replace(array('-','/','.','_', ' '),"",$mT007[T007_Cliente_Telefone]);
                
        //-- Dados do emitente
        $this->setNomeRazaoEmitente($mT007[T007_Empresa_Usuaria_Nome]);
        $this->setNomeFantasiaEmitente($mT007[T007_Empresa_Usuaria_Nome]);
        $this->setLogradouroEmitente($mT007[T007_Empresa_Usuaria_Endereco]);
        $this->setNumeroLogradouroEmitente($mT007[T007_Empresa_Usuaria_Numero]);
        $this->setComplementoEmitente("");
        $this->setBairroEmitente($mT007[T007_Empresa_Usuaria_Bairro]);
        $this->setCodigoMunicipioEmitente($mT007[T007_Empresa_Usuaria_Codigo_Cidade_IBGE]);
        $this->setMunicipioEmitente($mT007[T007_Empresa_Usuaria_Cidade]);
        $this->setUFEmitente($mT007[T007_Empresa_Usuaria_Uf]);
        $this->setCEPEmitente($mT007[T007_Empresa_Usuaria_Cep]);
        $this->setTelefoneEmitente($mT007[T007_Empresa_Usuaria_Telefone]);
        $this->setInscricaoEstadualEmitente($mT007[T007_Empresa_Usuaria_Inscricao_Estadual]);
        $this->setInscricaoEstadualSubTribEmitente("{$mT007['D053_Inscricao_Estadual_ST']}");
        $this->setInscricaoMunicipalEmitente($mT007[T007_Inscricao_Municipal]);
        $this->setCNAEEmitente($mT007[T007_Empresa_Usuaria_CNAE_Fiscal]);

        //-- Dados do destinatario
        $this->setCnpjCpfDestinatario($mT007[T007_Cliente_CNPJ_CPF]);
        $this->setNomeDestinatario($mT007[T007_Cliente_Razao]); 
        //$this->setNomeDestinatario("NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL");
        $this->setLogradouroDestinatario($mT007[T007_Cliente_Endereco]);
        $this->setNumeroLogradouroDestinatario($mT007[T007_Cliente_Endereco_Numero]);
        $this->setComplementoDestinatario($mT007[T007_Cliente_Complemento]);
        $this->setBairroDestinatario($mT007[T007_Cliente_Bairro]);
        $this->setCodigoMunicipioDestinatario($mT007[T007_Cliente_Codigo_Cidade_IBGE]);
        $this->setMunicipioDestinatario($mT007[T007_Cliente_Cidade]);
        $this->setUFDestinatario($mT007[T007_Cliente_Estado]);
        $this->setCEPDestinatario($mT007[T007_Cliente_CEP]);
        $this->setTelefoneDestinatario($mT007[T007_Cliente_Telefone]);
        $this->setInscricaEstadualDestinatario($mT007[T007_Cliente_Inscricao_Estadual]);
        $this->setInscricaSuframaDestinatario($mT007[T007_Cliente_Inscricao_Suframa]);
        
        //-- VALIDACOES FRETE
        $nFlagFrete = $mT007[T007_Flag_Frete];
  	    if ($this->versao_nfe=='1.10')
        {
            if ($mT007[T007_Flag_Frete]==2)
            {
                $nFlagFrete=1;
            }
            else
            {
                $nFlagFrete=0;
            }
        }
        
       //-- FAZ A VALIDACAO NOS DADOS DO TRANSPORTADOR CASO EXISTAM DADOS DO TRANSPORTADOR
       $mT007[T007_Transportadora_CNPJ]=$this->validarCnpjTransportadora($mT007[T007_Transportadora_CNPJ]);
       $mT007[T007_Nome_Transportadora]=$this->validarNomeTransportadora($mT007[T007_Nome_Transportadora]);
       $mT007[T007_Transportadora_Inscricao_Estadual]=$this->validarInscricaoEstadualTransportadora($mT007[T007_Transportadora_Inscricao_Estadual]);
       $mT007[T007_Transportadora_Endereco]=$this->validarEnderecoTransportadora($mT007[T007_Transportadora_Endereco]);
       $mT007[T007_Transportadora_Cidade]=$this->validarCidadeTransportadora($mT007[T007_Transportadora_Cidade]);
       $mT007[T007_Transportadora_Estado]=$this->validarEstadoTransportadora($mT007[T007_Transportadora_Estado]);

        $mT007[T007_Observacao_Nota_Fiscal_1]=$this->validarObsFisco($mT007[T007_Observacao_Nota_Fiscal_1]);
        $mT007[T007_Observacao_Nota_Fiscal_2]=$this->validarObsContribuinte($mT007[T007_Observacao_Nota_Fiscal_2]);
        
        $this->setChaveAcessoNFe();

   		//-- Criando XML
		$oNovo = new DOMDocument("1.0", "UTF-8");
		
		//-- Descartar espacos em branco
		$oNovo->preserveWhiteSpace = false;

		//-- Gerar um codigo bem legivel
		$oNovo  ->formatOutput = true;

		//-- Tag Raiz
        $oNFe = $oNovo->createElement("NFe");
		$oNFe->setAttribute("xmlns", "http://www.portalfiscal.inf.br/nfe");
		$oNovo->appendChild($oNFe);

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- A - Dados da Nota Fiscal eletrônica
        /////////////////////////////////////////////////////////////////////////////////////////
        
        //-- TAG de grupo das informações da NF-e
        $oA01 = $oNovo->createElement("infNFe");        
		//$oA01->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
		
		//-- Identificador da TAG a ser assinada
		//-- informar a chave de acesso da NFe precedida do literal ‘NFe’
		//-- A Chave de Acesso da Nota Fiscal Eletrônica é representada por uma seqüência de 44
        //-- caracteres numéricos, representados da seguinte forma:
        //-- Código da UF (02) + AAMM da emissão (04) + CNPJ do Emitente (14) + Modelo Série Número da NF-e (02) + Código Numérico (09) + DV (01)
		$oA01->setAttribute("Id", $this->getChaveAcessoNFe());
		$oNFe->appendChild($oA01);
		
		//-- Versão do leiaute
		$oA01->setAttribute("versao", $this->getVersaoNFe());



        /////////////////////////////////////////////////////////////////////////////////////////
        //-- B - Identificação da Nota Fiscal eletrônica
        /////////////////////////////////////////////////////////////////////////////////////////

        //-- TAG de grupo das informações de identificação da NF-e
        $oB01     = $oNovo->createElement("ide");
		$oA01->appendChild($oB01);
		

        //-- Código da UF do emitente do Documento Fiscal.
        //-- Utilizar a Tabela do IBGE de código de unidades da federação (Anexo IV - Tabela de UF, Município e País).
        $oB02     = $oNovo->createElement("cUF",$this->getCodigoUFEmitente());
		$oB01->appendChild($oB02);
		
        //-- Código Numérico que compõe a Chave de Acesso
        //-- Número aleatório gerado pelo emitente para cada NF-e para evitar acessos indevidos da NF-e.
        $oB03     = $oNovo->createElement("cNF",$this->getNumeroAleatorioChaveAcesso());
		$oB01->appendChild($oB03);
		
        //-- Descrição da Natureza da Operação
        //-- Informar a natureza da operação de que decorrer a saída ou a entrada, tais como: venda, compra, transferência, devolução,
        //-- importação, consignação, remessa (para fins de demonstração, de industrialização ou outra), conforme
        $oB04   = $oNovo->createElement("natOp",$this->getDescNatOperacao());
		$oB01->appendChild($oB04);

        //-- Código do Modelo do Documento Fiscal
        //-- Utilizar o código 55 para identificação da NF-e, emitida em substituição ao modelo 1 ou 1A.
        $oB06     = $oNovo->createElement("mod",$this->getModeloNF());
		$oB01->appendChild($oB06);
		
        //-- Série do Documento Fiscal
        //-- informar 0 (zero) para série única. No paraná não pode ser informado zero devido ao sintegra
        //-- Mudar a série para o numero sequente, quando o numero de nota ultrapassar  999.999.999
        $oB07   = $oNovo->createElement("serie",$mT007[T007_Serie]);
		$oB01->appendChild($oB07);

        //-- Número do Documento Fiscal.
        $oB08     = $oNovo->createElement("nNF",$this->getNumeroNotaFiscal());
		$oB01->appendChild($oB08);
		
        //-- Data de emissão do Documento Fiscal
        //-- Formato “AAAA-MM-DDThh:mm:ssTZD”
        $oB09    = $oNovo->createElement("dhEmi",$this->getDataEmissao());
		$oB01->appendChild($oB09);
		
        //-- Data de Saída ou da Entrada da Mercadoria/Produto
        //-- Formato “AAAA-MM-DDThh:mm:ssTZD”
        if($this->getModeloNF()=='55'){
            $oB10 = $oNovo->createElement("dhSaiEnt", $this->getDataSaida());
    		$oB01->appendChild($oB10);
        }

		
  	    if ($this->versao_nfe!='1.10')
		{
            //-- Hora de Saída ou da Entrada da Mercadoria/Produto
            //-- Formato “HH:MM:SS” (v.2.0)
            //$B10a = $oNovo->createElement("hSaiEnt",$mT007[T007_Hora_Saida]);
    		//$oB01->appendChild($B10a);
        }
        
        //-- Tipo do Documento Fiscal
        //-- 0-entrada / 1-saída
        if ($mT007[T007_Flag_Entrada_Saida]=='E')
        {
            $cFlag_Entrada_Saida='0';
        }
        else
        {
            $cFlag_Entrada_Saida='1';
        }
        $oB11    = $oNovo->createElement("tpNF",$cFlag_Entrada_Saida);
		$oB01->appendChild($oB11);

        $oB11a    = $oNovo->createElement("idDest", $mT007['T007_Flag_Destino_Operacao']);
        $oB01->appendChild($oB11a);
		
        //-- Código do Município de Ocorrência do Fato Gerador
        //-- Informar o município de ocorrência do fato gerador do ICMS. Utilizar a Tabela do IBGE (Anexo IV - Tabela de UF, Município e País)
        $oB12  = $oNovo->createElement("cMunFG",$this->getCodigoMunicipioEmitente());
		$oB01->appendChild($oB12);
        
        //-- Formato de Impressão do DANFE
        //-- 1-Retrato/ 2-Paisagem
        $oB21   = $oNovo->createElement("tpImp",$this->getTipoDANFE());
		$oB01->appendChild($oB21);


        //-- 1 – Normal – emissão normal;
        //-- 2 – Contingência FS – emissão em contingência com impressão do DANFE em Formulário de Segurança;
        //-- 3 – Contingência SCAN – emissão em contingência no Sistema de Contingência do Ambiente Nacional– SCAN;
        //-- 4 – Contingência DPEC - emissão em contingência com envio da Declaração Prévia de Emissão em Contingência – DPEC;
        //-- 5 – Contingência FS-DA - emissão em contingência com impressão do DANFE em Formulário de Segurança para Impressão de
        //-- Documento Auxiliar de Documento Fiscal Eletrônico (FS-DA).
        $oB22   = $oNovo->createElement("tpEmis",$this->getTipoDocAuxiliar());
		$oB01->appendChild($oB22);

        //-- Dígito Verificador da Chave de Acesso da NF-e
        //-- Informar o DV da Chave de Acesso da NF-e, o DV será calculado com a aplicação do algoritmo módulo 11 (base 2,9) da Chave de Acesso
        $oB23     = $oNovo->createElement("cDV",$this->getDVChaveAcesso());
		$oB01->appendChild($oB23);
		
        //-- Identificação do Ambiente
        //-- 1-Produção/ 2-Homologação
        $oB24   = $oNovo->createElement("tpAmb",$this->getIdentAmbiente());
		$oB01->appendChild($oB24);
		
        //-- Finalidade de emissão da NF-e
        //-- 1 - NF-e normal
        //-- 2 - NF-e complementar
        //-- 3 – NF-e de ajuste
        $oB25  = $oNovo->createElement("finNFe",$this->getFinalidadeNFe());
        $oB01->appendChild($oB25);

        $oB25a  = $oNovo->createElement("indFinal", $indFinal);
        $oB01->appendChild($oB25a);

        if($this->getModeloNF()=='55'){
            if($mT007['T007_Flag_Finalidade_NFe'] == '2'){
                $oB25b  = $oNovo->createElement("indPres", "0");
            } else {
                $oB25b  = $oNovo->createElement("indPres", "3");
            }
        }else{
            $oB25b  = $oNovo->createElement("indPres", "1");
        }
        $oB01->appendChild($oB25b);
		
        //-- Processo de emissão da NF-e
        //-- Identificador do processo de emissão da NF-e:
        //-- 0 - emissão de NF-e com aplicativo do contribuinte;
        //-- 1 - emissão de NF-e avulsa pelo Fisco;
        //-- 2 - emissão de NF-e avulsa, pelo contribuinte com seu certificado digital, através do site do Fisco;
        //-- 3- emissão NF-e pelo contribuinte com aplicativo fornecido pelo Fisco.
        $oB26 = $oNovo->createElement("procEmi",$this->getOrigemProcessoEmissao());
		$oB01->appendChild($oB26);
		
        //-- Versão do Processo de emissão da NF-e
        //-- Identificador da versão do processo de emissão (informar a versão do aplicativo emissor de NF-e).
        $oB27 = $oNovo->createElement("verProc",$this->getVersaoSistema());
		$oB01->appendChild($oB27);
		
		
		if ($this->getTipoDocAuxiliar()=='3' || $this->getTipoDocAuxiliar()=='6' || $this->getTipoDocAuxiliar()=='7' || $this->getTipoDocAuxiliar()=='9')
		{ 
	        //-- Informar a data e hora de entrada em contingência no formato AAAA-MMDDTHH: MM:SS (v.2.0).
	        //-- Data e Hora da entrada em contingência date("Y-m-d",time())."T".date("H:i:s",time())        
	        //$oB28 = $oNovo->createElement("dhCont","2011-05-26T17:00:00");
            $oB28 = $oNovo->createElement("dhCont",date("Y-m-d",time())."T".date("H:i:s",time()).$mT007['T007_Fuso_Horario_Emissao']);
			$oB01->appendChild($oB28);
	
	        //-- Informar a Justificativa da entrada em (v.2.0)
	        //-- Justificativa da entrada em contingência
	        $oB29 = $oNovo->createElement("xJust","SEFAZ FORA DO AR");
			$oB01->appendChild($oB29);
		}

        $cClausula  = " select ";
        $cClausula .= " T132_Tipo, ";
        $cClausula .= " T132_Chave_Acesso, ";
        $cClausula .= " T132_UF_IBGE, ";
        $cClausula .= " T132_Ano_Mes, ";
        $cClausula .= " T132_Pessoa_Fisica_Juridica, ";
        $cClausula .= " T132_Cnpj, ";
        $cClausula .= " T132_Cpf, ";
        $cClausula .= " T132_Inscricao_Estadual, ";
        $cClausula .= " T132_Modelo, ";
        $cClausula .= " T132_Serie, ";
        $cClausula .= " T132_Numero_Nota_Fiscal, ";
        $cClausula .= " T132_Cupom_Numero_EFC, ";
        $cClausula .= " T132_Cupom_Numero_COO, ";
        $cClausula .= " T132_Cupom_Modelo ";
        $cClausula .= " from T132 ";
        $cClausula .= " where T132_T007_Id='".$this->getT007Id()."' ";
        $cClausula .= " order by T132_Tipo ";
        $cSql_T132 = mysql_query($cClausula);
        echo mysql_error();

        unset($mDescricao_CFOP);
        if (mysql_num_rows($cSql_T132)>0)
        {

            while ($mT132 = mysql_fetch_array($cSql_T132))
            {
                //-- Grupo com as informações das NF/NF-e /NF de produtor/ Cupom Fiscal referenciadas.
                //-- Esta informação será utilizada nas hipóteses previstas na legislação.
                //-- (Ex.: Devolução de Mercadorias, Substituição de NF cancelada, Complementação de NF, etc.).
                $oB12a   = $oNovo->createElement("NFref");
                $oB01->appendChild($oB12a);
                
                if ($mT132[T132_Tipo]=='NFe')
                {
                    //-- Chave de acesso das NF-e referenciadas
                    //-- Utilizar esta TAG para referenciar uma Nota Fiscal Eletrônica emitida anteriormente, vinculada a NF-e atual.
                    $oB13   = $oNovo->createElement("refNFe",$mT132[T132_Chave_Acesso]);
                    $oB12a->appendChild($oB13);
                }
                
                if ($mT132[T132_Tipo]=='NF')
                {
                    //-- Grupo com as informações das NF referenciadas Idem a informação da TAG anterior,
                    //-- referenciando uma Nota Fiscal modelo 1/1A normal (a NF referenciada não é uma NF-e).
                    $oB14   = $oNovo->createElement("refNF");
                    $oB12a->appendChild($oB14);

                    //-- Código da UF do emitente do Documento Fiscal
                    //-- Utilizar a Tabela do IBGE (Anexo IV - Tabela de UF, Município e País)
                    $oB15   = $oNovo->createElement("cUF",$mT132[T132_UF_IBGE]);
                    $oB14->appendChild($oB15);

                    //-- Ano e Mês de emissão da NF-e
                    //-- AAMM da emissão da NF
                    $oB16   = $oNovo->createElement("AAMM",$mT132[T132_Ano_Mes]);
                    $oB14->appendChild($oB16);

                    //-- CNPJ do emitente
                    //-- Informar o CNPJ do emitente da NF
                    $oB17   = $oNovo->createElement("CNPJ",$mT132[T132_Cnpj]);
                    $oB14->appendChild($oB17);

                    //-- Modelo do Documento Fiscal
                    //-- Informar o código do modelo do Documento fiscal: 01 – modelo 01
                    $oB18   = $oNovo->createElement("mod",$mT132[T132_Modelo]);
                    $oB14->appendChild($oB18);

                    //-- Série do Documento Fiscal
                    //-- Informar a série do documento fiscal (informar zero se inexistente).
                    $oB19   = $oNovo->createElement("serie",$mT132[T132_Serie]);
                    $oB14->appendChild($oB19);

                    //-- Número do Documento Fiscal - Aceita de 1 à 999999999
                    $oB20   = $oNovo->createElement("nNF",$mT132[T132_Numero_Nota_Fiscal]);
                    $oB14->appendChild($oB20);
                }

                if ($mT132[T132_Tipo]=='NF_PRODUTOR')
                {
                    //-- Grupo de informações da NF de produtor rural referenciada
                    $oB20a   = $oNovo->createElement("refNFP");
                    $oB12a->appendChild($oB20a);

                    //-- Código da UF do emitente do Documento Fiscal
                    //-- Utilizar a Tabela do IBGE (Anexo IV - Tabela de UF, Município e País)
                    $oB20b   = $oNovo->createElement("cUF",$mT132[T132_UF_IBGE]);
                    $oB20a->appendChild($oB20b);

                    //-- Ano e Mês de emissão da NF-e
                    //-- AAMM da emissão da NF de produtor (v2.0)
                    $oB20c   = $oNovo->createElement("AAMM",$mT132[T132_Ano_Mes]);
                    $oB20a->appendChild($oB20c);

                    if ($mT132[T132_Pessoa_Fisica_Juridica]=='J')
                    {
                        //-- CNPJ do emitente
                        //-- Informar o CNPJ do emitente da NF de produtor (v2.0)
                        $oB20d   = $oNovo->createElement("CNPJ",$mT132[T132_Cnpj]);
                        $oB20a->appendChild($oB20d);
                    }
                    elseif ($mT132[T132_Pessoa_Fisica_Juridica]=='F')
                    {
                        //-- CPF do emitente
                        //-- Informar o CPF do emitente da NF de produtor (v2.0)
                        $oB20e   = $oNovo->createElement("CPF",$mT132[T132_Cpf]);
                        $oB20a->appendChild($oB20e);
                    }
                    
                    //-- IE do emitente
                    //-- Informar a IE do emitente da NF de Produtor (v2.0)
                    $oB20f   = $oNovo->createElement("IE",$mT132[T132_Inscricao_Estadual]);
                    $oB20a->appendChild($oB20f);

                    //-- Modelo do Documento Fiscal
                    //-- Informar o código 04 – NF de Produtor ou 01- para NF avulsa (v2.0)
                    $oB20f   = $oNovo->createElement("mod",$mT132[T132_Modelo]);
                    $oB20a->appendChild($oB20f);

                    //-- Série do Documento Fiscal
                    //-- Informar a série do documento fiscal (informar zero se inexistente) (v2.0).
                    $oB20g   = $oNovo->createElement("serie",$mT132[T132_Serie]);
                    $oB20a->appendChild($oB20g);

                    //-- Número do Documento Fiscal - Aceita de 1 à 999999999
                    $oB20h   = $oNovo->createElement("nNF",$mT132[T132_Numero_Nota_Fiscal]);
                    $oB20a->appendChild($oB20h);
                }
                
                if ($mT132[T132_Tipo]=='CTe')
                {
                    //-- Chave de acesso do CT-e referenciada
                    //-- Utilizar esta TAG para referenciar um CT-e emitido anteriormente, vinculada a NF-e atual - (v2.0).
                    $B20i   = $oNovo->createElement("refCTe",$mT132[T132_Chave_Acesso]);
                    $oB12a->appendChild($B20i);
                }

                if ($mT132[T132_Tipo]=='CUPOM')
                {

                    //-- Informações do Cupom Fiscal referenciado
                    //-- Grupo do Cupom Fiscal vinculado à NF-e (v2.0).
                    $B20j   = $oNovo->createElement("refECF");
                    $oB12a->appendChild($B20j);

                    //-- Preencher com "2B", quando se tratar de Cupom Fiscal emitido por máquina registradora (não ECF),
                    //-- com "2C", quando se tratar de Cupom Fiscal PDV,
                    //-- ou "2D", quando se tratar de Cupom Fiscal (emitido por ECF) (v2.0).
                    $oB20k   = $oNovo->createElement("mod",$mT132[T132_Cupom_Modelo]);
                    $B20j->appendChild($oB20k);

                    //-- Número de ordem seqüencial do ECF
                    //-- Informar o número de ordem seqüencial do ECF que emitiu o Cupom Fiscal vinculado à NF-e (v2.0).
                    $oB20l   = $oNovo->createElement("nECF",$mT132[T132_Cupom_Numero_EFC]);
                    $B20j->appendChild($oB20l);
                    
                    //-- Número do Contador de Ordem de Operação - COO
                    //-- Informar o Número do Contador de Ordem de Operação - COO vinculado à NF-e (v2.0).
                    $oB20m   = $oNovo->createElement("nCOO",$mT132[T132_Cupom_Numero_COO]);
                    $B20j->appendChild($oB20m);
                }

            }
        }   
        
        /////////////////////////////////////////////////////////////////////////////////////////
        //-- C - Identificação do Emitente da Nota Fiscal eletrônica
        /////////////////////////////////////////////////////////////////////////////////////////
        $oC01    = $oNovo->createElement("emit");
		$oA01->appendChild($oC01);

        //-- CNPJ do emitente
        //-- Informar o CNPJ do emitente. Em se tratando de emissão de NF-e avulsa pelo Fisco, as informações
        //-- do remente serão informadas neste grupo. O CNPJ ou CPF deverão ser informados com os zeros não significativos.
        $oC02 = $oNovo->createElement("CNPJ",$this->getCnpjEmitente());
		$oC01->appendChild($oC02);

        /*
        //-- CPF do remetente
        $oC02a = $oNovo->createElement("CPF","C02a");
		$oC01->appendChild($oC02a);
		*/
        //-- Razão Social ou Nome do emitente
        $oC03 = $oNovo->createElement("xNome",$this->getNomeRazaoEmitente());
		$oC01->appendChild($oC03);
		
        //-- Nome fantasia
        if (!empty($this->nome_fantasia_emitente))
        {
           $oC04 = $oNovo->createElement("xFant",$this->getNomeFantasiaEmitente());
		   $oC01->appendChild($oC04);
        }
        
        //-- TAG de grupo do Endereço do emitente
        $oC05 = $oNovo->createElement("enderEmit");
		$oC01->appendChild($oC05);
		
        //-- Logradouro
        $oC06 = $oNovo->createElement("xLgr",$this->getLogradouroEmitente());
		$oC05->appendChild($oC06);
		
        //-- Número
        $oC07 = $oNovo->createElement("nro",$this->getNumeroLogradouroEmitente());
 	    $oC05->appendChild($oC07);

        //-- Complemento
        if (!empty($this->complemento_logradouro_emitente))
        {
           $oC08 = $oNovo->createElement("xCpl",$this->getComplementoEmitente());
		   $oC05->appendChild($oC08);
        }
        
        //-- Bairro
        $oC09 = $oNovo->createElement("xBairro",$this->getBairroEmitente());
		$oC05->appendChild($oC09);

        //-- Código do município
        //-- Utilizar a Tabela do IBGE (Anexo IV  - Tabela de UF, Município e País). Informar ‘9999999 ‘para operações  com o exterior.
        $oC10 = $oNovo->createElement("cMun",$this->getCodigoMunicipioEmitente());
		$oC05->appendChild($oC10);

        //-- Nome do município
        //-- Informar ‘EXTERIOR‘ para operações com o exterior.
        $oC11 = $oNovo->createElement("xMun",$this->getMunicipioEmitente());
		$oC05->appendChild($oC11);

        //-- Sigla da UF
        //-- Informar ‘EX ‘para operações com o exterior.
        $oC12 = $oNovo->createElement("UF",$this->getUFEmitente());
		$oC05->appendChild($oC12);
		
		if (!empty($this->cep_emitente))
		{
            //-- Código do CEP
            //-- Informar os zeros não significativos.
            $oC13 = $oNovo->createElement("CEP",$this->getCEPEmitente());
    		$oC05->appendChild($oC13);
        }
        
        //-- Código do País
        //-- 1058 - Brasil
        $oC14 = $oNovo->createElement("cPais","1058");
		$oC05->appendChild($oC14);

        //-- Nome do País
        //-- Brasil ou BRASIL
        $oC15 = $oNovo->createElement("xPais","BRASIL");
		$oC05->appendChild($oC15);
		
        if (!empty($this->telefone_emitente))
        {
            //-- Telefone
            //-- Preencher com Código DDD + número do telefone.
            $oC16 = $oNovo->createElement("fone",$this->getTelefoneEmitente());
    		$oC05->appendChild($oC16);
        }
        
        //-- Inscricao Estadual
        //-- Campo de informação obrigatória nos casos de emissão própria (procEmi = 0, 2 ou 3).
        //-- A IE deve ser informada apenas com algarismos para destinatários contribuintes do ICMS,
        //-- sem caracteres de formatação (ponto,barra, hífen, etc.);
        //-- O literal “ISENTO” deve ser informado apenas para contribuintes do ICMS que são isentos de inscrição no cadastro de
        //-- contribuintes do ICMS e estejam emitindo NF-e avulsa;
        if ($this->origem_processo_emissao==0 || $this->origem_processo_emissao==2 || $this->origem_processo_emissao==3)
        {
            $oC17 = $oNovo->createElement("IE",$this->getInscricaoEstadualEmitente());
		    $oC01->appendChild($oC17);
        }
        
        //-- IE do Substituto Tributário
        //-- Informar a IE do ST da UF de destino da mercadoria, quando houver a retenção do ICMS ST para a UF de destino.
        if (!empty($mT007['D053_Inscricao_Estadual_ST']))
        {
            $oC18 = $oNovo->createElement("IEST",$this->getInscricaoEstadualSubTribEmitente());
	 	    $oC01->appendChild($oC18);
		}
		
        //-- Inscrição Municipal
        //-- Este campo deve ser informado, quando ocorrer a emissão de NF-e conjugada, com prestação de serviços
        //-- sujeitos ao ISSQN e fornecimento de peças sujeitos ao ICMS..
        if (!empty($this->inscricao_municipal_emitente))
        {
            $oC19 = $oNovo->createElement("IM",$this->getInscricaoMunicipalEmitente());
		    $oC01->appendChild($oC19);
        }
        
        //-- CNAE fiscal
        //-- Este campo deve ser informado quando o campo IM (C19) for informado
        if (!empty($this->cnae_emitente) and !empty($this->inscricao_municipal_emitente))
        {
            $oC20 = $oNovo->createElement("CNAE",$this->getCNAEEmitente());
    		$oC01->appendChild($oC20);
        }
		
		if ($this->versao_nfe!='1.10')
		{
    		//-- Código de Regime Tributário
    		//-- Este campo será obrigatoriamente preenchido com:
            //-- 1 – Simples Nacional;
            //-- 2 – Simples Nacional – excesso de sublimite de receita bruta;
            //-- 3 – Regime Normal. (v2.0).
            $oC21 = $oNovo->createElement("CRT",$mT007[T007_Empresa_Usuaria_CRT]);
      		$oC01->appendChild($oC21);
        }

        $CnpjCpfDestinatario = $this->getCnpjCpfDestinatario();
        if($this->getModeloNF()=='55' || ($this->getModeloNF()=='65' && !empty($CnpjCpfDestinatario))) {
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- E - Identificação do Destinatário da Nota Fiscal eletrônica
            /////////////////////////////////////////////////////////////////////////////////////////
            $oE01    = $oNovo->createElement("dest");
    		$oA01->appendChild($oE01);

            //-- Informar o CNPJ ou o CPF do destinatário, preenchendo os zeros não significativos.
            //-- Não informar o conteúdo da TAG se a operação for realizada com o exterior.
            
            if ($this->D024_Flag_Pessoa_Fisica_Juridica!='I')
            {
                if (strlen($this->getCnpjCpfDestinatario())==14)
                {
                   //-- CNPJ do destinatário
                   $oE02 = $oNovo->createElement("CNPJ",$this->getCnpjCpfDestinatario());
        		   $oE01->appendChild($oE02);
                }
                elseif(strlen($this->getCnpjCpfDestinatario())==11)
                {
                   //-- CPF do destinatário
                   $oE03 = $oNovo->createElement("CPF",$this->getCnpjCpfDestinatario());
        		   $oE01->appendChild($oE03);
                }
            }
            else
            {
                   //Em caso de operação com o exterior informar este campo (Aceita valor nulo)
                   $oE02 = $oNovo->createElement("idEstrangeiro","");
        		   $oE01->appendChild($oE02);
            }
            
            //-- Razão Social ou nome do destinatário
            $oE04 = $oNovo->createElement("xNome",$this->getNomeDestinatario());
    		$oE01->appendChild($oE04);

            //Caso o logradouro esteja preenchido, informamos os dados de enderDest
            $xLgr = $this->getLogradouroDestinatario();
            if(!empty($xLgr)){
                /////////////////////////////////////////////////////////////////////////////////////
                //-- TAG de grupo de endereço do Destinatário da NF-e
                $oE05 = $oNovo->createElement("enderDest");
        		$oE01->appendChild($oE05);

                //-- Logradouro
                $oE06 = $oNovo->createElement("xLgr",$this->getLogradouroDestinatario());
        		$oE05->appendChild($oE06);

                //-- Número
                $oE07 = $oNovo->createElement("nro",$this->getNumeroLogradouroDestinatario());
        		$oE05->appendChild($oE07);

                //-- Complemento
                if (strlen($this->complemento_logradouro_destinatario)>0)
                {
                   $oE08 = $oNovo->createElement("xCpl",$this->getComplementoDestinatario());
        		   $oE05->appendChild($oE08);
                }
                
                //-- Bairro
                $oE09 = $oNovo->createElement("xBairro",$this->getBairroDestinatario());
        		$oE05->appendChild($oE09);

                //-- Código do município
                //-- Utilizar a Tabela do IBGE (Anexo IV - Tabela de UF, Município e País).
                //-- Informar ‘9999999 ‘ para operações com o exterior.
                $oE10 = $oNovo->createElement("cMun",$this->getCodigoMunicipioDestinatario());
        		$oE05->appendChild($oE10);

                //-- Nome do município
                //-- Informar ‘EXTERIOR ‘para operações com o exterior.
                $oE11 = $oNovo->createElement("xMun",$this->getMunicipioDestinatario());
        		$oE05->appendChild($oE11);

                //-- Sigla da UF
                //-- Informar ‘EX’ para operações com o exterior.
                $oE12 = $oNovo->createElement("UF",$this->getUFDestinatario());
        		$oE05->appendChild($oE12);

                if (!empty($this->cep_destinatario))
                {
                    //-- Código do CEP
                    //-- Informar os zeros não significativos.
                    $oE13 = $oNovo->createElement("CEP",$this->getCEPDestinatario());
            		$oE05->appendChild($oE13);
                }
                
                //-- Código do País
                //-- Utilizar a Tabela do BACEN (Anexo IV - Tabela de UF, Município e País).
                $oE14 = $oNovo->createElement("cPais","$mT007[T007_Cliente_Codigo_Pais]");
        		$oE05->appendChild($oE14);

                //-- Nome do País
                $oE15 = $oNovo->createElement("xPais","$mT007[T007_Cliente_Pais]");
        		$oE05->appendChild($oE15);

                //-- Telefone
                //-- Preencher com Código DDD + número do telefone
                $TelefoneDestinatario=$this->getTelefoneDestinatario();
                if (!empty($TelefoneDestinatario))
                {
                   $oE16 = $oNovo->createElement("fone",$this->getTelefoneDestinatario());
        		   $oE05->appendChild($oE16);
                }
            }

            //-- IE
            //-- Informar a IE quando o destinatário for contribuinto do ICMS.
            //-- Informar ISENTO quando o destinatário for contribuinto do ICMS, mas não estiver obrigado à inscrição no cadastro de contribuintes do ICMS.
            //-- Não informar o conteúdo da TAG se o destinatário não for contribuinte do ICMS.
            //-- Esta tag aceita apenas:
            //-- . ausência de conteúdo (<IE></IE> ou <IE/>) para destinatários não contribuintes do ICMS;
            //-- . algarismos para destinatários contribuintes do ICMS, sem caracteres de formatação (ponto, barra, hífen, etc.);
            //-- . literal “ISENTO” para destinatários
            //-- contribuintes do ICMS que são isentos de inscrição no cadastro de contribuintes do ICMS
            $indIEDest = '';
            if (($this->D024_Flag_Pessoa_Fisica_Juridica == 'J' || $this->D024_Flag_Pessoa_Fisica_Juridica == 'F') && $mT007[T007_Flag_ACP]!=8) {
                if (($mT007['D024_Flag_Revenda_Consumidor'] == '2' && trim($this->getInscricaEstadualDestinatario()) == '' && $servidorId == 'NF') || ($mT007['D024_Flag_Contribuinte']=='0' && trim($this->getInscricaEstadualDestinatario()) == '' && $servidorId != 'NF')) {
                    // Caso seja NAO CONTRIBUINTE e a IE esteja em branco
                    $oE16a = $oNovo->createElement("indIEDest", "9");
                    $oE01->appendChild($oE16a);
                    $indIEDest = '9';
                } else if($this->getInscricaEstadualDestinatario() == 'ISENTO' || trim($this->getInscricaEstadualDestinatario()) == ''){
                    $oE16a = $oNovo->createElement("indIEDest", "2");
                    $oE01->appendChild($oE16a);
                    $indIEDest = '2';
                } else {
                    $oE16a = $oNovo->createElement("indIEDest", "1");
                    $oE01->appendChild($oE16a);
                    $indIEDest = '1';

                    $oE17 = $oNovo->createElement("IE", $this->getInscricaEstadualDestinatario());
                    $oE01->appendChild($oE17);
                }
            } else {
                $oE16a = $oNovo->createElement("indIEDest", "9");
                $oE01->appendChild($oE16a);
                $indIEDest = '9';
            }

            //-- Inscrição na SUFRAMA
            //-- Obrigatório, nas operações que se beneficiam de incentivos fiscais existentes nas áreas sob controle da SUFRAMA.
            //-- A omissão da Inscrição SUFRAMA impede o processamento da operação pelo Sistema de Mercadoria Nacional da SUFRAMA
            //-- e a liberação da Declaração de Ingresso, prejudicando a comprovação do ingresso/internamento da mercadoria nas
            //-- áreas sob controle da SUFRAMA.
            if (!empty($this->inscricao_suframa_destinatario))
            {
               $oE18 = $oNovo->createElement("ISUF",$this->getInscricaSuframaDestinatario());
    		   $oE01->appendChild($oE18);
    		}

            $mT007[D024_E_Mail] = $this->adicionarEspape($mT007[D024_E_Mail]);
            if (!empty($mT007[D024_E_Mail]) and $this->versao_nfe!='1.10')
            {
                //-- Informar o e-mail do destinatário. O campo pode ser utilizado para informar o e-mail de recepção da NF-e indicada pelo destinatário (v2.0)
                $mT007[D024_E_Mail] = substr($mT007[D024_E_Mail],0,60);
                $oE19 = $oNovo->createElement("email", "$mT007[D024_E_Mail]");
                $oE01->appendChild($oE19);
                
            }
            
        }


        /*
        /////////////////////////////////////////////////////////////////////////////////////////
        //-- F - Identificação do Local de Retirada
        /////////////////////////////////////////////////////////////////////////////////////////
        //-- Informar apenas quando for diferente do endereço do remetente.
        $oF01 = $oNovo->createElement("retirada");
		$oA01->appendChild($oF01);

        //-- CNPJ
        //-- Informar os zeros não significativos.
        $oF02 = $oNovo->createElement("CNPJ","F02");
		$oF01->appendChild($oF02);

        //-- Logradouro
        $oF03 = $oNovo->createElement("xLgr","F03");
		$oF01->appendChild($oF03);
		
        //-- Número
        $oF04 = $oNovo->createElement("nro","F04");
		$oF01->appendChild($oF04);

        //-- Complemento
        $oF05 = $oNovo->createElement("Complemento","F05");
		$oF01->appendChild($oF05);

        //-- Bairro
        $oF06 = $oNovo->createElement("xBairro","F06");
		$oF01->appendChild($oF06);

        //-- Código do município
        //-- Utilizar a Tabela do IBGE (Anexo IV - Tabela de UF, Município e País).
        //-- Informar ‘9999999 ‘para operações com o exterior.
        $oF07 = $oNovo->createElement("cMun","F07");
		$oF01->appendChild($oF07);
		
		//-- Nome do município
		//-- Informar ‘EXTERIOR ‘ para operações com o exterior.
        $oF08 = $oNovo->createElement("xMun","F08");
		$oF01->appendChild($oF08);
		
		//-- Sigla da UF
		//-- Informar ‘EX’ para operações com o exterior.
        $oF09 = $oNovo->createElement("UF","F09");
		$oF01->appendChild($oF09);



        /////////////////////////////////////////////////////////////////////////////////////////
        //-- G - Identificação do Local de Entrega
        /////////////////////////////////////////////////////////////////////////////////////////
        //-- Informar apenas quando for diferente do endereço do destinatário.
        $oG01 = $oNovo->createElement("entrega");
		$oA01->appendChild($oG01);

        //-- CNPJ
        //-- Informar os zeros não significativos.
        $oG02 = $oNovo->createElement("CNPJ","G02");
		$oG01->appendChild($oG02);

        //-- Logradouro
        $oG03 = $oNovo->createElement("xLgr","G03");
		$oG01->appendChild($oG03);

        //-- Número
        $oG04 = $oNovo->createElement("nro","G04");
		$oG01->appendChild($oG04);

        //-- Complemento
        $oG05 = $oNovo->createElement("Complemento","G05");
		$oG01->appendChild($oG05);

        //-- Bairro
        $oG06 = $oNovo->createElement("xBairro","G06");
		$oG01->appendChild($oG06);

        //-- Código do município
        //-- Utilizar a Tabela do IBGE (Anexo IV - Tabela de UF, Município e País).
        //-- Informar ‘9999999 ‘para operações com o exterior.
        $oG07 = $oNovo->createElement("cMun","G07");
		$oG01->appendChild($oG07);

		//-- Nome do município
		//-- Informar ‘EXTERIOR ‘ para operações com o exterior.
        $oG08 = $oNovo->createElement("xMun","G08");
		$oG01->appendChild($oG08);

		//-- Sigla da UF
		//-- Informar ‘EX’ para operações com o exterior.
        $oG09 = $oNovo->createElement("UF","G09");
		$oG01->appendChild($oG09);
        */
        // Verifica e corrige diferenca de arredondamento no valor do frete
        //Corrige arredondamento do frete e despesas acessórias (Ambos valores são reteados no T008)
        $T008 = mysql_query("select sum(round(T008_Valor_Total_Frete,2)) as valorTotalFrete, sum(round(T008_Valor_Total_Despesas_Acessorias,2)) as valorTotalOutro, T008_Id from T008 where T008_T007_Id='".$this->getT007Id()."'");
        $mT008 = mysql_fetch_array($T008);

        if (number_format($mT007['T007_Valor_Frete'],2)!=number_format($mT008['valorTotalFrete'],2)){
            $diferenca = $mT007['T007_Valor_Frete'] - $mT008['valorTotalFrete'];
            mysql_query("UPDATE T008 SET T008_Valor_Total_Frete=T008_Valor_Total_Frete+$diferenca WHERE T008_Id='{$mT008['T008_Id']}'");            
        }

        if (number_format($mT007['T007_Valor_Despesas_Acessorias'],2)!=number_format($mT008['valorTotalOutro'],2)){
            $diferencaOutro = $mT007['T007_Valor_Despesas_Acessorias'] - $mT008['valorTotalOutro'];
            mysql_query("UPDATE T008 SET T008_Valor_Total_Despesas_Acessorias=T008_Valor_Total_Despesas_Acessorias+$diferencaOutro WHERE T008_Id='{$mT008['T008_Id']}'");
        }

        $cClausula  = " select ";
        if($servidorId == 'NF'){ 
            $cClausula .= " if(replace(trim(T008_Codigo_Substituto),'*','')='',T008_Codigo_Produto,T008_Codigo_Substituto) T008_Codigo_Produto, ";
            $cClausula .= " T008_Codigo_Produto_Nota Codigo_ANP, ";
        } else {
           $cClausula  .= "T008_Codigo_Produto, ";
           $cClausula .= " '' Codigo_ANP, ";
	       $cClausula .= " T008_Aliquota_ICMS_Oculto, ";
        }
        $cClausula .= " T008_Codigo_Substituto, ";
        $cClausula .= " T008_Codigo_Produto_Pendencia CODIF, ";
        $cClausula .= " T008_Descricao_Produto, ";
        $cClausula .= " T008_Classificacao_Fiscal, ";
        $cClausula .= " D006_Codigo_CFOP T008_Codigo_CFOP, ";
        $cClausula .= " T008_Unidade, ";
        $cClausula .= " T008_Quantidade, ";
        $cClausula .= " T008_Valor_Total_Desconto, ";
        $cClausula .= " T008_Valor_Preco_Sem_Desconto_Unitario, ";
        $cClausula .= " T008_Valor_Total_Preco_Sem_Desconto, ";
        $cClausula .= " T008_Valor_Frete_Unitario, ";
        $cClausula .= " T008_Valor_Seguro_Unitario, ";
        $cClausula .= " T008_Valor_Total_Frete, ";
        $cClausula .= " T008_Valor_Total_Seguro, ";
        $cClausula .= " T008_Flag_Situacao_Tributaria, ";
        $cClausula .= " T008_Percentual_Reducao_ICMS, ";
        $cClausula .= " T008_Valor_Base_ICMS_Impresso, ";
        $cClausula .= " T008_Aliquota_ICMS, ";
        $cClausula .= " T008_Valor_ICMS, ";
        $cClausula .= " T008_Codigo_Barras, ";
        $cClausula .= " T008_ST_VA, ";
        $cClausula .= " T008_Percentual_Reducao_ICMS_ST, ";
        $cClausula .= " T008_ICMS_Inter_Estadual, ";
        $cClausula .= " T008_ST_ICMS_Interno, ";
        $cClausula .= " T008_Valor_Base_ICMS_Substituicao, ";
        $cClausula .= " T008_Valor_ICMS_Substituicao_Retencao, ";
        $cClausula .= " T008_Valor_Base_IPI, ";
        $cClausula .= " T008_Aliquota_IPI, ";
        $cClausula .= " T008_Valor_IPI, ";
        $cClausula .= " T008_Situacao_Tributaria_PIS, ";
        $cClausula .= " T008_Valor_Base_PIS, ";
        $cClausula .= " T008_Aliquota_PIS, ";
        $cClausula .= " T008_Valor_PIS, ";
        $cClausula .= " T008_Situacao_Tributaria_COFINS, ";
        $cClausula .= " T008_Valor_Base_COFINS, ";
        $cClausula .= " T008_Aliquota_COFINS, ";
        $cClausula .= " T008_Valor_COFINS, ";
        $cClausula .= " T008_Valor_Total_Base_Imposto_Importacao, ";
        $cClausula .= " T008_Valor_Total_Imposto_Importacao, ";
        $cClausula .= " T008_Valor_Total_Despesas_Aduaneira, ";
        $cClausula .= " T008_Valor_Total_Despesas_Acessorias, ";
        $cClausula .= " T008_Valor_Total_IOF, ";
        $cClausula .= " T008_Numero_Documento_Importacao, ";
        $cClausula .= " T008_Data_Registro_Documento_Importacao, ";
        $cClausula .= " T008_Local_Desembaraco, ";
        $cClausula .= " T008_UF_Desembaraco, ";
        $cClausula .= " T008_Data_Desembaraco, ";
        $cClausula .= " T008_Codigo_Exportador, ";
        $cClausula .= " T008_Numero_Adicao, ";
        $cClausula .= " T008_Numero_Sequencia_Adicao, ";
        $cClausula .= " T008_Codigo_Fabricante, ";
        $cClausula .= " T008_Valor_Desconto_Item_Adicao, ";
        $cClausula .= " T008_Numero_Pedido_Compra, ";
        $cClausula .= " T008_Item_Pedido_Compra, ";
        $cClausula .= " T008_Situacao_Tributaria_IPI, ";
        $cClausula .= " T008_Percentual_Base_Operacao_Propria, ";
        $cClausula .= " T008_Aliquota_ICMS_Credito, ";
        $cClausula .= " T008_Valor_ICMS_Credito, ";
        $cClausula .= " T008_Valor_ICMS_Substituicao_Destino, ";
        $cClausula .= " T008_Valor_Base_ICMS_Substituicao_Destino, ";
        $cClausula .= " T008_Valor_ICMS_Substituicao_Retido, ";
        $cClausula .= " T008_Valor_Base_ICMS_Substituicao_Retido, ";
        $cClausula .= " T008_Percentual_Reducao_ICMS_ST, ";
        $cClausula .= " T008_Modalidade_ICMS, ";
        $cClausula .= " T008_Modalidade_ICMS_Substituicao, ";
        $cClausula .= " T008_UF_ICMS_Substituicao_Devido, ";
        $cClausula .= " T008_D059_Id, ";
        $cClausula .= " D059_Codigo_ST, ";
        $cClausula .= " D082_Marca, ";
        $cClausula .= " T008_Id, ";
        $cClausula .= " T008_Valor_Trib_Transparencia, ";
        $cClausula .= " T008_Informacoes_Adicionais, ";
        $cClausula .= " T008_Flag_Transporte_DI, ";
        $cClausula .= " T008_Valor_Total_AFRMM, ";
        $cClausula .= " T008_Flag_Tipo_Intermedio_DI, ";
        $cClausula .= " T008_CNPJ_Importador, ";
        $cClausula .= " T008_UF_Importador, ";
        $cClausula .= " T008_Numero_Drawback_Importacao, ";
        $cClausula .= " T008_Numero_Drawback_Exportacao, ";
        $cClausula .= " T008_Numero_Registro_Exportacao, ";
        $cClausula .= " T008_Chave_Acesso_Exportacao, ";
        $cClausula .= " T008_Quantidade_Exportada, ";
        $cClausula .= " T008_Percentual_GN_GLP, ";
        $cClausula .= " T008_Numero_Recopi, ";
        $cClausula .= " T008_Valor_ICMS_Desonerado, ";
        $cClausula .= " T008_Motivo_Desoneracao_ICMS, ";
        $cClausula .= " T008_Valor_ICMS_Oculto, ";
        $cClausula .= " T008_D009_Id, ";
        $cClausula .= " T008_Codigo_CEST, ";
        $cClausula .= " T008_Valor_Base_Partilha_ICMS, ";
        $cClausula .= " T008_Percentual_FCP, ";
        $cClausula .= " T008_Percentual_Partilha_ICMS_Destino, ";
        $cClausula .= " T008_Valor_FCP, ";
        $cClausula .= " T008_Valor_Base_FCP, ";
        $cClausula .= " T008_Valor_Partilha_ICMS_Destino, ";
        $cClausula .= " T008_Valor_Partilha_ICMS_Origem, ";
        $cClausula .= " T008_Codigo_Enq_Legal_IPI, ";
        $cClausula .= " T008_Valor_FCP_Partilha,  ";
        $cClausula .= " T008_Valor_Base_FCP_Ret,  ";
        $cClausula .= " T008_Valor_FCP_Retido,  ";
        $cClausula .= " T008_Valor_Base_FCP_ST,  ";
        $cClausula .= " T008_Valor_FCP_ST,  ";
        $cClausula .= " T008_Valor_Base_FCP_Dest,  ";
        $cClausula .= " T008_Aliquota_ST_FCP, ";
        $cClausula .= " T008_Indicador_Escala, ";
        $cClausula .= " T008_Codigo_Beneficio_Fiscal, ";
        $cClausula .= " D001_CNPJ_Fabricante, ";
        $cClausula .= " T008_D037_Id_Tributavel,";
        $cClausula .= " T008_Quantidade_Tributavel,";
        $cClausula .= " T008_Valor_Preco_Sem_Desconto_Unitario_Tributavel,";
        $cClausula .= " T008_Codigo_Barras_Tributavel, ";
        $cClausula .= " T008_Valor_IPI_Devolucao, ";
        $cClausula .= " T008_Valor_ICMS_Substituicao_Retido ";
        $cClausula .= " from T008 ";
        $cClausula .= " left join D059 on D059_Id=T008_D059_Id ";
        $cClausula .= " left join D009 on D009_Id=T008_D009_Id ";
        $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D082 on D082_Id=D049_D082_Id ";
        $cClausula .= " left join D006 on D006_Id=T008_D006_Id ";
        $cClausula .= " where T008_T007_Id='".$this->getT007Id()."' ";
        $cSql_T008 = mysql_query($cClausula);
        echo mysql_error();
        
        $nSequencial=0;

        while ($mT008 = mysql_fetch_array($cSql_T008))
        {
            $cOrigem_Mercadoria=substr($mT008[T008_Flag_Situacao_Tributaria],0,1);

            // $sqlT290 = mysql_query("SELECT SUM(T290_Valor_Base_ICMS_Substituicao) AS T007_BC_ICMS_ST_Compra, SUM(T290_Valor_ICMS_Substituicao) AS T007_Valor_ICMS_ST_Compra, T290_T008_Id FROM T290 LEFT JOIN T008 ON T008_Id=T290_T008_Id WHERE T290_T008_Id='{$mT008['T008_Id']}'");
            // $mT290   = mysql_fetch_array($sqlT290);

            //Ajuste de aliquota ICMS Inter Estadual para partilha de ICMS
            if($servidorId == 'NF'){
                //- 7% para os Estados de origem do Sul e Sudeste (exceto ES), destinado para os Estados do Norte, Nordeste, Centro Oeste e Espírito Santo;
                //- 12% para os demais casos.
                $listaUFRegraICMSInter = array('PR', 'SC', 'RS', 'SP', 'RJ', 'MG');
                if(in_array($this->getUFEmitente(), $listaUFRegraICMSInter) && !in_array($this->getUFDestinatario(), $listaUFRegraICMSInter)){
                    $mT008['T008_Aliquota_ICMS_Oculto'] = 7;
                } else {
                    $mT008['T008_Aliquota_ICMS_Oculto'] = 12;
                }
                //Se origem mercadoria importado - aliquota de 4%
                if($cOrigem_Mercadoria == '1' || $cOrigem_Mercadoria == '2' || $cOrigem_Mercadoria == '6' || $cOrigem_Mercadoria == '7' || $cOrigem_Mercadoria == '3'){
                    $mT008['T008_Aliquota_ICMS_Oculto'] = 4;
                }
            } else {
                
                if(substr($mT008['T008_Flag_Situacao_Tributaria'],0,1)>0 and substr($mT008['T008_Flag_Situacao_Tributaria'],0,1)!=5) {
                        $mT008['T008_Aliquota_ICMS_Oculto'] = 4;
                } else {
                    //- 7% para os Estados de origem do Sul e Sudeste (exceto ES), destinado para os Estados do Norte, Nordeste, Centro Oeste e Espírito Santo;^M
                    //- 12% para os demais casos.^M
                    $listaUFRegraICMSInter = array('PR', 'SC', 'RS', 'SP', 'RJ', 'MG');
                    if(in_array($this->getUFEmitente(), $listaUFRegraICMSInter) && !in_array($this->getUFDestinatario(), $listaUFRegraICMSInter)){
                        $mT008['T008_Aliquota_ICMS_Oculto'] = 7;
                    } else {
                        $mT008['T008_Aliquota_ICMS_Oculto'] = 12;
                    }
                }
            }



            if ($this->versao_nfe!='1.10' and empty($mT008[T008_Situacao_Tributaria_IPI]))
            {
               $this->mErrorMsg[] = "Codigo da situação tributária nao foi preenchido";
               $this->errorStatus = true;
            }

            $nSequencial++;

            $mT008[T008_Classificacao_Fiscal]=str_replace(".","",$mT008[T008_Classificacao_Fiscal]);
            $mT008[T008_Codigo_CFOP]=str_replace(".","",$mT008[T008_Codigo_CFOP]);
            $mT008[T008_Descricao_Produto]=$this->adicionarEspape($mT008[T008_Descricao_Produto]);
            $mT008['T008_CNPJ_Importador'] = trim($mT008['T008_CNPJ_Importador']);
            $mT008['T008_CNPJ_Importador']=str_replace(array('.','/','-'), '', $mT008['T008_CNPJ_Importador']);

            /////////////////////////////////////////////////////////////////////////////////////////
            //-- H - Detalhamento de Produtos e Serviços da NF-e
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- TAG de grupo do detalhamento de Produtos e Serviços da NF-e
            //-- Múltiplas ocorrências (máximo = 990)
            $oH01 = $oNovo->createElement("det");
    		$oH01->setAttribute("nItem","$nSequencial"); //-- Número do item (1-990)
    		$oA01->appendChild($oH01);


            /////////////////////////////////////////////////////////////////////////////////////////
            //-- I - Produtos e Serviços da NF-e
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- TAG de grupo do detalhamento de Produtos e Serviços da NF-e
            $oI01 = $oNovo->createElement("prod");
    		$oH01->appendChild($oI01);


            if($servidorId == 'ERP'){                
                if(isset($mT008['Codigo_ANP']) && !empty($mT008['Codigo_ANP'])){
                    //O T008_Codigo_Produto_Nota permanece com o Alias, se ele estiver setado, ele é atribuido para T008_Codigo_Produto e depois limpado
                   // $mT008['T008_Codigo_Produto'] = $mT008['Codigo_ANP'];
                    $mT008['Codigo_ANP'] = '';
                }
            }
            //-- Código do produto ou serviço
            //-- Preencher com CFOP, caso se trate de itens não relacionados com mercadorias/produto e que o contribuinte não possua codificação
            //-- própria. Formato ”CFOP9999”
            $mT008['T008_Codigo_Produto']=$this->adicionarEspape($mT008['T008_Codigo_Produto']);
            //$mT008['T008_Codigo_Produto']=$mT008['T008_Codigo_Produto'];
            $oI02 = $oNovo->createElement("cProd",$mT008[T008_Codigo_Produto]);
    		$oI01->appendChild($oI02);

            //-- GTIN (Global Trade Item Number) do produto, antigo código EAN ou código de barras
            //-- Preencher com o código GTIN-8, GTIN-12, GTIN-13 ou GTIN-14 (antigos códigos EAN, UPC e DUN-14), não informar o conteúdo da
            //-- TAG em caso de o produto não possuir este código.
            
            $cEAN = $mT008[T008_Codigo_Barras];
            $len = strlen($mT008[T008_Codigo_Barras]);
            if ($len != 8 && $len != 12 && $len != 13 && $len != 14) {
                $cEAN = "SEM GTIN";
            }
      	    $oI03 = $oNovo->createElement("cEAN",$cEAN);
   	     	$oI01->appendChild($oI03);

            //Caso a variavel da configuração global esteja preenchida, a descrição será populada com os campos especificados            
            $mC031['camposDescricaoProdutoNF'] = trim($mC031['camposDescricaoProdutoNF']);            
            if(!empty($mC031['camposDescricaoProdutoNF'])){
                $camposDescricao = explode(",", trim($mC031['camposDescricaoProdutoNF']));
                $tabelas = array('D001','D049','D009','C008','D060','D003','D002','D082');
                
                $select = array();
                foreach ($camposDescricao as $campo) {
                    if(in_array(substr($campo, 0, 4), $tabelas)){
                        $select[] = $campo;
                    }
                }
                $select = implode(", ", $select);
                $select = (empty($select)) ? '*' : $select;

                //Select nos campos relacionados ao cadastro do produto
                $sqlD001 = mysql_query("SELECT {$select} FROM D001
                            LEFT JOIN D049 ON D049_D001_Id=D001_Id
                            LEFT JOIN D009 ON D009_D049_Id=D049_Id
                            LEFT JOIN C008 ON D001_C008_Id=C008_Id
                            LEFT JOIN D060 ON D001_D060_Id=D060_Id
                            LEFT JOIN D003 ON D001_D003_Id=D003_Id
                            LEFT JOIN D002 ON D001_D002_Id=D002_Id
                            LEFT JOIN D082 ON D049_D082_Id=D082_Id
                            WHERE D009_Id = '{$mT008['T008_D009_Id']}'");
                $mD001 = mysql_fetch_array($sqlD001);

                $descricaoProduto = '';
                foreach ($camposDescricao as $campo) {
                    $campo = trim($campo);
                    if(($campo[0]=='\'' && $campo[strlen($campo)-1]=='\'') || ($campo[0]=='"' && $campo[strlen($campo)-1]=='"')){
                        $campo = trim($campo, "'\"");
                        $descricaoProduto .= $campo;
                    } else {
                        if(array_key_exists($campo, $mT008) && !empty($mT008[$campo])) {
                            $descricaoProduto .= $mT008[$campo]." ";
                            continue;
                        }
                        if(array_key_exists($campo, $mD001) && !empty($mD001[$campo])) {
                            $descricaoProduto .= $mD001[$campo]." ";
                        }
                    }
                }
                $mT008[T008_Descricao_Produto] = $descricaoProduto;
            } else {
                //Código mantido por compatibilidade das versões mais antigas do sistema
                
                //-- Incluindo código substituto junto a descrição
                if (!empty($mT008[T008_Codigo_Substituto]))
                {
                    $mT008[T008_Descricao_Produto].=' '.$mT008[T008_Codigo_Substituto];
                }

                //-- Descrição do produto ou serviço
                if (!empty($mT008[D082_Marca]) and $mT007[C020_Imprimir_Marca_NF]=='S')
                {
// ------------------DESCOMENTAR ESSA LINHA - ELA FOI COMENTADA PARA REALIZAÇÃO DOS TESTES DA NFC-E -----------------------------------------------------------//
                    $mT008[T008_Descricao_Produto].=' '.$mT008[D082_Marca];
                }
                
                //-- Incluindo código substituto junto a descrição
                if (!empty($mT008[T008_Codigo_Barras]))
                {
                    $mT008[T008_Descricao_Produto].=' '.$mT008[T008_Codigo_Produto];
                }
            }

            $mT008[T008_Descricao_Produto]=$this->adicionarEspape($mT008[T008_Descricao_Produto]);
            //$mT008[T008_Descricao_Produto]=$mT008[T008_Descricao_Produto];
            $oI04 = $oNovo->createElement("xProd",$mT008[T008_Descricao_Produto]);
    		    $oI01->appendChild($oI04);

            //-- Código NCM (8 posições), informar o gênero (posição do capítulo do NCM) quando a operação não for de comércio exterior (importação/ exportação)
            //-- ou o produto não seja tributado pelo IPI.
            //-- Em caso de serviço informar o código 99 (v2.0)
            if (strlen(trim($mT008[T008_Classificacao_Fiscal]))>0)
            {
                $oI05 = $oNovo->createElement("NCM",$mT008[T008_Classificacao_Fiscal]);
                $oI01->appendChild($oI05);
            }

            //-- Código CEST
            //-- Código Especificador da Substituição Tributária – CEST, que estabelece a
            //-- sistemática de uniformização e identificação das mercadorias e bens passíveis de
            //-- sujeição aos regimes de substituição tributária e de antecipação de recolhimento do ICMS
            $mT008[T008_Codigo_CEST] = str_replace(array('.', '-', ' '), '', $mT008[T008_Codigo_CEST]);
            if (strlen(trim($mT008[T008_Codigo_CEST]))>0)
            {
                $oI05c = $oNovo->createElement("CEST",$mT008[T008_Codigo_CEST]);
                $oI01->appendChild($oI05c);
            }
            if(!empty($mT008[T008_Indicador_Escala])){
                $oI05d = $oNovo->createElement("indEscala",$mT008[T008_Indicador_Escala]);
                $oI01->appendChild($oI05d);
            }

            if($mT008[T008_Indicador_Escala] == 'N'){
                $mT008[D001_CNPJ_Fabricante] = str_replace("/", "", str_replace(".", "", str_replace("-", "", $mT008[D001_CNPJ_Fabricante])));
                $I05e = $oNovo->createElement("CNPJFab",$mT008[D001_CNPJ_Fabricante]);
                $oI01->appendChild($I05e);   
            }

            if(!empty($mT008[T008_Codigo_Beneficio_Fiscal])){
                $oI05d = $oNovo->createElement("cBenef",$mT008[T008_Codigo_Beneficio_Fiscal]);
                $oI01->appendChild($oI05d);
            }

            //Calculo da diferença de aliquotas
            $aliquotaICMSST = $mT008[T008_ST_ICMS_Interno] - $mT008[T008_ICMS_Inter_Estadual];
            $aliquotaICMSST = ($aliquotaICMSST > 0) ? $aliquotaICMSST : 0;

    		 /*
            //-- EX_TIPI
            //-- Preencher de acordo com o código EX da TIPI. Em caso de serviço, não incluir a TAG.
            $oI06 = $oNovo->createElement("EXTIPI","I06");
    		$oI01->appendChild($oI06);

            //-- Gênero do Produto ou Serviço
            //-- Preencher de acordo com a Tabela de Capítulos da NCM. Em caso de serviço, não incluir a TAG
            $oI07 = $oNovo->createElement("genero","I07");
    		$oI01->appendChild($oI07);
    		*/

            //-- Código Fiscal de Operações e Prestações
            //-- Utilizar Tabela de CFOP
            $oI08 = $oNovo->createElement("CFOP",$mT008[T008_Codigo_CFOP]);
    		$oI01->appendChild($oI08);

            //-- Unidade Comercial
            //-- Informar a unidade de comercialização do produto.
            $mT008[T008_Unidade]=$this->adicionarEspape($mT008[T008_Unidade]);
            if (!empty($mT008[T008_Unidade]))
            {
               //$mT008[T008_Unidade]=$mT008[T008_Unidade];
               $oI09 = $oNovo->createElement("uCom",substr($mT008[T008_Unidade],0,3));
               $oI01->appendChild($oI09);
            }

            //-- Quantidade Comercial
            //-- Informar a quantidade de comercialização do produto.
            $oI10 = $oNovo->createElement("qCom",number_format($mT008[T008_Quantidade],4,'.',''));
    		$oI01->appendChild($oI10);

            //-- Valor Unitário de comercialização
            //-- Informar o valor unitário de comercialização do produto, campo meramente informativo, o contribuinte pode utilizar a
            //-- precisão desejada (0-10 decimais). Para efeitos de cálculo, o valor unitário será obtido pela divisão do valor do
            //-- produto pela quantidade comercial. (v2.0)
            $oI10a = $oNovo->createElement("vUnCom",number_format($mT008[T008_Valor_Preco_Sem_Desconto_Unitario],5,'.',''));
    		$oI01->appendChild($oI10a);

            //-- Valor Total Bruto dos Produtos ou Serviços
            $oI11 = $oNovo->createElement("vProd",number_format($mT008[T008_Valor_Total_Preco_Sem_Desconto],2,'.',''));
    		$oI01->appendChild($oI11);

            // ----------------- VOLTAR AQUI --------------------------//
            //-- GTIN (Global Trade Item Number) da unidade tributável, antigo código EAN ou código de barras
            //-- Preencher com o código GTIN-8, GTIN-12, GTIN-13 ou GTIN-14 (antigos códigos EAN, UPC e DUN-14) da unidade tributável do
            //-- produto, não informar o conteúdo da TAG em caso de o produto não possuir este código.
            $cEANTrib = $mT008[T008_Codigo_Barras_Tributavel];
            $lenTrib = strlen($mT008[T008_Codigo_Barras_Tributavel]);
            if ($lenTrib != 8 && $lenTrib != 12 && $lenTrib != 13 && $lenTrib != 14) {
                $cEANTrib = "SEM GTIN";
            }

            $oI12 = $oNovo->createElement("cEANTrib", $cEANTrib);
    		$oI01->appendChild($oI12);

            //-- Unidade Tributável
            if(!empty($mT008['T008_D037_Id_Tributavel'])){

                $D037 = mysql_query("SELECT D037_Unidade 
                                   FROM D037 
                                  WHERE D037_Id='{$mT008['T008_D037_Id_Tributavel']}'");
                $mD037 = mysql_fetch_array($D037);    
                $mD037[D037_Unidade]=$this->adicionarEspape($mD037[D037_Unidade]);
                $oI13 = $oNovo->createElement("uTrib",$mD037[D037_Unidade]);
                $oI01->appendChild($oI13);

            } else {
                
                $oI13 = $oNovo->createElement("uTrib",$mT008[T008_Unidade]);
                $oI01->appendChild($oI13);
            }

            //-- Quantidade Tributável
            //-- Informar a quantidade de tributação do produto (v2.0).
            if(empty($mT008[T008_Quantidade_Tributavel])){
                $mT008[T008_Quantidade_Tributavel] = $mT008[T008_Quantidade];
            }
            $oI14 = $oNovo->createElement("qTrib",number_format($mT008[T008_Quantidade_Tributavel],4,'.',''));
    		$oI01->appendChild($oI14);

            //-- Valor Unitário de tributação
            //-- Informar o valor unitário de tributação do produto, campo meramente informativo, o contribuinte pode utilizar a
            //-- precisão desejada (0-10 decimais). Para efeitos de cálculo, o valor unitário será obtido pela divisão do valor do
            //-- produto pela quantidade tributável.
            if($mT008[T008_Valor_Preco_Sem_Desconto_Unitario_Tributavel] <= 0){
                $mT008[T008_Valor_Preco_Sem_Desconto_Unitario_Tributavel] = $mT008[T008_Valor_Preco_Sem_Desconto_Unitario];
            }
            $oI14a = $oNovo->createElement("vUnTrib",number_format($mT008[T008_Valor_Preco_Sem_Desconto_Unitario_Tributavel],5,'.',''));
    		$oI01->appendChild($oI14a);


            if ($mT008[T008_Valor_Total_Frete]>0)
            {
                //-- Valor Total do Frete
                $oI15 = $oNovo->createElement("vFrete",number_format($mT008[T008_Valor_Total_Frete],2,'.',''));
        		$oI01->appendChild($oI15);
            }

            if ($mT008[T008_Valor_Total_Seguro]>0)
            {
                //-- Valor Total do Frete
                $oI16 = $oNovo->createElement("vSeg",number_format($mT008[T008_Valor_Total_Seguro],2,'.',''));
        		$oI01->appendChild($oI16);
            }

            /*
            //-- Valor Total do Seguro
            $oI16 = $oNovo->createElement("vSeg","I16");
    		$oI01->appendChild($oI16);
            */

            if ($mT008[T008_Valor_Total_Desconto]>0)
            {
                //-- Valor do Desconto
                $oI17 = $oNovo->createElement("vDesc",number_format($mT008[T008_Valor_Total_Desconto],2,'.',''));
    		    $oI01->appendChild($oI17);
            }

            if (number_format($mT008[T008_Valor_Total_Despesas_Acessorias],2,'.','')>0 and $this->versao_nfe!='1.10')
            {
                //-- Outras despesas acessórias
                $oI17a = $oNovo->createElement("vOutro",number_format($mT008[T008_Valor_Total_Despesas_Acessorias],2,'.',''));
    		    $oI01->appendChild($oI17a);
            }
            
            if ($this->versao_nfe!='1.10')
            {
                //-- Indica se valor do Item (vProd) entra no valor total da NF-e (vProd)
                //-- Este campo deverá ser preenchido com:
                //-- 0 – o valor do item (vProd) não compõe o valor total da NF-e (vProd)
                //-- 1 – o valor do item (vProd) compõe o valor total da NF-e (vProd) (v2.0)
                $oI17b = $oNovo->createElement("indTot",'1');
      		    $oI01->appendChild($oI17b);
            }
            
            if (!empty($mT008['Codigo_ANP']))
            {
                /////////////////////////////////////////////////////////////////////////////////////////
                //-- I18 Tag da Declaração de Importação
                //-- Linha LA
                /////////////////////////////////////////////////////////////////////////////////////////

                //-- Tag de combustíveis
                //-- Informar apenas para operações com combustíveis líquidos e lubrificantes.
                $oL101 = $oNovo->createElement("comb");
                $oI01->appendChild($oL101);

                //-- Utilizar a codificação de produtos do Sistema de Informações de Movimentação de produtos - SIMP (http://www.anp.gov.br/simp/index.htm). 
                $oL102 = $oNovo->createElement("cProdANP","{$mT008['Codigo_ANP']}");
                $oL101->appendChild($oL102);

                //-- Utilizar a descrição de produtos do Sistema de Informações de Movimentação de Produtos - SIMP
                $oL103 = $oNovo->createElement("descANP","{$mT008['T008_Descricao_Produto']}");
                $oL101->appendChild($oL103);

                //-- Informar apenas quando a UF utilizar o CODIF (Sistema de Controle do Diferimento do Imposto nas Operações com AEAC - Álcool Etílico Anidro Combustível).
                if(!empty($mT008['CODIF'])){
                    $oL104 = $oNovo->createElement("CODIF","{$mT008['CODIF']}");
                    $oL101->appendChild($oL104);
                }

                //-- Informar quando a quantidade faturada informada no campo qCom (I10) tiver sido ajustada para uma temperatura diferente da ambiente.
                $qTemp=number_format($mT008['T008_Quantidade'],4);
                $oL105 = $oNovo->createElement("qTemp","$qTemp");
                $oL101->appendChild($oL105);

                //-- Informar a UF de consumo
                $oL106 = $oNovo->createElement("UFCons",$this->getUFDestinatario());
                $oL101->appendChild($oL106);

            }

            if (!empty($mT008['T008_Numero_Documento_Importacao']))
            {
                /////////////////////////////////////////////////////////////////////////////////////////
                //-- I18 Tag da Declaração de Importação
                /////////////////////////////////////////////////////////////////////////////////////////

                //-- Tag da Declaração de Importação
                //-- Informar dados da importação 0-N
                $oI18 = $oNovo->createElement("DI");
        		$oI01->appendChild($oI18);

                //-- Número do Documento de Importação (DI/DSI/DA)
                $oI19 = $oNovo->createElement("nDI","{$mT008['T008_Numero_Documento_Importacao']}");
        		$oI18->appendChild($oI19);

                //-- Data de Registro da DI/DSI/DA    Formato “AAAA-MM-DD”
                $oI20 = $oNovo->createElement("dDI","{$mT008['T008_Data_Registro_Documento_Importacao']}");
        		$oI18->appendChild($oI20);

                //-- Local de desembaraço
                $oI21 = $oNovo->createElement("xLocDesemb","{$mT008['T008_Local_Desembaraco']}");
        		$oI18->appendChild($oI21);

                //-- Sigla da UF onde ocorreu o Desembaraço Aduaneiro
                $oI22 = $oNovo->createElement("UFDesemb","{$mT008['T008_UF_Desembaraco']}");
        		$oI18->appendChild($oI22);

                //-- Data do Desembaraço Aduaneiro Formato “AAAA-MM-DD”
                $oI23 = $oNovo->createElement("dDesemb","{$mT008['T008_Data_Desembaraco']}");
        		$oI18->appendChild($oI23);

                /*-- Via de transporte internacional informada na Declaração de Importação (DI)
                1=Marítima;
                2=Fluvial;
                3=Lacustre;
                4=Aérea;
                5=Postal
                6=Ferroviária;
                7=Rodoviária;
                8=Conduto / Rede Transmissão;
                9=Meios Próprios;
                10=Entrada / Saída ficta; 11=Courier; 12=Handcarry. (NT 2013/005 v 1.10) */
                $oI23a = $oNovo->createElement("tpViaTransp","{$mT008['T008_Flag_Transporte_DI']}");
                $oI18->appendChild($oI23a);

                //-- Valor da AFRMM - Adicional ao Frete para Renovação da Marinha Mercante
                $oI23b = $oNovo->createElement("vAFRMM","{$mT008['T008_Valor_Total_AFRMM']}");
                $oI18->appendChild($oI23b);

                //-- Forma de importação quanto a intermediação
                $oI23c = $oNovo->createElement("tpIntermedio","{$mT008['T008_Flag_Tipo_Intermedio_DI']}");
                $oI18->appendChild($oI23c);

                if($mT008['T008_Flag_Tipo_Intermedio_DI'] != '1'){
                    //-- CNPJ do adquirente ou do encomendante
                    $oI23d = $oNovo->createElement("CNPJ","{$mT008['T008_CNPJ_Importador']}");
                    $oI18->appendChild($oI23d);

                    //-- Sigla da UF do adquirente ou do encomendante
                    $oI23e = $oNovo->createElement("UFTerceiro","{$mT008['T008_UF_Importador']}");
                    $oI18->appendChild($oI23e);
                }

                //-- Código do exportador, usado nos sistemas internos de informação do emitente da NF-e
                $oI24 = $oNovo->createElement("cExportador","{$mT008['T008_Codigo_Exportador']}");
        		$oI18->appendChild($oI24);

                //-- Adições
                $oI25 = $oNovo->createElement("adi");
        		$oI18->appendChild($oI25);

                //-- Numero da adição 1-3
                $oI26 = $oNovo->createElement("nAdicao","{$mT008['T008_Numero_Adicao']}");
        		$oI25->appendChild($oI26);

                //-- Numero seqüencial do item dentro da adição 1-3
                $oI27 = $oNovo->createElement("nSeqAdic","{$mT008['T008_Numero_Sequencia_Adicao']}");
        		$oI25->appendChild($oI27);

                //-- Código do fabricante estrangeiro, usado nos sistemas internos de informação do emitente da NF-e
                $oI28 = $oNovo->createElement("cFabricante","{$mT008['T008_Codigo_Fabricante']}");
        		$oI25->appendChild($oI28);

                if ($mT008['T008_Valor_Desconto_Item_Adicao']>0)
                {
                    //-- Valor do desconto do item da DI – Adição
                    $oI29 = $oNovo->createElement("vDescDI","{$mT008['T008_Valor_Desconto_Item_Adicao']}");
            		$oI25->appendChild($oI29);
                }

                $oI29a = $oNovo->createElement("nDraw","{$mT008['T008_Numero_Drawback_Importacao']}");
                $oI25->appendChild($oI29a);                
                
    		}


            if (!empty($mT008['T008_Numero_Drawback_Exportacao']))
            {
                /////////////////////////////////////////////////////////////////////////////////////////
                //-- I50 Tag da Informações de Exportação
                /////////////////////////////////////////////////////////////////////////////////////////

                //-- Tag do detalhamento da exportação
                //-- Informar dados da exportação 0-N
                $oI50 = $oNovo->createElement("detExport");
                $oI01->appendChild($oI50);

                //-- Número do ato concessório de Drawback
                $oI51 = $oNovo->createElement("nDraw","{$mT008['T008_Numero_Drawback_Exportacao']}");
                $oI50->appendChild($oI51);
                
                //-- Grupo sobre exportação indireta
                $oI52 = $oNovo->createElement("exportInd");
                $oI50->appendChild($oI52);

                //-- Número do Registro de Exportação
                $oI53 = $oNovo->createElement("nRE","{$mT008['T008_Numero_Registro_Exportacao']}");
                $oI52->appendChild($oI53);

                //-- Chave de Acesso da NF-e recebida para exportação
                $oI54 = $oNovo->createElement("chNFe","{$mT008['T008_Chave_Acesso_Exportacao']}");
                $oI52->appendChild($oI54);

                //-- Quantidade do item realmente exportado
                $oI55 = $oNovo->createElement("qExport","{$mT008['T008_Quantidade_Exportada']}");
                $oI52->appendChild($oI55);
            }

            $mT008['T008_Numero_Pedido_Compra']=$this->adicionarEspape($mT008['T008_Numero_Pedido_Compra']);
            if ($this->versao_nfe!='1.10')
            {
                if (!empty($mT008['T008_Numero_Pedido_Compra']))
                {
                    //-- Número do Pedido de Compra
            		//-- Informação de interesse do emissor para controle do B2B. (v2.0)
                    //$mT008['T008_Numero_Pedido_Compra']=$mT008['T008_Numero_Pedido_Compra'];
                    $oI60 = $oNovo->createElement("xPed","{$mT008['T008_Numero_Pedido_Compra']}");
            		$oI01->appendChild($oI60);
                }

                if (!empty($mT008['T008_Item_Pedido_Compra']))
                {
                    //-- Item do Pedido de Compra
            		//-- Informação de interesse do emissor para controle do B2B. (v2.0)
                    $oI61 = $oNovo->createElement("nItemPed","{$mT008['T008_Item_Pedido_Compra']}");
            		$oI01->appendChild($oI61);
                }
            }
            
            //-- Número do RECOPI
            if(!empty($mT008['T008_Numero_Recopi'])){
                $oLB01 = $oNovo->createElement("nRECOPI", "{$mT008['T008_Numero_Recopi']}");
                $oI01->appendChild($oLB01);
            }

            /////////////////////////////////////////////////////////////////////////////////////////
            //-- M - Tributos incidentes no Produto ou Serviço
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- TAG de grupo de Tributos incidentes no Produto ou Serviço
            $oM01 = $oNovo->createElement("imposto");
    		$oH01->appendChild($oM01);

            //-- TAG Lei da Transparencia (opcional)
            // Criação de campo opcional para que o contribuinte possa informar o valor aproximado correspondente a totalidade dos tributos federais, estaduais e municipais, cuja incidência influa na formação do respectivo preço de venda, atendendo o disposto na Lei citada; (Lei nº 12.741/12)
            if(isset($mT007['T007_Valor_Total_Trib_Transparencia']) && $mT007['T007_Valor_Total_Trib_Transparencia'] > 0 && $mT007['C020_Atualizacao_Lei_Transparencia']=='N') {
                $this->vTotTrib += number_format($mT008[T008_Valor_Trib_Transparencia],2,'.','');
                $oM02 = $oNovo->createElement("vTotTrib", number_format($mT008[T008_Valor_Trib_Transparencia],2,'.',''));
                $oM01->appendChild($oM02);
            }

    		//-- TAG de grupo do ICMS daOperação própria e ST
    		//-- Informar apenas um dos grupos N02, N03, N04, N05, N06, N07, N08, N09 ou N10, com base no conteúdo informado na TAG CST
            $oN01 = $oNovo->createElement("ICMS");
    		$oM01->appendChild($oN01);

            if ($mT008[T008_D059_Id]==1)
            {
                //-- TAG de grupo do CST = 00
        		//-- CST – 00 – Tributada integralmente
                $oN02 = $oNovo->createElement("ICMS00");
        		$oN01->appendChild($oN02);

        		//-- Origem da mercadoria
        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno.
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN02->appendChild($oN11);

        		//-- Tributação do ICMS:
        		//-- 00 – Tributada integralmente
                $oN12 = $oNovo->createElement("CST","00");
        		$oN02->appendChild($oN12);

        		//-- Modalidade de determinação da BC do ICMS
        		//-- 0 - Margem Valor Agregado (%);
                //-- 1 - Pauta (Valor);
                //-- 2 - Preço Tabelado Máx. (valor);
                //-- 3 - valor da operação
                $oN13 = $oNovo->createElement("modBC","{$mT008['T008_Modalidade_ICMS']}");
        		$oN02->appendChild($oN13);

        		//-- Valor da BC do ICMS
                $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
        		$oN02->appendChild($oN15);

        		//-- Alíquota do imposto
                $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
        		$oN02->appendChild($oN16);

        		//-- Valor do ICMS
                $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
        		$oN02->appendChild($oN17);

                if($mT008[T008_Percentual_FCP] > 0 && $indIEDest != '9' && $indFinal != '1'){

                    //-- aliquota FCP 
                    $oN17b = $oNovo->createElement("pFCP",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN02->appendChild($oN17b);

                    //-- aliquota FCP 
                    //$oN17c = $oNovo->createElement("vFCP",number_format($mT008[T008_Valor_FCP],2,'.',''));
                    $oN17c = $oNovo->createElement("vFCP",number_format($mT008[T008_Valor_FCP],2,'.',''));
                    $oN02->appendChild($oN17c);
                }

            }
            elseif($mT008[T008_D059_Id]==2)
            {
        		//-- TAG de grupo do CST = 10
        		//-- CST - 10 - Tributada e com cobrança do ICMS por substituição tributária
                $oN03 = $oNovo->createElement("ICMS10");
        		$oN01->appendChild($oN03);

        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno.
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN03->appendChild($oN11);

                //-- Tributação do ICMS
                //-- Tributação pelo ICMS 10 - Tributada e com cobrança do ICMS por substituição tributária
                $oN12 = $oNovo->createElement("CST","10");
        		$oN03->appendChild($oN12);

                //-- Modalidade de determinação da BC do ICMS
                //-- 0 - Margem Valor Agregado (%);
                //-- 1 - Pauta (Valor);
                //-- 2 - Preço Tabelado Máx. (valor);
                //-- 3 - valor da operação.
                $oN13 = $oNovo->createElement("modBC","{$mT008['T008_Modalidade_ICMS']}");
        		$oN03->appendChild($oN13);

                //-- Percentual da Redução de BC
                if ($mT008[T008_Percentual_Reducao_ICMS]>0){
                    //$oN14 = $oNovo->createElement("pRedBC",number_format($mT008[T008_Percentual_Reducao_ICMS],2,'.',''));
                    //$oN03->appendChild($oN14);
                }

        		//-- Valor da BC do ICMS
                $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
        		$oN03->appendChild($oN15);

        		//-- Alíquota do imposto
                $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
        		$oN03->appendChild($oN16);

        		//-- Valor do ICMS
                $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
        		$oN03->appendChild($oN17);

                if($mT008[T008_Percentual_FCP] > 0 && $indIEDest != '9' && $indFinal != '1'){
                    //-- Valor da Base de Cálculo do FCP
                    $oN17a = $oNovo->createElement("vBCFCP",number_format($mT008[T008_Valor_Base_FCP],2,'.',''));
                    $oN03->appendChild($oN17a);

                    //-- Percentual do Fundo de Combate à Pobreza (FCP)
                    $oN17b = $oNovo->createElement("pFCP",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN03->appendChild($oN17b);

                    //-- Valor do Fundo de Combate à Pobreza (FCP)
                    $oN17c = $oNovo->createElement("vFCP",number_format($mT008[T008_Valor_FCP],2,'.',''));
                    $oN03->appendChild($oN17c);
                }

        		//-- Modalidade de determinação da BC do ICMS ST
        		//-- 0 – Preço tabelado ou máximo sugerido;
                //-- 1 - Lista Negativa (valor);
                //-- 2 - Lista Positiva (valor);
                //-- 3 - Lista Neutra (valor);
                //-- 4 - Margem Valor Agregado (%);
                //-- 5 - Pauta (valor);
                $oN18 = $oNovo->createElement("modBCST","{$mT008['T008_Modalidade_ICMS_Substituicao']}");
        		$oN03->appendChild($oN18);

                //-- Percentual da margem de valor Adicionado do ICMS ST
                if ($mT008[T008_ST_VA]>0)
                {
                    $oN19 = $oNovo->createElement("pMVAST",number_format($mT008[T008_ST_VA],2,'.',''));
            		$oN03->appendChild($oN19);
                }
                
                //-- Percentual da Redução de BC do ICMS ST
                if ($mT008[T008_Percentual_Reducao_ICMS_ST]>0)
                {
                   $oN20 = $oNovo->createElement("pRedBCST",number_format($mT008[T008_Percentual_Reducao_ICMS_ST],2,'.',''));
        		   $oN03->appendChild($oN20);
        		}

                //-- Valor da BC do ICMS ST
                $oN21 = $oNovo->createElement("vBCST",number_format($mT008[T008_Valor_Base_ICMS_Substituicao],2,'.',''));
        		$oN03->appendChild($oN21);

                //-- Alíquota do imposto do ICMS ST
                //$oN22 = $oNovo->createElement("pICMSST",number_format($mT008[T008_ICMS_Inter_Estadual],2,'.',''));
                $oN22 = $oNovo->createElement("pICMSST",number_format($aliquotaICMSST,2,'.',''));
        		$oN03->appendChild($oN22);

                //-- Valor do ICMS ST retido
                $oN23 = $oNovo->createElement("vICMSST",number_format($mT008[T008_Valor_ICMS_Substituicao_Retencao],2,'.',''));
        		$oN03->appendChild($oN23);
            
                if($mT008[T008_Percentual_FCP] > 0 && $indIEDest != '9' && $indFinal != '1'){
                    //-- Valor da Base de Cálculo do FCP retido por Substituição Tributária
                    $oN23a = $oNovo->createElement("vBCFCPST",number_format($mT008[T008_Valor_Base_FCP_ST],2,'.',''));
                    $oN03->appendChild($oN23a);
                    
                    //-- Percentual do FCP retido por Substituição Tributária
                    $oN23b = $oNovo->createElement("pFCPST",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN03->appendChild($oN23b);

                    //-- Valor do FCP retido por Substituição Tributária
                    $oN23d = $oNovo->createElement("vFCPST",number_format($mT008[T008_Valor_FCP_ST],2,'.',''));
                    $oN03->appendChild($oN23d);
                }
            }
            elseif($mT008[T008_D059_Id]==3)
            {
                //-- TAG de grupo do CST = 20
            	//-- CST – 20 - Com redução de base de cálculo
                $oN04 = $oNovo->createElement("ICMS20");
        		$oN01->appendChild($oN04);

        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno.
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN04->appendChild($oN11);

                //-- Tributação do ICMS
                //-- Tributação pelo ICMS 20 - Com redução de base de cálculo
                $oN12 = $oNovo->createElement("CST","20");
        		$oN04->appendChild($oN12);

                //-- Modalidade de determinação da BC do ICMS
                //-- 0 - Margem Valor Agregado (%);
                //-- 1 - Pauta (Valor);
                //-- 2 - Preço Tabelado Máx. (valor);
                //-- 3 - valor da operação.
                $oN13 = $oNovo->createElement("modBC","{$mT008['T008_Modalidade_ICMS']}");
        		$oN04->appendChild($oN13);

         		//-- Percentual da Redução de BC
                $oN14 = $oNovo->createElement("pRedBC",number_format($mT008[T008_Percentual_Reducao_ICMS],2,'.',''));
        		$oN04->appendChild($oN14);

         		//-- Valor da BC do ICMS
                $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
        		$oN04->appendChild($oN15);

        		//-- Alíquota do imposto
                $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
        		$oN04->appendChild($oN16);

        		//-- Valor do ICMS
                $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
        		$oN04->appendChild($oN17);

                if($mT008[T008_Percentual_FCP] > 0 && $indIEDest != '9' && $indFinal != '1'){
                    //-- Valor da Base de Cálculo do FCP retido por Substituição Tributária
                    $oN17a = $oNovo->createElement("vBCFCP",number_format($mT008[T008_Valor_Base_FCP],2,'.',''));
                    $oN04->appendChild($oN17a);

                    //-- Percentual do FCP retido por Substituição Tributária
                    $oN17b = $oNovo->createElement("pFCP",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN04->appendChild($oN17b);

                    //-- Valor do FCP retido por Substituição Tributária
                    $oN17c = $oNovo->createElement("vFCP",number_format($mT008[T008_Valor_FCP],2,'.',''));
                    $oN04->appendChild($oN17c);
                }

                if(!empty($mT008[T008_Motivo_Desoneracao_ICMS])){
                    //-- Valor desonerado
                    $oN28a = $oNovo->createElement("vICMSDeson",number_format($mT008[T008_Valor_ICMS_Desonerado],2,'.',''));
                    $oN04->appendChild($oN28a);                

                    //-- Motivo da desoneração
                    $oN28 = $oNovo->createElement("motDesICMS","{$mT008['T008_Motivo_Desoneracao_ICMS']}");
                    $oN04->appendChild($oN28);   
                }
        		
            }
            elseif($mT008[T008_D059_Id]==4)
            {
                //-- TAG de grupo do CST = 30
        		//-- CST – 30 - Isenta ou não tributada e com cobrança do ICMS por substituição tributária
                $oN05 = $oNovo->createElement("ICMS30");
        		$oN01->appendChild($oN05);

        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno.
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN05->appendChild($oN11);

                //-- Tributação do ICMS
                //-- CST – 30 - Isenta ou não tributada e com cobrança do ICMS por substituição tributária
                $oN12 = $oNovo->createElement("CST","30");
        		$oN05->appendChild($oN12);

                //-- Modalidade de determinação da BC do ICMS ST
                //-- 0 – Preço tabelado ou máximo sugerido;
                //-- 1 - Lista Negativa (valor);
                //-- 2 - Lista Positiva (valor);
                //-- 3 - Lista Neutra (valor);
                //-- 4 - Margem Valor Agregado (%);
                //-- 5 - Pauta (valor);
                $oN18 = $oNovo->createElement("modBCST","{$mT008['T008_Modalidade_ICMS_Substituicao']}");
        		$oN05->appendChild($oN18);

                //-- Percentual da margem de valor Adicionado do ICMS ST
                if ($mT008[T008_ST_VA]>0)
                {
                   $oN19 = $oNovo->createElement("pMVAST",number_format($mT008[T008_ST_VA],2,'.',''));
        		   $oN05->appendChild($oN19);
        		}

                //-- Percentual da Redução de BC do ICMS ST
                if ($mT008[T008_Percentual_Reducao_ICMS_ST]>0)
                {
                   $oN20 = $oNovo->createElement("pRedBCST",number_format($mT008[T008_Percentual_Reducao_ICMS_ST],2,'.',''));
        		   $oN05->appendChild($oN20);
        		}

                //-- Valor da BC do ICMS ST
                $oN21 = $oNovo->createElement("vBCST",number_format($mT008[T008_Valor_Base_ICMS_Substituicao],2,'.',''));
        		$oN05->appendChild($oN21);

                //-- Alíquota do imposto do ICMS ST
                //$oN22 = $oNovo->createElement("pICMSST",number_format($mT008[T008_ICMS_Inter_Estadual],2,'.',''));
                $oN22 = $oNovo->createElement("pICMSST",number_format($aliquotaICMSST,2,'.',''));
        		$oN05->appendChild($oN22);

                //-- Valor do ICMS ST retido
                $oN23 = $oNovo->createElement("vICMSST",number_format($mT008[T008_Valor_ICMS_Substituicao_Retencao],2,'.',''));
        		$oN05->appendChild($oN23);

                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP retido por Substituição Tributária
                    $oN23a = $oNovo->createElement("vBCFCPST",number_format($mT008[T008_Valor_Base_FCP_ST],2,'.',''));
                    $oN05->appendChild($oN23a);

                    //-- Percentual do FCP retido por Substituição Tributária
                    $oN23b = $oNovo->createElement("pFCPST",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN05->appendChild($oN23b);

                    //-- Valor do FCP retido por Substituição Tributária
                    $oN23d = $oNovo->createElement("vFCPST",number_format($mT008[T008_Valor_FCP_ST],2,'.',''));
                    $oN05->appendChild($oN23d);
                }

                if(!empty($mT008[T008_Motivo_Desoneracao_ICMS])){
                    //-- Valor desonerado
                    $oN27 = $oNovo->createElement("vICMSDeson",$mT008[T008_Valor_ICMS_Desonerado]);
                    $oN05->appendChild($oN27);                

                    //-- Motivo da desoneração
                    $oN28 = $oNovo->createElement("motDesICMS","{$mT008['T008_Motivo_Desoneracao_ICMS']}");
                    $oN05->appendChild($oN28);
                }
            }
            elseif($mT008[T008_D059_Id]==5 or $mT008[T008_D059_Id]==6 or $mT008[T008_D059_Id]==7)
            {
        		//-- TAG de grupo do CST = 40, 41 ou 50
        		//-- 4 = CST – 40 - Isenta
                //-- 5 = CST – 41 - Não tributada
                //-- 6 = CST – 50 - Suspensão
                $oN06 = $oNovo->createElement("ICMS40");
        		$oN01->appendChild($oN06);

        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno.
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN06->appendChild($oN11);

        		//-- Tributação pelo ICMS
                //-- 40 - Isenta
                //-- 41 - Não tributada
                //-- 50 - Suspensão
                $oN12 = $oNovo->createElement("CST",$mT008[D059_Codigo_ST]);
        		$oN06->appendChild($oN12);

                if(!empty($mT008[T008_Motivo_Desoneracao_ICMS])){
                    //-- Valor desonerado
                    $oN27 = $oNovo->createElement("vICMSDeson",$mT008[T008_Valor_ICMS_Desonerado]);
                    $oN06->appendChild($oN27);                

                    //-- Motivo da desoneração
                    $oN28 = $oNovo->createElement("motDesICMS","{$mT008['T008_Motivo_Desoneracao_ICMS']}");
                    $oN06->appendChild($oN28);                
                }
            }
            elseif($mT008[T008_D059_Id]=='8')
            {
        		//-- TAG de grupo do CST – 51 - Diferimento
                //-- A exigência do preenchimento das informações do ICMS diferido fica à critério de cada UF.
                $oN07 = $oNovo->createElement("ICMS51");
        		$oN01->appendChild($oN07);

        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno.
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN07->appendChild($oN11);

        		//-- Tributação pelo ICMS
                //--  51 - Diferimento
                $oN12 = $oNovo->createElement("CST","51");
        		$oN07->appendChild($oN12);

        		//-- Modalidade de determinação da BC do ICMS
                //-- 0 - Margem Valor Agregado (%);
                //-- 1 - Pauta (Valor);
                //-- 2 - Preço Tabelado Máx. (valor);
                //-- 3 - valor da operação.
                $oN13 = $oNovo->createElement("modBC","{$mT008['T008_Modalidade_ICMS']}");
        		$oN07->appendChild($oN13);

        		//-- Percentual da Redução de BC
                $oN14 = $oNovo->createElement("pRedBC",number_format($mT008[T008_Percentual_Reducao_ICMS],2,'.',''));
        		$oN07->appendChild($oN14);

        		//-- Valor da BC do ICMS
                $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
        		$oN07->appendChild($oN15);

        		//-- Alíquota do imposto
                $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
        		$oN07->appendChild($oN16);


                if($mT008[T008_Valor_ICMS]>0){
                    //-- Valor como se não tivesse o diferimento
                    $N16a = $oNovo->createElement("vICMSOp",number_format($mT008[T008_Valor_ICMS_Oculto],2,'.',''));
                    $oN07->appendChild($N16a);
                    
                    //-- No caso de diferimento total, informar o percentual de diferimento "100".
                    $percentual_Diferimento = ($mT008[T008_Valor_ICMS] * 100) / $mT008[T008_Valor_ICMS_Oculto];
                    $N16b = $oNovo->createElement("pDif",number_format($mT008[T008_Percentual_Reducao_ICMS],2,'.',''));
                    $oN07->appendChild($N16b);

                    //-- Valor do diferimento
                    $valor_Diferido = $mT008[T008_Valor_ICMS_Oculto] - $mT008[T008_Valor_ICMS];
                    $N16c = $oNovo->createElement("vICMSDif",number_format($valor_Diferido,2,'.',''));
                    $oN07->appendChild($N16c);
                }
        		
                //-- Valor do ICMS
                $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
        		$oN07->appendChild($oN17);

                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP
                    $oN17 = $oNovo->createElement("vBCFCP",number_format($mT008[T008_Valor_Base_FCP],2,'.',''));
                    $oN07->appendChild($oN17);

                    //-- Percentual do Fundo de Combate à Pobreza (FCP)
                    $oN17 = $oNovo->createElement("pFCP",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN07->appendChild($oN17);

                    //-- Valor do Fundo de Combate à Pobreza (FCP)
                    $oN17 = $oNovo->createElement("vFCP",number_format($mT008[T008_Valor_Total_FCP],2,'.',''));
                    $oN07->appendChild($oN17);
                }
            
            }
            elseif($mT008[T008_D059_Id]==9)
            {
                //-- CST – 60 - ICMS cobrado anteriormente por substituição
                $oN08 = $oNovo->createElement("ICMS60");
        		$oN01->appendChild($oN08);

        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno.
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN08->appendChild($oN11);

                //-- Tributação pelo ICMS 60 - ICMS cobrado anteriormente por substituição tributária
                $oN12 = $oNovo->createElement("CST","60");
        		$oN08->appendChild($oN12);

                //-- Valor da BC do ICMS ST retido
                //-- Valor da BC do ICMS ST cobrado anteriormente por ST
                $oN21 = $oNovo->createElement("vBCSTRet",number_format($mT008[T008_Valor_Base_ICMS_Substituicao_Retido],2,'.',''));
        		$oN08->appendChild($oN21);

                //-- Alíquota suportada pelo Consumidor Final
                $oN21a = $oNovo->createElement("pST",number_format($mT008[T008_Aliquota_ST_FCP],2,'.',''));
                $oN08->appendChild($oN21a);

                $oN21b = $oNovo->createElement("vICMSSubstituto",number_format($mT008[T008_Valor_ICMS_Substituicao_Retido],2,'.',''));
                $oN08->appendChild($oN21b);

                //-- Valor do ICMS ST retido
                //-- Valor do ICMS ST cobrado anteriormente por ST
                $oN23 = $oNovo->createElement("vICMSSTRet",number_format($mT008[T008_Valor_ICMS_Substituicao_Retido],2,'.',''));
        		$oN08->appendChild($oN23);

                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP retido anteriormente por ST
                    $oN23a = $oNovo->createElement("vBCFCPSTRet",number_format($mT008[T008_Valor_Base_FCP_Ret],2,'.',''));
                    $oN08->appendChild($oN23a);

                    //-- Percentual do FCP retido anteriormente por Substituição Tributária
                    $oN23b = $oNovo->createElement("pFCPSTRet",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN08->appendChild($oN23b);

                    //-- Valor do FCP retido por Substituição Tributária
                    $oN23d = $oNovo->createElement("vFCPSTRet",number_format($mT008[T008_Valor_FCP_Retido],2,'.',''));
                    $oN08->appendChild($oN23d);
                }

                

                // if($mT008[T008_Valor_Base_ICMS_Impresso] > 0){
                //     //-- Valor da BC do ICMS                
                //     $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
                //     $oN08->appendChild($oN15);
                // }

                // if($mT008[T008_Aliquota_ICMS] > 0){
                //     //-- Alíquota do imposto
                //     $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
                //     $oN08->appendChild($oN16);
                // }
                // if($mT008[T008_Valor_ICMS] > 0){
                //     //-- Valor do ICMS
                //     $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
                //     $oN08->appendChild($oN17);
                // }
            }
            elseif($mT008[T008_D059_Id]==10)
            {
                //-- TAG de grupo do CST = 70
        		//-- CST - 70 - Com redução de base de cálculo e cobrança do ICMS por substituição tributária
                $oN09 = $oNovo->createElement("ICMS70");
        		$oN01->appendChild($oN09);

        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN09->appendChild($oN11);

        		//-- Tributação pelo ICMS
                //-- 70 - Com redução de base de cálculo e cobrança do ICMS por substituição tributária ICMS por substituição tributária
                $oN12 = $oNovo->createElement("CST","70");
        		$oN09->appendChild($oN12);

        		//-- Modalidade de determinação da BC do ICMS
                //-- 0 - Margem Valor Agregado (%);
                //-- 1 - Pauta (Valor);
                //-- 2 - Preço Tabelado Máx. (valor);
                //-- 3 - valor da operação.
                $oN13 = $oNovo->createElement("modBC","{$mT008['T008_Modalidade_ICMS']}");
        		$oN09->appendChild($oN13);

        		//-- Percentual da Redução de BC
                $oN14 = $oNovo->createElement("pRedBC",number_format($mT008[T008_Percentual_Reducao_ICMS],2,'.',''));
        		$oN09->appendChild($oN14);

                // -- Valores não conhecidos, portanto comentados
                // $T008_Valor_Base_ICMS_Impresso = explode(".", $mT008[T008_Valor_Base_ICMS_Impresso]);
                // $decimaisBaseICMS = substr($T008_Valor_Base_ICMS_Impresso[1], 0, 2);
                // $mT008[T008_Valor_Base_ICMS_Impresso] = $T008_Valor_Base_ICMS_Impresso[0] . $decimaisBaseICMS;
        		
                //-- Valor da BC do ICMS
                $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
        		$oN09->appendChild($oN15);

        		//-- Alíquota do imposto
                $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
        		$oN09->appendChild($oN16);

        		//-- Valor do ICMS
                $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
        		$oN09->appendChild($oN17);

                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP
                    $oN17a = $oNovo->createElement("vBCFCP",number_format($mT008[T008_Valor_Base_FCP],2,'.',''));
                    $oN09->appendChild($oN17a);

                    //-- Percentual do Fundo de Combate à Pobreza (FCP)
                    $oN17b = $oNovo->createElement("pFCP",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN09->appendChild($oN17b);

                    //-- Valor do Fundo de Combate à Pobreza (FCP)
                    $oN17c = $oNovo->createElement("vFCP",number_format($mT008[T008_Valor_FCP],2,'.',''));
                    $oN09->appendChild($oN17c);           
                }
        		//-- Modalidade de determinação da BC do ICMS ST
        		//-- 0 – Preço tabelado ou máximo sugerido
        		//-- 1 - Lista Negativa (valor);
                //-- 2 - Lista Positiva (valor);
                //-- 3 - Lista Neutra (valor);
                //-- 4 - Margem Valor Agregado (%);
                //-- 5 - Pauta (valor)
                $oN18 = $oNovo->createElement("modBCST","{$mT008['T008_Modalidade_ICMS_Substituicao']}");
        		$oN09->appendChild($oN18);

        		//-- Percentual da margem de valor Adicionado do ICMS ST
                if ($mT008[T008_ST_VA]>0)
                {
                   $oN19 = $oNovo->createElement("pMVAST",number_format($mT008[T008_ST_VA],2,'.',''));
        		   $oN09->appendChild($oN19);
        		}

        		//-- Percentual da Redução de BC do ICMS ST
                if ($mT008[T008_Percentual_Reducao_ICMS_ST]>0)
                {
                   $oN20 = $oNovo->createElement("pRedBCST",number_format($mT008[T008_Percentual_Reducao_ICMS_ST],2,'.',''));
        		   $oN09->appendChild($oN20);
        		}

        		//-- Valor da BC do ICMS ST
                $oN21 = $oNovo->createElement("vBCST",number_format($mT008[T008_Valor_Base_ICMS_Substituicao],2,'.',''));
        		$oN09->appendChild($oN21);

        		//-- Alíquota do imposto do ICMS ST
                //$oN22 = $oNovo->createElement("pICMSST",number_format($mT008[T008_ICMS_Inter_Estadual],2,'.',''));
                $oN22 = $oNovo->createElement("pICMSST",number_format($aliquotaICMSST,2,'.',''));
        		$oN09->appendChild($oN22);

        		//-- Valor do ICMS ST
                $oN23 = $oNovo->createElement("vICMSST",number_format($mT008[T008_Valor_ICMS_Substituicao_Retencao],2,'.',''));
        		$oN09->appendChild($oN23);

                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP retido por Substituição Tributária
                    $oN23a = $oNovo->createElement("vBCFCPST",number_format($mT008[T008_Valor_Base_FCP_ST],2,'.',''));
                    $oN09->appendChild($oN23a);

                    //-- Percentual do FCP retido por Substituição Tributária
                    $oN23b = $oNovo->createElement("pFCPST",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN09->appendChild($oN23b);

                    //-- Valor do FCP retido por Substituição Tributária
                    $oN23d = $oNovo->createElement("vFCPST",number_format($mT008[T008_Valor_FCP_ST],2,'.',''));
                    $oN09->appendChild($oN23d);
                }



                if(!empty($mT008[T008_Motivo_Desoneracao_ICMS])){
                    //-- Valor desonerado
                    $oN27 = $oNovo->createElement("vICMSDeson",$mT008[T008_Valor_ICMS_Desonerado]);
                    $oN09->appendChild($oN27);                

                    //-- Motivo da desoneração
                    $oN28 = $oNovo->createElement("motDesICMS","{$mT008['T008_Motivo_Desoneracao_ICMS']}");
                    $oN09->appendChild($oN28);
                }
            }
            elseif($mT008[T008_D059_Id]==11)
            {
                //-- TAG de grupo do CST = 90
            	//-- CST - 90 – Outros
                $oN10 = $oNovo->createElement("ICMS90");
        		$oN01->appendChild($oN10);

        		//-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
        		$oN10->appendChild($oN11);

        		//-- Tributação pelo ICMS 90 - Outros
                $oN12 = $oNovo->createElement("CST","90");
        		$oN10->appendChild($oN12);

                //-- Modalidade de determinação da BC do ICMS
        		//-- 0 - Margem Valor Agregado (%);
                //-- 1 - Pauta (Valor);
                //-- 2 - Preço Tabelado Máx. (valor);
                //-- 3 - valor da operação.
                $oN13 = $oNovo->createElement("modBC","{$mT008['T008_Modalidade_ICMS']}");
        		$oN10->appendChild($oN13);

                //-- Valor da BC do ICMS
                $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
        		$oN10->appendChild($oN15);

                if ($mT008[T008_Percentual_Reducao_ICMS]>0)
                {
                    //-- Valor da Reducao BC do ICMS
                    $oN14 = $oNovo->createElement("pRedBC",number_format($mT008[T008_Percentual_Reducao_ICMS],2,'.',''));
            		$oN10->appendChild($oN14);
                }
                
                //-- Percentual do ICMS
                $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
         		$oN10->appendChild($oN16);
                
                //-- Valor do ICMS
                $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
        		$oN10->appendChild($oN17);
                
                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP
                    $oN17a = $oNovo->createElement("vBCFCP",number_format($mT008[T008_Valor_Base_FCP],2,'.',''));
                    $oN10->appendChild($oN17a);

                    //-- Percentual do Fundo de Combate à Pobreza (FCP)
                    $oN17b = $oNovo->createElement("pFCP",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN10->appendChild($oN17b);

                    //-- Valor do Fundo de Combate à Pobreza (FCP)
                    $oN17c = $oNovo->createElement("vFCP",number_format($mT008[T008_Valor_FCP],2,'.',''));
                    $oN10->appendChild($oN17c);
                }

                if ($mT008[T008_Valor_ICMS_Substituicao_Retencao]>0 or $mT008[T008_Valor_Base_ICMS_Substituicao]>0)
                {
                    //-- Modalidade de determinação da BC do ICMS ST
                    //-- 0 – Preço tabelado ou máximo sugerido;
                    //-- 1 - Lista Negativa (valor);
                    //-- 2 - Lista Positiva (valor);
                    //-- 3 - Lista Neutra (valor);
                    //-- 4 - Margem Valor Agregado (%);
                    //-- 5 - Pauta (valor);
                    $oN18 = $oNovo->createElement("modBCST","{$mT008['T008_Modalidade_ICMS_Substituicao']}");
            		$oN10->appendChild($oN18);

                    if ($mT008[T008_ST_VA]>0)
                    {
                       //-- Percentual da margem de valor Adicionado do ICMS ST
                       $oN19 = $oNovo->createElement("pMVAST",number_format($mT008[T008_ST_VA],2,'.',''));
            		   $oN10->appendChild($oN19);
            		}

                    //-- Percentual da Redução de BC do ICMS ST
                    if ($mT008[T008_Percentual_Reducao_ICMS_ST]>0)
                    {
                       $oN20 = $oNovo->createElement("pRedBCST",number_format($mT008[T008_Percentual_Reducao_ICMS_ST],2,'.',''));
            		   $oN10->appendChild($oN20);
            		}

                    //-- Valor da BC do ICMS ST
                    $oN21 = $oNovo->createElement("vBCST",number_format($mT008[T008_Valor_Base_ICMS_Substituicao],2,'.',''));
             		$oN10->appendChild($oN21);

                    if ($mT008[T008_ICMS_Inter_Estadual]>0)
                    {
                        //-- Alíquota do imposto do ICMS ST
                        //$oN22 = $oNovo->createElement("pICMSST",number_format($mT008[T008_ICMS_Inter_Estadual],2,'.',''));
                        $oN22 = $oNovo->createElement("pICMSST",number_format($aliquotaICMSST,2,'.',''));
                		$oN10->appendChild($oN22);
                    }

                    //-- Valor do ICMS ST retido
                    $oN23 = $oNovo->createElement("vICMSST",number_format($mT008[T008_Valor_ICMS_Substituicao_Retencao],2,'.',''));
             		$oN10->appendChild($oN23);

                    if($mT008[T008_Percentual_FCP] > 0){
                        //-- Valor da Base de Cálculo do FCP retido por Substituição Tributária
                        $oN23a = $oNovo->createElement("vBCFCPST",number_format($mT008[T008_Valor_Base_FCP_ST],2,'.',''));
                        $oN10->appendChild($oN23a);

                        //-- Percentual do FCP retido por Substituição Tributária
                        $oN23b = $oNovo->createElement("pFCPST",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                        $oN10->appendChild($oN23b);

                        //-- Valor do FCP retido por Substituição Tributária
                        $oN23d = $oNovo->createElement("vFCPST",number_format($mT008[T008_Valor_FCP_ST],2,'.',''));
                        $oN10->appendChild($oN23d);
                    }

                    if(!empty($mT008[T008_Motivo_Desoneracao_ICMS])){
                        //-- Valor desonerado
                        $oN27 = $oNovo->createElement("vICMSDeson",$mT008[T008_Valor_ICMS_Desonerado]);
                        $oN10->appendChild($oN27);                

                        //-- Motivo da desoneração
                        $oN28 = $oNovo->createElement("motDesICMS","{$mT008['T008_Motivo_Desoneracao_ICMS']}");
                        $oN10->appendChild($oN28);
                    }
                }
            }
            elseif($mT008[T008_D059_Id]==12 or $mT008[T008_D059_Id]==14)
            {
                //-- 12 - 10 TRIBUTADA E COM COBRANÇA DE ICMS POR SUBSTITUIÇÃO TRIBUTÁRIA (COM PARTILHA DO ICMS ENTRE A UF DE ORIGEM E UF DE DESTINO DEFINIDA NA LEGISLAÇÃO)
                //-- 14 - 90 OUTRAS (COM PARTILHA DO ICMS ENTRE A UF DE ORIGEM E A UF DE DESTINO OU A UF DEFINIDA NA LEGISLAÇÃO)
            
                //-- Partilha do ICMS entre a UF de origem e UF de destino ou a UF definida na legislação.
                //-- Operação interestadual para consumidor final com partilha do ICMS devido na operação entre a UF de origem e a UF do
                //-- destinatário ou a UF definida na Página 136 de 232 # ID Campo Descrição Ele Pai Tipo Ocorrência tamanho Dec. Observação
                //-- legislação. (Ex. UF da concessionária de entrega do veículos) (v2.0)
                $oN10a = $oNovo->createElement("ICMSPart");
        		$oN01->appendChild($oN10a);

                //-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno. (v2.0)
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
          		$oN10a->appendChild($oN11);
          		
        		//-- Tributação pelo ICMS
                //-- 10 - Tributada e com cobrança do ICMS por substituição tributária;
                //-- 90 – Outros.
                $oN12 = $oNovo->createElement("CST","{$mT008['D059_Codigo_ST']}");
        		$oN10a->appendChild($oN12);

        		//-- Modalidade de determinação da BC do ICMS
                //-- 0 - Margem Valor Agregado (%);
                //-- 1 - Pauta (Valor);
                //-- 2 - Preço Tabelado Máx. (valor);
                //-- 3 - valor da operação.
                $oN13 = $oNovo->createElement("modBC","{$mT008['T008_Modalidade_ICMS']}");
        		$oN10a->appendChild($oN13);

        		//-- Valor da BC do ICMS
                $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
        		$oN10a->appendChild($oN15);

                if ($mT008[T008_Percentual_Reducao_ICMS]>0)
                {
        		    //-- Percentual da Redução de BC
                    $oN14 = $oNovo->createElement("pRedBC",number_format($mT008[T008_Percentual_Reducao_ICMS],2,'.',''));
          		    $oN10a->appendChild($oN14);
          		}

        		//-- Alíquota do imposto
                $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
        		$oN10a->appendChild($oN16);

        		//-- Valor do ICMS
                $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
        		$oN10a->appendChild($oN17);

        		//-- Modalidade de determinação da BC do ICMS ST
        		//-- 0 – Preço tabelado ou máximo sugerido
        		//-- 1 - Lista Negativa (valor);
                //-- 2 - Lista Positiva (valor);
                //-- 3 - Lista Neutra (valor);
                //-- 4 - Margem Valor Agregado (%);
                //-- 5 - Pauta (valor)
                $oN18 = $oNovo->createElement("modBCST","{$mT008['T008_Modalidade_ICMS_Substituicao']}");
        		$oN10a->appendChild($oN18);

        		//-- Percentual da margem de valor Adicionado do ICMS ST
                if ($mT008[T008_ST_VA]>0)
                {
                   $oN19 = $oNovo->createElement("pMVAST",number_format($mT008[T008_ST_VA],2,'.',''));
        		   $oN10a->appendChild($oN19);
        		}

        		//-- Percentual da Redução de BC do ICMS ST
                if ($mT008[T008_Percentual_Reducao_ICMS_ST]>0)
                {
                   $oN20 = $oNovo->createElement("pRedBCST",number_format($mT008[T008_Percentual_Reducao_ICMS_ST],2,'.',''));
        		   $oN10a->appendChild($oN20);
        		}

        		//-- Valor da BC do ICMS ST
                $oN21 = $oNovo->createElement("vBCST",number_format($mT008[T008_Valor_Base_ICMS_Substituicao],2,'.',''));
        		$oN10a->appendChild($oN21);

        		//-- Alíquota do imposto do ICMS ST
                //$oN22 = $oNovo->createElement("pICMSST",number_format($mT008[T008_ICMS_Inter_Estadual],2,'.',''));
                $oN22 = $oNovo->createElement("pICMSST",number_format($aliquotaICMSST,2,'.',''));
        		$oN10a->appendChild($oN22);

        		//-- Valor do ICMS ST
                $oN23 = $oNovo->createElement("vICMSST",number_format($mT008[T008_Valor_ICMS_Substituicao_Retencao],2,'.',''));
        		$oN10a->appendChild($oN23);

        		//-- Percentual da BC operação própria
        		//-- Percentual para determinação do valor da Base de Cálculo da operação própria.
                $oN25 = $oNovo->createElement("pBCOp",number_format($mT008[T008_Percentual_Base_Operacao_Propria],2,'.',''));
        		$oN10a->appendChild($oN25);
        		
        		//-- UF para qual é devido o ICMS ST
        		//-- Sigla da UF para qual é devido o ICMS ST da operação. (v2.0)
                $oN24 = $oNovo->createElement("UFST",$mT008[T008_UF_ICMS_Substituicao_Devido]);
        		$oN10a->appendChild($oN24);

            }
            elseif($mT008[T008_D059_Id]==13)
            {
                //-- 13 - 41 NÃO TRIBUTADA (ICMSST DEVIDO PARA UF DE DESTINO, NAS OPERAÇÕES INTERESTADUAIS DE PRODUTOS QUE TIVERAM RETENÇÃO ANTECIPADA DE ICMS POR ST NA UF DO REMETENTE)
                
                //-- ICMS ST – repasse de ICMS ST retido anteriormente em operações interestaduais com
                //-- repasses através do Substituto Tributário
                //-- Grupo de informação do ICMS ST devido para a UF de destino, nas operações interestaduais de
                //-- produtos que tiveram retenção antecipada de ICMS por ST na UF do remetente.
                //-- Repasse via Substituto Tributário. (v2.0)
                $oN10b = $oNovo->createElement("ICMSST");
        		$oN01->appendChild($oN10b);

                //-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno. (v2.0)
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
          		$oN10b->appendChild($oN11);
          		
        		//-- Tributação pelo ICMS
                //-- 41 – Não Tributado (v2.0)
                $oN12 = $oNovo->createElement("CST","41");
        		$oN10b->appendChild($oN12);

                //-- Valor do BC do ICMS ST retido na UF remetente
        		//-- Informar o valor da BC do ICMS ST retido na UF remetente (v2.0)
                $oN26 = $oNovo->createElement("vBCSTRet",number_format($mT008[T008_Valor_Base_ICMS_Substituicao_Retido],2,'.',''));
        		$oN10b->appendChild($oN26);

                //-- Valor do ICMS ST retido na UF remetente
        		//-- Informar o valor do ICMS ST retido na UF remetente (iv2.0)
                $oN27 = $oNovo->createElement("vICMSSTRet",number_format($mT008[T008_Valor_ICMS_Substituicao_Retido],2,'.',''));
        		$oN10b->appendChild($oN27);

        		//-- Valor da BC do ICMS ST da UF destino
                //-- Informar o valor da BC do ICMS ST da UF destino (v2.0)
                $oN31 = $oNovo->createElement("vBCSTDest",number_format($mT008[T008_Valor_Base_ICMS_Substituicao_Destino],2,'.',''));
        		$oN10b->appendChild($oN31);

        		//-- Valor do ICMS ST da UF destino
                //-- Informar o valor da BC do ICMS ST da UF destino (v2.0)
                $oN32 = $oNovo->createElement("vICMSSTDest",number_format($mT008[T008_Valor_ICMS_Substituicao_Destino],2,'.',''));
        		$oN10b->appendChild($oN32);
        		
            }
            elseif($mT008[T008_D059_Id]==15)
            {
                //-- 15 - 101 Tributada pelo Simples Nacional com permissão de crédito
                
                //-- Grupo CRT=1 – Simples Nacional e CSOSN=101
                //-- Tributação do ICMS pelo SIMPLES NACIONAL e CSOSN=101 (v.2.0)
                $oN10c = $oNovo->createElement("ICMSSN101");
        		$oN01->appendChild($oN10c);
        		
                //-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno. (v2.0)
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
          		$oN10c->appendChild($oN11);

        		//-- Código de Situação da Operação – Simples Nacional
                //-- 101- Tributada pelo Simples Nacional com permissão de crédito. (v.2.0)
                $oN12a = $oNovo->createElement("CSOSN","101");
        		$oN10c->appendChild($oN12a);
        		
        		//-- Alíquota aplicável de cálculo do crédito (Simples Nacional).
                $oN29 = $oNovo->createElement("pCredSN",number_format($mT008[T008_Aliquota_ICMS_Credito],2,'.',''));
        		$oN10c->appendChild($oN29);

        		//-- Valor crédito do ICMS que pode ser aproveitado nos termos do art. 23 da LC 123 (Simples Nacional)
                $oN30 = $oNovo->createElement("vCredICMSSN",number_format($mT008[T008_Valor_ICMS_Credito],2,'.',''));
        		$oN10c->appendChild($oN30);

            }
            elseif($mT008[T008_D059_Id]==16 or $mT008[T008_D059_Id]==17 or $mT008[T008_D059_Id]==21 or $mT008[T008_D059_Id]==22)
            {
                //-- 16 - 102 Tributada pelo Simples Nacional sem permissão de crédito
                //-- 17 - 103 Isenção do ICMS no Simples Nacional para faixa de receita bruta
                //-- 21 - 300 Imune
                //-- 22 - 400 Não tributada pelo Simples Nacional

                //-- Grupo CRT=1 – Simples Nacional e CSOSN=102, 103, 300 ou 400
                //-- Tributação do ICMS pelo SIMPLES NACIONAL e CSOSN=102, 103, 300 ou 400 (v.2.0)
                $oN10d = $oNovo->createElement("ICMSSN102");
        		$oN01->appendChild($oN10d);

                //-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno. (v2.0)
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
          		$oN10d->appendChild($oN11);

        		//-- Código de Situação da Operação – Simples Nacional
                //-- 102- Tributada pelo Simples Nacional sem permissão de crédito.
                //-- 103 – Isenção do ICMS no Simples Nacional para faixa de receita bruta.
                //-- 300 – Imune.
                //-- 400 – Não tributada pelo Simples Nacional (v.2.0) (v.2.0)
                $oN12a = $oNovo->createElement("CSOSN",$mT008[D059_Codigo_ST]);
        		$oN10d->appendChild($oN12a);

            }
            elseif($mT008[T008_D059_Id]==18)
            {
                //-- 18 - 201 Tributada pelo Simples Nacional com permissão de crédito e com cobrança do ICMS por Substituição Tributária

                //-- Grupo CRT=1 – Simples Nacional e CSOSN=201
                //-- Tributação do ICMS pelo SIMPLES NACIONAL e CSOSN=201 (v.2.0)
                $oN10e = $oNovo->createElement("ICMSSN201");
        		$oN01->appendChild($oN10e);

                //-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno. (v2.0)
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
          		$oN10e->appendChild($oN11);

        		//-- Código de Situação da Operação – Simples Nacional
                //-- 201- Tributada pelo Simples Nacional com permissão de crédito e com cobrança do ICMS por Substituição Tributária (v.2.0)
                $oN12a = $oNovo->createElement("CSOSN","201");
        		$oN10e->appendChild($oN12a);
        		
        		//-- Modalidade de determinação da BC do ICMS ST
        		//-- 0 – Preço tabelado ou máximo sugerido
        		//-- 1 - Lista Negativa (valor);
                //-- 2 - Lista Positiva (valor);
                //-- 3 - Lista Neutra (valor);
                //-- 4 - Margem Valor Agregado (%);
                //-- 5 - Pauta (valor)
                $oN18 = $oNovo->createElement("modBCST","{$mT008['T008_Modalidade_ICMS_Substituicao']}");
        		$oN10e->appendChild($oN18);

        		//-- Percentual da margem de valor Adicionado do ICMS ST
                if ($mT008[T008_ST_VA]>0)
                {
                   $oN19 = $oNovo->createElement("pMVAST",number_format($mT008[T008_ST_VA],2,'.',''));
        		   $oN10e->appendChild($oN19);
        		}

        		//-- Percentual da Redução de BC do ICMS ST
                if ($mT008[T008_Percentual_Reducao_ICMS_ST]>0)
                {
                   $oN20 = $oNovo->createElement("pRedBCST",number_format($mT008[T008_Percentual_Reducao_ICMS_ST],2,'.',''));
        		   $oN10e->appendChild($oN20);
        		}

        		//-- Valor da BC do ICMS ST
                $oN21 = $oNovo->createElement("vBCST",number_format($mT008[T008_Valor_Base_ICMS_Substituicao],2,'.',''));
        		$oN10e->appendChild($oN21);

        		//-- Alíquota do imposto do ICMS ST
                //$oN22 = $oNovo->createElement("pICMSST",number_format($mT008[T008_ICMS_Inter_Estadual],2,'.',''));
                $oN22 = $oNovo->createElement("pICMSST",number_format($aliquotaICMSST,2,'.',''));
        		$oN10e->appendChild($oN22);

        		//-- Valor do ICMS ST
                $oN23 = $oNovo->createElement("vICMSST",number_format($mT008[T008_Valor_ICMS_Substituicao_Retencao],2,'.',''));
        		$oN10e->appendChild($oN23);

                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP retido por Substituição Tributária
                    $oN23a = $oNovo->createElement("vBCFCPST",number_format($mT008[T008_Valor_Base_FCP_ST],2,'.',''));
                    $oN10e->appendChild($oN23a);

                    //-- Percentual do FCP retido por Substituição Tributária
                    $oN23b = $oNovo->createElement("pFCPST",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN10e->appendChild($oN23b);

                    //-- Valor do FCP retido por Substituição Tributária
                    $oN23d = $oNovo->createElement("vFCPST",number_format($mT008[T008_Valor_FCP_ST],2,'.',''));
                    $oN10e->appendChild($oN23d);
                }
        		
        		//-- Alíquota aplicável de cálculo do crédito (Simples Nacional).
                $oN29 = $oNovo->createElement("pCredSN",number_format($mT008[T008_Aliquota_ICMS_Credito],2,'.',''));
        		$oN10e->appendChild($oN29);

        		//-- Valor crédito do ICMS que pode ser aproveitado nos termos do art. 23 da LC 123 (Simples Nacional)
                $oN30 = $oNovo->createElement("vCredICMSSN",number_format($mT008[T008_Valor_ICMS_Credito],2,'.',''));
        		$oN10e->appendChild($oN30);

            }
            elseif($mT008[T008_D059_Id]==19 or $mT008[T008_D059_Id]==20)
            {
                //-- 19 - 202- Tributada pelo Simples Nacional sem permissão de crédito e com cobrança do ICMS por Substituição Tributária
                //-- 20 - 203- Isenção do ICMS nos Simples Nacional para faixa de receita bruta e com cobrança do ICMS por Substituição Tributária (v.2.0)

                //-- Grupo CRT=1 – Simples Nacional e CSOSN=202 ou 203
                //-- Tributação do ICMS pelo SIMPLES NACIONAL e CSOSN=202 ou 203 (v.2.0)
                $oN10f = $oNovo->createElement("ICMSSN202");
        		$oN01->appendChild($oN10f);

                //-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno. (v2.0)
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
          		$oN10f->appendChild($oN11);

        		//-- Código de Situação da Operação – Simples Nacional
                //-- 202- Tributada pelo Simples Nacional sem permissão de crédito e com cobrança do ICMS por Substituição Tributária
                //-- 203- Isenção do ICMS nos Simples Nacional para faixa de receita bruta e com cobrança do ICMS por Substituição Tributária (v.2.0)
                $oN12a = $oNovo->createElement("CSOSN",$mT008[D059_Codigo_ST]);
        		$oN10f->appendChild($oN12a);
        		
        		//-- Modalidade de determinação da BC do ICMS ST
        		//-- 0 – Preço tabelado ou máximo sugerido
        		//-- 1 - Lista Negativa (valor);
                //-- 2 - Lista Positiva (valor);
                //-- 3 - Lista Neutra (valor);
                //-- 4 - Margem Valor Agregado (%);
                //-- 5 - Pauta (valor)
                $oN18 = $oNovo->createElement("modBCST","{$mT008['T008_Modalidade_ICMS_Substituicao']}");
        		$oN10f->appendChild($oN18);

        		//-- Percentual da margem de valor Adicionado do ICMS ST
                if ($mT008[T008_ST_VA]>0)
                {
                   $oN19 = $oNovo->createElement("pMVAST",number_format($mT008[T008_ST_VA],2,'.',''));
        		   $oN10f->appendChild($oN19);
        		}

        		//-- Percentual da Redução de BC do ICMS ST
                if ($mT008[T008_Percentual_Reducao_ICMS_ST]>0)
                {
                   $oN20 = $oNovo->createElement("pRedBCST",number_format($mT008[T008_Percentual_Reducao_ICMS_ST],2,'.',''));
        		   $oN10f->appendChild($oN20);
        		}

        		//-- Valor da BC do ICMS ST
                $oN21 = $oNovo->createElement("vBCST",number_format($mT008[T008_Valor_Base_ICMS_Substituicao],2,'.',''));
        		$oN10f->appendChild($oN21);

        		//-- Alíquota do imposto do ICMS ST
                //$oN22 = $oNovo->createElement("pICMSST",number_format($mT008[T008_ICMS_Inter_Estadual],2,'.',''));
                $oN22 = $oNovo->createElement("pICMSST",number_format($aliquotaICMSST,2,'.',''));
        		$oN10f->appendChild($oN22);

        		//-- Valor do ICMS ST
                $oN23 = $oNovo->createElement("vICMSST",number_format($mT008[T008_Valor_ICMS_Substituicao_Retencao],2,'.',''));
        		$oN10f->appendChild($oN23);

                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP retido por Substituição Tributária
                    $oN23a = $oNovo->createElement("vBCFCPST",number_format($mT008[T008_Valor_Base_FCP_ST],2,'.',''));
                    $oN10f->appendChild($oN23a);

                    //-- Percentual do FCP retido por Substituição Tributária
                    $oN23b = $oNovo->createElement("pFCPST",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN10f->appendChild($oN23b);

                    //-- Valor do FCP retido por Substituição Tributária
                    $oN23d = $oNovo->createElement("vFCPST",number_format($mT008[T008_Valor_FCP_ST],2,'.',''));
                    $oN10f->appendChild($oN23d);
                }
            }
            elseif($mT008[T008_D059_Id]==23)
            {
                //-- 23 - 500 ICMS cobrado anteriormente por substituição tributária (substituído) ou por antecipação
                
                //-- Grupo CRT=1 – Simples Nacional e CSOSN = 500
                //-- Tributação do ICMS pelo SIMPLES NACIONAL e CSOSN=500 (v.2.0)
                $oN10g = $oNovo->createElement("ICMSSN500");
        		$oN01->appendChild($oN10g);

                //-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno. (v2.0)
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
          		$oN10g->appendChild($oN11);

        		//-- Código de Situação da Operação – Simples Nacional
                //-- 500 – ICMS cobrado anteriormente por substituição tributária (substituído) ou por antecipação (v.2.0)
                $oN12a = $oNovo->createElement("CSOSN","500");
        		$oN10g->appendChild($oN12a);

                //-- Valor da BC do ICMS ST retido
                //-- Valor da BC do ICMS ST cobrado anteriormente por ST
                $oN26 = $oNovo->createElement("vBCSTRet",number_format($mT008[T008_Valor_Base_ICMS_Substituicao_Retido],2,'.',''));
        		$oN10g->appendChild($oN26);

                //-- Alíquota suportada pelo Consumidor Final, 
                //-- Deve ser informada a alíquota do cálculo do ICMS-ST, já incluso o FCP caso incida sobre a mercadoria.
                $oN26a = $oNovo->createElement("pST",number_format($mT008[T008_Aliquota_ST_FCP],2,'.',''));
                $oN10g->appendChild($oN26a);

                //-- Valor do ICMS ST retido
                //-- Valor do ICMS ST cobrado anteriormente por ST
                $oN27 = $oNovo->createElement("vICMSSTRet",number_format($mT008[T008_Valor_ICMS_Substituicao_Retido],2,'.',''));
        		$oN10g->appendChild($oN27);

                
                if(T008_Percentual_FCP > 0){
                    //-- Valor da Base de Cálculo do FCP retido anteriormente
                    $oN27a = $oNovo->createElement("vBCFCPSTRet",number_format($mT008[T008_Valor_Base_FCP_Ret],2,'.',''));
                    $oN10g->appendChild($oN27a);

                    //-- Percentual do FCP retido anteriormente por Substituição Tributária
                    $oN27b = $oNovo->createElement("pFCPSTRet",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN10g->appendChild($oN27b);

                    //-- Valor do FCP retido anteriormente por Substituição Tributária
                    $oN27d = $oNovo->createElement("vFCPSTRet",number_format($mT008[T008_Valor_FCP_Retido],2,'.',''));
                    $oN10g->appendChild($oN27d);

                }

            }
            elseif($mT008[T008_D059_Id]==24)
            {
                //-- 24 - 900 Outros

                //-- TAG de Grupo CRT=1 – Simples Nacional e CSOSN=900
                //-- Tributação do ICMS pelo SIMPLES NACIONAL e CSOSN=900 (v2.0)
                $oN10h = $oNovo->createElement("ICMSSN900");
        		$oN01->appendChild($oN10h);

                //-- Origem da mercadoria:
                //-- 0 – Nacional;
                //-- 1 – Estrangeira – Importação direta;
                //-- 2 – Estrangeira – Adquirida no mercado interno. (v2.0)
                $oN11 = $oNovo->createElement("orig",$cOrigem_Mercadoria);
          		$oN10h->appendChild($oN11);

        		//-- Código de Situação da Operação – Simples Nacional
                //-- 900 - Outros(v2.0)
                $oN12a = $oNovo->createElement("CSOSN","900");
        		$oN10h->appendChild($oN12a);
        		
        		//-- Modalidade de determinação da BC do ICMS
                //-- 0 - Margem Valor Agregado (%);
                //-- 1 - Pauta (Valor);
                //-- 2 - Preço Tabelado Máx. (valor);
                //-- 3 - valor da operação.
                $oN13 = $oNovo->createElement("modBC","{$mT008['T008_Modalidade_ICMS']}");
        		$oN10h->appendChild($oN13);

        		//-- Valor da BC do ICMS
                $oN15 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_ICMS_Impresso],2,'.',''));
        		$oN10h->appendChild($oN15);

                if ($mT008[T008_Percentual_Reducao_ICMS]>0)
                {
            		//-- Percentual da Redução de BC
                    $oN14 = $oNovo->createElement("pRedBC",number_format($mT008[T008_Percentual_Reducao_ICMS],2,'.',''));
            		$oN10h->appendChild($oN14);
                }
                
        		//-- Alíquota do imposto
                $oN16 = $oNovo->createElement("pICMS",number_format($mT008[T008_Aliquota_ICMS],2,'.',''));
        		$oN10h->appendChild($oN16);

        		//-- Valor do ICMS
                $oN17 = $oNovo->createElement("vICMS",number_format($mT008[T008_Valor_ICMS],2,'.',''));
        		$oN10h->appendChild($oN17);

        		//-- Modalidade de determinação da BC do ICMS ST
        		//-- 0 – Preço tabelado ou máximo sugerido
        		//-- 1 - Lista Negativa (valor);
                //-- 2 - Lista Positiva (valor);
                //-- 3 - Lista Neutra (valor);
                //-- 4 - Margem Valor Agregado (%);
                //-- 5 - Pauta (valor)
                $oN18 = $oNovo->createElement("modBCST","{$mT008['T008_Modalidade_ICMS_Substituicao']}");
        		$oN10h->appendChild($oN18);

        		//-- Percentual da margem de valor Adicionado do ICMS ST
                if ($mT008[T008_ST_VA]>0)
                {
                   $oN19 = $oNovo->createElement("pMVAST",number_format($mT008[T008_ST_VA],2,'.',''));
        		   $oN10h->appendChild($oN19);
        		}

        		//-- Percentual da Redução de BC do ICMS ST
                if ($mT008[T008_Percentual_Reducao_ICMS_ST]>0)
                {
                   $oN20 = $oNovo->createElement("pRedBCST",number_format($mT008[T008_Percentual_Reducao_ICMS_ST],2,'.',''));
        		   $oN10h->appendChild($oN20);
        		}

        		//-- Valor da BC do ICMS ST
                $oN21 = $oNovo->createElement("vBCST",number_format($mT008[T008_Valor_Base_ICMS_Substituicao],2,'.',''));
        		$oN10h->appendChild($oN21);

        		//-- Alíquota do imposto do ICMS ST
                //$oN22 = $oNovo->createElement("pICMSST",number_format($mT008[T008_ICMS_Inter_Estadual],2,'.',''));
                $oN22 = $oNovo->createElement("pICMSST",number_format($aliquotaICMSST,2,'.',''));
        		$oN10h->appendChild($oN22);

        		//-- Valor do ICMS ST
                $oN23 = $oNovo->createElement("vICMSST",number_format($mT008[T008_Valor_ICMS_Substituicao_Retencao],2,'.',''));
        		$oN10h->appendChild($oN23);

                if($mT008[T008_Percentual_FCP] > 0){
                    //-- Valor da Base de Cálculo do FCP retido por Substituição Tributária
                    $oN23a = $oNovo->createElement("vBCFCPST",number_format($mT008[T008_Valor_Base_FCP_ST],2,'.',''));
                    $oN10h->appendChild($oN23a);

                    //-- Percentual do FCP retido por Substituição Tributária
                    $oN23b = $oNovo->createElement("pFCPST",number_format($mT008[T008_Percentual_FCP],2,'.',''));
                    $oN10h->appendChild($oN23b);

                    //-- Valor do FCP retido por Substituição Tributária
                    $oN23d = $oNovo->createElement("vFCPST",number_format($mT008[T008_Valor_FCP_ST],2,'.',''));
                    $oN10h->appendChild($oN23d);
                }
        		
        		//-- Alíquota aplicável de cálculo do crédito (Simples Nacional).
                $oN29 = $oNovo->createElement("pCredSN",number_format($mT008[T008_Aliquota_ICMS_Credito],2,'.',''));
        		$oN10h->appendChild($oN29);

        		//-- Valor crédito do ICMS que pode ser aproveitado nos termos do art. 23 da LC 123 (Simples Nacional)
                $oN30 = $oNovo->createElement("vCredICMSSN",number_format($mT008[T008_Valor_ICMS_Credito],2,'.',''));
        		$oN10h->appendChild($oN30);


            }

            if($this->getModeloNF()=='55'){

                if ($mT008[T008_Classificacao_Fiscal] != '00') {
                
                    /////////////////////////////////////////////////////////////////////////////////////////
                    //-- O - Imposto sobre Produtos Industrializados
                    /////////////////////////////////////////////////////////////////////////////////////////
                    //-- TAG de grupo do IPI
                    //-- Informar apenas quando o item for sujeito ao IPI
                    $oO01 = $oNovo->createElement("IPI");
                    $oM01->appendChild($oO01);    

                    
                    /*
                    //-- Classe de enquadramento do IPI para Cigarros e Bebidas
                    //-- Preenchimento conforme Atos Normativos editados pela Receita Federal (Observação 2)
                    $oO02 = $oNovo->createElement("clEnq","");
            		$oO01->appendChild($oO02);

                    //-- CNPJ do produtor da mercadoria, quando diferente do emitente. Somente para os casos de exportação direta ou indireta.
                    //-- Informar os zeros não significativos
                    $oO03 = $oNovo->createElement("CNPJProd","CNPJProd");
            		$oO01->appendChild($oO03);

                    //-- Código do selo de controle IPI
                    //-- Preenchimento conforme Atos Normativos editados pela Receita Federal (Observação 3)
                    $oO04 = $oNovo->createElement("cSelo","O04");
            		$oO01->appendChild($oO04);

                    //-- Quantidade de selo de controle
                    $oO05 = $oNovo->createElement("qSelo","O05");
            		$oO01->appendChild($oO05);
                    */
                    
                    //-- Código de Enquadramento Legal do IPI
                    //-- Tabela a ser criada pela RFB, informar 999 enquanto a tabela não for criada
                    $mT008['T008_Codigo_Enq_Legal_IPI'] = trim($mT008['T008_Codigo_Enq_Legal_IPI']);
                    $cEnq = !empty($mT008['T008_Codigo_Enq_Legal_IPI']) ? $mT008['T008_Codigo_Enq_Legal_IPI'] : '999';
                    $oO06 = $oNovo->createElement("cEnq", $cEnq);
                    $oO01->appendChild($oO06);
            		
                    if ($this->versao_nfe=='1.10')
                    {
                        //-- TAG de grupo do CST 00, 49, 50 e 99
                        //-- Informar apenas um dos grupos O07 ou O08 com base valor atribuído ao campo O09 – CST do IPI
                        $oO07 = $oNovo->createElement("IPITrib");
                		$oO01->appendChild($oO07);
                		
                		//-- Código da situação tributária do IPI
                		//-- 00-Entrada com recuperação de crédito
                        //-- 49-Outras entradas
                        //-- 50-Saída tributada
                        //-- 99-Outras saídas
                        $oO09 = $oNovo->createElement("CST","99");
                		$oO07->appendChild($oO09);
                		
                        //-- Valor da BC do IPI
                        $oO10 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_IPI],2,'.',''));
                		$oO07->appendChild($oO10);

                        //-- Alíquota do IPI
                        $oO13 = $oNovo->createElement("pIPI",number_format($mT008[T008_Aliquota_IPI],2,'.',''));
                		$oO07->appendChild($oO13);

                        //-- Valor do IPI
                        $oO14 = $oNovo->createElement("vIPI",number_format($mT008[T008_Valor_IPI],2,'.',''));
                		$oO07->appendChild($oO14);
            		
                    }
                    elseif ($mT008[T008_Situacao_Tributaria_IPI]=='00' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='49' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='50' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='99')
                    {
                        //-- TAG de grupo do CST 00, 49, 50 e 99
                        //-- Informar apenas um dos grupos O07 ou O08 com base valor atribuído ao campo O09 – CST do IPI
                        $oO07 = $oNovo->createElement("IPITrib");
                		$oO01->appendChild($oO07);

                		//-- Código da situação tributária do IPI
                		//-- 00-Entrada com recuperação de crédito
                        //-- 49-Outras entradas
                        //-- 50-Saída tributada
                        //-- 99-Outras saídas
                        $oO09 = $oNovo->createElement("CST","{$mT008['T008_Situacao_Tributaria_IPI']}");
                		$oO07->appendChild($oO09);

                        //-- Valor da BC do IPI
                        $oO10 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_IPI],2,'.',''));
                		$oO07->appendChild($oO10);
                        /*
                        //-- Quantidade total na unidade padrão para tributação (somente para os produtos tributados por unidade)
                        $oO11 = $oNovo->createElement("qUnid","O11");
                		$oO07->appendChild($oO11);

                        //-- Valor por Unidade Tributável
                        $oO12 = $oNovo->createElement("vUnid","O12");
                		$oO07->appendChild($oO12);
                        */
                        //-- Alíquota do IPI
                        $oO13 = $oNovo->createElement("pIPI",number_format($mT008[T008_Aliquota_IPI],2,'.',''));
                		$oO07->appendChild($oO13);
                        //-- Valor do IPI
                        $oO14 = $oNovo->createElement("vIPI",number_format($mT008[T008_Valor_IPI],2,'.',''));
                		$oO07->appendChild($oO14);
                    }
                    elseif ($mT008[T008_Situacao_Tributaria_IPI]=='01' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='02' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='03' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='04' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='05' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='51' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='52' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='53' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='54' or
                            $mT008[T008_Situacao_Tributaria_IPI]=='55')
                    {
                    
                        //-- TAG de grupo do CST 01, 02, 03, 04, 51, 52, 53, 54 e 55
                        $oO08 = $oNovo->createElement("IPINT");
                		$oO01->appendChild($oO08);

                        //-- Código da situação tributária do IPI:
                        //-- 01-Entrada tributada com alíquota zero
                        //-- 02-Entrada isenta
                        //-- 03-Entrada não-tributada
                        //-- 04-Entrada imune
                        //-- 05-Entrada com suspensão
                        //-- 51-Saída tributada com alíquota zero
                        //-- 52-Saída isenta
                        //-- 53-Saída não-tributada
                        //-- 54-Saída imune
                        //-- 55-Saída com suspensã
                        $oO09 = $oNovo->createElement("CST","{$mT008['T008_Situacao_Tributaria_IPI']}");
                		$oO08->appendChild($oO09);
                    }
                }
            }

            

            /////////////////////////////////////////////////////////////////////////////////////////
            //-- P - Imposto de Importação
            /////////////////////////////////////////////////////////////////////////////////////////
            if ($cD006_Flag_Estado=='I')
            {
                //-- TAG de grupo do Imposto de Importação
                $oP01 = $oNovo->createElement("II");
        		$oM01->appendChild($oP01);

                //-- Valor da BC do Imposto de Importação
                $oP02 = $oNovo->createElement("vBC","{$mT008['T008_Valor_Total_Base_Imposto_Importacao']}");
        		$oP01->appendChild($oP02);

                //-- Valor das despesas aduaneiras
                $oP03 = $oNovo->createElement("vDespAdu","{$mT008['T008_Valor_Total_Despesas_Aduaneira']}");
        		$oP01->appendChild($oP03);

                //-- Valor do Imposto de Importação
                $oP04 = $oNovo->createElement("vII","{$mT008['T008_Valor_Total_Imposto_Importacao']}");
        		$oP01->appendChild($oP04);

                //-- Valor do Imposto sobre Operações Financeiras
                $oP05 = $oNovo->createElement("vIOF","{$mT008['T008_Valor_Total_IOF']}");
        		$oP01->appendChild($oP05);
            }

            /////////////////////////////////////////////////////////////////////////////////////////
            //-- Q – PIS
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- TAG de grupo do PIS
            //-- Informar apenas um dos grupos Q02, Q03, Q04 ou Q05 com base valor atribuído ao campo Q06 – CST do PIS
            $oQ01 = $oNovo->createElement("PIS");
    		$oM01->appendChild($oQ01);

            if ($mT008[T008_Situacao_Tributaria_PIS]=='01' || $mT008[T008_Situacao_Tributaria_PIS]=='02' || $mT008[T008_Situacao_Tributaria_PIS]=='50' || $mT008[T008_Situacao_Tributaria_PIS]=='51' || $mT008[T008_Situacao_Tributaria_PIS]=='52' || $mT008[T008_Situacao_Tributaria_PIS]=='53' || $mT008[T008_Situacao_Tributaria_PIS]=='54' || $mT008[T008_Situacao_Tributaria_PIS]=='55' || $mT008[T008_Situacao_Tributaria_PIS]=='56')
            {
                //-- TAG do grupo de PIS tributado pela alíquota
                //-- CST = 01 e 02
                $oQ02 = $oNovo->createElement("PISAliq");
        		$oQ01->appendChild($oQ02);

                //-- Código de Situação Tributária do PIS
                $oQ06 = $oNovo->createElement("CST",$mT008[T008_Situacao_Tributaria_PIS]);
        		$oQ02->appendChild($oQ06);

                //-- Valor da Base de Cálculo do PIS
                $oQ07 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_PIS],2,'.',''));
        		$oQ02->appendChild($oQ07);

                //-- Alíquota do PIS (em percentual)
                $oQ08 = $oNovo->createElement("pPIS",number_format($mT008[T008_Aliquota_PIS],2,'.',''));
        		$oQ02->appendChild($oQ08);

                //-- Valor do PIS
                $oQ09 = $oNovo->createElement("vPIS",number_format($mT008[T008_Valor_PIS],2,'.',''));
        		$oQ02->appendChild($oQ09);
            }
            elseif ($mT008[T008_Situacao_Tributaria_PIS]=='03')
            {
                //-- TAG do grupo de PIS tributado por Qtde
                //-- CST = 03
                $oQ03 = $oNovo->createElement("PISQtde");
        		$oQ01->appendChild($oQ03);

        		//-- Código de Situação Tributária do PIS
        		//-- 03 - Operação Tributável (base de cálculo = quantidade vendida x alíquota por unidade de produto);
                $oQ06 = $oNovo->createElement("CST","03");
        		$oQ03->appendChild($oQ06);

        		//-- Quantidade Vendida
                $oQ10 = $oNovo->createElement("qBCProd",number_format($mT008[T008_Quantidade],4,',','.'));
        		$oQ03->appendChild($oQ10);

                /** duvida na aliquota do pis em reais */
        		//-- Alíquota do PIS (em reais)
                $oQ11 = $oNovo->createElement("vAliqProd",number_format(0,4,',','.'));
        		$oQ03->appendChild($oQ11);

        		//-- Valor do PIS
                $oQ09 = $oNovo->createElement("vPIS",number_format($mT008[T008_Valor_Base_PIS],2,',','.'));
        		$oQ03->appendChild($oQ09);
            }
            elseif ($mT008[T008_Situacao_Tributaria_PIS]=='04' || $mT008[T008_Situacao_Tributaria_PIS]=='06' || $mT008[T008_Situacao_Tributaria_PIS]=='07' || $mT008[T008_Situacao_Tributaria_PIS]=='08' || $mT008[T008_Situacao_Tributaria_PIS]=='09')
            {
        		//-- TAG do grupo de PIS não tributado
        		//-- CST = 04, 06, 07, 08 ou 09
                $oQ04 = $oNovo->createElement("PISNT");
        		$oQ01->appendChild($oQ04);

        		//-- Código de Situação Tributária do PIS
        		//-- 04 - Operação Tributável (tributação monofásica (alíquota zero));
                //-- 06 - Operação Tributável (alíquota zero);
                //-- 07 - Operação Isenta da Contribuição;
                //-- 08 - Operação Sem Incidência da Contribuição;
                //-- 09 - Operação com Suspensão da Contribuição;
                $oQ02 = $oNovo->createElement("CST",$mT008[T008_Situacao_Tributaria_PIS]);
        		$oQ04->appendChild($oQ02);
            }
            elseif ($mT008[T008_Situacao_Tributaria_PIS]=='98' || $mT008[T008_Situacao_Tributaria_PIS]=='99' || $mT008[T008_Situacao_Tributaria_PIS]=='49')
            {
         		//-- TAG do grupo de PIS Outras Operações
        		//-- CST = 99
                $oQ05 = $oNovo->createElement("PISOutr");
        		$oQ01->appendChild($oQ05);

        		//-- Código de Situação Tributária do PIS
        		//-- 99 - Outras Operações
                $oQ06 = $oNovo->createElement("CST",$mT008[T008_Situacao_Tributaria_PIS]);
        		$oQ05->appendChild($oQ06);

        		//-- Valor da Base de Cálculo do PIS
        		//-- Informar campos para cálculo do PIS em percentual (P07 e P08) ou campos para PIS em valor (P10 e P11).
                $oQ07 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_PIS],2,'.',''));
        		$oQ05->appendChild($oQ07);

        		//-- Alíquota do PIS (em percentual)
                $oQ08 = $oNovo->createElement("pPIS",number_format($mT008[T008_Aliquota_PIS],2,'.',''));
        		$oQ05->appendChild($oQ08);

                $nQuantidade_PIS = $mT008[T008_Quantidade];
                if ($mT008[T008_Valor_Base_PIS]==0)
                {
                   $nQuantidade_PIS=0;
                }
        		//-- Quantidade Vendida
                //$oQ10 = $oNovo->createElement("qBCProd",number_format($nQuantidade_PIS,4,'.',''));
        		//$oQ05->appendChild($oQ10);

                /** duvida na aliquota do pis em reais */
        		//-- Alíquota do PIS (em reais)
                //$oQ11 = $oNovo->createElement("vAliqProd",number_format(0,4,'.',''));
        		//$oQ05->appendChild($oQ11);

        		//-- Valor do PIS
                $oQ09 = $oNovo->createElement("vPIS",number_format($mT008[T008_Valor_PIS],2,'.',''));
        		$oQ05->appendChild($oQ09);
            }
            /*
            elseif ($mT008[T008_Situacao_Tributaria_PIS]=='99')
            {
        		//-- TAG do grupo de PIS Substituição Tributária
                $oR01 = $oNovo->createElement("PISST");
        		$oM01->appendChild($oR01);

        		//-- Valor da Base de Cálculo do PIS
        		//-- Informar campos para cálculo do PIS em percentual (R02 e R03) ou campos para PIS em valor (R04 e R05).
                $oR02 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_PIS],2,',','.'));
        		$oR01->appendChild($oR02);

        		//-- Alíquota do PIS (em percentual)
                $oR03 = $oNovo->createElement("pPIS",number_format($mT008[T008_Aliquota_PIS],2,',','.'));
        		$oR01->appendChild($oR03);

        		//-- Quantidade Vendida
                //$oR04 = $oNovo->createElement("qBCProd",number_format($mT008[T008_Quantidade],4,',','.'));
        		//$oR01->appendChild($oR04);
        		
                // duvida na aliquota do pis em reais
        		//-- Alíquota do PIS (em reais)
                $oR05 = $oNovo->createElement("vAliqProd",number_format(0,4,',','.'));
        		$oR01->appendChild($oR05);

        		//-- Valor do PIS
                $oR06 = $oNovo->createElement("vPIS",number_format($mT008[T008_Valor_PIS],2,',','.'));
        		$oR01->appendChild($oR06);
            } */
            
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- S – COFINS
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- TAG de grupo do COFINS
            //-- Informar apenas um dos grupos S02, S03, S04 ou S04 com base valor atribuído ao campo S06 – CST do COFINS
            $oS01 = $oNovo->createElement("COFINS");
    		$oM01->appendChild($oS01);

            if ($mT008[T008_Situacao_Tributaria_COFINS]=='01' || $mT008[T008_Situacao_Tributaria_COFINS]=='02' || $mT008[T008_Situacao_Tributaria_COFINS]=='50' || $mT008[T008_Situacao_Tributaria_COFINS]=='51' || $mT008[T008_Situacao_Tributaria_COFINS]=='52' || $mT008[T008_Situacao_Tributaria_COFINS]=='53' || $mT008[T008_Situacao_Tributaria_COFINS]=='54' || $mT008[T008_Situacao_Tributaria_COFINS]=='55' || $mT008[T008_Situacao_Tributaria_COFINS]=='56')
            {
        		//-- CST = 01 ou 02
                $oS02 = $oNovo->createElement("COFINSAliq");
        		$oS01->appendChild($oS02);

        		//-- Código de Situação Tributária do COFINS
        		//-- 01 – Operação Tributável (base de cálculo = valor da operação alíquota normal (cumulativo/não cumulativo));
                //-- 02 - Operação Tributável (base de cálculo = valor da operação (alíquota diferenciada));
                $oS06 = $oNovo->createElement("CST",$mT008[T008_Situacao_Tributaria_COFINS]);
        		$oS02->appendChild($oS06);

        		//-- Valor da Base de Cálculo da COFINS
                $oS07 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_Base_COFINS],2,'.',''));
        		$oS02->appendChild($oS07);

        		//-- Alíquota da COFINS (em percentual)
                $oS08 = $oNovo->createElement("pCOFINS",number_format($mT008[T008_Aliquota_COFINS],2,'.',''));
        		$oS02->appendChild($oS08);

        		//-- Valor do COFINS
                $oS11 = $oNovo->createElement("vCOFINS",number_format($mT008[T008_Valor_COFINS],2,'.',''));
        		$oS02->appendChild($oS11);
            }
            elseif ($mT008[T008_Situacao_Tributaria_COFINS]=='03')
            {
          		//-- TAG do grupo de COFINS tributado por Qtde
        		//-- CST = 03
                $oS03 = $oNovo->createElement("COFINSQtde");
        		$oS01->appendChild($oS03);

        		//-- 03 - Operação Tributável (base de cálculo = quantidade vendida x alíquota por unidade de produto);
                $oS06 = $oNovo->createElement("CST","03");
        		$oS03->appendChild($oS06);

        		//-- Quantidade Vendida
                $oS09 = $oNovo->createElement("qBCProd",number_format($mT008[T008_Quantidade],4,'.',''));
        		$oS03->appendChild($oS09);

                /** duvida na aliquota do cofins em reais */
        		//-- Alíquota do COFINS (em reais)
                $oS10 = $oNovo->createElement("vAliqProd",number_format(0,4,'.',''));
        		$oS03->appendChild($oS10);

        		//-- Valor do COFINS
                $oS11 = $oNovo->createElement("vCOFINS",number_format($mT008[T008_Valor_COFINS],2,'.',''));
        		$oS03->appendChild($oS11);
            }
            elseif ($mT008[T008_Situacao_Tributaria_COFINS]=='04' || $mT008[T008_Situacao_Tributaria_COFINS]=='06' || $mT008[T008_Situacao_Tributaria_COFINS]=='07' || $mT008[T008_Situacao_Tributaria_COFINS]=='08' || $mT008[T008_Situacao_Tributaria_COFINS]=='09')
            {
                //-- TAG do grupo de COFINS não tributado
                $oS04 = $oNovo->createElement("COFINSNT");
        		$oS01->appendChild($oS04);

        		//-- Código de Situação Tributária do COFINS
        		//-- 04 - Operação Tributável (tributação monofásica (alíquota zero));
                //-- 06 - Operação Tributável (alíquota zero);
                //-- 07 - Operação Isenta da Contribuição;
                //-- 08 - Operação Sem Incidência da Contribuição;
                //-- 09 - Operação com Suspensão da Contribuição;
                $oS06 = $oNovo->createElement("CST",$mT008[T008_Situacao_Tributaria_COFINS]);
        		$oS04->appendChild($oS06);
            }
            elseif ($mT008[T008_Situacao_Tributaria_COFINS]=='98' || $mT008[T008_Situacao_Tributaria_COFINS]=='99' || $mT008[T008_Situacao_Tributaria_COFINS]=='49')
            {
        		//-- TAG do grupo de COFINS Outras Operações
                $oS05 = $oNovo->createElement("COFINSOutr");
        		$oS01->appendChild($oS05);

        		//-- Código de Situação Tributária do COFINS
                $oS06 = $oNovo->createElement("CST",$mT008[T008_Situacao_Tributaria_COFINS]);
        		$oS05->appendChild($oS06);

        		//-- Valor da Base de Cálculo da COFINS
        		//-- Informar campos para cálculo do COFINS em percentual (S07 e S08) ou campos para COFINS em valor (S09 e S10).
                $oS07 = $oNovo->createElement("vBC",number_format($mT008[T008_Valor_COFINS],2,'.',''));
        		$oS05->appendChild($oS07);

        		//-- Alíquota da COFINS (em percentual)
                $oS08 = $oNovo->createElement("pCOFINS",number_format($mT008[T008_Aliquota_COFINS],2,'.',''));
        		$oS05->appendChild($oS08);

                $nQuantidade_COFINS = $mT008[T008_Quantidade];
                if ($mT008[T008_Valor_Base_COFINS]==0)
                {
                   $nQuantidade_COFINS=0;
                }
        		//-- Quantidade Vendida
                //$oS09 = $oNovo->createElement("qBCProd",number_format($nQuantidade_COFINS,4,'.',''));
        		//$oS05->appendChild($oS09);

                /** duvida na aliquota do cofins em reais */
        		//-- Alíquota do COFINS (em reais)
                //$oS10 = $oNovo->createElement("vAliqProd",number_format(0,4,'.',''));
        		//$oS05->appendChild($oS10);

        		//-- Valor do COFINS
                $oS11 = $oNovo->createElement("vCOFINS",number_format($mT008[T008_Valor_COFINS],2,'.',''));
        		$oS05->appendChild($oS11);
            }

            /////////////////////////////////////////////////////////////////////////////////////////
            // NA. ICMS para a UF de destino
            /////////////////////////////////////////////////////////////////////////////////////////
            // Grupo de Tributação do ICMS para a UF de destino
            //Foi criado um novo grupo de informações no item, para identificar o ICMS Interestadual nas operações de venda para consumidor final, atendendo ao disposto na Emenda Constitucional 87 de 2015.
            if($mT007['T007_Flag_Destino_Operacao'] == '2' && $this->getModeloNF() != '65' && $cFlag_Entrada_Saida != '0' && $indIEDest == '9' && $indFinal == '1'){
                $oNA01 = $oNovo->createElement("ICMSUFDest");
                $oM01->appendChild($oNA01);

                $oNA03 = $oNovo->createElement("vBCUFDest", number_format($mT008['T008_Valor_Base_Partilha_ICMS'], 2, '.', ''));
                $oNA01->appendChild($oNA03);

                //-- Valor da BC FCP na UF de destino
                $oNA04 = $oNovo->createElement("vBCFCPUFDest", number_format($mT008['T008_Valor_Base_FCP_Dest'], 2, '.', ''));
                $oNA01->appendChild($oNA04);

                $oNA05 = $oNovo->createElement("pFCPUFDest", number_format($mT008['T008_Percentual_FCP'], 2, '.', ''));
                $oNA01->appendChild($oNA05);

                $oNA07 = $oNovo->createElement("pICMSUFDest", number_format($mT008['T008_ST_ICMS_Interno'], 2, '.', ''));
                $oNA01->appendChild($oNA07);

                $oNA09 = $oNovo->createElement("pICMSInter", number_format($mT008['T008_Aliquota_ICMS_Oculto'], 2, '.', ''));
                $oNA01->appendChild($oNA09);

                $oNA11 = $oNovo->createElement("pICMSInterPart", number_format($mT008['T008_Percentual_Partilha_ICMS_Destino'], 2, '.', ''));
                $oNA01->appendChild($oNA11);

                $oNA13 = $oNovo->createElement("vFCPUFDest", number_format($mT008['T008_Valor_FCP'], 2, '.', ''));
                $oNA01->appendChild($oNA13);

                $oNA15 = $oNovo->createElement("vICMSUFDest", number_format($mT008['T008_Valor_Partilha_ICMS_Destino'], 2, '.', ''));
                $oNA01->appendChild($oNA15);

                $oNA17 = $oNovo->createElement("vICMSUFRemet", number_format($mT008['T008_Valor_Partilha_ICMS_Origem'], 2, '.', ''));
                $oNA01->appendChild($oNA17);
            }

            /////////////////////////////////////////////////////////////////////////////////////////
            //-- V - Informações adicionais
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- Informações Adicionais do Produto
            //-- Norma referenciada, informações complementares, etc.
            $mT008[T008_Informacoes_Adicionais] = $this->adicionarEspape($mT008[T008_Informacoes_Adicionais]);
            if (!empty($mT008[T008_Informacoes_Adicionais])) {
                $oV01 = $oNovo->createElement("infAdProd",$mT008[T008_Informacoes_Adicionais]);
                $oH01->appendChild($oV01);
            }

            /*
            ******* duvida
    		////////////////////////////////////////////////////////////////
            //-- T - COFINS ST
    		////////////////////////////////////////////////////////////////
    		//-- TAG do grupo de COFINS Substituição Tributária
            $oT01 = $oNovo->createElement("COFINSST");
    		$oM01->appendChild($oT01);

    		//-- Valor do COFINS
            $oT02 = $oNovo->createElement("vBC","T02");
    		$oT01->appendChild($oT02);

    		//-- Alíquota da COFINS (em percentual)
            $oT03 = $oNovo->createElement("pCOFINS","T03");
    		$oT01->appendChild($oT03);

    		//-- Quantidade Vendida
            $oT04 = $oNovo->createElement("qBCProd","T04");
    		$oT01->appendChild($oT04);

    		//-- Alíquota do COFINS (em reais)
            $oT05 = $oNovo->createElement("vAliqProd","T05");
    		$oT01->appendChild($oT05);

    		//-- Valor do COFINS
            $oT06 = $oNovo->createElement("vCOFINS","T06");
    		$oT01->appendChild($oT06);
    		*/
    		
            /*

            ******* duvida
            
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- U - ISSQN
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- TAG do grupo do ISSQN
            //-- Informar os campos para cálculo do ISSQN nas NFe conjugadas, onde há a prestação de serviços
            //-- sujeitos ao ISSQN e fornecimento de peças sujeitas ao ICMS
            $oU01 = $oNovo->createElement("ISSQN");
    		$oM01->appendChild($oU01);

    		//-- Valor do COFINS
            $oU02 = $oNovo->createElement("vBC","U02");
    		$oU01->appendChild($oU02);

    		//-- Alíquota do ISSQN
            $oU03 = $oNovo->createElement("vAliq","U03");
    		$oU01->appendChild($oU03);

    		//-- Valor do ISSQN
            $oU04 = $oNovo->createElement("vISSQN","U04");
    		$oU01->appendChild($oU04);

    		//-- Código do município de ocorrência do fato gerador do ISSQN
    		//-- Informar o município de ocorrência do fato gerador do ISSQN. Utilizar a Tabela do IBGE (Anexo IV - Tabela de UF, Município e País)
            $oU05 = $oNovo->createElement("cMunFG","U05");
    		$oU01->appendChild($oU05);

    		//-- Código da Lista de Serviços
    		//-- Informar o código da lista de serviços da LC 116/03 em que se classifica o serviço.
            $oU06 = $oNovo->createElement("cListServ","cListServ");
    		$oU01->appendChild($oU06);
    		*/

            // Grupo de informações implementado 08/10/2018 - Raquel
            /////////////////////////////////////////////////////////////////////////////////////////
            //-- U - Devolvidos (para o item da NF-e) # ID
            /////////////////////////////////////////////////////////////////////////////////////////[]
            if($this->getFinalidadeNFe() == 4 AND $mT008['T008_Valor_IPI_Devolucao'] > 0 ){
                //-- UA01 impostoDevol Informação do Imposto devolvido
                $oUA01 = $oNovo->createElement("impostoDevol");
                $oH01->appendChild($oUA01);

                //-- UA02 pDevol Percentual da mercadoria devolvida E UA01 N 1-1 3v2
                $oUA02 = $oNovo->createElement("pDevol", "100.00");
                $oUA01->appendChild($oUA02);

                //-- UA03 IPI Informação do IPI devolvido G UA01 1-1
                $oUA03 = $oNovo->createElement("IPI");
                $oUA01->appendChild($oUA03);

                //-- UA04 vIPIDevol Valor do IPI devolvido E UA03 N 1-1 13v2
                $oUA04 = $oNovo->createElement("vIPIDevol", number_format($mT008['T008_Valor_IPI_Devolucao'], 2, '.', ''));
                $oUA03->appendChild($oUA04);
            }

        }

        /////////////////////////////////////////////////////////////////////////////////////////
        //-- W - Valores Totais da NF-e
        /////////////////////////////////////////////////////////////////////////////////////////
        //-- TAG de grupo de Valores Totais  da NF-e
        $oW01     = $oNovo->createElement("total");
		$oA01->appendChild($oW01);

        //-- TAG de grupo de Valores Totais referentes ao ICMS
        $oW02     = $oNovo->createElement("ICMSTot");
		$oW01->appendChild($oW02);

        //-- Base de Cálculo do ICMS
        $oW03     = $oNovo->createElement("vBC",number_format($mT007[T007_Valor_Total_Base_ICMS_Impresso],2,'.',''));
		$oW02->appendChild($oW03);

        //-- Valor Total do ICMS
        $oW04     = $oNovo->createElement("vICMS",number_format($mT007[T007_Valor_Total_ICMS],2,'.',''));
		$oW02->appendChild($oW04);

        //-- Valor Total do ICMS Desonerado
        $oW04a     = $oNovo->createElement("vICMSDeson",number_format($mT007[T007_Valor_Total_ICMS_Desonerado],2,'.',''));
        $oW02->appendChild($oW04a);

        //-- Valor total do ICMS relativo Fundo de Combate à Pobreza (FCP) da UF de destino
        $W04c     = $oNovo->createElement("vFCPUFDest",number_format($mT007[T007_Valor_Total_FCP],2,'.',''));
        $oW02->appendChild($W04c);

        //-- Valor total do ICMS Interestadual para a UF de destino
        $W04e     = $oNovo->createElement("vICMSUFDest",number_format($mT007[T007_Valor_Total_Partilha_ICMS_Destino],2,'.',''));
        $oW02->appendChild($W04e);

        //-- Valor total do ICMS Interestadual para a UF do remetente
        $W04g     = $oNovo->createElement("vICMSUFRemet",number_format($mT007[T007_Valor_Total_Partilha_ICMS_Origem],2,'.',''));
        $oW02->appendChild($W04g);

        if($mT007[T007_Valor_Total_FCP] > 0 && $indIEDest == '9' && $indFinal == '1'){
            $mT007[T007_Valor_Total_FCP] = 0 ;
        }
        //-- Valor Total do FCP (Fundo de Combate à Pobreza)
        $oW04h = $oNovo->createElement("vFCP", number_format($mT007[T007_Valor_Total_FCP],2,'.',''));
        $oW02->appendChild($oW04h);

        //-- Base de Cálculo do ICMS ST
        $oW05     = $oNovo->createElement("vBCST",number_format($mT007[T007_Valor_Base_ICMS_Substituicao],2,'.',''));
		$oW02->appendChild($oW05);

        //-- Valor Total do ICMS ST
        $oW06     = $oNovo->createElement("vST",number_format($mT007[T007_Valor_ICMS_Substituicao_Retencao],2,'.',''));
        $oW02->appendChild($oW06);

        //-- Valor Total do FCP (Fundo de Combate à Pobreza) retido por substituição tributária
        $oW06a     = $oNovo->createElement("vFCPST",number_format($mT007[T007_Valor_FCP_ST],2,'.',''));
        $oW02->appendChild($oW06a);

        //-- Valor Total do FCP retido anteriormente por Substituição Tributária
        $oW06b     = $oNovo->createElement("vFCPSTRet",number_format($mT007[T007_Valor_FCP_ST_Ret],2,'.',''));
        $oW02->appendChild($oW06b);
        
        //-- Valor Total dos produtos e serviços
        $oW07     = $oNovo->createElement("vProd",number_format($mT007[T007_Valor_Total_Produtos],2,'.',''));
		$oW02->appendChild($oW07);

        //-- Valor Total do Frete
        $oW08     = $oNovo->createElement("vFrete",number_format($mT007[T007_Valor_Frete],2,'.',''));
		$oW02->appendChild($oW08);

        //-- Valor Total do Seguro
        $oW09     = $oNovo->createElement("vSeg",number_format($mT007[T007_Valor_Seguro],2,'.',''));
		$oW02->appendChild($oW09);

        //-- Valor Total do Desconto
        $oW10     = $oNovo->createElement("vDesc",number_format($mT007[T007_Valor_Desconto],2,'.',''));
		$oW02->appendChild($oW10);

        //-- Valor Total do II
        $oW11     = $oNovo->createElement("vII",number_format($mT007[T007_Valor_Total_Imposto_Importacao],2,'.',''));
		$oW02->appendChild($oW11);

        //-- Valor Total do IPI
        $oW12     = $oNovo->createElement("vIPI",number_format($mT007[T007_Valor_Total_IPI],2,'.',''));
		$oW02->appendChild($oW12);

        //-- Valor Total do IPI Devolvido finNFe=4 (devolução)
        $oW12a     = $oNovo->createElement("vIPIDevol",$mT007[T007_Valor_Total_IPI_Devolucao]);
        $oW02->appendChild($oW12a);

        //-- Valor do PIS
        $oW13     = $oNovo->createElement("vPIS",number_format($mT007[T007_Valor_Total_PIS],2,'.',''));
		$oW02->appendChild($oW13);

        //-- Valor do COFINS
        $oW14     = $oNovo->createElement("vCOFINS",number_format($mT007[T007_Valor_Total_COFINS],2,'.',''));
		$oW02->appendChild($oW14);

        //-- Valor das despesas acessorias
        $oW15     = $oNovo->createElement("vOutro",number_format($mT007[T007_Valor_Despesas_Acessorias],2,'.',''));
		$oW02->appendChild($oW15);

        //-- Valor Total da NF-e
        $oW16     = $oNovo->createElement("vNF",number_format($mT007[T007_Valor_Total],2,'.',''));
		$oW02->appendChild($oW16);

        //-- TAG Lei da Transparencia (opcional)
        // Criação de campo opcional para que o contribuinte possa informar o valor aproximado correspondente a totalidade dos tributos federais, estaduais e municipais, cuja incidência influa na formação do respectivo preço de venda, atendendo o disposto na Lei citada; (Lei nº 12.741/12)
        if(isset($mT007['T007_Valor_Total_Trib_Transparencia']) && $mT007['T007_Valor_Total_Trib_Transparencia'] > 0 && $mT007['C020_Atualizacao_Lei_Transparencia']=='N') {
            //$oW16a = $oNovo->createElement("vTotTrib", number_format($mT007[T007_Valor_Total_Trib_Transparencia],2,'.',''));
            $oW16a = $oNovo->createElement("vTotTrib", number_format($this->vTotTrib,2,'.',''));
            $oW02->appendChild($oW16a);
        }

		/*
		******************** DUVIDA

        //-- TAG de grupo de Valores Totais referentes ao ISSQN
        $oW17     = $oNovo->createElement("ISSQNtot");
		$oW01->appendChild($oW17);
		
        //-- Valor Total dos Serviços sob nãoincidência ou não tributados pelo ICMS
        $oW18     = $oNovo->createElement("vServ","W18");
		$oW17->appendChild($oW18);
		
        //-- Base de Cálculo do ISS
        $oW19     = $oNovo->createElement("vBC","W19");
		$oW17->appendChild($oW19);
		
        //-- Valor Total do ISS
        $oW20     = $oNovo->createElement("vISS","W20");
		$oW17->appendChild($oW20);
		
        //-- Valor do PIS sobre serviços
        $oW21     = $oNovo->createElement("vPIS","W21");
		$oW17->appendChild($oW21);

        //-- Valor do COFINS sobre serviços
        $oW22     = $oNovo->createElement("vCOFINS","W22");
		$oW17->appendChild($oW22);
		
        //-- TAG de grupo de Retenções de Tributos
        $oW23     = $oNovo->createElement("retTrib");
		$oW01->appendChild($oW23);
		
		//////////////////////////////////////////////////////////////////////////////////////////////////
		//-- Exemplos de atos normativos que definem obrigatoriedade da retenção de contribuições:
        //-- a) IRPJ/CSLL/PIS/COFINS - Fonte - Recebimentos de Órgãos Públicos Federais Lei nº 9.430, de 27 de dezembro de
        //--    1996, art. 64 Lei nº 10.833/2003, art. 34 como normas infra-legais, temos como exemplo:
        //--    Instrução Normativa SRF nº480/2004 e Instrução Normativa nº 539, de 25/04/2005.
        //-- b) Retenção do Imposto de Renda pelas Fontes Pagadoras REMUNERAÇÃO DE SERVIÇOS PROFISSIONAIS PRESTADOS
        //--    POR PESSOA JURÍDICA LEI Nº 7.450/85, ART. 52
        //-- c) IRPJ, CSLL, COFINS e PIS - Serviços Prestados por Pessoas Jurídicas - Retenção na Fonte
        //--    Lei nº 10.833 de 29.12.2003, arts.30, 31, 32, 35 e 36
		//////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Valor Retido de PIS
        $oW24     = $oNovo->createElement("vRetPIS","W24");
		$oW23->appendChild($oW24);
		
        //-- Valor Retido de COFINS
        $oW25     = $oNovo->createElement("vRetCOFINS","W25");
		$oW23->appendChild($oW25);
		
        //-- Valor Retido de CSLL
        $oW26     = $oNovo->createElement("vRetCSLL","W26");
		$oW23->appendChild($oW26);
		
        //-- VBase de Cálculo do IRRF
        $oW27     = $oNovo->createElement("vBCIRRF","W27");
		$oW23->appendChild($oW27);
		
        //-- Valor Retido do IRRF
        $oW28     = $oNovo->createElement("vBCIRRF","W28");
		$oW23->appendChild($oW28);
		
        //-- Base de Cálculo da Retenção da Previdência Social
        $oW29     = $oNovo->createElement("vBCRetPrev","W29");
		$oW23->appendChild($oW29);
		
        //-- Valor da Retenção da Previdência Social
        $oW30     = $oNovo->createElement("vRetPrev","W30");
		$oW23->appendChild($oW30);
		*/
		
		////////////////////////////////////////////////////////////
        //-- X - Informações do Transporte da NF-e
		////////////////////////////////////////////////////////////
		
        //-- TAG de grupo de Informações do Transporte da NF-e
        $oX01     = $oNovo->createElement("transp");
		$oA01->appendChild($oX01);
		
        //-- Modalidade do frete
        //-- 0 – por conta do emitente;
        //-- 1 – por conta do destinatário;

        $oX02     = $oNovo->createElement("modFrete","$nFlagFrete");
		$oX01->appendChild($oX02);
		
        if (!empty($mT007[T007_Nome_Transportadora]))
		{
            //-- TAG de grupo Transportador
            $oX03     = $oNovo->createElement("transporta");
    		$oX01->appendChild($oX03);

    		//-- Para as duas tags abaixo:
    		//-- Informar o CNPJ ou o CPF do Transportador, preenchendo os zeros não significativos.
            if (strlen($mT007[T007_Transportadora_CNPJ])=='14')
            {
                //-- CNPJ
                $oX04 = $oNovo->createElement("CNPJ",$mT007[T007_Transportadora_CNPJ]);
    		    $oX03->appendChild($oX04);
            }
            elseif (strlen($mT007[T007_Transportadora_CNPJ])=='11')
            {
               //-- CPF
               $oX05 = $oNovo->createElement("CPF",$mT007[T007_Transportadora_CNPJ]);
    		   $oX03->appendChild($oX05);
            }
            
            //-- Razão Social ou nome
            $mT007[T007_Nome_Transportadora]=$this->adicionarEspape($mT007[T007_Nome_Transportadora]);
            //$mT007[T007_Nome_Transportadora]=$mT007[T007_Nome_Transportadora];
            $oX06     = $oNovo->createElement("xNome",$mT007[T007_Nome_Transportadora]);
    		$oX03->appendChild($oX06);

            $mT007[T007_Transportadora_Inscricao_Estadual]=$this->adicionarEspape($mT007[T007_Transportadora_Inscricao_Estadual]);
            if (!empty($mT007[T007_Transportadora_Inscricao_Estadual]))
            {
                //-- Inscrição Estadual
                $oX07     = $oNovo->createElement("IE",$mT007[T007_Transportadora_Inscricao_Estadual]);
        		$oX03->appendChild($oX07);
            }
            
            $mT007[T007_Transportadora_Endereco]=$this->adicionarEspape($mT007[T007_Transportadora_Endereco]);
            if (!empty($mT007[T007_Transportadora_Endereco]))
            {
                //-- Endereço Completo
                $oX08     = $oNovo->createElement("xEnder",$mT007[T007_Transportadora_Endereco]);
        		$oX03->appendChild($oX08);
        	}

            $mT007[T007_Transportadora_Cidade]=$this->adicionarEspape($mT007[T007_Transportadora_Cidade]);
        	if (!empty($mT007[T007_Transportadora_Cidade]))
        	{
                //-- Nome do município
                $oX09     = $oNovo->createElement("xMun",$mT007[T007_Transportadora_Cidade]);
        		$oX03->appendChild($oX09);
            }
            
            $mT007[T007_Transportadora_Estado]=$this->adicionarEspape($mT007[T007_Transportadora_Estado]);
            if (!empty($mT007[T007_Transportadora_Estado]))
            {
                //-- Sigla da UF
                $oX10     = $oNovo->createElement("UF",$mT007[T007_Transportadora_Estado]);
        		$oX03->appendChild($oX10);
            }
            
            /*
            //-- TAG de grupo de Retenção do ICMS do transporte
            //-- Informar o valor do ICMS do serviço de transporte retido.
            $oX11     = $oNovo->createElement("retTransp");
    		$oX01->appendChild($oX11);

            //-- Valor do Serviço
            $oX12     = $oNovo->createElement("vServ","X12");
    		$oX11->appendChild($oX12);

            //-- BC da Retenção do ICMS
            $oX13     = $oNovo->createElement("vBCRet","X13");
    		$oX11->appendChild($oX13);

            //-- Alíquota da Retenção
            $oX14     = $oNovo->createElement("pICMSRet","X14");
    		$oX11->appendChild($oX14);

            //-- Valor do ICMS Retido
            $oX15     = $oNovo->createElement("vICMSRet","X15");
    		$oX11->appendChild($oX15);

            //-- CFOP - Utilizar Tabela de CFOP.
            $oX16     = $oNovo->createElement("CFOP","X16");
    		$oX11->appendChild($oX16);

            //-- Informar o município de ocorrência do fato gerador do ICMS do transporte.
            //-- Utilizar a Tabela do IBGE (Anexo IV - Tabela de UF, Município e País)
            $oX17     = $oNovo->createElement("cMunFG","X17");
    		$oX11->appendChild($oX17);
            */
            
            if (!empty($mT007[T007_Transportadora_Placa_Veiculo]))
            {
                //-- TAG de grupo Veículo
                $oX18     = $oNovo->createElement("veicTransp");
        		$oX01->appendChild($oX18);

                //-- Placa do Veículo
                $oX19     = $oNovo->createElement("placa","$mT007[T007_Transportadora_Placa_Veiculo]");
        		$oX18->appendChild($oX19);

                //-- Sigla da UF
                $oX20     = $oNovo->createElement("UF","$mT007[T007_Estado_Veiculo]");
        		$oX18->appendChild($oX20);
                /*
                //-- Registro Nacional de Transportador de Carga (ANTT)
                $oX21     = $oNovo->createElement("RNTC","X21");
        		$oX18->appendChild($oX21);
                */
            }
            /*
            //-- TAG de grupo Reboque
            $oX22     = $oNovo->createElement("reboque");
    		$oX01->appendChild($oX22);

            //-- Placa do Veículo
            $oX23     = $oNovo->createElement("placa","X23");
    		$oX22->appendChild($oX23);

            //-- Sigla da UF
            $oX24     = $oNovo->createElement("UF","X24");
    		$oX22->appendChild($oX24);

            //-- Registro Nacional de Transportador de Carga (ANTT)
            $oX25     = $oNovo->createElement("RNTC","X25");
    		$oX22->appendChild($oX25);
            */
		
		}

		if (!empty($mT007[T007_Quantidade_Volumes]) ||
            !empty($mT007[T007_Especie]) ||
            !empty($mT007[T007_Marca]) ||
            !empty($mT007[T007_Numero_Caixas]) ||
            $mT007[T007_Peso_Bruto]>0 ||
            $mT007[T007_Peso_Liquido]>0)
		{

            //-- TAG de grupo Volumes
            $oX26     = $oNovo->createElement("vol");
    		$oX01->appendChild($oX26);

            $mT007[T007_Quantidade_Volumes]=$this->adicionarEspape($mT007[T007_Quantidade_Volumes]);
    		if ($mT007[T007_Quantidade_Volumes]>0)
    		{
                //-- Quantidade de volumes transportados
                $oX27     = $oNovo->createElement("qVol",$mT007[T007_Quantidade_Volumes]);
        		$oX26->appendChild($oX27);
            }

            $mT007[T007_Especie]=$this->adicionarEspape($mT007[T007_Especie]);
    		if (!empty($mT007[T007_Especie]))
    		{
                //-- Espécie dos volumes transportados
                $oX28     = $oNovo->createElement("esp",$mT007[T007_Especie]);
        		$oX26->appendChild($oX28);
            }

            $mT007[T007_Marca]=$this->adicionarEspape($mT007[T007_Marca]);
    		if (!empty($mT007[T007_Marca]))
    		{
                //-- Marca dos volumes transportados
                $oX29     = $oNovo->createElement("marca",$mT007[T007_Marca]);
        		$oX26->appendChild($oX29);
            }

            $mT007[T007_Numero_Caixas]=$this->adicionarEspape($mT007[T007_Numero_Caixas]);
    		if (!empty($mT007[T007_Numero_Caixas]))
    		{
                //-- Numeração dos volumes transportados
                $oX30     = $oNovo->createElement("nVol",$mT007[T007_Numero_Caixas]);
        		$oX26->appendChild($oX30);
            }

            $mT007[T007_Peso_Liquido]=$this->adicionarEspape($mT007[T007_Peso_Liquido]);
    		if ($mT007[T007_Peso_Liquido]>0)
    		{
                //-- Peso Líquido (em kg)
                $oX31     = $oNovo->createElement("pesoL",number_format($mT007[T007_Peso_Liquido],3,'.',''));
        		$oX26->appendChild($oX31);
            }

            $mT007[T007_Peso_Bruto]=$this->adicionarEspape($mT007[T007_Peso_Bruto]);
    		if ($mT007[T007_Peso_Bruto]>0)
    		{
        		//-- Peso Bruto (em kg)
                $oX32     = $oNovo->createElement("pesoB",number_format($mT007[T007_Peso_Bruto],3,'.',''));
        		$oX26->appendChild($oX32);
    		}
		}
		
		/*
		//-- TAG de grupo de Lacres
        $oX33     = $oNovo->createElement("lacres");
		$oX26->appendChild($oX33);

		//-- Número dos Lacres
        $oX34     = $oNovo->createElement("nLacre","X34");
		$oX33->appendChild($oX34);
		*/
		

		////////////////////////////////////////////////////////////
        //-- Y – Dados da Cobrança
		////////////////////////////////////////////////////////////
		
		$C020=mysql_query("select C020_Utiliza_Modulo_Financeiro 
		                     from T007
		                left join C004 on C004_Id=T007_C004_Id
		                left join C020 on C020_C004_Id=C004_Id    
		                    where T007_Id='".$this->getT007Id()."'");
		 
		$mC020=mysql_fetch_array($C020);
        $quantidadeTitulos = 0;
        if(($_SESSION[Modulo_NFe] == 'S' && $mC020['C020_Utiliza_Modulo_Financeiro'] == 'S') || $_SESSION[Modulo_NFe] == 'N' || empty($_SESSION[Modulo_NFe]) || empty($mC020['C020_Utiliza_Modulo_Financeiro'])){
             $cClausula  = " select ";
             $cClausula .= " T002_Numero_Duplicata, ";
             $cClausula .= " T002_Data_Vencimento, ";
             $cClausula .= " D027_Portador, ";
             $cClausula .= " D027_Flag_Tipo, ";
             $cClausula .= " T002_Valor_Duplicata, ";
             $cClausula .= " D027_Tipo_Forma_Pagamento_NFCE, ";
             $cClausula .= " D027_Codigo_Bandeira_NFCE, ";
             $cClausula .= " T002_Numero_Parcela as T118_Numero_Parcela, ";
             $cClausula .= " T002_Valor_Desconto ";
             $cClausula .= " from T002 ";
             $cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
             $cClausula .= " where T002_T007_Id='".$this->getT007Id()."' ";
             $cClausula .= " and T002_Flag_Cancelada!='S' ";
             $cClausula .= " and T002_Valor_Duplicata>0 ";
             $cClausula .= " and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) ";
             $cClausula .= " order by T002_Numero_Parcela ";
             $cSql_T002=sql_query($cClausula);
             $quantidadeTitulos = mysql_num_rows($cSql_T002);
        } 
        
        if ($quantidadeTitulos <= 0)
         {
            $cClausula  = " select ";
            $cClausula .= " if(T118_Numero_Duplicata!='',T118_Numero_Duplicata,T007_Numero_Nota_Fiscal) as T002_Numero_Duplicata, ";
            $cClausula .= " T118_Data_Vencimento as T002_Data_Vencimento, ";
            $cClausula .= " T118_Valor_Parcela as T002_Valor_Duplicata, ";
            $cClausula .= " D027_Portador, ";
            $cClausula .= " D027_Flag_Tipo, ";
            $cClausula .= " D027_Tipo_Forma_Pagamento_NFCE, ";
            $cClausula .= " D027_Codigo_Bandeira_NFCE, ";
            $cClausula .= " T118_Numero_Parcela ";
            //$cClausula .= " T002_Valor_Troco ";
            $cClausula .= " from T118 ";
            $cClausula .= " left join D027 on D027_Id=T118_D027_Id ";
            $cClausula .= " left join T007 on T007_Id=T118_T007_Id ";
            $cClausula .= " where T118_T007_Id='".$this->getT007Id()."' ";
            $cClausula .= " and T118_Valor_Parcela>0 ";
            $cClausula .= " order by T118_Numero_Parcela ";
            $cSql_T002=sql_query($cClausula);
        }
         
        if (sql_num_rows($cSql_T002)>0)
        {
            if($this->getModeloNF()=='55'){
                //-- TAG de grupo de Cobrança
                $oY01     = $oNovo->createElement("cobr");
        		$oA01->appendChild($oY01);
                
                $quantidadeTitulos = mysql_num_rows($cSql_T002);
                if($quantidadeTitulos >= 4){
                    $substr = 10;
                } else if($quantidadeTitulos == 3) {
                    $substr = 20;
                } else {
                    $substr = 30;
                }
                $charsPortador = 10;

                //-- TAG de grupo da Fatura
                $oY02     = $oNovo->createElement("fat");
                $oY01->appendChild($oY02);

                //-- Número da Fatura
                $oY03     = $oNovo->createElement("nFat","$mT007[T007_Numero_Nota_Fiscal]");
                $oY02->appendChild($oY03);

                //-- Valor Original da Fatura
                $oY04     = $oNovo->createElement("vOrig",number_format($mT007[T007_Valor_Total],2,'.',''));
                $oY02->appendChild($oY04);

                //-- Valor do desconto
                $oY05     = $oNovo->createElement("vDesc", '0.00');
                $oY02->appendChild($oY05);

                //-- Valor Líquido da Fatura
                $oY06     = $oNovo->createElement("vLiq",number_format($mT007[T007_Valor_Total],2,'.',''));
                $oY02->appendChild($oY06);
                //}
                while ($mT002=mysql_fetch_array($cSql_T002))
                {
                    //if($this->getModeloNF()=='55'){
                    //-- TAG de grupo da Duplicata
                    $oY07     = $oNovo->createElement("dup");
            		$oY01->appendChild($oY07);

                    //-- Número da Duplicata
                    if (empty($mT002[T002_Numero_Duplicata]))
                    {
                       //$mT002[T002_Numero_Duplicata]=STR_PAD($mT002[T002_Numero_Duplicata],4,'0',STR_PAD_LEFT);
                    }

                    $mT002[T002_Numero_Duplicata]=$this->adicionarEspape($mT002[T002_Numero_Duplicata]);
                    $mT002[D027_Portador]=substr($this->adicionarEspape($mT002[D027_Portador]),0,$charsPortador);

                    //Caso o tipo do portador seja "E-COMMERCE" será impresso apenas 1 duplicata com o nome do portador e o valor total da nota fiscal
                    if($mT002['D027_Flag_Tipo'] == '4'){
                        $numeroDuplicata = str_pad($mT002[T118_Numero_Parcela], 3, '0', STR_PAD_LEFT);
                        $oY08     = $oNovo->createElement("nDup","$numeroDuplicata");
                        //$oY08     = $oNovo->createElement("nDup","$mT002[D027_Portador]");
                        $oY07->appendChild($oY08);

                        //-- Data de vencimento - Formato “AAAA-MM-DD”
                        $oY09     = $oNovo->createElement("dVenc","$mT002[T002_Data_Vencimento]");
                        $oY07->appendChild($oY09);

                        //-- Valor da duplicata
                        $oY10     = $oNovo->createElement("vDup","$mT007[T007_Valor_Total]");
                        $oY07->appendChild($oY10);
                        break;
                    } else {
                        $numeroDuplicata = str_pad($mT002[T118_Numero_Parcela], 3, '0', STR_PAD_LEFT);
                        $oY08     = $oNovo->createElement("nDup","$numeroDuplicata");
                		$oY07->appendChild($oY08);

                        //-- Data de vencimento - Formato “AAAA-MM-DD”
                        $oY09     = $oNovo->createElement("dVenc","$mT002[T002_Data_Vencimento]");
                		$oY07->appendChild($oY09);

                        //-- Valor da duplicata
                        $oY10     = $oNovo->createElement("vDup","$mT002[T002_Valor_Duplicata]");
                		$oY07->appendChild($oY10);
                    }

                    /*} else if($this->getModeloNF()=='65'){

                            $oYA01 = $oNovo->createElement("pag");
                            $oA01->appendChild($oYA01);

                            $oYA01a = $oNovo->createElement("detPag");
                            $oYA01->appendChild($oYA01a);

                            $oYA02 = $oNovo->createElement("tPag", "01");
                            $oYA01a->appendChild($oYA02);

                            $oYA03 = $oNovo->createElement("vPag", "$mT002[T002_Valor_Duplicata]");
                            $oYA01a->appendChild($oYA03);

                            if(($mT002['D027_Tipo_Forma_Pagamento_NFCE']==03 || $mT002['D027_Tipo_Forma_Pagamento_NFCE']==04) && $mT002['D027_Codigo_Bandeira_NFCE'] !=''){

                                $oYA04 = $oNovo->createElement("card");
                                $oYA01a->appendChild($oYA04);

                                $YA04a = $oNovo->createElement("tpIntegra", '2');
                                $oYA04->appendChild($YA04a);

                                $oYA06 = $oNovo->createElement("tBand", $mT002['D027_Codigo_Bandeira_NFCE']);
                                $oYA04->appendChild($oYA06);

                        }*/
                        
                        /*$oYA09 = $oNovo->createElement("vTroco", 0.00);
                        $oYA01->appendChild($oYA09);*/
                    //}
                }
            }
        }


        ////////////////////////////////////////////////////////////
        //-- Y – Dados da Cobrança  NFe4.0
        ////////////////////////////////////////////////////////////
        
         $C020=mysql_query("select C020_Utiliza_Modulo_Financeiro 
                              from T007
                         left join C004 on C004_Id=T007_C004_Id
                         left join C020 on C020_C004_Id=C004_Id    
                             where T007_Id='".$this->getT007Id()."'");
         
         $mC020=mysql_fetch_array($C020);
         $quantidadeTitulos = 0;
         if(($_SESSION[Modulo_NFe] == 'S' && $mC020['C020_Utiliza_Modulo_Financeiro'] == 'S') || $_SESSION[Modulo_NFe] == 'N' || empty($_SESSION[Modulo_NFe]) || empty($mC020['C020_Utiliza_Modulo_Financeiro'])){
             $cClausula  = " select ";
             $cClausula .= " T002_Numero_Duplicata, ";
             $cClausula .= " T002_Data_Vencimento, ";
             $cClausula .= " D027_Portador, ";
             $cClausula .= " D027_Flag_Tipo, ";
             $cClausula .= " T002_Valor_Duplicata, ";
             $cClausula .= " D027_Tipo_Forma_Pagamento_NFCE, ";
             $cClausula .= " D027_Codigo_Bandeira_NFCE, ";
             $cClausula .= " T002_Numero_Parcela as T118_Numero_Parcela ";
             $cClausula .= " from T002 ";
             $cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
             $cClausula .= " where T002_T007_Id='".$this->getT007Id()."' ";
             $cClausula .= " and T002_Flag_Cancelada!='S' ";
             //$cClausula .= " and T002_Valor_Duplicata>0 ";
             $cClausula .= " and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) ";
             $cClausula .= " order by T002_Data_Vencimento ";
             $cSql_T002=sql_query($cClausula);
             $quantidadeTitulos = mysql_num_rows($cSql_T002);
        } 
        
         if ($quantidadeTitulos <= 0)
         {
             $cClausula  = " select ";
             $cClausula .= " if(T118_Numero_Duplicata!='',T118_Numero_Duplicata,T007_Numero_Nota_Fiscal) as T002_Numero_Duplicata, ";
             $cClausula .= " T118_Data_Vencimento as T002_Data_Vencimento, ";
             $cClausula .= " T118_Valor_Parcela as T002_Valor_Duplicata, ";
             $cClausula .= " D027_Portador, ";
             $cClausula .= " D027_Flag_Tipo, ";
             $cClausula .= " D027_Tipo_Forma_Pagamento_NFCE, ";
             $cClausula .= " D027_Codigo_Bandeira_NFCE, ";
             $cClausula .= " T118_Numero_Parcela ";
             //$cClausula .= " T002_Valor_Troco ";
             $cClausula .= " from T118 ";
             $cClausula .= " left join D027 on D027_Id=T118_D027_Id ";
             $cClausula .= " left join T007 on T007_Id=T118_T007_Id ";
             $cClausula .= " where T118_T007_Id='".$this->getT007Id()."' ";
             //$cClausula .= " and T118_Valor_Parcela>0 ";
             $cClausula .= " order by T118_Numero_Parcela ";
             $cSql_T002=sql_query($cClausula);
         }
         
        if (sql_num_rows($cSql_T002)>0)
        {
            //if($this->getModeloNF()=='55'){
                $oYA01 = $oNovo->createElement("pag");
                $oA01->appendChild($oYA01);
            
                while ($mT002=mysql_fetch_array($cSql_T002))
                {

                    $oYA01a = $oNovo->createElement("detPag");
                    $oYA01->appendChild($oYA01a);

                    if($mT002['D027_Tipo_Forma_Pagamento_NFCE'] == '90'){
                        $mT002[T002_Valor_Duplicata] = 0;                        
                    }

                    $oYA02 = $oNovo->createElement("tPag", "{$mT002['D027_Tipo_Forma_Pagamento_NFCE']}");
                    $oYA01a->appendChild($oYA02);

                    $oYA03 = $oNovo->createElement("vPag", "$mT002[T002_Valor_Duplicata]");
                    $oYA01a->appendChild($oYA03);

                    if(($mT002['D027_Tipo_Forma_Pagamento_NFCE']==03 || $mT002['D027_Tipo_Forma_Pagamento_NFCE']==04) && $mT002['D027_Codigo_Bandeira_NFCE'] !=''){

                        $oYA04 = $oNovo->createElement("card");
                        $oYA01a->appendChild($oYA04);

                        $YA04a = $oNovo->createElement("tpIntegra", '2');
                        $oYA04->appendChild($YA04a);

                        $oYA06 = $oNovo->createElement("tBand", $mT002['D027_Codigo_Bandeira_NFCE']);
                        $oYA04->appendChild($oYA06);

                    }

                    //$oYA09 = $oNovo->createElement("vTroco", 0.00);
                    //$oYA01->appendChild($oYA09);    
                }
            //}
        }
        
		////////////////////////////////////////////////////////////
        //-- Z - Informações Adicionais da NF-e
		////////////////////////////////////////////////////////////
        //-- TAG de grupo de Informações Adicionais
        $oZ01     = $oNovo->createElement("infAdic");
		$oA01->appendChild($oZ01);

        // Substituição de variavéis dentro das observações. Exemplo: R$ [[T007_Total_ICMS]]

		if (!empty($mT007[T007_Observacao_Nota_Fiscal_1]))
		{
            //-- Informações Adicionais de Interesse do Fisco
            $mT007['T007_Observacao_Nota_Fiscal_1'] = $this->substVariaveis($mT007['T007_Id'], $mT007['T007_Observacao_Nota_Fiscal_1']);
            $oZ02     = $oNovo->createElement("infAdFisco",$mT007[T007_Observacao_Nota_Fiscal_1]);
		    $oZ01->appendChild($oZ02);
        }
		
		if (!empty($mT007[T007_Observacao_Nota_Fiscal_2]))
		{
            $mT007['T007_Observacao_Nota_Fiscal_2'] = $this->substVariaveis($mT007['T007_Id'], $mT007['T007_Observacao_Nota_Fiscal_2']);
            //-- Informações Complementares de interesse do Contribuinte
            $oZ03     = $oNovo->createElement("infCpl",$mT007[T007_Observacao_Nota_Fiscal_2]);
    		$oZ01->appendChild($oZ03);
        }
        
        /*
        //-- TAG de grupo do campo de uso livre do contribuinte
        //-- Campo de uso livre do contribuinte, informar o nome do campo no atributo xCampo e o conteúdo do campo no xTexto
        $oZ04     = $oNovo->createElement("obsCont");
		$oZ01->appendChild($oZ04);
		
        //-- Identificação do campo
        $oZ05     = $oNovo->createElement("xCampo","Z05");
		$oZ04->appendChild($oZ05);
		
        //-- Conteúdo do campo
        $oZ06     = $oNovo->createElement("xTexto","Z06");
		$oZ04->appendChild($oZ06);
		
        //-- TAG de grupo do campo de uso livre do Fisco
        //-- Campo de uso livre do Fisco Informar o nome do campo no atributo xCampo e o conteúdo do campo no xTexto
        $oZ07     = $oNovo->createElement("obsFisco");
		$oZ01->appendChild($oZ07);
		
        //-- Identificação do campo
        $oZ08     = $oNovo->createElement("xCampo","Z08");
		$oZ07->appendChild($oZ08);
		
        //-- Conteúdo do campo
        $oZ09     = $oNovo->createElement("xTexto","Z09");
		$oZ07->appendChild($oZ08);

        //-- Tag de grupo do processo referenciado
        //-- Campo de uso livre do Fisco Informar o nome do campo no  atributo xCampo e o conteúdo do campo no xTexto
        $oZ10     = $oNovo->createElement("procRef");
		$oZ01->appendChild($oZ10);

        //-- Indentificador do processo ou ato concessório
        $oZ11     = $oNovo->createElement("nProc","Z11");
		$oZ10->appendChild($oZ11);
		
        //-- Indicador da origem do processo
        //-- Origem do processo, informar com:
        //-- 0 - SEFAZ;
        //-- 1 - Justiça Federal;
        //-- 2 - Justiça Estadual;
        //-- 3 - Secex/RFB;
        //-- 9 - Outros
        $oZ12     = $oNovo->createElement("indProc","Z12");
		$oZ10->appendChild($oZ12);

		*/

        if (!empty($mT007[T007_Exportacao_UF_Embarque]) and !empty($mT007[T007_Exportacao_Local_Embarque]))
        {
            //-- TAG de Grupo de Exportação
            //-- Informar apenas na exportação.
            $oZA01     = $oNovo->createElement("exporta");
    		$oA01->appendChild($oZA01);

            //-- Sigla da UF onde ocorrerá o Embarque dos produtos
            $oZA02     = $oNovo->createElement("UFSaidaPais","$mT007[T007_Exportacao_UF_Embarque]");
    		$oZA01->appendChild($oZA02);

            //-- Local onde ocorrerá o Embarque dos produtos
            $oZA03     = $oNovo->createElement("xLocExporta","$mT007[T007_Exportacao_Local_Embarque]");
    		$oZA01->appendChild($oZA03);
        }

        ////////////////////////////////////////////////////////////////////
        //-- MONTA A ESTRUTURA XML PARA A ASSINATURA
        ////////////////////////////////////////////////////////////////////
        if ($this->modelo_certificado=='A1')
        {
           	$oXS01 = $oNovo->createElement('Signature');
        	$oXS01->setAttribute("xmlns", "http://www.w3.org/2000/09/xmldsig#");
        	$oNFe->appendChild($oXS01);

        	$oXS02 = $oNovo->createElement('SignedInfo');
        	$oXS01->appendChild($oXS02);

        	$oXS03 = $oNovo->createElement('CanonicalizationMethod');
        	$oXS03->setAttribute("Algorithm","http://www.w3.org/TR/2001/REC-xml-c14n-20010315");
        	$oXS02->appendChild($oXS03);

        	$oXS05  = $oNovo->createElement('SignatureMethod');
        	$oXS05->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        	$oXS02->appendChild($oXS05);

        	$oXS07 = $oNovo->createElement('Reference');
        	$oXS07->setAttribute("URI", '#'.$this->getChaveAcessoNFe());
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
        }

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
        //die($conteudo);
        file_put_contents($this->getNomeArquivoXML(), $conteudo); 
    }
}

?>
