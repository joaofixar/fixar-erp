<?php
namespace hardness;
include("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

$codigobanco = "001";
$codigo_banco_com_dv = b001_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = b001_fator_vencimento(gCorrigeData($cT117_Data_Vencimento));


$nValor_Total=number_format($nValor_Total,2,'.','');

//valor tem 10 digitos, sem virgula
$valor = b001_formata_numero($nValor_Total,10,0,"valor");
//agencia � sempre 4 digitos
$agencia = b001_formata_numero($mD007['D007_Numero_Agencia'],4,0);
//conta � sempre 8 digitos
$conta = b001_formata_numero($mD007['D007_Numero_Conta'],8,0);
//agencia e conta
$agencia_codigo = $agencia."-". b001_modulo_11($agencia) ." / ". $conta ."-". b001_modulo_11($conta);
//Zeros: usado quando convenio de 7 digitos
$livre_zeros='000000';

if (strlen($mD007['D007_Numero_Convenio'])>=8)
{
   $xFormatacaoConvernio="8";
}
else
{
   $xFormatacaoConvernio=strlen($mD007['D007_Numero_Convenio']);
}


if ($xFormatacaoConvernio!=8 and $xFormatacaoConvernio!=7 and $xFormatacaoConvernio!=6)
{
   mysqli_query("ROLLBACK");
   msg_alerta("Formatacao do convenio deve ser 6,7 ou 8, mais esta definida como: ".$xFormatacaoConvernio);
   exit;
}



// Carteira 18 com Conv�nio de 8 d�gitos
if ($xFormatacaoConvernio == "8") {
	$convenio = b001_formata_numero($mD007['D007_Numero_Convenio'],8,0,"convenio");
	// Nosso n�mero de at� 9 d�gitos
	$nossonumero = b001_formata_numero($mD007['D007_Nosso_Numero'],9,0);
	$dv=b001_modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero".$mD007['D007_Tipo_Carteira_Banco_Brasil']);
	$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero".$mD007['D007_Tipo_Carteira_Banco_Brasil'];
	//montando o nosso numero que aparecer� no boleto
	$nossonumero = $convenio . $nossonumero ."-". b001_modulo_11($convenio.$nossonumero);
}

// Carteira 18 com Conv�nio de 7 d�gitos
if ($xFormatacaoConvernio == "7") {
	$convenio = b001_formata_numero($mD007['D007_Numero_Convenio'],7,0,"convenio");
	// Nosso n�mero de at� 10 d�gitos
	$nossonumero = b001_formata_numero($mD007['D007_Nosso_Numero'],10,0);
	$dv=b001_modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero".$mD007['D007_Tipo_Carteira_Banco_Brasil']);
	$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero".$mD007['D007_Tipo_Carteira_Banco_Brasil'];
    $nossonumero = $convenio.$nossonumero;
	//N�o existe DV na composi��o do nosso-n�mero para conv�nios de sete posi��es
}

// Carteira 18 com Conv�nio de 6 d�gitos
if ($xFormatacaoConvernio == "6")
{
	$convenio = b001_formata_numero($mD007['D007_Numero_Convenio'],6,0,"convenio");

    /*
	if ($this->formatacao_nosso_numero == "1")
    {
		// Nosso n�mero de at� 5 d�gitos
		$nossonumero = b001_formata_numero($mD007['D007_Nosso_Numero'],5,0);
		$dv = b001_modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$agencia$conta".$mD007['D007_Tipo_Carteira_Banco_Brasil']);
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$agencia$conta".$mD007['D007_Tipo_Carteira_Banco_Brasil'];
		//montando o nosso numero que aparecer� no boleto
		$nossonumero = $convenio . $nossonumero ."-". b001_modulo_11($convenio.$nossonumero);
	}

	if ($this->formatacao_nosso_numero == "2")
    {
    */
		// Nosso n�mero de at� 17 d�gitos
		$nservico = "21";
		$nossonumero = b001_formata_numero($mD007['D007_Nosso_Numero'],17,0);
		$dv = b001_modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$nservico");
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$nservico";
	//}
}

$codigo_barras = $linha;
$linha_digitavel = b001_monta_linha_digitavel($linha);

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
