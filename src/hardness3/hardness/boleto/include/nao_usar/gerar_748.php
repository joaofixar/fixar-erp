<?php
namespace hardness;
require_once('../../old/lib/funcoes.php');
// include_once('../../old/lib/funcoes.php');
require_once("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

// FONTE: https://github.com/CobreGratis/boletophp/blob/master/boleto_sicredi.php

$codigobanco = "748";
$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = fator_vencimento($T117_Data_Vencimento);

$nValor_Total=number_format($nValor_Total,2,'','');
//valor tem 10 digitos, sem virgula
$valor = formata_numero($nValor_Total,10,0,"valor");
//agencia � 4 digitos
$agencia = formata_numero($mD007['D007_Numero_Agencia'],4,0);
$cedente = formata_numero($mD007['D007_Codigo_Cedente'],5,0);
//posto da cooperativa de credito � dois digitos
// Fixo 18
// No manual este campos est� fixo como 2
$posto = formata_numero($mD007['D007_Posto_Cooperativa'],2,0);
//conta � 5 digitos
$conta = formata_numero($mD007['D007_Numero_Conta'],5,0);
//dv da conta
$conta_dv = formata_numero($mD007['D007_Digito_Verificador_Conta'],1,0);
//carteira � 2 caracteres
if ($mD007['D007_Tipo_Carteira_Banco_Brasil']!='1')
{
   mysqli_query("ROLLBACK");
   msg_alerta("A Carteira deve ser 1 para Sicredi");
   exit;
}
$carteira = $mD007['D007_Tipo_Carteira_Banco_Brasil'];
//fillers - zeros Obs: filler1 contera 1 quando houver valor expresso no campo valor
$filler1 = 1;
$filler2 = 0;
// Byte de Identifica��o do cedente 1 - Cooperativa; 2 a 9 - Cedente - Fixo 2
$byteidt = 2;
// Codigo referente ao tipo de cobran�a: "1" - COM REGISTRO - 3 SEM REGISTRO;
$tipo_cobranca = $mD007['D007_Flag_Tipo_Registro'];
// Codigo referente ao tipo de carteira: "1" - Carteira Simples 
$tipo_carteira = 1;
//nosso n�mero (sem dv) � 8 digitos
$nnum = date("y") . $byteidt . formata_numero($mD007['D007_Nosso_Numero'],5,0);
//calculo do DV do nosso n�mero
// echo "agencia: ".$agencia."<br />";
// echo "posto: ".$posto."<br />";
// echo "conta: ".$conta."<br />";
// echo "nnum: ".$nnum."<br />";
$dv_nosso_numero = digitoVerificador_nossonumero("$agencia$posto$cedente$nnum");
$nossonumero_dv ="$nnum$dv_nosso_numero";
//forma��o do campo livre
$campolivre = "$tipo_cobranca$tipo_carteira$nossonumero_dv$agencia$posto$cedente$filler1$filler2";
$campolivre_dv = $campolivre . digitoVerificador_campolivre($campolivre); 
// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$campolivre_dv", 9, 0);
// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$campolivre_dv";
// DEBUGS
/*
echo "linha: ".$linha."<br /><br />";
echo "codigobanco: ".$codigobanco . 
    "nummoeda: ".$nummoeda .
    "dv: ".$dv .
    "fator_vencimento: ".$fator_vencimento .
    "valor: ".$valor .
    "campolivre_dv: ".$campolivre_dv;

$linha_digitavel = monta_linha_digitavel($linha);

echo "<br /><br />linha montada: ".$linha_digitavel;
die();
*/

$linha_digitavel = monta_linha_digitavel($linha);
$codigo_barras = $linha;
// Formata strings para impressao no boleto
$nossonumero = substr($nossonumero_dv,0,2).'/'.substr($nossonumero_dv,2,6).'-'.substr($nossonumero_dv,8,1);

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
$cClausula .= " T117_Linha_Digitavel='".$linha_digitavel."', ";
$cClausula .= " T117_Codigo_Barras='".$codigo_barras."' ";
$cClausula .= " where T117_Id='$nT117_Id' ";
mysqli_query($cClausula);
$cErro = mysqli_error();
if (!empty($cErro))
{
   mysqli_query("ROLLBACK");
   msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
   exit;
}
?>