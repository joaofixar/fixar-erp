<?php
namespace hardness;

// ======================================================================
// VARIÁVEIS PARA O BOLETO C6 (BANCO 336)
// ======================================================================

// Dados básicos
$codigobanco = "336";
$codigo_banco_com_dv = b336_geraCodigoBanco($codigobanco);
$nummoeda = "9";

// Fator de vencimento
if (!empty($this->data_vencimento)) {
    $fator_vencimento = b336_fator_vencimento($this->data_vencimento);
} elseif (!empty($mT117['T117_Data_Vencimento'])) {
    $fator_vencimento = b336_fator_vencimento($mT117['T117_Data_Vencimento']);
} else {
    $fator_vencimento = "0000";
}

// Valor do título
$valor_titulo = !empty($this->valor_duplicata) ? $this->valor_duplicata : 
               (!empty($mT117['T117_Valor_Titulo']) ? $mT117['T117_Valor_Titulo'] : 0);
$valor = b336_formata_numero($valor_titulo, 10, 0, "valor");

// Agência e conta
$agencia = b336_formata_numero(!empty($this->numero_agencia) ? $this->numero_agencia : 
                              (!empty($mD007['D007_Agencia']) ? $mD007['D007_Agencia'] : ''), 4, 0);
$conta = b336_formata_numero(!empty($this->numero_conta) ? $this->numero_conta : 
                            (!empty($mD007['D007_Conta']) ? $mD007['D007_Conta'] : ''), 5, 0);

// Carteira
$carteira = !empty($this->numero_carteira) ? $this->numero_carteira : 
           (!empty($mD007['D007_Tipo_Carteira_Banco_Brasil']) ? $mD007['D007_Tipo_Carteira_Banco_Brasil'] : '');

// Nosso número
$nosso_numero_base = b336_formata_numero(!empty($this->nosso_numero) ? $this->nosso_numero : 
                                        (!empty($mT117['T117_Nosso_Numero']) ? $mT117['T117_Nosso_Numero'] : ''), 10, 0);
$dvNossoNumero = b336_dvNossoNumero($nosso_numero_base);
$nosso_numero = $nosso_numero_base . $dvNossoNumero;

// Campo livre C6
$campoLivre = $carteira . $nosso_numero . $agencia . $conta . '000';

// Código de barras base (43 dígitos)
$codigo_barras_base = '336' . '9' . str_pad($fator_vencimento, 4, '0', STR_PAD_LEFT) . 
                      $valor . $campoLivre;

// Digito verificador do código de barras
$dv = b336_calculaDvCodigoBarras($codigo_barras_base);

// Código de barras completo (44 dígitos)
$codigo_barras = substr($codigo_barras_base, 0, 4) . $dv . substr($codigo_barras_base, 4);

// Atualiza banco de dados (se tiver ID do boleto)
if (!empty($nT117_Id)) {
    mysqli_query("update T117 set T117_Codigo_Barras='$codigo_barras' where T117_Id='$nT117_Id'");
}

// Linha digitável
$linha_digitavel = b336_geraLinhaDigitavel($codigo_barras);

// Formatação para exibição
$nossonumero = $carteira . '/' . substr($nosso_numero_base, 0, -1) . '-' . $dvNossoNumero;
$agencia_dv = b336_modulo10($agencia . $conta);
$agencia_codigo = $agencia . "/" . $conta . "-" . $agencia_dv;

// Array com dados do boleto
$dadosboleto["codigo_barras"] = $codigo_barras;
$dadosboleto["linha_digitavel"] = $linha_digitavel;
$dadosboleto["agencia_codigo"] = $agencia_codigo;
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

// Dados do beneficiário
$nome_cedente = !empty($this->nome_cedente) ? $this->nome_cedente : 
               (!empty($mD007['D007_Nome']) ? $mD007['D007_Nome'] : '');
$cpf_cnpj_cedente = !empty($this->cpf_cnpj_cedente) ? $this->cpf_cnpj_cedente : 
                   (!empty($mD007['D007_CNPJ']) ? $mD007['D007_CNPJ'] : '');
$endereco_cedente = !empty($this->endereco_cedente) ? $this->endereco_cedente : 
                   (!empty($mD007['D007_Endereco']) ? $mD007['D007_Endereco'] : '');
$cidade_cedente = !empty($this->cidade_cedente) ? $this->cidade_cedente : 
                 (!empty($mD007['D007_Cidade']) ? $mD007['D007_Cidade'] : '');
$estado_cedente = !empty($this->estado_cedente) ? $this->estado_cedente : 
                 (!empty($mD007['D007_UF']) ? $mD007['D007_UF'] : '');
$cep_cedente = !empty($this->cep_cedente) ? $this->cep_cedente : 
              (!empty($mD007['D007_CEP']) ? $mD007['D007_CEP'] : '');

$dadosBeneficiario = $nome_cedente . " " . $cpf_cnpj_cedente . " <br /> " . 
                     $endereco_cedente . " <br /> " . 
                     $cidade_cedente . " / " . $estado_cedente . " - " . $cep_cedente;

// Mensagens padrão
$mensagemLocalPagamento = "Até o vencimento, preferencialmente no Banco C6. Após o vencimento, cobrar juros e multa conforme instruções.";
$instrucoesPadrao = array(
    "Não receber após 30 dias do vencimento.",
    "Após o vencimento, cobrar juros de mora de 1% ao mês.",
    "Multa de 2% após o vencimento."
);

// Variáveis para o layout
$linha_digitavel = $dadosboleto["linha_digitavel"]; 
$codigo_barras = $dadosboleto["codigo_barras"];
$linha = $codigo_barras; // Para fbarcode

// Mensagem para o sacado (se houver)
$mensagemSacado = "";

// ======================================================================
// SE NÃO HOUVER CÁLCULOS PRÉVIOS, DEFINE AS INSTRUÇÕES PADRÃO
// ======================================================================
if (empty($this->instrucoes1) && !empty($instrucoesPadrao[0])) {
    $this->instrucoes1 = $instrucoesPadrao[0];
}
if (empty($this->instrucoes2) && !empty($instrucoesPadrao[1])) {
    $this->instrucoes2 = $instrucoesPadrao[1];
}
if (empty($this->instrucoes3) && !empty($instrucoesPadrao[2])) {
    $this->instrucoes3 = $instrucoesPadrao[2];
}

// ======================================================================
// DEFINE PROPRIEDADES DO OBJETO SE NÃO EXISTIREM
// ======================================================================
if (!isset($this->dados_especie)) {
    $this->dados_especie = 'R$';
}
if (!isset($this->dados_quantidade)) {
    $this->dados_quantidade = '1';
}
if (!isset($this->dados_especie_doc)) {
    $this->dados_especie_doc = 'R$';
}
if (!isset($this->dados_aceite)) {
    $this->dados_aceite = 'N';
}
if (!isset($this->data_processamento)) {
    $this->data_processamento = date('d/m/Y');
}
if (!isset($this->getDataEmissaoBoleto)) {
    $this->getDataEmissaoBoleto = function() { 
        return date('d/m/Y'); 
    };
}
?>