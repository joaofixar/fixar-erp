<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\ItemReport;

use HardnessMcp\Tools\ToolDefinition;

final class ItemReportDefinition
{
    private string $source; private string $scope;
    public function __construct(string $source, string $scope) { $this->source = $source; $this->scope = $scope; }

    public function toToolDefinition(): ToolDefinition
    {
        $label = ['quote' => 'quoted', 'order' => 'ordered', 'sales' => 'invoiced'][$this->source];
        return new ToolDefinition(
            'hardness.' . $this->source . '.item_summary',
            'Returns a product ranking from ' . $label . ' document items. Each row includes product code, description, unit, quantity, net revenue, weighted average net unit price, cost, gross profit, gross margin, and document count. Use it for top products in a month or up to a year. The period is based on the document issue date. The maximum period is 12 months and returns at most 50 products. It does not return individual documents. The default ranking has 10 products; when the caller requests up to 20, all requested rows are included in the tool text.',
            ['type' => 'object', 'properties' => [
                'date_from' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10],
                'date_to' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10],
                'customer_id' => ['type' => 'integer', 'minimum' => 1],
                'seller_internal_id' => ['type' => 'integer', 'minimum' => 1],
                'product_code' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 30],
                'group_by' => ['type' => 'string', 'enum' => ['product', 'product_customer']],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50],
            ], 'required' => ['date_from', 'date_to'], 'additionalProperties' => false],
            [$this->scope]
        );
    }
}
