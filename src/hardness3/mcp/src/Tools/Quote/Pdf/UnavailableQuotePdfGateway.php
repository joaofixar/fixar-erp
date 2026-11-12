<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Pdf;

use HardnessMcp\Context\ExecutionContext;
use RuntimeException;

final class UnavailableQuotePdfGateway implements QuotePdfGatewayInterface
{
    public function generate(int $quoteId, ExecutionContext $context): array
    {
        throw new RuntimeException('quote_pdf_runtime_unavailable');
    }
}
