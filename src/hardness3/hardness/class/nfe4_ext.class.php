<?php
namespace hardness;
class classeNFeExt
{
    public function setModeloCertificado($x)
    {
        $this->modelo_certificado=$x;
    }
    public function setcIdToken($x)
    {
        $this->cIdToken=$x;
    }
    public function setT007Id($x)
    {
        $this->T007_Id=$x;
    }
    public function getT007Id()
    {
        return $this->T007_Id;
    }
    public function setC004Id($x)
    {
        $this->C004_Id=$x;
    }
    public function getC004Id()
    {
        return $this->C004_Id;
    }
    public function getErro()
    {
         return implode("<BR>",str_replace(array("'",'"'),"",$this->mErrorMsg));
    }
    public function getMotivo()
    {
         if (!empty($this->cStat))
         {
            $x=$this->cStat.' - ';
         }
         $x.=utf8_decode($this->xMotivo);

         return $x;
    }
    public function setModuloNFe($x)
    {
        $this->modulo_nfe=$x;
    }
    public function getModuloNFe()
    {
        return $this->modulo_nfe;
    }
    
    public function setVersaoLayoutXSD($cServico)
    {
    	/********************************************************************
    	* Versoes dos layouts xsd das funcoes do webservice
    	********************************************************************/

        //-- ARQUIVOS REFERENTE A VERSAO 1.10
        $mLayXSD['ConsultaCadastro']['1.10']="1.01";
        $mLayXSD['NfeRecepcao']['1.10']="1.10";
        $mLayXSD['NfeRetRecepcao']['1.10']="1.10";
        $mLayXSD['NfeCancelamento']['1.10']="1.07";
        $mLayXSD['NfeInutilizacao']['1.10']="1.07";
        $mLayXSD['NfeStatusServico']['1.10']="1.07";
        $mLayXSD['NfeConsultaNF']['1.10']="1.07";
        $mLayXSD['CabecMsg']['1.10']="1.02";

        //-- ARQUIVOS REFERENTE A VERSAO 2.00
        $mLayXSD['ConsultaCadastro']['2.00']="2.00";
        $mLayXSD['NfeRecepcao']['2.00']="2.00";
        $mLayXSD['NfeRetRecepcao']['2.00']="2.00";
        $mLayXSD['NfeCancelamento']['2.00']="2.00";
        $mLayXSD['NfeInutilizacao']['2.00']="2.00";
        $mLayXSD['NfeStatusServico']['2.00']="2.00";
        $mLayXSD['NfeConsultaNF']['2.00']="2.00";
        $mLayXSD['CabecMsg']['2.00']="1.02";
        $mLayXSD['NfeRecepcaoEvento']['2.00']="1.00";

        //-- ARQUIVOS REFERENTE A VERSAO 3.10
        $mLayXSD['NfeInutilizacao']['3.10']="3.10";
        $mLayXSD['NfeConsultaProtocolo']['3.10']="3.10";
        $mLayXSD['NfeStatusServico']['3.10']="3.10";
        $mLayXSD['NfeConsultaCadastro']['3.10']="3.10";
        $mLayXSD['RecepcaoEvento']['3.10']="1.00";
        $mLayXSD['NFeAutorizacao']['3.10']="3.10";
        $mLayXSD['NFeRetAutorizacao']['3.10']="3.10";
        $mLayXSD['CabecMsg']['3.10']="1.00";

        //-- ARQUIVOS REFERENTE A VERSAO 4.00
        $mLayXSD['NfeInutilizacao']['4.00']="4.00";
        $mLayXSD['NfeConsultaProtocolo']['4.00']="";
        $mLayXSD['NfeStatusServico']['4.00']="4.00";
        $mLayXSD['NfeConsultaCadastro']['4.00']="";
        $mLayXSD['RecepcaoEvento']['4.00']="1.00";
        $mLayXSD['NFeAutorizacao']['4.00']="4.00";
        $mLayXSD['NFeRetAutorizacao']['4.00']="4.00";
        $mLayXSD['CabecMsg']['4.00']="1.00";


        $cVersao=$this->getVersaoNFe();

        $cRetorno=$mLayXSD[$cServico][$cVersao];
        
        if (empty($cRetorno))
        {
           $this->mErrorMsg[] = "Não foi identificado o Layout XSD - Versao: $cVersao / Servico: $cServico";
           $this->errorStatus = true;
           return false;
        }
        else
        {
           return $cRetorno;
        }
    }

    public function setFunctionWDSL($cServico)
    {
        //-- ARQUIVOS REFERENTE A VERSAO 1.10
        $mFunctionWDSL['ConsultaCadastro']['1.10']="consultaCadastro";
        $mFunctionWDSL['NfeRecepcao']['1.10']="nfeRecepcaoLote";
        $mFunctionWDSL['NfeRetRecepcao']['1.10']="nfeRetRecepcao";
        $mFunctionWDSL['NfeCancelamento']['1.10']="nfeCancelamentoNF";
        $mFunctionWDSL['NfeInutilizacao']['1.10']="nfeInutilizacaoNF";
        $mFunctionWDSL['NfeStatusServico']['1.10']="nfeStatusServicoNF";
        $mFunctionWDSL['NfeConsultaNF']['1.10']="nfeConsultaNF";

        //-- ARQUIVOS REFERENTE A VERSAO 2.00
        $mFunctionWDSL['ConsultaCadastro']['2.00']="consultaCadastro2";
        $mFunctionWDSL['NfeRecepcao']['2.00']="nfeRecepcaoLote2";
        $mFunctionWDSL['NfeRetRecepcao']['2.00']="nfeRetRecepcao2";
        $mFunctionWDSL['NfeCancelamento']['2.00']="nfeCancelamentoNF2";
        $mFunctionWDSL['NfeInutilizacao']['2.00']="nfeInutilizacaoNF2";
        $mFunctionWDSL['NfeStatusServico']['2.00']="nfeStatusServicoNF2";
        $mFunctionWDSL['NfeConsultaNF']['2.00']="nfeConsultaNF2";
        $mFunctionWDSL['NfeRecepcaoEvento']['2.00']="nfeRecepcaoEvento";

        //-- ARQUIVOS REFERENTE A VERSAO 3.10
        $mFunctionWDSL['NfeInutilizacao']['3.10']="NfeInutilizacao3";
        $mFunctionWDSL['NfeConsultaProtocolo']['3.10']="NfeConsulta3";
        $mFunctionWDSL['NfeStatusServico']['3.10']="nfeStatusServicoNF";
        $mFunctionWDSL['NfeConsultaCadastro']['3.10']="CadConsultaCadastro2";
        $mFunctionWDSL['RecepcaoEvento']['3.10']="nfeRecepcaoEvento";
        $mFunctionWDSL['NFeAutorizacao']['3.10']="NFeAutorizacao3";
        $mFunctionWDSL['NFeRetAutorizacao']['3.10']="NFeRetAutorizacao3";

        //-- ARQUIVOS REFERENTE A VERSAO 4.00
        $mFunctionWDSL['NfeInutilizacao']['4.00']="NFeInutilizacao4";
        $mFunctionWDSL['NfeConsultaProtocolo']['4.00']="NFeConsultaProtocolo4";
        $mFunctionWDSL['NfeStatusServico']['4.00']="NFeStatusServico4";
        $mFunctionWDSL['NfeConsultaCadastro']['4.00']="CadConsultaCadastro4";
        $mFunctionWDSL['RecepcaoEvento']['4.00']="NFeRecepcaoEvento4";
        $mFunctionWDSL['NFeAutorizacao']['4.00']="NFeAutorizacao4";
        $mFunctionWDSL['NFeRetAutorizacao']['4.00']="NFeRetAutorizacao4";


        $cVersao=$this->getVersaoNFe();

        $cRetorno=$mFunctionWDSL[$cServico][$cVersao];
        
        if (empty($cRetorno))
        {
           $this->mErrorMsg[] = "Não foi identificado a Function WDSL - Versao: $cVersao / Servico: $cServico";
           $this->errorStatus = true;
           return false;
        }
        else
        {
           return $cRetorno;
        }

    }

    public function setVersaoArqXSD($cServico)
    {
        //-- ARQUIVOS REFERENTE A VERSAO 1.10
        $mArqXSD['ConsultaCadastro']['1.10']="consCad_v1.01.xsd";
        $mArqXSD['NfeRecepcao']['1.10']="envNFe_v1.10.xsd";
        $mArqXSD['NfeRetRecepcao']['1.10']="retEnviNFe_v1.10.xsd";
        $mArqXSD['NfeCancelamento']['1.10']="cancNFe_v1.07.xsd";
        $mArqXSD['NfeInutilizacao']['1.10']="inutNFe_v1.07.xsd";
        $mArqXSD['NfeStatusServico']['1.10']="consStatServ_v1.07.xsd";
        $mArqXSD['NfeConsultaNF']['1.10']="consSitNFe_v1.07.xsd";
        $mArqXSD['CabecMsg']['1.10']="cabecMsg_v1.02.xsd";

        //-- ARQUIVOS REFERENTE A VERSAO 2.00
        $mArqXSD['ConsultaCadastro']['2.00']="consCad_v2.00.xsd";
        $mArqXSD['NfeRecepcao']['2.00']="enviNFe_v2.00.xsd";
        $mArqXSD['NfeRetRecepcao']['2.00']="retEnviNFe_v2.00.xsd";
        $mArqXSD['NfeCancelamento']['2.00']="cancNFe_v2.00.xsd";
        $mArqXSD['NfeInutilizacao']['2.00']="inutNFe_v2.00.xsd";
        $mArqXSD['NfeStatusServico']['2.00']="consStatServ_v2.00.xsd";
        $mArqXSD['NfeConsultaNF']['2.00']="consSitNFe_v2.00.xsd";
        $mArqXSD['CabecMsg']['2.00']="cabecMsg_v1.02.xsd";

        //-- ARQUIVOS REFERENTE A VERSAO 3.10
        $mArqXSD['NfeInutilizacao']['3.10']="inutNFe_v3.10.xsd";
        $mArqXSD['NfeConsultaProtocolo']['3.10']="NfeConsulta3";
        $mArqXSD['NfeStatusServico']['3.10']="consStatServ_v3.10.xsd";
        $mArqXSD['NfeConsultaCadastro']['3.10']="CadConsultaCadastro2";
        $mArqXSD['RecepcaoEvento']['3.10']="RecepcaoEvento";
        $mArqXSD['NFeAutorizacao']['3.10']="consSitNFe_v3.10.xsd";
        $mArqXSD['NFeRetAutorizacao']['3.10']="consReciNFe_v3.10.xsd";
        $mArqXSD['CabecMsg']['3.10']="cabecMsg_v1.02.xsd";

        //-- ARQUIVOS REFERENTE A VERSAO 4.00
        $mArqXSD['NfeInutilizacao']['4.00']="inutNFe_v4.00.xsd";
        $mArqXSD['NfeConsultaProtocolo']['4.00']="NfeConsulta4";
        $mArqXSD['NfeStatusServico']['4.00']="consStatServ_v4.00.xsd";
        $mArqXSD['NfeConsultaCadastro']['4.00']="cadconsultacadastro4";
        $mArqXSD['RecepcaoEvento']['4.00']="RecepcaoEvento";
        $mArqXSD['NFeAutorizacao']['4.00']="consSitNFe_v4.00.xsd";
        $mArqXSD['NFeRetAutorizacao']['4.00']="consReciNFe_v4.00.xsd";
        $mArqXSD['CabecMsg']['4.00']="cabecMsg_v1.02.xsd";

        $cVersao=$this->getVersaoNFe();

        $cRetorno=$mArqXSD[$cServico][$cVersao];
        
        if (empty($cRetorno))
        {
           $this->mErrorMsg[] = "Não foi identificado o Arquivo XSD - Versao: $cVersao / Servico: $cServico";
           $this->errorStatus = true;
           return false;
        }
        else
        {
           return $cRetorno;
        }

    }

    public function setWebService($cMetodo='')
    {
        /*********************************************************
        * TODOS OS WEBSERVICES EXISTENTES ESTAO AQUI
        * ADICIONE MAIS CONFORME AS VERSOES FOREM FICANDO DISPONIVEIS
        **********************************************************/

        //-- PRODUCAO
        $mListaWS['AM']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefaz.am.gov.br/ws/services/NfeRecepcao";
        $mListaWS['AM']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefaz.am.gov.br/ws/services/NfeRetRecepcao";
        $mListaWS['AM']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefaz.am.gov.br/ws/services/NfeCancelamento";
        $mListaWS['AM']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefaz.am.gov.br/ws/services/NfeInutilizacao";
        $mListaWS['AM']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefaz.am.gov.br/ws/services/NfeConsulta";
        $mListaWS['AM']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefaz.am.gov.br/ws/services/NfeStatusServico";
        $mListaWS['AM']['NfeRecepcao']['2.00']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeRecepcao2";
        $mListaWS['AM']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeRetRecepcao2";
        $mListaWS['AM']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeCancelamento2";
        $mListaWS['AM']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeInutilizacao2";
        $mListaWS['AM']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeConsulta2";
        $mListaWS['AM']['NfeStatusServico']['2.00']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeStatusServico2";
        $mListaWS['AM']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/RecepcaoEvento";
        $mListaWS['AM']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/RecepcaoEvento";
        $mListaWS['AM']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeInutilizacao2";
        $mListaWS['AM']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeConsulta2";
        $mListaWS['AM']['NfeStatusServico']['3.10']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeStatusServico2";
        $mListaWS['AM']['NfeConsultaCadastro']['3.10']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/cadconsultacadastro2";
        $mListaWS['AM']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeAutorizacao";
        $mListaWS['AM']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.am.gov.br/services2/services/NfeRetAutorizacao";

        //-- HOMOLOGAÇÃO
        $mListaWS['AM']['NfeRecepcao']['1.10']['2']['55']="https://homnfe.sefaz.am.gov.br/ws/services/NfeRecepcao";
        $mListaWS['AM']['NfeRetRecepcao']['1.10']['2']['55']="https://homnfe.sefaz.am.gov.br/ws/services/NfeRetRecepcao";
        $mListaWS['AM']['NfeCancelamento']['1.10']['2']['55']="https://homnfe.sefaz.am.gov.br/ws/services/NfeCancelamento";
        $mListaWS['AM']['NfeInutilizacao']['1.10']['2']['55']="https://homnfe.sefaz.am.gov.br/ws/services/NfeInutilizacao";
        $mListaWS['AM']['NfeConsultaNF']['1.10']['2']['55']="https://homnfe.sefaz.am.gov.br/ws/services/NfeConsulta";
        $mListaWS['AM']['NfeStatusServico']['1.10']['2']['55']="https://homnfe.sefaz.am.gov.br/ws/services/NfeStatusServico";
        $mListaWS['AM']['NfeRecepcao']['2.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeRecepcao2";
        $mListaWS['AM']['NfeRetRecepcao']['2.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeRetRecepcao2";
        $mListaWS['AM']['NfeCancelamento']['2.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeCancelamento2";
        $mListaWS['AM']['NfeInutilizacao']['2.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeInutilizacao2";
        $mListaWS['AM']['NfeConsultaNF']['2.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeConsulta2";
        $mListaWS['AM']['NfeStatusServico']['2.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeStatusServico2";
        $mListaWS['AM']['ConsultaCadastro']['2.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/cadconsultacadastro2";
        $mListaWS['AM']['NfeRecepcaoEvento']['2.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/RecepcaoEvento";
        $mListaWS['AM']['RecepcaoEvento']['3.10']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/RecepcaoEvento";
        $mListaWS['AM']['NfeInutilizacao']['3.10']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeInutilizacao2";
        $mListaWS['AM']['NfeConsultaProtocolo']['3.10']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeConsulta2";
        $mListaWS['AM']['NfeStatusServico']['3.10']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeStatusServico2";
        $mListaWS['AM']['NfeConsultaCadastro']['3.10']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/cadconsultacadastro2";
        $mListaWS['AM']['NFeAutorizacao']['3.10']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeAutorizacao";
        $mListaWS['AM']['NFeRetAutorizacao']['3.10']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeRetAutorizacao";
        $mListaWS['AM']['NfeInutilizacao']['4.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeInutilizacao4";
        $mListaWS['AM']['NfeConsultaProtocolo']['4.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeConsulta4";
        $mListaWS['AM']['NfeStatusServico']['4.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeStatusServico4";
        $mListaWS['AM']['RecepcaoEvento']['4.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/RecepcaoEvento4";
        $mListaWS['AM']['NFeAutorizacao']['4.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeAutorizacao4";
        $mListaWS['AM']['NFeRetAutorizacao']['4.00']['2']['55']="https://homnfe.sefaz.am.gov.br/services2/services/NfeRetAutorizacao4";

        /***** Raquel 11/09/2018 *******/
        // AMAZONAS NFC-E
        //-- PRODUCAO
        $mListaWS['AM']['NFeAutorizacao']['4.00']['1']['65']="https://nfce.sefaz.am.gov.br/nfce-services/services/NfeAutorizacao4";
        $mListaWS['AM']['NFeRetAutorizacao']['4.00']['1']['65']="https://nfce.sefaz.am.gov.br/nfce-services/services/NfeRetAutorizacao4";
        $mListaWS['AM']['NfeInutilizacao']['4.00']['1']['65']="https://nfce.sefaz.am.gov.br/nfce-services/services/NfeInutilizacao4";
        $mListaWS['AM']['NfeConsultaProtocolo']['4.00']['1']['65']="https://nfce.sefaz.am.gov.br/nfce-services/services/NfeConsulta4";
        $mListaWS['AM']['NfeStatusServico']['4.00']['1']['65']="https://nfce.sefaz.am.gov.br/nfce-services/services/NfeStatusServico4";
        $mListaWS['AM']['RecepcaoEvento']['4.00']['1']['65']="https://nfce.sefaz.am.gov.br/nfce-services/services/RecepcaoEvento4";

        //-- HOMOLOGAÇÃO
        $mListaWS['AM']['NFeAutorizacao']['4.00']['2']['65']="https://homnfce.sefaz.am.gov.br/nfce-services/services/NfeAutorizacao4";
        $mListaWS['AM']['NFeRetAutorizacao']['4.00']['2']['65']="https://homnfce.sefaz.am.gov.br/nfce-services/services/NfeRetAutorizacao4";
        $mListaWS['AM']['NfeInutilizacao']['4.00']['2']['65']="https://homnfce.sefaz.am.gov.br/nfce-services/services/NfeInutilizacao4";
        $mListaWS['AM']['NfeConsultaProtocolo']['4.00']['2']['65']="https://homnfce.sefaz.am.gov.br/nfce-services/services/NfeConsulta4";
        $mListaWS['AM']['NfeStatusServico']['4.00']['2']['65']="https://homnfce.sefaz.am.gov.br/nfce-services/services/NfeStatusServico4";
        $mListaWS['AM']['RecepcaoEvento']['4.00']['2']['65']="https://homnfce.sefaz.am.gov.br/nfce-services/services/RecepcaoEvento4";

        //-- PRODUCAO
        $mListaWS['BA']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfe/NfeRecepcao.asmx";
        $mListaWS['BA']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfe/NfeRetRecepcao.asmx";
        $mListaWS['BA']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfe/NfeCancelamento.asmx";
        $mListaWS['BA']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfe/NfeInutilizacao.asmx";
        $mListaWS['BA']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfe/NfeConsulta.asmx";
        $mListaWS['BA']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfe/NfeStatusServico.asmx";
        $mListaWS['BA']['ConsultaCadastro']['1.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfe/NfeConsulta.asmx";
        $mListaWS['BA']['NfeRecepcao']['2.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeRecepcao2.asmx";
        $mListaWS['BA']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeRetRecepcao2.asmx";
        $mListaWS['BA']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeCancelamento2.asmx";
        $mListaWS['BA']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeInutilizacao2.asmx";
        $mListaWS['BA']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeConsulta2.asmx";
        $mListaWS['BA']['NfeStatusServico']['2.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeStatusServico2.asmx";
        $mListaWS['BA']['ConsultaCadastro']['2.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfenw/CadConsultaCadastro2.asmx";
        $mListaWS['BA']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/sre/RecepcaoEvento.asmx";
        $mListaWS['BA']['NfeConsultaCadastro']['3.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/nfenw/CadConsultaCadastro2.asmx";
        $mListaWS['BA']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/sre/recepcaoevento.asmx";
        $mListaWS['BA']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NfeInutilizacao/NfeInutilizacao.asmx";
        $mListaWS['BA']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NfeConsulta/NfeConsulta.asmx";
        $mListaWS['BA']['NfeStatusServico']['3.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NfeStatusServico/NfeStatusServico.asmx";
        $mListaWS['BA']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NfeAutorizacao/NfeAutorizacao.asmx";
        $mListaWS['BA']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NfeRetAutorizacao/NfeRetAutorizacao.asmx";
        $mListaWS['BA']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NFeInutilizacao4/NFeInutilizacao4.asmx";
        $mListaWS['BA']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NFeConsultaProtocolo4/NFeConsultaProtocolo4.asmx";
        $mListaWS['BA']['NfeStatusServico']['4.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NFeStatusServico4/NFeStatusServico4.asmx";
        $mListaWS['BA']['NfeConsultaCadastro']['4.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/CadConsultaCadastro4/CadConsultaCadastro4.asmx";
        $mListaWS['BA']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NFeRecepcaoEvento4/NFeRecepcaoEvento4.asmx";
        $mListaWS['BA']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NFeAutorizacao4/NFeAutorizacao4.asmx";
        $mListaWS['BA']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.sefaz.ba.gov.br/webservices/NFeRetAutorizacao4/NFeRetAutorizacao4.asmx";
        
        //-- HOMOLOGAÇÃO
        $mListaWS['BA']['NfeRecepcao']['1.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfe/NfeRecepcao.asmx";
        $mListaWS['BA']['NfeRetRecepcao']['1.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfe/NfeRetRecepcao.asmx";
        $mListaWS['BA']['NfeCancelamento']['1.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfe/NfeCancelamento.asmx";
        $mListaWS['BA']['NfeInutilizacao']['1.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfe/NfeInutilizacao.asmx";
        $mListaWS['BA']['NfeConsultaNF']['1.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfe/NfeConsulta.asmx";
        $mListaWS['BA']['NfeStatusServico']['1.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfe/NfeStatusServico.asmx";
        $mListaWS['BA']['NfeRecepcao']['2.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfenw/NfeRecepcao2.asmx";
        $mListaWS['BA']['NfeRetRecepcao']['2.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfenw/NfeRetRecepcao2.asmx";
        $mListaWS['BA']['NfeCancelamento']['2.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfenw/NfeCancelamento2.asmx";
        $mListaWS['BA']['NfeInutilizacao']['2.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfenw/NfeInutilizacao2.asmx";
        $mListaWS['BA']['NfeConsultaNF']['2.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfenw/NfeConsulta2.asmx";
        $mListaWS['BA']['NfeStatusServico']['2.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfenw/NfeStatusServico2.asmx";
        $mListaWS['BA']['ConsultaCadastro']['2.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfenw/CadConsultaCadastro2.asmx";
        $mListaWS['BA']['NfeRecepcaoEvento']['2.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/sre/RecepcaoEvento.asmx";
        $mListaWS['BA']['NfeConsultaCadastro']['3.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/nfenw/CadConsultaCadastro2.asmx";
        $mListaWS['BA']['RecepcaoEvento']['3.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/sre/recepcaoevento.asmx";
        $mListaWS['BA']['NfeInutilizacao']['3.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NfeInutilizacao/NfeInutilizacao.asmx";
        $mListaWS['BA']['NfeConsultaProtocolo']['3.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NfeConsulta/NfeConsulta.asmx";
        $mListaWS['BA']['NfeStatusServico']['3.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NfeStatusServico/NfeStatusServico.asmx";
        $mListaWS['BA']['NFeAutorizacao']['3.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NfeAutorizacao/NfeAutorizacao.asmx";
        $mListaWS['BA']['NFeRetAutorizacao']['3.10']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NfeRetAutorizacao/NfeRetAutorizacao.asmx";
        $mListaWS['BA']['NfeInutilizacao']['4.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NFeInutilizacao4/NFeInutilizacao4.asmx";
        $mListaWS['BA']['NfeConsultaProtocolo']['4.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NFeConsultaProtocolo4/NFeConsultaProtocolo4.asmx";
        $mListaWS['BA']['NfeStatusServico']['4.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NFeStatusServico4/NFeStatusServico4.asmx";
        $mListaWS['BA']['NfeConsultaCadastro']['4.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/CadConsultaCadastro4/CadConsultaCadastro4.asmx";
        $mListaWS['BA']['RecepcaoEvento']['4.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NFeRecepcaoEvento4/NFeRecepcaoEvento4.asmx";
        $mListaWS['BA']['NFeAutorizacao']['4.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NFeAutorizacao4/NFeAutorizacao4.asmx";
        $mListaWS['BA']['NFeRetAutorizacao']['4.00']['2']['55']="https://hnfe.sefaz.ba.gov.br/webservices/NFeRetAutorizacao4/NFeRetAutorizacao4.asmx";

        //-- PRODUCAO
        $mListaWS['CE']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe/services/NfeRecepcao";
        $mListaWS['CE']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe/services/NfeRetRecepcao";
        $mListaWS['CE']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe/services/NfeCancelamento";
        $mListaWS['CE']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe/services/NfeInutilizacao";
        $mListaWS['CE']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe/services/NfeConsulta";
        $mListaWS['CE']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe/services/NfeStatusServico";
        $mListaWS['CE']['ConsultaCadastro']['1.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe/services/CadConsultaCadastro";
        $mListaWS['CE']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/RecepcaoEvento";
        $mListaWS['CE']['NfeRecepcao']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeRecepcao2";
        $mListaWS['CE']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeRetRecepcao2";
        $mListaWS['CE']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeCancelamento2";
        $mListaWS['CE']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeInutilizacao2";
        $mListaWS['CE']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeConsulta2";
        $mListaWS['CE']['NfeStatusServico']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeStatusServico2";
        $mListaWS['CE']['ConsultaCadastro']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/CadConsultaCadastro2";
        $mListaWS['CE']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/RecepcaoEvento";
        $mListaWS['CE']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeInutilizacao2?wsdl";
        $mListaWS['CE']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeConsulta2?wsdl";
        $mListaWS['CE']['NfeStatusServico']['3.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeStatusServico2?wsdl";
        $mListaWS['CE']['NfeConsultaCadastro']['3.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/CadConsultaCadastro2?wsdl";
        $mListaWS['CE']['NfeDownloadNF']['3.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeDownloadNF?wsdl";
        $mListaWS['CE']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeAutorizacao?wsdl";
        $mListaWS['CE']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/NfeRetAutorizacao?wsdl";
        $mListaWS['CE']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.sefaz.ce.gov.br/nfe2/services/RecepcaoEvento?wsdl";

        //-- HOMOLOGAÇÃO
        $mListaWS['CE']['NfeRecepcao']['1.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe/services/NfeRecepcao";
        $mListaWS['CE']['NfeRetRecepcao']['1.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe/services/NfeRetRecepcao";
        $mListaWS['CE']['NfeCancelamento']['1.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe/services/NfeCancelamento";
        $mListaWS['CE']['NfeInutilizacao']['1.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe/services/NfeInutilizacao";
        $mListaWS['CE']['NfeConsultaNF']['1.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe/services/NfeConsulta";
        $mListaWS['CE']['NfeStatusServico']['1.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe/services/NfeStatusServico";
        $mListaWS['CE']['ConsultaCadastro']['1.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe/services/CadConsultaCadastro";
        $mListaWS['CE']['NfeRecepcao']['2.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeRecepcao2";
        $mListaWS['CE']['NfeRetRecepcao']['2.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeRetRecepcao2";
        $mListaWS['CE']['NfeCancelamento']['2.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeCancelamento2";
        $mListaWS['CE']['NfeInutilizacao']['2.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeInutilizacao2";
        $mListaWS['CE']['NfeConsultaNF']['2.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeConsulta2";
        $mListaWS['CE']['NfeStatusServico']['2.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeStatusServico2";
        $mListaWS['CE']['ConsultaCadastro']['2.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeCadConsultaCadastro2";
        $mListaWS['CE']['NfeRecepcaoEvento']['2.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/RecepcaoEvento";
        $mListaWS['CE']['NfeInutilizacao']['3.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeInutilizacao2?wsdl";
        $mListaWS['CE']['NfeConsultaProtocolo']['3.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeConsulta2?wsdl";
        $mListaWS['CE']['NfeStatusServico']['3.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeStatusServico2?wsdl";
        $mListaWS['CE']['NfeConsultaCadastro']['3.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/CadConsultaCadastro2?wsdl";
        $mListaWS['CE']['NfeDownloadNF']['3.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeDownloadNF?wsdl";
        $mListaWS['CE']['NFeAutorizacao']['3.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeAutorizacao?wsdl";
        $mListaWS['CE']['NFeRetAutorizacao']['3.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/NfeRetAutorizacao?wsdl";
        $mListaWS['CE']['RecepcaoEvento']['3.10']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe2/services/RecepcaoEvento?wsdl";
        $mListaWS['CE']['NfeInutilizacao']['4.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe4/services/NFeInutilizacao4?WSDL";
        $mListaWS['CE']['NfeConsultaProtocolo']['4.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe4/services/NFeConsultaProtocolo4?WSDL";
        $mListaWS['CE']['NfeStatusServico']['4.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe4/services/NFeStatusServico4?WSDL";
        $mListaWS['CE']['NFeAutorizacao']['4.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe4/services/NFeAutorizacao4?WSDL";
        $mListaWS['CE']['NFeRetAutorizacao']['4.00']['2']['55']="https://nfeh.sefaz.ce.gov.br/nfe4/services/NFeRetAutorizacao4?WSDL";

        //-- PRODUCAO
        //-- versão 4.00 não foi adicionado
        $mListaWS['DF']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nferecepcao/NfeRecepcao.asmx";
        $mListaWS['DF']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nferetrecepcao/NfeRetRecepcao.asmx";
        $mListaWS['DF']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfecancelamento/NfeCancelamento.asmx";
        $mListaWS['DF']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfeinutilizacao/NfeInutilizacao.asmx";
        $mListaWS['DF']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfeconsulta/NfeConsulta.asmx";
        $mListaWS['DF']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfestatusservico/NfeStatusServico.asmx";
        $mListaWS['DF']['NfeRecepcao']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/Nferecepcao/NFeRecepcao2.asmx";
        $mListaWS['DF']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeRetRecepcao/NfeRetRecepcao2.asmx";
        $mListaWS['DF']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeCancelamento/NfeCancelamento2.asmx";
        $mListaWS['DF']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['DF']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx";
        $mListaWS['DF']['NfeStatusServico']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['DF']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";

        //-- PRODUCAO
        $mListaWS['DPEC']['RecepcaoDPEC']['1.10']['1']['55']="https://www.nfe.fazenda.gov.br/SCERecepcaoRFB/SCERecepcaoRFB.asmx";
        $mListaWS['DPEC']['ConsultaDPEC']['1.10']['1']['55']="https://www.nfe.fazenda.gov.br/SCEConsultaRFB/SCEConsultaRFB.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['DPEC']['RecepcaoDPEC']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/SCERecepcaoRFB/SCERecepcaoRFB.asmx";
        $mListaWS['DPEC']['ConsultaDPEC']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/SCEConsultaRFB/SCEConsultaRFB.asmx";

        //-- PRODUCAO
        $mListaWS['GO']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NfeRecepcao";
        $mListaWS['GO']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NfeRetRecepcao";
        $mListaWS['GO']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NfeCancelamento";
        $mListaWS['GO']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NfeInutilizacao";
        $mListaWS['GO']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NfeConsulta";
        $mListaWS['GO']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NfeStatusServico";
        $mListaWS['GO']['ConsultaCadastro']['1.10']['1']['55']="http://nfe.sefaz.go.gov.br/nfe/services/CadConsultaCadastro";
        $mListaWS['GO']['NfeRecepcao']['2.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeRecepcao2?wsdl";
        $mListaWS['GO']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeRetRecepcao2?wsdl";
        $mListaWS['GO']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeCancelamento2?wsdl";
        $mListaWS['GO']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeInutilizacao2?wsdl";
        $mListaWS['GO']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeConsulta2?wsdl";
        $mListaWS['GO']['NfeStatusServico']['2.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeStatusServico2?wsdl";
        $mListaWS['GO']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeRecepcaoEvento?wsdl";
        $mListaWS['GO']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/RecepcaoEvento?wsdl";
        $mListaWS['GO']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeInutilizacao2?wsdl";
        $mListaWS['GO']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeConsulta2?wsdl";
        $mListaWS['GO']['NfeStatusServico']['3.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeStatusServico2?wsdl";
        $mListaWS['GO']['NfeConsultaCadastro']['3.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/CadConsultaCadastro2?wsdl";
        $mListaWS['GO']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeAutorizacao?wsdl";
        $mListaWS['GO']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeRetAutorizacao?wsdl";
        $mListaWS['GO']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NFeInutilizacao4?wsdl";
        $mListaWS['GO']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NFeConsultaProtocolo4?wsdl";
        $mListaWS['GO']['NfeStatusServico']['4.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NFeStatusServico4?wsdl";
        $mListaWS['GO']['NfeConsultaCadastro']['4.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/CadConsultaCadastro4?wsdl";
        $mListaWS['GO']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NFeRecepcaoEvento4?wsdl";
        $mListaWS['GO']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NFeAutorizacao4?wsdl";
        $mListaWS['GO']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.sefaz.go.gov.br/nfe/services/NFeRetAutorizacao4?wsdl";

        //-- HOMOLOGAÇÃO
        $mListaWS['GO']['NfeRecepcao']['1.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NfeRecepcao";
        $mListaWS['GO']['NfeRetRecepcao']['1.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NfeRetRecepcao";
        $mListaWS['GO']['NfeCancelamento']['1.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NfeCancelamento";
        $mListaWS['GO']['NfeInutilizacao']['1.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NfeInutilizacao";
        $mListaWS['GO']['NfeConsultaNF']['1.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NfeConsulta";
        $mListaWS['GO']['NfeStatusServico']['1.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NfeStatusServico";
        $mListaWS['GO']['ConsultaCadastro']['1.10']['2']['55']="http://homolog.sefaz.go.gov.br/nfe/services/CadConsultaCadastro";
        $mListaWS['GO']['NfeRecepcao']['2.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeRecepcao2?wsdl";
        $mListaWS['GO']['NfeRetRecepcao']['2.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeRetRecepcao2?wsdl";
        $mListaWS['GO']['NfeCancelamento']['2.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeCancelamento2?wsdl";
        $mListaWS['GO']['NfeInutilizacao']['2.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeInutilizacao2?wsdl";
        $mListaWS['GO']['NfeConsultaNF']['2.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeConsulta2?wsdl";
        $mListaWS['GO']['NfeStatusServico']['2.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeStatusServico2?wsdl";
        $mListaWS['GO']['NfeRecepcaoEvento']['2.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeRecepcaoEvento?wsdl";
        $mListaWS['GO']['RecepcaoEvento']['3.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/RecepcaoEvento?wsdl";
        $mListaWS['GO']['NfeInutilizacao']['3.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeInutilizacao2?wsdl";
        $mListaWS['GO']['NfeConsultaProtocolo']['3.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeConsulta2?wsdl";
        $mListaWS['GO']['NfeStatusServico']['3.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeStatusServico2?wsdl";
        $mListaWS['GO']['NfeConsultaCadastro']['3.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/CadConsultaCadastro2?wsdl";
        $mListaWS['GO']['NFeAutorizacao']['3.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeAutorizacao?wsdl";
        $mListaWS['GO']['NFeRetAutorizacao']['3.10']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/v2/NfeRetAutorizacao?wsdl";
        $mListaWS['GO']['NfeInutilizacao']['4.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NFeInutilizacao4?wsdl";
        $mListaWS['GO']['NfeConsultaProtocolo']['4.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NFeConsultaProtocolo4?wsdl";
        $mListaWS['GO']['NfeStatusServico']['4.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NFeStatusServico4?wsdl";
        $mListaWS['GO']['NfeConsultaCadastro']['4.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/CadConsultaCadastro4?wsdl";
        $mListaWS['GO']['RecepcaoEvento']['4.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NFeRecepcaoEvento4?wsdl";
        $mListaWS['GO']['NFeAutorizacao']['4.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NFeAutorizacao4?wsdl";
        $mListaWS['GO']['NFeRetAutorizacao']['4.00']['2']['55']="https://homolog.sefaz.go.gov.br/nfe/services/NFeRetAutorizacao4?wsdl";

        /***** Raquel 11/09/2018 *****/
        // GOIAS NFC-E
        //-- PRODUCAO
        $mListaWS['GO']['NFeAutorizacao']['4.00']['1']['65']="https://nfe.sefaz.go.gov.br/nfe/services/NFeAutorizacao4?wsdl";
        $mListaWS['GO']['NFeRetAutorizacao']['4.00']['1']['65']="https://nfe.sefaz.go.gov.br/nfe/services/NFeRetAutorizacao4?wsdl";
        $mListaWS['GO']['NfeInutilizacao']['4.00']['1']['65']="https://nfe.sefaz.go.gov.br/nfe/services/NFeInutilizacao4?wsdl";
        $mListaWS['GO']['NfeConsultaProtocolo']['4.00']['1']['65']="https://nfe.sefaz.go.gov.br/nfe/services/NFeConsultaProtocolo4?wsdl";
        $mListaWS['GO']['NfeStatusServico']['4.00']['1']['65']="https://nfe.sefaz.go.gov.br/nfe/services/NFeStatusServico4?wsdl";
        $mListaWS['GO']['NfeConsultaCadastro']['4.00']['1']['65']="https://nfe.sefaz.go.gov.br/nfe/services/CadConsultaCadastro4?wsdl";
        $mListaWS['GO']['RecepcaoEvento']['4.00']['1']['65']="https://nfe.sefaz.go.gov.br/nfe/services/NFeRecepcaoEvento4?wsdl";

        //-- HOMOLOGAÇÃO
        $mListaWS['GO']['NFeAutorizacao']['4.00']['2']['65']="https://homolog.sefaz.go.gov.br/nfe/services/NFeAutorizacao4?wsdl";
        $mListaWS['GO']['NFeRetAutorizacao']['4.00']['2']['65']="https://homolog.sefaz.go.gov.br/nfe/services/NFeRetAutorizacao4?wsdl";
        $mListaWS['GO']['NfeInutilizacao']['4.00']['2']['65']="https://homolog.sefaz.go.gov.br/nfe/services/NFeInutilizacao4?wsdl";
        $mListaWS['GO']['NfeConsultaProtocolo']['4.00']['2']['65']="https://homolog.sefaz.go.gov.br/nfe/services/NFeConsultaProtocolo4?wsdl";
        $mListaWS['GO']['NfeStatusServico']['4.00']['2']['65']="https://homolog.sefaz.go.gov.br/nfe/services/NFeStatusServico4?wsdl";
        $mListaWS['GO']['NfeConsultaCadastro']['4.00']['2']['65']="https://homolog.sefaz.go.gov.br/nfe/services/CadConsultaCadastro4?wsdl";
        $mListaWS['GO']['RecepcaoEvento']['4.00']['2']['65']="https://homolog.sefaz.go.gov.br/nfe/services/NFeRecepcaoEvento4?wsdl";

        //-- PRODUCAO
        $mListaWS['MG']['NfeRecepcao']['1.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe/services/NfeRecepcao";
        $mListaWS['MG']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe/services/NfeRetRecepcao";
        $mListaWS['MG']['NfeCancelamento']['1.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe/services/NfeCancelamento";
        $mListaWS['MG']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe/services/NfeInutilizacao";
        $mListaWS['MG']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe/services/NfeConsulta";
        $mListaWS['MG']['NfeStatusServico']['1.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe/services/NfeStatusServico";
        $mListaWS['MG']['ConsultaCadastro']['1.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe/services/CadConsultaCadastro";
        $mListaWS['MG']['NfeRecepcao']['2.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeRecepcao2";
        $mListaWS['MG']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeRetRecepcao2";
        $mListaWS['MG']['NfeCancelamento']['2.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeCancelamento2";
        $mListaWS['MG']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeInutilizacao2";
        $mListaWS['MG']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeConsulta2";
        $mListaWS['MG']['NfeStatusServico']['2.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeStatus2";
        $mListaWS['MG']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento";
        $mListaWS['MG']['NfeRecepcao']['3.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeRecepcao2";
        $mListaWS['MG']['NfeRetRecepcao']['3.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeRetRecepcao2";
        $mListaWS['MG']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeInutilizacao2";
        $mListaWS['MG']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeConsulta2";
        $mListaWS['MG']['NfeStatusServico']['3.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeStatus2";
        $mListaWS['MG']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento";
        $mListaWS['MG']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeAutorizacao";
        $mListaWS['MG']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NfeRetAutorizacao";
        $mListaWS['MG']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NFeInutilizacao4";
        $mListaWS['MG']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NFeConsulta4";
        $mListaWS['MG']['NfeStatusServico']['4.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NFeStatusServico4";
        $mListaWS['MG']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NFeAutorizacao4";
        $mListaWS['MG']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NFeRetAutorizacao4";
        $mListaWS['MG']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.fazenda.mg.gov.br/nfe2/services/NFeRecepcaoEvento4";

        //-- HOMOLOGAÇÃO
        $mListaWS['MG']['NfeRecepcao']['1.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe/services/NfeRecepcao";
        $mListaWS['MG']['NfeRetRecepcao']['1.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe/services/NfeRetRecepcao";
        $mListaWS['MG']['NfeCancelamento']['1.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe/services/NfeCancelamento";
        $mListaWS['MG']['NfeInutilizacao']['1.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe/services/NfeInutilizacao";
        $mListaWS['MG']['NfeConsultaNF']['1.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe/services/NfeConsulta";
        $mListaWS['MG']['NfeStatusServico']['1.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe/services/NfeStatusServico";
        $mListaWS['MG']['ConsultaCadastro']['1.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe/services/CadConsultaCadastro";
        $mListaWS['MG']['NfeRecepcao']['2.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeRecepcao2";
        $mListaWS['MG']['NfeRetRecepcao']['2.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeRetRecepcao2";
        $mListaWS['MG']['NfeCancelamento']['2.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeCancelamento2";
        $mListaWS['MG']['NfeInutilizacao']['2.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeInutilizacao2";
        $mListaWS['MG']['NfeConsultaNF']['2.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeConsulta2";
        $mListaWS['MG']['NfeStatusServico']['2.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeStatus2";
        $mListaWS['MG']['NfeCancelamento']['2.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeCancelamento2";
        $mListaWS['MG']['NfeRecepcaoEvento']['2.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento";        
        $mListaWS['MG']['RecepcaoEvento']['3.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento";
        $mListaWS['MG']['NfeInutilizacao']['3.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeInutilizacao2";
        $mListaWS['MG']['NfeConsultaProtocolo']['3.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeConsulta2";
        $mListaWS['MG']['NfeStatusServico']['3.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeStatusServico2";
        $mListaWS['MG']['NfeConsultaCadastro']['3.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/cadconsultacadastro2";
        $mListaWS['MG']['NFeAutorizacao']['3.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeAutorizacao";
        $mListaWS['MG']['NFeRetAutorizacao']['3.10']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NfeRetAutorizacao";
        $mListaWS['MG']['NfeInutilizacao']['4.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeInutilizacao4";
        $mListaWS['MG']['NfeConsultaProtocolo']['4.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeConsulta4";
        $mListaWS['MG']['NfeStatusServico']['4.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeStatusServico4";
        $mListaWS['MG']['RecepcaoEvento']['4.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeRecepcaoEvento4";
        $mListaWS['MG']['NFeAutorizacao']['4.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeAutorizacao4"; 
        $mListaWS['MG']['NFeRetAutorizacao']['4.00']['2']['55']="https://hnfe.fazenda.mg.gov.br/nfe2/services/NFeRetAutorizacao4";

        //-- PRODUCAO
        $mListaWS['MS']['NfeRecepcao']['1.10']['1']['55']="https://producao.nfe.ms.gov.br/producao/services/NfeRecepcao";
        $mListaWS['MS']['NfeRetRecepcao']['1.10']['1']['55']="https://producao.nfe.ms.gov.br/producao/services/NfeRetRecepcao";
        $mListaWS['MS']['NfeCancelamento']['1.10']['1']['55']="https://producao.nfe.ms.gov.br/producao/services/NfeCancelamento";
        $mListaWS['MS']['NfeInutilizacao']['1.10']['1']['55']="https://producao.nfe.ms.gov.br/producao/services/NfeInutilizacao";
        $mListaWS['MS']['NfeConsultaNF']['1.10']['1']['55']="https://producao.nfe.ms.gov.br/producao/services/NfeConsulta";
        $mListaWS['MS']['NfeStatusServico']['1.10']['1']['55']="https://producao.nfe.ms.gov.br/producao/services/NfeStatusServico";
        $mListaWS['MS']['NfeRecepcao']['2.00']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeRecepcao2";
        $mListaWS['MS']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeRetRecepcao2";
        $mListaWS['MS']['NfeCancelamento']['2.00']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeCancelamento2";
        $mListaWS['MS']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeInutilizacao2";
        $mListaWS['MS']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeConsulta2";
        $mListaWS['MS']['NfeStatusServico']['2.00']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeStatusServico2";
        $mListaWS['MS']['ConsultaCadastro']['2.00']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/CadConsultaCadastro2";
        $mListaWS['MS']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeRecepcaoEvento";
        $mListaWS['MS']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/RecepcaoEvento";
        $mListaWS['MS']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeInutilizacao2";
        $mListaWS['MS']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeConsulta2";
        $mListaWS['MS']['NfeStatusServico']['3.10']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeStatusServico2";
        $mListaWS['MS']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeAutorizacao";
        $mListaWS['MS']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.fazenda.ms.gov.br/producao/services2/NfeRetAutorizacao";
        $mListaWS['MS']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.fazenda.ms.gov.br/ws/NFeInutilizacao4";
        $mListaWS['MS']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.fazenda.ms.gov.br/ws/NFeConsultaProtocolo4";
        $mListaWS['MS']['NfeStatusServico']['4.00']['1']['55']="https://nfe.fazenda.ms.gov.br/ws/NFeStatusServico4";
        $mListaWS['MS']['NfeConsultaCadastro']['4.00']['1']['55']="https://nfe.fazenda.ms.gov.br/ws/CadConsultaCadastro4";
        $mListaWS['MS']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.fazenda.ms.gov.br/ws/NFeRecepcaoEvento4";
        $mListaWS['MS']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.fazenda.ms.gov.br/ws/NFeAutorizacao4";
        $mListaWS['MS']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.fazenda.ms.gov.br/ws/NFeRetAutorizacao4";

        //-- HOMOLOGAÇÃO
        $mListaWS['MS']['NfeRecepcao']['1.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services/NfeRecepcao";
        $mListaWS['MS']['NfeRetRecepcao']['1.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services/NfeRetRecepcao";
        $mListaWS['MS']['NfeCancelamento']['1.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services/NfeCancelamento";
        $mListaWS['MS']['NfeInutilizacao']['1.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services/NfeInutilizacao";
        $mListaWS['MS']['NfeConsultaNF']['1.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services/NfeConsulta";
        $mListaWS['MS']['NfeStatusServico']['1.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services/NfeStatusServico";
        $mListaWS['MS']['NfeRecepcao']['2.00']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeRecepcao2";
        $mListaWS['MS']['NfeRetRecepcao']['2.00']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeRetRecepcao2";
        $mListaWS['MS']['NfeCancelamento']['2.00']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeCancelamento2";
        $mListaWS['MS']['NfeInutilizacao']['2.00']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeInutilizacao2";
        $mListaWS['MS']['NfeConsultaNF']['2.00']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeConsulta2";
        $mListaWS['MS']['NfeStatusServico']['2.00']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeStatusServico2";
        $mListaWS['MS']['ConsultaCadastro']['2.00']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/CadConsultaCadastro2";
        $mListaWS['MS']['NfeRecepcaoEvento']['2.00']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/RecepcaoEvento";
        $mListaWS['MS']['RecepcaoEvento']['3.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/RecepcaoEvento";
        $mListaWS['MS']['NfeInutilizacao']['3.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeInutilizacao2";
        $mListaWS['MS']['NfeConsultaProtocolo']['3.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeConsulta2";
        $mListaWS['MS']['NfeStatusServico']['3.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeStatusServico2";
        $mListaWS['MS']['NFeAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeAutorizacao";
        $mListaWS['MS']['NFeRetAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.ms.gov.br/homologacao/services2/NfeRetAutorizacao";
        $mListaWS['MS']['NfeInutilizacao']['4.00']['2']['55']="https://homologacao.nfe.ms.gov.br/ws/NFeInutilizacao4";
        $mListaWS['MS']['NfeConsultaProtocolo']['4.00']['2']['55']="https://homologacao.nfe.ms.gov.br/ws/NFeConsultaProtocolo4";
        $mListaWS['MS']['NfeStatusServico']['4.00']['2']['55']="https://homologacao.nfe.ms.gov.br/ws/NFeStatusServico4";
        $mListaWS['MS']['NfeConsultaCadastro']['4.00']['2']['55']="https://homologacao.nfe.ms.gov.br/ws/CadConsultaCadastro4";
        $mListaWS['MS']['RecepcaoEvento']['4.00']['2']['55']="https://homologacao.nfe.ms.gov.br/ws/NFeRecepcaoEvento4";
        $mListaWS['MS']['NFeAutorizacao']['4.00']['2']['55']="https://homologacao.nfe.ms.gov.br/ws/NFeAutorizacao4";
        $mListaWS['MS']['NFeRetAutorizacao']['4.00']['2']['55']="https://homologacao.nfe.ms.gov.br/ws/NFeRetAutorizacao4";

        /***** Raquel 11/09/2018 *****/
        //-- MATO GROSSO DO SUL NFC-E
        //-- PRODUCAO
        $mListaWS['MS']['NFeAutorizacao']['4.00']['1']['65']="https://nfce.sefaz.ms.gov.br/ws/NFeAutorizacao4";
        $mListaWS['MS']['NFeRetAutorizacao']['4.00']['1']['65']="https://nfce.sefaz.ms.gov.br/ws/NFeRetAutorizacao4";
        $mListaWS['MS']['NfeInutilizacao']['4.00']['1']['65']="https://nfce.sefaz.ms.gov.br/ws/NFeInutilizacao4";
        $mListaWS['MS']['NfeConsultaProtocolo']['4.00']['1']['65']="https://nfce.sefaz.ms.gov.br/ws/NFeConsultaProtocolo4";
        $mListaWS['MS']['NfeStatusServico']['4.00']['1']['65']="https://nfce.sefaz.ms.gov.br/ws/NFeStatusServico4";
        $mListaWS['MS']['NfeConsultaCadastro']['4.00']['1']['65']="https://nfe.sefaz.ms.gov.br/ws/CadConsultaCadastro4";
        $mListaWS['MS']['RecepcaoEvento']['4.00']['1']['65']="https://nfce.sefaz.ms.gov.br/ws/NFeRecepcaoEvento4";

        //-- HOMOLOGAÇÃO
        $mListaWS['MS']['NFeAutorizacao']['4.00']['2']['65']="https://hom.nfce.sefaz.ms.gov.br/ws/NFeAutorizacao4";
        $mListaWS['MS']['NFeRetAutorizacao']['4.00']['2']['65']="https://hom.nfce.sefaz.ms.gov.br/ws/NFeRetAutorizacao4";
        $mListaWS['MS']['NfeInutilizacao']['4.00']['2']['65']="https://hom.nfce.sefaz.ms.gov.br/ws/NFeInutilizacao4";
        $mListaWS['MS']['NfeConsultaProtocolo']['4.00']['2']['65']="https://hom.nfce.sefaz.ms.gov.br/ws/NFeConsultaProtocolo4";
        $mListaWS['MS']['NfeStatusServico']['4.00']['2']['65']="https://hom.nfce.sefaz.ms.gov.br/ws/NFeStatusServico4";
        $mListaWS['MS']['NfeConsultaCadastro']['4.00']['2']['65']="https://hom.nfe.sefaz.ms.gov.br/ws/CadConsultaCadastro4";
        $mListaWS['MS']['RecepcaoEvento']['4.00']['2']['65']="https://hom.nfce.sefaz.ms.gov.br/ws/NFeRecepcaoEvento4";

        //-- PRODUCAO
        $mListaWS['MT']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/NfeRecepcao";
        $mListaWS['MT']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/NfeRetRecepcao";
        $mListaWS['MT']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/NfeCancelamento";
        $mListaWS['MT']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/NfeInutilizacao";
        $mListaWS['MT']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/NfeConsulta";
        $mListaWS['MT']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/NfeStatusServico";
        $mListaWS['MT']['ConsultaCadastro']['1.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/CadConsultaCadastro";
        $mListaWS['MT']['NfeRecepcao']['2.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeRecepcao2";
        $mListaWS['MT']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeRetRecepcao2";
        $mListaWS['MT']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeCancelamento2";
        $mListaWS['MT']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeInutilizacao2";
        $mListaWS['MT']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeConsulta2";
        $mListaWS['MT']['NfeStatusServico']['2.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeStatusServico2";
        $mListaWS['MT']['ConsultaCadastro']['2.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/CadConsultaCadastro2";
        $mListaWS['MT']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/RecepcaoEvento";
        $mListaWS['MT']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeInutilizacao2?wsdl";
        $mListaWS['MT']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeConsulta2?wsdl";
        $mListaWS['MT']['NfeStatusServico']['3.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeStatusServico2?wsdl";
        $mListaWS['MT']['NfeConsultaCadastro']['3.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/CadConsultaCadastro2?wsdl";
        $mListaWS['MT']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/RecepcaoEvento?wsdl";
        $mListaWS['MT']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeAutorizacao?wsdl";
        $mListaWS['MT']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeRetAutorizacao?wsdl";
        $mListaWS['MT']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeInutilizacao4?wsdl";
        $mListaWS['MT']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeConsulta4?wsdl";
        $mListaWS['MT']['NfeStatusServico']['4.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeStatusServico4?wsdl";
        $mListaWS['MT']['NfeConsultaCadastro']['4.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/CadConsultaCadastro4?wsdl";
        $mListaWS['MT']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/RecepcaoEvento4?wsdl";
        $mListaWS['MT']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeAutorizacao4?wsdl";
        $mListaWS['MT']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeRetAutorizacao4?wsdl";

        //-- HOMOLOGAÇÃO
        $mListaWS['MT']['NfeRecepcao']['1.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/NfeRecepcao";
        $mListaWS['MT']['NfeRetRecepcao']['1.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/NfeRetRecepcao";
        $mListaWS['MT']['NfeCancelamento']['1.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/NfeCancelamento";
        $mListaWS['MT']['NfeInutilizacao']['1.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/NfeInutilizacao";
        $mListaWS['MT']['NfeConsultaNF']['1.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/NfeConsulta";
        $mListaWS['MT']['NfeStatusServico']['1.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/NfeStatusServico";
        $mListaWS['MT']['ConsultaCadastro']['1.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/CadConsultaCadastro";
        $mListaWS['MT']['NfeRecepcao']['2.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeRecepcao2?wsdl";
        $mListaWS['MT']['NfeRetRecepcao']['2.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeRetRecepcao2?wsdl";
        $mListaWS['MT']['NfeCancelamento']['2.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeCancelamento2?wsdl";
        $mListaWS['MT']['NfeInutilizacao']['2.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeInutilizacao2?wsdl";
        $mListaWS['MT']['NfeConsultaNF']['2.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeConsulta2?wsdl";
        $mListaWS['MT']['NfeStatusServico']['2.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeStatusServico2?wsdl";
        $mListaWS['MT']['NfeRecepcaoEvento']['2.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/RecepcaoEvento";
        $mListaWS['MT']['NfeInutilizacao']['3.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeInutilizacao2?wsdl";
        $mListaWS['MT']['NfeStatusServico']['3.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeStatusServico2?wsdl";
        $mListaWS['MT']['RecepcaoEvento']['3.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/RecepcaoEvento?wsdl";
        $mListaWS['MT']['NfeConsultaCadastro']['3.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/CadConsultaCadastro2?wsdl";
        $mListaWS['MT']['NFeAutorizacao']['3.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeAutorizacao?wsdl";
        $mListaWS['MT']['NFeRetAutorizacao']['3.10']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeRetAutorizacao?wsdl";
        $mListaWS['MT']['NfeInutilizacao']['4.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeInutilizacao4";
        $mListaWS['MT']['NfeConsultaProtocolo']['4.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeConsulta4";
        $mListaWS['MT']['NfeStatusServico']['4.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeStatusServico4";
        $mListaWS['MT']['NfeConsultaCadastro']['4.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/CadConsultaCadastro4";
        $mListaWS['MT']['RecepcaoEvento']['4.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/RecepcaoEvento4";
        $mListaWS['MT']['NFeAutorizacao']['4.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeAutorizacao4";
        $mListaWS['MT']['NFeRetAutorizacao']['4.00']['2']['55']="https://homologacao.sefaz.mt.gov.br/nfews/v2/services/NfeRetAutorizacao4";

        /***** Raquel 11/09/2018 *****/
        //-- MATO GROSSO NFC-E
        //-- PRODUCAO
        $mListaWS['MT']['NFeAutorizacao']['4.00']['1']['65']="https://nfce.sefaz.mt.gov.br/nfcews/services/NfeAutorizacao4?wsdl";
        $mListaWS['MT']['NFeRetAutorizacao']['4.00']['1']['65']="https://nfce.sefaz.mt.gov.br/nfcews/services/NfeRetAutorizacao4?wsdl";
        $mListaWS['MT']['NfeInutilizacao']['4.00']['1']['65']="https://nfce.sefaz.mt.gov.br/nfcews/services/NfeInutilizacao4?wsdl";
        $mListaWS['MT']['NfeConsultaProtocolo']['4.00']['1']['65']="https://nfce.sefaz.mt.gov.br/nfcews/services/NfeConsulta4?wsdl";
        $mListaWS['MT']['NfeStatusServico']['4.00']['1']['65']="https://nfce.sefaz.mt.gov.br/nfcews/services/NfeStatusServico4?wsdl";
        $mListaWS['MT']['RecepcaoEvento']['4.00']['1']['65']="https://nfce.sefaz.mt.gov.br/nfcews/services/RecepcaoEvento4?wsdl";

        //-- HOMOLOGAÇÃO
        $mListaWS['MT']['NFeAutorizacao']['4.00']['2']['65']="https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeAutorizacao4?wsdl";
        $mListaWS['MT']['NFeRetAutorizacao']['4.00']['2']['65']="https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeRetAutorizacao4?wsdl";
        $mListaWS['MT']['NfeInutilizacao']['4.00']['2']['65']="https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeInutilizacao4?wsdl";
        $mListaWS['MT']['NfeConsultaProtocolo']['4.00']['2']['65']="https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeConsulta4?wsdl";
        $mListaWS['MT']['NfeStatusServico']['4.00']['2']['65']="https://homologacao.sefaz.mt.gov.br/nfcews/services/NfeStatusServico4?wsdl";
        $mListaWS['MT']['RecepcaoEvento']['4.00']['2']['65']="https://homologacao.sefaz.mt.gov.br/nfcews/services/RecepcaoEvento4?wsdl";

        //-- PRODUCAO
        $mListaWS['PE']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeRecepcao";
        $mListaWS['PE']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeRetRecepcao";
        $mListaWS['PE']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeCancelamento";
        $mListaWS['PE']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeInutilizacao";
        $mListaWS['PE']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeConsulta";
        $mListaWS['PE']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeStatusServico";
        $mListaWS['PE']['ConsultaCadastro']['1.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/CadConsultaCadastro";
        $mListaWS['PE']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/RecepcaoEvento";
        $mListaWS['PE']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeInutilizacao2";
        $mListaWS['PE']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeConsulta2";
        $mListaWS['PE']['NfeStatusServico']['3.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeStatusServico2";
        $mListaWS['PE']['NfeConsultaCadastro']['3.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/CadConsultaCadastro2";
        $mListaWS['PE']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeAutorizacao?wsdl";
        $mListaWS['PE']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeRetAutorizacao?wsdl";

        $mListaWS['PE']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NFeInutilizacao4";
        $mListaWS['PE']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NFeConsultaProtocolo4";
        $mListaWS['PE']['NfeStatusServico']['4.00']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NFeStatusServico4";
        $mListaWS['PE']['NfeConsultaCadastro']['4.00']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/CadConsultaCadastro4?wsdl";
        $mListaWS['PE']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NFeRecepcaoEvento4";
        $mListaWS['PE']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NFeAutorizacao4";
        $mListaWS['PE']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.sefaz.pe.gov.br/nfe-service/services/NFeRetAutorizacao4";        

        //-- HOMOLOGAÇÃO
        $mListaWS['PE']['NfeRecepcao']['1.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeRecepcao";
        $mListaWS['PE']['NfeRetRecepcao']['1.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeRetRecepcao";
        $mListaWS['PE']['NfeCancelamento']['1.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeCancelamento";
        $mListaWS['PE']['NfeInutilizacao']['1.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeInutilizacao";
        $mListaWS['PE']['NfeConsultaNF']['1.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeConsulta";
        $mListaWS['PE']['NfeStatusServico']['1.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeStatusServico";
        $mListaWS['PE']['ConsultaCadastro']['1.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/CadConsultaCadastro";
        $mListaWS['PE']['RecepcaoEvento']['3.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/RecepcaoEvento";
        $mListaWS['PE']['NfeInutilizacao']['3.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeInutilizacao2";
        $mListaWS['PE']['NfeConsultaProtocolo']['3.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeConsulta2";
        $mListaWS['PE']['NfeStatusServico']['3.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeStatusServico2";
        $mListaWS['PE']['NFeAutorizacao']['3.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeAutorizacao?wsdl";
        $mListaWS['PE']['NFeRetAutorizacao']['3.10']['2']['55']="https://nfehomolog.sefaz.pe.gov.br/nfe-service/services/NfeRetAutorizacao?wsdl";

        //-- PRODUCAO
        $mListaWS['PR']['NfeRecepcao']['1.10']['1']['55']="https://nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeRecepcao";
        $mListaWS['PR']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeRetRecepcao";
        $mListaWS['PR']['NfeCancelamento']['1.10']['1']['55']="https://nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeCancelamentoNF";
        $mListaWS['PR']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeInutilizacaoNF";
        $mListaWS['PR']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeConsultaNF";
        $mListaWS['PR']['NfeStatusServico']['1.10']['1']['55']="https://nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeStatusServicoNF";        
        $mListaWS['PR']['NfeRecepcao']['2.00']['1']['55']="https://nfe2.fazenda.pr.gov.br/nfe/NFeRecepcao2";
        $mListaWS['PR']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe2.fazenda.pr.gov.br/nfe/NFeRetRecepcao2";
        $mListaWS['PR']['NfeCancelamento']['2.00']['1']['55']="https://nfe2.fazenda.pr.gov.br/nfe/NFeCancelamento2";
        $mListaWS['PR']['NfeInutilizacao']['2.00']['1']['55']="https://nfe2.fazenda.pr.gov.br/nfe/NFeInutilizacao2";
        $mListaWS['PR']['NfeConsultaNF']['2.00']['1']['55']="https://nfe2.fazenda.pr.gov.br/nfe/NFeConsulta2";
        $mListaWS['PR']['NfeStatusServico']['2.00']['1']['55']="https://nfe2.fazenda.pr.gov.br/nfe/NFeStatusServico2";
        $mListaWS['PR']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe2.fazenda.pr.gov.br/nfe-evento/NFeRecepcaoEvento";
        $mListaWS['PR']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.fazenda.pr.gov.br/nfe/NFeInutilizacao3?wsdl";
        $mListaWS['PR']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.fazenda.pr.gov.br/nfe/NFeConsulta3?wsdl";
        $mListaWS['PR']['NfeStatusServico']['3.10']['1']['55']="https://nfe.fazenda.pr.gov.br/nfe/NFeStatusServico3?wsdl";
        $mListaWS['PR']['NfeConsultaCadastro']['3.10']['1']['55']="https://nfe.fazenda.pr.gov.br/nfe/CadConsultaCadastro2?wsdl";
        $mListaWS['PR']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.fazenda.pr.gov.br/nfe/NFeRecepcaoEvento?wsdl";
        $mListaWS['PR']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.fazenda.pr.gov.br/nfe/NFeAutorizacao3?wsdl";
        $mListaWS['PR']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.fazenda.pr.gov.br/nfe/NFeRetAutorizacao3?wsdl";
        $mListaWS['PR']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.sefa.pr.gov.br/nfe/NFeInutilizacao4?wsdl";
        $mListaWS['PR']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.sefa.pr.gov.br/nfe/NFeConsultaProtocolo4?wsdl";
        $mListaWS['PR']['NfeStatusServico']['4.00']['1']['55']="https://nfe.sefa.pr.gov.br/nfe/NFeStatusServico4?wsdl";
        $mListaWS['PR']['NfeConsultaCadastro']['4.00']['1']['55']="https://nfe.sefa.pr.gov.br/nfe/CadConsultaCadastro4?wsdl";
        $mListaWS['PR']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.sefa.pr.gov.br/nfe/NFeRecepcaoEvento4?wsdl";
        $mListaWS['PR']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.sefa.pr.gov.br/nfe/NFeAutorizacao4?wsdl";
        $mListaWS['PR']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.sefa.pr.gov.br/nfe/NFeRetAutorizacao4?wsdl";

        //-- HOMOLOGAÇÃO
        $mListaWS['PR']['NfeRecepcao']['1.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeRecepcao";
        $mListaWS['PR']['NfeRetRecepcao']['1.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeRetRecepcao";
        $mListaWS['PR']['NfeCancelamento']['1.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeCancelamentoNF";
        $mListaWS['PR']['NfeInutilizacao']['1.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeInutilizacaoNF";
        $mListaWS['PR']['NfeConsultaNF']['1.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeConsultaNF";
        $mListaWS['PR']['NfeStatusServico']['1.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/NFENWebServices/services/nfeStatusServicoNF";
        $mListaWS['PR']['NfeRecepcao']['2.00']['2']['55']="https://homologacao.nfe2.fazenda.pr.gov.br/nfe/NFeRecepcao2";
        $mListaWS['PR']['NfeRetRecepcao']['2.00']['2']['55']="https://homologacao.nfe2.fazenda.pr.gov.br/nfe/NFeRetRecepcao2";
        $mListaWS['PR']['NfeCancelamento']['2.00']['2']['55']="https://homologacao.nfe2.fazenda.pr.gov.br/nfe/NFeCancelamento2";
        $mListaWS['PR']['NfeInutilizacao']['2.00']['2']['55']="https://homologacao.nfe2.fazenda.pr.gov.br/nfe/NFeInutilizacao2";
        $mListaWS['PR']['NfeConsultaNF']['2.00']['2']['55']="https://homologacao.nfe2.fazenda.pr.gov.br/nfe/NFeConsulta2";
        $mListaWS['PR']['NfeStatusServico']['2.00']['2']['55']="https://homologacao.nfe2.fazenda.pr.gov.br/nfe/NFeStatusServico2";
        $mListaWS['PR']['NfeRecepcaoEvento']['2.00']['2']['55']="https://homologacao.nfe2.fazenda.pr.gov.br/nfe-evento/NFeRecepcaoEvento";
        $mListaWS['PR']['NfeInutilizacao']['3.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/nfe/NFeInutilizacao3?wsdl";
        $mListaWS['PR']['NfeConsultaProtocolo']['3.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/nfe/NFeConsulta3?wsdl";
        $mListaWS['PR']['NfeStatusServico']['3.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/nfe/NFeStatusServico3?wsdl";
        $mListaWS['PR']['NfeConsultaCadastro']['3.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/nfe/CadConsultaCadastro2?wsdl";
        $mListaWS['PR']['RecepcaoEvento']['3.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/nfe/NFeRecepcaoEvento?wsdl";
        $mListaWS['PR']['NFeAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/nfe/NFeAutorizacao3?wsdl";
        $mListaWS['PR']['NFeRetAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.fazenda.pr.gov.br/nfe/NFeRetAutorizacao3?wsdl";
        $mListaWS['PR']['NfeInutilizacao']['4.00']['2']['55']="https://homologacao.nfe.sefa.pr.gov.br/nfe/NFeInutilizacao4?wsdl";
        $mListaWS['PR']['NfeConsultaProtocolo']['4.00']['2']['55']="https://homologacao.nfe.sefa.pr.gov.br/nfe/NFeConsultaProtocolo4?wsdl";
        $mListaWS['PR']['NfeStatusServico']['4.00']['2']['55']="https://homologacao.nfe.sefa.pr.gov.br/nfe/NFeStatusServico4?wsdl";
        $mListaWS['PR']['NfeConsultaCadastro']['4.00']['2']['55']="https://homologacao.nfe.sefa.pr.gov.br/nfe/CadConsultaCadastro4?wsdl";
        $mListaWS['PR']['RecepcaoEvento']['4.00']['2']['55']="https://homologacao.nfe.sefa.pr.gov.br/nfe/NFeRecepcaoEvento4?wsdl";
        $mListaWS['PR']['NFeAutorizacao']['4.00']['2']['55']="https://homologacao.nfe.sefa.pr.gov.br/nfe/NFeAutorizacao4?wsdl";
        $mListaWS['PR']['NFeRetAutorizacao']['4.00']['2']['55']="https://homologacao.nfe.sefa.pr.gov.br/nfe/NFeRetAutorizacao4?wsdl";


        //-- HOMOLOGAÇÃO NFC-E
        $mListaWS['PR']['NFeRetAutorizacao']['3.10']['2']['65']="https://homologacao.nfce.fazenda.pr.gov.br/nfce/NFeRetAutorizacao3";
        $mListaWS['PR']['NFeAutorizacao']['3.10']['2']['65']="https://homologacao.nfce.fazenda.pr.gov.br/nfce/NFeAutorizacao3";
        $mListaWS['PR']['NfeConsultaProtocolo']['3.10']['2']['65']="https://homologacao.nfce.fazenda.pr.gov.br/nfce/NFeConsulta3";
        $mListaWS['PR']['NfeInutilizacao']['3.10']['2']['65']="https://homologacao.nfce.fazenda.pr.gov.br/nfce/NFeInutilizacao3";
        $mListaWS['PR']['NfeStatusServico']['3.10']['2']['65']="https://homologacao.nfce.fazenda.pr.gov.br/nfce/NFeStatusServico3";
        $mListaWS['PR']['NfeRecepcaoEvento']['3.10']['2']['65']="https://homologacao.nfce.fazenda.pr.gov.br/nfce/NFeRecepcaoEvento";
        $mListaWS['PR']['RecepcaoEvento']['3.10']['2']['65']="https://homologacao.nfce.fazenda.pr.gov.br/nfce/NFeRecepcaoEvento";
        $mListaWS['PR']['NFeAutorizacao']['4.00']['2']['65']="https://homologacao.nfce.sefa.pr.gov.br/nfce/NFeAutorizacao4?wsdl";
        $mListaWS['PR']['NFeRetAutorizacao']['4.00']['2']['65']="https://homologacao.nfce.sefa.pr.gov.br/nfce/NFeRetAutorizacao4?wsdl";
        $mListaWS['PR']['NfeInutilizacao']['4.00']['2']['65']="https://homologacao.nfce.sefa.pr.gov.br/nfce/NFeInutilizacao4?wsdl";
        $mListaWS['PR']['NfeConsultaProtocolo']['4.00']['2']['65']="https://homologacao.nfce.sefa.pr.gov.br/nfce/NFeConsultaProtocolo4?wsdl";
        $mListaWS['PR']['NfeStatusServico']['4.00']['2']['65']="https://homologacao.nfce.sefa.pr.gov.br/nfce/NFeStatusServico4?wsdl";
        $mListaWS['PR']['NfeConsultaCadastro']['4.00']['2']['65']="https://homologacao.nfce.sefa.pr.gov.br/nfce/CadConsultaCadastro4?wsdl";
        $mListaWS['PR']['RecepcaoEvento']['4.00']['2']['65']="https://homologacao.nfce.sefa.pr.gov.br/nfce/NFeRecepcaoEvento4?wsdl";

        //-- PRODUÇÃO NFC-E
        $mListaWS['PR']['NFeRetAutorizacao']['3.10']['1']['65']="https://nfce.fazenda.pr.gov.br/nfce/NFeRetAutorizacao3";
        $mListaWS['PR']['NFeAutorizacao']['3.10']['1']['65']="https://nfce.fazenda.pr.gov.br/nfce/NFeAutorizacao3";
        $mListaWS['PR']['NfeConsultaProtocolo']['3.10']['1']['65']="https://nfce.fazenda.pr.gov.br/nfce/NFeConsulta3";
        $mListaWS['PR']['NfeInutilizacao']['3.10']['1']['65']="https://nfce.fazenda.pr.gov.br/nfce/NFeInutilizacao3";
        $mListaWS['PR']['NfeStatusServico']['3.10']['1']['65']="https://nfce.fazenda.pr.gov.br/nfce/NFeStatusServico3";
        $mListaWS['PR']['NfeRecepcaoEvento']['3.10']['1']['65']="https://nfce.fazenda.pr.gov.br/nfce/NFeRecepcaoEvento";
        $mListaWS['PR']['RecepcaoEvento']['3.10']['1']['65']="https://nfce.fazenda.pr.gov.br/nfce/NFeRecepcaoEvento";
        $mListaWS['PR']['NFeAutorizacao']['4.00']['1']['65']="https://nfce.sefa.pr.gov.br/nfce/NFeAutorizacao4?wsdl";
        $mListaWS['PR']['NFeRetAutorizacao']['4.00']['1']['65']="https://nfce.sefa.pr.gov.br/nfce/NFeRetAutorizacao4?wsdl";
        $mListaWS['PR']['NfeInutilizacao']['4.00']['1']['65']="https://nfce.sefa.pr.gov.br/nfce/NFeInutilizacao4?wsdl";
        $mListaWS['PR']['NfeConsultaProtocolo']['4.00']['1']['65']="https://nfce.sefa.pr.gov.br/nfce/NFeConsultaProtocolo4?wsdl";
        $mListaWS['PR']['NfeStatusServico']['4.00']['1']['65']="https://nfce.sefa.pr.gov.br/nfce/NFeStatusServico4?wsdl";
        $mListaWS['PR']['NfeConsultaCadastro']['4.00']['1']['65']="https://nfce.sefa.pr.gov.br/nfce/CadConsultaCadastro4";
        $mListaWS['PR']['RecepcaoEvento']['4.00']['1']['65']="https://nfce.sefa.pr.gov.br/nfce/NFeRecepcaoEvento4";

        //-- PRODUCAO
        $mListaWS['RS']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/nferecepcao/NfeRecepcao.asmx";
        $mListaWS['RS']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/nferetrecepcao/NfeRetRecepcao.asmx";
        $mListaWS['RS']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/nfecancelamento/NfeCancelamento.asmx";
        $mListaWS['RS']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/nfeinutilizacao/NfeInutilizacao.asmx";
        $mListaWS['RS']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/nfeconsulta/NfeConsulta.asmx";
        $mListaWS['RS']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/nfestatusservico/NfeStatusServico.asmx";
        $mListaWS['RS']['ConsultaCadastro']['1.10']['1']['55']="https://sef.sefaz.rs.gov.br/ws/CadConsultaCadastro/CadConsultaCadastro.asmx";
        $mListaWS['RS']['NfeRecepcao']['2.00']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/Nferecepcao/NFeRecepcao2.asmx";
        $mListaWS['RS']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/NfeRetRecepcao/NfeRetRecepcao2.asmx";
        $mListaWS['RS']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/NfeCancelamento/NfeCancelamento2.asmx";
        $mListaWS['RS']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['RS']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx";
        $mListaWS['RS']['NfeStatusServico']['2.00']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['RS']['ConsultaCadastro']['2.00']['1']['55']="https://sef.sefaz.rs.gov.br/ws/cadconsultacadastro/cadconsultacadastro2.asmx";
        $mListaWS['RS']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefaz.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";        
        $mListaWS['RS']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['RS']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['RS']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx";
        $mListaWS['RS']['NfeStatusServico']['3.10']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['RS']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao.asmx";
        $mListaWS['RS']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao.asmx";
        $mListaWS['RS']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx";
        $mListaWS['RS']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx";
        $mListaWS['RS']['NfeStatusServico']['4.00']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx";
        $mListaWS['RS']['NfeConsultaCadastro']['4.00']['1']['55']="https://cad.sefazrs.rs.gov.br/ws/cadconsultacadastro/cadconsultacadastro4.asmx";
        $mListaWS['RS']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";
        $mListaWS['RS']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx";
        $mListaWS['RS']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.sefazrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['RS']['NfeRecepcao']['1.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/nferecepcao/NfeRecepcao.asmx";
        $mListaWS['RS']['NfeRetRecepcao']['1.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/nferetrecepcao/NfeRetRecepcao.asmx";
        $mListaWS['RS']['NfeCancelamento']['1.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/nfecancelamento/NfeCancelamento.asmx";
        $mListaWS['RS']['NfeInutilizacao']['1.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/nfeinutilizacao/NfeInutilizacao.asmx";
        $mListaWS['RS']['NfeConsultaNF']['1.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/nfeconsulta/NfeConsulta.asmx";
        $mListaWS['RS']['NfeStatusServico']['1.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/nfestatusservico/NfeStatusServico.asmx";
        $mListaWS['RS']['NfeRecepcao']['2.00']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/Nferecepcao/NFeRecepcao2.asmx";
        $mListaWS['RS']['NfeRetRecepcao']['2.00']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/NfeRetRecepcao/NfeRetRecepcao2.asmx";
        $mListaWS['RS']['NfeCancelamento']['2.00']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/NfeCancelamento/NfeCancelamento2.asmx";
        $mListaWS['RS']['NfeInutilizacao']['2.00']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['RS']['NfeConsultaNF']['2.00']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx";
        $mListaWS['RS']['NfeStatusServico']['2.00']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['RS']['NfeRecepcaoEvento']['2.00']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['RS']['RecepcaoEvento']['3.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['RS']['NfeInutilizacao']['3.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['RS']['NfeConsultaProtocolo']['3.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx";
        $mListaWS['RS']['NfeStatusServico']['3.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['RS']['NFeAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao.asmx";
        $mListaWS['RS']['NFeRetAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.sefaz.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao.asmx";
        $mListaWS['RS']['NfeInutilizacao']['4.00']['2']['55']="https://nfe-homologacao.sefazrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx";
        $mListaWS['RS']['NfeConsultaProtocolo']['4.00']['2']['55']="https://nfe-homologacao.sefazrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx";
        $mListaWS['RS']['NfeStatusServico']['4.00']['2']['55']="https://nfe-homologacao.sefazrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx";
        $mListaWS['RS']['RecepcaoEvento']['4.00']['2']['55']="https://nfe-homologacao.sefazrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";
        $mListaWS['RS']['NFeAutorizacao']['4.00']['2']['55']="https://nfe-homologacao.sefazrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx";
        $mListaWS['RS']['NFeRetAutorizacao']['4.00']['2']['55']="https://nfe-homologacao.sefazrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx";

        /***** Raquel 11/09/2018 *****/
        //-- RIO GRANDE DO SUL NFC-E
        //-- PRODUCAO
        $mListaWS['RS']['NFeAutorizacao']['4.00']['1']['65']="https://nfce.sefazrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx";
        $mListaWS['RS']['NFeRetAutorizacao']['4.00']['1']['65']="https://nfce.sefazrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx";
        $mListaWS['RS']['NfeInutilizacao']['4.00']['1']['65']="https://nfce.sefazrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx";
        $mListaWS['RS']['NfeConsultaProtocolo']['4.00']['1']['65']="https://nfce.sefazrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx";
        $mListaWS['RS']['NfeStatusServico']['4.00']['1']['65']="https://nfce.sefazrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx";
        $mListaWS['RS']['RecepcaoEvento']['4.00']['1']['65']="https://nfce.sefazrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['RS']['NFeAutorizacao']['4.00']['2']['65']="https://nfce-homologacao.sefazrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx";
        $mListaWS['RS']['NFeRetAutorizacao']['4.00']['2']['65']="https://nfce-homologacao.sefazrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx";
        $mListaWS['RS']['NfeInutilizacao']['4.00']['2']['65']="https://nfce-homologacao.sefazrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx";
        $mListaWS['RS']['NfeConsultaProtocolo']['4.00']['2']['65']="https://nfce-homologacao.sefazrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx";
        $mListaWS['RS']['NfeStatusServico']['4.00']['2']['65']="https://nfce-homologacao.sefazrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx";
        $mListaWS['RS']['RecepcaoEvento']['4.00']['2']['65']="https://nfce-homologacao.sefazrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";

        //-- PRODUCAO
        $mListaWS['SCAN']['NfeRecepcao']['1.10']['1']['55']="https://www.scan.fazenda.gov.br/NfeRecepcao/NfeRecepcao.asmx";
        $mListaWS['SCAN']['NfeRetRecepcao']['1.10']['1']['55']="https://www.scan.fazenda.gov.br/NfeRetRecepcao/NfeRetRecepcao.asmx";
        $mListaWS['SCAN']['NfeCancelamento']['1.10']['1']['55']="https://www.scan.fazenda.gov.br/NfeCancelamento/NfeCancelamento.asmx";
        $mListaWS['SCAN']['NfeInutilizacao']['1.10']['1']['55']="https://www.scan.fazenda.gov.br/NfeInutilizacao/NfeInutilizacao.asmx";
        $mListaWS['SCAN']['NfeConsultaNF']['1.10']['1']['55']="https://www.scan.fazenda.gov.br/NfeConsulta/NfeConsulta.asmx";
        $mListaWS['SCAN']['NfeStatusServico']['1.10']['1']['55']="https://www.scan.fazenda.gov.br/NfeStatusServico/NfeStatusServico.asmx";
        $mListaWS['SCAN']['NfeRecepcao']['2.00']['1']['55']="https://www.scan.fazenda.gov.br/NfeRecepcao2/NfeRecepcao2.asmx";
        $mListaWS['SCAN']['NfeRetRecepcao']['2.00']['1']['55']="https://www.scan.fazenda.gov.br/NfeRetRecepcao2/NfeRetRecepcao2.asmx";
        $mListaWS['SCAN']['NfeCancelamento']['2.00']['1']['55']="https://www.scan.fazenda.gov.br/NfeCancelamento2/NfeCancelamento2.asmx";
        $mListaWS['SCAN']['NfeInutilizacao']['2.00']['1']['55']="https://www.scan.fazenda.gov.br/NfeInutilizacao2/NfeInutilizacao2.asmx";
        $mListaWS['SCAN']['NfeConsultaNF']['2.00']['1']['55']="https://www.scan.fazenda.gov.br/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SCAN']['NfeStatusServico']['2.00']['1']['55']="https://www.scan.fazenda.gov.br/NFeStatusServico2/NFeStatusServico2.asmx";
        $mListaWS['SCAN']['NfeRecepcaoEvento']['2.00']['1']['55']="https://www.scan.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        //$mListaWS['SCAN']['NfeRecepcaoEvento']['2.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['SCAN']['NfeRecepcao']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeRecepcao/NfeRecepcao.asmx";
        $mListaWS['SCAN']['NfeRetRecepcao']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeRetRecepcao/NfeRetRecepcao.asmx";
        $mListaWS['SCAN']['NfeCancelamento']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeCancelamento/NfeCancelamento.asmx";
        $mListaWS['SCAN']['NfeInutilizacao']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeInutilizacao/NfeInutilizacao.asmx";
        $mListaWS['SCAN']['NfeConsultaNF']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeConsulta/NfeConsulta.asmx";
        $mListaWS['SCAN']['NfeStatusServico']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeStatusServico/NfeStatusServico.asmx";
        $mListaWS['SCAN']['NfeRecepcao']['2.00']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeRecepcao2/NfeRecepcao2.asmx";
        $mListaWS['SCAN']['NfeRetRecepcao']['2.00']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeRetRecepcao2/NfeRetRecepcao2.asmx";
        $mListaWS['SCAN']['NfeCancelamento']['2.00']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeCancelamento2/NfeCancelamento2.asmx";
        $mListaWS['SCAN']['NfeInutilizacao']['2.00']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeInutilizacao2/NfeInutilizacao2.asmx";
        $mListaWS['SCAN']['NfeConsultaNF']['2.00']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SCAN']['NfeStatusServico']['2.00']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/NfeStatusServico2/NfeStatusServico2.asmx";
        $mListaWS['SCAN']['NfeRecepcaoEvento']['2.00']['2']['55']="https://hom.nfe.fazenda.gov.br/SCAN/RecepcaoEvento/RecepcaoEvento.asmx";

        //-- PRODUCAO
        $mListaWS['SP']['NfeRecepcao']['1.10']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nferecepcao.asmx";
        $mListaWS['SP']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nferetrecepcao.asmx";
        $mListaWS['SP']['NfeCancelamento']['1.10']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nfecancelamento.asmx";
        $mListaWS['SP']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nfeinutilizacao.asmx";
        $mListaWS['SP']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nfeconsulta.asmx";
        $mListaWS['SP']['NfeStatusServico']['1.10']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nfestatusservico.asmx";
        $mListaWS['SP']['ConsultaCadastro']['1.10']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/cadconsultacadastro.asmx";
        $mListaWS['SP']['NfeRecepcao']['2.00']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nferecepcao2.asmx";
        $mListaWS['SP']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nferetrecepcao2.asmx";
        $mListaWS['SP']['NfeCancelamento']['2.00']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nfecancelamento2.asmx";
        $mListaWS['SP']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nfeinutilizacao2.asmx";
        $mListaWS['SP']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nfeconsulta2.asmx";
        $mListaWS['SP']['NfeStatusServico']['2.00']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/nfestatusservico2.asmx";
        $mListaWS['SP']['ConsultaCadastro']['2.00']['1']['55']="https://nfe.fazenda.sp.gov.br/nfeweb/services/cadconsultacadastro2.asmx";
        $mListaWS['SP']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.fazenda.sp.gov.br/eventosWEB/services/RecepcaoEvento.asmx";
        $mListaWS['SP']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nfeinutilizacao2.asmx";
        $mListaWS['SP']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nfeconsulta2.asmx";
        $mListaWS['SP']['NfeStatusServico']['3.10']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nfestatusservico2.asmx";
        $mListaWS['SP']['NfeConsultaCadastro']['3.10']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/cadconsultacadastro2.asmx";
        $mListaWS['SP']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/recepcaoevento.asmx";
        $mListaWS['SP']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nfeautorizacao.asmx";
        $mListaWS['SP']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nferetautorizacao.asmx";
        $mListaWS['SP']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nfeinutilizacao4.asmx";
        $mListaWS['SP']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nfeconsultaprotocolo4.asmx";
        $mListaWS['SP']['NfeStatusServico']['4.00']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nfestatusservico4.asmx";
        $mListaWS['SP']['NfeConsultaCadastro']['4.00']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/cadconsultacadastro4.asmx";
        $mListaWS['SP']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nferecepcaoevento4.asmx";
        $mListaWS['SP']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nfeautorizacao4.asmx";
        $mListaWS['SP']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.fazenda.sp.gov.br/ws/nferetautorizacao4.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['SP']['NfeRecepcao']['1.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/nferecepcao.asmx";
        $mListaWS['SP']['NfeRetRecepcao']['1.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/nferetrecepcao.asmx";
        $mListaWS['SP']['NfeCancelamento']['1.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/nfecancelamento.asmx";
        $mListaWS['SP']['NfeInutilizacao']['1.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/nfeinutilizacao.asmx";
        $mListaWS['SP']['NfeConsultaNF']['1.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/nfeconsulta.asmx";
        $mListaWS['SP']['NfeStatusServico']['1.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/nfestatusservico.asmx";
        $mListaWS['SP']['ConsultaCadastro']['1.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeWEB/services/cadconsultacadastro.asmx";
        $mListaWS['SP']['NfeRecepcao']['2.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/NfeRecepcao2.asmx";
        $mListaWS['SP']['NfeRetRecepcao']['2.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/NfeRetRecepcao2.asmx";
        $mListaWS['SP']['NfeCancelamento']['2.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/NfeCancelamento2.asmx";
        $mListaWS['SP']['NfeInutilizacao']['2.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/NfeInutilizacao2.asmx";
        $mListaWS['SP']['NfeConsultaNF']['2.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/NfeConsulta2.asmx";
        $mListaWS['SP']['NfeStatusServico']['2.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/NfeStatusServico2.asmx";
        $mListaWS['SP']['ConsultaCadastro']['2.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/nfeweb/services/CadConsultaCadastro2.asmx";
        $mListaWS['SP']['NfeRecepcaoEvento']['2.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/eventosWEB/services/RecepcaoEvento.asmx";
        $mListaWS['SP']['NfeInutilizacao']['3.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nfeinutilizacao2.asmx";
        $mListaWS['SP']['NfeConsultaProtocolo']['3.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nfeconsulta2.asmx";
        $mListaWS['SP']['NfeStatusServico']['3.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nfestatusservico2.asmx";
        $mListaWS['SP']['NfeConsultaCadastro']['3.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/cadconsultacadastro2.asmx";
        $mListaWS['SP']['RecepcaoEvento']['3.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/recepcaoevento.asmx";
        $mListaWS['SP']['NFeAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nfeautorizacao.asmx";
        $mListaWS['SP']['NFeRetAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nferetautorizacao.asmx";
        $mListaWS['SP']['NfeInutilizacao']['4.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nfeinutilizacao4.asmx";
        $mListaWS['SP']['NfeConsultaProtocolo']['4.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nfeconsultaprotocolo4.asmx";
        $mListaWS['SP']['NfeStatusServico']['4.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nfestatusservico4.asmx";
        $mListaWS['SP']['NfeConsultaCadastro']['4.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/cadconsultacadastro4.asmx";
        $mListaWS['SP']['RecepcaoEvento']['4.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nferecepcaoevento4.asmx";
        $mListaWS['SP']['NFeAutorizacao']['4.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nfeautorizacao4.asmx";
        $mListaWS['SP']['NFeRetAutorizacao']['4.00']['2']['55']="https://homologacao.nfe.fazenda.sp.gov.br/ws/nferetautorizacao4.asmx";

        /***** Raquel 11/09/2018 *****/
        //-- SÃO PAULO NFC-E
        //-- PRODUCAO
        $mListaWS['SP']['NFeAutorizacao']['4.00']['1']['65']="https://nfce.fazenda.sp.gov.br/ws/NFeAutorizacao4.asmx";
        $mListaWS['SP']['NFeRetAutorizacao']['4.00']['1']['65']="https://nfce.fazenda.sp.gov.br/ws/NFeRetAutorizacao4.asmx";
        $mListaWS['SP']['NfeInutilizacao']['4.00']['1']['65']="https://nfce.fazenda.sp.gov.br/ws/NFeInutilizacao4.asmx";
        $mListaWS['SP']['NfeConsultaProtocolo']['4.00']['1']['65']="https://nfce.fazenda.sp.gov.br/ws/NFeConsultaProtocolo4.asmx";
        $mListaWS['SP']['NfeStatusServico']['4.00']['1']['65']="https://nfce.fazenda.sp.gov.br/ws/NFeStatusServico4.asmx";
        $mListaWS['SP']['RecepcaoEvento']['4.00']['1']['65']="https://nfce.fazenda.sp.gov.br/ws/NFeRecepcaoEvento4.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['SP']['NFeAutorizacao']['4.00']['2']['65']="https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeAutorizacao4.asmx";
        $mListaWS['SP']['NFeRetAutorizacao']['4.00']['2']['65']="https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeRetAutorizacao4.asmx";
        $mListaWS['SP']['NfeInutilizacao']['4.00']['2']['65']="https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeInutilizacao4.asmx";
        $mListaWS['SP']['NfeConsultaProtocolo']['4.00']['2']['65']="https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeConsultaProtocolo4.asmx";
        $mListaWS['SP']['NfeStatusServico']['4.00']['2']['65']="https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeStatusServico4.asmx";
        $mListaWS['SP']['RecepcaoEvento']['4.00']['2']['65']="https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeRecepcaoEvento4.asmx";

        //-- PRODUCAO
        $mListaWS['SVAN']['NfeRecepcao']['1.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NfeRecepcao/NfeRecepcao.asmx";
        $mListaWS['SVAN']['NfeRetRecepcao']['1.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeRetRecepcao/NFeRetRecepcao.asmx";
        $mListaWS['SVAN']['NfeCancelamento']['1.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeCancelamento/NFeCancelamento.asmx";
        $mListaWS['SVAN']['NfeInutilizacao']['1.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeInutilizacao/NFeInutilizacao.asmx";
        $mListaWS['SVAN']['NfeConsultaNF']['1.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/nfeconsulta/nfeconsulta.asmx";
        $mListaWS['SVAN']['NfeStatusServico']['1.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeStatusServico/NFeStatusServico.asmx";
        $mListaWS['SVAN']['NfeRecepcao']['2.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NfeRecepcao2/NfeRecepcao2.asmx";
        $mListaWS['SVAN']['NfeRetRecepcao']['2.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeRetRecepcao2/NFeRetRecepcao2.asmx";
        $mListaWS['SVAN']['NfeCancelamento']['2.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeCancelamento2/NFeCancelamento2.asmx";
        $mListaWS['SVAN']['NfeInutilizacao']['2.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeInutilizacao2/NFeInutilizacao2.asmx";
        $mListaWS['SVAN']['NfeConsultaNF']['2.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/nfeconsulta2/nfeconsulta2.asmx";
        $mListaWS['SVAN']['NfeStatusServico']['2.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeStatusServico2/NFeStatusServico2.asmx";
        $mListaWS['SVAN']['RecepcaoEvento']['3.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        $mListaWS['SVAN']['NfeInutilizacao']['3.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NfeInutilizacao2/NfeInutilizacao2.asmx";
        $mListaWS['SVAN']['NfeConsultaProtocolo']['3.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SVAN']['NfeStatusServico']['3.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NfeStatusServico2/NfeStatusServico2.asmx";
        $mListaWS['SVAN']['NfeDownloadNF']['3.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NfeDownloadNF/NfeDownloadNF.asmx";
        $mListaWS['SVAN']['NFeAutorizacao']['3.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NfeAutorizacao/NfeAutorizacao.asmx";
        $mListaWS['SVAN']['NFeRetAutorizacao']['3.10']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NfeRetAutorizacao/NfeRetAutorizacao.asmx";       
        $mListaWS['SVAN']['NfeInutilizacao']['4.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeInutilizacao4/NFeInutilizacao4.asmx";
        $mListaWS['SVAN']['NfeConsultaProtocolo']['4.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeConsultaProtocolo4/NFeConsultaProtocolo4.asmx";
        $mListaWS['SVAN']['NfeStatusServico']['4.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeStatusServico4/NFeStatusServico4.asmx";
        $mListaWS['SVAN']['RecepcaoEvento']['4.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeRecepcaoEvento4/NFeRecepcaoEvento4.asmx";
        $mListaWS['SVAN']['NFeAutorizacao']['4.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeAutorizacao4/NFeAutorizacao4.asmx";
        $mListaWS['SVAN']['NFeRetAutorizacao']['4.00']['1']['55']="https://www.sefazvirtual.fazenda.gov.br/NFeRetAutorizacao4/NFeRetAutorizacao4.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['SVAN']['NfeRecepcao']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/NfeRecepcao/NfeRecepcao.asmx";
        $mListaWS['SVAN']['NfeRetRecepcao']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/NFeRetRecepcao/NFeRetRecepcao.asmx";
        $mListaWS['SVAN']['NfeCancelamento']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/NFeCancelamento/NFeCancelamento.asmx";
        $mListaWS['SVAN']['NfeInutilizacao']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/NFeInutilizacao/NFeInutilizacao.asmx";
        $mListaWS['SVAN']['NfeConsultaNF']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/nfeconsulta/nfeconsulta.asmx";
        $mListaWS['SVAN']['NfeStatusServico']['1.10']['2']['55']="https://hom.nfe.fazenda.gov.br/NFeStatusServico/NFeStatusServico.asmx";
        $mListaWS['SVAN']['NfeRecepcao']['2.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeRecepcao2/NfeRecepcao2.asmx";
        $mListaWS['SVAN']['NfeRetRecepcao']['2.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeRetRecepcao2/NfeRetRecepcao2.asmx";
        $mListaWS['SVAN']['NfeCancelamento']['2.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeCancelamento2/NfeCancelamento2.asmx";
        $mListaWS['SVAN']['NfeInutilizacao']['2.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeInutilizacao2/NfeInutilizacao2.asmx";
        $mListaWS['SVAN']['NfeConsultaNF']['2.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SVAN']['NfeStatusServico']['2.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeStatusServico2/NfeStatusServico2.asmx";
        $mListaWS['SVAN']['NfeRecepcaoEvento']['2.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        $mListaWS['SVAN']['RecepcaoEvento']['3.10']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        $mListaWS['SVAN']['NfeInutilizacao']['3.10']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeInutilizacao2/NfeInutilizacao2.asmx";
        $mListaWS['SVAN']['NfeConsultaProtocolo']['3.10']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SVAN']['NfeStatusServico']['3.10']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeStatusServico2/NfeStatusServico2.asmx";
        $mListaWS['SVAN']['NfeDownloadNF']['3.10']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeDownloadNF/NfeDownloadNF.asmx";
        $mListaWS['SVAN']['NFeAutorizacao']['3.10']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeAutorizacao/NfeAutorizacao.asmx";
        $mListaWS['SVAN']['NFeRetAutorizacao']['3.10']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NfeRetAutorizacao/NfeRetAutorizacao.asmx";
        $mListaWS['SVAN']['NfeInutilizacao']['4.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NFeInutilizacao4/NFeInutilizacao4.asmx";
        $mListaWS['SVAN']['NfeConsultaProtocolo']['4.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NFeConsultaProtocolo4/NFeConsultaProtocolo4.asmx";
        $mListaWS['SVAN']['NfeStatusServico']['4.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NFeStatusServico4/NFeStatusServico4.asmx";
        $mListaWS['SVAN']['RecepcaoEvento']['4.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NFeRecepcaoEvento4/NFeRecepcaoEvento4.asmx";
        $mListaWS['SVAN']['NFeAutorizacao']['4.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NFeAutorizacao4/NFeAutorizacao4.asmx";
        $mListaWS['SVAN']['NFeRetAutorizacao']['4.00']['2']['55']="https://hom.sefazvirtual.fazenda.gov.br/NFeRetAutorizacao4/NFeRetAutorizacao4.asmx";

        //-- PRODUCAO
        // ------------------
        $mListaWS['SVRS']['NfeRecepcao']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nferecepcao/NfeRecepcao.asmx";
        $mListaWS['SVRS']['NfeRetRecepcao']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nferetrecepcao/NfeRetRecepcao.asmx";
        $mListaWS['SVRS']['NfeCancelamento']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfecancelamento/NfeCancelamento.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfeinutilizacao/NfeInutilizacao.asmx";
        $mListaWS['SVRS']['NfeConsultaNF']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfeconsulta/NfeConsulta.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['1.10']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfestatusservico/NfeStatusServico.asmx";
        $mListaWS['SVRS']['NfeRecepcao']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/Nferecepcao/NFeRecepcao2.asmx";
        $mListaWS['SVRS']['NfeRetRecepcao']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeRetRecepcao/NfeRetRecepcao2.asmx";
        $mListaWS['SVRS']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeCancelamento/NfeCancelamento2.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['SVRS']['NfeConsultaNF']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['SVRS']['NfeRecepcaoEvento']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['3.10']['1']['55']="https://nfe.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['3.10']['1']['55']="https://nfe.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['SVRS']['NfeConsultaProtocolo']['3.10']['1']['55']="https://nfe.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['3.10']['1']['55']="https://nfe.svrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['SVRS']['NFeAutorizacao']['3.10']['1']['55']="https://nfe.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao.asmx";
        $mListaWS['SVRS']['NFeRetAutorizacao']['3.10']['1']['55']="https://nfe.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['4.00']['1']['55']="https://nfe.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx";
        $mListaWS['SVRS']['NfeConsultaProtocolo']['4.00']['1']['55']="https://nfe.svrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['4.00']['1']['55']="https://nfe.svrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx";
        $mListaWS['SVRS']['NfeConsultaCadastro']['4.00']['1']['55']="https://cad.svrs.rs.gov.br/ws/cadconsultacadastro/cadconsultacadastro4.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['4.00']['1']['55']="https://nfe.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";
        $mListaWS['SVRS']['NFeAutorizacao']['4.00']['1']['55']="https://nfe.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx";
        $mListaWS['SVRS']['NFeRetAutorizacao']['4.00']['1']['55']="https://nfe.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['SVRS']['NfeRecepcao']['1.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/nferecepcao/NfeRecepcao.asmx";
        $mListaWS['SVRS']['NfeRetRecepcao']['1.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/nferetrecepcao/NfeRetRecepcao.asmx";
        $mListaWS['SVRS']['NfeCancelamento']['1.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/nfecancelamento/NfeCancelamento.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['1.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/nfeinutilizacao/NfeInutilizacao.asmx";
        $mListaWS['SVRS']['NfeConsultaNF']['1.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/nfeconsulta/NfeConsulta.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['1.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/nfestatusservico/NfeStatusServico.asmx";
        $mListaWS['SVRS']['NfeRecepcao']['2.00']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/Nferecepcao/NFeRecepcao2.asmx";
        $mListaWS['SVRS']['NfeRetRecepcao']['2.00']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeRetRecepcao/NfeRetRecepcao2.asmx";
        $mListaWS['SVRS']['NfeCancelamento']['2.00']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeCancelamento/NfeCancelamento2.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['2.00']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['SVRS']['NfeConsultaNF']['2.00']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['2.00']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['SVRS']['NfeRecepcaoEvento']['2.00']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['3.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['3.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx";
        $mListaWS['SVRS']['NfeConsultaProtocolo']['3.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['3.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx";
        $mListaWS['SVRS']['NFeAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao.asmx";
        $mListaWS['SVRS']['NFeRetAutorizacao']['3.10']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['4.00']['2']['55']="https://nfe-homologacao.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx";
        $mListaWS['SVRS']['NfeConsultaProtocolo']['4.00']['2']['55']="https://nfe-homologacao.svrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['4.00']['2']['55']="https://nfe-homologacao.svrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['4.00']['2']['55']="https://nfe-homologacao.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";
        $mListaWS['SVRS']['NFeAutorizacao']['4.00']['2']['55']="https://nfe-homologacao.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx";
        $mListaWS['SVRS']['NFeRetAutorizacao']['4.00']['2']['55']="https://nfe-homologacao.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx";

        /***** Raquel 11/09/2018 *****/
        //-- SEFAZ VIRTUAL - SVRS NFC-E
        //-- PRODUCAO
        $mListaWS['SVRS']['NFeAutorizacao']['4.00']['1']['65']="https://nfce.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx";
        $mListaWS['SVRS']['NFeRetAutorizacao']['4.00']['1']['65']="https://nfce.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['4.00']['1']['65']="https://nfce.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx";
        $mListaWS['SVRS']['NfeConsultaProtocolo']['4.00']['1']['65']="https://nfce.svrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['4.00']['1']['65']="https://nfce.svrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['4.00']['1']['65']="https://nfce.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['SVRS']['NFeAutorizacao']['4.00']['2']['65']="https://nfce-homologacao.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx";
        $mListaWS['SVRS']['NFeRetAutorizacao']['4.00']['2']['65']="https://nfce-homologacao.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx";
        $mListaWS['SVRS']['NfeInutilizacao']['4.00']['2']['65']="https://nfce-homologacao.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx";
        $mListaWS['SVRS']['NfeConsultaProtocolo']['4.00']['2']['65']="https://nfce-homologacao.svrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx";
        $mListaWS['SVRS']['NfeStatusServico']['4.00']['2']['65']="https://nfce-homologacao.svrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['4.00']['2']['65']="https://nfce-homologacao.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx";

        //-- SVC
        $mListaWS['SVC']['NfeRecepcao']['2.00']['1']['55']="https://www.svc.fazenda.gov.br/NfeRecepcao2/NfeRecepcao2.asmx";
        $mListaWS['SVC']['NfeRetRecepcao']['2.00']['1']['55']="https://www.svc.fazenda.gov.br/NfeRetRecepcao2/NfeRetRecepcao2.asmx";
        //$mListaWS['SVC']['NfeCancelamento']['2.00']['1']['55']="https://nfe.sefazvirtual.rs.gov.br/ws/NfeCancelamento/NfeCancelamento2.asmx";
        $mListaWS['SVC']['NfeInutilizacao']['2.00']['1']['55']="https://www.svc.fazenda.gov.br/NfeInutilizacao2/NfeInutilizacao2.asmx";
        $mListaWS['SVC']['NfeConsultaNF']['2.00']['1']['55']="https://www.svc.fazenda.gov.br/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SVC']['NfeStatusServico']['2.00']['1']['55']="https://www.svc.fazenda.gov.br/NfeStatusServico2/NfeStatusServico2.asmx";
        $mListaWS['SVC']['NfeRecepcaoEvento']['2.00']['1']['55']="https://www.svc.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        $mListaWS['SVC']['RecepcaoEvento']['3.10']['1']['55']="https://www.svc.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        $mListaWS['SVC']['NfeConsultaProtocolo']['3.10']['1']['55']="https://www.svc.fazenda.gov.br/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SVC']['NfeStatusServico']['3.10']['1']['55']="https://www.svc.fazenda.gov.br/NfeStatusServico2/NfeStatusServico2.asmx";
        $mListaWS['SVC']['NFeAutorizacao']['3.10']['1']['55']="https://www.svc.fazenda.gov.br/NfeAutorizacao/NfeAutorizacao.asmx";
        $mListaWS['SVC']['NFeRetAutorizacao']['3.10']['1']['55']="https://www.svc.fazenda.gov.br/NfeRetAutorizacao/NfeRetAutorizacao.asmx";
        $mListaWS['SVC']['NfeConsultaProtocolo']['4.00']['1']['55']="https://www.svc.fazenda.gov.br/NFeConsultaProtocolo4/NFeConsultaProtocolo4.asmx";
        $mListaWS['SVC']['NfeStatusServico']['4.00']['1']['55']="https://www.svc.fazenda.gov.br/NFeStatusServico4/NFeStatusServico4.asmx";
        $mListaWS['SVC']['RecepcaoEvento']['4.00']['1']['55']="https://www.svc.fazenda.gov.br/NFeRecepcaoEvento4/NFeRecepcaoEvento4.asmx";
        $mListaWS['SVC']['NFeAutorizacao']['4.00']['1']['55']="https://www.svc.fazenda.gov.br/NFeAutorizacao4/NFeAutorizacao4.asmx";
        $mListaWS['SVC']['NFeRetAutorizacao']['4.00']['1']['55']="https://www.svc.fazenda.gov.br/NFeRetAutorizacao4/NFeRetAutorizacao4.asmx";

        //-- HOMOLOGAÇÃO
        $mListaWS['SVC']['NfeRecepcao']['2.00']['2']['55']="https://hom.svc.fazenda.gov.br/NfeRecepcao2/NfeRecepcao2.asmx";
        $mListaWS['SVC']['NfeRetRecepcao']['2.00']['2']['55']="https://hom.svc.fazenda.gov.br/NfeRetRecepcao2/NfeRetRecepcao2.asmx";
        //$mListaWS['SVC']['NfeCancelamento']['2.00']['2']['55']="https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/NfeCancelamento/NfeCancelamento2.asmx";
        $mListaWS['SVC']['NfeInutilizacao']['2.00']['2']['55']="https://hom.svc.fazenda.gov.br/NfeInutilizacao2/NfeInutilizacao2.asmx";
        $mListaWS['SVC']['NfeConsultaNF']['2.00']['2']['55']="https://hom.svc.fazenda.gov.br/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SVC']['NfeStatusServico']['2.00']['2']['55']="https://hom.svc.fazenda.gov.br/NfeStatusServico2/NfeStatusServico2.asmx";
        $mListaWS['SVC']['NfeRecepcaoEvento']['2.00']['2']['55']="https://hom.svc.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        $mListaWS['SVC']['RecepcaoEvento']['3.10']['2']['55']="https://hom.svc.fazenda.gov.br/RecepcaoEvento/RecepcaoEvento.asmx";
        $mListaWS['SVC']['NfeConsultaProtocolo']['3.10']['2']['55']="https://hom.svc.fazenda.gov.br/NfeConsulta2/NfeConsulta2.asmx";
        $mListaWS['SVC']['NfeStatusServico']['3.10']['2']['55']="https://hom.svc.fazenda.gov.br/NfeStatusServico2/NfeStatusServico2.asmx";
        $mListaWS['SVC']['NFeAutorizacao']['3.10']['2']['55']="https://hom.svc.fazenda.gov.br/NfeAutorizacao/NfeAutorizacao.asmx";
        $mListaWS['SVC']['NFeRetAutorizacao']['3.10']['2']['55']="https://hom.svc.fazenda.gov.br/NfeRetAutorizacao/NfeRetAutorizacao.asmx";
        $mListaWS['SVC']['NfeConsultaProtocolo']['4.00']['2']['55']="https://hom.svc.fazenda.gov.br/NFeConsultaProtocolo4/NFeConsultaProtocolo4.asmx";
        $mListaWS['SVC']['NfeStatusServico']['4.00']['2']['55']="https://hom.svc.fazenda.gov.br/NFeStatusServico4/NFeStatusServico4.asmx";
        $mListaWS['SVC']['RecepcaoEvento']['4.00']['2']['55']="https://hom.svc.fazenda.gov.br/NFeRecepcaoEvento4/NFeRecepcaoEvento4.asmx";
        $mListaWS['SVC']['NFeAutorizacao']['4.00']['2']['55']="https://hom.svc.fazenda.gov.br/NFeAutorizacao4/NFeAutorizacao4.asmx";
        $mListaWS['SVC']['NFeRetAutorizacao']['4.00']['2']['55']="https://hom.svc.fazenda.gov.br/NFeRetAutorizacao4/NFeRetAutorizacao4.asmx";

        $cAmbiente=$this->getIdentAmbiente();
        $cTipoDocumento=$this->getTipoDocAuxiliar();
        $cEstadoEmitente=$this->getUFEmitente();
        $cVersao=$this->getVersaoNFe();
        $cModelo=$this->getModeloNF();

        if ($cTipoDocumento==3)
        {
            $cEstadoEmitente="SVC";
        }
        else if ($cTipoDocumento==6) {
            $cEstadoEmitente="SVC";
        }
        else if ($cTipoDocumento==7) {
            $cEstadoEmitente="SVRS";
        }
        elseif ($cEstadoEmitente=='AC' or $cEstadoEmitente=='AL' or $cEstadoEmitente=='AP' or $cEstadoEmitente=='DF' or $cEstadoEmitente=='PB' or $cEstadoEmitente=='RJ' or
                $cEstadoEmitente=='RR' or $cEstadoEmitente=='SC' or $cEstadoEmitente=='SE' or $cEstadoEmitente=='RO' or $cEstadoEmitente=='TO' or $cEstadoEmitente=='ES' or $cEstadoEmitente=='PA')
        {
            $cEstadoEmitente='SVRS';
        }
        elseif ($cEstadoEmitente=='XES' or $cEstadoEmitente=='MA' or $cEstadoEmitente=='PI' or $cEstadoEmitente=='RN' )
        {
            $cEstadoEmitente='SVAN';
        }

        if (!empty($cEstadoEmitente) and !empty($cMetodo) and !empty($cVersao) and !empty($cAmbiente))
        {
           $cRetorno = $mListaWS[$cEstadoEmitente][$cMetodo][$cVersao][$cAmbiente][$cModelo];
           $this->retornoDebug .= "1. Retorno: ".$cRetorno."\n".$cEstadoEmitente."\n".$cVersao."\n".$cAmbiente."\n".$cModelo."\n";
        }

        if (empty($cRetorno))
        {
            $this->retornoDebug .= "2. Retorno: ".$cRetorno."<br />";
         //   die();

           $this->mErrorMsg[] = "Não foi identificado o ambiente da nota fiscal. Estado Emitente: $cEstadoEmitente / Ambiente: $cAmbiente / Versao: $cVersao / Metodo: $cMetodo / Modelo: $cModelo";
           $this->errorStatus = true;
           return false;
        }
        else
        {
           return $cRetorno;
        }
    }

    public function setConsultaNFCe()
    {
        // PRODUÇÃO
        // CONSULTA VERSÃO 1.00
        /*$mListaWS['AC']['1']['4.00']="www.sefaznet.ac.gov.br/nfce/consulta";
        $mListaWS['AL']['1']['4.00']="http://nfce.sefaz.al.gov.br/consultaNFCe.htm";
        $mListaWS['AP']['1']['4.00']="https://www.sefaz.ap.gov.br/sate/seg/SEGf_AcessarFuncao.jsp?cdFuncao=FIS_1261";
        $mListaWS['AM']['1']['4.00']="sistemas.sefaz.am.gov.br/nfceweb/formConsulta.do";
        $mListaWS['BA']['1']['4.00']="nfe.sefaz.ba.gov.br/servicos/nfce/default.aspx";
        $mListaWS['DF']['1']['4.00']="http://dec.fazenda.df.gov.br/NFCE/";
        $mListaWS['ES']['1']['4.00']="http://app.sefaz.es.gov.br/ConsultaNFCe";
        $mListaWS['MA']['1']['4.00']="http://www.nfce.sefaz.ma.gov.br/portal/consultaNFe.do?method=preFilterCupom&";
        $mListaWS['MT']['1']['4.00']="http://www.sefaz.mt.gov.br/nfce/consultanfce";
        $mListaWS['MS']['1']['4.00']="http://www.dfe.ms.gov.br/nfce";
        $mListaWS['PA']['1']['4.00']="https://appnfc.sefa.pa.gov.br/portal/view/consultas/nfce/consultanfce.seam";
        $mListaWS['PE']['1']['4.00']="www.receita.pb.gov.br/nfce";
        $mListaWS['PB']['1']['4.00']="http://www.fazenda.pr.gov.br";
        $mListaWS['PR']['1']['4.00']="http://www.fazenda.pr.gov.br";
        $mListaWS['PI']['1']['4.00']="http://webas.sefaz.pi.gov.br/nfceweb/consultarNFCe.jsf";
        $mListaWS['RJ']['1']['4.00']="www.nfce.fazenda.rj.gov.br/consulta";
        $mListaWS['RN']['1']['4.00']="http://nfce.set.rn.gov.br/consultarNFCe.aspx";
        $mListaWS['RS']['1']['4.00']="https://www.sefaz.rs.gov.br/NFCE/NFCE-COM.aspx";
        $mListaWS['RO']['1']['4.00']="http://www.nfce.sefin.ro.gov.br";
        $mListaWS['RR']['1']['4.00']="https://www.sefaz.rr.gov.br/nfce/servlet/wp_consulta_nfce";
        $mListaWS['SE']['1']['4.00']="http://www.nfce.se.gov.br/portal/portalNoticias.jsp";
        $mListaWS['SP']['1']['4.00']="https://www.nfce.fazenda.sp.gov.br/NFCeConsultaPublica";
        $mListaWS['TO']['1']['4.00']="http://apps.sefaz.to.gov.br/portal-nfce/consultarNFCe.jsf";

        // HOMOLOGAÇÃO
        $mListaWS['AC']['2']['4.00']="http://hml.sefaznet.ac.gov.br/nfce/consulta";
        $mListaWS['AL']['2']['4.00']="http://nfce.sefaz.al.gov.br/consultaNFCe.htm";
        $mListaWS['AP']['2']['4.00']="https://www.sefaz.ap.gov.br/sate1/seg/SEGf_AcessarFuncao.jsp?cdFuncao=FIS_1261";
        $mListaWS['AM']['2']['4.00']="homnfce.sefaz.am.gov.br/nfceweb/formConsulta.do";
        $mListaWS['BA']['2']['4.00']="http://hnfe.sefaz.ba.gov.br/servicos/nfce/default.aspxA";
        $mListaWS['CE']['2']['4.00']="http://nfceh.sefaz.ce.gov.br/pages/consultaNota.jsf";
        $mListaWS['DF']['2']['4.00']="http://dec.fazenda.df.gov.br/NFCE/";
        $mListaWS['ES']['2']['4.00']="http://homologacao.sefaz.es.gov.br/ConsultaNFCe";
        $mListaWS['MA']['2']['4.00']="http://www.hom.nfce.sefaz.ma.gov.br/portal/consultarNFCe.jsp";
        $mListaWS['MT']['2']['4.00']="http://homologacao.sefaz.mt.gov.br/nfce/consultanfce";
        $mListaWS['MS']['2']['4.00']="http://www.dfe.ms.gov.br/nfce";
        $mListaWS['PA']['2']['4.00']="https://appnfc.sefa.pa.gov.br/portal-homologacao/view/consultas/nfce/consultanfce.seam";
        $mListaWS['PB']['2']['4.00']="http://www.receita.pb.gov.br/nfcehom";
        $mListaWS['PR']['2']['4.00']="http://www.fazenda.pr.gov.br";
        $mListaWS['PI']['2']['4.00']="http://webas.sefaz.pi.gov.br/nfceweb-homologacao/consultarNFCe.jsf";
        $mListaWS['RJ']['2']['4.00']="www.nfce.fazenda.rj.gov.br/consulta";
        $mListaWS['RN']['2']['4.00']="http://nfce.set.rn.gov.br/consultarNFCe.aspx";
        $mListaWS['RS']['2']['4.00']="https://www.sefaz.rs.gov.br/NFCE/NFCE-COM.aspx";
        $mListaWS['RO']['2']['4.00']="http://www.nfce.sefin.ro.gov.br";
        $mListaWS['RR']['2']['4.00']="http://200.174.88.103:8080/nfce/servlet/wp_consulta_nfce";
        $mListaWS['SP']['2']['4.00']="https://www.homologacao.nfce.fazenda.sp.gov.br/NFCeConsultaPublica";
        $mListaWS['SE']['2']['4.00']="http://www.hom.nfe.se.gov.br/portal/portalNoticias.jsp";
        $mListaWS['TO']['2']['4.00']="http://apps.sefaz.to.gov.br/portal-nfce-homologacao/consultarNFCe.jsf";*/

        

        // Produção
        // CONSULTA VERSÃO 2.00
        $mListaWS['BA']['1']['4.00']="http://www.sefaz.ba.gov.br/nfce/consulta";
        $mListaWS['PB']['1']['4.00']="www.receita.pb.gov.br/nfce/consulta";
        $mListaWS['SP']['1']['4.00']="https://www.nfce.fazenda.sp.gov.br/consulta";
        $mListaWS['AC']['1']['4.00']="www.sefaznet.ac.gov.br/nfce/consulta";
        $mListaWS['AL']['1']['4.00']="www.sefaz.al.gov.br/nfce/consulta";
        $mListaWS['AP']['1']['4.00']="www.sefaz.ap.gov.br/nfce/consulta";
        $mListaWS['AM']['1']['4.00']="www.sefaz.am.gov.br/nfce/consulta";
        $mListaWS['BA']['1']['4.00']="www.sefaz.ce.gov.br/nfce/consulta";
        $mListaWS['CE']['1']['4.00']="www.fazenda.df.gov.br/nfce/consulta";
        $mListaWS['DF']['1']['4.00']="http://dec.fazenda.df.gov.br/NFCE/";
        $mListaWS['ES']['1']['4.00']="www.sefaz.es.gov.br/nfce/consulta";
        $mListaWS['GO']['1']['4.00']="www.sefaz.go.gov.br/nfce/consulta";
        $mListaWS['MA']['1']['4.00']="www.sefaz.ma.gov.br/nfce/consulta";
        $mListaWS['MT']['1']['4.00']="www.sefaz.mt.gov.br/nfce/consulta";
        $mListaWS['MS']['1']['4.00']="www.dfe.ms.gov.br/nfce/consulta";
        $mListaWS['MG']['1']['4.00']="www.fazenda.mg.gov.br/nfce/consulta";
        $mListaWS['PA']['1']['4.00']="www.sefa.pa.gov.br/nfce/consulta";
        $mListaWS['PR']['1']['4.00']="http://www.fazenda.pr.gov.br";
        $mListaWS['PE']['1']['4.00']="www.sefaz.pe.gov.br/nfce/consulta";
        $mListaWS['PI']['1']['4.00']="www.sefaz.pi.gov.br/nfce/consulta";
        $mListaWS['RJ']['1']['4.00']="www.fazenda.rj.gov.br/nfce/consulta";
        $mListaWS['RN']['1']['4.00']="www.set.rn.gov.br/nfce/consulta";
        $mListaWS['RS']['1']['4.00']="www.sefaz.rs.gov.br/nfce/consulta";
        $mListaWS['RO']['1']['4.00']="www.sefin.ro.gov.br/nfce/consulta";
        $mListaWS['RR']['1']['4.00']="www.sefaz.rr.gov.br/nfce/consulta";
        $mListaWS['SC']['1']['4.00']="https://sat.sef.sc.gov.br/nfce/consulta";
        $mListaWS['SE']['1']['4.00']="www.sefaz.se.gov.br/nfce/consulta";
        $mListaWS['TO']['1']['4.00']="www.sefaz.to.gov.br/nfce/consulta";

        // Homologação
        $mListaWS['BA']['2']['4.00']="http://hinternet.sefaz.ba.gov.br/nfce/consulta";
        $mListaWS['PB']['2']['4.00']="www.receita.pb.gov.br/nfcehom";
        $mListaWS['SP']['2']['4.00']="https://www.homologacao.nfce.fazenda.sp.gov.br/consulta";
        $mListaWS['AC']['2']['4.00']="www.sefaznet.ac.gov.br/nfce/consulta";
        $mListaWS['AL']['2']['4.00']="www.sefaz.al.gov.br/nfce/consulta";
        $mListaWS['AP']['2']['4.00']="www.sefaz.ap.gov.br/nfce/consulta";
        $mListaWS['AM']['2']['4.00']="www.sefaz.am.gov.br/nfce/consulta";
        $mListaWS['BA']['2']['4.00']="www.sefaz.ce.gov.br/nfce/consulta";
        $mListaWS['CE']['2']['4.00']="www.fazenda.df.gov.br/nfce/consulta";
        $mListaWS['DF']['2']['4.00']="http://dec.fazenda.df.gov.br/NFCE/";
        $mListaWS['ES']['2']['4.00']="www.sefaz.es.gov.br/nfce/consulta";
        $mListaWS['GO']['2']['4.00']="www.sefaz.go.gov.br/nfce/consulta";
        $mListaWS['MA']['2']['4.00']="www.sefaz.ma.gov.br/nfce/consulta";
        $mListaWS['MT']['2']['4.00']="www.sefaz.mt.gov.br/nfce/consulta";
        $mListaWS['MS']['2']['4.00']="www.dfe.ms.gov.br/nfce/consulta";
        $mListaWS['MG']['2']['4.00']="www.fazenda.mg.gov.br/nfce/consulta";
        $mListaWS['PA']['2']['4.00']="www.sefa.pa.gov.br/nfce/consulta";
        $mListaWS['PR']['2']['4.00']="http://www.fazenda.pr.gov.br";
        $mListaWS['PE']['2']['4.00']="www.sefaz.pe.gov.br/nfce/consulta";
        $mListaWS['PI']['2']['4.00']="www.sefaz.pi.gov.br/nfce/consulta";
        $mListaWS['RJ']['2']['4.00']="www.fazenda.rj.gov.br/nfce/consulta";
        $mListaWS['RN']['2']['4.00']="www.set.rn.gov.br/nfce/consulta";
        $mListaWS['RS']['2']['4.00']="www.sefaz.rs.gov.br/nfce/consulta";
        $mListaWS['RO']['2']['4.00']="www.sefin.ro.gov.br/nfce/consulta";
        $mListaWS['RR']['2']['4.00']="www.sefaz.rr.gov.br/nfce/consulta";
        $mListaWS['SC']['2']['4.00']="http://hom.sat.sef.sc.gov.br/nfce/consulta";
        $mListaWS['SE']['2']['4.00']="www.sefaz.se.gov.br/nfce/consulta";
        $mListaWS['TO']['2']['4.00']="www.sefaz.to.gov.br/nfce/consulta";

        $cEstadoEmitente=$this->getUFEmitente();
        $cAmbiente=$this->getIdentAmbiente();
        $cVersao=$this->getVersaoNFe();


        $cRetorno = $mListaWS[$cEstadoEmitente][$cAmbiente][$cVersao];
        return $cRetorno;
    }

    public function removerAcentos($x) {
        $stringSemAcentos = iconv('UTF-8', 'ASCII//TRANSLIT', $x);
        $x = $stringSemAcentos ? $stringSemAcentos : $x;

          log("****************************************** x".$x);

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
          $x = $novoX;
          return $x;

    }
    
    public function adicionarEspape($x)
    {
        $x=trim($x);
        $x = preg_replace("/\s+/", " ",$x);
        
        /*$replace = array(
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
        );*/

        //$x = str_replace(array_keys($replace), $replace, $x);

        /*if ((mb_detect_encoding($x)=='UTF-8') && (!mb_detect_encoding($x,'UTF-8',true))) {     
           // $x = utf8_decode($x);
        }*/

       // $x = utf8_decode($x);

        $stringSemAcentos = iconv('UTF-8', 'ASCII//TRANSLIT', $x);
        $x = $stringSemAcentos ? $stringSemAcentos : $x;
 
        //$x = urldecode($x);
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
        
        $x = $novoX;

        
        $cCharPermitido='&ABCDEFGHIJLKYMNOPQRSTUVXWZ 1234567890()[]{}.,+_-:/=%?;!@|$"<>';
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
        $x = str_replace(">","&gt;",$x);
        $x = str_replace('"',"&quot;",$x);
        $x = str_replace("<","&lt;",$x);
        $x = str_replace("'","&#39;",$x);

        return "$x";
    }

    /****************************************************
    *  Validacao do Cnpj ou Cpf da transportadora
    ****************************************************/
    public function validarCnpjTransportadora($x)
    {
        //-- Limpa o Cnpj ou Cpf
        $x=str_replace(array(".","/","-"," "),"",$x);
        $x=$this->adicionarEspape($x);

        if (strlen($x)!=14 && strlen($x)!=11 && !empty($x))
        {
           $this->mErrorMsg[] = "Cnpj ou CPF da transportadora esta incompleto: ".strlen($x)." caracteres";
           $this->errorStatus = true;
        }
        return $x;
    }

    /****************************************************
    *  Validacao do Nome da transportadora
    ****************************************************/
    public function validarNomeTransportadora($x)
    {
        $x=trim($x);
        $x=$this->adicionarEspape($x);

        if (strlen($x)>60)
        {
           $this->mErrorMsg[] = "Nome da transportadora é superior a 60 caracteres: ".strlen($x)." - $x caracteres";
           $this->errorStatus = true;
        }
        return $x;
    }


    /****************************************************
    *  Validacao da IE da transportadora
    ****************************************************/
    public function validarInscricaoEstadualTransportadora($x)
    {
        $x=trim($x);
        $x=$this->adicionarEspape($x);

        if (strlen($x)<2 && !empty($x))
        {
           $this->mErrorMsg[] = "IE da transportadora é inferior a 2 caracteres: ".strlen($x)." - $x caracteres";
           $this->errorStatus = true;
        }
        elseif (strlen($x)>14)
        {
           $this->mErrorMsg[] = "IE da transportadora é superior a 14 caracteres: ".strlen($x)." - $x caracteres";
           $this->errorStatus = true;
        }
        return $x;
    }

    /****************************************************
    *  Validacao da IE da transportadora
    ****************************************************/
    public function validarEnderecoTransportadora($x)
    {
        $x=trim($x);
        $x=$this->adicionarEspape($x);

        if (strlen($x)>60)
        {
           $this->mErrorMsg[] = "Endereço da transportadora é superior a 60 caracteres: ".strlen($x)." - $x caracteres";
           $this->errorStatus = true;
        }
        return $x;
    }

    /****************************************************
    *  Validacao da Cidade da transportadora
    ****************************************************/
    public function validarCidadeTransportadora($x)
    {
        $x=trim($x);
        $x=$this->adicionarEspape($x);

        if (strlen($x)>60)
        {
           $this->mErrorMsg[] = "Cidade da transportadora é superior a 60 caracteres: ".strlen($x)." - $x caracteres";
           $this->errorStatus = true;
        }
        return $x;
    }
    /****************************************************
    *  Validacao do Estado da transportadora
    ****************************************************/
    public function validarEstadoTransportadora($x)
    {
        $x=trim($x);
        $x=$this->adicionarEspape($x);

        if (strlen($x)>2)
        {
           $this->mErrorMsg[] = "Estado da transportadora é superior a 2 caracteres: ".strlen($x)." - $x caracteres";
           $this->errorStatus = true;
        }
        return $x;
    }
    /****************************************************
    *  Validacao da observacao ao contribuinte
    ****************************************************/
    public function validarObsContribuinte($x)
    {
        $x=trim($x);
        $x=$this->adicionarEspape($x);

        if (strlen($x)>5000)
        {
           $this->mErrorMsg[] = "Observação ao contribuinte é superior a 5000 caracteres: ".strlen($x)." caracteres";
           $this->errorStatus = true;
        }
        //$x=$this->adicionarEspape($x);
        return $x;
    }
    /****************************************************
    *  Validacao da observacao ao fisco
    ****************************************************/
    public function validarObsFisco($x)
    {
        $x=trim($x);
        $x=$this->adicionarEspape($x);

        if (strlen($x)>5000)
        {
           $this->mErrorMsg[] = "Observação fiscal é superior a 5000 caracteres: ".strlen($x)." caracteres";
           $this->errorStatus = true;
        }
        $x=$this->adicionarEspape($x);
        return $x;
    }

    
    /****************************************************
    *  Digit Value
    ****************************************************/
    public function setDigestValue($x)
    {
        $this->digest_value=$x;
    }
    public function getDigestValue()
    {
        return $this->digest_value;
    }

    /****************************************************
    *  MOTIVO DO CANCELAMENTO DA NOTA FISCAL
    ****************************************************/
    public function setMotivoCancelamento($x)
    {
        $x=$this->adicionarEspape($x);
        $this->motivo_cancelamento=$x;
    }
    public function getMotivoCancelamento()
    {
        if (strlen($this->motivo_cancelamento)<15)
        {
           $this->mErrorMsg[] = "Justificativa de cancelamento deve ter mais de 15 caracteres";
           $this->errorStatus = true;
        }
        elseif (strlen($this->motivo_cancelamento)>255)
        {
           $this->mErrorMsg[] = "Caracteres excedidos para justificativa de cancelamento. Ultrapassou 255 caracteres";
           $this->errorStatus = true;
        }
        return $this->motivo_cancelamento;
    }


    /****************************************************
    *  Versao do Sistema
    ****************************************************/
    public function setVersaoSistema($x)
    {
        $this->versao_sistema=$this->adicionarEspape($x);
    }
    public function getVersaoSistema()
    {
        if (empty($this->versao_sistema))
        {
           $this->mErrorMsg[] = "(verProc-B27) Não foi definida a versao do aplicativo emissor";
           $this->errorStatus = true;
        }
        if (strlen($this->versao_sistema)>20)
        {
           $this->mErrorMsg[] = "(verProc-B27) Tamanho da versao do aplicativo emissor ultrapassou 20 caracteres";
           $this->errorStatus = true;
        }

        return $this->versao_sistema;
    }

    /***************************************************
    *  Versao da NFe
    ***************************************************/
    public function setVersaoNFe($x)
    {
        $this->versao_nfe=$x;
    }
    public function getVersaoNFe()
    {
        if (empty($this->versao_nfe))
        {
           $this->mErrorMsg[] = "(versao-A02) Não foi definida a versao da nota fiscal";
           $this->errorStatus = true;
        }
        if (strlen($this->versao_nfe)>4)
        {
           $this->mErrorMsg[] = "(versao-A02) Tamanho do campo da versao do leiaute ultrapassou a 4 caracteres ";
           $this->errorStatus = true;
        }
        if (substr($this->versao_nfe,-2)=='.')
        {
           $this->mErrorMsg[] = "(versao-A02) Campo da versao do leiaute deve ter duas casas decimais separado por ponto ";
           $this->errorStatus = true;
        }
        return $this->versao_nfe;
    }
    
    /****************************************************
    *  LOCALIZACAO DO ARQUIVO PEM
    ****************************************************/
     public function setCertificadoPEM($x)
     {
        if (!file_exists($x))
        {
           //$this->mErrorMsg[] = "Certificado não existe: $x";
           //$this->errorStatus = true;
        }
        $this->certificado_pem=$x;
     }
     public function getCertificadoPEM()
     {
        return $this->certificado_pem;
     }

    /****************************************************
    *  LOCALIZACAO DO ARQUIVO PEM HARDNESS
    ****************************************************/
     public function setCertificadoHardness($x)
     {
        if (!file_exists($x))
        {
           //$this->mErrorMsg[] = "Certificado não existe: $x";
           //$this->errorStatus = true;
        }
        $this->certificado_hardness=$x;
     }
     public function getCertificadoHardness()
     {
        return $this->certificado_hardness;
     }

    /****************************************************
    *  Pasta do Arquivo XML
    ****************************************************/
    public function setPastaArquivoXML($x)
    {
        //-- Cria pasta caso a mesma não exista
        if (!is_dir($x)) {
            mkdir($x, 0777);
            chmod($x,0777);
        }
        if (substr($x,-1)!='/') $x.='/';
        $this->pasta_arquivo_xml=$x;
    }
    public function getPastaArquivoXML()
    {
        return $this->pasta_arquivo_xml;
    }
    
    //-- ARQUIVO ASSINADO
    public function setArquivoAssinado($x)
    {
       $this->arquivo_assinado=$x;
    }
    public function getArquivoAssinado()
    {
       return $this->arquivo_assinado;
    }

    //-- ARQUIVO AUTORIZADO
    public function setArquivoAutorizado($x)
    {
       $this->arquivo_autorizado=$x;
    }
    public function getArquivoAutorizado()
    {
       return $this->arquivo_autorizado;
    }
    /****************************************************
    *  Código numérico que compõe a Chave de Acesso.
    *  Número aleatório gerado pelo emitente para cada NF-e para evitar acessos indevidos da NF-e.
    ****************************************************/
    public function setNumeroAleatorioChaveAcesso($x='')
    {
        if ($this->versao_nfe=='1.10')
        {
            $nTamanhoAleatorio=9;
            if (empty($x))
            {
               $x=rand(100000000,999999999);
            }
        }
        elseif ($this->versao_nfe=='2.00' || $this->versao_nfe=='3.10' || $this->versao_nfe=='4.00')
        {
            $nTamanhoAleatorio=8;
            if (empty($x))
            {
               $x=rand(10000000,99999999);
            }
        }
        else
        {
           $this->mErrorMsg[] = "Versao nao foi definida em setNumeroAleatorioChaveAcesso()";
           $this->errorStatus = true;
        }

        $this->numero_aleatorio_chave_acesso=str_pad($x,$nTamanhoAleatorio,'0',STR_PAD_LEFT);
        
    }
    public function getNumeroAleatorioChaveAcesso()
    {
        if (empty($this->numero_aleatorio_chave_acesso))
        {
           $this->mErrorMsg[] = "(cNF-B03) Codigo numerico que compoe a chave de acesso nao foi definido";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_aleatorio_chave_acesso)>9)
        {
           $this->mErrorMsg[] = "(cNF-B03) Codigo numerico que compoe a chave de acesso ultrapassou 9 caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_aleatorio_chave_acesso;
    }



    /****************************************************
    *  Chave de Acesso da NF-e
    ****************************************************/
    public function setChaveAcessoNFe($chaveAcesso = '')
    {
        if(!empty($chaveAcesso)) {
            $cNF = substr($chaveAcesso,35,8);
            $cDV = substr($chaveAcesso,-1);
            $cChaveAcesso  = "NFe" . $chaveAcesso;

            $this->dv_chave_acesso = $cDV;
            $this->numero_aleatorio_chave_acesso = $cNF;
            $this->chave_acesso_nfe = $cChaveAcesso;
            return;
        }
        

        /****************************************************************************************************************************
        *  VERSAO 1.10
        *  Chave de Acesso da NF-e composta por
        *  Código da UF + AAMM da emissão + CNPJ do Emitente + Modelo + Série + Número da NFe + Código Numérico + DV.
        *       02      +       04        +        14        +   02   +  03           09      +       09        + 01
        *****************************************************************************************************************************/
        
        /****************************************************************************************************************************
        *  VERSAO 2.00
        *  Chave de Acesso da NF-e composta por
        *  Código da UF + AAMM da emissão + CNPJ do Emitente + Modelo + Série + Número da NFe + Forma Emissao + Código Numérico + DV.
        *       02      +       04        +        14        +   02   +  03           09      +       01      +       08        + 01
        *****************************************************************************************************************************/

        $cAnoMesEmissao = substr($this->getDataEmissao(),2,2).substr($this->getDataEmissao(),5,2);

        $cChaveAcesso  = $this->getCodigoUFEmitente();
        $cChaveAcesso .= $cAnoMesEmissao;
        $cChaveAcesso .= $this->getCnpjEmitente();

        $modeloNF      = $this->getModeloNF();
        $cChaveAcesso .= $modeloNF;

        $cChaveAcesso .= STR_PAD($this->getSerieNF(),3,'0',STR_PAD_LEFT);

        $numeroNF      = $this->getNumeroNotaFiscal();
        $cChaveAcesso .= STR_PAD($numeroNF,9,'0',STR_PAD_LEFT);

        $comparacaoChaveAcesso = $cChaveAcesso;

        if ($this->versao_nfe!='1.10')
        {
            $cChaveAcesso .= STR_PAD($this->getTipoDocAuxiliar(),1,'0',STR_PAD_LEFT);
            $cChaveAcesso .= STR_PAD($this->getNumeroAleatorioChaveAcesso(),8,'0',STR_PAD_LEFT);
        }
        else
        {
            $cChaveAcesso .= STR_PAD($this->getNumeroAleatorioChaveAcesso(),9,'0',STR_PAD_LEFT);
        }
        
        $this->setDVChaveAcesso($this->criarDVChaveAcesso($cChaveAcesso));

        $cChaveAcesso .= $this->getDVChaveAcesso();

        // Verifica se pode utilizar a útlima chave gerada
        /*
        $C004_Id      = $this->getC004Id();

        $T127 = mysqli_query("SELECT T127_Chave_Acesso_Nfe, T127_Numero_Nota_Fiscal, T127_Codigo_Retorno FROM T127 WHERE T127_Numero_Nota_Fiscal='{$numeroNF}' and T127_Modelo = '{$modeloNF}' and T127_C004_Id='{$C004_Id}' ORDER BY T127_Id desc LIMIT 1");
        $mT127 = mysql_fetch_array($T127);

        if (!empty($mT127['T127_Chave_Acesso_Nfe']) && $mT127['T127_Numero_Nota_Fiscal']==$numeroNF && substr($mT127['T127_Chave_Acesso_Nfe'], 0, 34)==$comparacaoChaveAcesso && $mT127['T127_Codigo_Retorno']!=502){
           $cChaveAcesso = $mT127['T127_Chave_Acesso_Nfe'];
        }
        */
        // fim
        
        $cChaveAcesso  = "NFe" . $cChaveAcesso;
        
        $this->chave_acesso_nfe = $cChaveAcesso;
        
    }
    public function getChaveAcessoNFe()
    {
        return $this->chave_acesso_nfe;
    }
    
    /****************************************************
    *  Digito verificado da Chave de Acesso da NF-e
    ****************************************************/
    public function setDVChaveAcesso($x)
    {
        $this->dv_chave_acesso=$x;
    }
    public function getDVChaveAcesso()
    {
        if (strlen($this->dv_chave_acesso)==0)
        {
            $this->mErrorMsg[] = "(cDV-B23) Digito verificado da chave de acesso não foi encontrado";
            $this->errorStatus = true;
        }
        if (strlen($this->dv_chave_acesso)>1)
        {
            $this->mErrorMsg[] = "(cDV-B23) Digito verificado da chave de acesso ultrapassou 1 caracter";
            $this->errorStatus = true;
        }
        return $this->dv_chave_acesso;
    }
    public function criarDVChaveAcesso($xChaveAcesso)
    {
        //-- Cria o digito verificador da chave de acesso
        $nTamanhoChave=strlen($xChaveAcesso);
        if ($nTamanhoChave!=43)
        {
           $this->mErrorMsg[] = "Chave de acesso divergente de 43 caracteres: $xChaveAcesso ($nTamanhoChave caracteres)";
           $this->errorStatus = true;
        }

        $mPesos=explode(",","2,3,4,5,6,7,8,9");
        $nSomaPonderada = 0;
        $nTotalPesos=strlen($xChaveAcesso)-1;
        $ny=0;
        for ($x = $nTotalPesos; $x >=0 ; $x--)
        {
            $np=$ny%8;
            $ny++;
            $caractere = strtoupper(substr($xChaveAcesso,$x,1));
            $nPonderacao = ord($caractere) - 48;
            $nSomaPonderada += $mPesos[$np]*$nPonderacao;
        }
        $nResto=$nSomaPonderada%11;

        if ($nResto<=1)
        {
           $nDV=0;
        }
        else
        {
           $nDV=11-$nResto;
        }
        return $nDV;
    }
    
    /****************************************************
    *  Serie da nota fiscal
    ****************************************************/
    public function setSerieNF($x)
    {
        $this->serie_nota_fiscal=$x;
    }
    public function getSerieNF()
    {
        if (empty($this->serie_nota_fiscal))
        {
           $this->mErrorMsg[] = "(serie-B07) Não foi definido a serie da nota fiscal";
           $this->errorStatus = true;
        }
        if (strlen($this->serie_nota_fiscal)>3)
        {
           $this->mErrorMsg[] = "(serie-B07) Serie da nota fiscal ultrapassou 3 caracteres";
           $this->errorStatus = true;
        }
        return $this->serie_nota_fiscal;
    }


    /****************************************************
    *  Modelo da nota fiscal
    ****************************************************/
    public function setModeloNF($x)
    {
        $this->modelo_nota_fiscal=$x;
    }
    public function getModeloNF()
    {
        if (empty($this->modelo_nota_fiscal))
        {
           $this->mErrorMsg[] = "(mod-B06) Não foi definido o modelo da nota fiscal";
           $this->errorStatus = true;
        }
        if (strlen($this->modelo_nota_fiscal)>2)
        {
           $this->mErrorMsg[] = "(mod-B06) Modelo da nota fiscal ultrapassou 2 caracteres";
           $this->errorStatus = true;
        }
        return $this->modelo_nota_fiscal;
    }
    
    /****************************************************
    *  Data da emissao da nota fiscal
    ****************************************************/
    public function setDataEmissao($x)
    {
        $this->data_emissao=$x;
    }
    public function getDataEmissao()
    {
        if (empty($this->data_emissao))
        {
           $this->mErrorMsg[] = "(dEmis-B09) Não foi definida a data de emissao da nota fiscal";
           $this->errorStatus = true;
        }
        if (strlen($this->data_emissao)>25)
        {
           $this->mErrorMsg[] = "(dEmis-B09) Campo data de emissao da nota fiscal ultrapassou 25 caracteres " . $this->data_emissao;
           $this->errorStatus = true;
        }
        return $this->data_emissao;
    }
    
    /****************************************************
    *  Data da emissao da nota fiscal
    ****************************************************/
    public function setDataSaida($x)
    {
        $this->data_saida=$x;
    }
    public function getDataSaida()
    {
        if (empty($this->data_saida))
        {
           $this->mErrorMsg[] = "(dSaiEnt-B10) Não foi definida data de saida da nota fiscal";
           $this->errorStatus = true;
        }
        if (strlen($this->data_saida)>25)
        {
           $this->mErrorMsg[] = "(dSaiEnt-B10) Campo data de saida da nota fiscal ultrapassou 25 caracteres";
           $this->errorStatus = true;
        }
        return $this->data_saida;
    }

    /****************************************************
    *  Nome do Arquivo XML
    ****************************************************/
    public function getNomeArquivoXML()
    {
        if (!is_dir($this->getPastaArquivoXML()."assinado/")) {
            mkdir($this->getPastaArquivoXML() . "assinado/", 0777);
            chmod($this->getPastaArquivoXML() . "assinado/", 0777);
        }
        $cArquivo_NFe = $this->getPastaArquivoXML()."assinado/".$this->getNumeroNotaFiscal().'_'.$this->getSerieNF().'.xml';
        return $cArquivo_NFe;
    }

    /****************************************************
    *  Numero da Nota Fiscal
    ****************************************************/
    public function setNumeroNotaFiscal($x)
    {
        $this->numero_nota_fiscal=$this->adicionarEspape($x);
    }
    public function getNumeroNotaFiscal()
    {
        if (strlen($this->numero_nota_fiscal)<1)
        {
           $this->mErrorMsg[] = "(nNF-B08) Tamanho do Número da nota fiscal é menor que 1 caracter";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_nota_fiscal)>9)
        {
           $this->mErrorMsg[] = "(nNF-B08) Tamanho do Número da nota fiscal é maior que 9 caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_nota_fiscal;
    }

    /****************************************************
    *  Descricao da natureza de operacao
    ****************************************************/
    public function setDescNatOperacao($x)
    {
        $this->descricao_natureza_operacao=$this->adicionarEspape($x);
    }
    public function getDescNatOperacao()
    {
        if (empty($this->descricao_natureza_operacao))
        {
           $this->mErrorMsg[] = "(natOp-B04) Não foi definido a descricao da natureza da operacao";
           $this->errorStatus = true;
        }
        if (strlen($this->descricao_natureza_operacao)>60)
        {
           $this->mErrorMsg[] = "(natOp-B04) Descricao da natureza da operacao ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->descricao_natureza_operacao;
    }

    /****************************************************************
    *  Descrição do pagamento (em casos de tipo pagamento 99- outros)
    ****************************************************************/
    public function setDescricaoMeioPagamento($x)
    {
        $x=$this->adicionarEspape($x);
        $this->descricao_meio_pagamento=$x;
    }
    public function getDescricaoMeioPagamento()
    {
        if (strlen($this->descricao_meio_pagamento)<2)
        {
           $this->mErrorMsg[] = "Descrição do meio de pagamento é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->descricao_meio_pagamento)>60)
        {
           $this->mErrorMsg[] = "Descrição do meio de pagamento é maior que 60 caracteres: ".strlen($this->descricao_meio_pagamento)." caracteres";
           $this->errorStatus = true;
        }
        return $this->descricao_meio_pagamento;
    }
    
    /****************************************************
    *  Indicador da forma de pagamento
    *  0 – pagamento à vista; 1 – pagamento à prazo; 2 - outros.
    ****************************************************/
    public function setFormaPagamento($x)
    {
        $x=$this->adicionarEspape($x);
        $this->forma_pagamento=$x;
    }
    public function getFormaPagamento()
    {
        if (empty($this->forma_pagamento))
        {
           $this->mErrorMsg[] = "(indPag-B05) Não foi definido o indicador da forma de pagamento";
           $this->errorStatus = true;
        }
        if (strlen($this->forma_pagamento)>1)
        {
           $this->mErrorMsg[] = "(indPag-B05) Forma de pagamento ultrapassou 1 caractere";
           $this->errorStatus = true;
        }
        return $this->forma_pagamento;
    }

    /****************************************************
    *  Identificação do Ambiente: 1-Produção / 2-Homologação
    ****************************************************/
    public function setIdentAmbiente($x)
    {
        $this->identificacao_ambiente=$x;
    }
    public function getIdentAmbiente()
    {
        if ($this->identificacao_ambiente!=1 && $this->identificacao_ambiente!=2)
        {
            $this->mErrorMsg[] = "(tpAmb-B024) Identificação do ambiente é inválida: ".$this->identificacao_ambiente;
            $this->errorStatus = true;
        }
        return $this->identificacao_ambiente;
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
        if ($this->tipo_documento_auxiliar!=1 && $this->tipo_documento_auxiliar!=2 && $this->tipo_documento_auxiliar!=3 && $this->tipo_documento_auxiliar!=4 && $this->tipo_documento_auxiliar!=5 && $this->tipo_documento_auxiliar!=6 && $this->tipo_documento_auxiliar!=7 && $this->tipo_documento_auxiliar!=9)
        {
            $this->mErrorMsg[] = "(tpEmis-B22) Documento Auxiliar de Documento Fiscal Eletrônico é inválido: ".$this->tipo_documento_auxiliar;
            $this->errorStatus = true;
        }
        return $this->tipo_documento_auxiliar;
    }

    /****************************************************
    *  Formato da impressao do DANFE
    ****************************************************/
    public function setTipoDANFE($x)
    {
        $this->tipo_danfe=$x;
    }
    public function getTipoDANFE()
    {
        if ($this->tipo_danfe!=1 && $this->tipo_danfe!=2 && $this->tipo_danfe!=4)
        {
            $this->mErrorMsg[] = "(tpImp-B21) Formato da impressao do DANFE é inválido: ".$this->tipo_danfe;
            $this->errorStatus = true;
        }
        return $this->tipo_danfe;
    }

    /****************************************************
    *  Finalidade da Nota
    ****************************************************/
    public function setFinalidadeNFe($x)
    {
        $this->finalidade_nfe=$x;
    }
    public function getFinalidadeNFe()
    {
        if ($this->finalidade_nfe!=1 && $this->finalidade_nfe!=2 && $this->finalidade_nfe!=3 && $this->finalidade_nfe!=4 && $this->finalidade_nfe!=5 && $this->finalidade_nfe!=6)
        {
            $this->mErrorMsg[] = "(finNFe-B025) finalidade da nota fiscal eletronica é inválida: ".$this->finalidade_nfe;
            $this->errorStatus = true;
        }
        return $this->finalidade_nfe;
    }

    /****************************************************
    *  Processo de emissão da NF-e
    ****************************************************/
    public function setOrigemProcessoEmissao($x)
    {
        $this->origem_processo_emissao=$x;
    }
    public function getOrigemProcessoEmissao()
    {
        if ($this->origem_processo_emissao!=0 && $this->origem_processo_emissao!=1 && $this->origem_processo_emissao!=2 && $this->origem_processo_emissao!=3)
        {
            $this->mErrorMsg[] = "(procEmi-B26) Processo de emissão da NF-e é inválido: ".$this->origem_processo_emissao;
            $this->errorStatus = true;
        }
        return $this->origem_processo_emissao;
    }





    /****************************************************
    *  cnpj do Emitente
    ****************************************************/
    public function setCnpjEmitente($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_emitente=$x;
    }
    public function getCnpjEmitente()
    {
        if (empty($this->cnpj_emitente))
        {
           //$this->mErrorMsg[] = "(CNPJ-C02) Não foi definido o cnpj do emitente";
           //$this->errorStatus = true;
        }
        if (strlen($this->cnpj_emitente)!=14)
        {
           //$this->mErrorMsg[] = "(CNPJ-C02) Cnpj do emitente esta incompleto: ".strlen($this->cnpj_emitente)." caracteres";
           //$this->errorStatus = true;
        }
        return $this->cnpj_emitente;
    }

    /****************************************************
    *  Codigo da UF do Emitente
    ****************************************************/
    public function setCodigoUFEmitente($x)
    {
        $this->codigo_uf_emitente=$x;
    }
    public function getCodigoUFEmitente()
    {
        if (empty($this->codigo_uf_emitente))
        {
           $this->mErrorMsg[] = "(cUF-B02) Não foi definido o codigo da unidade federativa do emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->codigo_uf_emitente)>2)
        {
           $this->mErrorMsg[] = "(cUF-B02) Tamanho do codigo da unidade federativa do emitente ultrapassou 2 caracteres";
           $this->errorStatus = true;
        }
        return $this->codigo_uf_emitente;
    }

    /****************************************************
    * Codigo do municipio para o emitente
    ****************************************************/
    public function setCodigoMunicipioEmitente($x)
    {
        $this->codigo_municipio_emitente=$x;
    }
    public function getCodigoMunicipioEmitente()
    {
        if (empty($this->codigo_municipio_emitente))
        {
           $this->mErrorMsg[] = "(cMunFG-B12) (cMun-C10) Não foi definido o codigo do municipio para o emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->codigo_municipio_emitente)>7)
        {
           $this->mErrorMsg[] = "(cMunFG-B12) (cMun-C10) Codigo do municipio para o emitente ultrapassou 7 caracteres";
           $this->errorStatus = true;
        }
        return $this->codigo_municipio_emitente;
    }

    /****************************************************
    *  Razao social do emitente
    ****************************************************/
    public function setNomeRazaoEmitente($x)
    {
        $this->razao_social_emitente=$this->adicionarEspape($x);
    }
    public function getNomeRazaoEmitente()
    {
        if (empty($this->razao_social_emitente))
        {
           $this->mErrorMsg[] = "Não foi definido o nome / razao social do emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->razao_social_emitente)>60)
        {
           $this->mErrorMsg[] = "Razão social do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->razao_social_emitente;
    }

    /****************************************************
    *  Nome fantasia social do emitente
    ****************************************************/
    public function setNomeFantasiaEmitente($x)
    {
        $this->nome_fantasia_emitente=$this->adicionarEspape($x);
    }
    public function getNomeFantasiaEmitente()
    {
        if (strlen($this->nome_fantasia_emitente)>60)
        {
           $this->mErrorMsg[] = "(xFant-C04) Nome fantasia do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->nome_fantasia_emitente;
    }

    /****************************************************
    *  Logradouro do emitente
    ****************************************************/
    public function setLogradouroEmitente($x)
    {
        $this->logradouro_emitente=$this->adicionarEspape($x);
    }
    public function getLogradouroEmitente()
    {
        if (strlen($this->logradouro_emitente)<2)
        {
           $this->mErrorMsg[] = "(xLgr-C06) Logradouro do emitente é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->logradouro_emitente)>60)
        {
           $this->mErrorMsg[] = "(xLgr-C06) Logradouro do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->logradouro_emitente;
    }

    /****************************************************
    *  Numero do logradouro do emitente
    ****************************************************/
    public function setNumeroLogradouroEmitente($x)
    {
        $this->numero_logradouro_emitente=$this->adicionarEspape($x);
    }
    public function getNumeroLogradouroEmitente()
    {
        if (empty($this->numero_logradouro_emitente))
        {
           $this->mErrorMsg[] = "(nro-C07) Não foi definido o numero do logradouro do emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_logradouro_emitente)>60)
        {
           $this->mErrorMsg[] = "(nro-C07) Numero do logradouro do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_logradouro_emitente;
    }
    
    /****************************************************
    *  Complemento do logradouro do emitente
    ****************************************************/
    public function setComplementoEmitente($x)
    {
        $this->complemento_logradouro_emitente=$this->adicionarEspape($x);
    }
    public function getComplementoEmitente()
    {
        if (strlen($this->complemento_logradouro_emitente)>60)
        {
           $this->mErrorMsg[] = "3(xCpl-C08) Complemento do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->complemento_logradouro_emitente;
    }

    /****************************************************
    *  Bairro do emitente
    ****************************************************/
    public function setBairroEmitente($x)
    {
        $this->bairro_emitente=$this->adicionarEspape($x);
    }
    public function getBairroEmitente()
    {
        if (strlen($this->bairro_emitente)<2)
        {
           $this->mErrorMsg[] = "(xBairro-C09) Bairro do emitente é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->bairro_emitente)>60)
        {
           $this->mErrorMsg[] = "(xBairro-C09) Bairro do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->bairro_emitente;
    }

    /****************************************************
    *  Nome do Municipio do emitente
    ****************************************************/
    public function setMunicipioEmitente($x)
    {
        $this->municipio_emitente=$this->adicionarEspape($x);
    }
    public function getMunicipioEmitente()
    {
        if (strlen($this->municipio_emitente)<2)
        {
           $this->mErrorMsg[] = "(xMun-C11) Municipio do emitente é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->municipio_emitente)>60)
        {
           $this->mErrorMsg[] = "(xMun-C11) Municipio do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->municipio_emitente;
    }

    /****************************************************
    *  Sigla da UF do emitente
    ****************************************************/
    public function setUFEmitente($x)
    {
        $this->sigla_uf_emitente=$this->adicionarEspape($x);
    }
    public function getUFEmitente()
    {
        if (empty($this->sigla_uf_emitente))
        {
           $this->mErrorMsg[] = "(UF-C12) Sigla da unidade federativa do emitente nao foi definida";
           $this->errorStatus = true;
        }
        if (strlen($this->sigla_uf_emitente)>2)
        {
           $this->mErrorMsg[] = "(UF-C12) Sigla da unidade federativa do emitente ultrapassou 2 caracteres";
           $this->errorStatus = true;
        }
        return $this->sigla_uf_emitente;
    }
    
    /****************************************************
    *  CEP do emitente
    ****************************************************/
    public function setCEPEmitente($x)
    {
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $x=trim($x);
        $x=$this->adicionarEspape($x);
        $this->cep_emitente=$x;
    }
    public function getCEPEmitente()
    {
        if (strlen($this->cep_emitente)>8)
        {
           $this->mErrorMsg[] = "(CEP-C13) CEP do emitente ultrapassou 8 caracteres";
           $this->errorStatus = true;
        }
        return $this->cep_emitente;
    }

    /****************************************************
    *  Telefone do emitente
    ****************************************************/
    public function setTelefoneEmitente($x)
    {
        $x=str_replace("(","",$x);
        $x=str_replace(")","",$x);
        $x=str_replace(" ","",$x);
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=trim($x);
        $x=$this->adicionarEspape($x);
        $this->telefone_emitente=$x;
    }
    public function getTelefoneEmitente()
    {
        /*
        if (strlen($this->telefone_emitente)>10)
        {
           $this->mErrorMsg[] = "(fone-C16) Telefone do emitente ultrapassou 10 caracteres";
           $this->errorStatus = true;
        }
        */
        return $this->telefone_emitente;
    }

    /****************************************************
    *  Inscricao ESTADUAL do emitente
    ****************************************************/
    public function setInscricaoEstadualEmitente($x)
    {
        $x=str_replace("/","",$x);
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $x=trim($x);
        $x=$this->adicionarEspape($x);
        $this->inscricao_estadual_emitente=$x;
    }
    public function getInscricaoEstadualEmitente()
    {
        if ($this->getOrigemProcessoEmissao()==0 || $this->getOrigemProcessoEmissao()==2 || $this->getOrigemProcessoEmissao()==3)
        {
           if (empty($this->inscricao_estadual_emitente))
           {
              $this->mErrorMsg[] = "(IE-C17) Campo Inscricao Estadual do emitente é obrigatorio";
              $this->errorStatus = true;
           }
        }
        if (strlen($this->inscricao_estadual_emitente)>14)
        {
           $this->mErrorMsg[] = "(IE-C17) Inscricao Estadual do emitente ultrapassou 14 caracteres";
           $this->errorStatus = true;
        }
        return $this->inscricao_estadual_emitente;
    }
    
    /****************************************************
    *  Inscricao ESTADUAL do ST do emitente
    ****************************************************/
    public function setInscricaoEstadualSubTribEmitente($x)
    {
        $x=str_replace("/","",$x);
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $x=trim($x);
        $x=$this->adicionarEspape($x);
        $this->inscricao_estadual_st_emitente=$x;
    }
    public function getInscricaoEstadualSubTribEmitente()
    {
        if (strlen($this->inscricao_estadual_st_emitente)<2)
        {
           $this->mErrorMsg[] = "(IEST-C18) Inscricao Estadual do ST do emitente é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->inscricao_estadual_st_emitente)>14)
        {
           $this->mErrorMsg[] = "(IEST-C18) Inscricao Estadual do ST do emitente ultrapassou 14 caracteres";
           $this->errorStatus = true;
        }
        return $this->inscricao_estadual_st_emitente;
    }

    /****************************************************
    *  Inscricao MUNICIPAL do emitente
    ****************************************************/
    public function setInscricaoMunicipalEmitente($x)
    {
        $x=str_replace("/","",$x);
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $x=trim($x);
        $x=$this->adicionarEspape($x);
        $this->inscricao_municipal_emitente=$x;
    }
    public function getInscricaoMunicipalEmitente()
    {
        if (strlen($this->inscricao_municipal_emitente)>15)
        {
           $this->mErrorMsg[] = "(IM-C19) Inscricao Municipal do emitente ultrapassou 15 caracteres";
           $this->errorStatus = true;
        }
        return $this->inscricao_municipal_emitente;
    }

    /****************************************************
    *  CNAE do emitente
    ****************************************************/
    public function setCNAEEmitente($x)
    {
        $x=str_replace("/","",$x);
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $x=trim($x);
        $x=$this->adicionarEspape($x);
        $this->cnae_emitente=$x;
    }
    public function getCNAEEmitente()
    {
        if (!empty($this->inscricao_municipal_emitente) && empty($this->cnae_emitente))
        {
           $this->mErrorMsg[] = "(CNAE-C20) CNAE do emitente é obrigatorio pois foi informada a Inscrição Municipal";
           $this->errorStatus = true;
        }
        elseif (strlen($this->cnae_emitente)<7)
        {
           $this->mErrorMsg[] = "(CNAE-C20) CNAE do emitente é inferior a 7 caracteres: ".$this->cnae_emitente;
           $this->errorStatus = true;
        }
        elseif (strlen($this->cnae_emitente)>7)
        {
           $this->mErrorMsg[] = "(CNAE-C20) CNAE do emitente ultrapassou 7 caracteres: ".$this->cnae_emitente;
           $this->errorStatus = true;
        }
        return $this->cnae_emitente;
    }

    /****************************************************
    *  cnpj do destinatario
    ****************************************************/
    public function setCnpjCpfDestinatario($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_cpf_destinatario=$x;
    }
    public function getCnpjCpfDestinatario()
    {
        if ($this->D024_Flag_Pessoa_Fisica_Juridica!='I')
        {
            if (empty($this->cnpj_cpf_destinatario))
            {
               //$this->mErrorMsg[] = "Não foi definido o cnpj ou cpf do destinatario";
               //$this->errorStatus = true;
            }
            if (strlen($this->cnpj_cpf_destinatario)!=14 && strlen($this->cnpj_cpf_destinatario)!=11)
            {
               //$this->mErrorMsg[] = "Cnpj/Cpf do destinatário esta incompleto: ".strlen($this->cnpj_cpf_destinatario)." caracteres";
               //$this->errorStatus = true;
            }
        }
        return $this->cnpj_cpf_destinatario;
    }
    
    /****************************************************
    *  razao social do destinatario
    ****************************************************/
    public function setNomeDestinatario($x)
    {
        $x=$this->adicionarEspape($x);
        $this->nome_destinatario=$x;
    }
    public function getNomeDestinatario()
    {
        if (strlen($this->nome_destinatario)<2)
        {
           $this->mErrorMsg[] = "Nome do destinatario é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->nome_destinatario)>60)
        {
           //$this->mErrorMsg[] = "Nome do destinatário é maior que 60 caracteres: ".strlen($this->nome_destinatario)." caracteres";
           //$this->errorStatus = true;
        }
        return $this->nome_destinatario;
    }
    
    /****************************************************
    *  Logradouro do destinatario
    ****************************************************/
    public function setLogradouroDestinatario($x)
    {
        $this->logradouro_destinatario=$this->adicionarEspape($x);
    }
    public function getLogradouroDestinatario()
    {
        //if (strlen($this->logradouro_destinatario)<2)
        //{
        //   $this->mErrorMsg[] = "Logradouro do destinatario é menor que 2 caracteres";
        //   $this->errorStatus = true;
        //}
        //if (strlen($this->logradouro_destinatario)>60)
        //{
        //   $this->mErrorMsg[] = "Logradouro do destinatário é maior que 60 caracteres: ".strlen($this->logradouro_destinatario)." caracteres";
        //   $this->errorStatus = true;
        //}
        return $this->logradouro_destinatario;
    }
    
    /****************************************************
    *  Numero do Logradouro do destinatario
    ****************************************************/
    public function setNumeroLogradouroDestinatario($x)
    {
        $x=$this->adicionarEspape($x);
        $this->numero_logradouro_destinatario=$x;
    }
    public function getNumeroLogradouroDestinatario()
    {
        if (empty($this->numero_logradouro_destinatario))
        {
           $this->mErrorMsg[] = "Não foi informado o numero do logradouro do destinatario";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_logradouro_destinatario)>60)
        {
           $this->mErrorMsg[] = "Numero do logradouro do destinatário é maior que 60 caracteres: ".strlen($this->numero_logradouro_destinatario)." caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_logradouro_destinatario;
    }

    /****************************************************
    *  Complemento do endereco do destinatario
    ****************************************************/
    public function setComplementoDestinatario($x)
    {
        $x=$this->adicionarEspape($x);
        $this->complemento_logradouro_destinatario=$x;
    }
    public function getComplementoDestinatario()
    {
        if (strlen($this->complemento_logradouro_destinatario)>60)
        {
           $this->mErrorMsg[] = "Complemento do destinatário é maior que 60 caracteres: ".strlen($this->complemento_logradouro_destinatario)." caracteres";
           $this->errorStatus = true;
        }
        return $this->complemento_logradouro_destinatario;
    }
    
    /****************************************************
    *  Bairro do destinatario
    ****************************************************/
    public function setBairroDestinatario($x)
    {
        $x=$this->adicionarEspape($x);
        $this->bairro_destinatario=$x;
    }
    public function getBairroDestinatario()
    {
       /* if (empty($this->bairro_destinatario))
        {
           $this->mErrorMsg[] = "Não foi informado o bairro do destinatario";
           $this->errorStatus = true;
        }
        if (strlen($this->bairro_destinatario)>60)
        {
           $this->mErrorMsg[] = "Bairro do destinatário é maior que 60 caracteres: ".strlen($this->bairro_destinatario)." caracteres";
           $this->errorStatus = true;
        }*/
        return $this->bairro_destinatario;
    }

    /****************************************************
    *  Código do municipio do destinatario
    ****************************************************/
    public function setCodigoMunicipioDestinatario($x)
    {
        $this->codigo_municipio_destinatario=$x;
    }
    public function getCodigoMunicipioDestinatario()
    {
        if (empty($this->codigo_municipio_destinatario))
        {
           $this->mErrorMsg[] = "Não foi informado o codigo do municipio do destinatario";
           $this->errorStatus = true;
        }
        if (strlen($this->codigo_municipio_destinatario)>7)
        {
           $this->mErrorMsg[] = "Codigo do municipio do destinatário é maior que 7 caracteres: ".strlen($this->codigo_municipio_destinatario)." caracteres";
           $this->errorStatus = true;
        }
        return $this->codigo_municipio_destinatario;
    }

    /****************************************************
    *  Municipio do destinatario
    ****************************************************/
    public function setMunicipioDestinatario($x)
    {
        $x=$this->adicionarEspape($x);
        $this->municipio_destinatario=$x;
    }
    public function getMunicipioDestinatario()
    {
        if (strlen($this->municipio_destinatario)<2)
        {
           $this->mErrorMsg[] = "Municipio do destinatário é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->municipio_destinatario)>60)
        {
           $this->mErrorMsg[] = "Municipio do destinatário é maior que 60 caracteres: ".strlen($this->municipio_destinatario)." caracteres";
           $this->errorStatus = true;
        }
        return $this->municipio_destinatario;
    }

    /****************************************************
    *  Estado do destinatario
    ****************************************************/
    public function setUFDestinatario($x)
    {
        $x=$this->adicionarEspape($x);
        $this->UF_destinatario=$x;
    }
    public function getUFDestinatario()
    {
        if (strlen($this->UF_destinatario)<2)
        {
           $this->mErrorMsg[] = "UF do destinatário é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->UF_destinatario)>2)
        {
           $this->mErrorMsg[] = "UF do destinatário é maior que 2 caracteres: ".strlen($this->UF_destinatario)." caracteres";
           $this->errorStatus = true;
        }
        return $this->UF_destinatario;
    }


    /****************************************************
    *  Cep do destinatario
    ****************************************************/
    public function setCEPDestinatario($x)
    {
        $x = str_replace("-","",$x);
        $x = str_replace(" ","",$x);
        $x = str_replace(".","",$x);
        $x=$this->adicionarEspape($x);
        $this->cep_destinatario=$x;
    }
    public function getCEPDestinatario()
    {
        if (strlen($this->cep_destinatario)<8 and strlen($this->cep_destinatario)>0)
        {
           $this->mErrorMsg[] = "CEP do destinatário deve ter 8 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->cep_destinatario)>8)
        {
           $this->mErrorMsg[] = "CEP do destinatário é maior que 8 caracteres: ".strlen($this->cep_destinatario)." caracteres";
           $this->errorStatus = true;
        }
        return $this->cep_destinatario;
    }

    /****************************************************
    *  Telefone do destinatario
    ****************************************************/
    public function setTelefoneDestinatario($x)
    {
        $x=$this->adicionarEspape($x);
        $this->telefone_destinatario=$x;
    }
    public function getTelefoneDestinatario()
    {
        /*
        if (empty($this->telefone_destinatario))
        {
           $this->mErrorMsg[] = "Telefone do destinatário não foi informado";
           $this->errorStatus = true;
        }
        */
        $this->telefone_destinatario = str_replace('-', '', $this->telefone_destinatario);
        $this->telefone_destinatario = str_replace('(', '', $this->telefone_destinatario);
        $this->telefone_destinatario = str_replace(')', '', $this->telefone_destinatario);
        /*
        if (strlen($this->telefone_destinatario)>10)
        {
           $this->mErrorMsg[] = "Telefone do destinatário é maior que 10 caracteres: ".strlen($this->telefone_destinatario)." caracteres";
           $this->errorStatus = true;
        }
        */
        return $this->telefone_destinatario;
    }
    
    /****************************************************
    *  Inscricao Estadual do destinatario
    ****************************************************/
    public function setInscricaEstadualDestinatario($x)
    {
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $x=$this->adicionarEspape($x);
        $this->inscricao_estadual_destinatario=$x;
    }
    public function getInscricaEstadualDestinatario()
    {
        if (strlen($this->inscricao_estadual_destinatario)>14)
        {
           $this->mErrorMsg[] = "Inscrição Estadual do destinatário é maior que 14 caracteres: ".strlen($this->inscricao_estadual_destinatario)." caracteres <BR>".$this->inscricao_estadual_destinatario;
           $this->errorStatus = true;
        }
        return $this->inscricao_estadual_destinatario;
    }

    /****************************************************
    *  Inscricao Suframa do destinatario
    ****************************************************/
    public function setInscricaSuframaDestinatario($x)
    {
        $x=$this->adicionarEspape($x);
        $this->inscricao_suframa_destinatario=$x;
    }
    public function getInscricaSuframaDestinatario()
    {
        if (strlen($this->inscricao_suframa_destinatario)>9)
        {
           $this->mErrorMsg[] = "Inscricao do Suframa do destinatário é maior que 9 caracteres: ".strlen($this->inscricao_suframa_destinatario)." caracteres";
           $this->errorStatus = true;
        }
        return $this->inscricao_suframa_destinatario;
    }

    /****************************************************
    *  cnpj do destinatário endereço retira
    ****************************************************/
    public function setCnpjCpfRetira($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_cpf_retira=$x;
    }
    public function getCnpjCpfRetira()
    {
        if ($this->D024_Flag_Pessoa_Fisica_Juridica!='I')
        {
            if (empty($this->cnpj_cpf_retira))
            {
               //$this->mErrorMsg[] = "Não foi definido o cnpj ou cpf do destinatário endereço retira";
               //$this->errorStatus = true;
            }
            if (strlen($this->cnpj_cpf_retira)!=14 && strlen($this->cnpj_cpf_retira)!=11)
            {
               //$this->mErrorMsg[] = "Cnpj/Cpf do destinatário endereço retira esta incompleto: ".strlen($this->cnpj_cpf_retira)." caracteres";
               //$this->errorStatus = true;
            }
        }
        return $this->cnpj_cpf_retira;
    }


    /****************************************************
    *  Logradouro do destinatário endereço retira
    ****************************************************/
    public function setLogradouroRetira($x)
    {
        $this->logradouro_retira=$this->adicionarEspape($x);
    }
    public function getLogradouroRetira()
    {
        //if (strlen($this->logradouro_retira)<2)
        //{
        //   $this->mErrorMsg[] = "Logradouro do destinatário endereço retira é menor que 2 caracteres";
        //   $this->errorStatus = true;
        //}
        //if (strlen($this->logradouro_retira)>60)
        //{
        //   $this->mErrorMsg[] = "Logradouro do destinatário endereço retira é maior que 60 caracteres: ".strlen($this->logradouro_retira)." caracteres";
        //   $this->errorStatus = true;
        //}
        return $this->logradouro_retira;
    }
    
    /****************************************************
    *  Numero do Logradouro do destinatário endereço retira
    ****************************************************/
    public function setNumeroLogradouroRetira($x)
    {
        $x=$this->adicionarEspape($x);
        $this->numero_logradouro_retira=$x;
    }
    public function getNumeroLogradouroRetira()
    {
        if (empty($this->numero_logradouro_retira))
        {
           $this->mErrorMsg[] = "Não foi informado o numero do logradouro do destinatário endereço retira";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_logradouro_retira)>60)
        {
           $this->mErrorMsg[] = "Numero do logradouro do destinatário endereço retira é maior que 60 caracteres: ".strlen($this->numero_logradouro_retira)." caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_logradouro_retira;
    }

    /****************************************************
    *  Complemento do endereco do destinatário endereço retira
    ****************************************************/
    public function setComplementoRetira($x)
    {
        $x=$this->adicionarEspape($x);
        $this->complemento_logradouro_retira=$x;
    }
    public function getComplementoRetira()
    {
        if (strlen($this->complemento_logradouro_retira)>60)
        {
           $this->mErrorMsg[] = "Complemento do destinatário endereço retira é maior que 60 caracteres: ".strlen($this->complemento_logradouro_retira)." caracteres";
           $this->errorStatus = true;
        }
        return $this->complemento_logradouro_retira;
    }
    
    /****************************************************
    *  Bairro do destinatário endereço retira
    ****************************************************/
    public function setBairroRetira($x)
    {
        $x=$this->adicionarEspape($x);
        $this->bairro_retira=$x;
    }
    public function getBairroRetira()
    {
       /* if (empty($this->bairro_retira))
        {
           $this->mErrorMsg[] = "Não foi informado o bairro do destinatário endereço retira";
           $this->errorStatus = true;
        }
        if (strlen($this->bairro_retira)>60)
        {
           $this->mErrorMsg[] = "Bairro do destinatário endereço retira é maior que 60 caracteres: ".strlen($this->bairro_retira)." caracteres";
           $this->errorStatus = true;
        }*/
        return $this->bairro_retira;
    }

    /****************************************************
    *  Código do municipio do destinatário endereço retira
    ****************************************************/
    public function setCodigoMunicipioRetira($x)
    {
        $this->codigo_municipio_retira=$x;
    }
    public function getCodigoMunicipioRetira()
    {
        if (empty($this->codigo_municipio_retira))
        {
           $this->mErrorMsg[] = "Não foi informado o codigo do municipio do destinatário endereço retira";
           $this->errorStatus = true;
        }
        if (strlen($this->codigo_municipio_retira)>7)
        {
           $this->mErrorMsg[] = "Codigo do municipio do destinatário endereço retira é maior que 7 caracteres: ".strlen($this->codigo_municipio_retira)." caracteres";
           $this->errorStatus = true;
        }
        return $this->codigo_municipio_retira;
    }

    /****************************************************
    *  Municipio do destinatário endereço retira
    ****************************************************/
    public function setMunicipioRetira($x)
    {
        $x=$this->adicionarEspape($x);
        $this->municipio_retira=$x;
    }
    public function getMunicipioRetira()
    {
        if (strlen($this->municipio_retira)<2)
        {
           $this->mErrorMsg[] = "Municipio do destinatário endereço retira é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->municipio_retira)>60)
        {
           $this->mErrorMsg[] = "Municipio do destinatário endereço retira é maior que 60 caracteres: ".strlen($this->municipio_retira)." caracteres";
           $this->errorStatus = true;
        }
        return $this->municipio_retira;
    }

    /****************************************************
    *  Estado do destinatário endereço retira
    ****************************************************/
    public function setUFRetira($x)
    {
        $x=$this->adicionarEspape($x);
        $this->UF_retira=$x;
    }
    public function getUFRetira()
    {
        if (strlen($this->UF_retira)<2)
        {
           $this->mErrorMsg[] = "UF do destinatário endereço retira é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->UF_retira)>2)
        {
           $this->mErrorMsg[] = "UF do destinatário endereço retira é maior que 2 caracteres: ".strlen($this->UF_retira)." caracteres";
           $this->errorStatus = true;
        }
        return $this->UF_retira;
    }

    /****************************************************
    *  cnpj do destinatário endereço entrega
    ****************************************************/
    public function setCnpjCpfEntrega($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_cpf_entrega=$x;
    }
    public function getCnpjCpfEntrega()
    {
        if ($this->D024_Flag_Pessoa_Fisica_Juridica!='I')
        {
            if (empty($this->cnpj_cpf_entrega))
            {
               //$this->mErrorMsg[] = "Não foi definido o cnpj ou cpf do destinatário endereço entrega";
               //$this->errorStatus = true;
            }
            if (strlen($this->cnpj_cpf_entrega)!=14 && strlen($this->cnpj_cpf_entrega)!=11)
            {
               //$this->mErrorMsg[] = "Cnpj/Cpf do destinatário endereço entrega esta incompleto: ".strlen($this->cnpj_cpf_entrega)." caracteres";
               //$this->errorStatus = true;
            }
        }
        return $this->cnpj_cpf_entrega;
    }

    /****************************************************
    *  cnpj do intermediador da transação
    ****************************************************/
    public function setCnpjIntermediadorTransacao($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_intermediador_transacao=$x;
    }
    public function getCnpjIntermediadorTransacao()
    {
        return $this->cnpj_intermediador_transacao;
    }

    /****************************************************
    *  cnpj do intermediador do pagamento
    ****************************************************/
    public function setCnpjIntermediadorPagamento($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_intermediador_pagamento=$x;
    }
    public function getCnpjIntermediadorPagamento()
    {
        return $this->cnpj_intermediador_pagamento;
    }

    /****************************************************
    *  Logradouro do destinatário endereço entrega
    ****************************************************/
    public function setLogradouroEntrega($x)
    {
        $this->logradouro_entrega=$this->adicionarEspape($x);
    }
    public function getLogradouroEntrega()
    {
        //if (strlen($this->logradouro_entrega)<2)
        //{
        //   $this->mErrorMsg[] = "Logradouro do destinatário endereço entrega é menor que 2 caracteres";
        //   $this->errorStatus = true;
        //}
        //if (strlen($this->logradouro_entrega)>60)
        //{
        //   $this->mErrorMsg[] = "Logradouro do destinatário endereço entrega é maior que 60 caracteres: ".strlen($this->logradouro_entrega)." caracteres";
        //   $this->errorStatus = true;
        //}
        return $this->logradouro_entrega;
    }
    
    /****************************************************
    *  Numero do Logradouro do destinatário endereço entrega
    ****************************************************/
    public function setNumeroLogradouroEntrega($x)
    {
        $x=$this->adicionarEspape($x);
        $this->numero_logradouro_entrega=$x;
    }
    public function getNumeroLogradouroEntrega()
    {
        if (empty($this->numero_logradouro_entrega))
        {
           $this->mErrorMsg[] = "Não foi informado o numero do logradouro do destinatário endereço entrega";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_logradouro_entrega)>60)
        {
           $this->mErrorMsg[] = "Numero do logradouro do destinatário endereço entrega é maior que 60 caracteres: ".strlen($this->numero_logradouro_entrega)." caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_logradouro_entrega;
    }

    /****************************************************
    *  Complemento do endereco do destinatário endereço entrega
    ****************************************************/
    public function setComplementoEntrega($x)
    {
        $x=$this->adicionarEspape($x);
        $this->complemento_logradouro_entrega=substr($x,0,60);
    }
    public function getComplementoEntrega()
    {
        if (strlen($this->complemento_logradouro_entrega)>60)
        {
           $this->mErrorMsg[] = "Complemento do destinatário endereço entrega é maior que 60 caracteres: ".strlen($this->complemento_logradouro_entrega)." caracteres";
           $this->errorStatus = true;
        }
        return $this->complemento_logradouro_entrega;
    }
    
    /****************************************************
    *  Bairro do destinatário endereço entrega
    ****************************************************/
    public function setBairroEntrega($x)
    {
        $x=$this->adicionarEspape($x);
        $this->bairro_entrega=$x;
    }
    public function getBairroEntrega()
    {
       /* if (empty($this->bairro_entrega))
        {
           $this->mErrorMsg[] = "Não foi informado o bairro do destinatário endereço entrega";
           $this->errorStatus = true;
        }
        if (strlen($this->bairro_entrega)>60)
        {
           $this->mErrorMsg[] = "Bairro do destinatário endereço entrega é maior que 60 caracteres: ".strlen($this->bairro_entrega)." caracteres";
           $this->errorStatus = true;
        }*/
        return $this->bairro_entrega;
    }

    /****************************************************
    *  Código do municipio do destinatário endereço entrega
    ****************************************************/
    public function setCodigoMunicipioEntrega($x)
    {
        $this->codigo_municipio_entrega=$x;
    }
    public function getCodigoMunicipioEntrega()
    {
        if (empty($this->codigo_municipio_entrega))
        {
           $this->mErrorMsg[] = "Não foi informado o codigo do municipio do destinatário endereço entrega";
           $this->errorStatus = true;
        }
        if (strlen($this->codigo_municipio_entrega)>7)
        {
           $this->mErrorMsg[] = "Codigo do municipio do destinatário endereço entrega é maior que 7 caracteres: ".strlen($this->codigo_municipio_entrega)." caracteres";
           $this->errorStatus = true;
        }
        return $this->codigo_municipio_entrega;
    }

    /****************************************************
    *  Municipio do destinatário endereço entrega
    ****************************************************/
    public function setMunicipioEntrega($x)
    {
        $x=$this->adicionarEspape($x);
        $this->municipio_entrega=$x;
    }
    public function getMunicipioEntrega()
    {
        if (strlen($this->municipio_entrega)<2)
        {
           $this->mErrorMsg[] = "Municipio do destinatário endereço entrega é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->municipio_entrega)>60)
        {
           $this->mErrorMsg[] = "Municipio do destinatário endereço entrega é maior que 60 caracteres: ".strlen($this->municipio_entrega)." caracteres";
           $this->errorStatus = true;
        }
        return $this->municipio_entrega;
    }

    /****************************************************
    *  Estado do destinatário endereço entrega
    ****************************************************/
    public function setUFEntrega($x)
    {
        $x=$this->adicionarEspape($x);
        $this->UF_entrega=$x;
    }
    public function getUFEntrega()
    {
        if (strlen($this->UF_entrega)<2)
        {
           $this->mErrorMsg[] = "UF do endereço de entrega é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->UF_entrega)>2)
        {
           $this->mErrorMsg[] = "UF do endereço de entrega é maior que 2 caracteres: ".strlen($this->UF_entrega)." caracteres";
           $this->errorStatus = true;
        }
        return $this->UF_entrega;
    }

    /****************************************************
    *  Razao social / nome do destinatario endereço de entrega
    ****************************************************/
    public function setNomeEntrega($x)
    {
        $this->razao_social_entrega=$this->adicionarEspape($x);
    }
    public function getNomeEntrega()
    {
        if (empty($this->razao_social_entrega))
        {
           $this->mErrorMsg[] = "Não foi definido o nome / razao social do endereço de entrega";
           $this->errorStatus = true;
        }
        if (strlen($this->razao_social_entrega)>60)
        {
           $this->mErrorMsg[] = "Razão social / nome do endereço de entrega ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->razao_social_entrega;
    }

    /****************************************************
    *  CEP do destinatario endereço entrega
    ****************************************************/
    public function setCepEntrega($x)
    {
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $x=trim($x);
        $x=$this->adicionarEspape($x);
        $this->cep_entrega=$x;
    }
    public function getCepEntrega()
    {
        if (strlen($this->cep_entrega)>8)
        {
           $this->mErrorMsg[] = "CEP do endereço de entrega ultrapassou 8 caracteres";
           $this->errorStatus = true;
        }
        return $this->cep_entrega;
    }

    
    /****************************************************
    *  Telefone do destinatario endereço entrega
    ****************************************************/
    public function setTelefoneEntrega($x)
    {
        $x=str_replace("(","",$x);
        $x=str_replace(")","",$x);
        $x=str_replace(" ","",$x);
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=trim($x);
        $x=$this->adicionarEspape($x);
        $this->telefone_entrega=$x;
    }
    public function getTelefoneEntrega()
    {
        /*
        if (strlen($this->telefone_entrega)>14)
        {
           $this->mErrorMsg[] = "(fone-G13) Telefone do endereço de entrega ultrapassou 14 caracteres";
           $this->errorStatus = true;
        }
        */
        return $this->telefone_entrega;
    }

    /****************************************************
    *  cnpj do destinatario endereço entrega
    ****************************************************/
    public function setCnpjEntrega($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_entrega=$x;
    }
    public function getCnpjEntrega()
    {
        if (empty($this->cnpj_entrega))
        {
           //$this->mErrorMsg[] = "(CNPJ-C02) Não foi definido o cnpj do endereço de entrega";
           //$this->errorStatus = true;
        }
        if (strlen($this->cnpj_entrega)!=14)
        {
           //$this->mErrorMsg[] = "(CNPJ-C02) Cnpj do endereço de netrega esta incompleto: ".strlen($this->cnpj_entrega)." caracteres";
           //$this->errorStatus = true;
        }
        return $this->cnpj_entrega;
    }

    /****************************************************
    *  CPF do destinatario endereço entrega
    ****************************************************/
    public function setCpfEntrega($x)
    {
        //-- Limpa o Cpf
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cpf_entrega=$x;
    }
    public function getCpfEntrega()
    {
        if (empty($this->cpf_entrega))
        {
           //$this->mErrorMsg[] = "(CNPJ-C02) Não foi definido o CPF do emitente";
           //$this->errorStatus = true;
        }
        if (strlen($this->cpf_entrega)!=11)
        {
           //$this->mErrorMsg[] = "(CNPJ-C02) CPF do endereço de entreta esta incompleto: ".strlen($this->cpf_entrega)." caracteres";
           //$this->errorStatus = true;
        }
        return $this->cpf_entrega;
    }

    /****************************************************
    *  Inscricao Estadual do destinatario endereço de entrega
    ****************************************************/
    public function setInscricaoEstadualEntrega($x)
    {
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $x=$this->adicionarEspape($x);
        $this->inscricao_estadual_entrega=$x;
    }
    public function getInscricaoEstadualEntrega()
    {
        if (strlen($this->inscricao_estadual_entrega)>14)
        {
           $this->mErrorMsg[] = "Inscrição Estadual do endereço de entrega é maior que 14 caracteres: ".strlen($this->inscricao_estadual_entrega)." caracteres <BR>".$this->inscricao_estadual_entrega;
           $this->errorStatus = true;
        }
        return $this->inscricao_estadual_entrega;
    }


    public function setSenhaCertificado($x)
    {
        if (empty($x))
        {
           //$this->mErrorMsg[] = "Não foi definida uma senha para o certificado!";
           //$this->errorStatus = true;
        }
        $this->passKey=$x;
    }

    /****************************************************
    *  cnpj do autorizado xml
    ****************************************************/
    public function setCnpjAutorizadoXML($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_autorizado_xml=$x;
    }
    public function getCnpjAutorizadoXML()
    {        
        return $this->cnpj_autorizado_xml;
    }

    /****************************************************
    *  cpf do autorizado xml
    ****************************************************/
    public function setCpfAutorizadoXML($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cpf_autorizado_xml=$x;
    }
    public function getCpfAutorizadoXML()
    {        
        return $this->cpf_autorizado_xml;
    }


    /****************************************************
    *  hash responsavel tecnico
    ****************************************************/
    public function setHashCSRT($chaveAcesso,$ambiente) {

        // As chaves abaixo foram geradas em 06/02/2025
        // A partir de 01/04/2025, a SEFAZ-PR exigirá a validação do vínculo entre o cliente e o sistema emissor da NFe.
        // Até o momento, nenhuma outra UF implementou essa exigência oficialmente, mas isso pode mudar futuramente.

        $chaveAcesso = str_replace("NFe","",$chaveAcesso);
        if($ambiente == 1){            
            // Chave para o ambiente de produção, essa chave é gerada no portal da receita
            $hash = 'WDGFWFT96CMG7C4RJDWXNEH3AVDETOA8WC65'.$chaveAcesso;
            $this->id_token_csrt = '02';

        } elseif($ambiente == 2) {
            // Chave para o ambiente de homologação, essa chave é gerada no portal da receita
            $hash = 'DHC77WFCFN31X72SO8BYJW9TJV3Z5VFQW4J2'.$chaveAcesso;
            $this->id_token_csrt = '01';
        } else {
            $hash = '';
            $this->id_token_csrt = '';
        }

        $hashCsrt = base64_encode(hash('sha1',$hash,TRUE));

        $this->hash_csrt=$hashCsrt;
    }

    public function getHashCSRT() {        
        return $this->hash_csrt;
    }

    public function getIdTokenCSRT() {
        return $this->id_token_csrt;
    }
    
    /**
     * carregaCertP12
     *
     *
     *
     * @param	string $pathCert
     * @return	boolean TRUE se o certificado foi carregado e FALSE se nao
     * @access  public
    **/
    public function carregaCertP12($pathCert='')
    {
        //-- Verifica se foi passa o path completo do certificado p12
        if( $pathCert == '' )
        {
           $this->mErrorMsg[] = "Um certificado deve ser passado para a classe!!";
           $this->errorStatus = true;
           return FALSE;
        }
        elseif (!file_exists($pathCert))
        {
           //$this->mErrorMsg[] = "Certificado não existe: $pathCert";
           //$this->errorStatus = true;
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

	public function assinarXML()
	{
    	/////////////////////////////////////////////////////////////////////////
    	//-- ASSINATURA DIGITAL DO ARQUIVO
    	/////////////////////////////////////////////////////////////////////////
        //carrega as chaves do certifciado p12
        $key = file_get_contents($this->pathCert);
        $resp = openssl_pkcs12_read ($key,$x509certdata,$this->passKey);
        $rPrivkey = openssl_pkey_get_private($x509certdata['pkey']);
        $rPubkey =  openssl_pkey_get_public($x509certdata['cert']);

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
        
        $oAssinatura = new \DOMDocument();
        $oAssinatura->preservWhiteSpace = FALSE;
        $oAssinatura->formatOutput = FALSE;

        //-- Muito importante deixar ativadas as opÃ§oes para limpar os espacos em branco e as tags vazias
        if (!$oAssinatura->load($this->getNomeArquivoXML(),LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG))
        {
           $this->mErrorMsg[] = "Não foi possivel abrir o arquivo:".$this->getNomeArquivoXML();
           $this->errorStatus = true;
           echo "********** nao foi possivel assinar";
        }

        //-- Carrega o node que sera assinado
        $oA01 = $oAssinatura->getElementsByTagName('infNFe')->item(0);
        $oXS02 = $oAssinatura->getElementsByTagName('SignedInfo')->item(0);

        //-- Extrai o id da NF
        $Id = trim($oA01->getAttribute("Id"));


        //////////////////////////////////////////////////////////////
        //-- DIGEST VALUE
        //////////////////////////////////////////////////////////////

        //-- Pega todos os dados contidos na TAG infNFe
        $cString = $oA01->C14N(FALSE,FALSE,NULL,NULL);

        //-- Codifica os dados extraidos da TAG infNFe
        $cDigestValue = base64_encode(hash('sha1',$cString,TRUE));
        $this->setDigestValue($cDigestValue);

        //-- Localiza o Node da tag Digest Value
	    $oXS17 = $oAssinatura->getElementsByTagName('DigestValue')->item(0);

	    //-- Salva o valor do digest value dentro da tag DigestValue
        $oXS17->nodeValue = $cDigestValue;


        $sqlT007 = mysqli_query("SELECT C004_Identificacao_CSC, C004_Codigo_CSC FROM T007 LEFT JOIN C004 ON C004_Id=T007_C004_Id WHERE T007_Id = '{$this->T007_Id}'");
        $mT007 = mysql_fetch_assoc($sqlT007);

        $ide        = $oAssinatura->getElementsByTagName("ide")->item(0);
        $dest       = $oAssinatura->getElementsByTagName("dest")->item(0);
        $ICMSTot   = $oAssinatura->getElementsByTagName("ICMSTot")->item(0);
        $protNFe    = $oAssinatura->getElementsByTagName("protNFe")->item(0);

        if($ide->getElementsByTagName("mod")->item(0)->nodeValue == '65'){
            echo "<br/>%%%%% Modelo 65 ." . $ide->getElementsByTagName("mod")->item(0)->nodeValue;
            $digValHex = $this->strtohex($cDigestValue);
            $chaveAcesso = substr($Id, 3);
            // GERANDO QR CODE-
            $tpEmis = $ide->getElementsByTagName('tpEmis')->item(0)->nodeValue;
            $vNF = $ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue;
            $dhEmi = $ide->getElementsByTagName("dhEmi")->item(0)->nodeValue;
            $diaEmissao = (new \DateTimeImmutable($dhEmi))->format('d');
            

            if(empty($dest)) {
                $destQRCode = '';
            } else {
                $clienteCnpj = (!empty($dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) ? $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue : $dest->getElementsByTagName("CPF")->item(0)->nodeValue;
                $destQRCode = "&cDest={$clienteCnpj}";
            }

            /*$parameters = array(
                'URL'           =>"http://www.dfeportal.fazenda.pr.gov.br/dfe-portal/rest/servico/consultaNFCe",
                'chNFe'         =>$chaveAcesso,
                'nVersao'       =>100, // NOS EXEMPLOS ESTAVA FIXADO A VERSÃO 100
                'tpAmb'         =>$ide->getElementsByTagName("tpAmb")->item(0)->nodeValue,
                'dhEmi'         =>$dhHex,
                'vNF'           =>$ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue,
                'vICMS'         =>$ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue,
                'digVal'        =>$digValHex,
                'cIdToken'      =>$mT007['C004_Identificacao_CSC'],
                'Token'         =>$mT007['C004_Codigo_CSC']
            );
            $strHashURL = "chNFe={$parameters['chNFe']}&nVersao={$parameters['nVersao']}&tpAmb={$parameters['tpAmb']}{$destQRCode}&dhEmi={$parameters['dhEmi']}&vNF={$parameters['vNF']}&vICMS={$parameters['vICMS']}&digVal={$parameters['digVal']}&cIdToken={$parameters['cIdToken']}";
            $strHash = "chNFe={$parameters['chNFe']}&nVersao={$parameters['nVersao']}&tpAmb={$parameters['tpAmb']}{$destQRCode}&dhEmi={$parameters['dhEmi']}&vNF={$parameters['vNF']}&vICMS={$parameters['vICMS']}&digVal={$parameters['digVal']}&cIdToken={$parameters['cIdToken']}{$parameters['Token']}";
            $hashSHA1 = strtolower(sha1($strHash));*/

            //******** Raquel 12/09/2018 ****************/
            // Alterações de acordo com manual NFC-e Manual de Padrões, Padrões técnicos DANFE NFC-E E QR CODE VERSÃO 2, Versão 5.0 – fevereiro de 2018
            // Remoção dos 0 significativos do cIDToken
            $mT007['C004_Identificacao_CSC'] = intval($mT007['C004_Identificacao_CSC']);

            $linkQRCode      = "http://www.fazenda.pr.gov.br/nfce/qrcode?";

            if($this->getUFEmitente() == 'SC'){
                // Se for SC seta para o link da SC
                $linkQRCode = "https://sat.sef.sc.gov.br/nfce/consulta?";
                if($ide->getElementsByTagName("tpAmb")->item(0)->nodeValue == '2'){
                    $linkQRCode = "https://hom.sat.sef.sc.gov.br/nfce/consulta?";
                }
            }
            
            // Criação dos parâmetros para emissão online
            $parameters = array(
                'URL'           =>  $linkQRCode,
                'chNFe'         =>  $chaveAcesso,
                'nVersao'       =>  2, // NOS EXEMPLOS ESTAVA FIXADO A VERSÃO 100
                'tpAmb'         =>  $ide->getElementsByTagName("tpAmb")->item(0)->nodeValue,
                'dhEmi'         =>  $diaEmissao,
                'vNF'           =>  number_format((float)$vNF, 2, '.', ''),
                // 'vICMS'         =>  $ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue,
                'digVal'        =>  $digValHex,
                'cIdToken'      =>  $mT007['C004_Identificacao_CSC'],
                'Token'         =>  $mT007['C004_Codigo_CSC']
            );

            
            if ($tpEmis != 9) {
                //Padrão emissão normal 
                //<http://<dominio>/nfce/qrcode?p>=<chave_acesso>|<versao_qrcode>|<tipo_ambiente>|<identificador_csc>|<codigo_hash>
                $strHashURL = "{$parameters['chNFe']}|{$parameters['nVersao']}|{$parameters['tpAmb']}|{$parameters['cIdToken']}";
                $strHash = "{$strHashURL}{$parameters['Token']}";
            } else {
                //Padrão emissão contingência offline 
                //<http://<dominio>/nfce/qrcode/?p>=<chave_acesso>|<versao_qrcode>|<tipo_ambiente>|<dia_data_emissao>|<valor_total_nfce>|<digVal>|<identificador_csc>|<codigo_hash>
                $strHashURL = "{$parameters['chNFe']}|{$parameters['nVersao']}|{$parameters['tpAmb']}|{$parameters['dhEmi']}|{$parameters['vNF']}|{$parameters['digVal']}|{$parameters['cIdToken']}";
                $strHash = "{$strHashURL}{$parameters['Token']}";
            }
            
            $hashSHA1 = strtoupper(sha1($strHash));

            $URL = <<<EOT
            {$parameters['URL']}p={$strHashURL}|{$hashSHA1}
EOT;
            $URL = str_replace(' ', '', $URL);
            $URL = trim($URL);

            $oNFe = $oAssinatura->getElementsByTagName('NFe')->item(0);
            $Signature = $oNFe->getElementsByTagName('Signature')->item(0);

            //-- ZX - Informações suplementares da Nota Fiscal
            $oZX01 = $oAssinatura->createElement("infNFeSupl");
            $oNFe->insertBefore($oZX01, $Signature);
            //-- qrCode - TAG de grupo das informações de identificação da NF-e
            $oZX02 = $oAssinatura->createElement("qrCode");
            $oZX01->appendChild($oZX02);

            $oZX02Texto = $oAssinatura->createCDATASection($URL);
            $oZX02->appendChild($oZX02Texto);

            //-- urlChave - Texto com a URL de consulta por chave de acesso a ser impressa no DANFE NFC-e.
            $urlNFCe = $this->setConsultaNFCe();
            $oZX03 = $oAssinatura->createElement("urlChave", $urlNFCe);
            $oZX01->appendChild($oZX03);
        }

        //////////////////////////////////////////////////////////////
        //-- ASSINATURA  (SIGNATURE VALUE)
        //////////////////////////////////////////////////////////////

        // extrai os dados a serem assinados para uma string
        $dados = $oXS02->C14N(FALSE,FALSE,NULL,NULL);
        $signature = '';


        //executa a assinatura digital usando o resource da chave privada
        if (!$resp = @openssl_sign($dados,$signature,$rPrivkey))
        {
            //$this->mErrorMsg[] = "Verifique a senha do certificado digital";
           // $this->errorStatus = true;
        }
        else
        {

            //codifica assinatura para o padrao base64
            $cSignatureValue = base64_encode($signature);

            $oXS18 = $oAssinatura->getElementsByTagName('SignatureValue')->item(0);
            $oXS18->nodeValue = $cSignatureValue;

            //-- Monta o nome do arquivo que sera assinado
            $cArquivoAssinado = $this->getPastaArquivoXML().'assinado/'.str_replace("Nfe","",$Id).'.xml';

            // se o arquivo da nota ja existir, excluo
            if (file_exists($cArquivoAssinado)) unlink($cArquivoAssinado);
            if (file_exists($this->getNomeArquivoXML())) unlink($this->getNomeArquivoXML());

    		$oAssinatura->normalize();
    		$oAssinatura->save($cArquivoAssinado);
    		$this->setArquivoAssinado($cArquivoAssinado);
    		
    		//-- Elimina os lixos do meio das tags
    		$conteudo = file_get_contents($cArquivoAssinado);
			$conteudo = str_replace(array(chr(13),chr(10)),"",$conteudo);    		
	    	file_put_contents($cArquivoAssinado, $conteudo);    		

        }
     }
     

    /**
     * Estabelece comunicaÃ§ao com servidor SOAP
     * FUNCIONAL !!!
     * @param    array   $param Matriz com o cabeÃ§alho e os dados da mensagem soap
     * @param    string  $wsdl DesignaÃ§ao do ServiÃ§o SOAP
     * @return   mixed  Array com a resposta do SOAP ou String do erro ou false
     * @access   private
     **/
     
     
     
    private function sendSOAP($param, $wsdl)
    {
        // COMEÇAR AQUI
        $dataVer = $this->setVersaoLayoutXSD($wsdl);
    	//-- Monta a url do serviço
        if (!$URL = $this->setWebService($wsdl))
        {
             $this->retornoDebug .= "ws \n";
             return false;
        }
        if (substr($URL,-5)!='?WSDL')
        {
            $this->retornoDebug .= "url \n";
        	$xURL.=$URL.'?WSDL';
        }
        //echo "<div style='position:absolute;top:5;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>WevService : ".$URL."</font></div>";

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
        // TERMINAR AQUI
        if ($this->isSoap=='NUSOAP')
        {

            $this->retornoDebug .= "\n";
            $this->retornoDebug .= "**************************************************************************************\n";
            $this->retornoDebug .= "* SENDSOAP - É NUSOAP - Inicia tentativa de conexao \n";
            $this->retornoDebug .= "**************************************************************************************\n";
            $this->retornoDebug .= "\n";
            try {

                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - Inicia conexao \n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";
                // inicia a conexao SOAP
                // ele precisa passar como parâmetro a URL que será utilizada, no caso será utilizada a URL da web service
                $client = new \nusoap_client($xURL, true);
                $client->authtype         = 'certificate';
                $client->soap_defencoding = 'UTF-8';

                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - Seta parametros para conexao segura \n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";
                //Seta parametros para a conexao segura
                if (file_exists($ArquivoKEY_Padrao) and file_exists($ArquivoCERT_Padrao))
                {
                    $client->certRequest['sslkeyfile']  = $ArquivoKEY_Padrao;
                    $client->certRequest['sslcertfile'] = $ArquivoCERT_Padrao;
                    $client->certRequest['passphrase']  = $this->passKey;
                }
                elseif (!file_exists($this->getCertificadoPEM()))
                {
                   $client->certRequest['sslcertfile'] = $this->getCertificadoHardness();
                   $client->certRequest['passphrase']  = "hard3871";
                }
                else
                {
                   $client->certRequest['sslcertfile'] = $this->getCertificadoPEM();
                   $client->certRequest['passphrase']  = $this->passKey;
                }
                $client->certRequest['verifypeer']  = false;
                $client->certRequest['verifyhost']  = false;
                $client->certRequest['trace']       = 1;

                if (is_array($param))
                {
                   //$this->retornoDebug .= "<font color=blue>request:".htmlspecialchars($param)."</font>";
                }
                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - Chama funcao webservice \n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";
                // chama a funÃ§ao do webservice, passando os parametros
                // $this->setFunctionWDSL($wsdl) -> retorna o serviço e a versão da nfe, param -> trás os parâmetros retornados.
                $retorno = $client->call($this->setFunctionWDSL($wsdl), $param);

                $this->debug_str = htmlspecialchars($client->debug_str);

            }
            catch (Exception $ex)
            {

                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - Erro \n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";
                //-- em caso de erro retorne o mesmo
                if (!is_bool($client->getError()))
                {
                    $this->errorMsg = $client->getError();
                    $this->retornoDebug .= "<div style='position:absolute;top:15;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>Erro : ".$client->getError()."</font></div>";
                    $this->errorStatus = true;
                }

            }

        }
        else
        {        
        //APAGAR - COMEÇAR AQUI         
            $this->retornoDebug .= "\n";
            $this->retornoDebug .= "**************************************************************************************\n";
            $this->retornoDebug .= "* SENDSOAP - Não é NUSOAP \n";
            $this->retornoDebug .= "**************************************************************************************\n";
            $this->retornoDebug .= "\n";
             if ($this->versao_nfe=='1.10')
             {
            	$this->errorMsg = "Versão 1.10 da NF-e não funciona com Soap Nativo do PHP";
                $this->retornoDebug .= "<div style='position:absolute;top:25;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>Erro : Versão 1.10 da NF-e não funciona com Soap Nativo do PHP </font></div>";
            	$this->errorStatus = true;
                return;
             }

             // Cria o arquivo unico do certificado (.PEM) unindo os dois arquivos PEM que foram gerados no upload do certificado PFX
   	         if (!file_exists($this->getCertificadoPEM()) and file_exists($ArquivoKEY_Padrao) and file_exists($ArquivoCERT_Padrao))
   	         {
  	              $Certificado1 = file_get_contents($ArquivoKEY_Padrao);
   	              $Certificado2 = file_get_contents($ArquivoCERT_Padrao);
                  file_put_contents($this->getCertificadoPEM(),$Certificado1.$Certificado2);
  	         }
  	         elseif (!file_exists($ArquivoKEY_Padrao) or !file_exists($ArquivoCERT_Padrao))
  	         {
                $this->errorMsg = "Arquivo do certificado não encontrado";
                $this->retornoDebug .= "<font style='font-family:arial;font-size:10px;color:black;'>Erro : Arquivo do certificado não encontrado</font>";
                $this->retornoDebug .= "<font style='font-family:arial;font-size:10px;color:black;'>$ArquivoKEY_Padrao $ArquivoCERT_Padrao</font>";
                $this->errorStatus = true;
  	         }
            if (true)
            {            
        // CONTINUAR AQUI
                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - is true \n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";
                $versao    = $this->setVersaoLayoutXSD($wsdl);
                
                // wsdl utilizada para informar a versão da mensagem submetida no XML 
                if ($wsdl=='NfeConsultaNF' and ($this->getTipoDocAuxiliar()==3 || $this->getTipoDocAuxiliar()==6 || $this->getTipoDocAuxiliar()==7) and $this->getIdentAmbiente()==2)
                {
                    // para o SCAN                  
                    $wsdl='consultaCadastro';
                    $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/CadConsultaCadastro4';
                }
                elseif($wsdl=="RecepcaoEvento")
                {
                    $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/NFeRecepcaoEvento4';
                    //$wsdl='nfeRecepcaoEvento';
                }
                elseif($wsdl=="NFeAutorizacao")
                {
                    $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/NFeAutorizacao4';
                    $wsdl = 'nfeAutorizacaoLote';
                }
                elseif($wsdl=="NFeRetAutorizacao")
                {
                    $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/NFeRetAutorizacao4';
                    $wsdl = 'nfeRetAutorizacaoLote';
                }
                elseif($wsdl=="NfeConsultaProtocolo")
                {
                    $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/NFeConsultaProtocolo4';
                    $wsdl = 'nfeConsultaNF';
                }
                else if($wsdl == 'NfeInutilizacao' && ($this->getUFEmitente()=='MT' || $this->getUFEmitente()=='MG' || $this->getUFEmitente()=='SC' || $this->getUFEmitente()=='RS' || $this->getUFEmitente()=='SP' || $this->getUFEmitente()=='DF' || $this->getUFEmitente()=='ES' || $this->getUFEmitente()=='PA' || $this->getUFEmitente()=='RJ' || $this->getUFEmitente()=='PE')){
                        $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/NFeInutilizacao4';
                }
                else                
                {
                    if ($this->getUFEmitente()=='PR'){
                        $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/NfeStatusServico3';
                    } else {
                        $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/'.$wsdl.'4';
                    }
                }
                
                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - Setou name space $namespace\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";

                //$cabec = '<nfeCabecMsg xmlns="'. $namespace . '"><cUF>'.$this->getCodigoUFEmitente().'</cUF><versaoDados>'.$versao.'</versaoDados></nfeCabecMsg>';
                $cabec = '';
                // $dados = '<nfeDadosMsg xmlns="'. $namespace . '">'.$param.'</nfeDadosMsg>';
                // inserindo mensagem de requisição padrão SOAP no Body
                if ($wsdl=="RecepcaoEvento")
                {
                    $dados = $param;
                }
                else
                {
                    $dados = '<nfeDadosMsg xmlns="'. $namespace . '">'.$param.'</nfeDadosMsg>';
                }
                
                if($this->getUFEmitente()=='CE'){
                    if($wsdl == 'NfeStatusServico'){
                        $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/'.$wsdl.'2/nfeStatusServicoNF2';
                    } else if($wsdl == 'nfeAutorizacaoLote'){
                        $namespace = $namespace.'/nfeAutorizacaoLote';
                    } else if($wsdl == 'nfeConsultaNF2'){
                        $namespace = $namespace.'/nfeConsultaNF2';
                    } else if($wsdl == "RecepcaoEvento"){
                        $namespace = $namespace.'/nfeRecepcaoEvento';
                    } else if($wsdl == "NfeInutilizacao"){
                        $namespace = $namespace.'/nfeInutilizacaoNF2';
                    }
                }

                if($wsdl == "NfeInutilizacao" && $this->getUFEmitente()=='GO'){
                    $wsdl = 'nfeInutilizacaoNF2';
                }

                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - Modificou namespace $namespace\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";
                
                //remove as tags xml que porventura tenham sido inclusas
                $dados = str_replace('<?xml version="1.0"?>','', $dados);
                $dados = str_replace('<?xml version="1.0" encoding="utf-8"?>','', $dados);
                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - __sendSOAP2\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";
                $retorno = $this->__sendSOAP2($URL, $namespace, $cabec, $dados, $wsdl, $this->getIdentAmbiente());
                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - Fim _sendSOAP2\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";

            }
        // TERMINAR AQUI     
			else
			{
                $this->retornoDebug .= "\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "* SENDSOAP - Else is true\n";
                $this->retornoDebug .= "**************************************************************************************\n";
                $this->retornoDebug .= "\n";
                try{
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
                    $this->retornoDebug .= "\n";
                    $this->retornoDebug .= "**************************************************************************************\n";
                    $this->retornoDebug .= "* SENDSOAP - soapdebug\n";
                    $this->retornoDebug .= "**************************************************************************************\n";
                    $this->retornoDebug .= "\n";

                    $this->retornoDebug .= $client->soapDebug;
                
                    $headerWSDL = rtrim($wsdl, 'NF');

                    $headerbody = array('cUF' => $this->getCodigoUFEmitente(), 'versaoDados' => $dataVer);
                    $headerbody = (object)array(
                        'ns2:cUF' => $this->getCodigoUFEmitente(),
                        'ns2:versaoDados' => $dataVer
                    );
                    $this->retornoDebug .= "\n";
                    $this->retornoDebug .= "**************************************************************************************\n";
                    $this->retornoDebug .= "* SENDSOAP - soapheader\n";
                    $this->retornoDebug .= "**************************************************************************************\n";
                    $this->retornoDebug .= "\n";
                    $header = new \SOAPHeader('http://www.portalfiscal.inf.br/nfe/wsdl/'.$headerWSDL, 'nfeCabecMsg', $headerbody);
                    $client->__setSoapHeaders($header);

                    $nfeDadosMsg = new \stdClass;
                    $nfeDadosMsg->any = $param;
    
                    //-- CHAMA O METODO
                    if ($wsdl=='ConsultaCadastro')
                    {
                        $this->retornoDebug .= "\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "* SENDSOAP - nfeConsultaCadastro2\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "\n";
                        $retorno = $client->nfeConsultaCadastro2($nfeDadosMsg);
                    }
                    elseif( $wsdl=='NFeAutorizacao')
                    {
                        $this->retornoDebug .= "\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "* SENDSOAP - nfeAutorizacaoLote\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "\n";
                       $retorno = $client->nfeAutorizacaoLote($nfeDadosMsg);
                    }
                    elseif( $wsdl=='NFeRetAutorizacao')
                    {
                        $this->retornoDebug .= "\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "* SENDSOAP - nfeRetAutorizacaoLote\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "\n";
                    	$retorno = $client->nfeRetAutorizacaoLote($nfeDadosMsg);
                    }
                    elseif( $wsdl=='NfeCancelamento')
                    {
                    	$retorno = $client->nfeCancelamentoNF2($nfeDadosMsg);
                    }
                    elseif( $wsdl=='NfeInutilizacao')
                    {
                        $this->retornoDebug .= "\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "* SENDSOAP - NfeInutilizacao\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "\n";
                    	$retorno = $client->nfeInutilizacaoNF2($nfeDadosMsg);
                    }
                    elseif( $wsdl=='NfeConsultaNF')
                    {
                        $this->retornoDebug .= "\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "* SENDSOAP - nfeConsultaNF2\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "\n";
                    	$retorno = $client->nfeConsultaNF2($nfeDadosMsg);
                    }
                    elseif( $wsdl=='NfeStatusServico')
                    {
                        $this->retornoDebug .= "\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "* SENDSOAP - nfeStatusServicoNF2\n";
                        $this->retornoDebug .= "**************************************************************************************\n";
                        $this->retornoDebug .= "\n";
                    	$retorno = $client->nfeStatusServicoNF2($nfeDadosMsg);
                    }
	        	}
	            catch(Exception $e)
	            {
                    $this->retornoDebug .= "\n";
                    $this->retornoDebug .= "**************************************************************************************\n";
                    $this->retornoDebug .= "* SENDSOAP - Exception e \n";
                    $this->retornoDebug .= "**************************************************************************************\n";
                    $this->retornoDebug .= "\n";
	            	$this->errorMsg = $e->getMessage();
                    $this->retornoDebug .= "<div style='position:absolute;top:55;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>Erro : ".$e->getMessage()."</font></div>";
	            	$this->errorStatus = true;
	
	        	}


	            if (is_soap_fault($retorno))
	            {
                      $this->retornoDebug .= "\n";
                      $this->retornoDebug .= "**************************************************************************************\n";
                      $this->retornoDebug .= "* SENDSOAP - is soap fault \n";
                      $this->retornoDebug .= "**************************************************************************************\n";
                      $this->retornoDebug .= "\n";
	                $this->retornoDebug .= "<br><hr><font color=blue>Requisição<br>".htmlspecialchars($param)."</font>";                
	
	                if (is_object($retorno))
	                {
	                   $xmlresp = utf8_encode($retorno->any);
	                   $this->retornoDebug .= "<hr><font color=red>Retorno:<br>$retorno<br>".htmlspecialchars($xmlresp)."</font>";                   
	                }
	                else
	                {
	                   $this->retornoDebug .= "<hr><font color=red>Retorno:<br>Não Retornou NADA</font>";
	                }
	                $this->retornoDebug .= "<hr><font color=gray>";
	                foreach($retorno as $erro) 
	                {
                          $this->retornoDebug .= "\n";
                          $this->retornoDebug .= "**************************************************************************************\n";
                          $this->retornoDebug .= "* SENDSOAP - Erro \n";
                          $this->retornoDebug .= "**************************************************************************************\n";
                          $this->retornoDebug .= "\n";
	                	$this->retornoDebug .= htmlspecialchars($erro,ENT_QUOTES)."<br>";
	                }                
	
	            }

			}


        }

        log("wsdl = " . $wsdl . "namespace = " . $namespace);

        // CONTINUAR AQUI
          $this->retornoDebug .= "\n";
          $this->retornoDebug .= "**************************************************************************************\n";
          $this->retornoDebug .= "* SENDSOAP - Fim  \n";
          $this->retornoDebug .= "**************************************************************************************\n";
          $this->retornoDebug .= "\n";

        return $retorno;
    }


     /**********************************************
     * Verifica o status do servico da SEFAZ
     * FUNCIONAL !!
     * Este metodo carrega a variavel
     * $this->cStat = 107 OK
     * cStat = 108 sitema paralizado momentaneamente, aguardar retorno
     * cStat = 109 sistema parado sem previsao de retorno, verificar status SCAN
     *
     * @param	none
     * @return	boolean True se operacional e False se nao
     * @access  public
     ***********************************************/
    public function statusServico()
    {
        $bRet = FALSE;
        return true;

        echo "\n";
        echo "**************************************************************************************\n";
        echo "* Inicia function statusServico \n";
        echo "**************************************************************************************\n";
        echo "\n";

        // Carga das variaveis da funcao do webservice
        $wsdl = 'NfeStatusServico';
        
        // $cabecXsdfile não está sendo utilizada 
        $cabecXsdfile   = $this->setVersaoArqXSD('CabecMsg');
        // Não está sendo utilizada
        $cabecVer       = $this->setVersaoLayoutXSD('CabecMsg');
        // $dataXsdfile não está sendo utilizada 
        $dataXsdfile    = $this->setVersaoArqXSD($wsdl);
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);
        $isRetorno      = false;


        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $param = '<consStatServ versao="'.$dataVer.'" xmlns="'.$this->URLnfe.'">'.'<tpAmb>'.$this->getIdentAmbiente().'</tpAmb><cUF>'.$this->getCodigoUFEmitente().'</cUF><xServ>STATUS</xServ></consStatServ>';
        //xmlns:xsi="'.$this->URLxsi.'" xmlns:xsd="'.$this->URLxsd.'"
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* SEND SOAP \n";
        echo "**************************************************************************************\n";
        echo "\n";
        $retorno = $this->sendSOAP($param, $wsdl);
       
        if (is_object($retorno)) {
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }

        if ($isRetorno===true)
        {
            if ($xmlresp == ''){
                //Houve uma falha na comunicação SOAP
                $this->errorMsg = 'Falha na comunicação SOAP.';
                return FALSE;
            }

            //tratar dados de retorno
            $doc = new \DOMDocument(); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            // status do serviÃ§o
            $this->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            // tempo medio de resposta
            $this->tMed = $doc->getElementsByTagName('tMed')->item(0)->nodeValue;
            // data e hora da mensagem
            $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
            //converter a informaÃ§ao de data hora para timestamp
            $this->dhRecbto = $this->convertTime($this->dhRecbto);
            // motivo da resposta (opcional)
            $this->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            // obervaÃ§oes opcional
            $this->xObs = $doc->getElementsByTagName('xObs')->item(0)->nodeValue;
            
        	if ($this->cStat!='107' and $this->getTipoDocAuxiliar()=='3')
			{
				// Se for SCAN e nao estiver ativo
            	$this->mErrorMsg[] = $this->cStat." - ".$this->xMotivo;
            	$this->errorStatus = true;
            	return FALSE;				
			}
                    
            
            $bRet=true;

        }
        else
        {
            //$this->mErrorMsg[] = 
            echo "EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS . <BR> ";
            $this->errorStatus = true;
            return FALSE;
        }
        return $bRet;
    }
    
    
    /**********************************************
     * Envia lote de Notas Fiscais
     *
     * @param	array   $aNFe notas fiscais em xml uma em cada campo de uma string
     * @param   integer $idLote o id do lote e um numero que deve ser gerado pelo sistema
     *                          a cada envio mesmo que seja de apenas uma NFe usar banco
     *                          de dados
     * @return	boolean	True se aceito o lote ou False de rejeitado
     * @access  public
     ************************************************/
    public function enviaNF($cArquivo,$idLote='1',$modo='0')
    {

        if (empty($cArquivo))
        {
           $this->mErrorMsg[] = "Arquivo a ser enviado para o SEFAZ nao foi definido";
           $this->errorStatus = true;
        }

        if (!file_exists($cArquivo))
        {
           $this->mErrorMsg[] = "Arquivo inexistente: $cArquivo";
           $this->errorStatus = true;
        }

        //-- Variavel de retorno do metodo
        $bRet = false;

        //-- Carga das variaveis da funçao do webservice
        $wsdl = 'NFeAutorizacao';
        //$wsdl = 'NfeRecepcao';
        $cabecXsdfile   = $this->setVersaoArqXSD('CabecMsg');
        $cabecVer       = $this->setVersaoLayoutXSD('CabecMsg');
        $dataXsdfile    = $this->setVersaoArqXSD($wsdl);
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);
        $isRetorno      = false;

        // limpa a variavel
        $sNFe = file_get_contents($cArquivo);

        //remover xml version="1.0" encoding=...MAIUSCULO E MINUSCULO
        $sNFe = str_replace('<?xml version="1.0" encoding="utf-8"?'.'>','',$sNFe);
        $sNFe = str_replace('<?xml version="1.0" encoding="UTF-8"?'.'>','',$sNFe);

        //$sNFe nao pode ultrapassar 500kBytes
        if (strlen($sNFe) > 470000) {
            //indicar erro e voltar
            //return FALSE;
        }

        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $param = '<enviNFe xmlns="'.$this->URLnfe.'" versao="'.$dataVer.'"><idLote>'.$idLote.'</idLote><indSinc>'.$modo.'</indSinc>'.$sNFe.'</enviNFe>';
        //$param = '<enviNFe xmlns="'.$this->URLnfe.'" versao="'.$dataVer.'"><idLote>'.$idLote.'</idLote>'.$sNFe.'</enviNFe>';

        $retorno = $this->sendSOAP($param, $wsdl);

        $this->retornoEnviaNF = $retorno;

        if (is_object($retorno)) {
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }

        file_put_contents("/tmp/logNFCe.txt", $xmlresp . "\n--------------\n", FILE_APPEND);
            
        if ($isRetorno===true)
        {
            if ($xmlresp == ''){
                //Houve uma falha na comunicação SOAP
                $this->errorMsg = 'Falha na comunicação SOAP.';
                return FALSE;
            }

            $doc->RetornoXMLSefaz=$xmlresp;

            //tratar dados de retorno
            $doc = new \DOMDocument(); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            //echo "<pre>" . htmlentities($xmlresp) . "</pre>";

            // status do recebimento ou mensagem de erro
            $this->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $this->tAmb = $doc->getElementsByTagName('tAmb')->item(0)->nodeValue;
            $this->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $this->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;

            $temInfProt = ($doc->getElementsByTagName('infProt')->length > 0);
            if ($temInfProt) {
                // Felipe Carrano - 14/08/2025
                // No modelo sincrono quando é SC (Problema que aconteceu na Guimepa com NFe e Ceigon NFCe)
                // Na Guimepa estamos testando o envio de NFe no modelo sincrono
                // Vem esse modelo de XML de retorno e informação do erro está no infProt
                //Exemplo : 
                // <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"
                //             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                //             xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                // <soap:Body>
                //     <nfeResultMsg xmlns="http://www.portalfiscal.inf.br/nfe/wsdl/NFeAutorizacao4">
                //     <retEnviNFe versao="4.00" xmlns="http://www.portalfiscal.inf.br/nfe">
                //         <tpAmb>2</tpAmb>
                //         <verAplic>SVRS2508131518</verAplic>
                //         <cStat>104</cStat>
                //         <xMotivo>Lote processado</xMotivo>
                //         <cUF>42</cUF>
                //         <dhRecbto>2025-08-14T20:18:52-03:00</dhRecbto>
                //         <protNFe versao="4.00">
                //         <infProt>
                //             <tpAmb>2</tpAmb>
                //             <verAplic>SVRS2508131518</verAplic>
                //             <chNFe>42250839590561000123550010000035001575702621</chNFe>
                //             <dhRecbto>2025-08-14T20:18:52-03:00</dhRecbto>
                //             <digVal>vzkFNRDPpfef4AgqHMRPkWazphI=</digVal>
                //             <cStat>733</cStat>
                //             <xMotivo>Rejeicao: CFOP de operacao interna e idDest &lt;&gt; 1</xMotivo>
                //         </infProt>
                //         </protNFe>
                //     </retEnviNFe>
                //     </nfeResultMsg>
                // </soap:Body>
                //</soap:Envelope>
                // pega os valores do infProt
                $this->cStat    = $doc->getElementsByTagName('cStat')->item(1)->nodeValue;
                $this->tAmb     = $doc->getElementsByTagName('tpAmb')->item(1)->nodeValue;
                $this->verAplic = $doc->getElementsByTagName('verAplic')->item(1)->nodeValue;
                $this->xMotivo  = $doc->getElementsByTagName('xMotivo')->item(1)->nodeValue;
            }     
 

            // em caso de sucesso  cStat = 103
            if ($this->cStat == '103')
            {
                // tempo medio de processamento
                $this->tMed = $doc->getElementsByTagName('tMed')->item(0)->nodeValue;
                // data e hora da mensagem
                $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                // numero do recibo, se o lote foi aceito
                // guardar o numero do recibo na base de dados pois devera ser usado
                // para verificar o estatus dos Lotes enviados
                $this->nRec = $doc->getElementsByTagName('nRec')->item(0)->nodeValue;
                $bRet = TRUE;

            }

            if ($this->cStat == '104' || $this->cStat == '100'){
                $bRet = TRUE;
            } 

        }
        else
        {
            $this->mErrorMsg[] = "EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _";
            $this->errorStatus = true;

        }

        return $bRet;
    }

        /**********************************************
     * Envia lote de Notas Fiscais
     *
     * @param	array   $aNFe notas fiscais em xml uma em cada campo de uma string
     * @param   integer $idLote o id do lote e um numero que deve ser gerado pelo sistema
     *                          a cada envio mesmo que seja de apenas uma NFe usar banco
     *                          de dados
     * @return	boolean	True se aceito o lote ou False de rejeitado
     * @access  public
     ************************************************/
    public function enviaNFLote($aNFe,$idLote='1')
    {                
        if(!is_array($aNFe))
        {               
           $this->mErrorMsg[] = "É necessário informar um array contendo os xml do Lote";
           $this->errorStatus = true;
        }

        //-- Variavel de retorno do metodo
        $bRet = false;        

        //-- Carga das variaveis da funçao do webservice
        $wsdl = 'NFeAutorizacao';
        //$wsdl = 'NfeRecepcao';
        $this->setVersaoArqXSD('CabecMsg');
        $this->setVersaoLayoutXSD('CabecMsg');
        $this->setVersaoArqXSD($wsdl);
        $dataVer= $this->setVersaoLayoutXSD($wsdl);

        //Compacta os XML e remove os cabeçalhos do XML
        $content = array_reduce($aNFe, function ($carry, $xml) {
            $carry .= preg_replace("/<\?xml.*?\?>/", "", $xml);
            return $carry;
        }, '');

        //$content nao pode ultrapassar 500kBytes        
        
        $param = '<enviNFe xmlns="'.$this->URLnfe.'" versao="'.$dataVer.'"><idLote>'.$idLote.'</idLote><indSinc>0</indSinc>'.$content.'</enviNFe>';        

        $retorno = $this->sendSOAP($param, $wsdl);

        $this->retornoEnviaNF = $retorno;
        
        if (!empty($this->retornoEnviaNF))
        {            
            //tratar dados de retorno
            $doc = new \DOMDocument(); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($this->retornoEnviaNF,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            // status do recebimento ou mensagem de erro
            $this->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $this->tAmb = $doc->getElementsByTagName('tAmb')->item(0)->nodeValue;
            $this->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $this->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;

            // em caso de sucesso  cStat = 103
            if ($this->cStat == '103')
            {
                // tempo medio de processamento
                $this->tMed = $doc->getElementsByTagName('tMed')->item(0)->nodeValue;
                // data e hora da mensagem
                $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                // numero do recibo, se o lote foi aceito
                // guardar o numero do recibo na base de dados pois devera ser usado
                // para verificar o estatus dos Lotes enviados
                $this->nRec = $doc->getElementsByTagName('nRec')->item(0)->nodeValue;
                $bRet = true;
            }          
        }
        else
        {
            $this->mErrorMsg[] = "EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _";
            $this->errorStatus = true;

        }        
        return $bRet;
    }

    /**
     * Solicita dados de situaÃ§ao de NF
     * FUNCIONAL !!
     * @param	string   $idNFe numerico com 44 digitos
     * @return	mixed	response from SOAP call
     * @access  public
     **/
    public function consultaNF($idNFe,$nRecibo='')
    {
        //-- Variavelde retorno do metodo
        $bRet = FALSE;
        $cabecXsdfile   = $this->setVersaoArqXSD('CabecMsg');
	    $cabecVer       = $this->setVersaoLayoutXSD('CabecMsg');
    	$isRetorno      = false;
    	$isRetornoRecibo = false;
    	
	    //-- Carga das variaveis da funÃ§ao do webservice
		$wsdl = 'NfeConsultaProtocolo';
		$dataXsdfile    = $this->setVersaoArqXSD($wsdl);
		$dataVer        = $this->setVersaoLayoutXSD($wsdl);

		//-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
		$param = '<consSitNFe  versao="'.$dataVer.'" xmlns="'.$this->URLnfe.'"><tpAmb>'.$this->getIdentAmbiente().'</tpAmb><xServ>CONSULTAR</xServ><chNFe>'.$idNFe.'</chNFe></consSitNFe>';
        $retorno = $this->sendSOAP($param, $wsdl);

        if (is_object($retorno)) {
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }

        if ($isRetorno===true)
        {
            echo "**************************************************************************************\n";
            echo "* 7 - Consulta pelo protocolo retornou dados  \n";
            echo "**************************************************************************************\n";
            if ($xmlresp == ''){
                //Houve uma falha na comunicação SOAP
                $this->errorMsg = 'Falha na comunicação SOAP.';
                return FALSE;
            }

            $doc->RetornoXMLSefaz=$xmlresp;
            
            // tratar dados de retorno
            $doc = new \DOMDocument(); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            
				
	        $this->tpAmb = $doc->getElementsByTagName('tpAmb')->item(0)->nodeValue;
	        $this->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
	        $this->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
	        $this->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
	        $this->cUF = $doc->getElementsByTagName('cUF')->item(0)->nodeValue;
	            
            if ( $this->cStat == '100' || $this->cStat == '101' || $this->cStat == '110' )
            {            	
                $bRet = TRUE;

                $this->chNFe = $doc->getElementsByTagName('chNFe')->item(0)->nodeValue;
                $this->digVal = $doc->getElementsByTagName('digVal')->item(0)->nodeValue;
                $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                $this->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;
                
                //-- ADICIONA A AUTORIZACAO AO XML
				$cAno_Mes_NFe=substr($this->getDataEmissao(),0,7);
				if (!is_dir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe))  {
					mkdir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe, 0777);
                    chmod($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe, 0777);
                }
                
                $cArquivo_Autorizado=$this->getPastaArquivoXML()."autorizado/$cAno_Mes_NFe/".$this->nProt.".xml";
                $this->setArquivoAutorizado($cArquivo_Autorizado);
                
                $cProtocolo   = '<protNFe versao="'.$this->getVersaoNFe().'">';
                // Informações do Protocolo de resposta. TAG a ser assinada
                $cProtocolo  .= '<infProt Id="ID'.$this->nProt.'">';
        	    $cProtocolo  .= '<tpAmb>'.$this->tpAmb.'</tpAmb>';
        	    $cProtocolo  .= '<verAplic>'.$this->verAplic.'</verAplic>';
        	    $cProtocolo  .= '<chNFe>'.$this->chNFe.'</chNFe>';
        	    $cProtocolo  .= '<dhRecbto>'.$this->dhRecbto.'</dhRecbto>';
        	    $cProtocolo  .= '<nProt>'.$this->nProt.'</nProt>';
        	    $cProtocolo  .= '<digVal>'.$this->digVal.'</digVal>';
        	    $cProtocolo  .= '<cStat>'.$this->cStat.'</cStat>';
        	    $cProtocolo  .= '<xMotivo>'.$this->xMotivo.'</xMotivo>';
        	    $cProtocolo  .= '</infProt>';
        	    $cProtocolo  .= '</protNFe>';

				if (empty($this->nProt))
				{
					return false;
				}

            	//-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração
            	$cArquivoAssinado = $this->getPastaArquivoXML().'assinado/NFe'.$idNFe.'.xml';
            	if (!file_exists($cArquivoAssinado))
            	{
                   echo "Arquivo não existe: $cArquivoAssinado";
                   exit;
            	}
        	    $cConteudo = file_get_contents($cArquivoAssinado);

                $cConteudo = substr($cConteudo,38,strlen($cConteudo)-38);
        	    ?><?php
                $cAutorizado = '<?xml version="1.0" encoding="UTF-8"?>';
        	    ?><?php
                $cAutorizado .= '<nfeProc versao="'.$this->getVersaoNFe().'" xmlns="http://www.portalfiscal.inf.br/nfe">';
        	    $cAutorizado .= $cConteudo;
                $cAutorizado .= $cProtocolo;
                $cAutorizado .= '</nfeProc>';

        	    file_put_contents($cArquivo_Autorizado, $cAutorizado);
        	    mysqli_query("update T007 set T007_Arquivo_XML='$cAutorizado' where T007_Id='".$this->getT007Id()."'");
        	    
            }
            else
            {
            	//-- Faz a Consulta pelo Recibo para tentar saber qual o motivo
            	//$this->errorStatus = true;
                echo "**************************************************************************************\n";
                echo "* 7 - Consulta pelo protocolo não retornou nada, consultando pelo Recibo \n";
                echo "**************************************************************************************\n";
            	$bRet=$this->consultaNFRecibo($idNFe,$nRecibo);
            }
        } else {
            $this->errorStatus = true;
            $this->errorMsg = 'EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS *';
        }

        return $bRet;
    }

    /**
     * Solicita dados de situacao da NF
     * FUNCIONAL !!
     * @param	string   $idNFe numerico com 44 digitos
     * @return	mixed	response from SOAP call
     * @access  public
     **/
    public function consultaChaveNF($idNFe, $gerarArquivoAutorizado = true)
    {
        //-- Variavelde retorno do metodo
        $bRet = FALSE;
        $cabecXsdfile   = $this->setVersaoArqXSD('CabecMsg');
	    $cabecVer       = $this->setVersaoLayoutXSD('CabecMsg');
    	$isRetorno      = false;
    	$isRetornoRecibo = false;
    	
	    //-- Carga das variaveis da funÃ§ao do webservice
		$wsdl = 'NfeConsultaProtocolo';
		$dataXsdfile    = $this->setVersaoArqXSD($wsdl);
		//$dataVer      = $this->setVersaoLayoutXSD($wsdl);
        $dataVer        = "4.00";

		//-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
		$param = '<consSitNFe  versao="'.$dataVer.'" xmlns="'.$this->URLnfe.'"><tpAmb>'.$this->getIdentAmbiente().'</tpAmb><xServ>CONSULTAR</xServ><chNFe>'.$idNFe.'</chNFe></consSitNFe>';
        $retorno = $this->sendSOAP($param, $wsdl);

        if (is_object($retorno)) {
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }

        if ($isRetorno===true)
        {
            if ($xmlresp == ''){
                echo "**************************************************************************************\n";
                echo "* 7 - Consulta pelo chave retornou dados  \n";
                echo "**************************************************************************************\n";
                //Houve uma falha na comunicação SOAP
                $this->errorMsg = 'Falha na comunicação SOAP.';
                return FALSE;
            }

            $this->RetornoXMLSefaz=$xmlresp;
            
            // tratar dados de retorno
            $doc = new \DOMDocument(); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            
				
	        $this->tpAmb = $doc->getElementsByTagName('tpAmb')->item(0)->nodeValue;
	        $this->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
	        $this->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
	        $this->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
	        $this->cUF = $doc->getElementsByTagName('cUF')->item(0)->nodeValue;
	            
            if ( $this->cStat == '100' || $this->cStat == '101' || $this->cStat == '110' )
            {            	
                $bRet = TRUE;

                $this->chNFe = $doc->getElementsByTagName('chNFe')->item(0)->nodeValue;
                $this->digVal = $doc->getElementsByTagName('digVal')->item(0)->nodeValue;
                $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                $this->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;
                
                if ($gerarArquivoAutorizado) {
                    //-- ADICIONA A AUTORIZACAO AO XML
                    $cAno_Mes_NFe=substr($this->getDataEmissao(),0,7);
                    if (!is_dir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe))  {
                        mkdir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe, 0777);
                        chmod($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe, 0777);
                    }

                    $cArquivo_Autorizado=$this->getPastaArquivoXML()."autorizado/$cAno_Mes_NFe/".$this->nProt.".xml";
                    $this->setArquivoAutorizado($cArquivo_Autorizado);

                    $cProtocolo   = '<protNFe versao="'.$this->getVersaoNFe().'">';
                    // Informações do Protocolo de resposta. TAG a ser assinada
                    $cProtocolo  .= '<infProt Id="ID'.$this->nProt.'">';
                    $cProtocolo  .= '<tpAmb>'.$this->tpAmb.'</tpAmb>';
                    $cProtocolo  .= '<verAplic>'.$this->verAplic.'</verAplic>';
                    $cProtocolo  .= '<chNFe>'.$this->chNFe.'</chNFe>';
                    $cProtocolo  .= '<dhRecbto>'.$this->dhRecbto.'</dhRecbto>';
                    $cProtocolo  .= '<nProt>'.$this->nProt.'</nProt>';
                    $cProtocolo  .= '<digVal>'.$this->digVal.'</digVal>';
                    $cProtocolo  .= '<cStat>'.$this->cStat.'</cStat>';
                    $cProtocolo  .= '<xMotivo>'.$this->xMotivo.'</xMotivo>';
                    $cProtocolo  .= '</infProt>';
                    $cProtocolo  .= '</protNFe>';

                    if (empty($this->nProt))
                    {
                        return false;
                    }

                    //-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração
                    $cArquivoAssinado = $this->getPastaArquivoXML().'assinado/NFe'.$idNFe.'.xml';
                    if (!file_exists($cArquivoAssinado))
                    {
                        $this->errorStatus = true;
                        $this->errorMsg = "Arquivo assinado não existe: $cArquivoAssinado";
                        return false;
                    }
                    $cConteudo = file_get_contents($cArquivoAssinado);

                    $cConteudo = substr($cConteudo,38,strlen($cConteudo)-38);
                    $cAutorizado = '<?xml version="1.0" encoding="UTF-8"?>';
                    $cAutorizado .= '<nfeProc versao="'.$this->getVersaoNFe().'" xmlns="http://www.portalfiscal.inf.br/nfe">';
                    $cAutorizado .= $cConteudo;
                    $cAutorizado .= $cProtocolo;
                    $cAutorizado .= '</nfeProc>';

                    file_put_contents($cArquivo_Autorizado, $cAutorizado);
                    mysqli_query("update T007 set T007_Arquivo_XML='$cAutorizado' where T007_Id='".$this->getT007Id()."'");
                }
        	    
            }
            else
            {
            	//-- Faz a Consulta pelo Recibo para tentar saber qual o motivo
            	//$this->errorStatus = true;
                echo "**************************************************************************************\n";
                echo "* 7 - Consulta pela chave e não retornou nada \n";
                echo "**************************************************************************************\n";
            	$bRet = FALSE;
            }
        } else {
            $this->errorStatus = true;
            $this->errorMsg = 'EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS *';
        }

        return $bRet;
    }


    public function consultaNFRecibo($idNFe,$nRecibo, $retorno = NULL)
    {
        //-- Variavelde retorno do metodo
        $bRet = FALSE;
        $cabecXsdfile   = $this->setVersaoArqXSD('CabecMsg');
	    $cabecVer       = $this->setVersaoLayoutXSD('CabecMsg');
    	$isRetorno      = false;
    	
        //-- Carga das variaveis da funÃ§ao do webservice
        $wsdl = 'NFeRetAutorizacao';
        $dataXsdfile    = $this->setVersaoArqXSD($wsdl);
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);

        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $param = '<consReciNFe versao="'. $dataVer.'"  xmlns="'.$this->URLnfe.'"><tpAmb>'. $this->getIdentAmbiente().'</tpAmb><nRec>'.$nRecibo .'</nRec></consReciNFe>';

        if (!$retorno) {
            $retorno = $this->sendSOAP($param, $wsdl);
        }

        if (is_object($retorno)) {
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }
    	
        if ($isRetorno===true)
        {
            if ($xmlresp == ''){
                //Houve uma falha na comunicação SOAP
                $this->errorMsg = 'Falha na comunicação SOAP.';
                return FALSE;
            }

            $doc->RetornoXMLSefaz=$xmlresp;

            // tratar dados de retorno
            $doc = new \DOMDocument(); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            
            $this->cStat2 = !empty($doc->getElementsByTagName('cStat')->item(0)->nodeValue) ? $doc->getElementsByTagName('cStat')->item(0)->nodeValue : '';
            if ($this->cStat2 == ''){
            	$this->errorStatus = true;
            	$this->errorMsg = 'Não Retornou um Status';
            	return false;
            }            
            $this->xMotivo2 = !empty($doc->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';

       		$this->errorMsg = $this->xMotivo2;										

			if ($this->cStat2 == '104')
			{
				$bRet=TRUE;
				$protNFe = $doc->getElementsByTagName('protNFe')->item(0);
				$infProt = $protNFe->getElementsByTagName('infProt')->item(0);
				
				$this->tpAmb    = $infProt->getElementsByTagName('tpAmb')->item(0)->nodeValue;
	        	$this->verAplic = $infProt->getElementsByTagName('verAplic')->item(0)->nodeValue;
				$this->chNFe    = $infProt->getElementsByTagName('chNFe')->item(0)->nodeValue;
				$this->dhRecbto = $infProt->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
				$this->nProt    = $infProt->getElementsByTagName('nProt')->item(0)->nodeValue;
				$this->digVal   = $infProt->getElementsByTagName('digVal')->item(0)->nodeValue;
				$this->cStat    = $infProt->getElementsByTagName('cStat')->item(0)->nodeValue;
	        	$this->xMotivo  = $infProt->getElementsByTagName('xMotivo')->item(0)->nodeValue;				

				if (empty($this->nProt))
				{
					return false;
				}
                //-- ADICIONA A AUTORIZACAO AO XML
				$cAno_Mes_NFe=substr($this->getDataEmissao(),0,7);
				if (!is_dir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe))  {
					mkdir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe, 0777);
                    chmod($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_NFe, 0777);
                }
				
                $cArquivo_Autorizado=$this->getPastaArquivoXML()."autorizado/$cAno_Mes_NFe/".$this->nProt.".xml";
                $this->setArquivoAutorizado($cArquivo_Autorizado);
                
        	    $cProtocolo   = '<protNFe versao="'.$this->getVersaoNFe().'">';
                $cProtocolo  .= '<infProt Id="ID'.$this->nProt.'">';
        	    $cProtocolo  .= '<tpAmb>'.$this->tpAmb.'</tpAmb>';
        	    $cProtocolo  .= '<verAplic>'.$this->verAplic.'</verAplic>';
        	    $cProtocolo  .= '<chNFe>'.$this->chNFe.'</chNFe>';
        	    $cProtocolo  .= '<dhRecbto>'.$this->dhRecbto.'</dhRecbto>';
        	    $cProtocolo  .= '<nProt>'.$this->nProt.'</nProt>';
        	    $cProtocolo  .= '<digVal>'.$this->digVal.'</digVal>';
        	    $cProtocolo  .= '<cStat>'.$this->cStat.'</cStat>';
        	    $cProtocolo  .= '<xMotivo>'.$this->xMotivo.'</xMotivo>';
        	    $cProtocolo  .= '</infProt>';
        	    $cProtocolo  .= '</protNFe>';

            	//-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração
            	$cArquivoAssinado = $this->getPastaArquivoXML().'assinado/NFe'.$idNFe.'.xml';
            	if (!file_exists($cArquivoAssinado))
            	{
                   echo "Arquivo não existe: $cArquivoAssinado";
                   exit;
            	}
        	    $cConteudo = file_get_contents($cArquivoAssinado);

                $cConteudo = substr($cConteudo,38,strlen($cConteudo)-38);
        	    ?><?php
                $cAutorizado = '<?xml version="1.0" encoding="UTF-8"?>';
        	    ?><?php
                $cAutorizado .= '<nfeProc versao="'.$this->getVersaoNFe().'" xmlns="http://www.portalfiscal.inf.br/nfe">';
        	    $cAutorizado .= $cConteudo;
                $cAutorizado .= $cProtocolo;
                $cAutorizado .= '</nfeProc>';

        	    file_put_contents($cArquivo_Autorizado, $cAutorizado);
        	    mysqli_query("update T007 set T007_Arquivo_XML='$cAutorizado' where T007_Id='".$this->getT007Id()."'");
				
			}
        }
        return $bRet;
    }
    

    public function consultaNFReciboLote($nRecibo)
    {                
        $this->setVersaoArqXSD('CabecMsg');
	    $this->setVersaoLayoutXSD('CabecMsg');    	
    	
        //-- Carga das variaveis da funÃ§ao do webservice
        $wsdl = 'NFeRetAutorizacao';
        $this->setVersaoArqXSD($wsdl);
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);

        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $param = '<consReciNFe versao="'. $dataVer.'"  xmlns="'.$this->URLnfe.'"><tpAmb>'. $this->getIdentAmbiente().'</tpAmb><nRec>'.$nRecibo .'</nRec></consReciNFe>';
                
        $retorno = $this->sendSOAP($param, $wsdl);
        $this->retornoEnviaNF = $retorno;

        if(!empty($retorno)) {
            // tratar dados de retorno
            $doc = new \DOMDocument(); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($retorno,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

                               
            $retorno = new \stdClass;
            // status do recebimento ou mensagem de erro
            $retorno->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $retorno->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            $retorno->nRec = $doc->getElementsByTagName('nRec')->item(0)->nodeValue;

            // Lote processado cStat = 104
            if ($retorno->cStat == '104') {
                $retorno->protNFe = array();
                foreach ($doc->getElementsByTagName('protNFe') as $node) {
                    $protNFe = new \stdClass;
                    $protNFe->tpAmb =       $node->getElementsByTagName('tpAmb')->item(0)->nodeValue;
                    $protNFe->verAplic =    $node->getElementsByTagName('verAplic')->item(0)->nodeValue;
                    $protNFe->chNFe =       $node->getElementsByTagName('chNFe')->item(0)->nodeValue;
                    $protNFe->dhRecbto =    $node->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                    $protNFe->nProt =       $node->getElementsByTagName('nProt')->item(0)->nodeValue;
                    $protNFe->digVal =      $node->getElementsByTagName('digVal')->item(0)->nodeValue;
                    $protNFe->cStat =       $node->getElementsByTagName('cStat')->item(0)->nodeValue;
                    $protNFe->xMotivo =     $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
                    
                    $retorno->protNFe[$protNFe->chNFe] = $protNFe;
                }
            }
            
            return $retorno;             
        } else {
            $this->errorStatus = true;
            $this->errorMsg = 'Falha na comunicação SOAP.';
            return false;
        }    	                
    }

    /**
     * Assinador TOTALMENTE baseado em PHP das NFe
     * este assinador somente utiliza comandos nativos do PHP para assinar
     * FUNCIONAL !!!! resulta em arquvo identico ao assinadorRS
     *
     * @param	string $nfe
     * @param   string $tagid TAG que devera ser assinada
     * @return	string XML assinada
     * @access  public
    **/
    public function assinaPHPNF($nfe, $tagid='infCanc',$taginicio='cancNFe', $outDir=''){
            //carrega as chaves do certifciado p12
            $key = file_get_contents($this->pathCert);
            $resp = openssl_pkcs12_read ($key,$x509certdata,$this->passKey);
            $rPrivkey = openssl_pkey_get_private($x509certdata['pkey']);
            $rPubkey =  openssl_pkey_get_public($x509certdata['cert']);

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
            $xmldoc->preservWhiteSpace = FALSE; //elimina espaÃ§os em branco
            $xmldoc->formatOutput = FALSE;
            // muito importante deixar ativadas as opÃ§oes para limpar os espacos em branco
            // e as tags vazias

            $xmldoc->loadXML("$nfe",LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            //extrair a tag com os dados a serem assinados
            $infNFe = $xmldoc->getElementsByTagName($tagid)->item(0);
            $id = trim($infNFe->getAttribute("Id"));
            $outname = $this->getPastaArquivoXML().'assinado/'.$id.'.xml';
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
            $xmldoc->preservWhiteSpace = FALSE; //elimina espaÃ§os em branco
            $xmldoc->formatOutput = FALSE;

            $xmldoc->loadXML($nfe,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            //obtem a tag com as informaÃ§oes da assinatura
            // esta informaÃ§oes e que serao assinadas
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
            $ret = $xmldoc->save($outname);
            return $nfe;
    }
    
    
    /**********************************************
     * Solicita inutilizaÃ§ao de uma serie de numeros de NF
     *
     * @param	string  $ano
     * @param   string  $nfSerie
     * @param   integer $numIni
     * @param   integer $numFim
     * @param   boolean $bSave
     * @return	boolean TRUE se sucesso FALSE se falha
     * FUNCIONAL !!
     * @access  public
     ************************************************/
    public function criarIdInutilizacao($ano,$nfSerie,$modelo,$numIni,$numFim,$xJust)
    {
        //Identificador da TAG a ser assinada formada
        //com Código da UF + Ano (2 posições) + CNPJ + modelo + série
        //+ nro inicial e nro final precedida do literal “ID”
        $id = 'ID'.$this->getCodigoUFEmitente().date('y').$this->getCnpjEmitente().$modelo.str_pad($nfSerie,3,'0',STR_PAD_LEFT).str_pad($numIni,9,'0',STR_PAD_LEFT).str_pad($numFim,9,'0',STR_PAD_LEFT);
        $this->id_inutilizacao=$id;
    }
    
    public function criarInutilizaNF($ano,$nfSerie,$modelo,$numIni,$numFim,$xJust)
    {
        $bRet = FALSE;
        // carga das variaveis da funÃ§ao do webservice
        $wsdl='NfeInutilizacao';
        $dataVer = $this->setVersaoLayoutXSD($wsdl);

        $nfeDadosMsg = '<?xml version="1.0" encoding="UTF-8"?><inutNFe xmlns="'.$this->URLnfe.'" versao="'.$dataVer.'"><infInut Id="'.$this->id_inutilizacao.'"><tpAmb>'.$this->getIdentAmbiente().'</tpAmb><xServ>INUTILIZAR</xServ><cUF>'.$this->getCodigoUFEmitente().'</cUF><ano>'.$ano.'</ano><CNPJ>'.$this->getCnpjEmitente().'</CNPJ><mod>'.$modelo.'</mod><serie>'.$nfSerie.'</serie><nNFIni>'.$numIni.'</nNFIni><nNFFin>'.$numFim.'</nNFFin><xJust>'.$xJust.'</xJust></infInut></inutNFe>';
        
        $cArquivo=$this->getPastaArquivoXML()."assinado/INUT_".$this->id_inutilizacao.".xml";

  	    if (file_put_contents($cArquivo, $nfeDadosMsg))
  	    {
  	       $bRet = TRUE;
  	    }
        return $bRet;
    }

    public function retornoInutilizaNF($ano,$nfSerie,$modelo,$numIni,$numFim,$xJust,$bSave=TRUE)
    {

        //variavel de retorno
        $bRet = FALSE;
        // carga das variaveis da funÃ§ao do webservice
        $wsdl='NfeInutilizacao';
        $cabecVer       = $this->setVersaoLayoutXSD('CabecMsg');
        $dataXsdfile    = $this->setVersaoArqXSD($wsdl);
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);
        $isRetorno      = false;
        
        $nfeCabecMsg = '<?xml version="1.0" encoding="UTF-8"?><cabecMsg versao="'.$cabecVer.'" xmlns="'.$this->URLnfe.'"><versaoDados>'.$dataVer.'</versaoDados></cabecMsg>';

       	$cArquivoAssinado = $this->getPastaArquivoXML().'assinado/INUT_assinado_'.$this->id_inutilizacao.'.xml';

        if ($this->modelo_certificado=='A1')
        {
            $cArquivo=$this->getPastaArquivoXML()."assinado/INUT_".$this->id_inutilizacao.".xml";
            $ponteiro=fopen($cArquivo, "r");
            $nfeDadosMsg=fread($ponteiro, filesize($cArquivo));

            //-- ADICIONA A ASSINATURA NO ARQUIVO
            $nfeDadosMsg = $this->assinaPHPNF($nfeDadosMsg, 'infInut','inutNFe');
            if (!@file_put_contents($cArquivoAssinado, $nfeDadosMsg))
            {
               echo "Falha ao criar o arquivo assinado: <b>$cArquivoAssinado</b>";
               exit;
            }
        }
        else
        {
            $ponteiro=fopen($cArquivoAssinado, "r");
            $nfeDadosMsg=fread($ponteiro, filesize($cArquivoAssinado));
        }

    	if (!file_exists($cArquivoAssinado))
    	{
           echo "Arquivo não existe.: $cArquivoAssinado";
           exit;
    	}
    	
        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $nfeDadosMsg = str_replace('<?xml version="1.0" encoding="utf-8"?'.'>','',$nfeDadosMsg);
        $nfeDadosMsg = str_replace('<?xml version="1.0" encoding="UTF-8"?'.'>','',$nfeDadosMsg);

        $retorno = $this->sendSOAP($nfeDadosMsg, $wsdl);

        if (is_object($retorno)) {
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }
    
        if ($isRetorno===true)
        {

            if ($xmlresp == ''){
                //Houve uma falha na comunicação SOAP
                $this->errorMsg = 'Falha na comunicação SOAP.';
                return FALSE;
            }

            // tratar dados de retorno
            $doc = new \DOMDocument(); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

            $this->versao = $doc->getElementsByTagName('versao')->item(0)->nodeValue;
            $infInut= $doc->getElementsByTagName('infInut')->item(0);
            //extrai o id da tag
            $id = trim($infInut->getAttribute("Id"));

            $this->tpAmb = $doc->getElementsByTagName('tpAmb')->item(0)->nodeValue;
            $this->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $this->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $this->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            $this->cUF = $doc->getElementsByTagName('cUF')->item(0)->nodeValue;

            /*
             * No retorno 563 a SEFAZ pode informar o protocolo da
             * inutilização já autorizada. Ele precisa ser lido mesmo quando
             * cStat não é 102 para que a recuperação consiga sincronizar a
             * situação local.
             */
            $nProt = $doc->getElementsByTagName('nProt')->item(0);
            if ($nProt) {
                $this->nProt = trim($nProt->nodeValue);
            }

            if ($this->cStat == '102')
            {
                $bRet = TRUE;
                $this->ano = $doc->getElementsByTagName('ano')->item(0)->nodeValue;
                $this->CNPJ = $doc->getElementsByTagName('CNPJ')->item(0)->nodeValue;
                $this->modelo = $doc->getElementsByTagName('mod')->item(0)->nodeValue;
                $this->serie = $doc->getElementsByTagName('serie')->item(0)->nodeValue;
                $this->nNFIni = $doc->getElementsByTagName('nNFIni')->item(0)->nodeValue;
                $this->nNFFin = $doc->getElementsByTagName('nNFFin')->item(0)->nodeValue;
                $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                $this->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;

                //-- ADICIONA A AUTORIZACAO AO XML
                $cConteudo   = str_replace("<infInut","$nfeDadosMsg<infInut",$xmlresp);
                $cArquivo_Autorizado = $this->getPastaArquivoXML().'/autorizado/INUT_'.$id.'.xml';
         	    file_put_contents($cArquivo_Autorizado, $cConteudo);
            }

        }
        else
        {
            $this->errorStatus = true;
            $this->errorMsg = 'Nao houve retorno do NuSoap!!';
        }
        return $bRet;
    }


    /**********************************************
     * Solicita o cancelamento de NF enviada
     *
     * @param	string  $idNFe ID da NFe com 44 digitos (sem o NFe na frente dos numeros)
     * @param   string  $protId Numero do protocolo de aceitaÃ§ao da NFe enviado anteriormente pelo SEFAZ
     * @return	boolean TRUE se sucesso ou FALSE se falha
     * @access  public
     ************************************************/
    public function criarCancelaNF($idNFe,$protId)
    {
        $bRet = FALSE;
        // carga das variaveis da funÃ§ao do webservice
        $wsdl = 'NfeCancelamento';
        //$wsdl = 'NfeRecepcaoEvento';
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);
        $nfeDadosMsg = '<?xml version="1.0" encoding="UTF-8"?><cancNFe xmlns="'.$this->URLnfe.'" versao="'.$dataVer.'"><infCanc Id="ID'.$idNFe.'"><tpAmb>'.$this->getIdentAmbiente().'</tpAmb><xServ>CANCELAR</xServ><chNFe>'.$idNFe.'</chNFe><nProt>'.$protId.'</nProt><xJust>'.$this->getMotivoCancelamento().'</xJust></infCanc></cancNFe>';
        ?><?php

        $cArquivo=$this->getPastaArquivoXML()."assinado/CANC_".$this->getT007Id().".xml";

  	    if (file_put_contents($cArquivo, $nfeDadosMsg))
  	    {
  	       $bRet = TRUE;
  	    }
        return $bRet;
    }

    public function retornoCancelaNF($idNFe,$protId)
    {
        //variavel de retorno
        $bRet = FALSE;
        // carga das variaveis da funÃ§ao do webservice
        $wsdl = 'NfeCancelamento';
        $cabecXsdfile   = $this->setVersaoArqXSD('CabecMsg');
        $cabecVer       = $this->setVersaoLayoutXSD('CabecMsg');
        $dataXsdfile    = $this->setVersaoArqXSD($wsdl);
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);
        $isRetorno      = false;
        
        $nfeCabecMsg = '<?xml version="1.0" encoding="UTF-8"?><cabecMsg versao="'.$cabecVer.'" xmlns="'.$this->URLnfe.'"><versaoDados>'.$dataVer.'</versaoDados></cabecMsg>';
        ?><?php

       	$cArquivoAssinado = $this->getPastaArquivoXML().'assinado/ID'.$idNFe.'.xml';

        if ($this->modelo_certificado=='A1')
        {
            $cArquivo=$this->getPastaArquivoXML()."assinado/CANC_".$this->getT007Id().".xml";
            $ponteiro=fopen($cArquivo, "r");
            $nfeDadosMsg=fread($ponteiro, filesize($cArquivo));

            //-- ADICIONA A ASSINATURA NO ARQUIVO
            $nfeDadosMsg = $this->assinaPHPNF($nfeDadosMsg, 'infCanc','cancNFe');
            if (!@file_put_contents($cArquivoAssinado, $nfeDadosMsg))
            {
               echo "Falha ao criar o arquivo assinado: <b>$cArquivoAssinado</b>";
               exit;
            }
        }
        else
        {
            $ponteiro=fopen($cArquivoAssinado, "r");
            $nfeDadosMsg=fread($ponteiro, filesize($cArquivoAssinado));
        }
        
        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $nfeDadosMsg = str_replace('<?xml version="1.0" encoding="utf-8"?'.'>','',$nfeDadosMsg);
        $nfeDadosMsg = str_replace('<?xml version="1.0" encoding="UTF-8"?'.'>','',$nfeDadosMsg);

        $retorno = $this->sendSOAP($nfeDadosMsg, $wsdl);
        
        if (is_object($retorno)){
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }

        if ($isRetorno===true)
        {
            if ($xmlresp == ''){
                //Houve uma falha na comunicação SOAP
                $this->errorMsg = 'Falha na comunicação SOAP.';
                return FALSE;
            }

            // tratar dados de retorno
            $doc = new \DOMDocument("1.0", "UTF-8"); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            if (@$doc->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG))
            {

                $this->versao = $doc->getElementsByTagName('versao')->item(0)->nodeValue;
                $infCanc = $doc->getElementsByTagName('infCanc');
                //extrai o id da tag
                //$id = trim($infCanc->getAttribute("Id"));

                $this->tpAmb = $doc->getElementsByTagName('tpAmb')->item(0)->nodeValue;
                $this->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
                $this->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
                $this->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
                $this->cUF = $doc->getElementsByTagName('cUF')->item(0)->nodeValue;

                if ( $this->cStat == '101' ) {
                    $bRet = TRUE;
                    $this->chNFe = $doc->getElementsByTagName('chNFe')->item(0)->nodeValue;
                    $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                    $this->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;

                    //-- ADICIONA A AUTORIZACAO AO XML
                    $cArquivo_Autorizado=$this->getPastaArquivoXML()."autorizado/ID".$this->nProt.".xml";

            	    $cProtocolo   = '<retCancNFe versao="1.07" xmlns="http://www.portalfiscal.inf.br/nfe">';
            	    $cProtocolo  .= '<infCanc Id="ID'.$this->nProt.'">';
            	    $cProtocolo  .= '<tpAmb>'.$this->tpAmb.'</tpAmb>';
            	    $cProtocolo  .= '<verAplic>'.$this->verAplic.'</verAplic>';
            	    $cProtocolo  .= '<cStat>'.$this->cStat.'</cStat>';
            	    $cProtocolo  .= '<xMotivo>'.$this->xMotivo.'</xMotivo>';
            	    $cProtocolo  .= '<cUF>'.$this->cUF.'</cUF>';
            	    $cProtocolo  .= '<chNFe>'.$this->chNFe.'</chNFe>';
            	    $cProtocolo  .= '<dhRecbto>'.$this->dhRecbto.'</dhRecbto>';
            	    $cProtocolo  .= '<nProt>'.$this->nProt.'</nProt>';
            	    $cProtocolo  .= '</infCanc>';
            	    $cProtocolo  .= '</retCancNFe>';

                	if (!file_exists($cArquivoAssinado))
                	{
                       echo "Arquivo não existe: $cArquivoAssinado";
                       exit;
                	}
            	    $cConteudo = file_get_contents($cArquivoAssinado);

                    $cConteudo = substr($cConteudo,38,strlen($cConteudo)-38);
            	    ?><?php
                    $cAutorizado = '<?xml version="1.0" encoding="UTF-8"?>';
            	    ?><?php
                    $cAutorizado .= '<procCancNFe versao="1.07" xmlns="http://www.portalfiscal.inf.br/nfe">';
                    $cAutorizado .= $cConteudo;
                    $cAutorizado .= $cProtocolo;
                    $cAutorizado .= '</procCancNFe >';

            	    file_put_contents($cArquivo_Autorizado, $cAutorizado);
                 }
            }
            else
            {
                $this->mErrorMsg[] = "Nao houve retorno do ".$this->setFunctionWDSL($wsdl).'Result';
                $this->errorStatus = true;
            }
        }
        else
        {
            $this->mErrorMsg[] = "EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS -";
            $this->errorStatus = true;
        }
        return $bRet;
    }

    /**********************************************
     * Solicita o cancelamento de NF enviada
     *
     * @param   string  $idNFe ID da NFe com 44 digitos (sem o NFe na frente dos numeros)
     * @param   string  $protId Numero do protocolo de aceitaÃ§ao da NFe enviado anteriormente pelo SEFAZ
     * @return  boolean TRUE se sucesso ou FALSE se falha
     * @access  public
     ************************************************/
    public function cancelaNF($idNFe,$protId)
    {
        //variavel de retorno
        $bRet = false;
        
        // carga das variaveis da funÃ§ao do webservice
        $wsdl = 'RecepcaoEvento';
        
        //$cabecVer       = $this->aVerxsd['CabecMsg'];
        //$dataVer        = $this->aVerxsd[$wsdl];        
        $cabecVer       = $this->setVersaoLayoutXSD('CabecMsg');
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);

        $cTipoDocumento = $this->getTipoDocAuxiliar();
               
        // Estabelece o codigo do tipo de evento CANCELAMENTO
        $tpEvento   = '110111';         
        $descEvento = 'Cancelamento';
        
        //montagem do namespace do serviço
        $namespace = $this->URLnfe.'/wsdl/NFeRecepcaoEvento4';

        log("Cancelamento namespace: " . $namespace);
        
        // Para cancelamento o numero sequencia do evento sempre será 1
        $nSeqEvento = '1'; 
        $numLote    = substr(str_replace(',','',number_format(microtime(true)*1000000,0)),0,15);

        // Data e hora do evento no formato AAAA-MM-DDTHH:MM:SSTZD (UTC)
        $dhEvento   = date('Y-m-d').'T'.date('H:i:s').$this->getTimeZone(); 
        
        $cOrgao     = $this->getCodigoUFEmitente();
        //if ($cTipoDocumento==3) $cOrgao=90;
                
        // de acordo com o manual versão 5 de março de 2012
        // 2 + 6 + 44 + 2 = 54 digitos
        // ID + tpEvento + chave da NF-e + nSeqEvento
        // garantir que existam 2 digitos em nSeqEvento para montar o ID com 54 digitos        
        
        if (strlen(trim($nSeqEvento))==1){
            $zenSeqEvento = str_pad($nSeqEvento, 2, "0", STR_PAD_LEFT);
        } else {
            $zenSeqEvento = trim($nSeqEvento);
        }
                
        $id_cancelamento = "ID".$tpEvento.$idNFe.$zenSeqEvento;
        
        $Ev  = '';
        $Ev .= "<evento xmlns=\"$this->URLnfe\" versao=\"$dataVer\">";
        $Ev .= "<infEvento Id=\"$id_cancelamento\">";
        $Ev .= "<cOrgao>$cOrgao</cOrgao>";
        $Ev .= "<tpAmb>".$this->getIdentAmbiente()."</tpAmb>";
        $Ev .= "<CNPJ>".$this->getCnpjEmitente()."</CNPJ>";
        $Ev .= "<chNFe>$idNFe</chNFe>";
        $Ev .= "<dhEvento>$dhEvento</dhEvento>";
        $Ev .= "<tpEvento>$tpEvento</tpEvento>";
        $Ev .= "<nSeqEvento>$nSeqEvento</nSeqEvento>";
        $Ev .= "<verEvento>$dataVer</verEvento>";
        $Ev .= "<detEvento versao=\"$dataVer\">";
        $Ev .= "<descEvento>$descEvento</descEvento>";
        $Ev .= "<nProt>$protId</nProt>";
        $Ev .= "<xJust>".$this->getMotivoCancelamento()."</xJust>";
        $Ev .= "</detEvento></infEvento></evento>";
       

        // Assina o XML de cancelamento
        $Ev = $this->assinaPHPNF($Ev, 'infEvento','evento');

        $Ev = str_replace('<?xml version="1.0"?>','', $Ev);
        $Ev = str_replace('<?xml version="1.0" encoding="utf-8"?>','', $Ev);
        $Ev = str_replace('<?xml version="1.0" encoding="UTF-8"?>','', $Ev);
        $Ev = str_replace(array("\r","\n","\s"),"", $Ev);

        //montagem dos dados
        $dados = '';
        $dados .= "<envEvento xmlns=\"$this->URLnfe\" versao=\"$dataVer\">";
        $dados .= "<idLote>$numLote</idLote>";
        $dados .= $Ev;
        $dados .= "</envEvento>";
                
        $nfeCabecMsg = "<nfeCabecMsg xmlns=\"$namespace\"><cUF>".$this->getCodigoUFEmitente()."</cUF><versaoDados>$dataVer</versaoDados></nfeCabecMsg>";
        $nfeDadosMsg = '<?xml version="1.0" encoding="UTF-8"?>'."<nfeDadosMsg xmlns=\"$namespace\">$dados</nfeDadosMsg>";       
                                
        //$cArquivoTemp=$this->getPastaArquivoXML()."assinado/CANC_".$id_cancelamento.".xml";
        $cArquivoTemp=$this->getPastaArquivoXML()."assinado/CANC_".$id_cancelamento.".xml";

        if (!file_put_contents($cArquivoTemp, $nfeDadosMsg))
        {
            $this->mErrorMsg[] = "IMPOSSIVEL GRAVAR O ARQUIVO: $cArquivo";
            $this->errorStatus = true;
            return false;           
        }

        $nfeDadosMsg = str_replace('<?xml version="1.0" encoding="utf-8"?'.'>','',$nfeDadosMsg);
        $nfeDadosMsg = str_replace('<?xml version="1.0" encoding="UTF-8"?'.'>','',$nfeDadosMsg);

        // Envia o arquivo....
        
        $xmlresp = $this->sendSOAP($nfeDadosMsg, $wsdl);

        // || $wsdl=="NfeRecepcaoEvento"
        /*
        if ($this->getUFEmitente()=='PR')
        {           
            if (!empty($retorno))
            {
               $xmlresp = utf8_encode($retorno);
               $isRetorno = true;
            }
        }
        else 
        {
            if (is_object($retorno))
            {
               $xmlresp = utf8_encode($retorno->any);
               $isRetorno = true;
            }               
        }        
        */

        //if ($isRetorno===true)        
        if ($xmlresp)
        {
                
            if ($xmlresp == ''){
                //Houve uma falha na comunicação SOAP
                $this->mErrorMsg[] = "Falha na comunicação SOAP.";
                return FALSE;
            }
            
            $bRet=$this->processarRetornoCancelamentoEvento($xmlresp,$Ev,$id_cancelamento);

            if ($bRet)
            {
                // elimina o arquivo temporario que foi criado para gerar o arquivo inicial
                unlink($cArquivoTemp);
            } else {
                $bRet[0] .= " - id xml " . $id_cancelamento . " - dhEvento " . $dhEvento;
            }
          
        }
        else
        {
            $this->mErrorMsg[] = "OCORREU UM ERRO AO TENTAR CANCELAR A NOTA FISCAL";
            $this->errorStatus = true;
            echo $this->soapDebug;            

            $bRet[0] .= " - id xml " . $id_cancelamento . " - dhEvento " . $dhEvento;

        }
        return $bRet;
            
        
    } //fim cancEvent

    public function processarRetornoCancelamentoEvento($xmlresp,$Ev,$id_cancelamento)
    {
        $xmlretEvent = new \DOMDocument('1.0', 'utf-8'); //cria objeto DOM
        $xmlretEvent->formatOutput = false;
        $xmlretEvent->preserveWhiteSpace = false;
        $xmlretEvent->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        
        // Verifica se o lote foi processado
        $retEnvEvento = $xmlretEvent->getElementsByTagName("retEnvEvento")->item(0);
        $cStat = !empty($retEnvEvento->getElementsByTagName('cStat')->item(0)->nodeValue) ? $retEnvEvento->getElementsByTagName('cStat')->item(0)->nodeValue : '';
                
        if ($cStat != 128 ){
            //se cStat <> 128 houve erro e o lote foi rejeitado
            $xMotivo = !empty($retEnvEvento->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $retEnvEvento->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';
            $xMotivo=utf8_decode(utf8_decode($xMotivo));
            
            $this->errorStatus = true;
            $this->mErrorMsg[] = "$cStat - $xMotivo";
            return false;
        }
        
        
        
        $retEvento = $xmlretEvent->getElementsByTagName("retEvento")->item(0);
        
        $cStat = !empty($retEvento->getElementsByTagName('cStat')->item(0)->nodeValue) ? $retEvento->getElementsByTagName('cStat')->item(0)->nodeValue : '';
        
        $xMotivo = !empty($retEvento->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $retEvento->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';      
        $xMotivo=utf8_decode(utf8_decode($xMotivo));
        
        if ($cStat == ''){
            // houve erro
            $this->errorStatus = true;
            $this->mErrorMsg[] = "cStat está em branco, houve erro na comunicação Soap verifique a mensagem de erro e o debug!!";
            return false;
        
        }
        
        //tratar erro de versão do XML
        /*
         if ($cStat == '238' || $cStat == '239'){
        $this->__trata239($retorno, $this->UF, $tpAmb, $servico, $versao);
        $this->errorStatus = true;
        $this->mErrorMsg[] = "Versão do arquivo XML não suportada no webservice!!";
        
        }
        */
        
        //erro no processamento cStat <> 135
        if ($cStat != 135 ){
            //se cStat <> 135 houve erro e o lote foi rejeitado
            $this->errorStatus = true;
            $this->mErrorMsg[] = "Retorno de ERRO: $cStat - $xMotivo";
            return false;
        }
        
        // Retorna a data e o numero do protocolo de cancelamento para guardar no banco de dados no programa que cancela a nota
        $this->dhRecbto = !empty($retEvento->getElementsByTagName('dhRegEvento')->item(0)->nodeValue) ? $retEvento->getElementsByTagName('dhRegEvento')->item(0)->nodeValue : '';
        $this->nProt = !empty($retEvento->getElementsByTagName('nProt')->item(0)->nodeValue) ? $retEvento->getElementsByTagName('nProt')->item(0)->nodeValue : '';
        
        //o evento foi aceito cStat == 135
        //carregar o evento
        $xmlenvEvento = new \DOMDocument('1.0', 'utf-8'); //cria objeto DOM
        $xmlenvEvento->formatOutput = false;
        $xmlenvEvento->preserveWhiteSpace = false;
        $xmlenvEvento->loadXML($Ev,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $evento = $xmlenvEvento->getElementsByTagName("evento")->item(0);
        
        //Processo completo solicitação + protocolo
        $xmlprocEvento = new \DOMDocument('1.0', 'utf-8'); //cria objeto DOM
        $xmlprocEvento->formatOutput = false;
        $xmlprocEvento->preserveWhiteSpace = false;
        
        //cria a tag retEnvEvento
        $retEnvEvento = $xmlprocEvento->createElement('retEnvEvento');
        $xmlprocEvento->appendChild($retEnvEvento);
        
        //estabele o atributo de versão
        $eventProc_att1 = $retEnvEvento->appendChild($xmlprocEvento->createAttribute('versao'));
        $eventProc_att1->appendChild($xmlprocEvento->createTextNode($versao));
        
        //estabelece o atributo xmlns
        $eventProc_att2 = $retEnvEvento->appendChild($xmlprocEvento->createAttribute('xmlns'));
        $eventProc_att2->appendChild($xmlprocEvento->createTextNode($this->URLnfe));
        
        //carrega o node evento
        $node1 = $xmlprocEvento->importNode($evento, true);
        $retEnvEvento->appendChild($node1);
        
        //carrega o node retEvento
        $node2 = $xmlprocEvento->importNode($retEvento, true);
        $retEnvEvento->appendChild($node2);
        
        //salva o xml como string em uma variável
        $procXML = $xmlprocEvento->saveXML();
        
        //remove as informações indesejadas
        $procXML = str_replace("xmlns:default=\"http://www.w3.org/2000/09/xmldsig#\"",'',$procXML);
        $procXML = str_replace('default:','',$procXML);
        $procXML = str_replace(':default','',$procXML);
        $procXML = str_replace("\n",'',$procXML);
        $procXML = str_replace("\r",'',$procXML);
        $procXML = str_replace("\s",'',$procXML);
        
        
        // Pega o ano e o mes da emissao da nota para posteriormente criar as pastas
        $cAno_Mes_NFe=substr($this->getDataEmissao(),0,7);
        
        // Verifica se a pasta do mes e ano ja existe,
        // Caso nao exista, ele cria e
        if (!is_dir($this->getPastaArquivoXML().'cancelado')) {
            mkdir($this->getPastaArquivoXML().'cancelado', 0777);
            chmod($this->getPastaArquivoXML().'cancelado', 0777);
        }
        if (!is_dir($this->getPastaArquivoXML().'cancelado/'.$cAno_Mes_NFe)) {
            mkdir($this->getPastaArquivoXML().'cancelado/'.$cAno_Mes_NFe, 0777);
            chmod($this->getPastaArquivoXML().'cancelado/'.$cAno_Mes_NFe, 0777);
        }
        
        
        // Monta o caminho do arquivo autorizado
        $arqName=$this->getPastaArquivoXML()."cancelado/".$cAno_Mes_NFe."/".$id_cancelamento.".xml";
        $this->arquivo_autorizado_cancelado=$arqName;
        
        // salva o arquivo xml autorizado
        if (!file_put_contents($arqName, $procXML)){
            $this->errorStatus = true;
            $this->mErrorMsg[] = "Falha na gravação do arquivo $arqName";
            return false;           
        }
        
        $this->conteudo_arquivo_cancelado=$procXML;
        
        return true;
    }
    
     /**
     * Converte o campo data time retornado pelo webservice
     * em um timestamp unix
     *
     * @param    string   $DH
     * @return   timestamp
     * @access   private
     **/
    private function convertTime($DH){
        if ($DH){
            $aDH = explode('T',$DH);
            $adDH = explode('-',$aDH[0]);
            $atDH = explode(':',$aDH[1]);
            $timestampDH = mktime($atDH[0],$atDH[1],$atDH[2],$adDH[1],$adDH[2],$adDH[0]);
            return $timestampDH;
        }
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
    /*protected function __sendSOAP2($urlsefaz,$namespace,$cabecalho,$dados,$metodo,$ambiente,$UF=''){
        
        if ($urlsefaz == ''){
            //não houve retorno
            $this->errMsg = 'URL do webservice não disponível.';
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
        /*
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
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 3);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($oCurl, CURLOPT_SSLCERT, $this->pubKEY);
        curl_setopt($oCurl, CURLOPT_SSLKEY, $this->priKEY);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER,$parametros);

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
            $this->errMsg = curl_error($oCurl) . $info['http_code'] . $cCode[$info['http_code']];
            $this->errStatus = true;
        } else {
            //houve retorno mas ainda pode ser uma mensagem de erro do webservice
            $this->errMsg = $info['http_code'] . $cCode[$info['http_code']];
            $this->errStatus = false;
        }
        curl_close($oCurl);

        return $xml;
    } //fim __sendSOAP2
    */

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
    protected function __sendSOAP2($urlsefaz,$namespace,$cabecalho,$dados,$metodo,$ambiente,$UF=''){
        if ($urlsefaz == ''){
            //não houve retorno
            $this->errMsg = 'URL do webservice não disponível.';
            $this->errStatus = true;
        }
        $this->retornoDebug .= "\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "* __sendSOAP2 - Inicio\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "\n";

        // inicio da ?requisição ou retorno? padrão SOAP 
        $data = '';
        $data .= '<?xml version="1.0" encoding="utf-8"?>';
        $data .= '<soap12:Envelope ';
        $data .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $data .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
        $data .= 'xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">';
        $data .= '<soap12:Header>';
        //$data .= $cabecalho;
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

        // cria um cabeçalho personalizado
        $parametros = Array('Content-Type: application/soap+xml;charset=utf-8;action="'.$namespace.'"','SOAPAction: "'.$metodo.'"',"Content-length: $tamanho"); // ."/".$metodo
        //$Arquivo_PublKey = $this->getCertificadoPEM()."_certificado_publ_key.pem";
        //$Arquivo_PrivKey = $this->getCertificadoPEM()."_certificado_priv_key.pem";        
        $_aspa = '"';

        $this->retornoDebug .= "\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "* __sendSOAP2 - Curl_init\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "\n";

        $oCurl = curl_init();
        if(is_array($this->aProxy)){
            // curl_setopt - define parâmetros da requisição
            // torna o canal libcurl todas as operações do proxy http.   
            curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
            // passa um valor para definir um tipo de proxy: HTTP Proxy. Default., HTTPS Proxy. (Added in 7.52.0 for OpenSSL, GnuTLS and NSS)
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, "CURLPROXY_HTTP");
            // configura o proxy para usar
            curl_setopt($oCurl, CURLOPT_PROXY, $this->aProxy['IP'].':'.$this->aProxy['PORT']);
            if( $this->aProxy['PASS'] != '' ){
                // nome de usuário e senha para autenticaçãop de proxy
                curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->aProxy['USER'].':'.$this->aProxy['PASS']);
                // configuração dos métodos de proxy http para tentar
                curl_setopt($oCurl, CURLOPT_PROXYAUTH, "CURLAUTH_BASIC");
            } //fim if senha proxy
        }//fim if aProxy
        // é fornecida a URL para ser usada.
        curl_setopt($oCurl, CURLOPT_URL, $urlsefaz.'');
        // é configurada o número da porta
        curl_setopt($oCurl, CURLOPT_PORT , 443);
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
        curl_setopt($oCurl, CURLOPT_SSLCERT, $this->pubKEY);
        // especifica o arquivo de chaves privado para o certificado do cliente TLS e SSL
        curl_setopt($oCurl, CURLOPT_SSLKEY, $this->priKEY);
        //curl_setopt($oCurl, CURLOPT_SSLCERT, $Arquivo_PublKey);
        //curl_setopt($oCurl, CURLOPT_SSLKEY, $Arquivo_PrivKey);
        // Solicita um HTTP POST
        curl_setopt($oCurl, CURLOPT_POST, 1);
        // especifica dados para POST no servidor
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        // forçará o Curl a não imprimir os resultados de sua consulta. Em vez disso, ele retornará os resultados como um valor de retorno de seqüência de curl_exec () ao invés do usual true / false. Observe que se houver um erro, falso ainda será o valor de retorno de curl_exec ().
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        // configura o cabeçalho http personalizado
        curl_setopt($oCurl, CURLOPT_HTTPHEADER,$parametros);
        // tempo máximo que o pedido pode levar
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 15);
        // tempo limite para a fase de conexão
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 15);
        //-- Carrega os certificados em um resource
        $this->retornoDebug .= "\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "* __sendSOAP2 - Carrega certificados em um resource - Curl_exec\n";
        $this->retornoDebug .= "*               url: $urlsefaz\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "\n";


        if(is_array($this->aProxy)){
            $this->retornoDebug .= "CURLOPT_HTTPPROXYTUNNEL, 1<br>";
            $this->retornoDebug .= "CURLOPT_PROXYTYPE, CURLPROXY_HTTP<br>";
            $this->retornoDebug .= "CURLOPT_PROXY" . $this->aProxy['IP'].':'.$this->aProxy['PORT'].'<br>';
            if( $this->aProxy['PASS'] != '' ){
                $this->retornoDebug .= "CURLOPT_PROXYUSERPWD". $this->aProxy['USER'].':'.$this->aProxy['PASS'].'<br>';
                $this->retornoDebug .= "CURLOPT_PROXYAUTH CURLAUTH_BASIC <br>";
            } //fim if senha proxy
        }//fim if aProxy
        $this->retornoDebug .= "CURLOPT_URL" . $urlsefaz.'<br>';
        $this->retornoDebug .= "CURLOPT_PORT 443 <br>";
        $this->retornoDebug .= "CURLOPT_VERBOSE  1 <br>";
        $this->retornoDebug .= "CURLOPT_HEADER  1 <br>"; //retorna o cabeçalho de resposta
        $this->retornoDebug .= "CURLOPT_SSLVERSION, 3 <br>";
        $this->retornoDebug .= "CURLOPT_SSL_VERIFYHOST, 0 <br>";
        $this->retornoDebug .= "CURLOPT_SSL_VERIFYPEER, 0 <br>";
        $this->retornoDebug .= "CURLOPT_SSLCERT". $this->pubKEY . "<br>";
        $this->retornoDebug .= "CURLOPT_SSLKEY". $this->priKEY . "<br>";
        //$this->retornoDebug .= "CURLOPT_SSLCERT, $Arquivo_PublKey);
        //$this->retornoDebug .= "CURLOPT_SSLKEY, $Arquivo_PrivKey);
        $this->retornoDebug .= "CURLOPT_POST, 1 <br>";
        $this->retornoDebug .= "CURLOPT_POSTFIELDS". $data . "<BR>";
        $this->retornoDebug .= "CURLOPT_RETURNTRANSFER, 1 <br>";
        $this->retornoDebug .= "CURLOPT_HTTPHEADER".$parametros.'<br>';
        $this->retornoDebug .= "CURLOPT_TIMEOUT 15 <br>";
        $this->retornoDebug .= "CURLOPT_CONNECTTIMEOUT, 15 <br>";


        $__xml = curl_exec($oCurl);

        $this->retornoDebug .= "\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "* __sendSOAP2 - Carrega Certificados em um Resource - Curl_getinfo ($__xml)\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "\n";

        $info = curl_getinfo($oCurl); //informações da conexão

        $this->retornoDebug .= "\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "* __sendSOAP2 - Continua\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "\n";

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
        $Requisicao = $this->quebraString($Requisicao);
        
        $Retorno = htmlspecialchars($xml,ENT_QUOTES);
        $Retorno = str_replace("&lt;","<br>&lt;",$Retorno);
        $Retorno = str_replace("<br>&lt;/","&lt;/",$Retorno);
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
        
        $this->retornoDebug .= "\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "* __sendSOAP2 - Seta soapDebug\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "\n";

        $this->soapDebug = $cDebug;        
        
        if ($__xml === false){
            //não houve retorno
            $this->errMsg = curl_error($oCurl) . $info['http_code'] . $cCode[$info['http_code']];
            $this->errStatus = true;
        } else {
            //houve retorno mas ainda pode ser uma mensagem de erro do webservice
            $this->errMsg = $info['http_code'] . $cCode[$info['http_code']];
            $this->errStatus = false;
        }
        curl_close($oCurl);
        $this->retornoDebug .= "\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "* __sendSOAP2 - Fecha Curl - Fim\n";
        $this->retornoDebug .= "**************************************************************************************\n";
        $this->retornoDebug .= "\n";

        return $xml;
    } //fim __sendSOAP2


    /**
     * __sendSOAP
     * Estabelece comunicaçao com servidor SOAP 1.1 ou 1.2 da SEFAZ,
     * usando as chaves publica e privada parametrizadas na contrução da classe.
     * Conforme Manual de Integração Versão 4.0.1
     *
     * @name __sendSOAP
     * @param string $urlsefaz
     * @param string $namespace
     * @param string $cabecalho
     * @param string $dados
     * @param string $metodo
     * @param numeric $ambiente tipo de ambiente 1 - produção e 2 - homologação
     * @param string $UF unidade da federação, necessário para diferenciar AM, MT e PR
     * @return mixed false se houve falha ou o retorno em xml do SEFAZ
     */
    protected function __sendSOAP($urlsefaz,$namespace,$cabecalho,$dados,$metodo,$ambiente,$UF=''){
        try {
            if(!class_exists("SoapClient")){
                $msg = "A classe SOAP não está disponível no PHP, veja a configuração.";
                throw new \nfephpException($msg);
            }
            //ativa retorno de erros soap
            use_soap_error_handler(true);
            //versão do SOAP
            $soapver = SOAP_1_2;
            if($ambiente == 1){
                $ambiente = 'producao';
            } else {
                $ambiente = 'homologacao';
            }
            //monta a terminação do URL
            switch ($metodo){
                case 'nfeRecepcaoLote2':
                    $usef = "_NFeRecepcao2.asmx";
                    break;
                case 'nfeRetRecepcao2':
                    $usef = "_NFeRetRecepcao2.asmx";
                    break;
                case 'nfeCancelamentoNF2':
                    $usef = "_NFeCancelamento2.asmx";
                    break;
                case 'nfeInutilizacaoNF2':
                    $usef = "_NFeInutilizacao2.asmx";
                    break;
                case 'nfeConsultaNF2':
                    $usef = "_NFeConsulta2.asmx";
                    break;
                case 'nfeStatusServicoNF2':
                    $usef = "_NFeStatusServico2.asmx";
                    break;
                case 'consultaCadastro':
                    $usef = "";
                    break;
            }

            //para os estados de AM, MT e PR é necessário usar wsdl baixado para acesso ao webservice
            if ($UF=='AM' || $UF=='MT' || $UF=='PR'){
                $urlsefaz = "$urlsefaz/wsdl/2.00/$ambiente/$UF$usef";
            }
            if ($this->enableSVAN){
                //se for SVAN montar o URL baseado no metodo e ambiente
                $urlsefaz = "$urlsefaz/wsdl/2.00/$ambiente/SVAN$usef";
            }
            //verificar se SCAN 
            if ($this->getTipoDocAuxiliar()==3){
                //se for SCAN montar o URL baseado no metodo e ambiente
                $urlsefaz = "$urlsefaz/wsdl/2.00/$ambiente/SCAN$usef";
            }
            if ($this->soapTimeout == 0){
                $tout = 999999;
            } else {
                $tout = $this->soapTimeout;
            }
            
            //completa a url do serviço para baixar o arquivo WSDL
            $URL = $urlsefaz.'?WSDL';
            $this->soapDebug = $urlsefaz;
            $options = array(
                    'encoding' => 'UTF-8',
                    'verifypeer' => false,
                    'verifyhost' => true,
                    'soap_version' => $soapver,
                    'style' => SOAP_DOCUMENT,
                    'use' => SOAP_LITERAL,
                    'local_cert' =>  $this->getCertificadoPEM()."_certificado.pem",
                    'trace' => true,
                    'compression' => 0,
                    'exceptions' => true,
                    'connection_timeout' => $tout,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'ssl_method' => SOAP_SSL_METHOD_TLS
            );          
            // se comunica com o server da sefaz, instancia a classe SOAP  
            $oSoapClient = new NFeSOAP2Client($URL,$options);
            // monta o cabeçalho da mensagem, representa uma varialvel ou objeto para uso com serviços SOAP
            // SoapVar -> serve para passar um "pedaço" do documento xml como um parâmetro de função, XSD_ANYXML é uma constante de codificação.
            $varCabec = new \SoapVar($cabecalho,XSD_ANYXML);
            // representa o SOAP header
            $header = new \SoapHeader($namespace,'nfeCabecMsg',$varCabec);
            //instancia o cabeçalho
            $oSoapClient->__setSoapHeaders($header);
            //monta o corpo da mensagem soap
            $varBody = new \SoapVar($dados,XSD_ANYXML);
            //faz a chamada ao metodo do webservices
            $resp = $oSoapClient->__soapCall($metodo, array($varBody) );
            if (is_soap_fault($resp)) {
                $soapFault = "SOAP Fault: (faultcode: {$resp->faultcode}, faultstring: {$resp->faultstring})";
            }
            $resposta = $oSoapClient->__getLastResponse();
            $this->soapDebug .= "\n" . $soapFault;
            $this->soapDebug .= "\n" . $oSoapClient->__getLastRequestHeaders();
            $this->soapDebug .= "\n" . $oSoapClient->__getLastRequest();
            $this->soapDebug .= "\n" . $oSoapClient->__getLastResponseHeaders();
            $this->soapDebug .= "\n" . $oSoapClient->__getLastResponse();
            
        } catch (nfephpException $e) {
            
            $this->errorStatus = true;
            $this->errorMsg = $e->getMessage();
            if ($this->exceptions) {
                throw $e;
            }
            return false;
        }
        return $resposta;
    } //fim __sendSOAP
    
    /**
     * __getNumLot
     * Obtêm o numero do último lote de envio
     *  
     * @version 1.01
     * @package NFePHP
     * @author    Roberto L. Machado <linux.rlm at gmail dot com>
     * @return numeric Numero do Lote
     */
    protected function __getNumLot(){
         $lotfile = $this->raizDir . 'config/numloteenvio.xml';
         $domLot = new \DomDocument;
         $domLot->load($lotfile);
         $num = $domLot->getElementsByTagName('num')->item(0)->nodeValue;
         if( is_numeric($num) ){
            return $num;
         } else {
             //arquivo não existe suponho que o numero então seja 1
             return 1;
         }
    }

    /**
     * __putNumLot
     * Grava o numero do lote de envio usado
     *
     * @version 1.01
     * @package NFePHP
     * @author    Roberto L. Machado <linux.rlm at gmail dot com>
     * @param numeric $num Inteiro com o numero do lote enviado
     * @return boolean true sucesso ou FALSO erro
     */
    protected function __putNumLot($num){
        if ( is_numeric($num) ){
            $lotfile = $this->raizDir . 'config/numloteenvio.xml';
            $numLot = '<?xml version="1.0" encoding="UTF-8"?><root><num>' . $num . '</num></root>';
            if (!file_put_contents($lotfile,$numLot) ) {
                return true;
            } else {
                return false;
            }
        }
    }
    
 /**
    * loadSEFAZ
    * Função para extrair o URL, nome do serviço e versão dos webservices das SEFAZ de
    * todos os Estados da Federação do arquivo urlWebServicesNFe.xml
    *
    * O arquivo xml é estruturado da seguinte forma :
    * <WS>
    *   <UF>
    *      <sigla>AC</sigla>
    *          <homologacao>
    *              <Recepcao service='nfeRecepcao' versao='1.10'>http:// .....
    *              ....
    *          </homologacao>
    *          <producao>
    *              <Recepcao service='nfeRecepcao' versao='1.10'>http:// ....
    *              ....
    *          </producao>
    *   </UF>
    *   <UF>
    *      ....
    * </WS>
    *
    * @name loadSEFAZ
    * @version 1.10
    * @package NFePHP
    * @author Roberto L. Machado <linux.rlm at gmail dot com>
    * @param  string $spathXML  Caminho completo para o arquivo xml
    * @param  string $tpAmb  Pode ser "2-homologacao" ou "1-producao"
    * @param  string $sUF       Sigla da Unidade da Federação (ex. SP, RS, etc..)
    * @return mixed             false se houve erro ou array com os dado do URLs das SEFAZ
    */
    public function loadSEFAZ($spathXML,$tpAmb,$sUF) {
        //verifica se o arquivo xml pode ser encontrado no caminho indicado
        if ( file_exists($spathXML) ) {
            //carrega o xml
            $xml = simplexml_load_file($spathXML);
        } else {
            //sai caso não possa localizar o xml
            return false;
        }
        $aUrl = null;
        //testa p parametro tpAmb
        if ($tpAmb == '1'){
            $sAmbiente = 'producao';
        } else {
            if ($tpAmb == '2'){
                $sAmbiente = 'homologacao';
            } else {
                $sAmbiente = $tpAmb;
            }
        }
        //extrai a variável cUF do lista
        $this->cUF = $this->cUFlist[$sUF];
        $alias = $this->aliaslist[$sUF];
        if ($alias == 'SVAN'){
            $this->enableSVAN = true;
        } else {
            $this->enableSVAN = false;
        }
        //estabelece a expressão xpath de busca
        $xpathExpression = "/WS/UF[sigla='" . $alias . "']/$sAmbiente";
        //para cada "nó" no xml que atenda aos critérios estabelecidos
        foreach ( $xml->xpath( $xpathExpression ) as $gUF ) {
            //para cada "nó filho" retonado
            foreach ( $gUF->children() as $child ) {
                $u = (string) $child[0];
                $aUrl[$child->getName()]['URL'] = $u;
                // em cada um desses nós pode haver atributos como a identificação
                // do nome do webservice e a sua versão
                foreach ( $child->attributes() as $a => $b) {
                    $aUrl[$child->getName()][$a] = (string) $b;
                }
            }
        }
        return $aUrl;
    } //fim loadSEFAZ
    
    public function quebraString($String,$nTamanho=80)
    {
    	$cRetorno='';
    	$mLinha = explode("<br>",$String);
    	for ($i=0;$i<count($mLinha);$i++)
    	{
    		if (strlen($mLinha[$i])>=$nTamanho)
    		{
    	   		for ($n = 0;$n<strlen($mLinha[$i]);$n+=$nTamanho)
           		{
               		$c=substr($mLinha[$i],$n,$nTamanho);
             		$cRetorno.=$c."<br>";
           		} 
			}
    		else
    		{
	    		$cRetorno.=$mLinha[$i]."<br>";
    		}			
    	}
    	return $cRetorno;
    }

    public function getTimeZone()
    {
     $cEstadoEmitente=$this->getUFEmitente();
     
     //estados que participam do horario de ver�o
     $aUFhv = array('BA','ES','GO','MG','MS','PR','RJ','RS','SP','SC','TO');
     
     $this->timeZone = '-03:00';
     
     // corrigir o timeZone
     if ($cEstadoEmitente == 'AC' ||
       $cEstadoEmitente == 'AM' ||
       $cEstadoEmitente == 'MT' ||
       $cEstadoEmitente == 'MS' ||
       $cEstadoEmitente == 'RO' ||
       $cEstadoEmitente == 'RR' ){
      $this->timeZone = '-04:00';
     }
     
     // verificar se estamos no hor�rio de ver�o *** depende da configura��o do servidor ***
     if (date('I') == 1){
      
      //estamos no horario de ver�o verificar se o estado est� incluso
      if(in_array($cEstadoEmitente, $aUFhv)) {
       $tz = (int) $this->timeZone;
       $tz++;
       $this->timeZone = '-'.sprintf("%02d",abs($tz)).':00'; //poderia ser obtido com date('P')
      }
      
     }//fim check horario verao
     
     return $this->timeZone;
     
    }

    public function substVariaveis($T007_Id, $texto) {
        // campos
        $campos = array();
        $sql = "DESC T007";
        $res = mysqli_query($sql);
        while ($row = mysql_fetch_assoc($res)) {
            $campos[strtoupper($row['Field'])] = $row['Type'];
        }
        // t007
        $sql = "SELECT * FROM T007 WHERE T007_Id = '{$T007_Id}'";
        $res = mysqli_query($sql);
        $row = mysql_fetch_assoc($res);
        // loop
        foreach ($row as $key => $value) {
            $key = strtoupper($key);
            if (strpos($campos[$key], 'decimal') !== false) {
                $value = number_format($value, 2, ',', '.');
            }
            $texto = str_replace("[{$key}]", $value, $texto);
        }
        return $texto;
    }

    function strtohex($string){
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    function validarXML($xml, $pathXSD = "") 
    {
        if (!is_file($pathXSD)) {
            return [false,"Arquivo XSD não encontrado"];
        }

        if (empty($xml)) {
            return [false, "Não foi informado o conteúdo do xml"];
        }

        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        libxml_use_internal_errors(true);

        if (!$dom->schemaValidate($pathXSD)) {
            $mensagens = [];
            foreach (libxml_get_errors() as $error) {
                $mensagens[] = $error->message;
            }

            libxml_clear_errors();
            return [false, $mensagens];
        }

        return [true, 'XML Válido'];
    }
}

/**
 * Classe complementar
 * necessária para a comunicação SOAP 1.2
 * Remove algumas tags para adequar a comunicação
 * ao padrão Ruindows utilizado
 *
 * @version 1.2
 * @package NFePHP
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 *
 */
class NFeSOAP2Client extends \SoapClient {
    function __doRequest($request, $location, $action, $version) {
        $request = str_replace(':ns1', '', $request);
        $request = str_replace('ns1:', '', $request);
        $request = str_replace("\n", '', $request);
        $request = str_replace("\r", '', $request);
        return parent::__doRequest($request, $location, $action, $version);
    }
}
?>
