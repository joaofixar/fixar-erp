<?php
namespace hardness;
/**
* Etapa 2 - Assinatura do XML
*/
require_once('scripts/queueNF/etapa3-enviarXml-function.php');

echo "\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "**\n";
echo "**                     E T A P A  -  2\n";
echo "** \n";
echo "**                     Assinar XML \n";
echo "**\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "\n";

$logTxt = "\r\n Etapa 2 - Assinar XML. \r\n";
$r = fwrite($h, $logTxt);

$oNFE->setIdentAmbiente($mT007['T007_Ambiente_NFe']);  //--  1-Produção / 2-Homologação
$oNFE->setUFEmitente($mT007['T007_Empresa_Usuaria_Uf']);
if ($mT007['T007_Versao_NFe']=='1.10') {
	$oNFE->setWebService();
}
$oNFE->setCodigoUFEmitente($mT007['T007_Empresa_Usuaria_Codigo_Estado_IBGE']);
$oNFE->setSenhaCertificado($mT007['C004_Senha_Certificados']);
$oNFE->setModeloCertificado($mConfig['C020_NFe_Tipo_Certificado']);
$oNFE->setNumeroNotaFiscal($mT007['T007_Numero_Nota_Fiscal']);
$oNFE->setSerieNF($mT007['T007_Serie']);
$oNFE->setDataEmissao($mT007['T007_Data_Emissao']);
$oNFE->setCnpjEmitente($mT007['T007_Empresa_Usuaria_Cnpj']);

if ($mConfig['C020_NFe_Treinamento']!='S')
{
  $logTxt = "\tCert Hardness: {$certificadoHardness} \r\n";
  $r = fwrite($h, $logTxt);

  $logTxt = "\tCert Empresa: {$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pem \r\n";
  $r = fwrite($h, $logTxt);

  $logTxt = "\tCert Empresa: {$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pfx \r\n";
  $r = fwrite($h, $logTxt);

  echo "\tCert Hardness: {$certificadoHardness}\n";
  echo "\tCert Empresa: {$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pem\n";
  echo "\tCert Empresa: {$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pfx\n";
  $oNFE->setCertificadoHardness($certificadoHardness);
  $oNFE->setCertificadoPEM("{$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pem");
  $oNFE->carregaCertP12("{$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pfx");
}

if (!file_exists("{$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pfx")) {

  $logTxt = "\nArquivo certificado não existe: {$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pfx \r\n";
  $r = fwrite($h, $logTxt);
  fclose($h);

  msg_erro_nfe(121);
  return false;
}

$Origem="/pre/{$mT007['T007_Numero_Nota_Fiscal']}_{$mT007['T007_Serie']}.xml";
$Destino="$Diretorio_NFe/xml/assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml";

echo "\tXML Origem: {$Origem}\n";
echo "\tXML Destino: {$Destino}\n";

$logTxt = "XML Origem:{$Origem} - XML Destino: {$Destino} \r\n";
$r = fwrite($h, $logTxt);

//-- ADICIONA A ASSINATURA NO ARQUIVO
if (!file_exists("$Diretorio_NFe/xml/assinado/$Destino"))
{
  $logTxt = "Não encontrou arquivo na pasta {$Diretorio_NFe}/xml/assinado/{$Destino} \r\n";
  $r = fwrite($h, $logTxt);
  
    if ($mConfig['C020_NFe_Tipo_Certificado']=='A1')
    {
      $logTxt = "C020_NFe_Tipo_Certificado=A1 - Assinar XML \r\n";
      $r = fwrite($h, $logTxt);

      $logTxt = "Assinando XML \r\n";
      $r = fwrite($h, $logTxt);

       //-- ASSINA O ARQUIVO XML COM CERTIFICADO A1
       $oNFE->assinarXml();
       echo "\tAssinado!\n";
       
       $xml = file_get_contents($Destino);
       msg_erro_nfe(2, "Certificado: {$mConfig['C020_NFe_Tipo_Certificado']}");
       //-- ENVIA A NOTA FISCAL PARA O SEFA
       msg_erro_nfe(5);
       $logTxt = "Recoloca T007 na fila Q001 \r\n";
       $r = fwrite($h, $logTxt);
       fclose($h);

      // NFC-e (mod 65) e tipo Emissao offline (tpEmiss 9)
       if($oNFE->getModeloNF() == '65' && $oNFE->getTipoDocAuxiliar() == '9') {
        
          //Valida XML contra Schema XSD
          list($status, $retorno) = $oNFE->validarXML($xml, dirname(__DIR__, 2) . '/hardness/class/PL/PL_Nfe/nfe_v4.00.xsd');
          
          if($status !== true) {
            //Remove chave de acesso e xml assinado, pois não pode pular para etapa 3 em caso de erro            
            mostrarMsgErro(225, $Destino, 'Erro na validação do xml');
            if (file_exists($Destino)) unlink($Destino);
            
            
            //Erros de validação do xml
            if(is_array($retorno)) {
                $mensagem = array_reduce($retorno, function($acumulado, $item) {
                  return $acumulado . '<br>' .  $item;
                }, '');      
              
                msg_erro_nfe(109, $mensagem, true);
            } else {
                msg_erro_nfe(109, $retorno, true);
            }

            return false;
          }

          //Desabilita flag SCAN para não refazer o xml quando realizar o envio normal
          // Recupera as informações da nota emitida em contingência offline e grava em caso de rejeição
          mysqli_query("update T007 set T007_Flag_SCAN='N' where T007_Id='{$mT007['T007_Id']}'");
          mysqli_query("update T007A 
                          set T007A_Flag_Contingencia_Offline = 'S',
                              T007A_Data_Entrada_Contingencia = '{$mConfig['C020_Data_NFCe_SVC_Ativado']}',
                              T007A_Chave_Acesso_Contingencia = '{$mT007['T007_Chave_Acesso_Nfe']}'
                          where T007A_T007_Id='{$mT007['T007_Id']}'");

          msg_erro_nfe(15);
          return false;
       }
          

	     recolocarNaFila();
       return false;
    }
    else
    {
      $logTxt = "Status T007 - 2 - Certificado: {$mConfig['C020_NFe_Tipo_Certificado']} \r\n";
      $r = fwrite($h, $logTxt);
      fclose($h);

    	msg_erro_nfe(2, "Certificado: {$mConfig['C020_NFe_Tipo_Certificado']}");
      msg_erro_nfe(104);
      return false;
    }
}
else
{
  $logTxt = "XML já estava assinado (mesma chave de acesso) \r\n";
  $r = fwrite($h, $logTxt);

  echo "\tJá estava assinado (mesma chave de acesso)\n";
	$xml = file_get_contents($Destino);
  msg_erro_nfe(2, "Certificado: {$mConfig['C020_NFe_Tipo_Certificado']}");
  
  $logTxt = "Recoloca o T007 na fila Q001 - etapa 2  \r\n";
  $r = fwrite($h, $logTxt);
  fclose($h);

  //-- CASO O ARQUIVO JA EXISTA, ENVIO PARA SEFA
  recolocarNaFila();
  return false;
}
