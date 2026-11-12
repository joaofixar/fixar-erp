<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Search;

use HardnessMcp\Tools\ToolDefinition;

final class QuoteSearchDefinition
{
    public function toToolDefinition(): ToolDefinition { return new ToolDefinition('hardness.quote.search', 'Searches quote summaries for the company bound to this MCP connection. Set origin to assistant_ia to return only quotes created by the Assistant IA through MCP.', ['type' => 'object', 'properties' => ['issued_from' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10], 'issued_to' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10], 'customer_id' => ['type' => 'integer', 'minimum' => 1], 'origin' => ['type' => 'string', 'enum' => ['assistant_ia']], 'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50]], 'required' => ['issued_from', 'issued_to'], 'additionalProperties' => false], ['quote:read']); }
}
