<?php
namespace hardness;
/**
* Etapa 2 - Assinatura do XML
*/

/********************************************
  Raquel Pazini Cardoso 07/12/2018 
  implementa��o do MDFe
*********************************************/

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


$oMDFe->setIdentAmbiente($mT276['T276_Ambiente_MDFe']);  //--  1-Produção / 2-Homologação
$oMDFe->setUFEmitente($mT276['T276_Empresa_Usuaria_Uf']);
$oMDFe->setCodigoUFEmitente($mT276['T276_Emitente_UF_IBGE']);
$oMDFe->setSenhaCertificado($mT276['C004_Senha_Certificados']);
// $oMDFe->setModeloCertificado($mConfig['C020_NFe_Tipo_Certificado']);
$oMDFe->setNumeroMDFe($mT276['T276_Numero_MDFe']);
$oMDFe->setSerieMDFe($mT276['T276_Serie']);
$oMDFe->setDataEmissao($mT276['T276_Data_Emissao']);
$oMDFe->setCnpjEmitente($mT276['T276_Emitente_CNPJ']);

if ($mConfig['C020_NFe_Treinamento']!='S')
{
  echo "\tCert Hardness: {$certificadoHardness}\n";
  echo "\tCert Empresa: {$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pem\n";
  echo "\tCert Empresa: {$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pfx\n";
  $oMDFe->setCertificadoHardness($certificadoHardness);
  $oMDFe->setCertificadoPEM("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pem");
  $oMDFe->carregaCertP12("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pfx");  
}

if (!file_exists("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pfx")) {
  msg_erro_mdfe(121);
  return false;
}

$Origem="/pre/{$mT276['T276_Numero_MDFe']}_{$mT276['T276_Serie']}.xml";
$Destino="$Diretorio_mdfe/xml/assinado/mdfe{$mT276['T276_Chave_Acesso_MDFe']}.xml";

echo "\tXML Origem: {$Origem}\n";
echo "\tXML Destino: {$Destino}\n";

//-- ADICIONA A ASSINATURA NO ARQUIVO
if (!file_exists("$Diretorio_mdfe/xml/assinado/$Destino"))
{
  /*if ($mConfig['C020_mdfe_Tipo_Certificado']=='A1')
  {*/
  //-- ASSINA O ARQUIVO XML COM CERTIFICADO A1
  $oMDFe->assinarXml();
  echo "\tAssinado!\n";
 
  $xml = file_get_contents($Destino);
  msg_erro_mdfe(2, "Certificado: {$mConfig['C020_NFe_Tipo_Certificado']}");
  //-- ENVIA A NOTA FISCAL PARA O SEFA
  msg_erro_mdfe(5, "XML Assinado!");
  recolocarNaFila();
  return false;
}
else
{
  echo "\tJá estava assinado (mesma chave de acesso)\n";
	$xml = file_get_contents($Destino);
	msg_erro_mdfe(2, "Certificado: {$mConfig['C020_NFe_Tipo_Certificado']}");
	   
  //-- CASO O ARQUIVO JA EXISTA, ENVIO PARA SEFA
  recolocarNaFila();
  return false;
}
