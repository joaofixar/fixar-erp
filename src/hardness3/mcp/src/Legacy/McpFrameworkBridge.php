<?php

declare(strict_types=1);

namespace HardnessMcp\Legacy;

/**
 * Isolates the smallest proven non-interactive Framework setup.
 *
 * The callback is intentionally a Closure rather than a class/method name;
 * future MCP code can provide a fixed internal operation, never Tool input.
 */
final class McpFrameworkBridge
{
    private string $erpRootPath;

    public function __construct(string $erpRootPath)
    {
        $root = realpath($erpRootPath);
        if ($root === false || !is_dir($root)) {
            throw new LegacyBridgeException();
        }
        $this->erpRootPath = rtrim($root, '/') . '/';
    }

    /**
     * Runs one fixed, internal legacy operation with the context prepared.
     *
     * @return mixed
     */
    public function execute(LegacyExecutionContext $context, \Closure $operation)
    {
        return $this->executeInternal($context, $operation, false);
    }

    /** @return array{success:bool,quote_id:int,items_count:int} */
    public function executeMcp001CreateQuote(LegacyExecutionContext $context, array $quoteData): array
    {
        $result = $this->executeInternal($context, function () use ($quoteData): array {
            $connection = $GLOBALS['g']['conexaoBanco'] ?? null;
            if (!$connection instanceof \mysqli || !$connection->query('SET AUTOCOMMIT=0') || !$connection->begin_transaction()) {
                throw new LegacyBridgeException('quote_transaction_begin_failed');
            }
            try {
                require_once $this->erpRootPath . 'bibliotecas/classes/MCP001.php';
                $result = (new \hardness\MCP001())->criarOrcamento($quoteData);
                if (!$connection->commit()) {
                    throw new LegacyBridgeException('quote_transaction_commit_failed');
                }
                return $result;
            } catch (\Throwable $exception) {
                $connection->rollback();
                throw $exception;
            } finally {
                $connection->autocommit(true);
            }
        }, true);
        if (!is_array($result) || array_keys($result) !== ['success', 'quote_id', 'items_count'] || $result['success'] !== true || !is_int($result['quote_id']) || $result['quote_id'] <= 0 || !is_int($result['items_count']) || $result['items_count'] <= 0) {
            throw new LegacyBridgeException('quote_result_invalid');
        }
        return $result;
    }

    /** @return array<string,mixed> */
    public function executeMcp001QuoteItemUpdate(LegacyExecutionContext $context, array $data): array
    {
        return $this->executeMcp001ItemWrite($context, function () use ($data): array { require_once $this->erpRootPath . 'bibliotecas/classes/MCP001.php'; return (new \hardness\MCP001())->alterarItemOrcamentoMcp($data); });
    }

    /** @return array<string,mixed> */
    public function executeMcp001QuoteItemAdd(LegacyExecutionContext $context, array $data): array
    {
        return $this->executeMcp001ItemWrite($context, function () use ($data): array { require_once $this->erpRootPath . 'bibliotecas/classes/MCP001.php'; return (new \hardness\MCP001())->adicionarItensOrcamentoMcp($data); });
    }

    /** @return array<string,mixed> */
    public function executeMcp001QuoteItemDelete(LegacyExecutionContext $context, int $itemId): array
    {
        return $this->executeMcp001ItemWrite($context, function () use ($itemId): array { require_once $this->erpRootPath . 'bibliotecas/classes/MCP001.php'; return (new \hardness\MCP001())->excluirItemOrcamentoMcp($itemId); });
    }

    /** @return array<string,mixed> */
    private function executeMcp001ItemWrite(LegacyExecutionContext $context, \Closure $operation): array
    {
        $result=$this->executeInternal($context,function() use($operation):array { $connection=$GLOBALS['g']['conexaoBanco']??null; if(!$connection instanceof \mysqli||!$connection->begin_transaction()) throw new LegacyBridgeException('quote_item_transaction_begin_failed'); try{$result=$operation();if(!$connection->commit())throw new LegacyBridgeException('quote_item_transaction_commit_failed');return $result;}catch(\Throwable $e){$connection->rollback();throw $e;} },true);
        if(!is_array($result))throw new LegacyBridgeException('quote_item_result_invalid'); return $result;
    }

    /**
     * Generates the standard VEN001 PDF. Loading VEN001 through definirClasse
     * preserves tenant and company method customizations, while montarEmailPdf
     * keeps the configured C031 layout, margins, logo and print variables.
     */
    public function executeVen001GenerateQuotePdf(LegacyExecutionContext $context, int $quoteId): string
    {
        if ($quoteId <= 0) { throw new LegacyBridgeException('quote_pdf_invalid'); }
        $result = $this->execute($context, function () use ($quoteId): array {
            require_once $this->erpRootPath . 'bibliotecas/classes/VEN001.php';
            $generated = (new \hardness\VEN001())->montarEmailPdf($quoteId);
            return is_array($generated) ? $generated : [];
        });
        $webPath = is_array($result) && isset($result['AnexosWeb'][0]) && is_string($result['AnexosWeb'][0]) ? $result['AnexosWeb'][0] : '';
        $attachment = is_array($result) && isset($result['Anexos'][0]) && is_string($result['Anexos'][0]) ? $result['Anexos'][0] : '';
        $expectedAttachment = 'dados_usuarios/' . $context->getTenant()->getDatabase() . '/tmp/orcamento-' . $quoteId . '.pdf';
        if ($attachment !== $expectedAttachment || $webPath !== '/hardness3/' . $expectedAttachment || !is_file($this->erpRootPath . $attachment)) {
            throw new LegacyBridgeException('quote_pdf_generation_failed');
        }
        // The regular temporary path is an ERP implementation detail and is
        // normally deleted after interactive printing. Create a separate,
        // unguessable public copy using the existing WhatsApp share area.
        // Existing installations may expose this legacy share area through a
        // deployment-managed symbolic link (for example, /dados/whatsapp).
        // is_dir and is_writable deliberately resolve that trusted path, just
        // as the original WhatsApp flow does.
        $shareDirectory = $this->erpRootPath . 'whatsapp';
        if ((!is_dir($shareDirectory) && !mkdir($shareDirectory, 0775, true)) || !is_dir($shareDirectory) || !is_writable($shareDirectory)) {
            throw new LegacyBridgeException('quote_pdf_share_unavailable');
        }
        try {
            $shareFilename = bin2hex(random_bytes(24)) . '.pdf';
        } catch (\Throwable $exception) {
            throw new LegacyBridgeException('quote_pdf_share_unavailable');
        }
        $sharePath = $shareDirectory . '/' . $shareFilename;
        if (!copy($this->erpRootPath . $attachment, $sharePath) || !is_file($sharePath) || filesize($sharePath) <= 0) {
            @unlink($sharePath);
            throw new LegacyBridgeException('quote_pdf_share_unavailable');
        }
        return '/hardness3/whatsapp/' . $shareFilename;
    }

    /**
     * Executes the IDE SMT quote-to-order flow without routing through its
     * interactive AJAX program. VEN001 is loaded only after the authenticated
     * company context is ready, preserving IDE and customer method overrides.
     *
     * @return array{success:bool,quote_id:int,order_id:int,type:int,order_status:int,order_status_name:string,credit_review:bool,post_processing_complete:bool}
     */
    public function executeVen001IdeSmtGenerateOrder(LegacyExecutionContext $context, int $quoteId, int $type): array
    {
        if ($quoteId <= 0 || !in_array($type, [1, 2, 3, 4], true)) {
            throw new LegacyBridgeException('ide_smt_order_invalid');
        }

        $result = $this->executeInternal($context, function () use ($context, $quoteId, $type): array {
            $connection = $GLOBALS['g']['conexaoBanco'] ?? null;
            if (!$connection instanceof \mysqli) {
                throw new LegacyBridgeException('ide_smt_order_unavailable');
            }
            if (($GLOBALS['g']['C031']['SMT_habilitaMudarGerarPedido'] ?? null) !== 'S') {
                throw new LegacyBridgeException('ide_smt_order_disabled');
            }

            require_once $this->erpRootPath . 'bibliotecas/classes/VEN001.php';
            $ven001 = new \hardness\VEN001();
            $quote = $this->ideSmtQuote($connection, $quoteId);
            if ($quote === null || (int) ($quote['T003_C004_Id'] ?? 0) !== $context->getCompanyId()) {
                throw new LegacyBridgeException('ide_smt_quote_not_available');
            }
            if ((int) ($quote['T003A_T003_Id'] ?? 0) !== $quoteId) {
                throw new LegacyBridgeException('ide_smt_quote_extension_missing');
            }

            $this->assertIdeSmtInteractivePreconditions($connection, $ven001, $quoteId, $quote);

            if (!$connection->query('UPDATE T003A SET T003A_Tipo_Gerar_Pedido=' . $type . ' WHERE T003A_T003_Id=' . $quoteId)) {
                throw new LegacyBridgeException('ide_smt_order_write_failed');
            }

            $GLOBALS['T005_Id'] = 0;
            $legacyCallBufferLevel = ob_get_level();
            ob_start();
            try {
                $beforeResult = $ven001->acoesAntesGerarPedido($quoteId, $type);
                if ($beforeResult !== true) {
                    throw $this->ideSmtFunctionalFailure('ide_smt_order_precondition_failed', $beforeResult);
                }
                $generationResult = $ven001->gerarPedido($quoteId);
                if ($generationResult !== true) {
                    throw $this->ideSmtFunctionalFailure('ide_smt_order_generation_failed', $generationResult);
                }

                $orderId = (int) ($GLOBALS['T005_Id'] ?? 0);
                if ($orderId <= 0) {
                    throw new LegacyBridgeException('ide_smt_order_result_invalid');
                }

                // Confirm the irreversible result before optional legacy
                // post-processing. A later exception must never invite the
                // caller to create a duplicate order.
                $order = $this->ideSmtOrder($connection, $orderId, $quoteId, $context->getCompanyId());
                if ($order === null) {
                    throw new LegacyBridgeException('ide_smt_order_result_invalid');
                }

                $postProcessingComplete = true;
                try {
                    // The interactive endpoint deliberately ignores this return.
                    $ven001->processoPedido($orderId, $type);
                    $ven001->peVEN001FormFuncFimGerarPedido($orderId);

                    // Preserve the interactive flow's final status when all
                    // post-processing steps completed successfully.
                    $refreshedOrder = $this->ideSmtOrder($connection, $orderId, $quoteId, $context->getCompanyId());
                    if ($refreshedOrder === null) {
                        throw new LegacyBridgeException('ide_smt_order_result_invalid');
                    }
                    $order = $refreshedOrder;
                } catch (\Throwable $exception) {
                    $postProcessingComplete = false;
                    $this->logIdeSmtPostProcessingFailure($exception, $quoteId, $orderId);
                }
            } finally {
                $legacyOutput = '';
                while (ob_get_level() > $legacyCallBufferLevel) {
                    $chunk = ob_get_clean();
                    if (is_string($chunk)) { $legacyOutput = $chunk . $legacyOutput; }
                }
                if (trim($legacyOutput) !== '') {
                    error_log(json_encode([
                        'event' => 'hardness_mcp_ide_smt_legacy_output_discarded',
                        'quote_id' => $quoteId,
                    ]));
                }
            }

            $status = (int) ($order['T005_Flag_Status'] ?? 0);
            return [
                'success' => true,
                'quote_id' => $quoteId,
                'order_id' => $orderId,
                'type' => $type,
                'order_status' => $status,
                'order_status_name' => (string) ($order['T005_Nome_Status'] ?? ''),
                'credit_review' => $status === 6,
                'post_processing_complete' => $postProcessingComplete,
            ];
        }, true);

        if (!is_array($result)
            || array_keys($result) !== ['success', 'quote_id', 'order_id', 'type', 'order_status', 'order_status_name', 'credit_review', 'post_processing_complete']
            || $result['success'] !== true
            || !is_int($result['quote_id']) || $result['quote_id'] !== $quoteId
            || !is_int($result['order_id']) || $result['order_id'] <= 0
            || !is_int($result['type']) || $result['type'] !== $type
            || !is_int($result['order_status'])
            || !is_string($result['order_status_name'])
            || !is_bool($result['credit_review'])
            || !is_bool($result['post_processing_complete'])) {
            throw new LegacyBridgeException('ide_smt_order_result_invalid');
        }
        return $result;
    }

    private function logIdeSmtPostProcessingFailure(\Throwable $exception, int $quoteId, int $orderId): void
    {
        error_log(json_encode([
            'event' => 'hardness_mcp_ide_smt_order_post_processing_failed',
            'quote_id' => $quoteId,
            'order_id' => $orderId,
            'exception_class' => get_class($exception),
            'file' => basename($exception->getFile()),
            'line' => $exception->getLine(),
        ]));
    }

    /** @return array<string,mixed>|null */
    private function ideSmtQuote(\mysqli $connection, int $quoteId): ?array
    {
        $sql = 'SELECT t.T003_Id,t.T003_C004_Id,t.T003_D024_Id,t.T003_Flag_Status_Orcamento,'
            . 't.T003_Flag_Status_Liberacao,t.T003_Flag_Frete,t.T003_Valor_Frete,'
            . 'a.T003A_T003_Id,COALESCE((SELECT ROUND(SUM(i.T004_Valor_Frete_Total),2) '
            . 'FROM T004 i WHERE i.T004_T003_Id=t.T003_Id),0) AS FreteProdutos '
            . 'FROM T003 t LEFT JOIN T003A a ON a.T003A_T003_Id=t.T003_Id '
            . 'WHERE t.T003_Id=' . $quoteId . ' LIMIT 1';
        $result = $connection->query($sql);
        if (!$result instanceof \mysqli_result) {
            throw new LegacyBridgeException('ide_smt_order_read_failed');
        }
        $row = $result->fetch_assoc();
        $result->free();
        return is_array($row) ? $row : null;
    }

    /** @return array<string,mixed>|null */
    private function ideSmtOrder(\mysqli $connection, int $orderId, int $quoteId, int $companyId): ?array
    {
        $result = $connection->query(
            'SELECT T005_Id,T005_Flag_Status,T005_Nome_Status FROM T005 WHERE T005_Id=' . $orderId
            . ' AND T005_T003_Id=' . $quoteId . ' AND T005_C004_Id=' . $companyId . ' LIMIT 1'
        );
        if (!$result instanceof \mysqli_result) {
            throw new LegacyBridgeException('ide_smt_order_read_failed');
        }
        $row = $result->fetch_assoc();
        $result->free();
        return is_array($row) ? $row : null;
    }

    /** @param object $ven001 @param array<string,mixed> $quote */
    private function assertIdeSmtInteractivePreconditions(\mysqli $connection, $ven001, int $quoteId, array $quote): void
    {
        $configuration = $GLOBALS['g']['C031'] ?? [];
        if (($configuration['ratearFreteVendaNoCusto'] ?? null) === 'S'
            && (string) ($quote['T003_Flag_Frete'] ?? '') === '0'
            && abs((float) ($quote['T003_Valor_Frete'] ?? 0) - (float) ($quote['FreteProdutos'] ?? 0)) >= 0.005) {
            throw new LegacyBridgeException('ide_smt_quote_freight_mismatch');
        }
        if (($quote['T003_Flag_Status_Orcamento'] ?? null) === 'L') {
            throw new LegacyBridgeException('ide_smt_quote_in_analysis');
        }

        if (!$ven001->ven001TriggerPersonalizadaPularVerificacaoAlcada($quoteId, false)) {
            $ven001->verificaAlcadaNovo($quoteId, false);
        }
        $refreshed = $this->ideSmtQuote($connection, $quoteId);
        if ($refreshed === null) {
            throw new LegacyBridgeException('ide_smt_quote_not_available');
        }
        $releaseStatus = (int) ($refreshed['T003_Flag_Status_Liberacao'] ?? 0);
        $couponCustomerId = (int) ($GLOBALS['g']['C020']['C020_Cupom_Fiscal_Cliente'] ?? 0);
        if ($couponCustomerId > 0 && (int) ($refreshed['T003_D024_Id'] ?? 0) === $couponCustomerId) {
            $releaseStatus = 1;
        }
        if ($releaseStatus === 4) {
            throw new LegacyBridgeException('ide_smt_quote_approval_required');
        }

        $timeLimit = (string) ($configuration['horarioLimiteGeracaoPedido'] ?? '');
        if ($timeLimit !== '' && $timeLimit !== '00:00') {
            $limit = strtotime($timeLimit);
            $now = strtotime(date('H:i'));
            if ($limit !== false && $now !== false && $now > $limit) {
                throw new LegacyBridgeException('ide_smt_order_time_limit');
            }
        }
    }

    /** @param mixed $legacyResult */
    private function ideSmtFunctionalFailure(string $code, $legacyResult): LegacyBridgeException
    {
        if (!is_string($legacyResult)) {
            return new LegacyBridgeException($code);
        }
        $message = str_ireplace(['<br>', '<br/>', '<br />', '</p>'], "\n", $legacyResult);
        $message = strip_tags(html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $message = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $message) ?? '';
        $message = preg_replace('/[ \t]+/', ' ', $message) ?? '';
        $message = preg_replace('/\s*\n\s*/', "\n", $message) ?? '';
        $message = trim($message);

        // Legacy SQL failures may contain statements, schema details or
        // database diagnostics. They are never suitable for an MCP response.
        if ($message === '' || preg_match('/(?:\b(?:select|insert|update|delete|from|where|join|sql|mysql)\b|(?:^|\s)\d+(?:\.\d+)?\s+erro\s*:|^erro\s*:)/iu', $message) === 1) {
            return new LegacyBridgeException($code);
        }
        if (function_exists('mb_substr')) {
            $message = mb_substr($message, 0, 600, 'UTF-8');
        } else {
            $message = substr($message, 0, 600);
        }
        return new LegacyBridgeException($code, ['functional_message' => $message]);
    }

    /** @return mixed */
    private function executeInternal(LegacyExecutionContext $context, \Closure $operation, bool $writeOperation)
    {
        $this->assertContextRoot($context);
        $state = $this->captureState();
        $connection = null;
        $bufferStarted = false;
        $errorHandlerInstalled = false;
        $previousDisplayErrors = ini_get('display_errors');

        try {
            if (!chdir($this->erpRootPath)) {
                throw new LegacyBridgeException();
            }

            ini_set('display_errors', '0');
            set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
                // The interactive ERP runs with legacy diagnostics hidden.
                // Keep MCP aligned so non-fatal notices, deprecations and
                // warnings do not prevent a quote from being created.
                if (($severity & (E_NOTICE | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT | E_WARNING | E_USER_WARNING)) !== 0) {
                    return true;
                }
                $origin = preg_replace('/[^A-Za-z0-9_-]/', '_', basename($file)) ?: 'unknown';
                error_log(json_encode([
                    'event' => 'hardness_mcp_legacy_runtime_warning',
                    'severity' => $severity,
                    'file' => basename($file),
                    'line' => $line,
                ]));
                throw new LegacyBridgeException(substr('legacy_warning_' . $origin . '_' . $line, 0, 60));
            });
            $errorHandlerInstalled = true;
            ob_start();
            $bufferStarted = true;

            $this->prepareGlobals($context);
            $this->loadLegacyFiles();
            $connection = $this->openConnection($context);
            $GLOBALS['g']['conexaoBanco'] = $connection;
            if ($writeOperation) {
                $this->prepareWriteAuditing($connection);
            }

            $index = new \hardness\INDEX();
            $index->carragaVARSGlobal();
            date_default_timezone_set($context->getTimeZone());

            $result = $operation();
            $output = ob_get_clean();
            $bufferStarted = false;
            if (trim($output) !== '') {
                error_log(json_encode([
                    'event' => 'hardness_mcp_legacy_unexpected_output',
                ]));
                throw new LegacyBridgeException('legacy_unexpected_output');
            }

            return $result;
        } catch (LegacyBridgeException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $quoteItemFailure = $this->quoteItemFailure($exception);
            if ($quoteItemFailure !== null) {
                throw new LegacyBridgeException($quoteItemFailure['code'], $quoteItemFailure['details']);
            }
            $safeCode = $this->legacyFailureCode($exception);
            throw new LegacyBridgeException($safeCode);
        } finally {
            if ($bufferStarted) {
                ob_end_clean();
            }
            if ($errorHandlerInstalled) {
                restore_error_handler();
            }
            ini_set('display_errors', (string) $previousDisplayErrors);
            if ($connection instanceof \mysqli) {
                @$connection->close();
            }
            $this->restoreState($state);
        }
    }

    private function legacyFailureCode(\Throwable $exception): ?string
    {
        $code = $exception->getMessage();
        if (preg_match('/\A(?:customer_not_authorized|customer_not_ready_for_quote|responsible_user_not_seller|quote_header_insert_failed|quote_header_extension_insert_failed|quote_item_unavailable|quote_item_locked|quote_not_pending|quote_item_invalid|quote_item_invalid_value|quote_item_creation_failed|quote_item_invalid_position|quote_item_price_unavailable|quote_item_retryable|quote_item_write_failed|product_not_available_for_company|product_services_not_supported|product_availability_validation_failed|product_service_validation_failed|quote_transaction_begin_failed|quote_transaction_commit_failed|quote_result_invalid|legacy_write_audit_unavailable|legacy_runtime_warning|legacy_warning_[A-Za-z0-9_-]{1,44}|legacy_unexpected_output)\z/', $code) === 1) {
            return $code;
        }
        $file = preg_replace('/[^A-Za-z0-9_-]/', '_', basename($exception->getFile())) ?: 'unknown';
        return substr('legacy_origin_' . $file . '_' . $exception->getLine(), 0, 60);
    }

    /**
     * Converts the fixed legacy facade marker into safe, client-actionable data.
     * The product id was supplied by the client; no ERP diagnostic is exposed.
     *
     * @return array{code:string,details:array{item_index:int,product_id:int}}|null
     */
    private function quoteItemFailure(\Throwable $exception): ?array
    {
        if (preg_match('/\A(quote_item_creation_failed|product_not_available_for_company|product_services_not_supported):(\d{1,3}):(\d{1,20})\z/', $exception->getMessage(), $matches) !== 1) {
            return null;
        }
        $itemIndex = (int) $matches[2];
        $productId = (int) $matches[3];
        if ($itemIndex <= 0 || $productId <= 0) { return null; }
        return ['code' => $matches[1], 'details' => ['item_index' => $itemIndex, 'product_id' => $productId]];
    }

    private function prepareWriteAuditing(\mysqli $connection): void
    {
        // Mirrors the legacy fallback: without a dedicated audit configuration,
        // audit data stays in the resolved tenant and shares its transaction.
        $GLOBALS['g']['conexaoBancoAuditoria'] = $connection;
        // The generic T162 writer changes mysqlLastId before the legacy
        // insertAfter trigger consumes it. Keep it disabled here so T003's
        // trigger receives the T003 identity; its functional D085/T259 task
        // audit continues through the normal trigger path.
        $GLOBALS['g']['tabelasAuditoria'] = [
            'T003' => 'N', 'T003A' => 'N', 'T004' => 'N', 'T004A' => 'N',
            'D085' => 'N', 'T259' => 'N', 'T260' => 'N', 'T250' => 'N',
            'T162' => 'N',
        ];
        if (!$connection->query('SELECT 1 FROM T162 LIMIT 1')) {
            throw new LegacyBridgeException('legacy_write_audit_unavailable');
        }
    }

    /**
     * The only Stage 5 facade entry point. No class, file, or method name is
     * accepted from a Tool or other request data.
     *
     * @return array<string,int|string>
     */
    public function executeMcp001ContextPilot(LegacyExecutionContext $context): array
    {
        $result = $this->execute($context, function (): array {
            require_once $this->erpRootPath . 'bibliotecas/classes/MCP001.php';
            $facade = new \hardness\MCP001();
            return $facade->mcp001ContextPilot();
        });

        if (!is_array($result) || array_keys($result) !== ['implementation', 'company_id', 'user_id', 'profile_id', 'approval_level']) {
            throw new LegacyBridgeException();
        }
        foreach (['company_id', 'user_id', 'profile_id', 'approval_level'] as $field) {
            if (!isset($result[$field]) || !is_int($result[$field]) || $result[$field] <= 0) {
                throw new LegacyBridgeException();
            }
        }
        if (!is_string($result['implementation']) || $result['implementation'] === '') {
            throw new LegacyBridgeException();
        }

        return $result;
    }

    private function assertContextRoot(LegacyExecutionContext $context): void
    {
        $contextRoot = realpath($context->getRootPath());
        if ($contextRoot === false || rtrim($contextRoot, '/') . '/' !== $this->erpRootPath) {
            throw new LegacyBridgeException();
        }
        if (preg_match('/\A[a-zA-Z0-9_-]+\z/', $context->getTenant()->getDatabase()) !== 1) {
            throw new LegacyBridgeException();
        }
        $expectedDataPath = $this->erpRootPath . 'dados_usuarios/' . $context->getTenant()->getDatabase() . '/';
        if ($context->getDataPath() !== $expectedDataPath) {
            throw new LegacyBridgeException();
        }
    }

    /** @return array<string,mixed> */
    private function captureState(): array
    {
        return [
            'cwd' => getcwd(),
            'g_exists' => array_key_exists('g', $GLOBALS),
            'g' => $GLOBALS['g'] ?? null,
            'conf_exists' => array_key_exists('confUsuario', $GLOBALS),
            'conf' => $GLOBALS['confUsuario'] ?? null,
            'session_exists' => array_key_exists('_SESSION', $GLOBALS),
            'session' => $GLOBALS['_SESSION'] ?? null,
            'remote_addr_exists' => array_key_exists('REMOTE_ADDR', $_SERVER),
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
            'timezone' => date_default_timezone_get(),
            't005_id_exists' => array_key_exists('T005_Id', $GLOBALS),
            't005_id' => $GLOBALS['T005_Id'] ?? null,
        ];
    }

    /** @param array<string,mixed> $state */
    private function restoreState(array $state): void
    {
        if ($state['g_exists']) { $GLOBALS['g'] = $state['g']; } else { unset($GLOBALS['g']); }
        if ($state['conf_exists']) { $GLOBALS['confUsuario'] = $state['conf']; } else { unset($GLOBALS['confUsuario']); }
        if ($state['session_exists']) { $GLOBALS['_SESSION'] = $state['session']; } else { unset($GLOBALS['_SESSION']); }
        if ($state['remote_addr_exists']) { $_SERVER['REMOTE_ADDR'] = $state['remote_addr']; } else { unset($_SERVER['REMOTE_ADDR']); }
        if ($state['t005_id_exists']) { $GLOBALS['T005_Id'] = $state['t005_id']; } else { unset($GLOBALS['T005_Id']); }
        if (is_string($state['timezone']) && $state['timezone'] !== '') { date_default_timezone_set($state['timezone']); }
        if (is_string($state['cwd']) && $state['cwd'] !== '') { @chdir($state['cwd']); }
    }

    private function prepareGlobals(LegacyExecutionContext $context): void
    {
        $tenant = $context->getTenant();
        $database = $tenant->getDatabase();
        $dataPath = 'dados_usuarios/' . $database . '/';

        $GLOBALS['confUsuario'] = [
            'dbDatabase' => $database,
            'dbHost' => $tenant->getDatabaseHost(),
            'dbUser' => $tenant->getDatabaseUser(),
            'dbPass' => $tenant->getDatabasePassword(),
            'pathRaiz' => $this->erpRootPath,
            'urlRaiz' => $tenant->getPdfRenderUrl(),
        ];
        $GLOBALS['g'] = [
            'pathRaiz' => $this->erpRootPath,
            'urlRaiz' => $tenant->getPdfRenderUrl(),
            'pathDados' => $dataPath,
            'pathDadosAntigo' => str_replace('hardness3', 'hardness', $this->erpRootPath) . $dataPath . '/',
            'empresaAtual' => $context->getCompanyId(),
            'usuarioAtual' => $context->getUserId(),
            'perfilUsuario' => $context->getProfileId(),
            'modoDesenvolvedor' => false,
            'modoDesenvolvedorApp' => false,
            // D085's subject callback renders historical task UI. It is not
            // part of quote persistence and must not run in MCP's headless
            // request context.
            'mcpNonInteractive' => true,
            // Configuration helpers expect the current web route key even
            // when the operation is non-interactive.
            'r_' => '',
            'nivelAlcada' => $context->getApprovalLevel(),
            'nivelAlcadaUsuarioSistema' => $context->getApprovalLevel(),
            // INDEX reads these cache slots before it repopulates them.
            'c020' => ['C020_Id' => 0],
            'c004' => ['C004_Id' => 0],
            'c007' => ['C007_Id' => 0],
            'c021' => ['C021_Id' => 0],
            'time' => ['start' => microtime(true), 'sql' => 0.0],
            'tabelasCache' => [],
            'tabelasAuditoria' => [],
            'mysqlLastId' => 0,
            'mysqlAffectedRows' => 0,
            // mysql_error() in the legacy mysqli compatibility layer reads
            // this key without an existence check. Web bootstrap creates it;
            // MCP must provide the same neutral initial state.
            'conexaoLastQuery' => false,
        ];
        $GLOBALS['_SESSION'] = ['nivelAlcadaUsuarioSistema' => $context->getApprovalLevel()];
        $_SERVER['REMOTE_ADDR'] = $context->getRemoteIp();
        date_default_timezone_set($context->getTimeZone());
    }

    private function loadLegacyFiles(): void
    {
        require_once $this->erpRootPath . 'mcp/src/Legacy/legacy_session_shim.php';
        require_once $this->erpRootPath . 'sistema/confSistema.php';
        require_once $this->erpRootPath . 'sistema/apps.php';
        require_once $this->erpRootPath . 'bibliotecas/funcoesGerais.php';
        require_once $this->erpRootPath . 'bibliotecas/restricoes.php';
        require_once $this->erpRootPath . 'bibliotecas/classes/INDEX.php';
        $GLOBALS['g']['conf'] = new \hardness\confSistema();
    }

    private function openConnection(LegacyExecutionContext $context): \mysqli
    {
        $tenant = $context->getTenant();
        if (!function_exists('mysqli_init')) {
            throw new LegacyBridgeException();
        }
        mysqli_report(MYSQLI_REPORT_OFF);
        $connection = mysqli_init();
        if (!$connection instanceof \mysqli) {
            throw new LegacyBridgeException();
        }
        if (!@$connection->real_connect(
            $tenant->getDatabaseHost(),
            $tenant->getDatabaseUser(),
            $tenant->getDatabasePassword()
        )) {
            @$connection->close();
            throw new LegacyBridgeException();
        }
        if (!$connection->set_charset('utf8') || !$connection->query('USE `' . $tenant->getDatabase() . '`')) {
            @$connection->close();
            throw new LegacyBridgeException();
        }
        return $connection;
    }
}
