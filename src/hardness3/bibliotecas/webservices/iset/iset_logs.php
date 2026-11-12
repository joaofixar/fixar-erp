<?php
namespace hardness;

const INT_STATUS_SUCESSO = 'S';
const INT_STATUS_ERRO = 'E';
const INT_STATUS_AVISO = 'A';
const INT_STATUS_INFO = 'I';
const INT_OPERACAO_IMPORTAR_PEDIDO = 'IMPORTAR_PEDIDO';
const INT_OPERACAO_IMPORTACAO_AUTOMATICA = 'IMPORTACAO_AUTOMATICA';
const INT_OPERACAO_WEBHOOK = 'WEBHOOK';
const INT_OPERACAO_SINCRONIZAR_PRODUTO = 'SINCRONIZAR_PRODUTO';
const INT_OPERACAO_CONSULTAR_PRODUTO = 'CONSULTAR_PRODUTO';
const INT_OPERACAO_ATUALIZAR_STATUS_PEDIDO = 'ATUALIZAR_STATUS_PEDIDO';
const INT_ETAPA_VALIDACAO = 'VALIDACAO';
const INT_ETAPA_CRIACAO_CLIENTE = 'CRIACAO_CLIENTE';
const INT_ETAPA_CRIACAO_CONTATO = 'CRIACAO_CONTATO';
const INT_ETAPA_CRIACAO_ENDERECO = 'CRIACAO_ENDERECO';
const INT_ETAPA_CRIACAO_PEDIDO = 'CRIACAO_PEDIDO';
const INT_ETAPA_CRIACAO_ITENS = 'CRIACAO_ITENS';
const INT_ETAPA_CRIACAO_PAGAMENTO = 'CRIACAO_PAGAMENTO';
const INT_ETAPA_CONFERENCIA_ITENS = 'CONFERENCIA_ITENS';
const INT_ETAPA_COMMIT = 'COMMIT';
const INT_ETAPA_ROLLBACK = 'ROLLBACK';
const INT_ETAPA_RECEBIMENTO = 'RECEBIMENTO';
const INT_ETAPA_ATUALIZACAO_ALIQUOTAS = 'ATUALIZACAO_ALIQUOTAS';
const INT_ETAPA_CONSULTA_PRODUTO = 'CONSULTA_PRODUTO';
const INT_ETAPA_CRIACAO_PRODUTO = 'CRIACAO_PRODUTO';
const INT_ETAPA_ATUALIZACAO_PRODUTO = 'ATUALIZACAO_PRODUTO';
const INT_ETAPA_DESATIVACAO_PRODUTO = 'DESATIVACAO_PRODUTO';
const INT_ETAPA_ATUALIZACAO_STATUS_PEDIDO = 'ATUALIZACAO_STATUS_PEDIDO';

class iset_logs_
{
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_LOGS');
}

if (!class_exists(__NAMESPACE__ . '\\iset_logs', false)) {
    class_alias(__NAMESPACE__ . '\\iset_logs_', __NAMESPACE__ . '\\iset_logs');
}

/**
 * Registra uma operação completa na INT001.
 *
 * A função deliberadamente não controla transações. Quem executa a operação
 * deve chamá-la somente depois do COMMIT ou do ROLLBACK correspondente.
 */
function integracoesRegistrarLog(array $dados) {
    global $g;

    $empresaId = isset($dados['empresaId']) ? (int) $dados['empresaId'] : (isset($g['empresaAtual']) ? (int) $g['empresaAtual'] : 0);
    $status = isset($dados['status']) ? strtoupper(trim((string) $dados['status'])) : '';
    $conexao = isset($g['conexaoBanco']) ? $g['conexaoBanco'] : null;

    if ($empresaId <= 0 || !$conexao || !in_array($status, [INT_STATUS_SUCESSO, INT_STATUS_ERRO, INT_STATUS_AVISO, INT_STATUS_INFO], true)) {
        return false;
    }

    $log = isset($dados['log']) && is_array($dados['log']) ? $dados['log'] : [];
    // Escapes Unicode preservam também caracteres de quatro bytes em bases legadas utf8.
    $log = json_encode(integracoesLimparDadosLog($log), JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($log === false) { error_log('Falha ao serializar INT001.'); return false; }

    $escapar = function ($valor, $limite) use ($conexao) {
        $valor = json_decode(json_encode(trim((string) $valor), JSON_INVALID_UTF8_SUBSTITUTE), true);
        $valor = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '�', $valor);
        $valor = substr($valor, 0, $limite);
        while ($valor !== '' && !preg_match('//u', $valor)) $valor = substr($valor, 0, -1);
        return \mysqli_real_escape_string($conexao, $valor);
    };

    $agora = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
    $idPrincipal = isset($dados['idPrincipal']) ? (int) $dados['idPrincipal'] : 0;
    $data = $agora->format('Y-m-d');
    $hora = $agora->format('H:i:s');
    $integracao = $escapar(isset($dados['integracao']) ? $dados['integracao'] : '', 30);
    $operacao = $escapar(isset($dados['operacao']) ? $dados['operacao'] : '', 50);
    $etapa = $escapar(isset($dados['etapa']) ? $dados['etapa'] : '', 50);
    $idExterno = $escapar(isset($dados['idExterno']) ? $dados['idExterno'] : '', 100);
    $tabelaPrincipal = $escapar(isset($dados['tabelaPrincipal']) ? $dados['tabelaPrincipal'] : '', 20);
    $mensagem = $escapar(integracoesMascararTextoSensivel(isset($dados['mensagem']) ? $dados['mensagem'] : ''), 255);
    $log = \mysqli_real_escape_string($conexao, $log);
    $idPrincipalSql = $idPrincipal > 0 ? "'{$idPrincipal}'" : 'NULL';
    $sql = <<<SQL
        INSERT INTO INT001 (
            INT001_C004_Id,
            INT001_Data,
            INT001_Hora,
            INT001_Integracao,
            INT001_Operacao,
            INT001_Status,
            INT001_Etapa,
            INT001_Id_Externo,
            INT001_Tabela_Principal,
            INT001_Id_Principal,
            INT001_Mensagem,
            INT001_Log
        ) VALUES (
            '{$empresaId}',
            '{$data}',
            '{$hora}',
            '{$integracao}',
            '{$operacao}',
            '{$status}',
            '{$etapa}',
            '{$idExterno}',
            '{$tabelaPrincipal}',
            {$idPrincipalSql},
            '{$mensagem}',
            '{$log}'
        )
SQL;

    if (\mysqli_query($conexao, $sql) !== false) {
        return true;
    }

    error_log('Falha ao gravar INT001.');
    return false;
}

function integracoesLimparDadosLog($dados) {
    if (!is_array($dados)) {
        return is_string($dados) ? integracoesMascararTextoSensivel($dados) : $dados;
    }

    $resultado = [];
    foreach ($dados as $chave => $valor) {
        $nome = strtolower((string) $chave);
        $sensivel = preg_match('/authorization|cookie|chave|access[_-]?token|bearer|secret|password|senha|credential|(^|[_-])token([_-]|$)|(^|[_-])key([_-]|$)|(^|[_-])sql([_-]|$)|(^|[_-])stack([_-]|$)|(^|[_-])trace([_-]|$)/', $nome);
        $resultado[$chave] = $sensivel ? '********' : integracoesLimparDadosLog($valor);
    }

    return $resultado;
}

/**
 * Remove credenciais que eventualmente apareçam dentro de uma mensagem de
 * exceção. O mascaramento recursivo acima protege valores por chave; esta
 * função cobre mensagens textuais recebidas de bibliotecas externas.
 */
function integracoesMascararTextoSensivel($texto)
{
    $texto = (string) $texto;
    // Respostas inválidas também podem conter pares JSON com valores entre aspas.
    $texto = preg_replace('/(["\'](?:[^"\']*(?:token|authorization|cookie|secret|password|senha|credential|chave)|key)["\']\s*:\s*)(["\'])(?:\\\\.|(?!\2).)*\2/i', '$1"********"', $texto);
    $texto = preg_replace('/\b(?:authorization|cookie|set-cookie)\s*:\s*[^\r\n]*/i', 'credencial: ********', $texto);
    $padrao = '/\b(authorization|access[_ -]?token|refresh[_ -]?token|api[_ -]?(key|secret)|chave[_ -]?de[_ -]?acesso|senha|password|secret|credential)\b\s*[:=]\s*(?:bearer\s+)?[^\s,;]+/i';

    return preg_replace($padrao, '$1: ********', $texto);
}

/**
 * Dados mínimos para suporte de uma falha inesperada. Não inclui rastreio
 * completo nem valores da requisição, que podem conter dados desnecessários.
 */
function integracoesDiagnosticoExcecao(\Throwable $erro)
{
    return array(
        'classe' => get_class($erro),
        'codigo' => (int) $erro->getCode(),
        'mensagem' => integracoesMascararTextoSensivel($erro->getMessage()),
        'arquivo' => basename($erro->getFile()),
        'linha' => (int) $erro->getLine(),
    );
}
