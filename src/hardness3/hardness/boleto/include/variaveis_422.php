<?php
namespace hardness;

$codigobanco = "422";
$codigo_banco_com_dv = b422_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$dv_banco = "7";

$valor = $this->valor_duplicata;
$agencia = $this->numero_agencia;
$conta = $this->numero_conta;
$nnum = $this->nosso_numero;
$tipo_cobranca = "2";
$fator_vencimento = b422_fator_vencimento($this->data_vencimento);

$mensagemLocalPagamento = "Pagável em qualquer Banco do Sistema de Compensação";

$nossonumero = $this->nosso_numero;
$linha_digitavel = $this->linha_digitavel;
$codigo_barras = $this->codigo_barras;

?>




