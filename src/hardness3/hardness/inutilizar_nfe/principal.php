<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

include "../func/funcoes_genericas.inc";

//--  Cria o historico de navegacao
include "../include/voltar.inc";

echo "<html>";
echo "<head>";
echo "<title>INUTILIZAÇÃO DAS NOTAS FISCAIS</title>";
echo "</head>";

echo "<body bottommargin=2 topmargin=0 rightmargin=2 leftmargin=0 ";
echo "bgcolor='#F2F2EE' ";
echo ">";


?>

<link rel=stylesheet href="../classes/classe_tamanho_medio_firefox.css" type="text/css">
<STYLE TYPE="text/css">
A:link {text-decoration:none; color:000000}
A:visited {text-decoration:none; color:000000}
A:hover {text-decoration:none; color:000000; background-color=none; cursor=hand}
A:active {text-decoration:none; color:000000; background-color=none; cursor=hand}
</style>

<script src='../func/ajax.js'></script>
<script src='../func/funcoes_genericas.js'></script>

<?php

    echo "<STYLE TYPE=\"text/css\">
    .divMensagemCentro {
    	width:220px;
    	height:40px;
    	position: absolute;
    	top:50%;
    	left:45%;
    	margin-top:-50px;
    	margin-left:-50px;
    	z-index:auto;
    	background-color: #00FF00;
    	border: 1px solid #004000;
    	clear:both;
    	vertical-align:middle;
    	color: #202020;
    	padding:13px;
        font-family:arial, tahoma, verdana, helvetica, sans-serif;
    	font-size:12px;
    	font-weight:bold;
    	text-align:center;
    }
    </style>";

    echo "<div id='Div_Transmitindo' class=divMensagemCentro style='display:none;'>";
    echo "<img src='../imagens/gif/loading.gif' alt='' /><br /><br />";
        echo "&nbsp;Enviando inutilização para Sefaz. . .";
    echo "</div>";

    echo "<script>";

         echo "  function confirma_inutilizacao() {

         if (confirm(\"Confirma?\"))
         {
            document.getElementById('Div_Transmitindo').style.display='inline';
            return true;
         } else {
            return false;
         }

      }
      </script>
    ";

if ($T007_Id>0)
{         
	$cClausula  = " select ";
	$cClausula .= " T007_Numero_Nota_Fiscal ";
	$cClausula .= " from T007 ";
	$cClausula .= " where T007_Id='$T007_Id' ";
	$cSql_T007 = mysql_query($cClausula);
	$mT007 = mysql_fetch_array($cSql_T007);	      
}         

$Titulo_Tela='INUTILIZAR NF-e';

echo "<table border=0 cellspacing=0 cellpadding=0 width=100% bgcolor=#F2F2EE height=100%>";

    echo "<tr align=left valign=top height=1%>";
		echo "<td>";
          include '../menu/menu.php';
		echo "</td>";
	echo "</tr>";
    echo "<tr align=left valign=top height=4%>";
		echo "<td>";
             echo "&nbsp;";
		echo "</td>";
	echo "</tr>";
	echo "<tr align=left valign=top height=99%>";
		echo "<td style='border-bottom:1px solid silver' valign=top align=center>";
		
            echo "<form name='formulario' id='formulario' method='post' target='fme_inutilizar' action='inutilizacao.php' onsubmit='return confirma_inutilizacao();'>";

              echo "<table border=0 height=430 height=100% cellspacing=0 cellpading=0 width=600>";

                      echo "<tr width=99% bgcolor=#EBE9E0>";
                         echo "<td align=right height=20>";
                            echo "<small><font color=red>*</font></small> ";
                            echo "<font class='fonte'>Numeração Inicial NF";
                            echo "</font>";
                         echo "</td>";
                         echo "<td align=left>";
                           echo "<input type='text' id='T007_Numero_Nota_Fiscal_Inicial' name='T007_Numero_Nota_Fiscal_Inicial' value='$mT007[T007_Numero_Nota_Fiscal]' class=campo>";
                         echo "</td>";
                      echo "</tr>";
                      echo "<tr width=99% bgcolor=#EBE9E0>";
                         echo "<td align=right height=20>";
                            echo "<small><font color=red>*</font></small> ";
                            echo "<font class='fonte'>Numeração Final NF";
                            echo "</font>";
                         echo "</td>";
                         echo "<td align=left>";
                           echo "<input type='text' id='T007_Numero_Nota_Fiscal_Final' name='T007_Numero_Nota_Fiscal_Final' value='$mT007[T007_Numero_Nota_Fiscal]' class=campo>";
                         echo "</td>";
                      echo "</tr>";
                      echo "<tr width=99% bgcolor=#EBE9E0>";
                         echo "<td align=right height=20>";
                            echo "<small><font color=red>*</font></small> ";
                            echo "<font class='fonte'>Série";
                            echo "</font>";
                         echo "</td>";
                         echo "<td align=left>";
									$nTabIndex++;
									echo "<select style='display:inline;' name='T007_Serie' id='T007_Serie' class='campo' tabindex='$nTabIndex'   >";
										unset($mOpcoesCombobox);
										$mOpcoesCombobox[]="1,,001 - Normal";
										$mOpcoesCombobox[]="900,,900 - SCAN";
										for ($o=0; $o<count($mOpcoesCombobox); $o++)
										{
											list($Id,$Valor)=explode(',,',$mOpcoesCombobox[$o]);
											echo "<option value='$Id' ";
											echo ">$Valor</OPTION>";
										}
									echo "</select>";

                         echo "</td>";
                         echo "</tr>";

                      echo "<tr width=99% bgcolor=#EBE9E0>";
                         echo "<td align=right height=20>";
                            echo "<small><font color=red>*</font></small> ";
                            echo "<font class='fonte'>Justificativa<BR><BR><small>Mim 15 / Max 255<BR>Caracteres</small>";
                            echo "</font>";
                         echo "</td>";
                         echo "<td align=left>";
                           echo "<textarea id='T007_Justificativa_Inutilizacao' name='T007_Justificativa_Inutilizacao' cols=80 rows=5 class=campo></textarea>";
                         echo "</td>";
                      echo "</tr>";

                         echo "<tr>";
                              echo "<td colspan=2 height=30>";
                                  echo "<p align=center>";
                                      echo "<input  type='submit' value='Enviar' class='enviar' >";
                                      echo " ";
                                      echo "<input  type='button' value='Cancelar' onClick=\"voltar();\" class='cancelar'  >";
                                      echo " ";
                                  echo "</p>";

                              echo "</td>";
                         echo "</tr>";

                         echo "<tr>";
                              echo "<td colspan=2 height=100%>";
                                   echo "<iframe name='fme_inutilizar' id='fme_inutilizar'  style='scrollbar:no;width:100%;height:100%;border:0px'></iframe>";
                              echo "</td>";
                         echo "</tr>";

              echo "</table>";
            echo "</form>";

		echo "</td>";
	echo "</tr>";
echo "</table>";


?>
