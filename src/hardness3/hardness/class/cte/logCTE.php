<?php

namespace hardness;



/**
* Código de erros internos
* TX_Resposta_Codigo = Numeracao Interna
* TX_Retorno SEFAZ = Codigo SEFAZ- Motivo
* TX_Resposta_EXTRA = Codigo SEFAZ
* TX_Flag_Erro = Se houve erro ou não 1 - true 0 -false
* TX_Timestamp_Envio_SEFAZ = envio
*/
// TODO(Gabriel): Repensar como vou lidar com os status e erros
class logCTE {
    
    public static array $codigos = array(
        0	=> '',        
        1   => 'Iniciando Processo ...',
        2	=> 'Gerando XML ...',
        3	=> 'Assinando XML ...',
        4	=> 'Transmitindo CT-e para a SEFAZ ...',
        5	=> 'XML Gerado! Assinando ...',
        6	=> 'XML Assinado! Transmitindo ...',                        
        7	=> 'Aguardando retorno da Sefaz ...',    
        8   => 'CT-e Enviado e Autorizado!',        
        

        //Erros Internos
        100 => 'Certificado Digital não encontrado.',                
        101 => 'Erro na hora de comunicar com o SEFAZ',
        102 => 'Erro Gerando XML!',	        
        103 => 'Erro ao preparar o XML',
        104 => 'Erro ao transmitir CT-e',
        105 => 'Erro Interno: Tentando gravar o protocolo',

        // 200 - 300 (Dentro do erro da Sefaz)        
        201 => 'Nao foi possivel limpar a chave de acesso, pois esta nota ja tem protocolo.',
        202 => 'Erro Sefaz',
    );
        
    /**
    * Atualiza status da T084 
    * @param int $codigo
    * @param string $extra
    */
     public static function log($T084_Id, $codigoInterno, $codigoSEFAZ = '', $motivo = '') {
        $T084 = mysql_query("SELECT 
                                T084_Id,                                
                                T084_TX_Resposta_Codigo_Interno,
                                T084_TX_Retorno_Codigo,
                                T084_TX_Retorno_Motivo                                                                
                            FROM T084 
                            WHERE T084_Id = '{$T084_Id}'");

        $mT084 = mysql_fetch_array($T084);        
        
        $codigo_ = mysql_real_escape_string($codigoInterno);
        $codigoSEFAZ_ = mysql_real_escape_string($codigoSEFAZ);
        $motivo_ = mysql_real_escape_string($motivo);        
        mysqli_query("UPDATE T084 SET 
                            T084_TX_Resposta_Codigo_Interno = '{$codigo_}', 
                            T084_TX_Retorno_Codigo = '{$codigoSEFAZ_}', 
                            T084_TX_Retorno_Motivo = '{$motivo_}'
                        WHERE T084_Id = '{$mT084['T084_Id']}'");                       
    }    

    /**
    * Atualiza status da T084 
    * @param int $codigo
    * @param string $extra
    */
    public static function logLote($codigoInterno, $numLote, $codigoSEFAZ = '', $retornoSefaz = '') {
        global $g;                
        $codigo_ = mysql_real_escape_string($codigoInterno);
        $codigoSEFAZ_ = mysql_real_escape_string($codigoSEFAZ);
        $retornoSefaz_ = mysql_real_escape_string($retornoSefaz);        
        $numLote_ = mysql_real_escape_string($numLote);        

        mysqli_query("UPDATE T084 SET 
                            T084_TX_Resposta_Codigo_Interno = '{$codigo_}', 
                            T084_TX_Retorno_Codigo = '{$codigoSEFAZ_}', 
                            T084_TX_Retorno_Motivo = '{$retornoSefaz_}'
                        WHERE T084_Numero_Lote = '{$numLote_}'
                            AND T084_C004_Id = '{$g['empresaAtual']}'");                                   
    }    

    public static function logArquivo($message,$printR = false) {    
        global $g;

        chdir($g['pathRaiz']);

        $dir = 'dados_usuarios/logs';

        is_dir($dir) OR mkdir($dir, 0777, true);

        $fileName = 'dados_usuarios/logs/cte.log';

        if(!file_exists($fileName)) {
            touch($fileName);
        }

        if($printR == true) {
            $message = print_r($message,true);         
        }

        file_put_contents($fileName,"$message\n", FILE_APPEND);                
    }
}