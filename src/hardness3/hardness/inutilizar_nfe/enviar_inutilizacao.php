<?php
namespace hardness;
global $g, $confUsuario;
require_once("../../sistema/apps.php");
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
require_once("../../bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();
require_once('../../sistema/confSistema.php');
$g['conf'] = new confSistema();
include "../include/pasta_usuario.inc";
include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";

/*

  Programador..............: EVANTONI FUCHS
  Data Criacao.............: 21/06/2010
  Objetivo.................: envia o inutilizacao para o sefaz

*/


$cSql_C004=mysql_query("select *
                          from C004
                     left join D020 on D020_Id=C004_D020_Id
                     left join D018 on D018_Id=D020_D018_Id
                         where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($cSql_C004);

$resultado = mysql_query("select * from C020 where C020_C004_Id = '{$Empresa_Atual}'");
$mConfig = mysql_fetch_array($resultado);

// if($T007_Flag_ACP == '8'){
//     $mC004[C004_Versao_NFe] = '3.10';
// }

if ($mC004[C004_Versao_NFe]=='1.10')
{
   //--  Class para gerar o arquivo da NFe VERSAO 1.10
   include "../class/nfe.class.php";
}
else if ($mC004[C004_Versao_NFe]=='2.00')
{
   //--  Class para gerar o arquivo da NFe VERSAO 2.00
   include "../class/nfe2.class.php";
}
else if ($mC004[C004_Versao_NFe]=='3.10')
{
   //--  Class para gerar o arquivo da NFe VERSAO 2.00
   include "../class/nfe3.class.php";
}
else
{
   //--  Class para gerar o arquivo da NFe VERSAO 3.00
   include "../class/nfe4.class.php";
}

set_time_limit(0);

$nAno_Atual                      = $_GET[Ano_Atual];
$T007_Serie                      = $_GET[T007_Serie];
$T007_Numero_Nota_Fiscal_Inicial = $_GET[T007_Numero_Nota_Fiscal_Inicial];
$T007_Numero_Nota_Fiscal_Final   = $_GET[T007_Numero_Nota_Fiscal_Final];
$T007_Justificativa_Inutilizacao = $_GET[T007_Justificativa_Inutilizacao];
$T007_Flag_ACP                    = $_GET[T007_Flag_ACP];
$Recuperacao_Inutilizacao         = (int) ($_GET['T127_Recuperacao_Id'] ?? 0) > 0;
$ven003DivRefresh                = $_GET['ven003DivRefresh'] ?? '';

if (empty($nAno_Atual))
{
    echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
    msg_erro_nfe("Ano nao foi encontrado");
    exit;
}
if (empty($T007_Serie))
{
    echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
    msg_erro_nfe("Serie deve ser selecionada");
    exit;
}
if (strlen($T007_Justificativa_Inutilizacao)<15)
{
    echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
    msg_erro_nfe("Justificativa de inutilização deve ter mais de 15 caracteres");
    exit;
}
elseif (strlen($T007_Justificativa_Inutilizacao)>255)
{
    echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
    msg_erro_nfe("Caracteres excedidos para justificativa de inutilização. Ultrapassou 255 caracteres");
    exit;
}

$xCNPJ=$mC004[C004_Cnpj];
$mC004[C004_Cnpj]=str_replace(array("-",".","/"," "),"",$mC004[C004_Cnpj]);


if (empty($mC004[C004_Cnpj]))
{
   echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
   msg_alerta("Nao foi preenchido o CNPJ no cadastro da empresa emitente.");
   exit;
}
$cIP_Servidor="201.22.57.137";
if ($_SERVER[SERVER_NAME]==$cIP_Servidor)
{
//   $cC025=mysql_query("select C025_D024_Id from e001.C025 left join e001.D024 on D024_Id=C025_D024_Id where D024_Cnpj='$xCNPJ'");
//   $mC025=mysql_fetch_array($cC025);
}

$oNFE = new classeNFe($T007_Flag_ACP);

//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
$Diretorio_NFe = $confUsuario['pathRaiz'] . "../hardness/dados_usuarios/".$confUsuario['dbDatabase']."/nfe";
$oNFE->setPastaArquivoXML("$Diretorio_NFe/xml");
$oNFE->setIdentAmbiente($mC004[C004_Ambiente_NFe]);
if ($T007_Serie>=900)
{
   $oNFE->setTipoDocAuxiliar('3');
}
else
{
   $oNFE->setTipoDocAuxiliar('1');
}
if($T007_Flag_ACP==8){
    $modelo='65';
}else{
    $modelo='55';
}
$oNFE->setCodigoUFEmitente($mC004[D018_Codigo_IBGE]);
$oNFE->setModeloCertificado($mConfig[C020_NFe_Tipo_Certificado]);
$oNFE->setCnpjEmitente($mC004[C004_Cnpj]);
$oNFE->setUFEmitente($mC004[D018_UF]);
if ($mC004[C004_Versao_NFe]=='1.10') $oNFE->setWebService();
$oNFE->setVersaoNFe($mC004[C004_Versao_NFe]);
$oNFE->setSenhaCertificado($mC004[C004_Senha_Certificados]);
$oNFE->setCertificadoHardness(DIR_WWW."/principal/hardness.pem");
$oNFE->setCertificadoPEM($mC004[C004_Localizacao_Certificados]."$mC004[C004_Id].pem");
$oNFE->carregaCertP12($mC004[C004_Localizacao_Certificados]."$mC004[C004_Id].pfx");
$oNFE->criarIdInutilizacao($nAno_Atual,$T007_Serie,$modelo,$T007_Numero_Nota_Fiscal_Inicial,$T007_Numero_Nota_Fiscal_Final,$T007_Justificativa_Inutilizacao);

$cArquivo_Assinado = $oNFE->getPastaArquivoXML().'assinado/INUT_assinado_'.$oNFE->id_inutilizacao.'.xml';

$cArquivo_Sem_Assinatura=$oNFE->getPastaArquivoXML()."assinado/INUT_".$oNFE->id_inutilizacao.".xml";

if (!file_exists("$cArquivo_Assinado") and $mConfig[C020_NFe_Tipo_Certificado]=='A3')
{
   $Origem="INUT_".$oNFE->id_inutilizacao.".xml";
   $Destino="INUT_assinado_".$oNFE->id_inutilizacao.".xml";
   echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
   msg_erro_nfe("Arquivo de Inutilizacao n�o foi assinado ainda, por favor, <a href='../inutilizar_nfe/assinar_inutilizacao.php?Origem=$Origem&Destino=$Destino&Diretorio_NFe=$Diretorio_NFe&Ano_Atual=$nAno_Atual&T007_Serie=$T007_Serie&T007_Numero_Nota_Fiscal_Inicial=$T007_Numero_Nota_Fiscal_Inicial&T007_Numero_Nota_Fiscal_Final=$T007_Numero_Nota_Fiscal_Final&T007_Justificativa_Inutilizacao=$T007_Justificativa_Inutilizacao'>clique aqui</a> para tentar assina-lo novamente");
   exit;
}

/*
 * A resposta da SEFAZ pode se perder depois que o XML foi enviado (por exemplo,
 * em uma queda de conexão ou erro 503). Este histórico precisa sobreviver ao
 * rollback da T007 para que a solicitação possa ser localizada e recuperada.
 */
if (!inutilizacao_t127_registrar_pendencia(
    $Empresa_Atual,
    $modelo,
    $T007_Numero_Nota_Fiscal_Inicial,
    $T007_Numero_Nota_Fiscal_Final,
    $T007_Serie,
    $T007_Justificativa_Inutilizacao,
    /*
     * No certificado A1 a assinatura é gerada dentro de retornoInutilizaNF(),
     * portanto neste ponto ainda existe somente o XML montado. Ele também é
     * suficiente para conferir e remontar a solicitação na recuperação.
     */
    file_exists($cArquivo_Assinado) ? $cArquivo_Assinado : $cArquivo_Sem_Assinatura
))
{
    msg_erro_nfe("Não foi possível registrar a pendência de inutilização. A transmissão não foi enviada à SEFAZ.");
    exit;
}

mysql_query("SET AUTOCOMMIT=0");
mysql_query("START TRANSACTION");

include "gravar_inutilizacao.php";

if ($oNFE->retornoInutilizaNF($nAno_Atual,$T007_Serie,$modelo,$T007_Numero_Nota_Fiscal_Inicial,$T007_Numero_Nota_Fiscal_Final,$T007_Justificativa_Inutilizacao))
{
   $cNumero_Protocolo = $oNFE->nProt;
   $Data_Recebimento = $oNFE->dhRecbto;
   $Data_Recebimento = str_replace("T"," ",$Data_Recebimento);

   /* Inicio Alteracoes 21/06/2012 */
   mysql_query("UPDATE T007 SET
     			  T007_Flag_Almoxarifado = 'N',
     			  T007_Flag_Inutilizado = 'S',
     			  T007_Versao_NFe = '{$mC004[C004_Versao_NFe]}',
     			  T007_Protocolo_Inutilizacao = '{$cNumero_Protocolo}',
     			  T007_Data_Hora_Inutilizacao = '{$Data_Recebimento}',
     			  T007_Valor_Total = '0',
     			  
     			  T007_Valor_Frete=0,
				  T007_Valor_Total_Custo=0,
				  T007_Valor_Total_Preco=0,
				  T007_Valor_Total_Produtos=0,
				  T007_Valor_Total_Produtos_Sem_Desconto=0,
				  T007_Valor_Total_Custo_Final=0,
				  T007_Valor_Total=0,
				  T007_Percentual_Comissao_Representante=0,
				  T007_Valor_Despesas_Acessorias=0,
				  T007_Valor_Desconto=0,
				  T007_Valor_Total_Base_ICMS_Impresso=0,
				  T007_Valor_Total_Base_ICMS=0,
				  T007_Valor_Total_Base_ICMS_Sem_Reducao=0,
				  T007_Valor_Total_Base_ICMS_Diferido=0,
				  T007_Aliquota_ICMS=0,
				  T007_Valor_Total_ICMS=0,
				  T007_Valor_Base_ICMS_Substituicao=0,
				  T007_Valor_ICMS_Substituicao=0,
				  T007_Valor_ICMS_Substituicao_Retencao=0,
				  T007_Valor_Total_Base_IPI=0,
				  T007_Aliquota_IPI=0,
				  T007_Valor_Total_IPI=0,
				  T007_Valor_Total_PIS=0,
				  T007_Valor_Total_COFINS=0,
          T007_Flag_Calcular='N',
						
     			  T007_Justificativa_Inutilizacao = '{$T007_Justificativa_Inutilizacao}'
     			  
     			  WHERE T007_Numero_Nota_Fiscal>='{$T007_Numero_Nota_Fiscal_Inicial}'
     			  AND T007_Numero_Nota_Fiscal<='{$T007_Numero_Nota_Fiscal_Final}'
     			  AND T007_Serie='{$T007_Serie}'
     			  AND T007_C004_Id='{$Empresa_Atual}'
     			  AND T007_Flag_ACP='{$T007_Flag_ACP}'
     ");
     
     /* Faz uma consulta para pegar o ID do T007 a ser INUTILIZADO */
     $ConT007=mysql_query("SELECT * FROM T007  
     						WHERE T007_Numero_Nota_Fiscal>='{$T007_Numero_Nota_Fiscal_Inicial}'
     			  			AND T007_Numero_Nota_Fiscal<='{$T007_Numero_Nota_Fiscal_Final}'
     			  			AND T007_Serie='{$T007_Serie}'
     			  			AND T007_C004_Id='{$Empresa_Atual}'
     			  			AND T007_Flag_ACP='{$T007_Flag_ACP}'
                         ");
     $arrT007 = mysql_fetch_assoc($ConT007);
     
     /* Limpa a Flag de classificacao do item */
      mysql_query("UPDATE T008 SET 
      				T008_Flag_Classificacao=0 
      				WHERE T008_T007_Id='{$arrT007['T007_Id']}'
                 ");
     
     /* Cancelando Contas a Receber */
      mysql_query("UPDATE T002 SET 
      				T002_Flag_Cancelada='S',
                    T002_Data_Recebimento=current_date(),
                    T002_Observacao_2='*** NF INUTILIZADA ***',
                    T002_Valor_Duplicata=0,
                    T002_Valor_Total=0
                    WHERE T002_T007_Id='{$arrT007['T007_Id']}'
                  ");
      
       /* Cancelando Contas a Pagar */
      mysql_query("UPDATE T015 SET 
      				T015_Flag_Cancelada='S',
                    T015_Data_Pagamento=current_date(),
                    T015_Observacao_2='*** NF INUTILIZADA ***',
                    T015_Valor_Duplicata=0,
                    T015_Valor_Total=0
                    WHERE T015_T007_Id='{$arrT007['T007_Id']}'
                  ");
                  
      mysql_query("UPDATE T001 SET 
      				T001_Flag_Operacao='C' 
      				WHERE T001_T007_Id='{$arrT007['T007_Id']}'
                 ");
                 
      $ConT008 = mysql_query("SELECT 
      							D006_Flag_Estoque,
                                T008_D009_Id,
                                T008_Quantidade,
						      	T007_D024_Id,
						      	T008_Id,
						      	T007_Id,
						      	T008_C004_Id,
                                T007_D006_Id,
            					T008_Codigo_Produto,
						      	T007_Numero_Nota_Fiscal,
						      	D049_D001_Id
                              FROM T008
                              	LEFT JOIN T007 on T008_T007_Id=T007_Id
                                LEFT JOIN D006 on T007_D006_Id=D006_Id
					 			LEFT JOIN D009 on D009_Id=T008_D009_Id
					 			LEFT JOIN D049 on D049_Id=D009_D049_Id
                              WHERE T008_T007_Id='{$arrT007['T007_Id']}'
                             ");
      while( $arrT008 = mysql_fetch_array($ConT008) )
      {
		  if( $arrT008[D006_Flag_Estoque] == 'D' )
                 $Flag_Operacao_Historico="E";

      	  elseif( $arrT008[D006_Flag_Estoque] == 'C' )
                 $Flag_Operacao_Historico="S";

      	  elseif( $arrT008[D006_Flag_Estoque] == 'N' )
                 $Flag_Operacao_Historico="N";

          $cSql_T066=mysql_query("select T066_Id from T066 where T066_D048_Id='$D048_Id' and T066_D009_Id='$mT008[T008_D009_Id]'");
          $mT066=mysql_fetch_array($cSql_T066);
        
          chdir("../../");
          $CAD002->D001_reprocessa_historico(0, $arrT008['T008_D009_Id'], 50, true);
          chdir("hardness/inutilizar_nfe/");
       	  
      }
      
      // PROCESSO DE ATUALIZAR DATA DA PRIMEIRA ULTIMA COMPRA SENDO EXECUDATO PELO CRONTAB
    /* mysql_query("UPDATE D024 SET
     				D024_Data_Primeira_Compra = T007_Primeira_Ultima_Compra(1,D024_Id),
     				D024_Data_Ultima_Compra = T007_Primeira_Ultima_Compra(2,D024_Id),
     				D024_C004_Id_Ultima_Compra = T007_Primeira_Ultima_Compra(3,D024_Id)
                  WHERE D024_Id='{$mT007[T007_D024_Id]}'
     		    ");*/


    mysql_query("UPDATE T005 SET
    			  T005_Flag_Status='3',
                  T005_Flag_Situacao='1',
                  T005_Flag_Almoxarifado='S'
                WHERE T005_Id='{$mT007[T007_T005_Id]}'
               ");
	

      mysql_query("UPDATE T072
              	   LEFT JOIN T007 ON T007_Id=T072_T007_Id SET
              	     T072_Flag_Atualizado='S'
                   WHERE T007_Flag_Cancelada='S'
                 ");
     
     $cErro = mysql_error();
     if (!empty($cErro))
     {
        mysql_query("ROLLBACK");
        msg_erro_nfe("Ocorreu um erro ao gravar o retorno do Sefaz na nota fiscal<BR>Por favor entre em contato com o suporte <BR>Erro:{$cErro}<BR>");
        exit;
     }
     /* Fim Alteracoes 21/06/2012 */  
   
/*   if ($_SERVER[SERVER_NAME]==$cIP_Servidor and $mC025[C025_D024_Id]>0)
   {
      $IP_Acesso=$_SERVER['REMOTE_ADDR'];
      $cClausula  = " insert into e001.A001 ( ";
      $cClausula .= " A001_D024_Id, ";
      $cClausula .= " A001_Data_Acesso, ";
      $cClausula .= " A001_IP, ";
      $cClausula .= " A001_Tipo, ";
      $cClausula .= " A001_Retorno_Sefaz ";
      $cClausula .= " ) VALUES ( ";
      $cClausula .= " '$mC025[C025_D024_Id]', ";
      $cClausula .= " now(), ";
      $cClausula .= " '$IP_Acesso', ";
      $cClausula .= " '4', ";
      $cClausula .= " '".$oNFE->xMotivo."' ";
      $cClausula .= " ) ";
      mysql_query($cClausula);
   } */
   unlink($cArquivo_Assinado);
   unlink($cArquivo_Sem_Assinatura);
}
else
{
    $cNumero_Protocolo = trim($oNFE->nProt);
    if (empty($cNumero_Protocolo))
    {
        preg_match('#\[nProt\:([0-9]+)\]#', $oNFE->getMotivo(), $protocolo);
        $cNumero_Protocolo = isset($protocolo[1]) ? $protocolo[1] : '';
    }
/*   if ($_SERVER[SERVER_NAME]==$cIP_Servidor and $mC025[C025_D024_Id]>0)
   {
       $IP_Acesso=$_SERVER['REMOTE_ADDR'];
       $cClausula  = " insert into e001.A001 ( ";
       $cClausula .= " A001_D024_Id, ";
       $cClausula .= " A001_Data_Acesso, ";
       $cClausula .= " A001_IP, ";
       $cClausula .= " A001_Tipo, ";
       $cClausula .= " A001_Retorno_Sefaz ";
       $cClausula .= " ) VALUES ( ";
       $cClausula .= " '$mC025[C025_D024_Id]', ";
       $cClausula .= " now(), ";
       $cClausula .= " '$IP_Acesso', ";
       $cClausula .= " '4', ";
       $cClausula .= " '".$oNFE->xMotivo."' ";
       $cClausula .= " ) ";
       mysql_query($cClausula);
   } */

	   if ($oNFE->errorStatus===true)
	   {
	      mysql_query("ROLLBACK");
	      msg_erro_nfe("Erro 3: ".$oNFE->getErro());
	      exit;
	   }
	   if (empty($oNFE->cStat))
	   {
	      mysql_query("ROLLBACK");
	      inutilizacao_t127_atualizar_pendencia(
		  $Empresa_Atual,
		  $modelo,
		  $T007_Numero_Nota_Fiscal_Inicial,
		  $T007_Numero_Nota_Fiscal_Final,
		  'Não houve retorno válido da SEFAZ'
	      );
	      mysql_query("COMMIT");
	      inutilizacao_resultado('pendente', 'Solicitação pendente', $T007_Numero_Nota_Fiscal_Inicial, $T007_Numero_Nota_Fiscal_Final, $modelo, '', 'Não houve retorno válido da SEFAZ. A solicitação foi mantida em Pendências para recuperação.');
	      exit;
	   }
	   else if($oNFE->cStat==563 && !empty($cNumero_Protocolo))
   {                
        mysql_query("UPDATE T007 SET
                  T007_Flag_Almoxarifado = 'N',
                  T007_Flag_Inutilizado = 'S',
                  T007_Versao_NFe = '{$mC004['C004_Versao_NFe']}',
                  T007_Protocolo_Inutilizacao = '{$cNumero_Protocolo}',
                  T007_Data_Hora_Inutilizacao = CURRENT_TIMESTAMP(),
                  T007_Valor_Total = '0',                  
                  T007_Valor_Frete=0,
                  T007_Valor_Total_Custo=0,
                  T007_Valor_Total_Preco=0,
                  T007_Valor_Total_Produtos=0,
                  T007_Valor_Total_Produtos_Sem_Desconto=0,
                  T007_Valor_Total_Custo_Final=0,
                  T007_Valor_Total=0,
                  T007_Percentual_Comissao_Representante=0,
                  T007_Valor_Despesas_Acessorias=0,
                  T007_Valor_Desconto=0,
                  T007_Valor_Total_Base_ICMS_Impresso=0,
                  T007_Valor_Total_Base_ICMS=0,
                  T007_Valor_Total_Base_ICMS_Sem_Reducao=0,
                  T007_Valor_Total_Base_ICMS_Diferido=0,
                  T007_Aliquota_ICMS=0,
                  T007_Valor_Total_ICMS=0,
                  T007_Valor_Base_ICMS_Substituicao=0,
                  T007_Valor_ICMS_Substituicao=0,
                  T007_Valor_ICMS_Substituicao_Retencao=0,
                  T007_Valor_Total_Base_IPI=0,
                  T007_Aliquota_IPI=0,
                  T007_Valor_Total_IPI=0,
                  T007_Valor_Total_PIS=0,
                  T007_Valor_Total_COFINS=0,
                  T007_Flag_Calcular='N',                        
                  T007_Justificativa_Inutilizacao = '{$T007_Justificativa_Inutilizacao}'                  
                  WHERE T007_Numero_Nota_Fiscal>='{$T007_Numero_Nota_Fiscal_Inicial}'
                  AND T007_Numero_Nota_Fiscal<='{$T007_Numero_Nota_Fiscal_Final}'
                  AND T007_Serie='{$T007_Serie}'
                  AND T007_C004_Id='{$Empresa_Atual}'
                  AND T007_Flag_ACP='{$T007_Flag_ACP}'");

        /* Faz uma consulta para pegar o ID do T007 a ser INUTILIZADO */
        $ConT007=mysql_query("SELECT * FROM T007  
                                WHERE T007_Numero_Nota_Fiscal>='{$T007_Numero_Nota_Fiscal_Inicial}'
                                AND T007_Numero_Nota_Fiscal<='{$T007_Numero_Nota_Fiscal_Final}'
                                AND T007_Serie='{$T007_Serie}'
                                AND T007_C004_Id='{$Empresa_Atual}'
                                AND T007_Flag_ACP='{$T007_Flag_ACP}'");
        $arrT007 = mysql_fetch_assoc($ConT007);

        /* Limpa a Flag de classificacao do item */
        mysql_query("UPDATE T008 SET T008_Flag_Classificacao=0 WHERE T008_T007_Id='{$arrT007['T007_Id']}'");

        /* Cancelando Contas a Receber */
        mysql_query("UPDATE T002 SET 
                        T002_Flag_Cancelada='S',
                        T002_Data_Recebimento=current_date(),
                        T002_Observacao_2='*** NF INUTILIZADA ***',
                        T002_Valor_Duplicata=0,
                        T002_Valor_Total=0
                    WHERE T002_T007_Id='{$arrT007['T007_Id']}'");

        /* Cancelando Contas a Pagar */
        mysql_query("UPDATE T015 SET 
                        T015_Flag_Cancelada='S',
                        T015_Data_Pagamento=current_date(),
                        T015_Observacao_2='*** NF INUTILIZADA ***',
                        T015_Valor_Duplicata=0,
                        T015_Valor_Total=0
                    WHERE T015_T007_Id='{$arrT007['T007_Id']}'");
                  
        mysql_query("UPDATE T001 SET T001_Flag_Operacao='C' WHERE T001_T007_Id='{$arrT007['T007_Id']}'");
                 
        $ConT008 = mysql_query("SELECT 
                                D006_Flag_Estoque,
                                T008_D009_Id,
                                T008_Quantidade,
                                T007_D024_Id,
                                T008_Id,
                                T007_Id,
                                T008_C004_Id,
                                T007_D006_Id,
                                T008_Codigo_Produto,
                                T007_Numero_Nota_Fiscal,
                                D049_D001_Id
                              FROM T008
                                LEFT JOIN T007 on T008_T007_Id=T007_Id
                                LEFT JOIN D006 on T007_D006_Id=D006_Id
                                LEFT JOIN D009 on D009_Id=T008_D009_Id
                                LEFT JOIN D049 on D049_Id=D009_D049_Id
                              WHERE T008_T007_Id='{$arrT007['T007_Id']}'");
        while($arrT008 = mysql_fetch_array($ConT008)){
            if($arrT008[D006_Flag_Estoque] == 'D'){
                $Flag_Operacao_Historico="E";
            } elseif($arrT008[D006_Flag_Estoque] == 'C'){
                $Flag_Operacao_Historico="S";
            } elseif($arrT008[D006_Flag_Estoque] == 'N'){
                $Flag_Operacao_Historico="N";
            }

            $cSql_T066=mysql_query("select T066_Id from T066 where T066_D048_Id='$D048_Id' and T066_D009_Id='$mT008[T008_D009_Id]'");
            $mT066=mysql_fetch_array($cSql_T066);

            
            chdir("../../");
            $CAD002->D001_reprocessa_historico(0, $arrT008['T008_D009_Id'], 50, true);
            chdir("hardness/inutilizar_nfe/");
          
        }
        
            // PROCESSO DE ATUALIZAR DATA DA PRIMEIRA ULTIMA COMPRA SENDO EXECUDATO PELO CRONTAB
            /*mysql_query("UPDATE D024 SET
                    D024_Data_Primeira_Compra = T007_Primeira_Ultima_Compra(1,D024_Id),
                    D024_Data_Ultima_Compra = T007_Primeira_Ultima_Compra(2,D024_Id),
                    D024_C004_Id_Ultima_Compra = T007_Primeira_Ultima_Compra(3,D024_Id)
                  WHERE D024_Id='{$mT007['T007_D024_Id']}'");*/


        mysql_query("UPDATE T005 SET T005_Flag_Status='3', T005_Flag_Situacao='1', T005_Flag_Almoxarifado='S' WHERE T005_Id='{$mT007['T007_T005_Id']}'");
        mysql_query("UPDATE T072 LEFT JOIN T007 ON T007_Id=T072_T007_Id SET T072_Flag_Atualizado='S' WHERE T007_Flag_Cancelada='S'");

        $cErro = mysql_error();
        if (!empty($cErro))
        {
            mysql_query("ROLLBACK");
            msg_erro_nfe("Ocorreu um erro ao gravar o retorno do Sefaz na nota fiscal<BR>Por favor entre em contato com o suporte <BR>Erro:{$cErro}<BR>");
            exit;
        }
   }

	   else if ($oNFE->cStat!=102)
   {
      unlink($cArquivo_Assinado);
      mysql_query("ROLLBACK");
	  if ($oNFE->cStat == 563)
	  {
		  inutilizacao_t127_atualizar_pendencia(
			  $Empresa_Atual,
			  $modelo,
			  $T007_Numero_Nota_Fiscal_Inicial,
			  $T007_Numero_Nota_Fiscal_Final,
			  'Retorno SEFAZ 563: ' . $oNFE->getMotivo()
		  );
	  }
	  else
	  {
		  inutilizacao_t127_finalizar(
			  $Empresa_Atual,
			  $modelo,
			  $T007_Numero_Nota_Fiscal_Inicial,
			  $T007_Numero_Nota_Fiscal_Final,
			  $oNFE->cStat,
			  $oNFE->getMotivo()
		  );
	  }
	  mysql_query("COMMIT");
	  inutilizacao_resultado('erro', 'Inutilização não concluída', $T007_Numero_Nota_Fiscal_Inicial, $T007_Numero_Nota_Fiscal_Final, $modelo, '', utf8_encode($oNFE->getMotivo()));
      exit;
   }
}

if ($oNFE->cStat == 102 || ($oNFE->cStat == 563 && !empty($cNumero_Protocolo)))
{
	$codigoHistorico = $oNFE->cStat;
	$motivoHistorico = $oNFE->getMotivo();
	if ($oNFE->cStat == 563)
	{
		/*
		 * O 563 é a resposta ao reenvio; com protocolo, a situação fiscal da
		 * faixa já é de inutilização confirmada. A T127 deve refletir o estado
		 * final e preservar o retorno técnico para auditoria.
		 */
		$codigoHistorico = '102';
		$motivoHistorico = 'Inutilização confirmada por recuperação';
	}
	inutilizacao_t127_finalizar(
		$Empresa_Atual,
		$modelo,
		$T007_Numero_Nota_Fiscal_Inicial,
		$T007_Numero_Nota_Fiscal_Final,
		$codigoHistorico,
		$motivoHistorico,
		$cNumero_Protocolo
	);
}
mysql_query("COMMIT");

if (empty($cNumero_Protocolo))
{
   inutilizacao_resultado('pendente', 'Solicitação concluída sem protocolo', $T007_Numero_Nota_Fiscal_Inicial, $T007_Numero_Nota_Fiscal_Final, $modelo, '', 'A SEFAZ confirmou a inutilização, porém não retornou protocolo. Consulte a situação antes de uma nova tentativa.');
}
else if ($Recuperacao_Inutilizacao)
{
   $mensagem = $oNFE->cStat == 563
      ? 'A inutilização já constava na SEFAZ. Os registros locais foram sincronizados.'
      : 'A solicitação pendente foi reenviada e confirmada pela SEFAZ.';
   inutilizacao_resultado('sucesso', 'Recuperação concluída', $T007_Numero_Nota_Fiscal_Inicial, $T007_Numero_Nota_Fiscal_Final, $modelo, $cNumero_Protocolo, $mensagem);
	 inutilizacao_atualizar_grid_pendencias($ven003DivRefresh);
}
else
{
   inutilizacao_resultado('sucesso', 'Inutilização concluída', $T007_Numero_Nota_Fiscal_Inicial, $T007_Numero_Nota_Fiscal_Final, $modelo, $cNumero_Protocolo, 'A inutilização foi autorizada e os registros locais foram atualizados.');
}

function inutilizacao_resultado($tipo, $titulo, $numeroInicial, $numeroFinal, $modelo, $protocolo, $mensagem)
{
    $estilos = array(
        'sucesso' => array('icone' => '&#10003;', 'cor' => '#287a43', 'fundo' => '#edf8f0', 'borda' => '#9fd3ad'),
        'pendente' => array('icone' => '!', 'cor' => '#8a5c00', 'fundo' => '#fff8e6', 'borda' => '#edcf85'),
        'erro'    => array('icone' => '&#10007;', 'cor' => '#a62d2d', 'fundo' => '#fff0f0', 'borda' => '#e0a0a0'),
    );
    $estilo = $estilos[$tipo] ?? $estilos['erro'];
    $faixa = (int)$numeroInicial . ' a ' . (int)$numeroFinal;
    /* Os iframes legados desta rotina são servidos como ISO-8859-1. */
    $titulo = utf8_decode(htmlspecialchars($titulo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    $mensagem = utf8_decode(htmlspecialchars($mensagem, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    $protocolo = htmlspecialchars($protocolo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $protocoloHtml = $protocolo !== '' ? '<div class="resultado-protocolo"><small>Protocolo</small><b>' . $protocolo . '</b></div>' : '';

    echo <<<EOT
        <style>
            .resultado-inutilizacao { margin: 18px; padding: 18px; border: 1px solid {$estilo['borda']}; border-radius: 6px; background: {$estilo['fundo']}; color: #28394c; font: 14px Arial, sans-serif; }
            .resultado-inutilizacao .resultado-titulo { display: flex; align-items: center; gap: 10px; color: {$estilo['cor']}; font-size: 18px; font-weight: bold; }
            .resultado-inutilizacao .resultado-icone { display: inline-block; width: 25px; height: 25px; line-height: 25px; border-radius: 50%; background: {$estilo['cor']}; color: #fff; text-align: center; }
            .resultado-inutilizacao .resultado-dados { display: flex; gap: 10px; margin: 16px 0; }
            .resultado-inutilizacao .resultado-dados > div, .resultado-inutilizacao .resultado-protocolo { min-width: 105px; padding: 9px 11px; border-radius: 4px; background: rgba(255,255,255,.65); }
            .resultado-inutilizacao small { display: block; margin-bottom: 3px; color: #65758a; }
            .resultado-inutilizacao .resultado-mensagem { line-height: 1.45; }
        </style>
        <div class="resultado-inutilizacao">
            <div class="resultado-titulo"><span class="resultado-icone">{$estilo['icone']}</span>{$titulo}</div>
            <div class="resultado-dados">
                <div><small>Faixa</small><b>{$faixa}</b></div>
                <div><small>Modelo</small><b>{$modelo}</b></div>
                {$protocoloHtml}
            </div>
            <div class="resultado-mensagem">{$mensagem}</div>
        </div>
        <script>
            if (parent.document.getElementById('Div_Transmitindo')) {
                parent.document.getElementById('Div_Transmitindo').style.display = 'none';
            }
            if (parent.document.getElementById('ven003RecuperarProcessando')) {
                parent.document.getElementById('ven003RecuperarProcessando').style.display = 'none';
            }
        </script>
EOT;
}

function inutilizacao_atualizar_grid_pendencias($divId)
{
    if ($divId === '') {
        return;
    }
    $divIdJson = json_encode($divId);
    echo <<<EOT
        <script>
            try {
                if (parent.parent && typeof parent.parent.divRefresh === 'function') {
                    parent.parent.divRefresh({$divIdJson}, true);
                }
            } catch (e) {}
        </script>
EOT;
}

function msg_erro_nfe($cErro)
{
    $cErro = utf8_decode($cErro);
    echo <<<EOT
        <style>
            .erro-inutilizacao { margin: 18px; padding: 18px; border: 1px solid #e0a0a0; border-radius: 6px; background: #fff0f0; color: #3c2b2b; font: 14px Arial, sans-serif; }
            .erro-inutilizacao h3 { margin: 0 0 12px; color: #a62d2d; font-size: 18px; }
            .erro-inutilizacao h3 span { display: inline-block; width: 24px; height: 24px; margin-right: 8px; border-radius: 50%; background: #a62d2d; color: #fff; text-align: center; line-height: 24px; }
            .erro-inutilizacao .mensagem { line-height: 1.45; }
        </style>
        <div class="erro-inutilizacao">
            <h3><span>!</span>Dados precisam ser revisados</h3>
            <div class="mensagem">{$cErro}</div>
        </div>
        <script>
            if (parent.document.getElementById('Div_Transmitindo')) {
                parent.document.getElementById('Div_Transmitindo').style.display = 'none';
            }
            if (parent.document.getElementById('ven003RecuperarProcessando')) {
                parent.document.getElementById('ven003RecuperarProcessando').style.display = 'none';
            }
        </script>
EOT;
    exit;
}

function inutilizacao_t127_registrar_pendencia($empresaId, $modelo, $numeroInicial, $numeroFinal, $serie, $justificativa, $arquivoXml)
{
    $empresaId = mysql_real_escape_string($empresaId);
    $modelo = mysql_real_escape_string($modelo);
    $numeroInicial = (int)$numeroInicial;
    $numeroFinal = (int)$numeroFinal;
    $serie = mysql_real_escape_string($serie);
    $justificativa = mysql_real_escape_string($justificativa);
    $xmlSolicitacao = mysql_real_escape_string(@file_get_contents($arquivoXml));
    $retorno = "INUTILIZACAO_PENDENTE | Série: {$serie} | Faixa: {$numeroInicial}-{$numeroFinal} | Justificativa: {$justificativa}";
    $retorno = mysql_real_escape_string($retorno);

    for ($numero = $numeroInicial; $numero <= $numeroFinal; $numero++)
    {
        $pendencia = mysql_query("SELECT T127_Id
                                    FROM T127
                                   WHERE T127_C004_Id = '{$empresaId}'
                                     AND T127_Numero_Nota_Fiscal = '{$numero}'
                                     AND T127_Modelo = '{$modelo}'
                                     AND T127_Codigo_Retorno = '000'
                                     AND T127_Retorno_Sefaz LIKE 'INUTILIZACAO_PENDENTE%'
                                ORDER BY T127_Id DESC
                                   LIMIT 1");
        $t127 = mysql_fetch_array($pendencia);

        if (!empty($t127['T127_Id']))
        {
            $gravou = mysql_query("UPDATE T127
                            SET T127_Data_Transmicao = NOW(),
                                T127_Retorno_Sefaz = '{$retorno}',
                                T127_Xml_Retorno = '{$xmlSolicitacao}'
                          WHERE T127_Id = '{$t127['T127_Id']}'");
        }
        else
        {
            $gravou = mysql_query("INSERT INTO T127 (
                                T127_C004_Id,
                                T127_Numero_Nota_Fiscal,
                                T127_Codigo_Retorno,
                                T127_Data_Transmicao,
                                T127_Retorno_Sefaz,
                                T127_Xml_Retorno,
                                T127_Modelo
                         ) VALUES (
                                '{$empresaId}',
                                '{$numero}',
                                '000',
                                NOW(),
                                '{$retorno}',
                                '{$xmlSolicitacao}',
                                '{$modelo}'
                         )");
        }

        if ($gravou === false)
        {
            return false;
        }
    }

    return true;
}

function inutilizacao_t127_atualizar_pendencia($empresaId, $modelo, $numeroInicial, $numeroFinal, $motivo)
{
    $empresaId = mysql_real_escape_string($empresaId);
    $modelo = mysql_real_escape_string($modelo);
    $numeroInicial = (int)$numeroInicial;
    $numeroFinal = (int)$numeroFinal;
    $motivo = mysql_real_escape_string($motivo);

    /*
     * Série e faixa identificam uma solicitação pendente no grid. Mantemos
     * esses dados e trocamos apenas o detalhe do último erro/retorno.
     */
    for ($numero = $numeroInicial; $numero <= $numeroFinal; $numero++)
    {
        mysql_query("UPDATE T127
                        SET T127_Retorno_Sefaz = CONCAT(
                                IF(LOCATE(' | Retorno:', T127_Retorno_Sefaz) > 0,
                                    SUBSTRING_INDEX(T127_Retorno_Sefaz, ' | Retorno:', 1),
                                    T127_Retorno_Sefaz
                                ),
                                ' | Retorno: {$motivo}'
                            )
                      WHERE T127_C004_Id = '{$empresaId}'
                        AND T127_Numero_Nota_Fiscal = '{$numero}'
                        AND T127_Modelo = '{$modelo}'
                        AND T127_Codigo_Retorno = '000'
                        AND T127_Retorno_Sefaz LIKE 'INUTILIZACAO_PENDENTE%'
                   ORDER BY T127_Id DESC
                       LIMIT 1");
    }
}

function inutilizacao_t127_finalizar($empresaId, $modelo, $numeroInicial, $numeroFinal, $codigoRetorno, $motivo, $protocolo = '')
{
    $codigoRetorno = mysql_real_escape_string($codigoRetorno);
    $protocolo = mysql_real_escape_string($protocolo);
    $motivo = trim($motivo);
    if (!preg_match('/^' . preg_quote($codigoRetorno, '/') . '\\s*[-:]/', $motivo))
    {
        $motivo = "{$codigoRetorno} - {$motivo}";
    }
    $motivo = mysql_real_escape_string($motivo);
    inutilizacao_t127_atualizar($empresaId, $modelo, $numeroInicial, $numeroFinal, $motivo, $codigoRetorno, $protocolo);
}

function inutilizacao_t127_atualizar($empresaId, $modelo, $numeroInicial, $numeroFinal, $retorno, $codigoRetorno = '000', $protocolo = '')
{
    $empresaId = mysql_real_escape_string($empresaId);
    $modelo = mysql_real_escape_string($modelo);
    $numeroInicial = (int)$numeroInicial;
    $numeroFinal = (int)$numeroFinal;
    $codigoRetorno = mysql_real_escape_string($codigoRetorno);
    $protocolo = mysql_real_escape_string($protocolo);

    for ($numero = $numeroInicial; $numero <= $numeroFinal; $numero++)
    {
        mysql_query("UPDATE T127
                        SET T127_Codigo_Retorno = '{$codigoRetorno}',
                            T127_Numero_Recibo_Nfe = '{$protocolo}',
                            T127_Retorno_Sefaz = '{$retorno}'
                      WHERE T127_C004_Id = '{$empresaId}'
                        AND T127_Numero_Nota_Fiscal = '{$numero}'
                        AND T127_Modelo = '{$modelo}'
                        AND T127_Codigo_Retorno = '000'
                        AND T127_Retorno_Sefaz LIKE 'INUTILIZACAO_PENDENTE%'
                   ORDER BY T127_Id DESC
                       LIMIT 1");
    }
}


?>
