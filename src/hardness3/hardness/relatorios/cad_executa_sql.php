<?php
namespace hardness;

//--  Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

//--  Seta as configurações e acesso do usuario
include "../include/config_usuarios.inc";

//--  Cria o historico de navegacao
include "../include/voltar.inc";

global $g;

echo 'Usuario atual ' . $g['usuarioAtual'];

die();

?>
<html>
<title>MYSQL</title>
<head>
<link rel=stylesheet href="../classes/<?php echo $cArquivo_Classe; ?>" type="text/css">
<!-- Funções Genéricas em Javascript -->
<script src='../func/funcoes_genericas.js'></script>

<script>
function imprimir() {
   document.getElementById('fme_geral').contentWindow.focus();
   document.getElementById('fme_geral').contentWindow.print();
   return false;
}
function inicializar() {
<?php
   if ($C012_Id>0) {
      echo "document.getElementById('formulario').submit();";
   } else {
      echo "document.getElementById('Comando_Sql').focus();";
   }
?>
}

</script>
</head>

<body leftmargin=0 rightmargin=0 topmargin=0 bgcolor="#EAEAEA" onload="inicializar()">
<?php

if ($C012_Id>0) {
   $cSql_C012=mysql_query("select * from $cBd_Configuracao.C012 where C012_Id='$C012_Id'");
   $mC012=mysql_fetch_array($cSql_C012);
}

$Titulo_Tela='MYSQL';

echo "<table border=0 cellspacing=0 cellpadding=0 width=100% bgcolor=#F2F2EE height=100%>";


	echo "<form id='formulario' name='formulario' method='post' target='fme_geral' action='executa_sql.php' style='margin:0px 0px 0px 0px'>";
	
	echo "<tr align=left valign=top height=100%>";
		echo "<td colspan=4 style='border-top:1px solid white'>";

            echo "<table align=center border=0 bgcolor='white' style='border:gray solid 1px;'cellspacing=1 cellpadding=2 width=100% height=98%>";
              echo "<tr bgcolor=#93C6D9>";
                echo "<td colspan=2 style=\"filter:progid:DXImageTransform.Microsoft.Gradient(startColorStr='#93C6D9', endColorStr='#E6F1F7', gradientType='1')\">";
                  echo "<font class=fonte><b><big>Instrução Sql:</big></b></font>";
                echo "</td>";
              echo "</tr>";
              echo "<tr height=1>";
                echo "<td width=100%>";
                  echo "<textarea name='Comando_Sql' id='Comando_Sql' rows=5 class=campo_memo style='width:100%;'>$mC012[C012_Clausula_Sql]</textarea>";
                echo "</td>";
                echo "<td align=center>";
                  echo "<table align=center border=0 width=100px>";
                    echo "<tr height=1>";
                      echo "<td width=100% align=center>";
                        echo "<input type=submit class=botao style='width:100%;' ";
                        if ($cNavegador!='Internet Exploder') {
                            echo "value='Executar' ";
                            echo "title='Executa a Clausula SQL. Tecla de atalho: ALT + X' ";
                        } else {
                            echo "value='Executar' ";
                            echo "title='Executa a Clausula SQL. Tecla de atalho: Pressione \"ALT + X\" em seguinda ENTER' ";
                        }
                        echo "accesskey='X'>";
                      echo "</td>";
                    echo "</tr>";
                    echo "<tr height=1>";
                      echo "<td width=100% align=center >";
                        echo "<input type='button' class='botao' value='Imprimir' onclick=\"imprimir()\" style='width:100%;'>";
                      echo "</td>";
                    echo "</tr>";
                  echo "</table>";
                echo "</td>";
              echo "</tr>";
              echo "<tr>";
                echo "<td width=100% colspan=2>";
                  echo "<font class=fonte>Titulo&nbsp;</font>";
                  echo "<input type='text' name='Titulo' id='Titulo' value='$mC012[C012_Nome]' class=campo style='width:99%'>";
                echo "</td>";
              echo "</tr>";
              echo "<tr height='100%'>";
                echo "<td colspan=3 align=center>";
                  echo "<iframe name='fme_geral' id='fme_geral' style='visibility:visible;   border-left-width: 1px;   border-right-width: 1px;   border-top-width : 1px;   border-bottom-width : 1px;   border-color : white;   border-left : 1px solid white;   border-right : 1px solid white;   border-top : 1px solid white;   border-bottom : 1px solid white;   width:100%;   height:100%;   padding : 0px;' scrolling=yes frameborder=0 marginwidth=0 src='executa_sql.php'></iframe>";
                echo "</td>";
              echo "</tr>";
            echo "</table>";

        echo "</td>";
     echo "</tr>";
   echo "</table>";

   echo "</form>";
?>
</body>
</html>
