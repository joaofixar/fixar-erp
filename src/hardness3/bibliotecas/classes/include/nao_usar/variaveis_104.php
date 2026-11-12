<?php
$codigobanco = "104";
$codigo_banco_com_dv = b104_geraCodigoBanco($codigobanco);
$nummoeda = "9";

$dvCedente = b104_digitoVerificador_cedente($this->numero_convenio);

$mensagemLocalPagamento = "PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE";

$nossonumero = $this->nosso_numero;
$linha_digitavel = $this->linha_digitavel;
$codigo_barras = $this->codigo_barras;
?>