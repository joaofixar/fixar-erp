<?php
namespace hardness;

/**
 * Confere creditos do extrato com retornos de cobranca ja importados.
 * Somente leitura: nao processa retornos, baixa titulos ou vincula lancamentos.
 */
class ComposicaoRetornoExtrato
{
    const LIMITE_CREDITOS = 500;
    const LIMITE_EVENTOS = 5000;

    public static function consultar($extratoId, $empresaId)
    {
        $saida = array('extrato' => false, 'grupos' => array(), 'aviso' => '');
        $extratoId = self::id($extratoId);
        $empresaId = self::id($empresaId);
        if (!$extratoId || !$empresaId) { return $saida; }
        $consulta = self::query("/* FIN003_COMPOSICAO_CABECALHO */
            SELECT e.T215_Id AS extrato_id, e.T215_C004_Id AS empresa_id,
                   e.T215_D007_Id AS conta_bancaria_id, d.D007_Nome_Banco AS conta_bancaria
              FROM T215 e LEFT JOIN D007 d ON d.D007_Id=e.T215_D007_Id
             WHERE e.T215_Id={$extratoId} AND e.T215_C004_Id={$empresaId} LIMIT 1");
        if (!$consulta) { $saida['aviso'] = 'Não foi possível consultar o extrato.'; return $saida; }
        $extrato = mysql_fetch_assoc($consulta);
        if (!$extrato || (int) $extrato['empresa_id'] !== $empresaId) { return $saida; }
        $saida['extrato'] = $extrato;
        $banco = (int) $extrato['conta_bancaria_id'];
        if ($banco <= 0) { return $saida; }
        // Conferir todos os creditos, mesmo quando a grade esta filtrada/paginada.
        $consulta = self::query("/* FIN003_COMPOSICAO_CREDITOS */
            SELECT T216_Id AS movimento_id, T216_Data AS data_movimento, T216_Valor AS valor_movimento
              FROM T216 WHERE T216_T215_Id={$extratoId} AND T216_Flag_Excluido='N' AND T216_Valor>0
             LIMIT 501");
        if (!$consulta) { $saida['aviso'] = 'Não foi possível conferir os créditos do extrato.'; return $saida; }
        $movimentos = array(); $datas = array();
        while ($linha = mysql_fetch_assoc($consulta)) {
            $id = self::id($linha['movimento_id']);
            $valor = self::centavos($linha['valor_movimento']);
            if (!$id || isset($movimentos[$id]) || !self::dataValida($linha['data_movimento']) || $valor === null || $valor <= 0) {
                $saida['aviso'] = 'A composição dos retornos não foi conferida: há crédito com data ou valor inválido.';
                return $saida;
            }
            $movimentos[$id] = $linha;
            $datas[$linha['data_movimento']] = "'" . $linha['data_movimento'] . "'";
            if (count($movimentos) > self::LIMITE_CREDITOS) {
                $saida['aviso'] = 'A composição dos retornos não foi conferida: mais de 500 créditos neste extrato.';
                return $saida;
            }
        }
        if (!$movimentos) { return $saida; }
        $datasSql = implode(',', $datas);
        // Mesma empresa da tela de Retornos e mesma conta bancaria do extrato.
        // Mantem todos os detalhes do arquivo, inclusive titulos nao identificados:
        // omiti-los poderia produzir uma composicao parcial aparentemente exata.
        $consulta = self::query("/* FIN003_COMPOSICAO_EVENTOS */
            SELECT p.T037_Id AS retorno_id, p.T037_Nome_Arquivo AS arquivo_retorno,
                   CAST(p.T037_Codigo_Banco AS UNSIGNED) AS codigo_banco,
                   r.T038_Id AS registro_retorno_id, t.T002_Id AS titulo_id,
                   r.T038_Data_Recebimento AS pagamento_no_retorno, r.T038_Data_Credito AS credito_no_retorno,
                   r.T038_Valor_Recebido AS recebido_no_retorno,
                   t.T002_Numero_Duplicata AS duplicata, c.C004_Nome_Abreviado AS empresa_titulo,
                   d.D024_Nome_Empresa AS cliente
              FROM T037 p JOIN T038 r ON r.T038_T037_Id=p.T037_Id
              LEFT JOIN T002 t ON t.T002_Id=r.T038_T002_Id
              LEFT JOIN C004 c ON c.C004_Id=t.T002_C004_Id
              LEFT JOIN D024 d ON d.D024_Id=t.T002_D024_Id
             WHERE p.T037_C004_Id={$empresaId} AND p.T037_D007_Id={$banco}
               AND r.T038_Data_Credito IN ({$datasSql}) AND r.T038_Valor_Recebido>0
               AND ((CAST(p.T037_Codigo_Banco AS UNSIGNED)=1 AND CAST(r.T038_Codigo_Natureza_Recebimento AS UNSIGNED) IN (5,6,15))
                 OR (CAST(p.T037_Codigo_Banco AS UNSIGNED)=104 AND CAST(r.T038_Codigo_Natureza_Recebimento AS UNSIGNED) IN (21,22,23,25))
                 OR (CAST(p.T037_Codigo_Banco AS UNSIGNED)=237 AND CAST(r.T038_Codigo_Natureza_Recebimento AS UNSIGNED) IN (6,15,17))
                 OR (CAST(p.T037_Codigo_Banco AS UNSIGNED)=33 AND CAST(r.T038_Codigo_Natureza_Recebimento AS UNSIGNED) IN (6,7,8,17))
                 OR (CAST(p.T037_Codigo_Banco AS UNSIGNED)=341 AND CAST(r.T038_Codigo_Natureza_Recebimento AS UNSIGNED) IN (6,8)))
             LIMIT 5001");
        if (!$consulta) { $saida['aviso'] = 'Não foi possível consultar a composição dos retornos.'; return $saida; }
        $eventos = array();
        while ($linha = mysql_fetch_assoc($consulta)) {
            $eventos[] = $linha;
            if (count($eventos) > self::LIMITE_EVENTOS) {
                $saida['aviso'] = 'A composição dos retornos não foi conferida: mais de 5.000 registros nas datas dos créditos.';
                return $saida;
            }
        }
        $aviso = null;
        $saida['grupos'] = self::compor(array_values($movimentos), $eventos, $aviso);
        if ($aviso !== null) { $saida['aviso'] = $aviso; }
        return $saida;
    }

    public static function localizar($consulta, $movimentoId, $retornoId, $assinatura)
    {
        $movimentoId = self::id($movimentoId);
        $retornoId = self::id($retornoId);
        if (!$movimentoId || !$retornoId || !is_string($assinatura) || !preg_match('/^[a-f0-9]{64}$/D', $assinatura)
            || empty($consulta['extrato']) || empty($consulta['grupos'][$movimentoId])) { return false; }
        $grupo = $consulta['grupos'][$movimentoId];
        if ($grupo['situacao'] !== 'encontrado' || $grupo['retorno_id'] !== $retornoId
            || !hash_equals($grupo['assinatura'], $assinatura)) { return false; }
        return $grupo;
    }

    // Valores decimais do banco, sem conversao para ponto flutuante na soma.
    public static function centavos($valor)
    {
        if (!is_string($valor) && !is_int($valor)) { return null; }
        if (!preg_match('/^([+-]?)(\d{1,12})(?:\.(\d+))?$/D', $valor, $p)) { return null; }
        $fracao = isset($p[3]) ? $p[3] : '';
        if (strlen($fracao) > 2 && trim(substr($fracao, 2), '0') !== '') { return null; }
        $centavos = (int) $p[2] * 100 + (int) substr(str_pad($fracao, 2, '0'), 0, 2);
        return $p[1] === '-' ? -$centavos : $centavos;
    }

    public static function formatarCentavos($valor)
    {
        return ($valor < 0 ? '-' : '') . number_format(intdiv(abs($valor), 100), 0, ',', '.')
            . ',' . str_pad((string) (abs($valor) % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function formatarData($data)
    {
        return self::dataValida($data) ? substr($data, 8, 2) . '/' . substr($data, 5, 2) . '/' . substr($data, 0, 4) : '';
    }

    private static function dataValida($data)
    {
        return is_string($data) && preg_match('/^([1-9]\d{3})-(\d{2})-(\d{2})$/D', $data, $p)
            && checkdate((int) $p[2], (int) $p[3], (int) $p[1]);
    }

    private static function id($id)
    {
        return (is_int($id) || is_string($id)) && preg_match('/^[1-9]\d{0,9}$/D', (string) $id) ? (int) $id : 0;
    }

    private static function query($sql)
    {
        try { return mysql_query($sql); } catch (\Throwable $erro) { return false; }
    }

    // Grupos completos por arquivo/credito e arquivo/credito/pagamento.
    // Nao escolhe subconjuntos arbitrarios para atingir um valor.
    public static function compor($movimentos, $eventos, &$aviso = null) {
        $aviso = null;
        if (count($movimentos) > self::LIMITE_CREDITOS || count($eventos) > self::LIMITE_EVENTOS) {
            $aviso = 'A composição dos retornos não foi conferida: limite de registros atingido.';
            return array();
        }
        usort($eventos, function ($a, $b) { return (int) $a['registro_retorno_id'] <=> (int) $b['registro_retorno_id']; });
        $grupos = array();
        foreach ($eventos as $linha) {
            $chave = (int) $linha['retorno_id'] . '|' . $linha['credito_no_retorno'];
            $grupos[$chave . '|todos'][] = $linha;
            if (self::dataValida($linha['pagamento_no_retorno'])) {
                $grupos[$chave . '|pagamento|' . $linha['pagamento_no_retorno']][] = $linha;
            }
        }
        $candidatos = array();
        $naturaisSantander = array();
        foreach ($grupos as $membros) {
            $total = 0;
            $ids = array();
            $titulos = array();
            $valido = true;
            foreach ($membros as $linha) {
                $valor = self::centavos($linha['recebido_no_retorno']);
                $titulo = (int) $linha['titulo_id'];
                $registro = (int) $linha['registro_retorno_id'];
                if ($valor === null || $valor <= 0 || $titulo <= 0 || $registro <= 0
                    || (int) $linha['retorno_id'] <= 0 || !self::dataValida($linha['credito_no_retorno'])
                    || isset($titulos[$titulo]) || isset($ids[$registro])) { $valido = false; }
                $total += $valor === null ? 0 : $valor;
                $titulos[$titulo] = true;
                $ids[$registro] = $registro;
            }
            // Inconsistencias nao podem virar uma composicao parcial aparentemente exata.
            if (!$valido) { continue; }
            sort($ids, SORT_NUMERIC);
            $assinatura = hash('sha256', implode(',', $ids));
            $data = $membros[0]['credito_no_retorno'];
            $candidatos[$data][$total][$assinatura] = array(
                'assinatura' => $assinatura, 'retorno_id' => (int) $membros[0]['retorno_id'],
                'arquivo' => $membros[0]['arquivo_retorno'], 'total' => $total,
                'membros' => $membros, 'titulos' => $titulos,
            );
            if (isset($membros[0]['codigo_banco']) && (int) $membros[0]['codigo_banco'] === 33) {
                $naturaisSantander[$assinatura] = $candidatos[$data][$total][$assinatura];
            }
        }
        // Santander pode creditar separadamente um titulo e o restante do mesmo retorno.
        $santander = self::particoesSantander($movimentos, $naturaisSantander);
        if ($santander['limite']) {
            // O corte de alternativas impediria afirmar que o resultado e unico.
            $aviso = 'A composição dos retornos não foi conferida: limite de candidatos atingido.';
            return array();
        }
        foreach ($santander['grupos'] as $grupo) {
            $data = $grupo['membros'][0]['credito_no_retorno'];
            $candidatos[$data][$grupo['total']][$grupo['assinatura']] = $grupo;
        }
        $resultado = array();
        $uso = array();
        foreach ($movimentos as $movimento) {
            $id = (int) $movimento['movimento_id'];
            $valor = self::centavos($movimento['valor_movimento']);
            $opcoes = isset($candidatos[$movimento['data_movimento']][$valor])
                ? $candidatos[$movimento['data_movimento']][$valor] : array();
            foreach ($opcoes as $opcao) {
                foreach ($opcao['titulos'] as $titulo => $ignorado) {
                    $chave = $movimento['data_movimento'] . '|' . $titulo;
                    $uso[$chave][$id] = true;
                }
            }
            if (count($opcoes) > 1) {
                $resultado[$id] = array('situacao' => 'ambiguo');
            } elseif (count($opcoes) === 1) {
                $grupo = reset($opcoes);
                $grupo['situacao'] = 'encontrado';
                $grupo['movimento'] = $movimento;
                $resultado[$id] = $grupo;
            }
        }
        // O mesmo titulo/credito nao pode compor duas linhas, inclusive alternativas ambiguas.
        foreach ($uso as $ids) {
            if (count($ids) <= 1) { continue; }
            foreach ($ids as $id => $ignorado) { $resultado[$id] = array('situacao' => 'ambiguo'); }
        }
        // Se uma ponta tiver outra composicao possivel, ambas exigem conferencia.
        $vizinhos = array(); $fila = array();
        foreach ($santander['particoes'] as $particao) {
            $a = $particao['a']; $b = $particao['b'];
            $vizinhos[$a][$b] = true; $vizinhos[$b][$a] = true;
            if (!isset($resultado[$a], $resultado[$b])
                || $resultado[$a]['situacao'] !== 'encontrado' || $resultado[$b]['situacao'] !== 'encontrado'
                || $resultado[$a]['assinatura'] !== $particao['assinatura_a']
                || $resultado[$b]['assinatura'] !== $particao['assinatura_b']) {
                $fila[] = $a; $fila[] = $b;
            }
        }
        $visitados = array();
        for ($i = 0; $i < count($fila); $i++) {
            $id = $fila[$i]; if (isset($visitados[$id])) { continue; }
            $visitados[$id] = true; $resultado[$id] = array('situacao' => 'ambiguo');
            if (isset($vizinhos[$id])) { foreach ($vizinhos[$id] as $vizinho => $ignorado) { $fila[] = $vizinho; } }
        }
        return $resultado;
    }

    private static function particoesSantander($movimentos, $naturais) {
        $saida = array('grupos' => array(), 'particoes' => array(), 'limite' => false);
        if (!$naturais) { return $saida; }
        $porDataValor = array();
        foreach ($movimentos as $movimento) {
            $valor = self::centavos($movimento['valor_movimento']);
            if ($valor > 0) { $porDataValor[$movimento['data_movimento']][$valor][(int) $movimento['movimento_id']] = true; }
        }
        $membrosPropostos = 0;
        foreach ($naturais as $natural) {
            if (count($natural['membros']) < 2) { continue; }
            $data = $natural['membros'][0]['credito_no_retorno'];
            if (empty($porDataValor[$data])) { continue; }
            $porValor = array(); $ids = array();
            foreach ($natural['membros'] as $linha) {
                $valor = self::centavos($linha['recebido_no_retorno']);
                $porValor[$valor][] = $linha;
                $ids[(int) $linha['registro_retorno_id']] = true;
            }
            foreach ($porValor as $valor => $linhas) {
                $restante = $natural['total'] - $valor;
                if (count($linhas) !== 1 || $restante <= 0 || $restante === $valor
                    || empty($porDataValor[$data][$valor]) || empty($porDataValor[$data][$restante])
                    || count($porDataValor[$data][$valor]) !== 1 || count($porDataValor[$data][$restante]) !== 1) { continue; }
                $a = (int) key($porDataValor[$data][$valor]); $b = (int) key($porDataValor[$data][$restante]);
                if ($a === $b) { continue; }
                $linha = $linhas[0]; $registro = (int) $linha['registro_retorno_id'];
                $outrosIds = $ids; unset($outrosIds[$registro]);
                $outrosIds = array_keys($outrosIds); sort($outrosIds, SORT_NUMERIC);
                $assinaturaA = hash('sha256', (string) $registro);
                $assinaturaB = hash('sha256', implode(',', $outrosIds));
                $par = array($assinaturaA, $assinaturaB); sort($par, SORT_STRING);
                $chave = implode('|', $par);
                if (isset($saida['particoes'][$chave])) { continue; }
                $membrosPropostos += count($natural['membros']);
                if ($membrosPropostos > 10000 || count($saida['particoes']) >= 500) {
                    // Descartar o lote inteiro: limites nao podem esconder alternativas.
                    return array('grupos' => array(), 'particoes' => array(), 'limite' => true);
                }
                $individual = $natural; $individual['assinatura'] = $assinaturaA;
                $individual['total'] = $valor; $individual['membros'] = array($linha);
                $individual['titulos'] = array((int) $linha['titulo_id'] => true);
                $grupo = $natural; $grupo['assinatura'] = $assinaturaB;
                $grupo['total'] = $restante; $grupo['membros'] = array();
                $grupo['titulos'] = array();
                foreach ($natural['membros'] as $outra) {
                    if ((int) $outra['registro_retorno_id'] === $registro) { continue; }
                    $grupo['membros'][] = $outra; $grupo['titulos'][(int) $outra['titulo_id']] = true;
                }
                $saida['grupos'][$assinaturaA] = $individual;
                $saida['grupos'][$assinaturaB] = $grupo;
                $saida['particoes'][$chave] = array('a' => $a, 'b' => $b,
                    'assinatura_a' => $assinaturaA, 'assinatura_b' => $assinaturaB);
            }
        }
        return $saida;
    }
}
