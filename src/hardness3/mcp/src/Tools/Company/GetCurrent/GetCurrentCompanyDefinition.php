<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Company\GetCurrent;

use HardnessMcp\Tools\ToolDefinition;

final class GetCurrentCompanyDefinition
{
    public function toToolDefinition(): ToolDefinition
    {
        return new ToolDefinition(
            'hardness.company.get_current',
            'Returns the basic profile of the company bound to this MCP connection.',
            ['type' => 'object', 'properties' => new \stdClass(), 'additionalProperties' => false],
            ['company:read']
        );
    }
}
