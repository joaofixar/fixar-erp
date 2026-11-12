<?php
namespace hardness;
$codigobanco = "033";
$codigo_banco_com_dv = b033_geraCodigoBanco($codigobanco);

//agencia ĝś sempre 4 digitos
$agencia = b033_formata_numero($this->numero_agencia,4,0);
//conta ĝś sempre 8 digitos
$conta = b033_formata_numero($this->numero_conta,8,0);
//agencia e conta
$agencia_codigo = $agencia."-". b033_modulo_11($agencia) ." / ". $conta ."-". b033_modulo_11($conta);

$mensagemLocalPagamento = "Pagável em qualquer banco até o vencimento";

$nossonumero = $this->nosso_numero;
$linha_digitavel = $this->linha_digitavel;
$codigo_barras = $this->codigo_barras;
?>