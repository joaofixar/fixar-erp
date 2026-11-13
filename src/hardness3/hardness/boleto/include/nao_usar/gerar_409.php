<?php
namespace hardness;
include("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

$codigobanco = "409";
$codigo_banco_com_dv = b409_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$cvt = "04";


$fator_vencimento = b409_fator_vencimento(gCorrigeData($cT117_Data_Vencimento));
$vencimento_amd = substr(str_replace("-","",$cT117_Data_Vencimento),2,6);
$dv_agencia=$mD007['D007_Digito_Verificador_Agencia'];

$nValor_Total=number_format($nValor_Total,2,'.','');

//valor tem 10 digitos, sem virgula
$valor = b409_formata_numero($nValor_Total,10,0,"valor");
//agencia � 4 digitos
$agencia = b409_formata_numero($mD007['D007_Numero_Agencia'],4,0);
//conta � 6 digitos + 1 do dv
$conta = b409_formata_numero($mD007['D007_Numero_Conta'],6,0);
//dv da conta
$conta_dv = b409_formata_numero($mD007['D007_Digito_Verificador_Conta'],1,0);

//nosso n�mero (sem dv) � 14 digitos
$nnum = b409_formata_numero(substr($mD007['D007_Nosso_Numero'],0,10),10,0);
//dv do nosso n�mero
$dv_nosso_numero = b409_modulo_11($nnum);
$nossonumero_dv = $nnum.$dv_nosso_numero;
$SuperDigitoNossoNumero = b409_modulo_11('1'.$nossonumero_dv);

//codigo_cliente � 6 digitos + 1 do dv
$codigo_cliente = b409_formata_numero($xCodigo_Cedente,7,0);


//-- Tamanho 3 - N�mero de identifica��o do Unibanco: 409 (n�mero FIXO)
$Codigo_Barras  = $codigobanco;

//-- Tamanho 1 - C�digo da moeda. Real (R$)=9 (n�mero FIXO)
$Codigo_Barras .= $nummoeda;

//-- Tamanho 4 - fator de vencimento em 4 algarismos, conforme tabela da p�gina 14
$Codigo_Barras .= str_pad(substr($fator_vencimento,0,4),4,'0',STR_PAD_LEFT);

//-- Tamanho 10 - valor do t�tulo com zeros � esquerda
$Codigo_Barras .= str_pad(substr($valor,0,10),10,'0',STR_PAD_LEFT);

//-- Tamanho 2 - C�digo para transa��o CVT =  04 (n�mero FIXO) (04=5539-5)
$Codigo_Barras .= str_pad(substr($cvt,0,2),2,'0',STR_PAD_LEFT);

//-- Tamanho 6 - data de vencimento (AAMMDD)
$Codigo_Barras .= str_pad(substr($vencimento_amd,0,6),6,'0',STR_PAD_LEFT);

//-- Tamanho 5 - C�digo da ag�ncia + d�gito verificador
$Codigo_Barras .= str_pad(substr($agencia.$dv_agencia,0,6),5,'0',STR_PAD_LEFT);

//-- "Nosso N�mero" (NNNNNNNNNND) onde D � o d�gito a ser calculado pelo M�dulo 11 (p�gina 11)
$Codigo_Barras .= $nossonumero_dv;

//-- Super d�gito do "Nosso N�mero" onde S � o Super D�gito calculado pelo m�dulo 11 (p�gina 11) -
//-- utilizando os algarismos do "Nosso N�mero" acrescido do n�mero 1 � esquerda = 1NNNNNNNNNNDS
$Codigo_Barras .= $SuperDigitoNossoNumero;


// 43 numeros para o calculo do digito verificador
$dv = b409_digitoVerificador("$Codigo_Barras");


//-- Tamanho 3 - N�mero de identifica��o do Unibanco: 409 (n�mero FIXO)
$Codigo_Barras  = $codigobanco;

//-- Tamanho 1 - C�digo da moeda. Real (R$)=9 (n�mero FIXO)
$Codigo_Barras .= $nummoeda;

//-- d�gito verificador do C�DIGO DE BARRAS
//-- Calculado pelo m�dulo 11 (p�gina 11), onde dever� ser utilizado as 43 d�gitos desta seq��ncia num�rica que dar� origem ao C�DIGO DE BARRAS
$Codigo_Barras .= $dv;

//-- Tamanho 4 - fator de vencimento em 4 algarismos, conforme tabela da p�gina 14
$Codigo_Barras .= str_pad(substr($fator_vencimento,0,4),4,'0',STR_PAD_LEFT);

//-- Tamanho 10 - valor do t�tulo com zeros � esquerda
$Codigo_Barras .= str_pad(substr($valor,0,10),10,'0',STR_PAD_LEFT);

//-- Tamanho 2 - C�digo para transa��o CVT =  04 (n�mero FIXO) (04=5539-5)
$Codigo_Barras .= str_pad(substr($cvt,0,2),2,'0',STR_PAD_LEFT);

//-- Tamanho 6 - data de vencimento (AAMMDD)
$Codigo_Barras .= str_pad(substr($vencimento_amd,0,6),6,'0',STR_PAD_LEFT);

//-- Tamanho 5 - C�digo da ag�ncia + d�gito verificador
$Codigo_Barras .= str_pad(substr($agencia.$dv_agencia,0,6),5,'0',STR_PAD_LEFT);

//-- "Nosso N�mero" (NNNNNNNNNND) onde D � o d�gito a ser calculado pelo M�dulo 11 (p�gina 11)
$Codigo_Barras .= $nossonumero_dv;

//-- Super d�gito do "Nosso N�mero" onde S � o Super D�gito calculado pelo m�dulo 11 (p�gina 11) -
//-- utilizando os algarismos do "Nosso N�mero" acrescido do n�mero 1 � esquerda = 1NNNNNNNNNNDS
$Codigo_Barras .= $SuperDigitoNossoNumero;

//echo $Codigo_Barras;

// Numero para o codigo de barras com 44 digitos
$linha = $Codigo_Barras;

//echo "<font style='font-famly:arial;font-size:30px;'<hr>B:40999439500001556220409101904880816017784100<BR>H:$linha<BR><hr>";


$Linha_Digitavel  = $codigobanco;

$Linha_Digitavel  .= $nummoeda;

$Linha_Digitavel  .= $cvt;

$Linha_Digitavel  .= substr($codigo_cliente,0,4);

$Linha_Digitavel  .= substr($codigo_cliente,0,4);


$nossonumero = "1/".$nnum.'-'.$dv_nosso_numero."/".$SuperDigitoNossoNumero;
$agencia_codigo = $agencia."-".$dv_agencia."/". $conta ."-". $conta_dv;

$dadosboleto['"codigo_barras"'] = $linha;
$dadosboleto['"linha_digitavel"'] = b409_monta_linha_digitavel($linha);

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
$cClausula .= " T117_Linha_Digitavel='".$dadosboleto['"linha_digitavel"']."', ";
$cClausula .= " T117_Codigo_Barras='".$dadosboleto['"codigo_barras"']."' ";
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
