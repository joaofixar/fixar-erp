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

//agencia é 4 digitos
$agencia = b756_formata_numero($mD007['D007_Numero_Agencia'],4,0);
//conta é 6 digitos
$conta = b756_formata_numero($mD007['D007_Numero_Conta'],6,0);
//dv da conta
$conta_dv = b756_formata_numero($mD007['D007_Digito_Verificador_Conta'],1,0);

//carteira é 2 caracteres
if ($mD007['D007_Tipo_Carteira_Banco_Brasil']!='1' && $mD007['D007_Tipo_Carteira_Banco_Brasil']!='3')
{
   mysql_query("ROLLBACK");
   msg_alerta("A Carteira deve ser 1 ou 3 para o Sicoob");
   exit;
}

//carteira
$tpCarteira = b756_formata_numero($mD007['D007_Tipo_Carteira_Banco_Brasil'],1,0); 

if($g['C031']['atualizarNossoNumeroBoleto'] != 'S' && !empty($mD007['T117_Codigo_Barras'])){
	$partes = explode("-",$mD007['T117_Nosso_Numero']);
	$nnum = STR_PAD($partes[0],7,'0',STR_PAD_LEFT);
}else{
	//nosso número (sem dv) é 8 digitos
	$nnum = b756_formata_numero($mD007['D007_Nosso_Numero'],7,0);
}

//código do associado/cliente (sem dv) é 7 digitos
$codigo_cedente = b756_formata_numero($mD007['D007_Codigo_Cedente'],7,0);
$codigo_cedente_para_dv = b756_formata_numero($mD007['D007_Codigo_Cedente'],10,0);

$var_dv_nosso_numero = "$agencia$codigo_cedente_para_dv$nnum";

//dv do nosso número
$dv_nosso_numero = b756_digitoVerificador_nossonumero($var_dv_nosso_numero);

$cadastrarNnumDV = $nnum.$dv_nosso_numero;

//Número de parcelas - Deixamos como parcela 1 fixo. Não existe um campo parcela na tabela T117 do boleto e um boleto pode ter mais de um T002.
$numParcelas = b756_formata_numero(1,3,0);

// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = b756_digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$tpCarteira$agencia"."01"."$codigo_cedente$cadastrarNnumDV$numParcelas");

// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$tpCarteira$agencia"."01"."$codigo_cedente$cadastrarNnumDV$numParcelas";
//				<03>	<1>		<1> 	<04>		<10>	<01>	  <04>     <2>		<7>			<08>	  <03>
//				<0-2> 	<3-3> 	<4-4>	<5-8>		<9-18>  <19-19>   <20-23> <24-25>   <26-32>     <33-40>  <40-43>  

$nossonumero = substr($nnum,1).'-'.$dv_nosso_numero;
$agencia_codigo = $agencia."/". $codigo_cedente;

$codigo_barras = $linha;
$linha_digitavel = b756_monta_linha_digitavel($linha);

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
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
