<?php
namespace hardness;
require_once('scripts/queueMDFE/etapa3-enviarXml-function.php');
/**
* Etapa 3 - Enviar XML
*/

/********************************************
  Raquel Pazini Cardoso 10/12/2018 
  implementação do MDFe
*********************************************/

echo "\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "**\n";
echo "**                     E T A P A  -  3 - SINCRONA\n";
echo "** \n";
echo "**                     Enviar XML \n";
echo "**\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "\n";


$Destino="MDFe".trim($mT276['T276_Chave_Acesso_MDFe']).".xml";
mkdir($Diretorio_MDFe.'/xml/autorizado/', 0777);

//-- ADICIONA A ASSINATURA NO ARQUIVO
if (!file_exists("$Diretorio_MDFe/xml/assinado/$Destino"))
{
  msg_erro_mdfe(113);
  return false;
}


if (empty($mT276['T276_Numero_Recibo_MDFe']))
{
  msg_erro_mdfe(3, "Número MDFe: {$mT276['T276_Numero_MDFe']}");
}
elseif (empty($mT276['T276_Numero_Protocolo_MDFe']))
{
	msg_erro_mdfe(6, "Número MDFe: {$mT276['T276_Numero_MDFe']}");
}
else
{
  recolocarNaFila();
  return false;
}

echo "\n";
echo "**************************************************************************************\n";
echo "* 1 - Seta algumas variaveis \n";
echo "**************************************************************************************\n";
echo "\n";

$oMDFe->setDataEmissao($mT276['T276_Data_Emissao']);
$oMDFe->setIdentAmbiente($mT276['T276_Ambiente_MDFe']);  //--  1-Produ��o / 2-Homologa��o
$oMDFe->setUFEmitente($mT276['T276_Emitente_UF']);
$oMDFe->setCodigoUFEmitente($mT276['T276_Emitente_UF_IBGE']);
$oMDFe->setSenhaCertificado($mT276['C004_Senha_Certificados']);

if ($mConfig['C020_MDFe_Treinamento']!='S')
{
  $oMDFe->setCertificadoHardness($certificadoHardness);
  $oMDFe->setCertificadoPEM("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pem");
  $oMDFe->carregaCertP12("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pfx");
}

//-- Monta o nome do arquivo sem assinatura
$cArquivoAssinado = $oMDFe->getPastaArquivoXML().'assinado/'.$mT276['T276_Numero_MDFe'].'_'.$mT276['T276_Serie'].'.xml';
// se o arquivo sem assinatura existir, entao excluo
if (file_exists($cArquivoAssinado)) unlink($cArquivoAssinado);

if (empty($mT276['T276_Numero_Recibo_MDFe'])){
  echo "\n";
  echo "**************************************************************************************\n";
  echo "* 2 - Tem Recibo e verifica o Status do servico \n";
  echo "**************************************************************************************\n";
  echo "\n";
  if ($oMDFe->statusServico()===true){
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 3 - Servico ok \n";
    echo "**************************************************************************************\n";
    echo "\n";
    //-- se o status estiver OK, envia a nota...
    $cArquivoAssinado = $oMDFe->getPastaArquivoXML()."assinado/MDFe{$mT276['T276_Chave_Acesso_MDFe']}.xml";
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 4 - Enviando XML Assinado \n";
    echo "**************************************************************************************\n";
    echo "\n";
    
    if ($oMDFe->enviaMDFe($cArquivoAssinado,1,1)===true){

      echo "\n";
      echo "**************************************************************************************\n";
      echo "* 5 - Enviado no enviaMDFe, retornoEnviaMDFE :  {$oMDFe->retornoEnviaMDFE}! \n";
      echo "**************************************************************************************\n";
      echo "\n";

      $oMDFe->consultaMDFeRecibo($mT276['T276_Chave_Acesso_MDFe'],$mT276['T276_Numero_Recibo_MDFe'],$oMDFe->retornoEnviaMDFE);

      $cNumero_Protocolo = $oMDFe->nProt;
      $Data_Protocolo    = $oMDFe->dhRecbto;
      $Data_Protocolo    = str_replace("T"," ",$Data_Protocolo);

      echo "\n";
      echo "**************************************************************************************\n";
      echo "* cNumero_Protocolo :  {$cNumero_Protocolo}! \n";
      echo "**************************************************************************************\n";
      echo "\n";

      $cClausula  = " update T276 set ";
      $cClausula .= " T276_Numero_Protocolo_MDFe = '$cNumero_Protocolo', ";
      $cClausula .= " T276_Data_Hora_Protocolo_MDFe = '$Data_Protocolo' ";
      $cClausula .= " where T276_Id='$nT276_Id' ";

      mysqli_query($cClausula);
      $cErro = mysql_error();
      if (!empty($cErro)){
        msg_erro_mdfe(115, "Número MDFe: {$mT276['T276_Numero_MDFe']} - Numero do recibo: {$cNumero_Recibo}");
        return false;
      } else {
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* 5 - Todo OK - Gravando log T285 \n";
        echo "**************************************************************************************\n";
        echo "\n";
        // SE DEU TUDO CERTO GRAVA NO LOG TAMBEM
        $Codigo_Retorno=$oMDFe->cStat;
        $cMotivo=$oMDFe->getMotivo();
        $XML_Retorno=$oMDFe->RetornoXMLSefaz;
        $XML_Retorno=addslashes($XML_Retorno);

        mysqli_query("insert into T285 (T285_Chave_Acesso_MDFe,
                                       T285_Codigo_Retorno,
                                       T285_Numero_Recibo_MDFe,
                                       T285_Data_Transmicao,
                                       T285_Retorno_Sefaz,
                                       T285_Xml_Retorno,
                                       T285_Modelo,
                                       T285_T276_Id
                                       ) values (
                                       '{$mT276['T276_Chave_Acesso_MDFe']}',
                                       '$Codigo_Retorno',
                                       '{$mT276['T276_Numero_Recibo_MDFe']}',
                                       now(),
                                       '$cMotivo',
                                       '$XML_Retorno',
                                       '58',
                                       '$nT276_Id')");
        // Ok!
        unlink($cArquivoAssinado);
        msg_erro_mdfe(9);
        return false;
      }
    } else {
      echo "\n";
      echo "**************************************************************************************\n";
      echo "* 5 - ERRO NO ENVIO DO MDF-E !!!! \n";
      echo "**************************************************************************************\n";
      echo "\n";
      $cArquivo_Assinado=$oMDFe->getPastaArquivoXML()."assinado/MDFe".$mT276['T276_Chave_Acesso_MDFe'].'.xml';
      $Codigo_Retorno=$oMDFe->cStat;
      echo "Codigo Retorno : {$Codigo_Retorno}\n";
      $cMotivo=$oMDFe->getMotivo();
      $XML_Retorno=$oMDFe->RetornoXMLSefaz;
      $XML_Retorno=addslashes($XML_Retorno);

      mysqli_query("insert into T285 (T285_Chave_Acesso_MDFe,
                                     T285_Codigo_Retorno,
                                     T285_Numero_Recibo_MDFe,
                                     T285_Data_Transmicao,
                                     T285_Retorno_Sefaz,
                                     T285_Xml_Retorno,
                                     T285_Modelo,
                                     T285_T276_Id
                                     ) values (
                                     '{$mT276['T276_Chave_Acesso_MDFe']}',
                                     '{$Codigo_Retorno}',
                                     '{$mT276['T276_Numero_Recibo_MDFe']}',
                                     now(),
                                     '{$cMotivo}',
                                     '{$XML_Retorno}',
                                     '58',
                                     '$nT276_Id')");

      mostrarMsgErro($oMDFe->cStat,$cArquivo_Assinado,$oMDFe->getMotivo());
    }
  } else {
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 3 - STATUS SERVICO RETORNOU ERRO OU SERVICO FORA !!!! \n";
    echo "**************************************************************************************\n";
    echo "\n";
    $cMotivo=$oMDFe->getMotivo();
    $cErro=$oMDFe->getErro();

    $Codigo_Retorno=$oMDFe->cStat;
    $XML_Retorno=$oMDFe->RetornoXMLSefaz;
    $XML_Retorno=addslashes($XML_Retorno);

    if (empty($cMotivo)) {
      $cMotivo = 'Serviço com status indisponível';
    }

    mysqli_query("insert into T285 (T285_Chave_Acesso_MDFe,
                                   T285_Codigo_Retorno,
                                   T285_Numero_Recibo_MDFe,
                                   T285_Data_Transmicao,
                                   T285_Retorno_Sefaz,
                                   T285_Xml_Retorno,
                                   T285_Modelo,
                                   T285_T276_Id
                                   ) values (
                                   '{$mT276['T276_Chave_Acesso_MDFe']}',
                                   '{$Codigo_Retorno}',
                                   '{$mT276['T276_Numero_Recibo_MDFe']}',
                                   now(),
                                   '{$cMotivo}',
                                   '{$XML_Retorno}',
                                   '58',
                                   '$nT276_Id')");

		mostrarMsgErro($oMDFe->cStat,$oMDFe->getArquivoAssinado(),$cMotivo);
  }
}