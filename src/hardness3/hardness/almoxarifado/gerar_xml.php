<?php

//-- Cria ou inicia uma sess�o
require "../include/sessao.inc";

//--  Conex�o com o banco de dados
include "../include/conexao.inc";

//-- Fun��es Gen�ricas em PHP
require "../func/funcoes_genericas.inc";

include "../include/pasta_usuario.inc";

set_time_limit(0);

$nT007_Id       = $_GET['T007_Id'];
$nTentativa     = $_GET['Tentativa'];
$cContingencia  = $_GET['Contingencia'];
$cFase          = $_GET['Fase'];
$Senha          = $_POST['Senha'];

echo "<STYLE TYPE=\"text/css\">
.fonte_atencao
{
   font-size:16px;
   font-family:arial,verdana;
   font-weight:bold;
   color:darkred;
   font-style:normal;
}

.divMensagemCentro {
	width:240px;
	height:40px;
	position: absolute;
	top:50%;
	left:45%;
	margin-top:-50px;
	margin-left:-50px;
	z-index:auto;
	background-color: #FFFF00;
	border: 1px solid #004000;
	clear:both;
	vertical-align:middle;
	color: #202020;
	padding:13px;
    font-family:arial, tahoma, verdana, helvetica, sans-serif;
	font-size:12px;
	font-weight:bold;
	text-align:center;
}
</style>";


echo "<body topmargin=0 leftmargin=0 bottommargin=0 rightmargin=0>";

if (empty($nT007_Id))
{
    msg_erro_nfe("Programa que gera o xml nao recebeu o parametro: T007_Id");
    exit;
}

$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from T007 ";
$cClausula .= " left join D006 on D006_Id=T007_D006_Id ";
$cClausula .= " left join $cBd_Configuracao.C004 on C004_Id=T007_C004_Id ";
$cClausula .= " left join $cBd_Configuracao.C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
$cClausula .= " where T007_Id='$nT007_Id' ";
$cSql_T007=mysql_query($cClausula);
$mT007=mysql_fetch_array($cSql_T007);

if ($mT007[T007_Versao_NFe]=='1.10')
{
   //--  Class para gerar o arquivo da NFe VERSAO 1.10
   include "../class/nfe.class.php";
}
elseif ($mT007[T007_Versao_NFe]=='2.00')
{
   //--  Class para gerar o arquivo da NFe VERSAO 2.00
   include "../class/nfe2.class.php";
}
elseif ($mT007[T007_Versao_NFe]=='3.10')
{
   //--  Class para gerar o arquivo da NFe VERSAO 3.10
   include "../class/nfe3.class.php";
}
else
{
   $cClausula  = " update T007 set ";
   $cClausula .= " T007_Versao_NFe = '1.10' ";
   $cClausula .= " where T007_Id='$nT007_Id' ";
   mysql_query($cClausula);

   $cClausula  = " select ";
   $cClausula .= " * ";
   $cClausula .= " from T007 ";
   $cClausula .= " left join D006 on D006_Id=T007_D006_Id ";
   $cClausula .= " left join $cBd_Configuracao.C004 on C004_Id=T007_C004_Id ";
   $cClausula .= " left join $cBd_Configuracao.C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
   $cClausula .= " where T007_Id='$nT007_Id' ";
   $cSql_T007=mysql_query($cClausula);
   $mT007=mysql_fetch_array($cSql_T007);

   //--  Class para gerar o arquivo da NFe VERSAO 1.10
   include "../class/nfe.class.php";

   if ($mT007[T007_Versao_NFe]!='1.10' and $mT007[T007_Versao_NFe]!='2.00' and $mT007[T007_Versao_NFe]!='3.10')
   {
       msg_erro_nfe("Vers�o n�o foi definida na nota fiscal (T007_Versao_NFe), verifique!");
       exit;
   }

}


if ($mConfig[C020_NFe_Treinamento]=='S' and $mT007[T007_Ambiente_NFe]!='2')
{
    msg_erro_nfe("NFe esta definida como treinamento e o ambiente n�o � homologacao, verifique!");
    exit;
}

if ($mConfig[C020_NFe_Tipo_Certificado]=='A3' and $mConfig[C020_NFe_Treinamento]!='S')
{
   if (!file_exists("$Diretorio_NFe/xml/assinado/assinar_a3.php"))
   {
      msg_erro_nfe("Para o Certificado A3, � necess�rio que o arquivo: assinar_a3.php<BR>esteja na pasta: $Diretorio_NFe/xml/assinado/<BR><BR>Copie o mesmo da pasta /config/");
      exit;
   }
   if (!file_exists("$Diretorio_NFe/xml/assinado/certifica_nfe.exe"))
   {
      msg_erro_nfe("Para o Certificado A3, � necess�rio que o arquivo: certifica_nfe.exe<BR>esteja na pasta: $Diretorio_NFe/xml/assinado/<BR><BR>Copie o mesmo da pasta /config/");
      exit;
   }
}


if ($mT007[T007_Flag_ACP]!='6')
{
   msg_erro_nfe("O Tipo do documento n�o � NF-e. Verifique!!!<BR><BR>Tipo: $mT007[T007_Flag_ACP]");
   exit;
}

if ($mT007[T007_Flag_Cancelada]=='S' and empty($mT007[T007_Numero_Protocolo_Nfe]))
{
    echo "<p align=center><font class=fonte_atencao>NOTA FISCAL ESTA CANCELADA NAO PODE SER TRANSMITIDA PARA O SEFAZ</font></p>";
    exit;
}

if ($mT007[T007_Ambiente_NFe]=='2')
{
   echo "<div style='position:absolute;top:5%;left:40%;'><font class=fonte_atencao>Ambiente de Homologa��o</font></div>";
}


if ($mT007[T007_Flag_SCAN]=='S')
{
   echo "<div style='position:absolute;top:10%;left:85%;'><font class=fonte_atencao><blink>SCAN ATIVADO</blink></font></div>";
}

if ($mConfig[C020_NFe_Solicitar_Senha_Certificado]=='S')
{
    if (!empty($Senha) and $Senha!=$mT007[C004_Senha_Certificados])
    {
       echo "<div style='position:absolute;top:35%;left:38%;'><font class=fonte_atencao>Senha Inv�lida!  Tente novamente</font></div>";
       $Senha="";
    }
    if (empty($Senha))
    {
       msg_erro_nfe("<BR><form name=formulario id='formulario' method=post action='gerar_xml.php?T007_Id=$mT007[T007_Id]'>Informe a Senha do Certificado Digital:&nbsp;<input name='Senha' id='Senha' class=campo type=password >&nbsp;<input type='submit' value='Enviar' class=botao_enviar></form>");
       echo "<script>document.getElementById('Senha').focus()</script>";
       exit;
    }
}



if($cContingencia=='S')
{
    $cClausula  = " update T007 set ";
    $cClausula .= " T007_Flag_Contingencia = 'S' ";
    $cClausula .= " where T007_Id='$nT007_Id' ";
    mysql_query($cClausula);
}

$oNFE = new classeNFe();
$oNFE->setT007Id($nT007_Id);

//-- $Diretorio_NFe � definido no arquivo include/pasta_usuario.inc
$oNFE->setPastaArquivoXML("$Diretorio_NFe/xml");

if ($cContingencia=='S')
{
    $oNFE->setTipoDocAuxiliar(2);
}
elseif ($mConfig[C020_NFe_SCAN_Ativado]=='S')
{
    $oNFE->setTipoDocAuxiliar(3);
/*    if ($mT007[T007_Serie]!='900')
    {
        if (!empty($mT007[T007_Numero_Recibo_Nfe]))
        {
           msg_erro_nfe("ESSA NOTA JA POSSUI RECIBO, NAO PODE SER ENVIANDO PELA SCAN");
           exit;
        }
        if (!empty($mT007[T007_Data_Hora_Protocolo_Nfe]))
        {
           msg_erro_nfe("ESSA NOTA JA POSSUI PROTOCOLO, NAO PODE SER ENVIANDO PELA SCAN");
           exit;
        }

       	$notaf=mysql_query("select T007_Numero_Nota_Fiscal
    							from T007
     		 		           where T007_Flag_ACP='6'
    							 and T007_C004_Id='$mT007[T007_C004_Id]'
    							 and T007_Serie='900'
    						order by year(T007_Data_Emissao) desc,round(T007_Numero_Nota_Fiscal) desc limit 0,1;");
    	$mT007_ultimo=mysql_fetch_array($notaf);
    	if ($mT007_ultimo[0]>0)
    	{
    	   $mT007_ultimo[0]++;
    	}
    	else
    	{
    	   $mT007_ultimo[0]='1';
    	}
    	mysql_query("update T007 set T007_Numero_Nota_Fiscal='$mT007_ultimo[0]',T007_Serie='900',T007_Flag_SCAN='S',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='$mT007[T007_Id]'");
    	mysql_query("update T002 set T002_Numero_Duplicata=REPLACE(T002_Numero_Duplicata,'$mT007[T007_Numero_Nota_Fiscal]','$mT007_ultimo[0]'),T002_Numero_Documento='$mT007_ultimo[0]' where T002_T007_Id='$mT007[T007_Id]'");

        $cClausula  = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T007 ";
        $cClausula .= " left join D006 on D006_Id=T007_D006_Id ";
        $cClausula .= " left join $cBd_Configuracao.C004 on C004_Id=T007_C004_Id ";
        $cClausula .= " left join $cBd_Configuracao.C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " where T007_Id='$nT007_Id' ";
        $cSql_T007=mysql_query($cClausula);
        $mT007=mysql_fetch_array($cSql_T007);

    } */
}
elseif ($mT007[T007_Flag_SCAN]=='S')
{
    $oNFE->setTipoDocAuxiliar(3);
}
else
{
    $oNFE->setTipoDocAuxiliar(1);
}

$Origem="$mT007[T007_Numero_Nota_Fiscal]"."_$mT007[T007_Serie].xml";
$Destino="NFe".$mT007[T007_Chave_Acesso_Nfe].".xml";

echo "<div id='Div_Transmitindo' class=divMensagemCentro style='display:inline;'>";
echo "<img src='../imagens/gif/loading.gif' alt='' /><br /><br />";
if (!empty($mT007[T007_Numero_Protocolo_Nfe]))
{
  //-- SE TEM PROTOCOLO, ABRE A DANFE
  echo "&nbsp;Aguarde . . .&nbsp;&nbsp;&nbsp;&nbsp;abrindo Danfe $mT007[T007_Numero_Nota_Fiscal] . . .";
  echo "</div>";
  echo "<script>";
  echo "location.replace('../almoxarifado/gerar_danfe.php?T007_Id=$nT007_Id');";
  echo "</script>";
  exit;
}
elseif (!empty($mT007[T007_Numero_Recibo_Nfe]) and (!empty($mT007[T007_Chave_Acesso_Nfe])))
{
   //-- SE O ARQUIVO DE ASSINATURA JA EXISTIR, PASSA DIRETO PRO ENVIO
   echo "&nbsp;Enviando dados NF-e $mT007[T007_Numero_Nota_Fiscal] para Sefaz...";
   echo "</div>";
   echo "<script>";
   echo "location.replace('../almoxarifado/enviar_xml.php?T007_Id=$nT007_Id&Tentativa=$nTentativa');";
   echo "</script>";
}
elseif (empty($mT007[T007_Numero_Recibo_Nfe]) and empty($mT007[T007_Numero_Protocolo_Nfe]) and !empty($mT007[T007_Chave_Acesso_Nfe]))
{
   //-- SE O ARQUIVO XML JA EXISTIR, PASSA DIRETO PRA ASSINA-LO
   echo "&nbsp;Assinando XML com Certificado: $mConfig[C020_NFe_Tipo_Certificado] . . .";
   echo "</div>";
   echo "<script>";
   echo "location.replace('../almoxarifado/assinar_xml.php?T007_Id=$nT007_Id&Tentativa=$nTentativa');";
   echo "</script>";
}
elseif(empty($cFase))
{
   //-- SE NAO TEM PROTOCOLO, MOSTRA O DIV DA SEGUNDA ETAPA, A ASSINATURA DA NOTA
   echo "&nbsp;Criando XML da NF-e $mT007[T007_Numero_Nota_Fiscal] . . .";
   echo "</div>";
   sleep(2);
   echo "<script>";
   echo "location.replace('../almoxarifado/gerar_xml.php?T007_Id=$nT007_Id&Tentativa=$nTentativa&Contingencia=$cContingencia&Fase=1');";
   echo "</script>";
}
else
{
   echo "&nbsp;Criando XML da NF-e $mT007[T007_Numero_Nota_Fiscal] . . .";
   echo "</div>";
   sleep(2);
}
echo "</div>";

$oNFE->setIdentAmbiente($mT007[T007_Ambiente_NFe]);  //--  1-Produ��o / 2-Homologa��o
$oNFE->setUFEmitente($mT007[T007_Empresa_Usuaria_Uf]);
if ($mT007[T007_Versao_NFe]=='1.10') $oNFE->setWebService();
$oNFE->setModuloNFe($_SESSION[Modulo_NFe]);
$oNFE->setCodigoUFEmitente($mT007[T007_Empresa_Usuaria_Codigo_Estado_IBGE]);
$oNFE->setSenhaCertificado($mT007[C004_Senha_Certificados]);

$oNFE->setModeloCertificado($mConfig[C020_NFe_Tipo_Certificado]);

if ($mConfig[C020_NFe_Treinamento]!='S')
{
   $oNFE->setCertificadoHardness(DIR_WWW."/principal/hardness.pem");
   $oNFE->setCertificadoPEM($mT007[C004_Localizacao_Certificados]."$mT007[T007_C004_Id].pem");
   $oNFE->carregaCertP12($mT007[C004_Localizacao_Certificados]."$mT007[T007_C004_Id].pfx");
}



if (empty($mT007[T007_Numero_Recibo_Nfe]))
{
    //-- SE NAO EXISTE RECIBO � POR QUE A NOTA NAO FOI ENVIADA AINDA

    if (empty($mT007[T007_Chave_Acesso_Nfe]))
    {
       //-- SO CRIA O XML SE NAO EXISTIR CHAVE DE ACESSO
       $oNFE->criarArquivoXML();
       $cChave_Acesso = $oNFE->getChaveAcessoNFe();
       $cChave_Acesso = str_replace("NFe","",$cChave_Acesso);

       if ($oNFE->errorStatus===true)
       {
          $cMsg_Erro = $oNFE->getErro();
          $cMsg_Erro.= "<BR><BR><input type='button' value='Corrija o erro e DEPOIS clique aqui para refazer o envio'  onclick=\"location.replace('refazer_envio_nfe.php?T007_Id=$nT007_Id');\"; />";
          msg_erro_nfe($cMsg_Erro,"N");
          exit;
       }

       if ($mConfig[C020_NFe_Treinamento]=='S')
       {
           $cClausula  = " update T007 set ";
           $cClausula .= " T007_Chave_Acesso_Nfe = '$cChave_Acesso', ";
           $cClausula .= " T007_Numero_Recibo_Nfe = '".substr($cChave_Acesso,-20)."', ";
           $cClausula .= " T007_Data_Hora_Recebimento_Nfe = now(), ";
           $cClausula .= " T007_Numero_Protocolo_Nfe = '".substr($cChave_Acesso,-20)."', ";
           $cClausula .= " T007_Data_Hora_Protocolo_Nfe = now() ";
           $cClausula .= " where T007_Id='$nT007_Id' ";

           mysql_query($cClausula);
           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_erro_nfe("TREINAMENTO! Ocorreu um erro ao tentar gravar a chave de acesso na tabela T007<BR><BR>Erro:$cErro<BR><BR>Clausula:$cClausula");
              exit;
           }
           
           $T007=mysql_query("select * from T007 where T007_Id='$nT007_Id'");
           $mT007=mysql_fetch_array($T007);           

           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_erro_nfe("Ocorreu um erro ao tentar gravar a chave de acesso na tabela T127<BR><BR>Erro:$cErro");
              exit;
           }

           mysql_query("insert into T127 (T127_C004_Id,           T127_D024_Id,          T127_Chave_Acesso_Nfe,          T127_Numero_Nota_Fiscal,          T127_Codigo_Retorno,T127_Numero_Recibo_Nfe,          T127_Data_Transmicao,T127_Retorno_Sefaz, T127_Xml_Retorno)
                                   values ('$mT007[T007_C004_Id]', '$mT007[T007_D024_Id]','$mT007[T007_Chave_Acesso_Nfe]','$mT007[T007_Numero_Nota_Fiscal]','$Codigo_Retorno',    '$cNumero_Recibo',now(),               'Gerou Chave Acesso Treinamento'        , '$XML_Retorno')");

           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_erro_nfe("Ocorreu um erro ao tentar gravar a chave de acesso na tabela T127<BR><BR>Erro:$cErro");
              exit;
           }
           
           $cAno_Mes_NFe=substr($mT007[T007_Data_Emissao],0,7);

           //-- COPIA O ARQUIVO SEM ASSINATURA PARA A PASTA AUTORIZADO
           $Origem="$Diretorio_NFe/xml/assinado/$mT007[T007_Numero_Nota_Fiscal]"."_$mT007[T007_Serie].xml";
           $Destino="$Diretorio_NFe/xml/autorizado/$cAno_Mes_NFe/".substr($cChave_Acesso,-20).".xml";
           if (!@copy($Origem, $Destino))
           {
              echo "Falha ao copiar: <b>$Destino</b>  para: <b>$Destino</b> <BR>";
              exit;
           }
           else
           {
              echo "<script>";
              echo "location.replace('../almoxarifado/gerar_danfe.php?T007_Id=$nT007_Id');";
              echo "</script>";
           }
       }
       else
       {
           $cClausula  = " update T007 set ";
           $cClausula .= " T007_Chave_Acesso_Nfe = '".str_replace("NFe","",$cChave_Acesso)."' ";
           $cClausula .= " where T007_Id='".$nT007_Id."' ";
           mysql_query($cClausula);
           
           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_erro_nfe("Ocorreu um erro ao tentar gravar a chave de acesso na tabela T007<BR><BR>Erro:$cErro<BR><BR>Clausula:$cClausula");
              exit;
           }

           $T007=mysql_query("select * from T007 where T007_Id='$nT007_Id'");
           $mT007=mysql_fetch_array($T007);           
           
           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_erro_nfe("Ocorreu um erro ao tentar gravar a chave de acesso na tabela T127<BR><BR>Erro:$cErro");
              exit;
           }

           mysql_query("insert into T127 (T127_C004_Id,           T127_D024_Id,          T127_Chave_Acesso_Nfe,          T127_Numero_Nota_Fiscal,          T127_Codigo_Retorno,T127_Numero_Recibo_Nfe,          T127_Data_Transmicao,T127_Retorno_Sefaz, T127_Xml_Retorno)
                                   values ('$mT007[T007_C004_Id]', '$mT007[T007_D024_Id]','$mT007[T007_Chave_Acesso_Nfe]','$mT007[T007_Numero_Nota_Fiscal]','$Codigo_Retorno',    '$cNumero_Recibo',now(),               'Gerou Chave Acesso'        , '$XML_Retorno')");

           $cErro = mysql_error();
           if (!empty($cErro))
           {
              msg_erro_nfe("Ocorreu um erro ao tentar gravar a chave de acesso na tabela T127<BR><BR>Erro:$cErro");
              exit;
           }
       }
    }
}

echo "<script>";
echo "location.replace('assinar_xml.php?T007_Id=$nT007_Id&Tentativa=$nTentativa');";
echo "</script>";

function msg_erro_nfe($cErro)
{
    echo "<script>document.getElementById('Div_Transmitindo').style.display='none';</script>";
    echo "<table width=100% height=100% border=0 bgcolor=#F4F4F4 >";
         echo "<tr>";
              echo "<td align=center>";
                  echo "<table border=0 bgcolor=#000080>";
                      echo "<tr>";
                          echo "<td align=center>";
                             echo "<fieldset>";

                                echo "<table border=0>";
                                     echo "<tr>";
                                          echo "<td align=left>";
                                               echo "<font style='font-size:14px;font-family:arial,verdana;color:white;font-weight:bold;'>";
                                               echo $cErro;
                                               echo "</font>";
                                          echo "</td>";
                                     echo "</tr>";
                                 echo "</table>";

                              echo "</fieldset>";
                          echo "</td>";
                     echo "</tr>";
                echo "</table>";
             echo "</td>";
         echo "</tr>";
    echo "</table>";
}
?>
