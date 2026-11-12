<?php

namespace hardness;

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\ValidatorException;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;

class RadarNFe
{
    private Tools $tools;
    private const RESUMO_NFE = 'resNFe';
    private const PROC_NFE = 'procNFe';
    private const RESUMO_EVENTO = 'resEvento';
    private const PROC_EVENTO = 'procEventoNFe';
    public const EVENTO_CIENCIA = 210210;
    public const EVENTO_CONFIRMACAO = 210200;
    public const EVENTO_DESCONHECIMENTO = 210220; 
    public const EVENTO_NAO_REALIZADA = 210240;


    public function __construct($config)
    {        
        $configJson = json_encode($config);        
        $content = file_get_contents($config['caminhoCertificado']);
        $this->tools = new Tools($configJson, Certificate::readPfx($content, $config['senhaCertificado']));
        $this->tools->model('55');        
        $this->tools->setEnvironment($config['tpAmb']);                  
    }

    public function buscarDocumentos()
    {
        global $g;          

        if($this->tools->tpAmb === 2) {
            return "A busca de notas só funciona em ambiente de produção!";
        }

        $RNE002 = mysql_query("SELECT RNE002_Ultimo_NSU, RNE002_Maior_NSU FROM RNE002 WHERE RNE002_C004_Id = '{$g['empresaAtual']}'");
        $mRNE002 = mysql_fetch_array($RNE002);

        $ultimoNSUBase = (int)ltrim($mRNE002['RNE002_Ultimo_NSU'],'0');
        $ultimoNSU =  $ultimoNSUBase;
        $maxNSU = $ultimoNSU;
        $loopLimit = 20;
        $count = 0;
    
        while ($ultimoNSU <= $maxNSU) {            
            $count++;
            if ($count > $loopLimit) {
                break;
            }
            
            try {
                $resp = $this->tools->sefazDistDFe($ultimoNSU);                              
            } catch (\Exception $e) {                
                return "Erro ao comunicar com a SEFAZ, tente novamente mais tarde";
            }

    
            $dom = new \DOMDocument();
            $dom->loadXML($resp);
            $node = $dom->getElementsByTagName('retDistDFeInt')->item(0);                        
            $cStat = $node->getElementsByTagName('cStat')->item(0)->nodeValue;
            $xMotivo = $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            $dhResp = $node->getElementsByTagName('dhResp')->item(0)->nodeValue;
            $ultimoNSU = $node->getElementsByTagName('ultNSU')->item(0)->nodeValue;
            $maxNSU = $node->getElementsByTagName('maxNSU')->item(0)->nodeValue;
            $lote = $node->getElementsByTagName('loteDistDFeInt')->item(0);
            
            //Grava histórico da consulta
            mysql_query("INSERT INTO RNE005 (RNE005_NSU_Consultado, RNE005_Data_Hora,RNE005_C007_Id,RNE005_Maior_NSU, RNE005_Ultimo_NSU, RNE005_C004_Id, RNE005_Status,RNE005_Motivo)
                    VALUES('{$ultimoNSUBase}', NOW(), '{$g['usuarioAtual']}', '{$maxNSU}', '{$ultimoNSU}','{$g['empresaAtual']}', '{$cStat}', '{$xMotivo}')");                                    

            //137 - Nenhum documento localizado
            //656 - Consumo Indevido
            if (in_array($cStat, ['137', '656'])) {                              
                mysql_query("INSERT INTO RNE002 (RNE002_Ultima_Consulta,RNE002_C007_Id,RNE002_C004_Id) VALUES(NOW(),'{$g['usuarioAtual']}','{$g['empresaAtual']}')
                                ON DUPLICATE KEY UPDATE RNE002_Ultima_Consulta = NOW(), RNE002_C007_Id = '{$g['usuarioAtual']}'");
                            
                return "<b>{$cStat} - {$xMotivo}.</b><br>Por favor, evite consultas em outros sistemas.";
            }

            //138 - Documentos localizados               
            if($cStat !=  138) {
                return "{$cStat} - {$xMotivo}.";
            }

            //Atualizar ultimo NSU
            mysql_query("INSERT INTO RNE002 
                                    (RNE002_Ultimo_NSU,RNE002_Maior_NSU,RNE002_C004_Id) 
                                VALUES('{$ultimoNSU}','{$maxNSU}', '{$g['empresaAtual']}')
                            ON DUPLICATE KEY UPDATE RNE002_Ultimo_NSU = '{$ultimoNSU}', RNE002_Maior_NSU = '{$maxNSU}'");
              
            
            if (empty($lote)) {                                                                
                continue;
            }

            // Documentos, pode ser de nota ou evento, limite de 50 por consulta
            $docs = $lote->getElementsByTagName('docZip');            
            foreach ($docs as $doc) {
                $numNSU = $doc->getAttribute('NSU');                
                $schema = explode('_', $doc->getAttribute('schema'))[0];
                $content = gzdecode(base64_decode($doc->nodeValue));                
                $retorno = $this->processaRetorno($numNSU, $schema, $content);

                if($retorno !== true) {                    
                    mysql_query("INSERT INTO RNE002 (RNE002_Ultimo_NSU,RNE002_Maior_NSU,RNE002_C004_Id) 
                            VALUES('{$ultimoNSUBase}','{$maxNSU}', '{$g['empresaAtual']}')
                        ON DUPLICATE KEY UPDATE RNE002_Ultimo_NSU = '{$ultimoNSUBase}'");
                    return $retorno;
                }
            }

            if ($ultimoNSU == $maxNSU) {
                break;
            }

            $ultimoNSUBase = $ultimoNSU;

            sleep(2);
        }

        return "Atualizado com sucesso!";
    }


    public function processaRetorno($numNSU, $schema, $content)
    {
        global $g;        
        $st = new Standardize($content);
        $std = $st->toStd();

        if ($schema == self::RESUMO_NFE) {

            $dataEmissao = str_replace("T", " ", $std->dhEmi);
            $dataEmissao = substr($dataEmissao, 0, 19);
            $xNome       = mysql_real_escape_string($std->xNome);

            $query = "INSERT INTO RNE001 (RNE001_Chave_Acesso,
                                            RNE001_CNPJ,
                                            RNE001_Razao_Social,
                                            RNE001_Data_Emissao,
                                            RNE001_Numero_Protocolo,
                                            RNE001_Situacao_NFe,
                                            RNE001_Inscricao_Estadual,
                                            RNE001_Valor_Total,                                            
                                            RNE001_NSU,
                                            RNE001_C004_Id                                           
                                            )
                                    VALUES(
                                        '{$std->chNFe}',
                                        '{$std->CNPJ}',
                                        '{$xNome}',
                                        '{$dataEmissao}',
                                        '{$std->nProt}',
                                        '{$std->cSitNFe}',
                                        '{$std->IE}',
                                        '{$std->vNF}',                                        
                                        '{$numNSU}',
                                        '{$g['empresaAtual']}'                                        
                                    ) ON DUPLICATE KEY UPDATE RNE001_Data_Emissao = '{$dataEmissao}'";

            mysql_query($query);        
            $error = mysql_error();
            if (!empty($error)) {                
                return "Erro: {$error}";
            }

        } else if($schema == self::RESUMO_EVENTO) {    
            $dataRecebimento = str_replace("T"," ",$std->dhRecbto);
            $dataRecebimento  = substr($dataRecebimento, 0, 19);     

            $query = "INSERT INTO RNE004 (RNE004_CNPJ,
                                            RNE004_Chave_Acesso,
                                            RNE004_Tipo_Evento,
                                            RNE004_Descricao_Evento,
                                            RNE004_Numero_Protocolo,
                                            RNE004_Data_Hora_Autorizado,
                                            RNE004_NSU,
                                            RNE004_C004_Id
                                        )
                                    VALUES(
                                        '{$std->CNPJ}',
                                        '{$std->chNFe}',
                                        '{$std->tpEvento}',
                                        '{$std->xEvento}',
                                        '{$std->nProt}',                                                                                                                      
                                        '{$dataRecebimento}',                                                                                                                      
                                        '{$numNSU}',
                                        '{$g['empresaAtual']}'                                        
                                    ) ON DUPLICATE KEY UPDATE RNE004_Data_Hora_Autorizado = '{$dataRecebimento}'";

            mysql_query($query);        
            $error = mysql_error();
            if (!empty($error)) {                
                return "Erro: {$error}";
            }

        } else if ($schema == self::PROC_NFE) {
            $path =  $g['pathDadosAntigo'] . 'nfe/xml/baixado/';
            if (!is_dir($path)) mkdir($path, 0777, true);
            $protocolo = $std->protNFe->infProt->nProt;
            $path = "{$path}{$protocolo}.xml";
            if(!file_exists($path)) {
                if(file_put_contents($path, $content) === false) {
                    return "Não foi possível salvar o arquivo, caminho: {$path}";
                }
            }            
        }

        return true;
    }

    public function manifestaNFe($nsu, $tipoEvento, $justificativa = '') {
        global $g;
        
        if($tipoEvento === self::EVENTO_NAO_REALIZADA && empty($justificativa) ) {
            return array(false, 'É necessário informar a justificativa para Evento de Operação não Realizada!');
        }

        $RNE001 = mysql_query("SELECT   RNE001_Id, 
                                        RNE001_Chave_Acesso                                        
                                FROM RNE001                                 
                                WHERE RNE001_NSU = '{$nsu}' AND RNE001_C004_Id = '{$g['empresaAtual']}'");

        $mRNE001 = mysql_fetch_array($RNE001);    
               
        try {
                     
            $response = $this->tools->sefazManifesta($mRNE001['RNE001_Chave_Acesso'],$tipoEvento,$justificativa); 
            
            $std = new Standardize($response);    
            $std =  $std->toStd();

            //Lote não foi processado
            if ($std->cStat != 128) { 
                return array(false,"{$std->cStat} - {$std->xMotivo}");
            }

            $retEvento = $std->retEvento->infEvento;
            $cStat = $retEvento->cStat;
            
            // Evento retornou rejeição
            if ($cStat != '135' && $cStat != '136') {
                return array(false,"{$cStat} - {$retEvento->xMotivo}");
            }
                            
            $xml = Complements::toAuthorize($this->tools->lastRequest, $response);
                               
            //Armazenar na tabela de evento
            $query = "INSERT INTO RNE003 (                                            
                                            RNE003_Status,
                                            RNE003_Motivo,
                                            RNE003_Codigo_Evento,
                                            RNE003_Retorno_XML,
                                            RNE003_RNE001_Id                                          
                                        )
                                        VALUES(                                          
                                            '{$retEvento->cStat}',
                                            '{$retEvento->xMotivo}',
                                            '{$retEvento->tpEvento}',
                                            '{$xml}',
                                            '{$mRNE001['RNE001_Id']}'
                                        )";            
            mysql_query($query);
                   
            
            mysql_query("UPDATE RNE001 SET RNE001_Situacao_Manifesto = '{$retEvento->tpEvento}' WHERE RNE001_Id = '{$mRNE001['RNE001_Id']}' AND RNE001_C004_Id = '{$g['empresaAtual']}'");

        } catch(ValidatorException $e) {
            return array(false, $e->getMessage());
        } catch(\Exception $e) {               
            return array(false,"Houve um erro ao tentar fazer a manifestação dessa nota, contate o Suporte Técnico");
        }

        return array(true, "Manifestação realizada com sucesso!");
    }
}
