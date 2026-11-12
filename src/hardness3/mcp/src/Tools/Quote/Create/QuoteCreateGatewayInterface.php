<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Create;

use HardnessMcp\Context\ExecutionContext;

interface QuoteCreateGatewayInterface
{
    /** @param array<string,mixed> $arguments @return array{success:bool,quote_id:int,items_count:int} */
    public function create(array $arguments, ExecutionContext $context): array;
}
