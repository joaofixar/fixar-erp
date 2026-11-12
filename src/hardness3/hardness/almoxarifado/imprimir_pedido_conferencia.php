<?php
namespace hardness;
global $g;
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
include "../include/pasta_usuario.inc";

include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";
$cArquivo_Cabecalho_Relatorio = "../cabecalho_impressoes.php";

$Cor_Normal="#ffffff";
header ('Content-type: text/html; charset=UTF-8');

$Linhas_Normais=30;
$Linhas_Ultima_Pagina=25;
$Tamanho_Maximo_Tabela=580;
$Tamanho_Minimo_Tabela=400;
$Width_Relatorio='98%';

// Verifica se o usuario � um administrador
$cSql_C007=mysqli_query("select * from C007 where C007_Id='$Usuario_Atual'");
$mC007=mysql_fetch_array($cSql_C007);

$cSql_C004=mysqli_query("select * from C004 where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($cSql_C004);
$mC004['C004_Observacao_Promocao']=str_replace("\n","<br>",$mC004['C004_Observacao_Promocao']);

$cSql_T005=mysqli_query("select * from T005
                            left join D024 on D024_Id=T005_D024_Id
                            left join D020 on D020_Id=D024_D020_Id
                            left join D018 on D018_Id=D020_D018_Id
                            left join D021 on D021_Id=T005_D021_Id
                            left join D022 on D022_Id=T005_D022_Id
          left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
                                where T005_Id='{$T005_Id}'");
$mT005=mysql_fetch_array($cSql_T005);

$mT005['T005_Data_Emissao']=gCorrigeData($mT005['T005_Data_Emissao']);
$mT005['T005_Observacao']=str_replace("\n","<br>",trim($mT005['T005_Observacao']));

$cSql_Prazos=mysqli_query("select T005_Prazos('{$T005_Id}') as Prazos");
$mPrazos=mysql_fetch_array($cSql_Prazos);

$cClausula="select T006_Item,
                   T006_Quantidade,
                   if(T006_Codigo_Substituto!='',T006_Codigo_Substituto,T006_Codigo_Produto) as T006_Codigo_Produto_Substituto,
                   T006_Codigo_Produto,
                   T006_Codigo_Substituto,
                   T006_Descricao_Produto,
                   D082_Marca,
                   D009_Quantidade_Estoque,
                   T006_Flag_Status,
                   T006_Quantidade_Separacao Separado,
                   T006_Quantidade_Estoque,
                   T006_Peso_Unitario,
                   T006_Id,
                   T005_Id,
                   T006_Estoque_BOX,
                   D037_Unidade, 
                   group_concat(D004_Local) D004_Local
              from T006
         left join T005 on T005_Id=T006_T005_Id
         left join D009 on D009_Id=T006_D009_Id
         left join T066 on T066_D009_Id=D009_Id
         left join D004 on D004_Id=T066_D004_Id
         left join D049 on D049_Id=D009_D049_Id
         left join C004 on C004_Id=T006_C004_Id
         left join D024 on D024_Id=D009_D024_Id
         left join D005 on D005_Id=T006_D005_Id
         left join D082 on D082_Id=D049_D082_Id
         left join D001 on D049_D001_Id=D001_Id 
         left join D037 on D037_Id=D001_D037_Id 
             where T006_T005_Id='$T005_Id'
          group by T006_Id
          order by D004_Local";
$Resultado=mysqli_query($cClausula);

echo mysql_error();

echo "<html>";
echo "<head>";
echo "<title>Pedido venda Nr $T005_Id - $mT005[D024_Nome_Empresa]</title>";

echo "<link rel=stylesheet href=\"../classes/classe_tamanho_pequeno_firefox.css\" type=\"text/css\">";

echo "<style>

.fonte
{
font-family:arial,verdana;
font-size:11px;
}

</style>";


echo "</head>";
echo "<body bgcolor=white leftmargin='0' topmargin='0'>";


////////////////////////////////////////////////////////////
// VERIFICA QUANTAS PAGINAS SER�O IMPRESSAS
////////////////////////////////////////////////////////////
// Encontra o total das linhas a ser impressa
$Total_Linhas=mysql_num_rows($Resultado);
if (!empty($mT005['T005_Mao_Obra_1'])) {
	$Total_Linhas+=4;
}

if ($Total_Linhas==0) {
   //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS � DEFINIDO PARA 1,
   //-- PARA QUE SEJA IMPRESSO O CABECALHO.
   $Total_Linhas=1;
}

if ($Total_Linhas>=1) {

    if (count($mLimit)==0) {
        // Encontra o numero de paginas cheias
        $Total_Paginas = intval($Total_Linhas / $Linhas_Normais);
        // Encontra o numero de itens restantes
        if ($Total_Paginas>=1) {
          $Resto = ($Total_Linhas - ($Total_Paginas * $Linhas_Normais));
        } else {
          $Resto = $Total_Linhas;
        }

        // Verifica caso o resto seja maior que o n�mero m�ximo de itens que cabe na �ltima folha
        if ($Resto > $Linhas_Ultima_Pagina) {
          // Sendo maior soma mais duas folhas
          $Total_Paginas += 2 ;
        } else {
          // Sendo menor soma mais uma folha
          $Total_Paginas += 1 ;
        }
    } else {
        $Total_Paginas=count($mLimit);

        $Linhas_Normais=$mLimit[0];
    }
}

//////////////////////////////////////////////////////////////

// Inicia com o tamanho maximo estabelecido
$Tamanho_Tabela_Itens=$Tamanho_Maximo_Tabela;

// Inicializa Variavel utilizada no limit da consulta dos itens
$Inicio=0;
$Pagina_Atual=0;

    $Pagina_Atual++;

       echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='735' bgcolor=white style='border:0px solid black'>";
         echo "<tr bgcolor=white>";
          echo "<td valign=top>";
           echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top style='border:0px solid'>";
            echo "<tr>";
             echo "<td bgcolor=white width=80%>";
                //-- $cArquivo_Cabecalho_Relatorio � definido no arquivo include/pasta_usuario.inc
                include $cArquivo_Cabecalho_Relatorio;
             echo "</td>";
             echo "<td bgcolor=white align=center width=20%>";
                  echo "<font class='titulo_relatorio'>";
               echo "PEDIDO<BR>Nr. $T005_Id";
                  echo "</font>";
               echo "</font>";
               echo "<br><font class=fonte>".hoje()."</font>";
             echo "</td>";
           echo "</tr>";
           echo "<tr bgcolor=white>";
             echo "<td colspan=2>";
               echo "<table cellpadding=7 width=100%>";
                 echo "<tr bgcolor=white>";
                   echo "<td colspan=3>";
                   echo "<font class='fonte'></font><br> ";
                          echo "<font class='fonte'>Vendedor:</font> ";
                          echo "<font class='fonte' style='font-size:12px'>$mT005[C007_Nome]</font><br>";

                   if ($mT005['D024_Id']>0) {
                       echo "<font class='fonte' style='font-size:12px'><b>$mT005[D024_Id] - $mT005[D024_Nome_Empresa]</b></font><br>";
                       echo "<font class='fonte' style='font-size:12px'>$mT005[D018_UF] - $mT005[D020_Nome_Cidade] - $mT005[D024_Bairro]</font><br>";
                       if (!empty($mT005['T005_Nome_Comprador'])) {
                          echo "<font class='fonte'>Comprador:</font> ";
                          echo "<font class='fonte' style='font-size:12px'>$mT005[T005_Nome_Comprador]</font> <font class=fonte>$mT005[T005_E_mail_Comprador]</font><br>";
                       }
                   } else {
                       echo "<font class='fonte' style='font-size:12px'><b>$mT005[T005_Nome_Cliente]</b></font><br>";
                       echo "<font class='fonte' style='font-size:12px'>TELEFONE: $mT005[T005_Telefone] - FAX: $mT005[T005_Fax]</font><br>";
                       if (!empty($mT005['T005_Nome_Comprador'])) {
                          echo "<font class='fonte'>Comprador:</font> ";
                          echo "<font class='fonte' style='font-size:12px'>$mT005[T005_Nome_Comprador]</font> <font class=fonte>$mT005[T005_E_mail_Comprador]</font><br>";
                       }
                   }
                   if (!empty($mT005['T005_Observacao'])) {
                      echo "<br><font class='fonte' style='font-size:12px'><b>$mT005[T005_Observacao]</b></font>";
                   }
                   if (!empty($mT005['T005_Observacao_1'])) {
                      echo "<br><font class='fonte' style='font-size:14px;background:yellow;'><b>$mT005[T005_Observacao_1]</b></font>";
                   }
		   echo "<br>";
                 echo "<td align=right>";
                   if ($mC004['C004_Flag_Empresa_Simples']=='1') {
                       echo "<table border=0 cellpadding=5 style='border:1px dotted black' bgcolor=#f4f4f4><tr><td align=center><font class=fonte style='font-size:12px;color:red'>Empresa Participante do<br><b>SUPER SIMPLES</b></td></tr></table>";
                   }
                 echo "</td>";
                 echo "</td>";
                 echo "<td colspan=2>";
               echo "</tr>";
             echo "</table>";
           echo "</td>";
         echo "</tr>";
        echo "</table>";
       echo "</td>";
     echo "</tr>";

     //////////////////////////////////////////////////////////////////////////////////////////////
     // CORPO DO RELATORIO
     //////////////////////////////////////////////////////////////////////////////////////////////
     echo "<tr valign=top height=100% bgcolor=white>";
       echo "<td valign=top>";

         echo "<table align=center cellspacing=1 cellpadding=3 border=0 width=100% bgcolor=black valign=top>";

           echo "<tr bgcolor=#f4f4f4>";
             echo "<td align='center' valign='center' width='30'";
               echo "<font class='fonte'><b>Id</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='60'";
               echo "<font class='fonte'><b>Código</b></font>";
             echo "</td>";

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Descrição Produto</b></font>";
             echo "</td>";
             
             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Un</b></font>";
             echo "</td>";               

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Marca</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Quant.</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Separado</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>BOX</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>PEDIDO</b></font>";
             echo "</td>";

           echo "</tr>";


         $cClausula_Limit = $cClausula;
         $Resultado_Itens = mysqli_query($cClausula_Limit);

         $Sequencial=0;
         $nTotal_Linhas_Impressas=0;

         while ($mResultado=mysql_fetch_array($Resultado_Itens)) {

                $mResultado['T006_Descricao_Produto']=stripslashes($mResultado['T006_Descricao_Produto']);

                unset($Style);
				if ($mResultado['T006_Flag_Status']=='3')
				{
 					//-- Item Anexado CANCELADO
                    $Style="style='text-decoration:line-through;'";
				}
                echo "<tr bgcolor=white height=25>";

                  echo "<td align='center' valign='center'>";
                    echo "<font class='fonte' $Style>$mResultado[T006_Id]</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='60'>";
                    echo "<font class='fonte' $Style>$mResultado[T006_Codigo_Produto]</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    if ($mResultado['T006_Flag_Status']==3)
                    {
                        echo "<font class='fonte'>*** ITEM CANCELADO ***</font>";
                    }
                    else
                    {
                        echo "<font class='fonte' $Style>$mResultado[T006_Descricao_Produto]</font>";
                    }
                  echo "</td>";
                  
                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>$mResultado[D037_Unidade]</font>";
                  echo "</td>";                     

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte' $Style>$mResultado[D082_Marca]</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte' $Style><b><big>".number_format($mResultado['T006_Quantidade'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte' $Style><b><big>".number_format($mResultado['Separado'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='center' valign='center'>";
                    echo "<font class='fonte' $Style><b><big>$mResultado[T006_Estoque_BOX]</b></big></font>";
                  echo "</td>";

                  echo "<td align='center' valign='center'>";
                    echo "<font class='fonte' $Style><b><big>$mResultado[T006_T005_Id]</b></big></font>";
                  echo "</td>";

                echo "</tr>";

                $nTotal_Linhas_Impressas++;

         }


echo "</table></td></tr>";

if ($Moeda=='D') {
    $mT005['T005_Valor_Total_Produtos']=$mT005['T005_Valor_Total_Produtos']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Frete']=$mT005['T005_Valor_Frete']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Mao_Obra']=$mT005['T005_Valor_Mao_Obra']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Despesas_Acessorias']=$mT005['T005_Valor_Despesas_Acessorias']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Desconto']=$mT005['T005_Valor_Desconto']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Total']=$mT005['T005_Valor_Total']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Total_IPI']=$mT005['T005_Valor_Total_IPI']/$mT005['T005_Valor_Dolar'];
}

if ($mT005['T005_Valor_Mao_Obra']>0) {

    echo "<tr bgcolor=white><td>";
    echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='$Width_Relatorio' bgcolor='white'>";

    if (!empty($mT005['T005_Mao_Obra_1'])) {
      echo "<tr bgcolor=white>";
          echo "<td valign=top colspan=10>";
            echo "<font class='fonte'>$mT005[T005_Mao_Obra_1]<br></font>";
            echo "<font class='fonte'>$mT005[T005_Mao_Obra_2]<br></font>";
            echo "<font class='fonte'>$mT005[T005_Mao_Obra_3]<br></font>";
            echo "<font class='fonte'>$mT005[T005_Mao_Obra_4]</font>";
          echo "</td>";
      echo "</tr>";

    }

    echo "</table>";

    echo "</td></tr>";

}

echo "<tr bgcolor=white><td>";


///////////////////////////////////////////////////////////////////////////////////
// TOTALIZACAO
///////////////////////////////////////////////////////////////////////////////////

$mC004['C004_Observacao_Rodape_Orcamento']=str_replace("\n","<br>",$mC004['C004_Observacao_Rodape_Orcamento']);

echo "<font class=fonte>Sistema Hardness - www.hardness.com.br</font>";


?>
</body>
</html>
