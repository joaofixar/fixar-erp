<?php
namespace hardness;
/**
* Etapa 1 - Geração do XML da NF
*/
msg_erro_nfe(1);

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

$logTxt = "\r\n Etapa 1 - Gerar XML \r\n";
$r = fwrite($h, $logTxt);

$oNFE->setIdentAmbiente($mT007['T007_Ambiente_NFe']);  //--  1-Produção / 2-Homologação
$oNFE->setUFEmitente($mT007['T007_Empresa_Usuaria_Uf']);
if ($mT007['T007_Versao_NFe']=='1.10') {
    $oNFE->setWebService();
}
$oNFE->setModuloNFe($_SESSION['Modulo_NFe']);
$oNFE->setCodigoUFEmitente($mT007['T007_Empresa_Usuaria_Codigo_Estado_IBGE']);
$oNFE->setSenhaCertificado($mT007['C004_Senha_Certificados']);

$oNFE->setModeloCertificado($mConfig['C020_NFe_Tipo_Certificado']);

if ($mConfig['C020_NFe_Treinamento']!='S')
{
    $oNFE->setCertificadoHardness($certificadoHardness);
    $oNFE->setCertificadoPEM("{$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pem");
    $oNFE->carregaCertP12("{$mT007['C004_Localizacao_Certificados']}{$mT007['T007_C004_Id']}.pfx");
}

if (empty($mT007['T007_Numero_Recibo_Nfe']))
{
    //-- SE NAO EXISTE RECIBO ɉ POR QUE A NOTA NAO FOI ENVIADA AINDA

    if (empty($mT007['T007_Chave_Acesso_Nfe']))
    {
                
        $logTxt = "T007_Chave_Acesso_Nfe está vazio - Criando o arquivo XML. \r\n";
        $r = fwrite($h, $logTxt);

        //-- SO CRIA O XML SE NAO EXISTIR CHAVE DE ACESSO
        $oNFE->criarArquivoXML();
        $cChave_Acesso = $oNFE->getChaveAcessoNFe();
        $cChave_Acesso = str_replace("NFe","",$cChave_Acesso);

        if ($oNFE->errorStatus===true)
        {
            // TODO: botão refazer envio?
            $cMsg_Erro = $oNFE->getErro();
            msg_erro_nfe(109, $cMsg_Erro);

            $logTxt = "Erro ao gerar XML - {$cMsg_Erro}. \r\n";
            $r = fwrite($h, $logTxt);
            fclose($h);

            //$cMsg_Erro.= "<BR><BR><input type='button' value='Corrija o erro e DEPOIS clique aqui para refazer o envio'  onclick=\"location.replace('refazer_envio_nfe.php?T007_Id=$nT007_Id');\"; />";
            return false;
        }

        if ($mConfig['C020_NFe_Treinamento']=='S')
        {
            $logTxt = "Entrou no if C020_NFe_Treinamento=S\r\n";
            $r = fwrite($h, $logTxt);
            
            $cClausula  = " update T007 set ";
            $cClausula .= " T007_Chave_Acesso_Nfe = '$cChave_Acesso', ";
            $cClausula .= " T007_Numero_Recibo_Nfe = '".substr($cChave_Acesso,-20)."', ";
            $cClausula .= " T007_Data_Hora_Recebimento_Nfe = now(), ";
            $cClausula .= " T007_Numero_Protocolo_Nfe = '".substr($cChave_Acesso,-20)."', ";
            $cClausula .= " T007_Data_Hora_Protocolo_Nfe = now() ";
            $cClausula .= " where T007_Id='{$nT007_Id}' ";

            mysqli_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro))
            {
                $logTxt = "Erro 110 no update T007 - {$cErro}. \r\n";
                $logTxt .= "\r\nClausula: {$cClausula}. \r\n";
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(110);
                return false;
            }
            
            $T007=mysqli_query("select T007_Flag_ACP,
                                      T007_C004_Id,
                                      T007_D024_Id,
                                      T007_Chave_Acesso_Nfe,
                                      T007_Numero_Nota_Fiscal,
                                      T007_Data_Emissao,
                                      T007_Serie
                                 from T007 
                                where T007_Id='{$nT007_Id}'");
            $mT007=mysql_fetch_assoc($T007);           

            $cErro = mysql_error();
            if (!empty($cErro))
            {
                $logTxt = "Erro 100 no select T007 - {$cErro}. \r\n";
                $logTxt .= "\r\n select T007_Flag_ACP,
                                        T007_C004_Id,
                                        T007_D024_Id,
                                        T007_Chave_Acesso_Nfe,
                                        T007_Numero_Nota_Fiscal,
                                        T007_Data_Emissao,
                                        T007_Serie
                                from T007 
                                where T007_Id='{$nT007_Id}'";
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(100, 'etapa1');
                return false;
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
                                           '{$cNumero_Recibo}',
                                           now(),
                                           'Gerou Chave Acesso Treinamento',
                                           '{$XML_Retorno}',
                                           '{$modelo}',
                                           '{$nT007_Id}')");

            $cErro = mysql_error();
            if (!empty($cErro))
            {
                $logTxt = "Erro 111 no insert T127 - Gerou Chave Acesso Treinamento - {$cErro}. \r\n";
                $logTxt .= "\r\n Clausula: insert into T127 (T127_C004_Id,           
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
                                                            '{$cNumero_Recibo}',
                                                            now(),
                                                            'Gerou Chave Acesso Treinamento',
                                                            '{$XML_Retorno}',
                                                            '{$modelo}',
                                                            '{$nT007_Id}') \r\n";
                
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(111);
                return false;
            }
            
            $cAno_Mes_NFe=substr($mT007['T007_Data_Emissao'],0,7);

            //-- COPIA O ARQUIVO SEM ASSINATURA PARA A PASTA AUTORIZADO
            $Origem="{$Diretorio_NFe}/xml/assinado/{$mT007['T007_Numero_Nota_Fiscal']}_{$mT007['T007_Serie']}.xml";
            $Destino="{$Diretorio_NFe}/xml/autorizado/{$cAno_Mes_NFe}/".substr($cChave_Acesso,-20).".xml";

            if (!@copy($Origem, $Destino))
            {
                $logTxt = "Nao conseguiu copiar o xml da pasta {$Origem} para a pasta {$Destino}. \r\n";
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(112, "{$Destino} para: {$Destino}");
                return false;
            }
            else
            {
                $logTxt = "NF de treinamento \r\n";
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(7, "TREINAMENTO");
                return false;
            }
        }
        else
        {
            $cClausula  = " update T007 set ";
            $cClausula .= " T007_Chave_Acesso_Nfe = '".str_replace("NFe","",$cChave_Acesso)."' ";
            $cClausula .= " where T007_Id='".$nT007_Id."' ";
            mysqli_query($cClausula);
            
            $cErro = mysql_error();
            if (!empty($cErro))
            {
                $logTxt = "Erro 110 mysql Update T007 - {$cErro} \r\n";
                $logTxt = "\r\n Clausula: {$cClausula} \r\n";
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(110);
                return false;
            }

            $T007=mysqli_query("select T007_Flag_ACP,
                                      T007_C004_Id,
                                      T007_D024_Id,
                                      T007_Chave_Acesso_Nfe,
                                      T007_Numero_Nota_Fiscal
                                 from T007 
                                where T007_Id='$nT007_Id'");
            $mT007=mysql_fetch_array($T007);           
            
            $cErro = mysql_error();
            if (!empty($cErro))
            {
                $logTxt = "Erro 100 mysql select T007 - {$cErro} \r\n";
                $logTxt .= "select T007_Flag_ACP,
                                    T007_C004_Id,
                                    T007_D024_Id,
                                    T007_Chave_Acesso_Nfe,
                                    T007_Numero_Nota_Fiscal
                            from T007 
                            where T007_Id='$nT007_Id' \r\n";
                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(100);
                return false;
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
                                           '{$cNumero_Recibo}',
                                           now(),
                                           'Gerou Chave Acesso',
                                           '{$XML_Retorno}',
                                           '{$modelo}',
                                           '{$nT007_Id}')");
            
            $cErro = mysql_error();
            if (!empty($cErro))
            {
                $logTxt = "Erro 111 mysql insert T127 - Gerou Chave Acesso - - {$cErro} \r\n";
                $logTxt .= "\r\n Clausula: insert into T127 (T127_C004_Id,
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
                                                            '{$cNumero_Recibo}',
                                                            now(),
                                                            'Gerou Chave Acesso',
                                                            '{$XML_Retorno}',
                                                            '{$modelo}',
                                                            '{$nT007_Id}') \r\n";

                $r = fwrite($h, $logTxt);
                fclose($h);

                msg_erro_nfe(111);
                return false;
            }
       }
    }
}

msg_erro_nfe(4);
recolocarNaFila();
