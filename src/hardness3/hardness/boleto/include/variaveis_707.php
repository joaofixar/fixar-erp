<?php
namespace hardness;

$codigobanco = "707";
$codigo_banco_com_dv = b707_geraCodigoBanco($codigobanco);
$nummoeda = "9";
//$dv_banco = "7";

$valor = $this->valor_duplicata;
$agencia = $this->numero_agencia;
$conta = $this->numero_conta;
$carteira = $this->numero_carteira;
$nnum = substr($this->nosso_numero,0,-1);
//$tipo_cobranca = "2";
$fator_vencimento = b707_fator_vencimento($this->data_vencimento);

$mensagemLocalPagamento = "PAGAVEL EM QUALQUER AGÊNCIA BANCÁRIA, MESMO APÓS VENCIMENTO";

$nossonumero = $carteira."/".$nnum."-".b707_modulo_10($agencia.$carteira.$nnum);
$linha_digitavel = $this->linha_digitavel;
$codigo_barras = $this->codigo_barras;

?>




