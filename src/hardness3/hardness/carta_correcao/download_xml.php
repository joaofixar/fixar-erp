<?php

//-- Cria ou inicia uma sessão
$Pular_Script='S';
//-- Cria ou inicia uma sessão
require "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

//-- Funções Genéricas em PHP
require "../func/funcoes_genericas.inc";

set_time_limit(0);

$nT199_Id = $_GET['T199_Id'];

$cClausula  = " select ";
$cClausula .= " T007_Chave_Acesso_Nfe, ";
$cClausula .= " T199_Sequencial_Evento, ";
$cClausula .= " T199_Flag_Enviado, ";
$cClausula .= " T200_Retorno_XML ";
$cClausula .= " from T199 ";
$cClausula .= " left join T200 on T200_T199_Id=T199_Id and T200_Status='135' ";
$cClausula .= " left join T007 on T199_T007_Id=T007_Id ";
$cClausula .= " where T199_Id='$nT199_Id' ";
$cSql_T199=sql_query($cClausula);
$mT199=mysql_fetch_array($cSql_T199);

if ($mT199['T199_Flag_Enviado']=='N')
{
   msg_alerta("Essa carta de correção ainda não foi enviada");
   exit;
}

if (empty($mT199['T200_Retorno_XML']))
{
    msg_alerta("Essa carta de correção ainda não tem xml de retorno");
    exit;
}

    // We'll be outputting a PDF
    header('Content-type: text/xml');

    // It will be called downloaded.pdf
    header('Content-Disposition: attachment; filename="'.$mT199['T007_Chave_Acesso_Nfe']."_".$mT199['T199_Sequencial_Evento'].".xml".'"');

    // The PDF source is in original.pdf
    echo $mT199['T200_Retorno_XML'];

?>