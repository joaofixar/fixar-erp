<?php
namespace hardness;

require_once __DIR__ . '/../../../bibliotecas/layouts_remessa/104/beneficiario_104.php';

$codigobanco = "104";
$codigo_banco_com_dv = b104_geraCodigoBanco($codigobanco);
$nummoeda = "9";

try {
    $beneficiarioCaixa = b104_separar_beneficiario($this->codigo_cliente);
    $digitoBeneficiarioCaixa = $beneficiarioCaixa['digito'];
    if ($digitoBeneficiarioCaixa === null) {
        $digitoBeneficiarioCaixa = b104_digitoVerificador_cedente($beneficiarioCaixa['codigo']);
    }
    $codigoBeneficiarioCaixa = $beneficiarioCaixa['cnab'] . '-' . $digitoBeneficiarioCaixa;
} catch (\InvalidArgumentException $e) {
    $this->erro = $e->getMessage();
    return;
}

$dvCedente = b104_digitoVerificador_cedente($this->numero_convenio);

$mensagemLocalPagamento = "EM TODA REDE BANCÁRIA E SEUS CORRESPONDETES ATÉ O VALOR LIMITE";
$mensagemSacado = "SAC CAIXA: 0800 726 0101 (informações, reclamações, sugestões e elogios).<br> 
Para pessoas com deficiência auditiva ou de fala: 0800 726 2492.<br> 
Ouvidoria: 0800 725 7474.<br>caixa.gov.br";

$nossonumero = $this->nosso_numero;
$linha_digitavel = $this->linha_digitavel;
$codigo_barras = $this->codigo_barras;
?>