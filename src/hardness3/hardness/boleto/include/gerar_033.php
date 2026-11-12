<?php
namespace hardness;
require_once('../../old/lib/funcoes.php');
include_once("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");
$fixo     = "9";   // Numero fixo para a posição 05-05
$ios	  = "0";   // IOS - somente para Seguradoras (Se 7% informar 7, limitado 9%)

$log_boleto = '';

$codigobanco = "033";
$codigo_banco_com_dv = b033_geraCodigoBanco($codigobanco);
$nummoeda = "9";

$fator_vencimento = b033_fator_vencimento(troca_data_dma($T117_Data_Vencimento));

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

$log_boleto .= "nosso numero = ".$mD007['D007_Nosso_Numero'];

//Se for uma atualização do boleto, deve pegar o nosso número do T117 e não do D007
if($g['C031']['atualizarNossoNumeroBoleto'] != 'S' && !empty($mD007['T117_Codigo_Barras'])){
	$partes = substr($mD007['T117_Nosso_Numero'], 0, -1); //retira o dígito verificador
	$nnum = substr_replace($partes,'',0,5); //retira os 00000 do início
}else{
	$nnum = b033_formata_numero($mD007['D007_Nosso_Numero'],7,0);
}

$log_boleto .= "nosso numero FORMATADO = ".$nnum;

//dv do nosso número
$dv_nosso_numero = b033_digitoVerificador_nossonumero($nnum);

$log_boleto .= "nosso numero DV = ".$dv_nosso_numero;

// nosso número (com dvs) são 13 digitos
$nossonumero = "00000".$nnum.$dv_nosso_numero;

$log_boleto .= "nosso numero final = ".$nossonumero;

$vencimento = $T117_Data_Vencimento;

$vencjuliano = b033_dataJuliano($vencimento);

// 43 numeros para o calculo do digito verificador do codigo de barras
//        3           1=4      4=6              10=10 1=20 7=21          13=28       1=41 3=42  
$barra = "$codigobanco$nummoeda$fator_vencimento$valor$fixo$codigocliente$nossonumero$ios$carteira";

$log_boleto .= "Montagem = Codigo banco = $codigobanco NumMoeda = $nummoeda Fator venc = $fator_vencimento $valor = $valor Fixo = $fixo Codigo cliente = $codigocliente Nosso = $nossonumero IOS = $ios Carteira = $carteira";

$log_boleto .= 'Código barras: '.$barra.'\n\n';

//$barra = "$codigobanco$nummoeda$fixo$codigocliente$nossonumero$ios$carteira";
$dv = b033_digitoVerificador_barra($barra);

$log_boleto .= 'Digito verificador: '.$dv.'\n\n';

// Numero para o codigo de barras com 44 digitos
$linha = substr($barra,0,4) . $dv . substr($barra,4);

$log_boleto .= 'Linha: '.$linha.'\n\n';

$linha_digitavel = b033_monta_linha_digitavel($linha);

$log_boleto .= 'Linha digitavel: '.$linha_digitavel.'\n\n';

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
$cClausula .= " T117_Linha_Digitavel='".$linha_digitavel."', ";
$cClausula .= " T117_Codigo_Barras='".$linha."' ";
$cClausula .= " where T117_Id='$nT117_Id' ";
mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
   mysql_query("ROLLBACK");
   msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
   exit;
}

$cClausula  = " update T117 set ";
$cClausula .= " T117_Log_Boleto='".$log_boleto."' ";
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