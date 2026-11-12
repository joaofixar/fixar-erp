<?php
namespace hardness;

/*
Layout CNAB400 Cresol (Banco 133) - remessa com registro.
Baseado nos manuais em bibliotecas/layouts_remessa/manual/cresol.
*/

require_once __DIR__ . '/../../../hardness/boleto/include/funcoes_133.php';

DEFINE("_SALTO_LINHA", CHR(13) . CHR(10));

$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from D007 ";
$cClausula .= " left join C004 on C004_Id=D007_C004_Id ";
$cClausula .= " where D007_Id='$D007_Id' ";
$D007 = mysql_query($cClausula);
$mD007 = mysql_fetch_array($D007);

if (mysql_error()) {
    return mysql_error();
}

$mD007['D007_CNPJ'] = trim($mD007['D007_CNPJ']);
$mD007['D007_CNPJ'] = str_replace(array(".", "-", "/", " "), "", $mD007['D007_CNPJ']);

if (strlen($mD007['D007_Numero_Agencia']) <= 0) {
    return "Não informado o código da cooperativa/agência no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Agencia']) > 5) {
    return "Código da cooperativa/agência deve ter no máximo 5 caracteres";
}
if (strlen($mD007['D007_Numero_Conta']) <= 0) {
    return "Não informado o número da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta']) > 7) {
    return "Número da conta deve ter no máximo 7 caracteres";
}
if (strlen($mD007['D007_Digito_Verificador_Conta']) <= 0) {
    return "Não informado o dígito verificador da conta no cadastro de contas";
}
if (strlen($mD007['D007_Digito_Verificador_Conta']) > 1) {
    return "Dígito verificador da conta deve ter no máximo 1 caractere";
}
if (strlen($mD007['D007_CNPJ']) != 14) {
    return "CNPJ do beneficiário inválido para geração da remessa Cresol";
}

$cClausula  = " select ";
$cClausula .= " *, ";
$cClausula .= " if(T117_Id is null,T002_Id,T117_Id) T117_Id, ";
$cClausula .= " if(T117_Data_Vencimento is null,T002_Data_Vencimento,T117_Data_Vencimento) T117_Data_Vencimento, ";
$cClausula .= " if(T117_Data_Emissao is null,T002_Data_Emissao,T117_Data_Emissao) T117_Data_Emissao, ";
$cClausula .= " if(T117_Valor_Total is null,T002_Valor_Total,T117_Valor_Total) T117_Valor_Total ";
$cClausula .= " from T002 ";
$cClausula .= " left join T117 on T117_Id=T002_T117_Id ";
$cClausula .= " left join D024 on D024_Id=T002_D024_Id ";
$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
$cClausula .= " left join C004 on C004_Id=T002_C004_Id ";
$cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
$cClausula .= " where $where ";
$cClausula .= " GROUP BY ifnull(T117_Id,T002_Id)";

log("Sql remessa 133: " . $cClausula);

$T117_validacao = mysql_query($cClausula);
echo mysql_error();

if (mysql_num_rows($T117_validacao) == 0) {
    return "Nenhum titulo foi selecionado para esta conta";
}
$numRegistros = mysql_num_rows($T117_validacao);

$cArquivo = '/tmp/remessa_133.txt';
if (file_exists($cArquivo)) {
    unlink($cArquivo);
}

$mErroCadastro = array();

while ($mT117 = mysql_fetch_array($T117_validacao)) {
    $mT117['D024_Cpf'] = trim($mT117['D024_Cpf']);
    $mT117['D024_Cpf'] = str_replace(array(".", "-", "/", " "), "", $mT117['D024_Cpf']);

    $mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
    $mT117['D024_Cnpj'] = str_replace(array(".", "-", "/", " "), "", $mT117['D024_Cnpj']);

    $mT117['D024_Cep'] = trim($mT117['D024_Cep']);
    $mT117['D024_Cep'] = str_replace(array(".", "-", " "), "", $mT117['D024_Cep']);

    if (empty($mT117['D024_Cpf']) && $mT117['D024_Flag_Pessoa_Fisica_Juridica'] == 'F') {
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem CPF. Remessa não gerada!";
    }

    if (empty($mT117['D024_Cnpj']) && $mT117['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem CNPJ. Remessa não gerada!";
    }

    if (empty($mT117['D024_Endereco'])) {
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem endereço. Remessa não gerada!";
    }

    if (empty($mT117['D024_Cep'])) {
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está sem CEP. Remessa não gerada!";
    }

    if (strlen($mT117['D024_Cep']) != 8) {
        $mErroCadastro[] = "Cliente: {$mT117['D024_Nome_Empresa']} está com CEP inválido. Remessa não gerada!";
    }

    if (empty($mT117['T117_Data_Vencimento'])) {
        $mErroCadastro[] = "Título {$mT117['T002_Numero_Duplicata']} está sem vencimento. Remessa não gerada!";
    }

    if (empty($mT117['T117_Data_Emissao'])) {
        $mErroCadastro[] = "Título {$mT117['T002_Numero_Duplicata']} está sem data de emissão. Remessa não gerada!";
    }

    $nossoNumeroInfo = cresolNossoNumeroInfo($mT117['T117_Nosso_Numero']);
    if (!empty($nossoNumeroInfo['erro'])) {
        $mErroCadastro[] = "Título {$mT117['T002_Numero_Duplicata']} está com Nosso Número inválido: {$nossoNumeroInfo['erro']}";
    }

    $percentualJurosMensal = round((float)$mT117['T117_Percentual_Juros_Dia'], 2);
    if ($percentualJurosMensal > 99.99) {
        $mErroCadastro[] = "Título {$mT117['T002_Numero_Duplicata']} está com juros mensal acima de 99,99% para a remessa Cresol.";
    }

    $percentualMulta = round((float)$mT117['T117_Percentual_Multa'], 2);
    if ($percentualMulta > 99.99) {
        $mErroCadastro[] = "Título {$mT117['T002_Numero_Duplicata']} está com multa acima de 99,99% para a remessa Cresol.";
    }
}

if (count($mErroCadastro) > 0) {
    return implode("\n", array_unique($mErroCadastro));
}

$cSql_T117 = mysql_query($cClausula);

mysql_query("set autocommit=0");
mysql_query("begin");

$cClausula = "select * from T035 where T035_Data_Processamento >= '" . date("Y-m-d") . " 00:00:00' AND T035_Data_Processamento <='" . date("Y-m-d") . " 23:59:59'";
$cT035 = mysql_query($cClausula);
$numReg = mysql_num_rows($cT035) + 1;
if ($numReg < 10) {
    $nomeArquivo = date("dmy") . '0' . $numReg . '.rem';
} else {
    $nomeArquivo = date("dmy") . $numReg . '.rem';
}

$cClausula  = " insert into T035 ( ";
$cClausula .= " T035_D007_Id,  ";
$cClausula .= " T035_C004_Id, ";
$cClausula .= " T035_D062_Id, ";
$cClausula .= " T035_Data_Processamento, ";
$cClausula .= " T035_Nome_Arquivo, ";
$cClausula .= " T035_Numero_Titulos ";
$cClausula .= " ) values (  ";
$cClausula .= " '$D007_Id',  ";
$cClausula .= " '{$mD007['D007_C004_Id']}', ";
$cClausula .= " '{$mD007['D007_D062_Id']}', ";
$cClausula .= " now(), ";
$cClausula .= " '{$nomeArquivo}', ";
$cClausula .= " '{$numRegistros}' ";
$cClausula .= " )  ";
$cT035 = mysql_query($cClausula);
$cErro = mysql_error();

if (!empty($cErro)) {
    mysql_query("ROLLBACK");
    return "Ocorreu um erro:$cErro/p/p$cClausula";
}

$nT035_Id = $g['mysqlLastId'];

$cHoje = date("dmy", time());
$nSeq = 1;
$valorTotalRemessa = 0;
$cString = "";

$mD007['D007_Numero_Sequencial_Remessa'] = (int)$mD007['D007_Numero_Sequencial_Remessa'];
$mD007['D007_Numero_Sequencial_Remessa']++;

$codigoEmpresaHeader = cresolCodigoEmpresaHeader($mD007);

// Header CNAB400
// 001-001 Identificação do Registro
$cString .= prepararCampo("Identificação do Registro", "Num", 1, 0, "0");
// 002-002 Identificação do Arquivo Remessa
$cString .= prepararCampo("Identificação do Arquivo Remessa", "Num", 1, 0, "1");
// 003-009 Literal Remessa
$cString .= prepararCampo("Literal Remessa", "Char", 7, 0, "REMESSA");
// 010-011 Código de Serviço
$cString .= prepararCampo("Código de Serviço", "Num", 2, 0, "01");
// 012-026 Literal Serviço
$cString .= prepararCampo("Literal Serviço", "Char", 15, 0, "COBRANCA");
// 027-046 Código da Empresa
$cString .= prepararCampo("Código da Empresa", "Num", 20, 0, $codigoEmpresaHeader);

$mD007['C004_Nome_Empresa'] = \hardness\gLimpaAcentos($mD007['C004_Nome_Empresa']);
$mD007['C004_Nome_Empresa'] = \hardness\gLimpaTexto($mD007['C004_Nome_Empresa']);
// 047-076 Nome da Empresa
$cString .= prepararCampo("Nome da Empresa", "Char", 30, 0, substr($mD007['C004_Nome_Empresa'], 0, 30));
// 077-079 Número da Cresol na Câmara
$cString .= prepararCampo("Número da Cresol na Câmara", "Num", 3, 0, "133");
// 080-094 Nome do Banco
$cString .= prepararCampo("Nome do Banco", "Char", 15, 0, "CRESOL");
// 095-100 Data da Gravação
$cString .= prepararCampo("Data da Gravação", "Num", 6, 0, $cHoje);
// 101-108 Brancos
$cString .= prepararCampo("Brancos", "Char", 8, 0, "");
// 109-110 Identificação do Sistema
$cString .= prepararCampo("Identificação do Sistema", "Char", 2, 0, "");
// 111-117 Número Sequencial da Remessa
$cString .= prepararCampo("Número Sequencial da Remessa", "Num", 7, 0, $mD007['D007_Numero_Sequencial_Remessa']);
// 118-394 Brancos
$cString .= prepararCampo("Brancos", "Char", 277, 0, "");
// 395-400 Número Sequencial do Registro
$cString .= prepararCampo("Número Sequencial do Registro", "Num", 6, 0, $nSeq);
$cString .= _SALTO_LINHA;

while ($mT117 = mysql_fetch_array($cSql_T117)) {
    $valorTotalRemessa += $mT117['T117_Valor_Total'];

    $cClausula  = " update T002 set ";
    $cClausula .= " T002_Flag_Enviar_Banco='N',";
    $cClausula .= " T002_T035_Id='$nT035_Id'  ";
    $cClausula .= " where T002_T117_Id='{$mT117['T117_Id']}' or T002_Id='{$mT117['T002_Id']}'";
    mysql_query($cClausula);

    $nSeq++;

    if ($mT117['D024_Flag_Pessoa_Fisica_Juridica'] == 'F') {
        $cCpfCnpjTipo = '01';
        $cCpfCnpj = str_replace(array(".", "-", "/", " "), "", trim($mT117['D024_Cpf']));
    } else {
        $cCpfCnpjTipo = '02';
        $cCpfCnpj = str_replace(array(".", "-", "/", " "), "", trim($mT117['D024_Cnpj']));
    }

    $mT117['D024_Cep'] = str_replace(array(".", "-", " "), "", trim($mT117['D024_Cep']));

    $nossoNumeroInfo = cresolNossoNumeroInfo($mT117['T117_Nosso_Numero']);
    $nossoNumeroBase = $nossoNumeroInfo['base'];
    $nossoNumeroDv = $nossoNumeroInfo['dv'];
    $especieTitulo = cresolEspecieTitulo($mT117, $mD007);

    $flagMulta = ((float)$mT117['T117_Percentual_Multa'] > 0) ? "2" : "0";
    $percentualMulta = ($flagMulta === "2") ? number_format((float)$mT117['T117_Percentual_Multa'], 2, "", "") : "";

    $jurosDia = cresolValorMoraDia($mT117['T117_Valor_Total'], $mT117['T117_Percentual_Juros_Dia']);

    $identificacaoEmpresa = "0";
    $identificacaoEmpresa .= prepararCampo("Carteira", "Num", 3, 0, "9");
    $identificacaoEmpresa .= prepararCampo("Código da Cooperativa", "Num", 5, 0, $mD007['D007_Numero_Agencia']);
    $identificacaoEmpresa .= prepararCampo("Conta Corrente", "Num", 7, 0, $mD007['D007_Numero_Conta']);
    $identificacaoEmpresa .= prepararCampo("DV Conta Corrente", "Char", 1, 0, $mD007['D007_Digito_Verificador_Conta']);

    $numeroDocumento = substr(trim($mT117['T002_Numero_Duplicata']), 0, 10);
    $controleParticipante = substr(trim($mT117['T002_Numero_Duplicata']), 0, 25);

    $mT117['D024_Nome_Empresa'] = \hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa'] = \hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);

    $mT117['D024_Endereco'] = \hardness\gLimpaAcentos($mT117['D024_Endereco']);
    $mT117['D024_Endereco'] = \hardness\gLimpaTexto($mT117['D024_Endereco']);
    $mT117['D024_Complemento'] = \hardness\gLimpaAcentos($mT117['D024_Complemento']);
    $mT117['D024_Complemento'] = \hardness\gLimpaTexto($mT117['D024_Complemento']);

    $enderecoCompleto = trim($mT117['D024_Endereco']);
    if (!empty($mT117['D024_Numero'])) {
        $enderecoCompleto .= ", " . trim($mT117['D024_Numero']);
    }
    if (!empty($mT117['D024_Complemento'])) {
        $enderecoCompleto .= " " . trim($mT117['D024_Complemento']);
    }

    // Detalhe CNAB400
    // 001-001 Identificação do Registro
    $cString .= prepararCampo("Identificação do Registro", "Num", 1, 0, "1");
    // 002-006 Agência de Débito
    $cString .= prepararCampo("Agência de Débito", "Char", 5, 0, "");
    // 007-007 Dígito da Agência de Débito
    $cString .= prepararCampo("Dígito da Agência de Débito", "Char", 1, 0, "");
    // 008-012 Razão da Conta Corrente
    $cString .= prepararCampo("Razão da Conta Corrente", "Char", 5, 0, "");
    // 013-019 Conta Corrente
    $cString .= prepararCampo("Conta Corrente", "Char", 7, 0, "");
    // 020-020 Dígito da Conta Corrente
    $cString .= prepararCampo("Dígito da Conta Corrente", "Char", 1, 0, "");
    // 021-037 Identificação da Empresa no Banco
    $cString .= $identificacaoEmpresa;
    // 038-062 Número Controle do Participante
    $cString .= prepararCampo("Número Controle do Participante", "Char", 25, 0, $controleParticipante);
    // 063-065 Banco para Débito na Câmara
    $cString .= prepararCampo("Banco para Débito na Câmara", "Char", 3, 0, "");
    // 066-066 Campo de Multa
    $cString .= prepararCampo("Campo de Multa", "Num", 1, 0, $flagMulta);
    // 067-070 Percentual de Multa
    $cString .= prepararCampo("Percentual de Multa", "Num", 4, 0, $percentualMulta);
    // 071-081 Nosso Número
    $cString .= prepararCampo("Nosso Número", "Num", 11, 0, $nossoNumeroBase);
    // 082-082 DV Nosso Número
    $cString .= prepararCampo("DV Nosso Número", "Char", 1, 0, $nossoNumeroDv);
    // 083-092 Desconto Bonificação por Dia
    $cString .= prepararCampo("Desconto Bonificação por Dia", "Char", 10, 0, "");
    // 093-093 Condição Emissão Papeleta
    $cString .= prepararCampo("Condição Emissão Papeleta", "Char", 1, 0, "2");
    // 094-094 Ident. se emite boleto para débito automático
    $cString .= prepararCampo("Ident. se emite boleto para débito automático", "Char", 1, 0, "");
    // 095-104 Identificação da Operação do Banco
    $cString .= prepararCampo("Identificação da Operação do Banco", "Char", 10, 0, "");
    // 105-105 Indicador Rateio Crédito
    $cString .= prepararCampo("Indicador Rateio Crédito", "Char", 1, 0, "");
    // 106-106 Aviso Débito Automático
    $cString .= prepararCampo("Aviso Débito Automático", "Char", 1, 0, "");
    // 107-108 Quantidade Possíveis de Pagamento
    $cString .= prepararCampo("Quantidade Possíveis de Pagamento", "Char", 2, 0, "");
    // 109-110 Identificação da Ocorrência
    $cString .= prepararCampo("Identificação da Ocorrência", "Num", 2, 0, "01");
    // 111-120 Número do Documento
    $cString .= prepararCampo("Número do Documento", "Char", 10, 0, $numeroDocumento);
    // 121-126 Data do Vencimento
    $cString .= prepararCampo("Data do Vencimento", "Date", 6, 0, $mT117['T117_Data_Vencimento']);
    // 127-139 Valor do Título
    $cString .= prepararCampo("Valor do Título", "Num", 13, 2, $mT117['T117_Valor_Total']);
    // 140-142 Banco Encarregado da Cobrança
    $cString .= prepararCampo("Banco Encarregado da Cobrança", "Char", 3, 0, "");
    // 143-147 Agência Depositária
    $cString .= prepararCampo("Agência Depositária", "Char", 5, 0, "");
    // 148-149 Espécie de Título
    $cString .= prepararCampo("Espécie de Título", "Num", 2, 0, $especieTitulo['codigo_remessa']);
    // 150-150 Identificação
    $cString .= prepararCampo("Identificação", "Char", 1, 0, "");
    // 151-156 Data da Emissão
    $cString .= prepararCampo("Data da Emissão", "Date", 6, 0, $mT117['T117_Data_Emissao']);
    // 157-158 Primeira Instrução
    $cString .= prepararCampo("Primeira Instrução", "Char", 2, 0, "");
    // 159-160 Segunda Instrução
    $cString .= prepararCampo("Segunda Instrução", "Char", 2, 0, "");
    // 161-173 Valor Mora por Dia
    $cString .= prepararCampo("Valor Mora por Dia", "Num", 13, 2, $jurosDia);
    // 174-179 Data Limite Desconto
    $cString .= prepararCampo("Data Limite Desconto", "Date", 6, 0, "");
    // 180-192 Valor Desconto
    $cString .= prepararCampo("Valor Desconto", "Num", 13, 2, "");
    // 193-205 Valor IOF
    $cString .= prepararCampo("Valor IOF", "Num", 13, 2, "");
    // 206-218 Valor Abatimento
    $cString .= prepararCampo("Valor Abatimento", "Num", 13, 2, "");
    // 219-220 Tipo de Inscrição do Pagador
    $cString .= prepararCampo("Tipo de Inscrição do Pagador", "Num", 2, 0, $cCpfCnpjTipo);
    // 221-234 Número Inscrição do Pagador
    $cString .= prepararCampo("Número Inscrição do Pagador", "Num", 14, 0, $cCpfCnpj);
    // 235-274 Nome do Pagador
    $cString .= prepararCampo("Nome do Pagador", "Char", 40, 0, $mT117['D024_Nome_Empresa']);
    // 275-314 Endereço Completo
    $cString .= prepararCampo("Endereço Completo", "Char", 40, 0, $enderecoCompleto);
    // 315-326 Primeira Mensagem
    $cString .= prepararCampo("Primeira Mensagem", "Char", 12, 0, "");
    // 327-331 CEP
    $cString .= prepararCampo("CEP", "Num", 5, 0, substr($mT117['D024_Cep'], 0, 5));
    // 332-334 Sufixo CEP
    $cString .= prepararCampo("Sufixo CEP", "Num", 3, 0, substr($mT117['D024_Cep'], 5, 3));
    // 335-394 Sacador/Avalista ou Segunda Mensagem
    $cString .= prepararCampo("Sacador/Avalista ou Segunda Mensagem", "Char", 60, 0, "");
    // 395-400 Número Sequencial do Registro
    $cString .= prepararCampo("Número Sequencial do Registro", "Num", 6, 0, $nSeq);
    $cString .= _SALTO_LINHA;
}

$nSeq++;

// Trailer CNAB400
// 001-001 Identificação do Registro
$cString .= prepararCampo("Identificação do Registro", "Num", 1, 0, "9");
// 002-394 Brancos
$cString .= prepararCampo("Brancos", "Char", 393, 0, "");
// 395-400 Número Sequencial do Registro
$cString .= prepararCampo("Número Sequencial do Registro", "Num", 6, 0, $nSeq);

$cString = mysql_real_escape_string($cString);

$cClausula  = " update T035 set ";
$cClausula .= " T035_Conteudo_Arquivo='$cString', ";
$cClausula .= " T035_Total_Remessa='$valorTotalRemessa' ";
$cClausula .= " where T035_Id='$nT035_Id' ";
mysql_query($cClausula);

mysql_query("UPDATE D007 SET D007_Numero_Sequencial_Remessa = '{$mD007['D007_Numero_Sequencial_Remessa']}' WHERE D007_Id = '{$D007_Id}'");

mysql_query("COMMIT");

return $nT035_Id;

function prepararCampo($Campo, $Tipo = "Char", $Tamanho, $Decimais = 0, $String = "")
{
    if (empty($Tipo)) {
        echo "Campo:$Campo Não foi definido tipo<br>";
    }

    $cRetorno = "";
    $String = trim($String);

    if (strtoupper($Tipo) == "NUM") {
        if ($String === "") {
            $String = 0;
        }

        if ($Decimais >= $Tamanho) {
            echo "Erro!! Campo:$Campo - Decimal ($Decimais) maior que o tamanho do campo($Tamanho)";
            exit;
        }

        if ($Decimais > 0) {
            $String = number_format($String, $Decimais, "", "");
        } else {
            if (($posicao = strpos($String, ".")) !== FALSE) {
                $String = substr($String, 0, $posicao);
            }
        }

        $String = preg_replace('/[^0-9]/', '', (string)$String);
        $cRetorno = str_pad(substr($String, 0, $Tamanho), $Tamanho, "0", STR_PAD_LEFT);
    }

    if (strtoupper($Tipo) == "CHAR") {
        $String = substr($String, 0, $Tamanho);
        $cRetorno = str_pad($String, $Tamanho, " ", STR_PAD_RIGHT);
    }

    if (strtoupper($Tipo) == "DATE") {
        if (!empty($String)) {
            if ($Tamanho == 6) {
                list($ano, $mes, $dia) = explode("-", $String);
                $ano = substr($ano, -2);
                $String = "$dia$mes$ano";
            }
            if ($Tamanho == 8) {
                list($ano, $mes, $dia) = explode("-", $String);
                $String = "$dia$mes$ano";
            }
        }
        $cRetorno = str_pad($String, $Tamanho, "0", STR_PAD_LEFT);
    }

    return $cRetorno;
}

function cresolCodigoEmpresaHeader($mD007)
{
    $codigo = preg_replace('/[^0-9]/', '', (string)$mD007['D007_Codigo_Cedente']);
    if (!empty($codigo)) {
        return $codigo;
    }

    return preg_replace(
        '/[^0-9]/',
        '',
        str_pad($mD007['D007_Numero_Agencia'], 5, "0", STR_PAD_LEFT) .
        str_pad($mD007['D007_Numero_Conta'], 7, "0", STR_PAD_LEFT)
    );
}

function cresolNossoNumeroInfo($nossoNumero)
{
    return b133_nosso_numero_info($nossoNumero, '09');
}

function cresolDigitoNossoNumero($nossoNumeroBase)
{
    $base = str_pad(preg_replace('/[^0-9]/', '', (string)$nossoNumeroBase), 11, '0', STR_PAD_LEFT);
    return b133_digitoVerificador_nossonumero('09' . substr($base, -11));
}

function cresolValorMoraDia($valorTitulo, $percentualJurosMensal) {
    $percentualJurosMensal = round((float)$percentualJurosMensal, 2);
    if ($percentualJurosMensal <= 0 || (float)$valorTitulo <= 0) {
        return 0;
    }

    return round(((float)$valorTitulo * ($percentualJurosMensal / 100)) / 30, 2);
}

function cresolEspecieTitulo($mT117, $mD007)
{
    return b133_resolve_especie_titulo(
        isset($mT117['T117_Especie_Documento']) && trim((string)$mT117['T117_Especie_Documento']) !== ''
            ? $mT117['T117_Especie_Documento']
            : (isset($mD007['D007_Especie_Documento']) ? $mD007['D007_Especie_Documento'] : ''),
        isset($mT117['T117_Especie']) && trim((string)$mT117['T117_Especie']) !== ''
            ? $mT117['T117_Especie']
            : (isset($mD007['D007_Especie']) ? $mD007['D007_Especie'] : '')
    );
}

?>
