<?php
/*
   Programador: Evantoni Fuchs
   Data: 05/05/2010
*/

// Cria ou inicia uma sessão
$Pular_Script='S';
include "../include/sessao.inc";

// Conexão com o banco de dados
include "../include/conexao.inc";

// Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

include "../include/pasta_usuario.inc";

require"../class/phpmailer/class.phpmailer.php";

include "../class/danfe.php";

if ($T117_Id<=0)
{
   echo "<script>parent.MostrarMensagem('none');</script>";
   msg_alerta("Não foi passado T117_Id");
   exit;
}

$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from T117 ";
$cClausula .= " where T117_Id='$T117_Id' ";
$cSql_T117=mysql_query("$cClausula");
echo mysql_error();
$mT117=mysql_fetch_array($cSql_T117);


if (empty($De))
{
   echo "<script>parent.document.getElementById('De').focus();</script>";
   msg_alerta("Digite o seu e-mail!");
   exit;
}

if (empty($Para))
{
   echo "<script>parent.document.getElementById('Para').focus();</script>";
   msg_alerta("Digite um e-mail!");
   exit;
}

if (empty($Assunto))
{
   echo "<script>parent.document.getElementById('Assunto').focus();</script>";
   msg_alerta("Digite o Assunto!");
   exit;
}

if ($mT117[Dias_Vencer]==0)
{
   $dias_vencimento = " (hoje)";
}
elseif ($mT117[Dias_Vencer]==1)
{
   $dias_vencimento = " (amanhã) ";
}
elseif ($mT117[Dias_Vencer]>2)
{
   $dias_vencimento = " (daqui a $mT117[Dias_Vencer] dias)";
}

$Fonte='font-size: 11px;font-family: arial, sans-serif, verdana, helvetica, sans-serif; color:black;';

$cInfoBoleto ="<table width=500 border=0 cellpadding=5 bgcolor=white>";
   $cInfoBoleto.="<tr>";
    $cInfoBoleto.="<td colspan=2 style='border-bottom:0px solid silver'>";
     $cInfoBoleto.="<font style='font-family:arial,verdana;font-size:18px;font-weight:bold'>";
      $cInfoBoleto.="Hardness Sistemas";
    $cInfoBoleto.="</td>";
   $cInfoBoleto.="</tr>";
   $cInfoBoleto.="<tr>";
    $cInfoBoleto.="<td colspan=2 style='border-bottom:0px solid silver'>";
     $cInfoBoleto.="<font style='font-family:arial,verdana;font-size:12px;'>";
      $cInfoBoleto.="Olá! Já está disponível o boleto para pagamento.";
    $cInfoBoleto.="</td>";
   $cInfoBoleto.="</tr>";
   $cInfoBoleto.="<tr>";
    $cInfoBoleto.="<td width=10 style='border-left:0px solid silver'>";
      $cInfoBoleto.="<a target='_blank' href='http://201.22.57.137:8080/hard/boleto/gerar_boleto_hardness.php?cQuery=".md5($mT117[T117_Id].$mT117[T117_D024_Id])."'>";
       $cInfoBoleto.="<img src='http://201.22.57.137:8080/hardness/imagens/jpg/boleto.jpg' border=0><font color=blue>";
      $cInfoBoleto.="</a>";
    $cInfoBoleto.="</td>";
    $cInfoBoleto.="<td class=fonte style='font-size:14px;border-right:0px solid silver'>";
      $cInfoBoleto.="<a target='_blank' href='http://201.22.57.137:8080/hard/boleto/gerar_boleto_hardness.php?cQuery=".md5($mT117[T117_Id].$mT117[T117_D024_Id])."'>";
       $cInfoBoleto.="Vencimento em <b>".troca_data_dma($mT117[T117_Data_Vencimento])."</b><br>";
       $cInfoBoleto.="<font style='color:green'>Clique aqui para imprimir o boleto</font>";
      $cInfoBoleto.="</a>";
    $cInfoBoleto.="</td>";
   $cInfoBoleto.="</tr>";
  $cInfoBoleto.="<tr>";
   $cInfoBoleto.="<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>";
     $cInfoBoleto.="O pagamento pode ser realizado pela internet ou em qualquer agência bancária até a data do vencimento.";
     $cInfoBoleto.="<BR>";
     $cInfoBoleto.="<BR>";
     $cInfoBoleto.="Att.";
    $cInfoBoleto.="</td>";
   $cInfoBoleto.="</tr>";
  $cInfoBoleto.="</table>";

$Propaganda="<table><tr><td rowspan=2><img src='http://www.hardness.com.br/2010/images/logo_hardness.png'></td><td><font style='$Fonte'>E-mail enviado por <BR><b>Harndess Sistemas</b></td></tr><tr><td valign=top><font style='$Fonte'><font color=blue>http://www.hardness.com.br</font></td></tr></table>";

$Corpo=$cInfoBoleto."<BR><b>".str_replace("\n","<BR>",$Corpo)."</b>".$Propaganda;

//-- INICIO DA CRIAÇÃO DO E-MAIL
$mail = new PHPMailer();

$mail->From     = "$De";
$mail->FromName = StrToMaiusculoMinusculo("$mT117[T117_Empresa_Usuaria_Nome]");
$mail->Host     = "smtp.hardness1.com.br";
$mail->Mailer   = "smtp";
$mail->SMTPAuth = true;
$mail->Username = 'nfe@hardness1.com.br';
$mail->Password = 'hard3871';
$mail->Subject	= "$Assunto";
$mail->Body     = "$Corpo";
$mail->AltBody  = "ALT BODY";
$mail->CharSet  = 'iso-8859-1';
$mail->SetLanguage("br");

$mPara=explode(";",$Para);
for($x=0;$x<count($mPara);$x++)
{
   $mail->AddAddress($mPara[$x]);
}

if (!empty($ComCopia))
{
   $mail->AddCc("$ComCopia");
}
if (!empty($ComCopiaOculta))
{
   $mail->AddBcc("$ComCopiaOculta");
}
$mail->AddBcc("suporte@hardness.com.br");

//$mail->AddAttachment($cArquivoPDF,basename($cArquivoPDF));
//$mail->AddAttachment($cArquivoXML,basename($cArquivoXML));

if($mail->Send())
{
   mysql_query("update T117 set T117_Data_Envio_Email=now() where T117_Id='$T117_Id'");
   msg_alerta("E-mail enviado com sucesso para:/p$Para/p$ComCopia/p$ComCopiaOculta");
   echo "<script>parent.MostrarMensagem('none');parent.FecharFormularioEmail()</script>";
}
else
{
   $cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
   $cErro=str_replace(array("<p>","</p>"),"/p/p",$cErro);
   echo "<script>parent.MostrarMensagem('none');</script>";
   msg_alerta("NÃO FOI POSSIVEL ENVIAR O E-MAIL PARA:/p$Para/p$ComCopia/p$ComCopiaOculta/p/p".$cErro);
   exit;
}

?>
