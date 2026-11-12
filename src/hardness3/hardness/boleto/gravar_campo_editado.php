<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

// Conexão com o banco de dados
include "../include/conexao.inc";

// Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

$T117_Id = $_GET[T117_Id];

if ($T117_Id<=0) {
   msg_alerta("T117_Id nao existe!");
   exit;
}
if (empty($Campo_Editado)) {
   msg_alerta("Campo_Editado nao existe!");
   exit;
}

$cClausula  = " select ";
$cClausula .= " T117_T035_Id, ";
$cClausula .= " T117_Flag_Cancelado ";
$cClausula .= " from T117 ";
$cClausula .= " where T117_Id='$T117_Id' ";
$T117 = mysql_query($cClausula);
$mT117=mysql_fetch_array($T117);

if ($Campo_Editado=='T117_Flag_Enviar_Remessa')
{
   if ($mT117[T117_Flag_Cancelado]=='S')
   {
      echo "<script>parent.MostrarMensagem('none');</script>";
      msg_alerta("Esse boleto esta cancelado");
      exit;
   }
   if ($mT117[T117_T035_Id]>0)
   {
      echo "<script>parent.MostrarMensagem('none');</script>";
      msg_alerta("Esse boleto esta na remessa lote numero: $mT117[T117_T035_Id], não pode ser alterado");
      exit;
   }

   if ($Conteudo_Campo_Editado=='true')
   {
       $Conteudo_Campo_Editado='S';
   }
   else
   {
       $Conteudo_Campo_Editado='N';
   }
   mysql_query("update T117 set $Campo_Editado='$Conteudo_Campo_Editado'
                       where T117_Id='$T117_Id'");
}

echo "<script>";
  echo "parent.parent.MostrarMensagem('none');";
echo "</script>";

?>
