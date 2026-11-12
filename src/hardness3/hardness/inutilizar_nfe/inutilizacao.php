<?php
namespace hardness;
global $g, $confUsuario;
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
require_once('../../sistema/confSistema.php');
require_once('../../sistema/apps.php');
require_once('../../sistema/personalizacoes.php');
$g['conf'] = new confSistema();
include "../include/pasta_usuario.inc";
include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";


$cSql_C004=mysql_query("select *
                          from C004
                     left join D020 on D020_Id=C004_D020_Id
                     left join D018 on D018_Id=D020_D018_Id
                         where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($cSql_C004);

if ($mC004['C004_Versao_NFe']=='1.10')
{
   //--  Class para gerar o arquivo da NFe VERSAO 1.10
   include "../class/nfe.class.php";
}
else if ($mC004['C004_Versao_NFe']=='2.00')
{
   //--  Class para gerar o arquivo da NFe VERSAO 1.10
   include "../class/nfe2.class.php";
} 
else if ($mC004['C004_Versao_NFe']=='3.10')
{
    //--  Class para gerar o arquivo da NFe VERSAO 2.00
    include "../class/nfe3.class.php";
}
else 
{
   //--  Class para gerar o arquivo da NFe VERSAO 2.00
   include "../class/nfe4.class.php";
}

set_time_limit(0);
$T007_Numero_Nota_Fiscal_Inicial = $_REQUEST['T007_Numero_Nota_Fiscal_Inicial'];
$T007_Numero_Nota_Fiscal_Final = $_REQUEST['T007_Numero_Nota_Fiscal_Final'];
$T007_Justificativa_Inutilizacao = $_REQUEST['T007_Justificativa_Inutilizacao'];
$T007_Serie = $_REQUEST['T007_Serie'];
$T007_Flag_ACP = $_REQUEST['T007_Flag_ACP'];
$T127_Recuperacao_Id = (int) ($_REQUEST['T127_Recuperacao_Id'] ?? 0);
$ven003DivRefresh = $_REQUEST['ven003DivRefresh'] ?? '';


if (empty($T007_Numero_Nota_Fiscal_Inicial))
{
   echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
   msg_alerta("Digite o numero da nota fiscal inicial");
   exit;
}

if (empty($T007_Numero_Nota_Fiscal_Final))
{
   echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
   msg_alerta("Digite o numero da nota fiscal final ");
   exit;
}


$mC004[C004_Cnpj]=str_replace(array("-",".","/"," "),"",$mC004[C004_Cnpj]);

if (empty($mC004[C004_Cnpj]))
{
   echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
   msg_alerta("Nao foi preenchido o CNPJ no cadastro da empresa emitente.");
   exit;
}

//-- ENCONTRA O NUMERO DA ULTIMA NOTA FISCAL

//-- ENCONTRA O NUMERO DA ULTIMA NOTA FISCAL
$cSql_T007=mysql_query("select T007_Id,T007_Numero_Nota_Fiscal
                          from T007
                         where T007_Data_Emissao > DATE_ADD(CURDATE(), INTERVAL -30 DAY) 
                           and T007_C004_Id='$Empresa_Atual'
                           and T007_Flag_ACP='{$T007_Flag_ACP}'
                           and T007_Serie='$T007_Serie'
                           order by abs(T007_Numero_Nota_Fiscal) desc
                           limit 1");

if(mysql_num_rows($cSql_T007) <= 0){

   $cSql_T007=mysql_query("select T007_Id,T007_Numero_Nota_Fiscal
                             from T007
                            where T007_C004_Id='$Empresa_Atual'
                              and T007_Flag_ACP='{$T007_Flag_ACP}'
                              and T007_Serie='$T007_Serie'
                         order by abs(T007_Numero_Nota_Fiscal) desc
                            limit 1");
}



$mT007_Ultimo=mysql_fetch_array($cSql_T007);
$cErro = mysql_error();
if (!empty($cErro))
{
   echo "Erro 1: $cErro<BR> ";
   exit;
}

if ($T007_Numero_Nota_Fiscal_Final>=$mT007_Ultimo[T007_Numero_Nota_Fiscal] and $mC004[C004_Ambiente_NFe]=='1')
{
   echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
   msg_alerta("A última nfe emitida é o número $mT007_Ultimo[T007_Numero_Nota_Fiscal], por favor não utilize numeração superior");
   exit;
}

for ($xNF=$T007_Numero_Nota_Fiscal_Inicial;$xNF<=$T007_Numero_Nota_Fiscal_Final;$xNF++)
{
    $cSql_T007=mysql_query("select T007_Protocolo_Inutilizacao,
                                   T007_Numero_Protocolo_Cancelamento_Nfe,
                                   T007_Numero_Protocolo_Nfe,
                                   T007_Flag_Inutilizado,
                                   T007_Ambiente_NFe,
                                   T007_D024_Id,
                                   T007_T005_Id
                              from T007
                             where T007_Numero_Nota_Fiscal='$xNF'
                               and T007_Serie='$T007_Serie'
                               and T007_Flag_ACP='{$T007_Flag_ACP}'
                               and T007_C004_Id='$Empresa_Atual'");

    $mT007=mysql_fetch_array($cSql_T007);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       echo "Erro 2: $cErro<BR> ";
       exit;
    }
    
    if (mysql_num_rows($cSql_T007)>0)
    {
        if (!empty($mT007[T007_Protocolo_Inutilizacao]))
        {
           echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
           msg_alerta("NF-e: $mT007[T007_Numero_Nota_Fiscal] já foi inutilizada");
           exit;
        }
        if (!empty($mT007[T007_Numero_Protocolo_Cancelamento_Nfe]))
        {
           echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
           msg_alerta("NF-e: $mT007[T007_Numero_Nota_Fiscal] ESTA CANCELADA");
           exit;
        }
        if (!empty($mT007[T007_Numero_Protocolo_Nfe]))
        {
           echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
           msg_alerta("NF-e: $mT007[T007_Numero_Nota_Fiscal] já foi enviada para o SEFAZ");
           exit;
        }
        /*
        if ($mT007[T007_Flag_Inutilizado]=='N')
        {
           echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
           msg_alerta("NF-e: $mT007[T007_Numero_Nota_Fiscal] j� existe na base de dados do sistema, por�m, ainda n�o foi enviada para o SEFAZ.");
           exit;
        }
        */
    }
}

$resultado = mysql_query("select * from C020 where C020_C004_Id = '{$Empresa_Atual}'");
$mConfig = mysql_fetch_array($resultado);

if ($mConfig[C020_NFe_Treinamento]=='S' and $mT007[T007_Ambiente_NFe]!='2')
{
   echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
   msg_alerta("NFe esta definida como treinamento e o ambiente não é homologacao, verifique!");
   exit;
}

if ($mConfig[C020_NFe_Treinamento]=='S')
{
   msg_alerta("Aviso!/pIsso é apenas um treinamento, os dados n�o ser�o enviados para a SEFAZ");
}

//-- ELIMINA O ENTER da justificativa
$T007_Justificativa_Inutilizacao = str_replace("\n","",$T007_Justificativa_Inutilizacao);
$T007_Justificativa_Inutilizacao = gRemoveCaracteresEspeciais($T007_Justificativa_Inutilizacao);
$T007_Justificativa_Inutilizacao = trim($T007_Justificativa_Inutilizacao);

if (strlen($T007_Justificativa_Inutilizacao)<15)
{
    mysql_query("ROLLBACK");
    msg_erro_nfe("Justificativa de inutilização deve ter mais de 15 caracteres");
    exit;
}
elseif (strlen($T007_Justificativa_Inutilizacao)>255)
{
    mysql_query("ROLLBACK");
    msg_erro_nfe("Caracteres excedidos para justificativa de inutilização. Ultrapassou 255 caracteres");
    exit;
}

if ($mC004[C004_Ambiente_NFe]=='2')
{
   echo "Ambiente de Homologação<BR>";
}

mysql_query("SET AUTOCOMMIT=0");
mysql_query("START TRANSACTION");

if ($mConfig[C020_NFe_Treinamento]=='S')
{
     include "gravar_inutilizacao.php";
     
     /* Inicio Alteracoes 21/06/2012 */ 
     mysql_query("UPDATE T007 SET
     			  T007_Flag_Almoxarifado = 'N',
     			  T007_Flag_Inutilizado = 'S',
     			  T007_Protocolo_Inutilizacao = 'TREINAMENTO1234',
     			  T007_Data_Hora_Inutilizacao = now(),
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
						
     			  T007_Justificativa_Inutilizacao = '{$T007_Justificativa_Inutilizacao}'
     			  
     			  WHERE T007_Numero_Nota_Fiscal>='{$T007_Numero_Nota_Fiscal_Inicial}'
     			  AND T007_Numero_Nota_Fiscal<='{$T007_Numero_Nota_Fiscal_Final}'
     			  AND T007_Serie='{$T007_Serie}'
     			  AND T007_C004_Id='{$Empresa_Atual}'
     			  AND T007_Flag_ACP='{$T007_Flag_ACP}'
     ");
     
     /* Faz uma consulta para pegar o ID do T007 a ser INUTILIZADO */
     $ConT007 = mysql_query("SELECT * FROM T007  
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

          D001_reprocessa_historico($arrT008[D049_D001_Id]);
       	  
      }
      
      // PROCESSO DE ATUALIZAR DATA DA PRIMEIRA ULTIMA COMPRA SENDO EXECUDATO PELO CRONTAB
     /*mysql_query("UPDATE D024 SET
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
}
else
{
    $nAno_Atual=date("y",time());
    $oNFE = new classeNFe($T007_Flag_ACP);
   
    $Diretorio_NFe = $confUsuario['pathRaiz'] . "../hardness/dados_usuarios/".$confUsuario['dbDatabase']."/nfe";
    //-- $Diretorio_Usuario é definido no arquivo include/pasta_usuario.inc
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

    $oNFE->setCodigoUFEmitente($mC004[D018_Codigo_IBGE]);
    $oNFE->setModeloCertificado($mConfig[C020_NFe_Tipo_Certificado]);
    $oNFE->setCnpjEmitente($mC004[C004_Cnpj]);
    $oNFE->setUFEmitente($mC004[D018_UF]);
    if ($mC004[C004_Versao_NFe]=='1.10') $oNFE->setWebService();
    $oNFE->setVersaoNFe($mC004['C004_Versao_NFe']);
    $oNFE->setSenhaCertificado($mC004[C004_Senha_Certificados]);
    $oNFE->setCertificadoHardness(DIR_WWW."/principal/hardness.pem");
    $oNFE->setCertificadoPEM($mC004[C004_Localizacao_Certificados]."$mC004[C004_Id].pem");
    $oNFE->carregaCertP12($mC004[C004_Localizacao_Certificados]."$mC004[C004_Id].pfx");

    if($T007_Flag_ACP==8){
      $modelo='65';
    }else{
      $modelo='55';
    }

    $oNFE->criarIdInutilizacao($nAno_Atual,$T007_Serie,$modelo,$T007_Numero_Nota_Fiscal_Inicial,$T007_Numero_Nota_Fiscal_Final,$T007_Justificativa_Inutilizacao);
    
    if ($oNFE->statusServico()===true)
    {
        //-- CRIA O XML

        if (!$oNFE->criarInutilizaNF($nAno_Atual,$T007_Serie,$modelo,$T007_Numero_Nota_Fiscal_Inicial,$T007_Numero_Nota_Fiscal_Final,$T007_Justificativa_Inutilizacao))
        {
            mysql_query("ROLLBACK");
            msg_erro_nfe("Ocorreu um erro ao tentar gerar o xml do arquivo de cancelamento");
            exit;
        }
        //-- ASSINA O XML CASO SEJA A3, SE FOR A1 ELE ASSINA NO RETORNO DO CANCELAMENTO
        if ($mConfig[C020_NFe_Tipo_Certificado]=='A3')
        {
            $Origem="INUT_".$oNFE->id_inutilizacao.".xml";
            $Destino="INUT_assinado_".$oNFE->id_inutilizacao.".xml";
            echo "<script>location.replace('../inutilizar_nfe/assinar_inutilizacao.php?Origem=$Origem&Destino=$Destino&Diretorio_NFe=$Diretorio_NFe&Ano_Atual=$nAno_Atual&T007_Serie=$T007_Serie&T007_Numero_Nota_Fiscal_Inicial=$T007_Numero_Nota_Fiscal_Inicial&T007_Numero_Nota_Fiscal_Final=$T007_Numero_Nota_Fiscal_Final&T007_Justificativa_Inutilizacao=$T007_Justificativa_Inutilizacao&T007_Flag_ACP=$T007_Flag_ACP&T127_Recuperacao_Id=$T127_Recuperacao_Id&ven003DivRefresh=$ven003DivRefresh')</script>";
            exit;
        }
        else
        {
            echo "<script>location.replace('../inutilizar_nfe/enviar_inutilizacao.php?Ano_Atual=$nAno_Atual&T007_Serie=$T007_Serie&T007_Numero_Nota_Fiscal_Inicial=$T007_Numero_Nota_Fiscal_Inicial&T007_Numero_Nota_Fiscal_Final=$T007_Numero_Nota_Fiscal_Final&T007_Justificativa_Inutilizacao=$T007_Justificativa_Inutilizacao&T007_Flag_ACP=$T007_Flag_ACP&T127_Recuperacao_Id=$T127_Recuperacao_Id&ven003DivRefresh=$ven003DivRefresh')</script>";
            exit;
        }

    }
    else
    {
       mysql_query("ROLLBACK");
       msg_erro_nfe("Problemas com os servidores do Sefaz: ".$oNFE->getMotivo());
       exit;
    }
}

echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";

mysql_query("COMMIT");
      
msg_alerta("Procedimento efetuado com sucesso!");


function msg_erro_nfe($cErro)
{
    global $T007_Id;
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
        </script>
EOT;
    exit;
}
?>
