<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\Search;

use HardnessMcp\Tools\ToolDefinition;

final class ProductSearchDefinition
{
    public function toToolDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.product.search', 'Searches active products linked to the company bound to this MCP connection. The id returned for each product is its internal identifier and must be used as product_id when creating a quote; code is only the commercial product code.', [
            'type' => 'object',
            'properties' => ['query' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 100], 'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50]],
            'required' => ['query'],
            'additionalProperties' => false,
        ], ['product:read']);
    }
}
