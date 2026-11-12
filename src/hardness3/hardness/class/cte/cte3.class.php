<?php

namespace hardness;

use Exception;
use InvalidArgumentException;

require_once('Cte.php');
require_once('logCTE.php');

class classeCTe extends Cte
{    
    private Builder $montador;    
    protected string $pathAssinado;
    protected string $pathAutorizado;

    public function __construct(Certificate $certificado, Builder $montador, array $configuracoes)
    {        
        //Adicionar verificação caso não seja informada as configurações

        $this->montador = $montador;               
        $this->setPastaArquivoXML($configuracoes['path']);

        parent::__construct($certificado, $configuracoes);
    }
    
    public function setPastaArquivoXML($pathRaiz)
    {           
        //-- Cria pasta caso a mesma não exista
        if (!is_dir($pathRaiz)) mkdir($pathRaiz, 0777,true);
        if (substr($pathRaiz,-1)!='/') $pathRaiz.='/';
        
        $this->pathAssinado = $pathRaiz . 'assinado/';
        $this->pathAutorizado = $pathRaiz . 'autorizado/';

        if (!is_dir($this->pathAssinado)) mkdir($this->pathAssinado, 0777,true);
        if (!is_dir($this->pathAutorizado)) mkdir($this->pathAutorizado, 0777,true);


        logCTE::logArquivo("Path Assinado: {$this->pathAssinado}");
        logCTE::logArquivo("Path Autorizado: {$this->pathAutorizado}");
    }

    public function preparaCte($T084_Id, $chave)
    {
        logCTE::logArquivo("\nIniciando preparo do CT-e\n");
        if (empty($T084_Id)) {
            return 'T084_Id não informado';
        }
        #region query
        $cClausula  = " SELECT  ";
        $cClausula .= " T084_Id,";
        $cClausula .= " T084_C004_Id,";
        $cClausula .= " T084_D006_Id,";
        $cClausula .= " T084_Numero_Conhecimento,";
        $cClausula .= " T084_Valor_Conhecimento,";
        $cClausula .= " T084_Data_Emissao,";
        $cClausula .= " T084_Fuso_Horario_Emissao,";        
        $cClausula .= " T084_Tipo_Conhecimento,";
        $cClausula .= " T084_Data_Vencimento,";
        $cClausula .= " T084_Aliquota_ICMS,";
        $cClausula .= " T084_Valor_Base_ICMS,";
        $cClausula .= " T084_Valor_ICMS,";
        $cClausula .= " T084_Data_Lancamento,";
        $cClausula .= " T084_Valor_ICMS_ST,";
        $cClausula .= " T084_Chave_Acesso_Cte,";
        $cClausula .= " T084_Numero_Protocolo_Cte,";
        $cClausula .= " T084_Modelo,";
        $cClausula .= " T084_Serie,";
        $cClausula .= " T084_Numero_Protocolo_Cancelamento_Cte,";
        $cClausula .= " T084_Flag_Modalidade_Transporte,";
        $cClausula .= " T084_Flag_Tipo_Servico,";
        $cClausula .= " T084_Informacoes_Adicionais_Retira,";
        $cClausula .= " T084_Natureza_Operacao,";
        $cClausula .= " T084_Flag_Indicador_IE,";
        $cClausula .= " T084_Observacoes_Gerais,";
        $cClausula .= " T084_D018_Id_Origem,";
        $cClausula .= " T084_D020_Id_Origem,";
        $cClausula .= " T084_D018_Id_Destino,";
        $cClausula .= " T084_D020_Id_Destino,";
        $cClausula .= " T084_Valor_Total_Carga,";
        $cClausula .= " T084_Produto_Predominante,";
        $cClausula .= " T084_Produto_Predominante_Outras_Caracteristicas,";
        $cClausula .= " T084_Flag_Retira,";
        $cClausula .= " T084_Numero_Recibo_Cte,";
        $cClausula .= " T084_Codigo_CFOP,";
        $cClausula .= " T084_Descricao_CFOP,";
        $cClausula .= " T084_Flag_Travar,";
        $cClausula .= " T084_Hora_Emissao,";
        $cClausula .= " T084_Observacao_Cancelamento,";
        $cClausula .= " T084_Versao_Cte,";
        $cClausula .= " T084_Tomador_CNPJ,";
        $cClausula .= " T084_Tomador_CPF,";
        $cClausula .= " T084_Tomador_IE,";
        $cClausula .= " T084_Tomador_Nome,";
        $cClausula .= " T084_Tomador_Nome_Fantasia,";
        $cClausula .= " T084_Tomador_Rua,";
        $cClausula .= " T084_Tomador_Numero,";
        $cClausula .= " T084_Tomador_Bairro,";
        $cClausula .= " T084_Tomador_Codigo_Cidade_IBGE,";
        $cClausula .= " T084_Tomador_Cidade,";
        $cClausula .= " T084_Tomador_UF,";
        $cClausula .= " T084_Emitente_CNPJ,";
        $cClausula .= " T084_Emitente_IE,";
        $cClausula .= " T084_Emitente_Nome,";
        $cClausula .= " T084_Emitente_Rua,";
        $cClausula .= " T084_Emitente_Numero,";
        $cClausula .= " T084_Emitente_Bairro,";
        $cClausula .= " T084_Emitente_Codigo_Cidade_IBGE,";
        $cClausula .= " T084_Emitente_Cidade,";
        $cClausula .= " T084_Emitente_CEP,";
        $cClausula .= " T084_Emitente_UF,";
        $cClausula .= " T084_Emitente_UF_IBGE,";
        $cClausula .= " T084_Emitente_Telefone,";
        $cClausula .= " T084_Remetente_CNPJ,";
        $cClausula .= " T084_Remetente_CPF,";
        $cClausula .= " T084_Remetente_IE,";
        $cClausula .= " T084_Remetente_Nome,";
        $cClausula .= " T084_Remetente_Telefone,";
        $cClausula .= " T084_Remetente_Rua,";
        $cClausula .= " T084_Remetente_Numero,";
        $cClausula .= " T084_Remetente_Bairro,";
        $cClausula .= " T084_Remetente_Codigo_Cidade_IBGE,";
        $cClausula .= " T084_Remetente_Cidade,";
        $cClausula .= " T084_Remetente_CEP,";
        $cClausula .= " T084_Remetente_UF,";
        $cClausula .= " T084_Remetente_Codigo_Pais,";
        $cClausula .= " T084_Remetente_Nome_Pais,";
        $cClausula .= " T084_Recebedor_CNPJ,";
        $cClausula .= " T084_Recebedor_CPF,";
        $cClausula .= " T084_Recebedor_IE,";
        $cClausula .= " T084_Recebedor_Nome,";
        $cClausula .= " T084_Recebedor_Rua,";
        $cClausula .= " T084_Recebedor_Numero,";
        $cClausula .= " T084_Recebedor_Bairro,";
        $cClausula .= " T084_Recebedor_Codigo_Cidade_IBGE,";
        $cClausula .= " T084_Recebedor_Cidade,";
        $cClausula .= " T084_Recebedor_CEP,";
        $cClausula .= " T084_Recebedor_UF,";
        $cClausula .= " T084_Destinatario_CNPJ,";
        $cClausula .= " T084_Destinatario_CPF,";
        $cClausula .= " T084_Destinatario_IE,";
        $cClausula .= " T084_Destinatario_Nome,";
        $cClausula .= " T084_Destinatario_Telefone,";
        $cClausula .= " T084_Destinatario_Rua,";
        $cClausula .= " T084_Destinatario_Numero,";
        $cClausula .= " T084_Destinatario_Bairro,";
        $cClausula .= " T084_Destinatario_Codigo_Cidade_IBGE,";
        $cClausula .= " T084_Destinatario_Cidade,";
        $cClausula .= " T084_Destinatario_CEP,";
        $cClausula .= " T084_Destinatario_UF,";
        $cClausula .= " T084_Destinatario_Codigo_Pais,";
        $cClausula .= " T084_Destinatario_Nome_Pais,";
        $cClausula .= " T084_Destinatario_Email,";
        $cClausula .= " T084_Flag_Situacao_Tributaria,";
        $cClausula .= " T084_Percentual_Reducao_ICMS,";
        $cClausula .= " T084_Valor_Base_ICMS_Substituicao_Retido,";
        $cClausula .= " T084_Valor_ICMS_Substituicao_Retido,";
        $cClausula .= " T084_Valor_Averbacao,";
        $cClausula .= " T084_Flag_Entrada_Saida,";
        $cClausula .= " T084_Valor_Receber,";
        $cClausula .= " T084_Ambiente_CTe,";
        $cClausula .= " T084_Data_Hora_Protocolo_CTe,";
        $cClausula .= " T084_D024_Tomador_Id,";
        $cClausula .= " T084_D024_Remetente_Id,";
        $cClausula .= " T084_D024_Destinatario_Id,";        
        $cClausula .= " OrigemD020.D020_Codigo_IBGE as OrigemCodigoIBGE,";
        $cClausula .= " OrigemD020.D020_Nome_Cidade as OrigemCidade,";
        $cClausula .= " OrigemD018.D018_UF as OrigemUF,";
        $cClausula .= " DestinoD020.D020_Codigo_IBGE as DestinoCodigoIBGE,";
        $cClausula .= " DestinoD020.D020_Nome_Cidade as DestinoCidade,";
        $cClausula .= " DestinoD018.D018_UF as DestinoUF,";                    
        $cClausula .= " C004_RNTRC,";
        $cClausula .= " C004_Codigo_Regime_Tributario,";
        /* [REFORMA TRIBUTÁRIA CTe] Gabriel Cegantini 09/12/2025
        =============================================================== */
        $cClausula .= " T084A_Situacao_Tributaria_CBS_IBS,";
        $cClausula .= " T084A_Classificacao_Tributaria_CBS_IBS,";
        $cClausula .= " T084A_Valor_Base_CBS_IBS,";
        $cClausula .= " T084A_Aliquota_IBS_UF,";
        $cClausula .= " T084A_Percentual_Diferimento_IBS_UF,";
        $cClausula .= " T084A_Valor_Diferimento_IBS_UF,";
        $cClausula .= " T084A_Percentual_Reducao_IBS_UF,";
        $cClausula .= " T084A_Aliquota_IBS_UF_Efetiva,";
        $cClausula .= " T084A_Valor_IBS_UF,";
        $cClausula .= " T084A_Aliquota_IBS_Municipal,";
        $cClausula .= " T084A_Percentual_Diferimento_IBS_Municipal,";
        $cClausula .= " T084A_Valor_Diferimento_IBS_Municipal,";
        $cClausula .= " T084A_Percentual_Reducao_IBS_Municipal,";
        $cClausula .= " T084A_Aliquota_IBS_Municipal_Efetiva,";
        $cClausula .= " T084A_Valor_IBS_Municipal,";
        $cClausula .= " T084A_Valor_IBS,";
        $cClausula .= " T084A_Aliquota_CBS,";
        $cClausula .= " T084A_Percentual_Diferimento_CBS,";
        $cClausula .= " T084A_Valor_Diferimento_CBS,";
        $cClausula .= " T084A_Percentual_Reducao_CBS,";
        $cClausula .= " T084A_Aliquota_CBS_Efetiva,";
        $cClausula .= " T084A_Valor_CBS,";
        $cClausula .= " T084A_Valor_Total_Reforma";
        /* ============================================================= */
        $cClausula .= " FROM T084 ";
        $cClausula .= " LEFT JOIN T084A ON T084A_T084_Id = T084_Id ";
        $cClausula .= " LEFT JOIN D018 as OrigemD018 ON OrigemD018.D018_Id=T084_D018_Id_Origem ";
        $cClausula .= " LEFT JOIN D020 as OrigemD020 ON OrigemD020.D020_Id=T084_D020_Id_Origem ";
        $cClausula .= " LEFT JOIN D018 as DestinoD018 ON DestinoD018.D018_Id=T084_D018_Id_Destino ";
        $cClausula .= " LEFT JOIN D020 as DestinoD020 ON DestinoD020.D020_Id=T084_D020_Id_Destino ";
        $cClausula .= " LEFT JOIN C004 ON  C004_Id = T084_C004_Id";
        $cClausula .= " WHERE T084_Id='{$T084_Id}'";
        #endregion
        $T084 = mysql_query($cClausula);
        $mT084 = mysql_fetch_array($T084);
        
        $dhEmi = $mT084['T084_Data_Emissao'] . 'T' . $mT084['T084_Hora_Emissao'] . $mT084['T084_Fuso_Horario_Emissao'];        

        $cCT = '';        
        
        //Montagem da chave
        if(empty($chave)) {
            $cCT = $this->montador->getNumeroAleatorioChaveAcesso();
        
            $chave = $this->montador->montaChave(
                $this->codUF,
                date('y', strtotime($dhEmi)) . date('m', strtotime($dhEmi)),
                $mT084['T084_Emitente_CNPJ'],
                $mT084['T084_Modelo'],
                $mT084['T084_Serie'],
                $mT084['T084_Numero_Conhecimento'],
                1,
            $cCT);    
        } else {
            $cCT = substr($chave,-9,-1);        
        }
        
        logCTE::logArquivo("Chave Cte: $chave");
        //Montagem tag infCTe
        $infCte = new \stdClass;
        $infCte->Id = $chave;
        $infCte->versao = $this->versao;
        $this->montador->taginfCte($infCte);

        #region ide
        $ide = new \stdClass;        
        $ide->cUF = $mT084['T084_Emitente_UF_IBGE'];        
        $ide->cCT = $cCT;
        $ide->CFOP = $mT084['T084_Codigo_CFOP'];
        $ide->natOp = $mT084['T084_Descricao_CFOP'];
        $ide->mod = $mT084['T084_Modelo'];
        $ide->serie = $mT084['T084_Serie'];
        $ide->nCT = $mT084['T084_Numero_Conhecimento'];
        $ide->dhEmi = $dhEmi;
        $ide->tpImp = '1';
        $ide->tpEmis = '1';
        $ide->cDV = substr($chave, -1);;
        $ide->tpAmb = $mT084['T084_Ambiente_CTe'];
        $ide->tpCTe = $mT084['T084_Tipo_Conhecimento'];
        $ide->procEmi = '0';
        $ide->verProc = '1.0';//Versao do aplicativo do emissor
        $ide->cMunEnv = $mT084['T084_Emitente_Codigo_Cidade_IBGE'];
        $ide->xMunEnv = $mT084['T084_Emitente_Cidade'];
        $ide->UFEnv = $mT084['T084_Emitente_UF'];
        $ide->modal = $mT084['T084_Flag_Modalidade_Transporte'];
        $ide->tpServ = $mT084['T084_Flag_Tipo_Servico'];
        $ide->cMunIni = $mT084['OrigemCodigoIBGE'];
        $ide->xMunIni = $mT084['OrigemCidade'];
        $ide->UFIni =   $mT084['OrigemUF'];
        $ide->cMunFim = $mT084['DestinoCodigoIBGE'];
        $ide->xMunFim = $mT084['DestinoCidade'];
        $ide->UFFim = $mT084['DestinoUF'];
        $ide->retira = $mT084['T084_Flag_Retira'] != 'S' ? '0' : '1';
        $ide->xDetRetira = $mT084['T084_Informacoes_Adicionais_Retira'];
        $ide->indIEToma = $mT084['T084_Flag_Indicador_IE'];
        $this->montador->tagide($ide);
        #endregion
        $toma = '';

        switch ($mT084['T084_D024_Tomador_Id']) {
            case $mT084['T084_D024_Remetente_Id']:
                $toma = '0'; //Remetente
                if($mT084['T084_Flag_Indicador_IE'] == '2') //Contribuinte isento de inscrição
                    $mT084['T084_Remetente_IE'] = 'ISENTO';
                break;
            case $mT084['T084_D024_Destinatario_Id']:
                $toma = '3'; //Destinatário
                if($mT084['T084_Flag_Indicador_IE'] == '2') //Contribuinte isento de inscrição
                    $mT084['T084_Destinatario_IE'] = 'ISENTO';
                break;
            default:
                $toma = '4'; //Outros
                if($mT084['T084_Flag_Indicador_IE'] == '2') //Contribuinte isento de inscrição
                    $mT084['T084_Tomador_IE'] = 'ISENTO';
                break;
        }

                        
        if ($toma != '4') {
            $toma3 = new \stdClass;
            $toma3->toma =  $toma;
            $this->montador->tagtoma3($toma3);
        } else {
            #region tomador
            $toma4 = new \stdClass;
            $toma4->toma = $toma;
            $toma4->CNPJ = $mT084['T084_Tomador_CNPJ'];
            $toma4->CPF = $mT084['T084_Tomador_CPF'];
            $toma4->IE = $mT084['T084_Tomador_IE'];
            $toma4->xNome = $mT084['T084_Tomador_Nome'];
            $toma4->xFant = $mT084['T084_Tomador_Nome_Fantasia'];
            $toma4->enderToma = new \stdClass;            
            $toma4->enderToma->xLgr = $mT084['T084_Tomador_Rua'];
            $toma4->enderToma->nro = $mT084['T084_Tomador_Numero'];
            $toma4->enderToma->xBairro = $mT084['T084_Tomador_Bairro'];
            $toma4->enderToma->cMun = $mT084['T084_Tomador_Codigo_Cidade_IBGE'];
            $toma4->enderToma->xMun = $mT084['T084_Tomador_Cidade'];
            $toma4->enderToma->UF = $mT084['T084_Tomador_UF'];            
            $this->montador->tagtoma4($toma4);
            #endregion
        }

        #region Complemento 
        $compl = new \stdClass;
        $compl->xObs = $mT084['T084_Observacoes_Gerais'];

        $T306 = mysql_query("SELECT T306_Identificador, T306_Conteudo FROM T306 WHERE T306_T084_Id='{$T084_Id}'");
        while($mT306 = mysql_fetch_array($T306))  {
            $ObsCont = new \stdClass;
            $ObsCont->xCampo = $mT306['T306_Identificador'];
            $ObsCont->xTexto = $mT306['T306_Conteudo'];
            $this->montador->tagObsCont($ObsCont);
        }        
        $this->montador->tagcompl($compl);

        #region Emitente
        $emit = new \stdClass;
        $emit->CNPJ = $mT084['T084_Emitente_CNPJ'];
        $emit->IE = $mT084['T084_Emitente_IE'];        
        $emit->xNome = $mT084['T084_Emitente_Nome'];
        if($this->ambiente == '2'){
            if($this->versao == '4.00'){
                $emit->xNome = 'CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';  
            } elseif($this->versao == '3.00'){
                $emit->xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';  
            }
        }    
        $emit->xFant = $mT084['T084_Emitente_Nome'];  
        $enderEmit = new \stdClass;
        $enderEmit->xLgr = $mT084['T084_Emitente_Rua'];
        $enderEmit->nro = $mT084['T084_Emitente_Numero'];
        $enderEmit->xBairro = $mT084['T084_Emitente_Bairro'];
        $enderEmit->cMun = $mT084['T084_Emitente_Codigo_Cidade_IBGE'];
        $enderEmit->xMun = $mT084['T084_Emitente_Cidade'];
        $enderEmit->CEP = $mT084['T084_Emitente_CEP'];
        $enderEmit->UF = $mT084['T084_Emitente_UF'];
        $emit->enderEmit = $enderEmit;
        // Mudaça XML 4.00
        if($this->versao == '4.00'){
            $emit->CRT = $mT084['C004_Codigo_Regime_Tributario'];     
        }
        $this->montador->tagemit($emit);
        #endregion

        #region Remetente
        $rem = new \stdClass;
        $rem->CNPJ = $mT084['T084_Remetente_CNPJ'];
        $rem->CPF = $mT084['T084_Remetente_CPF'];
        $rem->IE = $mT084['T084_Remetente_IE'];
        $rem->xNome = $mT084['T084_Remetente_Nome'];   
        if($this->ambiente == '2'){
            if($this->versao == '4.00'){
                $rem->xNome = 'CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';  
            } elseif($this->versao == '3.00'){
                $rem->xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';  

            }
        }     
        $rem->xFant = $mT084['T084_Remetente_Nome'];  

        $enderReme = new \stdClass;
        $enderReme->xLgr = $mT084['T084_Remetente_Rua'];
        $enderReme->nro = $mT084['T084_Remetente_Numero'];
        $enderReme->xBairro = $mT084['T084_Remetente_Bairro'];
        $enderReme->cMun = $mT084['T084_Remetente_Codigo_Cidade_IBGE'];
        $enderReme->xMun = $mT084['T084_Remetente_Cidade'];
        $enderReme->CEP = $mT084['T084_Remetente_CEP'];
        $enderReme->UF = $mT084['T084_Remetente_UF'];
        $rem->enderReme = $enderReme;
        $this->montador->tagrem($rem);
        #endregion

        #region Expedidor
        #endregion

        if (!empty($mT084['T084_Recebedor_Nome'])) {
            #region Recebedor
            $receb = new \stdClass;
            $receb->CNPJ = $mT084['T084_Recebedor_CNPJ'];
            $receb->CPF = $mT084['T084_Recebedor_CPF'];
            $receb->IE = $mT084['T084_Recebedor_IE'];
            $receb->xNome = $mT084['T084_Recebedor_Nome'];
            if($this->ambiente == '2'){
                if($this->versao == '4.00'){
                    $receb->xNome = 'CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';  
                } elseif($this->versao == '3.00'){
                    $receb->xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';  
                }
            }       
            $receb->enderReceb = new \stdClass;
            $receb->enderReceb->xLgr = $mT084['T084_Recebedor_Rua'];
            $receb->enderReceb->nro = $mT084['T084_Recebedor_Numero'];
            $receb->enderReceb->xBairro = $mT084['T084_Recebedor_Bairro'];
            $receb->enderReceb->cMun = $mT084['T084_Recebedor_Codigo_Cidade_IBGE'];
            $receb->enderReceb->xMun = $mT084['T084_Recebedor_Cidade'];
            $receb->enderReceb->CEP = $mT084['T084_Recebedor_CEP'];
            $receb->enderReceb->UF = $mT084['T084_Recebedor_UF'];
            $this->montador->tagreceb($receb);
            #endregion
        }

        #region Destinatário
        $dest = new \stdClass;
        $dest->CNPJ = $mT084['T084_Destinatario_CNPJ'];
        $dest->CPF = $mT084['T084_Destinatario_CPF'];
        $dest->IE = $mT084['T084_Destinatario_IE'];
        $dest->xNome = $mT084['T084_Destinatario_Nome'];
        if($this->ambiente == '2'){
            if($this->versao == '4.00'){
                $dest->xNome = 'CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';  
            } elseif($this->versao == '3.00'){
                $dest->xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';  
            }
        }       
        $dest->enderDest = new \stdClass;
        $dest->enderDest->xLgr = $mT084['T084_Destinatario_Rua'];
        $dest->enderDest->nro = $mT084['T084_Destinatario_Numero'];
        $dest->enderDest->xBairro = $mT084['T084_Destinatario_Bairro'];
        $dest->enderDest->cMun = $mT084['T084_Destinatario_Codigo_Cidade_IBGE'];
        $dest->enderDest->xMun = $mT084['T084_Destinatario_Cidade'];
        $dest->enderDest->CEP = $mT084['T084_Destinatario_CEP'];
        $dest->enderDest->UF = $mT084['T084_Destinatario_UF'];
        $this->montador->tagdest($dest);
        #endregion
        
        #region Valores
        $vPrest = new \stdClass;
        $vPrest->vTPrest = $mT084['T084_Valor_Conhecimento'];
        $vPrest->vRec =  $mT084['T084_Valor_Receber'];


        //Componentes da prestação

        $T304 = mysql_query("SELECT T304_Nome_Componente, T304_Valor_Componente 
                                FROM T304 
                                WHERE T304_T084_Id = '{$T084_Id}'");
        while($mT304 = mysql_fetch_array($T304))  {
            $Comp = new \stdClass;
            $Comp->xNome = $mT304['T304_Nome_Componente'];
            $Comp->vComp = $mT304['T304_Valor_Componente'];
            $this->montador->tagComp($Comp);
        }
        
        $this->montador->tagVPrest($vPrest);
        
        #region Impostos
        // Impostos do ICMS
        $impICMS = new \stdClass;
        $impICMS->CST    = $mT084['T084_Flag_Situacao_Tributaria'];
        $impICMS->vBC    = $mT084['T084_Valor_Base_ICMS'];
        $impICMS->pRedBC = $mT084['T084_Percentual_Reducao_ICMS'];
        $impICMS->pICMS  = $mT084['T084_Aliquota_ICMS'];
        $impICMS->vICMS  = $mT084['T084_Valor_ICMS'];
        $impICMS->CRT    = $mT084['C004_Codigo_Regime_Tributario'];         
        $this->montador->tagimpICMS($impICMS);
        

        /* [REFORMA TRIBUTÁRIA CTe] Gabriel Cegantini 09/12/2025
        ======================================================================= */
        // Impostos do IBSCBS
        if (!empty($mT084['T084A_Situacao_Tributaria_CBS_IBS']) and !empty($mT084['T084A_Classificacao_Tributaria_CBS_IBS'])) {
            $impIBSCBS = new \stdClass;
            $impIBSCBS->CST                 = $mT084['T084A_Situacao_Tributaria_CBS_IBS'];
            $impIBSCBS->cClassTrib          = $mT084['T084A_Classificacao_Tributaria_CBS_IBS'];
            $impIBSCBS->vBC                 = $mT084['T084A_Valor_Base_CBS_IBS'];
            $impIBSCBS->pIBSUF              = $mT084['T084A_Aliquota_IBS_UF'];
            $impIBSCBS->pDif_IBSUF          = $mT084['T084A_Percentual_Diferimento_IBS_UF'];
            $impIBSCBS->vDif_IBSUF          = $mT084['T084A_Valor_Diferimento_IBS_UF'];
            $impIBSCBS->pRedAliq_IBSUF      = $mT084['T084A_Percentual_Reducao_IBS_UF'];
            $impIBSCBS->pAliqEfet_IBSUF     = $mT084['T084A_Aliquota_IBS_UF_Efetiva'];
            $impIBSCBS->vIBSUF              = $mT084['T084A_Valor_IBS_UF'];
            $impIBSCBS->pIBSMun             = $mT084['T084A_Aliquota_IBS_Municipal'];
            $impIBSCBS->pDif_IBSMun         = $mT084['T084A_Percentual_Diferimento_IBS_Municipal'];
            $impIBSCBS->vDif_IBSMun         = $mT084['T084A_Valor_Diferimento_IBS_Municipal'];
            $impIBSCBS->pRedAliq_IBSMun     = $mT084['T084A_Percentual_Reducao_IBS_Municipal'];
            $impIBSCBS->pAliqEfet_IBSMun    = $mT084['T084A_Aliquota_IBS_Municipal_Efetiva'];
            $impIBSCBS->vIBSMun             = $mT084['T084A_Valor_IBS_Municipal'];
            $impIBSCBS->vIBS                = $mT084['T084A_Valor_IBS'];
            $impIBSCBS->pCBS                = $mT084['T084A_Aliquota_CBS'];
            $impIBSCBS->pDif_CBS            = $mT084['T084A_Percentual_Diferimento_CBS'];
            $impIBSCBS->vDif_CBS            = $mT084['T084A_Valor_Diferimento_CBS'];
            $impIBSCBS->pRedAliq_CBS        = $mT084['T084A_Percentual_Reducao_CBS'];
            $impIBSCBS->pAliqEfet_CBS       = $mT084['T084A_Aliquota_CBS_Efetiva'];
            $impIBSCBS->vCBS                = $mT084['T084A_Valor_CBS'];
            $impIBSCBS->vTotDFe             = $mT084['T084A_Valor_Total_Reforma'];
            $this->montador->tagimpIBSCBS($impIBSCBS);
        }
        #endregion
        /* ==================================================================== */

        #infCTeNorm
        if($ide->tpCTe == 0) { //CT-e Normal 
            ##Inf Carga
            $infCarga = new \stdClass;
            $infCarga->vCarga = $mT084['T084_Valor_Total_Carga'];
            $infCarga->proPred = $mT084['T084_Produto_Predominante'];
            $infCarga->xOutCat = $mT084['T084_Produto_Predominante_Outras_Caracteristicas'];

            $T305 =  mysql_query("SELECT T305_Codigo_Unidade, T305_Tipo_Medida, T305_Quantidade FROM T305 WHERE T305_T084_Id = '{$T084_Id}'");
            while($mT305 = mysql_fetch_array($T305)) {
                $infQ = new \stdClass;
                $infQ->cUnid = $mT305['T305_Codigo_Unidade'];
                $infQ->tpMed = $mT305['T305_Tipo_Medida'];
                $infQ->qCarga = $mT305['T305_Quantidade'];
                
                $this->montador->taginfQ($infQ);
            }
            
            $this->montador->taginfCarga($infCarga);

            
            ## infDoc
            $T085 =  mysql_query("SELECT 
                                        T085_Flag_Tipo_Documento, 
                                        T085_Chave_Acesso_Nfe, 
                                        T085_Data_Prevista_Entrega,
                                        T085_Tipo_Documento_Originario,
                                        T085_Descricao_Documento,
                                        T085_Data_Emissao,
                                        T085_Numero_Nota_Fiscal,
                                        T085_Valor_Total_Nota,
                                        T085_Data_Prevista_Entrega
                                    FROM T085 
                                    WHERE T085_T084_Id = '{$T084_Id}'");

            while($mT085 = mysql_fetch_assoc($T085)) {
                //infDoc - Tipo Documento NF-e
                if($mT085['T085_Flag_Tipo_Documento'] == 2) {
                    $infDoc = new \stdClass;
                    $infDoc->chave = $mT085['T085_Chave_Acesso_Nfe'];
                    if($mT085['T085_Data_Prevista_Entrega'] != '0000-00-00') {
                        $infDoc->dPrev = $mT085['T085_Data_Prevista_Entrega'];
                    }
                    $this->montador->taginfNfe($infDoc);
                }

                //infOutros - Tipo Documento Outros
                if($mT085['T085_Flag_Tipo_Documento'] == 3) {
                    $infOutros = new \stdClass();
                    $infOutros->tpDoc = $mT085['T085_Tipo_Documento_Originario'];
                    $infOutros->descOutros = $mT085['T085_Descricao_Documento'];
                    $infOutros->nDoc = $mT085['T085_Numero_Nota_Fiscal'];
                    if($mT085['T085_Data_Emissao'] != '0000-00-00') {
                        $infOutros->dEmi = $mT085['T085_Data_Emissao'];
                    }

                    if($mT085['T085_Valor_Total_Nota'] > 0) {
                        $infOutros->vDocFisc = $mT085['T085_Valor_Total_Nota'];
                    }
                    
                    if($mT085['T085_Data_Prevista_Entrega'] != '0000-00-00') {
                        $infDoc->dPrev = $mT085['T085_Data_Prevista_Entrega'];
                    }
                    $this->montador->taginfOutros($infOutros);
                }
                
            }

            $this->montador->taginfDoc();

            ##docAnt

            ##infModal
            $rodo = new \stdClass;
            $rodo->RNTRC = $mT084['C004_RNTRC'];
            $rodo->versao = $this->versao;
            $this->montador->taginfModal($rodo);
            $this->montador->taginfCTeNorm();

        } else if($ide->tpCTe == 1) { //CT-e de Complemento de Valores
            #infCteComp
            $T308 = mysql_query("SELECT T308_Chave_Acesso FROM T308 WHERE T308_T084_Id = '{$T084_Id}'");
            $mT308 = mysql_fetch_array($T308);

            $infCteComp = new \stdClass;
            $infCteComp->chCte = $mT308['T308_Chave_Acesso'];
            $this->montador->taginfCteComp($infCteComp);            
        } else if($ide->tpCTe == 2) {//CT-e de Anulação
            #infCteAnu    
            $T308 = mysql_query("SELECT T308_Chave_Acesso FROM T308 WHERE T308_T084_Id = '{$T084_Id}'");
            $mT308 = mysql_fetch_array($T308);

            $infCteAnu = new \stdClass;
            $infCteAnu->chCte = $mT084['T308_Chave_Acesso'];
            $infCteAnu->dEmi = $dhEmi;
            $this->montador->taginfCteAnu($infCteAnu);
        }

        // Impostos
        $infCTeSupl = new \stdClass;
        $infCTeSupl->UF    = $mT084['T084_Emitente_UF'];     
        $this->montador->taginfCTeSupl($infCTeSupl);
               
        try {
            # Monta o CT-e
            logCte::log($T084_Id, 2);
            $this->montador->montarCTe();

            # Gera o XML
            $xml =  $this->montador->print();                                    
            logCTE::logArquivo("\n");
            logCTE::logArquivo("**************************************************************************************\n");
            logCTE::logArquivo("* XML Gerado: \n");
            logCTE::logArquivo($xml."\n");
            logCTE::logArquivo("**************************************************************************************\n");
            logCTE::logArquivo("\n");                  

            # Verifica os erros
            if(!$this->montador->isValid()) {
                logCTE::logArquivo("Erro ao montar XML:\n\n");
                logCTE::logArquivo($this->montador->getErrors(),true);

                $erros = implode("<br>",$this->montador->getErrors());
                logCte::log($T084_Id,102,0,$erros);

                //Reseta o montador
                $this->montador = new Builder();
                return array(false,$erros);
                // TODO(Gabriel): Decidir se vou criar um classe para retornar os dados do Módulo do CT-e                
            }

            # Assina o XML
            logCTE::log($T084_Id, 3);
            $xmlAssinado = $this->assinarXML($xml);
            logCTE::logArquivo("XML assinado");
            
            # Grava
            $this->gravarCteAssinado($xmlAssinado, "CTe{$chave}");
                        
            //Atualiza o CT-e com a chave 
            mysql_query("UPDATE T084 SET T084_Chave_Acesso_Cte = '{$chave}' WHERE T084_Id = '{$T084_Id}'");            
                     
            //Reseta o montador
            $this->montador = new Builder();
            //Retorna XML assinado
            return array(true,$xmlAssinado);

        } catch(\Exception $ex){
            logCTE::logArquivo("Erro ao preparar O XML: " . $ex->getMessage());
            logCTE::log($T084_Id, 103);
            return array(false,$ex->getMessage());
        }        
    }

    protected function gravarCteAssinado($xml) {        
        logCTE::logArquivo("Cte assinado: {$this->pathAssinado}{$this->montador->id}.xml");   
        file_put_contents("{$this->pathAssinado}{$this->montador->id}.xml",$xml);             
    }

    protected function gravarCteAutorizado($xml,$nomeArquivo,$subPasta) {                
        $path = $this->pathAutorizado;

        if(!empty($subPasta))
            $path .= $subPasta . '/';            

        if (!is_dir($path)) mkdir($path, 0777,true);

        logCTE::logArquivo("gravarCteAutorizado - Caminho Completo: {$path}{$nomeArquivo}.xml");
        file_put_contents("{$path}{$nomeArquivo}.xml",$xml);
    }
    
    /**
     * Envia o CTe e gravar o recibo no CT-e
     * @param   int   $T084_Id
     * @return  array status, mensagem
     **/
    public function enviaCTeSEFAZ($T084_Id) {        
        
        if(empty($T084_Id)) {
            throw new InvalidArgumentException("Nao foi informado o T084_Id");
        }
            
        $T084 = mysql_query("SELECT T084_Chave_Acesso_Cte,
                                    T084_Versao_Cte,
                                    T084_Ambiente_Cte
                               FROM T084 
                              WHERE T084_Id = '{$T084_Id}'");                            
        $mT084 = mysql_fetch_array($T084);
    
        //Monta o Caminho do CT-e Assinado
        $pathCteAssinado = "{$this->pathAssinado}CTe{$mT084['T084_Chave_Acesso_Cte']}.xml";
        
        if(!file_exists($pathCteAssinado)) {            
            return array(false,'CT-e: não foi possível encontrar o arquivo assinado');            
        }

        $xml = file_get_contents($pathCteAssinado);                

        if($this->versao == '3.00'){
            $xmls = array($xml);
            $resultado = $this->enviaCTeLote($xmls);
            $sincrono = false;
        } elseif($this->versao == '4.00'){
            // Mudaça XML 4.00 - Envio sincrono
            $resultado = $this->enviaCTe($xml);
            $sincrono = true;
        }
        
        if($this->erroStatus) {            
            logCTE::log($T084_Id, 104,0,$this->erro);
            return array(false, $this->erro);
        } 

        //Verifica se houve problema com o retorno da SEFAZ
        if(!isset($resultado->cStat)) {            
            logCTE::log($T084_Id, 101);
            return array(false, "Erro ao comunicar com o SEFAZ");
        }      

        //Verifica se houve rejeição
        //if($resultado->cStat != 103) {
        if($resultado->cStat != 103 && $resultado->cStat != 100) {
            // A rejeição 204 informa que a SEFAZ já autorizou esta numeração.
            // Não se deve retransmitir o XML: consulta-se a própria chave, com
            // certificado digital, e somente se grava a autorização confirmada.
            if ($resultado->cStat == 204) {
                $recuperacao = $this->recuperaAutorizacaoDuplicidade($T084_Id, false);
                if ($recuperacao[0]) {
                    return $recuperacao;
                }
            }
            //Gravar a rejeição
            logCTE::log($T084_Id, 202, $resultado->cStat,$resultado->xMotivo);
            return array(false, "Rejeição na SEFAZ");
        }      
        
        if ($resultado->cStat == 100 && $sincrono) {

            $this->processaRetornoCTE($this->retornoSEFAZ, $T084_Id);

        } else {
            // Gravar o recibo no conhecimento        
            mysql_query("UPDATE T084 
                            SET T084_Numero_Recibo_Cte = '{$resultado->nRec}'
                          WHERE T084_Id = '{$T084_Id}'");              
            logCte::log($T084_Id,7, $resultado->cStat,$resultado->xMotivo);  
        }

        return array(true, "CT-e enviado");
    }

    /**
     * Recupera o protocolo de um CT-e que a SEFAZ já autorizou (rejeição 204).
     * Esta rotina não transmite nem recria o documento fiscal.
     */
    public function recuperaAutorizacaoDuplicidade($T084_Id, $exigirErro204 = true)
    {
        global $g;

        $T084_Id = mysql_real_escape_string($T084_Id);
        $sql = "SELECT T084_Id, T084_Chave_Acesso_Cte, T084_Data_Emissao,
                       T084_Numero_Protocolo_Cte, T084_TX_Retorno_Codigo
                  FROM T084
                 WHERE T084_Id = '{$T084_Id}'
                   AND T084_C004_Id = '{$g['empresaAtual']}'";
        $consulta = mysql_query($sql);
        $mT084 = mysql_fetch_assoc($consulta);

        if (!$mT084) {
            return array(false, 'CT-e não localizado para a empresa selecionada.');
        }
        if (!empty($mT084['T084_Numero_Protocolo_Cte'])) {
            return array(false, 'O CT-e já possui protocolo de autorização.');
        }
        if ($exigirErro204 && $mT084['T084_TX_Retorno_Codigo'] != '204') {
            return array(false, 'A recuperação é permitida somente após a rejeição 204 da SEFAZ.');
        }
        if (empty($mT084['T084_Chave_Acesso_Cte'])) {
            return array(false, 'CT-e sem chave de acesso para consulta.');
        }

        logCTE::logArquivo("Consultando autorização por duplicidade: {$mT084['T084_Chave_Acesso_Cte']}");
        $retorno = $this->consultaChave($mT084['T084_Chave_Acesso_Cte']);
        if ($this->erroStatus || !$retorno) {
            logCTE::logArquivo("Falha ao consultar duplicidade: {$this->erro}");
            return array(false, 'Não foi possível consultar a autorização na SEFAZ.');
        }

        // A consulta pode retornar um documento cancelado, denegado ou uma
        // chave distinta. Nenhum desses casos pode ser promovido a autorizado.
        if ($retorno->cStat != '100'
            || !isset($retorno->protCStat) || $retorno->protCStat != '100'
            || !isset($retorno->chCTe) || $retorno->chCTe !== $mT084['T084_Chave_Acesso_Cte']
            || empty($retorno->nProt) || empty($retorno->dhRecbto)) {
            logCTE::logArquivo('Consulta de duplicidade não confirmou autorização para a mesma chave.');
            return array(false, 'A SEFAZ não confirmou uma autorização válida para esta chave.');
        }

        $pathCteAssinado = "{$this->pathAssinado}CTe{$mT084['T084_Chave_Acesso_Cte']}.xml";
        if (!file_exists($pathCteAssinado)) {
            return array(false, 'Não foi possível encontrar o XML assinado para vincular ao protocolo.');
        }

        $xmlAssinado = file_get_contents($pathCteAssinado);
        $xmlCTeProtocolado = $this->adicionaProtocolo(
            $xmlAssinado,
            $this->retornoSEFAZ,
            $mT084['T084_Chave_Acesso_Cte']
        );
        if (empty($xmlCTeProtocolado)) {
            return array(false, 'Não foi possível montar o XML autorizado retornado pela SEFAZ.');
        }

        $dataRecebimento = substr(str_replace('T', ' ', $retorno->dhRecbto), 0, 19);
        $anoMesCte = substr($mT084['T084_Data_Emissao'], 0, 7);
        $this->gravarCteAutorizado($xmlCTeProtocolado, $retorno->nProt, $anoMesCte);

        mysql_query("UPDATE T084
                        SET T084_Numero_Protocolo_Cte = '{$retorno->nProt}',
                            T084_Arquivo_XML = '" . mysql_real_escape_string($xmlCTeProtocolado) . "',
                            T084_Data_Hora_Protocolo_CTe = '{$dataRecebimento}'
                      WHERE T084_Id = '{$mT084['T084_Id']}'
                        AND T084_C004_Id = '{$g['empresaAtual']}'");
        if (mysql_error()) {
            return array(false, 'Erro ao gravar a autorização recuperada.');
        }

        logCTE::log($mT084['T084_Id'], 8, $retorno->protCStat, 'CT-e autorizado por consulta após duplicidade 204');
        if (file_exists($pathCteAssinado)) {
            unlink($pathCteAssinado);
        }
        return array(true, 'CT-e autorizado por consulta à SEFAZ.');
    }
    
     /**
     * Consulta recibo
     * @param   string  $numRecibo
     * @param   int  $ambiente
     * @return  array status, mensagem
     **/
    public function consultaReciboSEFAZ($numRecibo, $ambiente)
    {   
        logCTE::logArquivo("Consulta Recibo");

        if(empty($numRecibo)) {
            throw new InvalidArgumentException('não foi informado o numero do recibo');
        }
                
        $retorno = $this->consultaRecibo($numRecibo, $ambiente);                                          
       
        //Erro de comunicação com o SEFAZ
        if($this->erroStatus) {
            logCTE::logArquivo("Erro ao comunicar com o SEFAZ");
            return array(false, $this->erro);
        }
                
        //Busca os CT-e com o recibo
        $T084 = mysql_query("SELECT T084_Id, 
                                    T084_Chave_Acesso_Cte, 
                                    T084_Data_Emissao 
                                FROM T084 
                                WHERE T084_Numero_Recibo_Cte  = '{$numRecibo}'");            

        logCTE::logArquivo("Retorno Consulta Recibo:\n");
        logCTE::logArquivo($retorno,true);

        while($mT084 = mysql_fetch_array($T084)) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            //Lote em processamento | Lote Nao localizado
            if($retorno->cStat == 105 || $retorno->cStat == 106) {
                logCTE::log($mT084['T084_Id'], 7, $retorno->cStat, $retorno->xMotivo);                    
                continue;
            }

            //Recibo ou CNPJ do requisitante com problemas
            if($retorno->cStat == 248 || $retorno->cStat ==223) {
                logCTE::log($mT084['T084_Id'], 202, $retorno->cStat, $retorno->xMotivo);                    
                continue;
            }

            $cteRetorno = $retorno->protCTe["{$mT084['T084_Chave_Acesso_Cte']}"];

            logCTE::logArquivo("CHAVE: {$mT084['T084_Chave_Acesso_Cte']}\n");
            logCTE::logArquivo("CTE RETORNO:\n\n");
            logCTE::logArquivo($cteRetorno, true);
            
            //Rejeicao
            if($cteRetorno->cStat != 100 && $cteRetorno->cStat != 110 && $cteRetorno->cStat != 205 && $cteRetorno->cStat != 301) {
                logCTE::logArquivo("CT-e Rejeitado");
                logCTE::log($mT084['T084_Id'], 202, $cteRetorno->cStat, $cteRetorno->xMotivo);                    
                continue;
            }
            
            // Adiciona o protocolo no XML
            
            // Carrega XML assinado
            $pathCteAssinado = "{$this->pathAssinado}CTe{$mT084['T084_Chave_Acesso_Cte']}.xml";
            if(!file_exists($pathCteAssinado)) 
                return  array(false,"Não foi possível encontrar o xml assinado para adicionar o protocolo");

            $xmlAssinado = file_get_contents($pathCteAssinado);
            $xmlCTeProtocolado = $this->adicionaProtocolo($xmlAssinado,$this->retornoSEFAZ, $mT084['T084_Chave_Acesso_Cte']);

            // Grava o XML com protocolo
            $anoMesCte=substr($mT084['T084_Data_Emissao'],0,7);
            $this->gravarCteAutorizado($xmlCTeProtocolado, $cteRetorno->nProt,$anoMesCte);

            $dataRecebimento = str_replace("T"," ",$cteRetorno->dhRecbto);
            $dataRecebimento  = substr($dataRecebimento, 0, 19);          
                        
            //Autorizado
            logCTE::logArquivo("\nCT-e Autorizado: $cteRetorno->nProt");            
            $cClausula  = " UPDATE T084 SET ";
            $cClausula .= " T084_Numero_Protocolo_Cte = '{$cteRetorno->nProt}', ";                
            $cClausula .= " T084_Arquivo_XML = '{$xmlCTeProtocolado}', ";                
            $cClausula .= " T084_Data_Hora_Protocolo_CTe = '{$dataRecebimento}'";                
            $cClausula .= " WHERE T084_Id='{$mT084['T084_Id']}'";            
            mysql_query($cClausula);

            $cErro = mysql_error();
            if (!empty($cErro)) {
                logCTE::log($mT084['T084_Id'], 105,$cteRetorno->cStat,$cteRetorno->xMotivo);                                
                return array(false,"Erro: " . $cErro . "    " . $cClausula);
            }
            //Atualiza o Status
            logCTE::log($mT084['T084_Id'], 8,$cteRetorno->cStat,$cteRetorno->xMotivo);                                
            if (file_exists($pathCteAssinado)) unlink($pathCteAssinado);                     
            
            //Denegada
            if($cteRetorno->cStat == 110 || $cteRetorno->cStat == 205 || $cteRetorno->cStat == 301) {
                
                //Apagar os titulos a receber e a pagar
                //Cancela Contas a receber
                mysql_query("UPDATE T002 
                                SET T002_Flag_Cancelada='S',
                                    T002_Data_Recebimento=current_date(),
                                    T002_Observacao_2='*** CT-E DENEGADO ***',
                                    T002_Valor_Duplicata=0,
                                    T002_Valor_Total=0                            
                                WHERE T002_T084_Id='{$mT084['T084_Id']}'");

                //Cancela boleto se existir
                $T002 = mysql_query("SELECT T002_T117_Id
                                       FROM T002
                                      WHERE T002_T084_Id='{$mT084['T084_Id']}'
                                        AND IFNULL(T002_T117_Id,0) > 0");

                while($mT002 = mysql_fetch_assoc($T002)){                
                    mysql_query("UPDATE T117
                                    SET T117_Flag_Cancelado = 'S'
                                  WHERE T117_Id = '{$mT002['T002_T117_Id']}'");
                }

                //Cancela Contas a pagar
                mysql_query("UPDATE T015 
                                SET T015_Flag_Cancelada='S',
                                    T015_Data_Pagamento=current_date(),
                                    T015_Observacao_2='*** CT-E DENEGADO ***',
                                    T015_Valor_Duplicata=0,
                                    T015_Valor_Total=0
                                WHERE T015_T084_Id='{$mT084['T084_Id']}'");

                //Setar cancelamento e denegado                
                mysql_query("UPDATE T084 
                                SET T084_Flag_Denegado  = 'S',
                                    T084_Observacao_Cancelamento = 'CT-E DENEGADO',
                                    T084_Valor_Conhecimento = 0,
                                    T084_Valor_Base_ICMS = 0,
                                    T084_Valor_ICMS = 0,
                                    T084_Valor_Receber = 0
                                WHERE T084_Id = '{$mT084['T084_Id']}'");
            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
        }        
    }

    /**
     * processaRetornoCTE
     * @param   string  $retornoSefaz
     * @param   int  $T084_Id
     * @return  bool 
     **/
    public function processaRetornoCTE($retornoEnviaCTE = NULL, $T084_Id)
    {   
       
        //Converte resposta, tratar dados de retorno
        $doc = new \DOMDocument();
        $doc->formatOutput = false;
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($retornoEnviaCTE, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

        $retorno = new \stdClass;
        // status do recebimento ou mensagem de erro
        $retorno->cStat    = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
        $retorno->verAplic = $doc->getElementsByTagName('verAplic')->item(0)->nodeValue;
        $retorno->xMotivo  = $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue;
        $retorno->nRec     = $doc->getElementsByTagName('nRec')->item(0)->nodeValue;

        // CTE Processado com sucesso = 100
        if ($retorno->cStat == '100') {
            $retorno->protCTe = array();
            foreach ($doc->getElementsByTagName('protCTe') as $node) {
                $protCTe = new \stdClass;
                $protCTe->tpAmb    = $node->getElementsByTagName('tpAmb')->item(0)->nodeValue;
                $protCTe->verAplic = $node->getElementsByTagName('verAplic')->item(0)->nodeValue;
                $protCTe->chCTe    = $node->getElementsByTagName('chCTe')->item(0)->nodeValue;
                $protCTe->dhRecbto = $node->getElementsByTagName('dhRecbto')->item(0)->nodeValue;
                $protCTe->nProt    = $node->getElementsByTagName('nProt')->item(0)->nodeValue;
                $protCTe->digVal   = $node->getElementsByTagName('digVal')->item(0)->nodeValue;
                $protCTe->cStat    = $node->getElementsByTagName('cStat')->item(0)->nodeValue;
                $protCTe->xMotivo  = $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
                
                $retorno->protCTe[$protCTe->chCTe] = $protCTe;
            }
        }           
        

        //Erro de comunicação com o SEFAZ
        if($this->erroStatus) {
            logCTE::logArquivo("Erro ao comunicar com o SEFAZ");
            return array(false, $this->erro);
        }
                
        //Busca os CT-e com o recibo
        $T084 = mysql_query("SELECT T084_Id, 
                                    T084_Chave_Acesso_Cte, 
                                    T084_Data_Emissao 
                               FROM T084 
                              WHERE T084_Id = '{$T084_Id}'");            


        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        while($mT084 = mysql_fetch_array($T084)) {

            $cteRetorno = $retorno->protCTe["{$mT084['T084_Chave_Acesso_Cte']}"];
            
            //Rejeicao
            if($cteRetorno->cStat != 100 && $cteRetorno->cStat != 110 && $cteRetorno->cStat != 205 && $cteRetorno->cStat != 301) {
                logCTE::logArquivo("CT-e Rejeitado");
                logCTE::log($mT084['T084_Id'], 202, $cteRetorno->cStat, $cteRetorno->xMotivo);                    
                continue;
            }
            
            // Adiciona o protocolo no XML
            
            // Carrega XML assinado
            $pathCteAssinado = "{$this->pathAssinado}CTe{$mT084['T084_Chave_Acesso_Cte']}.xml";
            if(!file_exists($pathCteAssinado)){
                return  array(false,"Não foi possível encontrar o xml assinado para adicionar o protocolo");
            } 

            $xmlAssinado       = file_get_contents($pathCteAssinado);
            //$xmlCTeProtocolado = $this->adicionaProtocolo($xmlAssinado, $this->retornoSEFAZ, $mT084['T084_Chave_Acesso_Cte']);
            $xmlCTeProtocolado = $this->adicionaProtocolo($xmlAssinado, $retornoEnviaCTE, $mT084['T084_Chave_Acesso_Cte']);

            // Grava o XML com protocolo
            $anoMesCte = substr($mT084['T084_Data_Emissao'],0,7);
            $this->gravarCteAutorizado($xmlCTeProtocolado, $cteRetorno->nProt,$anoMesCte);

            $dataRecebimento = str_replace("T"," ",$cteRetorno->dhRecbto);
            $dataRecebimento  = substr($dataRecebimento, 0, 19);  
            
            logCTE::logArquivo("\n");
            logCTE::logArquivo("**************************************************************************************\n");
            logCTE::logArquivo("CT-e Autorizado: $cteRetorno->nProt \n");   
            logCTE::logArquivo("CT-e Autorizado: $dataRecebimento \n");           
            logCTE::logArquivo("**************************************************************************************\n");
            logCTE::logArquivo("\n"); 
                        
            //Autorizado
            $cClausula  = " UPDATE T084 SET ";
            $cClausula .= " T084_Numero_Protocolo_Cte    = '{$cteRetorno->nProt}', ";                
            $cClausula .= " T084_Arquivo_XML             = '{$xmlCTeProtocolado}', ";                
            $cClausula .= " T084_Data_Hora_Protocolo_CTe = '{$dataRecebimento}'";                
            $cClausula .= " WHERE T084_Id = '{$mT084['T084_Id']}'";            
            mysql_query($cClausula);

            $cErro = mysql_error();
            if (!empty($cErro)) {
                logCTE::log($mT084['T084_Id'], 105,$cteRetorno->cStat,$cteRetorno->xMotivo);                                
                return array(false,"Erro: " . $cErro . "    " . $cClausula);
            }

            //Atualiza o Status
            logCTE::log($mT084['T084_Id'],8,$cteRetorno->cStat,$cteRetorno->xMotivo); 
                                         
            if (file_exists($pathCteAssinado)) unlink($pathCteAssinado);                     
            
            //Denegada
            if($cteRetorno->cStat == 110 || $cteRetorno->cStat == 205 || $cteRetorno->cStat == 301) {
                
                //Apagar os titulos a receber e a pagar
                //Cancela Contas a receber
                mysql_query("UPDATE T002 
                                SET T002_Flag_Cancelada='S',
                                    T002_Data_Recebimento=current_date(),
                                    T002_Observacao_2='*** CT-E DENEGADO ***',
                                    T002_Valor_Duplicata=0,
                                    T002_Valor_Total=0                            
                              WHERE T002_T084_Id='{$mT084['T084_Id']}'");

                //Cancela boleto se existir
                $T002 = mysql_query("SELECT T002_T117_Id
                                       FROM T002
                                      WHERE T002_T084_Id='{$mT084['T084_Id']}'
                                        AND IFNULL(T002_T117_Id,0) > 0");

                while($mT002 = mysql_fetch_assoc($T002)){                
                    mysql_query("UPDATE T117
                                    SET T117_Flag_Cancelado = 'S'
                                  WHERE T117_Id = '{$mT002['T002_T117_Id']}'");
                }

                //Cancela Contas a pagar
                mysql_query("UPDATE T015 
                                SET T015_Flag_Cancelada='S',
                                    T015_Data_Pagamento=current_date(),
                                    T015_Observacao_2='*** CT-E DENEGADO ***',
                                    T015_Valor_Duplicata=0,
                                    T015_Valor_Total=0
                              WHERE T015_T084_Id='{$mT084['T084_Id']}'");

                //Setar cancelamento e denegado                
                mysql_query("UPDATE T084 
                                SET T084_Flag_Denegado  = 'S',
                                    T084_Observacao_Cancelamento = 'CT-E DENEGADO',
                                    T084_Valor_Conhecimento = 0,
                                    T084_Valor_Base_ICMS = 0,
                                    T084_Valor_ICMS = 0,
                                    T084_Valor_Receber = 0
                              WHERE T084_Id = '{$mT084['T084_Id']}'");
            }
        }        

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
    }

    /**
    * enviarLoteCTe    
    * @param String $numLote
    * @return array status, mensagem
    **/ 
    public function enviaCTeLoteSEFAZ($numLote) {
        global $g;
   
        logCTE::logArquivo("NUMERO LOTE: $numLote");
        if(empty($numLote))
            throw new InvalidArgumentException("Não foi informado o número do Lote");

        $T084 = mysql_query("SELECT T084_Id, T084_Numero_Conhecimento, T084_Chave_Acesso_Cte
                                FROM T084
                            WHERE T084_Numero_Lote = '{$numLote}'
                            AND T084_C004_Id = '{$g['empresaAtual']}'");        
        $xmls = array();
    
        try {                     
            while($mT084 = mysql_fetch_array($T084)) {
                logCTE::logArquivo("\n\nXML CT-e '{$mT084['T084_Id']}'\n");
                logCTE::logArquivo("Preparando CT-e {$mT084['T084_Numero_Conhecimento']}");
                list($status,$retorno) = $this->preparaCte($mT084['T084_Id'], $mT084['T084_Chave_Acesso_Cte']);                                        

                // Caso encontre na montagem do XML
                if(!$status) {
                    //Remove CT-e do Lote
                    mysql_query("UPDATE T084 
                                    SET T084_Flag_Enviar = 'N', 
                                        T084_Numero_Lote = '1' 
                                    WHERE T084_Id = '{$mT084['T084_Id']}'");
                    continue;             
                }

                $xmls[] = $retorno;                    
                unset($retorno);
            }                    
    
            if(count($xmls) == 0) 
                return array(false, "Nenhum CT-e para transmitir");
                            
            $retorno = $this->enviaCTeLote($xmls, $numLote);     

            log("\n\nRetorno:\n");
            logCTE::logArquivo($this->retornoSEFAZ,true);
    
            //Caso o SEFAZ nao retornar nada
            if($this->erroStatus) {            
                logCTE::logLote(104,$numLote);
                logCTE::logArquivo("ENTROU ERRO");
                return array(false, $this->erro);
            }            
    
            // Verifica se houve problema com o retorno da SEFAZ
            if(!isset($retorno->cStat)) {
                logCTE::logLote(101,$numLote);
                return array(false,"ERRO: PROBLEMA NA COMUNICAÇÃO COM O SEFAZ");                
            }

            //Verifica se tem recibo
            if($retorno->cStat != 103) {            
                //Gravar a rejeição
                logCTE::logLote(202, $numLote,$retorno->cStat,$retorno->xMotivo);
                return array(false,"Rejeição na SEFAZ");
            }
    
            //Gravar o recibo no conhecimento        
            mysql_query("UPDATE T084 
                            SET T084_Numero_Recibo_Cte = '{$retorno->nRec}'  ,
                                T084_Flag_Enviar = 'N'
                        WHERE T084_Numero_Lote = '{$numLote}' AND T084_C004_Id = '{$g['empresaAtual']}'");          
    
            logCte::logLote(7, $numLote,$retorno->cStat,$retorno->xMotivo);   
            return array(true,"Lote Ct-e enviado");  
            
        } catch (\Exception $ex) {
            logCTE::logArquivo("\n\nException:" . $ex->getMessage());
            return array(false, $ex->getMessage());
        }    
    }

}
