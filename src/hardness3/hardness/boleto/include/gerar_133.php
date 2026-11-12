<?php
namespace hardness;

require_once('../../old/lib/funcoes.php');
require_once("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");

$codigobanco = "133";
$codigo_banco_com_dv = b133_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = b133_fator_vencimento($T117_Data_Vencimento);

$valorTituloInfo = b133_resolve_valor_titulo($nValor_Total, isset($nT117_Id) ? $nT117_Id : null);
if (!empty($valorTituloInfo['aviso'])) {
    log($valorTituloInfo['aviso']);
}
$valorTitulo = $valorTituloInfo['valor'];
$valor = b133_formata_numero(number_format($valorTitulo, 2, '.', ''), 10, 0, "valor");
$agencia = b133_formata_numero($mD007['D007_Numero_Agencia'], 4, 0);
$agencia_dv = b133_formata_numero($mD007['D007_Digito_Verificador_Agencia'], 1, 0);
$contaBase = !empty($mD007['D007_Codigo_Cedente']) ? $mD007['D007_Codigo_Cedente'] : $mD007['D007_Numero_Conta'];
$conta = b133_formata_numero($contaBase, 7, 0);
$conta_dv = b133_formata_numero($mD007['D007_Digito_Verificador_Conta'], 1, 0);
$carteira = b133_formata_numero($mD007['D007_Tipo_Carteira_Banco_Brasil'], 2, 0);

$nossoNumeroInfo = b133_nosso_numero_info($mD007['D007_Nosso_Numero'], $carteira);
if (!empty($nossoNumeroInfo['erro'])) {
    return 'ERRO: ' . $nossoNumeroInfo['erro'];
}
$nosso_numero_base = $nossoNumeroInfo['base'];
$dv_nosso_numero = $nossoNumeroInfo['dv'];
$especieTitulo = b133_resolve_especie_titulo(
    isset($mD007['T117_Especie_Documento']) && trim((string)$mD007['T117_Especie_Documento']) !== ''
        ? $mD007['T117_Especie_Documento']
        : (isset($mD007['D007_Especie_Documento']) ? $mD007['D007_Especie_Documento'] : ''),
    isset($mD007['T117_Especie']) && trim((string)$mD007['T117_Especie']) !== ''
        ? $mD007['T117_Especie']
        : (isset($mD007['D007_Especie']) ? $mD007['D007_Especie'] : '')
);

$campo_livre = $agencia . $carteira . $nosso_numero_base . $conta . '0';
$codigo_barras_sem_dv = $codigobanco . $nummoeda . $fator_vencimento . $valor . $campo_livre;
$dv = b133_digitoVerificador_barra($codigo_barras_sem_dv);
$linha = substr($codigo_barras_sem_dv, 0, 4) . $dv . substr($codigo_barras_sem_dv, 4, 43);

$nossonumero = $carteira . '/' . $nosso_numero_base . '-' . $dv_nosso_numero;
$agencia_codigo = $agencia . "-" . $agencia_dv . " / " . $conta . "-" . $conta_dv;

$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = b133_monta_linha_digitavel($linha);
$dadosboleto["agencia_codigo"] = $agencia_codigo;
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

$dadosBeneficiario = $this->nome_cedente . " " . $this->cpf_cnpj_cedente . " <br /> " . $this->endereco_cedente . " <br /> " . $this->cidade_cedente . " / " . $this->estado_cedente . " - " . $this->cep_cedente;
$mensagemLocalPagamento = "Pagar preferencialmente na Rede Cresol";
$linha_digitavel = $dadosboleto["linha_digitavel"];
$codigo_barras = $dadosboleto["codigo_barras"];

$cClausula  = " update T117 set ";
$cClausula .= " T117_Nosso_Numero='$nossonumero', ";
$cClausula .= " T117_Especie_Documento='{$especieTitulo['sigla_boleto']}', ";
$cClausula .= " T117_Linha_Digitavel='".$linha_digitavel."', ";
$cClausula .= " T117_Codigo_Barras='".$codigo_barras."' ";
$cClausula .= " where T117_Id='$nT117_Id' ";
mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro)) {
    mysql_query("ROLLBACK");
    return "ERRO: $cErro/p/pClausula: $cClausula";
}

?>
