<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Create;

use HardnessMcp\Context\ExecutionContext;

final class UnavailableQuoteCreateGateway implements QuoteCreateGatewayInterface
{
    public function create(array $arguments, ExecutionContext $context): array
    {
        throw new \RuntimeException('legacy_quote_create_unavailable');
    }
}
