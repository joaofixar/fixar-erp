<?php
   namespace hardness;
   global $g;

   require_once('../../bibliotecas/confUsuario.php');
   require_once('../../bibliotecas/conexaoBanco.php');
   require_once("../../configuracoes/sysConfig.php");
   require_once('../../bibliotecas/funcoesGerais.php');
   include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";
   $cArquivo_Cabecalho_Relatorio = "../cabecalho_impressoes.php";
   include "../include/pasta_usuario.inc";

   header ('Content-type: text/html; charset=UTF-8');
   // Busca os dados da empresa usu�ria
   $cSql=mysqli_query("select * from C004 where C004_Id='$Empresa_Atual'");
   $mC004=mysql_fetch_array($cSql);

   $nT047_Id=$_GET['T047_Id'];
   $validarStatus = $_GET['validarStatus'];
?>

<html>
<head>
<title>Impressão da Montagem <?php $nT047_Id; ?></title>
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
  
.fontemed
{
  font-size:11px;
  font-family:arial,verdana;
}
--></style>
</head>
<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>

<?php

$sql = "SELECT T047_Flag_Status FROM T047 WHERE T047_Id = '{$nT047_Id}';";
$res = mysqli_query($sql);
$row = mysql_fetch_assoc($res);

if ($validarStatus !== 'N' && $row['T047_Flag_Status'] != 'A') {
	die('<div style="margin: 30px;">Esta montagem ainda não foi finalizada!</div>');
}

$cClausula  = " select ";
$cClausula .= " T048_Id, ";
$cClausula .= " D001_Id, ";
$cClausula .= " D001_Codigo_Produto, ";
$cClausula .= " D001_Descricao_Produto, ";
$cClausula .= " T048_Quantidade, ";
$cClausula .= " D009_Quantidade_Montagem(D009_Id) as Disp, ";
$cClausula .= " D082_Marca, ";
$cClausula .= " T047_Flag_Status, ";
$cClausula .= " D001_Observacao_Montagem, ";
$cClausula .= " T048_Observacao ";
$cClausula .= " from T048 ";
$cClausula .= " left join T047 on T047_Id=T048_T047_Id ";
$cClausula .= " left join D049 on D049_Id=T048_D049_Id ";
$cClausula .= " left join D009 on D049_Id=D009_D049_Id and T048_C004_Id=D009_C004_Id";
$cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
$cClausula .= " left join D082 on D082_Id=D049_D082_Id ";
$cClausula .= " where T048_T047_Id='$nT047_Id' ";
$cClausula .= " and T048_D049_Id>0 ";
$cClausula .= " order by T048_Id desc ";
$cSql_T048 = mysqli_query($cClausula);

echo mysql_error();

    //
    // CABE�ALHO
    //

    echo "<table align=center border='0' valign=top cellpadding=3 cellspacing=0 bgcolor=white width='820'>";
     echo "<tr>";
      echo "<td valign=top>";
       echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
        echo "<tr>";
         echo "<td bgcolor=white width=80%>";

                //-- $cArquivo_Cabecalho_Relatorio � definido no arquivo include/pasta_usuario.inc
                include $cArquivo_Cabecalho_Relatorio;

       echo "</td>";
         echo "<td bgcolor=white align=center width=20% rowspan=2>";
           echo "<font class='titulo_relatorio'>Montagem $nT047_Id</font>";
           echo "</font><br>";
           if ($mC004['C004_Flag_Empresa_Simples']=='1') {
               echo "<br><table border=0 cellpadding=5 style='border:1px dotted black' bgcolor=#f4f4f4><tr><td align=center><font class=fonte style='font-size:12px;color:red'>Empresa Participante do<br><b>SUPER SIMPLES</b></td></tr></table>";
           }
         echo "</td>";
       echo "</tr>";
       echo "<tr bgcolor=white>";
         echo "<td colspan=2>";
           echo "<font class=fontemed>Impresso</b> &nbsp;<b>".date("d/m/y H:i:s")."</b></font>";
           echo "</td>";
         echo "</td>";
       echo "</tr>";
     echo "</table>";
    echo "</td>";
   echo "</tr>";
   echo "<tr>";
     echo "<td>";

    echo "<table border=0 cellspacing=2 width=100% cellpadding=2 >";


  $cor='#E5E5E5';

  while ($mT048 = mysql_fetch_array($cSql_T048))
  {

    	echo "<tr bgcolor='silver'>";
            echo "<td align=center width='60' class='fontemed'>";
                    echo "Qte";
            echo "</td>";             
        		echo "<td align=left  class='fontemed'>";
                     echo "Referência";
        		echo "</td>";
        		echo "<td align=center width='300' class='fontemed'>";
                     echo "Produto";
        		echo "</td>";
        		echo "<td align=left width='140' class='fontemed'>";
                     echo "Marca";
        		echo "</td>";
/*        		echo "<td align=left width='70' class='fontemed'>";
                     echo "Disp.Mont.";
        		echo "</td>"; */
            echo "<td align=left width='170' class='fontemed'>";
                     echo "Observação";
       	    echo "</td>";
        echo "</tr>";

           $cor='white';

  	   echo "<tr bgcolor=$cor Id='l_$mT048[T013_Id]' height=19>";

          echo "<td align=center class='fontemed' style='font-size:12px;font-weight:bold'>";
             echo number_format($mT048['T048_Quantidade'],2,',','.');
          echo "</td>";
          echo "<td align=left class='fontemed' style='font-size:12px;font-weight:bold'>";
             echo "$mT048[D001_Codigo_Produto]&nbsp;";
          echo "</td>";
          echo "<td align=left class='fontemed' style='font-size:12px;font-weight:bold'>";
             echo substr($mT048['D001_Descricao_Produto'],0,25)."&nbsp;";
          echo "</td>";
          echo "<td align=left class='fontemed' style='font-size:12px;font-weight:bold'>";
             echo substr($mT048['D082_Marca'],0,20);
          echo "</td>";
/*          echo "<td align=center class='fontemed' style='font-size:12px;font-weight:bold'>";
             echo $mT048[Disp];
          echo "</td>"; */
          echo "<td align=center class='fontemed' style='font-size:12px;font-weight:bold'>";
             echo $mT048['T048_Observacao'];
          echo "</td>";
          
       echo "</tr>";
       
       echo "<tr bgcolor=$cor Id='l_$mT048[T013_Id]' height=19>";

          echo "<td align=center colspan=10 class='fontemed' style='font-size:12px;font-weight:bold'>";
             echo $mT048['D001_Observacao_Montagem'];
          echo "</td>";
          
       echo "</tr>";

       $cClausula = <<<EOT
			SELECT 
				T048_Id, 
				D009_Id, 
				D001_Id, 
				D001_Codigo_Produto, 
				D049_Id, 
				D004_Id,
				D004_Local,
				D004_Flag_Tipo, 
				D001_Descricao_Produto, 
				D082_Marca, 
				D037_Unidade, 
				D009_Quantidade_Estoque, 
				T066_Id,
				T066_Quantidade_Estoque,
				D009_Quantidade_OC(D009_Id) as OC,
				T048_Quantidade,
        T071_Quantidade,
				D001_Id,
				D001_Descricao_Produto,
				T048_Codigo_Produto
			FROM T048 
				LEFT JOIN D009 ON D009_D049_Id=T048_D049_Id AND D009_C004_Id=T048_C004_Id
				left join D049 on D049_Id=D009_D049_Id 
				left join D082 on D082_Id=D049_D082_Id 
				left join D001 on D049_D001_Id=D001_Id 
				left join D037 on D037_Id=D001_D037_Id 
				left join T066 ON T066_D009_Id=D009_Id 
				left join D004 ON D004_Id=T066_D004_Id 
			WHERE 
				(
					T048_T047_Id = '{$nT047_Id}'
					AND T048_Flag_Tipo_Montagem = 'D'
				)
EOT;
		$cSql_T071 = mysqli_query($cClausula);
		
    if (mysql_num_rows($cSql_T071)>0){
		  echo "<tr bgcolor=$cor Id='l_$mT048[T013_Id]' height=19>";

          echo "<td align=center class='fontemed' colspan=10>";
          			echo "<div style='font-size: 16px; text-align:left; margin: 2px; font-weight: bold;'>Entrada:</div>";

                    echo "<table bgcolor=silver cellspacing=1 width=100% cellpadding=2 >";

                   	    echo "<tr bgcolor=white>";
                            		echo "<td align=left width='100' class='fontepeq'>";
                                         echo "Referência";
                            		echo "</td>";
                            		echo "<td align=center  class='fontepeq'>";
                                         echo "Produto";
                            		echo "</td>";
                            		echo "<td align=center width='100' class='fontepeq'>";
                                         echo "Fornecedor";
                            		echo "</td>";
                                echo "<td align=center width='60' class='fontepeq'>";
                                         echo "Quantidade";
                                echo "</td>";
                            		echo "<td align=center width='60' class='fontepeq'>";
                                         echo "Estoque";
                            		echo "</td>";
                            		echo "<td align=center width='160' class='fontepeq'>";
                                         echo "Locações";
                            		echo "</td>";
                              echo "</tr>";

                              while ($mT071 = mysql_fetch_array($cSql_T071))
                              {
                                   $mT071['D001_Descricao_Produto']=str_replace(trim('\ '),"",$mT071['D001_Descricao_Produto']);

                              	   echo "<tr bgcolor=white>";


                                      echo "<td align=left class='fontemed' style='$Style;font-size:10px;'>";
                                         echo "$mT071[T048_Codigo_Produto]&nbsp;";
                                      echo "</td>";

                                      echo "<td align=left class='fontemed' style='$Style;font-size:10px;'>";
                                         echo substr($mT071['D001_Descricao_Produto'],0,25)."&nbsp;";
                                      echo "</td>";

                                      $nTabIndex+=1000;
                                      echo "<td align=left class='fontemed' style='$Style;font-size:10px;'>";
                                         echo substr($mT071['D082_Marca'],0,20);
                                      echo "</td>";

                                      echo "<td align=right class='fontemed' style='$Style;font-size:10px;'>";
                                         echo $mT071['T071_Quantidade'];
                                      echo "</td>";

                                      echo "<td align=right class='fontemed' style='$Style;font-size:10px;'>";
                                         echo $mT071['D009_Quantidade_Estoque'];
                                      echo "</td>";

                                      // Verifica se estamos em uma loca��o antigo (onde o estoque n�o era definido por loca��o)
									$mT071['D004_Local'] = '* ' . $mT071['D004_Local'];
									$antigo = false;
									if (empty($mT071['D004_Id']) || empty($mT071['D004_Flag_Tipo'])) {
										$antigo = true;
										$sqlLocs = "SELECT * FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '{$mT071['D009_Id']}';";
										$resLocs = mysqli_query($sqlLocs);
										$locs = '';
										while ($loc = mysql_fetch_assoc($resLocs)) {
											if (empty($loc['D004_Id']) || empty($loc['D004_Flag_Tipo'])) {
												$loc['D004_Local'] = empty($loc['D004_Local']) ? 'DESCONHECIDO' : $loc['D004_Local'];
												$locs .= (empty($locs)) ? $loc['D004_Local'] : ', ' . $loc['D004_Local'];
											}
										}
										$mT071['D004_Local'] = $locs;
									}
                                      echo "<td align=left class='fontemed' style='$Style;font-size:10px;text-align:center;'>";
                                         echo $mT071['D004_Local'];
                                      echo "</td>";

                                   echo "</tr>";

                              }

                            echo "</table>";
          echo "</td>";

       echo "</tr>";
	   }

		$cClausula = <<<EOT
	SELECT 
		T071_Id, 
		D009_Id, 
		D001_Id, 
		D001_Codigo_Produto, 
		D049_Id, 
		D004_Id,
		group_concat(distinct D004_Local SEPARATOR ',') D004_Local,
		D004_Flag_Tipo, 
		D001_Descricao_Produto, 
		D082_Marca, 
		D037_Unidade, 
		D009_Quantidade_Estoque, 
		T066_Id,
		T066_Quantidade_Estoque,
		D009_Quantidade_OC(D009_Id) as OC,
		T071_Quantidade,
		D001_Id,
		T071_Codigo_Produto,
		D001_Descricao_Produto,
		floor(D009_Quantidade_Estoque_Real/T071_Quantidade) as Quantidade_Minima,
		T071_Observacao
	FROM T071 
		left join D009 on D009_Id=T071_D009_Id 
		left join D049 on D049_Id=D009_D049_Id 
		left join D082 on D082_Id=D049_D082_Id 
		left join D001 on D049_D001_Id=D001_Id 
		left join T048 on T071_T048_Id=T048_Id 
		left join D037 on D037_Id=D001_D037_Id 
		left join T066 ON T066_D009_Id=D009_Id
		left join D004 ON D004_Id=T066_D004_Id 
	WHERE 
		(
      T071_T048_Id = '{$mT048['T048_Id']}'
		)
  group by T071_Id
	order by Quantidade_Minima desc
EOT;
$cSql_T071 = mysqli_query($cClausula);	

  	   echo "<tr bgcolor=$cor Id='l_$mT048[T013_Id]' height=19>";

          echo "<td align=center class='fontemed' colspan=10>";
          		echo "<div style='font-size: 16px; text-align:left; margin: 2px; font-weight: bold;'>Saída:</div>";

                    echo "<table bgcolor=silver cellspacing=1 width=100% cellpadding=2 >";

                   	    echo "<tr bgcolor=white>";
                            		echo "<td align=left width='100' class='fontepeq'>";
                                         echo "Referência";
                            		echo "</td>";
                            		echo "<td align=center  class='fontepeq'>";
                                         echo "Produto";
                            		echo "</td>";
                            		echo "<td align=center width='100' class='fontepeq'>";
                                         echo "Fornecedor";
                            		echo "</td>";
                                echo "<td align=center width='60' class='fontepeq'>";
                                         echo "Quantidade";
                                echo "</td>";
                            		echo "<td align=center width='60' class='fontepeq'>";
                                         echo "Estoque";
                            		echo "</td>";
                            		echo "<td align=center width='100' class='fontepeq'>";
                                         echo "Locaões";
                            		echo "</td>";
                              echo "</tr>";

                              while ($mT071 = mysql_fetch_array($cSql_T071))
                              {
                                   $mT071['D001_Descricao_Produto']=str_replace(trim('\ '),"",$mT071['D001_Descricao_Produto']);

                              	   echo "<tr bgcolor=white>";

                                      echo "<td align=left class='fontemed' style='$Style;font-size:10px;'>";
                                         echo "$mT071[T071_Codigo_Produto]&nbsp;";
                                      echo "</td>";

                                      echo "<td align=left class='fontemed' style='$Style;font-size:10px;'>";
                                         echo substr($mT071['D001_Descricao_Produto'],0,25)."&nbsp;";
                                      echo "</td>";

                                      $nTabIndex+=1000;
                                      echo "<td align=left class='fontemed' style='$Style;font-size:10px;'>";
                                         echo substr($mT071['D082_Marca'],0,20);
                                      echo "</td>";

                                      echo "<td align=center class='fontemed' style='$Style;font-size:10px;'>";
                                         echo $mT071['T071_Quantidade'];
                                      echo "</td>";

                                      echo "<td align=right class='fontemed' style='$Style;font-size:10px;'>";
                                         echo $mT071['D009_Quantidade_Estoque'];
                                      echo "</td>";
                                     
                                    // Verifica se estamos em uma locação antigo (onde o estoque não era definido por locação)
                                    $mT071['D004_Local'] = '* ' . $mT071['D004_Local'];
                                    $antigo = false;
                                    if (empty($mT071['D004_Id']) || empty($mT071['D004_Flag_Tipo'])) {
                                       $antigo = true;
                                       $sqlLocs = "SELECT * FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '{$mT071['D009_Id']}';";
                                       $resLocs = mysqli_query($sqlLocs);
                                       $locs = '';
                                       while ($loc = mysql_fetch_assoc($resLocs)) {
                                          if (empty($loc['D004_Id']) || empty($loc['D004_Flag_Tipo'])) {
                                             $loc['D004_Local'] = empty($loc['D004_Local']) ? 'DESCONHECIDO' : $loc['D004_Local'];
                                             $locs .= (empty($locs)) ? $loc['D004_Local'] : ', ' . $loc['D004_Local'];
                                          }
                                       }
                                       $mT071['D004_Local'] = $locs;
                                    }
                                       echo "<td align=left class='fontemed' style='$Style;font-size:10px;'>";
                                          echo $mT071['D004_Local'];
                                       echo "</td>";

                                    echo "</tr>";

                              }

                            echo "</table>";
                            echo "<br />";
          echo "</td>";

       echo "</tr>";

  }


echo "</table>";



 
?>