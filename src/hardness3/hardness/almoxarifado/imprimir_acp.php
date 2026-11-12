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

//-- Definindo Variaveis e Constantes
define(SALTAR_LINHA,chr(13).chr(10));

header ('Content-type: text/html; charset=UTF-8');

$Cor_Normal="#ffffff";
$Linhas_Normais=99999999;
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

$cSql_T007=mysqli_query("select * from T007
                            left join D024 on D024_Id=T007_D024_Id
                            left join D020 on D020_Id=D024_D020_Id
                            left join D018 on D018_Id=D020_D018_Id
                            left join D022 on D022_Id=T007_D022_Id
                            left join D021 on D021_Id=T007_D021_Id
          left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                where T007_Id='$T007_Id'");
$mT007=mysql_fetch_array($cSql_T007);

if ($mT007['T007_Flag_ACP'] == 3){
    $tipoDoc = 'ACP';
} else if ($mT007['T007_Flag_ACP'] == 4){
    $tipoDoc = 'CF';
} else if ($mT007['T007_Flag_ACP'] == 6){
    $tipoDoc = 'NF-e';
} else if ($mT007['T007_Flag_ACP'] == 8){
    $tipoDoc = 'NFC-e';
}

if(!empty($mT007['D024_Complemento'])){
  $complemento = '- '.$mT007['D024_Complemento'];
}else{
  $complemento = '';
}

echo mysql_error();

$T007_Id=$mT007['T007_Id'];

$mT007['T007_Data_Emissao']=gCorrigeData($mT007['T007_Data_Emissao']);
$mT007['T007_Observacao']=str_replace("\n","<br>",trim($mT007['T007_Observacao']));

$cSql_Prazos=mysqli_query("select T007_Prazos('$T007_Id') as Prazos");
$mPrazos=mysql_fetch_array($cSql_Prazos);

if (($Moeda=='D') and ($mT007['T007_Valor_Dolar']<=0)) {
   msg_alerta("Preencha o valor do dólar!");
   echo "<script>self.close();</script>";
   exit;
}

$cClausula="select T008_Quantidade,
                   if(T008_Codigo_Substituto!='',T008_Codigo_Substituto,T008_Codigo_Produto) as T008_Codigo_Produto_Substituto,
                   T008_Codigo_Produto,
                   T008_Codigo_Substituto,
                   T008_Descricao_Produto,
                   D082_Marca,
                   T008_Valor_Preco_Sem_Desconto_Unitario,
                   T008_Peso_Total,
                   T008_Valor_Total_Preco_Sem_Desconto,
                   T008_Aliquota_IPI,
                   T008_Id,
                   T008_Flag_Preco_Especial,
                   T008_Informacoes_Adicionais,
                   D005_Classificacao_Fiscal
              from T008
         left join T007 on T007_Id=T008_T007_Id
         left join D009 on D009_Id=T008_D009_Id
         left join D049 on D049_Id=D009_D049_Id
         left join D001 on D001_Id=D049_D001_Id
         left join C004 on C004_Id=T008_C004_Id
         left join D024 on D024_Id=D009_D024_Id
         left join D005 on D005_Id=T008_D005_Id
         left join D082 on D082_Id=D049_D082_Id
             where T008_T007_Id='$T007_Id'";
$Resultado=mysqli_query($cClausula);

echo mysql_error();

echo "<html>";
echo "<head>";
echo "<title>$tipoDoc Nr $T007_Id - $mT007[D024_Nome_Empresa]</title>";
echo "<link rel=stylesheet href=\"../classes/classe_tamanho_medio_firefox.css\" type=\"text/css\">";
echo "</head>";
echo "<body bgcolor=white leftmargin='0' topmargin='0'>";


////////////////////////////////////////////////////////////
// VERIFICA QUANTAS PAGINAS SER�O IMPRESSAS
////////////////////////////////////////////////////////////
// Encontra o total das linhas a ser impressa
$Total_Linhas=mysql_num_rows($Resultado);
if (!empty($mT007['T007_Mao_Obra_1'])) {
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

       echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='850' bgcolor=white style='border:0px solid black'>";
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
               echo "$tipoDoc<BR>$mT007[T007_Numero_Nota_Fiscal]";
                  echo "</font>";
               echo "</font>";
               echo "<br><font class=fonte>$mT007[T007_Data_Emissao]</font>";
             echo "</td>";
           echo "</tr>";
           echo "<tr bgcolor=white>";
             echo "<td colspan=2>";
               echo "<table cellpadding=7 width=100%>";
                 echo "<tr bgcolor=white>";
                   echo "<td colspan=3>";
                   echo "<font class='fonte'></font><br> ";
                   if ($mT007['D024_Id']>0) {
                       echo "<font class='fonte' style='font-size:12px'><b>$mT007[D024_Nome_Empresa]</b></font><br>";
                       echo "<font class='fonte' style='font-size:12px'>FONE: $mT007[D024_Telefone_1] - FAX: $mT007[D024_Fax]</font><br>";
                       echo "<font class='fonte' style='font-size:12px'>$mT007[D024_Endereco], $mT007[D024_Numero] $complemento - $mT007[D024_Bairro]</font><br>";
                       echo "<font class='fonte' style='font-size:12px'>CEP: $mT007[D024_Cep] - $mT007[D020_Nome_Cidade] - $mT007[D018_UF]</font><br>";
                       if (!empty($mT007['T007_Nome_Comprador'])) {
                          echo "<font class='fonte'>Comprador:</font> ";
                          echo "<font class='fonte' style='font-size:12px'>$mT007[T007_Nome_Comprador]</font> <font class=fonte>$mT007[T007_E_mail_Comprador]</font><br>";
                       }
                   } else {
                       echo "<font class='fonte' style='font-size:12px'><b>$mT007[T007_Nome_Cliente]</b></font><br>";
                       echo "<font class='fonte' style='font-size:12px'>TELEFONE: $mT007[T007_Telefone] - FAX: $mT007[T007_Fax]</font><br>";
                       if (!empty($mT007['T007_Nome_Comprador'])) {
                          echo "<font class='fonte'>Comprador:</font> ";
                          echo "<font class='fonte' style='font-size:12px'>$mT007[T007_Nome_Comprador]</font> <font class=fonte>$mT007[T007_E_mail_Comprador]</font><br>";
                       }
                   }
                   if (!empty($mT007['T007_Observacao'])) {
                      echo "<br><font class='fonte' style='font-size:12px'><b>$mT007[T007_Observacao]</b></font>";
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
             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte_titulo_tabela'><b>Quant.</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='60'";
               echo "<font class='fonte_titulo_tabela'><b>Código</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='60'";
               echo "<font class='fonte_titulo_tabela'><b>Código Cliente</b></font>";
             echo "</td>";

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte_titulo_tabela'><b>Descrição Produto</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center'>";
               echo "<font class='fonte_titulo_tabela'><b>NCM</b></font>";
             echo "</td>";

			if($Tipo_Opcoes == "L") {
				echo "<td align='right' valign='center' width='40'>";
				echo "<font class='fonte_titulo_tabela'><b>Peso Unit.</b></font>";
				echo "</td>";
			}


             echo "<td align='right' valign='center' width='60'>";
             echo "<font class='fonte_titulo_tabela'><b>Preço Unit.</b></font>";
             echo "</td>";


             echo "<td align='right' valign='center' width='60'>";
               echo "<font class='fonte_titulo_tabela'><b>Valor Total</b></font>";
             echo "</td>";


             echo "<td align='right' valign='center' width='40'>";
               echo "<font class='fonte_titulo_tabela'><b>Peso Total</b></font>";
             echo "</td>";

           echo "</tr>";


         $cClausula_Limit = $cClausula;
         $Resultado_Itens = mysqli_query($cClausula_Limit);

         $Sequencial=0;
         $nTotal_Linhas_Impressas=0;

         while ($mResultado=mysql_fetch_array($Resultado_Itens)) {

                if ($Moeda=='D') {

                   $mResultado['T008_Valor_Preco_Sem_Desconto_Unitario']=$mResultado['T008_Valor_Preco_Sem_Desconto_Unitario']/$mT007['T007_Valor_Dolar'];
                   $mResultado['T008_Valor_Total_Preco']=$mResultado['T008_Valor_Total_Preco']/$mT007['T007_Valor_Dolar'];

                   $mResultado['T008_Valor_Preco_Unitario']=round($mResultado['T008_Valor_Preco_Unitario']/$mT007['T007_Valor_Dolar'],2);
                   $mResultado['T008_Valor_Total_Preco']=$mResultado['T008_Valor_Preco_Unitario']*$mResultado['T008_Quantidade'];

                }

                $mResultado['T008_Descricao_Produto']=stripslashes($mResultado['T008_Descricao_Produto']);

                echo "<tr bgcolor=white height=25>";
                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte_titulo_tabela'>".number_format($mResultado['T008_Quantidade'],2,',','.')."</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='60'>";
                    echo "<font class='fonte_titulo_tabela'>$mResultado[T008_Codigo_Produto]</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='60'>";
                    echo "<font class='fonte_titulo_tabela'>$mResultado[T008_Codigo_Substituto]</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    if ($mResultado['T008_Flag_Preco_Especial']=='O') {
                       echo "<font class='fonte_titulo_tabela'>*</font>&nbsp;";
                    }
                    echo "<font class='fonte_titulo_tabela'>$mResultado[T008_Descricao_Produto]</font>";
                    if($mResultado['T008_Informacoes_Adicionais'] != ''){
                        echo "<br /><small><i>{$mResultado['T008_Informacoes_Adicionais']}</i></small>";
                    }
                  echo "</td>";

                  echo "<td align='center' valign='center' width='20'>";
                    echo "<font class='fonte_titulo_tabela'>$mResultado[D005_Classificacao_Fiscal]</font>";
                  echo "</td>";

                  echo "<td align='right' valign='center' width=']50'>";
                    echo "<font class='fonte_titulo_tabela'>".number_format($mResultado['T008_Valor_Preco_Sem_Desconto_Unitario'],5,',','.')."</font>";
                  echo "</td>";

                  echo "<td align='right' valign='center' width='50'>";

                    echo "<font class='fonte_titulo_tabela'>".number_format($mResultado['T008_Valor_Total_Preco_Sem_Desconto'],2,',','.')."</font>";

                  echo "</td>";

                  echo "<td align='right' valign='center' width='40'>";
                    echo "<font class='fonte_titulo_tabela'>".number_format($mResultado['T008_Peso_Total'],3,',','.')."</font>";
                  echo "</td>";

                echo "</tr>";

                $nTotal_Linhas_Impressas++;

         }



echo "</table></td></tr>";

if ($Moeda=='D') {
    $mT007['T007_Valor_Total_Produtos']=$mT007['T007_Valor_Total_Produtos']/$mT007['T007_Valor_Dolar'];
    $mT007['T007_Valor_Frete']=$mT007['T007_Valor_Frete']/$mT007['T007_Valor_Dolar'];
    $mT007['T007_Valor_Mao_Obra']=$mT007['T007_Valor_Mao_Obra']/$mT007['T007_Valor_Dolar'];
    $mT007['T007_Valor_Despesas_Acessorias']=$mT007['T007_Valor_Despesas_Acessorias']/$mT007['T007_Valor_Dolar'];
    $mT007['T007_Valor_Desconto']=$mT007['T007_Valor_Desconto']/$mT007['T007_Valor_Dolar'];
    $mT007['T007_Valor_Total']=$mT007['T007_Valor_Total']/$mT007['T007_Valor_Dolar'];
    $mT007['T007_Valor_Total_IPI']=$mT007['T007_Valor_Total_IPI']/$mT007['T007_Valor_Dolar'];
}

if ($mT007['T007_Valor_Mao_Obra']>0) {

    echo "<tr bgcolor=white><td>";
    echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='$Width_Relatorio' bgcolor='white'>";

    if (!empty($mT007['T007_Mao_Obra_1'])) {
      echo "<tr bgcolor=white>";
          echo "<td valign=top colspan=10>";
            echo "<font class='fonte'>$mT007[T007_Mao_Obra_1]<br></font>";
            echo "<font class='fonte'>$mT007[T007_Mao_Obra_2]<br></font>";
            echo "<font class='fonte'>$mT007[T007_Mao_Obra_3]<br></font>";
            echo "<font class='fonte'>$mT007[T007_Mao_Obra_4]</font>";
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

echo "<table border=0 cellspacing=0 cellpadding=0 align=right width=100% bgcolor=white style='border-bottom:1px dashed gray'>";

  echo "<tr bgcolor=white>";

    echo "<td rowspan=2 width=80% align=left valign=top>";
      echo "<table border=0 cellpadding=0 cellspacing=0>";
       echo "<tr>";
        echo "<td colspan=2>";
         echo "<table border=0 cellspacing=1 cellpadding=3 bgcolor=black width=300>";
          echo "<tr bgcolor=white>";
           echo "<td colspan=5 align=center bgcolor=#f4f4f4>";
            echo "<font class=fonte>Pagamento</font>";
           echo "</td>";
          echo "</tr>";
          echo "<tr bgcolor=white>";
            echo "<td align=center>";
              echo "<font class='fonte'><b>Prazo</b></font><br>";
            echo "</td>";
            echo "<td align=center>";
              echo "<font class='fonte'><b>Vencimento</b></font><br>";
            echo "</td>";
            echo "<td align=right>";
              echo "<font class='fonte'><b>Valor</b></font><br>";
            echo "</td>";
            echo "<td align=center>";
              echo "<font class='fonte'><b>Portador</b></font><br>";
            echo "</td>";
          echo "</tr>";
            $T002=mysqli_query("select *,datediff(T002_Data_Vencimento,T002_Data_Emissao) Prazo from T002 left join D027 on D027_Id=T002_D027_Id where T002_T007_Id='$T007_Id' ORDER BY T002_Data_Vencimento ASC;");
            while ($mT002=mysql_fetch_array($T002)) {
                 echo "<tr bgcolor=white>";
                   echo "<td align=center>";
                     echo "<font class='fonte'>$mT002[Prazo]</font><br>";
                   echo "</td>";
                   echo "<td align=center>";
                     echo "<font class='fonte'>".gCorrigeData($mT002['T002_Data_Vencimento'])."</font><br>";
                   echo "</td>";
                   echo "<td align=right>";
                     echo "<font class='fonte'>".number_format($mT002['T002_Valor_Duplicata'],2,',','.')."</font><br>";
                   echo "</td>";
                   echo "<td align=center>";
                     echo "<font class='fonte'>$mT002[D027_Portador]</font><br>";
                   echo "</td>";
                 echo "</tr>";
            }
         echo "</table>";
        echo "</td>";
       echo "</tr>";
       echo "<tr>";
        echo "<td colspan=2 height=10>";
         echo "";
        echo "</td>";
       echo "</tr>";
       echo "<tr>";
        echo "<td>";
         echo "<font class='fonte' style='font-size:12px'>- Transporte:</font><br>";
        echo "</td>";
        echo "<td>";
         if ($mT007['T007_Flag_Frete']=='2') {
            echo "<font class='fonte' style='font-size:12px'><b>(A PAGAR) $mT007[D022_Nome_Empresa]</b></font><br>";
         } else if ($mT007['T007_Flag_Frete']=='1') {
            echo "<font class='fonte' style='font-size:12px'><b>(PAGO) $mT007[D022_Nome_Empresa]</b></font><br>";
         } else {
            echo "<font class='fonte' style='font-size:12px'><b>$mT007[D022_Nome_Empresa]</b></font><br>";
         }
        echo "</td>";
       echo "</tr>";
       echo "<tr>";
        echo "<td>";
         echo "<font class='fonte' style='font-size:12px'>- Representante:</font><br>";
        echo "</td>";
        echo "<td>";
         echo "<font class='fonte' style='font-size:12px'><b>$mT007[D021_Nome_Empresa]</b></font><br>";
        echo "</td>";
       echo "</tr>";
      echo "</table>";

      if (!empty($mT007['T007_Observacao_Nota_Fiscal_2'])) {
        echo "<font class='fonte' style='font-size:12px'><b></b></font><br>";
        echo "<font class='fonte' style='font-size:12px'><b>Observação: $mT007[T007_Observacao_Nota_Fiscal_2]</b></font><br>";
      }
      
    echo "</td>";

  echo "<td align=right>";

    echo "<table bgcolor=white width=200 border=0 cellspacing=1 align=right>";

      
      echo "<tr bgcolor=white>";
        echo "<td>";
          echo "<font class='fonte'><big>Total Produtos: </big></font>";
        echo "</td>";
        echo "<td align=right>";
          echo "<font class='fonte'><big>".number_format($mT007['T007_Valor_Total_Produtos'],2,',','.')."</big></font>";
        echo "</td>";
      echo "</tr>";
      

      echo "<tr bgcolor=white>";
        echo "<td>";
          echo "<font class='fonte'><big>Desconto: </big></font>";
        echo "</td>";
        echo "<td align=right>";
          echo "<font class='fonte'><big>".number_format($mT007['T007_Valor_Desconto']*-1,2,',','.')."</big></font>";
        echo "</td>";
      echo "</tr>";

      $SubTotal=0;
      $SubTotal=$mT007['T007_Valor_Total_Produtos'];

      if ($mT007['T007_Valor_Total_IPI']>0) {
         echo "<tr bgcolor=white>";
           echo "<td>";
             echo "<font class='fonte'><big>Valor IPI: </big></font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class='fonte'><big>".number_format($mT007['T007_Valor_Total_IPI'],2,',','.')."</big></font>";
           echo "</td>";
         echo "</tr>";

      }

      
        echo "<tr bgcolor=white>";
          echo "<td>";
            echo "<font class='fonte'><big>Frete: </big></font>";
          echo "</td>";
          echo "<td align=right>";
            echo "<font class='fonte'><big>".number_format($mT007['T007_Valor_Frete'],2,',','.')."</big></font>";
          echo "</td>";
        echo "</tr>";
  

      if ($mT007['T007_Valor_Mao_Obra']>0) {
          echo "<tr bgcolor=white>";
            echo "<td>";
              echo "<font class='fonte'><big>Mão-de-Obra: </big></font>";
            echo "</td>";
            echo "<td align=right>";
              echo "<font class='fonte'><big>".number_format($mT007['T007_Valor_Mao_Obra'],2,',','.')."</big></font>";
            echo "</td>";
          echo "</tr>";
      }


      if ($mT007['T007_Valor_Despesas_Acessorias']>0) {
          echo "<tr bgcolor=white>";
            echo "<td>";
              echo "<font class='fonte'><big>Despesas Acessórias: </big></font>";
            echo "</td>";
            echo "<td align=right>";
              echo "<font class='fonte'><big>".number_format($mT007['T007_Valor_Despesas_Acessorias'],2,',','.')."</big></font>";
            echo "</td>";
          echo "</tr>";
      }

      echo "<tr bgcolor=white>";
        echo "<td>";
          echo "<font class='fonte'><big><b>Total:</b></big></font>";
        echo "</td>";
        echo "<td align=right>";
          echo "<font class='fonte'><big><b>".number_format($mT007['T007_Valor_Total'],2,',','.')."</b></big></font>";
        echo "</td>";
      echo "</tr>";

      if ($mT007['T007_Valor_ISS']>0) {
         echo "<tr bgcolor=white>";
           echo "<td>";
             echo "<font class='fonte'><big>ISS: </big></font>";
           echo "</td>";
           echo "<td align=right>";
             echo "<font class='fonte'><big>".number_format($mT007['T007_Valor_ISS'],2,',','.')."</big></font>";
           echo "</td>";
         echo "</tr>";
      }

          $mT008 = mysql_fetch_array($Resultado_Itens);
          echo "<tr bgcolor=white>";
            echo "<td>";
              echo "<font class='fonte'><big>Peso Total: </big></font>";
            echo "</td>";
            echo "<td align=right>";
              echo "<font class='fonte'><big>".$mT007['T007_Peso_Total']."</big></font>";
            echo "</td>";
          echo "</tr>";

      echo "</table>";
    echo "</td>";
  echo "</tr>";
  echo "<tr><td colspan=4 align=center>";
    echo "<table border=0 cellpadding=5>";
     echo "<tr><td colspan=4 align=center style='border:0px dotted gray'><font class='fonte' style='font-size:12px'><b>$mC004[C004_Observacao_Promocao]&nbsp;</b></big></font><br>";
    echo "</table>";
    echo "</td>";
  echo "</tr>";
echo "</td></tr>";
  echo "<tr>";
    echo "<td height=0>";
      echo "<br><font class=fonte style='font-size:12px'>$mT007[C007_Primeiro_Nome]<br>$mT007[C007_Email]<br>$mT007[C007_Telefone_Direto]</font><br><br>";
    echo "</td>";
  echo "</tr>";
echo "</table>";
echo "<font class=fonte>Sistema Hardness - www.hardness.com.br</font>";
?>
</body>
</html>
