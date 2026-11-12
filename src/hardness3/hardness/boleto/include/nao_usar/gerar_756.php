<?php
namespace hardness;
include_once('../../old/lib/funcoes.php');
require_once("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

$codigobanco = "756";
$codigo_banco_com_dv = b756_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = b756_fator_vencimento($T117_Data_Vencimento);


$nValor_Total=number_format($nValor_Total,2,'','');

//valor tem 10 digitos, sem virgula. preenche com 0
$valor = b756_formata_numero($nValor_Total,10,0,"valor");

//agencia � 4 digitos
$agencia = b756_formata_numero($mD007['D007_Numero_Agencia'],4,0);
//conta � 6 digitos
$conta = b756_formata_numero($mD007['D007_Numero_Conta'],6,0);
//dv da conta
$conta_dv = b756_formata_numero($mD007['D007_Digito_Verificador_Conta'],1,0);

//carteira � 2 caracteres
if ($mD007['D007_Tipo_Carteira_Banco_Brasil']!='1' and $mD007['D007_Tipo_Carteira_Banco_Brasil']!='3')
{
   mysqli_query("ROLLBACK");
   msg_alerta("A Carteira deve ser 1 ou 3 para o Sicoob");
   exit;
}

//carteira
$tpCarteira = b756_formata_numero($mD007['D007_Tipo_Carteira_Banco_Brasil'],1,0); 

//nosso n�mero (sem dv) � 8 digitos
$nnum = b756_formata_numero($mD007['D007_Nosso_Numero'],8,0);

//c�digo do associado/cliente (sem dv) � 7 digitos
$codigo_cedente = b756_formata_numero($mD007['D007_Codigo_Cedente'],7,0);


$var_dv_nosso_numero = "$agencia$codigo_cedente$codigo_cedente_dv$nnum";

//dv do nosso n�mero
$dv_nosso_numero = b756_digitoVerificador_nossonumero($var_dv_nosso_numero);

//N�mero de parcelas 
$numParcelas = b756_formata_numero($mD007['T002_Numero_Parcela'],3,0);

// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = b756_digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$agencia$nnum$conta_cedente".'0', 9, 0);

// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$tpCarteira$agencia"."01"."$codigo_cedente$nnum$numParcelas";
//				<03>	<1>		<1> 	<04>		<10>	<01>	  <04>     <2>		<7>			<08>	  <03>
//				<0-2> 	<3-3> 	<4-4>	<5-8>		<9-18>  <19-19>   <20-23> <24-25>   <26-32>     <33-40>  <40-43>  

$nossonumero = substr($nnum,2).'-'.$dv_nosso_numero;
$agencia_codigo = $agencia."/". $codigo_cedente;

$codigo_barras = $linha;
$linha_digitavel = b756_monta_linha_digitavel($linha);

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
