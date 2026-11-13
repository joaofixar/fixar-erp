<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner\Search;

use HardnessMcp\Tools\ToolDefinition;

final class PartnerSearchDefinition
{
    public function toToolDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.partner.search', 'Searches active customers or suppliers accessible to the connection by legal name, trading name, or a known CPF/CNPJ. Each returned candidate_ref must be preserved and supplied to hardness.partner.get to consult that selected partner. Name searches can return multiple candidates; do not choose a candidate automatically.', [
            'type' => 'object',
            'properties' => [
                'criterion' => ['type' => 'string', 'enum' => ['name', 'document']],
                'query' => ['type' => 'string', 'minLength' => 3, 'maxLength' => 100],
                'role' => ['type' => 'string', 'enum' => ['both', 'customer', 'supplier']],
            ],
            'required' => ['criterion', 'query'],
            'additionalProperties' => false,
        ], ['partner:search']);
    }
}
