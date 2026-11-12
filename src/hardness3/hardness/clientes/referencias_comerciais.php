<?php

namespace hardness;
global $g;
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');

include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";
include "../class/calendario.php";
$oCalendario = new Calendario();

if (empty($Data_Inicio))
{
    //-- Obtem o primeiro dia do mes se o mesmo nao estiver preenchido
	$cMes='01';
	$cAno=date("Y",time());
	$Data_Inicio=date("01/m/Y", mktime(0, 0, 0, $cMes, 1, $cAno));
}
if (empty($Data_Fim))
{
    //-- Obtem o ultimo dia do mes se o mesmo nao estiver preenchido
	$cMes=date("m",time());
	$cAno=date("Y",time());
	$Data_Fim=date("t/m/Y", mktime(0, 0, 0, $cMes, 1, $cAno));
}

$xData_Inicial=gCorrigeDataInverte($Data_Inicio);
$xData_Final=gCorrigeDataInverte($Data_Fim);
// $D024_Id=$_GET['D024_Id'];
// $Abre_Janela=$_GET['Abre_Janela'];
// $Voltar=$_GET['Voltar'];

    echo "<html>";
    echo "<title>Referências Comerciais</title>";
    echo "<head>";
    echo "<link rel=stylesheet href='../classes/classe_tamanho_medio_firefox.css' type='text/css'>";

    echo "
    <style>
    @media print {

    #instructions {
    	height: 1px;
    	visibility: hidden;
    	overflow: hidden;
    	font-size: 10px;
    	padding-bottom: 4px;
    }

    </style>
    <style>
    .fonte_local{
     font-size: 12;
     font-family: arial, sans-serif, verdana, helvetica, sans-serif;
     color:black;
    }
    </style>
    ";

    echo "</head>";

    echo "<script src='../func/funcoes_genericas.js'></script>";
    echo "<script src='../func/ajax.js'></script>";


    echo "<body bgcolor=#F2F2EE leftmargin=0px topmargin=0px rightmargin=0px bottommargin=0px>";

    if (empty($D024_Id))
    {
       echo "<p align=center class=titulo><big><b>Selecione um Cliente</b></big></p>";
       exit;
    }

   $D024=mysqli_query("select D024_Nome_Empresa,D024_Data_Cadastro from D024 where D024_Id='$D024_Id'");
   $mD024=mysql_fetch_array($D024);
   
   $T007=mysqli_query("select T007_Valor_Total,
                             T007_Data_Emissao
                        from T007
                       where T007_D024_Id='$D024_Id'
                         and T007_C004_Id='$Empresa_Atual'
                         and T007_Data_Emissao>='$xData_Inicial'
                         and T007_Data_Emissao<='$xData_Final'
                         and T007_Flag_Cancelada='N'
                    order by T007_Valor_Total desc, T007_Data_Emissao desc
                       limit 1");
   $mT007_Maior_Compra=mysql_fetch_array($T007);

   $T005=mysqli_query("select T005_Valor_Total,
                             T005_Data_Emissao
                        from T005
                       where T005_D024_Id='$D024_Id'
                         and T005_C004_Id='$Empresa_Atual'
                         and T005_Data_Emissao>='$xData_Inicial'
                         and T005_Data_Emissao<='$xData_Final'
                         and (T005_Flag_Status='7' or T005_Flag_Status='11')
                         and T005_Flag_Situacao='1'
                    order by T005_Valor_Total desc
                       limit 1");
   $mT005_Maior_Compra=mysql_fetch_array($T005);
   
   $T008=mysqli_query("(select T008_Codigo_Produto,
                             T008_Descricao_Produto,
                             sum(T008_Quantidade) as T008_Quantidade,
                             sum(T008_Valor_Total_Preco) as T008_Faturado,
                             T008_Unidade
                        from T007
                   left join T008 on T008_T007_Id=T007_Id
                       where T007_D024_Id='$D024_Id'
                         and T007_C004_Id='$Empresa_Atual'
                         and T007_Data_Emissao>='$xData_Inicial'
                         and T007_Data_Emissao<='$xData_Final'
                         and T007_Flag_Cancelada='N'
                    group by T008_Codigo_Produto,T008_Unidade
                    order by T008_Descricao_Produto)

                    union

                    (select T006_Codigo_Produto as T008_Codigo_Produto,
                             T006_Descricao_Produto as T008_Descricao_Produto,
                             sum(T006_Quantidade) as T008_Quantidade,
                             sum(T006_Valor_Total_Preco) as T008_Faturado,
                             D037_Unidade as T008_Unidade
                        from T005
                   left join T006 on T006_T005_Id=T005_Id
                   left join D037 on T006_D037_Id=D037_Id
                       where T005_D024_Id='$D024_Id'
                         and T005_C004_Id='$Empresa_Atual'
                         and T005_Data_Emissao>='$xData_Inicial'
                         and T005_Data_Emissao<='$xData_Final'
                         and (T005_Flag_Status='7' or T005_Flag_Status='11')
                         and T005_Flag_Situacao='1'
                    group by T006_Codigo_Produto,D037_Unidade
                    order by T006_Descricao_Produto)
                    order by T008_Descricao_Produto");


   $T007_Mes=mysqli_query("select Concat(Month(T007_Data_Emissao),'/',Year(T007_Data_Emissao)),
                             sum(T007_Valor_Total) as Total_Mensal
                        from T007
                       where T007_D024_Id='$D024_Id'
                         and T007_C004_Id='$Empresa_Atual'
                         and T007_Data_Emissao>='$xData_Inicial'
                         and T007_Data_Emissao<='$xData_Final'
                         and T007_Flag_Cancelada='N'
                    group by Year(T007_Data_Emissao),Month(T007_Data_Emissao)
                    order by Year(T007_Data_Emissao),Month(T007_Data_Emissao)");
                    
   echo mysql_error();

   $T005_Mes=mysqli_query("select Concat(Month(T005_Data_Emissao),'/',Year(T005_Data_Emissao)),
                                 sum(T005_Valor_Total) Total_Mensal
                                from T005
                               where T005_D024_Id='$D024_Id'
                                 and T005_C004_Id='$Empresa_Atual'
                                 and T005_Data_Emissao>='$xData_Inicial'
                                 and T005_Data_Emissao<='$xData_Final'
                                 and (T005_Flag_Status='7' or T005_Flag_Status='11')
                                 and T005_Flag_Situacao='1'
                                 group by Year(T005_Data_Emissao),Month(T005_Data_Emissao)");

   echo mysql_error();

   $T007=mysqli_query("select T007_Valor_Total,
                             T007_Data_Emissao
                        from T007
                       where T007_D024_Id='$D024_Id'
                         and T007_C004_Id='$Empresa_Atual'
                         and T007_Data_Emissao>='$xData_Inicial'
                         and T007_Data_Emissao<='$xData_Final'
                         and T007_Flag_Cancelada!='S'
                         and T007_Valor_Total>0
                    order by T007_Data_Emissao desc
                       limit 1");
   $mT007_Ultima_Compra=mysql_fetch_array($T007);

   $T005=mysqli_query("select T005_Valor_Total,
                             T005_Data_Emissao
                        from T005
                       where T005_D024_Id='$D024_Id'
                         and T005_C004_Id='$Empresa_Atual'
                         and T005_Data_Emissao>='$xData_Inicial'
                         and T005_Data_Emissao<='$xData_Final'
                         and (T005_Flag_Status='7' or T005_Flag_Status='11')
                         and T005_Flag_Situacao='1'
                    order by T005_Data_Emissao desc
                       limit 1");
   $mT005_Ultima_Compra=mysql_fetch_array($T005);

   $T002=mysqli_query("select count(*)
                        from T002
                       where T002_D024_Id='$D024_Id'
                         and T002_C004_Id='$Empresa_Atual'
                         and T002_Data_Emissao>='$xData_Inicial'
                         and T002_Data_Emissao<='$xData_Final'
                         and T002_Flag_Cancelada='N'");
   $mT002_Total=mysql_fetch_array($T002);

   $T005=mysqli_query("select count(*)
                        from T005
                       where T005_D024_Id='$D024_Id'
                         and T005_C004_Id='$Empresa_Atual'
                         and T005_Data_Emissao>='$xData_Inicial'
                         and T005_Data_Emissao<='$xData_Final'
                         and (T005_Flag_Status='7' or T005_Flag_Status='11')
                         and T005_Flag_Situacao='1'");
   $mT005_Total=mysql_fetch_array($T005);

   $T002_Total_Aberto=mysqli_query("select sum(T002_Valor_Total)
                        from T002
                       where T002_Data_Recebimento='0000-00-00'
                         and T002_C004_Id='$Empresa_Atual'
                         and T002_Data_Emissao>='$xData_Inicial'
                         and T002_Data_Emissao<='$xData_Final'
                         and T002_D024_Id='$D024_Id'
                         and T002_Flag_Cancelada='N'");
   $mT002_Total_Aberto=mysql_fetch_array($T002_Total_Aberto);

   $T005_Total_Romaneio=mysqli_query("select sum(T005_Valor_Total)
                        from T005
                       where T005_Data_Emissao>='$xData_Inicial'
                         and T005_C004_Id='$Empresa_Atual'
                         and T005_Data_Emissao<='$xData_Final'
                         and T005_D024_Id='$D024_Id'
                         and (T005_Flag_Status='7' or T005_Flag_Status='11')
                         and T005_Flag_Situacao='1'");
   echo mysql_error();
   $mT005_Total_Romaneio=mysql_fetch_array($T005_Total_Romaneio);
   
   $T002_Total_Atrasado=mysqli_query("select sum(T002_Valor_Total) as t1
                        from T002
                       where current_date()>T002_Data_Vencimento
                         and T002_C004_Id='$Empresa_Atual'
                         and T002_Data_Recebimento='0000-00-00'
                         and T002_Data_Emissao>='$xData_Inicial'
                         and T002_Data_Emissao<='$xData_Final'
                         and T002_D024_Id='$D024_Id'
                         and T002_Flag_Cancelada='N'");
   echo mysql_error();
   $mT002_Total_Atrasado=mysql_fetch_array($T002_Total_Atrasado);
   


   $T002_Media_Atraso=mysqli_query("select count(*),
                                          sum(datediff(T002_Data_Recebimento,T002_Data_Vencimento))
                        from T002
                       where T002_Data_Recebimento>T002_Data_Vencimento
                         and T002_C004_Id='$Empresa_Atual'
                         and T002_Data_Emissao>='$xData_Inicial'
                         and T002_Data_Emissao<='$xData_Final'
                         and T002_D024_Id='$D024_Id'
                         and T002_Flag_Cancelada='N'");
   $mT002_Media_Atraso=mysql_fetch_array($T002_Media_Atraso);
   
   $T002_Maior_Atraso=mysqli_query("select datediff(T002_Data_Recebimento,T002_Data_Vencimento)
                        from T002
                       where T002_Data_Recebimento>T002_Data_Vencimento
                         and T002_C004_Id='$Empresa_Atual'
                         and T002_D024_Id='$D024_Id'
                         and T002_Data_Emissao>='$xData_Inicial'
                         and T002_Data_Emissao<='$xData_Final'
                         and T002_Flag_Cancelada='N'
                    order by datediff(T002_Data_Recebimento,T002_Data_Vencimento) desc");
   $mT002_Maior_Atraso=mysql_fetch_array($T002_Maior_Atraso);

   echo mysql_error();

   $T002_Media_EmDia=mysqli_query("select count(*)
                        from T002
                       where T002_Data_Recebimento<=T002_Data_Vencimento
                         and T002_C004_Id='$Empresa_Atual'
                         and T002_Data_Emissao>='$xData_Inicial'
                         and T002_Data_Emissao<='$xData_Final'
                         and T002_D024_Id='$D024_Id'
                         and T002_Flag_Cancelada='N'");
   $mT002_Media_EmDia=mysql_fetch_array($T002_Media_EmDia);

   $T002=mysqli_query("select D027_Portador,
                             count(*)
                        from T002
                   left join D027 on D027_Id=T002_D027_Id
                       where T002_D024_Id='$D024_Id'
                         and T002_C004_Id='$Empresa_Atual'
                         and T002_Data_Emissao>='$xData_Inicial'
                         and T002_Data_Emissao<='$xData_Final'
                         and T002_Flag_Cancelada='N'
                    group by T002_D027_Id");
                    
   $T002_Prazo=mysqli_query("select DateDiff(T002_Data_Vencimento,T002_Data_Emissao),
                             count(*)
                        from T002
                       where T002_D024_Id='$D024_Id'
                         and T002_C004_Id='$Empresa_Atual'
                         and T002_Data_Emissao>='$xData_Inicial'
                         and T002_Data_Emissao<='$xData_Final'
                         and T002_Flag_Cancelada='N'
                    group by DateDiff(T002_Data_Vencimento,T002_Data_Emissao)");

   echo "<p align=left style='padding:0px'>";
     echo "<font class=fonte_local style='font-size:16px;font-weight:bold'><b>$mD024[D024_Nome_Empresa] <br> Rererências Comerciais</b></font>";
   echo "</p>";

         echo "<div style='border:0px solid silver; border-bottom:0px;background:#F2F2EE;padding:2px 2px 0px 2px'>";
            echo "<form name='formulario' method=post action='referencias_comerciais.php' style='margin:0px'>";
            echo "<input type='hidden' name='D024_Id' id='D024_Id' value='$D024_Id'>";
            echo "<table border=0 cellspacing=0 cellpadding=2 width=100% height=30 align=center valign=top bgcolor=#DDD6C8>";
              echo "<tr>";
                echo "<td></td>";
                echo "<td align=right width=100>";
                  echo "<font class=fonte_local>Data Inicial</font>";
                echo "</td>";
                  echo "<td align=left width=100>";
                     echo "<table border=0 cellpadding=0 cellspacing=0>";
                     	echo "<tr>";
                     		echo "<td>";
                     			echo "<input type=text name='Data_Inicio' id='Data_Inicio' value='$Data_Inicio' size=10 class=campo onKeyPress=\"return(Formatar_Data('2',this,event,'/'));\">";
            				echo "</td>";
                     		echo "<td>";
                     			$oCalendario->setNomeCampo("Data_Inicio");
            		 			$oCalendario->setNomeDiv("Div_Calendario_Data_Inicial");
            	 	 			$oCalendario->AdicionarCalendario();
                     		echo "</td>";
                     	echo "</tr>";
                     echo "</table>";
                  echo "</td>";
                echo "<td width=30>";
                  echo "";
                echo "</td>";
                echo "<td align=right width=70>";
                  echo "<font class=fonte_local>Data Final</font>";
                echo "</td>";
                echo "</td>";
                  echo "<td align=left width=100>";
                     echo "<table border=0 cellpadding=0 cellspacing=0>";
                     	echo "<tr>";
                     		echo "<td>";
                     			echo "<input type=text name='Data_Fim' id='Data_Fim' value='$Data_Fim' size=10 class=campo onKeyPress=\"return(Formatar_Data('2',this,event,'/'));\">";
            				echo "</td>";
                     		echo "<td>";
                     			$oCalendario->setNomeCampo("Data_Fim");
            		 			$oCalendario->setNomeDiv("Div_Calendario_Data_Final");
            	 	 			$oCalendario->AdicionarCalendario();
                     		echo "</td>";
                     	echo "</tr>";
                     echo "</table>";
                  echo "</td>";
                echo "<td align=center width=100>";
                  echo "<input type='submit' value='Atualizar' class=atualizar>";
                echo "</td>";
                echo "<td></td>";
              echo "</tr>";
           echo "</table>";
           
           echo "</form>";
        echo "</div>";


   echo "<table align=center border=0 width=100% cellpadding=3 cellspacing=3 style='border:0px solid silver;border-top:0px solid #F2F2EE'>";
     echo "<tr align=left>";
       echo "<td bgcolor='#DDD6C8' width=145>";
          echo "<font class=fonte_local>Data cadastro:</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<font class=fonte_local>".gCorrigeData($mD024['D024_Data_Cadastro'])."&nbsp;</font>";
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left>";
       echo "<td  bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Maior Compra</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<font class=fonte_local>R$ ".number_format($mT007_Maior_Compra[0],2,',','.')." em ".gCorrigeData($mT007_Maior_Compra[1])."</font>";
          if ($mT005_Maior_Compra[0]>0)
          {
             echo "&nbsp;&nbsp;&nbsp;";
             echo "<font class=fonte_local>  (Romaneio R$ ".number_format($mT005_Maior_Compra[0],2,',','.')." em ".gCorrigeData($mT005_Maior_Compra[1]).")</font>";
          }
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left bgcolor='white'>";
       echo "<td  bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Útima Compra</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<font class=fonte_local>R$ ".number_format($mT007_Ultima_Compra[0],2,',','.')." em ".gCorrigeData($mT007_Ultima_Compra[1])."</font>";
          if ($mT005_Ultima_Compra[0]>0)
          {
             echo "&nbsp;&nbsp;&nbsp;";
             echo "<font class=fonte_local>  (Romaneio R$ ".number_format($mT005_Ultima_Compra[0],2,',','.')." em ".gCorrigeData($mT005_Ultima_Compra[1]).")</font>";
          }
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left  bgcolor='white'>";
       echo "<td bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Compras por mês (NF)</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<table bgcolor='white' cellspacing=1 cellpadding=2>";
          echo "<tr bgcolor=white>
                   <td><font class=fonte_local>Mês</font></td>
                   <td align=right width=80><font class=fonte_local>NF</td>
                </tr>" ;
          while ($mT007_Mes=mysql_fetch_array($T007_Mes))
          {
                 echo "<tr bgcolor=white>
                          <td><font class=fonte_local>$mT007_Mes[0]</font></td>
                          <td align=right width=80><font class=fonte_local> ".number_format($mT007_Mes['Total_Mensal'],2,',','.')."  </td>
                       </tr>" ;
          }
          echo "</table>";
       echo "</td>";
     echo "</tr>";
     if (mysql_num_rows($T005_Mes)>0)
     {
	     echo "<tr align=left  bgcolor='white'>";
	       echo "<td bgcolor='#DDD6C8'>";
	          echo "<font class=fonte_local>Compras por mês (Romaneio)</font>";
	       echo "</td>";
	       echo "<td bgcolor='white'>";
	          echo "<table bgcolor='white' cellspacing=1 cellpadding=2>";
	          echo "<tr bgcolor=white>
	                   <td><font class=fonte_local>Mês</font></td>
	                   <td align=right width=80><font class=fonte_local>Romaneios</td>
	                </tr>" ;
	          while ($mT005_Mes=mysql_fetch_array($T005_Mes))
	          {
	                 echo "<tr bgcolor=white>
	                          <td><font class=fonte_local>$mT005_Mes[0]</font></td>
	                          <td align=right width=80><font class=fonte_local> ".number_format($mT005_Mes[Total_Mensal],2,',','.')."  </td>
	                       </tr>" ;
	          }
	          echo "</table>";
	       echo "</td>";
	     echo "</tr>";
     }
     echo "<tr align=left bgcolor='white'>";
       echo "<td bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Forma de pagamento</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<table>";
          while ($mT002=mysql_fetch_array($T002)) {
                 echo "<tr><td align=right><font class=fonte_local>".number_format(($mT002[1]/$mT002_Total[0])*100,2,',','.')."% </td><td><font class=fonte_local> $mT002[0]</font></td></tr>";
          }
          echo "</table>";
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left>";
       echo "<td bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Prazo de pagamento</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<table>";
          while ($mT002_Prazo=mysql_fetch_array($T002_Prazo)) {
                 echo "<tr><td align=right><font class=fonte_local>".number_format(($mT002_Prazo[1]/$mT002_Total[0])*100,2,',','.')."% </td><td><font class=fonte_local>$mT002_Prazo[0] dias</font></td></tr>";
          }
          echo "</table>";
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left>";
       echo "<td bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Pagamento</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<table>";
             if ($mT002_Media_Atraso[0]>0 and $mT002_Total[0]>0)
             {
                 echo "<tr><td align=right><font class=fonte_local>".number_format(($mT002_Media_Atraso[0]/$mT002_Total[0])*100,2,',','.')."% </td><td><font class=fonte_local> Pago Atrasado (média dias ".number_format($mT002_Media_Atraso[1]/$mT002_Media_Atraso[0],0).")</font></td></tr>";
             }
             if ($mT002_Media_EmDia[0]>0 and $mT002_Total[0]>0)
             {
                 echo "<tr><td align=right><font class=fonte_local>".number_format(($mT002_Media_EmDia[0]/$mT002_Total[0])*100,2,',','.')."% </td><td><font class=fonte_local> Pago em dia</font></td></tr>";
             }
          echo "</table>";
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left>";
       echo "<td bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Maior atraso</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<font class=fonte_local>$mT002_Maior_Atraso[0] dias</font>";
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left>";
       echo "<td bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Total a pagar</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<font class=fonte_local>R$ ".number_format($mT002_Total_Aberto[0],2,',','.');
          if ($mT005_Total_Romaneio[0]>0)
          {
             echo " + (Romaneios: R$ ".number_format($mT005_Total_Romaneio[0],2,',','.').") ";
          }
          echo "</font>";
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left>";
       echo "<td bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Total atrasado</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<font class=fonte_local>R$ ".number_format($mT002_Total_Atrasado[0],2,',','.')."</font>";
       echo "</td>";
     echo "</tr>";
     echo "<tr align=left>";
       echo "<td bgcolor='#DDD6C8'>";
          echo "<font class=fonte_local>Principais Produtos</font>";
       echo "</td>";
       echo "<td bgcolor='white'>";
          echo "<table bgcolor='white' cellspacing=1>";
          $count=0;
          echo "<tr bgcolor=white><td align=center><font class=fonte_local>Código</font></td><td width=300><font class=fonte_local>Descrição</font></td><td align=right><font class=fonte_local>Quantidade</td><td align=center><font class=fonte_local>Und</td><td align=right><font class=fonte_local>Faturado</td></tr>" ;
          while ($mT008=mysql_fetch_array($T008)) {
                 echo "<tr bgcolor=white><td align=left><font class=fonte_local>$mT008[0]</font></td><td width=300><font class=fonte_local>".substr($mT008[1],0,40)."</font></td><td align=right><font class=fonte_local> ".number_format($mT008[T008_Quantidade],2,',','.')."  </td><td align=center><font class=fonte_local>$mT008[T008_Unidade]</td><td align=right><font class=fonte_local> ".number_format($mT008[T008_Faturado],2,',','.')."  </td></tr>" ;
                 $nTotal_Geral+=$mT008['T008_Faturado'];
          }
          echo "<tr bgcolor=white><td colspan=2><font class=fonte_local  style='font-weight:bold'>".mysql_num_rows($T008)." produtos fornecidos</font></td><td align=right width=80><font class=fonte_local></td><td align=right width=80><font class=fonte_local></td><td align=right width=80><font class=fonte_local style='font-weight:bold'>".number_format($nTotal_Geral,2,',','.')."</td></tr>" ;
          echo "</table>";
       echo "</td>";
     echo "</tr>";
   echo "</table>";
   
   echo "<div id='instructions'>";
      echo "<p align=center><input type='button' class='botao' value='Fechar' onclick='self.close();'></p>";
   echo "</div>";

?>
