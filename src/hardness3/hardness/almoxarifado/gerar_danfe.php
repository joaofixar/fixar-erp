<?php
namespace hardness;
global $g;

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
include "../include/pasta_usuario.inc";

// include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";

set_time_limit(0);

$nT007_Id       = $_GET['T007_Id'];
$nEspessura=1;
$vlrNF = $_GET['vlrNF'];
$impressaoHtml = $_GET['impressaoHtml'];

$mConfRelatorio['Linhas_Primeira_Pagina']=17; //12
$mConfRelatorio['Linhas_Segunda_Pagina']=60;  //33

echo "<iframe name='fme_geral' id='fme_geral' style='display:none'></iframe>";
echo "<script src='../func/funcoes_genericas.js'></script>";
echo "
<STYLE TYPE=\"text/css\">

.titulo_quadro
{
   font-size:8px;
   font-family:arial,verdana;
   font-weight:normal;
   font-style:italic;
}
.dados_emissor
{
   font-size:10px;
   font-family:arial,verdana;
   font-weight:normal;
   font-style:normal;
}
.caixa_completa
{
   border:".$nEspessura."px;
   border-style:solid;
   border-color:black;
}

.caixa_1
{
   border-left:".$nEspessura."px;
   border-top:".$nEspessura."px;
   border-right:0px;
   border-bottom:0px;
   border-style:solid;
   border-color:black;
}
.caixa_2
{
   border-left:0px;
   border-top:".$nEspessura."px;
   border-right:".$nEspessura."px;
   border-bottom:0px;
   border-style:solid;
   border-color:black;
}
.caixa_3
{
   border-left:".$nEspessura."px;
   border-top:0px;
   border-right:0px;
   border-bottom:".$nEspessura."px;
   border-style:solid;
   border-color:black;
}
.caixa_4
{
   border-left:0px;
   border-top:0px;
   border-right:".$nEspessura."px;
   border-bottom:".$nEspessura."px;
   border-style:solid;
   border-color:black;
}
.caixa_5
{
   border-left:".$nEspessura."px;
   border-top:".$nEspessura."px;
   border-right:".$nEspessura."px;
   border-bottom:0px;
   border-style:solid;
   border-color:black;
}
.caixa_6
{
   border-left:".$nEspessura."px;
   border-top:0px;
   border-right:".$nEspessura."px;
   border-bottom:".$nEspessura."px;
   border-style:solid;
   border-color:black;
}
.caixa_7
{
   border-left:0px;
   border-top:0px;
   border-right:".$nEspessura."px;
   border-bottom:0px;
   border-style:solid;
   border-color:black;
}
.caixa_8
{
   border-left:0px;
   border-top:".$nEspessura."px;
   border-right:0px;
   border-bottom:".$nEspessura."px;
   border-style:solid;
   border-color:black;
}
.caixa_9
{
   border-left:0px;
   border-top:0px;
   border-right:0px;
   border-bottom:".$nEspessura."px;
   border-style:solid;
   border-color:black;
}
.caixa_10
{
   border-left:0px;
   border-top:".$nEspessura."px;
   border-right:0px;
   border-bottom:0px;
   border-style:solid;
   border-color:black;
}

.caixa_11
{
   border-left:".$nEspessura."px;
   border-top:".$nEspessura."px;
   border-right:0px;
   border-bottom:".$nEspessura."px;
   border-style:solid;
   border-color:black;
}

.caixa_12
{
   border-left:0px;
   border-top:".$nEspessura."px;
   border-right:".$nEspessura."px;
   border-bottom:".$nEspessura."px;
   border-style:solid;
   border-color:black;
}

.caixa_13
{
   border-left:".$nEspessura."px;
   border-top:0px;
   border-right:".$nEspessura."px;
   border-bottom:0px;
   border-style:solid;
   border-color:black;
}

.fonte_titulo_itens
{
   font-size:8px;
   font-family:arial,verdana;
   font-weight:bold;
   font-style:normal;
}

.fonte_dados_itens
{
   font-size:10px;
   font-family:courier new,arial,verdana;
   font-weight:normal;
   font-style:normal;
}

.fonte_dados
{
   font-size:10px;
   font-family:arial,verdana;
   font-weight:bold;
   font-style:normal;
}

.fonte_subtitulo
{
   font-size:11px;
   font-family:arial,verdana;
   font-weight:bold;
   font-style:italic;
}

.fonte_normal
{
   font-size:10px;
   font-family:arial,verdana;
   font-weight:normal;
   font-style:normal;
}

.fonte_atencao
{
   font-size:16px;
   font-family:arial,verdana;
   font-weight:bold;
   color:red;
   font-style:normal;
}
</style>";

echo "<STYLE TYPE=\"text/css\">
.divMensagemCentro {
	width:220px;
	height:40px;
	position: absolute;
	top:50%;
	left:45%;
	margin-top:-50px;
	margin-left:-50px;
	z-index:auto;
	background-color: #00FF00;
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
$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from T007 ";
$cClausula .= " left join D006 on D006_Id=T007_D006_Id ";
$cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
$cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
$cClausula .= " where T007_Id='$nT007_Id' ";
$cSql_T007=mysqli_query($cClausula);
$mT007=mysql_fetch_array($cSql_T007);

$T127 = mysqli_query("SELECT * FROM T127 WHERE T127_Numero_Nota_Fiscal = '{$mT007['T007_Numero_Nota_Fiscal']}' AND T127_C004_Id = '{$mT007['T007_C004_Id']}' ORDER BY T127_Id DESC LIMIT 1");
$mT127 = mysql_fetch_array($T127);
if($mT127['T127_Codigo_Retorno'] == '301' || $mT127['T127_Codigo_Retorno'] == '302'){         
    echo "Esta NF-e está DENEGADA! Não pode ser impressa";
    exit;
}

if ($mConfig['C020_NFe_Treinamento']=='S' and $mT007['T007_Ambiente_NFe']!='2')
{
   echo "NFe esta definida como treinamento e o ambiente não é homologacao, verifique!";
    exit;
}


if ($mT007['T007_Flag_ACP']!='6' && $mT007['T007_Flag_ACP']!='8')
{
   msg_erro_nfe("O Tipo do documento não é NF-e. Verifique!!!<BR><BR>Tipo: {$mT007['T007_Flag_ACP']}");
   exit;
}

if ($mT007['T007_Flag_Cancelada']=='S' and empty($mT007['T007_Numero_Protocolo_Nfe']))
{
    echo "<p align=center><font class=fonte_atencao>NOTA FISCAL ESTA CANCELADA NAO PODE SER TRANSMITIDA PARA O SEFAZ</font></p>";
    exit;
}

if ($mT007['T007_Ambiente_NFe']=='2')
{
   if (empty($mT007['T007_Numero_Recibo_Nfe']))
   {
       echo "<p align=center><font class=fonte_atencao>Ambiente de Homologa��o</font></p>";
   }
   elseif (empty($mT007['T007_Numero_Protocolo_Nfe']))
   {
       echo "<p align=center><font class=fonte_atencao>Ambiente de Homologa��o</font></p>";
   }
}

//if ($mConfig[C020_NFe_Tipo_DANFE]=='PDF')
//{
   // IMPRIME O DANFE EM PDF, CASO CONTRARIO CONTINUA IMPRIMINDO EM HTML
   echo "<script>";
   echo "DANFE('".md5($mT007['T007_D024_Id'].'|'.$mT007['T007_Id'])."','".$vlrNF."','".$mT007['T007_Id']."','".$impressaoHtml."');";
   echo "self.close();";
   echo "</script>";
   exit;
//}

$Numero_Nota_Fiscal = str_pad($mT007['T007_Numero_Nota_Fiscal'],9,'0',STR_PAD_LEFT);
$Numero_Nota_Fiscal = substr($Numero_Nota_Fiscal,0,3).'.'.substr($Numero_Nota_Fiscal,3,3).'.'.substr($Numero_Nota_Fiscal,6,3);


////////////////////////////////////////////////////////////
// VERIFICA QUANTAS PAGINAS SER�O IMPRESSAS
////////////////////////////////////////////////////////////
$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from T008 ";
$cClausula .= " where T008_T007_Id='$nT007_Id' ";
$cClausula .= " order by T008_Id ";
$cSql_T008=mysqli_query($cClausula);

// Encontra o total das linhas a ser impressa
$Total_Linhas=sql_num_rows($cSql_T008);

if ($Total_Linhas==0) {
   //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS � DEFINIDO PARA 1,
   //-- PARA QUE SEJA IMPRESSO O CABECALHO.
   $Total_Linhas=1;
}

if ($Total_Linhas>=1) {

    if (count($mLimit)==0) {
        // Encontra o numero de paginas cheias
        $Total_Paginas = intval($Total_Linhas / $mConfRelatorio['Linhas_Segunda_Pagina']);
        // Encontra o numero de itens restantes
        if ($Total_Paginas>=1) {
          $Resto = ($Total_Linhas - ($Total_Paginas * $mConfRelatorio['Linhas_Segunda_Pagina']));
        } else {
          $Resto = $Total_Linhas;
        }

        // Verifica caso o resto seja maior que o n�mero m�ximo de itens que cabe na �ltima folha
        if ($Resto > $mConfRelatorio['Linhas_Primeira_Pagina']) {
          // Sendo maior soma mais duas folhas
          $Total_Paginas += 2 ;
        } else {
          // Sendo menor soma mais uma folha
          $Total_Paginas += 1 ;
        }
    } else {
        $Total_Paginas=count($mLimit);
        $mConfRelatorio['Linhas_Segunda_Pagina']=$mLimit[0];
    }
}

$Inicio=0;
$Pagina_Atual=0;
$nItensPorPagina=$mConfRelatorio['Linhas_Primeira_Pagina'];

for ($Impressao=0;$Impressao<$Total_Linhas;$Impressao+=$nItensPorPagina) {

    $Pagina_Atual++;

    if ($Pagina_Atual>1)
    {
       $nItensPorPagina=$mConfRelatorio['Linhas_Segunda_Pagina'];
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // FAZ A CONSULTA POR PAGINA USANDO LIMIT
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    $cClausula  = " select ";
    $cClausula .= " * ";
    $cClausula .= " from T008 ";
    $cClausula .= " where T008_T007_Id='$nT007_Id' ";
    $cClausula .= " order by T008_Id ";
    $cClausula .= " limit $Inicio,$nItensPorPagina";

    $cSql_T008=mysqli_query($cClausula);
    
    echo "<table align=center border=0 width=900 cellspacing=0 cellpadding=0 class=caixa_completa >";
    
         if ($Pagina_Atual==1)
         {
             echo "<tr>";
                  echo "<td valign=top height=10>";
                       echo "<table border=0 cellspacing=0 cellpadding=1>";
                            echo "<tr>";
                                 echo "<td width=700 align=left valign=center colspan=2 class=caixa_7>";
                                      echo "<font class='titulo_quadro'>&nbsp;RECEBEMOS DE $mT007[T007_Empresa_Usuaria_Nome] OS PRODUTOS/SERVIÇOS CONSTANTES DA NOTA FISCAL INDICADA AO LADO</font>";
                                 echo "</td>";
                                 echo "<td width=200 align=center valign=top rowspan=2>";
                                   echo "<table border=0 cellspacing=0 cellpadding=2 >";
                                        echo "<tr align=center>";
                                             echo "<td colspan=2>";
                                                  echo "<font class='fonte_normal'>NF-e</font>";
                                             echo "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                             echo "<td>";
                                                  echo "<font class='fonte_dados'>N&ordm;</font>";
                                             echo "</td>";
                                             echo "<td bgcolor=#E6E6E6 width=100%>";
                                                  echo "<font class='fonte_dados'><b><big>$Numero_Nota_Fiscal</big></b></font>";
                                             echo "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                             echo "<td width=200 colspan=2 align=left valign=center>";
                                                  echo "<font class='fonte_normal'>SERIE: $mT007[T007_Serie]</font>";
                                                  if($vlrNF == 'S'){
                                                        echo "<font class='fonte_dados'>R$ ".number_format($mT007['T007_Valor_Total'],2,',','.')."</font>";
                                                  }
                                             echo "</td>";
                                        echo "</tr>";

                                   echo "</table>";

                                 echo "</td>";
                            echo "</tr>";
                            echo "<tr>";
                                 echo "<td height=60 width=200 valign=top class=caixa_2>";
                                      echo "<font class='titulo_quadro'>DATA DE RECEBIMENTO</font>";
                                 echo "</td>";
                                 echo "<td width=500 valign=top class=caixa_2>";
                                      echo "<font class='titulo_quadro'>IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR</font>";
                                 echo "</td>";
                            echo "</tr>";
                       echo "</table>";
                  echo "</td>";
             echo "</tr>";
        echo "</table>";

        echo "<table align=center border=0 width=900 cellspacing=0 cellpadding=0  >";
            echo "<tr>";
                 echo "<td valign=center height=40 >";
                 echo "<hr style='border:1px dashed gray'>";
                 echo "</td>";
            echo "</tr>";
        echo "</table>";
    }
    
    echo "<table align=center border=0 width=900 height=900 cellspacing=0 cellpadding=5 class=caixa_completa ";
    if ($mT007['T007_Flag_Contingencia']=='S')
    {
       echo " style='background-image: url(../imagens/jpg/danfe_contingencia.jpg); background-position: center center; background-repeat: no-repeat;' ";
    }
    echo ">";

         echo "<tr>";
              echo "<td valign=top>";
                   echo "<table border=0 cellspacing=0 cellpadding=0 >";
                        echo "<tr>";
                             echo "<td class=caixa_4  align=center valign=center>";
                                  echo "<table border=0 cellspacing=0 cellpadding=0 width=100% height=100%>";
                                       echo "<tr align=center>";

                                        echo "<td height=100 valign=center align=center>";
                                        //-- $Diretorio_Imagens � definido no arquivo include/pasta_usuario.inc
                                        if (file_exists("$Diretorio_Imagens/$mT007[C004_Logo_Marca]") and $mT007['C004_Logo_Marca']!='')
                                        {
                                        echo "<img src='$Diretorio_Imagens/$mT007[C004_Logo_Marca]'>";
                                        }
                                         echo "</td>";
                                         echo "<td>";

                                              echo "<table border=0 cellspacing=0 cellpadding=0 width=100% height=100%>";
                                                   echo "<tr align=center>";
                                                        echo "<td class=dados_emissor>";
                                                             echo "<b>$mT007[T007_Empresa_Usuaria_Nome]</b>";
                                                        echo "</td>";
                                                   echo "</tr>";
                                                   echo "<tr align=center>";
                                                        echo "<td class=dados_emissor>";
                                                             echo "$mT007[T007_Empresa_Usuaria_Endereco], $mT007[T007_Empresa_Usuaria_Numero]";
                                                        echo "</td>";
                                                   echo "</tr>";
                                                   echo "<tr align=center>";
                                                        echo "<td class=dados_emissor>";
                                                             echo "$mT007[T007_Empresa_Usuaria_Bairro], $mT007[T007_Empresa_Usuaria_Cep]";
                                                        echo "</td>";
                                                   echo "</tr>";
                                                   echo "<tr align=center>";
                                                        echo "<td class=dados_emissor>";
                                                             echo "$mT007[T007_Empresa_Usuaria_Cidade] - $mT007[T007_Empresa_Usuaria_Uf]";
                                                        echo "</td>";
                                                   echo "</tr>";
                                                   echo "<tr align=center>";
                                                        echo "<td class=dados_emissor>";
                                                             if (!empty($mT007['T007_Empresa_Usuaria_Telefone']))
                                                             {
                                                                 echo "Fone: {$mT007['T007_Empresa_Usuaria_Telefone']}";
                                                             }
                                                             if (!empty($mT007['T007_Empresa_Usuaria_Fax']))
                                                             {
                                                                 echo "&nbsp;Fax: {$mT007['T007_Empresa_Usuaria_Fax']}";
                                                             }
                                                        echo "</td>";
                                                   echo "</tr>";
                                              echo "</table>";
                                         echo "</td>";
                                     echo "</tr>";
                                 echo "</table>";
                             echo "</td>";
                             echo "<td class=caixa_4 align=center valign=center>";
                                  echo "<table border=0 cellspacing=0 cellpadding=2 width=100% height=100%>";
                                       echo "<tr align=center>";
                                            echo "<td class=dados_emissor>";
                                                 echo "<font class=fonte_normal><b><big>DANFE</big></b></font>";
                                            echo "</td>";
                                       echo "</tr>";
                                       echo "<tr align=center>";
                                            echo "<td class=dados_emissor>";
                                                 echo "Documento Auxiliar da<BR>Nota Fiscal Eletrônica";
                                            echo "</td>";
                                       echo "</tr>";
                                       echo "<tr align=left>";
                                            echo "<td>";
                                                 echo "<table border=0 width=150 cellspacing=0 cellpadding=0>";
                                                      echo "<tr>";
                                                           echo "<td class=fonte_normal>";
                                                                echo "Saida: 1";
                                                           echo "</td>";
                                                           echo "<td rowspan=2 width=50 align=center class=caixa_completa height=20>";
                                                                echo "<font class=fonte_normal>";
                                                                if ($mT007['T007_Flag_Entrada_Saida']=='S')
                                                                {
                                                                    echo "1";
                                                                }
                                                                elseif ($mT007['T007_Flag_Entrada_Saida']=='E')
                                                                {
                                                                    echo "2";
                                                                }
                                                                echo "</font>";
                                                           echo "</td>";
                                                      echo "</tr>";
                                                      echo "<tr>";
                                                           echo "<td class=fonte_normal>";
                                                                echo "Entrada: 2";
                                                           echo "</td>";
                                                      echo "</tr>";
                                                 echo "</table>";
                                            echo "</td>";
                                       echo "</tr>";
                                       echo "<tr align=left>";
                                            echo "<td class=dados_emissor>";
                                                 echo "<b><big>N&ordm; $Numero_Nota_Fiscal</big></b>";
                                            echo "</td>";
                                       echo "</tr>";
                                       echo "<tr align=left>";
                                            echo "<td class=dados_emissor>";
                                                 echo "SÉRIE: $mT007[T007_Serie]";
                                            echo "</td>";
                                       echo "</tr>";
                                       echo "<tr align=left>";
                                            echo "<td class=dados_emissor>";
                                                 echo "Folha $Pagina_Atual de $Total_Paginas";
                                            echo "</td>";
                                       echo "</tr>";
                                  echo "</table>";
                             echo "</td>";

                                echo "<td class=caixa_9 width=300 valign=center align=center style='padding-left:5px;'>";
                                   echo "<table border=0 cellspacing=0 cellpadding=0 >";
                                        echo "<tr >";
                                             echo "<td>";
                                                  echo "<font class='titulo_quadro'>CONTROLE FISCO</font>";
                                             echo "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                             echo "<td>";
                                             if ($mT007['T007_Flag_Cancelada']=='S')
                                             {
                                                   echo "<font class=fonte_atencao>DANFE CANCELADA</font>";
                                             }
                                             else
                                             {
                                                   echo "<img src='../class/codebar/html/image.php?code=code128&o=1&t=35&r=1&text=$mT007[T007_Chave_Acesso_Nfe]&f=1&a1=A&a2='/>";
                                             }
                                             echo "</td>";
                                        echo "</tr>";
                                   echo "</table>";
                                   echo "<table border=0 cellspacing=2 cellpadding=0 class=caixa_completa>";
                                        echo "<tr >";
                                             echo "<td>";
                                                  echo "<font class='titulo_quadro'>CHAVE DE ACESSO</font>";
                                             echo "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                             echo "<td>";
                                                  echo "<font class='fonte_dados'>$mT007[T007_Chave_Acesso_Nfe]</font>";
                                             echo "</td>";
                                        echo "</tr>";
                                   echo "</table>";

                                   echo "<table cellpsacing=0 cellpadding=0>";
                                        echo "<tr>";
                                               echo "<td class='fonte_dados'>";
                                                 echo "Consulta de autenticidade no portal nacional da NF-e www.nfe.fazenda.gov.br/portal ou no site da Sefaz Autorizado";
                                              echo "</td>";
                                        echo "</tr>";
                                   echo "</table>";
                                echo "</td>";

                        echo "</tr>";

                        echo "<tr>";
                          echo "<td valign=top class=caixa_4 colspan=2 height=30 width=100% align=left>";
                               echo "<table border=0 cellspacing=0 cellpadding=0 >";
                                    echo "<tr >";
                                         echo "<td>";
                                              echo "<font class='titulo_quadro'>NATUREZA OPERAÇÃO</font>";
                                         echo "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                         echo "<td>";
                                            $cClausula  = " select ";
                                            $cClausula .= " D006_Descricao ";
                                            $cClausula .= " from T008 ";
                                            $cClausula .= " left join D006 on D006_Id=T008_D006_Id ";
                                            $cClausula .= " where T008_T007_Id='$T007_Id' ";
                                            $cClausula .= " group by T008_D006_Id ";
                                            $cSql_D006 = mysqli_query($cClausula);
                                            echo mysql_error();

                                            unset($mDescricao_CFOP);
                                            while ($mD006 = mysql_fetch_array($cSql_D006))
                                            {
                                                   $mDescricao_CFOP[]=$mD006['D006_Descricao'];
                                            }
                                            $cDescricao_CFOP=implode(" - ",$mDescricao_CFOP);
                                            $cDescricao_CFOP=substr($cDescricao_CFOP,0,60);

                                            echo "<font class='fonte_dados'>$cDescricao_CFOP</font>";
                                         echo "</td>";
                                    echo "</tr>";
                               echo "</table>";
                          echo "</td>";

                          echo "<td valign=top class=caixa_9 height=30 align=left>";
                               echo "<table width=100% border=0 cellspacing=0 cellpadding=0 >";
                                    echo "<tr >";
                                         echo "<td>";
                                              echo "<font class='titulo_quadro'>&nbsp;PROTOCOLO DE AUTORIZAÇÃO DE USO</font>";
                                         echo "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                         echo "<td align=center>";
                                              echo "<font class='fonte_dados'>{$mT007['T007_Numero_Protocolo_Nfe']} - ".gCorrigeData($mT007['T007_Data_Hora_Protocolo_Nfe'])."</font>";
                                         echo "</td>";
                                    echo "</tr>";
                               echo "</table>";
                          echo "</td>";
                     echo "</tr>";

                     echo "<tr>";
                          echo "<td valign=top colspan=3>";
                               echo "<table border=0 width=100% cellspacing=0 cellpadding=2 >";
                                    echo "<tr>";
                                      echo "<td valign=top class=caixa_7 height=30 width=33% align=left>";
                                           echo "<table border=0 cellspacing=0 cellpadding=0 >";
                                                echo "<tr >";
                                                     echo "<td>";
                                                          echo "<font class='titulo_quadro'>INSCRICAO ESTADUAL</font>";
                                                     echo "</td>";
                                                echo "</tr>";
                                                echo "<tr>";
                                                     echo "<td>";
                                                          echo "<font class='fonte_dados'>$mT007[T007_Empresa_Usuaria_Inscricao_Estadual]</font>";
                                                     echo "</td>";
                                                echo "</tr>";
                                           echo "</table>";
                                      echo "</td>";
                                      echo "<td valign=top class=caixa_7 height=30 width=33% align=left>";
                                           echo "<table border=0 cellspacing=0 cellpadding=0 >";
                                                echo "<tr >";
                                                     echo "<td>";
                                                          echo "<font class='titulo_quadro'>INSCRICAO ESTADUAL DO SUBST.TRIBUTARIO</font>";
                                                     echo "</td>";
                                                echo "</tr>";
                                                echo "<tr>";
                                                     echo "<td>";
                                                          echo "<font class='fonte_dados'></font>";
                                                     echo "</td>";
                                                echo "</tr>";
                                           echo "</table>";
                                      echo "</td>";
                                      echo "<td valign=top height=30 width=33% align=left>";
                                           echo "<table border=0 cellspacing=0 cellpadding=0 >";
                                                echo "<tr >";
                                                     echo "<td>";
                                                          echo "<font class='titulo_quadro'>CNPJ</font>";
                                                     echo "</td>";
                                                echo "</tr>";
                                                echo "<tr>";
                                                     echo "<td>";
                                                          echo "<font class='fonte_dados'>$mT007[T007_Empresa_Usuaria_Cnpj]</font>";
                                                     echo "</td>";
                                                echo "</tr>";
                                           echo "</table>";
                                      echo "</td>";
                                 echo "</tr>";

                               echo "</table>";
                          echo "</td>";
                     echo "</tr>";

                  echo "</table>";
              echo "</td>";
         echo "</tr>";

         echo "<tr>";
              echo "<td valign=top height=10>";
                 echo "<font class='fonte_subtitulo'>DESTINATÁRIO/REMETENTE</font>";
              echo "</td>";
         echo "</tr>";

         echo "<tr>";
              echo "<td valign=top>";
                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                        echo "<tr >";
                             echo "<td>";
                                  echo "<table border=0 cellspacing=0 cellpadding=2 width=100%>";


                                    echo "<tr>";
                                         echo "<td class=caixa_2 height=30 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>NOME/RAZÃO SOCIAL</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_subtitulo'>$mT007[T007_Cliente_Razao]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_10 height=30 width=200 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>CNPJ/CPF</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Cliente_CNPJ_CPF]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_1 height=30 width=110 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>DATA DA EMISSÃO</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>".troca_data_dma($mT007['T007_Data_Emissao'])."</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                    echo "</tr>";

                                   echo "</table>";
                             echo "</td>";
                        echo "</tr>";


                        echo "<tr >";
                             echo "<td>";
                                  echo "<table border=0 cellspacing=0 cellpadding=2 width=100%>";

                                    echo "<tr>";
                                         echo "<td class=caixa_2 height=30 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>ENDEREÇO</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Cliente_Endereco], $mT007[T007_Cliente_Endereco_Numero]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_2 height=30 width=200 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>BAIRRO/DISTRITO</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Cliente_Bairro]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_10 height=30 width=100 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>CEP</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Cliente_CEP]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_1 height=30 width=110 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>DATA DE SAIDA/ENTRADA</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>".troca_data_dma($mT007['T007_Data_Saida'])."</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                    echo "</tr>";

                                   echo "</table>";
                             echo "</td>";
                        echo "</tr>";


                        echo "<tr >";
                             echo "<td>";
                                  echo "<table border=0 cellspacing=0 cellpadding=2 width=100%>";

                                    echo "<tr>";
                                         echo "<td class=caixa_12 height=30 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>MUNICIPIO</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Cliente_Cidade]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_12 height=30 width=200 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>FONE/FAX</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Cliente_Telefone]&nbsp;</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_12 height=30 width=100 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>UF</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Cliente_Estado]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_12 height=30 width=150 valign=center align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>INSCRIÇÃO ESTADUAL</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Cliente_Inscricao_Estadual]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                         echo "<td class=caixa_8 height=30 width=110 valign=top align=center>";
                                               echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                    echo "<tr >";
                                                         echo "<td>";
                                                              echo "<font class='titulo_quadro'>HORA DE SAIDA</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                         echo "<td>";
                                                              echo "<font class='fonte_dados'>$mT007[T007_Hora_Saida]</font>";
                                                         echo "</td>";
                                                    echo "</tr>";
                                               echo "</table>";
                                         echo "</td>";
                                    echo "</tr>";

                                  echo "</table>";
                             echo "</td>";
                        echo "</tr>";

                   echo "</table>";
              echo "</td>";
         echo "</tr>";
         
         if ($Pagina_Atual==1)
         {

             $cClausula  = " select ";
             $cClausula .= " T002_Numero_Duplicata, ";
             $cClausula .= " T002_Data_Vencimento, ";
             $cClausula .= " T002_Valor_Duplicata ";
             $cClausula .= " from T002 ";
             $cClausula .= " where T002_T007_Id='$nT007_Id' ";
             $cClausula .= " and T002_Flag_Cancelada!='S' ";
             $cClausula .= " order by T002_Data_Vencimento ";

             $cSql_T002=sql_query($cClausula);
             if (sql_num_rows($cSql_T002)>0)
             {
                 echo "<tr>";
                      echo "<td valign=top>";
                         echo "<font class='fonte_subtitulo'>FATURA/DUPLICATA</font>";
                      echo "</td>";
                 echo "</tr>";
                 echo "<tr>";
                      echo "<td>";
                           echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                echo "<tr height=30>";
                                     echo "<td class=caixa_8>";

                                        while ($mT002=mysql_fetch_array($cSql_T002))
                                        {
                                           $mFatura[] ="$mT002[T002_Numero_Duplicata] - ".troca_data_dma($mT002['T002_Data_Vencimento'])." - ".number_format($mT002['T002_Valor_Duplicata'],2,',','.');
                                        }
                                        if (count($mFatura)>0)
                                        {
                                           echo "<font class='fonte_dados'>".implode(" / ",$mFatura)."</font>";
                                        }
                                        else
                                        {
                                           echo "<font class='fonte_dados'>&nbsp;</font>";
                                        }
                                     echo "</td>";
                                echo "</tr>";
                           echo "</table>";
                      echo "</td>";
                 echo "</tr>";

             }



             echo "<tr>";
                  echo "<td valign=top height=17>";
                     echo "<font class='fonte_subtitulo'>CALCULO DO IMPOSTO</font>";
                  echo "</td>";
             echo "</tr>";
             echo "<tr>";
                  echo "<td valign=top>";
                       echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                            echo "<tr >";
                                 echo "<td>";
                                      echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";


                                        echo "<tr>";
                                             echo "<td class=caixa_2 height=30 width=20% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>BASE DE CALCULO DE ICMS</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Total_Base_ICMS_Impresso'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_2 height=30 width=20% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>VALOR DO ICMS</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Total_ICMS'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_2 height=30 width=20% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>BASE DE CALCULO DE ICMS SUBSTITUIÇÃO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Base_ICMS_Substituicao'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_10 height=30 width=20% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>VALOR DO ICMS SUBSTITUIÇÃO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_ICMS_Substituicao_Retencao'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_1 height=30 width=20% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>VALOR TOTAL DOS PRODUTOS</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Total_Produtos'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                        echo "</tr>";

                                       echo "</table>";
                                 echo "</td>";
                            echo "</tr>";


                            echo "<tr >";
                                 echo "<td>";
                                      echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";

                                        echo "<tr>";
                                             echo "<td class=caixa_12 height=30 width=16% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>VALOR DO FRETE</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Frete'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30 width=16% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>VALOR DO SEGURO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format(0,2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30 width=16% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>DESCONTO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Desconto'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30 width=16% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>OUTRAS DESPESAS ACESSÓRIAS</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Despesas_Acessorias'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30 width=16% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>VALOR DO IPI</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Total_IPI'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_8 height=30 width=16% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>VALOR TOTAL DA NOTA</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Total'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                        echo "</tr>";

                                       echo "</table>";
                                 echo "</td>";
                            echo "</tr>";

                       echo "</table>";
                  echo "</td>";
             echo "</tr>";

             echo "<tr>";
                  echo "<td valign=top height=17>";
                     echo "<font class='fonte_subtitulo'>TRANSPORTADOR/VOLUMES TRANSPORTADOS</font>";
                  echo "</td>";
             echo "</tr>";

             echo "<tr>";
                  echo "<td valign=top>";
                       echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                            echo "<tr >";
                                 echo "<td>";
                                      echo "<table border=0 cellspacing=0 cellpadding=2 width=100%>";


                                        echo "<tr>";
                                             echo "<td class=caixa_2 height=30 width=300 valign=top align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>NOME / RAZÃO SOCIAL</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Nome_Transportadora]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_2 height=30 width=115 valign=top align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top colspan=2>";
                                                                  echo "<font class='titulo_quadro'>FRETE POR CONTA</font>";
                                                             echo "</td>";
                                                             echo "<td rowspan=3 valign=center align=center width=30 class=caixa_completa>";
                                                                  echo "<font class='fonte_dados'>";
                                                                  if ($mT007['T007_Flag_Frete']=='1')
                                                                  {
                                                                     echo "0";
                                                                  }
                                                                  elseif ($mT007['T007_Flag_Frete']=='2')
                                                                  {
                                                                     echo "1";
                                                                  }
                                                                  echo "</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='titulo_quadro'>0 - EMITENTE</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='titulo_quadro'>1 - DESTINATÁRIO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_2 height=30 width=100 valign=top align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>CODIGO ANTT</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=LEFT>";
                                                                  echo "<font class='fonte_dados'>&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_2 height=30 width=159 valign=top align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>PLACA DO VEICULO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=LEFT>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Transportadora_Placa_Veiculo]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_10 height=30 width=50 valign=top align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>UF</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=LEFT>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Estado_Veiculo]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_1 height=30 widht=130 valign=top align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>CNPJ/CPF</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=LEFT>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Transportadora_CNPJ]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                        echo "</tr>";

                                       echo "</table>";
                                 echo "</td>";
                            echo "</tr>";


                            echo "<tr >";
                                 echo "<td>";
                                      echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";

                                        echo "<tr>";
                                             echo "<td class=caixa_2 height=30 width=300 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>ENDEREÇO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Transportadora_Endereco]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_2 height=30 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>MUNICIPIO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Transportadora_Cidade]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_2 height=30 width=50 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>UF</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Transportadora_Estado]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_10 height=30 width=130 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>INSCRIÇÃO ESTADUAL</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Transportadora_Inscricao_Estadual]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                        echo "</tr>";

                                       echo "</table>";
                                 echo "</td>";
                            echo "</tr>";

                            echo "<tr >";
                                 echo "<td>";
                                      echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";

                                        echo "<tr>";
                                             echo "<td class=caixa_12 height=30 width=300 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>QUANTIDADE</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Quantidade_Volumes]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>ESPECIE</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Especie]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30  valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>MARCA</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Marca]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30 width=130 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>NUMERAÇO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Numero_Caixas]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30 width=90 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>PESO BRUTO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;".number_format($mT007['T007_Peso_Bruto'],3,',','.')."</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_8 height=30 width=90 valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td>";
                                                                  echo "<font class='titulo_quadro'>PESO LIQUIDO</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;".number_format($mT007['T007_Peso_Liquido'],3,',','.')."</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                        echo "</tr>";

                                       echo "</table>";
                                 echo "</td>";
                            echo "</tr>";

                       echo "</table>";
                  echo "</td>";
             echo "</tr>";

         }
         
         echo "<tr>";
              echo "<td valign=top height=17>";
                 echo "<font class='fonte_subtitulo'>DADOS DO PRODUTO/SERVIÇO</font>";
              echo "</td>";
         echo "</tr>";

         echo "<tr>";
              echo "<td valign=top height=100% >";
                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                        echo "<tr height=30>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>COD. PROD/SERVIÇO</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>DESCRIÇÃO DO PRODUTO/SERVIÇO</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>NCM/SH</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>CST</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12 width=30>";
                                  echo "<font class=fonte_titulo_itens>CFOP</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>UNID.</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12 width=45>";
                                  echo "<font class=fonte_titulo_itens>QTD.</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>VLR. UNIT.</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>VLR. TOTAL</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>BC ICMS</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>VLR. ICMS</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>VLR. IPI</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_12>";
                                  echo "<font class=fonte_titulo_itens>ALIQ.ICMS</font>";
                             echo "</td>";
                             echo "<td align=center class=caixa_8>";
                                  echo "<font class=fonte_titulo_itens>ALIQ.IPI</font>";
                             echo "</td>";
                        echo "</tr>";

                        while ($mT008=mysql_fetch_array($cSql_T008))
                        {
                            $mT008['T008_Codigo_Produto'] = substr($mT008['T008_Codigo_Produto'],0,17);


                            $mT008['T008_Descricao_Produto'] = str_replace("<"," ",$mT008['T008_Descricao_Produto']);
                            $mT008['T008_Descricao_Produto'] = str_replace(">"," ",$mT008['T008_Descricao_Produto']);
                            $mT008['T008_Descricao_Produto'] = str_replace("&"," ",$mT008['T008_Descricao_Produto']);
                            $mT008['T008_Descricao_Produto'] = str_replace("'","",$mT008['T008_Descricao_Produto']);
                            $mT008['T008_Descricao_Produto'] = str_replace('"',"",$mT008['T008_Descricao_Produto']);
                            $mT008['T008_Descricao_Produto'] = str_replace("\n","<br>",$mT008['T008_Descricao_Produto']);
                            $mT008['T008_Descricao_Produto'] = str_replace(trim("\ "),"",$mT008['T008_Descricao_Produto']);
                            $mT008['T008_Descricao_Produto'] = substr($mT008['T008_Descricao_Produto'],0,50);

                            if (empty($mT008['T008_Classificacao_Fiscal']))
                            {
                               $mT008['T008_Classificacao_Fiscal']='&nbsp;';
                            }

                            echo "<tr height=13>";
                                 echo "<td align=left class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>$mT008[T008_Codigo_Produto]</font>";
                                 echo "</td>";
                                 echo "<td align=left class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>{$mT008['T008_Descricao_Produto']}</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>$mT008[T008_Classificacao_Fiscal]</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>$mT008[T008_Flag_Situacao_Tributaria]</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>$mT008[T008_Codigo_CFOP]</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>$mT008[T008_Unidade]</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>".number_format($mT008['T008_Quantidade'],2,',','.')."</font>";
                                 echo "</td>";
                                 echo "<td align=right class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>".number_format($mT008['T008_Valor_Preco_Unitario'],2,',','.')."</font>";
                                 echo "</td>";
                                 echo "<td align=right class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>".number_format($mT008['T008_Valor_Total_Preco'],2,',','.')."</font>";
                                 echo "</td>";
                                 echo "<td align=right class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>".number_format($mT008['T008_Valor_Base_ICMS_Impresso'],2,',','.')."</font>";
                                 echo "</td>";
                                 echo "<td align=right class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>".number_format($mT008['T008_Valor_ICMS'],2,',','.')."</font>";
                                 echo "</td>";
                                 echo "<td align=right class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>".number_format($mT008['T008_Valor_IPI'],2,',','.')."</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>".number_format($mT008['T008_Aliquota_ICMS'],2,',','.')."</font>";
                                 echo "</td>";
                                 echo "<td align=center>";
                                      echo "<font class=fonte_dados_itens>".number_format($mT008['T008_Aliquota_IPI'],2,',','.')."</font>";
                                 echo "</td>";
                            echo "</tr>";
                            $nTotal_Linhas_Impressas++;
                        }

                        for ($x=$nTotal_Linhas_Impressas;$x<$nItensPorPagina;$x++)
                        {
                            echo "<tr height=13>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center class=caixa_7>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                                 echo "<td align=center>";
                                      echo "<font class=fonte_dados_itens>&nbsp;</font>";
                                 echo "</td>";
                            echo "</tr>";
                        }
                        if ($Pagina_Atual==1)
                        {
                            echo "<tr height=25>";
                               echo "<td align=left valign=bottom class=caixa_10 height=1 colspan=14>";
                                  echo "<font class='fonte_subtitulo'>CÁLCULO DO ISSQN</font>";
                               echo "</td>";
                            echo "</tr>";
                        }
                        echo "</td>";
                   echo "</table>";
              echo "</td>";
         echo "</tr>";

         if ($Pagina_Atual==1)
         {
             echo "<tr>";
                  echo "<td valign=top>";
                       echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                            echo "<tr >";
                                 echo "<td>";
                                      echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";


                                        echo "<tr>";
                                             echo "<td class=caixa_12 height=30 width=25% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>INSCRIÇÃO MUNICIPAL</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=left>";
                                                                  echo "<font class='fonte_dados'>&nbsp;$mT007[T007_Inscricao_Municipal]</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_12 height=30 width=25% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>VALOR TOTAL DOS SERVIÇOS</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Mao_Obra'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_8 height=30 width=25% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>BASE DE CALCULO DO ISSQN</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_Mao_Obra'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                             echo "<td class=caixa_11 height=30 width=25% valign=center align=center>";
                                                   echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                                        echo "<tr >";
                                                             echo "<td valign=top>";
                                                                  echo "<font class='titulo_quadro'>VALOR DO ISSQN</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                        echo "<tr>";
                                                             echo "<td align=right>";
                                                                  echo "<font class='fonte_dados'>".number_format($mT007['T007_Valor_ISS'],2,',','.')."&nbsp;</font>";
                                                             echo "</td>";
                                                        echo "</tr>";
                                                   echo "</table>";
                                             echo "</td>";
                                        echo "</tr>";

                                       echo "</table>";
                                 echo "</td>";
                            echo "</tr>";
                       echo "</table>";
                  echo "</td>";
             echo "</tr>";

             echo "<tr>";
                  echo "<td valign=top height=17>";
                     echo "<font class='fonte_subtitulo'>DADOS ADICIONAIS</font>";
                  echo "</td>";
             echo "</tr>";

             echo "<tr>";
                  echo "<td valign=top>";
                       echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                            echo "<tr height=130 valign=top>";
                                 echo "<td width=50% class=caixa_10>";
                                      echo "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
                                            echo "<tr >";
                                                 echo "<td valign=top>";
                                                      echo "<font class='titulo_quadro'>INFORMAÇÕES COMPLEMENTARES</font>";
                                                 echo "</td>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                 echo "<td align=LEFT>";
                                                 
                                                 if (!empty($mT007['C007_Primeiro_Nome']))
                                                 {
                                                    echo "<font class=fonte_dados>VENDEDOR:</font>";
                                                    echo "<font class=fonte_subtitulo>".$mT007['C007_Primeiro_Nome']."</font>";
                                                    echo "&nbsp;";
                                                 }
                                                 if ($mT007['T007_T005_Id']>0)
                                                 {
                                                     echo "<font class=fonte_dados>PEDIDO:</font>";
                                                     echo "<font class=fonte_subtitulo>".$mT007['T007_T005_Id']."</font>";
                                                     echo "<br>";
                                                 }
                                                 if (!empty($mT007['T007_Observacao_Nota_Fiscal_1']))
                                                 {
                                                     $mT007['T007_Observacao_Nota_Fiscal_1'] = str_replace("\n","<br>",$mT007['T007_Observacao_Nota_Fiscal_1']);
                                                     echo "<BR>";
                                                     echo "<font class=fonte_dados><small><small>".$mT007['T007_Observacao_Nota_Fiscal_1']."</small></small>";
                                                     echo "<BR>";
                                                 }

                                                 if (!empty($mT007['T007_Observacao_Nota_Fiscal_2']))
                                                 {
                                                     $mT007['T007_Observacao_Nota_Fiscal_2'] = str_replace("\n","<br>",$mT007['T007_Observacao_Nota_Fiscal_2']);
                                                     echo "<BR>";
                                                     echo "<font class=fonte_dados><small><small>".$mT007['T007_Observacao_Nota_Fiscal_2']."</small></small>";
                                                     echo "<BR>";
                                                 }


                                                  echo "</td>";
                                           echo "</tr>";
                                       echo "</table>";
                                 echo "</td>";
                                 echo "<td width=50% class=caixa_1>";
                                      echo "<table border=0 cellspacing=0 cellpadding=0 height=100% width=100%>";
                                            echo "<tr >";
                                                 echo "<td valign=top>";
                                                      echo "<font class='titulo_quadro'>RESERVADO AO FISCO</font>";
                                                 echo "</td>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                 echo "<td align=LEFT>";
                                                      echo "<font class='fonte_dados'></font>";
                                                 echo "</td>";
                                            echo "</tr>";
                                       echo "</table>";
                                 echo "</td>";
                            echo "</tr>";
                       echo "</table>";
                  echo "</td>";
             echo "</tr>";
         }
       echo "</table>";

    if ((($nTotal_Linhas_Impressas)>=$nItensPorPagina) and (($Impressao+$nItensPorPagina)<$Total_Linhas))
    {
       $nTotal_Linhas_Impressas=0;
       $Inicio+=$nItensPorPagina;
       if ($Inicio<=$Total_Linhas)
       {
          //-- salto de pagina
          echo "<p style='page-break-after: always'></p>";
       }
    }
}

echo "<script>document.getElementById('Div_Transmitindo').style.display='none';</script>";



function msg_erro_nfe($cErro,$Tentar='N')
{
    global $nT007_Id;
    global $mT007;
    global $nTentativa;
    global $Usuario_Atual;
    
    echo "<script>document.getElementById('Div_Transmitindo').style.display='none';</script>";
    echo "<table width=100% height=100% border=0 bgcolor=#0000FF >";
         echo "<tr>";
              echo "<td align=center>";
                  echo "<table border=0 bgcolor=#FF0000>";
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
                                     

                                     if ($Tentar=='S')
                                     {
                                         echo "<tr height=50 valign=bottom>";
                                              echo "<td align=center>";
                                                 echo "<font style='font-size:12px;font-family:arial,verdana;color:white;font-weight:bold;'>";
                                                   echo "<big>$nTentativa &ordm; Tentativa</big>";
                                                 echo "</font>";
                                              echo "</td>";
                                         echo "</tr>";
                                         echo "<tr height=50 valign=bottom>";
                                              echo "<td align=center>";
                                              echo "<input type='button' value='Tentar novamente' style='width:150;' onclick=\"location.replace('gerar_danfe.php?T007_Id=$nT007_Id&Tentativa=$nTentativa');\";";
                                              echo "</td>";
                                         echo "</tr>";
                                         echo "<tr height=20 valign=bottom>";
                                              echo "<td align=center>";
                                              echo "</td>";
                                         echo "</tr>";

                                         if ($nTentativa>3 && (empty($mT007['T007_Numero_Recibo_Nfe']) or $mT007['T007_Flag_Contingencia']=='S'))
                                         {
                                            
                                         }
                                      }
                                 echo "</table>";

                              echo "</fieldset>";
                          echo "</td>";
                     echo "</tr>";
                echo "</table>";
             echo "</td>";
         echo "</tr>";
    echo "</table>";
}

mysqli_query("update T007 set T007_Flag_Impresso='S' where T007_Id='{$mT007['T007_Id']}'");



?>
