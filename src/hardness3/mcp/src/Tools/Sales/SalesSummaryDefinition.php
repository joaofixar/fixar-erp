<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Sales;

use HardnessMcp\Tools\ToolDefinition;

final class SalesSummaryDefinition
{
    public function toToolDefinition(): ToolDefinition
    {
        $groupBy = ['day', 'week', 'month', 'customer', 'seller_internal', 'document'];
        return new ToolDefinition(
            'hardness.sales.summary',
            'Returns invoiced-sales reports. Use document to list individual outgoing, non-cancelled NF-e documents with invoice number, internal ID, issue date, customer, customer group, proposal customer when available, and total; other groupings return totals and rankings. It uses invoices with an NF-e protocol belonging to the company bound to this MCP connection. Historical summaries grouped by month, customer, or internal seller accept up to 5 years. Weekly grouping and document listings accept up to 12 months; daily grouping accepts up to 90 days. Rankings and document listings return at most 50 rows. It never returns product analysis. Never split a period longer than these limits into sequential calls: explain the applicable interactive limit and request one allowed period instead.',
            [
                'type' => 'object',
                'properties' => [
                    'date_from' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10],
                    'date_to' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10],
                    'group_by' => ['type' => 'string', 'enum' => $groupBy],
                    'customer_id' => ['type' => 'integer', 'minimum' => 1],
                    'seller_internal_id' => ['type' => 'integer', 'minimum' => 1],
                    'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50],
                ],
                'required' => ['date_from', 'date_to', 'group_by'],
                'additionalProperties' => false,
            ],
            ['sales:read']
        );
    }
}
