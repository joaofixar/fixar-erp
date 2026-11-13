<?php
$codigobanco = "001";
$codigo_banco_com_dv = b001_geraCodigoBanco($codigobanco);

//agencia ? sempre 4 digitos
$agencia = b001_formata_numero($this->numero_agencia,4,0);
//conta ? sempre 8 digitos
$conta = b001_formata_numero($this->numero_conta,8,0);
//agencia e conta
$agencia_codigo = $agencia."-". b001_modulo_11($agencia) ." / ". $conta ."-". b001_modulo_11($conta);

$mensagemLocalPagamento = "PAGÁVEL EM QUALQUER BANCO ATE O VENCIMENTO.";

$nossonumero = $this->nosso_numero;
$linha_digitavel = $this->linha_digitavel;
$codigo_barras = $this->codigo_barras;
?>