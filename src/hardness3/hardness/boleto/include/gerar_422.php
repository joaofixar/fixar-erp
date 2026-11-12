<?php
namespace hardness;

require_once('../../old/lib/funcoes.php');
require_once("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");


$banco         = "422"; // Código banco safra 
$moeda         = "9"; // 9 = real
$digBanco      = "7"; // Digito do banco safra
$fatorVenc     = b422_fator_vencimento(troca_data_dma($T117_Data_Vencimento)); // Fator vencimento
$valor         = b422_formata_numero(number_format($nValor_Total,2,',',''), 10, 0);
$agencia       = substr(b422_formata_numero($mD007['D007_Numero_Agencia'], 4, 0),0,4); // Agencia 
$agenciaDiv    = ($mD007['D007_Digito_Verificador_Agencia'] == '') ? 0 : $mD007['D007_Digito_Verificador_Agencia']; // Digito verificador da agencia
$conta         = b422_formata_numero($mD007['D007_Numero_Conta'] . $mD007['D007_Digito_Verificador_Conta'], 9, 0); // Conta corrente com digito
if($g['C031']['atualizarNossoNumeroBoleto'] != 'S' && !empty($mD007['T117_Nosso_Numero'])){
   $nossoNumero = str_pad( floor( $mD007['T117_Nosso_Numero'] ), 9, "0", STR_PAD_LEFT); // Pega o nosso número que já está no boleto
}else{
   $nossoNumero = $mD007['D007_Nosso_Numero']; // Pega o próximo nosso número do banco
   $nossoNumero = b422_adiciona_dv_nossonumero($nossoNumero); // Adiciona o digito verificador
}
$tipoCobranca  = "2"; // Cobranca registrada

$DAC           = b422_modulo_11($banco . $moeda . $fatorVenc . $valor . $digBanco . $agencia . $agenciaDiv. $conta . $nossoNumero . $tipoCobranca);

$codigoBarras  = $banco . $moeda . $DAC . $fatorVenc . $valor . $digBanco . $agencia . $agenciaDiv . $conta . $nossoNumero . $tipoCobranca;

$linha1        = $banco . $moeda . $digBanco . $agencia;
$linha2        = $agenciaDiv . $conta;
$linha3        = $nossoNumero . $tipoCobranca;
$linha4        = $DAC . $fatorVenc . $valor;

$linhaDigitavel= $linha1 . b422_modulo_10($linha1) . $linha2 . b422_modulo_10($linha2) . $linha3 . b422_modulo_10($linha3) . $linha4; 

//$T002['T117_Nosso_Numero'] = $nossoNumero;

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossoNumero', ";
$cClausula .= " T117_Linha_Digitavel='".$linhaDigitavel."', ";
$cClausula .= " T117_Codigo_Barras='".$codigoBarras."' ";
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

