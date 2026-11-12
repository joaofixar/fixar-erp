<?php
namespace hardness;
require_once('scripts/queueNF/etapa3-enviarXml-function.php');
/**
* Etapa 3 - Enviar XML
*/
/*$cIP_Servidor="201.22.57.137";
if ($_SERVER[SERVER_NAME]==$cIP_Servidor)
{
   $cC025=mysqli_query("select C025_D024_Id from e001.C025 left join e001.D024 on D024_Id=C025_D024_Id where D024_Cnpj='$mT007[C004_Cnpj]'");
   $mC025=mysql_fetch_array($cC025);
}*/
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

$logTxt = "\r\n Etapa 3 - Enviar XML \r\n";
$r = fwrite($h, $logTxt);

$Destino="NFe".trim($mT007['T007_Chave_Acesso_Nfe']).".xml";

//-- ADICIONA A ASSINATURA NO ARQUIVO
if (!file_exists("$Diretorio_NFe/xml/assinado/$Destino"))
{
    $logTxt =" Não encontrou o arquivo na pasta - {$Diretorio_NFe}/xml/assinado/{$Destino}\n";
    $r = fwrite($h, $logTxt);
    fclose($h);
    
    msg_erro_nfe(113);
    return false;
}

$logTxt ="Número do recibo: {$mT007['T007_Numero_Recibo_Nfe']}.\n";
$r = fwrite($h, $logTxt);

$logTxt ="Número do protocolo: {$mT007['T007_Numero_Protocolo_Nfe']}.\n";
$r = fwrite($h, $logTxt);

if (empty($mT007['T007_Numero_Recibo_Nfe']))
{
	msg_erro_nfe(3, "Número NF: {$mT007['T007_Numero_Nota_Fiscal']}");
}
elseif (empty($mT007['T007_Numero_Protocolo_Nfe']))
{
	msg_erro_nfe(6, "Número NF: {$mT007['T007_Numero_Nota_Fiscal']}");
}
else
{
    $logTxt ="Recolocar a T007 na fila Q001 - T007_Numero_Protocolo_Nfe e T007_Numero_Recibo_Nfe não estão vazios.\n";
    $r = fwrite($h, $logTxt);
    fclose($h);

   //msg_atualizar_status("Gerando Danfe {$mT007['T007_Numero_Nota_Fiscal']} ...");
   recolocarNaFila();
   return false;
}

echo "\n";
echo "**************************************************************************************\n";
echo "* 1 - Seta algumas variaveis \n";
echo "**************************************************************************************\n";
echo "\n";

$logTxt = "1 - Seta algumas variaveis para envio da nota para sefaz.\r\n";
$r = fwrite($h, $logTxt);

$oNFE->setDataEmissao($mT007['T007_Data_Emissao']);
$oNFE->setIdentAmbiente($mT007['T007_Ambiente_NFe']);  //--  1-Produção / 2-Homologação
$oNFE->setUFEmitente($mT007['T007_Empresa_Usuaria_Uf']);
if ($mT007[T007_Versao_NFe]=='1.10') {
	$oNFE->setWebService();
}
$oNFE->setCodigoUFEmitente($mT007['T007_Empresa_Usuaria_Codigo_Estado_IBGE']);
$oNFE->setSenhaCertificado($mT007['C004_Senha_Certificados']);

if ($mConfig['C020_NFe_Treinamento']!='S')
{
   $oNFE->setCertificadoHardness($certificadoHardness);
   $oNFE->setCertificadoPEM("{$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pem");
   $oNFE->carregaCertP12("{$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pfx");
}

//-- Monta o nome do arquivo sem assinatura
$cArquivoAssinado = $oNFE->getPastaArquivoXML().'assinado/'.$mT007['T007_Numero_Nota_Fiscal'].'_'.$mT007['T007_Serie'].'.xml';
// se o arquivo sem assinatura existir, entao excluo
if (file_exists($cArquivoAssinado)) unlink($cArquivoAssinado);

if (empty($mT007['T007_Numero_Recibo_Nfe']))
{
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 2 - Tem Recibo e verifica o Status do servico \n";
    echo "**************************************************************************************\n";
    echo "\n";

    $logTxt = "2 - Tem Recibo e verifica o Status do serviço.\r\n";
    $r = fwrite($h, $logTxt);

    if ($oNFE->statusServico()===true)
    {
      echo "\n";
      echo "**************************************************************************************\n";
      echo "* 3 - Servico ok \n";
      echo "**************************************************************************************\n";
      echo "\n";
      $logTxt = "3 - Servico ok.\r\n";
      $r = fwrite($h, $logTxt);

       //-- se o status estiver OK, envia a nota...
       $cArquivoAssinado = $oNFE->getPastaArquivoXML()."assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml";
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* 4 - Enviando XML Assinado \n";
        echo "**************************************************************************************\n";
        echo "\n";

        $logTxt = "4 - Enviando XML Assinado - {$cArquivoAssinado}.\r\n";
        $r = fwrite($h, $logTxt);

       //echo "\t*********** Enviando XML assinado:  {$cArquivoAssinado}\n";
       if ($oNFE->enviaNF($cArquivoAssinado,1)===true)
       {
          echo "\n";
          echo "**************************************************************************************\n";
          echo "* 5 - Enviado! \n";
          echo "**************************************************************************************\n";
          echo "\n";

          $logTxt = "5 - Nota enviada com sucesso.\r\n";
          $r = fwrite($h, $logTxt);

           //-- SE A TRANSMISSAO DA NOTA RETORNOU UM NUMERO DE RECIBO,
           //-- ENTAO GRAVO O MESMO NA NOTA

           //-- GRAVAR O RECIBO E A AUTORIZACAO
           $Data_Recebimento = $oNFE->dhRecbto;
           $cNumero_Recibo = $oNFE->nRec;

           $Data_Recebimento = str_replace("T"," ",$Data_Recebimento);

           $cClausula  = " update T007 set ";
           $cClausula .= " T007_Numero_Recibo_Nfe = '$cNumero_Recibo', ";
           $cClausula .= " T007_Data_Hora_Recebimento_Nfe = '$Data_Recebimento' ";
           $cClausula .= " where T007_Id='$nT007_Id' ";

           mysqli_query($cClausula);
           $cErro = mysql_error();
           if (!empty($cErro))
           {
                $logTxt = "Erro 114 mysql update T007 - {$cClausula} - Erro: {$cErro}\r\n";
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(114, "Número NF: {$mT007['T007_Numero_Nota_Fiscal']} - Número do recibo: {$cNumero_Recibo}");
                return false;
           }
           else
           {
              /*echo "\n";
              echo "**************************************************************************************\n";
              echo "* 6 - Aguardando 12 segundos para pesquisar protocolo \n";
              echo "**************************************************************************************\n";
              echo "\n";
              //-- PAUSA PARA ESPERAR O PROCESSAMENTO DA NOTA NO SEFAZ
              sleep(12);

              //-- RELOAD NA TELA PARA OBTER A RESPOSTA DO SEFAZ
              //msg_erro_nfe(8); */

              $logTxt = "Recolocando na fila passando o numero de recibo - {$cNumero_Recibo} \r\n";
              $r = fwrite($h, $logTxt);
              fclose($h);

              recolocarNaFila($cNumero_Recibo);
              return false;
           }
        }
        else
        {
            echo "\n";
            echo "**************************************************************************************\n";
            echo "* 5 - ERRO NO ENVIO DA NF !!!! \n";
            echo "**************************************************************************************\n";
            echo "\n";
            $cArquivo_Assinado=$oNFE->getPastaArquivoXML()."assinado/NFe".$mT007['T007_Chave_Acesso_Nfe'].'.xml';
            //$cMsg_Erro ="Transmissção da NF-e $mT007[T007_Numero_Nota_Fiscal] NÃO foi concluída.<BR><BR>Arquivo: <a href='".$cArquivo_Assinado."'><font color=yellow>".basename($cArquivo_Assinado)."</a></font><BR>Retorno Sefaz: ".$oNFE->getMotivo();
            $Codigo_Retorno=$oNFE->cStat;
            echo "{$Codigo_Retorno}\n";

            $logTxt = "5 - ERRO NO ENVIO DA NF - código de retorno: {$Codigo_Retorno}\r\n";
            $r = fwrite($h, $logTxt);

            $XML_Retorno=$oNFE->RetornoXMLSefaz;
            $XML_Retorno=addslashes($XML_Retorno);
            print_r($XML_Retorno);

            $modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';
            mysqli_query("insert into T127 (T127_C004_Id,
                                           T127_D024_Id,
                                           T127_Chave_Acesso_Nfe,
                                           T127_Numero_Nota_Fiscal,
                                           T127_Codigo_Retorno,
                                           T127_Numero_Recibo_Nfe,
                                           T127_Data_Transmicao,
                                           T127_Retorno_Sefaz,
                                           T127_Xml_Retorno,
                                           T127_Modelo,
                                           T127_T007_Id
                                           ) values (
                                           '{$mT007['T007_C004_Id']}',
                                           '{$mT007['T007_D024_Id']}',
                                           '{$mT007['T007_Chave_Acesso_Nfe']}',
                                           '{$mT007['T007_Numero_Nota_Fiscal']}',
                                           '{$Codigo_Retorno}',
                                           '{$mT007['T007_Numero_Recibo_Nfe']}',
                                           now(),
                                           '{$cMotivo}',
                                           '{$XML_Retorno}',
                                           '{$modelo}',
                                           '{$nT007_Id}')");

			      mostrarMsgErro($oNFE->cStat,$cArquivo_Assinado,$oNFE->getMotivo());
        }
    }
    else
    {
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* 3 - STATUS SERVICO RETORNOU ERRO OU SERVICO FORA !!!! \n";
        echo "**************************************************************************************\n";
        echo "\n";

        $logTxt = "3 - STATUS SERVICO RETORNOU ERRO OU SERVICO FORA !!!!\r\n";
        $r = fwrite($h, $logTxt);

        $cMotivo=$oNFE->getMotivo();
        $cErro=$oNFE->getErro();

        $Codigo_Retorno=$oNFE->cStat;
        $XML_Retorno=$oNFE->RetornoXMLSefaz;
        $XML_Retorno=addslashes($XML_Retorno);

        if (empty($cMotivo)) {
            $cMotivo = 'Serviço com status indisponível';
            $logTxt = "Serviço com status indisponível.\r\n";
            $r = fwrite($h, $logTxt);
        }

        $modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';
        mysqli_query("insert into T127 (T127_C004_Id,
                                       T127_D024_Id,
                                       T127_Chave_Acesso_Nfe,
                                       T127_Numero_Nota_Fiscal,
                                       T127_Codigo_Retorno,
                                       T127_Numero_Recibo_Nfe,
                                       T127_Data_Transmicao,
                                       T127_Retorno_Sefaz,
                                       T127_Xml_Retorno,
                                       T127_Modelo,
                                       T127_T007_Id
                                       ) values (
                                       '{$mT007['T007_C004_Id']}',
                                       '{$mT007['T007_D024_Id']}',
                                       '{$mT007['T007_Chave_Acesso_Nfe']}',
                                       '{$mT007['T007_Numero_Nota_Fiscal']}',
                                       '{$Codigo_Retorno}',
                                       '{$mT007['T007_Numero_Recibo_Nfe']}',
                                       now(),
                                       '{$cMotivo}',
                                       '{$XML_Retorno}',
                                       '{$modelo}',
                                       '{$nT007_Id}')");

    		mostrarMsgErro($oNFE->cStat,$oNFE->getArquivoAssinado(),$cMotivo);
    }
}
elseif (empty($mT007['T007_Numero_Protocolo_Nfe']))
{
    echo "\n";
    echo "**************************************************************************************\n";
    echo "* 7 - Consulta o protocolo na sefaz, verifica status servico \n";
    echo "**************************************************************************************\n";
    echo "\n";

    $logTxt = "7 - Consulta o protocolo na sefaz, verifica status servico.\r\n";
    $r = fwrite($h, $logTxt);

    if ($oNFE->statusServico()===true)
    {
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* 8 - Status servico ok, inicia consulta \n";
        echo "**************************************************************************************\n";
        echo "\n";
        $logTxt = "8 - Status servico ok, iniciando consulta.\r\n";
        $r = fwrite($h, $logTxt);
        if ($oNFE->consultaNF($mT007['T007_Chave_Acesso_Nfe'],$mT007['T007_Numero_Recibo_Nfe'])!==false)
        {
            echo "\n";
            echo "**************************************************************************************\n";
            echo "* 9 - Consultou NF \n";
            echo "**************************************************************************************\n";
            echo "\n";

            $logTxt = "9 - Consultou NF na sefaz.\r\n";
            $r = fwrite($h, $logTxt);

            $cNumero_Protocolo = $oNFE->nProt;
            $Data_Protocolo = $oNFE->dhRecbto;
            $Data_Protocolo = str_replace("T"," ",$Data_Protocolo);

            $logTxt = "Numero do protocolo recebido: {$cNumero_Protocolo } - Data protocolo: {$Data_Protocolo}\r\n";
            $r = fwrite($h, $logTxt);

            $cClausula  = " update T007 set ";
            if($g['C031']['removerNFCeDaFilaEnvioAposTransmitir'] == 'S') {
                $cClausula .= " T007_TX_Flag_Finalizada = if(T007_Flag_ACP=8 and T007_Flag_PDV='S', 1, 0), ";
            }
            $cClausula .= " T007_Numero_Protocolo_Nfe = '$cNumero_Protocolo', ";
            $cClausula .= " T007_Data_Hora_Protocolo_Nfe = '$Data_Protocolo' ";
            $cClausula .= " where T007_Id='$nT007_Id' ";
            mysqli_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro))
            {   
                $logTxt = "Erro 115 update t007 - {$cClausula} - {$cErro} \r\n";
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(115, "Número NF: {$mT007['T007_Numero_Nota_Fiscal']} - Número do recibo: {$cNumero_Recibo}");
                return false;
            } else {
                echo "\n";
                echo "**************************************************************************************\n";
                echo "* 5 - Todo OK - Gravando log T127 \n";
                echo "**************************************************************************************\n";
                echo "\n";

                //-- Tudo certo, apaga arquivo da pasta assinado
                $xmlAssinado = $oNFE->getPastaArquivoXML()."assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml";
                // se o arquivo sem assinatura existir, entao excluo
                if (file_exists($xmlAssinado)){
                    unlink($xmlAssinado);
                } 

                $logTxt = "5 - Consulta da nota na sefaz realizada com sucesso - Gravando log T127.\r\n";
                $r = fwrite($h, $logTxt);
                
                // SE DEU TUDO CERTO GRAVA NO LOG TAMBEM
                $Codigo_Retorno=$oNFE->cStat;
                $cMotivo=$oNFE->getMotivo();
                $XML_Retorno=$oNFE->RetornoXMLSefaz;
                $XML_Retorno=addslashes($XML_Retorno);

                $logTxt = "Consulta da nota na sefaz status: {$Codigo_Retorno} - Motivo: {$cMotivo}\r\n";
                $r = fwrite($h, $logTxt);

                $modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';
                mysqli_query("insert into T127 (T127_C004_Id,
                                               T127_D024_Id,
                                               T127_Chave_Acesso_Nfe,
                                               T127_Numero_Nota_Fiscal,
                                               T127_Codigo_Retorno,
                                               T127_Numero_Recibo_Nfe,
                                               T127_Data_Transmicao,
                                               T127_Retorno_Sefaz,
                                               T127_Xml_Retorno,
                                               T127_Modelo,
                                               T127_T007_Id
                                               ) values (
                                               '{$mT007['T007_C004_Id']}',
                                               '{$mT007['T007_D024_Id']}',
                                               '{$mT007['T007_Chave_Acesso_Nfe']}',
                                               '{$mT007['T007_Numero_Nota_Fiscal']}',
                                               '$Codigo_Retorno',
                                               '{$mT007['T007_Numero_Recibo_Nfe']}',
                                               now(),
                                               '$cMotivo',
                                               '$XML_Retorno',
                                               '{$modelo}',
                                               '{$nT007_Id}')");
        
  			    $VEN012 = new \hardness\VEN012();
                                                
                $logTxt = "Chamando VEN012->Gatilho apos transmitir.\r\n";
                $r = fwrite($h, $logTxt);
                
                // Gatilho para executar processos após transmitir a NF
                $retorno = $VEN012->gatilhoAposTransmitirNF($nT007_Id);
                
/*                 // Ok!
                msg_erro_nfe(9);
                unlink("/tmp/QueueNF-{$Q001['Q001_Database']}-{$Q001['Q001_C004_Id']}-{$mT007['T007_Numero_Nota_Fiscal']}.txt"); */

                // Denegar
                if($Codigo_Retorno == '301' || $Codigo_Retorno == '302' || $Codigo_Retorno == '303' || $Codigo_Retorno == '110' || $Codigo_Retorno == '205'){
                    echo "******************* Denegando nota...\n";
                    
                    $cSql_T007=mysqli_query("select T007_Flag_ACP,	
                                                   T007_Id,	
                                                   T007_D024_Id	 
                                              from T007 
                                          left join C004 on C004_Id=T007_C004_Id 
                                              where T007_Id='{$nT007_Id}'");
                    $mT007=mysql_fetch_assoc($cSql_T007);
                    if (mysql_num_rows($cSql_T007)==1 && $mT007['T007_Flag_ACP'] == '6') {
                        // INICIO DENEGAR
                        mysqli_query("SET AUTOCOMMIT=0");
                        mysqli_query("START TRANSACTION");
                        //-- Cancelando Nota Fiscal
                        mysqli_query("update T007
                                        set T007_Flag_Cancelada='S',
                                            T007_Flag_Classificacao=0,
                                            T007_Observacao_Cancelamento='NOTA FISCAL DENEGADA',
                                            T007_Flag_Denegado='S',
                                            T007_Valor_Total=0
                                      where T007_Id='{$mT007['T007_Id']}'");
                        //-- Limpa o Flag de classificacao do item
                        mysqli_query("update T008 set T008_Flag_Classificacao=0 where T008_T007_Id='{$mT007['T007_Id']}'");
                        //-- Cancelando Contas a Receber 
                        mysqli_query("update T002 set T002_Flag_Cancelada='S',
                                                    T002_Data_Recebimento=current_date(),
                                                     T002_Observacao_2='*** NF DENEGADA ***',
                                                     T002_Valor_Duplicata=0,
                                                     T002_Valor_Total=0
                                          where T002_T007_Id='{$mT007['T007_Id']}'");
                        //-- Cancelando Contas a Pagar
                        mysqli_query("update T015 set T015_Flag_Cancelada='S',
                                                     T015_Data_Pagamento=current_date(),
                                                     T015_Observacao_2='*** NF DENEGADA ***',
                                                     T015_Valor_Duplicata=0,
                                                     T015_Valor_Total=0
                                           where T015_T007_Id='{$mT007['T007_Id']}'");
                        mysqli_query("update T001 set T001_Flag_Operacao='C' where T001_T007_Id='{$mT007['T007_Id']}'");
                        $cSql_T008=mysqli_query("select D006_Flag_Estoque,
                                                        T008_D009_Id,
                                                        T008_Quantidade,
                                                        T007_D024_Id,
                                                        T008_Id,
                                                        T007_Id,
                                                        T008_C004_Id,
                                                        T007_D006_Id,
                                                        T008_Codigo_Produto,
                                                        T007_Numero_Nota_Fiscal,
                                                        D049_D001_Id,
                                                        T008_D009_Id 
                                                   from T008 
                                              left join T007 on T008_T007_Id=T007_Id 
                                              left join D006 on T007_D006_Id=D006_Id 
                                              left join D009 on D009_Id=T008_D009_Id
                                              left join D049 on D049_Id=D009_D049_Id
                                                  where T008_T007_Id='{$mT007['T007_Id']}'");
                        while ($mT008=mysql_fetch_assoc($cSql_T008)) {
                            ///////////////////////////////////////////////////////////////////////////////////////////////////
                            //-- INVERSAO DOS FLAG POR SER UM CANCELAMENTO
                            ///////////////////////////////////////////////////////////////////////////////////////////////////
                            if ($mT008['D006_Flag_Estoque']=='D') {
                                   $Flag_Operacao_Historico="E";
                            } elseif($mT008['D006_Flag_Estoque']=='C'){
                                   $Flag_Operacao_Historico="S";
                            } elseif($mT008['D006_Flag_Estoque']=='N'){
                                   $Flag_Operacao_Historico="N";
                            }

                            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            // ENCONTRA O ID DO T066
                            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $cSql_T066=mysqli_query("select T066_Id from T066 where T066_D048_Id='1' and T066_D009_Id='{$mT008['T008_D009_Id']}'");
                            $mT066=mysql_fetch_assoc($cSql_T066);

                            require_once 'bibliotecas/classes/CAD002.php';
                            $CAD002 = new \hardness\CAD002();
                            $CAD002->D001_reprocessa_historico(0,$mT008['T008_D009_Id'],10,false);                            
                        
                            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            // HISTORICO DO PRODUTO
                            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                        }

                        // PROCESSO DE ATUALIZAR DATA DA PRIMEIRA ULTIMA COMPRA SENDO EXECUDATO PELO CRONTAB
                        /*$sqlUpdD024  = " UPDATE D024 SET";
                        $sqlUpdD024 .= " D024_Data_Primeira_Compra = T007_Primeira_Ultima_Compra(1,D024_Id),";
                        $sqlUpdD024 .= " D024_Data_Ultima_Compra = T007_Primeira_Ultima_Compra(2,D024_Id),";
                        //Function parametro = 3 retorna a empresa da ultima compra
                        $sqlUpdD024 .= " D024_C004_Id_Ultima_Compra = T007_Primeira_Ultima_Compra(3,D024_Id)";
                        $sqlUpdD024 .= " WHERE D024_Id={$mT007['T007_D024_Id']}";
                        $cmD204 = mysqli_query($sqlUpdD024);*/

                        mysqli_query("COMMIT");
                        mysqli_query("SET AUTOCOMMIT=1");
                        // FIM DENEGAR
                    }
                    // Ok!
                    msg_erro_nfe(9);
                    unlink("/tmp/QueueNF-{$Q001['Q001_Database']}-{$Q001['Q001_C004_Id']}-{$mT007['T007_Numero_Nota_Fiscal']}.txt");
                } else {

                    // Email!
                    echo "\n";
                    echo "**************************************************************************************\n";
                    echo "* 6 - Chamando script para enviar e-mail \n";
                    echo "**************************************************************************************\n";
                    echo "\n";
                    $logTxt = "6 - Chamando script para enviar e-mail.\r\n";
                    $r = fwrite($h, $logTxt);

                    include ('scripts/queueNF/etapa4-enviarEmail.php');

                    // Ok!
                    msg_erro_nfe(9);
                    unlink("/tmp/QueueNF-{$Q001['Q001_Database']}-{$Q001['Q001_C004_Id']}-{$mT007['T007_Numero_Nota_Fiscal']}.txt");

                    // Entrada do XML em transferências
                    $sql = <<<EOT
                        SELECT
                            T224_Id, C004_Id, T007_Data_Emissao, T007_Numero_Protocolo_Nfe
                        FROM T007
                        LEFT JOIN T005 ON T007_T005_Id=T005_Id
                        LEFT JOIN T224 ON T005_T224_Id=T224_Id
                        LEFT JOIN C004 ON T224_C004_Id=C004_Id
                        WHERE
                            IFNULL(T224_Id,0) > 0
                            AND T007_Id = '{$nT007_Id}'
EOT;
                    $resT224 = mysqli_query($sql);
                    if (mysql_num_rows($resT224) > 0) {
                        $rowT224 = mysql_fetch_assoc($resT224);
                        echo "Entrada de nota da OC: \n" . $rowT224['T224_Id'] . "\n";
                        include ('scripts/queueNF/etapa5-entradaXml.php');
                    }
                }
                return false;
            }
       }
       else
       {
            echo "\n";
            echo "**************************************************************************************\n";
            echo "* 9 - Consultou NF e nao retornou protocolo\n";
            echo "**************************************************************************************\n";
            echo "\n";

            $logTxt = "9 - Consultou NF e nao retornou protocolo\r\n";
            $r = fwrite($h, $logTxt);
            
            //$cArquivo_Assinado=$oNFE->getArquivoAssinado();
            $cMotivo=$oNFE->getMotivo();
            //print_r($oNFE->getErro());
            $Codigo_Retorno=$oNFE->cStat;
            $XML_Retorno=$oNFE->RetornoXMLSefaz;
            $XML_Retorno=addslashes($XML_Retorno);

            $logTxt = "Consulta sefaz status: {$Codigo_Retorno} - Motivo: {$cMotivo} \r\n";
            $r = fwrite($h, $logTxt);

/*            if (!mysqli_ping($con)) {
                echo '\n\n Tentando reconectar Banco de Dados.';
                $logTxt = "Tentando reconectar Banco de Dados. \r\n";
                $r = fwrite($h, $logTxt);
                
                $con = @mysql_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass']);
                $Error = mysql_error();
                if (!empty($Error)) {
                    echo '\n\n Erro 9.1 Não foi possível se reconectar Banco de Dados.';
                    $logTxt = "Erro 9.1 Não foi possível se reconectar Banco de Dados. \r\n";
                    die("Erro 9.1 Não foi possível se reconectar" . $Error); 
                }
                
                $dbs = mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
                if (isset($usarUTF8) && $usarUTF8) {
                    mysql_set_charset('utf8');
                    mysqli_query('SET NAMES utf8;');
                }
            }*/

            // Se não retornou nenhum status problema é com a conexão com a SEFAZ
            if($Codigo_Retorno <= 0){
                $logTxt = "Código do retorno é menor que 0, a sefaz não retornou nenhum código. \r\n";
                $r = fwrite($h, $logTxt);
                
                msg_erro_nfe(202, '', true, 'Servico indisponivel no momento, favor tentar enviar a nota mais tarde.');
            }

            $modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';
            mysqli_query("insert into T127 (T127_C004_Id,
                                           T127_D024_Id,
                                           T127_Chave_Acesso_Nfe,
                                           T127_Numero_Nota_Fiscal,
                                           T127_Codigo_Retorno,
                                           T127_Numero_Recibo_Nfe,
                                           T127_Data_Transmicao,
                                           T127_Retorno_Sefaz,
                                           T127_Xml_Retorno,
                                           T127_Modelo,
                                           T127_T007_Id
                                           ) values (
                                           '{$mT007['T007_C004_Id']}',
                                           '{$mT007['T007_D024_Id']}',
                                           '{$mT007['T007_Chave_Acesso_Nfe']}',
                                           '{$mT007['T007_Numero_Nota_Fiscal']}',
                                           '$Codigo_Retorno',
                                           '{$mT007['T007_Numero_Recibo_Nfe']}',
                                           now(),
                                           '$cMotivo',
                                           '$XML_Retorno',
                                           '{$modelo}',
                                           '{$nT007_Id}')");
    

           mostrarMsgErro($oNFE->cStat,$oNFE->getPastaArquivoXML()."assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml",$oNFE->getMotivo());
       }
   }
   else
   {    
        echo "\n";
        echo "**************************************************************************************\n";
        echo "* 9 - Status do servico nao esta ok \n";
        echo "**************************************************************************************\n";
        echo "\n";

        $logTxt = "9 - Status do servico nao esta ok \r\n";
        $r = fwrite($h, $logTxt);
        
        $cMotivo=$oNFE->getMotivo();
        $cErro=$oNFE->getErro();
        //print_r($cMotivo);

        $Codigo_Retorno=$oNFE->cStat;
        $XML_Retorno=$oNFE->RetornoXMLSefaz;
        $XML_Retorno=addslashes($XML_Retorno);

        $modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';
        mysqli_query("insert into T127 (T127_C004_Id,
                                       T127_D024_Id,
                                       T127_Chave_Acesso_Nfe,
                                       T127_Numero_Nota_Fiscal,
                                       T127_Codigo_Retorno,
                                       T127_Numero_Recibo_Nfe,
                                       T127_Data_Transmicao,
                                       T127_Retorno_Sefaz,
                                       T127_Xml_Retorno,
                                       T127_Modelo,
                                       T127_T007_Id
                                       ) values (
                                       '{$mT007['T007_C004_Id']}',
                                       '{$mT007['T007_D024_Id']}',
                                       '{$mT007['T007_Chave_Acesso_Nfe']}',
                                       '{$mT007['T007_Numero_Nota_Fiscal']}',
                                       '$Codigo_Retorno',
                                       '{$mT007['T007_Numero_Recibo_Nfe']}',
                                       now(),
                                       '$cMotivo',
                                       '$XML_Retorno',
                                       '{$modelo}',
                                       '{$nT007_Id}')");

	    if(!empty($cErro))
	    {
            $logTxt = "Erro 115 insert T127 - Codigo retorno:{$Codigo_Retorno} -  {$cErro} \r\n";
            $r = fwrite($h, $logTxt);
            fclose($h);
	    	msg_erro_nfe(115, "Número NF: {$mT007['T007_Numero_Nota_Fiscal']} - Número do recibo: {$cNumero_Recibo} - Erro: {$cMsg_Erro} - Suporte: {$oNFE->debug_str}");
            return false;
	    }
	    else
	    {        
			mostrarMsgErro($oNFE->cStat,$oNFE->getPastaArquivoXML()."assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml",$oNFE->getMotivo());
	    }
   }
}