<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Commercial;

use HardnessMcp\Tools\ToolDefinition;

final class OpportunityDefinition
{
    public function toToolDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.commercial.opportunities', 'Returns actionable commercial opportunities from the company-bound ERP: quotes with value not yet converted to order items and orders with value not yet invoiced in authorised outgoing NF-e items. The period applies to the quote or order issue date; minimum_age_days defaults to 7. Rows contain customer, seller, document, age, original amount, converted or invoiced amount, estimated pending amount and a transparent priority calculated from age and pending amount. It can filter by customer, seller or product code. It does not infer last contact, loss reason or repurchase because those are not consistently recorded in these documents.', ['type' => 'object', 'properties' => ['date_from' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10], 'date_to' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10], 'opportunity_type' => ['type' => 'string', 'enum' => ['all', 'quote_pending_order', 'order_pending_invoice']], 'minimum_age_days' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 365], 'minimum_pending_value' => ['type' => 'number', 'minimum' => 0], 'customer_id' => ['type' => 'integer', 'minimum' => 1], 'seller_internal_id' => ['type' => 'integer', 'minimum' => 1], 'product_code' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 30], 'cursor' => ['type' => 'string', 'minLength' => 8, 'maxLength' => 512], 'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50]], 'required' => ['date_from', 'date_to'], 'additionalProperties' => false], ['commercial:opportunities:read']);
    }
}
