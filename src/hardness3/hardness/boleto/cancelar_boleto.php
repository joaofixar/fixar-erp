<?php
/*
Programador: Evantoni Fuchs
Data.......: 11/01/2010
*/

// Cria ou inicia uma sessão
include_once("../include/sessao.inc");

//--  Conexão com o banco de dados
include_once("../include/conexao.inc");

// Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

$nT117_Id=$_REQUEST['T117_Id'];

if (empty($nT117_Id))
{
	msg_alerta("Não foi passado o T117_Id");
}
$cClausula  = " select ";
$cClausula .= " T002_Id ";
$cClausula .= " from T002 ";
$cClausula .= " where (T002_Data_Recebimento!='000-00-00' or T002_Data_Recebimento is null) ";
$cClausula .= " and T002_T117_Id='$nT117_Id' ";
$cClausula .= " and T002_Flag_Cancelada!='S' ";
$cSql_T002=mysql_query($cClausula);
if (mysql_num_rows($cSql_T002)==1)
{
   msg_alerta("O registro relacionado a esse boleto já foi baixado, portanto, este boleto não pode ser cancelado");
   exit;
}
elseif (mysql_num_rows($cSql_T002)>1)
{
   msg_alerta("Existem registros relacionados a esse boleto que já foram baixados,portanto, este boleto não pode ser cancelado");
   exit;
}

$cClausula  = " select ";
$cClausula .= " T117_Flag_Cancelado ";
$cClausula .= " from T117 ";
$cClausula .= " where T117_Id='$nT117_Id' ";
$cSql_T117=mysql_query($cClausula);
$mT117=mysql_fetch_array($cSql_T117);
echo mysql_error();
if (mysql_num_rows($cSql_T117)==0)
{
   msg_alterta("Boleto não encontrado");
}
if ($mT117[T117_Flag_Cancelado]=='S')
{
   msg_alerta("Boleto já está cancelado");
   echo "<script>parent.parent.Recarrega();</script>";
   exit;
}

mysql_query("SET AUTOCOMMIT=0");
mysql_query("START TRANSACTION");

$cClausula  = " update T117 set ";
$cClausula .= " T117_Flag_Cancelado='S' ";
$cClausula .= " where T117_Id='$nT117_Id' ";
mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{
   mysql_query("ROLLBACK");
   msg_alerta("Erro: $cErro/p/p$cClausula");
   exit;
}

$cClausula  = " update T002 set ";
$cClausula .= " T002_T117_Id='0' ";
$cClausula .= " where T002_T117_Id='$nT117_Id' ";
mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{
   mysql_query("ROLLBACK");
   msg_alerta("Erro: $cErro/p/p$cClausula");
   exit;
}

mysql_query("COMMIT");
echo "<script>parent.parent.Recarrega();</script>";

?>
