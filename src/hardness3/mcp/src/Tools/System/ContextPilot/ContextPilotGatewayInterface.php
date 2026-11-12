<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\System\ContextPilot;

use HardnessMcp\Context\ExecutionContext;

interface ContextPilotGatewayInterface
{
    /** @return array<string,int|string> */
    public function execute(ExecutionContext $executionContext): array;
}
