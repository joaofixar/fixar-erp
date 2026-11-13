<?php

namespace hardness;
global $g;

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";
include "../include/pasta_usuario.inc";

$cArquivo_Cabecalho_Relatorio = "../cabecalho_impressoes.php";

header ('Content-type: text/html; charset=UTF-8');

// Busca os dados da empresa usu�ria
$cSql=mysqli_query("select * from C004 where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($cSql);

?>

<html>
<head>
<title>Fluxo de Caixa</title>
<meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  <meta http-equiv='Pragma' content='no-cache'>  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
<link rel=stylesheet href="../classes/classe_tamanho_medio_firefox.css" type="text/css">
<!-- Fun��es Gen�ricas em Javascript -->
<script src='../func/funcoes_genericas.js'></script>
<script src='../func/ajax.js'></script>

<STYLE TYPE="text/css"><!--
  A:link {text-decoration:none; color:000000}
  A:visited {text-decoration:none; color:000000}
  A:hover {text-decoration:underline; color:000000; cursor:hand}
  A:active {text-decoration:underline; color:000000; cursor:hand}

--></style>
</head>
<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>

<?php

if (empty($Data)) {
   $Data=date("d/m/Y");
}

$xData=$Data;

$Data=gCorrigeDataInverte($Data);
$Data_Inicial=gCorrigeDataInverte($Data_Inicio);
$Data_Final=gCorrigeDataInverte($Data_Fim);

  //
  // CABE�ALHO
  //

    echo "<table align=center border='0' valign=top cellpadding=3 cellspacing=0 bgcolor=white width='100%' style='border-bottom:1px dashed black'>";
     echo "<tr>";
      echo "<td valign=top>";
       echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
        echo "<tr>";
         echo "<td bgcolor=white width=80%>";

                //-- $cArquivo_Cabecalho_Relatorio � definido no arquivo include/pasta_usuario.inc
                include $cArquivo_Cabecalho_Relatorio;

         echo "</td>";
         echo "<td bgcolor=white align=center width=20% rowspan=2>";
           echo "<font class='titulo_relatorio'>Fluxo de Caixa</font><font class=fonte><br><b></b>";
           echo "</font>";
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor=white>";
         echo "<td colspan=2>";
           echo "<font class=fontemed>Impresso</b> &nbsp;<b>".date("d/m/Y h:i:s",time())."</b></font>";
           echo "</td>";
         echo "</td>";
       echo "</tr>";
     echo "</table>";
    echo "</td>";
   echo "</tr>";
   
    if ($Titulos_Cobranca=='N')
    {
        $Filtro_Cobranca=" and T002_Flag_Status!='3' ";
	}
    	
      if ($Empresa=='1') {
          $Filtro_Empresa1="and T002_C004_Id='$Empresa_Atual'";
          $Filtro_Empresa2="and T015_C004_Id='$Empresa_Atual'";
          $Filtro_Empresa3="and T018_C004_Id='$Empresa_Atual'";
          $Filtro_Empresa4="and T005_C004_Id='$Empresa_Atual'";
          $Filtro_Empresa5="and T019_C004_Id='$Empresa_Atual'";
          $Filtro_Empresa6="and D007_C004_Id='$Empresa_Atual'";
          
          echo "<tr>";
            echo "<td>";
              echo "<font class=fonte>Empresa: $mC004[C004_Nome_Empresa]</font>";
            echo "</td>";
          echo "</tr>";

      } else {
      
          echo "<tr>";
            echo "<td>";
              echo "<font class=fonte>Empresa: TODAS</font>";
            echo "</td>";
          echo "</tr>";

      }

      echo "<tr>";
        echo "<td>";
          echo "<font class=fonte>Data inicial: ".gCorrigeData($Data_Inicial)."</font>";
        echo "</td>";
      echo "</tr>";
      echo "<tr>";
        echo "<td>";
          echo "<font class=fonte>Data Final: ".gCorrigeData($Data_Final)."</font>";
        echo "</td>";
      echo "</tr>";

      $T002=mysqli_query("Select T002_Data_Emissao,
                                T002_Data_Vencimento,
                                sum(T002_Valor_Receber) T002_Valor_Receber,
                                sum(T002_Valor_Recebido) T002_Valor_Recebido,
                                sum(T005_Valor_Romaneio) T005_Valor_Romaneio,
                                sum(T002_Valor_Pagar) T002_Valor_Pagar,
                                sum(T002_Valor_Pago) T002_Valor_Pago,
                                sum(Projecao_RC) Projecao_RC,
                                sum(Projecao_PG) Projecao_PG,
                                sum(Projecao_PG_Projetado) Projecao_PG_Projetado

                         from

                        ((select T002_Data_Emissao,
                                 if(T117_Data_Vencimento is not NULL,T117_Data_Vencimento,T002_Data_Vencimento) T002_Data_Vencimento,
                                 sum(T002_Valor_Total) T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            from T002
                       left join T117 on T117_Id=T002_T117_Id     
                           where T002_Data_Vencimento>='$Data_Inicial'
                             and T002_Data_Vencimento<='$Data_Final'
                             and T002_Data_Recebimento='0000-00-00'
                             and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
                             and T002_Flag_Status!='2'
                                 $Filtro_Empresa1
                                 $Filtro_Cobranca
                        group by if(T117_Data_Vencimento is not NULL,T117_Data_Vencimento,T002_Data_Vencimento))

                         union all

                         (select T019_Data_Lancamento,
                                 T019_Data_Lancamento,
                                 0 T002_Valor_Receber,
                                 sum(T019_Valor_Lancamento) T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            from T019
                           where T019_Data_Lancamento>='$Data_Inicial'
                             and T019_Data_Lancamento<='$Data_Final'
                             and T019_Valor_Lancamento>0
                                 $Filtro_Empresa5
                        group by T019_Data_Lancamento)

                         union all

                         (select T005_Data_Emissao,
                                 T005_Data_Emissao,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 sum(T005_Valor_Total-ifnull((select sum(T019_Valor_Lancamento) from T019 where T019_T005_Id=T005_Id),0)) T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            from T005
                           where T005_Data_Emissao>='$Data_Inicial'
                             and T005_Data_Emissao<='$Data_Final'
                             and T005_Flag_Status=7
                                 $Filtro_Empresa4
                        group by T005_Data_Emissao)

                         union all

                         (select T015_Data_Emissao,
                                 T015_Data_Vencimento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 sum(T015_Valor_Total) T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            from T015
                           where T015_Data_Vencimento>='$Data_Inicial'
                             and T015_Data_Vencimento<='$Data_Final'
                             and T015_Data_Pagamento='0000-00-00'
                             and (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL)
                                 $Filtro_Empresa2
                        group by T015_Data_Vencimento)

                         union all

                         (select T019_Data_Lancamento,
                                 T019_Data_Lancamento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 sum(T019_Valor_Lancamento) T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            from T019
                           where T019_Data_Lancamento>='$Data_Inicial'
                             and T019_Data_Lancamento<='$Data_Final'
                             and T019_Valor_Lancamento<0
                                 $Filtro_Empresa5
                        group by T019_Data_Lancamento)

                        union all

                         (select concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 sum(T018_Valor_Saldo) Projecao_RC,
                                 0 Projecao_PG,
                                 0 Projecao_PG_Projetado
                            from T018
                       left join D014 on D014_Id=T018_D014_Id
                           where concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)>='$Data_Inicial'
                             and concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)<='$Data_Final'
                             and D014_Flag_Tipo='RC'
                                 $Filtro_Empresa3
                             and T018_Valor_Saldo>0    
                        group by concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento))

                        union all

                         (select concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 0 Projecao_PG,
                                 sum(T018_Valor_Saldo) Projecao_PG_Projetado
                            from T018
                       left join D014 on D014_Id=T018_D014_Id
                           where concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)>='$Data_Inicial'
                             and concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)<='$Data_Final'
                             and D014_Flag_Tipo='PG'
                                 $Filtro_Empresa3
                             and ((D014_Flag_Pagamento_Unico!='S' AND T018_Valor_Saldo > 0) OR (D014_Flag_Pagamento_Unico='S' AND (T018_Valor_Pagar+T018_Valor_Pago)<=0 AND T018_Valor_Saldo>0))
                        group by concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento))

                        union all

                         (select concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) T018_Data_Vencimento,
                                 0 T002_Valor_Receber,
                                 0 T002_Valor_Recebido,
                                 0 T005_Valor_Romaneio,
                                 0 T002_Valor_Pagar,
                                 0 T002_Valor_Pago,
                                 0 Projecao_RC,
                                 sum(T018_Valor_Saldo) Projecao_PG,
                                 0 Projecao_PG_Projetado
                            from T018
                       left join D014 on D014_Id=T018_D014_Id
                           where concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)>='$Data_Inicial'
                             and concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)<='$Data_Final'
                             and concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento)>=curdate()
                             and D014_Flag_Tipo='PG'
                                 $Filtro_Empresa3
                             and T018_Valor_Saldo>0    
                        group by concat(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento))) B

                            group by T002_Data_Vencimento");

      echo mysql_error();

         //
         // CABECALHO DAS COLUNAS
         //

   echo "<tr>";
    echo "<td>";

         echo "<table border=0 cellspacing=2 width=100% cellpadding=2>";

          echo "<tr bgcolor='silver'>";
           echo "<td align=center class='fonte' bgcolor='white' colspan=2>";
            echo "&nbsp;";
           echo "</td>";
           echo "<td align=center class='fonte' colspan=6>";
            echo "A Realizar";
           echo "</td>";
           echo "<td align=center class='fonte' style='border-left:8px solid white' colspan=3>";
            echo "Realizado";
           echo "</td>";
         echo "</tr>";
         echo "<tr bgcolor='silver'>";
           echo "<td align=center width='70' class='fonte'>";
            echo "Vencimento";
           echo "</td>";
           echo "<td align=center width='10' class='fonte'>";
            echo "Dia";
           echo "</td>";
           echo "<td align=right width='80' class='fonte'>";
            echo "A Receber (+)";
           echo "</td>";
           echo "<td align=right width='90' class='fonte'>";
            echo "Projeção (+)";
           echo "</td>";
           echo "<td align=right width='80' class='fonte'>";
            echo "A Pagar (-)";
           echo "</td>";
           echo "<td align=right width='80' class='fonte'>";
            echo "Projeção (-)";
           echo "</td>";
           echo "<td align=right width='70' class='fonte'>";
            echo "Saldo Proj";
           echo "<td align=right width='70' class='fonte'>";
            echo "Saldo Real";
           echo "</td>";
           echo "<td align=right width='80' class='fonte' style='border-left:8px solid white'>";
            echo "Recebido (+)";
           echo "</td>";
           echo "<td align=right width='80' class='fonte'>";
            echo "Pago (-)";
           echo "</td>";
           echo "<td align=right width='80' class='fonte'>";
            echo "Saldo";
           echo "</td>";
         echo "</tr>";
         
           $cor='#E5E5E5';
           
    if ($Saldo_Bancario=='S')
    {
        
          $D007=mysqli_query("select * from D007 where D007_Flag_Ativo='S' $Filtro_Empresa6");
          while ($mD007=mysql_fetch_array($D007))
          {
              if ($mD007['D007_Flag_Fluxo_Caixa']=='S') {
                  if ($cor=='#E5E5E5')
                  {
                      $cor='white';
                  }
                  else
                  {
                      $cor='#E5E5E5';
                  }
                  echo "<tr bgcolor=$cor>";
                   echo "<td align=right class='fonte' colspan=5>";
                    echo 'Saldo '.$mD007['D007_Nome_Banco'];  
                   echo "</td>";
                   echo "<td align=right class='fonte' colspan=2>";
                    echo number_format($mD007['D007_Valor_Saldo'],2,',','.');
                   echo "</td>";
                   echo "<td align=right class='fonte' style='border-left:8px solid white'>";
                   echo "</td>";
                   echo "<td align=right class='fonte' >";
                   echo "</td>";
                   echo "<td align=right class='fonte' >";
                   echo "</td>";
                   echo "<td align=right class='fonte' >";
                   echo "</td>";
                  echo "</tr>";
                  $Saldo_Real+=$mD007['D007_Valor_Saldo'];
                  $Saldo_Projetado+=$mD007['D007_Valor_Saldo'];
              }
          }
    }

    while ($mT002=mysql_fetch_array($T002)) {
    
	      if ($cor=='#E5E5E5')
	      {
	           $cor='white';
	      }
	      else
	      {
	           $cor='#E5E5E5';
	      }
	       
	      if ($mT002['T002_Data_Vencimento']==gCorrigeDataInverte(date("d/m/Y")))
	      {
	      	   $cor="lightblue";
	      }
	      
	      if ($mT002['T002_Data_Vencimento']>=gCorrigeDataInverte(date("d/m/Y")))
	      { 
             $Saldo_Real    += ($mT002['T002_Valor_Receber']+$mT002['T005_Valor_Romaneio']+$mT002['Projecao_RC'])-($mT002['T002_Valor_Pagar']+$mT002['Projecao_PG']);
          }
          $Saldo_Projetado  += ($mT002['T002_Valor_Receber']+$mT002['T005_Valor_Romaneio']+$mT002['Projecao_RC'])-($mT002['T002_Valor_Pagar']+$mT002['Projecao_PG_Projetado']);
          $Saldo_Realizado  += $mT002['T002_Valor_Recebido']+$mT002['T002_Valor_Pago'];
          
          echo "<tr bgcolor=$cor>";
           echo "<td align=center class='fonte' >";
            echo gCorrigeData($mT002['T002_Data_Vencimento']);
           echo "</td>";
           echo "<td align=center class='fonte' >";
            echo substr(diasem($mT002['T002_Data_Vencimento']),0,3);
           echo "</td>";
           echo "<td align=right class='fonte' >";
            echo number_format($mT002['T002_Valor_Receber']+$mT002['T005_Valor_Romaneio'],2,',','.');
           echo "</td>";
           echo "<td align=right class='fonte' >";
            echo number_format($mT002['Projecao_RC'],2,',','.');
           echo "</td>";
           echo "<td align=right class='fonte' >";
            echo number_format($mT002['T002_Valor_Pagar'],2,',','.');
           echo "</td>";
           echo "<td align=right class='fonte' >";
            echo number_format($mT002['Projecao_PG_Projetado'],2,',','.');
           echo "</td>";
           echo "<td align=right class='fonte' >";
            echo number_format($Saldo_Projetado,2,',','.');
           echo "</td>";
           echo "<td align=right class='fonte' >";
            echo number_format($Saldo_Real,2,',','.');
           echo "</td>";
           echo "<td align=right class='fonte' style='border-left:8px solid white'>";
            echo number_format($mT002['T002_Valor_Recebido'],2,',','.');
           echo "</td>";
           echo "<td align=right class='fonte' >";
            echo number_format($mT002['T002_Valor_Pago'],2,',','.');
           echo "</td>";
           echo "<td align=right class='fonte' >";
            echo number_format($Saldo_Realizado,2,',','.');
           echo "</td>";
          echo "</tr>";
          
          $mTotal[0]+=$mT002['T002_Valor_Receber']+$mT002['T005_Valor_Romaneio'];
          $mTotal[1]+=$mT002['T002_Valor_Pagar'];
          $mTotal[2]+=$mT002['Projecao_RC'];
          $mTotal[3]+=$mT002['Projecao_PG'];
          $mTotal[4]+=$mT002['T002_Valor_Recebido'];
          $mTotal[5]+=$mT002['T002_Valor_Pago'];

     }

          echo "<tr bgcolor='white'>";
           echo "<td align=center colspan=2>";
            echo "<font class=fonte>Total</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fonte>".number_format($mTotal[0],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fonte>".number_format($mTotal[2],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fonte>".number_format($mTotal[1],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fonte>".number_format($mTotal[3],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fonte>&nbsp;</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fonte>&nbsp;</font>";
           echo "</td>";
           echo "<td align=right style='border-left:8px solid white'>";
            echo "<font class=fonte>".number_format($mTotal[4],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fonte>".number_format($mTotal[5],2,',','.')."</font>";
           echo "</td>";
           echo "<td align=right>";
            echo "<font class=fonte>&nbsp;</font>";
           echo "</td>";
          echo "</tr>";

    echo "</table>";

?>
