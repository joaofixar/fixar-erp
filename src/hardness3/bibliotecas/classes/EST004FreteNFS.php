<?php
namespace hardness;

/** Frete documentado por NFS, separado dos valores fiscais do XML de produtos. */
class EST004FreteNFS
{
    /** Confere os documentos e as parcelas antes de finalizar compras/estoque. */
    public function validarFinalizacao($notaId)
    {
        global $g;
        $notaId = (int) $notaId;
        $empresaId = (int) $g['empresaAtual'];
        try {
            $consulta = $this->executar("SELECT T013_Tipo, T013_Flag_Frete, T013_Valor_Total,
                                                T013A_T013_Id_Frete_NFS, T013A_JSON
                                           FROM T013 LEFT JOIN T013A ON T013A_T013_Id = T013_Id
                                           WHERE T013_Id = '{$notaId}' AND T013_C004_Id = '{$empresaId}' LIMIT 1");
            $nota = mysql_fetch_assoc($consulta);
            if (!$nota) { return 'Nota não encontrada na empresa atual.'; }
            if ($nota['T013_Flag_Frete'] == '4' || $nota['T013A_T013_Id_Frete_NFS'] > 0) {
                $json = json_decode($nota['T013A_JSON'], true);
                if ((int) $nota['T013_Tipo'] != 10 || $nota['T013_Flag_Frete'] != '4' ||
                    $nota['T013A_T013_Id_Frete_NFS'] <= 0 || !isset($json['freteNFS']['valorCentavos']) ||
                    (int) $json['freteNFS']['valorCentavos'] != (int) round((float) $nota['T013_Valor_Total'] * 100)) {
                    return 'Selecione Frete NFS e clique em Ratear antes de finalizar a nota de serviço.';
                }
            }
            $consulta = $this->executar("SELECT T013_Id, T013_Tipo, T013_Flag_Frete, T013_Flag_Cancelada, T013_Valor_Total, T013A_JSON
                                           FROM T013A INNER JOIN T013 ON T013_Id = T013A_T013_Id
                                           WHERE T013A_T013_Id_Frete_NFS = '{$notaId}' AND T013_C004_Id = '{$empresaId}'");
            $fontes = array();
            while ($fonte = mysql_fetch_assoc($consulta)) { $fontes[] = $fonte; }
            if (!$fontes) { return true; }
            $consulta = $this->executar("SELECT T014_Id, T014_Quantidade, T014_Valor_Custo_Unitario, IFNULL(T014A_Valor_Frete_NFS,0) AS T014A_Valor_Frete_NFS
                                           FROM T014 LEFT JOIN T014A ON T014A_T014_Id = T014_Id
                                           WHERE T014_T013_Id = '{$notaId}' AND T014_C004_Id = '{$empresaId}' ORDER BY T014_Id");
            $itens = array();
            while ($item = mysql_fetch_assoc($consulta)) { $itens[(int) $item['T014_Id']] = $item; }
            $esperado = array_fill_keys(array_keys($itens), 0);
            foreach ($fontes as $fonte) {
                $json = json_decode($fonte['T013A_JSON'], true);
                $rateio = isset($json['freteNFS']) ? $json['freteNFS'] : array();
                $valor = (int) round((float) $fonte['T013_Valor_Total'] * 100);
                if ($fonte['T013_Flag_Cancelada'] == 'S' || (int) $fonte['T013_Tipo'] != 10 || $fonte['T013_Flag_Frete'] != '4' ||
                    !isset($rateio['valorCentavos'], $rateio['forma'], $rateio['itens']) || $valor != $rateio['valorCentavos']) {
                    return 'Uma NFS vinculada foi alterada. Refaça o rateio antes de finalizar a nota de produtos.';
                }
                $novo = self::distribuir($itens, $valor, $rateio['forma']);
                if ($novo != $rateio['itens']) {
                    return 'Os itens foram alterados após o rateio. Clique em Ratear na NFS vinculada antes de finalizar.';
                }
                foreach ($novo as $id => $centavos) { $esperado[$id] += $centavos; }
            }
            foreach ($itens as $id => $item) {
                if ($esperado[$id] != (int) round((float) $item['T014A_Valor_Frete_NFS'] * 100)) {
                    return 'O frete NFS dos itens está divergente. Refaça o rateio antes de finalizar.';
                }
            }
            return true;
        } catch (\Exception $erro) { return $erro->getMessage(); }
    }

    public function validarRemocao($notaId)
    {
        global $g;
        $notaId = (int) $notaId;
        $empresaId = (int) $g['empresaAtual'];
        try {
            $consulta = $this->executar("SELECT T013A_Id FROM T013A INNER JOIN T013 ON T013_Id = T013A_T013_Id
                                           WHERE T013_C004_Id = '{$empresaId}' AND
                                           ((T013A_T013_Id = '{$notaId}' AND T013A_T013_Id_Frete_NFS > 0)
                                            OR T013A_T013_Id_Frete_NFS = '{$notaId}') LIMIT 1");
            return mysql_fetch_assoc($consulta) ? 'Nota com frete NFS rateado não pode ser excluída ou cancelada.' : true;
        } catch (\Exception $erro) { return $erro->getMessage(); }
    }

    private function executar($sql)
    {
        $resultado = mysql_query($sql, false);
        if ($resultado === false) {
            throw new \RuntimeException('Não foi possível processar o frete NFS. Verifique a atualização da base e tente novamente.');
        }
        return $resultado;
    }

    /** Distribui centavos inteiros, atribuindo os resíduos às maiores frações. */
    public static function distribuir($itens, $centavos, $forma)
    {
        if ($centavos <= 0 || !is_scalar($forma) || !in_array((string) $forma, array('1', '2'), true)) {
            throw new \RuntimeException('Informe um valor positivo e a forma de rateio do frete.');
        }
        $pesos = array();
        foreach ($itens as $item) {
            $quantidade = (float) $item['T014_Quantidade'];
            $preco = (float) $item['T014_Valor_Custo_Unitario'];
            if (!is_finite($quantidade) || !is_finite($preco) || $quantidade <= 0 || $preco < 0) {
                throw new \RuntimeException('Corrija as quantidades e os preços dos itens antes de ratear o frete NFS.');
            }
            $pesos[(int) $item['T014_Id']] = $forma == '1' ? $quantidade * $preco : $quantidade;
        }
        $total = array_sum($pesos);
        if ($total <= 0 || !is_finite($total)) {
            throw new \RuntimeException('A nota selecionada não possui itens com base para o rateio.');
        }
        $rateio = array();
        $fracoes = array();
        foreach ($pesos as $id => $peso) {
            $parcela = $centavos * ($peso / $total);
            $rateio[$id] = (int) floor($parcela);
            $fracoes[$id] = $parcela - $rateio[$id];
        }
        uksort($fracoes, function ($a, $b) use ($fracoes) {
            if ($fracoes[$a] == $fracoes[$b]) { return $a <=> $b; }
            return $fracoes[$b] <=> $fracoes[$a];
        });
        $restante = $centavos - array_sum($rateio);
        foreach ($fracoes as $id => $fracao) {
            if ($restante-- <= 0) { break; }
            $rateio[$id]++;
        }
        return $rateio;
    }

    private function extensao($notaId)
    {
        $resultado = $this->executar("SELECT T013A_Id, T013A_T013_Id_Frete_NFS, T013A_JSON
                                      FROM T013A WHERE T013A_T013_Id = '{$notaId}' LIMIT 1 FOR UPDATE");
        $linha = mysql_fetch_assoc($resultado);
        $json = $linha ? json_decode($linha['T013A_JSON'], true) : array();
        return array('id' => $linha ? (int) $linha['T013A_Id'] : 0,
                     'destino' => $linha ? (int) $linha['T013A_T013_Id_Frete_NFS'] : 0,
                     'rateio' => isset($json['freteNFS']) ? $json['freteNFS'] : array());
    }

    /** O vínculo guarda a parcela de cada NFS para substituir um rateio anterior. */
    public function ratear($origemId, $destinoId, $forma, $valorEsperado)
    {
        global $g;
        if (!isset($g['c029Ids'][189])) { return 'Não possui permissão para alterar notas recebidas.'; }
        if (!is_scalar($origemId) || !is_scalar($destinoId) || !is_scalar($forma) || !is_scalar($valorEsperado)) {
            return 'Informe a NFS, a nota de produtos e a forma de rateio.';
        }
        $origemId = (int) $origemId;
        $destinoId = (int) $destinoId;
        $empresaId = (int) $g['empresaAtual'];
        if ($origemId <= 0 || $destinoId <= 0 || $origemId == $destinoId) {
            return 'Salve a NFS e selecione outra nota, de produtos, para ratear o frete.';
        }
        $transacao = false;
        try {
            $this->executar('START TRANSACTION');
            $transacao = true;
            // Descobre a nota anterior sem bloquear; a leitura é conferida após os locks ordenados.
            $consulta = $this->executar("SELECT T013A_T013_Id_Frete_NFS FROM T013A
                                         INNER JOIN T013 ON T013_Id = T013A_T013_Id
                                         WHERE T013A_T013_Id = '{$origemId}' AND T013_C004_Id = '{$empresaId}' LIMIT 1");
            $anterior = mysql_fetch_assoc($consulta);
            $anteriorId = $anterior ? (int) $anterior['T013A_T013_Id_Frete_NFS'] : 0;
            $ids = array_unique(array_filter(array($origemId, $destinoId, $anteriorId)));
            sort($ids, SORT_NUMERIC);
            $lista = implode(',', $ids);
            $consulta = $this->executar("SELECT T013_Id, T013_Tipo, T013_Numero_Nota_Fiscal, T013_Valor_Total,
                                                T013_Flag_Travar_Nota, T013_Flag_Financeiro_Finalizado,
                                                T013_Flag_Estoque_Finalizado, T013_Flag_Cancelada
                                           FROM T013 WHERE T013_C004_Id = '{$empresaId}' AND T013_Id IN ({$lista})
                                           ORDER BY T013_Id FOR UPDATE");
            $notas = array();
            while ($nota = mysql_fetch_assoc($consulta)) { $notas[(int) $nota['T013_Id']] = $nota; }
            if (count($notas) != count($ids)) {
                throw new \RuntimeException('As notas devem existir e pertencer à empresa atual.');
            }
            foreach ($notas as $nota) {
                if ($nota['T013_Flag_Cancelada'] == 'S' || $nota['T013_Flag_Travar_Nota'] == 'S' ||
                    $nota['T013_Flag_Estoque_Finalizado'] == 'S' || $nota['T013_Flag_Financeiro_Finalizado'] == 'S') {
                    throw new \RuntimeException('O rateio do frete NFS deve ser realizado antes da finalização das notas.');
                }
            }
            if ((int) $notas[$origemId]['T013_Tipo'] != 10 ||
                !in_array((int) $notas[$destinoId]['T013_Tipo'], array(1, 2, 3, 4, 5, 9), true)) {
                throw new \RuntimeException('A origem deve ser uma NFS e o destino uma nota de produtos.');
            }
            $valor = (int) round((float) $notas[$origemId]['T013_Valor_Total'] * 100);
            if (!is_numeric($valorEsperado) || $valor != (int) round((float) $valorEsperado * 100)) {
                throw new \RuntimeException('O valor da NFS foi alterado. Reabra a aba Frete e tente novamente.');
            }
            $extensao = $this->extensao($origemId);
            if ($extensao['destino'] != $anteriorId) {
                throw new \RuntimeException('O vínculo da NFS foi alterado. Reabra a aba Frete e tente novamente.');
            }
            $destinos = array_unique(array_filter(array($destinoId, $anteriorId)));
            $listaDestinos = implode(',', $destinos);
            $consulta = $this->executar("SELECT T014_Id, T014_T013_Id, T014_Quantidade, T014_Valor_Custo_Unitario
                                           FROM T014 WHERE T014_T013_Id IN ({$listaDestinos})
                                           AND T014_C004_Id = '{$empresaId}' ORDER BY T014_Id FOR UPDATE");
            $itens = array();
            $itensDestino = array();
            while ($item = mysql_fetch_assoc($consulta)) {
                $itens[(int) $item['T014_Id']] = $item;
                if ((int) $item['T014_T013_Id'] == $destinoId) { $itensDestino[] = $item; }
            }
            $rateio = self::distribuir($itensDestino, $valor, $forma);
            $parcelasAnteriores = isset($extensao['rateio']['itens']) ? $extensao['rateio']['itens'] : array();
            $afetados = array_unique(array_merge(array_keys($parcelasAnteriores), array_keys($rateio)));
            foreach ($afetados as $itemId) {
                $itemId = (int) $itemId;
                // Itens excluídos já tiveram sua extensão removida pelo fluxo padrão.
                if (!isset($itens[$itemId])) { continue; }
                $parcelaAnterior = isset($parcelasAnteriores[$itemId]) ? (int) $parcelasAnteriores[$itemId] : 0;
                $parcelaNova = isset($rateio[$itemId]) ? $rateio[$itemId] : 0;
                $this->executar("INSERT INTO T014A (T014A_T014_Id, T014A_Valor_Frete_NFS)
                                 SELECT '{$itemId}', 0 WHERE NOT EXISTS
                                 (SELECT 1 FROM T014A WHERE T014A_T014_Id = '{$itemId}')");
                $consultaItem = $this->executar("SELECT T014A_Valor_Frete_NFS FROM T014A
                                                  WHERE T014A_T014_Id = '{$itemId}' LIMIT 1 FOR UPDATE");
                $extensaoItem = mysql_fetch_assoc($consultaItem);
                $atual = (int) round((float) $extensaoItem['T014A_Valor_Frete_NFS'] * 100);
                $novo = $atual - $parcelaAnterior + $parcelaNova;
                if ($novo < 0) {
                    throw new \RuntimeException('O frete NFS de um item está divergente. Verifique o rateio anterior.');
                }
                $novo = number_format($novo / 100, 2, '.', '');
                $this->executar("UPDATE T014A SET T014A_Valor_Frete_NFS = '{$novo}' WHERE T014A_T014_Id = '{$itemId}'");
                $this->executar("CALL T014_Gravar_Totalizacao_Custo('{$itemId}')");
            }
            $dados = array('valorCentavos' => $valor, 'forma' => (string) $forma, 'itens' => $rateio,
                           'usuarioId' => (int) $g['usuarioAtual'], 'data' => date('Y-m-d H:i:s'));
            $json = mysql_real_escape_string(json_encode($dados));
            if (!$extensao['id']) {
                $this->executar("INSERT INTO T013A (T013A_T013_Id) VALUES ('{$origemId}')");
            }
            $this->executar("UPDATE T013A SET T013A_T013_Id_Frete_NFS = '{$destinoId}',
                                  T013A_JSON = JSON_SET(COALESCE(T013A_JSON, JSON_OBJECT()), '$.freteNFS', CAST('{$json}' AS JSON))
                                  WHERE T013A_T013_Id = '{$origemId}'");
            $this->executar("UPDATE T013 SET T013_Flag_Frete = '4', T013_Flag_Rateio_Frete = '{$forma}' WHERE T013_Id = '{$origemId}'");
            foreach ($destinos as $notaId) { $this->executar("CALL T013_Gravar_Totalizacao('{$notaId}')"); }
            $this->executar('COMMIT');
            return array('valor' => $valor / 100, 'numeroNota' => $notas[$destinoId]['T013_Numero_Nota_Fiscal']);
        } catch (\Exception $erro) {
            if ($transacao) { mysql_query('ROLLBACK', false); }
            return $erro->getMessage();
        }
    }
}
