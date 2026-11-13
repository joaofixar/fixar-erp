<?php

// Cria ou inicia uma sessão
include_once("../include/sessao.inc");

//--  Conexão com o banco de dados
include_once("../include/conexao.inc");

// Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

$nD007_Id=$_GET[D007_Id];

$cClausula  = " select ";
$cClausula .= " D007_Instrucoes_Boleto, ";
$cClausula .= " D007_Percentual_Juros_Dia, ";
$cClausula .= " D007_Percentual_Multa ";
$cClausula .= " from D007 ";
$cClausula .= " where D007_Id='$nD007_Id' ";
$cSql_D007 = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
	msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
	exit;
}
$mD007 = mysql_fetch_array($cSql_D007);

$mD007[D007_Instrucoes_Boleto] = str_replace("'","",$mD007[D007_Instrucoes_Boleto]);
$mD007[D007_Instrucoes_Boleto] = str_replace('"','',$mD007[D007_Instrucoes_Boleto]);
$mD007[D007_Instrucoes_Boleto] = str_replace(chr(10),'',$mD007[D007_Instrucoes_Boleto]);
$Instrucoes=explode(chr(13),$mD007[D007_Instrucoes_Boleto]);

echo "<script>";
echo "parent.document.getElementById('T117_Instrucoes').value='$Instrucoes[0]'+String.fromCharCode(13)+'$Instrucoes[1]'+String.fromCharCode(13)+'$Instrucoes[2]'+String.fromCharCode(13)+'$Instrucoes[3]';";
echo "parent.document.getElementById('T117_Percentual_Juros_Dia').value='$mD007[D007_Percentual_Juros_Dia]';";
echo "parent.document.getElementById('T117_Percentual_Multa').value='$mD007[D007_Percentual_Multa]';";
echo "</script>";

?>
