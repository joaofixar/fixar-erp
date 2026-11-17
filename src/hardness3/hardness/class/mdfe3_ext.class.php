<?php
namespace hardness;

class classeMDFeExt
{
    /***************************************************
    *  Id do MDFe
    ***************************************************/
    public function setT276Id($x)
    {
        $this->T276_Id=$x;
    }

    public function setModeloCertificado($x)
    {
        $this->modelo_certificado=$x;
    }
    
    public function getT276Id()
    {
        return $this->T276_Id;
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
    *  Documento Auxiliar de Documento Fiscal Eletrônico
    ****************************************************/
    public function setTipoDocAuxiliar($x)
    {
        $this->tipo_documento_auxiliar=$x;
    }
    public function getTipoDocAuxiliar()
    {
        if ($this->tipo_documento_auxiliar!=1 && $this->tipo_documento_auxiliar!=2)
        {
            $this->mErrorMsg[] = "(tpEmis) Tipo de emissão inválido: ".$this->tipo_documento_auxiliar;
            $this->errorStatus = true;
        }
        return $this->tipo_documento_auxiliar;
    }

    /***************************************************
    *  Id do emitente do MDFe
    ***************************************************/
    public function setC004Id($x)
    {
        $this->C004_Id=$x;
    }
    public function getC004Id()
    {
        return $this->C004_Id;
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

    /***************************************************
    *  Versao do MDFe
    ***************************************************/
    public function setVersaoMDFe($x)
    {
        $this->versao_mdfe=$x;
    }
    public function getVersaoMDFe()
    {
        if (empty($this->versao_mdfe))
        {
           $this->mErrorMsg[] = "(versao-A02) Não foi definida a versao do mdfe";
           $this->errorStatus = true;
        }
        if (strlen($this->versao_mdfe)>4)
        {
           $this->mErrorMsg[] = "Tamanho do campo da versao do leiaute ultrapassou a 4 caracteres ";
           $this->errorStatus = true;
        }
        if (substr($this->versao_mdfe,-2)=='.')
        {
           $this->mErrorMsg[] = "Campo da versao do leiaute deve ter duas casas decimais separado por ponto ";
           $this->errorStatus = true;
        }
        return $this->versao_mdfe;
    }

    /****************************************************
    *  QR CODE
    ****************************************************/
    public function setQRCodeMDFe()
    {
        // 1° Parte parte - Endereço do site da Portal Nacional do MDF-e, seguido do caractere “?”;
        $primeiraParte = $this->setWebService("QR Code");
        $primeiraParte .="?";

        // 2° Parte - Parâmetros para consultar a chave de acesso de MDF-e separados pelo caractere “&”;
        $segundaParte = "chMDFe=" . str_replace("MDFe","",$this->getChaveAcessoMDFe()) . "&amp;";
        $segundaParte .= "tpAmb=" . $this->getIdentAmbiente();
        
        $this->QRCode_mdfe = $primeiraParte.$segundaParte;
    }
    
    public function getQRCodeMDFe()
    {
        return $this->QRCode_mdfe;
    }

    /****************************************************
    *  Modelo do MDFE
    ****************************************************/
    public function setModeloMDFe($x)
    {
        $this->modelo_mdfe=$x;
    }
    public function getModeloMDFe()
    {
        if (empty($this->modelo_mdfe))
        {
           $this->mErrorMsg[] = "Não foi definido o modelo do MDF-e";
           $this->errorStatus = true;
        }
        if (strlen($this->modelo_mdfe)>2)
        {
           $this->mErrorMsg[] = "Modelo do MDF-e ultrapassou 2 caracteres";
           $this->errorStatus = true;
        }
        return $this->modelo_mdfe;
    }
    
    /****************************************************
    *  Modal do MDFE
    ****************************************************/
    public function setModalMDFe($x)
    {
        $this->modal_mdfe=$x;
    }
    public function getModalMDFe()
    {
        if (empty($this->modal_mdfe))
        {
           $this->mErrorMsg[] = "Não foi definido o modal do MDF-e.";
           $this->errorStatus = true;
        }
        if (strlen($this->modal_mdfe)>2)
        {
           $this->mErrorMsg[] = "Modal do MDF-e ultrapassou 2 caracteres";
           $this->errorStatus = true;
        }
        return $this->modal_mdfe;
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
           $this->mErrorMsg[] = "(xMun) Cidade do emitente é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->municipio_emitente)>60)
        {
           $this->mErrorMsg[] = "(xMun) Cidade do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->municipio_emitente;
    }

    /****************************************************
    * Modalidade de transporte
    ****************************************************/
    public function setModalidadeTransporteMDFe($x)
    {
        $this->modalidade_MDFe=$x;
    }
    public function getModalidadeTransporteMDFe()
    {
        if (empty($this->modalidade_MDFe))
        {
           $this->mErrorMsg[] = "Não foi definido a modalidae de transporte do MDFe";
           $this->errorStatus = true;
        }
        if (strlen($this->modalidade_MDFe)>1)
        {
           $this->mErrorMsg[] = "Modalidade de transporte ultrapassou 1 caractere";
           $this->errorStatus = true;
        }
        return $this->modalidade_MDFe;
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
           $this->mErrorMsg[] = "(verProc) Não foi definida a versao do aplicativo emissor";
           $this->errorStatus = true;
        }
        if (strlen($this->versao_sistema)>20)
        {
           $this->mErrorMsg[] = "(verProc) Tamanho da versao do aplicativo emissor ultrapassou 20 caracteres";
           $this->errorStatus = true;
        }

        return $this->versao_sistema;
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
           $this->mErrorMsg[] = "Nome fantasia do emitente ultrapassou 60 caracteres";
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
           $this->mErrorMsg[] = "Logradouro do emitente é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->logradouro_emitente)>60)
        {
           $this->mErrorMsg[] = "Logradouro do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->logradouro_emitente;
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
           $this->mErrorMsg[] = "Não foi definido o numero do logradouro do emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_logradouro_emitente)>60)
        {
           $this->mErrorMsg[] = "Número do logradouro do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_logradouro_emitente;
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
           $this->mErrorMsg[] = "Bairro do emitente é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->bairro_emitente)>60)
        {
           $this->mErrorMsg[] = "Bairro do emitente ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->bairro_emitente;
    }

    /****************************************************
    *  Nome do Municipio do emitente
    ****************************************************/
    public function setMunicipioCarregamento($x)
    {
        $this->municipio_carregamento=$this->adicionarEspape($x);
    }
    public function getMunicipioCarregamento()
    {
        if (strlen($this->municipio_carregamento)<2)
        {
           $this->mErrorMsg[] = "Cidade do carregamento é menor que 2 caracteres";
           $this->errorStatus = true;
        }
        if (strlen($this->municipio_carregamento)>60)
        {
           $this->mErrorMsg[] = "Cidade do carregamento ultrapassou 60 caracteres";
           $this->errorStatus = true;
        }
        return $this->municipio_carregamento;
    }

    /****************************************************
    * Codigo do municipio para o emitente
    ****************************************************/
    public function setCodigoMunicipioCarregamento($x)
    {
        $this->codigo_municipio_carregamento=$x;
    }
    public function getCodigoMunicipioCarregamento()
    {
        if (empty($this->codigo_municipio_carregamento))
        {
           $this->mErrorMsg[] = "(cMun-C10) Não foi definido o codigo da cidade de carregamento";
           $this->errorStatus = true;
        }
        if (strlen($this->codigo_municipio_carregamento)>7)
        {
           $this->mErrorMsg[] = "(cMun-C10) Codigo da cidade de carregamento ultrapassou 7 caracteres";
           $this->errorStatus = true;
        }
        return $this->codigo_municipio_carregamento;
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
           $this->mErrorMsg[] = "Não foi definido o codigo da cidade para o emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->codigo_municipio_emitente)>7)
        {
           $this->mErrorMsg[] = "Código da cidade para o emitente ultrapassou 7 caracteres";
           $this->errorStatus = true;
        }
        return $this->codigo_municipio_emitente;
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
           $this->mErrorMsg[] = "CEP do emitente ultrapassou 8 caracteres";
           $this->errorStatus = true;
        }
        return $this->cep_emitente;
    }


    /****************************************************
    *  Código numérico que compõe a Chave de Acesso.
    *  Número aleatório gerado pelo emitente para cada MDF-e para evitar acessos indevidos da MDF-e.
    ****************************************************/
    public function setNumeroAleatorioChaveAcesso($x='')
    {
        if ($this->versao_mdfe=='3.00')
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

    /****************************************************
    *  Código interno do veículo
    ****************************************************/
    public function setCodigoInternoVeiculo($x){
        $this->codigo_veiculo=$x;
    }
    public function getCodigoInternoVeiculo(){
        if (strlen($this->codigo_veiculo)>10){
            $this->mErrorMsg[] = "Código interno do veículo possui mais de 10 caracteres: ".$this->codigo_veiculo;
            $this->errorStatus = true;
        }
        return $this->codigo_veiculo;
    }

    /****************************************************
    *  Processo de emissão do MDFe
    ****************************************************/
    public function setOrigemProcessoEmissao($x){
        $this->origem_processo_emissao=$x;
    }
    public function getOrigemProcessoEmissao(){
        if ($this->origem_processo_emissao!=0 && $this->origem_processo_emissao!=3){
            $this->mErrorMsg[] = "Processo de emissão do MDF-e é inválido: ".$this->origem_processo_emissao;
            $this->errorStatus = true;
        }
        return $this->origem_processo_emissao;
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
           $this->mErrorMsg[] = "Não foi definido o codigo da unidade federativa do emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->codigo_uf_emitente)>2)
        {
           $this->mErrorMsg[] = "Tamanho do codigo da unidade federativa do emitente ultrapassou 2 caracteres";
           $this->errorStatus = true;
        }
        return $this->codigo_uf_emitente;
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
    *  Sigla da UF de Carregamento
    ****************************************************/
    public function setUFCarregamento($x)
    {
        $this->sigla_uf_carregamento=$this->adicionarEspape($x);
    }
    public function getUFCarregamento()
    {
        if (empty($this->sigla_uf_carregamento))
        {
           $this->mErrorMsg[] = "(UF) Sigla da unidade federativa de carregamento";
           $this->errorStatus = true;
        }
        if (strlen($this->sigla_uf_carregamento)>2)
        {
           $this->mErrorMsg[] = "(UF) Sigla da unidade federativa de carregamento ultrapassou 2 caracteres";
           $this->errorStatus = true;
        }
        return $this->sigla_uf_carregamento;
    }

    /****************************************************
    *  Sigla da UF de Carregamento
    ****************************************************/
    public function setUFDescarregamento($x)
    {
        $this->sigla_uf_descarregamento=$this->adicionarEspape($x);
    }
    public function getUFDescarregamento()
    {
        if (empty($this->sigla_uf_descarregamento))
        {
           $this->mErrorMsg[] = "(UF) Sigla da unidade federativa de descarregamento";
           $this->errorStatus = true;
        }
        if (strlen($this->sigla_uf_descarregamento)>2)
        {
           $this->mErrorMsg[] = "(UF) Sigla da unidade federativa de carregamento ultrapassou 2 descaracteres";
           $this->errorStatus = true;
        }
        return $this->sigla_uf_descarregamento;
    }

    /****************************************************
    *  Sigla da UF de Carregamento
    ****************************************************/
    public function setCidadeDescarregamento($x)
    {
        $this->cidade_descarregamento=$this->adicionarEspape($x);
    }
    public function getCidadeDescarregamento()
    {
        if (empty($this->cidade_descarregamento))
        {
           $this->mErrorMsg[] = "Cidade de descarregamento não está preenchida.";
           $this->errorStatus = true;
        }
        return $this->cidade_descarregamento;
    }

    /****************************************************
    *  Data da emissao do MDFe
    ****************************************************/
    public function setDataEmissao($x)
    {
        $this->data_emissao=$x;
    }
    public function getDataEmissao()
    {
        if (empty($this->data_emissao))
        {
           $this->mErrorMsg[] = "Não foi definida a data de emissao do MDF-e";
           $this->errorStatus = true;
        }
        if (strlen($this->data_emissao)>25)
        {
           $this->mErrorMsg[] = "Campo data de emissao do MDF-e ultrapassou 25 caracteres " . $this->data_emissao;
           $this->errorStatus = true;
        }
        return $this->data_emissao;
    }

    /****************************************************
    *  Data da encerramento do MDFe
    ****************************************************/
    public function setDataEncerramento($x)
    {
        $this->data_emissao=$x;
    }
    public function getDataEncerramento()
    {
        if (empty($this->data_emissao))
        {
           $this->mErrorMsg[] = "Não foi definida a data de encerramento do MDF-e";
           $this->errorStatus = true;
        }
        return $this->data_emissao;
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
           $this->mErrorMsg[] = "Não foi definido o cnpj do emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->cnpj_emitente)!=14)
        {
           $this->mErrorMsg[] = "Cnpj do emitente esta incompleto: ".strlen($this->cnpj_emitente)." caracteres";
           $this->errorStatus = true;
        }
        return $this->cnpj_emitente;
    }

    /****************************************************
    *  Placa do Veículo
    ****************************************************/
    public function setPlaca($x)
    {
        //-- Limpa a Placa
        $x=str_replace("-","",$x);
        $x=str_replace(" ","",$x);
        $this->placa=$x;
    }
    public function getPlaca()
    {
        if (empty($this->placa)){
           $this->mErrorMsg[] = "Não foi definida a Placa do carro";
           $this->errorStatus = true;
        } else if (strlen($this->placa)!=7){
           $this->mErrorMsg[] = "Placa do carro ".$this->placa." está incorreta. ";
           $this->errorStatus = true;
        }
        return $this->placa;
    }

    /****************************************************
    *  Renavam do Veículo
    ****************************************************/
    public function setRenavam($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->renavam=$x;
    }
    public function getRenavam()
    {
        if (!empty($this->renavam) && strlen($this->renavam)!=14)
        {
           $this->mErrorMsg[] = "Renavam do carro ".$this->renavam."está incorreto ";
           $this->errorStatus = true;
        }
        return $this->renavam;
    }

    /****************************************************
    *  cnpj do Emitente
    ****************************************************/
    public function setCnpjSeguradora($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cnpj_seguradora=$x;
    }
    public function getCnpjSeguradora()
    {
        if (empty($this->cnpj_seguradora))
        {
           $this->mErrorMsg[] = "Não foi definido o cnpj do emitente";
           $this->errorStatus = true;
        }
        if (strlen($this->cnpj_seguradora)!=14)
        {
           $this->mErrorMsg[] = "Cnpj do emitente esta incompleto: ".strlen($this->cnpj_seguradora)." caracteres";
           $this->errorStatus = true;
        }
        return $this->cnpj_seguradora;
    }

    /****************************************************
    *  CPF Condutor
    ****************************************************/
    public function setCPFCondutor($x)
    {
        //-- Limpa o Cnpj
        $x=str_replace(".","",$x);
        $x=str_replace("-","",$x);
        $x=str_replace("/","",$x);
        $this->cpf_condutor=$x;
    }
    public function getCPFCondutor(){
        if (empty($this->cpf_condutor)){
           $this->mErrorMsg[] = "Não foi definido o CPF do condutor";
           $this->errorStatus = true;
        }
        if (strlen($this->cpf_condutor)!=11){
           $this->mErrorMsg[] = "CPF do condutor esta incompleto: ".strlen($this->cpf_condutor)." caracteres";
           $this->errorStatus = true;
        }
        return $this->cpf_condutor;
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
        if ($this->getOrigemProcessoEmissao()==0 || $this->getOrigemProcessoEmissao()==3)
        {
           if (empty($this->inscricao_estadual_emitente))
           {
              $this->mErrorMsg[] = "(IE) Campo Inscricao Estadual do emitente é obrigatorio";
              $this->errorStatus = true;
           }
        }
        if (strlen($this->inscricao_estadual_emitente)>14)
        {
           $this->mErrorMsg[] = "(IE) Inscricao Estadual do emitente ultrapassou 14 caracteres";
           $this->errorStatus = true;
        }
        return $this->inscricao_estadual_emitente;
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

    /****************************************************
    *  Serie da nota fiscal
    ****************************************************/
    public function setSerieMDFe($x)
    {
        $this->serie_mdfe=$x;
    }
    public function getSerieMDFe()
    {
        if (empty($this->serie_mdfe))
        {
           $this->mErrorMsg[] = "Não foi definido a serie da nota fiscal";
           $this->errorStatus = true;
        }
        if (strlen($this->serie_mdfe)>3)
        {
           $this->mErrorMsg[] = "Serie da nota fiscal ultrapassou 3 caracteres";
           $this->errorStatus = true;
        }
        return $this->serie_mdfe;
    }

    public function getNumeroAleatorioChaveAcesso()
    {
        if (empty($this->numero_aleatorio_chave_acesso))
        {
           $this->mErrorMsg[] = "Codigo numerico que compoe a chave de acesso nao foi definido";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_aleatorio_chave_acesso)>9)
        {
           $this->mErrorMsg[] = "Codigo numerico que compoe a chave de acesso ultrapassou 9 caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_aleatorio_chave_acesso;
    }

    /****************************************************
    *  Chave de Acesso da MDF-e
    ****************************************************/
    public function setChaveAcessoMDFe()
    {
        
        /****************************************************************************************************************************
        *  VERSAO 2.00
        *  Chave de Acesso da MDF-e composta por
        *  Código da UF + AAMM da emissão + CNPJ do Emitente + Modelo + Série + Número do MDFe + Forma Emissao + Código Numérico + DV.
        *       02      +       04        +        14        +   02   +  03   +        09      +       01      +       08        + 01
        *****************************************************************************************************************************/

        
        $cChaveAcesso  = $this->getCodigoUFEmitente();

        $cAnoMesEmissao = substr($this->getDataEmissao(),2,2).substr($this->getDataEmissao(),5,2);
        $cChaveAcesso .= $cAnoMesEmissao;
        $cChaveAcesso .= $this->getCnpjEmitente();

        $modeloMDFe      = $this->getModeloMDFe();
        $cChaveAcesso .= $modeloMDFe;

        $cChaveAcesso .= STR_PAD($this->getSerieMDFe(),3,'0',STR_PAD_LEFT);

        $numeroMDFe      = $this->getNumeroMDFe();
        $cChaveAcesso .= STR_PAD($numeroMDFe,9,'0',STR_PAD_LEFT);

        $cChaveAcesso .= $this->getTipoDocAuxiliar();

        $comparacaoChaveAcesso = $cChaveAcesso;

        $cChaveAcesso .= STR_PAD($this->getNumeroAleatorioChaveAcesso(),8,'0',STR_PAD_LEFT);
        
        $this->setDVChaveAcesso($this->criarDVChaveAcesso($cChaveAcesso));

        $cChaveAcesso .= $this->getDVChaveAcesso();
        
        $cChaveAcesso  = "MDFe" . $cChaveAcesso;
        
        $this->chave_acesso_mdfe = $cChaveAcesso;
    }
    public function getChaveAcessoMDFe()
    {
        return $this->chave_acesso_mdfe;
    }

    /****************************************************
    *  Digito verificado da Chave de Acesso da MDF-e
    ****************************************************/
    public function setDVChaveAcesso($x)
    {
        $this->dv_chave_acesso=$x;
    }
    public function getDVChaveAcesso()
    {
        if (strlen($this->dv_chave_acesso)==0)
        {
            $this->mErrorMsg[] = "Dígito verificador da chave de acesso não foi encontrado";
            $this->errorStatus = true;
        }
        if (strlen($this->dv_chave_acesso)>1)
        {
            $this->mErrorMsg[] = "Dígito verificador da chave de acesso ultrapassou 1 carácter";
            $this->errorStatus = true;
        }
        return $this->dv_chave_acesso;
    }

    /****************************************************
    *  Numero do MDFe
    ****************************************************/
    public function setNumeroMDFe($x)
    {
        $this->numero_mdfe=$this->adicionarEspape($x);
    }
    public function getNumeroMDFe()
    {
        if (strlen($this->numero_mdfe)<1)
        {
           $this->mErrorMsg[] = "Tamanho do Número do MDFe é menor que 1 caracter";
           $this->errorStatus = true;
        }
        if (strlen($this->numero_mdfe)>9)
        {
           $this->mErrorMsg[] = "Tamanho do Número do MDFe é maior que 9 caracteres";
           $this->errorStatus = true;
        }
        return $this->numero_mdfe;
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
            $nPonderacao = substr($xChaveAcesso,$x,1);
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
    *  Pasta do Arquivo XML
    ****************************************************/
    public function setPastaArquivoXML($x)
    {   
        //-- Cria pasta caso a mesma não exista
        if (!is_dir($x)) mkdir($x, 0777);
        if (substr($x,-1)!='/') $x.='/';
        $this->pasta_arquivo_xml=$x;
    }
    public function getPastaArquivoXML()
    {
        return $this->pasta_arquivo_xml;
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
            $this->mErrorMsg[] = "Identificação do ambiente é inválida: ".$this->identificacao_ambiente;
            $this->errorStatus = true;
        }
        return $this->identificacao_ambiente;
    }

    /****************************************************
    *  Tipo do Emitente  1 - Prestador de serviço de transporte 2 - Transportador de Carga Própria OBS: Deve ser preenchido com 2 para emitentes de NF-e e pelas transportadoras quando estiverem fazendo transporte de carga própria     
    ****************************************************/
    public function setTipoEmitente($x)
    {
        $this->tipo_emitente=$x;
    }
    public function getTipoEmitente()
    {
        if ($this->tipo_emitente!=1 && $this->tipo_emitente!=2)
        {
            $this->mErrorMsg[] = "Tipo do emitente é inválido: ".$this->tipo_emitente;
            $this->errorStatus = true;
        }
        return $this->tipo_emitente;
    }

    public function setModuloMDFe($x)
    {
        $this->modulo_MDFe=$x;
    }
    public function getModuloMDFe()
    {
        return $this->modulo_MDFe;
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

    //-- ARQUIVO ASSINADO
    public function setArquivoAssinado($x)
    {
       $this->arquivo_assinado=$x;
    }
    
    public function getArquivoAssinado()
    {
       return $this->arquivo_assinado;
    }

    /**
    * carregaCertP12
    *
    *
    *
    * @param   string $pathCert
    * @return  boolean TRUE se o certificado foi carregado e FALSE se nao
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
        if (empty($x))
        {
           //$this->mErrorMsg[] = "Não foi definida uma senha para o certificado!";
           //$this->errorStatus = true;
        }
        $this->passKey=$x;
    }

    /****************************************************
    *  Nome do Arquivo XML
    ****************************************************/
    public function getNomeArquivoXML()
    {
        $cArquivo_MDFe = $this->getPastaArquivoXML()."assinado/".$this->getNumeroMDFe().'_'.$this->getSerieMDFe().'.xml';
        return $cArquivo_MDFe;
    }

    // Função que elimnia caracteres especiais...
    public function adicionarEspape($x)
    {
        // Remover acentos
        $replace = array(
                'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A','Å'=>'A', 'Æ'=>'A', 
                'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a','å'=>'a', 'æ'=>'a', 
                'Ç'=>'C', 
                'ç' => 'c',
                'Ð' => 'D',
                'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', '&' => '&',
                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e',
                'Ì'=>'I', 'Í'=>'I', 'Î'=>'I','Ï'=>'I', 
                'ì'=>'i', 'í'=>'i', 'î'=>'i','ï'=>'i', 
                'Ñ'=>'N', 
                'ñ'=>'n',
                'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 
                'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 
                'Ù'=>'U', 'Ú'=>'U','Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
                'ù'=>'u', 'ú'=>'u','û'=>'u', 'ü'=>'u', 'ý'=>'Y',
                'º' => '.', 'ª' => '.', 'Ž' => 'z', 'ž' => 'z', 'ð' => 'o','Š' => 'S','š'=>''
        );
                                
        $x = strtr($x,$replace);  
        
        $x=trim($x);
        $x = preg_replace("/\s+/", " ",$x);

        ////-- ELIMINA CARACTERES ESPECIAIS
        define( "ACENTOS" , "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŒØŠÙÚÛÜÝŽÞàáâãäåæçèéêëìíîïðñòóôõö&œøšùúûüýžþºª&.$" );
        define( "NORMAL"  , "AAAAAAACEEEEIIIIDNOOOOO   UUUUY  aaaaaaaceeeeiiii noooooE   uuuuy  BCED$" );
        
        $x = strtr($x, ACENTOS, NORMAL);
 
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

    public function removerAcentos($x) {
        //-- ELIMINA CARACTERES ESPECIAIS
        $replace = array(
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A','Å'=>'A', 'Æ'=>'A', 
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a','å'=>'a', 'æ'=>'a', 
            'Ç'=>'C', 
            'ç' => 'c',
            'Ð' => 'D',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', '&' => '&',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e',
            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I','Ï'=>'I', 
            'ì'=>'i', 'í'=>'i', 'î'=>'i','ï'=>'i', 
            'Ñ'=>'N', 
            'ñ'=>'n',
            'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 
            'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 
            'Ù'=>'U', 'Ú'=>'U','Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
            'ù'=>'u', 'ú'=>'u','û'=>'u', 'ü'=>'u', 'ý'=>'Y',
            'º' => '.', 'ª' => '.', 'Ž' => 'z', 'ž' => 'z', 'ð' => 'o','Š' => 'S','š'=>''
        );
                                
        $x = strtr($x,$replace);  

        define( "ACENTOS" , "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŒØŠÙÚÛÜÝŽÞàáâãäåæçèéêëìíîïðñòóôõö&œøšùúûüýžþºª.$" );
        define( "NORMAL"  , "AAAAAAACEEEEIIIIDNOOOOO   UUUUY  aaaaaaaceeeeiiii noooooE   uuuuy  ...$" );
        $x=strtr($x, ACENTOS, NORMAL);
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

    /**
     * Remove os caracteres especiais do CNPJ
     * @param string $nr O CNPJ a ser limpo
     * @return string O CNPJ limpo, contendo apenas números
     */
    public function limpaCNPJouCPF(string $nr): string
    {
        //-- Limpa o Cnpj/Cpf
        $nr=str_replace(".","",$nr);
        $nr=str_replace("-","",$nr);
        $nr=str_replace("/","",$nr);
        return $nr;
    }

    public function setFunctionWDSL($cServico)
    {
        //-- ARQUIVOS REFERENTE A VERSAO 3.00
        $mFunctionWDSL['MDFeRecepcao']['3.00']="MDFeRecepcao";
        $mFunctionWDSL['MDFeRetRecepcao']['3.00']="MDFeRetRecepcao";
        $mFunctionWDSL['RecepcaoEvento']['3.00']="RecepcaoEvento";
        $mFunctionWDSL['MDFeConsulta']['3.00']="MDFeConsulta";
        $mFunctionWDSL['MDFeStatusServico']['3.00']="MDFeStatusServico";
        $mFunctionWDSL['MDFeConsNaoEnc']['3.00']="MDFeConsultaNaoEncerrados";


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

    public function setWebService($cMetodo='')
    {
        /*********************************************************
        * TODOS OS WEBSERVICES EXISTENTES ESTAO AQUI
        * ADICIONE MAIS CONFORME AS VERSOES FOREM FICANDO DISPONIVEIS
        **********************************************************/

        //-- PRODUCAO
        //$mListaWS['SVRS']['MDFeRecepcao']['3.00']['1']['58']="https://mdfe.svrs.rs.gov.br/ws/MDFeRecepcao/MDFeRecepcao.asmx";
        // Descotinuado modo assincrono, ajustado para modo sincrono
        $mListaWS['SVRS']['MDFeRecepcao']['3.00']['1']['58']="https://mdfe.svrs.rs.gov.br/ws/MDFeRecepcaoSinc/MDFeRecepcaoSinc.asmx";
        $mListaWS['SVRS']['MDFeRetRecepcao']['3.00']['1']['58']="https://mdfe.svrs.rs.gov.br/ws/MDFeRetRecepcao/MDFeRetRecepcao.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['3.00']['1']['58']="https://mdfe.svrs.rs.gov.br/ws/MDFeRecepcaoEvento/MDFeRecepcaoEvento.asmx";
        $mListaWS['SVRS']['MDFeConsulta']['3.00']['1']['58']="https://mdfe.svrs.rs.gov.br/ws/MDFeConsulta/MDFeConsulta.asmx";
        $mListaWS['SVRS']['MDFeStatusServico']['3.00']['1']['58']="https://mdfe.svrs.rs.gov.br/ws/MDFeStatusServico/MDFeStatusServico.asmx";
        $mListaWS['SVRS']['MDFeConsultaNaoEncerrados']['3.00']['1']['58']="https://mdfe.svrs.rs.gov.br/ws/MDFeConsNaoEnc/MDFeConsNaoEnc.asmx";
        $mListaWS['SVRS']['MDFeDistribuicaoDFe']['1.00']['1']['58']="https://mdfe.svrs.rs.gov.br/ws/MDFeDistribuicaoDFe/MDFeDistribuicaoDFe.asmx";
        $mListaWS['SVRS']['QR Code']['3.00']['1']['58']="https://dfe-portal.svrs.rs.gov.br/mdfe/qrCode";

        //-- HOMOLOGAÇÃO
        //$mListaWS['SVRS']['MDFeRecepcao']['3.00']['2']['58']="https://mdfe-homologacao.svrs.rs.gov.br/ws/MDFeRecepcao/MDFeRecepcao.asmx";
        // Descotinuado modo assincrono, ajustado para modo sincrono
        $mListaWS['SVRS']['MDFeRecepcao']['3.00']['2']['58']="https://mdfe-homologacao.svrs.rs.gov.br/ws/MDFeRecepcaoSinc/MDFeRecepcaoSinc.asmx";
        $mListaWS['SVRS']['MDFeRetRecepcao']['3.00']['2']['58']="https://mdfe-homologacao.svrs.rs.gov.br/ws/MDFeRetRecepcao/MDFeRetRecepcao.asmx";
        $mListaWS['SVRS']['RecepcaoEvento']['3.00']['2']['58']="https://mdfe-homologacao.svrs.rs.gov.br/ws/MDFeRecepcaoEvento/MDFeRecepcaoEvento.asmx";
        $mListaWS['SVRS']['MDFeConsulta']['3.00']['2']['58']="https://mdfe-homologacao.svrs.rs.gov.br/ws/MDFeConsulta/MDFeConsulta.asmx";
        $mListaWS['SVRS']['MDFeStatusServico']['3.00']['2']['58']="https://mdfe-homologacao.svrs.rs.gov.br/ws/MDFeStatusServico/MDFeStatusServico.asmx";
        $mListaWS['SVRS']['MDFeConsultaNaoEncerrados']['3.00']['2']['58']="https://mdfe-homologacao.svrs.rs.gov.br/ws/MDFeConsNaoEnc/MDFeConsNaoEnc.asmx";
        $mListaWS['SVRS']['MDFeDistribuicaoDFe']['1.00']['2']['58']="https://mdfe-homologacao.svrs.rs.gov.br/ws/MDFeDistribuicaoDFe/MDFeDistribuicaoDFe.asmx";
        $mListaWS['SVRS']['QR Code']['3.00']['2']['58']="https://dfe-portal.svrs.rs.gov.br/mdfe/qrCode";


        $cAmbiente=$this->getIdentAmbiente();
        $cTipoDocumento=$this->getTipoDocAuxiliar();
        $cEstadoEmitente=$this->getUFEmitente();
        $cVersao=$this->getVersaoMDFe();
        $cModelo=$this->getModeloMDFe();

        $cEstadoEmitente= 'SVRS';

        if (!empty($cEstadoEmitente) and !empty($cMetodo) and !empty($cVersao) and !empty($cAmbiente))
        {
           $cRetorno = $mListaWS[$cEstadoEmitente][$cMetodo][$cVersao][$cAmbiente][$cModelo];
           echo "1. Retorno: ".$cRetorno."\n".$cEstadoEmitente."\n".$cVersao."\n".$cAmbiente."\n".$cModelo."\n";
        }

        if (empty($cRetorno))
        {
            echo "2. Retorno: ".$cRetorno."<br />";
         //   die();

           $this->mErrorMsg[] = "Não foi identificado o ambiente do MDF-e. Estado Emitente: $cEstadoEmitente / Ambiente: $cAmbiente / Versao: $cVersao / Metodo: $cMetodo / Modelo: $cModelo";
           $this->errorStatus = true;
           return false;
        }
        else
        {
           return $cRetorno;
        }
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

        //-- Muito importante deixar ativadas as ocoes para limpar os espacos em branco e as tags vazias
        if (!$oAssinatura->load($this->getNomeArquivoXML(),LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG))
        {
           $this->mErrorMsg[] = "Não foi possivel abrir o arquivo:".$this->getNomeArquivoXML();
           $this->errorStatus = true;
           echo "********** nao foi possivel assinar";
        }

        //-- Carrega o node que sera assinado
        $oA01 = $oAssinatura->getElementsByTagName('infMDFe')->item(0);
        $oXS02 = $oAssinatura->getElementsByTagName('SignedInfo')->item(0);

        //-- Extrai o id do MDFe
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

        $sqlT007 = mysqli_query("SELECT C004_Identificacao_CSC, C004_Codigo_CSC FROM T276 LEFT JOIN C004 ON C004_Id=T276_C004_Id WHERE T276_Id = '{$this->T276_Id}'");
        $mT007 = mysql_fetch_assoc($sqlT007);

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
            $cArquivoAssinado = $this->getPastaArquivoXML().'assinado/'.$Id.'.xml';

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

    public function setVersaoArqXSD($cServico)
    {
        //-- ARQUIVOS REFERENTE A VERSAO 4.00
        $mArqXSD['MDFeRecepcao']['3.00']="enviMDFe_v3.00.xsd";
        $mArqXSD['MDFeConsulta']['3.00']="consSitMdfe_v3.00.xsd";
        $mArqXSD['MDFeRetRecepcao']['3.00']="consReciMdfe_v3.00.xsd";
        $mArqXSD['RecepcaoEvento']['3.00']="eventoMDFe_v3.00.xsd";
        $mArqXSD['MDFeStatusServico']['3.00']="consStatServ_v3.00.xsd";
        $mArqXSD['MDFeConsultaNaoEncerrados']['3.00']="consMDFeNaoEnc_v3.00.xsd";

        $cVersao=$this->getVersaoMDFe();

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

    public function setVersaoLayoutXSD($cServico){
        /********************************************************************
        * Versoes dos layouts xsd das funcoes do webservice
        ********************************************************************/

        //-- ARQUIVOS REFERENTE A VERSAO 3.00
        $mLayXSD['MDFeStatusServico']['3.00']="3.00";
        $mLayXSD['MDFeRecepcao']['3.00']="3.00";
        $mLayXSD['MDFeConsulta']['3.00']="3.00";
        $mLayXSD['MDFeRetRecepcao']['3.00']="3.00";
        $mLayXSD['RecepcaoEvento']['3.00']="3.00";
        $mLayXSD['MDFeConsultaNaoEncerrados']['3.00']="3.00";

        $cVersao=$this->getVersaoMDFe();
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

    public function quebraString($String,$nTamanho=80){
        $cRetorno='';
        $mLinha = explode("<br>",$String);
        for ($i=0;$i<count($mLinha);$i++){
            if (strlen($mLinha[$i])>=$nTamanho){
                for ($n = 0;$n<strlen($mLinha[$i]);$n+=$nTamanho){
                    $c=substr($mLinha[$i],$n,$nTamanho);
                    $cRetorno.=$c."<br>";
                } 
            }else{
                $cRetorno.=$mLinha[$i]."<br>";
            }           
        }
        return $cRetorno;
    }

    public function getTimeZone($evento = NULL){
        
        // ESSE MÉTODO NÃO ESTÁ SENDO UTILIZADO
        if($evento == 'Encerramento'){
            $cEstadoEmitente=$this->getUFDescarregamento();
        } else if($evento == 'Cancelamento'){
            $cEstadoEmitente=$this->getUFEmitente();
        }

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
        //return $this->timeZone;
        return date("P");
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
    protected function __sendSOAP2($urlsefaz,$namespace,$cabecalho,$dados,$metodo,$ambiente,$UF=''){
        if ($urlsefaz == ''){
            //não houve retorno
            $this->errMsg = 'URL do webservice não disponível.';
            $this->errStatus = true;
        }
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* __sendSOAP2 - Inicio\n";
        echo "**************************************************************************************\n";
        echo "\n";

        // inicio da ?requisição ou retorno? padrão SOAP 
        $data = '';
        $data .= '<?xml version="1.0" encoding="utf-8"?>';
        $data .= '<soap12:Envelope ';
        $data .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $data .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
        $data .= 'xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">';
        // Descotinuado modo assincrono, ajustado para modo sincrono
/*         $data .= '<soap12:Header>';
        $data .= $cabecalho;
        $data .= '</soap12:Header>';*/
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
        //die("\n\n\n-------------- PARAMETROS ----------- \n\n\n ".print_r($parametros));
        $_aspa = '"';
        echo "\n\n\n*****************";
        print_r($parametros);
        echo "*************\n\n\n";

        echo "\n";
        echo "**************************************************************************************\n";
        echo "* __sendSOAP2 - Curl_init\n";
        echo "**************************************************************************************\n";
        echo "\n";

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
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* __sendSOAP2 - Carrega certificados em um resource - Curl_exec\n";
        echo "*               url: $urlsefaz\n";
        echo "**************************************************************************************\n";
        echo "\n";

        if(is_array($this->aProxy)){
            echo "CURLOPT_HTTPPROXYTUNNEL, 1\n";
            echo "CURLOPT_PROXYTYPE, CURLPROXY_HTTP\n";
            echo "CURLOPT_PROXY" . $this->aProxy['IP'].':'.$this->aProxy['PORT'].'\n';
            if( $this->aProxy['PASS'] != '' ){
                echo "CURLOPT_PROXYUSERPWD". $this->aProxy['USER'].':'.$this->aProxy['PASS'].'\n';
                echo "CURLOPT_PROXYAUTH CURLAUTH_BASIC \n";
            } //fim if senha proxy
        }//fim if aProxy
        echo "CURLOPT_URL" . $urlsefaz.'\n';
        echo "CURLOPT_PORT 443 \n";
        echo "CURLOPT_VERBOSE  1 \n";
        echo "CURLOPT_HEADER  1 \n"; //retorna o cabeçalho de resposta
        echo "CURLOPT_SSLVERSION, 6 \n";
        echo "CURLOPT_SSL_VERIFYHOST, 0 \n";
        echo "CURLOPT_SSL_VERIFYPEER, 0 \n";
        echo "CURLOPT_SSLCERT". $this->pubKEY . "\n";
        echo "CURLOPT_SSLKEY". $this->priKEY . "\n";
        //echo "CURLOPT_SSLCERT, $Arquivo_PublKey);
        //echo "CURLOPT_SSLKEY, $Arquivo_PrivKey);
        echo "CURLOPT_POST, 1 \n";
        echo "CURLOPT_POSTFIELDS". $data . "\n";
        echo "CURLOPT_RETURNTRANSFER, 1 \n";
        echo "CURLOPT_HTTPHEADER".$parametros.'\n';
        echo "CURLOPT_TIMEOUT 15 \n";
        echo "CURLOPT_CONNECTTIMEOUT, 15 \n";

        $__xml = curl_exec($oCurl);

        echo "\n";
        echo "**************************************************************************************\n";
        echo "* __sendSOAP2 - Carrega certificados em um resource - Curl_getinfo ($__xml)\n";
        echo "**************************************************************************************\n";
        echo "\n";

        $info = curl_getinfo($oCurl); //informações da conexão
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* __sendSOAP2 - Continua\n";
        echo "**************************************************************************************\n";
        echo "\n";

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
        
          echo "\n";
          echo "**************************************************************************************\n";
          echo "* __sendSOAP2 - Seta soapDebug\n";
          echo "**************************************************************************************\n";
          echo "\n";

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
          echo "\n";
          echo "**************************************************************************************\n";
          echo "* __sendSOAP2 - Fecha Curl - Fim\n";
          echo "**************************************************************************************\n";
          echo "\n";

        return $xml;
    } //fim __sendSOAP2

    /**
    * Estabelece comunicaÃ§ao com servidor SOAP
    * FUNCIONAL !!!
    * @param    array   $param Matriz com o cabeÃ§alho e os dados da mensagem soap
    * @param    string  $wsdl DesignaÃ§ao do ServiÃ§o SOAP
    * @return   mixed  Array com a resposta do SOAP ou String do erro ou false
    * @access   private
    **/     
     
    private function sendSOAP($param, $wsdl){    
        // COMEÇAR AQUI
        $dataVer = $this->setVersaoLayoutXSD($wsdl);
        //-- Monta a url do serviço
        if (!$URL = $this->setWebService($wsdl)){
            echo "ws \n";
            return false;
        }
        if (substr($URL,-5)!='?WSDL'){
            echo "url \n";
            $xURL.=$URL.'?WSDL';
        }

        $ArquivoPEM_Padrao=basename($this->getCertificadoPEM());
        list($ArquivoPEM,$Extencao)=explode(".",$ArquivoPEM_Padrao);
        $ArquivoKEY_Padrao=str_replace("$ArquivoPEM_Padrao",$ArquivoPEM."_key.$Extencao",$this->getCertificadoPEM());
        $ArquivoCERT_Padrao=str_replace("$ArquivoPEM_Padrao",$ArquivoPEM."_cert.$Extencao",$this->getCertificadoPEM());

        //-- Verifica se existe o certificado hardness para enviar o xml
        $ArquivoKEY_Hardness=str_replace("$ArquivoPEM_Padrao",$ArquivoPEM."_key_hardness.$Extencao",$this->getCertificadoPEM());
        $ArquivoCERT_Hardness=str_replace("$ArquivoPEM_Padrao",$ArquivoPEM."_cert_hardness.$Extencao",$this->getCertificadoPEM());
        
        if (file_exists($ArquivoKEY_Hardness) and file_exists($ArquivoCERT_Hardness)){
            $this->pubKEY=$ArquivoCERT_Hardness;
            $this->priKEY=$ArquivoKEY_Hardness;
        }
        else{
            $this->pubKEY=$ArquivoCERT_Padrao;
            $this->priKEY=$ArquivoKEY_Padrao;           
        }   

        // TERMINAR AQUI
        if ($this->isSoap=='NUSOAP'){
            echo "\n";
            echo "**************************************************************************************\n";
            echo "* SENDSOAP - É NUSOAP - Inicia tentativa de conexao \n";
            echo "**************************************************************************************\n";
            echo "\n";
            try {

                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - Inicia conexao \n";
                echo "**************************************************************************************\n";
                echo "\n";
                // inicia a conexao SOAP
                // ele precisa passar como parâmetro a URL que será utilizada, no caso será utilizada a URL da web service
                $client = new \nusoap_client($xURL, true);
                $client->authtype         = 'certificate';
                $client->soap_defencoding = 'UTF-8';

                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - Seta parametros para conexao segura \n";
                echo "**************************************************************************************\n";
                echo "\n";
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
                   //echo "<font color=blue>request:".htmlspecialchars($param)."</font>";
                }
                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - Chama funcao webservice \n";
                echo "**************************************************************************************\n";
                echo "\n";
                // chama a funÃ§ao do webservice, passando os parametros
                // $this->setFunctionWDSL($wsdl) -> retorna o serviço e a versão da nfe, param -> trás os parâmetros retornados.
                $retorno = $client->call($this->setFunctionWDSL($wsdl), $param);

                $this->debug_str = htmlspecialchars($client->debug_str);

            }
            catch (Exception $ex){

                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - Erro \n";
                echo "**************************************************************************************\n";
                echo "\n";
                //-- em caso de erro retorne o mesmo
                if (!is_bool($client->getError()))
                {
                    $this->errorMsg = $client->getError();
                    echo "<div style='position:absolute;top:15;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>Erro : ".$client->getError()."</font></div>";
                    $this->errorStatus = true;
                }

            }

        } else {      
            //APAGAR - COMEÇAR AQUI         
            echo "\n";
            echo "**************************************************************************************\n";
            echo "* SENDSOAP - Não é NUSOAP \n";
            echo "**************************************************************************************\n";
            echo "\n";
            // Cria o arquivo unico do certificado (.PEM) unindo os dois arquivos PEM que foram gerados no upload do certificado PFX
            if (!file_exists($this->getCertificadoPEM()) and file_exists($ArquivoKEY_Padrao) and file_exists($ArquivoCERT_Padrao)){
                $Certificado1 = file_get_contents($ArquivoKEY_Padrao);
                $Certificado2 = file_get_contents($ArquivoCERT_Padrao);
                file_put_contents($this->getCertificadoPEM(),$Certificado1.$Certificado2);
            } elseif (!file_exists($ArquivoKEY_Padrao) or !file_exists($ArquivoCERT_Padrao)){
                $this->errorMsg = "Arquivo do certificado não encontrado";
                echo "<div style='position:absolute;top:35;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>Erro : Arquivo do certificado não encontrado</font></div>";
                echo "<div style='position:absolute;top:45;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>$ArquivoKEY_Padrao $ArquivoCERT_Padrao</font></div>";
                $this->errorStatus = true;
            } if (true) {            
                // CONTINUAR AQUI
                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - is true \n";
                echo "**************************************************************************************\n";
                echo "\n";
                
                $versao    = $this->setVersaoLayoutXSD($wsdl);

                // wsdl utilizada para informar a versão da mensagem submetida no XML 
                if ($wsdl=='MDFeRecepcao') {
                    // Descontinuado modo assincrono, ajustando para modo sincrono
                    //$namespace = 'http://www.portalfiscal.inf.br/mdfe/wsdl/MDFeRecepcao';
                    $namespace = 'http://www.portalfiscal.inf.br/mdfe/wsdl/MDFeRecepcaoSinc';
                    $wsdl = 'mdfeRecepcaoEvento';
                } elseif($wsdl=="MDFeConsulta") {
                    $namespace = 'http://www.portalfiscal.inf.br/mdfe/wsdl/MDFeConsulta';
                    $wsdl = 'mdfeConsultaMDF';
                } elseif($wsdl=="MDFeRetRecepcao") {
                    $namespace = 'http://www.portalfiscal.inf.br/mdfe/wsdl/MDFeRetRecepcao';
                    $wsdl = 'mdfeRetRecepcao';
                } elseif($wsdl=="RecepcaoEvento") {
                    $namespace = 'http://www.portalfiscal.inf.br/mdfe/wsdl/MDFeRecepcaoEvento';
                    $wsdl = 'mdfeRecepcaoEvento';
                } elseif($wsdl=="MDFeStatusServico") {
                    $namespace = 'http://www.portalfiscal.inf.br/mdfe/wsdl/MDFeStatusServico';
                    $wsdl = 'mdfeStatusServicoMDF';
                } elseif($wsdl=="MDFeConsultaNaoEncerrados") {
                    $namespace = 'http://www.portalfiscal.inf.br/mdfe/wsdl/MDFeConsNaoEnc';
                    $wsdl = 'mdfeConsNaoEnc';
                }
                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - Setou name space $namespace\n";
                echo "**************************************************************************************\n";
                echo "\n";

                $cabec = '<mdfeCabecMsg xmlns="'. $namespace . '"><cUF>'.$this->getCodigoUFEmitente().'</cUF><versaoDados>'.$versao.'</versaoDados></mdfeCabecMsg>';
                $dados = '<mdfeDadosMsg xmlns="'. $namespace . '">'.$param.'</mdfeDadosMsg>';

                //remove as tags xml que porventura tenham sido inclusas
                $dados = str_replace('<?xml version="1.0"?>','', $dados);
                $dados = str_replace('<?xml version="1.0" encoding="utf-8"?>','', $dados);
                //die("\n\n\nDADOS =   ".$dados."\n\n\n");

                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - __sendSOAP2\n";
                echo "**************************************************************************************\n";
                echo "\n";
                $retorno = $this->__sendSOAP2($URL, $namespace, $cabec, $dados, $wsdl, $this->getIdentAmbiente());
                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - Fim _sendSOAP2\n";
                echo "**************************************************************************************\n";
                echo "\n";

            } else {
                echo "\n";
                echo "**************************************************************************************\n";
                echo "* SENDSOAP - Else is true\n";
                echo "**************************************************************************************\n";
                echo "\n";
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
                    echo "\n";
                    echo "**************************************************************************************\n";
                    echo "* SENDSOAP - soapdebug\n";
                    echo "**************************************************************************************\n";
                    echo "\n";

                    echo $client->soapDebug;
                
                    //$headerWSDL = rtrim($wsdl, 'MDFe');
                    $headerWSDL = $wsdl;
                    $headerbody = array('cUF' => $this->getCodigoUFEmitente(), 'versaoDados' => $dataVer);
                    $headerbody = (object)array(
                        'ns2:cUF' => $this->getCodigoUFEmitente(),
                        'ns2:versaoDados' => $dataVer
                    );
                    echo "\n";
                    echo "**************************************************************************************\n";
                    echo "* SENDSOAP - soapheader\n";
                    echo "**************************************************************************************\n";
                    echo "\n";
                    $header = new \SOAPHeader('http://www.portalfiscal.inf.br/mdfe/wsdl/'.$headerWSDL, 'mdfeCabecMsg', $headerbody);
                    $client->__setSoapHeaders($header);
                    $mdfeDadosMsg = new \stdClass;
                    $mdfeDadosMsg->any = $param;
    
                    //-- CHAMA O METODO
                    if ($wsdl=='MDFeRecepcao'){
                        echo "\n";
                        echo "**************************************************************************************\n";
                        echo "* SENDSOAP - MdfeRecepcao\n";
                        echo "**************************************************************************************\n";
                        echo "\n";
                        $retorno = $client->enviaMDFe($mdfeDadosMsg);
                    }
                }
                catch(Exception $e)
                {
                    echo "\n";
                    echo "**************************************************************************************\n";
                    echo "* SENDSOAP - Exception e \n";
                    echo "**************************************************************************************\n";
                    echo "\n";
                    $this->errorMsg = $e->getMessage();
                    echo "<div style='position:absolute;top:55;left:1;'><font style='font-family:arial;font-size:10px;color:black;'>Erro : ".$e->getMessage()."</font></div>";
                    $this->errorStatus = true;
    
                }


                if (is_soap_fault($retorno))
                {
                    echo "\n";
                    echo "**************************************************************************************\n";
                    echo "* SENDSOAP - is soap fault \n";
                    echo "**************************************************************************************\n";
                    echo "\n";
                    echo "<br><hr><font color=blue>Requisição<br>".htmlspecialchars($param)."</font>";                
    
                    if (is_object($retorno))
                    {
                       $xmlresp = utf8_encode($retorno->any);
                       echo "<hr><font color=red>Retorno:<br>$retorno<br>".htmlspecialchars($xmlresp)."</font>";                   
                    }
                    else
                    {
                       echo "<hr><font color=red>Retorno:<br>Não Retornou NADA</font>";
                    }
                    echo "<hr><font color=gray>";
                    foreach($retorno as $erro) 
                    {
                        echo "\n";
                        echo "**************************************************************************************\n";
                        echo "* SENDSOAP - Erro \n";
                        echo "**************************************************************************************\n";
                        echo "\n";
                        echo htmlspecialchars($erro,ENT_QUOTES)."<br>";
                    }                
    
                }

            }
        }
        // CONTINUAR AQUI
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* SENDSOAP - Fim  \n";
        echo "**************************************************************************************\n";
        echo "\n";

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
     * @param   none
     * @return  boolean True se operacional e False se nao
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
        $wsdl = 'MDFeStatusServico';
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);
        $isRetorno      = false;

        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $param = '<consStatServ versao="'.$dataVer.'" xmlns="'.$this->URLmdfe.'">'.'<tpAmb>'.$this->getIdentAmbiente().'</tpAmb><xServ>STATUS</xServ></consStatServ>';
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
     * @param   array   $aNFe notas fiscais em xml uma em cada campo de uma string
     * @param   integer $idLote o id do lote e um numero que deve ser gerado pelo sistema
     *                          a cada envio mesmo que seja de apenas uma NFe usar banco
     *                          de dados
     * @return  boolean True se aceito o lote ou False de rejeitado
     * @access  public
     ************************************************/
    public function enviaMDFe($cArquivo,$idLote='1',$modo='0') // Descotinuado modo assincrono, ajustado para modo sincrono
    {

        echo "\n";
        echo "**************************************************************************************\n";
        echo "* Entrou enviaMDFE! \n";
        echo "**************************************************************************************\n";
        echo "\n";

        if (empty($cArquivo)){
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
        $wsdl = 'MDFeRecepcao';
        
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);
        $isRetorno      = false;

        // limpa a variavel
        $sMDFe = file_get_contents($cArquivo);

        //remover xml version="1.0" encoding=...MAIUSCULO E MINUSCULO
        $sMDFe = str_replace('<?xml version="1.0" encoding="utf-8"?'.'>','',$sMDFe);
        $sMDFe = str_replace('<?xml version="1.0" encoding="UTF-8"?'.'>','',$sMDFe);

        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $URLmdfe="http://www.portalfiscal.inf.br/mdfe";
        $param = '<enviMDFe xmlns="'.$URLmdfe.'" versao="'.$dataVer.'"><idLote>'.$idLote.'</idLote>'.$sMDFe.'</enviMDFe>';

        if($modo == '1'){
            // Descotinuado modo assincrono, ajustado para modo sincrono
            $param = base64_encode(gzencode($sMDFe, 9, FORCE_DEFLATE));
            //$param = base64_encode(gzencode($sMDFe));

        }

        $retorno = $this->sendSOAP($param, $wsdl);

        // Descotinuado modo assincrono, ajustado para modo sincrono
        $this->retornoEnviaMDFE = $retorno;

        if (is_object($retorno)) {
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }
            
        if ($isRetorno===true) {

            echo "\n";
            echo "**************************************************************************************\n";
            echo "* Retorno que estou tentando e true! \n {$xmlresp} \n";
            echo "**************************************************************************************\n";
            echo "\n";

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

            // status do recebimento ou mensagem de erro
            $this->cStat = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            //$this->tAmb = $doc->getElementsByTagName('tAmb')->item(0)->nodeValue;
            $this->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $this->xMotivo = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;

            echo "\n";
            echo "**************************************************************************************\n";
            echo "* cstat : {$this->cStat} \n";
            echo "**************************************************************************************\n";
            echo "\n";

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

            // Descotinuado modo assincrono, ajustado para modo sincrono
            if ($this->cStat == '100'){
                echo "\n";
                echo "**************************************************************************************\n";
                echo "* Retronou TRUE : \n";
                echo "**************************************************************************************\n";
                echo "\n";
                $bRet = TRUE;
            }

        }
        else
        {

            echo "\n";
            echo "**************************************************************************************\n";
            echo "* Retorno e false! \n";
            echo "**************************************************************************************\n";
            echo "\n";
            $this->mErrorMsg[] = "EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS _";
            $this->errorStatus = true;

        }

        return $bRet;
    }

    public function consultaMDFeRecibo($idMDFe,$nRecibo,$retorno = NULL)// Descotinuado modo assincrono, ajustado para modo sincrono
    {
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* Entrou consultaMDFeRecibo! \n";
        echo "**************************************************************************************\n";
        echo "\n";
        //-- Variavelde retorno do metodo
        $bRet = FALSE;
        $isRetorno      = false;
        
        //-- Carga das variaveis da funÃ§ao do webservice
        $wsdl = 'MDFeRetRecepcao';
        $dataXsdfile    = $this->setVersaoArqXSD($wsdl);
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);

        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $param = '<consReciMDFe versao="'. $dataVer.'" xmlns="http://www.portalfiscal.inf.br/mdfe"><tpAmb>'. $this->getIdentAmbiente().'</tpAmb><nRec>'.$nRecibo .'</nRec></consReciMDFe>';

         // Descotinuado modo assincrono, ajustado para modo sincrono
        if (!$retorno) {
            
            echo "\n";
            echo "**************************************************************************************\n";
            echo "* Retorno e null! \n";
            echo "**************************************************************************************\n";
            echo "\n";
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

            
            $this->cStat = !empty($doc->getElementsByTagName('cStat')->item(0)->nodeValue) ? $doc->getElementsByTagName('cStat')->item(0)->nodeValue : '';
            if ($this->cStat == ''){
                $this->errorStatus = true;
                $this->errorMsg = 'Não Retornou um Status';
                return false;
            }            
            $this->xMotivo = !empty($doc->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';
            $this->errorMsg = $this->xMotivo;                                      

            echo "\n\n\n****************************** STATUS ******************************";
            echo "\n\n\n****************************** $this->cStat ******************************";
            echo "\n\n\n****************************** STATUS ******************************";
            //if ($this->cStat == '104')
            // Descontinuado modo assincrono, ajustando para modo sincrono
            if ($this->cStat == '100')
            {

                echo "**************************************************************************************\n";
                echo "* 8 - MDF-e com Status =  100 \n";
                echo "**************************************************************************************\n";
                $bRet = TRUE;

                $this->chMDFe = $doc->getElementsByTagName('chMDFe')->item(0)->nodeValue;
                $this->digVal = $doc->getElementsByTagName('digVal')->item(0)->nodeValue;
                $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                $this->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;
                
                //-- ADICIONA A AUTORIZACAO AO XML
                $cAno_Mes_MDFe=substr($this->getDataEmissao(),0,7);
                if (!is_dir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_MDFe)) 
                    mkdir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_MDFe, 0777);
                
                $cArquivo_Autorizado=$this->getPastaArquivoXML()."autorizado/$cAno_Mes_MDFe/".$this->nProt.".xml";
                $this->setArquivoAutorizado($cArquivo_Autorizado);
                
                $cProtocolo   = '<protMDFe versao="'.$this->getVersaoMDFe().'">';
                // Informações do Protocolo de resposta. TAG a ser assinada
                $cProtocolo  .= '<infProt Id="ID'.$this->nProt.'">';
                $cProtocolo  .= '<tpAmb>'.$this->tpAmb.'</tpAmb>';
                $cProtocolo  .= '<verAplic>'.$this->verAplic.'</verAplic>';
                $cProtocolo  .= '<chMDFe>'.$this->chMDFe.'</chMDFe>';
                $cProtocolo  .= '<dhRecbto>'.$this->dhRecbto.'</dhRecbto>';
                $cProtocolo  .= '<nProt>'.$this->nProt.'</nProt>';
                $cProtocolo  .= '<digVal>'.$this->digVal.'</digVal>';
                $cProtocolo  .= '<cStat>'.$this->cStat.'</cStat>';
                $cProtocolo  .= '<xMotivo>'.$this->xMotivo.'</xMotivo>';
                $cProtocolo  .= '</infProt>';
                $cProtocolo  .= '</protMDFe>';

                if (empty($this->nProt)){
                    return false;
                }

                //-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração
                $cArquivoAssinado = $this->getPastaArquivoXML().'assinado/MDFe'.$idMDFe.'.xml';
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
                $cAutorizado .= '<mdfeProc versao="'.$this->getVersaoMDFe().'" xmlns="http://www.portalfiscal.inf.br/mdfe">';
                $cAutorizado .= $cConteudo;
                $cAutorizado .= $cProtocolo;
                $cAutorizado .= '</mdfeProc>';

                file_put_contents($cArquivo_Autorizado, $cAutorizado);
                mysqli_query("update T276 set T276_Arquivo_XML='$cAutorizado' where T276_Id='".$this->getT276Id()."'");
/*                 $bRet=TRUE;
                $protNFe = $doc->getElementsByTagName('protMDFe')->item(0);
                $infProt = $protNFe->getElementsByTagName('infProt')->item(0);
                
                $this->tpAmb    = $infProt->getElementsByTagName('tpAmb')->item(0)->nodeValue;
                $this->verAplic = $infProt->getElementsByTagName('verAplic')->item(0)->nodeValue;
                $this->chNFe    = $infProt->getElementsByTagName('chMDFe')->item(0)->nodeValue;
                $this->dhRecbto = $infProt->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                $this->nProt    = $infProt->getElementsByTagName('nProt')->item(0)->nodeValue;
                $this->digVal   = $infProt->getElementsByTagName('digVal')->item(0)->nodeValue;
                $this->cStat    = $infProt->getElementsByTagName('cStat')->item(0)->nodeValue;
                $this->xMotivo  = $infProt->getElementsByTagName('xMotivo')->item(0)->nodeValue;    
                
                echo "\n";
                echo "**************************************************************************************\n";
                echo "* Qual o protocolo : {$this->nProt}! \n";
                echo "**************************************************************************************\n";
                echo "\n";

                if (empty($this->nProt))
                {
                    return false;
                }
                //-- ADICIONA A AUTORIZACAO AO XML
                $cAno_Mes_MDFe=substr($this->getDataEmissao(),0,7);
                if (!is_dir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_MDFe)) 
                    mkdir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_MDFe, 0777);
                
                $cArquivo_Autorizado=$this->getPastaArquivoXML()."autorizado/$cAno_Mes_MDFe/".$this->nProt.".xml";
                $this->setArquivoAutorizado($cArquivo_Autorizado);
                
                $cProtocolo   = '<protNFe versao="'.$this->getVersaoMDFe().'">';
                $cProtocolo  .= '<infProt Id="ID'.$this->nProt.'">';
                $cProtocolo  .= '<tpAmb>'.$this->tpAmb.'</tpAmb>';
                $cProtocolo  .= '<verAplic>'.$this->verAplic.'</verAplic>';
                $cProtocolo  .= '<chMDFe>'.$this->chMDFe.'</chMDFe>';
                $cProtocolo  .= '<dhRecbto>'.$this->dhRecbto.'</dhRecbto>';
                $cProtocolo  .= '<nProt>'.$this->nProt.'</nProt>';
                $cProtocolo  .= '<digVal>'.$this->digVal.'</digVal>';
                $cProtocolo  .= '<cStat>'.$this->cStat.'</cStat>';
                $cProtocolo  .= '<xMotivo>'.$this->xMotivo.'</xMotivo>';
                $cProtocolo  .= '</infProt>';
                $cProtocolo  .= '</protNFe>';

                //-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração
                $cArquivoAssinado = $this->getPastaArquivoXML().'assinado/MDFe'.$idNFe.'.xml';
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
                $cAutorizado .= '<mdfeProc versao="'.$this->getVersaoMDFe().'" xmlns="http://www.portalfiscal.inf.br/mdfe">';
                $cAutorizado .= $cConteudo;
                $cAutorizado .= $cProtocolo;
                $cAutorizado .= '</mdfeProc>';

                file_put_contents($cArquivo_Autorizado, $cAutorizado);
                mysqli_query("update T276 set T276_Arquivo_XML='$cAutorizado' where T276_Id='".$this->getT276Id()."'"); */
            }
        }
        return $bRet;
    }

    /**
     * Solicita dados de situaÃ§ao do MDFe
     * FUNCIONAL !!
     * @param   string   $idMDFe numerico com 44 digitos
     * @return  mixed   response from SOAP call
     * @access  public
     **/
    public function consultaMDFe($idMDFe,$nRecibo='', $evento='')
    {   
        //-- Variavelde retorno do metodo
        $bRet = FALSE;
        $isRetorno      = false;
        $isRetornoRecibo = false;
        
        //-- Carga das variaveis da funÃ§ao do webservice
        $wsdl = 'MDFeConsulta';
        $dataXsdfile    = $this->setVersaoArqXSD($wsdl);
        $dataVer        = $this->setVersaoLayoutXSD('MDFeConsulta');

        //-- CHAMADA DO WEBSERVICE PARA SOAP NATIVO
        $param = '<consSitMDFe versao="'.$dataVer.'" xmlns="http://www.portalfiscal.inf.br/mdfe"><tpAmb>'.$this->getIdentAmbiente().'</tpAmb><xServ>CONSULTAR</xServ><chMDFe>'.$idMDFe.'</chMDFe></consSitMDFe>';
        $retorno = $this->sendSOAP($param, $wsdl);
        if (is_object($retorno)) {
            $xmlresp = utf8_encode($retorno->any);
            $isRetorno = true;
        } else if (!empty($retorno)) {
            $xmlresp = utf8_encode($retorno);
            $isRetorno = true;
        }
        if ($isRetorno===true){
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
            
            // 100 - Autorizado o uso do MDF-e, 101 - Cancelamento de MDF-e homologado e 132 - Encerramento de MDF-e Homologado
            if ( $this->cStat == '100'){               
                echo "**************************************************************************************\n";
                echo "* 8 - MDF-e com Status =  100 \n";
                echo "**************************************************************************************\n";
                $bRet = TRUE;

                $this->chMDFe = $doc->getElementsByTagName('chMDFe')->item(0)->nodeValue;
                $this->digVal = $doc->getElementsByTagName('digVal')->item(0)->nodeValue;
                $this->dhRecbto = $doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                $this->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;
                
                //-- ADICIONA A AUTORIZACAO AO XML
                $cAno_Mes_MDFe=substr($this->getDataEmissao(),0,7);
                if (!is_dir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_MDFe)) 
                    mkdir($this->getPastaArquivoXML().'autorizado/'.$cAno_Mes_MDFe, 0777);
                
                $cArquivo_Autorizado=$this->getPastaArquivoXML()."autorizado/$cAno_Mes_MDFe/".$this->nProt.".xml";
                $this->setArquivoAutorizado($cArquivo_Autorizado);
                
                $cProtocolo   = '<protMDFe versao="'.$this->getVersaoMDFe().'">';
                // Informações do Protocolo de resposta. TAG a ser assinada
                $cProtocolo  .= '<infProt Id="ID'.$this->nProt.'">';
                $cProtocolo  .= '<tpAmb>'.$this->tpAmb.'</tpAmb>';
                $cProtocolo  .= '<verAplic>'.$this->verAplic.'</verAplic>';
                $cProtocolo  .= '<chMDFe>'.$this->chMDFe.'</chMDFe>';
                $cProtocolo  .= '<dhRecbto>'.$this->dhRecbto.'</dhRecbto>';
                $cProtocolo  .= '<nProt>'.$this->nProt.'</nProt>';
                $cProtocolo  .= '<digVal>'.$this->digVal.'</digVal>';
                $cProtocolo  .= '<cStat>'.$this->cStat.'</cStat>';
                $cProtocolo  .= '<xMotivo>'.$this->xMotivo.'</xMotivo>';
                $cProtocolo  .= '</infProt>';
                $cProtocolo  .= '</protMDFe>';

                if (empty($this->nProt)){
                    return false;
                }

                //-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração
                $cArquivoAssinado = $this->getPastaArquivoXML().'assinado/MDFe'.$idMDFe.'.xml';
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
                $cAutorizado .= '<mdfeProc versao="'.$this->getVersaoMDFe().'" xmlns="http://www.portalfiscal.inf.br/mdfe">';
                $cAutorizado .= $cConteudo;
                $cAutorizado .= $cProtocolo;
                $cAutorizado .= '</mdfeProc>';

                file_put_contents($cArquivo_Autorizado, $cAutorizado);
                mysqli_query("update T276 set T276_Arquivo_XML='$cAutorizado' where T276_Id='".$this->getT276Id()."'");
                
            }else if($this->cStat == '132' || $this->cStat == '101'){
                echo "**************************************************************************************\n";
                echo "* 8 - MDF-e com Status =  $this->cStat \n";
                echo "**************************************************************************************\n";
                $bRet = TRUE;

                $this->chMDFe = $doc->getElementsByTagName('chMDFe')->item(0)->nodeValue;
                $this->digVal = $doc->getElementsByTagName('digVal')->item(0)->nodeValue;
                $this->dhRegEvento = $doc->getElementsByTagName('dhRegEvento')->item(0)->nodeValue;
                $this->nProt = $doc->getElementsByTagName('nProt')->item(0)->nodeValue;
                $this->cOrgao = $doc->getElementsByTagName('cOrgao')->item(0)->nodeValue;
                $this->tpEvento = $doc->getElementsByTagName('tpEvento')->item(0)->nodeValue;
                $this->xEvento = $doc->getElementsByTagName('xEvento')->item(0)->nodeValue;
                $this->nSeqEvento = $doc->getElementsByTagName('nSeqEvento')->item(0)->nodeValue;
                
                //-- ADICIONA A AUTORIZACAO AO XML
                $cAno_Mes_MDFe=substr($this->getDataEmissao(),0,7);
                if (!is_dir($this->getPastaArquivoXML().$evento."/".$cAno_Mes_MDFe)) 
                    mkdir($this->getPastaArquivoXML().$evento."/".$cAno_Mes_MDFe, 0777);
                
                $cArquivo_Autorizado=$this->getPastaArquivoXML().$evento."/".$cAno_Mes_MDFe."/".$this->nProt.".xml";
                $this->setArquivoAutorizado($cArquivo_Autorizado);
                
                $cProtocolo   = '<retEventoMDFe versao="'.$this->getVersaoMDFe().'">';
                // Informações do Protocolo de resposta. TAG a ser assinada
                $cProtocolo  .= '<infEvento Id="ID'.$this->nProt.'">';
                $cProtocolo  .= '<tpAmb>'.$this->tpAmb.'</tpAmb>';
                $cProtocolo  .= '<verAplic>'.$this->verAplic.'</verAplic>';
                $cProtocolo  .= '<cOrgao>'.$this->cOrgao.'</cOrgao>';
                $cProtocolo  .= '<cStat>'.$this->cStat.'</cStat>';
                $cProtocolo  .= '<xMotivo>'.$this->xMotivo.'</xMotivo>';
                $cProtocolo  .= '<chMDFe>'.$this->chMDFe.'</chMDFe>';
                $cProtocolo  .= '<tpEvento>'.$this->tpEvento.'</tpEvento>';
                $cProtocolo  .= '<xEvento>'.$this->xEvento.'</xEvento>';
                $cProtocolo  .= '<nSeqEvento>'.$this->nSeqEvento.'</nSeqEvento>';
                $cProtocolo  .= '<dhRegEvento>'.$this->dhRegEvento.'</dhRegEvento>';
                $cProtocolo  .= '<nProt>'.$this->nProt.'</nProt>';
                $cProtocolo  .= '</infEvento>';
                $cProtocolo  .= '</retEventoMDFe>';

                if (empty($this->nProt)){
                    return false;
                }

                $id_encerramento = "ID".$this->tpEvento.$this->chMDFe.'01';
                //-- Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração
                $cAno_Mes_MDFe=substr($this->getDataEmissao(),0,7);
                $cArquivoAssinado = $this->getPastaArquivoXML().'assinado/'.$id_encerramento.'.xml';
                if (!file_exists($cArquivoAssinado))
                {
                   echo "Arquivo não existe: $cArquivoAssinado\n\n";
                   exit;
                }
                $cConteudo = file_get_contents($cArquivoAssinado);

                $cConteudo = substr($cConteudo,38,strlen($cConteudo)-38);
                ?><?php
                $cAutorizado = '<?xml version="1.0" encoding="UTF-8"?>';
                ?><?php
                $cAutorizado .= '<procEventoMDFe versao="'.$this->getVersaoMDFe().'" xmlns="http://www.portalfiscal.inf.br/mdfe">';
                $cAutorizado .= $cConteudo;
                $cAutorizado .= $cProtocolo;
                $cAutorizado .= '</procEventoMDFe>';
                //die("\n\n\nCONTEUDO \n\n\n".$cConteudo."\n\n\n");

                file_put_contents($cArquivo_Autorizado, $cAutorizado);
                mysqli_query("update T276 set T276_Arquivo_Encerramento_XML='$cAutorizado' where T276_Id='".$this->getT276Id()."'");
            }else{  
                //-- Faz a Consulta pelo Recibo para tentar saber qual o motivo
                //$this->errorStatus = true;
                echo "**************************************************************************************\n";
                echo "* 7 - Consulta pelo protocolo não retornou nada, consultando pelo Recibo \n";
                echo "**************************************************************************************\n";
                $bRet=$this->consultaMDFeRecibo($idMDFe,$nRecibo);
            }
        } else {
            $this->errorStatus = true;
            $this->errorMsg = 'EXISTE UM PROBLEMA TECNICO COM OS SERVIÇOS DO SEFAZ, TENTE NOVAMENTE EM 3 MINUTOS *';
        }

        return $bRet;
    }

    /**********************************************
     * Método da parte geral da recepção de eventos (encerramento, cancelamento e inclusão de condutor)
     *
     * @param   string  $idNFe ID da NFe com 44 digitos (sem o NFe na frente dos numeros)
     * @param   string  $protId Numero do protocolo de aceitaÃ§ao da NFe enviado anteriormente pelo SEFAZ
     * @param   string  $evento Tipo do evento 
     * @return  boolean TRUE se sucesso ou FALSE se falha
     * @access  public
     ************************************************/
    public function recepcaoEvento($idMDFe,$protId,$evento){
        echo "\n";
        echo "**************************************************************************************\n";
        echo "**************************************************************************************\n";
        echo "**\n";
        echo "**                    RECEPÇÃO EVENTO \n";
        echo "** \n";
        echo "**                       ENCERRAMENTO OU CANCELAMENTO \n";
        echo "**\n";
        echo "**************************************************************************************\n";
        echo "**************************************************************************************\n";
        echo "\n";

        //variavel de retorno
        $bRet = false;
        // carga das variaveis da funÃ§ao do webservice
        $wsdl = 'RecepcaoEvento';       
        $dataVer        = $this->setVersaoLayoutXSD($wsdl);
        $cTipoDocumento = $this->getTipoDocAuxiliar();       
        $parteEspecifica = '';

        // Estabelece o código e a parte específica do xml do evento de ENCERRAMENTO e depois do CANCELAMENTO
        if($evento == 'Encerramento'){
            $dataEncerramento = $this->getDataEncerramento();
            $dhEvento   = date('Y-m-d').'T'.date('H:i:s').$this->getTimeZone($evento); 
            $cUF = mysql_fetch_array(mysqli_query("SELECT D018_Codigo_IBGE FROM D018 WHERE D018_UF='".$this->getUFDescarregamento()."' "));
            $cCidade = mysql_fetch_array(mysqli_query("SELECT D020_Codigo_IBGE FROM D020 LEFT JOIN D018 ON D018_Id=D020_D018_Id WHERE D020_Nome_Cidade='".$this->getCidadeDescarregamento()."' AND D018_UF='".$this->getUFDescarregamento()."' "));
            $tpEvento   = '110112';         
            $parteEspecifica .= "<evEncMDFe>";
            $parteEspecifica .= "<descEvento>$evento</descEvento>";
            $parteEspecifica .= "<nProt>$protId</nProt>";
            $parteEspecifica .= "<dtEnc>".date('Y-m-d')."</dtEnc>";
            $parteEspecifica .= "<cUF>$cUF[0]</cUF>";
            $parteEspecifica .= "<cMun>$cCidade[0]</cMun>";
            $parteEspecifica .= "</evEncMDFe>";
        }else if($evento == 'Cancelamento'){
            $dhEvento   = date('Y-m-d').'T'.date('H:i:s').$this->getTimeZone($evento); 
            $tpEvento   = '110111';         
            $parteEspecifica .= "<evCancMDFe>";
            $parteEspecifica .= "<descEvento>$evento</descEvento>";
            $parteEspecifica .= "<nProt>$protId</nProt>";
            $parteEspecifica .= "<xJust>".$this->getMotivoCancelamento()."</xJust>";
            $parteEspecifica .= "</evCancMDFe>";
        }
        //montagem do namespace do serviço
        $namespace = 'http://www.portalfiscal.inf.br/mdfe';
        // Para cancelamento e encerramento o numero sequencia do evento sempre será 1
        $nSeqEvento = '1'; 
        // Data e hora do evento no formato AAAA-MM-DDTHH:MM:SSTZD (UTC)
        $dhEvento   = date('Y-m-d').'T'.date('H:i:s').$this->getTimeZone(); 
        // Código da UF que registrou o Evento. Utilizar 90 para o SUFRAMA
        $cOrgao     = $this->getCodigoUFEmitente();
                
        // de acordo com o manual versão 3
        // 2 + 6 + 44 + 2 = 54 digitos
        // ID + tpEvento + chave da NF-e + nSeqEvento
        // garantir que existam 2 digitos em nSeqEvento para montar o ID com 54 digitos        
        if (strlen(trim($nSeqEvento))==1){
            $zenSeqEvento = str_pad($nSeqEvento, 2, "0", STR_PAD_LEFT);
        } else {
            $zenSeqEvento = trim($nSeqEvento);
        }
        $id_evento = "ID".$tpEvento.$idMDFe.$zenSeqEvento;

        $Ev  = '';
        $Ev .= "<eventoMDFe versao=\"$dataVer\" xmlns=".'"http://www.portalfiscal.inf.br/mdfe">';
        $Ev .= "<infEvento Id=\"$id_evento\">";
        $Ev .= "<cOrgao>$cOrgao</cOrgao>";
        $Ev .= "<tpAmb>".$this->getIdentAmbiente()."</tpAmb>";
        $Ev .= "<CNPJ>".$this->getCnpjEmitente()."</CNPJ>";
        $Ev .= "<chMDFe>$idMDFe</chMDFe>";
        $Ev .= "<dhEvento>$dhEvento</dhEvento>";
        $Ev .= "<tpEvento>$tpEvento</tpEvento>";
        $Ev .= "<nSeqEvento>$nSeqEvento</nSeqEvento>";
        $Ev .= "<detEvento versaoEvento=\"$dataVer\">";
        $Ev .= $parteEspecifica;
        $Ev .= "</detEvento></infEvento></eventoMDFe>";

        // Assina o XML de cancelamento e encerramento
        $Ev = $this->assinaPHPMDFe($Ev, 'infEvento','eventoMDFe');

        $Ev = str_replace('<?xml version="1.0"?>','', $Ev);
        $Ev = str_replace('<?xml version="1.0" encoding="utf-8"?>','', $Ev);
        $Ev = str_replace('<?xml version="1.0" encoding="UTF-8"?>','', $Ev);
        $Ev = str_replace(array("\r","\n","\s"),"", $Ev);
   
        $mdfeDadosMsg = '<?xml version="1.0" encoding="UTF-8"?>'.$Ev;          

        $cArquivoTemp=$this->getPastaArquivoXML()."assinado/".$id_evento.".xml";
        if (!file_put_contents($cArquivoTemp, $mdfeDadosMsg))
        {
            $this->mErrorMsg[] = "<br/>Impossível gravar o arquivo: $cArquivoTemp DESC = $Ev";
            $this->errorStatus = true;
            return false;           
        }

        $mdfeDadosMsg = str_replace('<?xml version="1.0" encoding="utf-8"?'.'>','',$mdfeDadosMsg);
        $mdfeDadosMsg = str_replace('<?xml version="1.0" encoding="UTF-8"?'.'>','',$mdfeDadosMsg);
        // Envia o arquivo....
        $xmlresp = $this->sendSOAP($mdfeDadosMsg, $wsdl);
        if (is_object($xmlresp)) {
            $xmlresp = utf8_encode($xmlresp->any);
            $isRetorno = true;
        } else if (!empty($xmlresp)) {
            $xmlresp = utf8_encode($xmlresp);
            $isRetorno = true;
        }

        if ($isRetorno){ 
            if ($xmlresp == ''){
                //Houve uma falha na comunicação SOAP
                $this->mErrorMsg[] = "Falha na comunicação SOAP.";
                return false;
            }
            $bRet = $this->processarRetornoEvento($xmlresp,$Ev,$id_evento,$evento);
            mysqli_query("update T276 set T276_Arquivo_Encerramento_Cancelamento_XML='$Ev' where T276_Id='".$this->getT276Id()."'");
        } else {
            $this->mErrorMsg[] = "Ocorreu um erro ao tentar cancelar o MDF-e";
            $this->errorStatus = true;
            echo $this->soapDebug;            
        }
        return $bRet;    
    } 

    /**
     * Assinador TOTALMENTE baseado em PHP das NFe
     * este assinador somente utiliza comandos nativos do PHP para assinar
     * FUNCIONAL !!!! resulta em arquvo identico ao assinadorRS
     *
     * @param   string $nfe
     * @param   string $tagid TAG que devera ser assinada
     * @return  string XML assinada
     * @access  public
    **/
    public function assinaPHPMDFe($MDFe, $tagid,$taginicio){
        //carrega as chaves do certifciado p12

        //echo "\n\n\n\n<br/><br/>".$this->pathCert."<br/><br/>\n\n\n\n";
        $key = file_get_contents($this->pathCert);
        /*if(!$key)
            echo "\n\n\n\n\n\n\n<br/>Não foi possível ler os dados\n\n\n\n\n\n\n";*/
        $resp = openssl_pkcs12_read ($key,$x509certdata,$this->passKey);
        /*if(!$resp){
            echo "\n\n\n\n\n\n\n<br/>Não foi possível armazenar os dados do certificado\n\n\n\n\n\n\n";
        }*/
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
        $MDFe = str_replace($order, $replace, $MDFe);
        // carrega o documento no DOM
        $xmldoc = new \DOMDocument();
        $xmldoc->preservWhiteSpace = FALSE; //elimina espaÃ§os em branco
        $xmldoc->formatOutput = FALSE;
        // muito importante deixar ativadas as opÃ§oes para limpar os espacos em branco
        // e as tags vazias
        $xmldoc->loadXML("$MDFe",LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        //extrair a tag com os dados a serem assinados
        $infMDFe = $xmldoc->getElementsByTagName($tagid)->item(0);
        $id = trim($infMDFe->getAttribute("Id"));
        $outname = $this->getPastaArquivoXML().'assinado/'.$id.'.xml';
        
        //extrai os dados da tag para uma string
        $dados = $infMDFe->C14N(FALSE,FALSE,NULL,NULL);
        //calcular o hash dos dados
        $hashValue = hash('sha1',$dados,TRUE);
        //converte o valor para base64 para serem colocados no xml
        $digValue = base64_encode($hashValue);
        //monta a tag da assinatura digital
        $templsign = '<Signature xmlns="'.$this->URLdsig.'"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /><SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1" /><Reference URI="#'.$id.'"><Transforms><Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" /><Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /></Transforms><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" /><DigestValue>'.$digValue.'</DigestValue></Reference></SignedInfo><SignatureValue></SignatureValue><KeyInfo><X509Data><X509Certificate>'.$certX509.'</X509Certificate></X509Data></KeyInfo></Signature>';
        //salva o xml normalizado
        $MDFe = $xmldoc->saveXML();
        //remove a tag de fechamento final da MDFe </MDFe>
        $MDFe = str_replace("</$taginicio>","",$MDFe);
        //acrescenta o template da assinatura
        $MDFe = $MDFe . $templsign."</$taginicio>";
        // limpa algumas variaveis da memoria
        unset($xmldoc);
        unset($dados);
        unset($hashValue);
        unset($digValue);
        unset($id);
        unset($infMDFe);
        unset($order);
        unset($replace);
        unset($templsign);
        unset($data);

        //carrega novamente o DOM com a MDFe
        $xmldoc = new \DOMDocument();
        $xmldoc->preservWhiteSpace = FALSE; //elimina espaÃ§os em branco
        $xmldoc->formatOutput = FALSE;

        $xmldoc->loadXML($MDFe,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
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
        $MDFe = $xmldoc->saveXML();

        //se for passado parametro de destino salvar o xml como arquvo
        if (!is_dir($this->getPastaArquivoXML().'assinado/')) mkdir($this->getPastaArquivoXML().'assinado/', 0777);
        $ret = $xmldoc->save($outname);
        if(!$ret){
            echo "\n\n Não Foi possível gravar o arquivo.\n\n";
        }
        return $MDFe;
    }

    public function processarRetornoEvento($xmlresp,$Ev,$id_evento, $nomeEvento)
    {
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* PROCESSAR RETORNO EVENTO\n";
        echo "**************************************************************************************\n";
        echo "\n";

        $xmlretEvent = new \DOMDocument('1.0', 'utf-8'); //cria objeto DOM
        $xmlretEvent->formatOutput = false;
        $xmlretEvent->preserveWhiteSpace = false;
        $xmlretEvent->loadXML($xmlresp,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);        
        
        $retEvento = $xmlretEvent->getElementsByTagName("retEventoMDFe")->item(0);
        $cStat = !empty($retEvento->getElementsByTagName('cStat')->item(0)->nodeValue) ? $retEvento->getElementsByTagName('cStat')->item(0)->nodeValue : '';
        $this->cStat = $cStat;
        $this->xMotivo = !empty($retEvento->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $retEvento->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';      
        //$this->xMotivo=utf8_decode(utf8_decode($xMotivo));
        
        if ($cStat == ''){
            // houve erro
            $this->errorStatus = true;
            $this->mErrorMsg[] = "cStat está em branco, houve erro na comunicação Soap verifique a mensagem de erro e o debug!!";
            return false;
        }
        
        //erro no processamento cStat <> 135
        echo "<br/><br/>cStatus = ".$cStat."<br/><br/>";
        if ($cStat!= 135 ){
            //se cStat <> 135 houve erro e o lote foi rejeitado
            $this->errorStatus = true;
            $this->mErrorMsg[] = "Retorno de ERRO: $this->cStat - $this->xMotivo";
            echo "\n";
            echo "**************************************************************************************\n";
            echo "* Retorno de Erro: $cStat - $xMotivo\n";
            echo "**************************************************************************************\n";
            echo "\n";
            return false;
        }
        
        // Retorna a data e o numero do protocolo do evento para guardar no banco de dados no programa 
        $this->dhRecbto = !empty($retEvento->getElementsByTagName('dhRegEvento')->item(0)->nodeValue) ? $retEvento->getElementsByTagName('dhRegEvento')->item(0)->nodeValue : '';
        $this->nProt = !empty($retEvento->getElementsByTagName('nProt')->item(0)->nodeValue) ? $retEvento->getElementsByTagName('nProt')->item(0)->nodeValue : '';
        
        //o evento foi aceito cStat == 135
        //carregar o evento
        if($nomeEvento == 'Cancelamento'){
            $xmlenvEvento = new \DOMDocument('1.0', 'utf-8'); //cria objeto DOM
            $xmlenvEvento->formatOutput = false;
            $xmlenvEvento->preserveWhiteSpace = false;
            $xmlenvEvento->loadXML($Ev,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            $evento = $xmlenvEvento->getElementsByTagName("eventoMDFe")->item(0);
            
            //Processo completo solicitação + protocolo
            $xmlprocEvento = new \DOMDocument('1.0', 'utf-8'); //cria objeto DOM
            $xmlprocEvento->formatOutput = false;
            $xmlprocEvento->preserveWhiteSpace = false;
            
            //cria a tag retEnvEvento
            $retEnvEvento = $xmlprocEvento->createElement('procEventoMDFe');
            $xmlprocEvento->appendChild($retEnvEvento);
            
            //estabele o atributo de versão
            $eventProc_att1 = $retEnvEvento->appendChild($xmlprocEvento->createAttribute('xmlns'));
            $eventProc_att1->appendChild($xmlprocEvento->createTextNode("http://www.portalfiscal.inf.br/mdfe"));
            
            //estabelece o atributo xmlns
            $eventProc_att2 = $retEnvEvento->appendChild($xmlprocEvento->createAttribute('versao'));
            $eventProc_att2->appendChild($xmlprocEvento->createTextNode($versao));
            
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
            $cAno_Mes_MDFe=substr($this->getDataEmissao(),0,7);
            
            // Verifica se a pasta do mes e ano ja existe,
            // Caso nao exista, ele cria e
            if (!is_dir($this->getPastaArquivoXML().'cancelado')) mkdir($this->getPastaArquivoXML().'cancelado', 0777);
            if (!is_dir($this->getPastaArquivoXML().'cancelado'.'/'.$cAno_Mes_MDFe)) mkdir($this->getPastaArquivoXML().'cancelado'.'/'.$cAno_Mes_MDFe, 0777);

            // Monta o caminho do arquivo autorizado
            $arqName=$this->getPastaArquivoXML().'cancelado'."/".$cAno_Mes_MDFe."/".$id_evento.".xml";
            $this->arquivo_autorizado_evento=$arqName;
            
            // salva o arquivo xml autorizado
            if (!file_put_contents($arqName, $procXML)){
                $this->errorStatus = true;
                $this->mErrorMsg[] = "Falha na gravação do arquivo $arqName";
                return false;           
            }
            
            $this->conteudo_arquivo_evento=$procXML;
        }
        
        return true;
    }
}

/**
 * Classe complementar
 * necessária para a comunicação SOAP 1.2
 * Remove algumas tags para adequar a comunicação
 * ao padrão Ruindows utilizado
 *
 * @version 1.2
 * @package MDFePHP
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 *
 */
class MDFeSOAP2Client extends \SoapClient {
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $request = str_replace(':ns1', '', $request);
        $request = str_replace('ns1:', '', $request);
        $request = str_replace("\n", '', $request);
        $request = str_replace("\r", '', $request);
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
?>