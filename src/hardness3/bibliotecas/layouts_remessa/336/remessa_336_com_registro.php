<?php
/*
Programador: Marcelo Dias
Data: 29/09/2025
Ajustado para layout C6 CNAB400 conforme especificação oficial
*/
namespace hardness;

DEFINE("_SALTO_LINHA", CHR(13).CHR(10));

// ======================================================================
// 1) VALIDAÇÃO DOS DADOS DA CONTA
// ======================================================================
$cClausula  = " SELECT * FROM D007 ";
$cClausula .= " LEFT JOIN C004 ON C004_Id=D007_C004_Id ";
$cClausula .= " WHERE D007_Id='$D007_Id' ";
$D007 = mysql_query($cClausula);
$mD007 = mysql_fetch_array($D007);

if (mysql_error()) {
    return mysql_error();
}

// Validações específicas para C6
if (strlen($mD007['D007_Numero_Agencia']) <= 0) {
    return "Não informado o número da agência no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Agencia']) > 4) {
    return "Número da agência deve ter no máximo 4 caracteres";
}
if (strlen($mD007['D007_Numero_Conta']) <= 0) {
    return "Não informado o número da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta']) > 12) {
    return "Número da conta deve ter no máximo 12 caracteres";
}
if (strlen($mD007['D007_Digito_Verificador_Conta']) <= 0) {
    return "Não informado o dígito verificador da conta no cadastro de contas";
}
if (strlen($mD007['D007_Digito_Verificador_Conta']) > 2) {
    return "Dígito verificador da conta deve ter no máximo 2 caracteres";
}

// ======================================================================
// 2) CONSULTA DOS TÍTULOS
// ======================================================================
$cClausula  = " SELECT ";
$cClausula .= " *, ";
$cClausula .= " IF(T117_Id IS NULL, T002_Id, T117_Id) AS T117_Id, ";
$cClausula .= " IF(T117_Data_Vencimento IS NULL, T002_Data_Vencimento, T117_Data_Vencimento) AS T117_Data_Vencimento, ";
$cClausula .= " IF(T117_Data_Emissao IS NULL, T002_Data_Emissao, T117_Data_Emissao) AS T117_Data_Emissao, ";
$cClausula .= " IF(T117_Valor_Total IS NULL, T002_Valor_Total, T117_Valor_Total) AS T117_Valor_Total, ";
$cClausula .= " IF(T117_Nosso_Numero IS NULL, T002_Numero_Duplicata, T117_Nosso_Numero) AS T117_Nosso_Numero ";
$cClausula .= " FROM T002 ";
$cClausula .= " LEFT JOIN T117 ON T117_Id=T002_T117_Id ";
$cClausula .= " LEFT JOIN D024 ON D024_Id=T002_D024_Id ";
$cClausula .= " LEFT JOIN D020 ON D020_Id=D024_D020_Id ";
$cClausula .= " LEFT JOIN D018 ON D018_Id=D020_D018_Id ";
$cClausula .= " LEFT JOIN C004 ON C004_Id=T002_C004_Id ";
$cClausula .= " LEFT JOIN D027 ON D027_Id=T002_D027_Id ";
$cClausula .= " WHERE $where ";
$cClausula .= " GROUP BY IFNULL(T117_Id, T002_Id)";

log("Sql remessa 336: ".$cClausula);

$T117_validacao = mysql_query($cClausula);
if (mysql_error()) {
    return mysql_error();
}

if (mysql_num_rows($T117_validacao) == 0) {
    return "Nenhum título foi selecionado para esta conta";
}
$numRegistros = mysql_num_rows($T117_validacao);

// Verifica boletos com nosso número repetido
$cClausula_Nosso_Numero = <<<EOT
    SELECT COUNT(*), T117_Nosso_Numero
    FROM T002 
    LEFT JOIN T117 ON T117_Id=T002_T117_Id 
    WHERE $where
    GROUP BY T117_Nosso_Numero
    HAVING COUNT(*) > 1
EOT;

$cClausula_Nosso_Numero = mysql_query($cClausula_Nosso_Numero);
$msg_boletos_duplicados = '';
if (mysql_num_rows($cClausula_Nosso_Numero) > 0) {
    while ($mSql = mysql_fetch_array($cClausula_Nosso_Numero)) {
        $msg_boletos_duplicados .= $mSql['T117_Nosso_Numero'] . ", ";
    }
    $msg_boletos_duplicados = trim($msg_boletos_duplicados, ', ');
}

if ($msg_boletos_duplicados != '') {
    return "Os boletos com o 'nosso número' abaixo estão duplicados, favor verificar: <br /> " . $msg_boletos_duplicados;
}

// ======================================================================
// 3) VALIDAÇÃO DOS DADOS DOS CLIENTES
// ======================================================================
$mErroCadastro = array();
while ($mT117 = mysql_fetch_array($T117_validacao)) {
    $mT117['D024_Cpf']  = trim($mT117['D024_Cpf']);
    $mT117['D024_Cpf']  = str_replace(array(".", "-", "/", " "), "", $mT117['D024_Cpf']);
    
    $mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
    $mT117['D024_Cnpj'] = str_replace(array(".", "-", "/", " "), "", $mT117['D024_Cnpj']);
    
    $mT117['D024_Cep']  = trim($mT117['D024_Cep']);
    $mT117['D024_Cep']  = str_replace(array(".", "-", " "), "", $mT117['D024_Cep']);
    
    if (empty($mT117['D024_Cpf']) && $mT117['D024_Flag_Pessoa_Fisica_Juridica'] == 'F') { 
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem CPF. Remessa não gerada!"; 
    }
    
    if (empty($mT117['D024_Cnpj']) && $mT117['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') { 
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem CNPJ. Remessa não gerada!"; 
    }
    
    if (empty($mT117['D024_Endereco'])) { 
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem Endereço. Remessa não gerada!"; 
    }

    if (empty($mT117['D024_Bairro'])) { 
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem Bairro. Remessa não gerada!"; 
    }

    if (empty($mT117['D024_Cep'])) { 
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem CEP. Remessa não gerada!"; 
    }
        
    if (empty($mT117['D020_Nome_Cidade'])) { 
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem Cidade. Remessa não gerada!"; 
    }
}

if (count($mErroCadastro) > 0) {
    return implode("\n", $mErroCadastro);
}

// ======================================================================
// 4) INÍCIO DA TRANSAÇÃO E REGISTRO DO ARQUIVO
// ======================================================================
mysql_query("SET autocommit=0");
mysql_query("BEGIN");

// Gera número sequencial para o arquivo
$cClausula = "SELECT * FROM T035 WHERE T035_Data_Processamento >= '" . date("Y-m-d") . " 00:00:00' AND T035_Data_Processamento <= '" . date("Y-m-d") . " 23:59:59'";
$cT035 = mysql_query($cClausula);
$numReg = mysql_num_rows($cT035) + 1;
$nomeArquivo = str_pad($numReg, 8, '0', STR_PAD_LEFT);

// Insere registro do arquivo de remessa
$cClausula  = " INSERT INTO T035 ( ";
$cClausula .= " T035_D007_Id, ";
$cClausula .= " T035_C004_Id, ";
$cClausula .= " T035_D062_Id, ";
$cClausula .= " T035_Data_Processamento, ";
$cClausula .= " T035_Nome_Arquivo, ";
$cClausula .= " T035_Numero_Titulos ";
$cClausula .= " ) VALUES ( ";
$cClausula .= " '$D007_Id', ";
$cClausula .= " '{$mD007['D007_C004_Id']}', ";
$cClausula .= " '{$mD007['D007_D062_Id']}', ";
$cClausula .= " NOW(), ";
$cClausula .= " '{$nomeArquivo}', ";
$cClausula .= " '{$numRegistros}' ";
$cClausula .= " ) ";

$cT035 = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {    
    mysql_query("ROLLBACK");
    return "Ocorreu um erro: $cErro\n\n$cClausula";
}

$nT035_Id = $g['mysqlLastId'];

// ======================================================================
// 5) GERAÇÃO DO ARQUIVO DE REMESSA - HEADER (REGISTRO 0)
// ======================================================================
$cHoje = date("dmy");
$nSeq = 1;
$cString = "";

// HEADER - Registro 0 (posições 1-400)
$cString .= prepararCampo("Tipo de Registro", "Num", 1, 0, "0"); // 1-1
$cString .= prepararCampo("Código de Remessa", "Num", 1, 0, "1"); // 2-2
$cString .= prepararCampo("Literal Remessa", "Char", 7, 0, "REMESSA"); // 3-9
$cString .= prepararCampo("Código do Serviço", "Num", 2, 0, "01"); // 10-11
$cString .= prepararCampo("Literal do Serviço", "Char", 8, 0, "COBRANCA"); // 12-19
$cString .= prepararCampo("Uso do Banco", "Char", 7, 0, ""); // 20-26

// Código do Beneficiário (12 posições)
$codigoBeneficiarioBase = '';
if (!empty($mD007['D007_Codigo_Beneficiario'])) {
    $codigoBeneficiarioBase = $mD007['D007_Codigo_Beneficiario'];
} elseif (!empty($mD007['D007_Codigo_Cedente'])) {
    $codigoBeneficiarioBase = $mD007['D007_Codigo_Cedente'];
}

if (strlen(trim($codigoBeneficiarioBase)) <= 0) {
    return "Não informado o código do beneficiário/cedente no cadastro de contas";
}

$codigo_beneficiario = str_pad($codigoBeneficiarioBase, 12, "0", STR_PAD_LEFT);
$cString .= prepararCampo("Código do Beneficiário", "Num", 12, 0, $codigo_beneficiario); // 27-38

$cString .= prepararCampo("Uso do Banco", "Char", 8, 0, ""); // 39-46

// Nome do Beneficiário (30 posições)
$nome_beneficiario = substr($mD007['D007_Nome_Conta'], 0, 30);
$nome_beneficiario = gLimpaAcentos($nome_beneficiario);
$cString .= prepararCampo("Nome do Beneficiário", "Char", 30, 0, $nome_beneficiario); // 47-76

$cString .= prepararCampo("Código do Banco", "Num", 3, 0, "336"); // 77-79
$cString .= prepararCampo("Uso do Banco", "Char", 15, 0, ""); // 80-94
$cString .= prepararCampo("Data de Gravação", "Num", 6, 0, $cHoje); // 95-100
$cString .= prepararCampo("Uso do Banco", "Char", 8, 0, ""); // 101-108

// Conta Cobrança (12 posições)
$contaCobrancaBase = '';
if (!empty($mD007['D007_Conta_Cobrança'])) {
    $contaCobrancaBase = $mD007['D007_Conta_Cobrança'];
} elseif (!empty($mD007['D007_Numero_Conta'])) {
    $contaCobrancaBase = $mD007['D007_Numero_Conta'];
}

if (strlen(trim($contaCobrancaBase)) <= 0) {
    return "Não informado a conta cobrança no cadastro de contas";
}

$conta_cobranca = str_pad($contaCobrancaBase, 12, "0", STR_PAD_LEFT);
$cString .= prepararCampo("Conta Cobrança", "Num", 12, 0, $conta_cobranca); // 109-120

$cString .= prepararCampo("Uso do Banco", "Char", 266, 0, ""); // 121-386

// Sequencial da Remessa (8 posições)
$sequencial_remessa = str_pad($nomeArquivo, 8, "0", STR_PAD_LEFT);
$cString .= prepararCampo("Sequencial da Remessa", "Num", 8, 0, $sequencial_remessa); // 387-394

// Número Sequencial do Registro (6 posições)
$cString .= prepararCampo("Número Sequencial do Registro", "Num", 6, 0, str_pad($nSeq, 6, "0", STR_PAD_LEFT)); // 395-400

$cString .= _SALTO_LINHA;

// ======================================================================
// 6) DETALHES (REGISTRO 1)
// ======================================================================
$valorTotalRemessa = 0;
mysql_data_seek($T117_validacao, 0); // Reinicia o ponteiro do resultado

while ($mT117 = mysql_fetch_array($T117_validacao)) {
    $valorTotalRemessa += $mT117['T117_Valor_Total'];

    // Atualiza título com informação da remessa
    $cClausula  = " UPDATE T002 SET ";
    $cClausula .= " T002_Flag_Enviar_Banco='N', ";
    $cClausula .= " T002_T035_Id='$nT035_Id' ";
    $cClausula .= " WHERE T002_T117_Id='{$mT117['T117_Id']}' OR T002_Id='{$mT117['T002_Id']}'";
    mysqli_query($cClausula);
    
    $nSeq++;

    // Determina tipo de inscrição e CPF/CNPJ do pagador
    if ($mT117['D024_Flag_Pessoa_Fisica_Juridica'] == 'F') {
        $cCpfCnpjTipoPagador = '01';
        $cCpfCnpjPagador = str_replace(array(".", "-", "/", " "), "", trim($mT117['D024_Cpf']));
        $cCpfCnpjPagador = str_pad($cCpfCnpjPagador, 14, "0", STR_PAD_LEFT);
    } else {
        $cCpfCnpjTipoPagador = '02';
        $cCpfCnpjPagador = str_replace(array(".", "-", "/", " "), "", trim($mT117['D024_Cnpj']));
        $cCpfCnpjPagador = str_pad($cCpfCnpjPagador, 14, "0", STR_PAD_LEFT);
    }

    // Limpa dados para remessa
    $mT117['D024_Cep'] = str_replace(array(".", "-", " "), "", trim($mT117['D024_Cep']));
    $mT117['D024_Cep'] = str_pad($mT117['D024_Cep'], 8, "0", STR_PAD_LEFT);
    
    // Prepara dados do pagador
    $nomePagador = substr(gLimpaAcentos($mT117['D024_Nome_Empresa']), 0, 40);
    $enderecoPagador = substr(gLimpaAcentos($mT117['D024_Endereco']), 0, 40);
    $bairroPagador = substr(gLimpaAcentos($mT117['D024_Bairro']), 0, 12);
    $cidadePagador = substr(gLimpaAcentos($mT117['D020_Nome_Cidade']), 0, 15);
    $ufPagador = substr(trim($mT117['D018_UF']), 0, 2);
    
    // Nosso Número (10 dígitos + 1 DV = 11 posições)
    $nosso_numero_base = str_pad($mT117['T117_Nosso_Numero'], 10, "0", STR_PAD_LEFT);
    $dvNossoNumero = calcular_dv_nosso_numero_c6($nosso_numero_base);
    
    // CNPJ do Beneficiário (14 posições)
    $cnpjBeneficiario = str_replace(array(".", "-", "/", " "), "", $mD007['D007_CNPJ']);
    $cnpjBeneficiario = str_pad($cnpjBeneficiario, 14, "0", STR_PAD_LEFT);
    
    // DETALHE - Registro 1 (posições 1-400)
    $cString .= prepararCampo("Tipo de Registro", "Num", 1, 0, "1"); // 1-1
    $cString .= prepararCampo("Tipo de Inscrição", "Num", 2, 0, "02"); // 2-3 (sempre 02 - CNPJ do beneficiário)
    $cString .= prepararCampo("Identificação do Beneficiário", "Num", 14, 0, $cnpjBeneficiario); // 4-17
    $cString .= prepararCampo("Código do Beneficiário", "Num", 12, 0, $codigo_beneficiario); // 18-29
    $cString .= prepararCampo("Uso do Banco", "Char", 8, 0, ""); // 30-37
    
    // Uso Exclusivo - pode conter o ID do título (25 posições)
    $cString .= prepararCampo("Uso Exclusivo", "Char", 25, 0, str_pad($mT117['T117_Id'], 25, " ", STR_PAD_RIGHT)); // 38-62
    
    // Nosso Número (11 posições = 10 dígitos + 1 DV)
    $cString .= prepararCampo("Nosso Número", "Num", 11, 0, $nosso_numero_base . $dvNossoNumero); // 63-73
    
    // Dígito do Nosso Número (1 posição)
    $cString .= prepararCampo("Dígito do Nosso Número", "Num", 1, 0, $dvNossoNumero); // 74-74
    
    $cString .= prepararCampo("Uso do Banco", "Char", 8, 0, ""); // 75-82
    $cString .= prepararCampo("Código do Banco", "Num", 3, 0, "336"); // 83-85
    $cString .= prepararCampo("Uso do Banco", "Char", 21, 0, ""); // 86-106
    
    // Código da Carteira (10 ou 20)
    $carteira = $mD007['D007_Tipo_Carteira_Banco_Brasil'];
    $cString .= prepararCampo("Código da Carteira", "Num", 2, 0, str_pad($carteira, 2, "0", STR_PAD_LEFT)); // 107-108
    
    // Código de Ocorrência (01 = Entrada de título)
    $cString .= prepararCampo("Código de Ocorrência", "Num", 2, 0, "01"); // 109-110
    
    // Seu Número do Título (10 posições)
    $seuNumero = substr($mT117['T002_Numero_Duplicata'], 0, 10);
    $cString .= prepararCampo("Seu Número do Título", "Char", 10, 0, $seuNumero); // 111-120
    
    // Data de Vencimento (DDMMAA)
    $dataVencimento = !empty($mT117['T117_Data_Vencimento']) ? 
        date("dmy", strtotime($mT117['T117_Data_Vencimento'])) : "000000";
    $cString .= prepararCampo("Data de Vencimento", "Num", 6, 0, $dataVencimento); // 121-126
    
    // Valor do Título (13 posições: 11 inteiros + 2 decimais)
    $valorTitulo = number_format($mT117['T117_Valor_Total'], 2, '', '');
    $cString .= prepararCampo("Valor do Título", "Num", 13, 0, str_pad($valorTitulo, 13, "0", STR_PAD_LEFT)); // 127-139
    
    $cString .= prepararCampo("Uso do Banco", "Char", 8, 0, ""); // 140-147
    
    // Espécie do Título (01=DM, 02=DS, etc.)
    $especie = "01"; // Duplicata Mercantil
    $cString .= prepararCampo("Espécie do Título", "Num", 2, 0, $especie); // 148-149
    
    // Aceite (A=ACEITE, N=NÃO ACEITE)
    $aceite = (!empty($mT117['T117_Aceite']) && $mT117['T117_Aceite'] == 'S') ? 'A' : 'N';
    $cString .= prepararCampo("Aceite", "Char", 1, 0, $aceite); // 150-150
    
    // Data de Emissão (DDMMAA)
    $dataEmissao = !empty($mT117['T117_Data_Emissao']) ? 
        date("dmy", strtotime($mT117['T117_Data_Emissao'])) : $cHoje;
    $cString .= prepararCampo("Data de Emissão", "Num", 6, 0, $dataEmissao); // 151-156
    
    // Instrução 1 (zeros)
    $cString .= prepararCampo("Instrução 1", "Num", 2, 0, "00"); // 157-158
    
    // Instrução 2 (zeros)
    $cString .= prepararCampo("Instrução 2", "Num", 2, 0, "00"); // 159-160
    
    // Juros ao Dia (valor por dia de atraso)
    $jurosDia = !empty($mT117['T117_Percentual_Juros_Dia']) ? 
        number_format(($mT117['T117_Percentual_Juros_Dia']/30/100) * $mT117['T117_Valor_Total'], 2, '', '') : "0000000000000";
    $cString .= prepararCampo("Juros ao Dia", "Num", 13, 0, str_pad($jurosDia, 13, "0", STR_PAD_LEFT)); // 161-173
    
    // Data Desconto 1 (normalmente zeros)
    $cString .= prepararCampo("Data Desconto 1", "Num", 6, 0, "000000"); // 174-179
    
    // Valor Desconto 1 (normalmente zeros)
    $cString .= prepararCampo("Valor Desconto 1", "Num", 13, 0, "0000000000000"); // 180-192
    
    // Data da Multa (normalmente zeros ou igual ao vencimento)
    $dataMulta = "000000";
    $cString .= prepararCampo("Data da Multa", "Num", 6, 0, $dataMulta); // 193-198
    
    $cString .= prepararCampo("Uso do Banco", "Char", 7, 0, ""); // 199-205
    
    // Valor do Abatimento (normalmente zeros)
    $cString .= prepararCampo("Valor do Abatimento", "Num", 13, 0, "0000000000000"); // 206-218
    
    // Tipo do Pagador (01=CPF, 02=CNPJ)
    $cString .= prepararCampo("Tipo do Pagador", "Num", 2, 0, $cCpfCnpjTipoPagador); // 219-220
    
    // CPF/CNPJ do Pagador (14 posições)
    $cString .= prepararCampo("CPF/CNPJ do Pagador", "Num", 14, 0, $cCpfCnpjPagador); // 221-234
    
    // Nome do Pagador (40 posições)
    $cString .= prepararCampo("Nome do Pagador", "Char", 40, 0, $nomePagador); // 235-274
    
    // Endereço do Pagador (40 posições)
    $cString .= prepararCampo("Endereço do Pagador", "Char", 40, 0, $enderecoPagador); // 275-314
    
    // Bairro do Pagador (12 posições)
    $cString .= prepararCampo("Bairro do Pagador", "Char", 12, 0, $bairroPagador); // 315-326
    
    // CEP do Pagador (8 posições)
    $cString .= prepararCampo("CEP do Pagador", "Num", 8, 0, $mT117['D024_Cep']); // 327-334
    
    // Cidade do Pagador (15 posições)
    $cString .= prepararCampo("Cidade do Pagador", "Char", 15, 0, $cidadePagador); // 335-349
    
    // UF do Pagador (2 posições)
    $cString .= prepararCampo("UF do Pagador", "Char", 2, 0, $ufPagador); // 350-351
    
    // Beneficiário Final/Mensagem (30 posições)
    $mensagem = "";
    $cString .= prepararCampo("Beneficiário Final/Mensagem", "Char", 30, 0, $mensagem); // 352-381
    
    // Indicador de Multa (0=Sem multa, 2=Percentual)
    $indicadorMulta = (!empty($mT117['T117_Percentual_Multa']) && $mT117['T117_Percentual_Multa'] > 0) ? '2' : '0';
    $cString .= prepararCampo("Indicador de Multa", "Num", 1, 0, $indicadorMulta); // 382-382
    
    // Percentual de Multa (01-99)
    $percentualMulta = (!empty($mT117['T117_Percentual_Multa']) && $mT117['T117_Percentual_Multa'] > 0) ? 
        intval($mT117['T117_Percentual_Multa']) : 0;
    $cString .= prepararCampo("Percentual de Multa", "Num", 2, 0, str_pad($percentualMulta, 2, "0", STR_PAD_LEFT)); // 383-384
    
    $cString .= prepararCampo("Uso do Banco", "Char", 1, 0, ""); // 385-385
    
    // Data dos Juros (normalmente igual ao vencimento)
    $dataJuros = !empty($mT117['T117_Data_Vencimento']) ? 
        date("dmy", strtotime($mT117['T117_Data_Vencimento'])) : "000000";
    $cString .= prepararCampo("Data dos Juros", "Num", 6, 0, $dataJuros); // 386-391
    
    $cString .= prepararCampo("Uso do Banco", "Char", 2, 0, ""); // 392-393
    $cString .= prepararCampo("Uso do Banco", "Char", 1, 0, ""); // 394-394
    
    // Número Sequencial do Registro (6 posições)
    $cString .= prepararCampo("Número Sequencial do Registro", "Num", 6, 0, str_pad($nSeq, 6, "0", STR_PAD_LEFT)); // 395-400

    $cString .= _SALTO_LINHA;
}

// ======================================================================
// 7) TRAILER (REGISTRO 9)
// ======================================================================
$nSeq++;
$cString .= prepararCampo("Tipo de Registro", "Num", 1, 0, "9"); // 1-1
$cString .= prepararCampo("Uso do Banco", "Char", 393, 0, ""); // 2-394
$cString .= prepararCampo("Número Sequencial do Registro", "Num", 6, 0, str_pad($nSeq, 6, "0", STR_PAD_LEFT)); // 395-400

$cString .= _SALTO_LINHA;

// ======================================================================
// 8) SALVA O CONTEÚDO E FINALIZA
// ======================================================================
$cString = mysql_real_escape_string($cString);

$cClausula  = " UPDATE T035 SET ";
$cClausula .= " T035_Conteudo_Arquivo='$cString', ";
$cClausula .= " T035_Total_Remessa='$valorTotalRemessa' ";
$cClausula .= " WHERE T035_Id='$nT035_Id' ";

mysql_query($cClausula);
mysql_query("COMMIT");

return $nT035_Id;

// ======================================================================
// FUNÇÕES AUXILIARES
// ======================================================================

function prepararCampo($Campo, $Tipo = "Char", $Tamanho, $Decimais = 0, $String = "") {
    if (empty($Tipo)) {
        echo "Campo: $Campo Não foi definido tipo<br>";        
    }

    $cRetorno = "";
    $String = trim($String);

    if (strtoupper($Tipo) == "NUM") {
        if (empty($String)) {
            $String = 0;
        }
        
        if (strlen($String) > ($Tamanho - $Decimais)) {
            echo "Erro!! Campo: $Campo - Total de caracteres deste campo: $String (" . strlen($String) . ") maior que o tamanho do campo(" . ($Tamanho - $Decimais) . ")";
            exit;
        }
        
        if ($Decimais >= $Tamanho) {
            echo "Erro!! Campo: $Campo - Decimal ($Decimais) maior que o tamanho do campo($Tamanho)";
            exit;
        }
        
        if ($Decimais > 0) {
            $String = number_format($String, $Decimais, "", "");
        } else {
            if (($posicao = strpos($String, ".")) !== FALSE) {
                $String = substr($String, 0, $posicao);
            }
        }
        $cRetorno = STR_PAD($String, $Tamanho, "0", STR_PAD_LEFT);
    }
    
    if (strtoupper($Tipo) == "CHAR") {
        $String = substr($String, 0, $Tamanho);
        $cRetorno = STR_PAD($String, $Tamanho, " ", STR_PAD_RIGHT);
    }    
    
    if (strtoupper($Tipo) == "DATE") {
        if ($Tamanho == 6) {
            if (!empty($String) && $String != "000000") {
                list($ano, $mes, $dia) = explode("-", $String);
                $ano = substr($ano, -2);
                $String = "$dia$mes$ano";
            } else {
                $String = "000000";
            }
        }
        if ($Tamanho == 8) {
            if (!empty($String) && $String != "00000000") {
                list($ano, $mes, $dia) = explode("-", $String);
                $String = "$dia$mes$ano";
            } else {
                $String = "00000000";
            }
        }
        $cRetorno = STR_PAD($String, $Tamanho, "0", STR_PAD_LEFT);
    }        
    
    return $cRetorno;
}

function calcular_dv_nosso_numero_c6($numeroBase) {
    // Implementação do cálculo do DV do nosso número para C6
    // Baseado na função b336_dvNossoNumero do funcoes_336.php
    
    $soma = 0;
    $peso = 2;
    
    // Percorre o número da direita para a esquerda
    for ($i = strlen($numeroBase) - 1; $i >= 0; $i--) {
        $soma += intval($numeroBase[$i]) * $peso;
        $peso++;
        if ($peso > 9) {
            $peso = 2;
        }
    }
    
    $resto = $soma % 11;
    $dv = 11 - $resto;
    
    if (in_array($dv, [0, 1, 10, 11])) {
        $dv = 1;
    }
    
    return $dv;
}

?>