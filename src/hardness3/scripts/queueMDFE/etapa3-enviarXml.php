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
echo "**                     E T A P A  -  3\n";
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
    
    if ($oMDFe->enviaMDFe($cArquivoAssinado,1)===true){
      echo "\n";
      echo "**************************************************************************************\n";
      echo "* 5 - Enviado! \n";
      echo "**************************************************************************************\n";
      echo "\n";
      //-- SE A TRANSMISSAO DA NOTA RETORNOU UM NUMERO DE RECIBO,
      //-- ENTAO GRAVO O MESMO NA NOTA
      //-- GRAVAR O RECIBO E A AUTORIZACAO
      $Data_Recebimento = $oMDFe->dhRecbto;
      $cNumero_Recibo = $oMDFe->nRec;
      $Data_Recebimento = str_replace("T"," ",$Data_Recebimento);

      $cClausula  = " update T276 set ";
      $cClausula .= " T276_Numero_Recibo_MDFe = '$cNumero_Recibo', ";
      $cClausula .= " T276_Data_Hora_Recebimento_MDFe = '$Data_Recebimento' ";
      $cClausula .= " where T276_Id='$nT276_Id' ";

      mysqli_query($cClausula);
      $cErro = mysql_error();
      if (!empty($cErro)){
        msg_erro_mdfe(114, "Número MDFe: {$mT276['T276_Numero_MDFe']} - Número do recibo: {$cNumero_Recibo}");
        return false;
      } else {
        //-- RELOAD NA TELA PARA OBTER A RESPOSTA DO SEFAZ
        //msg_erro_mdfe(8); */
        recolocarNaFila($cNumero_Recibo);
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
      echo "{$Codigo_Retorno}\n";
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
} elseif (empty($mT276['T276_Numero_Protocolo_MDFe'])){
  echo "\n";
  echo "**************************************************************************************\n";
  echo "* 7 - Consulta o protocolo na sefaz, verifica status servico \n";
  echo "**************************************************************************************\n";
  echo "\n";
  if ($oMDFe->statusServico()===true){
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 8 - Status servico ok, inicia consulta \n";
    echo "**************************************************************************************\n";
    echo "\n";
    if ($oMDFe->consultaMDFe($mT276['T276_Chave_Acesso_MDFe'],$mT276['T276_Numero_Recibo_MDFe'])!==false){
      echo "\n";
      echo "**************************************************************************************\n";
      echo "* 9 - Consultou MDFe \n";
      echo "**************************************************************************************\n";
      echo "\n";
      $cNumero_Protocolo = $oMDFe->nProt;
      $Data_Protocolo = $oMDFe->dhRecbto;
      $Data_Protocolo = str_replace("T"," ",$Data_Protocolo);

      $cClausula  = " update T276 set ";
      $cClausula .= " T276_Numero_Protocolo_MDFe = '$cNumero_Protocolo', ";
      $cClausula .= " T276_Data_Hora_Protocolo_MDFe = '$Data_Protocolo' ";
      $cClausula .= " where T276_Id='$nT276_Id' ";
      mysqli_query($cClausula);
      $cErro = mysql_error();
      if (!empty($cErro)){
        msg_erro_mdfe(115, "Número MDFe: {$mT276['T276_Numero_MDFe']} - N�mero do recibo: {$cNumero_Recibo}");
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
        msg_erro_mdfe(9);
        return false;
      }
    } else {
      echo "\n";
      echo "**************************************************************************************\n";
      echo "* 9 - Consultou MDFe e nao retornou protocolo\n";
      echo "**************************************************************************************\n";
      echo "\n";
      //$cArquivo_Assinado=$oMDFe->getArquivoAssinado();
      $cMotivo=$oMDFe->getMotivo();
      //print_r($oMDFe->getErro());
      $Codigo_Retorno=$oMDFe->cStat;
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
      mostrarMsgErro($oMDFe->cStat,$oMDFe->getPastaArquivoXML()."assinado/MDFe{$mT276['T276_Chave_Acesso_MDFe']}.xml",$oMDFe->getMotivo());
    }
  } else {    
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 9 - Status do servico nao esta ok \n";
    echo "**************************************************************************************\n";
    echo "\n";
    $cMotivo=$oMDFe->getMotivo();
    $cErro=$oMDFe->getErro();
    //print_r($cMotivo);

    $Codigo_Retorno=$oMDFe->cStat;
    $XML_Retorno=$oMDFe->RetornoXMLSefaz;
    $XML_Retorno=addslashes($XML_Retorno);

    mysqli_query("insert into T285 (T285_D024_Id,
                                   T285_Chave_Acesso_MDFe,
                                   T285_Codigo_Retorno,
                                   T285_Numero_Recibo_MDFe,
                                   T285_Data_Transmicao,
                                   T285_Retorno_Sefaz,
                                   T285_Xml_Retorno,
                                   T285_Modelo,
                                   T285_T276_Id
                                   ) values (
                                   '{$mT276['T276_D024_Id']}',
                                   '{$mT276['T276_Chave_Acesso_MDFe']}',
                                   '$Codigo_Retorno',
                                   '{$mT276['T276_Numero_Recibo_MDFe']}',
                                   now(),
                                   '$cMotivo',
                                   '$XML_Retorno',
                                   '58',
                                   '$nT276_Id')");
    if(!empty($cErro)){
    	msg_erro_mdfe(115, "Número MDFe: {$mT276['T276_Numero_MDFe']} - Número do recibo: {$cNumero_Recibo} - Erro: {$cMsg_Erro} - Suporte: {$oMDFe->debug_str}");
      return false;
    }
    else {        
		  mostrarMsgErro($oMDFe->cStat,$oMDFe->getPastaArquivoXML()."assinado/MDFE{$mT276['T276_Chave_Acesso_MDFe']}.xml",$oMDFe->getMotivo());
    }
  }
}