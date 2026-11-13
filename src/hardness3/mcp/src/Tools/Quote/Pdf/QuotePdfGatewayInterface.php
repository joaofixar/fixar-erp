<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Pdf;

use HardnessMcp\Context\ExecutionContext;

interface QuotePdfGatewayInterface
{
    /** @return array{pdf_url:string} */
    public function generate(int $quoteId, ExecutionContext $context): array;
}
