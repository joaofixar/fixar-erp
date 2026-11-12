<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Search;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Quote\Domain\QuoteRepositoryInterface;
use HardnessMcp\Tools\Quote\QuoteOutput;

final class QuoteSearchHandler
{
    private QuoteRepositoryInterface $repository; public function __construct(QuoteRepositoryInterface $repository) { $this->repository = $repository; }
    /** @return array<string,mixed> */
    public function handle(QuoteSearchInput $input, ExecutionContext $context): array
    {
        $quotes = $this->repository->search($context->getCompanyId(), $input->getIssuedFrom(), $input->getIssuedTo(), $input->getCustomerId(), $input->getOrigin(), $input->getLimit() + 1);
        return QuoteOutput::search(array_slice($quotes, 0, $input->getLimit()), count($quotes) > $input->getLimit());
    }
}
