<?php
namespace hardness;
require_once('../../old/lib/funcoes.php');
include("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");
$fixo     = "9";   // Numero fixo para a posição 05-05
$ios	  = "0";   // IOS - somente para Seguradoras (Se 7% informar 7, limitado 9%)

$codigobanco = "033";
$codigo_banco_com_dv = b033_geraCodigoBanco($codigobanco);
$nummoeda = "9";

$fator_vencimento = b033_fator_vencimento(gCorrigeData($T117_Data_Vencimento));

$nValor_Total=number_format($nValor_Total,2,',','');

//valor tem 10 digitos, sem virgula
$valor = b033_formata_numero($nValor_Total,10,0,"valor");

//agencia é 4 digitos
$agencia = b033_formata_numero($mD007['D007_Numero_Agencia'],4,0);
//conta é 6 digitos
$conta = b033_formata_numero($mD007['D007_Numero_Conta'],6,0);
//dv da conta
$conta_dv = b033_formata_numero($mD007['D007_Digito_Verificador_Conta'],1,0);

//Modalidade Carteira
$carteira = $mD007['D007_Tipo_Carteira_Banco_Brasil'];
//codigocedente deve possuir 7 caracteres
$codigocliente = b033_formata_numero($mD007['D007_Codigo_Cedente'],7,0);

//nosso número (sem dv) é 11 digitos
$nnum = b033_formata_numero($mD007['D007_Nosso_Numero'],7,0);
//dv do nosso número
$dv_nosso_numero = b033_digitoVerificador_nossonumero($nnum);
// nosso número (com dvs) são 13 digitos
$nossonumero = "00000".$nnum.$dv_nosso_numero;

$vencimento = $T117_Data_Vencimento;

$vencjuliano = b033_dataJuliano($vencimento);

// 43 numeros para o calculo do digito verificador do codigo de barras
$barra = "$codigobanco$nummoeda$fator_vencimento$valor$fixo$codigocliente$nossonumero$ios$carteira";

//$barra = "$codigobanco$nummoeda$fixo$codigocliente$nossonumero$ios$carteira";
$dv = b033_digitoVerificador_barra($barra);
// Numero para o codigo de barras com 44 digitos
$linha = substr($barra,0,4) . $dv . substr($barra,4);

$linha_digitavel = b033_monta_linha_digitavel($linha);

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
$cClausula .= " T117_Linha_Digitavel='".$linha_digitavel."', ";
$cClausula .= " T117_Codigo_Barras='".$linha."' ";
$cClausula .= " where T117_Id='$nT117_Id' ";
mysqli_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
   mysqli_query("ROLLBACK");
   msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
   exit;
}

?>