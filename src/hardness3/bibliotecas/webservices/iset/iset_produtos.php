<?php
namespace hardness;

require_once __DIR__ . '/iset_cliente.php';
require_once __DIR__ . '/iset_logs.php';
require_once __DIR__ . '/iset_consultas_produtos.php';
require_once __DIR__ . '/iset_produtos_controle.php';

/** Catálogo ISET configurável. As origens do JSON são sempre resolvidas por uma lista fechada. */
class iset_produtos_
{
    const POR_PAGINA = 50;
    private $permitirCompraPorEstoque = null;
    private $cacheConsultasDisponivel = null;

    /**
     * Consulta um único produto relido para a empresa informada e atualiza somente
     * o cache de consulta. Não sincroniza nem altera o vínculo INT003.
     */
    public function consultarProdutoNaIset($empresaId, $d009Id, $tentativa = '')
    {
        $empresaId = (int) $empresaId;
        $d009Id = (int) $d009Id;
        if ($empresaId <= 0 || $d009Id <= 0) {
            return array('estado' => ISET_CONSULTAS_PRODUTOS::ERRO, 'statusHttp' => 0, 'idIset' => '', 'sku' => '', 'mensagem' => 'Produto Hardness inválido para consulta.');
        }
        $configuracao = (new ISET_CONFIGURACOES())->obterProdutosConfiguracao();
        if (!$configuracao['ok']) {
            return array('estado' => ISET_CONSULTAS_PRODUTOS::ERRO, 'statusHttp' => 0, 'idIset' => '', 'sku' => '', 'mensagem' => 'Configuração de produtos inválida.');
        }
        $porId = $this->produtosPorD009($empresaId, array($d009Id), $this->temCampoImagens($configuracao['campos']));
        if (!isset($porId[$d009Id])) {
            return array('estado' => ISET_CONSULTAS_PRODUTOS::ERRO, 'statusHttp' => 0, 'idIset' => '', 'sku' => '', 'mensagem' => 'Produto não pertence à empresa atual ou não foi encontrado.');
        }
        $produto = $this->prepararProduto($porId[$d009Id], $configuracao['campos']);
        $produto['tentativa'] = $tentativa ?: bin2hex(random_bytes(16));
        try {
            $resultado = $this->consultasProdutos()->consultarProduto($empresaId, $produto, $configuracao['campos']);
        } catch (\Throwable $erro) {
            $resultado = array('estado' => ISET_CONSULTAS_PRODUTOS::ERRO, 'statusHttp' => 0, 'idIset' => '', 'sku' => $produto['sku'], 'mensagem' => 'Falha inesperada ao consultar o produto na ISET.');
        }
        if (!$this->registrarConsultaProduto($empresaId, $produto, $resultado)) {
            $resultado['estado'] = ISET_CONSULTAS_PRODUTOS::ERRO;
            $resultado['mensagem'] = 'Não foi possível registrar o log da consulta.';
            $resultado['motivo'] = 'FALHA_LOG';
            error_log('ISET: falha no log de consulta D009 #' . $d009Id);
        }
        return $resultado;
    }

    /** Processa IDs independentemente; uma falha nunca interrompe o restante do lote. */
    public function consultarProdutosNaIset($empresaId, array $d009Ids)
    {
        $empresaId = (int) $empresaId;
        $ids = array_values(array_unique(array_filter(array_map('intval', $d009Ids), function ($id) { return $id > 0; })));
        $resumo = $this->resumoConsultaVazio();
        if ($empresaId <= 0 || !$ids) return $resumo;
        foreach ($ids as $d009Id) {
            try {
                $resultado = $this->consultarProdutoNaIset($empresaId, $d009Id);
            } catch (\Throwable $erro) {
                $resultado = array('estado' => ISET_CONSULTAS_PRODUTOS::ERRO, 'statusHttp' => 0, 'idIset' => '', 'sku' => '', 'mensagem' => 'Falha inesperada ao processar o produto.');
            }
            $estado = isset($resultado['estado']) ? $resultado['estado'] : ISET_CONSULTAS_PRODUTOS::ERRO;
            if ($estado === ISET_CONSULTAS_PRODUTOS::ENCONTRADO) $resumo['encontrados']++;
            elseif ($estado === ISET_CONSULTAS_PRODUTOS::NAO_ENCONTRADO) $resumo['naoEncontrados']++;
            else { $estado = ISET_CONSULTAS_PRODUTOS::ERRO; $resumo['erros']++; }
            $resumo['resultados'][] = array('D009_Id' => $d009Id, 'sku' => isset($resultado['sku']) ? (string) $resultado['sku'] : '', 'estadoConsulta' => $estado, 'mensagem' => isset($resultado['mensagem']) ? (string) $resultado['mensagem'] : '');
        }
        return $resumo;
    }

    /** Equivalente genérico da seleção de todos os produtos filtrados na tela. */
    public function consultarTodosFiltradosNaIset($empresaId, array $filtros, array $excluirD009Ids = array())
    {
        $empresaId = (int) $empresaId;
        $resumo = $this->resumoConsultaVazio();
        if ($empresaId <= 0) return $resumo;
        $excluir = array_flip(array_map('intval', $excluirD009Ids));
        $resultado = mysql_query($this->sqlProdutos($empresaId, $filtros, array(), 0, 0, true));
        if (!$resultado) return $resumo;
        $lote = array();
        while ($linha = mysql_fetch_assoc($resultado)) {
            $d009Id = (int) $linha['D009_Id'];
            if ($d009Id <= 0 || isset($excluir[$d009Id])) continue;
            $lote[] = $d009Id;
            if (count($lote) >= self::POR_PAGINA) { $this->somarResumoConsulta($resumo, $this->consultarProdutosNaIset($empresaId, $lote)); $lote = array(); }
        }
        if ($lote) $this->somarResumoConsulta($resumo, $this->consultarProdutosNaIset($empresaId, $lote));
        return $resumo;
    }

    public function listarParaPrevia($empresaId, array $filtros = array(), $pagina = 1, $incluirTotal = true)
    {
        $configuracao = (new ISET_CONFIGURACOES())->obterProdutosConfiguracao();
        $vazio = array('produtos' => array(), 'campos' => array(), 'paginacao' => array('pagina' => 1, 'porPagina' => self::POR_PAGINA, 'temMais' => false), 'totalFiltrados' => 0, 'totais' => $this->totaisVazios(), 'erro' => '');
        if (!$configuracao['ok']) {
            $vazio['erro'] = 'A configuração de produtos é inválida: ' . implode(' ', $configuracao['erros']);
            return $vazio;
        }
        $empresaId = (int) $empresaId;
        if ($empresaId <= 0) return $vazio;
        $pagina = max(1, (int) $pagina);
        $resultado = mysql_query($this->sqlProdutos($empresaId, $filtros, array(), self::POR_PAGINA + 1, ($pagina - 1) * self::POR_PAGINA));
        if (!$resultado) {
            $vazio['erro'] = 'Não foi possível carregar os produtos da empresa atual.';
            return $vazio;
        }
        $linhas = array(); $totais = $this->totaisVazios(); $temMais = false;
        while ($linha = mysql_fetch_assoc($resultado)) {
            if (count($linhas) >= self::POR_PAGINA) { $temMais = true; break; }
            $linhas[] = $linha;
        }
        $camposVisiveis = array_values(array_filter($configuracao['campos'], function ($campo) { return $campo['visivel']; }));
        if ($this->temCampoImagens($configuracao['campos'])) $this->adicionarImagensProdutos($linhas);
        $cacheConsultas = $this->carregarCacheConsultasEmLote($empresaId, $linhas);
        $produtos = array();
        foreach ($linhas as $linha) {
            $produto = $this->prepararProduto($linha, $configuracao['campos']);
            $comparacao = $this->prepararComparacaoCampos($produto, $camposVisiveis, isset($cacheConsultas[(int) $produto['D009_Id']]) ? $cacheConsultas[(int) $produto['D009_Id']] : null);
            $produto['comparacaoCampos'] = $comparacao['campos'];
            $produto['estadoConsulta'] = $comparacao['estadoConsulta'];
            $produto['consultadoEm'] = $comparacao['consultadoEm'];
            $produto['acao'] = $this->acaoPreviaProduto($produto['bloqueios'], $produto['estadoConsulta']);
            if ($produto['acao'] === 'CRIAR' && ($produto['id_iset'] > 0 || $this->controle()->controles($empresaId, $produto['D009_Id'], $produto['skuIset']))) {
                $produto['acao'] = 'BLOQUEAR';
                $produto['bloqueios'][] = 'Existe tentativa ou cadastro anterior. Nova criação bloqueada; a consulta à ISET permanece disponível.';
            }
            $produtos[] = $produto;
            if ($produto['bloqueios']) $totais['bloqueados']++;
            elseif ($produto['acao'] === 'ATUALIZAR') $totais['atualizados']++;
            elseif ($produto['acao'] === 'CRIAR') $totais['novos']++;
        }
        return array('produtos' => $produtos, 'campos' => $camposVisiveis, 'paginacao' => array('pagina' => $pagina, 'porPagina' => self::POR_PAGINA, 'temMais' => $temMais), 'totalFiltrados' => $incluirTotal ? $this->contarFiltrados($empresaId, $filtros) : 0, 'totais' => $totais, 'erro' => '');
    }

    /** Sincroniza o conjunto filtrado em blocos, sem materializar todos os produtos na memória. */
    public function sincronizarTodosFiltrados($empresaId, array $filtros, array $excluirD009Ids = array())
    {
        $empresaId = (int) $empresaId;
        $excluir = array_flip(array_map('intval', $excluirD009Ids));
        $resumo = array('criados' => 0, 'atualizados' => 0, 'ignorados' => 0, 'falhos' => 0, 'itens' => array());
        if ($empresaId <= 0) return $resumo;
        $resultado = mysql_query($this->sqlProdutos($empresaId, $filtros, array(), 0, 0, true));
        if (!$resultado) return $resumo;
        $lote = array();
        while ($linha = mysql_fetch_assoc($resultado)) {
            $id = (int) $linha['D009_Id'];
            if ($id <= 0 || isset($excluir[$id])) continue;
            $lote[] = $id;
            if (count($lote) >= self::POR_PAGINA) { $this->somarResumo($resumo, $this->sincronizarSelecionados($empresaId, $lote)); $lote = array(); }
        }
        if ($lote) $this->somarResumo($resumo, $this->sincronizarSelecionados($empresaId, $lote));
        return $resumo;
    }

    /** O cron e a tela adquirem a mesma pendência antes de reler e enviar o produto. */
    public function sincronizarSelecionados($empresaId, array $d009Ids, $automatico = false)
    {
        $empresaId = (int) $empresaId;
        $ids = array_values(array_unique(array_filter(array_map('intval', $d009Ids), function ($id) { return $id > 0; })));
        $resumo = array('criados' => 0, 'atualizados' => 0, 'ignorados' => 0, 'falhos' => 0, 'itens' => array());
        if ($empresaId <= 0) return $resumo;
        foreach ($ids as $d009Id) {
            $INT006 = null;
            $produto = array('D001_Id' => 0, 'D049_Id' => 0, 'D009_Id' => $d009Id, 'sku' => '', 'payload' => array(), 'id_iset' => 0, 'origem' => $automatico ? 'AUTOMATICO' : 'MANUAL');
            try {
                $this->controle()->verificarDisponibilidade();
                // IDs recebidos da tela nunca podem criar pendências de outra empresa.
                $sqlD009 = <<<SQL
                    SELECT D009_Id FROM D009
                    WHERE D009_Id = '{$d009Id}' AND D009_C004_Id = '{$empresaId}'
SQL;
                $consulta = mysql_query($sqlD009);
                if (!$consulta || !mysql_fetch_assoc($consulta)) throw new \Exception('Produto não encontrado na empresa informada ou falha ao consultar D009.');
                if (!$this->controle()->marcarPendente($empresaId, $d009Id, false)) throw new \Exception('Não foi possível registrar a pendência ISET.');
                $INT006 = $this->controle()->adquirir($empresaId, $d009Id, $automatico);
                if (!$INT006) continue;
                $produto['tentativa'] = $INT006['INT006_Token_Processamento'];
                $produto['fila'] = $INT006;
                $configuracao = (new ISET_CONFIGURACOES())->obterProdutosConfiguracao();
                if (!$configuracao['ok']) {
                    $resultado = $this->pendencia('CADASTRO_INVALIDO', 'Configuração de produtos inválida.', 'VALIDAR');
                } else {
                    $porId = $this->produtosPorD009($empresaId, array($d009Id), $this->temCampoImagens($configuracao['campos']));
                    if (!isset($porId[$d009Id])) {
                        $sqlD001 = <<<SQL
                            SELECT D001_Id, D001_Flag_Ecommerce FROM D009
                                LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                            WHERE D009_Id = '{$d009Id}' AND D009_C004_Id = '{$empresaId}'
SQL;
                        $consulta = mysql_query($sqlD001);
                        if (!$consulta || !($D001 = mysql_fetch_assoc($consulta)) || empty($D001['D001_Id']) || $D001['D001_Flag_Ecommerce'] === 'S') throw new \Exception('Cadastro do produto inconsistente; pendência preservada.');
                        $resultado = array('resultado' => 'ignorados', 'acao' => 'VALIDAR', 'motivo' => 'INELEGIVEL', 'idIset' => 0, 'statusHttp' => 0, 'mensagem' => 'Produto não está mais elegível para e-commerce; pendência encerrada sem chamada à ISET.');
                    } else {
                        $produto = array_merge($produto, $this->prepararProduto($porId[$d009Id], $configuracao['campos']));
                        $resultado = $this->sincronizarProduto($empresaId, $produto, $automatico);
                    }
                }
            } catch (\Throwable $erro) {
                $motivo = 'FALHA_CONSULTA';
                if (!empty($produto['skuIset'])) {
                    try {
                        foreach ($this->controle()->controles($empresaId, $d009Id, $produto['skuIset']) as $INT007) {
                            if ($INT007['INT007_Estado'] !== 'EXISTENCIA_CONFIRMADA') $motivo = 'CRIACAO_INCERTA';
                        }
                    } catch (\Throwable $ignorado) { /* O diagnóstico original deve ser preservado se o banco também falhou. */ }
                }
                $resultado = $this->pendencia($motivo, integracoesMascararTextoSensivel($erro->getMessage()), 'FALHA');
            }
            $resultado['tentativa'] = isset($produto['tentativa']) ? $produto['tentativa'] : '';
            if (!$this->registrarResultado($empresaId, $produto, $resultado)) {
                $resultado = array_merge($resultado, $this->pendencia('FALHA_LOG', 'Não foi possível registrar a conclusão. Pendência preservada.', $resultado['acao'], $resultado['statusHttp'], $resultado['idIset']));
                error_log('ISET: falha no log final D009 #' . $d009Id . ' tentativa=' . $resultado['tentativa']);
            }
            if ($INT006) {
                try { $this->controle()->finalizar($INT006, $resultado); }
                catch (\Throwable $erro) {
                    $resultado = array_merge($resultado, $this->pendencia('FALHA_FILA', $erro->getMessage(), 'FALHA'));
                    error_log('ISET: falha na fila D009 #' . $d009Id . ': ' . $erro->getMessage());
                    $this->registrarResultado($empresaId, $produto, $resultado);
                }
            }
            $resumo[$resultado['resultado']]++;
            $resumo['itens'][] = array_merge($resultado, array('d009Id' => $d009Id, 'sku' => $produto['sku']));
        }
        return $resumo;
    }

    protected function sincronizarProduto($empresaId, array $produto, $automatico = false)
    {
        if ($produto['bloqueios']) return $this->pendencia('CADASTRO_INVALIDO', implode(' ', $produto['bloqueios']), 'VALIDAR');
        $cliente = $this->cliente();
        $sku = $produto['skuIset'];
        $consulta = $cliente->buscarProdutoPorSku($sku);
        $this->registrarChamada($empresaId, $produto, 'CONSULTA_PRODUTO', $consulta);
        $consultasProdutos = $this->consultasProdutos();
        $validacao = $consultasProdutos->validarRespostaProduto($consulta, $sku);
        $INT007s = $this->controle()->controles($empresaId, $produto['D009_Id'], $sku);
        if (!$this->registrarEvento($empresaId, $produto, 'VALIDACAO_PRODUTO', INT_STATUS_INFO, $validacao['ok'] ? 'Identidade remota validada.' : $validacao['mensagem'], array('controlesCriacao' => $INT007s))) throw new \Exception('Falha ao registrar a validação do produto.');
        if (!$validacao['ok']) {
            if (!$cliente->ausenciaProdutoConfirmada($consulta, $sku)) {
                return $this->pendencia(isset($validacao['motivo']) ? $validacao['motivo'] : 'FALHA_CONSULTA', $validacao['mensagem'], 'CONSULTAR', $consulta['status']);
            }
            if ($INT007s || $produto['id_iset'] > 0 || $this->controle()->temHistorico($empresaId, $produto['D009_Id'])) {
                return $this->pendencia('CRIACAO_INCERTA', 'O SKU não foi localizado, mas existe tentativa ou existência anterior. Nova criação bloqueada.', 'CONSULTAR', $consulta['status'], $produto['id_iset']);
            }
            $this->controle()->verificarDisponibilidade();
            if (!$this->controle()->possui($produto['fila'])) throw new \Exception('Posse da pendência perdida antes da criação.');
            $this->controle()->reservarCriacao($empresaId, $produto['D009_Id'], $sku, $produto['tentativa']);
            try {
                $this->registrarInicioEnvio($empresaId, $produto, 'CRIAR', 0);
                $resposta = $cliente->inserirProduto($produto['payloadIset']);
                $this->registrarChamada($empresaId, $produto, 'RESPOSTA_CRIACAO', $resposta);
                $idIset = $this->extrairIdIset($resposta['dados']);
                if (!$resposta['ok'] || $idIset <= 0) return $this->pendencia('CRIACAO_INCERTA', 'Tentativa de criação sem confirmação do produto e do vínculo. Nova criação bloqueada; consulte o diagnóstico de envio.', 'CRIAR', $resposta['status']);
                return $this->confirmarEnvio($empresaId, $produto, $cliente, $idIset, 'CRIAR', null);
            } finally {
                $this->controle()->marcarIncerto($empresaId, $sku, $produto['tentativa']);
            }
        }
        $idIset = (int) $validacao['produto']['id'];
        if ($produto['id_iset'] > 0 && $produto['id_iset'] !== $idIset) return $this->pendencia('CONFLITO_VINCULO', 'O ID encontrado não corresponde ao vínculo local.', 'CONSULTAR', $consulta['status'], $idIset);
        foreach ($INT007s as $INT007) {
            if ((int) $INT007['INT007_D009_Id'] !== (int) $produto['D009_Id'] || ((int) $INT007['INT007_Id_Produto_ISET'] > 0 && (int) $INT007['INT007_Id_Produto_ISET'] !== $idIset)) {
                return $this->pendencia('CONFLITO_VINCULO', 'O produto encontrado conflita com o controle de criação anterior.', 'CONSULTAR', $consulta['status'], $idIset);
            }
        }
        $sqlINT003 = <<<SQL
            SELECT INT003_D009_Id FROM INT003
            WHERE INT003_C004_Id = '{$empresaId}' AND INT003_Id_Produto_ISET = '{$idIset}'
SQL;
        $vinculo = mysql_query($sqlINT003);
        if (!$vinculo) throw new \Exception('Falha consultando vínculo do ID remoto.');
        if (($INT003 = mysql_fetch_assoc($vinculo)) && (int) $INT003['INT003_D009_Id'] !== (int) $produto['D009_Id']) return $this->pendencia('CONFLITO_VINCULO', 'O ID remoto pertence a outro D009.', 'CONSULTAR', 200, $idIset);
        if (!$consultasProdutos->salvarProdutoValidado($empresaId, $produto, $validacao['produto'], $consulta['status'], $produto['campos'])) {
            return $this->pendencia('ATUALIZACAO_PENDENTE', 'O produto foi encontrado na ISET, mas o cache da consulta não pôde ser atualizado.', 'CONSULTAR', $consulta['status'], $idIset);
        }
        $this->controle()->confirmarExistencia($empresaId, $produto['D009_Id'], $sku, $idIset);
        if (!$this->produtoIsetTemDiferencas($produto, $validacao['produto'])) {
            if (!$this->salvarVinculo($empresaId, $produto, $idIset, INT_STATUS_SUCESSO)) return $this->pendencia('ATUALIZACAO_PENDENTE', 'Produto equivalente, mas vínculo não pôde ser salvo.', 'SEM_ALTERACAO', 200, $idIset);
            return array('resultado' => 'ignorados', 'acao' => 'SEM_ALTERACAO', 'motivo' => 'CONCLUIDO', 'statusHttp' => 200, 'idIset' => $idIset, 'mensagem' => 'Produto equivalente e vínculo confirmado.');
        }
        try {
            $this->registrarInicioEnvio($empresaId, $produto, 'ATUALIZAR', $idIset);
            $resposta = $cliente->atualizarProduto($idIset, $produto['payloadIset']);
            $this->registrarChamada($empresaId, $produto, 'RESPOSTA_ATUALIZACAO', $resposta);
            if (!$resposta['ok']) return $this->pendencia('ATUALIZACAO_PENDENTE', 'Atualização não confirmada. ' . $this->mensagemErroIset($resposta), 'ATUALIZAR', $resposta['status'], $idIset);
            return $this->confirmarEnvio($empresaId, $produto, $cliente, $idIset, 'ATUALIZAR', $validacao['produto']);
        } catch (\Throwable $erro) {
            return $this->pendencia('ATUALIZACAO_PENDENTE', integracoesMascararTextoSensivel($erro->getMessage()), 'ATUALIZAR', 0, $idIset);
        }
    }

    private function confirmarEnvio($empresaId, array $produto, $cliente, $idIset, $acao, $anterior)
    {
        $consulta = $cliente->buscarProdutoPorId($idIset);
        $this->registrarChamada($empresaId, $produto, 'CONFIRMACAO_PRODUTO', $consulta);
        $consultasProdutos = $this->consultasProdutos();
        $validacao = $consultasProdutos->validarRespostaProduto($consulta, $produto['skuIset'], $idIset);
        $motivo = $acao === 'CRIAR' ? 'CRIACAO_INCERTA' : 'ATUALIZACAO_PENDENTE';
        if (!$validacao['ok']) return $this->pendencia($motivo, 'A leitura posterior não confirmou a identidade do produto. ' . $validacao['mensagem'], $acao, $consulta['status'], $idIset);
        if (!$consultasProdutos->salvarProdutoValidado($empresaId, $produto, $validacao['produto'], $consulta['status'], $produto['campos'])) {
            return $this->pendencia('ATUALIZACAO_PENDENTE', 'O produto foi confirmado na ISET, mas o cache da consulta não pôde ser atualizado.', $acao, $consulta['status'], $idIset);
        }
        $this->controle()->confirmarExistencia($empresaId, $produto['D009_Id'], $produto['skuIset'], $idIset);
        if ($this->produtoIsetTemDiferencas($produto, $validacao['produto'])) return $this->pendencia('ATUALIZACAO_PENDENTE', 'A leitura posterior ainda apresenta diferenças nos campos enviados.', $acao, $consulta['status'], $idIset);
        $this->controle()->verificarDisponibilidade();
        $sqlInicio = <<<SQL
            START TRANSACTION
SQL;
        $sqlCommit = <<<SQL
            COMMIT
SQL;
        $sqlRollback = <<<SQL
            ROLLBACK
SQL;
        if (!mysql_query($sqlInicio)) throw new \Exception('Não foi possível iniciar a confirmação local.');
        try {
            if (!$this->salvarVinculo($empresaId, $produto, $idIset, INT_STATUS_SUCESSO)) throw new \Exception('Falhou a gravação do vínculo.');
            if (!$this->salvarHistoricoSincronizacao($empresaId, $produto, $idIset, $acao, $anterior)) throw new \Exception('Falhou a gravação do histórico.');
            if (!mysql_query($sqlCommit)) throw new \Exception('Falhou a confirmação local da sincronização.');
        } catch (\Throwable $erro) {
            mysql_query($sqlRollback);
            return $this->pendencia('ATUALIZACAO_PENDENTE', $erro->getMessage(), $acao, $consulta['status'], $idIset);
        }
        return array('resultado' => $acao === 'CRIAR' ? 'criados' : 'atualizados', 'acao' => $acao, 'motivo' => 'CONCLUIDO', 'statusHttp' => $consulta['status'], 'idIset' => $idIset, 'mensagem' => 'Produto e vínculo confirmados após ' . ($acao === 'CRIAR' ? 'criação.' : 'atualização.'));
    }

    private function registrarInicioEnvio($empresaId, array $produto, $acao, $idIset)
    {
        $this->controle()->verificarDisponibilidade();
        if (!$this->controle()->possui($produto['fila'])) throw new \Exception('Posse da pendência perdida antes do envio.');
        if (!$this->registrarEvento($empresaId, $produto, 'INICIO_ENVIO', INT_STATUS_INFO, 'Envio autorizado após validações.', array('acao' => $acao, 'idIset' => $idIset, 'payload' => $produto['payloadIset']))) throw new \Exception('Falha no log de início. Nenhum envio será realizado.');
    }

    private function registrarChamada($empresaId, array $produto, $etapa, array $resposta)
    {
        $dados = isset($resposta['dados']['result']) && is_array($resposta['dados']['result']) ? $resposta['dados']['result'] : array();
        $candidatos = array();
        foreach ($dados as $item) if (is_array($item)) $candidatos[] = array('id' => isset($item['id']) ? $item['id'] : null, 'sku' => isset($item['cod_ref']) ? $item['cod_ref'] : null);
        $detalhes = array('requisicaoExecutada' => isset($resposta['requisicaoExecutada']) ? $resposta['requisicaoExecutada'] : !empty($resposta['diagnostico']['executada']), 'chamadas' => isset($resposta['chamadas']) ? $resposta['chamadas'] : array(), 'diagnostico' => isset($resposta['diagnostico']) ? $resposta['diagnostico'] : array('executada' => false), 'mensagem' => $resposta['mensagem'], 'candidatos' => $candidatos);
        if (!$this->registrarEvento($empresaId, $produto, $etapa, $resposta['ok'] ? INT_STATUS_INFO : INT_STATUS_ERRO, 'Resultado da chamada ISET.', $detalhes)) {
            error_log('ISET: falha registrando ' . $etapa . ' D009 #' . $produto['D009_Id'] . ' tentativa=' . $produto['tentativa']);
            throw new \Exception('Falha ao registrar resposta ISET. Pendência preservada.');
        }
    }

    protected function registrarEvento($empresaId, array $produto, $etapa, $status, $mensagem, array $detalhes)
    {
        return integracoesRegistrarLog(array('empresaId' => $empresaId, 'integracao' => 'ISET', 'operacao' => INT_OPERACAO_SINCRONIZAR_PRODUTO,
            'status' => $status, 'etapa' => $etapa, 'tabelaPrincipal' => 'D009', 'idPrincipal' => $produto['D009_Id'],
            'idExterno' => isset($detalhes['idIset']) ? $detalhes['idIset'] : '', 'mensagem' => $mensagem,
            'log' => array_merge(array('produtoHardness' => array('D001_Id' => $produto['D001_Id'], 'D049_Id' => $produto['D049_Id'], 'D009_Id' => $produto['D009_Id']),
                'skuHardness' => $produto['sku'], 'skuIset' => isset($produto['skuIset']) ? $produto['skuIset'] : '',
                'tentativa' => isset($produto['tentativa']) ? $produto['tentativa'] : '', 'origem' => isset($produto['origem']) ? $produto['origem'] : 'MANUAL', 'vinculoAnterior' => $produto['id_iset']), $detalhes)));
    }

    private function pendencia($motivo, $mensagem, $acao = 'CONSULTAR', $statusHttp = 0, $idIset = 0)
    {
        return array('resultado' => 'falhos', 'acao' => $acao, 'motivo' => $motivo, 'statusHttp' => (int) $statusHttp, 'idIset' => (int) $idIset, 'mensagem' => $mensagem);
    }

    public function marcarProdutoPendenteAutomatico($empresaId, $d009Id)
    {
        return $this->controle()->marcarPendente($empresaId, $d009Id);
    }

    public function sincronizarPendentesAutomaticos($empresaId, $limite = 50)
    {
        $empresaId = (int) $empresaId; $limite = max(1, min(200, (int) $limite));
        $resumo = array('processados' => 0, 'criados' => 0, 'atualizados' => 0, 'semAlteracao' => 0, 'ignorados' => 0, 'falhos' => 0);
        if ($empresaId <= 0) return $resumo;
        try {
            $this->controle()->verificarDisponibilidade();
            $sqlINT006 = <<<SQL
                SELECT INT006_D009_Id FROM INT006
                WHERE INT006_C004_Id = '{$empresaId}' AND INT006_Status IN ('P', 'E')
                    AND INT006_Proxima_Tentativa <= NOW()
                    AND (INT006_Flag_Processando <> 'S' OR INT006_Data_Hora_Processando < DATE_SUB(NOW(), INTERVAL 10 MINUTE))
                ORDER BY INT006_Proxima_Tentativa, INT006_Id LIMIT {$limite}
SQL;
            $consulta = mysql_query($sqlINT006);
            if (!$consulta) throw new \Exception('Erro ao carregar pendências ISET: ' . mysql_error());
            $ids = array();
            while ($INT006 = mysql_fetch_assoc($consulta)) $ids[] = (int) $INT006['INT006_D009_Id'];
            $resultado = $this->sincronizarSelecionados($empresaId, $ids, true);
            foreach ($resultado['itens'] as $item) {
                $resumo['processados']++;
                if ($item['resultado'] !== 'falhos' && $item['acao'] === 'SEM_ALTERACAO') $resumo['semAlteracao']++;
                else $resumo[$item['resultado']]++;
            }
        } catch (\Throwable $erro) {
            $resumo['erro'] = $erro->getMessage();
            error_log('ISET: automático bloqueado: ' . $erro->getMessage());
        }
        return $resumo;
    }

    protected function controle() { return new ISET_PRODUTOS_CONTROLE(); }

    public static function situacoesPendencias()
    {
        return array('AGUARDANDO' => 'Aguardando sincronização', 'FALHA_CONSULTA' => 'Falha de consulta',
            'CRIACAO_INCERTA' => 'Criação com resultado incerto', 'ATUALIZACAO_PENDENTE' => 'Atualização pendente',
            'DUPLICIDADE' => 'Duplicidade encontrada', 'CONFLITO_VINCULO' => 'Conflito de vínculo',
            'CADASTRO_INVALIDO' => 'Cadastro inválido', 'FALHA_LOG' => 'Falha ao registrar log', 'FALHA_FILA' => 'Falha na fila');
    }

    public function listarPendencias($empresaId, array $filtros = array(), $pagina = 1)
    {
        $empresaId = (int) $empresaId; $pagina = max(1, (int) $pagina); $offset = ($pagina - 1) * self::POR_PAGINA;
        $where = array("INT006_C004_Id = '{$empresaId}'");
        foreach (array('codigo' => 'D001_Codigo_Produto', 'descricao' => 'D001_Descricao_Produto') as $campo => $coluna) {
            if (isset($filtros[$campo]) && trim($filtros[$campo]) !== '') $where[] = $coluna . " LIKE '%" . mysql_real_escape_string(trim($filtros[$campo])) . "%'";
        }
        if (!empty($filtros['situacao']) && isset(self::situacoesPendencias()[$filtros['situacao']])) $where[] = "INT006_Motivo = '" . mysql_real_escape_string($filtros['situacao']) . "'";
        $where = implode(' AND ', $where); $limite = self::POR_PAGINA + 1;
        $sqlINT006 = <<<SQL
            SELECT INT006.*, D001_Codigo_Produto, D001_Descricao_Produto,
                INT003_Id_Produto_ISET, INT003_SKU,
                COALESCE(INT003_Data_Hora_Ultima_Sincronizacao,
                    (SELECT MAX(INT005_Data_Hora_Sincronizacao) FROM INT005
                    WHERE INT005_C004_Id = INT006_C004_Id AND INT005_D009_Id = INT006_D009_Id)) AS Ultima_Sincronizacao,
                (SELECT MAX(INT007_Id_Produto_ISET) FROM INT007
                    WHERE INT007_C004_Id = INT006_C004_Id AND INT007_D009_Id = INT006_D009_Id) AS Id_ISET_Conhecido
            FROM INT006
                LEFT JOIN D009 ON D009_Id = INT006_D009_Id AND D009_C004_Id = INT006_C004_Id
                LEFT JOIN D049 ON D049_Id = D009_D049_Id
                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                LEFT JOIN INT003 ON INT003_C004_Id = INT006_C004_Id AND INT003_D009_Id = INT006_D009_Id
            WHERE {$where}
            ORDER BY INT006_Proxima_Tentativa, INT006_Id LIMIT {$offset}, {$limite}
SQL;
        $consulta = mysql_query($sqlINT006);
        if (!$consulta) throw new \Exception('Não foi possível carregar as pendências. Confira a migração de proteção ISET.');
        $linhas = array();
        while ($INT006 = mysql_fetch_assoc($consulta)) $linhas[] = $INT006;
        $temMais = count($linhas) > self::POR_PAGINA;
        return array('pendencias' => array_slice($linhas, 0, self::POR_PAGINA), 'pagina' => $pagina, 'temMais' => $temMais);
    }

    /** Apenas GET na ISET; o envio permanece responsabilidade da sincronização. */
    public function consultarPendencia($empresaId, $d009Id)
    {
        $empresaId = (int) $empresaId; $d009Id = (int) $d009Id;
        $this->controle()->verificarDisponibilidade();
        $INT006 = $this->controle()->adquirir($empresaId, $d009Id, false);
        if (!$INT006) return 'Pendência não encontrada nesta empresa ou já em processamento.';
        $produto = array('D001_Id' => 0, 'D049_Id' => 0, 'D009_Id' => $d009Id, 'sku' => '', 'skuIset' => '', 'id_iset' => 0, 'origem' => 'MANUAL', 'tentativa' => $INT006['INT006_Token_Processamento']);
        try {
            $resultado = $this->consultarProdutoNaIset($empresaId, $d009Id, $INT006['INT006_Token_Processamento']);
            $produto['sku'] = $resultado['sku'];
            $produto['skuIset'] = (new ISET_SKU())->paraIset($resultado['sku']);
            $motivo = isset($resultado['motivo']) ? $resultado['motivo'] : 'FALHA_CONSULTA';
            if ($resultado['estado'] === ISET_CONSULTAS_PRODUTOS::ENCONTRADO) {
                $sqlINT003 = <<<SQL
                    SELECT INT003_D009_Id, INT003_Id_Produto_ISET FROM INT003
                    WHERE INT003_C004_Id = '{$empresaId}' AND (INT003_D009_Id = '{$d009Id}' OR INT003_Id_Produto_ISET = '{$resultado['idIset']}')
SQL;
                $consulta = mysql_query($sqlINT003);
                if (!$consulta) throw new \Exception('Falha ao verificar vínculo após consulta.');
                $motivo = 'CONFLITO_VINCULO';
                while ($INT003 = mysql_fetch_assoc($consulta)) {
                    if ((int) $INT003['INT003_D009_Id'] !== $d009Id || (int) $INT003['INT003_Id_Produto_ISET'] !== (int) $resultado['idIset']) throw new \Exception('Conflito com o vínculo local; nenhum envio foi realizado.');
                }
                $this->controle()->confirmarExistencia($empresaId, $d009Id, (new ISET_SKU())->paraIset($resultado['sku']), (int) $resultado['idIset']);
                $motivo = 'FALHA_LOG';
                if (!$this->registrarEvento($empresaId, $produto, 'CONCLUSAO_CONSULTA', INT_STATUS_INFO, 'Identidade validada; pendência preparada para processamento futuro.', array('decisao' => 'AGENDAR', 'idIset' => (int) $resultado['idIset']))) throw new \Exception('Falha registrando conclusão da consulta.');
                $motivo = 'FALHA_FILA';
                $this->controle()->agendarAposConsulta($INT006);
                return 'Produto encontrado. Pendência preparada para sincronização; nenhum envio realizado.';
            }
            if ($resultado['estado'] === ISET_CONSULTAS_PRODUTOS::NAO_ENCONTRADO && $this->controle()->controles($empresaId, $d009Id, (new ISET_SKU())->paraIset($resultado['sku']))) $motivo = 'CRIACAO_INCERTA';
            if (!$this->registrarEvento($empresaId, $produto, 'CONCLUSAO_CONSULTA', INT_STATUS_ERRO, $resultado['mensagem'], array('decisao' => 'MANTER_PENDENCIA', 'motivo' => $motivo))) {
                $motivo = 'FALHA_LOG';
                throw new \Exception('Falha registrando conclusão da consulta.');
            }
            $this->controle()->finalizar($INT006, $this->pendencia($motivo, $resultado['mensagem']));
            return $resultado['mensagem'];
        } catch (\Throwable $erro) {
            if (!$this->registrarEvento($empresaId, $produto, 'CONCLUSAO_CONSULTA', INT_STATUS_ERRO, $erro->getMessage(), array('decisao' => 'MANTER_PENDENCIA', 'motivo' => isset($motivo) ? $motivo : 'FALHA_CONSULTA'))) error_log('ISET: falha no log de consulta D009 #' . $d009Id . ' tentativa=' . $produto['tentativa']);
            try { $this->controle()->finalizar($INT006, $this->pendencia(isset($motivo) ? $motivo : 'FALHA_CONSULTA', $erro->getMessage())); }
            catch (\Throwable $falhaFila) { error_log('ISET: falha finalizando consulta D009 #' . $d009Id . ': ' . $falhaFila->getMessage()); }
            throw $erro;
        }
    }

    private function produtoIsetTemDiferencas(array $produto, array $produtoIset)
    {
        $normalizador = $this->consultasProdutos();
        foreach (isset($produto['campos']) ? $produto['campos'] : array() as $campo) {
            if (empty($campo['enviar']) || !array_key_exists($campo['payload'], $produto['payloadIset'])) continue;
            if (!array_key_exists($campo['payload'], $produtoIset) || $produtoIset[$campo['payload']] === null) return true;
            $hardness = $produto['payloadIset'][$campo['payload']];
            $iset = array_key_exists($campo['payload'], $produtoIset) ? $produtoIset[$campo['payload']] : null;
            if ($campo['payload'] === 'cod_ref') {
                if (!(new ISET_SKU())->iguais($produto['sku'], $iset)) return true;
                continue;
            }
            if ($campo['tipo'] === 'imagens') {
                if (!$normalizador->imagensEquivalentes($hardness, $iset)) return true;
                continue;
            }
            if ($normalizador->normalizarValor($hardness, $campo['tipo']) !== $normalizador->normalizarValor($iset, $campo['tipo'])) return true;
        }
        return false;
    }

    private function prepararProduto(array $linha, array $campos)
    {
        $linha['id_iset'] = (int) $linha['INT003_Id_Produto_ISET']; $linha['bloqueios'] = array(); $linha['valores'] = array(); $linha['payload'] = array();
        $linha['_ISET_Quantidade_Configurada'] = 0;
        foreach ($campos as $campo) {
            if ($campo['payload'] !== 'quantity') continue;
            $quantidade = $this->valorCampo($campo, $linha);
            $linha['_ISET_Quantidade_Configurada'] = $quantidade['ok'] ? (float) $quantidade['valor'] : 0;
            break;
        }
        foreach ($campos as $campo) {
            $valor = $this->valorCampo($campo, $linha);
            $linha['valores'][$campo['id']] = $valor['ok'] ? $valor['valor'] : '';
            if (!$valor['ok']) {
                if ($campo['obrigatorio']) $linha['bloqueios'][] = $campo['titulo'] . ': ' . $valor['erro'];
                continue;
            }
            if ($campo['tipo'] === 'imagens') {
                $imagens = is_array($valor['valor']) ? $valor['valor'] : array();
                if ($campo['obrigatorio'] && !$imagens) $linha['bloqueios'][] = $campo['titulo'] . ' é obrigatório.';
                // A ISET rejeita images: []; quando não houver fotos, o campo opcional deve ser omitido.
                if ($campo['enviar'] && $imagens) $linha['payload'][$campo['payload']] = array_values(array_map(function ($imagem) { return $imagem['original']; }, $imagens));
                continue;
            }
            if ($campo['obrigatorio'] && $this->vazio($valor['valor'])) $linha['bloqueios'][] = $campo['titulo'] . ' é obrigatório.';
            if ($campo['enviar'] && !$this->vazio($valor['valor'])) $linha['payload'][$campo['payload']] = $valor['valor'];
        }
        $linha['sku'] = isset($linha['payload']['cod_ref']) ? trim((string) $linha['payload']['cod_ref']) : trim((string) $linha['D001_Codigo_Produto']);
        if ($linha['sku'] === '') $linha['bloqueios'][] = 'SKU para consulta não informado.';
        $linha['skuIset'] = (new ISET_SKU())->paraIset($linha['sku']);
        if (strlen($linha['skuIset']) > 100 || preg_match('/[[:cntrl:]]/', $linha['skuIset'])) $linha['bloqueios'][] = 'SKU inválido para controle da integração.';
        $linha['payloadIset'] = $linha['payload'];
        if (isset($linha['payloadIset']['cod_ref'])) $linha['payloadIset']['cod_ref'] = $linha['skuIset'];
        $linha['acao'] = $linha['bloqueios'] ? 'BLOQUEAR' : '';
        $linha['campos'] = $campos;
        return $linha;
    }

    /** A ação exibida na prévia só é definida pelo resultado da consulta ISET. */
    private function acaoPreviaProduto(array $bloqueios, $estadoConsulta)
    {
        if ($bloqueios) return 'BLOQUEAR';
        if ($estadoConsulta === ISET_CONSULTAS_PRODUTOS::ENCONTRADO) return 'ATUALIZAR';
        if ($estadoConsulta === ISET_CONSULTAS_PRODUTOS::NAO_ENCONTRADO) return 'CRIAR';
        return '';
    }

    private function valorCampo(array $campo, array $linha)
    {
        $origem = $campo['origem'];
        if ($origem === 'ISET_Status_Produto') $bruto = 1;
        elseif ($origem === 'ISET_Allow_Buy') $bruto = $this->controleCompraPorEstoqueAtivo() ? (float) $linha['_ISET_Quantidade_Configurada'] > 0 : true;
        else $bruto = isset($linha[$origem]) ? $linha[$origem] : null;
        if ($campo['tipo'] === 'imagens') return array('ok' => true, 'valor' => is_array($bruto) ? $bruto : array());
        if ($campo['tipo'] === 'texto') return array('ok' => true, 'valor' => trim((string) $bruto));
        if ($campo['tipo'] === 'booleano') return array('ok' => true, 'valor' => $this->booleano($bruto));
        if ($bruto === null || $bruto === '' || !is_numeric(str_replace(',', '.', (string) $bruto))) return array('ok' => false, 'valor' => null, 'erro' => 'valor numérico inválido.');
        $numero = (float) str_replace(',', '.', (string) $bruto);
        if ($campo['tipo'] === 'inteiro') {
            if ($origem === 'D009_Quantidade_Estoque_Liquido') $numero = max(0, floor($numero));
            else $numero = (int) $numero;
        }
        return array('ok' => true, 'valor' => $campo['tipo'] === 'inteiro' ? (int) $numero : $numero);
    }

    private function booleano($valor) { return is_bool($valor) ? $valor : in_array(strtoupper(trim((string) $valor)), array('1', 'S', 'SIM', 'TRUE', 'Y', 'YES'), true); }
    private function vazio($valor) { return $valor === null || $valor === ''; }
    private function controleCompraPorEstoqueAtivo() { if ($this->permitirCompraPorEstoque === null) $this->permitirCompraPorEstoque = (new ISET_CONFIGURACOES())->permitirCompraPorEstoqueAtivo(); return $this->permitirCompraPorEstoque; }
    private function temCampoImagens(array $campos) { foreach ($campos as $campo) if ($campo['tipo'] === 'imagens') return true; return false; }

    /** Busca as fotos de todo o lote em uma única consulta e nunca usa D001_Foto. */
    private function adicionarImagensProdutos(array &$linhas)
    {
        global $g, $confUsuario;
        if (!$linhas) return;
        $ids = array();
        foreach ($linhas as &$linha) { $id = isset($linha['D001_Id']) ? (int) $linha['D001_Id'] : 0; if ($id > 0) { $ids[$id] = true; $linha['T172_Imagens'] = array(); } }
        unset($linha);
        if (!$ids) return;
        $resultado = mysql_query('SELECT T172_Id,T172_D001_Id,T172_Nome_Arquivo FROM T172 WHERE T172_D001_Id IN (' . implode(',', array_keys($ids)) . ') ORDER BY T172_Nome_Arquivo DESC');
        if (!$resultado) return;
        $porProduto = array();
        while ($foto = mysql_fetch_assoc($resultado)) {
            $produtoId = (int) $foto['T172_D001_Id']; $fotoId = (int) $foto['T172_Id'];
            $extensaoArquivo = (string) pathinfo((string) $foto['T172_Nome_Arquivo'], PATHINFO_EXTENSION);
            $extensao = strtolower($extensaoArquivo);
            if ($produtoId <= 0 || $fotoId <= 0 || !preg_match('/^[a-z0-9]{1,10}$/i', $extensaoArquivo) || !in_array($extensao, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'avif'), true)) continue;
            $relativo = rtrim((string) $g['pathDados'], '/') . '/produtos/' . $produtoId . '/fotos/' . $fotoId . '.' . $extensaoArquivo;
            $arquivoOriginal = $this->arquivoDados($relativo, $confUsuario);
            if ($arquivoOriginal === '' || !@getimagesize($arquivoOriginal)) continue;
            $miniaturaRelativa = rtrim((string) $g['pathDados'], '/') . '/produtos/' . $produtoId . '/fotos/' . $fotoId . '-100.' . $extensaoArquivo;
            $arquivoMiniatura = $this->arquivoDados($miniaturaRelativa, $confUsuario);
            $urlBase = rtrim(is_array($confUsuario) && isset($confUsuario['urlRaiz']) ? (string) $confUsuario['urlRaiz'] : '', '/') . '/hardness3/';
            $urlOriginal = $urlBase . ltrim($relativo, '/');
            $porProduto[$produtoId][] = array('original' => $urlOriginal, 'miniatura' => $arquivoMiniatura !== '' ? $urlBase . ltrim($miniaturaRelativa, '/') : $urlOriginal);
        }
        foreach ($linhas as &$linha) { $linha['T172_Imagens'] = isset($porProduto[(int) $linha['D001_Id']]) ? $porProduto[(int) $linha['D001_Id']] : array(); }
        unset($linha);
    }

    private function arquivoDados($relativo, $confUsuario)
    {
        if (is_file($relativo)) return $relativo;
        global $g;
        $raiz = is_array($confUsuario) && isset($confUsuario['pathRaiz']) ? rtrim((string) $confUsuario['pathRaiz'], '/') : (isset($g['pathRaiz']) ? rtrim((string) $g['pathRaiz'], '/') : '');
        $arquivo = $raiz === '' ? '' : $raiz . '/' . ltrim($relativo, '/');
        return $arquivo !== '' && is_file($arquivo) ? $arquivo : '';
    }

    private function sqlProdutos($empresaId, array $filtros = array(), array $d009Ids = array(), $limite = 0, $offset = 0, $somenteIds = false)
    {
        $where = array("D009.D009_C004_Id='" . (int) $empresaId . "'", "D001.D001_Flag_Ecommerce='S'");
        foreach (array('codigo' => 'D001.D001_Codigo_Produto', 'descricao' => 'D001.D001_Descricao_Produto') as $chave => $coluna) {
            $valor = isset($filtros[$chave]) ? $filtros[$chave] : '';
            $valores = $this->valoresFiltro($valor);
            if ($valores) {
                $where[] = $coluna . ' IN (' . implode(',', array_map(function ($item) { return "'" . mysql_real_escape_string($item) . "'"; }, $valores)) . ')';
            } elseif (trim((string) $valor) !== '') {
                $where[] = $coluna . " LIKE '%" . mysql_real_escape_string(trim((string) $valor)) . "%'";
            }
        }
        if ($d009Ids) $where[] = 'D009.D009_Id IN (' . implode(',', array_map('intval', $d009Ids)) . ')';
        $campos = $somenteIds ? 'D009.D009_Id' : 'D001.D001_Id, D049.D049_Id, D009.D009_Id,
            D001.D001_Codigo_Produto, D001.D001_Descricao_Produto, D001.D001_Flag_Ecommerce,
            D009.D009_Preco_1, D009.D009_Quantidade_Estoque_Liquido,
            INT003.INT003_Id_Produto_ISET, INT003.INT003_SKU, INT005.INT005_Data_Hora_Sincronizacao';
        $where = implode(' AND ', $where);
        $limite = $limite > 0 ? 'LIMIT ' . (int) $offset . ', ' . (int) $limite : '';
        $sql = <<<SQL
            SELECT {$campos}
            FROM D009
                INNER JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
                INNER JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
                LEFT JOIN INT003 ON INT003.INT003_C004_Id = D009.D009_C004_Id AND INT003.INT003_D009_Id = D009.D009_Id
                LEFT JOIN INT005 ON INT005.INT005_Id = (
                    SELECT MAX(INT005_Historico.INT005_Id) FROM INT005 AS INT005_Historico
                    WHERE INT005_Historico.INT005_C004_Id = D009.D009_C004_Id AND INT005_Historico.INT005_D009_Id = D009.D009_Id
                )
            WHERE {$where}
            ORDER BY D001.D001_Codigo_Produto, D009.D009_Id {$limite}
SQL;
        return $sql;
    }

    /** Listas separadas por ponto e vírgula, vírgula ou quebra de linha usam correspondências exatas. */
    private function valoresFiltro($valor)
    {
        $valor = trim((string) $valor);
        if ($valor === '' || !preg_match('/[;,\r\n]/', $valor)) return array();
        $codigos = array(); $vistos = array();
        foreach (preg_split('/[;,\r\n]+/', $valor) as $codigo) {
            $codigo = trim($codigo);
            $chave = 'codigo:' . $codigo;
            if ($codigo === '' || isset($vistos[$chave])) continue;
            $vistos[$chave] = true;
            $codigos[] = $codigo;
        }
        return $codigos;
    }

    public function contarFiltrados($empresaId, array $filtros = array())
    {
        $sql = $this->sqlProdutos($empresaId, $filtros, array(), 0, 0, true);
        $sql = preg_replace('/^SELECT .*? FROM /', 'SELECT COUNT(*) AS total FROM ', $sql);
        $sql = preg_replace('/ ORDER BY .+$/', '', $sql);
        $resultado = mysql_query($sql); $linha = $resultado ? mysql_fetch_assoc($resultado) : array();
        return isset($linha['total']) ? (int) $linha['total'] : 0;
    }

    /** Uma consulta ao cache por página; a ausência da migration não quebra a prévia. */
    protected function carregarCacheConsultasEmLote($empresaId, array $linhas)
    {
        if ($this->cacheConsultasDisponivel === false) return array();
        $ids = array();
        foreach ($linhas as $linha) {
            $d009Id = isset($linha['D009_Id']) ? (int) $linha['D009_Id'] : 0;
            if ($d009Id > 0) $ids[$d009Id] = true;
        }
        if (!$ids) return array();
        $sql = "SELECT INT004_D009_Id,INT004_Encontrado,INT004_Consultado_Em,INT004_Valores_Normalizados_Json FROM INT004 WHERE INT004_C004_Id='" . (int) $empresaId . "' AND INT004_D009_Id IN (" . implode(',', array_keys($ids)) . ')';
        $resultado = @mysql_query($sql);
        if (!$resultado) { $this->cacheConsultasDisponivel = false; return array(); }
        $this->cacheConsultasDisponivel = true;
        $cache = array();
        while ($linha = mysql_fetch_assoc($resultado)) {
            $valores = json_decode(isset($linha['INT004_Valores_Normalizados_Json']) ? (string) $linha['INT004_Valores_Normalizados_Json'] : '', true);
            $estado = $this->estadoConsultaCache(isset($linha['INT004_Encontrado']) ? $linha['INT004_Encontrado'] : '');
            if ($estado === ISET_CONSULTAS_PRODUTOS::ENCONTRADO && !is_array($valores)) $estado = ISET_CONSULTAS_PRODUTOS::ERRO;
            $cache[(int) $linha['INT004_D009_Id']] = array(
                'estadoConsulta' => $estado,
                'consultadoEm' => isset($linha['INT004_Consultado_Em']) ? (string) $linha['INT004_Consultado_Em'] : null,
                'valores' => is_array($valores) ? $valores : array(),
            );
        }
        return $cache;
    }

    /** Prepara a comparação sem expor resposta bruta da ISET nem executar HTTP. */
    protected function prepararComparacaoCampos(array $produto, array $campos, $cache)
    {
        $estado = is_array($cache) && isset($cache['estadoConsulta']) ? $cache['estadoConsulta'] : 'NAO_CONSULTADO';
        $consultadoEm = is_array($cache) && !empty($cache['consultadoEm']) ? $cache['consultadoEm'] : null;
        $valoresIset = is_array($cache) && isset($cache['valores']) && is_array($cache['valores']) ? $cache['valores'] : array();
        $normalizador = $this->consultasProdutos(); $camposComparacao = array();
        foreach ($campos as $campo) {
            $id = $campo['id'];
            $hardness = array_key_exists($id, $produto['valores']) ? $produto['valores'][$id] : null;
            $iset = null; $diferente = false;
            if ($estado === ISET_CONSULTAS_PRODUTOS::ENCONTRADO) {
                $hardnessNormalizado = $normalizador->normalizarValor($hardness, $campo['tipo']);
                $iset = array_key_exists($id, $valoresIset) ? $normalizador->normalizarValor($valoresIset[$id], $campo['tipo']) : null;
                // cod_ref é o mesmo identificador nos dois sistemas, mas cada lado
                // mantém o separador próprio. A comparação não pode gerar pendência
                // apenas pela conversão pontual entre ponto e hífen.
                $diferente = $campo['payload'] === 'cod_ref'
                    ? !(new ISET_SKU())->iguais($hardness, $iset)
                    : ($campo['tipo'] === 'imagens'
                        ? !$normalizador->imagensEquivalentes($hardness, $iset)
                        : $hardnessNormalizado !== $iset);
            }
            $camposComparacao[$id] = array('hardness' => $hardness, 'iset' => $iset, 'diferente' => $diferente, 'estadoConsulta' => $estado, 'consultadoEm' => $consultadoEm);
        }
        return array('estadoConsulta' => $estado, 'consultadoEm' => $consultadoEm, 'campos' => $camposComparacao);
    }

    private function estadoConsultaCache($encontrado)
    {
        $encontrado = strtoupper(trim((string) $encontrado));
        if ($encontrado === 'S') return ISET_CONSULTAS_PRODUTOS::ENCONTRADO;
        if ($encontrado === 'N') return ISET_CONSULTAS_PRODUTOS::NAO_ENCONTRADO;
        return ISET_CONSULTAS_PRODUTOS::ERRO;
    }

    private function somarResumo(array &$destino, array $origem) { foreach (array('criados', 'atualizados', 'ignorados', 'falhos') as $campo) $destino[$campo] += isset($origem[$campo]) ? (int) $origem[$campo] : 0; if (!empty($origem['itens'])) $destino['itens'] = array_merge($destino['itens'], $origem['itens']); }
    private function resumoConsultaVazio() { return array('encontrados' => 0, 'naoEncontrados' => 0, 'erros' => 0, 'resultados' => array()); }
    private function somarResumoConsulta(array &$destino, array $origem) { foreach (array('encontrados', 'naoEncontrados', 'erros') as $campo) $destino[$campo] += isset($origem[$campo]) ? (int) $origem[$campo] : 0; if (!empty($origem['resultados'])) $destino['resultados'] = array_merge($destino['resultados'], $origem['resultados']); }

    private function produtosPorD009($empresaId, array $ids, $carregarImagens = false) { $resultado = mysql_query($this->sqlProdutos($empresaId, array(), $ids)); $produtos = array(); if (!$resultado) throw new \Exception('Falha consultando produtos da empresa.'); if ($resultado) while ($linha = mysql_fetch_assoc($resultado)) $produtos[(int) $linha['D009_Id']] = $linha; if (!$carregarImagens) return $produtos; $linhas = array_values($produtos); $this->adicionarImagensProdutos($linhas); foreach ($linhas as $linha) $produtos[(int) $linha['D009_Id']] = $linha; return $produtos; }

    protected function cliente() { return new ISET_CLIENTE(); }
    protected function consultasProdutos() { return new ISET_CONSULTAS_PRODUTOS(); }

    protected function salvarVinculo($empresaId, array $produto, $idIset, $status)
    {
        $empresaId = (int) $empresaId; $idIset = (int) $idIset; $d009Id = (int) $produto['D009_Id'];
        if ($empresaId <= 0 || $idIset <= 0 || $d009Id <= 0) return false;
        $sku = mysql_real_escape_string($produto['skuIset']);
        $status = mysql_real_escape_string($status);
        $sqlINT003 = <<<SQL
            SELECT INT003_D009_Id, INT003_Id_Produto_ISET FROM INT003
            WHERE INT003_C004_Id = '{$empresaId}'
                AND (INT003_Id_Produto_ISET = '{$idIset}' OR INT003_D009_Id = '{$d009Id}')
SQL;
        $consulta = mysql_query($sqlINT003);
        if (!$consulta) return false;
        while ($INT003 = mysql_fetch_assoc($consulta)) {
            if ((int) $INT003['INT003_D009_Id'] !== $d009Id || (int) $INT003['INT003_Id_Produto_ISET'] !== $idIset) return false;
        }
        $sqlINT003 = <<<SQL
            INSERT INTO INT003 (
                INT003_C004_Id, INT003_D001_Id, INT003_D049_Id, INT003_D009_Id,
                INT003_Id_Produto_ISET, INT003_SKU, INT003_Data_Hora_Ultima_Sincronizacao, INT003_Status_Ultima_Sincronizacao
            ) VALUES ('{$empresaId}', '{$produto['D001_Id']}', '{$produto['D049_Id']}', '{$d009Id}',
                '{$idIset}', '{$sku}', NOW(), '{$status}')
            ON DUPLICATE KEY UPDATE
                INT003_SKU = IF(INT003_D009_Id = VALUES(INT003_D009_Id) AND INT003_Id_Produto_ISET = VALUES(INT003_Id_Produto_ISET), VALUES(INT003_SKU), INT003_SKU),
                INT003_Data_Hora_Ultima_Sincronizacao = IF(INT003_D009_Id = VALUES(INT003_D009_Id) AND INT003_Id_Produto_ISET = VALUES(INT003_Id_Produto_ISET), NOW(), INT003_Data_Hora_Ultima_Sincronizacao),
                INT003_Status_Ultima_Sincronizacao = IF(INT003_D009_Id = VALUES(INT003_D009_Id) AND INT003_Id_Produto_ISET = VALUES(INT003_Id_Produto_ISET), VALUES(INT003_Status_Ultima_Sincronizacao), INT003_Status_Ultima_Sincronizacao)
SQL;
        if (!mysql_query($sqlINT003)) return false;
        $sqlINT003 = <<<SQL
            SELECT INT003_Id FROM INT003
            WHERE INT003_C004_Id = '{$empresaId}' AND INT003_D009_Id = '{$d009Id}' AND INT003_Id_Produto_ISET = '{$idIset}'
SQL;
        $consulta = mysql_query($sqlINT003);
        return $consulta && mysql_num_rows($consulta) === 1;
    }
    protected function salvarHistoricoSincronizacao($empresaId, array $produto, $idIset, $operacao, $cadastroAnterior)
    {
        $empresaId = (int) $empresaId; $idIset = (int) $idIset;
        if ($empresaId <= 0 || $idIset <= 0 || !in_array($operacao, array('CRIAR', 'ATUALIZAR'), true)) return false;
        $cadastroPosterior = json_encode(integracoesLimparDadosLog(isset($produto['payloadIset']) ? $produto['payloadIset'] : $produto['payload']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $cadastroAnterior = $cadastroAnterior === null ? null : json_encode(integracoesLimparDadosLog($cadastroAnterior), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($cadastroPosterior === false || $cadastroAnterior === false) return false;
        $codigo = mysql_real_escape_string(substr(isset($produto['D001_Codigo_Produto']) ? $produto['D001_Codigo_Produto'] : '', 0, 100));
        $descricao = mysql_real_escape_string(substr(isset($produto['D001_Descricao_Produto']) ? $produto['D001_Descricao_Produto'] : '', 0, 255));
        $sku = mysql_real_escape_string(substr(isset($produto['skuIset']) ? $produto['skuIset'] : (new ISET_SKU())->paraIset(isset($produto['sku']) ? $produto['sku'] : ''), 0, 100));
        $cadastroPosterior = mysql_real_escape_string($cadastroPosterior);
        $cadastroAnteriorSql = $cadastroAnterior === null ? 'NULL' : "'" . mysql_real_escape_string($cadastroAnterior) . "'";
        $sqlINT005 = <<<SQL
            INSERT INTO INT005 (
                INT005_C004_Id,
                INT005_D001_Id,
                INT005_D049_Id,
                INT005_D009_Id,
                INT005_Codigo_Produto,
                INT005_Descricao_Produto,
                INT005_Id_Produto_ISET,
                INT005_SKU,
                INT005_Operacao,
                INT005_Data_Hora_Sincronizacao,
                INT005_Cadastro_Anterior_Json,
                INT005_Cadastro_Posterior_Json
            ) VALUES (
                '{$empresaId}',
                '{$produto['D001_Id']}',
                '{$produto['D049_Id']}',
                '{$produto['D009_Id']}',
                '{$codigo}',
                '{$descricao}',
                '{$idIset}',
                '{$sku}',
                '{$operacao}',
                NOW(),
                {$cadastroAnteriorSql},
                '{$cadastroPosterior}'
            )
SQL;
        if (mysql_query($sqlINT005) !== false) return true;
        error_log('ISET: falha ao gravar histórico da sincronização do produto D009 #' . (int) $produto['D009_Id'] . '.');
        return false;
    }
    protected function atualizarStatusVinculo($empresaId, $d009Id, $status) { $empresaId = (int) $empresaId; $d009Id = (int) $d009Id; if ($empresaId <= 0 || $d009Id <= 0) return false; $status = mysql_real_escape_string(substr((string) $status, 0, 1)); return mysql_query("UPDATE INT003 SET INT003_Status_Ultima_Sincronizacao='{$status}',INT003_Data_Hora_Ultima_Sincronizacao=NOW() WHERE INT003_C004_Id='{$empresaId}' AND INT003_D009_Id='{$d009Id}'") !== false; }
    /** Reconhece o productId de criação e os IDs das estruturas de consulta suportadas. */
    private function extrairIdIset(array $dados)
    {
        $candidatos = array($dados);
        foreach (array('data', 'result') as $chave) {
            if (!isset($dados[$chave]) || !is_array($dados[$chave])) continue;
            $candidatos[] = $dados[$chave];
            foreach ($dados[$chave] as $item) if (is_array($item)) $candidatos[] = $item;
        }
        $ids = array();
        if (isset($dados['productId']) && (is_int($dados['productId']) || is_string($dados['productId'])) && filter_var($dados['productId'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 4294967295)))) $ids[(int) $dados['productId']] = (int) $dados['productId'];
        foreach ($candidatos as $candidato) {
            if (isset($candidato['id']) && (is_int($candidato['id']) || is_string($candidato['id'])) && filter_var($candidato['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 4294967295)))) $ids[(int) $candidato['id']] = (int) $candidato['id'];
        }
        return count($ids) === 1 ? reset($ids) : 0;
    }
    private function sucesso($resultado, $acao, array $resposta, $idIset, $mensagem) { return array('resultado' => $resultado, 'acao' => $acao, 'statusHttp' => (int) $resposta['status'], 'idIset' => (int) $idIset, 'mensagem' => $mensagem); }
    private function falha($acao, array $resposta, $idIset)
    {
        $status = isset($resposta['status']) ? (int) $resposta['status'] : 0;
        $detalhe = $this->mensagemErroIset($resposta);
        return array('resultado' => 'falhos', 'acao' => $acao, 'statusHttp' => $status, 'idIset' => (int) $idIset, 'mensagem' => 'A ISET não concluiu a operação (' . $status . ').' . ($detalhe !== '' ? ' ' . $detalhe : ''));
    }
    /** Retém somente mensagens de validação da API; nunca registra a resposta bruta. */
    private function mensagemErroIset(array $resposta)
    {
        $dados = isset($resposta['dados']) && is_array($resposta['dados']) ? $resposta['dados'] : array();
        $mensagens = array();
        $coletar = function ($valor, $caminho = '') use (&$coletar, &$mensagens) {
            if (is_string($valor)) {
                $texto = trim((string) $valor);
                if ($texto !== '') $mensagens[] = $caminho !== '' ? $caminho . ': ' . $texto : $texto;
                return;
            }
            if (!is_array($valor)) return;
            foreach ($valor as $chave => $item) {
                $chave = is_string($chave) ? $chave : '';
                if ($chave !== '' && preg_match('/token|secret|senha|password|chave|authorization/i', $chave)) continue;
                $proximoCaminho = $chave === '' ? $caminho : ($caminho === '' ? $chave : $caminho . '.' . $chave);
                $coletar($item, $proximoCaminho);
            }
        };
        $coletar($dados);
        $mensagens = array_values(array_unique($mensagens));
        return $mensagens ? substr(implode(' ', $mensagens), 0, 600) : 'A ISET não enviou detalhes de validação na resposta.';
    }
    private function falhaVinculo($acao, array $resposta, $idIset) { return array('resultado' => 'falhos', 'acao' => $acao, 'statusHttp' => (int) $resposta['status'], 'idIset' => (int) $idIset, 'mensagem' => 'A ISET concluiu a operação, mas o vínculo local não pôde ser salvo.'); }
    private function totaisVazios() { return array('novos' => 0, 'atualizados' => 0, 'bloqueados' => 0); }
    protected function registrarResultado($empresaId, array $produto, array $resultado)
    {
        return $this->registrarEvento($empresaId, $produto, 'CONCLUSAO_PRODUTO', $resultado['resultado'] === 'falhos' ? INT_STATUS_ERRO : INT_STATUS_SUCESSO, $resultado['mensagem'], array_merge($resultado, array('payload' => isset($produto['payloadIset']) ? $produto['payloadIset'] : $produto['payload'])));
    }
    protected function registrarConsultaProduto($empresaId, array $produto, array $resultado) { $estado = isset($resultado['estado']) ? $resultado['estado'] : ISET_CONSULTAS_PRODUTOS::ERRO; $status = $estado === ISET_CONSULTAS_PRODUTOS::ENCONTRADO ? INT_STATUS_SUCESSO : ($estado === ISET_CONSULTAS_PRODUTOS::NAO_ENCONTRADO ? INT_STATUS_AVISO : INT_STATUS_ERRO); $skuIset = (new ISET_SKU())->paraIset($produto['sku']); $idExterno = !empty($resultado['idIset']) ? $resultado['idIset'] : $skuIset; return integracoesRegistrarLog(array('empresaId' => $empresaId, 'integracao' => 'ISET', 'operacao' => INT_OPERACAO_CONSULTAR_PRODUTO, 'status' => $status, 'etapa' => INT_ETAPA_CONSULTA_PRODUTO, 'idExterno' => $idExterno, 'tabelaPrincipal' => 'D009', 'idPrincipal' => $produto['D009_Id'], 'mensagem' => isset($resultado['mensagem']) ? $resultado['mensagem'] : 'Consulta de produto concluída.', 'log' => array('produtoHardness' => array('D001_Id' => (int) $produto['D001_Id'], 'D049_Id' => (int) $produto['D049_Id'], 'D009_Id' => (int) $produto['D009_Id']), 'skuHardness' => $produto['sku'], 'skuIset' => $skuIset, 'origem' => 'MANUAL', 'tentativa' => isset($produto['tentativa']) ? $produto['tentativa'] : '', 'motivo' => isset($resultado['motivo']) ? $resultado['motivo'] : '', 'candidatos' => isset($resultado['candidatos']) ? $resultado['candidatos'] : array(), 'chamadas' => isset($resultado['chamadas']) ? $resultado['chamadas'] : array(), 'requisicaoExecutada' => !empty($resultado['requisicaoExecutada']), 'diagnostico' => isset($resultado['diagnostico']) ? $resultado['diagnostico'] : array('executada' => false), 'estadoConsulta' => $estado, 'resultadoHttp' => isset($resultado['statusHttp']) ? (int) $resultado['statusHttp'] : 0))); }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) definirClasse('ISET_PRODUTOS');
if (!class_exists(__NAMESPACE__ . '\\iset_produtos', false)) class_alias(__NAMESPACE__ . '\\iset_produtos_', __NAMESPACE__ . '\\iset_produtos');
