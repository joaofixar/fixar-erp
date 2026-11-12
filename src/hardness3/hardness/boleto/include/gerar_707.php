<?php
namespace hardness;

require_once('../../old/lib/funcoes.php');
require_once("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");


$banco         = "707"; // Código banco Daycoval 
$moeda         = "9"; // 9 = real
$fatorVenc     = b707_fator_vencimento(troca_data_dma($T117_Data_Vencimento)); // Fator vencimento
$valor         = b707_formata_numero(number_format($nValor_Total,2,',',''), 10, 0);
$agencia       = b707_formata_numero($mD007['D007_Numero_Agencia'], 4, 0); // Agencia 
$carteira      = $mD007['D007_Tipo_Carteira_Banco_Brasil']; // Carteira
$operacao      = $mD007['D007_Codigo_Cliente']; // Operação
//$digBanco      = "7"; // Digito do banco Daycoval
//$agenciaDiv    = $mD007['D007_Digito_Verificador_Agencia']; // Digito verificador da agencia
//$conta         = b707_formata_numero($mD007['D007_Numero_Conta'] . $mD007['D007_Digito_Verificador_Conta'], 9, 0); // Conta corrente com digito

if($g['C031']['atualizarNossoNumeroBoleto'] != 'S' && !empty($mD007['T117_Nosso_Numero'])){
   $nossoNumero = str_pad( floor( $mD007['T117_Nosso_Numero'] ), 11, "0", STR_PAD_LEFT); // Pega o nosso número que já está no boleto
   log("Entrou no if nosso numero : ". $nossoNumero);
}else{
   $nossoNumero = str_pad($mD007['D007_Nosso_Numero'],10,"0", STR_PAD_LEFT); // Pega o próximo nosso número do banco
   $nossoNumero .= b707_modulo_10($agencia.$carteira.$nossoNumero); // Adiciona o digito verificador
   log("Entrou no else nosso numero : ". $nossoNumero);
}
//$tipoCobranca  = "2"; // Cobranca registrada

$DAC           = b707_modulo_11($banco . $moeda . $fatorVenc . $valor . $agencia . $carteira. $operacao . $nossoNumero);

$codigoBarras  = $banco . $moeda . $DAC . $fatorVenc . $valor . $agencia . $carteira . $operacao . $nossoNumero;
log("Montando o cod barras : ".$codigoBarras);

log("Montando a linha digitavel por linha");
$linha1        = substr($banco . $moeda . $agencia . $carteira,0,9);
log("Linha1 : ".$linha1);
$linha2        = substr($carteira . $operacao . $nossoNumero,1,10);
log("Linha2 : ".$linha2);
$linha3        = substr($nossoNumero,1,10);
log("Linha3 : ".$linha3);
$linha4        = $DAC . $fatorVenc . $valor;
log("Linha4 : ".$linha4);

$linhaDigitavel= $linha1 . b707_modulo_10($linha1) . $linha2 . b707_modulo_10($linha2) . $linha3 . b707_modulo_10($linha3) . $linha4; 
log(" Montando a linha digitavel final : ". $linhaDigitavel);

//$T002['T117_Nosso_Numero'] = $carteira."/".substr($nossoNumero,0,-1)."-".substr($nossoNumero,-1);
$nossonumero = $carteira."/".substr($nossoNumero,0,-1)."-".b707_modulo_10($agencia.$carteira.$nossoNumero);
log("Montou nosso numero boleto : ". $nossonumero);

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

