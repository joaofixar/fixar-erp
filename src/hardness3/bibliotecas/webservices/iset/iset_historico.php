<?php
namespace hardness;

class iset_historico_
{
    public function ultimosPedidosImportados($empresaId, $limite = 5)
    {
        $empresaId = (int) $empresaId;
        $limite = max(1, min(200, (int) $limite));
        $sql = "SELECT T005.T005_Id,T005.T005_Id_Pedido_Externo,T005.T005_Data_Emissao,T005.T005_Valor_Total,T005.T005_Valor_Frete,T005.T005_Valor_Desconto,COALESCE(NULLIF(D024.D024_Nome_Empresa,''),NULLIF(D024.D024_Nome_Fantasia,''),CONCAT('Cliente #',T005.T005_D024_Id)) AS Cliente,COUNT(T006.T006_Id) AS Quantidade_Itens,COALESCE(SUM(T006.T006_Quantidade),0) AS Quantidade_Produtos FROM T005 LEFT JOIN D024 ON D024.D024_Id=T005.T005_D024_Id LEFT JOIN T006 ON T006.T006_T005_Id=T005.T005_Id WHERE T005.T005_C004_Id='{$empresaId}' AND T005.T005_Flag_Plataforma_Ecommerce='7' AND T005.T005_Id_Pedido_Externo IS NOT NULL AND T005.T005_Id_Pedido_Externo<>'' GROUP BY T005.T005_Id,T005.T005_Id_Pedido_Externo,T005.T005_Data_Emissao,T005.T005_Valor_Total,T005.T005_Valor_Frete,T005.T005_Valor_Desconto,T005.T005_D024_Id,D024.D024_Nome_Empresa,D024.D024_Nome_Fantasia ORDER BY T005.T005_Id DESC LIMIT {$limite}";
        return $this->linhas($sql);
    }

    public function logs($empresaId, array $filtros = array(), $limite = 50)
    {
        $empresaId = (int) $empresaId;
        $limite = max(1, min(200, (int) $limite));
        $where = array("INT001_C004_Id='{$empresaId}'", "INT001_Integracao='ISET'");
        $temFiltro = false;
        foreach (array('status' => 'INT001_Status', 'operacao' => 'INT001_Operacao') as $chave => $campo) {
            if (!empty($filtros[$chave])) {
                $where[] = $campo . "='" . mysql_real_escape_string($filtros[$chave]) . "'";
                $temFiltro = true;
            }
        }
        foreach (array('inicio' => '>=', 'fim' => '<=') as $chave => $operador) {
            if (!empty($filtros[$chave]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filtros[$chave])) {
                $where[] = "INT001_Data{$operador}'" . mysql_real_escape_string($filtros[$chave]) . "'";
                $temFiltro = true;
            }
        }
        if (!empty($filtros['produto'])) {
            $where[] = "INT001_Tabela_Principal='D009' AND INT001_Id_Principal='" . (int) $filtros['produto'] . "'";
            $temFiltro = true;
        }
        $whereSql = implode(' AND ', $where);
        $sqlINT001 = <<<SQL
            SELECT *
            FROM INT001
            WHERE {$whereSql}
            ORDER BY INT001_Data DESC,
                INT001_Hora DESC,
                INT001_Id DESC
SQL;

        // Sem filtro, a tela mostra somente os últimos registros para evitar
        // carregar todo o histórico. Qualquer filtro deve retornar o conjunto completo.
        if (!$temFiltro) {
            $sqlINT001 .= <<<SQL

            LIMIT {$limite}
SQL;
        }

        return $this->linhas($sqlINT001);
    }

    /** Retorna somente a sincronização mais recente de cada produto da empresa. */
    public function ultimasSincronizacoesProdutos($empresaId)
    {
        $empresaId = (int) $empresaId;
        if ($empresaId <= 0) return array();
        $sqlINT005 = <<<SQL
            SELECT INT005.*
            FROM INT005
                INNER JOIN (
                    SELECT INT005_D009_Id,
                        MAX(INT005_Id) AS INT005_Id
                    FROM INT005
                    WHERE INT005_C004_Id = '{$empresaId}'
                    GROUP BY INT005_D009_Id
                ) AS Ultima_Sincronizacao ON Ultima_Sincronizacao.INT005_Id = INT005.INT005_Id
            WHERE INT005.INT005_C004_Id = '{$empresaId}'
            ORDER BY INT005.INT005_Data_Hora_Sincronizacao DESC,
                INT005.INT005_Id DESC
SQL;
        return $this->linhas($sqlINT005);
    }

    /**
     * Agrupa somente os eventos que pertencem à automação ISET. A tela não
     * consulta INT001 diretamente e não precisa inferir qual operação contém
     * o resultado final da importação.
     */
    public function resumoAutomacao($empresaId, $limite = 100)
    {
        $empresaId = (int) $empresaId;
        $limite = max(20, min(200, (int) $limite));
        $operacaoWebhook = mysql_real_escape_string(INT_OPERACAO_WEBHOOK);
        $operacaoImportacao = mysql_real_escape_string(INT_OPERACAO_IMPORTACAO_AUTOMATICA);
        $sql = "SELECT *
                  FROM INT001
                 WHERE INT001_C004_Id = '{$empresaId}'
                   AND INT001_Integracao = 'ISET'
                   AND INT001_Operacao IN ('{$operacaoWebhook}', '{$operacaoImportacao}')
                 ORDER BY INT001_Data DESC, INT001_Hora DESC, INT001_Id DESC
                 LIMIT {$limite}";
        $eventos = $this->linhas($sql);
        $resumo = array(
            'ultimoWebhook' => null,
            'ultimoPedidoProcessado' => null,
            'ultimaImportacaoAutomatica' => null,
            'ultimoErro' => null,
            'ultimoPagamentoPendente' => null,
            'ultimoPedidoDuplicado' => null,
            'eventos' => array_slice($eventos, 0, 20),
        );

        foreach ($eventos as $evento) {
            $operacao = isset($evento['INT001_Operacao']) ? $evento['INT001_Operacao'] : '';
            $status = isset($evento['INT001_Status']) ? $evento['INT001_Status'] : '';
            $etapa = isset($evento['INT001_Etapa']) ? $evento['INT001_Etapa'] : '';

            if ($resumo['ultimoWebhook'] === null) {
                $resumo['ultimoWebhook'] = $evento;
            }
            if ($resumo['ultimoPedidoProcessado'] === null && $operacao === INT_OPERACAO_IMPORTACAO_AUTOMATICA && $status !== INT_STATUS_INFO) {
                $resumo['ultimoPedidoProcessado'] = $evento;
            }
            if ($resumo['ultimaImportacaoAutomatica'] === null && $operacao === INT_OPERACAO_IMPORTACAO_AUTOMATICA && in_array($status, array(INT_STATUS_SUCESSO, INT_STATUS_AVISO), true)) {
                $resumo['ultimaImportacaoAutomatica'] = $evento;
            }
            if ($resumo['ultimoErro'] === null && $status === INT_STATUS_ERRO) {
                $resumo['ultimoErro'] = $evento;
            }
            if ($resumo['ultimoPagamentoPendente'] === null && $operacao === INT_OPERACAO_WEBHOOK && $status === INT_STATUS_AVISO && $etapa === 'PAGAMENTO_PENDENTE') {
                $resumo['ultimoPagamentoPendente'] = $evento;
            }
            if ($resumo['ultimoPedidoDuplicado'] === null && $operacao === INT_OPERACAO_WEBHOOK && $status === INT_STATUS_AVISO && $etapa === 'PEDIDO_JA_IMPORTADO') {
                $resumo['ultimoPedidoDuplicado'] = $evento;
            }
        }

        return $resumo;
    }

    private function linhas($sql)
    {
        $resultado = mysql_query($sql);
        $linhas = array();
        if ($resultado) while ($linha = mysql_fetch_assoc($resultado)) $linhas[] = $linha;
        return $linhas;
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_HISTORICO');
}

if (!class_exists(__NAMESPACE__ . '\\iset_historico', false)) {
    class_alias(__NAMESPACE__ . '\\iset_historico_', __NAMESPACE__ . '\\iset_historico');
}
