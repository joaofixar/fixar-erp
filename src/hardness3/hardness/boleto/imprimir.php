<?php

//-- Cria ou inicia uma sessão
require "../include/sessao.inc";

//-- Conexão com o banco de dados
require "../include/conexao.inc";

//-- Funções Genéricas em PHP
require "../func/funcoes_genericas.inc";

include "../include/pasta_usuario.inc";

//-- NOME DA TELA :
//-- SERA CRIADO UM ARQUIVO PHP DENTRO DA PASTA DADOS_USUARIOS COMO O NOME QUE FOR DEFINIDO
//-- SERVE PARA GUARDAR OS FILTROS E CONDICOES QUE O USUARIO ESCOLHER, PORTANTO,
//-- PARA CADA FILTRO, O IDEIAL É QUE TENHA UM NOME DIFERENTE
$cNomeTelaFiltro='boleto';
include "../include/include_filtro.php";

// Busca os dados da empresa usuária
$cSql=mysql_query("select * from C004 where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($cSql);

?>

<html>
<head>
<title>Boletos</title>
<meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  <meta http-equiv='Pragma' content='no-cache'>  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
<link rel=stylesheet href="../classes/classe_tamanho_medio_firefox.css" type="text/css">
<!-- Funções Genéricas em Javascript -->
<script src='../func/funcoes_genericas.js'></script>
<script src='../func/ajax.js'></script>

<STYLE TYPE="text/css"><!--
  A:link {text-decoration:none; color:000000}
  A:visited {text-decoration:none; color:000000}
  A:hover {text-decoration:underline; color:000000; cursor:hand}
  A:active {text-decoration:underline; color:000000; cursor:hand}
  
.fontemed
{
  font-size:11px;
  font-family:arial,verdana;
}
--></style>
</head>
<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>

<?php

if ($D007_Id>0) {
    $Filtro_D007 = "and T117_D007_Id='$D007_Id'";
}

    $cClausula  = " select ";
    $cClausula .= " T117_Id, ";
    $cClausula .= " T117_Data_Emissao, ";
    $cClausula .= " T117_Data_Vencimento, ";
    $cClausula .= " T002_Data_Recebimento, ";
    $cClausula .= " T117_Valor_Total, ";
    $cClausula .= " T117_Nosso_Numero,";
    $cClausula .= " D007_Nome_Banco,";
    $cClausula .= " T117_Flag_Cancelado,";
    $cClausula .= " D024_Nome_Empresa ";
    $cClausula .= " from T117 ";
    $cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
    $cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
    $cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
    $cClausula .= " where T117_C004_Id='$Empresa_Atual' ";
    if (!empty($xConsulta_Clausula_Filtro))
    {
       $cClausula .= " and ".$xConsulta_Clausula_Filtro;
    }
    $cClausula .= " and T117_Flag_Cancelado!='S' ";
    $cClausula .= " group by T117_Id ";
    $cClausula .= " order by T117_Data_Vencimento ";
    $cClausula .= " limit 500 ";
    $T117 = mysql_query($cClausula);

echo mysql_error();

    //
    // CABEÇALHO
    //

    echo "<table align=center border='0' valign=top cellpadding=3 cellspacing=0 bgcolor=white width='820' style='border-bottom:1px dashed black'>";
     echo "<tr>";
      echo "<td valign=top>";
       echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
        echo "<tr>";
         echo "<td bgcolor=white width=80%>";

                //-- $cArquivo_Cabecalho_Relatorio é definido no arquivo include/pasta_usuario.inc
                include $cArquivo_Cabecalho_Relatorio;

         echo "</td>";
         echo "<td bgcolor=white align=center width=20% rowspan=2>";
           echo "<font class='titulo_relatorio'>Boletos</font>";
           echo "</font><br>";
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor=white>";
         echo "<td colspan=2>";
           echo "<font class=fontemed>Impresso</b> &nbsp;<b>".agora()."</b></font>";
           echo "</td>";
         echo "</td>";
       echo "</tr>";
     echo "</table>";
    echo "</td>";
   echo "</tr>";
   echo "<tr>";
     echo "<td>";

    echo "<table border=0 cellspacing=2 width='100%' cellpadding=2>";

	echo "<tr bgcolor='silver'>";
  		echo "<td align=center width='80' class='fontemed'>";
             echo "Nosso número";
  		echo "</td>";
   		echo "<td align=left width='120' class='fontemed'>";
             echo "Banco";
		echo "</td>";
		echo "<td align=left width='250' class='fontemed'>";
             echo "Cliente";
		echo "</td>";
		echo "<td align=center width='70' class='fontemed'>";
             echo "Emissão";
		echo "</td>";
		echo "<td align=center width='70' class='fontemed'>";
             echo "Vencimento";
		echo "</td>";
		echo "<td align=center width='70' class='fontemed'>";
             echo "Pagamento";
        echo "</td>";
		echo "<td align=right width='70' class='fontemed'>";
             echo "Total";
		echo "</td>";
  echo "</tr>";

  $cor='#E5E5E5';

  while ($mT117 = mysql_fetch_array($T117))
  {

       if ($cor=='#E5E5E5')
       {
           $cor='white';
       }
       else
       {
           $cor='#E5E5E5';
       }


  	   echo "<tr bgcolor=$cor Id='l_$mT117[T117_Id]' height=19>";

          echo "<td align=center class='fontemed' >";
             echo $mT117[T117_Nosso_Numero].'&nbsp;';
          echo "</td>";
          echo "<td align=left class='fontemed' >";
             echo $mT117[D007_Nome_Banco];
          echo "</td>";
          echo "<td align=left class='fontemed' >";
             echo substr($mT117[D024_Nome_Empresa],0,30);
          echo "</td>";
          echo "<td align=center class='fontemed' >";
             echo troca_data_dma($mT117[T117_Data_Emissao]);
          echo "</td>";
          echo "<td align=center class='fontemed' >";
             echo troca_data_dma($mT117[T117_Data_Vencimento]);
          echo "</td>";
          echo "<td align=center class='fontemed' >";
             echo troca_data_dma($mT117[T002_Data_Recebimento]);
          echo "</td>";
          echo "<td align=right class='fontemed' >";
             echo number_format($mT117[T117_Valor_Total],2,',','.');
          echo "</td>";
          //echo "<td align=right class='fontemed' >";
             //echo number_format($mT117[T117_Valor_Total],2,',','.');
          //echo "</td>";
          //echo "<td align=left class='fontemed' >";
             //echo substr($mT117[D031_Portador],0,20).'&nbsp;';
          //echo "</td>";
       echo "</tr>";
       
       $total_boletos += 1;
       $valor_total +=  $mT117[T117_Valor_Total];

  }

  	   echo "<tr height=19>";

          echo "<td align=left class='fontemed' >";
          echo "</td>";
          echo "<td align=center class='fontemed' >";
          echo "</td>";
          echo "<td align=center class='fontemed' >";
          echo "</td>";
          echo "<td align=center class='fontemed' >";
          echo "</td>";
          echo "<td align=right class='fonte' colspan=2>";
             echo "Boletos Emitidos";
          echo "</td>";
          echo "<td align=right class='fonte' colspan=2>";
             echo $total_boletos;
       echo "</tr>";

  	   echo "<tr height=19>";
          echo "<td align=left class='fontemed' >";
          echo "</td>";
          echo "<td align=center class='fontemed' >";
          echo "</td>";
          echo "<td align=center class='fontemed' >";
          echo "</td>";
          echo "<td align=center class='fontemed' >";
          echo "</td>";
          echo "<td align=right class='fonte' colspan=2>";
             echo "Valor Total";
          echo "</td>";
          echo "<td align=right class='fonte' colspan=2>";
             echo number_format($valor_total ,2,',','.');
         echo "</tr>";

echo "</table>";



 
?>
