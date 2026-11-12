<?php
namespace hardness;

$Empresa = $_REQUEST['Empresa'];

// Busca os dados da empresa usu?ia
if($Empresa <= 0){
    $cSql=mysql_query("select * from C004 where C004_Id='1'");
} else {
    $cSql=mysql_query("select * from C004 where C004_Id='{$Empresa}'");
}
$mC004=mysql_fetch_array($cSql);

set_time_limit(0);

?>

<html>
<head>
<title>Mapa Venda</title>
 
<meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  <meta http-equiv='Pragma' content='no-cache'>  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
<link rel=stylesheet href="/hardness/classes/classe_tamanho_medio_firefox.css" type="text/css">
<!-- Funções Genéricas em Javascript -->
<script src='/hardness/func/funcoes_genericas.js'></script>
<script src='/hardness/func/ajax.js'></script>

<STYLE TYPE="text/css"><!--
  A:link {text-decoration:none; color:000000}
  A:visited {text-decoration:none; color:000000}
  A:hover {text-decoration:underline; color:000000; cursor:hand}
  A:active {text-decoration:underline; color:000000; cursor:hand}

-->

.fonte
{
 font-size:12px;
}
</style>
</head>
<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>

<?php

if (empty($Data)) {
   $Data=date("Y-m-d");
}

$xData=$Data;

$Data=gCorrigeDataInverte($Data);
$Data_Inicial=gCorrigeDataInverte($_REQUEST['Data_Inicial']);
$Data_Final=gCorrigeDataInverte($_REQUEST['Data_Final']);
$Expedicao = $_REQUEST['Expedicao'];
$Modelo = $_REQUEST['Modelo'];
$T007_C007_Id_Vendedor_Interno = $_REQUEST['T007_C007_Id_Vendedor_Interno'];

if ($Expedicao > 0) {
    $filtro_expedicao="and T007_Flag_Expedicao='$Expedicao'";
}
if ($T007_C007_Id_Vendedor_Interno>0) {
    $C007=mysql_query("select C007_Id,C007_Nome from C007 where C007_Id='$T007_C007_Id_Vendedor_Interno'");
    $mC007=mysql_fetch_array($C007);
    $filtro_vendedor="and T007_C007_Id_Vendedor_Interno='$T007_C007_Id_Vendedor_Interno'";
}
  //
  // CABE?LHO
  //

  echo "<table align=center border='0' valign=top cellpadding=3 cellspacing=0 bgcolor=white width='800' style='border-bottom:1px dashed black'>";
   echo "<tr>";
     echo "<td valign=top colspan=2>";

       echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
        echo "<tr>";
         echo "<td bgcolor=white width=80%>";

            //-- $cArquivo_Cabecalho_Relatorio é definido no arquivo include/pasta_usuario.inc
            //include $cArquivo_Cabecalho_Relatorio;
            $Diretorio_Imagens="/hardness/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/";
            //include $g['pathDadosAntigo']."/cabecalho_relatorio.php";

         echo "</td>";
         echo "<td bgcolor=white align=center width=20% rowspan=2>";
           echo "<font class='titulo_relatorio'>Mapa Vendas</font><font class=fonte><br><b></b>";
           echo "</font><br>";
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor=white>";
         echo "<td colspan=2>";
           echo "<font class=fontemed>Impresso</b> &nbsp;<b>".date('d/m/Y H:i:s')."</b></font>";
           echo "</td>";
         echo "</td>";
       echo "</tr>";
     echo "</table>";

    echo "</td>";
   echo "</tr>";
   echo "<tr>";
    echo "<td colspan=2>";

     echo "<table>";
      if ($Empresa > 0) {
          $filtro_empresa="and T007_C004_Id='{$Empresa}'";

          echo "<tr>";
            echo "<td>";
              echo "<font class=fonte>Empresa:</font>";
            echo "</td>";
            echo "<td colspan=10>";
              echo "<font class=fonte><b>$mC004[C004_Nome_Empresa]</b></font>";
            echo "</td>";

      } else {
          echo "<tr>";
            echo "<td>";
              echo "<font class=fonte>Empresa:</font>";
            echo "</td>";
            echo "<td colspan=10>";
              echo "<font class=fonte><b>TODAS</b></font>";
            echo "</td>";
          echo "</tr>";

      }
      if ($T007_C007_Id_Vendedor_Interno>0) {
          echo "<tr>";
            echo "<td>";
              echo "<font class=fonte>Vendedor:</font>";
            echo "</td>";
            echo "<td colspan=10>";
              echo "<font class=fonte><b>$mC007[C007_Nome]</b></font>";
            echo "</td>";
      }
      echo "<tr>";
        echo "<td colspan=2>";
          echo "<font class=fonte>Data inicial:</font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fonte><b>".gCorrigeData($Data_Inicial)."</b></font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fonte>Data Final:</font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fonte><b>".gCorrigeData($Data_Final)."</b></font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fonte>Expedição:</font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fonte><b>";
          if($Tipo=='1') {
             echo 'COMERCIAL';
          } else if ($Tipo=='2') {
             echo 'BALC?';
          } else {
             echo 'TODOS';
          }
          echo "</b></font>";
        echo "</td>";
      echo "</tr>";
     echo "</table>";

    echo "</td>";
   echo "</tr>";

   echo "<tr>";

     if ($Modelo=='1') {

        echo "<td valign=top>";

        $T007=mysql_query("select T007_Data_Emissao,
                                  sum(T007_Valor_Total) Valor_Total,
                                  count(*) Notas,
                                  (select count(DISTINCT T1.T007_D024_Id)
                                     from T007 as T1
                                left join D006 on D006_Id=T007_D006_Id
                                    where D006_Flag_Venda_Compra_Outros='V'
                                      and D006_Flag_Devolucao='N'
                                      and T1.T007_Flag_Cancelada!='S'
                                      $filtro_expedicao
                                      $filtro_vendedor
                                      $filtro_empresa
                                      and T1.T007_Data_Emissao=T007.T007_Data_Emissao) Clientes,
                                  (select count(DISTINCT T008_D009_Id)
                                     from T007 as T1
                                left join D006 on D006_Id=T007_D006_Id
                                left join T008 on T008_T007_Id=T007_Id
                                    where D006_Flag_Venda_Compra_Outros='V'
                                      and D006_Flag_Devolucao='N'
                                      and T1.T007_Flag_Cancelada!='S'
                                      $filtro_expedicao
                                      $filtro_vendedor
                                      $filtro_empresa
                                      and T1.T007_Data_Emissao=T007.T007_Data_Emissao) Itens
                             from T007
                        left join D006 on D006_Id=T007_D006_Id
                            where D006_Flag_Venda_Compra_Outros='V'
                              and D006_Flag_Devolucao='N'
                              and T007_Flag_Cancelada!='S'
                              and T007_Data_Emissao>='$Data_Inicial'
                              and T007_Data_Emissao<='$Data_Final'
                              $filtro_expedicao
                              $filtro_vendedor
                              $filtro_empresa
                         group by T007_Data_Emissao
                         order by T007_Data_Emissao");

         echo mysql_error();

         echo "<table cellpadding=2 width=350>";
           echo "<tr bgcolor='lightblue'>";
             echo "<td align=center colspan=5>";
               echo "<font class=fonte>FATURAMENTO POR DIA</font>";
             echo "</td>";
           echo "</tr>";
           echo "<tr bgcolor='lightblue'>";
             echo "<td align=center>";
               echo "<font class=fonte>Emissão</font>";
             echo "</td>";
             echo "<td align=center>";
               echo "<font class=fonte>Notas<br>emitidas</font>";
             echo "</td>";
             echo "<td align=center>";
               echo "<font class=fonte>Clientes<br>atendidos</font>";
             echo "</td>";
             echo "<td align=center>";
               echo "<font class=fonte>Produtos<br>vendidos</font>";
             echo "</td>";
             echo "<td align=right>";
               echo "<font class=fonte>Valor<br>total</font>";
             echo "</td>";
           echo "</tr>";

           unset($mTotal);

           while ($mT007=mysql_fetch_array($T007)) {

                 echo "<tr bgcolor='#f4f4f4'>";
                   echo "<td align=center>";
                     echo "<font class=fonte>".gCorrigeData($mT007[T007_Data_Emissao])."</font>";
                   echo "</td>";
                   echo "<td align=center>";
                     echo "<font class=fonte>".number_format($mT007[Notas],0,',','.')."</font>";
                   echo "</td>";
                   echo "<td align=center>";
                     echo "<font class=fonte>".number_format($mT007[Clientes],0,',','.')."</font>";
                   echo "</td>";
                   echo "<td align=center>";
                     echo "<font class=fonte>".number_format($mT007[Itens],0,',','.')."</font>";
                   echo "</td>";
                   echo "<td align=right>";
                     echo "<font class=fonte>".number_format($mT007[Valor_Total],2,',','.')."</font>";
                   echo "</td>";
                 echo "</tr>";

                 $mTotal[Notas]      += $mT007[Notas];
                 $mTotal[Clientes]   += $mT007[Clientes];
                 $mTotal[Itens]      += $mT007[Itens];
                 $mTotal[Valor_Total]+= $mT007[Valor_Total];

           }

             echo "<tr bgcolor='#f4f4f4'>";
               echo "<td align=center>";
                 echo "<font class=fonte><b>Total</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte><b>".number_format($mTotal[Notas],0,',','.')."</b></font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte><b></b></font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte><b></b></font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
               echo "</td>";
             echo "</tr>";

         echo "</table>";

        echo "</td>";

     } else {

        echo "<td valign=top>";

        $T007=mysql_query("select month(T007_Data_Emissao) Mes,
                                  year(T007_Data_Emissao) Ano,
                                  sum(T007_Valor_Total) Valor_Total,
                                  count(*) Notas,
                                  (select count(DISTINCT T1.T007_D024_Id)
                                     from T007 as T1
                                left join D006 on D006_Id=T007_D006_Id
                                    where D006_Flag_Venda_Compra_Outros='V'
                                      and D006_Flag_Devolucao='N'
                                      and T1.T007_Flag_Cancelada!='S'
                                      $filtro_expedicao
                                      $filtro_vendedor
                                      $filtro_empresa
                                      and T1.T007_Data_Emissao=T007.T007_Data_Emissao) Clientes,
                                  (select count(DISTINCT T008_D009_Id)
                                     from T007 as T1
                                left join D006 on D006_Id=T007_D006_Id
                                left join T008 on T008_T007_Id=T007_Id
                                    where D006_Flag_Venda_Compra_Outros='V'
                                      and D006_Flag_Devolucao='N'
                                      and T1.T007_Flag_Cancelada!='S'
                                      $filtro_expedicao
                                      $filtro_vendedor
                                      $filtro_empresa
                                      and T1.T007_Flag_Cancelada!='S'
                                      and T1.T007_Data_Emissao=T007.T007_Data_Emissao) Itens
                             from T007
                        left join D006 on D006_Id=T007_D006_Id
                            where D006_Flag_Venda_Compra_Outros='V'
                              and D006_Flag_Devolucao='N'
                              and T007_Flag_Cancelada!='S'
                              and T007_Data_Emissao>='$Data_Inicial'
                              and T007_Data_Emissao<='$Data_Final'
                              $filtro_expedicao
                                      $filtro_vendedor
                              $filtro_empresa
                         group by year(T007_Data_Emissao),month(T007_Data_Emissao)
                         order by year(T007_Data_Emissao),month(T007_Data_Emissao)");

         echo mysql_error();

         echo "<table cellpadding=2 width=350>";
           echo "<tr bgcolor='lightblue'>";
             echo "<td align=center colspan=5>";
               echo "<font class=fonte>FATURAMENTO POR MÊS</font>";
             echo "</td>";
           echo "</tr>";
           echo "<tr bgcolor='lightblue'>";
             echo "<td align=center>";
               echo "<font class=fonte>Mês</font>";
             echo "</td>";
             echo "<td align=center>";
               echo "<font class=fonte>Notas<br>emitidas</font>";
             echo "</td>";
             echo "<td align=center>";
               echo "<font class=fonte>Clientes<br>atendidos</font>";
             echo "</td>";
             echo "<td align=center>";
               echo "<font class=fonte>Produtos<br>vendidos</font>";
             echo "</td>";
             echo "<td align=right>";
               echo "<font class=fonte>Valor<br>total</font>";
             echo "</td>";
           echo "</tr>";

           unset($mTotal);

           while ($mT007=mysql_fetch_array($T007)) {

                 echo "<tr bgcolor='#f4f4f4'>";
                   echo "<td align=center>";
                     echo "<font class=fonte>".str_pad($mT007[Mes],2,'0',STR_PAD_LEFT)."/$mT007[Ano]</font>";
                   echo "</td>";
                   echo "<td align=center>";
                     echo "<font class=fonte>".number_format($mT007[Notas],0,',','.')."</font>";
                   echo "</td>";
                   echo "<td align=center>";
                     echo "<font class=fonte>".number_format($mT007[Clientes],0,',','.')."</font>";
                   echo "</td>";
                   echo "<td align=center>";
                     echo "<font class=fonte>".number_format($mT007[Itens],0,',','.')."</font>";
                   echo "</td>";
                   echo "<td align=right>";
                     echo "<font class=fonte>".number_format($mT007[Valor_Total],2,',','.')."</font>";
                   echo "</td>";
                 echo "</tr>";

                 $mTotal[Notas]      += $mT007[Notas];
                 $mTotal[Clientes]   += $mT007[Clientes];
                 $mTotal[Itens]      += $mT007[Itens];
                 $mTotal[Valor_Total]+= $mT007[Valor_Total];

           }

             echo "<tr bgcolor='#f4f4f4'>";
               echo "<td align=center>";
                 echo "<font class=fonte><b>Total</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte><b>".number_format($mTotal[Notas],0,',','.')."</b></font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte><b></b></font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte><b></b></font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
               echo "</td>";
             echo "</tr>";

         echo "</table>";

        echo "</td>";

    }
    
//   echo "</tr>";

    $Total_Mapa=$mTotal[Valor_Total];

//   echo "<tr>";
    echo "<td valign=top>";

    $T007=mysql_query("select If(T007_Flag_Expedicao='1','COMERCIAL',if(T007_Flag_Expedicao='2','BALCAO',T007_Flag_Expedicao)) Expedicao,
                              sum(T007_Valor_Total) Valor_Total,
                              count(*) Notas,
                              (select count(DISTINCT T1.T007_D024_Id)
                                 from T007 as T1
                            left join D006 on D006_Id=T007_D006_Id
                            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                where T1.T007_Data_Emissao>='$Data_Inicial'
                                  and T1.T007_Data_Emissao<='$Data_Final'
                                  and T1.T007_Flag_Expedicao=T007.T007_Flag_Expedicao
                                  and D006_Flag_Venda_Compra_Outros='V'
                                  and D006_Flag_Devolucao='N'
                                  and T1.T007_Flag_Cancelada!='S'
                                  $filtro_expedicao
                                  $filtro_vendedor
                                  $filtro_empresa) Clientes,
                              (select count(DISTINCT T008_D009_Id)
                                 from T007 as T1
                            left join D006 on D006_Id=T007_D006_Id
                            left join T008 on T008_T007_Id=T007_Id
                                where T1.T007_Data_Emissao>='$Data_Inicial'
                                  and T1.T007_Data_Emissao<='$Data_Final'
                                  and T1.T007_Flag_Expedicao=T007.T007_Flag_Expedicao
                                  and D006_Flag_Venda_Compra_Outros='V'
                                  and D006_Flag_Devolucao='N'
                                  and T1.T007_Flag_Cancelada!='S'
                                  $filtro_expedicao
                                  $filtro_vendedor
                                  $filtro_empresa) Itens
                         from T007
                    left join D006 on D006_Id=T007_D006_Id
                    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                        where D006_Flag_Venda_Compra_Outros='V'
                          and D006_Flag_Devolucao='N'
                          and T007_Flag_Cancelada!='S'
                          and T007_Data_Emissao>='$Data_Inicial'
                          and T007_Data_Emissao<='$Data_Final'
                          $filtro_expedicao
                          $filtro_vendedor
                          $filtro_empresa
                     group by T007_Flag_Expedicao
                     order by sum(T007_Valor_Total) desc");

     echo mysql_error();

     echo "<table cellpadding=2 width=350>";
       echo "<tr bgcolor='lightblue'>";
         echo "<td align=center colspan=6>";
           echo "<font class=fonte>FATURAMENTO POR EXPEDIÇÃO</font>";
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor='lightblue'>";
         echo "<td align=center>";
           echo "<font class=fonte>Expedição</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Notas<br>emitidas</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Clientes<br>atendidos</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Produtos<br>vendidos</font>";
         echo "</td>";
         echo "<td align=right>";
           echo "<font class=fonte>Valor<br>total</font>";
         echo "</td>";
         echo "<td align=right>";
           echo "<font class=fonte>&nbsp;</font>";
         echo "</td>";
       echo "</tr>";

       unset($mTotal);

       while ($mT007=mysql_fetch_array($T007)) {

             echo "<tr bgcolor='#f4f4f4'>";
               echo "<td align=center>";
                 echo "<font class=fonte>$mT007[Expedicao]</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Notas],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Clientes],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Itens],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte>".number_format($mT007[Valor_Total],2,',','.')."</font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte>".number_format(($mT007[Valor_Total]/$Total_Mapa)*100,2,',','.')."%</font>";
               echo "</td>";
             echo "</tr>";

             $mTotal[Notas]      += $mT007[Notas];
             $mTotal[Clientes]   += $mT007[Clientes];
             $mTotal[Itens]      += $mT007[Itens];
             $mTotal[Valor_Total]+= $mT007[Valor_Total];

       }

         echo "<tr bgcolor='#f4f4f4'>";
           echo "<td align=center>";
             echo "<font class=fonte><b>Total</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b>".number_format($mTotal[Notas],0,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class=fonte><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
         echo "</tr>";

     echo "</table>";

     echo "<br>";
     
    $T007=mysql_query("select C021_Nome_Perfil,
                              sum(T007_Valor_Total) Valor_Total,
                              count(*) Notas,
                              (select count(DISTINCT T1.T007_D024_Id)
                                 from T007 as T1
                            left join D006 on D006_Id=T007_D006_Id
                            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                            left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='{$Empresa}'
                            left join C021 as T2 on C021_Id=C019_C021_Id 
                            where T1.T007_Data_Emissao>='$Data_Inicial'
                              and T1.T007_Data_Emissao<='$Data_Final'
                              and T2.C021_Id=C021.C021_Id
                              and D006_Flag_Venda_Compra_Outros='V'
                              and D006_Flag_Devolucao='N'
                              and T1.T007_Flag_Cancelada!='S'
                              $filtro_expedicao
                              $filtro_vendedor
                              $filtro_empresa) Clientes,
                              (select count(DISTINCT T008_D009_Id)
                                 from T007 as T1
                            left join D006 on D006_Id=T007_D006_Id
                            left join T008 on T008_T007_Id=T007_Id
                            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                            left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='{$Empresa}'
                            left join C021 as T2 on C021_Id=C019_C021_Id 
                            where T1.T007_Data_Emissao>='$Data_Inicial'
                              and T1.T007_Data_Emissao<='$Data_Final'
                              and T2.C021_Id=C021.C021_Id
                              and D006_Flag_Venda_Compra_Outros='V'
                              and D006_Flag_Devolucao='N'
                              and T1.T007_Flag_Cancelada!='S'
                              $filtro_expedicao
                              $filtro_vendedor
                              $filtro_empresa) Itens
                         from T007
                    left join D006 on D006_Id=T007_D006_Id
                    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                    left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='{$Empresa}'
                    left join C021 on C021_Id=C019_C021_Id 
                    where D006_Flag_Venda_Compra_Outros='V'
                          and D006_Flag_Devolucao='N'
                          and T007_Flag_Cancelada!='S'
                          and T007_Data_Emissao>='$Data_Inicial'
                          and T007_Data_Emissao<='$Data_Final'
                          $filtro_expedicao
                          $filtro_vendedor
                          $filtro_empresa
                     group by C021_Id
                     order by sum(T007_Valor_Total) desc");

     echo mysql_error();

     echo "<table cellpadding=2 width=350>";
       echo "<tr bgcolor='lightblue'>";
         echo "<td align=center colspan=6>";
           echo "<font class=fonte>FATURAMENTO POR PERFIL</font>";
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor='lightblue'>";
         echo "<td align=center>";
           echo "<font class=fonte>Perfil</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Notas<br>emitidas</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Clientes<br>atendidos</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Produtos<br>vendidos</font>";
         echo "</td>";
         echo "<td align=right>";
           echo "<font class=fonte>Valor<br>total</font>";
         echo "</td>";
         echo "<td align=right>";
           echo "<font class=fonte>&nbsp;</font>";
         echo "</td>";
       echo "</tr>";

       unset($mTotal);

       while ($mT007=mysql_fetch_array($T007)) {

             echo "<tr bgcolor='#f4f4f4'>";
               echo "<td align=center>";
                 echo "<font class=fonte>$mT007[C021_Nome_Perfil]</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Notas],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Clientes],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Itens],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte>".number_format($mT007[Valor_Total],2,',','.')."</font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte>".number_format(($mT007[Valor_Total]/$Total_Mapa)*100,2,',','.')."%</font>";
               echo "</td>";
             echo "</tr>";

             $mTotal[Notas]      += $mT007[Notas];
             $mTotal[Clientes]   += $mT007[Clientes];
             $mTotal[Itens]      += $mT007[Itens];
             $mTotal[Valor_Total]+= $mT007[Valor_Total];

       }

         echo "<tr bgcolor='#f4f4f4'>";
           echo "<td align=center>";
             echo "<font class=fonte><b>Total</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b>".number_format($mTotal[Notas],0,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class=fonte><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
         echo "</tr>";

     echo "</table>";
     
    echo "</td>";
   echo "</tr>";

   echo "<tr>";
    echo "<td valign=top>";

    $T007=mysql_query("select C007_Primeiro_Nome,
                              sum(T007_Valor_Total) Valor_Total,
                              count(*) Notas,
                              (select count(DISTINCT T1.T007_D024_Id)
                                 from T007 as T1
                            left join D006 on D006_Id=T007_D006_Id
                            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                where T1.T007_Data_Emissao>='$Data_Inicial'
                                  and T1.T007_Data_Emissao<='$Data_Final'
                                  and T1.T007_C007_Id_Vendedor_Interno=T007.T007_C007_Id_Vendedor_Interno
                                  and D006_Flag_Venda_Compra_Outros='V'
                                  and D006_Flag_Devolucao='N'
                                  and T1.T007_Flag_Cancelada!='S'
                                  $filtro_expedicao
                                  $filtro_vendedor
                                  $filtro_empresa) Clientes,
                              (select count(DISTINCT T008_D009_Id)
                                 from T007 as T1
                            left join D006 on D006_Id=T007_D006_Id
                            left join T008 on T008_T007_Id=T007_Id
                                where T1.T007_Data_Emissao>='$Data_Inicial'
                                  and T1.T007_Data_Emissao<='$Data_Final'
                                  and T1.T007_C007_Id_Vendedor_Interno=T007.T007_C007_Id_Vendedor_Interno
                                  and D006_Flag_Venda_Compra_Outros='V'
                                  and D006_Flag_Devolucao='N'
                                  and T1.T007_Flag_Cancelada!='S'
                                  $filtro_expedicao
                                  $filtro_vendedor
                                  $filtro_empresa) Itens
                         from T007
                    left join D006 on D006_Id=T007_D006_Id
                    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                        where D006_Flag_Venda_Compra_Outros='V'
                          and D006_Flag_Devolucao='N'
                          and T007_Flag_Cancelada!='S'
                          and T007_Data_Emissao>='$Data_Inicial'
                          and T007_Data_Emissao<='$Data_Final'
                          $filtro_expedicao
                          $filtro_vendedor
                          $filtro_empresa
                     group by T007_C007_Id_Vendedor_Interno
                     order by sum(T007_Valor_Total) desc");

     echo mysql_error();

     echo "<table cellpadding=2 width=350>";
       echo "<tr bgcolor='lightblue'>";
         echo "<td align=center colspan=6>";
           echo "<font class=fonte>FATURAMENTO POR VENDEDOR</font>";
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor='lightblue'>";
         echo "<td align=center>";
           echo "<font class=fonte>Vendedor</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Notas<br>emitidas</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Clientes<br>atendidos</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Produtos<br>vendidos</font>";
         echo "</td>";
         echo "<td align=right>";
           echo "<font class=fonte>Valor<br>total</font>";
         echo "</td>";
         echo "<td align=right>";
           echo "<font class=fonte>&nbsp;</font>";
         echo "</td>";
       echo "</tr>";

       unset($mTotal);

       while ($mT007=mysql_fetch_array($T007)) {

             echo "<tr bgcolor='#f4f4f4'>";
               echo "<td align=left>";
                 echo "<font class=fonte>$mT007[C007_Primeiro_Nome]</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Notas],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Clientes],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Itens],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte>".number_format($mT007[Valor_Total],2,',','.')."</font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte>".number_format(($mT007[Valor_Total]/$Total_Mapa)*100,2,',','.')."%</font>";
               echo "</td>";
             echo "</tr>";

             $mTotal[Notas]      += $mT007[Notas];
             $mTotal[Clientes]   += $mT007[Clientes];
             $mTotal[Itens]      += $mT007[Itens];
             $mTotal[Valor_Total]+= $mT007[Valor_Total];

       }

         echo "<tr bgcolor='#f4f4f4'>";
           echo "<td align=center>";
             echo "<font class=fonte><b>Total</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b>".number_format($mTotal[Notas],0,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class=fonte><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
         echo "</tr>";

     echo "</table>";

    echo "</td>";
//   echo "</tr>";

//   echo "<tr>";
    echo "<td valign=top>";

    $T007=mysql_query("select D018_UF,
                              sum(T007_Valor_Total) Valor_Total,
                              count(*) Notas,
                              (select count(DISTINCT T007_D024_Id)
                                 from T007 as T2
                            left join D006 on D006_Id=T007_D006_Id
                            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                            left join D024 on D024_Id=T007_D024_Id
                            left join D018 as T1 on D018_Id=D024_D018_Id
                                where T2.T007_Data_Emissao>='$Data_Inicial'
                                  and T2.T007_Data_Emissao<='$Data_Final'
                                  and T1.D018_Id=D018.D018_Id
                                  and D006_Flag_Venda_Compra_Outros='V'
                                  and D006_Flag_Devolucao='N'
                                  and T007_Flag_Cancelada!='S'
                                  $filtro_expedicao
                                  $filtro_vendedor
                                  $filtro_empresa) Clientes,
                              (select count(DISTINCT T008_D009_Id)
                                 from T007 as T2
                            left join D006 on D006_Id=T007_D006_Id
                            left join T008 on T008_T007_Id=T007_Id
                            left join D024 on D024_Id=T007_D024_Id
                            left join D018 as T1 on D018_Id=D024_D018_Id
                                where T2.T007_Data_Emissao>='$Data_Inicial'
                                  and T2.T007_Data_Emissao<='$Data_Final'
                                  and T1.D018_Id=D018.D018_Id
                                  and D006_Flag_Venda_Compra_Outros='V'
                                  and D006_Flag_Devolucao='N'
                                  and T007_Flag_Cancelada!='S'
                                  $filtro_expedicao
                                  $filtro_vendedor
                                  $filtro_empresa) Itens
                         from T007
                    left join D006 on D006_Id=T007_D006_Id
                    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                    left join D024 on D024_Id=T007_D024_Id
                    left join D018 on D018_Id=D024_D018_Id
                        where T007_Data_Emissao>='$Data_Inicial'
                          and T007_Data_Emissao<='$Data_Final'
                          and D006_Flag_Venda_Compra_Outros='V'
                          and D006_Flag_Devolucao='N'
                          and T007_Flag_Cancelada!='S'
                          $filtro_expedicao
                          $filtro_vendedor
                          $filtro_empresa
                     group by D018_UF
                     order by sum(T007_Valor_Total) desc");

     echo mysql_error();

     echo "<table cellpadding=2 width=350>";
       echo "<tr bgcolor='lightblue'>";
         echo "<td align=center colspan=6>";
           echo "<font class=fonte>FATURAMENTO POR UF</font>";
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor='lightblue'>";
         echo "<td align=center>";
           echo "<font class=fonte>UF</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Notas<br>emitidas</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Clientes<br>atendidos</font>";
         echo "</td>";
         echo "<td align=center>";
           echo "<font class=fonte>Produtos<br>vendidos</font>";
         echo "</td>";
         echo "<td align=right>";
           echo "<font class=fonte>Valor<br>total</font>";
         echo "</td>";
         echo "<td align=right>";
           echo "<font class=fonte>&nbsp;</font>";
         echo "</td>";
       echo "</tr>";

       unset($mTotal);

       while ($mT007=mysql_fetch_array($T007)) {

             echo "<tr bgcolor='#f4f4f4'>";
               echo "<td align=center>";
                 echo "<font class=fonte>$mT007[D018_UF]</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Notas],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Clientes],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>".number_format($mT007[Itens],0,',','.')."</font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte>".number_format($mT007[Valor_Total],2,',','.')."</font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte>".number_format(($mT007[Valor_Total]/$Total_Mapa)*100,2,',','.')."%</font>";
               echo "</td>";
             echo "</tr>";

             $mTotal[Notas]      += $mT007[Notas];
             $mTotal[Clientes]   += $mT007[Clientes];
             $mTotal[Itens]      += $mT007[Itens];
             $mTotal[Valor_Total]+= $mT007[Valor_Total];

       }

         echo "<tr bgcolor='#f4f4f4'>";
           echo "<td align=center>";
             echo "<font class=fonte><b>Total</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b>".number_format($mTotal[Notas],0,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class=fonte><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
         echo "</tr>";

     echo "</table>";

    echo "</td>";
   echo "</tr>";

   echo "<tr>";
    echo "<td valign=top colspan=2>";

      $totalProdutos=mysql_query("select sum(T008_Valor_Total_Preco) Valor_Total
                           from T007
                      left join D006 on D006_Id=T007_D006_Id
                      left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                      left join T008 on T008_T007_Id=T007_Id
                      left join D009 on D009_Id=T008_D009_Id
                      left join D049 on D049_Id=D009_D049_Id
                      left join D001 as D1 on D001_Id=D049_D001_Id
                      left join D002 on D002_Id=D001_D002_Id
                      left join D003 on D003_Id=D002_D003_Id 
                      left join D060 on D060_Id=D001_D060_Id
                      left join C008 on C008_Id=D001_C008_Id
                          where T007_Data_Emissao>='$Data_Inicial'
                            and T007_Data_Emissao<='$Data_Final'
                            and D006_Flag_Venda_Compra_Outros='V'
                            and D006_Flag_Devolucao='N'
                            and T007_Flag_Cancelada!='S'
                            $filtro_expedicao
                            $filtro_vendedor
                            $filtro_empresa");
      $totalProdutos=mysql_fetch_array($totalProdutos);

      $C008=mysql_query("select D003_Nome_Linha,
                                D002_Descricao_Produto,
                                sum(T008_Valor_Total_Preco) Valor_Total,
                                count(*) Notas,
                                D060_Id,
                                D060_Descricao_Grupo,
                                C008_Id,
                                C008_Tipo,
                                (select count(DISTINCT T2.T007_D024_Id)
                                   from T007 as T2
                              left join D006 on D006_Id=T007_D006_Id
                              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                              left join T008 on T008_T007_Id=T007_Id
                              left join D009 on D009_Id=T008_D009_Id
                              left join D049 on D049_Id=D009_D049_Id
                              left join D001 as T1 on D001_Id=D049_D001_Id
                              left join D003 on D003_Id=D001_D003_Id
                                  where T2.T007_Data_Emissao>='$Data_Inicial'
                                    and T2.T007_Data_Emissao<='$Data_Final'
                                    and T1.D001_C008_Id=D1.D001_C008_Id
                                    and D006_Flag_Venda_Compra_Outros='V'
                                    and D006_Flag_Devolucao='N'
                                    and T007_Flag_Cancelada!='S'
                                    $filtro_expedicao
                                    $filtro_vendedor
                                    $filtro_empresa) Clientes,
                                (select count(DISTINCT T3.T008_D009_Id)
                                   from T007 as T2
                              left join D006 on D006_Id=T007_D006_Id
                              left join T008 as T3 on T008_T007_Id=T007_Id
                              left join D009 on D009_Id=T008_D009_Id
                              left join D049 on D049_Id=D009_D049_Id
                              left join D001 as T1 on D001_Id=D049_D001_Id
                              left join D060 on D060_Id=D001_D060_Id
                                  where T2.T007_Data_Emissao>='$Data_Inicial'
                                    and T2.T007_Data_Emissao<='$Data_Final'
                                    and T1.D001_C008_Id=D1.D001_C008_Id
                                    and D006_Flag_Venda_Compra_Outros='V'
                                    and D006_Flag_Devolucao='N'
                                    and T007_Flag_Cancelada!='S'
                                    $filtro_expedicao
                                    $filtro_vendedor
                                    $filtro_empresa) Itens
                           from T007
                      left join D006 on D006_Id=T007_D006_Id
                      left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                      left join T008 on T008_T007_Id=T007_Id
                      left join D009 on D009_Id=T008_D009_Id
                      left join D049 on D049_Id=D009_D049_Id
                      left join D001 as D1 on D001_Id=D049_D001_Id
                      left join D002 on D002_Id=D001_D002_Id
                      left join D003 on D003_Id=D002_D003_Id 
                      left join D060 on D060_Id=D001_D060_Id
                      left join C008 on C008_Id=D001_C008_Id
                          where T007_Data_Emissao>='$Data_Inicial'
                            and T007_Data_Emissao<='$Data_Final'
                            and D006_Flag_Venda_Compra_Outros='V'
                            and D006_Flag_Devolucao='N'
                            and T007_Flag_Cancelada!='S'
                            $filtro_expedicao
                            $filtro_vendedor
                            $filtro_empresa
                       group by C008_Id
                       order by sum(T008_Valor_Total_Preco) desc");

       echo mysql_error();

       echo "<table cellpadding=2 width=100%>";
         echo "<tr bgcolor='lightblue'>";
           echo "<td align=center colspan=8>";
             echo "<font class=fonte>FATURAMENTO POR DEPARTAMENTO</font>";
           echo "</td>";
         echo "</tr>";
         echo "<tr bgcolor='lightblue'>";
           echo "<td align=center>";
             echo "<font class=fonte>Departamento</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte>Setor</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte>Grupo</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte>Sub-Grupo</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte>Clientes<br>atendidos</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte>Produtos<br>vendidos</font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class=fonte>Valor<br>total</font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class=fonte>&nbsp;</font>";
           echo "</td>";
         echo "</tr>";

         echo mysql_error();
         unset($mTotal);

         while ($mC008=mysql_fetch_array($C008)) {

             echo "<tr bgcolor='#DDEAEC'>";
               echo "<td align=left>";
                 echo "<font class=fonte style='font-size:12px;'><b>{$mC008[C008_Tipo]}</b></font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte style='font-size:12px;'><b>&nbsp;</b></font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte style='font-size:12px;'><b>&nbsp;</b></font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte>&nbsp;</font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte style='font-size:12px;'><b>".number_format($mC008[Clientes],0,',','.')."</b></font>";
               echo "</td>";
               echo "<td align=center>";
                 echo "<font class=fonte style='font-size:12px;'><b>".number_format($mC008[Itens],0,',','.')."</b></font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte style='font-size:12px;'><b>".number_format($mC008[Valor_Total],2,',','.')."</b></font>";
               echo "</td>";
               echo "<td align=right>";
                 echo "<font class=fonte style='font-size:12px;'><b>".number_format(($mC008[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</b></font>";
               echo "</td>";
             echo "</tr>";


            $T007=mysql_query("select D003_Nome_Linha,
                                      D002_Descricao_Produto,
                                      sum(T008_Valor_Total_Preco) Valor_Total,
                                      count(*) Notas,
                                      D060_Id,
                                      D060_Descricao_Grupo,
                                      (select count(DISTINCT T2.T007_D024_Id)
                                         from T007 as T2
                                    left join D006 on D006_Id=T007_D006_Id
                                    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                    left join T008 on T008_T007_Id=T007_Id
                                    left join D009 on D009_Id=T008_D009_Id
                                    left join D049 on D049_Id=D009_D049_Id
                                    left join D001 as T1 on D001_Id=D049_D001_Id
                                    left join D003 on D003_Id=D001_D003_Id
                                        where T2.T007_Data_Emissao>='$Data_Inicial'
                                          and T2.T007_Data_Emissao<='$Data_Final'
                                          and T1.D001_D060_Id=D1.D001_D060_Id
                                          and T1.D001_C008_Id='$mC008[C008_Id]'
                                          and D006_Flag_Venda_Compra_Outros='V'
                                          and D006_Flag_Devolucao='N'
                                          and T007_Flag_Cancelada!='S'
                                          $filtro_expedicao
                                          $filtro_vendedor
                                          $filtro_empresa) Clientes,
                                      (select count(DISTINCT T3.T008_D009_Id)
                                         from T007 as T2
                                    left join D006 on D006_Id=T007_D006_Id
                                    left join T008 as T3 on T008_T007_Id=T007_Id
                                    left join D009 on D009_Id=T008_D009_Id
                                    left join D049 on D049_Id=D009_D049_Id
                                    left join D001 as T1 on D001_Id=D049_D001_Id
                                    left join D060 on D060_Id=D001_D060_Id
                                        where T2.T007_Data_Emissao>='$Data_Inicial'
                                          and T2.T007_Data_Emissao<='$Data_Final'
                                          and T1.D001_D060_Id=D1.D001_D060_Id
                                          and T1.D001_C008_Id='$mC008[C008_Id]'
                                          and D006_Flag_Venda_Compra_Outros='V'
                                          and D006_Flag_Devolucao='N'
                                          and T007_Flag_Cancelada!='S'
                                          $filtro_expedicao
                                          $filtro_vendedor
                                          $filtro_empresa) Itens
                                 from T007
                            left join D006 on D006_Id=T007_D006_Id
                            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                            left join T008 on T008_T007_Id=T007_Id
                            left join D009 on D009_Id=T008_D009_Id
                            left join D049 on D049_Id=D009_D049_Id
                            left join D001 as D1 on D001_Id=D049_D001_Id
                            left join D002 on D002_Id=D001_D002_Id
                            left join D003 on D003_Id=D002_D003_Id 
                            left join D060 on D060_Id=D001_D060_Id
                            left join C008 on C008_Id=D001_C008_Id
                                where T007_Data_Emissao>='$Data_Inicial'
                                  and T007_Data_Emissao<='$Data_Final'
                                  and D006_Flag_Venda_Compra_Outros='V'
                                  and D006_Flag_Devolucao='N'
                                  and T007_Flag_Cancelada!='S'
                                  $filtro_expedicao
                                  $filtro_vendedor
                                  $filtro_empresa
                                  and D001_C008_Id='$mC008[C008_Id]'
                             group by D060_Id
                             order by sum(T008_Valor_Total_Preco) desc");

             echo mysql_error();


               while ($mT007=mysql_fetch_array($T007)) {

                     echo "<tr bgcolor='#DDEAEC'>";
                       echo "<td align=center>";
                         echo "<font class=fonte style='font-size:12px;'><b>&nbsp;</b></font>";
                       echo "</td>";
                       echo "<td align=left>";
                         echo "<font class=fonte style='font-size:12px;'><b>{$mT007[D060_Descricao_Grupo]}</b></font>";
                       echo "</td>";
                       echo "<td align=center>";
                         echo "<font class=fonte style='font-size:12px;'><b>&nbsp;</b></font>";
                       echo "</td>";
                       echo "<td align=center>";
                         echo "<font class=fonte>&nbsp;</font>";
                       echo "</td>";
                       echo "<td align=center>";
                         echo "<font class=fonte style='font-size:12px;'><b>".number_format($mT007[Clientes],0,',','.')."</b></font>";
                       echo "</td>";
                       echo "<td align=center>";
                         echo "<font class=fonte style='font-size:12px;'><b>".number_format($mT007[Itens],0,',','.')."</b></font>";
                       echo "</td>";
                       echo "<td align=right>";
                         echo "<font class=fonte style='font-size:12px;'><b>".number_format($mT007[Valor_Total],2,',','.')."</b></font>";
                       echo "</td>";
                       echo "<td align=right>";
                         echo "<font class=fonte style='font-size:12px;'><b>".number_format(($mT007[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</b></font>";
                       echo "</td>";
                     echo "</tr>";

                     $mTotal[Notas]      += $mT007[Notas];
                     $mTotal[Clientes]   += $mT007[Clientes];
                     $mTotal[Itens]      += $mT007[Itens];
                     $mTotal[Valor_Total]+= $mT007[Valor_Total];

                     $T0071=mysql_query("select D003_Nome_Linha,
                                              D002_Descricao_Produto,
                                              D003_Id,
                                              sum(T008_Valor_Total_Preco) Valor_Total,
                                              count(*) Notas,
                                              (select count(DISTINCT T2.T007_D024_Id)
                                                 from T007 as T2
                                            left join D006 on D006_Id=T007_D006_Id
                                            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                            left join T008 on T008_T007_Id=T007_Id
                                            left join D009 on D009_Id=T008_D009_Id
                                            left join D049 on D049_Id=D009_D049_Id
                                            left join D001 as T1 on D001_Id=D049_D001_Id
                                                where T2.T007_Data_Emissao>='$Data_Inicial'
                                                  and T2.T007_Data_Emissao<='$Data_Final'
                                                  and T1.D001_C008_Id='$mC008[C008_Id]'
                                                  and T1.D001_D060_Id='$mT007[D060_Id]'
                                                  and T1.D001_D003_Id=D1.D001_D003_Id
                                                  and D006_Flag_Venda_Compra_Outros='V'
                                                  and D006_Flag_Devolucao='N'
                                                  and T2.T007_Flag_Cancelada!='S'
                                                  $filtro_expedicao
                                                  $filtro_vendedor
                                                  $filtro_empresa) Clientes,
                                              (select count(DISTINCT T3.T008_D009_Id)
                                                 from T007 as T2
                                            left join D006 on D006_Id=T007_D006_Id
                                            left join T008 as T3 on T008_T007_Id=T007_Id
                                            left join D009 on D009_Id=T008_D009_Id
                                            left join D049 on D049_Id=D009_D049_Id
                                            left join D001 as T1 on D001_Id=D049_D001_Id
                                            where T2.T007_Data_Emissao>='$Data_Inicial'
                                              and T2.T007_Data_Emissao<='$Data_Final'
                                              and T1.D001_C008_Id='$mC008[C008_Id]'
                                              and T1.D001_D060_Id='$mT007[D060_Id]'
                                              and T1.D001_D003_Id=D1.D001_D003_Id
                                              and D006_Flag_Venda_Compra_Outros='V'
                                              and D006_Flag_Devolucao='N'
                                              and T007_Flag_Cancelada!='S'
                                              $filtro_expedicao
                                              $filtro_vendedor
                                              $filtro_empresa) Itens
                                         from T007
                                    left join D006 on D006_Id=T007_D006_Id
                                    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                    left join T008 on T008_T007_Id=T007_Id
                                    left join D009 on D009_Id=T008_D009_Id
                                    left join D049 on D049_Id=D009_D049_Id
                                    left join D001 as D1 on D001_Id=D049_D001_Id
                                    left join D002 on D002_Id=D001_D002_Id
                                    left join D003 on D003_Id=D001_D003_Id
                                    left join D060 on D060_Id=D001_D060_Id
                                        where T007_Data_Emissao>='$Data_Inicial'
                                          and T007_Data_Emissao<='$Data_Final'
                                          and D006_Flag_Venda_Compra_Outros='V'
                                          and D006_Flag_Devolucao='N'
                                          and T007_Flag_Cancelada!='S'
                                          $filtro_expedicao
                                          $filtro_vendedor
                                          $filtro_empresa
                                          and D001_C008_Id='$mC008[C008_Id]'
                                          and D001_D060_Id='$mT007[D060_Id]'
                                     group by D003_Id
                                     order by sum(T008_Valor_Total_Preco) desc");

                       echo mysql_error();


                       while ($mT0071=mysql_fetch_array($T0071)) {

                             echo "<tr bgcolor='#f4f4f4'>";
                               echo "<td align=center>";
                                 echo "<font class=fonte>&nbsp;</font>";
                               echo "</td>";
                               echo "<td align=center>";
                                 echo "<font class=fonte>&nbsp;</font>";
                               echo "</td>";
                               echo "<td align=left>";
                                 echo "<font class=fonte><b>{$mT0071[D003_Nome_Linha]}</b></font>";
                               echo "</td>";
                               echo "<td align=left>";
                                 echo "<font class=fonte>&nbsp;</font>";
                               echo "</td>";
                               echo "<td align=center>";
                                 echo "<font class=fonte><b>".number_format($mT0071[Clientes],0,',','.')."</b></font>";
                               echo "</td>";
                               echo "<td align=center>";
                                 echo "<font class=fonte><b>".number_format($mT0071[Itens],0,',','.')."</b></font>";
                               echo "</td>";
                               echo "<td align=right>";
                                 echo "<font class=fonte><b>".number_format($mT0071[Valor_Total],2,',','.')."</b></font>";
                               echo "</td>";
                               echo "<td align=right>";
                                 echo "<font class=fonte><b>".number_format(($mT0071[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</b></font>";
                               echo "</td>";
                             echo "</tr>";

                                 $T0072=mysql_query("select D003_Nome_Linha,
                                                          D002_Descricao_Produto,
                                                          sum(T008_Valor_Total_Preco) Valor_Total,
                                                          count(*) Notas,
                                                          (select count(DISTINCT T2.T007_D024_Id)
                                                             from T007 as T2
                                                        left join D006 on D006_Id=T007_D006_Id
                                                        left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                                        left join T008 on T008_T007_Id=T007_Id
                                                        left join D009 on D009_Id=T008_D009_Id
                                                        left join D049 on D049_Id=D009_D049_Id
                                                        left join D001 as T1 on D001_Id=D049_D001_Id
                                                            where T2.T007_Data_Emissao>='$Data_Inicial'
                                                              and T2.T007_Data_Emissao<='$Data_Final'
                                                              and T1.D001_C008_Id='$mC008[C008_Id]'
                                                              and T1.D001_D060_Id='$mT007[D060_Id]'
                                                              and T1.D001_D003_Id='$mT0071[D003_Id]'
                                                              and T1.D001_D002_Id=D1.D001_D002_Id
                                                              and D006_Flag_Venda_Compra_Outros='V'
                                                              and D006_Flag_Devolucao='N'
                                                              and T2.T007_Flag_Cancelada!='S'
                                                              $filtro_expedicao
                                                              $filtro_vendedor
                                                              $filtro_empresa) Clientes,
                                                          (select count(DISTINCT T3.T008_D009_Id)
                                                             from T007 as T2
                                                        left join D006 on D006_Id=T007_D006_Id
                                                        left join T008 as T3 on T008_T007_Id=T007_Id
                                                        left join D009 on D009_Id=T008_D009_Id
                                                        left join D049 on D049_Id=D009_D049_Id
                                                        left join D001 as T1 on D001_Id=D049_D001_Id
                                                        where T2.T007_Data_Emissao>='$Data_Inicial'
                                                          and T2.T007_Data_Emissao<='$Data_Final'
                                                          and T1.D001_C008_Id='$mC008[C008_Id]'
                                                          and T1.D001_D060_Id='$mT007[D060_Id]'
                                                          and T1.D001_D003_Id='$mT0071[D003_Id]'
                                                          and T1.D001_D002_Id=D1.D001_D002_Id
                                                          and D006_Flag_Venda_Compra_Outros='V'
                                                          and D006_Flag_Devolucao='N'
                                                          and T007_Flag_Cancelada!='S'
                                                          and T007_Data_Emissao>='$Data_Inicial'
                                                          and T007_Data_Emissao<='$Data_Final'
                                                          $filtro_expedicao
                                                          $filtro_vendedor
                                                          $filtro_empresa) Itens
                                                     from T007
                                                left join D006 on D006_Id=T007_D006_Id
                                                left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                                left join T008 on T008_T007_Id=T007_Id
                                                left join D009 on D009_Id=T008_D009_Id
                                                left join D049 on D049_Id=D009_D049_Id
                                                left join D001 as D1 on D001_Id=D049_D001_Id
                                                left join D002 on D002_Id=D001_D002_Id
                                                left join D003 on D003_Id=D001_D003_Id
                                                left join D060 on D060_Id=D001_D060_Id
                                                    where T007_Data_Emissao>='$Data_Inicial'
                                                      and T007_Data_Emissao<='$Data_Final'
                                                      and D006_Flag_Venda_Compra_Outros='V'
                                                      and D006_Flag_Devolucao='N'
                                                      and T007_Flag_Cancelada!='S'
                                                      $filtro_expedicao
                                                      $filtro_vendedor
                                                      $filtro_empresa
                                                      and D001_C008_Id='$mC008[C008_Id]'
                                                      and D001_D060_Id='$mT007[D060_Id]'
                                                      and D001_D003_Id='$mT0071[D003_Id]'
                                                 group by D002_Id
                                                 order by sum(T008_Valor_Total_Preco) desc");

                                 echo mysql_error();

                                   while ($mT0072=mysql_fetch_array($T0072)) {

                                         echo "<tr bgcolor='#f4f4f4'>";
                                           echo "<td align=center>";
                                             echo "<font class=fonte>&nbsp;</font>";
                                           echo "</td>";
                                           echo "<td align=center>";
                                             echo "<font class=fonte>&nbsp;</font>";
                                           echo "</td>";
                                           echo "<td align=center>";
                                             echo "<font class=fonte>&nbsp;</font>";
                                           echo "</td>";
                                           echo "<td alig=left>";
                                             echo "<font class=fonte>$mT0072[D002_Descricao_Produto]</font>";
                                           echo "</td>";
                                           echo "<td align=center>";
                                             echo "<font class=fonte>".number_format($mT0072[Clientes],0,',','.')."</font>";
                                           echo "</td>";
                                           echo "<td align=center>";
                                             echo "<font class=fonte>".number_format($mT0072[Itens],0,',','.')."</font>";
                                           echo "</td>";
                                           echo "<td align=right>";
                                             echo "<font class=fonte>".number_format($mT0072[Valor_Total],2,',','.')."</font>";
                                           echo "</td>";
                                           echo "<td align=right>";
                                             echo "<font class=fonte>".number_format(($mT0072[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</font>";
                                           echo "</td>";
                                         echo "</tr>";

                                   }


                            }

                     }
              } 
             echo "<tr bgcolor='#f4f4f4'>";
           echo "<td align=center colspan=3>";
             echo "<font class=fonte><b>Total</font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class=fonte><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
           echo "</td>";
           echo "<td align=center>";
             echo "<font class=fonte><b></b></font>";
           echo "</td>";
         echo "</tr>";

     echo "</table>";

    echo "</td>";
   echo "</tr>";

  echo "</table>";

?>
