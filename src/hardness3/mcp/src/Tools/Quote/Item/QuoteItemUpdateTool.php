<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Item;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;

final class QuoteItemUpdateTool implements ToolInterface
{
    private QuoteItemGatewayInterface $gateway;
    private ?ToolResultPresenter $presenter;

    public function __construct(QuoteItemGatewayInterface $gateway, ?ToolResultPresenter $presenter = null)
    {
        $this->gateway = $gateway;
        $this->presenter = $presenter ?? new ToolResultPresenter();
    }

    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.quote_item.update', 'Updates exactly one controlled field of one pending quote item. For quantity, discount_percent, unit_price, or item_order, send the numeric value property. For location_code (ERP Ambiente), send the location_code string property instead; an empty string explicitly removes the Ambiente. Never ask for Ambiente merely because it is absent, and never invent one. A user may request either one specific item, or a collective update within exactly one explicitly chosen quote: in the collective case, the request must state one target quote, the field, the value or rule, and whether every item or a specific subset of that quote is affected; the client may then make one unitary call per item, only for items of that same quote. A collective update intent may cover at most one T003. If the current intent involves multiple quotes, do not execute any update from that intent, do not process quotes sequentially, and do not convert a general authorization into per-quote processing; require the user to choose one quote and then make a new request limited to that one quote before any update. Never choose quotes or items arbitrarily, invent a value, choose a field on behalf of the user, or extend the update beyond the explicitly chosen quote. An unambiguous collective instruction for items of one explicitly chosen quote already authorizes the required unitary calls; do not require item-by-item confirmation. Discounts may be negative (acréscimo) or zero (remove desconto).', [
            'type' => 'object',
            'properties' => [
                't004_id' => ['type' => 'integer', 'minimum' => 1],
                'field' => ['type' => 'string', 'enum' => ['quantity', 'discount_percent', 'unit_price', 'item_order', 'location_code']],
                'value' => ['type' => 'number'],
                'location_code' => ['type' => 'string', 'maxLength' => 60],
            ],
            // The local JSON-schema subset has no conditional requirements.
            // execute() requires value or location_code according to field.
            'required' => ['t004_id', 'field'],
            'additionalProperties' => false,
        ], ['quote:item:update'], true);
    }

    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        $field = (string) ($arguments['field'] ?? '');
        if ($field === 'location_code') {
            if (!array_key_exists('location_code', $arguments) || array_key_exists('value', $arguments) || !is_string($arguments['location_code'])) {
                return $this->invalidValue();
            }
            $locationCode = trim($arguments['location_code']);
            $length = function_exists('mb_strlen') ? mb_strlen($locationCode, 'UTF-8') : strlen($locationCode);
            if ($length > 60 || preg_match('/[[:cntrl:]]/u', $locationCode)) {
                return $this->invalidValue();
            }
        } elseif (array_key_exists('location_code', $arguments)
            || !array_key_exists('value', $arguments)
            || !is_numeric($arguments['value'])
            || ($field !== 'discount_percent' && (float) $arguments['value'] <= 0)) {
            return $this->invalidValue();
        }

        try {
            $result = $this->gateway->update($arguments, $context);
            return $this->presenter->record($result, 'Item de orçamento alterado', [
                ['path' => 't004_id', 'label' => 'Item'],
                ['path' => 'field', 'label' => 'Campo'],
                ['path' => 'value', 'label' => 'Valor'],
            ], 'tools_call_quote_item_update');
        } catch (\Throwable $exception) {
            return $this->fail($exception);
        }
    }

    private function invalidValue(): ToolExecutionResult
    {
        return $this->presenter->functionalError('quote_item_invalid_value', 'Não foi possível alterar o item do orçamento.', 'tools_call_quote_item_update_failed', [], 'quote_item_invalid_value');
    }

    private function fail(\Throwable $exception): ToolExecutionResult
    {
        $code = $exception->getMessage();
        $map = [
            'quote_item_unavailable' => 'item_not_available',
            'quote_item_locked' => 'quote_item_locked',
            'quote_not_pending' => 'quote_not_pending',
            'quote_item_invalid_value' => 'quote_item_invalid_value',
            'quote_item_invalid_position' => 'quote_item_invalid_position',
            'quote_item_price_unavailable' => 'quote_item_price_unavailable',
            'quote_item_retryable' => 'quote_item_retryable',
        ];
        $safeCode = $map[$code] ?? 'quote_item_update_failed';
        $message = $safeCode === 'item_not_available'
            ? 'O item não está disponível neste contexto.'
            : ($safeCode === 'quote_item_retryable' ? 'O item foi alterado por outra operação. Tente novamente.' : 'Não foi possível alterar o item do orçamento.');
        return $this->presenter->functionalError($safeCode, $message, 'tools_call_quote_item_update_failed', [], $code);
    }
}
