<?php
namespace hardness;

$codigobanco = "748";
$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
$mensagemLocalPagamento = "PAGÁVEL PREFERENCIALMENTE NAS COOPERATIVAS DE CRÉDITO DO SICREDI";

$nossonumero = $this->nosso_numero;
$linha_digitavel = $this->linha_digitavel;
$codigo_barras = $this->codigo_barras;
?>