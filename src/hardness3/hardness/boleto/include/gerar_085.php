<?php
namespace hardness;
include_once('../../old/lib/funcoes.php');
require_once("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");
$codigobanco = "085";
$codigo_banco_com_dv = b085_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = b085_fator_vencimento(troca_data_dma($T117_Data_Vencimento));

$nValor_Total=number_format($nValor_Total,2,'','');

//valor tem 10 digitos, sem virgula
$valor = b085_formata_numero($nValor_Total,10,0,"valor");

//convenio 6 dígitos
$convenio = b085_formata_numero($mD007['D007_Numero_Convenio'],6,0);

$conta = $mD007[D007_Numero_Conta];
//conta corrente 6 digitos
$contaFormatada = str_pad($mD007[D007_Numero_Conta],8,'0');

//dv da conta
$conta_dv = b085_formata_numero($mD007[D007_Digito_Verificador_Conta],1,0);

//agencia � 4 digitos
$agencia = b085_formata_numero($mD007[D007_Numero_Agencia],4,0);

$contaComDv = str_pad("$conta$conta_dv",8,0);

//nosso n�mero (sem dv) � 11 digitos
$nnum = b085_formata_numero($contaComDv,8,0).b085_formata_numero($mD007['D007_Nosso_Numero'],9,0);

$numBoleto = b085_formata_numero($mD007['D007_Nosso_Numero'],9,0);
// $nnum = b085_formata_numero($mD007[D007_Tipo_Carteira_Banco_Brasil],2,0).b085_formata_numero($mD007[D007_Nosso_Numero],9,0);
//dv do nosso n�mero
// $dv_nosso_numero = b085_digitoVerificador_nossonumero($nnum);

//conta cedente (sem dv) � 7 digitos
$conta_cedente = b085_formata_numero($mD007[D007_Numero_Conta],7,0);
//dv da conta cedente
$conta_cedente_dv = b085_formata_numero($mD007[D007_Digito_Verificador_Conta],1,0);

$carteira = b085_formata_numero($mD007[D007_Tipo_Carteira_Banco_Brasil],2,0);
//$ag_contacedente = $agencia . $conta_cedente;

// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = b085_digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$convenio$contaComDv$numBoleto$carteira", 9, 0);

// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$contaComDv$numBoleto$carteira";
// $linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$agencia$nnum$conta_cedente"."0";

// $nossonumero = substr($nnum,0,2).'/'.substr($nnum,2).'-'.$dv_nosso_numero;
$agencia_codigo = $agencia."-".$mD007[D007_Digito_Verificador_Agencia]." / ". $conta_cedente ."-". $conta_cedente_dv;

$codigo_barras = $linha;
$linha_digitavel = b085_monta_linha_digitavel($linha);

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nnum', ";
$cClausula .= " T117_Linha_Digitavel='".$linha_digitavel."', ";
$cClausula .= " T117_Codigo_Barras='".$codigo_barras."' ";
$cClausula .= " where T117_Id='$nT117_Id' ";
mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
   mysql_query("ROLLBACK");
   msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
   exit;
}
?>
