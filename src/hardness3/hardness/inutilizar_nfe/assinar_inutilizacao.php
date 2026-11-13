<?php

$Origem        = $_GET[Origem];
$Destino       = $_GET[Destino];
$Diretorio_NFe = $_GET[Diretorio_NFe];

$nAno_Atual                      = $_GET[Ano_Atual];
$T007_Serie                      = $_GET[T007_Serie];
$T007_Numero_Nota_Fiscal_Inicial = $_GET[T007_Numero_Nota_Fiscal_Inicial];
$T007_Numero_Nota_Fiscal_Final   = $_GET[T007_Numero_Nota_Fiscal_Final];
$T007_Justificativa_Inutilizacao = $_GET[T007_Justificativa_Inutilizacao];
$T007_Flag_ACP                    = $_GET[T007_Flag_ACP];
$T127_Recuperacao_Id             = $_GET[T127_Recuperacao_Id];
$ven003DivRefresh                = $_GET[ven003DivRefresh];
echo "<iframe src='http://localhost:8081/hardness/dados_usuarios/e003/nfe/xml/assinado/assinar_a3.php?Origem=$Origem&Destino=$Destino&Tipo_Assinatura=infInut' style='display:none;position:absolute;'></iframe>";
$cMensagem="";
$cMensagem.="Inutilização será assinada com certificado A3, após selecionar o certificado, clique no botão abaixo:";
$cMensagem.="<BR>";
$cMensagem.="<p align=center><input type='button' value='Clique aqui ap�s selecionar o certificado digital' onclick=\"parent.document.getElementById('Div_Transmitindo').style.display='inline';location.replace('../inutilizar_nfe/enviar_inutilizacao.php?&Ano_Atual=$nAno_Atual&T007_Serie=$T007_Serie&T007_Numero_Nota_Fiscal_Inicial=$T007_Numero_Nota_Fiscal_Inicial&T007_Numero_Nota_Fiscal_Final=$T007_Numero_Nota_Fiscal_Final&T007_Justificativa_Inutilizacao=$T007_Justificativa_Inutilizacao&T007_Flag_ACP=$T007_Flag_ACP&T127_Recuperacao_Id=$T127_Recuperacao_Id&ven003DivRefresh=$ven003DivRefresh');\"></p>";
msg_erro_nfe($cMensagem);

function msg_erro_nfe($cErro)
{
    echo "<script>parent.document.getElementById('Div_Transmitindo').style.display='none';</script>";
    echo "<table width=100% height=100% border=0 >";
         echo "<tr>";
              echo "<td align=center>";
                  echo "<table border=0 bgcolor=#FF0000>";
                      echo "<tr>";
                          echo "<td align=center>";
                             echo "<fieldset>";

                                echo "<table border=0>";
                                     echo "<tr>";
                                          echo "<td align=left>";
                                               echo "<font style='font-size:12px;font-family:arial,verdana;color:white;font-weight:bold;'>";
                                               echo $cErro;
                                               echo "</font>";
                                          echo "</td>";
                                     echo "</tr>";
                                 echo "</table>";
                              echo "</fieldset>";
                          echo "</td>";
                     echo "</tr>";
                echo "</table>";
             echo "</td>";
         echo "</tr>";
    echo "</table>";
    exit;
}

?>
