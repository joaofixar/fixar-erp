<?php
namespace hardness;

/* //-- Cria ou inicia uma sess�o
require "../../../include/sessao.inc";

//-- Fun��es Gen�ricas em PHP
require "../../../func/funcoes_genericas.inc";

//-- Conex�o com o banco de dados
require "../../../include/conexao.inc";

//-- gravar entidade
require "../../../func/gravar_entidade.php3";

//--  Cria o historico de navegacao
include "../../../include/voltar.inc";
 */


// Busca os dados da empresa usu�ria
$cSql=mysqli_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
$mC004=mysqli_fetch_array($cSql);

$Data_Inicial  = $_REQUEST['Data_Inicial'];
$Data_Final    = $_REQUEST['Data_Final'];
$Representante = $_REQUEST['Representante'];
$T007_Flag_ACP = $_REQUEST['T007_Flag_ACP'];
$Data_Inicial  = gCorrigeDataInverte($Data_Inicial);
$Data_Final    = gCorrigeDataInverte($Data_Final);

?>

<html>
<head>
<title>Relatório Comissão</title>
<meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  <meta http-equiv='Pragma' content='no-cache'>  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
<link rel=stylesheet href="../../../classes/classe_tamanho_medio_firefox.css" type="text/css">
<!-- Fun��es Gen�ricas em Javascript -->
<script src='../../../func/funcoes_genericas.js'></script>
<script src='../../../func/ajax.js'></script>

<STYLE TYPE="text/css"><!--
  A:link {text-decoration:none; color:000000}
  A:visited {text-decoration:none; color:000000}
  A:hover {text-decoration:underline; color:000000; cursor:hand}
  A:active {text-decoration:underline; color:000000; cursor:hand}

  .fonte{
    font-size: 12;
    font-family: arial, sans-serif, verdana, helvetica, sans-serif;
    color:black;
  }

  .fontemed{
    font-size: 11;
    font-family: arial, sans-serif, verdana, helvetica, sans-serif;
    color:black;
  }

  .titulo_relatorio {
    font-size: 18;
    font-family: arial, sans-serif, verdana, helvetica;
    font-weight: bold;
    color:black;
  }

</style>
</head>
<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>

<?php

if ($Representante>0) {
   $Filtro_Representante="where D021_Id='$Representante' ";
}
if ($T007_Flag_ACP>0) {
   $Filtro_Flag_ACP="and T007_Flag_ACP='$T007_Flag_ACP' ";
}

 $D021=mysqli_query("select D021_Nome_Empresa,
                           D021_Id,
                           D021_Agencia,
                           D021_Nome_Conta,
                           D021_Conta_Bancaria,
                           D062_Numero_Banco,
                           D062_Nome_Banco
                      from D021
                 left join D062 on D062_Id=D021_D062_Id
                     $Filtro_Representante");
                  
echo mysqli_error();

while ($mD021=mysqli_fetch_array($D021)) {

    $T007=mysqli_query("select T007_Numero_Nota_Fiscal,
                              T007_Data_Emissao,
                              (T007_Valor_Total_Produtos_Sem_Desconto-IFNULL(T007_Valor_Desconto,0)) T007_Valor_Total,
                              D024_Nome_Empresa,
                              T007_Percentual_Comissao_Representante,
                              (T007_Valor_Total_Produtos_Sem_Desconto-T007_Valor_Desconto)*(T007_Percentual_Comissao_Representante/100) Valor_Comissao,
                              T007_Valor_Comissao_Representante as T007_Valor_Total_Comissao,
                              if(T007_Flag_ACP='1','NF',if(T007_Flag_ACP='3','ACP',if(T007_Flag_ACP='5','D1',if(T007_Flag_ACP='6','NFe','CF')))) Tipo_Documento
                         from D021
                    left join T007 on T007_D021_Id=D021_Id
                    left join D024 on T007_D024_Id=D024_Id
                    left join D006 on T007_D006_Id=D006_Id
                        where T007_Data_Emissao>='$Data_Inicial'
                          and T007_Data_Emissao<='$Data_Final'
                          and D006_Descricao like '%VENDA%'
                          and T007_Flag_Cancelada!='S'
                          $Filtro_Flag_ACP
                          and T007_D021_Id='$mD021[D021_Id]'");

    echo mysqli_error();

    $T002=mysqli_query("select T002_Numero_Duplicata,
                              T002_Data_Vencimento,
                              T002_Valor_Total,
                              D024_Nome_Empresa,
                              T007_Percentual_Comissao_Representante
                         from D021
                    left join T002 on T002_D021_Id=D021_Id
                    left join D024 on T002_D024_Id=D024_Id
                    left join T007 on T002_T007_Id=T002_Id
                    left join D006 on T007_D006_Id=D006_Id
                        where T002_Data_Vencimento>='$Data_Inicial'
                          and T002_Data_Vencimento<='$Data_Final'
                          and D006_Descricao like '%VENDA%'
                          and T002_Data_Recebimento='0000-00-00'
                          $Filtro_Flag_ACP
                          and T002_D021_Id='$mD021[D021_Id]'");

    if (mysqli_num_rows($T007)<=0) {
        continue;
    }

    //
    // CABE�ALHO
    //

    echo "<table align=center border='0' valign=top cellpadding=3 cellspacing=0 bgcolor=white width='$width' style='border-bottom:1px dashed black'>";
     echo "<tr>";
      echo "<td valign=top>";
       echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
        echo "<tr>";
         echo "<td bgcolor=white width=80%>";
          echo "<table cellspacing=0 cellpading=0 border='0' align=center width=100% height=70>";
           echo "<tr>";
            $Arquivo_Logo="../../../empresas/E003/imagens/logo.jpg";
            echo "<td bgcolor=white align=center valign=center width=220>
                              <img src='$Arquivo_Logo' align=center><br><font class=fonte>$mC004[C004_Observacao_Logo]</font>
                           </td>";
            echo "<td align=center>";
             echo "<table cellspacing=0 cellpading=0 border='0' align=center height=70>";
              echo "<tr><th><font class=fontemed>$mC004[C004_Nome_Empresa]</font></th></tr>";
              echo "<tr><td align=center><font class=fontemed>$mC004[C004_Logradouro], $mC004[C004_Numero] $mC004[C004_Complemento]</font></td></tr>";
              echo "<tr><td align=center><font class=fontemed>$mC004[C004_Cep] - $mC004[C004_Cidade] - $mC004[C004_Estado]</font></td></tr>";
              echo "<tr><td align=center><font class=fontemed>Fone/Fax: $mC004[C004_Telefone] Fax: $mC004[C004_Fax]</font></td></tr>";
              echo "<tr><td align=center><font class=fontemed>$mC004[C004_Site]</font></td></tr>";
             echo "</table>";
            echo "</td>";
           echo "</tr>";
          echo "</table>";
         echo "</td>";
         echo "<td bgcolor=white align=center width=20% rowspan=2>";
           echo "<font class='titulo_relatorio'>Relatorio Comissão</font><font class=fonte><br><b></b>";
           echo "</font><br>";
           if ($mC004['C004_Flag_Empresa_Simples']=='1') {
               echo "<br><table border=0 cellpadding=5 style='border:1px dotted black' bgcolor=#f4f4f4><tr><td align=center><font class=fonte style='font-size:12px;color:red'>Empresa Participante do<br><b>SUPER SIMPLES</b></td></tr></table>";
           }
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor=white>";
         echo "<td colspan=2>";
           echo "<font class=fontemed>Impresso</b> &nbsp;<b>".gCorrigeData(date('Y-m-d H:i:s'))."</b></font>";
           echo "</td>";
         echo "</td>";
       echo "</tr>";
     echo "</table>";
    echo "</td>";
   echo "</tr>";

   echo "<tr>";
     echo "<td>";
       echo "<font class=fonte>Representante: <b><big>$mD021[D021_Nome_Empresa]</big></b><BR><i>Nome: $mD021[D021_Nome_Conta]<BR>Agencia: $mD021[D021_Agencia]<BR>Conta: $mD021[D021_Conta_Bancaria]<BR>Banco: $mD021[D062_Numero_Banco]-$mD021[D062_Nome_Banco]</i><BR></font>";
     echo "</td>";
   echo "</tr>";
   echo "<tr>";
     echo "<td>";
       echo "<font class=fonte>Data inicial: <b>".gCorrigeData($Data_Inicial)."</b></font> &nbsp;";
       echo "<font class=fonte>Data Final: <b>".gCorrigeData($Data_Final)."</b></font>";
     echo "</td>";
   echo "</tr>";

   //
   // CABECALHO DAS COLUNAS
   //
  
   echo "<tr>";
    echo "<td>";

     echo "<table align=center border='0' cellpadding=2 cellspacing=1 width='100%' height='100%'>";

      echo "<tr bgcolor='lightblue'>";
       echo "<td align=center colspan=10>";
        echo "<font class=fontemed>Faturamento</font>";
       echo "</td>";
      echo "</tr>";
      echo "<tr bgcolor='lightblue'>";
       echo "<td align=center>";
        echo "<font class=fontemed>Emissão</font>";
       echo "</td>";
       echo "<td align=center>";
        echo "<font class=fontemed>NF</font>";
       echo "</td>";
       echo "<td align=center>";
        echo "<font class=fontemed>Tp</font>";
       echo "</td>";
       echo "<td align=left>";
        echo "<font class=fontemed>Cliente</font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed>Valor Total</font>";
       echo "</td>";
       echo "<td align=center>";
        echo "<font class=fontemed>% Comissão</font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed>Valor Comissão</font>";
       echo "</td>";
      echo "</tr>";
      
    $Total_Notas=0;
    $Total_Comissao=0;
    
    while ($mT007=mysqli_fetch_array($T007)) {
          
          echo "<tr bgcolor='#f4f4f4'>";
           echo "<td align=center>";
            echo "<font class=fontemed>".gCorrigeData($mT007['T007_Data_Emissao'])."</font>";
           echo "</td>";
           echo "<td align=center>";
            echo "<font class=fontemed>$mT007[T007_Numero_Nota_Fiscal]</font>";
           echo "</td>";
           echo "<td align=center>";
            echo "<font class=fontemed>$mT007[Tipo_Documento]</font>";
           echo "</td>";
           echo "<td align=left>";
            echo "<font class=fontemed>$mT007[D024_Nome_Empresa]</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fontemed>".number_format($mT007['T007_Valor_Total'],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fontemed>".number_format($mT007['T007_Percentual_Comissao_Representante'],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fontemed>".number_format($mT007['T007_Valor_Total_Comissao'],2,',','.')."</font>";
           echo "</td>";
          echo "</tr>";
          
          $Total_Notas+=$mT007['T007_Valor_Total'];
          $Total_Comissao+=$mT007['T007_Valor_Total_Comissao'];

     }

      echo "<tr bgcolor='#f4f4f4'>";
       echo "<td align=center colspan=4>";
        echo "<font class=fontemed><b>VALOR TOTAL</b></font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed><b>".number_format($Total_Notas,2,',','.')."</b></font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed>&nbsp;</font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed><b>".number_format($Total_Comissao,2,',','.')."</b></font>";
       echo "</td>";
      echo "</tr>";

    echo "</table>";

    echo "<td>";
   echo "<tr>";

/*
   echo "<tr>";
    echo "<td>";

     echo "<table align=center border='0' cellpadding=2 cellspacing=1 width='100%' height='100%'>";

      echo "<tr bgcolor='lightblue'>";
       echo "<td align=center colspan=10>";
        echo "<font class=fontemed>Duplicatas</font>";
       echo "</td>";
      echo "</tr>";
      echo "<tr bgcolor='lightblue'>";
       echo "<td align=center>";
        echo "<font class=fontemed>Duplicata</font>";
       echo "</td>";
       echo "<td align=left>";
        echo "<font class=fontemed>Cliente</font>";
       echo "</td>";
       echo "<td align=center>";
        echo "<font class=fontemed>Emiss�o</font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed>Valor Total</font>";
       echo "</td>";
       echo "<td align=center>";
        echo "<font class=fontemed>% Comiss�o</font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed>Valor Comiss�o</font>";
       echo "</td>";
      echo "</tr>";

    $Total_Notas=0;
    $Total_Comissao=0;

    while ($mT002=mysqli_fetch_array($T002)) {

          echo "<tr bgcolor='#f4f4f4'>";
           echo "<td align=center>";
            echo "<font class=fontemed>$mT002[T002_Numero_Duplicatas]</font>";
           echo "</td>";
           echo "<td align=left>";
            echo "<font class=fontemed>$mT002[D024_Nome_Empresa]</font>";
           echo "</td>";
           echo "<td align=center>";
            echo "<font class=fontemed>".gCorrigeDataInverte($mT002[T002_Data_Vencimento])."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fontemed>".number_format($mT002[T002_Valor_Total],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fontemed>".number_format($mT002[T007_Percentual_Comissao_Representante],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fontemed>".number_format($mT002[Valor_Comissao],2,',','.')."</font>";
           echo "</td>";
          echo "</tr>";

          $Total_Notas+=$mT007[T002_Valor_Total];
          $Total_Comissao+=$mT007[Valor_Comissao];

     }

      echo "<tr bgcolor='#f4f4f4'>";
       echo "<td align=center colspan=3>";
        echo "<font class=fontemed><b>VALOR TOTAL</b></font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed><b>".number_format($Total_Notas,2,',','.')."</b></font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed>&nbsp;</font>";
       echo "</td>";
       echo "<td align=right>";
        echo "<font class=fontemed><b>".number_format($Total_Comissao,2,',','.')."</b></font>";
       echo "</td>";
      echo "</tr>";

    echo "</table>";

    echo "<td>";
   echo "<tr>";
*/

 echo "</table>";
 
}
 
?>
