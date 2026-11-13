<?php
namespace hardness;

// ======================================================================
// DEPENDÊNCIAS (IGUAL AO 341)
// ======================================================================
require_once('../../old/lib/funcoes.php'); // OBRIGATÓRIO (layout legado)
require_once("./include/funcoes_336.php");

// ======================================================================
// 1) LEITURA DOS DADOS
// ======================================================================
$codigobanco = "336";
$nummoeda    = "9";

// Corrige: no 341 usa $this->data_vencimento, aqui usa $mT117
$fator_vencimento = $mT117['T117_Fator_Vencimento'];
if (empty($fator_vencimento) && !empty($this->data_vencimento)) {
    $fator_vencimento = b336_fator_vencimento($this->data_vencimento);
}

$valor_titulo     = (float) $mT117['T117_Valor_Titulo'];

// Usando função de formatação igual ao 341
$valor   = b336_formata_numero($valor_titulo, 10, 0, "valor");
$agencia = b336_formata_numero($mD007['D007_Agencia'], 4, 0);
$conta   = b336_formata_numero($mD007['D007_Conta'], 5, 0);
$carteira = $mD007['D007_Tipo_Carteira_Banco_Brasil'];

// ======================================================================
// 2) CÁLCULOS BANCÁRIOS (C6)
// ======================================================================
$codigo_banco_com_dv = b336_geraCodigoBanco($codigobanco);

// Nosso número
$nosso_numero_base = b336_formata_numero($mT117['T117_Nosso_Numero'], 10, 0);
$dvNossoNumero     = b336_dvNossoNumero($nosso_numero_base);
$nosso_numero      = $nosso_numero_base . $dvNossoNumero;

// Campo livre C6
$campoLivre = $carteira . $nosso_numero . $agencia . $conta . '000';

// Código de barras (44)
$codigo_barras = b336_geraCodigoBarrasCompleto(
    $fator_vencimento,
    str_replace(['.', ','], '', $valor_titulo),
    $campoLivre
);

// Linha digitável
$linha_digitavel = b336_geraLinhaDigitavel($codigo_barras);

// Campos visuais
$nosso_numero_visual = $carteira . '/' . substr($nosso_numero_base, 0, -1) . '-' . $dvNossoNumero;
$agencia_dv          = b336_modulo10($agencia . $conta);
$agencia_codigo      = $agencia . "/" . $conta . "-" . $agencia_dv;

// ======================================================================
// 3) ATUALIZA BANCO DE DADOS (IGUAL AO 341)
// ======================================================================
mysqli_query("update T117 set T117_Codigo_Barras='$codigo_barras' where T117_Id='$nT117_Id'");

// ======================================================================
// 4) ARRAY $dadosboleto (FONTE ÚNICA)
// ======================================================================
$dadosboleto = [
    "codigo_barras"       => $codigo_barras,
    "linha_digitavel"     => $linha_digitavel,
    "codigo_banco_com_dv" => $codigo_banco_com_dv,
    "agencia_codigo"      => $agencia_codigo,
    "nosso_numero"        => $nosso_numero_visual,
    "numero_documento"    => $mT117['T117_Numero_Documento'],
    "numero"              => $mT117['T117_Numero_Documento'],
    "carteira"            => $carteira,
    "valor_titulo"        => $valor_titulo,
    "data_vencimento"     => $mT117['T117_Data_Vencimento'],
    "mensagem_local_pagamento" =>
        "Até o vencimento, preferencialmente no Banco C6. Após o vencimento, cobrar juros e multa conforme instruções.",
    "instrucoes" => !empty($mT117['T117_Instrucoes']) ? $mT117['T117_Instrucoes'] : "Após o vencimento cobrar juros.\nMulta de 2% após o vencimento."
];

// ======================================================================
// 5) POPULAR O OBJETO ($this) - COMPLETO
// ======================================================================
$this->codigo_banco        = $codigobanco;
$this->codigo_banco_com_dv = $codigo_banco_com_dv;

$this->linha_digitavel = $linha_digitavel;
$this->codigo_barras   = $codigo_barras;

$this->dados_especie    = 'R$';
$this->dados_quantidade = '1';
$this->numero_carteira  = $carteira;

$this->valor_duplicata = $valor_titulo;

$this->numero_agencia             = $agencia;
$this->digito_verificador_agencia = substr($agencia_codigo, -1);

$this->numero_conta               = $conta;
$this->digito_verificador_conta   = $agencia_dv;

// Dados adicionais necessários para o layout
$this->numero_documento     = $mT117['T117_Numero_Documento'];
$this->numero_contrato      = !empty($mT117['T117_Numero_Contrato']) ? $mT117['T117_Numero_Contrato'] : '';
$this->dados_especie_doc    = 'R$';
$this->dados_aceite         = !empty($mD007['D007_Aceite']) ? $mD007['D007_Aceite'] : 'N';
$this->data_processamento   = date('d/m/Y');
$this->dados_valor_unitario = number_format($valor_titulo, 2, ',', '.');

// Instruções (separadas em até 4 linhas)
$instrucoesArray = explode("\n", $dadosboleto["instrucoes"]);
$this->instrucoes1 = isset($instrucoesArray[0]) ? $instrucoesArray[0] : '';
$this->instrucoes2 = isset($instrucoesArray[1]) ? $instrucoesArray[1] : '';
$this->instrucoes3 = isset($instrucoesArray[2]) ? $instrucoesArray[2] : '';
$this->instrucoes4 = isset($instrucoesArray[3]) ? $instrucoesArray[3] : '';

// Dados do cedente
$this->nome_cedente      = !empty($mD007['D007_Nome']) ? $mD007['D007_Nome'] : '';
$this->cpf_cnpj_cedente  = !empty($mD007['D007_CNPJ']) ? $mD007['D007_CNPJ'] : '';
$this->endereco_cedente  = !empty($mD007['D007_Endereco']) ? $mD007['D007_Endereco'] : '';
$this->cidade_cedente    = !empty($mD007['D007_Cidade']) ? $mD007['D007_Cidade'] : '';
$this->estado_cedente    = !empty($mD007['D007_UF']) ? $mD007['D007_UF'] : '';
$this->cep_cedente       = !empty($mD007['D007_CEP']) ? $mD007['D007_CEP'] : '';

// Dados do sacado (vêm do cliente)
$sqlSacado = mysqli_query("SELECT * FROM D024 WHERE D024_Id = '{$mT117['T117_D024_Id']}'");
if ($sacado = mysqli_fetch_assoc($sqlSacado)) {
    $this->nome_sacado      = !empty($sacado['D024_Nome_Empresa']) ? $sacado['D024_Nome_Empresa'] : '';
    $this->cpf_cnpj_sacado  = !empty($sacado['D024_CNPJ']) ? $sacado['D024_CNPJ'] : (!empty($sacado['D024_CPF']) ? $sacado['D024_CPF'] : '');
    $this->endereco_sacado  = !empty($sacado['D024_Endereco']) ? $sacado['D024_Endereco'] : '';
    
    // Funções para complemento de endereço (se existirem)
    $this->getEndereco2Cedente = function() { 
        return !empty($mD007['D007_Complemento']) ? $mD007['D007_Complemento'] : ''; 
    };
    $this->getEndereco2Sacado = function() use ($sacado) { 
        return !empty($sacado['D024_Complemento']) ? $sacado['D024_Complemento'] : ''; 
    };
}

// Data de emissão do boleto
$this->getDataEmissaoBoleto = function() use ($mT117) { 
    return !empty($mT117['T117_Data_Emissao']) ? date('d/m/Y', strtotime($mT117['T117_Data_Emissao'])) : date('d/m/Y'); 
};

// ======================================================================
// 6) VARIÁVEIS PARA O LAYOUT (IGUAL AO 341)
// ======================================================================
$codigo_banco_com_dv = $dadosboleto["codigo_banco_com_dv"];
$linha_digitavel = $dadosboleto["linha_digitavel"];
$codigo_barras = $dadosboleto["codigo_barras"];
$nossonumero = $dadosboleto["nosso_numero"]; // O layout espera $nossonumero (sem underscore)
$linha = $codigo_barras; // Para o fbarcode
$mensagemLocalPagamento = $dadosboleto["mensagem_local_pagamento"];
$mensagemSacado = ''; // Se houver mensagem específica para o sacado

$dadosBeneficiario =
    $this->nome_cedente . " " . $this->cpf_cnpj_cedente . " <br /> " .
    $this->endereco_cedente . " <br /> " .
    $this->cidade_cedente . " / " . $this->estado_cedente . " - " .
    $this->cep_cedente;

// ======================================================================
// 7) VERIFICAÇÃO DE ERROS
// ======================================================================
if (empty($codigo_barras) || empty($linha_digitavel)) {
    $this->setErro("Erro na geração do código de barras ou linha digitável.");
}

?>