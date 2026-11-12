<?php

// Inicializa a Sessao
include "../include/sessao.inc";

//--  Conex�o com o banco de dados
include "../include/conexao.inc";

//--  Fun��es Gen�ricas em PHP
include "../func/funcoes_genericas.inc";

//--  Tratamento de Erros no sistema
include "../func/classe_erros.inc";

//--  Seta as configura��es e acesso do usuario
include "../include/config_usuarios.inc";

$C007=mysql_query("select * from C007 where C007_Id='$Usuario_Atual'");
$mC007=mysql_fetch_array($C007);

if ( $mC007[C007_Usuario_Suporte] != 'S' ) {
    ECHO "ACESSO NEGADO!";
    exit;
}


?>
<html>
<head>

<link rel=stylesheet href="../classes/<?php echo $cArquivo_Classe; ?>" type="text/css">

<!-- Fun��es Gen�ricas em Javascript -->
<script src='../func/funcoes_genericas.js'></script>

</head>

<body bgcolor=white onload="parent.document.getElementById('Comando_Sql').focus();">

<?php
//$Comando_Sql=str_replace("\'","'",$Comando_Sql);
$Comando_Sql=stripslashes($Comando_Sql);
$mComando_Sql=explode(";",$Comando_Sql);
$total_rows=0;

for ($nConsultas=0;$nConsultas<=count($mComando_Sql);$nConsultas++) {

    $cConsulta=trim($mComando_Sql[$nConsultas]);
    $cConsultaHaystack=strtoupper('<<<'.$cConsulta.'>>>');

    if (strpos($cConsultaHaystack,'DELETE')>0) {
      echo "<p align=center><font class='fonte'><big>$cConsulta ---> Comando n�o autorizado</big></font></p>";
      continue;
    }

    if (strpos($cConsultaHaystack,'DROP')>0) {
      echo "<p align=center><font class='fonte'><big>$cConsulta ---> Comando n�o autorizado</big></font></p>";
      continue;
    }

    if (strpos($cConsultaHaystack,'ALTER ')>0) {
      echo "<p align=center><font class='fonte'><big>$cConsulta ---> Comando n�o autorizado</big></font></p>";
      continue;
    }

    if (strpos($cConsultaHaystack,'CREATE ')>0) {
      echo "<p align=center><font class='fonte'><big>$cConsulta ---> Comando n�o autorizado</big></font></p>";
      continue;
    }

    if (strpos($cConsultaHaystack,'SHOW ')>0) {
      echo "<p align=center><font class='fonte'><big>$cConsulta ---> Comando n�o autorizado</big></font></p>";
      continue;
    }

    if (strpos($cConsultaHaystack,'USE ')>0) {
      echo "<p align=center><font class='fonte'><big>$cConsulta ---> Comando n�o autorizado</big></font></p>";
      continue;
    }


    if (empty($Titulo)) {
       echo "<p align=center><font class='fonte'><big>$mComando_Sql[$nConsultas]</big></font></p>";
    } else {
       echo "<p align=center><font class='fonte'><big>$nConsultas - $Titulo</big></font><br><font style='font-size:9;color:gray;font-family:arial'>($mComando_Sql[$nConsultas])</font></p>";
    }

    if (strlen(trim($mComando_Sql[$nConsultas]))==0) {
       continue;
    }

    $resultado=mysql_query("$cConsulta");
    $cErro=mysql_error();
    $nLinha=0;

    $total_rows=mysql_num_rows($resultado);

    echo "<table align=center border=0 bgcolor='silver' style='border:gray solid 1px;'cellspacing=1 cellpadding=2 width=100%>";

    // MOSTRO A DESCRICAO DO ERRO
    if (!empty($cErro)) {
       echo "<tr align=center>";
        echo "<td>";
         echo "<font class='fonte'><font color=red><big>ERRO: $cErro</big></font>";
        echo "</td>";
       echo "</tr>";
      echo "</table>";
      continue;
    }

   // MOSTRO O NUMERO DE LINHA QUE O COMANDO SQL RETORNOU
//   If (substr(strtoupper($resultado),0,8)=='RESOURCE') {
      If (mysql_num_rows($resultado)==0) {
          echo "<p>";
            echo "<font class='fonte'>Este comando n�o retornou dados, e n�o retornou nenhuma linha</font>";
          echo "</p>";
          continue;
      }

        echo "<tr bgcolor=#93C6D9>";
          echo "<td style=\"filter:progid:DXImageTransform.Microsoft.Gradient(startColorStr='#93C6D9', endColorStr='#E6F1F7', gradientType='1')\">Linha</td>";
          for ($x=0;$x<mysql_num_fields($resultado);$x++) {
              echo "<td style=\"filter:progid:DXImageTransform.Microsoft.Gradient(startColorStr='#93C6D9', endColorStr='#E6F1F7', gradientType='1')\">";
                echo "<font class=fonte>".mysql_field_name($resultado,$x)."</font></td>";
              echo "</td>";
          }
        echo "</tr>";
        // MOSTRO OS RESULTADOS DO COMANDO
        while ($mComandos=mysql_fetch_array($resultado)) {
             $nLinha++;
             echo "<tr bgcolor=#ffffff>";
                echo "<td align=center><font class=fonte style='color:red;'>$nLinha</font></td>";

                for ($x=0;$x<mysql_num_fields($resultado);$x++) {
                   echo "<td>";
                    if ((empty($mComandos[$x])) or ($mComandos[$x]=="null")) {
                        echo str_pad("",30, "&nbsp");
                    } else {
                      echo "<font class=fonte>".str_replace("\n","<br>",$mComandos[$x])."</font>";
                    }
                   echo "</td>";
                }
              echo "</tr>";
        }
        // MOSTRO O NUMERO DE LINHA QUE O COMANDO SQL RETORNOU

  // }
   echo "</table>";

   echo "<p>";
     echo "<font class='fonte'>Retornou $total_rows Linha(s)</font>";
   echo "</p>";
}
?>
</body>
</html>
