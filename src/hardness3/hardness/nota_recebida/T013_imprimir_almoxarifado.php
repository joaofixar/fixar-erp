<?php
namespace hardness;
global $g;

// Funções Genéricas em PHP
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
include "../include/pasta_usuario.inc";

include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";
$cArquivo_Cabecalho_Relatorio = "../cabecalho_impressoes.php";

$Cor_Normal="#ffffff";

$Linhas_Normais=30;
$Linhas_Ultima_Pagina=25;
$Tamanho_Maximo_Tabela=580;
$Tamanho_Minimo_Tabela=400;
$Width_Relatorio='98%';

// Verifica se o usuario é um administrador
$cSql_C007=mysql_query("select * from $cBd_Configuracao.C007 where C007_Id='$Usuario_Atual'");
$mC007=mysql_fetch_array($cSql_C007);

$cSql_C004=mysql_query("select * from $cBd_Configuracao.C004 where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($cSql_C004);
$mC004['C004_Observacao_Promocao']=str_replace("\n","<br>",$mC004['C004_Observacao_Promocao']);


$cSql_T013=mysql_query("select * from T013
                            left join D024 on D024_Id=T013_D024_Id
                                where T013_Id='$T013_Id'");
$mT013=mysql_fetch_array($cSql_T013);

if ($mT013['T013_Flag_Travar_Nota']!='S') {
	echo "Nota não foi finalizada ainda!";
	exit;
}

$mT013['T013_Data_Emissao']=gCorrigeData($mT013['T013_Data_Emissao']);
$mT013['T013_Observacao']=str_replace("\n","<br>",trim($mT013['T013_Observacao']));

$Data_Compra=mysql_query("select date_sub(current_date(),interval 3 month)");
$mData_Compra=mysql_fetch_array($Data_Compra);
echo mysql_error();

$cClausula="select 
          T193_Id,
          T193_Qtd_Estoque,
          T066_Quantidade_Estoque,
          T014_Quantidade,
                   T014_Codigo_Produto,
                   D001_Codigo_Produto,
                   D001_Descricao_Produto,
                   D082_Marca,
                   D009_Quantidade_Estoque,
                   (select count(*) from T001 where T001_D009_Id=D009_Id and T001_Flag_MMF='S' and T001_Data_Lancamento>='{$mData_Compra[0]}') D009_Frequencia_Venda,
                   (select T001_Quantidade from T001 where T001_D009_Id=D009_Id and T001_Flag_MMF='S' and T001_Data_Lancamento>='{$mData_Compra[0]}' order by T001_Quantidade limit 1) D009_Menor_Pacote,
                   (select T001_Quantidade from T001 where T001_D009_Id=D009_Id and T001_Flag_MMF='S' and T001_Data_Lancamento>='{$mData_Compra[0]}' order by T001_Quantidade desc limit 1) D009_Maior_Pacote,
                   (select sum(T001_Quantidade)/count(*) from T001 where T001_D009_Id=D009_Id and T001_Flag_MMF='S' and T001_Data_Lancamento>='{$mData_Compra[0]}') D009_Pacote_Medio,
                   D004_Id,
                   D004_Flag_Tipo,
                   D004_Local,
                   T014_Id,
                   D009_Id
              from T014
         left join T013 on T013_Id=T014_T013_Id
         left join D009 on D009_Id=T014_D009_Id
         left join D049 on D049_Id=D009_D049_Id
         left join D001 on D001_Id=D049_D001_Id
         left join C004 on C004_Id=T014_C004_Id
         left join D024 on D024_Id=D009_D024_Id
         left join D005 on D005_Id=T014_D005_Id
         LEFT JOIN T193 ON T193_T014_Id=T014_Id 
         left join T066 ON T066_Id=T193_T066_Id 
         left join D004 ON D004_Id=T066_D004_Id 
         left join D082 on D082_Id=D049_D082_Id
             where T014_T013_Id='$T013_Id'
               and (T014_T014_Id is null or T014_T014_Id<=0)
          order by D004_Local";

$Resultado=mysql_query($cClausula);

echo mysql_error();

echo "<html>";
echo "<head>";
echo "<title>Nota Fiscal Entrada Nr $T013_Id - {$mT013['D024_Nome_Empresa']}</title>";

echo "<link rel=stylesheet href=\"../classes/classe_tamanho_pequeno_firefox.css\" type=\"text/css\">";

echo "<style>

.fonte
{
font-family:arial,verdana;
font-size:11px;
}

</style>";


echo "</head>";
echo "<body bgcolor=white leftmargin='0' topmargin='0' rightmargin='0'>";


////////////////////////////////////////////////////////////
// VERIFICA QUANTAS PAGINAS SERÃO IMPRESSAS
////////////////////////////////////////////////////////////
// Encontra o total das linhas a ser impressa
$Total_Linhas=mysql_num_rows($Resultado);
if (!empty($mT013['T013_Mao_Obra_1'])) {
	$Total_Linhas+=4;
}

if ($Total_Linhas==0) {
   //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS É DEFINIDO PARA 1,
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

        // Verifica caso o resto seja maior que o número máximo de itens que cabe na última folha
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

       echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='100%' bgcolor=white style='border:0px solid black'>";
         echo "<tr bgcolor=white>";
          echo "<td valign=top>";
           echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top style='border:0px solid'>";
            echo "<tr>";
             echo "<td bgcolor=white width=80%>";
                //-- $cArquivo_Cabecalho_Relatorio é definido no arquivo include/pasta_usuario.inc
                include $cArquivo_Cabecalho_Relatorio;
             echo "</td>";
             echo "<td bgcolor=white align=center width=20%>";
                  echo "<font class='fonte' style='font-size:18px;font-weight:bold'>";
                   echo "Nota Fiscal Entrada<BR>Nr. {$mT013['T013_Numero_Nota_Fiscal']}";
                  echo "</font>";
               echo "</font>";
               echo "<br><font class=fonte>". date('d/m/y')."</font>";
             echo "</td>";
           echo "</tr>";
           echo "<tr bgcolor=white>";
             echo "<td colspan=2>";
               echo "<table cellpadding=7 width=100%>";
                 echo "<tr bgcolor=white>";
                   echo "<td colspan=3>";
                   echo "<font class='fonte'></font><br> ";
                   if ($mT013[D024_Id]>0) {
                       echo "<font class='fonte' style='font-size:12px'><b>{$mT013[D024_Id]} - {$mT013[D024_Nome_Empresa]}</b></font><br>";
                       echo "<font class='fonte' style='font-size:12px'>CNPJ: {$mT013[D024_Cnpj]}</font><br>";
                       echo "<font class='fonte' style='font-size:12px'>FONE: {$mT013[D024_Telefone_1]} - FAX: {$mT013[D024_Fax]}</font><br>";
                       echo "<font class='fonte' style='font-size:12px'>{$mT013[D024_Endereco]} - {$mT013[D024_Bairro]}</font><br>";
                       echo "<font class='fonte' style='font-size:12px'>CEP: {$mT013[D024_Cep]} - {$mT013[D020_Nome_Cidade]} - {$mT013[D018_UF]}</font><br>";
                       if (!empty($mT013['T013_Nome_Comprador'])) {
                          echo "<font class='fonte'>Comprador:</font> ";
                          echo "<font class='fonte' style='font-size:12px'>{$mT013[T013_Nome_Comprador]}</font> <font class=fonte>{$mT013[T013_E_mail_Comprador]}</font><br>";
                       }
                   } else {
                       echo "<font class='fonte' style='font-size:12px'><b>{$mT013[T013_Nome_Cliente]}</b></font><br>";
                       echo "<font class='fonte' style='font-size:12px'>TELEFONE: {$mT013[T013_Telefone]} - FAX: {$mT013[T013_Fax]}</font><br>";
                       if (!empty($mT013['T013_Nome_Comprador'])) {
                          echo "<font class='fonte'>Comprador:</font> ";
                          echo "<font class='fonte' style='font-size:12px'>{$mT013[T013_Nome_Comprador]}</font> <font class=fonte>{$mT013[T013_E_mail_Comprador]}</font><br>";
                       }
                   }
                   if (!empty($mT013['T013_Observacao'])) {
                      echo "<br><font class='fonte' style='font-size:12px'><b>{$mT013[T013_Observacao]}</b></font>";
                   }
        		 echo "<br>";
                 echo "<td align=right>";
                   if ($mC004[C004_Flag_Empresa_Simples]=='1') {
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
             echo "<td align='center' valign='center' width='60'";
               echo "<font class='fonte'><b>Código</b></font>";
             echo "</td>";

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Descrição Produto</b></font>";
             echo "</td>";

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Marca</b></font>";
             echo "</td>";

             echo "<td align='center' valign='right' width='40'>";
               //echo "<font class='fonte'><b>Qtd. Total</b></font>";
               echo "<font class='fonte'><b>Quantidade</b></font>";
             echo "</td>";
             
/*             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Qtd. Colocar</b></font>";
             echo "</td>"; */

             echo "<td align='center' valign='right' width='40'>";
               echo "<font class='fonte'><b>Estoque</b></font>";
             echo "</td>";
             
/*             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Est. Loc.</b></font>";
             echo "</td>"; */

             echo "<td align='right' valign='center' width='40'>";
			         echo "<font class='fonte'><b>Peso Unit.</b></font>";
			       echo "</td>";

             echo "<td align='centar' valign='center' width='40'>";
			         echo "<font class='fonte'><b>Locação</b></font>";
			       echo "</td>";

/*             echo "<td align='center' valign='center' width='40'>";
			         echo "<font class='fonte'><b>Freq <br> 3 meses</b></font>";
			       echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
			         echo "<font class='fonte'><b>Menor <br> Pacote</b></font>";
			       echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
			         echo "<font class='fonte'><b>Maior <br> Pacote</b></font>";
			       echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
			         echo "<font class='fonte'><b>Pacote <br> Médio</b></font>";
			       echo "</td>"; */

           echo "</tr>";


         $cClausula_Limit = $cClausula;
         $Resultado_Itens = mysql_query($cClausula_Limit);

         $Sequencial=0;
         $nTotal_Linhas_Impressas=0;

         $cacheRowSpan = array();
         while ($mResultado=mysql_fetch_array($Resultado_Itens)) {

                $mResultado['D001_Descricao_Produto']=stripslashes($mResultado['D001_Descricao_Produto']);

                echo "<tr bgcolor=white>";

                  echo "<td align='left' valign='center' width='60'>";
                    //echo "<font class='fonte'>$mResultado[T014_Codigo_Produto]</font>";
                    echo "<font class='fonte'>{$mResultado['D001_Codigo_Produto']}</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['D001_Descricao_Produto']}</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['D082_Marca']}</font>";
                  echo "</td>";

        /*          if (!isset($cacheRowSpan[$mResultado['D009_Id']])) {
                  	  $cacheRowSpan[$mResultado['D009_Id']] = true;
                  	  $resTmp = mysql_query("SELECT * FROM T193 WHERE T193_T014_Id = '{$mResultado['T014_Id']}' AND T193_Qtd_Estoque > 0 AND T193_T013_Id = '{$T013_Id}';");
                  	  $rowSpan = mysql_numrows($resTmp);
	                  echo "<td align='right' valign='center' width='40' rowspan='{$rowSpan}'>";
	                    echo "<font class='fonte'><b><big>".number_format($mResultado[T014_Quantidade],2,',','.')."</big></b></font>";
	                  echo "</td>";
        				  }*/
                  
                  echo "<td align='right' valign='right' width='40'>";
                    //echo "<font class='fonte'><b><big>".number_format($mResultado[T193_Qtd_Estoque],2,',','.')."</big></b></font>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['T014_Quantidade'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='right' valign='right' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['D009_Quantidade_Estoque'],2,',','.')."</big></b></font>";
                  echo "</td>";
                  
/*                  echo "<td align='right' valign='center' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['T066_Quantidade_Estoque'],2)."</big></b></font>";
                  echo "</td>"; */

                  echo "<td align='right' valign='center' width='80'>";
				            echo "<font class='fonte'>".$mResultado['T014_Peso_Unitario']."</font>";
                  echo "</td>";
                  
                  	// Verifica se estamos em uma locação antigo (onde o estoque não era definido por locação)
/*					$mResultado['D004_Local'] = '* ' . $mResultado['D004_Local'];
					$antigo = false;
					if (empty($mResultado['D004_Id']) || empty($mResultado['D004_Flag_Tipo'])) {
						$antigo = true;
						$sqlLocs = "SELECT * FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '{$mResultado['D009_Id']}';";
						$resLocs = mysql_query($sqlLocs);
						$locs = '';
						while ($loc = mysql_fetch_assoc($resLocs)) {
							if (empty($loc['D004_Id']) || empty($loc['D004_Flag_Tipo'])) {
								$loc['D004_Local'] = empty($loc['D004_Local']) ? 'DESCONHECIDO' : $loc['D004_Local'];
								$locs .= (empty($locs)) ? $loc['D004_Local'] : ', ' . $loc['D004_Local'];
							}
						}
						$mResultado['D004_Local'] = $locs;
					} */

                  echo "<td align='left' valign='center' width='80'>";
				       echo "<font class='fonte'><b>".$mResultado['D004_Local']."</b></font>";
                  echo "</td>";

/*                  echo "<td align='center' valign='center'>";
				       echo "<font class='fonte'>".$mResultado[D009_Frequencia_Venda]."</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center'>";
				       echo "<font class='fonte'>".number_format($mResultado[D009_Menor_Pacote],2,',','.')."</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center'>";
				       echo "<font class='fonte'>".number_format($mResultado[D009_Maior_Pacote],2,',','.')."</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center'>";
				       echo "<font class='fonte'>".number_format($mResultado[D009_Pacote_Medio],2,',','.')."</font>";
                  echo "</td>";*/

                echo "</tr>";

                $nTotal_Linhas_Impressas++;

         }


echo "</table></td></tr>";

?>
</body>
</html>