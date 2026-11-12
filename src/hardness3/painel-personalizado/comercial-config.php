<?php
namespace hardness;

function painelComercialCamposDados()
{
    return array('documento', 'situacao', 'quantidade_hoje', 'valor_hoje', 'quantidade_mes', 'valor_mes');
}

function painelComercialValidarDados($dados, &$erros, $id = 'Comercial')
{
    $situacoes = array(
        'orcamentos' => array('com_pedido', 'em_aberto', 'cancelado'),
        'pedidos' => array('pendente', 'em_expedicao', 'faturado', 'com_nf', 'sem_nf', 'cancelado'),
        'notas_fiscais' => array('emitida', 'aguardando_autorizacao', 'cancelada'),
    );
    if (!is_array($dados) || !in_array(count($dados), array(8, 9), true) || array_values($dados) !== $dados) {
        $erros[] = $id . ': informe os registros de orçamento, pedido e nota fiscal, inclusive quando zerados.';
        return;
    }
    $vistos = array();
    foreach ($dados as $linha) {
        if (!is_array($linha) || !isset($linha['documento'], $linha['situacao']) || !is_string($linha['documento']) || !is_string($linha['situacao']) || !isset($situacoes[$linha['documento']]) || !in_array($linha['situacao'], $situacoes[$linha['documento']], true)) {
            $erros[] = $id . ': documento ou situação comercial inválido.';
            continue;
        }
        $chave = $linha['documento'] . ':' . $linha['situacao'];
        if (isset($vistos[$chave])) $erros[] = $id . ': não repita documento e situação.';
        $vistos[$chave] = true;
        $validos = true;
        foreach (array('quantidade_hoje', 'valor_hoje', 'quantidade_mes', 'valor_mes') as $campo) {
            $valor = isset($linha[$campo]) ? $linha[$campo] : null;
            if (!is_numeric($valor) || !is_finite((float) $valor) || (float) $valor < 0 || (float) $valor > 9007199254740991 || (strpos($campo, 'quantidade') === 0 && floor((float) $valor) !== (float) $valor)) {
                $erros[] = $id . ': valores devem ser numéricos, finitos e não negativos; quantidades devem ser inteiras.';
                $validos = false;
            }
        }
        if (!$validos) continue;
        if ((float) $linha['quantidade_hoje'] > (float) $linha['quantidade_mes'] || (float) $linha['valor_hoje'] > (float) $linha['valor_mes']) $erros[] = $id . ': o dia deve estar incluído no acumulado do mês.';
        foreach (array('hoje', 'mes') as $periodo) {
            if ((float) $linha['quantidade_' . $periodo] === 0.0 && (float) $linha['valor_' . $periodo] !== 0.0) $erros[] = $id . ': uma situação sem documentos deve ter valor zero.';
        }
    }
    $fixas = array('orcamentos:com_pedido', 'orcamentos:em_aberto', 'orcamentos:cancelado', 'notas_fiscais:emitida', 'notas_fiscais:cancelada');
    $formatosPedidos = array(array('pedidos:pendente', 'pedidos:em_expedicao', 'pedidos:faturado'), array('pedidos:com_nf', 'pedidos:sem_nf', 'pedidos:cancelado'));
    $recebidas = array_keys($vistos);
    sort($recebidas);
    $formatoValido = false;
    foreach ($formatosPedidos as $pedidos) {
        foreach (array(false, true) as $comFila) {
            $esperadas = array_merge($fixas, $pedidos, $comFila ? array('notas_fiscais:aguardando_autorizacao') : array());
            sort($esperadas);
            if ($recebidas === $esperadas) $formatoValido = true;
        }
    }
    if (!$formatoValido) {
        $erros[] = $id . ': informe todas as situações de orçamento, pedido e nota fiscal sem misturar os formatos.';
    }
}

function painelComercialValidar($painel, &$erros)
{
    $id = isset($painel['id']) && is_string($painel['id']) ? $painel['id'] : 'Comercial';
    if (isset($painel['demonstracao']) && !is_bool($painel['demonstracao'])) $erros[] = $id . ': demonstracao deve ser booleano.';
    if (isset($painel['referencia'])) {
        $data = is_string($painel['referencia']) ? \DateTime::createFromFormat('!Y-m-d', $painel['referencia']) : false;
        if (!$data || $data->format('Y-m-d') !== $painel['referencia']) $erros[] = $id . ': referencia deve ser uma data válida no formato AAAA-MM-DD.';
    }
    if (!array_key_exists('dados', $painel)) return;
    if (array_key_exists('sqlBase', $painel)) $erros[] = $id . ': escolha dados no JSON ou sqlBase.';
    if (isset($painel['filtros']) || isset($painel['ordenacao'])) $erros[] = $id . ': filtros e ordenacao são usados somente com sqlBase.';
    painelComercialValidarDados($painel['dados'], $erros, $id);
}
