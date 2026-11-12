<?php
/*
  Programador: Evantoni Fuchs
  Data: 28-06-2010
*/

//-- Cria ou inicia uma sessão
@include "../include/sessao.inc";

//-- Conexão com o banco de dados
include "../include/conexao.inc";

//-- Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

if ($T117_Id<=0)
{
   echo "Não foi passado T117_Id";
}

// Busca os dados do usuario
$resultado=mysql_query("select * from C007 where C007_Id='$Usuario_Atual'");
$mUsuario=mysql_fetch_array($resultado);


$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from T117 ";
$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
$cClausula .= " where T117_Id='$T117_Id' ";
$cSql_T117=mysql_query("$cClausula");
echo mysql_error();
$mT117=mysql_fetch_array($cSql_T117);

$nTabIndex=89;

echo "<form name='formulario_T013' id='formulario_T013' method='post' target='fme_gravar' action='../boleto/enviar_boleto_email.php' style='margin: 0px 0px 0px 0px;' onsubmit=\"return MostrarMensagem('inline');\">";

echo "<input type='hidden' id='T117_Id' name='T117_Id' value='$T117_Id'>";
					     
echo "<table border=0 cellspacing=0 cellpadding=0 width=100% bgcolor=#F2F2EE height=100%>";

     echo "<tr valign=center bgcolor='#47678D' height=20>";
          echo "<td colspan=2>";
               echo "<table border=0 cellspacing=0 cellpadding=0 height=20 width=100%>";
                    echo "<tr>";
                         echo "<td width=350>";
                              echo "<font class='fonte' style='color:#FFFFFF;font-size:14px;'><b>&nbsp;Enviar o boleto por e-mail</b></font>";
                         echo "</td>";
                         echo "<td align=right>";
                              echo "<input type='button' value='X' class=botao_fechar style='cursor:pointer' onclick='FecharFormularioEmail()'>";
                         echo "</td>";
                    echo "</tr>";
               echo "</table>";
          echo "</td>";
     echo "</tr>";

     echo "<tr>";
        echo "<td valign=top align=left style='border-right:1px solid silver;border-bottom:1px solid silver;border-left:1px solid silver'>";

            echo "<table border=0 width=100% height=100% bgcolor=#F2F2EE cellpadding=0 cellspacing=5  align=left style='border: 0px solid;border-color:  white #B0B0B0 #B0B0B0 white;'>";

                echo "<tr width=100% height=10>";
                    echo "<td width=160 align=right >";
                         echo "<font class='fonte'><font color=red>*</font></font>";
                         echo "<font class='fonte'>De&nbsp;</font>";
                    echo "</td>";
				    echo "<td align=left  >";
				         $nTabIndex++;
					     echo "<input type='text' name='De' id='De' value='".htmlspecialchars($mUsuario[C007_Email])."' class=campo style='width:100%;text-transform: lowercase;'   tabindex='$nTabIndex' >";
                    echo "</td>";
		        echo "</tr>";

                echo "<tr width=100% height=10>";
                    echo "<td width=160 align=right >";
                         echo "<font class='fonte'><font color=red>*</font></font>";
                         echo "<font class='fonte'>Cliente&nbsp;</font>";
                    echo "</td>";
				    echo "<td align=left>";
					     echo "<input type='text' value='".htmlspecialchars($mT117[D024_Nome_Empresa])."'  class='campo_desabilitado' style='width:100%' disabled  >";
                    echo "</td>";
		        echo "</tr>";

                echo "<tr width=100% height=10>";
                    echo "<td width=160 align=right >";
                         echo "<font class='fonte'><font color=red>*</font></font>";
                         echo "<font class='fonte'>Para&nbsp;</font>";
                    echo "</td>";
				    echo "<td align=left >";
				         $nTabIndex++;
					     echo "<input type='text' name='Para' id='Para' value='".htmlspecialchars($mT117[D024_E_Mail])."' class=campo style='width:100%;text-transform: lowercase;'   tabindex='$nTabIndex' >";
                    echo "</td>";
		        echo "</tr>";

                echo "<tr width=100% height=10>";
                    echo "<td width=160 align=right>";
                         echo "<font class='fonte'><font color=red>*</font></font>";
                         echo "<font class='fonte'>Assunto&nbsp;</font>";
                    echo "</td>";
				    echo "<td align=left>";
				         $nTabIndex++;
					     echo "<input type='text' name='Assunto' id='Assunto' value='Hardness Sistemas - Boleto Cobrança' class=campo style='width:100%;'   tabindex='$nTabIndex' >";
                    echo "</td>";
		        echo "</tr>";

                echo "<tr width=100%>";
				    echo "<td align=center colspan=2>";
					     echo "<textarea name='Corpo' id='Corpo' class=campo style='width:100%;height:100%;'></textarea>";
                    echo "</td>";
		        echo "</tr>";

				echo "<tr height=10>";
					echo "<td colspan=2>";
     					echo "<p align=center>";
								$nTabIndex++;
								echo "<input  type='submit'  value='Enviar' tabindex='$nTabIndex'  class='enviar'   >&nbsp;";
								$nTabIndex++;
								echo "<input  type='button'  value='Cancelar' onClick=\"FecharFormularioEmail();\" tabindex='$nTabIndex' class='cancelar'  >&nbsp;";
					     echo "</p>";
					echo "</td>";
				echo "</tr>";
            echo "</table>";
   		echo "</td>";
   	echo "</tr>";
echo "</table>";

echo "</form>";

?>
