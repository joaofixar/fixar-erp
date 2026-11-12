<?php
namespace hardness;
include("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

// Composi��o Nosso Numero - CEF SIGCB
$dadosboleto["nosso_numero1"] = "000"; // tamanho 3
$dadosboleto["nosso_numero_const1"] = "2"; //constanto 1 , 1=registrada , 2=sem registro
$dadosboleto["nosso_numero2"] = "000"; // tamanho 3
$dadosboleto["nosso_numero_const2"] = "4"; //constanto 2 , 4=emitido pelo proprio cliente
$dadosboleto["nosso_numero3"] = STR_PAD(substr($mD007['D007_Nosso_Numero'],0,9),9,'0',STR_PAD_LEFT); // tamanho 9

$codigobanco = "104";
$codigo_banco_com_dv = b104_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$b104_fator_vencimento = b104_fator_vencimento(gCorrigeData($cT117_Data_Vencimento));

$nValor_Total=number_format($nValor_Total,2,'.','');

//valor tem 10 digitos, sem virgula
$valor = b104_formata_numero(str_replace('.','',$nValor_Total),10,0,"valor");
//agencia � 4 digitos
$agencia = b104_formata_numero($mD007['D007_Numero_Agencia'],4,0);
//conta � 5 digitos
$conta = b104_formata_numero($mD007['D007_Numero_Conta'],5,0);
//dv da conta
$conta_dv = b104_formata_numero($mD007['D007_Digito_Verificador_Conta'],1,0);

//conta cedente (sem dv) com 6 digitos
if (strlen($mD007['D007_Numero_Convenio'])>6)
{
   mysqli_query("ROLLBACK");
   msg_alerta("Campo: Numero do convenio definido na conta bancaria n�o pode ultrapassar 6 caracteres: {$mD007['D007_Numero_Convenio']}");
   exit;
}
$conta_cedente = b104_formata_numero($mD007['D007_Numero_Convenio'],6,0);
//dv da conta cedente
$conta_cedente_dv = b104_digitoVerificador_cedente($conta_cedente);

//campo livre (sem dv) � 24 digitos
$campo_livre = $conta_cedente . $conta_cedente_dv . b104_formata_numero($dadosboleto["nosso_numero1"],3,0) . b104_formata_numero($dadosboleto["nosso_numero_const1"],1,0) . b104_formata_numero($dadosboleto["nosso_numero2"],3,0) . b104_formata_numero($dadosboleto["nosso_numero_const2"],1,0) . b104_formata_numero($dadosboleto["nosso_numero3"],9,0);
//dv do campo livre
$dv_campo_livre = b104_digitoVerificador_nossonumero($campo_livre);
$campo_livre_com_dv ="$campo_livre$dv_campo_livre";

//nosso n�mero (sem dv) � 17 digitos
$nnum = b104_formata_numero($dadosboleto["nosso_numero_const1"],1,0).b104_formata_numero($dadosboleto["nosso_numero_const2"],1,0).b104_formata_numero($dadosboleto["nosso_numero1"],3,0).b104_formata_numero($dadosboleto["nosso_numero2"],3,0).b104_formata_numero($dadosboleto["nosso_numero3"],9,0);
//nosso n�mero completo (com dv) com 18 digitos
$nossonumero = $nnum . b104_digitoVerificador_nossonumero($nnum);

// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = b104_digitoVerificador_barra("$codigobanco$nummoeda$b104_fator_vencimento$valor$campo_livre_com_dv", 9, 0);
// Numero para o codigo de barras com 44 digitos
$codigo_barras = "$codigobanco$nummoeda$dv$b104_fator_vencimento$valor$campo_livre_com_dv";

$linha_digitavel = b104_monta_linha_digitavel($codigo_barras);


$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
$cClausula .= " T117_Linha_Digitavel=".$linha_digitavel.", ";
$cClausula .= " T117_Codigo_Barras=".$codigo_barras." ";
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
