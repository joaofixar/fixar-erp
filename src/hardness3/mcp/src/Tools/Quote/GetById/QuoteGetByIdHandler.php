<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\GetById;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Quote\Domain\QuoteRepositoryInterface;
use HardnessMcp\Tools\Quote\QuoteOutput;

final class QuoteGetByIdHandler
{
    private QuoteRepositoryInterface $repository; public function __construct(QuoteRepositoryInterface $repository) { $this->repository = $repository; }
    /** @return array<string,mixed>|null */
    public function handle(QuoteGetByIdInput $input, ExecutionContext $context): ?array
    {
        $quote = $this->repository->findById($context->getCompanyId(), $input->getId());
        if ($quote === null) { return null; }
        $result = QuoteOutput::header($quote['header']); $items = [];
        foreach ($quote['items'] as $item) { $items[] = QuoteOutput::item($item); }
        $result['items'] = $items;
        return $result;
    }
}
