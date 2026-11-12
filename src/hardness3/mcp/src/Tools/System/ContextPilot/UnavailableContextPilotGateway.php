<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\System\ContextPilot;

use HardnessMcp\Context\ExecutionContext;
use RuntimeException;

/** Used only by non-request composition checks that have no resolved tenant. */
final class UnavailableContextPilotGateway implements ContextPilotGatewayInterface
{
    /** @return array<string,int|string> */
    public function execute(ExecutionContext $executionContext): array
    {
        throw new RuntimeException('Legacy context is unavailable.');
    }
}
