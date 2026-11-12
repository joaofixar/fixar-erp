<?php

namespace hardness;

definirClasse('MCP001');

class MCP001_
{
    /**
     * Fixed MCP facade for a list of products belonging to one existing quote.
     * It deliberately returns per-item outcomes instead of claiming a rollback
     * that MyISAM-backed legacy writes cannot provide.
     *
     * @return array<string,mixed>
     */
    public function adicionarItensOrcamentoMcp(array $dados): array
    {
        $quoteId = (int) ($dados['quote_id'] ?? 0);
        $items = isset($dados['items']) && is_array($dados['items']) ? $dados['items'] : array();
        if ($quoteId <= 0 || count($items) < 1 || count($items) > 100) {
            throw new \RuntimeException('quote_item_invalid');
        }

        $header = $this->mcp001OrcamentoPendenteBloqueado($quoteId);
        $results = $this->mcp001ResultadosAddIniciais($items);
        $preflightFailed = false;
        $firstError = '';
        foreach ($items as $offset => $item) {
            $error = $this->mcp001ValidarItemAdicionar($item);
            if ($error !== null) {
                $results[$offset]['status'] = 'failed';
                $results[$offset]['error_code'] = $error;
                $preflightFailed = true;
                if ($firstError === '') $firstError = $error;
            }
        }
        if ($preflightFailed) return array('quote_id' => $quoteId, 'status' => 'preflight_failed', 'items' => $results, 'error_code' => $firstError);

        require_once 'bibliotecas/classes/VEN001.php';
        $ven = new VEN001();
        foreach ($items as $offset => $item) {
            $productId = (int) $item['product_id'];
            $GLOBALS['T004_Id'] = 0;
            try {
                $form = $ven->montarFormIncluirProdutoOrcamento($quoteId, $productId, $item['quantity'], $this->mcp001CodigoLocalItem($item));
                $included = $form !== false && $ven->incluirProduto($form) === true;
                $confirmed = $this->mcp001ItemAdicionadoConfirmado((int) ($GLOBALS['T004_Id'] ?? 0), $quoteId, (int) $header['T003_C004_Id'], $productId);
                if ($included && $confirmed !== null) {
                    $results[$offset]['status'] = 'included';
                    $results[$offset]['t004_id'] = (int) $confirmed['T004_Id'];
                    $results[$offset]['item_order'] = (int) $confirmed['T004_Item'];
                    continue;
                }
                if ($confirmed !== null) {
                    $results[$offset]['status'] = 'indeterminate';
                    $results[$offset]['t004_id'] = (int) $confirmed['T004_Id'];
                    $results[$offset]['item_order'] = (int) $confirmed['T004_Item'];
                    $results[$offset]['error_code'] = 'requires_quote_review';
                } else {
                    $results[$offset]['status'] = 'failed';
                    $results[$offset]['error_code'] = 'quote_item_creation_failed';
                }
            } catch (\Throwable $exception) {
                $confirmed = $this->mcp001ItemAdicionadoConfirmadoSeguro((int) ($GLOBALS['T004_Id'] ?? 0), $quoteId, (int) $header['T003_C004_Id'], $productId);
                if ($confirmed !== null) {
                    $results[$offset]['status'] = 'indeterminate';
                    $results[$offset]['t004_id'] = (int) $confirmed['T004_Id'];
                    $results[$offset]['item_order'] = (int) $confirmed['T004_Item'];
                    $results[$offset]['error_code'] = 'requires_quote_review';
                } else {
                    $results[$offset]['status'] = 'failed';
                    $results[$offset]['error_code'] = $this->mcp001CodigoAddSeguro($exception->getMessage());
                }
            }
            return array('quote_id' => $quoteId, 'status' => 'interrupted', 'items' => $results, 'error_code' => $results[$offset]['error_code']);
        }
        return array('quote_id' => $quoteId, 'status' => 'completed', 'items' => $results);
    }

    /** @param array<int,mixed> $items @return array<int,array<string,mixed>> */
    private function mcp001ResultadosAddIniciais(array $items): array
    {
        $results = array();
        foreach ($items as $offset => $item) {
            $results[] = array('index' => $offset + 1, 'product_id' => is_array($item) ? (int) ($item['product_id'] ?? 0) : 0, 'requested_quantity' => is_array($item) ? ($item['quantity'] ?? 0) : 0, 'status' => 'not_attempted');
        }
        return $results;
    }

    /** @param mixed $item */
    private function mcp001ValidarItemAdicionar($item): ?string
    {
        if (!is_array($item) || !isset($item['product_id'], $item['quantity']) || !is_numeric($item['product_id']) || (int) $item['product_id'] != $item['product_id'] || (int) $item['product_id'] <= 0 || !is_numeric($item['quantity']) || (float) $item['quantity'] <= 0) return 'quote_item_invalid';
        try { $this->mcp001CodigoLocalItem($item); } catch (\Throwable $exception) { return 'quote_item_invalid'; }
        $productId = (int) $item['product_id'];
        if (!$this->mcp001ProdutoAtivoDisponivelParaEmpresa($productId)) return 'product_not_available_for_company';
        if ($this->mcp001ProdutoPossuiServicos($productId)) return 'product_services_not_supported';
        if ($this->mcp001ProdutoUsaConversao($productId)) return 'product_conversion_not_supported';
        return null;
    }

    /** @param array<string,mixed> $item */
    private function mcp001CodigoLocalItem(array $item): string
    {
        if (!array_key_exists('location_code', $item)) return '';
        if (!is_string($item['location_code'])) throw new \RuntimeException('quote_item_invalid');
        $codigoLocal = trim($item['location_code']);
        $length = function_exists('mb_strlen') ? mb_strlen($codigoLocal, 'UTF-8') : strlen($codigoLocal);
        if ($codigoLocal === '' || $length > 60 || preg_match('/[[:cntrl:]]/u', $codigoLocal)) throw new \RuntimeException('quote_item_invalid');
        return $codigoLocal;
    }

    /** @return array<string,mixed> */
    private function mcp001OrcamentoPendenteBloqueado(int $quoteId): array
    {
        global $g;
        $company = (int) ($g['empresaAtual'] ?? 0);
        if ($company <= 0) throw new \RuntimeException('quote_unavailable');
        $statement = $this->mcp001Stmt('SELECT T003_Id,T003_C004_Id,T003_Flag_Status_Orcamento FROM T003 WHERE T003_Id=? AND T003_C004_Id=? FOR UPDATE', 'ii', array($quoteId, $company));
        $row = $statement->get_result()->fetch_assoc();
        if (!$row) throw new \RuntimeException('quote_unavailable');
        if (($row['T003_Flag_Status_Orcamento'] ?? '') !== 'P') throw new \RuntimeException('quote_not_pending');
        return $row;
    }

    private function mcp001ProdutoAtivoDisponivelParaEmpresa(int $productId): bool
    {
        global $g;
        $company = (int) ($g['empresaAtual'] ?? 0);
        $statement = $this->mcp001Stmt("SELECT 1 FROM D001 INNER JOIN D049 ON D049_D001_Id=D001_Id INNER JOIN D009 ON D009_D049_Id=D049_Id WHERE D001_Id=? AND D001_Flag_Ativo='S' AND D009_C004_Id=? LIMIT 1", 'ii', array($productId, $company));
        return $statement->get_result()->fetch_assoc() !== null;
    }

    private function mcp001ProdutoUsaConversao(int $productId): bool
    {
        $statement = $this->mcp001Stmt('SELECT D002A_Utiliza_Conversao FROM D001 LEFT JOIN D002 ON D002_Id=D001_D002_Id LEFT JOIN D002A ON D002A_D002_Id=D002_Id WHERE D001_Id=? LIMIT 1', 'i', array($productId));
        $row = $statement->get_result()->fetch_assoc();
        return is_array($row) && ($row['D002A_Utiliza_Conversao'] ?? '') === 'S';
    }

    /** @return array<string,mixed>|null */
    private function mcp001ItemAdicionadoConfirmado(int $itemId, int $quoteId, int $companyId, int $productId): ?array
    {
        if ($itemId <= 0) return null;
        $statement = $this->mcp001Stmt('SELECT t004.T004_Id,t004.T004_Item FROM T004 t004 INNER JOIN D009 d009 ON d009.D009_Id=t004.T004_D009_Id INNER JOIN D049 d049 ON d049.D049_Id=d009.D009_D049_Id WHERE t004.T004_Id=? AND t004.T004_T003_Id=? AND t004.T004_C004_Id=? AND d049.D049_D001_Id=? LIMIT 1', 'iiii', array($itemId, $quoteId, $companyId, $productId));
        $row = $statement->get_result()->fetch_assoc();
        return is_array($row) ? $row : null;
    }

    /** @return array<string,mixed>|null */
    private function mcp001ItemAdicionadoConfirmadoSeguro(int $itemId, int $quoteId, int $companyId, int $productId): ?array
    {
        try { return $this->mcp001ItemAdicionadoConfirmado($itemId, $quoteId, $companyId, $productId); } catch (\Throwable $exception) { return null; }
    }

    private function mcp001CodigoAddSeguro(string $code): string
    {
        return in_array($code, array('quote_item_retryable', 'quote_item_invalid', 'quote_item_creation_failed'), true) ? $code : 'quote_item_creation_failed';
    }

    /** @return array{success:bool,quote_id:int,items_count:int} */
    public function criarOrcamento(array $dados): array
    {
        global $g;
        require_once 'bibliotecas/classes/VEN001.php';
        $VEN001 = new VEN001();
        $items = isset($dados['items']) && is_array($dados['items']) ? $dados['items'] : array();
        if ($items === array()) {
            throw new \RuntimeException('quote_item_invalid');
        }
        foreach ($items as $index => $item) {
            if (!is_array($item) || !isset($item['product_id']) || !ctype_digit((string) $item['product_id']) || (int) $item['product_id'] <= 0) {
                throw new \RuntimeException('quote_item_invalid');
            }
            $this->mcp001CodigoLocalItem($item);
            if (!$this->mcp001ProdutoDisponivelParaEmpresa((int) $item['product_id'])) {
                throw new \RuntimeException('product_not_available_for_company:' . ($index + 1) . ':' . (int) $item['product_id']);
            }
            if ($this->mcp001ProdutoPossuiServicos((int) $item['product_id'])) {
                throw new \RuntimeException('product_services_not_supported:' . ($index + 1) . ':' . (int) $item['product_id']);
            }
        }

        $resultado = $VEN001->incluirCabecalhoOrcamento(array('customer_id' => $dados['customer_id'] ?? null));
        if (!is_array($resultado) || ($resultado['success'] ?? false) !== true || !isset($resultado['quote_id'])) {
            $codigo = is_array($resultado) && isset($resultado['error_code']) && is_string($resultado['error_code'])
                ? $resultado['error_code']
                : 'quote_creation_failed';
            throw new \RuntimeException($codigo);
        }
        $quoteId = (int) $resultado['quote_id'];
        if ($quoteId <= 0) {
            throw new \RuntimeException('quote_creation_failed');
        }

        $this->mcp001VincularCanalAssistenteIa($quoteId);
        $included = 0;
        // A inclusão pela tela continua totalizando o cabeçalho por item.
        // No lote MCP, cada T004 é totalizado normalmente e o T003 é
        // totalizado uma única vez quando todos os itens existirem.
        $g['mcpQuoteCreateBatch'] = true;
        try {
            foreach ($items as $index => $item) {
                if (!is_array($item)) {
                    throw new \RuntimeException('quote_item_invalid');
                }
                $form = $VEN001->montarFormIncluirProdutoOrcamento($quoteId, $item['product_id'] ?? 0, $item['quantity'] ?? 0, $this->mcp001CodigoLocalItem($item));
                if ($form === false || $VEN001->incluirProduto($form) !== true) {
                    throw new \RuntimeException('quote_item_creation_failed:' . ($index + 1) . ':' . (int) ($item['product_id'] ?? 0));
                }
                ++$included;
            }
            $totalizacao = mysql_query("call T003_Gravar_Totalizacao_4('$quoteId');");
            if ($totalizacao === false || mysql_error() !== '') {
                throw new \RuntimeException('quote_header_totalization_failed');
            }
        } finally {
            unset($g['mcpQuoteCreateBatch']);
        }
        return array('success' => true, 'quote_id' => $quoteId, 'items_count' => $included);
    }

    /**
     * Vincula o orçamento criado pelo MCP ao canal técnico padronizado.
     *
     * D201 é administrável por empresa, por isso a integração nunca depende
     * de um D201_Id fixo. O registro é criado somente quando ainda não existe.
     */
    private function mcp001VincularCanalAssistenteIa(int $orcamentoId): void
    {
        global $g;

        $chaveSistema = 'MCP_IA';
        $canal = mysql_query("SELECT D201_Id FROM D201 WHERE D201_Chave_Sistema = '" . mysql_real_escape_string($chaveSistema) . "' ORDER BY D201_Id LIMIT 1");
        if ($canal === false) {
            throw new \RuntimeException('quote_header_extension_insert_failed');
        }
        $dadosCanal = mysql_fetch_assoc($canal);
        $canalId = (int) ($dadosCanal['D201_Id'] ?? 0);

        if ($canalId <= 0) {
            $descricao = mysql_real_escape_string('ASSISTENTE IA');
            $chave = mysql_real_escape_string($chaveSistema);
            $conexao = $g['conexaoBanco'] ?? null;
            if (!$conexao instanceof \mysqli || !$conexao->query("INSERT INTO D201 (D201_Descricao, D201_Chave_Sistema) VALUES ('{$descricao}', '{$chave}')")) {
                throw new \RuntimeException('quote_header_extension_insert_failed');
            }
            // mysql_query() aciona as auditorias legadas, que podem executar
            // outro INSERT antes de retornarem. O ID precisa vir diretamente
            // da mesma conexão que criou o canal.
            $canalId = (int) $conexao->insert_id;
        }

        if ($canalId <= 0 || !mysql_query("UPDATE T003A SET T003A_D201_Id = '{$canalId}' WHERE T003A_T003_Id = '" . (int) $orcamentoId . "'")) {
            throw new \RuntimeException('quote_header_extension_insert_failed');
        }
    }

    private function mcp001ProdutoPossuiServicos(int $produtoId): bool
    {
        $servicos = mysql_query("SELECT 1 FROM D135 LEFT JOIN D112 ON D112_Id = D135_D112_Id WHERE D135_D001_Id = '{$produtoId}' LIMIT 1");
        if ($servicos === false) {
            throw new \RuntimeException('product_service_validation_failed');
        }
        return mysql_num_rows($servicos) > 0;
    }

    private function mcp001ProdutoDisponivelParaEmpresa(int $produtoId): bool
    {
        global $g;
        $empresaId = (int) ($g['empresaAtual'] ?? 0);
        if ($empresaId <= 0) {
            throw new \RuntimeException('product_availability_validation_failed');
        }
        $produto = mysql_query("SELECT 1 FROM D049 INNER JOIN D009 ON D009_D049_Id = D049_Id WHERE D049_D001_Id = '{$produtoId}' AND D009_C004_Id = '{$empresaId}' LIMIT 1");
        if ($produto === false) {
            throw new \RuntimeException('product_availability_validation_failed');
        }
        return mysql_num_rows($produto) > 0;
    }

    /**
     * Read-only smoke-test method for the MCP legacy facade.
     *
     * @return array<string,int|string>
     */
    public function mcp001ContextPilot(): array
    {
        global $g;

        return [
            'implementation' => $this->mcp001PilotImplementation(),
            'company_id' => (int) $g['empresaAtual'],
            'user_id' => (int) $g['usuarioAtual'],
            'profile_id' => (int) $g['perfilUsuario'],
            'approval_level' => (int) $g['nivelAlcadaUsuarioSistema'],
        ];
    }

    protected function mcp001PilotImplementation(): string
    {
        return 'standard';
    }

    /** Fixed MCP facade: no table, column or predicate comes from the caller. */
    public function alterarItemOrcamentoMcp(array $dados): array
    {
        $field=(string)($dados['field']??'');
        if(!in_array($field,['quantity','discount_percent','unit_price','item_order','location_code'],true))throw new \RuntimeException('quote_item_invalid_value');
        if($field==='location_code'){
            if(!array_key_exists('location_code',$dados)||array_key_exists('value',$dados))throw new \RuntimeException('quote_item_invalid_value');
            $value=$this->mcp001CodigoLocalAtualizacao($dados['location_code']);
        }else{
            if(array_key_exists('location_code',$dados))throw new \RuntimeException('quote_item_invalid_value');
            $value=$dados['value']??null;
            if(!is_numeric($value)||($field!=='discount_percent'&&(float)$value<=0))throw new \RuntimeException('quote_item_invalid_value');
        }
        $item=$this->mcp001ItemPendenteBloqueado((int)($dados['t004_id']??0),$field==='item_order');
        require_once 'bibliotecas/classes/VEN001.php'; $ven=new VEN001(); $old='';
        if($field==='quantity'){
            if($this->mcp001UsaConversao((int)$item['T004_Id']))throw new \RuntimeException('quote_item_invalid_value');
            $old=(string)$item['T004_Quantidade']; $this->mcp001Exec('UPDATE T004 SET T004_Quantidade=? WHERE T004_Id=?',[(string)$value,(int)$item['T004_Id']]);
            $r=$ven->aplicarQuantidade((int)$item['T004_Id'],'T004_Quantidade',$old,(string)$value); if($r!==true)throw new \RuntimeException('quote_item_invalid_value');
        } elseif($field==='discount_percent'){
            $column='T004_Percentual_Desconto'; $old=(string)$item[$column];
            // The grid sends pt-BR decimals to VEN001. JSON numbers use a
            // dot, which gCorrigeNumeroInverte interprets as a thousands separator.
            $legacyValue=str_replace('.',',',(string)$value);
            $r=$ven->ven001AplicarPercentualDesconto((int)$item['T004_Id'],$column,$old,$legacyValue);if($r!==true)throw new \RuntimeException('quote_item_invalid_value');
        } elseif($field==='unit_price'){
            // The grid sends pt-BR decimals to VEN001.  A JSON number uses a
            // dot, which gCorrigeNumeroInverte would otherwise treat as a
            // thousands separator (12.5 -> 125).
            if ((float)$item['T004_Valor_Preco_Original'] <= 0) throw new \RuntimeException('quote_item_price_unavailable');
            $column='T004_Valor_Preco_Unitario'; $old=(string)$item[$column];
            $legacyValue=str_replace('.',',',(string)$value);
            $r=$ven->ven001AplicarPercentualDesconto((int)$item['T004_Id'],$column,$old,$legacyValue);if($r!==true)throw new \RuntimeException('quote_item_invalid_value');
        } elseif($field==='item_order') {
            if((int)$value!=(float)$value)throw new \RuntimeException('quote_item_invalid_value'); $old=(string)$item['T004_Item'];
            if((int)$value===(int)$item['T004_Item'])return ['t004_id'=>(int)$item['T004_Id'],'quote_id'=>(int)$item['T004_T003_Id'],'field'=>$field,'value'=>(string)$value];
            if((int)$value>(int)$item['_mcp_item_count'])throw new \RuntimeException('quote_item_invalid_position');
            $this->mcp001Exec('UPDATE T004 SET T004_Item=? WHERE T004_Id=?',[(int)$value,(int)$item['T004_Id']]); if($ven->processarNumeroItemOrcamento((int)$item['T004_Id'],(int)$value)!==true)throw new \RuntimeException('quote_item_write_failed');$this->mcp001OrdemConfirmada((int)$item['T004_T003_Id'],(int)$item['T004_C004_Id'],(int)$item['T004_Id'],(int)$value);
        } else {
            $value=strtoupper($value); $old=(string)($item['T004_Codigo_Local']??'');
            if($value===$old)return ['t004_id'=>(int)$item['T004_Id'],'quote_id'=>(int)$item['T004_T003_Id'],'field'=>$field,'value'=>$value];
            $this->mcp001Exec('UPDATE T004 SET T004_Codigo_Local=? WHERE T004_Id=?',[$value,(int)$item['T004_Id']]);
        }
        if($field!=='location_code')$this->mcp001Totalizar((int)$item['T004_Id'],(int)$item['T004_T003_Id']);
        $this->mcp001Atividade($item,'Edição MCP: '.$field,$old,(string)$value); return ['t004_id'=>(int)$item['T004_Id'],'quote_id'=>(int)$item['T004_T003_Id'],'field'=>$field,'value'=>(string)$value];
    }

    /** @param mixed $value */
    private function mcp001CodigoLocalAtualizacao($value): string
    {
        if(!is_string($value))throw new \RuntimeException('quote_item_invalid_value');
        $codigoLocal=trim($value);
        $length=function_exists('mb_strlen')?mb_strlen($codigoLocal,'UTF-8'):strlen($codigoLocal);
        if($length>60||preg_match('/[[:cntrl:]]/u',$codigoLocal))throw new \RuntimeException('quote_item_invalid_value');
        return $codigoLocal;
    }

    public function excluirItemOrcamentoMcp(int $id): array
    {
        $item=$this->mcp001ItemPendenteBloqueado($id,true); $quote=(int)$item['T004_T003_Id']; require_once 'bibliotecas/classes/VEN001.php';
        $result=(new \hardness\VEN001())->excluirProduto($id);
        if($result!==true)throw new \RuntimeException('quote_item_write_failed');
        $this->mcp001ExclusaoConfirmada($quote,(int)$item['T004_C004_Id'],$id);
        // The single-item grid does not create a D085. MCP keeps its own audit,
        // but only after the complete legacy deletion has succeeded.
        $this->mcp001Atividade($item,'Exclusão MCP','', '');
        return ['t004_id'=>$id,'quote_id'=>$quote,'deleted'=>true];
    }

    private function mcp001ItemPendenteBloqueado(int $id,bool $lockQuoteItems=false): array { global $g; if($id<=0)throw new \RuntimeException('quote_item_unavailable');$company=(int)$g['empresaAtual'];$lookup=$this->mcp001Stmt('SELECT T004_T003_Id,T004_C004_Id FROM T004 WHERE T004_Id=? AND T004_C004_Id=?','ii',[$id,$company]);$candidate=$lookup->get_result()->fetch_assoc();if(!$candidate)throw new \RuntimeException('quote_item_unavailable');$quote=(int)$candidate['T004_T003_Id'];$header=$this->mcp001Stmt('SELECT T003_Flag_Status_Orcamento FROM T003 WHERE T003_Id=? AND T003_C004_Id=? FOR UPDATE','ii',[$quote,$company]);$state=$header->get_result()->fetch_assoc();if(!$state)throw new \RuntimeException('quote_item_unavailable');$target=$this->mcp001Stmt('SELECT * FROM T004 WHERE T004_Id=? AND T004_T003_Id=? AND T004_C004_Id=? FOR UPDATE','iii',[$id,$quote,$company]);$row=$target->get_result()->fetch_assoc();if(!$row)throw new \RuntimeException('quote_item_unavailable');if($lockQuoteItems)$row['_mcp_item_count']=$this->mcp001QuantidadeItensBloqueada($quote,$company);if($state['T003_Flag_Status_Orcamento']!=='P')throw new \RuntimeException('quote_not_pending');$lock=$this->mcp001Stmt('SELECT 1 FROM T006 WHERE T006_T004_Id=? LIMIT 1 FOR UPDATE','i',[$id]);if($lock->get_result()->fetch_assoc())throw new \RuntimeException('quote_item_locked');return $row; }
    private function mcp001QuantidadeItensBloqueada(int $quote,int $company): int { $stmt=$this->mcp001Stmt('SELECT T004_Id FROM T004 WHERE T004_T003_Id=? AND T004_C004_Id=? ORDER BY T004_Id FOR UPDATE','ii',[$quote,$company]);$count=$stmt->get_result()->num_rows;if($count<1)throw new \RuntimeException('quote_item_invalid_position');return $count; }
    private function mcp001OrdemConfirmada(int $quote,int $company,int $item,int $position): void { $stmt=$this->mcp001Stmt('SELECT T004_Id,T004_Item FROM T004 WHERE T004_T003_Id=? AND T004_C004_Id=? ORDER BY T004_Item,T004_Id','ii',[$quote,$company]);$expected=1;$found=false;foreach($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $row){if((int)$row['T004_Item']!==$expected++)throw new \RuntimeException('quote_item_write_failed');if((int)$row['T004_Id']===$item&&(int)$row['T004_Item']===$position)$found=true;}if(!$found)throw new \RuntimeException('quote_item_write_failed'); }
    private function mcp001ExclusaoConfirmada(int $quote,int $company,int $item): void { $gone=$this->mcp001Stmt('SELECT T004_Id FROM T004 WHERE T004_Id=?','i',[$item]);if($gone->get_result()->fetch_assoc())throw new \RuntimeException('quote_item_write_failed');$aux=$this->mcp001Stmt('SELECT T004A_Id FROM T004A WHERE T004A_T004_Id=?','i',[$item]);if($aux->get_result()->fetch_assoc())throw new \RuntimeException('quote_item_write_failed');$stmt=$this->mcp001Stmt('SELECT T004_Item FROM T004 WHERE T004_T003_Id=? AND T004_C004_Id=? ORDER BY T004_Item,T004_Id','ii',[$quote,$company]);$expected=1;foreach($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $row)if((int)$row['T004_Item']!==$expected++)throw new \RuntimeException('quote_item_write_failed'); }
    private function mcp001UsaConversao(int $id): bool { $r=mysql_query("SELECT D002A_Utiliza_Conversao v FROM T004 LEFT JOIN D009 ON D009_Id=T004_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id LEFT JOIN D002 ON D002_Id=D001_D002_Id LEFT JOIN D002A ON D002A_D002_Id=D002_Id WHERE T004_Id='$id'");$x=mysql_fetch_assoc($r);return $x&&$x['v']==='S'; }
    private function mcp001Stmt(string $sql,string $types,array $values): \mysqli_stmt { global $g;$stmt=$g['conexaoBanco']->prepare($sql);if(!$stmt){$this->mcp001BancoFalhou();}$stmt->bind_param($types,...$values);if(!$stmt->execute()){$this->mcp001BancoFalhou();}return $stmt; }
    private function mcp001Exec(string $sql,array $values): void { $types='';foreach($values as $v)$types.=is_int($v)?'i':'s';$s=$this->mcp001Stmt($sql,$types,$values);if($s->affected_rows<1)throw new \RuntimeException('quote_item_write_failed'); }
    private function mcp001Totalizar(int $item,int $quote): void { global $g;if(!$g['conexaoBanco']->query("CALL T004_Gravar_Totalizacao_4('$item')"))$this->mcp001BancoFalhou();while($g['conexaoBanco']->more_results()&&$g['conexaoBanco']->next_result()){}if($g['conexaoBanco']->errno)$this->mcp001BancoFalhou();if(!$g['conexaoBanco']->query("CALL T003_Gravar_Totalizacao_4('$quote')"))$this->mcp001BancoFalhou();while($g['conexaoBanco']->more_results()&&$g['conexaoBanco']->next_result()){}if($g['conexaoBanco']->errno)$this->mcp001BancoFalhou(); }
    private function mcp001BancoFalhou(): void { global $g;$errno=(int)($g['conexaoBanco']->errno??0);if($errno===1205||$errno===1213)throw new \RuntimeException('quote_item_retryable');throw new \RuntimeException('quote_item_write_failed'); }
    private function mcp001Atividade(array $item,string $operation,string $old,string $new): void { global $g;$msg=mysql_real_escape_string($operation.' | Item '.$item['T004_Id'].' | '.$old.' -> '.$new);mysql_query("INSERT INTO D085 (D085_C007_Id,D085_Data,D085_Hora,D085_Mensagem,D085_C007_Id_Inclusao,D085_T003_Id,D085_Flag_Tipo,D085_Flag_Lido,D085_Flag_Manual) VALUES ('{$g['usuarioAtual']}',CURDATE(),CURTIME(),'$msg','{$g['usuarioAtual']}','{$item['T004_T003_Id']}',2,'N','N')");if(mysql_error())$this->mcp001BancoFalhou(); }
}
