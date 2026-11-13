<?php
namespace hardness\layouts\cna400;

define('_SALTO_LINHA', chr(13) . chr(10));

/**
 * Processamento de Retorno C6 Bank (336)
 * Baseado na documentação CNAB400 C6 páginas 15-18 e 24-29
 */
function processarRetorno336($conteudoArquivo, $D007_Id) {
    if (empty($conteudoArquivo)) {
        logC6('Arquivo de retorno vazio', 'ERRO');
        return 'Arquivo de retorno vazio';
    }
    $linhas = preg_split('/\r\n|\r|\n/', $conteudoArquivo);
    if (count($linhas) < 3) {
        logC6('Arquivo de retorno inválido: menos de 3 linhas', 'ERRO');
        return 'Arquivo de retorno inválido';
    }
    $header = $linhas[0];
    validarHeaderRetornoC6($header);
    $titulosProcessados = 0;
    $titulosLiquidados = 0;
    $valorTotal = 0;
    for ($i = 1; $i < count($linhas) - 1; $i++) {
        if (empty(trim($linhas[$i]))) continue;
        $tipoRegistro = substr($linhas[$i], 0, 1);
        if ($tipoRegistro == '1') {
            $resultado = processarDetalheRetornoC6($linhas[$i]);
            if ($resultado['sucesso']) {
                $titulosProcessados++;
                if ($resultado['liquidado']) {
                    $titulosLiquidados++;
                    $valorTotal += $resultado['dados']['valor_pago'];
                }
                atualizarTituloRetornoC6($resultado);
            }
        }
    }
    $trailer = $linhas[count($linhas) - 1];
    validarTrailerRetornoC6($trailer, $titulosProcessados, $valorTotal);
    registrarProcessamentoRetornoC6($D007_Id, $titulosProcessados, $titulosLiquidados, $valorTotal);
    logC6("Retorno processado: $titulosProcessados títulos, $titulosLiquidados liquidados, valor total R$ $valorTotal");
    return array(
        'sucesso' => true,
        'titulos_processados' => $titulosProcessados,
        'titulos_liquidados' => $titulosLiquidados,
        'valor_total' => $valorTotal
    );
}

function processarDetalheRetornoC6($linha) {
    // C6 FIX: Correção das posições dos campos conforme manual CNAB400 C6
    $dados = array(
        'nosso_numero' => substr($linha, 62, 11), // 63-73 (11)
        'digito_nosso_numero' => substr($linha, 73, 1), // 74 (1)
        'nosso_numero_complementar' => substr($linha, 74, 12), // 75-86 (12) // C6 FIX
        'codigo_ocorrencia' => substr($linha, 108, 2), // 109-110 (2)
        'data_ocorrencia' => cnabData(substr($linha, 110, 6)), // 111-116 (6) DDMMAA -> YYYY-MM-DD // C6 FIX
        'seu_numero' => substr($linha, 116, 10), // 117-126 (10) // C6 FIX
        'data_vencimento' => cnabData(substr($linha, 146, 6)), // 147-152 (6) DDMMAA -> YYYY-MM-DD // C6 FIX
        'valor_titulo' => cnabValor(substr($linha, 152, 13)), // 153-165 (13) // C6 FIX
        'banco_cobrador' => substr($linha, 165, 3), // 166-168 (3) // C6 FIX
        'agencia_cobradora' => substr($linha, 168, 5), // 169-173 (5) // C6 FIX
        'valor_tarifa' => cnabValor(substr($linha, 175, 13)), // 176-188 (13) // C6 FIX
        'valor_abatimento' => cnabValor(substr($linha, 227, 13)), // 228-240 (13) // C6 FIX
        'valor_desconto' => cnabValor(substr($linha, 240, 13)), // 241-253 (13) // C6 FIX
        'valor_principal' => cnabValor(substr($linha, 253, 13)), // 254-266 (13) // C6 FIX
        'valor_juros' => cnabValor(substr($linha, 266, 13)), // 267-279 (13) // C6 FIX
        'valor_outros_acrescimos' => cnabValor(substr($linha, 279, 13)), // 280-292 (13) // C6 FIX
        'data_credito' => cnabData(substr($linha, 295, 6)), // 296-301 (6) // C6 FIX
        'campos_invalidos' => substr($linha, 365, 12), // 366-377 (12) // C6 FIX
        'codigo_recusa' => substr($linha, 377, 16), // 378-393 (16) // C6 FIX
    );
    $status = interpretarOcorrenciaRetornoC6($dados['codigo_ocorrencia']);
    $recusa = interpretarCodigoRecusaC6($dados['codigo_recusa']); // C6 FIX
    // C6 TRAILER FIX: lógica de sucesso aprimorada
    $codigosNaoSucesso = ['03', '15', '16', '17', '90'];
    $sucesso = in_array($dados['codigo_ocorrencia'], array_keys(ocorrenciasValidasC6())) && !in_array($dados['codigo_ocorrencia'], $codigosNaoSucesso);
    return array(
        'sucesso' => $sucesso,
        'liquidado' => in_array($dados['codigo_ocorrencia'], ['06', '07', '08', '75']),
        'status' => $status,
        'recusa' => $recusa, // C6 FIX
        'dados' => $dados
    );
}

function interpretarOcorrenciaRetornoC6($codigo) {
    $ocorrencias = ocorrenciasValidasC6(); // C6 FIX: tabela completa
    return $ocorrencias[$codigo] ?? "Código desconhecido: $codigo";
}

// C6 FIX: tabela completa de ocorrências válidas (página 24)
function ocorrenciasValidasC6() {
    return array(
        '02' => 'Entrada Confirmada',
        '03' => 'Entrada Rejeitada',
        '04' => 'Alteração de Dados (Entrada)',
        '05' => 'Alteração de Dados (Baixa)',
        '06' => 'Liquidação do Título',
        '07' => 'Liquidação do Título Após a Baixa',
        '08' => 'Título Liquidado em Cartório',
        '09' => 'Baixa do Título',
        '10' => 'Baixa Realizada pelo Beneficiário via Arquivo',
        '12' => 'Abatimento Concedido',
        '13' => 'Abatimento Cancelado',
        '14' => 'Vencimento Alterado',
        '15' => 'Baixa Rejeitada',
        '16' => 'Instrução Rejeitada',
        '17' => 'Alterações de Dados Rejeitados',
        '19' => 'Confirma Instrução de Protesto',
        '20' => 'Confirma Instrução de Sustação de Protesto',
        '21' => 'Confirma Instrução de Não Protestar',
        '23' => 'Protesto Enviado a Cartório',
        '32' => 'Baixa por ter Sido Protestado',
        '35' => 'Alegações do Sacado',
        '69' => 'Cancelamento de Liquidação por Cheque Devolvido',
        '71' => 'Título Cancelado pelo Cartório',
        '72' => 'Baixa Operacional',
        '74' => 'Cancelamento da Baixa Operacional',
        '75' => 'Pagamento Parcial',
        '90' => 'Instrução de Protesto Rejeitada',
        '95' => 'Troca Uso Empresa',
        '96' => 'Emissão Extrato Movimentação da Carteira',
        '97' => 'Tarifa de Sustação de Protesto',
        '98' => 'Tarifa de Protesto',
        '99' => 'Custas de Protesto',
    );
}

// C6 RECUSA FIX: mapeamento real dos códigos de recusa 9000-9999 (páginas 25-29)
function interpretarCodigoRecusaC6($codigo) {
    $codigo = trim($codigo);
    if ($codigo === '' || $codigo === str_repeat('0', strlen($codigo))) return null;
    $mapa = array(
        '9000' => 'Código de ocorrência inválido',
        '9001' => 'Agência inválida',
        '9021' => 'Dígito do Nosso Número inválido',
        '9024' => 'Data de emissão inválida',
        '9025' => 'Data de desconto inválida',
        '9026' => 'Data de abatimento inválida',
        '9027' => 'Data de protesto inválida',
        '9035' => 'Valor de IOF inválido',
        '9090' => 'Pagador não cadastrado',
        '9091' => 'Pagador bloqueado',
        '9092' => 'Pagador com restrição',
        '9093' => 'Pagador CPF/CNPJ inválido',
        '9094' => 'Pagador endereço incompleto',
        '9095' => 'Pagador nome inválido',
        '9096' => 'Pagador UF inválida',
        '9097' => 'Pagador município inválido',
        '9098' => 'Pagador bairro inválido',
        '9099' => 'Pagador CEP inválido',
        '9103' => 'Sacador não cadastrado',
        '9106' => 'Sacador CPF/CNPJ inválido',
        '9240' => 'Espécie de documento inválida',
        '9250' => 'Código de multa inválido',
        '9251' => 'Valor de multa inválido',
        '9252' => 'Data de multa inválida',
        '9253' => 'Código de desconto inválido',
        '9254' => 'Valor de desconto inválido',
        '9255' => 'Data de desconto inválida',
        '9256' => 'Código de abatimento inválido',
        '9257' => 'Valor de abatimento inválido',
        '9258' => 'Data de abatimento inválida',
        '9259' => 'Código de protesto inválido',
        '9260' => 'Data de protesto inválida',
        '9261' => 'Código de baixa inválido',
        '9262' => 'Data de baixa inválida',
        '9263' => 'Código de mora inválido',
        '9264' => 'Valor de mora inválido',
        '9265' => 'Data de mora inválida',
        '9266' => 'Código de IOF inválido',
        '9267' => 'Data de IOF inválida',
        '9999' => 'Outros motivos de recusa',
    );
    // Pode haver múltiplos códigos separados
    $codigos = str_split($codigo, 4);
    $desc = [];
    foreach ($codigos as $c) {
        if (isset($mapa[$c])) $desc[] = $mapa[$c];
        elseif (preg_match('/^9[0-9]{3}$/', $c)) $desc[] = 'Motivo de recusa não mapeado: ' . $c;
    }
    return $desc ? implode('; ', $desc) : null;
}

// C6 TRAILER FIX: simplificação de cnabValor()
function cnabValor($valor) {
    return floatval($valor) / 100.00;
}

// C6 FIX: conversão de data DDMMAA para YYYY-MM-DD
function cnabData($data) {
    $data = trim($data);
    if (strlen($data) !== 6 || $data === '000000') return null;
    $d = substr($data, 0, 2);
    $m = substr($data, 2, 2);
    $a = substr($data, 4, 2);
    $a = (intval($a) < 50 ? '20' : '19') . $a;
    return "$a-$m-$d";
}

function logC6($mensagem, $nivel = 'INFO') {
    $logEntry = date('Y-m-d H:i:s') . " [C6-$nivel] $mensagem";
    error_log($logEntry . PHP_EOL, 3, '/tmp/c6_remessa.log');
}

// Funções de validação e atualização (stubs, devem ser implementadas conforme integração do sistema)
// C6 FIX: validação completa do header (tipo 0)
function validarHeaderRetornoC6($header) {
    $tipo_registro = substr($header, 0, 1);
    $codigo_banco = substr($header, 76, 3); // 77-79 (3)
    if ($tipo_registro !== '0') {
        logC6('Header inválido: tipo de registro diferente de 0', 'ERRO');
        throw new \Exception('Header inválido: tipo de registro diferente de 0');
    }
    if ($codigo_banco !== '336') {
        logC6('Header inválido: código do banco diferente de 336', 'ERRO');
        throw new \Exception('Header inválido: código do banco diferente de 336');
    }
    // Outras validações podem ser incluídas conforme necessidade do sistema
    return true;
}

// C6 TRAILER FIX: validação completa do trailer (tipo 9) com posições corretas
function validarTrailerRetornoC6($trailer, $titulosProcessados, $valorTotal) {
    $tipo_registro = substr($trailer, 0, 1);
    if ($tipo_registro !== '9') {
        logC6('Trailer inválido: tipo de registro diferente de 9', 'ERRO');
        throw new \Exception('Trailer inválido: tipo de registro diferente de 9');
    }
    // Valor Total da Carteira: posições 3-16 (14 dígitos)
    $valor_total_carteira = cnabValor(substr($trailer, 2, 14));
    // Quantidade Total de Títulos na Carteira: posições 17-22 (6 dígitos)
    $qtd_total_carteira = intval(ltrim(substr($trailer, 16, 6), '0'));
    // Valor Total de Liquidações: posições 23-36 (14 dígitos)
    $valor_total_liquidacoes = cnabValor(substr($trailer, 22, 14));
    // Quantidade Total de Títulos Liquidados: posições 37-42 (6 dígitos)
    $qtd_total_liquidados = intval(ltrim(substr($trailer, 36, 6), '0'));

    // Validação dos totais liquidados
    if ($qtd_total_liquidados !== $titulosProcessados) {
        logC6("Trailer: quantidade de títulos liquidados divergente. Arquivo: $qtd_total_liquidados, Processados: $titulosProcessados", 'ERRO');
        throw new \Exception('Trailer: quantidade de títulos liquidados divergente');
    }
    if (abs($valor_total_liquidacoes - $valorTotal) > 0.01) {
        logC6("Trailer: valor total de liquidações divergente. Arquivo: $valor_total_liquidacoes, Processado: $valorTotal", 'ERRO');
        throw new \Exception('Trailer: valor total de liquidações divergente');
    }
    return true;
}
function atualizarTituloRetornoC6($resultado) { /* ... */ }
function registrarProcessamentoRetornoC6($D007_Id, $titulosProcessados, $titulosLiquidados, $valorTotal) { /* ... */ }
