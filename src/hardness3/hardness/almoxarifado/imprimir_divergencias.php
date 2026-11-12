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

 $Resultado=mysqli_query("select T006_Flag_Excluir,
                                T006_Item,
                                T006_Quantidade,
                                T006_Codigo_Produto,
                                T006_Codigo_Substituto,
                                D082_Marca,
                                T006_Marca,
                                T006_Valor_Custo_Unitario,
                                T006_Valor_Preco_Sem_Desconto_Unitario,
                                T006_Valor_Total_Preco_Sem_Desconto,
                                T006_IPV,
                                T006_Aliquota_IPI,
                                T006_Descricao_Produto,
                                T006_Id,
                                T006_T006_Id,
                                T006_T005_Id,
                                T005_Id,
                                D009_Quantidade_Separado_3(D009_Id) Separado,
                                T005_Estoque_Box,
                                D009_Id,
                                D049_Id,
                                D001_Id,
                                T005_Flag_Status,
                                T006_Flag_Preco_Especial,
                                T006_Flag_Status,
                                T006_Peso_Unitario,
                                T006_Quantidade_Separacao,
                                T006_D049_Id,
                                D049_Flag_Nacional_Importado,
                                D009_Quantidade_Estoque,
                                T006_Observacao_Divergencia,
                                T006_Quantidade_Estoque,
                                D037_Unidade,
                                D024_Nome_Empresa,
                                T006_Flag_ST,
                                T006_ST_VA,
                                T006_Valor_Base_ICMS_Substituicao,
                                T006_Valor_ICMS_Substituicao_Retencao,
				                        D049_Flag_Substituicao_Tributaria,
				                        T006_Flag_Tipo_Produto,
				                        D006_Flag_Substituicao_Tributaria,
				                        D006_Id,
				                        D006_Codigo_CFOP,
                                T006_Flag_Situacao_Tributaria,
                                T006_Aliquota_ICMS,
                                T006_Percentual_Reducao_ICMS,
                                T006_Valor_ICMS,
                                T006_Situacao_Tributaria_COFINS,
                                T006_Aliquota_COFINS,
                                T006_Valor_COFINS,
                                T006_Situacao_Tributaria_PIS,
                                T006_Aliquota_PIS,
                                T006_Valor_PIS,
                                T006_Classificacao_Fiscal,
                                T006_Quantidade_Comprar,
                                group_concat(D004_Local) D004_Local
                           from T006
                      left join D009 on D009_Id=T006_D009_Id
                      left join D049 on D049_Id=D009_D049_Id
                      left join D082 on D082_Id=D049_D082_Id
                      left join D001 on D049_D001_Id=D001_Id
                      left join T005 on T006_T005_Id=T005_Id
                      left join D024 on D024_Id=T005_D024_Id
                      left join D006 on D006_Id=T006_D006_Id
                      left join D037 on D037_Id=D001_D037_Id
                      left join T066 on T066_D009_Id=D009_Id
                      left join D004 on D004_Id=T066_D004_Id
                      left join T001 on T001_T006_Id=T006_Id
                          where T006_Flag_Divergencia='S'
                            and T006_Flag_Status!='3'
                            and T006_C004_Id = '{$Empresa_Atual}'
                            and T006_Quantidade > T006_Quantidade_Separacao
                       group by T006_Id
                       order by T006_T005_Id,T006_Codigo_Produto");

echo mysql_error();
echo "<html>";
echo "<head>";
echo "<title>Divergências Separação</title>";

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
                  echo "<font class='fonte' style='font-size:18px'>";
               echo "Divergências<BR>Separação";
                  echo "</font>";
               echo "</font>";
               echo "<br><font class=fonte>".date("d/m/Y")."</font>";
             echo "</td>";
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
               echo "<font class='fonte'><b>Box</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='30'";
               echo "<font class='fonte'><b>Número Pedido</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='150'";
               echo "<font class='fonte'><b>Cliente</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='60'";
               echo "<font class='fonte'><b>Código</b></font>";
             echo "</td>";

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Descrição Produto</b></font>";
             echo "</td>";

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Marca</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Qte Pedido</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Estoque Atual</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Separado Geral</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Separado</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Divergência</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='100'>";
               echo "<font class='fonte'><b>Local</b></font>";
             echo "</td>";

           echo "</tr>";

         while ($mResultado=mysql_fetch_array($Resultado)) {

                $mResultado['T006_Descricao_Produto']=stripslashes($mResultado['T006_Descricao_Produto']);

                echo "<tr bgcolor=white height=25>";

                  echo "<td align='center' valign='center'>";
                    echo "<font class='fonte'>$mResultado[T005_Estoque_Box]</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center'>";
                    echo "<font class='fonte'>$mResultado[T005_Id]</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>".substr($mResultado['D024_Nome_Empresa'],0,15)."</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='60'>";
                    echo "<font class='fonte'>$mResultado[T006_Codigo_Produto]</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>$mResultado[T006_Descricao_Produto]</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>$mResultado[D082_Marca]</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['T006_Quantidade'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['D009_Quantidade_Estoque'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['Separado'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['T006_Quantidade_Separacao'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['T006_Quantidade']-$mResultado['T006_Quantidade_Separacao'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>$mResultado[D004_Local]</font>";
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
