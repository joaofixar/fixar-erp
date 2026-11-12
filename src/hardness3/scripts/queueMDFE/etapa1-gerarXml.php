<?php
namespace hardness;

/**
* Etapa 1 - Geração do XML da NF
*/

/********************************************
*       Raquel Pazini Cardoso 26/11/2018    *
*           implementação do MDFe           *
********************************************/

msg_erro_mdfe(1);

echo "\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "**\n";
echo "**                     E T A P A  -  1\n";
echo "** \n";
echo "**                     Gerar XML \n";
echo "**\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "\n";

$oMDFe->setIdentAmbiente($mT276['T276_Ambiente_MDFe']);  //--  1-Produção / 2-Homologação
$oMDFe->setUFEmitente($mT276['T276_Emitente_UF']);
$oMDFe->setModuloMDFe($_SESSION['Modulo_NFe']);
$oMDFe->setCodigoUFEmitente($mT276['T276_Emitente_UF_IBGE']);
$oMDFe->setSenhaCertificado($mT276['C004_Senha_Certificados']);
$oMDFe->setModeloCertificado($mConfig['C020_NFe_Tipo_Certificado']);
mkdir($Diretorio_MDFe.'/xml/assinado/', 0777);

if ($mConfig['C020_NFe_Treinamento']!='S'){
  $oMDFe->setCertificadoHardness($certificadoHardness);
  $oMDFe->setCertificadoPEM("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pem");
  $oMDFe->carregaCertP12("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pfx");
}

//-- SE NAO EXISTE RECIBO É‰ POR QUE A NOTA NAO FOI ENVIADA AINDA
if (empty($mT276['T276_Numero_Recibo_MDFe']) && empty($mT276['T276_Chave_Acesso_MDFe'])){
  //-- SO CRIA O XML SE NAO EXISTIR CHAVE DE ACESSO
  $oMDFe->criarArquivoXML();
  $cChave_Acesso = $oMDFe->getChaveAcessoMDFe();
  $cChave_Acesso = str_replace("MDFe","",$cChave_Acesso);

  if ($oMDFe->errorStatus===true){
    $cMsg_Erro = $oMDFe->getErro();
    msg_erro_mdfe(109, $cMsg_Erro);
    return false;
  }
  
  $cClausula  = " update T276 set ";
  $cClausula .= " T276_Chave_Acesso_MDFe = '".str_replace("MDFe","",$cChave_Acesso)."' ";
  $cClausula .= " where T276_Id='".$nT276_Id."' ";
  
  mysqli_query($cClausula);
  
  $cErro = mysql_error();
  if (!empty($cErro)){
    msg_erro_mdfe(110);
    return false;
  }

  $T276=mysqli_query("select T276_C004_Id,
                            T276_Chave_Acesso_MDFe,
                            T276_Numero_MDFe,
                            T284_Id
                       from T276 
                  left join T284 on T284_T276_Id=T276_Id 
                      where T276_Id='$nT276_Id'");
  $mT276=mysql_fetch_array($T276);           
  
  $cErro = mysql_error();
  if (!empty($cErro)){
    msg_erro_mdfe(100);
    return false;
  }

  mysqli_query("insert into T285 (T285_Chave_Acesso_MDFe,
                                 T285_Data_Transmicao,
                                 T285_Retorno_Sefaz,
                                 T285_Xml_Retorno,
                                 T285_Modelo,
                                 T285_T276_Id
                                 ) values (
                                 '{$mT276['T276_Chave_Acesso_MDFe']}',
                                 now(),
                                 'Gerou Chave Acesso',
                                 '{$XML_Retorno}',
                                 '58',
                                 '$nT276_Id')");
  $cErro = mysql_error();
  if (!empty($cErro)){
    msg_erro_mdfe(111);
    return false;
  }
}

msg_erro_mdfe(4, 'XML gerado');
recolocarNaFila();
