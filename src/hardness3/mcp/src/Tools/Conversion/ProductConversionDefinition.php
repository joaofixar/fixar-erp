<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Conversion;

use HardnessMcp\Tools\ToolDefinition;

final class ProductConversionDefinition
{
    public function toToolDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.product.conversion_summary', 'Returns the product conversion funnel from quote item to order item and then to authorised outgoing NF-e item. The period and optional customer or internal-seller filters apply to the quote issue date; orders and invoices linked to those quote items are counted even when issued later. Each product row contains quoted, ordered, invoiced and pending quantities; conversion percentages; and invoiced net revenue, cost, gross profit and margin. One quote item can be split into multiple order or NF-e items without double-counting the quoted quantity. The maximum quote period is 12 months and the ranking returns at most 50 products. It never returns individual documents.', ['type' => 'object', 'properties' => ['date_from' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10], 'date_to' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10], 'customer_id' => ['type' => 'integer', 'minimum' => 1], 'seller_internal_id' => ['type' => 'integer', 'minimum' => 1], 'product_code' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 30], 'group_by' => ['type' => 'string', 'enum' => ['product', 'product_customer']], 'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50]], 'required' => ['date_from', 'date_to'], 'additionalProperties' => false], ['product:conversion-report:read']);
    }
}
