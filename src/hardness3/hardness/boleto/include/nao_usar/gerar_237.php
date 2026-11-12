<?php
namespace hardness;
include("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

$codigobanco = "237";
$codigo_banco_com_dv = b237_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = b237_fator_vencimento(gCorrigeData($cT117_Data_Vencimento));


$nValor_Total=number_format($nValor_Total,2,'','');

//valor tem 10 digitos, sem virgula
$valor = b237_formata_numero($nValor_Total,10,0,"valor");

//agencia � 4 digitos
$agencia = b237_formata_numero($mD007['D007_Numero_Agencia'],4,0);
//conta � 6 digitos
$conta = b237_formata_numero($mD007['D007_Numero_Conta'],6,0);
//dv da conta
$conta_dv = b237_formata_numero($mD007['D007_Digito_Verificador_Conta'],1,0);

//carteira � 2 caracteres
if ($mD007['D007_Tipo_Carteira_Banco_Brasil']!='06' and $mD007['D007_Tipo_Carteira_Banco_Brasil']!='03')
{
   mysqli_query("ROLLBACK");
   msg_alerta("A Carteira deve ser 03 ou 06 para o banco bradesco");
   exit;
}

//nosso n�mero (sem dv) � 11 digitos
$nnum = b237_formata_numero($mD007['D007_Tipo_Carteira_Banco_Brasil'],2,0).b237_formata_numero($mD007['D007_Nosso_Numero'],11,0);
//dv do nosso n�mero
$dv_nosso_numero = b237_digitoVerificador_nossonumero($nnum);

//conta cedente (sem dv) � 7 digitos
$conta_cedente = b237_formata_numero($mD007['D007_Numero_Conta'],7,0);
//dv da conta cedente
$conta_cedente_dv = b237_formata_numero($mD007['D007_Digito_Verificador_Conta'],1,0);

//$ag_contacedente = $agencia . $conta_cedente;

// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = b237_digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$agencia$nnum$conta_cedente".'0', 9, 0);
// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$agencia$nnum$conta_cedente"."0";

$nossonumero = substr($nnum,0,2).'/'.substr($nnum,2).'-'.$dv_nosso_numero;
$agencia_codigo = $agencia."-".$mD007['D007_Digito_Verificador_Agencia']." / ". $conta_cedente ."-". $conta_cedente_dv;

$codigo_barras = $linha;
$linha_digitavel = b237_monta_linha_digitavel($linha);

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
$cClausula .= " T117_Linha_Digitavel='".$linha_digitavel."', ";
$cClausula .= " T117_Codigo_Barras='".$codigo_barras."' ";
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
