<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Create;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Legacy\LegacyBridgeException;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use Throwable;

final class QuoteCreateTool implements ToolInterface
{
    private QuoteCreateGatewayInterface $gateway;
    private ToolResultPresenter $presenter;

    public function __construct(QuoteCreateGatewayInterface $gateway, ?ToolResultPresenter $presenter = null)
    {
        $this->gateway = $gateway;
        $this->presenter = $presenter ?? new ToolResultPresenter();
    }

    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.quote.create', 'Creates a basic draft quote for an authorized customer and products. Each item product_id must be the internal product id returned as id by hardness.product.search; never use its product code. An item may include location_code when the user explicitly supplies the ERP Ambiente for that item. location_code is optional: never ask for it merely because it was omitted.', [
            'type' => 'object',
            'properties' => [
                'customer_id' => ['type' => 'integer', 'minimum' => 1],
                'items' => ['type' => 'array', 'minItems' => 1, 'maxItems' => 100, 'items' => [
                    'type' => 'object',
                    'properties' => [
                        'product_id' => ['type' => 'integer', 'minimum' => 1],
                        'quantity' => ['type' => 'number', 'exclusiveMinimum' => 0],
                        'location_code' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 60],
                    ],
                    'required' => ['product_id', 'quantity'],
                    'additionalProperties' => false,
                ]],
            ],
            'required' => ['customer_id', 'items'],
            'additionalProperties' => false,
        ], ['quote:write'], true);
    }

    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        try {
            $result = $this->gateway->create($arguments, $context);
        } catch (Throwable $exception) {
            $auditCode = $this->auditFailureCode($exception);
            if ($auditCode === null) {
                error_log(json_encode([
                    'event' => 'hardness_mcp_quote_create_unclassified_failure',
                    'exception_class' => get_class($exception),
                    'file' => basename($exception->getFile()),
                    'line' => $exception->getLine(),
                ]));
                $file = preg_replace('/[^A-Za-z0-9_-]/', '_', basename($exception->getFile())) ?: 'unknown';
                $auditCode = substr('quote_create_' . $file . '_' . $exception->getLine(), 0, 60);
            }
            $details = $exception instanceof LegacyBridgeException ? $exception->getPublicDetails() : [];
            $itemDetails = $this->quoteItemDetails($details);
            $message = $this->failureMessage($auditCode, $itemDetails);
            return $this->presenter->functionalError('quote_creation_failed', $message, 'tools_call_quote_create_failed', $itemDetails === [] ? [] : ['failed_item' => $itemDetails], $auditCode);
        }
        $safe = [
            'success' => $result['success'] ?? false,
            'quote_id' => (int) ($result['quote_id'] ?? 0),
            'items_count' => (int) ($result['items_count'] ?? 0),
            'customer_id' => (int) ($arguments['customer_id'] ?? 0),
            'status' => 'draft',
            'items' => $this->acceptedItems($arguments['items'] ?? []),
        ];
        if ($safe['success'] !== true || $safe['quote_id'] <= 0 || $safe['items_count'] <= 0 || $safe['items_count'] !== count($safe['items'])) {
            return $this->presenter->functionalError('quote_creation_failed', 'O orçamento não pôde ser criado.', 'tools_call_quote_create_failed');
        }
        return $this->presenter->record($safe, 'Orçamento criado', [
            ['path' => 'quote_id', 'label' => 'Orçamento'],
            ['path' => 'customer_id', 'label' => 'Cliente'],
            ['path' => 'status', 'label' => 'Status'],
            ['path' => 'items_count', 'label' => 'Itens'],
        ]);
    }

    private function auditFailureCode(Throwable $exception): ?string
    {
        $code = $exception->getMessage();
        return preg_match('/\A(?:customer_not_authorized|customer_not_ready_for_quote|responsible_user_not_seller|quote_header_insert_failed|quote_header_extension_insert_failed|quote_item_invalid|quote_item_creation_failed|product_not_available_for_company|product_services_not_supported|product_availability_validation_failed|product_service_validation_failed|quote_transaction_begin_failed|quote_transaction_commit_failed|quote_result_invalid|legacy_write_audit_unavailable|legacy_runtime_warning|legacy_warning_[A-Za-z0-9_-]{1,44}|legacy_unexpected_output|legacy_origin_[A-Za-z0-9_-]{1,44})\z/', $code) === 1
            ? $code
            : null;
    }

    /** @param mixed $items @return array<int,array{index:int,product_id:int,quantity:float|int,location_code?:string}> */
    private function acceptedItems($items): array
    {
        if (!is_array($items)) { return []; }
        $accepted = [];
        foreach ($items as $index => $item) {
            if (!is_array($item)) { return []; }
            $acceptedItem = ['index' => $index + 1, 'product_id' => (int) ($item['product_id'] ?? 0), 'quantity' => $item['quantity'] ?? 0];
            if (array_key_exists('location_code', $item)) { $acceptedItem['location_code'] = trim((string) $item['location_code']); }
            $accepted[] = $acceptedItem;
        }
        return $accepted;
    }

    /** @param array<string,int> $details @return array{index:int,product_id:int} */
    private function quoteItemDetails(array $details): array
    {
        $index = (int) ($details['item_index'] ?? 0);
        $productId = (int) ($details['product_id'] ?? 0);
        return $index > 0 && $productId > 0 ? ['index' => $index, 'product_id' => $productId] : [];
    }

    /** @param array{index:int,product_id:int} $itemDetails */
    private function failureMessage(?string $auditCode, array $itemDetails): string
    {
        $item = $itemDetails === [] ? '' : ' o item ' . $itemDetails['index'] . ' (produto ' . $itemDetails['product_id'] . ')';
        if ($auditCode === 'product_services_not_supported') {
            return 'O orçamento não pôde ser criado porque' . ($item === '' ? ' um produto' : $item) . ' possui serviços associados, configuração ainda não suportada via MCP.';
        }
        if ($auditCode === 'product_not_available_for_company' && $item !== '') {
            return 'O orçamento não pôde ser criado porque' . $item . ' não está disponível para venda na empresa vinculada à conexão. Confirme que product_id é o ID interno retornado no campo id da busca de produtos, e não o código do produto.';
        }
        if ($auditCode === 'quote_item_creation_failed' && $item !== '') {
            return 'O orçamento não pôde ser criado porque não foi possível incluir' . $item . '. Verifique se o produto está disponível para a empresa e possui a configuração comercial e fiscal necessária.';
        }
        return 'O orçamento não pôde ser criado.';
    }
}
