<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\GetById;

use HardnessMcp\Tools\ToolDefinition;

final class QuoteGetByIdDefinition
{
    public function toToolDefinition(): ToolDefinition { return new ToolDefinition('hardness.quote.get_by_id', 'Returns a quote and its items when it belongs to the company bound to this MCP connection.', ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'minimum' => 1]], 'required' => ['id'], 'additionalProperties' => false], ['quote:read']); }
}
