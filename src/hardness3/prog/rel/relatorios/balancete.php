<?php
namespace hardness;
// Busca os dados da empresa usu?ia
$cSql=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
$mC004=mysql_fetch_array($cSql);

set_time_limit(0);

?>

<html>
<head>
<title>Balancete</title>
 
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

$Data=date("d/m/Y");
$Empresa = $_REQUEST['Empresa'];
$Data_Inicio=gCorrigeDataInverte($_REQUEST['Data_Inicial']);
$Data_Fim=gCorrigeDataInverte($_REQUEST['Data_Final']);
$Tipo = $_REQUEST['Tipo'];
$Contas = $_REQUEST['Contas'];

echo "<table align=center border=0 valign=top cellpadding=3 cellspacing=0 bgcolor=white style='border-bottom:1px dashed black;width:800px;max-width:800px'>";
  echo "<tr>";
    echo "<td valign=top>";
      echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
        echo "<tr>";
          echo "<td width=80%>";

            //-- $cArquivo_Cabecalho_Relatorio é definido no arquivo include/pasta_usuario.inc
            //include $cArquivo_Cabecalho_Relatorio;
            $Diretorio_Imagens="/hardness/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/";
            include $g['pathDadosAntigo']."/cabecalho_relatorio.php";

          echo "</td>";
          echo "<td bgcolor=white align=center width=20% rowspan=2>";
            echo "<font class='titulo_relatorio'>Balancete<br>$Data</font><font class=fonte><br><b></b>";
            echo "</font>";
          echo "</td>";
        echo "</tr>";
      echo "</table>";
    echo "</td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td>";
      echo "<table>";

      if ($Empresa=='1') {

          If ($Tipo=='1') {
              $Filtro_Empresa1="and T019_C004_Id='{$g['empresaAtual']}'";
              $Filtro_Empresa2="and T019_C004_Id='{$g['empresaAtual']}'";
          } else {
              $Filtro_Empresa1="and T002_C004_Id='{$g['empresaAtual']}'";
              $Filtro_Empresa2="and T015_C004_Id='{$g['empresaAtual']}'";
          }
          $Filtro_Empresa3="and T018_C004_Id='{$g['empresaAtual']}'";

          echo "<tr>";
            echo "<td>";
              echo "<font class=fontemed>Empresa:</font>";
            echo "</td>";
            echo "<td colspan=10>";
              echo "<font class=fontemed>$mC004[C004_Nome_Empresa]</font>";
            echo "</td>";

      } else {
      
          echo "<tr>";
            echo "<td>";
              echo "<font class=fontemed>Empresa:</font>";
            echo "</td>";
            echo "<td colspan=10>";
              echo "<font class=fontemed>TODAS</font>";
            echo "</td>";
          echo "</tr>";

      }

      echo "<tr>";
        echo "<td>";
          echo "<font class=fontemed>Data inicial:</font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fontemed>".gCorrigeData($Data_Inicio)."</font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fontemed>Data Final:</font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fontemed>".gCorrigeData($Data_Fim)."</font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fontemed>Tipo:</font>";
        echo "</td>";
        echo "<td>";
          echo "<font class=fontemed>";
          if($Tipo=='1') { echo 'REALIZADO'; } else { echo 'A REALIZAR'; }
          echo "</font>";
        echo "</td>";
      echo "</tr>";

      If ($Tipo=='1') {
         $D014=mysql_query("select D014_D032_Id,
                                   D014_SubConta,
                                   D014_Flag_Plano,
                                   D014_Flag_Tipo,
                                   D032_Descricao,
                                   D032_Tipo,
                                   D032_Tipo(D032_Tipo) grupoTipo,
                                   (select sum(T019_Valor_Lancamento) from T019 where T019_D014_Id=D014_Id and T019_Data_Lancamento>='$Data_Inicio' and T019_Data_Lancamento<='$Data_Fim' and T019_Valor_Lancamento>0 $Filtro_Empresa1) Recebido,
                                   (select sum(T019_Valor_Lancamento*-1) from T019 where T019_D014_Id=D014_Id and T019_Data_Lancamento>='$Data_Inicio' and T019_Data_Lancamento<='$Data_Fim' and T019_Valor_Lancamento<0 $Filtro_Empresa1) Pago,
                                   T018_Valor
                              from D014
                         left join T018 on T018_D014_Id=D014_Id and T018_Ano='$Ano' and T018_Mes='$Mes' $Filtro_Empresa3
                         left join D032 on D032_Id=D014_D032_Id
                             where D032_Flag_Ativo='S'
                               and D014_Flag_Ativo='S'
                          order by D032_Tipo,D032_Descricao,D014_SubConta");
      } else {
         $D014=mysql_query("select D014_D032_Id,
                                   D014_SubConta,
                                   D014_Flag_Plano,
                                   D014_Flag_Tipo,
                                   D032_Descricao,
                                   D032_Tipo,
                                   D032_Tipo(D032_Tipo) grupoTipo,
                                   (select sum(T002_Valor_Total) from T002 where T002_D014_Id=D014_Id and T002_Data_Vencimento>='$Data_Inicio' and T002_Data_Vencimento<='$Data_Fim' and T002_Data_Recebimento='0000-00-00' $Filtro_Empresa1) Recebido,
                                   (select sum(T015_Valor_Total) from T015 where T015_D014_Id=D014_Id and T015_Data_Vencimento>='$Data_Inicio' and T015_Data_Vencimento<='$Data_Fim' and T015_Data_Pagamento='0000-00-00' $Filtro_Empresa2) Pago,
                                   T018_Valor
                              from D014
                         left join T018 on T018_D014_Id=D014_Id and T018_Ano='$Ano' and T018_Mes='$Mes' $Filtro_Empresa3
                         left join D032 on D032_Id=D014_D032_Id
                             where D032_Flag_Ativo='S'
                               and D014_Flag_Ativo='S'
                          order by D032_Tipo,D032_Descricao,D014_SubConta");
      }

      echo mysql_error();

         //
         // CABECALHO DAS COLUNAS
         //

     echo "</table>";
    echo "</td>";
   echo "</tr>";
   echo "<tr>";
    echo "<td>";

    echo "<table align=center border='0' cellpadding=3 cellspacing=2 width='100%' height='100%'>";

    $Resultado=0;
    $D032_Id='9999';
    $D032_Tipo='9999';
    while ($mD014=mysql_fetch_array($D014)) {
    
          if ($mD014[D032_Tipo]!=$D032_Tipo) {

            $Bold='font-weight:bold';
            $D032_Tipo=$mD014[D032_Tipo];

            $T018=mysql_query("select sum(T018_Valor) Total
                                  from D014
                             left join T018 on T018_D014_Id=D014_Id and T018_Ano='$Ano' and T018_Mes='$Mes' $Filtro_Empresa3
                             left join D032 on D032_Id=D014_D032_Id
                                 where D014_D032_Id = '$mD014[D014_D032_Id]'
                                   and D032_Tipo='$D032_Tipo'");
            $mT018=mysql_fetch_array($T018);

            If ($Tipo=='1') {
             
                $T002=mysql_query("select sum(T019_Valor_Lancamento)
                                      from T019
                                 left join D014 on D014_Id=T019_D014_Id
                                 left join D032 on D032_Id=D014_D032_Id
                                 where D032_Tipo = '$mD014[D032_Tipo]' and T019_Data_Lancamento>='$Data_Inicio' and T019_Data_Lancamento<='$Data_Fim' and T019_Valor_Lancamento>0 $Filtro_Empresa1");
                $mT002=mysql_fetch_array($T002);
                 
                echo mysql_error();

                $T015=mysql_query("select sum(T019_Valor_Lancamento*-1)
                                      from T019
                                 left join D014 on D014_Id=T019_D014_Id
                                 left join D032 on D032_Id=D014_D032_Id
                                     where D032_Tipo = '$mD014[D032_Tipo]' and T019_Data_Lancamento>='$Data_Inicio' and T019_Data_Lancamento<='$Data_Fim' and T019_Valor_Lancamento<0 $Filtro_Empresa1");
                $mT015=mysql_fetch_array($T015);
                 
                echo mysql_error();

            } else {

                $T002=mysql_query("select sum(T002_Valor_Total)
                                      from T002
                                 left join D014 on D014_Id=T002_D014_Id
                                 left join D032 on D032_Id=D014_D032_Id
                                     where D032_Tipo = '$mD014[D032_Tipo]' and T002_Data_Vencimento>='$Data_Inicio' and T002_Data_Vencimento<='$Data_Fim' and T002_Data_Recebimento='0000-00-00' $Filtro_Empresa1");
                $mT002=mysql_fetch_array($T002);

                $T015=mysql_query("select sum(T015_Valor_Total)
                                      from T015
                                 left join D014 on D014_Id=T015_D014_Id
                                 left join D032 on D032_Id=D014_D032_Id
                                     where D032_Tipo = '$mD014[D032_Tipo]' and T015_Data_Vencimento>='$Data_Inicio' and T015_Data_Vencimento<='$Data_Fim' and T015_Data_Pagamento='0000-00-00' $Filtro_Empresa2");
                $mT015=mysql_fetch_array($T015);
                 
            }

            $TotalD032=$mT002[0]-$mT015[0];

            $mD032[T018_Valor]=$mT018[Total];
            $mD032[Recebido]=$mT002[0];
            $mD032[Pago]=$mT015[0];

            $espaco='';
            $Fonte=14;
    
            if (($Contas=='1') and ($TotalD032==0)) {
                continue;
            }

            echo "<tr bgcolor='lightblue'>";
              echo "<td align=left rowspan=2>";
                echo "<font class=fontemed>$espaco</font>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>$mD014[grupoTipo]</font>";
              echo "</td>";
              if (!empty($Mes)){
                echo "<td align=right>";
                  echo "<font class=fontemed style='font-size:12px'>Projeção</font>";
                echo "</td>";
              }
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:12px'>Entradas</font>";
              echo "</td>";
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:12px'>Saidas</font>";
              echo "</td>";
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:12px'>Total</font>";
              echo "</td>";
            echo "</tr>";
            echo "<tr bgcolor='lightblue'>";
              if (!empty($Mes)){
                  echo "<td align=right>";
                    echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD032[T018_Valor],2,',','.')."</font>";
                  echo "</td>";
              }
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD032[Recebido],2,',','.')."</font>";
              echo "</td>";
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD032[Pago],2,',','.')."</font>";
              echo "</td>";
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($TotalD032,2,',','.')."</font>";
              echo "</td>";
            echo "</tr>";

          }

          if ($mD014[D014_D032_Id]!=$D032_Id) {

            $Bold='font-weight:bold';
            $D032_Id=$mD014[D014_D032_Id];

            $T018=mysql_query("select sum(T018_Valor) Total
                                  from D014
                             left join T018 on T018_D014_Id=D014_Id and T018_Ano='$Ano' and T018_Mes='$Mes' $Filtro_Empresa3
                                 where D014_D032_Id = '$mD014[D014_D032_Id]'");
            $mT018=mysql_fetch_array($T018);
            If ($Tipo=='1') {
             
                $T002=mysql_query("select sum(T019_Valor_Lancamento)
                                      from T019
                                 left join D014 on D014_Id=T019_D014_Id
                                     where D014_D032_Id = '$mD014[D014_D032_Id]' and T019_Data_Lancamento>='$Data_Inicio' and T019_Data_Lancamento<='$Data_Fim' and T019_Valor_Lancamento>0 $Filtro_Empresa1");
                $mT002=mysql_fetch_array($T002);
                 
                echo mysql_error();

                $T015=mysql_query("select sum(T019_Valor_Lancamento*-1)
                                      from T019
                                 left join D014 on D014_Id=T019_D014_Id
                                     where D014_D032_Id = '$mD014[D014_D032_Id]' and T019_Data_Lancamento>='$Data_Inicio' and T019_Data_Lancamento<='$Data_Fim' and T019_Valor_Lancamento<0 $Filtro_Empresa1");
                $mT015=mysql_fetch_array($T015);
                 
                echo mysql_error();

            } else {

                $T002=mysql_query("select sum(T002_Valor_Total)
                                      from T002
                                 left join D014 on D014_Id=T002_D014_Id
                                     where D014_D032_Id = '$mD014[D014_D032_Id]' and T002_Data_Vencimento>='$Data_Inicio' and T002_Data_Vencimento<='$Data_Fim' and T002_Data_Recebimento='0000-00-00' $Filtro_Empresa1");
                $mT002=mysql_fetch_array($T002);

                $T015=mysql_query("select sum(T015_Valor_Total)
                                      from T015
                                 left join D014 on D014_Id=T015_D014_Id
                                     where D014_D032_Id = '$mD014[D014_D032_Id]' and T015_Data_Vencimento>='$Data_Inicio' and T015_Data_Vencimento<='$Data_Fim' and T015_Data_Pagamento='0000-00-00' $Filtro_Empresa2");
                $mT015=mysql_fetch_array($T015);
                 
            }

            $TotalD032=$mT002[0]-$mT015[0];

            $mD032[T018_Valor]=$mT018[Total];
            $mD032[Recebido]=$mT002[0];
            $mD032[Pago]=$mT015[0];

            $espaco='';
            $Fonte=14;
    
            $D032_Id=$mD014[D014_D032_Id];

            if (($Contas=='1') and ($TotalD032==0)) {
                continue;
            }

            $cor='white';

            echo "<tr'>";
              echo "<td align=left>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>$mD014[D032_Descricao]</font>";
              echo "</td>";
              if (!empty($Mes)){
                  echo "<td align=right>";
                    echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD032[T018_Valor],2,',','.')."</font>";
                  echo "</td>";
              }
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD032[Recebido],2,',','.')."</font>";
              echo "</td>";
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD032[Pago],2,',','.')."</font>";
              echo "</td>";
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($TotalD032,2,',','.')."</font>";
              echo "</td>";
            echo "</tr>";

          }

          $Total=$mD014[Recebido]-$mD014[Pago];
          $Resultado+=$Total;
          if ($mD014[D014_Flag_Tipo]=='RC')
          {
              $Total_Projecao+=$mD014[T018_Valor];
          } else {
            $Total_Projecao-=$mD014[T018_Valor];
          }
          $Total_Recebido+=$mD014[Recebido];

          $Total_Pago+=$mD014[Pago];

          $D032_Id=$mD014[D014_D032_Id];

          if (($Contas=='1') and ($Total==0) and ($mD014[T018_Valor]<=0)) {
             continue;
          }

          if ($cor=='#f4f4f4')
          {
              $cor='white';
          } else {
              $cor='#f4f4f4';
          }

          $Bold='';
          $espaco='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
          $Fonte=12;

          echo "<tr bgcolor='$cor'>";
            echo "<td align=left>";
              echo "<font class=fontemed>$espaco</font>";
              echo "<font class=fontemed style='font-size:$Fonte;$Bold'>$mD014[D014_SubConta]</font>";
            echo "</td>";
            if (!empty($Mes))
            {
              echo "<td align=right>";
                echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD014[T018_Valor],2,',','.')."</font>";
              echo "</td>";
            }
            echo "<td align=right>";
              echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD014[Recebido],2,',','.')."</font>";
            echo "</td>";
            echo "<td align=right>";
            echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($mD014[Pago],2,',','.')."</font>";
          echo "</td>";
          echo "<td align=right>";
            echo "<font class=fontemed style='font-size:$Fonte;$Bold'>".number_format($Total,2,',','.')."</font>";
          echo "</td>";
        echo "</tr>";
    }

    echo "<tr bgcolor='white'>";
      echo "<td align=center style='border-top:1px solid silver'>";
        echo "<font class=fontemed style='font-size:18;font-weight:bold'>&nbsp;</font>";
      echo "</td>";
      if (!empty($Mes))
      {
        echo "<td align=right style='border-top:1px solid silver'>";
          echo "<font class=fontemed style='font-size:14;font-weight:bold'>".number_format($Total_Projecao,2,',','.')."</font>";
        echo "</td>";
      }
      echo "<td align=right style='border-top:1px solid silver'>";
        echo "<font class=fontemed style='font-size:14;font-weight:bold'>".number_format($Total_Recebido,2,',','.')."</font>";
      echo "</td>";
      echo "<td align=right style='border-top:1px solid silver'>";
        echo "<font class=fontemed style='font-size:14;font-weight:bold'>".number_format($Total_Pago,2,',','.')."</font>";
      echo "</td>";
      echo "<td align=right style='border-top:1px solid silver'>";
        echo "<font class=fontemed style='font-size:14;font-weight:bold'>".number_format($Resultado,2,',','.')."</font>";
      echo "</td>";
    echo "</tr>";
  echo "</table>";

?>
