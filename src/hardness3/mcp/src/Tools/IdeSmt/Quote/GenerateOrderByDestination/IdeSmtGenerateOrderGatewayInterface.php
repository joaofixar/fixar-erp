<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination;

use HardnessMcp\Context\ExecutionContext;

interface IdeSmtGenerateOrderGatewayInterface
{
    /** @return array<string,mixed> */
    public function generate(int $quoteId, string $destination, ExecutionContext $context): array;
}
