<?php
namespace hardness;

require_once('../../old/lib/funcoes.php');
include("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

$codigobanco = "409";
$codigo_banco_com_dv = b409_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$cvt = "04";


$fator_vencimento = b409_fator_vencimento(troca_data_dma($cT117_Data_Vencimento));
$vencimento_amd = substr(str_replace("-","",$cT117_Data_Vencimento),2,6);
$dv_agencia=$mD007[D007_Digito_Verificador_Agencia];

$nValor_Total=number_format($nValor_Total,2,'.','');

//valor tem 10 digitos, sem virgula
$valor = b409_formata_numero($nValor_Total,10,0,"valor");
//agencia é 4 digitos
$agencia = b409_formata_numero($mD007[D007_Numero_Agencia],4,0);
//conta é 6 digitos + 1 do dv
$conta = b409_formata_numero($mD007[D007_Numero_Conta],6,0);
//dv da conta
$conta_dv = b409_formata_numero($mD007[D007_Digito_Verificador_Conta],1,0);

//nosso número (sem dv) é 14 digitos
$nnum = b409_formata_numero(substr($mD007[D007_Nosso_Numero],0,10),10,0);
//dv do nosso número
$dv_nosso_numero = b409_modulo_11($nnum);
$nossonumero_dv = $nnum.$dv_nosso_numero;
$SuperDigitoNossoNumero = b409_modulo_11('1'.$nossonumero_dv);

//codigo_cliente é 6 digitos + 1 do dv
$codigo_cliente = b409_formata_numero($xCodigo_Cedente,7,0);


//-- Tamanho 3 - Número de identificação do Unibanco: 409 (número FIXO)
$Codigo_Barras  = $codigobanco;

//-- Tamanho 1 - Código da moeda. Real (R$)=9 (número FIXO)
$Codigo_Barras .= $nummoeda;

//-- Tamanho 4 - fator de vencimento em 4 algarismos, conforme tabela da página 14
$Codigo_Barras .= str_pad(substr($fator_vencimento,0,4),4,'0',STR_PAD_LEFT);

//-- Tamanho 10 - valor do título com zeros à esquerda
$Codigo_Barras .= str_pad(substr($valor,0,10),10,'0',STR_PAD_LEFT);

//-- Tamanho 2 - Código para transação CVT =  04 (número FIXO) (04=5539-5)
$Codigo_Barras .= str_pad(substr($cvt,0,2),2,'0',STR_PAD_LEFT);

//-- Tamanho 6 - data de vencimento (AAMMDD)
$Codigo_Barras .= str_pad(substr($vencimento_amd,0,6),6,'0',STR_PAD_LEFT);

//-- Tamanho 5 - Código da agência + dígito verificador
$Codigo_Barras .= str_pad(substr($agencia.$dv_agencia,0,6),5,'0',STR_PAD_LEFT);

//-- "Nosso Número" (NNNNNNNNNND) onde D é o dígito a ser calculado pelo Módulo 11 (página 11)
$Codigo_Barras .= $nossonumero_dv;

//-- Super dígito do "Nosso Número" onde S é o Super Dígito calculado pelo módulo 11 (página 11) -
//-- utilizando os algarismos do "Nosso Número" acrescido do número 1 à esquerda = 1NNNNNNNNNNDS
$Codigo_Barras .= $SuperDigitoNossoNumero;


// 43 numeros para o calculo do digito verificador
$dv = b409_digitoVerificador("$Codigo_Barras");


//-- Tamanho 3 - Número de identificação do Unibanco: 409 (número FIXO)
$Codigo_Barras  = $codigobanco;

//-- Tamanho 1 - Código da moeda. Real (R$)=9 (número FIXO)
$Codigo_Barras .= $nummoeda;

//-- dígito verificador do CÓDIGO DE BARRAS
//-- Calculado pelo módulo 11 (página 11), onde deverá ser utilizado as 43 dígitos desta seqüência numérica que dará origem ao CÓDIGO DE BARRAS
$Codigo_Barras .= $dv;

//-- Tamanho 4 - fator de vencimento em 4 algarismos, conforme tabela da página 14
$Codigo_Barras .= str_pad(substr($fator_vencimento,0,4),4,'0',STR_PAD_LEFT);

//-- Tamanho 10 - valor do título com zeros à esquerda
$Codigo_Barras .= str_pad(substr($valor,0,10),10,'0',STR_PAD_LEFT);

//-- Tamanho 2 - Código para transação CVT =  04 (número FIXO) (04=5539-5)
$Codigo_Barras .= str_pad(substr($cvt,0,2),2,'0',STR_PAD_LEFT);

//-- Tamanho 6 - data de vencimento (AAMMDD)
$Codigo_Barras .= str_pad(substr($vencimento_amd,0,6),6,'0',STR_PAD_LEFT);

//-- Tamanho 5 - Código da agência + dígito verificador
$Codigo_Barras .= str_pad(substr($agencia.$dv_agencia,0,6),5,'0',STR_PAD_LEFT);

//-- "Nosso Número" (NNNNNNNNNND) onde D é o dígito a ser calculado pelo Módulo 11 (página 11)
$Codigo_Barras .= $nossonumero_dv;

//-- Super dígito do "Nosso Número" onde S é o Super Dígito calculado pelo módulo 11 (página 11) -
//-- utilizando os algarismos do "Nosso Número" acrescido do número 1 à esquerda = 1NNNNNNNNNNDS
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

$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = b409_monta_linha_digitavel($linha);

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
$cClausula .= " T117_Linha_Digitavel='".$dadosboleto["linha_digitavel"]."', ";
$cClausula .= " T117_Codigo_Barras='".$dadosboleto["codigo_barras"]."' ";
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
