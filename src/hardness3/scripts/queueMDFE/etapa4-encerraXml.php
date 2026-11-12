<?php
namespace hardness;
global $g;

echo "\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "**\n";
echo "**                     E T A P A  -  4\n";
echo "** \n";
echo "**                     Encerrar XML \n";
echo "**\n";
echo "**************************************************************************************\n";
echo "**************************************************************************************\n";
echo "\n";
		
echo "\n";
echo "**************************************************************************************\n";
echo "* 1 - Seta algumas variaveis \n";
echo "**************************************************************************************\n";
echo "\n";
$descarregamento = mysqli_query(" SELECT D018_UF, 
                                        D020_Nome_Cidade 
                                   FROM T276 
                              LEFT JOIN D018 ON D018_Id=T276_D018_Id_Descarregamento 
                              LEFT JOIN D020 ON D020_Id=T276_D020_Id_Descarregamento
                              WHERE T276_Id='{$nT276_Id}'");
$mDescarregamento = mysql_fetch_assoc($descarregamento);

$oMDFe->setDataEncerramento(date("Y-m-d"));
$oMDFe->setUFEmitente($mT276['T276_Empresa_Usuaria_Uf']);
$oMDFe->setCodigoUFEmitente($mT276['T276_Emitente_UF_IBGE']);
$oMDFe->setSenhaCertificado($mT276['C004_Senha_Certificados']);
$oMDFe->setIdentAmbiente($mT276['T276_Ambiente_MDFe']);  //--  1-Produção / 2-Homologação
$oMDFe->setUFDescarregamento($mDescarregamento['D018_UF']);
$oMDFe->setCidadeDescarregamento($mDescarregamento['D020_Nome_Cidade']);
$oMDFe->setCnpjEmitente($mT276['T276_Emitente_CNPJ']);
mkdir($Diretorio_MDFe.'/xml/encerrado/', 0777);

if ($mConfig['C020_MDFe_Treinamento']!='S')
{
  $oMDFe->setCertificadoHardness($certificadoHardness);
  $oMDFe->setCertificadoPEM("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pem");
  $oMDFe->carregaCertP12("{$mT276['C004_Localizacao_Certificados']}{$mT276['T276_C004_Id']}.pfx");
}

if (empty($mT276['T276_Numero_Protocolo_Encerramento_MDFe'])){
	  msg_erro_mdfe(14, "Número MDFe: {$mT276['T276_Numero_MDFe']}");
	  echo "\n";
  	echo "**************************************************************************************\n";
  	echo "* 10 - Consulta o Protocoloco de Encerramento na Sefaz \n";
  	echo "**************************************************************************************\n";
  	echo "\n";

  	if ($oMDFe->recepcaoEvento($mT276['T276_Chave_Acesso_MDFe'],$mT276['T276_Numero_Protocolo_MDFe'], 'Encerramento') === true){
		  echo "\n";
	  	echo "**************************************************************************************\n";
	  	echo "* 11 - Encerrado \n";
	  	echo "**************************************************************************************\n";
	  	echo "\n";  

	  	$cNumero_Protocolo = $oMDFe->nProt;
      $Data_Recebimento  = $oMDFe->dhRecbto;
      $Data_Recebimento  = str_replace("T", " ", $Data_Recebimento);
      $Data_Recebimento  = substr($Data_Recebimento, 0, 19);

      mysqli_query("SET AUTOCOMMIT=1");
      $cClausula  = " UPDATE T276 SET ";
      $cClausula .= " T276_Numero_Protocolo_Encerramento_MDFe = '$cNumero_Protocolo', ";
      $cClausula .= " T276_Data_Hora_Encerramento_MDFe = '$Data_Recebimento', ";
      $cClausula .= " T276_Flag_Encerrada = 'S' ";
      $cClausula .= " WHERE T276_Id='$nT276_Id' ";

      mysqli_query($cClausula);
      $cErro = mysql_error();
      if (!empty($cErro)) {
        mysqli_query("ROLLBACK");
        msg_erro_mdfe(114, "Número MDFe: {$mT276['T276_Numero_MDFe']} - Protocolo Encerramento: {$cNumero_Protocolo}");
        return "Ocorreu um erro ao gravar o retorno do Sefaz no MDFe\nPor favor entre em contato com o suporte \nErro:$cErro\nClausula:$cClausula";
      }else{
        recolocarNaFila($mT276['T276_Numero_Recibo_MDFe']);
        return false;
      }
  	}else{
  		echo "\n";
	  	echo "**************************************************************************************\n";
	  	echo "* 11 - Não foi possível encerrar o MDF-e \n";
	  	echo "**************************************************************************************\n";
	  	echo "\n";  	  
  		$Codigo_Retorno=$oMDFe->cStat;
      $cMotivo = $oMDFe->getMotivo();
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
      msg_erro_mdfe(202, $cMotivo, true, $cMotivo);
  	}
}else if ($oMDFe->consultaMDFe($mT276['T276_Chave_Acesso_MDFe'],$mT276['T276_Numero_Recibo_MDFe'], 'encerrado')!==false){

    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 11 - Consultou MDFe \n";
    echo "**************************************************************************************\n";
    echo "\n";
    $cNumero_Protocolo = $oMDFe->nProt;
    $Data_Protocolo = $oMDFe->dhRecbto;
    $Data_Protocolo = str_replace("T"," ",$Data_Protocolo);

    
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 12 - Todo OK - Gravando log T285 \n";
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
                                   '{$modelo}',
                                   '$nT276_Id')");
    // Ok!
    msg_erro_mdfe(15);
    return false;
  } else {
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 11 - Consultou MDFe e nao retornou protocolo\n";
    echo "**************************************************************************************\n";
    echo "\n";
    $cMotivo=$oMDFe->getMotivo();
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
  }

