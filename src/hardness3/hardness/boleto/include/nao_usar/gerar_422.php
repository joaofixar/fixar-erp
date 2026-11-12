<?php
namespace hardness;
include("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

$codigobanco = "422";
$codigo_banco_com_dv = b422_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$cT117_Data_Vencimento = gCorrigeData($cT117_Data_Vencimento);

//Gerar o codigo de barras

$dadosboleto["codigo_barras"] = gerarCodigoBarras('422',
                                          $cfbagencia,
                                          $cfbconta_corrente,
                                          $digito_conta,
                                          $mD007['D007_Tipo_Carteira_Banco_Brasil'],
                                          $vencimento,
                                          $nTotal,
                                          $mD007['D007_Nosso_Numero']);
                                          
$dadosboleto["linha_digitavel"] = b422_monta_linha_digitavel($dadosboleto["codigo_barras"]);

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='".$mD007['D007_Nosso_Numero']."', ";
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