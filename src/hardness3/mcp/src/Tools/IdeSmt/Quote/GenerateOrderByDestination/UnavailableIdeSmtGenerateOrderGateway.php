<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Legacy\LegacyBridgeException;

final class UnavailableIdeSmtGenerateOrderGateway implements IdeSmtGenerateOrderGatewayInterface
{
    public function generate(int $quoteId, string $destination, ExecutionContext $context): array
    {
        throw new LegacyBridgeException('ide_smt_order_unavailable');
    }
}
